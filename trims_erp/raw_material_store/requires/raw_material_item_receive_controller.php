<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)";
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}
else
{
	 $item_cate_credential_cond="".implode(",",array_flip($item_category))."";
}
if ($supplier_id !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}

//========== user credential end ==========

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


//$result = array_intersect($array1, $array2);
//--------------------------------------------------------------------------------------------
$trim_group_arr = return_library_array("select id, order_uom from lib_item_group","id","order_uom");

//load drop down supplier
if ($action=="load_drop_down_supplier")
{
	//echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where FIND_IN_SET($data,a.tag_company) and a.id=b.supplier_id and b.party_type in (1,6,7,8,90) and a.status_active=1 and a.is_deleted=0 group by a.id order by a.supplier_name","id,supplier_name", 1, "-- Select --", "", "" );
	//select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  b.party_type=2 and a.status_active=1 and a.is_deleted=0  group by a.id,a.supplier_name order by a.supplier_name
	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) $supplier_credential_cond and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	exit();
}

if ($action=="load_drop_down_loan_party")
{
	echo create_drop_down( "cbo_loan_party", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","","" );
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/raw_material_item_receive_controller",$data);
}

//load drop down store
/*if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in ($item_cate_credential_cond) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id in($data)  group by a.id ,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", "", "","" );
	exit();
}*/
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
	$data = explode('_',$data);
//print_r($data);
	echo create_drop_down( "cbo_item_group", 130, "select id,item_name from lib_item_group where item_category=$data[0] and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", 0, "load_drop_down( 'requires/raw_material_item_receive_controller', this.value, 'load_drop_down_uom', 'uom_td' );","" );
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
?>
<script>
	function js_set_value(str)
	{
		var splitData = str.split("_");

		if(splitData[2] == "No")
		{
			alert("Goods receive not allowed against Un-Approved P.O. Please ensure the P.O is approved before receiving the goods"); return;
		}

		$("#hidden_tbl_id").val(splitData[0]); // wo/pi id
		$("#hidden_wopi_number").val(splitData[1]); // wo/pi number
		parent.emailwindow.hide();
	}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
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
                    <? ($receive_basis == 1) ? $category_disable = "":$category_disable=1; ?>
                    <td>
                        <?
							//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index )
							echo create_drop_down( "cbo_item_category", 170, $item_category,"", 1, "-- Select --", "", "","$category_disable","101","","","" );


                            // 4,8,9,10,11,15,16,17,18,19,20,21,22,32
                        ?>
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                     </td>
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $cbo_store_name ?>', 'create_wopi_search_list_view', 'search_div', 'raw_material_item_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
            	</tr>
                <tr>
                    <td align="center" height="40" valign="middle" colspan="5">
                        <? echo load_month_buttons(1);  ?>
                        <!-- Hidden field here-->
                        <input type="hidden" id="hidden_tbl_id" value="" />
                        <input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number" />
                        <!-- END -->
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
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$item_cat_ref = $ex_data[5];
	$cbo_string_search_type = $ex_data[6];

	$appr_status=array();

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
		if($item_cat_ref>0)
        {
            $sql_cond .= " and a.item_category_id=$item_cat_ref";
        }
        else {
            $sql_cond .= " and a.item_category_id in (101)" ;
        }
	}
	else if(trim($txt_search_by)==2) // for wo
	{
		if( $txt_date_from!="" && $txt_date_to!="" ) $sql_cond .= " and wo_date  between '".$txt_date_from."' and '".$txt_date_to."'";
		//if($item_cat_ref>0) $sql_cond .= " and item_category=$item_cat_ref";
	}
	else if(trim($txt_search_by)==7) // for requisition
	{
		if( $txt_date_from!="" && $txt_date_to!="" ) $sql_cond .= " and requisition_date  between '".$txt_date_from."' and '".$txt_date_to."'";
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
				$sql_cond .= " and wo_number_prefix_num='$txt_search_common'";
			}
			else if($cbo_string_search_type==2)
			{
				$sql_cond .= " and wo_number_prefix_num LIKE '$txt_search_common%'";
			}
			else if($cbo_string_search_type==3)
			{
				$sql_cond .= " and wo_number_prefix_num LIKE '%$txt_search_common'";
			}
			else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
			{
				$sql_cond .= " and wo_number_prefix_num LIKE '%$txt_search_common%'";
			}

			if(trim($company)!="") $sql_cond .= " and company_name='$company'";
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
 		$sql = "select a.id as id,a.pi_number as wopi_number,b.lc_number as lc_number,a.pi_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source ,a.item_category_id as item_category
				from com_pi_master_details a left join com_btb_lc_master_details b on FIND_IN_SET(a.id,b.pi_id)
				where
				a.item_category_id  in (101) and
				a.status_active=1 and a.is_deleted=0 and a.goods_rcv_status<>1 and
				a.importer_id=$company
				$sql_cond $approval_status_cond";//a.supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
		}

		if($db_type==1 || $db_type==2)
		{
			$sql = "select a.id as id,a.pi_number as wopi_number,b.lc_number as lc_number,a.pi_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source,a.item_category_id as item_category
				from com_pi_master_details a left join com_btb_lc_pi c on a.id=c.pi_id left join com_btb_lc_master_details b on c.com_btb_lc_master_details_id=b.id
				where
				a.item_category_id in (101) and
				a.status_active=1 and a.is_deleted=0 and
				a.importer_id=$company
				$sql_cond $approval_status_cond";
		}

	}
	else if($txt_search_by==2) // wo base
	{
 				$sql = "select id,wo_number_prefix_num as wopi_number,' ' as lc_number,wo_date as wopi_date,supplier_id as supplier_id,currency_id as currency_id,source as source,item_category,location_id,is_approved,entry_form
				from wo_non_order_info_mst
				where
				status_active=1 and is_deleted=0 and
				entry_form in(146,147) and pay_mode in (1,4) and
				company_name=$company
				$sql_cond";//supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )

				//checking approval status start for stationary
					$current_date = date('m/d/Y');
					if($db_type==0)
					{
						$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($current_date,'yyyy-mm-dd')."' and company_id='$company')) and page_id=16 and status_active=1 and is_deleted=0";
					}
					else
					{
						$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($current_date, "", "",1)."' and company_id='$company')) and page_id=16 and status_active=1 and is_deleted=0";
					}
					$approval_status=sql_select($approval_status);
					$approve_status[146]['status']=$approval_status[0][csf('approval_need')];

					//checking approval status start for other Purchase
					if($db_type==0)
					{
						$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($current_date,'yyyy-mm-dd')."' and company_id='$company')) and page_id=22 and status_active=1 and is_deleted=0";
					}
					else
					{
						$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($current_date, "", "",1)."' and company_id='$company')) and page_id=22 and status_active=1 and is_deleted=0";
					}
					$approval_status=sql_select($approval_status);
					$approve_status[147]['status']=$approval_status[0][csf('approval_need')];

				$sql_data=sql_select($sql);
				foreach($sql_data as $val)
				{
					if($val[csf('is_approved')]==1){$result="Yes";}else{$result="No";}
					if($approve_status[$val[csf('entry_form')]]['status']==1)
					{
						$appr_status[$val[csf('id')]]=$result;
					}
					else
					{
						$appr_status[$val[csf('id')]]="N/A";
					}
				}
	}
	else if($txt_search_by==7) // requisition base
	{
 		/* $sql = "select id, requ_prefix_num as wopi_number,' ' as lc_number,requisition_date as wopi_date,'' as supplier_id, cbo_currency as currency_id,source as source , item_category_id as item_category
				from inv_purchase_requisition_mst
				where
				status_active=1 and is_deleted=0 and
				item_category_id not in (1,2,3,5,6,7,12,13,14,23) and pay_mode=4 and
				company_id=$company
				$sql_cond"; */
                //and  find_in_set(b.item_category,c.item_category_id) > 0

                 $approval_need=return_field_value("approval_need","approval_setup_mst a, approval_setup_dtls b","a.id = b.mst_id
			    and b.page_id = 13 and a.company_id = $company
			    and a.setup_date = ( select max(c.setup_date) from approval_setup_mst c where c.company_id = $company )");
			    if($approval_need==1)
			    {
			        $approval_cond = " and a.is_approved = '1'";
			    }else{
			        $approval_cond="";
			    }

                $sql = "select a.id, a.requ_prefix_num as wopi_number,' ' as lc_number,a.requisition_date as wopi_date,'' as supplier_id, a.cbo_currency as currency_id,a.source as source , a.item_category_id , b.item_category,a.location_id
				from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b ,lib_store_location c
				where a.id = b.mst_id  and c.id = $ex_data[7]
				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and
				b.item_category in (101) and a.pay_mode=4 and
				a.company_id=$company $approval_cond
				$sql_cond";
	}
	//echo $sql;
	$result = sql_select($sql);

	$location_lib_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
 	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$location_lib_arr,4=>$supplier_arr,5=>$currency,6=>$source,7=>$item_category,8=>$appr_status);

	/*echo  create_list_view("list_view", "WO/PI No,Location, LC ,Date, Supplier, Currency, Source,Item Category,Approval Status","50,100,100,100,150,100,120,120,50","1000","260",0, $sql , "js_set_value", "id,wopi_number","", 1, "0,location_id,0,0,supplier_id,currency_id,source,item_category,id", $arr, "wopi_number,location_id,lc_number,wopi_date,supplier_id,currency_id,source,item_category,id", "",'','0,0,0,0,0,0,0,0,0') ;*/
	?>

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
            <thead>
                <th width="40">SL No</th>
                <th width="70">WO/PI No</th>
                <th width="120">Location</th>
                <th width="80">LC</th>
                <th width="90">Date</th>
                <th width="150">Supplier</th>
                <th width="60">Currency</th>
                <th width="100">Source</th>
                <th width="100"> Item Category</th>
                <th> Approval Status</th>
            </thead>
         </table>
         <div style="width:900px; max-height:250px; overflow-y:scroll">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="list_view">
             <?
             $i=1;
             foreach ($result as $row)
             {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('wopi_number')]."_".$appr_status[$row[csf('id')]]; ?>')">

                        <td width="40">
						<? echo "$i"; ?>
                        </td>
                        <td width="70"><p><? echo $row[csf('wopi_number')];?></p></td>
                        <td width="120"><p><? echo $location_lib_arr[$row[csf('location_id')]];?></p></td>
                        <td width="80"><p><? echo $row[csf('lc_number')]; ?></p></td>
                        <td width="90"><p><? echo change_date_format($row[csf('wopi_date')]);?> </p></td>
                        <td width="150" align="center"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td width="100"><p><? echo $source[$row[csf('source')]]; ?></p></td>
                        <td width="100"><p><? echo $item_category[$row[csf('item_category')]]; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $appr_status[$row[csf('id')]]; ?></p></td>

                    </tr>
                    <?
             		$i++;
             }
             ?>
            </table>
	<?
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
 		$sql = "select b.id as id, a.pi_number as wopi_number, b.lc_number as lc_number, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, '' as pay_mode, a.id as pi_id
				from com_pi_master_details a left join com_btb_lc_master_details b on FIND_IN_SET(a.id,b.pi_id)
				where
				a.item_category_id  in (101) and
				a.status_active=1 and a.is_deleted=0 and
				a.id=$wo_pi_ID";
		}
		if($db_type==1 || $db_type==2)
		{
			$sql ="select b.id as id, a.pi_number as wopi_number, b.lc_number as lc_number, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, '' as pay_mode , a.id as pi_id
				from com_pi_master_details a left join com_btb_lc_pi c on a.id=c.pi_id left join com_btb_lc_master_details b on c.com_btb_lc_master_details_id=b.id
				where
				a.item_category_id in (101) and
				a.status_active=1 and a.is_deleted=0 and
				a.id=$wo_pi_ID";
		}
	}
	else if($receive_basis==2) //WO
	{
 		$sql = "select id, wo_number as wopi_number, '' as lc_number, supplier_id as supplier_id, currency_id as currency_id, source as source, pay_mode, 0 as pi_id
				from wo_non_order_info_mst
				where
				status_active=1 and is_deleted=0 and
				entry_form in(146,147) and
				id=$wo_pi_ID";
	}
	else if($receive_basis==7) //Requisition
	{
 		$sql = "select id, requ_no as wopi_number,'' as lc_number, requisition_date as wopi_date, '' as supplier_id, cbo_currency as currency_id, source as source, pay_mode as pay_mode, 0 as pi_id
				from inv_purchase_requisition_mst
				where status_active=1 and is_deleted=0 and pay_mode=4 and  id=$wo_pi_ID";
	}
	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		echo "$('#txt_wo_pi_req').val('".$row[csf("wopi_number")]."');\n";
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		echo "check_exchange_rate();\n";

		/*if($row[csf("currency_id")]==1)
		{
			echo "$('#txt_exchange_rate').val(1);\n";
		}*/

		echo "$('#cbo_source').val(".$row[csf("source")].");\n";
		if($receive_basis==1)
		{
			$pay_mode=return_field_value("b.pay_mode as pay_mode","com_pi_item_details a, wo_non_order_info_mst b"," a.work_order_id=b.id and a.pi_id=".$row[csf("pi_id")],"pay_mode");
			echo "$('#cbo_pay_mode').val(".$pay_mode.");\n";
		}
		else
		{
			echo "$('#cbo_pay_mode').val(".$row[csf("pay_mode")].");\n";
		}

		echo "$('#txt_lc_no').val('".$row[csf("lc_number")]."');\n";
		if($row[csf("lc_number")]!="")
		{
			echo "$('#hidden_lc_id').val(".$row[csf("id")].");\n";
		}
	}
	exit();
}

