<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

// user credential data prepare start
$userCredential = sql_select("SELECT store_location_id,unit_id as company_id,company_location_id,supplier_id,buyer_id,item_cate_id FROM user_passwd where id='$user_id'");
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];


if($item_cate_id !='') {
    $item_cate_credential_cond =  "and lib_item_group.id in($item_cate_id)";  
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

if ($supplier_id !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}
// user credential data prepare end 

if($db_type==2 || $db_type==1 )
{
	$mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
	$group_concat="wm_concat";
}
else if ($db_type==0)
{
	$mrr_date_check="and year(insert_date)=".date('Y',time())."";
	$group_concat="group_concat";
}

//--------------------------------------------------------------------------------------------
$trim_group_arr = return_library_array("select id, order_uom from lib_item_group","id","order_uom");
//load drop down supplier
if ($action=="load_drop_down_supplier")
{
	//echo "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39) $supplier_credential_cond and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name";die;	 
	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39) $supplier_credential_cond and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	exit();
}

if ($action=="load_drop_down_loan_party")
{
	echo create_drop_down( "cbo_loan_party", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","","" );
	exit();
}

//load drop down store
if ($action=="load_drop_down_store")
{	
	$data=explode("_",$data);
	//echo $data[1];die;
	//echo "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in ($data[1]) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id in($data[0]) group by a.id ,a.store_name order by a.store_name";die;
	echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in ($data[1]) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id in($data[0]) group by a.id ,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", "", "","" );  	 
	exit();
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
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();	
}
 //load drop down item group
if ($action=="load_drop_down_itemgroup")
{	   
	echo create_drop_down( "cbo_item_group", 130, "select id,item_name from lib_item_group where item_category=$data and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", 0, "load_drop_down( 'requires/emb_material_receive_controller', this.value, 'load_drop_down_uom', 'uom_td' );","" );  	 
	exit();
}
 
  //load drop down uom
if ($action=="load_drop_down_uom")
{	
	if($data==0) $uom=0; else $uom=$trim_group_arr[$data];
	//echo $data;die;
	echo create_drop_down( "cbo_uom", 130, $unit_of_measurement, "", 1, "-- Select --", $uom , "", 1); 	 
	exit();
} 
 
// wo/pi popup here----------------------// 
if ($action=="wopi_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST); 
	$itme_category_id=str_replace("'","",$itme_category_id); 
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
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
    		<thead>
             <th width="150"> </th>
                <th>
                  <?
                   echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                  ?>
                </th>
              <th width="150" colspan="3"> </th>
            </thead>
            <thead>
                <th width="150">Search By</th>
                <th width="150" align="center" id="search_by_th_up">Enter WO/PI/Req Number</th>
                <th width="150">Item Category</th>
                <th width="100">Year</th>
                <th width="200">Date Range</th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?  
                            echo create_drop_down( "cbo_search_by", 150, $receive_basis_arr,"",1, "--Select--", $receive_basis,"",1 );
                        ?>
                    </td>
                    <td width="180" align="center" id="search_by_td">				
                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                    <td>
                        <?
							//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index )
							echo create_drop_down( "cbo_item_category", 170, $item_category,"", 1, "-- Select --", "", "","","$itme_category_id","","","" );
						?> 
                    </td>   
					<td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                     </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $itme_category_id; ?>'+'_'+document.getElementById('cbo_year_selection').value, 'create_wopi_search_list_view', 'search_div', 'emb_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            	</tr>
                <tr>                  
                    <td align="center" height="40" valign="middle" colspan="5">
                        <? echo load_month_buttons(1);  ?>
                        <!-- Hidden field here-->
                        <input type="hidden" id="hidden_tbl_id" value="" />
                        <input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number" />
                        <!--END-->
                    </td>
                </tr>    
            </tbody>
        </table> 
        <br>   
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
	// print_r($ex_data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = str_replace("'","",$ex_data[4]);
	$item_cat_ref = $ex_data[5];
	$cbo_string_search_type = $ex_data[6];
	$itme_category_id = str_replace("'","",$ex_data[7]);
		if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$ex_data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[8]";}
	
	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_date_from=change_date_format($txt_date_from,'','',1);
			$txt_date_to=change_date_format($txt_date_to,'','',1);
		}
	}
 	
	$sql_cond="";
	if(trim($txt_search_by)==1) // for pi
	{
		if( $txt_date_from!="" && $txt_date_to!="" ) $sql_cond .= " and a.pi_date  between '".$txt_date_from."' and '".$txt_date_to."'";
		if($item_cat_ref==126) $item_cat_ref="126";
		if($item_cat_ref>0) $sql_cond .= " and a.item_category_id in($item_cat_ref)";
	}
	else if(trim($txt_search_by)==2) // for wo
	{
		if( $txt_date_from!="" && $txt_date_to!="" ) $sql_cond .= " and a.wo_date  between '".$txt_date_from."' and '".$txt_date_to."'";
		if($item_cat_ref>0) $sql_cond .= " and b.item_category_id=$item_cat_ref";
	}
	else if(trim($txt_search_by)==7) // for requisition
	{
		if( $txt_date_from!="" && $txt_date_to!="" ) $sql_cond .= " and a.requisition_date  between '".$txt_date_from."' and '".$txt_date_to."'";
		if($item_cat_ref>0) $sql_cond .= " and b.item_category=$item_cat_ref";
	}
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for pi
		{
			if($cbo_string_search_type==1)
			{
				$sql_cond .= " and a.pi_number='$txt_search_common'";
			}
			else if($cbo_string_search_type==2)
			{
				$sql_cond .= " and a.pi_number LIKE '$txt_search_common%'";
			}
			else if($cbo_string_search_type==3)
			{
				$sql_cond .= " and a.pi_number LIKE '%$txt_search_common'";
			}
			else
			{
				$sql_cond .= " and a.pi_number LIKE '%$txt_search_common%'";
			}
			
				
			if(trim($company)!="") $sql_cond .= " and a.importer_id='$company'";
		}
		else if(trim($txt_search_by)==2) // for wo
		{
			if($cbo_string_search_type==1)
			{
				$sql_cond .= " and a.wo_number_prefix_num='$txt_search_common'";
			}
			else if($cbo_string_search_type==2)
			{
				$sql_cond .= " and a.wo_number_prefix_num LIKE '$txt_search_common%'";
			}
			else if($cbo_string_search_type==3)
			{
				$sql_cond .= " and a.wo_number_prefix_num LIKE '%$txt_search_common'";
			}
			else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
			{
				$sql_cond .= " and a.wo_number_prefix_num LIKE '%$txt_search_common%'";
			}
							
			if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
		}
		else if(trim($txt_search_by)==7) // for requisition
		{
			if($cbo_string_search_type==1)
			{
				$sql_cond .= " and a.requ_prefix_num='$txt_search_common'";
			}
			else if($cbo_string_search_type==2)
			{
				$sql_cond .= " and a.requ_prefix_num LIKE '$txt_search_common%'";
			}
			else if($cbo_string_search_type==3)
			{
				$sql_cond .= " and a.requ_prefix_num LIKE '%$txt_search_common'";
			}
			else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
			{
				$sql_cond .= " and a.requ_prefix_num LIKE '%$txt_search_common%'";
			}
							
			if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
		}
 	}
	
	//echo $sql_cond;die; 
 	
	if($txt_search_by==1 ) //pi base
	{
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

		if($db_type==0)
		{
			if($itme_category_id==126) $itme_category_id="126";
			
			$sql = "select a.id as id,a.pi_number as wopi_number,b.lc_number as lc_number,a.pi_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source ,a.item_category_id as item_category
					from com_pi_master_details a left join com_btb_lc_master_details b on FIND_IN_SET(a.id,b.pi_id)
					where  
					a.item_category_id in($itme_category_id) and
					a.status_active=1 and a.is_deleted=0 and a.goods_rcv_status<>1 and
					a.importer_id=$company 
					$sql_cond $approval_status_cond $year_cond ORDER BY wopi_date DESC";//a.supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
		}
				
		if($db_type==1 || $db_type==2)
		{
			if($itme_category_id==126) $itme_category_id="126";
			//echo $itme_category_id;
			$sql = "select a.id as id,a.pi_number as wopi_number,b.lc_number as lc_number,a.pi_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source,a.item_category_id as item_category 
				from com_pi_master_details a left join com_btb_lc_pi c on a.id=c.pi_id left join com_btb_lc_master_details b on c.com_btb_lc_master_details_id=b.id
				where  
				a.item_category_id in($itme_category_id) and
				a.status_active=1 and a.is_deleted=0 and
				a.importer_id=$company 
				$sql_cond $approval_status_cond $year_cond ORDER BY wopi_date DESC";
		}
				
	}
	else if($txt_search_by==2) // wo base
	{
 		$sql = "select a.id, a.wo_number_prefix_num as wopi_number,' ' as lc_number, a.wo_date as wopi_date, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, b.item_category_id
				from wo_non_order_info_mst a, wo_non_order_info_dtls b
				where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and
				b.item_category_id in($itme_category_id) and a.pay_mode in (1,4) and a.company_name=$company
				$sql_cond $year_cond
				group by a.id,a.wo_number_prefix_num, a.wo_date, a.supplier_id, a.currency_id, a.source, b.item_category_id ORDER BY wopi_date DESC";
				//supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
	}
	else if($txt_search_by==7) // requisition base
	{
		 $approval_need=return_field_value("approval_need","approval_setup_mst a, approval_setup_dtls b","a.id = b.mst_id
			    and b.page_id = 13 and a.company_id = $company
			    and a.setup_date = ( select max(c.setup_date) from approval_setup_mst c where c.company_id = $company )");
	    if($approval_need==1)
	    {
	        $approval_cond = " and a.is_approved = '1'";
	    }else{
	        $approval_cond="";
	    }

 		$sql = "select a.id, a.requ_prefix_num as wopi_number, ' ' as lc_number, a.requisition_date as wopi_date, '' as supplier_id, a.cbo_currency as currency_id, a.source as source , b.item_category as item_category
				from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
				where a.id=b.mst_id and	a.status_active=1 and a.is_deleted=0 and b.item_category in($itme_category_id) and a.pay_mode=4 and a.company_id=$company $sql_cond $approval_cond $year_cond
				group by a.id, a.requ_prefix_num, a.requisition_date, a.cbo_currency, a.source, b.item_category ORDER BY wopi_date DESC"; 
	}
	//echo $sql;
	$result = sql_select($sql);
 	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(3=>$supplier_arr,4=>$currency,5=>$source,6=>$item_category);
	echo  create_list_view("list_view", "WO/PI No, LC ,Date, Supplier, Currency, Source,Item Category","50,100,100,200,100,120,120","900","260",0, $sql , "js_set_value", "id,wopi_number", "", 1, "0,0,0,supplier_id,currency_id,source,item_category", $arr, "wopi_number,lc_number,wopi_date,supplier_id,currency_id,source,item_category", "",'','0,0,3,0,0,0,0') ;	
	exit();	
}

//after select wo/pi number get form data here---------------------------//
if($action=="populate_data_from_wopi_popup")
{
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	
	if($receive_basis==1 ) //PI
	{
		if($db_type==0)
		{
 		$sql = "select b.id as id, a.pi_number as wopi_number, b.lc_number as lc_number,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source,'2' as pay_mode 
				from com_pi_master_details a left join com_btb_lc_master_details b on FIND_IN_SET(a.id,b.pi_id)
				where  
				a.item_category_id not in (1,2,3,5,6,7,12,13,14) and
				a.status_active=1 and a.is_deleted=0 and
				a.id=$wo_pi_ID";
		}
		if($db_type==1 || $db_type==2)
		{
			$sql ="select b.id as id, a.pi_number as wopi_number, b.lc_number as lc_number,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source,'2' as pay_mode 
				from com_pi_master_details a left join com_btb_lc_pi c on a.id=c.pi_id left join com_btb_lc_master_details b on c.com_btb_lc_master_details_id=b.id
				where  
				a.item_category_id not in (1,2,3,5,6,7,12,13,14) and
				a.status_active=1 and a.is_deleted=0 and
				a.id=$wo_pi_ID";
		}
	}
	else if($receive_basis==2) //WO
	{
 		$sql = "select id, wo_number as wopi_number,'' as lc_number,supplier_id as supplier_id,currency_id as currency_id,source as source,pay_mode  
				from wo_non_order_info_mst
				where status_active=1 and is_deleted=0 and id=$wo_pi_ID";
	}
	else if($receive_basis==7) //Requisition
	{
 		$sql = "select id, requ_no as wopi_number,'' as lc_number,requisition_date as wopi_date,'' as supplier_id, cbo_currency as currency_id,source as source,pay_mode as pay_mode 
				from inv_purchase_requisition_mst
				where status_active=1 and is_deleted=0 and pay_mode=4 and id=$wo_pi_ID";
	}
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		echo "$('#txt_wo_pi_req').val('".$row[csf("wopi_number")]."');\n";
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		//echo "check_exchange_rate()";
		echo "check_exchange_rate();\n";
		/*if($row[csf("currency_id")]==1)
		{
			echo "$('#txt_exchange_rate').val(1);\n";
		}*/
		echo "$('#cbo_source').val(".$row[csf("source")].");\n";
		echo "$('#cbo_pay_mode').val(".$row[csf("pay_mode")].");\n";
		echo "$('#txt_lc_no').val('".$row[csf("lc_number")]."');\n";
		if($row[csf("lc_number")]!="")
		{
			echo "$('#hidden_lc_id').val(".$row[csf("id")].");\n";
		}
	}
			echo "$('#cbo_company_name').attr('disabled',true);\n";
			echo "$('#cbo_receive_basis').attr('disabled',true);\n";


	exit();	
}

