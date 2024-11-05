<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------
if($db_type==0) { $find_inset="FIND_IN_SET(a.id,b.pi_id)";	}
if($db_type==2 || $db_type==1){ $find_inset=" FIND_IN_SET(a.id,b.pi_id)>0 "; }
//load drop down supplier
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );

// user credential data prepare start
$userCredential = sql_select("SELECT store_location_id, unit_id as company_id, company_location_id, supplier_id, buyer_id, item_cate_id FROM user_passwd where id=$user_id");
$store_location_id = $userCredential[0][csf('store_location_id')];
$company_id = $userCredential[0][csf('company_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];

$supplier_id = $userCredential[0][csf('supplier_id')];
$buyer_id = $userCredential[0][csf('buyer_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if (trim($company_id) !='') {
    $company_credential_cond = "and a.company_id in($company_id)";
}

if (trim($company_location_id) !='') {
    $company_location_credential_cond = "and id in($company_location_id)";
}

if (trim($store_location_id )!='') {
    $store_location_credential_cond = "and b.store_location_id in($store_location_id)";
}

if (trim($supplier_id) !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}


if(trim($item_cate_id) !='') {
	$cre_cat_arr=explode(",",$item_cate_id);
	$selected_category=array( '5', '6', '7', '23' );
	$filteredArr = array_intersect( $cre_cat_arr, $selected_category );
    $item_cate_credential_cond = implode(",",$filteredArr); ;
}
else
{
	$item_cate_credential_cond="5,6,7,23";
}

// user credential data prepare end

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b, lib_supplier_party_type c where a.id=c.supplier_id and a.id=b.supplier_id $supplier_credential_cond and b.tag_company='$data' and c.party_type='3' and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	exit();
}
if($action=="load_drop_down_knit_com_new")
{
	extract($data);
	
	$sql="SELECT DISTINCT a.id, a.supplier_name FROM lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c  WHERE   a.id = b.supplier_id AND b.supplier_id = c.supplier_id AND a.status_active = 1 and b.party_type='3' AND c.tag_company = $data GROUP BY a.id, a.supplier_name UNION ALL  SELECT DISTINCT c.id, c.supplier_name FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier  c, inv_receive_master  d  WHERE   c.id = b.supplier_id AND a.supplier_id = b.supplier_id AND c.id = d.supplier_id	and b.party_type='3' AND a.tag_company = $data AND c.status_active IN (1, 3) AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";
	
	echo create_drop_down( "cbo_supplier", 170, "$sql","id,supplier_name", 1, "-- Select --", 0, "",0 );
	exit();
}

//load drop down store
/*if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $company_credential_cond $store_location_credential_cond and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", 0, "",0 );
	exit();
}
*/

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where company_id='$data' $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/chemical_dyes_receive_controller*5_6_7_23', 'store','store_td', $('#cbo_company_id').val(), this.value);" );
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/chemical_dyes_receive_controller",$data);
}

if ($action=="load_drop_down_loan_party")
{
	echo create_drop_down( "cbo_loan_party", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) group by a.id, a.supplier_name order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","","" );
	exit();
}

if ($action=="load_drop_down_item_group")
{
	echo create_drop_down( "cbo_item_group_id", 130,"select id,item_name from lib_item_group where item_category='$data' and status_active=1","id,item_name", 1, "-- Select --", "", "","","","","","");

	exit();
}


if ($action=="load_drop_down_basis")
{
	$data=explode("**",$data);
	$com_id=$data[0];
	$manu_id=$data[1];
	$variable_set_basis=return_field_value("independent_controll","variable_settings_inventory","company_name=$com_id and menu_page_id=4 and variable_list=20 and status_active=1 and is_deleted=0","independent_controll");
	if($variable_set_basis==1) $basis_previlige="1,2,6,7"; else $basis_previlige="1,2,4,6,7";
	echo create_drop_down( "cbo_receive_basis", 170, $receive_basis_arr,"", 1, "- Select Receive Basis -", $selected, "fn_independent(this.value)","","$basis_previlige" );
	exit();
}

if ($action=="load_drop_down_rate")
{
	$data=explode("**",$data);
	$com_id=$data[0];
	$manu_id=$data[1];
	$variable_set_rate=return_field_value("rate_edit","variable_settings_inventory","company_name=$com_id and menu_page_id=$manu_id and variable_list=20","rate_edit");
	if($variable_set_rate==2) $read_only="readonly"; else $read_only="";
	echo '<input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" style="width:120px;" onBlur="fn_calile()" value="0" '. $read_only.' />';
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=263 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print1').hide();\n";
	echo "$('#print2').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print1').show();\n";}
			if($id==84){echo "$('#print2').show();\n";}
		}
	}
	else
	{
		echo "$('#print1').show();\n";
		echo "$('#print2').show();\n";
	}

	exit();
}


// wo/pi popup here----------------------//
if ($action=="wopi_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
	function js_set_value(str)
	{
		var splitData = str.split("_");
		//alert(splitData[2]);
		if(splitData[2] == "No")
		{ 
			alert("Goods receive not allowed against Un-Approved P.O. Please ensure the P.O is approved before receiving the goods"); return; 
		}
		
		$("#hidden_tbl_id").val(splitData[0]); // wo/pi id
		$("#hidden_wopi_number").val(splitData[1]); // wo/pi number
		$("#hidden_is_non_ord_sample").val(splitData[2]); // wo/pi number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th width="150">Search By</th>
                    <th width="150" align="center" id="search_by_th_up">Enter WO/PI Number</th>
                    <th width="170" align="center" id="search_by_th_up">Suppliers</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?
                            echo create_drop_down( "cbo_search_by", 170, $receive_basis_arr,"",1, "--Select--", $receive_basis,"",1 );
                        ?>
                    </td>
                    <td width="150" align="center" id="search_by_td">
                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <? echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b,lib_supplier_party_type c where a.id=c.supplier_id and a.id=b.supplier_id and b.tag_company='$company' and c.party_type='3' and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );?>
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                     </td>
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_year_selection').value, 'create_wopi_search_list_view', 'search_div', 'chemical_dyes_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
            </tr>
        	<tr>
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-->
                    <input type="hidden" id="hidden_tbl_id" value="" />
                    <input type="hidden" id="hidden_wopi_number" value="" />
                    <input type="hidden" id="hidden_is_non_ord_sample" value="" />
                    <!-- -END-->
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
}

