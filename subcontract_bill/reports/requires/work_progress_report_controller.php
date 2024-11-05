<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$data=$_REQUEST['data'];
$user_id = $_SESSION['logic_erp']['user_id'];
//$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
//$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
//$imge_arr=return_library_array( "select id,master_tble_id,image_location from common_photo_library",'id','image_location');
$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');

if ($action=="load_drop_down_buyer")
{ 
	echo create_drop_down( "cbo_buyer_id", 125, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();   	 
} 
if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
	?>
	 <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#list_view tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
			}
			
		}
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#selected_id').val( id );
		$('#selected_job_no').val( ddd );
	} 
	</script>
 <input type="hidden" id="selected_id" />
 <input type="hidden" id="selected_job_no" />
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
	$grpCond="LISTAGG(CAST(b.order_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.order_no) as order_no,LISTAGG(CAST(b.cust_style_ref AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.cust_style_ref) as style_ref_no";
	$arr=array(2=>$buyer);
	
    $sql="select a.id as sub_jobid,a.party_id, a.subcon_job, a.job_no_prefix_num, $grpCond,$year_field from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond
   group by a.id,a.party_id,a.insert_date, a.subcon_job, a.job_no_prefix_num order by a.id desc";	
   
	echo create_list_view("list_view", "Job No,Style,Buyer,Year","200,100,100,100","550","310",0, $sql , "js_set_value", "sub_jobid,job_no_prefix_num", "", 1, "0,0,party_id,0", $arr, "subcon_job,style_ref_no,party_id,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();	 
}
if($action=="job_no_popup_old")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		/*function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}*/
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#table_body2 tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
			}
			
		}
	function js_set_value(id)
	{
		
		var str=id.split("_");
		//alert(str[0]);
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[1];
		str=str[0];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#selected_id').val( id );
		$('#selected_job_no').val( selected_job_no );
	} 
	
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
     <input type="hidden" id="selected_job_no" name="selected_job_no" /> 
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
	$grpCond="LISTAGG(CAST(b.order_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.order_no) as order_no,LISTAGG(CAST(b.cust_style_ref AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.cust_style_ref) as cust_style_ref";
	
   $sql="select a.id as sub_jobid,a.party_id, a.subcon_job, a.job_no_prefix_num, $grpCond,$year_field from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond
   group by a.id,a.party_id,a.insert_date, a.subcon_job, a.job_no_prefix_num order by a.id desc";	
	
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
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<?=$i;?>"  onClick="js_set_value('<? echo $row[csf('sub_jobid')].'_'.$row[csf('job_no_prefix_num')]; ?>')" style="cursor:pointer;">
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
     <? $data_array=sql_select($sql);
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