//right side product list create here--------------------//
if($action=="show_product_listview")
{ 
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$itme_category_id = $ex_data[2];
	
	//$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$item_grp_sql=sql_select("select id, item_name, conversion_factor from lib_item_group");
	$item_conversion_factor=array();
	foreach($item_grp_sql as $row)
	{
		$item_group_arr[$row[csf("id")]]=$row[csf("item_name")];
		$item_conversion_factor[$row[csf("id")]]=$row[csf("conversion_factor")];
	}
	$receive_return_sql = sql_select("select a.received_id, b.prod_id, c.item_group_id, sum(b.cons_quantity) as issue_qnty 
	from  inv_issue_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and b.transaction_type=3 and a.received_id>0 and a.entry_form=368 and b.transaction_type=3 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.received_id,b.prod_id, c.item_group_id");
	foreach($receive_return_sql as $row)
	{
		$receive_rtn_arr[$row[csf("received_id")]][$row[csf("prod_id")]]+=$row[csf("issue_qnty")]/$item_conversion_factor[$row[csf("item_group_id")]];
	}
	
	
	if($receive_basis==1) // pi basis
	{	
		$sql = "select a.id,c.id as prod_id,a.importer_id as company_id, a.supplier_id, c.item_number,c.product_name_details as product_name_details, c.item_size, c.item_category_id, c.item_group_id, sum(b.quantity) as quantity
		from com_pi_master_details a, com_pi_item_details b, product_details_master c
		where a.id=$wo_pi_ID and a.id=b.pi_id and b.item_prod_id=c.id and c.item_category_id in(106) and a.status_active=1 and b.status_active=1  
		group by a.id,a.importer_id,a.supplier_id,c.id,c.item_number,c.product_name_details, c.item_size, c.item_category_id,c.item_group_id"; 
		
		
		$receive_sql = sql_select("select a.id, a.booking_id, b.prod_id, sum(b.order_qnty) as receive_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=366 and a.receive_basis=1 and b.transaction_type=1 group by a.id, a.booking_id, b.prod_id");
		foreach($receive_sql as $row)
		{
			$receive_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]+=$row[csf("receive_qnty")]-$receive_rtn_arr[$row[csf("id")]][$row[csf("prod_id")]];
		}
		
	}  
	else if($receive_basis==2) // wo basis
	{
		$sql = "select  a.id ,c.id as prod_id,a.company_name as company_id,a.supplier_id, c.item_number, c.product_name_details, c.item_size, c.item_category_id, c.item_group_id, sum(b.supplier_order_quantity) as quantity
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c
		where a.id=b.mst_id and a.id=$wo_pi_ID and a.pay_mode in (1,4) and b.item_id=c.id and c.item_category_id in(106) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id,a.company_name,a.supplier_id, c.id, c.item_number,c.product_name_details, c.item_size, c.item_category_id,c.item_group_id";
		
		
		$receive_sql = sql_select("select a.id, a.booking_id, b.prod_id, sum(b.order_qnty) as receive_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=366 and a.receive_basis=2 and b.transaction_type=1 group by a.id, a.booking_id, b.prod_id");
		foreach($receive_sql as $row)
		{
			$receive_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]+=$row[csf("receive_qnty")]-$receive_rtn_arr[$row[csf("id")]][$row[csf("prod_id")]];
		}
	}
	else if($receive_basis==7) // requisition basis
	{
		$sql = "select  a.id,c.id as prod_id,a.company_id, '' as supplier_id, c.item_number, c.product_name_details, c.item_size, c.item_category_id, c.item_group_id, sum(b.quantity) as quantity
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
		where a.id=b.mst_id and b.product_id=c.id and c.item_category_id in(106) and a.id=$wo_pi_ID and a.pay_mode=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id,a.company_id,c.id,c.item_number,c.product_name_details, c.item_size, c.item_category_id,c.item_group_id";
		
		
		$receive_sql = sql_select("select a.id, a.booking_id, b.prod_id, sum(b.cons_quantity) as receive_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=366 and a.receive_basis=7 and b.transaction_type=1 group by a.id, a.booking_id, b.prod_id");
		foreach($receive_sql as $row)
		{
			$receive_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]+=$row[csf("receive_qnty")]-$receive_rtn_arr[$row[csf("id")]][$row[csf("prod_id")]];
		}
	}	
	//echo $sql;
	
	$result = sql_select($sql); 
	$i=1; 
	?>
    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="400" rules="all">
        	<thead>
            	<tr>
                	<th width="15">SL</th>
                    <th width="50">Item Number</th>
                    <th width="130">Product Name</th>
                    <th width="60">Item Group</th>
                    <th width="40">Wo/RQ /PI Qnty</th>
                    <th width="40">Receive Qnty</th>
                    <th >Balance</th>
                </tr>
            </thead>
            <tbody>
            	<? foreach($result as $row)
				{  
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";
					if($row[csf("item_category_id")]==126)
					{
						$productName=$row[csf("product_name_details")]." ".$row[csf("item_size")];
					}
					else
					{
						$productName=$row[csf("product_name_details")];
					}
					
					$balance_quantity=$row[csf("quantity")]-$receive_arr[$row[csf("id")]][$row[csf("prod_id")]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $receive_basis."**".$row[csf("id")]."**".$row[csf("company_id")]."**".$row[csf("supplier_id")]."**".$row[csf("quantity")]."**".$row[csf("prod_id")];?>","wo_pi_product_form_input","requires/emb_material_receive_controller")' style="cursor:pointer" >
                        <td><? echo $i; ?></td>
                        <td><p><? echo $row[csf("item_number")]; ?></p></td>
                        <td><p><? echo $productName; ?>&nbsp;</p></td>
                        <td ><p><? echo $item_group_arr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
                        <td align="right"><? echo number_format($receive_arr[$row[csf("id")]][$row[csf("prod_id")]],2); ?></td>
                        <td align="right"><? echo number_format($balance_quantity,2); ?></td>
					</tr>
					<? 
					$i++; 
				} 
				?>
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
	$wo_po_qnty = $ex_data[4];
	$product_id = $ex_data[5];
	if($receive_basis==1) // pi basis
	{	
  		/*$sql = "select a.importer_id as company_id, a.supplier_id,c.id,c.item_category_id as item_category, c.item_group_id as item_group, c.item_description as item_description, b.uom as cons_uom, b.rate, sum(b.quantity) as quantity, sum(c.current_stock) as global_stock  
		from com_pi_master_details a, com_pi_item_details b, product_details_master c
		where a.id=$wo_pi_ID and c.id=$product_id and a.id=b.pi_id and b.item_prod_id=c.id 
		group by c.id,c.item_category_id, c.item_group_id,c.item_description,a.importer_id, a.supplier_id,b.uom,b.rate";*/	
		/*new dev*/
		$sql = "select c.brand_name,c.origin,c.model, a.importer_id as company_id, a.supplier_id,c.id,c.item_category_id as item_category, c.item_group_id as item_group, c.item_description as item_description, b.uom as cons_uom, b.rate, sum(b.quantity) as quantity, sum(c.current_stock) as global_stock  
		from com_pi_master_details a, com_pi_item_details b, product_details_master c
		where a.id=$wo_pi_ID and c.id=$product_id and a.id=b.pi_id and b.item_prod_id=c.id 
		group by c.id,c.item_category_id, c.item_group_id,c.item_description,a.importer_id, a.supplier_id,b.uom,b.rate,c.brand_name,c.origin,c.model";	
	}
	else if($receive_basis==2) // wo basis
	{
 		/*$sql = "select a.company_name as company_id,a.supplier_id,c.id,c.item_category_id as item_category, c.item_group_id as item_group, c.item_description as item_description, b.uom as cons_uom, b.rate, sum(b.supplier_order_quantity) as quantity, sum(c.current_stock) as global_stock
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c
		where a.id=b.mst_id and a.id=$wo_pi_ID and c.id=$product_id and a.pay_mode in (1,4) and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by c.id,c.item_category_id, c.item_group_id,c.item_description,a.company_name,a.supplier_id,b.uom, b.rate";*/	
		/*new dev*/
		$sql = "select c.brand_name,c.origin,c.model, a.company_name as company_id,a.supplier_id,c.id,c.item_category_id as item_category, c.item_group_id as item_group, c.item_description as item_description, b.uom as cons_uom, b.rate, sum(b.supplier_order_quantity) as quantity, sum(c.current_stock) as global_stock
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c
		where a.id=b.mst_id and a.id=$wo_pi_ID and c.id=$product_id and a.pay_mode in (1,4) and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by c.id,c.item_category_id, c.item_group_id,c.item_description,a.company_name,a.supplier_id,b.uom, b.rate,c.brand_name,c.origin,c.model";
	}
	else if($receive_basis==7) // requisition basis
	{
		/*$sql = "select a.company_id, '' as supplier_id, c.id, a.item_category_id as item_category, c.item_group_id as item_group, c.item_description as item_description, b.cons_uom, b.rate, sum(b.quantity) as quantity, sum(c.current_stock) as global_stock
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
		where a.id=b.mst_id and b.product_id=c.id and a.id=$wo_pi_ID and c.id=$product_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by c.id,c.item_category_id, c.item_group_id,c.item_description,a.company_id,a.item_category_id,b.cons_uom, b.rate";*/
		/*new dev*/
		$sql = "select c.brand_name,c.origin,c.model,a.company_id, '' as supplier_id, c.id, b.item_category as item_category, c.item_group_id as item_group, c.item_description as item_description, b.cons_uom, b.rate, sum(b.quantity) as quantity, sum(c.current_stock) as global_stock
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
		where a.id=b.mst_id and b.product_id=c.id and a.id=$wo_pi_ID and c.id=$product_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by c.id,c.item_category_id, c.item_group_id,c.item_description,a.company_id,b.item_category,b.cons_uom, b.rate,c.brand_name,c.origin,c.model";
	}	
	//echo $sql;
	$result = sql_select($sql); 
	foreach($result as $row)
	{
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";	 
	 	echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
		echo "load_drop_down( 'requires/emb_material_receive_controller',".$row[csf("item_category")].", 'load_drop_down_itemgroup', 'item_group_td' );";		
		echo "$('#cbo_item_group').val(".$row[csf("item_group")].");\n";
		echo "$('#txt_item_desc').val('".$row[csf("item_description")]."');\n";
		echo "$('#current_prod_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_glob_stock').val('".$row[csf("global_stock")]."');\n";
		//echo "$('#txt_serial_no').val(".$row[csf("")].");\n";
		echo "$('#cbo_uom').val(".$row[csf("cons_uom")].");\n";
 		echo "$('#txt_rate').val('".$row[csf("rate")]."');\n";

 		echo "$('#txt_brand').val('".$row[csf("brand_name")]."');\n";//new dev
		echo "$('#cbo_origin').val('".$row[csf("origin")]."');\n";//new dev
		echo "$('#txt_model').val('".$row[csf("model")]."');\n";//new dev
		if($receive_basis==7)
		{
			$totalRcvQnty = return_field_value("sum(a.cons_quantity) as bal","inv_transaction a, inv_receive_master b ","a.mst_id=b.id and b.booking_id=$wo_pi_ID and a.prod_id=".$row[csf("id")]." and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
		}
		else
		{
			$totalRcvQnty = return_field_value("sum(a.order_qnty) as bal","inv_transaction a, inv_receive_master b ","a.mst_id=b.id and b.booking_id=$wo_pi_ID and a.prod_id=".$row[csf("id")]." and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
		}
 		
		$orderQnty = round($wo_po_qnty,2)-round($totalRcvQnty,2);
		echo "$('#txt_order_qty').val('".$orderQnty."');\n";
		
		echo "$('#cbo_item_category').attr('disabled',true);\n";
		echo "$('#cbo_item_group').attr('disabled',true);\n";
		echo "$('#txt_item_desc').attr('disabled',true);\n";
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
                   		<!-- Hidden field here   -->
                        <input type="hidden" id="hidden_tbl_id" value="" />
                        <input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number" />
                        <!-- -END   -->
                    </th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?  
                            $search_by_arr=array(0=>'LC Number',1=>'Supplier Name');
							$dd="change_search_event(this.value, '0*1', '0*select id, supplier_name from lib_supplier', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="180" align="center" id="search_by_td">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lc_search_list_view', 'search_div', 'emb_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where lc_number LIKE '%$search_string%' and importer_id=$company and item_category_id=126 and is_deleted=0 and status_active=1";
	} 
	else if($cbo_search_by==1 && $txt_search_common!="") //supplier
	{
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where supplier_id='$search_string' and importer_id=$company and item_category_id=126 and is_deleted=0 and status_active=1";
	} 
	else
	{
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
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
	$category = $ex_data[3];
	$group = $ex_data[4];
	
	
	if($db_type==0)
	{
		$sql="select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category=$category and item_group=$group and status_active=1 and is_deleted=0 order by id limit 1";
	}
	else
	{
		$sql="select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category=$category and item_group=$group and status_active=1 and is_deleted=0 and rownum <= 2 order by id desc";
	}
	//echo $sql;
	
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

if($action=="serial_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
 	if(str_replace("'","",$serialString)!="")
	{
		$mainEx = explode("**",str_replace("'","",$serialString)); 
		$serialArr = explode(",",$mainEx[0]);
		$qntyArr = explode(",",$mainEx[1]);
	}
	
	?>
	<script>
	function add_break_down_tr(i) 
	 {
		var row_num=$('#tbl_serial tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;			
			$("#tbl_serial tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }              
				});  
			  }).end().appendTo("#tbl_serial"); 
			$('#txtSerialNo_'+i).val('');
			$('#txtQuantity_'+i).removeClass("class").addClass("class","text_boxes_numeric");
   			$('#btnIncrease_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#btnDecrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#txtSerialNo_'+i).removeAttr("onBlur").attr("onBlur","fn_check_serial("+i+")");	
 		}
	}
	
	function fn_deletebreak_down_tr(rowNo) 
	{   
		var numRow = $('table#tbl_serial tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_serial tbody tr:last').remove();
		}
 	}
	
	function fnClosed() 
	{   
		var numRow = $('table#tbl_serial tr').length;  
		var serialS="";
		var qntyS="";
		for(var i=1;i<numRow;i++)
		{
 			if(i*1>1){ serialS+=","; qntyS+=","; }
			serialS+=$("#txtSerialNo_"+i).val();
			qntyS+=$("#txtQuantity_"+i).val();
			if( form_validation('txtSerialNo_'+i,'Serial')==false )
			{
				return;
			}
		}
		var txtString = serialS;//+"**"+qntyS;
		$("#txt_string").val(txtString);
		$("#txt_qty").val(qntyS);
		parent.emailwindow.hide();
 	}
	
	function fn_check_serial(rowNo) 
	{
		if(rowNo!=1)
		{
			var table_length = $('#tbl_serial tr').length;
			for(var i=1; i<=rowNo-1; i++)
			{
				if(($('#txtSerialNo_'+i).val()*1)==($('#txtSerialNo_'+rowNo).val()*1))
				{
					$('#txtSerialNo_'+rowNo).val("");
				}
			}
		}
 	}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
		<table width="450" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_serial" >
				<thead>
					<tr>                	 
						<th width="260" class="must_entry_caption">Serial No</th>
                        <th width="80">Quantity</th>
 						<th width="120">Action</th> 
					</tr>
				</thead>
				<tbody>
                	<?
 						$chkNo = sizeof($serialArr);
						if(!empty($serialArr[0]))
						{ 
 							for($j=1;$j<=$chkNo;$j++)
							{
								?>
								<tr>
									<td>
										<input type="text" id="txtSerialNo_<? echo $j;?>" name="txtSerialNo_<? echo $j;?>" style="width:250px" class="text_boxes" value="<? echo $serialArr[$j-1]; ?>" onBlur="fn_check_serial(<? echo $j;?>)" />
									</td>
									<td>
										<input type="text" id="txtQuantity_<? echo $j;?>" name="txtQuantity_<? echo $j;?>" style="width:70px" class="text_boxes_numeric" value="<? echo $qntyArr[$j-1]; ?>" disabled />
									</td>
									<td>				
										<input type="button" id="btnIncrease_<? echo $j;?>" name="btnIncrease_<? echo $j;?>" class="formbutton" style="width:40px" onClick="add_break_down_tr(<? echo $j;?>)" value="+" />
										<input type="button" id="btnDecrease_<? echo $j;?>" name="btnDecrease_<? echo $j;?>" class="formbutton" style="width:40px" onClick="fn_deletebreak_down_tr(<? echo $j;?>)" value="-" />
									</td> 
								</tr> 
					<?	
							}
 						}
						else
						{
					?>	
                            <tr>
                                <td>
                                    <input type="text" id="txtSerialNo_1" name="txtSerialNo_1" style="width:250px" class="text_boxes" value=""  onBlur="fn_check_serial(1)" />
                                </td>
                                <td>
                                    <input type="text" id="txtQuantity_1" name="txtQuantity_1" style="width:70px" class="text_boxes_numeric" value="1" disabled />
                                </td>
                                <td>				
                                    <input type="button" id="btnIncrease_1" name="btnIncrease_1" class="formbutton" style="width:40px" onClick="add_break_down_tr(1)" value="+" />
                                    <input type="button" id="btnDecrease_1" name="btnDecrease_1" class="formbutton" style="width:40px" onClick="fn_deletebreak_down_tr(1)" value="-" />
                                </td> 
                            </tr> 
                    <? } ?>
				</tbody>         
			</table>  
            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fnClosed()" /></div>  
            <!-- Hidden field here  -->
			<input type="hidden" id="txt_string" value="" />
            <input type="hidden" id="txt_qty" value="" />				 
			<!-- -END   --> 
			</form>
	   </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action=="item_description_popup")
{
	echo load_html_head_contents("Item popup", "../../../", 1, 1,'','1','');	
	extract($_REQUEST);
?>
<script> 
	function js_set_value(item_description)
	{
		var splitArr = item_description.split("_");
		$("#product_id_td").val(splitArr[0]);
		$("#item_description_td").val(splitArr[1]);
		$("#current_stock").val(splitArr[2]);

		$("#brand_name").val(splitArr[3]);
                $("#origin").val(splitArr[4]);
                 $("#model").val(splitArr[5]);
		parent.emailwindow.hide(); 
	} 
</script>
</head>	
<body>
	<div align="center" style="width:100%" >
	<form name="item_popup_1"  id="item_popup_1">
      <?
		  $entry_cond="";
		  if(str_replace("'","",$item_category)==126) $entry_cond="and entry_form=366";
 		  /*$sql="select id, item_code, item_description, item_size,current_stock from product_details_master where status_active=1 and is_deleted=0 and company_id=$company_id and item_category_id=$item_category and item_group_id=$item_group $entry_cond";*/
 		 /* new dev*/
 		 $sql="select id, item_code, item_number, item_description, item_size,current_stock,brand_name,origin,model from product_details_master where status_active=1 and is_deleted=0 and company_id=$company_id and item_category_id=$item_category and item_group_id=$item_group $entry_cond";
		  //echo $sql;
		  
 		  /*echo  create_list_view ( "list_view","Product Id,Item Account,Item Code,Item Description,Item Size", "50,110,70,200","590","250",0, $sql, "js_set_value", "id,item_description,current_stock", "", 1, "0,0,0,0,0", $arr, "id,item_account,item_code,item_description,item_size", "", 'setFilterGrid("list_view",-1);'); */
 		 /* new dev*/
 		 echo  create_list_view ( "list_view","Product Id,Item Account,Item Code,Item Number,Item Description,Item Size", "50,110,70,70,130","590","250",0, $sql, "js_set_value", "id,item_description,current_stock,brand_name,origin,model", "", 1, "0,0,0,0,0,0", $arr, "id,item_account,item_code,item_number,item_description,item_size", "", 'setFilterGrid("list_view",-1);'); 
    ?>
     <input type="hidden" id="item_description_td" />
     <input type="hidden" id="product_id_td" />
     <input type="hidden" id="current_stock" />

     <input type="hidden" id="brand_name" />
     <input type="hidden" id="origin" />
     <input type="hidden" id="model" />

   </form>
   </div>  
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>                                  
<? 		
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if(str_replace("'","",$txt_amount)*1 <= 0)
	{
		echo "20**Receive Amount Not Allow Less Than Or Equal Zero";disconnect($con);die;
	}
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		
		$variable_set_invent=return_field_value("user_given_code_status","variable_settings_inventory","company_name=$cbo_company_name and variable_list=19 and item_category_id=$cbo_item_category","user_given_code_status");
		
		if($variable_set_invent==1)
		{
			$challan_no=str_replace("'","",$txt_challan_no);
			if($challan_no!="")
			{
				$variable_set_invent=return_field_value("a.id as id"," inv_gate_in_mst a,  inv_gate_in_dtl b","a.id=b.mst_id and a.company_id=$cbo_company_name and a.challan_no='$challan_no' and b.item_category_id=$cbo_item_category  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
				if(empty($variable_set_invent))
				{
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";disconnect($con); die;
				}
				
			}
		}
		//---------------End Check Receive control on Gate Entry---------------------------//
		
		//---------------Check Meterial Over Receive control Start---------------------------//
		//$variable_over_rcv_percent=return_field_value("over_rcv_percent","variable_inv_ile_standard"," company_name = $cbo_company_name and category=$cbo_item_category and variable_list = 23","over_rcv_percent");
		//echo "10**$variable_over_rcv_percent";disconnect($con); die; 
		$rcv_basis=str_replace("'","",$cbo_receive_basis);
		
		if($rcv_basis !=4 && $rcv_basis !=6)
		{
			//txt_receive_qty current_prod_id
			$wo_pi_req_id=str_replace("'","",$txt_wo_pi_req_id);
			$cr_prod_id=str_replace("'","",$current_prod_id);
			$receive_return_sql = sql_select("select a.received_id, b.prod_id, c.item_group_id, sum(b.cons_quantity) as issue_qnty 
			from  inv_issue_master a, inv_transaction b, product_details_master c 
			where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and b.transaction_type=3 and a.received_id>0 and a.entry_form=368 and b.transaction_type=3 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id=$cr_prod_id group by a.received_id, b.prod_id, c.item_group_id");
			foreach($receive_return_sql as $row)
			{
				$receive_rtn_arr[$row[csf("received_id")]]+=$row[csf("issue_qnty")]/$item_conversion_factor[$row[csf("item_group_id")]];
			}
			$prev_rcv_sql=sql_select(" select a.id as mst_id, sum(b.order_qnty) as qnty from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.entry_form = 366 and b.transaction_type=1 and a.receive_basis = $rcv_basis and a.booking_id = $wo_pi_req_id and b.item_category=$cbo_item_category and b.prod_id=$cr_prod_id and a.status_active=1 and b.status_active=1 group by a.id");
			$prev_rcv_qnty=0;
			foreach($prev_rcv_sql as $row)
			{
				$prev_rcv_qnty+=$row[csf("qnty")]-$receive_rtn_arr[$row[csf("mst_id")]];
			}
			
			$current_receive_qty=str_replace("'","",$txt_receive_qty);
			$tot_qnty=$prev_rcv_qnty+$current_receive_qty;
			//echo "30** $prev_rcv_qnty.";disconnect($con); die;
			if($rcv_basis==1)
			{
				$wo_pi_req_sql=sql_select(" select sum(b.quantity) as quantity from com_pi_item_details b where b.status_active=1 and b.pi_id=$wo_pi_req_id and b.item_prod_id=$cr_prod_id");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
			}
			else if($rcv_basis==2)
			{
				$wo_pi_req_sql=sql_select(" select sum(b.supplier_order_quantity) as quantity from wo_non_order_info_dtls b where b.status_active=1 and b.mst_id=$wo_pi_req_id and b.item_id=$cr_prod_id");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
			}
			else
			{
				$wo_pi_req_sql=sql_select(" select sum(b.quantity) as quantity from inv_purchase_requisition_dtls b where b.status_active=1 and b.mst_id=$wo_pi_req_id and b.product_id=$cr_prod_id");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
			}
			
			$allow_qnty=$wo_pi_req_qnty+($wo_pi_req_qnty/100);
			if($tot_qnty>$allow_qnty)
			{
				echo "30** MRR Quantity Not Allow More Then PI/Wo/Req Allowed Quantity.";disconnect($con); die;
			}
			
			
		}
		
		//---------------Check Meterial Over Receive control End---------------------------//
		
		
		
		
 		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id","inv_receive_master a, inv_transaction b","a.id=b.mst_id and a.id=$hidden_mrr_id and b.prod_id=$current_prod_id and b.transaction_type=1  and a.status_active=1 and b.status_active=1"); 
		if($duplicate==1) 
		{
			echo "20**Duplicate Product is Not Allow in Same MRR Number.";
			disconnect($con); die;
		}
		//------------------------------Check product END---------------------------------------//
				
		//---------------Check Receive date with Last Transaction date-------------//
		$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$current_prod_id and store_id=$cbo_store_name and status_active = 1 and transaction_type in(2,3,6)", "max_date");      
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
			if ($receive_date < $max_transaction_date) 
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				die;
			}
		} 

		//---------------Check Last Transaction date End---------------------------//



		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty from product_details_master where id=$current_prod_id");
		$presentStock=$presentStockValue=$presentAvgRate=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock		=$result[csf("current_stock")];
			$presentStockValue	=$result[csf("stock_value")];
			$presentAvgRate		=$result[csf("avg_rate_per_unit")];
			$product_name_details=$result[csf("product_name_details")];	
			$available_qnty		=$result[csf("available_qnty")];	
		}		 
		//----------------Check Product ID END---------------------//
		if($txt_store_sl_no=="") $txt_store_sl_no="''";

		$txt_challan_date=$txt_bill_date=$txt_gate_entry_date=$txt_addi_rcvd_date="";
		/*if(str_replace("'","",$cbo_item_category)==16)
		{*/
		$addi_info_arr = explode("_", str_replace("'","",$txt_addi_info));
		
		$txt_book_no = $addi_info_arr[0];
		$txt_challan_date = $addi_info_arr[1];
		$txt_bill_no = $addi_info_arr[2];
		$txt_bill_date = $addi_info_arr[3];
		$cbo_purchaser_name = $addi_info_arr[4];
		$cbo_carried_by = $addi_info_arr[5];
		$cbo_qc_check_by = $addi_info_arr[6];
		$cbo_receive_by = $addi_info_arr[7];
		$cbo_gate_entry_by = $addi_info_arr[8];
		$txt_gate_entry_date = $addi_info_arr[9];
		$txt_addi_rcvd_date = $addi_info_arr[10];
		$txt_gate_entry_no = $addi_info_arr[11];
		$txt_store_sl_no = "'".$addi_info_arr[12]."'";

		if($db_type == 0)
		{
			$txt_challan_date= change_date_format($txt_challan_date, 'yyyy-mm-dd');
			$txt_bill_date = change_date_format($txt_bill_date, 'yyyy-mm-dd');
			$txt_gate_entry_date = change_date_format($txt_gate_entry_date, 'yyyy-mm-dd');
			$txt_addi_rcvd_date = change_date_format($txt_addi_rcvd_date, 'yyyy-mm-dd');
		}else{
			$txt_challan_date = change_date_format($txt_challan_date, '', '', 1);
			$txt_bill_date = change_date_format($txt_bill_date, '', '', 1);
			$txt_gate_entry_date = change_date_format($txt_gate_entry_date, '', '', 1);
			$txt_addi_rcvd_date = change_date_format($txt_addi_rcvd_date, '', '', 1);
		}

		/*}*/
		
		if(str_replace("'","",$txt_mrr_no)!="")
		{
			$new_recv_number[0] = str_replace("'","",$txt_mrr_no);
			$id=str_replace("'","",$hidden_mrr_id);
			//master table UPDATE here START----------------------//		
			$field_array1="receive_basis*receive_date*booking_id*challan_no*store_id*exchange_rate*currency_id*supplier_id*lc_no*pay_mode*source*supplier_referance*remarks*store_sl_no*rcvd_book_no*addi_challan_date*bill_no*bill_date*purchaser_name*carried_by*qc_check_by*receive_by*gate_entry_by*gate_entry_date*addi_rcvd_date*gate_entry_no*updated_by*update_date";
			$data_array1="".$cbo_receive_basis."*".$txt_receive_date."*".$txt_wo_pi_req_id."*".$txt_challan_no."*".$cbo_store_name."*".$txt_exchange_rate."*".$cbo_currency."*".$cbo_supplier."*".$hidden_lc_id."*".$cbo_pay_mode."*".$cbo_source."*".$txt_sup_ref."*".$txt_remarks."*".$txt_store_sl_no."*'".$txt_book_no."'*'".$txt_challan_date."'*'".$txt_bill_no."'*'".$txt_bill_date."'*'".$cbo_purchaser_name."'*'".$cbo_carried_by."'*'".$cbo_qc_check_by."'*'".$cbo_receive_by."'*'".$cbo_gate_entry_by."'*'".$txt_gate_entry_date."'*'".$txt_addi_rcvd_date."'*'".$txt_gate_entry_no."'*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;die;
			//$rID=sql_update("inv_receive_master",$field_array,$data_array,"id",$id,1);	
			//master table UPDATE here END---------------------------------------// 
		}
		else  	
		{	
			//master table entry here START---------------------------------------//txt_remarks		
			//$id=return_next_id("id", "inv_receive_master", 1);		
			//$new_recv_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GIR', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_name and entry_form='20' $mrr_date_check order by id DESC ", "recv_number_prefix", "recv_number_prefix_num" ));
			
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$cbo_company_name),'EBMR',366,date("Y",time()))); 
			
			$field_array1="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, company_id, receive_basis, receive_date, booking_id, challan_no, store_id, exchange_rate, currency_id, supplier_id, lc_no, pay_mode, source, supplier_referance, remarks,  store_sl_no, rcvd_book_no,addi_challan_date,bill_no,bill_date,purchaser_name,carried_by,qc_check_by,receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date,gate_entry_no, inserted_by, insert_date";
			$data_array1="(".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',366,".$cbo_company_name.",".$cbo_receive_basis.",".$txt_receive_date.",".$txt_wo_pi_req_id.",".$txt_challan_no.",".$cbo_store_name.",".$txt_exchange_rate.",".$cbo_currency.",".$cbo_supplier.",".$hidden_lc_id.",".$cbo_pay_mode.",".$cbo_source.",".$txt_sup_ref.",".$txt_remarks.",".$txt_store_sl_no.",'".$txt_book_no."','".$txt_challan_date."','".$txt_bill_no."','".$txt_bill_date."','".$cbo_purchaser_name."','".$cbo_carried_by."','".$cbo_qc_check_by."','".$cbo_receive_by."','".$cbo_gate_entry_by."','".$txt_gate_entry_date."','".$txt_addi_rcvd_date."','".$txt_gate_entry_no."','".$user_id."','".$pc_date_time."')";
			//echo "20**".$field_array."<br>".$data_array;die;
			//$rID=sql_insert("inv_receive_master",$field_array,$data_array,1); 		
			//master table entry here END---------------------------------------// 
		}

		/*if(str_replace("'","",$cbo_item_category)==22)
		{
			if(str_replace("'","",$txt_mrr_no)!="")
			{
				$new_recv_number[0] = str_replace("'","",$txt_mrr_no);
				$id=str_replace("'","",$hidden_mrr_id);
				//master table UPDATE here START----------------------//		
				$field_array1="receive_basis*receive_date*receive_purpose*booking_id*challan_no*loan_party*store_id*exchange_rate*currency_id*supplier_id*lc_no*pay_mode*source*supplier_referance*remarks*store_sl_no*updated_by*update_date";
				$data_array1="".$cbo_receive_basis."*".$txt_receive_date."*".$cbo_receive_purpose."*".$txt_wo_pi_req_id."*".$txt_challan_no."*".$cbo_loan_party."*".$cbo_store_name."*".$txt_exchange_rate."*".$cbo_currency."*".$cbo_supplier."*".$hidden_lc_id."*".$cbo_pay_mode."*".$cbo_source."*".$txt_sup_ref."*".$txt_remarks."*".$txt_store_sl_no."*'".$user_id."'*'".$pc_date_time."'";
				//echo $field_array."<br>".$data_array;die;
				//$rID=sql_update("inv_receive_master",$field_array,$data_array,"id",$id,1);	
				//master table UPDATE here END---------------------------------------// 
			}
			else  	
			{	
				//master table entry here START---------------------------------------//txt_remarks		
				//$id=return_next_id("id", "inv_receive_master", 1);		
				//$new_recv_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GIR', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_name and entry_form='20' $mrr_date_check order by id DESC ", "recv_number_prefix", "recv_number_prefix_num" ));
				
				$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
				$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$cbo_company_name),'GIR',20,date("Y",time())));  
				
				$field_array1="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, company_id, receive_basis, receive_date, receive_purpose, booking_id, challan_no, loan_party, store_id, exchange_rate, currency_id, supplier_id, lc_no, pay_mode, source, supplier_referance, remarks,store_sl_no,inserted_by, insert_date";
				$data_array1="(".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',20,".$cbo_company_name.",".$cbo_receive_basis.",".$txt_receive_date.",".$cbo_receive_purpose.",".$txt_wo_pi_req_id.",".$txt_challan_no.",".$cbo_loan_party.",".$cbo_store_name.",".$txt_exchange_rate.",".$cbo_currency.",".$cbo_supplier.",".$hidden_lc_id.",".$cbo_pay_mode.",".$cbo_source.",".$txt_sup_ref.",".$txt_remarks.",".$txt_store_sl_no.",'".$user_id."','".$pc_date_time."')";
				//echo "20**".$field_array1."<br>".$data_array1;//die;
				//$rID=sql_insert("inv_receive_master",$field_array,$data_array,1); 		
				//master table entry here END---------------------------------------// 
			}
		}
		else
		{
			$txt_challan_date=$txt_bill_date=$txt_gate_entry_date=$txt_addi_rcvd_date="";
			if(str_replace("'","",$cbo_item_category)==16)
			{
			$addi_info_arr = explode("_", str_replace("'","",$txt_addi_info));

			$txt_book_no = $addi_info_arr[0];
			$txt_challan_date = $addi_info_arr[1];
			$txt_bill_no = $addi_info_arr[2];
			$txt_bill_date = $addi_info_arr[3];
			$cbo_purchaser_name = $addi_info_arr[4];
			$cbo_carried_by = $addi_info_arr[5];
			$cbo_qc_check_by = $addi_info_arr[6];
			$cbo_receive_by = $addi_info_arr[7];
			$cbo_gate_entry_by = $addi_info_arr[8];
			$txt_gate_entry_date = $addi_info_arr[9];
			$txt_addi_rcvd_date = $addi_info_arr[10];
			$txt_gate_entry_no = $addi_info_arr[11];
			$txt_store_sl_no = "'".$addi_info_arr[12]."'";

			if($db_type == 0)
			{
				$txt_challan_date= change_date_format($txt_challan_date, 'yyyy-mm-dd');
				$txt_bill_date = change_date_format($txt_bill_date, 'yyyy-mm-dd');
				$txt_gate_entry_date = change_date_format($txt_gate_entry_date, 'yyyy-mm-dd');
				$txt_addi_rcvd_date = change_date_format($txt_addi_rcvd_date, 'yyyy-mm-dd');
			}else{
				$txt_challan_date = change_date_format($txt_challan_date, '', '', 1);
				$txt_bill_date = change_date_format($txt_bill_date, '', '', 1);
				$txt_gate_entry_date = change_date_format($txt_gate_entry_date, '', '', 1);
				$txt_addi_rcvd_date = change_date_format($txt_addi_rcvd_date, '', '', 1);
			}

			}
			
			if(str_replace("'","",$txt_mrr_no)!="")
			{
				$new_recv_number[0] = str_replace("'","",$txt_mrr_no);
				$id=str_replace("'","",$hidden_mrr_id);
				//master table UPDATE here START----------------------//		
				$field_array1="receive_basis*receive_date*booking_id*challan_no*store_id*exchange_rate*currency_id*supplier_id*lc_no*pay_mode*source*supplier_referance*remarks*store_sl_no*rcvd_book_no*addi_challan_date*bill_no*bill_date*purchaser_name*carried_by*qc_check_by*receive_by*gate_entry_by*gate_entry_date*addi_rcvd_date*gate_entry_no*updated_by*update_date";
				$data_array1="".$cbo_receive_basis."*".$txt_receive_date."*".$txt_wo_pi_req_id."*".$txt_challan_no."*".$cbo_store_name."*".$txt_exchange_rate."*".$cbo_currency."*".$cbo_supplier."*".$hidden_lc_id."*".$cbo_pay_mode."*".$cbo_source."*".$txt_sup_ref."*".$txt_remarks."*".$txt_store_sl_no."*'".$txt_book_no."'*'".$txt_challan_date."'*'".$txt_bill_no."'*'".$txt_bill_date."'*'".$cbo_purchaser_name."'*'".$cbo_carried_by."'*'".$cbo_qc_check_by."'*'".$cbo_receive_by."'*'".$cbo_gate_entry_by."'*'".$txt_gate_entry_date."'*'".$txt_addi_rcvd_date."'*'".$txt_gate_entry_no."'*'".$user_id."'*'".$pc_date_time."'";
				//echo $field_array."<br>".$data_array;die;
				//$rID=sql_update("inv_receive_master",$field_array,$data_array,"id",$id,1);	
				//master table UPDATE here END---------------------------------------// 
			}
			else  	
			{	
				//master table entry here START---------------------------------------//txt_remarks		
				//$id=return_next_id("id", "inv_receive_master", 1);		
				//$new_recv_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GIR', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_name and entry_form='20' $mrr_date_check order by id DESC ", "recv_number_prefix", "recv_number_prefix_num" ));
				
				$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
				$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$cbo_company_name),'GIR',20,date("Y",time()))); 
				
				$field_array1="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, company_id, receive_basis, receive_date, booking_id, challan_no, store_id, exchange_rate, currency_id, supplier_id, lc_no, pay_mode, source, supplier_referance, remarks,  store_sl_no, rcvd_book_no,addi_challan_date,bill_no,bill_date,purchaser_name,carried_by,qc_check_by,receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date,gate_entry_no, inserted_by, insert_date";
				$data_array1="(".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',20,".$cbo_company_name.",".$cbo_receive_basis.",".$txt_receive_date.",".$txt_wo_pi_req_id.",".$txt_challan_no.",".$cbo_store_name.",".$txt_exchange_rate.",".$cbo_currency.",".$cbo_supplier.",".$hidden_lc_id.",".$cbo_pay_mode.",".$cbo_source.",".$txt_sup_ref.",".$txt_remarks.",".$txt_store_sl_no.",'".$txt_book_no."','".$txt_challan_date."','".$txt_bill_no."','".$txt_bill_date."','".$cbo_purchaser_name."','".$cbo_carried_by."','".$cbo_qc_check_by."','".$cbo_receive_by."','".$cbo_gate_entry_by."','".$txt_gate_entry_date."','".$txt_addi_rcvd_date."','".$txt_gate_entry_no."','".$user_id."','".$pc_date_time."')";
				//echo "20**".$field_array."<br>".$data_array;die;
				//$rID=sql_insert("inv_receive_master",$field_array,$data_array,1); 		
				//master table entry here END---------------------------------------// 
			}
		}*/
		
		//echo "10**insert into inv_receive_master ($field_array1) values $data_array1";die;
		//details table entry here START-----------------------------------//
		//echo $cbo_item_group;die;		
		$rate = str_replace("'","",$txt_rate);
		$txt_ile = str_replace("'","",$txt_ile);
		$txt_receive_qty = str_replace("'","",$txt_receive_qty);
		$ile = ($txt_ile/$rate)*100; // ile cost to ile
		$ile_cost = str_replace("'","",$txt_ile); //ile cost = (ile/100)*rate
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		if($db_type==0)
		{
			$concattS = explode(",",return_field_value(" concat(trim_uom,',',conversion_factor) as cons_uom","lib_item_group","id=$cbo_item_group","cons_uom")); 
		}
		if($db_type==2)
		{
			$concattS = explode(",",return_field_value("(trim_uom || ',' ||conversion_factor) as cons_uom","lib_item_group","id=$cbo_item_group","cons_uom")); 
		}
		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];
		$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
 		$cons_rate = number_format($domestic_rate,$dec_place[3],".","");//number_format($rate*$exchange_rate,$dec_place[3],".","");
		$con_quantity = $conversion_factor*$txt_receive_qty;
		$con_amount = $cons_rate*$con_quantity;
		$con_ile = $ile/$conversion_factor;//($ile/$domestic_rate)*100;
		$con_ile_cost = ($con_ile/100)*$cons_rate;
		
		if($ile_cost=="") $ile_cost =0;
		if($cons_uom=="") $cons_uom =0;
		if($con_ile=="") $con_ile =0;
			
		//$dtlsid = return_next_id("id", "inv_transaction", 1);		 
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		
		$field_array_trams = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,cons_rate,cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,room,rack,self,bin_box,expire_date,remarks,inserted_by,insert_date";
 		$data_array_trans = "(".$dtlsid.",".$id.",".$cbo_receive_basis.",".$txt_wo_pi_req_id.",".$cbo_company_name.",".$cbo_supplier.",".$current_prod_id.",".$cbo_item_category.",1,".$txt_receive_date.",".$cbo_store_name.",".$cbo_uom.",".$txt_receive_qty.",".$txt_rate.",".$ile.",".$ile_cost.",".$txt_amount.",".$cons_uom.",".$con_quantity.",".$cons_rate.",".$con_ile.",".$con_ile_cost.",".$con_amount.",".$con_quantity.",".$con_amount.",".$txt_room.",".$txt_rack.",".$txt_self.",".$txt_binbox.",".$txt_warranty_date.",".$txt_referance.",'".$user_id."','".$pc_date_time."')";
		//echo "INSERT INTO inv_transaction (".$field_array.") VALUES ".$data_array.""; die;
		//echo "**".$field_array."<br>".$data_array;die;
		//$dtlsrID = sql_insert("inv_transaction",$field_array_trams,$data_array_trans,1);
		//yarn details table entry here END-----------------------------------//
		
		//product master table data UPDATE START----------------------------------------------------------//	
		$stock_value 	= $domestic_rate*$con_quantity;
  		$currentStock   = $presentStock+$con_quantity;
		$available_qnty = $available_qnty+$con_quantity;
		$StockValue	 = $presentStockValue+$stock_value;
		$avgRate		= $StockValue/$currentStock;
 		$field_array3="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
 		$data_array3="".number_format($avgRate,$dec_place[3],".","")."*".$con_quantity."*".$currentStock."*".number_format($StockValue,$dec_place[4],".","")."*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'";
		//echo "**".$field_array."<br>".$data_array;die;
		//$prodUpdate = sql_update("product_details_master",$field_array,$data_array,"id",$current_prod_id,1);
		
				

		//------------------ product_details_master END---------------------------------------------------//
		
		//serial no save---------------
 		//$serialID = return_next_id("id", "inv_serial_no_details", 1);		 
 		$serial_field_array = "id,recv_trans_id,prod_id,serial_no,is_issued,inserted_by,insert_date,serial_qty";
		$expSerial = explode(",",str_replace("'","",$txt_serial_no));
		$expSerialqty = explode(",",str_replace("'","",$txt_serial_qty));
		//print_r($current_prod_id);die;
		$serial_data_array=="";
		for($i=0;$i<count($expSerial);$i++)
		{
			$serialID = return_next_id_by_sequence("INV_SERIAL_NO_DETAILS_PK_SEQ", "inv_serial_no_details", $con);
 			if($i>0){ $serial_data_array .=","; }
			$serial_data_array .= "(".$serialID.",".$dtlsid.",".$current_prod_id.",'".$expSerial[$i]."',0,'".$user_id."','".$pc_date_time."','".$expSerialqty[$i]."')";
			//$serialID++;
		}
		
		
		//echo $field_array1;die;
		//for test 
		if(str_replace("'","",$txt_mrr_no)!="")
		{
			$rID=sql_update("inv_receive_master",$field_array1,$data_array1,"id",$id,1);	
		}
		else  	
		{	
			$rID=sql_insert("inv_receive_master",$field_array1,$data_array1,1); 		
		}
		$dtlsrID = sql_insert("inv_transaction",$field_array_trams,$data_array_trans,1);
		$prodUpdate = sql_update("product_details_master",$field_array3,$data_array3,"id",$current_prod_id,1);
		$serial_dtlsrID=1;
		if(str_replace("'","",$txt_serial_no)!="")
		{
			$serial_dtlsrID = sql_insert("inv_serial_no_details",$serial_field_array,$serial_data_array,1);
		}
		
		//echo "10** insert into inv_receive_master ($field_array1) values $data_array1";die;
		//echo "10** $rID && $dtlsrID && $prodUpdate && $serial_dtlsrID" ;die;
		
		if($db_type==0)
		{
			if( $rID && $dtlsrID && $prodUpdate && $serial_dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_recv_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_recv_number[0]."**".$id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if( $rID && $dtlsrID && $prodUpdate && $serial_dtlsrID)
			{
				oci_commit($con); 
				echo "0**".$new_recv_number[0]."**".$id;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$new_recv_number[0]."**".$id;
			}
		}
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$hidden_mrr_id=str_replace("'","",$hidden_mrr_id);
		//table lock here 
		if( str_replace("'","",$update_id) == "" )
		{
			echo "15";disconnect($con); die;
		}
		
		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		if($variable_set_invent==1)
		{
			$challan_no=str_replace("'","",$txt_challan_no);
			if($challan_no!="")
			{
				$variable_set_invent=return_field_value("a.id as id"," inv_gate_in_mst a,  inv_gate_in_dtl b","a.id=b.mst_id and a.company_id=$cbo_company_name and a.challan_no='$challan_no' and b.item_category_id=$cbo_item_category  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
				if(empty($variable_set_invent))
				{
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";disconnect($con); die;
				}
				
			}
		}
		//---------------End Check Receive control on Gate Entry---------------------------//
		
		//---------------Check Meterial Over Receive control Start---------------------------//
		//$variable_over_rcv_percent=return_field_value("over_rcv_percent","variable_inv_ile_standard"," company_name = $cbo_company_name and category=$cbo_item_category and variable_list = 23","over_rcv_percent");
		//echo "10**$variable_over_rcv_percent";die; 
		$rcv_basis=str_replace("'","",$cbo_receive_basis);
		
		//if($variable_over_rcv_percent>0 && $rcv_basis !=4 && $rcv_basis !=6)
		if($rcv_basis !=4 && $rcv_basis !=6)
		{
			//txt_receive_qty current_prod_id
			$wo_pi_req_id=str_replace("'","",$txt_wo_pi_req_id);
			$cr_prod_id=str_replace("'","",$current_prod_id);
			
			$receive_return_sql = sql_select("select a.received_id, b.prod_id, c.item_group_id, sum(b.cons_quantity) as issue_qnty 
			from  inv_issue_master a, inv_transaction b, product_details_master c 
			where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and b.transaction_type=3 and a.received_id>0 and a.entry_form=368 and b.transaction_type=3 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id=$cr_prod_id group by a.received_id, b.prod_id, c.item_group_id");
			foreach($receive_return_sql as $row)
			{
				$receive_rtn_arr[$row[csf("received_id")]]+=$row[csf("issue_qnty")]/$item_conversion_factor[$row[csf("item_group_id")]];
			}
			
			$prev_rcv_sql=sql_select(" select a.id as mst_id, sum(b.order_qnty) as qnty from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.entry_form = 366 and b.transaction_type=1 and a.receive_basis = $rcv_basis and a.booking_id = $wo_pi_req_id and b.item_category=$cbo_item_category and b.prod_id=$cr_prod_id and a.status_active=1 and b.status_active=1 and b.id<>$update_id group by a.id");
			$prev_rcv_qnty=0;
			foreach($prev_rcv_sql as $row)
			{
				$prev_rcv_qnty+=$row[csf("qnty")]-$receive_rtn_arr[$row[csf("mst_id")]];
			}
			
			/*$prev_rcv_sql=sql_select(" select sum(b.order_qnty) as qnty from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.entry_form = 20 and b.transaction_type=1 and a.receive_basis = $rcv_basis and a.booking_id = $wo_pi_req_id and b.item_category=$cbo_item_category and b.prod_id=$cr_prod_id and a.status_active=1 and b.status_active=1 and b.id<>$update_id");
			$prev_rcv_qnty=$prev_rcv_sql[0][csf("qnty")];*/
			
			$current_receive_qty=str_replace("'","",$txt_receive_qty);
			$tot_qnty=$prev_rcv_qnty+$current_receive_qty;
			
			if($rcv_basis==1)
			{
				$wo_pi_req_sql=sql_select(" select sum(b.quantity) as quantity from com_pi_item_details b where b.status_active=1 and b.pi_id=$wo_pi_req_id and b.item_prod_id=$cr_prod_id");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
			}
			else if($rcv_basis==2)
			{
				$wo_pi_req_sql=sql_select(" select sum(b.supplier_order_quantity) as quantity from wo_non_order_info_dtls b where b.status_active=1 and b.mst_id=$wo_pi_req_id and b.item_id=$cr_prod_id");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
			}
			else
			{
				$wo_pi_req_sql=sql_select(" select sum(b.quantity) as quantity from inv_purchase_requisition_dtls b where b.status_active=1 and b.mst_id=$wo_pi_req_id and b.product_id=$cr_prod_id");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];
			}
			
			//$allow_qnty=($wo_pi_req_qnty+(($wo_pi_req_qnty/100)*$variable_over_rcv_percent));
			$allow_qnty=$wo_pi_req_qnty+($wo_pi_req_qnty/100);
			if($tot_qnty>$allow_qnty)
			{
				echo "30** MRR Quantity Not Allow More Then PI/Wo/Req Allowed Quantity.";disconnect($con); die;
			}
			
			
		}
		
		//---------------Check Meterial Over Receive control End---------------------------//
		
		//echo "10** select id from inv_mrr_wise_issue_details where recv_trans_id=$update_id and status_active=1";die;
		$issue_check = return_field_value("id","inv_mrr_wise_issue_details","recv_trans_id=$update_id and status_active=1","id");
		if( $issue_check>0)
		{
			echo "20**This Product Already Issue. Update Not Allow.";
			disconnect($con); die;
		}
		else
		{
			//check update id
			
					
			//previous product stock adjust here--------------------------//
			//product master table UPDATE here START ---------------------//
			
			$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,b.avg_rate_per_unit,b.current_stock,b.stock_value from inv_transaction a, product_details_master b  where a.status_active=1 and a.id=$update_id and a.prod_id=b.id");
			$before_prod_id=$before_receive_qnty=$before_rate=$beforeAmount=$before_brand="";
			$beforeStock=$beforeStockValue=$beforeAvgRate=0;
			foreach( $sql as $row)
			{
				$before_prod_id 		= $row[csf("prod_id")]; 
				$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
				$before_rate 			= $row[csf("cons_rate")]; 
				$beforeAmount			= $row[csf("cons_amount")]; //stock value
				$beforeStock			=$row[csf("current_stock")];
				$beforeStockValue		=$row[csf("stock_value")];
				$beforeAvgRate			=$row[csf("avg_rate_per_unit")];	
			}
			
			
			//stock value minus here---------------------------//
			$adj_beforeStock			=$beforeStock-$before_receive_qnty;
			$adj_beforeStockValue		=$beforeStockValue-$beforeAmount;
			$adj_beforeAvgRate			=number_format(($adj_beforeStockValue/$adj_beforeStock),$dec_place[3],'.','');	
			 
			//current product stock-------------------------//
			$current_prod_id=str_replace("'","",$current_prod_id);
			$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty from product_details_master where id=$current_prod_id");
			$presentStock=$presentStockValue=$presentAvgRate=0;
			$product_name_details="";
			foreach($sql as $result)
			{
				$presentStock		  =$result[csf("current_stock")];
				$presentStockValue	 =$result[csf("stock_value")];
				$presentAvgRate		=$result[csf("avg_rate_per_unit")];
				$product_name_details  =$result[csf("product_name_details")];
				$available_qnty		=$result[csf("available_qnty")];
			}	 
			//----------------Check Product ID END---------------------//
			
			//yarn master table UPDATE here START----------------------//booking_id$txt_wo_pi_req_id
			if($txt_store_sl_no=="") $txt_store_sl_no="''";
			if($update_id!="")
			{
				$txt_challan_date=$txt_bill_date=$txt_gate_entry_date=$txt_addi_rcvd_date="";
				$addi_info_arr = explode("_", str_replace("'","",$txt_addi_info));

				$txt_book_no = $addi_info_arr[0];
				$txt_challan_date = $addi_info_arr[1];
				$txt_bill_no = $addi_info_arr[2];
				$txt_bill_date = $addi_info_arr[3];
				$cbo_purchaser_name = $addi_info_arr[4];
				$cbo_carried_by = $addi_info_arr[5];
				$cbo_qc_check_by = $addi_info_arr[6];
				$cbo_receive_by = $addi_info_arr[7];
				$cbo_gate_entry_by = $addi_info_arr[8];
				$txt_gate_entry_date = $addi_info_arr[9];
				$txt_addi_rcvd_date = $addi_info_arr[10];
				$txt_gate_entry_no = $addi_info_arr[11];
				$txt_store_sl_no = "'".$addi_info_arr[12]."'";
				if($db_type == 0)
				{
					$txt_challan_date= change_date_format($txt_challan_date, 'yyyy-mm-dd');
					$txt_bill_date = change_date_format($txt_bill_date, 'yyyy-mm-dd');
					$txt_gate_entry_date = change_date_format($txt_gate_entry_date, 'yyyy-mm-dd');
					$txt_addi_rcvd_date = change_date_format($txt_addi_rcvd_date, 'yyyy-mm-dd');
				}else{
					$txt_challan_date = change_date_format($txt_challan_date, '', '', 1);
					$txt_bill_date = change_date_format($txt_bill_date, '', '', 1);
					$txt_gate_entry_date = change_date_format($txt_gate_entry_date, '', '', 1);
					$txt_addi_rcvd_date = change_date_format($txt_addi_rcvd_date, '', '', 1);
				}
				
				
				$field_array_receive="receive_basis*receive_date*booking_id*challan_no*store_id*exchange_rate*currency_id*supplier_id*lc_no*pay_mode*source*supplier_referance*remarks*store_sl_no*rcvd_book_no*addi_challan_date*bill_no*bill_date*purchaser_name*carried_by*qc_check_by*receive_by*gate_entry_by*gate_entry_date*addi_rcvd_date*gate_entry_no*updated_by*update_date";
				$data_array_receive="".$cbo_receive_basis."*".$txt_receive_date."*".$txt_wo_pi_req_id."*".$txt_challan_no."*".$cbo_store_name."*".$txt_exchange_rate."*".$cbo_currency."*".$cbo_supplier."*".$hidden_lc_id."*".$cbo_pay_mode."*".$cbo_source."*".$txt_sup_ref."*".$txt_remarks."*".$txt_store_sl_no."*'".$txt_book_no."'*'".$txt_challan_date."'*'".$txt_bill_no."'*'".$txt_bill_date."'*'".$cbo_purchaser_name."'*'".$cbo_carried_by."'*'".$cbo_qc_check_by."'*'".$cbo_receive_by."'*'".$cbo_gate_entry_by."'*'".$txt_gate_entry_date."'*'".$txt_addi_rcvd_date."'*'".$txt_gate_entry_no."'*'".$user_id."'*'".$pc_date_time."'";
				//echo $field_array."<br>".$data_array."==".$hidden_mrr_id."--";die;
				//$rID=sql_update("inv_receive_master",$field_array_receive,$data_array_receive,"id",$hidden_mrr_id,1);
				
				//yarn master table UPDATE here END---------------------------------------// 
				
				// yarn details table UPDATE here START-----------------------------------//		
				$rate = str_replace("'","",$txt_rate);
				$txt_ile = str_replace("'","",$txt_ile);
				$txt_receive_qty = str_replace("'","",$txt_receive_qty);
				$ile = ($txt_ile/$rate)*100; // ile cost to ile
		
				$ile_cost = str_replace("'","",$txt_ile); //ile cost = (ile/100)*rate
				$exchange_rate = str_replace("'","",$txt_exchange_rate);
				
				if($db_type==0)
				{
					$concattS = explode(",",return_field_value("concat(trim_uom,',',conversion_factor) as concat_val","lib_item_group","id=$cbo_item_group","concat_val"));
				}
				else
				{
					$concattS = explode(",",return_field_value("(trim_uom || ',' || conversion_factor) as concat_val","lib_item_group","id=$cbo_item_group","concat_val"));
				}
				$cons_uom = $concattS[0];
				$conversion_factor = $concattS[1];
				$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
				$cons_rate = number_format($domestic_rate,$dec_place[3],".","");//number_format($rate*$exchange_rate,$dec_place[3],".","");
				
				$con_quantity = $conversion_factor*$txt_receive_qty;
				$con_amount = $cons_rate*$con_quantity;
				$con_ile = $ile/$conversion_factor;  
				$con_ile_cost = ($con_ile/100)*($cons_rate);
				//echo "20**".$con_ile_cost; mysql_query("ROLLBACK"); die; 
				if($ile_cost=="") $ile_cost=0;
				if($con_ile=="") $con_ile=0;
				if($cons_uom=="") $cons_uom=0;
				 
				$field_array_trans = "receive_basis*pi_wo_batch_no*company_id*supplier_id*prod_id*item_category*transaction_type*transaction_date*store_id*order_uom*order_qnty*order_rate*order_ile*order_ile_cost*order_amount*cons_uom*cons_quantity*cons_rate*cons_ile*cons_ile_cost*cons_amount*balance_qnty*balance_amount*room*rack*self*bin_box*expire_date*remarks*updated_by*update_date";
				$data_array_trans = "".$cbo_receive_basis."*".$txt_wo_pi_req_id."*".$cbo_company_name."*".$cbo_supplier."*".$current_prod_id."*".$cbo_item_category."*1*".$txt_receive_date."*".$cbo_store_name."*".$cbo_uom."*".$txt_receive_qty."*".$txt_rate."*".$ile."*".$ile_cost."*".$txt_amount."*".$cons_uom."*".$con_quantity."*".$cons_rate."*".$con_ile."*".$con_ile_cost."*".$con_amount."*".$con_quantity."*".$con_amount."*".$txt_room."*".$txt_rack."*".$txt_self."*".$txt_binbox."*".$txt_warranty_date."*".$txt_referance."*'".$user_id."'*'".$pc_date_time."'";
				//echo "**".$field_array."<br>".$data_array;die;
				//$dtlsrID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
			}
			 
			//yarn details table UPDATE here END-----------------------------------//
				
			//product master table data UPDATE START----------------------------------------------------------// 
			$field_array_product="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
			if($before_prod_id==$current_prod_id)
			{
				$currentStock	= $adj_beforeStock+$con_quantity;
				$available_qnty  = $available_qnty+$con_quantity;
				$StockValue	  = $adj_beforeStockValue+$con_amount;
				if($currentStock<0) //Aziz
				{
					echo "30**Stock cannot be less than zero.";disconnect($con); die;
				}
				$avgRate		 = number_format($StockValue/$currentStock,$dec_place[3],'.','');
				$data_array_product = "".$avgRate."*".$con_quantity."*".$currentStock."*".number_format($StockValue,$dec_place[4],'.','')."*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'";
				//$prodUpdate = sql_update("product_details_master",$field_array_product,$data_array_product,"id",$current_prod_id,1);
			}
			else
			{ 
				//before
				$updateID_array=$update_data=array();
				$updateID_array[]=$before_prod_id;
				
				if($adj_beforeStock<0) //Aziz
				{
					echo "30**Stock cannot be less than zero.";disconnect($con); die;
				}
				$update_data[$before_prod_id]=explode("*",("".$adj_beforeAvgRate."*0*".$adj_beforeStock."*".number_format($adj_beforeStockValue,$dec_place[4],'.','')."*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'"));
				//current			 
				$presentStock 		= $presentStock+$con_quantity;
				$available_qnty  	= $available_qnty+$con_quantity;
				$presentStockValue	= $presentStockValue+$con_amount;
				$presentAvgRate		= number_format($presentStockValue/$presentStock,$dec_place[3],'.','');
				$updateID_array[]=$current_prod_id;
				
				$update_data[$current_prod_id]=explode("*",("".$presentAvgRate."*0*".$presentStock."*".number_format($presentStockValue,$dec_place[4],'.','')."*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'"));
				//$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product,$update_data,$updateID_array),1);
			}
			//------------------ product_details_master END---------------------------------------------------//
			
			

			//---------------Check Receive date with Last Transaction date-------------//
			
			$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", " prod_id = $current_prod_id and store_id=$cbo_store_name and id <> $update_id and status_active = 1 and transaction_type in(2,3,6)", "max_date");      
			if($max_transaction_date != "")
			{
				$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
				$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
				if ($receive_date < $max_transaction_date) 
				{
					echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
					//check_table_status($_SESSION['menu_id'], 0);
					disconnect($con);
					die;
				}
			} 
			//---------------Check Last Transaction date End ---------------------------//


			//serial no save---------------
			$deleteSerial=execute_query("delete from inv_serial_no_details where recv_trans_id=".$update_id,0);
			//$serialID = return_next_id("id", "inv_serial_no_details", 1);		 
			$serial_field_array = "id,recv_trans_id,prod_id,serial_no,inserted_by,insert_date,serial_qty";
			$expSerial = explode(",",str_replace("'","",$txt_serial_no));
			$expSerialqty = explode(",",str_replace("'","",$txt_serial_qty));
			$serial_data_array=="";
			for($i=0;$i<count($expSerial);$i++)
			{
				$serialID = return_next_id_by_sequence("INV_SERIAL_NO_DETAILS_PK_SEQ", "inv_serial_no_details", $con);
				if($i>0){ $serial_data_array .=","; }
				$serial_data_array .= "(".$serialID.",".$update_id.",".$current_prod_id.",'".$expSerial[$i]."','".$user_id."','".$pc_date_time."','".$expSerialqty[$i]."')";
				//$serialID++;
			}
			

			//all query execute here
			
			if($update_id!="")
			{
				$rID=sql_update("inv_receive_master",$field_array_receive,$data_array_receive,"id",$hidden_mrr_id,1);
				$dtlsrID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
			}
			
			$prodUpdate=true;
			if($before_prod_id==$current_prod_id)
			{
				$prodUpdate = sql_update("product_details_master",$field_array_product,$data_array_product,"id",$current_prod_id,1);
			}
			else
			{
				$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product,$update_data,$updateID_array),1);
			}
			$serial_dtlsrID=true;
			if(str_replace("'","",$txt_serial_no)!="")
			{
				$serial_dtlsrID = sql_insert("inv_serial_no_details",$serial_field_array,$serial_data_array,1);		
			}
			
			if($db_type==0)
			{
				if($rID && $dtlsrID && $prodUpdate && $deleteSerial && $serial_dtlsrID)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $dtlsrID && $prodUpdate && $deleteSerial && $serial_dtlsrID)
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
				}
			}
		}
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = str_replace("'","",$hidden_mrr_id);
		if($mst_id=="" || $mst_id==0)
		{ 
			echo "16**Delete not allowed. Problem occurred"; disconnect($con); die;
		}
		else 
		{
			$update_id = str_replace("'","",$update_id);
			$product_id = str_replace("'","",$current_prod_id);
			if( str_replace("'","",$update_id) == "" )
			{
				echo "16**Delete not allowed. Problem occurred"; disconnect($con);die;
			}

			//echo "10**select id from inv_transaction where transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id"; disconnect($con); die;
			$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id ","id");
			if($chk_next_transaction !="")
			{ 
				echo "20**Delete not allowed.This item is used in another transaction"; disconnect($con); die;
			}
			else
			{
				$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,b.avg_rate_per_unit,b.current_stock,b.stock_value from inv_transaction a, product_details_master b  where a.status_active=1 and a.id=$update_id and a.prod_id=b.id");
			
				$before_prod_id=$before_receive_qnty=$before_rate=$beforeAmount=$before_brand="";
				$beforeStock=$beforeStockValue=$beforeAvgRate=0;
				foreach( $sql as $row)
				{
					$before_prod_id 		= $row[csf("prod_id")]; 
					$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
					$before_rate 			= $row[csf("cons_rate")]; 
					$beforeAmount			= $row[csf("cons_amount")]; //stock value
					$beforeStock			=$row[csf("current_stock")];
					$beforeStockValue		=$row[csf("stock_value")];
					$beforeAvgRate			=$row[csf("avg_rate_per_unit")];	
				}
				//stock value minus here---------------------------//
				$adj_beforeStock			=$beforeStock-$before_receive_qnty;
				$adj_beforeStockValue		=$beforeStockValue-$beforeAmount;
				if($adj_beforeStockValue>0 && $adj_beforeStock>0)
				{
					$adj_beforeAvgRate			=number_format(($adj_beforeStockValue/$adj_beforeStock),$dec_place[3],'.','');	
				}
				else
				{
					$adj_beforeAvgRate			=0;	
				}
			
				$field_array_product="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
				$data_array_product = "".$adj_beforeAvgRate."*".$adj_beforeStock."*".number_format($adj_beforeStockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";
				
				$field_array_trans="updated_by*update_date*status_active*is_deleted";
				$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";
				
				$rID=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
				$rID2=sql_update("product_details_master",$field_array_product,$data_array_product,"id",$product_id,1);
			}
			/*$rID = sql_update("inv_receive_master",'status_active*is_deleted','0*1',"id*item_category","$mst_id*$cbo_item_category",0);
			$dtlsrID = sql_update("inv_transaction",'status_active*is_deleted','0*1',"mst_id*item_category","$mst_id*$cbo_item_category",0);
			$srID = sql_update("inv_serial_no_details",'status_active*is_deleted','0*1',"mst_id*entry_form","$mst_id*20",1);*/
		}
		//echo "10**".$rID."**".$rID2; die;
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			
			if($rID && $rID2)
			{
				oci_commit($con);   
				echo "2**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
			}
			else
			{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id);
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
	?>
     
	<script>
		function js_set_value(mrr)
		{
			$("#hidden_recv_number").val(mrr); // mrr number
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="1">
				<thead>
					<tr>                	 
						<th width="150">Supplier</th>
						<th width="150">Search By</th>
						<th width="250" align="center" id="search_by_td_up">Enter MRR Number</th>
						<th width="200">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?  
								echo create_drop_down( "cbo_supplier", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  b.party_type in(1,6,7,8) and a.status_active=1 and a.is_deleted=0  group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
							?>
						</td>
						<td align="center">
							<?  
								$search_by = array(1=>'MRR No',2=>'Challan No');
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
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
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+'<? echo $item_category; ?>'+'_'+document.getElementById('cbo_year_selection').value, 'create_mrr_search_list_view', 'search_div', 'emb_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
						</td>
				</tr>
				<tr>                  
					<td align="center" height="40" valign="middle" colspan="5">
						<? echo load_month_buttons(1);  ?>
						<!-- Hidden field here-------->
						<input type="hidden" id="hidden_recv_number" value="hidden_recv_number" />
						<!-- ---------END-------->
					</td>
				</tr>    
				</tbody>
			</tr>         
			</table>   
			<br> 
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
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = $ex_data[2];
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
	$item_category = $ex_data[6];
	$cbo_year_selection = $ex_data[7];
	// echo $cbo_year_selection; die;

	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$cbo_year_selection";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";}

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.recv_number_prefix_num LIKE '%$txt_search_common%'";	
			
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";				
 		}		 
 	} 
	
	if($db_type==0)
	{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
	else
	{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd','',-1)."' and '".change_date_format($toDate,'yyyy-mm-dd','',-1)."'";
	}
	
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	if(trim($supplier)!=0) $sql_cond .= " and a.supplier_id='$supplier'";
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];
	
	$credientian_cond="";
	if($cre_company_id!="") $credientian_cond=" and a.company_id in($cre_company_id)";
	if($cre_supplier_id!="") $credientian_cond.=" and a.supplier_id in($cre_supplier_id)";
	if($cre_store_location_id!="") $credientian_cond.=" and b.store_id in($cre_store_location_id)";
	if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";
	
	$sql = "SELECT a.id as rcv_id, a.recv_number,a.supplier_id,a.challan_no,c.lc_number,a.receive_date,a.receive_basis, sum(b.cons_quantity) as receive_qnty 
	from inv_transaction b, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id 
	where a.id=b.mst_id and a.entry_form=366 and b.status_active=1 and b.item_category in(106) and a.status_active=1 and a.is_deleted=0 $sql_cond $year_cond $credientian_cond 
	group by 
			a.id, b.mst_id,a.recv_number,a.supplier_id,a.challan_no,c.lc_number,a.receive_date,a.receive_basis order by b.mst_id";
	//echo $sql;
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$supplier_arr,5=>$receive_basis_arr);
	echo create_list_view("list_view", "MRR No, Supplier Name, Challan No, LC No, Receive Date, Receive Basis, Receive Qnty","120,120,120,120,120,100,100","900","260",0, $sql , "js_set_value", "rcv_id", "", 1, "0,supplier_id,0,0,0,receive_basis,0", $arr, "recv_number,supplier_id,challan_no,lc_number,receive_date,receive_basis,receive_qnty", "",'','0,0,0,0,3,0,1') ;	
	exit();
}

