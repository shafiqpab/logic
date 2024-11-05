<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');



if ($action=="load_drop_down_location_deli")
{
	echo create_drop_down( "cbo_deli_location_id", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "" );
	exit();
}

if($action=="buyer_party_popup")
{
	echo load_html_head_contents("Buyer Party Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$exdata=explode("__",$data);
	$company_id=$exdata[0];
	$type=$exdata[1];
	?>
	<script>
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
			
			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?
	
	if ($_SESSION['logic_erp']["data_level_secured"]==1)
	{
		if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond="";
	}

    if ($type==1) {
        $sql = "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_id_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
    } else {
        $sql="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_id_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name";
    }
	
	echo create_list_view("tbl_list_search", "Buyer/Party Name", "370","370","260",0, $sql , "js_set_value", "id,buyer_name", "", 1, "0", $arr , "buyer_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	
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
	
    $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond order by a.id desc";	
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
        <? 
        $data_array=sql_select($sql);
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
			<? 
			$i++; 
		} 
		?>
    </table>
    </div>
	<script> setFilterGrid("table_body2",-1); </script>
    <?
	disconnect($con);
	exit();
}

if($action=="style_no_popup")
{
	echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
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
	
    $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond order by a.id desc";	
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
    	<? 
    	$data_array=sql_select($sql);
        $i=1;
		foreach($data_array as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		    ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('cust_style_ref')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td align="center" width="70"><? echo $row[csf('year')]; ?></td>
				<td width="130"><? echo $buyer[$row[csf('party_id')]]; ?></td>
				<td width="110"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('order_no')]; ?></p></td>
			</tr>
			<? 
			$i++; 
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
	
	$sql="select distinct b.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond $year_field_cond and a.is_deleted =0 group by b.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";	
	
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






if($action=="batch_no_popup")
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
	if($db_type==0) $group_by_cond="GROUP BY a.batch_no, a.extention_no"; 
	else if($db_type==2) $group_by_cond=" GROUP BY a.id, a.batch_no, a.extention_no, a.batch_no, a.booking_no, a.color_id, a.batch_weight order by a.batch_no, a.extention_no desc";

	$sql="select a.id, a.batch_no, a.extention_no, a.booking_no, a.color_id, a.batch_weight from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 $group_by_cond ";	

	$arr=array(2=>$color_library);
	
	echo  create_list_view("list_view", "Batch no,Ext,Color,Booking no,Batch weight ", "100,70,100,100,100","520","350",0, $sql, "js_set_value", "id,batch_no,extention_no", "", 1, "0,0,color_id,0,0", $arr , "batch_no,extention_no,color_id,booking_no,batch_weight", "subcon_batch_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,2') ;
	exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
    
    extract(check_magic_quote_gpc( $process ));
    
    $company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
    $location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
    
    $company_id=str_replace("'","",$cbo_company_id);
    $deli_company_id=str_replace("'","",$cbo_deli_company_id);
    $deli_location_id=str_replace("'","",$cbo_deli_location_id);
    $buyer_id=str_replace("'","",$txt_buyer_id);
    $party_id=str_replace("'","",$txt_party_id);
    $prod_category=str_replace("'","",$cbo_prod_category);
    
    $date_from=str_replace("'","",$txt_date_from);
    $date_to=str_replace("'","",$txt_date_to);

	$batch_no = str_replace("'","",$txt_batch_no);
    $job_no   = str_replace("'","",$txt_job_no);
    $internal_ref = str_replace("'","",$txt_internal_ref);
	$txt_style_ref = str_replace("'","",$txt_style_ref);
	$txt_order_no  = str_replace("'","",$txt_order_no);
    $type_id=str_replace("'","",$cbo_type_id);
    if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no like '%$batch_no%'" ;$batch_no_cond2=" and b.batch_id like '%$batch_no%' ";   
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and c.job_no_prefix_num=$job_no";$job_no_cond2=" and d.job_no_mst like '%$job_no%'";
	if ($internal_ref!='')
    { 
        $txt_internal_ref_cond=" and d.grouping='$internal_ref'";
    }else{ 
        $txt_internal_ref_cond="";
    }

	if ($txt_style_ref!='')
    { 
        $style_ref_cond=" and c.style_ref_no='$txt_style_ref'";
        $style_ref_cond2=" and d.order_no='$txt_style_ref'";
    }else{ $style_ref_cond="";};
	if ($txt_order_no==""){ $order_no_cond="";$order_no_cond2="";} else{  $order_no_cond=" and po_number='$txt_order_no'";$order_no_cond2=" and d.order_no='$txt_order_no'";}

    if ($deli_company_id==0) $workingCompany_name_cond2=""; else $workingCompany_name_cond2="  and a.delivery_company='".$deli_company_id."' ";
    if ($deli_location_id==0) $workingCompany_location_cond2=""; else $workingCompany_location_cond2="  and a.delivery_location='".$deli_location_id."' ";
	if ($company_id==0) $companyCond=""; else $companyCond="  and a.company_id=$company_id";


   if ($batch_no!="") 
   {
   		$batch_id=return_field_value("id","pro_batch_create_mst","batch_no='$batch_no' and status_active=1 and is_deleted=0","id");
   		if ($batch_no=="") $batch_no_cond2=""; else $batch_no_cond2=" and b.batch_id='$batch_id'" ;
   }
    



    // $subcon_inbound_bill_arr = array();
    $order_id_arr = array();
    
    ob_start();
    ?>
    <div align="center">
     <fieldset style="width:1370px;">
        <table cellpadding="0" cellspacing="0" width="470">
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="5" style="font-size:20px"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="5" style="font-size:16px"><strong><? echo $company_arr[$company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" width="100%" colspan="5" style="font-size:12px">
                    <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                </td>
            </tr>
        </table>
	    <div>
	        <div style="float: left;">
	            <table width="470" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
	                <thead>
	                    <tr><th colspan="5">Self Order Delivery To Store</th></tr>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="70">Date</th>
	                        <th width="150">Buyer Name</th>
	                        <th width="100">T-Shirt</th>
	                        <th>U-Garments</th>                            
	                    </tr>
	                </thead>
	            </table>
	            <div style="max-height:200px; overflow-y:scroll; width:470px" id="scroll_body_1">
	                <table width="450" border="1" class="rpt_table" rules="all" id="table_body">
	                <?
	                    if($buyer_id=="") $buyer_cond=""; else  $buyer_cond="and c.buyer_name in ($buyer_id)";
	                    if($prod_category==0) $prod_category_cond=""; else  $prod_category_cond="and c.product_category='$prod_category'";
	                    if($party_id=="") $party_cond=""; else  $party_cond="and c.party_id in ($party_id)";
	                    
	                    if($db_type==0)
	                    {
	                        if( $date_from==0 && $date_to==0 ) $self_date_cond=""; else $self_date_cond= " and a.delevery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
	                        if( $date_from==0 && $date_to==0 ) $subcon_date_cond=""; else $subcon_date_cond= " and a.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
	                        if( $date_from==0 && $date_to==0 ) $samplewot_date_cond=""; else $samplewot_date_cond= " and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
	                    }
	                    else if($db_type==2)
	                    {
	                        if( $date_from==0 && $date_to==0 ) $self_date_cond=""; else $self_date_cond= " and a.delevery_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
	                        if( $date_from=='' && $date_to=='' ) $subcon_date_cond=""; else $subcon_date_cond= " and a.delivery_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
	                        if( $date_from=='' && $date_to=='' ) $samplewot_date_cond=""; else $samplewot_date_cond= " and a.receive_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
	                    }
	                
	                  /*  $sql_self="select a.sys_number as transection_no, a.delevery_date, sum(b.current_delivery) as delivery_qty, c.buyer_name, c.product_category, d.grouping
	                    from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, wo_po_details_master c, wo_po_break_down d
	                    where a.company_id='$company_id' and a.id=b.mst_id and d.id=b.order_id and a.entry_form in (54,67) and c.job_no=d.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 $self_date_cond $buyer_cond $prod_category_cond  group by a.sys_number, a.delevery_date, c.buyer_name, c.product_category, d.grouping order by a.delevery_date ASC";*/
						
					     $sql_self="SELECT a.sys_number as transection_no, a.delevery_date as delivery_date, a.id, b.batch_id, c.buyer_name as buyer_id, '' as item_id, e.dia_width_type as width_dia_type, e.color_id, b.current_delivery as delivery_qty, 'self_order' as delivery_data, job_no_mst as job_no, po_number as order_no, b.product_id, c.product_category, c.style_ref_no as style_no, e.receive_qnty, d.grouping, b.order_id
	                        from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, wo_po_details_master c, wo_po_break_down d, pro_finish_fabric_rcv_dtls e 
	                        where  a.id=b.mst_id and d.id=b.order_id $companyCond  $workingCompany_name_cond2 $workingCompany_location_cond2  and a.entry_form in (54,67) and c.job_no=d.job_no_mst and b.sys_dtls_id=e.id and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $self_date_cond $buyer_cond $prod_category_cond 
	                        $job_no_cond $style_ref_cond $order_no_cond  $batch_no_cond2 $txt_internal_ref_cond order by a.delevery_date ASC";


	                  if($type_id==1) 
	                    { 
	                        $sql_self_data=sql_select($sql_self);
	                    }else if($type_id ==0){
	                        $sql_self_data=sql_select($sql_self);
	                    }
	                    $self_po_del_arr=array();
	                    
	                    foreach($sql_self_data as $row)
	                    {
	                        if($row[csf("product_category")]==2) $self_po_del_arr[$row[csf("delivery_date")]][$row[csf("buyer_id")]]['lingerie']+=$row[csf("delivery_qty")];
	                        else $self_po_del_arr[$row[csf("delivery_date")]][$row[csf("buyer_id")]]['other_gmts']+=$row[csf("delivery_qty")];
	                    }
	                    
	                    $i=1;
	                    
	                    foreach($self_po_del_arr as $trns_date=>$date_data)
	                    {
	                        foreach($date_data as $buyer_id=>$buyer_data)
	                        {
	                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                            ?>
	                            <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                                 <td width="30"><? echo $i; ?></td>
	                                 <td width="70"><? echo '&nbsp;'.change_date_format($trns_date); ?></td>
	                                 <td width="150"><? echo $buyer_arr[$buyer_id]; ?></td>
	                                 <td width="100" align="right" ><? echo number_format($buyer_data['other_gmts'],2,'.',','); ?></td>
	                                 <td align="right" ><? echo number_format($buyer_data['lingerie'],2,'.',','); ?></td>
	                            </tr>
	                            <?
	                            $i++;
	                            $tot_othergmts_qty+=$buyer_data['other_gmts'];
	                            $tot_lingerie_qty+=$buyer_data['lingerie'];
	                        }
	                    }
	                    ?>
	                </table>
	            </div>
	            <table width="470" border="1" class="rpt_table" rules="all" id="table_body">
	                <tr class="tbl_bottom">
	                    <td width="30">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
	                    <td width="150">Total:</td>
	                    <td width="100" align="right" ><? echo number_format($tot_othergmts_qty,2,'.',','); ?></td>
	                    <td align="right" ><? echo number_format($tot_lingerie_qty,2,'.',','); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	                </tr>
	            </table>
	        </div> 
	        <div style="float: left; margin-left: 20px;">
	            <table width="370" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
	                <thead>
	                    <tr><th colspan="4">Dyeing Sub-Cont Fabric Deliery to Party</th></tr>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="70">Date</th>
	                        <th width="150">Subcontract Party</th>
	                        <th>Delivery Qty</th>                            
	                    </tr>
	                </thead>
	            </table>
	            <div style="max-height:200px; overflow-y:scroll; width:370px" id="scroll_body_2">
	                <table width="350" border="1" class="rpt_table" rules="all" id="table_body">
	                <?
	                   

					    $sql_subcon="select a.delivery_no as transection_no, a.delivery_date, sum(b.delivery_qty) as delivery_qty, c.party_id from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_mst c, subcon_ord_dtls d where a.company_id='$company_id' and a.id=b.mst_id and c.subcon_job=d.job_no_mst and b.order_id=d.id and a.process_id=4 and a.status_active=1 and a.is_deleted=0 $subcon_date_cond $party_cond $job_no_cond $style_ref_cond2 $order_no_cond2  $batch_no_cond2 group by a.delivery_no, a.delivery_date, c.party_id order by a.delivery_date ASC";
	               
	                    if($type_id==2) 
	                    { 
	                    $sql_sub_data=sql_select($sql_subcon); 
	                    }elseif($type_id==0) {
	                        $sql_sub_data=sql_select($sql_subcon); 
	                    }
	                    
	                    $j=1;
	                    
	                    foreach($sql_sub_data as $row)
	                    {
	                        if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trs_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $j; ?>">
	                             <td width="30"><? echo $j; ?></td>
	                             <td width="70"><? echo '&nbsp;'.change_date_format($row[csf("delivery_date")]); ?></td>
	                             <td width="150"><? echo $buyer_arr[$row[csf("party_id")]]; ?></td>
	                             <td align="right" ><? echo number_format($row[csf("delivery_qty")],2,'.',','); ?></td>
	                        </tr>
	                        <?
	                        $j++;
	                        $tot_subcon_qty+=$row[csf("delivery_qty")];
	                    }
	                    ?>
	                </table>
	            </div>
	            <table width="370" border="1" class="rpt_table" rules="all" id="table_body">
	                <tr class="tbl_bottom">
	                    <td width="30">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
	                    <td width="150">Total:</td>
	                    <td align="right" ><? echo number_format($tot_subcon_qty,2,'.',','); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	                </tr>
	            </table>
	        </div>

	        <div style="float: left; margin-left: 20px;">
	            <table width="370" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
	                <thead>
	                    <tr><th colspan="4">Sample Order Deliery to Party</th></tr>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="70">Date</th>
	                        <th width="150">Buyer</th>
	                        <th>Delivery Qty</th>                            
	                    </tr>
	                </thead>
	            </table>
	            <div style="max-height:200px; overflow-y:scroll; width:370px" id="scroll_body_2">
	                <table width="350" border="1" class="rpt_table" rules="all" id="table_body">
	                <?
	            if ($job_no == "" && $txt_style_ref == "" && $txt_order_no == "") 
	            {
	              	$sql_for_samwout_summary="SELECT  a.recv_number as transection_no, a.receive_date as delivery_date,  b.production_qty as receive_qnty
		            FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c
		            WHERE a.company_id='$company_id' and a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0  
		            and c.booking_without_order = 1 $samplewot_date_cond $batch_no_cond2 order by a.receive_date ASC";
	            }  
	            

				// $sql_for_samwout="SELECT  a.recv_number as transection_no, a.receive_date as delivery_date,  sum(b.production_qty) as receive_qnty
				// FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c
				// WHERE a.company_id='$company_id' and a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0  
				// and c.booking_without_order = 1 $samplewot_date_cond $batch_no_cond2 group by a.recv_number,a.receive_date order by a.receive_date ASC";

				// $sql_for_samwout="SELECT a.id,a.entry_form, a.company_id, a.recv_number as transection_no, a.receive_basis, a.receive_date as delivery_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id as dtls_id,b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type as width_dia_type, b.gsm,b.production_qty as receive_qnty, 'sample_order' as delivery_data, b.width, c.barcode_no, c.roll_id as roll_id, c.roll_no, c.po_breakdown_id,c.is_sales, c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id as dtlsid,c.reprocess,c.roll_id as previous_roll_id,c.is_sales,c.booking_without_order,c.booking_no
				// FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c
				// WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0  
				// and c.booking_without_order = 1 $samplewot_date_cond $batch_no_cond2 order by a.receive_date ASC";

	               //echo  $sql_for_samwout;
	                    if($type_id==3) 
	                    { 
	                    $sql_samwout_summary_data=sql_select($sql_for_samwout_summary); 
	                    }elseif($type_id==0) {
	                        $sql_samwout_summary_data=sql_select($sql_for_samwout_summary); 
	                    }
	                    
	                    $j=1;
	                    
	                    foreach($sql_samwout_summary_data as $row)
	                    {
	                        if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trsw_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trsw_<? echo $j; ?>">
	                             <td width="30"><? echo $j; ?></td>
	                             <td width="70"><? echo '&nbsp;'.change_date_format($row[csf("delivery_date")]); ?></td>
	                             <td width="150"><? //echo $buyer_arr[$row[csf("party_id")]]; ?></td>
	                             <td align="right" ><? echo number_format($row[csf("receive_qnty")],2,'.',','); ?></td>
	                        </tr>
	                        <?
	                        $j++;
	                        $tot_samplewout_qty+=$row[csf("receive_qnty")];
	                    }
	                    ?>
	                </table>
	            </div>
	            <table width="370" border="1" class="rpt_table" rules="all" id="table_body">
	                <tr class="tbl_bottom">
	                    <td width="30">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
	                    <td width="150">Total:</td>
	                    <td align="right" ><? echo number_format($tot_samplewout_qty,2,'.',','); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	                    
	                </tr>
	            </table>
	        </div>
	    </div>

    	<!-- ######################## Details Part Start ################################## -->

        <table width="2600" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" style="float: left; margin-top: 10px;">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="100">Transaction Date</th>
                    <th width="100">Transaction No</th>
                    <th width="100">Delivery Company</th>
                    <th width="100">Delivery Location</th>
                    <th width="100">Batch No</th>
                    <th width="100">Ext. No</th>
                    <th width="100">Buyer/Party</th>
                    <th width="100">Job No</th>
                    <th width="100">Cust Buyer</th>
                    <th width="100">Cust Style Ref</th>
                    <th width="100">Internal Ref.</th>
                    <th width="110">F.Booking No</th>
                    <th width="100">Style Ref.</th>
                    <th width="100">Order No</th>
                    <th width="100">Receive Challan</th>
                    <th width="200">Fabric Type</th>
                    <th width="100">Dia/Width Type</th>
                    <th width="100">Color Name</th>
                    <th width="100">Color Range</th>
                    <th width="100">Production Qty(Finish)</th>
                    <th width="100">Production Qty(Grey)</th>
                    <th width="100">Delivery to Store Qty</th>
                    <th width="100">Delivery to Party Qty(Finish)</th>
                    <th>Delivery to Party Qty(Grey)</th>                           
                </tr>
            </thead>
        </table>

        <div style="max-height:400px; overflow-y:scroll; width:2618px; float: left;" id="scroll_body_3">
            <table width="2600" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="list_views" style="float: left">
                <tbody>
                    <?  
                    //$tot_delivery_to_store_qty=0;
                    //$tot_delivery_to_party_qty=0;
                    //$tot_delivery_to_party_qty=array();
                    //$tot_delivery_to_store_qty=array();

                    $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
                    $fabric_desc_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
                    
                    $batch_sql="SELECT a.id, a.batch_no, a.booking_no,a.color_range_id, b.id as item_id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$company_id'  and a.entry_form in (36,0) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                    $batch_sql_result=sql_select($batch_sql);
                    foreach($batch_sql_result as $row)
                    {
                        $batch_array[$row[csf('id')]][$row[csf('item_id')]]=$row[csf('item_description')];
                        $batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
                        $batch_array[$row[csf('id')]]['ext_no']=$row[csf('extention_no')];
                        $batch_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
                        $batch_array[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
                    }

                    //----------------------------  This is for Subcontract   ---------------------------

                    $prod_sql="SELECT b.batch_id, b.cons_comp_id, sum(b.product_qnty) as product_qnty 
                    from subcon_production_mst a, subcon_production_dtls b 
                    where a.entry_form=292 and a.id=b.mst_id and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.product_type=4 group by  b.batch_id, b.cons_comp_id";
                    $prod_data_sql=sql_select($prod_sql);
                    foreach($prod_data_sql as $row)
                    {
                        $production_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]=$row[csf('product_qnty')];
                    }
                    //Receive ID
                    $sql= "select a.sys_no, listagg(b.order_id,',') within group (order by b.order_id) as order_id  from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c , subcon_ord_mst d where a.id=b.mst_id  and c.id=b.order_id and c.id=b.order_id and c.job_no_mst=d.subcon_job and c.mst_id=d.id and a.entry_form=288 and a.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 $party_cond $job_no_cond $style_ref_cond2 $order_no_cond2 group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active order by a.id DESC ";
                    $data_sql=sql_select($sql);
                    foreach($data_sql as $row)
                    {
                        $receive_array[$row[csf('order_id')]]=$row[csf('sys_no')];
                    }
                    //echo '<pre>';print_r($receive_array);die;
                    //echo $sql; 
                    // Subcontact order sql-----------------------------------
                    $sql_subcontract="SELECT a.delivery_no as transection_no, a.delivery_date, a.id, b.batch_id, c.party_id as buyer_id, b.item_id, b.width_dia_type, b.color_id, b.delivery_qty as delivery_qty, 'subcontract' as delivery_data, c.subcon_job as job_no, d.order_no, '' as product_id, '' as product_category, '' as style_no, b.gray_qty as grey_used_qty, b.order_id, '' as production_qty,d.cust_buyer as cust_buyer, d.cust_style_ref as cust_style_ref, a.company_id as delivery_company,a.location_id as delivery_location
                    from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_mst c, subcon_ord_dtls d
                    where a.company_id='$company_id' and a.id=b.mst_id and c.subcon_job=d.job_no_mst and b.order_id=d.id and a.process_id=4 and a.status_active=1 and a.is_deleted=0 $subcon_date_cond $party_cond $job_no_cond $style_ref_cond2 $order_no_cond2  $batch_no_cond2 order by b.batch_id, a.delivery_date desc";
                   	 // echo $sql_subcontract;die;

                    //-------------------------------  This is for Self   ------------------------------

                    /*$self_prod_sql="SELECT sum(b.receive_qnty) as qnty, b.batch_id, b.prod_id 
                    from inv_receive_master a, pro_finish_fabric_rcv_dtls b 
                    where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and a.company_id='$company_id' group by  b.batch_id, b.prod_id";
                    $self_prod_data=sql_select($self_prod_sql);
                    foreach($self_prod_data as $row)
                    {
                        $self_production_qty_array[$row[csf('batch_id')]][$row[csf('prod_id')]]=$row[csf('qnty')];
                    }*/

                    /* $sql_for_self="SELECT a.sys_number as transection_no, a.delevery_date as delivery_date, a.id, b.batch_id, c.buyer_name as buyer_id, '' as item_id, e.dia_width_type as width_dia_type, e.color_id, b.current_delivery as delivery_qty, 'self_order' as delivery_data, job_no_mst as job_no, po_number as order_no, b.product_id, c.product_category, c.style_ref_no as style_no, e.receive_qnty, f.grey_used_qty, d.grouping, b.order_id, f.quantity as production_qty
                    from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, wo_po_details_master c, wo_po_break_down d, pro_finish_fabric_rcv_dtls e, order_wise_pro_details f
                    where a.company_id='$company_id' and a.id=b.mst_id and d.id=b.order_id and f.po_breakdown_id=d.id and e.id=f.dtls_id $job_no_cond $style_ref_cond $order_no_cond  $batch_no_cond2 and a.entry_form in (54,67) and c.job_no=d.job_no_mst and b.sys_dtls_id=e.id and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $self_date_cond $buyer_cond $prod_category_cond 
                    order by a.delevery_date ASC";
                    */

                    // Self Order sql--------------------------
                    $sql_for_self="SELECT a.sys_number as transection_no, a.delevery_date as delivery_date, a.id, a.knitting_source, a.knitting_company as delivery_company, a.delivery_location, b.batch_id, c.buyer_name as buyer_id, '' as item_id, e.dia_width_type as width_dia_type, e.color_id, b.current_delivery as delivery_qty, 'self_order' as delivery_data, job_no_mst as job_no, po_number as order_no, b.gsm, b.dia, b.product_id, b.determination_id as fabric_description_id, c.product_category, c.style_ref_no as style_no, e.receive_qnty, 0 as grey_used_qty, d.grouping, b.order_id, 0 as production_qty,e.id as edtls_id,'' as cust_buyer, '' as cust_style_ref
                    from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, wo_po_details_master c, wo_po_break_down d, pro_finish_fabric_rcv_dtls e
                    where  a.id=b.mst_id and d.id=b.order_id $companyCond  $workingCompany_name_cond2 $workingCompany_location_cond2 $job_no_cond $txt_internal_ref_cond $style_ref_cond $order_no_cond  $batch_no_cond2 and a.entry_form in (54,67) and c.job_no=d.job_no_mst and b.sys_dtls_id=e.id and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $self_date_cond $buyer_cond $prod_category_cond 
                    order by a.delevery_date ASC";
                    	//  echo $sql_for_self;
                    $all_deter_cond="";
                    $all_deter_arr = array_filter($all_deter_arr);
                    if(count($all_deter_arr)>0)
                    {
                        $deter_cond="";
                        $all_deter_nos=implode(",",$all_deter_arr);
                        if($db_type==2 && count($all_deter_arr)>999)
                        {
                            $all_deter_chunk=array_chunk($all_deter_arr,999) ;
                            foreach($all_deter_chunk as $chunk_arr)
                            {
                                $chunk_arr_value=implode(",",$chunk_arr);
                                $deter_cond.=" a.id in($chunk_arr_value) or ";
                            }

                            $all_deter_cond.=" and (".chop($deter_cond,'or ').")";
                        }
                        else
                        {
                            $all_deter_cond=" and a.id in($all_deter_nos)";
                        }
                    }

                    $composition_arr = array();
                    $constructtion_arr = array();
                    $sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $all_deter_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                    $data_array_deter = sql_select($sql_deter);
                    foreach ($data_array_deter as $row) {
                        $constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
                        $composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
                    }
                    unset($data_array_deter);

                    // Sample Order sql-------------------------------------
                    if ($job_no == "" && $txt_style_ref == "" && $txt_order_no == "") 
	            	{
	                    $sql_for_samwout="SELECT a.id,b.buyer_id,a.entry_form, a.company_id, a.recv_number as transection_no, a.receive_basis, a.receive_date as delivery_date, a.booking_no, a.knitting_source, a.knitting_company as delivery_company, a.knitting_location_id, b.id as dtls_id,b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type as width_dia_type, b.gsm,b.production_qty as receive_qnty, 'sample_order' as delivery_data, b.width, c.barcode_no, c.roll_id as roll_id, c.roll_no, c.po_breakdown_id,c.is_sales, c.qnty as delivery_qty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id as dtlsid,c.reprocess,c.roll_id as previous_roll_id,c.is_sales,c.booking_without_order,c.booking_no,,'' as cust_buyer, '' as cust_style_ref
	                    FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c
	                    WHERE a.company_id='$company_id' and a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0  
	                    and c.booking_without_order = 1 $samplewot_date_cond $batch_no_cond2 order by a.receive_date ASC";
                	}
                     //echo $sql_for_samwout;

                    // echo $sql_subcontract.'<br>'.$sql_for_self.'<br>'.$sql_for_samwout;die;

                    $sql_self_data=sql_select($sql_for_self);
                    $sql_subcontract_data=sql_select($sql_subcontract);
                    $sql_samwout_data=sql_select($sql_for_samwout);

                    $po_break_down_ids=array();
                    $edtls_ids=array();

                    foreach ($sql_self_data as $row) {
                    	
                    	array_push($po_break_down_ids, $row[csf('order_id')]);
                    	array_push($edtls_ids, $row[csf('edtls_id')]);
                    }

                    $po_id_cond_orderwise=where_con_using_array($po_break_down_ids,0,"f.po_breakdown_id");
                    $edtls_id_cond_orderwise=where_con_using_array($edtls_ids,0,"f.dtls_id");

                    $order_wise_po_res=sql_select("SELECT sum(f.grey_used_qty) as grey_used_qty, sum(f.returnable_qnty) as returnable_qnty, sum(f.quantity)  as production_qty,f.po_breakdown_id,f.dtls_id
                    from order_wise_pro_details f
                    where f.status_active=1 and f.is_deleted=0  $po_id_cond_orderwise $edtls_id_cond_orderwise group by f.po_breakdown_id,f.dtls_id");
                  
                    $order_wise_po_data=array();
                    foreach ($order_wise_po_res as $row) 
                    {
                    	$order_wise_po_data[$row[csf('dtls_id')]][$row[csf('po_breakdown_id')]]['grey_used_qty']+=$row[csf('grey_used_qty')];
                    	$order_wise_po_data[$row[csf('dtls_id')]][$row[csf('po_breakdown_id')]]['production_qty']+=$row[csf('production_qty')]+$row[csf('returnable_qnty')];
						//returnable_qnty added by CRM : ISD-23-02538
                    }

                    if($type_id==2) 
                    {  $result=$sql_subcontract_data;
                    }elseif($type_id==1){

                        $result=$sql_self_data;

                    }elseif($type_id==3){

                        $result=$sql_samwout_data;

                    }else{

                        $result = array_merge($sql_subcontract_data, $sql_self_data, $sql_samwout_data);
                    }

                    // if(count($sql_self_data)>0 && count($sql_subcontract_data>0))
                    // {
                    //     $result = array_merge($sql_subcontract_data, $sql_self_data);
                    // }
                    // else if (count($sql_subcontract_data)>0)
                    // {
                    //     $result=$sql_subcontract_data;
                    // }
                    // else
                    // {
                    //     $result=$sql_self_data;
                    // }

                    foreach($result as $row) {
                        $order_id_arr[] = $row[csf('order_id')];
                    }

                    $order_id_arr = array_unique($order_id_arr);

                    $user_id = $_SESSION['logic_erp']["user_id"];
                    $con = connect();
                    foreach($order_id_arr as $ord_id) {
                        if($ord_id!=0) {
                            /*echo "insert into tmp_poid (userid, poid, type) values ($user_id,$ord_id,953)";
                            echo '<br>';*/
                            $r_id2=execute_query("insert into tmp_poid(userid, poid, type) values ($user_id,$ord_id,953)");
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

                    /*$order_ids_str = rtrim($order_ids_str, ',');*/
                    /*$subcon_inbound_data = sql_select("select a.bill_no, b.order_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, tmp_poid c where a.process_id=4 and a.id=b.mst_id and b.order_id=c.poid and a.status_active=1 and b.status_active=1");

                    foreach ($subcon_inbound_data as $row) {
                        $subcon_inbound_bill_arr[$row[csf('order_id')]]['bill_no'] = $row[csf('bill_no')];
                    }*/

                    $r_id3=execute_query("delete from tmp_poid where userid=$user_id and type='953'");
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

                    //echo "<pre>";print_r($result);die;
                    $j=1;
                    foreach($result as $row)
                    {
                        if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        if($type_id!=2){  
                            if ($row[csf("knitting_source")]==1) 
                        {
                        	$delivery_company=$company_arr[$row[csf("delivery_company")]];
                        }
                        else
                        {
                        	$delivery_company=$buyer_arr[$row[csf("delivery_company")]];
                        }
                    }else{
    
                        $delivery_company=$company_arr[$row[csf("delivery_company")]];
                        }
                        
                        ?>
                        <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $j; ?>">
                            <td width="50"  align="center" style="word-wrap: break-word; word-break: break-all;"><p><? echo $j; ?>   </p> </td>
                            <td width="100" align="center" style="word-wrap: break-word; word-break: break-all;" ><p><? echo '&nbsp;'.change_date_format($row[csf("delivery_date")]); ?> </p> </td>
                            <td width="100"  align="center" style="word-wrap: break-word; word-break: break-all;"><p>  <? echo $row[csf("transection_no")]; ?> </p> </td>
                            <td width="100"  align="center" style="word-wrap: break-word; word-break: break-all;"><p>  <? echo $delivery_company; ?> </p> </td>
                            <td width="100"  align="center" style="word-wrap: break-word; word-break: break-all;"><p> 
                             <? echo $location_arr[$row[csf("delivery_location")]]; ?> </p> </td>
                            <td width="100"  align="center" style="word-wrap: break-word; word-break: break-all;"><p>
                            <?
                            //$batch = return_field_value("batch_no", "pro_batch_create_mst", " id=" . $row[csf('batch_id')] . "");
                            //echo $batch;
                            echo $batch_array[$row[csf("batch_id")]]["batch_no"]; ?> </p> </td>
                            <td width="100"  align="center" style="word-wrap: break-word; word-break: break-all;" ><p>  <? echo $batch_array[$row[csf("batch_id")]]["ext_no"]; ?> </p> </td>
                            <td width="100" align="center" style="word-wrap: break-word; word-break: break-all;" ><p>  <? echo $buyer_arr[$row[csf("buyer_id")]]; ?> </p> </td>
                            <td width="100" align="center" style="word-wrap: break-word; word-break: break-all;" ><p>  <? echo $row[csf("job_no")]; ?> </p> </td>
                            <td width="100" align="center" style="word-wrap: break-word; word-break: break-all;" ><p>  <? echo $row[csf("cust_buyer")]; ?> </p> </td>
                            <td width="100" align="center" style="word-wrap: break-word; word-break: break-all;" ><p>  <? echo $row[csf("cust_style_ref")]; ?> </p> </td>
                            <td width="100" align="center" style="word-wrap: break-word; word-break: break-all;"><p><?php echo $row[csf("grouping")]; ?></p></td>
                            <td width="110" align="center" style="word-wrap: break-word; word-break: break-all;" ><p><?
                             if($row[csf("delivery_data")]=="self_order")
                             {
                                echo $batch_array[$row[csf('batch_id')]]['booking_no'];

                             }else if($row[csf("delivery_data")]=="sample_order"){
                                echo $batch_array[$row[csf('batch_id')]]['booking_no'];
                             }
                             
                             
                             
                             
                            //   if($type_id==1) echo $batch_array[$row[csf('batch_id')]]['booking_no'];
                             
                             ?></p></td>
                            <td width="100"  align="center" style="word-wrap: break-word; word-break: break-all;" ><p>
                            <? 
                            //  if($type_id==2)
                             if($row[csf("delivery_data")]=="subcontract") 
                            
                            { 
                                echo $row[csf("order_no")];
                            }
                            else
                            { 
                                echo $row[csf("style_no")];
                            } 
                            ?></p></td>
                            <td width="100" align="center" style="word-wrap: break-word; word-break: break-all;" ><p><? echo $row[csf("order_no")]; ?> </p> </td>
                            <td width="100" align="center" style="word-wrap: break-word; word-break: break-all;" ><p><? echo  $receive_array[$row[csf('order_id')]]; ?> </p> </td>
                            <td width="200" title="Detar Id: <? echo $row[csf("fabric_description_id")].' Product ID: '.$row[csf("product_id")]; ?>"  align="center" style="word-wrap: break-word; word-break: break-all;" ><p>
                            <? 
                            // if($type_id==2)
                            if($row[csf("delivery_data")]=="subcontract") 
                            { 
                                echo $batch_array[$row[csf("batch_id")]][$row[csf("item_id")]];
                            }
                            else
                            { 
                                echo $constructtion_arr[$row[csf("fabric_description_id")]].','.$composition_arr[$row[csf("fabric_description_id")]].','.$row[csf("gsm")].','.$row[csf("dia")];
                                // echo $fabric_desc_arr[$row[csf("product_id")]];
                            }
                            ?></p></td>
                            <td width="100"  align="center" style="word-wrap: break-word; word-break: break-all;" ><p><? echo $fabric_typee[$row[csf("width_dia_type")]]; ?> </p> </td>
                            <td width="100" align="center" style="word-wrap: break-word; word-break: break-all;" ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p> </td>
                            <td width="100"  align="center" style="word-wrap: break-word; word-break: break-all;" ><p>  <? echo $color_range[$batch_array[$row[csf("batch_id")]]["color_range_id"]]; ?> </p> </td>
                            <td width="100"  align="right">   
                            <?php
                                // echo $row[csf('production_qty')];
                                // $tot_receive_qnty += $row[csf('production_qty')];
                                if($row[csf("delivery_data")]=="subcontract")
                                {
                                    echo number_format($row[csf("delivery_qty")],2); 
                                    $tot_receive_qnty += $row[csf("delivery_qty")];
                                    // echo number_format($production_qty_array[$row[csf("batch_id")]][$row[csf("item_id")]],2); 
                                    // $tot_receive_qnty += $production_qty_array[$row[csf("batch_id")]][$row[csf("item_id")]];
                                }
                                else
                                {
                                    //echo number_format($self_production_qty_array[$row[csf("batch_id")]][$row[csf("product_id")]],2);
                                    echo number_format($row[csf("receive_qnty")],2);
                                    $tot_receive_qnty += $row[csf("receive_qnty")];
                                }
                            ?> 
                            </td>
                            <td width="100" align="right" title="<? echo $row[csf('edtls_id')].']['.$row[csf('order_id')]; ?>">
                            <? 
                            // if($type_id==1)
                            if($row[csf("delivery_data")]=="self_order")
                            {
                                $grey_used_qty=$order_wise_po_data[$row[csf('edtls_id')]][$row[csf('order_id')]]['production_qty'];
                    			
                                //echo number_format($row[csf("grey_used_qty")],2);
                                echo number_format($grey_used_qty,2);
                               // $tot_grey_used_qty += $row[csf("grey_used_qty")];
                                $tot_grey_used_qty += $grey_used_qty;
                            }
                            ?></td>
                            <td width="100" align="right">  
                            <?
                            // if($type_id==1)
                            if($row[csf("delivery_data")]=="self_order")
                            {
                                echo number_format($row[csf("delivery_qty")],2); 
                                $tot_delivery_to_store_qty += $row[csf("delivery_qty")];
                            }else if($row[csf("delivery_data")]=="sample_order"){

                                echo number_format($row[csf("delivery_qty")],2); 
                                $tot_delivery_to_store_qty += $row[csf("delivery_qty")];
                            }
                            ?>  
                            </td>
                            <td  width="100" align="right">
                            <?
                            // if($type_id==2)
                            if($row[csf("delivery_data")]=="subcontract")
                            {
                                echo number_format($row[csf("delivery_qty")],2); 
                                $tot_delivery_to_party_qty+=$row[csf("delivery_qty")];
                            }
                            ?>
                            </td>
                            <td align="right">
                            <? 
                            // if($type_id==2)
                             if($row[csf("delivery_data")]=="subcontract")
                             {
                                echo number_format($row[csf("grey_used_qty")],2); 
                                $tot_delivery_to_party_grey_qty+=$row[csf("grey_used_qty")];
                            } 
                            ?></td>
                        </tr>
                        <?
                        $j++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <table width="2600" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" style="float: left;">
            <tr class="tbl_bottom">
                <td width="50"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <!-- <td width="100"></td> -->
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="200"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100">Total: </td>
                <td width="100" style="word-wrap: break-word; word-break: break-all;" align="right"> 
                    <? echo number_format($tot_receive_qnty,2) ?> 
                </td> 
                <td width="100" style="word-wrap: break-word; word-break: break-all;" align="right"> 
                    <? echo number_format($tot_grey_used_qty,2) ?> 
                </td>
                <td width="100" style="word-wrap: break-word; word-break: break-all;" align="right"> 
                    <? echo number_format($tot_delivery_to_store_qty,2) ?> 
                </td>
                <td width="100" style="word-wrap: break-word; word-break: break-all;" align="right"> 
                    <? echo number_format($tot_delivery_to_party_qty,2) ?>
                </td> 
                <td style="word-wrap: break-word; word-break: break-all;" align="right"> 
                    <? echo number_format($tot_delivery_to_party_grey_qty,2) ?>
                </td>
            </tr>
        </table>
    </fieldset>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');    
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$type"; 
    exit();
}

?>