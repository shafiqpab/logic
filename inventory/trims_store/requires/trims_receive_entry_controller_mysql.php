<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$permission=$_SESSION['page_permission'];
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

 //-------------------START ----------------------------------------
$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");

$trim_group_arr =array(); 
$data_array=sql_select("select id, item_name, order_uom from lib_item_group");
foreach($data_array as $row)
{
	$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
	$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
}

if($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 152,"select id, supplier_name from lib_supplier where find_in_set($data,tag_company) and (find_in_set(5,party_type) or find_in_set(4,party_type)) and status_active=1 and is_deleted=0 order by supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,0,1);

	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 152, "select id, store_name from lib_store_location where company_id='$data' and find_in_set(4,item_category_id) and status_active=1 and is_deleted=0 order by store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}


if ($action=="wo_pi_popup")
{
	echo load_html_head_contents("WO/PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(id,no,type,data)
		{
			$('#hidden_wo_pi_id').val(id);
			$('#hidden_wo_pi_no').val(no);
			$('#booking_without_order').val(type);
			$('#hidden_data').val(data);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:830px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:930px; margin-left:3px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="720" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th width="240">Enter WO/PI No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_wo_pi_id" id="hidden_wo_pi_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_wo_pi_no" id="hidden_wo_pi_no" class="text_boxes" value=""> 
                        <input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
                        <input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value=""> 
                    </th> 
                </thead>
                <tr>
                    <td align="center">	
                    	<?
							echo create_drop_down("cbo_receive_basis",152,$receive_basis_arr,"",1,"-- Select --",$recieve_basis,"","1","1,2,4,6");
						?>
                    </td>                 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value, 'create_wo_pi_search_list_view', 'search_div', 'trims_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
           </table>
            <div style="margin-top:10px;" id="search_div" align="left"></div> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_wo_pi_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$recieve_basis=$data[1];
	$company_id =$data[2];
	
	if($recieve_basis==1)
	{
		if(trim($data[0])!="")
		{
			$search_field_cond="and pi_number like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}
		
		$sql = "select id, pi_number, supplier_id, pi_date, last_shipment_date, pi_basis_id, internal_file_no, currency_id, source from com_pi_master_details where item_category_id='4' and status_active=1 and is_deleted=0 and importer_id=$company_id $search_field_cond"; 
		//echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="125">PI No</th>
				<th width="80">PI Date</th>
                <th width="110">PI Basis</th>               
				<th width="160">Supplier</th>
				<th width="100">Last Shipment Date</th>
				<th width="100">Internal File No</th>
				<th width="80">Currency</th>
				<th>Source</th>
			</thead>
		</table>
		<div style="width:928px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{  
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					
					$lc_data=sql_select("select b.id, b.lc_number from com_btb_lc_pi a, com_btb_lc_master_details b where a.pi_id=".$row[csf('id')]." and a.status_active=1 and a.is_deleted=0 and a.com_btb_lc_master_details_id=b.id");
					
					$lc_no=$lc_data[0][csf('lc_number')];
					$lc_id=$lc_data[0][csf('id')];
						
					$data=$row[csf('supplier_id')]."**".$row[csf('currency_id')]."**".$row[csf('source')]."**".$lc_no."**".$lc_id; 
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('pi_number')]; ?>','0','<? echo $data; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="125"><p><? echo $row[csf('pi_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>  
                        <td width="110"><? echo $pi_basis[$row[csf('pi_basis_id')]]; ?></td>             
						<td width="160"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
						<td width="100" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('internal_file_no')]; ?></p></td>
						<td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
						<td><p><? echo $source[$row[csf('source')]]; ?></p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
	<?	
	}
	else if($recieve_basis==2)
	{
		if(trim($data[0])!="")
			$search_field_cond="and a.booking_no like '$search_string'";
		else
			$search_field_cond="";
		
		$sql = "select a.id, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, group_concat(distinct(b.po_break_down_id)) as po_id,  group_concat(distinct(b.job_no)) as job_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond group by a.id"; 
		//echo $sql;die;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="115">Booking No</th>
				<th width="75">Booking Date</th>               
				<th width="100">Supplier</th>
				<th width="75">Delivary date</th>
                <th width="65">Source</th>
                <th width="65">Currency</th>
				<th width="90">Job No</th>
				<th width="80">Order Qnty</th>
				<th width="75">Shipment Date</th>
				<th>Order No</th>
			</thead>
		</table>
		<div style="width:928px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{  
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	 
					
					$po_qnty_in_pcs=''; $po_no=''; $min_shipment_date='';
					
					if($row[csf('po_id')]!="")
					{
						$po_sql="select b.po_number, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($row[po_id]) group by b.id";									
						$nameArray=sql_select($po_sql);
						foreach ($nameArray as $po_row)
						{
							if($po_no=="") $po_no=$po_row[csf('po_number')]; else $po_no.=",".$po_row[csf('po_number')];
							
							if($min_shipment_date=='')
							{
								$min_shipment_date=$po_row[csf('pub_shipment_date')];
							}
							else
							{
								if($po_row[csf('pub_shipment_date')]<$min_shipment_date) $min_shipment_date=$po_row[csf('pub_shipment_date')]; else $min_shipment_date=$min_shipment_date;
							}
							
							$po_qnty_in_pcs+=$po_row[csf('po_qnty_in_pcs')];
						}
					}
					
					$data=$row[csf('supplier_id')]."**".$row[csf('currency_id')]."**".$row[csf('source')]; 
					
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','0','<? echo $data; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>               
						<td width="100"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
                        <td width="65"><p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p></td>
                        <td width="65"><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
						<td width="80" align="right"><? echo $po_qnty_in_pcs; ?></td>
						<td width="75" align="center"><? echo change_date_format($min_shipment_date); ?></td>
						<td><p><? echo $po_no; ?></p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
	<?	
	}
exit();
}

if($action=='show_fabric_desc_listview')
{
	$data=explode("**",$data);
	$bookingNo_piId=$data[0];
	$receive_basis=$data[1];
	
	if($receive_basis==1)
	{
		$sql="select item_group as trim_group, item_description as description, brand_supplier, color_id, item_color, size_id, item_size, '' as sensitivity, rate from com_pi_item_details where pi_id='$bookingNo_piId' and status_active=1 and is_deleted=0";// group by item_group, item_description, brand_supplier, color_id, item_color, size_id, item_size
	}
	else if($receive_basis==2)
	{
		/*$wo_sensitivity=return_field_value("group_concat(distinct(sensitivity))","wo_booking_dtls","booking_no='$bookingNo_piId' and status_active=1 and is_deleted=0");
		$wo_sensitivity=explode(",",$wo_sensitivity);
		foreach($wo_sensitivity as $sensitivity)
		{
			if($sensitivity==1 || $sensitivity==3)
			{
				$sql1= "select b.sensitivity, b.trim_group, c.description, c.color_number_id as color_id, c.item_color, 0 as size_id, c.item_size, c.brand_supplier from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId' and b.sensitivity in(1,3) and b.status_active=1 and b.is_deleted=0 group by b.trim_group, c.description, c.brand_supplier, b.sensitivity, c.color_number_id, c.item_color, c.item_size"; 
			}
			else if($sensitivity==2)
			{
				$sql2 = "select b.sensitivity, b.trim_group, c.description, 0 as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId' and b.sensitivity=$sensitivity and b.status_active=1 and b.is_deleted=0 group by b.trim_group, c.description, c.brand_supplier, b.sensitivity, c.gmts_sizes, c.item_color, c.item_size";  
			}
			else if($sensitivity==4)
			{
				$sql3 = "select b.sensitivity, b.trim_group, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId' and b.sensitivity=$sensitivity and b.status_active=1 and b.is_deleted=0 group by b.trim_group, c.description, c.brand_supplier, b.sensitivity, c.color_number_id, c.gmts_sizes, c.item_color, c.item_size"; 
			}
			else if($sensitivity==0)
			{
				$sql4 = "select b.sensitivity, b.trim_group, c.description, 0 as color_id, c.item_color, 0 as size_id, c.item_size, c.brand_supplier from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId' and b.sensitivity=$sensitivity and b.status_active=1 and b.is_deleted=0 group by b.trim_group, c.description, c.brand_supplier, b.sensitivity, c.item_color, c.item_size";
			}
		}*/
		
		/*$union=" union all ";
		
		if($sql1!='')
		{
			if($sql=='') $sql=$sql1; else $sql.=$union.$sql1;
		}
		
		if($sql2!='')
		{
			if($sql=='') $sql=$sql2; else $sql.=$union.$sql2;
		}
		
		if($sql3!='')
		{
			if($sql=='') $sql=$sql3; else $sql.=$union.$sql3;
		}
		
		if($sql4!='')
		{
			if($sql=='') $sql=$sql4; else $sql.=$union.$sql4;
		}*/
		$sql = "select b.sensitivity, b.trim_group, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.rate from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId' and b.status_active=1 and b.is_deleted=0";
	}
	//echo $sql;
	$data_array=sql_select($sql);
	
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="470">
        <thead>
            <th width="25">SL</th>
            <th width="60">Item Group</th>
            <th>Item Description</th>
            <th width="60">Brand/Sup Ref</th>
            <th width="60">Item Color</th>
            <th width="60">Item Size</th>
            <th width="50">rate</th>
            <? if($receive_basis==2) echo "<th width='70'>Sensitivity</th>";  ?>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
				
				$data=$row[csf('trim_group')]."**".$row[csf('description')]."**".$row[csf('brand_supplier')]."**".$row[csf('sensitivity')]."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('size_id')]."**".$size_arr[$row[csf('size_id')]]."**".$row[csf('item_color')]."**".$color_arr[$row[csf('item_color')]]."**".$row[csf('item_size')];
				
				$desc=$row[csf('description')];
				
				if($row[csf('item_color')]!=0) $desc.=", ".$color_arr[$row[csf('item_color')]];
				if($row[csf('item_size')]!="" && $row[csf('item_size')]!=0) $desc.=", ".$row[csf('item_size')];
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>")' style="cursor:pointer" >
                    <td><? echo $i; ?></td>
                    <td><p><? echo $trim_group_arr[$row[csf('trim_group')]]['name']; ?></p></td>
                    <td><p><? echo $desc; ?></p></td>
                    <td><p><? echo $row[csf('brand_supplier')]; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('item_color')]]; ?></p></td>
                    <td><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                    <? if($receive_basis==2) echo "<td><p>".$size_color_sensitive[$row[csf('sensitivity')]]."</p></td>";  ?>
                </tr>
            <? 
            $i++; 
            } 
            ?>
        </tbody>
    </table>
<?
exit();
}

if ($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$data=explode("**",$data);
	$po_id=$data[0]; $type=$data[1];
	//echo $type;die;
	if($type==1) 
	{
		$item_group=$data[2]; 
		$item_description=$data[3]; 
		$brand_supref=$data[4]; 
		$order_uom=$data[5]; 
		$receive_basis=$data[6]; 
		$save_data=$data[7];
		$gmts_color_id=$data[8]; 
		$gmts_size_id=$data[9]; 
		$item_color_id=$data[10]; 
		$item_size=$data[11];
		$booking_pi_id=$data[12];
	}

?> 

	<script>
		var receive_basis=<? echo $receive_basis; ?>;
		
		function fn_show_check()
		{
			if(form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}	
					
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $all_po_id; ?>', 'create_po_search_list_view', 'search_div', 'trims_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}
		
		function calculate_total()
		{
			var tblRow = $("#tbl_list_search tbody tr").length;
			var total_receive=0;
			for(var i=1;i<=tblRow;i++)
			{
				var recv_qnty=$('#txtRecvQnty_'+i).val()*1;
				total_receive=total_receive*1+recv_qnty;
			}
			
			$('#total_recieve').html(total_receive);
		}
		
		var selected_id = new Array();
		
		 function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i],0 ) 
				}
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#po_id').val( id );
		}
		
		function show_trims_recv() 
		{ 
			var po_id=$('#po_id').val();
			show_list_view ( po_id+'**'+'1'+'**'+'<? echo $item_group; ?>'+'**'+'<? echo $item_description; ?>'+'**'+'<? echo $brand_supref; ?>'+'**'+'<? echo $order_uom; ?>'+'**'+'<? echo $receive_basis; ?>'+'**'+'<? echo $save_data; ?>'+'**'+'<? echo $gmts_color_id; ?>'+'**'+'<? echo $gmts_size_id; ?>'+'**'+'<? echo $item_color_id; ?>'+'**'+'<? echo $item_size; ?>'+'**'+'<? echo $booking_pi_id; ?>', 'po_popup', 'search_div', 'trims_receive_entry_controller', '');
		}
		
		function hidden_field_reset()
		{
			$('#po_id').val('');
			$('#save_string').val( '' );
			$('#tot_trims_qnty').val( '' );
			selected_id = new Array();
		}
		
		function fnc_close()
		{
			var save_string='';	 var tot_trims_qnty=''; var po_id_array = new Array(); var po_no='';
			
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
				var txtRecvQnty=$(this).find('input[name="txtRecvQnty[]"]').val();
				var txtTotRecvQnty=$(this).find('input[name="txtTotRecvQnty[]"]').val();

				tot_trims_qnty=tot_trims_qnty*1+txtRecvQnty*1;
				
				if(txtRecvQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtPoId+"_"+txtRecvQnty;
					}
					else
					{
						save_string+=","+txtPoId+"_"+txtRecvQnty;
					}
					
					if(jQuery.inArray( txtPoId, po_id_array) == -1 ) 
					{
						po_id_array.push(txtPoId);
						if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
					}
				}
			});
			
			$('#save_string').val( save_string );
			$('#tot_trims_qnty').val( tot_trims_qnty );
			$('#all_po_id').val( po_id_array );
			$('#all_po_no').val( po_no );
			
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
	<? 
	if($type!=1)
	{
	?>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:10px">
        	<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
            <input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes" value="">
            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
            <input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
	<?
	}
	
	if(($receive_basis==1 || $receive_basis==4 || $receive_basis==6) && $type!=1)
	{
	?>
		<table cellpadding="0" cellspacing="0" width="620" class="rpt_table">
			<thead>
				<th>Buyer</th>
				<th>Search By</th>
				<th>Search</th>
				<th>
					<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="po_id" id="po_id" value="">
				</th> 
			</thead>
			<tr class="general">
				<td align="center">
					<?
						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",$data[0] ); 
					?>       
				</td>
				<td align="center">	
					<?
						$search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Style Ref." );
						echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
					?>
				</td>                 
				<td align="center">				
					<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
				</td> 						
				<td align="center">
					<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check();" style="width:100px;" />
				</td>
			</tr>
		</table>
		<div id="search_div" style="margin-top:10px">
       		<?
			if($save_data!="")
			{
			?>
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580">
						<thead>
							<th>PO No</th>
                            <th width="130">Total Receive Qnty</th>
                            <th width="60">UOM</th>
                            <th width="115">Receive Qnty</th>
						</thead>
					</table>
					<div style="width:600px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580" id="tbl_list_search">  
							<tbody>
							<? 
							$i=1; $tot_trims_receive_qnty=0;

							$explSaveData = explode(",",$save_data); 	
							for($z=0;$z<count($explSaveData);$z++)
							{
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
									
								$po_wise_data = explode("_",$explSaveData[$z]);
								$order_id=$po_wise_data[0];
								$recv_qnty=$po_wise_data[1];
								
								if($receive_basis==1)
								{
									$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=1 and m.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description='$item_description' and a.brand_supplier='$brand_supref' and a.gmts_color_id='$gmts_color_id' and a.item_color='$item_color_id' and a.gmts_size_id ='$gmts_size_id' and a.item_size ='$item_size' and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
								}
								else
								{
									$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description='$item_description' and a.brand_supplier='$brand_supref' and a.gmts_color_id='$gmts_color_id' and a.item_color='$item_color_id' and a.gmts_size_id ='$gmts_size_id' and a.item_size ='$item_size' and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
								}

								$po_data=sql_select("select id, po_number from wo_po_break_down where id=$order_id");
								$tot_trims_receive_qnty+=$recv_qnty;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td>
                                        <p><? echo $po_data[0][csf('po_number')]; ?></p>
                                        <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $po_data[0][csf('id')]; ?>">
                                        <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_number')]; ?>">
                                    </td>
                                    <td width="130" align="right">
                                        <? echo number_format($tot_recv_qnty); ?>
                                        <input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
                                    </td>
                                    <td width="60" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
                                    <td align="right" width="115">
                                        <input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $recv_qnty; ?>" onKeyUp="calculate_total();">
                                    </td>
								</tr>
							<? 
							$i++;
							}
							?>
                            </tbody>
                            <tfoot class="tbl_bottom">
                                <td colspan="3">Total</td>
                                <td id="total_recieve"><? echo $tot_trims_receive_qnty; ?></td>
                            </tfoot>
						</table>
					</div>
					<table width="620">
						 <tr>
							<td align="center" >
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							</td>
						</tr>
					</table>
				</div>
			<?
			}
			?>
        </div>
	<?
	}
	else
	{
	?>
		<div style="margin-left:10px; margin-top:10px">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580">
				<thead>
					<th>PO No</th>
                    <? if($receive_basis==2) echo "<th width='100'>WO Qnty</th>"; ?>
                    <? if($receive_basis==4) echo "<th width='100'>RMG Qty</th>"; ?>
                    <th width="130">Total Receive Qnty</th>
                    <th width="60">UOM</th>
                    <th width="115">Receive Qnty</th>
				</thead>
			</table>
			<div style="width:600px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580" id="tbl_list_search">  
                	<tbody>
					<? 
					$i=1; $tot_trims_receive_qnty=0; $po_array=array();
					
					if($save_data!="" && ($receive_basis==1 || $receive_basis==4 || $receive_basis==6))
					{ 
						//$po_id = explode(",",$po_id);
						
						$explSaveData = explode(",",$save_data); $po_array=array();	
						for($z=0;$z<count($explSaveData);$z++)
						{
							$po_wise_data = explode("_",$explSaveData[$z]);

							$order_id=$po_wise_data[0];
							$recv_qnty=$po_wise_data[1];
							
							$po_array[$order_id]=$recv_qnty;
						}	
							
						$data_array=sql_select("select id, po_number from wo_po_break_down where id in ($po_id)");
						foreach($data_array as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$recv_qnty=$po_array[$row[csf('id')]];
							$tot_trims_receive_qnty+=$recv_qnty;
							
							if($receive_basis==1)
							{
								$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=1 and m.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description='$item_description' and a.brand_supplier='$brand_supref' and a.gmts_color_id='$gmts_color_id' and a.item_color='$item_color_id' and a.gmts_size_id ='$gmts_size_id' and a.item_size ='$item_size' and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$row[id]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
							}
							else
							{
								$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description='$item_description' and a.brand_supplier='$brand_supref' and a.gmts_color_id='$gmts_color_id' and a.item_color='$item_color_id' and a.gmts_size_id ='$gmts_size_id' and a.item_size ='$item_size' and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$row[id]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
							}
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td>
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
								</td>
								<td width="130" align="right">
									<? echo number_format($tot_recv_qnty); ?>
									<input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
								</td>
								<td width="60" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
								<td align="right" width="115">
									<input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $recv_qnty; ?>" onKeyUp="calculate_total();">
								</td>
							</tr>
							<? 
							$i++;
						}
					}
					else if($save_data!="" && $receive_basis==2)
					{
						$explSaveData = explode(",",$save_data); $order_data_array=array();	
						for($z=0;$z<count($explSaveData);$z++)
						{
							$po_wise_data = explode("_",$explSaveData[$z]);
							$order_data_array[$po_wise_data[0]]=$po_wise_data[1];
							
							/*if($sensitivity==1 || $sensitivity==3)
							{
								$po_sql="select a.id, a.po_number, sum(c.cons) as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.color_number_id='$gmts_color_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0 group by a.id";
							}
							else if($sensitivity==2)
							{
								$po_sql="select a.id, a.po_number, sum(c.cons) as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.gmts_sizes='$gmts_size_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0 group by a.id";
							}
							else if($sensitivity==4)
							{
								$po_sql="select a.id, a.po_number, sum(c.cons) as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.color_number_id='$gmts_color_id' and c.gmts_sizes='$gmts_size_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0 group by a.id";
							}
							else if($sensitivity==0)
							{
								$po_sql="select a.id, a.po_number, sum(c.cons) as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0 group by a.id";
							}*/
							$po_sql="select a.id, a.po_number, c.cons as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.color_number_id='$gmts_color_id' and c.gmts_sizes='$gmts_size_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0";
							//echo $po_sql;
							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{  
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
									
								$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and m.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description='$item_description' and a.brand_supplier='$brand_supref' and a.gmts_color_id='$gmts_color_id' and a.item_color='$item_color_id' and a.gmts_size_id='$gmts_size_id' and a.item_size ='$item_size' and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$row[id]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
								
								$recv_qnty=$order_data_array[$row[csf('id')]];
								$tot_trims_receive_qnty+=$recv_qnty;
							 ?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td>
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
									</td>
									<td width='100' align='right'><? echo number_format($row[csf('qty')],2); ?></td>
									<td width="130" align="right">
										<? echo number_format($tot_recv_qnty); ?>
										<input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
									</td>
									<td width="60" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
									<td align="right" width="115">
										<input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $recv_qnty; ?>" onKeyUp="calculate_total();">
									</td>
								</tr>
							<? 
							$i++; 
							} 
						}	
					}
					else
					{ 
						if($type==1)
						{
							if($po_id!="")
							{
								$po_sql="select id, po_number, po_quantity from wo_po_break_down where id in ($po_id)";
							}
						}
						else
						{
							/*if($sensitivity==1 || $sensitivity==3)
							{
								$po_sql="select a.id, a.po_number, sum(c.cons) as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.color_number_id='$gmts_color_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0 group by a.id";
							}
							else if($sensitivity==2)
							{
								$po_sql="select a.id, a.po_number, sum(c.cons) as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.gmts_sizes='$gmts_size_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0 group by a.id";
							}
							else if($sensitivity==4)
							{
								$po_sql="select a.id, a.po_number, sum(c.cons) as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.color_number_id='$gmts_color_id' and c.gmts_sizes='$gmts_size_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0 group by a.id";
							}
							else if($sensitivity==0)
							{
								$po_sql="select a.id, a.po_number, sum(c.cons) as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0 group by a.id";
							}*/
							$po_sql="select a.id, a.po_number, c.cons as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.color_number_id='$gmts_color_id' and c.gmts_sizes='$gmts_size_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0";
						}
						//echo $po_sql;die;
						$nameArray=sql_select($po_sql);
						foreach($nameArray as $row)
						{  
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								
							if($receive_basis==1 || $receive_basis==2)
							{ 
								$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and m.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description='$item_description' and a.brand_supplier='$brand_supref' and a.gmts_color_id='$gmts_color_id' and a.item_color='$item_color_id' and a.gmts_size_id ='$gmts_size_id' and a.item_size ='$item_size' and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$row[id]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
							}
							else
							{
								$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description='$item_description' and a.brand_supplier='$brand_supref' and a.gmts_color_id='$gmts_color_id' and a.item_color='$item_color_id' and a.gmts_size_id ='$gmts_size_id' and a.item_size ='$item_size' and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$row[id]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
							}
						 ?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td>
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                    <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
								</td>

                                <? 
									if($receive_basis==2) 
									{
										echo "<td width='100' align='right'>".number_format($row[csf('qty')],2)."</td>";
									}
									if($receive_basis==4) 
									{
										echo "<td width='100' align='right'>".number_format($row[csf('po_quantity')],2)."</td>";
									}
							    ?>
								<td width="130" align="right">
									<? echo number_format($tot_recv_qnty); ?>
                                    <input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
                                </td>
                                <td width="60" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
								<td align="right" width="115">
									<input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="" onKeyUp="calculate_total();">
								</td>
							</tr>
						<? 
						$i++; 
						} 
					}
					?>
                    </tbody>
                    <tfoot class="tbl_bottom">
                    	<td colspan="<? if($receive_basis==2 || $receive_basis==4) echo 4; else echo 3; ?>">Total</td>
                        <td id="total_recieve"><? echo $tot_trims_receive_qnty; ?></td>
                    </tfoot>
				</table>
			</div>
			<table width="620">
				 <tr>
					<td align="center" >
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>
				</tr>
			</table>
		</div>
	<?
	}
	if($type!=1)
	{
	?>
		</fieldset>
	</form>
    <?
	}
	?>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	
	if($search_by==1)
		$search_field='b.po_number';
	else if($search_by==2)
		$search_field='a.job_no';
	else
		$search_field='a.style_ref_no';	
		
	$company_id =$data[2];
	$buyer_id =$data[3];
	
	$all_po_id=$data[4];
	
	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else 
		$po_id_cond="";
	
	$hidden_po_id=explode(",",$all_po_id);

	if($buyer_id==0) { echo "Please Select Buyer First."; die; }
	
	$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_id_cond group by b.id"; 
	//echo $sql;die;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Job No</th>
                <th width="110">Style No</th>
                <th width="110">PO No</th>
                <th width="90">PO Quantity</th>
                <th width="50">UOM</th>
                <th>Shipment Date</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					$roll_used=0;
					
					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
							
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                        <td width="40" align="center">
                            <? echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                        </td>	
                        <td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td> 
                        <td width="50" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
                        <td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>	
                    </tr>
                    <?
                    $i++;
				}
			?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
            </table>
        </div>
         <table width="620" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="show_trims_recv();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>           