if($action=="populate_data_from_data")
{
	 $sql = "select id, recv_number, company_id, receive_basis, receive_purpose, receive_date, booking_id, challan_no, loan_party, store_id, lc_no, supplier_id, exchange_rate, currency_id, lc_no, pay_mode, source, remarks, supplier_referance,store_sl_no,is_posted_account, rcvd_book_no, addi_challan_date,bill_no, bill_date, purchaser_name, carried_by, qc_check_by, receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date,gate_entry_no
			from inv_receive_master 
			where id='$data' and entry_form=366";
	$res = sql_select($sql);
	foreach($res as $row)
	{		
        
        echo "$('#txt_store_sl_no').val('".$row[csf("store_sl_no")]."');\n";
        echo "$('#txt_store_sl_no').attr('disabled',false);\n";
        echo "$('#hidden_mrr_id').val(".$row[csf("id")].");\n";
		echo "$('#txt_mrr_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#cbo_company_name').val(".$row[csf("company_id")].");\n";
		echo"load_drop_down( 'requires/emb_material_receive_controller', ".$row[csf("company_id")].", 'load_drop_down_supplier', 'supplier' );\n";
		echo "$('#cbo_receive_basis').val(".$row[csf("receive_basis")].");\n";
		echo "$('#txt_receive_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#cbo_receive_purpose').val('".$row[csf("receive_purpose")]."');\n";
		echo "$('#hidden_posted_in_account').val('".$row[csf("is_posted_account")]."');\n";
		if($row[csf("receive_basis")]==4 && $row[csf("receive_purpose")]==5)
		{
			echo"load_drop_down( 'requires/emb_material_receive_controller', ".$row[csf("company_id")].", 'load_drop_down_loan_party', 'loan_party_td' );\n";
			echo "$('#cbo_loan_party').val('".$row[csf("loan_party")]."');\n";
		}
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		echo "$('#txt_sup_ref').val('".$row[csf("supplier_referance")]."');\n";
		if($row[csf("currency_id")]==1)
		{
			echo "$('#txt_exchange_rate').val(1);\n";
			echo "$('#txt_exchange_rate').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#txt_exchange_rate').attr('disabled',false);\n";
		}
		echo "$('#txt_exchange_rate').val(".$row[csf("exchange_rate")].");\n";
		echo "$('#cbo_pay_mode').val(".$row[csf("pay_mode")].");\n";
		echo "$('#cbo_source').val(".$row[csf("source")].");\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		
		if($row[csf("receive_basis")]==1)
			$wopireq=return_field_value("pi_number","com_pi_master_details","id=".$row[csf("booking_id")]."");	
		else if($row[csf("receive_basis")]==2)
			$wopireq=return_field_value("wo_number","wo_non_order_info_mst","id=".$row[csf("booking_id")]."");
		else if($row[csf("receive_basis")]==7)
			$wopireq=return_field_value("requ_no","inv_purchase_requisition_mst","id=".$row[csf("booking_id")]."");	
		echo "$('#txt_wo_pi_req').val('".$wopireq."');\n";
		echo "$('#txt_wo_pi_req_id').val(".$row[csf("booking_id")].");\n";
		
		echo "$('#hidden_lc_id').val(".$row[csf("lc_no")].");\n";
		$lcNumber = return_field_value("lc_number","com_btb_lc_master_details","id=".$row[csf("lc_no")]."");
		echo "$('#txt_lc_no').val('".$lcNumber."');\n";
		
        //rcvd_book_no,addi_challan_date,bill_no,bill_date,purchaser_name,carried_by,qc_check_by,receive_by,gate_entry_by,gate_entry_date
		$addi_info_str = $row[csf("rcvd_book_no")]."_".change_date_format($row[csf("addi_challan_date")])."_".$row[csf("bill_no")]."_".change_date_format($row[csf("bill_date")])."_".$row[csf("purchaser_name")]."_".$row[csf("carried_by")]."_".$row[csf("qc_check_by")]."_".$row[csf("receive_by")]."_".$row[csf("gate_entry_by")]."_".change_date_format($row[csf("gate_entry_date")])."_".change_date_format($row[csf("addi_rcvd_date")])."_".$row[csf("gate_entry_no")]."_".$row[csf("store_sl_no")];

		echo "$('#txt_addi_info').val('".$addi_info_str."');\n";


		//right side list view
		echo "show_list_view('".$row[csf("id")]."','show_dtls_list_view','list_container','requires/emb_material_receive_controller','');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_general_item_receive_entry',1);\n";
 	}
	exit();	
}

