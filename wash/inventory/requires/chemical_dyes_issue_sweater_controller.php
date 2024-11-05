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
	echo create_drop_down( "cbo_location_name", 170, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $company_location_credential_cond  order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}


if ($action == "company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=20 and report_id=99 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print').hide();\n";
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print').show();\n";}			
		}
	}
	
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");
	echo "$('#variable_lot').val(".$sql[0][csf("auto_transfer_rcv")].");\n";	
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("**",$data);
	
	
	$company_id=$data[1];
	$basis_id=$data[0];
	if($basis_id==4)
	{
	echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $store_location_credential_cond and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "fn_sub_process_enable(this.value);fnc_item_details(this.value,'','')",0 );
	}
	else
	{
		echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $store_location_credential_cond and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "fn_sub_process_enable(this.value);",0 );
	}
 
	exit();
}

if ($action=="load_drop_down_loan_party")
{
	$party_sql = "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
		where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name";

	$party_sql = sql_select($party_sql);
	//$party_count=count($party_sql);
	echo create_drop_down( "cbo_loan_party", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","","" );
	exit();
}

if ($action=="load_drop_down_color")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$basis_id=$data[1];
	if($basis_id==4)
	{
		echo create_drop_down( "cbo_sub_process", 170, "select max(id) as id, color_name from lib_color where status_active=1 and is_deleted=0 and (color_name is not null or color_name <> 0) and entry_form =300  group by color_name","id,color_name", 1, "-- Select--", "", "",1);
	}
	else
	{
		echo create_drop_down( "cbo_sub_process", 170, "select max(id) as id, color_name from lib_color where status_active=1 and is_deleted=0 and (color_name is not null or color_name <> 0) and entry_form =300  group by color_name","id,color_name", 1, "-- Select--", "", "fnc_item_details(this.value,'','')",1);
	}
	
	
	exit();
}

if ($action=="load_drop_down_sub_process")
{
	$data=explode('**',$data); 
	//if($data[2]==1) $is_posted_account=1; else $is_posted_account=0; PRO_RECIPE_ENTRY_MST
	//$sql_req_dtls="select a.multicolor_id, b.color_name from dyes_chem_issue_requ_dtls a, lib_color b where a.multicolor_id=b.id and a.status_active=1 and a.mst_id=$data[1] group by a.multicolor_id, b.color_name";
	$sql_req_dtls="select a.color_id, b.color_name from pro_recipe_entry_mst a, lib_color b where a.color_id=b.id and a.status_active=1 and a.id=$data[1] group by a.color_id, b.color_name";
	echo create_drop_down( "cbo_sub_process", 170, $sql_req_dtls,"color_id,color_name", 1, "-- Select--", "", "fnc_item_details(this.value,'','')",1);
	exit();
}

if ($action=="load_drop_down_sub_process_up")
{
	$data=explode('**',$data);
	//if($data[2]==1) $is_posted_account=1; else $is_posted_account=0;
	$sql_req_dtls="select a.sub_process, b.color_name from dyes_chem_issue_dtls a, lib_color b where a.sub_process=b.id and a.status_active=1 and a.mst_id=$data[1] group by a.sub_process, b.color_name";
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
	<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
            	<tr>
                	<th colspan="3" style="text-align:right">Search Type:</th>
                    <th><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 0, "",1 ); ?></th>
                  <th colspan="4"> </th>
                </tr>
                <tr>                	 
                    <th width="150">Company Name</th>
                    <th width="80">Requisition No</th>
                    <th width="80">Job No</th>
                    <th width="110">PO No</th>
                    <th width="110">Style Ref.</th>
                    <th width="130" colspan="2">Req. Date Range</th>
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
                    <td><input type="text" style="width:70px" class="text_boxes_numeric"  name="txt_requisition_no" id="txt_requisition_no" placeholder="Number Field" /></td> 
                    <td><input type="text" style="width:70px" class="text_boxes_numeric"  name="txt_job_no" id="txt_job_no" placeholder="Number Field" /></td>
                    <td><input type="text" style="width:100px" class="text_boxes"  name="txt_buyer_po" id="txt_buyer_po" placeholder="Character Field" /></td>
                    <td><input type="text" style="width:100px" class="text_boxes"  name="txt_buyer_style" id="txt_buyer_style" placeholder="Character Field" /></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" /></td> 
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" /></td> 
                    <td>
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_requisition_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_buyer_po').value+'_'+document.getElementById('txt_buyer_style').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_reqisition_search_list_view', 'search_div', 'chemical_dyes_issue_sweater_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" />				
                    </td>
                </tr>
                <tr>                  
                    <td align="center" valign="middle" colspan="8">
                        <? echo load_month_buttons(1);  ?>
                         <input type="hidden" id="hidden_issue_number" value="" />
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
	exit();
}

