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
$userCredential = sql_select("SELECT store_location_id,unit_id as company_id,company_location_id,supplier_id,buyer_id FROM user_passwd where id=$user_id ");
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
	echo create_drop_down( "cbo_location_name", 170, "SELECT id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $company_location_credential_cond  order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/chemical_dyes_issue_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	//echo $data;die;
	$data_ref=explode("_",$data);
	echo create_drop_down( "cbo_flore_name", 170, "SELECT a.id, a.floor_name from lib_prod_floor a where a.status_active=1 and a.is_deleted=0 and a.company_id=$data_ref[0] and a.location_id=$data_ref[1] order by a.floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/chemical_dyes_issue_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+this.value, 'load_drop_down_machine', 'machine_td' );",0 );  	 
	exit();
}

if ($action=="load_drop_down_machine")
{
	//echo $data;die;
	$data_ref=explode("_",$data);
	echo create_drop_down( "cbo_machine_name", 170, "SELECT a.id, a.machine_no from lib_machine_name a where a.status_active=1 and a.is_deleted=0 and a.company_id=$data_ref[0] and a.location_id=$data_ref[1] and a.floor_id=$data_ref[2] order by a.machine_no","id,machine_no", 1, "-- Select --", $selected, "",0 );  	 
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
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","","" );
	exit();
}

