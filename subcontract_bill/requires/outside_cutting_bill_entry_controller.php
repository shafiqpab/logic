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
	echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=22) order by supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $selected, "show_list_view(document.getElementById('cbo_company_id').value+'_'+this.value,'cutting_entry_list_view','cutting_info_list','requires/outside_cutting_bill_entry_controller','');","","","","","",5 );
	exit();
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=22) order by supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $selected, "","","","","","",5 );
	exit();
}



if ($action=="wonum_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1,'',1,'');
	$data=explode('_',$data);
	?>	
    <script>
		  function js_set_value(id)
		  { 
			  document.getElementById('hidd_item_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
    </head>
    <body>
        <form name="searchpofrm"  id="searchpofrm">
        <input type="hidden" id="hidd_item_id" />
        <div style="width:100%;">
        <table cellspacing="0" width="100%" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th width="150" align="center">Wo No</th>
                <th width="150" align="center">Supplier id </th>
                <th width="100" align="center">WO Date</th>
                <th width="50" align="center">Style</th>
                <th width="50" align="center">Rate</th>                    
                <th width="50" align="center">Uom</th>
            </thead>
        </table>
        </div>
        <div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" width="100%" class="rpt_table">
			<?  
                $supplier_library_arr=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
			    $i=1;
			    $sql_cond = "";
			    $sql_cond .= ($data[0]!="") ? " and a.company_id=$data[0]" : "";
			    $sql_cond .= ($data[1]!="") ? " and a.working_company_id=$data[1]" : "";
			    $sql_cond .= ($data[2]!="") ? " and b.po_id=$data[2]" : "";
			   
				$sql="SELECT a.id,a.sys_number,a.wo_date,a.cbo_source,a.working_company_id,a.currency,b.avg_rate,b.style_ref,b.uom from garments_service_wo_mst a,garments_service_wo_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.rate_for=20 $sql_cond";
				// echo $sql;die();
				$sql_result=sql_select($sql);
                foreach($sql_result as $row)
                {
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('sys_number')]."_".$row[csf('avg_rate')]."_".$row[csf('currency')]; ?>');" > 
                        <td width="50" align="center"><? echo $i; ?></td>
                        <td width="150" align="center"><p><? echo $row[csf('sys_number')]; ?></p></td>
                        <td width="150" align="center"><? echo $supplier_library_arr[$row[csf('working_company_id')]]; ?></td>
                        <td width="100" align="center"><? echo change_date_format($row[csf('wo_date')]); ?></td>
                        <td width="50" align="center"><? echo $row[csf('style_ref')]; ?></td>
                        <td width="50" align="center"><? echo $row[csf('avg_rate')]; ?></td>
                        <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
					</tr>
					<?
					$i++;
                }
                ?>
            </table>
        </div>
    </form>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
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
            <form name="cuttingbill_1"  id="cuttingbill_1" autocomplete="off">
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
									echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'outside_cutting_bill_entry_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );",0 );
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'cutting_bill_list_view', 'search_div', 'outside_cutting_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

if ($action=="cutting_bill_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name=" and supplier_id='$data[1]'"; else { echo "Please Select Party First."; die; }
	if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2],  "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[3],  "mm-dd-yyyy", "/",1)."'"; else $return_date="";
	if ($data[4]!='') $bill_id_cond=" and prefix_no_num='$data[4]'"; else $bill_id_cond="";
	
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	
	$arr=array (2=>$location,4=>$party_arr,5=>$knitting_source,6=>$bill_for);
	
	if($db_type==0)
	{
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$sql= "select id, bill_no, prefix_no_num, $year_cond, location_id, bill_date, supplier_id, bill_for from subcon_outbound_bill_mst where process_id=1 and status_active=1 $company_name $party_name $return_date $bill_id_cond";
	
	echo  create_list_view("list_view", "Bill No,Year,Location Name,Bill Date,Party Name,Bill For", "70,70,100,100,120,100","600","250",0, $sql , "js_set_value", "id", "", 1, "0,0,location_id,0,supplier_id,bill_for", $arr , "prefix_no_num,year,location_id,bill_date,supplier_id,bill_for", "outside_cutting_bill_entry_controller","",'0,0,0,3,0,0') ;
	exit(); 
}

if ($action=="load_php_data_to_form_issue")
{
	$nameArray= sql_select("select id, bill_no, company_id, location_id, bill_date, supplier_id from subcon_outbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/outside_cutting_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "load_drop_down( 'requires/outside_cutting_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "document.getElementById('cbo_supplier_company').value			= '".$row[csf("supplier_id")]."';\n"; 
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
	}
	exit();
}

if ($action=="cutting_entry_list_view")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,1,'');
	$data=explode('_',$data);
	?>
	<script>
	</script>
	</head>
	<body>
        <div style="width:100%;">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="797px" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="80">Challan No</th>
                    <th width="70">Recive Date</th>
                    <th width="110">Recive No</th>                    
                    <th width="200">Garments Item</th>
                    <th width="90">Recive Qty</th>
                    <th width="100">Order No</th>
                    <th>Buyer</th>
                </thead>
            </table>
        </div>
        <div style="width:800px;max-height:180px; overflow-y:scroll" id="cutting_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780px" class="rpt_table" id="list_view_issue">
            <? 
            $order_no=return_library_array( "select id,po_number from wo_po_break_down ",'id','po_number');
            $buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
            $i=1;
            if($data[2]=="") // Insert
            {
                $sql="select id, challan_no, production_date, po_break_down_id, item_number_id, serving_company, production_quantity from pro_garments_production_mst where company_id=$data[0] and serving_company=$data[1] and production_source=3 and production_type=1 and status_active=1 and is_deleted=0";			
            }
            else
            {
                $sql="(select id, challan_no, production_date, po_break_down_id, item_number_id, serving_company, production_quantity from pro_garments_production_mst where company_id=$data[0] and serving_company=$data[1] and id NOT IN (SELECT receive_id FROM subcon_outbound_bill_dtls) and production_source=3  and production_type=1 and status_active=1 and is_deleted=0) 
                union (select id, challan_no, production_date, po_break_down_id, item_number_id, serving_company, production_quantity from pro_garments_production_mst where company_id=$data[0] and serving_company=$data[1]  and id IN (SELECT receive_id FROM subcon_outbound_bill_dtls) and production_source=3  and production_type=1 and status_active=1 and is_deleted=0)";
            }
            $sql_result=sql_select($sql);
            foreach($sql_result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            
                if($row[csf('recid')]==1) $bgcolor="yellow";
                ?>
                <tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="80" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td width="70" align="center"><? echo change_date_format($row[csf('production_date')]); ?></td>
                    <td width="110" align="center"><? echo $row[csf('recv_number')]; ?></td>
                    <td width="200" align="center"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                    <td width="90" align="center"><? echo $row[csf('production_quantity')]; ?></td>
                    <td width="100" align="center"><? echo $order_no[$row[csf("po_break_down_id")]]; ?></td>
                    <td align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>
                    <input type="hidden" id="currid<? echo $row[csf('id')]; ?>" value="<? echo $row[csf('id')]; ?>"></td>
                </tr>
                <?php
                $i++;
            }
            ?>
        </table>
        </div>
        <table width="780">
            <tr>
                <td colspan="8" align="center">
                    <input type="button" id="show_button" class="formbutton" style="width:100px" value="Close" onClick="window_close()" />
                </td>
            </tr>
        </table>
        <div>
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
    // $bill_id=array_intersect(explode(",",$data[0]), explode(",",$data[2]));
	$bill_id=array_intersect(explode(",",$data[0]), explode(",",$data[1]));
	$delete_id=array_diff(explode(",",$data[1]), explode(",",$data[0]));
	$del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id); $delete_id=implode(",",$delete_id);
	//echo $del_id;  
	$order_array=array();
	$sql_order="SELECT a.id, a.po_number, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$sql_order_result=sql_select($sql_order);
	foreach ($sql_order_result as $row)
	{
		$order_array[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$order_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$order_array[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
	}
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name'); 
	if( $data[2]!="" )//update===========
	{
		$sql="SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id,wo_order_no, receive_qty as order_qnty, uom, rate, amount, remarks FROM subcon_outbound_bill_dtls  WHERE mst_id=$data[2] and process_id=1";
	}
	else //insert=================
	{
		if($bill_id!="" && $del_id!="")
			$sql="(SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id,wo_order_no, receive_qty as order_qnty, uom, rate, amount, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id='1' )
			 union 
			 (SELECT 0 as upd_id, id as receive_id, production_date as receive_date, challan_no, po_break_down_id as order_id, item_number_id as prod_id,wo_order_no, sum(production_quantity) as order_qnty, 0 as uom, 0 as rate, 0 as amount, null as remarks FROM pro_garments_production_mst WHERE id in ($del_id) and production_source=3 and production_type=1 and status_active=1 and is_deleted=0 group by id, production_date, challan_no, po_break_down_id, item_number_id,wo_order_no)";
		else if($bill_id!="" && $del_id=="")
			$sql="SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id,wo_order_no, receive_qty as order_qnty, uom, rate, amount, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id='1' ";
		else if($bill_id=="" && $del_id!="")
			$sql="SELECT 0 as upd_id, id as receive_id, production_date as receive_date, challan_no, po_break_down_id as order_id, item_number_id as prod_id, wo_order_no,sum(production_quantity) as order_qnty, 0 as uom, 0 as rate, 0 as amount, null as remarks FROM pro_garments_production_mst WHERE id in ($del_id) and production_source=3 and production_type=1 and status_active=1 and is_deleted=0 group by id, production_date, challan_no, po_break_down_id, item_number_id,wo_order_no";
	}	
    // echo $sql;
	$k=0;
	$sql_result=sql_select($sql);
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		 $k++;
		 if( $data[2]!="" )
		 {
			 if($data[1]=="") $data[1]=$row[csf("receive_id")]; else $data[1].=",".$row[csf("receive_id")];
		 }
	?>
       <tr align="center">				
            <td>
				 <? if ($k==$num_rowss) { ?>
                    <input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:80px" value="<? echo $data[1]; ?>" />
                    <input type="hidden" name="delete_id" id="delete_id"  style="width:80px" value="<? echo $delete_id; ?>" />
                 <? } ?>
                <input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
                <input type="hidden" name="reciveid_<? echo $k; ?>" id="reciveid_<? echo $k; ?>" value="<? echo $row[csf("receive_id")]; ?>"> 
                <input type="text" name="txtreceivedate_<? echo $k; ?>" id="txtreceivedate_<? echo $k; ?>"  class="datepicker" style="width:65px" value="<? echo change_date_format($row[csf("receive_date")]); ?>" readonly />									
            </td>
            <td>
                <input type="text" name="txt_challenno_<? echo $k; ?>" id="txt_challenno_<? echo $k; ?>"  class="text_boxes" style="width:85px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
            </td>
            <td>
                <input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>" style="width:40px" readonly /> 
                <input type="text" name="txtorderno_<? echo $k; ?>" id="txtorderno_<? echo $k; ?>"  class="text_boxes" style="width:55px" value="<? echo $order_array[$row[csf("order_id")]]['po_number']; ?>" readonly />										
            </td>
            <td>
                <input type="text" name="txt_stylename_<? echo $k; ?>" id="txt_stylename_<? echo $k; ?>"  class="text_boxes" style="width:75px;" value="<? echo $order_array[$row[csf("order_id")]]['style_ref_no']; ?>" readonly />
            </td>
            <td>
                <input type="text" name="txt_partyname_<? echo $k; ?>" id="txt_partyname_<? echo $k; ?>"  class="text_boxes" style="width:85px" value="<? echo $buyer_arr[$order_array[$row[csf("order_id")]]['buyer_name']]; ?>" readonly />								
            </td>
            <td>
                <input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("prod_id")]; ?>">
                <input type="text" name="txt_febricdesc_<? echo $k; ?>" id="txt_febricdesc_<? echo $k; ?>"  class="text_boxes" style="width:125px" value="<? echo $garments_item[$row[csf("prod_id")]]; ?>" readonly />
            </td>
            <td>
                <input type="text" name="text_wo_num_<? echo $k; ?>" id="text_wo_num_<? echo $k; ?>"  class="text_boxes" style="width:60px" value="<? echo $row[csf("wo_order_no")]; ?>" placeholder="Browse" onDblClick="openmypage_wonum(<? echo $k; ?>);" readonly />   <!--placeholder="Browse" onDblClick="openmypage_wonum();" -->
                <input type="hidden" name="text_wo_id_<? echo $k; ?>" id="text_wo_id_<? echo $k; ?>">
            </td>
            <td>
				<? echo create_drop_down( "cbouom_$k", 45, $unit_of_measurement,"", 0, "--Select UOM--",1,"","","1,2","" );?>
            </td>
            <td>
                <input type="text" name="txt_qnty_<? echo $k; ?>" id="txt_qnty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf("order_qnty")]; ?>" />
            </td>
            <td>
                <input type="text" name="txt_rate_<? echo $k; ?>" id="txt_rate_<? echo $k; ?>"  class="text_boxes_numeric" style="width:40px;" value="<? echo $row[csf("rate")]; ?>" onBlur="amount_caculation(<? echo $k; ?>);" />
            </td>
            <td>
				<?
					$total_amount=$row[csf("order_qnty")]*$row[csf("rate")];
                ?>
                <input type="text" name="txt_amount_<? echo $k; ?>" id="txt_amount_<? echo $k; ?>" style="width:60px;"  class="text_boxes_numeric" value="<? echo $row[csf("amount")]; ?>" readonly  />                	
            </td>
            <td>
                <input type="text" name="txt_remarks_<? echo $k; ?>" id="txt_remarks_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $row[csf("remarks")]; ?>" />
            </td>
        </tr>
	<?	
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id="1";
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
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'CUT', date("Y",time()), 5, "select prefix_no,prefix_no_num from  subcon_outbound_bill_mst where company_id=$cbo_company_id and process_id=$bill_process_id $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_outbound_bill_mst",1); 	
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, supplier_id, process_id, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_supplier_company.",".$bill_process_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
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
		$field_array1 ="id, mst_id, receive_id, receive_date, challan_no, order_id, item_id, receive_qty, uom, rate, amount,wo_order_no, remarks, process_id, inserted_by, insert_date";
		$field_array_up ="receive_id*receive_date*challan_no*order_id*item_id*receive_qty*uom*rate*amount*wo_order_no*remarks*updated_by*update_date";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$reciveid="reciveid_".$i;
			$receive_date="txtreceivedate_".$i;
			$challen_no="txt_challenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$wo_num="text_wo_num_".$i;
			$cbouom="cbouom_".$i;
			$quantity="txt_qnty_".$i;
			$rate="txt_rate_".$i;
			$amount="txt_amount_".$i;
			$remarks="txt_remarks_".$i;
			$updateid_dtls="updateiddtls_".$i;
			  
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$reciveid.",".$$receive_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$quantity.",".$$cbouom.",".$$rate.",".$$amount.",".$$wo_num.",".$$remarks.",'".$bill_process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$reciveid."*".$$receive_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$quantity."*".$$cbouom."*".$$rate."*".$$amount."*".$$wo_num."*".$$remarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
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
		$field_array="location_id*bill_date*supplier_id*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_supplier_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_outbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$id1=return_next_id( "id","subcon_outbound_bill_dtls",1);
		$field_array1 ="id, mst_id, receive_id, receive_date, challan_no, order_id, item_id, receive_qty, uom, rate, amount,wo_order_no, remarks, process_id, inserted_by, insert_date";
		$field_array_up ="receive_id*receive_date*challan_no*order_id*item_id*receive_qty*uom*rate*amount*wo_order_no*remarks*updated_by*update_date";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$reciveid="reciveid_".$i;
			$receive_date="txtreceivedate_".$i;
			$challen_no="txt_challenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$wo_num="text_wo_num_".$i;
			$cbouom="cbouom_".$i;
			$quantity="txt_qnty_".$i;
			$rate="txt_rate_".$i;
			$amount="txt_amount_".$i;
			$remarks="txt_remarks_".$i;
			$updateid_dtls="updateiddtls_".$i;
			  
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$reciveid.",".$$receive_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$quantity.",".$$cbouom.",".$$rate.",".$$amount.",".$$wo_num.",".$$remarks.",'".$bill_process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$reciveid."*".$$receive_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$quantity."*".$$cbouom."*".$$rate."*".$$amount."*".$$wo_num."*".$$remarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
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
?>