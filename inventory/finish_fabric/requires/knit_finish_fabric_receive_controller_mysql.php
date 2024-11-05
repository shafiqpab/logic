<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");

//====================Location ACTION========
if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 162, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 162, "select id, store_name from lib_store_location where company_id='$data' and find_in_set(2,item_category_id) and status_active=1 and is_deleted=0 order by store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

if($action=="load_drop_down_dyeing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_dyeing_company", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Dyeing Company--", "$company_id", "","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_dyeing_company", 162, "select id, supplier_name from lib_supplier where find_in_set(21,party_type) and find_in_set($company_id,tag_company) and status_active=1 and is_deleted=0","id,supplier_name", 1, "--Select Dyeing Company--", 1, "" );
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_company", 162, $blank_array,"",1, "--Select Dyeing Company--", 1, "" );
	}
	exit();
}


if($action=="wo_pi_production_popup")
{
	echo load_html_head_contents("WO/PI/Production Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(id,no,type)
		{
			$('#hidden_wo_pi_production_id').val(id);
			$('#hidden_wo_pi_production_no').val(no);
			$('#booking_without_order').val(type);
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
                    <th width="240">Enter WO/PI/Pro. Recv. No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_wo_pi_production_id" id="hidden_wo_pi_production_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_wo_pi_production_no" id="hidden_wo_pi_production_no" class="text_boxes" value=""> 
                        <input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
                    </th> 
                </thead>
                <tr>
                    <td align="center">	
                    	<?
							echo create_drop_down("cbo_receive_basis",152,$receive_basis_arr,"",1,"-- Select --",$receive_basis,"","1","1,2,4,6,9");
						?>
                    </td>                 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value, 'create_wo_pi_production_search_list_view', 'search_div', 'knit_finish_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=="create_wo_pi_production_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$receive_basis=$data[1];
	$company_id =$data[2];
	
	if($receive_basis==1)
	{
		if(trim($data[0])!="")
		{
			$search_field_cond="and pi_number like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}
		
		$sql = "select id, pi_number, supplier_id, pi_date, last_shipment_date, pi_basis_id, internal_file_no, currency_id, source from com_pi_master_details where item_category_id=2 and status_active=1 and is_deleted=0 and importer_id=$company_id $search_field_cond"; 
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
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('pi_number')]; ?>','0');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="125"><p><? echo $row[csf('pi_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>  
                        <td width="110"><p><? echo $pi_basis[$row[csf('pi_basis_id')]]; ?>&nbsp;</p></td>             
						<td width="160"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="100" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
						<td width="100"><p><? echo $row[csf('internal_file_no')]; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
						<td><p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
	<?	
	}
	else if($receive_basis==2)
	{
		if(trim($data[0])!="")
		{
			$search_field_cond="and a.booking_no like '$search_string'";
			$search_field_cond_sample="and s.booking_no like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}
		
		$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, b.job_no_mst, 0 as type from wo_booking_mst a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_id=$company_id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond group by a.id
				union all
				SELECT s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, '' as po_break_down_id, s.item_category, s.delivery_date, '' as job_no_mst, 1 as type FROM wo_non_ord_samp_booking_mst s WHERE s.company_id=$company_id and s.status_active =1 and s.is_deleted=0 and s.item_category=2 $search_field_cond_sample 
		"; 
		//echo $sql;die;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="115">Booking No</th>
				<th width="80">Booking Date</th>               
				<th width="100">Buyer</th>
				<th width="85">Item Category</th>
				<th width="80">Delivary date</th>
				<th width="90">Job No</th>
				<th width="90">Order Qnty</th>
				<th width="80">Shipment Date</th>
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
					
					if($row[csf('po_break_down_id')]!="")
					{
						$po_sql="select b.po_number, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($row[po_break_down_id]) group by b.id";									
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
					
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('type')]; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>               
						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
						<td width="85"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
						<td width="90"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
						<td width="90" align="right"><? echo $po_qnty_in_pcs; ?>&nbsp;</td>
						<td width="80" align="center"><? echo change_date_format($min_shipment_date); ?>&nbsp;</td>
						<td><p><? echo $po_no; ?>&nbsp;</p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
	<?	
	}
	else
	{
		if(trim($data[0])!="")
		{
			$search_field_cond="and recv_number like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}
		
		$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
		
		$po_array=array();
		$po_sql=sql_select("select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and company_name='$company_id'");
		foreach($po_sql as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		}
		
		$sql = "select id, recv_number_prefix_num, recv_number, store_id, knitting_source, knitting_company, receive_date, challan_no from inv_receive_master where entry_form=7 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond"; 
		//echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
			<thead>
				<th width="30">SL</th>
                <th width="115">Production No</th>
				<th width="80">Production Date</th>
                <th width="90">Challan No</th>
				<th width="100">Dyeing Source</th>
                <th width="110">Dyeing Company</th>
                <th width="100">Store</th>
				<th width="120">Style Ref.</th>
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
						
					if($row[csf('knitting_source')]==1)	$knit_comp=$company_arr[$row[csf('knitting_company')]]; else $knit_comp=$supplier_arr[$row[csf('knitting_company')]];
					
					$order_id=return_field_value("group_concat(order_id)","pro_finish_fabric_rcv_dtls","mst_id=$row[id] and status_active=1 and is_deleted=0");
					$order_id=array_unique(explode(",",$order_id));
					
					$order_no=''; $style_ref='';
					foreach($order_id as $value)
					{
						if($order_no=='') $order_no=$po_array[$value]['no']; else $order_no.=",".$po_array[$value]['no'];
						if($style_ref=='') $style_ref=$po_array[$value]['style']; else $style_ref.=",".$po_array[$value]['style'];
					}
					
					$style_ref=array_unique(explode(",",$style_ref));
					$style_ref=implode(",",$style_ref);
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('recv_number')]; ?>','0','<? echo $row[csf('buyer_id')]; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="115"><? echo $row[csf('recv_number')]; ?></td>
                        <td width="80" align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>   
                        <td width="90"><p><? echo $row[csf('challan_no')]; ?></p></td>            
						<td width="100"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                        <td width="110"><p><? echo $knit_comp; ?></p></td>
                        <td width="100"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
						<td width="120"><p><? echo $style_ref; ?></p></td>
						<td><p><? echo $order_no; ?></p></td>
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

if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  

?> 
	<script> 
		
		function fn_show_check()
		{
			if( form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}			
			show_list_view ( $('#txt_search_common').val()+'_'+$('#cbo_search_by').val()+'_'+<? echo $cbo_company_id; ?>+'_'+$('#cbo_buyer_name').val()+'_'+'<? echo $hidden_order_id; ?>', 'create_po_search_list_view', 'search_div', 'knit_finish_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}
		
				
		var selected_id = new Array(); var selected_name = new Array();
		
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
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		
		function js_set_value( str ) 
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
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
			
			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);
		}
		
		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
		
    </script>

</head>
<body>
	
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:5px">
        <input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
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
						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_name, "","" ); 
					?>       
				</td>
				<td align="center">	
					<?
						$search_by_arr=array(1=>"PO No",2=>"Job No");

						echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
					?>
				</td>                 
				<td align="center">				
					<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
				</td> 						
				<td align="center">
					<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
				</td>
			</tr>
		</table>
        <div id="search_div" style="margin-top:10px"></div>
		</fieldset>
	</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string=trim($data[0]);
	$search_by=$data[1];
	
	$search_con="";
	if($search_by==1 && $search_string!="")
		$search_con = " and b.po_number like '%$search_string%'";
	else if($search_by==2 && $search_string!="")
		$search_con =" and a.job_no like '%$search_string%'"; 
		
	$company_id =$data[2];
	$buyer_id =$data[3];	
	$all_po_id=$data[4];
	
	$hidden_po_id=explode(",",$all_po_id);
	 
	if($buyer_id==0) { echo "<b>Please Select Buyer First</b>"; die; }

	$sql = "select a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date 
	from wo_po_details_master a, wo_po_break_down b
	where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id";

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
        <div style="width:618px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
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
					 
					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{ 
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                            <td width="40" align="center"><?php echo "$i"; ?>
                            	<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>	
                                <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
                                <input type="hidden" name="txt_buyer" id="txt_buyer<?php echo $i ?>" value="<? echo $selectResult[csf('buyer_name')]; ?>"/>
                                <input type="hidden" name="txt_styleRef" id="txt_styleRef<?php echo $i ?>" value="<? echo $selectResult[csf('style_ref_no')]; ?>"/>	
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
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $po_row_id; ?>"/>
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
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>

                    </div>
                </td>
            </tr>
        </table>
	</div>           
<?
	
exit();
}

if ($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  
?> 
	<script> 
		var receive_basis=<? echo $receive_basis; ?>;
		var roll_maintained=<? echo $roll_maintained; ?>;
		
		function distribute_qnty(str)
		{
			//if(str==1 && roll_maintained==0)
			if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var txt_prop_finish_qnty=$('#txt_prop_finish_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var len=totalFinish=0;
				
				$("#tbl_list_search").find('tr').each(function()
				{
					len=len+1;
					var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
					var perc=(po_qnty/tot_po_qnty)*100;
					
					var finish_qnty=(perc*txt_prop_finish_qnty)/100;
					totalFinish = totalFinish*1+finish_qnty*1;
					totalFinish = totalFinish.toFixed(2);
											
					if(tblRow==len)
					{
						var balance = txt_prop_finish_qnty-totalFinish;
						if(balance!=0) totalFinish=totalFinish*1+(balance*1);
					}
					
					$(this).find('input[name="txtfinishQnty[]"]').val(finish_qnty.toFixed(2));
					
				});
			}
			else
			{
				$('#txt_prop_finish_qnty').val('');
				$("#tbl_list_search").find('tr').each(function()
				{
					$(this).find('input[name="txtfinishQnty[]"]').val('');
				});
			}
		}	
		
		function fnc_close()
		{
			var save_string='';	 var tot_finish_qnty=''; var order_nos='';
			var po_id_array = new Array(); var buyer_id_array = new Array(); var buyer_name_array = new Array();

			$("#tbl_list_search").find('tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtPoNo=$(this).find('input[name="txtPoNo[]"]').val();
				var txtfinishQnty=$(this).find('input[name="txtfinishQnty[]"]').val();
				//var txtRoll=$(this).find('input[name="txtRoll[]"]').val();
				var buyerId=$(this).find('input[name="buyerId[]"]').val();
				var buyerName=$(this).find('input[name="buyerName[]"]').val();

				tot_finish_qnty=tot_finish_qnty*1+txtfinishQnty*1;
				
				if(txtfinishQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtPoId+"**"+txtfinishQnty;
						//save_string=txtPoId+"**"+txtfinishQnty+"**"+txtRoll;
					}
					else
					{
						save_string+=","+txtPoId+"**"+txtfinishQnty;
						//save_string+=","+txtPoId+"**"+txtfinishQnty+"**"+txtRoll;
					}
					
					if( jQuery.inArray( txtPoId, po_id_array) == -1 )
					{
						po_id_array.push(txtPoId);
						if(order_nos=='') order_nos=txtPoNo; else order_nos+=","+txtPoNo;
					}
					
					if( jQuery.inArray( buyerId, buyer_id_array) == -1 )
					{
						buyer_id_array.push(buyerId);
						buyer_name_array.push(buyerName);
					}
				}
			});
			
			$('#save_string').val( save_string );
			$('#tot_finish_qnty').val(tot_finish_qnty);
			$('#all_po_id').val( po_id_array );
			$('#order_nos').val( order_nos );
			$('#buyer_id').val( buyer_id_array );
			$('#buyer_name').val( buyer_name_array );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );

			parent.emailwindow.hide();
		}
		
    </script>

</head>
<body>
	
<form name="searchdescfrm"  id="searchdescfrm">
    <fieldset style="width:620px;margin-left:5px">
   		<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
        <input type="hidden" name="tot_finish_qnty" id="tot_finish_qnty" class="text_boxes" value="">
        <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
         <input type="hidden" name="order_nos" id="order_nos" class="text_boxes" value="">
        <input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" value="">
        <input type="hidden" name="buyer_name" id="buyer_name" class="text_boxes" value="">
        <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
	<?   
	if($receive_basis==1 || $receive_basis==4 || $receive_basis==6 || $receive_basis==9)
	{
	?>
		<div id="search_div" style="margin-top:10px">
            <div style="width:600px; margin-top:10px" align="center">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
                    <thead>
                        <th>Total Issue Qnty</th>
                        <th>Distribution Method</th>
                    </thead>
                    <tr class="general">
                        <td><input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_production_qty; ?>" style="width:120px"  onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" /></td>
                        <td>
                            <?
                                $distribiution_method=array(1=>"Proportionately",2=>"Manually");
                                echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0,"--Select--",$prev_distribution_method,"distribute_qnty(this.value);",0 );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="margin-left:10px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580">
                    <thead>
                        <th width="130">PO No</th>
                        <th width="90">Shipment Date</th>
                        <th width="100">PO Qnty</th>
                        <th>Finish Qnty</th>
                        <!--<?if($roll_maintained==1)
                        {
                        ?>
                            <th>Roll</th>
                        <?}
                        ?>-->
                    </thead>
                </table>
				<div style="width:600px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580" id="tbl_list_search">  
                		<?
						$i=1; $tot_po_qnty=0;
						/*if($receive_basis==9)
						{
							$hidden_order_id=return_field_value("order_id","pro_finish_fabric_rcv_dtls","id=$finish_production_dtls_id");
						}*/
						
						if($hidden_order_id!="")
						{
							$po_sql="select b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, a.buyer_name
							from wo_po_details_master a, wo_po_break_down b 
							where a.job_no=b.job_no_mst and b.id in ($hidden_order_id) group by b.id";
						}				 
						//echo $po_sql; 
						$po_data_array=array();					
						$explSaveData = explode(",",$save_data);
						foreach($explSaveData as $val)
						{
							$finQnty = explode("**",$val);
							//$po_data_array[$finQnty[0]]['qty']=$finQnty[1];	
							//$po_data_array[$finQnty[0]]['roll']=$finQnty[2];
							$po_data_array[$finQnty[0]]=$finQnty[1];		
						}
						
						$nameArray=sql_select($po_sql);
						foreach($nameArray as $row)
						{  
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								
							$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
							$qnty = $po_data_array[$row[csf('id')]];
						?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="130">
                                    <p><? echo $row[csf('po_number')]; ?></p>
                                    <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                    <input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
                                    <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
                                    <input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$row[csf('buyer_name')]];?>">
                                </td>
                                <td width="90" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                <td width="100" align="right">
                                    <? echo $row[csf('po_qnty_in_pcs')]; ?>
                                    <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
                                </td>
                                <td align="center">
                                    <input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $qnty; ?>"/>
                                </td>	
                                <!--<?if($roll_maintained==1)
								{
								?>
									<td align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<?// echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<?// if($roll_no!=0) echo $roll_no; ?>" disabled="disabled"/>
									</td>
								<?}
								?>	-->			
                            </tr>
						<? 
                        $i++; 
						} 
						?>
                        <input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
                    </table> 
				</div>
                <table width="620" id="table_id">
                     <tr>
                        <td align="center" >
                            <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        </td>
                    </tr>
                </table>
            </div>
       </div>     
	<?
	}
	else
	{
	?>
		<div style="width:600px; margin-top:10px" align="center">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
				<thead>
					<th>Total Issue Qnty</th>
					<th>Distribution Method</th>
				</thead>
				<tr class="general">
                    <td><input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_production_qty; ?>" style="width:120px"  onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" /></td>
                    <td>
                        <?
                            $distribiution_method=array(1=>"Proportionately",2=>"Manually");
                            echo create_drop_down("cbo_distribiution_method", 160, $distribiution_method,"",0,"--Select--",$prev_distribution_method,"distribute_qnty(this.value);",0);
                        ?>
                    </td>
                </tr>
			</table>
		</div>
		<div style="margin-left:10px; margin-top:10px">
        	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580">
				<thead>
               		<th width="130">PO No</th>
                    <th width="90">Shipment Date</th>
                    <th width="100">PO Qnty</th>
                    <th>Finish Qnty</th>
				</thead>
			</table>
			<div style="width:600px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="580" id="tbl_list_search"> 
					<? 
					$i=1; $tot_po_qnty=0; $po_data_array=array();
					
					$po_sql="select a.buyer_name, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_booking_dtls c, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no='$txt_booking_no' group by b.id";
					
					//echo $po_sql;
					$explSaveData = explode(",",$save_data);
					foreach($explSaveData as $val)
					{
						$finQnty = explode("**",$val);
						$po_data_array[$finQnty[0]]=$finQnty[1];		
					}
 					$nameArray=sql_select($po_sql);
					foreach($nameArray as $row)
					{  
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
						$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
						$qnty = $po_data_array[$row[csf('id')]];						
						
					 ?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                            <td width="130">
                                <p><? echo $row[csf('po_number')]; ?></p>
                                <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                <input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
                                <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
                                <input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$row[csf('buyer_name')]];?>">
                            </td>
                            <td width="90" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                            <td width="100" align="right">
                                <? echo $row[csf('po_qnty_in_pcs')]; ?>
                                <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
                            </td>
                            <td align="center">
                                <input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $qnty;?>"/>
                            </td>	
                        </tr>
					<? 
					$i++; 
					} 
					?>
					<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
 				</table>
			</div>
			<table width="620" id="table_id">
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
    </fieldset>
