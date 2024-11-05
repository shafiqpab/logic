<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
//$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$imge_arr=return_library_array( "select id,master_tble_id,image_location from common_photo_library",'id','image_location');
$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');

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

if($action=="bill_qty_pop_up")
{
    echo load_html_head_contents("Bill Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="7">Bill Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Sys ID</th>
                        <th width="70">Bill Date</th>
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
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
                $i=0;
                $sql = "select distinct b.id as bill_id, a.delivery_qty, a.amount as bill_amount, a.challan_no, b.bill_date, b.bill_no, b.prefix_no_num, d.fabric_description, a.order_id, c.batch_no
                    from subcon_inbound_bill_dtls a, subcon_inbound_bill_mst b, pro_batch_create_mst c, subcon_production_dtls d
                    where a.order_id=$expData[0] and d.batch_id=a.batch_id and d.batch_id=c.id and a.mst_id = b.id and a.batch_id = c.id and a.mst_id = b.id and a.order_id = a.order_id and a.status_active = 1 and b.status_active = 1 and a.status_active = 1";
                // echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
                    <td align="center" width="110"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="70"><? echo $row[csf("batch_no")]; ?></td>
                    <td width="300"><? echo $row[csf("fabric_description")]; ?> </td> 
                    <td align="right"><? echo number_format($row[csf("delivery_qty")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("delivery_qty")];
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

if($action=="delivery_qty_pop_up")
{
    echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="7">Delivery Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Sys ID</th>
                        <th width="70">Delivery Date</th>
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
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
                $i=0;
                $sql= "select a.product_no,a.prefix_no_num,a.product_date,b.batch_id, b.fabric_description,b.product_qnty,b.order_id,c.batch_no from  subcon_production_mst a, subcon_production_dtls b, pro_batch_create_mst c
                where a.id=b.mst_id and a.entry_form=307 and b.batch_id=c.id and b.order_id='$expData[0]' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                // echo $sql;
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


if($action=="report_generate")
{ 
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
	
	$tmpType = 952;
    $job_no=str_replace("'","",$txt_job_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$cbo_party_name=str_replace("'","",$cbo_party_name);
	$txt_buyer_po=str_replace("'","",$txt_buyer_po);
	$txt_buyer_style=str_replace("'","",$txt_buyer_style);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
    $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	if($cbo_within_group!=0) $within_group_cond=" and a.within_group='$cbo_within_group'"; else $within_group_cond="";
	if($cbo_party_name!=0) $buyer_id_cond=" and a.party_id='$cbo_party_name'"; else $buyer_id_cond="";
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
	if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
	if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") {$date_cond="";} else {$date_cond=" and a.delivery_date between $txt_date_from and $txt_date_to";
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	}

    $year_cond = '';
    $insert_year = '';

    if($db_type==0)
    { 
        $year_cond=" and SUBSTRING_INDEX(a.delivery_date, '-', 1)=$cbo_year_selection";
        $insert_year="YEAR(a.delivery_date)";
    }
    else
    {
        $year_cond=" and to_char(a.delivery_date,'YYYY')=$cbo_year_selection";
        $insert_year="to_char(a.delivery_date,'YYYY')";
    }
	
	$inventory_array=array();
	$inventory_sql="select b.job_dtls_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.entry_form=279 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_dtls_id";
	
	$inventory_sql_result=sql_select($inventory_sql);
	foreach ($inventory_sql_result as $row)
	{
		$inventory_array[$row[csf('job_dtls_id')]]['quantity']=$row[csf('quantity')];
	}
	
	$inv_iss_array=array();
	$inv_iss_sql="select b.job_dtls_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=280 group by b.job_dtls_id";
	$inv_iss_sql_result=sql_select($inv_iss_sql);
	foreach ($inv_iss_sql_result as $row)
	{
		$inv_iss_array[$row[csf('job_dtls_id')]]['quantity']=$row[csf('quantity')];
	}
		
	$inventory_ret_array=array();
	$inv_ret_sql="select b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
	
	$inv_ret_sql_result=sql_select($inv_ret_sql);
	foreach ($inv_ret_sql_result as $row)
	{
		$inventory_ret_array[$row[csf('order_id')]]=$row[csf('quantity')];
		
	}
	
	
	 $backup_job_sql = "SELECT a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no, b.buyer_po_no,b.order_quantity,b.amount ,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,b.id as job_dtls_id, d.product_qnty,d.order_id, d.batch_id
    from subcon_ord_mst a, subcon_ord_dtls b, subcon_production_mst c, subcon_production_dtls d
    where  a.id=b.mst_id and to_char(b.id)=d.order_id and c.id=d.mst_id and c.entry_form=307 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and a.entry_form=278 $year_cond $order_no_cond $style_ref_cond $job_no_cond  $within_group_cond $buyer_id_cond $date_cond order by job_no_prefix_num";

    $production_array=array();
    $production_sql="select b.order_id, sum(b.product_qnty) as quantity from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=291 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
    
    $production_sql_result=sql_select($production_sql);
    foreach ($production_sql_result as $row)
    {
        $production_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
        
    }
    unset($production_sql_result);

    $qc_array=array();
    $qc_sql="select b.order_id, sum(b.product_qnty) as quantity from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=294 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
    
    $qc_sql_result=sql_select($qc_sql);
    foreach ($qc_sql_result as $row)
    {
        $qc_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
        
    }
    unset($qc_sql_result);

	//echo "<pre>";
    //print_r($production_array);
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
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		
	}
	unset($po_sql_res);
	/*$job_sql = "SELECT a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no, b.order_quantity,b.amount,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,b.id as job_dtls_id, d.product_qnty, d.order_id, d.batch_id, c.id as prod_id
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_production_mst c, subcon_production_dtls d
	where a.subcon_job=b.job_no_mst and b.buyer_po_id=d.buyer_po_id and c.id=d.mst_id and c.entry_form=307 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and a.entry_form=278 $order_no_cond $style_ref_cond $job_no_cond  $within_group_cond $buyer_id_cond $date_cond order by job_no_prefix_num";	*/
	
	/*
	 $backup_job_sql = "SELECT a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no, b.buyer_po_no,b.order_quantity,b.amount ,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,b.id as job_dtls_id, d.product_qnty,d.order_id, d.batch_id
    from subcon_ord_mst a, subcon_ord_dtls b, subcon_production_mst c, subcon_production_dtls d
    where  a.id=b.mst_id and to_char(b.id)=d.order_id and c.id=d.mst_id and c.entry_form=307 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and a.entry_form=278 $year_cond $order_no_cond $style_ref_cond $job_no_cond  $within_group_cond $buyer_id_cond $date_cond order by job_no_prefix_num";  */

     $delivary_job_sql = "SELECT a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no, b.buyer_po_no,b.order_quantity,b.amount ,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,b.id as job_dtls_id, d.product_qnty,d.order_id, d.batch_id
    from subcon_ord_mst a, subcon_ord_dtls b, subcon_production_mst c, subcon_production_dtls d
    where  a.id=b.mst_id and to_char(b.id)=d.order_id and c.id=d.mst_id and c.entry_form=307 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and a.entry_form=278 $year_cond $order_no_cond $style_ref_cond $job_no_cond  $within_group_cond $buyer_id_cond $date_cond order by job_no_prefix_num";
 	$delivary_job_sql_result = sql_select($delivary_job_sql);

    $delivary_arr = array(); $job_dtls_id_arr = array();
    foreach ($delivary_job_sql_result as $row) 
	{
        $delivary_arr[$row[csf('job_dtls_id')]]['delivary_qnty'] += $row[csf('product_qnty')];
 		$job_dtls_id_arr[$row[csf('job_dtls_id')]]= $row[csf('job_dtls_id')];
		
		 
     }
	 
	 $all_job_ids=implode(",",$job_dtls_id_arr);
	 
	/* echo $all_job_ids; die;
	echo "<pre>";
   print_r($job_dtls_id_arr);*/


      $job_sql = "SELECT a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no, b.buyer_po_no,b.order_quantity, b.amount,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,b.id as job_dtls_id
    from subcon_ord_mst a, subcon_ord_dtls b 
    where  a.id=b.mst_id  and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=278 and b.id in ($all_job_ids) $year_cond $order_no_cond $style_ref_cond $job_no_cond  $within_group_cond $buyer_id_cond $date_cond  order by job_no_prefix_num";//a.subcon_job=b.job_no_mst and b.buyer_po_id=d.buyer_po_id ,, c.id as prod_id 

		//echo $job_sql;
	$job_sql_result=sql_select($job_sql);

    // $po_arr = array();
    $order_id_arr = array();
    foreach ($job_sql_result as $row) 
	{
        // $po_arr[] = $row[csf('buyer_po_id')];
        $order_id_arr[] = $row[csf('job_dtls_id')];
    }
    $order_id_arr = array_unique($order_id_arr);
    $order_id_str = implode(',', $order_id_arr);
    $order_id_arr = explode(',', $order_id_str);

    $user_id = $_SESSION['logic_erp']["user_id"];
    $con = connect();
    foreach($order_id_arr as $orderId) 
	{
        if($orderId!=0) 
		{
            $r_id2=execute_query("insert into tmp_poid(userid, poid, type) values ($user_id,$orderId,$tmpType)");
        }            
    }

    if($db_type==0) {
        if($r_id2) {
            mysql_query("COMMIT");  
        }
    }
    if($db_type==2 || $db_type==1) {
        if($r_id2) {
            oci_commit($con);  
        }
    }

    /*$bill_sql = "select d.id as bill_id, d.delivery_qty, d.amount as bill_amount, d.batch_id, c.buyer_po_id
                from subcon_inbound_bill_dtls d, tmp_poid e, pro_batch_create_dtls c
                where d.order_id=e.poid and e.type=$tmpType and e.userid=$user_id and d.batch_id=c.mst_id";
    $bill_result = sql_select($bill_sql);

    $bill_arr = array();
    foreach ($bill_result as $row) 
	{
        $bill_arr[$row[csf('buyer_po_id')]]['billQty'] = $row[csf('delivery_qty')];
        $bill_arr[$row[csf('buyer_po_id')]]['billAmt'] = $row[csf('bill_amount')];
        $bill_arr[$row[csf('buyer_po_id')]]['bill_id'] = $row[csf('bill_id')];
    }*/
	
	
/*	
  $job_sql = "SELECT a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no, b.buyer_po_no,b.order_quantity as order_quantity, b.amount as amount,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,b.id as job_dtls_id, sum(d.product_qnty) as product_qnty, d.order_id, d.batch_id
    from subcon_ord_mst a, subcon_ord_dtls b, subcon_production_mst c, subcon_production_dtls d
    where  a.subcon_job=b.job_no_mst and to_char(b.id)=d.order_id and c.id=d.mst_id and c.entry_form=307 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and a.entry_form=278 $year_cond $order_no_cond $style_ref_cond $job_no_cond  $within_group_cond $buyer_id_cond $date_cond group by a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no, b.buyer_po_no,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,b.id ,b.order_quantity, d.order_id, d.batch_id  order by job_no_prefix_num";//a.subcon_job=b.job_no_mst and b.buyer_po_id=d.buyer_po_id ,, c.id as prod_id*/ 

  $bill_sql = "select d.id as bill_id, d.delivery_qty, d.amount as bill_amount, d.batch_id, c.buyer_po_no,c.id as job_dtls_id
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

//die;

    ob_start();
	
	$tbl_width=1870;
	$col_span=23;
	
	$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
    ?>
    <div>
        <table width="1000"  cellspacing="0" cellpadding="0" rules="all" align="center" border="0">
            <tr style="border:none;">
                <td colspan="13" align="center" style="border:none; font-size:14px;">
                 <? echo "AOP Work Progress Report"; ?>                                
             </td>
            </tr>
            <tr style="border:none;">
                <td colspan="13" align="center" style="border:none;font-size:12px; font-weight:bold">
                 <? echo change_date_format($date_from)."   To   ".change_date_format($date_to) ;?>
                </td>
            </tr>
        </table>
        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
           <thead>
                <tr>
                <th colspan="21"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
               </tr>
               <th width="30">SL</th>
               <th width="70">Job No</th>
               <th width="100">Party</th>
               <th width="100">Order no</th>
               <th width="100">Buyer PO</th>                            
               <th width="100">Buyer Style</th>
               <th width="90">Order Quantity</th>
               <th width="100">Order Value</th>
               <th width="60">UOM</th>
               <th width="90">Delivery Date</th>
               <th width="60">Days in Hand</th>
               <th width="120">Material Receive</th>
               <th width="120">Material Issue</th>
               <th width="120">Material Balance</th>
               <th width="80">Prod. Qty</th>
               <th width="80">QC Qty</th>
               <th width="80">Delivery Qty</th>
               <th width="80">Reject Qty</th>
               <th width="80">Yet To Delv.</th>
               <th width="80">Bill Qty</th>
               <th>Bill Amount</th>
           </thead>
        </table>
        <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
            <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" id="table_body">
            <?
                $process_array=array();
                $i=1; $k=1;
                foreach ($job_sql_result as $row)
                {

                 if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
                 $product_qty=0;
                // $del_qty=$row[csf('product_qnty')];
				 $del_qty=$delivary_arr[$row[csf('job_dtls_id')]]['delivary_qnty'];
				 
                 $bill_qty= $bill_arr[$row[csf('job_dtls_id')]]['billQty'];
                 $bill_amnt=$bill_arr[$row[csf('job_dtls_id')]]['billAmt'];
				 
                 $bill_id=$bill_arr[$row[csf('job_dtls_id')]]['bill_id'];
                 $pay_rec=0;
                 $del_rej=0;
                 $rec_qty=$inventory_array[$row[csf('job_dtls_id')]]['quantity']-$inventory_ret_array[$row[csf('id')]];
                 $issue_qty=$inv_iss_array[$row[csf('job_dtls_id')]]['quantity'];
                 $product_qty=$production_array[$row[csf('job_dtls_id')]]['quantity'];
                 $qc_qty=$qc_array[$row[csf('job_dtls_id')]]['quantity'];
                 if($row[csf('within_group')]==1)
                 {
                    $partyarr=$company_array;
                 }
                 else
                 {
                    $partyarr=$party_arr;
                 }
                 if($row[csf('within_group')]==1){
                    $buyer_po = $buyer_po_arr[$row[csf('buyer_po_id')]]['po'];
                    $buyer_style = $buyer_po_arr[$row[csf('buyer_po_id')]]['style'];
                 }
                 else{
                    $buyer_po = $row[csf('buyer_po_no')];
                    $buyer_style = $row[csf('buyer_style_ref')];
                 }
                 ?>
                 <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">	
                    <td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
                    <td width="70" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="100"><p><? echo $partyarr[$row[csf('party_id')]]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('order_no')]; ?></p></td>
                    <td width="100"><? echo $buyer_po; ?></td>                              
                    <td width="100"><p><? echo $buyer_style;?></p></td>
                    <td width="90" align="right"><p> <a href="##" onclick="show_progress_report_details('order_desc_popup','<? echo $row[csf("job_dtls_id")]; ?>','850px')">
                            <? echo number_format($row[csf('order_quantity')],2); ?>
                        </a></p></td>
                    <td width="100" align="right"><p><? echo number_format($row[csf('amount')],2); ?></p></td>
                    <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                    <td width="90"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="60" align="center"><? $daysOnHand = datediff("d",date("Y-m-d"),$row[csf('delivery_date')]); echo $daysOnHand; ?> </td>
                    <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_popup','<? echo $row[csf("job_dtls_id")]; ?>','850px')"><? echo number_format($rec_qty,2); ?></a></p></td>
                    <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_iss_popup','<? echo $row[csf("job_dtls_id")]; ?>','850px')"><? echo number_format($issue_qty,2); ?></a></p></td>
                    <td width="120" align="right">
                        <?
                        $mat_blnce_qty=$rec_qty-$issue_qty;
                        echo number_format($mat_blnce_qty,2);
                        ?>
                    </td>
                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("job_dtls_id")];?>','850px')"><? echo number_format($product_qty,2); ?></a></p></td>
                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('qc_qty_pop_up','<? echo $row[csf("job_dtls_id")]; ?>','850px')"><? echo number_format($qc_qty,2); ?></a></p></td>
                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('delivery_qty_pop_up','<?php echo $row[csf("job_dtls_id")]; ?>','850px')"><? echo number_format($del_qty,2); ?></a></p></td>
                    <td width="80" align="right"><p><? echo number_format($del_rej,2); ?></p></td>
                    <td width="80" align="right"><? $yet_to_delv=$row[csf('order_quantity')]-$del_qty; echo  number_format($yet_to_delv,2); ?></td>
                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('bill_qty_pop_up','<?php echo $row[csf("job_dtls_id")]; ?>','850px')"><? echo number_format($bill_qty,2); ?></a></p></td>
                    <td  align="right"><? echo  number_format($bill_amnt,2); ?></td>

                </tr>
                <?
                $i++;
                $tot_order_qty+=$row[csf('order_quantity')];
                $tot_order_val+=$row[csf('amount')];
                $tot_rec_qty+=$rec_qty;
                $tot_issue_qty+=$issue_qty;
                $tot_material_blce+=$mat_blnce_qty;
                $tot_prod_qty+=$product_qty;
                $tot_qc_qty+=$qc_qty;
                $tot_del_rej+=$del_rej;

                if ($type==1)
                {
                   $tot_yet_to_delv+=$yet_to_delv;
                }
                else if ($type==2)
                {
                  $tot_yet_to_bill+=$yet_to_bill;
                }
                $tot_bill_qty+=$bill_qty;
                $tot_bill_amnt+=$bill_amnt;
                $tot_payment_amnt+=$order_wise_payment_received;
                $tot_balance+=$balance;

                if ($cbo_process==4)
                {
                  $tot_batch_qty+=$batch_qty;
                  $tot_dyeing_qty+=$dyeing_qty;

                  $tot_tottal_batch_qty+=$batch_qty;
                  $tot_total_dyeing_qty+=$dyeing_qty;
                }

                $tot_total_order_qty+=$row[csf('order_quantity')];
                $tot_total_order_val+=$row[csf('amount')];
                $tot_total_rec_qty+=$rec_qty;
                $tot_total_issue_qty+=$issue_qty;
                $tot_total_material_blce+=$mat_blnce_qty;
                $tot_total_prod_qty+=$product_qty;
                $tot_total_qc_qty+=$qc_qty;
                $tot_total_del_rej+=$del_rej; 
				$tot_total_del_qty+=$del_qty;

                if ($type==1)
                {
                  $tot_total_yet_to_delv+=$yet_to_delv;
                }
                else if ($type==2)
                {
                  $tot_total_yet_to_bill+=$yet_to_bill;
                }
					$tot_total_bill_qty+=$bill_qty;
					$tot_total_bill_amnt+=$bill_amnt;
					$tot_total_payment_amnt+=$order_wise_payment_received;
					$tot_total_balance+=$balance;
                }
                ?>
                <tr class="tbl_bottom">
                <td colspan="6" align="right">Grand Total:</td>
                <td align="right"><? echo number_format($tot_total_order_qty); ?></td>                            
                <td align="right"><? echo number_format($tot_total_order_val); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><? echo number_format($tot_total_rec_qty); ?></td>
                <td><? echo number_format($tot_total_issue_qty); ?></td>
                <td><? echo number_format($tot_total_material_blce) ?></td>
                <td><? echo number_format($tot_total_prod_qty); ?></td>
                <td><? echo number_format($tot_total_qc_qty); ?></td>
                <td><? echo number_format($tot_total_del_qty); ?></td>
                <td><? echo number_format($tot_total_del_rej); ?></td>
                <td><? echo number_format($tot_total_yet_to_delv); ?></td>
                <td><? echo number_format($tot_total_bill_qty); ?></td>
                <td><? echo number_format($tot_total_bill_amnt); ?></td>
            </tr>
        </table>        
        </div>
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
    echo "$html**$filename"; 

    if ($db_type == 2 || $db_type == 1) 
    {
        $r_id3=execute_query("delete from tmp_poid where userid=$user_id and type=$tmpType");
        
        if($r_id3)
        {
            oci_commit($con);
        }
    }
    disconnect($con);
    exit();
}

?>