<?
	
exit();
}

if($action=="show_ile_load_uom")
{
	$data=explode("_",$data);
	
	$uom=$trim_group_arr[$data[0]]['uom'];
	$company = $data[1];
	$source = $data[2];
	$rate = $data[3];
	
	$ile=return_field_value("standard","variable_inv_ile_standard","source='$source' and company_name='$company' and category=4 and item_group=$data[0] and status_active=1 and is_deleted=0");

	echo "document.getElementById('cbo_uom').value 	= '".$uom."';\n";
	// NOTE :- ILE=standard, ILE% = standard/100*rate
	
	if($ile<0 || $ile=='')
	{
		$ile_percentage=0; $ile=0;
	}
	else
	{
		$ile_percentage = number_format(($ile/100)*$rate,$dec_place[3],".","");
	}
	echo "document.getElementById('ile_td').innerHTML 	= 'ILE% ".$ile."';\n";
	echo "document.getElementById('txt_ile').value 	= '".$ile_percentage."';\n";
	
	exit();	
}

if($action=="put_balance_qnty")
{
	$data=explode("_",$data);
	
	$recieve_basis = $data[0];
	$bookingNo_piId = $data[1];
	$bookingNo_piNo = $data[2];
	$item_group = $data[3];
	$item_description = $data[4];
	$brand_supref = $data[5];
	$sensitivity = $data[6];
	$gmts_color_id = $data[7];
	$gmts_size_id = $data[8];
	$item_color_id = $data[9];
	$item_size = $data[10];
	
	if($recieve_basis==1 || $recieve_basis==2)
	{
		if($recieve_basis==1)
		{
			$sql="select quantity as qnty, rate from com_pi_item_details where pi_id='$bookingNo_piId' and item_group='$item_group' and item_description='$item_description' and brand_supplier='$brand_supref' and color_id='$gmts_color_id' and item_color='$item_color_id' and size_id='$gmts_size_id' and item_size='$item_size' and status_active=1 and is_deleted=0";
			//$qnty=return_field_value("sum(quantity)","com_pi_item_details","pi_id='$bookingNo_piId' and item_group='$item_group' and item_description='$item_description' and brand_supplier='$brand_supref' and color_id='$gmts_color_id' and item_color='$item_color_id' and size_id='$item_size_id' and item_size='$item_size' and status_active=1 and is_deleted=0");
		}
		else if($recieve_basis==2)
		{
			/*if($sensitivity==1 || $sensitivity==3)
			{
				$qnty=return_field_value("sum(c.cons) as qnty","wo_booking_dtls b, wo_trim_book_con_dtls c","b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.color_number_id='$gmts_color_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0","qnty");
			}
			else if($sensitivity==2)
			{
				$qnty=return_field_value("sum(c.cons) as qnty","wo_booking_dtls b, wo_trim_book_con_dtls c","b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.gmts_sizes='$item_size_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0","qnty");
			}
			else if($sensitivity==4)
			{
				$qnty=return_field_value("sum(c.cons) as qnty","wo_booking_dtls b, wo_trim_book_con_dtls c","b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.color_number_id='$gmts_color_id' and c.gmts_sizes='$item_size_id' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0","qnty");
			}
			else if($sensitivity==0)
			{
				$qnty=return_field_value("sum(c.cons) as qnty","wo_booking_dtls b, wo_trim_book_con_dtls c","b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0","qnty");
			}*/
			$sql="select c.cons as qnty, c.rate from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.brand_supplier='$brand_supref' and c.item_color='$item_color_id' and c.item_size='$item_size' and b.status_active=1 and b.is_deleted=0";
		}
		//echo $sql;
		$result=sql_select($sql);
		$qnty=$result[0][csf('qnty')];
		$rate=$result[0][csf('rate')];
		
		if($recieve_basis==1 || $recieve_basis==2)
		{
			$receive_qnty=return_field_value("sum(a.receive_qnty) as qnty","inv_receive_master m, inv_trims_entry_dtls a","m.id=a.mst_id and m.receive_basis=$recieve_basis and m.entry_form=24 and m.booking_id='$bookingNo_piId' and a.item_group_id='$item_group' and a.item_description='$item_description' and a.brand_supplier='$brand_supref' and a.gmts_color_id='$gmts_color_id' and a.item_color='$item_color_id' and a.gmts_size_id ='$item_size_id' and a.item_size ='$item_size' and a.status_active=1 and a.is_deleted=0","qnty");
		
			$balance_qnty=$qnty-$receive_qnty;
		}
		else $balance_qnty=0;
	}
	else
	{
		$balance_qnty='';
		$rate='';
	}
	
	echo "document.getElementById('txt_bl_qty').value 	= '".number_format($balance_qnty,2)."';\n";
	echo "document.getElementById('txt_rate').value 	= '".$rate."';\n";
	
	exit();	
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$trims_recv_num=''; $trims_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			$new_trims_recv_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'TRE', date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form='24' and YEAR(insert_date)=".date('Y',time())." order by recv_number_prefix_num desc", "recv_number_prefix", "recv_number_prefix_num" ));
		 	
			$id=return_next_id( "id", "inv_receive_master", 1 ) ;
					 
			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, challan_date, booking_id, booking_no, booking_without_order, store_id, lc_no, source, supplier_id, currency_id, exchange_rate, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_trims_recv_system_id[1]."',".$new_trims_recv_system_id[2].",'".$new_trims_recv_system_id[0]."',24,4,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",".$txt_receive_chal_no.",".$txt_challan_date.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_store_name.",".$lc_id.",".$cbo_source.",".$cbo_supplier_name.",".$cbo_currency_id.",".$txt_exchange_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_receive_master (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
			
			$trims_recv_num=$new_trims_recv_system_id[0];
			$trims_update_id=$id;
		}
		else
		{
			$field_array_update="receive_basis*receive_date*challan_no*challan_date*booking_id*booking_no*booking_without_order*store_id*lc_no*source*supplier_id*currency_id*exchange_rate*updated_by*update_date";
			
			$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_challan_date."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$cbo_store_name."*".$lc_id."*".$cbo_source."*".$cbo_supplier_name."*".$cbo_currency_id."*".$txt_exchange_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
			
			$trims_recv_num=str_replace("'","",$txt_recieved_id);
			$trims_update_id=str_replace("'","",$update_id);
		}
		
		//details table entry here START-----------------------------------//		
		$rate = str_replace("'","",$txt_rate);
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$txt_receive_qnty = str_replace("'","",$txt_receive_qnty);
		
		$ile=return_field_value("standard","variable_inv_ile_standard","source=$cbo_source and company_name=$cbo_company_id and category=4 and item_group=$cbo_item_group and status_active=1 and is_deleted=0");
		$ile_cost = str_replace("'","",$txt_ile);
		
		$concattS = explode(",",return_field_value("concat(trim_uom,',',conversion_factor)","lib_item_group","id=$cbo_item_group")); 
		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];
		
		$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
 		$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
		$con_qnty = $conversion_factor*$txt_receive_qnty;
		$con_amount = $cons_rate*$con_qnty;
		$con_ile = $ile/$conversion_factor;
		$con_ile_cost = ($con_ile/100)*$cons_rate;
		
		$item_desc=''; $gmts_color_id=0; $gmts_size_id=0; 
		
		if(str_replace("'","",$cbo_receive_basis)==1 || str_replace("'","",$cbo_receive_basis)==2)
		{
			$item_desc=str_replace("'","",$hidden_item_description);
			$gmts_color_id=str_replace("'","",$txt_gmts_color_id);
			$gmts_size_id=str_replace("'","",$txt_gmts_size_id);
		}
		else 
		{
			$item_desc=str_replace("'","",$txt_item_description);
			
			if(str_replace("'","",$txt_gmts_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_gmts_color),$new_array_color))
				{
					$gmts_color_id = return_id( str_replace("'","",$txt_gmts_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$gmts_color_id]=str_replace("'","",$txt_gmts_color);
				}
				else $gmts_color_id =  array_search(str_replace("'","",$txt_gmts_color), $new_array_color); 
			}
			else
			{
				$gmts_color_id=0;
			}
			
			if(str_replace("'","",$txt_item_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_item_color),$new_array_color))
				{
					$txt_item_color_id = return_id( str_replace("'","",$txt_item_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$txt_item_color_id]=str_replace("'","",$txt_item_color);
				}
				else $txt_item_color_id =  array_search(str_replace("'","",$txt_item_color), $new_array_color); 
			}
			else
			{
				$txt_item_color_id=0;
			}
			
			if(str_replace("'","",$txt_gmts_size)!="")
			{
				if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_size))
				{
				  $gmts_size_id = return_id( str_replace("'","",$txt_gmts_size), $size_arr, "lib_size", "id,size_name","24");  
				  $new_array_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
				}
				else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_size); 
			}
			else
			{
				$gmts_size_id=0;
			}
		}
		
		if($gmts_size_id=="") $gmts_size_id=0;
		if($gmts_color_id=="") $gmts_color_id=0;
		if(str_replace("'","",$txt_item_color_id)=="") $txt_item_color_id=0; 

		$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=4 and item_group_id=$cbo_item_group and item_description='$item_desc' and brand_supplier=$txt_brand_supref and color='$gmts_color_id' and item_color=$txt_item_color_id and gmts_size='$gmts_size_id' and item_size=$txt_item_size and status_active=1 and is_deleted=0");
		if(count($row_prod)>0)
		{
			$prod_id=$row_prod[0][csf('id')];

			$curr_stock_qnty=$row_prod[0][csf('current_stock')]+$con_qnty;
			$curr_stock_value=$row_prod[0][csf('stock_value')]+$con_amount;
			$avg_rate_per_unit=$curr_stock_value/$curr_stock_qnty;
			
			$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$con_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		else
		{
			$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
			$item_name =$trim_group_arr[str_replace("'","",$cbo_item_group)]['name'];
			$prod_name_dtls=$item_name.", ".trim(str_replace("'","",$txt_item_description));
			
			$field_array_prod="id, company_id, store_id, item_category_id, item_group_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, item_color, gmts_size, item_size, brand_supplier, inserted_by, insert_date";
			
			$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",4,".$cbo_item_group.",'".$item_desc."','".$prod_name_dtls."',".$cons_uom.",".$cons_rate.",".$con_qnty.",".$con_qnty.",".$con_amount.",'".$gmts_color_id."',".$txt_item_color_id.",'".$gmts_size_id."',".$txt_item_size.",".$txt_brand_supref.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into product_details_master (".$field_array_prod.") values ".$data_array_prod;die;
			$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}

		$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, supplier_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, order_ile, order_ile_cost, cons_uom, cons_quantity, cons_rate, cons_amount, cons_ile, cons_ile_cost, balance_qnty, balance_amount, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",".$cbo_uom.",".$txt_receive_qnty.",".$rate.",".$txt_amount.",'".$ile."','".$ile_cost."',".$cons_uom.",".$con_qnty.",".$cons_rate.",".$con_amount.",".$con_ile.",".$con_ile_cost.",".$con_qnty.",".$con_amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 

		$id_dtls=return_next_id( "id", "inv_trims_entry_dtls", 1 ) ;
		
		$field_array_dtls="id, mst_id, trans_id, prod_id, item_group_id, item_description, brand_supplier, order_uom, order_id, receive_qnty, reject_receive_qnty, rate, amount, ile, ile_cost, gmts_color_id, item_color, gmts_size_id, item_size, save_string, item_description_color_size, cons_uom, cons_qnty, cons_rate, cons_ile, cons_ile_cost, book_keeping_curr, sensitivity, inserted_by, insert_date";
		
		$data_array_dtls="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$prod_id.",".$cbo_item_group.",'".$item_desc."',".$txt_brand_supref.",".$cbo_uom.",".$all_po_id.",".$txt_receive_qnty.",".$txt_reject_recv_qnty.",".$rate.",".$txt_amount.",'".$ile."',".$ile_cost.",'".$gmts_color_id."',".$txt_item_color_id.",'".$gmts_size_id."',".$txt_item_size.",".$save_data.",".$txt_item_description.",".$cons_uom.",".$con_qnty.",".$cons_rate.",".$con_ile.",".$con_ile_cost.",".$con_amount.",".$hidden_sensitivity.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_trims_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID4=sql_insert("inv_trims_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		$save_data=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_data);$i++)
		{
			$order_dtls=explode("_",$save_data[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty=$order_dtls[1];
			
			if($i==0) $add_comma=""; else $add_comma=",";
			
			if( $id_prop=="" ) $id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); else $id_prop = $id_prop+1;
			
			$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",1,24,".$id_dtls.",".$order_id.",".$prod_id.",".$order_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if($data_array_prop!="")
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			} 
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$trims_update_id."**".$trims_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				echo "0**".$trims_update_id."**".$trims_recv_num."**0";
			}
			else
			{
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		
		check_table_status( $_SESSION['menu_id'],0);
				
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$item_desc=''; $gmts_color_id=0; $gmts_size_id=0; 
		
		if(str_replace("'","",$cbo_receive_basis)==1 || str_replace("'","",$cbo_receive_basis)==2)
		{
			$item_desc=str_replace("'","",$hidden_item_description);
			$gmts_color_id=str_replace("'","",$txt_gmts_color_id);
			$gmts_size_id=str_replace("'","",$txt_gmts_size_id);
		}
		else 
		{
			$item_desc=str_replace("'","",$txt_item_description);
			
			if(str_replace("'","",$txt_gmts_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_gmts_color),$new_array_color))
				{
					$gmts_color_id = return_id( str_replace("'","",$txt_gmts_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$gmts_color_id]=str_replace("'","",$txt_gmts_color);
				}
				else $gmts_color_id =  array_search(str_replace("'","",$txt_gmts_color), $new_array_color); 
			}
			else
			{
				$gmts_color_id=0;
			}
			
			if(str_replace("'","",$txt_item_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_item_color),$new_array_color))
				{
					$txt_item_color_id = return_id( str_replace("'","",$txt_item_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$txt_item_color_id]=str_replace("'","",$txt_item_color);
				}
				else $txt_item_color_id =  array_search(str_replace("'","",$txt_item_color), $new_array_color); 
			}
			else
			{
				$txt_item_color_id=0;
			}
			
			if(str_replace("'","",$txt_gmts_size)!="")
			{
				if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_size))
				{
				  $gmts_size_id = return_id( str_replace("'","",$txt_gmts_size), $size_arr, "lib_size", "id,size_name","24");  
				  $new_array_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
				}
				else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_size); 
			}
			else
			{
				$gmts_size_id=0;
			}
		}
		
		if($gmts_size_id=="") $gmts_size_id=0;
		if(str_replace("'","",$txt_item_color_id)=="") $txt_item_color_id=0; 
		if($gmts_color_id=="") $gmts_color_id=0;
		
		$field_array_update="receive_basis*receive_date*challan_no*challan_date*booking_id*booking_no*booking_without_order*store_id*lc_no*source*supplier_id*currency_id*exchange_rate*updated_by*update_date";
			
		$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_challan_date."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$cbo_store_name."*".$lc_id."*".$cbo_source."*".$cbo_supplier_name."*".$cbo_currency_id."*".$txt_exchange_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; 
		
		$sql = sql_select("select a.cons_qnty, a.cons_rate, a.book_keeping_curr, b.avg_rate_per_unit, b.current_stock, b.stock_value from inv_trims_entry_dtls a, product_details_master b where a.id=$update_dtls_id and a.prod_id=b.id");
		
		$adjust_curr_stock=$adjust_sql[0][csf('current_stock')]-$adjust_sql[0][csf('cons_qnty')];
		$cur_st_value=$adjust_sql[0][csf('stock_value')]-$adjust_sql[0][csf('book_keeping_curr')];
		$cur_st_rate=number_format($cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
		
		$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
		$data_array_adjust=$adjust_curr_stock."*".$cur_st_value."*".$cur_st_rate;
		
		$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
		if($flag==1) 
		{
			if($rID_adjust) $flag=1; else $flag=0; 
		} 
		
		//details table entry here START-----------------------------------//		
		$rate = str_replace("'","",$txt_rate);
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$txt_receive_qnty = str_replace("'","",$txt_receive_qnty);
		
		$ile=return_field_value("standard","variable_inv_ile_standard","source=$cbo_source and company_name=$cbo_company_id and category=4 and item_group=$cbo_item_group and status_active=1 and is_deleted=0");
		$ile_cost = str_replace("'","",$txt_ile);
		
		$concattS = explode(",",return_field_value("concat(trim_uom,',',conversion_factor)","lib_item_group","id=$cbo_item_group")); 
		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];
		
		$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
 		$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
		$con_qnty = $conversion_factor*$txt_receive_qnty;
		$con_amount = $cons_rate*$con_qnty;
		$con_ile = $ile/$conversion_factor;
		$con_ile_cost = ($con_ile/100)*$cons_rate;
		
		$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=4 and item_group_id=$cbo_item_group and item_description='$item_desc' and brand_supplier=$txt_brand_supref and color='$gmts_color_id' and item_color=$txt_item_color_id and gmts_size='$gmts_size_id' and item_size=$txt_item_size and status_active=1 and is_deleted=0");
			
		if(count($row_prod)>0)
		{
			$prod_id=$row_prod[0][csf('id')];

			$curr_stock_qnty=$row_prod[0][csf('current_stock')]+$con_qnty;
			$curr_stock_value=$row_prod[0][csf('stock_value')]+$con_amount;
			$avg_rate_per_unit=$curr_stock_value/$curr_stock_qnty;
			
			$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$con_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		else
		{
			$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
			$item_name =$trim_group_arr[str_replace("'","",$cbo_item_group)]['name'];
			$prod_name_dtls=$item_name.", ".trim(str_replace("'","",$txt_item_description));
			
			$field_array_prod="id, company_id, store_id, item_category_id, item_group_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, item_color, gmts_size, item_size, brand_supplier, inserted_by, insert_date";
			
			$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",4,".$cbo_item_group.",'".$item_desc."','".$prod_name_dtls."',".$cons_uom.",".$cons_rate.",".$con_qnty.",".$con_qnty.",".$con_amount.",'".$gmts_color_id."',".$txt_item_color_id.",'".$gmts_size_id."',".$txt_item_size.",".$txt_brand_supref.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into product_details_master (".$field_array_prod.") values ".$data_array_prod;die;
			$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		
		$field_array_trans_update="receive_basis*pi_wo_batch_no*prod_id*transaction_date*supplier_id*store_id*order_uom*order_qnty*order_rate*order_amount*order_ile*order_ile_cost*cons_uom*cons_quantity*cons_rate*cons_amount*cons_ile*cons_ile_cost*balance_qnty*balance_amount*updated_by*update_date";
		
		$data_array_trans_update=$cbo_receive_basis."*".$txt_booking_pi_id."*".$prod_id."*".$txt_receive_date."*".$cbo_supplier_name."*".$cbo_store_name."*".$cbo_uom."*".$txt_receive_qnty."*".$rate."*".$txt_amount."*'".$ile."'*'".$ile_cost."'*".$cons_uom."*".$con_qnty."*".$cons_rate."*".$con_amount."*".$con_ile."*".$con_ile_cost."*".$con_qnty."*".$con_amount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID3=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$field_array_dtls_update="prod_id*item_group_id*item_description*brand_supplier*order_uom*order_id*receive_qnty*reject_receive_qnty*rate*amount*ile*ile_cost*gmts_color_id*item_color*gmts_size_id*item_size*save_string*item_description_color_size*cons_uom*cons_qnty*cons_rate*cons_ile*cons_ile_cost*book_keeping_curr*sensitivity*updated_by*update_date";
		
		$data_array_dtls_update=$prod_id."*".$cbo_item_group."*'".$item_desc."'*".$txt_brand_supref."*".$cbo_uom."*".$all_po_id."*".$txt_receive_qnty."*".$txt_reject_recv_qnty."*".$rate."*".$txt_amount."*'".$ile."'*".$ile_cost."*'".$gmts_color_id."'*".$txt_item_color_id."*'".$gmts_size_id."'*".$txt_item_size."*".$save_data."*".$txt_item_description."*".$cons_uom."*".$con_qnty."*".$cons_rate."*".$con_ile."*".$con_ile_cost."*".$con_amount."*".$hidden_sensitivity."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo "10**".$data_array_dtls_update;die;
		$rID4=sql_update("inv_trims_entry_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=24",0);
		if($flag==1) 
		{
			if($delete_prop) $flag=1; else $flag=0; 
		}
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		$save_data=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_data);$i++)
		{
			$order_dtls=explode("_",$save_data[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty=$order_dtls[1];
			
			if($i==0) $add_comma=""; else $add_comma=",";
			
			if( $id_prop=="" ) $id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); else $id_prop = $id_prop+1;
			
			$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",1,24,".$update_dtls_id.",".$order_id.",".$prod_id.",".$order_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if($data_array_prop!="")
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				echo "6**0**0**1";
			}
		}
		disconnect($con);
		die;
	}
	
}

