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
$userCredential = sql_select("SELECT store_location_id,unit_id as company_id,company_location_id,supplier_id,buyer_id FROM user_passwd where id=$user_id");
$store_location_id = $userCredential[0][csf('store_location_id')];
$company_id = $userCredential[0][csf('company_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];
$buyer_id = $userCredential[0][csf('buyer_id')];

$company_credential_cond = $company_location_credential_cond = $store_location_credential_cond = "";

if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($company_location_id !='') {
    $company_location_credential_cond = "and id in($company_location_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and b.store_location_id in($store_location_id)"; 
}
// user credential data prepare end 

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 170, "SELECT id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $company_location_credential_cond  order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data_ref=explode("**",$data);
	if($data_ref[1]==7) $disable_status=1; else $disable_status=0;
	echo create_drop_down( "cbo_store_name", 170, "SELECT a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data_ref[0] $store_location_credential_cond and b.category_type in(5,6,7,22,23) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "fn_sub_process_enable(this.value);",$disable_status );  	 
	exit();
}

if ($action=="load_drop_down_loan_party")
{
	$party_sql = "SELECT a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
		where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name";

	$party_sql = sql_select($party_sql);
	//$party_count=count($party_sql);
	echo create_drop_down( "cbo_loan_party", 170, "SELECT a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "",1,"" );
	exit();
}

/*if ($action=="load_drop_down_color")
{
	echo create_drop_down( "cbo_sub_process", 170, "SELECT max(id) as id, color_name from lib_color where status_active=1 and is_deleted=0 and (color_name is not null or color_name <> 0) and entry_form =285  group by color_name","id,color_name", 1, "-- Select--", "", "fnc_item_details(this.value,'','')",1);
	exit();
}*/

if ($action=="load_drop_down_color")
{
	echo create_drop_down( "cbo_sub_process", 170, "SELECT max(a.id) as id, a.color_name from lib_color a,pro_recipe_entry_mst b,pro_recipe_entry_dtls c  where a.status_active=1 and a.is_deleted=0 and (a.color_name is not null or a.color_name <> 0) and b.id=c.mst_id and a.id=c.color_id and b.entry_form =285  group by color_name","id,color_name", 1, "-- Select--", "", "fnc_item_details(this.value,'','')",1);
	exit();
}


if ($action=="load_drop_down_sub_process")
{
	$data=explode('**',$data);
	//if($data[2]==1) $is_posted_account=1; else $is_posted_account=0;
	$sql_req_dtls="SELECT a.multicolor_id, b.color_name from dyes_chem_issue_requ_dtls a, lib_color b where a.multicolor_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$data[1] group by a.multicolor_id, b.color_name";
	echo create_drop_down( "cbo_sub_process", 170, $sql_req_dtls,"multicolor_id,color_name", 1, "-- Select--", "", "fnc_item_details(this.value,'','')",1);
	exit();
}

if ($action=="load_drop_down_sub_process_up")
{
	$data=explode('**',$data);
	//if($data[2]==1) $is_posted_account=1; else $is_posted_account=0;
	$sql_req_dtls="SELECT a.sub_process, b.color_name from dyes_chem_issue_dtls a, lib_color b where a.sub_process=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$data[1] group by a.sub_process, b.color_name";
	echo create_drop_down( "cbo_sub_process", 170, $sql_req_dtls,"sub_process,color_name", 1, "-- Select--", "", "fnc_item_details(this.value,'','')",1);
	exit();
}

if($action=="load_drop_down_purpose")
{ 
    $data=explode('_',$data);
	if($data[0]==13)
	{
      echo create_drop_down("cbo_sub_process", 170, $dyeing_sub_process,"", 1, "-- Select--", "", "fnc_item_details(this.value,'','')");
	}
	else
	{
       echo create_drop_down("cbo_sub_process", 170, $dyeing_sub_process,"", 1, "-- Select--", "", "fnc_item_details(this.value,'','')",'','','','',31 );
	}
	
}


if($action=="com_wise_all_data")
{
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");
	
	/*$location_data=sql_select("select ID, LOCATION_NAME from lib_location where company_id=$data $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name");
	$location_arr=array();
	foreach($location_data as $row)
	{
		$location_arr[$row["ID"]]=$row["LOCATION_NAME"];
	}
	unset($location_data);
	$js_location_arr= json_encode($location_arr);
	
	$loan_party_data=sql_select("select a.ID, a.SUPPLIER_NAME from lib_supplier a, lib_supplier_tag_company b 
		where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name");
	$loan_party_arr=array();
	foreach($loan_party_data as $row)
	{
		$loan_party_arr[$row["ID"]]=$row["SUPPLIER_NAME"];
	}
	unset($loan_party_data);
	$js_loan_party_arr= json_encode($loan_party_arr);
	
	$store_data=sql_select("select a.ID, a.STORE_NAME from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name");
	$store_arr=array();
	foreach($store_data as $row)
	{
		$store_arr[$row["ID"]]=$row["STORE_NAME"];
	}
	unset($store_data);
	$js_store_arr= json_encode($store_arr);
	
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=179 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	$js_print_report_format_arr= json_encode($print_report_format_arr);*/
	//."**".$js_location_arr."**".$js_loan_party_arr."**".$js_store_arr."**".$js_print_report_format_arr
	echo $sql[0][csf("auto_transfer_rcv")];
	exit();
}


if($action=="req_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	?>
	<script>
	function js_set_value(mrr)
	{
 		$("#hidden_requ_data").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
	</script>
    <input type="hidden" id="hidden_requ_data"/>
    </head>
    
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1030" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
            	<tr>
                	<th colspan="4" style="text-align:right">Search Type:</th>
                    <th><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 0, "",1 ); ?></th>
                  	<th colspan="4"> </th>
                </tr>
                <tr>                	 
                    <th width="150">Company Name</th>
                    <th width="110">Requisition No</th>
                    <th width="110">Aop Job</th>
                    <th width="110">Work Order</th>
                    <th width="110">Buyer PO</th>
                    <th width="110">Buyer Style</th>
                    <th width="110">Aop Ref</th>
                    <th width="160">Req. Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?  
						  echo create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1 );
                        ?>
                    </td>
                    <td>				
                        <input type="text" style="width:90px" class="text_boxes_numeric"  name="txt_requisition_no" id="txt_requisition_no" placeholder="Number Field" />	
                    </td> 
                    <td>				
                        <input type="text" style="width:90px" class="text_boxes_numeric"  name="txt_emb_job" id="txt_emb_job" placeholder="Number Field" />	
                    </td>
                    <td>				
                        <input type="text" style="width:90px" class="text_boxes"  name="txt_emb_order" id="txt_emb_order" placeholder="Character Field" />	
                    </td>
                    <td>				
                        <input type="text" style="width:90px" class="text_boxes"  name="txt_buyer_po" id="txt_buyer_po" placeholder="Character Field" />	
                    </td>
                    <td>				
                        <input type="text" style="width:90px" class="text_boxes"  name="txt_buyer_style" id="txt_buyer_style" placeholder="Character Field" />	
                    </td>
                    <td>				
                        <input type="text" style="width:90px" class="text_boxes"  name="txt_aop_ref" id="txt_aop_ref" placeholder="Character Field" />	
                    </td>
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" />&nbsp;To&nbsp;
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date" />
                    </td> 
                    <td>
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_requisition_no').value+'_'+document.getElementById('txt_emb_job').value+'_'+document.getElementById('txt_emb_order').value+'_'+document.getElementById('txt_buyer_po').value+'_'+document.getElementById('txt_buyer_style').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_aop_ref').value, 'create_reqisition_search_list_view', 'search_div', 'chemical_dyes_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="9">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-->
                     <input type="hidden" id="hidden_issue_number" value="" />
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

if($action=="create_reqisition_search_list_view")
{
	
	
	$ex_data = explode("_",$data);
	$company = trim($ex_data[0]);
	$requisition_no = trim($ex_data[1]);
	$emb_job = trim($ex_data[2]);
	$emb_order = trim($ex_data[3]);
	$buyer_po = trim($ex_data[4]);
	$buyer_style = trim($ex_data[5]);
	$fromDate = trim($ex_data[6]);
	$toDate = trim($ex_data[7]);
	$string_search_type = trim($ex_data[8]);
	$aop_ref = trim($ex_data[9]);
	if($db_type==0)
	{
		$fromDate=change_date_format($fromDate,'yyyy-mm-dd');
		$toDate=change_date_format($toDate,'yyyy-mm-dd');
	}
	else
	{
		$fromDate=change_date_format($fromDate,'','',1);
		$toDate=change_date_format($toDate,'','',1);
	}
	if($requisition_no=="" && $emb_job=="" && $emb_order=="" && $buyer_po=="" && $buyer_style=="" && $fromDate=="" && $toDate=="" && $aop_ref=="")
	{
		echo "Please Select Requisition Date Range.";die;
	}
	//echo $company."=".$requisition_no."=".$emb_job."=".$emb_order."=".$buyer_po."=".$buyer_style."=".$fromDate."=".$toDate."=".$string_search_type;die;
	$emb_order_id='';$buyer_order_id='';
	if($emb_job!="" || $emb_order!="" || $aop_ref!="")
	{
		$emb_job_cond="";
		if($emb_job!="")
		{
			if($string_search_type==1)
			{
				$emb_job_cond = " and a.job_no_prefix_num='$emb_job'";
			}
			else if($string_search_type==4 || $ex_data[5]==0)
			{
				 $emb_job_cond = " and a.job_no_prefix_num LIKE '%$emb_job%'";	
			}
			else if($string_search_type==2)
			{
				 $emb_job_cond = " and a.job_no_prefix_num LIKE '$emb_job%'";	
			}
			else if($string_search_type==3)
			{
				 $emb_job_cond = " and a.job_no_prefix_num LIKE '%$emb_job'";	
			}
		}
		
		if($emb_order!="")
		{
			if($string_search_type==1)
			{
				$emb_job_cond .= " and b.order_no='$emb_order'";
			}
			else if($string_search_type==4 || $ex_data[5]==0)
			{
				 $emb_job_cond .= " and b.order_no LIKE '%$emb_order%'";	
			}
			else if($string_search_type==2)
			{
				 $emb_job_cond .= " and b.order_no LIKE '$emb_order%'";	
			}
			else if($string_search_type==3)
			{
				 $emb_job_cond .= " and b.order_no LIKE '%$emb_order'";	
			}
		}
		if($aop_ref!="")
		{
			if($string_search_type==1)
			{
				$emb_job_cond .= " and a.aop_reference='$aop_ref'";
			}
			else if($string_search_type==4 || $ex_data[5]==0)
			{
				 $emb_job_cond .= " and a.aop_reference LIKE '%$aop_ref%'";	
			}
			else if($string_search_type==2)
			{
				 $emb_job_cond .= " and a.aop_reference LIKE '$aop_ref%'";	
			}
			else if($string_search_type==3)
			{
				 $emb_job_cond .= " and a.aop_reference LIKE '%$aop_ref'";	
			}
		}
		
		$emb_job_sql="SELECT b.id, a.job_no_prefix_num, b.job_no_mst, b.order_no, a.aop_reference from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form=278 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company $emb_job_cond";
		//echo $emb_job_sql;//die;
		$emb_job_result=sql_select($emb_job_sql);
		$emb_data=array();
		foreach($emb_job_result as $row)
		{
			$emb_order_id.=$row[csf("id")].",";
			$emb_data[$row[csf("id")]]["id"]=$row[csf("id")];
			$emb_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$emb_data[$row[csf("id")]]["job_no_mst"]=$row[csf("job_no_mst")];
			$emb_data[$row[csf("id")]]["order_no"]=$row[csf("order_no")];
			$emb_data[$row[csf("id")]]["aop_reference"]=$row[csf("aop_reference")];
		}
		$emb_order_id=chop($emb_order_id,",");
	}
	else
	{
		$emb_job_sql="SELECT b.id, a.job_no_prefix_num, b.job_no_mst, b.order_no, a.aop_reference from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form=278 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company";
		//echo $emb_job_sql;die;
		$emb_job_result=sql_select($emb_job_sql);
		$emb_data=array();
		foreach($emb_job_result as $row)
		{
			$emb_data[$row[csf("id")]]["id"]=$row[csf("id")];
			$emb_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$emb_data[$row[csf("id")]]["job_no_mst"]=$row[csf("job_no_mst")];
			$emb_data[$row[csf("id")]]["order_no"]=$row[csf("order_no")];
			$emb_data[$row[csf("id")]]["aop_reference"]=$row[csf("aop_reference")];
		}
	}
	
	
	if($buyer_po!="" || $buyer_style!="")
	{
		$buyer_job_cond=""; $job_com_cond="";
		if($buyer_style!="")
		{
			if($string_search_type==1)
			{
				$buyer_job_cond = " and a.style_ref_no='$buyer_style'";
				$job_com_cond = " and b.buyer_style_ref='$buyer_style'";
			}
			else if($string_search_type==4 || $ex_data[5]==0)
			{
				 $buyer_job_cond = " and a.style_ref_no LIKE '%$buyer_style%'";	
				 $job_com_cond = " and b.buyer_style_ref LIKE '%$buyer_style%'";
			}
			else if($string_search_type==2)
			{
				 $buyer_job_cond = " and a.style_ref_no LIKE '$buyer_style%'";	
				 $job_com_cond = " and b.buyer_style_ref LIKE '$buyer_style%'";	
			}
			else if($string_search_type==3)
			{
				 $buyer_job_cond = " and a.style_ref_no LIKE '%$buyer_style'";	
				 $job_com_cond = " and b.buyer_style_ref LIKE '%$buyer_style'";	
			}
		}
		
		if($buyer_po!="")
		{
			if($string_search_type==1)
			{
				$buyer_job_cond .= " and b.po_number='$buyer_po'";
				$job_com_cond .= " and b.buyer_po_no='$buyer_po'";
			}
			else if($string_search_type==4 || $ex_data[5]==0)
			{
				 $buyer_job_cond .= " and b.po_number LIKE '%$buyer_po%'";
				 $job_com_cond .= " and b.buyer_po_no LIKE '%$buyer_po%'";	
			}
			else if($string_search_type==2)
			{
				 $buyer_job_cond .= " and b.po_number LIKE '$emb_order%'";	
				 $job_com_cond .= " and b.buyer_po_no LIKE '$emb_order%'";
			}
			else if($string_search_type==3)
			{
				 $buyer_job_cond .= " and b.po_number LIKE '%$buyer_po'";
				 $job_com_cond .= " and b.buyer_po_no LIKE '%$buyer_po'";	
			}
		}
		
		 $buyer_job_sql="SELECT b.id, a.job_no_prefix_num, a.job_no, a.style_ref_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_job_cond";
		//echo $emb_job_sql;die;
		$buyer_job_result=sql_select($buyer_job_sql);
		$buyer_order_data=array();
		foreach($buyer_job_result as $row)
		{
			$buyer_order_id.=$row[csf("id")].",";
			$buyer_order_data[$row[csf("id")]]["id"]=$row[csf("id")];
			$buyer_order_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$buyer_order_data[$row[csf("id")]]["job_no"]=$row[csf("job_no")];
			$buyer_order_data[$row[csf("id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$buyer_order_data[$row[csf("id")]]["po_number"]=$row[csf("po_number")];
		}
		$buyer_order_id=chop($buyer_order_id,",");
	}
	
	   $order_buyer_po_array=array();
		$buyer_po_arr=array();
		$order_buyer_po='';
		$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='278' $job_com_cond"; 
		$order_sql_res=sql_select($order_sql);
		foreach ($order_sql_res as $row)
		{
			$order_buyer_po_array[]=$row[csf("id")];
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		}
		//unset($order_sql_res);
		$order_buyer_po=implode(",",$order_buyer_po_array);
		//echo $order_buyer_po; 
		if ($order_buyer_po!="") $order_order_buyer_poCond=" and a.order_id in ($order_buyer_po)"; else $order_order_buyer_poCond="";

		//if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();
		if ($order_buyer_po!="")
		{
			$po_ids=explode(",",$order_buyer_po);
			$po_idsCond=""; $poIdsCond="";
			//echo count($po_ids); die;
			if($db_type==2 && count($po_ids)>=999)
			{
				$chunk_arr=array_chunk($po_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($po_idsCond=="")
					{
						$po_idsCond.=" and ( a.order_id in ( $ids) ";
						
					}
					else
					{
						$po_idsCond.=" or  a.order_id in ( $ids) ";
						
					}
				}
				$po_idsCond.=")";
				
			}
			else
			{
				$ids=implode(",",$po_ids);
				$po_idsCond.=" and a.order_id in ($ids) ";
				
			}
		}
	
	
	$req_cond="";
	if($emb_order_id !="")
	{
		$req_cond=" and a.order_id in($emb_order_id)";
	}
	
	if($buyer_order_id !="")
	{
		$req_cond.=" and a.buyer_po_id in($buyer_order_id)";
	}
	//echo $ex_data[4].'='.$issue_basis;die; 
	if(trim($requisition_no)!="")
	{
		if($string_search_type==1)
		{
		  	$req_cond .= " and a.requ_prefix_num=$requisition_no";
		}
		else if($string_search_type==4 || $ex_data[5]==0)
		{
		 	 $req_cond .= " and a.requ_prefix_num LIKE '%$requisition_no%'";	
		}
		else if($string_search_type==2)
		{
			 $req_cond .= " and a.requ_prefix_num LIKE '$requisition_no%'";	
		}
		else if($string_search_type==3)
		{
			 $req_cond .= " and a.requ_prefix_num LIKE '%$requisition_no'";	
		}
 	} 
	
	if( $fromDate!="" || $toDate!="" ) 
	{ 
		if($db_type==0){ $req_cond .= " and a.requisition_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $req_cond .= " and a.requisition_date  between '".change_date_format($fromDate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($toDate,'yyyy-mm-dd',"-",1)."'";}
	
	}

	$batch_arr = return_library_array("SELECT id, batch_no from pro_batch_create_mst where entry_form=281 and status_active=1 and is_deleted=0","id","batch_no");
	$company_arr = return_library_array("SELECT id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	
	//$arr=array(1=>$company_arr,3=>$receive_basis_arr,4=>$batch_arr,5=>$receipe_arr);

	if($db_type==0) $null_cond="''";
	else if($db_type==2) $null_cond="'0'";
	$sql ="SELECT a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.requisition_basis, a.recipe_id, a.id, a.order_id, a.buyer_po_id
	from dyes_chem_issue_requ_mst a where a.company_id=$company and a.is_apply_last_update!=2 and a.entry_form=290 and status_active=1 and is_deleted=0 and a.recipe_id!=$null_cond $req_cond $po_idsCond";
	//echo $sql;
	$result_sql =sql_select($sql);
	foreach($result_sql as $row)
	{
		$all_recipe_id[$row[csf("recipe_id")]]=$row[csf("recipe_id")];
		$all_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
		if($row[csf("buyer_po_id")]) $all_buyer_po_id[$row[csf("buyer_po_id")]]=$row[csf("buyer_po_id")];
	}
	
	if($buyer_order_id =="" && count($all_buyer_po_id)>0)
	{
		$buyer_job_sql="SELECT b.id, a.job_no_prefix_num, a.job_no, a.style_ref_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$all_buyer_po_id).") ";
		//echo $buyer_job_sql;die;
		$buyer_job_result=sql_select($buyer_job_sql);
		$buyer_order_data=array();
		foreach($buyer_job_result as $row)
		{
			$buyer_order_data[$row[csf("id")]]["id"]=$row[csf("id")];
			$buyer_order_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$buyer_order_data[$row[csf("id")]]["job_no"]=$row[csf("job_no")];
			$buyer_order_data[$row[csf("id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$buyer_order_data[$row[csf("id")]]["po_number"]=$row[csf("po_number")];
		}
	}
	$recipe_arr=sql_select("SELECT a.id, a.recipe_no, a.store_id, a.location_id, a.batch_id from pro_recipe_entry_mst a where a.company_id=$company and a.entry_form=285 and a.is_deleted=0 and a.status_active=1 and a.id in(".implode(",",$all_recipe_id).") ");
	foreach($recipe_arr as $row)
	{
		$receipe_data_arr[$row[csf('id')]]['recipe_no']=$row[csf('recipe_no')];

		$receipe_data_arr[$row[csf('id')]]['store_id']=$row[csf('store_id')];
		$receipe_data_arr[$row[csf('id')]]['location_id']=$row[csf('location_id')];
		$receipe_data_arr[$row[csf('id')]]['batch_id']=$row[csf('batch_id')];
		$receipe_data_arr[$row[csf('id')]]['batch_no']=$batch_arr[$row[csf('batch_id')]];
	}
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="120">Req. No</th>
                <th width="70">Req. Date</th>
                <th width="120">Aop Job</th>
                <th width="120">Aop Order</th>
                <th width="120">Buyer Style</th>
                <th width="120">Buyer Order</th>
                <th>Aop Ref</th>
            </thead>
    </table>
   <div style="width:920px; overflow-y:scroll; max-height:230px; cursor:pointer" id="buyer_list_view" align="center">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search" >
        <tbody>
			<? 
            $i=1;
            foreach($result_sql as $row)
            {
				if ($i%2==0)$bgcolor="#E9F3FF";						
				else $bgcolor="#FFFFFF";
				$recipi_no=$receipe_data_arr[$row[csf('recipe_id')]]['recipe_no'];
				$store_ids=$receipe_data_arr[$row[csf('recipe_id')]]['store_id'];
				$location_ids=$receipe_data_arr[$row[csf('recipe_id')]]['location_id'];
				$batch_id=$receipe_data_arr[$row[csf('recipe_id')]]['batch_id'];
				$batch_no=$receipe_data_arr[$row[csf('recipe_id')]]['batch_no'];
				
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('order_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				
				//if($buyer_po!=""){ $buyer_po=$buyer_po;}else{$buyer_po=$buyer_order_data[$row[csf("buyer_po_id")]]["po_number"];}
				//if($buyer_style!=""){ $buyer_style=$buyer_style;}else{$buyer_style=$buyer_order_data[$row[csf("buyer_po_id")]]["style_ref_no"];}
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('requ_no')]."_".$row[csf('id')]."_".$row[csf('recipe_id')]."_".$recipi_no."_".$emb_data[$row[csf("order_id")]]["job_no_mst"]."_".$emb_data[$row[csf("order_id")]]["order_no"]."_".$row[csf("order_id")]."_".$buyer_style."_".$buyer_po."_".$row[csf("buyer_po_id")]."_".$store_ids."_".$location_ids."_".$batch_id."_".$batch_no."_".$emb_data[$row[csf("order_id")]]["aop_reference"]; ?>')">
                    <td width="30" align="center" title="<? echo $recipe_ids; ?>"><? echo $i; ?></td>
                    <td width="120" align="center"><? echo $row[csf("requ_no")]; ?></td>
                    <td width="70" align="center"><? echo change_date_format($row[csf("requisition_date")]); ?></td>
                    <td width="120" align="center" ><? echo $emb_data[$row[csf("order_id")]]["job_no_mst"]; ?></td>
                    <td  width="120" align="center"><? echo $emb_data[$row[csf("order_id")]]["order_no"]; ?></td>
                    <td width="120" align="center"><? echo $buyer_style;//$buyer_order_data[$row[csf("buyer_po_id")]]["style_ref_no"]; ?></td>
                    <td width="120"><? echo $buyer_po;//$buyer_order_data[$row[csf("buyer_po_id")]]["po_number"]; ?></td>
                    <td><? echo $emb_data[$row[csf("order_id")]]["aop_reference"]; ?></td>
				</tr>
				<? 
				$i++; 
            }
			?>
        </tbody>
    </table>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="item_details")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$sub_process_id=$data[1];
	$receipe_id=$data[2];
	$issue_basis=$data[3];
	$is_update=$data[4];
	$req_id=$data[5];
	$cbo_store=$data[6];
	$is_posted_account=$data[7];
	$variable_lot=$data[8];
  
	//echo $req_id;die;
	$item_group_arr=return_library_array( "SELECT id, item_name from lib_item_group where status_active =1 and is_deleted=0",'id','item_name');
	$store_arr=return_library_array( "SELECT a.id as id,a.store_name  as store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(5,6,7,22,23) group by a.id,a.store_name order by a.store_name", "id", "store_name"  );
	?>
	<div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1140" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="40">Product ID</th>
                <th width="80">Item Lot</th>
                <th width="100">Item Cat.</th>
                <th width="100">Group</th>
                <th width="80">Sub Group</th>
                <th width="140">Item Description</th>
                <th width="32">UOM</th>
                <th width="70">Dose Base</th>
                <th width="50">Ratio</th>
                <th width="70">Stock Qty</th>
                <th width="73">Recipe Qnty.</th>
                <th width="50">Adj%.</th>
                <th width="90">Adj. Type</th>
                <th>Issue Qnty.</th>
            </thead>
        </table>
        <div style="width:1140px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1120" class="rpt_table" id="tbl_list_search">
                <tbody>
                <?
                if($is_update=="")
                {
					if($issue_basis==4)
					{
						//$cbo_store
						$sql="SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.lot, b.cons_qty as store_stock
						from product_details_master a, inv_store_wise_qty_dtls b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(5,6,7,23) and b.cons_qty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						union all
						SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, null as lot, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock
						from product_details_master a, inv_transaction b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(22) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						group by a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock 
						order by item_category_id ";	
						//echo $sql;die;
						$i=1;
						$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store,$category,$prod_id);
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $selectResult)
						{
	                        $issue_remain=$totalIssued-$totalIssuedReturn;
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							if($selectResult[csf('item_category_id')]==22)
							{
								$stock_qty=$selectResult[csf('store_stock')];
							}
							else
							{
								$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]][$selectResult[csf('lot')]]['stock'];
							}
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td> 
                                <td width="40" align="center" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                </td>
                                <td width="80" align="center" id="td_lot_<? echo $i; ?>"><? echo $selectResult[csf('lot')]; ?>
                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('lot')]; ?>">
                                </td>
                                <td width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1,"","","","","","","cbo_dose_base[]"); ?></td>
                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($ratio,5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
                                <td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($recipe_qnty,5,""); ?>" disabled></td>
                                <td width="50" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? //echo $ratio; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
                                <td width="80" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -","","calculate_receipe_qty($i)",1,"","","","","","","cbo_adj_type[]"); ?></td>
                                <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($recipe_qnty,5,""); ?>" readonly>
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                <input type="hidden" name="stock_check[]" id="stock_check_<? echo $i; ?>" value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>">
                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty; ?>)"  value="<? //echo def_number_format($recipe_qnty,5,""); ?>" >
                                <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>" / >
                                </td>
							</tr>
							<?
							$i++;
						}
					}
					if($issue_basis==7)
					{
						$total_issue_sql=sql_select("SELECT a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit from inv_issue_master a, dyes_chem_issue_dtls b where a.id=b.mst_id and a.entry_form=308 and a.issue_basis=7 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and a.req_id='".$req_id."' and b.sub_process=$sub_process_id");
						foreach($total_issue_sql as $row)
						{
							$total_issue_arr[$row[csf("product_id")]]+=$row[csf("req_qny_edit")];
							$prev_issue_arr[$row[csf("issue_number")]]+=$row[csf("req_qny_edit")];
						}
						$issue_total_val=$issue_num_all="";
						foreach($prev_issue_arr as $issue_no=>$val)
						{
							if($issue_total_val!="") $issue_total_val .=",";
							$issue_total_val .=$val;
							if($issue_num_all!="") $issue_num_all .=",";
							$issue_num_all .=$issue_no;
						}
						
						$sql_store="SELECT a.id, b.store_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock
						from product_details_master a, inv_transaction b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(22) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						group by a.id, b.store_id";
						//echo $sql_store;
						$store_result=sql_select( $sql_store );
						$store_chemical_stock=array();
						foreach($store_result as $row)
						{
							$store_chemical_stock[$row[csf("id")]][$row[csf("store_id")]]+=$row[csf("store_stock")];
						}
						
						$sql="SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty as required_qnty, b.req_qny_edit, b.item_lot 
						from product_details_master a, dyes_chem_issue_requ_dtls b 
						where a.id=b.product_id and b.mst_id='".$req_id."' and b.multicolor_id=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,22,23) and a.status_active=1 and a.is_deleted=0 order by a.item_category_id ";
						//echo $sql;
						$is_disabled=1;
						$i=1;
						
						$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store,$category,$prod_id);
						//echo $stock_qty_arr;die;
						//echo "<pre>";print_r($stock_qty_arr);die;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $selectResult)
						{
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$req_qnty=$selectResult[csf('req_qny_edit')]-$total_issue_arr[$selectResult[csf("id")]];
							if($selectResult[csf('item_category_id')]==22)
							{
								$stock_qty=$store_chemical_stock[$selectResult[csf('id')]][$cbo_store];
								//$stock_qty=10;
							}
							else
							{
								if($variable_lot==1) $dyes_lot=$selectResult[csf('item_lot')]; else $dyes_lot="";
								$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]][$dyes_lot]['stock'];
							}
							
							if(def_number_format($req_qnty,5,"")>0)
							{
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
                                    <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                    <td width="40" align="center" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                    <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                    </td>
                                    <td width="80" align="center" id="td_lot_<? echo $i; ?>"><? echo $selectResult[csf('item_lot')]; ?>
                                    <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_lot')]; ?>">
                                    </td>
                                    <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                    <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                    </td>
                                    <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                    <td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                    <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                    <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                    <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1,"","","","","","","cbo_dose_base[]");  ?></td>
                                    <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($selectResult[csf('ratio')],5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" readonly></td>
                                    <td width="70" title="<? echo $stock_qty;?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                    <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('required_qnty')],5,""); ?>"  onClick="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)" readonly></td>
                                    <td width="50" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" readonly></td>
                                    <td width="80" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)",1,"","","","","","","cbo_adj_type[]");?></td>
                                    <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                    <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($req_qnty,5,""); ?>" readonly>
                                    <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($req_qnty,5,""); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)"  / >
                                    <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($req_qnty,5,""); ?>" readonly / >
                                    
                                    <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                    <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                    </td>
                                </tr>
								<?
                                $i++;
							}
						}
						echo '<input type="hidden"  id="txt_isu_qnty" value="'.$issue_total_val.'" >';
						echo '<input type="hidden"  id="txt_isu_num" value="'.$issue_num_all.'" >';
					}
                }
                else
                {
					$sql_store="SELECT a.id, b.store_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock
					from product_details_master a, inv_transaction b 
					where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(22) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 
					group by a.id, b.store_id";
					//echo $sql_store;
					$store_result=sql_select( $sql_store );
					$store_chemical_stock=array();
					foreach($store_result as $row)
					{
						$store_chemical_stock[$row[csf("id")]][$row[csf("store_id")]]+=$row[csf("store_stock")];
					}
					$sql="SELECT a.id ,a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure,a.current_stock, b.id as dtls_id, b.trans_id, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.batch_lot 
					from product_details_master a, dyes_chem_issue_dtls b, inv_transaction c
					where a.id=b.product_id and b.trans_id=c.id and b.mst_id=$is_update and b.sub_process=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,22,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					order by a.item_category_id";
					//echo $sql;
					$i=1;
					$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store,$category,$prod_id);
					$nameArray=sql_select( $sql );
					$desable_cond="";
					$desable_id=0;
					if($is_posted_account==1) { $desable_cond=" disabled";  $desable_id=1;}
					foreach ($nameArray as $selectResult)
					{
						if ($i%2==0)  
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						if($selectResult[csf('item_category_id')]==22)
						{
							$stock_qty=$store_chemical_stock[$selectResult[csf('id')]][$cbo_store]+$selectResult[csf('req_qny_edit')];
							//$stock_qty=10;
						}
						else
						{
							if($variable_lot==1) $dyes_lot=$selectResult[csf('batch_lot')]; else $dyes_lot="";
							$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]][$dyes_lot]['stock']+$selectResult[csf('req_qny_edit')];
						}
						
						if($issue_basis==4)
						{
							?>
							<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="40" align="center" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                </td>
                                <td width="80" align="center" id="td_lot_<? echo $i; ?>"><? echo $selectResult[csf('batch_lot')]; ?>
                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('batch_lot')]; ?>">
                                </td>
                                <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1,"","","","","","","cbo_dose_base[]"); ?></td>
                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($selectResult[csf('ratio')],5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
                                <td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                
                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('recipe_qnty')],5,""); ?>" disabled></td>
                                <td width="50" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
                                <td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)",1,"","","","","","","cbo_adj_type[]"); ?></td>
                                <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('required_qnty')],5,""); ?>" readonly>
                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty+$selectResult[csf('req_qny_edit')];//$selectResult[csf('current_stock')]+$selectResult[csf('req_qny_edit')]; ?>)"  <? echo $desable_cond; ?> />
                                <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>" / >
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                </td>
							</tr>
						<?
						}
						if($issue_basis==7)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="40" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                </td>
                                <td width="80" align="center" id="td_lot_<? echo $i; ?>"><? echo $selectResult[csf('batch_lot')]; ?>
                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('batch_lot')]; ?>">
                                </td>
                                <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1); ?></td>
                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($selectResult[csf('ratio')],5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" readonly></td>
                                <td width="70"  title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('recipe_qnty')],5,""); ?>" onClick="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)" readonly></td>
                                <td width="50" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" readonly></td>
                                <td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)",1); ?></td>
                                <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('required_qnty')],5,""); ?>" readonly>
                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>"  onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty+$selectResult[csf('req_qny_edit')];//$selectResult[csf('current_stock')]+$selectResult[csf('req_qny_edit')]; ?>)"   <? echo $desable_cond; ?> / >
                                <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo $selectResult[csf('req_qny_edit')]; ?>" readonly  / >
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                </td>
							</tr>
							<?
						}
						$i++;
		    	    }
					echo '<input type="hidden"  id="txt_isu_qnty" value="'.$issue_total_val.'" >';
					echo '<input type="hidden"  id="txt_isu_num" value="'.$issue_num_all.'" >';
               }
             ?>
           </tbody>
      </table>
   </div>
	</div>           
	<?
	exit();	
}

