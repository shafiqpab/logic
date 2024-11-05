<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data		=$_REQUEST['data'];
$action		=$_REQUEST['action'];
$user_id 	= $_SESSION['logic_erp']["user_id"];


if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);		
	exit();
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	
	if($company_id>0) $disable=1; else $disable=0;
	?> 
	<script>
	
		var selected_id = new Array(); var pi_id='<? echo $pi_id; ?>';
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		var prev_pi_id='<? echo $pi_id; ?>';
		var receive_basis='<? echo $cbo_receive_basis; ?>';
	
		function js_set_value( str) 
		{
			//alert (str);
			var cur_pi_id=$('#txt_pi_id' + str).val();
			if((prev_pi_id=="" || selected_id.length==0) && pi_id=="")
			{
				prev_pi_id=cur_pi_id;
			}
			else
			{
				if(prev_pi_id!=cur_pi_id)
				{
					if(receive_basis==1)
					{
						alert("PI Mix not Allowed");
						return;	
					}
					else
					{
						alert("WO Mix not Allowed");
						return;
					}
				}
			}
			
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
			var barcode_nos = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				barcode_nos += selected_id[i] + ',';
			}
			barcode_nos = barcode_nos.substr( 0, barcode_nos.length - 1 );
			
			$('#hidden_barcode_nos').val( barcode_nos );
			$('#selected_pi_id').val( prev_pi_id );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
		}
	
    </script>