if ($action=="load_drop_down_color")
{
	echo create_drop_down( "cbo_sub_process", 170, "SELECT max(id) as id, color_name from lib_color where status_active=1 and is_deleted=0 and (color_name is not null or color_name <> 0) and entry_form =220  group by color_name","id,color_name", 1, "-- Select--", "", "fnc_item_details(this.value,'','')",1);
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
		            	<th colspan="3" style="text-align:right">Search Type:</th>
		                <th><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 0, "",1 ); ?></th>
		              <th colspan="4"> </th>
		            </tr>
		            <tr>                	 
		                <th width="160">Company Name</th>
		                <th width="120">Requisition No</th>
		                <th width="120">Emb. Job</th>
		                <th width="120">Emb. Order</th>
		                <th width="120">Buyer PO</th>
		                <th width="120">Buyer Style</th>
		                <th width="170">Req. Date Range</th>
		                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
		            </tr>
		        </thead>
		        <tbody>
		            <tr class="general">
		                <td>
		                    <?  
							  echo create_drop_down( "cbo_company_name", 150, "SELECT id, company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1 );
		                    ?>
		                </td>
		                <td>				
		                    <input type="text" style="width:100px" class="text_boxes_numeric"  name="txt_requisition_no" id="txt_requisition_no" placeholder="Number Field" />	
		                </td> 
		                <td>				
		                    <input type="text" style="width:100px" class="text_boxes_numeric"  name="txt_emb_job" id="txt_emb_job" placeholder="Number Field" />	
		                </td>
		                <td>				
		                    <input type="text" style="width:100px" class="text_boxes"  name="txt_emb_order" id="txt_emb_order" placeholder="Character Field" />	
		                </td>
		                <td>				
		                    <input type="text" style="width:100px" class="text_boxes"  name="txt_buyer_po" id="txt_buyer_po" placeholder="Character Field" />	
		                </td>
		                <td>				
		                    <input type="text" style="width:100px" class="text_boxes"  name="txt_buyer_style" id="txt_buyer_style" placeholder="Character Field" />	
		                </td>
		                <td>
		                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" />&nbsp;To&nbsp;
		                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" />
		                </td> 
		                <td>
		                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_requisition_no').value+'_'+document.getElementById('txt_emb_job').value+'_'+document.getElementById('txt_emb_order').value+'_'+document.getElementById('txt_buyer_po').value+'_'+document.getElementById('txt_buyer_style').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_reqisition_search_list_view', 'search_div', 'chemical_dyes_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" />				
		                </td>
		            </tr>
		        	<tr>                  
		            	<td align="center" height="40" valign="middle" colspan="8">
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
	if($requisition_no=="" && $emb_job=="" && $emb_order=="" && $buyer_po=="" && $buyer_style=="" && $fromDate=="" && $toDate=="")
	{
		echo "Please Select Requisition Date Range.";die;
	}
	//echo $company."=".$requisition_no."=".$emb_job."=".$emb_order."=".$buyer_po."=".$buyer_style."=".$fromDate."=".$toDate."=".$string_search_type;die;
	$emb_order_id='';$buyer_order_id='';
	if($emb_job!="" || $emb_order!="")
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
		
		$emb_job_sql="SELECT b.id, a.job_no_prefix_num, b.job_no_mst, b.order_no,b.buyer_po_no, b.buyer_style_ref  from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form=204 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company $emb_job_cond";
		//echo $emb_job_sql;die;
		$emb_job_result=sql_select($emb_job_sql);
		$emb_data=array();
		foreach($emb_job_result as $row)
		{
			$emb_order_id.=$row[csf("id")].",";
			$emb_data[$row[csf("id")]]["id"]=$row[csf("id")];
			$emb_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$emb_data[$row[csf("id")]]["job_no_mst"]=$row[csf("job_no_mst")];
			$emb_data[$row[csf("id")]]["order_no"]=$row[csf("order_no")];
			$emb_data[$row[csf("id")]]["buyer_po_no"]=$row[csf("buyer_po_no")];
			$emb_data[$row[csf("id")]]["buyer_style_ref"]=$row[csf("buyer_style_ref")];
		}
		$emb_order_id=chop($emb_order_id,",");
	}
	else
	{
	 	$emb_job_sql="SELECT b.id, a.job_no_prefix_num, b.job_no_mst, b.order_no,b.buyer_po_no, b.buyer_style_ref  from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form=204 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company";
		//echo $emb_job_sql;die;
		$emb_job_result=sql_select($emb_job_sql);
		$emb_data=array();
		foreach($emb_job_result as $row)
		{
			$emb_data[$row[csf("id")]]["id"]=$row[csf("id")];
			$emb_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$emb_data[$row[csf("id")]]["job_no_mst"]=$row[csf("job_no_mst")];
			$emb_data[$row[csf("id")]]["order_no"]=$row[csf("order_no")];
			$emb_data[$row[csf("id")]]["buyer_po_no"]=$row[csf("buyer_po_no")];
			$emb_data[$row[csf("id")]]["buyer_style_ref"]=$row[csf("buyer_style_ref")];
		}
	}
	
	
	if($buyer_po!="" || $buyer_style!="")
	{
		$buyer_job_cond="";
		if($buyer_style!="")
		{
			if($string_search_type==1)
			{
				$buyer_job_cond = " and a.style_ref_no='$buyer_style'";
			}
			else if($string_search_type==4 || $ex_data[5]==0)
			{
				 $buyer_job_cond = " and a.style_ref_no LIKE '%$buyer_style%'";	
			}
			else if($string_search_type==2)
			{
				 $buyer_job_cond = " and a.style_ref_no LIKE '$buyer_style%'";	
			}
			else if($string_search_type==3)
			{
				 $buyer_job_cond = " and a.style_ref_no LIKE '%$buyer_style'";	
			}
		}
		
		if($buyer_po!="")
		{
			if($string_search_type==1)
			{
				$buyer_job_cond .= " and b.po_number='$buyer_po'";
			}
			else if($string_search_type==4 || $ex_data[5]==0)
			{
				 $buyer_job_cond .= " and b.po_number LIKE '%$buyer_po%'";	
			}
			else if($string_search_type==2)
			{
				 $buyer_job_cond .= " and b.po_number LIKE '$emb_order%'";	
			}
			else if($string_search_type==3)
			{
				 $buyer_job_cond .= " and b.po_number LIKE '%$buyer_po'";	
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

	
	$company_arr = return_library_array("SELECT id,company_name from lib_company where status_active =1 and is_deleted=0","id","company_name");
	
	//$arr=array(1=>$company_arr,3=>$receive_basis_arr,4=>$batch_arr,5=>$receipe_arr);

	if($db_type==0) $null_cond="''";
	else if($db_type==2) $null_cond="'0'";
	$sql ="SELECT a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.requisition_basis, a.recipe_id, a.id, a.order_id, a.order_ids, a.buyer_po_ids as buyer_po_id
	from dyes_chem_issue_requ_mst a where a.company_id=$company and a.is_apply_last_update!=2 and a.entry_form=221 and a.status_active=1 and a.is_deleted=0 and a.recipe_id!=$null_cond $req_cond";
	//echo $sql;
	$result_sql =sql_select($sql);
	foreach($result_sql as $row)
	{
		$all_recipe_id[$row[csf("recipe_id")]]=$row[csf("recipe_id")];
		$all_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
		$all_order_ids[$row[csf("order_ids")]]=$row[csf("order_ids")];
		if($row[csf("buyer_po_id")]) $all_buyer_po_id[$row[csf("buyer_po_id")]]=$row[csf("buyer_po_id")];
	}
	//echo $all_buyer_po_id;die;
	
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
	
	
	
	 
		
	/* $buyer_po_job_sql="SELECT b.id, a.job_no_prefix_num, b.job_no_mst, b.order_no,b.buyer_po_no, b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form=204 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and and b.id in(".implode(",",$all_order_ids).") ";
  		//echo $buyer_job_sql;die;
		$buyer_po_job_result=sql_select($buyer_po_job_sql);
		$buyer_po_order_data=array();
		foreach($buyer_po_job_result as $row)
		{
			////$buyer_order_data[$row[csf("id")]]["id"]=$row[csf("id")];
			//$buyer_order_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			//$buyer_order_data[$row[csf("id")]]["job_no"]=$row[csf("job_no")];
			$buyer_po_order_data[$row[csf("id")]]["buyer_style_ref"]=$row[csf("buyer_style_ref")];
			$buyer_po_order_data[$row[csf("id")]]["buyer_po_no"]=$row[csf("buyer_po_no")];
		}
		
		*/
		
		
		 
	
	$recipe_arr=sql_select("SELECT a.id, a.recipe_no, a.store_id,a.recipe_for from pro_recipe_entry_mst a where a.company_id=$company and a.entry_form=220 and a.status_active =1 and a.is_deleted=0 and a.id in(".implode(",",$all_recipe_id).") ");
	foreach($recipe_arr as $row)
	{
		$receipe_data_arr[$row[csf('id')]]['recipe_no']=$row[csf('recipe_no')];
		$receipe_data_arr[$row[csf('id')]]['store_id']=$row[csf('store_id')];
		$receipe_data_arr[$row[csf('id')]]['recipe_for']=$row[csf('recipe_for')];
	}
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="120">Req. No</th>
                <th width="70">Req. Date</th>
                <th width="120">Emb. Job</th>
                <th width="120">Emb. Order</th>
                <th width="120">Buyer Style</th>
                <th width="">Buyer Order</th>
            </thead>
    </table>
   <div style="width:820px; overflow-y:scroll; max-height:230px; cursor:pointer" id="buyer_list_view" align="center">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table" id="tbl_list_search" >
        <tbody>
			<? 
            $i=1;
            foreach($result_sql as $row)
            {
				if ($i%2==0)$bgcolor="#E9F3FF";						
				else $bgcolor="#FFFFFF";
				$recipi_no=$receipe_data_arr[$row[csf('recipe_id')]]['recipe_no'];
				$store_ids=$receipe_data_arr[$row[csf('recipe_id')]]['store_id'];
				$recipe_for=$receipe_data_arr[$row[csf('recipe_id')]]['recipe_for'];
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('requ_no')]."_".$row[csf('id')]."_".$row[csf('recipe_id')]."_".$recipi_no."_".$emb_data[$row[csf("order_ids")]]["job_no_mst"]."_".$emb_data[$row[csf("order_ids")]]["order_no"]."_".$row[csf("order_ids")]."_".$emb_data[$row[csf("order_ids")]]["buyer_style_ref"]."_".$emb_data[$row[csf("order_ids")]]["buyer_po_no"]."_".$row[csf("buyer_po_id")]."_".$store_ids."_".$recipe_for; ?>')">
                    <td width="30" align="center" title="<? echo $recipe_ids; ?>"><? echo $i; ?></td>
                    <td width="120" align="center"><? echo $row[csf("requ_no")]; ?></td>
                    <td width="70" align="center"><? echo change_date_format($row[csf("requisition_date")]); ?></td>
                    <td width="120" align="center" ><? echo $emb_data[$row[csf("order_ids")]]["job_no_mst"]; ?></td>
                    <td  width="120" align="center"><? echo $emb_data[$row[csf("order_ids")]]["order_no"]; ?></td> 
                    <td width="120" align="center"><? echo $emb_data[$row[csf("order_ids")]]["buyer_po_no"];//$buyer_order_data[$row[csf("buyer_po_id")]]["style_ref_no"]; ?></td>
                    <td align=""><? echo $emb_data[$row[csf("order_ids")]]["buyer_style_ref"];//$buyer_order_data[$row[csf("buyer_po_id")]]["po_number"]; ?></td>
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
	
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $company_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$variable_lot=$sql[0][csf("auto_transfer_rcv")];
	//echo $variable_lot;die;
	
	$item_group_arr=return_library_array( "SELECT id, item_name from lib_item_group where status_active =1 and is_deleted=0",'id','item_name');
	$store_arr=return_library_array( "SELECT a.id as id,a.store_name  as store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name", "id", "store_name"  );
	?>
	<div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="50">Product ID</th>
                <th width="80">Item Lot</th>
                <th width="100">Item Cat.</th>
                <th width="100">Group</th>
                <th width="100">Sub Group</th>
                <th width="140">Item Description</th>
                <th width="32">UOM</th>
                <th width="70">Dose Base</th>
                <th width="50">Ratio</th>
                <th width="70">Stock Qty</th>
                <th width="73">Recipe Qnty.</th>
                <th width="55">Adj%.</th>
                <th width="90">Adj. Type</th>
                <th width="">Issue Qnty.</th>
            </thead>
        </table>
        <div style="width:1200px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1180" class="rpt_table" id="tbl_list_search">
                <tbody>
                <?
                if($is_update=="")
                {
					if($issue_basis==4)
					{
						//$cbo_store
						/*$sql="SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock
						from product_details_master a, inv_transaction b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(5,6,7,22,23) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted =0 group by a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock having sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) >0
						order by a.item_category_id";
						*/
						
						$sql="SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.lot, b.cons_qty as store_stock
						from product_details_master a, inv_store_wise_qty_dtls b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(5,6,7,23) and b.cons_qty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						
						union all
						SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.batch_lot as lot, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock
						from product_details_master a, inv_transaction b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(22) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						group by a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.batch_lot ";
							
						//echo $sql;die;
						$i=1;
						$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $selectResult)
						{
	                        $issue_remain=$totalIssued-$totalIssuedReturn;
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							if($variable_lot) $item_lot=$selectResult[csf('lot')]; else $item_lot="";
							if($selectResult[csf('item_category_id')]==22)
							{
								$stock_qty=$selectResult[csf('store_stock')];
							}
							else
							{
								$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]][$item_lot]['stock'];
							}
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td> 
                                <td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                </td>
                                <td width="80" align="center" id="td_lot_<? echo $i; ?>"><? echo $selectResult[csf('lot')]; ?>
                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('lot')]; ?>">
                                </td>
                                <td width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1,"","","","","","","cbo_dose_base[]"); ?></td>
                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($ratio,5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
                                <td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($recipe_qnty,5,""); ?>" disabled></td>
                                <td width="55" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? //echo $ratio; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
                                <td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -","","calculate_receipe_qty($i)",1,"","","","","","","cbo_adj_type[]"); ?></td>
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
						$total_issue_sql=sql_select("SELECT a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit from inv_issue_master a, dyes_chem_issue_dtls b where a.id=b.mst_id and a.entry_form=250 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and a.issue_basis=7 and a.req_id='".$req_id."' and b.sub_process=$sub_process_id");
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
						
						$sql_store="SELECT a.id, b.store_id, b.batch_lot, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock
						from product_details_master a, inv_transaction b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(22) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						group by a.id, b.store_id, b.batch_lot";
						//echo $sql_store;
						$store_result=sql_select( $sql_store );
						$store_chemical_stock=array();
						foreach($store_result as $row)
						{
							$store_chemical_stock[$row[csf("id")]][$row[csf("store_id")]][$row[csf("batch_lot")]]+=$row[csf("store_stock")];
						}
						
						 $sql="SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty as required_qnty, b.req_qny_edit,b.item_lot 
						from product_details_master a, dyes_chem_issue_requ_dtls b where a.id=b.product_id and b.mst_id='".$req_id."' and b.multicolor_id=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23,22) and a.status_active=1 and a.is_deleted=0 order by a.item_category_id 
						";
						$nameArray=sql_select( $sql );

						$sql_req=sql_select("SELECT a.id, b.required_qnty as required_qnty, b.req_qny_edit from product_details_master a, dyes_chem_issue_requ_dtls b where a.id=b.product_id and b.mst_id='".$req_id."' and b.multicolor_id=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.item_category_id in(5,6,7,23,22) and a.status_active=1 and a.is_deleted=0 ");	
		
						$req_data=array();
						foreach($sql_req as $row)
						{
							$req_data[$row[csf("id")]]=$row[csf("req_qny_edit")];
						}
						
						//echo $sql;
						$is_disabled=1;
						$i=1;
						
						$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
						//echo $stock_qty_arr;die;
						//echo "<pre>";print_r($stock_qty_arr);die;
						
						foreach ($nameArray as $selectResult)
						{
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$req_qnty=$selectResult[csf('req_qny_edit')]-$total_issue_arr[$selectResult[csf("id")]];
							if($variable_lot) $item_lot=$selectResult[csf('item_lot')]; else $item_lot="";
							if($selectResult[csf('item_category_id')]==22)
							{
								$stock_qty=$store_chemical_stock[$selectResult[csf('id')]][$cbo_store][$item_lot];
								//$stock_qty=10;
							}
							else
							{
								$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]][$item_lot]['stock'];
							}
							
							if(def_number_format($req_qnty,5,"")>0)
							{
								$cu_req_qnty=(($req_data[$selectResult[csf('id')]]-$prev_issue[$selectResult[csf('id')]])*1);
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
                                    <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                    <td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                    <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                    </td>
                                    <td width="80" align="center" id="td_lot_<? echo $i; ?>"><? echo $selectResult[csf('item_lot')]; ?>
                                    <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_lot')]; ?>">
                                    </td>
                                    <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                    <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                    </td>
                                    <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                    <td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                    <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                    <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                    <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1,"","","","","","","cbo_dose_base[]");  ?></td>
                                    <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($selectResult[csf('ratio')],5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" readonly></td>
                                    <td width="70" title="<? echo $stock_qty;?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                    <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('required_qnty')],5,""); ?>"  onClick="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)" readonly></td>
                                    <td width="55" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" readonly></td>
                                    <td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)",1,"","","","","","","cbo_adj_type[]");?></td>
                                    <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                    <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($req_qnty,5,""); ?>" readonly>
                                    <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%" placeholder="<? echo def_number_format($cu_req_qnty,5,""); ?>"   value="<? echo def_number_format($req_qnty,5,""); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)"  / >
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
					/*$sql="select a.id,t.store_id,a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure,a.current_stock, b.id as dtls_id,b.trans_id, b.dose_base, b.ratio,recipe_qnty,adjust_percent,adjust_type,required_qnty,req_qny_edit 
					from product_details_master a, dyes_chem_issue_dtls b, inv_transaction t 
					where a.id=b.product_id and b.trans_id=t.id and b.mst_id=$is_update and b.sub_process=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23,22) and a.status_active=1 and a.is_deleted=0 order by a.item_category_id";*/
					//echo $sql;
					
					$sql_store="SELECT a.id, b.store_id, b.batch_lot, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock
					from product_details_master a, inv_transaction b where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(22) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.store_id, b.batch_lot";
					//echo $sql_store;
					$store_result=sql_select( $sql_store );
					$store_chemical_stock=array();
					foreach($store_result as $row)
					{
						$store_chemical_stock[$row[csf("id")]][$row[csf("store_id")]][$row[csf("batch_lot")]]+=$row[csf("store_stock")];
					}
					
					$sql="SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.trans_id, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty as required_qnty, b.req_qny_edit, c.batch_lot  
					from product_details_master a, dyes_chem_issue_dtls b, inv_transaction c where a.id=b.product_id and b.trans_id=c.id and b.mst_id='".$is_update."' and b.sub_process=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by a.item_category_id ";
					
					
				/*	$sql="SELECT a.id ,a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure,a.current_stock, b.id as dtls_id, b.trans_id, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.batch_lot 
					from product_details_master a, dyes_chem_issue_dtls b, inv_transaction c
					where a.id=b.product_id and b.trans_id=c.id and b.mst_id=$is_update and b.sub_process=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,22,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					order by a.item_category_id";*/
					//echo $sql;
					$i=1;
					$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
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
						//echo $company_id.'='.$selectResult[csf('store_id')].'='.$selectResult[csf('item_category_id')].'='.$selectResult[csf('id')];
						if($variable_lot) $item_lot=$selectResult[csf('batch_lot')]; else $item_lot="";
						if($selectResult[csf('item_category_id')]==22)
						{
							$stock_qty=$store_chemical_stock[$selectResult[csf('id')]][$cbo_store][$item_lot]+$selectResult[csf('req_qny_edit')];
							//$stock_qty=10;
						}
						else
						{
							$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]][$item_lot]['stock']+$selectResult[csf('req_qny_edit')];
						}
						
						if($issue_basis==4)
						{
							?>
							<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                </td>
                                <td width="80" align="center" id="td_lot_<? echo $i; ?>"><? echo $selectResult[csf('batch_lot')]; ?>
                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('batch_lot')]; ?>">
                                </td>
                                <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1,"","","","","","","cbo_dose_base[]"); ?></td>
                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($selectResult[csf('ratio')],5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
                                <td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                
                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('recipe_qnty')],5,""); ?>" disabled></td>
                                <td width="55" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
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
                                <td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                </td>
                                <td width="80" align="center" id="td_lot_<? echo $i; ?>"><? echo $selectResult[csf('batch_lot')]; ?>
                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('batch_lot')]; ?>">
                                </td>
                                <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1); ?></td>
                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($selectResult[csf('ratio')],5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" readonly></td>
                                <td width="70"  title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('recipe_qnty')],5,""); ?>" onClick="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)" readonly></td>
                                <td width="55" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" readonly></td>
                                <td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)",1); ?></td>
                                <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('required_qnty')],5,""); ?>" readonly>
                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>"  onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;?>)"   <? echo $desable_cond; ?> / >
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
			<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
		        <thead>
		            <tr>
		                <th width="150">Search By</th>
		                <th width="200" align="center" id="search_by_td_up">Enter Issue No</th>
		                
		                <th width="180">Issue Date Range</th>
		                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
		            </tr>
		        </thead>
		        <tbody>
		            <tr>
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
		                <!-- <td align="center">				
		                    <input type="text" style="width:130px" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />	
		                </td>  -->   
		                <td align="center">
		                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
		                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
		                </td> 
		                <td align="center">
		                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_mrr_search_list_view', 'search_div', 'chemical_dyes_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
		                </td>
		        	</tr>
		        	<tr>
		            	<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
		                    <!-- Hidden field here -->
		                     <input type="hidden" id="hidden_issue_number" value="" />
		                    <!-- END -->
		                </td>
		            </tr>    
		        </tbody>
		    </table>    
		    <div align="center" valign="top" id="search_div"></div>
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
	/*print_r($ex_data);die;*/
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	/*$batch_no = str_replace("'","",$ex_data[5]);*/
	$yearid = str_replace("'","",$ex_data[5]);
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
	$batch_cond='';
	$flag=0;
	if($batch_no!='')
	{
		if(count($batch_ids)>0)
		{	
			$batchId=""; $a=''; $for_cond=0;
			for($i=0; $i<count($batch_ids); $i++)
			{
				$batchId=explode(",",$batch_ids[$i]);
				foreach($batchId as $id)
				{
			        if($for_cond==0)
			        {
			            $a="and ";
			        }
			        else
			        {
			            $a="";
			        }
					$batch_cond.="$a find_in_set($id,batch_no)>0 or ";
			        $for_cond++;
				}
			}
			$batch_cond=chop($batch_cond,"or ");
		}
		else
		{
			$batch_cond=" and a.id<1";
		}
	}
	else
	{
		$batch_cond="";
	}
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.issue_number LIKE '%$txt_search_common%'";	
			
		}
		else if(trim($txt_search_by)==2) // for challan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";				
 		}
 		else if(trim($txt_search_by)==3) // for Requisition no
		{
			$sql_cond .= " and b.requ_prefix_num =$txt_search_common";
			$flag=1;				
 		}		 
 	} 
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
	
	$batch_arr = return_library_array("SELECT id,batch_no from pro_batch_create_mst where batch_against<>0 and status_active =1 and is_deleted=0","id","batch_no");
	$company_arr = return_library_array("SELECT id,company_name from lib_company where status_active =1 and is_deleted=0","id","company_name");
	$arr=array(1=>$company_arr,3=>$receive_basis_arr,4=>$batch_arr,5=>$receipe_arr);
		if($db_type==0) $year_cond=" and year(a.insert_date)=$yearid"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$yearid'";

	if($flag==1)
	{
		$sql = sql_select("SELECT a.issue_number_prefix_num,a.req_no,b.requ_no_prefix,b.requ_prefix_num, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.batch_no, a.issue_date, a.id, $year_id, a.is_posted_account from inv_issue_master a, dyes_chem_issue_requ_mst b where a.req_no=b.requ_no and a.company_id=$company and a.entry_form=250 $sql_cond  $batch_cond $year_cond and a.status_active =1 and a.is_deleted=0 order by a.id ");
		

	}
	else
	{
		
		$sql = sql_select("SELECT a.issue_number_prefix_num,a.req_no, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.batch_no, a.issue_date, a.id, $year_id, a.is_posted_account from inv_issue_master a where a.company_id=$company and a.entry_form=250 $sql_cond  $batch_cond $year_cond and a.status_active =1 and a.is_deleted=0 order by a.id ");

	}
	
	?>
	<br/>
    <table align="center" cellspacing="0" width="820"  border="1" rules="all" class="rpt_table"  >
        <thead bgcolor="#dddddd" align="center">

            <th width="30">SL</th>
            <th width="60" >Issue No</th>
            <th width="60" >Year</th>
            <th width="150" >Company</th>
            <th width="150" >Requisition Basis</th>
            <th width="100" >Issue Purpose</th>
            <th width="150" >Batch No</th>
            <th width="120" >Issue Date</th> 
        </thead>
    </table>
 <div style="width:850px;max-height:300px; padding-left:18px; overflow-y:scroll" id="scroll_body">
 	<table align="center" cellspacing="0" width="820"  border="1" rules="all" class="rpt_table"  id="list_view" >
        <tbody id="list_view"> 
        <?
		$i=1;
	    foreach($sql as $inf)
		{
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			 $batch_id_all=explode(",",$inf[csf("batch_no")]);
			 $batch_no_all="";	
			 foreach($batch_id_all as $b_id)
			 {
				if($batch_no_all!=0)  $batch_no_all.=",".$batch_arr[$b_id]; 
				else  $batch_no_all=$batch_arr[$b_id]; 
			 }
		?>
         	<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $inf[csf("issue_number")]."_".$inf[csf("id")]."_".$inf[csf("issue_basis")]."_".$inf[csf("issue_purpose")]."_".$inf[csf("is_posted_account")];?>")' style="cursor:pointer">
                <td width="30" ><?php echo $i; ?></td>
                <td width="60" align="right"><?php echo  $inf[csf("issue_number_prefix_num")]; ?></td>
                <td width="60"align="center"><?php echo $inf[csf("year")]; ?></td>
                <td width="150" align="center"><?php echo $company_arr[$inf[csf("company_id")]]; ?></td>
                <td width="150" align="center"><?php echo $receive_basis_arr[$inf[csf("issue_basis")]]; ?></td>
                <td width="100" align="center"><?php echo $yarn_issue_purpose[$inf[csf("issue_purpose")]]; ?></td>
                <td width="150" align="center"><?php echo $batch_no_all; ?></td>
                <td width="" align="center"><?php echo change_date_format($inf[csf("issue_date")]); ?></td>
               
           </tr>
        <?
		$i++;	
		}
		?>
	
		</tbody>
    </table>
