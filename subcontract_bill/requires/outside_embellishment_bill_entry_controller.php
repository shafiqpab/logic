<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
//print_r ($data[0]);
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_supplier_name")
{
	echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=23) order by supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $selected, "show_list_view(document.getElementById('cbo_company_id').value+'_'+this.value,'embellishment_entry_list_view','embellishment_info_list','requires/outside_embellishment_bill_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');","","","","","",5 );
	exit();
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[0]' $supplier_cond and sup.id in (select supplier_id from  lib_supplier_party_type where party_type=23) order by supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $selected, "","","","","","",5);
	exit();
}

if ($action=="bill_no_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('issue_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="packingbill_1"  id="packingbill_1" autocomplete="off">
                <table width="650" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Supplier Name</th>
                        <th width="80">Bill ID</th>
                        <th width="170">Date Range</th>
                        <th>
                        <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" />
                        </th>           
                    </thead>
                    <tbody>
                        <tr>
                            <td> 
                                <input type="hidden" id="issue_id">  
                                <?   
									echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'outside_packing_bill_entry_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );",0 );
                                ?>
                            </td>
                            <td width="150" id="supplier_td">
								<?
									echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$ex_data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=22) order by supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $ex_data[1], "","","","","","",5 );
                                ?> 
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'embellishment_bill_list_view', 'search_div', 'outside_embellishment_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                        <tr>
                        <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="embellishment_bill_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name_cond=" and supplier_id='$data[1]'"; $party_name_cond="";
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $return_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $return_date ="";
	}
	
	if ($data[4]!='') $bill_id_cond=" and prefix_no_num='$data[4]'"; else $bill_id_cond="";
	
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	
	$arr=array (3=>$party_arr,5=>$knitting_source,6=>$bill_for);
	
	if($db_type==0)
	{
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$sql= "select id, bill_no, prefix_no_num, $year_cond, location_id, bill_date, supplier_id, bill_for from subcon_outbound_bill_mst where process_id=3 and status_active=1 $company_name $party_name_cond $return_date $bill_id_cond";
	
	echo  create_list_view("list_view", "Bill No,Year,Bill Date,Party Name,Bill For", "70,70,100,120,100","600","250",0, $sql , "js_set_value", "id", "", 1, "0,0,0,supplier_id,bill_for", $arr , "prefix_no_num,year,bill_date,supplier_id,bill_for", "outside_embellishment_bill_entry_controller","",'0,0,3,0,0') ;
	exit(); 
}