if($action=="show_dtls_list_view")
{
	$sql = "select a.recv_number, b.id, b.receive_basis,b.pi_wo_batch_no,c.product_name_details,c.lot,b.order_uom,b.order_qnty,b.order_rate,b.order_ile_cost,b.order_amount,b.cons_amount, c.item_size, c.item_category_id  
		from inv_receive_master a, inv_transaction b,  product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.status_active=1 and b.status_active=1 and a.entry_form=366 and a.id=$data order by a.id desc";
	//echo $sql;die;		
	$result = sql_select($sql);
	$i=1;
	$totalQnty=0;
	$totalAmount=0;
	$totalbookCurr=0;
	?>
    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="950" rules="all">
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Item Details</th>
                    <th>UOM</th>
                    <th>Receive Qty</th>
                    <th>Rate</th>
                    <th>ILE Cost</th>
                    <th>Amount</th>
                    <th>Book Currency</th>
                </tr>
            </thead>
            <tbody>
            	<? foreach($result as $row){  
					
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";
					
					$wopireq="";
					if($row[csf("receive_basis")]==1)
						$wopireq=return_field_value("pi_number","com_pi_master_details","id=".$row[csf("pi_wo_batch_no")]."");	
					else if($row[csf("receive_basis")]==2)
						$wopireq=return_field_value("wo_number","wo_non_order_info_mst","id=".$row[csf("pi_wo_batch_no")]."");
					else if($row[csf("receive_basis")]==7)
						$wopireq=return_field_value("requ_no","inv_purchase_requisition_mst","id=".$row[csf("pi_wo_batch_no")]."");		
					
					$totalQnty +=$row[csf("order_qnty")];
					$totalAmount +=$row[csf("order_amount")];
					$totalbookCurr +=$row[csf("cons_amount")];
					
					if($row[csf("item_category_id")]==126)
					{
						$product_name_dtls=$row[csf("product_name_details")]." ".$row[csf("item_size")];
					}
					else
					{
						$product_name_dtls=$row[csf("product_name_details")];
					}
 				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")];?>","child_form_input_data","requires/emb_material_receive_controller")' style="cursor:pointer" >
                        <td width="50"><?php echo $i; ?></td>
                        <td width="300"><p><?php echo $row[csf("product_name_details")]; ?></p></td>
                        <td width="100"><p><?php echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p></td>
                        <td width="100" align="right"><p><?php echo number_format($row[csf("order_qnty")],2,'.',''); ?></p></td>
                        <td width="100" align="right"><p><?php echo number_format($row[csf("order_rate")],4,'.',''); ?></p></td>
                        <td width="100" align="right"><p><?php echo $row[csf("order_ile_cost")]; ?></p></td>                       
                        <td width="100" align="right"><p><?php echo number_format($row[csf("order_amount")],2,'.',''); ?></p></td>
                        <td width="" align="right"><p><?php echo number_format($row[csf("cons_amount")],2,'.',''); ?></p></td>
                   </tr>
                <? $i++; } ?>
                	<tfoot>
                        <th colspan="3">Total</th>                         
                        <th><?php echo number_format($totalQnty,2,'.',''); ?></th>
                        <th colspan="2"></th>
                        <th><?php echo number_format($totalAmount,2,'.',''); ?></th>
                        <th><?php echo number_format($totalbookCurr,2,'.',''); ?></th>
                        <th></th>
                  </tfoot>
            </tbody>
        </table>
    <?
	exit();
}

