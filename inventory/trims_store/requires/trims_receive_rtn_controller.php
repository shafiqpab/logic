<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');


if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, supplier_id, company_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];

if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)"; 
}
if ($supplier_id !='') {
    $supplier_credential_cond = " and a.id in($supplier_id)";
}

if ($company_location_id !='') {
    $location_credential_cond = " and id in($company_location_id)";
}

if($action=="varible_inventory")
{
	$sql_variable_inventory=sql_select("select id, independent_controll, rate_optional, is_editable, rate_edit  from variable_settings_inventory where company_name=$data and variable_list=20 and status_active=1 and menu_page_id=49");
	if(count($sql_variable_inventory)>0)
	{
		echo "1**".$sql_variable_inventory[0][csf("independent_controll")]."**".$sql_variable_inventory[0][csf("rate_optional")]."**".$sql_variable_inventory[0][csf("is_editable")]."**".$sql_variable_inventory[0][csf("rate_edit")];
	}
	else
	{
		echo "0**".$sql_variable_inventory[0][csf("independent_controll")]."**".$sql_variable_inventory[0][csf("rate_optional")]."**".$sql_variable_inventory[0][csf("is_editable")]."**".$sql_variable_inventory[0][csf("rate_edit")];
	}
	/*$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name=$data and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo "**".$variable_inventory;
	$variable_lot=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
	echo "**".$variable_lot;*/
	die;
}
//--------------------------------------------------------------------------------------------


/*if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 145, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}
*/
if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/trims_receive_rtn_controller",$data);
}
if($action=="load_drop_down_knitting_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_return_to", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Knit Company--", "$company_id", "","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_return_to", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", 0, "","");
	}
	else
	{
		echo create_drop_down( "cbo_return_to", 170, $blank_array,"",1, "--Select--", 0, "" );
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
			/*if( form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}*/			
			show_list_view ( $('#txt_search_common').val()+'_'+$('#cbo_search_by').val()+'_'+<? echo $cbo_company_id; ?>+'_'+$('#cbo_buyer_name').val()+'_'+'<? echo $all_po_id; ?>'+'_'+$('#cbo_year').val(), 'create_po_search_list_view', 'search_div', 'trims_receive_rtn_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}
		
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var buyer_name='';
		
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($('#search'+i).css('display')!='none')
				{
					js_set_value( i );
				}
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
			var color=document.getElementById('search' + str ).style.backgroundColor;
			var txt_buyer=$('#txt_buyer' + str).val();
			
			//if(color!='yellow' && selected_id.length>0 && $('#txt_buyer' + str).css('display') != 'none')
			if(color!='yellow' && selected_id.length>0 && $('#txt_buyer' + str).is(':visible'))
			{
				if(buyer_name=="")
				{
					buyer_name=txt_buyer;
				}
				else if(buyer_name*1!=txt_buyer*1)
				{
					alert("Buyer Mix Not Allowed");
					return;
				}
			}
			
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
			
			buyer_id=$('#txt_buyer' + str).val();

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);
			$('#hide_buyer').val(buyer_id);
			
		}
		
		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			$('#hide_buyer').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
		
    </script>

