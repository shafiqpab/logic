<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_name", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/aop_production_status_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/aop_production_status_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value+'_'+0, 'load_drop_down_machine', 'machine_td' );" ); 
}

if ($action=="load_drop_down_floor")
{
    $ex_data=explode('_',$data);
    echo create_drop_down( "cbo_floor_name", 100, "select id,floor_name from lib_prod_floor where company_id=$ex_data[0] and location_id=$ex_data[1] and status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/aop_production_status_report_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+this.value, 'load_drop_down_machine', 'machine_td' );" );
}

if ($action=="load_drop_down_machine")
{
    $data= explode("_", $data);

    if($data[1]==0 || $data[2]==0)
    {
        echo create_drop_down( "cbo_machine_id", 100, $blank_array,"", 1, "-- Select Machine --", $selected, "" );
    }
    else
    {
        if($db_type==2)
        {
            echo create_drop_down( "cbo_machine_id", 100, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where company_id='$data[0]' and location_id='$data[1]' and floor_id='$data[2]' and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );
        }
        else if($db_type==0)
        {
            echo create_drop_down( "cbo_machine_id", 100, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where company_id='$data[0]' and location_id='$data[1]' and floor_id='$data[2]' and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
        }
    }   
}

                    
if ($action=="load_drop_down_buyer_buyer")
{
    $data=explode("_",$data);
    if($data[1]==1)
    {
        echo create_drop_down( "txt_buyer_buyer_no", 125, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select Buyer --", "", "");
    }
    else
    {
       echo '<input name="txt_buyer_buyer_no" id="txt_buyer_buyer_no" class="text_boxes" style="width:115px"  placeholder="Write">';
    }   
    exit();  
} 
                    
                    

if ($action=="load_drop_down_buyer")
{
    $data=explode("_",$data);

   /* if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
    else $load_function="";*/
    if($data[1]==1)
    {
        echo create_drop_down( "cbo_party_name", 125, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "$load_function");
    }
    else
    {
        echo create_drop_down( "cbo_party_name", 125, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
    }   
    exit();  
} 

if($action=="job_no_popup")
{
    echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(id)
        {
            document.getElementById('selected_id').value=id;
            parent.emailwindow.hide();
        }
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
    <?
    $year_job = str_replace("'","",$year);

    if($db_type==0) 
    {
        $year_field="year(a.insert_date) as year"; 
         if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year_job";
    }
    else if($db_type==2) 
    {
        $year_field="to_char(a.insert_date,'YYYY') as year";
         if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year_job";
    }
    else 
    {
        $year_field="";
        $year_field_cond="";
    }
    
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
    
    $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num,  b.order_no, b.cust_style_ref from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.entry_form=278  and a.company_id='$company_id' $buyer_cond group by a.party_id, a.subcon_job, a.job_no_prefix_num,  b.order_no, b.cust_style_ref  order by a.subcon_job ASC";   
    
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="70">Year</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql);
        $i=1;
         foreach($data_array as $row)
         {
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('job_no_prefix_num')]; ?>')" style="cursor:pointer;">
                <td width="30"><? echo $i; ?></td>
                <td align="center"  width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td align="center" width="70"><? echo $row[csf('year')]; ?></td>
                <td width="130"><? echo $buyer[$row[csf('party_id')]]; ?></td>
                <td width="110"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
                <td width="120"><p><? echo $row[csf('order_no')]; ?></p></td>
            </tr>
            <? $i++; 
            } 
        ?>
    </table>
    </div>
    <script> setFilterGrid("table_body2",-1); </script>
    <?
    disconnect($con);
    exit();
}
if($action=="order_desc_popup")
{
    echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="11">Order Qty Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Order No</th>
                        <th width="300">Item Description </th>
                        <th width="120">Color</th>
                        <th width="80">Receive Date</th>
                        <th width="50">Rate</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                $item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');   
                $size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                $sql = "SELECT a.id,a.job_no_prefix_num, a.subcon_job, a.party_id, b.order_no, b.order_quantity,b.amount,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,b.construction, b.composition, 
                b.gsm, b.grey_dia, b.gmts_color_id, 
                b.item_color_id, b.fin_dia, b.aop_color_id,a.receive_date,b.rate
                from subcon_ord_mst a, subcon_ord_dtls b
                where a.subcon_job=b.job_no_mst and b.id=$expData[0]   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278   order by  a.job_no_prefix_num, b.order_no";
               // echo $sql;
                $order_dtls_sql= sql_select($sql);
                foreach( $order_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="120"><? echo $row[csf("order_no")];?> </td>
                        <td width="300"><? echo $row[csf('composition')].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")]; ?> </td> 
                        <td align="center" width="120"><p><? echo  $color_arr[$row[csf("gmts_color_id")]]; ?></p></td>
                        <td align="center" width="80"><? echo change_date_format($row[csf("receive_date")]); ?></td>
                        <td align="right" width="50"><? echo $row[csf("rate")]; ?> &nbsp; </td>
                        <td align="right"><? echo number_format($row[csf("order_quantity")]); ?> &nbsp;</td>
                    </tr>
                    <? 
                    $tot_qty+=$row[csf("order_quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Total: &nbsp;</td>
                    <td align="right"><p><? echo number_format($tot_qty); ?> &nbsp; </p></td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}
if($action=="order_no_popup")
{
    echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    <script type="text/javascript">
        function js_set_value(id)
        { 
            document.getElementById('selected_id').value=id;
            parent.emailwindow.hide();
        }
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
    <?
    $buyer = str_replace("'","",$buyer_name);
    $year = str_replace("'","",$year);
    $buyer = str_replace("'","",$buyer_name);
    $job_no = str_replace("'","",$job_no);

    if($db_type==0) 
    {
        $year_field="year(a.insert_date) as year"; 
         if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
    }
    else if($db_type==2) 
    {
        $year_field="to_char(a.insert_date,'YYYY') as year";
         if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
    }
    else 
    {
        $year_field="";
        $year_field_cond="";
    }
    
    if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num=$job_no";
    //echo $buyer;die;
    
    if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
    
     $sql="select distinct a.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b where b.job_no_mst=a.subcon_job and a.id=b.mst_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond and a.entry_form=278  and a.is_deleted =0 group by a.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";  
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Order Number</th>
                <th width="50">Job no</th>
                <th width="80">Buyer</th>
                <th width="40">Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
            <? 
            $rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('po_number')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
                    <td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
                </tr>
                <? $i++; 
            } ?>
        </table>
    </div>
    <script> setFilterGrid("table_body2",-1); </script>
    <?
    exit();
}
if($action=="material_desc_popup")
{
    echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="7">Receive Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Receive ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Rec. Date</th>
                        <th width="110">Order No</th>
                        <th width="300">Item Description</th>
                        <th>Receive Qty</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                //$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                $sql= "select a.sys_no,a.prefix_no_num,a.chalan_no,a.subcon_date,b.job_dtls_id, b.quantity,c.order_no,c.construction, c.composition, 
                c.gsm, c.grey_dia, c.gmts_color_id, c.item_color_id, c.fin_dia, c.aop_color_id from  sub_material_mst a, sub_material_dtls b, subcon_ord_dtls c where a.id=b.mst_id and b.job_dtls_id=c.id and a.trans_type=1 and a.entry_form=279 and b.job_dtls_id=$expData[0] and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    $material_name= $row[csf('composition')].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
                    ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td align="center" width="110"><? echo $row[csf("order_no")]; ?></td>
                    <td width="300"><? echo$material_name; ?> </td> 
                    <td align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                </tr>
            </table>
        </div> 
    </fieldset>
   
 </div> 
    <?
    exit();
}

if($action=="material_desc_iss_popup")
{
    echo load_html_head_contents("Material Issue Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="7">Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Issue ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Rec. Date</th>
                        <th width="110">Order No</th>
                        <th width="300">Item Description</th>
                        <th>Issue Qty</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                $sql= "select a.sys_no,a.prefix_no_num,a.chalan_no,a.subcon_date,b.job_dtls_id, b.quantity,c.order_no,c.construction, c.composition, c.gsm, c.grey_dia, c.gmts_color_id, c.item_color_id, c.fin_dia, c.aop_color_id from  sub_material_mst a, sub_material_dtls b, subcon_ord_dtls c where a.id=b.mst_id and b.job_dtls_id=c.id and a.trans_type=2 and a.entry_form=280 and b.job_dtls_id=$expData[0] and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                
                //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    $material_name= $row[csf('composition')].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
                    ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td align="center" width="110"><? echo $row[csf("order_no")]; ?></td>
                    <td width="300"><? echo$material_name; ?> </td> 
                    <td align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}

if($action=="product_qty_pop_up")
{
    echo load_html_head_contents("Production Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="7">Production Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Sys ID</th>
                        <th width="70">Prod. Date</th>
                        <th width="110">Order No</th>
                        <th width="70">Batch</th>
                        <th width="300">Item Description</th>
                        <th>Qty</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                $sql= "select a.product_no,a.prefix_no_num,a.product_date,b.batch_id, b.fabric_description,b.product_qnty,b.order_id,c.batch_no from  subcon_production_mst a, subcon_production_dtls b ,pro_batch_create_mst c where a.id=b.mst_id and a.entry_form=291 and b.batch_id=c.id and b.order_id='$expData[0]' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("product_date")]);?> </td> 
                    <td align="center" width="110"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="70"><? echo $row[csf("batch_no")]; ?></td>
                    <td width="300"><? echo $row[csf("fabric_description")]; ?> </td> 
                    <td align="right"><? echo number_format($row[csf("product_qnty")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("product_qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}

if($action=="qc_qty_pop_up")
{
    echo load_html_head_contents("QC Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="7">QC Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Sys ID</th>
                        <th width="70">QC Date</th>
                        <th width="110">Order No</th>
                        <th width="70">Batch</th>
                        <th width="300">Item Description</th>
                        <th>Qty</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                $sql= "select a.product_no,a.prefix_no_num,a.product_date,b.batch_id, b.fabric_description,b.product_qnty,b.order_id,c.batch_no from  subcon_production_mst a, subcon_production_dtls b ,pro_batch_create_mst c where a.id=b.mst_id and a.entry_form=294 and b.batch_id=c.id and b.order_id='$expData[0]' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("product_date")]);?> </td> 
                    <td align="center" width="110"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="70"><? echo $row[csf("batch_no")]; ?></td>
                    <td width="300"><? echo $row[csf("fabric_description")]; ?> </td> 
                    <td align="right"><? echo number_format($row[csf("product_qnty")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("product_qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}



if($action=="total_delv_qty_pop_up")
{
    echo load_html_head_contents("QC Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:1130px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="11">AOP Delivery Status</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Delivery ID</th>
                        <th width="100">Delivery Date </th>
                        <th width="100">Order No</th>
                        <th width="100">Style</th>
                        <th width="80">Batch</th>
                        <th width="80">Color </th>
                        <th width="300">Item Description</th>
                        <th width="80">Batch Qty.</th>
                        <th width="80">Deli. Qty.</th>
                        <th width="">Deli. Blance</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls where status_active=1 and is_deleted=0",'id','order_no');
                $style_arr=return_library_array( "select id, buyer_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0",'id','buyer_style_ref');
                
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;

                $sql= "select a.product_no,a.prefix_no_num,a.product_no,a.product_date,b.batch_id, b.fabric_description,b.id,b.color_id,b.product_qnty,b.order_id,c.batch_no, d.batch_qnty from subcon_production_mst a, subcon_production_dtls b ,pro_batch_create_mst c, pro_batch_create_dtls d where a.id=b.mst_id and c.id=d.mst_id and a.entry_form=307 and b.batch_id=c.id and b.order_id='$expData[0]' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0";  //, d.order_no, d.buyer_style_ref, , subcon_ord_dtls d //and b.order_id=d.id
                //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><? echo $row[csf("product_no")];?> </td>
                    <td width="100"><? echo change_date_format($row[csf("product_date")]);?> </td> 
                    <td align="center" width="100"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="100"><? echo $style_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $row[csf("batch_no")]; ?></td>
                    <td align="center" width="80"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                    <td width="300"><? echo $row[csf("fabric_description")]; ?> </td> 
                    <td align="right" width="80"><? echo $row[csf("batch_qnty")]; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("product_qnty")],2); ?></td>
                    <td align="right" width=""><? echo number_format($row[csf("batch_qnty")]-$row[csf("product_qnty")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("product_qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td> 
                    <td  width="100">&nbsp;</td>
                    <td  width="100">&nbsp;</td>
                    <td  width="80">&nbsp;</td>
                    <td  width="80">&nbsp;</td>
                    <td width="300">&nbsp;</td> 
                    <td width="80" align="right">Total: </td>
                    <td width="80" align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td >&nbsp;</td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}



if($action=="bill_qty_pop_up")
{
    echo load_html_head_contents("QC Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:1130px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="11">AOP Delivery Status</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Bill ID</th>
                        <th width="100">Bill Date </th>
                        <th width="100">Order No</th>
                        <th width="100">Style</th>
                        <th width="80">Batch</th>
                        <th width="80">Color </th>
                        <th width="300">Item Description</th>
                        <th width="80">Batch Qty.</th>
                        <th width="80">Bill Qty.</th>
                        <th width="">Bill Blance</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls where status_active=1 and is_deleted=0",'id','order_no');
                $style_arr=return_library_array( "select id, buyer_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0",'id','buyer_style_ref');
                
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;


                $sql= "select a.bill_no,a.bill_date,b.batch_id,b.delivery_qty,b.order_id,c.batch_no, d.batch_qnty, e.fabric_description, e.color_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b ,pro_batch_create_mst c, pro_batch_create_dtls d, subcon_production_dtls e where a.id=b.mst_id and a.process_id=358 and b.delivery_id=e.id and c.id=d.mst_id and b.batch_id=c.id  and b.order_id='$expData[0]' and a.status_active=1 and a.is_deleted=0";
                //and a.process_id=358 and c.id=e.batch_id //and c.id='$expData[1]'



                /*$sql = "select  d.mst_id,d.delivery_qty as bill_qty 
                from subcon_ord_mst a, subcon_ord_dtls b, subcon_inbound_bill_dtls d, pro_batch_create_dtls e 
                where a.subcon_job=b.job_no_mst and e.mst_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.batch_id = e.mst_id order by a.subcon_job, b.delivery_status";*/


                //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><? echo $row[csf("bill_no")];?> </td>
                    <td width="100"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
                    <td align="center" width="100"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="100"><? echo $style_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $row[csf("batch_no")]; ?></td>
                    <td align="center" width="80"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                    <td width="300"><? echo $row[csf("fabric_description")]; ?> </td> 
                    <td align="right" width="80"><? echo $row[csf("batch_qnty")]; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("delivery_qty")],2); ?></td>
                    <td align="right" width=""><? echo number_format($row[csf("batch_qnty")]-$row[csf("delivery_qty")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("product_qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td> 
                    <td  width="100">&nbsp;</td>
                    <td  width="100">&nbsp;</td>
                    <td  width="80">&nbsp;</td>
                    <td  width="80">&nbsp;</td>
                    <td width="300">&nbsp;</td> 
                    <td width="80" align="right">Total: </td>
                    <td width="80" align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td >&nbsp;</td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}





if($action=="report_generate")
{    
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    //===============================================================start=====================================================================================
    $job_no=str_replace("'","",$txt_job_no);
    $txt_style_ref=str_replace("'","",$txt_style_ref);
    $txt_order_no=str_replace("'","",$txt_order_no);
    $cbo_party_name=str_replace("'","",$cbo_party_name);
    $txt_buyer_po=str_replace("'","",$txt_buyer_po);
    $txt_buyer_style=str_replace("'","",$txt_buyer_style);
    $cbo_within_group=str_replace("'","",$cbo_within_group);
    $txt_reference_no=str_replace("'","",$txt_reference_no);
    $txt_buyer_style=str_replace("'","",$txt_buyer_style);
    $cbo_location_name=str_replace("'","",$cbo_location_name);
    $txt_buyer_po=str_replace("'","",$txt_buyer_po);
    $txt_buyer_buyer_no=str_replace("'","",$txt_buyer_buyer_no);
    
    $cbo_floor_name=str_replace("'","",$cbo_floor_name);
    $cbo_machine_id=str_replace("'","",$cbo_machine_id);
    $cbo_delevery_status=str_replace("'","",$cbo_delevery_status);
    
    if($cbo_within_group!=0) $within_group_cond=" and a.within_group='$cbo_within_group'"; else $within_group_cond="";
    if($cbo_party_name!=0) $buyer_id_cond=" and a.party_id='$cbo_party_name'"; else $buyer_id_cond="";
    if($cbo_location_name!=0) $location_id_cond=" and a.location_id='$cbo_location_name'"; else $location_id_cond="";
    if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
    if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
    if ($txt_reference_no!='') $reference_no_cond=" and a.aop_reference like '%$txt_reference_no%'"; else $reference_no_cond="";
    if ($txt_buyer_style!='') $buyer_style_cond=" and b.buyer_style_ref like '%$txt_buyer_style%'"; else $buyer_style_cond="";
    if ($txt_buyer_po!='') $buyer_po_cond=" and b.buyer_po_no like '%$txt_buyer_po%'"; else $buyer_po_cond="";
    if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
    if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
    // if($cbo_delevery_status==1) $delivery_status_cond=""; else $delivery_status_cond=" and b.delivery_status=$cbo_delevery_status";
    if($cbo_within_group==1){
        if($txt_buyer_buyer_no!=0) $buyer_buyer_cond=" and b.buyer_buyer='$txt_buyer_buyer_no'"; else $buyer_buyer_cond="";
    } else if($cbo_within_group==1){
        if ($txt_buyer_buyer_no!='') $buyer_buyer_cond=" and b.buyer_buyer like '%$txt_buyer_buyer_no%'"; else $buyer_buyer_cond="";
    } else{
         $buyer_buyer_cond='';
    }
    
    
    if($cbo_floor_name!=0) $floor_name_cond=" and b.floor_id='$cbo_floor_name'"; else $floor_name_cond="";
    if($cbo_machine_id!=0) $machine_id_cond=" and b.machine_id='$cbo_machine_id'"; else $machine_id_cond="";
    
    $machinefloor_array=array();
    $machinefloor_sql="select b.order_id,b.floor_id, b.machine_id from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in (291) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $floor_name_cond $machine_id_cond  group by b.order_id,b.floor_id, b.machine_id";
    
    $machinefloor_sql_result=sql_select($machinefloor_sql);
    foreach ($machinefloor_sql_result as $row)
    {
        $machinefloor_array[]=$row[csf('order_id')].", ";
    }
    $order_details_id=rtrim(implode(" ",$machinefloor_array),", ");
    unset($machinefloor_sql_result);
    
    if($cbo_floor_name!=0 || $cbo_machine_id!=0)
    {
        $machine_flooR_cond=" and b.id in ($order_details_id)";
    }
    else
    { 
        $machine_flooR_cond=""; 
    }
            
    
    
    $date_from = str_replace("'", "", $txt_date_from);
    if ($date_from!="") $date_cond=" and a.delivery_date<='$date_from'"; else $date_cond="";
    $buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
    //==============================================================end========================================================================================
    
    //===============================================================start=====================================================================================
    $job_rece_issue_sql="select b.job_dtls_id,
    sum(case when a.trans_type=1 and a.entry_form=279 and a.subcon_date<='".$date_from."' then b.quantity else 0 end) as total_rec_pre_quantity,
    sum(case when a.trans_type=1 and a.entry_form=279 and a.subcon_date<'".$date_from."' then b.quantity else 0 end) as rec_pre_quantity,
    sum(case when a.trans_type=1 and a.entry_form=279 and a.subcon_date='".$date_from."' then b.quantity else 0 end) as rec_today_quantity,
    sum(case when a.trans_type=2 and a.entry_form=280 and a.subcon_date<='".$date_from."' then b.quantity else 0 end) as total_issue_pre_quantity,
    sum(case when a.trans_type=2 and a.entry_form=280 and a.subcon_date<'".$date_from."' then b.quantity else 0 end) as issue_pre_quantity,
    sum(case when a.trans_type=2 and a.entry_form=280 and a.subcon_date='".$date_from."' then b.quantity else 0 end) as issue_today_quantity
    from sub_material_mst a, sub_material_dtls b where 
    a.id=b.mst_id and a.trans_type in (1,2) and a.entry_form in (279,280) 
    and a.status_active=1 and b.is_deleted=0 
    and b.status_active=1 and b.is_deleted=0 
    group by b.job_dtls_id";
    $rece_issue_sql_result=sql_select($job_rece_issue_sql);
    $inventory_rece_issue_array=array();
    foreach ($rece_issue_sql_result as $row)
    {
        $inventory_rece_issue_array[$row[csf('job_dtls_id')]]['rec_pre_quantity']=$row[csf('rec_pre_quantity')];
        $inventory_rece_issue_array[$row[csf('job_dtls_id')]]['rec_today_quantity']=$row[csf('rec_today_quantity')];
        $inventory_rece_issue_array[$row[csf('job_dtls_id')]]['issue_pre_quantity']=$row[csf('issue_pre_quantity')];
        $inventory_rece_issue_array[$row[csf('job_dtls_id')]]['issue_today_quantity']=$row[csf('issue_today_quantity')];
        $inventory_rece_issue_array[$row[csf('job_dtls_id')]]['total_rec_pre_quantity']=$row[csf('total_rec_pre_quantity')];
        $inventory_rece_issue_array[$row[csf('job_dtls_id')]]['total_issue_pre_quantity']=$row[csf('total_issue_pre_quantity')];
    }
    unset($rece_issue_sql_result);
    
    $inventory_ret_array=array();
    $inv_ret_sql="select b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
    
    $inv_ret_sql_result=sql_select($inv_ret_sql);
    foreach ($inv_ret_sql_result as $row)
    {
        $inventory_ret_array[$row[csf('order_id')]]=$row[csf('quantity')];
    }
    
    //==============================================================end========================================================================================



    //===============================================================start=====================================================================================
        $production_sql="select b.order_id, a.entry_form, b.buyer_po_id,
        sum(case when a.entry_form=291 and a.product_date<='".$date_from."' then b.product_qnty else 0 end) as total_prod_pre_quantity,
        sum(case when a.entry_form=291 and a.product_date<'".$date_from."' then b.product_qnty else 0 end) as prod_pre_quantity,
        sum(case when a.entry_form=291 and a.product_date='".$date_from."' then b.product_qnty else 0 end) as prod_today_quantity,
        sum(case when a.entry_form=294 and a.product_date<='".$date_from."' then b.product_qnty else 0 end) as qc_total_prod_pre_quantity,
        sum(case when a.entry_form=294 and a.product_date<'".$date_from."' then b.product_qnty else 0 end) as qc_prod_pre_quantity,
        sum(case when a.entry_form=294 and a.product_date='".$date_from."' then b.product_qnty else 0 end) as qc_prod_today_quantity,
        sum(case when a.entry_form=294 and a.product_date<='".$date_from."' then b.reject_qnty else 0 end) as total_reject_qnty_quantity,
        sum(case when a.entry_form=307 and a.product_date<='".$date_from."' then b.product_qnty else 0 end) as total_delv_quantity,
        sum(case when a.entry_form=307 and a.product_date<'".$date_from."' then b.product_qnty else 0 end) as delv_pre_quantity,
        sum(case when a.entry_form=307 and a.product_date='".$date_from."' then b.product_qnty else 0 end) as delv_today_quantity
        from subcon_production_mst a, subcon_production_dtls b  
        where a.id=b.mst_id and a.entry_form in (291,294,307) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $delivery_status_cond and b.is_deleted=0 and a.product_date<='".$date_from."' 
        group by b.order_id, a.entry_form, b.buyer_po_id";

        /*$production_sql="select b.order_id, a.entry_form, b.buyer_po_id,
        sum(case when a.product_date<='".$date_from."' then b.product_qnty else 0 end) as total_prod_pre_quantity,
        sum(case when a.product_date<'".$date_from."' then b.product_qnty else 0 end) as prod_pre_quantity,
        sum(case when a.product_date='".$date_from."' then b.product_qnty else 0 end) as prod_today_quantity,
        sum(case when a.product_date<='".$date_from."' then b.product_qnty else 0 end) as qc_total_prod_pre_quantity,
        sum(case when a.product_date<'".$date_from."' then b.product_qnty else 0 end) as qc_prod_pre_quantity,
        sum(case when a.product_date='".$date_from."' then b.product_qnty else 0 end) as qc_prod_today_quantity,
        sum(case when a.product_date<='".$date_from."' then b.reject_qnty else 0 end) as total_reject_qnty_quantity,
        sum(case when a.product_date<='".$date_from."' then b.product_qnty else 0 end) as total_delv_quantity,
        sum(case when a.product_date<'".$date_from."' then b.product_qnty else 0 end) as delv_pre_quantity,
        sum(case when a.product_date='".$date_from."' then b.product_qnty else 0 end) as delv_today_quantity
        from subcon_production_mst a, subcon_production_dtls b  
        where a.id=b.mst_id and a.entry_form in (291,294,307) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.product_date<='".$date_from."' 
        group by b.order_id, a.entry_form, b.buyer_po_id";*/
        //echo $production_sql;die;
        $production_sql_result=sql_select($production_sql);
        $production_array=array();
        $order_id_arr=array();
        foreach ($production_sql_result as $row)
        {
            if($row[csf('entry_form')] == 291) {
                if($cbo_within_group == 2) {
                    $tmp_ord_id = explode(',', $row[csf('order_id')]);
                } else if($cbo_within_group == 1){
                    $tmp_ord_id = explode(',', $row[csf('buyer_po_id')]);
                } else {
                    $tmp_ord_id = explode(',', $row[csf('order_id')]);
                }

                if(count($tmp_ord_id) > 0) {
                    for ($i=0; $i < count($tmp_ord_id); $i++) { 
                        $tmp_ord_id = $tmp_ord_id[$i];
                    }
                }
                // else {
                //     if($cbo_within_group == 2) {
                //         $tmp_ord_id = $row[csf('order_id')];
                //     } else {
                //         //$tmp_ord_id = explode(',', $row[csf('buyer_po_id')]);
                //         $tmp_ord_id = $row[csf('buyer_po_id')];
                //     }
                // }
            }

            if($cbo_within_group == 2) {
                $order_id_arr[] = $row[csf('order_id')];
            } else if($cbo_within_group == 1){
                $order_id_arr[] = $row[csf('buyer_po_id')];
            } else {
                 $order_id_arr[] = $row[csf('order_id')]; // new dev
                 $byer_po_id_arr[] = $row[csf('buyer_po_id')]; // new dev
            }

            //***************
            if(isset($production_array[$row[csf('order_id')]])) {
                switch ($row[csf('entry_form')]) {
                    case 291:   // if production entry
                        $production_array[$row[csf('order_id')]]['total_prod_pre_quantity']=$row[csf('total_prod_pre_quantity')];
                        $production_array[$row[csf('order_id')]]['prod_pre_quantity']=$row[csf('prod_pre_quantity')];
                        $production_array[$row[csf('order_id')]]['prod_today_quantity']=$row[csf('prod_today_quantity')];
                        break;

                    case 294:   // if qc entry
                        $production_array[$row[csf('order_id')]]['qc_total_prod_pre_quantity']=$row[csf('qc_total_prod_pre_quantity')];
                        $production_array[$row[csf('order_id')]]['qc_prod_pre_quantity']=$row[csf('qc_prod_pre_quantity')];
                        $production_array[$row[csf('order_id')]]['qc_prod_today_quantity']=$row[csf('qc_prod_today_quantity')];
                        $production_array[$row[csf('order_id')]]['total_reject_qnty_quantity']=$row[csf('total_reject_qnty_quantity')];
                        break;
                    
                    case 307:   // if delivery entry
                        $production_array[$row[csf('order_id')]]['total_delv_quantity']=$row[csf('total_delv_quantity')];
                        $production_array[$row[csf('order_id')]]['delv_pre_quantity']=$row[csf('delv_pre_quantity')];
                        $production_array[$row[csf('order_id')]]['delv_today_quantity']=$row[csf('delv_today_quantity')];
                        $production_array[$row[csf('order_id')]]['order_id']=$row[csf('order_id')];
                        break;
                }
            } else {
                $production_array[$row[csf('order_id')]]['total_prod_pre_quantity']=$row[csf('total_prod_pre_quantity')];
                $production_array[$row[csf('order_id')]]['prod_pre_quantity']=$row[csf('prod_pre_quantity')];
                $production_array[$row[csf('order_id')]]['prod_today_quantity']=$row[csf('prod_today_quantity')];
                                //***************
                $production_array[$row[csf('order_id')]]['qc_total_prod_pre_quantity']=$row[csf('qc_total_prod_pre_quantity')];
                $production_array[$row[csf('order_id')]]['qc_prod_pre_quantity']=$row[csf('qc_prod_pre_quantity')];
                $production_array[$row[csf('order_id')]]['qc_prod_today_quantity']=$row[csf('qc_prod_today_quantity')];
                $production_array[$row[csf('order_id')]]['total_reject_qnty_quantity']=$row[csf('total_reject_qnty_quantity')];
                                //***************
                $production_array[$row[csf('order_id')]]['total_delv_quantity']=$row[csf('total_delv_quantity')];
                $production_array[$row[csf('order_id')]]['delv_pre_quantity']=$row[csf('delv_pre_quantity')];
                $production_array[$row[csf('order_id')]]['delv_today_quantity']=$row[csf('delv_today_quantity')];
                $production_array[$row[csf('order_id')]]['order_id']=$row[csf('order_id')];
            }            
        }

        // echo "<pre>";
        // print_r($production_array);
        // echo "</pre>";

        unset($production_sql_result);
        // $buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
        $order_ids=implode(",", array_unique($order_id_arr));

        $order_id_arr = array_unique($order_id_arr);

        $ord_id="";
        $con = connect();
        $user_id = $_SESSION['logic_erp']["user_id"] ;
        if($db_type==0) { mysql_query("BEGIN"); }
        foreach($order_id_arr as $ord_id) {
            if($ord_id!=0) {
                $r_id2=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$ord_id,985)");
                // echo "insert into tmp_poid (userid, poid, type) values ($user_id,$ord_id,985)";
                // if($pi_id=="") $pi_id=$p_id[csf('pi_id')];else $pi_id.=",".$p_id[csf('pi_id')];
            }            
        }
        if($r_id2==1) $flag=1; else $flag=0;
        if($cbo_within_group == '' || $cbo_within_group == 0) 
        {
            // $order_id_arr[] = $row[csf('order_id')]; // new dev
            //$byer_po_id_arr[] = $row[csf('buyer_po_id')]; // new dev
            // $buyer_po_ids=implode(",", array_unique($byer_po_id_arr));

            $byer_po_id_arr = array_unique($byer_po_id_arr);
            $buyer_po_id="";
            foreach($byer_po_id_arr as $buyer_po_id) {
                if($buyer_po_id!=0) {
                    $r_id3=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$buyer_po_id,986)");
                    // echo "insert into tmp_poid (userid, poid, type) values ($user_id,$ord_id,985)";
                    // if($pi_id=="") $pi_id=$p_id[csf('pi_id')];else $pi_id.=",".$p_id[csf('pi_id')];
                }            
            }
            if($r_id3==1) $flag=1; else $flag=0;
        }


        if($db_type==0) {
            if($flag) {
                mysql_query("COMMIT");  
            }
        }
        if($db_type==2 || $db_type==1) {
            if($flag) {
                oci_commit($con);  
            }
        }
        //echo $order_ids;die;
        //==============================================================end========================================================================================
    
        //===============================================================start=====================================================================================
        $del_date_cond = str_replace("c.delivery_date", "a.delivery_date", $date_cond);
        $order_wise_tot_bill_arr2=array();
        $sum=0;
        foreach ($order_wise_tot_bill_arr2 as $key=>$value) 
        {
            foreach ($value as $val) 
            {
                foreach ($val as $val2) 
                {
                     $sum+=$val2;
                     break;
                }
            }
            $order_wise_tot_bill_arr[$key]=$sum;
            $sum=0;
        }
        //==============================================================end=====================================================================================                             
        //===============================================================start=====================================================================================
        $buyer_po_arr=array();
        $po_sql ="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
        $po_sql_res=sql_select($po_sql);
        foreach ($po_sql_res as $row)
        {
            $buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
            
        }
        unset($po_sql_res);
    
        //==============================================================end========================================================================================
    
        //===============================================================start=====================================================================================
        $po_id_cond = '';

        if($cbo_within_group == 2) {
            $po_id_cond = 'and b.id=tmp.poid';
        } else if ($cbo_within_group == 1) {
            $po_id_cond = 'and b.buyer_po_id=tmp.poid';
        } else {
            $po_id_cond = 'and ( ( b.id=tmp.poid and tmp.type=985) or ( b.buyer_po_id=tmp.poid and tmp.type=986))'; //new dev
        }
        // comment main query
        /*$job_sql = "select a.id,a.subcon_job,a.within_group, a.party_id, b.order_no, b.delivery_status,
        b.order_quantity,b.amount,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,
        b.id as job_dtls_id,b.buyer_buyer,a.aop_reference, a.aop_work_order_type as wo_type, d.delivery_qty as bill_qty, d.amount as bill_amount
        from subcon_ord_mst a, subcon_ord_dtls b, tmp_poid tmp, subcon_inbound_bill_dtls d, pro_batch_create_dtls e
        where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 
        and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $po_id_cond and tmp.userid=$user_id 
        $order_no_cond $style_ref_cond $job_no_cond $location_id_cond $within_group_cond $buyer_id_cond $reference_no_cond $buyer_style_cond $buyer_po_cond $buyer_buyer_cond $machine_flooR_cond and d.batch_id = e.mst_id and e.po_id = tmp.poid
        order by a.subcon_job, b.delivery_status";*/



        //echo $cbo_within_group; die;
        $job_sql = "select a.id,a.subcon_job,a.within_group, a.party_id, b.order_no, b.delivery_status,
        b.order_quantity,b.amount,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,
        b.id as job_dtls_id,b.buyer_buyer,a.aop_reference, a.aop_work_order_type as wo_type, e.mst_id
        from subcon_ord_mst a, subcon_ord_dtls b, tmp_poid tmp, pro_batch_create_dtls e
        where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 
        and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $po_id_cond and tmp.userid=$user_id 
        $order_no_cond $style_ref_cond $job_no_cond $location_id_cond $within_group_cond $buyer_id_cond $reference_no_cond $buyer_style_cond $buyer_po_cond $buyer_buyer_cond $machine_flooR_cond and e.po_id = tmp.poid
        order by a.subcon_job, b.delivery_status"; 
        //echo $job_sql; die;

        
        // echo $job_sql;die;
        $job_sql_result=sql_select($job_sql);

        $jobs_arr = array();
        $dtls_id_arr= array();
        $batch_id_arr= array();
        foreach ($job_sql_result as $row) {

            $job_dtls_id    = $row[csf('job_dtls_id')];
            $wo_type        = $row[csf('wo_type')];
            $within_group_id= $row[csf('within_group')];
            $dtls_id_arr[]  = $row[csf('job_dtls_id')];
            $batch_id_arr[]  = $row[csf('mst_id')];
            $tot_delv       = $production_array[$job_dtls_id]['total_delv_quantity'];
            //if($wo_type=='') $wo_type=0;
            if($within_group_id==1) $wo_type=0;

            // if searching for partial delivery
            if ($cbo_delevery_status == 2) {
                // if delivery quantity is less then order quantity and delivery status is not full delivered
                if( ($tot_delv < $row[csf('order_quantity')]) &&  ($row[csf('delivery_status')] != 3) ) {
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['job_dtls_id'] = $job_dtls_id;
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['wo_type'] = $wo_type;
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['id'] = $row[csf('id')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['subcon_job'] = $row[csf('subcon_job')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['within_group'] = $row[csf('within_group')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['party_id'] = $row[csf('party_id')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['order_no'] = $row[csf('order_no')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['delivery_status'] = $row[csf('delivery_status')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['order_quantity'] = $row[csf('order_quantity')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['amount'] = $row[csf('amount')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['order_uom'] = $row[csf('order_uom')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['buyer_po_id'] = $row[csf('buyer_po_id')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['buyer_style_ref'] = $row[csf('buyer_style_ref')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['delivery_date'] = $row[csf('delivery_date')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['buyer_buyer'] = $row[csf('buyer_buyer')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['aop_reference'] = $row[csf('aop_reference')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['mst_id'] = $row[csf('mst_id')];
                }
            } else if ($cbo_delevery_status == 3) {
                // echo "deliver status 3";
                // if delivery quantity is greater then or equal to order quantity or delivery status is full delivered
                if( ($tot_delv >= $row[csf('order_quantity')]) || ($row[csf('delivery_status')] == 3) ) {
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['job_dtls_id'] = $job_dtls_id;
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['wo_type'] = $wo_type;
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['id'] = $row[csf('id')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['subcon_job'] = $row[csf('subcon_job')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['within_group'] = $row[csf('within_group')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['party_id'] = $row[csf('party_id')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['order_no'] = $row[csf('order_no')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['delivery_status'] = $row[csf('delivery_status')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['order_quantity'] = $row[csf('order_quantity')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['amount'] = $row[csf('amount')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['order_uom'] = $row[csf('order_uom')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['buyer_po_id'] = $row[csf('buyer_po_id')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['buyer_style_ref'] = $row[csf('buyer_style_ref')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['delivery_date'] = $row[csf('delivery_date')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['buyer_buyer'] = $row[csf('buyer_buyer')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['aop_reference'] = $row[csf('aop_reference')];
                    $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['mst_id'] = $row[csf('mst_id')];
                }
            } else {
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['job_dtls_id'] = $job_dtls_id;
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['wo_type'] = $wo_type;
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['id'] = $row[csf('id')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['subcon_job'] = $row[csf('subcon_job')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['within_group'] = $row[csf('within_group')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['party_id'] = $row[csf('party_id')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['order_no'] = $row[csf('order_no')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['delivery_status'] = $row[csf('delivery_status')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['order_quantity'] = $row[csf('order_quantity')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['amount'] = $row[csf('amount')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['order_uom'] = $row[csf('order_uom')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['buyer_po_id'] = $row[csf('buyer_po_id')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['buyer_style_ref'] = $row[csf('buyer_style_ref')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['delivery_date'] = $row[csf('delivery_date')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['buyer_buyer'] = $row[csf('buyer_buyer')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['aop_reference'] = $row[csf('aop_reference')];
                $jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['mst_id'] = $row[csf('mst_id')];
            }

            //$jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['bill_qty'] = $row[csf('bill_qty')];
            //$jobs_arr[$within_group_id][$wo_type][$job_dtls_id]['bill_amount'] = $row[csf('bill_amount')];
        }

        


        $dtls_ids=implode(",", array_unique($dtls_id_arr));
        $batch_ids=implode(",", array_unique($batch_id_arr));

       // echo $dtls_ids; die;


        //echo "<pre>";
        //print_r($jobs_arr);//die;
        // echo "</pre>";
		
		
		
		/* $bill_sql = "select d.id as bill_id, d.delivery_qty, d.amount as bill_amount, d.batch_id, c.buyer_po_no,c.id as job_dtls_id
                from subcon_inbound_bill_dtls d, tmp_poid e, subcon_ord_dtls c
                where d.order_id=e.poid  and d.process_id=358 and e.type=$tmpType and e.userid=$user_id and d.order_id=c.id";
    $bill_result = sql_select($bill_sql);

    $bill_arr = array();
    foreach ($bill_result as $row) 
	{
        $bill_arr[$row[csf('job_dtls_id')]]['billQty'] += $row[csf('delivery_qty')];
        $bill_arr[$row[csf('job_dtls_id')]]['billAmt'] += $row[csf('bill_amount')];
        $bill_arr[$row[csf('job_dtls_id')]]['bill_id'] = $row[csf('bill_id')];
    }
*/
    
/*
       $job_bill_sql = "select a.id,a.subcon_job,a.within_group, a.party_id, b.order_no,b.delivery_status,b.order_quantity,b.amount,b.order_uom,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,b.id as job_dtls_id,b.buyer_buyer,a.aop_reference, a.aop_work_order_type as wo_type, d.delivery_qty as bill_qty, d.amount as bill_amount from subcon_ord_mst a, subcon_ord_dtls b, tmp_poid tmp, subcon_inbound_bill_dtls d, pro_batch_create_dtls e where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and e.mst_id in($batch_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and tmp.userid=$user_id and d.batch_id = e.mst_id and e.po_id = tmp.poid order by a.subcon_job, b.delivery_status"; */ //and b.id in($dtls_ids) //and e.mst_id in($batch_ids)  //and a.entry_form=278 $po_id_cond
	   
	  /*   $bill_sql = "select d.id as bill_id, d.delivery_qty, d.amount as bill_amount, d.batch_id, c.buyer_po_no,c.id as job_dtls_id
                from subcon_inbound_bill_dtls d, tmp_poid e, subcon_ord_dtls c
                where d.order_id=e.poid  and d.process_id=358 and e.type=$tmpType and e.userid=$user_id and d.order_id=c.id";
    $bill_result = sql_select($bill_sql);*/
	   
	    $job_bill_sql = "select a.within_group,b.id as job_dtls_id, a.aop_work_order_type as wo_type,d.delivery_qty as bill_qty, d.amount as bill_amount from subcon_ord_mst a, subcon_ord_dtls b, tmp_poid tmp, subcon_inbound_bill_dtls d  where a.id=b.mst_id and d.order_id=b.id and  a.entry_form=278  and d.process_id=358 and a.company_id=$cbo_company_id and d.batch_id in($batch_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and tmp.userid=$user_id  and b.id=tmp.poid and d.order_id=tmp.poid group by a.within_group,b.id, a.aop_work_order_type,d.delivery_qty, d.amount";  

        //echo $job_bill_sql; die;
        //$job_bill_sql=array();
        $job_bill_result=sql_select($job_bill_sql);

        $job_arr_with_bill =array();
        foreach ($job_bill_result as $row) 
		{

            $job_dtls_id    = $row[csf('job_dtls_id')];
            $wo_type        = $row[csf('wo_type')];
            $within_group_id= $row[csf('within_group')];
            

            $job_arr_with_bill[$within_group_id][$wo_type][$job_dtls_id]['bill_qty'] += $row[csf('bill_qty')];
            $job_arr_with_bill[$within_group_id][$wo_type][$job_dtls_id]['bill_amount'] += $row[csf('bill_amount')];
            
        }
    //==============================================================end========================================================================================
    ob_start();
    $tbl_width=3200;
    $col_span=34;
    
    ?>
    <style>
        table th td{word-break: break-all;}
    </style>
    
    <div>
        <table width="3290"  cellspacing="0" cellpadding="0" rules="all" align="left" border="1">
            <tr style="border:none;">
                <td colspan="34" align="center" style="border:none; font-size:14px;">
                 <? echo "AOP Production Status Report"; ?>                                
             </td>
            </tr>
            <tr style="border:none;">
                <td colspan="34" align="center" style="border:none;font-size:12px; font-weight:bold">
                 <? echo change_date_format($date_from);?>
                </td>
            </tr>
        </table>
        <?
        $totalBillQty = 0;
        $totalBillAmount = 0;
        $table_id=1;
        foreach ($jobs_arr as $within_group_id=>$within_group_data)
        {
            foreach ($within_group_data as $wo_type=>$wo_type_data)
            {
                ?>
                <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" align="left" class="rpt_table">
                    <thead>
                        <tr >
                            <th colspan="34" style="text-align:left"><? 
                            if($within_group_id==1 )
                            {
                                echo "In-House";
                                /*if($wo_type ==1){
                                    
                                }
                                else{
                                    echo "Sample";
                                }*/
                            }
                            else
                            {
                                if($wo_type==2){
                                    echo "Subcontract-Sample";
                                } else {
                                    echo "Subcontract";
                                }
                            }
                            ?></th>
                        </tr>
                        <tr>
                        <th colspan="34"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
                        </tr>
                        <tr>
                           <th width="30">SL</th>
                           <th width="100">AOP Job No</th>
                           <th width="100">Party</th>
                           <th width="100">AOP Order no</th>
                           <th width="100">Aop Reference</th> 
                           <th width="100">Buyer</th>
                           <th width="100">Buyer PO</th>                            
                           <th width="100">Buyer Style</th>
                           <th width="100">Order Quantity</th>
                           <th width="100">Order Value</th>
                           <th width="60">UOM</th>
                           <th width="90">Delivery Date</th>
                           <th width="60">Days in Hand</th>
                           <th width="120">Pre Mat Rec</th>
                           <th width="120">Today Material Rec</th>
                           <th width="120">Total Rec</th>
                           <th width="120">Pre Mat Issue</th>
                           <th width="120">Today Material Issue</th>
                           <th width="120">Total Issue</th>
                           <th width="120">Material Balance</th>
                           <th width="80">Pre Production</th>
                           <th width="80">Today Prod. Qty</th>
                           <th width="80">Total Production</th>
                           <th width="80">Pre Qc</th>
                           <th width="80">Today QC Qty</th>
                           <th width="80">Total Reject Qty</th>
                           <th width="80">Total Qc</th>
                           <th width="80">Prod Balance</th>
                           <th width="80">Pre Delivery</th>
                           <th width="80">Today Delivery Qty</th>
                           <th width="80">Total Delivery</th> 
                           <th width="80">Delv Balance</th>
                           <th width="80">Total Bill Qty</th>
                           <th width="80">Bill Amount</th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
                    <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" align="left" id="table_body<? echo $table_id; ?>">
                        <tbody>
                            <?
                            $process_array=array();
                            $i=1;
                            $totalRec = 0;
                            $totalProd = 0;
                            $totalQc = 0;
                            $tot_total_order_qty=$tot_total_order_val=$tot_total_rec_qty=$totalRec=$tot_total_issue_qty=$tot_total_material_blce=$tot_total_prod_qty=$totalProd=$tot_total_qc_qty=$total_reject_quantity=$totalQc=$tot_delv_today_quantity=$tot_delv_balance=$totalBillQty=$totalBillAmt=0;
                            foreach ($wo_type_data as $job_dtls_id=>$row)
                            {
                                if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
                                if ($cbo_delevery_status == 1) {
                                    # code...
                                }
                                //===============================================================start====================================  
                                $total_prod_pre_quantity=$production_array[$row['job_dtls_id']]['total_prod_pre_quantity'];
                                $prod_pre_quantity=$production_array[$row['job_dtls_id']]['prod_pre_quantity'];
                                $prod_today_quantity=$production_array[$row['job_dtls_id']]['prod_today_quantity'];
                                $qc_total_prod_pre_quantity=$production_array[$row['job_dtls_id']]['qc_total_prod_pre_quantity'];
                                $qc_prod_pre_quantity=$production_array[$row['job_dtls_id']]['qc_prod_pre_quantity'];
                                $qc_prod_today_quantity=$production_array[$row['job_dtls_id']]['qc_prod_today_quantity'];
                                $total_reject_qnty_quantity=$production_array[$row['job_dtls_id']]['total_reject_qnty_quantity'];
                                                    //*************************
                                $rec_qty=$inventory_rece_issue_array[$row['job_dtls_id']]['total_rec_pre_quantity']-$inventory_ret_array[$row['id']];
                                $issue_qty=$inventory_rece_issue_array[$row['job_dtls_id']]['total_issue_pre_quantity'];
                                $rec_pre_quantity=$inventory_rece_issue_array[$row['job_dtls_id']]['rec_pre_quantity'];
                                $rec_today_quantity=$inventory_rece_issue_array[$row['job_dtls_id']]['rec_today_quantity'];
                                $issue_pre_quantity=$inventory_rece_issue_array[$row['job_dtls_id']]['issue_pre_quantity'];
                                $issue_today_quantity=$inventory_rece_issue_array[$row['job_dtls_id']]['issue_today_quantity'];
                                //*************************
                                $total_delv_quantity=$production_array[$row['job_dtls_id']]['total_delv_quantity'];
                                $delv_pre_quantity=$production_array[$row['job_dtls_id']]['delv_pre_quantity'];
                                $delv_today_quantity=$production_array[$row['job_dtls_id']]['delv_today_quantity'];
                                //*************************

                                $production_order_id = $production_array[$row['job_dtls_id']]['order_id'];
                                $prod_balance=$total_prod_pre_quantity-$qc_total_prod_pre_quantity;
                                $delv_balance=$total_prod_pre_quantity-$total_delv_quantity;





                                //$billQty = $row['bill_qty'];
                                $billQty = $job_arr_with_bill[$row['within_group']][$row['wo_type']][$row['job_dtls_id']]['bill_qty'];

                                //$billAmt = $row['bill_amount'];
                                $billAmt = $job_arr_with_bill[$row['within_group']][$row['wo_type']][$row['job_dtls_id']]['bill_amount'];  
                                
                                
                                                     
                                //*************************//
                                if($row['within_group']==1) 
                                {
                                    $partyarr=$company_array;
                                }
                                else
                                {
                                    $partyarr=$party_arr;
                                }
                                //==============================================================end=======================================
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">   
                                    <td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
                                    <td width="100" align="center"><p><? echo $row['subcon_job']; ?></p></td>
                                    <td width="100"><p><? echo $partyarr[$row['party_id']]; ?></p></td>
                                    <td width="100"><p><? echo $row['order_no']; ?></p></td>
                                    <td width="100"><?  echo $row['aop_reference']; ?></td>  
                                    <td width="100"><? if($cbo_within_group==1){echo $buyer_arr[$row['buyer_buyer']];}else{echo $row['buyer_buyer'];} ?></td>  
                                    <td width="100"><? echo $buyer_po_arr[$row['buyer_po_id']]['po'] ; ?></td>                              
                                    <td width="100"><p><? echo $buyer_po_arr[$row['buyer_po_id']]['style'] ?></p></td>
                                    <td width="100" align="right"><p> <a href="##" onclick="show_progress_report_details('order_desc_popup','<? echo $row["job_dtls_id"]; ?>','850px')"> <? echo number_format($row['order_quantity'],2); ?> </a></p></td>
                                    <td width="100" align="right"><p><? echo number_format($row['amount'],2); ?></p></td>
                                   <td width="60" align="center"><p><? echo $unit_of_measurement[$row['order_uom']]; ?></p></td>
                                    <td width="90"><p><? echo change_date_format($row['delivery_date']); ?></p></td>
                                    <td width="60" align="center"><? $daysOnHand = datediff("d",date("Y-m-d"),$row['delivery_date']); echo $daysOnHand; ?> </td>
                                    <td width="120" align="right"><p><? echo number_format($rec_pre_quantity,2); ?></p></td>
                                    <td width="120" align="right"><p><? echo number_format($rec_today_quantity,2); ?></p></td>
                                    <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_popup','<? echo $row["job_dtls_id"]; ?>','850px')"><? echo number_format($rec_qty,2); ?></a></p></td>
                                    <td width="120" align="right"><p><? echo number_format($issue_pre_quantity,2); ?></p></td>
                                    <td width="120" align="right"><p><? echo number_format($issue_today_quantity,2); ?></p></td>
                                    <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_iss_popup','<? echo $row["job_dtls_id"]; ?>','850px')"><? echo number_format($issue_qty,2); ?></a></p></td>
                                    <td width="120" align="right"><?
                                        $mat_blnce_qty=$rec_qty-$issue_qty;
                                        echo number_format($mat_blnce_qty,2);
                                        ?></td>
                                    <td width="80" align="right"><p><? echo number_format($prod_pre_quantity,2); ?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($prod_today_quantity,2); ?></p></td>
                                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $production_order_id; ?>','850px')"><? echo number_format($total_prod_pre_quantity,2); ?></a></p></td>
                                    <td width="80" align="right"><p><? echo number_format($qc_prod_pre_quantity,2); ?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($qc_prod_today_quantity,2); ?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($total_reject_qnty_quantity,2);?></p></td>
                                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('qc_qty_pop_up','<? echo $row["job_dtls_id"]; ?>','850px')"><? echo number_format($qc_total_prod_pre_quantity,2); ?></a></p></td>
                                    <td width="80" align="right"><p><? echo number_format($prod_balance,2);?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($delv_pre_quantity,2); ?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($delv_today_quantity,2); ?></p></td>
                                    


                                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('total_delv_qty_pop_up','<? echo $row["job_dtls_id"]; ?>','1150px')"><? echo number_format($total_delv_quantity,2); ?></a></p></td>


                                    <td width="80" align="right"><p><? echo number_format($delv_balance,2); ?></p></td>
                                    


                                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('bill_qty_pop_up','<? echo $row["job_dtls_id"]."_".$row["mst_id"]; ?>','1150px')"><? echo number_format($billQty,2); ?></a></p></td>


                                    <td width="80" align="right"><?php echo number_format($billAmt); ?></td>
                                </tr>
                                <?
                                $i++;
                               
                                ////////////////////////////////////////////////////////////////
                                $tot_total_order_qty+=$row['order_quantity'];
                                $tot_total_order_val+=$row['amount'];
                                $tot_total_rec_qty+=$rec_today_quantity;;
                                $tot_total_issue_qty+=$issue_today_quantity;
                                $tot_total_material_blce+=$mat_blnce_qty;
                                $tot_total_prod_qty+=$prod_today_quantity;
                                $tot_total_qc_qty+=$qc_prod_today_quantity;
                                $tot_delv_today_quantity+=$delv_today_quantity;
                                $tot_delv_balance+=$delv_balance;
                                $total_reject_quantity+=$total_reject_qnty_quantity;
                                $totalRec += $rec_qty;
                                $totalProd += $total_prod_pre_quantity;
                                $totalQc += $qc_total_prod_pre_quantity;
                                $totalBillQty += $billQty;
                                $totalBillAmt += $billAmt;
                                            ////////////////////////////////////////////////////////////////////        
                            }
                            ?>
                        </tbody>
                    </table>
                   <!--  <script language="javascript"> setFilterGrid('table_body',-1)</script> -->
                </div>

                <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" align="left" id="table_footer">
                    <tr class="tbl_bottom">
                        <td width="30"></td>
                        <td width="100"></td>
                        <td width="100"></td>
                        <td width="100"></td>
                        <td width="100"></td>  
                        <td width="100"></td>  
                        <td width="100"></td>                              
                        <td width="100">Grand Total:</td>
                        <td width="100" align="right" id="value_tot_total_order_qty<? echo $table_id; ?>"><? echo number_format($tot_total_order_qty); ?></td>
                        <td width="100" align="right" id="value_tot_total_order_val<? echo $table_id; ?>"><? echo number_format($tot_total_order_val); ?></td>
                        <td width="60" align="center"></td>
                        <td width="90"></td>
                        <td width="60" align="center"></td>
                        <td width="120" align="right"></td>
                        <td width="120" align="right" id="value_tot_total_rec_qty<? echo $table_id; ?>"><? echo number_format($tot_total_rec_qty); ?></td>
                        <td width="120" align="right" id="value_tot_total_totalRec<? echo $table_id; ?>"><?php echo number_format($totalRec, 2); ?></td>
                        <td width="120" align="right"></td>
                        <td width="120" align="right" id="value_tot_total_issue_qty<? echo $table_id; ?>"><? echo number_format($tot_total_issue_qty); ?></td>
                        <td width="120" align="right"></td>
                        <td width="120" align="right" id="value_tot_total_material_blce<? echo $table_id; ?>"><? echo number_format($tot_total_material_blce) ?></td>
                        <td width="80" align="right"></td>
                        <td width="80" align="right" id="value_tot_total_prod_qty<? echo $table_id; ?>"><? echo number_format($tot_total_prod_qty); ?></td>
                        <td width="80" align="right" id="value_totalProd<? echo $table_id; ?>"><?php echo number_format($totalProd, 2); ?></td>
                        <td width="80" align="right"></td>
                        <td width="80" align="right" id="value_tot_total_qc_qty<? echo $table_id; ?>"><? echo number_format($tot_total_qc_qty); ?></td>
                        <td width="80" align="right" id="value_total_reject_quantity<? echo $table_id; ?>"><? echo number_format($total_reject_quantity); ?></td>
                        <td width="80" align="right" id="value_totalQc<? echo $table_id; ?>"><?php echo number_format($totalQc, 2); ?></td>
                        <td width="80" align="right"></td>
                        <td width="80" align="right"></td>
                        <td width="80" align="right" id="value_tot_delv_today_quantity<? echo $table_id; ?>"><? echo number_format($tot_delv_today_quantity); ?></td>
                        <td width="80" align="right"></td>
                        <td width="80" align="right" id="value_tot_delv_balance<? echo $table_id; ?>"><? echo number_format($tot_delv_balance); ?></td>
                        <td width="80" align="right" id="value_tot_billQty<? echo $table_id; ?>"><?php echo number_format($totalBillQty); ?></td>
                        <td width="80" align="right" id="value_tot_billAmt<? echo $table_id; ?>"><?php echo number_format($totalBillAmt); ?></td>
                    </tr>
                </table> 
                <?
                $table_id++;
            }
        } ?>
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
    echo "$html**$filename**$table_id";

    $r_id3=execute_query("delete from tmp_poid where userid=$user_id and type in (985,986)");
    if($db_type==0) {
        if($r_id3) {
            mysql_query("COMMIT");  
        }
    }
    if($db_type==2 || $db_type==1 ) {
        if($r_id3) {
            oci_commit($con);  
        }
    }
    disconnect($con);
    die;

    exit();
}

?>