if($action=="recipe_item_details")
{
	$color_arr=return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$sql="SELECT b.id, b.sub_process as sub_process_id, a.store_id from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id and a.transaction_type=2 and b.mst_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";
	$nameArray=sql_select( $sql );
	$process_array=array();
	foreach($nameArray as $inf)
	{
		$process_array[$inf[csf("sub_process_id")]]["sub_process_id"]=$inf[csf("sub_process_id")];
		$process_array[$inf[csf("sub_process_id")]]["store_id"]=$inf[csf("store_id")];
	}
    foreach($process_array as $sub_process_val)
	{
		?>
        <h3 align="left" id="accordion_h<? echo $sub_process_val["sub_process_id"]; ?>" style="width:910px" class="accordion_h" onClick="fnc_item_details(<? echo $sub_process_val["sub_process_id"];?>,<? echo $data; ?>,<? echo $sub_process_val["store_id"]; ?>)"><span style="display:none" id="accordion_h<? echo $sub_process_val["sub_process_id"]; ?>id"><? echo $sub_process_val["sub_process_id"]; ?></span><span id="accordion_h<? echo $sub_process_val["sub_process_id"]; ?>span">+</span><? echo $color_arr[$sub_process_val["sub_process_id"]]; ?></h3>
		<?
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
 		$("#hidden_issue_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="140">Search By</th>
                    <th width="160" align="center" id="search_by_td_up">Enter Issue No</th>
                    <th width="140">Aop Ref</th>
                    <th width="140">Batch No</th>
                    <th width="160">Issue Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td align="center">
                        <?  
                            $search_by = array(1=>'Issue No',2=>'Challan No',3=>'Requisition No');
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>
                    <td align="center">				
                        <input type="text" style="width:110px" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />	
                    </td>
                    <td align="center">				
                        <input type="text" style="width:110px" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_ref_no').value, 'create_mrr_search_list_view', 'search_div', 'chemical_dyes_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="6">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here -->
                     <input type="hidden" id="hidden_issue_number" value="" />
                    <!-- END -->
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
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	$batch_no = trim(str_replace("'","",$ex_data[5]));
	$yearid = str_replace("'","",$ex_data[6]);
	$ref_no = trim(str_replace("'","",$ex_data[7]));
	
	/*	$batch_id=return_field_value("id as batch_id" ,"pro_batch_create_mst","batch_no like '$batch_no' and status_active=1 and is_deleted=0","batch_id");
	$batch_cond="";
	if($batch_no!="")
	{
		if($batch_id!="")
		{
			$batch_cond=" and find_in_set($batch_id,batch_no)>0";
		}
		else
		{
			$batch_cond=" and a.id<1";
		}
	}
	$sql_cond="";*/
	
	$batch_id_sql = sql_select("SELECT id as batch_id from pro_batch_create_mst where batch_no like '$batch_no' and status_active=1 and is_deleted=0");
	foreach($batch_id_sql as $row)
	{
		$batch_ids[]=$row[csf('batch_id')];
	}
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.issue_number LIKE '%$txt_search_common%'";	
			
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";				
 		}
		else if(trim($txt_search_by)==3) // for chllan no
		{
			$sql_cond .= " and a.req_no LIKE '%$txt_search_common%'";				
 		}		 
 	} 
	if($batch_no!='') $sql_cond .= " and a.batch_no='$batch_no'";
	if($ref_no!='') $sql_cond .= " and a.sub_order_ref='$ref_no'";	
	if( $fromDate!="" || $toDate!="" ) 
	{ 
		if($db_type==0)
		{
		 $sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		if($db_type==2 || $db_type==1)
		{ 
		$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($toDate,'yyyy-mm-dd',"-",1)."'";   }
	}
	if($db_type==2 ) { $year_id=" extract(year from a.insert_date) as year"; }
	if($db_type==0)  { $year_id="YEAR(a.insert_date) as year"; }
	
	//$batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where batch_against<>0 ","id","batch_no");
	$company_arr = return_library_array("SELECT id,company_name from lib_company where status_active =1 and is_deleted=0","id","company_name");
	//$arr=array(1=>$company_arr,3=>$receive_basis_arr,4=>$batch_arr,5=>$receipe_arr);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$yearid"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$yearid'";
	$sql = "SELECT a.issue_number_prefix_num, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.batch_no, a.issue_date, a.id, $year_id, a.is_posted_account, a.sub_order_ref, a.req_no
	from inv_issue_master a where a.company_id=$company and a.entry_form=308 and a.status_active=1 and a.is_deleted=0 $sql_cond $year_cond order by a.id desc";
	//echo $sql;
	$sql_result=sql_select($sql);
	?>
	<br/>
    <table align="center" cellspacing="0" width="940"  border="1" rules="all" class="rpt_table"  >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="50" >Issue No</th>
            <th width="50" >Year</th>
            <th width="130" >Company</th>
            <th width="130" >Issue Basis</th>
            <th width="100" >Issue Purpose</th>
            <th width="120" >Requisition No</th>
            <th width="100" >Aop Ref</th>
            <th width="120" >Batch No</th>
            <th>Issue Date</th> 
        </thead>
    </table>
	<div style="width:940px;max-height:300px; overflow-y:scroll" id="scroll_body">
 	<table align="center" cellspacing="0" width="920"  border="1" rules="all" class="rpt_table"  id="list_view" >
        <tbody id="list_view"> 
        <?
		$i=1;
	    foreach($sql_result as $inf)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			/*$batch_id_all=explode(",",$inf[csf("batch_no")]);
			$batch_no_all="";	
			foreach($batch_id_all as $b_id)
			{
			if($batch_no_all!=0)  $batch_no_all.=",".$batch_arr[$b_id]; 
			else  $batch_no_all=$batch_arr[$b_id]; 
			}*/
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $inf[csf("issue_number")]."_".$inf[csf("id")]."_".$inf[csf("issue_basis")]."_".$inf[csf("issue_purpose")]."_".$inf[csf("is_posted_account")];?>")' style="cursor:pointer">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="50" align="center"><? echo  $inf[csf("issue_number_prefix_num")]; ?></td>
                <td width="50" align="center"><? echo $inf[csf("year")]; ?></td>
                <td width="130"><? echo $company_arr[$inf[csf("company_id")]]; ?></td>
                <td width="130"><? echo $receive_basis_arr[$inf[csf("issue_basis")]]; ?></td>
                <td width="100"><? echo $general_issue_purpose[$inf[csf("issue_purpose")]]; ?></td>
                <td width="120" align="center"><? echo $inf[csf("req_no")]; ?></td>
                <td width="100" align="center"><? echo $inf[csf("sub_order_ref")]; ?></td>
                <td width="120" align="center" title="<? echo $inf[csf("batch_no")]; ?>"><? echo $inf[csf("batch_no")];; ?></td>
                <td align="center"><? echo change_date_format($inf[csf("issue_date")]); ?></td>
			
			</tr>
			<?
			$i++;	
		}
		?>
		</tbody>
    </table>
	</div>	
	<!--</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>-->
	<?
}