</form>
        
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='show_fabric_desc_listview')
{
	$data=explode("**",$data);
	$booking_pi_production_no=$data[0];
	$is_sample=$data[1];
	$receive_basis=$data[2];
	
	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
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
	}
	
	if($receive_basis==1)
	{
		$sql="select determination_id, '' as body_part_id, gsm as gsm_weight, dia_width, sum(quantity) as qnty from com_pi_item_details where pi_id='$booking_pi_production_no' and status_active=1 and is_deleted=0 group by determination_id, gsm_weight, dia_width";
	}
	else if($receive_basis==2)
	{
		if($is_sample==0)
		{
			$sql="select b.lib_yarn_count_deter_id as determination_id, b.body_part_id, b.gsm_weight, a.dia_width, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_pi_production_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.lib_yarn_count_deter_id, b.body_part_id, b.gsm_weight, a.dia_width";
		}
		else
		{
			$sql="select lib_yarn_count_deter_id as determination_id, body_part as body_part_id, gsm_weight, dia_width, construction, composition, sum(grey_fabric) as qnty from wo_non_ord_samp_booking_dtls where booking_no='$booking_pi_production_no' and status_active=1 and is_deleted=0 group by lib_yarn_count_deter_id, body_part, gsm_weight, dia_width, construction, composition";
		}
	}
	else
	{
		$sql="select id, fabric_description_id as determination_id, body_part_id, gsm as gsm_weight, width as dia_width, receive_qnty as qnty from pro_finish_fabric_rcv_dtls where mst_id='$booking_pi_production_no' and status_active=1 and is_deleted=0";
	}
	$data_array=sql_select($sql);
	
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="360">
        <thead>
            <th width="30">SL</th>
            <th>Fabric Description</th>
            <th width="80">Qnty</th>
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
				
				$fabric_desc='';
				if($receive_basis!=1)
				{
					$fabric_desc=$body_part[$row[csf('body_part_id')]].", ";
				}
				
				if($receive_basis==2 && $is_sample==1 && $row[csf('determination_id')]==0)
				{
					$fabric_desc.=$row[csf('construction')].", ".$row[csf('composition')].", ".$row[csf('gsm_weight')];
				}
				else
				{
					$fabric_desc.=$composition_arr[$row[csf('determination_id')]].", ".$row[csf('gsm_weight')];
				}
					
				if($row[csf('dia_width')]!="")
				{
					$fabric_desc.=", ".$row[csf('dia_width')];	
				}
				
				if($receive_basis==9)
				{
					$data=$row[csf('id')];
				}
				else
				{
					if($receive_basis==2 && $is_sample==1 && $row[csf('determination_id')]==0)
					{
						$cons_comp=$row[csf('construction')].", ".$row[csf('composition')];
						$data=$row[csf('body_part_id')]."**".$cons_comp."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')];
					}
					else
					{
						$data=$row[csf('body_part_id')]."**".$composition_arr[$row[csf('determination_id')]]."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')];
					}
				}
				
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>")' style="cursor:pointer" >
                    <td><? echo $i; ?></td>
                    <td><? echo $fabric_desc; ?></td>
                    <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
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