if($action=="create_reqisition_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = trim($ex_data[0]);
	$requisition_no = trim($ex_data[1]);
	$job_no = trim($ex_data[2]);
	$buyer_po = trim($ex_data[3]);
	$buyer_style = trim($ex_data[4]);
	$fromDate = trim($ex_data[5]);
	$toDate = trim($ex_data[6]);
	$string_search_type = trim($ex_data[7]);
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
		echo "Please Select Requisition Date Range.";disconnect($con); die;
	}
	//echo $company."=".$requisition_no."=".$emb_job."=".$emb_order."=".$buyer_po."=".$buyer_style."=".$fromDate."=".$toDate."=".$string_search_type;die;
	$emb_order_id='';$buyer_order_id='';
	if($emb_job!="" || $emb_order!="")
	{
		$emb_job_cond="";
		if($emb_job!="")
		{
			if($string_search_type==1) $emb_job_cond = " and a.job_no_prefix_num='$emb_job'";
			else if($string_search_type==4 || $ex_data[5]==0) $emb_job_cond = " and a.job_no_prefix_num LIKE '%$emb_job%'";	
			else if($string_search_type==2) $emb_job_cond = " and a.job_no_prefix_num LIKE '$emb_job%'";	
			else if($string_search_type==3) $emb_job_cond = " and a.job_no_prefix_num LIKE '%$emb_job'";	
		}
		
		if($emb_order!="")
		{
			if($string_search_type==1) $emb_job_cond .= " and b.order_no='$emb_order'";
			else if($string_search_type==4 || $ex_data[5]==0) $emb_job_cond .= " and b.order_no LIKE '%$emb_order%'";	
			else if($string_search_type==2) $emb_job_cond .= " and b.order_no LIKE '$emb_order%'";	
			else if($string_search_type==3) $emb_job_cond .= " and b.order_no LIKE '%$emb_order'";	
		}
	}
	
	
	if($buyer_po!="" || $buyer_style!="" || $job_no!="")
	{
		$buyer_job_cond="";
		if($job_no!="")
		{
			if($string_search_type==1) $buyer_job_cond = " and a.job_no_prefix_num='$job_no'";
			else if($string_search_type==4 || $ex_data[5]==0) $buyer_job_cond = " and a.job_no_prefix_num LIKE '%$job_no%'";	
			else if($string_search_type==2) $buyer_job_cond = " and a.job_no_prefix_num LIKE '$job_no%'";	
			else if($string_search_type==3) $buyer_job_cond = " and a.job_no_prefix_num LIKE '%$job_no'";	
		}
		
		if($buyer_style!="")
		{
			if($string_search_type==1) $buyer_job_cond = " and a.style_ref_no='$buyer_style'";
			else if($string_search_type==4 || $ex_data[5]==0) $buyer_job_cond = " and a.style_ref_no LIKE '%$buyer_style%'";	
			else if($string_search_type==2) $buyer_job_cond = " and a.style_ref_no LIKE '$buyer_style%'";	
			else if($string_search_type==3) $buyer_job_cond = " and a.style_ref_no LIKE '%$buyer_style'";	
		}
		
		if($buyer_po!="")
		{
			if($string_search_type==1) $buyer_job_cond .= " and b.po_number='$buyer_po'";
			else if($string_search_type==4 || $ex_data[5]==0) $buyer_job_cond .= " and b.po_number LIKE '%$buyer_po%'";	
			else if($string_search_type==2) $buyer_job_cond .= " and b.po_number LIKE '$emb_order%'";	
			else if($string_search_type==3) $buyer_job_cond .= " and b.po_number LIKE '%$buyer_po'";	
		}
		
		$buyer_job_sql="select b.id, a.job_no_prefix_num, a.job_no, a.style_ref_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $buyer_job_cond";
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
	if($buyer_order_id !="") $req_cond=" and a.order_id in($buyer_order_id)";
	
	//if($buyer_order_id !="") $req_cond.=" and a.buyer_po_id in($buyer_order_id)";
	//echo $ex_data[4].'='.$issue_basis;die; 
	if(trim($requisition_no)!="")
	{
		if($string_search_type==1) $req_cond .= " and a.requ_prefix_num=$requisition_no";
		else if($string_search_type==4 || $ex_data[5]==0) $req_cond .= " and a.requ_prefix_num LIKE '%$requisition_no%'";	
		else if($string_search_type==2)  $req_cond .= " and a.requ_prefix_num LIKE '$requisition_no%'";	
		else if($string_search_type==3) $req_cond .= " and a.requ_prefix_num LIKE '%$requisition_no'";	
 	} 
	
	if( $fromDate!="" || $toDate!="" ) 
	{ 
		if($db_type==0){ $req_cond .= " and a.requisition_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $req_cond .= " and a.requisition_date  between '".change_date_format($fromDate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($toDate,'yyyy-mm-dd',"-",1)."'";}
	}

	
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	
	//$arr=array(1=>$company_arr,3=>$receive_basis_arr,4=>$batch_arr,5=>$receipe_arr);

	if($db_type==0) $null_cond="''"; else if($db_type==2) $null_cond="'0'";
	$sql ="select a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.requisition_basis, a.recipe_id, a.id, a.order_id from dyes_chem_issue_requ_mst a where a.company_id=$company and a.entry_form=391 and a.recipe_id!=$null_cond $req_cond";
	//echo $sql;
	$result_sql =sql_select($sql);
	foreach($result_sql as $row)
	{
		$all_recipe_id[$row[csf("recipe_id")]]=$row[csf("recipe_id")];
		$all_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
	}
	if($buyer_order_id =="" && count($all_order_id)>0)
	{
		$buyer_job_sql="select b.id, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.id in (".implode(",",$all_order_id).") ";
		//echo $buyer_job_sql;die;
		$buyer_job_result=sql_select($buyer_job_sql);
		$buyer_order_data=array();
		foreach($buyer_job_result as $row)
		{
			$buyer_order_data[$row[csf("id")]]["buyer_name"]=$buyerArr[$row[csf("buyer_name")]];
			$buyer_order_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$buyer_order_data[$row[csf("id")]]["job_no"]=$row[csf("job_no")];
			$buyer_order_data[$row[csf("id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$buyer_order_data[$row[csf("id")]]["po_number"]=$row[csf("po_number")];
		}
	}
	
	if($all_recipe_id!="")
	{
		$recipe_arr=sql_select("select a.id, a.recipe_no,color_id from pro_recipe_entry_mst a where a.company_id=$company and a.entry_form=300 and a.id in(".implode(",",$all_recipe_id).") ");
	
	}
	foreach($recipe_arr as $row)
	{
		$receipe_data_arr[$row[csf('id')]]['id']=$row[csf('recipe_no')];
		$receipe_data_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
	}
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="120">Req. No</th>
            <th width="70">Req. Date</th>
            <th width="120">Job No</th>
            <th width="120">Order</th>
            <th width="120">Style Ref</th>
            <th>Buyer</th>
        </thead>
   </table>
   <div style="width:750px; overflow-y:scroll; max-height:230px; cursor:pointer" id="buyer_list_view" align="center">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_list_search" >
        <tbody>
			<? 
            $i=1;
            foreach($result_sql as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//echo $row[csf('recipe_id')];
				$recipi_no=$receipe_data_arr[$row[csf('recipe_id')]]['id'];
				
				?>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="js_set_value('<?=$row[csf('requ_no')]."_".$row[csf('id')]."_".$row[csf('recipe_id')]."_".$recipi_no."_".$buyer_order_data[$row[csf("order_id")]]["job_no"]."_".$buyer_order_data[$row[csf("order_id")]]["po_number"]."_".$row[csf("order_id")]."_".$buyer_order_data[$row[csf("order_id")]]["style_ref_no"]."_".$buyer_order_data[$row[csf("order_id")]]["buyer_name"]; ?>')">
                    <td width="30" align="center" title="<?=$recipe_ids; ?>"><?=$i; ?></td>
                    <td width="120" align="center"><?=$row[csf("requ_no")]; ?></td>
                    <td width="70" align="center"><?=change_date_format($row[csf("requisition_date")]); ?></td>
                    <td width="120" align="center" ><?=$buyer_order_data[$row[csf("order_id")]]["job_no"]; ?></td>
                    <td width="120" align="center"><?=$buyer_order_data[$row[csf("order_id")]]["po_number"]; ?></td>
                    <td width="120" align="center"><?=$buyer_order_data[$row[csf("order_id")]]["style_ref_no"]; ?></td>
                    <td><? echo $buyer_order_data[$row[csf("order_id")]]["buyer_name"]; ?></td>
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
	exit();
}

if($action=="item_details")
{
	//echo $data; die;
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
	//1**6769****4**19667****28**0
  	//3**2**1672**7**19368**967**56**0
	//echo $req_id;
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$store_arr=return_library_array( "select a.id as id,a.store_name  as store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name", "id", "store_name"  );
	?>
	<div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="50">Product ID</th>
                <th width="100">Lot No</th>
                <th width="100">Item Cat.</th>
                <th width="100">Group</th>
                <th width="100">Sub Group</th>
                <th width="140">Item Description</th>
                <th width="32">UOM</th>
                <th width="70">Stock Qty</th>
                <th width="73">Recipe Qnty.</th>
                <th width="">Issue Qnty.</th>
            </thead>
        </table>
        <div style="width:1000px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table" id="tbl_list_search" align="left">
                <tbody>
                <?
                if($is_update=="")
                {
					if($issue_basis==4)
					{
						//$cbo_store
						/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock 
						from product_details_master a, inv_transaction b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(5,6,7,23) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 
						group by a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock 
						order by a.item_category_id ";*/	
						$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.cons_qty as store_stock, b.lot 
						from product_details_master a, inv_store_wise_qty_dtls b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(5,6,7,23) and round(b.cons_qty,6)>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						order by a.item_category_id, a.id";
						//echo $sql;die;
						$i=1;
						//$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $selectResult)
						{
	                        $issue_remain=$totalIssued-$totalIssuedReturn;
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$stock_qty=$current_stock=number_format($selectResult[csf('current_stock')],6,".","");
							$stock_qty_org=number_format($selectResult[csf('store_stock')],6,".","");
							//$update_stock=$stock_qty_org+$selectResult[csf('req_qny_edit')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td> 
                                <td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                </td>
                                <td width="100"><p><? echo $selectResult[csf('lot')]; ?>
	                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" value="<? echo $selectResult[csf('lot')]; ?>"></p></td>
                                <td width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                
                                <td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty_org;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($recipe_qnty,6,".",""); ?>" disabled></td>


                                <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($recipe_qnty,6,".",""); ?>" readonly>
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                <input type="hidden" name="stock_check[]" id="stock_check_<? echo $i; ?>" value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>">
                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty_org; ?>)"  value="<? //echo number_format($recipe_qnty,6,".",""); ?>" >
                                <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,".",""); ?>" / >
                                <input type="hidden" name="subreqprocessId[]" id="subreqprocessId_<? echo $i; ?>" value="<?  ?>">
                                </td>
							</tr>
							<?
							$i++;
						}
					}
					if($issue_basis==7)
					{
						$total_issue_sql=sql_select("select a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit,b.sub_req_process_id from inv_issue_master a, dyes_chem_issue_dtls b where a.id=b.mst_id and a.entry_form=298 and a.issue_basis=7 and a.req_id='".$req_id."' and b.sub_process=$sub_process_id");
						foreach($total_issue_sql as $row)
						{
							$total_issue_arr[$row[csf("product_id")]][$row[csf("sub_req_process_id")]]+=$row[csf("req_qny_edit")];
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
						
						$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty as required_qnty, b.req_qny_edit ,b.multicolor_id, b.item_lot 
						from product_details_master a, dyes_chem_issue_requ_dtls b, pro_recipe_entry_mst c
						where a.id=b.product_id and b.mst_id='".$req_id."' and b.recipe_id=c.id and c.color_id=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 order by a.item_category_id ";
						//echo $sql;
						$is_disabled=1;
						$i=1;
						
						$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $selectResult)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$total_preveious_issue=($total_issue_arr[$selectResult[csf("id")]][$selectResult[csf("multicolor_id")]])*1;
							$req_qnty=number_format(($selectResult[csf('req_qny_edit')]-$total_preveious_issue),6,".","");
							if($variable_lot==1) $dyes_lot=$selectResult[csf('item_lot')]; else $dyes_lot="";
							//$stock_qty=$current_stock=number_format($stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]]['stock'],6);
							$stock_qty=number_format($current_stock=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('id')]][$dyes_lot]['stock'],6,".","");;
							
							if(number_format($req_qnty,4,".","")>0)
							{
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
                                    <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                    <td width="50" id="product_id_<? echo $i; ?>" title="<? echo $selectResult[csf("multicolor_id")]; ?>"><? echo $selectResult[csf('id')]; ?>
                                    <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                    </td>
                                    <td width="100"><p><? echo $selectResult[csf('item_lot')]; ?>
	                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" value="<? echo $selectResult[csf('item_lot')]; ?>"></p></td>
                                    <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                    <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                    </td>
                                    <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                    <td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                    <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                    <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                    
                                    <td width="70" title="<? echo $stock_qty;?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                    <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($selectResult[csf('required_qnty')],6,".",""); ?>"  onClick="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)" readonly></td>

                                    <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                    <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($req_qnty,6,".",""); ?>" readonly>
                                    <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($req_qnty,6,".",""); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)"  / >
                                    <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($req_qnty,6,".",""); ?>" readonly / >
                                    
                                    <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                    <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                     <input type="hidden" name="subreqprocessId[]" id="subreqprocessId_<? echo $i; ?>" value="<? echo $selectResult[csf('multicolor_id')]; ?>">		
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
					
					$total_issue_sql=sql_select("select a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit,b.sub_req_process_id from inv_issue_master a, dyes_chem_issue_dtls b where a.id=b.mst_id and a.entry_form=298 and a.issue_basis=7 and a.req_id='".$req_id."' and b.sub_process=$sub_process_id and b.mst_id<>$is_update");
					foreach($total_issue_sql as $row)
					{
						$total_issue_arr[$row[csf("product_id")]][$row[csf("sub_req_process_id")]]+=$row[csf("req_qny_edit")];
						$prev_issue_arr[$row[csf("issue_number")]]+=$row[csf("req_qny_edit")];
					}
					
					$req_sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty as required_qnty, b.req_qny_edit, b.multicolor_id 
					from product_details_master a, dyes_chem_issue_requ_dtls b ,pro_recipe_entry_mst c
					where a.id=b.product_id and b.mst_id='".$req_id."' and b.recipe_id=c.id and c.color_id=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 
					order by a.item_category_id ";
					//echo $req_sql;
					$total_req_sql=sql_select($req_sql);
					foreach($total_req_sql as $row)
					{
						$total_req_arr[$row[csf("id")]][$row[csf("multicolor_id")]]+=$row[csf("ratio")];
					}
					
					$sql="select a.id, t.store_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.trans_id, b.dose_base, b.ratio, recipe_qnty, adjust_percent, adjust_type, required_qnty, req_qny_edit, b.product_id, b.sub_req_process_id, t.batch_lot
					from product_details_master a, dyes_chem_issue_dtls b, inv_transaction t 
					where a.id=b.product_id and b.trans_id=t.id and b.mst_id=$is_update and b.sub_process=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 order by a.item_category_id";
					
					$i=1;
					$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
					//echo "<pre>";print_r($stock_qty_arr);die;
					$nameArray=sql_select( $sql );
					$desable_cond="";
					$desable_id=0;
					if($is_posted_account==1) { $desable_cond=" disabled";  $desable_id=1;}
					foreach ($nameArray as $selectResult)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($variable_lot==1) $dyes_lot=$selectResult[csf('batch_lot')]; else $dyes_lot="";
						
						$stock_qty=number_format($stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('id')]][$dyes_lot]['stock'],6,".","");
						$stock_qty_org=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('id')]][$dyes_lot]['stock'];
						//echo $company_id."=".$cbo_store."=".$selectResult[csf('id')]."=".$dyes_lot."<br>";
						$total_preveious_issue=($total_issue_arr[$selectResult[csf("id")]][$selectResult[csf("sub_req_process_id")]])*1;
						
						$total_requisition_qty=($total_req_arr[$selectResult[csf("id")]][$selectResult[csf("sub_req_process_id")]])*1;
						$req_qnty=number_format(($total_requisition_qty-$total_preveious_issue),6,".","");
						$update_stock=$stock_qty_org+$selectResult[csf('req_qny_edit')];
						if($issue_basis==4)
						{
							//echo $stock_qty_org.'=='.$selectResult[csf('req_qny_edit')];
							?>
							<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                </td>
                                <td width="100"><? echo $selectResult[csf('batch_lot')]; ?><p>
	                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" value="<? echo $selectResult[csf('batch_lot')]; ?>"></p></td>
                                <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                
                                <td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                
                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($selectResult[csf('recipe_qnty')],6,".",""); ?>" disabled></td>
                                <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,".",""); ?>" readonly>
                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,".",""); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $update_stock; ?>)"  <? echo $desable_cond; ?> />
                                <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,".",""); ?>" / >
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                <input type="hidden" name="subreqprocessId[]" id="subreqprocessId_<? echo $i; ?>" value="" >
                                </td>
							</tr>
						<?
						}
						if($issue_basis==7)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="50" id="product_id_<? echo $i; ?>" title="<? echo $selectResult[csf("sub_req_process_id")];?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')];?>">
                                </td>
                                 <td width="100"><? echo $selectResult[csf('batch_lot')]; ?><p>
	                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" value="<? echo $selectResult[csf('batch_lot')]; ?>"></p></td>
                                <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                
                                <td width="70"  title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($selectResult[csf('recipe_qnty')],6,".",""); ?>" onClick="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)" readonly></td>
                                
                                <td width="" align="center" id="reqn_qnty_<? echo $i; ?>" title="<?= $total_requisition_qty."=".$total_preveious_issue;?>">
                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('required_qnty')],6,".",""); ?>" readonly>
                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,".",""); ?>"  onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty+$selectResult[csf('req_qny_edit')];//$selectResult[csf('current_stock')]+$selectResult[csf('req_qny_edit')]; ?>)"   <? echo $desable_cond; ?> / >
                                <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($req_qnty,4,".",""); ?>" readonly  / >
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                 <input type="hidden" name="subreqprocessId[]" id="subreqprocessId_<? echo $i; ?>" value="<? echo $selectResult[csf('sub_req_process_id')]; ?>">	
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
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$sql="select b.id, b.sub_process as sub_process_id, a.store_id from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id and a.transaction_type=2 and b.mst_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";
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
        <table width="820" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="150">Search By</th>
                    <th width="140" align="center" id="search_by_td_up">Enter Issue No</th> 
                    <th width="140">Batch No</th>
                    <th width="130" colspan="2">Issue Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?  
                            $search_by = array(1=>'Issue No',3=>'Requisition No');
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td"><input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" /></td>
                    <td><input type="text" style="width:130px" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" /></td>    
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" /></td> 
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" /></td> 
                    <td>
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('cbo_year_selection').value, 'create_mrr_search_list_view', 'search_div', 'chemical_dyes_issue_sweater_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
                </tr>
                <tr>                  
                    <td align="center" valign="middle" colspan="6">
                        <? echo load_month_buttons(1);  ?>
                         <input type="hidden" id="hidden_issue_number" value="" />
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
exit();
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	$batch_no = str_replace("'","",$ex_data[5]);
	$yearid = str_replace("'","",$ex_data[6]);
	
	/*$batch_id_sql = sql_select("select id as batch_id from pro_batch_create_mst where batch_no like '$batch_no' and status_active=1 and is_deleted=0");
	foreach($batch_id_sql as $row)
	{
		$batch_ids[]=$row[csf('batch_id')];
	}*/
	//select recipe_no,batch_id from pro_recipe_entry_mst","recipe_no
	if($batch_no!="")
	{
		$recipe_sql ="Select a.id as batch_id, b.recipe_no, b.id from pro_bundle_batch_mst a, pro_recipe_entry_mst b where a.id=b.batch_id and b.entry_form=390 and a.batch_no like '$batch_no'";
		$recipe_sql_res=sql_select($recipe_sql);
		$recipe_arr=array();
		foreach ($recipe_sql_res as $row)
		{
			$recipe_arr[$row[csf('id')]]="'".$row[csf("id")]."'";
			$batch_ids[]=$row[csf('batch_id')];
		}
		$recipe_no=implode(',', $recipe_arr);
		if ($recipe_no != '') $recipe_cond = " and a.lap_dip_no in ($recipe_no) "; else $recipe_cond = "";
			
		$batch_cond='';
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
						if($for_cond==0) $a="and "; else $a="";
						
						$batch_cond.="$a find_in_set($id,batch_no)>0 or ";
						$for_cond++;
					}
				}
				$batch_cond=chop($batch_cond,"or ");
			}
			else $batch_cond=" and a.id<1";
		}
		else $batch_cond="";
	}
	
	$batch_arr = return_library_array("select id, batch_no from pro_bundle_batch_mst where batch_against<>0 ","id","batch_no");
	$batch_id_arr = return_library_array("select id, batch_id from pro_recipe_entry_mst","id","batch_id");
	
	//if ($batch_no != '') $batch_nocond = " and c.batch_no='$batch_no'"; else $batch_nocond = "";
		
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) $sql_cond .= " and a.issue_number_prefix_num='$txt_search_common'";	// for mrr
		else if(trim($txt_search_by)==2) $sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";// for chllan no
		else if(trim($txt_search_by)==3) $sql_cond .= " and a.req_no LIKE '%$txt_search_common%'";// for chllan no
 	} 
	if( $fromDate!="" || $toDate!="" ) 
	{ 
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		if($db_type==2 || $db_type==1)
		{ 
			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($toDate,'yyyy-mm-dd',"-",1)."'";   		}
	}
	 if($db_type==2 ) { $year_id=" extract(year from a.insert_date) as year"; }
	 if($db_type==0)  { $year_id="YEAR(a.insert_date) as year"; }
	
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	//$arr=array(1=>$company_arr,3=>$receive_basis_arr,4=>$batch_arr,5=>$receipe_arr);
	if($db_type==0) $year_cond=" and year(a.insert_date)=$yearid"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$yearid'";