if($action=="populate_data_from_data")
{
	//req_id, batch_no, batch_id, sub_order_ref, remarks
	$sql = sql_select("SELECT id, location_id, issue_date, issue_basis, req_no, req_id, issue_purpose, company_id, loan_party, lap_dip_no, batch_id, batch_no, order_id, sub_order_id, style_ref, store_id, buyer_job_no, is_posted_account, lc_company, sub_order_ref, remarks, service_booking_no from inv_issue_master where id=$data and entry_form=308 and status_active=1 and is_deleted=0");
	$all_order_id="";$all_subcon_order="";
	foreach($sql as $row)
	{
		$all_order_id.=$row[csf("order_id")].",";
		$all_subcon_order.=$row[csf("sub_order_id")].",";
	}
	$all_order_id=implode(",",array_unique(explode(",",chop($all_order_id,","))));
	$all_subcon_order=implode(",",array_unique(explode(",",chop($all_subcon_order,","))));
	
	//echo $all_order_id."=".$all_subcon_order;die;
	
	if($all_order_id!="")
	{
		$po_sqls=sql_select("SELECT a.id as po_id, a.po_number from wo_po_break_down a where a.status_active=1 and a.is_deleted=0 and a.id in($all_order_id)");
		foreach($po_sqls as $row)
		{
			$po_data_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
		}
	}
	
	if($all_subcon_order!="")
	{
		$subcon_po_sqls=sql_select("SELECT a.id as po_id, a.job_no_mst, a.order_no,a.buyer_po_no,a.buyer_style_ref from subcon_ord_dtls a where a.status_active=1 and a.is_deleted=0 and a.id in($all_subcon_order)");
		foreach($subcon_po_sqls as $row)
		{
			$subcon_order_data[$row[csf('po_id')]]['job_no_mst']=$row[csf('job_no_mst')];
			$subcon_order_data[$row[csf('po_id')]]['order_no']=$row[csf('order_no')];
			$subcon_order_data[$row[csf('po_id')]]['buyer_po_no']=$row[csf('buyer_po_no')];
			$subcon_order_data[$row[csf('po_id')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
		}
	}
	
	foreach($sql as $row)
	{
		if($row[csf("store_id")]=='' || $row[csf("store_id")]==0) $row[csf("store_id")]=0;else $row[csf("store_id")]=$row[csf("store_id")];
		echo "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n"; 
		echo  "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("company_id")]."', 'load_drop_down_loan_party', 'loan_party_td');\n";
		echo  "load_drop_down( 'requires/chemical_dyes_issue_controller', ".$row[csf("company_id")]."+'**'+".$row[csf("issue_basis")].", 'load_drop_down_store', 'store_td');\n";
		
		echo "document.getElementById('txt_issue_date').value = '".change_date_format($row[csf("issue_date")],"dd-mm-yyyy","-")."';\n"; 
		echo "document.getElementById('cbo_issue_basis').value = '".$row[csf("issue_basis")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n"; 
		echo "document.getElementById('cbo_issue_purpose').value = '".$row[csf("issue_purpose")]."';\n";
		echo "fn_active_inactive('".$row[csf("issue_purpose")]."');\n";
		echo "document.getElementById('cbo_loan_party').value = '".$row[csf("loan_party")]."';\n"; 
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_id")]."';\n";
		  
		echo "document.getElementById('txt_req_no').value = '".$row[csf("req_no")]."';\n"; 
		echo "document.getElementById('txt_req_id').value = '".$row[csf("req_id")]."';\n"; 
		echo "document.getElementById('hidden_batch_id').value = '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value = '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('txt_recipe_no').value = '".$row[csf("service_booking_no")]."';\n";
		echo "document.getElementById('txt_recipe_id').value = '".$row[csf("lap_dip_no")]."';\n";
		echo "document.getElementById('txt_apo_ref').value = '".$row[csf("sub_order_ref")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		
		echo "document.getElementById('txt_job_no').value = '".$subcon_order_data[$row[csf("sub_order_id")]]['job_no_mst']."';\n"; 
		
		echo "document.getElementById('txt_order_no').value = '".$row[csf("buyer_job_no")]."';\n"; 
		echo "document.getElementById('hidden_order_id').value = '".$row[csf("sub_order_id")]."';\n"; 
		echo "document.getElementById('cbo_store_name').value = '".$row[csf("store_id")]."';\n";
		
        //echo "document.getElementById('txt_buyer_po').value = '".$po_data_arr[$row[csf("order_id")]]['po_number']."';\n"; 
		echo "document.getElementById('txt_buyer_po').value = '".$subcon_order_data[$row[csf("sub_order_id")]]['buyer_po_no']."';\n";
		
		echo "document.getElementById('hidden_buyer_po_id').value = '".$row[csf("order_id")]."';\n";
				  
		//echo "document.getElementById('txt_buyer_style').value = '".$row[csf("style_ref")]."';\n"; 
		echo "document.getElementById('txt_buyer_style').value = '".$subcon_order_data[$row[csf("sub_order_id")]]['buyer_style_ref']."';\n"; 
		
		echo "document.getElementById('update_for_cost').value = '1';\n"; 
		//echo "$('#txt_req_no').attr('disabled',true);\n"; 
		
		if(str_replace("'","",$row[csf("is_posted_account")])==1)
		{
			echo "disable_enable_fields( 'cbo_company_name*cbo_issue_basis*txt_issue_date*cbo_sub_process', 1, '', '' );\n"; // disable true
		}
		else
		{
			echo "disable_enable_fields( 'txt_issue_date*cbo_sub_process', 0, '', '' );\n";
		}
	}
	exit();
}

if($action=="save_update_delete")
{	  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($variable_lot==1) $lot_no=$val["BATCH_LOT"]; else $lot_no="";
	//--------------------------for check issue date with all product id's last receive date
	for ($i=1;$i<$total_row;$i++)
	{
		$txt_prod_id="txt_prod_id_".$i;
		$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
		
		if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
		{
			$prod_ids.=str_replace("'",'',$$txt_prod_id).",";
		}
		
		if(str_replace("'",'',$$txt_prod_id)!="")
		{
			$pord_ids_arr[str_replace("'",'',$$txt_prod_id)]=str_replace("'",'',$$txt_prod_id);
		}
	}        
	$prod_ids =  chop($prod_ids,",");

	$issue_store_id=str_replace("'","",$cbo_store_name);
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in($prod_ids) and store_id = $issue_store_id and transaction_type in (1,4,5) and status_active = 1 and is_deleted = 0", "max_date");   
	if($max_recv_date != "")
	{
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
		if ($issue_date < $max_recv_date) 
		{
				echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Item";
				disconnect($con); die;
		}
	}
	if(str_replace("'","",$cbo_issue_basis)==7)
	{
		$all_dtls_id="";
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$cbo_dose_base="cbo_dose_base_".$i;
			$txt_ratio="txt_ratio_".$i;
			$txt_recipe_qnty="txt_recipe_qnty_".$i;
			$txt_adj_per="txt_adj_per_".$i;
			$cbo_adj_type="cbo_adj_type_".$i;
			$txt_reqn_qnty="txt_reqn_qnty_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$transId="transId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			if(str_replace("'",'',$$updateIdDtls)!="") $all_dtls_id.=str_replace("'",'',$$updateIdDtls).",";
			if(str_replace("'",'',$$txt_prod_id)!="") $all_prod_id.=str_replace("'",'',$$txt_prod_id).",";
		}
		$all_dtls_id=chop($all_dtls_id,",");
		$all_prod_id=chop($all_prod_id,",");
		$dtls_cond="";
		if($all_dtls_id!="") $dtls_cond=" and b.id not in($all_dtls_id)";
		$total_issue_sql=sql_select("SELECT a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit from inv_issue_master a, dyes_chem_issue_dtls b where a.id=b.mst_id and a.entry_form=308 and a.issue_basis=7 and a.req_id='".str_replace("'","",$txt_req_id)."' and b.sub_process='".str_replace("'","",$cbo_sub_process)."' $dtls_cond");
		$prev_issue=array();
		foreach($total_issue_sql as $row)
		{
			$prev_issue[$row[csf("product_id")]]=$row[csf("req_qny_edit")];
		}
		
		unset($total_issue_sql);
		$prod_cond="";
		if($all_prod_id!="") $prod_cond=" and a.id in($all_prod_id)";
		$sql_req=sql_select("SELECT a.id, b.required_qnty as required_qnty, b.req_qny_edit 
		from product_details_master a, dyes_chem_issue_requ_dtls b 
		where a.id=b.product_id and b.mst_id='".str_replace("'","",$txt_req_id)."' and b.multicolor_id='".str_replace("'","",$cbo_sub_process)."' and b.status_active=1 and b.is_deleted=0 and a.company_id='".str_replace("'","",$cbo_company_name)."' and a.item_category_id in(5,6,7,22,23) and a.status_active=1 and a.is_deleted=0 $prod_cond");
		$req_data=array();
		foreach($sql_req as $row)
		{
			$req_data[$row[csf("id")]]=$row[csf("req_qny_edit")];
		}
		//echo "10**";print_r($req_data);disconnect($con); die;
		unset($sql_req);
		
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$cbo_dose_base="cbo_dose_base_".$i;
			$txt_ratio="txt_ratio_".$i;
			$txt_recipe_qnty="txt_recipe_qnty_".$i;
			$txt_adj_per="txt_adj_per_".$i;
			$cbo_adj_type="cbo_adj_type_".$i;
			$txt_reqn_qnty="txt_reqn_qnty_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$transId="transId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			$cu_req_qnty=(($req_data[str_replace("'","",$$txt_prod_id)]-$prev_issue[str_replace("'","",$$txt_prod_id)])*1);
			$current_issue=str_replace("'","",$$txt_reqn_qnty_edit)*1;
			if($current_issue>$cu_req_qnty)
			{
				echo "20**Issue Quantity Not Allow Over Requisition Quantity. $current_issue = $cu_req_qnty";disconnect($con); die;
			}
		}
	}
	//-----------------------------------------------------------------------------
	$trans_sql=sql_select("select ID, MST_ID, PROD_ID, STORE_ID, BATCH_LOT, TRANSACTION_TYPE, ITEM_CATEGORY, CONS_QUANTITY, CONS_AMOUNT, BALANCE_QNTY, BALANCE_AMOUNT, CONS_RATE, STORE_AMOUNT
 	from inv_transaction where status_active=1 and is_deleted=0 and prod_id in(".implode(",",$pord_ids_arr).") and store_id = $issue_store_id");
	$trans_data_arr=array();$chemDataArr=array(); $dyesDataArr=array(); $auxChemDataArr=array();
	foreach($trans_sql as $val)
	{
		if($variable_lot==1) $lot_no=strtoupper(trim($val["BATCH_LOT"])); else $lot_no="";
		if(($val["TRANSACTION_TYPE"]==1 || $val["TRANSACTION_TYPE"]==4 || $val["TRANSACTION_TYPE"]==5) && $val["ITEM_CATEGORY"]==5 && $val["BALANCE_QNTY"]>0)
		{
			$chemDataArr[$val["PROD_ID"]].=$val["ID"]."**".$val["CONS_RATE"]."**".$val["BALANCE_QNTY"]."**".$val["BALANCE_AMOUNT"].",";
		}
		if(($val["TRANSACTION_TYPE"]==1 || $val["TRANSACTION_TYPE"]==4 || $val["TRANSACTION_TYPE"]==5) && $val["ITEM_CATEGORY"]==6 && $val["BALANCE_QNTY"]>0)
		{
			$dyesDataArr[$val["PROD_ID"]].=$val["ID"]."**".$val["CONS_RATE"]."**".$val["BALANCE_QNTY"]."**".$val["BALANCE_AMOUNT"].",";
		}
		if(($val["TRANSACTION_TYPE"]==1 || $val["TRANSACTION_TYPE"]==4 || $val["TRANSACTION_TYPE"]==5) && ($val["ITEM_CATEGORY"]==7 || $val["ITEM_CATEGORY"]==23) && $val["BALANCE_QNTY"]>0)
		{
			$auxChemDataArr[$val["PROD_ID"]].=$val["ID"]."**".$val["CONS_RATE"]."**".$val["BALANCE_QNTY"]."**".$val["BALANCE_AMOUNT"].",";
		}
		
		if($val["TRANSACTION_TYPE"]==1 || $val["TRANSACTION_TYPE"]==4 || $val["TRANSACTION_TYPE"]==5)
		{
			$trans_data_arr[$val["PROD_ID"]][$lot_no]["STOCK"] +=$val["CONS_QUANTITY"];
			$trans_data_arr[$val["PROD_ID"]][$lot_no]["STORE_AMOUNT"] +=$val["STORE_AMOUNT"];
		}
		else
		{
			if(str_replace("'","",$update_id) != $val["MST_ID"])
			{
				$trans_data_arr[$val["PROD_ID"]][$lot_no]["STOCK"] -=$val["CONS_QUANTITY"];
				$trans_data_arr[$val["PROD_ID"]][$lot_no]["STORE_AMOUNT"] -=$val["STORE_AMOUNT"];
			}
		}
	}


	$product_arr=array();
	$dataArray = sql_select("SELECT id, avg_rate_per_unit, current_stock, stock_value from product_details_master where item_category_id in (5,6,7,22,23) and status_active=1 and is_deleted=0");
	foreach($dataArray as $row)
	{
		$product_arr[$row[csf("id")]]['qty']=$row[csf("current_stock")];
		$product_arr[$row[csf("id")]]['rate']=$row[csf("avg_rate_per_unit")];
		$product_arr[$row[csf("id")]]['val']=$row[csf("stock_value")];
	}
	$sql_store = sql_select("SELECT id, prod_id, store_id, category_id as cat_id, rate as avg_rate, cons_qty as current_stock, amount as stock_value, last_purchased_qnty as last_qty, lot 
	from inv_store_wise_qty_dtls where company_id=$cbo_company_name and store_id=$issue_store_id and status_active=1 and is_deleted=0 ");
	$store_arr=array();$store_idarr=array();
	foreach($sql_store as $row)
	{
		if($variable_lot==1) $lot_no=$row[csf("lot")]; else $lot_no="";
		$store_arr[$row[csf("id")]]['qty']=$row[csf("current_stock")];
		$store_arr[$row[csf("id")]]['avg_rate']=$row[csf("avg_rate")];
		//$store_arr[$row[csf("id")]]['val']=$row[csf("stock_value")];
		//$store_arr[$row[csf("id")]]['last_qty']=$row[csf("last_qty")];
		$store_idarr[$row[csf("prod_id")]][$lot_no][$row[csf("store_id")]][$row[csf("cat_id")]]['id']=$row[csf("id")];
		$store_wise_stock_qnty_arr[$row[csf("prod_id")]][$lot_no] = $row[csf("current_stock")];
	}
	
	//echo "10**".$flag;disconnect($con); die;
	$lifoFifoArr=return_library_array("SELECT item_category_id, store_method from variable_settings_inventory where company_name=$cbo_company_name and variable_list=17 and item_category_id in (5,6,7,22,23) and status_active=1 and is_deleted=0","item_category_id","store_method");
			
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$dys_che_issue_num=''; $dys_che_update_id=''; $product_id=0;
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==2 || $db_type==1) { $year_id=" extract(year from insert_date)="; }
			if($db_type==0)  { $year_id="YEAR(insert_date)="; }
			
			$new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_name,'ADCI',308,date("Y",time()) ));
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			
			
			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number,issue_basis, issue_purpose, entry_form, company_id, location_id, store_id, buyer_job_no, style_ref, req_no, req_id, issue_date, loan_party, lap_dip_no, service_booking_no, order_id, fso_no, sub_order_id, inserted_by, insert_date, lc_company, batch_no, batch_id, sub_order_ref, remarks";
			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_basis.",".$cbo_issue_purpose.",308,".$cbo_company_name.",".$cbo_location_name.",".$cbo_store_name.",".$txt_job_no.",".$txt_buyer_style.",".$txt_req_no.",".$txt_req_id.",".$txt_issue_date.",".$cbo_loan_party.",".$txt_recipe_id.",".$txt_recipe_no.",".$hidden_buyer_po_id.",".$txt_order_no.",".$hidden_order_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_company_name.",".$txt_batch_no.",".$hidden_batch_id.",".$txt_apo_ref.",".$txt_remarks.")";
			//$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			$dys_che_issue_num=$new_system_id[0];
			$dys_che_update_id=$id;
		}
		else
		{
			$field_array_update="issue_purpose*location_id*buyer_job_no*style_ref*req_no*req_id*issue_date*loan_party*lap_dip_no*service_booking_no*order_id*fso_no*sub_order_id*updated_by*update_date*lc_company*batch_no*batch_id*sub_order_ref*remarks";
			$data_array_update="".$cbo_issue_purpose."*".$cbo_location_name."*".$txt_job_no."*".$txt_buyer_style."*".$txt_req_no."*".$txt_req_id."*".$txt_issue_date."*".$cbo_loan_party."*".$txt_recipe_id."*".$txt_recipe_no."*".$hidden_buyer_po_id."*".$txt_order_no."*".$hidden_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_name."*".$txt_batch_no."*".$hidden_batch_id."*".$txt_apo_ref."*".$txt_remarks."";
			
			$dys_che_issue_num=str_replace("'","",$txt_mrr_no);
			$dys_che_update_id=str_replace("'","",$update_id);
			//$rID=sql_update("inv_issue_master", $field_array_update, $data_array_update, "id", $update_id,1); 
		}
		
		$all_prod_id_c=''; $all_prod_id_d=''; $all_prod_id_ac=''; $all_prod_ids=''; $all_store_ids='';$all_cat_ids='';$all_reqn_qnty_edit='';$store_stock_arr=array();
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_lot="txt_lot_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			if(str_replace("'",'',$$txt_item_cat)==5) $all_prod_id_c.=str_replace("'",'',$$txt_prod_id).",";
			else if(str_replace("'",'',$$txt_item_cat)==6) $all_prod_id_d.=str_replace("'",'',$$txt_prod_id).",";
			else $all_prod_id_ac.=str_replace("'",'',$$txt_prod_id).",";
			if($variable_lot==1) $lot_no=str_replace("'",'',$$txt_lot); else $lot_no="";
			
			$trans_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][strtoupper(trim($lot_no))]["STOCK"];			
			if(number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($trans_stock,6,'.','') )
			{
				//echo "10**".$store_prod_qnty."=".str_replace("'",'',$$txt_prod_id)."=".str_replace("'",'',$$txt_lot)."=".$dyes_lot;die;
				echo "20**Issue Quantity Not Allow Over Stock Quantity. \n Your Input Qnty = ".str_replace("'",'',$$txt_reqn_qnty_edit)."\n Transaction Wise Stock = " .$trans_stock;disconnect($con);die;
			}
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$prod_id=str_replace("'",'',$$txt_prod_id);
				$cat_id=str_replace("'",'',$$txt_item_cat);
				
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$store_stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
				$txt_reqn_qnty_edit=str_replace("'",'',$$txt_reqn_qnty_edit);
				$storeid=$store_idarr[$prod_id][$lot_no][$issue_store_id][$cat_id]['id'];
				$store_stock_arr[$storeid][$prod_id][$lot_no][$issue_store_id][$cat_id]['issue_qty']=$txt_reqn_qnty_edit;
					
				$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
				$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				//$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
			}
		}
		
		//echo "10**".$cbo_store_id;disconnect($con); die;
		$cbo_company_name=str_replace("'",'',$cbo_company_name);
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
		$reg_issue_qty=0;//$curr_stock=0;
		for ($i=1;$i<$total_row;$i++)
		{
			$prod_id="txt_prod_id_".$i;
			$txt_lot="txt_lot_".$i;
			$item_cat="txt_item_cat_".$i;
			$reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$prod_id=str_replace("'",'',$$prod_id);
			$cat_id=str_replace("'",'',$$item_cat);
			
			if($variable_lot==1) $lot_no=str_replace("'",'',$$txt_lot); else $lot_no="";
			
			$storeId=$store_idarr[$prod_id][$lot_no][$issue_store_id][$cat_id]['id'];
			
			if(str_replace("'",'',$$reqn_qnty_edit)>0)
			{
				if(str_replace("'",'',$storeId)!="")
				{
					$reg_issue_qty=$store_stock_arr[$storeId][$prod_id][$lot_no][$issue_store_id][$cat_id]['issue_qty'];
					$curr_stock=$store_arr[$storeId]['qty']-$reg_issue_qty;
					$s_avg_rate=$store_arr[$storeId]['avg_rate'];
					$store_StockValue=$curr_stock*$s_avg_rate;
					$sid_arr[]=str_replace("'",'',$storeId);
					$data_array_store[str_replace("'",'',$storeId)] =explode(",",("".$reg_issue_qty.",".$curr_stock.",".$store_StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
			}
		}
		//echo "10**";disconnect($con); die;	
		
		
		$all_prod_id_c=chop($all_prod_id_c,',');
		$all_prod_id_d=chop($all_prod_id_d,',');
		$all_prod_id_ac=chop($all_prod_id_ac,',');
		
		
		$updateID_array=array();
		$update_data=array();
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, requisition_no, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, cons_uom, cons_quantity, cons_rate, cons_amount, store_id, inserted_by, insert_date, batch_lot, store_rate, store_amount";
		
		$field_array_dtls="id, mst_id, trans_id, requ_no, batch_id, recipe_id, requisition_basis, sub_process, product_id, item_category, dose_base, ratio, recipe_qnty, adjust_percent, adjust_type, required_qnty, req_qny_edit, inserted_by, insert_date";
		$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
		$field_array_mrr="id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		
		$adcomma=1;
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_lot="txt_lot_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$cbo_dose_base="cbo_dose_base_".$i;
			$txt_ratio="txt_ratio_".$i;
			$txt_recipe_qnty="txt_recipe_qnty_".$i;
			$txt_adj_per="txt_adj_per_".$i;
			$cbo_adj_type="cbo_adj_type_".$i;
			$txt_reqn_qnty="txt_reqn_qnty_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$transId="transId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			$item_lot=str_replace("'",'',$$txt_lot);
			$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
			if($variable_lot==1) $lot_no=$item_lot; else $lot_no="";
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("DYES_CHEM_ISSUE_DTLS_PK_SEQ", "dyes_chem_issue_dtls", $con);
				
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];

				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
				$trans_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$lot_no]["STOCK"];
				$trans_store_amount=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$lot_no]["STORE_AMOUNT"];
				$store_item_rate=0;
				if($trans_store_amount !=0 && $trans_stock !=0) $store_item_rate=$trans_store_amount/$trans_stock;
				$issue_store_value=$txt_reqn_qnty_e*$store_item_rate;	
				
				$issue_stock_value = $avg_rate*$txt_reqn_qnty_e;
								
				if ($adcomma!=1) $data_array_trans .=",";
				$data_array_trans.="(".$id_trans.",".$dys_che_update_id.",".$txt_req_id.",".$cbo_issue_basis.",".$txt_req_id.",".$cbo_company_name.",".$$txt_prod_id.",".$$txt_item_cat.",2,".$txt_issue_date.",12,".$txt_reqn_qnty_e.",'".number_format($avg_rate,10,'.','')."',".number_format($issue_stock_value,8,'.','').",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$txt_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
				
				if ($adcomma!=1) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$dys_che_update_id.",".$id_trans.",".$txt_req_no.",".$txt_req_id.",".$txt_recipe_id.",".$cbo_issue_basis.",".$cbo_sub_process.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				//product master table data UPDATE START----------------------//
				$currentStock   = $stock_qnty-$txt_reqn_qnty_e;
				$StockValue	 	= $currentStock*$avg_rate;
				$avgRate	 	= number_format($avg_rate,$dec_place[3],'.',''); 
				
				if(str_replace("'",'',$$txt_prod_id)!="")
				{
					$id_arr[]=str_replace("'",'',$$txt_prod_id);
					$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".number_format($StockValue,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
				//------------------ product_details_master END--------------//
				//LIFO/FIFO Start-----------------------------------------------//txt_issue_qnty
				

				//$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=".$$txt_prod_id." and balance_qnty>0 and transaction_type in (1,4,5) and item_category=".$$txt_item_cat." order by transaction_date $cond_lifofifo");
				
				if(str_replace("'",'',$$txt_item_cat)==5) $dataArray=explode(",",chop($chemDataArr[str_replace("'",'',$$txt_prod_id)],','));
				else if(str_replace("'",'',$$txt_item_cat)==6) $dataArray=explode(",",chop($dyesDataArr[str_replace("'",'',$$txt_prod_id)],','));
				else $dataArray=explode(",",chop($auxChemDataArr[str_replace("'",'',$$txt_prod_id)],','));
				//if(str_replace("'",'',$$txt_prod_id)==162) {print_r($dataArray);}
				if(count(array_filter($dataArray))>0)
				{
					foreach($dataArray as $val)
					{				
						$value=explode("**",$val);
						$recv_trans_id = $value[0]; 
						$cons_rate = $value[1];
						$balance_qnty = $value[2];
						$balance_amount = $value[3];
						
						$issueQntyBalance = $balance_qnty-$txt_reqn_qnty_e; // minus issue qnty
						$issueStockBalance = $balance_amount-($txt_reqn_qnty_e*$cons_rate);
						
						if($issueQntyBalance>=0)
						{					
							$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
							$amount = $txt_reqn_qnty_e*$cons_rate;
							//for insert
							if($data_array_mrr!="") $data_array_mrr .= ",";  
							$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",308,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
							
							//for update
							$updateID_array[]=$recv_trans_id; 
							$update_data[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
							//$mrrWiseIsID++;
							break;
						}
						else if($issueQntyBalance<0)
						{
							$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
							
							$issueQntyBalance = $txt_reqn_qnty_e-$balance_qnty;				
							//$txt_reqn_qnty_e = $balance_qnty;				
							$amount = $txt_reqn_qnty_e*$cons_rate;
							//echo $balance_qnty;

							//for insert
							if($data_array_mrr!="") $data_array_mrr .= ",";  
							$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",308,".$$txt_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
							//for update
							$updateID_array[]=$recv_trans_id; 
							$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
							$txt_reqn_qnty_e = $issueQntyBalance;
							//$mrrWiseIsID++;
						}
					}//end foreach
				}
				$adcomma++;
				//$id_trans=$id_trans+1;
				//$id_dtls=$id_dtls+1;
			}
		}
		
		$rID=$rID2=$rID3=$rID4=$mrrWiseIssueID=$upTrID=$store_ID=true;
		if(str_replace("'","",$update_id)=="")
		{
			//echo "5**insert into inv_issue_master (".$field_array.") values ".$data_array;disconnect($con); die;
		 	$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
		}
		else
		{		
			$rID=sql_update("inv_issue_master", $field_array_update, $data_array_update, "id", $update_id,1); 
		}
		if($rID) $flag=1; else $flag=0;
		
		// echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;disconnect($con); die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) { if($rID2) $flag=1; else $flag=0; } 
		//mysql_query("ROLLBACK"); echo "10**".$flag;disconnect($con); die;
		//echo "10**insert into dyes_chem_issue_dtls (".$field_array_dtls.") values ".$data_array_dtls;disconnect($con); die;
		$rID3=sql_insert("dyes_chem_issue_dtls",$field_array_dtls,$data_array_dtls,1); 
		if($flag==1) { if($rID3) $flag=1; else $flag=0; } 
		
	    $rID4=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod, $data_array_prod, $id_arr ),1);
        if($flag==1) { if($rID4) $flag=1; else $flag=0; } 
		$mrrWiseIssueID=true;
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			if($flag==1) { if($mrrWiseIssueID) $flag=1; else $flag=0; }
		}
		//
		//echo "5**0**insert into inv_mrr_wise_issue_details (".$field_array_mrr.") values ".$data_array_mrr;disconnect($con); die;
		
		//transaction table stock update here------------------------//
		//$upTrID=true;
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array),1);
			if($flag==1) { if($upTrID) $flag=1; else $flag=0;} 
		}
		//$store_ID=true;
		if(count($sid_arr)>0)
		{
			$store_ID=execute_query(bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr ),1);
			if($flag==1) { if($store_ID) $flag=1; else $flag=0; } 
		}
		//echo "10**".$rID.'='.$rID2.'='.$rID3.'='.$rID4.'='.$mrrWiseIssueID.'='.$upTrID.'='.$store_ID.'='.$flag;disconnect($con); die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$dys_che_update_id."**".$dys_che_issue_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$dys_che_update_id."**".$dys_che_issue_num."**0";
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
		
		//table lock here 
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
		
		$all_prod_id_c=''; $all_prod_id_d=''; $all_prod_id_ac=''; $all_transId='';  $all_prod_ids='';$all_cat_ids='';
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_lot="txt_lot_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$transId="transId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			
			if($variable_lot==1) $lot_no=str_replace("'",'',$$txt_lot); else $lot_no="";
			
			if(str_replace("'",'',$$txt_item_cat)==5) $all_prod_id_c.=str_replace("'",'',$$txt_prod_id).",";
			else if(str_replace("'",'',$$txt_item_cat)==6) $all_prod_id_d.=str_replace("'",'',$$txt_prod_id).",";
			else $all_prod_id_ac.=str_replace("'",'',$$txt_prod_id).",";
			
			$all_transId.=str_replace("'",'',$$transId).",";			
			

			$trans_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][strtoupper(trim($lot_no))]["STOCK"];
			$store_c_stock=($trans_stock+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;			
			if(number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($store_c_stock,6,'.','') )
			{
				//echo "10**".$store_prod_qnty."=".str_replace("'",'',$$txt_prod_id)."=".str_replace("'",'',$$txt_lot)."=".$dyes_lot;die;
				echo "20**Issue Quantity Not Allow Over Stock Quantity. \n Your Input Qnty = ".str_replace("'",'',$$txt_reqn_qnty_edit)."\n Transaction Wise Stock = " .$store_c_stock;disconnect($con);die;
			}

			//store wise
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$store_stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
				$txt_reqn_qnty_edit=str_replace("'",'',$$txt_reqn_qnty_edit);
				$hidtxt_reqn_qnty_edit=str_replace("'",'',$$hidtxt_reqn_qnty_edit);
				$prod_id=str_replace("'",'',$$txt_prod_id);
				$cat_id=str_replace("'",'',$$txt_item_cat);
				
				
				$storeid=$store_idarr[$prod_id][$lot_no][$issue_store_id][$cat_id]['id'];
				$store_stock_arr[$storeid][$prod_id][$lot_no][$issue_store_id][$cat_id]['issue_qty']=$txt_reqn_qnty_edit;
				$store_stock_arr[$storeid][$prod_id][$lot_no][$issue_store_id][$cat_id]['hidden_qty']=$hidtxt_reqn_qnty_edit;
				//$store_stock_arr[str_replace("'",'',$$txt_prod_id)]['hidd_qty']=str_replace("'",'',$$hidtxt_reqn_qnty_edit);
				$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
				$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				//$all_store_ids.=str_replace("'",'',$$cbo_store_name).",";
			}
		}
		$cbo_company_name=str_replace("'",'',$cbo_company_name);
		$stock_store_arr=fnc_store_wise_qty_operation($cbo_company_name,$issue_store_id,$all_cat_ids,$all_prod_ids,2);
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
		$storeArrId=array_unique(explode(",",$stock_store_arr));
		
		$chemDataArr=array(); $dyesDataArr=array(); $auxChemDataArr=array(); $adjTransDataArr=array();
		
		$isLIFOfifoC=$lifoFifoArr[5];
		if($isLIFOfifoC==2) $cond_lifofifoC=" DESC"; else $cond_lifofifoC=" ASC";
		
		$isLIFOfifoD=$lifoFifoArr[6];
		if($isLIFOfifoD==2) $cond_lifofifoD=" DESC"; else $cond_lifofifoD=" ASC";
		
		$isLIFOfifoA=$lifoFifoArr[7];
		if($isLIFOfifoA==2) $cond_lifofifoA=" DESC"; else $cond_lifofifoA=" ASC";
		
		$isLIFOfifoA=$lifoFifoArr[22];
		if($isLIFOfifoA==2) $cond_lifofifoA=" DESC"; else $cond_lifofifoA=" ASC";
		
		$isLIFOfifoA=$lifoFifoArr[23];
		if($isLIFOfifoA==2) $cond_lifofifoA=" DESC"; else $cond_lifofifoA=" ASC";
		
		$all_prod_id_c=chop($all_prod_id_c,',');
		$all_prod_id_d=chop($all_prod_id_d,',');
		$all_prod_id_ac=chop($all_prod_id_ac,',');
		$all_transId=chop($all_transId,',');
		
		if($all_prod_id_c!="")
		{
			$chemData = sql_select("SELECT prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_c) and balance_qnty>0 and transaction_type in (1,4,5) and item_category=5 order by transaction_date $cond_lifofifoC");
			foreach($chemData as $row)
			{
				$chemDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		
		if($all_prod_id_d!="")
		{
			$dyesData = sql_select("SELECT prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_d) and balance_qnty>0 and transaction_type in (1,4,5) and item_category=6 order by transaction_date $cond_lifofifoD");
			foreach($dyesData as $row)
			{
				$dyesDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		
		if($all_prod_id_ac!="")
		{
			$auxChemData = sql_select("SELECT prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_ac) and balance_qnty>0 and transaction_type in (1,4,5) and item_category in(7,22,23) order by transaction_date $cond_lifofifoA");
			foreach($auxChemData as $row)
			{
				$auxChemDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		//echo "10**select a.item_category, a.prod_id, b.issue_trans_id, a.id, a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id in($all_transId) and b.entry_form=5";disconnect($con); die;
		$prev_prod_id_arr=array();
		$transData = sql_select("SELECT a.item_category, a.prod_id, b.issue_trans_id, a.id, a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id in($all_transId) and b.entry_form=308 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"); 
		foreach($transData as $row)
		{
			$adjTransDataArr[$row[csf("item_category")]][$row[csf("issue_trans_id")]].=$row[csf("id")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")]."**".$row[csf("issue_qnty")]."**".$row[csf("rate")]."**".$row[csf("amount")].",";
			
		}
		
		$updateID_array=array(); $update_data=array();
		
		$field_array_update="issue_purpose*location_id*buyer_job_no*style_ref*req_no*req_id*issue_date*loan_party*lap_dip_no*service_booking_no*order_id*fso_no*sub_order_id*updated_by*update_date*lc_company*batch_no*batch_id*sub_order_ref*remarks";
		$data_array_update="".$cbo_issue_purpose."*".$cbo_location_name."*".$txt_job_no."*".$txt_buyer_style."*".$txt_req_no."*".$txt_req_id."*".$txt_issue_date."*".$cbo_loan_party."*".$txt_recipe_id."*".$txt_recipe_no."*".$hidden_buyer_po_id."*".$txt_order_no."*".$hidden_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_name."*".$txt_batch_no."*".$hidden_batch_id."*".$txt_apo_ref."*".$txt_remarks."";
		//echo $data_array_update;
		$dys_che_issue_num=$txt_mrr_no;
		$dys_che_update_id=$update_id;
		
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		
		$up_field_array_trans="prod_id*item_category*transaction_date*cons_uom*cons_quantity*cons_rate*cons_amount*store_id*updated_by*update_date*batch_lot*store_rate*store_amount";
		$up_field_array_dtls="sub_process*product_id*item_category*dose_base*ratio*recipe_qnty*adjust_percent*adjust_type*required_qnty*req_qny_edit*updated_by*update_date";
		$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
		//echo $total_row;disconnect($con); die;
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$user_id=$_SESSION['logic_erp']['user_id']; 
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_lot="txt_lot_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$cbo_dose_base="cbo_dose_base_".$i;
			$txt_ratio="txt_ratio_".$i;
			$txt_recipe_qnty="txt_recipe_qnty_".$i;
			$txt_adj_per="txt_adj_per_".$i;
			$cbo_adj_type="cbo_adj_type_".$i;
			$txt_reqn_qnty="txt_reqn_qnty_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$transId="transId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			
			if($variable_lot==1) $lot_no=str_replace("'",'',$$txt_lot); else $lot_no="";
			$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
			
			$current_store_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$item_lot]["STOCK"];
			$current_store_amt=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$item_lot]["STORE_AMOUNT"];
			$store_item_rate=0;
			if($current_store_amt !=0 && $current_store_stock !=0) $store_item_rate=$current_store_amt/$current_store_stock;
			$issue_store_value = $store_item_rate*$txt_reqn_qnty_e;
			
			$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
			$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
			$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];

			
			
			$issue_stock_value = $avg_rate*str_replace("'","",$txt_reqn_qnty_e);
			
			if(str_replace("'",'',$$transId)!="")
			{
				$id_arr_trans[]=str_replace("'",'',$$transId);
				$data_array_trans[str_replace("'",'',$$transId)] =explode("*",("".$$txt_prod_id."*".$$txt_item_cat."*".$txt_issue_date."*12*".$txt_reqn_qnty_e."*'".$avg_rate."'*".$issue_stock_value."*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$txt_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));
			}
			
			if(str_replace("'",'',$$updateIdDtls)!="")
			{
				$id_arr_dtls[]=str_replace("'",'',$$updateIdDtls);
				$data_array_dtls[str_replace("'",'',$$updateIdDtls)] =explode(",",("".$cbo_sub_process.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
			}
			//product master table data UPDATE START----------------------//
			$currentStock   = $stock_qnty-$txt_reqn_qnty_e+str_replace("'", '',$$hidtxt_reqn_qnty_edit);
			$StockValue	 	= $currentStock*$avg_rate;
			$avgRate	 	= number_format($avg_rate,$dec_place[3],'.',''); 

			if(str_replace("'",'',$$txt_prod_id)!="")
			{
				$id_arr[]=str_replace("'",'',$$txt_prod_id);
				$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".number_format($StockValue,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
			}
			//------------------ product_details_master END--------------//
			
			//-----Store Wise Stock----//
			$prod_id=str_replace("'",'',$$txt_prod_id);
			$cat_id=str_replace("'",'',$$txt_item_cat);
			$storeId=$store_idarr[$prod_id][$lot_no][$issue_store_id][$cat_id]['id'];
			// echo $$txt_reqn_qnty_e.'aaz';
			if(str_replace("'",'',$txt_reqn_qnty_e)>0)
			{
				//echo $$txt_reqn_qnty_e.'a4';
				if(in_array($storeId,$storeArrId))
				{
					if(str_replace("'",'',$storeId)!="")
					{
						$req_hidden_qty=$store_stock_arr[$storeId][$prod_id][$lot_no][$issue_store_id][$cat_id]['hidden_qty'];
						$reg_issue_qty=$store_stock_arr[$storeId][$prod_id][$lot_no][$issue_store_id][$cat_id]['issue_qty'];
						$store_curr_stock=$store_arr[$storeId]['qty']-$reg_issue_qty+$req_hidden_qty;
						$s_avg_rate=$store_arr[$storeId]['avg_rate'];
						$store_StockValue=$store_curr_stock*$s_avg_rate;
						$sid_arr[]=str_replace("'",'',$storeId);
						$data_array_store[str_replace("'",'',$storeId)] =explode(",",("".$reg_issue_qty.",".$store_curr_stock.",".$store_StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
					}
				}
			}
			//----Store Wise Stock End------//
		
			$transDataArray=explode(",",chop($adjTransDataArr[str_replace("'",'',$$txt_item_cat)][str_replace("'",'',$$transId)],','));
			foreach($transDataArray as $val)
			{				
				$value=explode("**",$val);
				$id = $value[0];
				$balanceQnty = $value[1];
				$balanceAmount = $value[2];
				$issueQnty = $value[3];
				$rate = $value[4];
				$amount = $value[5];

				$adjBalance = $balanceQnty+$issueQnty;
				$adjAmount = $balanceAmount+$amount;
				
				if($adjBalance>0)
				{
					$updateID_array[]=$id; 
					$update_data[$id]=explode("*",("".$adjBalance."*".$adjAmount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					
					$trans_data_array[$id]['qnty']=$adjBalance;
					$trans_data_array[$id]['amnt']=$adjAmount;
					if($balanceQnty<=0)
					{
						$prev_prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
						$prev_prod_data_arr[str_replace("'",'',$$txt_prod_id)].=$id."**".$rate."**".$adjBalance."**".$adjAmount.",";
					}
				}
			}
			
			$dataArray_prev=array();
			if(in_array(str_replace("'",'',$$txt_prod_id),$prev_prod_id_arr))
			{
				$dataArray_prev[]=chop($prev_prod_data_arr[str_replace("'",'',$$txt_prod_id)],',');
			}
			if(str_replace("'",'',$$txt_item_cat)==5) $dataArray_suff=explode(",",chop($chemDataArr[str_replace("'",'',$$txt_prod_id)],','));
			else if(str_replace("'",'',$$txt_item_cat)==6) $dataArray_suff=explode(",",chop($dyesDataArr[str_replace("'",'',$$txt_prod_id)],','));
			else $dataArray_suff=explode(",",chop($auxChemDataArr[str_replace("'",'',$$txt_prod_id)],','));
			
			$dataArray=array_merge($dataArray_prev,$dataArray_suff);
			foreach(array_filter($dataArray) as $val)
			{	
				//count($dataArray);
				//echo $val;
				$value=explode("**",$val);
				$recv_trans_id = $value[0]; 
				$cons_rate = $value[1];
				$balance_qnty = $value[2];
				$balance_amount = $value[3];

				if($trans_data_array[$recv_trans_id]['qnty']=="")
				{ 
					$issueQntyBalance = $balance_qnty-$txt_reqn_qnty_e; 
					$issueStockBalance = $balance_amount-($txt_reqn_qnty_e*$cons_rate);
				}
				else
				{
					$issueQntyBalance = $trans_data_array[$recv_trans_id]['qnty']-$txt_reqn_qnty_e;
					$issueStockBalance = $trans_data_array[$recv_trans_id]['amnt']-($txt_reqn_qnty_e*$cons_rate);
				}
				//echo $txt_reqn_qnty_e."<br>";
				if($issueQntyBalance>=0)
				{					
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					$amount = $txt_reqn_qnty_e*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",308,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					if(!in_array($recv_trans_id,$updateID_array))
					{
						$updateID_array[]=$recv_trans_id; 
					}
					
					$update_data[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					//$mrrWiseIsID++;
					break;
				}
				else if($issueQntyBalance<0)
				{
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					$issueQntyBalance = $txt_reqn_qnty_e-$balance_qnty;				
					//$txt_reqn_qnty_e = $balance_qnty;				
					//echo $txt_reqn_qnty_e."<br>";
					$amount = $txt_reqn_qnty_e*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",308,".$$txt_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					 
					if(!in_array($recv_trans_id,$updateID_array))
					{
						$updateID_array[]=$recv_trans_id; 
					}
					
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$txt_reqn_qnty_e = $issueQntyBalance;
					//$mrrWiseIsID++;
				}
			}//end foreach
		}
		
		$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id in($all_transId) and entry_form=308 and status_active=1 and is_deleted=0",1);
		if($query2) $flag=1; else $flag=0;
		
		$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1); 
		if($flag==1) { if($rID) $flag=1; else $flag=0; }
		
		if(count($id_arr_trans)>0)
		{  
			//echo bulk_update_sql_statement("inv_transaction","id",$up_field_array_trans,$data_array_trans,$id_arr_trans);disconnect($con); die;
		    $upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$up_field_array_trans,$data_array_trans,$id_arr_trans),1);
			if($flag==1) 
			{	 if($upTrID) $flag=1; else $flag=0; }
		}
		
		if(count($id_arr_dtls)>0)
		{ 
		    $upDtID=execute_query(bulk_update_sql_statement("dyes_chem_issue_dtls","id",$up_field_array_dtls,$data_array_dtls,$id_arr_dtls),1);
			if($flag==1) { if($upDtID) $flag=1; else $flag=0; } 
		}
		
		if(count($id_arr)>0)
		{
			//echo bulk_update_sql_statement( "product_details_master", "id", $field_array_prod, $data_array_prod, $id_arr );disconnect($con); die;
		    $rID4=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod, $data_array_prod, $id_arr ),1);
			if($flag==1) { if($rID4) $flag=1; else $flag=0; }
		}
		
		
		if($data_array_mrr!="")
		{	
		//echo "insert into inv_mrr_wise_issue_details($field_array_mrr)values".$data_array_mrr;disconnect($con); die;
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			if($flag==1) 
			{
				if($mrrWiseIssueID) $flag=1; else $flag=0; 
			} 
		}
		
		if(count($updateID_array)>0)
		{  
			//echo bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array);disconnect($con); die;
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array),1);
			if($flag==1) { if($upTrID) $flag=1; else $flag=0; }
		}
		if(count($sid_arr)>0)
		{
		 $store_ID=execute_query(bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr ),1);
        if($flag==1) { if($store_ID) $flag=1; else $flag=0; } 
		}
		//echo "10**".bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr );disconnect($con); die;
		//echo "10**".$query2.'='.$rID.'='.$upTrID.'='.$upDtID.'='.$rID4.'='.$mrrWiseIssueID.'='.$store_ID;die;
		//mysql_query("ROLLBACK");
		//echo "6**".$flag;die;
 		//print_r($update_data);
		//echo bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array);
		//check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
			//echo $flag;
			if($flag==1)
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
		else if($db_type==2 || $db_type==1 )
		{
			
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**0**".$flag;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		disconnect($con);
		die;
	}		
}


if($action=="chemical_dyes_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$supplier_library=return_library_array( "SELECT id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name"  );
	$color_arr=return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country where status_active=1 and is_deleted=0", "id", "country_name"  );
	$group_arr = return_library_array("SELECT id, group_name from lib_group where status_active=1 and is_deleted=0", "id", "group_name");
	$sql="SELECT a.id, a.issue_number, a.issue_date, a.issue_basis, a.issue_purpose, a.loan_party, a.req_no, a.batch_id, a.batch_no, a.lap_dip_no as recepi_id, a.service_booking_no as recepi_no, a.order_id, a.buyer_id, a.style_ref, a.sub_order_id, a.buyer_job_no as subcon_job_no, a.fso_no as subcon_order_no, a.sub_order_ref, a.challan_no, a.remarks
	from inv_issue_master a where a.id='$data[1]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	//$recepi_id=$dataArray[0][csf('recepi_id')];
	
	$issue_batch_id=$dataArray[0][csf('batch_id')];
	if($issue_batch_id)
	{
		$batch_result=sql_select("SELECT id, batch_no, color_id, batch_weight from pro_batch_create_mst where id = $issue_batch_id and status_active=1 and is_deleted=0");
		$batch_color_id=$batch_result[0][csf('color_id')];
		$batch_weight=$batch_result[0][csf('batch_weight')];
	}
	//$order_id=$dataArray[0][csf('order_id')];
	$order_ids = return_field_value("b.po_ids", "pro_batch_create_mst a, pro_recipe_entry_mst b", "b.batch_id=a.id and a.id=$issue_batch_id and status_active=1 and is_deleted=0", "po_ids");
	$order_id=array_unique(explode(",",$order_ids));
	

	/*if($order_id=="") $order_id=0; else $order_id=$order_id;
	$po_sqls=sql_select("Select a.id as po_id, a.po_number, a.job_no_mst from wo_po_break_down a where a.status_active=1 and a.is_deleted=0 and a.id in(".$order_id.")");
	$po_number='';$job_no_mst='';
	foreach($po_sqls as $row_id)
	{
		$job_no_mst=$row_id[csf('job_no_mst')];
		if($po_number=='') $po_number=$row_id[csf('po_number')];else $po_number.=",".$row_id[csf('po_number')];
	}*/

	$order_arr = array();
	$embl_sql ="SELECT a.subcon_job, a.within_group, b.id,a.aop_reference, b.order_no, b.buyer_buyer, b.buyer_po_no, b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row) 
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['aop_reference']=$row[csf("aop_reference")];
		$order_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$order_arr[$row[csf("id")]]['buyer_buyer'] =$row[csf("buyer_buyer")];
		$order_arr[$row[csf("id")]]['buyer_po_no'] =$row[csf("buyer_po_no")];
		$order_arr[$row[csf("id")]]['buyer_style_ref'] =$row[csf("buyer_style_ref")];
	}
	unset($embl_sql_res);
	$buyer_po_arr=array();
	$po_sql ="SELECT b.id,a.job_no,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);

	foreach($order_id as $val)
	{
		if($job_no=="") $job_no=$order_arr[$val]['job']; else $job_no.=", ".$order_arr[$val]['job'];
		if($po_no=="") $po_no=$order_arr[$val]['po']; else $po_no.=", ".$order_arr[$val]['po'];
		if($buyer_po_no=="") $buyer_po_no=$order_arr[$val]['buyer_po_no']; else $buyer_po_no.=", ".$order_arr[$val]['buyer_po_no'];
		if($buyer_style_ref=="") $buyer_style_ref=$order_arr[$val]['buyer_style_ref']; else $buyer_style_ref.=", ".$order_arr[$val]['buyer_style_ref'];
		if($aop_ref=="") $aop_ref=$order_arr[$val]['ref']; else $aop_ref.=", ".$order_arr[$val]['ref'];

		if($dataArray[0][csf('within_group')]==1) 
		{
			if($buyer_buyer=="") $buyer_buyer=$buyer_library[$order_arr[$val]['buyer_buyer']]; else $buyer_buyer.=", ".$buyer_library[$order_arr[$val]['buyer_buyer']];
		}
		else
		{
			if($buyer_buyer=="") $buyer_buyer=$order_arr[$val]['buyer_buyer']; else $buyer_buyer.=", ".$order_arr[$val]['buyer_buyer'];
		}
	}

	$buyer_po_id=array_unique(explode(",",$dataArray[0][csf('buyer_po_ids')]));
	foreach($buyer_po_id as $val)
	{
		if($internalRef=="") $internalRef=$buyer_po_arr[$val]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$val]['internalRef'];
		if($buyer_job=="") $buyer_job=$buyer_po_arr[$val]['job']; else $buyer_job.=", ".$buyer_po_arr[$val]['job'];
	}
	//echo "<pre>";
	//print_r($all_ref_arr);
	$job_no = implode(",", array_unique(explode(", ",$job_no)));
	$po_no = implode(",", array_unique(explode(", ",$po_no)));
	$buyer_po_no = $data[4];//implode(",", array_unique(explode(", ",$buyer_po_no)));
	$buyer_style_ref = $data[5];//implode(",", array_unique(explode(", ",$buyer_style_ref)));
	$buyer_buyer = implode(",", array_unique(explode(", ",$buyer_buyer)));
	$aop_ref = $data[6];//implode(",", array_unique(explode(", ",$aop_ref)));
	$internalRef = implode(",", array_unique(explode(", ",$internalRef)));
	$buyer_job = implode(",", array_unique(explode(", ",$buyer_job)));
	$nameArray_com=sql_select( "SELECT company_name, plot_no, group_id, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
	
	?>
	<div style="width:1200px;">
    <table width="1100" cellspacing="0" align="">
        <tr>
        	<!--<td rowspan="3" width="70">
            	<img src="../../../<?// echo $image_location; ?>" height="60" width="180">
            </td>-->
            <td colspan="9" align="center" style="font-size:20px">
            	<strong><? echo $nameArray_com[0][csf("company_name")]; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
				foreach ($nameArray_com as $result)
				{ 
					if($result[csf('plot_no')]) echo "Plot No: ".$result[csf('plot_no')];
					if($result[csf('level_no')]) echo " Level No: ".$result[csf('level_no')];
					if($result[csf('road_no')]) echo " Road No: ". $result[csf('road_no')];
					if($result[csf('block_no')]) echo " Block No: ". $result[csf('block_no')];
					if($result[csf('city')]) echo " City No: ". $result[csf('city')];
					if($result[csf('zip_code')]) echo " Zip Code: ". $result[csf('zip_code')];
					echo "<br>";
					if($result[csf('province')]) echo " Province No: ". $result[csf('province')];
					if($result[csf('country_id')]) echo "Country: ". $country_arr[$result[csf('country_id')]];
					if($result[csf('email')]) echo " Email Address: ". $result[csf('email')];
					if($result[csf('website')]) echo " Website No: ". $result[csf('website')];
				}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><strong><u><? echo ' Dyes & Chemical Issue Note';?></u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="125"><strong>Issue Date:</strong></td><td width="150px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="140"><strong>Issue Basis :</strong></td> <td width="250px"><? echo $receive_basis_arr[$dataArray[0][csf('issue_basis')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Req. No:</strong></td> <td><? echo $dataArray[0][csf('req_no')]; ?></td>
            <td><strong>Batch No :</strong></td><td><? echo $dataArray[0][csf('batch_no')]; ?></td>
			<td><strong>Issue Purpose :</strong></td> <td><? echo $general_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Recipe No :</strong></td><td><? echo $dataArray[0][csf('recepi_no')]; ?></td>
            <td><strong>Batch Weight:</strong></td><td><? echo $batch_weight; ?></td>
            <td><strong>Batch Color:</strong></td><td><? echo $color_arr[$batch_color_id]; ?></td>
        </tr>
         <tr>
            <td><strong>AOP Job No :</strong></td><td><? echo $dataArray[0][csf('subcon_job_no')]; ?>&nbsp;</td>
            <td><strong>Work Order :</strong></td><td><? echo $dataArray[0][csf('subcon_order_no')]; ?></td>
            <td><strong>AOP Ref. :</strong></td><td><? echo $aop_ref; ?></td>
        </tr>
        <tr>
            <td><strong>Buyer Job No:</strong></td> <td><? echo $buyer_job ; ?></td>
            <td><strong>Buyer Order. :</strong></td><td><? echo $buyer_po_no; ?></td>
            <td><strong>Style Ref.:</strong></td><td><? echo $buyer_style_ref; ?></td>
        </tr>
        
        <tr>
            <td><strong>Loan Party:</strong></td> <td><? echo $supplier_library[$dataArray[0][csf('loan_party')]]; ?></td>
            <td><strong>Remarks :</strong></td><td><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table> 
        <br>
    <table align="" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="130" >Item Cat.</th>
            <th width="100" >Item Group</th>
            <th width="80" >Item Lot</th>
            <th width="80" >Sub Group</th>
            <th width="180" >Item Description</th>
            <th width="50" >UOM</th>
            <th width="80" >Ratio in %</th>
            <th width="80" >Req Qnty</th>
            <th width="80" >Adj%</th>
            <th width="90" >Adj Type</th> 
            <th>Issue Qnty</th>
        </thead>
        <tbody> 
   
		<?
        /*$sql_dtls = "select b.id, a.issue_number, b.store_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id,  b.section_id, b.cons_rate, b.cons_amount, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit
        from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
        where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id  and b.transaction_type=2 and a.entry_form=308 and b.item_category in (5,6,7,23) and d.mst_id=$data[1]  
        order by d.sub_process ";*/
		
		$sql_dtls = "SELECT a.sub_process, b.item_category_id, b.item_description, b.item_group_id, b.sub_group_name, b.unit_of_measure, a.dose_base, a.ratio, a.recipe_qnty, a.adjust_percent, a.adjust_type, a.required_qnty, a.req_qny_edit, c.batch_lot
        from inv_transaction c, dyes_chem_issue_dtls a, product_details_master b
        where c.id=a.trans_id and a.product_id=b.id and a.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
        order by a.sub_process "; 
        //echo $sql_dtls;die;
        $sql_result= sql_select($sql_dtls);
		$dtls_data=array();
		foreach($sql_result as $row)
		{
			$dtls_data[$row[csf("sub_process")]]+=$row[csf("req_qny_edit")];
		}
        $i=1;
        //if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==13) $colspan=9; else $colspan=10;
       	$p=1;$q=1;     
        foreach($sql_result as $row)
        {
			if($sub_process_check[$row[csf("sub_process")]]=="")
			{
				$sub_process_check[$row[csf("sub_process")]]=$row[csf("sub_process")];
				?>
                <tr bgcolor="#FFFFCC"><td colspan="12" style="font-size:14px; font-weight:bold;"><? echo $p.". ".$color_arr[$row[csf("sub_process")]]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Paste Weight:-".number_format($dtls_data[$row[csf("sub_process")]],2); ?></td></tr>
                <?
			}
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><p><? echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
                <td title="<? echo $row[csf("item_group_id")]; ?>"><p><? echo $group_arr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $row[csf("batch_lot")]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
                <td align="right"><? echo number_format($row[csf("ratio")],4); ?></td>
                <td align="right"><? echo number_format($row[csf("recipe_qnty")],4); ?></td>
                <td align="right"><p><? echo $row[csf("adjust_percent")]; ?>&nbsp;</p></td>
                <td><p><? echo $increase_decrease[$row[csf("adjust_type")]]; ?>&nbsp;</p></td>
                <td align="right"><? echo number_format($row[csf("req_qny_edit")],4); ?></td>
			</tr>
			<?
			$recipe_qnty_sum += $row[csf('recipe_qnty')];
			$req_qny_edit_sum += $row[csf('req_qny_edit')]; 
			$i++; 
		} 
		?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" align="right"><strong>Total :</strong></td>
                <td align="right"><? echo number_format($recipe_qnty_sum,4); ?></td>
                <td align="right" colspan="3"><? echo number_format($req_qny_edit_sum,4); ?></td>
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            echo signature_table(168, $data[0], "1100px");
         ?>
   	</div> 
	<?
    exit(); 
}


function fnc_store_wise_qty_operation($company_id,$store_id,$category,$prod_id,$trans_type)
{
	
	$trans_type=str_replace("'","",$trans_type);
	$prod_id=str_replace("'","",$prod_id);
	$store_id=str_replace("'","",$store_id);
	$category=str_replace("'","",$category);
	$company_id=str_replace("'","",$company_id);
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
		$sql_data=sql_select("SELECT id, company_id, category_id, store_id, prod_id, cons_qty, rate, amount
	from  inv_store_wise_qty_dtls where  company_id=$company_id  and status_active=1 and is_deleted=0 $prod_cond $cat_cond");
	}
	else if($trans_type==1 || $trans_type==4) //Recv && Issue Return;
	{
		$sql_data=sql_select("SELECT id, company_id, category_id, store_id, prod_id, cons_qty, rate, amount
		from  inv_store_wise_qty_dtls where  company_id=$company_id and store_id=$store_id and category_id in($category)  and status_active=1 and is_deleted=0 and prod_id=$prod_id");
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
	else if($trans_type==1 || $trans_type==4) //recv && Issue Return
	{
		if(count($sql_data)==0)//value Empty
		{
			foreach($sql_data as $row)
			{
				$stock_prod_arr[$row[csf('company_id')]][$row[csf('prod_id')]][$store_id][$category]=$row[csf('id')];
			}
		}
		else
		{
			foreach($sql_data as $row)
			{
				$stock_prod_arr[$row[csf('company_id')]][$row[csf('prod_id')]][$row[csf('store_id')]][$row[csf('category_id')]]=$row[csf('id')];
			}
		}
	}
	return $stock_prod_arr;	
} //Function End

//Store Wise Stock Function
function fnc_store_wise_stock($company_id,$store_id,$category,$prod_id)
{
	$result=sql_select("SELECT category_id, prod_id, cons_qty, lot 
	from  inv_store_wise_qty_dtls where company_id=$company_id and store_id=$store_id and status_active=1 and is_deleted=0");
	$stock_qty_arr=array();
	foreach($result as $row)
	{
		 $stock_qty_arr[$company_id][$store_id][$row[csf('category_id')]][$row[csf('prod_id')]][$row[csf('lot')]]['stock']=$row[csf('cons_qty')]; 
	}
	return $stock_qty_arr;
}
?>
