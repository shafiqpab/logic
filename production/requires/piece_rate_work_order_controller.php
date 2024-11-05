<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
extract($_REQUEST);

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/piece_rate_work_order_controller', this.value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_rate_for').value, 'load_drop_down_sewing_line', 'sewing_line_td' );","","","","","",3 );     	 
	exit();
}
if ($action=="load_drop_down_sewing_line")
{ 
	$explode_data = explode("_",$data);
	$location = $explode_data[0];
	$company  = $explode_data[1];
	$rate_for = $explode_data[2];

	if ($rate_for == 30) // Rate For Sewing
	{
		// print_r($explode_data);die;
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($company) and variable_list=23 and is_deleted=0 and status_active=1");
		// $txt_date = $explode_data[3];
		// echo $prod_reso_allo; die;
		$cond="";
		if($prod_reso_allo==1)
		{
			$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
			$line_array=array(); 
			if( $company!=0) $cond = " and a.company_id in($company)";
			if($location!=0 ) $cond = " and a.location_id in($location)";
			
			$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number order by a.line_number"); 
			foreach($line_data as $row)
			{
				$line='';
				$line_number=explode(",",$row[csf('line_number')]);
				foreach($line_number as $val)
				{
					if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
				}
				$line_array[$row[csf('id')]]=$line;
			}
			echo create_drop_down( "cbo_line_id", 150,$line_array,"", 1, "-- Select Sewing Line --", $selected, "",0,0 );
		}
		else
		{
			if( $company!=0) $cond = " and company_id in($company)";
			if($location!=0 ) $cond = " and location_name in($location)";
			// if( $company==0 && $location!=0 ) $cond = " and location_name in($location)";
			// if( $company!=0 ) $cond = " and floor_name in($company)";
			// echo "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
			echo create_drop_down( "cbo_line_id", 150, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select Sewing Line --", $selected, "",0,0 );
		}  
	}
	else
	{
		echo create_drop_down( "cbo_line_id", 150, "","", 1, "-- Select Sewing Line --", $selected, "",0,0 );

	}
	
	exit(); 	 
}
if ($action=='load_variable_settings') 
{
	//Piece Rate Work Order & Bill
	$qty_rate_source = sql_select("SELECT id,company_name,variable_list,qty_source_sample,process_wise_rate_source,inserted_by,insert_date,status_active  FROM variable_settings_production where company_name=$data and variable_list=82 and status_active=1 and is_deleted= 0 order by id");
	$qty_source = $qty_rate_source[0]['QTY_SOURCE_SAMPLE'];
	$rate_source = $qty_rate_source[0]['PROCESS_WISE_RATE_SOURCE'];


	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$data and variable_list=23 and is_deleted=0 and status_active=1");
	echo "$('#prod_reso_allo').val('$prod_reso_allo');\n";


	if ($qty_source == $rate_source && $rate_source>0) 
	{
		echo "$('#qty_source').val('$qty_source');\n";
		echo "$('#rate_source').val('$rate_source');\n";
		if ($qty_source == 1 ) 
		{
			echo "$('#txtwoqty_1').attr('onDblClick','openmypage_wo_qty(1)');\n";
			echo "$('#txtwoqty_1').attr('placeholder','Double click to search');\n";
			echo "$('#txtwoqty_1').attr('readonly','readonly');\n";

			echo "$('#txtavgrate_1').removeAttr('onDblClick');\n"; 
			echo "$('#txtavgrate_1').removeAttr('placeholder');\n";
		}
		else if($qty_source == 2 )
		{
			echo "$('#txtwoqty_1').removeAttr('onDblClick');\n"; 
			echo "$('#txtwoqty_1').removeAttr('readonly');\n"; 
			echo "$('#txtwoqty_1').removeAttr('placeholder');\n"; 

			echo "$('#txtavgrate_1').attr('onDblClick','openmypage_avg_rate(1)');\n"; 
			echo "$('#txtavgrate_1').attr('placeholder','Double click to search');\n";
		}
		
	} else //Default
	{
		echo "$('#qty_source').val('1');\n";
		echo "$('#rate_source').val('1');\n";

		echo "$('#txtwoqty_1').attr('onDblClick','openmypage_wo_qty(1)');\n";
		echo "$('#txtwoqty_1').attr('placeholder','Double click to search');\n";
		echo "$('#txtwoqty_1').attr('readonly','readonly');\n";

		echo "$('#txtavgrate_1').removeAttr('onDblClick');\n"; 
		echo "$('#txtavgrate_1').removeAttr('placeholder');\n";
	}
	
}


if ($action=="service_provider_popup")
{
	echo create_drop_down( "txt_service_provider_id", 160, "SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type  in(22,36) and c.tag_company =$data order by supplier_name","id,supplier_name", 1, "-- Select Location --", $selected, "","","","","","",3 );     	 
	exit();
}





//$service_provider_arr=return_library_array("SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type =36 and c.tag_company=$cbo_company_id order by supplier_name",'id','supplier_name');

$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');

$subcon_buyer_arr=return_library_array( "select id,cust_buyer from subcon_ord_dtls where status_active=1 and is_deleted=0 order by cust_buyer",'id','cust_buyer');

//$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
//$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
$company_arr = return_library_array("select id, company_name from lib_company order by company_name","id","company_name");





if($action=="load_details_entry")
{ 

	list($job_str,$order_source_id,$company_id,$OrdRceveCompId,$serial,$qty_rate_source,$rate_for)=explode("**",$data);

	foreach(explode("__",$job_str) as $job_item_po){
		list($job_id,$item_id,$order_id,)=explode("*",$job_item_po);
		$jobArr[$job_id]=$job_id;
		$itemArr[$item_id]=$item_id;
		$poArr[$order_id]=$order_id;
	}
		
	$sql="select a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,b.gmts_item_id,c.id as po_id,c.po_number,c.po_quantity,a.product_dept from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c where a.id=b.job_id and a.id=c.job_id and a.company_name=$company_id and c.id in(".implode(',',$poArr).") and b.gmts_item_id in(".implode(',',$itemArr).")  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $sql; die;
	$sql_result = sql_select($sql);

	// echo "<pre>";print_r($total_wo_qty_arr);die;
	$i=($serial+1);
	foreach($sql_result as $row){ 
		$wo_qty = $row['PO_QUANTITY'];
		$row_id = $i-1;
		$attrCond1 = " placeholder='Double click to search' onDblClick='openmypage_wo_qty($row_id);' readonly";
		$attrCond2 = "";
		$is_disabled = 1;
		if ($qty_rate_source == 2) 
		{
			$product_dept = $row['PRODUCT_DEPT'];
			$item = $row['GMTS_ITEM_ID'];
			$attrCond1 = " placeholder='$wo_qty'";
			$attrCond2 = " placeholder='Double click to search' onDblClick='openmypage_avg_rate($row_id,$product_dept,$item);'";
			$is_disabled = 0;
		}

	?>
		<tr>
			<td align="center">
				<? 
					echo create_drop_down( "cboOrderSource_".$i, 100, $order_source,"", 1, "-- Select --",$order_source_id, "",1 );
				?>
			</td>
			<td>
				<?
					echo create_drop_down( "cboOrdRceveCompId_".$i, 90, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--",$OrdRceveCompId, "" );
				?>
			</td>
			<td>
				<input type="hidden" id="detailsUpdateId_<? echo $i; ?>" name="detailsUpdateId_<? echo $i; ?>" value="" />
				<input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" style="width:100px;" placeholder="Double click to search" readonly disabled onDblClick="openmypage_job_no(1);" value="<? echo $row[csf("job_no")];?>" />
				<input type="hidden" name="txtjobid_<? echo $i; ?>" id="txtjobid_<? echo $i; ?>" value="<? echo $row[csf("id")];?>" />
			</td>
			<td>
				<input type="text" name="txtorderno_<? echo $i; ?>" id="txtorderno_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $row[csf("po_number")];?>" readonly/>
				<input type="hidden" name="txtorderid_<? echo $i; ?>" id="txtorderid_<? echo $i; ?>" value="<? echo $row[csf("po_id")];?>" />
			</td>
			<td>
				<input type="text" name="txtbuyer_<? echo $i; ?>" id="txtbuyer_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $buyer_arr[$row[csf("buyer_name")]];?>" readonly />
				<input type="hidden" name="txtbuyerid_<? echo $i; ?>" id="txtbuyerid_<? echo $i; ?>" value="<? echo $row[csf("buyer_name")];?>" />
			</td>
			<td>
				<input type="text" name="txtitem_<? echo $i; ?>" id="txtitem_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $garments_item[$row[csf("gmts_item_id")]];?>" readonly />
				<input type="hidden" name="txtitemid_<? echo $i; ?>" id="txtitemid_<? echo $i; ?>" value="<? echo $row[csf("gmts_item_id")];?>" />
			</td>
			<td>
				<input type="text" name="txtstyle_<? echo $i; ?>" id="txtstyle_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $row[csf("style_ref_no")];?>" readonly />
			</td>
			<td>
				<? 
				echo create_drop_down( "colortype_".$i, 90, $color_type,"",1, "--Select--", 0,"",0,"" ); 
				?>                                    
			</td>
			<td>
				<input type="text" name="txtwoqty_<? echo $i; ?>" id="txtwoqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px;" <?= $attrCond1 ?> value="<? echo $row[csf("wo_qty")];?>"  />
				<input type="hidden" name="txtOrderQtyHistory_<? echo $i; ?>" id="txtOrderQtyHistory_<? echo $i; ?>" value="<? echo $wo_qty_data; ?>" />
			
			</td>
			<td>
				<? 
				echo create_drop_down( "cbodtlsuom_".$i, 80, $unit_of_measurement,"",1, "--Select--", $row[csf("uom")],"changeUom(this)",$is_disabled,"1,2" ); 
				?>                                    
			</td>
			<td>
				<input type="text" name="txtavgrate_<? echo $i; ?>" id="txtavgrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<? echo $row[csf("avg_rate")];?>" <?= $attrCond2 ?> readonly />

				<input type="hidden" name="txtRate_<?= $i; ?>" id="txtRate_<?= $i; ?>" />

			</td>
			
			<td>
				<input type="text" name="txtdtlamount_<? echo $i; ?>" id="txtdtlamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<? echo $row[csf("amount")];?>" readonly />
			</td>
			
			<td>
				<input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $row[csf("remarks")];?>" />
			</td>
			
			<td align="center">
				<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>)"/>
				<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
			</td>
			
			
		</tr>

	<?
		$i++;
		}
	//---------------------------
	exit();
}


if ($action=="job_no_popup")
{
	echo load_html_head_contents("System ID Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		

	function toggle( x, origColor ) {
		
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	
	var selected_id = new Array;
	function js_set_value(str,id)
	{ 		
		
/*			for( var m = 0; m < str.length; m++ ) {
				var a2=job_ids[m]+order_ids[m]+buyer_ids[m]+item_ids[m];
				if( a1 == a2 )
				{
					alert("Same Job Order and Item Found in this Job");
					return;
					break;
				}
			}
		
*/		 
		 toggle( document.getElementById( 'tr_' + id ), '#FFFFCC' );
		
		if( jQuery.inArray( str, selected_id ) == -1 ) {
			selected_id.push(str);
			
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str ) break;
			}
			selected_id.splice( i, 1 );
		}
			
		
		var jobno='';
		for( var i = 0; i < selected_id.length; i++ ) {
			jobno += selected_id[i] + '__';
		}
			
		jobno = jobno.substr( 0, jobno.length - 2 );
		
		$('#txt_selected_id').val( jobno );
		
			 
	}
			
		
	function close_popup()
	{
		 parent.emailwindow.hide();
	
	}
		
	function fnc_close_popup_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText;
			if(reponse==0){parent.emailwindow.hide();}
			else{alert(reponse+" Item Found in this Job");}
		}
	}
		
		
    </script>
</head>

<body>
<div align="center" style="width:840px;">
    <form name="searchsystemidfrm"  id="searchsystemidfrm">
        <fieldset style="width:830px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Buyer Name</th>
                    <th>Year</th>
                    <th>Buyer Order</th>
                    <th>Style</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
                        <input type="hidden" name="order_source" id="order_source" value="<? echo $order_source; ?>">
					</th>
                </thead>
                <tr>
                    <td align="center">
						<?
							if($order_source==1){
								echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$cbo_company_id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
							}
							else if($order_source==2){
								echo create_drop_down( "cbo_buyer_id", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in ($cbo_company_id))  group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
								
							}
						?>
                    </td>
                    <td align="center">
						<?
							echo create_drop_down( "cbo_year", 80, $year,"", 1, "-- Select --", date("Y",time()+2100), "",0 );
                        ?>
                    </td>
                    <td align="center">
						<input type="text" style="width:100px;" class="text_boxes"  name="txt_buyer_order" id="txt_buyer_order" />
                    </td>
                    <td align="center">
						<input type="text" style="width:180px;" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
                        </td>
                    <td align="center">
                        
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_buyer_order').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('order_source').value+'_'+document.getElementById('cbo_year').value, 'create_job_no_list_view', 'search_div', 'piece_rate_work_order_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                        
                    </td>
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_job_no_list_view")
{
	

	
list($buyer_order,$style_no,$buyer_id,$company_id,$order_source,$cob_year,$buyer_ids,$item_ids)=explode("_",$data);	


if($order_source==1){

	if($buyer_id==0)$buyer_id="a.buyer_name like('%%')"; else $buyer_id="a.buyer_name =$buyer_id";	
	
	if($buyer_order=='')$buyer_order="c.po_number like('%%')"; else $buyer_order="c.po_number like('%".trim($buyer_order)."%')";	
	if($style_no=='')$style_no="a.style_ref_no like('%%')"; else $style_no="a.style_ref_no='$style_no'";	
	
		if($db_type==0)
		{
		
			if($cob_year=='')$cob_year=""; else $cob_year="and year(a.insert_date)='$cob_year'";	
		
			$sql = "select a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,year(a.insert_date) as year,b.gmts_item_id,c.id as po_id,c.po_number,c.po_quantity from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and a.company_name=$company_id and $buyer_id and $buyer_order and $style_no $cob_year and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		}
		else
		{
			if($cob_year=='')$cob_year=""; else $cob_year="and to_char(a.insert_date,'YYYY')='$cob_year'";	
			
			$sql = "select a.id,a.job_no,a.job_no_prefix_num,a.buyer_name,a.style_ref_no,to_char(a.insert_date,'YYYY') as year,b.gmts_item_id,c.id as po_id,c.po_number,c.po_quantity from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and a.company_name=$company_id and $buyer_id and $buyer_order and $style_no $cob_year and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; 
		}
	
}// echo $sql; 

	$result = sql_select($sql);

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="60">Year</th>
            <th width="60">Job No</th>
            <th width="100">Order</th>
            <th width="80">Qty</th>
            <th width="120">Buyer</th>
            <th width="150">Item</th>
            <th>Style</th>
        </thead>
	</table>
	<div style="width:820px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	 
				
        	?>
                <tr id="tr_<? echo $row[csf('id')].$i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('job_no')].'*'.$row[csf('gmts_item_id')].'*'.$row[csf('po_id')]; ?>',<? echo $row[csf('id')].$i; ?>);"> 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
                    <td width="80" align="right"><p><? echo $row[csf('po_quantity')]; ?> &nbsp;</p></td>
                    <td width="120"><p><? if($order_source==1)echo $buyer_arr[$row[csf('buyer_name')]]; else echo $row[csf('buyer_name')]; ?></p></td>
                    <td width="150"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                    <td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                </tr>
        	<?
            $i++;
            }
        	?>
        </table>
    </div>
        <table width="100%">
            <tr>
                <td align="center">
                    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="">
                    <input type="button" value="Close" class="formbutton" onClick="close_popup();" />
                </td>
            </tr>
        </table>
        
<?
exit();
}