//right side product list create here--------------------//
if($action=="show_product_listview")
{
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];

	//$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$item_grp_sql=sql_select("select id, item_name, conversion_factor from lib_item_group");
	$item_conversion_factor=array();
	foreach($item_grp_sql as $row)
	{
		$item_group_arr[$row[csf("id")]]=$row[csf("item_name")];
		$item_conversion_factor[$row[csf("id")]]=$row[csf("conversion_factor")];
	}
	$receive_return_sql = sql_select("select a.received_id, b.prod_id, c.item_group_id, sum(b.cons_quantity) as issue_qnty,b.pi_wo_req_dtls_id as dtls_id 
	from  inv_issue_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and b.transaction_type=3 and a.received_id>0 and a.entry_form=264 and c.entry_form=334 and b.transaction_type=3 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.received_id,b.prod_id, c.item_group_id,b.pi_wo_req_dtls_id");
	foreach($receive_return_sql as $row)
	{
		$receive_rtn_arr[$row[csf("received_id")]][$row[csf("dtls_id")]]+=$row[csf("issue_qnty")]/$item_conversion_factor[$row[csf("item_group_id")]];
	}

	if($receive_basis==1) // pi basis
	{
		$sql = "select a.id,b.id as dtls_id,c.id as prod_id,a.importer_id as company_id, a.supplier_id,c.product_name_details as product_name_details, c.item_group_id, c.sub_group_name, c.section_id , sum(b.quantity) as quantity
		from com_pi_master_details a, com_pi_item_details b, product_details_master c
		where a.id=$wo_pi_ID and a.id=b.pi_id and b.item_prod_id=c.id and c.entry_form=334 and a.status_active=1 and b.status_active=1
		group by a.id,b.id,a.importer_id,a.supplier_id,c.id,c.product_name_details,c.item_group_id, c.sub_group_name, c.section_id";


		$receive_sql = sql_select("select a.id, a.booking_id,b.prod_id,sum(b.order_qnty) as receive_qnty,b.pi_wo_req_dtls_id as dtls_id  from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=263 and a.receive_basis=1 and b.transaction_type=1 group by a.id, a.booking_id, b.prod_id,b.pi_wo_req_dtls_id");
		foreach($receive_sql as $row)
		{
			$receive_arr[$row[csf("booking_id")]][$row[csf("dtls_id")]]+=$row[csf("receive_qnty")]-$receive_rtn_arr[$row[csf("id")]][$row[csf("prod_id")]];
		}

		$lc_tolerance_sql=sql_select("select b.pi_id, a.tolerance from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.status_active=1 and  b.status_active=1 ");
		$tolerance_arr=array();
		foreach($lc_tolerance_sql as $row)
		{
			$tolerance_arr[$row[csf("pi_id")]]=$row[csf("tolerance")];
		}

	}
	else if($receive_basis==2) // wo basis
	{
		$sql = "select  a.id ,b.id as dtls_id,c.id as prod_id,a.company_name as company_id,a.supplier_id, c.product_name_details, c.item_group_id, c.sub_group_name, c.section_id, sum(b.supplier_order_quantity) as quantity
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c
		where a.id=b.mst_id and a.id=$wo_pi_ID and a.pay_mode in (1,4) and c.entry_form=334 and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id,b.id,a.company_name,a.supplier_id, c.id, c.product_name_details,c.item_group_id, c.sub_group_name, c.section_id, b.id order by b.id asc";


		$receive_sql = sql_select("select a.id, a.booking_id,b.prod_id,sum(b.order_qnty) as receive_qnty,b.pi_wo_req_dtls_id as dtls_id from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=263 and a.receive_basis=2 and b.transaction_type=1 group by a.id, a.booking_id, b.prod_id,b.pi_wo_req_dtls_id");
		
		foreach($receive_sql as $row)
		{
			$receive_arr[$row[csf("booking_id")]][$row[csf("dtls_id")]]+=$row[csf("receive_qnty")]-$receive_rtn_arr[$row[csf("id")]][$row[csf("dtls_id")]];
		}
	}
	else if($receive_basis==7) // requisition basis
	{
		$sql = "select  a.id,b.id as dtls_id,c.id as prod_id,a.company_id, '' as supplier_id, c.product_name_details, c.item_group_id, c.sub_group_name,c.section_id, sum(b.quantity) as quantity
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and a.id=$wo_pi_ID and a.pay_mode=4 and c.entry_form=334 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id,b.id,a.company_id,c.id,c.product_name_details,c.item_group_id, c.sub_group_name,c.section_id";


		$receive_sql = sql_select("select a.id, a.booking_id,b.prod_id,sum(b.cons_quantity) as receive_qnty,b.pi_wo_req_dtls_id as dtls_id  from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=263 and a.receive_basis=7 and b.transaction_type=1 group by a.id, a.booking_id, b.prod_id,b.pi_wo_req_dtls_id");
		foreach($receive_sql as $row)
		{
			$receive_arr[$row[csf("booking_id")]][$row[csf("dtls_id")]]+=$row[csf("receive_qnty")]-$receive_rtn_arr[$row[csf("id")]][$row[csf("prod_id")]];
		}
	}
	//echo $sql;
	$result = sql_select($sql);
	$i=1;
	?>
    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="450" rules="all">
        	<thead>
            	<tr>
                	<th width="15">SL</th>
                    <th width="130">Product Name</th>
                    <th width="60">Section</th>
                    <th width="60">Item Group</th>
                    <th width="60">Item Sub Group</th>
                    <th width="40">Wo/RQ /PI Qnty</th>
                    <th width="40">Receive Qnty</th>
                    <th >Balance</th>
                </tr>
            </thead>
            <tbody id="list_view">
            	<? foreach($result as $row)
				{
					if ($i%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					$productName=$row[csf("product_name_details")];
					$quantity=$row[csf("quantity")];
					if($receive_basis==1) // pi basis
					{
						$quantity=$quantity+($quantity*($tolerance_arr[$row[csf("id")]]/100));
					}
					$balance_quantity=$quantity-$receive_arr[$row[csf("id")]][$row[csf("dtls_id")]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $receive_basis."**".$row[csf("id")]."**".$row[csf("company_id")]."**".$row[csf("supplier_id")]."**".$quantity."**".$row[csf("prod_id")]."**".$balance_quantity."**".$row[csf("section_id")]."**".$row[csf("dtls_id")];?>","wo_pi_product_form_input","requires/raw_material_item_receive_controller")' style="cursor:pointer">
                        <td><? echo $i; ?></td>
                        <td><p><? echo $productName; ?>&nbsp;</p></td>
                        <td ><p><? echo $trims_section[$row[csf("section_id")]]; ?>&nbsp;</p></td>
                        <td ><p><? echo $item_group_arr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
                        <td ><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($quantity,0); ?></td>
                        <td align="right"><? echo number_format($receive_arr[$row[csf("id")]][$row[csf("dtls_id")]],0); ?></td>
                        <td align="right"><? echo number_format($balance_quantity,0); ?></td>
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
    $balance_quantity = $ex_data[6];
	$dtls_id = $ex_data[8];
	if($receive_basis==1) // pi basis
	{
		$sql = "select a.importer_id as company_id, a.supplier_id,c.id,c.item_category_id as item_category, c.item_group_id as item_group, c.item_description as item_description,b.id as dtls_id, b.uom as cons_uom, b.rate, sum(b.quantity) as quantity, c.current_stock as global_stock  ,c.brand_name,c.origin,c.model,c.section_id
		from com_pi_master_details a, com_pi_item_details b, product_details_master c
		where a.id=$wo_pi_ID and b.id = $dtls_id and c.id=$product_id and a.id=b.pi_id and b.item_prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=334
		group by c.id,c.item_category_id, c.item_group_id,c.item_description,a.importer_id, a.supplier_id,b.uom,b.rate,c.brand_name,c.origin,c.model,c.current_stock,c.section_id,b.id";
	}
	else if($receive_basis==2) // wo basis
	{
		$sql = "select a.company_name as company_id,a.supplier_id,b.id as dtls_id,c.id,c.item_category_id as item_category, c.item_group_id as item_group, c.item_description as item_description, b.uom as cons_uom, b.rate, sum(b.supplier_order_quantity) as quantity, c.current_stock as global_stock,c.brand_name,c.origin,c.model,c.section_id
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c
		where a.id=b.mst_id and a.id=$wo_pi_ID and b.id = $dtls_id and c.id=$product_id and a.pay_mode in (1,4) and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=334
		group by c.id,c.item_category_id, c.item_group_id,c.item_description,a.company_name,a.supplier_id,b.id,b.uom, b.rate,c.brand_name,c.origin,c.model, c.current_stock,c.section_id";
	}
	else if($receive_basis==7) //  requisition basis
	{
		$sql = "select a.company_id, '' as supplier_id, b.id as dtls_id, c.id, b.item_category as item_category, c.item_group_id as item_group, c.item_description as item_description, b.cons_uom, b.rate, sum(b.quantity) as quantity, c.current_stock as global_stock,c.brand_name,c.origin,c.model,c.section_id
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and a.id=$wo_pi_ID and b.id = $dtls_id and c.id=$product_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=334
		group by c.id,b.item_category, c.item_group_id,c.item_description,a.company_id,a.item_category_id,b.cons_uom, b.rate,c.brand_name,c.origin,c.model, c.current_stock,c.section_id,b.id";
	}
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
	 	echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
		echo "load_drop_down( 'requires/raw_material_item_receive_controller',".$row[csf("item_category")].", 'load_drop_down_itemgroup', 'item_group_td' );";
		echo "$('#cbo_item_group').val(".$row[csf("item_group")].");\n";
		echo "$('#txt_item_desc').val('".$row[csf("item_description")]."');\n";
		echo "$('#current_prod_id').val('".$row[csf("id")]."');\n";
		echo "$('#current_dtls_id').val('".$row[csf("dtls_id")]."');\n";
		echo "$('#txt_glob_stock').val('".$row[csf("global_stock")]."');\n";
		//echo "$('#txt_serial_no').val(".$row[csf("")].");\n";
		echo "$('#cbo_uom').val(".$row[csf("cons_uom")].");\n";
 		echo "$('#txt_rate').val('".$row[csf("rate")]."');\n";
		echo "$('#txt_brand').val('".$row[csf("brand_name")]."');\n";
		echo "$('#cbo_origin').val('".$row[csf("origin")]."');\n";
		echo "$('#txt_model').val('".$row[csf("model")]."');\n";//new dev
		echo "$('#cbo_section').val('".$row[csf("section_id")]."');\n";//new dev
        echo "$('#txt_order_qty').val('".$balance_quantity."');\n";
		$item_category_id=$row[csf("item_category")];
		$company_id=$row[csf("company_id")];
		echo "get_php_form_data($item_category_id+'_'+$company_id+'_'+$receive_basis, 'set_rate_credential', 'requires/raw_material_item_receive_controller' );\n";

		echo "$('#cbo_item_category').attr('disabled',true);\n";
		echo "$('#cbo_item_group').attr('disabled',true);\n";
		echo "$('#txt_item_desc').attr('disabled',true);\n";

		echo "fn_calile();\n";
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
                   		<!-- Hidden field here-------->
                        <input type="hidden" id="hidden_tbl_id" value="" />
                        <input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number" />
                        <!-- ---------END------------->
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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lc_search_list_view', 'search_div', 'raw_material_item_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where lc_number LIKE '%$search_string%' and importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	}
	else if($cbo_search_by==1 && $txt_search_common!="") //supplier
	{
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where supplier_id='$search_string' and importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
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

	//$sql="select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category=$category and item_group=$group and status_active=1 and is_deleted=0 order by id limit 1";

	if($db_type==0)
	{
		$sql="select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category=$category and item_group=$group and status_active=1 and is_deleted=0 order by id limit 1";
	}
	else
	{
		$sql="select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category=$category and item_group=$group and status_active=1 and is_deleted=0 and rownum <= 2 order by id desc";
	}

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
            <!-- Hidden field here-------->
			<input type="hidden" id="txt_string" value="" />
            <input type="hidden" id="txt_qty" value="" />
			<!-- ---------END------------->
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
		$("#section").val(splitArr[6]);
		$("#order_uom").val(splitArr[7]);
		parent.emailwindow.hide();
	}
</script>
</head>
<body>
	<div align="center" style="width:100%" >
	<form name="item_popup_1"  id="item_popup_1">
		<th colspan="8"><?  echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
      <?
		  $entry_cond="";
		  if(str_replace("'","",$item_category)==4) $entry_cond="and entry_form=20";
 		  /*$sql="select id, item_code, item_description, item_size,current_stock,brand_name,origin from product_details_master where status_active=1 and is_deleted=0 and company_id=$company_id and item_category_id=$item_category and item_group_id=$item_group $entry_cond";*/
 		  /*new dev*/
 		  $sql="select id, item_code, item_description, item_size, current_stock, brand_name, origin, model, sub_group_name, section_id, order_uom from product_details_master where status_active=1 and is_deleted=0 and entry_form=334 and company_id=$company_id and item_category_id=$item_category and item_group_id=$item_group $entry_cond";
		 // echo $sql;die;
		$arr=array(4=>$trims_section);
 		echo  create_list_view ( "list_view","Product Id,Item Account,Item Code,Item Description,Section,Item Size,Sub Group", "50,110,70,200,120,70","780","250",0, $sql, "js_set_value", "id,item_description,current_stock,brand_name,origin,model,section_id,order_uom", "", 1, "0,0,0,0,section_id,0,0", $arr, "id,item_account,item_code,item_description,section_id,item_size,sub_group_name", "", 'setFilterGrid("list_view",-1);');
    ?>
     <input type="hidden" id="item_description_td" />
     <input type="hidden" id="product_id_td" />
     <input type="hidden" id="current_stock" />
     <input type="hidden" id="brand_name" />
     <input type="hidden" id="origin" />
     <input type="hidden" id="model" />
     <input type="hidden" id="section" />
     <input type="hidden" id="order_uom" />
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
	$variable_set_invent=return_field_value("user_given_code_status","variable_settings_inventory","company_name=$cbo_company_id and variable_list=19 and item_category_id=$cbo_item_category","user_given_code_status");

	//echo "20** select user_given_code_status from variable_settings_inventory where company_name=$cbo_company_id and variable_list=19 and item_category_id=$cbo_item_category";die;

	if(str_replace("'","",$txt_amount)*1 <= 0)
	{
		echo "20**Receive Amount Not Allow Less Than Or Equal Zero";die;
	}

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0**0"; die;}

		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		if($variable_set_invent==1)
		{
			$challan_no=str_replace("'","",$txt_challan_no);
			if($challan_no!="")
			{
				$variable_set_invent=return_field_value("a.id as id"," inv_gate_in_mst a,  inv_gate_in_dtl b","a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=$cbo_item_category  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
				if(empty($variable_set_invent))
				{
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}

				

			}
		}
		//---------------End Check Receive control on Gate Entry---------------------------//
		/*if($db_type==0)
		{
			$concattS = explode(",",return_field_value(" concat(trim_uom,',',conversion_factor) as cons_uom","lib_item_group","id=$cbo_item_group","cons_uom"));
		}
		if($db_type==2)
		{
			$concattS = explode(",",return_field_value("(trim_uom || ',' ||conversion_factor) as cons_uom","lib_item_group","id=$cbo_item_group","cons_uom"));
		}*/

		if($db_type==0)
		{
			$concattS = explode(",",return_field_value(" concat(unit_of_measure,',',conversion_factor) as cons_uom","product_details_master","id=$current_prod_id","cons_uom"));
		}
		if($db_type==2)
		{
			$concattS = explode(",",return_field_value("(unit_of_measure || ',' ||conversion_factor) as cons_uom","product_details_master","id=$current_prod_id","cons_uom"));
		}


		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];


		//echo $cons_uom."hy".$conversion_factor.$current_prod_id; die;

		//---------------Check Meterial Over Receive control Start---------------------------//
		$variable_over_rcv_percent=return_field_value("over_rcv_percent","variable_inv_ile_standard"," company_name = $cbo_company_id and category=$cbo_item_category and variable_list = 23","over_rcv_percent");
		//echo "10**$variable_over_rcv_percent";die;
		$rcv_basis=str_replace("'","",$cbo_receive_basis);
		$wo_pi_req_id=str_replace("'","",$txt_wo_pi_req_id);
		$cr_prod_id=str_replace("'","",$current_prod_id);
		if($variable_over_rcv_percent>0 && $rcv_basis !=4 && $rcv_basis !=6)
		{
			$totalRtnQnty = return_field_value("sum(c.cons_quantity) as bal","inv_receive_master a, inv_issue_master b, inv_transaction c"," a.id=b.received_id and c.mst_id=b.id and a.booking_id=".$wo_pi_req_id." and c.prod_id=".$cr_prod_id." and a.receive_basis=2 and b.entry_form=264 and c.transaction_type=3 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
			$totalRtnQnty=$totalRtnQnty/$conversion_factor;
			//txt_receive_qty current_prod_id
			$prev_rcv_sql=sql_select(" select sum(b.order_qnty) as qnty from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.entry_form =263 and b.transaction_type=1 and a.receive_basis = $rcv_basis and a.booking_id = $wo_pi_req_id and b.item_category=$cbo_item_category and b.prod_id=$cr_prod_id and a.status_active=1 and b.status_active=1");
			$prev_rcv_qnty=$prev_rcv_sql[0][csf("qnty")]-$totalRtnQnty;
			$current_receive_qty=str_replace("'","",$txt_receive_qty);
			$tot_qnty=$prev_rcv_qnty+$current_receive_qty;

			if($rcv_basis==1)
			{
				$wo_pi_req_sql=sql_select(" select sum(b.quantity) as quantity from com_pi_item_details b where b.status_active=1 and b.pi_id=$wo_pi_req_id and b.item_prod_id=$cr_prod_id");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];

				$lc_tolerance_sql=sql_select("select b.pi_id, a.tolerance from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and b.pi_id=$wo_pi_req_id and a.status_active=1 and  b.status_active=1 ");
				$tolerance=$lc_tolerance_sql[0][csf("tolerance")];
				$wo_pi_req_qnty=$wo_pi_req_qnty+($wo_pi_req_qnty*($tolerance/100));
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

			$allow_qnty=($wo_pi_req_qnty+(($wo_pi_req_qnty/100)*$variable_over_rcv_percent));
			if($tot_qnty>$allow_qnty)
			{
				echo "30** MRR Quantity Not Allow More Then PI/Wo/Req Allowed Quantity.";
				disconnect($con);
				die;
			}


		}

		//---------------Check Meterial Over Receive control End---------------------------//


		//---------------Check Receive date with Last Transaction date-------------//
		$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$current_prod_id and store_id=$cbo_store_name and status_active = 1", "max_date");
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
		//---------------Check Last Transaction date End -------------//


 		//---------------Check Duplicate product in Same return number ------------------------//
		// $duplicate = is_duplicate_field("b.id","inv_receive_master a, inv_transaction b","a.id=b.mst_id and a.id=$hidden_mrr_id and b.prod_id=$current_prod_id and b.transaction_type=1  and a.status_active=1 and b.status_active=1");
		// if($duplicate==1)
		// {
		// 	echo "20**Duplicate Product is Not Allow in Same MRR Number.";
		// 	//check_table_status( $_SESSION['menu_id'],0);
		// 	disconnect($con);
		// 	die;
		// }
		//------------------------------Check product END---------------------------------------//

		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty from product_details_master where id=$current_prod_id " );
		$presentStock=$presentStockValue=$presentAvgRate=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock		=$result[csf("current_stock")];
			$presentStockValue	=$result[csf("stock_value")];
			$presentAvgRate		=$result[csf("avg_rate_per_unit")];
			$product_name_details 	=$result[csf("product_name_details")];
			$available_qnty			=$result[csf("available_qnty")];
		}
		//----------------Check Product ID END---------------------//

		if(str_replace("'","",$txt_mrr_no)!="")
		{
			$new_recv_number[0] = str_replace("'","",$txt_mrr_no);
			$id=str_replace("'","",$hidden_mrr_id);
			//master table UPDATE here START----------------------//
			$field_array1="receive_basis*receive_date*booking_id*challan_no*store_id*exchange_rate*currency_id*supplier_id*lc_no*lc_sc_no*pay_mode*source*supplier_referance*receive_purpose*loan_party*remarks* boe_mushak_challan_no*boe_mushak_challan_date*gate_entry_no*gate_entry_date*challan_date*updated_by*update_date";
			$data_array1="".$cbo_receive_basis."*".$txt_receive_date."*".$txt_wo_pi_req_id."*".$txt_challan_no."*".$cbo_store_name."*".$txt_exchange_rate."*".$cbo_currency."*".$cbo_supplier."*".$hidden_lc_id."*".$txt_lc_no."*".$cbo_pay_mode."*".$cbo_source."*".$txt_sup_ref."*".$cbo_receive_purpose."*".$cbo_loan_party."*".$txt_remarks."*".$txt_boe_mushak_challan_no."*".$txt_boe_mushak_challan_date."*".$txt_gate_entry_no."*".$txt_gate_entry_date."*".$txt_Challan_date."*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;die;
			//$rID=sql_update("inv_receive_master",$field_array,$data_array,"id",$id,1);
			//master table UPDATE here END---------------------------------------//
		}
		else
		{
			//master table entry here START---------------------------------------//txt_remarks
			//$id=return_next_id("id", "inv_receive_master", 1);
			//$new_recv_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GIR', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form='263' $mrr_date_check order by id DESC ", "recv_number_prefix", "recv_number_prefix_num" ));



			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$cbo_company_id),'RMIR',263,date("Y",time())));

			//echo "10**jahid**$id"."**";print_r($new_recv_number);disconnect($con);die;

 			$field_array1="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, company_id, receive_basis, receive_date, booking_id, challan_no, store_id, exchange_rate, currency_id, supplier_id, lc_no,lc_sc_no, pay_mode, source, supplier_referance,receive_purpose,loan_party, remarks, boe_mushak_challan_no, boe_mushak_challan_date, gate_entry_no, gate_entry_date,challan_date, inserted_by, insert_date"; 
			$data_array1="(".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',263,".$cbo_company_id.",".$cbo_receive_basis.",".$txt_receive_date.",".$txt_wo_pi_req_id.",".$txt_challan_no.",".$cbo_store_name.",".$txt_exchange_rate.",".$cbo_currency.",".$cbo_supplier.",".$hidden_lc_id.",".$txt_lc_no.",".$cbo_pay_mode.",".$cbo_source.",".$txt_sup_ref.",".$cbo_receive_purpose.",".$cbo_loan_party.",".$txt_remarks.",".$txt_boe_mushak_challan_no.",".$txt_boe_mushak_challan_date.",".$txt_gate_entry_no.",".$txt_gate_entry_date.",".$txt_Challan_date.",'".$user_id."','".$pc_date_time."')";
			//echo "20**".$field_array."<br>".$data_array;die;
			//$rID=sql_insert("inv_receive_master",$field_array,$data_array,1);
			//master table entry here END---------------------------------------//
		}

		//echo "insert into inv_receive_master ($field_array1) values $data_array1";die;




		//details table entry here START-----------------------------------//
		//echo $cbo_item_group;die;
		$rate = str_replace("'","",$txt_rate);
		$txt_ile = str_replace("'","",$txt_ile);
		$txt_receive_qty = str_replace("'","",$txt_receive_qty);
		$ile = ($txt_ile/$rate)*100; // ile cost to ile
		$ile_cost = str_replace("'","",$txt_ile); //ile cost = (ile/100)*rate
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
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
		$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$field_array_trams = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,pi_wo_req_dtls_id,item_category,transaction_type,transaction_date,store_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,cons_rate,cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,floor_id,room,rack,self,bin_box,expire_date,remarks,trans_uom,no_of_qty,inserted_by,insert_date";
 		$data_array_trans = "(".$dtlsid.",".$id.",".$cbo_receive_basis.",".$txt_wo_pi_req_id.",".$cbo_company_id.",".$cbo_supplier.",".$current_prod_id.",".$current_dtls_id.",".$cbo_item_category.",1,".$txt_receive_date.",".$cbo_store_name.",".$cbo_uom.",".$txt_receive_qty.",".$txt_rate.",".$ile.",".$ile_cost.",".$txt_amount.",".$cons_uom.",".$con_quantity.",".$cons_rate.",".$con_ile.",".$con_ile_cost.",".$con_amount.",".$con_quantity.",".$con_amount.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_warranty_date.",".$txt_referance.",".$cbo_receive_uom.",".$txt_no_of_qty.",'".$user_id."','".$pc_date_time."')";
		
		//echo "INSERT INTO inv_transaction (".$field_array.") VALUES ".$data_array.""; die;
		//echo "**".$field_array."<br>".$data_array;die;
		//$dtlsrID = sql_insert("inv_transaction",$field_array_trams,$data_array_trans,1);
		//yarn details table entry here END-----------------------------------//

		//product master table data UPDATE START----------------------------------------------------------//
		$stock_value 	= $domestic_rate*$con_quantity;
  		$currentStock   = $presentStock+$con_quantity;
		$available_qnty = $available_qnty+$con_quantity;
		$StockValue	 = $presentStockValue+$stock_value;
                if($currentStock > 0){
                    $avgRate		= $StockValue/$currentStock;
                }else{
                    $avgRate		= 0;
                }

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
		//echo "10**".$rID;die;
		//echo "10**".$new_recv_number[0]."**".$id;die;
		//echo "10**$rID && $dtlsrID && $prodUpdate && $serial_dtlsrID".jahid;die;

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
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$hidden_mrr_id=str_replace("'","",$hidden_mrr_id);
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0**0"; die;}
		//table lock here
		if( str_replace("'","",$update_id) == "" )
		{
			echo "15";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			exit();
		}

		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		if($variable_set_invent==1)
		{
			$challan_no=str_replace("'","",$txt_challan_no);
			if($challan_no!="")
			{
				$variable_set_invent=return_field_value("a.id as id"," inv_gate_in_mst a,  inv_gate_in_dtl b","a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=$cbo_item_category  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
				if(empty($variable_set_invent))
				{
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}

			}
		}
		//---------------End Check Receive control on Gate Entry---------------------------//


		//---------------Check Meterial Over Receive control Start---------------------------//
		$variable_over_rcv_percent=return_field_value("over_rcv_percent","variable_inv_ile_standard"," company_name = $cbo_company_id and category=$cbo_item_category and variable_list = 23","over_rcv_percent");
		//echo "10**$variable_over_rcv_percent";die;
		$rcv_basis=str_replace("'","",$cbo_receive_basis);

		if($variable_over_rcv_percent>0 && $rcv_basis !=4 && $rcv_basis !=6)
		{
			//txt_receive_qty current_prod_id
			$wo_pi_req_id=str_replace("'","",$txt_wo_pi_req_id);
			$cr_prod_id=str_replace("'","",$current_prod_id);
			$prev_rcv_sql=sql_select(" select sum(b.order_qnty) as qnty from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.entry_form =263 and b.transaction_type=1 and a.receive_basis = $rcv_basis and a.booking_id = $wo_pi_req_id and b.item_category=$cbo_item_category and b.prod_id=$cr_prod_id and a.status_active=1 and b.status_active=1 and b.id<>$update_id");
			$prev_rcv_qnty=$prev_rcv_sql[0][csf("qnty")];
			$current_receive_qty=str_replace("'","",$txt_receive_qty);
			$tot_qnty=$prev_rcv_qnty+$current_receive_qty;

			if($rcv_basis==1)
			{
				$wo_pi_req_sql=sql_select(" select sum(b.quantity) as quantity from com_pi_item_details b where b.status_active=1 and b.pi_id=$wo_pi_req_id and b.item_prod_id=$cr_prod_id");
				$wo_pi_req_qnty=$wo_pi_req_sql[0][csf("quantity")];

				$lc_tolerance_sql=sql_select("select b.pi_id, a.tolerance from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and b.pi_id=$wo_pi_req_id and a.status_active=1 and  b.status_active=1 ");
				$tolerance=$lc_tolerance_sql[0][csf("tolerance")];
				$wo_pi_req_qnty=$wo_pi_req_qnty+($wo_pi_req_qnty*($tolerance/100));
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

			$allow_qnty=($wo_pi_req_qnty+(($wo_pi_req_qnty/100)*$variable_over_rcv_percent));
			if($tot_qnty>$allow_qnty)
			{
				echo "30** MRR Quantity Not Allow More Then PI/Wo/Req Allowed Quantity.";
				disconnect($con);
				die;
			}


		}

		//---------------Check Meterial Over Receive control End---------------------------//


		$issue_check = return_field_value("id","inv_mrr_wise_issue_details","recv_trans_id=$update_id and status_active=1","id");
		if( $issue_check>0)
		{
			echo "20**This Product Already Issue. Update Not Allow.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
		else
		{
			//check update id

			//---------------Check Receive date with Last Transaction date-------------//
			$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$current_prod_id and store_id=$cbo_store_name and id <> $update_id and status_active = 1", "max_date");
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
			//---------------Check Last Transaction date End -------------//




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
				$presentStock		  	=$result[csf("current_stock")];
				$presentStockValue	 	=$result[csf("stock_value")];
				$presentAvgRate			=$result[csf("avg_rate_per_unit")];
				$product_name_details  	=$result[csf("product_name_details")];
				$available_qnty			=$result[csf("available_qnty")];
			}
			//----------------Check Product ID END---------------------//

			//yarn master table UPDATE here START----------------------//booking_id$txt_wo_pi_req_id
			if($update_id!="")
			{
				$field_array_receive="receive_basis*receive_date*booking_id*challan_no*receive_purpose*loan_party*store_id*exchange_rate*currency_id*supplier_id*lc_no*lc_sc_no*pay_mode*source*supplier_referance*remarks*boe_mushak_challan_no*boe_mushak_challan_date*gate_entry_no*gate_entry_date*challan_date*updated_by*update_date";
				$data_array_receive="".$cbo_receive_basis."*".$txt_receive_date."*".$txt_wo_pi_req_id."*".$txt_challan_no."*".$cbo_receive_purpose."*".$cbo_loan_party."*".$cbo_store_name."*".$txt_exchange_rate."*".$cbo_currency."*".$cbo_supplier."*".$hidden_lc_id."*".$txt_lc_no."*".$cbo_pay_mode."*".$cbo_source."*".$txt_sup_ref."*".$txt_remarks."*".$txt_boe_mushak_challan_no."*".$txt_boe_mushak_challan_date."*".$txt_gate_entry_no."*".$txt_gate_entry_date."*".$txt_Challan_date."*'".$user_id."'*'".$pc_date_time."'";
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

				/*	if($db_type==0)
				{
					$concattS = explode(",",return_field_value("concat(trim_uom,',',conversion_factor) as concat_val","lib_item_group","id=$cbo_item_group","concat_val"));
				}
				else
				{
					$concattS = explode(",",return_field_value("(trim_uom || ',' || conversion_factor) as concat_val","lib_item_group","id=$cbo_item_group","concat_val"));
				}
				*/

				if($db_type==0)
				{
					$concattS = explode(",",return_field_value("concat(unit_of_measure,',',conversion_factor) as concat_val","product_details_master","id=$current_prod_id","concat_val"));
				}
				else
				{
					$concattS = explode(",",return_field_value("(unit_of_measure || ',' || conversion_factor) as concat_val","product_details_master","id=$current_prod_id","concat_val"));
				}




				$cons_uom = $concattS[0];
				$conversion_factor = $concattS[1];

				//echo "30**".$cons_uom."".$conversion_factor; die;
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

				$field_array_trans = "receive_basis*pi_wo_batch_no*company_id*supplier_id*prod_id*item_category*transaction_type*transaction_date*store_id*order_uom*order_qnty*order_rate*order_ile*order_ile_cost*order_amount*cons_uom*cons_quantity*cons_rate*cons_ile*cons_ile_cost*cons_amount*balance_qnty*balance_amount*floor_id*room*rack*self*bin_box*expire_date*remarks*trans_uom*no_of_qty*updated_by*update_date";
				$data_array_trans = "".$cbo_receive_basis."*".$txt_wo_pi_req_id."*".$cbo_company_id."*".$cbo_supplier."*".$current_prod_id."*".$cbo_item_category."*1*".$txt_receive_date."*".$cbo_store_name."*".$cbo_uom."*".$txt_receive_qty."*".$txt_rate."*".$ile."*".$ile_cost."*".$txt_amount."*".$cons_uom."*".$con_quantity."*".$cons_rate."*".$con_ile."*".$con_ile_cost."*".$con_amount."*".$con_quantity."*".$con_amount."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_warranty_date."*".$txt_referance."*".$cbo_receive_uom."*".$txt_no_of_qty."*'".$user_id."'*'".$pc_date_time."'";
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
					echo "30**Stock cannot be less than zero.";
					disconnect($con);
					die;
				}
				$avgRate		 = number_format($StockValue/$currentStock,$dec_place[3],'.','');
				$data_array_product = "".$avgRate."*".$con_quantity."*".$currentStock."*".number_format($StockValue,$dec_place[4],'.','')."*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'";
				//$prodUpdate = sql_update("product_details_master",$field_array_product,$data_array_product,"id",$current_prod_id,1);
			}
			else
			{
				//before
				$updateID_array=$update_data=array();

				if($adj_beforeStock<0) //Aziz
				{
					echo "30**Stock cannot be less than zero.";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}


				//echo "10**".$adj_beforeAvgRate."==sh";die;
				//$test_data.=$adj_beforeAvgRate.",";
				//if( $adj_beforeAvgRate<0 || $adj_beforeAvgRate=="" || $adj_beforeAvgRate=="nan") $test_data="jahid";else $test_data="nahid";
				if( $adj_beforeAvgRate<0 || $adj_beforeAvgRate=="" || $adj_beforeAvgRate=="nan") $adj_beforeAvgRate=0; else $adj_beforeAvgRate=number_format($adj_beforeAvgRate,$dec_place[3],'.','');
				$updateID_array[]=$before_prod_id;
				$update_data[$before_prod_id]=explode("*",("".$adj_beforeAvgRate."*0*".$adj_beforeStock."*".number_format($adj_beforeStockValue,$dec_place[4],'.','')."*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'"));
				//current
				$presentStock 		= $presentStock+$con_quantity;
				$available_qnty  	= $available_qnty+$con_quantity;
				$presentStockValue	= $presentStockValue+$con_amount;
				$presentAvgRate		= number_format($presentStockValue/$presentStock,$dec_place[3],'.','');

				$updateID_array[]=$current_prod_id;
				if($presentAvgRate<0 || $presentAvgRate=="" || $presentAvgRate=="nan") $presentAvgRate=0;
				$update_data[$current_prod_id]=explode("*",("".$presentAvgRate."*0*".$presentStock."*".number_format($presentStockValue,$dec_place[4],'.','')."*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'"));
				//$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product,$update_data,$updateID_array),1);
			}
			//$test_data=chop($test_data,",");

			//echo "10**".$test_data."==".$adj_beforeAvgRate;die;
			//------------------ product_details_master END---------------------------------------------------//

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
			if($before_prod_id==$current_prod_id)
			{
				$prodUpdate = sql_update("product_details_master",$field_array_product,$data_array_product,"id",$current_prod_id,1);
			}
			else
			{
				//execute_query execute_query
				$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product,$update_data,$updateID_array));
			}

			//echo "10**$prodUpdate";die;

			$serial_dtlsrID=true;
			if(str_replace("'","",$txt_serial_no)!="")
			{
				$serial_dtlsrID = sql_insert("inv_serial_no_details",$serial_field_array,$serial_data_array,1);
			}

			//echo "10**$rID && $dtlsrID && $prodUpdate && $deleteSerial && $serial_dtlsrID";die;

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
		//check_table_status( $_SESSION['menu_id'],0);
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
			echo "16**Delete not allowed. Problem occurred";
			disconnect($con);
			die;
		}
		else
		{
			$update_id = str_replace("'","",$update_id);
			$product_id = str_replace("'","",$current_prod_id);
			if( str_replace("'","",$update_id) == "" )
			{
				echo "16**Delete not allowed. Problem occurred";
				disconnect($con);
				die;
			}

			//echo "10**select id from inv_transaction where transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id"; die;
			$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id ","id");
			if($chk_next_transaction !="")
			{
				echo "18**Delete not allowed.This item is used in another transaction";
				disconnect($con);
				die;
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
				$adj_beforeAvgRate=$adj_beforeStockValue = 0;
				
				//if($adj_beforeStockValue>0 && $adj_beforeStock>0)
				if ( $adj_beforeStock != 0 ) 
				{
					$adj_beforeStockValue		=$beforeStockValue-$beforeAmount;
					$adj_beforeAvgRate			=number_format(($adj_beforeStockValue/$adj_beforeStock),$dec_place[3],'.','');
				}
				else
				{
					$adj_beforeAvgRate			=0;
				}

				$field_array_product="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
				$data_array_product = "".$adj_beforeAvgRate."*".$adj_beforeStock."*".number_format($adj_beforeStockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";
 				$sql_mst = sql_select("select id from inv_transaction where status_active=1 and is_deleted=0 and transaction_type=1 and mst_id=$mst_id");
				
				if(count($sql_mst)==1)
				{
					$field_array_mst="updated_by*update_date*status_active*is_deleted";
					$data_array_mst="".$user_id."*'".$pc_date_time."'*0*1";

					$rID=sql_update("inv_receive_master",$field_array_mst,$data_array_mst,"id",$mst_id,1);
					 $resetLoad=1;
				}
				else
				{
					$rID=1;
					 $resetLoad=2;
				}
				

				$field_array_trans="updated_by*update_date*status_active*is_deleted";
				$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";
				$rID2=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
				$rID3=sql_update("product_details_master",$field_array_product,$data_array_product,"id",$product_id,1);

				//$rID=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
				//$rID2=sql_update("product_details_master",$field_array_product,$data_array_product,"id",$product_id,1);
			}
			/*$rID = sql_update("inv_receive_master",'status_active*is_deleted','0*1',"id*item_category","$mst_id*$cbo_item_category",0);
			$dtlsrID = sql_update("inv_transaction",'status_active*is_deleted','0*1',"mst_id*item_category","$mst_id*$cbo_item_category",0);
			$srID = sql_update("inv_serial_no_details",'status_active*is_deleted','0*1',"mst_id*entry_form","$mst_id*20",1);*/
		}
		//echo "10**".$rID."**".$rID2; die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id)."**".$resetLoad;;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id)."**".$resetLoad;;
			}
		}
		if($db_type==2 || $db_type==1 )
		{

			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id)."**".$resetLoad;;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_mrr_no)."**".str_replace("'","",$hidden_mrr_id)."**".$resetLoad;
			}
		}
		disconnect($con);
		die;
	}
}