if ($action=="trims_receive_popup_search")
{
	echo load_html_head_contents("Trims Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(id)
		{
			$('#hidden_recv_id').val(id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:880px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:875px; margin-left:5px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="820" class="rpt_table">
                <thead>
                    <th>Supplier</th>
                    <th>Received Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="200">Enter Received ID No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr>
                    <td align="center">
                    	<?
							echo create_drop_down( "cbo_supplier_name", 150,"select id, supplier_name from lib_supplier where find_in_set($cbo_company_id,tag_company) and (find_in_set(5,party_type) or find_in_set(4,party_type)) and status_active=1 and is_deleted=0 order by supplier_name",'id,supplier_name', 1, '-- ALL Supplier --',0);
						?>       
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Received ID",2=>"WO/PI",3=>"Challan No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_supplier_name').value, 'create_trims_recv_search_list_view', 'search_div', 'trims_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:15px; margin-left:3px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_trims_recv_search_list_view")
{
	
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$supplier_id =$data[5];
	
	if($supplier_id==0) $supplier_name="%%"; else $supplier_name=$supplier_id;
	
	if($start_date!="" && $end_date!="")
	{
		$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)	
			$search_field_cond="and recv_number like '$search_string'";
		else if($search_by==2)
			$search_field_cond="and booking_no like '$search_string'";
		else	
			$search_field_cond="and challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	$sql = "select id, recv_number, booking_no, supplier_id, store_id, source, currency_id, receive_date, challan_no, challan_date from inv_receive_master where entry_form=24 and status_active=1 and is_deleted=0 and company_id=$company_id and supplier_id like '$supplier_name' $search_field_cond $date_cond"; 

	$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$arr=array(2=>$supplier_arr,3=>$store_arr,7=>$currency,8=>$source);
	 
	echo  create_list_view("list_view", "Received No,WO/PI No,Supplier,Store,Receive date,Challan No,Challan Date,Currency,Source", "115,125,110,90,80,80,80,70","870","240",0, $sql, "js_set_value", "id", "", 0, "0,0,supplier_id,store_id,0,0,0,currency_id,source", $arr, "recv_number,booking_no,supplier_id,store_id,receive_date,challan_no,challan_date,currency_id,source", "",'','0,0,0,0,3,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_trims_recv')
{
	
	$data_array=sql_select("select id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, supplier_id, store_id, source, currency_id, challan_no, receive_date, challan_date, lc_no, exchange_rate from inv_receive_master where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_recieved_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value 			= '".$row[csf("receive_basis")]."';\n";

		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "set_receive_basis();\n";

		$lc_no=return_field_value("lc_number","com_btb_lc_master_details","id=$row[lc_no]");
		
		echo "document.getElementById('txt_receive_date').value 			= '".change_date_format($row[csf("receive_date")])."';\n";
		echo "document.getElementById('txt_booking_pi_no').value 			= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_pi_id').value 			= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('booking_without_order').value 		= '".$row[csf("booking_without_order")]."';\n";
		echo "document.getElementById('txt_receive_chal_no').value 			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_challan_date').value 			= '".change_date_format($row[csf("challan_date")])."';\n";
		echo "document.getElementById('txt_lc_no').value 					= '".$lc_no."';\n";
		echo "document.getElementById('lc_id').value 						= '".$row[csf("lc_no")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value 			= '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbo_currency_id').value 				= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value 			= '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_trims_receive',1,1);\n";  
		exit();
	}
}

if($action=="show_trims_listview")
{
	$sql="select id, item_group_id, item_description_color_size, brand_supplier, receive_qnty, rate, amount, reject_receive_qnty, gmts_color_id, gmts_size_id, item_color, item_size, order_uom from inv_trims_entry_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$item_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$arr=array(0=>$item_arr,7=>$unit_of_measurement,8=>$color_arr,9=>$size_arr);
	 
	echo create_list_view("list_view", "Item Group,Item Description,Brand/Sup Ref,Recv. Qnty, Rate, Amount, Reject Qty, UOM,Item Color,Item Size", "90,130,90,80,60,80,80,60,80","850","200",0, $sql, "get_php_form_data", "id", "'populate_trims_details_form_data'", 0, "item_group_id,0,0,0,0,0,0,order_uom,item_color,0", $arr, "item_group_id,item_description_color_size,brand_supplier,receive_qnty,rate,amount,reject_receive_qnty,order_uom,item_color,item_size", "requires/trims_receive_entry_controller",'','0,0,0,2,2,2,2,0,0,0');
	
	exit();
}

if($action=='populate_trims_details_form_data')
{
	$data_array=sql_select("select id, trans_id, prod_id, item_group_id, item_description, item_description_color_size, brand_supplier, receive_qnty, rate, amount, reject_receive_qnty, gmts_color_id, gmts_size_id, item_size, order_uom, order_id, save_string, item_color, ile, ile_cost, book_keeping_curr, sensitivity from inv_trims_entry_dtls where id='$data'");
	foreach ($data_array as $row)
	{ 
		$order_no=return_field_value("group_concat(po_number)","wo_po_break_down","id in($row[order_id])");
			
		echo "document.getElementById('cbo_item_group').value 				= '".$row[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('txt_amount').value 					= '".$row[csf("amount")]."';\n";
		echo "document.getElementById('txt_item_description').value 		= '".$row[csf("item_description_color_size")]."';\n";
		echo "document.getElementById('hidden_item_description').value 		= '".$row[csf("item_description")]."';\n";
		echo "document.getElementById('txt_receive_qnty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('txt_reject_recv_qnty').value 		= '".$row[csf("reject_receive_qnty")]."';\n";
		echo "document.getElementById('txt_brand_supref').value 			= '".$row[csf("brand_supplier")]."';\n";
		
		echo "get_php_form_data(document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_booking_pi_id').value+'_'+document.getElementById('txt_booking_pi_no').value+'_'+$row[item_group_id]+'_'+'".$row[csf('item_description')]."'+'_'+'".$row[csf('brand_supplier')]."'+'_'+$row[sensitivity]+'_'+$row[gmts_color_id]+'_'+$row[gmts_size_id]+'_'+$row[item_color]+'_'+'".$row[csf('item_size')]."', 'put_balance_qnty', 'requires/trims_receive_entry_controller')".";\n";
		
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_buyer_order').value 				= '".$order_no."';\n";
		echo "document.getElementById('ile_td').innerHTML 					= 'ILE% ".$row[csf("ile")]."';\n";
		echo "document.getElementById('txt_ile').value 						= '".$row[csf("ile_cost")]."';\n";
		echo "document.getElementById('txt_gmts_color').value 				= '".$color_arr[$row[csf("gmts_color_id")]]."';\n";
		echo "document.getElementById('txt_gmts_color_id').value 			= '".$row[csf("gmts_color_id")]."';\n";
		echo "document.getElementById('txt_item_color').value 				= '".$color_arr[$row[csf("item_color")]]."';\n";
		echo "document.getElementById('txt_item_color_id').value 			= '".$row[csf("item_color")]."';\n";
		echo "document.getElementById('txt_gmts_size').value 				= '".$size_arr[$row[csf("gmts_size_id")]]."';\n";
		echo "document.getElementById('txt_gmts_size_id').value 			= '".$row[csf("gmts_size_id")]."';\n";
		echo "document.getElementById('txt_item_size').value 				= '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('txt_book_currency').value 			= '".$row[csf("book_keeping_curr")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('hidden_sensitivity').value 			= '".$row[csf("sensitivity")]."';\n";
		echo "document.getElementById('save_data').value 					= '".$row[csf("save_string")]."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_receive',1,1);\n";  
		exit();
	}
}

if ($action=="goods_placement_popup")
{
	echo load_html_head_contents("Goods Placement Entry Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$dtls_data=sql_select("select item_group_id, item_description, receive_qnty from inv_trims_entry_dtls where id=$update_dtls_id");
?> 

	<script>
		
		var permission='<? echo $permission; ?>';
		
		function fn_addRow( i )
		{ 
			var row_num=$('#txt_tot_row').val();
			row_num++;
			
			var clone= $("#tr_"+i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});
			
			clone.find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return value }              
			});
			 
			}).end();
			
			$("#tr_"+i).after(clone);
			
			$('#txtSelfNo_'+row_num).val('');
			$('#txtBoxBinNo_'+row_num).val('');
			$('#txtCtnNo_'+row_num).val('');
			$('#txtCtnQnty_'+row_num).val('');
			
			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fn_addRow("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			
			$('#txt_tot_row').val(row_num);
		}
		
		function fn_deleteRow(rowNo) 
		{ 		
			var row_num=$('#tbl_list tbody tr').length;
			if(row_num!=1)
			{
				$("#tr_"+rowNo).remove();
			}
		}
		
		
		function fnc_goods_placement_entry(operation)
		{
			
			var dataString=""; var j=0;
			$("#tbl_list").find('tbody tr').each(function()
			{
				var txtRoomNo=$(this).find('input[name="txtRoomNo[]"]').val();
				var txtRackNo=$(this).find('input[name="txtRackNo[]"]').val();
				var txtSelfNo=$(this).find('input[name="txtSelfNo[]"]').val();
				var txtBoxBinNo=$(this).find('input[name="txtBoxBinNo[]"]').val();
				var txtCtnNo=$(this).find('input[name="txtCtnNo[]"]').val();
				var txtCtnQnty=$(this).find('input[name="txtCtnQnty[]"]').val();
				
				if(txtRackNo!="")
				{
					j++;
					
					dataString+='&txtRoomNo_' + j + '=' + txtRoomNo + '&txtRackNo_' + j + '=' + txtRackNo + '&txtSelfNo_' + j + '=' + txtSelfNo + '&txtBoxBinNo_' + j + '=' + txtBoxBinNo + '&txtCtnNo_' + j + '=' + txtCtnNo + '&txtCtnQnty_' + j + '=' + txtCtnQnty;
				}
			});
			
			if(j==0)
			{
				alert("Please Insert At Least One Rack No.");
				return;	
			}
			
			var data="action=save_update_delete_goods_placement&operation="+operation+'&tot_row='+j+get_submitted_data_string('dtls_id',"../../../")+dataString;
			//alert(data);return;
			freeze_window(operation);
			
			http.open("POST","trims_receive_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_goods_placement_entry_Reply_info;
		}
		
		function fnc_goods_placement_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				//alert(http.responseText);release_freezing();return;
				var reponse=trim(http.responseText).split('**');	
					
				show_msg(reponse[0]);
				
				if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
				{
					reset_form('goodsPlacement_1','','','','','dtls_id');
					load_dtls_part();
				}
				
				set_button_status(reponse[2], permission, 'fnc_goods_placement_entry',1);	
				release_freezing();	
			}
		}
		
		function load_dtls_part()
		{
			var list_view_goods_placement = return_global_ajax_value( <? echo $update_dtls_id; ?>, 'load_php_dtls_form', '', 'trims_receive_entry_controller');

			if(list_view_goods_placement!='')
			{
				$("#tbl_list tbody tr").remove();
				$("#tbl_list tbody").append(list_view_goods_placement);
				
				var row_num=$("#tbl_list tbody tr").length;
				$('#txt_tot_row').val(row_num);
			}
		}
		
		function fnc_carton_sticker()
		{
			data=<? echo $update_dtls_id; ?>;
			var url=return_ajax_request_value(data, "print_report_carton_sticker", "trims_receive_entry_controller");
			//alert(url);
			window.open(url,"##");
		}
	
    </script>

</head>

<body>
<div align="center">
	<? echo load_freeze_divs ("../../../",$permission,1); ?>
	<form name="goodsPlacement_1" id="goodsPlacement_1">
		<fieldset style="width:580px;">
        	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                <thead>
                    <th width="160">Item Group</th>
                    <th width="200">Item Description</th>
                    <th>Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                    <td><p>&nbsp;<? echo $trim_group_arr[$dtls_data[0][csf('item_group_id')]]['name']; ?></p></td>
                    <td><p>&nbsp;<? echo $dtls_data[0][csf('item_description')]; ?></p></td>
                    <td align="right"><? echo number_format($dtls_data[0][csf('receive_qnty')],2); ?>&nbsp;</td>
                    <input type="hidden" name="dtls_id" id="dtls_id" class="text_boxes" value="<? echo $update_dtls_id; ?>">
                    <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="">
                </tr>
            </table>
        </fieldset> 
        <fieldset style="width:770px; margin-top:10px">
            <legend>New Entry</legend>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760" id="tbl_list">
            	<thead>
                    <th width="110">Room No</th>
                    <th width="110">Rack No</th>
                    <th width="110">Shelf No</th>
                    <th width="110">Box/Bin</th>
                    <th width="110">Ctn. No</th>
                    <th width="110">Ctn. Qnty</th>
                    <th></th>
                </thead>
                <tbody>
                    <!--<tr id="tr_1">
                        <td>
                            <input type="text" name="txtRoomNo[]" id="txtRoomNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtRackNo[]" id="txtRackNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtSelfNo[]" id="txtSelfNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtBoxBinNo[]" id="txtBoxBinNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtCtnNo[]" id="txtCtnNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_1" class="text_boxes_numeric" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="button" id="increase_1" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(1)" />
                            <input type="button" id="decrease_1" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
						</td>
                    </tr>-->
                </tbody>    
            </table>
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760">
             	<tr>
                    <td colspan="7">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="7" align="center" class="button_container">
						<? 
							echo load_submit_buttons($permission, "fnc_goods_placement_entry", 0,0,"reset_form('goodsPlacement_1','','','','','txt_tot_row*dtls_id');$('#tbl_list tbody tr:not(:first)').remove();",1);
                        ?>
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                        <input type="button" name="sticker" class="formbutton" value="Carton Sticker" id="sticker" onClick="fnc_carton_sticker();" style="width:120px" /> 
                    </td>	  
                </tr>
			</table>
		</fieldset>
	</form>
</div>
</body>  
<script>

 	get_php_form_data(<? echo $update_dtls_id; ?>, "populate_data_goods_placement", "trims_receive_entry_controller" );
	load_dtls_part();
	        
</script>		
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="save_update_delete_goods_placement")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="id, entry_form, mst_id, trans_id, dtls_id, prod_id, room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty, inserted_by, insert_date";
		
		$dtls_data=sql_select("select mst_id, trans_id, prod_id from inv_trims_entry_dtls where id=$dtls_id");
		
		$data_array='';
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$txtRoomNo="txtRoomNo_".$j;
			$txtRackNo="txtRackNo_".$j;
			$txtSelfNo="txtSelfNo_".$j;
			$txtBoxBinNo="txtBoxBinNo_".$j;
			$txtCtnNo="txtCtnNo_".$j;
			$txtCtnQnty="txtCtnQnty_".$j;
			
			if($id=="") $id=return_next_id( "id", "inv_goods_placement", 1 ) ; else $id = $id+1;
			
			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",24,".$dtls_data[0][csf('mst_id')].",".$dtls_data[0][csf('trans_id')].",".$dtls_id.",".$dtls_data[0][csf('prod_id')].",'".$$txtRoomNo."','".$$txtRackNo."','".$$txtSelfNo."','".$$txtBoxBinNo."','".$$txtCtnNo."','".$$txtCtnQnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		}
		
		if($data_array!="")
		{
			//echo $data_array;die;
			$rID=sql_insert("inv_goods_placement",$field_array,$data_array,1);
			if($rID) $flag=1; else $flag=0; 
		}
			
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'", '', $dtls_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				echo "0**".str_replace("'", '', $dtls_id)."**1";
			}
			else
			{
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$delete=execute_query("delete from inv_goods_placement where dtls_id=$dtls_id and entry_form=24",0);

		if($delete) $flag=1; else $flag=0;
		
		$field_array="id, entry_form, mst_id, trans_id, dtls_id, prod_id, room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty, inserted_by, insert_date";
		
		$dtls_data=sql_select("select mst_id, trans_id, prod_id from inv_trims_entry_dtls where id=$dtls_id");
		
		$data_array='';
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$txtRoomNo="txtRoomNo_".$j;
			$txtRackNo="txtRackNo_".$j;
			$txtSelfNo="txtSelfNo_".$j;
			$txtBoxBinNo="txtBoxBinNo_".$j;
			$txtCtnNo="txtCtnNo_".$j;
			$txtCtnQnty="txtCtnQnty_".$j;
			
			if($id=="") $id=return_next_id( "id", "inv_goods_placement", 1 ) ; else $id = $id+1;
			
			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",24,".$dtls_data[0][csf('mst_id')].",".$dtls_data[0][csf('trans_id')].",".$dtls_id.",".$dtls_data[0][csf('prod_id')].",'".$$txtRoomNo."','".$$txtRackNo."','".$$txtSelfNo."','".$$txtBoxBinNo."','".$$txtCtnNo."','".$$txtCtnQnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		}
		
		if($data_array!="")
		{
			//echo $data_array;die;
			$rID=sql_insert("inv_goods_placement",$field_array,$data_array,1);
			if($flag==1)
			{
				if($rID) $flag=1; else $flag=0;
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==2)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$delete=execute_query("delete from inv_goods_placement where dtls_id=$dtls_id and entry_form=24",0);
		if($db_type==0)
		{
			if($delete)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		
		
		if($db_type==2 || $db_type==1 )
		{
			if($delete)
			{
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="load_php_dtls_form")
{
	$sql="select room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty from inv_goods_placement where dtls_id=$data and entry_form=24 and status_active=1 and is_deleted=0";
	$result=sql_select($sql);
	$count=count($result);
	
	if($count==0 ) // New Insert
	{
	?>
        <tr id="tr_1">
            <td>
                <input type="text" name="txtRoomNo[]" id="txtRoomNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtRackNo[]" id="txtRackNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtSelfNo[]" id="txtSelfNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtBoxBinNo[]" id="txtBoxBinNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtCtnNo[]" id="txtCtnNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_1" class="text_boxes_numeric" style="width:100px;"/>
            </td>
            <td>
                <input type="button" id="increase_1" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(1)" />
                <input type="button" id="decrease_1" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
            </td>
        </tr>
    <?
	}
	else // From Update
	{
		$i=0;
		foreach($result as $row)
		{
			$i++;
		?>
			<tr id="tr_<? echo $i; ?>">
                <td>
                    <input type="text" name="txtRoomNo[]" id="txtRoomNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('room_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtRackNo[]" id="txtRackNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('rack_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtSelfNo[]" id="txtSelfNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('self_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtBoxBinNo[]" id="txtBoxBinNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('box_bin_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtCtnNo[]" id="txtCtnNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('ctn_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:100px;" value="<? echo $row[csf('ctn_qnty')]; ?>"/>
                </td>
                <td>
                    <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>)" />
                    <input type="button" id="decrease_<? echo $i;?>" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
                </td>
            </tr>
		<?
		}		
	}
	
	exit();
}

if ($action=="populate_data_goods_placement")
{
	$result=sql_select("select id from inv_goods_placement where dtls_id=$data and entry_form=24 and status_active=1 and is_deleted=0");
	
	if(count($result)>0) $button_status=1; else $button_status=0;
	
	echo "set_button_status($button_status, '".$_SESSION['page_permission']."', 'fnc_goods_placement_entry',1,1);\n";  
	exit();
}

if($action=="print_report_carton_sticker")
{
	/*define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/fpdf.php');
	require('../../../ext_resource/pdf/html_table.php');
	
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}
		
	$pdf=new PDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',12);
	
	$html='<table width="100%" border="1"><tr>
		<td width="200"><table><tr><td>Fuad</td></tr></table></td>
		<td width="200"><table><tr><td>Shahriar</td></tr></table></td>
		</tr></table>';
	$html='<table border="1"><tr>';
	$sql="select a.challan_no, a.receive_date, b.prod_id, b.order_id, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_receive_master a, inv_trims_entry_dtls b, inv_goods_placement c where a.id=b.mst_id and a.entry_form=24 and a.item_category=4 and b.id=c.dtls_id and c.entry_form=24 and c.dtls_id=$data";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$html.='<td><table border="1" rules="all">
		<tr>
		<td width="170"><b>BUYER</b></td><td width="130">'.$row[csf('challan_no')].'</td>
		</tr>
		<tr>
		<td width="170"><b>ORDER-NO</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>NAME OF ITEM</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>CHALLAN-NO</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>RCVD-DATE</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>CTN-NO</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Room No</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Rack No</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Self No</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Box/Bin</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Ctn. Qty.</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>FIRST ISSUE DATE</b></td><td width="130"></td>
		</tr>
		</table></td>';
	}
	
	$html.='</tr></table>';
	
	$pdf->WriteHTML($html);	
	
	$name = 'carton_sticker_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;*/
	
	require_once('../../../ext_resource/pdf/tcpdf_5_9_082/tcc/config/lang/eng.php');
	require_once('../../../ext_resource/pdf/tcpdf_5_9_082/tcc/tcpdf.php');
	header ('Content-type: text/html; charset=utf-8'); 
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'RA4', true, 'UTF-8', false);	// create new PDF document
	 
	// set document information
	$pdf->SetCreator('Md. Fuad Shahriar');
	$pdf->SetAuthor('Md. Fuad Shahriar');
	$pdf->SetTitle('Logic ERP');
	$pdf->SetSubject('Goods Placement Carton Sticker');
	//$pdf->SetKeywords('Logic, HRM, Payroll, HRM & Payroll, ID Card');
	
	// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);	//set default monospaced font
	$pdf->SetMargins(12, 15, 8);								//set margins
	$pdf->SetAutoPageBreak(TRUE, 5);						//set auto page breaks
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);				//set image scale factor
	$pdf->setLanguageArray($l);								//set some language-dependent strings
	$pdf->SetFont('times', '', 12);
	
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}
		
	$pdf->AddPage();
	/*$html='<table width="100%" border="1"><tr>
		<td width="200"><table><tr><td>Fuad</td></tr></table></td>
		<td width="200"><table><tr><td>Shahriar</td></tr></table></td>
		</tr></table>';*/

	$i=1; $br=0; $order_no='';	
	$html='<table border="0"><tr>';
	$sql="select a.challan_no, a.receive_date, b.prod_id, b.order_id, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_receive_master a, inv_trims_entry_dtls b, inv_goods_placement c where a.id=b.mst_id and a.entry_form=24 and a.item_category=4 and b.id=c.dtls_id and c.entry_form=24 and c.dtls_id=$data";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		if($i==1)
		{
			if($row[csf('order_id')]!="")
			{
				$order_data=sql_select("select a.buyer_name, group_concat(b.po_number) as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".$row[csf('order_id')].")");
				$buyer=$buyer_arr[$order_data[0][csf('buyer_name')]];
				$order_no=$order_data[0][csf('po_number')];
			}
			
			$item_desc=return_field_value("product_name_details","product_details_master","id=".$row[csf('prod_id')]);
		}
		
		$html.='<td><table border="1" rules="all">
		<tr>
		<td width="150"><b>&nbsp;BUYER</b></td><td width="170">&nbsp;'.$buyer.'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;ORDER-NO</b></td><td width="170">&nbsp;'.$order_no.'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;NAME OF ITEM</b></td><td width="170">&nbsp;'.$item_desc.'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;CHALLAN-NO</b></td><td width="170">&nbsp;'.$row[csf('challan_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;RCVD-DATE</b></td><td width="170">&nbsp;'.change_date_format($row[csf('receive_date')]).'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;CTN-NO</b></td><td width="170">&nbsp;'.$row[csf('ctn_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;ROOM NO</b></td><td width="170">&nbsp;'.$row[csf('room_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;RACK NO</b></td><td width="170">&nbsp;'.$row[csf('rack_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;SHELF NO</b></td><td width="170">&nbsp;'.$row[csf('self_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;BOX/BIN</b></td><td width="170">&nbsp;'.$row[csf('box_bin_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;CARTON QTY.</b></td><td width="170">&nbsp;'.$row[csf('ctn_qnty')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;FIRST ISSUE DATE</b></td><td width="170">&nbsp;</td>
		</tr>
		</table></td>';
		
		if($i%2==0) {$html.='</tr><tr><td><br><br><br><br></td></tr><tr>';}
		if( $i % 6 == 0 && $i < count( $result ) ) {
				$html .= "</tr></table>";
				$pdf->writeHTML($html, true, false, true, false, '');
				$pdf->AddPage();
				$html='<table border="0"><tr>';
			}
		$i++;
		
	}
	
	$html.='</tr></table>';	
		
	$pdf->writeHTML($html, true, false, true, false, '');
	$name = 'carton_sticker_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;	
}