//batch_id
	/*$sql = "select a.issue_number_prefix_num, a.req_no, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.batch_no, c.batch_no, a.id, $year_id, a.is_posted_account 
	 from inv_issue_master a, pro_recipe_entry_mst b, pro_bundle_batch_mst c where a.lap_dip_no=b.id and b.batch_id=c.id and b.entry_form=390 and a.company_id=$company and a.entry_form=392 $sql_cond $batch_nocond $year_cond and a.status_active=1 and a.is_deleted=0  order by a.id DESC";*/
	 
	 $sql = "select a.issue_number_prefix_num,a.req_no, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.lap_dip_no, a.issue_date, a.id, $year_id, a.is_posted_account 
	 from inv_issue_master a where a.company_id=$company and a.entry_form=392 $sql_cond $recipe_cond $year_cond and a.status_active=1 and a.is_deleted=0  order by a.id DESC";
	//echo $sql;
	$sqlR= sql_select($sql);
	?>
	<br/>
    <table align="center" cellspacing="0" width="800px"  border="1" rules="all" class="rpt_table"  >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="60">Issue No</th>
            <th width="60">Year</th>
            <th width="120">Company</th>
            <th width="100">Requisition Basis</th>
            <th width="120">Requisition No</th>
            <th width="100">Issue Purpose</th>
            <th width="140">Batch No</th>
            <th>Issue Date</th> 
        </thead>
    </table>
 	<div style="width:820px;max-height:300px; padding-left:18px; overflow-y:scroll" id="scroll_body">
        <table align="center" cellspacing="0" width="800" border="1" rules="all" class="rpt_table" id="list_view" >
            <tbody> 
            <?
            $i=1;
            foreach($sqlR as $inf)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                 $batch_id_all=explode(",",$inf[csf("lap_dip_no")]);
                 $batch_no_all="";	
                 foreach($batch_id_all as $b_id)
                 {
                    if($batch_no_all!=0)  $batch_no_all.=",".$batch_arr[$batch_id_arr[$b_id]]; else $batch_no_all=$batch_arr[$batch_id_arr[$b_id]]; 
                 }
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $inf[csf("issue_number")]."_".$inf[csf("id")]."_".$inf[csf("issue_basis")]."_".$inf[csf("issue_purpose")]."_".$inf[csf("is_posted_account")];?>")' style="cursor:pointer">
                    <td width="30" ><?php echo $i; ?></td>
                    <td width="60" align="center"><?php echo  $inf[csf("issue_number_prefix_num")]; ?></td>
                    <td width="60"align="center"><?php echo $inf[csf("year")]; ?></td>
                    <td width="120" align="center"><?php echo $company_arr[$inf[csf("company_id")]]; ?></td>
                    <td width="100" align="center"><?php echo $receive_basis_arr[$inf[csf("issue_basis")]]; ?></td>
                    <td width="120" align="center"><?php echo  $inf[csf("req_no")]; ?></td>
                    <td width="100" align="center"><?php echo $general_issue_purpose[$inf[csf("issue_purpose")]]; ?></td>
                    <td width="140" align="center"><?=$batch_no_all; //$inf[csf("batch_no")]; ?></td>
                    <td align="center"><?php echo change_date_format($inf[csf("issue_date")]); ?></td>
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
	exit();
}