</div>	

	
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}


if($action=="populate_data_from_data")
{
	$sql = sql_select("SELECT location_id, issue_date, issue_basis, req_no, req_id, issue_purpose, company_id, loan_party, lap_dip_no, batch_no, order_id, sub_order_id, style_ref, store_id, buyer_job_no, is_posted_account, lc_company, floor_id, machine_id, remarks from inv_issue_master where id=$data and entry_form=250 and status_active =1 and is_deleted=0");
	$all_order_id="";$all_subcon_order="";
	foreach($sql as $row)
	{
		$all_order_id.=$row[csf("order_id")].",";
		$all_subcon_order.=$row[csf("sub_order_id")].",";
		$all_recipe_id.=$row[csf("lap_dip_no")].",";
	}
	$all_order_id=implode(",",array_unique(explode(",",chop($all_order_id,","))));
	$all_subcon_order=implode(",",array_unique(explode(",",chop($all_subcon_order,","))));
	$all_recipe_id=implode(",",array_unique(explode(",",chop($all_recipe_id,","))));
	
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
		$subcon_po_sqls=sql_select("SELECT a.id as po_id, a.job_no_mst, a.order_no from subcon_ord_dtls a where a.status_active=1 and a.is_deleted=0 and a.id in($all_subcon_order)");
		foreach($subcon_po_sqls as $row)
		{
			$subcon_order_data[$row[csf('po_id')]]['job_no_mst']=$row[csf('job_no_mst')];
			$subcon_order_data[$row[csf('po_id')]]['order_no']=$row[csf('order_no')];
		}
	}
	
	$recipe_arr=sql_select("SELECT a.id, a.recipe_no, a.store_id,a.recipe_for from pro_recipe_entry_mst a where a.entry_form=220 and a.status_active =1 and a.is_deleted=0 and a.id in(".implode(",",$all_recipe_id).") ");
	foreach($recipe_arr as $row)
	{
		//$receipe_data_arr[$row[csf('id')]]['recipe_no']=$row[csf('recipe_no')];
		//$receipe_data_arr[$row[csf('id')]]['store_id']=$row[csf('store_id')];
		$receipe_data_arr[$row[csf('id')]]['recipe_for']=$row[csf('recipe_for')];
	}
	
	
	foreach($sql as $row)
	{
		if($receipe_data_arr[$row[csf("lap_dip_no")]]['recipe_for']=='' || $receipe_data_arr[$row[csf("lap_dip_no")]]['recipe_for']==0) $recipe_for=0;else $recipe_for=$receipe_data_arr[$row[csf("lap_dip_no")]]['recipe_for'];

		if($row[csf("store_id")]=='' || $row[csf("store_id")]==0) $row[csf("store_id")]=0;else $row[csf("store_id")]=$row[csf("store_id")];
		echo "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n"; 
		echo  "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("company_id")]."', 'load_drop_down_loan_party', 'loan_party_td');\n";
		echo  "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("company_id")]."', 'load_drop_down_store', 'store_td');\n";
		if($row[csf("location_id")]>0)
		{
			echo  "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("company_id")]."_".$row[csf("location_id")]."', 'load_drop_down_floor', 'floor_td' );\n";
		}
		if($row[csf("location_id")]>0 && $row[csf("floor_id")]>0)
		{
			echo  "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("company_id")]."_".$row[csf("location_id")]."_".$row[csf("floor_id")]."', 'load_drop_down_machine', 'machine_td' );\n";
		}
		
		
		echo "document.getElementById('txt_issue_date').value = '".change_date_format($row[csf("issue_date")],"dd-mm-yyyy","-")."';\n"; 
		echo "document.getElementById('cbo_issue_basis').value = '".$row[csf("issue_basis")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n"; 
		echo "document.getElementById('cbo_issue_purpose').value = '".$row[csf("issue_purpose")]."';\n";
		echo "document.getElementById('cbo_loan_party').value = '".$row[csf("loan_party")]."';\n"; 
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_id")]."';\n";
		  
		echo "document.getElementById('txt_req_no').value = '".$row[csf("req_no")]."';\n"; 
		echo "document.getElementById('txt_req_id').value = '".$row[csf("req_id")]."';\n"; 
		echo "document.getElementById('txt_recipe_no').value = '".$row[csf("batch_no")]."';\n"; 
		echo "document.getElementById('txt_recipe_id').value = '".$row[csf("lap_dip_no")]."';\n";
		
		echo "document.getElementById('txt_job_no').value = '".$subcon_order_data[$row[csf("sub_order_id")]]['job_no_mst']."';\n"; 
		
		echo "document.getElementById('txt_order_no').value = '".$row[csf("buyer_job_no")]."';\n"; 
		echo "document.getElementById('hidden_order_id').value = '".$row[csf("sub_order_id")]."';\n"; 
		echo "document.getElementById('cbo_store_name').value = '".$row[csf("store_id")]."';\n";
		
		echo "document.getElementById('cbo_flore_name').value = '".$row[csf("floor_id")]."';\n"; 
		echo "document.getElementById('cbo_machine_name').value = '".$row[csf("machine_id")]."';\n"; 
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		 
        echo "document.getElementById('txt_buyer_po').value = '".$po_data_arr[$row[csf("order_id")]]['po_number']."';\n";
		echo "document.getElementById('hidden_buyer_po_id').value = '".$row[csf("order_id")]."';\n";		  
		echo "document.getElementById('txt_buyer_style').value = '".$row[csf("style_ref")]."';\n"; 
		echo "document.getElementById('cbo_recipe_for').value = '".$recipe_for."';\n"; 
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
	$issue_store_id=str_replace("'","",$cbo_store_name);
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $cbo_company_name and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$variable_lot=$sql[0][csf("auto_transfer_rcv")];
	
	//--------------------------for check issue date with all product id's last receive date
	for ($i=1;$i<$total_row;$i++)
	{
		$txt_prod_id="txt_prod_id_".$i;
		$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
		//$pord_ids_arr[str_replace("'",'',$$txt_prod_id)]=str_replace("'",'',$$txt_prod_id);
		if(str_replace("'",'',$$txt_prod_id)!="")
		{
			$pord_ids_arr[str_replace("'",'',$$txt_prod_id)]=str_replace("'",'',$$txt_prod_id);
		}
		if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
		{
			$prod_ids.=str_replace("'",'',$$txt_prod_id).",";
		}
		
		$transId="transId_".$i;
		if(str_replace("'","",$$transId)!="")
		{
			$upTransIdArr[str_replace("'","",$$transId)]=str_replace("'","",$$transId);
		}
	}        
	$prod_ids =  chop($prod_ids,",");

	
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
			//$all_prod_id.=str_replace("'",'',$$txt_prod_id).",";
		}
		$all_dtls_id=chop($all_dtls_id,",");
		$all_prod_id=chop($all_prod_id,",");
		$dtls_cond="";
		if($all_dtls_id!="") $dtls_cond=" and b.id not in($all_dtls_id)";
		$total_issue_sql=sql_select("SELECT a.issue_number, b.id as dtls_id, b.product_id, b.req_qny_edit as req_qny_edit, c.batch_lot 
		from inv_issue_master a, dyes_chem_issue_dtls b, inv_transaction c 
		where a.id=b.mst_id and b.trans_id=c.id and a.entry_form=250 and a.issue_basis=7 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and a.req_id='".str_replace("'","",$txt_req_id)."' and b.sub_process='".str_replace("'","",$cbo_sub_process)."' $dtls_cond");
		$prev_issue=array();
		foreach($total_issue_sql as $row)
		{
			if($variable_lot==1) $row[csf("batch_lot")]=$row[csf("batch_lot")]; else $row[csf("batch_lot")]="";
			if($dtls_id_arr[$row[csf("dtls_id")]]=="")
			{
				$dtls_id_arr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
				$prev_issue[$row[csf("product_id")]][$row[csf("batch_lot")]]=$row[csf("req_qny_edit")];
			}
		}
		
		unset($total_issue_sql);
		$prod_cond="";
		if($all_prod_id!="") $prod_cond=" and a.id in($all_prod_id)";
		$sql_req=sql_select("SELECT a.id, b.item_lot, b.required_qnty as required_qnty, b.req_qny_edit from product_details_master a, dyes_chem_issue_requ_dtls b where a.id=b.product_id and b.mst_id='".str_replace("'","",$txt_req_id)."' and b.multicolor_id='".str_replace("'","",$cbo_sub_process)."' and b.status_active=1 and b.is_deleted=0 and a.company_id='".str_replace("'","",$cbo_company_name)."' and a.item_category_id in(5,6,7,23,22) and a.status_active=1 and a.is_deleted=0 $prod_cond");
		$req_data=array();
		foreach($sql_req as $row)
		{
			if($variable_lot==1) $row[csf("item_lot")]=$row[csf("item_lot")]; else $row[csf("item_lot")]="";
			$req_data[$row[csf("id")]][$row[csf("item_lot")]]=$row[csf("req_qny_edit")];
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
			$txt_lot="txt_lot_".$i;
			$item_lot=str_replace("'",'',$$txt_lot);
			if($variable_lot==1) $lot_no=$item_lot; else $lot_no="";
			$cu_req_qnty=(($req_data[str_replace("'","",$$txt_prod_id)][$lot_no]-$prev_issue[str_replace("'","",$$txt_prod_id)][$lot_no])*1);
			$current_issue=str_replace("'","",$$txt_reqn_qnty_edit)*1;
			$cu_req_qnty=str_replace("'","",$cu_req_qnty)*1;

			if($current_issue>$cu_req_qnty)
			{
				//echo "20**".var_dump($cu_req_qnty).'=='.var_dump($current_issue); die;
				//echo "20**$current_issue==$cu_req_qnty";die;
				echo "20**Issue Quantity Not Allow Over Requisition Quantity.";die;
			}
		}
	}
	//-----------------------------------------------------------------------------
	$trans_sql=sql_select("select ID, PROD_ID, STORE_ID, BATCH_LOT, TRANSACTION_TYPE, ITEM_CATEGORY, CONS_QUANTITY, CONS_AMOUNT, BALANCE_QNTY, BALANCE_AMOUNT, CONS_RATE
 	from inv_transaction where status_active=1 and is_deleted=0 and prod_id in(".implode(",",$pord_ids_arr).") and store_id = $issue_store_id order by PROD_ID, ID");
	$trans_data_arr=array();$chemDataArr=array(); $dyesDataArr=array(); $auxChemDataArr=array();
	foreach($trans_sql as $val)
	{
		if($variable_lot==1) $lot_no=$val["BATCH_LOT"]; else $lot_no="";
		
		if($val["TRANSACTION_TYPE"]==1 || $val["TRANSACTION_TYPE"]==4 || $val["TRANSACTION_TYPE"]==5)
		{
			$trans_data_arr[$val["PROD_ID"]][$lot_no]["STOCK"] +=$val["CONS_QUANTITY"];
		}
		else
		{
			$trans_data_arr[$val["PROD_ID"]][$lot_no]["STOCK"] -=$val["CONS_QUANTITY"];
		}
	}
	
	$product_arr=array();
	$dataArray = sql_select("SELECT id, avg_rate_per_unit, current_stock, stock_value from product_details_master where item_category_id in (5,6,7,23,22) and status_active =1 and is_deleted=0 and id in(".implode(",",$pord_ids_arr).")");
	foreach($dataArray as $row)
	{
		$product_arr[$row[csf("id")]]['qty']=$row[csf("current_stock")];
		$product_arr[$row[csf("id")]]['rate']=$row[csf("avg_rate_per_unit")];
		$product_arr[$row[csf("id")]]['val']=$row[csf("stock_value")];
	}
	
	
	$sql_store = sql_select("SELECT id, prod_id, store_id, category_id as cat_id, rate as avg_rate, cons_qty as current_stock, amount as stock_value, last_purchased_qnty as last_qty, lot 
	from inv_store_wise_qty_dtls where company_id=$cbo_company_name and store_id=$issue_store_id and status_active =1 and is_deleted=0 and prod_id in(".implode(",",$pord_ids_arr).")");
	$store_arr=array();$store_idarr=array();
	foreach($sql_store as $row)
	{
		if($variable_lot==1) $lot_no=$row[csf("lot")]; else $lot_no="";
		$store_arr[$row[csf("id")]]['qty']=$row[csf("current_stock")];
		$store_arr[$row[csf("id")]]['avg_rate']=$row[csf("avg_rate")];
		$store_idarr[$row[csf("prod_id")]][$row[csf("store_id")]][$lot_no]['id']=$row[csf("id")];
		$store_wise_stock_qnty_arr[$row[csf("prod_id")]][$lot_no] = $row[csf("current_stock")];
	}
	
	//echo "10**".$flag;disconnect($con); die;
	$lifoFifoArr=return_library_array("SELECT item_category_id, store_method from variable_settings_inventory where company_name=$cbo_company_name and variable_list=17 and item_category_id in (5,6,7,23,22) and status_active=1 and is_deleted=0","item_category_id","store_method");
			
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$dys_che_issue_num=''; $dys_che_update_id=''; $product_id=0;
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==2 || $db_type==1) { $year_id=" extract(year from insert_date)="; }
			if($db_type==0)  { $year_id="YEAR(insert_date)="; }
			
			$new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_name,'PDCI',250,date("Y",time()) ));
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			
			
			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number,issue_basis, issue_purpose, entry_form, company_id, location_id, store_id, buyer_job_no, style_ref, req_no, req_id, issue_date, loan_party, lap_dip_no, batch_no, order_id, sub_order_id, inserted_by, insert_date, lc_company,floor_id,machine_id,remarks";
			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_basis.",".$cbo_issue_purpose.",250,".$cbo_company_name.",".$cbo_location_name.",".$cbo_store_name.",".$txt_order_no.",".$txt_buyer_style.",".$txt_req_no.",".$txt_req_id.",".$txt_issue_date.",".$cbo_loan_party.",".$txt_recipe_id.",".$txt_recipe_no.",".$hidden_buyer_po_id.",".$hidden_order_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_company_name.",".$cbo_flore_name.",".$cbo_machine_name.",".$txt_remarks.")";
			//$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			$dys_che_issue_num=$new_system_id[0];
			$dys_che_update_id=$id;
		}
		else
		{
			$field_array_update="issue_purpose*location_id*buyer_job_no*style_ref*req_no*req_id*issue_date*loan_party*lap_dip_no*batch_no*order_id*sub_order_id*updated_by*update_date*lc_company*floor_id*machine_id*remarks";
			$data_array_update="".$cbo_issue_purpose."*".$cbo_location_name."*".$txt_order_no."*".$txt_buyer_style."*".$txt_req_no."*".$txt_req_id."*".$txt_issue_date."*".$cbo_loan_party."*".$txt_recipe_id."*".$txt_recipe_no."*".$hidden_buyer_po_id."*".$hidden_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_name."*".$cbo_flore_name."*".$cbo_machine_name."*".$txt_remarks."";
			
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
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$prod_id=str_replace("'",'',$$txt_prod_id);
				$item_lot=str_replace("'",'',$$txt_lot);
				$cat_id=str_replace("'",'',$$txt_item_cat);
				
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$store_stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
				$txt_reqn_qnty_edit=str_replace("'",'',$$txt_reqn_qnty_edit);
				if($variable_lot==1) $lot_no=$item_lot; else $lot_no="";
				$storeid=$store_idarr[$prod_id][$issue_store_id][$lot_no]['id'];
				$store_stock_arr[$storeid][$prod_id][$issue_store_id][$cat_id][$lot_no]['issue_qty']=$txt_reqn_qnty_edit;
				$prod_stock=$product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
					
				$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
				$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				//$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				
				if(str_replace("'",'',$$txt_item_cat)==22)
				{
					$sql_store="SELECT a.id, b.store_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock 	from product_details_master a, inv_transaction b where a.id=b.prod_id and a.company_id=$cbo_company_name and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and b.store_id=$issue_store_id and a.id in(".str_replace("'",'',$$txt_prod_id).")
					group by a.id, b.store_id";
					//echo $sql_store;
					$store_result=sql_select( $sql_store );
					$store_prod_qnty=$store_result[0][csf("store_stock")]*1;
					
					if(number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($store_prod_qnty,6,'.','') || number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($prod_stock,6,'.',''))
					{
						echo "20**Issue Quantity Not Allow Over Stock Quantity";disconnect($con); die;
					}
				}
				else
				{
					$store_prod_qnty=$store_wise_stock_qnty_arr[str_replace("'",'',$$txt_prod_id)][$lot_no]*1;
					$trans_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$lot_no]["STOCK"];
					if(number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($store_prod_qnty,6,'.','') || number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($prod_stock,6,'.','') || number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($trans_stock,6,'.','') )
					//if(str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $store_prod_qnty || str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $trans_stock)
					{
						echo "20**Issue Quantity Not Allow Over Stock Quantity";disconnect($con); die;
					}
				}
				
				
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
			$item_lot=str_replace("'",'',$$txt_lot);
			if($variable_lot==1) $lot_no=$item_lot; else $lot_no="";
			$storeId=$store_idarr[$prod_id][$issue_store_id][$lot_no]['id'];
			
			if(str_replace("'",'',$$reqn_qnty_edit)>0)
			{
				if(str_replace("'",'',$storeId)!="")
				{
					$reg_issue_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$cat_id][$lot_no]['issue_qty'];
					$curr_stock=$store_arr[$storeId]['qty']-$reg_issue_qty;
					$s_avg_rate=$store_arr[$storeId]['avg_rate'];
					$store_StockValue=$curr_stock*$s_avg_rate;
					$sid_arr[]=str_replace("'",'',$storeId);
					$data_array_store[str_replace("'",'',$storeId)] =explode(",",("".$reg_issue_qty.",".$curr_stock.",".$store_StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
			}
		}
		//echo "10**";disconnect($con); die;
		
		$chemDataArr=array(); $dyesDataArr=array(); $auxChemDataArr=array();
		
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
		
		if($all_prod_id_c!="")
		{
			$chemData = sql_select("SELECT prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_c) and balance_qnty>0 and transaction_type in (1,4,5) and item_category=5 and status_active =1 and is_deleted=0 order by transaction_date,id $cond_lifofifoC");
			foreach($chemData as $row)
			{
				$chemDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		
		if($all_prod_id_d!="")
		{
			$dyesData = sql_select("SELECT prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_d) and balance_qnty>0 and transaction_type in (1,4,5) and item_category=6 and status_active =1 and is_deleted=0 order by transaction_date,id $cond_lifofifoD");
			foreach($dyesData as $row)
			{
				$dyesDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		
		if($all_prod_id_ac!="")
		{
			$auxChemData = sql_select("SELECT prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_ac) and balance_qnty>0 and transaction_type in (1,4,5) and item_category in(7,22,23)  and status_active =1 and is_deleted=0 order by transaction_date,id $cond_lifofifoA");
			foreach($auxChemData as $row)
			{
				$auxChemDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}

		$updateID_array=array();
		$update_data=array();
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, requisition_no, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, cons_uom, cons_quantity, cons_rate, cons_amount, store_id, inserted_by, insert_date, batch_lot";
		
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
			
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("DYES_CHEM_ISSUE_DTLS_PK_SEQ", "dyes_chem_issue_dtls", $con);
				
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];

				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
					
				$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
				$issue_stock_value = $avg_rate*$txt_reqn_qnty_e;
								
				if ($adcomma!=1) $data_array_trans .=",";
				$data_array_trans.="(".$id_trans.",".$dys_che_update_id.",".$txt_req_id.",".$cbo_issue_basis.",".$txt_req_id.",".$cbo_company_name.",".$$txt_prod_id.",".$$txt_item_cat.",2,".$txt_issue_date.",12,".$txt_reqn_qnty_e.",".$avg_rate.",".$issue_stock_value.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$txt_lot.")";
				
				if ($adcomma!=1) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$dys_che_update_id.",".$id_trans.",".$txt_req_no.",".$txt_req_id.",".$txt_recipe_id.",".$cbo_issue_basis.",".$cbo_sub_process.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				//product master table data UPDATE START----------------------//
				$currentStock   = $stock_qnty-$txt_reqn_qnty_e;
				$StockValue	 	= $currentStock*$avg_rate;
				$avgRate	 	= number_format($avg_rate,$dec_place[3],'.',''); 
				
				if(str_replace("'",'',$$txt_prod_id)!="")
				{
					$id_arr[]=str_replace("'",'',$$txt_prod_id);
					$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".$StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
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
							$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",250,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
							
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
							$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",250,".$$txt_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
		//mysql_query("ROLLBACK"); echo "10**".$flag;die;
		//echo "10**insert into dyes_chem_issue_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
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
		//echo "5**0**insert into inv_mrr_wise_issue_details (".$field_array_mrr.") values ".$data_array_mrr;die;
		
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
		//echo "10**".$rID.'='.$rID2.'='.$rID3.'='.$rID4.'='.$mrrWiseIssueID.'='.$upTrID.'='.$store_ID;die;
		
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
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$all_prod_id_c=''; $all_prod_id_d=''; $all_prod_id_ac=''; $all_transId='';  $all_prod_ids='';$all_cat_ids='';
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_lot="txt_lot_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$transId="transId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			
			if(str_replace("'",'',$$txt_item_cat)==5) $all_prod_id_c.=str_replace("'",'',$$txt_prod_id).",";
			else if(str_replace("'",'',$$txt_item_cat)==6) $all_prod_id_d.=str_replace("'",'',$$txt_prod_id).",";
			else $all_prod_id_ac.=str_replace("'",'',$$txt_prod_id).",";
			
			$all_transId.=str_replace("'",'',$$transId).",";
			
			//store wise
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$store_stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
				$txt_reqn_qnty_edit=str_replace("'",'',$$txt_reqn_qnty_edit);
				$hidtxt_reqn_qnty_edit=str_replace("'",'',$$hidtxt_reqn_qnty_edit);
				$prod_id=str_replace("'",'',$$txt_prod_id);
				$item_lot=str_replace("'",'',$$txt_lot);
				$cat_id=str_replace("'",'',$$txt_item_cat);
				
				if($variable_lot==1) $lot_no=$item_lot; else $lot_no="";
				$storeid=$store_idarr[$prod_id][$issue_store_id][$lot_no]['id'];
				$store_stock_arr[$storeid][$prod_id][$issue_store_id][$cat_id][$lot_no]['issue_qty']=$txt_reqn_qnty_edit;
				$store_stock_arr[$storeid][$prod_id][$issue_store_id][$cat_id][$lot_no]['hidden_qty']=$hidtxt_reqn_qnty_edit;
				//$store_stock_arr[str_replace("'",'',$$txt_prod_id)]['hidd_qty']=str_replace("'",'',$$hidtxt_reqn_qnty_edit);
				$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
				$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				//$all_store_ids.=str_replace("'",'',$$cbo_store_name).",";
				$prod_stock=($product_arr[$prod_id]['qty']+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
				
			
				if(str_replace("'",'',$$txt_item_cat)==22)
				{
					$sql_store="SELECT a.id, b.store_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as store_stock from product_details_master a, inv_transaction b where a.id=b.prod_id and a.company_id=$cbo_company_name and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0and b.store_id=$issue_store_id and a.id in(".str_replace("'",'',$$txt_prod_id).")
					group by a.id, b.store_id";
					//echo $sql_store;
					$store_result=sql_select( $sql_store );
					$store_prod_qnty=$store_result[0][csf("store_stock")]*1;
					$store_c_stock=($store_prod_qnty+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
					if(str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $store_c_stock || str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $prod_stock)
					{
						echo "20**Issue Quantity Not Allow Over Stock Quantity";disconnect($con); die;
					}
				}
				else
				{
					$store_prod_qnty=$store_wise_stock_qnty_arr[str_replace("'",'',$$txt_prod_id)][$lot_no]*1;
					$store_c_stock=($store_prod_qnty+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
					$store_tans_qnty=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$lot_no]["STOCK"];
					$store_tans_c_stock=($store_tans_qnty+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
					if(str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $store_c_stock  || str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $prod_stock || str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $store_tans_c_stock)
					//if(str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $store_c_stock || str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $store_tans_c_stock)
					{
						echo "20**Issue Quantity Not Allow Over Stock Quantity";disconnect($con); die;
					}
				}
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
		
		$all_prod_id_c=chop($all_prod_id_c,',');
		$all_prod_id_d=chop($all_prod_id_d,',');
		$all_prod_id_ac=chop($all_prod_id_ac,',');
		$all_transId=chop($all_transId,',');
		
		if($all_prod_id_c!="")
		{
			$chemData = sql_select("SELECT prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_c) and balance_qnty>0 and status_active =1 and is_deleted=0 and transaction_type in (1,4,5) and item_category=5 order by transaction_date $cond_lifofifoC");
			foreach($chemData as $row)
			{
				$chemDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		
		if($all_prod_id_d!="")
		{
			$dyesData = sql_select("SELECT prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_d) and balance_qnty>0 and status_active =1 and is_deleted=0 and transaction_type in (1,4,5) and item_category=6 order by transaction_date $cond_lifofifoD");
			foreach($dyesData as $row)
			{
				$dyesDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		
		if($all_prod_id_ac!="")
		{
			$auxChemData = sql_select("SELECT prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_ac) and balance_qnty>0 and status_active =1 and is_deleted=0 and transaction_type in (1,4,5) and item_category in(7,22,23) order by transaction_date $cond_lifofifoA");
			foreach($auxChemData as $row)
			{
				$auxChemDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		//echo "10**select a.item_category, a.prod_id, b.issue_trans_id, a.id, a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id in($all_transId) and b.entry_form=5";disconnect($con); die;
		$prev_prod_id_arr=array();
		$transData = sql_select("SELECT a.item_category, a.prod_id, b.issue_trans_id, a.id, a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id in($all_transId) and b.entry_form=250 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"); 
		foreach($transData as $row)
		{
			$adjTransDataArr[$row[csf("item_category")]][$row[csf("issue_trans_id")]].=$row[csf("id")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")]."**".$row[csf("issue_qnty")]."**".$row[csf("rate")]."**".$row[csf("amount")].",";
			
		}
		
		$updateID_array=array(); $update_data=array();
		
		$field_array_update="issue_purpose*location_id*buyer_job_no*style_ref*req_no*req_id*issue_date*loan_party*lap_dip_no*batch_no*order_id*sub_order_id*updated_by*update_date*lc_company*floor_id*machine_id*remarks";
		$data_array_update="".$cbo_issue_purpose."*".$cbo_location_name."*".$txt_order_no."*".$txt_buyer_style."*".$txt_req_no."*".$txt_req_id."*".$txt_issue_date."*".$cbo_loan_party."*".$txt_recipe_id."*".$txt_recipe_no."*".$hidden_buyer_po_id."*".$hidden_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_name."*".$cbo_flore_name."*".$cbo_machine_name."*".$txt_remarks."";
		//echo $data_array_update;
		$dys_che_issue_num=$txt_mrr_no;
		$dys_che_update_id=$update_id;
		
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		
		$up_field_array_trans="prod_id*item_category*transaction_date*cons_uom*cons_quantity*cons_rate*cons_amount*store_id*updated_by*update_date*batch_lot";
		$up_field_array_dtls="sub_process*product_id*item_category*dose_base*ratio*recipe_qnty*adjust_percent*adjust_type*required_qnty*req_qny_edit*updated_by*update_date";
		$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
		//echo $total_row;die;
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
			
			$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
			$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
			$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
			
			$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
			$issue_stock_value = $avg_rate*str_replace("'","",$txt_reqn_qnty_e);
			
			if(str_replace("'",'',$$transId)!="")
			{
				//fnc_store_wise_qty_operation($operation,$cbo_company_name,$$cbo_store_name,$$txt_item_cat,$$txt_prod_id,$txt_reqn_qnty_e,$avg_rate,$issue_stock_value,$pc_date_time,2);
				$id_arr_trans[]=str_replace("'",'',$$transId);
				$data_array_trans[str_replace("'",'',$$transId)] =explode("*",("".$$txt_prod_id."*".$$txt_item_cat."*".$txt_issue_date."*12*".$txt_reqn_qnty_e."*".$avg_rate."*".$issue_stock_value."*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$txt_lot.""));
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
				$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".$StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
			}
			//------------------ product_details_master END--------------//
			
			//-----Store Wise Stock----//
			$prod_id=str_replace("'",'',$$txt_prod_id);
			$item_lot=str_replace("'",'',$$txt_lot);
			$cat_id=str_replace("'",'',$$txt_item_cat);
			if($variable_lot==1) $lot_no=$item_lot; else $lot_no="";
			$storeId=$store_idarr[$prod_id][$issue_store_id][$lot_no]['id'];
			// echo $$txt_reqn_qnty_e.'aaz';
			if(str_replace("'",'',$txt_reqn_qnty_e)>0)
			{
				//echo $$txt_reqn_qnty_e.'a4';
				if(in_array($storeId,$storeArrId))
				{
					if(str_replace("'",'',$storeId)!="")
					{
						$req_hidden_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$cat_id][$lot_no]['hidden_qty'];
						$reg_issue_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$cat_id][$lot_no]['issue_qty'];
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",250,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",250,".$$txt_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
		
		$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id in($all_transId) and entry_form=250 and status_active =1 and is_deleted=0",1);
		if($query2) $flag=1; else $flag=0;
		
		$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1); 
		if($flag==1) { if($rID) $flag=1; else $flag=0; }
		
		if(count($id_arr_trans)>0)
		{  
			//echo bulk_update_sql_statement("inv_transaction","id",$up_field_array_trans,$data_array_trans,$id_arr_trans);die;
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
			//echo bulk_update_sql_statement( "product_details_master", "id", $field_array_prod, $data_array_prod, $id_arr );die;
		    $rID4=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod, $data_array_prod, $id_arr ),1);
			if($flag==1) { if($rID4) $flag=1; else $flag=0; }
		}
		
		
		if($data_array_mrr!="")
		{	
		//echo "insert into inv_mrr_wise_issue_details($field_array_mrr)values".$data_array_mrr;die;
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			if($flag==1) 
			{
				if($mrrWiseIssueID) $flag=1; else $flag=0; 
			} 
		}
		
		if(count($updateID_array)>0)
		{  
			//echo bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array);die;
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array),1);
			if($flag==1) { if($upTrID) $flag=1; else $flag=0; }
		}
		if(count($sid_arr)>0)
		{
			//$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
			$store_ID=execute_query(bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr ),1);
			if($flag==1) { if($store_ID) $flag=1; else $flag=0; } 
		}
		//echo "10**".bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr );die;
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
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$transData = sql_select("SELECT a.item_category, a.prod_id, b.issue_trans_id, a.id, a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount
		from inv_transaction a, inv_mrr_wise_issue_details b 
		where a.id=b.recv_trans_id and b.issue_trans_id in(".implode(",",$upTransIdArr).") and b.entry_form=250 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"); 
		foreach($transData as $row)
		{
			$adjTransDataArrs[$row[csf("item_category")]][$row[csf("issue_trans_id")]].=$row[csf("id")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")]."**".$row[csf("issue_qnty")]."**".$row[csf("rate")]."**".$row[csf("amount")].",";
		}
		$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
		$update_field = "balance_qnty*balance_amount*updated_by*update_date";
		$sid_arr=array();$id_arr_prod=array();
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_lot="txt_lot_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$transId="transId_".$i;
			if(str_replace("'","",$$transId)) $issueTransIdArr[str_replace("'","",$$transId)]=str_replace("'","",$$transId);
			if(str_replace("'","",$$updateIdDtls)) $issueDtlsIdArr[str_replace("'","",$$updateIdDtls)]=str_replace("'","",$$updateIdDtls);
			
			
			$txt_reqn_qnty_e=str_replace("'","",$$hidtxt_reqn_qnty_edit);
			
			$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
			$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
			$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
			
			//product master table data UPDATE START----------------------//
			$currentStock   = $stock_qnty+str_replace("'", '',$$hidtxt_reqn_qnty_edit);
			$StockValue	 	= $currentStock*$avg_rate;
			if(str_replace("'",'',$$txt_prod_id)!="")
			{
				$id_arr_prod[]=str_replace("'",'',$$txt_prod_id);
				$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".$StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
			}
			//------------------ product_details_master END--------------//
			
			//-----Store Wise Stock----//
			$prod_id=str_replace("'",'',$$txt_prod_id);
			$item_lot=str_replace("'",'',$$txt_lot);
			$cat_id=str_replace("'",'',$$txt_item_cat);
			if($variable_lot==1) $lot_no=$item_lot; else $lot_no="";
			$storeId=$store_idarr[$prod_id][$issue_store_id][$lot_no]['id'];
			// echo $$txt_reqn_qnty_e.'aaz';
			if(str_replace("'",'',$txt_reqn_qnty_e)>0)
			{
				if(str_replace("'",'',$storeId)!="")
				{
					$store_curr_stock=$store_arr[$storeId]['qty']+$txt_reqn_qnty_e;
					$s_avg_rate=$store_arr[$storeId]['avg_rate'];
					$store_StockValue=$store_curr_stock*$s_avg_rate;
					$sid_arr[]=str_replace("'",'',$storeId);
					$data_array_store[str_replace("'",'',$storeId)] =explode(",",("".$txt_reqn_qnty_e.",".$store_curr_stock.",".$store_StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
			}
			//----Store Wise Stock End------//
		
			$transDataArray=explode(",",chop($adjTransDataArrs[str_replace("'",'',$$txt_item_cat)][str_replace("'",'',$$transId)],','));
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
				}
			}
			
		}
		//item for each end
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
		
		$upTrID=$upProdId=$store_ID=$delTrans=$delDtls=$delMrr=true;
		$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_field,$update_data,$updateID_array),0);
		$upProdId=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod,$data_array_prod,$id_arr_prod),0);
		if(count($sid_arr)>0)
		{
			$store_ID=execute_query(bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr ),0);
		}
		
		$delTrans=execute_query("update inv_transaction set status_active=0, is_deleted=1 where id in(".implode(",",$issueTransIdArr).") ");
		$delDtls=execute_query("update dyes_chem_issue_dtls set status_active=0, is_deleted=1 where id in(".implode(",",$issueDtlsIdArr).") ");
		$delMrr=execute_query("update inv_mrr_wise_issue_details set status_active=0, is_deleted=1 where issue_trans_id in(".implode(",",$issueTransIdArr).") ");
		
		
		//echo "10**$upTrID && $upProdId && $store_ID && $delTrans && $delDtls && $delMrr";die;
		
		if($db_type==0)
		{
			//echo $flag;
			if($upTrID && $upProdId && $store_ID && $delTrans && $delDtls && $delMrr)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($upTrID && $upProdId && $store_ID && $delTrans && $delDtls && $delMrr)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
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
	
	disconnect($con);
		die;
			
}


if($action=="chemical_dyes_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="SELECT a.id, a.issue_number, a.issue_date, a.issue_basis,a.issue_purpose, a.req_no, a.batch_no, a.loan_party,a.lap_dip_no, a.knit_dye_source, a.knit_dye_company, a.challan_no,a.remarks, a.buyer_job_no,a.order_id, a.buyer_id, a.style_ref from inv_issue_master a where a.id='$data[1]' and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0";
	$batch_arr = return_library_array("SELECT id,batch_no from pro_batch_create_mst where batch_against<>0 status_active =1 and is_deleted=0","id","batch_no");
    $dataArray=sql_select($sql);
	$recipe_id=$dataArray[0][csf('lap_dip_no')];
	$batch_id_all=explode(",",$dataArray[0][csf('batch_no')]);
    foreach($batch_id_all as $b_id)
	{
		if($batch_no=="") $batch_no=$batch_arr[$b_id];
		else $batch_no.=",".$batch_arr[$b_id];
	}

	$sql3="select a.id,b.buyer_po_ids from inv_issue_master a, dyes_chem_issue_requ_mst b where a.req_id=b.id and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql3; die;

	$data_sql3=sql_select($sql3);
	$buyer_po_ids=$data_sql3[0][csf('buyer_po_ids')];


	$order_sql="select a.id,c.buyer_buyer from inv_issue_master a, dyes_chem_issue_requ_mst b, subcon_ord_dtls c where a.req_id=b.id and a.id='$data[1]' and c.buyer_po_id in($buyer_po_ids) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

	//echo $order_sql; die;

	$data_Order_Array=sql_select($order_sql);
	$buyer_buyer=$data_Order_Array[0][csf('buyer_buyer')];


	$company_library=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name"  );
	$buyer_library=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );
	$supplier_library=return_library_array( "SELECT id,supplier_name from  lib_supplier where status_active =1 and is_deleted=0", "id","supplier_name"  );
	$store_library=return_library_array( "SELECT id, store_name from  lib_store_location where status_active =1 and is_deleted=0", "id", "store_name"  );
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country where status_active =1 and is_deleted=0", "id", "country_name"  );
	$batch_weight_arr=return_library_array( "SELECT id, batch_weight from  pro_batch_create_mst where status_active =1 and is_deleted=0", "id", "batch_weight"  );
	$color_arr=return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$group_library = return_library_array("SELECT id, group_name from lib_group where status_active =1 and is_deleted=0", "id", "group_name");
	// $req_arr = return_library_array("SELECT id,requ_no from dyes_chem_issue_requ_mst where status_active=1 and is_deleted=0 and entry_form=221","id","requ_no");
	$process_sql = "SELECT b.id, b.sub_process as sub_process_id, a.store_id from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id and a.transaction_type=2 and b.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";
	$nameArray=sql_select( $process_sql );
	$process_array=array();
	foreach($nameArray as $inf) {
		$process_array[$inf[csf("sub_process_id")]]["sub_process_id"]=$inf[csf("sub_process_id")];
		// $process_array[$inf[csf("sub_process_id")]]["store_id"]=$inf[csf("store_id")];
	}
    foreach($process_array as $sub_process_val) {
    	$color_name .= $color_arr[$sub_process_val["sub_process_id"]].',';
	}
	
	$order_id=$dataArray[0][csf('order_id')];
	if($order_id=="") $order_id=0;else $order_id=$order_id;
	$po_sqls=sql_select("SELECT id as po_id,grouping as ref_no,file_no from  wo_po_break_down where  status_active=1 and is_deleted=0 and id in(".$order_id.")");
	$file_no='';$ref_no='';
	foreach($po_sqls as $row_id)
	{
		if($file_no=='') $file_no=$row_id[csf('file_no')];else $file_no.=",".$row_id[csf('file_no')];
		if($ref_no=='') $ref_no=$row_id[csf('ref_no')];else $ref_no.=",".$row_id[csf('ref_no')];
	}
	
	if(str_replace("'","",$dataArray[0][csf('batch_no')])!="")
	{
    	$color_id=return_field_value("color_id"," pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].")");
		$color_range_id=return_field_value("color_range_id"," pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].")");
	}
	
    if(trim($dataArray[0][csf('issue_basis')])!=7)
	{
		if(str_replace("'","",$dataArray[0][csf('batch_no')])!="")
		{
			$sql_batch = sql_select("SELECT  batch_weight,color_range_id, total_liquor from pro_batch_create_mst where id in(".$dataArray[0][csf('batch_no')].")  and status_active=1 and is_deleted=0"); 
		}
		foreach($sql_batch as $row_batch)
		{
			$total_liquor=$row_batch[csf("total_liquor")]; 
			$total_batch=$row_batch[csf("batch_weight")];
			$color_range_id=$row_batch[csf("color_range_id")];	
		}
    }
	if(trim($dataArray[0][csf('issue_basis')])==7)
	{
		$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$dataArray[0][csf('lap_dip_no')].") and entry_form=220 and company_id=".$data[0]."","total_liquor");
		//$total_batch=return_field_value("sum(batch_weight) as batch_weight" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","batch_weight");
		if($recipe_id=="") $recipe_id=0; else $recipe_id=$recipe_id;
		$batch_id=return_field_value("batch_id as batch_id" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and entry_form=220","batch_id");
		if($batch_id=="") $batch_id=0; else $batch_id=$batch_id;
		$total_batch=return_field_value("sum(batch_qty) as batch_weight" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and batch_id in(".$batch_id.") and entry_form=220 and company_id=".$data[0]." ","batch_weight");
				
		$color_range_id=return_field_value("color_range_id as color_range_id" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","color_range_id");
	}
	?>
	<?
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$dye_company=$dataArray[0][csf('knit_dye_company')];
	$nameArray=sql_select( "SELECT plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$dye_company and status_active =1 and is_deleted=0");
	$group_id=$nameArray[0][csf('group_id')];
	
	if($dataArray[0][csf('knit_dye_source')]==3)
		{
			$com_supp_cond=$supplier_library[$dataArray[0][csf('knit_dye_company')]];
		}
		else
		{
			$com_supp_cond=$company_library[$dataArray[0][csf('knit_dye_company')]];
		}
	?>
	<div style="width:1200px;">
    <table width="1100" cellspacing="0" align="">
        <tr>
        	<td rowspan="3" width="70">
            	<img src="../../../<? echo $image_location; ?>" height="60" width="180">
            </td>
            <td colspan="6" align="center" style="font-size:20px">
            	<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$com_supp_cond; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					 
					foreach ($nameArray as $result)
					{ 
					?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')] ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							City No: <? echo $result[csf('city')]; ?>
							Zip Code: <? echo $result[csf('zip_code')]; ?>
							Province No: <?php echo $result[csf('province')]; ?>
							Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
							Email Address: <? echo $result[csf('email')]; ?>
							Website No: <? echo $result[csf('website')];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><strong><u><? echo 'Owner Company : '.$company_library[$data[0]].'<br/>'.' Dyes & Chemical Issue Note';?></u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Issue ID</strong></td><td width="175px">: <? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="125"><strong>Issue Date</strong></td><td width="150px">: <? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="140"><strong>Issue Basis</strong></td><td width="250px">: <? echo $receive_basis_arr[$dataArray[0][csf('issue_basis')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Req. No</strong></td><td>: <? echo $dataArray[0][csf('req_no')]; ?></td>
			<td><strong>Issue Purpose</strong></td><td>: <? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			<td><strong>Loan Party</strong></td><td>: <? echo $supplier_library[$dataArray[0][csf('loan_party')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Challan No</strong></td><td>: <? echo $dataArray[0][csf('challan_no')]; ?>&nbsp;</td>
            <td><strong>Recipe No</strong></td><td>: <? echo $dataArray[0][csf('batch_no')]; ?></td>
            <td><strong>Buyer Order</strong></td><td style="word-break:break-all">: <? echo $dataArray[0][csf('buyer_job_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Buyer Name</strong></td><td>: <? echo $buyer_library[$buyer_buyer]; ?></td>
            <td><strong>Style Ref.</strong></td><td>: <? echo $dataArray[0][csf('style_ref')]; ?></td>
           	<td><strong>Color Range</strong></td><td>: <? echo $color_range[$color_range_id]; ?></td>
        </tr>
        <tr>
            <td><strong>File No</strong></td><td>: <? echo $file_no; ?></td>
            <td><strong>Ref. No</strong></td><td>: <?  echo $ref_no; ?></td>
            <td><strong>Color</strong></td><td><div style="word-wrap:break-word; width:175px">: <?
            echo implode(", ", array_unique(explode(",", chop($color_name,","))));
             //echo $color_name; //$color_arr[$color_id]; ?></div></td>
        </tr>
         <tr>
            <td><strong>Remarks</strong></td><td colspan="5"><p>: <? echo $dataArray[0][csf('remarks')]; ?></p></td>
            <td></td>
        </tr>
    </table>
        <br>
	<div style="width:100%;">
    <table align="" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <?
			/*if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==13)
			{
				
			}
			else
			{
				?>
				<th width="80" >Sub Process</th>
				<?
			}*/
			?>
            <th width="200" >Store Name</th>
            <th width="80" >Item Cat.</th>
            <th width="80" >Item Group</th>
            <th width="80" >Lot</th>
            <th width="100" >Sub Group</th>
            <th width="170" >Item Description</th>
            <th width="50" >UOM</th>
            <th width="110" >Dose Base</th> 
            <th width="40" >Ratio</th>
            <th width="60" >Recipe Qnty</th>
            <th width="50" >Adj%</th>
            <th width="60" >Adj Type</th> 
            <th width="60" >Issue Qnty</th>
        </thead>
        <tbody> 
   
	<?
 	$group_arr=return_library_array( "SELECT id,item_name from lib_item_group where item_category in (5,6,7,23,22) and status_active=1 and is_deleted=0",'id','item_name');
 
 	$sql_dtls = "SELECT b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity,b.cons_amount, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount,c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit, b.batch_lot 
 	from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
	where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id  and b.transaction_type=2 and a.entry_form=250 and b.item_category in (5,6,7,23,22) and d.mst_id=$data[1] and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 
	order by d.sub_process "; 
	 // echo $sql_dtls;die;
	$sql_result= sql_select($sql_dtls);
	$i=1;
	//if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==13) $colspan=10; else $colspan=11;
	$colspan=10;
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			
			$recipe_qnty=$row[csf('recipe_qnty')];
			$recipe_qnty_sum += $recipe_qnty;
			
			$req_qny_edit=$row[csf('req_qny_edit')];
			$req_qny_edit_sum += $req_qny_edit;
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                 <?
				/*if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==13)
				{
					
				}
				else
				{
					?>
					<td style="word-break:break-all"><? echo $dyeing_sub_process[$row[csf("sub_process")]]; ?></td>
					<?
				}*/
				?>
                <td align="center" style="word-break:break-all"><? echo $store_library[$row[csf("store_id")]]; ?></td>
                <td style="word-break:break-all"><? echo $item_category[$row[csf("item_category")]]; ?></td>
                <td style="word-break:break-all"><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                <td style="word-break:break-all"><? echo $row[csf("batch_lot")]; ?></td>
                <td style="word-break:break-all"><? echo $row[csf("sub_group_name")]; ?></td>
                <td style="word-break:break-all"><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
                <td align="center" style="word-break:break-all"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                <td style="word-break:break-all"><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("ratio")],4); ?></td>
                <td align="right"><? echo number_format($row[csf("recipe_qnty")],4); ?></td>
                <td align="right"><? echo $row[csf("adjust_percent")]; ?></td>
                <td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("req_qny_edit")],4); ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="<? echo $colspan; ?>" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo number_format($recipe_qnty_sum,4); ?></td>
                <td align="right" colspan="3"><?php echo number_format($req_qny_edit_sum,4); ?></td>
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            echo signature_table(9, $data[0], "1100px");
         ?>
      </div>
   </div> 
    <script type="text/javascript" src="../../../js/jquery.js"></script>
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
  
	   generateBarcode('<? echo $data[3]; ?>');
	 
	 
	 </script>
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
	$result=sql_select("SELECT category_id, prod_id, lot, cons_qty 
	from inv_store_wise_qty_dtls where company_id=$company_id and store_id=$store_id and status_active=1 and is_deleted=0");
	$stock_qty_arr=array();
	foreach($result as $row)
	{
		 $stock_qty_arr[$company_id][$store_id][$row[csf('category_id')]][$row[csf('prod_id')]][$row[csf('lot')]]['stock']=$row[csf('cons_qty')]; 
	}
	return $stock_qty_arr;
}
?>
