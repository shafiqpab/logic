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
                where a.subcon_job=b.job_no_mst and b.id in($expData[0])   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278   order by  a.job_no_prefix_num, b.order_no";
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

if($action=="material_desc_popup")
{
	echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
    $date_from = $expData[1];
    $date_to = $expData[2];
    if($date_from !="" && $date_to !=""){$dateCond = " and a.subcon_date between '$date_from' and '$date_to'";}
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="6">Material Receive Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="150">Tran. ID</th>
                        <th width="70">Tran. Date</th>
                        <th width="110">Color</th>
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
                $sql= "SELECT a.sys_no,a.prefix_no_num,a.subcon_date,sum(b.quantity) as quantity,c.construction, c.composition, 
                c.gsm, c.grey_dia, c.gmts_color_id, c.item_color_id, c.fin_dia, c.aop_color_id from  sub_material_mst a, sub_material_dtls b, subcon_ord_dtls c where a.id=b.mst_id and b.job_dtls_id=c.id and a.trans_type=1 and a.entry_form=279 and b.job_dtls_id in($expData[0]) $dateCond and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_no,a.prefix_no_num,a.subcon_date,c.construction, c.composition, 
                c.gsm, c.grey_dia, c.gmts_color_id, c.item_color_id, c.fin_dia, c.aop_color_id order by a.sys_no,c.gmts_color_id, c.item_color_id, c.aop_color_id";
                // echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    $material_name= $row[csf('composition')].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="150"><? echo $row[csf("sys_no")];?> </td>
                        <td align="center" width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                        <td align="left" width="110"><? echo $color_arr[$row[csf("gmts_color_id")]]; ?></td>
                        <td width="300"><? echo $material_name; ?> </td> 
                        <td align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
                    </tr>
                    <? 
                    $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="5" align="right">Total: </td>
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
    $date_from = $expData[1];
    $date_to = $expData[2];
    if($date_from !="" && $date_to !=""){$dateCond = " and a.subcon_date between '$date_from' and '$date_to'";}
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="6">Material Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="160">Tran. ID</th>
                        <th width="70">Tran. Date</th>
                        <th width="110">Color</th>
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
				$sql= "SELECT a.sys_no,a.subcon_date, sum(b.quantity) as quantity,c.construction, c.composition, c.gsm, c.grey_dia, c.gmts_color_id, c.item_color_id, c.fin_dia, c.aop_color_id from  sub_material_mst a, sub_material_dtls b, subcon_ord_dtls c where a.id=b.mst_id and b.job_dtls_id=c.id and a.trans_type=2 and a.entry_form=280 and b.job_dtls_id in($expData[0]) $dateCond and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_no,a.subcon_date, c.construction, c.composition, c.gsm, c.grey_dia, c.gmts_color_id, c.item_color_id, c.fin_dia, c.aop_color_id order by a.sys_no,c.grey_dia, c.gmts_color_id, c.item_color_id, c.aop_color_id";
                
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
                        <td width="160"><? echo $row[csf("sys_no")];?> </td>
                        <td align="center" width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                        <td align="left" width="110"><? echo $color_arr[$row[csf("gmts_color_id")]]; ?></td>
                        <td width="300"><? echo $material_name; ?> </td> 
                        <td align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
                    </tr>
                    <? 
                    $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="5" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="batch_qty_popup")
{ 
    echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    $date_from = $expData[1];
    $date_to = $expData[2];
    if($date_from !="" && $date_to !=""){$dateCond = " and c.batch_date between '$date_from' and '$date_to'";}
    ?>
    <fieldset style="width:450px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="5">Batch Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Batch Date</th>
                        <th width="110">Batch No</th>
                        <th width="100">Batch Color</th>
                        <th width="">Batch Qty</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:430px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                //$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                $sql= "SELECT c.batch_date,c.batch_no,c.color_id,sum(b.batch_qnty) as batch_qnty from pro_batch_create_dtls b,pro_batch_create_mst c where c.id=b.mst_id and b.po_id in($expData[0]) $dateCond and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.batch_date,c.batch_no,c.color_id";
                // echo $sql;
                $batch_sql= sql_select($sql);
                $tot_qty = 0;
                foreach( $batch_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    $material_name= $row[csf('composition')].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td align="center" width="80"><? echo change_date_format($row[csf("batch_date")]);?> </td> 
                        <td align="left" width="110"><? echo $row[csf("batch_no")]; ?></td>
                        <td width="100"><? echo $color_arr[$row[csf("color_id")]]; ?> </td> 
                        <td align="right"><? echo number_format($row[csf("batch_qnty")],2); ?></td>
                    </tr>
                    <? 
                    $tot_qty+=$row[csf("batch_qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="4" align="right">Total: </td>
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
    echo load_html_head_contents("Material Issue Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    $date_from = $expData[2];
    $date_to = $expData[3];
    if($date_from !="" && $date_to !=""){$dateCond = " and c.product_date between '$date_from' and '$date_to'";}
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="9">AOP Delivery Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Sys. ID</th>
                        <th width="70">Prod. Date</th>
                        <th width="100">Batch No.</th>
                        <th width="100">Batch Color</th>
                        <th width="100">Service Company</th>
                        <th width="70">Del. Qty</th>
                        <th width="70">Fab. Used. Qty</th>
                        <th>Fabric Description</th>
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
                $sql= "SELECT c.company_id, a.batch_no,a.color_id,d.item_description, c.product_no as sys_no, c.product_date, sum(b.product_qnty) as qnty,sum(b.fabric_used_qnty) as used_qnty from subcon_production_dtls b,subcon_production_mst c,pro_batch_create_dtls d, pro_batch_create_mst a where  d.po_id in($expData[0]) $dateCond and c.id=b.mst_id and c.entry_form in(307) and d.mst_id=b.batch_id and a.id=d.mst_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by c.company_id,a.batch_no,a.color_id,d.item_description, c.product_no, c.product_date";
                
                // echo $sql;
                $material_sql= sql_select($sql);
                $tot_qty = 0;
                $tot_used_qty = 0;
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="100"><? echo $row[csf("sys_no")];?> </td>
                        <td width="70"><? echo change_date_format($row[csf("product_date")]);?> </td> 
                        <td width="100"><? echo $row[csf("batch_no")];?> </td> 
                        <td align="center" width="100"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td width="100"><? echo $company_array[$row[csf("company_id")]]; ?> </td> 
                        <td width="70" align="right"><? echo number_format($row[csf("qnty")]);?> </td> 
                        <td width="70" align="right"> <? echo number_format($row[csf("used_qnty")]);?></td> 
                        <td align="left"><? echo $row[csf("item_description")]; ?></td>
                    </tr>
                    <? 
                    $tot_qty+=$row[csf("qnty")];
                    $tot_used_qty+=$row[csf("used_qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty); ?></p></td>
                    <td align="right"><p><? echo number_format($tot_used_qty); ?></p></td>
                    <td align="right"></td>
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
    echo load_html_head_contents("Material Issue Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    $date_from = $expData[2];
    $date_to = $expData[3];
    if($date_from !="" && $date_to !=""){$dateCond = " and b.bill_date between '$date_from' and '$date_to'";}
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="9">AOP Bill Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Bill No</th>
                        <th width="70">Bill. Date</th>
                        <th width="150">Party</th>
                        <th width="150">Delivery Challan</th>
                        <th width="100">Batch No</th>
                        <th width="100">Batch Color</th>
                        <th width="70">Bill. Qty</th>
                        <th width="70">Bill Amt.</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:auto;" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $del_challan_arr=return_library_array( "select b.id,a.product_no from subcon_production_mst a,subcon_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1",'id','product_no');
                // print_r($del_challan_arr);
                $i=0;
                $sql_bill= "SELECT b.party_id, b.bill_no,b.bill_date, b.company_id,x.batch_no,x.color_id,c.delivery_id,sum(c.delivery_qty) as bill_qnty,sum(c.amount) as amount from pro_batch_create_mst x, pro_batch_create_dtls y, subcon_inbound_bill_mst b,subcon_inbound_bill_dtls c where x.id=y.mst_id and y.po_id=c.order_id and b.id=c.mst_id and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.order_id in($expData[0]) $dateCond and x.id=c.batch_id group by b.party_id, b.bill_no,b.bill_date, b.company_id,x.batch_no,x.color_id,c.delivery_id";
                
                // echo $sql_bill;die();
                $sql_bill_res= sql_select($sql_bill);
                $tot_qty = 0;
                $tot_amt = 0;
                foreach( $sql_bill_res as $row )
                {
                    $party_arr = $expData[1]==1 ? $company_array : $party_arr;
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="100"><? echo $row[csf("bill_no")];?> </td>
                        <td width="70" align="center"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
                        <td width="150"><? echo $party_arr[$row[csf("party_id")]];?> </td> 
                        <td width="150"><? echo $del_challan_arr[$row[csf("delivery_id")]];?> </td> 
                        <td width="100"><? echo $row[csf("batch_no")];?> </td> 
                        <td width="100"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td width="70" align="right"><? echo number_format($row[csf("bill_qnty")]);?> </td> 
                        <td width="70" align="right"><? echo number_format($row[csf("amount")],2); ?></td>
                    </tr>
                    <? 
                    $tot_qty+=$row[csf("bill_qnty")];
                    $tot_amt+=$row[csf("amount")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="7" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty); ?></p></td>
                    <td align="right"><p><? echo number_format($tot_amt,2); ?></p></td>
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
    $date_from = $expData[1];
    $date_to = $expData[2];
    if($date_from !="" && $date_to !=""){$dateCond = " and a.product_date between '$date_from' and '$date_to'";}
    $orderId ="'".implode("','", explode(",", $expData[0]))."'";
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
                $sql= "SELECT a.product_no,a.prefix_no_num,a.product_date,b.batch_id, b.fabric_description,b.product_qnty,b.order_id,c.batch_no from  subcon_production_mst a, subcon_production_dtls b ,pro_batch_create_mst c,pro_batch_create_dtls d where a.id=b.mst_id and c.id=d.mst_id and a.entry_form=291 and b.batch_id=c.id and d.po_id in($orderId) $dateCond and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
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

if($action=="qc_qty_pop_up")
{
    echo load_html_head_contents("QC Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    $orderId ="'".implode("','", explode(",", $expData[0]))."'";
    $date_from = $expData[1];
    $date_to = $expData[2];
    if($date_from !="" && $date_to !=""){$dateCond = " and a.product_date between '$date_from' and '$date_to'";}
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
                $sql= "SELECT a.product_no,a.prefix_no_num,a.product_date,b.batch_id, b.fabric_description,b.product_qnty,b.order_id,c.batch_no from  subcon_production_mst a, subcon_production_dtls b ,pro_batch_create_mst c,pro_batch_create_dtls d where a.id=b.mst_id and c.id=d.mst_id and a.entry_form=294 and b.batch_id=c.id and d.po_id in($orderId) $dateCond and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
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

if($action=="report_generate")
{ 
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
	
	$job_no=str_replace("'","",$txt_job_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$cbo_party_name=str_replace("'","",$cbo_party_name);
	$txt_buyer_po=str_replace("'","",$txt_buyer_po);
    $txt_buyer_style=str_replace("'","",$txt_buyer_style);
	$txt_int_ref=str_replace("'","",$txt_int_ref);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
    // ========================== make query condition ===========================
	if($cbo_within_group!=0) $within_group_cond=" and a.within_group='$cbo_within_group'"; else $within_group_cond="";
	if($cbo_party_name!=0) $buyer_id_cond=" and a.party_id='$cbo_party_name'"; else $buyer_id_cond="";
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
	if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
    if($txt_int_ref=='')$int_ref_con="";else $int_ref_con=" and d.grouping like('%".trim($txt_int_ref)."%')";
    if($txt_buyer_po=='')$buyer_po_con="";else $buyer_po_con=" and b.buyer_po_no like('%".trim($txt_buyer_po)."%')";
	if($txt_buyer_style=='')$buyer_style_con="";else $buyer_style_con=" and b.buyer_style_ref like('%".trim($txt_buyer_style)."%')";

    $buyer_po_id_cond = '';
    if($cbo_within_group==1 && $txt_int_ref !='')
    {
        $buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$txt_int_ref%'",'id','id2');
        $buyer_po_id = implode(",", $buyer_po_lib);
        if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_id in($buyer_po_id)";
    }

	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") 
    {
        $date_cond="";
        $date_cond_prod="";
        $date_cond_mate="";
        $date_cond_batch="";
        $date_cond_bill="";
    }
    else
    {
        $date_cond=" and a.receive_date between $txt_date_from and $txt_date_to";
        $date_cond_prod=" and c.product_date between $txt_date_from and $txt_date_to";
        $date_cond_mate=" and a.subcon_date between $txt_date_from and $txt_date_to";
        $date_cond_batch=" and c.batch_date between $txt_date_from and $txt_date_to";
        $date_cond_bill=" and b.bill_date between $txt_date_from and $txt_date_to";
    	$date_from = str_replace("'", "", $txt_date_from);
    	$date_to = str_replace("'", "", $txt_date_to);
	}
	// ============================= main query =============================
	$job_sql="SELECT a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no, sum(b.order_quantity) as order_quantity,sum(b.amount) as amount,b.order_uom ,b.buyer_style_ref,b.buyer_buyer,a.delivery_date,d.grouping as int_ref,listagg(b.id ,',') within group (order by b.id) AS po_id,listagg(b.buyer_po_id ,',') within group (order by b.buyer_po_id) AS buyer_po_id
    from subcon_ord_mst a, subcon_ord_dtls b
    left join wo_po_break_down d on b.buyer_po_id=d.id and d.is_deleted=0 and d.status_active=1 $int_ref_con 
    where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $order_no_cond $style_ref_cond $job_no_cond  $within_group_cond $buyer_id_cond $date_cond $buyer_po_con $buyer_style_con $buyer_po_id_cond group by a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no,b.order_uom ,b.buyer_style_ref,a.delivery_date,d.grouping,b.buyer_buyer order by job_no_prefix_num";
	
	/*$job_sql="SELECT a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no, sum(b.order_quantity) as order_quantity,sum(b.amount) as amount,b.order_uom ,b.buyer_style_ref,b.buyer_buyer,a.delivery_date,listagg(b.id ,',') within group (order by b.id) AS po_id,listagg(b.buyer_po_id ,',') within group (order by b.buyer_po_id) AS buyer_po_id
    from subcon_ord_mst a, subcon_ord_dtls b  where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $order_no_cond $style_ref_cond $job_no_cond  $within_group_cond $buyer_id_cond $date_cond $buyer_po_con $buyer_style_con $buyer_po_id_cond group by a.id,a.job_no_prefix_num,a.within_group, a.subcon_job, a.party_id, b.order_no,b.order_uom ,b.buyer_style_ref,a.delivery_date,b.buyer_buyer order by job_no_prefix_num";
	*/
   //  echo $job_sql;
    $job_sql_result=sql_select($job_sql);
	
	
    $job_no_array = array();
    $po_array = '';
    foreach ($job_sql_result as $val) 
    {
        $job_no_array[$val['SUBCON_JOB']] = $val['SUBCON_JOB'];
        $po_array .= $val['PO_ID'].",";
    }
    $jobNos = "'".implode("','", $job_no_array)."'";
    $poIds = chop(implode(",", array_unique(explode(",", $po_array))),',');
    // echo $poIds;die();
    // ============================ material issue and receive ========================
    $inv_material_array=array();
    $inv_sql="SELECT a.embl_job_no,a.trans_type,a.entry_form, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type in(1,2) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(279,280) and a.embl_job_no in($jobNos) $date_cond_mate group by a.embl_job_no,a.trans_type,a.entry_form";
    // echo $inv_iss_sql;
    $inv_sql_result=sql_select($inv_sql);
    foreach ($inv_sql_result as $row)
    {
        $inv_material_array[$row[csf('embl_job_no')]][$row[csf('trans_type')]][$row[csf('entry_form')]]=$row[csf('quantity')];
    }
    // ======================= getting batch data =======================
    $batch_qty_array = return_library_array("SELECT a.job_no_mst,sum(b.batch_qnty) as batch_qnty from subcon_ord_dtls a, pro_batch_create_dtls b,pro_batch_create_mst c where a.id=b.po_id and c.id=b.mst_id and c.color_id=a.item_color_id and a.id in($poIds) $date_cond_batch and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst",'job_no_mst','batch_qnty');
    // print_r($batch_qty_array);
    // ======================= getting production,delivery  and qc data =======================
   echo  $sql_prod = "SELECT a.job_no_mst as job_no,c.entry_form,sum(b.product_qnty) as product_qnty,sum(b.reject_qnty) as rej_qty from subcon_ord_dtls a, subcon_production_dtls b,subcon_production_mst c,pro_batch_create_dtls d where a.id=d.po_id and a.id in($poIds) and c.id=b.mst_id and c.entry_form in(291,294,307) and d.mst_id=b.batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $date_cond_prod group by a.job_no_mst,c.entry_form"; // 291=production, 294=qc
   // echo $sql_prod;
    $sql_prod_res = sql_select($sql_prod);
    $product_qty_array = array();
    foreach ($sql_prod_res as $val) 
    {
        $product_qty_array[$val['JOB_NO']][$val['ENTRY_FORM']]['prod'] = $val['PRODUCT_QNTY'];
        $product_qty_array[$val['JOB_NO']][$val['ENTRY_FORM']]['rej'] = $val['REJ_QTY'];
    }
    // ================================= bill data ===========================
    $sql_bill = "SELECT a.job_no_mst as job_no,sum(c.delivery_qty) as BILL_QNTY,sum(c.delivery_qty)*c.rate as BILL_AMNT from subcon_ord_dtls a, subcon_inbound_bill_mst b,subcon_inbound_bill_dtls c where a.id=c.order_id and a.id in($poIds) and c.mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond_bill group by a.job_no_mst,c.rate";
    // echo $sql_bill;
    $sql_bill_res = sql_select($sql_bill);
    $bill_qty_array = array();
    foreach ($sql_bill_res as $val) 
    {
        $bill_qty_array[$val['JOB_NO']]['qty'] += $val['BILL_QNTY'];
        $bill_qty_array[$val['JOB_NO']]['bill_amnt'] += $val['BILL_AMNT'];
    }

	ob_start();
	
	$tbl_width=1590;
	$col_span=23;
	
	$buyer_name_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
    ?>
    <div>
        <table width="<? echo $tbl_width; ?>"  cellspacing="0" cellpadding="0" rules="all" align="center" border="0">
            <tr style="border:none;">
                <td colspan="19" align="center" style="border:none; font-size:16px; font-weight: bold;">
                Date Wise AOP Production Report                               
             </td>
            </tr>
            <tr style="border:none;">
                <td colspan="19" align="center" style="border:none;font-size:12px; font-weight:bold">
                 <? echo $date_from !="" ? "   Date   From ".change_date_format($date_from)."   To   ".change_date_format($date_to) : "" ;?>
                </td>
            </tr>
        </table>
        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
           <thead>
               <th width="30">SL</th>
               <th width="100">Party</th>
               <th width="100">Cust Buyer</th>
               <th width="100">Job No</th>
               <th width="70">Int. Ref.</th>                           
               <th width="100">Buyer Style</th>
               <th width="90">Job Qty</th>
               <th width="100">Job Value</th>
               <th width="60">UOM</th>
               <th width="120">Material Receive</th>
               <th width="120">Material Issue</th>
               <th width="120">Batch Qty.</th>
               <th width="80">Prod. Qty</th>
               <th width="80">QC Qty</th>
               <th width="80">Delivery Qty</th>
               <th width="80">Reject Qty</th>
               <th width="80">Bill Qty</th>
               <th width="80">Bill Amount</th>
           </thead>
        </table>
        <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
            <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" id="table_body">
            <?
            $process_array=array();
            $i=1; $k=1;
            $tot_total_order_qty    = 0;
            $tot_total_order_val    = 0;
            $tot_total_rec_qty      = 0;
            $tot_total_issue_qty    = 0;
            $tot_total_material_blce= 0;
            $tot_total_prod_qty     = 0;
            $tot_total_qc_qty       = 0;
            $tot_total_del_rej      = 0;
            $tot_total_del_qty      = 0;
            $tot_total_bil_qty      = 0;
            $tot_total_bil_amt      = 0;
            foreach ($job_sql_result as $row)
            {

                if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
                $product_qty=0; $del_qty=0; $bill_qty=0; $bill_amnt=0; $pay_rec=0;
                $del_rej    = 0;
                $rec_qty    = $inv_material_array[$row[csf('subcon_job')]][1][279];
                $issue_qty  = $inv_material_array[$row[csf('subcon_job')]][2][280];
                $batch_qty  = $batch_qty_array[$row[csf('subcon_job')]];

                $product_qty= $product_qty_array[$row[csf('subcon_job')]][291]['prod'];
                $qc_qty     = $product_qty_array[$row[csf('subcon_job')]][294]['prod'];
                $del_qty    = $product_qty_array[$row[csf('subcon_job')]][307]['prod'];
                $rej_qty    = $product_qty_array[$row[csf('subcon_job')]][307]['rej'];
                $bill_qty   = $bill_qty_array[$row[csf('subcon_job')]]['qty'];
                $bill_amnt  = $bill_qty_array[$row[csf('subcon_job')]]['bill_amnt'];
                $within_group = $row[csf('within_group')];
                if($row[csf('within_group')]==1)
                {
                    $partyarr=$company_array;
                }
                else
                {
                    $partyarr=$party_arr;
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">	
                    <td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
                    <td width="100"><p><? echo $partyarr[$row[csf('party_id')]]; ?></p></td>
                    <td width="100"><p><? echo $buyer_name_arr[$row[csf('buyer_buyer')]]; ?></p></td>
                    <td width="100" align="center"><p><? echo $row[csf('subcon_job')]; ?></p></td>
                    <td width="70"><p><? echo $row[csf('int_ref')]; ?></p></td>                             
                    <td width="100"><p><? echo $row['BUYER_STYLE_REF']; ?></p></td>
                    <td width="90" align="right">
                        <p> 
                            <a href="##" onclick="show_progress_report_details('order_desc_popup','<? echo $row[csf("po_id")]; ?>','850px')">
                                <? echo number_format($row[csf('order_quantity')]); ?>
                            </a>
                        </p>
                    </td>
                    <td width="100" align="right"><p><? echo number_format($row[csf('amount')],2); ?></p></td>
                    <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                    <td width="120" align="right">
                        <p>
                            <a href="##" onclick="show_progress_report_details('material_desc_popup','<? echo $row[csf("po_id")]."_".$date_from."_".$date_to; ?>','850px')">
                                <? echo number_format($rec_qty); ?>                                
                            </a>
                        </p>
                    </td>
                    <td width="120" align="right">
                        <p>
                            <a href="##" onclick="show_progress_report_details('material_desc_iss_popup','<? echo $row[csf("po_id")]."_".$date_from."_".$date_to; ?>','850px')">
                                <? echo number_format($issue_qty); ?>
                        
                            </a>
                        </p>
                    </td>
                    <td width="120" align="right">
                        <a href="##" onclick="show_progress_report_details('batch_qty_popup','<? echo $row[csf("po_id")]."_".$date_from."_".$date_to; ?>','850px')">
                            <? echo number_format($batch_qty);?>
                        </a>
                    </td>
                    <td width="80" align="right">
                        <p>
                            <a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("po_id")]."_".$date_from."_".$date_to;?>','850px')">
                                <? echo number_format($product_qty); ?>
                        
                            </a>
                        </p>
                    </td>
                    <td width="80" align="right">
                        <p>
                            <a href="##" onclick="show_progress_report_details('qc_qty_pop_up','<? echo $row[csf("po_id")]."_".$date_from."_".$date_to; ?>','850px')">
                                <? echo number_format($qc_qty); ?>
                        
                            </a>
                        </p>
                    </td>
                    <td width="80" align="right">
                        <p>
                            <a href="##" onclick="show_progress_report_details('delivery_qty_pop_up','<? echo $row[csf("po_id")]; ?>_<? echo $row[csf('main_process_id')]; ?>_<? echo $date_from; ?>_<? echo $date_to;?>','850px')">
                                <? echo number_format($del_qty); ?>                        
                            </a>
                        </p>
                    </td>
                    <td width="80" align="right"><p><? echo number_format($rej_qty); ?></p></td>
                    <td width="80" align="right">
                        <p>
                            <a href="##" onclick="show_progress_report_details('bill_qty_pop_up','<? echo $row[csf("po_id")]; ?>_<? echo $within_group."_".$date_from."_".$date_to;?>','850px')">
                                <? echo number_format($bill_qty); ?>                        
                            </a>
                        </p>
                    </td>
                    <td width="80" align="right"><? echo number_format($bill_amnt,2); ?></td>

                </tr>
                <?
                $i++;

                $tot_total_order_qty    += $row[csf('order_quantity')];
                $tot_total_order_val    += $row[csf('amount')];
                $tot_total_rec_qty      += $rec_qty;
                $tot_total_issue_qty    += $issue_qty;
                $tot_total_material_blce+= $mat_blnce_qty;
                $tot_total_prod_qty     += $product_qty;
                $tot_total_qc_qty       += $qc_qty;
                $tot_total_del_rej      += $del_rej;
                $tot_total_del_qty      += $del_qty;                
                $tot_total_bil_qty      += $bill_qty;
                $tot_total_bil_amt      += $bill_amnt;
                }
                ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Grand Total:</td>
                    <td align="right"><? echo number_format($tot_total_order_qty); ?></td>                            
                    <td align="right"><? echo number_format($tot_total_order_val,2); ?></td>
                    <td>&nbsp;</td>
                    <td><? echo number_format($tot_total_rec_qty); ?></td>
                    <td><? echo number_format($tot_total_issue_qty); ?></td>
                    <td><? echo number_format($tot_total_material_blce) ?></td>
                    <td><? echo number_format($tot_total_prod_qty); ?></td>
                    <td><? echo number_format($tot_total_qc_qty); ?></td>
                    <td><? echo number_format($tot_total_del_qty); ?></td>
                    <td><? echo number_format($tot_total_del_rej); ?></td>
                    <td><? echo number_format($tot_total_bil_qty); ?></td>
                    <td><? echo number_format($tot_total_bil_amt,2); ?></td>
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
    exit();
}

?>