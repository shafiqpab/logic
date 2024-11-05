<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
//$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$imge_arr=return_library_array( "select id,master_tble_id,image_location from common_photo_library",'id','image_location');
$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$company_array=return_library_array( "select id, company_name from lib_company",'id','company_name'); 


if ($action=="load_drop_down_buyer_buyer")
{
    $data=explode("_",$data);
    if($data[1]==1)
    {
        echo create_drop_down( "txt_buyer_buyer_no", 120, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select Buyer --", "", "");
    }
    else
    {
       echo '<input name="txt_buyer_buyer_no" id="txt_buyer_buyer_no" class="text_boxes" style="width:110px"  placeholder="Write">';
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
    elseif ($data[1]==2) 
    {
        echo create_drop_down( "cbo_party_name", 125, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
    }
    else
    {
         echo create_drop_down( "cbo_party_name", 125, $blank_array,"", 1, "-- Select Party --", $selected, "",1,"" );
        // echo "document.getElementById('cbo_party_name').disabled=true";
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
        $year_field="max(year(a.insert_date)) as year"; 
         if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year_job";
    }
    else if($db_type==2) 
    {
        $year_field="max(to_char(a.insert_date,'YYYY')) as year";
         if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year_job";
    }
    else 
    {
        $year_field="";
        $year_field_cond="";
    }
    
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
    
    $sql="SELECT a.subcon_job,a.within_group, a.job_no_prefix_num, $year_field, b.order_no, b.buyer_style_ref,d.buyer_name,b.cust_buyer from  subcon_ord_mst a, subcon_ord_dtls b 
    left join wo_po_break_down c on c.id=b.buyer_po_id and c.status_active=1 and c.is_deleted=0 
    left join wo_po_details_master d on d.id=c.job_id and d.status_active=1 and d.is_deleted=0 
    where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.entry_form=278  and a.company_id='$company_id' $buyer_cond 
    group by a.subcon_job, a.job_no_prefix_num,a.within_group,  b.order_no, b.buyer_style_ref,d.buyer_name,b.cust_buyer  order by a.subcon_job DESC";   
    // echo $sql;
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
                <td width="130">
                <? echo $row[csf('within_group')]==1 ? $buyer[$row[csf('buyer_name')]] : $row[csf('cust_buyer')]; ?>                    
                </td>
                <td width="110"><p><? echo $row[csf('buyer_style_ref')]; ?></p></td>
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
    
    $sql="SELECT distinct a.id,a.within_group, b.job_no_mst as job_no , b.order_no as po_number, a.job_no_prefix_num as  
    job_prefix, $year_field,d.buyer_name,b.cust_buyer from subcon_ord_mst a, subcon_ord_dtls b     
    left join wo_po_break_down c on c.id=b.buyer_po_id and c.status_active=1 and c.is_deleted=0 
    left join wo_po_details_master d on d.id=c.job_id and d.status_active=1 and d.is_deleted=0
     where b.job_no_mst=a.subcon_job and a.id=b.mst_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond and a.entry_form=278  and a.is_deleted =0 
     group by a.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date,d.buyer_name,b.cust_buyer,a.within_group order by a.id DESC";  
    
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
                    <td width="80"><p>
                    <? echo $data[csf('within_group')] ==1 ? $buyer[$data[csf('buyer_name')]] : $data[csf('cust_buyer')]; ?>                        
                    </p></td>
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



if($action=="chemical_cost_popup")
{
    echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
    //echo $batch_id;//die;
    $expData=explode('_',$batch_id);
    ?>
    <fieldset style="width:740px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="11">Chemical Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Product Id</th>
                        <th width="120">Category </th>
                        <th width="200">Item Description</th>
                        <th width="60">UOM</th>
                        <th width="60">Quantity</th>
                        <th width="50">Avg. Rate</th>
                        <th width="">Amount(BDT)</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                
                $i=0;
                

                 $cost_sql="select a.batch_id, b.product_id, b.item_category, b.required_qnty, b.req_qny_edit, c.item_description, c.unit_of_measure, d.batch_lot, d.cons_quantity, d.cons_rate, d.cons_amount 
                 from inv_issue_master a, dyes_chem_issue_dtls b, product_details_master c, inv_transaction d 
                 where a.id=b.mst_id and b.product_id=c.id and d.id=b.trans_id and a.id=d.mst_id and a.entry_form=308 and a.batch_id in($expData[0]) and b.item_category in(5) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";



               //echo $cost_sql; die;
                $batch_dtls_sql= sql_select($cost_sql);
                $tot_qty = 0;
                $tot_amount = 0;
                foreach( $batch_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td align="center" width="80"><? echo $row[csf("product_id")];?> </td>
                        <td align="center" width="120"><? echo $item_category[$row[csf('item_category')]]; ?>&nbsp; </td> 
                        <td align="center" width="200"><p><? echo  $row[csf("item_description")]; ?>&nbsp;</p></td>
                        <td align="center" width="60"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</td>
                        <td align="right" width="60"><? echo $row[csf("cons_quantity")]; ?> &nbsp; </td>
                        <td align="right" width="50"><? echo number_format($row[csf("cons_rate")],3); ?> &nbsp; </td>
                        <td align="right" width=""><? echo number_format($row[csf("cons_amount")],3); ?> &nbsp;</td>
                    </tr>
                    <? 
                    $tot_qty+=$row[csf("cons_quantity")];
                    $tot_amount+=$row[csf("cons_amount")];
                } ?>
                <tr class="tbl_bottom">
                    <td width="30">&nbsp; </td>
                    <td width="80">&nbsp; </td>
                    <td width="120">&nbsp; </td>
                    <td width="200">&nbsp; </td>
                    <td width="60">Total: &nbsp;</td>
                    
                    <td width="60" align="right"><p><? echo $tot_qty; ?> &nbsp; </p></td>
                    <td width="50"  ?> &nbsp; </p></td>
                    <td width="" align="right"><p><? echo number_format($tot_amount,3); ?> &nbsp; </p></td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}


if($action=="dyes_cost_popup")
{
    echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
    //echo $batch_id;//die;
    $expData=explode('_',$batch_id);
    ?>
    <fieldset style="width:740px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="11">Dyes Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Product Id</th>
                        <th width="120">Category </th>
                        <th width="200">Item Description</th>
                        <th width="60">UOM</th>
                        <th width="60">Quantity</th>
                        <th width="50">Avg. Rate</th>
                        <th width="">Amount(BDT)</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                
                $i=0;
                

                 $cost_sql="select a.batch_id, b.product_id, b.item_category, b.required_qnty, b.req_qny_edit, c.item_description, c.unit_of_measure, d.batch_lot, d.cons_quantity, d.cons_rate, d.cons_amount 
                 from inv_issue_master a, dyes_chem_issue_dtls b, product_details_master c, inv_transaction d 
                 where a.id=b.mst_id and b.product_id=c.id and d.id=b.trans_id and a.id=d.mst_id and a.entry_form=308 and a.batch_id in($expData[0]) and b.item_category in(6) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";



               //echo $cost_sql; die;
                $batch_dtls_sql= sql_select($cost_sql);
                $tot_qty = 0;
                $tot_amount = 0;
                foreach( $batch_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td align="center" width="80"><? echo $row[csf("product_id")];?> </td>
                        <td align="center" width="120"><? echo $item_category[$row[csf('item_category')]]; ?>&nbsp; </td> 
                        <td align="center" width="200"><p><? echo  $row[csf("item_description")]; ?>&nbsp;</p></td>
                        <td align="center" width="60"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</td>
                        <td align="right" width="60"><? echo $row[csf("cons_quantity")]; ?> &nbsp; </td>
                        <td align="right" width="50"><? echo number_format($row[csf("cons_rate")],3); ?> &nbsp; </td>
                        <td align="right" width=""><? echo number_format($row[csf("cons_amount")],3); ?> &nbsp;</td>
                    </tr>
                    <? 
                    $tot_qty+=$row[csf("cons_quantity")];
                    $tot_amount+=$row[csf("cons_amount")];
                } ?>
                <tr class="tbl_bottom">
                    <td width="30">&nbsp; </td>
                    <td width="80">&nbsp; </td>
                    <td width="120">&nbsp; </td>
                    <td width="200">&nbsp; </td>
                    <td width="60">Total: &nbsp;</td>
                    
                    <td width="60" align="right"><p><? echo $tot_qty; ?> &nbsp; </p></td>
                    <td width="50"  ?> &nbsp; </p></td>
                    <td width="" align="right"><p><? echo number_format($tot_amount,3); ?> &nbsp; </p></td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}



if($action=="printing_chemical_popup")
{
    echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
    //echo $batch_id;//die;
    $expData=explode('_',$batch_id);
    ?>
    <fieldset style="width:740px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="11">Printing Chemical/ Auxilary Chemicals Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Product Id</th>
                        <th width="120">Category </th>
                        <th width="200">Item Description</th>
                        <th width="60">UOM</th>
                        <th width="60">Quantity</th>
                        <th width="50">Avg. Rate</th>
                        <th width="">Amount(BDT)</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                
                $i=0;
                

                 $cost_sql="select a.batch_id, b.product_id, b.item_category, b.required_qnty, b.req_qny_edit, c.item_description, c.unit_of_measure, d.batch_lot, d.cons_quantity, d.cons_rate, d.cons_amount 
                 from inv_issue_master a, dyes_chem_issue_dtls b, product_details_master c, inv_transaction d 
                 where a.id=b.mst_id and b.product_id=c.id and d.id=b.trans_id and a.id=d.mst_id and a.entry_form=308 and a.batch_id in($expData[0]) and b.item_category in(22,23) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";



               //echo $cost_sql; die;
                $batch_dtls_sql= sql_select($cost_sql);
                $tot_qty = 0;
                $tot_amount = 0;
                foreach( $batch_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td align="center" width="80"><? echo $row[csf("product_id")];?> </td>
                        <td align="center" width="120"><? echo $item_category[$row[csf('item_category')]]; ?>&nbsp; </td> 
                        <td align="center" width="200"><p><? echo  $row[csf("item_description")]; ?>&nbsp;</p></td>
                        <td align="center" width="60"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</td>
                        <td align="right" width="60"><? echo $row[csf("cons_quantity")]; ?> &nbsp; </td>
                        <td align="right" width="50"><? echo number_format($row[csf("cons_rate")],3); ?> &nbsp; </td>
                        <td align="right" width=""><? echo number_format($row[csf("cons_amount")],3); ?> &nbsp;</td>
                    </tr>
                    <? 
                    $tot_qty+=$row[csf("cons_quantity")];
                    $tot_amount+=$row[csf("cons_amount")];
                } ?>
                <tr class="tbl_bottom">
                    <td width="30">&nbsp; </td>
                    <td width="80">&nbsp; </td>
                    <td width="120">&nbsp; </td>
                    <td width="200">&nbsp; </td>
                    <td width="60">Total: &nbsp;</td>
                    
                    <td width="60" align="right"><p><? echo $tot_qty; ?> &nbsp; </p></td>
                    <td width="50"  ?> &nbsp; </p></td>
                    <td width="" align="right"><p><? echo number_format($tot_amount,3); ?> &nbsp; </p></td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}


if($action=="chemical_dyes_print_popup")
{
    echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
    //echo $batch_id;//die;
    $expData=explode('_',$batch_id);
    ?>
    <fieldset style="width:740px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="11">Chemical Dyes and printing Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Product Id</th>
                        <th width="120">Category </th>
                        <th width="200">Item Description</th>
                        <th width="60">UOM</th>
                        <th width="60">Quantity</th>
                        <th width="50">Avg. Rate</th>
                        <th width="">Amount(BDT)</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                
                $i=0;
                

                 $cost_sql="select a.batch_id, b.product_id, b.item_category, b.required_qnty, b.req_qny_edit, c.item_description, c.unit_of_measure, d.batch_lot, d.cons_quantity, d.cons_rate, d.cons_amount 
                 from inv_issue_master a, dyes_chem_issue_dtls b, product_details_master c, inv_transaction d 
                 where a.id=b.mst_id and b.product_id=c.id and d.id=b.trans_id and a.id=d.mst_id and a.entry_form=308 and a.batch_id in($expData[0]) and b.item_category in(5,6,22) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";



               //echo $cost_sql; die;
                $batch_dtls_sql= sql_select($cost_sql);
                $item_wise_arr=array();
                foreach ($batch_dtls_sql as $row) {
                    $item_wise_arr[$row[csf('item_category')]][$row[csf('product_id')]]['batch_id']=$row[csf('batch_id')];
                    $item_wise_arr[$row[csf('item_category')]][$row[csf('product_id')]]['product_id']=$row[csf('product_id')];
                    $item_wise_arr[$row[csf('item_category')]][$row[csf('product_id')]]['item_category']=$row[csf('item_category')];
                    $item_wise_arr[$row[csf('item_category')]][$row[csf('product_id')]]['item_description']=$row[csf('item_description')];
                    $item_wise_arr[$row[csf('item_category')]][$row[csf('product_id')]]['unit_of_measure']=$row[csf('unit_of_measure')];
                    $item_wise_arr[$row[csf('item_category')]][$row[csf('product_id')]]['cons_quantity']+=$row[csf('cons_quantity')];
                    $item_wise_arr[$row[csf('item_category')]][$row[csf('product_id')]]['cons_rate']=$row[csf('cons_rate')];
                    $item_wise_arr[$row[csf('item_category')]][$row[csf('product_id')]]['cons_amount']+=$row[csf('cons_amount')];
                    
                }
                $gross_tot_qty=0;
                $gross_tot_amount=0;
                
                foreach( $item_wise_arr as $item_category_id => $product_arr)
                {
                $tot_qty = 0;
                $tot_amount = 0;
                    foreach ($product_arr as  $row) {
                        
                    
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td align="center" width="80"><? echo $row["product_id"];?> </td>
                            <td align="center" width="120"><? echo $item_category[$row['item_category']]; ?>&nbsp; </td> 
                            <td align="center" width="200"><p><? echo  $row["item_description"]; ?>&nbsp;</p></td>
                            <td align="center" width="60"><? echo $unit_of_measurement[$row["unit_of_measure"]]; ?>&nbsp;</td>
                            <td align="right" width="60"><? echo $row["cons_quantity"]; ?> &nbsp; </td>
                            <td align="right" width="50"><? echo number_format($row["cons_rate"],3); ?> &nbsp; </td>
                            <td align="right" width=""><? echo number_format($row["cons_amount"],3); ?> &nbsp;</td>
                        </tr>
                        <? 
                        $tot_qty+=$row["cons_quantity"];
                        $tot_amount+=$row["cons_amount"];
                    }
                    
                    ?>
                    <tr class="tbl_bottom">
                    <td width="30">&nbsp; </td>
                    <td width="80">&nbsp; </td>
                    <td width="120">&nbsp; </td>
                    <td width="200">&nbsp; </td>
                    <td width="60">Sub Total: &nbsp;</td>
                    
                    <td width="60" align="right"><p><? echo $tot_qty; ?> &nbsp; </p></td>
                    <td width="50"  ?> &nbsp; </p></td>
                    <td width="" align="right"><p><? echo number_format($tot_amount,3); ?> &nbsp; </p></td>
                </tr>
                <?php
                $gross_tot_qty+=$tot_qty;
                $gross_tot_amount+=$tot_amount;
                } 
                ?>
                <tr class="tbl_bottom">
                    <td width="30">&nbsp; </td>
                    <td width="80">&nbsp; </td>
                    <td width="120">&nbsp; </td>
                    <td width="200">&nbsp; </td>
                    <td width="60">Gross Total: &nbsp;</td>
                    
                    <td width="60" align="right"><p><? echo $gross_tot_qty; ?> &nbsp; </p></td>
                    <td width="50"  ?> &nbsp; </p></td>
                    <td width="" align="right"><p><? echo number_format($gross_tot_amount,3); ?> &nbsp; </p></td>
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
    $cbo_company_id=str_replace("'","",$cbo_company_id);
    $job_no=str_replace("'","",$txt_job_no);
    $txt_order_no=str_replace("'","",$txt_order_no);
    $cbo_party_name=str_replace("'","",$cbo_party_name);
    $txt_buyer_buyer_no=str_replace("'","",$txt_buyer_buyer_no);
    $txt_reference_no=str_replace("'","",$txt_reference_no);
    $txt_int_ref=str_replace("'","",$txt_int_ref);
    $txt_batch_no=str_replace("'","",$txt_batch_no);
    $cbo_within_group=str_replace("'","",$cbo_within_group);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
    $cbo_date_type=str_replace("'","",$cbo_date_type);
    $type=str_replace("'","",$type);

    //echo $cbo_date_type; die;

    if ($type==1) {

        // ========================== make query condition ===========================
        if($cbo_within_group!=0) $within_group_cond=" and a.within_group='$cbo_within_group'"; else $within_group_cond="";
        if($cbo_party_name!=0) $buyer_id_cond=" and a.party_id='$cbo_party_name'"; else $buyer_id_cond="";
        if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
        if ($txt_style_ref!='') $style_ref_cond=" and d.style_ref_no like '%$txt_style_ref%'"; else $style_ref_cond="";
        if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
        if($txt_int_ref=='')$int_ref_con="";else $int_ref_con=" and e.grouping like('%".trim($txt_int_ref)."%')";
        if($txt_reference_no!='') $reference_no_cond=" and a.aop_reference like '%$txt_reference_no%'"; else $reference_no_cond="";
        if($txt_batch_no!='') $batch_cond=" and d.batch_no like '%$txt_batch_no%'"; else $batch_cond="";
        if($cbo_within_group==1)
        {
            if($txt_buyer_buyer_no!=0) $buyer_buyer_cond=" and b.buyer_buyer='$txt_buyer_buyer_no'"; else $buyer_buyer_cond="";
        } 
        else
        {
            if ($txt_buyer_buyer_no!='') $buyer_buyer_cond=" and b.buyer_buyer like '%$txt_buyer_buyer_no%'"; else $buyer_buyer_cond="";
        }

        $buyer_po_id_cond = '';
        if($cbo_within_group==1 && $txt_int_ref !='')
        {
            $buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$txt_int_ref%'",'id','id2');
            $buyer_po_id = implode(",", $buyer_po_lib);
            if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_id in($buyer_po_id)";
        }


        $date_cond_batch=""; 
        if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
        {
            if($db_type==0)
            {
                $start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
                $end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
            }
            else if($db_type==2)
            {
                $start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
                $end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
            }
            
            $date_cond_batch=" and d.batch_date between '$start_date' and '$end_date'";
            
        }

        // ============================= main query =============================
       

       $sql="SELECT d.id, d.batch_no, d.batch_date, d.company_id, d.color_id, d.style_ref_no, b.buyer_style_ref, c.batch_qnty, c.po_id, a.job_no_prefix_num, a.subcon_job, a.aop_reference, a.party_id,a.within_group, b.buyer_po_id, b.order_no, e.grouping as int_ref, b.buyer_buyer, f.item_category,b.order_uom
        from pro_batch_create_dtls c, pro_batch_create_mst d, inv_issue_master f, subcon_ord_mst a,subcon_ord_dtls b
        left join wo_po_break_down e on b.buyer_po_id=e.id and e.is_deleted=0 and e.status_active=1 $int_ref_con
        where a.id=b.mst_id and d.id=f.batch_id and b.id=c.po_id and d.id=c.mst_id and d.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 and d.entry_form=281  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $date_cond_batch $buyer_po_id_cond $buyer_buyer_cond $batch_cond $reference_no_cond $order_no_cond $style_ref_cond $job_no_cond $buyer_id_cond $within_group_cond order by d.batch_date";//and f.entry_form=308 //a.subcon_job=b.job_no_mst
         //echo $sql; //die;
        $batch_sql_result=sql_select($sql);
        if(count($batch_sql_result)==0)
        {
            ?>
            <div style="margin:20px auto; width: 90%;color:red;font-size:18px;text-align:center;">            
                <strong>Data not found!</strong> Please try again.
            </div>
            <?
            die();
        }
        

        $data_array = array();
        $batch_array = array();
        foreach ($batch_sql_result as $val) 
        {

            $batch_array[$val[csf("id")]]=$val[csf("id")];

            $data_array[$val[csf("id")]]['batch_no']=$val[csf("batch_no")];
            $data_array[$val[csf("id")]]['batch_date']=$val[csf("batch_date")];
            $data_array[$val[csf("id")]]['color_id']=$val[csf("color_id")];
            $data_array[$val[csf("id")]]['style_ref_no']=$val[csf("style_ref_no")];
            $data_array[$val[csf("id")]]['buyer_style_ref']=$val[csf("buyer_style_ref")];
            $data_array[$val[csf("id")]]['batch_qnty']=$val[csf("batch_qnty")];
            $data_array[$val[csf("id")]]['po_id']=$val[csf("po_id")];
            $data_array[$val[csf("id")]]['subcon_job']=$val[csf("subcon_job")];
            $data_array[$val[csf("id")]]['aop_reference']=$val[csf("aop_reference")];
            $data_array[$val[csf("id")]]['party_id']=$val[csf("party_id")];
            $data_array[$val[csf("id")]]['buyer_po_id']=$val[csf("buyer_po_id")];
            $data_array[$val[csf("id")]]['order_no']=$val[csf("order_no")];
            $data_array[$val[csf("id")]]['int_ref']=$val[csf("int_ref")];
            $data_array[$val[csf("id")]]['buyer_buyer']=$val[csf("buyer_buyer")];
            $data_array[$val[csf("id")]]['item_category']=$val[csf("item_category")];
            $data_array[$val[csf("id")]]['within_group']=$val[csf("within_group")]; 
			$data_array[$val[csf("id")]]['order_uom']=$val[csf("order_uom")];
        }

        $batch_id=implode(',', $batch_array);


        $cost_sql="select a.batch_id, d.item_category, d.cons_quantity, d.cons_rate, d.cons_amount
        from inv_issue_master a, dyes_chem_issue_dtls b, inv_transaction d
        where  a.id=d.mst_id and a.entry_form=308 and d.id=b.trans_id and a.batch_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 "; //b.item_category, dyes_chem_issue_dtls b, //and b.item_category in(5,6,22)

        //echo $cost_sql; //die;

        $batch_sql_dtls=sql_select($cost_sql);
        $batch_wise_cost_array = array();
        foreach ($batch_sql_dtls as $row) 
        {
            $batch_wise_cost_array[$row[csf("batch_id")]][$row[csf("item_category")]]['cost']+=$row[csf("cons_amount")];

        }

        ob_start();
        
        $tbl_width=1760;
        //$col_span=23;
        $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
        $buyer_name_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
        ?>
        <div>
            <table width="<? echo $tbl_width; ?>"  cellspacing="0" cellpadding="0" rules="all" align="center" border="0">
                <tr style="border:none;">
                    <td colspan="20" align="center" style="border:none; font-size:16px; font-weight: bold;">
                    AOP Batch Wise Dyes and Chemical Costing Report.
                 </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold">
                     <? echo $start_date !="" ? "   Date   From ".$start_date."   To   ".$end_date : "" ;?>
                    </td>
                </tr>
            </table>

            <table width="<? echo $tbl_width; ?>"  border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
               <thead>
					<? //$content.=ob_get_flush(); ?>		
                    <!-- <tr>
                    <th colspan="20" align="center"><? //echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
                    </tr> -->
                    <? //ob_start();?>
                    <tr>
                   <th width="30">SL</th>
                   <th width="100">Batch Date</th>
                   <th width="70">Int. Ref.</th> 
                   <th width="70">AOP Ref.</th>
                   <th width="100">Work Order</th>
                   <th width="110">Job No</th>
                   <th width="100">Party</th>
                   <th width="100">Cust Buyer</th>
                   <th width="100">Cust Style</th>
                   <th width="100">Color Name</th>
                   <th width="100">Batch No</th>
                   <th width="80">Batch Qty</th>
                   <th width="80">UOM</th>
                   <th width="120">Total Chem Cost (Tk)</th>
                   <th width="120">Total Dyes Cost (Tk)</th>
                   <th width="120">Printing Chemical/ Auxilary Chemicals Cost (Tk) </th>
                   <th width="120">Total Chem + Dyes + Printing Cost (Tk)</th>
                   <th width="">Cost Per Unit</th>
                   </tr>
               </thead>
            </table>

            <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
                <table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
                    <tbody>
                        <?
                        $process_array=array();
                        $i=1; $k=1;
                        $tot_total_batch_qty   = 0;
                        $tot_total_chem_qty    = 0;
                        $tot_total_dyes_qty    = 0;
                        $tot_total_print_qty   = 0;
                        $tot_total_item_qty    = 0;
                        $tot_total_par_kg_qty  = 0;
                        
                        foreach ($data_array as $batch_id=>$row)
                        {
                            if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
                            $batch_qty=$row['batch_qnty'];
                            
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">  
                                    <td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
                                    <td width="100"><p><? echo change_date_format($row['batch_date']); ?></p></td>
                                    <td width="70"><p><? echo $row['int_ref']; ?></p></td>                             
                                    <td width="70"><p><? echo $row['aop_reference']; ?></p></td>
                                    <td width="100"><p><? echo $row['order_no']; ?></p></td>
                                    <td width="110" align="center"><p><? echo $row['subcon_job']; ?></p></td>
                                    <td width="100"><p><? 
                                    if ($row['within_group']==1) {
                                        $party=$company_array[$row['party_id']];
                                    }else{
                                        $party=$party_arr[$row['party_id']];
                                    }
                                    echo $party; ?></p></td>
                                    <td width="100"><p><?
                                    if ($row['within_group']==1) {
                                        $buyer_buyer=$buyer_name_arr[$row['buyer_buyer']];
                                    }else{
                                        $buyer_buyer=$row['buyer_buyer'];
                                    }
                                    echo $buyer_buyer; ?></p></td>
                                    
                                    <td width="100"><p><? echo $row['buyer_style_ref']; ?></p></td>
                                    <td width="100"><p><? echo $color_arr[$row['color_id']]; ?></p></td>
                                    <td width="100"><p><? echo $row['batch_no']; ?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($batch_qty,2); ?></p></td>
                                    <td width="80" align="right"><p><? echo $unit_of_measurement[$row['order_uom']]; ?></p></td>
                                    <td width="120" align="right">
                                        <p> 
                                            <a href="##" onclick="show_progress_report_details('chemical_cost_popup','<? echo $batch_id; ?>','750px')">
                                                <?
                                                $Tolat_chem_cost=$batch_wise_cost_array[$batch_id][5]['cost'];
                                                echo number_format($Tolat_chem_cost,2); ?>
                                            </a>
                                        </p>
                                    </td>
                                    <td width="120" align="right">
                                        <p>
                                            <a href="##" onclick="show_progress_report_details('dyes_cost_popup','<? echo $batch_id; ?>','750px')">
                                                <?
                                                $Total_dyes_cost=$batch_wise_cost_array[$batch_id][6]['cost'];
                                                echo number_format($Total_dyes_cost,2); ?>                                
                                            </a>
                                        </p>
                                    </td>
                                    <td width="120" align="right">
                                        <p>
                                            <a href="##" onclick="show_progress_report_details('printing_chemical_popup','<? echo $batch_id; ?>','750px')">
                                                <?
                                                $printing_chemical=$batch_wise_cost_array[$batch_id][22]['cost']+$batch_wise_cost_array[$batch_id][23]['cost'];
                                                echo number_format($printing_chemical,2); ?>
                                        
                                            </a>
                                        </p>
                                    </td>
                                    <td width="120" align="right">
                                        <a href="##" onclick="show_progress_report_details('chemical_dyes_print_popup','<? echo $batch_id; ?>','750px')">
                                            <?
                                            $total_chem_dyes_print=($Tolat_chem_cost+$Total_dyes_cost+$printing_chemical);
                                            echo number_format($total_chem_dyes_print,2);?>
                                        </a>
                                    </td>
                                    <td width="" align="right"><?
                                    $cost_par_kg=($Tolat_chem_cost+$Total_dyes_cost+$printing_chemical)/$batch_qty;
                                    echo number_format($cost_par_kg,2); ?></td>

                                </tr>
                                <?
                                $i++;

                                $tot_total_batch_qty    += $row['batch_qnty'];

                                $tot_total_chem_qty    += $Tolat_chem_cost;
                                $tot_total_dyes_qty    += $Total_dyes_cost;
                                $tot_total_print_qty    += $printing_chemical;
                                $tot_total_item_qty    += $total_chem_dyes_print;
                                $tot_total_par_kg_qty    += $cost_par_kg;
                                
                                
                            
                        }
                        ?>
                    </tbody>
                </table>        
            </div>
         
            <table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" >
            
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th> 
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>    
                    <th width="100" align="right">Grand Total :</th>
                    <th width="80" id="batch_qnty" align="right"><? echo number_format($tot_total_batch_qty,3); ?></th> 
                    <th width="80">&nbsp;</th>                  
                    <th width="120" id="chem_qty" align="right"><? echo number_format($tot_total_chem_qty,3); ?></th>
                    <th width="120" id="dyes_qty"><? echo number_format($tot_total_dyes_qty,3); ?></th>
                    <th width="120" id="print_qty"><? echo number_format($tot_total_print_qty,3); ?></th>
                    <th width="120" id="item_qty"><? echo number_format($tot_total_item_qty,3) ?></th>
                    <th width="" id="par_kg_qty" align="right"><? echo number_format($tot_total_item_qty/$tot_total_batch_qty,3); ?></th>
                </tfoot>
            </table>

        </div>
        <?
    }
    else
    {

        // ========================== make query condition ===========================
        if($cbo_within_group!=0) $within_group_cond=" and a.within_group='$cbo_within_group'"; else $within_group_cond="";
        if($cbo_party_name!=0) $buyer_id_cond=" and a.party_id='$cbo_party_name'"; else $buyer_id_cond="";
        if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
        if ($txt_style_ref!='') $style_ref_cond=" and d.style_ref_no like '%$txt_style_ref%'"; else $style_ref_cond="";
        if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
        if($txt_int_ref=='')$int_ref_con="";else $int_ref_con=" and e.grouping like('%".trim($txt_int_ref)."%')";
        if($txt_reference_no!='') $reference_no_cond=" and a.aop_reference like '%$txt_reference_no%'"; else $reference_no_cond="";
        if($txt_batch_no!='') $batch_cond=" and d.batch_no like '%$txt_batch_no%'"; else $batch_cond="";
        if($cbo_within_group==1)
        {
            if($txt_buyer_buyer_no!=0) $buyer_buyer_cond=" and b.buyer_buyer='$txt_buyer_buyer_no'"; else $buyer_buyer_cond="";
        } 
        else
        {
            if ($txt_buyer_buyer_no!='') $buyer_buyer_cond=" and b.buyer_buyer like '%$txt_buyer_buyer_no%'"; else $buyer_buyer_cond="";
        }

        $buyer_po_id_cond = '';
        if($cbo_within_group==1 && $txt_int_ref !='')
        {
            $buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$txt_int_ref%'",'id','id2');
            $buyer_po_id = implode(",", $buyer_po_lib);
            if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_id in($buyer_po_id)";
        }

        if($cbo_date_type==2)
        {
            $date_cond_prod=""; 
            if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
            {
                if($db_type==0)
                {
                    $start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
                    $end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
                }
                else if($db_type==2)
                {
                    $start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
                    $end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
                }
                
                $date_cond_prod=" and h.product_date between '$start_date' and '$end_date'";
                
            }
        }else{

            ?>
            <div style="margin:20px auto; width: 90%;color:red;font-size:18px;text-align:center;">            
                <strong>Show2 Button Only For Data Type Production Date!</strong> Please try again.
            </div>
            <?
            die();
           
        }
        

        // ============================= main query =============================
       

       /*$sql="SELECT d.id, d.batch_no, d.batch_date, d.company_id, d.color_id, d.style_ref_no, b.buyer_style_ref,  a.job_no_prefix_num, a.subcon_job, a.aop_reference, a.party_id,a.within_group, b.buyer_po_id, b.order_no, e.grouping as int_ref, b.buyer_buyer, f.item_category, g.product_date
        from subcon_production_dtls c,subcon_production_mst g, pro_batch_create_mst d, inv_issue_master f, subcon_ord_mst a,subcon_ord_dtls b
        left join wo_po_break_down e on b.buyer_po_id=e.id and e.is_deleted=0 and e.status_active=1 $int_ref_con
        where a.id=b.mst_id and d.id=f.batch_id and g.id=c.mst_id and d.id=c.batch_id and d.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 and d.entry_form=281  and c.status_active=1 and g.is_deleted=0 and g.status_active=1 and d.is_deleted=0 $date_cond_prod $buyer_po_id_cond $buyer_buyer_cond $batch_cond $reference_no_cond $order_no_cond $style_ref_cond $job_no_cond $buyer_id_cond $within_group_cond order by g.product_date";//and f.entry_form=308 //a.subcon_job=b.job_no_mst //and f.entry_form=308 and g.entry_form=291*/


        $sql="SELECT d.id, d.batch_no, d.batch_date, d.company_id, d.color_id, d.style_ref_no, b.buyer_style_ref, c.batch_qnty, c.po_id, a.job_no_prefix_num, a.subcon_job, a.aop_reference, a.party_id,a.within_group, b.buyer_po_id, b.order_no, e.grouping as int_ref, b.buyer_buyer, i.product_id, i.item_category, f.req_no, f.issue_number, h.product_date
        from pro_batch_create_dtls c, pro_batch_create_mst d, subcon_production_dtls g, subcon_production_mst h, inv_issue_master f, dyes_chem_issue_dtls i, subcon_ord_mst a,subcon_ord_dtls b
        left join wo_po_break_down e on b.buyer_po_id=e.id and e.is_deleted=0 and e.status_active=1 $int_ref_con
        where a.id=b.mst_id and d.id=f.batch_id and f.id=i.mst_id and d.id=g.batch_id and h.id=g.mst_id and b.id=c.po_id and d.id=c.mst_id and d.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 and d.entry_form=281 and h.entry_form=291 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $date_cond_prod $buyer_po_id_cond $buyer_buyer_cond $batch_cond $reference_no_cond $order_no_cond $style_ref_cond $job_no_cond $buyer_id_cond $within_group_cond order by d.batch_date";//and f.entry_form=308 //a.subcon_job=b.job_no_mst
         //echo $sql; die;

        $batch_sql_result=sql_select($sql);
        if(count($batch_sql_result)==0)
        {
            ?>
            <div style="margin:20px auto; width: 90%;color:red;font-size:18px;text-align:center;">            
                <strong>Data not found!</strong> Please try again.
            </div>
            <?
            die();
        }
    
        $data_array = array();
        $batch_array = array();
        foreach ($batch_sql_result as $val) 
        {

            $batch_array[$val[csf("id")]]=$val[csf("id")];

            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['batch_no']=$val[csf("batch_no")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['batch_date']=$val[csf("batch_date")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['color_id']=$val[csf("color_id")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['style_ref_no']=$val[csf("style_ref_no")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['buyer_style_ref']=$val[csf("buyer_style_ref")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['product_date']=$val[csf("product_date")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['issue_number']=$val[csf("issue_number")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['subcon_job']=$val[csf("subcon_job")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['aop_reference']=$val[csf("aop_reference")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['party_id']=$val[csf("party_id")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['buyer_po_id']=$val[csf("buyer_po_id")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['order_no']=$val[csf("order_no")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['int_ref']=$val[csf("int_ref")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['buyer_buyer']=$val[csf("buyer_buyer")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['item_category']=$val[csf("item_category")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['product_id']=$val[csf("product_id")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['within_group']=$val[csf("within_group")];
            $data_array[$val[csf("id")]][$val[csf("item_category")]][$val[csf("product_id")]]['req_no']=$val[csf("req_no")];
        }

        $batch_id=implode(',', $batch_array);


        $cost_sql="select a.batch_id, b.product_id, b.item_category, c.item_description, c.unit_of_measure, d.cons_quantity, d.cons_rate, d.cons_amount 
            from inv_issue_master a, dyes_chem_issue_dtls b, product_details_master c, inv_transaction d 
            where a.id=b.mst_id and b.product_id=c.id and d.id=b.trans_id and a.id=d.mst_id and a.entry_form=308 and a.batch_id in($batch_id) and b.item_category in(5,6,22,23) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";


        /*$cost_sql="select a.batch_id, d.item_category, d.cons_quantity, d.cons_rate, d.cons_amount
        from inv_issue_master a, dyes_chem_issue_dtls b, inv_transaction d
        where  a.id=d.mst_id and a.entry_form=308 and d.id=b.trans_id and a.batch_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 "; //b.item_category, dyes_chem_issue_dtls b, //and b.item_category in(5,6,22)*/

        //echo $cost_sql; //die;

        $batch_sql_dtls=sql_select($cost_sql);
        $batch_wise_cost_array = array();
        //$batch_wise_cost_array = array();
        foreach ($batch_sql_dtls as $row) 
        {
            $batch_wise_cost_array[$row[csf("batch_id")]][$row[csf("item_category")]][$row[csf("product_id")]]['cons_amount']+=$row[csf("cons_amount")];
            $batch_wise_cost_array[$row[csf("batch_id")]][$row[csf("item_category")]][$row[csf("product_id")]]['cons_rate']=$row[csf("cons_rate")];
            $batch_wise_cost_array[$row[csf("batch_id")]][$row[csf("item_category")]][$row[csf("product_id")]]['cons_quantity']+=$row[csf("cons_quantity")];
            $batch_wise_cost_array[$row[csf("batch_id")]][$row[csf("item_category")]][$row[csf("product_id")]]['product_id']=$row[csf("product_id")];
            $batch_wise_cost_array[$row[csf("batch_id")]][$row[csf("item_category")]][$row[csf("product_id")]]['item_description']=$row[csf("item_description")];
            $batch_wise_cost_array[$row[csf("batch_id")]][$row[csf("item_category")]][$row[csf("product_id")]]['unit_of_measure']=$row[csf("unit_of_measure")];
            $batch_wise_cost_array[$row[csf("batch_id")]][$row[csf("item_category")]][$row[csf("product_id")]]['item_category']=$row[csf("item_category")];

        }



        ob_start();
        
        $tbl_width=2040;
        //$col_span=23;
        $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
        $buyer_name_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
        ?>
        <div>
            <table width="<? echo $tbl_width; ?>"  cellspacing="0" cellpadding="0" rules="all" align="center" border="0">
                <tr style="border:none;">
                    <td colspan="21" align="center" style="border:none; font-size:16px; font-weight: bold;">
                    AOP Batch Wise Dyes and Chemical Costing Report.                               
                 </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold">
                     <? echo $start_date !="" ? "   Date   From ".$start_date."   To   ".$end_date : "" ;?>
                    </td>
                </tr>
            </table>
            <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
               <thead>
                   <th width="30">SL</th>
                   <th width="100">Batch Date</th>
                   <th width="100">Production Date</th> 
                   <th width="70">Int Ref.</th>
                   <th width="100">Requisition No</th>
                   <th width="100">Issued ID</th>
                   <th width="70">AOP Ref.</th>
                   <th width="100">Work Order</th>
                   <th width="100">Job No</th>
                   <th width="100">Party</th>
                   <th width="100">Cust Buyer</th>
                   <th width="100">Cust Style</th>
                   <th width="100">Color Name</th>
                   <th width="100">Batch No</th>
                   <th width="100">Category</th>
                   <th width="100">Product Id</th>
                   <th width="200">Item Description</th>
                   <th width="70">UOM</th>
                   <th width="100">Issued Qty</th>
                   <th width="100">Avg. Rate</th>
                   <th width="">Amount(BDT)</th>
                   
               </thead>
            </table>
            <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+10; ?>px" id="scroll_body">
                <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" id="table_body_show2">
                <?
                $process_array=array();
                $i=1; $k=1;
                $tot_total_issue_qty    = 0;
                $tot_total_amount       = 0;
                
                foreach ($data_array as $batch_id=>$item_category_arr)
                {
                    foreach ($item_category_arr as $category_id=>$product_id_arr)
                    {
                        foreach ($product_id_arr as $product_id=>$row)
                        {

                            if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
                            //$batch_qty=$row['batch_qnty'];
                            $issue_amount=$batch_wise_cost_array[$batch_id][$category_id][$product_id]['cons_amount'];
                            $issue_qnty=$batch_wise_cost_array[$batch_id][$category_id][$product_id]['cons_quantity'];
                            //$issue_rate=$batch_wise_cost_array[$batch_id][$row['item_category']]['cons_rate'];
                            $issue_rate=$issue_amount/$issue_qnty;

                            
                            $product_id=$batch_wise_cost_array[$batch_id][$category_id][$product_id]['product_id'];
                            $item_category_id=$batch_wise_cost_array[$batch_id][$category_id][$product_id]['item_category'];
                            $item_description=$batch_wise_cost_array[$batch_id][$category_id][$product_id]['item_description'];
                            $uom=$batch_wise_cost_array[$batch_id][$category_id][$product_id]['unit_of_measure'];

                            
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">  
                                    <td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
                                    <td width="100"><p><? echo change_date_format($row['batch_date']); ?></p></td>
                                    <td width="100"><p><? echo change_date_format($row['product_date']); ?></p></td>
                                    <td width="70"><p><? echo $row['int_ref']; ?></p></td>                
                                    <td width="100"><p><? echo $row['req_no']; ?></p></td>            
                                    <td width="100"><p><? echo $row['issue_number']; ?></p></td>                
                                    <td width="70"><p><? echo $row['aop_reference']; ?></p></td>
                                    <td width="100"><p><? echo $row['order_no']; ?></p></td>
                                    <td width="100" align="center"><p><? echo $row['subcon_job']; ?></p></td>
                                    <td width="100"><p><? 
                                    if ($row['within_group']==1) {
                                        $party=$company_array[$row['party_id']];
                                    }else{
                                        $party=$party_arr[$row['party_id']];
                                    }
                                    echo $party; ?></p></td>
                                    <td width="100"><p><?
                                    if ($row['within_group']==1) {
                                        $buyer_buyer=$buyer_name_arr[$row['buyer_buyer']];
                                    }else{
                                        $buyer_buyer=$row['buyer_buyer'];
                                    }
                                     echo $buyer_buyer; ?></p></td>
                                    
                                    <td width="100"><p><? echo $row['buyer_style_ref']; ?></p></td>
                                    <td width="100"><p><? echo $color_arr[$row['color_id']]; ?></p></td>
                                    <td width="100"><p><? echo $row['batch_no']; ?></p></td>
                                    <td width="100"><p><? echo $item_category[$item_category_id]; ?></p></td>
                                    <td width="100"><p><? echo $product_id; ?></p></td>
                                    <td width="200"><p><? echo $item_description; ?></p></td>
                                    <td width="70"><p><? echo $unit_of_measurement[$uom]; ?></p></td>
                                    <td width="100" align="right"><p><? echo number_format($issue_qnty,2); ?></p></td>
                                    <td width="100" align="right"><p><? echo number_format($issue_rate,2); ?></p></td>
                                    <td width="" align="right"><p><? echo number_format($issue_amount,2); ?></p></td>
                                </tr>
                                <?
                                $i++;

                                $tot_total_issue_qty    += $issue_qnty;
                                $tot_total_amount       += $issue_amount; 
                        }
                    }
                }
                ?>
                <tr class="tbl_bottom">
                    <td width="70" colspan="18" align="right">Grand Total:</td>
                    <td width="100" id="issue_qnty" align="right"><? echo number_format($tot_total_issue_qty,3); ?></td>                  
                    <td width="100"></td>
                    <td width="" id="amonut"><? echo number_format($tot_total_amount,3); ?></td>
                </tr>
            </table>        
        </div>
        </div>
        <?

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
    $filename=$user_id."_".$name.".xls";
    echo "$total_data**$filename";
    exit();

   /*  $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w'); 
	$content.=ob_get_flush();   
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit(); */
}

?>