if($action=="child_form_input_data")
{
	$rcv_dtls_id = $data;	
 	
	/*$sql = "select a.currency_id, a.booking_id, a.receive_basis, a.exchange_rate, b.id, b.pi_wo_batch_no, b.prod_id, b.brand_id, c.lot, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount, b.expire_date, b.room,b.rack, b.self,b.bin_box,c.item_category_id,c.item_group_id,c.item_description,c.current_stock as global_stock,b.remarks
			from inv_receive_master a, inv_transaction b, product_details_master c  
			where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id'  and a.status_active=1 and b.status_active=1";*/
			/*new dev*/
	$sql = "select a.currency_id, a.booking_id, a.receive_basis, a.exchange_rate, b.id, b.pi_wo_batch_no, b.prod_id, b.brand_id, c.lot, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount, b.expire_date, b.room,b.rack, b.self,b.bin_box,c.item_category_id,c.item_group_id,c.item_description,c.current_stock as global_stock,b.remarks,c.brand_name,c.origin,c.model
	from inv_receive_master a, inv_transaction b, product_details_master c  
	where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id'  and a.status_active=1 and b.status_active=1";
			//echo $sql;
	$result = sql_select($sql);
    
	foreach($result as $row)
	{
		// sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c  where a.id=b.mst_id
		
		
		echo "$('#cbo_item_category').val(".$row[csf("item_category_id")].");\n";
		echo "load_drop_down( 'requires/emb_material_receive_controller', ".$row[csf("item_category_id")].", 'load_drop_down_itemgroup', 'item_group_td' );\n";
		echo "$('#cbo_item_group').val(".$row[csf("item_group_id")].");\n";
		echo "$('#txt_item_desc').val('".$row[csf("item_description")]."');\n";
		echo "$('#txt_warranty_date').val('".change_date_format($row[csf("expire_date")])."');\n";
		if($db_type==0)
		{
			$serialString = return_field_value("$group_concat(serial_no)","inv_serial_no_details","recv_trans_id=".$row[csf("id")]." group by recv_trans_id");
			$serialqty = return_field_value("$group_concat(serial_qty)","inv_serial_no_details","recv_trans_id=".$row[csf("id")]." group by recv_trans_id");
		}
		else
		{
			$serialString = return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as serial_no","inv_serial_no_details","recv_trans_id=".$row[csf("id")]." group by recv_trans_id","serial_no");
			$serialqty = return_field_value("LISTAGG(CAST(serial_qty AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_qty) as serial_qty","inv_serial_no_details","recv_trans_id=".$row[csf("id")]." group by recv_trans_id","serial_qty");

		}
		
		
		echo "$('#txt_serial_no').val('".$serialString."');\n";
		echo "$('#txt_serial_qty').val('".$serialqty."');\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
 		if($row[csf("receive_basis")]==1)
			$wopireq=return_field_value("pi_number","com_pi_master_details","id=".$row[csf("booking_id")]."");	
		else if($row[csf("receive_basis")]==2)
			$wopireq=return_field_value("wo_number","wo_non_order_info_mst","id=".$row[csf("booking_id")]."");
		else if($row[csf("receive_basis")]==7)
			$wopireq=return_field_value("requ_no","inv_purchase_requisition_mst","id=".$row[csf("booking_id")]."");
		echo "$('#txt_wo_pi').val('".$wopireq."');\n";
		echo "$('#txt_wo_pi_id').val(".$row[csf("booking_id")].");\n";
 		echo "$('#txt_receive_qty').val(".$row[csf("order_qnty")].");\n";
		echo "$('#txt_rate').val(".$row[csf("order_rate")].");\n";
		echo "$('#txt_ile').val(".$row[csf("order_ile_cost")].");\n";
		echo "$('#cbo_uom').val(".$row[csf("order_uom")].");\n";
		echo "$('#txt_amount').val(".$row[csf("order_amount")].");\n";
		echo "$('#txt_book_currency').val(".$row[csf("cons_amount")].");\n";
		echo "$('#txt_glob_stock').val(".$row[csf("global_stock")].");\n";
		echo "$('#txt_referance').val('".$row[csf("remarks")]."');\n";

		echo "$('#txt_brand').val('".$row[csf("brand_name")]."');\n";//new dev
        echo "$('#cbo_origin').val('".$row[csf("origin")]."');\n";//new dev
        echo "$('#txt_model').val('".$row[csf("model")]."');\n";//new dev
		
                
		if($row[csf("receive_basis")]==1)// pi
		{
			$pi_wo_req_qty = return_field_value("sum(b.quantity) as pi_qnty","com_pi_master_details a, com_pi_item_details b","a.id=b.pi_id and a.id=".$row[csf("booking_id")]."  and b.item_prod_id=".$row[csf("prod_id")]."  group by a.id","pi_qnty");
			$totalRcvQnty = return_field_value("sum(a.order_qnty) as bal","inv_transaction a, inv_receive_master b ","a.mst_id=b.id and b.booking_id=".$row[csf("booking_id")]." and a.prod_id=".$row[csf("prod_id")]." and b.receive_basis=1 and a.transaction_type=1 and b.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
			$wo_pi_re_bal=(($pi_wo_req_qty+$row[csf("order_qnty")])-$totalRcvQnty);
		}
		else if($row[csf("receive_basis")]==2)// wo
		{
			$pi_wo_req_qty = return_field_value("sum(b.supplier_order_quantity) as wo_quantity","wo_non_order_info_mst a, wo_non_order_info_dtls b","a.id=b.mst_id and a.id=".$row[csf("booking_id")]." and b.item_id=".$row[csf("prod_id")]." group by a.id","wo_quantity");
			$totalRcvQnty = return_field_value("sum(a.order_qnty) as bal","inv_transaction a, inv_receive_master b ","a.mst_id=b.id and b.booking_id=".$row[csf("booking_id")]." and a.prod_id=".$row[csf("prod_id")]." and b.receive_basis=2 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
			$wo_pi_re_bal=(($pi_wo_req_qty+$row[csf("order_qnty")])-$totalRcvQnty);
			$wo_pi_re_bal=number_format($wo_pi_re_bal,4,".","");
		}
		else if($row[csf("receive_basis")]==7)// Req
		{
			$pi_wo_req_qty = return_field_value("sum(b.quantity) as req_quantity","inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b","a.id=b.mst_id and a.id=".$row[csf("booking_id")]." and b.product_id=".$row[csf("prod_id")]." group by a.id","req_quantity");
			$totalRcvQnty = return_field_value("sum(a.cons_quantity) as bal","inv_transaction a, inv_receive_master b ","a.mst_id=b.id and b.booking_id=".$row[csf("booking_id")]." and a.prod_id=".$row[csf("prod_id")]." and b.receive_basis=7 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
			$wo_pi_re_bal=(($pi_wo_req_qty+$row[csf("order_qnty")])-$totalRcvQnty);
		}
		
		
		
		echo "$('#txt_order_qty').val('".$wo_pi_re_bal."');\n";
		echo "$('#txt_room').val('".$row[csf("room")]."');\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "$('#txt_self').val('".$row[csf("self")]."');\n";
		echo "$('#txt_binbox').val('".$row[csf("bin_box")]."');\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		echo "$('#current_prod_id').val(".$row[csf("prod_id")].");\n";
 		echo "set_button_status(1, permission, 'fnc_general_item_receive_entry',1);\n";
		echo "disable_enable_fields( 'txt_receive_date*txt_challan_no*cbo_store_name*txt_exchange_rate*txt_store_sl_no', 0, '', '');\n";
		echo "disable_enable_fields( 'cbo_item_category*cbo_item_group', 1, '', '');\n";
		echo "fn_calile();\n";
	}
	exit();
}
if($action=="load_exchange_rate")
{
	if($data==1)
	{
		echo "$('#txt_exchange_rate').val(1);\n";
		echo "$('#txt_exchange_rate').attr('disabled',true);\n";
	}
	else
	{
		$last_exchange_rate=return_field_value("exchange_rate","inv_receive_master","currency_id=$data order by id limit 0,1");
		
		echo "$('#txt_exchange_rate').val(".$last_exchange_rate.");\n";
		echo "$('#txt_exchange_rate').attr('disabled',false);\n";
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



if ($action=="general_item_receive_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql=" select id, recv_number,receive_basis,receive_date, challan_no, lc_no, store_id, supplier_id, currency_id, exchange_rate, pay_mode,source,booking_id,store_sl_no from inv_receive_master where id='$data[1]'";
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$lc_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details where item_category_id in(106)", "id", "lc_number"  );
	$pi_arr=return_library_array( "select id, pi_number from  com_pi_master_details where item_category_id in(106)", "id", "pi_number"  );
	$wo_arr=return_library_array( "select id, wo_number from  wo_non_order_info_mst where entry_form in(146,147)", "id", "wo_number"  );
	$req_arr=return_library_array( "select id, requ_no from  inv_purchase_requisition_mst", "id", "requ_no"  );
?>
	<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
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
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Material Receiving & Inspection Report</u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>MRIR Number:</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="130"><strong>Receive Basis :</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>L/C No:</strong></td><td width="175px"><? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
            <td><strong>Store Name:</strong></td><td width="175px"><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td> <td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong>Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            <td><strong>Store Sl No:</strong></td><td width="175px"><? echo $dataArray[0][csf('store_sl_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Pay Mode:</strong></td> <td width="175px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            <td><strong>WO/PI/Req.No:</strong></td> <td width="175px"><? if ($dataArray[0][csf('receive_basis')]==1) echo $pi_arr[$dataArray[0][csf('booking_id')]]; else if ($dataArray[0][csf('receive_basis')]==2) echo $wo_arr[$dataArray[0][csf('booking_id')]]; else if ($dataArray[0][csf('receive_basis')]==7) echo $req_arr[$dataArray[0][csf('booking_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Exchange Rate:</strong></td><td width="175px"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
        </tr>
    </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" style="margin-bottom:15px;">
            <thead bgcolor="#dddddd" align="center">
                <th width="40">SL</th>
                <th width="80" align="center">Item Category</th>
                <th width="150" align="center">Item Group</th>
                <th width="200" align="center">Item Description</th>
                <th width="50" align="center">UOM</th> 
                <th width="80" align="center">Recv. Qnty.</th>
                <th width="50" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th width="80" align="center">PI/Ord/Req Qnty Bal.</th> 
                <th width="80" align="center">Warranty Exp. Date</th>                  
            </thead>
<?
	$mrr_no =$dataArray[0][csf('recv_number')];;
	$up_id =$data[1];
	$cond="";
	if($mrr_no!="") $cond .= " and a.recv_number='$mrr_no'";
	if($up_id!="") $cond .= " and a.id='$up_id'";
	 $i=1;
	 $item_name_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );

	$sql_result= sql_select("select a.id, a.receive_basis, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount,b.balance_qnty,b.expire_date, c.item_category_id, c.item_group_id, c.item_description, c.product_name_details, c.lot, c.item_size from inv_receive_master a, inv_transaction b,  product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category in (106) and a.entry_form=366  and a.status_active=1 and b.status_active=1 $cond");
	
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$order_qnty=$row[csf('order_qnty')];
			$order_qnty_sum += $order_qnty;
			
			$order_amount=$row[csf('order_amount')];
			$order_amount_sum += $order_amount;
			
			$balance_qnty=$row[csf('balance_qnty')];
			$balance_qnty_sum += $balance_qnty;
			
			$desc=$row[csf('item_description')];
			
			if($row[csf('item_size')]!="")
			{
				$desc.=", ".$row[csf('item_size')];
			}
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
                <td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                <td><? echo $desc; ?></td>
                <td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                <td align="right"><? echo number_format($row[csf('order_qnty')],2,'.',''); ?></td>
                <td align="right"><? echo number_format($row[csf('order_rate')],4,'.',''); ?></td>
                <td align="right"><? echo number_format($row[csf('order_amount')],2,'.',''); ?></td>
                <td align="right"><?  echo number_format($row[csf('balance_qnty')],2,'.',''); ?></td>
                <td><? echo change_date_format($row[csf('expire_date')]); ?></td>
			</tr>
			<?php
			$i++;
			}
			?>
        	<tr> 
                <td align="right" colspan="5" >Total</td>
                <td align="right"><? echo number_format($order_qnty_sum,2,'.',''); ?></td>
                <td align="right" colspan="2" ><? echo number_format($order_amount_sum,2,'.',''); ?></td>
                <td align="right" ><? echo number_format($balance_qnty_sum,2,'.',''); ?></td>
                <td align="right">&nbsp;</td>
			</tr>
		</table>
        
		<div style="margin-left:27px;">
        <?
			$remarks=return_field_value("remarks","inv_receive_master","company_id=$data[0] and id='$data[1]'");
			echo "Remarks : ".$remarks;
		?>
        </div>
        <h3 align="center">In Words : &nbsp;<? echo number_to_words(number_format($order_amount_sum,2,'.',''))."( ".$currency[$dataArray[0][csf('currency_id')]]." )";?></h3>


		 <?
            echo signature_table(184, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}




if($action=="general_item_receive_print_new")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents("General Item Receive Info","../../../", 1, 1, $unicode);
	
	$division_name_arr=return_library_array( "select id, division_name from   lib_division", "id", "division_name"  );
	$department_name_arr=return_library_array( "select id, department_name from   lib_department", "id", "department_name"  );
	
	$req_div_name_arr=return_library_array( "select id, division_id from   inv_purchase_requisition_mst", "id", "division_id"  );
	$req_department_name_arr=return_library_array( "select id, department_id from inv_purchase_requisition_mst", "id", "department_id"  );
	$req_no_arr=return_library_array( "select wo_number, requisition_no from wo_non_order_info_mst where entry_form in(146,147)", "wo_number", "requisition_no"  );
	
	$lc_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details", "id", "lc_number"  );
	
	$sql=" select id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, location_id, store_id, supplier_id, lc_no, currency_id, exchange_rate, source,supplier_referance,pay_mode,store_sl_no from inv_receive_master where id='$data[1]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	
	if($dataArray[0][csf('receive_basis')]==2 || $dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==7)
	{
		
		if($dataArray[0][csf('receive_basis')]==2) // Wo
		{
			$wo_sql=sql_select( "select a.id,a.wo_number,a.requisition_no as requ_id,b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and b.status_active=1 group by a.id,a.wo_number,a.requisition_no ,b.item_id");
			foreach($wo_sql as $row)
			{
				$wo_library[$row[csf("id")]]=$row[csf("wo_number")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]]+=$row[csf("wo_qnty")];
				$requsition_id_arr[$row[csf("wo_number")]]=$row[csf("requ_id")];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==1) // Pi
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach($sql_pi as $row)
			{
				$pi_library[$row[csf("pi_id")]]=$row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]]+=$row[csf("quantity")];
				
				$pi_wo_no_library[$row[csf("pi_number")]]=$row[csf("work_order_no")];
			}
		}
		else // Req.
		{
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id");
			foreach($sql_req as $row)
			{
				$requisition_library[$row[csf("req_id")]]=$row[csf("requ_no")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]]+=$row[csf("quantity")];
				
				$division_library[$row[csf("requ_no")]]=$row[csf("division_id")];
				$department_library[$row[csf("requ_no")]]=$row[csf("department_id")];
			}
		}
		
		
		$order_prev_sql=sql_select("select a.booking_id,b.prod_id,sum(b.order_qnty) as wo_prev_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id='".$dataArray[0][csf('booking_id')]."' and a.id !='".$dataArray[0][csf('id')]."'  and a.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");
		foreach($order_prev_sql as $row)
		{
			$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]=$row[csf("wo_prev_qnty")];
		}
	}
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$item_name_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );

?>
	<div style="width:970px;">
    <table width="950" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="">
        	<td colspan="6" align="center" style="font-size:14px">  
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
						Province No: <? echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></td>
        </tr>
        <tr>
        	<td width="100"><strong>MRR Number:</strong></td><td width="220"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="100"><strong>Receive Basis :</strong></td> <td width="220"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="100"><strong>Receive Date:</strong></td><td ><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Challan No:</strong></td> <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>WO/PI:</strong></td> <td >
			<? 
			if ($dataArray[0][csf('receive_basis')]==1) // PI
			{
				echo $pi_library[$dataArray[0][csf('booking_id')]];
			}
			else if($dataArray[0][csf('receive_basis')]==2) // WO
			{
				echo $wo_library[$dataArray[0][csf('booking_id')]];
			} 
			else  if($dataArray[0][csf('receive_basis')]==7) // Req.
			{
				echo $requisition_library[$dataArray[0][csf('booking_id')]];
			}
			else
			{
				echo "Independent";
			}
			?></td>
            
            <td><strong>Store Name:</strong></td><td ><strong><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></strong></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td> <td ><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong>L/C No:</strong></td><td ><? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
            <td><strong>Store Sl No:</strong></td><td ><strong><? echo $dataArray[0][csf('store_sl_no')]; ?></strong></td>
        </tr>
         <tr>
            <td><strong>Gate Entry:</strong></td> <td >&nbsp;</td>
            <td><strong>Currency:</strong></td><td ><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            <td><strong>Source:</strong></td><td ><? echo $source[$dataArray[0][csf('source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Pay Mode:</strong></td> <td ><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            <td><strong>Exchange Rate:</strong></td><td ><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
        </tr>
        <tr>
            <td rowspan="2" valign="top"><strong>Barcode:</strong></td><td rowspan="2"  valign="top" id="bar_code"></td>
        </tr>
    </table>
    <?
	if($db_type==2)
	{
	  $sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks,
	  (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details, c.item_group_id, c.item_description,c.item_code
	  from inv_receive_master a, inv_transaction b, product_details_master c 
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1 ";
	}
	else
	{
	  $sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks,
	 concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_group_id, c.item_description,c.item_code
	  from inv_receive_master a, inv_transaction b, product_details_master c 
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1";
		
	}
	
	  $sql_result= sql_select($sql_dtls);
	 $i=1;
	 
	  ?>
         <br>
<div style="width:100%;">
    <table align="right" cellspacing="0" width="960"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="40" >Item Code</th>
            <th width="70" >Item Category</th>
            <th width="110" >Item Group</th>
            <th width="160" >Item Description</th>
            <th width="40" >UOM</th> 
            <th width="70" >WO/PI Qnty.</th>
            <th width="70" >Previous Recv Qnty</th>
            <th width="70" >Today Recv. Qnty.</th>
            <th width="80">WO/PI Qnty Bal.</th>
            <th width="50" >Rate</th>
            <th width="80">Amount</th>
            <th >Comments</th>                   
        </thead>
      <?
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$order_qnty=$row[csf('order_qnty')];
			$order_qnty_sum += $order_qnty;
			
			$order_amount=$row[csf('order_amount')];
			$order_amount_sum += $order_amount;
			
			$balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]-($row[csf('order_qnty')]+$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
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
                <td align="center"><? echo $row[csf('product_name_details')]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                <td align="right"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]],2); $tot_ord_qnty+=$wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
                <td align="right"><? echo number_format($order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]],2); $tot_prev_qnty+=$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
                <td align="right"><? echo number_format($row[csf('order_qnty')],2,'.',','); ?></td>
                <td align="right"><? echo  number_format($balance_qnty,2,'.',','); ?></td>
                <td align="right"><? echo number_format($row[csf('order_rate')],4,'.',','); ?></td>
                <td align="right"><? echo number_format($row[csf('order_amount')],2,'.',',');  ?></td>
                <td><? echo $row[csf('remarks')]; ?></td>
			</tr>
			<?
			$i++;
			}
			?>
        	<tr bgcolor="#CCCCCC"> 
                <td align="right" colspan="6" >Total</td>
                <td align="right"><? echo number_format($tot_ord_qnty,2,'.',','); ?></td>
                <td align="right"><? echo number_format($tot_prev_qnty,2,'.',','); ?></td>
                <td align="right"><? echo number_format($order_qnty_sum,2,'.',','); ?></td>
                <td align="right" ><? echo number_format($balance_qnty_sum,2,'.',','); ?></td>
                <td align="right" colspan="2" ><? echo number_format($order_amount_sum,2,'.',','); ?></td>
                <td align="right">&nbsp;</td>
			</tr>
		</table>
      <table>
      	<tr>
        	<td colspan="13">
                <h3 align="center" style="margin-left:200px;" > In Words : &nbsp;<? echo number_to_words(number_format($order_amount_sum,2,'.',','))."( ".$currency[$dataArray[0][csf('currency_id')]]." )";?></h3>
            </td>
        </tr>
      </table>
        <br>
		 <?
            echo signature_table(184, $data[0], "970px");
         ?>
      </div>
   </div> 
   
   <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess )
	{
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
		
		$("#bar_code").show().barcode(value, btype, settings);
	
	} 
  
	   generateBarcode('<? echo $dataArray[0][csf('recv_number')]; ?>');
	 
	 
	 </script>
   
 <?
 exit(); 
}