if ($action=="load_php_data_to_form_issue")
{
	$nameArray= sql_select("select id, bill_no, company_id, location_id, bill_date, supplier_id from subcon_outbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down('requires/outside_embellishment_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "load_drop_down('requires/outside_embellishment_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "document.getElementById('cbo_supplier_company').value			= '".$row[csf("supplier_id")]."';\n"; 
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
	}
	exit();
}

if ($action=="embellishment_entry_list_view")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,1,'');
	$data=explode('_',$data);
	?>
	<script>
	</script>
	</head>
	<body>
        <div style="width:100%;">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="945px" class="rpt_table">
                <thead>
					
                    <th width="25">SL</th>
                    <th width="70">Challan No</th>
                    <th width="65">Recive Date</th>
                    <th width="60">Sys. No</th>                    
                    <th width="110">Garments Item</th>
                    <th width="120">Embl. Name & Type</th>
                    <th width="75">Recive Qty</th>
                    <th width="100">Order No</th>
                    <th width="100">Style Ref.</th>
                    <th width="50">Job</th>
                    <th width="50">Year</th>
                    <th>Buyer</th>
                </thead>
            </table>
        </div>
        <div style="width:945px; max-height:180px; overflow-y:scroll">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="902px" class="rpt_table" id="tbl_list_search">
            <? 
			$order_array=array();
			if($db_type==0) $year_cond= "year(a.insert_date)";
			else if($db_type==2) $year_cond= "TO_CHAR(a.insert_date,'YYYY')";
            $order_sql=sql_select( "select a.job_no_prefix_num, $year_cond as year, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($order_sql as $row)
			{
				$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')];
				$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
				$order_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
			}
			 
            $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
            $receive_qty_arr=return_library_array( "select receive_id, sum(receive_qty) as receive_qty from subcon_outbound_bill_dtls where status_active=1 and is_deleted=0 group by receive_id",'receive_id','receive_qty');
            $i=1;
            if($data[2]=="") // Insert
            {
                $sql="select id, challan_no, production_date, po_break_down_id, item_number_id, production_quantity, embel_name, embel_type from pro_garments_production_mst where company_id=$data[0] and serving_company=$data[1] and production_source=3 and production_type=3 and status_active=1 and is_deleted=0 order by id Desc";			
            }
            else
            {
                $sql="(select id, challan_no, production_date, po_break_down_id, item_number_id, production_quantity, embel_name, embel_type from pro_garments_production_mst where company_id=$data[0] and serving_company=$data[1] and id NOT IN (SELECT receive_id FROM subcon_outbound_bill_dtls where status_active=1 and is_deleted=0) and production_source=3 and production_type=3 and status_active=1 and is_deleted=0) 
                union (select id, challan_no, production_date, po_break_down_id, item_number_id, production_quantity, embel_name, embel_type from pro_garments_production_mst where company_id=$data[0] and serving_company=$data[1]  and id IN (SELECT receive_id FROM subcon_outbound_bill_dtls where status_active=1 and is_deleted=0) and production_source=3 and production_type=3 and status_active=1 and is_deleted=0) order by id DESC";
            }
			//echo $sql;
            $sql_result=sql_select($sql);
            foreach($sql_result as $row)
            {
				$balance_qty=$row[csf('production_quantity')]-$receive_qty_arr[$row[csf('id')]];
				if($balance_qty>0)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				    // if($row[csf('recid')]==1) $bgcolor="yellow";
					if($row[csf('embel_name')]==1) $embel_type=' & '.$emblishment_print_type[$row[csf('embel_type')]];
					elseif($row[csf('embel_name')]==2) $embel_type=' & '.$emblishment_embroy_type[$row[csf('embel_type')]];
					elseif($row[csf('embel_name')]==3) $embel_type=' & '.$emblishment_wash_type[$row[csf('embel_type')]];	
					elseif($row[csf('embel_name')]==4) $embel_type=' & '.$emblishment_spwork_type[$row[csf('embel_type')]];
					else $embel_type='';	
					?>
					<tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');">
						<td width="25" align="center"><input type="checkbox" name="checkid<?=$i; ?>" id="checkid<?=$i; ?>" onClick="fnc_check(<?=$i; ?>)" value="1" ></td>
						<td width="25"><? echo $i; ?></td>
						<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $row[csf('challan_no')]; ?></div></td>
						<td width="65"><? echo change_date_format($row[csf('production_date')]); ?></td>
						<td width="60"><? echo $row[csf('id')]; ?></td>
						<td width="110"><div style="word-wrap:break-word; width:100px"><? echo $garments_item[$row[csf('item_number_id')]]; ?></div></td>
						<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $emblishment_name_array[$row[csf('embel_name')]].''.$embel_type; ?></div></td>
						<td width="75" align="right"><? echo number_format($balance_qty,2); ?></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_array[$row[csf('po_break_down_id')]]['po_number']; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_array[$row[csf('po_break_down_id')]]['style']; ?></div></td>
						<td width="50"><? echo $order_array[$row[csf('po_break_down_id')]]['job_no']; ?></td>
						<td width="50"><? echo $order_array[$row[csf('po_break_down_id')]]['year']; ?></td>
						<td align="center"><div style="word-wrap:break-word; width:60px"><? echo $buyer_arr[$order_array[$row[csf('po_break_down_id')]]['buyer_name']]; ?>
						<input type="hidden" id="pro_gmts_mst_id_<? echo $i; ?>" name="pro_gmts_mst_id_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
						<input type="hidden" id="currid<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" style="width:40px"></div></td>
					</tr>
					<?php
					$i++;
				}
            }
            ?>
        </table>
        </div>
        <table width="900">
            <tr>
			<td bgcolor="#7FDF00" align="center"><input type="checkbox" name="checkall" id="checkall" class="formbutton" value="2" onClick="checkall_data();"/> Check all</td>
                <td colspan="12" align="center">
                    <input type="button" id="show_button" class="formbutton" style="width:100px" value="Close" onClick="window_close()" />
                </td>
            </tr>
        </table>
	</body>           
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="load_php_dtls_form") 
{
	$data = explode("_",$data);
	$del_id=array_diff(explode(",",$data[0]), explode(",",$data[1]));
	$bill_id=array_intersect(explode(",",$data[0]), explode(",",$data[1]));
	$delete_id=array_diff(explode(",",$data[1]), explode(",",$data[0]));
	$del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id); $delete_id=implode(",",$delete_id);
	//echo $del_id.'=='.$bill_id;  
	$order_array=array();
	$sql_order="Select a.id, a.po_number, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$sql_order_result=sql_select($sql_order);
	foreach ($sql_order_result as $row)
	{
		$order_array[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$order_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$order_array[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
	}
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name'); 
	$receive_qty_arr=return_library_array( "select receive_id, sum(receive_qty) as receive_qty from subcon_outbound_bill_dtls where status_active=1 and is_deleted=0 group by receive_id",'receive_id','receive_qty');

	if( $data[2]!="" )//update===========
	{
		$sql="SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, receive_qty as order_qnty, embel_name, embel_type, rate, amount,currency_id, remarks FROM subcon_outbound_bill_dtls  WHERE mst_id=$data[2] and process_id=3 and status_active=1 and is_deleted=0";
	}
	else //insert=================
	{
		if($bill_id!="" && $del_id!="")
			$sql="(SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, embel_name, embel_type, receive_qty as order_qnty, rate, amount,currency_id, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id='3' and status_active=1 and is_deleted=0 )
			 union 
			 (SELECT 0 as upd_id, id as receive_id, production_date as receive_date, challan_no, po_break_down_id as order_id, item_number_id as prod_id, embel_name, embel_type, sum(production_quantity) as order_qnty, 0 as rate, 0 as amount,0 as currency_id, null as remarks FROM pro_garments_production_mst WHERE id in ($del_id) and production_source=3 and production_type=3 and status_active=1 and is_deleted=0 group by id, production_date, challan_no, po_break_down_id, item_number_id, embel_name, embel_type) order by receive_id DESC";
		else if($bill_id!="" && $del_id=="")
			$sql="SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, receive_qty as order_qnty, embel_name, embel_type, rate, amount,currency_id, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id='3' and status_active=1 and is_deleted=0";
		else if($bill_id=="" && $del_id!="")
			$sql="SELECT 0 as upd_id, id as receive_id, production_date as receive_date, challan_no, po_break_down_id as order_id, item_number_id as prod_id, sum(production_quantity) as order_qnty, embel_name, embel_type, 0 as rate, 0 as amount,0 as currency_id, null as remarks FROM pro_garments_production_mst WHERE id in ($del_id) and production_source=3 and production_type=3 and status_active=1 and is_deleted=0 group by id, production_date, challan_no, po_break_down_id, item_number_id, embel_name, embel_type order by id DESC";
	}
	//echo $sql;//die;	
	$k=0;
	$sql_result=sql_select($sql);
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$balance_qty=$row[csf('order_qnty')]-$receive_qty_arr[$row[csf('receive_id')]];
		 $k++;
		 if( $data[2]!="" )
		 {
			 if($data[1]=="") $data[1]=$row[csf("receive_id")]; else $data[1].=",".$row[csf("receive_id")];
	    	$balance_qty=$row[csf('order_qnty')];

		 }
	?>
       <tr align="center">				
            <td>
				<? if ($k==$num_rowss) { ?>
                    <input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:65px" value="<? echo $data[1]; ?>" />
                    <input type="hidden" name="delete_id" id="delete_id"  style="width:65px" value="<? echo $delete_id; ?>" />
                 <? } ?>
                <input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
                <input type="text" name="txtReceiveDate_<? echo $k; ?>" id="txtReceiveDate_<? echo $k; ?>"  class="datepicker" style="width:65px" value="<? echo change_date_format($row[csf("receive_date")]); ?>" readonly />									
            </td>
            <td>
                <input type="text" name="txtChallenno_<? echo $k; ?>" id="txtChallenno_<? echo $k; ?>"  class="text_boxes" style="width:75px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
            </td>
            <td>
                <input type="text" name="txtSysno_<? echo $k; ?>" id="txtSysno_<? echo $k; ?>" class="text_boxes" style="width:55px" value="<? echo $row[csf("receive_id")]; ?>" readonly />							 
            </td>
            <td>
                <input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>" style="width:40px" readonly /> 
                <input type="text" name="txtOrderno_<? echo $k; ?>" id="txtOrderno_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? echo $order_array[$row[csf("order_id")]]['po_number']; ?>" readonly />										
            </td>
            <td>
                <input type="text" name="txtStylename_<? echo $k; ?>" id="txtStylename_<? echo $k; ?>"  class="text_boxes" style="width:65px;" value="<? echo $order_array[$row[csf("order_id")]]['style_ref_no']; ?>" readonly />
            </td>
            <td>
                <input type="text" name="txtPartyname_<? echo $k; ?>" id="txtPartyname_<? echo $k; ?>"  class="text_boxes" style="width:55px" value="<? echo $buyer_arr[$order_array[$row[csf("order_id")]]['buyer_name']]; ?>" readonly />								
            </td>
            <td>
                <input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("prod_id")]; ?>">
                <input type="text" name="txtGmtsItem_<? echo $k; ?>" id="txtGmtsItem_<? echo $k; ?>"  class="text_boxes" style="width:95px" value="<? echo $garments_item[$row[csf("prod_id")]]; ?>" readonly />
            </td>
            <td>
            <?
				if($row[csf('embel_name')]==1) $embel_type=' & '.$emblishment_print_type[$row[csf('embel_type')]];
				elseif($row[csf('embel_name')]==2) $embel_type=' & '.$emblishment_embroy_type[$row[csf('embel_type')]];
				elseif($row[csf('embel_name')]==3) $embel_type=' & '.$emblishment_wash_type[$row[csf('embel_type')]];	
				elseif($row[csf('embel_name')]==4) $embel_type=' & '.$emblishment_spwork_type[$row[csf('embel_type')]];
				else $embel_type='';
			?>
                <input type="hidden" name="embelid_<? echo $k; ?>" id="embelid_<? echo $k; ?>" value="<? echo $row[csf("embel_name")]; ?>">
                <input type="hidden" name="embelTypeid_<? echo $k; ?>" id="embelTypeid_<? echo $k; ?>" value="<? echo $row[csf("embel_type")]; ?>">
                <input type="text" name="textEmbelNameType_<? echo $k; ?>" id="textEmbelNameType_<? echo $k; ?>"  class="text_boxes" style="width:115px" value="<? echo  $emblishment_name_array[$row[csf('embel_name')]].''.$embel_type; ?>" readonly />
            </td>
            <td>
                <input type="text" name="textWoNum_<? echo $k; ?>" id="textWoNum_<? echo $k; ?>" class="text_boxes" style="width:55px" value="<? //echo $row[csf("")]; ?>" disabled/>
            </td>
            <td>
                <input type="text" name="txtQnty_<? echo $k; ?>" id="txtQnty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:55px;" value="<? echo $balance_qty; ?>" />
            </td>
            <td>
                <input type="text" name="txtRate_<? echo $k; ?>" id="txtRate_<? echo $k; ?>"  class="text_boxes_numeric" style="width:35px;" value="<? echo $row[csf("rate")]; ?>" onChange="fnc_rate_copy(<? echo $k; ?>); onchangeonBlur=amount_caculation(<? echo $k; ?>);" />
            </td>
            <td>
				<?
					$total_amount=$row[csf("order_qnty")]*$row[csf("rate")];
                ?>
                <input type="text" name="txtAmount_<? echo $k; ?>" id="txtAmount_<? echo $k; ?>" style="width:60px;"  class="text_boxes_numeric" value="<? echo $row[csf("amount")]; ?>" readonly  />                	
            </td>
			<td>
                <?php echo create_drop_down( "curanci_$k", 60, $currency,"", 0, "-Currency-",$row[csf("currency_id")],"fnc_currency_copy($k);",0,"");?>
            </td>
            <td>
            	<input type="button" name="txtRemarks_<? echo $k; ?>" id="txtRemarks_<? echo $k; ?>"  class="formbuttonplasminus" style="width:30px" value="R" onClick="openmypage_remarks(<? echo $k; ?>);" />
                <input type="hidden" name="hiddRemarks_<? echo $k; ?>" id="hiddRemarks_<? echo $k; ?>"  class="text_boxes" style="width:25px" value="<? echo $row[csf("remarks")]; ?>" />
            </td>
        </tr>
	<?	
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id="3";
	if ($operation==0)   // Insert Here 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'EMBL', date("Y",time()), 5, "select prefix_no,prefix_no_num from  subcon_outbound_bill_mst where company_id=$cbo_company_id and process_id=$bill_process_id $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_outbound_bill_mst",1); 	
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, supplier_id, process_id, remarks, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_supplier_company.",".$bill_process_id.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			//echo "INSERT INTO subcon_outbound_bill_mst (".$field_array.") VALUES ".$data_array; die;
			$rID=sql_insert("subcon_outbound_bill_mst",$field_array,$data_array,0);
			$return_no=$new_bill_no[0]; 
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="location_id*bill_date*party_id*updated_by*update_date";
			$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_supplier_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_outbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=str_replace("'",'',$txt_bill_no);
		}
		
		$id1=return_next_id( "id", "subcon_outbound_bill_dtls",1);
		$field_array1 ="id, mst_id, receive_id, receive_date, challan_no, order_id, item_id, embel_name, embel_type, receive_qty, rate, amount, currency_id,remarks, process_id, inserted_by, insert_date";
		$field_array_up ="receive_id*receive_date*challan_no*order_id*item_id*embel_name*embel_type*receive_qty*rate*amount*currency_id*remarks*updated_by*update_date";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$reciveid="txtSysno_".$i;
			$receive_date="txtReceiveDate_".$i;
			$challen_no="txtChallenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$embelid="embelid_".$i;
			$embelTypeid="embelTypeid_".$i;
			$wo_num="textWoNum_".$i;
			$quantity="txtQnty_".$i;
			$rate="txtRate_".$i;
			$amount="txtAmount_".$i;
			$curanci="curanci_".$i;
			$remarks="hiddRemarks_".$i;
			$updateid_dtls="updateiddtls_".$i;
			  
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$reciveid.",".$$receive_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$embelid.",".$$embelTypeid.",".$$quantity.",".$$rate.",".$$amount.",".$$curanci.",".$$remarks.",'".$bill_process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$reciveid."*".$$receive_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$embelid."*".$$embelTypeid."*".$$quantity."*".$$rate."*".$$amount."*".$$curanci."*".$$remarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$reciveid);
				$data_array_delivery[str_replace("'",'',$$reciveid)] =explode("*",("1"));
			}
		}
			
		if($data_array1!="")
		{
			//echo "insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("subcon_outbound_bill_dtls",$field_array1,$data_array1,1);
		}
	
		if($db_type==0)
		{
			if($rID && $rID1 )
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1 )
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$id=str_replace("'",'',$update_id);
		$field_array="location_id*bill_date*supplier_id*remarks*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_supplier_company."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_outbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$id1=return_next_id( "id","subcon_outbound_bill_dtls",1);
		$field_array1 ="id, mst_id, receive_id, receive_date, challan_no, order_id, item_id, embel_name, embel_type, receive_qty, rate, amount, currency_id,remarks, process_id, inserted_by, insert_date";
		$field_array_up ="receive_id*receive_date*challan_no*order_id*item_id*embel_name*embel_type*receive_qty*rate*amount*currency_id*remarks*updated_by*update_date";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$reciveid="txtSysno_".$i;
			$receive_date="txtReceiveDate_".$i;
			$challen_no="txtChallenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$embelid="embelid_".$i;
			$embelTypeid="embelTypeid_".$i;
			$wo_num="textWoNum_".$i;
			$quantity="txtQnty_".$i;
			$rate="txtRate_".$i;
			$amount="txtAmount_".$i;
			$curanci="curanci_".$i;
			$remarks="hiddRemarks_".$i;
			$updateid_dtls="updateiddtls_".$i;
			  
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$reciveid.",".$$receive_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$embelid.",".$$embelTypeid.",".$$quantity.",".$$rate.",".$$amount.",".$$curanci.",".$$remarks.",'".$bill_process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$reciveid."*".$$receive_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$embelid."*".$$embelTypeid."*".$$quantity."*".$$rate."*".$$amount."*".$$curanci."*".$$remarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$reciveid);
				$data_array_delivery[str_replace("'",'',$$reciveid)] =explode("*",("1"));
			}
		}
			  
		$rID1=execute_query(bulk_update_sql_statement("subcon_outbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		if($data_array1!="")
		{
			//echo "insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID1=sql_insert("subcon_outbound_bill_dtls",$field_array1,$data_array1,1);
		}
		
		if(str_replace("'",'',$delete_id)!="")
		{
			$delete_id=str_replace("'",'',$delete_id);
			$rID3=execute_query( "delete from subcon_outbound_bill_dtls where receive_id in ($delete_id)",0);
			$delete_id=explode(",",str_replace("'",'',$delete_id));
			for ($i=0;$i<count($delete_id);$i++)
			{
				$id_delivery[]=$delete_id[$i];
				$data_delivery[str_replace("'",'',$delete_id[$i])] =explode(",",("0"));
			}
		}
		
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}		
		disconnect($con);
		die;
	}
}