if($action=='populate_data_from_production')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	
	$data_array=sql_select("select id, trans_id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, color_id, receive_qnty, reject_qty, no_of_roll, machine_no_id, rack_no, shelf_no, order_id, buyer_id from pro_finish_fabric_rcv_dtls b where id='$id'");
	foreach($data_array as $row)
	{ 
		$buyer_name='';
		$buyer=explode(",",$row[csf('buyer_id')]);
		foreach($buyer as $val )
		{
			if($buyer_name=='') $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
		}
		
		$comp='';
		$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$row[fabric_description_id]");
				
		if($determination_sql[0][csf('construction')]!="")
		{
			$comp=$determination_sql[0][csf('construction')].", ";
		}
		
		foreach( $determination_sql as $d_row )
		{
			$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
		}
		
		if($row[csf("order_id")]!="")
		{
			$order_nos=return_field_value("group_concat(po_number)","wo_po_break_down","id in(".$row[csf("order_id")].")");
		}
		
		echo "document.getElementById('txt_batch_id').value 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_arr[$row[csf("batch_id")]]."';\n";
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_fabric_desc').value 				= '".$comp."';\n";
		echo "document.getElementById('fabric_desc_id').value 				= '".$row[csf("fabric_description_id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia_width').value 				= '".$row[csf("width")]."';\n";
		echo "document.getElementById('product_id').value 					= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_production_qty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('txt_reject_qty').value 				= '".$row[csf("reject_qty")]."';\n";
		echo "document.getElementById('txt_no_of_roll').value 				= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('buyer_name').value 					= '".$buyer_name."';\n";
		echo "document.getElementById('buyer_id').value 					= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_machine_name').value 			= '".$row[csf("machine_no_id")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf_no")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('hidden_order_id').value 				= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value 				= '".$order_nos."';\n";
		//echo "document.getElementById('finish_production_dtls_id').value 	= '".$row[csf("id")]."';\n";
		
		$save_string=''; 
		$dataPoArray=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where dtls_id='$id' and entry_form=7 and status_active=1 and is_deleted=0");
		foreach($dataPoArray as $row_po)
		{ 
			if($save_string=="")
			{
				$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
			}
			else
			{
				$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
			}
		}
		
		/*if($roll_maintained==1)
		{
			$data_roll_array=sql_select("select id, po_breakdown_id, qnty,roll_no from pro_roll_details where dtls_id='$id' and entry_form=4 and status_active=1 and is_deleted=0");
			foreach($data_roll_array as $row_roll)
			{ 
				if($save_string=="")
				{
					$save_string=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")];
				}
				else
				{
					$save_string.=",".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")];
				}
			}
		}
		else
		{
			$data_po_array=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where dtls_id='$id' and entry_form=7 and status_active=1 and is_deleted=0");
			foreach($data_po_array as $row_po)
			{ 
				if($save_string=="")
				{
					$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
				}
				else
				{
					$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
				}
			}
		}*/
		
		echo "document.getElementById('save_data').value 				= '".$save_string."';\n";
		exit();
	}
}

if ($action=="fabricDescription_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		function js_set_value(comp,gsm,detarmination_id)
		{
			$('#hidden_desc_no').val(comp);
			$('#hidden_gsm').val(gsm);
			$('#fabric_desc_id').val(detarmination_id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:520px;margin-left:10px">
            <input type="hidden" name="hidden_desc_no" id="hidden_desc_no" class="text_boxes" value="">  
            <input type="hidden" name="hidden_gsm" id="hidden_gsm" class="text_boxes" value=""> 
            <input type="hidden" name="hidden_dia_width" id="hidden_dia_width" class="text_boxes" value="">  
            <input type="hidden" name="fabric_desc_id" id="fabric_desc_id" class="text_boxes" value="">  
            
            <div style="margin-left:10px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="480">
                    <thead>
                        <th width="40">SL</th>
                        <th width="120">Construction</th>
                        <th>Composition</th>
                        <th width="100">GSM/Weight</th>
                    </thead>
                </table>
                <div style="width:500px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="480" id="tbl_list_search">  
                        <? 
                        $i=1;
						$data_array=sql_select("select id, construction, fab_nature_id, gsm_weight from lib_yarn_count_determina_mst where fab_nature_id=2 and status_active=1 and is_deleted=0"); 
                        foreach($data_array as $row)
                        {  
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
							
                            $construction=$row[csf('construction')]; $comp='';
							
                            $determ_sql=sql_select("select copmposition_id, percent from lib_yarn_count_determina_dtls where mst_id=$row[id] and status_active=1 and is_deleted=0");
                            foreach( $determ_sql as $d_row )
                            {
                                $comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
                            }
							$cons_comp=$construction.", ".$comp;
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $cons_comp; ?>','<? echo $row[csf('gsm_weight')]; ?>','<? echo $row[csf('id')]; ?>')" style="cursor:pointer" >
                                <td width="40"><? echo $i; ?></td>
                                <td width="120"><p><? echo $construction; ?></p></td>
                                <td><p><? echo $comp; ?></p></td>
                                <td width="100"><? echo $row[csf('gsm_weight')]; ?></td>
                            </tr>
                        <? 
                        $i++; 
                        } 
                        ?>
                    </table>
                </div> 
            </div>
		</fieldset>
	</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
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
		
		$finish_recv_num=''; $finish_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			$new_finish_recv_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FFPE', date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form='37' and YEAR(insert_date)=".date('Y',time())." order by id desc", "recv_number_prefix", "recv_number_prefix_num" ));
		 	
			$id=return_next_id( "id", "inv_receive_master", 1 ) ;
					 
			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, booking_id, booking_no, booking_without_order, receive_date, challan_no, store_id, location_id, knitting_source, knitting_company, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_finish_recv_system_id[1]."',".$new_finish_recv_system_id[2].",'".$new_finish_recv_system_id[0]."',37,2,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_booking_no_id.",".$txt_booking_no.",".$booking_without_order.",".$txt_receive_date.",".$txt_challan_no.",".$cbo_store_name.",".$cbo_location.",".$cbo_dyeing_source.",".$cbo_dyeing_company.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_receive_master (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
			
			$finish_recv_num=$new_finish_recv_system_id[0];
			$finish_update_id=$id;
		}
		else
		{
			$field_array_update="receive_basis*booking_id*booking_no*booking_without_order*receive_date*challan_no*store_id*location_id*knitting_source*knitting_company*updated_by*update_date";
			
			$data_array_update=$cbo_receive_basis."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$txt_receive_date."*".$txt_challan_no."*".$cbo_store_name."*".$cbo_location."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
			
			$finish_recv_num=str_replace("'","",$txt_system_id);
			$finish_update_id=str_replace("'","",$update_id);
		}
		if (str_replace("'", "", trim($txt_color)) != "") {
			if (!in_array(str_replace("'", "", trim($txt_color)),$new_array_color)){
				$color_id = return_id( str_replace("'", "", trim($txt_color)), $color_arr, "lib_color", "id,color_name","37");
				$new_array_color[$color_id]=str_replace("'", "", trim($txt_color));
			}
			else $color_id =  array_search(str_replace("'", "", trim($txt_color)), $new_array_color);
		} else $color_id = 0;
		//$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		$ItemDesc=str_replace("'","",$txt_fabric_desc).", ".str_replace("'","",$txt_gsm).", ".str_replace("'","",$txt_dia_width);
		if(str_replace("'","",$cbo_receive_basis)==9)
		{
			$batch_id=str_replace("'","",$txt_batch_id);
			$prod_id=str_replace("'","",$product_id);
			
			$cur_st_value=0; $cur_st_rate=0;
			
			$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value";
			$data_array_prod_update="current_stock+".str_replace("'", '',$txt_production_qty)."*".$cur_st_value."*".$cur_st_rate;
			
			$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		else
		{
			if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_no" )==1)
			{
				check_table_status( $_SESSION['menu_id'],0);
				echo "11**0"; 
				die;			
			}
			
			$batch_id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
					 
			$field_array_batch="id, batch_no, entry_form, batch_date, company_id, color_id, batch_weight, inserted_by, insert_date";
			
			$data_array_batch="(".$batch_id.",".$txt_batch_no.",37,".$txt_receive_date.",".$cbo_company_id.",'".$color_id."',".$txt_production_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;die;
			$rIDBatch=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			if($flag==1) 
			{
				if($rIDBatch) $flag=1; else $flag=0;
			} 
			
			if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;
			
			if(str_replace("'","",$cbo_receive_basis)==2 && str_replace("'","",$booking_without_order)==1 && str_replace("'","",$fabric_desc_id)==0)
			{
				$fabric_description=trim(str_replace("'","",$txt_fabric_desc));
				$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and item_description='$fabric_description' and gsm=$txt_gsm and dia_width=$txt_dia_width and color='$color_id' and status_active=1 and is_deleted=0");
			}
			else
			{
				$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and gsm=$txt_gsm and dia_width=$txt_dia_width and color='$color_id' and status_active=1 and is_deleted=0");
			}
		
			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_production_qty);
				$avg_rate_per_unit=0; $stock_value=0;
	
				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$stock_value."*".$color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
			else
			{
				$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
				
				$stock_qnty=$txt_production_qty; $last_purchased_qnty=$txt_production_qty; $avg_rate_per_unit=0; $stock_value=0;
				
				$prod_name_dtls=trim(str_replace("'","",$txt_fabric_desc)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_dia_width));
				$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, gsm, dia_width, inserted_by, insert_date";
				
				$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",2,".$fabric_desc_id.",".$txt_fabric_desc.",'".$prod_name_dtls."',12,".$avg_rate_per_unit.",".$last_purchased_qnty.",".$stock_qnty.",".$stock_value.",".$color_id.",".$txt_gsm.",".$txt_dia_width.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
		}
		
		$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		
		$order_rate=0; $order_amount=0; $cons_rate=0; $cons_amount=0;
		
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, machine_id, rack, self, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$finish_update_id.",".$cbo_receive_basis.",".$batch_id.",".$cbo_company_id.",".$prod_id.",2,1,".$txt_receive_date.",".$cbo_store_name.",12,".$txt_production_qty.",".$order_rate.",".$order_amount.",12,".$txt_production_qty.",".$txt_reject_qty.",".$cons_rate.",".$cons_amount.",".$cbo_machine_name.",".$txt_rack.",".$txt_shelf.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$id_dtls=return_next_id( "id", "pro_finish_fabric_rcv_dtls", 1 ) ;
		
		$rate=0; $amount=0;
		$field_array_dtls="id, mst_id, trans_id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, color_id, receive_qnty, reject_qty, no_of_roll, order_id, buyer_id, machine_no_id, rack_no, shelf_no, inserted_by, insert_date";
		 
		$data_array_dtls="(".$id_dtls.",".$finish_update_id.",".$id_trans.",".$prod_id.",".$batch_id.",".$cbo_body_part.",".$fabric_desc_id.",".$txt_gsm.",".$txt_dia_width.",".$color_id.",".$txt_production_qty.",".$txt_reject_qty.",".$txt_no_of_roll.",".$all_po_id.",".$buyer_id.",".$cbo_machine_name.",".$txt_rack.",".$txt_shelf.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$rID4=sql_insert("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );

		$field_array_batch_dtls="id, mst_id, po_id, item_description, roll_no, batch_qnty, dtls_id, inserted_by, insert_date";
		$id_dtls_batch = return_next_id( "id", "pro_batch_create_dtls", 1 );
		
		$save_string=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_string);$i++)
		{
			$order_dtls=explode("**",$save_string[$i]);
			$order_id=$order_dtls[0];
			$order_qnty=$order_dtls[1];
			
			if($data_array_prop!="" ) $data_array_prop.=",";
			$data_array_prop.="(".$id_prop.",".$id_trans.",1,37,".$id_dtls.",'".$order_id."','".$prod_id."','".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_prop = $id_prop+1;
			
			if(str_replace("'","",$cbo_receive_basis)!=9)
			{
				if($data_array_batch_dtls!="" ) $data_array_batch_dtls.=",";
				
				$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_id."',".$order_id.",'".$prod_name_dtls."','0',".$order_qnty.",".$id_dtls.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_dtls_batch = $id_dtls_batch+1;
			}
		}
		
		if(str_replace("'","",$booking_without_order)==1 && str_replace("'","",$cbo_receive_basis)!=9)
		{
			$data_array_batch_dtls="(".$id_dtls_batch.",'".$batch_id."',0,'".$ItemDesc."',".$txt_no_of_roll.",".$txt_production_qty.",".$id_dtls.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_batch_dtls!="")
		{
			//echo "insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;	
			$rID6=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
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
				echo "0**".$finish_update_id."**".$finish_recv_num."**0";
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
				echo "0**".$finish_update_id."**".$finish_recv_num."**0";
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
		
		$field_array_update="receive_basis*booking_id*booking_no*booking_without_order*receive_date*challan_no*store_id*location_id*knitting_source*knitting_company*updated_by*update_date";
			
		$data_array_update=$cbo_receive_basis."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$txt_receive_date."*".$txt_challan_no."*".$cbo_store_name."*".$cbo_location."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; 
		if (str_replace("'", "", trim($txt_color)) != "") {
			if (!in_array(str_replace("'", "", trim($txt_color)),$new_array_color)){
				$color_id = return_id( str_replace("'", "", trim($txt_color)), $color_arr, "lib_color", "id,color_name","37");
				$new_array_color[$color_id]=str_replace("'", "", trim($txt_color));
			}
			else $color_id =  array_search(str_replace("'", "", trim($txt_color)), $new_array_color);
		} else $color_id = 0;
		//$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		$ItemDesc=str_replace("'","",$txt_fabric_desc).", ".str_replace("'","",$txt_gsm).", ".str_replace("'","",$txt_dia_width);
		
		$stock= return_field_value("current_stock","product_details_master","id=$previous_prod_id");
		$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
		$cur_st_value=0; $cur_st_rate=0;
		
		$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
		$data_array_adjust=$adjust_curr_stock."*".$cur_st_rate."*".$cur_st_value;
		
		$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
		if($flag==1) 
		{
			if($rID_adjust) $flag=1; else $flag=0; 
		} 
		
		$batch_id=str_replace("'","",$txt_batch_id);
		
		if(str_replace("'","",$cbo_receive_basis)==9)
		{
			$prod_id=str_replace("'","",$product_id);
			
			$cur_st_value=0; $cur_st_rate=0;
			
			$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
			$data_array_prod_update="current_stock+".str_replace("'", '',$txt_production_qty)."*".$cur_st_rate."*".$cur_st_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		else
		{
			if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_no and id<>$txt_batch_id" )==1)
			{
				check_table_status( $_SESSION['menu_id'],0);
				echo "11**0"; 
				die;			
			}
					 
			$field_array_batch="batch_no*batch_date*color_id*batch_weight*updated_by*update_date";
			$data_array_batch=$txt_batch_no."*".$txt_receive_date."*'".$color_id."'*".$txt_production_qty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rIDBatch=sql_update("pro_batch_create_mst",$field_array_batch,$data_array_batch,"id",$txt_batch_id,0);
			if($flag==1) 
			{
				if($rIDBatch) $flag=1; else $flag=0; 
			} 
			
			$delete_batch_dtls=execute_query( "delete from pro_batch_create_dtls where mst_id=$txt_batch_id",0);
			if($flag==1) 
			{
				if($delete_batch_dtls) $flag=1; else $flag=0; 
			} 
			
			if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;
			
			if(str_replace("'","",$cbo_receive_basis)==2 && str_replace("'","",$booking_without_order)==1 && str_replace("'","",$fabric_desc_id)==0)
			{
				$fabric_description=trim(str_replace("'","",$txt_fabric_desc));
				$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and item_description='$fabric_description' and gsm=$txt_gsm and dia_width=$txt_dia_width and color='$color_id' and status_active=1 and is_deleted=0");
			}
			else
			{
				$row_prod=sql_select("select id, current_stock from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and gsm=$txt_gsm and dia_width=$txt_dia_width and color='$color_id' and status_active=1 and is_deleted=0");
			}
			
			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_production_qty);
				$avg_rate_per_unit=0; $stock_value=0;
	
				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$stock_value."*".$color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
			else
			{
				$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
				$stock_qnty=$txt_production_qty; $last_purchased_qnty=$txt_production_qty; $avg_rate_per_unit=0; $stock_value=0;
				
				$prod_name_dtls=trim(str_replace("'","",$txt_fabric_desc)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_dia_width));
				$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, gsm, dia_width, inserted_by, insert_date";
				
				$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",2,".$fabric_desc_id.",".$txt_fabric_desc.",'".$prod_name_dtls."',12,".$avg_rate_per_unit.",".$last_purchased_qnty.",".$stock_qnty.",".$stock_value.",".$color_id.",".$txt_gsm.",".$txt_dia_width.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
		}
		
		$order_rate=0; $order_amount=0; $cons_rate=0; $cons_amount=0;
		$field_array_trans_update="receive_basis*pi_wo_batch_no*prod_id*transaction_date*store_id*order_qnty*order_rate*order_amount*cons_quantity*cons_reject_qnty*cons_rate*cons_amount*machine_id*rack*self*updated_by*update_date";
		
		$data_array_trans_update=$cbo_receive_basis."*'".$batch_id."'*".$prod_id."*".$txt_receive_date."*".$cbo_store_name."*".$txt_production_qty."*".$order_rate."*".$order_amount."*".$txt_production_qty."*".$txt_reject_qty."*".$cons_rate."*".$cons_amount."*".$cbo_machine_name."*".$txt_rack."*".$txt_shelf."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID3=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$rate=0; $amount=0;
		$field_array_dtls_update="prod_id*batch_id*body_part_id*fabric_description_id*gsm*width*color_id*receive_qnty*reject_qty*no_of_roll*order_id*buyer_id*machine_no_id*rack_no*shelf_no*updated_by*update_date";
		
		$data_array_dtls_update=$prod_id."*'".$batch_id."'*".$cbo_body_part."*".$fabric_desc_id."*".$txt_gsm."*".$txt_dia_width."*".$color_id."*".$txt_production_qty."*".$txt_reject_qty."*".$txt_no_of_roll."*".$all_po_id."*".$buyer_id."*".$cbo_machine_name."*".$txt_rack."*".$txt_shelf."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID4=sql_update("pro_finish_fabric_rcv_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=37",0);
		if($flag==1) 
		{
			if($delete_prop) $flag=1; else $flag=0; 
		}
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );

		$field_array_batch_dtls="id, mst_id, po_id, item_description, roll_no, batch_qnty, dtls_id, inserted_by, insert_date";
		$id_dtls_batch = return_next_id( "id", "pro_batch_create_dtls", 1 );
		
		$save_string=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_string);$i++)
		{
			$order_dtls=explode("**",$save_string[$i]);
			$order_id=$order_dtls[0];
			$order_qnty=$order_dtls[1];
			
			if($data_array_prop!="" ) $data_array_prop.=",";
			
			$data_array_prop.="(".$id_prop.",".$update_trans_id.",1,37,".$update_dtls_id.",'".$order_id."','".$prod_id."','".$color_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_prop = $id_prop+1;
			
			if(str_replace("'","",$cbo_receive_basis)!=9)
			{
				if($data_array_batch_dtls!="" ) $data_array_batch_dtls.=",";
				$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_id."',".$order_id.",'".$ItemDesc."','0',".$order_qnty.",".$update_dtls_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_dtls_batch = $id_dtls_batch+1;
			}
		}
		
		if(str_replace("'","",$booking_without_order)==1 && str_replace("'","",$cbo_receive_basis)!=9)
		{
			$data_array_batch_dtls="(".$id_dtls_batch.",'".$batch_id."',0,'".$ItemDesc."','0',".$txt_production_qty.",".$update_dtls_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_batch_dtls!="")
		{
			//echo "11**0**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;	
			$rID6=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
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
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
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
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
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

//====================SYSTEM ID POPUP========
if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{
			$('#hidden_sys_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center" style="width:840px;">
    <form name="searchsystemidfrm"  id="searchsystemidfrm">
        <fieldset style="width:830px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Receive Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up">Please Enter System Id</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                        <input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
                    </td>
                    <td>
						<?
							$search_by_arr=array(1=>"System ID",2=>"Challan No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value, 'create_finish_search_list_view', 'search_div', 'knit_finish_fabric_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <table width="100%" style="margin-top:5px;">
                <tr>
                    <td colspan="5">
                        <div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
                    </td>
                </tr>
            </table>
    	</fieldset>
    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_finish_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

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
		else
			$search_field_cond="and challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	$sql = "select id, recv_number, recv_number_prefix_num, receive_basis, booking_no, knitting_source, knitting_company, receive_date, challan_no, year(insert_date) as year from inv_receive_master where entry_form=37 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond"; 
	//echo $sql;die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="807" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="70">Received ID</th>
            <th width="90">Receive Basis</th>
            <th width="110">WO/PI/Prod. No</th>
            <th width="90">Dyeing Source</th>
            <th width="110">Dyeing Company</th>
            <th width="80">Receive date</th>
            <th width="80">Receive Qnty</th>
            <th>Challan No</th>
        </thead>
	</table>
	<div style="width:825px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="807" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	 
					
                if($row[csf('knitting_source')]==1)
					$dye_comp=$company_arr[$row[csf('knitting_company')]]; 
				else
					$dye_comp=$supllier_arr[$row[csf('knitting_company')]];
				
				$recv_qnty=return_field_value("sum(receive_qnty)","pro_finish_fabric_rcv_dtls","mst_id='$row[id]' and status_active=1 and is_deleted=0");
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="90"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
                    <td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                    <td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                    <td width="110"><p><? echo $dye_comp; ?></p></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" align="right"><? echo number_format($recv_qnty,2); ?>&nbsp;</td>
                    <td><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
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

if($action=='populate_data_from_finish_fabric')
{
	$data_array=sql_select("select id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id from inv_receive_master where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value 			= '".$row[csf("receive_basis")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "set_receive_basis();\n";
		echo "document.getElementById('txt_booking_no_id').value 			= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('booking_without_order').value 		= '".$row[csf("booking_without_order")]."';\n";
		
		if($row[csf("booking_without_order")]==1)
		{
			echo "$('#txt_production_qty').removeAttr('readonly','readonly');\n";
			echo "$('#txt_production_qty').removeAttr('onClick','onClick');\n";	
			echo "$('#txt_production_qty').removeAttr('placeholder','placeholder');\n";		
		}
		else
		{
			echo "$('#txt_production_qty').attr('readonly','readonly');\n";
			echo "$('#txt_production_qty').attr('onClick','openmypage_po();');\n";	
			echo "$('#txt_production_qty').attr('placeholder','Single Click');\n";	
		}
		
		echo "document.getElementById('txt_receive_date').value 			= '".change_date_format($row[csf("receive_date")])."';\n";
		echo "document.getElementById('cbo_dyeing_source').value 			= '".$row[csf("knitting_source")]."';\n";
		
		echo "load_drop_down('requires/knit_finish_fabric_receive_controller', $row[knitting_source]+'_'+$row[company_id], 'load_drop_down_dyeing_com', 'dyeingcom_td' );\n";
		
		echo "document.getElementById('cbo_dyeing_company').value 			= '".$row[csf("knitting_company")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_finish_receive_entry',1);\n";  
		exit();
	}
}

if($action=="show_finish_fabric_listview")
{
	$machine_arr = return_library_array("select id, concat(machine_no,'-',brand) as machine_name from lib_machine_name","id","machine_name");
	$fabric_desc_arr=return_library_array("select id, item_description from product_details_master where item_category_id=2","id","item_description");
	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
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
	}
	
	//$arr=array(0=>$batch_arr,1=>$body_part,2=>$composition_arr,5=>$color_arr,8=>$machine_arr);
	$sql="select id, batch_id, body_part_id, fabric_description_id, gsm, width, color_id, receive_qnty, reject_qty, machine_no_id, prod_id from pro_finish_fabric_rcv_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	$result=sql_select($sql);
	//echo  create_list_view("list_view", "Batch,Body Part,Fabric Description,GSM,Dia / Width,Color, QC Pass Qty, Reject Qty, Machine No", "80,100,150,60,70,80,80,80,100","820","200",0, $sql, "put_data_dtls_part", "id", "'populate_finish_details_form_data'", 0, "batch_id,body_part_id,fabric_description_id,0,0,color_id,0,0,machine_no_id", $arr, "batch_id,body_part_id,fabric_description_id,gsm,width,color_id,receive_qnty,reject_qty,machine_no_id", "requires/knit_finish_fabric_receive_controller",'','0,0,0,0,0,0,2,2,0');
?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
        <thead>
            <th width="80">Batch</th>
            <th width="100">Body Part</th>
            <th width="150">Fabric Description</th>
            <th width="60">GSM</th>
            <th width="70">Dia / Width</th>
            <th width="80">Color</th>
            <th width="80">QC Pass Qty</th>
            <th width="80">Reject Qty</th>
            <th>Machine No</th>
        </thead>
	</table>
	<div style="width:820px; max-height:200px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="list_view">  
        <?
            $i=1;
            foreach($result as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	 
					
                if($row[csf('fabric_description_id')]==0)
					$fabric_desc=$fabric_desc_arr[$row[csf('prod_id')]]; 
				else
					$fabric_desc=$composition_arr[$row[csf('fabric_description_id')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_data_dtls_part(<? echo $row[csf('id')]; ?>,'populate_finish_details_form_data', 'requires/knit_finish_fabric_receive_controller');"> 
                    <td width="80"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
                    <td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
                    <td width="150"><p><? echo $fabric_desc; ?></p></td>
                    <td width="60"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $row[csf('width')]; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
                    <td width="80" align="right"><? echo number_format($row[csf('reject_qty')],2); ?>&nbsp;</td>
                    <td><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?>&nbsp;</p></td>
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

if($action=='populate_finish_details_form_data')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	
	$data_array=sql_select("select a.receive_basis, b.id, b.trans_id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty, b.reject_qty, b.no_of_roll, b.machine_no_id, rack_no, shelf_no, b.order_id, b.buyer_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and b.id='$id' and a.item_category=2 and a.entry_form=37");
	foreach ($data_array as $row)
	{ 
		$buyer_name='';
		$buyer=explode(",",$row[csf('buyer_id')]);
		foreach($buyer as $val )
		{
			if($buyer_name=='') $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
		}
		
		$comp='';
		if($row[csf('fabric_description_id')]==0)
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$row[fabric_description_id]");
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		
		echo "document.getElementById('txt_batch_id').value 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_arr[$row[csf("batch_id")]]."';\n";
		
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_fabric_desc').value 				= '".$comp."';\n";
		echo "document.getElementById('fabric_desc_id').value 				= '".$row[csf("fabric_description_id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia_width').value 				= '".$row[csf("width")]."';\n";
		echo "document.getElementById('txt_production_qty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('txt_reject_qty').value 				= '".$row[csf("reject_qty")]."';\n";
		echo "document.getElementById('txt_no_of_roll').value 				= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('buyer_name').value 					= '".$buyer_name."';\n";
		echo "document.getElementById('buyer_id').value 					= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_machine_name').value 			= '".$row[csf("machine_no_id")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf_no")]."';\n";
		echo "document.getElementById('hidden_receive_qnty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('product_id').value 					= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		
		$save_string='';
		$dataPoArray=sql_select("select po_breakdown_id,quantity from order_wise_pro_details where dtls_id='$id' and entry_form=37 and status_active=1 and is_deleted=0");
		foreach($dataPoArray as $row_po)
		{ 
			if($save_string=="")
			{
				$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
			}
			else
			{
				$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
			}
		}
		
		$order_nos='';
		if($row[csf("order_id")]!="")
		{
			$order_nos=return_field_value("group_concat(po_number)","wo_po_break_down","id in(".$row[csf("order_id")].")");
			echo "get_php_form_data('".$row[csf("order_id")]."', 'load_color', 'requires/knit_finish_fabric_receive_controller' );\n";
		}
		
		echo "document.getElementById('hidden_order_id').value 			= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value 			= '".$order_nos."';\n";
		echo "document.getElementById('save_data').value 				= '".$save_string."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_finish_receive_entry',1);\n";  
		exit();
	}
}

if($action=="roll_maintained")
{
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name='$data' and variable_list=3 and is_deleted=0 and status_active=1"); 
	
	if($roll_maintained==1) $roll_maintained=$roll_maintained; else $roll_maintained=0;
	
	echo "document.getElementById('roll_maintained').value 				= '".$roll_maintained."';\n";
	
	echo "reset_form('finishFabricEntry_1','list_fabric_desc_container','','','set_receive_basis();','cbo_company_id*cbo_receive_basis*txt_production_date*txt_challan_no*roll_maintained');\n";
	
	exit();	
}

if($action=="load_color")
{
	$sql="select c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color_id=c.id and b.po_break_down_id in($data) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";
	//echo $sql;die;
	echo "var str_color = [". substr(return_library_autocomplete( $sql, "color_name" ), 0, -1). "];\n";
	echo "$('#txt_color').autocomplete({
						 source: str_color
					  });\n";
	exit();	
}

?>