function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor)
{
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;	
}

if ($action=="trims_receive_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$pi_library=return_library_array( "select id, pi_number from com_pi_master_details", "id", "pi_number"  );
	$wo_library=return_library_array( "select id, booking_no from wo_booking_mst", "id", "booking_no"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	
	$sql="select id, recv_number, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate from   inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	
?>
<div style="width:930px;">
    <table width="910" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
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
        </tr>
        <tr>
            <td colspan="7" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Challan</u></strong></center></td>
        </tr>
        <tr>
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="120"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>WO/PI:</strong></td> <td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            <td><strong>Supplier:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong> Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
        </tr>
    </table>
	<br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="910"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="110" align="center">Item Group</th>
                <th width="130" align="center">Item Des.</th>
                <th width="70" align="center">Gmts Color</th>
                <th width="70" align="center">Buyer Order</th>
                <th width="40" align="center">UOM</th>
                <th width="70" align="center">Rec. Qty </th>
                <th width="60" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th width="50" align="center">Reject Qty</th>
                <th width="70" align="center">Rack</th>
                <th width="70" align="center">Shelf</th>
                <th width="70" align="center">Box</th>
            </thead>
    	<?
        $i=1; 
        $mst_id=$dataArray[0][csf('id')];
        $sql_dtls="select b.id, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.cons_qnty, b.cons_uom, b.receive_qnty, b.rate, b.amount, b.reject_receive_qnty,
        c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty
        
         from inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
        //echo $sql_dtls;
        $sql_result=sql_select($sql_dtls);
		foreach($sql_result as $row)
        {
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
                
			$order_no=$row[csf('order_id')];
			$po_number = return_field_value("group_concat(po_number) as po_number","wo_po_break_down"," id in ($order_no)","po_number");
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td><? echo $i; ?></td>
				<td><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>
				<td><p><? echo $row[csf('item_description')]; ?></p></td>
				<td><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
				<td><p><? echo $po_number; ?></p></td>
				<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
				<td align="right"><? echo $row[csf('receive_qnty')]; ?></td>
				<td align="right"><? echo number_format($row[csf('rate')],2,'.',''); ?></td>
				<td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
				<td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
				<td align="center"><p><? echo $row[csf('rack_no')]; ?></p></td>
				<td align="center"><p><? echo $row[csf('self_no')]; ?></p></td>
				<td align="center"><p><? echo $row[csf('box_bin_no')]; ?></p></td>
			</tr>
		<?php
        	$i++;
        }
		echo signature_table(35, $data[0], "910px");
		?>
		</table>
	</div>
</div>
<?
exit();
}
?>