function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit,$return_query='')
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	echo "10**".$strQuery; die;
	if($return_query==1){return $strQuery ;}
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}

if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
	function js_set_value(mrr)
	{
		var splitArr = mrr.split("__");
 		$("#hidden_recv_number").val(splitArr[0]); // mrr number
		 $("#hidden_is_posted_account").val(splitArr[1]); //is posted account
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
 							 echo create_drop_down( "cbo_supplier", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  b.party_type in(1,5,6,7,8,30,36,37,39,92) $supplier_credential_cond  and a.status_active=1 and a.is_deleted=0  group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_list_view', 'search_div', 'raw_material_item_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
            </tr>
        	<tr>
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here -->
                     <input type="hidden" id="hidden_recv_number" value="hidden_recv_number" />
                     <input type="hidden" id="hidden_is_posted_account" value="" />

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

	$sql_cond="";
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
	//if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";

	//echo $credientian_cond;die;

	$sql = "SELECT a.id as rcv_id, a.recv_number, a.supplier_id, a.challan_no, c.lc_number,a.lc_sc_no,a.lc_no, a.receive_date, a.receive_basis, sum(b.cons_quantity) as receive_qnty, a.is_posted_account from inv_transaction b, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id where a.id=b.mst_id and a.entry_form=263 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.transaction_type=1 and b.item_category in(101) $sql_cond  $credientian_cond
	group by a.id,b.mst_id,a.recv_number,a.supplier_id,a.challan_no,c.lc_number,a.lc_sc_no,a.lc_no,a.receive_date,a.receive_basis, a.is_posted_account order by b.mst_id desc";
	//echo $sql;
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$arr=array(1=>$supplier_arr,5=>$receive_basis_arr);

	$result = sql_select($sql);

	// echo create_list_view("list_view", "MRR No, Supplier Name, Challan No, LC No, Receive Date, Receive Basis, Receive Qnty","120,120,120,120,120,100,100","900","260",0, $sql , "js_set_value", "rcv_id", "", 1, "0,supplier_id,0,0,0,receive_basis,0", $arr, "recv_number,supplier_id,challan_no,lc_number,receive_date,receive_basis,receive_qnty", "",'','0,0,0,0,0,0,1') ;
	?>

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
            <thead>
                <th width="40">SL No</th>
                <th width="120">MRR No</th>
                <th width="120">Supplier Name</th>
                <th width="120">Challan No</th>
                <th width="120">LC No</th>
                <th width="120">Receive Date</th>
                <th width="100">Receive Basis</th>
                <th >Receive Qnty</th>
            </thead>
         </table>
         <div style="width:900px; max-height:250px; overflow-y:scroll">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="list_view">
             <?
             $i=1;
             foreach ($result as $row)
             {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('rcv_id')]."__".$row[csf('is_posted_account')]; ?>')">

                        <td width="40">
						<? echo "$i"; ?>
                        </td>
                        <td width="120"><p><? echo $row[csf('recv_number')];?></p></td>
                        <td width="120"><p><? echo $supplier_arr[$row[csf('supplier_id')]];?></p></td>
                        <td width="120"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="120"><p><?
						if($row[csf('lc_no')])
						{
							echo $row[csf('lc_number')];
						}else
						{
							echo $row[csf('lc_sc_no')];
						}

						?> </p></td>
                        <td width="120" align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
                        <td  align="center"><p><? echo $row[csf('receive_qnty')]; ?></p></td>

                    </tr>
                    <?
             		$i++;
             }
             ?>
            </table>
	<?
	exit();


}