if($action=="fabric_finishing_print")
{
    extract($_REQUEST);
	//echo $data;
	$data=explode('*',$data);
	// echo "<pre>";
	// print_r($data);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$party_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	
	$sql_mst="SELECT id, bill_no, bill_date, supplier_id, remarks from subcon_outbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:100%;" align="center">
     <table width="880" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center">
                <?
                    $nameArray=sql_select( "SELECT plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
                    foreach ($nameArray as $result)
                    { 
                    ?>
                        Plot No: <? echo $result[csf('plot_no')]; ?> 
                        Level No: <? echo $result[csf('level_no')]?>
                        Road No: <? echo $result[csf('road_no')]; ?> 
                        Block No: <? echo $result[csf('block_no')];?> 
                        City No: <? echo $result[csf('city')];?> 
                        Zip Code: <? echo $result[csf('zip_code')]; ?> 
                        Province No: <?php echo $result[csf('province')];?> 
                        Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                        Email Address: <? echo $result[csf('email')];?> 
                        Website No: <? echo $result[csf('website')]; ?> <br>
                        <?
                    }
                ?> 
            </td>
        </tr>           
    	<tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong><? echo $data[3]; ?></strong></u></td>
            </tr>
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>                
            </tr>
            <tr>    
			<td width="130"><strong>Party Name :</strong></td> <td width="175"><? echo $party_library[$dataArray[0][csf('supplier_id')]]; ?></td>
			<td width="130"><strong>Remarks :</strong></td> <td width="175"><? echo $dataArray[0][csf('remarks')]; ?></td>     
            </tr>
        </table>
         <br>
	<div style="width:100%;" align="center">
		<table align="center" cellspacing="0" width="880"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="60" align="center">Challan No</th>
                <th width="65" align="center">Receive Date</th>
                <th width="70" align="center">Job No</th> 
                <th width="70" align="center">Order</th>
                <th width="70" align="center">Style</th> 
                <th width="120" align="center">Buyer</th>
                <th width="120" align="center">Garments Item</th>
                <th width="120" align="center">Embl. Name & Type</th>
                <th width="60" align="center">Gmts Qty</th>
				<th width="60" align="center">Currency</th>
                <th width="30" align="center">Rate</th>
                <th width="60" align="center">Amount</th>
            </thead>
		 <?
		$order_array=array();
		$sql_order="SELECT a.id, a.po_number, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		//echo $sql_order;
		$sql_order_result=sql_select($sql_order);
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		foreach ($sql_order_result as $row)
		{
			$order_array[$row[csf("id")]]['po_number']=$row[csf("po_number")];
			$order_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$order_array[$row[csf("id")]]['buyer_name']=$buyer_arr[$row[csf("buyer_name")]];
		}		
			//var_dump($order_array);
 		$i=1;
		$mst_id=$dataArray[0][csf('id')];
		$sql_result =sql_select("SELECT a.id, a.challan_no, a.receive_date, a.receive_qty, b.job_no_mst, a.order_id, a.item_id, a.embel_name, a.embel_type, a.rate, a.amount,a.color_id,CURRENCY_ID from subcon_outbound_bill_dtls a join wo_po_break_down b on a.order_id=b.id where a.mst_id='$mst_id' and a.status_active=1 and a.is_deleted=0"); 
		foreach($sql_result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($row[csf('embel_name')]==1) $embel_type=' & '.$emblishment_print_type[$row[csf('embel_type')]];
			elseif($row[csf('embel_name')]==2) $embel_type=' & '.$emblishment_embroy_type[$row[csf('embel_type')]];
			elseif($row[csf('embel_name')]==3) $embel_type=' & '.$emblishment_wash_type[$row[csf('embel_type')]];	
			elseif($row[csf('embel_name')]==4) $embel_type=' & '.$emblishment_spwork_type[$row[csf('embel_type')]];
			else $embel_type='';
		?>
		<tr bgcolor="<? echo $bgcolor; ?>"> 
            <td><? echo $i; ?></td>
            <td><p><? echo $row[csf('challan_no')]; ?></p></td>
            <td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
            <td><p><? echo $row[csf('job_no_mst')]; ?></p></td>
            <td><p><? echo $order_array[$row[csf("order_id")]]['po_number']; ?></p></td>
            <td><p><? echo $order_array[$row[csf("order_id")]]['style_ref_no']; ?></p></td>
            <td><p><? echo $order_array[$row[csf("order_id")]]['buyer_name']; ?></p></td>
           <!-- <td><p><? //echo $yarn_desc_arr[$row[csf('item_id')]]; ?></p></td>-->
            <td><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>                    
            <td><p><? echo $emblishment_name_array[$row[csf('embel_name')]].''.$embel_type; ?></p></td>
            <td align="right"><p><? echo $row[csf('receive_qty')]; $tot_receive_qty+=$row[csf('receive_qty')]; ?>&nbsp;</p></td>
			<td align="right"><p><? echo $currency[$row['CURRENCY_ID']] ?>&nbsp;</p></td>
            <td align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
            <td align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>
            <? 
			$carrency_id=$row['currency_id'];
			if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
		   ?>
        </tr>
        <?php
        $i++;
		}
		?>
    	<tr>
    	    <td>&nbsp;</td>
            <td align="right" colspan="8"><strong>Total</strong></td>
			<td align="right"><? echo $tot_receive_qty; ?>&nbsp;</td>
            <td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
            
		</tr>
       <tr>
           <td colspan="14" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
       </tr>
	   </table>
	   <table width="880" align="center" >
	        <tr>

	            <td colspan=14 align=left>&bull; Receiver should be aware of the quantity &amp; specification of the Product(s) at the time of taking delivery.</td>
	        </tr>
	        <tr>
	            <td colspan=14 align=left>&bull; No claim will be entertained after delivery of goods.</td>

	        </tr>
	        <tr>
	            <td colspan=14 align=left>&bull; Delivery Challan have been attached.</td>
	        </tr>
	        <tr>
	            <td colspan=14 align=left>&bull; Payment should be made within seven days from the bill date.</td>
	        </tr> 
	    </table>
	    <br>
		 <?
	        echo signature_table(298, $data[0], "880px");
	     ?>
   </div>
   </div>
<?
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('text_new_remarks').value=val;
		parent.emailwindow.hide();
	}
	</script>
    </head>
<body>
<div align="center">
	<fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                      <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td align="center">
                 <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
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
?>