</head>
<body>
	<div align="center">
        <form name="searchdescfrm" id="searchdescfrm" autocomplete=off>
            <fieldset style="width:780px;margin-left:5px">
                <input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
                <input type="hidden" name="hide_buyer" id="hide_buyer" class="text_boxes" value="">
                <table cellpadding="0" cellspacing="0" width="630" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th>Buyer</th>
                        <th>Year</th>
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
                                echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", 0, "","" ); 
                            ?>       
                        </td>
                        <td>
                            <?
                            echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                        <td align="center">	
                            <?
                                $search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Style Ref.",4=>"Int. Ref.");
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
    </div>
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
	else if($search_by==3)
		$search_field='a.style_ref_no';	
	else
	   $search_field='b.grouping';	
		
		
	$company_id =$data[2];
	$buyer_id =$data[3];
	
	$all_po_id=$data[4];
	$year_filter=$data[5];

	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else 
		$po_id_cond="";
	
	$hidden_po_id=explode(",",$all_po_id);
	
	if(str_replace("'","",$buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
	}
	
	if($db_type==0){
        $year_field = "YEAR(a.insert_date) as year,";
        $year_cond = " and year(a.insert_date)='".$year_filter."'";
    }else if($db_type==2){
        $year_field="to_char(a.insert_date,'YYYY') as year,";
        $year_cond=" and to_char(a.insert_date,'YYYY')='".$year_filter."'";
    }else{
        $year_field="";//defined Later
        $year_cond="";//defined Later
    }
	
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	
	$sql = "select a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, $year_field b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $year_cond";

	// echo $sql;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="778" class="rpt_table" style="margin-left:2px">
            <thead>
                <th width="40">SL</th>
                <th width="110">Buyer</th>
                <th width="60">Year</th>
                <th width="70">Job No</th>
                <th width="110">Style Ref.</th>
                <th width="110">PO No</th>
                <th width="90">PO Quantity</th>
                <th width="60">UOM</th>
                <th>Shipment Date</th>
            </thead>
        </table>
        <div style="width:778px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="tbl_list_search" align="left">
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
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                        <td width="40" align="center">
                            <? echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
                            <input type="hidden" name="txt_buyer" id="txt_buyer<?php echo $i ?>" value="<? echo $selectResult[csf('buyer_name')]; ?>"/>
                        </td>
                        <td width="110"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>	
                        <td align="center" width="60"><p><? echo $selectResult[csf('year')]; ?></p></td>
                        <td width="70"><p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?>&nbsp;</td> 
                        <td width="60" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
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


if($action=="booking_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_company_id."==".$all_po_id;die;
	
	?>
    <script>
	
	function js_set_value(str)
	{
		var str_ref=str.split("_");
		$('#booking_id').val(str_ref[0]);
		$('#booking_no').val(str_ref[1]);
		parent.emailwindow.hide();
	}
	
	</script>
    <?
	
	if($cbo_receive_basis==2)
	{
		$sql = "select a.id, a.booking_no 
		from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c 
		where a.booking_no=b.booking_no and b.po_break_down_id=c.id and b.po_break_down_id in($all_po_id) and a.company_id=$cbo_company_id and a.booking_type in(2,8) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id, a.booking_no";
	}
	else
	{
		$sql="select d.id, d.pi_number as booking_no
		from wo_po_break_down a, order_wise_pro_details b,  inv_transaction c, com_pi_master_details d
		where a.id=b.po_breakdown_id and b.trans_id=c.id and c.pi_wo_batch_no=d.id and c.receive_basis=1 and b.po_breakdown_id in($all_po_id) and c.receive_basis=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by d.id, d.pi_number";
	}
	
	//echo $sql;
	$sql_result=sql_select($sql);
	?>
	<div style="width:100%">
		<input type="hidden" id="booking_id" name="booking_id" />
		<input type="hidden" id="booking_no" name="booking_no" />
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" style="margin-left:2px">
			<thead>
				<th width="50">SL</th>
				<th>Booking/PI No</th>
			</thead>
			<tbody>
			<?
			$i=1;
			foreach($sql_result as $row)
			{
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
						
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf("id")]."_".$row[csf("booking_no")]; ?>')">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><p><? echo $row[csf("booking_no")]; ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
			?>
			</tbody>
		</table>
		
	</div>           
	<?
	
	exit();

}




if($action=="create_itemDesc_search_list_view")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$prod_id=$data[1];
	$store_id=$data[2];
	$booking_id=$data[3];
	$booking_no=trim(str_replace("'","",$data[4]));
	$receive_basis=$data[5];
	$all_po_id=$po_id;
	//echo $booking_no;die;
	
	$sql_order = "select c.po_breakdown_id, b.prod_id  
	from inv_receive_master a, inv_transaction b, order_wise_pro_details c
	where a.id=b.mst_id and b.id=c.trans_id and a.receive_basis=$receive_basis and b.pi_wo_batch_no=$booking_id and b.booking_no='$booking_no' and b.item_category=4 and a.entry_form=24 and c.entry_form=24 and b.transaction_type=1 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	//b.pi_wo_batch_no=$booking_id and b.booking_no='$booking_no'
	//echo $sql_order."<br>";
	
	$sql_order_result=sql_select($sql_order);
	$all_prod_id="";
	foreach($sql_order_result as $row)
	{
		$all_prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
	}
	//$all_po_id=chop($all_po_id,",");
	$all_prod_id=implode(",",$all_prod_id_arr);
	if($all_po_id=="") $all_po_id=0;
	if($all_prod_id=="") $all_prod_id=0;
	
	
	$cumilite_issue_sql=sql_select("select b.prod_id, b.trans_type, b.quantity, c.id as trans_id, c.transaction_type, c.cons_quantity, c.cons_amount  
	from order_wise_pro_details b, inv_transaction c  
	where b.trans_id=c.id and b.status_active=1 and b.is_deleted=0 and b.entry_form in(24,25,49,73,78,112) and b.po_breakdown_id in ($all_po_id) and b.prod_id in($all_prod_id) and c.prod_id in($all_prod_id) and c.store_id=$store_id");
	$cumilite_issue_data=array();
	foreach($cumilite_issue_sql as $row)
	{
		if($row[csf("trans_type")]==2)
		{
			$cumilite_issue_data[$row[csf("prod_id")]]["issue_qnty"]+=$row[csf("quantity")];
		}
		elseif($row[csf("trans_type")]==3)
		{
			$cumilite_issue_data[$row[csf("prod_id")]]["rcv_rtn_qnty"]+=$row[csf("quantity")];
		}
		elseif($row[csf("trans_type")]==4)
		{
			$cumilite_issue_data[$row[csf("prod_id")]]["issue_rtn_qnty"]+=$row[csf("quantity")];
		}
		elseif($row[csf("trans_type")]==6)
		{
			$cumilite_issue_data[$row[csf("prod_id")]]["trans_out_qnty"]+=$row[csf("quantity")];
		}
		
		if($trans_ids_check[$row[csf("trans_id")]]=="")
		{
			$trans_ids_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
			if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
			{
				$cumilite_issue_data[$row[csf("prod_id")]]["cons_bal_qnty"]+=$row[csf("cons_quantity")];
				$cumilite_issue_data[$row[csf("prod_id")]]["cons_bal_amt"]+=$row[csf("cons_amount")];
			}
			else
			{
				$cumilite_issue_data[$row[csf("prod_id")]]["cons_bal_qnty"]-=$row[csf("cons_quantity")];
				$cumilite_issue_data[$row[csf("prod_id")]]["cons_bal_amt"]-=$row[csf("cons_amount")];
			}
		}
	}
	
	$po_no_arr = return_library_array("select id, po_number from wo_po_break_down where id in($all_po_id)","id","po_number");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group where status_active=1 and is_deleted=0");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	unset($data_array);
	
	$sql = "select a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, b.id as prop_id, b.po_breakdown_id, b.quantity as recv_qty, c.id as trans_id, c.cons_quantity as recv_cons_qty, c.cons_amount, d.booking_no, 1 as type  
	from product_details_master a, order_wise_pro_details b, inv_transaction c, inv_trims_entry_dtls d
	where a.id=b.prod_id and b.trans_id=c.id and c.id=d.trans_id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1) and b.entry_form in(24) and b.po_breakdown_id in ($all_po_id) and b.prod_id in($all_prod_id) and c.prod_id in($all_prod_id) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0
	union all
	select a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, b.id as prop_id, b.po_breakdown_id, b.quantity as recv_qty, c.id as trans_id, c.cons_quantity as recv_cons_qty, c.cons_amount, null as booking_no, 2 as type  
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(5) and b.entry_form in(78,112) and b.po_breakdown_id in ($all_po_id) and b.prod_id in($all_prod_id) and c.prod_id in($all_prod_id) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0";
	
	//echo $sql;
	
	$result = sql_select($sql);
	$item_order_data=$item_order_check=$item_tranaction_check=array();
	foreach($result as $row)
	{
		if($item_order_check[$row[csf("id")]][$row[csf("prop_id")]]=="")
		{
			$item_order_check[$row[csf("id")]][$row[csf("prop_id")]]=$row[csf("prop_id")];
			
			$item_order_data[$row[csf("id")]]["id"]=$row[csf("id")];
			$item_order_data[$row[csf("id")]]["item_group_id"]=$row[csf("item_group_id")];
			$item_order_data[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
			$item_order_data[$row[csf("id")]]["brand_supplier"]=$row[csf("brand_supplier")];
			$item_order_data[$row[csf("id")]]["color"]=$row[csf("color")];
			$item_order_data[$row[csf("id")]]["gmts_size"]=$row[csf("gmts_size")];
			$item_order_data[$row[csf("id")]]["item_color"]=$row[csf("item_color")];
			$item_order_data[$row[csf("id")]]["item_size"]=$row[csf("item_size")];
			$item_order_data[$row[csf("id")]]["current_stock"]=$row[csf("current_stock")];
			$item_order_data[$row[csf("id")]]["prop_id"]=$row[csf("prop_id")];
			$item_order_data[$row[csf("id")]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
			$item_order_data[$row[csf("id")]]["recv_qty"]+=$row[csf("recv_qty")];
		}
		
		if($item_tranaction_check[$row[csf("id")]][$row[csf("trans_id")]]=="")
		{
			$item_tranaction_check[$row[csf("id")]][$row[csf("trans_id")]]=$row[csf("trans_id")];
			//$item_order_data[$row[csf("id")]]["recv_cons_qty"]+=$row[csf("recv_cons_qty")];
			//$item_order_data[$row[csf("id")]]["cons_amount"]+=$row[csf("cons_amount")];
			//if($booking_no==$row[csf("booking_no")] && $row[csf("type")]==1)
			//{
				//$item_order_data[$row[csf("id")]]["booking_cons_qty"]+=$row[csf("recv_cons_qty")];
				//$item_order_data[$row[csf("id")]]["booking_cons_amount"]+=$row[csf("cons_amount")];
			//}
			$item_order_data[$row[csf("id")]]["booking_cons_qty"]+=$row[csf("recv_cons_qty")];
			$item_order_data[$row[csf("id")]]["booking_cons_amount"]+=$row[csf("cons_amount")];
		}
	}
	//echo '<pre>';print_r($item_order_data);

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="300" class="rpt_table" id="table_header">
    	<thead>
			<th width="30">Prod. ID</th>
			<th width="170">Item Desc.</th>               
            <th>Stock</th>
		</thead>
    </table>
		
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="300" class="rpt_table" id="tbl_list_search">  
		<?
		$i=1;
		foreach ($item_order_data as $row)
		{
			$booking_cons_rate=$row["booking_cons_amount"]/$row["booking_cons_qty"];
			$booking_cons_qty=$row["booking_cons_qty"];
			$po_id_arr=array_unique(explode(",",chop($row[("po_breakdown_id")],",")));
			$all_po_no=$all_po_ids="";
			foreach($po_id_arr as $po_id)
			{
				$all_po_no.=$po_no_arr[$po_id].",";
				$all_po_ids.=$po_id.",";
			}
			$all_po_no=chop($all_po_no,",");$all_po_ids=chop($all_po_ids,",");
				  
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cu_issue=$balance=0;
			$current_stock=$row[('current_stock')];
			$cu_issue=($cumilite_issue_data[$row[('id')]]["issue_qnty"]-$cumilite_issue_data[$row[('id')]]["issue_rtn_qnty"])*$trim_group_arr[$row[('item_group_id')]]['conversion_factor'];
			if($cu_issue=="") $cu_issue=0;
			$receive_qnty=(($row[('recv_qty')]-$cumilite_issue_data[$row[('id')]]["rcv_rtn_qnty"]-$cumilite_issue_data[$row[('id')]]["trans_out_qnty"])*$trim_group_arr[$row[('item_group_id')]]['conversion_factor']);
			$trim_rcv=$row[('recv_qty')]*$trim_group_arr[$row[('item_group_id')]]['conversion_factor'];
			$balance=$receive_qnty-$cu_issue;
			$cumilitive_issue=$cumilite_issue_data[$row[('id')]]["rcv_rtn_qnty"]*$trim_group_arr[$row[('item_group_id')]]['conversion_factor'];
			$cons_rate=0;
			if($cumilite_issue_data[$row[('id')]]["cons_bal_amt"]!=0 && $cumilite_issue_data[$row[('id')]]["cons_bal_qnty"])
			{
				$cons_rate=$cumilite_issue_data[$row[('id')]]["cons_bal_amt"]/$cumilite_issue_data[$row[('id')]]["cons_bal_qnty"];
			}
			//$current_stock=$current_stock_arr[$row[('id')]];
			
			$data=$row[('id')]."**".$row[('item_group_id')]."**".$row[('product_name_details')]."**".$row[('item_color')]."**".$row[('color')]."**".$row[('item_size')]."**".$row[('gmts_size')]."**".$row[('brand_supplier')]."**".$trim_group_arr[$row[('item_group_id')]]['uom']."**".$rack."**".$shelf."**".$row[('item_color')]."**".number_format($current_stock,2,".","")."**".number_format($cumilitive_issue,2,".","")."**".number_format($balance,2,".","")."**".number_format($trim_rcv,2,".","")."**".$trim_group_arr[$row[('item_group_id')]]['conversion_factor']."**".$all_po_no."**".$all_po_ids."**".number_format($cons_rate,6,".","")."**".number_format($booking_cons_rate,6,".","")."**".number_format($booking_cons_qty,6,".","");
			if(number_format($balance,2,'.','')>0)
			{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='set_form_data("<? echo $data; ?>")' > 
					<td width="30"><? echo $row[('id')]; ?></td>
					<td width="170"><p><? echo $row[('product_name_details')]; ?></p></td>             
					<td align="right" title="<? echo $cumilite_issue_data[$row[('id')]]["issue_qnty"]*$trim_group_arr[$row[('item_group_id')]]['conversion_factor']."==".$cumilite_issue_data[$row[('id')]]["rcv_rtn_qnty"]*$trim_group_arr[$row[('item_group_id')]]['conversion_factor']."==".$cumilite_issue_data[$row[('id')]]["trans_out_qnty"]*$trim_group_arr[$row[('item_group_id')]]['conversion_factor']; ?>"><p><? echo number_format($balance,2,'.',''); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
		}
		?>
	</table>
	<?	
    exit();
}

if($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
	
		/*function distribute_qnty()
		{
			var tot_po_qnty=$('#tot_po_qnty').val()*1;
			var txt_prop_issue_qnty=$('#txt_prop_issue_qnty').val()*1;
			var tblRow = $("#tbl_list_search tbody tr").length;
			var len=totalIssue=0;
			
			
			if(txt_prop_issue_qnty>0)
			{
				$('#txt_prop_ship_trims_qty').val("").attr('disabled',true);
			}
			else
			{
				$('#txt_prop_ship_trims_qty').attr('disabled',false);
			}
			
			if(txt_prop_issue_qnty>0)
			{
				$("#tbl_list_search tbody").find('tr').each(function()
				{
					var txtPoQnty_placeholder=$(this).find('input[name="txtIssueQnty[]"]').attr('placeholder')*1;
					len=len+1;
					
					var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
					var perc=(po_qnty/tot_po_qnty)*100;
					
					var issue_qnty=(perc*txt_prop_issue_qnty)/100;
					
					totalIssue = totalIssue*1+issue_qnty*1;
					totalIssue = totalIssue.toFixed(2);						
					if(tblRow==len)
					{
						var balance = txt_prop_issue_qnty-totalIssue;
						if(balance!=0) issue_qnty=issue_qnty+(balance);							
					}
					//alert(issue_qnty+"=="+txtPoQnty_placeholder);
					if(issue_qnty>txtPoQnty_placeholder)
					{
						$(this).find('input[name="txtIssueQnty[]"]').val("");
						//alert("Issue Quantity Not Allow Over Balance Quantity");
					}
					else
					{
						$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(2));
					}
					

				});
			}
			
			calculate_total();
		}*/
		
		
		function distribute_qnty()
		{
			var tot_balance_qnty=$('#tot_balance_qnty').val()*1;
			var txt_prop_issue_qnty=$('#txt_prop_issue_qnty').val()*1;
			var tblRow = $("#tbl_list_search tbody tr").length;
			
			if(txt_prop_issue_qnty>0)
			{
				$('#txt_prop_ship_trims_qty').val("").attr('disabled',true);
			}
			else
			{
				$('#txt_prop_ship_trims_qty').attr('disabled',false);
			}
			
			
			if(txt_prop_issue_qnty>tot_balance_qnty)
			{
				alert("Return Quantity Not Allow Over Stock Quantity.");
				$('#txt_prop_issue_qnty').val("");
				$('#txt_prop_issue_qnty').focus();
				$("#tbl_list_search tbody").find('tr').each(function()
				{
					$(this).find('input[name="txtIssueQnty[]"]').val("");
				});
				calculate_total();
				return;
			}
			
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				$(this).find('input[name="txtIssueQnty[]"]').val("");
			});
			
			
			var tbl_length=$('#tbl_list_search tbody tr').length*1;
			var balance_quantity=txt_prop_issue_qnty;
			
			if(txt_prop_issue_qnty>0)
			{
				var row_num=1;
				$("#tbl_list_search tbody").find('tr').each(function()
				{
					var txtPoQnty_placeholder=$(this).find('input[name="txtIssueQnty[]"]').attr('placeholder')*1;
					var issue_qnty=(txt_prop_issue_qnty/tot_balance_qnty)*txtPoQnty_placeholder;
					
					if(tbl_length==row_num)
					{
						$(this).find('input[name="txtIssueQnty[]"]').val(balance_quantity.toFixed(6));
					}
					else
					{
						$(this).find('input[name="txtIssueQnty[]"]').val(issue_qnty.toFixed(6));
						balance_quantity=balance_quantity-issue_qnty.toFixed(6);
					}
					row_num++;
				});
			}
			
			calculate_total();
		}
		
		function distribute_ship_qnty()
		{
			var tot_po_qnty=$('#tot_po_qnty').val()*1;
			var txt_prop_issue_qnty=$('#txt_prop_ship_trims_qty').val()*1;
			var tblRow = $("#tbl_list_search tbody tr").length;
			var len=totalIssue=totalTrims=0;
			var balance =txt_prop_issue_qnty;
			
			
			if(txt_prop_issue_qnty>0)
			{
				$('#txt_prop_issue_qnty').val("").attr('disabled',true);
			}
			else
			{
				$('#txt_prop_issue_qnty').attr('disabled',false);
			}
			
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				$(this).find('input[name="txtIssueQnty[]"]').val("");
			});
			if(txt_prop_issue_qnty>0)
			{
				for(var i=1;i<=tblRow;i++)
				{
					var RcvBalance=$('#txtIssueQnty_'+i).attr('placeholder')*1;
					var trims_qnty=RcvBalance;
					if(RcvBalance>0 && txt_prop_issue_qnty>0)
					{
						if(balance<trims_qnty)
						{
							$('#txtIssueQnty_'+i).val(balance.toFixed(6));
							break;
						}
						else
						{
							$('#txtIssueQnty_'+i).val(trims_qnty.toFixed(6));
							balance=(balance*1)-(trims_qnty*1).toFixed(6);
							
						}
					}
				}
				
			}
			
			calculate_total();
		}
		
		function calculate_total()
		{
			var tblRow = $("#tbl_list_search tbody tr").length;
			var total_issue=0;
			for(var i=1;i<=tblRow;i++)
			{
				var issue_qnty=$('#txtIssueQnty_'+i).val()*1;
				total_issue=total_issue*1+issue_qnty;
			}
			
			$('#total_issue').html(total_issue.toFixed(6));
		}
		
		function fn_placeholde_check(i)
		{
			if($('#txtIssueQnty_'+i).val()*1>$('#txtIssueQnty_'+i).attr('placeholder')*1)
			{
				$('#txtIssueQnty_'+i).val("");
				alert("Issue Quantity Not Allow Over Balance Quantity");
				return;
			}
		}
		
		
		
		function fnc_close()
		{
			var save_string='';	 var tot_trims_qnty=''; var po_id_array = new Array(); var po_no='';
			var conversion_factor=$('#conversion_factor').val()*1;
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
				var txtIssueQnty=(($(this).find('input[name="txtIssueQnty[]"]').val()*1)/conversion_factor).toFixed(6);

				tot_trims_qnty=tot_trims_qnty*1+$(this).find('input[name="txtIssueQnty[]"]').val()*1;
				//alert($(this).find('input[name="txtIssueQnty[]"]').val()/conversion_factor);
				if(txtIssueQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtPoId+"_"+txtIssueQnty;
					}
					else
					{
						save_string+=","+txtPoId+"_"+txtIssueQnty;
					}
					
					if(jQuery.inArray( txtPoId, po_id_array) == -1 ) 
					{
						po_id_array.push(txtPoId);
						if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
					}
				}
			});
			
			$('#save_string').val( save_string );
			$('#tot_trims_qnty').val( tot_trims_qnty.toFixed(6) );
			$('#all_po_id').val( po_id_array );
			$('#all_po_no').val( po_no );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );	
			
			parent.emailwindow.hide();
		}
		
		
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:10px">
            <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
            <input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes" value="">
            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
            <input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
            <input type="hidden" name="conversion_factor" id="conversion_factor" class="text_boxes" value="<? echo $conversion_factor; ?>">
            <div style="width:600px; margin-top:10px" align="center">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="550" align="center">
                	<thead>
                    	<tr>
                            <th>&nbsp;&nbsp;&nbsp;</th>
                            <th><b>Proportionately</b></th>
                            <th><b>Ship Date Wise</b></th>
                        </tr>
                    </thead>
                    <tr>
                        <td width="250" align="right"><b>Total Issue Qty : &nbsp;&nbsp;</b></td>
                        <td><input type="text" name="txt_prop_issue_qnty" id="txt_prop_issue_qnty" class="text_boxes_numeric" style="width:120px" onBlur="distribute_qnty()" /></td>
                        <td align="center"><input type="text" name="txt_prop_ship_trims_qty" id="txt_prop_ship_trims_qty" class="text_boxes_numeric" style="width:100px" onBlur="distribute_ship_qnty()" /></td>
                    </tr>
                </table>
            </div>
			<div style="margin-left:30px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530">
                    <thead>
                        <th width="150">PO No</th>
                        <th width="100">Shipment Date</th>
                        <th width="120">PO Qnty</th>
                        <th>Issue Qnty</th>
                    </thead>
                </table>
                <div style="width:550px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530" id="tbl_list_search">
                        <tbody>
                        <?
                        $i=1; $tot_issue_qnty=''; $po_qnty_array=array();
                        
                        $explSaveData = explode(",",$save_data); 	
                        for($z=0;$z<count($explSaveData);$z++)
                        {
                            $po_wise_data = explode("_",$explSaveData[$z]);
                            $order_id=$po_wise_data[0];
                            $issue_qnty=$po_wise_data[1]*$conversion_factor;
                            $po_qnty_array[$order_id]=number_format($issue_qnty,6,".","");
                        }
						
                        //print_r($po_array);die;
						//echo $all_po_id;die;
						
                        if($all_po_id!="")
                        {
							
							$all_po_idd=explode(",",$all_po_id);
							$all_po_idd="'".implode("','", $all_po_idd)."'";
							//echo $all_po_idd;
							$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=$prod_id","conversion_factor");
							$check_rec_po=sql_select("select b.po_breakdown_id, sum((case when b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance  
							from inv_transaction a, order_wise_pro_details b 
							where a.id=b.trans_id and a.store_id=$cbo_store_name and b.po_breakdown_id in ($all_po_idd) and b.prod_id=$prod_id and b.trans_type in(1,2,3,4,5,6) and b.entry_form in(24,25,49,73,78,112) and a.status_active=1 and b.status_active=1 group by b.po_breakdown_id");
							foreach ($check_rec_po as $row)
							{
								$po_array[$row[csf('po_breakdown_id')]]=$row[csf('balance')]*$conversion_fac;
							}
							
							//print_r($po_array);
							
                            $po_sql="select b.id, a.buyer_name, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date 
							from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c 
							where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.trans_type in(1,5) and c.entry_form in(24,78) and b.id in ($all_po_id) and c.status_active=1 and c.is_deleted=0 group by b.id, a.buyer_name, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date order by b.pub_shipment_date";
					    }
                        //echo "<pre>";print_r($po_sql);

                        $nameArray=sql_select($po_sql);
                        foreach($nameArray as $row)
                        {
                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                                
                            $issue_qnty=$po_qnty_array[$row[csf('id')]];
                            $tot_issue_qnty+=$issue_qnty;
							$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
							$tot_po_qnty+=$po_qnty_in_pcs;
							$order_idd=$po_array[$row[csf('id')]];
							//echo $order_idd;
							if($order_idd!="")
	                        {
								$bgcolorr="green";
							}
							else
							{
								$bgcolorr="red";
							}
							
							//if($po_array[$row[csf('id')]]>0)
							//{
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                    <td width="150">
                                        <p style="color:<? echo $bgcolorr; ?>"><b><? echo $row[csf('po_number')]; ?></b></p>
                                        <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                        <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
                                    </td>
                                    <td align="center" width="100"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                    <td width="120" align="right">
                                        <? echo $po_qnty_in_pcs; ?>&nbsp;
                                        <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
                                    </td>
                                    <td align="right">
                                        <input type="text" name="txtIssueQnty[]" id="txtIssueQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:140px" onKeyUp="calculate_total();" value="<? echo $issue_qnty; ?>" placeholder="<? echo number_format(($po_array[$row[csf('id')]]+$issue_qnty),6,".",""); ?>" onBlur="fn_placeholde_check(<? echo $i; ?>)">
                                    </td>
                                </tr>
								<?
                                $i++;
								$total_place_holder_bal+=$po_array[$row[csf('id')]]+$issue_qnty;
							//}
                        }
                        ?>
                        </tbody>
                        <tfoot class="tbl_bottom">
                            <td colspan="3">Total</td>
                            <td id="total_issue"><? echo number_format($tot_issue_qnty,6,'.',''); ?></td>
                        </tfoot>
                    </table>
                </div>
                <table width="580">
                     <tr>
                        <td align="center" >
                            <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                             <input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo number_format($tot_po_qnty,6,".",""); ?>">
                            <input type="hidden" name="tot_balance_qnty" id="tot_balance_qnty" class="text_boxes" value="<? echo number_format($total_place_holder_bal,2,".",""); ?>">
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
	</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	unset($data_array);
	if($operation!=0) 
	{
		$max_recv_id = return_field_value("max(id) as max_id", "inv_transaction", "prod_id=$txt_prod_id and store_id=$cbo_store_name and transaction_type in (1,4,5) and status_active=1 and is_deleted=0", "max_id");      
		if($max_recv_id != "" && str_replace("'", "", $update_trans_id)>0)
		{
			if ($max_recv_id > str_replace("'", "", $update_trans_id)) 
			{
				echo "20**Next Transaction Found, Update Or Delete Not Allow";die;
			}
		}
	}
	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		$cbo_floor=str_replace("'","",$cbo_floor);
		$cbo_room=str_replace("'","",$cbo_room);
		$txt_rack=str_replace("'","",$txt_rack);
		$txt_shelf=str_replace("'","",$txt_shelf);
		$cbo_bin=str_replace("'","",$cbo_bin);
		if($store_update_upto==2)
		{
			$cbo_room=0;
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==3)
		{
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==4)
		{
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==5)
		{
			$cbo_bin=0;
		}
	}
	else
	{
		$cbo_floor=0;
		$cbo_room=0;
		$txt_rack=0;
		$txt_shelf=0;
		$cbo_bin=0;
	}

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		
		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id","inv_issue_master a, inv_transaction b","a.id=b.mst_id and a.id=$issue_mst_id and b.prod_id=$txt_prod_id and b.transaction_type=3"); 
		if($duplicate==1) 
		{
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			//check_table_status( $_SESSION['menu_id'], 0 );
			disconnect($con);
			die;
		}
		//------------------------------Check Duplicate END---------------------------------------//
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		//$all_po_id=str_replace("'","",$all_po_id);
		$conversion_factor=$trim_group_arr[str_replace("'","",$cbo_item_group)]['conversion_factor'];
		$sqlCon="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==6)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
				if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and c.bin_box=$cbo_bin" ;}
			}
			else if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			}
		}
		$store_order_sql=sql_select("SELECT sum((case when b.trans_type in (1,4,5) and c.transaction_type in (1,4,5) then  b.quantity else 0 end)-(case when b.trans_type in (2,3,6) and c.transaction_type in (2,3,6) then b.quantity else 0 end)) as balance, sum((case when b.trans_type in (1,4,5) and c.transaction_type in (1,4,5) then  b.order_amount else 0 end)-(case when b.trans_type in (2,3,6) and c.transaction_type in (2,3,6) then b.order_amount else 0 end)) as balance_amt 
		from order_wise_pro_details b, inv_transaction c  
		where b.trans_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.entry_form in(24,25,49,73,78,112) and b.po_breakdown_id in (".str_replace("'","",$all_po_id).") and c.store_id=$cbo_store_name and b.prod_id=$txt_prod_id  $sqlCon");
		$store_order_stock=$store_order_sql[0][csf("balance")]*$conversion_factor;
		$trim_stock=$store_order_sql[0][csf("balance")];
		$trim_stock_amt=$store_order_sql[0][csf("balance_amt")];
		$trim_ord_rate=0;
		if($store_order_sql[0][csf("balance")]!=0 && $trim_stock_amt!=0) $trim_ord_rate=$trim_stock_amt/$store_order_sql[0][csf("balance")];
		//echo $txt_return_qnty.'##'.$store_order_stock;
		if($txt_return_qnty>$store_order_stock)
		{
			
			echo "30**Return Quantity Not Over Order Stock."; disconnect($con); 
			die;
		}
		
		
 		if(str_replace("'","",$issue_mst_id)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_return_no);
			$id=$issue_mst_id;
			//issue master table UPDATE here START----------------------//	 cbo_store_name	cbo_return_source
 			$field_array_mst="company_id*issue_date*knit_dye_source*supplier_id*gate_pass_no*updated_by*update_date";
			$data_array_mst=$cbo_company_id."*".$txt_return_date."*".$cbo_return_source."*".$cbo_return_to."*".$txt_gate_pass_no."*'".$user_id."'*'".$pc_date_time."'";
		}
		else  	
		{	 
			//issue master table entry here START---------------------------------------//		
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			$new_return_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,'TRR',49,date("Y",time()) ));
 			$field_array_mst="id, issue_number_prefix, issue_number_prefix_num, issue_number, entry_form, item_category, company_id, issue_date, knit_dye_source, supplier_id, gate_pass_no, inserted_by, insert_date";
			$data_array_mst="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',49,4,".$cbo_company_id.",".$txt_return_date.",".$cbo_return_source.",".$cbo_return_to.",".$txt_gate_pass_no.",'".$user_id."','".$pc_date_time."')";
		}
		
		//adjust product master table START-------------------------------------//
		$sql = sql_select("select product_name_details,last_purchased_qnty,current_stock,stock_value,avg_rate_per_unit from product_details_master where id=$txt_prod_id");
		$presentStock=$available_qnty=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$product_name_details 	=$result[csf("product_name_details")];

			$stock_value 			=$result[csf("stock_value")];
			$avg_rate 				= $result[csf("avg_rate_per_unit")];	
		}
		$nowStock 		= $presentStock-str_replace("'","",$txt_return_qnty);
		$item_stock_value=str_replace("'","",$txt_return_qnty)*$avg_rate;
		$now_stock_value = 0;
		if ($nowStock != 0){
			$now_stock_value = $stock_value-$item_stock_value;			
		} 

		$field_array_prod="last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$data_array_prod=$txt_return_qnty."*".$nowStock."*".$now_stock_value."*'".$user_id."'*'".$pc_date_time."'";
		//transaction table insert here START--------------------------------//cbo_uom
		$avg_rate_amount=str_replace("'","",$txt_return_qnty)*str_replace("'","",$txt_cons_rate);
		 
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);				
		$field_array_trans = "id,mst_id,company_id,store_id,floor_id,room,rack,self,bin_box,prod_id,receive_basis,pi_wo_batch_no,item_category,transaction_type,transaction_date,cons_uom,cons_quantity,cons_rate,cons_amount,remarks,inserted_by,insert_date,rcv_rate,rcv_amount";
 		$data_array_trans = "(".$id_trans.",".$id.",".$cbo_company_id.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_prod_id.",".$cbo_receive_basis.",".$txt_booking_id.",4,3,".$txt_return_date.",".$cbo_uom.",".$txt_return_qnty.",".$txt_cons_rate.",'".$avg_rate_amount."',".$txt_remarks.",'".$user_id."','".$pc_date_time."',".$txt_rcv_rate.",".$txt_amount.")"; 
		
		
		
		//$id_dtls=return_next_id( "id", "inv_trims_issue_dtls", 1 ) ;
		$save_data=str_replace("'","",$save_data);
		$tot_string=strlen($save_data);
		$count_loop=ceil($tot_string/3900);
		$first_save_data=''; $second_save_data=''; $theRest_save_data='';$count=0; $interval=3900;
		for($i=1;$i<=$count_loop; $i++)
		{
		    if($count_loop>0 && $i==1) $first_save_data=substr($save_data, $count, $interval);
		    if($count_loop>1 && $i==2) $second_save_data=substr($save_data, $count, $interval);
		    if($count_loop>2 && $i==3) $theRest_save_data=substr($save_data, $count, $interval);
		    $count+=3900;
		}
		$id_dtls = return_next_id_by_sequence("INV_TRIMS_ISSUE_DTLS_PK_SEQ", "inv_trims_issue_dtls", $con);
		$field_array_dtls="id, mst_id, trans_id, prod_id, item_group_id, item_description, brand_supplier, uom, issue_qnty, rate, amount, order_id, item_color_id, item_size, save_string, save_string_2, save_string_3, store_id,floor_id,room,rack_no,shelf_no,bin, remarks, booking_id, booking_no, inserted_by, insert_date,rcv_rate,rcv_amount";
		
		$data_array_dtls="(".$id_dtls.",".$id.",".$id_trans.",".$txt_prod_id.",".$cbo_item_group.",".$txt_item_description.",".$txt_brad_supp.",".$cbo_uom.",".$txt_return_qnty.",".$txt_cons_rate.",'".$avg_rate_amount."',".$all_po_id.",".$cbo_item_color.",".$txt_item_size.",'".$first_save_data."','".$second_save_data."','".$theRest_save_data."',".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_remarks.",".$txt_booking_id.",".$txt_booking_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_rcv_rate.",".$txt_amount.")";
		
		//transaction table insert here END ---------------------------------//
		 
		//order_wise_pro_detail table insert here
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$save_data=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_data);$i++)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_dtls=explode("_",$save_data[$i]);
			
			$order_id=$order_dtls[0];
			$issue_qnty=$order_dtls[1];
			
			if($issue_qnty>$trim_stock)
			{
				echo "30**Transfer Quantity Not Allow Over Order Stock. $issue_qnty==$trim_stock";
				disconnect($con);
				die;
			}
			
			$order_amount=$issue_qnty*$trim_ord_rate;
			
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",3,49,".$id_dtls.",".$order_id.",".$txt_prod_id.",".$issue_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id_prop = $id_prop+1;
			$all_order_id.=$order_id.",";
		}
		
		$all_order_id=chop($all_order_id,",");
		
 		  
		$rID=$transID=$DtlsId=$prodUpdate=$propoId=true;
		if(str_replace("'","",$txt_return_no)!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("inv_issue_master",$field_array_mst,$data_array_mst,1);
		}
		$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		//echo "10** insert into inv_transaction ($field_array_trans) values $data_array_trans";die;
		$DtlsId=sql_insert("inv_trims_issue_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "10** insert into inv_trims_issue_dtls ($field_array_dtls) values $data_array_dtls";die;
		$prodUpdate = sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);
		if($data_array_prop!="")
		{
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		}
		
		//echo "10** $rID=$transID=$DtlsId=$prodUpdate=$propoId";die;
		
		if($db_type==0)
		{
			if( $rID && $transID && $DtlsId && $prodUpdate && $propoId)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id)."**".$new_return_number[0]."**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name)."**".str_replace("'","",$txt_booking_id)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_receive_basis);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$id."**".$new_return_number[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $transID && $DtlsId && $prodUpdate && $propoId)
			{
				oci_commit($con);  
				echo "0**".str_replace("'","",$id)."**".$new_return_number[0]."**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name)."**".str_replace("'","",$txt_booking_id)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_receive_basis);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id."**".$new_return_number[0];
			}
		}
		disconnect($con);
		die;
				
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		$issue_mst_id= str_replace("'","",$issue_mst_id);
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		$txt_cons_rate=str_replace("'","",$txt_cons_rate);
		$previous_prod_id=str_replace("'","",$previous_prod_id);
		$txt_prod_id=str_replace("'","",$txt_prod_id);
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		$hidden_issue_qnty=str_replace("'","",$hidden_issue_qnty);
		//$all_po_id=str_replace("'","",$all_po_id);
		
		$conversion_factor=$trim_group_arr[str_replace("'","",$cbo_item_group)]['conversion_factor'];

		$sqlCon="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==6)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
				if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and c.bin_box=$cbo_bin" ;}
			}
			else if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			}
		}

		//echo "10**".$sqlCon;die;

		$store_order_sql=sql_select("select sum((case when b.trans_type in (1,4,5) and c.transaction_type in (1,4,5) then  b.quantity else 0 end)-(case when b.trans_type in (2,3,6) and c.transaction_type in (2,3,6) then b.quantity else 0 end)) as balance, sum((case when b.trans_type in (1,4,5) and c.transaction_type in (1,4,5) then  b.order_amount else 0 end)-(case when b.trans_type in (2,3,6) and c.transaction_type in (2,3,6) then b.order_amount else 0 end)) as balance_amt 
		from order_wise_pro_details b, inv_transaction c  
		where b.trans_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.entry_form in(24,25,49,73,78,112) and b.po_breakdown_id in (".str_replace("'","",$all_po_id).") and c.store_id=$cbo_store_name and b.prod_id=$txt_prod_id and b.trans_id<>$update_trans_id and b.dtls_id<>$update_dtls_id  $sqlCon");
		
		$store_order_stock=$store_order_sql[0][csf("balance")]*$conversion_factor;
		$trim_stock=$store_order_sql[0][csf("balance")];
		$trim_stock_amt=$store_order_sql[0][csf("balance_amt")];
		$trim_ord_rate=0;
		if($store_order_sql[0][csf("balance")]!=0 && $trim_stock_amt!=0) $trim_ord_rate=$trim_stock_amt/$store_order_sql[0][csf("balance")];
		
		if($txt_return_qnty>$store_order_stock)
		{
			echo "30**Return Quantity Not Over Order Stock."; disconnect($con); die;
		}
		
		$sql_before = sql_select( "select a.id,a.current_stock, a.stock_value, b.cons_quantity,b.cons_amount from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id=$previous_prod_id and b.id=$update_trans_id and a.item_category_id=4 and b.item_category=4 and b.transaction_type=3" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=0;
		foreach($sql_before as $result)
		{
			$before_prod_id 	= $result[csf("id")];
 			$before_stock_qnty 	= $result[csf("current_stock")];
			$before_stock_value 	= $result[csf("stock_value")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
			$before_issue_value	= $result[csf("cons_amount")];
		}
		
		//current product ID
		$sql_current = sql_select( "select a.id,a.current_stock, a.stock_value, a.avg_rate_per_unit from product_details_master a where a.id=$txt_prod_id and a.item_category_id=4" );
		foreach($sql_current as $result)
		{
			$current_prod_id 		= $result[csf("id")];
 			$current_stock_qnty 	= $result[csf("current_stock")];
			$current_stock_value 	= $result[csf("stock_value")];
			$avg_rate 				= $result[csf("avg_rate_per_unit")];
		}
		//echo $consump_qnty;die;
		//product master table data UPDATE START----------------------//		
		
		if($previous_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = ($current_stock_qnty+$before_issue_qnty)-$txt_return_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty
			$adj_stock_value = 0;
			if ($adj_stock_qnty != 0){
				$adj_stock_value = ($current_stock_value+$before_issue_value)-($txt_return_qnty*$avg_rate);				
			}

			$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod= $txt_return_qnty."*".$adj_stock_qnty."*".$adj_stock_value."*'".$user_id."'*'".$pc_date_time."'";
			
		}
		else
		{
			$updateIdprod_array = $update_dataProd = array();
			//before product adjust
			$adj_before_stock_qnty 		= $before_stock_qnty+$before_issue_qnty;
			$adj_before_stock_value = 0;
			if ($adj_before_stock_qnty != 0){
				$adj_before_stock_value 	= $before_stock_value+$before_issue_value;				
			} 

			$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_prev= $before_issue_qnty."*".$adj_before_stock_qnty."*".$adj_before_stock_value."*'".$user_id."'*'".$pc_date_time."'";		
			
			//current product adjust
			$adj_curr_stock_qnty = 	$current_stock_qnty-$txt_return_qnty;
			$adj_curr_stock_value=0;
			if ($adj_curr_stock_qnty != 0){
				$adj_curr_stock_value = $current_stock_value-($txt_return_qnty*$avg_rate);				
			} 

			$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod= $txt_return_qnty."*".$adj_curr_stock_qnty."*".$adj_curr_stock_value."*'".$user_id."'*'".$pc_date_time."'";
		}
		
	
		//****************************************** BEFORE ENTRY ADJUST END *****************************************//
		 
  		$id=$issue_mst_id;
		//yarn master table UPDATE here START----------------------//		
		$field_array_mst="company_id*issue_date*knit_dye_source*supplier_id*gate_pass_no*updated_by*update_date";
		$data_array_mst=$cbo_company_id."*".$txt_return_date."*".$cbo_return_source."*".$cbo_return_to."*".$txt_gate_pass_no."*'".$user_id."'*'".$pc_date_time."'";
		
		$avg_rate_amount=str_replace("'","",$txt_return_qnty)*str_replace("'","",$txt_cons_rate);
		
 		$field_array_trans="company_id*prod_id*store_id*floor_id*room*rack*self*bin_box*item_category*transaction_type*transaction_date*cons_uom*cons_quantity*cons_rate*cons_amount*remarks*updated_by*update_date*rcv_rate*rcv_amount";
 		$data_array_trans= "".$cbo_company_id."*".$txt_prod_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*4*3*".$txt_return_date."*".$cbo_uom."*".$txt_return_qnty."*".$txt_cons_rate."*".$avg_rate_amount."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'*".$txt_rcv_rate."*".$txt_amount.""; 
		
		$save_data=str_replace("'","",$save_data);
		$tot_string=strlen($save_data);
		$count_loop=ceil($tot_string/3900);
		$first_save_data=''; $second_save_data=''; $theRest_save_data='';$count=0; $interval=3900;
		for($i=1;$i<=$count_loop; $i++)
		{
		    if($count_loop>0 && $i==1) $first_save_data=substr($save_data, $count, $interval);
		    if($count_loop>1 && $i==2) $second_save_data=substr($save_data, $count, $interval);
		    if($count_loop>2 && $i==3) $theRest_save_data=substr($save_data, $count, $interval);
		    $count+=3900;
		}
		
		$field_array_dtls_update="trans_id*prod_id*item_group_id*item_description*brand_supplier*uom*issue_qnty*rate*amount*order_id*item_color_id*item_size*save_string*save_string_2*save_string_3*store_id*floor_id*room*rack_no*shelf_no*bin*remarks*updated_by*update_date*rcv_rate*rcv_amount";
		
		$data_array_dtls_update=$update_trans_id."*".$txt_prod_id."*".$cbo_item_group."*".$txt_item_description."*".$txt_brad_supp."*".$cbo_uom."*".$txt_return_qnty."*".$txt_cons_rate."*".$avg_rate_amount."*".$all_po_id."*".$cbo_item_color."*".$txt_item_size."*'".$first_save_data."'*'".$second_save_data."'*'".$theRest_save_data."'*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_rcv_rate."*".$txt_amount."";

		
		//order_wise_pro_detail table insert here
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		$save_data=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_data);$i++)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_dtls=explode("_",$save_data[$i]);
			
			$order_id=$order_dtls[0];
			$issue_qnty=$order_dtls[1];
			
			if($issue_qnty>$trim_stock)
			{
				echo "30**Transfer Quantity Not Allow Over Order Stock.";
				disconnect($con);
				die;
			}
			
			$order_amount=$issue_qnty*$trim_ord_rate;
			
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",3,49,".$update_dtls_id.",".$order_id.",".$txt_prod_id.",".$issue_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
		}
		
		$all_order_id=chop($all_order_id,",");
		
 		$rID=$transID=$dtlsID=$delete_prop=$propoId=$prodUpdate=$adjust_prod=true;
		
		$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,0);
		$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_trans_id,0);
		$dtlsID=sql_update("inv_trims_issue_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=49",0);
		if($data_array_prop!="")
		{
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		}
		
		//echo "10** insert into order_wise_pro_details ($field_array_proportionate) values $data_array_prop";die;
		if($previous_prod_id==$txt_prod_id)
		{
			$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);
		}
		else
		{
			$adjust_prod=sql_update("product_details_master",$field_array_prod,$data_array_prod_prev,"id",$previous_prod_id,0);
			$prodUpdate= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);
		}
		
		//echo "10**$rID=$transID=$dtlsID=$delete_prop=$propoId=$prodUpdate=$adjust_prod";oci_rollback($con);die;
		
		
		
		
		if($db_type==0)
		{
			if($rID && $transID && $dtlsID && $delete_prop && $propoId && $prodUpdate && $adjust_prod)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$id)."**".str_replace("'","",$txt_return_no)."**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name)."**".str_replace("'","",$txt_booking_id)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_receive_basis);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $transID && $dtlsID && $delete_prop && $propoId && $prodUpdate && $adjust_prod)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$id)."**".str_replace("'","",$txt_return_no)."**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name)."**".str_replace("'","",$txt_booking_id)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_receive_basis);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		disconnect($con);
		die;
 	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$update_id=str_replace("'","",$issue_mst_id);
		$previous_prod_id=str_replace("'","",$previous_prod_id);
		$update_dtls_id=str_replace("'","",$update_dtls_id);
		$update_trans_id=str_replace("'","",$update_trans_id);
		//echo "10**$update_id=$previous_prod_id=$update_dtls_id=$update_trans_id";die;
		if($update_id>0 && $previous_prod_id>0 && $update_dtls_id>0 && $update_trans_id>0)
		{
			$previous_data_check=sql_select("select id as rcv_id, cons_quantity as rcv_qnty, cons_amount as rcv_amount  from inv_transaction where transaction_type=3 and id=$update_trans_id and prod_id=$previous_prod_id");
			$previous_check_id=$previous_data_check[0][csf("rcv_id")];
			$previous_qnty=$previous_data_check[0][csf("rcv_qnty")];
			$previous_amount=$previous_data_check[0][csf("rcv_amount")];
			
			/*if($db_type==0) $row_count_cond=" limit 1"; else $row_count_cond=" and rownum<2";
			$next_operation_check=sql_select("select id as next_id, mst_id as mst_id, transaction_type as transaction_type from inv_transaction where id > $previous_check_id and prod_id=$previous_prod_id and status_active=1 $row_count_cond");
			if(count($next_operation_check)>0)
			{
				$next_id=$next_operation_check[0][csf("next_id")];
				$next_mst_id=$next_operation_check[0][csf("mst_id")];
				$next_transaction_type=$next_operation_check[0][csf("transaction_type")];

				if($next_transaction_type==1 || $next_transaction_type==4)
				{
					$next_mrr=return_field_value("recv_number as next_mrr_number","inv_receive_master","id=$next_mst_id","next_mrr_number");
				}
				else if($next_transaction_type==2 || $next_transaction_type==3)
				{
					$next_mrr=return_field_value("issue_number as next_mrr_number","inv_issue_master","id=$next_mst_id","next_mrr_number");
				}
				else
				{
					$next_mrr=return_field_value("transfer_system_id as next_mrr_number","inv_item_transfer_mst","id=$next_mst_id","next_mrr_number");
				}
				echo "20**Next Operation No:- $next_mrr  Found, Delete Not Allow.";
				disconnect($con);die;
				//check_table_status( $_SESSION['menu_id'],0);
			}*/
			
			
			$row_prod=sql_select( "select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0" );
			$prod_id=$row_prod[0][csf('id')];
			$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
			$curr_stock_qnty=$row_prod[0][csf('current_stock')]+$previous_qnty;
			$curr_stock_value=0;			
			if ($curr_stock_qnty != 0){
				$curr_stock_value=$row_prod[0][csf('stock_value')]+$previous_amount;
				$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
			}
			
			$field_array_prod_update="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update=$avg_rate_per_unit."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			
			$row_propotionate=sql_select( "select id, po_breakdown_id, quantity, order_rate, order_amount 
			from order_wise_pro_details where trans_id=$previous_check_id and status_active=1 and is_deleted=0" );
			$propotionate_data=array();
			foreach($row_propotionate as $row)
			{
				$all_order_id.=$row[csf("po_breakdown_id")].",";
				$propotionate_data[$row[csf("po_breakdown_id")]]["quantity"]+=$row[csf("quantity")];
				$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount"]+=$row[csf("order_amount")];
			}
			$all_order_id=chop($all_order_id,",");
			$field_array_prod_ord_update="avg_rate*stock_quantity*stock_amount*updated_by*update_date";
			if($all_order_id!="")
			{
				$prod_order_stock=sql_select("select id, po_breakdown_id, stock_quantity, stock_amount 
				from order_wise_stock where prod_id=$previous_prod_id and po_breakdown_id in($all_order_id) and status_active=1 and is_deleted=0 ");
				foreach($prod_order_stock as $row)
				{
					$current_stock_qnty=$row[csf('stock_quantity')]+$propotionate_data[$row[csf("po_breakdown_id")]]["quantity"];
					$current_stock_value=$row[csf('stock_amount')]+$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount"];
					if($current_stock_value>0 && $current_stock_qnty>0)
					{
						$current_avg_rate=number_format($current_stock_value/$current_stock_qnty,$dec_place[3],'.','');
					}
					else
					{
						$current_avg_rate=0;
					}
					
					
					$ord_prod_id_arr[]=$row[csf('id')];
					$data_array_prod_ord_update[$row[csf('id')]]=explode("*",("".$current_avg_rate."*".$current_stock_qnty."*".$current_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
			
			$field_arr="status_active*is_deleted*updated_by*update_date";
			$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=$rID2=$rID3=$rID4=$ordProdUpdate=true;
			$rID=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$previous_prod_id,1);
			
			//echo "10** $rID";die;
			
			if(count($ord_prod_id_arr)>0)
			{
				$ordProdUpdate=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_prod_ord_update,$data_array_prod_ord_update,$ord_prod_id_arr));
			}
			//echo "10**$update_trans_id == $update_dtls_id == $update_trans_id";oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
			$rID2=sql_update("inv_transaction",$field_arr,$data_arr,"id",$update_trans_id,1);
			$rID3=sql_update("inv_trims_issue_dtls",$field_arr,$data_arr,"id",$update_dtls_id,1);
			if($all_order_id!="")
			{
				$rID4=sql_update("order_wise_pro_details",$field_arr,$data_arr,"trans_id",$update_trans_id,1);
			}
			
			//echo "10** $rID && $ordProdUpdate && $rID2 && $rID3 && $rID4";oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($rID && $ordProdUpdate && $rID2 && $rID3 && $rID4)
				{
					mysql_query("COMMIT");
					echo "2**".$update_id."**".str_replace("'","",$txt_return_no)."**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name)."**".str_replace("'","",$txt_booking_id)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_receive_basis); 
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "7**0**0**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name)."**".str_replace("'","",$txt_booking_id)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_receive_basis);
				}
			}
	
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $ordProdUpdate && $rID2 && $rID3 && $rID4)
				{
					oci_commit($con);  
					echo "2**".$update_id."**".str_replace("'","",$txt_return_no)."**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name)."**".str_replace("'","",$txt_booking_id)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_receive_basis); 
				}
				else
				{
					oci_rollback($con);
					echo "7**0**0**0"."**".$all_order_id."**".str_replace("'","",$cbo_store_name)."**".str_replace("'","",$txt_booking_id)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_receive_basis);
				}
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}		
}