if($action=="create_wopi_search_list_view")
{

 	$ex_data = explode("_",$data);
//     print_r($ex_data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
    $supplier_id = $ex_data[5];
    $year = $ex_data[6];

	//echo $txt_date_from."===".$company;die;

	$sql_cond="";

	if(trim($txt_search_by)==1) // for pi
	{
		if(trim($txt_search_common)!="") $sql_cond .= " and a.pi_number LIKE '%$txt_search_common%'";
		if( $txt_date_from!= "" && $txt_date_to != "" )
		{
			if($db_type==0)
			{
				$sql_cond .= " and a.pi_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and
				'".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			if($db_type==2 || $db_type==1)
			{
				$sql_cond .= " and a.pi_date  between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."'
				and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'";
			}
		}else{
            $sql_cond .= " and extract( YEAR from a.pi_date ) = $year";
        }
		if(trim($company)!="") $sql_cond .= " and a.importer_id=$company";
        if(trim($supplier_id)!= 0) $sql_cond .= " and a.supplier_id=$supplier_id";

        $approval_status_cond="";
		if($db_type==0)
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		$approval_status=sql_select($approval_status);
		if($approval_status[0][csf('approval_need')]==1)
		{
			$approval_status_cond= "and a.approved = 1";
		}
	}
	else if(trim($txt_search_by)==2) // for wo
	{
		if(trim($txt_search_common)!="") $sql_cond .= " and a.wo_number LIKE '%$txt_search_common%'";
		if( $txt_date_from!="" && $txt_date_to!="" )
		{
			if($db_type==0)
			{
				$sql_cond .= " and wo_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and
				'".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			if($db_type==2 || $db_type==1)
			{
				 $sql_cond .= " and wo_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd',"-",1)."' and
				 '".change_date_format($txt_date_to,'yyyy-mm-dd',"-",1)."'";
			}
		}else{
            $sql_cond .= " and extract( YEAR from wo_date ) = $year";
        }
		if(trim($company)>0) $sql_cond .= " and company_name=$company";
        if(trim($supplier_id) != 0) $sql_cond .= " and supplier_id=$supplier_id";

    }
	
	else if(trim($txt_search_by)==7) // for Req
	{
		if(trim($txt_search_common)!="") $sql_cond .= " and a.requ_no LIKE '%$txt_search_common%'";
		if( $txt_date_from!="" && $txt_date_to!="" )
		{
			if($db_type==0)
			{
				$sql_cond .= " and a.requisition_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and
				'".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			if($db_type==2 || $db_type==1)
			{
				 $sql_cond .= " and a.requisition_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd',"-",1)."' and
				 '".change_date_format($txt_date_to,'yyyy-mm-dd',"-",1)."'";
			}
		}else{
            $sql_cond .= " and extract( YEAR from a.requisition_date ) = $year";
        }
		if(trim($company)>0) $sql_cond .= " and a.company_id=$company";
	}


	//echo $sql_cond ;die;

    if($txt_search_by==1 ) //PI based
    {
        if($db_type == 2)
        {
           $sql = "select a.id as id,a.pi_number as wopi_number,c.lc_number as lc_number,a.pi_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source, a.item_category_id as item_category_id, sum(nvl(e.quantity, 0)) as currentQty
           from com_pi_master_details a left join com_pi_item_details e on e.pi_id = a.id left join com_btb_lc_pi b on a.id=b.pi_id left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
		   where a.entry_form=227 and a.status_active=1 and a.is_deleted=0 and a.goods_rcv_status<>1 $sql_cond $approval_status_cond 
		   group by  a.id,a.pi_number,c.lc_number,a.pi_date,a.supplier_id, a.currency_id,a.source, a.item_category_id order by a.id DESC";

           $sqlRcv = return_library_array("select sum(cons_quantity) as totalrcvqty, pi_wo_batch_no from inv_transaction where transaction_type = 1 and company_id = $company and receive_basis = 1 and item_category in(5,6,7,23) and status_active=1 and is_deleted=0  group by pi_wo_batch_no", "PI_WO_BATCH_NO", "TOTALRCVQTY");
           $sqlRcvReturn = return_library_array("select sum(a.cons_quantity) as totalrcvreturnqty, c.booking_id from inv_transaction a, inv_issue_master b, inv_receive_master c where b.id = a.mst_id  and c.id = b.received_id and b.entry_form = 28 and a.transaction_type = 3 and a.item_category in(5,6,7,23) and a.company_id = $company group by c.booking_id", "BOOKING_ID", "TOTALRCVRETURNQTY");
        }
        else
        {
            $sql = "select a.id as id,a.pi_number as wopi_number,c.lc_number as lc_number,a.pi_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source, a.item_category_id as item_category_id, sum(ifnull(e.quantity, 0)) as currentQty
           from com_pi_master_details a left join com_pi_item_details e on e.pi_id = a.id left join com_btb_lc_pi b on a.id=b.pi_id left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
		   where a.entry_form=227 and a.status_active=1 and a.is_deleted=0 and a.goods_rcv_status<>1 $sql_cond $approval_status_cond 
		   group by a.id,a.pi_number,c.lc_number,a.pi_date,a.supplier_id, a.currency_id,a.source, a.item_category_id order by a.id DESC";
           $sqlRcv = return_library_array("select sum(cons_quantity) as totalrcvqty, pi_wo_batch_no from inv_transaction where transaction_type = 1 and company_id = $company and receive_basis = 1 and item_category in(5,6,7,23) and status_active=1 and is_deleted=0 group by pi_wo_batch_no", "PI_WO_BATCH_NO", "TOTALRCVQTY");
           $sqlRcvReturn = return_library_array("select sum(a.cons_quantity) as totalrcvreturnqty, c.booking_id from inv_transaction a, inv_issue_master b, inv_receive_master c where b.id = a.mst_id  and c.id = b.received_id and b.entry_form = 28 and a.transaction_type = 3 and a.company_id = $company group by c.booking_id", "BOOKING_ID", "TOTALRCVRETURNQTY");
        }
    }
    else if($txt_search_by==2) //WO/Booking based
    {
        $sql = "select a.id, a.wo_number as wopi_number, ' ' as lc_number, a.wo_date as wopi_date, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, a.item_category as item_category_id, a.entry_form, a.is_approved, sum(b.supplier_order_quantity) as currentQty
		from wo_non_order_info_mst a, wo_non_order_info_dtls b
		where a.id = b.mst_id and  a.status_active=1 and a.is_deleted=0 and a.entry_form=145 and a.pay_mode!=2 $sql_cond 
		group by a.id, a.wo_number, ' ', a.wo_date, a.supplier_id, a.currency_id, a.source, a.item_category, a.entry_form, a.is_approved 
		order by a.id DESC";
        // $sqlRcv = return_library_array("select sum(cons_quantity) as totalrcvqty, pi_wo_batch_no from inv_transaction where transaction_type = 1 and company_id = $company and receive_basis = 2 group by pi_wo_batch_no", "PI_WO_BATCH_NO", "TOTALRCVQTY");
		$sqlRcv = return_library_array("SELECT sum(b.cons_quantity) as totalrcvqty, b.pi_wo_batch_no from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.entry_form = 4 and  b.transaction_type = 1 and b.company_id = $company and b.receive_basis = 2 and item_category in(5,6,7,23) and a.status_active=1 and b.status_active=1 group by b.pi_wo_batch_no", "PI_WO_BATCH_NO", "TOTALRCVQTY");
        $sqlRcvReturn = return_library_array("select sum(a.cons_quantity) as totalrcvreturnqty, c.booking_id from inv_transaction a, inv_issue_master b, inv_receive_master c where b.id = a.mst_id  and c.id = b.received_id and b.entry_form = 28 and a.transaction_type = 3 and a.item_category in(5,6,7,23) and a.company_id = $company group by c.booking_id", "BOOKING_ID", "TOTALRCVRETURNQTY");
		
		$current_date = date('m/d/Y');
		if($db_type==0)
		{ 
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($current_date,'yyyy-mm-dd')."' and company_id='$company')) and page_id=21 and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($current_date, "", "",1)."' and company_id='$company')) and page_id=21 and status_active=1 and is_deleted=0";
		}
		
		//echo $approval_status;
		
		$approval_status=sql_select($approval_status);
		$approve_status[145]['status']=$approval_status[0][csf('approval_need')];
		$approve_status[145]['allow_partial']=$approval_status[0][csf('allow_partial')];

		$sql_data=sql_select($sql);
		foreach($sql_data as $val)
		{
			if($approve_status[$val[csf('entry_form')]]['status']==1)
			{
				if($approve_status[$val[csf('entry_form')]]['allow_partial']==1)
				{
					if($val[csf('is_approved')]==1 || $val[csf('is_approved')]==3)
					{
						$appr_status[$val[csf('id')]]="Yes";
					}
					else
					{
						$appr_status[$val[csf('id')]]="No";
					}
				}
				else
				{
					if($val[csf('is_approved')]==1)
					{
						$appr_status[$val[csf('id')]]="Yes";
					}
					else
					{
						$appr_status[$val[csf('id')]]="No";
					}
				}
			}
			else
			{
				$appr_status[$val[csf('id')]]="N/A";
			}	
		}

    }
    else if($txt_search_by==7) //Requisition based
    {
        $sql = "select a.id, a.requ_no as wopi_number, ' ' as lc_number, a.requisition_date  as wopi_date, 0 as supplier_id, a.cbo_currency as currency_id, a.source as source, 0 as item_category_id, sum(b.quantity) as currentQty
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pay_mode=4 and a.entry_form=69 and b.item_category in(5,6,7,23) $sql_cond
		group by a.id, a.requ_no, a.requisition_date, a.cbo_currency, a.source order by a.id DESC";//supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
        $sqlRcv = return_library_array("select sum(cons_quantity) as totalrcvqty, pi_wo_batch_no from inv_transaction where transaction_type = 1 and company_id = $company and receive_basis = 7 and item_category in(5,6,7,23) and status_active=1 and is_deleted=0 group by pi_wo_batch_no", "PI_WO_BATCH_NO", "TOTALRCVQTY");
        $sqlRcvReturn = return_library_array("select sum(a.cons_quantity) as totalrcvreturnqty, c.booking_id from inv_transaction a, inv_issue_master b, inv_receive_master c where b.id = a.mst_id  and c.id = b.received_id and b.entry_form = 28 and a.transaction_type = 3 and a.item_category in(5,6,7,23) and a.company_id = $company group by c.booking_id", "BOOKING_ID", "TOTALRCVRETURNQTY");
    }
	$result = sql_select($sql);
 	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where  status_active=1",'id','supplier_name');
//	$arr=array(3=>$supplier_arr,4=>$currency,5=>$source,6=>$item_category);
	?>
    <div>
        <table class="rpt_table" id="rpt_tablelist_view" rules="all" width="910" cellspacing="0" cellpadding="0" border="0">
            <thead>
            <tr>
                <th width="50">SL No</th>
                <th width="120">WO/PI/Req No</th>
                <th width="120"> LC </th>
                <th width="80">Date</th>
                <th width="200"> Supplier</th>
                <th width="80"> Currency</th>
                <th width="120"> Source</th>
                <th> Item Category</th>
            </tr>
            </thead>
        </table>
    </div>
    <div style="max-height:260px; width:908px; overflow-y:scroll" id="">
        <table class="rpt_table" id="list_view" rules="all" width="888" height="" cellspacing="0" cellpadding="0" border="0">
            <tbody>
            <?
            $i = 0;
            foreach ($result as $key => $row){
                $bgcolor = '';
                if(($i+1) % 2 != 0){
                    $bgcolor = '#FFFFFF';
                }
                if($row[csf("currentQty")] > ($sqlRcv[$row[csf("id")]] - $sqlRcvReturn[$row[csf("id")]])){
            ?>
                    <tr id="tr_<?=($i+1)?>" height="20" style="cursor: pointer; vertical-align: middle;" bgcolor="<?=$bgcolor?>" onClick="js_set_value('<?=$row[csf("id")].'_'.$row[csf("wopi_number")]."_".$appr_status[$row[csf('id')]];?>')">
                        <td width="50" align="center"><?=($i+1)?></td>
                        <td width="120" align="center"><?=$row[csf("wopi_number")]?></td>
                        <td width="120" align="center"><?=$row[csf("lc_number")]?></td>
                        <td width="80" align="center"><?=$row[csf("wopi_date")]?></td>
                        <td width="200" align="center"><?=$supplier_arr[$row[csf("supplier_id")]]?></td>
                        <td width="80" align="center"><?=$currency[$row[csf("currency_id")]]?></td>
                        <td width="120" align="center"><?=$source[$row[csf("source")]]?></td>
                        <td align="center"><?=$item_category[$row[csf("item_category_id")]]?></td>
                    </tr>
            <?
                    $i++;
                }
            }
            ?>

            </tbody>
        </table>
    </div>
    <?
//    echo  create_list_view("list_view", "WO/PI/Req No, LC ,Date, Supplier, Currency, Source, Item Category","120,120,80,200,80,120","900","260",0, $sql , "js_set_value", "id,wopi_number", "", 1, "0,0,0,supplier_id,currency_id,source,item_category_id", $arr, "wopi_number,lc_number,wopi_date,supplier_id,currency_id,source,item_category_id", "",'','0,0,3,0,0,0,0') ;
	exit();

}

//for gate entry
// wo/pi popup here----------------------//
if ($action=="gate_search")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
	function js_set_value(str)
	{
		var splitData = str.split("_");
		$("#hidden_system_id").val(splitData[1]); // wo/pi id

		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th width="150">Sample </th>
                    <th width="150">Supplier </th>
                    <th width="150" align="center" id="search_by_th_up"> Item Category</th>
                     <th width="150" align="center" >Chalan no</th>
                    <th width="200">Receive Date</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        	<?

                                    	echo create_drop_down( "cbo_sample", 150, "select id,sample_name from lib_sample where status_active=1 and is_deleted=0 order by sample_name","id,sample_name",1, "-- Select --", 0, "" );
                                    ?>
                    </td>
                    <td width="180" align="center">
                           <?

                              echo create_drop_down( "cbo_supplier", 150, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
                          ?>
                    </td>
                    <td align="center">
                         <?
					     	echo create_drop_down( "cbo_item_category", 130,$item_category,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_item_group', 'item_group_td' )","","5,6,7,23","","","");
                           ?>
                     </td>
                     <td align="center">
                        <input type="text" id="txt_chalan_no" name="txt_chalan_no" class="text_boxes" />
                     </td>
                     <td>
                       <input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:120px;" placeholder="Select Date" />

                     </td>
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_sample').value+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_chalan_no').value+'_'+document.getElementById('txt_receive_date').value+'_'+<? echo $company; ?>, 'create_gate_list_view', 'search_div', 'chemical_dyes_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
            </tr>
        	<tr>
            	<td align="center" height="40" valign="middle" colspan="4">

                    <!-- Hidden field here-->
                    <input type="hidden" id="hidden_system_id" value="" />

                    <!-- END-->
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
}



if($action=="create_gate_list_view")
{

 	$ex_data = explode("_",$data);
	$sample = $ex_data[0];
	$supplier = $ex_data[1];
	$item_category = $ex_data[2];
	$chalan_no = $ex_data[3];
	$txt_receive_date = $ex_data[4];
	$company=$ex_data[5];

	$sql_cond="";

	  if(str_replace("'","",$sample)!=0)  $sql_cond .= " and a.sample=".str_replace("'","",$sample)."";
	   if(str_replace("'","",$supplier)!=0)  $sql_cond .= " and a.supplier_name=".str_replace("'","",$supplier)."";
	    if(str_replace("'","",$item_category)!=0)
		{ $sql_cond .= " and a.item_category=".str_replace("'","",$item_category)."";} else {$sql_cond .= " and a.item_category in(5,6,7,23)";}
		 if(str_replace("'","",$chalan_no)!="")  $sql_cond .= " and a.challan_no='".str_replace("'","",$chalan_no)."'";

			if(str_replace("'","",$txt_receive_date)!="" )
			{
			if($db_type==0){$sql_cond .= " and a.receive_date='".change_date_format($txt_receive_date,'yyyy-mm-dd')."'";}
			if($db_type==2 || $db_type==1){$sql_cond .= " and a.receive_date='".change_date_format($txt_receive_date, "yyyy-mm-dd", "-",1)."'";}
			}
			if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";




    $sql="select a.id,a.sys_number,a.item_category,a.sample,a.supplier_name ,a.challan_no ,a.receive_date from inv_gate_in_mst a where a.status_active=1 and a.is_deleted=0 $sql_cond and a.status_active=1 and a.is_deleted=0";

	$result = sql_select($sql);
	$item_category_arr=array(5=>"Chemicals",6=>"Dyes",7=>"Auxilary Chemicals");
 	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where   status_active=1",'id','supplier_name');
	$sample_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	$arr=array(0=>$item_category_arr,3=>$sample_arr,2=>$supplier_arr);
	echo  create_list_view("list_view", "Item Category,Gate Entry No, Supplier, Sample, Chalan No ,Receive Date","120,170,150,150,120,120","860","260",0, $sql , "js_set_value", "id,sys_number", "", 1, "item_category,0,sample,supplier_name,0,0", $arr, "item_category,sys_number,sample,supplier_name,challan_no,receive_date", "",'','0,0,0,0,0,0') ;
	exit();

}

if($action=="populate_data_from_wopi_popup")
{
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];

	if($receive_basis==1 )
	{
		if($db_type == 2){
			$sql = "select c.id as id, c.lc_number as lc_number,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source, 2 as pay_mode
			from com_pi_master_details a 
			left join com_btb_lc_pi b on a.id=b.pi_id
			left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
			where a.entry_form=227 and a.status_active=1 and a.is_deleted=0 and a.id=$wo_pi_ID";
		}else{
			$sql = "select b.id as id, b.lc_number as lc_number,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source
			from com_pi_master_details a left join com_btb_lc_master_details b on $find_inset
			where a.entry_form=227 and a.status_active=1 and a.is_deleted=0 and a.id=$wo_pi_ID";
		}
	}
	else if($receive_basis==2)
	{
 		$sql = "select id,'' as lc_number, supplier_id as supplier_id, currency_id as currency_id, source as source, pay_mode
				from wo_non_order_info_mst
				where
				status_active=1 and is_deleted=0 and
				entry_form=145 and
				id=$wo_pi_ID";

	}
	else if($receive_basis==7) //Requisition
	{
 		$sql = "select id, requ_no as wopi_number, '' as lc_number, requisition_date as wopi_date, 0 as supplier_id, cbo_currency as currency_id, source as source, pay_mode as pay_mode, 0 as pi_id 
				from inv_purchase_requisition_mst
				where status_active=1 and is_deleted=0 and pay_mode=4 and id=$wo_pi_ID";
	}
	

	$result = sql_select($sql);
	foreach($result as $row)
	{
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		echo "$('#cbo_source').val(".$row[csf("source")].");\n";
		echo "$('#txt_lc_no').val('".$row[csf("lc_number")]."');\n";
		echo "$('#cbo_pay_mode').val('".$row[csf("pay_mode")]."');\n";
		echo "$('#cbo_pay_mode').attr('disabled','disabled');\n";
		
		if($row[csf("lc_number")]!="")
		{
			echo "$('#hidden_lc_id').val(".$row[csf("id")].");\n";
		}
		if($row[csf("currency_id")]==1)
		{
			echo "$('#txt_exchange_rate').val(1);\n";
			echo "$('#txt_exchange_rate').attr('disabled','disabled');\n";
		}
		if($row[csf("currency_id")]!=1)
		{
			$sql1 = sql_select("select exchange_rate,max(id) from inv_receive_master where item_category in(5,6,7,23)");
			foreach($sql1 as $row1)
			{
				echo "$('#txt_exchange_rate').val(".$row1[csf("exchange_rate")].");\n";
			}

			echo "$('#txt_exchange_rate').removeAttr('disabled','disabled');\n";
		}
	}
	exit();
}

if($action=="set_exchange_rate")
{
	if($db_type==2) $sql1 =sql_select("select id, exchange_rate from inv_receive_master where currency_id=$data and rownum <= 1 order by id desc");
	else if($db_type==0) $sql1 = sql_select("select id, exchange_rate from inv_receive_master where currency_id=$data order by id desc limit 01,01");
	foreach($sql1 as $row1)
	{
		echo $row1[csf("exchange_rate")];
	}
}

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
	$currency_rate=set_conversion_rate( $data[0], $conversion_date,$data[2] );
	echo "1"."_".$currency_rate;
	exit();
}





if($action=="show_product_listview")
{
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$prev_rcv_result=sql_select("SELECT A.ID AS MST_ID, C.ITEM_GROUP_ID, C.ITEM_DESCRIPTION, C.SUB_GROUP_NAME, C.ITEM_SIZE, C.MODEL, C.ITEM_NUMBER, C.ITEM_CODE, min(B.PROD_ID) as PROD_ID, SUM(B.CONS_QUANTITY) AS QNTY 
	FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, PRODUCT_DETAILS_MASTER C 
	WHERE A.ID=B.MST_ID AND B.PROD_ID=C.ID AND A.ENTRY_FORM=4 AND B.TRANSACTION_TYPE=1 AND B.ITEM_CATEGORY IN(5,6,7,23) AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 and C.STATUS_ACTIVE in(1,3) AND C.IS_DELETED=0 AND A.RECEIVE_BASIS = $receive_basis AND A.BOOKING_ID=$wo_pi_ID  
    GROUP BY A.ID, C.ITEM_GROUP_ID, C.ITEM_DESCRIPTION, C.SUB_GROUP_NAME, C.ITEM_SIZE, C.MODEL, C.ITEM_NUMBER, C.ITEM_CODE");
	foreach($prev_rcv_result as $row)
	{
		$mst_id_arr[$row["MST_ID"]]=$row["MST_ID"];
	}
	//print_r($mst_id_arr);
	$prev_rev_rtn=sql_select("SELECT A.RECEIVED_ID, C.ITEM_GROUP_ID, C.ITEM_DESCRIPTION, C.SUB_GROUP_NAME, C.ITEM_SIZE, C.MODEL, C.ITEM_NUMBER, C.ITEM_CODE, min(B.PROD_ID) as PROD_ID, SUM(B.CONS_QUANTITY) AS QNTY 
	FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, PRODUCT_DETAILS_MASTER C 
	WHERE A.ID=B.MST_ID AND B.PROD_ID=C.ID AND A.ENTRY_FORM=28 AND B.TRANSACTION_TYPE=3 AND B.ITEM_CATEGORY IN(5,6,7,23) AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 and C.STATUS_ACTIVE in(1,3) AND C.IS_DELETED=0 AND A.RECEIVED_ID IN(".implode(",",$mst_id_arr).")  
    GROUP BY A.RECEIVED_ID, C.ITEM_GROUP_ID, C.ITEM_DESCRIPTION, C.SUB_GROUP_NAME, C.ITEM_SIZE, C.MODEL, C.ITEM_NUMBER, C.ITEM_CODE");
	foreach($prev_rev_rtn as $row)
	{
		$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
		$prev_rcv_rtn_array[$row["RECEIVED_ID"]][$item_key]+=$row["QNTY"];
	}
	unset($prev_rev_rtn);
	
	foreach($prev_rcv_result as $row)
	{
		$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
		$prev_rcv_array[$item_key]["previous_rcv_qnty"] += $row["QNTY"]-$prev_rcv_rtn_array[$row["MST_ID"]][$item_key];
	}
	//echo "<pre>";print_r($prev_rcv_array);
	if($receive_basis==1) // pi basis
	{
		if($db_type==2)
		{
			$dtls_id_str=",listagg(cast(b.id as varchar(4000)),',') within group (order by b.id)  as id";
		}else{
			$dtls_id_str=",group_concat(d.id) as id";
		}
		$sql = "select a.importer_id as company_id, a.supplier_id, min(b.item_prod_id) as item_id, c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code, sum(b.quantity) as quantity $dtls_id_str 
		from com_pi_master_details a, com_pi_item_details b, product_details_master c 
		where a.id=b.pi_id and b.item_prod_id=c.id and a.id=$wo_pi_ID and b.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted = 0 and c.status_active in(1,3) and c.is_deleted=0 
		group by a.importer_id, a.supplier_id, c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code";

	}
	else if($receive_basis==2) // wo basis
	{
		if($db_type==2)
		{
			$dtls_id_str=",listagg(cast(b.id as varchar(4000)),',') within group (order by b.id)  as id";
		}else{
			$dtls_id_str=",group_concat(d.id) as id";
		}
		
		$sql = "select a.company_name as company_id, a.supplier_id, min(b.item_id) as item_id, c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code, sum(b.supplier_order_quantity) as quantity $dtls_id_str
			from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c
			where a.id=b.mst_id and b.item_id=c.id and a.id=$wo_pi_ID and b.item_category_id in(5,6,7,23) and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.status_active in(1,3) and c.is_deleted=0
			group by a.company_name, a.supplier_id, c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code";
	}
	else if($receive_basis==7) // requisition basis 
	{
		if($db_type==2)
		{
			$dtls_id_str=",listagg(cast(b.id as varchar(4000)),',') within group (order by b.id)  as id";
		}else{
			$dtls_id_str=",group_concat(d.id) as id";
		}
		
		$sql = "select a.company_id as company_id, 0 as supplier_id, min(b.product_id) as item_id, c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code, sum(b.quantity) as quantity $dtls_id_str
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and a.id=$wo_pi_ID and b.item_category in(5,6,7,23) and a.pay_mode=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0
		group by a.company_id, c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code";
		
	}
	//echo $sql;//die;
	$result = sql_select($sql);

	if($db_type==0)
	{
		$product_name_details=return_library_array( "select id, concat(sub_group_name,' ',item_description,' ',item_size ) as product_name_details from product_details_master where item_category_id in(5,6,7,23) and status_active=1 and is_deleted=0",'id','product_name_details');
	}
	if($db_type==2 || $db_type==1)
	{
		$product_name_details=return_library_array( "select id, concat(concat(sub_group_name,item_description),item_size ) as product_name_details from product_details_master where item_category_id in(5,6,7,23) and status_active=1 and is_deleted=0",'id','product_name_details');
	}
	//echo 
	//$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$i=1;
	?>

    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" rules="all" width="320">
        	<thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Product Name</th>
                    <th width="40">Qnty</th>
                    <th width="60">Prev Rcv Qnty</th>
                    <th>Balance Qnty</th>
                </tr>
            </thead>
            <tbody>
            	<? foreach($result as $row)
				{

					if ($i%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					//c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code
					//$item_key=$row["ITEM_GROUP_ID"]."**".$row["ITEM_DESCRIPTION"]."**".$row["SUB_GROUP_NAME"]."**".$row["ITEM_SIZE"]."**".$row["MODEL"]."**".$row["ITEM_NUMBER"]."**".$row["ITEM_CODE"];
					$item_key=$row[csf("item_group_id")]."**".trim($row[csf("item_description")])."**".trim($row[csf("sub_group_name")])."**".trim($row[csf("item_size")])."**".trim($row[csf("model")])."**".trim($row[csf("item_number")])."**".trim($row[csf("item_code")]);
					?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $receive_basis."**".$row[csf("id")]."**".$row[csf("company_id")]."**".$row[csf("supplier_id")]."**".$row[csf("item_id")]."**".$row[csf("quantity")]."**".$wo_pi_ID;?>","wo_pi_product_form_input","requires/chemical_dyes_receive_controller")' style="cursor:pointer" >
                		<td align="center"><? echo $i; ?></td>
                    	<td><? echo $product_name_details[$row[csf("item_id")]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],'2'); ?></td>
                        <td align="right"><? echo number_format($prev_rcv_array[$item_key]["previous_rcv_qnty"], '2'); ?></td>
						<? $balance_qnty = ($row[csf('quantity')]- $prev_rcv_array[$item_key]["previous_rcv_qnty"]); ?>
                        <td align="right"><? echo number_format($balance_qnty,'2'); ?></td>
                    </tr>
                <? $i++; } ?>
            </tbody>
        </table>
     </fieldset>
	<?
	exit();
}


// get form data from product click in right side
if($action=="wo_pi_product_form_input")
{
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$company_id = $ex_data[2];
	$supplier_id = $ex_data[3];
	$product_id = $ex_data[4];
	$wo_po_qnty = $ex_data[5];
	$wo_pi_dtls_id = $ex_data[6];
	
	if($receive_basis==1) // pi basis
	{
  		$sql = "select a.pi_id as wo_pi_mst_id, a.item_prod_id as product_id, a.uom, sum(a.quantity) as quantity, sum(a.amount) as gross_amount, sum(a.net_pi_amount) as amount, b.id, b.item_category_id, b.lot, b.item_group_id, b.item_description, b.sub_group_name, b.item_size, b.model, b.item_number, b.item_code
		from com_pi_item_details a, product_details_master b
		where a.item_prod_id=b.id and  a.id in($wo_pi_ID) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.pi_id, a.item_prod_id, a.uom, b.id, b.item_category_id, b.lot, b.item_group_id, b.item_description, b.sub_group_name, b.item_size, b.model, b.item_number, b.item_code";
	}

	else if($receive_basis==2) // wo basis
	{
 		$sql = "select a.mst_id as wo_pi_mst_id, a.item_id as product_id, a.uom, sum(a.supplier_order_quantity) as quantity, sum(a.gross_amount) as gross_amount, sum(a.amount) as amount, b.id,  b.item_category_id, b.lot, b.item_group_id, b.item_description, b.sub_group_name, b.item_size, b.model, b.item_number, b.item_code
		from wo_non_order_info_dtls a,  product_details_master b
		where a.item_id=b.id and a.id in ($wo_pi_ID) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by  a.mst_id, a.item_id, a.uom, b.id, b.item_category_id, b.lot, b.item_group_id, b.item_description, b.sub_group_name, b.item_size, b.model, b.item_number, b.item_code";
	}
	else if($receive_basis==7) // Req basis
	{
 		$sql = "select a.mst_id as wo_pi_mst_id, a.product_id as product_id, a.cons_uom as uom, sum(a.quantity) as quantity, sum(a.amount) as gross_amount, sum(a.amount) as amount, b.id, b.item_category_id, b.lot, b.item_group_id, b.item_description, b.sub_group_name, b.item_size, b.model, b.item_number, b.item_code
		from inv_purchase_requisition_dtls a, product_details_master b
		where a.product_id=b.id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.mst_id, a.product_id, a.cons_uom, b.id, b.item_category_id, b.lot, b.item_group_id, b.item_description, b.sub_group_name, b.item_size, b.model, b.item_number, b.item_code";
	}
	else
	{
		$sql = "select 0 as wo_pi_mst_id, id as product_id,item_category_id, item_group_id, item_description,  unit_of_measure as  uom, avg_rate_per_unit as rate
		from product_details_master
		where id=$product_id and status_active=1 and is_deleted=0";
	}

	$result = sql_select($sql);
	//$item_key=$row[csf("item_group_id")]."**".$row[csf("item_description")]."**".$row[csf("sub_group_name")]."**".$row[csf("item_size")]."**".$row[csf("model")]."**".$row[csf("item_number")]."**".$row[csf("item_code")];
	foreach($result as $row)
	{
		//C.ITEM_GROUP_ID, C.ITEM_DESCRIPTION, C.SUB_GROUP_NAME, C.ITEM_SIZE, C.MODEL, C.ITEM_NUMBER, C.ITEM_CODE 
		$prod_conds="";
		if($row[csf("item_group_id")]>0) $prod_conds.=" and C.item_group_id='".$row[csf("item_group_id")]."'";
		if($row[csf("item_description")]!='') $prod_conds.=" and trim(C.item_description)='".trim($row[csf("item_description")])."'";		
		if(trim($row[csf("sub_group_name")])!='') $prod_conds.=" and trim(C.sub_group_name)='".trim($row[csf("sub_group_name")])."'"; else $prod_conds.=" and trim(C.sub_group_name) is null";
		if(trim($row[csf("item_size")])!='') $prod_conds.=" and trim(C.item_size)='".trim($row[csf("item_size")])."'"; else $prod_conds.=" and trim(C.item_size) is null";
		if(trim($row[csf("model")])!='') $prod_conds.=" and trim(C.model)='".trim($row[csf("model")])."'"; else $prod_conds.=" and trim(C.model) is null";
		if(trim($row[csf("item_number")])!='') $prod_conds.=" and trim(C.item_number)='".trim($row[csf("item_number")])."'"; else $prod_conds.=" and trim(C.item_number) is null";
		if(trim($row[csf("item_code")])) $prod_conds.=" and trim(C.item_code)='".trim($row[csf("item_code")])."'"; else $prod_conds.=" and trim(C.item_code) is null";
		$rcv_sql = sql_select("SELECT A.ID AS MST_ID, SUM(B.CONS_QUANTITY) AS QNTY 
		FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, product_details_master C 
		WHERE A.ID=B.MST_ID AND B.PROD_ID=C.ID AND B.COMPANY_ID=$company_id AND B.TRANSACTION_TYPE=1 AND B.ITEM_CATEGORY IN(5,6,7,23) AND B.RECEIVE_BASIS=$receive_basis AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND A.BOOKING_ID='".$row[csf("wo_pi_mst_id")]."' $prod_conds
		group by A.ID");
		foreach($rcv_sql as $rows)
		{
			$mst_id_arr[$rows["MST_ID"]]=$rows["MST_ID"];
		}
		
		$prev_rev_rtn=sql_select("SELECT A.RECEIVED_ID, SUM(B.CONS_QUANTITY) AS QNTY 
		FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, product_details_master C 
		WHERE A.ID=B.MST_ID AND B.PROD_ID=C.ID AND A.ENTRY_FORM=28 AND B.TRANSACTION_TYPE=3 AND B.ITEM_CATEGORY IN(5,6,7,23) AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND A.RECEIVED_ID IN(".implode(",",$mst_id_arr).") $prod_conds  
    	GROUP BY A.RECEIVED_ID");
		$prev_rcv_rtn_array=array();
		foreach($prev_rev_rtn as $val)
		{
			$prev_rcv_rtn_array[$val["RECEIVED_ID"]]+=$val["QNTY"];
		}
		unset($prev_rev_rtn);
		$totalRcvQnty=0;
		foreach($rcv_sql as $rows)
		{
			$totalRcvQnty+=$rows["QNTY"]-$prev_rcv_rtn_array[$rows["MST_ID"]];
		}
		
		$rate=$row[csf("amount")]/$row[csf("quantity")];
		//$totalRcvQnty = return_field_value("sum(b.cons_quantity) as recv_qnty","product_details_master a, inv_transaction b, inv_receive_master c","$whereCondition","recv_qnty");

		echo "reset_form('','','cbo_item_category_id*cbo_item_group_id*txt_description*txt_product_id*txt_lot*cbo_uom*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_bla_order_qty*txt_expire_date*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin','','','');\n";
	 	echo "$('#cbo_item_category_id').val(".$row[csf("item_category_id")].");\n";
		echo "$('#cbo_item_group_id').val(".$row[csf("item_group_id")].");\n";
		echo "$('#txt_description').val('".$row[csf("item_description")]."');\n";
		echo "$('#txt_product_id').val('".$row[csf("product_id")]."');\n";
		echo "$('#cbo_uom').val(".$row[csf("uom")].");\n";
		echo "$('#txt_lot').attr('disabled',false).attr('readonly',false);\n";
		//echo $wo_po_qnty.'='.$totalRcvQnty;
		$orderQnty = $wo_po_qnty-$totalRcvQnty;
		echo "$('#txt_bla_order_qty').val(".$orderQnty.");\n";

		if($receive_basis !=4 && $receive_basis !=6)
		{
			echo "$('#txt_rate').val(".$rate.");\n";
		}
		echo "set_button_status(0, permission, 'fnc_chemical_dyes_receive_entry',1,1);\n";
	}


	exit();
}





// LC popup here----------------------//
if ($action=="lc_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
function js_set_value(str)
{
		var splitData = str.split("_");
		$("#hidden_tbl_id").val(splitData[0]); // wo/pi id
		$("#hidden_wopi_number").val(splitData[1]); // wo/pi number
		parent.emailwindow.hide();
}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
	<table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th width="150">Search By</th>
                    <th width="150" align="center" id="search_by_td_up">Enter WO/PI Number</th>
                    <th>
                    	<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                   		<!-- Hidden field here -->
                        <input type="hidden" id="hidden_tbl_id" value="" />
                        <input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number" />
                        <!-- -END -->
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?
                            $search_by_arr=array(0=>'LC Number',1=>'Supplier Name');
							$dd="change_search_event(this.value, '0*1', '0*select id, supplier_name from lib_supplier', '../../') ";
							echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="180" align="center" id="search_by_td">
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lc_search_list_view', 'search_div', 'chemical_dyes_receive_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
           	 	</tr>
            </tbody>
        </table>
        <div align="center" valign="top" id="search_div"> </div>
        </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}


if($action=="create_lc_search_list_view")
{
	$ex_data = explode("_",$data);
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company = $ex_data[2];

	if($cbo_search_by==1 && $txt_search_common!="") // lc number
	{
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where lc_number LIKE '%$search_string%' and importer_id=$company and item_category_id in(5,6,7,23) and is_deleted=0 and status_active=1";
	}
	else if($cbo_search_by==1 && $txt_search_common!="") //supplier
	{
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where supplier_id='$search_string' and importer_id=$company and item_category_id in(5,6,7,23) and is_deleted=0 and status_active=1";
	}
	else
	{
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where importer_id=$company and item_category_id in(5,6,7,23) and is_deleted=0 and status_active=1";
	}

	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$company_arr,2=>$supplier_arr,3=>$item_category);
	echo  create_list_view("list_view", "LC No,Importer,Supplier Name,Item Category,Value","120,150,150,120,120","750","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,item_category_id,0", $arr, "lc_number,importer_id,supplier_id,item_category_id,lc_value", "",'','0,0,0,0,0,1') ;
	exit();

}


if($action=="show_ile")
{
	$ex_data = explode("**",$data);
	$company = $ex_data[0];
	$source = $ex_data[1];
	$rate = $ex_data[2];
	$cbo_item_category_id = $ex_data[3];
	$cbo_item_group_id = $ex_data[4];

	$sql="select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category='$cbo_item_category_id' and status_active=1 and is_deleted=0 order by id";
	//echo $sql;
	$result=sql_select($sql);
	foreach($result as $row)
	{
		// NOTE :- ILE=standard, ILE% = standard/100*rate
		$ile = $row[csf("standard")];
		$ile_percentage = ( $row[csf("standard")]/100 )*$rate;
		echo $ile."**".number_format($ile_percentage,$dec_place[3],".","");
		exit();
	}
	exit();
}



//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$variable_lot = str_replace("'","",$variable_lot);
	$txt_lot=strtoupper($txt_lot);
	$txt_receive_qty=str_replace(",","",$txt_receive_qty);
	$txt_receive_qty=str_replace("'","",$txt_receive_qty);
	$txt_amount=str_replace("'","",$txt_amount);
	$txt_rate = str_replace("'","",$txt_rate);
	
	
	if($db_type==0)
	{
		$concattS = explode(",",return_field_value(" concat(unit_of_measure,',',conversion_factor) as cons_uom","product_details_master","id=$txt_product_id and status_active=1 and is_deleted=0","cons_uom")); 
	}
	if($db_type==2)
	{
		$concattS = explode(",",return_field_value("(unit_of_measure || ',' ||conversion_factor) as cons_uom","product_details_master","id=$txt_product_id and status_active=1 and is_deleted=0","cons_uom")); 
	}
	$cons_uom = $concattS[0];
	$conversion_factor = $concattS[1];
	
	$variable_set_invent=return_field_value("user_given_code_status","variable_settings_inventory","company_name=$cbo_company_id and variable_list=19 and item_category_id=$cbo_item_category_id","user_given_code_status");
	
	$sql_variable_setup="select over_rcv_percent, over_rcv_payment from variable_inv_ile_standard where company_name = $cbo_company_id and category=$cbo_item_category_id and variable_list = 23 and status_active=1 and is_deleted=0";
	$sql_variable_setup_result=sql_select($sql_variable_setup);
	$variable_over_rcv_percent=$sql_variable_setup_result[0][csf("over_rcv_percent")];
	$variable_over_rcv_payment=$sql_variable_setup_result[0][csf("over_rcv_payment")];
    $previous_prod_id=str_replace("'","",$txt_product_id);
	

	$update_cond = "";
	if( $operation==1 )
	{
		$update_cond .= " and id <> $update_dtls_id ";
	}
	// check MRR Auditing Report is Audited or Not
	if (str_replace("'",'',$update_id) !='')
	{
		$is_audited=return_field_value("is_audited","inv_receive_master","id=".str_replace("'",'',$update_id)." and status_active=1 and is_deleted=0","is_audited");
		//echo "10**$is_audited".'rakib';die;
		if($is_audited==1) {
			echo "50**This MRR is Audited. Save, Update and Delete Not Allowed..";
			die;
		}
	}
	
	if( $operation==0 )
	{
		$product_table_update=1;
		$prod_scrtipt="";
		
		$rate = str_replace("'","",$txt_rate);
		$txt_ile = str_replace("'","",$txt_ile);
		
		$ile = ($txt_ile/$rate)*100; // ile cost to ile
		$ile_cost = str_replace("'","",$txt_ile); //ile cost = (ile/100)*rate
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
 		$cons_rate = number_format($domestic_rate,10,".","");//number_format($rate*$exchange_rate,$dec_place[3],".","");
		$con_amount = $cons_rate*$txt_receive_qty;
		$con_ile = $ile;//($ile/$domestic_rate)*100;
		$con_ile_cost = ($ile/100)*($rate*$exchange_rate);
		//echo "10 ** $rate = $cons_rate $txt_receive_qty $con_amount "; oci_rollback($con);disconnect($con);die;
		if($variable_lot==1)
		{
			$prod_sql="select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE, LOT from product_details_master where status_active=1 and is_deleted=0 and id=$txt_product_id";
			//echo "10**=".$prod_sql;die;
			$prod_sql_result=sql_select($prod_sql);
			$prod_company_id=$prod_sql_result[0]["COMPANY_ID"];
			$prod_item_category_id=$prod_sql_result[0]["ITEM_CATEGORY_ID"];
			$prod_item_group_id=$prod_sql_result[0]["ITEM_GROUP_ID"];
			$prod_sub_group_name=trim($prod_sql_result[0]["SUB_GROUP_NAME"]);
			$prod_item_description=trim($prod_sql_result[0]["ITEM_DESCRIPTION"]);
			$prod_item_size=trim($prod_sql_result[0]["ITEM_SIZE"]);
			$prod_model=trim($prod_sql_result[0]["MODEL"]);
			$prod_item_number=trim($prod_sql_result[0]["ITEM_NUMBER"]);
			$prod_item_code=trim($prod_sql_result[0]["ITEM_CODE"]);
			$prod_lot=$prod_sql_result[0]["LOT"];
			
			if($prod_sub_group_name=='') $prod_conds.=" and sub_group_name is null"; else $prod_conds.=" and trim(sub_group_name)='$prod_sub_group_name'";
			if($prod_item_description=='') $prod_conds .=" and item_description is null"; else $prod_conds.=" and trim(item_description)='$prod_item_description'";
			if($prod_item_size=='') $prod_conds .=" and item_size is null"; else $prod_conds.=" and trim(item_size)='$prod_item_size'";
			if($prod_model=='') $prod_conds .=" and model is null"; else $prod_conds.=" and trim(model)='$prod_model'";
			if($prod_item_number=='') $prod_conds .=" and item_number is null"; else $prod_conds.=" and trim(item_number)='$prod_item_number'";
			if($prod_item_code=='') $prod_conds .=" and item_code is null"; else $prod_conds.=" and trim(item_code)='$prod_item_code'";
			
			$row_prod="select ID, CURRENT_STOCK, AVG_RATE_PER_UNIT, STOCK_VALUE from product_details_master where status_active=1 and is_deleted=0 and company_id=$prod_company_id and item_category_id=$prod_item_category_id and item_group_id=$prod_item_group_id $prod_conds and lot ='".str_replace("'","",$txt_lot)."'";
			//echo "10**=".$row_prod;die;
			$row_prod_result=sql_select($row_prod);
			
			if(count($row_prod_result)<1)
			{
				$count_prod="select count(id) as TOT_ROW, max(lot) as LOT, max(ID) as PROD_ID from product_details_master where status_active=1 and is_deleted=0 and company_id=$prod_company_id and item_category_id=$prod_item_category_id and item_group_id=$prod_item_group_id and lot is null $prod_conds";
				//echo "10**=".$count_prod;die;
				$count_prod_result=sql_select($count_prod);
				$count_prod_row=$count_prod_result[0]["TOT_ROW"];
				$count_prod_lot=$count_prod_result[0]["LOT"];
				//echo "10**=".$count_prod_lot;die;
				if($count_prod_row==1)
				{
					$txt_product_id=$count_prod_result[0]["PROD_ID"];
					$prod_scrtipt="update product_details_master set lot='".str_replace("'","",$txt_lot)."' where id=$txt_product_id and status_active=1 and is_deleted=0";
				}
				else
				{
					$previous_prod_id=str_replace("'","",$txt_product_id);
					$txt_product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$prod_scrtipt="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, item_size, model, item_number, is_compliance, inserted_by, insert_date) 
					select	
					'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), '".str_replace("'","",$txt_lot)."', item_code, unit_of_measure, '".str_replace("'","",$cons_rate)."', '".str_replace("'","",$txt_receive_qty)."', '".str_replace("'","",$txt_receive_qty)."', '".str_replace("'","",$con_amount)."', yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, item_size, model, item_number, '".str_replace("'","",$cbo_zero_discharge)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0";
					$product_table_update=0;
				}
				//$tot_prod_row=return_field_value
			}
			else
			{
				$txt_product_id=$row_prod_result[0]["ID"];
			}
		}
	}

	$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_product_id and store_id=$cbo_store_name $update_cond and status_active= 1 and is_deleted = 0", "max_date");
	if($max_issue_date !="")
	{
		$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
		$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));

		if ($receive_date < $max_issue_date)
		{
			echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}
	
	
	//if(str_replace("'","",$txt_lot)=="") $txt_lot=0; else $txt_lot=str_replace("'","",$txt_lot);
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//check_table_status( $_SESSION['menu_id'],0);
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		if($variable_set_invent==1)
		{
			$challan_no=str_replace("'","",$txt_challan_no);
			if($challan_no!="")
			{
				$variable_set_invent=return_field_value("a.id as id"," inv_gate_in_mst a,  inv_gate_in_dtl b","a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=cbo_item_category_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
				if(empty($variable_set_invent))
				{
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";disconnect($con);die;
				}

			}
		}


		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate=is_duplicate_field("b.id","inv_receive_master a, inv_transaction b","a.id=b.mst_id and a.id=$update_id and b.prod_id=$txt_product_id and b.transaction_type=1 and status_active=1 and is_deleted=0");
		if($duplicate==1)
		{
			//check_table_status( $_SESSION['menu_id'],0);
			echo "20**Duplicate Product is Not Allow in Same MRR Number.";
			disconnect($con);die;
		}
		
		
		$rcv_basis=str_replace("'","",$cbo_receive_basis);
		$wo_pi_req_id=str_replace("'","",$txt_wo_pi_id);
		$cr_prod_id=str_replace("'","",$txt_product_id);
		if($variable_over_rcv_payment==1 && $rcv_basis !=4 && $rcv_basis !=6)
		{

			$prod_sql="select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE, LOT from product_details_master where status_active=1 and is_deleted=0 and id=$previous_prod_id";
			//echo "10**=".$prod_sql;die;
			$prod_sql_result=sql_select($prod_sql);
			$prod_company_id=$prod_sql_result[0]["COMPANY_ID"];
			$prod_item_category_id=$prod_sql_result[0]["ITEM_CATEGORY_ID"];
			$prod_item_group_id=$prod_sql_result[0]["ITEM_GROUP_ID"];
			$prod_sub_group_name=$prod_sql_result[0]["SUB_GROUP_NAME"];
			$prod_item_description=$prod_sql_result[0]["ITEM_DESCRIPTION"];
			$prod_item_size=$prod_sql_result[0]["ITEM_SIZE"];
			$prod_model=$prod_sql_result[0]["MODEL"];
			$prod_item_number=$prod_sql_result[0]["ITEM_NUMBER"];
			$prod_item_code=$prod_sql_result[0]["ITEM_CODE"];
			$prod_lot=$prod_sql_result[0]["LOT"];
			
			if($prod_sub_group_name=='') $woPi_prod_conds.=" and b.sub_group_name is null"; else $woPi_prod_conds.=" and b.sub_group_name='$prod_sub_group_name'";
			if($prod_item_description=='') $woPi_prod_conds .=" and b.item_description is null"; else $woPi_prod_conds.=" and b.item_description='$prod_item_description'";
			if($prod_item_size=='') $woPi_prod_conds .=" and b.item_size is null"; else $woPi_prod_conds.=" and b.item_size='$prod_item_size'";
			if($prod_model=='') $woPi_prod_conds .=" and b.model is null"; else $woPi_prod_conds.=" andb. model='$prod_model'";
			if($prod_item_number=='') $woPi_prod_conds .=" and b.item_number is null"; else $woPi_prod_conds.=" and b.item_number='$prod_item_number'";
			if($prod_item_code=='') $woPi_prod_conds .=" and b.item_code is null"; else $woPi_prod_conds.=" and b.item_code='$prod_item_code'";
			
            $prev_return_sql=sql_select("select sum(b.cons_quantity) as qnty from inv_issue_master a, inv_transaction b, inv_receive_master c 
			where a.id = b.mst_id and a.received_id = c.id and a.entry_form = 28 and b.transaction_type=3 and c.booking_id = $wo_pi_req_id  and b.prod_id=$cr_prod_id and a.status_active=1 and b.status_active=1 group by c.booking_id");
			
            $totalRtnQnty=$prev_return_sql[0][csf('qnty')]/$conversion_factor;
            $prev_rcv_sql=sql_select("select sum(c.cons_quantity) as qnty from inv_receive_master a, inv_transaction c, product_details_master b 
			where a.id = c.mst_id and c.prod_id=b.id and a.entry_form = 4 and c.transaction_type=1 and a.receive_basis = $rcv_basis and a.booking_id = $wo_pi_req_id and c.item_category=$cbo_item_category_id and b.item_category_id=$cbo_item_category_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $woPi_prod_conds");

			$prev_rcv_qnty=$prev_rcv_sql[0][csf("qnty")]-$totalRtnQnty;
			$tot_qnty=$prev_rcv_qnty+$txt_receive_qty;
			
			
			if($rcv_basis==1)
			{
				$wo_pi_req_sql=sql_select(" select sum(a.quantity) as quantity from com_pi_item_details a, product_details_master b where a.item_prod_id=b.id and a.status_active=1  and b.status_active=1 and a.pi_id=$wo_pi_req_id $woPi_prod_conds");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
				
			}
			else if($rcv_basis==2)
			{
				$wo_pi_req_sql=sql_select(" select sum(a.supplier_order_quantity) as quantity from wo_non_order_info_dtls a, product_details_master b  where a.item_id=b.id and a.status_active=1 and b.status_active=1 and a.mst_id=$wo_pi_req_id $woPi_prod_conds");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
			}
			else
			{
				$wo_pi_req_sql=sql_select(" select sum(a.quantity) as quantity from inv_purchase_requisition_dtls a, product_details_master b where a.product_id=b.id and b.status_active=1 and b.status_active=1 and a.mst_id=$wo_pi_req_id $woPi_prod_conds");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
			}
			
			$allow_qnty=($wo_pi_req_qnty+(($wo_pi_req_qnty/100)*$variable_over_rcv_percent));
			//echo "30** MRR Quantity Not Allow More Then PI/Wo/Req Allowed Quantity. $tot_qnty = $allow_qnty";disconnect($con);die;
			if($tot_qnty>$allow_qnty)
			{
				echo "30** MRR Quantity Not Allow More Then PI/Wo/Req Allowed Quantity. $tot_qnty = $allow_qnty";disconnect($con);die;
			}
		}
		
		

		$cd_recv_num=''; $cd_update_id=''; $flag=1;

		if(str_replace("'","",$update_id)=="")
		{
             $new_chemical_dyes_recv_system_id = explode("*", return_next_id_by_sequence("inv_receive_master_pk_seq", "inv_receive_master",$con,1,$cbo_company_id,'CDR',4,date("Y",time()),0 ));
			//echo "10**";print_r($new_chemical_dyes_recv_system_id);die;
			//$id=return_next_id( "id", "inv_receive_master", 1 ) ;
			$id = return_next_id_by_sequence("inv_receive_master_pk_seq", "inv_receive_master", $con);


			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category,company_id, receive_basis,receive_purpose,loan_party, receive_date,  booking_id, booking_no, booking_without_order,challan_no, challan_date, store_id, location_id,supplier_id,lc_no, currency_id,exchange_rate, source,gate_entry_no,supplier_referance,pay_mode, boe_mushak_challan_no, boe_mushak_challan_date, gate_entry_date, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_chemical_dyes_recv_system_id[1]."',".$new_chemical_dyes_recv_system_id[2].",'".$new_chemical_dyes_recv_system_id[0]."',4,".$cbo_item_category_id.",".$cbo_company_id.",".$cbo_receive_basis.",".$cbo_receive_purpose.",".$cbo_loan_party.",".$txt_receive_date.",".$txt_wo_pi_id.",".$txt_wo_pi.",1,".$txt_challan_no.",".$txt_challan_date.",".$cbo_store_name.",".$cbo_location.",".$cbo_supplier.",".$hidden_lc_id.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_source.",".$txt_gate_entry.",".$txt_sup_ref.",".$cbo_pay_mode.",".$txt_boe_mushak_challan_no.",".$txt_boe_mushak_challan_date.",".$txt_gate_entry_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//echo "insert into inv_receive_master (".$field_array.") values ".$data_array;

			$cd_recv_num=$new_chemical_dyes_recv_system_id[0];
			$cd_update_id=$id;
		}
		else
		{
			
			$field_array_update="item_category*receive_purpose*loan_party*receive_date*challan_no*challan_date*location_id*supplier_id*lc_no*source*gate_entry_no*supplier_referance*pay_mode*boe_mushak_challan_no*boe_mushak_challan_date*gate_entry_date*updated_by*update_date";

			$data_array_update=$cbo_item_category_id."*".$cbo_receive_purpose."*".$cbo_loan_party."*".$txt_receive_date."*".$txt_challan_no."*".$txt_challan_date."*".$cbo_location."*".$cbo_supplier."*".$hidden_lc_id."*".$cbo_source."*".$txt_gate_entry."*".$txt_sup_ref."*".$cbo_pay_mode."*".$txt_boe_mushak_challan_no."*".$txt_boe_mushak_challan_date."*".$txt_gate_entry_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


			$cd_recv_num=str_replace("'","",$txt_mrr_no);
			$cd_update_id=str_replace("'","",$update_id);
			
			$prev_store_id=return_field_value("store_id","inv_transaction","status_active=1 and transaction_type=1 and mst_id=$cd_update_id","store_id");
			if($prev_store_id<>str_replace("'","",$cbo_store_name))
			{
				echo "30** Multiple Store Not Allowed In Same MRR";disconnect($con);die;
			}
		}
		
		// yarn details table entry here START-----------------------------------//
		
		//$dtlsid = return_next_id("id", "inv_transaction", 1);
		$dtlsid = return_next_id_by_sequence("inv_transaction_pk_seq", "inv_transaction", $con);
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		
		$field_array1 = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id, order_uom, order_qnty, order_rate, order_ile,order_ile_cost, order_amount, cons_uom, cons_quantity, cons_rate, cons_ile, cons_ile_cost, cons_amount,balance_qnty, balance_amount,floor_id,room,rack, self,bin_box, batch_lot,expire_date, manufacture_date,remarks,inserted_by,insert_date,store_rate,store_amount";
 		$data_array1= "(".$dtlsid.",".$cd_update_id.",".$cbo_receive_basis.",".$txt_wo_pi_id.",".$cbo_company_id.",".$cbo_supplier.",".$txt_product_id.",".$cbo_item_category_id.",1,".$txt_receive_date.",".$cbo_store_name.",".$cbo_uom.",".$txt_receive_qty.",".number_format($txt_rate,10,".","").",".$ile.",'".$ile_cost."',".number_format($txt_amount,8,".","").",".$cbo_uom.",".$txt_receive_qty.",".number_format($cons_rate,10,".","").",".$con_ile.",".$con_ile_cost.",".number_format($con_amount,8,".","").",".$txt_receive_qty.",".number_format($con_amount,8,".","").",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_lot.",".$txt_expire_date.",".$txt_manufac_date.",".$txt_referance.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',".number_format($cons_rate,10,".","").",".number_format($con_amount,8,".","").")";
		//echo "10 ** INSERT INTO inv_transaction (".$field_array1.") VALUES ".$data_array1.""; oci_rollback($con);disconnect($con);die;

		//product master table data UPDATE START----------------------------------------------------------//

		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value from product_details_master where id=$txt_product_id and status_active=1 and is_deleted=0");
		$presentStock=$presentStockValue=$presentAvgRate=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$presentStockValue		=$result[csf("stock_value")];
			$presentAvgRate			=$result[csf("avg_rate_per_unit")];
			$product_name_details 	=$result[csf("product_name_details")];
		}
		
		$stock_value 	= $domestic_rate*$txt_receive_qty;
  		$currentStock 	= $presentStock+$txt_receive_qty;
  		$StockValue=0;
		$avgRate=$presentAvgRate;
  		if ($currentStock != 0)
		{
  			$StockValue	 	= $presentStockValue+$stock_value;
			$avgRate		= abs($StockValue/$currentStock);
  		}
		
		$field_array2="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*is_compliance*updated_by*update_date";
 		$data_array2="".number_format($avgRate,10,".","")."*".$txt_receive_qty."*".$currentStock."*".number_format($StockValue,8,".","")."*".$cbo_zero_discharge."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
		//echo "10 ** $field_array2   VALUES  $data_array2"; oci_rollback($con);disconnect($con);die;
 		

		//------------------ product_details_master END---------------------------------------------------//
		
		
		//--------------Store Wise Stock------------------
		$store_lot_cond="";
		if($variable_lot==1)
		{
			$store_lot_cond=" and lot ='".str_replace("'","",$txt_lot)."'";
		}
		$sql_store = sql_select("select rate as avg_rate_per_unit,cons_qty as current_stock,amount as stock_value from inv_store_wise_qty_dtls where prod_id=$txt_product_id and category_id=$cbo_item_category_id and store_id=$cbo_store_name and company_id=$cbo_company_id $store_lot_cond");
		$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
		foreach($sql_store as $result)
		{
			$store_presentStock	=$result[csf("current_stock")];
			$store_presentStockValue =$result[csf("stock_value")];
			$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
		}

		//$txt_product_id,$cbo_item_category_id,$cbo_store_name,$txt_receive_qty,$cons_rate,$con_amount
		$cbo_company_id   = str_replace("'","",$cbo_company_id);
		$cbo_store_name   = str_replace("'","",$cbo_store_name);
		$item_category_id = str_replace("'","",$cbo_item_category_id);
		$txt_product_id   = str_replace("'","",$txt_product_id);
		$variable_lot = str_replace("'","",$variable_lot);
		if($variable_lot==1) $dyes_lot   = str_replace("'","",$txt_lot); else $dyes_lot   = "";
		$stock_arr=fnc_store_wise_qty_operation($cbo_company_id,$cbo_store_name,$item_category_id,$txt_product_id,1,$dyes_lot);
		//print_r($stock_arr);
		$store_stock_value 	= $domestic_rate*$txt_receive_qty;
		$store_currentStock = $store_presentStock+$txt_receive_qty;
		$store_StockValue	= $store_presentStockValue+$store_stock_value;
		$store_avgRate=0;
		if($store_StockValue!=0 && $store_currentStock!=0) $store_avgRate = abs($store_StockValue/$store_currentStock);

 		$field_array_store_up="rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date";
		$field_array_store_insert="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";

		$store_update_id=$stock_arr[$cbo_company_id][$txt_product_id][$cbo_store_name][$item_category_id][$dyes_lot];
		$data_array_store_up="".number_format($store_avgRate,10,".","")."*".$txt_receive_qty."*".$store_currentStock."*".number_format($store_StockValue,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_receive_date."";
		//$sdtlsid = return_next_id("id", "inv_store_wise_qty_dtls", 1);
		$sdtlsid = return_next_id_by_sequence("inv_store_wise_qty_dtls_pk_seq", "inv_store_wise_qty_dtls", $con);
		$data_array_store_insert= "(".$sdtlsid.",".$cbo_company_id.",".$cbo_store_name.",".$cbo_item_category_id.",".$txt_product_id.",".$txt_receive_qty.",".number_format($cons_rate,10,".","").",".number_format($con_amount,8,".","").",".$txt_receive_qty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$dyes_lot."',".$txt_receive_date.",".$txt_receive_date.")";

		//print_r($data_array_store_up);
		//echo "10**".$store_update_id;
		$rID=$dtlsrID=$prodUpdate=$storeUpdate=$prodLotUpdate=true;
		if(str_replace("'","",$update_id)=="")
		{
		   $rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
		}
		else
		{
		  $rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,0);
		}
		//echo "10**insert into inv_transaction ($field_array1) values ".$data_array1;die;
		$dtlsrID = sql_insert("inv_transaction",$field_array1,$data_array1,0);
		
		if($product_table_update)
		{
			$prodUpdate = sql_update("product_details_master",$field_array2,$data_array2,"id",$txt_product_id,0);
		}
		
		if($store_update_id!='')
		{
			$storeUpdate = sql_update("inv_store_wise_qty_dtls",$field_array_store_up,$data_array_store_up,"id",$store_update_id,0);
		}
		else
		{
			//echo "10**INSERT INTO inv_store_wise_qty_dtls (".$field_array_store_insert.") VALUES ".$data_array_store_insert.""; die;
			$storeUpdate = sql_insert("inv_store_wise_qty_dtls",$field_array_store_insert,$data_array_store_insert,0);
		}
		
		if($prod_scrtipt!="")
		{
			$prodLotUpdate=execute_query($prod_scrtipt);
		}
		
		//echo $sdtlsrID;
      	// echo "10**INSERT INTO inv_store_wise_qty_dtls (".$field_array_store_insert.") VALUES ".$data_array_store_insert.""; die;
		//echo "10**".$rID.'='.$dtlsrID.'='.$prodUpdate.'='.$sdtlsrID;die;

		//echo "10**".$rID.'='.$dtlsrID.'='.$prodUpdate.'='.$storeUpdate."=".$prodLotUpdate;oci_rollback($con);die;
       	//echo "10**INSERT INTO inv_store_wise_qty_dtls (".$field_array_store_insert.") VALUES ".$data_array_store_insert.""; die;
		
		if($db_type==0)
		{
			if($rID && $dtlsrID && $prodUpdate && $storeUpdate && $prodLotUpdate)
			{
				mysql_query("COMMIT");
				echo "0**".$cd_update_id."**".$cd_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**"."&nbsp;"."**0";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			//echo $flag;die;
			if($rID && $dtlsrID && $prodUpdate && $storeUpdate && $prodLotUpdate)
			{
				oci_commit($con);
				echo "0**".$cd_update_id."**".$cd_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}

		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;

	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();

		if($db_type==0)	{ mysql_query("BEGIN"); }


		//check update id
		if( str_replace("'","",$update_id) == "" )
		{
			echo "15";disconnect($con);exit();
		}

		if($variable_set_invent==1)
		{
			$challan_no=str_replace("'","",$txt_challan_no);
			if($challan_no!="")
			{
				$variable_set_invent=return_field_value("a.id as id"," inv_gate_in_mst a,  inv_gate_in_dtl b","a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=cbo_item_category_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
				if(empty($variable_set_invent))
				{
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";disconnect($con);die;
				}

			}
		}
		$is_posted=sql_select("select is_posted_account from inv_receive_master where id=$update_id and status_active=1");
		if($is_posted[0][csf("is_posted_account")] == 1)
		{
			echo "20**Already Posted in Accounts, so update and delete is not allowed";
			disconnect($con);die;
		}
		
		$prev_store_id=return_field_value("store_id","inv_transaction","status_active=1 and transaction_type=1 and mst_id=$update_id","store_id");
		if($prev_store_id<>str_replace("'","",$cbo_store_name))
		{
			echo "30** Multiple Store Not Allowed In Same MRR";disconnect($con);die;
		}
		
		$rcv_basis=str_replace("'","",$cbo_receive_basis);
		
		
		if($variable_over_rcv_payment==1 && $rcv_basis !=4 && $rcv_basis !=6)
		{
			//txt_receive_qty current_prod_id
			$wo_pi_req_id=str_replace("'","",$txt_wo_pi_id);
			$cr_prod_id=str_replace("'","",$txt_product_id);
			
			$prod_sql="select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE, LOT from product_details_master where status_active=1 and is_deleted=0 and id=$cr_prod_id";
			//echo "10**=".$prod_sql;die;
			$prod_sql_result=sql_select($prod_sql);
			$prod_company_id=$prod_sql_result[0]["COMPANY_ID"];
			$prod_item_category_id=$prod_sql_result[0]["ITEM_CATEGORY_ID"];
			$prod_item_group_id=$prod_sql_result[0]["ITEM_GROUP_ID"];
			$prod_sub_group_name=$prod_sql_result[0]["SUB_GROUP_NAME"];
			$prod_item_description=$prod_sql_result[0]["ITEM_DESCRIPTION"];
			$prod_item_size=$prod_sql_result[0]["ITEM_SIZE"];
			$prod_model=$prod_sql_result[0]["MODEL"];
			$prod_item_number=$prod_sql_result[0]["ITEM_NUMBER"];
			$prod_item_code=$prod_sql_result[0]["ITEM_CODE"];
			$prod_lot=$prod_sql_result[0]["LOT"];
			
			if($prod_sub_group_name=='') $woPi_prod_conds.=" and b.sub_group_name is null"; else $woPi_prod_conds.=" and b.sub_group_name='$prod_sub_group_name'";
			if($prod_item_description=='') $woPi_prod_conds .=" and b.item_description is null"; else $woPi_prod_conds.=" and b.item_description='$prod_item_description'";
			if($prod_item_size=='') $woPi_prod_conds .=" and b.item_size is null"; else $woPi_prod_conds.=" and b.item_size='$prod_item_size'";
			if($prod_model=='') $woPi_prod_conds .=" and b.model is null"; else $woPi_prod_conds.=" andb. model='$prod_model'";
			if($prod_item_number=='') $woPi_prod_conds .=" and b.item_number is null"; else $woPi_prod_conds.=" and b.item_number='$prod_item_number'";
			if($prod_item_code=='') $woPi_prod_conds .=" and b.item_code is null"; else $woPi_prod_conds.=" and b.item_code='$prod_item_code'";
			
            $prev_return_sql=sql_select("select sum(b.cons_quantity) as qnty from inv_issue_master a, inv_transaction b, inv_receive_master c where a.id = b.mst_id and a.received_id = c.id and a.entry_form = 28 and b.transaction_type=3 and c.booking_id = $wo_pi_req_id  and b.prod_id=$cr_prod_id and a.status_active=1 and b.status_active=1 group by c.booking_id");
            $totalRtnQnty=$prev_return_sql[0][csf('qnty')]/$conversion_factor;
			$prev_rcv_sql=sql_select("select sum(c.cons_quantity) as qnty from inv_receive_master a, inv_transaction c, product_details_master b 
			where a.id = c.mst_id and c.prod_id=b.id and a.entry_form = 4 and c.transaction_type=1 and a.receive_basis = $rcv_basis and a.booking_id = $wo_pi_req_id and c.item_category=$cbo_item_category_id and b.item_category_id=$cbo_item_category_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id<>$update_id $woPi_prod_conds");
			$prev_rcv_qnty=$prev_rcv_sql[0][csf("qnty")]-$totalRtnQnty;
			$tot_qnty=$prev_rcv_qnty+$txt_receive_qty;
			
			if($rcv_basis==1)
			{
				$wo_pi_req_sql=sql_select(" select sum(a.quantity) as quantity from com_pi_item_details a, product_details_master b where a.item_prod_id=b.id and a.status_active=1  and b.status_active=1 and a.pi_id=$wo_pi_req_id $woPi_prod_conds");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
				
			}
			else if($rcv_basis==2)
			{
				$wo_pi_req_sql=sql_select(" select sum(a.supplier_order_quantity) as quantity from wo_non_order_info_dtls a, product_details_master b  where a.item_id=b.id and a.status_active=1 and b.status_active=1 and a.mst_id=$wo_pi_req_id $woPi_prod_conds");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
			}
			else
			{
				$wo_pi_req_sql=sql_select(" select sum(a.quantity) as quantity from inv_purchase_requisition_dtls a, product_details_master b where a.product_id=b.id and b.status_active=1 and b.status_active=1 and a.mst_id=$wo_pi_req_id $woPi_prod_conds");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
			}
			
			
			$allow_qnty=($wo_pi_req_qnty+(($wo_pi_req_qnty/100)*$variable_over_rcv_percent));
			if($tot_qnty>$allow_qnty)
			{
				echo "30** MRR Quantity Not Allow More Then PI/Wo/Req Allowed Quantity.";disconnect($con);die;
			}
		}

		$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,a.store_amount,b.avg_rate_per_unit,b.current_stock,b.stock_value from inv_transaction a, product_details_master b where a.id=$update_dtls_id and a.prod_id=b.id and b.status_active=1 and b.is_deleted=0");

		$before_prod_id="";
		$before_receive_qnty=0;
		$before_rate=0;
		$beforeAmount=0;
		$beforeStock=0;
		$beforeStockValue=0;
		$beforeAvgRate=0;
		foreach( $sql as $row)
		{
			$before_prod_id 		=$row[csf("prod_id")];
			$before_receive_qnty 	=$row[csf("cons_quantity")]; //stock qnty
			$before_rate 			=$row[csf("cons_rate")];
			$beforeAmount			=$row[csf("cons_amount")]; //stock value
			$beforeStoreAmount		= $row[csf("store_amount")];
			$before_brand 			=$row[csf("brand")];
			$beforeStock			=$row[csf("current_stock")];
			$beforeStockValue		=$row[csf("stock_value")];
			$beforeAvgRate			=$row[csf("avg_rate_per_unit")];
		}
		//stock value minus here---------------------------//
		$adj_beforeStock			=$beforeStock-$before_receive_qnty;
		$adj_beforeStockValue		=$beforeStockValue-$beforeAmount;
		if ($adj_beforeStock != 0)
			$adj_beforeAvgRate	=abs($adj_beforeStockValue/$adj_beforeStock);
		else $adj_beforeAvgRate=0;



		$issue_id_check=return_field_value("id as id","inv_mrr_wise_issue_details","recv_trans_id=$update_dtls_id and is_deleted=0 and status_active=1","id");
		if($issue_id_check!="")
		{
			echo "30**Update Not Allow, This Item Found In Issue Entry.";die;
		}
		//table lock here
		//check_table_status( $_SESSION['menu_id'],0);
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}


		//current product stock-------------------------//
		$sql_present_prod = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value from product_details_master where id=$txt_product_id and status_active=1 and is_deleted=0");
		$presentStock=$presentStockValue=$presentAvgRate=0;
		$product_name_details="";
		foreach($sql_present_prod as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$presentStockValue		=$result[csf("stock_value")];
			$presentAvgRate			=$result[csf("avg_rate_per_unit")];
			$product_name_details 	=$result[csf("product_name_details")];
		}

		//----------------Check Product ID END---------------------//
		//--------------Store Wise Stock------------------
		$store_lot_cond="";
		if($variable_lot==1)
		{
			//$update_dtls_id
			$prev_lot=return_field_value("batch_lot","inv_transaction","id=$update_dtls_id","batch_lot");
			if($prev_lot!=str_replace("'","",$txt_lot))
			{
				echo "30**Lot Change Not Allow In Update Event.";die;
			}
			$store_lot_cond=" and lot ='".str_replace("'","",$txt_lot)."'";
		}
		
		//echo "10**select rate as avg_rate_per_unit,cons_qty as current_stock,amount as stock_value,last_purchased_qnty from inv_store_wise_qty_dtls where prod_id=$txt_product_id and category_id=$cbo_item_category_id and store_id=$cbo_store_name and company_id=$cbo_company_id $store_lot_cond";die;
		$sql_store = sql_select("select rate as avg_rate_per_unit,cons_qty as current_stock,amount as stock_value,last_purchased_qnty from inv_store_wise_qty_dtls where prod_id=$txt_product_id and category_id=$cbo_item_category_id and store_id=$cbo_store_name and company_id=$cbo_company_id $store_lot_cond");
		$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;

		foreach($sql_store as $result)
		{
			$store_presentStock	=$result[csf("current_stock")];
			$store_presentStockValue =$result[csf("stock_value")];
			$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
		}
		$adj_beforeStock_store			=$store_presentStock-$before_receive_qnty;
	    $adj_beforeStockValue_store		=$store_presentStockValue-$beforeStoreAmount;
	    $adj_beforeAvgRate_Store=abs($adj_beforeStockValue_store/$adj_beforeStock_store);
		 
		 
		$flag=1;
		$field_array_update="receive_purpose*loan_party*receive_date*challan_no*challan_date*location_id*supplier_id*lc_no*source*gate_entry_no*supplier_referance*pay_mode*boe_mushak_challan_no*boe_mushak_challan_date*gate_entry_date*updated_by*update_date";
		$data_array_update=$cbo_receive_purpose."*".$cbo_loan_party."*".$txt_receive_date."*".$txt_challan_no."*".$txt_challan_date."*".$cbo_location."*".$cbo_supplier."*".$hidden_lc_id."*".$cbo_source."*".$txt_gate_entry."*".$txt_sup_ref."*".$cbo_pay_mode."*".$txt_boe_mushak_challan_no."*".$txt_boe_mushak_challan_date."*".$txt_gate_entry_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		//yarn master table UPDATE here END---------------------------------------//
		// yarn details table UPDATE here START-----------------------------------//
		$rate = str_replace("'","",$txt_rate);
		$txt_ile = str_replace("'","",$txt_ile);
		$txt_receive_qty = str_replace("'","",$txt_receive_qty);
		$ile = ($txt_ile/$rate)*100; // ile cost to ile
		$ile_cost = str_replace("'","",$txt_ile); //ile cost = (ile/100)*rate
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
 		$cons_rate = $domestic_rate;//number_format($rate*$exchange_rate,$dec_place[3],".","");

		$con_amount = $cons_rate*$txt_receive_qty;
		$con_ile = $ile;
		$con_ile_cost = ($ile/100)*($rate*$exchange_rate);

		//echo "20**".$con_ile_cost; mysql_query("ROLLBACK"); die;
		$field_array = "receive_basis*pi_wo_batch_no*company_id*supplier_id*prod_id*item_category*transaction_date*store_id*order_uom*order_qnty*order_rate*order_ile*order_ile_cost*order_amount*cons_uom*cons_quantity*cons_rate*cons_ile*cons_ile_cost*cons_amount*balance_qnty*balance_amount*floor_id*room*rack*self*bin_box*expire_date*manufacture_date*remarks*updated_by*update_date*store_rate*store_amount";
 		$data_array = "".$cbo_receive_basis."*".$txt_wo_pi_id."*".$cbo_company_id."*".$cbo_supplier."*".$txt_product_id."*".$cbo_item_category_id."*".$txt_receive_date."*".$cbo_store_name."*".$cbo_uom."*".$txt_receive_qty."*".number_format($txt_rate,10,'.','')."*".$ile."*'".$ile_cost."'*".number_format($txt_amount,8,'.','')."*".$cbo_uom."*".$txt_receive_qty."*".number_format($cons_rate,10,'.','')."*".$con_ile."*".$con_ile_cost."*".number_format($con_amount,8,'.','')."*".$txt_receive_qty."*".number_format($con_amount,8,'.','')."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_expire_date."*".$txt_manufac_date."*".$txt_referance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".number_format($cons_rate,10,".","")."*".number_format($con_amount,8,".","")."";
		//echo $field_array."<br>".$data_array;die;
		//product master table data UPDATE START----------------------------------------------------------//
		//if(str_replace("'","",$cbo_zero_discharge)>0)
		$field_array1="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*is_compliance*updated_by*update_date";
		if($before_prod_id==str_replace("'","",$txt_product_id))
		{

			$currentStock	=$adj_beforeStock+$txt_receive_qty;

			if($currentStock<0)
			{
				echo "30**Stock cannot be less than zero.";die;
			}
			$StockValue=0;
			$avgRate==$presentAvgRate;
			if ($currentStock != 0){
				$StockValue		=$adj_beforeStockValue+($domestic_rate*$txt_receive_qty);
		    	$avgRate		=abs($StockValue/$currentStock);
			}
 			$data_array1= "".number_format($avgRate,10,".","")."*".$txt_receive_qty."*".$currentStock."*".number_format($StockValue,8,".","")."*".$cbo_zero_discharge."*'".$user_id."'*'".$pc_date_time."'";
		}
		else
		{
			//before
			$updateID_array=$update_data=array();
			$updateID_array[]=$before_prod_id;
			if($adj_beforeStock<0 )
			{
				echo "30**Stock cannot be less than zero.";disconnect($con);die;
			}
			if ($adj_beforeStock != 0){
				//$adj_beforeAvgRate=$adj_beforeAvgRate;
				$adj_beforeStockValue=$adj_beforeStockValue;
			} else {
				//$adj_beforeAvgRate=0;
				$adj_beforeStockValue=0;
			}
			$update_data[$before_prod_id]=explode("*",("".number_format($adj_beforeAvgRate,10,".","")."*0*".$adj_beforeStock."*".number_format($adj_beforeStockValue,8,".","")."*".$cbo_zero_discharge."*'".$user_id."'*'".$pc_date_time."'"));
			//current
 			$presentStock 			= $presentStock+$txt_receive_qty;
 			if ($presentStock != 0){
 				$presentStockValue	 	= $presentStockValue+($domestic_rate*$txt_receive_qty);
				$presentAvgRate			= abs($presentStockValue/$presentStock);
 			} else {
 				$presentStockValue=0;
 				//$presentAvgRate=0;
 			}
			
			$updateID_array[]		=str_replace("'","",$txt_product_id);
			$update_data[str_replace("'","",$txt_product_id)]=explode("*",("".number_format($presentAvgRate,10,".","")."*0*".$presentStock."*".number_format($presentStockValue,8,".","")."*".$cbo_zero_discharge."*'".$user_id."'*'".$pc_date_time."'"));
			//echo bulk_update_sql_statement("product_details_master","id",$field_array,$update_data,$updateID_array);die;
		}

		// Store Wise Stock
		$cbo_company_id = str_replace("'","",$cbo_company_id);
		$cbo_store_name = str_replace("'","",$cbo_store_name);
		$item_category_id = str_replace("'","",$cbo_item_category_id);
		$txt_product_id = str_replace("'","",$txt_product_id);
		$variable_lot = str_replace("'","",$variable_lot);
		if($variable_lot==1) $dyes_lot= str_replace("'","",$txt_lot); else $dyes_lot= "";
		$stock_arr=fnc_store_wise_qty_operation($cbo_company_id,$cbo_store_name,$item_category_id,$txt_product_id,1,$dyes_lot);
		//print_r($stock_arr);

		if($before_prod_id==str_replace("'","",$txt_product_id))
		{

			$currentStock_store		=$adj_beforeStock_store+$txt_receive_qty;
			$StockValue_store		=$adj_beforeStockValue_store+($domestic_rate*$txt_receive_qty);
			$store_avgRate=0;
			if($StockValue_store!=0 && $currentStock_store!=0) $store_avgRate=abs($StockValue_store/$currentStock_store);
			$data_array_store_up= "".number_format($store_avgRate,10,".","")."*".$txt_receive_qty."*".$currentStock_store."*".number_format($StockValue_store,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_receive_date."";
			//print_r($data_array_store_up);

		}
		else
		{
			$store_stock_value 		= $domestic_rate*$txt_receive_qty;
			$store_currentStock 	= $store_presentStock+$txt_receive_qty;
			$store_StockValue = $store_presentStockValue+$store_stock_value;
			$store_avgRate=0;
			if($store_StockValue!=0 && $store_currentStock!=0) $store_avgRate = abs($store_StockValue/$store_currentStock);		
				
			$data_array_store_up	="".number_format($store_avgRate,10,".","")."*".$txt_receive_qty."*".$store_currentStock."*".number_format($store_StockValue,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$txt_receive_date."";
		}

 		$field_array_store_up="rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date";
		$field_array_store_insert="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";

		$store_update_id=$stock_arr[$cbo_company_id][$txt_product_id][$cbo_store_name][$item_category_id][$dyes_lot];

		//$sdtlsid = return_next_id("id", "inv_store_wise_qty_dtls", 1);
		$sdtlsid = return_next_id_by_sequence("INV_STORE_WISE_QTY_DTLS_PK_SEQ", "inv_store_wise_qty_dtls", $con);
		$data_array_store_insert= "(".$sdtlsid.",".$cbo_company_id.",".$cbo_store_name.",".$cbo_item_category_id.",".$txt_product_id.",".$txt_receive_qty.",".number_format($cons_rate,10,".","").",".number_format($con_amount,8,".","").",".$txt_receive_qty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$dyes_lot."',".$txt_receive_date.",".$txt_receive_date.")";

		//print_r($data_array_store_up);
		//echo "10**".$store_update_id;
		
		$rID=$dtlsrID=$prodUpdate=$storeId=true;
		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		$dtlsrID = sql_update("inv_transaction",$field_array,$data_array,"id",$update_dtls_id,1);
		if($before_prod_id==str_replace("'","",$txt_product_id))
		{
			$prodUpdate = sql_update("product_details_master",$field_array1,$data_array1,"id",$txt_product_id,1);
		}
		else
		{
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array1,$update_data,$updateID_array));
		}

		if($store_update_id!='')
		{
			//echo "10** $field_array_store_up == $data_array_store_up";oci_rollback($con);disconnect($con);die;
		 	$storeId = sql_update("inv_store_wise_qty_dtls",$field_array_store_up,$data_array_store_up,"id",$store_update_id,1);
		}
		else
		{
			//echo "10** rr";oci_rollback($con);disconnect($con);die;
			$storeId = sql_insert("inv_store_wise_qty_dtls",$field_array_store_insert,$data_array_store_insert,1);
		}
		
		//echo "10**".$rID.'='.$dtlsrID.'='.$prodUpdate.'='.$storeId.'='.$store_update_id;oci_rollback($con);disconnect($con);die;


		if($db_type==0)
		{
			if($rID && $dtlsrID && $prodUpdate && $storeId)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**"."&nbsp;"."**0";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID && $prodUpdate && $storeId)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**0";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		if( str_replace("'","",$update_id) == "" )
		{
			echo "15";disconnect($con);exit();
		}
		//$mst_id = str_replace("'","",$hidden_mrr_id);
		$update_id = str_replace("'","",$update_id);
		$product_id = str_replace("'","",$txt_product_id);
		if( str_replace("'","",$update_id) == "" )
		{
			echo "30**Delete not allowed. Problem occurred";disconnect($con); die;
		}

		$is_posted=sql_select("select is_posted_account from inv_receive_master where id=$update_id and status_active=1");

		if($is_posted[0][csf("is_posted_account")] == 1)
		{
			echo "20**Already Posted in Accounts, so update and delete is not allowed";
			disconnect($con);die;
		}

		//echo "10**select id from inv_transaction where transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_dtls_id"; die;
		$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_dtls_id ","id");
		if($chk_next_transaction !="")
		{ 
			echo "30**Delete not allowed.This item is used in another transaction";disconnect($con); die;
		}
		else
		{
			$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,a.store_amount,b.avg_rate_per_unit,b.current_stock,b.stock_value from inv_transaction a, product_details_master b  where a.status_active=1 and a.id=$update_dtls_id and a.prod_id=b.id and b.status_active=1 and b.is_deleted=0");
			$cbo_company_id = str_replace("'","",$cbo_company_id);
			$cbo_store_name = str_replace("'","",$cbo_store_name);
			$item_category_id = str_replace("'","",$cbo_item_category_id);
			$txt_product_id = str_replace("'","",$txt_product_id);
			$variable_lot = str_replace("'","",$variable_lot);
			if($variable_lot==1) $dyes_lot= str_replace("'","",$txt_lot); else $dyes_lot= "";
			$stock_arr=fnc_store_wise_qty_operation($cbo_company_id,$cbo_store_name,$item_category_id,$txt_product_id,1,$dyes_lot);
			$before_prod_id=$before_receive_qnty=$before_rate=$beforeAmount=$before_brand="";
			$beforeStock=$beforeStockValue=$beforeAvgRate=0;
			foreach( $sql as $row)
			{
				$before_prod_id 		= $row[csf("prod_id")]; 
				$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
				$before_rate 			= $row[csf("cons_rate")]; 
				$beforeAmount			= $row[csf("cons_amount")]; //stock value
				$beforeStoreAmount		= $row[csf("store_amount")];
				$beforeStock			=$row[csf("current_stock")];
				$beforeStockValue		=$row[csf("stock_value")];
				$beforeAvgRate			=$row[csf("avg_rate_per_unit")];	
			}
			//stock value minus here---------------------------//
			$adj_beforeStock			=$beforeStock-$before_receive_qnty;
			
			$adj_beforeAvgRate=$beforeAvgRate;
			if ($adj_beforeStock != 0){
				$adj_beforeStockValue	=$beforeStockValue-$beforeAmount;
				$adj_beforeAvgRate		=abs($adj_beforeStockValue/$adj_beforeStock);	
			}
			else
			{
				$adj_beforeStockValue=$adj_beforeAvgRate=0;
			}
		
			$field_array_product="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
			$data_array_product = "".number_format($adj_beforeAvgRate,10,".","")."*".$adj_beforeStock."*".number_format($adj_beforeStockValue,8,".","")."*'".$user_id."'*'".$pc_date_time."'";
			
			$field_array_trans="updated_by*update_date*status_active*is_deleted";
			$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";
			
			

			
			$store_lot_cond="";
			if($variable_lot==1)
			{
				$store_lot_cond=" and lot ='".str_replace("'","",$txt_lot)."'";
			}
			$sql_store = sql_select("select rate as avg_rate_per_unit,cons_qty as current_stock,amount as stock_value,last_purchased_qnty from inv_store_wise_qty_dtls where prod_id=$txt_product_id and category_id=$cbo_item_category_id and store_id=$cbo_store_name and company_id=$cbo_company_id $store_lot_cond");
			$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;

			foreach($sql_store as $result)
			{
				$store_presentStock	=$result[csf("current_stock")];
				$store_presentStockValue =$result[csf("stock_value")];
				$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
			}
			$adj_beforeStock_store			=$store_presentStock-$before_receive_qnty;
		    $adj_beforeStockValue_store		=$store_presentStockValue-$beforeStoreAmount;
		    $adj_beforeAvgRate_Store		=abs($adj_beforeStockValue_store/$adj_beforeStock_store);

		    $store_update_id=$stock_arr[$cbo_company_id][$txt_product_id][$cbo_store_name][$item_category_id][$dyes_lot];
			$field_array_store_up="rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date";
			if($before_prod_id==str_replace("'","",$txt_product_id))
			{

				$currentStock_store		=$adj_beforeStock_store-$txt_receive_qty;	
				$StockValue_store = $adj_beforeStockValue_store-($domestic_rate*$txt_receive_qty);
				$avgRate_store    =0;
				if($StockValue_store>0 && $currentStock_store>0) $avgRate_store = abs($StockValue_store/$currentStock_store);
				$data_array_store_up= "".number_format($avgRate_store,10,".","")."*".$txt_receive_qty."*".$currentStock_store."*".number_format($StockValue_store,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
				//print_r($data_array_store_up);
			}
			else
			{
				$store_stock_value 		= $domestic_rate*$txt_receive_qty;
				$store_currentStock 	= $store_presentStock-$txt_receive_qty;
				$store_StockValue = $store_presentStockValue-$store_stock_value;
				$store_avgRate    =0;
				if($store_StockValue>0 && $store_currentStock>0) $store_avgRate    = abs($store_StockValue/$store_currentStock);
				$data_array_store_up	="".number_format($store_avgRate,10,".","")."*".$txt_receive_qty."*".$store_currentStock."*".number_format($store_StockValue,8,".","")."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			}
			$rID=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_dtls_id,1);
			$rID2=sql_update("product_details_master",$field_array_product,$data_array_product,"id",$product_id,1);
			$storeId =true;
			if($store_update_id) $storeId = sql_update("inv_store_wise_qty_dtls",$field_array_store_up,$data_array_store_up,"id",$store_update_id,1);
		}
		
		//echo "20**".$rID."**".$rID2."**".$storeId; disconnect($con);oci_rollback($con);die;
		if($db_type==0)
		{
			if($rID && $rID2 && $storeId)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			
			if($rID && $rID2 && $storeId)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
			}
		}
		disconnect($con);
		die;
	}
}


if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $str_ref;die;
?>

<script>
	function js_set_value(mrr)
	{
 		$("#hidden_recv_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
    function createContentSearch(id) {
        var data = ["Exact","Starts with","Ends with","Contents"];
        var appender = '';
        appender += '<select name="cbo_string_search_type" id="cbo_string_search_type" class="combo_boxes " style="width:130px" onchange="">';
        appender += '<option data-attr="" value="0">-- Searching Type --</option>';
        $.each(data, function (index, val){
            if(index == 3){
                appender += '<option data-attr="" value="'+(index+1)+'" selected>'+val+'</option>';
            }else{
                appender += '<option data-attr="" value="'+(index+1)+'">'+val+'</option>';
            }
        });
        appender += '</select>';
        $('#'+id).find('thead').prepend('<tr><th colspan="9">'+appender+'</th></tr>');
    }
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th width="150">Supplier</th>
                    <th width="150">Search By</th>
                    <th width="250" align="center" id="search_by_td_up">Please Enter MRR No</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?
 							// echo create_drop_down( "cbo_supplier", 150, "select id,supplier_name from lib_supplier where FIND_IN_SET($company,tag_company) and FIND_IN_SET(3,party_type) order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
							echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b,lib_supplier_party_type c where a.id=c.supplier_id and a.id=b.supplier_id and b.tag_company='$company' and c.party_type='3' and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
                        ?>
                    </td>
                    <td>
                        <?
                            $search_by = array(1=>'MRR No',2=>'Challan No', 3=>'PI No', 4=>'WO No', 5=>'Requ No');
							$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+'<? echo $str_ref; ?>'+'_'+document.getElementById('cbo_year_selection').value, 'create_mrr_search_list_view', 'search_div', 'chemical_dyes_receive_controller', 'setFilterGrid(\'list_view\',-1);createContentSearch(\'rpt_tablelist_view\');')" style="width:100px;" />
                    </td>
            </tr>
        	<tr>
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-->
                     <input type="hidden" id="hidden_recv_number" value="hidden_recv_number" />
                    <!---END-->
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
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
//    print_r($ex_data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = $ex_data[2];
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
	$str_ref = $ex_data[6];
    $year = $ex_data[7];
	$variable_set_basis=return_field_value("independent_controll","variable_settings_inventory","company_name=$company and menu_page_id=$str_ref and variable_list=20","independent_controll");


	$sql_cond="";
	if($variable_set_basis==2) $sql_cond.=" and a.receive_basis<>4";

	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.recv_number LIKE '%$txt_search_common%'";

		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
 		}
        else if(trim($txt_search_by)==3) // for pi no
        {
            $sql_cond .= " and a.booking_no LIKE '%$txt_search_common%' and a.receive_basis = 1";
        }
        else if(trim($txt_search_by)==4) // for wo no
        {
            $sql_cond .= " and a.booking_no LIKE '%$txt_search_common%' and a.receive_basis = 2";
        }
        else if(trim($txt_search_by)==5) // for requ no
        {
            $sql_cond .= " and a.booking_no LIKE '%$txt_search_common%' and a.receive_basis = 7";
        }

 	}

	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	if(trim($supplier)!=0) $sql_cond .= " and a.supplier_id='$supplier'";

	if($db_type==0)
	{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		$sql = "select a.recv_number, a.supplier_id, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, sum(b.cons_quantity) as receive_qnty
		from inv_transaction b inner join inv_receive_master a on a.id=b.mst_id left join com_btb_lc_master_details c on a.lc_no=c.id
		where a.entry_form=4 and a.status_active=1 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and b.item_category in($item_cate_credential_cond) $sql_cond
		group by a.recv_number, a.supplier_id, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order order by a.id desc";
	}

	if($db_type==1 || $db_type==2)
	{
		if( $fromDate != "" && $toDate != ""){
            $sql_cond .= " and a.receive_date  between '".change_date_format($fromDate, "yyyy-mm-dd", "-",1)."' and '".change_date_format($toDate, "yyyy-mm-dd", "-",1)."'";
        }else{
            $sql_cond .= " and extract(YEAR from a.receive_date) = $year";
        }
		$sql = "select a.recv_number, a.supplier_id, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, sum(b.cons_quantity) as receive_qnty
		from inv_transaction b inner join inv_receive_master a on a.id=b.mst_id left join com_btb_lc_master_details c on a.lc_no=c.id
		where a.entry_form=4 and a.status_active=1  and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and b.item_category in($item_cate_credential_cond) $sql_cond
		group by a.recv_number, a.supplier_id, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order
		order by a.recv_number desc";
	}

	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$arr=array(1=>$supplier_arr,5=>$receive_basis_arr);

	echo create_list_view("list_view", "MRR No, Supplier Name, Challan No, PI/WO/Req No, LC No, Receive Date, Receive Basis, Receive Qnty","120,120,120,120,120,120,100,100","990","260",0, $sql , "js_set_value", "recv_number,receive_basis,booking_id,booking_no,booking_without_order", "", 1, "0,supplier_id,0,0,0,0,receive_basis,0", $arr, "recv_number,supplier_id,challan_no,booking_no,lc_number,receive_date,receive_basis,receive_qnty", "",'','0,0,0,0,0,0,0,2') ;
	exit();
}

if($action=="populate_data_from_data")
{
	$sql = "select id, recv_number, company_id, receive_basis, booking_id, booking_no,receive_purpose,loan_party,gate_entry_no, location_id, receive_date, challan_no, challan_date, store_id, lc_no, supplier_id, exchange_rate, currency_id, lc_no,source,supplier_referance,pay_mode,is_audited,is_posted_account,boe_mushak_challan_no,boe_mushak_challan_date,gate_entry_date from inv_receive_master where recv_number='$data' and entry_form=4";
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "fn_independent(".$row[csf("receive_basis")].");\n";
		echo "$('#txt_mrr_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_receive_basis').val(".$row[csf("receive_basis")].");\n";
		echo "$('#txt_wo_pi').val('".$row[csf("booking_no")]."');\n";
		echo "$('#txt_wo_pi_id').val(".$row[csf("booking_id")].");\n";
		echo "$('#cbo_receive_purpose').val(".$row[csf("receive_purpose")].");\n";
		echo "$('#cbo_loan_party').val(".$row[csf("loan_party")].");\n";
		echo "$('#txt_gate_entry').val('".$row[csf("gate_entry_no")]."');\n";
		echo "$('#txt_sup_ref').val('".$row[csf("supplier_referance")]."');\n";
		echo "$('#cbo_pay_mode').val(".$row[csf("pay_mode")].");\n";
		echo "$('#txt_boe_mushak_challan_no').val('".$row[csf("boe_mushak_challan_no")]."');\n";
		echo "$('#txt_boe_mushak_challan_date').val('".change_date_format($row[csf("boe_mushak_challan_date")])."');\n";
		echo "$('#txt_gate_entry_date').val('".change_date_format($row[csf("gate_entry_date")])."');\n";

		echo "$('#cbo_location').val(".$row[csf("location_id")].");\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_receive_controller*5_6_7_23', 'store','store_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."',this.value);\n";

		echo "$('#txt_receive_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_challan_date').val('".change_date_format($row[csf("challan_date")])."');\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";

		echo "load_room_rack_self_bin('requires/chemical_dyes_receive_controller', 'floor','floor_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."',this.value);\n";

		echo "load_drop_down( 'requires/chemical_dyes_receive_controller', ".$row[csf("company_id")].", 'load_drop_down_knit_com_new', 'supplier' );\n";

		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		echo "$('#txt_exchange_rate').val(".$row[csf("exchange_rate")].");\n";
		echo "$('#cbo_source').val(".$row[csf("source")].");\n";
		echo "$('#hidden_lc_id').val(".$row[csf("lc_no")].");\n";
		if($row[csf("lc_no")]>0)
		{
			$lcNumber = return_field_value("lc_number","com_btb_lc_master_details","id='".$row[csf("lc_no")]."'");
		}
		echo "$('#txt_lc_no').val('".$lcNumber."');\n";
		echo "$('#cbo_currency').attr('disabled',false);\n";
		echo "$('#txt_exchange_rate').attr('disabled',false);\n";

		// Check Audited
		if($row[csf("is_audited")]==1) echo "$('#audited').text('Audited');\n";
		else echo "$('#audited').text('');\n";

		echo "show_list_view('".$row[csf("recv_number")]."**".$row[csf("id")]."','show_dtls_list_view','list_container_yarn','requires/chemical_dyes_receive_controller','');\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";

		$msg="Already Posted in Accounts";
        if($row[csf("is_posted_account")]==1){
			echo "$('#posted_account_td').text('".$msg."');\n";
		}else{
			
		}
 	}
	exit();
}

if($action=="populate_data_lib_data")
{
	$sql_variable_inventory = sql_select("select id, independent_controll, rate_optional, is_editable, rate_edit  from variable_settings_inventory where company_name=$data and variable_list=20 and status_active=1 and menu_page_id=4");
	if (count($sql_variable_inventory) > 0) {
		echo "1**" . $sql_variable_inventory[0][csf("independent_controll")] . "**" . $sql_variable_inventory[0][csf("rate_optional")] . "**" . $sql_variable_inventory[0][csf("is_editable")] . "**" . $sql_variable_inventory[0][csf("rate_edit")];
	} else {
		echo "0**" . $sql_variable_inventory[0][csf("independent_controll")] . "**" . $sql_variable_inventory[0][csf("rate_optional")] . "**" . $sql_variable_inventory[0][csf("is_editable")] . "**" . $sql_variable_inventory[0][csf("rate_edit")];
	}
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");
	echo "**".$sql[0][csf("auto_transfer_rcv")];
	exit();
}

if($action=="show_dtls_list_view")
{
	$ex_data = explode("**",$data);
	$recv_number = $ex_data[0];
	$rcv_mst_id = $ex_data[1];
	$cond="";
	if($db_type==0) $prod_name="concat(c.sub_group_name,' ',c.item_description,' ',c.item_size )";
	if($db_type==2) $prod_name="(c.sub_group_name||c.item_description||c.item_size )";
	if($recv_number!="") $cond .= " and a.recv_number='$recv_number'";
	if($rcv_mst_id!="") $cond .= " and a.id='$rcv_mst_id'";

	$sql="SELECT a.recv_number, b.id, b.receive_basis, b.pi_wo_batch_no,$prod_name as product_name_details, c.lot, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot 
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category in(5,6,7,23) and a.entry_form=4 $cond and a.status_active=1 and b.status_active=1 and c.status_active in(1,3) and c.is_deleted=0
	order by b.id";

	// echo $sql."<br>";

	$result = sql_select($sql);
	$i=1;
	$totalQnty=0;
	$totalAmount=0;
	$totalbookCurr=0;
	?>
    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:1000px" rules="all">
        	<thead>
            	<tr>
                	<th width="40">SL</th>
                    <th width="110">WO/PI No</th>
                    <th width="110">MRR No</th>
                    <th width="200">Product Details</th>
                    <th width="80">Batch/Lot</th>
                    <th width="70">UOM</th>
                    <th width="80">Receive Qty</th>
                    <th width="60">Rate</th>
                    <th width="70" >ILE Cost</th>
                    <th width="80">Amount</th>
                    <th>Book Currency</th>
                </tr>
            </thead>
            <tbody>
            	<?
				$current_uom=array();
			    foreach($result as $row)
				{
					if ($i%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					$wopi="";
					if($row[csf("receive_basis")]==1)
						$wopi=return_field_value("pi_number","com_pi_master_details","id=".$row[csf("pi_wo_batch_no")]."");
					else if($row[csf("receive_basis")]==2)
					$wopi=return_field_value("booking_no","inv_receive_master","id=$rcv_mst_id");
						//$wopi=return_field_value("booking_no","wo_booking_mst","id=".$row[csf("pi_wo_batch_no")]."");
					$totalQnty +=$row[csf("order_qnty")];
					$totalAmount +=$row[csf("order_amount")];
					$totalbookCurr +=$row[csf("cons_amount")];
 					?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")]."_".$row[csf("store_id")];?>","child_form_input_data","requires/chemical_dyes_receive_controller")' style="cursor:pointer" >
                        <td><? echo $i; ?></td>
                        <td ><p><? echo $wopi; ?></p></td>
                        <td ><p><? echo $row[csf("recv_number")]; ?></p></td>
                        <td ><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        <td ><p><? echo $row[csf("batch_lot")]; ?></p></td>
                        <td ><p><? echo $unit_of_measurement[$row[csf("order_uom")]]; $current_uom[$row[csf("order_uom")]]=$row[csf("order_uom")]; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf("order_qnty")],2,'.',','); ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf("order_rate")],4,'.',''); ?></p></td>
                        <td align="right"><p><? echo $row[csf("order_ile_cost")]; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf("order_amount")],2,'.',''); ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf("cons_amount")],2,'.',''); ?></p></td>

                   </tr>
                   <? $i++; 
				} ?>
                <tfoot>
                        <th colspan="6">Total</th>
                        <th><? if(count($current_uom)<2) echo number_format($totalQnty,2); ?></th>
                        <th colspan="2"></th>
                        <th><? echo number_format($totalAmount,2); ?></th>
                        <th><? echo number_format($totalbookCurr,3); ?></th>
                        <th></th>
                </tfoot>
            </tbody>
        </table>
    <?
	exit();
}