if($action=="populate_data_from_data")
{
	$sql = "select id,recv_number,company_id,receive_basis,receive_purpose,loan_party,receive_date,booking_id,challan_no,store_id,lc_no,supplier_id,exchange_rate,currency_id,lc_no,lc_sc_no,pay_mode,source,remarks,supplier_referance,is_posted_account, boe_mushak_challan_no, boe_mushak_challan_date,challan_date, gate_entry_no, gate_entry_date
			from inv_receive_master
			where id='$data' and entry_form=263";
			//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#hidden_mrr_id').val(".$row[csf("id")].");\n";
		echo "$('#txt_mrr_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo"load_drop_down( 'requires/raw_material_item_receive_controller', ".$row[csf("company_id")].", 'load_drop_down_supplier', 'supplier' );\n";
		echo "$('#cbo_receive_basis').val(".$row[csf("receive_basis")].");\n";
		echo "$('#cbo_receive_purpose').val(".$row[csf("receive_purpose")].");\n";
		echo "$('#cbo_loan_party').val(".$row[csf("loan_party")].");\n";
		echo "$('#txt_receive_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_boe_mushak_challan_no').val('".$row[csf("boe_mushak_challan_no")]."');\n";
		echo "$('#txt_boe_mushak_challan_date').val('".change_date_format($row[csf("boe_mushak_challan_date")],1)."');\n";
		echo "$('#txt_Challan_date').val('".change_date_format($row[csf("challan_date")],1)."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_101', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		echo "$('#txt_sup_ref').val('".$row[csf("supplier_referance")]."');\n";
		echo "$('#hidden_posted_in_account').val('".$row[csf("is_posted_account")]."');\n";
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
		echo "$('#txt_gate_entry_no').val('".$row[csf("gate_entry_no")]."');\n";
		echo "$('#txt_gate_entry_date').val('".change_date_format($row[csf("gate_entry_date")],1)."');\n";

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
		if($row[csf("lc_no")])
		{
			echo "$('#txt_lc_no').val('".$lcNumber."');\n";
		}
		else
		{
			echo "$('#txt_lc_no').val('".$row[csf("lc_sc_no")]."');\n";
			echo "$('#hidden_lc_id').val('');\n";
		}


		//right side list view
		echo "show_list_view('".$row[csf("id")]."','show_dtls_list_view','list_container','requires/raw_material_item_receive_controller','');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_general_item_receive_entry',1);\n";
 	}
	exit();
}