if ($action=="wo_qty_popup")
{
	echo load_html_head_contents("Work Order Qty", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");

	
	
	list($job_number,$order_no,$buyer,$item_id,$item,$style,$bill_for,$job_id,$dtls_up_id,$company_id,$order_number)=explode("__",$data);
	
	$data=$job_number."__".$order_no."__".$buyer."__".$item_id."__".$item."__".$style;
		
	
	$slab_rang_list='';
	if($bill_for==20){
		$seftylimitArray= sql_select("select piece_rate_wq_limit from variable_settings_production where company_name='$company_id' and variable_list=29");
		$is_plan_cut_qty=$seftylimitArray[0][csf('piece_rate_wq_limit')];
	}
	else
	{
		$seftyLimitResult= sql_select("select slab_rang_start,slab_rang_end,excess_percent from variable_prod_excess_slab where company_name='$company_id' and variable_list=2");
		foreach($seftyLimitResult as $row){
			if($row[csf('excess_percent')])$slab_rang_list.=$row[csf('slab_rang_start')].','.$row[csf('slab_rang_end')].','.$row[csf('excess_percent')].'__';
		}
	}

	function fnc_slab_range_check($qty)
	{ 
		global $slab_rang_list;
		$slab_rang_data=substr($slab_rang_list,0,-2);
		 
		 $slab_rang_arr=explode('__',$slab_rang_data);
			$flag=0;
			foreach($slab_rang_arr as $slab_range_val){
				list($slab_start,$slab_end,$rate_per)=explode(',',$slab_range_val);
				 if($slab_start <= $qty && $slab_end >= $qty)
				 {
					$flag=1;
					return (($rate_per*$qty*1)/100)+$qty;	 
				 }
			}
			if($flag==0)return $qty; 
			
	}
    //echo fnc_slab_range_check(1000); die;		
		
    ?>
    <script>
    	function js_set_value()
    	{
    		
    		var qty='';	var rate='';var uom='';	var color='';var size='';var oqty='';var wo_qty_uom='';	
    		
    		var total_row=$("#total_row").val();
    		var qty=rate=uom=color=size=wo_qty_uom=0;
    		for(i=1; i<total_row; i++)
    		{
    			
    			
    			if (form_validation('txtrate_'+i+'*cbouom_'+i,'Please Choose Rate*Please Choose UOM.')==false)
    			{
    				if($("#txtwoqty_"+i).val()>0){
    					return;
    				}
    			}
    			else
    			{
    				if(i==1){
    					qty=$("#txtwoqty_"+i).val();
    					rate=$("#txtrate_"+i).val();
    					uom=$("#cbouom_"+i).val();
    					color=$("#txtcolor_"+i).val();
    					size=$("#txtsize_"+i).val();
    					oqty=$("#txtorderqty_"+i).val();
    					wo_qty_uom=$("#txtwo_qty_uom_"+i).val();
    				}
    				else
    				{
    					qty+=','+$("#txtwoqty_"+i).val();
    					rate+=','+$("#txtrate_"+i).val();
    					uom+=','+$("#cbouom_"+i).val();
    					color+=','+$("#txtcolor_"+i).val();
    					size+=','+$("#txtsize_"+i).val();
    					oqty+=','+$("#txtorderqty_"+i).val();
    					wo_qty_uom+=','+$("#txtwo_qty_uom_"+i).val();
    				}
    			}
    		}
    		$("#hidden_qty").val(qty);
    		$("#hidden_rate").val(rate);
    		$("#hidden_uom").val(uom);
    		$("#hidden_color").val(color);
    		$("#hidden_size").val(size);
    		$("#hidden_oqty").val(oqty);
    		$("#hidden_wo_qty_uom").val(wo_qty_uom);
    		parent.emailwindow.hide();
    	}
    	    	
    	function fn_calculate(str,inputType)
    	{ 
    		
    		var upWOQ=$('#updatewoqty_'+str).val()*1;
    		var WOQ=$('#txtorderqty_'+str).val()*1;
    		var BWQ=($('#blcorderqty_'+str).val()*1)+upWOQ;
    		var capacityWoQ=(BWQ)?BWQ:WOQ;
    		var copy_woqty=$('#txtwoqty_'+str).val();	
    		
    		var copy_basis=0;
    		if ($('#copy_basis_1').is(":checked")){
    			copy_basis=$('#copy_basis_1').val();
    		}
    		else if ($('#copy_basis_2').is(":checked")){
    			copy_basis=$('#copy_basis_2').val();
    		}
    		else if ($('#copy_basis_3').is(":checked")){
    			copy_basis=$('#copy_basis_3').val();
    		}
    		
    		
    		
    		if($('#txtwoqty_'+str).val()*1>capacityWoQ)
    		{
    			alert("Access WO Qty Not Allowed Then Order Qty");
    			$('#txtwoqty_'+str).val(capacityWoQ);
    			//return;
    		}
    		
    		
    		
    		
    		if ($('#check_id').is(":checked") && (copy_basis==3))//Size
    		{
    			var val_wo_qty=$('#txtwoqty_'+str).val();
    			var val_rate=$('#txtrate_'+str).val();
    			var uom=$('#cbouom_'+str).val();
    			if(uom==2) var divide_by=12; else var divide_by=1;

    			var copy_size_id=$('#txtsize_'+str).val();
    			
    			var total_row=$('#total_row').val(); 
    			//for(i=str; i<=total_row; i++)
    			for(i=1; i<=total_row; i++)
    			{
    				if($('#txtsize_'+i).val()==copy_size_id)
    				{
    					
    					if ($('#copy_wo_qty').is(":checked") && inputType==1){
    						
    						upWOQ=$('#updatewoqty_'+i).val()*1;
    						vWOQ=$('#txtorderqty_'+i).val()*1;
    						BWQ=($('#blcorderqty_'+i).val()*1)+upWOQ;
    						capacityWoQ=(BWQ)?BWQ:WOQ;
    						if(capacityWoQ < copy_woqty)
    						{
    							alert("Access WO Qty Not Allowed Then Order Qty");
    							$('#txtwoqty_'+i).val(capacityWoQ);
    						}
    						else{
    							$('#txtwoqty_'+i).val(copy_woqty);
    						}
    					}
    					
    					var txt_wo_qty=$('#txtwoqty_'+i).val()/divide_by;
    					txt_wo_qty=Math.round(txt_wo_qty);
    					$('#txtwo_qty_uom_'+i).val(txt_wo_qty);
    					$('#txtrate_'+i).val(val_rate);
    					var values=(txt_wo_qty*val_rate).toFixed(2);
    					$('#txtamount_'+i).val(values);
    				}
    				
    			}
    		  
    		}
    		
    		else if ($('#check_id').is(":checked") && (copy_basis==2))//Color
    		{
    			var val_wo_qty=$('#txtwoqty_'+str).val();
    			var val_rate=$('#txtrate_'+str).val();
    			var uom=$('#cbouom_'+str).val();
    			if(uom==2) var divide_by=12; else var divide_by=1;

    			var copy_color_id=$('#txtcolor_'+str).val();
    			
    			var total_row=$('#total_row').val(); 
    			//for(i=str; i<=total_row; i++)
    			for(i=1; i<=total_row; i++)
    			{
    				if($('#txtcolor_'+i).val()==copy_color_id)
    				{
    					
    					if ($('#copy_wo_qty').is(":checked") && inputType==1){
    					
    						upWOQ=$('#updatewoqty_'+i).val()*1;
    						WOQ=$('#txtorderqty_'+i).val()*1;
    						BWQ=($('#blcorderqty_'+i).val()*1)+upWOQ;
    						capacityWoQ=(BWQ)?BWQ:WOQ;
    						if(capacityWoQ < copy_woqty)
    						{
    							alert("Access WO Qty Not Allowed Then Order Qty");
    							$('#txtwoqty_'+i).val(capacityWoQ);
    						}
    						else{
    							$('#txtwoqty_'+i).val(copy_woqty);
    						}
    					}
    					
    					
    					var txt_wo_qty=$('#txtwoqty_'+i).val()/divide_by;
    					txt_wo_qty=Math.round(txt_wo_qty);
    					$('#txtwo_qty_uom_'+i).val(txt_wo_qty);
    					$('#txtrate_'+i).val(val_rate);
    					var values=(txt_wo_qty*val_rate).toFixed(2);
    					$('#txtamount_'+i).val(values);
    				}
    				
    			}
    		  
    		}
    		
    		else if ($('#check_id').is(":checked") && (copy_basis==1))//All
    		{
    			
    			var val_wo_qty=$('#txtwoqty_'+str).val();
    			var val_rate=$('#txtrate_'+str).val();
    			var uom=$('#cbouom_'+str).val();
    			if(uom==2) var divide_by=12; else var divide_by=1;

    			var total_row=$('#total_row').val(); 
    			//for(i=str; i<=total_row; i++)
    			for(i=1; i<=total_row; i++)
    			{
    				if ($('#copy_wo_qty').is(":checked") && inputType==1){
    					upWOQ=$('#updatewoqty_'+i).val()*1;
    					WOQ=$('#txtorderqty_'+i).val()*1;
    					BWQ=($('#blcorderqty_'+i).val()*1)+upWOQ;
    					capacityWoQ=(BWQ)?BWQ:WOQ;
    					if(capacityWoQ < copy_woqty)
    					{
    						alert("Access WO Qty Not Allowed Then Order Qty");
    						$('#txtwoqty_'+i).val(capacityWoQ);
    					}
    					else{
    						$('#txtwoqty_'+i).val(copy_woqty);
    					}
    					
    				}
    				
    				var txt_wo_qty=$('#txtwoqty_'+i).val()/divide_by;
    				txt_wo_qty=Math.round(txt_wo_qty);
    				$('#txtwo_qty_uom_'+i).val(txt_wo_qty);
    				$('#txtrate_'+i).val(val_rate);
    				var values=(txt_wo_qty*val_rate).toFixed(2);
    				$('#txtamount_'+i).val(values);
    			}
    		  
    		}
    		else
    		{
    			$('#txt_amount_'+str).val(val_wo_qty*val_rate);
    		}
    		
    		
    		
    		fn_get_total(str);
    		fn_copy_uom(str);
    	}
    		
    	function fn_copy_uom(str)
    	{ 
    		
    			
    		var uom=$('#cbouom_'+str).val();
    		var dzn=$( "#cbouom_"+str+" option:selected" ).text();
    		$('#td_wo_qty_uom').text('WO Qty ('+dzn+')');
    		var uom=$('#cbouom_'+str).val();
    		if(uom==2) var divide_by=12; else var divide_by=1;

    		var total_row=$('#total_row').val(); 
    		for(i=1; i<=total_row; i++)
    		{
    			$('#cbouom_'+i).val(uom);
    		
    			var txt_wo_qty=$('#txtwoqty_'+i).val()/divide_by;
    			//txt_wo_qty=Math.round(txt_wo_qty);
    			txt_wo_qty=txt_wo_qty.toFixed(2);
    			$('#txtwo_qty_uom_'+i).val(txt_wo_qty);
    			var val_rate=$('#txtrate_'+i).val();
    			var values=(txt_wo_qty*val_rate).toFixed(2);
    			$('#txtamount_'+i).val(values);
    		
    		}
    		
    		fn_get_total(str);
    	}
    	

    	function fn_get_total(str)
    	{
    		var tot_amount=0;var tot_qty_uom=0;var tot_rate=0;var tot_wo_qty=0;
    		var uom=$('#cbouom_'+str).val();
    		if(uom==2) var divide_by=12; else var divide_by=1;
    		
    		var total_row=$('#total_row').val(); 
    		for(i=1; i<=total_row-1; i++)
    		{
    			var wo_qty=($('#txtwoqty_'+i).val()*1);
    			var qty_uom=($('#txtwo_qty_uom_'+i).val()*1);
    			var rate=$('#txtrate_'+i).val()*1;
    			
    			tot_amount+=(qty_uom*rate);
    			tot_qty_uom+=qty_uom;
    			tot_rate+=rate;
    			tot_wo_qty+=wo_qty;
    			
    		}
    		var totAvgRate=tot_amount/tot_qty_uom;
    		
    		$('#tot_wo_qty').text(Math.round(tot_wo_qty));
    		$('#tot_rate').text(totAvgRate.toFixed(4));
    		
    		//$('#tot_wo_qty_uom').text(Math.round(tot_wo_qty/divide_by));
    		var tot_wo_qty_uom=tot_wo_qty/divide_by;
    		$('#tot_wo_qty_uom').text(tot_wo_qty_uom.toFixed(2));
    		$('#tot_amount').html(tot_amount.toFixed(2)+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
    	}

	
    </script>
    </head>

    <body>
    <div align="center" style="width:850px;">
        <form name="searchbatchnofrm"  id="searchbatchnofrm">
            <fieldset style="width:840px; margin-left:10px">
                <table align="left" cellpadding="7" cellspacing="7">
                    <tr>
                     <?
                    $sql_job=sql_select("select buyer_name from wo_po_details_master  where job_no='$job_number' ");
    				//echo "select buyer_name from wo_po_details_master  where job_no='$job_number' ";
    				?>
                        <td><strong>Job No</strong> :</td><td><? echo $job_number; ?>, </td>
                        <td><strong>Order No</strong> :</td><td><? echo $order_number; ?>, </td>
                         <td><strong>Buyer</strong> :</td><td><? echo $buyer_arr[$sql_job[0][csf('buyer_name')]]; ?>, </td>
                        <td><strong>Item</strong> :</td><td><? echo $item; ?>, </td>
                        <td><strong>Style</strong> :</td><td><? echo $style; ?></td>
                    </tr>
                    <tr>
                        <td colspan="10">
                            <fieldset>
                                <input type="checkbox" id="check_id" value="1" checked /> &nbsp;
                                <input type="radio" name="copy_basis" id="copy_basis_1" checked value="1"> Copy ALL &nbsp;
                                <input type="radio" name="copy_basis" id="copy_basis_2" value="2"> Copy Color Wise &nbsp;
                                <input type="radio" name="copy_basis" id="copy_basis_3" value="3"> Copy Size Wise &nbsp;
                                <input type="checkbox" id="copy_wo_qty" value="1" checked /> Copy Work Order Qty &nbsp;
                            </fieldset>
                        </td>
                    </tr>
                </table>    
                <table cellpadding="0" cellspacing="0" border="1" rules="all" width="835" class="rpt_table">
                    <thead>
                        <th width="50">Sl</th>
                        <th width="150">Color</th>
                        <th width="80">Size</th>
                        <th width="80">Order Qty(Pcs)</th>
                        <th width="80">WO Qty(Pcs)</th>
                        <th width="80">Rate</th>
                        <th width="80">UOM</th>
                        <th width="80" id="td_wo_qty_uom">WO Qty(UOM)</th>
                        <th>Amount</th>
                    </thead>
               </table>
            <div style="width:835px; max-height:260px; overflow-y:scroll">	 
               <table cellpadding="0" cellspacing="0" border="1" rules="all" width="818" class="rpt_table">
                   
      <? 


    	$sql = "SELECT a.rate_for,b.job_id,b.order_id,c.color_id,c.size_id,b.item_id,sum(c.wo_qty) as wo_qty from piece_rate_wo_mst a,piece_rate_wo_dtls b, piece_rate_wo_qty_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.job_id=$job_id and b.order_id='$order_no' and b.item_id=$item_id and a.rate_for=$bill_for and b.status_active=1 and b.is_deleted=0 group by c.color_id,c.size_id,b.item_id,b.order_id,b.job_id,b.order_id,a.rate_for order by c.size_id,b.item_id"; 
    $result = sql_select($sql);
     foreach($result as $rows){
    	$ik=$rows[csf('item_id')].$rows[csf('color_id')].$rows[csf('size_id')];
    	$ow_arr[$ik]=$rows[csf('wo_qty')];
     }

    //var_dump($ow_arr);


    $dtls_up_id=str_replace("'","",$dtls_up_id);
    if($dtls_up_id!=""){
    	$sql = "select color_id,size_id,wo_qty from piece_rate_wo_qty_dtls where dtls_id=$dtls_up_id"; 
    	$result = sql_select($sql);
    	 foreach($result as $rows){
    		$iks=$rows[csf('color_id')].$rows[csf('size_id')];
    		$up_ow_arr[$iks]=$rows[csf('wo_qty')];
    	 }
    }



    if($order_source==1)
    {
	   $sql = "SELECT color_number_id,size_number_id,sum(order_quantity) as order_quantity,sum(plan_cut_qnty) as plan_cut_qnty,item_number_id from wo_po_color_size_breakdown where job_no_mst='$job_number' and po_break_down_id='$order_no' and item_number_id=$item_id and status_active=1 and is_deleted=0 group by color_number_id,size_number_id,item_number_id order by color_number_id,size_number_id"; 
	      //echo $sql; 
    }
    else
    {
	    $sql = "SELECT b.color_id as color_number_id,b.size_id as size_number_id,sum(b.qnty) as order_quantity,sum(b.plan_cut) as plan_cut_qnty,item_id as item_number_id from subcon_ord_mst a,subcon_ord_breakdown b where a.id=b.mst_id and a.subcon_job='$job_number' and b.item_id=$item_id and a.status_active=1 and a.is_deleted=0 group by color_id,size_id,item_id order by b.color_id,b.size_id"; 
    }
	
	$result = sql_select($sql);

	  $i=1;
	  
		$history_data=explode("~~",$search_history);
		list($job_number_h,$order_no_h,$buyer_h,$item_id_h,$item_h,$style_h,$total_row_h)=explode("__",$history_data[0]);
		if($job_number==$job_number_h && $order_no==$order_no_h && $item_id==$item_id_h && $style==$style_h )
		{
		$qty=explode(",",$history_data[2]);
		$rate=explode(",",$history_data[3]);
		$uom=explode(",",$history_data[4]);
		$qty_uom=explode(",",$history_data[5]);
		
		}
 //echo ($job_number.'=='.$job_number_h .'&&'. $order_no.'=='.$order_no_h  .'&&'.  $item_id.'=='.$item_id_h  .'&&'.  $style.'=='.$style_h );
  

	foreach($result as $rows)
    {
        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
	    $indexkey=$item_id.$rows[csf('color_number_id')].$rows[csf('size_number_id')];
	    $upindexkey=$rows[csf('color_number_id')].$rows[csf('size_number_id')];
	    ?>             
                <tr id="tr_<? echo $i; ?>">
                	<td align="center" width="50"><? echo $i; ?></td>
                	<td width="150" align="center">
                        <input type="text" name="txtcolorshow_<? echo $i; ?>" id="txtcolorshow_<? echo $i; ?>" class="text_boxes" style="width:110px;" value="<? echo $color_arr[$rows[csf('color_number_id')]]; ?>" readonly />
                        <input type="hidden" name="txtcolor_<? echo $i; ?>" id="txtcolor_<? echo $i; ?>" value="<? echo $rows[csf('color_number_id')]; ?>" />
                    </td>
                	<td width="80" align="center">
                        <input type="text" name="txtsizeshow_<? echo $i; ?>" id="txtsizeshow_<? echo $i; ?>" class="text_boxes" style="width:60px;" value="<? echo $size_arr[$rows[csf('size_number_id')]]; ?>" readonly />
                        <input type="hidden" name="txtsize_<? echo $i; ?>" id="txtsize_<? echo $i; ?>" value="<? echo $rows[csf('size_number_id')]; ?>" />
                    </td>
                	<td width="80" align="center">
                        <input type="text" name="txtorderqty_<? echo $i; ?>" id="txtorderqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $rows[csf('order_quantity')]; ?>" readonly />
                        <?
						if($is_plan_cut_qty==2 && $bill_for==20){
							$blance=$rows[csf('plan_cut_qnty')]-$ow_arr[$indexkey];
						}
						else if($is_plan_cut_qty==1 && $bill_for==20)
						{
							$blance=$rows[csf('order_quantity')]-$ow_arr[$indexkey];
						}
						else
						{
							$cut_qnty=fnc_slab_range_check($rows[csf('order_quantity')]);
							$blance=$cut_qnty-$ow_arr[$indexkey];	
						}
						?>
                        <input type="hidden" id="blcorderqty_<? echo $i; ?>" value="<? echo $blance;?>" />
                    </td>
                	<td width="80" align="center">
                        <input type="text" name="txtwoqty_<? echo $i; ?>" id="txtwoqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" onKeyUp="fn_calculate(<? echo $i; ?>,1)" value="<? 
						if($qty[$i-1])
						{
							echo $woqty=$qty[$i-1];
						}
						else
						{	
							 echo $woqty=$rows[csf('order_quantity')]-$ow_arr[$indexkey];
						}
						 
						 ?>" />
                          <? $tot_wo_qty+=$woqty; ?>
                          
                        <input type="hidden" id="updatewoqty_<? echo $i; ?>" value="<? echo $up_ow_arr[$upindexkey];?>" />                    </td>
                	<td width="80" align="center">
                        <input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" onKeyUp="fn_calculate(<? echo $i; ?>,2)"  value="<? echo $rate[$i-1];?>"/>
                         <? $tot_rate+=$rate[$i-1]; ?>
                    </td>
                	<td width="80" align="center">
						<? 
						echo create_drop_down( "cbouom_".$i, 70, $unit_of_measurement,"",1, "--Select--", ($uom[$i-1])?$uom[$i-1]:1,"fn_copy_uom($i)",0,"1,2"); ?>
                    </td>
                	<td width="80" align="center">
                        <input type="text" name="txtwo_qty_uom_<? echo $i; ?>" id="txtwo_qty_uom_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $qty_uom[$i-1]; ?>" readonly />
                         <? 
						 if($uom[$i-1]==1) $tot_qty+=$qty[$i-1]/1; else  $tot_qty+=$qty[$i-1]/12; 
						 
						 ?>
                    </td>
                	<td align="center">
                        <input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px;" value="<? echo number_format($qty_uom[$i-1]*$rate[$i-1],2);?>" readonly />
                        <? $tot_amount+=($qty_uom[$i-1]*$rate[$i-1]); ?>
                    </td>
                </tr>
                
            <? $i++;
    } 
    ?>    
        </table>
    </div>
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="835" class="rpt_table">
            	<thead>
                	<th colspan="4" align="center">Total</th>
                    <th width="80" id="tot_wo_qty" style="text-align:right;"><? echo round($tot_wo_qty); ?></th>
                    <th width="80" id="tot_rate" style="text-align:right;" title="<?=$tot_amount."/".$tot_wo_qty;?>"><? echo number_format($tot_rate,2); //$tot_amount/$tot_wo_qty ?></th>
                    <th width="80">&nbsp;</th>
                    <th width="80" id="tot_wo_qty_uom" style="text-align:right;"><? echo round($tot_qty); ?></th>
                    <th width="145" id="tot_amount" style="text-align:right;"><? echo number_format($tot_amount,2); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                </thead>
            </table>
          <input type="hidden" id="total_row" value="<? echo $i; ?>" />
          
          <input type="hidden" id="hidden_qty" value="" />
          <input type="hidden" id="hidden_rate" value="" />
          <input type="hidden" id="hidden_uom" value="" />
          <input type="hidden" id="hidden_color" value="" />
          <input type="hidden" id="hidden_size" value="" />
          <input type="hidden" id="hidden_oqty" value="" />
          <input type="hidden" id="hidden_wo_qty_uom" value="" />
          
          <input type="hidden" id="hidden_up_ids" value="<? echo $history_data[8];?>" />
          <input type="hidden" id="hidden_search_history" value="<? if($search_history)echo $history_data[0]; else echo $data.'__'.($i-1); ?>" />
        </fieldset>
        
        	<input type="button" value="Close" class="formbutton" onClick="js_set_value()">    
        
    </form>
    </div>
    </body>
    <script>
    	var dzn=$( "#cbouom_1 option:selected" ).text();
    	if(dzn!="--Select--")$('#td_wo_qty_uom').text('WO Qty ('+dzn+')');
    </script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}


if ($action=="avg_rate_popup")
{
	echo load_html_head_contents("Avg Rate", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$process_sql = "SELECT id,operation_name from lib_sewing_operation_entry where gmt_item_id=$item and product_dept=$product_dept and status_active=1 and is_deleted=0";
	// echo $process_sql; die;
	$data_arr = sql_select($process_sql);
	$rate_column = 'txtRate_'.$row_id;
	$rate_data = str_replace("'","",$$rate_column) ;
	// echo $rate_data ; 
	$prv_rate_arr = explode('@@',$rate_data);
	$prve_rate_array = array();
	foreach ($prv_rate_arr as $old_rate) 
	{
		$pair = explode ('#', $old_rate);
		$prve_rate_array[$pair[0]] = $pair [1]; 
	}
	// echo $process_sql ; die;
	// echo "<pre>"; print_r($prv_rate_arr); die;
	$width = 260; 
    ?>
    <script>
    	function js_set_value(row_id)
    	{
    		
    		let process_str = '';
    		let total_row=$('#process_body tbody tr').length;

    		for(i=1; i<=total_row; i++)
    		{ 
				process_val = ''
				process_id 	= $("#hidden_process_id_"+i).val();
				qty			= $("#process_qty_"+i).val();
				process_val = process_id +"#"+qty;	
				if(i==1){
					process_str = process_val;
				}
				else
				{
					process_str += '@@'+process_val; 
				}
    		}
			let total_qty = $('#total_process_qty').val(); 
			$('#hidden_process_str').val(process_str); 
    		parent.emailwindow.hide();
    	}
    	function fn_get_total()
    	{
    		let total_qty=0;
    		let total_row=$('#process_body tbody tr').length; 
    		for(i=1; i<=total_row; i++)
    		{
    			process_qty = $('#process_qty_'+i).val()*1;
				qty = isNaN(process_qty) ? 0 : process_qty;
				// console.log(qty);
    			total_qty += qty; 
    		}
			
			$('#total_process_qty').val(total_qty);
    	}

	
    </script>
    </head>

    <body> 
		<form name="searchbatchnofrm"  id="searchbatchnofrm">
				<fieldset style="width:280px;">
					<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
						<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
							<thead class="form_caption" >	  
								<tr>
									<th width="120">Process</th>
									<th width="120">Rate </th> 
								</tr>
							</thead>
						</table>
						<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
							<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="process_body" width="<?= $width; ?>" rules="all" align="left">
								<tbody>
									<?
									$i = 0 ; 
									$total_rate = 0;
									foreach ($data_arr as $v) 
									{   $i++;
										$prev_rate = $prve_rate_array[$v['ID']];
										$total_rate += $prev_rate ;
										if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
										?>
											<tr bgcolor="<?= $bgcolor; ?>" id="tr_1nd<?= $i; ?>">
												<td width="120"> <strong><?= $v['OPERATION_NAME'] ?> </strong> </td>
												<td width="120" align="right">  
													<input onchange="fn_get_total()"  style="width: 110px;" class="text_boxes_numeric" type="text" id="process_qty_<?= $i; ?>" value="<?= $prev_rate ?>" > 
													<input type="hidden" name="hidden_process_id_<?= $i; ?>" id="hidden_process_id_<?= $i; ?>" value="<?= $v['ID'] ?>">
												 </td> 
											</tr> 
										<? 
									}
									?>
								</tbody> 
							</table> 
						</div>
						<div style="width:<?= $width+20;?>px;float:left;">
							<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?= $width;?>">
								<tfoot>
									<tr>
										<th width="120" align="right">Total</th>
										<th width="120"> 
											<input style="width: 110px;" class="text_boxes_numeric" id="total_process_qty"  type="text" readonly value="<?= $total_rate ?>">
											<input type="hidden" name="hidden_process_str" id="hidden_process_str" value="<?= $rate_data ?>">
										
										</th> 
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</fieldset> 
				<div style="text-align: center;">
					<input type="button" value="Close" class="formbutton" onClick="js_set_value(<?=$row_id?>)">  
				</div>
		</form> 
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}




//*********************new end




if($action=="check_conversion_rate") //Conversion Exchange Rate
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();	
}
//====================Location ACTION========

//====================SYSTEM ID POPUP========


if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../", 1, 1,'','','');
?>
	<script>
		function js_set_value(id)
		{ 
			$('#hidden_mst_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
    
    
</head>

<body>
<div align="center" style="width:840px;">
    <form name="searchsystemidfrm"  id="searchsystemidfrm">
        <fieldset style="width:830px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>SYS ID</th>
                    <th>Buyer</th>
                    <th>Order</th>
                    <th>Rate For</th>
                    <th colspan="2">WO Date</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
                        <input type="hidden" id="hidden_mst_id">
					</th>
                </thead>
                <tr>
                    <td align="center">
						<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:100px;" />
                    </td>
                    <td align="center">
						<?
							//echo create_drop_down( "cbo_service_provider_id", 150, $service_provider_arr,"", 1, "-- Select --", 0, "",0 );
							
						echo create_drop_down( "cbo_buyer_name", 120, "select buyer_name,id from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name","id,buyer_name", 1, "--Select Buyer--", 0, "",0 );							
							
                        ?>
                    </td>
                    <td align="center">
                    	<input type="text" id="txt_order" name="txt_order" class="text_boxes" style="width:100px;"/>
                    </td>
                    <td align="center">
						<?
							echo create_drop_down("cbo_rate_for", 100, $rate_for,"", 1,"-- Select --", 0,"","","20,30,35,40");
                        ?>
                    </td>
                    <td align="center">
                     	<input type="text" style="width:100px;" class="datepicker"  name="txt_from_date" id="txt_from_date" readonly />   
                    </td>
                    <td align="center">
                     	<input type="text" style="width:100px;" class="datepicker"  name="txt_to_date" id="txt_to_date" readonly />   
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_system_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order').value+'_'+document.getElementById('cbo_rate_for').value+'_'+document.getElementById('txt_from_date').value+'_'+document.getElementById('txt_to_date').value+'_'+document.getElementById('txt_company_id').value, 'price_rate_list_view', 'search_div', 'piece_rate_work_order_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <table width="100%" style="margin-top:5px;">
                <tr>
                    <td colspan="5">
                        <div style="margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
                    </td>
                </tr>
            </table>
    	</fieldset>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if($action=="price_rate_list_view")
{
list($sysid,$buyer,$order_number,$fill_for,$from_date,$to_date,$company_id)=explode("_",$data);	
	
if($sysid=='')$sysid="a.sys_number like('%%')"; else $sysid="a.sys_number like('%".trim($sysid)."%')";	
if($buyer==0)$buyer="b.buyer_id like('%%')"; else $buyer="b.buyer_id ='$buyer'";	
if($fill_for==0)$fill_for="a.rate_for like('%%')"; else $fill_for="a.rate_for='$fill_for'";	

if($order_number=="")$order_con="c.po_number like('%%')"; else $order_con="c.po_number like('%".$order_number."%')";	

if($from_date!='' && $to_date!=''){	
	if($db_type==0){
		
		$from_date=change_date_format($from_date);
		$to_date=change_date_format($to_date);
	}
	else
	{
		$from_date=change_date_format($from_date,'','',-1);
		$to_date=change_date_format($to_date,'','',-1);
	}
	$date_con="and a.wo_date BETWEEN '$from_date' and '$to_date'";	
}
else
{
	$date_con="";	
}
	

	
	$sql = "select a.id,a.sys_number, a.service_provider_id, a.wo_date, a.rate_for,sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b,wo_po_break_down c where a.id=b.mst_id and b.order_id=c.id and a.company_id=$company_id and $sysid and $buyer and $fill_for $date_con and $order_con and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number, a.service_provider_id, a.wo_date, a.rate_for order by a.id"; 
	$result = sql_select($sql);
	
	
	

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="150">Sys Number</th>
            <th>Service Provider</th>
            <th width="100">WO Qty</th>
            <th width="112">Rate For</th>
        </thead>
	</table>
	<div style="width:815px; max-height:220px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all"  width="797" class="rpt_table" id="tbl_list_search">  
        <?
            
			
			$i=1;
            foreach ($result as $row)
            {  
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	 
			
			?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value(<? echo $row[csf('id')]; ?>)" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $row[csf('sys_number')]; ?></p></td>
                    <td><p><? echo $supplier_arr[$row[csf('service_provider_id')]]; ?></p></td>
                    <td width="100" align="right"><p><? echo $row[csf('wo_qty')]; ?></p></td>
                    <td width="90" align="center"><? echo $rate_for[$row[csf('rate_for')]]; ?></td>
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




if($action=="check_unique")
{
	
	$operation_arr=explode("__",$operation);
	$flag=0;
	foreach($operation_arr as $operation_values)
	{ 
	list($id,$job_no,$buyer_id,$buyer_name,$style_ref_no,$gmts_item_id,$gmts_item,$po_id,$po_number)=explode("**",$operation_values);
	
	$is_duplicate = is_duplicate_field( "id", "piece_rate_wo_dtls", "mst_id='$mst_id' and job_id='$id' and order_id='$po_id' and item_id='$gmts_item_id'" );//
	
	if($is_duplicate==1){
		if($items=='')$items=$gmts_item; else $items.=' and '.$gmts_item;
		$flag=1;
		}
		else
		{
		$flag=0;
		}
	}
	
	if($flag==1){echo $items;}else{echo 0;}

exit();
}





if($action=="show_price_rate_wo_listview___off")
{


		if($db_type==0)
		{
			$sql = "select a.id,a.company_id,a.service_provider_id,group_concat(b.item_id) as item_id,group_concat(b.buyer_id) as buyer_id,group_concat(b.order_id) as order_id,b.order_source,b.job_id from  piece_rate_wo_mst a,piece_rate_wo_dtls b where a.id=b.mst_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_id,b.order_source,a.company_id,a.service_provider_id,a.id"; 
		}
		else
		{
			 $sql = "select a.id,a.company_id,a.service_provider_id,LISTAGG(CAST(b.item_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.item_id) as item_id,LISTAGG(CAST(b.buyer_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.buyer_id) as buyer_id,LISTAGG(CAST(b.order_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.order_id) as order_id,b.order_source,b.job_id from  piece_rate_wo_mst a,piece_rate_wo_dtls b where a.id=b.mst_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_id,b.order_source,a.company_id,a.service_provider_id,a.id"; 
		}
		
	  // echo $sql; 
	$result = sql_select($sql);
	foreach ($result as $row)
	{  
		$poIdArr[$row[csf('order_source')]][]=$row[csf('order_id')];
		//$jobIdArr[$row[csf('order_source')]]=$row[csf('job_id')];
	}
	
	
	$sql="select id,job_no_mst,po_number from wo_po_break_down where status_active = 1 and is_deleted = 0 ";
		$p=1;
		
		$po_id_chunk_arr=array_chunk(array_unique(explode(',',implode(',',$poIdArr[1]))),999);
		foreach($po_id_chunk_arr as $jobIdArr)
		{
			if($p==1) $sql .="  and ( id in(".implode(",",$jobIdArr).")"; 
			else  $sql .=" or id in(".implode(",",$jobIdArr).")";
			
			$p++;
		}
		$sql .=")";


	$po_sql_result = sql_select($sql);
	foreach($po_sql_result as $row)
	{
		$job_arr[$row[csf('id')]]=$row[csf('job_no_mst')];
		$po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
		
	}


	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="100">Job Number</th>
            <th width="120">Company</th>
            <th width="120">Service Provider</th>
            <th width="200">Order No</th>
            <th width="150">Buyer</th>
            <th>Item</th>
        </thead>
        
        
	</table>
	<div style="width:900px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
             
				if($row[csf('order_source')]==1)
				{
					$job_arrs=$job_arr; $po_number_arrs=$po_number_arr; $buyer_arrs=$buyer_arr;
				}
				else
				{
					$job_arrs=$subcon_job_arr; $po_number_arrs=$subcon_po_number_arr;$buyer_arrs=$subcon_buyer_arr;
				}		
			  
			 
			 
			  $item_conca='';
			  $items=array_unique(explode(",",$row[csf('item_id')]));
			  foreach($items as $item_id)
			  {
				if($item_conca=='')$item_conca=$garments_item[$item_id]; else $item_conca.=','.$garments_item[$item_id];  
			  }
				
			  $order_conca='';
			  $orders=array_unique(explode(",",$row[csf('order_id')]));
			  foreach($orders as $order_id)
			  {
				if($order_conca=='')$order_conca=$po_number_arrs[$order_id]; else $order_conca.=','.$po_number_arrs[$order_id]; 
			  	$job_no=$job_arrs[$order_id];
			  }
				
				
			  $buyer_conca='';
			  $buyers=array_unique(explode(",",$row[csf('buyer_id')]));
			  foreach($buyers as $buyer)
			  {
				if($buyer_conca=='')$buyer_conca=$buyer_arrs[$buyer]; else $buyer_conca.=','.$buyer_arrs[$buyer];  
			  }
				
			  $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	 
				
			?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="show_list_view('<? echo $row[csf('id')].'_'.$row[csf('job_id')]; ?>', 'populate_price_rat_dtls_form_data', 'details_entry_list_view', 'requires/piece_rate_work_order_controller', '');set_button_status(1, '<? echo $_SESSION['page_permission']; ?>', 'fnc_prices_rate_wo',1)" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><? echo $job_no; ?></td>
                    <td width="120" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
                    <td width="120" align="center"><p><? echo $supplier_arr[$row[csf('service_provider_id')]]; ?></p></td>
                    <td width="200"><p><? echo $order_conca; ?></p></td>
                    <td width="150"><p><? echo $buyer_conca; ?></p></td>
                    <td><p><? echo $item_conca; ?></p></td>
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



if($action=='populate_price_rat_dtls_form_data')
{
	//list($mst_id,$job_id)=explode('_',$data);
	$rate_bill_var = return_field_value("rate_bill_var","piece_rate_wo_mst","id=$data and status_active=1 and is_deleted=0"); 

	$sql = "select id, mst_id, order_source, job_id, order_id, buyer_id, item_id, style_ref,color_type,wo_qty,uom, avg_rate,amount, remarks,ord_recev_company,rate_history from piece_rate_wo_dtls where mst_id=$data and status_active=1 and is_deleted=0"; 
	// echo $sql; die;
	$i=1;
	$data_array=sql_select($sql);
	
	foreach ($data_array as $row)
	{  
		$poIdArr[$row[csf('order_source')]][]=$row[csf('order_id')];
		$dtlsIdArr[$row[csf('id')]][]=$row[csf('id')];
		$item_id_arr[$row[csf('item_id')]]=$row[csf('item_id')];
	}
	$item_cond = "";
	if (count($item_id_arr)) 
	{
		// $item_cond = " and item_number_id in ". implode(',',$item_id_arr); 
		$item_cond = where_con_using_array($item_id_arr,0,'c.item_number_id');
	}
	$sql="select a.id,a.job_no_mst,a.po_number,b.product_dept,c.item_number_id,c.order_quantity from wo_po_break_down a,wo_po_details_master b,wo_po_color_size_breakdown c where a.job_id=b.id and c.po_break_down_id=a.id $item_cond  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 ";
	$p=1;
		
	$po_id_chunk_arr=array_chunk(array_unique(explode(',',implode(',',$poIdArr[1]))),999);
	foreach($po_id_chunk_arr as $jobIdArr)
	{
		if($p==1) $sql .="  and ( a.id in(".implode(",",$jobIdArr).")"; 
		else  $sql .=" or a.id in(".implode(",",$jobIdArr).")";
		
		$p++;
	}
	$sql .=")";
	$sql;
	// echo $sql; die;
	$po_sql_result = sql_select($sql);
	foreach($po_sql_result as $row)
	{
		$job_arr[$row[csf('id')]]=$row[csf('job_no_mst')];
		$po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
		$po_qty_arr[$row[csf('id')]][$row[csf('item_number_id')]]['QTY'] += $row[csf('order_quantity')];
		$po_qty_arr[$row[csf('id')]][$row[csf('item_number_id')]]['PRODUCT_DEPT'] = $row[csf('PRODUCT_DEPT')];
		
	}
	if ($rate_bill_var != 2)   //COLOR SIZE WISE
	{ 
		//--------------------------------------------------------------
		$dtls_ids = implode(',',$dtlsIdArr);
		$sql2 = "select * from piece_rate_wo_qty_dtls where dtls_id in ($dtls_ids)"; 
		$data_array2=sql_select($sql2);
		$ids=$qty=$rate=$uom=$color=$size=$oqty=$final_wo_qty=$sl_arr=array();
		$sl=1;
		foreach ($data_array2 as $rows)
		{ 
			$id = $rows['DTLS_ID'];
			if($sl==1)
			{
				$qty[$id] 	= $rows[csf("wo_qty")];
				$rate[$id] 	= $rows[csf("rate")];
				$uom[$id] 	= $rows[csf("uom")];
				$color[$id] = $rows[csf("color_id")];
				$size[$id] 	= $rows[csf("size_id")];
				$oqty[$id] 	= $rows[csf("order_qty")];	
				$ids[$id] 	= $rows[csf("id")];	
				$final_wo_qty[$id] =$rows[csf("final_wo_qty")];
			}
			else
			{
				$qty[$id]	.=','.$rows[csf("wo_qty")];
				$rate[$id]	.=','.$rows[csf("rate")];
				$uom[$id]	.=','.$rows[csf("uom")];
				$color[$id]	.=','.$rows[csf("final_wo_qty")];
				$size[$id]	.=','.$rows[csf("color_id")];
				$oqty[$id]	.=','.$rows[csf("size_id")];
				$ids[$id]	.=','.$rows[csf("order_qty")];
				$final_wo_qty[$id].=','.$rows[csf("id")];	
			} 
			$sl_arr[$id] = $sl;
			$sl++;
		}
	}

	foreach ($data_array as $row)
	{  
		$id = $row[csf('id')];
	
		if($row[csf("order_source")]==1){$job_arrs=$job_arr; $po_number_arrs=$po_number_arr; $buyer_arrs=$buyer_arr;}
		else{$job_arrs=$subcon_job_arr;$po_number_arrs=$subcon_po_number_arr;$buyer_arrs=$subcon_buyer_arr;}

		
	 
		$rate_history = $row["RATE_HISTORY"];

		if ($rate_bill_var == 2) //PO WISE
		{
			$wo_qty_data = '';
			$item = $row[csf("item_id")];  
			$product_dept =$po_qty_arr[$row[csf("order_id")]][$item]['PRODUCT_DEPT'];
			$wo_qty = $po_qty_arr[$row[csf("order_id")]][$item]['QTY'];
			$attrCond1 = " placeholder='$wo_qty'";
			$attrCond2 = " placeholder='Double click to search' onDblClick='openmypage_avg_rate($i,$product_dept,$item);'";
			$is_disable = 0;

			$wo_qty_data=$job_arrs[$row[csf("order_id")]].'__'.$row[csf("order_id")].'__'.$buyer_arrs[$row[csf("buyer_id")]].'__'.$row[csf("item_id")].'__'.$garments_item[$row[csf("item_id")]].'__'.$row[csf("style_ref")];
		}
		else		//COLOR SIZE WISE
		{

			$attrCond1 = " placeholder='Double click to search' onDblClick='openmypage_wo_qty($id);' readonly";
			$attrCond2 = "";
			$is_disable = 1; 

			$wo_qty_data=$job_arrs[$row[csf("order_id")]].'__'.$row[csf("order_id")].'__'.$buyer_arrs[$row[csf("buyer_id")]].'__'.$row[csf("item_id")].'__'.$garments_item[$row[csf("item_id")]].'__'.$row[csf("style_ref")].'__'.($sl_arr[$id]).'~~'.$oqty[$id].'~~'.$qty[$id].'~~'.$rate[$id].'~~'.$uom[$id].'~~'.$final_wo_qty[$id].'~~'.$color[$id].'~~'.$size[$id].'~~'.$ids[$id];
		}
		
		//echo "document.getElementById('txtOrderQtyHistory_1').value= '".$wo_qty_data."';\n";$po_number_arr[]	




		//---------------------------------------------------------------
		?>

		<input type="hidden" name="txtOrderQtyHistory_<? echo $i; ?>" id="txtOrderQtyHistory_<? echo $i; ?>" value="<? echo $wo_qty_data; ?>" />
		<tr>
			<td align="center">
				<? 
					echo create_drop_down( "cboOrderSource_".$i, 100, $order_source,"", 1, "-- Select --", $row[csf("order_source")], "",1 );
				?>
			</td>
			<td>
				<?
					echo create_drop_down( "cboOrdRceveCompId_".$i, 90, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--", $row[csf("ord_recev_company")], "",1 );
				?>
			</td>
			<td>
				<input type="hidden" id="detailsUpdateId_<? echo $i; ?>" name="detailsUpdateId_<? echo $i; ?>" value="<? echo $row[csf("id")]; ?>" />
				<input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" style="width:100px;" placeholder="Double click to search" onDblClick="openmypage_job_no(1);" value="<? echo $job_arrs[$row[csf("order_id")]];?>" readonly disabled  />
				<input type="hidden" name="txtjobid_<? echo $i; ?>" id="txtjobid_<? echo $i; ?>" value="<? echo $row[csf("job_id")];?>" />
			</td>
			<td>
				<input type="text" name="txtorderno_<? echo $i; ?>" id="txtorderno_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $po_number_arrs[$row[csf("order_id")]];?>" readonly/>
				<input type="hidden" name="txtorderid_<? echo $i; ?>" id="txtorderid_<? echo $i; ?>" value="<? echo $row[csf("order_id")];?>" />
			</td>
			<td>
				<input type="text" name="txtbuyer_<? echo $i; ?>" id="txtbuyer_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $buyer_arrs[$row[csf("buyer_id")]];?>" readonly />
				<input type="hidden" name="txtbuyerid_<? echo $i; ?>" id="txtbuyerid_<? echo $i; ?>" value="<? echo $row[csf("buyer_id")];?>" />
			</td>
			<td>
				<input type="text" name="txtitem_<? echo $i; ?>" id="txtitem_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $garments_item[$row[csf("item_id")]];?>" readonly />
				<input type="hidden" name="txtitemid_<? echo $i; ?>" id="txtitemid_<? echo $i; ?>" value="<? echo $row[csf("item_id")];?>" />
			</td>
			<td>
				<input type="text" name="txtstyle_<? echo $i; ?>" id="txtstyle_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $row[csf("style_ref")];?>" readonly />
			</td>
			<td>
				<? 
				echo create_drop_down( "colortype_".$i, 90, $color_type,"",1, "--Select--", $row[csf("color_type")],"",0,"" ); 
				?>                                    
			</td> 
			<td>
				<input type="text" name="txtwoqty_<? echo $i; ?>" id="txtwoqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px;" <?= $attrCond1 ?> value="<? echo $row[csf("wo_qty")];?>"  />
			</td>
			<td>
				<? 
				echo create_drop_down( "cbodtlsuom_".$i, 80, $unit_of_measurement,"",1, "--Select--",$row[csf("uom")],"changeUom(this)",$is_disable,"1,2" ); 
				?>                                    
			</td>
			<td>
				<input type="text" name="txtavgrate_<? echo $i; ?>" id="txtavgrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<? echo $row[csf("avg_rate")];?>" readonly <?=$attrCond2 ?>/>
				<input type="hidden" name="txtRate_<?= $i; ?>" id="txtRate_<?= $i; ?>" value="<?= $rate_history ?>" />
			</td>
			
			<td>
				<input type="text" name="txtdtlamount_<? echo $i; ?>" id="txtdtlamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<? echo $row[csf("amount")];?>" readonly />
			</td>
			
			<td>
				<input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $row[csf("remarks")];?>" />
			</td>
			
			<td align="center">
				<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>)"/>
				<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
			</td>
			
			
			
		</tr> 
		<?
		$i++; 
	}
	
	
//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_prices_rate_wo',1);\n"; 
	
exit();	
}







if($action=='populate_price_rat_mst_form_data')
{
	
	
	$sql = "select id,sys_number, company_id, service_provider_id, wo_date, rate_for, attension, currence, exchange_rate,location,line_id,rate_bill_var, remarks from piece_rate_wo_mst where id=$data and status_active=1 and is_deleted=0"; 
	
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 
		
		$data_for_line = $row[csf("location")].'_'.$row[csf("company_id")].'_'.$row[csf("rate_for")]; 
		// echo $data_for_line."\n"; 
		echo"load_drop_down( 'requires/piece_rate_work_order_controller','".$data_for_line."', 'load_drop_down_sewing_line', 'sewing_line_td' );\n";

		echo "document.getElementById('update_id').value					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_system_id').value				= '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_service_provider_id').value		= '".$row[csf("service_provider_id")]."';\n";
		echo "document.getElementById('txt_wo_date').value					= '".change_date_format($row[csf("wo_date")])."';\n";
		echo "document.getElementById('cbo_rate_for').value					= '".$row[csf("rate_for")]."';\n";
		echo "document.getElementById('txt_attention').value				= '".$row[csf("attension")]."';\n";
		echo "document.getElementById('cbo_currency').value					= '".$row[csf("currence")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value			= '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_remarks_mst').value				= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_location').value					= '".$row[csf("location")]."';\n";
		echo "document.getElementById('cbo_line_id').value					= '".$row[csf("line_id")]."';\n";
		echo "document.getElementById('rate_source').value					= '".$row[csf("rate_bill_var")]."';\n";
		echo "document.getElementById('qty_source').value					= '".$row[csf("rate_bill_var")]."';\n";

		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		/*	echo "$('#cbo_rate_for').attr('disabled','disabled');\n";
		echo "$('#txt_wo_date').attr('disabled','disabled');\n";
		echo "$('#cbo_currency').attr('disabled','disabled');\n";
		echo "$('#txt_exchange_rate').attr('disabled','disabled');\n";
		echo "$('#cbo_location').attr('disabled','disabled');\n";
		*/		
		exit();
	}
}




if($action=="price_rate_wo_color_print")//----------------------------------Print Button 2------------------------
{
  
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$Color_library=return_library_array( "select id,COLOR_name from LIB_COLOR", "id", "COLOR_name");

$sql = "select service_provider_id,sys_number,wo_date,rate_for,company_id,attension,remarks from piece_rate_wo_mst where id='$data' and status_active=1 and is_deleted=0"; 
$data_array=sql_select($sql);
$company_id=$data_array[0][csf("company_id")];
$attension=$data_array[0][csf("attension")];
$sys_number=$data_array[0][csf("sys_number")];
$remarks=$data_array[0][csf("remarks")];
$comp_info=sql_select("select a.*,b.country_name from lib_company a,lib_country b where a.country_id=b.id and a.id='$company_id'");
 

$data_arr=sql_select("SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(22,36) and c.tag_company =$company_id");
    foreach ($data_arr as $row)
    { 
    $sp_arr[$row[csf("id")]]=$row[csf("supplier_name")];
    }


?>
<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" style="font-size:13px;">
   <tr>
         <p align="left">
       <?
               $data_row=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left" colspan="3">
                <?
                foreach($data_row as $img_row)
                {
                    ?>
                    <img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='70' align="middle" />   
                    <? 
                }
                ?>
   </p>
      
       
        <td colspan="14" align="center"><b style="font-size:36px; font-weight:bold;">
        <? echo $company_library[$company_id];//$comp_info[0][csf("company_name")]; ?></b><br>
        <? echo $comp_info[0][csf("plot_no")];?>,
        <? echo $comp_info[0][csf("level_no")];?>,
        <? echo $comp_info[0][csf("road_no")];?>,
        <? echo $comp_info[0][csf("block_no")];?>,
        <? echo $comp_info[0][csf("city")];?>,
        <? echo $comp_info[0][csf("zip_code")];?>,
        <? echo $comp_info[0][csf("province")];?>,
        <? echo $comp_info[0][csf("country_name")];?><br>
        <? echo $comp_info[0][csf("email")];?>,
        <? echo $comp_info[0][csf("website")];?><br>
        <b>Piece Rate Work Order for <? echo $rate_for[$data_array[0][csf("rate_for")]]; ?></b>
        </td>
        
   </tr> 
   <tr>
        <td colspan="15" align="right">Work Order No.: <b><? echo $sys_number;?></b></td>
   </tr>
   <tr>
        <td colspan="7"><b>Work Order To :</b> <? echo $sp_arr[$data_array[0][csf("service_provider_id")]]; ?></td>
        <td colspan="8">Attention : <? echo $attension;?></td>
   </tr> 
   <tr>
        <td colspan="7">Unite Name : <? echo $rate_fro; ?></td>
        <td colspan="8">Date : <? echo $ship_date=$data_array[0][csf("wo_date")]; ?></td>
   </tr> 
    <tr>
        <td colspan="15"><strong>Remarks : <? echo $remarks; ?></strong></td>
   </tr> 
   <tr height="10" style ="min-height:10px;">
        <td colspan="15"></td>
   </tr>
    <tr>
        <th width="35">SL</th>
        <th width="60">Buyer</th>
        <th width="40">Job No</th>
        <th width="100">Style No</th>
        <th width="100">Order No</th>
        <th width="100">Gmt.Item</th>
        <th width="100">Color</th>
        <th width="80">Rate Variable</th>
        <th width="45">Order Qty</th>
        <th width="70">Ship. Date</th>
        <th width="45">WO Qty</th>
        <th width="40">UOM</th>
        <th width="50">Rate</th>
        <th width="50">Amount</th>
        <th>Remarks</th>
    </tr>
        <tr>
        <!--<td colspan="12"> <strong>STYLE NAME: 
            <?
            $sql = "select a.id, a.mst_id , a.order_source,a.job_id, a.order_id,a.buyer_id,a.item_id,a.color_type,a.wo_qty,a.uom,a.avg_rate,a.amount, b.order_qty
              from piece_rate_wo_dtls a, piece_rate_wo_qty_dtls b where a.id=b.dtls_id and a.mst_id = $data and a.status_active = 1 and a.is_deleted = 0";
            $data1_array=sql_select($sql);
            //echo $sql;
            $i=0;
            foreach ($data1_array as $row)
            { 
                $job_id_arr_data[$row[csf("mst_id")]][$row[csf("job_id")]]['job']=$row[csf("job_id")];
                
                $job_id_arr[$row[csf("job_id")]]=$row[csf("job_id")];
                $job_library=return_library_array("SELECT ID, JOB_NO From WO_PO_DETAILS_MASTER", "ID", "JOB_NO" );
                $job=$job_library[$row[csf("job_id")]];
                $Style_info_library=return_library_array("SELECT JOB_NO, STYLE_REF_NO From WO_PO_DETAILS_MASTER", "JOB_NO", "STYLE_REF_NO" );               
                $i++;
            }   
            //echo '<pre>'; print_r($job_id_arr_data);
            //echo $Style_info_library[$job];
             
            foreach ($job_id_arr_data as $m_id=> $m_data)
            {
                foreach ($m_data as $job_id=> $rows)
                {
                    //echo $job_id = $rows[csf("job")];
                    //echo '<pre>'; print_r($rows);
                    $job_library_1=return_library_array("SELECT ID, JOB_NO From WO_PO_DETAILS_MASTER", "ID", "JOB_NO" );
                    //echo $job_1=$job_library_1[$rows[csf("job_id")]];
                    $Style_info_library_1=return_library_array("SELECT JOB_NO, STYLE_REF_NO From WO_PO_DETAILS_MASTER", "JOB_NO", "STYLE_REF_NO" ); 
                    //echo $Style_info_library[$job];
                    //echo $Style_info_library_1[$job_1]; echo ", ";
                }
            }
             ?></strong>
        </td>-->
    </tr>
<?


//$sql = "select id,order_source, job_id, order_id, buyer_id, item_id, color_type, wo_qty,uom, avg_rate,amount from  piece_rate_wo_dtls where mst_id='$data' and status_active=1 and is_deleted=0"; 
    
$sql = "select a.id,a.order_source,a.job_id, a.order_id,a.buyer_id,a.item_id,a.color_type,a.wo_qty,a.uom,a.avg_rate,
 a.amount, sum(order_qty) as order_qty
 from piece_rate_wo_dtls a,piece_rate_wo_qty_dtls b
 where a.id=b.dtls_id and a.mst_id = $data and a.status_active = 1 and a.is_deleted = 0 
 group by a.id,a.order_source,a.job_id,a.order_id,a.buyer_id,a.item_id, a.color_type,a.wo_qty, a.uom,a.avg_rate,a.amount"; 
 $sql_1 = "SELECT a.ID,  a.MST_ID,  a.ORDER_SOURCE,  a.JOB_ID,  a.ORDER_ID,  a.BUYER_ID,  a.ITEM_ID,  a.COLOR_TYPE,  SUM(b.ORDER_QTY) AS order_qty,  b.COLOR_ID,  b.RATE,
SUM(b.FINAL_WO_QTY) AS FINAL_WO_QTY,  b.AMOUNT            AS AMOUNT,  SUM(b.WO_QTY)       AS WO_QTY,  b.UOM, a.remarks
FROM piece_rate_wo_dtls a,  piece_rate_wo_qty_dtls b
WHERE a.ID          = b.DTLS_ID
AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED    = 0  AND a.MST_ID        = $data
GROUP BY a.ID,  a.MST_ID,  a.ORDER_SOURCE,  a.JOB_ID,  a.ORDER_ID,  a.BUYER_ID,  a.ITEM_ID,  a.COLOR_TYPE,  b.COLOR_ID,  b.RATE,  b.AMOUNT,  b.UOM , a.remarks
ORDER BY a.JOB_ID,  a.ORDER_ID,  a.BUYER_ID,  a.ITEM_ID,  b.COLOR_ID,  b.RATE"; 
    $data_array=sql_select($sql_1);
    //echo $sql_1;die;
    $po_id_arr[1][0]=0;$po_id_arr[2][0]=0;
    foreach ($data_array as $row)
    { 
        $po_id_arr[$row[csf('order_source')]][$row[csf("order_id")]]=$row[csf("order_id")];
    }
    
    
    $order_sql="select id, po_number,job_no_mst, 1 as order_source from wo_po_break_down where id in(".implode(',',$po_id_arr[1]).") and status_active=1 and is_deleted=0
                union all
                select id, order_no as po_number,job_no_mst, 2 as order_source from subcon_ord_dtls  where id in(".implode(',',$po_id_arr[2]).") and status_active=1 and is_deleted=0";
    //echo $order_sql;
    $order_sql_result_arr=sql_select($order_sql);
    foreach ($order_sql_result_arr as $row)
    { 
        $jobOrderdataArr['po'][$row[csf('order_source')]][$row[csf('id')]]=$row[csf('po_number')];
        $jobOrderdataArr['job'][$row[csf('order_source')]][$row[csf('id')]]=$row[csf('job_no_mst')];
        $jobOrderdataArr['job'][$row[csf('order_source')]][$row[csf('id')]]=$row[csf('job_no_mst')];
    }
    
    $sl=1;
    foreach ($data_array as $row)
    { 
    
        if($row[csf('order_source')]==1)
        {
            $buyer_arrs=$buyer_arr;
        }else
        {
            $buyer_arrs=$subcon_buyer_arr;
        }       
    $style_library=return_library_array("SELECT ID, STYLE_REF_NO FROM WO_PO_DETAILS_MASTER", "ID", "STYLE_REF_NO" );
    
    ?>
    <!--<tr>
        <td colspan="12"> <strong>STYLE NAME: 
            <? /*
             $job=$jobOrderdataArr['job'][$row[csf('order_source')]][$row[csf('order_id')]];
             $Style_info_library=return_library_array("SELECT JOB_NO, STYLE_REF_NO From WO_PO_DETAILS_MASTER", "JOB_NO", "STYLE_REF_NO" ); 
             echo $Style_info_library[$job];*/
             ?></strong>
        </td>
    </tr>-->
           <tr>
                <td align="center"><? echo $sl;?></td>
                <td><? echo $buyer_arrs[$row[csf("buyer_id")]];?></td>
                <td><? echo $jobOrderdataArr['job'][$row[csf('order_source')]][$row[csf('order_id')]];?></td>
                <td><? echo $style_library[$row[csf('JOB_ID')]];?></td>
                <td><? echo $jobOrderdataArr['po'][$row[csf('order_source')]][$row[csf('order_id')]];?></td>                
                <td><? echo $garments_item[$row[csf("item_id")]];?></td>
                <td><? echo $Color_library[$row[csf("COLOR_ID")]];?></td>
                <td><? echo $color_type[$row[csf("color_type")]];?></td>
                <td align="right"><? echo $row[csf("order_qty")];//$order_qty; ?></td>
                <td align="center"><?           
                $job=$jobOrderdataArr['job'][$row[csf('order_source')]][$row[csf('order_id')]];
                     
                $Ship_library=return_library_array("SELECT JOB_NO_MST, SHIPMENT_DATE From WO_PO_BREAK_DOWN", "JOB_NO_MST", "SHIPMENT_DATE" ); 
                
                     
                //echo $Style_info_library[$job];
                echo $Ship_library[$job]; ?></td>
                <td align="right"><? 
                                $um = $row[csf("uom")];
                                if($um==2)
                                {
                                    echo number_format ($row[csf("wo_qty")]/12,2,'.',''); $tot_wo_qty+=$row[csf("wo_qty")]/12;
                                }
                                else
                                {
                                    echo number_format ($row[csf("wo_qty")],2,'.',''); $tot_wo_qty+=$row[csf("wo_qty")];
                                }
                            
                ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]];?></td>
                <td align="center"><? echo $row[csf("rate")];?></td>
                <td align="right"><?
                    $um = $row[csf("uom")];
                    if($um==2)
                    {
                        echo number_format(($row[csf("wo_qty")]/12)*$row[csf("rate")],2,'.',''); $tot_amount+=($row[csf("wo_qty")]/12)*$row[csf("rate")];
                    }
                    else
                    {
                        echo number_format($row[csf("wo_qty")]*$row[csf("rate")],2,'.',''); $tot_amount+=$row[csf("wo_qty")]*$row[csf("rate")];
                    }
                    ?></td>
                <td> <? echo $row[csf("remarks")];?></td>
            </tr>
    <? 
        $sl++;  
    }
    ?>
    <tr>
        <th colspan="10">Total : </th>
        <th align="right"><? echo  number_format ($tot_wo_qty,0,'.','');?></th>
        <th></th>
        <th></th>
        <th align="right"><? echo number_format($tot_amount,2,'.','');?></th>
        <th></th>
    </tr>
    <tr>
        <td colspan="14">In Words: <? echo number_to_words($tot_amount,"Taka","Paisa");?></td>
    </tr>
</table>

<table width="700">    
<tr><td colspan="2"><b>Terms & Condition </b></td></tr>
<?
$sql = "select terms from  piece_rate_terms_condition  where mst_id='$data'"; 
    $i=1;
    $data_array=sql_select($sql);
    foreach ($data_array as $row)
    {
        echo "<tr><td width='20'>$i .</td><td>".$row[csf("terms")]."</td></tr>";
    $i++;
    }

?>
</table>
<br>
<div style=" width:900px;">
        <? echo signature_table(84, $company_id, "900px"); ?>
</div>

<?

exit();
}




if($action=="price_rate_wo_print")
{
extract($_REQUEST);

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");

$sql = "select service_provider_id,sys_number,wo_date,rate_for,company_id,attension from piece_rate_wo_mst where id='$data' and status_active=1 and is_deleted=0"; 
$data_array=sql_select($sql);
$company_id=$data_array[0][csf("company_id")];
$attension=$data_array[0][csf("attension")];
$sys_number=$data_array[0][csf("sys_number")];
$comp_info=sql_select("select a.*,b.country_name from lib_company a,lib_country b where a.country_id=b.id and a.id='$company_id'");
 

$data_arr=sql_select("SELECT a.id,a.supplier_name FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(22,36) and c.tag_company =$company_id");
	foreach ($data_arr as $row)
	{ 
	$sp_arr[$row[csf("id")]]=$row[csf("supplier_name")];
	}


?>
<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" style="font-size:13px;">
   <tr>
         <p align="left">
       <?
               $data_row=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left" colspan="3">
                <?
                foreach($data_row as $img_row)
                {
					?>
                    <img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' align="middle" />	
                    <? 
                }
                ?>
   </p>
      
       
        <td colspan="10" align="center"><b style="font-size:36px; font-weight:bold;">
		<? echo $company_library[$company_id];//$comp_info[0][csf("company_name")]; ?></b><br>
        <? echo $comp_info[0][csf("plot_no")];?>,
        <? echo $comp_info[0][csf("level_no")];?>,
        <? echo $comp_info[0][csf("road_no")];?>,
        <? echo $comp_info[0][csf("block_no")];?>,
        <? echo $comp_info[0][csf("city")];?>,
        <? echo $comp_info[0][csf("zip_code")];?>,
        <? echo $comp_info[0][csf("province")];?>,
        <? echo $comp_info[0][csf("country_name")];?><br>
        <? echo $comp_info[0][csf("email")];?>,
        <? echo $comp_info[0][csf("website")];?><br>
        <b>Piece Rate Work Order for <? echo $rate_for[$data_array[0][csf("rate_for")]]; ?></b>
        </td>
        
   </tr> 
   <tr>
        <td colspan="12" align="right">Work Order No.: <b><? echo $sys_number;?></b></td>
   </tr>
   <tr>
        <td colspan="6"><b>Work Order To :</b> <? echo $sp_arr[$data_array[0][csf("service_provider_id")]]; ?></td>
        <td colspan="7">Attention : <? echo $attension;?></td>
   </tr> 
   <tr>
        <td colspan="6">Unite Name : <? echo $rate_fro; ?></td>
        <td colspan="7">Date : <? echo $ship_date=$data_array[0][csf("wo_date")]; ?></td>
   </tr> 
    <tr>
        <th width="35">SL</th>
        <th width="60">Buyer</th>
        <th width="40">Job No</th>
        <th width="100">Order No</th>
        <th width="100">Gmt.Item</th>
        <th width="80">Rate Variable</th>
        <th width="45">Order Qty</th>
        <th width="70">Ship. Date</th>
        <th width="45">WO Qty</th>
        <th width="40">UOM</th>
        <th width="50">Rate</th>
        <th>Amount</th>
    </tr>
<?


//$sql = "select id,order_source, job_id, order_id, buyer_id, item_id, color_type, wo_qty,uom, avg_rate,amount from  piece_rate_wo_dtls where mst_id='$data' and status_active=1 and is_deleted=0"; 
	
$sql = "select a.id,a.order_source,a.job_id, a.order_id,a.buyer_id,a.item_id,a.color_type,a.wo_qty,a.uom,a.avg_rate,
       a.amount, sum(order_qty) as order_qty
  from piece_rate_wo_dtls a,piece_rate_wo_qty_dtls b
 where a.id=b.dtls_id and a.mst_id = $data and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
 group by a.id,a.order_source,a.job_id,a.order_id,a.buyer_id,a.item_id, a.color_type,a.wo_qty, a.uom,a.avg_rate,a.amount"; 
	$data_array=sql_select($sql);
	$po_id_arr[1][0]=0;$po_id_arr[2][0]=0;
	foreach ($data_array as $row)
	{ 
		$po_id_arr[$row[csf('order_source')]][$row[csf("order_id")]]=$row[csf("order_id")];
	}
	
	
	$order_sql="select id, po_number,job_no_mst, 1 as order_source from wo_po_break_down where id in(".implode(',',$po_id_arr[1]).") and status_active=1 and is_deleted=0
union all
select id, order_no as po_number,job_no_mst, 2 as order_source from subcon_ord_dtls  where id in(".implode(',',$po_id_arr[2]).") and status_active=1 and is_deleted=0";
	$order_sql_result_arr=sql_select($order_sql);
	foreach ($order_sql_result_arr as $row)
	{ 
		$jobOrderdataArr['po'][$row[csf('order_source')]][$row[csf('id')]]=$row[csf('po_number')];
		$jobOrderdataArr['job'][$row[csf('order_source')]][$row[csf('id')]]=$row[csf('job_no_mst')];
	}
	
	
	
	
	
	$sl=1;
	foreach ($data_array as $row)
	{ 
	
	if($row[csf('order_source')]==1){$buyer_arrs=$buyer_arr;}else{$buyer_arrs=$subcon_buyer_arr;}		
	
	
	?>
   <tr>
        <td align="center"><? echo $sl;?></td>
        <td><? echo $buyer_arrs[$row[csf("buyer_id")]];?></td>
        <td><? echo $jobOrderdataArr['job'][$row[csf('order_source')]][$row[csf('order_id')]];?></td>
        <td><? echo $jobOrderdataArr['po'][$row[csf('order_source')]][$row[csf('order_id')]];?></td>
        <td><? echo $garments_item[$row[csf("item_id")]];?></td>
        <td><? echo $color_type[$row[csf("color_type")]];?></td>
        <td align="right"><? echo $row[csf("order_qty")];//$order_qty; ?></td>
        <td align="center"><? echo $ship_date; ?></td>
        <td align="right"><? echo $row[csf("wo_qty")]; $tot_wo_qty+=$row[csf("wo_qty")];?></td>
        <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]];?></td>
        <td align="right"><? echo $row[csf("avg_rate")];?></td>
        <td align="right"><? echo $row[csf("amount")]; $tot_amount+=$row[csf("amount")];?></td>
    </tr>
	<? 
    $sl++;	
	}
 	?>
    <tr>
        <th colspan="8">Total : </th>
        <th align="right"><? echo $tot_wo_qty;?></th>
        <th></th>
        <th></th>
        <th align="right"><? echo $tot_amount;?></th>
    </tr>
    <tr>
        <td colspan="12">In Words: <? echo number_to_words($tot_amount,"Taka","Paisa");?></td>
    </tr>
</table>

<table width="700">    
<tr><td colspan="2"><b>Terms & Condition </b></td></tr>
<?
$sql = "select terms from  piece_rate_terms_condition  where mst_id='$data'"; 
	$i=1;
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		echo "<tr><td width='20'>$i .</td><td>".$row[csf("terms")]."</td></tr>";
	$i++;
	}

?>
</table>
<br>
<div style=" width:700px;">
		<? echo signature_table(84, $company_id, "700px"); ?>
</div>

<?

exit();
}




if($action=="terms_condition_popup")
{
	 session_start();
	echo load_html_head_contents("Order Search","../../", 1, 1, $unicode);
	extract($_REQUEST);
	 $permission=$_SESSION['page_permission'];
?>
	<script>
var permission='<? echo $permission; ?>';
	
function add_break_down_tr(i) 
 {
	var row_num=$('#tbl_termcondi_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;
	 
		 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});  
		  }).end().appendTo("#tbl_termcondi_details");
		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
		  $('#termscondition_'+i).val("");
	}
		  
}