if($action=="addi_info_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$user_info_arr=return_library_array("SELECT a.id, a.user_full_name, b.custom_designation from user_passwd a, lib_designation b where a.designation = b.id and a.valid = 1 order by a.user_full_name","id","user_full_name");


 	if(str_replace("'","",$pre_addi_info))
	{
		$pre_addi_info_arr = explode("_",str_replace("'","",$pre_addi_info)); 
		$pre_txt_book_no = $pre_addi_info_arr[0];
		$txt_challan_date = $pre_addi_info_arr[1];
		$txt_bill_no = $pre_addi_info_arr[2];
		$txt_bill_date = $pre_addi_info_arr[3];
		$cbo_purchaser_name = $pre_addi_info_arr[4];
		$cbo_carried_by = $pre_addi_info_arr[5];
		$cbo_qc_check_by = $pre_addi_info_arr[6];
		$cbo_receive_by = $pre_addi_info_arr[7];
		$cbo_gate_entry_by = $pre_addi_info_arr[8];

		$cbo_purchaser_name_show = $user_info_arr[$pre_addi_info_arr[4]];
		$cbo_carried_by_show = $user_info_arr[$pre_addi_info_arr[5]];
		$cbo_qc_check_by_show = $user_info_arr[$pre_addi_info_arr[6]];
		$cbo_receive_by_show = $user_info_arr[$pre_addi_info_arr[7]];
		$cbo_gate_entry_by_show = $user_info_arr[$pre_addi_info_arr[8]];

		$txt_gate_entry_date = $pre_addi_info_arr[9];
		$txt_addi_receive_date = $pre_addi_info_arr[10];
		$txt_gate_entry_no = $pre_addi_info_arr[11];
		$txt_store_sl_no = $pre_addi_info_arr[12];

	}
	
	?>
	<script>

	function fnClosed() 
	{   var txtString = "";
		txtString = $("#txt_book_no").val() + '_' + $("#txt_challan_date").val() + '_' + $("#txt_bill_no").val() + '_' + $("#txt_bill_date").val() + '_' + $("#cbo_purchaser_name").val() + '_' + $("#cbo_carried_by").val() + '_' + $("#cbo_qc_check_by").val() + '_' + $("#cbo_receive_by").val() + '_' + $("#cbo_gate_entry_by").val() + '_' + $("#txt_gate_entry_date").val()+ '_' + $("#txt_addi_receive_date").val()+ '_' + $("#txt_gate_entry_no").val()+ '_' + $("#txt_store_sl_no").val();
		$("#txt_string").val(txtString);
		parent.emailwindow.hide();
 	}

 	function openmypage_user_info(field_id)
	{
		var title = "User Info Popup";
		var pre_addi_info = $('#txt_addi_info').val();
		page_link='emb_material_receive_controller.php?action=user_info_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=410px, height=250px, center=1, resize=0, scrolling=0','../../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var user_id=this.contentDoc.getElementById("user_id").value;
			var txt_name=this.contentDoc.getElementById("txt_name").value;
			$('#'+field_id).val(user_id);
			$('#'+field_id+'_show').val(txt_name);
		}		
	}

	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<br>
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<fieldset style="width:650px;">   
				<table  width="650" cellspacing="2" cellpadding="0" border="0" >
					<tr>
						<td width="100">
							<b>Rcvd/Book No.</b>
						</td>
						<td>
							<input type="text" id="txt_book_no" name="txt_book_no" style="width:150px" class="text_boxes" value="<? echo $pre_txt_book_no; ?>" />
						</td>
						
						<td width="100">
							<b>Receive Date</b>
						</td>
						<td>
							<input type="text" id="txt_addi_receive_date" name="txt_addi_receive_date" style="width:150px" class="datepicker" value="<? echo $txt_addi_receive_date ; ?>" readonly />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Challan Date</b>
						</td>
						<td>
							<input type="text" id="txt_challan_date" name="txt_challan_date" style="width:150px" class="datepicker" value="<? echo $txt_challan_date ; ?>" readonly />
						</td>
						<td width="100">
							<b>Bill No.</b>
						</td>
						<td>
							<input type="text" id="txt_bill_no" name="txt_bill_no" style="width:150px" class="text_boxes" value="<? echo $txt_bill_no; ?>" />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Bill Date</b>
						</td>
						<td>
							<input type="text" id="txt_bill_date" name="txt_bill_date" style="width:150px" class="datepicker" value="<? echo $txt_bill_date; ?>" readonly />
						</td>
						<td width="100">
							<b>Purchaser Name</b>
						</td>
						<td>
							<?
							//echo create_drop_down( "cbo_purchaser_name", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_purchaser_name, "" );
							?>
							<input type="text" class="text_boxes" id="cbo_purchaser_name_show" value="<? echo $cbo_purchaser_name_show;?>" onDblClick="openmypage_user_info('cbo_purchaser_name')" style="width: 150px" placeholder="Browse" readonly >
							<input type="hidden" class="text_boxes" id="cbo_purchaser_name" value="<? echo $cbo_purchaser_name;?>" >
						</td>
					</tr>
					<tr>
						<td width="100">
							<p><b>Carried By</b>(Deliveried By)</p>
						</td>
						<td>
							<?
							//echo create_drop_down( "cbo_carried_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_carried_by, "" );
							?>
							<input type="text" class="text_boxes" id="cbo_carried_by_show" value="<? echo $cbo_carried_by_show;?>" onDblClick="openmypage_user_info('cbo_carried_by')" style="width: 150px" placeholder="Browse" readonly >
							<input type="hidden" class="text_boxes" id="cbo_carried_by" value="<? echo $cbo_carried_by;?>" >
						</td>
						<td width="100">
							<b>QC Check By</b>
						</td>
						<td>
							<?
							//echo create_drop_down( "cbo_qc_check_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_qc_check_by, "" );
							?>
							<input type="text" class="text_boxes" id="cbo_qc_check_by_show" value="<? echo $cbo_qc_check_by_show;?>" onDblClick="openmypage_user_info('cbo_qc_check_by')" style="width: 150px" placeholder="Browse" readonly >
							<input type="hidden" class="text_boxes" id="cbo_qc_check_by" value="<? echo $cbo_qc_check_by;?>" >
						</td>

					</tr>
					<tr>
						<td width="100">
							<b>Received By</b>
						</td>
						<td>
							<?
							//echo create_drop_down( "cbo_receive_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_receive_by, "" );
							?>
							<input type="text" class="text_boxes" id="cbo_receive_by_show" value="<? echo $cbo_receive_by_show;?>" onDblClick="openmypage_user_info('cbo_receive_by')" style="width: 150px" placeholder="Browse" readonly >
							<input type="hidden" class="text_boxes" id="cbo_receive_by" value="<? echo $cbo_receive_by;?>" >
						</td>
						<td width="100">
							<b>Gate Entry No</b>
						</td>
						<td>
							<input type="text" id="txt_gate_entry_no" name="txt_gate_entry_no" class="text_boxes" style="width:150px" value="<? echo $txt_gate_entry_no; ?>" />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Gate Entry By</b>
						</td>
						<td>
							<?
							//echo create_drop_down( "cbo_gate_entry_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_gate_entry_by, "" );
							?>
							<input type="text" class="text_boxes" id="cbo_gate_entry_by_show" value="<? echo $cbo_gate_entry_by_show;?>" onDblClick="openmypage_user_info('cbo_gate_entry_by')" style="width: 150px" readonly placeholder="Browse">
							<input type="hidden" class="text_boxes" id="cbo_gate_entry_by" value="<? echo $cbo_gate_entry_by;?>" >
						</td>

						<td width="100">
							<b>Gate Entry Date</b>
						</td>
						<td>
							<input type="text" id="txt_gate_entry_date" name="txt_gate_entry_date" style="width:150px" class="datepicker" value="<? echo $txt_gate_entry_date; ?>" readonly />
						</td>

					</tr>
					<tr>
						<td width="100">
							<b>Store Sl No.</b>
						</td>
						<td>
							<input type="text" class="text_boxes" id="txt_store_sl_no" value="<? echo $txt_store_sl_no;?>" style="width: 150px">
						</td>
					</tr>

				</table>
				<br>  
	            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fnClosed()" /></div>

				<input type="hidden" id="txt_string" value="" />
				<br>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}