if($action=="show_dtls_list_view")
{
	$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$sql = "select a.recv_number, b.id, b.receive_basis,b.pi_wo_batch_no,c.product_name_details,c.lot,b.order_uom,b.order_qnty,b.order_rate,b.order_ile_cost,b.order_amount,b.cons_amount, c.item_group_id , c.item_description , c.item_size
	from inv_receive_master a, inv_transaction b,  product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.status_active=1 and b.status_active=1 and a.entry_form=263 and c.entry_form=334 and a.id=$data";
	//echo $sql;//die;
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

					$item_description=$item_group_arr[$row[csf("item_group_id")]]." ".$row[csf("item_description")]." ".$row[csf("item_size")];

 					?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")];?>","child_form_input_data","requires/raw_material_item_receive_controller")' style="cursor:pointer" >
                        <td width="50"><?php echo $i; ?></td>
                        <td width="300"><p><?php echo $item_description; ?></p></td>
                        <td width="100"><p><?php echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p></td>
                        <td width="100" align="right"><p><?php echo number_format($row[csf("order_qnty")],4); ?></p></td>
                        <td width="100" align="right"><p><?php echo number_format($row[csf("order_rate")],4); ?></p></td>
                        <td width="100" align="right"><p><?php echo $row[csf("order_ile_cost")]; ?></p></td>
                        <td width="100" align="right"><p><?php echo number_format($row[csf("order_amount")],2); ?></p></td>
                        <td width="" align="right"><p><?php echo number_format($row[csf("cons_amount")],2); ?></p></td>
                   </tr>
                <? $i++; } ?>
                	<tfoot>
                        <th colspan="3">Total</th>
                        <th><?php echo number_format($totalQnty,4); ?></th>
                        <th colspan="2"></th>
                        <th><?php echo number_format($totalAmount,2); ?></th>
                        <th><?php echo number_format($totalbookCurr,2); ?></th>
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

	/*$sql = "select a.currency_id, a.booking_id, a.receive_basis, a.exchange_rate, b.id, b.pi_wo_batch_no, b.prod_id, b.brand_id, c.lot, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount, b.expire_date, b.room,b.rack, b.self,b.bin_box,c.item_category_id,c.item_group_id,c.item_description,c.current_stock as global_stock,b.remarks,c.brand_name,c.origin
	from inv_receive_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id'  and a.status_active=1 and b.status_active=1";*/
	/*new dev*/

	$sql = "select a.company_id, a.currency_id, a.booking_id, a.receive_basis, a.receive_purpose, a.loan_party, a.exchange_rate, b.id, b.pi_wo_batch_no, b.prod_id, b.brand_id, c.lot, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount, b.expire_date, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.item_category_id, c.item_group_id, c.item_description, c.current_stock as global_stock, b.remarks, c.brand_name, c.origin,c.model,c.section_id,b.trans_uom,b.no_of_qty, a.boe_mushak_challan_date, a.boe_mushak_challan_no
	from inv_receive_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id' and c.entry_form=334  and a.status_active=1 and b.status_active=1";
	//echo $sql;
	$result = sql_select($sql);

	foreach($result as $row)
	{
		// sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c  where a.id=b.mst_id

		echo "$('#cbo_item_category').val(".$row[csf("item_category_id")].");\n";
		echo "load_drop_down( 'requires/raw_material_item_receive_controller', ".$row[csf("item_category_id")].", 'load_drop_down_itemgroup', 'item_group_td' );\n";
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
		//echo "$('#txt_boe_mushak_challan_no').val(".$row[csf("boe_mushak_challan_no")].");\n";
		//echo "$('#txt_boe_mushak_challan_date').val(".change_date_format($row[csf("boe_mushak_challan_date")],1).");\n";
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
        echo "$('#txt_brand').val('".$row[csf("brand_name")]."');\n";
        echo "$('#cbo_origin').val('".$row[csf("origin")]."');\n";
        echo "$('#txt_model').val('".$row[csf("model")]."');\n";//new dev
        echo "$('#cbo_section').val('".$row[csf("section_id")]."');\n";//new dev
        echo "$('#cbo_receive_uom').val('".$row[csf("trans_uom")]."');\n";//new dev
        echo "$('#txt_no_of_qty').val('".$row[csf("no_of_qty")]."');\n";//new dev

		if($row[csf("receive_basis")]==1)// pi
		{
			$conversion_factor = return_field_value("conversion_factor as conversion_factor","lib_item_group","id=".$row[csf("item_group_id")]."","conversion_factor");
			$pi_wo_req_qty = return_field_value("sum(b.quantity) as pi_qnty","com_pi_master_details a, com_pi_item_details b","a.id=b.pi_id and a.id=".$row[csf("booking_id")]."  and b.item_prod_id=".$row[csf("prod_id")]." and b.status_active=1 and b.is_deleted=0  group by a.id","pi_qnty");
			$totalRcvQnty = return_field_value("sum(a.order_qnty) as bal","inv_transaction a, inv_receive_master b ","a.mst_id=b.id and b.booking_id=".$row[csf("booking_id")]." and a.prod_id=".$row[csf("prod_id")]." and b.receive_basis=1 and b.entry_form=263 and a.transaction_type=1 and b.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
			$totalRtnQnty = return_field_value("sum(c.cons_quantity) as bal","inv_receive_master a, inv_issue_master b, inv_transaction c"," a.id=b.received_id and c.mst_id=b.id and a.booking_id=".$row[csf("booking_id")]." and c.prod_id=".$row[csf("prod_id")]." and a.receive_basis=1 and b.entry_form=264 and c.transaction_type=3 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
			$totalRcvQnty=$totalRcvQnty-($totalRtnQnty/$conversion_factor);
			$wo_pi_re_bal=(($pi_wo_req_qty+$row[csf("order_qnty")])-$totalRcvQnty);
			$wo_pi_re_bal=number_format($wo_pi_re_bal,2,".","");
			echo "$('#txt_item_desc').attr('disabled','true');\n";
			echo "$('#cbo_item_group').attr('disabled','true');\n";
			echo "$('#cbo_item_category').attr('disabled','true');\n";
		}
		else if($row[csf("receive_basis")]==2)// wo
		{
			$conversion_factor = return_field_value("conversion_factor as conversion_factor","lib_item_group","id=".$row[csf("item_group_id")]."","conversion_factor");
			$pi_wo_req_qty = return_field_value("sum(b.supplier_order_quantity) as wo_quantity","wo_non_order_info_mst a, wo_non_order_info_dtls b","a.id=b.mst_id and a.id=".$row[csf("booking_id")]." and b.item_id=".$row[csf("prod_id")]." and b.status_active=1 and b.is_deleted=0 group by a.id,b.item_id","wo_quantity");
			$totalRcvQnty = return_field_value("sum(a.order_qnty) as bal","inv_transaction a, inv_receive_master b ","a.mst_id=b.id and b.booking_id=".$row[csf("booking_id")]." and a.prod_id=".$row[csf("prod_id")]." and b.receive_basis=2 and b.entry_form=263 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
			$totalRtnQnty = return_field_value("sum(c.cons_quantity) as bal","inv_receive_master a, inv_issue_master b, inv_transaction c"," a.id=b.received_id and c.mst_id=b.id and a.booking_id=".$row[csf("booking_id")]." and c.prod_id=".$row[csf("prod_id")]." and a.receive_basis=2 and b.entry_form=264 and c.transaction_type=3 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
			$totalRcvQnty=$totalRcvQnty-($totalRtnQnty/$conversion_factor);
			$wo_pi_re_bal=(($pi_wo_req_qty+$row[csf("order_qnty")])-$totalRcvQnty);
			$wo_pi_re_bal=number_format($wo_pi_re_bal,2,".","");
			echo "$('#txt_item_desc').attr('disabled','true');\n";
			echo "$('#cbo_item_group').attr('disabled','true');\n";
			echo "$('#cbo_item_category').attr('disabled','true');\n";
		}
		else if($row[csf("receive_basis")]==7)// Req
		{
			$pi_wo_req_qty = return_field_value("sum(b.quantity) as req_quantity","inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b","a.id=b.mst_id and a.id=".$row[csf("booking_id")]." and b.product_id=".$row[csf("prod_id")]." and b.status_active=1 and b.is_deleted=0 group by a.id","req_quantity");
			$totalRcvQnty = return_field_value("sum(a.cons_quantity) as bal","inv_transaction a, inv_receive_master b ","a.mst_id=b.id and b.booking_id=".$row[csf("booking_id")]." and a.prod_id=".$row[csf("prod_id")]." and b.receive_basis=7 and b.entry_form=263 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bal");
			$wo_pi_re_bal=(($pi_wo_req_qty+$row[csf("order_qnty")])-$totalRcvQnty);
			$wo_pi_re_bal=number_format($wo_pi_re_bal,2,".","");
			echo "$('#txt_item_desc').attr('disabled','true');\n";
			echo "$('#cbo_item_group').attr('disabled','true');\n";
			echo "$('#cbo_item_category').attr('disabled','true');\n";
		}
		echo "$('#txt_order_qty').val('".$wo_pi_re_bal."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_receive_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";
		echo "$('#cbo_bin').val('".$row[csf("bin_box")]."');\n";
		echo "$('#update_id').val('".$row[csf("id")]."');\n";
		echo "$('#current_prod_id').val(".$row[csf("prod_id")].");\n";
 		echo "set_button_status(1, permission, 'fnc_general_item_receive_entry',1);\n";

		if($row[csf("receive_basis")]==4)
		{
			echo "disable_enable_fields( 'txt_challan_no*txt_exchange_rate*txt_lc_no', 0, '', '');\n";

			echo "fn_onCheckreadonly();\n";
		}
		else
		{
			echo "disable_enable_fields( 'txt_challan_no*txt_exchange_rate', 0, '', '');\n";
		}

		echo "fn_calile();\n";

		$item_category_id=$row[csf("item_category_id")];
		$company_id=$row[csf("company_id")];
		echo "get_php_form_data($item_category_id+'_'+$company_id+'_'+".$row[csf("receive_basis")].", 'set_rate_credential', 'requires/raw_material_item_receive_controller' );\n";
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



if($action=="set_rate_credential")
{
	list($item_id,$company_id,$receive_basis)=explode('_',$data);

	$sql="select rate_optional,is_editable from variable_settings_inventory where variable_list = 10 and item_category_id=$item_id and company_name=$company_id";
	$dataArray=sql_select($sql);

	echo "$('#is_rate_optional').val(".$dataArray[0][csf('rate_optional')].");\n";
	if($dataArray[0][csf('is_editable')]==1)
	{
		echo "$('#txt_rate').attr('disabled',false);\n";
	}
	else if($dataArray[0][csf('is_editable')]==2)
	{
		echo "$('#txt_rate').attr('disabled',true);\n";
	}

	if($receive_basis==4 || $receive_basis==6){echo "$('#txt_rate').attr('disabled',false);\n";}


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

	$sql=" select id, recv_number,receive_basis,receive_date, challan_no, lc_no,lc_sc_no, store_id, supplier_id, currency_id, exchange_rate, pay_mode,source,booking_id, inserted_by, boe_mushak_challan_no,boe_mushak_challan_date,challan_date,gate_entry_no,gate_entry_date from inv_receive_master where id='$data[1]'";
	$dataArray=sql_select($sql);
	$inserted_by=$dataArray[0][csf("inserted_by")];

	if($dataArray[0][csf('receive_basis')]==2 || $dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==7)
	{

		if($dataArray[0][csf('receive_basis')]==2) // Wo Basis
		{
			$wo_sql=sql_select( "select a.id,a.wo_number,a.requisition_no as requ_id ,c.requ_no, b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b,inv_purchase_requisition_mst c where a.id=b.mst_id and a.id='".$dataArray[0][csf('booking_id')]."' and c.id=a.requisition_no and b.status_active=1 and b.is_deleted=0 group by a.id,a.wo_number,a.requisition_no,b.item_id,c.requ_no");
			foreach($wo_sql as $row)
			{
				$wo_library[$row[csf("id")]]=$row[csf("wo_number")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]]=$row[csf("wo_qnty")];

				$requsition_id_arr[$row[csf("wo_number")]]=$row[csf("requ_id")];
				$wo_arr[$row[csf("id")]]=$row[csf("wo_number")];

				$req_no=$row[csf("requ_no")];

			}

		}
		else if($dataArray[0][csf('receive_basis')]==1) //Pi Basis
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='".$dataArray[0][csf('booking_id')]."' and b.status_active=1 and b.is_deleted=0 group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach($sql_pi as $row)
			{
				$pi_library[$row[csf("pi_id")]]=$row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]]=$row[csf("quantity")];

				$pi_wo_no_library[$row[csf("pi_number")]]=$row[csf("work_order_no")];
				$pi_arr[$row[csf("pi_id")]]=$row[csf("pi_number")];
			}
		}
		else
		{
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='".$dataArray[0][csf('booking_id')]."' and b.status_active=1 and b.is_deleted=0 group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id");
			foreach($sql_req as $row)
			{
				$requisition_library[$row[csf("req_id")]]=$row[csf("requ_no")];
				$wo_library_prod[$row[csf("req_id")]][$row[csf("item_id")]]=$row[csf("quantity")];

				$division_library[$row[csf("requ_no")]]=$row[csf("division_id")];
				$department_library[$row[csf("requ_no")]]=$row[csf("department_id")];
				$req_arr[$row[csf("requ_no")]]=$row[csf("requ_no")];
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
	$lc_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details where item_category_id in(".implode(",",array_flip($item_category)).")", "id", "lc_number"  );
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");


    //print_r($wo_library_prod);//die;

	?>
	<div style="width:1060px;">
    <table width="1050" cellspacing="0" align="right">
        <tr>
            <td colspan="2" rowspan="3">
                <img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
            </td>
            <td colspan="4" align="center" style="font-size:xx-large;">
                <strong style="margin-left: -200px;"><? echo $company_library[$data[0]]; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="4" align="center" style="font-size:14px">

				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                    foreach ($nameArray as $result)
					{
					?>
                    <span style="margin-left: -200px;">
						Plot No: <? echo $result['PLOT_NO']; ?>
						Level No: <? echo $result['LEVEL_NO']?>
						Road No: <? echo $result['ROAD_NO']; ?>
						Block No: <? echo $result['BLOCK_NO'];?>
						City No: <? echo $result['CITY'];?>
						Zip Code: <? echo $result['ZIP_CODE']; ?>
                    </span>
                    <br>
                    <span style="margin-left:-200px;">
						Province No: <?php echo $result['PROVINCE'];?>
						Country: <? echo $country_arr[$result['COUNTRY_ID']]; ?>
                    </span>
                        <br>
                    <span style="margin-left: -200px;">
						Email Address: <? echo $result['EMAIL'];?>
						Website No: <? echo $result['WEBSITE'];?>
                        <span>
                    <?
					}
                ?>
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:x-large"><strong style="margin-left: -200px;"><u>Material Receiving & Inspection Report</u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>MRIR Number:</strong></td><td width="175"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="130"><strong>Receive Basis :</strong></td> <td width="175"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Receive Date:</strong></td><td ><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Challan No:</strong></td> <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>L/C No:</strong></td><td ><?
			if($dataArray[0][csf('lc_no')])
			{
				echo $lc_arr[$dataArray[0][csf('lc_no')]];
			}
			else
			{
				echo $dataArray[0][csf('lc_sc_no')];
			}

			?></td>
            <td><strong>Store Name:</strong></td><td ><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td> <td ><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong>Currency:</strong></td><td ><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            <td><strong>Exchange Rate:</strong></td><td ><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
        </tr>
        <tr>
            <td><strong>Pay Mode:</strong></td> <td ><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            <td><strong>Source:</strong></td><td ><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            <td><strong>WO/PI/Req.No:</strong></td> <td ><? if ($dataArray[0][csf('receive_basis')]==1) echo $pi_arr[$dataArray[0][csf('booking_id')]]; else if ($dataArray[0][csf('receive_basis')]==2) echo $wo_arr[$dataArray[0][csf('booking_id')]]; else if ($dataArray[0][csf('receive_basis')]==7) echo $req_arr[$dataArray[0][csf('booking_id')]]; ?></td>
        </tr>
		<tr>
            <td><strong>BOE/Mushak Challan No:</strong></td> <td ><? echo $dataArray[0][csf('boe_mushak_challan_no')]; ?></td>
            <td><strong>BOE/Mushak Challan Date:</strong></td><td ><? echo $dataArray[0][csf('boe_mushak_challan_date')]; ?></td>
            <td><strong>Gate Entry No:</strong></td><td ><? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Gate Entry Date:</strong></td> <td ><? echo change_date_format($dataArray[0][csf('gate_entry_date')],1); ?></td>
			<td><strong>Challan Date:</strong></td><td ><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
            <td><strong>Requisition No:</strong></td> <td ><? echo $req_no; ?></td>
        </tr>
    </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="1110"  border="1" rules="all" class="rpt_table" style="margin-bottom:15px;">
            <thead bgcolor="#dddddd" align="center">
                <th width="40">SL</th>
                <th width="80" align="center">Item Category</th>
                <th width="150" align="center">Item Group</th>
                <th width="100" align="center">Section</th>
                <th width="200" align="center">Item Description</th>
                <th width="50" align="center">UOM</th>
                <th width="60" align="center">No. Of</th>
                <th width="80" align="center">Recv. Qnty.</th>
                <th width="50" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th width="70" align="center">BDT Amount</th>
                <th width="80" align="center">PI/Ord/Req Qnty Bal.</th>
                <th align="center">Warranty Exp. Date</th>
            </thead>
	<?
	$mrr_no =$dataArray[0][csf('recv_number')];;
	$up_id =$data[1];
	$cond="";
	if($mrr_no!="") $cond .= " and a.recv_number='$mrr_no'";
	if($up_id!="") $cond .= " and a.id='$up_id'";
	$i=1;
	$item_name_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );


	//echo "select a.id, a.receive_basis, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, (b.order_amount*a.exchange_rate) as amount_bdt , c.item_category_id, c.item_group_id, c.item_description, c.product_name_details, c.lot, c.item_size from inv_receive_master a, inv_transaction b,  product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category not in (1,2,3,5,6,7,12,13,14) and a.entry_form=263  and a.status_active=1 and b.status_active=1 $cond";
	$sql_result= sql_select("select a.id, a.receive_basis, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, (b.order_amount*a.exchange_rate) as amount_bdt , c.item_category_id, c.item_group_id, c.item_description, c.product_name_details, c.lot, c.item_size,c.section_id, a.booking_id, b.prod_id , b.no_of_qty, b.trans_uom
	from inv_receive_master a, inv_transaction b,  product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category  in (101) and c.entry_form=334 and a.entry_form=263 and a.status_active=1 and b.status_active=1 $cond");

	foreach($sql_result as $row)
	{
		if ($i%2==0)
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			//$order_qnty=$row[csf('order_qnty')];
			$order_qnty_sum += $row[csf('order_qnty')];

			//$order_amount=$row[csf('order_amount')];
			$order_amount_sum += $row[csf('order_amount')];
			$amount_bdt_sum += $row[csf('amount_bdt')];

			//$balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]-$row[csf('order_qnty')]);
			if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==1 || $row[csf('receive_basis')]==7)
			{
				$balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]-($row[csf('order_qnty')]+$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
			}
			else
			{
				$balance_qnty=0;
			}

			$balance_qnty_sum += $balance_qnty;

			$desc=$row[csf('item_description')];

			if($row[csf('item_size')]!="")
			{
				$desc.=", ".$row[csf('item_size')];
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td title="<? echo $row[csf("booking_id")]."==".$row[csf("prod_id")]; ?>"><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
                <td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                <td><? echo $trims_section[$row[csf('section_id')]]; ?></td>
                <td><? echo $desc; ?></td>
                <td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                <td align="right"><? echo number_format($row[csf('no_of_qty')],0)." ".$unit_of_measurement[$row[csf("trans_uom")]]; ?></td>
                <td align="right"><? echo number_format($row[csf('order_qnty')],2); ?></td>
                <td align="right"><? echo $row[csf('order_rate')]; ?></td>
                <td align="right"><? echo number_format($row[csf('order_amount')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('amount_bdt')],2); ?></td>
                <td align="right"><?  echo number_format($balance_qnty,0); ?></td>
                <td><? echo change_date_format($row[csf('expire_date')]); ?></td>
			</tr>
			<?
			$i++;
			}
			?>
        	<tr>
        	<td><strong>Total:</strong></td>
        	<td>&nbsp;&nbsp;</td>
        	<td>&nbsp;&nbsp;</td>
        	<td>&nbsp;&nbsp;</td>
        	<td>&nbsp;&nbsp;</td>
        	<td>&nbsp;&nbsp;</td>
        	<td>&nbsp;&nbsp;</td>
        	<td align="right"><strong><? echo number_format($order_qnty_sum,2); ?></strong></td>
        	<td>&nbsp;&nbsp;</td>
        	<td align="right"><strong><? echo number_format($order_amount_sum,2);?></strong></td>
        	<td align="right"><strong><? echo number_format($amount_bdt_sum,2);?></strong></td>
        	<td align="right"><strong><? echo number_format($balance_qnty_sum,2); ?></strong></td>
        	<td>&nbsp;&nbsp;</td>

			</tr>
		</table>

		<div style="margin-left:27px;">
        <?
			$remarks=return_field_value("remarks","inv_receive_master","company_id=$data[0] and id='$data[1]'");
			echo "Remarks : ".$remarks;
		?>
        </div>
		<?
		echo signature_table(156, $data[0], "1050px","","",$inserted_by);
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

	$req_div_name_arr=return_library_array( "select id, division_id from inv_purchase_requisition_mst", "id", "division_id"  );
	$req_department_name_arr=return_library_array( "select id, department_id from inv_purchase_requisition_mst", "id", "department_id"  );
	$req_no_arr=return_library_array( "select wo_number, requisition_no from wo_non_order_info_mst where entry_form in(146,147)", "wo_number", "requisition_no"  );

	$sql=" select id, recv_number, receive_basis, receive_purpose, booking_id, loan_party, gate_entry_no, receive_date, challan_no, location_id, store_id, supplier_id, lc_no,lc_sc_no, currency_id, exchange_rate, source,supplier_referance,pay_mode from inv_receive_master where id='$data[1]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);



	$sql_LC = "select a.recv_number, c.lc_number,a.lc_sc_no,a.lc_no from inv_transaction b, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id where a.id=b.mst_id and a.id='$data[1]' and a.entry_form=263 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.transaction_type=1 and b.item_category in(101) group by a.recv_number,c.lc_number,a.lc_sc_no,a.lc_no order by a.recv_number";

	//echo $sql_LC; die;

	$LC_dataArray=sql_select($sql_LC);

	if ($LC_dataArray[0][csf('lc_no')]) {
		$lc_no=$LC_dataArray[0][csf('lc_number')];
	}else{
		$lc_no=$LC_dataArray[0][csf('lc_sc_no')];
	}

	if($dataArray[0][csf('receive_basis')]==2 || $dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==7)
	{

		if($dataArray[0][csf('receive_basis')]==2) // Wo Basis
		{
			$wo_sql=sql_select( "select a.id,a.wo_number,a.requisition_no as requ_id ,	b.item_id,sum(b.supplier_order_quantity) as wo_qnty from  wo_non_order_info_mst  a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id='".$dataArray[0][csf('booking_id')]."' and b.status_active=1 and b.is_deleted=0 group by a.id,a.wo_number,a.requisition_no,b.item_id");
			foreach($wo_sql as $row)
			{
				$wo_library[$row[csf("id")]]=$row[csf("wo_number")];
				$wo_library_prod[$row[csf("id")]][$row[csf("item_id")]]=$row[csf("wo_qnty")];

				$requsition_id_arr[$row[csf("wo_number")]]=$row[csf("requ_id")];

			}
		}
		else if($dataArray[0][csf('receive_basis')]==1) //Pi Basis
		{
			$sql_pi = sql_select("select a.id as pi_id, a.pi_number,b.work_order_no, b.item_prod_id as item_id , sum(b.quantity) as quantity from com_pi_master_details a , com_pi_item_details b where a.id=b.pi_id  and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.pi_number,b.work_order_no,b.item_prod_id");
			foreach($sql_pi as $row)
			{
				$pi_library[$row[csf("pi_id")]]=$row[csf("pi_number")];
				$wo_library_prod[$row[csf("pi_id")]][$row[csf("item_id")]]=$row[csf("quantity")];

				$pi_wo_no_library[$row[csf("pi_number")]]=$row[csf("work_order_no")];
			}
		}
		else
		{
			$sql_req = sql_select("select a.id as req_id, a.requ_no,a.division_id,a.department_id, b.product_id as item_id , sum(b.quantity) as quantity from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id,a.requ_no,a.division_id,a.department_id,b.product_id");
			foreach($sql_req as $row)
			{
				$requisition_library[$row[csf("req_id")]]=$row[csf("requ_no")];
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
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$item_name_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

    $loan_type=array('1' =>"Loan");


	?>
	<div style="width:970px;">
    <table width="950" cellspacing="0" align="right">
        <tr>
            <td colspan="2" rowspan="3">
                <img src="../../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
            </td>
            <td colspan="4" align="center" style="font-size:xx-large;">
                <strong style="margin-left: -200px;"><? echo $company_library[$data[0]]; ?></strong>
            </td>
        </tr>
        <tr class="">
        	<td colspan="4" align="center" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
                <span style="margin-left: -200px;">
						Plot No: <? echo $result['PLOT_NO']; ?>
						Level No: <? echo $result['LEVEL_NO']?>
						Road No: <? echo $result['ROAD_NO']; ?>
						Block No: <? echo $result['BLOCK_NO'];?>
						City No: <? echo $result['CITY'];?>
						Zip Code: <? echo $result['ZIP_CODE']; ?>
                    </span>
                <br>
                <span style="margin-left:-200px;">
						Province No: <?php echo $result['PROVINCE'];?>
						Country: <? echo $country_arr[$result['COUNTRY_ID']]; ?>
                    </span>
                <br>
                <span style="margin-left: -200px;">
						Email Address: <? echo $result['EMAIL'];?>
						Website No: <? echo $result['WEBSITE'];?>
                        <span>
                    <?
                    }
                    ?>
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:x-large"><strong style="margin-left: -200px;"><u>Material Receiving & Inspection Report</u></strong></td>
        </tr>
        <tr>
        	<td width="100"><strong>MRIR Number:</strong></td><td width="220"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="100"><strong>Receive Basis :</strong></td> <td width="180"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="100"><strong>Receive Date:</strong></td><td ><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Challan No:</strong></td> <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>L/C No:</strong></td><td ><?
			/*if($dataArray[0][csf('lc_no')])
			{
				echo $dataArray[0][csf('lc_no')];
			}
			else
			{
				echo $dataArray[0][csf('lc_sc_no')];
			}*/
			echo $lc_no;

			 ?></td>
            <td><strong>Store Name:</strong></td><td ><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td> <td ><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong>Currency:</strong></td><td ><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            <td><strong>Exchange Rate:</strong></td><td ><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
        </tr>
        <tr>
            <td><strong>Ref:</strong></td> <td ><? echo $dataArray[0][csf('supplier_referance')]; ?></td>
            <td><strong>Source:</strong></td><td ><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            <td><strong>Pay Mode:</strong></td> <td ><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>

        </tr>
          <tr>
            <td><strong>WO/PI:</strong></td> <td >
			<?
			if ($dataArray[0][csf('receive_basis')]==1) //Pi Basis
			{
				echo $pi_library[$dataArray[0][csf('booking_id')]];
			}
			else if($dataArray[0][csf('receive_basis')]==2) //Wo Basis
			{
				echo $wo_library[$dataArray[0][csf('booking_id')]];
			}
			else  if($dataArray[0][csf('receive_basis')]==7) // Req. Basis
			{
				echo $requisition_library[$dataArray[0][csf('booking_id')]];
			}
			else
			{
				echo "Independent";
			}
			?></td>
             <td><strong>Division: </strong></td>
             <td>

			 <?
			if($dataArray[0][csf('receive_basis')]==1) //PI
			 {
				echo $division_name_arr[$req_div_name_arr[$req_no_arr[$pi_wo_no_library[$pi_library[$dataArray[0][csf('booking_id')]]]]]];
			 }
			else if($dataArray[0][csf('receive_basis')]==2) //Wo
			 {
				 echo $division_name_arr[$req_div_name_arr[$requsition_id_arr[$wo_library[$dataArray[0][csf('booking_id')]]]]];
			 //echo $division_name_arr[$division_library[$requisition_library[$dataArray[0][csf('booking_id')]]]];
			 }
			else if($dataArray[0][csf('receive_basis')]==7) // Req..
			 {
			 echo $division_name_arr[$division_library[$requisition_library[$dataArray[0][csf('booking_id')]]]];
			 }
			else
			{
				echo "Independent";
			}
			 ?>

             </td>
             <td><strong>Department:</strong></td>
             <td><?
			 if($dataArray[0][csf('receive_basis')]==1) //PI
			 {
			 echo $department_name_arr[$req_department_name_arr[$req_no_arr[$pi_wo_no_library[$pi_library[$dataArray[0][csf('booking_id')]]]]]];
			 }
			 else  if($dataArray[0][csf('receive_basis')]==2) //WO
			 {
			  echo $department_name_arr[$req_department_name_arr[$requsition_id_arr[$wo_library[$dataArray[0][csf('booking_id')]]]]];
			 }
			  else  if($dataArray[0][csf('receive_basis')]==7) // Req..
			 {
			  echo $department_name_arr[$department_library[$requisition_library[$dataArray[0][csf('booking_id')]]]];
			 }
			 else
			 {
				echo "Independent";
			 }
			  ?>
             <td><strong>Receive Purpose: </strong></td><td ><? echo $loan_type[$dataArray[0][csf('receive_purpose')]]; ?></td>
             </td>
        </tr>
    </table>
    <?
	if($db_type==2)
	{
	  $sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks,
	  (c.sub_group_name||' '|| c.item_description || ' '|| c.item_size) as product_name_details, c.item_group_id, c.item_description,c.item_code
	  from inv_receive_master a, inv_transaction b, product_details_master c
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and c.entry_form=334 and a.status_active=1 and b.status_active=1";
	}
	else
	{
	  $sql_dtls = "select a.id as recv_id, a.booking_id, b.item_category,
	  b.id, b.receive_basis, b.pi_wo_batch_no, b.order_uom, b.order_qnty, b.order_rate, b.order_amount, b.cons_amount, b.balance_qnty, b.expire_date, b.batch_lot, b.prod_id, b.remarks,
	 concat(c.sub_group_name,c.item_description, c.item_size) as product_name_details, c.item_group_id, c.item_description,c.item_code
	  from inv_receive_master a, inv_transaction b, product_details_master c
	  where a.company_id=$data[0] and a.id=$data[1] and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and c.entry_form=334 and a.status_active=1 and b.status_active=1";

	}

	  $sql_result= sql_select($sql_dtls);

	  $ammount_arr=array();
	  foreach( $sql_result as $row){
		$ammount_arr[$row["ITEM_DESCRIPTION"]]["ORDER_AMOUNT"]+=$row["ORDER_AMOUNT"];
	  }

	//   print_r($ammount_arr);

	 $i=1;
	  ?>
         <br>
    <div style="width:100%;">
     <table align="right" cellspacing="0" width="960"  border="1" rules="all" class="rpt_table" style="margin-bottom: 15px;">
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="70" >Item Category</th>
            <th width="110" >Item Group</th>
            <th width="40" >Item Code</th>
            <th width="160" >Item Description</th>
            <th width="40" >UOM</th>
            <th width="70" >PI/WO Qnty.</th>
            <th width="70" >Previous Recv Qnty</th>
            <th width="70" >Today Recv. Qnty.</th>
            <th width="50" >Rate</th>
            <th width="80">Amount</th>
            <th width="80">PI/WO Qnty Bal.</th>
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

			//$balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]-($row[csf('order_qnty')]+$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));


			if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==1 || $row[csf('receive_basis')]==7)
			{
				$balance_qnty=($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]-($row[csf('order_qnty')]+$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]));
			}
			else
			{
				$balance_qnty=0;
			}


			$balance_qnty_sum += $balance_qnty;

			$desc=$row[csf('item_description')];

			if($row[csf('item_size')]!="")
			{
				$desc.=", ".$row[csf('item_size')];
			}
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf('item_category')]]; ?></td>
                <td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                <td><? echo $row[csf('item_code')]; ?></td>
                <td align="center"><? echo $row[csf('product_name_details')]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                <td align="right"><? echo number_format($wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]],2); $tot_ord_qnty+=$wo_library_prod[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
                <td align="right"><? echo number_format($order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]],2); $tot_prev_qnty+=$order_prev_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]]; ?></td>
                <td align="right"><? echo number_format($row[csf('order_qnty')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('order_rate')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('order_amount')],2);  ?></td>
                <td align="right"><? echo  number_format($balance_qnty,2); ?></td>
                <td><? echo $row[csf('remarks')]; ?></td>
			</tr>
			<?
			$i++;
			}
			?>
        	<tr>

				<td><strong>Total:</strong></td>
				<td><strong>&nbsp;&nbsp;</strong></td>
				<td><strong>&nbsp;&nbsp;</strong></td>
				<td><strong>&nbsp;&nbsp;</strong></td>
				<td><strong>&nbsp;&nbsp;</strong></td>
				<td><strong>&nbsp;&nbsp;</strong></td>
				<td align="right"><strong><? echo number_format($tot_ord_qnty,2); ?></strong></td>
				<td align="right"><strong><? echo number_format($tot_prev_qnty,2); ?></strong></td>
				<td align="right"><strong><? echo number_format($order_qnty_sum,2); ?></strong></td>
				<td><strong>&nbsp;&nbsp;</strong></td>
				<td align="right"><strong><? echo number_format($order_amount_sum,2); ?></strong></td>
				<td align="right"><strong><? echo number_format($balance_qnty_sum,2); ?></strong></td>
				<td><strong>&nbsp;&nbsp;</strong></td>

			</tr>
		</table>

		<div style="margin-left:20px;">
        <?
			$remarks=return_field_value("remarks","inv_receive_master","company_id=$data[0] and id='$data[1]'");
			echo "<b>Remarks</b> : ".$remarks;
		?>
        </div>
		<?

	$sql=" select id, issue_number, issue_date, received_id, challan_no, supplier_id,remarks from  inv_issue_master where received_id=$data[1] and company_id='$data[0]' ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$issue_id=$dataArray[0]["ID"];


	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$rec_id_arr=return_library_array( "select id, recv_number from  inv_receive_master", "id", "recv_number"  );

    if($issue_id>0){
        ?>
		<div style="width:930px;">
			<table width="900" cellspacing="0" align="right">
				<tr>
					<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo "Raw Material Receive Return Information" ?></u></strong></td>
				</tr>
				<tr>
					<td width="120"><strong>Return ID:</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
					<td width="100"><strong>Return Date :</strong></td> <td width="230px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				</tr>
				<tr>
					<td><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
					<td><strong>Returned To:</strong></td><td width="230px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
				</tr>
			</table>
			<br>
			<div style="width:100%;">
				<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
					<thead bgcolor="#dddddd" align="center">
						<th width="40">SL</th>
						<th width="80" align="center">Item Category</th>
						<th width="150" align="center">Item Group</th>
						<th width="200" align="center">Item Description</th>
						<th width="50" align="center">UOM</th> 
						<th width="80" align="center">Returned. Qnty.</th>
						<th width="50" align="center">Rate</th>
						<th width="70" align="center">Return Value</th>
						<th width="80" align="center">Store</th> 
					</thead>
		    <?
			$i=1;
			$item_name_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );
			
				 $sql_dtls= "select b.mst_id as id, b.item_category, b.cons_uom, b.cons_quantity, b.rcv_rate as cons_rate, b.rcv_amount as cons_amount, b.store_id, c.item_group_id,(c.sub_group_name || ' ' || c.item_description || ' ' || c.item_size ) as product_name_details,c.inserted_by, c.item_description from  inv_transaction b,  product_details_master c where b.prod_id=c.id and b.mst_id=$issue_id and  b.transaction_type=3";
			
			// echo $sql_dtls;
			$sql_result=sql_select($sql_dtls);
			$inserted_by=$sql_result[0][csf("inserted_by")];
			foreach($sql_result as $row)
			{
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
					$cons_quantity=$row[csf('cons_quantity')];
					$cons_quantity_sum += $cons_quantity;
					
					$cons_amount=$row[csf('cons_amount')];
					$cons_amount_sum += $cons_amount;
					
					$desc=$row[csf('item_description')];
					
					if($row[csf('item_size')]!="")
					{
						$desc.=", ".$row[csf('item_size')];
					}
					//  echo $row["ID"]."__".$row["ITEM_DESCRIPTION"];
					 $order_ammount= $ammount_arr[$row["ITEM_DESCRIPTION"]]["ORDER_AMOUNT"];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><? echo $item_category[$row[csf('item_category')]]; ?></td>
						<td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
						<td><? echo $row[csf('product_name_details')]; ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('cons_quantity')],2); ?></td>
						<td align="right"><? echo number_format($row[csf('cons_rate')],2); ?></td>
						<td align="right"><? echo number_format($row[csf('cons_amount')],2); ?></td>
						<td><? echo $store_library[$row[csf('store_id')]]; ?></td>
					</tr>
					<?
					$i++;
					}
					?>
					<tr> 
						<td align="right" colspan="5" ><b>Total</b></td>
						<td align="right"><b><? echo number_format($cons_quantity_sum,2); ?></b></td>
						<td align="right">&nbsp;</td>
						<td align="right"  ><b><? echo number_format($cons_amount_sum,2); ?></b></td>
						<td align="right">&nbsp;</td>
					</tr>
					<tr> 
						<td align="right" colspan="7" ><b> MRR Value After return</b></td>
						<td align="right"><b><? echo number_format($order_ammount-$cons_amount_sum,2); ?></b></td>
						<td align="right">&nbsp;</td>
					
					</tr>
				</table> 
			</div>
		</div> 
        <br>
		 <? 
	}	 
            echo signature_table(156, $data[0], "970px");
         ?>
      </div>
   </div>

 <?
 exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=17 and report_id=259 and is_deleted=0 and status_active=1");
	//echo $print_report_format;die;
	//$field_name, $table_name, $query_cond, $return_fld_name, $new_conn
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print').hide();\n";
	echo "$('#print2').hide();\n";


	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print').show();\n";}
			if($id==819){echo "$('#print2').show();\n";}

		}
	}
	/* else
	{
		echo "$('#Print').show();\n";
		echo "$('#print2').show();\n";

	} */
	exit();
}


/*if ($action = "get_receive_basis") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$variable_set_invent = return_field_value("independent_controll", "variable_settings_inventory", "company_name='$company_id' and variable_list=20 and menu_page_id=621 and status_active=1 and is_deleted=0", "independent_controll");
	$is_independent_controlled = ($variable_set_invent == 1) ? "1,2,4,6,7" : "1,2,6,7";
	echo create_drop_down("cbo_receive_basis", 170, $receive_basis_arr, "", 1, "- Select Receive Basis -", $selected, "fn_onCheckBasis(this.value)", "", $is_independent_controlled);
	exit();
}*/



?>