</head>
<body>
<div align="center" style="width:790px;">
	<form name="searchwofrm"  id="searchwofrm">
			<table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                 <thead>
                    <th>Company</th>
                    <th>Supplier</th>
                    <th>PI/WO Number</th>
                    <th>Barcode No</th>
                    <?php if($cbo_receive_basis!=1){?>
                    <th>Booking Type</th>
                    <? } ?>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                        <input type="hidden" name="selected_pi_id" id="selected_pi_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? 
						 	echo create_drop_down( "cbo_company_name", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',$company_id,"load_drop_down( 'yarn_bag_receive_controller',this.value+'_1', 'load_supplier_dropdown', 'supplier_td' );",$disable); ?>        
                    </td>
                    <td id="supplier_td">	
                        <?
							echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
						?> 
                    </td> 
                    <td align="center"> 
                        <input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:120px">
                    </td> 
                    <td align="center"> 
                        <input type="text" name="txt_barcode_no" id="txt_barcode_no" class="text_boxes_numeric" style="width:120px">
                    </td>
                    <td <?php if($cbo_receive_basis==1) echo "style='display:none'"; ?> >
                    <?php
						$case_basis_arr=array(1=>"Yarn Purchase order",2=>"Yarn Dyeing Work Order",3=>"Yarn Dyeing Work Order Without Order");
						if($booking_type>0)
						{
							echo create_drop_down( "cbo_booking_basis",152,$case_basis_arr,'', 0,'',$booking_type,"",1);
						}
						else
						{
							echo create_drop_down( "cbo_booking_basis",152,$case_basis_arr,'', 0,'',"","");
						}
					?>
                    </td>    
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_barcode_no').value+'_'+'<? echo $prevBarCodeNos; ?>'+'_'+'<? echo $cbo_receive_basis; ?>'+'_'+document.getElementById('cbo_booking_basis').value, 'create_barcode_search_list_view', 'search_div', 'yarn_bag_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px" id="search_div"></div>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
	
if($action=="create_barcode_search_list_view")
{
	$data=explode('_',$data);
	//echo "<pre>" . var_dump($data) . "</pre>";
	$receive_basis=$data[5];
	$booking_type=$data[6];
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//echo $receive_basis;die;
	if($receive_basis==1)
	{
		if (trim($data[2])!="") $pi_number=" and a.pi_number like '%".trim($data[2])."%'"; else { $pi_number = ''; }
		$importer_id =$data[0];
		if($data[1]==0) $supplier_id="%%"; else $supplier_id =$data[1];
		if($importer_id==0) { echo "Please Select Company First."; die; }
		
		$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		$supplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		
		$barcode_no =trim($data[3]);
		if($barcode_no!="")
		{
			$barcode_cond=" and b.barcode_no='$barcode_no'";
		}
		
		$prevBarCodeNos =trim($data[4]);
		$prevBarCodeNos_cond="";
		if($prevBarCodeNos!="")
		{
			$prevBarCodeNos_cond=" and b.barcode_no not in($prevBarCodeNos)";
		}
		
		$sql= "select a.id,a.pi_number,a.pi_date,a.source,a.importer_id,a.supplier_id,a.last_shipment_date,a.pi_basis_id, a.currency_id, b.barcode_no, b.wo_pi_id, b.receive_basis_id, b.pi_dtls_id from com_pi_master_details a, com_yarn_bag_sticker_barcode b where a.id=b.wo_pi_id and b.receive_basis_id=1 and a.supplier_id like '$supplier_id' and a.item_category_id=1 and a.importer_id = $importer_id and a.status_active=1 and a.is_deleted=0 $pi_number $barcode_cond $prevBarCodeNos_cond order by a.pi_number";
		$result = sql_select($sql);
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="100">PI</th>
            <th width="80">PI Date</th>
            <th width="80">Importer</th>
            <th width="140">Supplier</th>
            <th width="90">Last Shipment Date</th>
            <th width="90">PI Basis</th>
            <th>Barcode No</th>
        </thead>
	</table>
		<div style="width:750px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">  
        <?
			$scanned_barcode_arr=array();
			$barcodeData=sql_select( "select barcode_no from inv_yarn_bag_receive_barcode where status_active=1 and is_deleted=0");
			foreach ($barcodeData as $row)
			{
				$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			}
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="40">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                            <input type="hidden" name="txt_pi_id" id="txt_pi_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>"/>
						</td>
						<td width="100"><p><? echo $row[csf('pi_number')]; ?></p></td>
                        <td width="80" align="center"><p><? echo change_date_format($row[csf('pi_date')]); ?></p></td>
						<td width="80"><p><? echo $comp[$row[csf('importer_id')]]; ?>&nbsp;</p></td>
						<td width="140"><p><? echo $supplier[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                        <td width="90" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
                        <td width="90"><p><? echo $pi_basis[$row[csf('pi_basis_id')]]; ?></p></td>
						<td><? echo $row[csf('barcode_no')]; ?></td>
					</tr>
					<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
        <?php
	}
	else
	{
		if($booking_type==1)
		{
			
			if (trim($data[2])!="") $booking_no=" and a.wo_number like '%".trim($data[2])."%'"; else { $booking_no = ''; }
			$importer_id =$data[0];
			if($data[1]==0) $supplier_id="%%"; else $supplier_id =$data[1];
			if($importer_id==0) { echo "Please Select Company First."; die; }
			
			$barcode_no =trim($data[3]);
			if($barcode_no!="")
			{
				$barcode_cond=" and b.barcode_no='$barcode_no'";
			}
			
			$prevBarCodeNos =trim($data[4]);
			$prevBarCodeNos_cond="";
			if($prevBarCodeNos!="")
			{
				$prevBarCodeNos_cond=" and b.barcode_no not in($prevBarCodeNos)";
			}

			$sql= "select a.id,a.wo_number,a.wo_basis_id,a.wo_date,a.currency_id,a.company_name,a.supplier_id,a.delivery_date, a.currency_id, b.barcode_no, b.wo_pi_id, b.receive_basis_id, b.pi_dtls_id from wo_non_order_info_mst a, com_yarn_bag_sticker_barcode b where a.id=b.wo_pi_id and b.receive_basis_id=2 and a.supplier_id like '$supplier_id' and a.item_category=1 and a.company_name = $importer_id and a.status_active=1 and a.is_deleted=0 $booking_no $barcode_cond $prevBarCodeNos_cond order by a.wo_number";
			//echo $sql;
			$result = sql_select($sql);
			
			//$sql= "select a.id,a.ydw_no,a.booking_date,a.source,a.company_id,a.supplier_id,a.delivery_date_end,a.ecchange_rate, a.currency, b.barcode_no, b.wo_pi_id, b.receive_basis_id, b.pi_dtls_id from wo_non_order_info_mst a, com_yarn_bag_sticker_barcode b where a.id=b.wo_pi_id and b.receive_basis_id=2 and a.supplier_id like '$supplier_id' and a.item_category_id=1 and a.company_id = $importer_id and a.status_active=1 and a.is_deleted=0 $booking_no $barcode_cond $prevBarCodeNos_cond order by a.ydw_no";
			?>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="100">WO No</th>
                    <th width="80">WO Date</th>
                    <th width="80">Importer</th>
                    <th width="140">Supplier</th>
                    <th width="90">Delivery Date</th>
                    <th width="90">WO Basis</th>
                    <th>Barcode No</th>
                </thead>
            </table>
            <div style="width:750px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">	 
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">  
                <?
                    $scanned_barcode_arr=array();
                    $barcodeData=sql_select( "select barcode_no from inv_yarn_bag_receive_barcode where status_active=1 and is_deleted=0");
                    foreach ($barcodeData as $row)
                    {
                        $scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
                    }
                    $i=1;
                    foreach ($result as $row)
                    {  
                        if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
                        {
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                                <td width="40">
                                    <? echo $i; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                                    <input type="hidden" name="txt_pi_id" id="txt_pi_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>"/>
                                </td>
                                <td width="100"><p><? echo $row[csf('wo_number')]; ?></p></td>
                                <td width="80" align="center"><p><? echo change_date_format($row[csf('wo_date')]); ?></p></td>
                                <td width="80"><p><? echo $comp[$row[csf('company_name')]]; ?>&nbsp;</p></td>
                                <td width="140"><p><? echo $supplier[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                                <td width="90" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
                                <td width="90"><p><? echo $wo_basis[$row[csf('wo_basis_id')]]; ?></p></td>
                                <td><? echo $row[csf('barcode_no')]; ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                    ?>
                </table>
            </div>
		
        <?php
		}
		else if($booking_type==2)
		{
			
			if (trim($data[2])!="") $booking_no=" and a.ydw_no like '%".trim($data[2])."%'"; else { $booking_no = ''; }
			$importer_id =$data[0];
			if($data[1]==0) $supplier_id="%%"; else $supplier_id =$data[1];
			if($importer_id==0) { echo "Please Select Company First."; die; }
			
			$barcode_no =trim($data[3]);
			if($barcode_no!="")
			{
				$barcode_cond=" and b.barcode_no='$barcode_no'";
			}
			
			$prevBarCodeNos =trim($data[4]);
			$prevBarCodeNos_cond="";
			if($prevBarCodeNos!="")
			{
				$prevBarCodeNos_cond=" and b.barcode_no not in($prevBarCodeNos)";
			}

			$sql= "select a.id,a.ydw_no,a.booking_date,a.source,a.company_id,a.supplier_id,a.delivery_date_end,a.ecchange_rate, a.currency, b.barcode_no, b.wo_pi_id, b.receive_basis_id, b.pi_dtls_id from wo_yarn_dyeing_mst a, com_yarn_bag_sticker_barcode b where a.id=b.wo_pi_id and b.receive_basis_id=3 and a.supplier_id like '$supplier_id' and a.entry_form in (41,125) and a.company_id = $importer_id and a.status_active=1 and a.is_deleted=0 $booking_no $barcode_cond $prevBarCodeNos_cond order by a.ydw_no";
			//echo $sql;
			$result = sql_select($sql);
			
			
			?>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="100">WO No</th>
                    <th width="80">WO Date</th>
                    <th width="150">Company Name</th>
                    <th width="140">Supplier</th>
                    <th width="90">Delivery Date</th>
                    <th>Barcode No</th>
                </thead>
            </table>
            <div style="width:750px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">	 
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">  
                <?
                    $scanned_barcode_arr=array();
                    $barcodeData=sql_select( "select barcode_no from inv_yarn_bag_receive_barcode where status_active=1 and is_deleted=0");
                    foreach ($barcodeData as $row)
                    {
                        $scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
                    }
                    $i=1;
                    foreach ($result as $row)
                    {  
                        if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
                        {
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                                <td width="40">
                                    <? echo $i; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                                    <input type="hidden" name="txt_pi_id" id="txt_pi_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>"/>
                                </td>
                                <td width="100"><p><? echo $row[csf('ydw_no')]; ?></p></td>
                                <td width="80" align="center"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
                                <td width="150"><p><? echo $comp[$row[csf('company_id')]]; ?>&nbsp;</p></td>
                                <td width="140"><p><? echo $supplier[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                                <td width="90" align="center"><? echo change_date_format($row[csf('delivery_date_end')]); ?>&nbsp;</td>
                                <td><? echo $row[csf('barcode_no')]; ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                    ?>
                </table>
            </div>
		
        <?php
		}
		else if($booking_type==3)
		{
			
			if (trim($data[2])!="") $booking_no=" and a.ydw_no like '%".trim($data[2])."%'"; else { $booking_no = ''; }
			$importer_id =$data[0];
			if($data[1]==0) $supplier_id="%%"; else $supplier_id =$data[1];
			if($importer_id==0) { echo "Please Select Company First."; die; }
			
			$barcode_no =trim($data[3]);
			if($barcode_no!="")
			{
				$barcode_cond=" and b.barcode_no='$barcode_no'";
			}
			
			$prevBarCodeNos =trim($data[4]);
			$prevBarCodeNos_cond="";
			if($prevBarCodeNos!="")
			{
				$prevBarCodeNos_cond=" and b.barcode_no not in($prevBarCodeNos)";
			}

			$sql= "select a.id,a.ydw_no,a.booking_date,a.source,a.company_id,a.supplier_id,a.delivery_date_end,a.ecchange_rate, a.currency, b.barcode_no, b.wo_pi_id, b.receive_basis_id, b.pi_dtls_id from wo_yarn_dyeing_mst a, com_yarn_bag_sticker_barcode b where a.id=b.wo_pi_id and b.receive_basis_id=4 and a.supplier_id like '$supplier_id' and a.entry_form in (42,114) and a.company_id = $importer_id and a.status_active=1 and a.is_deleted=0 $booking_no $barcode_cond $prevBarCodeNos_cond order by a.ydw_no";
			//echo $sql;
			$result = sql_select($sql);
			?>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="100">WO No</th>
                    <th width="80">WO Date</th>
                    <th width="150">Company Name</th>
                    <th width="140">Supplier</th>
                    <th width="90">Delivery Date</th>
                    <th>Barcode No</th>
                </thead>
            </table>
            <div style="width:750px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">	 
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">  
                <?
                    $scanned_barcode_arr=array();
                    $barcodeData=sql_select( "select barcode_no from inv_yarn_bag_receive_barcode where status_active=1 and is_deleted=0");
                    foreach ($barcodeData as $row)
                    {
                        $scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
                    }
                    $i=1;
                    foreach ($result as $row)
                    {  
                        if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
                        {
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                                <td width="40">
                                    <? echo $i; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
                                    <input type="hidden" name="txt_pi_id" id="txt_pi_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>"/>
                                </td>
                                <td width="100"><p><? echo $row[csf('ydw_no')]; ?></p></td>
                                <td width="80" align="center"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
                                <td width="150"><p><? echo $comp[$row[csf('company_id')]]; ?>&nbsp;</p></td>
                                <td width="140"><p><? echo $supplier[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                                <td width="90" align="center"><? echo change_date_format($row[csf('delivery_date_end')]); ?>&nbsp;</td>
                                <td><? echo $row[csf('barcode_no')]; ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                    ?>
                </table>
            </div>
		
        <?php
		}
	}
	?>
    <table width="750" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%"> 
                    <div style="width:50%; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                        <input type="button" name="close" onClick="fnc_close();" class="formbuttonplasminus" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
	<?
	exit();	
} 

if ($action=="get_pi_data")
{
	$data=explode("**",$data);
	$receive_basis=$data[2];
	$booking_type=$data[3];
	if($receive_basis==1)
	{
		$data_array=sql_select("select id,importer_id,supplier_id,pi_number,currency_id,source from com_pi_master_details where id='$data[0]'");
		foreach ($data_array as $row)
		{
			echo "document.getElementById('cbo_company_name').value = '".$row[csf("importer_id")]."';\n";  
			echo "document.getElementById('cbo_supplier').value = '".$row[csf("supplier_id")]."';\n";  
			echo "document.getElementById('pi_number').value = '".$row[csf("pi_number")]."';\n";  
			echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";  
			echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
			echo "document.getElementById('pi_id').value = '".$row[csf("id")]."';\n";
			
			// return_field_value( $field_name, $table_name, $query_cond, $return_fld_name, $new_conn )
			$ile=return_field_value("standard","variable_inv_ile_standard","source='".$row[csf("source")]."' and company_name='".$row[csf("importer_id")]."' and category=1 and status_active=1 and is_deleted=0");
			echo "document.getElementById('ile_perc').value = '".$ile."';\n";
			
			if($row[csf("currency_id")]==1)
			{
				$exchange_rate=1;
			}
			else
			{
				if($db_type==0)
				{
					$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
				}
				else
				{
					$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
				}
				$exchange_rate=set_conversion_rate($row[csf("currency_id")], $conversion_date );
			}
			echo "document.getElementById('txt_exchange_rate').value = '".$exchange_rate."';\n";
			exit();
		}
	}
	else
	{
		if($booking_type==1)
		{
			//select a.id,a.wo_number,a.wo_basis_id,a.wo_date,a.currency_id,a.company_name,a.supplier_id,a.delivery_date, a.currency_id, b.barcode_no, b.wo_pi_id, b.receive_basis_id, b.pi_dtls_id from wo_non_order_info_mst a
			$data_array=sql_select("select id,company_name,supplier_id,wo_number,currency_id,source from wo_non_order_info_mst where id='$data[0]'");
			foreach ($data_array as $row)
			{
				echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
				echo "document.getElementById('cbo_supplier').value = '".$row[csf("supplier_id")]."';\n";  
				echo "document.getElementById('pi_number').value = '".$row[csf("wo_number")]."';\n";  
				echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";  
				echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
				echo "document.getElementById('pi_id').value = '".$row[csf("id")]."';\n";
				
				// return_field_value( $field_name, $table_name, $query_cond, $return_fld_name, $new_conn )
				$ile=return_field_value("standard","variable_inv_ile_standard","source='".$row[csf("source")]."' and company_name='".$row[csf("company_name")]."' and category=1 and status_active=1 and is_deleted=0");
				echo "document.getElementById('ile_perc').value = '".$ile."';\n";
				
				if($row[csf("currency_id")]==1)
				{
					$exchange_rate=1;
				}
				else
				{
					if($db_type==0)
					{
						$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
					}
					else
					{
						$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
					}
					$exchange_rate=set_conversion_rate($row[csf("currency_id")], $conversion_date );
				}
				echo "document.getElementById('txt_exchange_rate').value = '".$exchange_rate."';\n";
				exit();
			}
		}
		else if($booking_type==2 || $booking_type==3)
		{
		
			$data_array=sql_select("select id,company_id,supplier_id,ydw_no,currency,source,ecchange_rate from wo_yarn_dyeing_mst where id='$data[0]'");
			foreach ($data_array as $row)
			{
				echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
				echo "document.getElementById('cbo_supplier').value = '".$row[csf("supplier_id")]."';\n";  
				echo "document.getElementById('pi_number').value = '".$row[csf("ydw_no")]."';\n";  
				echo "document.getElementById('cbo_currency').value = '".$row[csf("currency")]."';\n";  
				echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
				echo "document.getElementById('pi_id').value = '".$row[csf("id")]."';\n";
				
				// return_field_value( $field_name, $table_name, $query_cond, $return_fld_name, $new_conn )
				$ile=return_field_value("standard","variable_inv_ile_standard","source='".$row[csf("source")]."' and company_name='".$row[csf("company_id")]."' and category=1 and status_active=1 and is_deleted=0");
				echo "document.getElementById('ile_perc').value = '".$ile."';\n";
				echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("ecchange_rate")]."';\n";
				exit();
			}
		}
	
	}
}

if ($action=="populate_pi_id")
{
	$scanned_barcode=return_field_value("barcode_no","inv_yarn_bag_receive_barcode","status_active=1 and is_deleted=0 and barcode_no='".$data."'");
	if($scanned_barcode!="")
	{
		echo "0_0";
		exit();
	}
	 $sticker_information=sql_select("select receive_basis_id,wo_pi_id from com_yarn_bag_sticker_barcode where  barcode_no='".$data."'");
	 foreach($sticker_information as $val)
	 {
		 echo $val[csf("receive_basis_id")]."_".$val[csf("wo_pi_id")];
		 exit();
	 }
	
	//$pi_id=return_field_value("wo_pi_id","com_yarn_bag_sticker_barcode","barcode_no='".$data."'");
	//echo $pi_id;
	//exit();
}

if( $action == 'populate_barcode_data') 
{
	$data=explode('**',$data);
	$barcode_nos=$data[0];
	$exchange_rate=$data[1];
	$i=$data[2];
	$ile_perc=$data[3];
	
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
        $brand_library = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
	
	$data_array=sql_select("select barcode_no, color_id, count_name, composition,composition_percentase, yarn_type, no_of_bag, lot_no, weight_per_con, con_per_bag, bag_weight, rate_perunit,brand from com_yarn_bag_sticker_barcode where barcode_no in($barcode_nos)");
	foreach($data_array as $row)
	{
		$i++;
		$ile_cost = ($ile_perc/100)*$row[csf('rate_perunit')];
		$amount = number_format($row[csf('bag_weight')]*($row[csf('rate_perunit')]+$ile_cost),$dec_place[4],'.',''); 
		$bookCurrency = number_format(($row[csf('rate_perunit')]+$ile_cost)*$exchange_rate*$row[csf('bag_weight')],$dec_place[4],'.','');
		
		?>
		<tr class="general" id="row_<?php echo $i; ?>">
			<td>
				<input type="text" name="lotName[]" id="lotName_<?php echo $i; ?>" value="<? echo $row[csf('lot_no')]; ?>" class="text_boxes" style="width:50px" disabled />
			</td>
			<td>
				<?
					echo create_drop_down("countName_".$i, 85, $count_arr,'', 1, '-Select-',$row[csf('count_name')],"",1,"","","","","","","countName[]"); 
				?>                         
			</td>
			<td>
				<?
					echo create_drop_down( "yarnCompositionItem_".$i,160, $composition,'', 1, '-Select-',$row[csf('composition')],"",1,"","","","","","","yarnCompositionItem[]"); 
				?>    
			</td>
                        <td>
                            <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $row[csf("composition_percentase")];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                        </td>
			<td> 
				<?
					echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",1,"","","","","","","yarnType[]"); 
				?>    
			</td>
			<td>
				<input type="text" name="colorName[]" id="colorName_<?php echo $i; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:50px;" disabled/>
				<input type="hidden" name="colorId[]" id="colorId_<?php echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
			</td>
                        <td>
				<input type="text" name="brand[]" id="brand_<?php echo $i; ?>" value="<? echo $brand_library[$row[csf('brand')]]; ?>" style="width:50px"  class="text_boxes" disabled/>
                                <input type="hidden" name="brandId[]" id="brandId_<?php echo $i; ?>" value="<? echo $row[csf('brand')]; ?>" />
			</td>
			<td>
				<input type="text" name="barCodeNo[]" id="barCodeNo_<?php echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>" style="width:80px"  class="text_boxes_numeric" disabled/>
			</td>
			<td>
				<input type="text" name="conWgt[]" id="conWgt_<?php echo $i; ?>" value="<? echo $row[csf('weight_per_con')]; ?>" class="text_boxes_numeric" style="width:50px;" disabled />
			</td>
			<td>
				<input type="text" name="bagCon[]" id="bagCon_<?php echo $i; ?>" value="<? echo $row[csf('con_per_bag')]; ?>" class="text_boxes_numeric" style="width:45px;" disabled />
			</td>
			<td>
				<input type="text" name="bagWgt[]" id="bagWgt_<?php echo $i;?>" value="<? echo $row[csf('bag_weight')];?>" class="text_boxes_numeric" style="width:50px;" disabled/>
			</td>
			<td> 
				<input type="text" name="rate[]" id="rate_<?php echo $i; ?>" value="<? echo $row[csf('rate_perunit')]; ?>" class="text_boxes_numeric" style="width:50px;" disabled/></td>
			<td> 
				<input type="text" name="ilecost[]" id="ilecost_<?php echo $i; ?>" class="text_boxes_numeric" value="<? echo $ile_cost; ?>" style="width:40px;"  disabled/></td>
			<td> 
				<input type="text" name="amount[]" id="amount_<?php echo $i; ?>" class="text_boxes_numeric" value="<? echo $amount; ?>" style="width:50px;" disabled/>	</td>
			<td> 
				<input type="text" name="bookcurrency[]" id="bookcurrency_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $bookCurrency;?>" style="width:60px;" disabled/>	
			</td>  
			<td width="65">
				<input type="button" id="decrease_<?php echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?php echo $i; ?>);" />
			</td> 
		</tr>
	<?
	}
	exit();
}


if($action=="mrr_popup_info")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrrID)
	{
 		var splitArr = mrrID.split("_");
 		$("#hidden_recv_id").val(splitArr[0]); 		// id number
		$("#hidden_recv_number").val(splitArr[1]); 	// mrr number
		parent.emailwindow.hide();
	}
	
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="880" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>   
                	<th width="130">Company</th>             	 
                    <th width="130">Supplier</th>
                    <th width="120">Search By</th>
                    <th width="160" align="center" id="search_by_td_up">Enter MRR Number</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">
                    	 <? 
						 	echo create_drop_down( "cbo_company_name", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',$company_id,"load_drop_down( 'yarn_bag_receive_controller',this.value+'_1', 'load_supplier_dropdown', 'supplier_td' );",$disable); ?>        
                    </td>
                    <td id="supplier_td">	
                        <?
							echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
						?> 
                    </td> 
                    <td>
                        <?  
                            $search_by = array(1=>'MRR No',2=>'Challan No');
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:160px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_mrr_search_list_view', 'search_div', 'yarn_bag_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="6">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_recv_number" value="" />
                     <input type="hidden" id="hidden_recv_id" value="" />
                    <!-- ---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

exit;
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	//echo $data;
	$company = $ex_data[0];
	$supplier = $ex_data[1];
	$txt_search_by = $ex_data[2];
	$txt_search_common = $ex_data[3];
	$fromDate = $ex_data[4];
	$toDate = $ex_data[5];
	
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.recv_number LIKE '%$txt_search_common'";	
			
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";				
 		}		 
		 
 	} 
	
	if($fromDate!="" && $toDate!="" ) 
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.receive_date between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
		}
	}
	
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	if($ex_data[1]==0) $supplier="%%"; else $supplier =$ex_data[1];
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$sql = "select a.id, $year_field a.recv_number_prefix_num, a.recv_number, a.supplier_id, a.challan_no,a.receive_date,a.receive_basis from inv_transaction b, inv_receive_master a  where a.id=b.mst_id and a.entry_form=1 and a.supplier_id like '$supplier' and b.item_category=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_bag_receive=1 $sql_cond group by a.id, a.recv_number_prefix_num, a.recv_number, a.supplier_id, a.challan_no, a.receive_date, a.receive_basis, a.insert_date order by a.id";
	//echo $sql;
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(2=>$supplier_arr,5=>$receive_basis_arr);
	echo create_list_view("list_view", "Year, MRR No, Supplier Name, Challan No, Receive Date, Receive Basis","60,100,140,140,120,100","870","260",0, $sql , "js_set_value", "id,recv_number,recv_number_prefix_num", "", 1,"0,0,supplier_id,0,0,receive_basis", $arr, "year,recv_number_prefix_num,supplier_id,challan_no,receive_date,receive_basis", "",'','0,0,0,0,3,0') ;	
	exit();
	
}

if($action=="populate_data_from_data")
{
	$ex_data = explode("_",$data);
	$mrrNo = $ex_data[0];
	$rcvID = $ex_data[1];
	
	$sql = "select id,recv_number,company_id,receive_basis,receive_purpose,receive_date,booking_id,challan_no,store_id,supplier_id,exchange_rate,currency_id,source,ile_perc,booking_type from inv_receive_master where id=$rcvID and recv_number='$mrrNo' and entry_form=1";
		
	//echo $sql;

	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#cbo_company_name').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_receive_basis').val(".$row[csf("receive_basis")].");\n";
		echo "$('#booking_type').val(".$row[csf("booking_type")].");\n";
		echo "$('#cbo_receive_purpose').val(".$row[csf("receive_purpose")].");\n";
		echo "$('#txt_receive_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		echo "$('#txt_exchange_rate').val(".$row[csf("exchange_rate")].");\n";
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
		echo "$('#cbo_source').val(".$row[csf("source")].");\n";
		echo "$('#ile_perc').val(".$row[csf("ile_perc")].");\n";
		
		
		if($row[csf("receive_basis")]==1)
		{
			$wopi=return_field_value("pi_number","com_pi_master_details","id=".$row[csf("booking_id")]."");	
		}
		else if($row[csf("receive_basis")]==2)
		{
			if($row[csf("booking_type")]==1)
			{
				$wopi=return_field_value("wo_number","wo_non_order_info_mst ","id=".$row[csf("booking_id")]."");	
			}
			else 
			{
				$wopi=return_field_value("ydw_no","wo_yarn_dyeing_mst ","id=".$row[csf("booking_id")]."");	
			}
		}
		
		echo "$('#pi_number').val('".$wopi."');\n";
		echo "$('#pi_id').val(".$row[csf("booking_id")].");\n";
 	}
	exit();	
}


if( $action == 'recive_details' ) 
{
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$brand_library = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
	$sql ="select id,mst_id,lot_no,count_name,composition,composition_percentase,yarn_type,color_id,barcode_no,weight_per_con,con_per_bag,bag_weight,rate,ile_cost,amount,book_currency,brand from inv_yarn_bag_receive_barcode where mst_id=$data";
	//echo $sql;
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$i++;
		?>
        <tr class="general" id="row_<?php echo $i; ?>">
            <td>
                <input type="text" name="lotName[]" id="lotName_<?php echo $i; ?>" value="<? echo $row[csf('lot_no')]; ?>" class="text_boxes" style="width:50px" disabled />
            </td>
            <td>
                <?
                    echo create_drop_down("countName_".$i, 85, $count_arr,'', 1, '-Select-',$row[csf('count_name')],"",1,"","","","","","","countName[]"); 
                ?>                         
            </td>
            <td>
                <?
                    echo create_drop_down( "yarnCompositionItem_".$i,160, $composition,'', 1, '-Select-',$row[csf('composition')],"",1,"","","","","","","yarnCompositionItem[]"); 
                ?>    
            </td>
            <td>
                <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $row[csf("composition_percentase")]?>" style="width:40px"  class="text_boxes" readonly disabled/>
            </td>
            <td> 
                <?
                    echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",1,"","","","","","","yarnType[]"); 
                ?>    
            </td>
            <td>
                <input type="text" name="colorName[]" id="colorName_<?php echo $i; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:50px;" disabled/>
                <input type="hidden" name="colorId[]" id="colorId_<?php echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
            </td>
            <td>
             <input type="text" name="brand[]" id="brand_<?php echo $i; ?>" value="<? echo $brand_library[$row[csf('brand')]]; ?>" style="width:50px"  class="text_boxes" disabled/>
             <input type="hidden" name="brandId[]" id="brandId_<?php echo $i; ?>" value="<? echo $row[csf('brand')]; ?>" />
            </td>
            <td>
                <input type="text" name="barCodeNo[]" id="barCodeNo_<?php echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>" style="width:80px"  class="text_boxes_numeric" disabled/>
            </td>
            <td>
                <input type="text" name="conWgt[]" id="conWgt_<?php echo $i; ?>" value="<? echo $row[csf('weight_per_con')]; ?>" class="text_boxes_numeric" style="width:50px;" disabled />
            </td>
            <td>
                <input type="text" name="bagCon[]" id="bagCon_<?php echo $i; ?>" value="<? echo $row[csf('con_per_bag')]; ?>" class="text_boxes_numeric" style="width:45px;" disabled />
            </td>
            <td>
                <input type="text" name="bagWgt[]" id="bagWgt_<?php echo $i; ?>" value="<? echo $row[csf('bag_weight')];?>" class="text_boxes_numeric" style="width:50px;" disabled/>
            </td>
            <td> 
                <input type="text" name="rate[]" id="rate_<?php echo $i; ?>" value="<? echo $row[csf('rate')]; ?>" class="text_boxes_numeric" style="width:50px;" disabled/></td>
            <td> 
                <input type="text" name="ilecost[]" id="ilecost_<?php echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('ile_cost')]; ?>" style="width:40px;"  disabled/></td>
            <td> 
                <input type="text" name="amount[]" id="amount_<?php echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:50px;" disabled/>	</td>
            <td> 
                <input type="text" name="bookcurrency[]" id="bookcurrency_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('book_currency')]; ?>" style="width:60px;" disabled/>	
            </td>  
            <td width="65">
                <input type="button" id="decrease_<?php echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?php echo $i; ?>);" />
            </td> 
        </tr>
	<?
	}
	exit();
}


if ($action=="save_update_delete")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
        //$brand_library = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
      
	$prod_id_arr=array(); $stock_arr=array();
	$prodData=sql_select("select id, company_id, supplier_id, lot, yarn_count_id, yarn_comp_type1st, yarn_type, color, current_stock, stock_value, allocated_qnty, available_qnty, last_purchased_qnty,yarn_comp_percent1st from product_details_master where company_id=$cbo_company_name and supplier_id=$cbo_supplier and item_category_id=1 and status_active=1 and is_deleted=0");
	foreach($prodData as $row)
	{
		$desc=$row[csf('company_id')]."_".$row[csf('supplier_id')]."_".$row[csf('lot')]."_".$row[csf('yarn_count_id')]."_".$row[csf('yarn_comp_type1st')]."_".$row[csf('yarn_type')]."_".$row[csf('color')]."_".$row[csf('yarn_comp_percent1st')];
		$prod_id_arr[$desc]=$row[csf('id')];
		$stock_arr[$row[csf('id')]][1]=$row[csf('current_stock')];
		$stock_arr[$row[csf('id')]][2]=$row[csf('stock_value')];
		$stock_arr[$row[csf('id')]][3]=$row[csf('allocated_qnty')];
		$stock_arr[$row[csf('id')]][4]=$row[csf('available_qnty')];
		$stock_arr[$row[csf('id')]][5]=$row[csf('last_purchased_qnty')];
	}
	
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
			
		$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";

		$new_rcv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_name,'GPE',1,date("Y",time()),1 ));
		
		$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, receive_basis, entry_form, item_category, challan_no, receive_date, company_id, yarn_bag_receive, ile_perc, store_id, receive_purpose, source, booking_id, exchange_rate, currency_id, supplier_id, booking_type, inserted_by, insert_date";
		$data_array="(".$id.",'".$new_rcv_number[1]."','".$new_rcv_number[2]."','".$new_rcv_number[0]."',".$cbo_receive_basis.",1,1,".$txt_challan_no.",".$txt_receive_date.",".$cbo_company_name.",1,".$ile_perc.",".$cbo_store_name.",".$cbo_receive_purpose.",".$cbo_source.",".$pi_id.",".$txt_exchange_rate.",".$cbo_currency.",".$cbo_supplier.",".$booking_type.",".$user_id.",'".$pc_date_time."')";
		 
		
		$field_array_barcode="id, mst_id, lot_no, count_name, composition,composition_percentase, yarn_type, color_id,brand, barcode_no, weight_per_con, con_per_bag, bag_weight, rate, ile_cost, amount, book_currency, inserted_by, insert_date";
		
		$dataDtlsArr=array(); $colorArr=array(); $update_data=array();
		for($i=1; $i<=$tot_row; $i++)
		{	
			$lotName 					= "lotName".$i;
			$countName					= "countName".$i;
			$yarnCompositionItem        = "yarnCompositionItem".$i;
            $yarnCompositionPercentage  = "yarnCompositionPercentage".$i;
			$yarnType					= "yarnType".$i;
			$colorName					= "colorName".$i;
			$colorId 					= "colorId".$i;
                        $brand                                          = "brandId".$i;
			$barCodeNo 					= "barCodeNo".$i;
			$conWgt 					= "conWgt".$i;
			$bagCon						= "bagCon".$i;
			$bagWgt 					= "bagWgt".$i;
			$rate 						= "rate".$i;
			$ilecost					= "ilecost".$i;
			$amount						= "amount".$i;
			$bookcurrency				= "bookcurrency".$i;
			
			$colorArr[$$colorId]=$$colorName;
			$orderAmount				= $$rate*$$bagWgt;//Order Amount without ile
			
			$desc=$$lotName."_".$$countName."_".$$yarnCompositionItem."_".$$yarnType."_".$$colorId."_".$$yarnCompositionPercentage."_".$$brand;
			$dataDtlsArr[$desc][1]+=$$bagWgt;
			$dataDtlsArr[$desc][2]+=$$amount;
			$dataDtlsArr[$desc][3]+=$$bookcurrency;
			$dataDtlsArr[$desc][4]=$$ilecost;
			$dataDtlsArr[$desc][5]+=1;//No of bag
			$dataDtlsArr[$desc][6]=$$bagCon;
			$dataDtlsArr[$desc][7]=$$bagWgt;
			$dataDtlsArr[$desc][8]=$$conWgt;
			$dataDtlsArr[$desc][9]+=$orderAmount;
            $dataDtlsArr[$desc][10]=$$brand;
			
			$id_barcode = return_next_id_by_sequence("INV_YARN_BAG_REC_BARC_PK_SEQ", "inv_yarn_bag_receive_barcode", $con);
			if($data_array_barcode!="") $data_array_barcode.=",";
			$data_array_barcode.="(".$id_barcode.",".$id.",'".$$lotName."','".$$countName."','".$$yarnCompositionItem."','".$$yarnCompositionPercentage."','".$$yarnType."','".$$colorId."','".$$brand."','".$$barCodeNo."','".$$conWgt."','".$$bagCon."','".$$bagWgt."','".$$rate."','".$$ilecost."','".$$amount."','".$$bookcurrency."',".$user_id.",'".$pc_date_time."')";			
		}
		
		
		$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$field_array_prod="id, company_id, supplier_id, item_category_id, product_name_details, unit_of_measure, lot, yarn_count_id, yarn_comp_type1st,yarn_comp_percent1st, yarn_type, color,brand, last_purchased_qnty, current_stock, avg_rate_per_unit, stock_value, allocated_qnty, available_qnty, inserted_by, insert_date";
		
		
		$field_array_trans= "id,mst_id, receive_basis, pi_wo_batch_no, company_id, supplier_id, prod_id, product_code, item_category, transaction_type, transaction_date, store_id,brand_id, order_uom, order_qnty, order_rate, order_ile, order_ile_cost, order_amount, cons_uom, cons_quantity, cons_rate, cons_ile, cons_ile_cost, cons_amount, balance_qnty, balance_amount, no_of_bags, cone_per_bag, weight_per_bag, weight_per_cone,inserted_by,insert_date";
		
		
		$field_array_dtls="id, mst_id, transaction_id, product_id, quantity, rate, amount, inserted_by, insert_date";
		
		foreach($dataDtlsArr as $desc=>$dtls_value)
		{
			$recv_qty=$dtls_value[1];
			$order_amount=$dtls_value[2];
			$con_amount=$dtls_value[3];
                        
			//======== For description compare to previous array start
			$prod_desc_check = array();
			$prod_desc_check = explode("_",$desc);
			$com_id=str_replace("'","",$cbo_company_name);
			$sup_id=str_replace("'","",$cbo_supplier);
			$product_desc = $com_id."_".$sup_id."_".$prod_desc_check[0]."_".$prod_desc_check[1]."_".$prod_desc_check[2]."_".$prod_desc_check[3]."_".$prod_desc_check[4]."_".$prod_desc_check[5];
			//========description compare to previous array end
                        
			if($prod_id_arr[$product_desc]!="")
			{
				$prod_id=$prod_id_arr[$product_desc];
				$stock_qnty=$stock_arr[$prod_id][1];
				$curr_stock_qnty=$stock_qnty+$recv_qty;
				
				$stock_val=$stock_arr[$prod_id][2];
				$curr_stock_val=$stock_val+$con_amount;
				$avg_rate= number_format($curr_stock_val/$curr_stock_qnty,$dec_place[3],'.','');
				
				$allocated_qnty=$stock_arr[$prod_id][3];
				$available_qnty=$stock_arr[$prod_id][4];
				
				if(str_replace("'","",$cbo_receive_purpose)==2)
				{
					$allocated_qnty=$allocated_qnty+$recv_qty;
					$available_qnty = $available_qnty;
				}
				else
				{
					$allocated_qnty=$allocated_qnty;
					$available_qnty = $available_qnty+$recv_qty;
				}
				
				$id_arr[]=$prod_id;
				$update_data[$prod_id] =explode("*",($avg_rate."*".$recv_qty."*".$curr_stock_qnty."*".$curr_stock_val."*'".$allocated_qnty."'*'".$available_qnty."'*".$user_id."*'".$pc_date_time."'"));
			}
			else
			{
				$descArr=explode("_",$desc);
				$prod_name_dtls=$count_arr[$descArr[1]]." ".$composition[$descArr[2]]." ".$descArr[5]." ".$yarn_type[$descArr[3]]." ".$colorArr[$descArr[4]];
				$avg_rate= number_format($con_amount/$recv_qty,$dec_place[3],'.','');
				
				$prodId = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				if($data_array_prod!="") $data_array_prod.=",";
				$data_array_prod.="(".$prodId.",".$cbo_company_name.",".$cbo_supplier.",1,'".$prod_name_dtls."',12,'".$descArr[0]."',".$descArr[1].",".$descArr[2].",".$descArr[5].",".$descArr[3].",".$descArr[4].",".$descArr[6].",".$recv_qty.",".$recv_qty.",".$avg_rate.",".$con_amount.",0,".$recv_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$prod_id=$prodId;
			}
			
			$orderAmount=$dtls_value[9];
			$order_rate= number_format($orderAmount/$recv_qty,$dec_place[3],'.','');// order rate without ile
			$rate= number_format($order_amount/$recv_qty,$dec_place[3],'.','');// order rate with ile
			$cons_rate= number_format($con_amount/$recv_qty,$dec_place[3],'.','');
			
			$ile=str_replace("'","",$ile_perc);
			$ile_cost=$dtls_value[4];
			
			$con_ile = $ile;//($ile/$cons_rate)*100;
			$con_ile_cost = ($ile/100)*($order_rate*str_replace("'","",$txt_exchange_rate));
			
			$no_bag=$dtls_value[5];
			$cone_per_bag=$dtls_value[6];
			$weight_per_bag=$dtls_value[7];
			$weight_per_cone=$dtls_value[8];
                        $brand=$dtls_value[10];
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$id_trans.",".$id.",".$cbo_receive_basis.",".$pi_id.",".$cbo_company_name.",".$cbo_supplier.",".$prod_id.",".$prod_id.",1,1,".$txt_receive_date.",".$cbo_store_name.",".$brand.",12,".$recv_qty.",".$order_rate.",'".$ile."','".$ile_cost."',".$order_amount.",12,".$recv_qty.",".$cons_rate.",'".$con_ile."','".$con_ile_cost."',".$con_amount.",".$recv_qty.",".$con_amount.",'".$no_bag."','".$cone_per_bag."','".$weight_per_bag."','".$weight_per_cone."','".$user_id."','".$pc_date_time."')";
			
			$dtlsId=return_next_id_by_sequence("INV_YB_REC_DETAILS_PK_SEQ", "inv_yarn_bag_receive_details", $con);
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtlsId.",".$id.",".$id_trans.",'".$prod_id."','".$dtls_value[1]."',".$cons_rate.",'".$dtls_value[3]."',".$user_id.",'".$pc_date_time."')";
			
            $all_prod_ids .= $prod_id .",";
		}
		
                //----------Check Receive Date with Issue Date-------------------//
                $prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
                $max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and store_id = $cbo_store_name and status_active = 1 and is_deleted = 0 ", "max_date");      
                    if($max_transaction_date != "")
                    {
                        $max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
                        $receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
                        if ($receive_date < $max_transaction_date) 
                        {
                            echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
                            check_table_status($_SESSION['menu_id'], 0);
                            disconnect($con);
                            die;
                        }
                    } 
                
		//echo "5**"."INSERT INTO inv_yarn_bag_receive_barcode(".$field_array_barcode.") VALUES ".$data_array_barcode; die;
		$rID=sql_insert("inv_receive_master",$field_array,$data_array,1);
		if($rID) $flag=1; else $flag=0;
		
		
		if($flag==1) 
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		if($flag==1)
		{	
			$rID3=sql_insert("inv_yarn_bag_receive_details",$field_array_dtls,$data_array_dtls,1);
			if($rID3) $flag=1; else $flag=0; 
		}
		
		if($data_array_prod!="" && $flag==1)
		{	
			$rID4=sql_insert("product_details_master",$field_array_prod,$data_array_prod,1);
			if($rID4) $flag=1; else $flag=0; 
		}//echo "5**".$flag;die;
		
		if(count($id_arr)>0 && $flag==1)
		{
			$rID5=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$update_data,$id_arr),1);
			if($rID5) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{	
			$rID6=sql_insert("inv_yarn_bag_receive_barcode",$field_array_barcode,$data_array_barcode,1);
			if($rID6) $flag=1; else $flag=0; 
		}
		
		//echo "5**".$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_rcv_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".$new_rcv_number[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "0**".$new_rcv_number[0]."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "5**".$new_rcv_number[0];
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

		/*#### Stop not eligible field from update operation start ####*/
		//store_id*source*currency_id*exchange_rate*supplier_id*
		//".$cbo_store_name."*".$cbo_source."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_supplier."*
		/*#### Stop not eligible field from update operation end ####*/

		$field_arr_mst="challan_no*receive_date*ile_perc*receive_purpose*booking_id*updated_by*update_date"; 
		$data_array_mst=$txt_challan_no."*".$txt_receive_date."*".$ile_perc."*".$cbo_receive_purpose."*".$pi_id."*".$user_id."*'".$pc_date_time."'";
		//echo "10**select a.id, a.prod_id, a.cons_quantity, a.cons_amount, a.balance_qnty, a.balance_amount, b.avg_rate_per_unit, b.current_stock, b.stock_value, b.allocated_qnty, b.available_qnty, c.receive_purpose from inv_transaction a, product_details_master b, inv_receive_master c where a.prod_id=b.id and a.mst_id=c.id and a.mst_id=$update_id and a.item_category=1 and a.transaction_type=1 and c.yarn_bag_receive=1 and a.status_active=1 and a.is_deleted=0";die;
		$sql = sql_select("select a.id, a.prod_id, a.cons_quantity, a.cons_amount, a.balance_qnty, a.balance_amount, b.avg_rate_per_unit, b.current_stock, b.stock_value, b.allocated_qnty, b.available_qnty, c.receive_purpose from inv_transaction a, product_details_master b, inv_receive_master c where a.prod_id=b.id and a.mst_id=c.id and a.mst_id=$update_id and a.item_category=1 and a.transaction_type=1 and c.yarn_bag_receive=1 and a.status_active=1 and a.is_deleted=0");
		$prev_data_arr=array(); $trans_id_arr=array();
		foreach( $sql as $row)
		{
			$trans_id_arr[$row[csf("prod_id")]]=$row[csf("id")]; 
			$prev_data_arr[$row[csf("id")]][1]=$row[csf("prod_id")]; 
			$prev_data_arr[$row[csf("id")]][2]=$row[csf("cons_quantity")]; 
			$prev_data_arr[$row[csf("id")]][3]=$row[csf("cons_amount")]; 
			$prev_data_arr[$row[csf("id")]][4]=$row[csf("balance_qnty")]; 
			$prev_data_arr[$row[csf("id")]][5]=$row[csf("balance_amount")]; 
			
			$stock_arr[$row[csf('prod_id')]][1]=$row[csf('current_stock')]-$row[csf("cons_quantity")];
			$stock_arr[$row[csf('prod_id')]][2]=$row[csf('stock_value')]-$row[csf("cons_amount")];
		
			if($row[csf("receive_purpose")]==2)
			{
				$stock_arr[$row[csf('prod_id')]][3]=$row[csf('allocated_qnty')]-$row[csf("cons_quantity")];
				$stock_arr[$row[csf('prod_id')]][4]=$row[csf('available_qnty')];
			}
			else
			{
				$stock_arr[$row[csf('prod_id')]][3]=$row[csf('allocated_qnty')];
				$stock_arr[$row[csf('prod_id')]][4]=$row[csf('available_qnty')]-$row[csf("cons_quantity")];
			}
		}
		//echo "10**";print_r($trans_id_arr);die;
		
		//$field_array_barcode="id, mst_id, lot_no, count_name, composition, yarn_type, color_id,brand, barcode_no, weight_per_con, con_per_bag, bag_weight, rate, ile_cost, amount, book_currency, inserted_by, insert_date";
		$field_array_barcode="id, mst_id, lot_no, count_name, composition,composition_percentase, yarn_type, color_id,brand, barcode_no, weight_per_con, con_per_bag, bag_weight, rate, ile_cost, amount, book_currency, inserted_by, insert_date";
		
		$dataDtlsArr=array(); $colorArr=array(); $update_data=array();
		for($i=1; $i<=$tot_row; $i++)
		{	
			$lotName 					= "lotName".$i;
			$countName					= "countName".$i;
			$yarnCompositionItem 		= "yarnCompositionItem".$i;
			$yarnType					= "yarnType".$i;
            $yarnCompositionPercentage	= "yarnCompositionPercentage".$i;
			$colorName					= "colorName".$i;
			$colorId 					= "colorId".$i;
            $brand                      = "brandId".$i;
			$barCodeNo 					= "barCodeNo".$i;
			$conWgt 					= "conWgt".$i;
			$bagCon						= "bagCon".$i;
			$bagWgt 					= "bagWgt".$i;
			$rate 						= "rate".$i;
			$ilecost					= "ilecost".$i;
			$amount						= "amount".$i;
			$bookcurrency				= "bookcurrency".$i;
			
			$colorArr[$$colorId]=$$colorName;
			$orderAmount				= $$rate*$$bagWgt;//Order Amount without ile
			
			$desc=$$lotName."_".$$countName."_".$$yarnCompositionItem."_".$$yarnType."_".$$colorId."_".$$yarnCompositionPercentage."_".$$brand;
			$dataDtlsArr[$desc][1]+=$$bagWgt;
			$dataDtlsArr[$desc][2]+=$$amount;
			$dataDtlsArr[$desc][3]+=$$bookcurrency;
			$dataDtlsArr[$desc][4]=$$ilecost;
			$dataDtlsArr[$desc][5]+=1;//No of bag
			$dataDtlsArr[$desc][6]=$$bagCon;
			$dataDtlsArr[$desc][7]=$$bagWgt;
			$dataDtlsArr[$desc][8]=$$conWgt;
			$dataDtlsArr[$desc][9]+=$orderAmount;
            $dataDtlsArr[$desc][10]=$$brand;
			
			$id_barcode = return_next_id_by_sequence("INV_YARN_BAG_REC_BARC_PK_SEQ", "inv_yarn_bag_receive_barcode", $con);  
			if($data_array_barcode!="") $data_array_barcode.=",";
			//$data_array_barcode.="(".$id_barcode.",".$update_id.",'".$$lotName."','".$$countName."','".$$yarnCompositionItem."','".$$yarnType."','".$$colorId."','".$$brand."','".$$barCodeNo."','".$$conWgt."','".$$bagCon."','".$$bagWgt."','".$$rate."','".$$ilecost."','".$$amount."','".$$bookcurrency."',".$user_id.",'".$pc_date_time."')";
			
			$data_array_barcode.="(".$id_barcode.",".$update_id.",'".$$lotName."','".$$countName."','".$$yarnCompositionItem."','".$$yarnCompositionPercentage."','".$$yarnType."','".$$colorId."','".$$brand."','".$$barCodeNo."','".$$conWgt."','".$$bagCon."','".$$bagWgt."','".$$rate."','".$$ilecost."','".$$amount."','".$$bookcurrency."',".$user_id.",'".$pc_date_time."')";
		}
		
		$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$field_array_prod="id, company_id, supplier_id, item_category_id, product_name_details, unit_of_measure, lot, yarn_count_id, yarn_comp_type1st, yarn_type, color,brand, last_purchased_qnty, current_stock, avg_rate_per_unit, stock_value, allocated_qnty, available_qnty, inserted_by, insert_date";
		
		$field_array_trans= "id,mst_id, receive_basis, pi_wo_batch_no, company_id, supplier_id, prod_id, product_code, item_category, transaction_type, transaction_date, store_id,brand_id, order_uom, order_qnty, order_rate, order_ile, order_ile_cost, order_amount, cons_uom, cons_quantity, cons_rate, cons_ile, cons_ile_cost, cons_amount, balance_qnty, balance_amount, no_of_bags, cone_per_bag, weight_per_bag, weight_per_cone,inserted_by,insert_date";
		$field_array_trans_update="pi_wo_batch_no*supplier_id*prod_id*transaction_date*store_id*order_qnty*order_rate*order_ile*order_ile_cost*order_amount*cons_quantity*cons_rate*cons_ile*cons_ile_cost*cons_amount*balance_qnty*balance_amount*no_of_bags*cone_per_bag*weight_per_bag*weight_per_cone*updated_by*update_date";

		$field_array_dtls="id, mst_id, transaction_id, product_id, quantity, rate, amount, inserted_by, insert_date";
		
		foreach($dataDtlsArr as $desc=>$dtls_value)
		{
			$recv_qty=$dtls_value[1];
			$order_amount=$dtls_value[2];
			$con_amount=$dtls_value[3];
			$orderAmount=$dtls_value[9];
			$order_rate= number_format($orderAmount/$recv_qty,$dec_place[3],'.','');// order rate without ile
			$rate= number_format($order_amount/$recv_qty,$dec_place[3],'.','');// order rate with ile
			$cons_rate= number_format($con_amount/$recv_qty,$dec_place[3],'.','');
			//echo "10**".$orderAmount."**".$recv_qty;die;
			$ile=str_replace("'","",$ile_perc);
			$ile_cost=$dtls_value[4];
			
			$con_ile = $ile;//($ile/$cons_rate)*100;
			$con_ile_cost = ($ile/100)*($order_rate*str_replace("'","",$txt_exchange_rate));
			
			$no_bag=$dtls_value[5];
			$cone_per_bag=$dtls_value[6];
			$weight_per_bag=$dtls_value[7];
			$weight_per_cone=$dtls_value[8];
			$brand=$dtls_value[10];

			//======== For description compare to previous array start
			$com_id=str_replace("'","",$cbo_company_name);
			$sup_id=str_replace("'","",$cbo_supplier);
			$prod_desc_check = array();
			$prod_desc_check = explode("_",$desc);
			$product_desc = $com_id."_".$sup_id."_".$prod_desc_check[0]."_".$prod_desc_check[1]."_".$prod_desc_check[2]."_".$prod_desc_check[3]."_".$prod_desc_check[4]."_".$prod_desc_check[5];
                        //========description compare to previous array end
                        
            //echo "10**$product_desc";die;           
                        
			if($prod_id_arr[$product_desc]!="")
			{
				$prod_id=$prod_id_arr[$product_desc];
				$stock_qnty=$stock_arr[$prod_id][1];
				$curr_stock_qnty=$stock_qnty+$recv_qty;
				
				$stock_val=$stock_arr[$prod_id][2];
				$curr_stock_val=$stock_val+$con_amount;
				if($curr_stock_val>0 && $curr_stock_qnty>0)
				{
					$avg_rate= number_format($curr_stock_val/$curr_stock_qnty,$dec_place[3],'.','');
				}
				else
				{
					$avg_rate= 0;
				}
				
				
				$allocated_qnty=$stock_arr[$prod_id][3];
				$available_qnty=$stock_arr[$prod_id][4];
				
				if(str_replace("'","",$cbo_receive_purpose)==2)
				{
					$allocated_qnty=$allocated_qnty+$recv_qty;
					$available_qnty = $available_qnty;
				}
				else
				{
					$allocated_qnty=$allocated_qnty;
					$available_qnty = $available_qnty+$recv_qty;
				}
				if($prod_id!="") $id_arr[]=$prod_id;
				$update_data[$prod_id]=explode("*",($avg_rate."*".$recv_qty."*".$curr_stock_qnty."*".$curr_stock_val."*'".$allocated_qnty."'*'".$available_qnty."'*".$user_id."*'".$pc_date_time."'"));
				//echo "10**$prod_id";die;
				$trnasId=$trans_id_arr[$prod_id];
				
				$bl_qty=$prev_data_arr[$trnasId][4]-$prev_data_arr[$trnasId][2]+$recv_qty;
				$bl_amnt=$prev_data_arr[$trnasId][5]-$prev_data_arr[$trnasId][3]+$con_amount;
				if($trnasId!="") $id_arr_trans[]=$trnasId;
				$field_array_trans_update="pi_wo_batch_no*supplier_id*prod_id*transaction_date*store_id*order_qnty*order_rate*order_ile*order_ile_cost*order_amount*cons_quantity*cons_rate*cons_ile*cons_ile_cost*cons_amount*balance_qnty*balance_amount*no_of_bags*cone_per_bag*weight_per_bag*weight_per_cone*updated_by*update_date";
				$data_array_trans_update[$trnasId]=explode("*",($pi_id."*".$cbo_supplier."*".$prod_id."*".$txt_receive_date."*".$cbo_store_name."*".$recv_qty."*'".$order_rate."'*'".$ile."'*'".$ile_cost."'*".$order_amount."*".$recv_qty."*".$cons_rate."*'".$con_ile."'*'".$con_ile_cost."'*".$con_amount."*'".$bl_qty."'*'".$bl_amnt."'*'".$no_bag."'*'".$cone_per_bag."'*'".$weight_per_bag."'*'".$weight_per_cone."'*".$user_id."*'".$pc_date_time."'"));
			}
			else
			{
				$descArr=explode("_",$desc);
				$prod_name_dtls=$count_arr[$descArr[1]]." ".$composition[$descArr[2]]." ".$descArr[5]." ".$yarn_type[$descArr[3]]." ".$colorArr[$descArr[4]];
				$avg_rate= number_format($con_amount/$recv_qty,$dec_place[3],'.','');
				
				$prodId = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				if($data_array_prod!="") $data_array_prod.=",";
				$data_array_prod.="(".$prodId.",".$cbo_company_name.",".$cbo_supplier.",1,'".$prod_name_dtls."',12,'".$descArr[0]."',".$descArr[1].",".$descArr[2].",".$descArr[3].",".$descArr[4].",".$descArr[5].",".$recv_qty.",".$recv_qty.",".$avg_rate.",".$con_amount.",0,".$recv_qty.",".$user_id.",'".$pc_date_time."')";
				$prod_id=$prodId;
				
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$update_id.",".$cbo_receive_basis.",".$pi_id.",".$cbo_company_name.",".$cbo_supplier.",".$prod_id.",".$prod_id.",1,1,".$txt_receive_date.",".$cbo_store_name.",".$brand.",12,".$recv_qty.",".$order_rate.",'".$ile."','".$ile_cost."',".$order_amount.",12,".$recv_qty.",".$cons_rate.",'".$con_ile."','".$con_ile_cost."',".$con_amount.",".$recv_qty.",".$con_amount.",'".$no_bag."','".$cone_per_bag."','".$weight_per_bag."','".$weight_per_cone."','".$user_id."','".$pc_date_time."')";
			}
			$dtlsId=return_next_id_by_sequence("INV_YB_REC_DETAILS_PK_SEQ", "inv_yarn_bag_receive_details", $con);
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtlsId.",".$update_id.",'".$id_trans."','".$prod_id."','".$dtls_value[1]."',".$cons_rate.",'".$dtls_value[3]."',".$user_id.",'".$pc_date_time."')";

                        
                        $all_prod_ids .= $prod_id.",";
                        $dtlsId .= $dtlsId. ",";
		}

                //----------Check Receive Date with Issue Date-------------------//
                $prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
                $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and store_id = $cbo_store_name mst_id <> $update_id and status_active = 1 and is_deleted = 0", "max_date");      
                    if($max_issue_date != "")
                    {
                        $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
                        $receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
                        if ($receive_date < $max_issue_date) 
                        {
                            echo "20**Receive Date Can not Be Less Than Last Issue Date Of This Lot";
                            check_table_status($_SESSION['menu_id'], 0);
                            disconnect($con);
                            die;
                        }
                    } 
                
                
		$deleteIds='';
		foreach($prev_data_arr as $prevTrans_id=>$value)
		{
			if(!in_array($prevTrans_id,$id_arr_trans))
			{
				$deleteIds.=$prevTrans_id.",";
				
				$prod_id=$prev_data_arr[$prevTrans_id][1];
				
				$curr_stock_qnty=$stock_arr[$prod_id][1];
				$curr_stock_val=$stock_arr[$prod_id][2];
				$allocated_qnty=$stock_arr[$prod_id][3];
				$available_qnty=$stock_arr[$prod_id][4];
				$avg_rate= number_format($curr_stock_val/$curr_stock_qnty,$dec_place[3],'.','');
				$last_purchased_qnty=$stock_arr[$prod_id][5];
				if($prod_id!="")$id_arr[]=$prod_id;
				$update_data[$prod_id]=explode("*",($avg_rate."*".$last_purchased_qnty."*".$curr_stock_qnty."*".$curr_stock_val."*'".$allocated_qnty."'*'".$available_qnty."'*".$user_id."*'".$pc_date_time."'"));
			}
		}


		
		//echo "5**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$id_arr_trans); die;
		//echo "5** insert into inv_yarn_bag_receive_barcode ($field_array_barcode) values $data_array_barcode";die;

		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=$rID7=$rID8=$rID9=$rID10=true;

	
		//echo $field_arr_mst.'<br>';
		//echo $data_array_mst;

		$rID=sql_update("inv_receive_master",$field_arr_mst,$data_array_mst,"id",$update_id,1);

		//echo "6**"."==".$rID;die;

		if($rID) $flag=1; else $flag=0;
		
		$rID2=true; $rID3=true; $rID4=true; $rID5=true; $rID6=true; $rID8=true;
		if($data_array_trans!='' && $flag==1)
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
			if($rID2) $flag=1; else $flag=0; 
		}
		
		
		//echo "6**";print_r($id_arr_trans);die;
		if(count($id_arr_trans)>0 && $flag==1)
		{
			$rID3=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$id_arr_trans),1);

			//echo "6**"."==".$rID;die;
			if($rID3) $flag=1; else $flag=0; 
		}
		
		
		$deleteIds=chop($deleteIds,',');
		if($deleteIds!='' && $flag==1)
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status="'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1";
			$rID4=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$deleteIds,0);
			if($rID4) $flag=1; else $flag=0; 
		}
		
		
		
		//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$update_data,$id_arr);die;
		//echo "10**";print_r($id_arr);die;
		if(count($id_arr)>0 && $flag==1)
		{
			//;
			$rID5=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$update_data,$id_arr),1);
			//echo "10**$rID5";die;
			if($rID5) $flag=1; else $flag=0; 
		}
		
		
		
		if($data_array_prod!="" && $flag==1)
		{		
			$rID6=sql_insert("product_details_master",$field_array_prod,$data_array_prod,1);
			if($rID6) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{		
			$rID7=execute_query("delete from inv_yarn_bag_receive_details where mst_id=$update_id");
			if($rID7) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{		
			$rID8=sql_insert("inv_yarn_bag_receive_details",$field_array_dtls,$data_array_dtls,1);
			if($rID8) $flag=1; else $flag=0; 
		}
		//echo "6**insert into inv_yarn_bag_receive_details ($field_array_dtls) values $data_array_dtls";die;
		if($flag==1)
		{		
			$rID9=execute_query("delete from inv_yarn_bag_receive_barcode where mst_id=$update_id");
			if($rID9) $flag=1; else $flag=0; 
		}
		
		
		
		if($flag==1)
		{	
			$rID10=sql_insert("inv_yarn_bag_receive_barcode",$field_array_barcode,$data_array_barcode,1);
			if($rID10) $flag=1; else $flag=0; 
		}
		
		//echo "6**".$flag."==".$rID."==".$rID2."==".$rID3."==".$rID4."==".$rID5."==".$rID6."==".$rID7."==".$rID8."==".$rID9."==".$rID10;die;
		
		//oci_rollback($con);
		//echo "6**".$flag."**".$data_array_prod;die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $txt_mrr_no)."**".str_replace("'", '', $update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $txt_mrr_no)."**".str_replace("'", '', $update_id);
			}
			else
			{
				oci_rollback($con);
				echo "6**0";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="yarn_bag_receive_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$sql=" select id, recv_number,supplier_id,currency_id,challan_no, receive_date, exchange_rate, store_id, receive_basis,receive_purpose,booking_id from inv_receive_master where id='$data[2]'";
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$brand_arr=return_library_array( "select id,brand_name from lib_brand",'id','brand_name');
?>
	<div id="table_row" style="width:1000px;">
        <table width="950" align="right">
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						<? echo $result[csf('plot_no')]; ?> 
						<? echo $result[csf('level_no')]?>
						<? echo $result[csf('road_no')]; ?> 
						<? echo $result[csf('block_no')];?> 
						<? echo $result[csf('city')];?> 
						<? echo $result[csf('zip_code')]; ?> 
						<?php echo $result[csf('province')];?> 
						<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						<? echo $result[csf('email')];?> 
					    <? echo $result[csf('website')];
					}
                ?> 
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px"><strong><u>Material Receive Report</u></strong></center></td>
            </tr>
            <tr style="font-size:14px">
                <td width="120"><strong>Supplier Name:</strong></td><td width="210px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
                <td width="110"><strong>MRR No:</strong></td><td width="200px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
                <td width="115"><strong>Currency:</strong></td><td ><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            </tr>
            <tr style="font-size:14px">
                <td><strong>Challan No:</strong></td> <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td><strong>Receive Date:</strong></td><td ><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
                <td><strong>Exchange Rate:</strong></td><td ><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
            </tr>
            <tr  =style="font-size:14px">
                <td><strong>Store Name:</strong></td> <td ><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
                <td><strong>Receive Basis:</strong></td><td ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            </tr>
        </table>
             <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:12px">
                <th width="30">SL</th>
                <th width="50">WO No</th>
                <th width="50">PI No</th>
                <th width="140">Item Details</th>
                <th width="60">Yarn Lot</th> 
                <th width="60">Brand Name</th>
                <th width="40">UOM</th>
                <th width="60">Receive Qty</th>
                <th width="50">Rate</th>
                <th width="60">ILE Cost</th>
                <?
               if($dataArray[0][csf('currency_id')]!=1)
			   {
				?>
                <th width="75">Amount(<? echo $currency[$dataArray[0][csf('currency_id')]]; ?>)</th>
                <?
			   }
				?>
                <th width="75">Amount(BDT)</th>
                <th width="50">No. Of Bag</th>
                <th width="50">Cons Per Bag</th>
            </thead>
     <?
	 
	$wopi=return_field_value("a.pi_number as pi_no","com_pi_master_details a,inv_receive_master b","a.id=b.booking_id and b.id=$data[2] and b.entry_form=1 and b.yarn_bag_receive=1","pi_no");	
	$wo_no_sql=sql_select("select id, wo_number FROM wo_non_order_info_mst where status_active =1 and  is_deleted =0 ");
	foreach($wo_no_sql as $vals)
	{
	   $wo_no_arr[$vals[csf("id")]]=$vals[csf("wo_number")];
	}

	
	$cond="";
	if($data[2]!="") $cond .= " and b.mst_id='$data[2]'";
     $i=1;
	$sql_result= sql_select("select a.recv_number, a.receive_basis, a.receive_purpose, b.id,b.brand_id, b.receive_basis, b.pi_wo_batch_no, b.cone_per_bag, c.product_name_details,c.yarn_comp_percent1st,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_type,c.color, c.lot, b.order_uom, b.order_qnty, b.order_rate, b.cons_avg_rate, b.dye_charge, b.order_ile_cost, b.order_amount, b.cons_amount, b.no_of_bags,a.booking_id from inv_receive_master a, inv_transaction b,  product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0 $cond");

			//echo $sql_result;
        $count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
			$total_amt_currency=0;
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_qnty_val_sum +=$row[csf('order_qnty')];
				$order_amount_val_sum +=$row[csf('order_amount')];
				$no_of_bags_val_sum += $row[csf('no_of_bags')];
				$con_per_bags_sum += $row[csf('cone_per_bag')];
                                
                                $yarn_comp_percent1st = $row[csf('yarn_comp_percent1st')];
				?>
            <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px"> 
                <td><? echo $i; ?></td>
                <td><div style="word-wrap:break-word; width:80px"><? echo $wo_no_arr[$row[csf("booking_id")]]; ?></div></td>
                <td><div style="word-wrap:break-word; width:50px"><? echo $wopi; ?></div></td>
                <td>
                    <div style="word-wrap:break-word; width:140px">
                        <? 
                            //echo $row[csf('product_name_details')]; 
                            echo $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$colorArr[$row[csf('color')]];
                        ?>
                    </div>
                </td>
                <td><? echo $row[csf('lot')]; ?></td>
                <td><? echo $brand_arr[$row[csf('brand_id')]]; ?></td>
                <td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                <td align="right"><? echo number_format($row[csf('order_qnty')],2,'.',',')//$row[csf('order_qnty')]; ?></td>
                <td align="right"><? echo number_format($row[csf('order_rate')],2,'.',','); ?></td>
                <td align="right"><? echo $row[csf('order_ile_cost')]; ?></td>
                <?
               if($dataArray[0][csf('currency_id')]!=1)
			   {
				?>
                <td align="right"><? echo number_format(($row[csf('order_amount')]),2,'.',','); 
				$total_amt_currency +=($row[csf('order_amount')]); ?></td>
				<? 
				}
				?>
                <td align="right"><? echo number_format(($row[csf('order_amount')]*$dataArray[0][csf('exchange_rate')]),2,'.',',');
				 $total_bdt_amt_currency +=($row[csf('order_amount')]*$dataArray[0][csf('exchange_rate')]); ?></td>
                <td align="right"><? echo $row[csf('no_of_bags')]; ?></td>
                <td align="right"><? echo $row[csf('cone_per_bag')]; ?></td>
            </tr>
				<?php
				$i++;
			}
		?>
        	<tr> 
                <td align="right" colspan="7" >Total : </td>
                <td align="right"><? echo number_format($order_qnty_val_sum,2,'.',',')  //$total_order_qnty; ?></td>
                <td align="right" colspan="2">&nbsp;</td>
                <?
                 if($dataArray[0][csf('currency_id')]!=1)
			   {
				?>

			    <td align="right"><? echo number_format($order_amount_val_sum,2,'.',','); ?></td>
			    <?
				}
				?>
           	 	<td align="right" colspan="<? echo $colspan ?>" ><? echo number_format($total_bdt_amt_currency,2,'.',','); ?></td>
                <td align="right"><? echo $no_of_bags_val_sum; ?></td>
                <td align="right"><? echo $con_per_bags_sum; ?></td>
			</tr>

        </table>
        <br>
		 <?
            echo signature_table(65, $data[0], "950px");
         ?>
</div>
</div>
<?
exit();
}

?>