if($action=="child_form_input_data")
{
	$data=explode("_",$data);
	$rcv_dtls_id = $data[0];
	$store_id=$data[1];
	//echo $store_id;
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$woben_fabric_type_library = return_library_array( "select id,fabric_type from lib_woben_fabric_type",'id','fabric_type');

	$sql = "select a.is_posted_account,a.company_id, a.location_id, a.currency_id, a.exchange_rate, a.booking_without_order, a.booking_no, b.id, b.receive_basis, b.pi_wo_batch_no, b.prod_id, b.item_category, b.batch_lot, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount, b.expire_date, b.manufacture_date, b.floor_id, b.room, b.rack, b.self, b.bin_box, b.remarks , c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code, c.is_compliance
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	//echo $sql;txt_bla_order_qty
	$result = sql_select($sql);
	foreach($result as $row)
	{
		echo "$('#cbo_item_category_id').val('".$row[csf("item_category")]."');\n";
		//echo "load_drop_down( 'requires/chemical_dyes_receive_controller',".$row[csf('item_category')].", 'load_drop_down_item_group', 'item_group_td' )\n";
		echo "$('#cbo_item_group_id').val('".$row[csf("item_group_id")]."');\n";
		echo "$('#txt_description').val('".$row[csf("item_description")]."');\n";
		echo "$('#txt_product_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_lot').val('".$row[csf("batch_lot")]."');\n";
		echo "$('#cbo_uom').val(".$row[csf("order_uom")].");\n";
		echo "$('#txt_receive_qty').val(".$row[csf("order_qnty")].");\n";
		echo "$('#txt_rate').val(".$row[csf("order_rate")].");\n";
		echo "$('#txt_ile').val(".$row[csf("order_ile_cost")].");\n";
		echo "$('#txt_amount').val(".number_format($row[csf("order_amount")],4,'.','').");\n";
		echo "$('#txt_book_currency').val(".number_format($row[csf("cons_amount")],4,'.','').");\n";
		echo "$('#txt_referance').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_zero_discharge').val(".$row[csf("is_compliance")].");\n";
		//echo "$('#txt_order_qty').val(0);\n";
		if($row[csf("expire_date")]!="") echo "$('#txt_expire_date').val('".change_date_format($row[csf("expire_date")])."');\n";
		if($row[csf("manufacture_date")]!="") echo "$('#txt_manufac_date').val('".change_date_format($row[csf("manufacture_date")])."');\n";

		echo "$('#cbo_floor').val(".$row[csf("floor_id")].");\n";
		if($row[csf("floor_id")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_receive_controller', 'room','room_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$store_id."','".$row[csf('floor_id')]."',this.value);\n";
		}
		echo "$('#cbo_room').val(".$row[csf("room")].");\n";
		if($row[csf("room")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_receive_controller', 'rack','rack_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$store_id."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		}
		echo "$('#txt_rack').val(".$row[csf("rack")].");\n";
		if($row[csf("rack")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_receive_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$store_id."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		}
		echo "$('#txt_shelf').val(".$row[csf("self")].");\n";
		if($row[csf("self")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_receive_controller', 'bin','bin_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$store_id."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";
		}
	  	echo "$('#cbo_bin').val(".$row[csf("bin_box")].");\n";
		echo "$('#update_dtls_id').val(".$row[csf("id")].");\n";

		$prod_conds="";
		if($row[csf("item_group_id")]>0) $prod_conds.=" and C.item_group_id='".$row[csf("item_group_id")]."'";
		if(trim($row[csf("item_description")])!='') $prod_conds.=" and trim(C.item_description)='".trim($row[csf("item_description")])."'";
		if(trim($row[csf("sub_group_name")])!='') $prod_conds.=" and trim(C.sub_group_name)='".trim($row[csf("sub_group_name")])."'";
		if(trim($row[csf("item_size")])!='') $prod_conds.=" and trim(C.item_size)='".trim($row[csf("item_size")])."'";
		if(trim($row[csf("model")])!='') $prod_conds.=" and trim(C.model)='".trim($row[csf("model")])."'";
		if(trim($row[csf("item_number")])!='') $prod_conds.=" and trim(C.item_number)='".trim($row[csf("item_number")])."'";
		if(trim($row[csf("item_code")])!='') $prod_conds.=" and trim(C.item_code)='".trim($row[csf("item_code")])."'";
		
		$totalRcvQnty=0;
		$rcv_sql = sql_select("SELECT A.ID AS MST_ID, SUM(B.CONS_QUANTITY) AS QNTY 
		FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, product_details_master C
		WHERE A.ID=B.MST_ID AND B.PROD_ID=C.id AND B.TRANSACTION_TYPE=1 AND B.ITEM_CATEGORY IN(5,6,7,23) AND B.RECEIVE_BASIS='".$row[csf("receive_basis")]."' AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 and c.status_active=1 and c.is_deleted=0 AND A.BOOKING_ID='".$row[csf("pi_wo_batch_no")]."' $prod_conds group by A.ID");
		foreach($rcv_sql as $rows)
		{
			$mst_id_arr[$rows["MST_ID"]]=$rows["MST_ID"];
		}
		
		$prev_rev_rtn=sql_select("SELECT A.RECEIVED_ID, SUM(B.CONS_QUANTITY) AS QNTY 
		FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, product_details_master C 
		WHERE A.ID=B.MST_ID AND B.PROD_ID=C.id AND A.ENTRY_FORM=28 AND B.TRANSACTION_TYPE=3 AND B.ITEM_CATEGORY IN(5,6,7,23) AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 and c.status_active=1 and c.is_deleted=0 AND A.RECEIVED_ID IN(".implode(",",$mst_id_arr).") $prod_conds  
    	GROUP BY A.RECEIVED_ID");
		foreach($prev_rev_rtn as $val)
		{
			$prev_rcv_rtn_array[$val["RECEIVED_ID"]]+=$val["QNTY"];
		}
		unset($prev_rev_rtn);
		$totalRcvQnty=0;
		foreach($rcv_sql as $rows)
		{
			$totalRcvQnty+=$rows["QNTY"]-$prev_rcv_rtn_array[$rows["MST_ID"]];
		}
		
		//echo "select sum(b.cons_quantity) as recv_qnty from product_details_master a, inv_transaction b where $whereCondition";
		if($row[csf("receive_basis")]==1)
		{
			$wo_po_qnty = return_field_value("sum(b.quantity) as qnty","com_pi_item_details b, product_details_master C","b.item_prod_id=C.id and b.pi_id=".$row[csf("pi_wo_batch_no")]." and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_conds","qnty");
		}
		else if($row[csf("receive_basis")]==2)
		{
			$wo_po_qnty = return_field_value("sum(b.supplier_order_quantity) as qnty","wo_non_order_info_dtls b, product_details_master C","b.item_id=C.id and b.mst_id=".$row[csf("pi_wo_batch_no")]." and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_conds","qnty");
		}
		else if($row[csf("receive_basis")]==7)
		{
			$wo_po_qnty = return_field_value("sum(b.quantity) as qnty","inv_purchase_requisition_dtls b, product_details_master C","b.product_id=C.id and b.mst_id=".$row[csf("pi_wo_batch_no")]." and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_conds","qnty");
		}
		

		$orderQnty = ($wo_po_qnty+$row[csf("order_qnty")])-$totalRcvQnty;
		echo "$('#txt_bla_order_qty').val(".$orderQnty.");\n";

		if($row[csf("receive_basis")]==1 || $row[csf("receive_basis")] ==2 || $row[csf("receive_basis")] ==7)
		{
			echo "show_list_view(".$row[csf("receive_basis")]."+'**'+".$row[csf("pi_wo_batch_no")]."+'**'+".$row[csf("booking_without_order")]."+'**'+'".$row[csf("booking_no")]."','show_product_listview','list_product_container','requires/chemical_dyes_receive_controller','');\n";
		}
		echo "set_button_status(1, permission, 'fnc_chemical_dyes_receive_entry',1,1);\n";
		echo "fn_calile();\n";
		echo "disable_enable_fields( 'txt_lot', 1, '', '' );\n";
		if($row[csf("is_posted_account")]==1)
		{
			echo "disable_enable_fields( 'txt_receive_qty*txt_ile*txt_rate*cbo_item_category_id*cbo_item_group_id*txt_description', 1, '', '' );\n"; // disable true
		}
		else
		{
			echo "$('#txt_receive_qty').attr('disabled',false);\n";
			echo "$('#txt_ile').attr('disabled',false);\n";
			echo "$('#cbo_item_category_id').attr('disabled',true);\n";
			echo "$('#cbo_item_group_id').attr('disabled',true);\n";
			echo "$('#txt_description').attr('disabled',false);\n";
		}
	}
	exit();
}
//################################################# function Here #########################################//
//function for domestic rate find--------------//
//parameters rate,ile cost,exchange rate,conversion factor
function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor){
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;
}

if ($action=="ItemDescription_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($cbo_company_id !=0)
		$CompanyCond="and company_id=$cbo_company_id";
	else
		$CompanyCond="";
	if($cbo_item_category_id !=0)
		$item_category_id="item_category_id=$cbo_item_category_id";
	else
		$item_category_id="item_category_id in(5,6,7,23)";
	if($cbo_item_group_id !=0)
		$item_group_id="and item_group_id=$cbo_item_group_id";
	else
		$item_group_id="";
?>

	<script>

		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });

		function js_set_value(id)
		{
			$('#hidden_prod_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:980px;">
			<?
			    $company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
				$item_group_arr = return_library_array( "select id,item_name from lib_item_group",'id','item_name');

                $data_array=sql_select("select id,company_id,supplier_id,item_category_id,item_group_id, product_name_details,current_stock,avg_rate_per_unit,unit_of_measure, item_description, lot from product_details_master where $item_category_id $CompanyCond $item_group_id and status_active=1 and is_deleted=0");
            ?>
            <input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes" value="">
            <div style="margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="980">
                    <thead>
                    <tr>
                        <th colspan="10"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
                    </tr>

                    <tr>
                        <th width="30" align="center">SL</th>
                        <th width="50" align="center">Prod Id</th>
                        <th width="130">Company</th>
                        <th width="120">Item Categ</th>
                        <th width="120">Item Group</th>
                        <th width="150">Item Description</th>
                        <th width="170">Product Name</th>
                        <th width="60">Lot</th>
                        <th width="60">UOM</th>
                        <th>Qnty</th>
                    </tr>

                    </thead>
                </table>
                <div style="width:980px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="960" id="tbl_list_search">
                        <?
                        $i=1;
                        foreach($data_array as $row)
                        {
                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                         	?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>)" style="cursor:pointer" >
                                <td width="30" align="center"><? echo $i; ?></td>
                                <td width="50" align="center"><? echo $row[csf('id')]; ?></td>
                                <td width="130"><? echo  $company_arr[$row[csf('company_id')]]; ?></td>
                                <td width="120"><p><? echo $item_category[$row[csf('item_category_id')]]; ?>&nbsp;</p></td>
                                <td width="120"><p><? echo $item_group_arr[$row[csf('item_group_id')]]; ?>&nbsp;</p></td>
                                <td width="150"><p><? echo $row[csf('item_description')]; ?>&nbsp;</p></td>
                                <td width="170"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
                                <td align="center" width="60"><? echo $row[csf('lot')]; ?></td>
                                <td align="center" width="60"><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
                                <td align="right"><? echo number_format($row[csf('current_stock')],2); ?></td>

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

if($action=="chemical_dyes_receive_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r( $data);
	$company=$data[0];
	$location=$data[5];
	echo load_html_head_contents($data[2],"../../../", 1, 1, $unicode);

	$loan_party_library=return_library_array( "SELECT a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b
	where a.id=b.supplier_id and b.tag_company=$data[4] and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name", "id","supplier_name"  );
	$sql=" select id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, location_id, store_id, supplier_id, lc_no, currency_id, exchange_rate, source, gate_entry_date, booking_no,pay_mode from inv_receive_master where id='$data[1]'";
	// echo $sql;die;
	$dataArray=sql_select($sql);
	// echo "<pre>"; print_r($dataArray); die;
	$rcv_basis=$dataArray[0][csf('receive_basis')];
	$order_prev_sql=sql_select("SELECT a.BOOKING_ID, b.PROD_ID, b.ORDER_QNTY AS WO_PREV_QNTY, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE
	from  inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.receive_basis=$rcv_basis and a.id !='".$dataArray[0][csf('id')]."' and a.booking_id !='".$dataArray[0][csf('booking_id')]."'");
	foreach($order_prev_sql as $row)
	{
		//$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]=$row[csf("wo_prev_qnty")];
		$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
		$order_prev_qnty_arr[$row["BOOKING_ID"]][$item_key]+=$row["WO_PREV_QNTY"];
	}
	if($dataArray[0][csf('receive_basis')]==2)
	{

		//$wo_sql=sql_select( "select a.id,a.wo_number,b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id,a.wo_number,b.item_id");
		$wo_sql=sql_select( "select a.ID, a.WO_NUMBER, b.ITEM_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE, sum(b.supplier_order_quantity) as WO_QNTY 
		from  wo_non_order_info_mst  a, wo_non_order_info_dtls b, product_details_master c 
		where a.id=b.mst_id and b.item_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id='".$dataArray[0][csf('booking_id')]."' 
		group by a.ID, a.WO_NUMBER, b.ITEM_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE");
		foreach($wo_sql as $row)
		{
			$wo_library[$row[csf("id")]]=$row[csf("wo_number")];
			//$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]]=$row[csf("wo_qnty")];
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$wo_library[$row["ID"]]=$row["WO_NUMBER"];
			$wo_library_prod[$row["ID"]][$item_key]=$row["WO_QNTY"];
		}
	}
	else if($dataArray[0][csf('receive_basis')]==1)
	{
		$sql_pi = sql_select("select a.ID AS PI_ID, b.ITEM_PROD_ID AS ITEM_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE, SUM(b.QUANTITY) AS QUANTITY 
		from com_pi_master_details a, com_pi_item_details b, product_details_master c  
		where a.id=b.pi_id and b.ITEM_PROD_ID=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id='".$dataArray[0][csf('booking_id')]."' 
		group by a.ID, b.ITEM_PROD_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE");
		foreach($sql_pi as $row)
		{
			//$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]]=$row[csf("quantity")];
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$wo_library_prod[$row["PI_ID"]][$item_key]=$row["QUANTITY"];
		}		
	}
	else if($dataArray[0][csf('receive_basis')]==7)
	{
		$sql_pi = sql_select("select a.id AS REQ_ID, b.product_id AS ITEM_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE, sum(b.quantity) as QUANTITY 
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c  
		where a.id=b.mst_id and b.product_id=c.id and a.id='".$dataArray[0][csf('booking_id')]."' 
		group by a.ID, b.product_id, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE");
		foreach($sql_pi as $row)
		{
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$wo_library_prod[$row["REQ_ID"]][$item_key]=$row["QUANTITY"];
		}
	}
	// echo "<pre>";print_r($wo_library_prod);die;
	// echo "<pre>";print_r($order_prev_qnty_arr);die;
	if($rcv_basis==1)
	{
		$pi_library=return_library_array( "select id,pi_number from  com_pi_master_details where entry_form=227 and id='".$dataArray[0][csf('booking_id')]."' ", "id","pi_number"  );
	}
	elseif($rcv_basis==2)
	{
		$wo_library=return_library_array( "select id,wo_number from  wo_non_order_info_mst where entry_form=145 and id='".$dataArray[0][csf('booking_id')]."' ", "id","wo_number"  );
	}
	
	
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$lc_number_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details where item_category_id in(5,6,7,23)", "id", "lc_number"  );
	$user_sql=sql_select("select ID, USER_NAME, USER_FULL_NAME from user_passwd");
	foreach($user_sql as $row)
	{
		$user_id[$row["ID"]]=$row["USER_NAME"];
		$user_name[$row["ID"]]=$row["USER_FULL_NAME"];
	}
	//$user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	//$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
	$com_dtls = fnc_company_location_address($company, $location, 2);

	?>
	<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $com_dtls[0]; ?></strong></td>
        </tr>
        <tr class="">
        	<td colspan="6" align="center" style="font-size:14px">
				<?
					echo $com_dtls[1];
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result['plot_no']; ?>
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no'];?>
						City No: <? echo $result['city'];?>
						Zip Code: <? echo $result['zip_code']; ?>
						Province No: <? echo $result['province'];?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email'];?>
						Website No: <? echo $result['website'];
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Dyes & Chemical Receive Report</u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>MRR Number:</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="130"><strong>Receive Basis :</strong></td> <td width="135px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Receive Date:</strong></td><td width="275px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>L/C No:</strong></td><td width="175px"><? echo $lc_number_arr[$dataArray[0][csf('lc_no')]]; ?></td>
            <td><strong>Store Name:</strong></td><td width="175px"><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td> <td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong>Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            <td><strong>Exchange Rate:</strong></td><td width="175px"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
        </tr>
        <tr>
            <td><strong>Gate Entry No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            <td><strong>WO/PI/req:</strong></td> <td width="175px"><? 
			if ($dataArray[0][csf('receive_basis')]==1){ echo $pi_library[$dataArray[0][csf('booking_id')]];}
			else if($dataArray[0][csf('receive_basis')]==2){ echo $wo_library[$dataArray[0][csf('booking_id')]];}
			else{  echo $dataArray[0][csf('booking_no')];} ?></td>
        </tr>
        <tr>
            <td><strong>Receive Purpose:</strong></td> <td width="175px"><? echo $general_issue_purpose[$dataArray[0][csf('receive_purpose')]]; ?></td>
            <td><strong>Loan Party:</strong></td><td width="175px"><? echo $loan_party_library[$dataArray[0][csf('loan_party')]]; ?></td>
            <td><strong>Bar Code:</strong></td> <td width="175px" id="barcode_img_id"></td>
        </tr>
		<tr>
            <td><strong>Gate Entry Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
			<td><strong>Pay mode:</strong></td> <td width="175px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            <td></td>
			<td></td>

        </tr>
    </table>
    <?
	if($db_type==2)
	{
	  $sql_dtls = "SELECT b.item_category,
	  b.id, b.receive_basis, a.booking_id, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, a.audit_by, a.audit_date, a.is_audited,
	  (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details, c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code
	  from inv_receive_master a, inv_transaction b, product_details_master c 
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category in(5,6,7,23) and a.entry_form=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	  order by b.id";
	}
	else
	{
	  $sql_dtls = "SELECT b.item_category,a.booking_id,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot,b.prod_id, a.audit_by, a.audit_date, a.is_audited,
	 concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code
	  from inv_receive_master a, inv_transaction b, product_details_master c 
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category in(5,6,7,23) and a.entry_form=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	  order by b.id";

	}
	// echo $sql_dtls; die; $item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
	$sql_result= sql_select($sql_dtls);
	$i=1;
	$item_name_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );
	?>
    <br>
	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Item Category</th>
            <th width="130" align="center">Item Group</th>
            <th width="180" align="center">Item Description</th>
            <th width="50" align="center">Lot</th>
            <th width="50" align="center">UOM</th>
            <th width="80" align="center">Recv. Qnty.</th>
            <th width="50" align="center">Rate</th>
            <th width="70" align="center">Amount</th>
            <?
			if($data[3]!=4)
			{
			?>
            <th width="80" align="center">PI/WO Qnty Bal.</th>
            <?
			}
			?>
            <th width="80" align="center">Warranty Exp. Date</th>
        </thead>
    <?
    $uomTempContainer = array();
	//echo "<pre>";print_r($wo_library_prod);
    foreach($sql_result as $row)
    {
		$item_key=$row[csf('item_group_id')]."**".trim($row[csf('item_description')])."**".trim($row[csf('sub_group_name')])."**".trim($row[csf('item_size')])."**".trim($row[csf('model')])."**".trim($row[csf('item_number')])."**".trim($row[csf('item_code')]);
		//echo $row[csf("booking_id")]."__".$item_key."__".$wo_library_prod[$row[csf("booking_id")]][$item_key]."__".$order_prev_qnty_arr[$row[csf("booking_id")]][$item_key]."__".$row[csf('order_qnty')]."<br>";
        if ($i%2==0)
            $bgcolor="#E9F3FF";
        else
            $bgcolor="#FFFFFF";
            $order_qnty=$row[csf('order_qnty')];
            $order_qnty_sum += $order_qnty;

            $order_amount=$row[csf('order_amount')];
            $order_amount_sum += $order_amount;

            // $balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$item_key]-($row[csf('order_qnty')]+$order_prev_qnty_arr[$row[csf("booking_id")]][$item_key]));
			
            $balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]-$row[csf('order_qnty')]);

            $balance_qnty_sum += $balance_qnty;

            $desc=$row[csf('item_description')];

            if($row[csf('item_size')]!="")
            {
                $desc.=", ".$row[csf('item_size')];
            }
            array_push($uomTempContainer, $row[csf('order_uom')]);
        ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf('item_category')]]; ?></td>
                <td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                <td align="center"><? echo $row[csf('product_name_details')]; ?></td>
                <td><? echo $row[csf('batch_lot')]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                <td align="right"><? echo number_format($row[csf('order_qnty')],2,'.',',');  ?></td>
                <td align="right"><? echo number_format($row[csf('order_rate')], 4); ?></td>
                <td align="right"><? echo number_format($row[csf('order_amount')],4);  ?></td>
                <?
                if($data[3]!=4)
                    {
                 ?>
                      <td align="right"><? echo number_format($balance_qnty,2); ?></td>
                  <?
                    }

                 ?>
                <td><? if($row[csf('expire_date')]!="") echo change_date_format($row[csf('expire_date')]); ?></td>
            </tr>
            <?
            $i++;
    }
			?>
        	<tr>
                <td align="right" colspan="6" >Total</td>
                <?
                if(count(array_unique($uomTempContainer)) == 1){
                ?>
                    <td align="right"><? echo number_format($order_qnty_sum,2,'.',','); ?></td>
                    <td align="right" colspan="2" ><? echo number_format($order_amount_sum,4); ?></td>
                <?
                }else{
                ?>
                    <td align="right" colspan="3" ><? echo number_format($order_amount_sum,4); ?></td>
                <?
                }
				if($data[3]!=4)
		        	{
			     ?>
                <td align="right" ><? echo number_format($balance_qnty_sum,2); ?></td>
                 <?
		        	}

			     ?>
                <td align="right">&nbsp;</td>
			</tr>
		</table>
		<table width="900" align="right">
			<tr>
				<?php

				if($sql_result[0][csf("is_audited")]==1){
					?>
					<td><? echo 'Audited By &nbsp;'.$user_name[$sql_result[0][csf("audit_by")]].'&nbsp;'.$sql_result[0][csf("audit_date")]; ?></td>
				<?php
				}
				?>
				
				
			</tr>
		</table>
        <br>
		 <?
            echo signature_table(8, $data[0], "900px");
         ?>
      </div>
   </div>

     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){

			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);

		}

	 generateBarcode('<? echo $dataArray[0][csf('recv_number')]; ?>');


	 </script>




 <?
 exit();
}


if($action=="chemical_dyes_receive_print_new")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$company=$data[0];
	$location=$data[5];
	echo load_html_head_contents("Dyes Chemical Receive Info","../../../", 1, 1, $unicode);
	$loan_party_library=return_library_array( "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b
	where a.id=b.supplier_id and b.tag_company=$data[4] and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name", "id","supplier_name"  );
    if(trim($data[6]) == 'yes'){
        $tableWidth = '1300';
    }else{
        $tableWidth = '1170';
    }
	$sql=" SELECT id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, location_id, store_id, supplier_id, lc_no, currency_id, exchange_rate, source, boe_mushak_challan_no, boe_mushak_challan_date, supplier_referance, pay_mode, inserted_by, booking_no, gate_entry_date, challan_date  from inv_receive_master where id='$data[1]'";
	// echo $sql;die;
	$dataArray=sql_select($sql);
	$booking_ids=$dataArray[0][csf("booking_id")];
	if($booking_ids) $book_cond=" and a.booking_id in($booking_ids)";
	if($booking_ids) $book_cond_return=" and c.booking_id in($booking_ids)";

	if($dataArray[0][csf('receive_basis')]==2)
	{
		
		$wo_sql=sql_select( "SELECT a.ID, a.WO_NUMBER, b.ITEM_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE, sum(b.supplier_order_quantity) as WO_QNTY
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c 
		where a.id=b.mst_id and b.item_id=c.id and a.id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0
		group by a.ID, a.WO_NUMBER, b.ITEM_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE");
		foreach($wo_sql as $row)
		{
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$wo_library[$row["ID"]]=$row["WO_NUMBER"];
			$wo_library_prod[$row["ID"]][$item_key]=$row["WO_QNTY"];
		}
		
		$order_prev_sql=sql_select("select a.BOOKING_ID, b.PROD_ID, b.ORDER_QNTY AS WO_PREV_QNTY, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE 
		from inv_receive_master a, inv_transaction b, product_details_master c  
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=2 and a.id !='".$dataArray[0][csf('id')]."' $book_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0");
		foreach($order_prev_sql as $row)
		{
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$order_prev_qnty_arr[$row["BOOKING_ID"]][$item_key]+=$row["WO_PREV_QNTY"];
		}
		
		$prev_rtn=sql_select("select d.BOOKING_ID, b.PROD_ID, b.CONS_QUANTITY AS WO_PREV_QNTY, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE  
		from inv_receive_master d, inv_issue_master a, inv_transaction b, product_details_master c 
		where d.id=a.received_id and a.id=b.mst_id and b.prod_id=c.id and d.receive_basis=2 and b.transaction_type=3 $book_cond_return and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0");
		foreach($prev_rtn as $row)
		{
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$prev_rtn_arr[$row["BOOKING_ID"]][$item_key]+=$row["WO_PREV_QNTY"];
		}
	}
	else if($dataArray[0][csf('receive_basis')]==1)
	{
		$sql_pi = sql_select("select a.ID AS PI_ID, b.ITEM_PROD_ID AS ITEM_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE, SUM(b.QUANTITY) AS QUANTITY 
		from com_pi_master_details a, com_pi_item_details b, product_details_master c  
		where a.id=b.pi_id and b.ITEM_PROD_ID=c.id and a.id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 
		group by a.ID, b.ITEM_PROD_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE");
		foreach($sql_pi as $row)
		{
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$wo_library_prod[$row["PI_ID"]][$item_key]=$row["QUANTITY"];
		}
		
		$order_prev_sql=sql_select("select a.BOOKING_ID, b.PROD_ID, b.ORDER_QNTY AS WO_PREV_QNTY, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE 
		from inv_receive_master a, inv_transaction b, product_details_master c  
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=1 and a.id !='".$dataArray[0][csf('id')]."' $book_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0");
		foreach($order_prev_sql as $row)
		{
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$order_prev_qnty_arr[$row["BOOKING_ID"]][$item_key]+=$row["WO_PREV_QNTY"];
		}
		
		$prev_rtn=sql_select("select d.BOOKING_ID, b.PROD_ID, b.CONS_QUANTITY AS WO_PREV_QNTY, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE  
		from inv_receive_master d, inv_issue_master a, inv_transaction b, product_details_master c 
		where d.id=a.received_id and a.id=b.mst_id and b.prod_id=c.id and d.receive_basis=1 and b.transaction_type=3 $book_cond_return and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0");
		foreach($prev_rtn as $row)
		{
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$prev_rtn_arr[$row["BOOKING_ID"]][$item_key]+=$row["WO_PREV_QNTY"];
		}
	}
	else if($dataArray[0][csf('receive_basis')]==7)
	{
		$sql_pi = sql_select("select a.id AS REQ_ID, b.product_id AS ITEM_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE, sum(b.quantity) as QUANTITY 
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c  
		where a.id=b.mst_id and b.product_id=c.id and a.id='".$dataArray[0][csf('booking_id')]."' 
		group by a.ID, b.product_id, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE");
		foreach($sql_pi as $row)
		{
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$wo_library_prod[$row["REQ_ID"]][$item_key]=$row["QUANTITY"];
		}
		//echo "<pre>";print_r($wo_library_prod);
		$order_prev_sql=sql_select("select a.BOOKING_ID, b.PROD_ID, b.ORDER_QNTY AS WO_PREV_QNTY, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE 
		from inv_receive_master a, inv_transaction b, product_details_master c  
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=7 and a.id !='".$dataArray[0][csf('id')]."' $book_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0");
		foreach($order_prev_sql as $row)
		{
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$order_prev_qnty_arr[$row["BOOKING_ID"]][$item_key]+=$row["WO_PREV_QNTY"];
		}
		
		$prev_rtn=sql_select("select d.BOOKING_ID, b.PROD_ID, b.CONS_QUANTITY AS WO_PREV_QNTY, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SUB_GROUP_NAME, c.ITEM_SIZE, c.MODEL, c.ITEM_NUMBER, c.ITEM_CODE  
		from inv_receive_master d, inv_issue_master a, inv_transaction b, product_details_master c 
		where d.id=a.received_id and a.id=b.mst_id and b.prod_id=c.id and d.receive_basis=7 and b.transaction_type=3 $book_cond_return and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0");
		foreach($prev_rtn as $row)
		{
			$item_key=$row["ITEM_GROUP_ID"]."**".trim($row["ITEM_DESCRIPTION"])."**".trim($row["SUB_GROUP_NAME"])."**".trim($row["ITEM_SIZE"])."**".trim($row["MODEL"])."**".trim($row["ITEM_NUMBER"])."**".trim($row["ITEM_CODE"]);
			$prev_rtn_arr[$row["BOOKING_ID"]][$item_key]+=$row["WO_PREV_QNTY"];
		}
	}

	$requisition_sql = sql_select("SELECT REQU_NO FROM INV_PURCHASE_REQUISITION_MST WHERE status_active=1 AND ID IN(SELECT REQUISITION_NO FROM WO_NON_ORDER_INFO_MST WHERE status_active=1 AND ID IN($booking_ids))");
	// echo "<pre>"; print_r($requisition_sql); die;

	$pi_library=return_library_array( "select id,pi_number from  com_pi_master_details where entry_form=227 ", "id","pi_number"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$lc_number_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details", "id", "lc_number"  );
	$com_dtls = fnc_company_location_address($company, $location, 2);

?>
	<div style="width:<?=$tableWidth?>px;">
    <table width="1050" cellspacing="0" style="margin: 0 auto;">
        <tr>
            <td colspan="2" rowspan="2" id="barcode_img_id"></td>
            <td colspan="2" align="center" style="font-size:xx-large"><strong><? echo $com_dtls[0]; ?></strong></td>
            <td colspan="2">
                   <?
				   	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company'","image_location");
					?>
                    <img  src='../../../<? echo $image_location; ?>' height='70' align="left" />
            
            </td>
        </tr>
        <tr class="">

            <td colspan="2" align="center" style="font-size:14px">
				<?
					echo $com_dtls[1];
                ?>
            </td>
            <td colspan="1"></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Dyes And Chemical Receiving Report</u></strong></td>
        </tr>
        <tr>
        	<td width="150"><strong>Requisition No:</strong></td><td width="200"><? echo $requisition_sql[0]['REQU_NO']; ?></td>
            <td width="150"><strong>Receive Basis :</strong></td> <td width="200"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="150"><strong>Receive Date:</strong></td><td ><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
        	<td width="150"><strong>MRR Number:</strong></td><td width="200"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            
            <td><strong>WO/PI:</strong></td> <td ><? if ($dataArray[0][csf('receive_basis')]==1) echo $pi_library[$dataArray[0][csf('booking_id')]]; else echo $wo_library[$dataArray[0][csf('booking_id')]]; ?></td>
            <td><strong>Store Name:</strong></td><td ><strong><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></strong></td>
        </tr>
        <tr>
        	<td><strong>Challan No:</strong></td> <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>L/C No:</strong></td><td ><? echo $lc_number_arr[$dataArray[0][csf('lc_no')]]; ?></td>
            <td><strong>Source:</strong></td><td ><? echo $source[$dataArray[0][csf('source')]]; ?></td>
        </tr>
          <tr>
          	<td><strong>Supplier:</strong></td> <td ><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong>Currency:</strong></td><td ><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            <td><strong>Receive Purpose:</strong></td> <td width="175px"><? echo $general_issue_purpose[$dataArray[0][csf('receive_purpose')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Gate Entry No:</strong></td> <td ><? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
            <td><strong>Exchange Rate:</strong></td><td ><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
            <td><strong>Loan Party:</strong></td><td width="175px"><? echo $loan_party_library[$dataArray[0][csf('loan_party')]]; ?></td>
        </tr>
		<tr>
			<td><strong>Pay Mode:</strong></td> <td ><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            <td><strong>BOE/Mushak Challan No:</strong></td> <td ><? echo $dataArray[0][csf('boe_mushak_challan_no')]; ?></td>
            <td><strong>BOE/Mushak Challan Date:</strong></td><td ><? echo change_date_format($dataArray[0][csf('boe_mushak_challan_date')]); ?></td>
            <td><strong></strong></td><td></td>
        </tr>
        <tr>
			<td><strong>Gate Entry Date:</strong></td> <td ><? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
			<td><strong>Challan Date:</strong></td> <td ><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
        </tr>		
    </table>
    <?
	/*if($db_type==2)
	{
	  $sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks,
	  (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details, c.item_group_id, c.item_description,c.item_code
	  from inv_receive_master a, inv_transaction b, product_details_master c where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category in(5,6,7,23) and a.entry_form=4";
	}
	else
	{
	  $sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks,
	 concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_group_id, c.item_description,c.item_code
	  from inv_receive_master a, inv_transaction b, product_details_master c where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category in(5,6,7,23) and a.entry_form=4";

	}*/
	
	if($db_type==2)
	{
		$sql_dtls = "select a.id as recv_id, a.booking_id,a.exchange_rate, b.item_category, b.manufacture_date,
		b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details, c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category in(5,6,7,23) and a.entry_form=4  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		order by b.id";
	}
	else
	{
		$sql_dtls = "select a.id as recv_id, a.booking_id, a.exchange_rate, b.item_category, b.manufacture_date,
		b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks, concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_group_id, c.item_description, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category in(5,6,7,23) and a.entry_form=4 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		order by b.id";

	}
	// echo $sql_dtls; die;

	 $sql_result= sql_select($sql_dtls);
	 $i=1;
	 $item_name_arr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );

	  ?>

<div style="width:100%;">
    <table align="right" cellspacing="0" width="<?=$tableWidth?>"  border="1" rules="all" class="rpt_table" style="margin-top:10px;" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="40" >Item Code</th>
            <th width="70" >Item Category</th>
            <th width="110" >Item Group</th>
			<th width="100" >Item Sub Group</th>
            <th width="160" >Item Description</th>
            <th width="40" >Lot</th>
            <th width="40" >UOM</th>
            <th width="70" >WO/PI Qnty.</th>
            <th width="70" >Previous Recv Qnty</th>
            <th width="70" >Receive Return Qnty</th>
            <th width="70" >Today Recv. Qnty.</th>
            <th width="80">WO/PI Qnty Bal.</th>
            <?
            if(trim($data[6]) == 'yes'){
            ?>
                <th width="50" >Rate</th>
                <th width="80">Amount</th>
            <?
            }
            ?>
            <th width="70">Bd Amount</th>
            <th width="70">Warranty Exp. Date</th>
            <th width="60">Manuf. Date</th>
            <th >Comments</th>
        </thead>
      	<?
		//echo "<pre>";print_r($wo_library_prod);echo "<pre>";print_r($order_prev_qnty_arr);
		foreach($sql_result as $row)
		{
			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			$order_qnty=$row[csf('order_qnty')];
			$order_qnty_sum += $order_qnty;
			$bd_ammount="";

			$order_amount=$row[csf('order_amount')];
			$exchange_rate=$row[csf('exchange_rate')];
			$order_amount_sum += $order_amount;
			$bd_ammount+=$exchange_rate*$order_amount;

			$total_bd_ammount+=$bd_ammount;
			
			$item_key=$row[csf("item_group_id")]."**".trim($row[csf("item_description")])."**".trim($row[csf("sub_group_name")])."**".trim($row[csf("item_size")])."**".trim($row[csf("model")])."**".trim($row[csf("item_number")])."**".trim($row[csf("item_code")]);
			
			$balance_qnty=(($wo_library_prod[$row[csf("booking_id")]][$item_key]+$prev_rtn_arr[$row[csf("booking_id")]][$item_key])-($row[csf('order_qnty')]+$order_prev_qnty_arr[$row[csf("booking_id")]][$item_key]));
			$balance_qnty_sum += $balance_qnty;

			$desc=$row[csf('item_description')];

			if($row[csf('item_size')]!="")
			{
				$desc.=", ".$row[csf('item_size')];
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td><? echo $i; ?></td>
				<td><? echo $row[csf('item_code')]; ?></td>
				<td><? echo $item_category[$row[csf('item_category')]]; ?></td>
				<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
				<td><? echo $row[csf('sub_group_name')]; ?></td>
				<td align="center"><? echo $row[csf('product_name_details')]; ?></td>
				<td><? echo $row[csf('batch_lot')]; ?></td>
				<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
				<td align="right" title="<?= $row[csf("booking_id")]."=".$row[csf("prod_id")]."=".$item_key; ?>"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$item_key],2); $tot_ord_qnty+=$wo_library_prod[$row[csf("booking_id")]][$item_key]; ?></td>
				<td align="right"><? echo number_format($order_prev_qnty_arr[$row[csf("booking_id")]][$item_key],2); $tot_prev_qnty+=$order_prev_qnty_arr[$row[csf("booking_id")]][$item_key]; ?></td>
				<td align="right"><? echo number_format($prev_rtn_arr[$row[csf("booking_id")]][$item_key],2); $tot_prev_rtn+=$prev_rtn_arr[$row[csf("booking_id")]][$item_key]; ?></td>
				<td align="right"><? echo number_format($row[csf('order_qnty')],2); ?></td>
				<td align="right"><? echo  number_format($balance_qnty,2); ?></td>
                <?
                if(trim($data[6]) == 'yes'){
                    ?>
                    <td align="right"><? echo number_format($row[csf('order_rate')],4); ?></td>
                    <td align="right"><? echo number_format($row[csf('order_amount')],2,'.',',');  ?></td>
                    <?
                }
                ?>
				<td align="right"><? echo number_format($bd_ammount,2); ?></td>
				<td><? if($row[csf('expire_date')]!="" && $row[csf('expire_date')]!="0000-00-00" ) echo change_date_format($row[csf('expire_date')]); else echo "&nbsp;"; ?></td>
                <td><? if($row[csf('manufacture_date')]!="" && $row[csf('manufacture_date')]!="0000-00-00" ) echo change_date_format($row[csf('manufacture_date')]); else echo "&nbsp;"; ?></td>
                <td><? echo $row[csf('remarks')]; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
		<tr bgcolor="#CCCCCC">
			<td align="right" colspan="8" >Total</td>
			<td align="right"><? echo number_format($tot_ord_qnty,2,'.',','); ?></td>
			<td align="right"><? echo number_format($tot_prev_qnty,2,'.',','); ?></td>
			<td align="right"><? echo number_format($tot_prev_rtn,2,'.',','); ?></td>
			<td align="right"><? echo number_format($order_qnty_sum,2,'.',','); ?></td>
            <?
            if(trim($data[6]) == 'yes'){
                ?>
                <td align="right" ><? echo number_format($balance_qnty_sum,2,'.',','); ?></td>
                <td>&nbsp;</td>
                <td align="right" colspan="" ><? echo number_format($order_amount_sum,2,'.',','); ?></td>
                <?
            }else{
                ?>
                <td align="right">&nbsp;</td>
                <?
            }
                ?>

			<td align="right"><?php echo number_format($total_bd_ammount,2,'.',','); ?>&nbsp;</td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
        </tr>
		<br>
		<tr>
		<td align="left" colspan="19" ><strong style="font-size:10pt;">In words: </strong><? echo number_to_words($order_amount_sum); ?></td>
		</tr>
	</table>
        <br>
		 <?
            echo signature_table(8, $data[0], $tableWidth."px",'',50, $dataArray[0][csf('inserted_by')]);
         ?>
      </div>
   </div>

     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){

			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);

		}

	 generateBarcode('<? echo $dataArray[0][csf('recv_number')]; ?>');


	 </script>

 <?
 exit();
}

function fnc_store_wise_qty_operation($company_id,$store_id,$category,$prod_id,$trans_type,$dyes_lot)
{
	$trans_type=str_replace("'","",$trans_type);
	$prod_id=str_replace("'","",$prod_id);
	$store_id=str_replace("'","",$store_id);
	$category=str_replace("'","",$category);
	$company_id=str_replace("'","",$company_id);
	$dyes_lot=str_replace("'","",$dyes_lot);
	
	if($trans_type==2)
	{
		$prod_ids=rtrim($prod_id,",");
		$prod_ids=array_chunk(array_unique(explode(",",$prod_ids)),1000, true);
		$prod_cond="";
		$ji=0;
		foreach($prod_ids as $key=> $value)
		{
			if($ji==0)
			{
				$prod_cond=" and prod_id  in(".implode(",",$value).")";
			}
			else
			{
				$prod_cond.=" or prod_id  in(".implode(",",$value).")";
			}
			$ji++;
		}
		$category_ids=rtrim($category,",");
		$cat_ids=array_chunk(array_unique(explode(",",$category_ids)),1000, true);
		$cat_cond="";
		$k=0;
		foreach($cat_ids as $key=> $value)
		{
			if($k==0)
			{
				$cat_cond=" and category_id  in(".implode(",",$value).")";
			}
			else
			{
				$cat_cond.=" or category_id  in(".implode(",",$value).")";
			}
			$k++;
		}
	}

	if($trans_type==2) //Issue
	{
		$sql_data=sql_select("select id, company_id, category_id, prod_id, cons_qty, rate, amount from inv_store_wise_qty_dtls where company_id=$company_id  and status_active=1 and is_deleted=0 $prod_cond $cat_cond");
	}
	else if($trans_type==1 || $trans_type==4) //Recv && Issue Return;
	{
		$lot_cond="";
		if($dyes_lot!="")  $lot_cond=" and lot='$dyes_lot'";
		$sql_data=sql_select("select id, company_id, category_id, store_id, prod_id, cons_qty, rate, amount, lot
		from inv_store_wise_qty_dtls where company_id=$company_id and store_id=$store_id and category_id in($category) and status_active=1 and is_deleted=0 and prod_id=$prod_id $lot_cond");
	}
	$stock_prod_arr=array();
	if($trans_type==2) //Issue
	{
		$updated_store_ids=''; $updated_ids='';$prod_arr=array();
		foreach($sql_data as $row)
		{
		if($updated_store_ids=='') $updated_store_ids=$row[csf("id")];else $updated_store_ids.=",".$row[csf("id")];
		}
		$stock_prod_arr=$updated_store_ids;//.'**'.$stock_prod_arr;
	}
	else if($trans_type==1 || $trans_type==4) //recv && Issue Return;
	{
		if(count($sql_data)>0)//value Empty
		{
			foreach($sql_data as $row)
			{
				$stock_prod_arr[$row[csf('company_id')]][$row[csf('prod_id')]][$row[csf('store_id')]][$row[csf('category_id')]][$row[csf('lot')]]=$row[csf('id')];
			}
		}
	}

	 return $stock_prod_arr;

} //Function End

//Store Wise Stock Function
function fnc_store_wise_stock($company_id,$store_id,$category,$prod_id)
	{
		 $result=sql_select("select	 category_id,prod_id,
		 sum(CASE WHEN transaction_type=1 THEN cons_qty ELSE 0 END) AS recv_qty,
		 sum(CASE WHEN transaction_type=2 THEN cons_qty ELSE 0 END) AS issue_qty,
		 sum(CASE WHEN transaction_type=3 THEN cons_qty ELSE 0 END) AS recv_ret_qty,
		 sum(CASE WHEN transaction_type=4 THEN cons_qty ELSE 0 END) AS issue_ret_qty,
		 sum(CASE WHEN transaction_type=5 THEN cons_qty ELSE 0 END) AS transfer_in,
		 sum(CASE WHEN transaction_type=6 THEN cons_qty ELSE 0 END) AS transfer_out
		 from  inv_store_wise_qty_dtls where  company_id=$company_id and store_id=$store_id and status_active=1 and is_deleted=0 group by  category_id,prod_id");
		$stock_qty_arr=array();
		 foreach($result as $row)
		 {
			 $stock_qty_arr[$company_id][$store_id][$row[csf('category_id')]][$row[csf('prod_id')]]['stock']=($row[csf('recv_qty')]+$row[csf('issue_ret_qty')]+$row[csf('transfer_in')])-($row[csf('issue_qty')]+$row[csf('recv_ret_qty')]+$row[csf('transfer_out')]);
		 }
		 return $stock_qty_arr;
	}
?>