if($action=="return_number_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_return_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="180" style="display:none">Search By</th>
                    <th width="250" align="center" id="search_by_td_up">Enter Return Number</th>
                    <th width="300">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">                    
                    <td align="center"  style="display:none">
                        <?  
                            $search_by = array(1=>'Return Number');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",1,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:200px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:120px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:120px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_return_search_list_view', 'search_div', 'trims_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="3">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-->
                     <input type="hidden" id="hidden_return_number" value="" />
                    <!--END-->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" style="margin-top:10px" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_return_search_list_view")
{
	
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$year_id = $ex_data[5];
	
	//echo $year_id.jahid;die;
	
	$sql_cond="";
	if($search_by==1)
	{
		if($search_common!="") $sql_cond .= " and a.issue_number like '%$search_common'";
	}
		 
	if( $txt_date_from!="" && $txt_date_to!="" ) 
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}
	
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$year_condition="";
	if($year_id>0)
	{
		if($db_type==0) $year_condition=" and YEAR(a.insert_date)='$year_id'";
		else $year_condition=" and to_char(a.insert_date,'YYYY')='$year_id'";
	}
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, supplier_id, company_location_id FROM user_passwd where id=$user_id");
	$company_id = $userCredential[0][csf('company_id')];
	$supplier_id = $userCredential[0][csf('supplier_id')];
	$store_location_id = $userCredential[0][csf('store_location_id')];
	$company_location_id = $userCredential[0][csf('company_location_id')];
	
	if ($company_id !='') {
		$company_credential_cond = " and a.company_id in($company_id)";
	}
	if ($store_location_id !='') {
		$store_location_credential_cond = " and b.store_id in($store_location_id)"; 
	}
	if ($supplier_id !='') {
		$supplier_credential_cond = " and a.supplier_id in($supplier_id)";
	}
	
	$sql = "select a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity, a.is_posted_account
			from inv_issue_master a, inv_transaction b
			where a.id=b.mst_id and b.transaction_type=3 and a.status_active=1 and a.item_category=4 and b.item_category=4 and a.entry_form=49 $sql_cond $company_credential_cond $store_location_credential_cond $supplier_credential_cond $year_condition 
			group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date, a.is_posted_account order by a.id";
	//echo $sql;
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(2=>$company_arr);
 	echo create_list_view("list_view", "Return No, Year, Company Name, Return Date, Return Qty","80,80,150,100","600","230",0, $sql , "js_set_value", "id,issue_number,received_id,is_posted_account", "", 1, "0,0,company_id,0,0", $arr, "issue_number_prefix_num,year,company_id,issue_date,cons_quantity","","",'0,0,0,3,1') ;	
 	exit();
}

 