if($action=="report_generate")
{ 
	if ($type == 1 || $type == 2) 
	{
		//======================== GETTING FORM DATA ===========================
		$job_no=str_replace("'","",$txt_job_no);
		$job_id=str_replace("'","",$txt_job_id);
		$txt_style_ref=str_replace("'","",$txt_style_ref);
		$txt_order_no=str_replace("'","",$txt_order_no);
		//$year_id=str_replace("'","",$cbo_year);
		$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
		$cbo_process=str_replace("'","",$cbo_process_id);
		//if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
		if($cbo_buyer_id!=0) $buyer_id_cond=" and a.party_id='$cbo_buyer_id'"; else $buyer_id_cond="";
		$job_no_cond="";
		if($job_id!="" && $job_no!="")
		{
			 $job_no_cond=" and a.id in ($job_id) ";	
		}
		elseif($job_id=="" &&  $job_no!="") {
			if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
		}
		//if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if($db_type==0)
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
		//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
		if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
		//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
		if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
		if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
		
		if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") {$date_cond="";} else {$date_cond=" and c.delivery_date between $txt_date_from and $txt_date_to";
		$date_from = str_replace("'", "", $txt_date_from);
		$date_to = str_replace("'", "", $txt_date_to);
		}
		
		if(str_replace("'","",trim($txt_date_from_rec))=="" && str_replace("'","",trim($txt_date_to_rec))=="") {$date_cond_recv="";} else {$date_cond_recv=" and b.order_rcv_date between $txt_date_from_rec and $txt_date_to_rec";
		$date_from_rec = str_replace("'", "", $txt_date_from_rec);
		$date_to_rec = str_replace("'", "", $txt_date_to_rec);
		}

		// ====================================== MAIN QUERY =====================================
		/*$job_sql = "select a.id as pic_id,a.job_no_prefix_num, a.subcon_job, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref 
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_no_cond $style_ref_cond $date_cond $job_no_cond $process_id_cond $buyer_id_cond
			group by a.job_no_prefix_num, a.subcon_job, a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref,a.id  order by b.main_process_id, a.job_no_prefix_num, b.order_no, b.delivery_date ";*/
		 $job_sql = "SELECT a.id as pic_id,a.job_no_prefix_num, a.subcon_job,a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref 
		from subcon_ord_mst a, subcon_ord_dtls b,subcon_delivery_mst c,subcon_delivery_dtls d
		where  a.id=b.mst_id and b.id=d.order_id and c.id=d.mst_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_no_cond $style_ref_cond $date_cond $date_cond_recv $job_no_cond $process_id_cond $buyer_id_cond $year_cond
			group by a.job_no_prefix_num, a.subcon_job, a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref,a.id  order by b.main_process_id, a.job_no_prefix_num, b.order_no, b.delivery_date ";
			//echo $job_sql;
		$job_sql_result=sql_select($job_sql);
		if(count($job_sql_result)==0)
		{
			?>
			<div style="margin: 0 auto;font-size: 20px;color: red;text-align: center;">Data not found! Please try again.</div>
			<?
		}
		$order_id_array = array();
		foreach ($job_sql_result as $val) 
		{
			$order_id_array[$val[csf('id')]] = $val[csf('id')];
		}
		
		$order_id_array = array();
		foreach ($job_sql_result as $val) 
		{
			$order_id_array[$val[csf('id')]] = $val[csf('id')];
		}
		
		
		$con = connect();
		$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (23)");
		if($r_id)
		{
			oci_commit($con);
		}
		
		if(count($order_id_array)>0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 23, 1,$order_id_array, $empty_arr);
		}
		
		
		//$ordeIDs = implode(",", $order_id_array);
		//$order_cond=where_con_using_array(array_unique($order_id_array),0,"b.order_id");
		// ====================================== FOR MATERIAL RCV ================================
		$inventory_array=array();
 		$inventory_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b , GBL_TEMP_ENGINE d  where a.id=b.mst_id and a.trans_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and b.order_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 group by b.order_id";
		
		$inventory_sql_result=sql_select($inventory_sql);
		foreach ($inventory_sql_result as $row)
		{
			$inventory_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
		}
		
		// ========================================= FOR ISSUE ======================================
		$inv_iss_array=array();
 		$inv_iss_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b , GBL_TEMP_ENGINE d where a.id=b.mst_id and a.trans_type=2 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 group by b.order_id";
		$inv_iss_sql_result=sql_select($inv_iss_sql);
		foreach ($inv_iss_sql_result as $row)
		{
			$inv_iss_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
		}	

		//========================================= FOR RETURN ===============================
		$inventory_ret_array=array();
 		$inv_ret_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b, GBL_TEMP_ENGINE d where a.id=b.mst_id and a.trans_type=3 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 group by b.order_id";
  		
		$inv_ret_sql_result=sql_select($inv_ret_sql);
		foreach ($inv_ret_sql_result as $row)
		{
			$inventory_ret_array[$row[csf('order_id')]]=$row[csf('quantity')];
		}	//var_dump($inventory_array);

		//========================================= FOR grey prod qty ===============================
		$inventory_grey_prod_array=array();
 		$inv_grey_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b , GBL_TEMP_ENGINE d where a.id=b.mst_id and a.trans_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=13 and b.order_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 group by b.order_id";
		
		$inv_grey_sql_result=sql_select($inv_grey_sql);
		foreach ($inv_grey_sql_result as $row)
		{
			$inventory_grey_prod_array[$row[csf('order_id')]]=$row[csf('quantity')];
		}	//var_dump($inventory_array);

		// ======================================== DELIVERY QTY ================================
		$del_date_cond = str_replace("c.delivery_date", "a.delivery_date", $date_cond);
		$delivery_array=array();
		 $del_date_cond = str_replace("c.delivery_date", "a.delivery_date", $date_cond);
		$delivery_array=array();
		  $delivery_sql="SELECT b.order_id,
		sum(CASE WHEN b.process_id='1' THEN  b.delivery_qty else 0 END) AS cutting,
		sum(CASE WHEN b.process_id='1' THEN  b.reject_qty  else 0 END) AS cutting_rej,

		sum(CASE WHEN b.process_id='2' THEN  b.delivery_qty  else 0 END) AS kniting,
		sum(CASE WHEN b.process_id='2' THEN  b.reject_qty  else 0 END) AS kniting_rej,

		sum(CASE WHEN b.process_id='3' THEN  b.delivery_qty  else 0 END) AS dyeing,
		sum(CASE WHEN b.process_id='3' THEN  b.reject_qty  else 0 END) AS dyeing_rej,

		sum(CASE WHEN b.process_id='4' THEN  b.delivery_qty  else 0 END) AS finishing,
		sum(CASE WHEN b.process_id='4' THEN  b.reject_qty else 0  END) AS finishing_rej,

		sum(CASE WHEN b.process_id='5' THEN  b.delivery_qty  else 0 END) AS sewing,
		sum(CASE WHEN b.process_id='5' THEN  b.reject_qty  else 0 END) AS sewing_rej,

		sum(CASE WHEN b.process_id='6' THEN  b.delivery_qty else 0  END) AS fab_print,
		sum(CASE WHEN b.process_id='6' THEN  b.reject_qty else 0  END) AS fab_print_rej,

		sum(CASE WHEN b.process_id='7' THEN  b.delivery_qty  else 0 END) AS washing,
		sum(CASE WHEN b.process_id='7' THEN  b.reject_qty else 0  END) AS washing_rej,

		sum(CASE WHEN b.process_id='8' THEN  b.delivery_qty  else 0 END) AS printing,
		sum(CASE WHEN b.process_id='8' THEN  b.reject_qty else 0  END) AS printing_rej,

		sum(CASE WHEN b.process_id='9' THEN  b.delivery_qty else 0  END) AS Embroidery,
		sum(CASE WHEN b.process_id='9' THEN  b.reject_qty  else 0 END) AS Embroidery_rej,

		sum(CASE WHEN b.process_id='10' THEN  b.delivery_qty  else 0 END) AS Iron,
		sum(CASE WHEN b.process_id='10' THEN  b.reject_qty else 0  END) AS Iron_rej,

		sum(CASE WHEN b.process_id='11' THEN  b.delivery_qty else 0  END) AS Gmts_Finishing,
		sum(CASE WHEN b.process_id='11' THEN  b.reject_qty  else 0 END) AS Gmts_Finishing_rej,

		sum(CASE WHEN b.process_id='12' THEN  b.delivery_qty else 0  END) AS Gmts_Dyeing,
		sum(CASE WHEN b.process_id='12' THEN  b.reject_qty else 0  END) AS Gmts_Dyeing_rej,

		sum(CASE WHEN b.process_id='13' THEN  b.delivery_qty  else 0 END) AS Poly ,
		sum(CASE WHEN b.process_id='13' THEN  b.reject_qty else 0  END) AS Poly_rej 

		from subcon_delivery_mst a, subcon_delivery_dtls b, GBL_TEMP_ENGINE d where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 $del_date_cond group by b.order_id";
		$delivery_sql_result=sql_select($delivery_sql);
		foreach ($delivery_sql_result as $row)
		{
			$delivery_array[$row[csf('order_id')]]['item_id']=$row[csf('item_id')];
			$delivery_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
			$delivery_array[$row[csf('order_id')]]['cutting_rej']=$row[csf('cutting_rej')];
			$delivery_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
			$delivery_array[$row[csf('order_id')]]['kniting_rej']=$row[csf('kniting_rej')];

			$delivery_array[$row[csf('order_id')]]['dyeing']=$row[csf('dyeing')];
			$delivery_array[$row[csf('order_id')]]['dyeing_rej']=$row[csf('dyeing_rej')];

			$delivery_array[$row[csf('order_id')]]['finishing']=$row[csf('finishing')];
			$delivery_array[$row[csf('order_id')]]['finishing_rej']=$row[csf('finishing_rej')];

			$delivery_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
			$delivery_array[$row[csf('order_id')]]['sewing_rej']=$row[csf('sewing_rej')];

			$delivery_array[$row[csf('order_id')]]['fab_print']=$row[csf('fab_print')];
			$delivery_array[$row[csf('order_id')]]['fab_print_rej']=$row[csf('fab_print_rej')];

			$delivery_array[$row[csf('order_id')]]['washing']=$row[csf('washing')];
			$delivery_array[$row[csf('order_id')]]['washing_rej']=$row[csf('washing_rej')];

			$delivery_array[$row[csf('order_id')]]['printing']=$row[csf('printing')];
			$delivery_array[$row[csf('order_id')]]['printing_rej']=$row[csf('printing_rej')];

			$delivery_array[$row[csf('order_id')]]['Embroidery']=$row[csf('Embroidery')];
			$delivery_array[$row[csf('order_id')]]['Embroidery_rej']=$row[csf('Embroidery_rej')];

			$delivery_array[$row[csf('order_id')]]['Iron']=$row[csf('Iron')];
			$delivery_array[$row[csf('order_id')]]['Iron_rej']=$row[csf('Iron_rej')];

			$delivery_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
			$delivery_array[$row[csf('order_id')]]['Gmts_Finishing_rej']=$row[csf('Gmts_Finishing_rej')];

			$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing']=$row[csf('Gmts_Dyeing')];
			$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing_rej']=$row[csf('Gmts_Dyeing_rej')];

			$delivery_array[$row[csf('order_id')]]['Poly']=$row[csf('Poly')];
			$delivery_array[$row[csf('order_id')]]['Poly_rej']=$row[csf('Poly_rej')];
			
		}
		// ======================================== FAB. PRODUCTION ===============================
		//$order_cond=str_replace("b.order_id", "c.order_id", $order_cond);
		 $fab_production_array=array();
 		 $fab_production_sql="SELECT c.order_id,
		sum(CASE WHEN c.product_type='4' THEN  c.quantity END) AS finishing,
		sum(CASE WHEN c.product_type='8' THEN  c.quantity END) AS printing
		from  subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c, GBL_TEMP_ENGINE d where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.order_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 group by c.order_id"; 
		$fab_production_sql_result=sql_select($fab_production_sql);
		foreach ($fab_production_sql_result as $row)
		{
			$order_id=explode(',',$row[csf('order_id')]);
			foreach ($order_id as $val)
			{
				$fab_production_array[$val]['fabric_description']=$row[csf('fabric_description')];
				$fab_production_array[$val]['finishing']=$row[csf('finishing')];
				$fab_production_array[$val]['printing']=$row[csf('printing')];
			}
		}
		
		// ================================== FOR KNITING PRODUCTION ==================================
		$ordeIDsKnit = "'".implode("','", $order_id_array)."'";
		$knit_production_array=array();
		//$order_cond=str_replace("c.order_id", "b.order_id", $order_cond);
 		 $knit_production_sql="SELECT b.order_id, sum(b.product_qnty) AS kniting
		from  subcon_production_mst a, subcon_production_dtls b, GBL_TEMP_ENGINE d where a.id=b.mst_id and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.order_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 group by b.order_id";
		$knit_production_sql_result=sql_select($knit_production_sql);
		foreach ($knit_production_sql_result as $row)
		{
			$knit_production_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
		}	
		//var_dump ($fab_production_array);

		//==================================== FOR DYEING PRODUCTION =====================================
		$order_cond=str_replace("b.order_id", "c.po_id", $order_cond);
		$dying_data_array=array();
 		if ($db_type==0)
		{
			$dying_sql="SELECT c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c, GBL_TEMP_ENGINE d where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 group by c.po_id ";
		}
		elseif($db_type==2)
		{
			  $dying_sql="SELECT c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c, GBL_TEMP_ENGINE d where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 group by c.po_id ";  
		}
		$dying_sql_result=sql_select($dying_sql);
		foreach ($dying_sql_result as $row)
		{
			$dying_data_array[$row[csf('po_id')]]=$row[csf('production_qnty')];
		}
		//var_dump($dying_data_array);
		
		// ======================================= FOR GMTS PRODUCTION ===============================
		//$order_cond=str_replace("c.po_id", "order_id", $order_cond);
		$gmt_production_array=array();
 		$gmt_production_sql="SELECT a.order_id,
		sum(CASE WHEN a.production_type='1' THEN  a.production_qnty END) AS cutting,
		sum(CASE WHEN a.production_type='2' THEN  a.production_qnty END) AS sewing,
		sum(CASE WHEN a.production_type='4' THEN  a.production_qnty END) AS Gmts_Finishing
		from subcon_gmts_prod_dtls a, GBL_TEMP_ENGINE d where a.status_active=1 and a.is_deleted=0 and a.order_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 group by a.order_id"; 
		$gmt_production_sql_result=sql_select($gmt_production_sql);
		foreach ($gmt_production_sql_result as $row)
		{
			$gmt_production_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
			$gmt_production_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
			$gmt_production_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
		}
		//var_dump($gmt_production_array);

		//========================================== FOR BILL =====================================
		//$order_cond=str_replace("order_id", "b.order_id", $order_cond);
		$in_bill_qty_array=array();
		$in_bill_amnt_array=array(); $bill_order_arr=array(); $bill_amt_arr=array();
		 $in_bill_sql="SELECT b.order_id, b.mst_id, sum(b.amount) as amount,
		sum(CASE WHEN b.process_id='1' THEN  b.delivery_qty END) AS cutting,
		sum(CASE WHEN b.process_id='2' THEN  b.delivery_qty END) AS kniting,
		sum(CASE WHEN b.process_id='3' THEN  b.delivery_qty END) AS dyeing,
		sum(CASE WHEN b.process_id='4' THEN  b.delivery_qty END) AS finishing,
		sum(CASE WHEN b.process_id='5' THEN  b.delivery_qty END) AS sewing,
		sum(CASE WHEN b.process_id='6' THEN  b.delivery_qty END) AS fab_print,
		sum(CASE WHEN b.process_id='7' THEN  b.delivery_qty END) AS washing,
		sum(CASE WHEN b.process_id='8' THEN  b.delivery_qty END) AS printing,
		sum(CASE WHEN b.process_id='9' THEN  b.delivery_qty END) AS Embroidery,
		sum(CASE WHEN b.process_id='10' THEN  b.delivery_qty END) AS Iron,
		sum(CASE WHEN b.process_id='11' THEN  b.delivery_qty END) AS Gmts_Finishing,
		sum(CASE WHEN b.process_id='12' THEN  b.delivery_qty END) AS Gmts_Dyeing,
		sum(CASE WHEN b.process_id='13' THEN  b.delivery_qty END) AS Poly,
		
		sum(CASE WHEN b.process_id='1' THEN  b.amount END) AS am_cutting,
		sum(CASE WHEN b.process_id='2' THEN  b.amount END) AS am_kniting,
		sum(CASE WHEN b.process_id='3' THEN  b.amount END) AS am_dyeing,
		sum(CASE WHEN b.process_id='4' THEN  b.amount END) AS am_finishing,
		sum(CASE WHEN b.process_id='5' THEN  b.amount END) AS am_sewing,
		sum(CASE WHEN b.process_id='6' THEN  b.amount END) AS am_fab_print,
		sum(CASE WHEN b.process_id='7' THEN  b.amount END) AS am_washing,
		sum(CASE WHEN b.process_id='8' THEN  b.amount END) AS am_printing,
		sum(CASE WHEN b.process_id='9' THEN  b.amount END) AS am_Embroidery,
		sum(CASE WHEN b.process_id='10' THEN  b.amount END) AS am_Iron,
		sum(CASE WHEN b.process_id='11' THEN  b.amount END) AS am_Gmts_Finishing,
		sum(CASE WHEN b.process_id='12' THEN  b.amount END) AS am_Gmts_Dyeing,
		sum(CASE WHEN b.process_id='13' THEN  b.amount END) AS am_Poly		
		from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, GBL_TEMP_ENGINE d where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and b.order_id=d.ref_val and d.entry_form=23 and d.user_id=$user_id and d.ref_from=1 group by b.order_id, b.mst_id"; 
		$in_bill_sql_result=sql_select($in_bill_sql);
		foreach ($in_bill_sql_result as $row)
		{
			$in_bill_qty_array[$row[csf('order_id')]]['cutting']+=$row[csf('cutting')];
			$in_bill_qty_array[$row[csf('order_id')]]['kniting']+=$row[csf('kniting')];
			$in_bill_qty_array[$row[csf('order_id')]]['dyeing']+=$row[csf('dyeing')];
			$in_bill_qty_array[$row[csf('order_id')]]['finishing']+=$row[csf('finishing')];
			$in_bill_qty_array[$row[csf('order_id')]]['sewing']+=$row[csf('sewing')];
			$in_bill_qty_array[$row[csf('order_id')]]['fab_print']+=$row[csf('fab_print')];
			$in_bill_qty_array[$row[csf('order_id')]]['washing']+=$row[csf('washing')];
			$in_bill_qty_array[$row[csf('order_id')]]['printing']+=$row[csf('printing')];

			$in_bill_qty_array[$row[csf('order_id')]]['Embroidery']+=$row[csf('Embroidery')];
			$in_bill_qty_array[$row[csf('order_id')]]['Iron']+=$row[csf('Iron')];
			$in_bill_qty_array[$row[csf('order_id')]]['Gmts_Finishing']+=$row[csf('Gmts_Finishing')];
			$in_bill_qty_array[$row[csf('order_id')]]['Gmts_Dyeing']+=$row[csf('Gmts_Dyeing')];
			$in_bill_qty_array[$row[csf('order_id')]]['Poly']+=$row[csf('Poly')];
			
			$in_bill_amnt_array[$row[csf('order_id')]]['am_cutting']+=$row[csf('am_cutting')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_kniting']+=$row[csf('am_kniting')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_dyeing']+=$row[csf('am_dyeing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_finishing']+=$row[csf('am_finishing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_sewing']+=$row[csf('am_sewing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_fab_print']+=$row[csf('am_fab_print')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_washing']+=$row[csf('am_washing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_printing']+=$row[csf('am_printing')];

			$in_bill_amnt_array[$row[csf('order_id')]]['am_Embroidery']+=$row[csf('am_Embroidery')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Iron']+=$row[csf('am_Iron')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Gmts_Finishing']+=$row[csf('am_Gmts_Finishing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Gmts_Dyeing']+=$row[csf('am_Gmts_Dyeing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Poly']+=$row[csf('am_Poly')];

			$bill_order_arr[$row[csf('mst_id')]].=$row[csf('order_id')].',';
			$bill_amt_arr[$row[csf('order_id')]]+=$row[csf('amount')];
		}
			

		$order_wise_tot_paid_arr=array();
		$order_wise_tot_bill_arr2=array();
		$order_wise_tot_bill_arr=array();
		//$order_wise_tot_paid="select d.order_id, sum(b.total_adjusted) as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by d.order_id";
		//$order_cond=str_replace("b.order_id", "d.order_id", $order_cond);
 		 $order_wise_tot_paid="SELECT d.order_id, b.total_adjusted as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d, GBL_TEMP_ENGINE e where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1  group by d.order_id,b.total_adjusted";

		$order_wise_tot_paid_result=sql_select($order_wise_tot_paid);
		foreach ($order_wise_tot_paid_result as $row)
		{
			//$order_wise_tot_paid_arr[$row[csf('order_id')]]=$row[csf('rec_amount')];
			//$order_wise_tot_bill_arr[$row[csf('order_id')]]=$row[csf('bill_amount')];
			$order_wise_tot_paid_arr[$row[csf('order_id')]]+=$row[csf('rec_amount')];
		}

 		 $order_wise_tot_bill="SELECT a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d , GBL_TEMP_ENGINE e where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 order by a.id asc"; 
		$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
		foreach ($order_wise_tot_bill_result as $row)
		{
			$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
		}

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

		//var_dump($po_wise_payRec_arr);
		$order_cond=str_replace("d.order_id", "b.po_id", $order_cond);
		$batch_qty_array=array();
 		 $sql_batch="SELECT b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b , GBL_TEMP_ENGINE e where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 group by b.po_id";  
		$sql_batch_result=sql_select($sql_batch);
		foreach ($sql_batch_result as $row)
		{
			$batch_qty_array[$row[csf('po_id')]]=$row[csf('batch_qnty')];
		}
		
		
		ob_start();
		if ($cbo_process==4)
		{
			$tbl_width=2230;
			$col_span=26;
		}
		else
		{
			$tbl_width=1990;
			$col_span=23;
		}
		//$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
		
		$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (23)");
		if($r_id)
		{
			oci_commit($con);
		}
		disconnect($con);
		?>
	    <div>
	        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="70">Job No</th>
	                <th width="100">Party</th>
	                <th width="80">Order no</th>
	                <th width="50">Image</th>                            
	                <th width="100">Style Name</th>
	                <th width="90">Order Quantity</th>
	                <th width="100">Order Value</th>
	                <th width="60">UOM</th>
	                <th width="90">Delivery Date</th>
	                <th width="60">Days in Hand</th>
	                <th width="120">Material Receive</th>
	                <th width="120">Material Issue</th>
	                <th width="120">Material Return</th>
	                <th width="120">Material Balance</th>
	                <?
						if ($cbo_process==4)
						{
							?>
	                        <th width="80">Batch Qty</th>
	                        <th width="80">Batch Bal.</th>
	                        <th width="80">Dyeing Qty</th>
	                        <?
						}
					?>
	                <th width="80">Prod. Finish Qty</th>
	                <th width="80">Delivery Qty</th>
	                <th width="80">Reject Qty</th>
	                <? if ($type==1)
					{
						?>
	                <th width="80">Yet To Delv.</th>
	                <? } ?>
	                <th width="80">Bill Qty</th>
	                <th width="80">Bill Amount</th>
	                <? if ($type==2)
					{
						?>
	                <th width="80">Yet To Bill</th>
	                <? } ?>
	                <th width="100">Payment Rec.</th>
	                <th>Rec. Balance</th>
	            </thead>
	        </table>
		    <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
		        <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" id="">
			    <?
				$process_array=array();
				$i=1; $k=1;
				foreach ($job_sql_result as $row)
				{
					if (!in_array($row[csf("main_process_id")],$process_array) )
					{
						if($k!=1)
						{
						?>
							<tr class="tbl_bottom">
								<td colspan="6" align="right"><b>Process Total:</b></td>
								<td align="right"><? echo number_format($tot_order_qty); ?></td>
								<td align="right"><? echo number_format($tot_order_val); ?></td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right"><? echo number_format($tot_rec_qty); ?></td>
			                    <td align="right"><? echo number_format($tot_issue_qty); ?></td>
			                    <td align="right"><? echo number_format($tot_return_qty); ?></td>
			                    <td align="right">
			                    	<? 
			                    		$tot_material_blce=$tot_rec_qty-$tot_issue_qty;
			                    		echo $tot_material_blce;
			                     	?>
			                     </td>
			                    <?
									if ($cbo_process==4)
									{
										?>
			                                <td><? echo number_format($tot_batch_qty); ?></td>
			                                <td><? echo number_format($tot_batch_bal_qty); ?></td>
			                                <td><? echo number_format($tot_dyeing_qty); ?></td>
										<?
									}
								?>
								<td align="right"><? echo number_format($tot_prod_qty); ?></td>
								<td align="right"><? echo number_format($tot_del_qty); ?></td>
								<td align="right"><? echo number_format($tot_del_rej); ?></td>
			                    <? if ($type==1)
								{
								?>
			                    <td align="right"><? echo number_format($tot_yet_to_delv); ?></td>
			                    <? } ?>
								<td align="right"><? echo number_format($tot_bill_qty); ?></td>
								<td align="right"><? echo number_format($tot_bill_amnt); ?></td>
			                    <? if ($type==2)
								{
								?>
			                    <td align="right"><? echo number_format($tot_yet_to_bill); ?></td>
			                    <? } ?>
								<td align="right"><? echo number_format($tot_payment_amnt); ?></td>
			                    <td align="right"><? echo number_format($tot_balance); ?></td>
							</tr>
							<tr bgcolor="#dddddd">
								<td colspan="<? echo $col_span; ?>" align="left" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
							</tr>
						<?
							unset($tot_order_qty);
							unset($tot_order_val);
							unset($tot_rec_qty);
							unset($tot_issue_qty);
							unset($tot_material_blce);
							if ($cbo_process==4)
							{
								unset($tot_batch_qty);
								unset($tot_dyeing_qty);
							}
							unset($tot_prod_qty);
							unset($tot_del_qty);
							unset($tot_del_rej);
							unset($tot_bill_qty);
							unset($tot_bill_amnt);
							unset($tot_payment_amnt);
							if ($type==1)
							{
								unset($tot_yet_to_delv);
							}
							else if ($type==2)
							{
								unset($tot_yet_to_bill);
							}
							unset($tot_balance);
						}
						else
						{
							?>
							<tr bgcolor="#dddddd">
								<td colspan="<? echo $col_span; ?>" align="left" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
							</tr>
							<?
						}					
						$process_array[]=$row[csf('main_process_id')];            
						$k++;
					}
			        if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
					$prod_qty=0; $del_qty=0; $bill_qty=0; $bill_amnt=0; $pay_rec=0;
					$del_rej=0;
					if ($row[csf('main_process_id')]==1)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['cutting'];
						$del_qty=$delivery_array[$row[csf('id')]]['cutting'];
						$del_rej=$delivery_array[$row[csf('id')]]['cutting_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['cutting'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_cutting'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==2)
					{
						$prod_qty=$knit_production_array[$row[csf('id')]]['kniting'];
						$del_qty=$delivery_array[$row[csf('id')]]['kniting'];
						$del_rej=$delivery_array[$row[csf('id')]]['kniting_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['kniting'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_kniting'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==3)
					{
						$prod_qty=$dying_data_array[$row[csf('id')]];
						$del_qty=$delivery_array[$row[csf('id')]]['dyeing'];
						$del_rej=$delivery_array[$row[csf('id')]]['dyeing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['dyeing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_dyeing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==4)
					{
						$prod_qty=$fab_production_array[$row[csf('id')]]['finishing'];
						$del_qty=$delivery_array[$row[csf('id')]]['finishing'];
						$del_rej=$delivery_array[$row[csf('id')]]['finishing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['finishing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_finishing'];
						$batch_qty=$batch_qty_array[$row[csf('id')]];
						$dyeing_qty=$dying_data_array[$row[csf('id')]];
						$grey_prod = $inventory_grey_prod_array[$row[csf('id')]];
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==5)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['sewing'];
						$del_qty=$delivery_array[$row[csf('id')]]['sewing'];
						$del_rej=$delivery_array[$row[csf('id')]]['sewing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['sewing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_sewing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==6)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['fab_print'];
						$del_qty=$delivery_array[$row[csf('id')]]['fab_print'];
						$del_rej=$delivery_array[$row[csf('id')]]['fab_print_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['fab_print'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_fab_print'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==7)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['washing'];
						$del_qty=$delivery_array[$row[csf('id')]]['washing'];
						$del_rej=$delivery_array[$row[csf('id')]]['washing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['washing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_washing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==8)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['printing'];
						$del_qty=$delivery_array[$row[csf('id')]]['printing'];
						$del_rej=$delivery_array[$row[csf('id')]]['printing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['printing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_printing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}

					else if ($row[csf('main_process_id')]==9)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Embroidery'];
						$del_qty=$delivery_array[$row[csf('id')]]['Embroidery'];
						$del_rej=$delivery_array[$row[csf('id')]]['Embroidery_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Embroidery'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Embroidery'];
						$batch_qty=""; $dyeing_qty="";
					}

					else if ($row[csf('main_process_id')]==10)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Iron'];
						$del_qty=$delivery_array[$row[csf('id')]]['Iron'];
						$del_rej=$delivery_array[$row[csf('id')]]['Iron_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Iron'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Iron'];
						$batch_qty=""; $dyeing_qty="";
					}

					else if ($row[csf('main_process_id')]==11)
					{	
						$prod_qty=$gmt_production_array[$row[csf('id')]]['Gmts_Finishing'];
						$del_qty=$delivery_array[$row[csf('id')]]['Gmts_Finishing'];
						$del_rej=$delivery_array[$row[csf('id')]]['Gmts_Finishing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Gmts_Finishing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Finishing'];
						$batch_qty=""; $dyeing_qty="";
					}

					else if ($row[csf('main_process_id')]==12)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Gmts_Dyeing'];
						$del_qty=$delivery_array[$row[csf('id')]]['Gmts_Dyeing'];
						$del_rej=$delivery_array[$row[csf('id')]]['Gmts_Dyeing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Gmts_Dyeing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Dyeing'];
						$batch_qty=""; $dyeing_qty="";
					}

					else if ($row[csf('main_process_id')]==13)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Poly'];
						$del_qty=$delivery_array[$row[csf('id')]]['Poly'];
						$del_rej=$delivery_array[$row[csf('id')]]['Poly_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Poly'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Poly'];
						$batch_qty=""; $dyeing_qty="";
					}
					
					
					$rec_qty 	= $inventory_array[$row[csf('id')]]['quantity']-$inventory_ret_array[$row[csf('id')]];
					$issue_qty 	= $inv_iss_array[$row[csf('id')]]['quantity'];
					$return_qty = $inventory_ret_array[$row[csf('id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">	
			        	<td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
			            <td width="70" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></td>
			            <td width="100"><p><? echo $party_arr[$row[csf('party_id')]]; ?></p></td>
			            <td width="80"><p><? echo $row[csf('order_no')]; ?></p></td>
			            <td width="50"><img onclick="openImageWindow( <? echo $row[csf('pic_id')];//$row[csf('job_no_prefix_num')]; ?> )" src='../../<? echo $imge_arr[$row[csf('pic_id')]]; ?>' height='25' width='30' /></td>
			            <td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
			            
			            <td width="90" align="right"><p>
			            	<a href="##" onclick="show_progress_report_details('order_desc_popup','<? echo $row[csf("id")]; ?>','1150px')">
			            	<? echo number_format($row[csf('order_quantity')],2); ?>
			            	</a>
			            </p></td>

			            <td width="100" align="right"><p><? echo number_format($row[csf('amount')],2); ?></p></td>
			            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
			            <td width="90"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
			            <td width="60" align="center"><? $daysOnHand = datediff("d",date("Y-m-d"),$row[csf('delivery_date')]); echo $daysOnHand; ?> </td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_popup','<? echo $row[csf("id")]; ?>','1150px')"><? echo number_format($rec_qty,2); ?></a></p></td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_iss_popup','<? echo $row[csf("id")]; ?>','1150px')"><? echo number_format($issue_qty,2); ?></a></p></td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_rtn_popup','<? echo $row[csf("id")]; ?>','1300px')"><? echo number_format($return_qty,2); ?></a></p></td>
			            <td width="120" align="right">
			            	<?
			            		$mat_blnce_qty=$rec_qty-$issue_qty;
			            		echo number_format($mat_blnce_qty,2);
			            	?>
			            </td>
						<?
			                if ($cbo_process==4)
			                {
			                	$batch_bal_qty = $issue_qty - $batch_qty;
			                    ?>
			                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('batch_qty_pop_up','<? echo $row[csf("id")]; ?>_summary','1150px')"><? echo number_format($batch_qty,2); ?></a></p></td>
			                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('batch_qty_pop_up','<? echo $row[csf("id")]; ?>_summary','1150px')"><? echo number_format($batch_bal_qty,2); ?></a></p></td>
			                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo '3'; ?>','1150px')"><? echo number_format($dyeing_qty,2); ?></a></p></td>
			                    <?
			                }
			            ?>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','1150px')"><? echo number_format($prod_qty,2); ?></a></p></td>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('delivery_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>_<?echo $date_from;?>_<?echo $date_to;?>','1150px')"><? echo number_format($del_qty,2); ?></a></p></td>
			            <td width="80" align="right"><p><? echo number_format($del_rej,2); ?></p></td>
			            <? if ($type==1)
						{
						?>
			            <td width="80" align="right"><? $yet_to_delv=$row[csf('order_quantity')]-$del_qty; echo  number_format($yet_to_delv,2); ?></td>
			            <? } ?>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('bill_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','1150px')"><? echo number_format($bill_qty,2); ?></a></p></td>
			            <td width="80" align="right"><? echo  number_format($bill_amnt,2); ?></td>
			            <? if ($type==2)
						{
						?>
			            <td width="80" align="right"><? $yet_to_bill=$row[csf('order_quantity')]-$bill_qty; echo  number_format($yet_to_bill,2); ?></td>
			            <? } ?>
			            

			            <td width="100" align="right">
			            	<p><a href="##" onclick="show_progress_report_details('payment_rec_pop_up','<? echo $row[csf("id")].'_'.$bill_amnt; ?>_<? echo $row[csf('main_process_id')]; ?>','1150px')">

			            		<? 

			            		$order_wise_payment_received = ($order_wise_tot_bill_arr[$row[csf('id')]]) ? ($order_wise_tot_paid_arr[$row[csf('id')]]/$order_wise_tot_bill_arr[$row[csf('id')]])*$bill_amnt : 0;
			            		echo number_format($order_wise_payment_received,2);

			            		//echo number_format($order_wise_tot_paid_arr[$row[csf('id')]],2); 

			            		?>
			            	</a></p>
			            </td>
			            

			            <td width="80" align="right"><? $balance=$bill_amnt-$order_wise_payment_received; echo  number_format($balance,2); ?></td>
			        </tr>
			        <?
					$i++;
					$tot_order_qty+=$row[csf('order_quantity')];
					$tot_order_val+=$row[csf('amount')];
					$tot_rec_qty+=$rec_qty;
					$tot_issue_qty+=$issue_qty;
					$tot_return_qty+=$return_qty;
					$tot_material_blce+=$mat_blnce_qty;
					$tot_prod_qty+=$prod_qty;
					$tot_del_qty+=$del_qty;
					$tot_grey_prod+=$grey_prod;
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
						$tot_batch_bal_qty+=$batch_bal_qty;
						$tot_dyeing_qty+=$dyeing_qty;
						
						$tot_tottal_batch_qty+=$batch_qty;
						$tot_tottal_batch_bal_qty+=$batch_bal_qty;
						$tot_total_dyeing_qty+=$dyeing_qty;
					}
					
					$tot_total_order_qty+=$row[csf('order_quantity')];
					$tot_total_order_val+=$row[csf('amount')];
					$tot_total_rec_qty+=$rec_qty;
					$tot_total_issue_qty+=$issue_qty;
					$tot_total_return_qty+=$return_qty;
					$tot_total_material_blce+=$mat_blnce_qty;
					$tot_total_prod_qty+=$prod_qty;
					$tot_total_del_qty+=$del_qty;
					$tot_total_grey_prod+=$grey_prod;
					$tot_total_del_rej+=$del_rej;

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
				        <td colspan="6" align="right"><b>Process Total:</b></td>
				        <td align="right"><? echo number_format($tot_order_qty); ?></td>
				        <td align="right"><? echo number_format($tot_order_val); ?></td>
				        <td align="right">&nbsp;</td>
				        <td align="right">&nbsp;</td>
				        <td align="right">&nbsp;</td>
				        <td align="right"><? echo number_format($tot_rec_qty); ?></td>
				        <td align="right"><? echo number_format($tot_issue_qty); ?></td>
				        <td align="right"><? echo number_format($tot_return_qty); ?></td>
				        <td align="right"><? echo number_format($tot_material_blce) ?></td>
				        <?
							if ($cbo_process==4)
							{
								?>
								<td><? echo number_format($tot_batch_qty); ?></td>
								<td><? echo number_format($tot_batch_bal_qty); ?></td>
								<td><? echo number_format($tot_dyeing_qty); ?></td>
								<?
							}
				        ?>
				        <td align="right"><? echo number_format($tot_prod_qty); ?></td>
				        <td align="right"><? echo number_format($tot_del_qty); ?></td>
				        <td align="right"><? echo number_format($tot_del_rej); ?></td>
						<? if ($type==1)
				        {
				        ?>
				        <td align="right"><? echo number_format($tot_yet_to_delv); ?></td>
				        <? } ?>
				        <td align="right"><? echo number_format($tot_bill_qty); ?></td>
				        <td align="right"><? echo number_format($tot_bill_amnt); ?></td>
				        <? if ($type==2)
				        {
				        ?>
				        <td align="right"><? echo number_format($tot_yet_to_bill); ?></td>
				        <? } ?>
				        <td align="right"><? echo number_format($tot_payment_amnt); ?></td>
				        <td align="right"><? echo number_format($tot_balance); ?></td>
			    	</tr>
			    	<!-- ====================================== bottom part ============================ -->
			        <tr class="tbl_bottom">
			            <td colspan="6" align="right">Grand Total:</td>
			            <td align="right"><? echo number_format($tot_total_order_qty); ?></td>                            
			            <td align="right"><? echo number_format($tot_total_order_val); ?></td>
			            <td>&nbsp;</td>
			            <td>&nbsp;</td>
			            <td>&nbsp;</td>
			            <td><? echo number_format($tot_total_rec_qty); ?></td>
			            <td><? echo number_format($tot_total_issue_qty); ?></td>
			            <td><? echo number_format($tot_total_return_qty); ?></td>
			            <td><? echo number_format($tot_total_material_blce) ?></td>
			            <?
			                if ($cbo_process==4)
			                {
			                    ?>
			                    <td><? echo number_format($tot_tottal_batch_qty); ?></td>
			                    <td><? echo number_format($tot_tottal_batch_bal_qty); ?></td>
			                    <td><? echo number_format($tot_total_dyeing_qty); ?></td>
			                    <? 
			                }
			            ?>
			            <td><? echo number_format($tot_total_prod_qty); ?></td>
			            <td><? echo number_format($tot_total_del_qty); ?></td>
			            <td><? echo number_format($tot_total_del_rej); ?></td>
						<? if ($type==1)
			            {
			            ?>
			            <td><? echo number_format($tot_total_yet_to_delv); ?></td>
			            <? } ?>
			            <td><? echo number_format($tot_total_bill_qty); ?></td>
			            <td><? echo number_format($tot_total_bill_amnt); ?></td>
			            <? if ($type==2)
			            {
			            ?>
			            <td><? echo number_format($tot_total_yet_to_bill); ?></td>
			            <? } ?>
			            <td><? echo number_format($tot_total_payment_amnt); ?></td>
			            <td><? echo number_format($tot_total_balance); ?></td>
			        </tr>
			    </table>        
		    </div>
	    </div>
	    <?
	}
	
	else if($type == 3)
	{
		$job_no=str_replace("'","",$txt_job_no);
		$txt_style_ref=str_replace("'","",$txt_style_ref);
		$txt_order_no=str_replace("'","",$txt_order_no);
		$year_id=str_replace("'","",$cbo_year);
		$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
		$cbo_process=str_replace("'","",$cbo_process_id);
		if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
		if($cbo_buyer_id!=0) $buyer_id_cond=" and a.party_id='$cbo_buyer_id'"; else $buyer_id_cond="";
		
		if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
		//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
		if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
		//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
		if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
		if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
		
		if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and b.delivery_date between $txt_date_from and $txt_date_to";
		//===================================== main query =================================
		$job_sql = "SELECT a.id as pic_id,a.job_no_prefix_num, a.subcon_job, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref 
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_no_cond $style_ref_cond $date_cond $job_no_cond $process_id_cond $buyer_id_cond
			group by a.job_no_prefix_num, a.subcon_job, a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref,a.id  order by b.main_process_id, a.job_no_prefix_num, b.order_no, b.delivery_date ";
			//echo $job_sql;
		$job_sql_result=sql_select($job_sql);
		if(count($job_sql_result)==0)
		{
			?>
			<div style="margin: 0 auto;font-size: 20px;color: red;text-align: center;">Data not found! Please try again.</div>
			<?
			die();
		}
		$order_id_array = array();
		foreach ($job_sql_result as $val) 
		{
			$order_id_array[$val[csf('id')]] = $val[csf('id')];
		}
		$ordeIDs = implode(",", $order_id_array);
		//================================================================
		$inventory_array=array();
		$inventory_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and b.order_id in($ordeIDs) group by b.order_id"; // trans_type = 1 receive, 2 issue, 3 return
		// echo $inventory_sql;
		$inventory_sql_result=sql_select($inventory_sql);
		foreach ($inventory_sql_result as $row)
		{
			$inventory_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
		}
		// print_r($inventory_array);
		//=========================================================================
		$inv_iss_array=array();
		$inv_iss_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in($ordeIDs) group by b.order_id";
		$inv_iss_sql_result=sql_select($inv_iss_sql);
		foreach ($inv_iss_sql_result as $row)
		{
			$inv_iss_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
		}	
		//===================================================================
		$inventory_ret_array=array();
		$inv_ret_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in($ordeIDs) group by b.order_id";
		
		$inv_ret_sql_result=sql_select($inv_ret_sql);
		foreach ($inv_ret_sql_result as $row)
		{
			$inventory_ret_array[$row[csf('order_id')]]=$row[csf('quantity')];
		}	//var_dump($inventory_array);
		//=============================================================
		$delivery_array=array();
		$delivery_sql="SELECT b.order_id,
		sum(CASE WHEN b.process_id='1' THEN  b.delivery_qty else 0 END) AS cutting,
		sum(CASE WHEN b.process_id='1' THEN  b.reject_qty  else 0 END) AS cutting_rej,
		sum(CASE WHEN b.process_id='1' THEN  b.gray_qty else 0 END) AS gray_qty_1,

		sum(CASE WHEN b.process_id='2' THEN  b.delivery_qty  else 0 END) AS kniting,
		sum(CASE WHEN b.process_id='2' THEN  b.reject_qty  else 0 END) AS kniting_rej,
		sum(CASE WHEN b.process_id='2' THEN  b.gray_qty else 0 END) AS gray_qty_2,

		sum(CASE WHEN b.process_id='3' THEN  b.delivery_qty  else 0 END) AS dyeing,
		sum(CASE WHEN b.process_id='3' THEN  b.reject_qty  else 0 END) AS dyeing_rej,
		sum(CASE WHEN b.process_id='3' THEN  b.gray_qty else 0 END) AS gray_qty_3,

		sum(CASE WHEN b.process_id='4' THEN  b.delivery_qty  else 0 END) AS finishing,
		sum(CASE WHEN b.process_id='4' THEN  b.reject_qty else 0  END) AS finishing_rej,
		sum(CASE WHEN b.process_id='4' THEN  b.gray_qty else 0 END) AS gray_qty_4,

		sum(CASE WHEN b.process_id='5' THEN  b.delivery_qty  else 0 END) AS sewing,
		sum(CASE WHEN b.process_id='5' THEN  b.reject_qty  else 0 END) AS sewing_rej,
		sum(CASE WHEN b.process_id='5' THEN  b.gray_qty else 0 END) AS gray_qty_5,

		sum(CASE WHEN b.process_id='6' THEN  b.delivery_qty else 0  END) AS fab_print,
		sum(CASE WHEN b.process_id='6' THEN  b.reject_qty else 0  END) AS fab_print_rej,
		sum(CASE WHEN b.process_id='5' THEN  b.gray_qty else 0 END) AS gray_qty_6,

		sum(CASE WHEN b.process_id='7' THEN  b.delivery_qty  else 0 END) AS washing,
		sum(CASE WHEN b.process_id='7' THEN  b.reject_qty else 0  END) AS washing_rej,
		sum(CASE WHEN b.process_id='7' THEN  b.gray_qty else 0 END) AS gray_qty_7,

		sum(CASE WHEN b.process_id='8' THEN  b.delivery_qty  else 0 END) AS printing,
		sum(CASE WHEN b.process_id='8' THEN  b.reject_qty else 0  END) AS printing_rej,
		sum(CASE WHEN b.process_id='8' THEN  b.gray_qty else 0 END) AS gray_qty_8,

		sum(CASE WHEN b.process_id='9' THEN  b.delivery_qty else 0  END) AS Embroidery,
		sum(CASE WHEN b.process_id='9' THEN  b.reject_qty  else 0 END) AS Embroidery_rej,
		sum(CASE WHEN b.process_id='9' THEN  b.gray_qty else 0 END) AS gray_qty_9,

		sum(CASE WHEN b.process_id='10' THEN  b.delivery_qty  else 0 END) AS Iron,
		sum(CASE WHEN b.process_id='10' THEN  b.reject_qty else 0  END) AS Iron_rej,
		sum(CASE WHEN b.process_id='10' THEN  b.gray_qty else 0 END) AS gray_qty_10,


		sum(CASE WHEN b.process_id='11' THEN  b.delivery_qty else 0  END) AS Gmts_Finishing,
		sum(CASE WHEN b.process_id='11' THEN  b.reject_qty  else 0 END) AS Gmts_Finishing_rej,
		sum(CASE WHEN b.process_id='11' THEN  b.gray_qty else 0 END) AS gray_qty_11,

		sum(CASE WHEN b.process_id='12' THEN  b.delivery_qty else 0  END) AS Gmts_Dyeing,
		sum(CASE WHEN b.process_id='12' THEN  b.reject_qty else 0  END) AS Gmts_Dyeing_rej,
		sum(CASE WHEN b.process_id='12' THEN  b.gray_qty else 0 END) AS gray_qty_12,


		sum(CASE WHEN b.process_id='13' THEN  b.delivery_qty  else 0 END) AS Poly ,
		sum(CASE WHEN b.process_id='13' THEN  b.reject_qty else 0  END) AS Poly_rej,
		sum(CASE WHEN b.process_id='13' THEN  b.gray_qty else 0 END) AS gray_qty_13

		from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.order_id in($ordeIDs) group by b.order_id";


		// echo $delivery_sql;
		$gray_qty_array=array();

		$delivery_sql_result=sql_select($delivery_sql);
		foreach ($delivery_sql_result as $row)
		{
			$delivery_array[$row[csf('order_id')]]['item_id']=$row[csf('item_id')];

			$delivery_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
			$delivery_array[$row[csf('order_id')]]['cutting_rej']=$row[csf('cutting_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_1']=$row[csf('gray_qty_1')];


			$delivery_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
			$delivery_array[$row[csf('order_id')]]['kniting_rej']=$row[csf('kniting_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_2']=$row[csf('gray_qty_2')];


			$delivery_array[$row[csf('order_id')]]['dyeing']=$row[csf('dyeing')];
			$delivery_array[$row[csf('order_id')]]['dyeing_rej']=$row[csf('dyeing_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_3']=$row[csf('gray_qty_3')];


			$delivery_array[$row[csf('order_id')]]['finishing']=$row[csf('finishing')];
			$delivery_array[$row[csf('order_id')]]['finishing_rej']=$row[csf('finishing_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_4']=$row[csf('gray_qty_4')];


			$delivery_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
			$delivery_array[$row[csf('order_id')]]['sewing_rej']=$row[csf('sewing_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_5']=$row[csf('gray_qty_5')];


			$delivery_array[$row[csf('order_id')]]['fab_print']=$row[csf('fab_print')];
			$delivery_array[$row[csf('order_id')]]['fab_print_rej']=$row[csf('fab_print_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_6']=$row[csf('gray_qty_6')];


			$delivery_array[$row[csf('order_id')]]['washing']=$row[csf('washing')];
			$delivery_array[$row[csf('order_id')]]['washing_rej']=$row[csf('washing_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_7']=$row[csf('gray_qty_7')];


			$delivery_array[$row[csf('order_id')]]['printing']=$row[csf('printing')];
			$delivery_array[$row[csf('order_id')]]['printing_rej']=$row[csf('printing_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_8']=$row[csf('gray_qty_8')];


			$delivery_array[$row[csf('order_id')]]['Embroidery']=$row[csf('Embroidery')];
			$delivery_array[$row[csf('order_id')]]['Embroidery_rej']=$row[csf('Embroidery_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_9']=$row[csf('gray_qty_8')];


			$delivery_array[$row[csf('order_id')]]['Iron']=$row[csf('Iron')];
			$delivery_array[$row[csf('order_id')]]['Iron_rej']=$row[csf('Iron_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_10']=$row[csf('gray_qty_10')];


			$delivery_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
			$delivery_array[$row[csf('order_id')]]['Gmts_Finishing_rej']=$row[csf('Gmts_Finishing_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_11']=$row[csf('gray_qty_11')];


			$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing']=$row[csf('Gmts_Dyeing')];
			$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing_rej']=$row[csf('Gmts_Dyeing_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_12']=$row[csf('gray_qty_12')];


			$delivery_array[$row[csf('order_id')]]['Poly']=$row[csf('Poly')];
			$delivery_array[$row[csf('order_id')]]['Poly_rej']=$row[csf('Poly_rej')];
			$gray_qty_array[$row[csf('order_id')]]['gray_qty_13']=$row[csf('gray_qty_13')];
			
		}


		// var_dump($delivery_array);
		//=========================================================
		$fab_production_array=array();
		$fab_production_sql="SELECT c.order_id, sum(CASE WHEN c.product_type='4' THEN  c.quantity END) AS finishing, sum(CASE WHEN c.product_type='8' THEN  c.quantity END) AS printing from  subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.order_id in($ordeIDs) group by c.order_id"; 
		// echo $fab_production_sql;
		$fab_production_sql_result=sql_select($fab_production_sql);
		foreach ($fab_production_sql_result as $row)
		{
			$order_id=explode(',',$row[csf('order_id')]);
			foreach ($order_id as $val)
			{
				$fab_production_array[$val]['fabric_description']=$row[csf('fabric_description')];
				$fab_production_array[$val]['finishing']=$row[csf('finishing')];
				$fab_production_array[$val]['printing']=$row[csf('printing')];
			}
		}
		//===============================================================
		$ordeIDsProd = "'".implode("','", $order_id_array)."'";
		$knit_production_array=array();
		$knit_production_sql="SELECT b.order_id, sum(b.product_qnty) AS kniting from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in($ordeIDsProd)group by b.order_id"; 
		$knit_production_sql_result=sql_select($knit_production_sql);
		foreach ($knit_production_sql_result as $row)
		{
			$knit_production_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
		}	
		// var_dump ($knit_production_array);
		//================================================================
		$dying_data_array=array();
		if ($db_type==0)
		{
			$dying_sql="SELECT c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_id in($ordeIDs) group by c.po_id ";
		}
		elseif($db_type==2)
		{
			$dying_sql="SELECT c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_id in($ordeIDs) group by c.po_id ";
		}
		$dying_sql_result=sql_select($dying_sql);
		foreach ($dying_sql_result as $row)
		{
			$dying_data_array[$row[csf('po_id')]]=$row[csf('production_qnty')];
		}
		//var_dump($dying_data_array);
		
		$gmt_production_array=array();
		$gmt_production_sql="SELECT order_id, sum(CASE WHEN production_type='1' THEN  production_qnty END) AS cutting, sum(CASE WHEN production_type='2' THEN  production_qnty END) AS sewing, sum(CASE WHEN production_type='4' THEN  production_qnty END) AS Gmts_Finishing from subcon_gmts_prod_dtls where status_active=1 and is_deleted=0 and order_id in($ordeIDs) group by order_id"; 
		$gmt_production_sql_result=sql_select($gmt_production_sql);
		foreach ($gmt_production_sql_result as $row)
		{
			$gmt_production_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
			$gmt_production_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
			$gmt_production_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
		}
		//var_dump($gmt_production_array);
		$in_bill_qty_array=array();
		$in_bill_amnt_array=array(); $bill_order_arr=array(); $bill_amt_arr=array();
		$in_bill_sql="SELECT order_id, mst_id, 
		sum(amount) as amount,
		sum(CASE WHEN process_id='1' THEN  delivery_qty END) AS cutting,
		sum(CASE WHEN process_id='2' THEN  delivery_qty END) AS kniting,
		sum(CASE WHEN process_id='3' THEN  delivery_qty END) AS dyeing,
		sum(CASE WHEN process_id='4' THEN  delivery_qty END) AS finishing,
		sum(CASE WHEN process_id='5' THEN  delivery_qty END) AS sewing,
		sum(CASE WHEN process_id='6' THEN  delivery_qty END) AS fab_print,
		sum(CASE WHEN process_id='7' THEN  delivery_qty END) AS washing,
		sum(CASE WHEN process_id='8' THEN  delivery_qty END) AS printing,
		sum(CASE WHEN process_id='9' THEN  delivery_qty END) AS Embroidery,
		sum(CASE WHEN process_id='10' THEN  delivery_qty END) AS Iron,
		sum(CASE WHEN process_id='11' THEN  delivery_qty END) AS Gmts_Finishing,
		sum(CASE WHEN process_id='12' THEN  delivery_qty END) AS Gmts_Dyeing,
		sum(CASE WHEN process_id='13' THEN  delivery_qty END) AS Poly,
		
		sum(CASE WHEN process_id='1' THEN  amount END) AS am_cutting,
		sum(CASE WHEN process_id='2' THEN  amount END) AS am_kniting,
		sum(CASE WHEN process_id='3' THEN  amount END) AS am_dyeing,
		sum(CASE WHEN process_id='4' THEN  amount END) AS am_finishing,
		sum(CASE WHEN process_id='5' THEN  amount END) AS am_sewing,
		sum(CASE WHEN process_id='6' THEN  amount END) AS am_fab_print,
		sum(CASE WHEN process_id='7' THEN  amount END) AS am_washing,
		sum(CASE WHEN process_id='8' THEN  amount END) AS am_printing,
		sum(CASE WHEN process_id='9' THEN  amount END) AS am_Embroidery,
		sum(CASE WHEN process_id='10' THEN  amount END) AS am_Iron,
		sum(CASE WHEN process_id='11' THEN  amount END) AS am_Gmts_Finishing,
		sum(CASE WHEN process_id='12' THEN  amount END) AS am_Gmts_Dyeing,
		sum(CASE WHEN process_id='13' THEN  amount END) AS am_Poly
		
		from subcon_inbound_bill_dtls 
		where status_active=1 and is_deleted=0 and order_id in($ordeIDs) group by order_id, mst_id";
		$in_bill_sql_result=sql_select($in_bill_sql);
		foreach ($in_bill_sql_result as $row)
		{
			$in_bill_qty_array[$row[csf('order_id')]]['cutting']+=$row[csf('cutting')];
			$in_bill_qty_array[$row[csf('order_id')]]['kniting']+=$row[csf('kniting')];
			$in_bill_qty_array[$row[csf('order_id')]]['dyeing']+=$row[csf('dyeing')];
			$in_bill_qty_array[$row[csf('order_id')]]['finishing']+=$row[csf('finishing')];
			$in_bill_qty_array[$row[csf('order_id')]]['sewing']+=$row[csf('sewing')];
			$in_bill_qty_array[$row[csf('order_id')]]['fab_print']+=$row[csf('fab_print')];
			$in_bill_qty_array[$row[csf('order_id')]]['washing']+=$row[csf('washing')];
			$in_bill_qty_array[$row[csf('order_id')]]['printing']+=$row[csf('printing')];

			$in_bill_qty_array[$row[csf('order_id')]]['Embroidery']+=$row[csf('Embroidery')];
			$in_bill_qty_array[$row[csf('order_id')]]['Iron']+=$row[csf('Iron')];
			$in_bill_qty_array[$row[csf('order_id')]]['Gmts_Finishing']+=$row[csf('Gmts_Finishing')];
			$in_bill_qty_array[$row[csf('order_id')]]['Gmts_Dyeing']+=$row[csf('Gmts_Dyeing')];
			$in_bill_qty_array[$row[csf('order_id')]]['Poly']+=$row[csf('Poly')];
			
			$in_bill_amnt_array[$row[csf('order_id')]]['am_cutting']+=$row[csf('am_cutting')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_kniting']+=$row[csf('am_kniting')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_dyeing']+=$row[csf('am_dyeing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_finishing']+=$row[csf('am_finishing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_sewing']+=$row[csf('am_sewing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_fab_print']+=$row[csf('am_fab_print')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_washing']+=$row[csf('am_washing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_printing']+=$row[csf('am_printing')];

			$in_bill_amnt_array[$row[csf('order_id')]]['am_Embroidery']+=$row[csf('am_Embroidery')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Iron']+=$row[csf('am_Iron')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Gmts_Finishing']+=$row[csf('am_Gmts_Finishing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Gmts_Dyeing']+=$row[csf('am_Gmts_Dyeing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Poly']+=$row[csf('am_Poly')];

			$bill_order_arr[$row[csf('mst_id')]].=$row[csf('order_id')].',';
			$bill_amt_arr[$row[csf('order_id')]]+=$row[csf('amount')];

		}

		$order_wise_tot_paid_arr=array();
		$order_wise_tot_bill_arr2=array();
		$order_wise_tot_bill_arr=array();
		//$order_wise_tot_paid="select d.order_id, sum(b.total_adjusted) as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by d.order_id";

		$order_wise_tot_paid="SELECT d.order_id, b.total_adjusted as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.order_id in($ordeIDs) group by d.order_id,b.total_adjusted";

		$order_wise_tot_paid_result=sql_select($order_wise_tot_paid);
		foreach ($order_wise_tot_paid_result as $row)
		{
			//$order_wise_tot_paid_arr[$row[csf('order_id')]]=$row[csf('rec_amount')];
			//$order_wise_tot_bill_arr[$row[csf('order_id')]]=$row[csf('bill_amount')];
			$order_wise_tot_paid_arr[$row[csf('order_id')]]+=$row[csf('rec_amount')];
		}

		$order_wise_tot_bill="SELECT a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.order_id in($ordeIDs)  order by a.id asc";
		$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
		foreach ($order_wise_tot_bill_result as $row)
		{
			$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
		}

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

		//var_dump($po_wise_payRec_arr);
		
		$batch_qty_array=array();
		$sql_batch="SELECT b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id in($ordeIDs) group by b.po_id";
		// echo $sql_batch;
		$sql_batch_result=sql_select($sql_batch);
		foreach ($sql_batch_result as $row)
		{
			$batch_qty_array[$row[csf('po_id')]]=$row[csf('batch_qnty')];
		}
		// print_r($batch_qty_array);
		
		
		ob_start();
		if ($cbo_process==4)
		{
			$tbl_width=1490;
			$col_span=17;
		}
		else
		{
			$tbl_width=1490;
			$col_span=17;
		}
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
		?>
	    <div>
	        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
	            <thead>
	            	<tr>
		                <th width="30">SL</th>
		                <th width="70">Job No</th>
		                <th width="150">Party</th>
		                <th width="150">Order no</th>                            
		                <th width="100">Style Name</th>
		                <th width="90">Order Quantity</th>
		                <th width="60">UOM</th>
		                <th width="120">Material Receive</th>
		                <th width="120">Material Issue</th>
		                <th width="120">Material Balance</th>	                
	                    <th width="80">Batch Qty</th>
	                    <th width="80">Batch Balance</th>	                        
		                <th width="80">Production [Grey]</th>
		                <th width="80">Production Balance</th>
		                <th width="80">Delivery Qty Grey</th>
		                <th width="80">Delivery Qty Finished</th>
		                <th width="80">Yet To Delv. In Grey.</th>
	            	</tr>
	            </thead>
	            
	        </table>
		    <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
		        <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" id="">
			    <?
				$process_array=array();
				$i=1; $k=1;
				foreach ($job_sql_result as $row)
				{
					if (!in_array($row[csf("main_process_id")],$process_array) )
					{
						if($k!=1)
						{
							?>
							<tr class="tbl_bottom">
								<td width="500" colspan="5" align="right"><b>Process Total:</b></td>
								<td width="90" align="right"><? echo number_format($tot_order_qty); ?></td>
								<td width="60" align="right">&nbsp;</td>
								<td width="120" align="right"><? echo number_format($tot_rec_qty); ?></td>
								<td width="120" align="right"><? echo number_format($tot_total_issue_qty); ?></td>
								<td width="120" align="right"><? echo number_format($tot_material_blce); ?></td>
								<td width="80" align="right"><? echo number_format($tot_batch_qty); ?></td>
			                    <td width="80" align="right"><? echo number_format($tot_batch_balance); ?></td>
			                    <td width="80" align="right"><? echo $tot_dyeing_qty;?></td>
			                    <td width="80" align="right"><?php echo number_format($tot_order_qty-$tot_dyeing_qty); ?></td>
                                <td width="80"><? echo number_format($tot_del_qty_gray); ?></td>
                                <td width="80"><? echo number_format($tot_del_qty); ?></td>
								<td width="80" align="right"><? echo number_format($tot_yet_to_delv); ?></td>
							</tr>
							<tr bgcolor="#dddddd">
								<td colspan="<? echo $col_span; ?>" align="left" >
									<b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b>
								</td>
							</tr>
							<?
							unset($tot_order_qty);
							unset($tot_rec_qty);
							unset($tot_total_issue_qty);
							unset($tot_material_blce);
							unset($tot_batch_qty);
							unset($tot_batch_balance);
							unset($tot_dyeing_qty);
							unset($tot_del_qty_gray);
							unset($tot_del_qty);
							unset($tot_yet_to_delv);
						}
						else
						{
							?>
							<tr bgcolor="#dddddd">
								<td colspan="<? echo $col_span; ?>" align="left" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
							</tr>
							<?
						}					
						$process_array[]=$row[csf('main_process_id')];            
						$k++;
					}
			        if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
					$prod_qty=0; $del_qty=0; $bill_qty=0; $bill_amnt=0; $pay_rec=0;
					$del_rej=0;

					if ($row[csf('main_process_id')]==1)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['cutting'];
						$del_qty=$delivery_array[$row[csf('id')]]['cutting'];
						$del_rej=$delivery_array[$row[csf('id')]]['cutting_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['cutting'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_cutting'];
						// $batch_qty=""; 
						$batch_qty=$batch_qty_array[$row[csf('id')]];
						$dyeing_qty="";
						//$pay_rec=
						$del_qty_fin = "";

						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_1'];
					}
					else if ($row[csf('main_process_id')]==2)
					{
						$prod_qty=$knit_production_array[$row[csf('id')]]['kniting'];
						$del_qty=$delivery_array[$row[csf('id')]]['kniting'];
						$del_rej=$delivery_array[$row[csf('id')]]['kniting_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['kniting'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_kniting'];
						$batch_qty="";
						$dyeing_qty="";
						$del_qty_fin = "";
						//$pay_rec=


						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_2'];
					}
					else if ($row[csf('main_process_id')]==3)
					{
						$prod_qty=$dying_data_array[$row[csf('id')]];
						$del_qty_fin=$delivery_array[$row[csf('id')]]['dyeing'];
						$del_qty = "";
						// $del_qty=$delivery_array[$row[csf('id')]]['dyeing'];
						$del_rej=$delivery_array[$row[csf('id')]]['dyeing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['dyeing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_dyeing'];
						$batch_qty="";
						$dyeing_qty="";
						//$pay_rec=

						$gray_qty_fin=$gray_qty_array[$row[csf('id')]]['gray_qty_3'];
					}
					else if ($row[csf('main_process_id')]==4)
					{
						$prod_qty=$fab_production_array[$row[csf('id')]]['finishing'];
						$del_qty=$delivery_array[$row[csf('id')]]['finishing'];
						$del_rej=$delivery_array[$row[csf('id')]]['finishing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['finishing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_finishing'];
						$batch_qty=$batch_qty_array[$row[csf('id')]];
						$dyeing_qty=$dying_data_array[$row[csf('id')]];
						//$pay_rec=
						$del_qty_fin = "";


						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_4'];
					}
					else if ($row[csf('main_process_id')]==5)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['sewing'];
						$del_qty=$delivery_array[$row[csf('id')]]['sewing'];
						$del_rej=$delivery_array[$row[csf('id')]]['sewing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['sewing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_sewing'];
						$batch_qty="";
						$dyeing_qty="";
						//$pay_rec=
						$del_qty_fin = "";

						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_5'];
					}
					else if ($row[csf('main_process_id')]==6)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['fab_print'];
						$del_qty=$delivery_array[$row[csf('id')]]['fab_print'];
						$del_rej=$delivery_array[$row[csf('id')]]['fab_print_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['fab_print'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_fab_print'];
						$batch_qty="";
						$dyeing_qty="";
						//$pay_rec=
						$del_qty_fin = "";

						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_6'];
					}
					else if ($row[csf('main_process_id')]==7)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['washing'];
						$del_qty=$delivery_array[$row[csf('id')]]['washing'];
						$del_rej=$delivery_array[$row[csf('id')]]['washing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['washing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_washing'];
						$batch_qty="";
						$dyeing_qty="";
						//$pay_rec=
						$del_qty_fin = "";

						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_7'];
					}
					else if ($row[csf('main_process_id')]==8)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['printing'];
						$del_qty=$delivery_array[$row[csf('id')]]['printing'];
						$del_rej=$delivery_array[$row[csf('id')]]['printing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['printing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_printing'];
						$batch_qty="";
						$dyeing_qty="";
						//$pay_rec=
						$del_qty_fin = "";


						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_8'];
					}

					else if ($row[csf('main_process_id')]==9)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Embroidery'];
						$del_qty=$delivery_array[$row[csf('id')]]['Embroidery'];
						$del_rej=$delivery_array[$row[csf('id')]]['Embroidery_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Embroidery'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Embroidery'];
						$batch_qty="";
						$dyeing_qty="";
						$del_qty_fin = "";

						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_9'];
					}

					else if ($row[csf('main_process_id')]==10)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Iron'];
						$del_qty=$delivery_array[$row[csf('id')]]['Iron'];
						$del_rej=$delivery_array[$row[csf('id')]]['Iron_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Iron'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Iron'];
						$batch_qty="";
						$dyeing_qty="";
						$del_qty_fin = "";

						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_10'];
						
					}

					else if ($row[csf('main_process_id')]==11)
					{	
						$prod_qty=$gmt_production_array[$row[csf('id')]]['Gmts_Finishing'];
						$del_qty=$delivery_array[$row[csf('id')]]['Gmts_Finishing'];
						$del_rej=$delivery_array[$row[csf('id')]]['Gmts_Finishing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Gmts_Finishing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Finishing'];
						$batch_qty=""; 
						$dyeing_qty="";
						$del_qty_fin = "";


						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_11'];
					}

					else if ($row[csf('main_process_id')]==12)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Gmts_Dyeing'];
						$del_qty=$delivery_array[$row[csf('id')]]['Gmts_Dyeing'];
						$del_rej=$delivery_array[$row[csf('id')]]['Gmts_Dyeing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Gmts_Dyeing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Dyeing'];
						$batch_qty="";
						 $dyeing_qty="";
						 $del_qty_fin = "";

						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_12'];
						
					}

					else if ($row[csf('main_process_id')]==13)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Poly'];
						$del_qty=$delivery_array[$row[csf('id')]]['Poly'];
						$del_rej=$delivery_array[$row[csf('id')]]['Poly_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Poly'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Poly'];
						$batch_qty=""; 
						$dyeing_qty="";
						$del_qty_fin = "";

						$gray_qty=$gray_qty_array[$row[csf('id')]]['gray_qty_13'];
						
					}
					
					
					$rec_qty=$inventory_array[$row[csf('id')]]['quantity']-$inventory_ret_array[$row[csf('id')]];
					$issue_qty=$inv_iss_array[$row[csf('id')]]['quantity'];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">	
			        	<td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
			            <td width="70" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></td>
			            <td width="150"><p><? echo $party_arr[$row[csf('party_id')]]; ?></p></td>
			            <td width="150"><p><? echo $row[csf('order_no')]; ?></p></td>			            
			            <td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
			            
			            <td width="90" align="right"><p>
			            	<a href="##" onclick="show_progress_report_details('order_desc_popup','<? echo $row[csf("id")]; ?>','850px')">
			            	<? echo number_format($row[csf('order_quantity')]); ?>
			            	</a>
			            </p></td>
			            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
			            
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_popup','<? echo $row[csf("id")]; ?>','850px')"><? echo number_format($rec_qty); ?></a></p></td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_iss_popup','<? echo $row[csf("id")]; ?>','850px')"><? echo number_format($issue_qty); ?></a></p></td>
			            <td width="120" align="right">
			            	<?
			            		$mat_blnce_qty=$rec_qty-$issue_qty;
			            		echo number_format($mat_blnce_qty);
			            	?>
			           </td>
	                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('batch_qty_pop_up','<? echo $row[csf("id")]; ?>_summary','960px')">
	                    	<? echo number_format($batch_qty = $batch_qty_array[$row[csf('id')]]); //number_format($batch_qty,2); ?>
	                    		
	                    	</a></p></td>
	                    <td width="80" align="right"><p>
	                    	<? $batch_balance = $issue_qty - $batch_qty;echo number_format($batch_balance); ?>	                   		
	                    	</p></td>
			                    
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>_summary','850px')"><? echo number_format($prod_qty); ?></a></p></td>
			            <td width="80" align="right"><?php echo number_format($row[csf('order_quantity')]-$prod_qty); ?></td>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('delivery_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>_<?echo $date_from;?>_<?echo $date_to;?>_summary','850px')"><? echo number_format($gray_qty); ?></a></p></td>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('delivery_qty_fin_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','850px')"><? echo number_format($del_qty); ?></a></p></td>
			           
			            <td width="80" align="right">
			            	<? 
			            	$delivery_qnty = 0;
			            	if($del_qty==""){
			            		$delivery_qnty = $del_qty_fin;
			            	}else{
			            		$delivery_qnty = $del_qty;
			            	}
			            	$yet_to_delv=$rec_qty-$delivery_qnty; 

			            	echo  number_format($yet_to_delv); 
			            	?>
			            		
			            	</td>
			        </tr>
			        <?
					$i++;
					$tot_order_qty+=$row[csf('order_quantity')];
					$tot_order_val+=$row[csf('amount')];
					$tot_rec_qty+=$rec_qty;
					$tot_issue_qty+=$issue_qty;
					$tot_material_blce+=$mat_blnce_qty;
					$tot_prod_qty+=$prod_qty;
					$tot_del_qty+=$del_qty;//$del_qty;
					$tot_del_qty_gray+=$gray_qty; //gray_qty;
					$tot_yet_to_delv+=$yet_to_delv;
					$tot_yet_to_bill+=$yet_to_bill;
					$tot_bill_qty+=$bill_qty;
					$tot_bill_amnt+=$bill_amnt;
					$tot_payment_amnt+=$order_wise_payment_received;
					$tot_balance+=$balance;
					$tot_batch_qty+=$batch_qty;
					$tot_batch_balance += $batch_balance;
					$tot_dyeing_qty+=$prod_qty;
					
					$tot_tottal_batch_qty+=$batch_qty;
					$tot_total_dyeing_qty+=$prod_qty;					
					$tot_total_order_qty+=$row[csf('order_quantity')];
					$tot_total_order_val+=$row[csf('amount')];
					$tot_total_rec_qty+=$rec_qty;
					$tot_total_issue_qty+=$issue_qty;
					$tot_total_material_blce+=$mat_blnce_qty;
					$tot_total_prod_qty+=$prod_qty;
					$tot_total_del_qty+=$del_qty;
					$tot_total_del_qty_gray+=$gray_qty;
					$tot_total_yet_to_delv+=$yet_to_delv;
					$tot_total_yet_to_bill+=$yet_to_bill;
					$tot_total_bill_qty+=$bill_qty;
					$tot_total_bill_amnt+=$bill_amnt;
					$tot_total_payment_amnt+=$order_wise_payment_received;
					$tot_total_balance+=$balance;
					$grnd_tot_batch_balance += $batch_balance;
				}
				?>
			    	<tr class="tbl_bottom">
				        <td width="500" colspan="5" align="right"><b>Process Total:</b></td>
				        <td width="90" align="right"><? echo number_format($tot_order_qty); ?></td>
				        <td width="60" align="right"></td>
				        <td width="120" align="right"><? echo number_format($tot_rec_qty); ?></td>
				        <td width="120" align="right"><? echo number_format($tot_total_issue_qty); ?></td>
				        <td width="120" align="right"><? echo number_format($tot_material_blce); ?></td>
				        <td width="80" align="right"><? echo number_format($tot_batch_qty); ?></td>
				        <td width="80" align="right"><? echo number_format($tot_batch_balance); ?></td>
				        <td width="80" align="right"><? echo number_format($tot_dyeing_qty) ?></td>			       
				        <td width="80" align="right"><?php echo number_format($tot_order_qty-$tot_dyeing_qty); ?></td>
						<td width="80"><? echo number_format($tot_del_qty_gray); ?></td>
						<td width="80"><? echo number_format($tot_del_qty); ?></td>								
				        <td width="80" align="right"><? echo number_format($tot_yet_to_delv); ?></td>				        
			    	</tr>
			        <tr class="tbl_bottom">
			            <td width="500" colspan="5" align="right">Grand Total:</td>
			            <td width="90" align="right"><? echo number_format($tot_total_order_qty); ?></td>
			            <td width="60" align="right"></td>
			            <td width="120"><? echo number_format($tot_total_rec_qty); ?></td>
			            <td width="120"><? echo number_format($tot_total_issue_qty); ?></td>
			            <td width="120"><? echo number_format($tot_total_material_blce); ?></td>
			            <td width="80"><? echo number_format($tot_tottal_batch_qty); ?></td>
			            <td width="80"><? echo number_format($grnd_tot_batch_balance); ?></td>
			            <td width="80"><? echo number_format($tot_total_dyeing_qty) ?></td>			            
			            <td width="80"><?php echo number_format($tot_total_order_qty-$tot_total_dyeing_qty); ?></td>
	                    <td width="80"><? echo number_format($tot_total_del_qty_gray); ?></td>		                    
	                    <td width="80"><? echo number_format($tot_total_del_qty); ?></td>
			            <td width="80"><? echo number_format($tot_total_yet_to_delv); ?></td>
			        </tr>
			    </table>        
		    </div>
	    </div>
	    <?
	}

	if ($type == 4) //shariar
	{
		//======================== GETTING FORM DATA ===========================
		$job_no=str_replace("'","",$txt_job_no);
		$job_id=str_replace("'","",$txt_job_id);
		$txt_style_ref=str_replace("'","",$txt_style_ref);
		$txt_order_no=str_replace("'","",$txt_order_no);
		$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
		$cbo_process=str_replace("'","",$cbo_process_id);
		if($cbo_buyer_id!=0) $buyer_id_cond=" and a.party_id='$cbo_buyer_id'"; else $buyer_id_cond="";
		if($db_type==0)
		{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
		}
		else
		{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
		}
		$job_no_cond="";
		if($job_id!="" && $job_no!="")
		{
			 $job_no_cond=" and a.id in ($job_id) ";	
		}
		elseif($job_id=="" &&  $job_no!="") {
			if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
		}
		
		if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
		if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
		if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
		
		if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") {$date_cond="";} else {$date_cond=" and c.delivery_date between $txt_date_from and $txt_date_to";
		$date_from = str_replace("'", "", $txt_date_from);
		$date_to = str_replace("'", "", $txt_date_to);
		}
		
		if(str_replace("'","",trim($txt_date_from_rec))=="" && str_replace("'","",trim($txt_date_to_rec))=="") {$date_cond_recv="";} else {$date_cond_recv=" and b.order_rcv_date between $txt_date_from_rec and $txt_date_to_rec";
		$date_from_rec = str_replace("'", "", $txt_date_from_rec);
		$date_to_rec = str_replace("'", "", $txt_date_to_rec);
		}

		// ====================================== MAIN QUERY =====================================
		$job_sql = "SELECT a.id as pic_id,a.job_no_prefix_num, a.subcon_job,a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref 
		from subcon_ord_mst a, subcon_ord_dtls b,subcon_delivery_mst c,subcon_delivery_dtls d
		where a.id=b.mst_id and b.id=d.order_id and c.id=d.mst_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_no_cond $style_ref_cond $date_cond $date_cond_recv $job_no_cond $process_id_cond $buyer_id_cond $year_cond
		group by a.job_no_prefix_num, a.subcon_job, a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref,a.id  order by b.main_process_id, a.job_no_prefix_num, b.order_no, b.delivery_date ";
			//echo $job_sql;
		$job_sql_result=sql_select($job_sql);
		if(count($job_sql_result)==0)
		{
			?>
			<div style="margin: 0 auto;font-size: 20px;color: red;text-align: center;">Data not found! Please try again.</div>
			<?
		}
		$order_id_array = array();
		foreach ($job_sql_result as $val) 
		{
			$order_id_array[$val[csf('id')]] = $val[csf('id')];
		}
		
		$con = connect();
		$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (23)");
		if($r_id)
		{
			oci_commit($con);
		}
		
		if(count($order_id_array)>0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 23, 1,$order_id_array, $empty_arr);
		}
		
		
		//$ordeIDs = implode(",", $order_id_array);
		//$order_cond=where_con_using_array(array_unique($order_id_array),0,"b.order_id");
		// ====================================== FOR MATERIAL RCV ================================
		$inventory_array=array();
		 $inventory_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b , GBL_TEMP_ENGINE e where a.id=b.mst_id and a.trans_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and b.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1  group by b.order_id";
		
		$inventory_sql_result=sql_select($inventory_sql);
		foreach ($inventory_sql_result as $row)
		{
			$inventory_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
		}
		
		// ========================================= FOR ISSUE ======================================
		$inv_iss_array=array();
 		$inv_iss_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b , GBL_TEMP_ENGINE e  where a.id=b.mst_id and a.trans_type=2 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 group by b.order_id";
		
		
		$inv_iss_sql_result=sql_select($inv_iss_sql);
		foreach ($inv_iss_sql_result as $row)
		{
			$inv_iss_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
		}	

		//========================================= FOR RETURN ===============================
		$inventory_ret_array=array();
 		$inv_ret_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b, GBL_TEMP_ENGINE e where a.id=b.mst_id and a.trans_type=3 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1  group by b.order_id";
		
		
		
		$inv_ret_sql_result=sql_select($inv_ret_sql);
		foreach ($inv_ret_sql_result as $row)
		{
			$inventory_ret_array[$row[csf('order_id')]]=$row[csf('quantity')];
		}	//var_dump($inventory_array);

		//========================================= FOR grey prod qty ===============================
		$inventory_grey_prod_array=array();
 		$inv_grey_sql="SELECT b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b, GBL_TEMP_ENGINE e where a.id=b.mst_id and a.trans_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=13 and b.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 group by b.order_id";
		
		$inv_grey_sql_result=sql_select($inv_grey_sql);
		foreach ($inv_grey_sql_result as $row)
		{
			$inventory_grey_prod_array[$row[csf('order_id')]]=$row[csf('quantity')];
		}	//var_dump($inventory_array);

		// ======================================== DELIVERY QTY ================================
		//$del_date_cond = str_replace("c.delivery_date", "a.delivery_date", $date_cond);
		 $delivery_array=array();
		 $delivery_sql="SELECT b.order_id,
		sum(CASE WHEN b.process_id='1' THEN  b.delivery_qty else 0 END) AS cutting,
		sum(CASE WHEN b.process_id='1' THEN  b.reject_qty  else 0 END) AS cutting_rej,

		sum(CASE WHEN b.process_id='2' THEN  b.delivery_qty  else 0 END) AS kniting,
		sum(CASE WHEN b.process_id='2' THEN  b.reject_qty  else 0 END) AS kniting_rej,

		sum(CASE WHEN b.process_id='3' THEN  b.delivery_qty  else 0 END) AS dyeing,
		sum(CASE WHEN b.process_id='3' THEN  b.reject_qty  else 0 END) AS dyeing_rej,

		sum(CASE WHEN b.process_id='4' THEN  b.delivery_qty  else 0 END) AS finishing,
		sum(CASE WHEN b.process_id='4' THEN  b.reject_qty else 0  END) AS finishing_rej,

		sum(CASE WHEN b.process_id='5' THEN  b.delivery_qty  else 0 END) AS sewing,
		sum(CASE WHEN b.process_id='5' THEN  b.reject_qty  else 0 END) AS sewing_rej,

		sum(CASE WHEN b.process_id='6' THEN  b.delivery_qty else 0  END) AS fab_print,
		sum(CASE WHEN b.process_id='6' THEN  b.reject_qty else 0  END) AS fab_print_rej,

		sum(CASE WHEN b.process_id='7' THEN  b.delivery_qty  else 0 END) AS washing,
		sum(CASE WHEN b.process_id='7' THEN  b.reject_qty else 0  END) AS washing_rej,

		sum(CASE WHEN b.process_id='8' THEN  b.delivery_qty  else 0 END) AS printing,
		sum(CASE WHEN b.process_id='8' THEN  b.reject_qty else 0  END) AS printing_rej,

		sum(CASE WHEN b.process_id='9' THEN  b.delivery_qty else 0  END) AS Embroidery,
		sum(CASE WHEN b.process_id='9' THEN  b.reject_qty  else 0 END) AS Embroidery_rej,

		sum(CASE WHEN b.process_id='10' THEN  b.delivery_qty  else 0 END) AS Iron,
		sum(CASE WHEN b.process_id='10' THEN  b.reject_qty else 0  END) AS Iron_rej,

		sum(CASE WHEN b.process_id='11' THEN  b.delivery_qty else 0  END) AS Gmts_Finishing,
		sum(CASE WHEN b.process_id='11' THEN  b.reject_qty  else 0 END) AS Gmts_Finishing_rej,

		sum(CASE WHEN b.process_id='12' THEN  b.delivery_qty else 0  END) AS Gmts_Dyeing,
		sum(CASE WHEN b.process_id='12' THEN  b.reject_qty else 0  END) AS Gmts_Dyeing_rej,

		sum(CASE WHEN b.process_id='13' THEN  b.delivery_qty  else 0 END) AS Poly ,
		sum(CASE WHEN b.process_id='13' THEN  b.reject_qty else 0  END) AS Poly_rej 

		from subcon_delivery_mst a, subcon_delivery_dtls b, GBL_TEMP_ENGINE e where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 $del_date_cond group by b.order_id";
		$delivery_sql_result=sql_select($delivery_sql);
		foreach ($delivery_sql_result as $row)
		{
			$delivery_array[$row[csf('order_id')]]['item_id']=$row[csf('item_id')];
			$delivery_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
			$delivery_array[$row[csf('order_id')]]['cutting_rej']=$row[csf('cutting_rej')];
			$delivery_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
			$delivery_array[$row[csf('order_id')]]['kniting_rej']=$row[csf('kniting_rej')];

			$delivery_array[$row[csf('order_id')]]['dyeing']=$row[csf('dyeing')];
			$delivery_array[$row[csf('order_id')]]['dyeing_rej']=$row[csf('dyeing_rej')];

			$delivery_array[$row[csf('order_id')]]['finishing']=$row[csf('finishing')];
			$delivery_array[$row[csf('order_id')]]['finishing_rej']=$row[csf('finishing_rej')];

			$delivery_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
			$delivery_array[$row[csf('order_id')]]['sewing_rej']=$row[csf('sewing_rej')];

			$delivery_array[$row[csf('order_id')]]['fab_print']=$row[csf('fab_print')];
			$delivery_array[$row[csf('order_id')]]['fab_print_rej']=$row[csf('fab_print_rej')];

			$delivery_array[$row[csf('order_id')]]['washing']=$row[csf('washing')];
			$delivery_array[$row[csf('order_id')]]['washing_rej']=$row[csf('washing_rej')];

			$delivery_array[$row[csf('order_id')]]['printing']=$row[csf('printing')];
			$delivery_array[$row[csf('order_id')]]['printing_rej']=$row[csf('printing_rej')];

			$delivery_array[$row[csf('order_id')]]['Embroidery']=$row[csf('Embroidery')];
			$delivery_array[$row[csf('order_id')]]['Embroidery_rej']=$row[csf('Embroidery_rej')];

			$delivery_array[$row[csf('order_id')]]['Iron']=$row[csf('Iron')];
			$delivery_array[$row[csf('order_id')]]['Iron_rej']=$row[csf('Iron_rej')];

			$delivery_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
			$delivery_array[$row[csf('order_id')]]['Gmts_Finishing_rej']=$row[csf('Gmts_Finishing_rej')];

			$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing']=$row[csf('Gmts_Dyeing')];
			$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing_rej']=$row[csf('Gmts_Dyeing_rej')];

			$delivery_array[$row[csf('order_id')]]['Poly']=$row[csf('Poly')];
			$delivery_array[$row[csf('order_id')]]['Poly_rej']=$row[csf('Poly_rej')];
			
		}
		// ======================================== FAB. PRODUCTION ===============================
		$order_cond=str_replace("b.order_id", "c.order_id", $order_cond);
		$fab_production_array=array();
 		$fab_production_sql="SELECT c.order_id,
		sum(CASE WHEN c.product_type='4' THEN  c.quantity END) AS finishing,
		sum(CASE WHEN c.product_type='8' THEN  c.quantity END) AS printing
		from  subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c, GBL_TEMP_ENGINE e where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 group by c.order_id";
		$fab_production_sql_result=sql_select($fab_production_sql);
		foreach ($fab_production_sql_result as $row)
		{
			$order_id=explode(',',$row[csf('order_id')]);
			foreach ($order_id as $val)
			{
				$fab_production_array[$val]['fabric_description']=$row[csf('fabric_description')];
				$fab_production_array[$val]['finishing']=$row[csf('finishing')];
				$fab_production_array[$val]['printing']=$row[csf('printing')];
			}
		}
		
		// ================================== FOR KNITING PRODUCTION ==================================
		$ordeIDsKnit = "'".implode("','", $order_id_array)."'";
		$knit_production_array=array();
		//$order_cond=str_replace("c.order_id", "b.order_id", $order_cond);
 		$knit_production_sql="SELECT b.order_id, sum(b.product_qnty) AS kniting
		from  subcon_production_mst a, subcon_production_dtls b, GBL_TEMP_ENGINE e where a.id=b.mst_id and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1  group by b.order_id";
		$knit_production_sql_result=sql_select($knit_production_sql);
		foreach ($knit_production_sql_result as $row)
		{
			$knit_production_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
		}	
		//var_dump ($fab_production_array);

		//==================================== FOR DYEING PRODUCTION =====================================
		//$order_cond=str_replace("b.order_id", "c.po_id", $order_cond);
		$dying_data_array=array();
		if ($db_type==0)
		{
			$dying_sql="SELECT c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c, GBL_TEMP_ENGINE e where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 group by c.po_id ";
		}
		elseif($db_type==2)
		{
			$dying_sql="SELECT c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c, GBL_TEMP_ENGINE e where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1  group by c.po_id ";
		}
		$dying_sql_result=sql_select($dying_sql);
		foreach ($dying_sql_result as $row)
		{
			$dying_data_array[$row[csf('po_id')]]=$row[csf('production_qnty')];
		}
		//var_dump($dying_data_array);
		
		// ======================================= FOR GMTS PRODUCTION ===============================
		//$order_cond=str_replace("c.po_id", "order_id", $order_cond);
		$gmt_production_array=array();
		$gmt_production_sql="SELECT order_id,
		sum(CASE WHEN production_type='1' THEN  production_qnty END) AS cutting,
		sum(CASE WHEN production_type='2' THEN  production_qnty END) AS sewing,
		sum(CASE WHEN production_type='4' THEN  production_qnty END) AS Gmts_Finishing
		from subcon_gmts_prod_dtls, GBL_TEMP_ENGINE e where status_active=1 and is_deleted=0 and order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 group by order_id";
		$gmt_production_sql_result=sql_select($gmt_production_sql);
		foreach ($gmt_production_sql_result as $row)
		{
			$gmt_production_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
			$gmt_production_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
			$gmt_production_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
		}
		//var_dump($gmt_production_array);

		//========================================== FOR BILL =====================================
		//$order_cond=str_replace("order_id", "b.order_id", $order_cond);
		$in_bill_qty_array=array();
		$in_bill_amnt_array=array(); $bill_order_arr=array(); $bill_amt_arr=array();$bill_number_arr=array();$mst_arr=array();$bill_arr=array(); 
		 $in_bill_sql="SELECT a.id,a.prefix_no_num,a.bill_no,a.party_source,b.order_id, b.mst_id, sum(b.amount) as amount,
		sum(CASE WHEN b.process_id='1' THEN  b.delivery_qty END) AS cutting,
		sum(CASE WHEN b.process_id='2' THEN  b.delivery_qty END) AS kniting,
		sum(CASE WHEN b.process_id='3' THEN  b.delivery_qty END) AS dyeing,
		sum(CASE WHEN b.process_id='4' THEN  b.delivery_qty END) AS finishing,
		sum(CASE WHEN b.process_id='5' THEN  b.delivery_qty END) AS sewing,
		sum(CASE WHEN b.process_id='6' THEN  b.delivery_qty END) AS fab_print,
		sum(CASE WHEN b.process_id='7' THEN  b.delivery_qty END) AS washing,
		sum(CASE WHEN b.process_id='8' THEN  b.delivery_qty END) AS printing,
		sum(CASE WHEN b.process_id='9' THEN  b.delivery_qty END) AS Embroidery,
		sum(CASE WHEN b.process_id='10' THEN  b.delivery_qty END) AS Iron,
		sum(CASE WHEN b.process_id='11' THEN  b.delivery_qty END) AS Gmts_Finishing,
		sum(CASE WHEN b.process_id='12' THEN  b.delivery_qty END) AS Gmts_Dyeing,
		sum(CASE WHEN b.process_id='13' THEN  b.delivery_qty END) AS Poly,
		
		sum(CASE WHEN b.process_id='1' THEN  b.amount END) AS am_cutting,
		sum(CASE WHEN b.process_id='2' THEN  b.amount END) AS am_kniting,
		sum(CASE WHEN b.process_id='3' THEN  b.amount END) AS am_dyeing,
		sum(CASE WHEN b.process_id='4' THEN  b.amount END) AS am_finishing,
		sum(CASE WHEN b.process_id='5' THEN  b.amount END) AS am_sewing,
		sum(CASE WHEN b.process_id='6' THEN  b.amount END) AS am_fab_print,
		sum(CASE WHEN b.process_id='7' THEN  b.amount END) AS am_washing,
		sum(CASE WHEN b.process_id='8' THEN  b.amount END) AS am_printing,
		sum(CASE WHEN b.process_id='9' THEN  b.amount END) AS am_Embroidery,
		sum(CASE WHEN b.process_id='10' THEN  b.amount END) AS am_Iron,
		sum(CASE WHEN b.process_id='11' THEN  b.amount END) AS am_Gmts_Finishing,
		sum(CASE WHEN b.process_id='12' THEN  b.amount END) AS am_Gmts_Dyeing,
		sum(CASE WHEN b.process_id='13' THEN  b.amount END) AS am_Poly		
		from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, GBL_TEMP_ENGINE e where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and b.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 group by a.id,a.prefix_no_num,a.bill_no,a.party_source,b.order_id, b.mst_id order by a.prefix_no_num ";
		$in_bill_sql_result=sql_select($in_bill_sql);
		foreach ($in_bill_sql_result as $row)
		{
			$in_bill_qty_array[$row[csf('order_id')]]['cutting']+=$row[csf('cutting')];
			$in_bill_qty_array[$row[csf('order_id')]]['kniting']+=$row[csf('kniting')];
			$in_bill_qty_array[$row[csf('order_id')]]['dyeing']+=$row[csf('dyeing')];
			$in_bill_qty_array[$row[csf('order_id')]]['finishing']+=$row[csf('finishing')];
			$in_bill_qty_array[$row[csf('order_id')]]['sewing']+=$row[csf('sewing')];
			$in_bill_qty_array[$row[csf('order_id')]]['fab_print']+=$row[csf('fab_print')];
			$in_bill_qty_array[$row[csf('order_id')]]['washing']+=$row[csf('washing')];
			$in_bill_qty_array[$row[csf('order_id')]]['printing']+=$row[csf('printing')];

			$in_bill_qty_array[$row[csf('order_id')]]['Embroidery']+=$row[csf('Embroidery')];
			$in_bill_qty_array[$row[csf('order_id')]]['Iron']+=$row[csf('Iron')];
			$in_bill_qty_array[$row[csf('order_id')]]['Gmts_Finishing']+=$row[csf('Gmts_Finishing')];
			$in_bill_qty_array[$row[csf('order_id')]]['Gmts_Dyeing']+=$row[csf('Gmts_Dyeing')];
			$in_bill_qty_array[$row[csf('order_id')]]['Poly']+=$row[csf('Poly')];
			
			$in_bill_amnt_array[$row[csf('order_id')]]['am_cutting']+=$row[csf('am_cutting')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_kniting']+=$row[csf('am_kniting')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_dyeing']+=$row[csf('am_dyeing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_finishing']+=$row[csf('am_finishing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_sewing']+=$row[csf('am_sewing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_fab_print']+=$row[csf('am_fab_print')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_washing']+=$row[csf('am_washing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_printing']+=$row[csf('am_printing')];

			$in_bill_amnt_array[$row[csf('order_id')]]['am_Embroidery']+=$row[csf('am_Embroidery')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Iron']+=$row[csf('am_Iron')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Gmts_Finishing']+=$row[csf('am_Gmts_Finishing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Gmts_Dyeing']+=$row[csf('am_Gmts_Dyeing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Poly']+=$row[csf('am_Poly')];

			$bill_order_arr[$row[csf('mst_id')]].=$row[csf('order_id')].',';
			$bill_amt_arr[$row[csf('order_id')]]+=$row[csf('amount')];
			$bill_number_arr[$row[csf('order_id')]] .=$row[csf('prefix_no_num')].",";
			
			$mst_arr[$row[csf('order_id')]].=$row[csf('id')].",";
			$bill_arr[$row[csf('order_id')]]=$row[csf('bill_no')];
			
			
			$mst_bill_id_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
			$mst_bill_id_arr[$row[csf('id')]]['prfix_bill_no']=$row[csf('prefix_no_num')];
			$mst_order_no_arr[$row[csf('order_id')]] .=$row[csf('bill_no')].",";
		}
			

		$order_wise_tot_paid_arr=array();
		$order_wise_tot_bill_arr2=array();
		$order_wise_tot_bill_arr=array();
		//$order_cond=str_replace("b.order_id", "d.order_id", $order_cond);
 		$order_wise_tot_paid="SELECT d.order_id, b.total_adjusted as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d, GBL_TEMP_ENGINE e  where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 group by d.order_id,b.total_adjusted";

		$order_wise_tot_paid_result=sql_select($order_wise_tot_paid);
		foreach ($order_wise_tot_paid_result as $row)
		{
			$order_wise_tot_paid_arr[$row[csf('order_id')]]+=$row[csf('rec_amount')];
		}

		 $order_wise_tot_bill="SELECT a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d, GBL_TEMP_ENGINE e where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.order_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1 order by a.id";
		$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
		foreach ($order_wise_tot_bill_result as $row)
		{
			$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
		}

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

		//var_dump($po_wise_payRec_arr);
		$order_cond=str_replace("d.order_id", "b.po_id", $order_cond);
		$batch_qty_array=array();
		 $sql_batch="SELECT b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, GBL_TEMP_ENGINE e where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id=e.ref_val and e.entry_form=23 and e.user_id=$user_id and e.ref_from=1  group by b.po_id";
		$sql_batch_result=sql_select($sql_batch);
		foreach ($sql_batch_result as $row)
		{
			$batch_qty_array[$row[csf('po_id')]]=$row[csf('batch_qnty')];
		}
		
		$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (23)");
			if($r_id)
			{
				oci_commit($con);
			}
			disconnect($con);
		ob_start();
		if ($cbo_process==4)
		{
			$tbl_width=2350;
			$col_span=27;
		}
		else
		{
			$tbl_width=2110;
			$col_span=24;
		}
		//$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
		?>
	    <div>
	        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="70">Job No</th>
	                <th width="100">Party</th>
	                <th width="80">Order no</th>
	                <th width="50">Image</th>                            
	                <th width="100">Style Name</th>
	                <th width="90">Order Quantity</th>
	                <th width="100">Order Value</th>
	                <th width="60">UOM</th>
	                <th width="90">Delivery Date</th>
	                <th width="60">Days in Hand</th>
	                <th width="120">Material Receive</th>
	                <th width="120">Material Issue</th>
	                <th width="120">Material Return</th>
	                <th width="120">Material Balance</th>
	                <?
						if ($cbo_process==4)
						{
							?>
	                        <th width="80">Batch Qty</th>
	                        <th width="80">Batch Bal.</th>
	                        <th width="80">Dyeing Qty</th>
	                        <?
						}
					?>
	                <th width="80">Prod. Finish Qty</th>
	                <th width="80">Delivery Qty</th>
	                <th width="80">Reject Qty</th>

	                <th width="80">Yet To Delv.</th>
					<th width="120">Bill No</th>
	                <th width="80">Bill Qty</th>
	                <th width="80">Bill Amount</th>

	                <th width="100">Payment Rec.</th>
	                <th>Rec. Balance</th>
	            </thead>
	        </table>
		    <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
		        <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" id="">
			    <?
				$process_array=array();
				$i=1; $k=1;
				foreach ($job_sql_result as $row)
				{
					if (!in_array($row[csf("main_process_id")],$process_array) )
					{
						if($k!=1)
						{
						?>
							<tr class="tbl_bottom">
								<td colspan="6" align="right"><b>Process Total:</b></td>
								<td align="right"><? echo number_format($tot_order_qty); ?></td>
								<td align="right"><? echo number_format($tot_order_val); ?></td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right"><? echo number_format($tot_rec_qty); ?></td>
			                    <td align="right"><? echo number_format($tot_issue_qty); ?></td>
			                    <td align="right"><? echo number_format($tot_return_qty); ?></td>
			                    <td align="right">
			                    	<? 
			                    		$tot_material_blce=$tot_rec_qty-$tot_issue_qty;
			                    		echo $tot_material_blce;
			                     	?>
			                     </td>
			                    <?
									if ($cbo_process==4)
									{
										?>
			                                <td><? echo number_format($tot_batch_qty); ?></td>
			                                <td><? echo number_format($tot_batch_bal_qty); ?></td>
			                                <td><? echo number_format($tot_dyeing_qty); ?></td>
										<?
									}
								?>
								<td align="right"><? echo number_format($tot_prod_qty); ?></td>
								<td align="right"><? echo number_format($tot_del_qty); ?></td>
								<td align="right"><? echo number_format($tot_del_rej); ?></td>
			                    <td align="right"><? echo number_format($tot_yet_to_delv); ?></td>
								<td align="right"><? //echo number_format($tot_yet_to_delv); ?></td>
								<td align="right"><? echo number_format($tot_bill_qty); ?></td>
								<td align="right"><? echo number_format($tot_bill_amnt); ?></td>
			                
								<td align="right"><? echo number_format($tot_payment_amnt); ?></td>
			                    <td align="right"><? echo number_format($tot_balance); ?></td>
							</tr>
							<tr bgcolor="#dddddd">
								<td colspan="<? echo $col_span; ?>" align="left" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
							</tr>
						<?
							unset($tot_order_qty);
							unset($tot_order_val);
							unset($tot_rec_qty);
							unset($tot_issue_qty);
							unset($tot_material_blce);
							if ($cbo_process==4)
							{
								unset($tot_batch_qty);
								unset($tot_dyeing_qty);
							}
							unset($tot_prod_qty);
							unset($tot_del_qty);
							unset($tot_del_rej);
							unset($tot_bill_qty);
							unset($tot_bill_amnt);
							unset($tot_payment_amnt);

								unset($tot_yet_to_delv);


							unset($tot_balance);
						}
						else
						{
							?>
							<tr bgcolor="#dddddd">
								<td colspan="<? echo $col_span; ?>" align="left" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
							</tr>
							<?
						}					
						$process_array[]=$row[csf('main_process_id')];            
						$k++;
					}
			        if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
					$prod_qty=0; $del_qty=0; $bill_qty=0; $bill_amnt=0; $pay_rec=0;
					$del_rej=0;
					if ($row[csf('main_process_id')]==1)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['cutting'];
						$del_qty=$delivery_array[$row[csf('id')]]['cutting'];
						$del_rej=$delivery_array[$row[csf('id')]]['cutting_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['cutting'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_cutting'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==2)
					{
						$prod_qty=$knit_production_array[$row[csf('id')]]['kniting'];
						$del_qty=$delivery_array[$row[csf('id')]]['kniting'];
						$del_rej=$delivery_array[$row[csf('id')]]['kniting_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['kniting'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_kniting'];
						$batch_qty=""; $dyeing_qty="";
						//echo $row[csf('main_process_id')].'K';
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==3)
					{
						$prod_qty=$dying_data_array[$row[csf('id')]];
						$del_qty=$delivery_array[$row[csf('id')]]['dyeing'];
						$del_rej=$delivery_array[$row[csf('id')]]['dyeing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['dyeing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_dyeing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==4)
					{
						$prod_qty=$fab_production_array[$row[csf('id')]]['finishing'];
						$del_qty=$delivery_array[$row[csf('id')]]['finishing'];
						$del_rej=$delivery_array[$row[csf('id')]]['finishing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['finishing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_finishing'];
						$batch_qty=$batch_qty_array[$row[csf('id')]];
						$dyeing_qty=$dying_data_array[$row[csf('id')]];
						$grey_prod = $inventory_grey_prod_array[$row[csf('id')]];
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==5)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['sewing'];
						$del_qty=$delivery_array[$row[csf('id')]]['sewing'];
						$del_rej=$delivery_array[$row[csf('id')]]['sewing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['sewing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_sewing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==6)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['fab_print'];
						$del_qty=$delivery_array[$row[csf('id')]]['fab_print'];
						$del_rej=$delivery_array[$row[csf('id')]]['fab_print_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['fab_print'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_fab_print'];

						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==7)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['washing'];
						$del_qty=$delivery_array[$row[csf('id')]]['washing'];
						$del_rej=$delivery_array[$row[csf('id')]]['washing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['washing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_washing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==8)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['printing'];
						$del_qty=$delivery_array[$row[csf('id')]]['printing'];
						$del_rej=$delivery_array[$row[csf('id')]]['printing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['printing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_printing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}

					else if ($row[csf('main_process_id')]==9)
					{
						$del_qty=$delivery_array[$row[csf('id')]]['Embroidery'];
						$del_rej=$delivery_array[$row[csf('id')]]['Embroidery_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Embroidery'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Embroidery'];
						$batch_qty=""; $dyeing_qty="";
					}

					else if ($row[csf('main_process_id')]==10)
					{
						$del_qty=$delivery_array[$row[csf('id')]]['Iron'];
						$del_rej=$delivery_array[$row[csf('id')]]['Iron_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Iron'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Iron'];
						$batch_qty=""; $dyeing_qty="";
					}

					else if ($row[csf('main_process_id')]==11)
					{	
						$prod_qty=$gmt_production_array[$row[csf('id')]]['Gmts_Finishing'];
						$del_qty=$delivery_array[$row[csf('id')]]['Gmts_Finishing'];
						$del_rej=$delivery_array[$row[csf('id')]]['Gmts_Finishing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Gmts_Finishing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Finishing'];
						$batch_qty=""; $dyeing_qty="";
					}

					else if ($row[csf('main_process_id')]==12)
					{
						$del_qty=$delivery_array[$row[csf('id')]]['Gmts_Dyeing'];
						$del_rej=$delivery_array[$row[csf('id')]]['Gmts_Dyeing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Gmts_Dyeing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Dyeing'];
						$batch_qty=""; $dyeing_qty="";
					}

					else if ($row[csf('main_process_id')]==13)
					{
						$del_qty=$delivery_array[$row[csf('id')]]['Poly'];
						$del_rej=$delivery_array[$row[csf('id')]]['Poly_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Poly'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Poly'];
						$batch_qty=""; $dyeing_qty="";
					}
					
					
					$rec_qty 		= $inventory_array[$row[csf('id')]]['quantity']-$inventory_ret_array[$row[csf('id')]];
					$issue_qty 		= $inv_iss_array[$row[csf('id')]]['quantity'];
					$return_qty 	= $inventory_ret_array[$row[csf('id')]];
					//$bill_no_array  = array_unique(explode(',',$bill_number_arr[$row[csf('id')]]));					
					$mst_noData   	    = rtrim($mst_arr[$row[csf('id')]],',');
					$bill_no_array  = array_unique(explode(',',$mst_noData));		
						
					$bill_no   	    = $bill_arr[$row[csf('id')]];
					$party_source   = $party_source_arr[$row[csf('id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">	
			        	<td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
			            <td width="70" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></td>
			            <td width="100"><p><? echo $party_arr[$row[csf('party_id')]]; ?></p></td>
			            <td width="80"><p><? echo $row[csf('order_no')]; ?></p></td>
			            <td width="50"><img onclick="openImageWindow( <? echo $row[csf('pic_id')];//$row[csf('job_no_prefix_num')]; ?> )" src='../../<? echo $imge_arr[$row[csf('pic_id')]]; ?>' height='25' width='30' /></td>
			            <td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
			            
			            <td width="90" align="right"><p>
			            	<a href="##" onclick="show_progress_report_details('order_desc_popup','<? echo $row[csf("id")]; ?>','1150px')">
			            	<? echo number_format($row[csf('order_quantity')],2); ?>
			            	</a>
			            </p></td>

			            <td width="100" align="right"><p><? echo number_format($row[csf('amount')],2); ?></p></td>
			            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
			            <td width="90"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
			            <td width="60" align="center"><? $daysOnHand = datediff("d",date("Y-m-d"),$row[csf('delivery_date')]); echo $daysOnHand; ?> </td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_popup','<? echo $row[csf("id")]; ?>','1150px')"><? echo number_format($rec_qty,2); ?></a></p></td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_iss_popup','<? echo $row[csf("id")]; ?>','1150px')"><? echo number_format($issue_qty,2); ?></a></p></td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_rtn_popup','<? echo $row[csf("id")]; ?>','1300px')"><? echo number_format($return_qty,2); ?></a></p></td>
			            <td width="120" align="right">
			            	<?
			            		$mat_blnce_qty=$rec_qty-$issue_qty;
			            		echo number_format($mat_blnce_qty,2);
			            	?>
			            </td>
						<?
			                if ($cbo_process==4)
			                {
			                	$batch_bal_qty = $issue_qty - $batch_qty;
			                    ?>
			                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('batch_qty_pop_up','<? echo $row[csf("id")]; ?>_summary','1150px')"><? echo number_format($batch_qty,2); ?></a></p></td>
			                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('batch_qty_pop_up','<? echo $row[csf("id")]; ?>_summary','1150px')"><? echo number_format($batch_bal_qty,2); ?></a></p></td>
			                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo '3'; ?>','1150px')"><? echo number_format($dyeing_qty,2); ?></a></p></td>
			                    <?
			                }
			            ?>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','1150px')"><? echo number_format($prod_qty,2); ?></a></p></td>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('delivery_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>_<?echo $date_from;?>_<?echo $date_to;?>','1150px')"><? echo number_format($del_qty,2); ?></a></p></td>
			            <td width="80" align="right"><p><? echo number_format($del_rej,2); ?></p></td>
			            <td width="80" align="right"><? $yet_to_delv=$row[csf('order_quantity')]-$del_qty; echo  number_format($yet_to_delv,2); ?></td>
						<td width="120" align="center"></p>
						<?
						foreach($bill_no_array as $bill_mst_id)
						{
							if($bill_mst_id!="")
							{
								//echo $bill_mst_id.'D';
								$bill_no=$mst_bill_id_arr[$bill_mst_id]['bill_no'];
								$prfix_bill_no=$mst_bill_id_arr[$bill_mst_id]['prfix_bill_no'];
							?>
							<a href="#report_details" onclick="fabric_finishing('<? echo $row[csf('company_id')];?>','<? echo $bill_mst_id; ?>','<? echo $bill_no;?>','<? echo $row[csf('main_process_id')];?>')"><? echo $prfix_bill_no."  ,";?></a>
							<?
							}
							

						} ?>
					</td> 

			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('bill_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','1150px')"><? echo number_format($bill_qty,2); ?></a></p></td>
			            <td width="80" align="right"><? echo  number_format($bill_amnt,2); ?></td>

			            <td width="100" align="right">
			            	<p><a href="##" onclick="show_progress_report_details('payment_rec_pop_up','<? echo $row[csf("id")].'_'.$bill_amnt; ?>_<? echo $row[csf('main_process_id')]; ?>','1150px')">
			            		<? 
			            		$order_wise_payment_received = ($order_wise_tot_bill_arr[$row[csf('id')]]) ? ($order_wise_tot_paid_arr[$row[csf('id')]]/$order_wise_tot_bill_arr[$row[csf('id')]])*$bill_amnt : 0;
			            		echo number_format($order_wise_payment_received,2);
			            		?>
			            	</a></p>
			            </td>
			            

			            <td width="80" align="right"><? $balance=$bill_amnt-$order_wise_payment_received; echo  number_format($balance,2); ?></td>
			        </tr>
			        <?
					$i++;
					$tot_order_qty+=$row[csf('order_quantity')];
					$tot_order_val+=$row[csf('amount')];
					$tot_rec_qty+=$rec_qty;
					$tot_issue_qty+=$issue_qty;
					$tot_return_qty+=$return_qty;
					$tot_material_blce+=$mat_blnce_qty;
					$tot_prod_qty+=$prod_qty;
					$tot_del_qty+=$del_qty;
					$tot_grey_prod+=$grey_prod;
					$tot_del_rej+=$del_rej;
					$tot_yet_to_delv+=$yet_to_delv;
					$tot_bill_qty+=$bill_qty;
					$tot_bill_amnt+=$bill_amnt;
					$tot_payment_amnt+=$order_wise_payment_received;
					$tot_balance+=$balance;
					
					if ($cbo_process==4)
					{
						$tot_batch_qty+=$batch_qty;
						$tot_batch_bal_qty+=$batch_bal_qty;
						$tot_dyeing_qty+=$dyeing_qty;
						
						$tot_tottal_batch_qty+=$batch_qty;
						$tot_tottal_batch_bal_qty+=$batch_bal_qty;
						$tot_total_dyeing_qty+=$dyeing_qty;
					}
					
					$tot_total_order_qty+=$row[csf('order_quantity')];
					$tot_total_order_val+=$row[csf('amount')];
					$tot_total_rec_qty+=$rec_qty;
					$tot_total_issue_qty+=$issue_qty;
					$tot_total_return_qty+=$return_qty;
					$tot_total_material_blce+=$mat_blnce_qty;
					$tot_total_prod_qty+=$prod_qty;
					$tot_total_del_qty+=$del_qty;
					$tot_total_grey_prod+=$grey_prod;
					$tot_total_del_rej+=$del_rej;
					$tot_total_yet_to_delv+=$yet_to_delv;
					$tot_total_bill_qty+=$bill_qty;
					$tot_total_bill_amnt+=$bill_amnt;
					$tot_total_payment_amnt+=$order_wise_payment_received;
					$tot_total_balance+=$balance;
				}
				?>
			    	<tr class="tbl_bottom">
				        <td colspan="6" align="right"><b>Process Total:</b></td>
				        <td align="right"><? echo number_format($tot_order_qty); ?></td>
				        <td align="right"><? echo number_format($tot_order_val); ?></td>
				        <td align="right">&nbsp;</td>
				        <td align="right">&nbsp;</td>
				        <td align="right">&nbsp;</td>
				        <td align="right"><? echo number_format($tot_rec_qty); ?></td>
				        <td align="right"><? echo number_format($tot_issue_qty); ?></td>
				        <td align="right"><? echo number_format($tot_return_qty); ?></td>
				        <td align="right"><? echo number_format($tot_material_blce) ?></td>
				        <?
							if ($cbo_process==4)
							{
								?>
								<td><? echo number_format($tot_batch_qty); ?></td>
								<td><? echo number_format($tot_batch_bal_qty); ?></td>
								<td><? echo number_format($tot_dyeing_qty); ?></td>
								<?
							}
				        ?>
				        <td align="right"><? echo number_format($tot_prod_qty); ?></td>
				        <td align="right"><? echo number_format($tot_del_qty); ?></td>
				        <td align="right"><? echo number_format($tot_del_rej); ?></td>
				        <td align="right"><? echo number_format($tot_yet_to_delv); ?></td>
						<td align="right"><? //echo number_format($tot_yet_to_delv); ?></td>
				        <td align="right"><? echo number_format($tot_bill_qty); ?></td>
				        <td align="right"><? echo number_format($tot_bill_amnt); ?></td>
				        <td align="right"><? echo number_format($tot_payment_amnt); ?></td>
				        <td align="right"><? echo number_format($tot_balance); ?></td>
			    	</tr>
			    	<!-- ====================================== bottom part ============================ -->
			        <tr class="tbl_bottom">
			            <td colspan="6" align="right">Grand Total:</td>
			            <td align="right"><? echo number_format($tot_total_order_qty); ?></td>                            
			            <td align="right"><? echo number_format($tot_total_order_val); ?></td>
			            <td>&nbsp;</td>
			            <td>&nbsp;</td>
			            <td>&nbsp;</td>
			            <td><? echo number_format($tot_total_rec_qty); ?></td>
			            <td><? echo number_format($tot_total_issue_qty); ?></td>
			            <td><? echo number_format($tot_total_return_qty); ?></td>
			            <td><? echo number_format($tot_total_material_blce) ?></td>
			            <?
			                if ($cbo_process==4)
			                {
			                    ?>
			                    <td><? echo number_format($tot_tottal_batch_qty); ?></td>
			                    <td><? echo number_format($tot_tottal_batch_bal_qty); ?></td>
			                    <td><? echo number_format($tot_total_dyeing_qty); ?></td>
			                    <? 
			                }
			            ?>
			            <td><? echo number_format($tot_total_prod_qty); ?></td>
			            <td><? echo number_format($tot_total_del_qty); ?></td>
			            <td><? echo number_format($tot_total_del_rej); ?></td>
			            <td><? echo number_format($tot_total_yet_to_delv); ?></td>
						<td><? //echo number_format($tot_yet_to_delv); ?></td>
			            <td><? echo number_format($tot_total_bill_qty); ?></td>
			            <td><? echo number_format($tot_total_bill_amnt); ?></td>
			            <td><? echo number_format($tot_total_payment_amnt); ?></td>
			            <td><? echo number_format($tot_total_balance); ?></td>
			        </tr>
			    </table>        
		    </div>
	    </div>
	    <?
	}
	if ($type == 5) //Azizur Rahman
	{
		//======================== GETTING FORM DATA ===========================
		$job_no=str_replace("'","",$txt_job_no);
		$job_id=str_replace("'","",$txt_job_id);
		$txt_style_ref=str_replace("'","",$txt_style_ref);
		$txt_order_no=str_replace("'","",$txt_order_no);
		$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
		$cbo_process=str_replace("'","",$cbo_process_id);
		if($cbo_buyer_id!=0) $buyer_id_cond=" and a.party_id='$cbo_buyer_id'"; else $buyer_id_cond="";
		if($db_type==0)
		{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
		}
		else
		{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
		}
		
	 
		$job_no_cond="";
		if($job_id!="" && $job_no!="")
		{
			 $job_no_cond=" and a.id in ($job_id) ";	
		}
		elseif($job_id=="" &&  $job_no!="") {
			if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
		}
		
		if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
		if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
		if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
		
		if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") {$date_cond="";} else {$date_cond=" and c.delivery_date between $txt_date_from and $txt_date_to";
		$date_from = str_replace("'", "", $txt_date_from);
		$date_to = str_replace("'", "", $txt_date_to);
		}
		
		if(str_replace("'","",trim($txt_date_from_rec))=="" && str_replace("'","",trim($txt_date_to_rec))=="") {$date_cond_recv="";} else {$date_cond_recv=" and b.order_rcv_date between $txt_date_from_rec and $txt_date_to_rec";
		$date_from_rec = str_replace("'", "", $txt_date_from_rec);
		$date_to_rec = str_replace("'", "", $txt_date_to_rec);
		}

		// ====================================== MAIN QUERY =====================================
		$job_sql = "SELECT a.id as pic_id,a.job_no_prefix_num, a.subcon_job,a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref ,d.color_id
		from subcon_ord_mst a, subcon_ord_dtls b,subcon_delivery_mst c,subcon_delivery_dtls d
		where a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.id=d.order_id and c.id=d.mst_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_no_cond $style_ref_cond $date_cond $date_cond_recv $job_no_cond $process_id_cond $buyer_id_cond $year_cond
		group by a.job_no_prefix_num, a.subcon_job, a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref,a.id , d.color_id order by b.main_process_id, a.job_no_prefix_num, b.order_no, b.delivery_date ";
			//echo $job_sql;
		$job_sql_result=sql_select($job_sql);
		if(count($job_sql_result)==0)
		{
			?>
			<div style="margin: 0 auto;font-size: 20px;color: red;text-align: center;">Data not found! Please try again.</div>
			<?
		}
		$order_id_array = array();
		foreach ($job_sql_result as $val) 
		{
			$order_id_array[$val[csf('id')]] = $val[csf('id')];
		}
		$ordeIDs = implode(",", $order_id_array);
		$order_cond=where_con_using_array(array_unique($order_id_array),0,"b.order_id");
		// ====================================== FOR MATERIAL RCV ================================
		$inventory_array=array();
		$inventory_sql="SELECT b.order_id,b.color_id, (b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=2 and b.is_deleted=0 $order_cond ";
		
		$inventory_sql_result=sql_select($inventory_sql);
		foreach ($inventory_sql_result as $row) 
		{
			$inventory_array[$row[csf('order_id')]][$row[csf('color_id')]]['quantity']+=$row[csf('quantity')];
		}
		
		// ========================================= FOR ISSUE ======================================
		$inv_iss_array=array();
		$inv_iss_sql="SELECT b.order_id, (b.quantity) as quantity,b.color_id from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond ";
		$inv_iss_sql_result=sql_select($inv_iss_sql);
		foreach ($inv_iss_sql_result as $row)
		{
			$inv_iss_array[$row[csf('order_id')]][$row[csf('color_id')]]['quantity']+=$row[csf('quantity')];
		}	

		//========================================= FOR RETURN ===============================
		$inventory_ret_array=array();
		$inv_ret_sql="SELECT b.order_id, b.color_id,(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond ";
		
		
		$inv_ret_sql_result=sql_select($inv_ret_sql);
		foreach ($inv_ret_sql_result as $row)
		{
			$inventory_ret_array[$row[csf('order_id')]][$row[csf('color_id')]]+=$row[csf('quantity')];
		}	//var_dump($inventory_array);

		//========================================= FOR grey prod qty ===============================
		$inventory_grey_prod_array=array();
		$inv_grey_sql="SELECT b.order_id, b.color_id,(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=13 $order_cond ";
		
		$inv_grey_sql_result=sql_select($inv_grey_sql);
		foreach ($inv_grey_sql_result as $row)
		{
			$inventory_grey_prod_array[$row[csf('order_id')]][$row[csf('color_id')]]+=$row[csf('quantity')];
		}	//var_dump($inventory_array);

		// ======================================== DELIVERY QTY ================================
		$del_date_cond = str_replace("c.delivery_date", "a.delivery_date", $date_cond);
		$delivery_array=array();
		$delivery_sql="SELECT b.order_id,b.color_id,
		sum(CASE WHEN b.process_id='1' THEN  b.delivery_qty else 0 END) AS cutting,
		sum(CASE WHEN b.process_id='1' THEN  b.reject_qty  else 0 END) AS cutting_rej,

		sum(CASE WHEN b.process_id='2' THEN  b.delivery_qty  else 0 END) AS kniting,
		sum(CASE WHEN b.process_id='2' THEN  b.reject_qty  else 0 END) AS kniting_rej,

		sum(CASE WHEN b.process_id='3' THEN  b.delivery_qty  else 0 END) AS dyeing,
		sum(CASE WHEN b.process_id='3' THEN  b.reject_qty  else 0 END) AS dyeing_rej,

		sum(CASE WHEN b.process_id='4' THEN  b.delivery_qty  else 0 END) AS finishing,
		sum(CASE WHEN b.process_id='4' THEN  b.gray_qty  else 0 END) AS gray_qty,
		sum(CASE WHEN b.process_id='4' THEN  b.reject_qty else 0  END) AS finishing_rej,

		sum(CASE WHEN b.process_id='5' THEN  b.delivery_qty  else 0 END) AS sewing,
		sum(CASE WHEN b.process_id='5' THEN  b.reject_qty  else 0 END) AS sewing_rej,

		sum(CASE WHEN b.process_id='6' THEN  b.delivery_qty else 0  END) AS fab_print,
		sum(CASE WHEN b.process_id='6' THEN  b.reject_qty else 0  END) AS fab_print_rej,

		sum(CASE WHEN b.process_id='7' THEN  b.delivery_qty  else 0 END) AS washing,
		sum(CASE WHEN b.process_id='7' THEN  b.reject_qty else 0  END) AS washing_rej,

		sum(CASE WHEN b.process_id='8' THEN  b.delivery_qty  else 0 END) AS printing,
		sum(CASE WHEN b.process_id='8' THEN  b.reject_qty else 0  END) AS printing_rej,

		sum(CASE WHEN b.process_id='9' THEN  b.delivery_qty else 0  END) AS Embroidery,
		sum(CASE WHEN b.process_id='9' THEN  b.reject_qty  else 0 END) AS Embroidery_rej,

		sum(CASE WHEN b.process_id='10' THEN  b.delivery_qty  else 0 END) AS Iron,
		sum(CASE WHEN b.process_id='10' THEN  b.reject_qty else 0  END) AS Iron_rej,

		sum(CASE WHEN b.process_id='11' THEN  b.delivery_qty else 0  END) AS Gmts_Finishing,
		sum(CASE WHEN b.process_id='11' THEN  b.reject_qty  else 0 END) AS Gmts_Finishing_rej,

		sum(CASE WHEN b.process_id='12' THEN  b.delivery_qty else 0  END) AS Gmts_Dyeing,
		sum(CASE WHEN b.process_id='12' THEN  b.reject_qty else 0  END) AS Gmts_Dyeing_rej,

		sum(CASE WHEN b.process_id='13' THEN  b.delivery_qty  else 0 END) AS Poly ,
		sum(CASE WHEN b.process_id='13' THEN  b.reject_qty else 0  END) AS Poly_rej 

		from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond $del_date_cond group by b.color_id,b.order_id";
		$delivery_sql_result=sql_select($delivery_sql);
		foreach ($delivery_sql_result as $row)
		{
			$color_id=$row[csf('color_id')];
			$delivery_array[$row[csf('order_id')]][$color_id]['item_id']=$row[csf('item_id')];
			$delivery_array[$row[csf('order_id')]][$color_id]['cutting']=$row[csf('cutting')];
			$delivery_array[$row[csf('order_id')]][$color_id]['cutting_rej']=$row[csf('cutting_rej')];
			$delivery_array[$row[csf('order_id')]][$color_id]['kniting']=$row[csf('kniting')];
			$delivery_array[$row[csf('order_id')]][$color_id]['kniting_rej']=$row[csf('kniting_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['dyeing']=$row[csf('dyeing')];
			$delivery_array[$row[csf('order_id')]][$color_id]['dyeing_rej']=$row[csf('dyeing_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['finishing']=$row[csf('finishing')];
			$delivery_array[$row[csf('order_id')]][$color_id]['gray_qty']=$row[csf('gray_qty')];
			$delivery_array[$row[csf('order_id')]][$color_id]['finishing_rej']=$row[csf('finishing_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['sewing']=$row[csf('sewing')];
			$delivery_array[$row[csf('order_id')]][$color_id]['sewing_rej']=$row[csf('sewing_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['fab_print']=$row[csf('fab_print')];
			$delivery_array[$row[csf('order_id')]][$color_id]['fab_print_rej']=$row[csf('fab_print_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['washing']=$row[csf('washing')];
			$delivery_array[$row[csf('order_id')]][$color_id]['washing_rej']=$row[csf('washing_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['printing']=$row[csf('printing')];
			$delivery_array[$row[csf('order_id')]][$color_id]['printing_rej']=$row[csf('printing_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['Embroidery']=$row[csf('Embroidery')];
			$delivery_array[$row[csf('order_id')]][$color_id]['Embroidery_rej']=$row[csf('Embroidery_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['Iron']=$row[csf('Iron')];
			$delivery_array[$row[csf('order_id')]][$color_id]['Iron_rej']=$row[csf('Iron_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
			$delivery_array[$row[csf('order_id')]][$color_id]['Gmts_Finishing_rej']=$row[csf('Gmts_Finishing_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['Gmts_Dyeing']=$row[csf('Gmts_Dyeing')];
			$delivery_array[$row[csf('order_id')]][$color_id]['Gmts_Dyeing_rej']=$row[csf('Gmts_Dyeing_rej')];

			$delivery_array[$row[csf('order_id')]][$color_id]['Poly']=$row[csf('Poly')];
			$delivery_array[$row[csf('order_id')]][$color_id]['Poly_rej']=$row[csf('Poly_rej')];
			
		}
		// ======================================== FAB. PRODUCTION ===============================
		$order_cond=str_replace("b.order_id", "c.order_id", $order_cond);
		$fab_production_array=array();
		$fab_production_sql="SELECT c.order_id,b.color_id,
		sum(CASE WHEN c.product_type='4' THEN  c.quantity END) AS finishing,
		sum(CASE WHEN c.product_type='8' THEN  c.quantity END) AS printing
		from  subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond group by b.color_id,c.order_id";
		$fab_production_sql_result=sql_select($fab_production_sql);
		foreach ($fab_production_sql_result as $row)
		{
			$order_id=explode(',',$row[csf('order_id')]);
			foreach ($order_id as $val)
			{
				$color_id=$row[csf('color_id')];
				
				$fab_production_array[$val][$color_id]['fabric_description']=$row[csf('fabric_description')];
				$fab_production_array[$val][$color_id]['finishing']=$row[csf('finishing')];
				$fab_production_array[$val][$color_id]['printing']=$row[csf('printing')];
			}
		}
		
		// ================================== FOR KNITING PRODUCTION ==================================
		$ordeIDsKnit = "'".implode("','", $order_id_array)."'";
		$knit_production_array=array();
		$order_cond=str_replace("c.order_id", "b.order_id", $order_cond);
		$knit_production_sql="SELECT b.order_id,b.color_id, sum(b.product_qnty) AS kniting
		from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond group by b.color_id,b.order_id";
		$knit_production_sql_result=sql_select($knit_production_sql);
		foreach ($knit_production_sql_result as $row)
		{
			$knit_production_array[$row[csf('order_id')]][$row[csf('color_id')]]['kniting']=$row[csf('kniting')];
		}	
		//var_dump ($fab_production_array);

		//==================================== FOR DYEING PRODUCTION =====================================
		$order_cond=str_replace("b.order_id", "c.po_id", $order_cond);
		$dying_data_array=array();
		if ($db_type==0)
		{
			$dying_sql="SELECT c.po_id, b.color_id,(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_cond  ";
		}
		elseif($db_type==2)
		{
			 $dying_sql="SELECT a.result,a.load_unload_id,c.po_id,b.color_id, (c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38  and a.load_unload_id in(1,2) and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_cond ";
		}
		$dying_sql_result=sql_select($dying_sql);
		foreach ($dying_sql_result as $row)
		{
			$load_unload_id=$row[csf('load_unload_id')];
			$result=$row[csf('result')];
			 if($result==1 && $load_unload_id==2)
			 {
				$dying_data_array[$row[csf('po_id')]][$row[csf('color_id')]][$load_unload_id]+=$row[csf('production_qnty')];
			 }
			 else
			 {
				 $dying_data_array[$row[csf('po_id')]][$row[csf('color_id')]][$load_unload_id]+=$row[csf('production_qnty')];
			 }
		}
		//var_dump($dying_data_array);
		
		// ======================================= FOR GMTS PRODUCTION ===============================
		$order_cond=str_replace("c.po_id", "order_id", $order_cond);
		$gmt_production_array=array();
		 $gmt_production_sql="SELECT order_id,
		sum(CASE WHEN production_type='1' THEN  production_qnty END) AS cutting,
		sum(CASE WHEN production_type='2' THEN  production_qnty END) AS sewing,
		sum(CASE WHEN production_type='4' THEN  production_qnty END) AS Gmts_Finishing
		from subcon_gmts_prod_dtls where status_active=1 and is_deleted=0 $order_cond group by order_id";
		$gmt_production_sql_result=sql_select($gmt_production_sql);
		foreach ($gmt_production_sql_result as $row)
		{
			$gmt_production_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
			$gmt_production_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
			$gmt_production_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
		}
		//var_dump($gmt_production_array);

		//========================================== FOR BILL =====================================
		$order_cond=str_replace("order_id", "b.order_id", $order_cond);
		$in_bill_qty_array=array();
		$in_bill_amnt_array=array(); $bill_order_arr=array(); $bill_amt_arr=array();$bill_number_arr=array();$mst_arr=array();$bill_arr=array();
	    $in_bill_sql="SELECT a.id,a.prefix_no_num,a.bill_no,a.party_source,b.order_id,b.color_id, b.mst_id, sum(b.amount) as amount,
		sum(CASE WHEN b.process_id='1' THEN  b.delivery_qty END) AS cutting,
		sum(CASE WHEN b.process_id='2' THEN  b.delivery_qty END) AS kniting,
		sum(CASE WHEN b.process_id='3' THEN  b.delivery_qty END) AS dyeing,
		sum(CASE WHEN a.process_id='4' THEN  b.delivery_qty END) AS finishing,
		sum(CASE WHEN b.process_id='5' THEN  b.delivery_qty END) AS sewing,
		sum(CASE WHEN b.process_id='6' THEN  b.delivery_qty END) AS fab_print,
		sum(CASE WHEN b.process_id='7' THEN  b.delivery_qty END) AS washing,
		sum(CASE WHEN b.process_id='8' THEN  b.delivery_qty END) AS printing,
		sum(CASE WHEN b.process_id='9' THEN  b.delivery_qty END) AS Embroidery,
		sum(CASE WHEN b.process_id='10' THEN  b.delivery_qty END) AS Iron,
		sum(CASE WHEN b.process_id='11' THEN  b.delivery_qty END) AS Gmts_Finishing,
		sum(CASE WHEN b.process_id='12' THEN  b.delivery_qty END) AS Gmts_Dyeing,
		sum(CASE WHEN b.process_id='13' THEN  b.delivery_qty END) AS Poly,
		
		sum(CASE WHEN b.process_id='1' THEN  b.amount END) AS am_cutting,
		sum(CASE WHEN b.process_id='2' THEN  b.amount END) AS am_kniting,
		sum(CASE WHEN b.process_id='3' THEN  b.amount END) AS am_dyeing,
		sum(CASE WHEN a.process_id='4' THEN  b.amount END) AS am_finishing,
		sum(CASE WHEN b.process_id='5' THEN  b.amount END) AS am_sewing,
		sum(CASE WHEN b.process_id='6' THEN  b.amount END) AS am_fab_print,
		sum(CASE WHEN b.process_id='7' THEN  b.amount END) AS am_washing,
		sum(CASE WHEN b.process_id='8' THEN  b.amount END) AS am_printing,
		sum(CASE WHEN b.process_id='9' THEN  b.amount END) AS am_Embroidery,
		sum(CASE WHEN b.process_id='10' THEN  b.amount END) AS am_Iron,
		sum(CASE WHEN b.process_id='11' THEN  b.amount END) AS am_Gmts_Finishing,
		sum(CASE WHEN b.process_id='12' THEN  b.amount END) AS am_Gmts_Dyeing,
		sum(CASE WHEN b.process_id='13' THEN  b.amount END) AS am_Poly		
		from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 $order_cond group by a.id,a.prefix_no_num,a.bill_no,a.party_source,b.order_id,b.color_id, b.mst_id order by a.prefix_no_num ";
		$in_bill_sql_result=sql_select($in_bill_sql);
		foreach ($in_bill_sql_result as $row)
		{
			$color_id=$row[csf('color_id')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['cutting']+=$row[csf('cutting')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['kniting']+=$row[csf('kniting')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['dyeing']+=$row[csf('dyeing')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['finishing']+=$row[csf('finishing')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['sewing']+=$row[csf('sewing')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['fab_print']+=$row[csf('fab_print')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['washing']+=$row[csf('washing')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['printing']+=$row[csf('printing')];

			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['Embroidery']+=$row[csf('Embroidery')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['Iron']+=$row[csf('Iron')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['Gmts_Finishing']+=$row[csf('Gmts_Finishing')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['Gmts_Dyeing']+=$row[csf('Gmts_Dyeing')];
			$in_bill_qty_array[$row[csf('order_id')]][$color_id]['Poly']+=$row[csf('Poly')];
			
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_cutting']+=$row[csf('am_cutting')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_kniting']+=$row[csf('am_kniting')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_dyeing']+=$row[csf('am_dyeing')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_finishing']+=$row[csf('am_finishing')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_sewing']+=$row[csf('am_sewing')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_fab_print']+=$row[csf('am_fab_print')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_washing']+=$row[csf('am_washing')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_printing']+=$row[csf('am_printing')];

			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_Embroidery']+=$row[csf('am_Embroidery')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_Iron']+=$row[csf('am_Iron')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_Gmts_Finishing']+=$row[csf('am_Gmts_Finishing')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_Gmts_Dyeing']+=$row[csf('am_Gmts_Dyeing')];
			$in_bill_amnt_array[$row[csf('order_id')]][$color_id]['am_Poly']+=$row[csf('am_Poly')];

			$bill_order_arr[$row[csf('mst_id')]].=$row[csf('order_id')].',';
			$bill_amt_arr[$row[csf('order_id')]]+=$row[csf('amount')];
			$bill_number_arr[$row[csf('order_id')]] .=$row[csf('prefix_no_num')].",";
			
			$mst_arr[$row[csf('order_id')]].=$row[csf('id')].",";
			$bill_arr[$row[csf('order_id')]]=$row[csf('bill_no')];
			
			
			$mst_bill_id_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
			$mst_bill_id_arr[$row[csf('id')]]['prfix_bill_no']=$row[csf('prefix_no_num')];
			$mst_order_no_arr[$row[csf('order_id')]] .=$row[csf('bill_no')].",";
		}
			

		$order_wise_tot_paid_arr=array();
		$order_wise_tot_bill_arr2=array();
		$order_wise_tot_bill_arr=array();
		$order_cond=str_replace("b.order_id", "d.order_id", $order_cond);
		$order_wise_tot_paid="SELECT d.order_id, b.total_adjusted as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond group by d.order_id,b.total_adjusted";

		$order_wise_tot_paid_result=sql_select($order_wise_tot_paid);
		foreach ($order_wise_tot_paid_result as $row)
		{
			$order_wise_tot_paid_arr[$row[csf('order_id')]]+=$row[csf('rec_amount')];
		}

		$order_wise_tot_bill="SELECT a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond order by a.id";
		$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
		foreach ($order_wise_tot_bill_result as $row)
		{
			$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
		}

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

		//var_dump($po_wise_payRec_arr);
		$order_cond=str_replace("d.order_id", "b.po_id", $order_cond);
		$batch_qty_array=array();
		$sql_batch="SELECT b.po_id, a.color_id,(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond ";
		$sql_batch_result=sql_select($sql_batch);
		foreach ($sql_batch_result as $row)
		{
			$batch_qty_array[$row[csf('po_id')]][$row[csf('color_id')]]+=$row[csf('batch_qnty')];
		}
		
		
		ob_start();
		if ($cbo_process==4)
		{
			$tbl_width=2610;
			$col_span=30;
		}
		else
		{
			$tbl_width=2210;
			$col_span=25;
		}
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		?>
	    <div>
	        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="70">Job No</th>
	                <th width="100">Party</th>
	                <th width="80">Order no</th>
	                <th width="50">Image</th>                            
	                <th width="100">Style Name</th>
                    <th width="100">Color</th>
	                <th width="90">Order Quantity</th>
	                <th width="100">Order Value</th>
	                <th width="60">UOM</th>
	                <th width="90">Delivery Date</th>
	                <th width="60">Days in Hand</th>
	                <th width="120">Material Receive</th>
	                <th width="120">Material Issue</th>
	                <th width="120">Material Return</th>
	                <th width="120">Material Balance</th>
	                <?
						if ($cbo_process==4)
						{
							?>
	                        <th width="80">Batch Qty</th>
	                        <th width="80">Batch Bal.</th>
	                        <th width="80"> Dyeing Loading</th>
                            <th width="80">Dyeing Unloading</th>
	                        <?
						}
					?>
	                <th width="80">Prod. Finish Qty</th>
	                <th width="80">Delivery Qty</th>
                     <?
						if ($cbo_process==4)
						{
							?>
                             <th width="80">Delivery Grey Qty</th>
                            <?
						}?>
	                <th width="80">Reject Qty</th>

	                <th width="80">Yet To Delv.</th>
					<th width="120">Bill No</th>
	                <th width="80">Bill Qty</th>
	                <th width="80">Bill Amount</th>

	                <th width="100">Payment Rec.</th>
	                <th>Rec. Balance</th>
	            </thead>
	        </table>
		    <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
		        <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" id="">
			    <?
				$process_array=array();
				$i=1; $k=1;
				foreach ($job_sql_result as $row)
				{
					if (!in_array($row[csf("main_process_id")],$process_array) )
					{
						if($k!=1)
						{
						?>
							<tr class="tbl_bottom">
								<td colspan="7" align="right"><b>Process Total:</b></td>
								<td align="right"><? echo number_format($tot_order_qty); ?></td>
								<td align="right"><? echo number_format($tot_order_val); ?></td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right"><? echo number_format($tot_rec_qty); ?></td>
			                    <td align="right"><? echo number_format($tot_issue_qty); ?></td>
			                    <td align="right"><? echo number_format($tot_return_qty); ?></td>
			                    <td align="right">
			                    	<? 
			                    		$tot_material_blce=$tot_rec_qty-$tot_issue_qty;
			                    		echo $tot_material_blce;
			                     	?>
			                     </td>
			                    <?
									if ($cbo_process==4)
									{
										?>
			                                <td><? echo number_format($tot_batch_qty); ?></td>
			                                <td><? echo number_format($tot_batch_bal_qty); ?></td>
			                                <td><? echo number_format($tot_dyeing_qty); ?></td>
                                            <td><? echo number_format($tot_unload_dyeing_qty); ?></td>
										<?
									}
								?>
								<td align="right"><? echo number_format($tot_prod_qty); ?></td>
								<td align="right"><? echo number_format($tot_del_qty); ?></td>
                                  <?
									if ($cbo_process==4)
									{
										?>
                                       <td align="right"><? echo number_format($tot_del_grey_qty); ?></td>
                                      <?
									}?>
									   
								<td align="right"><? echo number_format($tot_del_rej); ?></td>
			                    <td align="right"><? echo number_format($tot_yet_to_delv); ?></td>
								<td align="right"><? //echo number_format($tot_yet_to_delv); ?></td>
								<td align="right"><? echo number_format($tot_bill_qty); ?></td>
								<td align="right"><? echo number_format($tot_bill_amnt); ?></td>
			                
								<td align="right"><? echo number_format($tot_payment_amnt); ?></td>
			                    <td align="right"><? echo number_format($tot_balance); ?></td>
							</tr>
							<tr bgcolor="#dddddd">
								<td colspan="<? echo $col_span; ?>" align="left" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
							</tr>
						<?
							unset($tot_order_qty);
							unset($tot_order_val);
							unset($tot_rec_qty);
							unset($tot_issue_qty);
							unset($tot_material_blce);
							if ($cbo_process==4)
							{
								unset($tot_batch_qty);
								unset($tot_dyeing_qty);
								unset($tot_unload_dyeing_qty);
								unset($tot_del_grey_qty);
							}
							unset($tot_prod_qty);
							unset($tot_del_qty);
							unset($tot_del_rej);
							unset($tot_bill_qty);
							unset($tot_bill_amnt);
							unset($tot_payment_amnt);

								unset($tot_yet_to_delv);


							unset($tot_balance);
						}
						else
						{
							?>
							<tr bgcolor="#dddddd">
								<td colspan="<? echo $col_span; ?>" align="left" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
							</tr>
							<?
						}					
						$process_array[]=$row[csf('main_process_id')];            
						$k++;
					}
			        if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
					$prod_qty=0; $del_qty=0; $bill_qty=0; $bill_amnt=0; $pay_rec=0;
					$del_rej=0;
					$color_id=$row[csf('color_id')];
					if ($row[csf('main_process_id')]==1)
					{
						
						$prod_qty=$gmt_production_array[$row[csf('id')]]['cutting'];
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['cutting'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['cutting_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['cutting'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_cutting'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==2)
					{
						$prod_qty=$knit_production_array[$row[csf('id')]][$color_id]['kniting'];
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['kniting'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['kniting_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['kniting'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_kniting'];
						$batch_qty=""; $dyeing_qty="";
						//echo $row[csf('main_process_id')].'K';
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==3)
					{
						$prod_qty=$dying_data_array[$row[csf('id')]][$color_id][2];
						//$unload_prod_qty=$dying_data_array[$row[csf('id')]][$color_id][2];
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['dyeing'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['dyeing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['dyeing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_dyeing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==4)//
					{
						$prod_qty=$fab_production_array[$row[csf('id')]][$color_id]['finishing'];
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['finishing'];
						$del_grey_qty=$delivery_array[$row[csf('id')]][$color_id]['gray_qty'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['finishing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['finishing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_finishing'];
						$batch_qty=$batch_qty_array[$row[csf('id')]][$color_id];
						$dyeing_qty=$dying_data_array[$row[csf('id')]][$color_id][1];
						$unload_prod_qty=$dying_data_array[$row[csf('id')]][$color_id][2];
						$grey_prod = $inventory_grey_prod_array[$row[csf('id')]][$color_id];
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==5)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]][$color_id]['sewing'];
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['sewing'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['sewing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['sewing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_sewing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==6)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]][$color_id]['fab_print'];
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['fab_print'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['fab_print_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['fab_print'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_fab_print'];

						$batch_qty=""; $dyeing_qty="";$unload_prod_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==7)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]][$color_id]['washing'];
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['washing'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['washing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['washing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_washing'];
						$batch_qty=""; $dyeing_qty="";$unload_prod_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==8)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]][$color_id]['printing'];
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['printing'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['printing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['printing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_printing'];
						$batch_qty=""; $dyeing_qty="";$unload_prod_qty="";
						//$pay_rec=
					}

					else if ($row[csf('main_process_id')]==9)
					{
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['Embroidery'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['Embroidery_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['Embroidery'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_Embroidery'];
						$batch_qty=""; $dyeing_qty="";$unload_prod_qty="";
					}

					else if ($row[csf('main_process_id')]==10)
					{
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['Iron'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['Iron_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['Iron'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_Iron'];
						$batch_qty=""; $dyeing_qty="";$unload_prod_qty="";
					}

					else if ($row[csf('main_process_id')]==11)
					{	
						$prod_qty=$gmt_production_array[$row[csf('id')]][$color_id]['Gmts_Finishing'];
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['Gmts_Finishing'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['Gmts_Finishing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['Gmts_Finishing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_Gmts_Finishing'];
						$batch_qty=""; $dyeing_qty="";$unload_prod_qty="";
					}

					else if ($row[csf('main_process_id')]==12)
					{
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['Gmts_Dyeing'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['Gmts_Dyeing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['Gmts_Dyeing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_Gmts_Dyeing'];
						$batch_qty=""; $dyeing_qty=""; $unload_prod_qty="";
					}

					else if ($row[csf('main_process_id')]==13)
					{
						$del_qty=$delivery_array[$row[csf('id')]][$color_id]['Poly'];
						$del_rej=$delivery_array[$row[csf('id')]][$color_id]['Poly_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]][$color_id]['Poly'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]][$color_id]['am_Poly'];
						$batch_qty=""; $dyeing_qty="";$unload_prod_qty="";
					}
					
					
					$rec_qty 		= $inventory_array[$row[csf('id')]][$color_id]['quantity']-$inventory_ret_array[$row[csf('id')]][$color_id];
					$issue_qty 		= $inv_iss_array[$row[csf('id')]][$color_id]['quantity'];
					$return_qty 	= $inventory_ret_array[$row[csf('id')]][$color_id];
					//$bill_no_array  = array_unique(explode(',',$bill_number_arr[$row[csf('id')]]));					
					$mst_noData   	    = rtrim($mst_arr[$row[csf('id')]],',');
					$bill_no_array  = array_unique(explode(',',$mst_noData));		
						
					$bill_no   	    = $bill_arr[$row[csf('id')]];
					$party_source   = $party_source_arr[$row[csf('id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">	
			        	<td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
			            <td width="70" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></td>
			            <td width="100"><p><? echo $party_arr[$row[csf('party_id')]]; ?></p></td>
			            <td width="80"><p><? echo $row[csf('order_no')]; ?></p></td>
			            <td width="50"><img onclick="openImageWindow( <? echo $row[csf('pic_id')];//$row[csf('job_no_prefix_num')]; ?> )" src='../../<? echo $imge_arr[$row[csf('pic_id')]]; ?>' height='25' width='30' /></td>
			            <td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
                        <td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
			            
			            <td width="90" align="right"><p>
			            	<a href="##" onclick="show_progress_report_details('order_desc_popup','<? echo $row[csf("id")]; ?>','1150px')">
			            	<? echo number_format($row[csf('order_quantity')],2); ?>
			            	</a>
			            </p></td>

			            <td width="100" align="right"><p><? echo number_format($row[csf('amount')],2); ?></p></td>
			            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
			            <td width="90"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
			            <td width="60" align="center"><? $daysOnHand = datediff("d",date("Y-m-d"),$row[csf('delivery_date')]); echo $daysOnHand; ?> </td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_popup','<? echo $row[csf("id")]; ?>','1150px','<? echo $color_id; ?>')"><? echo number_format($rec_qty,2); ?></a></p></td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_iss_popup','<? echo $row[csf("id")]; ?>','1150px','<? echo $color_id; ?>')"><? echo number_format($issue_qty,2); ?></a></p></td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_rtn_popup','<? echo $row[csf("id")]; ?>','1300px','<? echo $color_id; ?>')"><? echo number_format($return_qty,2); ?></a></p></td>
			            <td width="120" align="right">
			            	<?
			            		$mat_blnce_qty=$rec_qty-$issue_qty;
			            		echo number_format($mat_blnce_qty,2);
			            	?>
			            </td>
						<?
			                if ($cbo_process==4)
			                {
			                	$batch_bal_qty = $issue_qty - $batch_qty;
			                    ?>
			                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('batch_qty_pop_up','<? echo $row[csf("id")]; ?>_','1150px','<? echo $color_id; ?>')"><? echo number_format($batch_qty,2); ?></a></p></td>
			                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('batch_qty_pop_up','<? echo $row[csf("id")]; ?>_,'1150px','<? echo $color_id; ?>')"><? echo number_format($batch_bal_qty,2); ?></a></p></td>
			                    <td width="80" align="right"><p><? echo number_format($dyeing_qty,2); ?></p></td>
                                 <td width="80" align="right"><p><? echo number_format($unload_prod_qty,2); ?></p></td>
			                    <?
			                }
			            ?>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','1150px','<? echo $color_id; ?>')"><? echo number_format($prod_qty,2); ?></a></p></td>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('delivery_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>_<?echo $date_from;?>_<?echo $date_to;?>','1150px','<? echo $color_id; ?>')"><? echo number_format($del_qty,2); ?></a></p></td>
                        <?
			                if ($cbo_process==4)
			                {
			                	 
			                    ?>
                         <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('delivery_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>_<?echo $date_from;?>_<?echo $date_to;?>','1150px','<? echo $color_id; ?>')"><? echo number_format($del_grey_qty,2); ?></a></p></td>
                         <?
							}
						 ?>
                         
			            <td width="80" align="right"><p><? echo number_format($del_rej,2); ?></p></td>
			            <td width="80" align="right"><? $yet_to_delv=$row[csf('order_quantity')]-$del_qty; echo  number_format($yet_to_delv,2); ?></td>
						<td width="120" align="center"></p>
						<?
						foreach($bill_no_array as $bill_mst_id)
						{
							if($bill_mst_id!="")
							{
								//echo $bill_mst_id.'D';
								$bill_no=$mst_bill_id_arr[$bill_mst_id]['bill_no'];
								$prfix_bill_no=$mst_bill_id_arr[$bill_mst_id]['prfix_bill_no'];
							?>
							<a href="#report_details" onclick="fabric_finishing('<? echo $row[csf('company_id')];?>','<? echo $bill_mst_id; ?>','<? echo $bill_no;?>','<? echo $row[csf('main_process_id')];?>')"><? echo $prfix_bill_no."  ,";?></a>
							<?
							}
							

						} ?>
					</td> 

			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('bill_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','1150px','<? echo $color_id; ?>')"><? echo number_format($bill_qty,2); ?></a></p></td>
			            <td width="80" align="right"><? echo  number_format($bill_amnt,2); ?></td>

			            <td width="100" align="right">
			            	<p><a href="##" onclick="show_progress_report_details('payment_rec_pop_up','<? echo $row[csf("id")].'_'.$bill_amnt; ?>_<? echo $row[csf('main_process_id')]; ?>','1150px')">
			            		<? 
			            		$order_wise_payment_received = ($order_wise_tot_bill_arr[$row[csf('id')]]) ? ($order_wise_tot_paid_arr[$row[csf('id')]]/$order_wise_tot_bill_arr[$row[csf('id')]])*$bill_amnt : 0;
			            		echo number_format($order_wise_payment_received,2);
			            		?>
			            	</a></p>
			            </td>
			            

			            <td width="80" align="right"><? $balance=$bill_amnt-$order_wise_payment_received; echo  number_format($balance,2); ?></td>
			        </tr>
			        <?
					$i++;
					$tot_order_qty+=$row[csf('order_quantity')];
					$tot_order_val+=$row[csf('amount')];
					$tot_rec_qty+=$rec_qty;
					$tot_issue_qty+=$issue_qty;
					$tot_return_qty+=$return_qty;
					$tot_material_blce+=$mat_blnce_qty;
					$tot_prod_qty+=$prod_qty;
					$tot_del_qty+=$del_qty;
					$tot_grey_prod+=$grey_prod;
					$tot_del_rej+=$del_rej;
					$tot_yet_to_delv+=$yet_to_delv;
					$tot_bill_qty+=$bill_qty;
					$tot_bill_amnt+=$bill_amnt;
					$tot_payment_amnt+=$order_wise_payment_received;
					$tot_balance+=$balance;
					
					if ($cbo_process==4)
					{
						$tot_batch_qty+=$batch_qty;
						$tot_batch_bal_qty+=$batch_bal_qty;
						$tot_dyeing_qty+=$dyeing_qty;
						$tot_unload_dyeing_qty+=$unload_prod_qty;
						$tot_del_grey_qty+=$del_grey_qty;
						
						$tot_tottal_batch_qty+=$batch_qty;
						$tot_tottal_batch_bal_qty+=$batch_bal_qty;
						$tot_total_dyeing_qty+=$dyeing_qty;
						$tot_total_unload_dyeing_qty+=$unload_prod_qty;
						$tot_total_del_grey_qty+=$del_grey_qty;
					}
					
					$tot_total_order_qty+=$row[csf('order_quantity')];
					$tot_total_order_val+=$row[csf('amount')];
					$tot_total_rec_qty+=$rec_qty;
					$tot_total_issue_qty+=$issue_qty;
					$tot_total_return_qty+=$return_qty;
					$tot_total_material_blce+=$mat_blnce_qty;
					$tot_total_prod_qty+=$prod_qty;
					$tot_total_del_qty+=$del_qty;
					$tot_total_grey_prod+=$grey_prod;
					$tot_total_del_rej+=$del_rej;
					$tot_total_yet_to_delv+=$yet_to_delv;
					$tot_total_bill_qty+=$bill_qty;
					$tot_total_bill_amnt+=$bill_amnt;
					$tot_total_payment_amnt+=$order_wise_payment_received;
					$tot_total_balance+=$balance;
				}
				?>
			    	<tr class="tbl_bottom">
				        <td colspan="7" align="right"><b>Process Total:</b></td>
				        <td align="right"><? echo number_format($tot_order_qty); ?></td>
				        <td align="right"><? echo number_format($tot_order_val); ?></td>
				        <td align="right">&nbsp;</td>
				        <td align="right">&nbsp;</td>
				        <td align="right">&nbsp;</td>
				        <td align="right"><? echo number_format($tot_rec_qty); ?></td>
				        <td align="right"><? echo number_format($tot_issue_qty); ?></td>
				        <td align="right"><? echo number_format($tot_return_qty); ?></td>
				        <td align="right"><? echo number_format($tot_material_blce) ?></td>
				        <?
							if ($cbo_process==4)
							{
								?>
								<td><? echo number_format($tot_batch_qty); ?></td>
								<td><? echo number_format($tot_batch_bal_qty); ?></td>
								<td><? echo number_format($tot_dyeing_qty); ?></td>
                                <td><? echo number_format($tot_unload_dyeing_qty); ?></td>
								<?
							}
				        ?>
				        <td align="right"><? echo number_format($tot_prod_qty); ?></td>
				        <td align="right"><? echo number_format($tot_del_qty); ?></td>
                          <?
							if ($cbo_process==4)
							{
								?>
                        <td align="right"><? echo number_format($tot_del_grey_qty); ?></td>
                        	<?
							}
				        ?>
                        
				        <td align="right"><? echo number_format($tot_del_rej); ?></td>
				        <td align="right"><? echo number_format($tot_yet_to_delv); ?></td>
						<td align="right"><? //echo number_format($tot_yet_to_delv); ?></td>
				        <td align="right"><? echo number_format($tot_bill_qty); ?></td>
				        <td align="right"><? echo number_format($tot_bill_amnt); ?></td>
				        <td align="right"><? echo number_format($tot_payment_amnt); ?></td>
				        <td align="right"><? echo number_format($tot_balance); ?></td>
			    	</tr>
			    	<!-- ====================================== bottom part ============================ -->
			        <tr class="tbl_bottom">
			            <td colspan="7" align="right">Grand Total:</td>
			            <td align="right"><? echo number_format($tot_total_order_qty); ?></td>                            
			            <td align="right"><? echo number_format($tot_total_order_val); ?></td>
			            <td>&nbsp;</td>
			            <td>&nbsp;</td>
			            <td>&nbsp;</td>
			            <td><? echo number_format($tot_total_rec_qty); ?></td>
			            <td><? echo number_format($tot_total_issue_qty); ?></td>
			            <td><? echo number_format($tot_total_return_qty); ?></td>
			            <td><? echo number_format($tot_total_material_blce) ?></td>
			            <?
			                if ($cbo_process==4)
			                {
			                    ?>
			                    <td><? echo number_format($tot_tottal_batch_qty); ?></td>
			                    <td><? echo number_format($tot_tottal_batch_bal_qty); ?></td>
			                    <td><? echo number_format($tot_total_dyeing_qty); ?></td>
                                 <td><? echo number_format($tot_total_unload_dyeing_qty); ?></td>
			                    <? 
			                }
			            ?>
			            <td><? echo number_format($tot_total_prod_qty); ?></td>
			            <td><? echo number_format($tot_total_del_qty); ?></td>
                         <?
							if ($cbo_process==4)
							{
								?>
                        <td align="right"><? echo number_format($tot_total_del_grey_qty); ?></td>
                        	<?
							}
				        ?>
			            <td><? echo number_format($tot_total_del_rej); ?></td>
			            <td><? echo number_format($tot_total_yet_to_delv); ?></td>
						<td><? //echo number_format($tot_yet_to_delv); ?></td>
			            <td><? echo number_format($tot_total_bill_qty); ?></td>
			            <td><? echo number_format($tot_total_bill_amnt); ?></td>
			            <td><? echo number_format($tot_total_payment_amnt); ?></td>
			            <td><? echo number_format($tot_total_balance); ?></td>
			        </tr>
			    </table>        
		    </div>
	    </div>
	    <?
	}
	else if($type == 33)// backup 
	{
		$job_no=str_replace("'","",$txt_job_no);
		$txt_style_ref=str_replace("'","",$txt_style_ref);
		$txt_order_no=str_replace("'","",$txt_order_no);
		//$year_id=str_replace("'","",$cbo_year);
		$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
		$cbo_process=str_replace("'","",$cbo_process_id);
		//if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
		if($cbo_buyer_id!=0) $buyer_id_cond=" and a.party_id='$cbo_buyer_id'"; else $buyer_id_cond="";
		
		if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
		if($db_type==0)
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
		//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
		if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
		//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
		if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
		if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
		
		if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and b.delivery_date between $txt_date_from and $txt_date_to";
		
		$inventory_array=array();
		$inventory_sql="select b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=2 and b.is_deleted=0 group by b.order_id"; // trans_type = 1 receive, 2 issue, 3 return
		// echo $inventory_sql;
		$inventory_sql_result=sql_select($inventory_sql);
		foreach ($inventory_sql_result as $row)
		{
			$inventory_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
		}
		// print_r($inventory_array);
		$inv_iss_array=array();
		$inv_iss_sql="select b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
		$inv_iss_sql_result=sql_select($inv_iss_sql);
		foreach ($inv_iss_sql_result as $row)
		{
			$inv_iss_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
		}	
		$inventory_ret_array=array();
		$inv_ret_sql="select b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
		
		$inv_ret_sql_result=sql_select($inv_ret_sql);
		foreach ($inv_ret_sql_result as $row)
		{
			$inventory_ret_array[$row[csf('order_id')]]=$row[csf('quantity')];
		}	//var_dump($inventory_array);
		$delivery_array=array();
		$delivery_sql="SELECT b.order_id,
		sum(CASE WHEN b.process_id='1' THEN  b.delivery_qty else 0 END) AS cutting,
		sum(CASE WHEN b.process_id='1' THEN  b.reject_qty  else 0 END) AS cutting_rej,

		sum(CASE WHEN b.process_id='2' THEN  b.delivery_qty  else 0 END) AS kniting,
		sum(CASE WHEN b.process_id='2' THEN  b.reject_qty  else 0 END) AS kniting_rej,

		sum(CASE WHEN b.process_id='3' THEN  b.delivery_qty  else 0 END) AS dyeing,
		sum(CASE WHEN b.process_id='3' THEN  b.reject_qty  else 0 END) AS dyeing_rej,

		sum(CASE WHEN b.process_id='4' THEN  b.delivery_qty  else 0 END) AS finishing,
		sum(CASE WHEN b.process_id='4' THEN  b.reject_qty else 0  END) AS finishing_rej,

		sum(CASE WHEN b.process_id='5' THEN  b.delivery_qty  else 0 END) AS sewing,
		sum(CASE WHEN b.process_id='5' THEN  b.reject_qty  else 0 END) AS sewing_rej,

		sum(CASE WHEN b.process_id='6' THEN  b.delivery_qty else 0  END) AS fab_print,
		sum(CASE WHEN b.process_id='6' THEN  b.reject_qty else 0  END) AS fab_print_rej,

		sum(CASE WHEN b.process_id='7' THEN  b.delivery_qty  else 0 END) AS washing,
		sum(CASE WHEN b.process_id='7' THEN  b.reject_qty else 0  END) AS washing_rej,

		sum(CASE WHEN b.process_id='8' THEN  b.delivery_qty  else 0 END) AS printing,
		sum(CASE WHEN b.process_id='8' THEN  b.reject_qty else 0  END) AS printing_rej,

		sum(CASE WHEN b.process_id='9' THEN  b.delivery_qty else 0  END) AS Embroidery,
		sum(CASE WHEN b.process_id='9' THEN  b.reject_qty  else 0 END) AS Embroidery_rej,

		sum(CASE WHEN b.process_id='10' THEN  b.delivery_qty  else 0 END) AS Iron,
		sum(CASE WHEN b.process_id='10' THEN  b.reject_qty else 0  END) AS Iron_rej,

		sum(CASE WHEN b.process_id='11' THEN  b.delivery_qty else 0  END) AS Gmts_Finishing,
		sum(CASE WHEN b.process_id='11' THEN  b.reject_qty  else 0 END) AS Gmts_Finishing_rej,

		sum(CASE WHEN b.process_id='12' THEN  b.delivery_qty else 0  END) AS Gmts_Dyeing,
		sum(CASE WHEN b.process_id='12' THEN  b.reject_qty else 0  END) AS Gmts_Dyeing_rej,

		sum(CASE WHEN b.process_id='13' THEN  b.delivery_qty  else 0 END) AS Poly ,
		sum(CASE WHEN b.process_id='13' THEN  b.reject_qty else 0  END) AS Poly_rej 

		from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 group by b.order_id";
		// echo $delivery_sql;
		$delivery_sql_result=sql_select($delivery_sql);
		foreach ($delivery_sql_result as $row)
		{
			$delivery_array[$row[csf('order_id')]]['item_id']=$row[csf('item_id')];
			$delivery_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
			$delivery_array[$row[csf('order_id')]]['cutting_rej']=$row[csf('cutting_rej')];
			$delivery_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
			$delivery_array[$row[csf('order_id')]]['kniting_rej']=$row[csf('kniting_rej')];

			$delivery_array[$row[csf('order_id')]]['dyeing']=$row[csf('dyeing')];
			$delivery_array[$row[csf('order_id')]]['dyeing_rej']=$row[csf('dyeing_rej')];

			$delivery_array[$row[csf('order_id')]]['finishing']=$row[csf('finishing')];
			$delivery_array[$row[csf('order_id')]]['finishing_rej']=$row[csf('finishing_rej')];

			$delivery_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
			$delivery_array[$row[csf('order_id')]]['sewing_rej']=$row[csf('sewing_rej')];

			$delivery_array[$row[csf('order_id')]]['fab_print']=$row[csf('fab_print')];
			$delivery_array[$row[csf('order_id')]]['fab_print_rej']=$row[csf('fab_print_rej')];

			$delivery_array[$row[csf('order_id')]]['washing']=$row[csf('washing')];
			$delivery_array[$row[csf('order_id')]]['washing_rej']=$row[csf('washing_rej')];

			$delivery_array[$row[csf('order_id')]]['printing']=$row[csf('printing')];
			$delivery_array[$row[csf('order_id')]]['printing_rej']=$row[csf('printing_rej')];

			$delivery_array[$row[csf('order_id')]]['Embroidery']=$row[csf('Embroidery')];
			$delivery_array[$row[csf('order_id')]]['Embroidery_rej']=$row[csf('Embroidery_rej')];

			$delivery_array[$row[csf('order_id')]]['Iron']=$row[csf('Iron')];
			$delivery_array[$row[csf('order_id')]]['Iron_rej']=$row[csf('Iron_rej')];

			$delivery_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
			$delivery_array[$row[csf('order_id')]]['Gmts_Finishing_rej']=$row[csf('Gmts_Finishing_rej')];

			$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing']=$row[csf('Gmts_Dyeing')];
			$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing_rej']=$row[csf('Gmts_Dyeing_rej')];

			$delivery_array[$row[csf('order_id')]]['Poly']=$row[csf('Poly')];
			$delivery_array[$row[csf('order_id')]]['Poly_rej']=$row[csf('Poly_rej')];
			
		}
		// var_dump($delivery_array);
		$fab_production_array=array();
		$fab_production_sql="SELECT c.order_id, sum(CASE WHEN c.product_type='4' THEN  c.quantity END) AS finishing, sum(CASE WHEN c.product_type='8' THEN  c.quantity END) AS printing from  subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.order_id"; 
		// echo $fab_production_sql;
		$fab_production_sql_result=sql_select($fab_production_sql);
		foreach ($fab_production_sql_result as $row)
		{
			$order_id=explode(',',$row[csf('order_id')]);
			foreach ($order_id as $val)
			{
				$fab_production_array[$val]['fabric_description']=$row[csf('fabric_description')];
				$fab_production_array[$val]['finishing']=$row[csf('finishing')];
				$fab_production_array[$val]['printing']=$row[csf('printing')];
			}
		}
		
		$knit_production_array=array();
		$knit_production_sql="SELECT b.order_id, sum(b.product_qnty) AS kniting from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id"; 
		$knit_production_sql_result=sql_select($knit_production_sql);
		foreach ($knit_production_sql_result as $row)
		{
			$knit_production_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
		}	
		// var_dump ($knit_production_array);
		$dying_data_array=array();
		if ($db_type==0)
		{
			$dying_sql="SELECT c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_id ";
		}
		elseif($db_type==2)
		{
			$dying_sql="SELECT c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_id ";
		}
		$dying_sql_result=sql_select($dying_sql);
		foreach ($dying_sql_result as $row)
		{
			$dying_data_array[$row[csf('po_id')]]=$row[csf('production_qnty')];
		}
		//var_dump($dying_data_array);
		
		$gmt_production_array=array();
		$gmt_production_sql="SELECT order_id, sum(CASE WHEN production_type='1' THEN  production_qnty END) AS cutting, sum(CASE WHEN production_type='2' THEN  production_qnty END) AS sewing, sum(CASE WHEN production_type='4' THEN  production_qnty END) AS Gmts_Finishing from subcon_gmts_prod_dtls where status_active=1 and is_deleted=0 group by order_id"; 
		$gmt_production_sql_result=sql_select($gmt_production_sql);
		foreach ($gmt_production_sql_result as $row)
		{
			$gmt_production_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
			$gmt_production_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
			$gmt_production_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
		}
		//var_dump($gmt_production_array);
		$in_bill_qty_array=array();
		$in_bill_amnt_array=array(); $bill_order_arr=array(); $bill_amt_arr=array();
		$in_bill_sql="SELECT order_id, mst_id, sum(amount) as amount,
		sum(CASE WHEN process_id='1' THEN  delivery_qty END) AS cutting,
		sum(CASE WHEN process_id='2' THEN  delivery_qty END) AS kniting,
		sum(CASE WHEN process_id='3' THEN  delivery_qty END) AS dyeing,
		sum(CASE WHEN process_id='4' THEN  delivery_qty END) AS finishing,
		sum(CASE WHEN process_id='5' THEN  delivery_qty END) AS sewing,
		sum(CASE WHEN process_id='6' THEN  delivery_qty END) AS fab_print,
		sum(CASE WHEN process_id='7' THEN  delivery_qty END) AS washing,
		sum(CASE WHEN process_id='8' THEN  delivery_qty END) AS printing,
		sum(CASE WHEN process_id='9' THEN  delivery_qty END) AS Embroidery,
		sum(CASE WHEN process_id='10' THEN  delivery_qty END) AS Iron,
		sum(CASE WHEN process_id='11' THEN  delivery_qty END) AS Gmts_Finishing,
		sum(CASE WHEN process_id='12' THEN  delivery_qty END) AS Gmts_Dyeing,
		sum(CASE WHEN process_id='13' THEN  delivery_qty END) AS Poly,
		
		sum(CASE WHEN process_id='1' THEN  amount END) AS am_cutting,
		sum(CASE WHEN process_id='2' THEN  amount END) AS am_kniting,
		sum(CASE WHEN process_id='3' THEN  amount END) AS am_dyeing,
		sum(CASE WHEN process_id='4' THEN  amount END) AS am_finishing,
		sum(CASE WHEN process_id='5' THEN  amount END) AS am_sewing,
		sum(CASE WHEN process_id='6' THEN  amount END) AS am_fab_print,
		sum(CASE WHEN process_id='7' THEN  amount END) AS am_washing,
		sum(CASE WHEN process_id='8' THEN  amount END) AS am_printing,
		sum(CASE WHEN process_id='9' THEN  amount END) AS am_Embroidery,
		sum(CASE WHEN process_id='10' THEN  amount END) AS am_Iron,
		sum(CASE WHEN process_id='11' THEN  amount END) AS am_Gmts_Finishing,
		sum(CASE WHEN process_id='12' THEN  amount END) AS am_Gmts_Dyeing,
		sum(CASE WHEN process_id='13' THEN  amount END) AS am_Poly
		
		from subcon_inbound_bill_dtls where status_active=1 and is_deleted=0 group by order_id, mst_id";
		$in_bill_sql_result=sql_select($in_bill_sql);
		foreach ($in_bill_sql_result as $row)
		{
			$in_bill_qty_array[$row[csf('order_id')]]['cutting']+=$row[csf('cutting')];
			$in_bill_qty_array[$row[csf('order_id')]]['kniting']+=$row[csf('kniting')];
			$in_bill_qty_array[$row[csf('order_id')]]['dyeing']+=$row[csf('dyeing')];
			$in_bill_qty_array[$row[csf('order_id')]]['finishing']+=$row[csf('finishing')];
			$in_bill_qty_array[$row[csf('order_id')]]['sewing']+=$row[csf('sewing')];
			$in_bill_qty_array[$row[csf('order_id')]]['fab_print']+=$row[csf('fab_print')];
			$in_bill_qty_array[$row[csf('order_id')]]['washing']+=$row[csf('washing')];
			$in_bill_qty_array[$row[csf('order_id')]]['printing']+=$row[csf('printing')];

			$in_bill_qty_array[$row[csf('order_id')]]['Embroidery']+=$row[csf('Embroidery')];
			$in_bill_qty_array[$row[csf('order_id')]]['Iron']+=$row[csf('Iron')];
			$in_bill_qty_array[$row[csf('order_id')]]['Gmts_Finishing']+=$row[csf('Gmts_Finishing')];
			$in_bill_qty_array[$row[csf('order_id')]]['Gmts_Dyeing']+=$row[csf('Gmts_Dyeing')];
			$in_bill_qty_array[$row[csf('order_id')]]['Poly']+=$row[csf('Poly')];
			
			$in_bill_amnt_array[$row[csf('order_id')]]['am_cutting']+=$row[csf('am_cutting')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_kniting']+=$row[csf('am_kniting')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_dyeing']+=$row[csf('am_dyeing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_finishing']+=$row[csf('am_finishing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_sewing']+=$row[csf('am_sewing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_fab_print']+=$row[csf('am_fab_print')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_washing']+=$row[csf('am_washing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_printing']+=$row[csf('am_printing')];

			$in_bill_amnt_array[$row[csf('order_id')]]['am_Embroidery']+=$row[csf('am_Embroidery')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Iron']+=$row[csf('am_Iron')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Gmts_Finishing']+=$row[csf('am_Gmts_Finishing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Gmts_Dyeing']+=$row[csf('am_Gmts_Dyeing')];
			$in_bill_amnt_array[$row[csf('order_id')]]['am_Poly']+=$row[csf('am_Poly')];

			$bill_order_arr[$row[csf('mst_id')]].=$row[csf('order_id')].',';
			$bill_amt_arr[$row[csf('order_id')]]+=$row[csf('amount')];
		}

		$order_wise_tot_paid_arr=array();
		$order_wise_tot_bill_arr2=array();
		$order_wise_tot_bill_arr=array();
		//$order_wise_tot_paid="select d.order_id, sum(b.total_adjusted) as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by d.order_id";

		$order_wise_tot_paid="SELECT d.order_id, b.total_adjusted as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by d.order_id,b.total_adjusted";

		$order_wise_tot_paid_result=sql_select($order_wise_tot_paid);
		foreach ($order_wise_tot_paid_result as $row)
		{
			//$order_wise_tot_paid_arr[$row[csf('order_id')]]=$row[csf('rec_amount')];
			//$order_wise_tot_bill_arr[$row[csf('order_id')]]=$row[csf('bill_amount')];
			$order_wise_tot_paid_arr[$row[csf('order_id')]]+=$row[csf('rec_amount')];
		}

		$order_wise_tot_bill="SELECT a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id asc";
		$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
		foreach ($order_wise_tot_bill_result as $row)
		{
			$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
		}

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

		//var_dump($po_wise_payRec_arr);
		
		$batch_qty_array=array();
		$sql_batch="SELECT b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id";
		// echo $sql_batch;
		$sql_batch_result=sql_select($sql_batch);
		foreach ($sql_batch_result as $row)
		{
			$batch_qty_array[$row[csf('po_id')]]=$row[csf('batch_qnty')];
		}
		// print_r($batch_qty_array);
		
		$job_sql = "SELECT a.id as pic_id,a.job_no_prefix_num, a.subcon_job, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref 
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_no_cond $style_ref_cond $date_cond $job_no_cond $process_id_cond $buyer_id_cond
			group by a.job_no_prefix_num, a.subcon_job, a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref,a.id  order by b.main_process_id, a.job_no_prefix_num, b.order_no, b.delivery_date ";
			//echo $job_sql;
		$job_sql_result=sql_select($job_sql);
		ob_start();
		if ($cbo_process==4)
		{
			$tbl_width=1490;
			$col_span=16;
		}
		else
		{
			$tbl_width=1490;
			$col_span=16;
		}
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
		?>
	    <div>
	        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
	            <thead>
	            	<tr>
		                <th width="30">SL</th>
		                <th width="70">Job No</th>
		                <th width="150">Party</th>
		                <th width="150">Order no</th>                            
		                <th width="100">Style Name</th>
		                <th width="90">Order Quantity</th>
		                <th width="60">UOM</th>
		                <th width="120">Material Receive</th>
		                <th width="120">Material Issue</th>
		                <th width="120">Material Balance</th>	                
	                    <th width="80">Batch Qty</th>
	                    <th width="80">Batch Balance</th>	                        
		                <th width="80">Production [Grey]</th>
		                <th width="80">Production Balance</th>
		                <th width="80">Delivery Qty Grey</th>
		                <th width="80">Delivery Qty Finished</th>
		                <th width="80">Yet To Delv. In Grey.</th>
	            	</tr>
	            </thead>
	            
	        </table>
		    <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
		        <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" id="">
			    <?
				$process_array=array();
				$i=1; $k=1;
				foreach ($job_sql_result as $row)
				{
					if (!in_array($row[csf("main_process_id")],$process_array) )
					{
						if($k!=1)
						{
							?>
							<tr class="tbl_bottom">
								<td width="500" colspan="5" align="right"><b>Process Total:</b></td>
								<td width="90" align="right"><? echo number_format($tot_order_qty); ?></td>
								<td width="60" align="right">&nbsp;</td>
								<td width="120" align="right"><? echo number_format($tot_rec_qty); ?></td>
								<td width="120" align="right"><? echo number_format($tot_total_issue_qty); ?></td>
								<td width="120" align="right"><? echo number_format($tot_material_blce); ?></td>
								<td width="80" align="right"><? echo number_format($tot_batch_qty); ?></td>
			                    <td width="80" align="right"><? echo number_format($tot_batch_balance); ?></td>
			                    <td width="80" align="right"><? echo $tot_dyeing_qty;?> </td>
			                    <td width="80" align="right"><?php echo number_format($tot_order_qty-$tot_dyeing_qty); ?></td>
                                <td width="80"><? echo number_format($tot_del_qty_gray); ?></td>
                                <td width="80"><? echo number_format($tot_del_qty); ?></td>
								<td width="80" align="right"><? echo number_format($tot_yet_to_delv); ?></td>
							</tr>
							<tr bgcolor="#dddddd">
								<td colspan="<? echo $col_span; ?>" align="left" >
									<b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b>
								</td>
							</tr>
							<?
							unset($tot_order_qty);
							unset($tot_rec_qty);
							unset($tot_total_issue_qty);
							unset($tot_material_blce);
							unset($tot_batch_qty);
							unset($tot_batch_balance);
							unset($tot_dyeing_qty);
							unset($tot_del_qty_gray);
							unset($tot_del_qty);
							unset($tot_yet_to_delv);
						}
						else
						{
							?>
							<tr bgcolor="#dddddd">
								<td colspan="<? echo $col_span; ?>" align="left" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
							</tr>
							<?
						}					
						$process_array[]=$row[csf('main_process_id')];            
						$k++;
					}
			        if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
					$prod_qty=0; $del_qty=0; $bill_qty=0; $bill_amnt=0; $pay_rec=0;
					$del_rej=0;

					if ($row[csf('main_process_id')]==1)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['cutting'];
						$del_qty=$delivery_array[$row[csf('id')]]['cutting'];
						$del_rej=$delivery_array[$row[csf('id')]]['cutting_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['cutting'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_cutting'];
						// $batch_qty=""; 
						$batch_qty=$batch_qty_array[$row[csf('id')]];
						$dyeing_qty="";
						//$pay_rec=
						$del_qty_fin = "";
					}
					else if ($row[csf('main_process_id')]==2)
					{
						$prod_qty=$knit_production_array[$row[csf('id')]]['kniting'];
						$del_qty=$delivery_array[$row[csf('id')]]['kniting'];
						$del_rej=$delivery_array[$row[csf('id')]]['kniting_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['kniting'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_kniting'];
						$batch_qty=""; $dyeing_qty=""; $del_qty_fin = "";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==3)
					{
						$prod_qty=$dying_data_array[$row[csf('id')]];
						$del_qty_fin=$delivery_array[$row[csf('id')]]['dyeing'];
						$del_qty = "";
						// $del_qty=$delivery_array[$row[csf('id')]]['dyeing'];
						$del_rej=$delivery_array[$row[csf('id')]]['dyeing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['dyeing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_dyeing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
					}
					else if ($row[csf('main_process_id')]==4)
					{
						$prod_qty=$fab_production_array[$row[csf('id')]]['finishing'];
						$del_qty=$delivery_array[$row[csf('id')]]['finishing'];
						$del_rej=$delivery_array[$row[csf('id')]]['finishing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['finishing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_finishing'];
						$batch_qty=$batch_qty_array[$row[csf('id')]];
						$dyeing_qty=$dying_data_array[$row[csf('id')]];
						//$pay_rec=
						$del_qty_fin = "";
					}
					else if ($row[csf('main_process_id')]==5)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['sewing'];
						$del_qty=$delivery_array[$row[csf('id')]]['sewing'];
						$del_rej=$delivery_array[$row[csf('id')]]['sewing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['sewing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_sewing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
						$del_qty_fin = "";
					}
					else if ($row[csf('main_process_id')]==6)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['fab_print'];
						$del_qty=$delivery_array[$row[csf('id')]]['fab_print'];
						$del_rej=$delivery_array[$row[csf('id')]]['fab_print_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['fab_print'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_fab_print'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
						$del_qty_fin = "";
					}
					else if ($row[csf('main_process_id')]==7)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['washing'];
						$del_qty=$delivery_array[$row[csf('id')]]['washing'];
						$del_rej=$delivery_array[$row[csf('id')]]['washing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['washing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_washing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
						$del_qty_fin = "";
					}
					else if ($row[csf('main_process_id')]==8)
					{
						$prod_qty=$gmt_production_array[$row[csf('id')]]['printing'];
						$del_qty=$delivery_array[$row[csf('id')]]['printing'];
						$del_rej=$delivery_array[$row[csf('id')]]['printing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['printing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_printing'];
						$batch_qty=""; $dyeing_qty="";
						//$pay_rec=
						$del_qty_fin = "";
					}

					else if ($row[csf('main_process_id')]==9)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Embroidery'];
						$del_qty=$delivery_array[$row[csf('id')]]['Embroidery'];
						$del_rej=$delivery_array[$row[csf('id')]]['Embroidery_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Embroidery'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Embroidery'];
						$batch_qty=""; $dyeing_qty="";$del_qty_fin = "";
					}

					else if ($row[csf('main_process_id')]==10)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Iron'];
						$del_qty=$delivery_array[$row[csf('id')]]['Iron'];
						$del_rej=$delivery_array[$row[csf('id')]]['Iron_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Iron'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Iron'];
						$batch_qty=""; $dyeing_qty="";$del_qty_fin = "";
					}

					else if ($row[csf('main_process_id')]==11)
					{	
						$prod_qty=$gmt_production_array[$row[csf('id')]]['Gmts_Finishing'];
						$del_qty=$delivery_array[$row[csf('id')]]['Gmts_Finishing'];
						$del_rej=$delivery_array[$row[csf('id')]]['Gmts_Finishing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Gmts_Finishing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Finishing'];
						$batch_qty=""; $dyeing_qty="";$del_qty_fin = "";
					}

					else if ($row[csf('main_process_id')]==12)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Gmts_Dyeing'];
						$del_qty=$delivery_array[$row[csf('id')]]['Gmts_Dyeing'];
						$del_rej=$delivery_array[$row[csf('id')]]['Gmts_Dyeing_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Gmts_Dyeing'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Dyeing'];
						$batch_qty=""; $dyeing_qty="";$del_qty_fin = "";
					}

					else if ($row[csf('main_process_id')]==13)
					{
						//$prod_qty=$gmt_production_array[$row[csf('id')]]['Poly'];
						$del_qty=$delivery_array[$row[csf('id')]]['Poly'];
						$del_rej=$delivery_array[$row[csf('id')]]['Poly_rej'];
						$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Poly'];
						$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Poly'];
						$batch_qty=""; $dyeing_qty="";$del_qty_fin = "";
					}
					
					
					$rec_qty=$inventory_array[$row[csf('id')]]['quantity']-$inventory_ret_array[$row[csf('id')]];
					$issue_qty=$inv_iss_array[$row[csf('id')]]['quantity'];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">	
			        	<td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
			            <td width="70" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></td>
			            <td width="150"><p><? echo $party_arr[$row[csf('party_id')]]; ?></p></td>
			            <td width="150"><p><? echo $row[csf('order_no')]; ?></p></td>			            
			            <td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
			            
			            <td width="90" align="right"><p>
			            	<a href="##" onclick="show_progress_report_details('order_desc_popup','<? echo $row[csf("id")]; ?>','1150px')">
			            	<? echo number_format($row[csf('order_quantity')]); ?>
			            	</a>
			            </p></td>
			            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
			            
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_popup','<? echo $row[csf("id")]; ?>','1150px')"><? echo number_format($rec_qty); ?></a></p></td>
			            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_iss_popup','<? echo $row[csf("id")]; ?>','1150px')"><? echo number_format($issue_qty); ?></a></p></td>
			            <td width="120" align="right">
			            	<?
			            		$mat_blnce_qty=$rec_qty-$issue_qty;
			            		echo number_format($mat_blnce_qty);
			            	?>
			           </td>
	                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('batch_qty_pop_up','<? echo $row[csf("id")]; ?>_summary','1260px')">
	                    	<? echo number_format($batch_qty = $batch_qty_array[$row[csf('id')]]); //number_format($batch_qty,2); ?>
	                    		
	                    	</a></p></td>
	                    <td width="80" align="right"><p>
	                    	<? $batch_balance = $issue_qty - $batch_qty;echo number_format($batch_balance); ?>	                   		
	                    	</p></td>
			                    
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>_summary','1250px')"><? echo number_format($prod_qty); ?></a></p></td>
			            <td width="80" align="right"><p><?php echo number_format($row[csf('order_quantity')]-$prod_qty); ?></p></td>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('delivery_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>_<?echo $date_from;?>_<?echo $date_to;?>_summary','1150px')"><? echo number_format($del_qty); ?></a></p></td>
			            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('delivery_qty_fin_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','1150px')"><? echo number_format($del_qty_fin); ?></a></p></td>
			           
			            <td width="80" align="right">
			            	<? 
			            	$delivery_qnty = 0;
			            	if($del_qty==""){
			            		$delivery_qnty = $del_qty_fin;
			            	}else{
			            		$delivery_qnty = $del_qty;
			            	}
			            	$yet_to_delv=$rec_qty-$delivery_qnty; 

			            	echo  number_format($yet_to_delv); 
			            	?>
			            		
			            	</td>
			        </tr>
			        <?
					$i++;
					$tot_order_qty+=$row[csf('order_quantity')];
					$tot_order_val+=$row[csf('amount')];
					$tot_rec_qty+=$rec_qty;
					$tot_issue_qty+=$issue_qty;
					$tot_material_blce+=$mat_blnce_qty;
					$tot_prod_qty+=$prod_qty;
					$tot_del_qty+=$del_qty_fin;
					$tot_del_qty_gray+=$del_qty;
					$tot_yet_to_delv+=$yet_to_delv;
					$tot_yet_to_bill+=$yet_to_bill;
					$tot_bill_qty+=$bill_qty;
					$tot_bill_amnt+=$bill_amnt;
					$tot_payment_amnt+=$order_wise_payment_received;
					$tot_balance+=$balance;
					$tot_batch_qty+=$batch_qty;
					$tot_batch_balance += $batch_balance;
					$tot_dyeing_qty+=$prod_qty;
					
					$tot_tottal_batch_qty+=$batch_qty;
					$tot_total_dyeing_qty+=$prod_qty;					
					$tot_total_order_qty+=$row[csf('order_quantity')];
					$tot_total_order_val+=$row[csf('amount')];
					$tot_total_rec_qty+=$rec_qty;
					$tot_total_issue_qty+=$issue_qty;
					$tot_total_material_blce+=$mat_blnce_qty;
					$tot_total_prod_qty+=$prod_qty;
					$tot_total_del_qty+=$del_qty_fin;
					$tot_total_del_qty_gray+=$del_qty;
					$tot_total_yet_to_delv+=$yet_to_delv;
					$tot_total_yet_to_bill+=$yet_to_bill;
					$tot_total_bill_qty+=$bill_qty;
					$tot_total_bill_amnt+=$bill_amnt;
					$tot_total_payment_amnt+=$order_wise_payment_received;
					$tot_total_balance+=$balance;
					$grnd_tot_batch_balance += $batch_balance;
				}
				?>
			    	<tr class="tbl_bottom">
				        <td width="500" colspan="5" align="right"><b>Process Total:</b></td>
				        <td width="90" align="right"><? echo number_format($tot_order_qty); ?></td>
				        <td width="60" align="right"></td>
				        <td width="120" align="right"><? echo number_format($tot_rec_qty); ?></td>
				        <td width="120" align="right"><? echo number_format($tot_total_issue_qty); ?></td>
				        <td width="120" align="right"><? echo number_format($tot_material_blce); ?></td>
				        <td width="80" align="right"><? echo number_format($tot_batch_qty); ?></td>
				        <td width="80" align="right"><? echo number_format($tot_batch_balance); ?></td>
				        <td width="80" align="right"><? echo number_format($tot_dyeing_qty) ?></td>
				        <td width="80" align="right"><?php echo number_format($tot_order_qty-$tot_dyeing_qty) ?></td>
						<td width="80"><? echo number_format($tot_del_qty_gray); ?></td>
						<td width="80"><? echo number_format($tot_del_qty); ?></td>								
				        <td width="80" align="right"><? echo number_format($tot_yet_to_delv); ?></td>				        
			    	</tr>
			        <tr class="tbl_bottom">
			            <td width="500" colspan="5" align="right">Grand Total:</td>
			            <td width="90" align="right"><? echo number_format($tot_total_order_qty); ?></td>
			            <td width="60" align="right"></td>
			            <td width="120"><? echo number_format($tot_total_rec_qty); ?></td>
			            <td width="120"><? echo number_format($tot_total_issue_qty); ?></td>
			            <td width="120"><? echo number_format($tot_total_material_blce); ?></td>
			            <td width="80"><? echo number_format($tot_tottal_batch_qty); ?></td>
			            <td width="80"><? echo number_format($grnd_tot_batch_balance); ?></td>
			            <td width="80"><? echo number_format($tot_total_dyeing_qty); ?></td>
			            <td width="80"><?php echo number_format($tot_total_order_qty-$tot_total_dyeing_qty); ?></td>
	                    <td width="80"><? echo number_format($tot_total_del_qty_gray); ?></td>
	                    <td width="80"><? echo number_format($tot_total_del_qty); ?></td>		                    
			            <td width="80"><? echo number_format($tot_total_yet_to_delv); ?></td>
			        </tr>
			    </table>        
		    </div>
	    </div>
	    <?
	}
	else
	{

	}
}

if($action=="material_desc_popup")
{
	echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
	
	$expData=explode('_',$order_id);
	?>
    <script>
	function generate_report(action,update_id,txt_receive_no,cbo_company_name,cbo_location_name,cbo_party_name,txt_receive_date,txt_receive_challan)
	{
	 
		if (txt_receive_no=="")
			{
				alert('Recv No not Found.');
				return;
			}
			else
			{
				
				
				var report_title='SubCon Material Receive';
				var data="action="+action+'&update_id='+update_id+'&txt_receive_no='+txt_receive_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_party_name='+cbo_party_name+'&txt_receive_date='+txt_receive_date+'&txt_receive_challan='+txt_receive_challan;
				//freeze_window(5);
				//alert(data);
				http.open("POST","../../requires/sub_contract_material_receive_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_report_reponse;
			}
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}
</script>

    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Receive Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Receive ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Rec. Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Receive Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
				// echo $expData[0].'='.$color_id;//die;
				if($color_id!="") $color_cond="and b.color_id=$color_id";
				else  $color_cond="";
				$clr_cond = ($color_id !=0) ? "and b.color_id=$color_id" : "";
                
                $sql= "select a.id,a.company_id,a.location_id,a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and a.trans_type=1 $clr_cond group by a.company_id,a.location_id,a.id,a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
              // echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><a href="##" onclick="generate_report('show_material_receive_report','<? echo $row[csf("id")]; ?>','<? echo $row[csf("sys_no")]; ?>','<? echo $row[csf("company_id")]; ?>','<? echo $row[csf("location_id")]; ?>','<? echo $row[csf("party_id")]; ?>','<? echo $row[csf("subcon_date")]; ?>','<? echo $row[csf("chalan_no")]; ?>')"><? echo $row[csf("prefix_no_num")];?></a> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Return Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql_ret= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.return_date, a.party_id, b.order_id, b.item_category_id, b.material_description,  sum(b.quantity) as quantity from  sub_material_return_mst a, sub_material_return_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.return_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description order by a.sys_no, a.return_date";
             // echo $sql_ret;
                $material_ret_sql= sql_select($sql_ret);
                foreach( $material_ret_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
					$material_name=$row[csf("material_description")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("return_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_ret_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_ret_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <div style="display:none" id="data_panel"></div>
 </div> 
	<?
	exit();
}
if($action=="material_desc_popup_summary")
{
	echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
	
	$expData=explode('_',$order_id);
	?>
    <script>
	function generate_report(action,update_id,txt_receive_no,cbo_company_name,cbo_location_name,cbo_party_name,txt_receive_date,txt_receive_challan)
	{
	 
		if (txt_receive_no=="")
			{
				alert('Recv No not Found.');
				return;
			}
			else
			{
				
				
				var report_title='SubCon Material Receive';
				var data="action="+action+'&update_id='+update_id+'&txt_receive_no='+txt_receive_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_party_name='+cbo_party_name+'&txt_receive_date='+txt_receive_date+'&txt_receive_challan='+txt_receive_challan;
				//freeze_window(5);
				//alert(data);
				http.open("POST","../../requires/sub_contract_material_receive_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_report_reponse;
			}
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}
</script>

    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Receive Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Receive ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Rec. Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Receive Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
				// echo $expData[0].'='.$color_id;//die;
				//if($color_id!="") $color_cond="and b.color_id=$color_id";
				//else  $color_cond="";
                
                $sql= "select a.id,a.company_id,a.location_id,a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and a.trans_type=1 $color_cond group by a.company_id,a.location_id,a.id,a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
              // echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><a href="##" onclick="generate_report('show_material_receive_report','<? echo $row[csf("id")]; ?>','<? echo $row[csf("sys_no")]; ?>','<? echo $row[csf("company_id")]; ?>','<? echo $row[csf("location_id")]; ?>','<? echo $row[csf("party_id")]; ?>','<? echo $row[csf("subcon_date")]; ?>','<? echo $row[csf("chalan_no")]; ?>')"><? echo $row[csf("prefix_no_num")];?></a> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Return Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql_ret= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.return_date, a.party_id, b.order_id, b.item_category_id, b.material_description,  sum(b.quantity) as quantity from  sub_material_return_mst a, sub_material_return_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.return_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description order by a.sys_no, a.return_date";
             // echo $sql_ret;
                $material_ret_sql= sql_select($sql_ret);
                foreach( $material_ret_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
					$material_name=$row[csf("material_description")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("return_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_ret_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_ret_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <div style="display:none" id="data_panel"></div>
 </div> 
	<?
	exit();
}
//material_desc_popup_summary
if($action=="material_desc_popup_summary")
{
	echo load_html_head_contents("Material Issue Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <script>
 
	function generate_report2(cbo_company_name,update_id,txt_issue_no,cbo_location_name,action)
		{
			var report_title='SubCon Material Issue';
			print_report( cbo_company_name+'*'+update_id+'*'+txt_issue_no+'*'+report_title+'*'+cbo_location_name, "material_issue_print", "../../requires/sub_contract_material_issue_controller") 
			//return;
			//show_msg("3");
		}
</script>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Issue ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Issue Date</th>
                        <th width="60">Issue To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $i=0;
                // if($color_id!="") $color_cond="and b.color_id=$color_id";
					// else $color_cond="";
                $sql= "select a.id,a.sys_no, a.prefix_no_num, a.company_id,a.location_id,a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=2 $color_cond  group by a.id,a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.company_id,a.location_id,a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
             //  echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
					//cbo_company_name,update_id,txt_issue_no,cbo_location_name,action
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><a href="##" onclick="generate_report2('<? echo $row[csf("company_id")]; ?>','<? echo $row[csf("id")]; ?>','<? echo $row[csf("sys_no")]; ?>','<? echo $row[csf("location_id")]; ?>','material_issue_print')"><? echo $row[csf("prefix_no_num")];?></a> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo  $issue_to; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
	<fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Return Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Return To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $i=0;
                
                $sql= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=3 $color_cond group by a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tott_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tott_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <div style="display:none" id="data_panel"></div>
 </div> 
	<?
	exit();
}
if($action=="material_desc_iss_popup_summary")
{
	echo load_html_head_contents("Material Issue Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <script>
 
	function generate_report2(cbo_company_name,update_id,txt_issue_no,cbo_location_name,action)
		{
			var report_title='SubCon Material Issue';
			print_report( cbo_company_name+'*'+update_id+'*'+txt_issue_no+'*'+report_title+'*'+cbo_location_name, "material_issue_print", "../../requires/sub_contract_material_issue_controller") 
			//return;
			//show_msg("3");
		}
</script>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Issue ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Issue Date</th>
                        <th width="60">Issue To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $i=0;
                // if($color_id!="") $color_cond="and b.color_id=$color_id";
					// else $color_cond="";
                $sql= "select a.id,a.sys_no, a.prefix_no_num, a.company_id,a.location_id,a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=2 $color_cond  group by a.id,a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.company_id,a.location_id,a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
             //  echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
					//cbo_company_name,update_id,txt_issue_no,cbo_location_name,action
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><a href="##" onclick="generate_report2('<? echo $row[csf("company_id")]; ?>','<? echo $row[csf("id")]; ?>','<? echo $row[csf("sys_no")]; ?>','<? echo $row[csf("location_id")]; ?>','material_issue_print')"><? echo $row[csf("prefix_no_num")];?></a> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo  $issue_to; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
	<fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Return Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Return To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $i=0;
                
                $sql= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=3 $color_cond group by a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tott_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tott_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <div style="display:none" id="data_panel"></div>
 </div> 
	<?
	exit();
}
if($action=="material_desc_iss_rtn_popup_summary")
{
	echo load_html_head_contents("Material Issue Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <script>
 
		function generate_report2(cbo_company_name,update_id,txt_issue_no,cbo_location_name,action)
			{
				var report_title='SubCon Material Issue';
				print_report( cbo_company_name+'*'+update_id+'*'+txt_issue_no+'*'+report_title+'*'+cbo_location_name, "material_issue_print", "../../requires/sub_contract_material_issue_controller") 
				//return;
				//show_msg("3");
			}
	</script>
	<fieldset style="width:1000px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="13"> Issue Return Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="100">Trans Ref No</th>
                        <th width="60">Trans Type</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Return To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th >Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $i=0;
                
                $sql= "SELECT a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=3 $color_cond group by a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="100"><? echo $row[csf("sys_no")];?> </td>
                    <td width="60"><? echo "Issue Return";?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tott_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="10" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tott_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <div style="display:none" id="data_panel"></div>
 </div> 
	<?
	exit();
}

if($action=="material_desc_rcv_rtn_popup_summary")
{
	echo load_html_head_contents("Material Receive Return Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <script>
 
		function generate_report2(cbo_company_name,update_id,txt_issue_no,cbo_location_name,action)
			{
				var report_title='SubCon Material Issue';
				print_report( cbo_company_name+'*'+update_id+'*'+txt_issue_no+'*'+report_title+'*'+cbo_location_name, "material_issue_print", "../../requires/sub_contract_material_issue_controller") 
				//return;
				//show_msg("3");
			}
	</script>
	<fieldset style="width:1040px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="13"> Receive Return Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="100">Trans Ref No</th>
                        <th width="100">Trans Type</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Return To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th >Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $i=0;
                
				$rcv_rtn_sql= " SELECT a.sys_no,
				a.prefix_no_num,
				a.chalan_no,
				a.return_date,
				a.party_id,
				a.sub_mat_recv_id,
				b.order_id,
				b.item_category_id,
				b.material_description,
				SUM (b.quantity) AS quantity,
				b.roll_bag,
				b.cone
				FROM sub_material_return_mst a, sub_material_return_dtls b
				WHERE a.id = b.mst_id
						AND b.order_id in ($expData[0])
						AND a.status_active = 1
						AND a.is_deleted = 0
						AND b.status_active = 1
						AND b.is_deleted = 0
						$color_cond
				GROUP BY a.sys_no,
							a.prefix_no_num,
							a.chalan_no,
							a.return_date,
							a.party_id,
							a.sub_mat_recv_id,
							b.order_id,
							b.item_category_id,
							b.id,
							b.material_description,
							b.roll_bag,
							b.cone
				ORDER BY a.sys_no, A.return_date";

                $material_rcv_rtn_rslt= sql_select($rcv_rtn_sql);

				$subMatRecvIdArr = [];
				foreach( $material_rcv_rtn_rslt as $row )
                {
					if($row[csf("sub_mat_recv_id")] !="")
					{
						array_push($subMatRecvIdArr,$row[csf("sub_mat_recv_id")]);
					}
				}

				if(!empty($subMatRecvIdArr))
				{
					
					$nameArray=sql_select("SELECT a.id, a.sys_no from sub_material_mst a where a.status_active=1 and a.is_deleted=0 ".where_con_using_array($subMatRecvIdArr,0,'a.id')."" );
					$refInfoArr=[];
					foreach ($nameArray as $row)
					{
						$refInfoArr[$row[csf("id")]]['sys_no']=$row[csf("sys_no")];
					}
					unset($nameArray);
				}

                foreach( $material_rcv_rtn_rslt as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
				
					$sys_no = $refInfoArr[$row[csf("sub_mat_recv_id")]]['sys_no'];
					$sys_no_data = explode("-",$sys_no);
					$trns_type='';
					if($sys_no_data[1]=='RECV')
					{
						$trns_type='Receive';
					}
					elseif($sys_no_data[1]=='RTN')
					{
						$trns_type='Issue Return';
					}

               		?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="60" align="center"><? echo $row[csf("prefix_no_num")];?> </td>
						<td width="100" align="center"><? echo $sys_no;?> </td>
						<td width="100" align="center"><? echo $trns_type;?> </td>
						<td width="70" align="center"><? echo $row[csf("chalan_no")];?> </td>
						<td width="70" align="center"><? echo change_date_format($row[csf("return_date")]);?> </td> 
						<td width="60" align="center"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
						<td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
						<td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
						<td align="center" width="150"><? echo $row[csf("material_description")]; ?></td>
						<td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
						<td align="center" width="80"><? echo $row[csf("roll_bag")];; ?></td>
						<td><p><? echo $row[csf("cone")]; ?></p></td>
					</tr>
					<? 
					$tott_qty+=$row[csf("quantity")];
                } ?>
					<tr class="tbl_bottom">
						<td colspan="10" align="right">Total: </td>
						<td align="right"><p><? echo number_format($tott_qty,2); ?></p></td>
						<td colspan="2">&nbsp;</td>
					</tr>
            </table>
        </div> 
	</fieldset>
    <div style="display:none" id="data_panel"></div>
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
    <script>
 
	function generate_report2(cbo_company_name,update_id,txt_issue_no,cbo_location_name,action)
		{
			var report_title='SubCon Material Issue';
			print_report( cbo_company_name+'*'+update_id+'*'+txt_issue_no+'*'+report_title+'*'+cbo_location_name, "material_issue_print", "../../requires/sub_contract_material_issue_controller") 
			//return;
			//show_msg("3");
		}
</script>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Issue ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Issue Date</th>
                        <th width="60">Issue To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $i=0;
                 if($color_id!=0) $color_cond="and b.color_id=$color_id";
					 else $color_cond="";
                $sql= "select a.id,a.sys_no, a.prefix_no_num, a.company_id,a.location_id,a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=2 $color_cond  group by a.id,a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.company_id,a.location_id,a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
             //  echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
					//cbo_company_name,update_id,txt_issue_no,cbo_location_name,action
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><a href="##" onclick="generate_report2('<? echo $row[csf("company_id")]; ?>','<? echo $row[csf("id")]; ?>','<? echo $row[csf("sys_no")]; ?>','<? echo $row[csf("location_id")]; ?>','material_issue_print')"><? echo $row[csf("prefix_no_num")];?></a> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo  $issue_to; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
	<fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Return Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Return To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $i=0;
                
                $sql= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=3 $color_cond group by a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tott_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tott_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <div style="display:none" id="data_panel"></div>
 </div> 
	<?
	exit();
}

if($action=="material_desc_rtn_popup")
{
	echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:1100px">
        <div style="width:100%;" align="left">
            <table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="13">Return Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Return Date</th>
                        <th width="100">Rtn. Challan No</th>
                        <th width="100">Rcv. Challan No</th>
                        <th width="100">Party</th>
                        <th width="100">Style</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="80">Grey Dia</th>
                        <th width="80">Fin. Dia</th>
                        <th width="100">Color</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:1100; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="1080" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "SELECT id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "SELECT id, order_no from subcon_ord_dtls",'id','order_no');
                $style_arr=return_library_array( "SELECT id, cust_style_ref from subcon_ord_dtls",'id','cust_style_ref');
				$color_arr=return_library_array( "SELECT id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql_ret= "SELECT a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.color_id, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=3 group by a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.color_id, b.grey_dia, b.fin_dia order by a.subcon_date";
               //echo $sql;
                $material_ret_sql= sql_select($sql_ret);
                foreach( $material_ret_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
					$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="100"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="100"><? //echo $row[csf("prefix_no_num")];?> </td>
                    <td width="100"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td width="100"><p><? echo $style_arr[$row[csf("order_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="80"><? echo $row[csf("grey_dia")]; ?></td>
                    <td align="center" width="80"><? echo $row[csf("fin_dia")]; ?></td>
                    <td align="right" width="100"><? $color_arr[$row[csf("color_id")]]; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                </tr>
                <? 
                $tot_ret_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="11" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_ret_qty,2); ?></p></td>
                    <td>&nbsp;</td>
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
	$order_id=$expData[0];
	$process_id=$expData[1];
	$btn_type=$expData[2];
	//echo $process_id.'Dx';
		?>
        <script>
        function generate_report2(cbo_company_id,txt_finishing_id,cbo_location_name,process_id)
		{
			
			// alert(process_id);
			// if(process_id==4)
			// {
				  var report_title='Fabric Finishing Entry';
			 print_report( cbo_company_id+'*'+txt_finishing_id+'*'+report_title+'*'+cbo_location_name, "subcon_fabric_finishing_print", "../../requires/subcon_fabric_finishing_production_controller" ) ;
			 //}
			 
			 return;
		}
        </script>
        <fieldset style="width:1020px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="60"><? if ($process_id==3) echo "Batch NO"; else echo "Sys ID" ?></th>
                            <th width="70">Prod. Date</th>
                            <th width="100">Party</th>
                            <th width="80">Order No</th>
                            <th width="130">Process</th>
							<th width="100">Floor Name</th>
							<th width="100">Machine Name</th>
                            <th width="150">Description</th>
                            <th width="80">Yarn Lot No</th>
                            <th width="">Prod. Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$po_party_arr=return_library_array( "select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','party_id');
					$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
					$machine_no_arr=return_library_array( "select id, machine_no from  lib_machine_name ",'id','machine_no');
					$floor_name_arr=return_library_array( "select id, floor_name from  lib_prod_floor",'id','floor_name');
                    $i=0;
					if($color_id!="") $color_cond="and b.color_id=$color_id";
					else  $color_cond="";
                
				
					if ($process_id==1)
					{
						 $sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=1 group by production_date, order_id, gmts_item_id";
					}
					else if ($process_id==5)
					{
						$sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=2 group by production_date, order_id, gmts_item_id";

					}
					else if ($process_id==11)
					{
						$sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=4 group by production_date, order_id, gmts_item_id";

					}
					else if ($process_id==2)
					{
						$sql="select a.prefix_no_num as sys_id, a.product_no, a.party_id, a.product_date as production_date, b.order_id, b.process, b.cons_comp_id as item_id, b.color_id, b.yarn_lot, sum(b.no_of_roll) as roll_qty, sum(b.product_qnty) as production_qnty,b.machine_id as machine_no,b.floor_id from subcon_production_mst a, subcon_production_dtls b where b.order_id='$order_id' and b.product_type=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, b.order_id, b.process, b.cons_comp_id, b.color_id,b.yarn_lot,b.machine_id,b.floor_id order by b.color_id";
					}
					else if($process_id==3)
					{
						if($db_type==0)
						{
							$sql="select b.batch_no as sys_id, a.process_end_date as production_date, c.po_id as order_id, c.item_description as item_id, a.process_id as process, sum(c.batch_qnty) as production_qnty,b.machine_no,b.floor_id from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and c.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.batch_no, a.process_end_date, a.process_id, c.po_id, c.item_description,b.machine_no,b.floor_id ";
						}
						elseif($db_type==2)
						{
							$sql="select b.batch_no as sys_id, a.process_end_date as production_date, c.po_id as order_id, c.item_description as item_id, a.process_id as process, sum(c.batch_qnty) as production_qnty,b.machine_no,b.floor_id from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and c.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.batch_no, a.process_end_date, a.process_id, c.po_id, c.item_description,b.machine_no,b.floor_id ";
						}
					}
					else if($process_id==4)
					{
						 $sql = "select a.company_id,a.location_id,a.prefix_no_num as sys_id, a.product_no, a.product_date as production_date, a.party_id, c.order_id, b.process as process, b.fabric_description as item_id, sum(c.quantity) as production_qnty,b.machine_id,b.floor_id, b.yarn_lot from subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c where a.id=b.mst_id and b.id=c.dtls_id and c.order_id in ($order_id) and b.product_type='$process_id' $color_cond   group by a.company_id,a.location_id,a.prefix_no_num, a.product_no, a.party_id, a.product_date, c.order_id, b.process, b.fabric_description,b.machine_id,b.floor_id,b.yarn_lot";

						//$sql="select a.prefix_no_num as sys_id, a.product_no, a.party_id, a.product_date as production_date, b.order_id, b.process, b.cons_comp_id as item_id, b.color_id, b.yarn_lot, sum(b.no_of_roll) as roll_qty, sum(b.product_qnty) as production_qnty,b.machine_id as machine_no,b.floor_id from subcon_production_mst a, subcon_production_dtls b where b.order_id='$order_id' and b.product_type=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $color_cond group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, b.order_id, b.process, b.cons_comp_id, b.color_id,b.yarn_lot,b.machine_id,b.floor_id order by b.color_id";
					}
                   // echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						
						if ($process_id==1 || $process_id==5 || $process_id==11)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
							$party_name=$party_arr[$po_party_arr[$row[csf("order_id")]]];
						}
						else if ($process_id==2)
						{
							$party_name=$party_arr[$row[csf("party_id")]];
							$process_name=$conversion_cost_head_array[$row[csf("process")]];
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($process_id==3)
						{
							$party_name=$party_arr[$po_party_arr[$row[csf("order_id")]]];
							$process_name="";
							$process_idArr=explode(',',$row[csf('process')]);
							foreach($process_idArr as $val)
							{
								if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=','.$conversion_cost_head_array[$val];
							}
							$item_name=$row[csf('item_id')];
						}
						else if ($process_id==4)
						{
							$party_name=$party_arr[$row[csf("party_id")]];
							$process_name="";
							$process_idArr=explode(',',$row[csf('process')]);
							foreach($process_idArr as $val)
							{
								if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=','.$conversion_cost_head_array[$val];
							}
							$item_name=$row[csf('item_id')];
						}
						else
						{
							$item_name=$row[csf('item_id')];
						}
						if ($process_id==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td colspan="9" align="right"><b>Color Total:</b></td>
										<td align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td colspan="11" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="11" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
						   ?>
						   
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								 
                                  <td width="60"><a href="##" onclick="generate_report2('<? echo $row[csf("company_id")]; ?>','<? echo $row[csf("id")]; ?>','<? echo $row[csf("sys_no")]; ?>','<? echo $row[csf("location_id")]; ?>','material_issue_print')"><? echo $row[csf("sys_id")];?></a> </td>
                                  
								<td width="70"><? echo change_date_format($row[csf("production_date")]);?> </td> 
								<td width="100"><p><? echo $party_name; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="130"><p><? echo $process_name; ?></p></td>
								<td align="center" width="100" title="<?=$row[csf("floor_id")];?>"><p><? echo $floor_name_arr[$row[csf("floor_id")]]; ?></p></td>
								<td align="center" width="100" title="<?=$row[csf("machine_no")];?>"><p><? echo $machine_no_arr[$row[csf("machine_no")]]; ?></p></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="center" width="80"><p><? echo $row[csf("yarn_lot")]; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("production_qnty")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("production_qnty")];
							$tot_qty+=$row[csf("production_qnty")];
							
						}
						else
						{
							//echo $process_id.'DScc';
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60" align="center"><a href="##" onclick="generate_report2('<? echo $row[csf("company_id")]; ?>','<? echo $row[csf("product_no")]; ?>','<? echo $row[csf("location_id")]; ?>','<? echo $process_id; ?>')"><? echo $row[csf("sys_id")];?></a> </td>
								<td width="70"><? echo change_date_format($row[csf("production_date")]);?> </td> 
								<td width="100"><p><? echo $party_name; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="130"><p><? echo $process_name; ?></p></td>
								<td align="center" width="100" title="<?=$row[csf("floor_id")];?>"><p><? echo $floor_name_arr[$row[csf("floor_id")]]; ?></p></td>
								<td align="center" width="100" title="<?=$row[csf("machine_no")];?>"><p><? echo $machine_no_arr[$row[csf("machine_no")]]; ?></p></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="center" width="80"><p><? echo $row[csf("yarn_lot")]; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("production_qnty")],2); ?></td>
							</tr>
							<?
							$tot_qty+=$row[csf("production_qnty")];
						}
					}
					if ($process_id==2)
					{ 
                    ?>
                        <tr class="tbl_bottom">
                            <td colspan="10" align="right"><b>Color Total:</b></td>
                            <td align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
					<? } ?>
                    <tr class="tbl_bottom">
                    	<td colspan="10" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
		</fieldset>	  
		<?
		
	exit();
}

if($action=="bill_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
      <script>
        function generate_report2(cbo_company_id,update_id,txt_bill_no,location_id)
		{
			 var report_title='SubCon Dye And Finishing Delivery';
			 var type=1;show_val_column="1";
			 alert(update_id);
			print_report( cbo_company_id+'*'+update_id+'*'+txt_bill_no+'*'+report_title+'*'+type+'*'+show_val_column+'*'+location_id, "fabric_finishing_print", "../../requires//sub_fabric_finishing_bill_issue_controller");
			// return;
		}
		
        </script>
        
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Bill ID</th>
                        <th width="70">Bill Date</th>
                        <th width="100">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Bill Qty</th>
                        <th>Amount</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
				$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
                $i=0;
				if($color_id!="") $color_cond="and b.color_id=$color_id";
				else  $color_cond="";
				
                $sql= "SELECT a.id,a.company_id,a.bill_no,a.location_id, a.bill_date, a.party_id, b.order_id, a.process_id, b.item_id, sum(b.delivery_qty) as quantity, sum(b.amount) as amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $color_cond  group by  a.id,a.company_id,a.location_id,a.bill_no, a.bill_date, a.party_id, b.order_id, a.process_id, b.item_id order by a.bill_no, a.bill_date";
                // echo $sql;
                $production_sql= sql_select($sql);
                foreach( $production_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
					{
						$item_name=$garments_item[$row[csf('item_id')]];
					}
					else if ($row[csf("process_id")]==2)
					{
						$item_name=$kniting_item_arr[$row[csf('item_id')]];
					}
					else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
					{
						$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
					}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"> <a href="##" onclick="generate_report2('<? echo $row[csf("company_id")]; ?>','<? echo $row[csf("id")]; ?>','<? echo $row[csf("bill_no")]; ?>','<? echo $row[csf("location_id")]; ?>')"><? echo $row[csf("bill_no")];?></a> </td>
                    <td width="70"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
                    <td width="100"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
                    <td align="center" width="150"><? echo $item_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="right" width=""><? echo number_format($row[csf("amount")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                $tot_amount+=$row[csf("amount")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="7" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td align="right"><p><? echo number_format($tot_amount,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="image_view_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Work Progress Info","../../../", 1, 1, $unicode);
	//echo "select master_tble_id,image_location from common_photo_library where form_name='sub_contract_order_entry' and file_type=1 and master_tble_id='$id'";

	$imge_data=sql_select("select id,master_tble_id,image_location from common_photo_library where form_name='sub_contract_order_entry' and file_type=1 and master_tble_id='$id'");
	?>
	<table>
        <tr>
			<?
            foreach($imge_data as $row)
            {
				?>
                    <td><img src='../../../<? echo $imge_arr[$row[csf("id")]]; ?>' height='100px' width='100px' /></td>
				<?
            }
            ?>
        </tr>
	</table>
	<?
	exit();
}

if($action=="batch_qty_pop_up")
{	
	echo load_html_head_contents("Batch Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$btn_type=$expData[1];
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
	// echo $color_id.'d';
	?>
      <script>
        function generate_report2(cbo_company_name,update_id,batch_sl_no,cbo_location_name,action)
		{
			 var report_title='SubCon Batch Creation';
			 print_report( cbo_company_name+'*'+update_id+'*'+batch_sl_no+'*'+report_title, "batch_card_print", "../../requires/subcon_batch_creation_controller" ) ;
			 return;
		}
        </script>
    <?
	if ($btn_type == "") 
	{
		?>
   
	    <fieldset style="width:800px">
	        <div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="80">Batch No</th>
	                        <th width="30">Ext.</th>
	                        <th width="65">Batch Date</th>
	                        <th width="100">Color</th>
	                        <th width="100">Order</th>
	                        <th width="100">Rec. Challan</th>
	                        <th width="180">Description</th>
	                        <th width="">Batch Qty</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>  
	        <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
				<?
					  if($color_id!="") $color_cond="and a.color_id=$color_id";
					  else $color_cond="";
					  $sql_batch="Select a.id,a.batch_no,a.company_id,a.batch_sl_no, a.extention_no, a.batch_date, a.color_id, b.po_id, b.item_description, b.rec_challan, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and b.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $color_cond group by a.id,a.company_id,a.batch_sl_no,a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id, b.item_description, b.rec_challan";
					$sql_batch_result=sql_select($sql_batch); $i=0;
					foreach ($sql_batch_result as $row)
					{
						$i++;
						if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
                             <td width="80"><a href="##" onclick="generate_report2('<? echo $row[csf("company_id")]; ?>','<? echo $row[csf("id")]; ?>','<? echo $row[csf("batch_sl_no")]; ?>','batch_card_print')"><? echo $row[csf("batch_no")];?></a> </td>
                             
							 
							<td width="30" align="center"><? echo $row[csf("extention_no")];?> </td>
							<td width="65"><? echo change_date_format($row[csf("batch_date")]);?> </td> 
							<td width="100"><p><? echo $color_arr[$row[csf("color_id")]];?></p></td>
							<td width="100"><? echo $po_arr[$row[csf("po_id")]]; ?></td>
							<td width="100"><p><? echo $row[csf("rec_challan")]; ?></p></td>
							<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
							<td align="right" width=""><? echo number_format($row[csf("batch_qnty")],2); ?></td>
						</tr>
						<?
						$tot_batch_qnty+=$row[csf("batch_qnty")];
					}
					?>
	                <tr class="tbl_bottom">
	                    <td colspan="8" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_batch_qnty,2); ?></p></td>
	                </tr>
	            </table>
	        </div> 
		</fieldset>
		<? 
	} 	
	else // summary btn
	{		
		echo load_html_head_contents("Batch Details", "../../../", 1, 1,$unicode,'','');
		//echo $order_id;//die;
		$expData=explode('_',$order_id);
		$order_id=$expData[0];
		//$process_id=$expData[1];
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
		$yarn_lot_arr=array();
		if($db_type==0)
		{
			$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id,group_concat(distinct(a.yarn_lot)) as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' group by a.prod_id, b.po_breakdown_id");
		}
		else if($db_type==2)
		{	
			$yarn_lot_data=sql_select("SELECT  b.order_id, b.yarn_lot 
				from subcon_production_mst a, subcon_production_dtls b 
				where a.id=b.mst_id and b.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.yarn_lot!='0' 
				group by b.order_id, b.yarn_lot ");	
		}
		
		foreach($yarn_lot_data as $rows)
		{
			$yarn_lot_arr[$rows[csf('order_id')]] = $rows[csf('yarn_lot')];
		}
		// print_r($yarn_lot_arr);
		?>
	    <fieldset style="width:950px">
	        <div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="3%">SL</th>
	                        <th width="8%">Batch Date</th>
	                        <th width="10%">Batch No</th>
	                        <th width="10%">Party</th>
	                        <th width="10%">Order No.</th>
	                        <th width="10%">Batch Color</th>
	                        <th width="8%">Construction</th>
	                        <th width="8%">Composition</th>
	                        <th width="6%">Dia</th>
	                        <th width="6%">GSM</th>
	                        <th width="7%">Lot</th>
	                        <th width="7%">Batch Qty</th>
	                        <th width="7%">Batch Weight</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>  
	        <div style="width:100%; max-height:330px; overflow-y:auto;" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
				<?
					$const_comp = array();
					$const_comp_sql = "select comapny_id, buyer_id, const_comp from lib_subcon_charge order by comapny_id";
					$const_comp_result = sql_select($const_comp_sql);
					foreach ($const_comp_result as $key => $value) {
						
					}
					$sql_batch="SELECT a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no_id,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type, c.job_no_mst,d.job_no_prefix_num,d.party_id as buyer_name, f.gsm,f.grey_dia
					from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_ord_dtls c,subcon_ord_mst d, sub_material_mst e, sub_material_dtls f  
					where a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and c.id=$order_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=36 and f.order_id = $order_id and e.id = f.mst_id and f.id = b.prod_id  and f.status_active=1 and f.is_deleted =0 and c.status_active =1 and c.is_deleted =0
					GROUP BY a.batch_no, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.party_id,f.gsm,f.grey_dia order by a.batch_no";
					// echo $sql_batch; //and d.entry_form = 238
					$sql_batch_result=sql_select($sql_batch); $i=0;
					foreach ($sql_batch_result as $row)
					{
						$i++;
						if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$desc=explode(",",$row[csf('item_description')]);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="3%"><? echo $i; ?></td>
							<td width="8%"><? echo change_date_format($row[csf("batch_date")]);?> </td> 
							<td width="10%"><? echo $row[csf("batch_no")];?> </td>
							<td width="10%"><? echo $party_arr[$row[csf('buyer_name')]]; ?></td>
							<td width="10%"><? echo $po_arr[$row[csf("po_id")]]; ?></td>
							<td width="10%"><p><? echo $color_arr[$row[csf("color_id")]];?></p></td>
							<td width="8%"><? echo $desc[0];?></td>
							<td width="8%"><p><? echo $desc[1]; ?></p></td>
							<td align="center" width="6%"><p><? echo $row[csf("grey_dia")]; ?></p></td>
							<td align="center" width="6%"><? echo $row[csf("gsm")]; ?></td>
							<td align="right" width="7%"><? echo $yarn_lot_arr[$rows[csf('order_id')]]; ?></td>
							<td align="right" width="7%"><? echo number_format($row[csf("batch_qnty")]); ?></td>
							<td align="right" width="7%"><? echo $row[csf("batch_weight")]; ?></td>
						</tr>
						<?
						$tot_batch_qnty+=$row[csf("batch_qnty")];
						$tot_batch_weight+=$row[csf("batch_weight")];
					}
					?>
	                <tr class="tbl_bottom">
	                    <td colspan="11" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_batch_qnty); ?></p></td>
	                    <td align="right"><p><? echo $tot_batch_weight; ?></p></td>
	                </tr>
	            </table>
	        </div> 
		</fieldset>	
		<?	
	}		
	exit();
}

if($action=="payment_rec_pop_up")
{
	echo load_html_head_contents("Payment Receive Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$order_bill_amount=$expData[1];
	//$process_id=$expData[1];
	$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Rec. No</th>
                        <th width="120">Party</th>
                        <th width="65">Rec. Date</th>
                        <th width="80">Instrument</th>
                        <th width="60">Currency</th>
                        <th width="120">Bill No</th>
                        <th width="80">Order No</th>
                        <th width="65">Bill Date</th>
                        <th width="">Rec. Amount</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
			<?
			$order_wise_tot_bill="select a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.order_id='$order_id' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id asc";
			$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
			foreach ($order_wise_tot_bill_result as $row)
			{
				$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
			}

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

				//$payment_sql="select a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id, sum(b.total_adjusted) as rec_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and d.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id";

			$payment_sql="select a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id, b.total_adjusted as rec_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and d.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id,b.total_adjusted";

				$payment_sql_result=sql_select($payment_sql); $i=0;
				foreach ($payment_sql_result as $row)
				{
					$i++;
					if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf("receive_no")];?> </td>
						<td width="120" align="center"><? echo $buyer_arr[$row[csf("party_name")]];?> </td>
						<td width="65"><? echo change_date_format($row[csf("receipt_date")]);?> </td> 
						<td width="80"><p><? echo $instrument_payment[$row[csf("instrument_id")]];?></p></td>
						<td width="60"><? echo $currency[$row[csf("currency_id")]]; ?></td>
						<td width="120"><p><? echo $row[csf("bill_no")]; ?></p></td>
                        <td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
						<td width="65"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
						<td align="right" width="">
							<? 
							$received_amount = ($row[csf("rec_amount")]/$order_wise_tot_bill_arr[$order_id])*$order_bill_amount;
							echo number_format($received_amount,2); 
							
							//echo number_format($row[csf("rec_amount")],2); 
							?>
						</td>
					</tr>
					<?
					$tot_rec_amount+=$received_amount;
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="9" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_rec_amount,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
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
                    	<th colspan="11">Order Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Order No</th>
                        <th width="70">Category</th>
                        <th width="120">Item Description </th>
                        <th width="80">Color</th>
                        <th width="60">Size</th>
                        <th width="80">Receive Date</th>
                        <th width="50">Rate</th>
                        <th width="93">Quantity</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				//$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

                $item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
				$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;

                $sql="SELECT a.party_id, b.order_no, b.order_rcv_date, b.main_process_id, c.item_id, c.color_id, c.size_id, c.qnty, c.rate, c.gsm, c.grey_dia, c.finish_dia, c.dia_width_type 
                from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c 
                where c.mst_id=a.id and c.order_id=b.id and a.subcon_job=b.job_no_mst and b.id=$expData[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                // echo $sql;
                $order_dtls_sql= sql_select($sql);
                foreach( $order_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

                    $process_id=$row[csf('main_process_id')];
					
						//$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("order_no")];?> </td>
                    <td align="center" width="70"><? echo $production_process[$row[csf("main_process_id")]];?> </td>
                    <td width="120">
                    	<? 
			                if($process_id==2 || $process_id==3 || $process_id==4 || $process_id==6 || $process_id==7)
							{
								echo $item_arr[$row[csf('item_id')]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("finish_dia")];	
							}
							else
							{
								echo $garments_item[$row[csf('item_id')]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("finish_dia")];
							}
                    	?> 
                    </td> 
                    <td align="center" width="80"><p><? echo  $color_arr[$row[csf("color_id")]]; ?></p></td>
                    <td align="center" width="60"><? echo $size_arr[$row[csf("size_id")]]; ?></td>
                    <td align="center" width="80"><? echo change_date_format($row[csf("order_rcv_date")]); ?></td>
                    
                    <td align="right" width="50"><? echo $row[csf("rate")]; ?> &nbsp; </td>
                    <td align="right" width="80"><? echo number_format($row[csf("qnty")]); ?> &nbsp;</td>
                   
                </tr>
                <? 
                $tot_qty+=$row[csf("qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: &nbsp;</td>
                    <td align="right"><p><? echo number_format($tot_qty); ?> &nbsp; </p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="delivery_qty_pop_up-backup")// bavck up 
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	//echo $expData[2].'=';
	if($expData[2] == "")
	{
		?>
         <script>
        function generate_report2(cbo_company_id,txt_finishing_id,cbo_location_name,process_id)
		{
			
			// alert(process_id);
			 if(process_id==4)
			 {
				  var report_title='Fabric Finishing Entry';
			 print_report( cbo_company_id+'*'+txt_finishing_id+'*'+report_title+'*'+cbo_location_name, "subcon_fabric_finishing_print", "../../requires/subcon_fabric_finishing_production_controller" ) ;
			 }
			 
			 return;
		}
        </script>
        
		<div id="data_panel" align="center" style="width:100%">
			<script>
				function new_window()
				{
					// $(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
					// $(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
		<div id="details_reports" style="width: 840px;">
	        <fieldset style="width:820px">
	            <div style="width:100%;" align="left">
	                <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
	                	<caption><div  style="text-align: center;font-weight: bold;">Work Progress Report Details </div></caption>
	                    <thead>
	                    	<tr>
	                            <th width="30">SL</th>
	                            <th width="60">Delivery ID </th>
	                            <th width="70">Delivery Date</th>
	                            <th width="80">Batch No</th>
	                            <th width="80">Order No</th>
	                            <th width="80">Category</th>
	                            <th width="150">Description</th>
	                            <th width="100">Delivery Qty</th>
	                            <th title="((Grey qty - Fin. qty) / Fin. qty) * 100" width="90">Process Loss</th>
	                    	</tr>
	                    </thead>
	                </table>
	            </div>  
	            <div style="width:100%; max-height:500px; overflow-y:auto;" align="left">
	                <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1" >
	                    <?
						$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
						$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
						$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
						$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
						$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
	                    $i=0;
	                    $sql= "SELECT a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity, sum(b.gray_qty) as gray_qty from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
	                    // echo $sql;
						$production_sql= sql_select($sql); $color_array=array(); $k=1; $process_id=0;
						foreach( $production_sql as $row )
	                    {
	                        $i++;
	                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							$process_id=$row[csf("process_id")];
							if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
							{
								$item_name=$garments_item[$row[csf('item_id')]];
							}
							else if ($row[csf("process_id")]==2)
							{
								$item_name=$kniting_item_arr[$row[csf('item_id')]];
							}
							else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
							{
								$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
							}
							
							if ($row[csf("process_id")]==2)
							{
								if (!in_array($row[csf("color_id")],$color_array) )
								{
									if($k!=1)
									{
									?>
										<tr class="tbl_bottom">
											<td colspan="7" align="right"><b>Color Total:</b></td>
											<td width="100" align="right"><? echo number_format($color_qty); ?></td>
											<td align="right"><? echo number_format($color_process_loss,2); ?></td>
										</tr>
										<tr bgcolor="#dddddd">
											<td colspan="9" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
										</tr>
									<?
										unset($color_qty);
										unset($color_process_loss);
									}
									else
									{
										?>
										<tr bgcolor="#dddddd">
											<td colspan="9" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
										</tr>
										<?
									}					
									$color_array[]=$row[csf('color_id')];            
									$k++;
								}
								$process_loss = (($row[csf("gray_qty")] - $row[csf("quantity")]) / $row[csf("quantity")])*100;							
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
									<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
									<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
									<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
									<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
									<td align="center" width="150"><p><? echo $item_name; ?></p></td>
									<td width="100" align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
									<td align="right" width="90"><? echo number_format($process_loss,2); ?></td>
								</tr>
								<? 
								$color_qty+=$row[csf("quantity")];
								$tot_qty+=$row[csf("quantity")];
								$color_process_loss += $process_loss;
								$tot_process_loss += $process_loss;
							}
							else
							{
								$process_loss = (($row[csf("gray_qty")] - $row[csf("quantity")]) / $row[csf("quantity")])*100;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
									<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
									<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
									<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
									<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
									<td align="center" width="150"><p><? echo $item_name; ?></p></td>
									<td width="100" align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
									<td align="right" width="90"><? echo number_format($process_loss,2); ?></td>
								</tr>
								<? 
								$tot_qty+=$row[csf("quantity")];
								$tot_process_loss += $process_loss;
							}
						} 
						if($process_id==2)
						{
						?>
	                        <tr class="tbl_bottom">
	                            <td colspan="7" align="right"><b>Color Total:</b></td>
	                            <td align="right"><? echo number_format($color_qty); ?></td>
	                            <td align="right"><? echo number_format($color_process_loss); ?></td>
	                        </tr>
	                    <?
						}
						?>
	                    <tr class="tbl_bottom">
	                    	<td colspan="7" align="right">Total: </td>
	                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
	                        <td align="right"><p><? echo number_format($tot_process_loss,2); ?></p></td>
	                    </tr>
	                </table>
	            </div> 
			</fieldset>
		</div>
		<?
	}
	else
	{
		?>
         <script>
        function generate_report2(cbo_company_id,update_id,txt_sys_id,cbo_location,type)
		{
			// alert(process_id);
			var cbo_template_id=1;
			 var report_title='SubCon Dye And Finishing Delivery';
			 print_report( cbo_company_id+'*'+update_id+'*'+txt_sys_id+'*'+report_title+'*'+type+'*'+cbo_location+'*'+cbo_template_id, "subcon_delivery_entry_print5", "../../delivery/requires/subcon_dye_finishing_delivery_controller" )
			 return;
		}
        </script>
		<div id="data_panel" align="center" style="width:100%">
			<script>
				function new_window()
				{
					// $(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
					// $(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
		<div id="details_reports" style="width: 840px;">
	        <fieldset style="width:820px">
	        	<caption><div  style="text-align: center;font-weight: bold;">Work Progress Report Details </div></caption>
	            <div style="width:100%;" align="left">
	                <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
	                    <thead>
	                    	<tr>
	                            <th width="30">SL</th>
	                            <th width="60">Delivery ID</th>
	                            <th width="70">Delivery Date</th>
	                            <th width="80">Batch No</th>
	                            <th width="80">Order No</th>
	                            <th width="80">Category</th>
	                            <th width="150">Description</th>
	                            <th width="100">Delivery Qty</th>
	                            <th width="80">Grey Used Qty</th>
	                            <th title="((Grey qty - Fin. qty) / Fin. qty) * 100" width="90">Process Loss</th>
	                    	</tr>
	                    </thead>
	                </table>
	            </div>  
	            <div style="width:100%; max-height:230px; overflow-y:auto;" align="left">
	                <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1" >
	                    <?
						$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
						$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
						$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
						$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
						$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');

						$knit_production_array=array();
						$knit_production_sql="SELECT b.order_id, sum(b.product_qnty) AS kniting
						from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.order_id = '$expData[0]' and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
						// echo $knit_production_sql;
						$knit_production_sql_result=sql_select($knit_production_sql);
						foreach ($knit_production_sql_result as $row)
						{
							$knit_production_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
						}	
						// var_dump ($knit_production_array);
					if($color_id!="") $color_cond="and b.color_id=$color_id";
				else  $color_cond="";
	                    $i=0;
	                    $sql= "SELECT a.id,a.company_id,a.delivery_no,a.location_id,a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity, sum(b.gray_qty) as gray_qty from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.delivery_date between '$expData[2]' and '$expData[3]' and a.status_active=1 and a.is_deleted=0 $color_cond group by a.id,a.company_id,a.delivery_no,a.location_id,a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
	                    /*$sql="SELECT a.prefix_no_num as sys_id, a.product_no, a.party_id, a.product_date as production_date, b.order_id, b.process, b.cons_comp_id as item_id, b.color_id, sum(b.no_of_roll) as roll_qty
						from subcon_production_mst a, subcon_production_dtls b 
						where b.order_id='$expData[1]' and b.product_type=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, b.order_id, b.process, b.cons_comp_id, b.color_id order by b.color_id";*/
	                    // echo $sql;
						$production_sql= sql_select($sql); 
						$color_array=array(); 
						$k=1; 
						$process_id=0;
						foreach( $production_sql as $row )
	                    {
	                        $i++;
	                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							$process_id=$row[csf("process_id")];
							if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
							{
								$item_name=$garments_item[$row[csf('item_id')]];
							}
							else if ($row[csf("process_id")]==2)
							{
								$item_name=$kniting_item_arr[$row[csf('item_id')]];
							}
							else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
							{
								$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
							}						
							$process_loss = (($row[csf("gray_qty")] - $row[csf("quantity")]) / $row[csf("quantity")])*100;
							
							//print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_sys_id').val()+'*'+report_title+'*'+type+'*'+$('#cbo_location').val()+'*'+$('#cbo_template_id').val(), "subcon_delivery_entry_print5", "requires/subcon_dye_finishing_delivery_controller" )
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"> <a href="##" onclick="generate_report2('<? echo $row[csf("company_id")]; ?>','<? echo $row[csf("id")]; ?>','<? echo $row[csf("delivery_no")]; ?>','<? echo $row[csf("location_id")]; ?>','5')"><? echo $row[csf("delivery_prefix_num")];?></a></td>
                                
								<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td width="100" align="right" ><? echo $row[csf("quantity")];//echo number_format($knit_production_array[$row[csf('order_id')]]['kniting']); ?></td>
								<td width="80" align="right" ><? echo $row[csf("gray_qty")];//echo number_format($knit_production_array[$row[csf('order_id')]]['kniting']); ?></td>
								<td align="right" width="90"><? echo number_format($process_loss,2);?></td>
							</tr>
							<? 
							$tot_qty+=$row[csf("quantity")];
							$tot_gray_qty+=$row[csf("gray_qty")];
							$tot_process_loss += $process_loss;
						} 
					
						?>
	                    <tr class="tbl_bottom">
	                    	<td colspan="7" align="right">Total: </td>
	                        <td align="right"><p><? echo number_format($tot_qty); ?></p></td>
	                        <td align="right"><p><? echo number_format($tot_gray_qty); ?></p></td>
	                        <td align="right"><p><? echo number_format($tot_process_loss,2); ?></p></td>
	                    </tr>
	                </table>
	            </div> 
			</fieldset>
		</div>
		<?
	}
	
	
	exit();
}

if($action=="delivery_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	if($expData[2] == "")
	{
		?>
		<div id="data_panel" align="center" style="width:100%">
			<script>
				function new_window()
				{
					// $(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
					// $(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
		<div id="details_reports" style="width: 940px;">
	        <fieldset style="width:920px">
	            <div style="width:100%;" align="left">
	                <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
	                	<caption><div  style="text-align: center;font-weight: bold;">Work Progress Report Details </div></caption>
	                    <thead>
	                    	<tr>
	                            <th width="30">SL</th>
	                            <th width="60">Delivery ID </th>
	                            <th width="70">Delivery Date</th>
	                            <th width="80">Batch No</th>
	                            <th width="80">Order No</th>
	                            <th width="80">Category</th>
	                            <th width="150">Description</th>
	                            <th width="100">Delivery Qty</th>
	                            <th title="((Grey qty - Fin. qty) / Fin. qty) * 100" width="90">Process Loss</th>
	                            <th width="100">Gray Qty</th>
	                            
	                    	</tr>
	                    </thead>
	                </table>
	            </div>  
	            <div style="width:100%; max-height:500px; overflow-y:auto;" align="left">
	                <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1" >
	                    <?
						$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
						$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
						$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
						$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
						$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
	                    $i=0;
	                    $sql= "SELECT a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity, sum(b.gray_qty) as gray_qty from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
	                    // echo $sql;
						$production_sql= sql_select($sql); $color_array=array(); $k=1; $process_id=0;
						foreach( $production_sql as $row )
	                    {
	                        $i++;
	                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							$process_id=$row[csf("process_id")];
							if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
							{
								$item_name=$garments_item[$row[csf('item_id')]];
							}
							else if ($row[csf("process_id")]==2)
							{
								$item_name=$kniting_item_arr[$row[csf('item_id')]];
							}
							else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
							{
								$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
							}
							
							if ($row[csf("process_id")]==2)
							{
								if (!in_array($row[csf("color_id")],$color_array) )
								{
									if($k!=1)
									{
									?>
										<tr class="tbl_bottom">
											<td colspan="7" align="right"><b>Color Total:</b></td>
											<td width="100" align="right"><? echo number_format($color_qty); ?></td>
											<td align="right"><? echo number_format($color_process_loss,2); ?></td>
										</tr>
										<tr bgcolor="#dddddd">
											<td colspan="9" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
										</tr>
									<?
										unset($color_qty);
										unset($color_process_loss);
									}
									else
									{
										?>
										<tr bgcolor="#dddddd">
											<td colspan="9" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
										</tr>
										<?
									}					
									$color_array[]=$row[csf('color_id')];            
									$k++;
								}

								$process_loss = (($row[csf("gray_qty")] - $row[csf("quantity")]) / $row[csf("quantity")])*100;							
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
									<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
									<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
									<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
									<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
									<td align="center" width="150"><p><? echo $item_name; ?></p></td>
									<td width="100" align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
									<td align="right" width="90"><? echo number_format($process_loss,2); ?></td>
									<td width="100" align="right"><? echo number_format($row[csf("gray_qty")],2); ?></td>

								</tr>
								<? 
								$color_qty+=$row[csf("quantity")];
								$tot_qty+=$row[csf("quantity")];
								$color_process_loss += $process_loss;
								$tot_process_loss += $process_loss;
							}
							else
							{
								$process_loss = (($row[csf("gray_qty")] - $row[csf("quantity")]) / $row[csf("quantity")])*100;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
									<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
									<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
									<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
									<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
									<td align="center" width="150"><p><? echo $item_name; ?></p></td>
									<td width="100" align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
									<td align="right" width="90"><? echo number_format($process_loss,2); ?></td>
									<td width="100" align="right"><? echo number_format($row[csf("gray_qty")],2); ?></td>
								</tr>
								<? 
								$tot_qty+=$row[csf("quantity")];
								$tot_gray_qty+=$row[csf("gray_qty")];

								$tot_process_loss += $process_loss;
							}
						} 
						if($process_id==2)
						{
						?>
	                        <tr class="tbl_bottom">
	                            <td colspan="7" align="right"><b>Color Total:</b></td>
	                            <td align="right"><? echo number_format($color_qty); ?></td>
	                            <td align="right"><? echo number_format($color_process_loss); ?></td>
	                            <td align="right"></td>

	                        </tr>
	                    <?
						}
						?>
	                    <tr class="tbl_bottom">
	                    	<td colspan="7" align="right">Total: </td>
	                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
	                        <td align="right"><p><? echo number_format($tot_process_loss,2); ?></p></td>
	                        <td align="right"><p><? echo number_format($tot_gray_qty,2); ?></p></td>

	                    </tr>
	                </table>
	            </div> 
			</fieldset>
		</div>
		<?
	}
	else
	{
		?>
		<div id="data_panel" align="center" style="width:100%">
			<script>
				function new_window()
				{
					// $(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
					// $(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
		<div id="details_reports" style="width: 840px;">
	        <fieldset style="width:820px">
	        	<caption><div  style="text-align: center;font-weight: bold;">Work Progress Report Details </div></caption>
	            <div style="width:100%;" align="left">
	                <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
	                    <thead>
	                    	<tr>
	                            <th width="30">SL</th>
	                            <th width="60">Delivery ID</th>
	                            <th width="70">Delivery Date</th>
	                            <th width="80">Batch No</th>
	                            <th width="80">Order No</th>
	                            <th width="80">Category</th>
	                            <th width="150">Description</th>
	                            <th width="100">Delivery Qty</th>
	                            <th width="80">Grey Used Qty</th>
	                            <th title="((Grey qty - Fin. qty) / Fin. qty) * 100" width="90">Process Loss</th>
	                    	</tr>
	                    </thead>
	                </table>
	            </div>  
	            <div style="width:100%; max-height:230px; overflow-y:auto;" align="left">
	                <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1" >
	                    <?
						$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
						$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
						$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
						$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
						$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');

						$knit_production_array=array();
						$knit_production_sql="SELECT b.order_id, sum(b.product_qnty) AS kniting
						from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.order_id = '$expData[0]' and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
						// echo $knit_production_sql;
						$knit_production_sql_result=sql_select($knit_production_sql);
						foreach ($knit_production_sql_result as $row)
						{
							$knit_production_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
						}	
						// var_dump ($knit_production_array);

	                    $i=0;
	                    $sql= "SELECT a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity, sum(b.gray_qty) as gray_qty from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.delivery_date between '$expData[2]' and '$expData[3]' and a.status_active=1 and a.is_deleted=0 group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
	                    /*$sql="SELECT a.prefix_no_num as sys_id, a.product_no, a.party_id, a.product_date as production_date, b.order_id, b.process, b.cons_comp_id as item_id, b.color_id, sum(b.no_of_roll) as roll_qty
						from subcon_production_mst a, subcon_production_dtls b 
						where b.order_id='$expData[1]' and b.product_type=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, b.order_id, b.process, b.cons_comp_id, b.color_id order by b.color_id";*/
	                    // echo $sql;
						$production_sql= sql_select($sql); 
						$color_array=array(); 
						$k=1; 
						$process_id=0;
						foreach( $production_sql as $row )
	                    {
	                        $i++;
	                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							$process_id=$row[csf("process_id")];
							if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
							{
								$item_name=$garments_item[$row[csf('item_id')]];
							}
							else if ($row[csf("process_id")]==2)
							{
								$item_name=$kniting_item_arr[$row[csf('item_id')]];
							}
							else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
							{
								$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
							}						
							$process_loss = (($row[csf("gray_qty")] - $row[csf("quantity")]) / $row[csf("quantity")])*100;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td width="100" align="right" ><? echo $row[csf("quantity")];//echo number_format($knit_production_array[$row[csf('order_id')]]['kniting']); ?></td>
								<td width="80" align="right" ><? echo $row[csf("gray_qty")];//echo number_format($knit_production_array[$row[csf('order_id')]]['kniting']); ?></td>
								<td align="right" width="90"><? echo number_format($process_loss,2);?></td>
							</tr>
							<? 
							$tot_qty+=$row[csf("quantity")];
							$tot_gray_qty+=$row[csf("gray_qty")];
							$tot_process_loss += $process_loss;
						} 
					
						?>
	                    <tr class="tbl_bottom">
	                    	<td colspan="7" align="right">Total: </td>
	                        <td align="right"><p><? echo number_format($tot_qty); ?></p></td>
	                        <td align="right"><p><? echo number_format($tot_gray_qty); ?></p></td>
	                        <td align="right"><p><? echo number_format($tot_process_loss,2); ?></p></td>
	                    </tr>
	                </table>
	            </div> 
			</fieldset>
		</div>
		<?
	}
	
	
	exit();
}

if($action=="delivery_qty_fin_pop_up")
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
                            <th width="5%">SL</th>
                            <th width="10%">Delivery ID</th>
                            <th width="10%">Delivery Date</th>
                            <th width="10%">Batch No</th>
                            <th width="15%">Order No</th>
                            <th width="15%">Category</th>
                            <th width="15%">Description</th>
                            <th width="10%">Delivery Qty</th>
                            <th width="10%">Process Loss</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:230px; overflow-y:auto;" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
					$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
                    $i=0;
                    $sql= "SELECT a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity 
                    from  subcon_delivery_mst a, subcon_delivery_dtls b 
                    where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 
                    group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id 
                    order by b.color_id,a.delivery_prefix_num, a.delivery_date";
                    //echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1; $process_id=0;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$process_id=$row[csf("process_id")];
						if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==2)
						{
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
						{
							$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
						}
						
						if ($row[csf("process_id")]==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td width="80%" colspan="7" align="right"><b>Color Total:</b></td>
										<td width="10%" align="right"><? echo number_format($color_qty); ?></td>
										<td width="10%" align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td width="100%" colspan="9" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="9" width="100%" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="5%"><? echo $i; ?></td>
								<td width="10%"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="10%"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="10%"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="15%"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="15%"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="15%"><p><? echo $item_name; ?></p></td>
								<td align="right" width="10%"><? echo number_format($row[csf("quantity")],2); ?></td>
								<td align="right" width="10%"><? //echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("quantity")];
							// $tot_qty+=$row[csf("quantity")];
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="5%"><? echo $i; ?></td>
								<td width="10%"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="10%"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="10%"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="15%"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="15%"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="15%"><p><? echo $item_name; ?></p></td>
								<td align="right" width="10%"><? echo number_format($row[csf("quantity")],2); ?></td>
								<td align="right" width="10%"><? //echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$tot_qty+=$row[csf("quantity")];
						}
					} 
					if($process_id==2)
					{
					?>
                        <tr class="tbl_bottom">
                            <td width="80%" colspan="7" align="right"><b>Color Total:</b></td>
                            <td width="10%" align="right"><? echo number_format($color_qty); ?></td>
                            <td width="10%" align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
                    <?
					}
					?>
                    <tr class="tbl_bottom">
                    	<td width="80%" colspan="7" align="right">Total: </td>
                        <td width="10%" align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                        <td width="10%" align="right"><p><? //echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="fabric_finishing_print") 
{
	
	
    extract($_REQUEST);
	$data=explode('*',$data);
	
	
//echo $data[2]; die;

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$color_library=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0", "id","color_name");
	$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
	$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
	$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
	$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
	$prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	
	
	$sql_mst="Select a.id, a.bill_no, a.bill_date, a.location_id, a.party_id, a.party_source, a.party_location_id, a.bill_for, a.terms_and_condition from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and b.order_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$location_id=$dataArray[0][csf('location_id')];
	$sql_com_loc="select b.id,b.location_name,b.address from lib_location b,lib_company a where a.id=b.company_id and b.company_id='$data[0]' and b.id='$location_id' and a.status_active=1";
	$dataArray_loc=sql_select($sql_com_loc);
	foreach($dataArray_loc as $row)
	{
		$loc_address=$row[csf('address')];
	}

	?>
    <div style="width:1130px;" align="center">
	<? if($data[3]==1)
	{
	?>
	<style>
		@media print {
			table tr th,table tr td{ font-size: 20px !important; }
		}
	</style>
	<? } ?>
    <table width="900" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td width="100" align="right"> 
            	<img src='../../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='100' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px"><? echo $loc_address;//show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong>Dyeing And Finishing Bill Issue</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table> 
    <table width="930" cellspacing="0" align="center" border="0">   
    	  <tr><td colspan="6" align="center"><hr></hr></td></tr>
             <tr>
			 <?
			 	if($dataArray[0][csf('party_source')]==2)
				{
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id='$party_add'"); 
					foreach ($nameArray as $result)
					{ 
                    	$address="";
						if($result!="") $address=$result[csf('address_1')];
					}
					$party_details=$party_library[$party_add].'<br>'.$address;
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$party_details=$company_library[$dataArray[0][csf('party_id')]];
				}
			 ?>
                <td width="300" rowspan="4" valign="top" colspan="2"><strong>Party :<? echo $party_details; ?></strong></td>
                <td width="130"><strong>Party Location: </strong></td><td width="175px"> <? echo $location_arr[$dataArray[0][csf('party_location_id')]]; ?></td>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><strong><? echo $dataArray[0][csf('bill_no')]; ?></strong></td>
            </tr>
             <tr>
             	<td><strong>Bill Date: </strong></td><td> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td><strong>Source :</strong></td> <td><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Bill For :</strong></td> <td><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
                <td>&nbsp;</td><td>&nbsp;</td>
                <td>&nbsp;</td><td>&nbsp;</td>
            </tr>
        </table>
        <br>
        <?
		$process_ids_arr=array();
		$sql_rate="select id, process_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_type_id=1 order by id Desc";
		$sql_rate_res=sql_select($sql_rate);
		foreach($sql_rate_res as $rrow)
		{
			$process_ids_arr[$rrow[csf('id')]]=$rrow[csf('process_id')];
		}
		unset($sql_rate_res);
		
		$batch_array=array(); $order_array=array();
		$grey_color_array=array();
		$grey_sql="Select a.color_id, b.fabric_from, b.po_id, b.id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
		$grey_sql_result =sql_select($grey_sql);
		foreach($grey_sql_result as $row)
		{
			//$batch_array[$row[csf('id')]]=$row[csf('fabric_from')];
			$batch_array[$row[csf('id')]]['color']=$row[csf('color_id')];
			$batch_array[$row[csf('id')]]['item_description']=$row[csf('item_description')];
		}	
		
		if($dataArray[0][csf('party_source')]==2)
		{
			$order_sql="select id, job_no_mst, order_no, order_uom, cust_buyer, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
				$order_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
			}
		}
		else if($dataArray[0][csf('party_source')]==1)
		{
			$order_sql="select a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$order_array[$row[csf('id')]]['order_no']=$row[csf('po_number')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$party_library[$row[csf('buyer_name')]];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('style_ref_no')];
			}
			$recChallan_arr=array();
			
			$rec_challa_sql="SELECT a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id
							FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
							WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37) and c.trans_id!=0 and a.entry_form in (7,37) AND a.knitting_source=1 AND a.company_id='".$dataArray[0][csf('party_id')]."' AND a.location_id='".$dataArray[0][csf('location_id')]."' AND a.knitting_company=$data[0] and a.receive_basis in(4,5,9) and b.trans_id!=0  and a.item_category=2 
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
							group by a.id, a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id order by a.recv_number_prefix_num DESC";
			$rec_challa_sql_res=sql_select($rec_challa_sql);
			foreach($rec_challa_sql_res as $row)
			{
				$recChallan_arr[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]]=$row[csf('challan_no')];
			}
		}
		//var_dump($recChallan_arr);
		 $rate_data_string=$lib_rate_id=""; $mainProcess_arr=array(); $fabric_color_arr=array();
		 if($data[3]==1)
		 {
			?>
			<div style="width:100%;">
			<table align="center" cellspacing="0" width="1470"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center" style="font-size:14px"> 
					<th width="20">SL</th>
					<th width="75">Challan & <br> Delv. Date</th>
					<th width="50">Rec. Challan</th>
					<th width="90">Batch No</th>
					<th width="90">Job No</th>
					<th width="100">Order</th> 
					<th width="100">Buyer  & <br> Style</th>
					<th width="150">Fabric Des.</th>
					<th width="80">D.W Type</th>
					<th width="60">Color</th>
					<th width="100">Color Range</th>
					<th width="100">A.Process</th>
					<th width="30">Roll</th>
					<th width="60">Bill Qty</th>
					<th width="30">UOM</th>
					<th width="30">Rate (Main)</th>
					<th width="30">Rate (Add)</th>
					<th width="60">Amount</th>
					<th width="50">Currency</th>
					<th>Remarks</th>
				</thead>
			<?
				$i=1;
				$mst_id=$dataArray[0][csf('id')];
				$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, batch_id, body_part_id, febric_description_id, dia_width_type, color_id, color_range_id, packing_qnty, delivery_qty, rate, add_rate, amount, remarks, currency_id, process_id, add_process, add_process_name, rate_data_string, lib_rate_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by challan_no"); 
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$rate_data_string.=$row[csf("rate_data_string")].',';
					//$lib_rate_id.=$row[csf("lib_rate_id")].',';
					
					$process=explode(',',$row[csf("add_process")]);
					$add_process="";
					foreach($process as $inf)
					{
						if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
					}
					
					if($dataArray[0][csf('party_source')]==2)
					{
						$item_all= explode(',',$batch_array[$row[csf('item_id')]]['item_description']);
					}
					else if($dataArray[0][csf('party_source')]==1)
					{
						$item_all= explode(',',$row[csf('item_id')]);
					}
					$item_name="";
					foreach($item_all as $inf)
					{
						if($dataArray[0][csf('party_source')]==2)
						{
							if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
						}
						else if($dataArray[0][csf('party_source')]==1)
						{
							if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
						}
					}
					$exrate_data_string=explode("#",$row[csf("rate_data_string")]);
					foreach($exrate_data_string as $process_data)
					{
						$exrate_data=explode("__",$process_data);
						$lib_id=$exrate_data[0];
						$librate=$exrate_data[1];
						$fabric_color_arr[$item_name][$color_library[$row[csf('color_id')]]][$process_ids_arr[$lib_id]]+=$librate;
						$mainProcess_arr[$process_ids_arr[$lib_id]]=$process_ids_arr[$lib_id];
					}
					
					$rec_challan="";
					$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px"> 
						<td><? echo $i; ?></td>
						<td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]); ?></td>
						<td style="word-break:break-all"><? echo $rec_challan; ?></td>
						<td style="word-break:break-all"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
						<td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['job_no']; ?></td>
						<td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
						<td style="word-break:break-all"><? echo $item_name; ?></td>
						<td style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
						<td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
						<td style="word-break:break-all"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
						<td style="word-break:break-all"><? echo $row[csf('add_process_name')];//$add_process; ?></td>
						<td align="right"><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</td>
						<td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',','); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</td>
						<td><? echo $unit_of_measurement[12]; ?></td>
						<td align="right"><? echo number_format($row[csf('rate')],2,'.',','); ?>&nbsp;</td>
						<td align="right"><? echo number_format($row[csf('add_rate')],2,'.',','); ?>&nbsp;</td>
						<td align="right"><? echo number_format($row[csf('amount')],2,'.',',');  $total_amount += $row[csf('amount')]; ?>&nbsp;</td>

						<td style="word-break:break-all"><? echo $currency[$row[csf('currency_id')]]; ?></td>

						<td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
						<? 
						$carrency_id=$row[csf('currency_id')];
						if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
						?>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr style="font-size:14px"> 
					<td align="right" colspan="12"><strong>Total</strong></td>
					<td align="right"><strong><? echo $tot_packing_qty; ?>&nbsp;</strong></td>
					<td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><strong><? echo $format_total_amount=number_format($total_amount,2,'.',','); ?>&nbsp;</strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			<tr>
				<td colspan="20" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
			</tr>
			</table>
			<?
		 }
		 elseif($data[3]==2)
		 {
			 ?>
            <div style="width:100%;">
             <style type="text/css" media="all">
		    	.rpt_table tr th,.rpt_table tr td{ font-size: 20px !important; }
		    </style>
            <table align="center" cellspacing="0" width="1380"  border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="105" align="center">Sys. Challan & <br> Delv. Date</th>
                    <th width="50">Rec. Challan</th>
                    <th width="120" align="center">Job No</th>
                    <th width="130" align="center">Order</th> 
                    <th width="100" align="center">Buyer & <br> Style</th>
                    <th width="80" align="center">Color</th>
                    <th width="100" align="center">Color Range</th>
                    <th width="130" align="center">A.Process</th>
                    <th width="30" align="center">Roll</th>
                    <th width="70" align="center">Bill Qty</th>
                    <th width="50" align="center">Rate (Main)</th>
                    <th width="30" align="center">Rate (Add)</th>
                    <th width="70" align="center">Amount</th>
                    <th width="50" align="center">Currency</th>
                    <th>Remarks</th>
                </thead>
             <?
                $i=1;
                $mst_id=$dataArray[0][csf('id')];
                $sql_dtls ="select delivery_date, challan_no, order_id, color_id, color_range_id, currency_id, sum(packing_qnty) as packing_qnty, sum(delivery_qty) as delivery_qty, rate, add_rate, sum(amount) as amount, add_process, add_process_name, max(remarks) as remarks, rate_data_string, lib_rate_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 group by delivery_date, challan_no, order_id, color_id, color_range_id, currency_id, rate, add_rate, add_process, add_process_name, rate_data_string, lib_rate_id order by delivery_date, challan_no, order_id, color_id, rate, add_process"; 
                //echo $sql_dtls; die;

                $sql_result =sql_select($sql_dtls);

                foreach($sql_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$process=explode(',',$row[csf("add_process")]);
					$add_process="";
					foreach($process as $inf)
					{
						if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
					}
					$rec_challan="";
					$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
					
                   ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"> 
                        <td><? echo $i; ?></td>
                        <td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]); ?></td>
                        <td style="word-break:break-all"><? echo $rec_challan; ?></td>
                        <td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['job_no']; ?></td>
                        <td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
                        <td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
                        <td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                        <td style="word-break:break-all"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
                        <td style="word-break:break-all"><? echo $row[csf('add_process_name')]; ?></td>
                        <td align="right"><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',','); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('rate')],2,'.',','); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('add_rate')],2,'.',','); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('amount')],2,'.',',');  $total_amount += $row[csf('amount')]; ?>&nbsp;</td>
                        <td align="center" style="word-break:break-all"><? echo $currency[$row[csf('currency_id')]]; ?></td>
                        <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
                        <? 
                        $carrency_id=$row['currency_id'];
                        if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
                       ?>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <tr> 
                    <td align="right" colspan="9"><strong>Total</strong></td>
                    <td align="right"><strong><? echo $tot_packing_qty; ?>&nbsp;</strong></td>
                    <td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><strong><? echo $format_total_amount=number_format($total_amount,2,'.',','); ?>&nbsp;</strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
               <tr>
                   <td colspan="16" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
               </tr>
            </table>
        <?			 
		 }
		 elseif($data[3]==3)
		 {
		 ?>
	<div style="width:100%;" align="center">
		<table align="center" cellspacing="0" width="1180"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:14px"> 
                <th width="30">SL</th>
                <th width="60">Challan & <br> Delv. Date</th>
                <th width="50">Rec. Challan</th>
                <th width="75">Job No</th> 
                <th width="80">Order</th> 
                <th width="70">Buyer  & <br> Style</th>
                <th width="180">Fabric Des.</th>
                <th width="60">D.W Type</th>
                <th width="60">Color</th>
                <th width="100">Color Range</th>
                <th width="100">A.Process</th>
                <th width="30">Roll</th>
                <th width="60">Bill Qty</th>
                <th width="30">UOM</th>
                <th width="30">Rate (Main)</th>
                <th width="30">Rate (Add)</th>
                <th width="60">Amount</th>
                <th width="50">Currency</th>
                <th>Remarks</th>
            </thead>
		 <?
     		$i=1;
			
			$sql_currency_result_usd=sql_select("SELECT conversion_rate from currency_conversion_rate WHERE con_date = (SELECT MAX(con_date) from currency_conversion_rate WHERE is_deleted=0 and status_active=1 and currency=2)");
			
			$mst_id=$dataArray[0][csf('id')];
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, batch_id, body_part_id, febric_description_id, dia_width_type, color_id, color_range_id, packing_qnty, delivery_qty, rate, add_rate, amount, remarks, currency_id, process_id, add_process, add_process_name, rate_data_string, lib_rate_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by challan_no"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$process=explode(',',$row[csf("add_process")]);
				$add_process="";
				foreach($process as $inf)
				{
					if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
				}
                
				if($dataArray[0][csf('party_source')]==2)
				{
					$item_all= explode(',',$batch_array[$row[csf('item_id')]]['item_description']);
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$item_all= explode(',',$row[csf('item_id')]);
				}
				$item_name="";
				foreach($item_all as $inf)
				{
					if($dataArray[0][csf('party_source')]==2)
					{
						if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
					}
					else if($dataArray[0][csf('party_source')]==1)
					{
						if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
					}
				}
				$rec_challan="";
				$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px"> 
                    <td><? echo $i; ?></td>
                    <td align="center" style="word-break:break-all"><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]); ?></td>
                    <td style="word-break:break-all"><? echo $rec_challan; ?></td>
                    <td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['job_no']; ?></td>
                    <td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
                    <td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
                    <td style="word-break:break-all"><? echo $item_name; ?></td>
                    <td style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
                    <td style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                    <td style="word-break:break-all"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('add_process_name')];//$add_process; ?></td>
                    <td align="right"><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',','); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</td>
                    <td style="word-break:break-all"><? echo $unit_of_measurement[12]; ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],2,'.',','); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('add_rate')],2,'.',','); ?>&nbsp;</td>
                    <td align="right" style="word-break:break-all"><? echo number_format($row[csf('amount')]/$sql_currency_result_usd[0][csf('conversion_rate')],2,'.',',');  $total_amount += $row[csf('amount')]/$sql_currency_result_usd[0][csf('conversion_rate')]; ?>&nbsp;</td>

                    <td align="center" style="word-break:break-all"><? echo $currency[2]; ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
                    <? 
					$carrency_id=$row[csf('currency_id')];
					if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
				    ?>
                </tr>
                <?php
                $i++;
			}
			?>
        	<tr style="font-size:14px"> 
                <td align="right" colspan="11"><strong>Total</strong></td>
                <td align="right"><strong><? echo $tot_packing_qty; ?>&nbsp;</strong></td>
                <td align="right"><strong><? echo number_format($tot_delivery_qty,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo $format_total_amount=number_format($total_amount,2,'.',','); ?>&nbsp;</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="19" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[2],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
		 }
		 

			if($data[3]==1)
			{
				$process_ids_count=count($mainProcess_arr);
				
				$tblWidth=$process_ids_count*70;
				if($process_ids_count>0)
				{
					?>
					<br>
					<table width="<? echo $tblWidth+350; ?>" cellspacing="0" border="1" class="rpt_table" rules="all">   
						<thead>
							<th width="150">Fabric Description</th>
							<th width="100">Color</th>
							<?
								foreach($mainProcess_arr as $process_ids)
								{
									?>
										<th width="70"><? echo $conversion_cost_head_array[$process_ids]; ?></th>
									<?
								}
							?>
							<th>Total (TK)</th>
						 </thead>
						 <tbody>
							<? 
							foreach($fabric_color_arr as $cons_comp=>$consData)
							{
								foreach($consData as $colorName=>$processData)
								{
									?>
									<tr>
										<td style="word-break:break-all"><? echo $cons_comp; ?></td>
										<td style="word-break:break-all"><? echo $colorName; ?></td>
										<?
											$rowTotalTk=0;
											foreach($mainProcess_arr as $process_ids)
											{
												?>
													<td align="right"><? if($processData[$process_ids]!=0) echo number_format($processData[$process_ids],3); else echo ""; ?></td>
												<?
												$rowTotalTk+=$processData[$process_ids];
											}
										?>
										<td align="right"><? echo number_format($rowTotalTk,3); ?></td>
									</tr>
							<? } } ?>
						 </tbody>
					</table>
        <? } }?>
        <table width="930" align="center" > 
        	<tr><td colspan="2">&nbsp;</td> </tr>
            <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td> </tr>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=2 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);
			$i=1;
			if(count($result_sql_terms)>0)
			{
				foreach($result_sql_terms as $rows)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td width="30"><? echo $i; ?></td>
						<td style="word-break:break-all"><? echo $rows[csf('terms')]; ?></td>
					</tr>
				<?
				$i++;
				}
			}
			?>
        </table>
        <br>
		 <?
            echo signature_table(48, $data[0], "930px");
         ?>
   </div>
   </div>
	<?
    exit();
}


?>