if($action == "user_info_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>

	function js_set_value(str) 
	{  
		var splitArr = str.split("_");
		$("#user_id").val(splitArr[0]);
		$("#txt_name").val(splitArr[1]);
		parent.emailwindow.hide(); 
 	}

	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<br>
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<?
					$sql="SELECT a.id,a.user_name, a.user_full_name, b.custom_designation from user_passwd a, lib_designation b where a.designation = b.id and a.valid = 1 order by a.user_full_name";
					echo  create_list_view ( "list_view","User Id, User Full Name,Designation", "70,130,140","370","240",0, $sql, "js_set_value", "id,user_full_name", "", 1, "0,0,0", $arr, "user_name,user_full_name,custom_designation", "", 'setFilterGrid("list_view",-1);'); 
				?>
				<input type="hidden" id="user_id" name="user_id">
				<input type="hidden" id="txt_name" name="txt_name">
			</form>
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="general_item_receive_print_3")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents("General Item Receive Info","../../../", 1, 1, $unicode);
	
	$division_name_arr=return_library_array( "select id, division_name from   lib_division", "id", "division_name"  );
	$department_name_arr=return_library_array( "select id, department_name from   lib_department", "id", "department_name"  );
	
	$req_div_name_arr=return_library_array( "select id, division_id from   inv_purchase_requisition_mst", "id", "division_id"  );
	$req_department_name_arr=return_library_array( "select id, department_id from inv_purchase_requisition_mst", "id", "department_id"  );
	$req_no_arr=return_library_array( "select wo_number, requisition_no from wo_non_order_info_mst where entry_form in(146,147)", "wo_number", "requisition_no"  );
	
	$user_name_arr=return_library_array("select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id","user_full_name");
	$lc_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details", "id", "lc_number"  );
	
	$sql=" select id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, location_id, store_id, supplier_id, lc_no, currency_id, exchange_rate, source,supplier_referance,pay_mode,store_sl_no,rcvd_book_no, addi_challan_date,bill_no, bill_date, purchaser_name, carried_by, qc_check_by, receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date from inv_receive_master where id='$data[1]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	
	if($dataArray[0][csf('receive_basis')]==2 || $dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==7)
	{
		
		if($dataArray[0][csf('receive_basis')]==2) // Wo
		{
			$wo_sql=sql_select( "select a.id,a.wo_number,a.wo_date,a.requisition_no as requ_id,b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and b.status_active=1 group by a.id,a.wo_number,a.requisition_no ,b.item_id,a.wo_date");
			foreach($wo_sql as $row)
			{
				$wo_library[$row[csf("id")]]["wo_number"]=$row[csf("wo_number")];
				$wo_library[$row[csf("id")]]["wo_date"]=$row[csf("wo_date")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]]=$row[csf("wo_qnty")];
				$requsition_id_arr[$row[csf("wo_number")]]=$row[csf("requ_id")];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==1) // Pi
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach($sql_pi as $row)
			{
				$pi_library[$row[csf("pi_id")]]=$row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]]+=$row[csf("quantity")];
				
				$pi_wo_no_library[$row[csf("pi_number")]]=$row[csf("work_order_no")];
			}
		}
		else // Req.
		{
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.requisition_date,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id,a.requisition_date");
			foreach($sql_req as $row)
			{
				$requisition_library[$row[csf("req_id")]]["requ_no"]=$row[csf("requ_no")];
				$requisition_library[$row[csf("req_id")]]["requisition_date"]=$row[csf("requisition_date")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]]=$row[csf("quantity")];
				
				$division_library[$row[csf("requ_no")]]=$row[csf("division_id")];
				$department_library[$row[csf("requ_no")]]=$row[csf("department_id")];
			}
		}
		
		
		$order_prev_sql=sql_select("select a.booking_id,b.prod_id,sum(b.order_qnty) as wo_prev_qnty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.booking_id='".$dataArray[0][csf('booking_id')]."' and a.id !='".$dataArray[0][csf('id')]."'  and a.status_active=1 and b.status_active=1 group by a.booking_id,b.prod_id");
		foreach($order_prev_sql as $row)
		{
			$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]=$row[csf("wo_prev_qnty")];
		}
	}
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

	$supplier_res = sql_select( "select id,supplier_name,address_1 from  lib_supplier" );
	foreach ($supplier_res as $val) {
		$supplier_library[$val[csf("id")]]["supplier_name"]=$val[csf("supplier_name")];
		$supplier_library[$val[csf("id")]]["supplier_address"]=$val[csf("address_1")];
	}
	
	
	$prod_min_max_rate_res = sql_select("select prod_id, max(order_rate) max_order_rate,max(transaction_date) keep (dense_rank first order by order_rate desc) max_order_date,
	min(order_rate)min_order_rate,min(transaction_date) keep (dense_rank first order by order_rate asc) min_order_date
	from inv_transaction where transaction_type=1 and status_active = 1
	group by prod_id");
	
	foreach ($prod_min_max_rate_res as $val) 
	{
		$prod_min_max_rate_arr [$val[csf("prod_id")]]["max_order_rate"]=$val[csf("max_order_rate")];
		$prod_min_max_rate_arr [$val[csf("prod_id")]]["max_order_date"]=$val[csf("max_order_date")];
		$prod_min_max_rate_arr [$val[csf("prod_id")]]["min_order_rate"]=$val[csf("min_order_rate")];
		$prod_min_max_rate_arr [$val[csf("prod_id")]]["min_order_date"]=$val[csf("min_order_date")];
	}	

	//=================Last Rate============>>>>>================

		//$last_rate_res = sql_select("select prod_id,order_rate,transaction_date, mst_id from inv_transaction where transaction_type=1 and status_active = 1 and transaction_date <= '".$dataArray[0][csf('receive_date')]."' and mst_id < ".$dataArray[0][csf('id')]." order by mst_id desc");

		$last_rate_res = sql_select("select a.prod_id,a.order_rate,a.transaction_date, a.mst_id, b.recv_number
		from inv_transaction a, inv_receive_master b
		where a.mst_id = b.id and a.transaction_type=1 and a.status_active = 1 
		and a.transaction_date <= '".$dataArray[0][csf('receive_date')]."' and a.mst_id < ".$dataArray[0][csf('id')]." 
		 order by a.mst_id desc");


		$prodDupliChkArr = array();
		foreach ($last_rate_res as $value) 
		{
			if($prodDupliChkArr[$value[csf("prod_id")]] == "")
			{
				$prodDupliChkArr[$value[csf("prod_id")]] = $value[csf("prod_id")];
				$last_rate_arr[$value[csf("prod_id")]]["last_rate"] = $value[csf("order_rate")];
				$last_rate_arr[$value[csf("prod_id")]]["trans_date"] = $value[csf("transaction_date")];
				$last_rate_arr[$value[csf("prod_id")]]["recv_number"] = $value[csf("recv_number")];
			}
			
		}
		unset($last_rate_res);

	//=======================================<<<<================
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$item_name_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );

?>
<style type="text/css">
	#top_table tr td{
		vertical-align:top;
	}
</style> 
	<div style="width:1170px;">
    <table width="1150" cellspacing="1" align="right" id="top_table">
        <tr>
            <td colspan="8" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="">
        	<td colspan="8" align="center" style="font-size:14px">  
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
						Province No: <? echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="8" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></td>
        </tr>
        <tr>
        	<td width="150"><strong>MRR Number</strong></td><td width="180">:<? echo $dataArray[0][csf('recv_number')]; ?></td>
        	<td width="100"><strong>Receive No</strong></td><td width="130">:<? echo $dataArray[0][csf('rcvd_book_no')]; ?></td>
            <td width="100"><strong>Receive Basis</strong></td> <td width="220">:<? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?>&nbsp;&nbsp;</td>
            <td width="120"><strong>Purchase By</strong></td> <td width="180">:<? echo $user_name_arr[$dataArray[0][csf('purchaser_name')]]; ?></td>
            
        </tr>
        <tr>
        	<td><strong>Mrr Date</strong></td><td>:<? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        	<td><strong>Receive Date</strong></td><td>:<? echo change_date_format($dataArray[0][csf('addi_rcvd_date')]); ?></td>
        	<td><strong>L/C No</strong></td><td>:<? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
        	<td><strong>Delivered By</strong></td><td>:<? echo $user_name_arr[$dataArray[0][csf('carried_by')]]; ?></td>
        </tr>
        <tr>    
            <td><strong>WO/PI/Req.No</strong></td> <td >:
			<? 
			if ($dataArray[0][csf('receive_basis')]==1) // PI
			{
				echo $pi_library[$dataArray[0][csf('booking_id')]];
			}
			else if($dataArray[0][csf('receive_basis')]==2) // WO
			{
				echo $wo_library[$dataArray[0][csf('booking_id')]]["wo_number"];
			} 
			else  if($dataArray[0][csf('receive_basis')]==7) // Req.
			{
				echo $requisition_library[$dataArray[0][csf('booking_id')]]["requ_no"];
			}
			else
			{
				echo "Independent";
			}
			?></td>
            <td><strong>Challan No</strong></td> <td >:<? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Supplier</strong></td> <td >:<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_name"]; ?></td>
            <td><strong>QC Checked By</strong></td> <td>:<? echo $user_name_arr[$dataArray[0][csf('qc_check_by')]]; ?></td>
        </tr>
        <tr>
            <td><strong>WO/PI/Req.No Date</strong></td> 
            <td >:
				<? 
				if ($dataArray[0][csf('receive_basis')]==1) // PI
				{
					echo $pi_library[$dataArray[0][csf('booking_id')]];
				}
				else if($dataArray[0][csf('receive_basis')]==2) // WO
				{
					echo $wo_library[$dataArray[0][csf('booking_id')]]["wo_date"];
				} 
				else  if($dataArray[0][csf('receive_basis')]==7) // Req.
				{
					echo $requisition_library[$dataArray[0][csf('booking_id')]]["requisition_date"];
				}
				else
				{
					echo "";
				}
				?>
			</td>
			<td><strong>Challan Date</strong></td><td >:<? echo change_date_format($dataArray[0][csf('addi_challan_date')]); ?></td>
			<td><strong>Supplier Address</strong></td> <td><p>:<? echo $supplier_library[$dataArray[0][csf('supplier_id')]]["supplier_address"]; ?></p></td>
			<td><strong>Received By </strong></td> <td>:<? echo $user_name_arr[$dataArray[0][csf('receive_by')]]; ?></td>
        </tr>
         <tr>
            <td><strong>Gate Entry No</strong></td> <td >:<? echo $dataArray[0][csf('gate_entry_no')];?></td>
            <td><strong>Bill No</strong></td> <td >:<? echo $dataArray[0][csf('bill_no')];?></td>
            <td><strong>Store Name</strong></td><td >:<strong><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></strong></td>
            <td><strong>Gate Entry By </strong></td> <td>:<? echo $user_name_arr[$dataArray[0][csf('gate_entry_by')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Gate Entry Date</strong></td><td >:<? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
        	<td><strong>Bill Date</strong></td><td >:<? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
        	<td><strong>Store Sl No</strong></td><td >:<strong><? echo $dataArray[0][csf('store_sl_no')]; ?></strong></td>
        	<td><strong>Prepared By</strong></td> <td>:<? echo $user_name_arr[$user_id]; ?></td>
		</tr>
		<tr>
			<td><strong>Pay Mode</strong></td> 
			<td>:<? 
					if($dataArray[0][csf('receive_basis')] == 1 && $dataArray[0][csf('pay_mode')] ==0)
					{
						echo $pay_mode[2]; //PI basis pay mode will be import
					}
					else{
						echo $pay_mode[$dataArray[0][csf('pay_mode')]]; 
					}
					
				?>
			</td>
        	<td><strong>Currency</strong></td><td >:<? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            <td><strong>Source</strong></td><td >:<? echo $source[$dataArray[0][csf('source')]]; ?></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2" valign="top"><strong>Barcode</strong></td>:
            <td rowspan="2" colspan="3" valign="top" id="bar_code"></td>
            <td><strong>Exchange Rate</strong></td><td >:<? echo $dataArray[0][csf('exchange_rate')]; ?></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
    </table>
    <?
	if($db_type==2)
	{
	  $sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks,
	  (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details,c.item_number,c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
	  from inv_receive_master a, inv_transaction b, product_details_master c 
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1 ";
	}
	else
	{
	  $sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,c.id as product_id,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks,
	 concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_number, c.item_group_id, c.item_description,c.item_code, c.brand_name, c.origin, b.transaction_date
	  from inv_receive_master a, inv_transaction b, product_details_master c 
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1  and a.status_active=1 and b.status_active=1";
		
	}
	
	  $sql_result= sql_select($sql_dtls);
	 $i=1;
	 
	  ?>
         <br>
         <br>
	<div style="width:100%;margin-top:20px;">
    <table align="left" cellspacing="0" width="1260"  border="1" rules="all" class="rpt_table" style="margin-left:20px;">
        <thead bgcolor="#dddddd" align="center">
		   <tr>
	            <th width="30" rowspan="2">SL</th>
	            <th width="40" rowspan="2">Item Code</th>
	            <th width="40" rowspan="2">Item Number</th>
	            <th width="70" rowspan="2">Item Category</th>
	            <th width="110" rowspan="2">Item Group</th>
	            <th width="160" rowspan="2">Item Description</th>
	            <th width="100" rowspan="2">Brand</th>
	            <th width="100" rowspan="2">Origin</th>
	            <th width="40" rowspan="2">UOM</th> 
	            <th width="70" rowspan="2">WO/PI Qnty.</th>
	            <th width="70" rowspan="2">Previous Recv Qnty</th>
	            <th width="70" rowspan="2">Today Recv. Qnty.</th>
	            <th width="80" rowspan="2">WO/PI Qnty Bal.</th>
		    	<th width="240" colspan="3">Unit Price and Date</th>
	            <th width="50" rowspan="2">Rate</th>
	            <th width="" rowspan="2">Amount</th>  
		   </tr>
		   <tr>
				<th width="100">Maximum</th>
				<th width="100">Minimum</th>
				<th width="100">Last</th>
		   </tr>           
        </thead>
	<tbody>
      <?
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$order_qnty=$row[csf('order_qnty')];
			$order_qnty_sum += $order_qnty;
			
			$order_amount=$row[csf('order_amount')];
			$order_amount_sum += $order_amount;
			
			$balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]-($row[csf('order_qnty')]+$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
			$balance_qnty_sum += $balance_qnty;
			
			$desc=$row[csf('item_description')];
			
			if($row[csf('item_size')]!="")
			{
				$desc.=", ".$row[csf('item_size')];
			}
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
                <td ><? echo $i; ?></td>
                <td ><? echo $row[csf('item_code')]; ?></td>
                <td ><? echo $row[csf('item_number')]; ?></td>
                <td ><? echo $item_category[$row[csf('item_category')]]; ?></td>
                <td ><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                <td  align="center"><? echo $row[csf('product_name_details')]; ?></td>
                <td  align="center"><? echo $row[csf('brand_name')]; ?></td>
                <td  align="center"><? echo $country_arr[$row[csf('origin')]]; ?></td>
                <td  align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                <td align="right"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]],2); $tot_ord_qnty+=$wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
                <td  align="right"><? echo number_format($order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]],2); $tot_prev_qnty+=$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
                <td  align="right"><? echo number_format($row[csf('order_qnty')],2,'.',','); ?></td>
                <td  align="right"><? echo  number_format($balance_qnty,2,'.',','); ?></td>

				<td  align="right">
					<? echo  number_format($prod_min_max_rate_arr [$row[csf("prod_id")]]["max_order_rate"],2)."<hr/ style='border:1px solid black'>".$prod_min_max_rate_arr [$row[csf("prod_id")]]["max_order_date"]; ?>
				</td>
				<td  align="right">
					<? echo  number_format($prod_min_max_rate_arr [$row[csf("prod_id")]]["min_order_rate"],2)."<hr/ style='border:1px solid black'>".$prod_min_max_rate_arr [$row[csf("prod_id")]]["min_order_date"]; ?>
				</td>
				<td  align="right" title="mrr no = <? echo $last_rate_arr[$row[csf("prod_id")]]["recv_number"];?>">
					<?        
						//echo  number_format($row[csf("order_rate")],2)."<hr/ style='border:1px solid black'><span>".$row[csf("transaction_date")]."</span>"; 
						echo  number_format($last_rate_arr[$row[csf("prod_id")]]["last_rate"],2)."<hr/ style='border:1px solid black'><span>".$last_rate_arr[$row[csf("prod_id")]]["trans_date"]."</span>"; 
					?>
				</td>

                <td  align="right"><? echo number_format($row[csf('order_rate')],4,'.',','); ?></td>
                <td  align="right"><? echo number_format($row[csf('order_amount')],2,'.',',');  ?></td>
			</tr>
			<?
			$i++;
			}
			?>
        	<tr bgcolor="#CCCCCC"> 
                <td align="right" colspan="8" >Total</td>
                <td align="right"><? echo number_format($tot_ord_qnty,2,'.',','); ?></td>
                <td align="right"><? echo number_format($tot_prev_qnty,2,'.',','); ?></td>
                <td align="right"><? echo number_format($order_qnty_sum,2,'.',','); ?></td>
                <td align="right" ><? echo number_format($balance_qnty_sum,2,'.',','); ?></td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right" colspan="2" ><? echo number_format($order_amount_sum,2,'.',','); ?></td>
                
		</tr>
	</tbody>
	</table>
      <table>
      	<tr>
        	<td colspan="15">
                <h3 align="center" style="margin-left:150px;" > In Words : &nbsp;<? echo number_to_words(number_format($order_amount_sum,2,'.',','))."( ".$currency[$dataArray[0][csf('currency_id')]]." )";?></h3>
            </td>
        </tr>
      </table>
        <br>
		 <?
            echo signature_table(184, $data[0], "970px");
         ?>
      </div>
   </div> 
   
   <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess )
	{
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
		
		$("#bar_code").show().barcode(value, btype, settings);
	
	} 
  
	   generateBarcode('<? echo $dataArray[0][csf('recv_number')]; ?>');
	 
	 
	 </script>
   
 <?
 exit(); 
}
?>