function fn_deletebreak_down_tr(rowNo) 
{   
	
	
		var numRow = $('table#tbl_termcondi_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_termcondi_details tbody tr:last').remove();
		}
	
}

function fnc_fabric_booking_terms_condition( operation )
{
	    var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			
			if (form_validation('termscondition_'+i,'Term Condition')==false)
			{
				return;
			}
			
			data_all=data_all+get_submitted_data_string('txt_mst_id*termscondition_'+i,"../../../",i);
		}
		var data="action=save_update_prices_rate_wo_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		
		freeze_window(operation);
		http.open("POST","piece_rate_work_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse()
{
	
	if(http.readyState == 4) 
	{
	    var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
	}
}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../",$permission);  ?>
<fieldset>
        	<form id="termscondi_1" autocomplete="off">
           <input type="hidden" id="txt_mst_id" name="txt_mst_id" value="<? echo $data ?>"/>
            
            
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  piece_rate_terms_condition  where mst_id=$data");
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td> 
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
					$terms_condition_arr=array(
					1=>'Goods Should be Supplied As per Approved Sample.',
					2=>'Goods must be Delivered to the Factory.',
					3=>'If any Defective or Running improter items found, the same Quantity should be Repleased or Will be Adjust From the Payment.',
					4=>'Buyer Name and Po number must be Mentioned With Details in Each Delivery Challan.',
					5=>'One delivery Challan should not Used more than one Order.'
					);
					foreach( $terms_condition_arr as $value )
						{
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $value; ?>"  /> 
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
                                    </td>
                                </tr>
                    <? 
						}
					} 
					?>
                </tbody>
                </table>
                
                <table width="650" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <? 
									echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
									?>
                        </td> 
                    </tr>
                </table>
            </form>
        </fieldset>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="save_update_prices_rate_wo_terms_condition")
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}		
		 $id=return_next_id( "id", "piece_rate_terms_condition ", 1 ) ;
		 $field_array="id,mst_id,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_mst_id.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from piece_rate_terms_condition where  mst_id =".$txt_mst_id."",0);
		$rID=sql_insert("piece_rate_terms_condition",$field_array,$data_array,1);
		
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}	

	
	
exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$rate_source =  str_replace("'","",$qty_source);
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$flag=1;
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			
            // master part--------------------------------------------------------------;
			$price_rate_wo_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'PRWO', date("Y",time()), 5, "select sys_number_prefix, sys_number_prefix_num from piece_rate_wo_mst where company_id=$cbo_company_id and $year_cond=".date('Y',time())." order by id desc", "sys_number_prefix", "sys_number_prefix_num" ));
		 	
			$id=return_next_id( "id", "piece_rate_wo_mst", 1 ) ;
			$field_array_mst="id,sys_number_prefix,sys_number_prefix_num,sys_number,company_id,service_provider_id,wo_date,rate_for,attension,currence,exchange_rate,location,line_id,prod_reso_allo,rate_bill_var,remarks,inserted_by,insert_date,status_active,is_deleted";
			
			$data_array_mst="(".$id.",'".$price_rate_wo_system_id[1]."',".$price_rate_wo_system_id[2].",'".$price_rate_wo_system_id[0]."',".$cbo_company_id.",".$txt_service_provider_id.",".$txt_wo_date.",".$cbo_rate_for.",".$txt_attention.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_location.",".$cbo_line_id.",".$prod_reso_allo.",".$rate_source.",".$txt_remarks_mst.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
			
        	// details part--------------------------------------------------------------;

			$field_array_dtls="id, mst_id, order_source,ord_recev_company, job_id, order_id, buyer_id, item_id, style_ref,color_type, wo_qty,uom, avg_rate,rate_history,amount, remarks, inserted_by, insert_date,status_active,is_deleted";
			$id_dtls=return_next_id( "id", "piece_rate_wo_dtls", 1 ) ;

			if ($rate_source ==2)  //PO WISE
			{
				
				$field_array_rate_dtls="id,mst_id,dtls_id,lib_sew_op_id,rate,inserted_by,insert_date,status_active,is_deleted";
			}
			else //COLOR SIZE WISE
			{
				$field_array_wo_dtls="id, dtls_id, color_id, size_id, order_qty, wo_qty, rate, uom,final_wo_qty, inserted_by, insert_date,status_active,is_deleted";
				$id_wo_dtls=return_next_id( "id", "piece_rate_wo_qty_dtls", 1 ) ;

			}
			
			$tot_rows= str_replace("'","",$tot_rows);
			
			for($i=1; $i<=$tot_rows; $i++)
			{
    			$cbo_order_source='cboOrderSource_'.$i;
    			$cbo_ord_rceve_comp_id='cboOrdRceveCompId_'.$i;
    			$txtjobid='txtjobid_'.$i;
    			$txtorderid='txtorderid_'.$i;
    			
    			$txtbuyerid='txtbuyerid_'.$i;
    			$txtitemid='txtitemid_'.$i;
    			$txtstyle='txtstyle_'.$i;
    			$colortype='colortype_'.$i;
    			
    			$txtwoqty='txtwoqty_'.$i;
    			$txtavgrate='txtavgrate_'.$i;
    			$rate_history='txtRate_'.$i;
    			$txtremarks='txtremarks_'.$i;
    			
    			$cbodtlsuom='cbodtlsuom_'.$i;
    			$txtdtlamount='txtdtlamount_'.$i;
			
			
				
				
				if($i==1)
				{
					if(str_replace("'",'',$$txtwoqty)!=""){
					$data_array_dtls="(".$id_dtls.",".$id.",".$$cbo_order_source.",".$$cbo_ord_rceve_comp_id.",".$$txtjobid.",".$$txtorderid.",".$$txtbuyerid.",".$$txtitemid.",".$$txtstyle.",".$$colortype.",".$$txtwoqty.",".$$cbodtlsuom.",".$$txtavgrate.",".$$rate_history.",".$$txtdtlamount.",".$$txtremarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
					}
				}
				else
				{
					if(str_replace("'",'',$$txtwoqty)!=""){
					$data_array_dtls.=", (".$id_dtls.",".$id.",".$$cbo_order_source.",".$$cbo_ord_rceve_comp_id.",".$$txtjobid.",".$$txtorderid.",".$$txtbuyerid.",".$$txtitemid.",".$$txtstyle.",".$$colortype.",".$$txtwoqty.",".$$cbodtlsuom.",".$$txtavgrate.",".$$rate_history.",".$$txtdtlamount.",".$$txtremarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
					
					}
				}
				
				if ($rate_source == 2)  //PO WISE
				{
					$rate_history_arr = array();
					$rate_history_arr = explode('@@',$$rate_history);  
					$kk =0;
					foreach ($rate_history_arr as $data) 
					{
						$rate_dtls_id = return_next_id_by_sequence("piece_rate_wo_rate_dtls_seq", "piece_rate_wo_rate_dtls", $con);
						$pair = explode ('#', $data);
						$lib_sew_op_id = str_replace("'","",$pair[0]);
						$rate	= str_replace("'","",$pair[1]);
						// echo "10**".$rate; die;
						if($kk==0 && $i==1)
						{
							$data_array_rate_dtls="(".$rate_dtls_id.",".$id.",".$id_dtls.",'".$lib_sew_op_id."','".$rate."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
						}
						else
						{
							$data_array_rate_dtls.=",(".$rate_dtls_id.",".$id.",".$id_dtls.",'".$lib_sew_op_id."','".$rate."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
						}
						$kk++;
					}
					

				}
				else //COLOR SIZE WISE
				{
					// order dtls start --------------------------
					$order_qty_history='txtOrderQtyHistory_'.$i;
					
					
					
					
					
					list($dtls_history,$order_qty,$wo_qty,$order_rate,$order_uom,$final_wo_qty,$order_color,$order_size)=explode("~~",$$order_qty_history);
					
					//echo "insert into piece_rate_wo_dtls ($field_array_dtls) values($data_array_dtls)"; die;
					
					$dtls_history=explode("__",$dtls_history);
					$wo_qty=explode(",",str_replace("'","",$wo_qty));
					$order_rate=explode(",",str_replace("'","",$order_rate));
					$order_uom=explode(",",str_replace("'","",$order_uom));
					$final_wo_qty=explode(",",str_replace("'","",$final_wo_qty));
					$order_color=explode(",",str_replace("'","",$order_color));
					$order_size=explode(",",str_replace("'","",$order_size));
					$order_qty=explode(",",str_replace("'","",$order_qty));


					//echo $txtOrderQtyHistory_3; die;



					for($di=0; $di<$dtls_history[6]; $di++)
					{
						
						if($di==0 && $i==1)
						{
						$data_array_wo_dtls="(".$id_wo_dtls.",".$id_dtls.",'".$order_color[$di]."','".$order_size[$di]."','".$order_qty[$di]."',".($wo_qty[$di]*1).",'".($order_rate[$di]*1)."',".$order_uom[$di].",'".$final_wo_qty[$di]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
						}
						else
						{
						$data_array_wo_dtls.=",(".$id_wo_dtls.",".$id_dtls.",'".$order_color[$di]."','".$order_size[$di]."','".$order_qty[$di]."',".($wo_qty[$di]*1).",'".($order_rate[$di]*1)."','".$order_uom[$di]."','".$final_wo_qty[$di]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
						}
						
						
						$id_wo_dtls++;
					}
					
					//order dtls end -----------------------------------
					$id_dtls++;

				}
			}


		}
		
	   	// echo "10**".$data_array_dtls; die;
		//    echo "10**"."INSERT INTO piece_rate_wo_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls;die;

		$rID1=sql_insert("piece_rate_wo_mst",$field_array_mst,$data_array_mst,0);
		if($flag==1) 
		{
			if($rID1) $flag=1; else $flag=0; 
		} 

		$rID2=sql_insert("piece_rate_wo_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 

										// PIECE RATE WORK ORDER & BILL VARIABLE
		if ($rate_source ==2) //PO WISE
		{
			$rID3=sql_insert("piece_rate_wo_rate_dtls",$field_array_rate_dtls,$data_array_rate_dtls,0);
		}
		else //COLOR SIZE WISE
		{
			$rID3=sql_insert("piece_rate_wo_qty_dtls",$field_array_wo_dtls,$data_array_wo_dtls,0);
		}


		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		// echo "10** ".$rID2."**".$rID3;print_r($data_array_wo_dtls); die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$price_rate_wo_system_id[0]."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".$id."**".$price_rate_wo_system_id[0]."**0";
			}
			else
			{
				oci_rollback($con);
				echo "10**0**"."&nbsp;"."**0 rid1=".$rID1."**rid2=".$rID2."**rid3=".$rID3;
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
			
			$flag=1; 
			
			$field_array_mst="company_id*service_provider_id*wo_date*rate_for*attension*currence*exchange_rate*location*line_id*prod_reso_allo*rate_bill_var*remarks*updated_by*update_date";
			$data_array_mst="".$cbo_company_id."*".$txt_service_provider_id."*".$txt_wo_date."*".$cbo_rate_for."*".$txt_attention."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_location."*".$cbo_line_id."*".$prod_reso_allo."*'".$rate_source."'*".$txt_remarks_mst."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
			//-----------------------------------------------------			
			$field_array_dtls_up="order_source*ord_recev_company*job_id*order_id*buyer_id*item_id*style_ref*color_type*wo_qty*uom*avg_rate*rate_history*amount*remarks*updated_by*update_date";
			$field_array_dtls="id, mst_id, order_source,ord_recev_company,job_id, order_id, buyer_id, item_id, style_ref,color_type,wo_qty, uom, avg_rate,rate_history, amount, remarks, inserted_by, insert_date, status_active, is_deleted";
			$id_dtls=return_next_id( "id", "piece_rate_wo_dtls", 1 ) ;
			 
			if ($rate_source == 2) //PO WISE
			{ 
				$field_array_rate_dtls="id,mst_id,dtls_id,lib_sew_op_id,rate,inserted_by,insert_date,status_active,is_deleted";
			}
			else //Color Size Wise
			{
				$wo_next_id=return_next_id( "id", "piece_rate_wo_qty_dtls", 1 ) ; 

			}

			$prev_dtls_id_arr=return_library_array( "select id, id as ids from piece_rate_wo_dtls where status_active=1 and is_deleted=0 and mst_id=$update_id",'id','ids');
			
			$tot_rows=str_replace("'","",$tot_rows);
			$f=1;$df=1;

			for($i=1; $i<=$tot_rows; $i++)
			{

				$cbo_order_source='cboOrderSource_'.$i;
				$cbo_ord_rceve_comp_id='cboOrdRceveCompId_'.$i;
				$txtjobid='txtjobid_'.$i;
				$txtorderid='txtorderid_'.$i;
				
				$txtbuyerid='txtbuyerid_'.$i;
				$txtitemid='txtitemid_'.$i;
				$txtstyle='txtstyle_'.$i;
				$colortype='colortype_'.$i;
				$txtwoqty='txtwoqty_'.$i;
				$txtavgrate='txtavgrate_'.$i;
				$rate_history='txtRate_'.$i;
				$txtremarks='txtremarks_'.$i;
				
				$cbodtlsuom='cbodtlsuom_'.$i;
				$txtdtlamount='txtdtlamount_'.$i;
				$details_update_id='detailsUpdateId_'.$i;
				
				$cbo_order_source=str_replace("'",'',$$cbo_order_source);
				$cbo_ord_rceve_comp_id=str_replace("'",'',$$cbo_ord_rceve_comp_id);
				$txtjobid=str_replace("'","",$$txtjobid);
				$txtorderid=str_replace("'","",$$txtorderid);
				$txtbuyerid=str_replace("'","",$$txtbuyerid);
				$txtitemid=str_replace("'","",$$txtitemid);
				$txtstyle=str_replace("'","",$$txtstyle);
				$colortype=str_replace("'","",$$colortype);
				$txtwoqty=str_replace("'","",$$txtwoqty);
				$txtavgrate=str_replace("'","",$$txtavgrate);
				$txtremarks=str_replace("'","",$$txtremarks);
				$cbodtlsuom=str_replace("'","",$$cbodtlsuom);
				$txtdtlamount=str_replace("'","",$$txtdtlamount);
				$rate_history=str_replace("'","",$$rate_history);

			
			
				if(str_replace("'","",$$details_update_id)!="")
				{ 
					//this is for update dels
					$all_dtls_id[]=str_replace("'","",$$details_update_id);
					$update_dtls_id[]=str_replace("'","",$$details_update_id);
					$data_array_dtls_up[str_replace("'","",$$details_update_id)] =explode("*",("'".$cbo_order_source."'*'".$cbo_ord_rceve_comp_id."'*'".$txtjobid."'*'".$txtorderid."'*'".$txtbuyerid."'*'".$txtitemid."'*'".$txtstyle."'*'".$colortype."'*'".$txtwoqty."'*'".$cbodtlsuom."'*'".$txtavgrate."'*'".$rate_history."'*'".$txtdtlamount."'*'".$txtremarks."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'")); 
				}
				else
				{
					//this is for news insert dels	
					if($txtwoqty!="")
					{ 
						if($data_array_dtls=='')
						{
							$all_dtls_id[]=$id_dtls;
							$data_array_dtls="('".$id_dtls."',".$update_id.",'".$cbo_order_source."','".$cbo_ord_rceve_comp_id."','".$txtjobid."','".$txtorderid."','".$txtbuyerid."','".$txtitemid."','".$txtstyle."','".$colortype."','".$txtwoqty."','".$cbodtlsuom."','".$txtavgrate."','".$rate_history."',".$txtdtlamount.",'".$txtremarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
						}
						else
						{
							$all_dtls_id[]=$id_dtls;
							$data_array_dtls.=",('".$id_dtls."',".$update_id.",'".$cbo_order_source."','".$cbo_ord_rceve_comp_id."','".$txtjobid."','".$txtorderid."','".$txtbuyerid."','".$txtitemid."','".$txtstyle."','".$colortype."','".$txtwoqty."','".$cbodtlsuom."',".$txtavgrate.",'".$rate_history."',".$txtdtlamount.",'".$txtremarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
						}
							
						$f++;
					}
				}

			
				//-----------------------------------------------------			
			
				
				if ($rate_source == 2)  //PO WISE
				{
					$rate_history_arr = array();
					$rate_history_arr = explode('@@',$rate_history);  
					$kk =0;
					foreach ($rate_history_arr as $data) 
					{
						$rate_dtls_id = return_next_id_by_sequence("piece_rate_wo_rate_dtls_seq", "piece_rate_wo_rate_dtls", $con);
						$pair = explode ('#', $data);
						$lib_sew_op_id = str_replace("'","",$pair[0]);
						$rate	= str_replace("'","",$pair[1]);
						// echo "10**".$rate; die;
						if($kk==0 && $i==1)
						{
							$data_array_rate_dtls="(".$rate_dtls_id.",".$update_id.",".$id_dtls.",'".$lib_sew_op_id."','".$rate."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
						}
						else
						{
							$data_array_rate_dtls.=",(".$rate_dtls_id.",".$update_id.",".$id_dtls.",'".$lib_sew_op_id."','".$rate."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
						}
						$kk++;
					}
					

				}
				else //Color Size Wise
				{ 
					//bulk_update_sql_statement

					$field_array_wo_dtls_up="color_id*size_id*wo_qty*rate*uom*final_wo_qty*updated_by*update_date";
					$field_array_wo_dtls="id, dtls_id, color_id, size_id, order_qty, wo_qty, rate, uom,final_wo_qty, inserted_by, insert_date,status_active,is_deleted";
					
					$txt_order_qty_history="txtOrderQtyHistory_".$i;  
					list($dtls_history,$order_qty,$wo_qty,$order_rate,$order_uom,$final_wo_qty,$order_color,$order_size,$up_ids)=explode("~~",$$txt_order_qty_history);
					
					
					$dtls_history=explode("__",$dtls_history);
					$up_id_arr=explode(",",str_replace("'","",$up_ids));
					$wo_qty=explode(",",str_replace("'","",$wo_qty));
					$order_rate=explode(",",str_replace("'","",$order_rate));
					$order_uom=explode(",",str_replace("'","",$order_uom));
					$final_wo_qty=explode(",",str_replace("'","",$final_wo_qty));
					$order_color=explode(",",str_replace("'","",$order_color));
					$order_size=explode(",",str_replace("'","",$order_size));
					$order_qty=explode(",",str_replace("'","",$order_qty));
				
				
				
					for($s=0; $s < $dtls_history[6]; $s++)
					{   
						if($up_id_arr[$s])
						{
							$id_arr[]=str_replace("'","",$up_id_arr[$s]);
							$data_array_wo_dtls_up[str_replace("'","",$up_id_arr[$s])] = explode("*",("'".$order_color[$s]."'*'".$order_size[$s]."'*'".$wo_qty[$s]."'*'".$order_rate[$s]."'*'".$order_uom[$s]."'*'".$final_wo_qty[$s]."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						}
						else
						{ 
							$wo_next_id+=1;
							// news inser wo rate data;	
						
							if($data_array_wo_dtls=='')
							{ 
								$data_array_wo_dtls="(".$wo_next_id.",".$id_dtls.",'".$order_color[$s]."','".$order_size[$s]."','".$order_qty[$s]."',".$wo_qty[$s].",".$order_rate[$s].",'".$order_uom[$s]."',".$final_wo_qty[$s].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
							}
							else
							{  
								$data_array_wo_dtls.=",(".$wo_next_id.",".$id_dtls.",'".$order_color[$s]."','".$order_size[$s]."',".$order_qty[$s].",".$wo_qty[$s].",".$order_rate[$s].",'".$order_uom[$s]."',".$final_wo_qty[$s].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
							}
							
						}
					
					}
					if(str_replace("'","",$$details_update_id)==""){$id_dtls++;}
				}	
			}

			
			// echo $con; 
			$flag_str = ''	;
			$rID1=sql_update("piece_rate_wo_mst",$field_array_mst,$data_array_mst,"id","".$update_id."",1);
			//echo "10**testing**$rID1";oci_rollback($con);disconnect($con);die;
			if($flag==1) 
			{
				if($rID1) $flag=1; else $flag=0; 
				if($rID1) $flag_str .='rID1-1*'; else $flag_str .='rID1-0*'; 
			} 
				
			$rID2=execute_query(bulk_update_sql_statement("piece_rate_wo_dtls", "id",$field_array_dtls_up,$data_array_dtls_up,$update_dtls_id ));
			if($flag==1 && count($update_dtls_id)>0) 
			{
				if($rID2) $flag=1; else $flag=0; 
				if($rID2) $flag_str.='rID2-1*'; else $flag_str.='rID2-0*';  
			} 
			//news insert;
			if($data_array_dtls!=''){$rID2_insert=sql_insert("piece_rate_wo_dtls",$field_array_dtls,$data_array_dtls,0);}
			if($data_array_dtls!='') 
			{
				if($rID2_insert) $flag=1; else $flag=0; 
				if($rID2_insert) $flag_str.='rID2_insert-1*'; else $flag_str.='rID2_insert-0*';  
			}		
			
			if ($rate_source == 2) //PO WISE
			{
				$rateDelete = execute_query("DELETE from piece_rate_wo_rate_dtls where mst_id=$update_id",1);
				if ($flag==1) 
				{
					if($rateDelete) $flag=1; else $flag=0;  
					if($rateDelete) $flag_str.='rateDelete-1*'; else $flag_str.='rateDelete-0*'; 
				}
				
				$rID3=sql_insert("piece_rate_wo_rate_dtls",$field_array_rate_dtls,$data_array_rate_dtls,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0;  
					if($rID3) $flag_str.='rID3-11*'; else $flag_str.= "INSERT INTO piece_rate_wo_rate_dtls (".$field_array_rate_dtls.") VALUES ".$data_array_rate_dtls.'rID3-00*'; 
				}  
			}
			else //Color Size Wise
			{
				$rID3=execute_query(bulk_update_sql_statement("piece_rate_wo_qty_dtls", "id",$field_array_wo_dtls_up,$data_array_wo_dtls_up,$id_arr )); 

				
				if($flag==1 && count($id_arr)>0) 
				{
					if($rID3) $flag=1; else $flag=0;  
					if($rID3) $flag_str.='rID3-1*'; else $flag_str.='rID3-0*'; 
				} 
				
				// new insert;
				if($data_array_wo_dtls!=''){$rID3_insert=sql_insert("piece_rate_wo_qty_dtls",$field_array_wo_dtls,$data_array_wo_dtls,0);}
				if($data_array_wo_dtls!='') 
				{
					if($rID3_insert) $flag=1; else $flag=0; 
					if($rID3) $flag_str.='rID32-1*'; else $flag_str.='rID32-0*'; 
				}     			
				$delete_dtls_id_arr = array_diff($prev_dtls_id_arr, $update_dtls_id);
				// echo "10**";print_r($delete_dtls_id_arr);die();
			}		

			$delete1 = execute_query("update piece_rate_wo_dtls set is_deleted=1,status_active=0,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where mst_id=$update_id and id not in(".implode(',',$all_dtls_id).")", 0);

			if ($rate_source == 2) //PO WISE
			{
				
			}
			else //Color Size Wise
			{
				$delete2 = execute_query("update piece_rate_wo_qty_dtls set is_deleted=1,status_active=0,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where dtls_id in(".implode(',',$delete_dtls_id_arr).")", 0);
			}

			// echo "10**".implode(",",$id_arr)."**".implode(",", $all_dtls_id)."**update piece_rate_wo_dtls set is_deleted=1,status_active=0,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where mst_id=$update_id and id not in(".implode(',',$all_dtls_id).")";die();
			//echo "10**testing";oci_rollback($con);disconnect($con);die;		
		
    	
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
    			echo "10**0**0**1";
    		}
    	}
    	else if($db_type==2 || $db_type==1 )
    	{
    		if($flag==1)
    		{
    			oci_commit($con);  
    			echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
    		}
    		else
    		{
    			oci_rollback($con);
    			echo "10**0**0**1".$flag_str."***";
    		}
    	}
    	disconnect($con);
    	die;
	}
	else if ($operation==2)   // Delete Here
	{

	}
}









?>