if($action=="populate_data_from_data")
{
	$sql = sql_select("select location_id, issue_date, issue_basis, req_no, req_id, issue_purpose, company_id, loan_party, lap_dip_no, batch_no, order_id, style_ref, store_id, buyer_job_no, is_posted_account, lc_company from inv_issue_master where id=$data and entry_form=392");
	//echo "select location_id, issue_date, issue_basis, req_no, req_id, issue_purpose, company_id, loan_party, lap_dip_no, batch_no, order_id, style_ref, store_id, buyer_job_no, is_posted_account, lc_company from inv_issue_master where id=$data and entry_form=392";
	$all_order_id="";$all_subcon_order="";
	foreach($sql as $row)
	{
		$all_order_id.=$row[csf("order_id")].",";
	}
	$all_order_id=implode(",",array_unique(explode(",",chop($all_order_id,","))));
	
	if($all_order_id!="")
	{
		$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
		$buyer_job_sql="select b.id, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.id in ($all_order_id) ";
		//echo $buyer_job_sql;die;
		$buyer_job_result=sql_select($buyer_job_sql);
		$buyer_order_data=array();
		foreach($buyer_job_result as $row)
		{
			$buyer_order_data[$row[csf("id")]]["buyer_name"]=$buyerArr[$row[csf("buyer_name")]];
			$buyer_order_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$buyer_order_data[$row[csf("id")]]["job_no"]=$row[csf("job_no")];
			$buyer_order_data[$row[csf("id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$buyer_order_data[$row[csf("id")]]["po_number"]=$row[csf("po_number")];
		}
	}
	
	foreach($sql as $row)
	{
		if($row[csf("store_id")]=='' || $row[csf("store_id")]==0) $row[csf("store_id")]=0;else $row[csf("store_id")]=$row[csf("store_id")];
		echo "load_drop_down( 'requires/chemical_dyes_issue_sweater_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n"; 
		echo  "load_drop_down( 'requires/chemical_dyes_issue_sweater_controller', '".$row[csf("company_id")]."', 'load_drop_down_loan_party', 'loan_party_td');\n";
		echo  "load_drop_down( 'requires/chemical_dyes_issue_sweater_controller', '".$row[csf("issue_basis")]."'+'**'+'".$row[csf("company_id")]."', 'load_drop_down_store', 'store_td');\n";
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
		
		echo "document.getElementById('txt_job_no').value = '".$buyer_order_data[$row[csf("order_id")]]["job_no"]."';\n"; 
		
		echo "document.getElementById('txt_order_no').value = '".$row[csf("buyer_job_no")]."';\n"; 
		echo "document.getElementById('hidden_order_id').value = '".$row[csf("sub_order_id")]."';\n"; 
		echo "document.getElementById('cbo_store_name').value = '".$row[csf("store_id")]."';\n";
		 
        echo "document.getElementById('txt_buyer').value = '".$buyer_order_data[$row[csf("order_id")]]["buyer_name"]."';\n";
		echo "document.getElementById('txt_buyer_style').value = '".$row[csf("style_ref")]."';\n"; 
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
	$variable_lot=str_replace("'","",$variable_lot);
	//echo $txt_buyer_po.'hi';die;
	//--------------------------for check issue date with all product id's last receive date
	
	if(str_replace("'","",$cbo_issue_basis)==7)
	{
		$all_dtls_id="";
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$txt_recipe_qnty="txt_recipe_qnty_".$i;
			$txt_reqn_qnty="txt_reqn_qnty_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$transId="transId_".$i;
			$subreqprocessId="subreqprocessId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			if(str_replace("'",'',$$updateIdDtls)!="") $all_dtls_id.=str_replace("'",'',$$updateIdDtls).",";
			if(str_replace("'",'',$$txt_prod_id)!="") $all_prod_id.=str_replace("'",'',$$txt_prod_id).",";
		}
		$all_dtls_id=chop($all_dtls_id,",");
		$all_prod_id=chop($all_prod_id,",");
		$dtls_cond="";
		if($all_dtls_id!="") $dtls_cond=" and b.id not in($all_dtls_id)";
		
		$total_issue_sql=sql_select("select a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit,b.sub_req_process_id from inv_issue_master a, dyes_chem_issue_dtls b where a.id=b.mst_id and a.entry_form=392 and a.issue_basis=7 and a.req_id='".str_replace("'","",$txt_req_id)."' and b.sub_process='".str_replace("'","",$cbo_sub_process)."' $dtls_cond");
		$prev_issue=array();
		foreach($total_issue_sql as $row)
		{
			$prev_issue[$row[csf("product_id")]][$row[csf("sub_req_process_id")]]+=$row[csf("req_qny_edit")];
		}
		unset($total_issue_sql);
		$prod_cond="";
		if($all_prod_id!="") $prod_cond=" and a.id in($all_prod_id)";
		
		$sql_req=sql_select("select a.id, b.required_qnty as required_qnty, b.req_qny_edit, b.multicolor_id
		from product_details_master a, dyes_chem_issue_requ_dtls b ,pro_recipe_entry_mst c
		where a.id=b.product_id and b.recipe_id=c.id and b.mst_id='".str_replace("'","",$txt_req_id)."' and c.color_id='".str_replace("'","",$cbo_sub_process)."' and b.status_active=1 and b.is_deleted=0 and a.company_id='".str_replace("'","",$cbo_company_name)."' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 $prod_cond");
		$req_data=array();
		foreach($sql_req as $row)
		{
			$req_data[$row[csf("id")]][$row[csf("multicolor_id")]]+=$row[csf("req_qny_edit")];
		}
		//echo "10**";print_r($req_data);
		unset($sql_req);
	}
	
	for ($i=1;$i<$total_row;$i++)
	{
		$txt_prod_id="txt_prod_id_".$i;
		$txt_item_cat="txt_item_cat_".$i;
		$txt_recipe_qnty="txt_recipe_qnty_".$i;
		$txt_reqn_qnty="txt_reqn_qnty_".$i;
		$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
		$updateIdDtls="updateIdDtls_".$i;
		$transId="transId_".$i;
		$subreqprocessId="subreqprocessId_".$i;
		$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
		if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
		{
			$prod_ids.=str_replace("'",'',$$txt_prod_id).",";
			$total_prev_issue=$prev_issue[str_replace("'","",$$txt_prod_id)][str_replace("'","",$$subreqprocessId)]*1;
			$total_req_qnty=$req_data[str_replace("'","",$$txt_prod_id)][str_replace("'","",$$subreqprocessId)]*1;
			$cu_req_qnty= number_format(($total_req_qnty-$total_prev_issue),2,".","");
			$current_issue= number_format((str_replace("'","",$$txt_reqn_qnty_edit)*1),2,".","");
			if(str_replace("'","",$cbo_issue_basis)==7)
			{
				if($current_issue>$cu_req_qnty)
				{
					echo "20**Issue Quantity Not Allow Over Requisition Quantity. $current_issue = $cu_req_qnty";disconnect($con); die;
				}
			}
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
	
	//-----------------------------------------------------------------------------
	$trans_sql=sql_select("select ID, MST_ID, PROD_ID, STORE_ID, BATCH_LOT, TRANSACTION_TYPE, ITEM_CATEGORY, CONS_QUANTITY, CONS_AMOUNT, BALANCE_QNTY, BALANCE_AMOUNT, CONS_RATE, STORE_AMOUNT
 	from inv_transaction where status_active=1 and is_deleted=0 and prod_id in($prod_ids) and store_id = $issue_store_id");
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
	$dataArray = sql_select("select id, avg_rate_per_unit, current_stock, stock_value from product_details_master where item_category_id in (5,6,7,23) and id in($prod_ids)");
	foreach($dataArray as $row)
	{
		$product_arr[$row[csf("id")]]['qty']=$row[csf("current_stock")];
		$product_arr[$row[csf("id")]]['rate']=$row[csf("avg_rate_per_unit")];
		$product_arr[$row[csf("id")]]['val']=$row[csf("stock_value")];
	}
	
	$sql_store = sql_select("select ID, PROD_ID, STORE_ID, CATEGORY_ID AS CAT_ID, RATE AS AVG_RATE, CONS_QTY AS CURRENT_STOCK, AMOUNT AS STOCK_VALUE, LAST_PURCHASED_QNTY AS LAST_QTY, LOT 
	from inv_store_wise_qty_dtls 
	where prod_id in($prod_ids) and status_active=1 and is_deleted=0");
	$store_arr=array();$store_idarr=array();
	foreach($sql_store as $row)
	{
		if($variable_lot==1) $lot_no=strtoupper(trim($row["LOT"])); else $lot_no="";
		$store_arr[$row["ID"]][$lot_no]['qty']=$row["CURRENT_STOCK"];
		$store_arr[$row["ID"]][$lot_no]['avg_rate']=$row["AVG_RATE"];
		
		$store_idarr[$row["PROD_ID"]][$row[csf("store_id")]][$lot_no]['id']=$row["ID"];
		$store_wise_stock_qnty_arr[$row["PROD_ID"]][$lot_no] = $row["CURRENT_STOCK"];
	}
	
	//echo "10**<pre>";print_r($store_arr);disconnect($con); die;
	$lifoFifoArr=return_library_array("select item_category_id, store_method from variable_settings_inventory where company_name=$cbo_company_name and variable_list=17 and item_category_id in (5,6,7,23) and status_active=1 and is_deleted=0","item_category_id","store_method");
			
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$dys_che_issue_num=''; $dys_che_update_id=''; $product_id=0;
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==2 || $db_type==1) { $year_id=" extract(year from insert_date)="; }
			if($db_type==0)  { $year_id="YEAR(insert_date)="; }
			
			$new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_name,'WDCI',392,date("Y",time()) ));
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			
			
			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number,issue_basis, issue_purpose, entry_form, company_id, location_id, store_id, buyer_job_no, style_ref, req_no, req_id, issue_date, loan_party, lap_dip_no, batch_no, order_id, inserted_by, insert_date, lc_company";
			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_basis.",".$cbo_issue_purpose.",392,".$cbo_company_name.",".$cbo_location_name.",".$cbo_store_name.",".$txt_order_no.",".$txt_buyer_style.",".$txt_req_no.",".$txt_req_id.",".$txt_issue_date.",".$cbo_loan_party.",".$txt_recipe_id.",".$txt_recipe_no.",".$hidden_order_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_company_name.")";
			//$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			$dys_che_issue_num=$new_system_id[0];
			$dys_che_update_id=$id;
		}
		else
		{
			$field_array_update="issue_purpose*location_id*buyer_job_no*style_ref*req_no*req_id*issue_date*loan_party*lap_dip_no*batch_no*order_id*updated_by*update_date*lc_company";
			$data_array_update="".$cbo_issue_purpose."*".$cbo_location_name."*".$txt_order_no."*".$txt_buyer_style."*".$txt_req_no."*".$txt_req_id."*".$txt_issue_date."*".$cbo_loan_party."*".$txt_recipe_id."*".$txt_recipe_no."*".$hidden_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_name."";
			
			$dys_che_issue_num=str_replace("'","",$txt_mrr_no);
			$dys_che_update_id=str_replace("'","",$update_id);
			//$rID=sql_update("inv_issue_master", $field_array_update, $data_array_update, "id", $update_id,1); 
		}
		
		$all_prod_id_c=''; $all_prod_id_d=''; $all_prod_id_ac=''; $all_prod_ids=''; $all_store_ids='';$all_cat_ids='';$all_reqn_qnty_edit='';
		$cbo_company_name=str_replace("'",'',$cbo_company_name);
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
		$reg_issue_qty=0;//$curr_stock=0;
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$txt_lot="txt_lot_".$i;
			
			
			if(str_replace("'",'',$$txt_item_cat)==5) $all_prod_id_c.=str_replace("'",'',$$txt_prod_id).",";
			else if(str_replace("'",'',$$txt_item_cat)==6) $all_prod_id_d.=str_replace("'",'',$$txt_prod_id).",";
			else $all_prod_id_ac.=str_replace("'",'',$$txt_prod_id).",";
			
			if($variable_lot==1) $dyes_lot=trim(str_replace("'",'',$$txt_lot)); else $dyes_lot="";
			
			$prod_stock=$product_arr[str_replace("'",'',$$txt_prod_id)]['qty']*1;			
			$store_prod_qnty=$store_wise_stock_qnty_arr[str_replace("'",'',$$txt_prod_id)][strtoupper(trim($dyes_lot))]*1;
			$trans_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][strtoupper(trim($dyes_lot))]["STOCK"];
			
			if(number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($store_prod_qnty,6,'.','') || number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($prod_stock,6,'.','') || number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($trans_stock,6,'.','') )
			{
				//echo "10**".$store_prod_qnty."=".str_replace("'",'',$$txt_prod_id)."=".str_replace("'",'',$$txt_lot)."=".$dyes_lot;die;
				echo "20**Issue Quantity Not Allow Over Stock Quantity. \n Your Input Qnty = ".str_replace("'",'',$$txt_reqn_qnty_edit)." \n Store Wise Stock = ".$store_prod_qnty." \n Global Stock = ".$prod_stock."\n Transaction Wise Stock = " .$trans_stock;disconnect($con);die;
			}
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$prod_id=str_replace("'",'',$$txt_prod_id);
				$cat_id=str_replace("'",'',$$txt_item_cat);
				
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$store_stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
				$storeid=$store_idarr[$prod_id][$issue_store_id][strtoupper(trim($dyes_lot))]['id'];
				
				
				
				$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
				$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				//$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				
				if($storeid>0)
				{
					//echo "10**".$storeid."=".$prod_id."=".$issue_store_id."=".$dyes_lot."=".$store_arr[$storeid][strtoupper(trim($dyes_lot))]['avg_rate'];oci_rollback($con);disconnect($con); die;
					$reg_issue_qty=str_replace("'",'',$$txt_reqn_qnty_edit)*1;
					$curr_stock=$store_arr[$storeid][strtoupper(trim($dyes_lot))]['qty']-$reg_issue_qty;
					$s_avg_rate=$store_arr[$storeid][strtoupper(trim($dyes_lot))]['avg_rate'];
					$store_StockValue=$curr_stock*$s_avg_rate;
					$sid_arr[]=$storeid;
					$data_array_store[$storeid] =explode(",",("".$reg_issue_qty.",".$curr_stock.",".$store_StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
			}
		}
		//echo "10**";print_r($data_array_store);oci_rollback($con);disconnect($con); die;
		//echo "10**".$cbo_store_id;disconnect($con); die;		
		

		$updateID_array=array();
		$update_data=array();
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, requisition_no, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, cons_uom, cons_quantity, cons_rate, cons_amount, store_id, inserted_by, insert_date, batch_lot, store_rate, store_amount";
		
		$field_array_dtls="id, mst_id, trans_id, requ_no, batch_id, recipe_id, requisition_basis, sub_process, product_id, item_category,  recipe_qnty, required_qnty, req_qny_edit,sub_req_process_id, inserted_by, insert_date";
		$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
		$field_array_mrr="id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		
		$adcomma=1;
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_lot="txt_lot_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$txt_recipe_qnty="txt_recipe_qnty_".$i;
			$txt_reqn_qnty="txt_reqn_qnty_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$transId="transId_".$i;
			$subreqprocessId="subreqprocessId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			
			if($variable_lot==1) $dyes_lot=trim(str_replace("'",'',$$txt_lot)); else $dyes_lot="";
			
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("DYES_CHEM_ISSUE_DTLS_PK_SEQ", "dyes_chem_issue_dtls", $con);
				
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];

				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
					
				$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
				$issue_stock_value = $avg_rate*$txt_reqn_qnty_e;
				
				$trans_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][strtoupper(trim($dyes_lot))]["STOCK"];
				$trans_store_amount=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][strtoupper(trim($dyes_lot))]["STORE_AMOUNT"];
				$store_item_rate=0;
				if($trans_store_amount !=0 && $trans_stock !=0) $store_item_rate=$trans_store_amount/$trans_stock;
				$issue_store_value=$txt_reqn_qnty_e*$store_item_rate;
								
				if ($adcomma!=1) $data_array_trans .=",";
				$data_array_trans.="(".$id_trans.",".$dys_che_update_id.",".$txt_req_id.",".$cbo_issue_basis.",".$txt_req_id.",".$cbo_company_name.",".$$txt_prod_id.",".$$txt_item_cat.",2,".$txt_issue_date.",12,".$txt_reqn_qnty_e.",".$avg_rate.",".$issue_stock_value.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$txt_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
				
				if ($adcomma!=1) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$dys_che_update_id.",".$id_trans.",".$txt_req_no.",".$txt_req_id.",".$txt_recipe_id.",".$cbo_issue_basis.",".$cbo_sub_process.",".$$txt_prod_id.",".$$txt_item_cat.",".$$txt_recipe_qnty.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$$subreqprocessId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
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
							$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",392,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
							
							//for update
							$updateID_array[]=$recv_trans_id; 
							$update_data[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
							break;
						}
						else if($issueQntyBalance<0)
						{
							$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
							
							$issueQntyBalance = $txt_reqn_qnty_e-$balance_qnty;				
							$amount = $txt_reqn_qnty_e*$cons_rate;

							//for insert
							if($data_array_mrr!="") $data_array_mrr .= ",";  
							$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",392,".$$txt_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
							//for update
							$updateID_array[]=$recv_trans_id; 
							$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
							$txt_reqn_qnty_e = $issueQntyBalance;
						}
					}//end foreach
				}
				$adcomma++;
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
		
		//echo "10**".$rID.'='.$rID2.'='.$rID3.'='.$rID4.'='.$mrrWiseIssueID.'='.$upTrID.'='.$store_ID;oci_rollback($con);disconnect($con); die;
		
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
			$txt_item_cat="txt_item_cat_".$i;
			$transId="transId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$txt_lot="txt_lot_".$i;
			
			$all_transId.=str_replace("'",'',$$transId).",";
			if($variable_lot==1) $dyes_lot=trim(str_replace("'",'',$$txt_lot)); else $dyes_lot="";
			
			if(str_replace("'",'',$$txt_item_cat)==5) $all_prod_id_c.=str_replace("'",'',$$txt_prod_id).",";
			else if(str_replace("'",'',$$txt_item_cat)==6) $all_prod_id_d.=str_replace("'",'',$$txt_prod_id).",";
			else $all_prod_id_ac.=str_replace("'",'',$$txt_prod_id).",";
			
			//$all_transId.=str_replace("'",'',$$transId).",";
			$store_prod_qnty=$store_wise_stock_qnty_arr[str_replace("'",'',$$txt_prod_id)][strtoupper(trim($dyes_lot))];
			$store_c_stock=($store_prod_qnty+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
			$prod_stock=($product_arr[str_replace("'",'',$$txt_prod_id)]['qty']+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
			$trans_stock=($trans_data_arr[str_replace("'",'',$$txt_prod_id)][strtoupper(trim($dyes_lot))]["STOCK"]+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
			if(str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $store_c_stock  || str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $prod_stock || str_replace("'",'',$$txt_reqn_qnty_edit)*1 > $trans_stock)
			{
				echo "20**Issue Quantity Not Allow Over Stock Quantity";disconnect($con);die;
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
				
				$storeid=$store_idarr[$prod_id][$issue_store_id][strtoupper(trim($dyes_lot))]['id'];
				$store_stock_arr[$storeid][$prod_id][$issue_store_id][trim($dyes_lot)]['issue_qty']=$txt_reqn_qnty_edit;
				$store_stock_arr[$storeid][$prod_id][$issue_store_id][trim($dyes_lot)]['hidden_qty']=$hidtxt_reqn_qnty_edit;
				//$store_stock_arr[str_replace("'",'',$$txt_prod_id)]['hidd_qty']=str_replace("'",'',$$hidtxt_reqn_qnty_edit);
				$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
				$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				//$all_store_ids.=str_replace("'",'',$$cbo_store_name).",";
			}
		}
		
		$all_transId=chop($all_transId,",");
		$all_prod_ids=chop($all_prod_ids,",");
		$all_cat_ids=chop($all_cat_ids,",");
		
		$cbo_company_name=str_replace("'",'',$cbo_company_name);
		$stock_store_arr=fnc_store_wise_qty_operation($cbo_company_name,$issue_store_id,$all_cat_ids,$all_prod_ids,2);
		
		$storeArrId=array_unique(explode(",",$stock_store_arr));
		$prev_prod_id_arr=array();
		$transData = sql_select("select a.item_category, a.prod_id, b.issue_trans_id, a.id, a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id in($all_transId) and b.entry_form=5"); 
		foreach($transData as $row)
		{
			$adjTransDataArr[$row[csf("item_category")]][$row[csf("issue_trans_id")]].=$row[csf("id")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")]."**".$row[csf("issue_qnty")]."**".$row[csf("rate")]."**".$row[csf("amount")].",";
		}
		
		
		$updateID_array=array(); $update_data=array();
		
		$field_array_update="issue_purpose*location_id*buyer_job_no*style_ref*req_no*req_id*issue_date*loan_party*lap_dip_no*batch_no*order_id*updated_by*update_date*lc_company";
		$data_array_update="".$cbo_issue_purpose."*".$cbo_location_name."*".$txt_order_no."*".$txt_buyer_style."*".$txt_req_no."*".$txt_req_id."*".$txt_issue_date."*".$cbo_loan_party."*".$txt_recipe_id."*".$txt_recipe_no."*".$hidden_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_name."";
		//echo $data_array_update;
		$dys_che_issue_num=$txt_mrr_no;
		$dys_che_update_id=$update_id;
		
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		
		//echo "10**<pre>";print_r($store_arr);die;
		
		$up_field_array_trans="prod_id*item_category*transaction_date*cons_uom*cons_quantity*cons_rate*cons_amount*store_id*updated_by*update_date*batch_lot*store_rate*store_amount";
		$up_field_array_dtls="sub_process*product_id*item_category*recipe_qnty*required_qnty*req_qny_edit*updated_by*update_date";
		$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$user_id=$_SESSION['logic_erp']['user_id']; 
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$txt_lot="txt_lot_".$i;
			//$cbo_dose_base="cbo_dose_base_".$i;
			//$txt_ratio="txt_ratio_".$i;
			$txt_recipe_qnty="txt_recipe_qnty_".$i;
			//$txt_adj_per="txt_adj_per_".$i;
			//$cbo_adj_type="cbo_adj_type_".$i;
			$txt_reqn_qnty="txt_reqn_qnty_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$transId="transId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			
			if($variable_lot==1) $dyes_lot=trim(str_replace("'",'',$$txt_lot)); else $dyes_lot="";
			
			$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
			$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
			$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
			
			$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
			$issue_stock_value = $avg_rate*str_replace("'","",$txt_reqn_qnty_e);
			
			$current_store_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][strtoupper(trim($dyes_lot))]["STOCK"];
			$current_store_amt=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][strtoupper(trim($dyes_lot))]["STORE_AMOUNT"];
			$store_item_rate=0;
			if($current_store_amt !=0 && $current_store_stock !=0) $store_item_rate=$current_store_amt/$current_store_stock;
			$issue_store_value = $store_item_rate*$txt_reqn_qnty_e;
			
			if(str_replace("'",'',$$transId)!="")
			{
				//fnc_store_wise_qty_operation($operation,$cbo_company_name,$$cbo_store_name,$$txt_item_cat,$$txt_prod_id,$txt_reqn_qnty_e,$avg_rate,$issue_stock_value,$pc_date_time,2);
				$id_arr_trans[]=str_replace("'",'',$$transId);
				$data_array_trans[str_replace("'",'',$$transId)] =explode("*",("".$$txt_prod_id."*".$$txt_item_cat."*".$txt_issue_date."*12*".$txt_reqn_qnty_e."*".$avg_rate."*".$issue_stock_value."*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$txt_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));
			}
			
			if(str_replace("'",'',$$updateIdDtls)!="")
			{
				$id_arr_dtls[]=str_replace("'",'',$$updateIdDtls);
				$data_array_dtls[str_replace("'",'',$$updateIdDtls)] =explode(",",("".$cbo_sub_process.",".$$txt_prod_id.",".$$txt_item_cat.",".$$txt_recipe_qnty.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
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
			$cat_id=str_replace("'",'',$$txt_item_cat);
			//echo "10**<pre>";print_r($store_idarr);die;
			$storeId=$store_idarr[$prod_id][$issue_store_id][strtoupper(trim($dyes_lot))]['id'];
			//$test_data2.=$storeId."=".$prod_id."=".$issue_store_id."=".strtoupper(trim($dyes_lot))."__";
			if(str_replace("'",'',$txt_reqn_qnty_e)>0)
			{
				if($storeId>0)
				{
					$req_hidden_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][trim($dyes_lot)]['hidden_qty'];
					$reg_issue_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][trim($dyes_lot)]['issue_qty'];
					$store_curr_stock=$store_arr[$storeId][strtoupper(trim($dyes_lot))]['qty']-$reg_issue_qty+$req_hidden_qty;
					$s_avg_rate=$store_arr[$storeId][strtoupper(trim($dyes_lot))]['avg_rate'];
					$store_StockValue=$store_curr_stock*$s_avg_rate;
					$sid_arr[]=str_replace("'",'',$storeId);
					$data_array_store[str_replace("'",'',$storeId)] =explode(",",("".$reg_issue_qty.",".$store_curr_stock.",".number_format($store_StockValue,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
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
				if($issueQntyBalance>=0)
				{					
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					$amount = $txt_reqn_qnty_e*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",392,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					if(!in_array($recv_trans_id,$updateID_array))
					{
						$updateID_array[]=$recv_trans_id; 
					}
					
					$update_data[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					break;
				}
				else if($issueQntyBalance<0)
				{
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					$issueQntyBalance = $txt_reqn_qnty_e-$balance_qnty;				
					$amount = $txt_reqn_qnty_e*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",392,".$$txt_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					 
					if(!in_array($recv_trans_id,$updateID_array))
					{
						$updateID_array[]=$recv_trans_id; 
					}
					
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$txt_reqn_qnty_e = $issueQntyBalance;
				}
			}//end foreach
		}
		
		//echo "10**".$test_data;print_r($data_array_store);die;
		
		$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id in($all_transId) and entry_form=5",1);
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
		//echo "10**".bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr );disconnect($con); die;
		if(count($sid_arr)>0)
		{
		 	$store_ID=execute_query(bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr ),1);
        	if($flag==1) { if($store_ID) $flag=1; else $flag=0; } 
		}
		
		//echo "10**".bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr );disconnect($con); die;
		
		//echo "10**".$query2.'='.$rID.'='.$upTrID.'='.$upDtID.'='.$rID4.'='.$mrrWiseIssueID.'='.$store_ID;oci_rollback($con);disconnect($con); die;
		//mysql_query("ROLLBACK");
		//echo "6**".$flag;disconnect($con); die;
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
		
	}		
}

if($action=="chemical_dyes_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	
	$sql="SELECT a.id, a.issue_number, a.issue_date, a.location_id, a.issue_basis,a.issue_purpose, a.loan_party, a.req_no, a.lap_dip_no,  a.buyer_job_no, a.store_id, a.buyer_id, a.color_range, a.style_ref, a.req_id ,a.sub_order_id, a.order_id from inv_issue_master a where a.id='$data[1]' and a.company_id='$data[0]'";
	//echo $sql;
    $dataArray=sql_select($sql);
    foreach($dataArray as $row)
	{
		$all_subcon_order.=$row[csf("sub_order_id")].",";
	}
	$all_subcon_order=implode(",",array_unique(explode(",",chop($all_subcon_order,","))));
	
	
	if($all_subcon_order!="")
	{
		$subcon_po_sqls=sql_select("select a.id as po_id, a.job_no_mst, a.order_no from subcon_ord_dtls a where a.status_active=1 and a.is_deleted=0 and a.id in($all_subcon_order)");
		foreach($subcon_po_sqls as $row)
		{
			$subcon_order_data[$row[csf('po_id')]]['job_no_mst']=$row[csf('job_no_mst')];
			//$subcon_order_data[$row[csf('po_id')]]['order_no']=$row[csf('order_no')];
		}
	}

    $batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where batch_against<>0 ","id","batch_no");
	$recipe_id=$dataArray[0][csf('lap_dip_no')];
	$batch_id_all=explode(",",$dataArray[0][csf('batch_no')]);
    foreach($batch_id_all as $b_id)
	{
		if($batch_no=="") $batch_no=$batch_arr[$b_id];
		else $batch_no.=",".$batch_arr[$b_id];
	}   	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_weight_arr=return_library_array( "select id, batch_weight from  pro_batch_create_mst", "id", "batch_weight"  );
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$sql_color="select a.mst_id, a.sub_process, b.color_name from dyes_chem_issue_dtls a, lib_color b where a.sub_process=b.id and a.status_active=1 and a.mst_id='$data[1]' group by a.mst_id, a.sub_process, b.color_name";
	$sql_color_result = sql_select($sql_color);
	
	$order_id=$dataArray[0][csf('order_id')];
	if($order_id=="") $order_id=0;else $order_id=$order_id;
	$po_sqls=sql_select("Select id as po_id,grouping as ref_no,file_no,po_number from  wo_po_break_down where  status_active=1 and is_deleted=0 and id in(".$order_id.")");
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
			$sql_batch = sql_select("select  batch_weight,color_range_id, total_liquor from pro_batch_create_mst where id in(".$dataArray[0][csf('batch_no')].")  and status_active=1 and is_deleted=0"); 
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
		$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$dataArray[0][csf('lap_dip_no')].") and entry_form=300 and company_id=".$data[0]."","total_liquor");
		//$total_batch=return_field_value("sum(batch_weight) as batch_weight" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","batch_weight");
		if($recipe_id=="") $recipe_id=0; else $recipe_id=$recipe_id;
		$batch_id=return_field_value("batch_id as batch_id" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and entry_form=300","batch_id");
		if($batch_id=="") $batch_id=0; else $batch_id=$batch_id;
		$total_batch=return_field_value("sum(batch_qty) as batch_weight" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and batch_id in(".$batch_id.") and entry_form=300 and company_id=".$data[0]." ","batch_weight");
		$color_range_id=return_field_value("color_range_id as color_range_id" ,"pro_batch_create_mst","id in(".$batch_id.") and company_id=".$data[0]."","color_range_id");		
		//$color_range_id=return_field_value("color_range_id as color_range_id" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","color_range_id");
	}
	?>
	<?
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$dye_company=$dataArray[0][csf('knit_dye_company')];
	$nameArray=sql_select( "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
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
    <table width="850" cellspacing="0" align="">
        <tr>
        	<td rowspan="3" width="70">
            	<img src="../../../<? echo $image_location; ?>" height="60" width="180">
            </td>            
            <td colspan="6" align="center">
				<strong style="font-size:25px"><? echo $company_library[$data[0]] ; ?></strong><br/>
				<?	echo $nameArray[0][csf('city')]; ?><br/>
				<strong style="font-size:18px"><? echo 'Dyes & Chemical Issue Note'; ?></strong><br/><br/>
	        </td>
        </tr>
    </table>
    <table width="1100" cellspacing="0" align="">    
        <tr>
        	<td width="100"><strong>Issue ID</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="100"><strong>Issue Date</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="100"><strong>Location</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
            
        </tr>
        <tr>
        	<td width="100"><strong>Issue Basis</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $receive_basis_arr[$dataArray[0][csf('issue_basis')]]; ?></td>
        	<td width="100"><strong>Issue Purpose</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $general_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
        	<td width="100"><strong>Loan Party</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $supplier_library[$dataArray[0][csf('loan_party')]]; ?></td>
        </tr>
        <tr>
            
            <td width="100"><strong>Req. No:</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $dataArray[0][csf('req_no')]; //$req_arr[$dataArray[0][csf('req_no')]]; ?></td>
            <td width="100"><strong>Recipe No</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $dataArray[0][csf('lap_dip_no')]; ?></td>
            <td width="100"><strong>Job No</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $subcon_order_data[$dataArray[0][csf('sub_order_id')]]['job_no_mst']; ?></td>
        </tr>
        <tr>
        	<td width="100"><strong>Order No</strong></td><td width="20"><strong>:</strong></td><td width="200" style="word-break:break-all"><? echo $dataArray[0][csf('buyer_job_no')]; ?></td>
            <td width="100"><strong>Store Name</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
            <td width="100"><strong>Buyer Po</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $po_sqls[0][csf('po_number')]; ?></td>           
         </tr>
        <tr>
            <td width="100"><strong>Color</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $sql_color_result[0][csf('color_name')]; ?></td>
            <td width="100"><strong>Buyer Style</strong></td><td width="20"><strong>:</strong></td><td width="200"><? echo $dataArray[0][csf('style_ref')]; ?></td>
         </tr>
    </table>
    <br>
	<div style="width:100%;">
    <table align="" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>            
			<th width="80" >Product Id</th>            
            <th width="80" >Item Category</th>
            <th width="80" >Item Group</th>
            <th width="100" >Sub Group</th>
            <th width="170" >Item Description</th>
            <th width="50" >UOM</th>
            <th width="60" >Stock Qnty</th>
            <th width="60" >Recipe Qnty</th>
            <th width="60" >Issue Qnty</th>
        </thead>
        <tbody> 
   
	<?
 	$group_arr=return_library_array( "SELECT id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0",'id','item_name');
	
 
 
 $sql_dtls = "SELECT a.inserted_by,b.id,a.issue_number,b.store_id,
	  b.cons_uom, b.cons_quantity,b.cons_amount, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount,
	  c.item_description, c.item_group_id, c.sub_group_name, c.item_size,
	  d.sub_process, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit
	  from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
	  where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id  and b.transaction_type=2 and a.entry_form=298 and b.item_category in (5,6,7,23) and d.mst_id=$data[1]  order by d.sub_process "; 
	   //echo $sql_dtls;die;
	  $sql_result= sql_select($sql_dtls);
	  $i=1;
	if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==13) $colspan=5; else $colspan=6;

	$stock_qty_arr=fnc_store_wise_stock($data[0],$dataArray[0][csf('store_id')]);



	$inserted_by=$sql_result[0][csf('inserted_by')];



//echo "<pre">;
	//print_r($stock_qty_arr); die;
		
	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$stock_qty=$stock_qty_arr[$data[0]][$row[csf("store_id")]][$row[csf("item_category")]][$row[csf("prod_id")]]['stock'];
			//$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]]['stock'];
			$cons_quantity=$row[csf('cons_quantity')];
			$cons_quantity_sum += $stock_qty;

			$recipe_qnty=$row[csf('recipe_qnty')];
			$recipe_qnty_sum += $recipe_qnty;
			
			$req_qny_edit=$row[csf('req_qny_edit')];
			$req_qny_edit_sum += $req_qny_edit;
			
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><? echo $row[csf("prod_id")]; ?></td>               
                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
                <td><? echo $item_group_arr[$row[csf("item_group_id")]]; ?></td>
                <td><? echo $row[csf("sub_group_name")]; ?></td>
                <td><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                <td align="right"><? echo number_format($stock_qty,4); ?></td>               
                <td align="right"><? echo number_format($row[csf("recipe_qnty")],4); ?></td>                
                <td align="right"><? echo number_format($row[csf("req_qny_edit")],4); ?></td>
                
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" align="right"><strong>Total :</strong></td>
                <td align="right"><? echo number_format($cons_quantity_sum,4); ?></td>
                <td align="right"><?php echo number_format($recipe_qnty_sum,4); ?></td>
                <td align="right" ><?php echo number_format($req_qny_edit_sum,4); ?></td>
                
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
          	$cbo_template_id=$data[4];
			$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

		    echo signature_table(172, $data[0], "1100px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
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
		$sql_data=sql_select("select id, company_id, category_id, store_id, prod_id, cons_qty, rate, amount
	from  inv_store_wise_qty_dtls where  company_id=$company_id  and status_active=1 and is_deleted=0 $prod_cond $cat_cond");
	}
	else if($trans_type==1 || $trans_type==4) //Recv && Issue Return;
	{
		$sql_data=sql_select("select id, company_id, category_id, store_id, prod_id, cons_qty, rate, amount
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
function fnc_store_wise_stock($company_id,$store_id,$category='',$prod_id='')
{
	$result=sql_select("select category_id, prod_id, cons_qty, lot 
	from  inv_store_wise_qty_dtls where  company_id=$company_id and store_id=$store_id and status_active=1 and is_deleted=0");
	$stock_qty_arr=array();
	foreach($result as $row)
	{
		 $stock_qty_arr[$company_id][$store_id][$row[csf('prod_id')]][$row[csf('lot')]]['stock']=$row[csf('cons_qty')]; 
	}
	return $stock_qty_arr;
}
?>