if($action=="populate_master_from_data")
{  
	$sql = "select id, issue_number, company_id, supplier_id, issue_date, knit_dye_source as knitting_source, gate_pass_no 
			from inv_issue_master 
			where id='$data' and item_category=4 and entry_form=49";
	//echo $sql;
	$res = sql_select($sql);

	$company_id=$res[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];

	foreach($res as $row)
	{
		echo "$('#txt_return_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#issue_mst_id').val('".$row[csf("id")]."');\n";
 		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		echo "load_drop_down( 'requires/trims_receive_rtn_controller','".$row[csf("knitting_source")]."'+'_'+'".$row[csf("company_id")]."', 'load_drop_down_knitting_com','knitting_com');\n";
		echo "$('#cbo_return_source').val(".$row[csf("knitting_source")].");\n";
		echo "$('#cbo_return_to').val(".$row[csf("supplier_id")].");\n";
		echo "$('#txt_gate_pass_no').val('".$row[csf("gate_pass_no")]."');\n";
		echo "$('#store_update_upto').val('" . $store_method . "');\n";
		//echo "document.getElementById('store_update_upto').value 			= '" . $store_method . "';\n";
		
   	}	
	exit();	
}



if($action=="show_dtls_list_view")
{
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
		$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
	}
	unset($data_array);
	$sql="select id, item_group_id, item_description, brand_supplier, issue_qnty, item_color_id, item_size, uom, order_id from inv_trims_issue_dtls where mst_id=$data and status_active=1 and is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
		<thead>
			<th width="120">Item Group</th>
			<th width="180">Item Description</th>               
			<th width="100">Item Color</th>
			<th width="80">Item Size</th>
			<th width="80">Supp Ref</th>
			<th width="50">UOM</th>
            <th width="80">Return Qnty</th>
            <th>Buyer Order</th>
		</thead>
	</table>
	<div style="width:820px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search_dtls">  
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				
				$order_no="";
				if($row[csf("order_id")]!="")
				{
					if($db_type==0)
					{
						$order_no=return_field_value("group_concat(po_number) as po_no","wo_po_break_down","id in (".$row[csf("order_id")].")","po_no");	
					}
					else
					{
						$order_no=return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_no","wo_po_break_down","id in (".$row[csf("order_id")].")","po_no");		
					}
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_trims_details_form_data', 'requires/trims_receive_rtn_controller');"> 
					<td width="120"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>  
					<td width="180"><p><? echo $row[csf('item_description')]; ?></p></td>             
					<td width="100"><p><? echo $color_arr[$row[csf('item_color_id')]]; ?></p></td>
					<td width="80"><p><? echo $row[csf('item_size')]; ?></p></td>
					<td width="80"><p><? echo $row[csf('brand_supplier')]; ?></p></td>
                    <td width="50"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					<td align="right" width="80"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
                    <td><p><? echo $order_no; ?>&nbsp;</p></td>
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



if($action=='populate_trims_details_form_data')
{
	//rcv_rate
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$issue_mst=sql_select("select a.issue_basis, a.booking_id from inv_issue_master a, inv_trims_issue_dtls b where a.id=b.mst_id and b.id='$data'");
	$issue_basis=$issue_mst[0][csf('issue_basis')];
	$booking_id=$issue_mst[0][csf('booking_id')];
	$data_array=sql_select("select b.id, b.trans_id, b.prod_id, b.item_group_id, b.item_description, b.brand_supplier, b.issue_qnty, b.gmts_color_id, b.gmts_size_id, b.uom, b.order_id, b.item_color_id, b.item_size, b.save_string, b.save_string_2, b.save_string_3, b.rate as cons_rate, b.rcv_rate, b.rcv_amount as amount, b.store_id,b.floor_id,b.room, b.rack_no, b.shelf_no, b.bin, b.remarks, b.booking_id, b.booking_no,a.company_id  
	from inv_trims_issue_dtls b, inv_issue_master a  
	where b.id='$data' and a.id=b.mst_id");
	foreach ($data_array as $row)
	{ 
		$save_string_data=$row[csf("save_string")]."".$row[csf("save_string_2")]."".$row[csf("save_string_3")];	
		$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=".$row[csf("prod_id")]."","conversion_factor");
		$return_basis=return_field_value("receive_basis","inv_transaction","id=".$row[csf("trans_id")]."","receive_basis");

		echo "load_room_rack_self_bin('requires/trims_receive_rtn_controller*4', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= ".$row[csf("store_id")].";\n";
		echo "load_room_rack_self_bin('requires/trims_receive_rtn_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_receive_rtn_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_receive_rtn_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack_no")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_receive_rtn_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("shelf_no")]."');\n";
		echo "load_room_rack_self_bin('requires/trims_receive_rtn_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."','".$row[csf('shelf_no')]."',this.value);\n";	
		echo "$('#cbo_bin').val('".$row[csf("bin")]."');\n";


		echo "document.getElementById('cbo_receive_basis').value 			= ".$return_basis.";\n";
		echo "document.getElementById('txt_item_description').value 		= '".$row[csf("item_description")]."';\n";
		echo "document.getElementById('txt_prod_id').value 					= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('cbo_item_group').value 				= '".$row[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_item_color').value 				= '".$row[csf("item_color_id")]."';\n";
		echo "document.getElementById('txt_brad_supp').value 				= '".$row[csf("brand_supplier")]."';\n";
		echo "document.getElementById('txt_item_size').value 				= '".$row[csf("item_size")]."';\n";
		
		echo "document.getElementById('txt_booking_id').value 				= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		
		echo "document.getElementById('txt_return_qnty').value 				= '".number_format($row[csf("issue_qnty")],2,'.','')."';\n";
		echo "document.getElementById('txt_cons_rate').value 				= '".number_format($row[csf("cons_rate")],4,'.','')."';\n";
		echo "document.getElementById('txt_rcv_rate').value 				= '".number_format($row[csf("rcv_rate")],4,'.','')."';\n";
		echo "document.getElementById('txt_amount').value 					= '".number_format($row[csf("amount")],2,'.','')."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		
		echo "document.getElementById('save_data').value 					= '".$save_string_data."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('hidden_issue_qnty').value 			= '".number_format($row[csf("issue_qnty")],2,'.','')."';\n";
		echo "document.getElementById('txt_conversion_faction').value 		= '".$conversion_fac."';\n";
		
		
		if($db_type==0)
		{
			$order_data=sql_select("select group_concat(a.po_number) as po_no from wo_po_break_down a where a.id in(".$row[csf("order_id")].")");
		}
		else
		{
			$order_data=sql_select("select LISTAGG(cast(a.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_no from wo_po_break_down a where a.id in(".$row[csf("order_id")].")");
		}
		
		$order_no=implode(",",array_unique(explode(",",$order_data[0][csf('po_no')])));//$order_data[0][csf('po_no')];
		
		echo "get_php_form_data('".$row[csf("order_id")]."'+'**'+".$row[csf("prod_id")]."+'**'+".$row[csf("store_id")]."+'**'+".$row[csf("id")]."+'**'+".$row[csf("trans_id")].", 'get_trim_cum_info', 'requires/trims_receive_rtn_controller')".";\n";
		echo "show_list_view('".$row[csf("order_id")]."'+'**'+".$row[csf("prod_id")]."+'**'+".$row[csf("store_id")]."+'**'+".$row[csf("booking_id")]."+'**'+'".$row[csf("booking_no")]."'+'**'+'".$return_basis."', 'create_itemDesc_search_list_view','list_product_container','requires/trims_receive_rtn_controller','setFilterGrid(\'tbl_list_search\',0);');\n";
		
		echo "document.getElementById('txt_po_no').value 				= '".$order_no."';\n";
		
		echo "disable_enable_fields( 'cbo_store_name*txt_po_no*txt_booking_no*cbo_receive_basis', 1, '', '' );\n"; 

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_receive_return_entry',1,1);\n";  
		exit();
	}
}

if ($action=="get_trim_cum_info")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$prod_id=$data[1];
	$store_id=$data[2];
	$update_dtls_id=$data[3];
	$update_trans_id=$data[4];
	
	$stockData=sql_select("select a.current_stock, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id=$prod_id");
	$current_stock=$stockData[0][csf('current_stock')];
	$conversion_factor=$stockData[0][csf('conversion_factor')];
	if($conversion_factor<=0) $conversion_factor=1;
	
	$dataArray=sql_select("select sum(case when a.trans_type in(1,5) and a.entry_form in(24,78,112) then a.quantity end) as recv_qnty, sum(case when a.trans_type=2 and a.entry_form=25 then a.quantity end) as issue_qnty, sum(case when a.trans_type=3 and a.entry_form=49 then a.quantity end) as rcv_rtn_qnty, sum(case when a.trans_type=4 and a.entry_form in(73) then a.quantity end) as issue_rtn_qnty , sum(case when a.trans_type=6 and a.entry_form in(78,112) then a.quantity end) as trans_out_qnty
	from  order_wise_pro_details a, inv_transaction b 
	where a.trans_id=b.id and a.po_breakdown_id in($po_id) and a.prod_id='$prod_id' and b.prod_id='$prod_id' and b.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id<>$update_dtls_id and a.trans_id<>$update_trans_id");
	
	$recv_qnty=$dataArray[0][csf('recv_qnty')]*$conversion_factor;
	//$cu_issue=(($dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('issue_rtn_qnty')])+$dataArray[0][csf('rcv_rtn_qnty')]+$dataArray[0][csf('trans_out_qnty')])*$conversion_factor;
	$cu_issue=$dataArray[0][csf('rcv_rtn_qnty')]*$conversion_factor;
    $yet_to_issue = ($recv_qnty-$cu_issue);
	
    echo "$('#txt_fabric_received').val(".number_format(($recv_qnty),2,".","").");\n";
    echo "$('#txt_cumulative_issued').val('".number_format($cu_issue,2,".","")."');\n";
    echo "$('#txt_yet_to_issue').val('".number_format(($yet_to_issue),2,".","")."');\n";
	echo "$('#txt_global_stock').val('".number_format(($current_stock),2,".","")."');\n";
	exit();
}


if ($action=="trims_receive_return_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql=" select id, issue_number, received_id, issue_date, supplier_id, knit_dye_source, gate_pass_no from  inv_issue_master where id='$data[1]' and entry_form=49 and item_category=4 and status_active=1 and is_deleted=0";
	//echo $sql;
	$dataArray=sql_select($sql);

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr = return_library_array("select id,country_name from lib_country","id","country_name");
	$receive_arr = return_library_array("select id,recv_number from inv_receive_master","id","recv_number");
	//$receive_quantity=return_field_value("supplier_id","inv_receive_master ","recv_number='$recv_trans_id'","supplier_id" );
	
	?>
	<div style="width:900px;">
    <table width="880" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <? echo $result['province'];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Purchase Return/Delivery Challan</u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Return Number:</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="100"><strong>Return To :</strong></td> <td width="175px"><? if($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('supplier_id')]]; else echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td width="110"><strong>Return Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
        </tr>
        <tr>
        	<td ><strong>Gate Pass No:</strong></td><td><? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
            <td>&nbsp;</td><td>&nbsp;</td>
            <td>&nbsp;</td><td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="6">&nbsp;</td>
        </tr>
    </table>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="880"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="50">SL</th>
                <th width="150" align="center">Item Description</th>
                <th width="70" align="center">UOM</th> 
                <th width="80" align="center">Return Qnty.</th>
                <th width="100" align="center">Store</th>
				<th width="100" align="center">Remark</th>
            </thead>
<?
	$mrr_no =$dataArray[0][csf('issue_number')];;
	//$up_id =$data[1];
	$cond="";
	if($mrr_no!="") $cond .= " and c.issue_number='$mrr_no'";
	//if($up_id!="") $cond .= " and a.id='$up_id'";
	 $i=1;
 	$sql_dtls = "select b.id as prod_id, b.product_name_details, a.id as tr_id, a.store_id, a.cons_uom, a.cons_quantity,a.REMARKS
			from inv_transaction a, product_details_master b, inv_issue_master c
 			where c.id=a.mst_id and a.status_active=1 and a.company_id='$data[0]' and c.id='$data[1]' and a.item_category=4 and transaction_type=3 and a.prod_id=b.id and b.status_active=1 ";
	
	//echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);
			
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
		$qnty+=$row[csf('cons_quantity')];
		?>

			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $row[csf('product_name_details')]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                <td align="right"><? echo $row[csf('cons_quantity')]; ?></td>
                <td><? echo $store_library[$row[csf('store_id')]]; ?></td>
				<td><? echo $row[csf('REMARKS')]; ?></td>
			</tr>
	<?
    $i++;
    }
    ?>
        	<tr> 
                <td align="right" colspan="3" >Total</td>
                <td align="right"><? echo number_format($qnty,0,'',','); ?></td>
                <td align="right">&nbsp;</td>
			</tr>
		</table>
        <br>
		 <?
            echo signature_table(98, $data[0], "880px");
         ?>
      </div>
	</div> 
	<?
    exit();
}
?>
