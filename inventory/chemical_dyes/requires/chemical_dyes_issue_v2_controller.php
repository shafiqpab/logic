<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

// user credential data prepare start
$userCredential = sql_select("SELECT store_location_id,unit_id as company_id,company_location_id,supplier_id,buyer_id FROM user_passwd where id=$user_id");
$store_location_id = $userCredential[0][csf('store_location_id')];
$company_id = $userCredential[0][csf('company_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];
$buyer_id = $userCredential[0][csf('buyer_id')];
$pack_type_array=[1=>"Bag",2=>"Dram",3=>"Carton",4=>"Jar"];
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

if($action=="com_wise_all_data")
{
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");
	
	$location_data=sql_select("select ID, LOCATION_NAME from lib_location where company_id=$data $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name");
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
	
	 $print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=304 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	$js_print_report_format_arr= json_encode($print_report_format_arr);
	
	echo $sql[0][csf("auto_transfer_rcv")]."**".$js_location_arr."**".$js_loan_party_arr."**".$js_store_arr."**".$js_print_report_format_arr;
	exit();
}


if($action=="req_wise_all_data")
{
	if ($company_id !='') {
		$company_credential_cond = " and a.company_id in($company_id)";
	}
		
	$sql = "select a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.requisition_basis, a.batch_id, a.recipe_id, a.id, a.machine_id, a.store_id, a.requisition_basis, a.company_id, sum(b.required_qnty) as requisition_qnty
	from dyes_chem_issue_requ_dtls b, dyes_chem_issue_requ_mst a 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_apply_last_update!=2 and a.entry_form in(156,259) and a.requ_no='$data' $company_credential_cond
	group by a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.requisition_basis, a.batch_id, a.recipe_id, a.id, a.machine_id, a.store_id, a.requisition_basis, a.company_id";	
	//echo "0**".$sql;die;	
	$sql_result = sql_select($sql);
	if(count($sql_result)<1)
	{
		echo "0**No Data Found";die;
	}
	$req_mst_id=$sql_result[0][csf("id")];
	if($req_mst_id>0)
	{
		$prev_req_issue="select b.REQUISITION_NO, b.CONS_QUANTITY from INV_TRANSACTION b, dyes_chem_issue_requ_mst a  
		where b.REQUISITION_NO=a.id and b.status_active=1 and b.is_deleted=0 and b.item_category in(5,6,7,23) and b.transaction_type=2 and b.RECEIVE_BASIS=7 and b.REQUISITION_NO='$req_mst_id'";
		//echo $prev_req_issue;die;
		$prev_req_issue_result = sql_select($prev_req_issue);
		$prev_req_issue_qnty=0;
		if(count($prev_req_issue_result)>0)
		{
			foreach($prev_req_issue_result as $val)
			{
				$prev_req_issue_qnty+=$val["CONS_QUANTITY"];
			}
		}
	}
	$req_qnty_balance=$sql_result[0][csf("requisition_qnty")]-$prev_req_issue_qnty;
	if($req_qnty_balance<=0)
	{
		echo "0**Requisition Balance Not Found";die;
	}
	
	$sql_variable = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = ".$sql_result[0][csf("company_id")]." and variable_list = 29 and is_deleted = 0 and status_active = 1");
	
	$recipe_arr=sql_select("select a.ID, a.COMPANY_ID from pro_recipe_entry_mst a, DYES_CHEM_ISSUE_REQU_DTLS_CHILD b, dyes_chem_issue_requ_mst c 
	where a.id=b.RECIPE_ID and b.mst_id=c.id and c.requ_no='$data'");
	$lc_comp_id=$recipe_arr[0]["COMPANY_ID"];
	unset($recipe_arr);
	
	echo "1**".$sql_result[0][csf("id")]."_".$sql_result[0][csf("requ_no")]."_".$sql_result[0][csf("batch_id")]."_".$sql_result[0][csf("recipe_id")]."_".$sql_result[0][csf("machine_id")]."_".$machine_name_arr[$sql_result[0][csf("machine_id")]]."_".$sql_result[0][csf("store_id")]."_".$lc_comp_id."_".$sql_result[0][csf("requisition_basis")]."_".$sql_result[0][csf("company_id")]."_".$sql_variable[0][csf("auto_transfer_rcv")];die;
	
}

//--------------------------------------------------------------------------------------------
if($action=="load_drop_down_dyeing_for_sub")
{
	if($data)$tag_company_con=" and c.tag_company=$data"; else $tag_company_con="";
	echo create_drop_down("cbo_dying_company", 145, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and  a.id=c.supplier_id $tag_company_con and a.status_active=1 and b.party_type=21 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if($action=="load_drop_down_dyeing")
{
	//echo "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name";die;
	echo create_drop_down( "cbo_dying_company", 145, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "reset_form('chemicaldyesissue_1','list_container_recipe_items*list_product_container','','','','txt_issue_date*cbo_dying_source*cbo_company_name*cbo_dying_company*cbo_issue_basis');company_onchange(this.value);","" );
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 145, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $company_location_credential_cond  order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 145, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "fnc_item_details(this.value,'','')",0 );  	 
	exit();
}

if ($action=="load_drop_down_loan_party")
{
	$party_sql = "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
		where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name";

	$party_sql = sql_select($party_sql);
	//$party_count=count($party_sql);
	echo create_drop_down( "cbo_loan_party", 145, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","","" );
	exit();
}

if ($action=="load_drop_down_division")
{
	$sql="select id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1";
	$result=sql_select($sql);
	$selected=0;
	if (count($result)==1) {
		$selected=$result[0][csf('id')];
	}
	
	echo create_drop_down( "cbo_division_name", 145,$sql,"id,division_name", 1, "-- Select --", 0, "load_drop_down( 'requires/chemical_dyes_issue_v2_controller', this.value, 'load_drop_down_department','department_td');" );
	die;
}

if ($action=="load_drop_down_department")
{
	echo create_drop_down( "cbo_department_name", 145,"select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/chemical_dyes_issue_v2_controller', this.value, 'load_drop_down_section','section_td');" );
	die;
}

if ($action=="load_drop_down_section")
{
	if ($data >0){
		echo create_drop_down( "cbo_section_name", 145,"select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1","id,section_name", 1, "-- Select --", $selected, "" );
	} else {
		echo create_drop_down( "cbo_section_name", 145,$blank_array,"", 1, "-- Select --", $selected, "" );
	}
	die;
}





if($action=="populate_data_lib_data")
{
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");
	echo $sql[0][csf("auto_transfer_rcv")];
	exit();
}



if($db_type==0)
{
	$machine_name_arr=return_library_array( "select id, concat(machine_no, '-', brand) as machine_name from lib_machine_name where is_deleted=0", "id", "machine_name"  );
}
else
{
	$machine_name_arr=return_library_array( "select id, machine_no || '-' || brand as machine_name from lib_machine_name where is_deleted=0", "id", "machine_name"  );
}


if($action=="req_num_list")
{
	

	$sql ="select a.id, a.requ_no, a.requ_prefix_num, a.company_id, sum(b.required_qnty) as requisition_qnty
	from dyes_chem_issue_requ_dtls b, dyes_chem_issue_requ_mst a 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_apply_last_update!=2 and a.entry_form in(156,259) and a.recipe_id!='0'
	group by a.id, a.requ_no, a.requ_prefix_num, a.company_id";
	$result_sql =sql_select($sql);
	
	$prev_req_issue=sql_select("select b.REQUISITION_NO, b.CONS_QUANTITY from INV_TRANSACTION b, dyes_chem_issue_requ_mst a  
	where b.REQUISITION_NO=a.id and b.status_active=1 and b.is_deleted=0 and b.item_category in(5,6,7,23) and b.transaction_type=2 and b.RECEIVE_BASIS=7");
	//echo $prev_req_issue;
	$prev_req_issue_qnty=array();
	foreach($prev_req_issue as $val)
	{
		$prev_req_issue_qnty[$val["REQUISITION_NO"]]+=$val["CONS_QUANTITY"];
	}
	$company_short_arr = return_library_array("select id,company_short_name from lib_company","id","company_short_name");
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="290" class="rpt_table" align="left">
        <thead>
        	<tr>
                <th colspan="4"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
            	<th width="30">SL</th>
                <th width="100">Req. No</th>
                <th width="60">WC Name</th>
                <th>Qnty</th>
            </tr>
        </thead>
    </table>
    <div style="width:290px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="left">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table" id="tbl_list_search" align="left">
        <tbody>
            	<? 
				$i=1;
				foreach($result_sql as $row)
				{  
					$prev_issue_qnty=$prev_req_issue_qnty[$row[csf('id')]];
					$req_qnty=$row[csf('requisition_qnty')];
					$req_bal=number_format($req_qnty-$prev_issue_qnty,6,'.','');
					if($req_bal>0)
					{
						if ($i%2==0)$bgcolor="#E9F3FF";						
						else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="fnc_requisition_by_scan('<? echo $row[csf('requ_no')]; ?>')">
							<td width="30" align="center" style="word-break:break-all" title="<? echo $recipe_ids; ?>"><? echo $i; ?></td>
                            <td width="100" align="center" style="word-break:break-all"><? echo $row[csf('requ_no')]; ?></td>
							<td width="60" align="center" style="word-break:break-all"><? echo $company_short_arr[$row[csf("company_id")]]; ?></td>
							<td align="right" title="<?= "previous issue=".$prev_issue_qnty."; req qnty=".$req_qnty."; req bal=".$req_bal; ?>"><? echo number_format($req_bal,6,'.',''); ?></td>
						</tr>
						<? 
						$i++;
					}
				} 
				?>
            </tbody>
    </table>
	<?
}


if($action=="req_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	//echo '<pre>';print_r($_REQUEST);
	?>
	<script>
	function js_set_value(mrr)
	{
		//alert(mrr);
		var data=mrr.split("_")
 		$("#hidden_requ_no").val(data[0]); // mrr number
		$("#hidden_requ_id").val(data[1]); // mrr number
		$("#hidden_batch_id").val(data[2]); // mrr number
		$("#hidden_receipe_id").val(data[3]); // mrr number
		$("#hidden_mc_id").val(data[4]);
		$("#hidden_mc_name").val(data[5]);
		$("#hidden_lc_company_id").val(data[6]);
		$("#hidden_store_id").val(data[7]);
		parent.emailwindow.hide();
	}
	</script>

    <input type="hidden" id="hidden_requ_no"/>
    <input type="hidden" id="hidden_requ_id"/>
    <input type="hidden" id="hidden_batch_id"/>
    <input type="hidden" id="hidden_receipe_id"/>
    <input type="hidden" id="hidden_mc_id"/>
    <input type="hidden" id="hidden_mc_name"/>
    <input type="hidden" id="hidden_lc_company_id"/>
    <input type="hidden" id="hidden_store_id"/>
    
    </head>
    
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
            	<tr>
                	<th colspan="2"> </th>
                    <th><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                  <th colspan="2"> </th>
                </tr>
                <tr>                	 
                    <th width="200">Company Name</th>
                    <th width="160" align="center" id="">Requisition No</th>
                    <th width="220">Req. Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?  
						  echo create_drop_down( "cbo_company_name", 170, "select id, company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1 );
                        ?>
                    </td>
                    <td>				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_requisition_no" id="txt_requisition_no" />	
                    </td> 
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />&nbsp;To&nbsp;
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td>
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_requisition_no').value+'_'+<?  echo $issue_purpose;  ?>+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<?  echo $issue_basis;  ?>, 'create_reqisition_search_list_view', 'search_div', 'chemical_dyes_issue_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
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
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[0];
	$issue_basis = $ex_data[7];
	$txt_requisition=$ex_data[3];
	$sql_cond="";
	$req_cond="";

	//echo $ex_data[4].'='.$issue_basis;die;
	if(trim($txt_requisition)!="")
	{
		if($ex_data[5]==1)
		{
		  	$req_cond = " and a.requ_prefix_num=$txt_requisition";
		}
		else if($ex_data[5]==4 || $ex_data[5]==0)
		{
		 	 $req_cond = " and a.requ_prefix_num LIKE '%$txt_requisition%'";	
		}
		else if($ex_data[5]==2)
		{
			 $req_cond = " and a.requ_prefix_num LIKE '$txt_requisition%'";	
		}
		else if($ex_data[5]==3)
		{
			 $req_cond = " and a.requ_prefix_num LIKE '%$txt_requisition'";	
		}
 	} 
	
	if( $fromDate!="" || $toDate!="" ) 
	{ 
		if($db_type==0){ $sql_cond .= " and a.requisition_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.requisition_date  between '".change_date_format($fromDate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($toDate,'yyyy-mm-dd',"-",1)."'";}
	
	}
	//$ex_data[6]=change_date_format(''); date('Y','a.requisition_date');

	if($db_type==0){
	   $year_cond= " and year(a.requisition_date) = $ex_data[6]";
	}else{
	   $year_cond=" and to_char(a.requisition_date, 'yyyy') = $ex_data[6]";
	}
	
	$batch_arr=array(); $batchExt_arr=array();
	$batch_data = sql_select("select ID, BATCH_NO, EXTENTION_NO from pro_batch_create_mst where COMPANY_ID=$company and batch_against<>0");
	foreach($batch_data as $row)
	{
		$batch_arr[$row["ID"]]=$row["BATCH_NO"];
		$batchExt_arr[$row["ID"]]=$row["EXTENTION_NO"];
	}
	
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	
	$arr=array(1=>$company_arr,3=>$receive_basis_arr,4=>$batch_arr,5=>$receipe_arr);

	if($db_type==0) $null_cond="''";
	else if($db_type==2) $null_cond="'0'";
	//echo $ex_data[4];die;
	if($ex_data[4]==56)
	{
		$sql ="select a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.requisition_basis, a.batch_id, a.recipe_id,a.id, a.machine_id, a.store_id, 0 as recipe_company_id, sum(b.required_qnty) as requisition_qnty 
		from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company and a.recipe_id=$null_cond and a.is_apply_last_update!=2 and a.entry_form in(156,259) $sql_cond $req_cond $year_cond and a.id not in (select req_no from inv_issue_master where status_active=1 and is_deleted=0 and entry_form=5 and company_id=$company and issue_basis=7 and issue_purpose=13  and req_no is not null )
		group by a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.requisition_basis, a.batch_id, a.recipe_id,a.id, a.machine_id, a.store_id";
		// echo $sql;die;
	}
	else
	{

		$sql ="select a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.requisition_basis, a.batch_id, a.recipe_id, a.id, a.machine_id, a.store_id, sum(b.required_qnty) as requisition_qnty
		from dyes_chem_issue_requ_dtls b, dyes_chem_issue_requ_mst a 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company and a.is_apply_last_update!=2 and a.entry_form in(156,259) and a.recipe_id!=$null_cond $sql_cond $req_cond $year_cond
		group by a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.requisition_basis, a.batch_id, a.recipe_id, a.id, a.machine_id, a.store_id";
		//echo $sql;
	}
	
	//echo $sql;die;
	$result_sql =sql_select($sql);
	$recipe_arr=sql_select("select a.ID, a.COMPANY_ID from pro_recipe_entry_mst a where a.working_company_id=$company and a.entry_form in(60,59)");
	foreach($recipe_arr as $row)
	{
		$receipe_data_arr[$row["ID"]]=$row["COMPANY_ID"];
	}
	unset($recipe_arr);
	
	$prev_req_issue=sql_select("select b.REQUISITION_NO, b.CONS_QUANTITY from INV_TRANSACTION b, dyes_chem_issue_requ_mst a  
	where b.REQUISITION_NO=a.id and b.status_active=1 and b.is_deleted=0 and b.item_category in(5,6,7,23) and b.transaction_type=2 and b.RECEIVE_BASIS=7 and b.company_id=$company $sql_cond $req_cond $year_cond");
	//echo $prev_req_issue;
	$prev_req_issue_qnty=array();
	foreach($prev_req_issue as $val)
	{
		$prev_req_issue_qnty[$val["REQUISITION_NO"]]+=$val["CONS_QUANTITY"];
	}
	
	//echo "test4=".$ex_data[4];die;
	
	if($txt_sub_process!=0)
	{
		$sub_process_cond="and c.sub_process_id=$txt_sub_process";
	}
	else
	{
		$sub_process_cond="";
	}
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="120">Company Name</th>
            <th width="80">Requisition No</th>
            <th width="100">Requisition Basis</th>
            <th width="120">Recipe</th>
            <th width="150">Batch No</th>
			<th width="70">Ext No</th>
            <th width="120">Requisition Date</th>
        </thead>
    </table>
    <div style="width:810px; overflow-y:scroll; padding-left:18px; max-height:230px; cursor:pointer" id="buyer_list_view">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table" id="tbl_list_search">
            <tbody>
            	<? 
				$i=1;
				foreach($result_sql as $row)
				{  
					$receipe_id=$row[csf('recipe_id')];
					$batch_id=explode(",",$row[csf('batch_id')]);
					$extension_no=explode(",",$row[csf("extention_no")]);
					$batch_id_all="";
					$recipe_id_all="";
					$extension_no="";	
					foreach($batch_id as $b_id)
					{
						if($batchExt_arr[$b_id]>0) $ext="".$batchExt_arr[$b_id]; else $ext="";
						if($batch_id_all!="") $batch_id_all.=",".$batch_arr[$b_id];
						else $batch_id_all=$batch_arr[$b_id];
					}
					foreach($receipe_id as $r_id)
					{
						if($recipe_id_all!="") $recipe_id_all.=",".$receipe_arr[$r_id];
						else $recipe_id_all=$receipe_arr[$r_id];
					}				
					foreach($extension_no as $e_id)
					{
					   if($extension_no!=0)  $extension_no.=",".$batch_ext_arr[$e_id]; 
					   else  $extension_no=$batch_arr[$e_id]; 
					}
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";
					$recipe_ids=array_unique(explode(",",$row[csf("recipe_id")]));
					$recipe_ids=$recipe_ids[0];
					//echo $ex_data[4].'=='.$row[csf("requisition_basis")];
					if($ex_data[4]==56 && $issue_basis==7) 
					{
						$company_id=$row[csf("company_id")];
					}
					else
					{
						$company_id=$receipe_data_arr[$recipe_ids];
					}
					$batchIds = implode(",", array_unique(explode(",", $row[csf('batch_id')])));
					$prev_issue_qnty=$prev_req_issue_qnty[$row[csf('id')]];
					$req_qnty=$row[csf('requisition_qnty')];
					$req_bal=number_format($req_qnty-$prev_issue_qnty,6,'.','');
					if($req_bal>0)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('requ_no')]."_".$row[csf('id')]."_".$batchIds."_".$row[csf('recipe_id')]."_".$row[csf('machine_id')]."_".$machine_name_arr[$row[csf('machine_id')]]."_".$company_id."_".$row[csf('store_id')]; ?>')">
							<td width="30" align="center" style="word-break:break-all" title="<? echo $recipe_ids; ?>"><? echo $i; ?></td>
							<td width="120" align="center" style="word-break:break-all"><? echo $company_arr[$row[csf("company_id")]]; ?></td>
							<td width="80" align="center" style="word-break:break-all"><? echo $row[csf('requ_prefix_num')]; ?></td>
							<td  width="100" align="center" style="word-break:break-all"><? echo $receive_basis_arr[$row[csf('requisition_basis')]]; ?></td>
							<td width="120" align="center" style="word-break:break-all"><? echo $row[csf("recipe_id")]; ?></td>
							<td width="150" align="center" style="word-break:break-all"><? echo implode(",", array_unique(explode(",", $batch_id_all))); ?></td>
							<td width="80" align="center" style="word-break:break-all"><? echo implode(",", array_unique(explode(",", $ext))); ?></td>
							<td width="120" align="center" style="word-break:break-all" title="<?= "previous issue=".$prev_issue_qnty."; req qnty=".$req_qnty."; req bal=".$req_bal; ?>"><? echo change_date_format($row[csf("requisition_date")]); ?></td>
						</tr>
						<? 
						$i++;
					}
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

if($action=="lab_dip_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $issue_basis."=".$issue_purpose;die;  
	//echo '<pre>';print_r($_REQUEST);
	?>
	<script>
	function js_set_value(mrr)
	{
		//alert(mrr);
		var data=mrr.split("_")
 		$("#hidden_requ_no").val(data[0]); // mrr number
		$("#hidden_requ_id").val(data[1]); // mrr number
		$("#hidden_lc_company_id").val(data[2]); // mrr number
		$("#hidden_store_id").val(data[3]); // mrr number
		
		parent.emailwindow.hide();
	}
	</script>

    <input type="hidden" id="hidden_requ_no"/>
    <input type="hidden" id="hidden_requ_id"/>
    <input type="hidden" id="hidden_lc_company_id"/>
    <input type="hidden" id="hidden_store_id"/>
    
    </head>
    
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
            	<tr>
                    <th colspan="3" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>                	 
                    <th width="250">Company Name</th>
                    <th width="300" align="center" id="">Lab Dip No</th>
                    <th style="display:none" width="220">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?  
						  echo create_drop_down( "cbo_company_name", 220, "select id, company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1 );
                        ?>
                    </td>
                    <td>				
                        <input type="text" style="width:220px" class="text_boxes"  name="txt_requisition_no" id="txt_requisition_no" />	
                    </td> 
                    <td style="display:none">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />&nbsp;To&nbsp;
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td>
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_requisition_no').value+'_'+<?  echo $issue_purpose;  ?>+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<?  echo $issue_basis;  ?>, 'lab_dip_popup_list_view', 'search_div', 'chemical_dyes_issue_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="4" style="display:none">
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

if($action=="lab_dip_popup_list_view")
{
	$ex_data = explode("_",$data);
	
	$company = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$txt_requisition=$ex_data[3];
	$issue_basis = $ex_data[7];
	
	$sql_cond="";
	$req_cond="";
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	//echo $ex_data[4].'='.$issue_basis;die;
	if(trim($txt_requisition)!="")
	{
		if($ex_data[5]==1)
		{
		  	$req_cond = " and c.COLOR_REF=$txt_requisition";
		}
		else if($ex_data[5]==4 || $ex_data[5]==0)
		{
		 	 $req_cond = " and c.COLOR_REF LIKE '%$txt_requisition%'";	
		}
		else if($ex_data[5]==2)
		{
			 $req_cond = " and c.COLOR_REF LIKE '$txt_requisition%'";	
		}
		else if($ex_data[5]==3)
		{
			 $req_cond = " and c.COLOR_REF LIKE '%$txt_requisition'";	
		}
 	} 
	
	$sql ="select a.ID, a.SYS_NO, a.SYS_PREFIX_NUM, a.COMPANY_ID, a.CORRECTION, a.STORE_ID, c.COLOR_REF
	from lab_color_ingredients_mst a, lab_color_ingredients_dtls b, lab_color_reference c 
	where a.id=b.mst_id and a.color_ref_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company $req_cond 
	group by a.ID, a.SYS_NO, a.SYS_PREFIX_NUM, a.COMPANY_ID, a.CORRECTION, a.STORE_ID, c.COLOR_REF
	order by a.SYS_NO desc";
	
	//echo $sql;die;
	
	//$prev_req_issue=sql_select("select b.REQUISITION_NO, b.CONS_QUANTITY from INV_TRANSACTION b, dyes_chem_issue_requ_mst a  
//	where b.REQUISITION_NO=a.id and b.status_active=1 and b.is_deleted=0 and b.item_category in(5,6,7,23) and b.transaction_type=2 and b.RECEIVE_BASIS=7 and b.company_id=$company $sql_cond $req_cond $year_cond");
//	//echo $prev_req_issue;
//	$prev_req_issue_qnty=array();
//	foreach($prev_req_issue as $val)
//	{
//		$prev_req_issue_qnty[$val["REQUISITION_NO"]]+=$val["CONS_QUANTITY"];
//	}
	$result_sql=sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="150">Company Name</th>
            <th width="150">Lab Dip No</th>
            <th width="150">System No</th>
            <th>Correction No</th>
        </thead>
    </table>
    <div style="width:810px; overflow-y:scroll; padding-left:18px; max-height:230px; cursor:pointer" id="buyer_list_view">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table" id="tbl_list_search" align="left">
            <tbody>
            	<? 
				$i=1;
				foreach($result_sql as $row)
				{ 
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";
					 
					
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row["COLOR_REF"]."_".$row["ID"]."_".$row["COMPANY_ID"]."_".$row["STORE_ID"]; ?>')">
                        <td width="50" align="center" style="word-break:break-all"><? echo $i; ?></td>
                        <td width="150" align="center" style="word-break:break-all"><? echo $company_arr[$row["COMPANY_ID"]]; ?></td>
                        <td width="150" align="center" style="word-break:break-all"><? echo $row["COLOR_REF"]; ?></td>
                        <td  width="150" align="center" style="word-break:break-all"><? echo $row["SYS_NO"]; ?></td>
                        <td align="center" style="word-break:break-all"><? echo $row["CORRECTION"]; ?></td>
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


if($action=="batch_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
    ?>
     
	<script>
	function js_set_value( data)
	{
		var data=data.split("_");
		document.getElementById('hidden_batch_id').value=data[0];
		document.getElementById('hidden_batch_no').value=data[1];
		document.getElementById('hidden_batch_weight').value=data[2];
		document.getElementById('hidden_total_liquor').value=data[3];
		document.getElementById('hidden_lc_com').value=data[4];
		parent.emailwindow.hide();
	}
	
    </script>
	</head>

	<body>
		<div align="center">
			<fieldset style="width:880px;margin-left:2px">
		        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
		                <thead>
		                	<th>Batch Company</th>
		                    <th>Search By</th>
		                    <th>Search</th>
		                    <th>
		                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
		                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
		                        <input type="hidden" name="hidden_batch_no" id="hidden_batch_no" value="">
		                         <input type="hidden" name="hidden_batch_weight" id="hidden_batch_weight" value="">
		                        <input type="hidden" name="hidden_total_liquor" id="hidden_total_liquor" value="">
								<input type="hidden" name="hidden_lc_com" id="hidden_lc_com" value="">
		                    </th> 
		                </thead>
		                <tr class="general">
		                	<td>
								<? 
									echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "" );
								?>
		                    </td>
		                    <td align="center">	
		                        <?
		                            $search_by_arr=array(1=>"Batch No",2=>"Booking No");
		                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
		                        ?>
		                    </td>                 
		                    <td align="center">				
		                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
		                    </td> 						
		                    <td align="center">
		                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_company_name').value, 'create_batch_search_list_view', 'search_div', 'chemical_dyes_issue_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
		                    </td>
		                </tr>
		            </table>
		            <div id="search_div" style="margin-top:10px"></div>
		        </form>
		    </fieldset>
		</div>  
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_batch_search_list_view")
{
	$data=explode('_',$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by =$data[1];
	$company_id =$data[2];
	
	if($company_id==0) { echo "Please Select Company";die;}
	
	if($search_by==1)
		$search_field='batch_no';
	else
		$search_field='booking_no';
		
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$com_arr=return_library_array("select id,company_short_name from lib_company" ,"id","company_short_name");
	$arr=array (0=>$com_arr, 6=>$batch_against,7=>$batch_for,9=>$color_arr);
	
	 $sql = "select id, company_id, batch_no, extention_no, batch_weight, total_liquor, batch_date, batch_against, batch_for, booking_no, color_id from pro_batch_create_mst where working_company_id=$company_id and $search_field like '$search_string' and status_active=1 and is_deleted=0 and batch_against<>0"; 
		 
	echo  create_list_view("list_view", "Company, Batch No,Ext. No,Batch Weight,Total Liquor, Batch Date,Batch Against,Batch For, Booking No, Color", "70,80,60,80,80,80,80,85,105,80","865","250",0, $sql, "js_set_value", "id,batch_no,batch_weight,total_liquor,company_id", "", 1, "company_id,0,0,0,0,0,batch_against,batch_for,0,color_id", $arr, "company_id,batch_no,extention_no,batch_weight,total_liquor,batch_date,batch_against,batch_for,booking_no,color_id", "",'','0,0,0,2,2,3,0,0,0,0');
	
    exit();	
}



if($action=="machine_item_details")
{
	$data=explode("**",$data);
	$company_id=$data[1];
	$requsn_id=$data[0];
	$is_update=$data[4];
	$issue_purpuse=$data[2];
	$cbo_store=$data[3];
	$variable_lot=trim($data[5]);
	$issue_basis=trim($data[6]);
	//echo $variable_lot.'==';  $issue_basis $issue_purpuse
	if(str_replace("'","",$cbo_store)==0 && $issue_purpuse==15)
	{
		echo "Please Select Store Name";die;
	}
	
	$total_issue_sql=sql_select("select a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit, b.sub_process 
	from inv_issue_master a, dyes_chem_issue_dtls b 
	where a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and a.status_active=1 and b.status_active=1 and a.req_no='".$requsn_id."'");
	
	foreach($total_issue_sql as $row)
	{
		$total_issue_arr[$row[csf("sub_process")]][$row[csf("product_id")]]+=$row[csf("req_qny_edit")];
		//$prev_issue_arr[$row[csf("issue_number")]]+=$row[csf("req_qny_edit")];
	}
	
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	?>
	<div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1510" class="rpt_table" align="left">
            <thead>

                <tr>

                    <th width="30">SL</th>
                    <th width="100">Sub-Porcess</th>
                    <th width="50">Product ID</th>
                    <th width="100">Lot</th>
                    <th width="100">Item Cat.</th>
                    <th width="100">Group</th>
                    <th width="80">Sub Group</th>
                    <th width="140">Item Description</th>
                    <th width="32">UOM</th>
                    <th width="70">Dose Base</th>
                    <th width="50">Ratio</th>
                    <th width="70">Stock Qty</th>
                    <th width="73">Recipe Qnty.</th>
                    <th width="55">Adj%.</th>
                    <th width="90">Adj. Type</th>
					<? if($issue_basis==4){ ?>
					<th width="80">Pack Type</th>
					<th width="80">No of Pack Qty</th>
					<? } ?>
                    <th width="90">Issue Qnty.</th>
                    <th>Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:1510px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1492" class="rpt_table" id="tbl_list_search" align="left">
                <tbody>
	                <?
					

					$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
					//print_r($stock_qty_arr);
					
					//echo "try=".$is_update."=";
	                if($is_update=="" || $is_update==0)
	                {
						$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, 1 as dose_base, b.id as dtls_id, a.current_stock, b.dose_base as dose_base_curr, b.ratio, b.recipe_qnty, b.required_qnty, b.req_qny_edit, b.item_lot as batch_lot, b.sub_process 
						from product_details_master a, dyes_chem_issue_requ_dtls b 
						where a.id=b.product_id and b.mst_id=$requsn_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and  a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 
						order by b.sub_process";
						
						//echo $sql;die;
						$i=1;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $selectResult)
						{
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$req_qnty=$selectResult[csf('req_qny_edit')]-$total_issue_arr[$selectResult[csf("sub_process")]][$selectResult[csf("id")]];

							if($variable_lot==1) $dyes_lot=$selectResult[csf('batch_lot')]; else $dyes_lot="";
							$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('id')]][$dyes_lot]['stock'];
							//echo $variable_lot.'='.$company_id.'='.$cbo_store.'='.$selectResult[csf('id')].'='.$selectResult[csf('batch_lot')].'<br>';
							//echo $selectResult[csf('id')].'='.$req_qnty.'='.$store_wise_qnty[$selectResult[csf("id")]].'<br>';  && $stock_qty>0
							if(number_format($req_qnty,6,".","")>0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                                    <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                    <td width="100" id="sub_process_<? echo $i; ?>" title="<?= $selectResult[csf('sub_process')];?>"><p><? echo $dyeing_sub_process[$selectResult[csf('sub_process')]]; ?>
                                    <input type="hidden" name="txt_sub_process_id[]" id="txt_sub_process_id_<? echo $i; ?>" value="<? echo $selectResult[csf('sub_process')]; ?>"></p> &nbsp;</td> 
                                    <td width="50" id="product_id_<? echo $i; ?>"><p><? echo $selectResult[csf('id')]; ?>
                                    <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>"></p>
                                    </td>
                                    <td width="100"><p><? echo $selectResult[csf('batch_lot')]; ?>
                                    <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" value="<? echo $selectResult[csf('batch_lot')]; ?>"></td>
                                    <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                    <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" value="<? echo $selectResult[csf('item_category_id')]; ?>"></p>
                                    </td>
                                    <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                    <td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                    <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                    <td width="32" align="center" id="uom_<? echo $i; ?>"><p><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></p></td>
                                    <td width="70" align="center" id="dose_base_<? echo $i; ?>"><p><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base_curr')],"calculate_receipe_qty($i)",1); ?></p></td>
                                    <td width="50" align="center" id="ratio_<? echo $i; ?>"><p><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo number_format($selectResult[csf('ratio')],4,".",""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></p></td>
                                    <td width="70" align="center" id="td_stock_qty_<? echo $i; ?>"><p><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($stock_qty,6,".",""); //$store_wise_qnty[$selectResult[csf("id")]]; ?>"  disabled></p></td>
                                    <td width="73" align="center" id="recipe_qnty_<? echo $i; ?>"><p><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($selectResult[csf('recipe_qnty')],6,".",""); ?>" disabled></p></td>
                                    <td width="55" align="center" id="adj_per_<? echo $i; ?>"><p><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:28px"  value="<? //echo $ratio; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></p></td>
                                    <td width="90" align="center" id="adj_type_<? echo $i; ?>"><p><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -","","calculate_receipe_qty($i)",1); ?></p></td>

									
                                    
                                    <td width="90" align="center" id="reqn_qnty_<? echo $i; ?>"><p>
                                    <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($req_qnty,6,".",""); ?>" readonly>
                                    <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                    <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                    <input type="hidden" name="stock_check[]" id="stock_check_<? echo $i; ?>" value="<? echo number_format($store_wise_qnty[$selectResult[csf("id")]],6,".",""); ?>">
                                    
                                    <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $store_wise_qnty[$selectResult[csf("id")]]+$req_qnty; ?>)"  value="<? echo number_format($req_qnty,6,".",""); ?>"></p></td>
                                    <td align="center" id="remarks_<? echo $i; ?>"><p>
                                    <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="" />
                                    <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" value="<? echo number_format($req_qnty,4,".",""); ?>" /></p>
                                    
                                    </td>								
								</tr>
								<?
								$i++;
							}
						}
	                }
	                else
	                {
						$sql="select a.id, t.store_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.trans_id, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, t.batch_lot, t.remarks, b.sub_process, b.PACK_TYPE, b.PACK_QTY  
						from product_details_master a, dyes_chem_issue_dtls b, inv_transaction t 
						where a.id=b.product_id and b.trans_id=t.id and b.mst_id=$is_update and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 
						order by b.sub_process";
						//echo $sql;
						$i=1;
						$nameArray=sql_select( $sql );
						//  echo "<pre>";
						// 	print_r($nameArray);
						//  echo "</pre>";
						foreach ($nameArray as $selectResult)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
	                            <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="100" id="sub_process_<? echo $i; ?>" title="<?= $selectResult[csf('sub_process')];?>"><p><? echo $dyeing_sub_process[$selectResult[csf('sub_process')]]; ?>
                                <input type="hidden" name="txt_sub_process_id[]" id="txt_sub_process_id_<? echo $i; ?>" value="<? echo $selectResult[csf('sub_process')]; ?>"></p> &nbsp;</td> 
	                            <td width="50" id="product_id_<? echo $i; ?>"><p><? echo $selectResult[csf('id')]; ?>
	                            <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>"></p>
	                            </td>
	                            <td width="100"><p><? echo $selectResult[csf('batch_lot')]; ?>
	                            <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" value="<? echo $selectResult[csf('batch_lot')]; ?>"></p></td>	
	                            <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?>
	                            <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" value="<? echo $selectResult[csf('item_category_id')]; ?>"></p>
	                            </td>
	                            <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
	                            <td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
	                            <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
	                            <td width="32" align="center" id="uom_<? echo $i; ?>"><p><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></p></td>
	                            <td width="70" align="center" id="dose_base_<? echo $i; ?>"><p><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1); ?></p></td>
	                            <td width="50" align="center" id="ratio_<? echo $i; ?>"><p><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo number_format($selectResult[csf('ratio')],4,".",""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></p></td>
	                            <td width="70" align="center" id="td_stock_qty_<? echo $i; ?>"><p><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($store_wise_qnty[$selectResult[csf("id")]],6,".",""); ?>"  disabled></p></td>
	                            
	                            <td width="73" align="center" id="recipe_qnty_<? echo $i; ?>"><p><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($selectResult[csf('recipe_qnty')],6,".",""); ?>" disabled></p></td>
	                            <td width="55" align="center" id="adj_per_<? echo $i; ?>"><p><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></p></td>
	                            <td width="90" align="center" id="adj_type_<? echo $i; ?>"><p><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)",1); ?></p></td>

								<? if($issue_basis==4){ ?>
									<td align="center" id="pack_type_<? echo $i; ?>" >
										<? 

											echo create_drop_down( "cbo_pack_type_$i", 80, $pack_type_array,"", 1, "-- Select --", $selectResult['PACK_TYPE'], "", "","");
											
										?>                                        
									</td> 

									<td>
										<input type="text" name="no_pack_qty" id="no_pack_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%" value="<? echo $selectResult['PACK_QTY']?>">
									</td>
								<? } ?>

	                            <td width="90" align="center" id="reqn_qnty_<? echo $i; ?>"><p>                            	
	                            <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" value="<? echo number_format($selectResult[csf('required_qnty')],6,".",""); ?>" readonly>

	                            <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,".",""); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $store_wise_qnty[$selectResult[csf("id")]]+$selectResult[csf('req_qny_edit')]; ?>)" />

	                            <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,".",""); ?>" />
	                            <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
	                            <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>"></p>	
	                            </td>
	                            <td align="center" id="remarks_<? echo $i; ?>"><p>
                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="<? echo $selectResult[csf('remarks')]; ?>" /></p>
                                </td>
							</tr>
							<?
	                        $i++;
			    	    }
	                }
	                ?>
	            </tbody>
        	</table>
        </div>
    </div>
	<?
	exit();	
}


if($action=="item_details")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$sub_process_id=$data[1];
	$receipe_id=$data[2];	
	$batch_weight=$data[3];
	$issue_purpose=$data[4];	
	$issue_basis=$data[5];
	$is_update=$data[6];
	$req_id=$data[7];
	$cbo_store=$data[8];
	$is_posted_account=$data[9];
	$variable_lot=$data[10];
	//echo $variable_lot;die;
  
	//echo $req_id;die;
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$store_arr=return_library_array( "select a.id as id,a.store_name  as store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name", "id", "store_name"  );
	?>
	<div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1510" class="rpt_table" align="left">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="100">Sub-Porcess</th>
                    <th width="50">Product ID</th>
                    <th width="100">Lot No</th>
                    <th width="100">Item Cat.</th>
                    <th width="100">Group</th>
                    <th width="80">Sub Group</th>
                    <th width="140">Item Description</th>
                    <th width="32">UOM</th>
                    <th width="70">Dose Base</th>
                    <th width="50">Ratio</th>
                    <th width="70">Stock Qty</th>
                    <th width="73">Recipe Qnty.</th>
                    <th width="55">Adj%.</th>
                    <th width="90">Adj. Type</th>
					<th width="80">Pack Type</th>
					<th width="80">No of Pack Qty</th>
                    <th width="90">Issue Qnty.</th>
                    <th>Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:1510px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1492" class="rpt_table" id="tbl_list_search" align="left">
                <tbody>
	                <?
	                if($is_update=="" || $is_update==0)
	                {
						if($issue_basis==4)
						{
							//$cbo_store
							if($issue_purpose==69)
							{
								$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.cons_qty as store_stock, b.lot, 1 as sub_process 
								from product_details_master a, inv_store_wise_qty_dtls b, lab_color_ingredients_dtls c 
								where a.id=b.prod_id and b.prod_id=c.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and c.mst_id=$req_id and a.item_category_id in(5,6,7,23) and round(b.cons_qty,6)>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
								order by a.item_category_id, a.id";
								//echo $sql;
							}
							else
							{
								$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.cons_qty as store_stock, b.lot, 1 as sub_process 
								from product_details_master a, inv_store_wise_qty_dtls b 
								where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(5,6,7,23) and round(b.cons_qty,6)>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
								order by a.item_category_id, a.id";
							}
								
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
								$stock_qty=$selectResult[csf('store_stock')];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
	                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                     <td width="100" id="sub_process_<? echo $i; ?>" title="<?= $selectResult[csf('sub_process')];?>"><p><? echo $dyeing_sub_process[$selectResult[csf('sub_process')]]; ?>
                                     <input type="hidden" name="txt_sub_process_id[]" id="txt_sub_process_id_<? echo $i; ?>" value="<? echo $selectResult[csf('sub_process')]; ?>"></p> &nbsp;</td> 
	                                <td width="50" id="product_id_<? echo $i; ?>"><p><? echo $selectResult[csf('id')]; ?>
	                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>"></p>
	                                </td>
	                                <td width="100"><p><? echo $selectResult[csf('lot')]; ?>
	                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" value="<? echo $selectResult[csf('lot')]; ?>"></p></td>
	                                <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?>
	                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" value="<? echo $selectResult[csf('item_category_id')]; ?>"></p>
	                                </td>
	                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
	                                <td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
	                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
	                                <td width="32" align="center" id="uom_<? echo $i; ?>"><p><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></p></td>
	                                <td width="70" align="center" id="dose_base_<? echo $i; ?>"><p><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1); ?></p></td>
	                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><p><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo number_format($ratio,4,".",""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></p></td>
	                                <td width="70" title="<? echo number_format($stock_qty,6,".","")?>" align="center" id="td_stock_qty_<? echo $i; ?>"><p><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($stock_qty,6,".","");//$selectResult[csf('current_stock')]; ?>"  disabled></p></td>
	                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><p><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($recipe_qnty,6,".",""); ?>" disabled></p></td>
	                                <td width="55" align="center" id="adj_per_<? echo $i; ?>"><p><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? //echo $ratio; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></p></td>

	                                <td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -","","calculate_receipe_qty($i)",1); ?></td>
									 
									<td align="center" id="pack_type_<? echo $i; ?>" >
										<? 
											
											echo create_drop_down( "cbo_pack_type_$i", 80, $pack_type_array,"", 1, "-- Select --", "", "", "","");
										?>                                        
                                    </td> 
									
									<td>
									  <input type="text" name="no_pack_qty" id="no_pack_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%" >
									</td>

	                                <td width="" align="center" id="reqn_qnty_<? echo $i; ?>"><p>
	                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" value="<? echo number_format($recipe_qnty,6,".",""); ?>" readonly>
	                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
	                                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
	                                <input type="hidden" name="stock_check[]" id="stock_check_<? echo $i; ?>" value="<? echo number_format($stock_qty,6,".","");//$selectResult[csf('current_stock')]; ?>">
	                                
	                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty; ?>)"  value="<? //echo number_format($recipe_qnty,6,".",""); ?>" >									
	                                <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" value="<? echo number_format($selectResult[csf('req_qny_edit')],6,".",""); ?>" />
	                                </p></td>

	                                <td align="center" id="remarks_<? echo $i; ?>"><p>
	                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="" /><p>
	                                </td>
								</tr>
								<?
								$i++;
							}
						}
	                }
	                else
	                {
						$sql="select a.id, t.store_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.trans_id, b.dose_base, b.ratio,recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, t.batch_lot, t.remarks, 1 as sub_process 
						from product_details_master a, dyes_chem_issue_dtls b, inv_transaction t 
						where a.id=b.product_id and b.trans_id=t.id and b.mst_id=$is_update and b.sub_process=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 
						order by a.item_category_id, a.id";
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
							if($variable_lot==1) $dyes_lot=$selectResult[csf('batch_lot')]; else $dyes_lot="";
							$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('id')]][$dyes_lot]['stock'];
							if($issue_basis==4)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
	                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                     <td width="100" id="sub_process_<? echo $i; ?>" title="<?= $selectResult[csf('sub_process')];?>"><p><? echo $dyeing_sub_process[$selectResult[csf('sub_process')]]; ?>
                                     <input type="hidden" name="txt_sub_process_id[]" id="txt_sub_process_id_<? echo $i; ?>" value="<? echo $selectResult[csf('sub_process')]; ?>"></p> &nbsp;</td>
	                                <td width="50" id="product_id_<? echo $i; ?>"><p><? echo $selectResult[csf('id')]; ?>
	                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>"></p>
	                                </td>
	                                <td width="100"><? echo $selectResult[csf('batch_lot')]; ?><p>
	                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" value="<? echo $selectResult[csf('batch_lot')]; ?>"></p></td>
	                                <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?>
	                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>"></p>
	                                </td>
	                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
	                                <td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
	                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
	                                <td width="32" align="center" id="uom_<? echo $i; ?>"><p><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></p></td>
	                                <td width="70" align="center" id="dose_base_<? echo $i; ?>"><p><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1); ?></p></td>
	                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><p><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo number_format($selectResult[csf('ratio')],4,".",""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></p></td>
	                                <td width="70" title="<? echo number_format($stock_qty,6,".","")?>" align="center" id="td_stock_qty_<? echo $i; ?>"><p><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($stock_qty,6,".","");//$selectResult[csf('current_stock')]; ?>"  disabled></p></td>
	                                
	                                <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><p><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($selectResult[csf('recipe_qnty')],6,".",""); ?>" disabled></p></td>
	                                <td width="55" align="center" id="adj_per_<? echo $i; ?>"><p><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></p></td>
	                                <td width="90" align="center" id="adj_type_<? echo $i; ?>"><p><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)",1); ?></p></td>
	                                <td width="90" align="center" id="reqn_qnty_<? echo $i; ?>"><p>
	                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('required_qnty')],6,".",""); ?>" readonly>
	                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,".",""); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty+$selectResult[csf('req_qny_edit')];//$selectResult[csf('current_stock')]+$selectResult[csf('req_qny_edit')]; ?>)"  <? echo $desable_cond; ?> />
	                                <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,".",""); ?>" />
	                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
	                                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>"></p>	
	                                </td>
	                                <td align="center" id="remarks_<? echo $i; ?>"><p>
	                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="<? echo $selectResult[csf('remarks')]; ?>" /></p>
	                                </td>
								</tr>
							    <?
							}
							
							$i++;
			    	    }
	                }
	                ?>
                </tbody>
            </table>
        </div>
    </div>           
	<?
	exit();	
}


//data save update delete here------------------------------//

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

		function change_caption(type)
		{
			if(type==1) $('#search_by_td_up').html('Enter Issue No');
			else if(type==2) $('#search_by_td_up').html('Enter Challan No');
			else if(type==3) $('#search_by_td_up').html('Enter Requisition No');
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;" >
		    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			    <table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
		            <thead>
		                <tr>                	 
		                    <th width="150">Search By</th>
		                    <th width="200" align="center" id="search_by_td_up">Enter Issue No</th>
		                    <th width="150">Batch No</th>
		                    <th width="180">Issue Date Range</th>
		                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
		                </tr>
		            </thead>
		            <tbody>
		                <tr>
		                    <td align="center">
		                        <?  
		                            $search_by = array(1=>'Issue No',2=>'Challan No',3=>'Requisition No');
									//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";

									//echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
									echo create_drop_down( "cbo_search_by", 110, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
		                        ?>
		                    </td>
		                    <td align="center">				
		                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
		                    </td>
		                    <td align="center">				
		                        <input type="text" style="width:130px" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />	
		                    </td>    
		                    <td align="center">
		                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
		                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
		                    </td> 
		                    <td align="center">
		                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_mrr_search_list_view', 'search_div', 'chemical_dyes_issue_v2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
                        <tr>
                            <th colspan="5"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
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

	$batch_cond="";
	if($batch_no!="")
	{
		$batch_id=return_field_value("id as batch_id" ,"pro_batch_create_mst","batch_no like '$batch_no' and status_active=1 and is_deleted=0","batch_id");
		if($batch_id!="")
		{
            if($ex_data[7]==1)
            {
                $batch_cond=" and a.batch_no = $batch_id";
            }
            else if($ex_data[7]==4 || $ex_data[7]==0)
            {
                $batch_cond=" and a.batch_no like '%$batch_id%'";
            }
            else if($ex_data[7]==2)
            {
                $batch_cond=" and a.batch_no like '$batch_id%'";
            }
            else if($ex_data[7]==3)
            {
                $batch_cond=" and a.batch_no like '%$batch_id'";
            }
		}
	}
	
	if(trim($txt_search_common)!="")
	{
        if($ex_data[7]==1)
        {
            $req_cond = " = '$txt_search_common'";

        }
        else if($ex_data[7]==4 || $ex_data[7]==0)
        {
            $req_cond = " LIKE '%$txt_search_common%' ";
        }
        else if($ex_data[7]==2)
        {
            $req_cond = " LIKE '$txt_search_common%' ";
        }
        else if($ex_data[7]==3)
        {
            $req_cond = " LIKE '%$txt_search_common' ";
        }

		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.issue_number $req_cond";
			
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no $req_cond";
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
	
	// $batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where company_id=$company and batch_against<>0 ","id","batch_no");
	$batch_arr=array(); $batch_ext_arr=array();
   $sql_batch= sql_select("select id,batch_no,extention_no from pro_batch_create_mst where company_id=$company and batch_against<>0");
   foreach( $sql_batch as $row){
	$batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
	$batch_ext_arr[$row[csf("id")]]=$row[csf("extention_no")];
   }
	$company_arr = return_library_array("select id,company_name from lib_company where id=$company","id","company_name");
	$arr=array(1=>$company_arr,3=>$receive_basis_arr,4=>$batch_arr,5=>$receipe_arr);
		if($db_type==0) $year_cond=" and year(a.insert_date)=$yearid"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$yearid'";
	if(trim($txt_search_common) !="" && trim($txt_search_by)==3)
	{
		$sql = sql_select("select b.requ_no, a.issue_number_prefix_num, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.batch_no, a.issue_date, a.id, $year_id,
		 a.is_posted_account 
		 from inv_issue_master a, dyes_chem_issue_requ_mst b 
		 where a.req_no=b.id and a.company_id=$company and a.entry_form=5 and a.is_multi=2 and a.issue_basis=7 and a.status_active=1 and a.is_deleted=0 and b.requ_no $req_cond $sql_cond  $batch_cond $year_cond 
		 group by b.requ_no, a.issue_number_prefix_num, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.batch_no, a.issue_date, a.id, a.insert_date, a.is_posted_account
		 order by a.id desc");
	}else{
		$sql = sql_select("select b.requ_no, a.issue_number_prefix_num, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.batch_no, a.issue_date, a.id, $year_id,
		 a.is_posted_account 
		 from inv_issue_master a left join dyes_chem_issue_requ_mst b on b.id = a.req_no
		 where a.company_id=$company and a.entry_form=5 and a.is_multi=2 and a.status_active=1 and a.is_deleted=0 $sql_cond  $batch_cond $year_cond order by a.id desc");
	}
	?>
	<br/>
    <table align="center" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table"  >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th width="30">SL</th>
                <th width="60" >Issue No</th>
                <th width="60" >Year</th>
                <th width="150" >Company</th>
                <th width="100" >Issue Basis</th>
                <th width="150" >Req. No.</th>
                <th width="100" >Issue Purpose</th>
                <th width="100" >Batch No</th>
				<th width="80" >Ext No</th>
                <th width="90" >Issue Date</th>
            </tr>
        </thead>
    </table>
    <div style="width:950px;max-height:300px; overflow-y:scroll" id="scroll_body">
	 	<table align="center" cellspacing="0" width="920"  border="1" rules="all" class="rpt_table"  id="list_view" >
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
					if($batch_ext_arr[$b_id]>0) $ext="".$batch_ext_arr[$b_id]; else $ext="";
					if($batch_no_all!=0)  $batch_no_all.=",".$batch_arr[$b_id]; 
					else  $batch_no_all=$batch_arr[$b_id]; 
				 }

				 $extension_no=explode(",",$inf[csf("extention_no")]);
				 $extension_no="";	
				 foreach($extension_no as $e_id)
				 {
					if($extension_no!=0)  $extension_no.=",".$batch_ext_arr[$e_id]; 
					else  $extension_no=$batch_arr[$e_id]; 
				 }
			?>
	         	<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $inf[csf("issue_number")]."_".$inf[csf("id")]."_".$inf[csf("issue_basis")]."_".$inf[csf("issue_purpose")]."_".$inf[csf("is_posted_account")];?>")' style="cursor:pointer">
	                <td width="30" align="center"><?php echo $i; ?></td>
	                <td width="60" align="center"><?php echo  $inf[csf("issue_number_prefix_num")]; ?></td>
	                <td width="60" align="center"><?php echo $inf[csf("year")]; ?></td>
	                <td width="150" align="center"><?php echo $company_arr[$inf[csf("company_id")]]; ?></td>
	                <td width="100" align="center"><?php echo $receive_basis_arr[$inf[csf("issue_basis")]]; ?></td>
                    <td width="150" align="center"><?= $inf[csf("issue_basis")] == 7 ? $inf[csf("REQU_NO")] : '' ?></td>
                    <td width="100" align="center"><?php echo $general_issue_purpose[$inf[csf("issue_purpose")]]; ?></td>
	                <td width="100" align="center"><p><?php echo $batch_no_all; ?></p></td>
					<td width="80" align="center"><p><?php echo $ext; ?></p></td>
	                <td width="90" align="center"><?php echo change_date_format($inf[csf("issue_date")]); ?></td>
	               
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


if($action=="populate_batch_receipe_data")
{
	$data=explode("**",$data);
	//echo "select sum(total_liquor) as total_liquor from pro_recipe_entry_mst where id in($data[2]) and entry_form=59 and working_company_id=$data[0]";die;
	$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in($data[2]) and entry_form=59 and working_company_id=$data[0]","total_liquor");
	$sql_data=sql_select("select id, entry_form, batch_id, total_liquor, new_batch_weight,batch_qty from pro_recipe_entry_mst where id in(".$data[2].")");
	$total_recipe_batch=0;
	foreach($sql_data as $row)
	{
		if($row[csf('entry_form')]==60)
		{
			$total_recipe_batch+=$row[csf('new_batch_weight')];
		}
		if($row[csf('entry_form')]==59)
		{
			$total_recipe_batch+=$row[csf('batch_qty')];
		}
		else
		{
			$total_recipe_batch+=$row[csf('batch_qty')];
		}
		
	}
	if($db_type==0)//extention_no
	{
		$sql = sql_select("select sum(batch_weight) as batch_weight, group_concat(batch_no) as batch_no from pro_batch_create_mst 
		where id in ($data[1]) and status_active=1 and is_deleted=0 and batch_against<>0"); 
	}
	if($db_type==2)
	{
		$sql = sql_select("select sum(batch_weight) as batch_weight, listagg(cast(batch_no as varchar2(500)),',') within group (order by batch_no) as batch_no, listagg(cast(extention_no as varchar2(500)),',') within group (order by extention_no) as extention_no from pro_batch_create_mst 
		where id in ($data[1]) and status_active=1 and is_deleted=0 and batch_against<>0"); 
	}
	
	foreach($sql as $row)
	{
		echo "document.getElementById('txt_batch_no').value = '".$row[csf("batch_no")]."';\n"; 
		echo "document.getElementById('txt_batch_weight').value = '".number_format($total_recipe_batch,6,'.','')."';\n"; 
		echo "document.getElementById('txt_ext_no').value = '".$row[csf("extention_no")]."';\n"; 
	}
	echo "document.getElementById('txt_tot_liquor').value = '".$total_liquor."';\n";
	
	
	$buyer_id=return_field_value("buyer_id","pro_recipe_entry_mst","working_company_id=$data[0] and id in($data[2]) and entry_form=59 and rownum=1","buyer_id");
	$sql = sql_select("select a.entry_form, a.is_sales, a.color_range_id, a.booking_without_order, a.sales_order_id, a.sales_order_no, b.po_id
	from pro_batch_create_mst a, pro_batch_create_dtls b 
	where a.id in($data[1]) and a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and a.batch_against<>0");
	
	$sql_result = sql_select($sql);
	$is_sales_id=$sql_result[0][csf("is_sales")];
	$batch_entry_form=$sql_result[0][csf("entry_form")];
	$bookint_type=$sql_result[0][csf("booking_without_order")];
	$color_range_id=$po_row[csf("color_range_id")];
	
	$po_number=$file_no=$ref_no="";
	if($is_sales_id==1)
	{
		$sales_order_id=$sales_order_no="";
		foreach($sql_result as $po_row)
		{
			$sales_order_id.=$po_row[csf("sales_order_id")].",";
			$sales_order_no.=$po_row[csf("sales_order_no")].","; 
		}
	}
	else
	{
		foreach($sql_result as $po_row)
		{
			$all_po_id_arr[$po_row[csf("po_id")]]=$po_row[csf("po_id")]; 
		}
		
		if($batch_entry_form==36)
		{
			$po_sqls=sql_select("select id, order_no from subcon_ord_dtls where status_active=1 and id in(".implode(",",$all_po_id_arr).")");
			foreach($po_sqls as $row)
			{
				$po_number.=$po_row[csf("order_no")].",";
			}
		}
		else
		{
			$po_sqls=sql_select("Select id as po_id, grouping as ref_no ,file_no, po_number from wo_po_break_down where status_active=1 and id in(".implode(",",$all_po_id_arr).")");
			foreach($po_sqls as $row)
			{
				$po_number.=$po_row[csf("po_number")].",";
				$file_no.=$po_row[csf("file_no")].",";
				$ref_no.=$po_row[csf("ref_no")].",";
			}
		}
	}
	
	$po_id=implode(",",$all_po_id_arr);
	
	$sales_order_id=chop($sales_order_id,",");
	$sales_order_no=chop($sales_order_no,",");
	$po_number=chop($po_number,",");
	$file_no=chop($file_no,",");
	$ref_no=chop($ref_no,",");
	
	
	if($is_sales_id==1)
	{
		echo "document.getElementById('txt_order_no').value = '".trim($sales_order_no)."';\n";
		echo "document.getElementById('hidden_order_id').value = '".$sales_order_id."';\n";
	}
	else
	{
		echo "document.getElementById('txt_order_no').value = '".trim($po_number)."';\n";
		echo "document.getElementById('hidden_order_id').value = '".$po_id."';\n";
	}
	
	//echo "document.getElementById('txt_order_no').value = '".trim($po_number)."';\n";
	echo "document.getElementById('txt_color_range').value = '".$color_range[$color_range_id]."';\n";
	echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
	echo "document.getElementById('txt_ref_no').value = '".$ref_no."';\n";
	
	echo "document.getElementById('hidden_booking_type').value = '".$bookint_type."';\n";
	echo "document.getElementById('cbo_buyer_name').value = '".$buyer_id."';\n";
}





//for machine wash data
if($action=="populate_machine_wish_data")
{
	//$data=explode("**",$data);
	//$sql_machine_data=sql_select("select machine_id, tot_liquor from dyes_chem_issue_requ_mst where company_id=$data[0] and id=$data[1] and status_active=1 and is_deleted=0");
	$sql_machine_data=sql_select("select machine_id, tot_liquor from dyes_chem_issue_requ_mst where id=$data and status_active=1 and is_deleted=0 and entry_form=156");
	foreach($sql_machine_data as $value)
	{
		//$machine_name=return_field_value("machine_no","lib_machine_name","id=".$value[csf('machine_id')]);
		$machine_name=$machine_name_arr[$value[csf("machine_id")]];
		echo "document.getElementById('txt_machine_name').value = '".$machine_name."';\n";
		echo "document.getElementById('txt_tot_liquor').value = '".$value[csf("tot_liquor")]."';\n";	
		echo "document.getElementById('txt_machine_id').value = '".$value[csf("machine_id")]."';\n";
		
	}
}

if($action=="populate_data_from_data")
{
	$sql = sql_select("select location_id, issue_date, issue_basis, req_no ,machine_id, batch_no, issue_purpose, company_id, loan_party, knit_dye_source, knit_dye_company, challan_no, lap_dip_no, order_id, buyer_id, style_ref, store_id, buyer_job_no, is_posted_account, remarks, division_id, department_id, section_id, attention, lc_company, issue_category
	from inv_issue_master where id=$data and entry_form=5");
	// echo "<pre>";
	// 	print_r($sql);
	// echo "</pre>";
	$all_batch_id_arr=$recipe_id_arr=$all_po_id=array();
	foreach($sql as $row)
	{
		$batch_id_all=explode(",",$row[csf("batch_no")]);
		foreach($batch_id_all as $b_id)
		{
			if($b_id) $all_batch_id_arr[$b_id]=$b_id;
		}
		
		$recipe_id_all=explode(",",$row[csf("lap_dip_no")]);
		foreach($recipe_id_all as $r_id)
		{
			if($r_id) $recipe_id_arr[$r_id]=$r_id;
		}
		
		$po_id_arr=array_unique(explode(",",$row[csf("order_id")]));
		foreach($po_id_arr as $p_id)
		{
			if($p_id) $all_po_id[$p_id]=$p_id;
		}
	}
	
	
	$array_data=sql_select("select id, entry_form, batch_id, total_liquor, new_batch_weight,batch_qty from pro_recipe_entry_mst where id in(".$row[csf("lap_dip_no")].")");
			$batch_new_qty=0;
			foreach($array_data as $re_val)
			{
				if($re_val[csf("entry_form")]==60)
				{
					$batch_new_qty+=$re_val[csf("new_batch_weight")];
				}
				if($re_val[csf("entry_form")]==59) //New Add from Recipe page
				{
					$batch_new_qty+=$re_val[csf("batch_qty")];
				}
			}
	if(count($recipe_id_arr)>0)
	{
		$po_sqls=sql_select("select id, entry_form, batch_id, total_liquor, new_batch_weight,batch_qty from pro_recipe_entry_mst where status_active=1 and is_deleted=0 and id in(".implode(",",$recipe_id_arr).")");
		$recipe_data_arr=array();
		foreach($po_sqls as $row)
		{
			$recipe_data_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
			$recipe_data_arr[$row[csf('id')]]['batch_id']=$row[csf('batch_id')];
			$recipe_data_arr[$row[csf('id')]]['total_liquor']=$row[csf('total_liquor')];
			$recipe_data_arr[$row[csf('id')]]['new_batch_weight']=$row[csf('new_batch_weight')];
			$recipe_data_arr[$row[csf('id')]]['batch_qty']=$row[csf('batch_qty')];
		}
	}
	if(count($all_po_id)>0)
	{
		$po_sqls=sql_select("Select id as po_id,grouping as ref_no,file_no from  wo_po_break_down where status_active=1 and is_deleted=0 and id in(".implode(",",$all_po_id).")");
		$po_data_arr=array();
		foreach($po_sqls as $row)
		{
			$po_data_arr[$row[csf('po_id')]]['ref']=$row[csf('ref_no')];
			$po_data_arr[$row[csf('po_id')]]['file']=$row[csf('file_no')];
		}
	}
	
	if(count($all_batch_id_arr)>0)
	{
		$batch_sql=sql_select("select id, color_range_id, extention_no, batch_no, batch_weight, total_liquor from pro_batch_create_mst where status_active=1 and is_deleted=0 and id in(".implode(",",$all_batch_id_arr).")");
		$all_batch_data=array();
		foreach($batch_sql as $row)
		{
			$all_batch_data[$row[csf("id")]]["color_range_id"]=$row[csf("color_range_id")];
			$all_batch_data[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
			$all_batch_data[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
			$all_batch_data[$row[csf("id")]]["batch_weight"]=$row[csf("batch_weight")];
			$all_batch_data[$row[csf("id")]]["total_liquor"]=$row[csf("total_liquor")];
		}
	}
	
	foreach($sql as $row)
	{
		$knit_dye_source=$row[csf("knit_dye_source")];
		$machine_name_data='';
		$machine_id_id_array=explode(",",$row[csf("machine_id")]);
		foreach($machine_id_id_array as $val)
		{
			if($machine_name_data=="") $machine_name_data=$machine_name_arr[$val]; else $machine_name_data.="*".$machine_name_arr[$val];
		}
		
		echo "document.getElementById('txt_issue_date').value = '".change_date_format($row[csf("issue_date")],"dd-mm-yyyy","-")."';\n"; 
		echo "document.getElementById('cbo_issue_basis').value = '".$row[csf("issue_basis")]."';\n";  
		$req_no=return_field_value("requ_no as requ_no" ,"dyes_chem_issue_requ_mst","id in(".$row[csf("req_no")].") and entry_form in(156,259)","requ_no");
		echo "document.getElementById('txt_req_no').value = '".$req_no."';\n"; 
		echo "document.getElementById('txt_req_id').value = '".$row[csf("req_no")]."';\n"; 
		echo "document.getElementById('txt_batch_id').value = '".$row[csf("batch_no")]."';\n";
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("lc_company")]."';\n"; 
		 
		echo "document.getElementById('cbo_issue_purpose').value = '".$row[csf("issue_purpose")]."';\n";
		echo "document.getElementById('cbo_issue_category').value = '".$row[csf("issue_category")]."';\n";
		
		if($row[csf("store_id")]=='' || $row[csf("store_id")]==0) $row[csf("store_id")]=0;else $row[csf("store_id")]=$row[csf("store_id")];  
		echo "document.getElementById('cbo_store_name').value = '".$row[csf("store_id")]."';\n"; 
		
		echo "document.getElementById('cbo_loan_party').value = '".$row[csf("loan_party")]."';\n"; 
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_id")]."';\n";  
		echo "document.getElementById('cbo_dying_source').value = '".$row[csf("knit_dye_source")]."';\n";
		 
		echo "set_dying_company(".$row[csf("knit_dye_source")].");\n";
		echo "document.getElementById('cbo_dying_company').value = '".$row[csf("knit_dye_company")]."';\n"; 
		echo "document.getElementById('txt_challan_no').value = '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_return').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "load_drop_down( 'requires/chemical_dyes_issue_v2_controller', '".$row[csf("lc_company")]."', 'load_drop_down_division','division_td');\n";
		echo "document.getElementById('cbo_division_name').value			= '".$row[csf("division_id")]."';\n";
		echo "load_drop_down( 'requires/chemical_dyes_issue_v2_controller', document.getElementById('cbo_division_name').value, 'load_drop_down_department','department_td');\n";
		echo "document.getElementById('cbo_department_name').value		= '".$row[csf("department_id")]."';\n";
		echo "load_drop_down( 'requires/chemical_dyes_issue_v2_controller', document.getElementById('cbo_department_name').value, 'load_drop_down_section','section_td');\n";
		echo "document.getElementById('cbo_section_name').value			= '".$row[csf("section_id")]."';\n";
		
		echo "document.getElementById('txt_machine_id').value 			= '".$row[csf("machine_id")]."';\n";
		echo "document.getElementById('txt_machine_name').value = '".$machine_name_data."';\n";  
			
		echo "document.getElementById('txt_recipe_no').value = '".$row[csf("lap_dip_no")]."';\n"; 
		echo "document.getElementById('txt_recipe_id').value = '".$row[csf("lap_dip_no")]."';\n";  
       // echo "document.getElementById('txt_tot_liquor').value = '".$liquior_arr[$row[csf("lap_dip_no")]]."';\n"; 
		echo "document.getElementById('txt_order_no').value = '".$row[csf("buyer_job_no")]."';\n"; 
		echo "document.getElementById('hidden_order_id').value = '".$row[csf("order_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n"; 
		echo "document.getElementById('txt_style_no').value = '".$row[csf("style_ref")]."';\n"; 
		echo "document.getElementById('update_for_cost').value = '1';\n"; 
		//echo "$('#txt_req_no').attr('disabled',true);\n"; 
		
		if(str_replace("'","",$row[csf("is_posted_account")])==1)
		{
			$msg="Already Posted in Accounts";
			echo "disable_enable_fields( 'cbo_company_name*cbo_issue_basis*txt_issue_date*txt_challan_no*cbo_dying_source*cbo_dying_company*txt_req_no', 1, '', '' );\n"; // disable true
			
		}
		else
		{
			$msg="";
			//echo "$('#cbo_issue_basis').attr('disabled',false);\n";
			echo "$('#txt_issue_date').attr('disabled',true);\n";
			echo "$('#txt_challan_no').attr('disabled',false);\n";
			echo "$('#cbo_dying_source').attr('disabled',false);\n";
			//echo "$('#cbo_sub_process').attr('disabled',false);\n";
			echo "$('#cbo_dying_company').attr('disabled',false);\n";
		}
		echo "$('#posted_account_td').text('".$msg."');\n";
		
		
		
		if(trim($row[csf("issue_basis")])==7)
		{
			$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$row[csf("lap_dip_no")].") and entry_form=59 and company_id=".$row[csf("company_id")]."","total_liquor");
			echo "load_drop_down( 'requires/chemical_dyes_issue_v2_controller', '".$row[csf("company_id")]."**".$row[csf("lap_dip_no")]."**".$row[csf("is_posted_account")]."', 'load_drop_down_sub_process', 'sub_process_td');\n"; 
			
			$recipe_id_all=explode(",",$row[csf("lap_dip_no")]);
			$batch_new_qty=0;
			foreach($recipe_id_all as $re_id)
			{
				if($recipe_data_arr[$re_id]['entry_form']==60)
				{
					$batch_new_qty+=$recipe_data_arr[$re_id]['new_batch_weight'];
				}
				if($recipe_data_arr[$re_id]['entry_form']==59) //New Add from Recipe page
				{
					$batch_new_qty+=$recipe_data_arr[$re_id]['batch_qty'];
				}
			}
			echo "document.getElementById('txt_tot_liquor').value = '".$total_liquor."';\n"; 
			echo "document.getElementById('txt_batch_weight').value = '".number_format($batch_new_qty, 6, ".", "")."';\n"; 
		}
		
		$batch_id_all=explode(",",$row[csf("batch_no")]);
		$batch_weitht=$tot_liqure=0; $color_range_name=$batch_no=$ext_no="";
		foreach($batch_id_all as $b_id)
		{
			$batch_weitht+=$all_batch_data[$b_id]["batch_weight"];
			$tot_liqure+=$all_batch_data[$b_id]["total_liquor"];
			if($color_range_name=="") $color_range_name=$color_range[$all_batch_data[$b_id]["color_range_id"]]; else $color_range_name.=",".$color_range[$all_batch_data[$b_id]["color_range_id"]];
			if($batch_no=="") $batch_no=$all_batch_data[$b_id]["batch_no"]; else $batch_no.=",".$all_batch_data[$b_id]["batch_no"];
			if($ext_no=="") $ext_no=$all_batch_data[$b_id]["extention_no"]; else $ext_no.=",".$all_batch_data[$b_id]["extention_no"];
			
		}
		
		echo "document.getElementById('txt_batch_weight').value = '".number_format($batch_weitht,6,'.','')."';\n"; 
		echo "document.getElementById('txt_tot_liquor').value = '".$tot_liqure."';\n"; //
		echo "document.getElementById('txt_batch_no').value = '".$batch_no."';\n";
		echo "document.getElementById('txt_ext_no').value = '".$ext_no."';\n";
		echo "document.getElementById('txt_color_range').value = '".$color_range_name."';\n"; 
		
		$po_id=array_unique(explode(",",$row[csf("order_id")]));
		$file_no='';$ref_no='';$color_range_arr='';$ext_arr="";
		foreach($po_id as $poId)
		{
			if($file_no!='') $file_no.=",".$po_data_arr[$poId]['file']; else  $file_no=$po_data_arr[$poId]['file'];	
			if($ref_no!='') $ref_no.=",".$po_data_arr[$poId]['ref'].","; else  	$ref_no=$po_data_arr[$poId]['ref'];	
		}
		
		echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
		echo "document.getElementById('txt_ref_no').value = '".$ref_no."';\n";
		
	}
	exit();
}

if($action=="save_update_delete")
{	  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	$cbo_division_name=0; $cbo_department_name=0; $cbo_section_name=0;
	
	if(str_replace("'","",$cbo_issue_basis)==7)
	{
		if(str_replace("'","",$update_id)>0) $up_mst_cond=" and a.id <> $update_id";
		$previous_entry="select b.SUB_PROCESS, b.PRODUCT_ID, c.BATCH_LOT, c.ID AS TRANS_ID, c.CONS_QUANTITY 
		from INV_ISSUE_MASTER a, DYES_CHEM_ISSUE_DTLS b, INV_TRANSACTION c 
		where a.id=b.mst_id and b.trans_id = c.id and a.id=c.mst_id and c.transaction_type=2 and c.item_category in(5,6,7,23) and a.REQ_NO=$txt_req_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $up_mst_cond";
		$previous_entry_result=sql_select($previous_entry);
		$prev_entry_data=array();
		foreach($previous_entry_result as $val)
		{
			if($trans_check[$val["TRANS_ID"]]=="")
			{
				$trans_check[$val["TRANS_ID"]]=$val["TRANS_ID"];
				$prev_entry_data[$val["SUB_PROCESS"]][$val["PRODUCT_ID"]][$val["BATCH_LOT"]]+=$val["CONS_QUANTITY"];
			}
		}
		unset($previous_entry_result);
		$sql_reqsn=sql_select("select PRODUCT_ID, RECIPE_QNTY, SUB_PROCESS, ITEM_LOT, REQ_QNY_EDIT from dyes_chem_issue_requ_dtls where MST_ID=$txt_req_id and STATUS_ACTIVE=1");
		
		foreach($sql_reqsn as $row)
		{
			$req_qnty_arr[$row["SUB_PROCESS"]][$row["PRODUCT_ID"]][$row["ITEM_LOT"]]=$row["REQ_QNY_EDIT"]-$prev_entry_data[$row["SUB_PROCESS"]][$row["PRODUCT_ID"]][$row["ITEM_LOT"]];
		}
		unset($sql_reqsn);
		//echo "10**test3";oci_rollback($con);disconnect($con);die;
	}
	
	//--------------------------for check issue date with all product id's last receive date
	for ($i=1;$i<$total_row;$i++)
	{
		$txt_prod_id="txt_prod_id_".$i;
		$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
		$txt_recipe_qnty="txt_recipe_qnty_".$i;
		$txt_lot="txt_lot_".$i;
		$cbo_sub_process="txt_sub_process_id_".$i;
		
		if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
		{
			$prod_ids.=str_replace("'",'',$$txt_prod_id).",";
			if(str_replace("'","",$cbo_issue_basis)==7)
			{
				if( number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($req_qnty_arr[str_replace("'",'',$$cbo_sub_process)][str_replace("'",'',$$txt_prod_id)][str_replace("'",'',$$txt_lot)],6,'.',''))
				{
					echo "20** Issue Quantity Not Allow Over Requisition Quantity. \n Issue Quantity =  ".number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','')." \n Requisition Quantity =  ".number_format($req_qnty_arr[str_replace("'",'',$$cbo_sub_process)][str_replace("'",'',$$txt_prod_id)][str_replace("'",'',$$txt_lot)],6,'.','')." ";
					disconnect($con);die;
				}
			}
		}
	}        
	$prod_ids =  chop($prod_ids,",");
	
	
	$variable_lot=str_replace("'","",$variable_lot);
	$issue_store_id=str_replace("'","",$cbo_store_name);
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in($prod_ids) and store_id = $issue_store_id and transaction_type in (1,4,5) and status_active = 1 and is_deleted = 0", "max_date");   
	if($max_recv_date != "")
	{
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
		if ($issue_date < $max_recv_date) 
		{
				echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Item";disconnect($con);die;
		}
	} 
	
	$variable_ready_to_approve=0;
	$variable_ready_to_approve=return_field_value("ready_to_approve","variable_settings_inventory","company_name=$cbo_dying_company and variable_list=48 and status_active=1 and is_deleted=0","ready_to_approve");
	if($variable_ready_to_approve != 1) $variable_ready_to_approve=0;
	
	if( $operation!=0)
	{
		$max_recv_id_arr = return_library_array ("select max(id) as id, prod_id from inv_transaction where prod_id in($prod_ids) and transaction_type in (1,4,5) and status_active = 1 and is_deleted = 0 group by prod_id", "prod_id", "id"); 
	}
	
	//-----------------------------------------------------------------------------
	
	$trans_sql=sql_select("select ID, PROD_ID, STORE_ID, BATCH_LOT, TRANSACTION_TYPE, ITEM_CATEGORY, CONS_QUANTITY, CONS_AMOUNT, BALANCE_QNTY, BALANCE_AMOUNT, CONS_RATE, STORE_AMOUNT
 	from inv_transaction where status_active=1 and is_deleted=0 and prod_id in($prod_ids) and store_id = $issue_store_id");
	$trans_data_arr=array();$chemDataArr=array(); $dyesDataArr=array(); $auxChemDataArr=array();
	foreach($trans_sql as $val)
	{
		if($variable_lot==1) $lot_no=$val["BATCH_LOT"]; else $lot_no="";
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
			$trans_data_arr[$val["PROD_ID"]][$lot_no]["STOCK"] -=$val["CONS_QUANTITY"];
			$trans_data_arr[$val["PROD_ID"]][$lot_no]["STORE_AMOUNT"] -=$val["STORE_AMOUNT"];
		}
	}
	
	$product_arr=array();
	//$issue_store_id=str_replace("'","",$cbo_store_name);
	$dataArray = sql_select("select ID, AVG_RATE_PER_UNIT, CURRENT_STOCK, STOCK_VALUE from product_details_master where item_category_id in (5,6,7,23) and id in($prod_ids)");
	foreach($dataArray as $row)
	{
		$product_arr[$row["ID"]]['qty']=$row["CURRENT_STOCK"];
		$product_arr[$row["ID"]]['rate']=$row["AVG_RATE_PER_UNIT"];
		$product_arr[$row["ID"]]['val']=$row["STOCK_VALUE"];
	}
	$sql_store = sql_select("select ID, PROD_ID, STORE_ID, CATEGORY_ID AS CAT_ID, RATE AS AVG_RATE, CONS_QTY AS CURRENT_STOCK, AMOUNT AS STOCK_VALUE, LAST_PURCHASED_QNTY AS LAST_QTY, LOT 
	from inv_store_wise_qty_dtls 
	where company_id=$cbo_dying_company and store_id=$issue_store_id and prod_id in($prod_ids) and status_active=1 and is_deleted=0");
	$store_arr=array();$store_idarr=array();
	foreach($sql_store as $row)
	{
		if($variable_lot==1) $lot_no=$row["LOT"]; else $lot_no="";
		$store_arr[$row["ID"]][$lot_no]['qty']=$row["CURRENT_STOCK"];
		$store_arr[$row["ID"]][$lot_no]['avg_rate']=$row["AVG_RATE"];
		
		$store_idarr[$row["PROD_ID"]][$row[csf("store_id")]][$lot_no]['id']=$row["ID"];
		$store_wise_stock_qnty_arr[$row["PROD_ID"]][$lot_no] = $row["CURRENT_STOCK"];
	}
			
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$dys_che_issue_num=''; $dys_che_update_id=''; $product_id=0;
	
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==2 || $db_type==1) { $year_id=" extract(year from insert_date)="; }
			if($db_type==0)  { $year_id="YEAR(insert_date)="; }
			
			$new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_dying_company,'DCI',5,date("Y",time()) ));
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			
			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number,issue_basis, issue_purpose, entry_form, company_id, location_id, store_id, buyer_id, buyer_job_no, style_ref, req_no, batch_no, issue_date, knit_dye_source, knit_dye_company, challan_no, loan_party, lap_dip_no, order_id, machine_id,remarks, division_id, department_id, section_id, attention, inserted_by, insert_date, lc_company,ready_to_approve,is_multi,issue_category";
			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_basis.",".$cbo_issue_purpose.",5,".$cbo_dying_company.",".$cbo_location_name.",".$cbo_store_name.",".$cbo_buyer_name.",".$txt_order_no.",".$txt_style_no.",".$txt_req_id.",".$txt_batch_id.",".$txt_issue_date.",".$cbo_dying_source.",".$cbo_dying_company.",".$txt_challan_no.",".$cbo_loan_party.",".$txt_recipe_id.",".$hidden_order_id.",".$txt_machine_id.",".$txt_return.",".$cbo_division_name.",".$cbo_department_name.",".$cbo_section_name.",".$txt_attention.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_company_name.",".$variable_ready_to_approve.",2,".$cbo_issue_category.")";
			//$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			$dys_che_issue_num=$new_system_id[0];
			$dys_che_update_id=$id;
		}
		else
		{
			$field_array_update="issue_basis*issue_purpose*entry_form*company_id*location_id*buyer_id*buyer_job_no*style_ref*req_no*batch_no*issue_date*knit_dye_source*knit_dye_company*challan_no*loan_party*lap_dip_no*order_id*machine_id*remarks*division_id*department_id*section_id*attention*updated_by*update_date*lc_company*issue_category";
			$data_array_update="".$cbo_issue_basis."*".$cbo_issue_purpose."*5*".$cbo_dying_company."*".$cbo_location_name."*".$cbo_buyer_name."*".$txt_order_no."*".$txt_style_no."*".$txt_req_id."*".$txt_batch_id."*".$txt_issue_date."*".$cbo_dying_source."*".$cbo_dying_company."*".$txt_challan_no."*".$cbo_loan_party."*".$txt_recipe_id."*".$hidden_order_id."*".$txt_machine_id."*".$txt_return."*".$cbo_division_name."*".$cbo_department_name."*".$cbo_section_name."*".$txt_attention."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_name."*".$cbo_issue_category."";
			
			$dys_che_issue_num=str_replace("'","",$txt_mrr_no);
			$dys_che_update_id=str_replace("'","",$update_id);
			//$rID=sql_update("inv_issue_master", $field_array_update, $data_array_update, "id", $update_id,1); 
		}
		
		$all_prod_id_c=''; $all_prod_id_d=''; $all_prod_id_ac=''; $all_prod_ids=''; $all_store_ids='';$all_cat_ids='';$all_reqn_qnty_edit='';$store_stock_arr=array();
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$txt_lot="txt_lot_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$prod_id=str_replace("'",'',$$txt_prod_id);
			$cat_id=str_replace("'",'',$$txt_item_cat);
			if($variable_lot==1) $dyes_lot=str_replace("'",'',$$txt_lot); else $dyes_lot="";
			$storeId=$store_idarr[$prod_id][$issue_store_id][$dyes_lot]['id'];
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				
				if(str_replace("'",'',$storeId)!="")
				{
					$reg_issue_qty=str_replace("'",'',$$txt_reqn_qnty_edit)*1;
					//$reg_issue_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$dyes_lot]['issue_qty'];
					$curr_stock=$store_arr[$storeId][$dyes_lot]['qty']-$reg_issue_qty;
					$s_avg_rate=$store_arr[$storeId][$dyes_lot]['avg_rate'];
					$store_StockValue=$curr_stock*$s_avg_rate;
					$sid_arr[]=str_replace("'",'',$storeId);
					$data_array_store[str_replace("'",'',$storeId)] =explode(",",("".$reg_issue_qty.",".$curr_stock.",".number_format($store_StockValue,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
			}

			
			$prod_stock=$product_arr[str_replace("'",'',$$txt_prod_id)]['qty']*1;			
			$store_prod_qnty=$store_wise_stock_qnty_arr[str_replace("'",'',$$txt_prod_id)][$dyes_lot]*1;
			$trans_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$dyes_lot]["STOCK"];
			
			if(number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($store_prod_qnty,6,'.','') || number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($prod_stock,6,'.','') || number_format(str_replace("'",'',$$txt_reqn_qnty_edit),6,'.','') > number_format($trans_stock,6,'.','') )
			{
				//echo "10**".$store_prod_qnty."=".str_replace("'",'',$$txt_prod_id)."=".str_replace("'",'',$$txt_lot)."=".$dyes_lot;die;
				echo "20**Issue Quantity Not Allow Over Stock Quantity. \n Your Input Qnty = ".str_replace("'",'',$$txt_reqn_qnty_edit)." \n Store Wise Stock = ".$store_prod_qnty." \n Global Stock = ".$prod_stock."\n Transaction Wise Stock = " .$trans_stock;disconnect($con);die;
			}
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$prod_id=str_replace("'",'',$$txt_prod_id);
				//$store_id=str_replace("'",'',$$cbo_store_name);
				$cat_id=str_replace("'",'',$$txt_item_cat);
				
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$store_stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
				$txt_reqn_qnty_edit=str_replace("'",'',$$txt_reqn_qnty_edit);
				$storeid=$store_idarr[$prod_id][$issue_store_id][$dyes_lot]['id'];
				$store_stock_arr[$storeid][$prod_id][$issue_store_id][$dyes_lot]['issue_qty']=$txt_reqn_qnty_edit;
					
				$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
				$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				//$all_store_ids.=str_replace("'",'',$$cbo_store_name).",";
				//$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
			}
		}
		
		//echo "10**";print_r($sid_arr );die;
		
		$cbo_dying_company=str_replace("'",'',$cbo_dying_company);
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
		$reg_issue_qty=0;//$curr_stock=0;
		
		$updateID_array=array();
		$update_data=array();
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, requisition_no, receive_basis, batch_id, company_id, prod_id, item_category, transaction_type, transaction_date, cons_uom, cons_quantity, cons_rate, cons_amount, issue_challan_no, store_id, inserted_by, insert_date, batch_lot, remarks, store_rate, store_amount";

		if(str_replace("'","",$cbo_issue_basis)==4){
			$field_array_dtls="id,mst_id,trans_id,requ_no,batch_id,recipe_id,requisition_basis,sub_process,product_id,item_category,dose_base,ratio,recipe_qnty,adjust_percent,adjust_type, required_qnty,req_qny_edit,inserted_by,insert_date,PACK_TYPE,PACK_QTY";
		}
		else{
			$field_array_dtls="id,mst_id,trans_id,requ_no,batch_id,recipe_id,requisition_basis,sub_process,product_id,item_category,dose_base,ratio,recipe_qnty,adjust_percent,adjust_type, required_qnty,req_qny_edit,inserted_by,insert_date";
		}
		
		 
		$field_array_mrr="id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$adcomma=1;
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$txt_lot="txt_lot_".$i;
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
			$txt_remarks="txt_remarks_".$i;
			$cbo_sub_process="txt_sub_process_id_".$i;
			$cbo_pack_type="cbo_pack_type_".$i;
			$pack_qty="no_pack_qty_".$i;
			
			if($variable_lot==1) $dyes_lot=str_replace("'",'',$$txt_lot); else $dyes_lot="";
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("DYES_CHEM_ISSUE_DTLS_PK_SEQ", "dyes_chem_issue_dtls", $con);
				
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];

				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
					
				$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
				$issue_stock_value = $avg_rate*$txt_reqn_qnty_e;
				$trans_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$dyes_lot]["STOCK"];
				$trans_store_amount=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$dyes_lot]["STORE_AMOUNT"];
				$store_item_rate=0;
				if($trans_store_amount !=0 && $trans_stock !=0) $store_item_rate=$trans_store_amount/$trans_stock;
				$issue_store_value=$txt_reqn_qnty_e*$store_item_rate;
								
				if ($adcomma!=1) $data_array_trans .=",";
				$data_array_trans.="(".$id_trans.",".$dys_che_update_id.",".$txt_req_id.",".$cbo_issue_basis.",".$txt_batch_id.",".$cbo_dying_company.",".$$txt_prod_id.",".$$txt_item_cat.",2,".$txt_issue_date.",12,".$txt_reqn_qnty_e.",".number_format($avg_rate,10,'.','').",".number_format($issue_stock_value,8,'.','').",".$txt_challan_no.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$txt_lot.",".$$txt_remarks.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";

				if(str_replace("'","",$cbo_issue_basis)==4){
					if ($adcomma!=1) $data_array_dtls .=",";
					$data_array_dtls .="(".$id_dtls.",".$dys_che_update_id.",".$id_trans.",".$txt_req_no.",".$txt_batch_id.",".$txt_recipe_id.",".$cbo_issue_basis.",".$$cbo_sub_process.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbo_pack_type.",".$$pack_qty.")";
				}
				else{
					if ($adcomma!=1) $data_array_dtls .=",";
					$data_array_dtls .="(".$id_dtls.",".$dys_che_update_id.",".$id_trans.",".$txt_req_no.",".$txt_batch_id.",".$txt_recipe_id.",".$cbo_issue_basis.",".$$cbo_sub_process.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				
				//product master table data UPDATE START----------------------//
				$currentStock   = $stock_qnty-$txt_reqn_qnty_e;
				
				if(str_replace("'",'',$$txt_prod_id)!="")
				{
					$id_arr[]=str_replace("'",'',$$txt_prod_id);
					if ($currentStock != 0){
						$StockValue	 	= $currentStock*$avg_rate;
						//$avgRate	 	= number_format($avg_rate,$dec_place[3],'.',''); 
						
						$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".number_format($StockValue,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
					} else {
						$StockValue	 	= 0;
						$avgRate	 	= 0; 
						$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".number_format($StockValue,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
					}					
				}
				//------------------ product_details_master END--------------//
				//LIFO/FIFO Start-----------------------------------------------//txt_issue_qnty
				
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
							$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",5,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
							
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
							$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",5,".$$txt_prod_id.",".$balance_qnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
							//for update
							$updateID_array[]=$recv_trans_id; 
							$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
							$txt_reqn_qnty_e = $issueQntyBalance;
							//$mrrWiseIsID++;
						}
					}//end foreach
				}
				$adcomma++;
			}
		}
		
		$rID=$rID2=$rID3=$rID4=$mrrWiseIssueID=$upTrID=$store_ID=true;
		// echo "5**insert into inv_issue_master (".$field_array.") values ".$data_array;die;
		if(str_replace("'","",$update_id)=="")
		{
			//echo "5**insert into inv_issue_master (".$field_array.") values ".$data_array;die;
		 	$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
		}
		else
		{		
			$rID=sql_update("inv_issue_master", $field_array_update, $data_array_update, "id", $update_id,1); 
		}
		
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("dyes_chem_issue_dtls",$field_array_dtls,$data_array_dtls,1); 
	    $rID4=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod, $data_array_prod, $id_arr ),1);
		$mrrWiseIssueID=true;
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
		}
		
		//transaction table stock update here------------------------//
		$upTrID=true;
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array),1);
		}
		$store_ID=true;
		//echo "10**".bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr );die;
		if(count($sid_arr)>0)
		{
		 $store_ID=execute_query(bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr ),1);
		}
		
		//echo "10**$rID=$rID2=$rID3=$rID4=$mrrWiseIssueID=$upTrID=$store_ID";oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $mrrWiseIssueID && $upTrID && $store_ID)
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
			if($rID && $rID2 && $rID3 && $rID4 && $mrrWiseIssueID && $upTrID && $store_ID)
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
		disconnect($con);
		die;
				
	}
	
	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$is_posted=sql_select("select is_posted_account from inv_issue_master where id=$update_id and status_active=1");

		if($is_posted[0][csf("is_posted_account")] == 1)
		{
			echo "20**Already Posted in Accounts, so update and delete is not allowed";
			disconnect($con);die;
		}
		
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
			if($variable_lot==1) $dyes_lot=str_replace("'",'',$$txt_lot); else $dyes_lot="";
			$store_prod_qnty=$store_wise_stock_qnty_arr[str_replace("'",'',$$txt_prod_id)][$dyes_lot];
			$store_c_stock=($store_prod_qnty+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
			$prod_stock=($product_arr[str_replace("'",'',$$txt_prod_id)]['qty']+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
			$trans_stock=($trans_data_arr[str_replace("'",'',$$txt_prod_id)][$dyes_lot]["STOCK"]+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
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
				
				
				$storeid=$store_idarr[$prod_id][$issue_store_id][$dyes_lot]['id'];
				$store_stock_arr[$storeid][$prod_id][$issue_store_id][$dyes_lot]['issue_qty']=$txt_reqn_qnty_edit;
				$store_stock_arr[$storeid][$prod_id][$issue_store_id][$dyes_lot]['hidden_qty']=$hidtxt_reqn_qnty_edit;
				//$store_stock_arr[str_replace("'",'',$$txt_prod_id)]['hidd_qty']=str_replace("'",'',$$hidtxt_reqn_qnty_edit);
				$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
				$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				//$all_store_ids.=str_replace("'",'',$$cbo_store_name).",";
			}
		}
		$all_transId=chop($all_transId,",");
		$cbo_dying_company=str_replace("'",'',$cbo_dying_company);
		$stock_store_arr=fnc_store_wise_qty_operation($cbo_dying_company,$issue_store_id,$all_cat_ids,$all_prod_ids,2);
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
		$storeArrId=array_unique(explode(",",$stock_store_arr));
		$prev_prod_id_arr=array();
		$transData = sql_select("select a.item_category, a.prod_id, b.issue_trans_id, a.id, a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id in($all_transId) and b.entry_form=5"); 
		foreach($transData as $row)
		{
			$adjTransDataArr[$row[csf("item_category")]][$row[csf("issue_trans_id")]].=$row[csf("id")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")]."**".$row[csf("issue_qnty")]."**".$row[csf("rate")]."**".$row[csf("amount")].",";
		}
		
		$updateID_array=array(); $update_data=array();
		
		$field_array_update="issue_basis*issue_purpose*entry_form*company_id*location_id*buyer_id*buyer_job_no*style_ref*req_no*batch_no*issue_date*knit_dye_source*knit_dye_company*challan_no*loan_party*lap_dip_no*order_id*machine_id*remarks*division_id*department_id*section_id*attention*updated_by*update_date*lc_company*issue_category";
		$data_array_update="".$cbo_issue_basis."*".$cbo_issue_purpose."*5*".$cbo_dying_company."*".$cbo_location_name."*".$cbo_buyer_name."*".$txt_order_no."*".$txt_style_no."*".$txt_req_id."*".$txt_batch_id."*".$txt_issue_date."*".$cbo_dying_source."*".$cbo_dying_company."*".$txt_challan_no."*".$cbo_loan_party."*".$txt_recipe_id."*".$hidden_order_id."*".$txt_machine_id."*".$txt_return."*".$cbo_division_name."*".$cbo_department_name."*".$cbo_section_name."*".$txt_attention."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_name."*".$cbo_issue_category."";
		//echo $data_array_update;
		$dys_che_issue_num=$txt_mrr_no;
		$dys_che_update_id=$update_id;
		
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		$up_field_array_trans="prod_id*item_category*transaction_type*transaction_date*cons_uom*cons_quantity*cons_rate*cons_amount*issue_challan_no*store_id*updated_by*update_date*batch_lot*remarks*store_rate*store_amount";

		if(str_replace("'","",$cbo_issue_basis)==4){
			$up_field_array_dtls="sub_process*product_id*item_category*dose_base*ratio*recipe_qnty*adjust_percent*adjust_type*required_qnty*req_qny_edit*updated_by*update_date*pack_type*pack_qty";		
		}
		else{
			$up_field_array_dtls="sub_process*product_id*item_category*dose_base*ratio*recipe_qnty*adjust_percent*adjust_type*required_qnty*req_qny_edit*updated_by*update_date";		
		}

		//echo $total_row;die;
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		 
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$txt_lot="txt_lot_".$i;
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
			$txt_remarks="txt_remarks_".$i;
			$cbo_sub_process="txt_sub_process_id_".$i;
			$pack_type="cbo_pack_type_".$i;
			$pack_qty="no_pack_qty_".$i;
			//===================================================
			if($variable_lot==1) $dyes_lot=str_replace("'",'',$$txt_lot); else $dyes_lot="";
			
			$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
			$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
			$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
			
			$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
			$issue_stock_value = $avg_rate*str_replace("'","",$txt_reqn_qnty_e);
			
			$current_store_stock=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$dyes_lot]["STOCK"];
			$current_store_amt=$trans_data_arr[str_replace("'",'',$$txt_prod_id)][$dyes_lot]["STORE_AMOUNT"];
			$store_item_rate=0;
			if($current_store_amt !=0 && $current_store_stock !=0) $store_item_rate=$current_store_amt/$current_store_stock;
			$issue_store_value = $store_item_rate*$txt_reqn_qnty_e;
			
			if(str_replace("'",'',$$transId)!="")
			{
				if($max_recv_id_arr[str_replace("'",'',$$txt_prod_id)]>str_replace("'",'',$$transId))
				{
					echo "20**Next Transaction Found=";oci_rollback($con);disconnect($con);die;
				}
				
				$id_arr_trans[]=str_replace("'",'',$$transId);
				$data_array_trans[str_replace("'",'',$$transId)] =explode("*",("".$$txt_prod_id."*".$$txt_item_cat."*2*".$txt_issue_date."*12*".$txt_reqn_qnty_e."*".number_format($avg_rate,10,'.','')."*".number_format($issue_stock_value,8,'.','')."*".$txt_challan_no."*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$txt_lot."*".$$txt_remarks."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));
			}
			
			if(str_replace("'",'',$$updateIdDtls)!="")
			{
				echo "wasy";
				if(str_replace("'","",$cbo_issue_basis)==4){
					$id_arr_dtls[]=str_replace("'",'',$$updateIdDtls);
					$data_array_dtls[str_replace("'",'',$$updateIdDtls)] =explode(",",("".$$cbo_sub_process.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$pack_type.",".$$pack_qty.""));
				}
				else{
					$id_arr_dtls[]=str_replace("'",'',$$updateIdDtls);
					$data_array_dtls[str_replace("'",'',$$updateIdDtls)] =explode(",",("".$$cbo_sub_process.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
			}
			//product master table data UPDATE START----------------------//
			$currentStock   = $stock_qnty-$txt_reqn_qnty_e+str_replace("'", '',$$hidtxt_reqn_qnty_edit);
			if(str_replace("'",'',$$txt_prod_id)!="")
			{
				$id_arr[]=str_replace("'",'',$$txt_prod_id);
				if ($currentStock != 0){
					$StockValue	 	= $currentStock*$avg_rate;
					$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".number_format($StockValue,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				} else {
					$StockValue	 	= 0;
					$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".number_format($StockValue,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
				
			}
			//------------------ product_details_master END--------------//
			
			//-----Store Wise Stock----//
			$prod_id=str_replace("'",'',$$txt_prod_id);
			$cat_id=str_replace("'",'',$$txt_item_cat);
			$storeId=$store_idarr[$prod_id][$issue_store_id][$dyes_lot]['id'];
			// echo $$txt_reqn_qnty_e.'aaz';
			if(str_replace("'",'',$txt_reqn_qnty_e)>0)
			{
				if(str_replace("'",'',$storeId)!="")
				{
					$req_hidden_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$dyes_lot]['hidden_qty'];
					$reg_issue_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$dyes_lot]['issue_qty'];
					$store_curr_stock=$store_arr[$storeId][$dyes_lot]['qty']-$reg_issue_qty+$req_hidden_qty;
					$s_avg_rate=$store_arr[$storeId][$dyes_lot]['avg_rate'];
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",5,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					$amount = $txt_reqn_qnty_e*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",5,".$$txt_prod_id.",".$balance_qnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
		//echo "10**DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id in($all_transId) and entry_form=5";die;
		$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id in($all_transId) and entry_form=5",1);
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
		 $store_ID=execute_query(bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr ),1);
        if($flag==1) { if($store_ID) $flag=1; else $flag=0; } 
		}
		//echo "10**".bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr );die;
		//echo "10**".$query2.'='.$rID.'='.$upTrID.'='.$upDtID.'='.$rID4.'='.$mrrWiseIssueID.'='.$store_ID;die;
		
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

		$is_posted=sql_select("select is_posted_account from inv_issue_master where id=$update_id and status_active=1");

		if($is_posted[0][csf("is_posted_account")] == 1)
		{
			echo "20**Already Posted in Accounts, so update and delete is not allowed";
			disconnect($con);die;
		}
		
		$all_prod_id_c=''; $all_prod_id_d=''; $all_prod_id_ac=''; $all_transId='';  $all_prod_ids='';$all_cat_ids='';
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$transId="transId_".$i;
			$hidtxt_reqn_qnty_edit="hidtxt_reqn_qnty_edit_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			$txt_lot="txt_lot_".$i;
			
			if(str_replace("'",'',$$transId)*1<$max_recv_id_arr[str_replace("'",'',$$txt_prod_id)]*1)
			{
				echo "20**Next Transaction Found=";oci_rollback($con);disconnect($con);die;
			}
			
			$all_transId.=str_replace("'",'',$$transId).",";
			if($variable_lot==1) $dyes_lot=str_replace("'",'',$$txt_lot); else $dyes_lot="";
			$store_prod_qnty=$store_wise_stock_qnty_arr[str_replace("'",'',$$txt_prod_id)][$dyes_lot];
			$store_c_stock=($store_prod_qnty+str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
			$prod_stock=($product_arr[str_replace("'",'',$$txt_prod_id)]['qty']-str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
			$trans_stock=($trans_data_arr[str_replace("'",'',$$txt_prod_id)][$dyes_lot]["STOCK"]-str_replace("'",'',$$hidtxt_reqn_qnty_edit))*1;
			
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
				
				
				$storeid=$store_idarr[$prod_id][$issue_store_id][$dyes_lot]['id'];
				$store_stock_arr[$storeid][$prod_id][$issue_store_id][$dyes_lot]['issue_qty']=$txt_reqn_qnty_edit;
				$store_stock_arr[$storeid][$prod_id][$issue_store_id][$dyes_lot]['hidden_qty']=$hidtxt_reqn_qnty_edit;
				//$store_stock_arr[str_replace("'",'',$$txt_prod_id)]['hidd_qty']=str_replace("'",'',$$hidtxt_reqn_qnty_edit);
				$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
				$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
				//$all_store_ids.=str_replace("'",'',$$cbo_store_name).",";
			}
		}
		$all_transId=chop($all_transId,",");
		$cbo_dying_company=str_replace("'",'',$cbo_dying_company);
		$stock_store_arr=fnc_store_wise_qty_operation($cbo_dying_company,$issue_store_id,$all_cat_ids,$all_prod_ids,2);
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
		$storeArrId=array_unique(explode(",",$stock_store_arr));
		$prev_prod_id_arr=array();
		$transData = sql_select("select a.item_category, a.prod_id, b.issue_trans_id, a.id, a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id in($all_transId) and b.entry_form=5"); 
		foreach($transData as $row)
		{
			$adjTransDataArr[$row[csf("item_category")]][$row[csf("issue_trans_id")]].=$row[csf("id")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")]."**".$row[csf("issue_qnty")]."**".$row[csf("rate")]."**".$row[csf("amount")].",";
		}
		
		$updateID_array=array(); $update_data=array();
		$dys_che_issue_num=$txt_mrr_no;
		$dys_che_update_id=$update_id;
		
		$up_field_array="updated_by*update_date*status_active*is_deleted";
		//echo $total_row;die;
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$user_id=$_SESSION['logic_erp']['user_id']; 
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$txt_lot="txt_lot_".$i;
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
			$txt_remarks="txt_remarks_".$i;
			//===================================================
			
			$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
			$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
			$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
			
			$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
			$issue_stock_value = $avg_rate*str_replace("'","",$txt_reqn_qnty_e);
			
			if(str_replace("'",'',$$transId)!="")
			{
				$id_arr_trans[]=str_replace("'",'',$$transId);
				$data_array_trans[str_replace("'",'',$$transId)] =explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
			}
			
			if(str_replace("'",'',$$updateIdDtls)!="")
			{
				$id_arr_dtls[]=str_replace("'",'',$$updateIdDtls);
				$data_array_dtls[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
			}
			//product master table data UPDATE START----------------------//
			$currentStock   = $stock_qnty-$txt_reqn_qnty_e+str_replace("'", '',$$hidtxt_reqn_qnty_edit);			

			if(str_replace("'",'',$$txt_prod_id)!="")
			{
				$id_arr[]=str_replace("'",'',$$txt_prod_id);
				if ($currentStock != 0){
					$StockValue	 	= $currentStock*$avg_rate;
					//$avgRate	 	= number_format($avg_rate,$dec_place[3],'.',''); 
					$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".number_format($StockValue,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				} else {
					$StockValue	 	= 0;
					//$avgRate	 	= 0; 
					$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod[str_replace("'",'',$$txt_prod_id)] =explode(",",("".$txt_reqn_qnty_e.",".$currentStock.",".number_format($StockValue,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
				
			}
			//------------------ product_details_master END--------------//
			
			//-----Store Wise Stock----//
			$prod_id=str_replace("'",'',$$txt_prod_id);
			$cat_id=str_replace("'",'',$$txt_item_cat);
			if($variable_lot==1) $dyes_lot=str_replace("'",'',$$txt_lot); else $dyes_lot="";
			$storeId=$store_idarr[$prod_id][$issue_store_id][$dyes_lot]['id'];
			// echo $$txt_reqn_qnty_e.'aaz';
			if(str_replace("'",'',$txt_reqn_qnty_e)>0)
			{
				//echo $$txt_reqn_qnty_e.'a4';
				if(str_replace("'",'',$storeId)!="")
				{
					$req_hidden_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$dyes_lot]['hidden_qty'];
					$reg_issue_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$dyes_lot]['issue_qty'];
					//$store_curr_stock=$store_arr[$storeId][$dyes_lot]['qty']-$reg_issue_qty+$req_hidden_qty;
					$store_curr_stock=$store_arr[$storeId][$dyes_lot]['qty']+$req_hidden_qty;
					$s_avg_rate=$store_arr[$storeId][$dyes_lot]['avg_rate'];
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
				//echo $txt_reqn_qnty_e."<br>";
				if($issueQntyBalance>=0)
				{					
					
					$amount = $txt_reqn_qnty_e*$cons_rate;
					//for insert
					/*$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",5,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";*/
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
					/*if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",5,".$$txt_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";*/
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

		$up_field_array="updated_by*update_date*status_active*is_deleted";
		
		$query2=$rID=$upTrID=$upDtID=$rID4=$mrrWiseIssueID=$store_ID=true;
		$query2 = execute_query("update inv_mrr_wise_issue_details set updated_by='".$_SESSION['logic_erp']['user_id']."' , update_date='".$pc_date_time."' , status_active=0 , is_deleted =1 WHERE issue_trans_id in($all_transId) and entry_form=5",1);		
		/*$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1); 
		if($flag==1) { if($rID) $flag=1; else $flag=0; }*/
		
		if(count($id_arr_trans)>0)
		{  
			//echo bulk_update_sql_statement("inv_transaction","id",$up_field_array_trans,$data_array_trans,$id_arr_trans);die;
		    $upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$up_field_array,$data_array_trans,$id_arr_trans),1);
		}
		
		if(count($id_arr_dtls)>0)
		{ 
		    $upDtID=execute_query(bulk_update_sql_statement("dyes_chem_issue_dtls","id",$up_field_array,$data_array_dtls,$id_arr_dtls),1);
		}
		
		if(count($id_arr)>0)
		{
			//echo bulk_update_sql_statement( "product_details_master", "id", $field_array_prod, $data_array_prod, $id_arr );die;
		    $rID4=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod, $data_array_prod, $id_arr ),1);
		}
		
		if(count($sid_arr)>0)
		{
			$store_ID=execute_query(bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr ),1);
		}
		//echo "10**".bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr );die;
		//echo "10**".$query2.'='.$rID.'='.$upTrID.'='.$upDtID.'='.$rID4.'='.$mrrWiseIssueID.'='.$store_ID;die;
		
		if($db_type==0)
		{
			if($query2 && $rID && $upTrID && $upDtID && $rID4 && $mrrWiseIssueID && $store_ID)
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
			
			if($query2 && $rID && $upTrID && $upDtID && $rID4 && $mrrWiseIssueID && $store_ID)
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
}


if($action=="chemical_dyes_issue_cost_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	// echo "<pre>";
	// print_r($data);
	$company=$data[0];
	$location=$data[3];
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$loan_party_arr=return_library_array( "select id, other_party_name from  lib_other_party", "id", "other_party_name"  );
	$batch_weight_arr=return_library_array( "select id, batch_weight from  pro_batch_create_mst", "id", "batch_weight"  );
	$liquor_arr=return_library_array( "select id, total_liquor from  pro_batch_create_mst", "id", "total_liquor"  );
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$group_library=return_library_array("select id,group_name from lib_group" ,"id","group_name");
	$req_arr = return_library_array("select id,requ_no from dyes_chem_issue_requ_mst where status_active=1 and entry_form=156","id","requ_no");
	$sql="select a.id, a.issue_number, a.issue_date, a.issue_basis,a.issue_purpose, a.req_no, a.batch_no, a.issue_purpose, a.loan_party,a.lap_dip_no, a.knit_dye_source, a.knit_dye_company, a.challan_no,a.remarks, a.buyer_job_no,a.order_id, a.buyer_id, a.style_ref, b.user_full_name from inv_issue_master a left join user_passwd b on b.id = a.inserted_by where a.id='$data[1]' and a.company_id='$data[0]'";
	$batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where batch_against<>0 ","id","batch_no");
    $dataArray=sql_select($sql);
	$batch_id_all=explode(",",$dataArray[0][csf('batch_no')]);
    foreach($batch_id_all as $b_id)
	{
		if($batch_no=="") $batch_no=$batch_arr[$b_id]; 
		else $batch_no.=",".$batch_arr[$b_id];
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
		$sql_batch = sql_select("select  batch_weight, total_liquor,color_range_id from pro_batch_create_mst where id in(".$dataArray[0][csf('batch_no')].")  and status_active=1 and is_deleted=0"); 
		foreach($sql_batch as $row_batch)
		{
		$total_liquor=$row_batch[csf("total_liquor")]; $total_batch=$row_batch[csf("batch_weight")];
		$color_range_id=$row_batch[csf("color_range_id")]; 
		}
		}
    }
	
	if(trim($dataArray[0][csf('issue_basis')])==7)
	{
	$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$dataArray[0][csf('lap_dip_no')].") ","total_liquor");
	$total_batch=return_field_value("sum(batch_weight) as batch_weight" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") ","batch_weight");
	$color_range_id=return_field_value("color_range_id as color_range_id" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") ","color_range_id");
	} 
	$order_id=$dataArray[0][csf('order_id')];
	if($order_id=="") $order_id=0;else $order_id=$order_id;
	$po_sqls=sql_select("Select id as po_id,grouping as ref_no,file_no from  wo_po_break_down where  status_active=1 and is_deleted=0 and id in(".$order_id.")");
	$file_no='';$ref_no='';
	foreach($po_sqls as $row_id)
	{
		if($file_no=='') $file_no=$row_id[csf('file_no')];else $file_no.=",".$row_id[csf('file_no')];
		if($ref_no=='') $ref_no=$row_id[csf('ref_no')];else $ref_no.=",".$row_id[csf('ref_no')];
	}

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$dye_company_id=$dataArray[0][csf('knit_dye_company')];
	$nameArray=sql_select( "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$dye_company_id"); 
	$group_id=$nameArray[0][csf('group_id')];
	
	if($dataArray[0][csf('knit_dye_source')]==3)
	{
		$supplier_com_library=$supplier_library[$dataArray[0][csf('knit_dye_company')]];
	}
	else
	{
		$supplier_com_library= $company_library[$dataArray[0][csf('knit_dye_company')]];
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
 ?>
 <div style="width:1100px; ">
    <table width="1100" cellspacing="0" align="center">
        <tr>
        	<td rowspan="3" width="70">
            	<img src="../../../<? echo $com_dtls[2]; ?>" height="60" width="180">
            </td>
            <td colspan="6" align="center" style="font-size:20px">
            	<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$com_dtls[0]; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
				echo $com_dtls[1];
				
				/*foreach ($nameArray as $result)
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
				}*/
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><strong><u> <? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.' Dyes & Chemical Issue Note'?> </u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="125"><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="130"><strong>Issue Basis :</strong></td> <td width="250px"><? echo $receive_basis_arr[$dataArray[0][csf('issue_basis')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Req. No:</strong></td><td><? echo $req_arr[$dataArray[0][csf('req_no')]]; ?></td>
            <td><strong>Batch No :</strong></td><td ><? echo $batch_no; ?></td>
			<td><strong>Issue Purpose :</strong></td> <td><? echo $general_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Loan Party:</strong></td> <td><? echo $loan_party_arr[$dataArray[0][csf('loan_party')]]; ?></td>
            <td><strong>Dyeing Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
            <td><strong>Dyeing Company:</strong></td><td><? 
			if($dataArray[0][csf('knit_dye_source')]==3)
			{
				echo $supplier_library[$dataArray[0][csf('knit_dye_company')]];
			}
			else
			{
				echo $company_library[$dataArray[0][csf('knit_dye_company')]];
			}
			 ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Recipe No :</strong></td><td><? echo $dataArray[0][csf('lap_dip_no')]; ?></td>
            <td><strong>Buyer Order:</strong></td><td><div style="word-wrap:break-word; width:240px"><? echo $dataArray[0][csf('buyer_job_no')]; ?></div></td>
        </tr>
        <tr>
            <td><strong>Buyer Name:</strong></td> <td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
            <td><strong>Style Ref. :</strong></td><td><? echo $dataArray[0][csf('style_ref')]; ?></td>
            <td><strong>Batch Weight:</strong></td><td><? echo $total_batch; ?></td>
        </tr>
         <tr>
            <td><strong>Color Range:</strong></td> <td><? echo $color_range[$color_range_id]; ?></td>
            <td><strong>File No :</strong></td><td><? echo $file_no; ?></td>
            <td><strong>Ref. No:</strong></td><td><? echo $ref_no; ?></td>
        </tr>
        <tr>
            <td><strong>Total Liq.(ltr):</strong></td> <td><? echo $total_liquor; ?></td>
            <td><strong>Batch Color:</strong></td> <td><div style="word-wrap:break-word; width:175px"><? echo $color_arr[$color_id]; ?></div></td>
            <td  colspan="2" id="barcode_img_id"></td>
        </tr>
		<tr>
		 <td><strong>Return:</strong></td><td colspan="5"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
		</tr>
    </table>
        <br>
	<div style="width:1300px;">
    <table align="" cellspacing="0" width="1150"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="160" >Store Name</th>
            <th width="80" >Item Cat.</th>
            <th width="80" >Item Group</th>
            <th width="80" >Sub Group</th>
            <th width="60" >Lot</th>
            <th width="170" >Item Description</th>
            <th width="40" >UOM</th>
            <th width="80" >Dose Base</th> 
            <th width="40" >Ratio</th>
            <th width="60" >Recipe Qnty</th>
            <th width="40" >Adj%</th>
            <th width="50" >Adj Type</th> 
            <th width="60" >Issue Qnty</th>
            <th width="60" >Unit Price</th> 
            <th>Amount(BDT)</th>
        </thead>
        <tbody> 
   
 <?
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0",'id','item_name');
 // ****************************previous sql***************************************************
	$sql_dtls ="select b.id, a.issue_number, b.store_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.machine_category, b.machine_id, b.prod_id, b.department_id, b.section_id, b.cons_rate, b.cons_amount, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit, b.batch_lot
	from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
	where a.id=d.mst_id and b.id=d.trans_id and d.product_id=c.id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and d.mst_id=$data[1] and a.status_active=1 and b.status_active=1 and d.status_active=1 
	order by d.sub_process "; 
	//  ******************************************************************************************************************************************************	
	$sql_result= sql_select($sql_dtls);
	$i=1;$m=1;
	$arr_sub_process=array();$sub_process_sum=array();
	if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56)
	{
		foreach($sql_result as $row)
		{
			if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$count=0;
			$arr_sub_process[]=$row[csf("sub_process")];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $store_library[$row[csf("store_id")]]; ?></td>
				<td><? echo $item_category[$row[csf("item_category")]]; ?></td>
				<td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
				<td><? echo $row[csf("sub_group_name")]; ?></td>
				<td><? echo $row[csf("batch_lot")]; ?></td>
				<td><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
				<td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
				<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
				<td align="right"><? echo number_format($row[csf("ratio")],6,".",""); ?></td>
				<td align="right"><? echo number_format($row[csf("recipe_qnty")],6,".",""); ?></td>
				<td align="right"><? echo $row[csf("adjust_percent")]; ?></td>
				<td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
				<td align="right"><? echo number_format($row[csf("req_qny_edit")],6,".",""); ?></td>
				<td align="right"><?php echo number_format($row[csf("cons_rate")],6,".",""); ?></td>
				<td align="right" colspan="3"><?php echo number_format($row[csf("cons_amount")],6,".",""); ?></td>
			</tr>
			<?
            $req_qny_edit=$row[csf('req_qny_edit')];
            $req_qny_edit_sum += $req_qny_edit;
            $recipe_qnty=$row[csf('recipe_qnty')];
            $recipe_qnty_sum += $recipe_qnty;
            $total_amount=$row[csf("cons_amount")];
            $total_amount_sum+=$total_amount;
            $sub_process_sum[$row[csf("sub_process")]]=$total_amount_sum;
            //echo $sub_preocess_data.'Aziz';
            $grand_totao_amount+=$total_amount;
            $total_req_qny_edit+=$req_qny_edit;
            $grand_total_receive+=$recipe_qnty;
            $i++; $m++;
		} 
	?>
	</tbody>
	<?
	}
	else
	{	
		foreach($sql_result as $row)
		{
			if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$count=0;
			if(!in_array($row[csf("sub_process")],$arr_sub_process))
			{
				if($m!=1)
				{
					?> 
					<tr>
					<td colspan="10" align="right"><strong>Total :</strong></td>
					<td align="right"><?php echo number_format($recipe_qnty_sum,6,".",""); ?></td>
					<td align="right" colspan="3"><?php echo number_format($req_qny_edit_sum,6,".",""); ?></td>
					<td align="right" colspan="2"><?php echo number_format($total_amount_sum,6,".",""); ?></td>
					</tr>
					<?
					$recipe_qnty_sum=0; $req_qny_edit_sum=0; $total_amount_sum=0;
				}
				?> 
				<tr>
					<td colspan="16" style="font-size:15px; text-align:center"><strong>Dyeing Sub-Process: <? echo  $dyeing_sub_process[$row[csf("sub_process")]] ?></strong></td>
				</tr>
				<?
			}
			$arr_sub_process[]=$row[csf("sub_process")];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><? echo $store_library[$row[csf("store_id")]]; ?></td>
                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
                <td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                <td><? echo $row[csf("sub_group_name")]; ?></td>
                <td><? echo $row[csf("batch_lot")]; ?></td>
                <td><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                <td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("ratio")],6,".",""); ?></td>
                <td align="right"><? echo number_format($row[csf("recipe_qnty")],6,".",""); ?></td>
                <td align="right"><? echo $row[csf("adjust_percent")]; ?></td>
                <td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("req_qny_edit")],6,".",""); ?></td>
                <td align="right"><?php echo number_format($row[csf("cons_rate")],6,".",""); ?></td>
                <td align="right" colspan="3"><?php echo number_format($row[csf("cons_amount")],6,".",""); ?></td>
			</tr>
			<?
			$req_qny_edit=$row[csf('req_qny_edit')];
			$req_qny_edit_sum += $req_qny_edit;
			$recipe_qnty=$row[csf('recipe_qnty')];
			$recipe_qnty_sum += $recipe_qnty;
			$total_amount=$row[csf("cons_amount")];
			$total_amount_sum+=$total_amount;
			$sub_process_sum[$row[csf("sub_process")]]=$total_amount_sum;
			//echo $sub_preocess_data.'DDD';
			
			$grand_totao_amount+=$total_amount;
			$total_req_qny_edit+=$req_qny_edit;
			$grand_total_receive+=$recipe_qnty;
			$i++; $m++;
		} 
		 ?>
        <tr>
            <td colspan="10" align="right"><strong>Total :</strong></td>
            <td align="right"><?php echo number_format($recipe_qnty_sum,6,".",""); ?></td>
            <td align="right" colspan="3"><?php echo number_format($req_qny_edit_sum,6,".",""); ?></td>
            <td align="right" colspan="2"><?php echo number_format($total_amount_sum,6,".",""); ?></td>
            
        </tr>
    </tbody>
    <?
	}
	?>
    <tfoot>
        <tr>
            <td colspan="10" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><?php echo number_format($grand_total_receive,6,".",""); ?></td>
            <td align="right" colspan="3"><?php echo number_format($total_req_qny_edit,6,".",""); ?></td>
            <td align="right" colspan="2"><?php echo number_format($grand_totao_amount,6,".",""); ?></td>
        </tr> 
        <tr>
            <td colspan="14" align="right"><strong>Cost Per Kg</strong></td>
            <td align="right" colspan="2"><?php  if($grand_totao_amount && $total_batch ) echo $CostPerKg=$CostPerKg=number_format(($grand_totao_amount/$total_batch),6,".",""); else echo $CostPerKg=0;//echo $grand_totao_amount."dkfj".$total_batch//number_format(($grand_totao_amount/$total_batch),6,".",""); ?></td>
        </tr>                       
     </tfoot>
 </table>
 <br>
 <?
  $tot_row=count($sub_process_sum);
 ?>
  <table   cellspacing="0" width="500"  border="1" rules="all" class="rpt_table" align="center" >
  <caption style="font:x-large"> <b>Process Wise Cost Summary :</b></caption> 
   <thead bgcolor="#dddddd" align="center">
        <th width="30">SL</th>
        <th width="200">Sub-Process Name</th>
        <th width="80">Total Cost</th>
        <th width="80">Batch Weight</th>
        <th width="100">Cost/Kg (Tk)</th>
   </thead>
   <?
		$k=1;	$total_summary=0; $row_span=1;
		foreach($sub_process_sum as $sub_id=>$sval)	  
		{
		if ($k%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
   <tr bgcolor="<? echo $bgcolor; ?>">
        <td align="center"><? echo $k; ?></td>
        <td><? echo $dyeing_sub_process[$sub_id] ; ?></td>
        <td align="right"><? echo number_format($sval,6,".",""); ?></td>
        <?
        if($row_span==1)
		{ ?>
         <td align="right" rowspan="<? echo $tot_row;?>"><? echo $total_batch; ?></td>
        
       <? } ?>
        <td align="right"><? if($sval && $total_batch ) echo $CostPerTk=number_format($sval/$total_batch,6,".",""); else echo $CostPerTk=0;//echo number_format($sval/$total_batch,6,".",""); ?></td>
   </tr>
   
   
   <? 
   $total_summary+=$sval;
   	$k++;
	$row_span++;
	}
   
   ?>
   <tfoot>
        <tr>
            <td colspan="2" align="right"><strong>Summary Total :</strong></td>
            <td align="right"><?php echo number_format($total_summary,6,".",""); ?></td>
            <td>&nbsp; </td>
       </tr>
  </tfoot>
  </table>
    
 <br>
        <table style="margin-top: 80px;" id="signatureTblId" width="1150">
            <tr>
                <?
                $sql = sql_select("select designation as DESIGNATION, name as NAME, prepared_by as PREPARED_BY from variable_settings_signature where report_id=9 and company_id= $company and status_active=1 and template_id = 1 order by sequence_no");
                //                echo "select designation as DESIGNATION, name as NAME, prepared_by as PREPARED_BY from variable_settings_signature where report_id=9 and company_id= 3 and status_active=1 and template_id = 1 order by sequence_no";
                if(count($sql) > 0){
                    if($sql[0]['PREPARED_BY'] == 1){
                        array_unshift($sql, array('DESIGNATION'=>'Prepared By', 'NAME' => $dataArray[0][csf('user_full_name')]));
                    }
                    $count = count($sql);
                    $td_width = floor(1150 / $count);
                    $standard_width = $count * 150;
                    if ($standard_width > 1150) {
                        $td_width = 150;
                    }
                }
                foreach ($sql as $key => $val){
                    echo '<td width="' . $td_width . '" align="center" valign="top"><strong style="text-decoration:overline">'.$val['DESIGNATION'].'</strong><br>'.$val['NAME'].'</td>';
                }
                ?>
            </tr>
        </table>
  </div>
 </div> 
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
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
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
			generateBarcode('<? echo $data[2]; ?>');
	</script>
	 <? 
} 

if($action=="delivery_challan_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	// echo "<pre>";
	// print_r ($data);
	$company=$data[0];
	$location=$data[4];
	
	$sql="select a.id, a.issue_number, a.issue_date, a.issue_basis,a.issue_purpose, a.req_no, a.batch_no, a.issue_purpose,a.attention, a.loan_party,a.lap_dip_no, a.issue_category, a.knit_dye_source, a.knit_dye_company, a.challan_no,a.remarks, a.buyer_job_no,a.order_id, a.buyer_id, a.style_ref, b.user_full_name from inv_issue_master a left join user_passwd b on b.id = a.inserted_by  where a.id='$data[1]' and a.company_id='$data[0]'";
	$batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where batch_against<>0 ","id","batch_no");
    $dataArray=sql_select($sql);
	$recipe_id=$dataArray[0][csf('lap_dip_no')];
	$batch_id_all=explode(",",$dataArray[0][csf('batch_no')]);
    foreach($batch_id_all as $b_id)
	{
		if($batch_no=="") $batch_no=$batch_arr[$b_id];
		else $batch_no.=",".$batch_arr[$b_id];
	}   	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_weight_arr=return_library_array( "select id, batch_weight from  pro_batch_create_mst", "id", "batch_weight"  );
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$req_arr = return_library_array("select id,requ_no from dyes_chem_issue_requ_mst where status_active=1 and entry_form=156","id","requ_no");
	
	$order_id=$dataArray[0][csf('order_id')];
	if($order_id=="") $order_id=0;else $order_id=$order_id;
	$po_sqls=sql_select("Select id as po_id,grouping as ref_no,file_no from  wo_po_break_down where  status_active=1 and is_deleted=0 and id in(".$order_id.")");
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
		$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$dataArray[0][csf('lap_dip_no')].") and entry_form=59 and company_id=".$data[0]."","total_liquor");
		//$total_batch=return_field_value("sum(batch_weight) as batch_weight" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","batch_weight");
		if($recipe_id=="") $recipe_id=0; else $recipe_id=$recipe_id;
		$batch_id=return_field_value("batch_id as batch_id" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and entry_form=59","batch_id");
		
		if($batch_id=="") $batch_id=0; else $batch_id=$batch_id;
		$total_batch=return_field_value("sum(batch_qty) as batch_weight" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and entry_form=59 and batch_id in(".$batch_id.")  ","batch_weight");
				
		$color_range_id=return_field_value("color_range_id as color_range_id" ,"pro_batch_create_mst","id in(".$batch_id.") ","color_range_id");
	}
	?>
	<?
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$dye_company=$dataArray[0][csf('knit_dye_company')];
	$nameArray=sql_select( "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$dye_company");
	$group_id=$nameArray[0][csf('group_id')];
	
	if($dataArray[0][csf('knit_dye_source')]==3)
	{
		$com_supp_cond=$supplier_library[$dataArray[0][csf('knit_dye_company')]];
	}
	else
	{
		$com_supp_cond=$company_library[$dataArray[0][csf('knit_dye_company')]];
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	 
	?>
	<div style="width:1200px;">
    <table width="1200" cellspacing="0" align="">
        <tr>
        	<td rowspan="3" width="70">
            	<img src="../../../<? echo $com_dtls[2]; ?>" height="60" width="180">
            </td>
            <td colspan="6" align="center" style="font-size:20px">
            	<strong style="font-size:32px;"><? echo $com_dtls[0]; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$addrs=$com_dtls[1];
					echo "<p style='font-size:18px;'>$addrs</p>";					 					 
                ?> 
            </td>  
        </tr>
		<tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					 
					echo "<p style='font-size:30px;margin-top:10px;font-weight:bold;'>Delivery Challan</p>";					 					 
                ?> 
            </td>  
        </tr>
          
	</table>

	<table width="1200" cellspacing="0" align="" style="margin-top:20px;border:1px solid black;">
        <tr>
        	<td width="140" style="border:1px solid black;height:50px;"><strong style='font-size:22px;'>Challan No</strong></td><td width="200" style="border:1px solid black;"><? echo "<p style='font-size:22px;'>".$dataArray[0][csf('issue_number')]."</p>"; ?></td>
            <td width="140" style="border:1px solid black; "><strong style='font-size:22px;'>Issue Date</strong></td><td width="220" style="border:1px solid black;"><? echo "<p style='font-size:22px;'>".change_date_format($dataArray[0][csf('issue_date')])."</p>"; ?></td>
            <td width="140" style="border:1px solid black;"><strong style='font-size:22px;'>Issue Basis</strong></td> <td style="border:1px solid black;"><? echo "<p style='font-size:22px;'>".$receive_basis_arr[$dataArray[0][csf('issue_basis')]]."</p>"; ?></td>
        </tr>
        <tr>
            <td style="border:1px solid black; height:50px;"><strong style='font-size:22px;'>Attention</strong></td> <td style="border:1px solid black;"><? echo "<p style='font-size:22px;'>".$dataArray[0][csf('attention')]."</p>"; ?></td>
            <td style="border:1px solid black;"><strong style='font-size:22px;'>Category</strong></td><td style="border:1px solid black;"><? 
			$issue_cat_array=[1=>"Return",2=>"Sales",3=>"Loan",4=>"Returnable",5=>"Non-Returnable",6=>"Service",7=>"Exchange"];
			echo "<p style='font-size:22px;'>".$issue_cat_array[$dataArray[0]['ISSUE_CATEGORY']]."</p>"; ?></td>
			<td style="border:1px solid black;"><strong style='font-size:22px;'>Issue Purpose</strong></td> <td style="border:1px solid black;"><? echo "<p style='font-size:22px;'>".$general_issue_purpose[$dataArray[0][csf('issue_purpose')]]."</p>"; ?></td>
        </tr>
        <tr>
            <td style="border:1px solid black;height:50px;"><strong style='font-size:22px;'>Loan Party</strong></td> <td style="border:1px solid black;"><? echo "<p style='font-size:22px;'>".$supplier_library[$dataArray[0][csf('loan_party')]]."</p>"; ?></td>
            <td style="border:1px solid black;"><strong style='font-size:22px;'>Address</strong></td><td style="border:1px solid black;" colspan="2" ><? 
				 $loan_party=$dataArray[0][csf('loan_party')];				  
				 $addrs=sql_select("select address_1 from lib_supplier where id=$loan_party");
				 echo "<p style='font-size:22px;'>".$addrs[0]['ADDRESS_1']."</p>";
			?></td>
            <td id="barcode_img_id" style="border:1px solid black;" ><? 
				 
			 ?></td>
			  
        </tr>
        		 
    </table>
        <br>
	<div style="width:100%;">
    <table align="" cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center" style="font-size:18px;">
            <th width="20">SL</th>             
            <th width="80" >Item Category</th>             
            <th width="50" >Lot</th>
			<th width="190">Item Description</th>
			<th width="20" >Pack Type</th>
			<th width="20" >No. Of <br>Pack Qty</th>
            
            <th width="20" >UOM</th>      
			<? 			
				if($data[5]!=0)               			
				{
			?>
			<th width="20" >Rate</th>   
			<th width="20" >Value</th>   
			<?
				}
			?>
            <th width="10">Issue Qnty</th>
            <th width="100">Remarks</th>
        </thead>
        <tbody> 
		<?
        $group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0",'id','item_name');
     
        $sql_dtls = "select b.id, a.issue_number, b.store_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, b.cons_rate, b.cons_amount, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit,d.pack_type,d.pack_qty, b.batch_lot, b.remarks
		from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
		where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and d.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		order by d.sub_process "; 
		//echo $sql_dtls;die;
		$sql_result= sql_select($sql_dtls);
		$i=1;$totIssueQnty=0;$totValue=0;
		if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56) $colspan=10; else $colspan=11;
	
		
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
                
                 
                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>                 
                <td><? echo $row[csf("batch_lot")]; ?></td>
				<td><? echo "<b>".$row[csf("item_description")].' '.$row[csf("item_size")]."</b>"; ?></td>
				<td style="text-align:center;"><? echo $pack_type_array[$row[csf("PACK_TYPE")]]; ?></td>
				<td style="text-align:center;"><? echo "<b>".$row[csf("pack_qty")]."</b>"; ?></td>
                
                <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>

				<? 			
					if($data[5]!=0)               			
					{
				?>
				<td align="center"><? echo number_format($row["CONS_RATE"],2); ?></td>
				<td align="center"><? echo number_format($row["CONS_RATE"]*$row[csf("req_qny_edit")],2); 
				 $totValue=$totValue+$row["CONS_RATE"]*$row[csf("req_qny_edit")];?></td>
				 <? 
					}
				 ?>
                <td align="right"><? echo "<b>".number_format($row[csf("req_qny_edit")],6,".","")."</b>"; 
				 		$totIssueQnty=$totIssueQnty+$row[csf("req_qny_edit")];
				 ?></td>
                <td style="text-align:center;"><? echo $row[csf("remarks")]; ?></td>
			</tr>
			<? $i++; 
		} 
		?>
        </tbody>
        <tfoot>
			<? 			
				if($data[5]!=0)               			
				{
			?>
				<tr>
					<td colspan="<? echo 8; ?>" align="right"><strong>Total :</strong></td>
					<td align="center"><? echo "<b>".number_format($totValue,2)."</b>"; ?></td>
					<td align="right"><? echo "<b>".number_format($totIssueQnty,6)."</b>"; ?></td>
					<td align="right"></td>
					
				</tr>     
			<?
				}
				else{
			?>       
				<tr>
					<td colspan="<? echo 7; ?>" align="right"><strong>Total :</strong></td>
					 
					<td align="right"><? echo "<b>".number_format($totIssueQnty,6)."</b>"; ?></td>
					<td align="right"></td>
					
				</tr>
			<?
				}
			?>
        </tfoot>
      </table>
        <div style="margin-top:-50px;">
			<? 
				echo signature_table(325,$company,1200);
			?>
		</div>
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
  
	   generateBarcode('<? echo $dataArray[0][csf('issue_number')]; ?>');
	 
	 
	 </script>
     <?
	 exit(); 
}


if($action=="chemical_dyes_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	// echo "<pre>";
	// print_r ($data);
	$company=$data[0];
	$location=$data[4];
	
	$sql="select a.id, a.issue_number, a.issue_date, a.issue_basis,a.issue_purpose, a.req_no, a.batch_no, a.issue_purpose, a.loan_party,a.lap_dip_no, a.knit_dye_source, a.knit_dye_company, a.challan_no,a.remarks, a.buyer_job_no,a.order_id, a.buyer_id, a.style_ref, b.user_full_name from inv_issue_master a left join user_passwd b on b.id = a.inserted_by  where a.id='$data[1]' and a.company_id='$data[0]'";
	$batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where batch_against<>0 ","id","batch_no");
    $dataArray=sql_select($sql);
	$recipe_id=$dataArray[0][csf('lap_dip_no')];
	$batch_id_all=explode(",",$dataArray[0][csf('batch_no')]);
    foreach($batch_id_all as $b_id)
	{
		if($batch_no=="") $batch_no=$batch_arr[$b_id];
		else $batch_no.=",".$batch_arr[$b_id];
	}   	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_weight_arr=return_library_array( "select id, batch_weight from  pro_batch_create_mst", "id", "batch_weight"  );
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$req_arr = return_library_array("select id,requ_no from dyes_chem_issue_requ_mst where status_active=1 and entry_form=156","id","requ_no");
	
	$order_id=$dataArray[0][csf('order_id')];
	if($order_id=="") $order_id=0;else $order_id=$order_id;
	$po_sqls=sql_select("Select id as po_id,grouping as ref_no,file_no from  wo_po_break_down where  status_active=1 and is_deleted=0 and id in(".$order_id.")");
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
		$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$dataArray[0][csf('lap_dip_no')].") and entry_form=59 and company_id=".$data[0]."","total_liquor");
		//$total_batch=return_field_value("sum(batch_weight) as batch_weight" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","batch_weight");
		if($recipe_id=="") $recipe_id=0; else $recipe_id=$recipe_id;
		$batch_id=return_field_value("batch_id as batch_id" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and entry_form=59","batch_id");
		
		if($batch_id=="") $batch_id=0; else $batch_id=$batch_id;
		$total_batch=return_field_value("sum(batch_qty) as batch_weight" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and entry_form=59 and batch_id in(".$batch_id.")  ","batch_weight");
				
		$color_range_id=return_field_value("color_range_id as color_range_id" ,"pro_batch_create_mst","id in(".$batch_id.") ","color_range_id");
	}
	?>
	<?
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$dye_company=$dataArray[0][csf('knit_dye_company')];
	$nameArray=sql_select( "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$dye_company");
	$group_id=$nameArray[0][csf('group_id')];
	
	if($dataArray[0][csf('knit_dye_source')]==3)
	{
		$com_supp_cond=$supplier_library[$dataArray[0][csf('knit_dye_company')]];
	}
	else
	{
		$com_supp_cond=$company_library[$dataArray[0][csf('knit_dye_company')]];
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1200px;">
    <table width="1200" cellspacing="0" align="">
        <tr>
        	<td rowspan="3" width="70">
            	<img src="../../../<? echo $com_dtls[2]; ?>" height="60" width="180">
            </td>
            <td colspan="6" align="center" style="font-size:20px">
            	<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$com_dtls[0]; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					echo $com_dtls[1];
					 
					/*foreach ($nameArray as $result)
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
					}*/
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><strong><u><? echo 'Owner Company : '.$company_library[$data[0]].'<br/>'.' Dyes & Chemical Issue Note';?></u></strong></td>
        </tr>
        <tr>
        	<td width="140"><strong>Issue ID :</strong></td><td width="200"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="140"><strong>Issue Date:</strong></td><td width="220"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="140"><strong>Issue Basis :</strong></td> <td><? echo $receive_basis_arr[$dataArray[0][csf('issue_basis')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Req. No:</strong></td> <td><? echo $req_arr[$dataArray[0][csf('req_no')]]; ?></td>
            <td><strong>Batch No :</strong></td><td><? echo $batch_no; ?></td>
			<td><strong>Issue Purpose :</strong></td> <td><? echo $general_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Loan Party:</strong></td> <td><? echo $supplier_library[$dataArray[0][csf('loan_party')]]; ?></td>
            <td><strong>Dyeing Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
            <td><strong>Dyeing Company:</strong></td><td><? 
				echo $com_supp_cond;
			 ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?>&nbsp;</td>
            <td><strong>Recipe No :</strong></td><td><? echo $dataArray[0][csf('lap_dip_no')]; ?></td>
            <td><strong>Buyer Order:</strong></td><td style="word-break:break-all"><? echo $dataArray[0][csf('buyer_job_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Buyer Name:</strong></td> <td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
            <td><strong>Style Ref. :</strong></td><td><? echo $dataArray[0][csf('style_ref')]; ?></td>
            <td><strong>Batch Weight:</strong></td><td><? echo $total_batch; ?></td>
        </tr>
         <tr>
            <td><strong>Color Range:</strong></td> <td><? echo $color_range[$color_range_id]; ?></td>
            <td><strong>File No :</strong></td><td><? echo $file_no; ?></td>
            <td><strong>Ref. No:</strong></td><td><?  echo $ref_no; ?></td>
        </tr>
        <tr>
            <td><strong>Total Liq.(ltr):</strong></td><td><? echo $total_liquor; ?></td>
            <td><strong>Batch Color:</strong></td><td><div style="word-wrap:break-word; width:175px"><? echo $color_arr[$color_id]; ?></div></td>
            <td colspan="2" id="barcode_img_id"></td>
        </tr>
		<tr>
		 <td><strong>Return:</strong></td><td colspan="5"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
		</tr>
    </table>
        <br>
	<div style="width:100%;">
    <table align="" cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <?
			if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56)
			{
				
			}
			else
			{
				?>
				<th width="100">Sub Process</th>
				<?
			}
			?>
            <th width="130" >Store Name</th>
            <th width="80" >Item Cat.</th>
            <th width="70" >Item Group</th>
            <th width="70" >Sub Group</th>
            <th width="50" >Lot</th>
            <th width="140">Item Description</th>
            <th width="40" >UOM</th>
            <th width="80" >Dose Base</th> 
            <th width="40" >Ratio</th>
            <th width="60" >Recipe Qnty</th>
            <th width="40" >Adj%</th>
            <th width="40" >Adj Type</th> 
            <th width="80">Issue Qnty</th>
            <th>Remarks</th>
        </thead>
        <tbody> 
		<?
        $group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0",'id','item_name');
     
        $sql_dtls = "select b.id, a.issue_number, b.store_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, b.cons_rate, b.cons_amount, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit, b.batch_lot, b.remarks
		from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
		where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and d.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		order by d.sub_process "; 
		//echo $sql_dtls;die;
		$sql_result= sql_select($sql_dtls);
		$i=1;
		if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56) $colspan=10; else $colspan=11;
	
		
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
                if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56)
                {
                
                }
                else
                {
                    ?>
                    <td><? echo $dyeing_sub_process[$row[csf("sub_process")]]; ?></td>
                    <?
                }
                ?>
                <td align="center"><? echo $store_library[$row[csf("store_id")]]; ?></td>
                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
                <td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                <td><? echo $row[csf("sub_group_name")]; ?></td>
                <td><? echo $row[csf("batch_lot")]; ?></td>
                <td><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                <td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("ratio")],6,".",""); ?></td>
                <td align="right"><? echo number_format($row[csf("recipe_qnty")],6,".",""); ?></td>
                <td align="right"><? echo $row[csf("adjust_percent")]; ?></td>
                <td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("req_qny_edit")],6,".",""); ?></td>
                <td><? echo $row[csf("remarks")]; ?></td>
			</tr>
			<? $i++; 
		} 
		?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="<? echo $colspan; ?>" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo "<b>".number_format($recipe_qnty_sum,6,".","")."</b>"; ?></td>
                <td align="right" colspan="3"><?php echo "<b>".number_format($req_qny_edit_sum,6,".","")."</b>"; ?></td>
                <td>&nbsp;</td>
            </tr>                           
        </tfoot>
      </table>
        <br>
        <table style="margin-top: 80px;" id="signatureTblId" width="1200">
            <tr>
                <?
                $sql = sql_select("select designation as DESIGNATION, name as NAME, prepared_by as PREPARED_BY from variable_settings_signature where report_id=9 and company_id= $company and status_active=1 and template_id = 1 order by sequence_no");
//                echo "select designation as DESIGNATION, name as NAME, prepared_by as PREPARED_BY from variable_settings_signature where report_id=9 and company_id= 3 and status_active=1 and template_id = 1 order by sequence_no";
                if(count($sql) > 0){
                    if($sql[0]['PREPARED_BY'] == 1){
                        array_unshift($sql, array('DESIGNATION'=>'Prepared By', 'NAME' => $dataArray[0][csf('user_full_name')]));
                    }
                    $count = count($sql);
                    $td_width = floor(1200 / $count);
                    $standard_width = $count * 150;
                    if ($standard_width > 1200) {
                        $td_width = 150;
                    }
                }
                foreach ($sql as $key => $val){
                    echo '<td width="' . $td_width . '" align="center" valign="top"><strong style="text-decoration:overline">'.$val['DESIGNATION'].'</strong><br>'.$val['NAME'].'</td>';
                }
                ?>
            </tr>
        </table>
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

if($action=="chemical_dyes_issue_print_single_com")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company=$data[0];
	$location=$data[4];
	
	$sql="select a.id, a.issue_number, a.issue_date, a.issue_basis,a.issue_purpose, a.req_no, a.batch_no, a.issue_purpose, a.loan_party,a.lap_dip_no, a.knit_dye_source, a.knit_dye_company, a.challan_no,a.remarks, a.buyer_job_no,a.order_id, a.buyer_id, a.style_ref, b.user_full_name from inv_issue_master a left join user_passwd b on b.id = a.inserted_by where a.id='$data[1]' and a.company_id='$data[0]'";
	$batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where batch_against<>0 ","id","batch_no");
    $dataArray=sql_select($sql);
	$recipe_id=$dataArray[0][csf('lap_dip_no')];
	$batch_id_all=explode(",",$dataArray[0][csf('batch_no')]);
    foreach($batch_id_all as $b_id)
	{
		if($batch_no=="") $batch_no=$batch_arr[$b_id];
		else $batch_no.=",".$batch_arr[$b_id];
	}   	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_weight_arr=return_library_array( "select id, batch_weight from  pro_batch_create_mst", "id", "batch_weight"  );
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$req_arr = return_library_array("select id,requ_no from dyes_chem_issue_requ_mst where status_active=1 and entry_form=156","id","requ_no");
	
	$order_id=$dataArray[0][csf('order_id')];
	if($order_id=="") $order_id=0;else $order_id=$order_id;
	$po_sqls=sql_select("Select id as po_id,grouping as ref_no,file_no from  wo_po_break_down where  status_active=1 and is_deleted=0 and id in(".$order_id.")");
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
		$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$dataArray[0][csf('lap_dip_no')].") and entry_form=59 and company_id=".$data[0]."","total_liquor");
		//$total_batch=return_field_value("sum(batch_weight) as batch_weight" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","batch_weight");
		if($recipe_id=="") $recipe_id=0; else $recipe_id=$recipe_id;
		$batch_id=return_field_value("batch_id as batch_id" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and entry_form=59","batch_id");
		
		if($batch_id=="") $batch_id=0; else $batch_id=$batch_id;
		$total_batch=return_field_value("sum(batch_qty) as batch_weight" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and entry_form=59 and batch_id in(".$batch_id.")  ","batch_weight");
				
		$color_range_id=return_field_value("color_range_id as color_range_id" ,"pro_batch_create_mst","id in(".$batch_id.") ","color_range_id");
	}
	?>
	<?
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$dye_company=$dataArray[0][csf('knit_dye_company')];
	$nameArray=sql_select( "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$dye_company");
	$group_id=$nameArray[0][csf('group_id')];
	
	if($dataArray[0][csf('knit_dye_source')]==3)
	{
		$com_supp_cond=$supplier_library[$dataArray[0][csf('knit_dye_company')]];
	}
	else
	{
		$com_supp_cond=$company_library[$dataArray[0][csf('knit_dye_company')]];
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1200px;">
    <table width="1100" cellspacing="0" align="">
        <tr>
        	<td rowspan="3" width="70">
            	<img src="../../../<? echo $com_dtls[2]; ?>" height="60" width="180">
            </td>
            <td colspan="6" align="center" style="font-size:20px">
            	<strong>
				<? 
				//echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$com_supp_cond; 
				echo 'Company : '.$com_dtls[0];;
				?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
				echo $com_dtls[1];
					 
					/*foreach ($nameArray as $result)
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
					}*/
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><strong><u>
			<? 
			//echo 'Owner Company : '.$company_library[$data[0]].'<br/>'.' Dyes & Chemical Issue Note';
			echo 'Dyes & Chemical Issue Note';
			?>
            </u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="125"><strong>Issue Date:</strong></td><td width="150px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="140"><strong>Issue Basis :</strong></td> <td width="250px"><? echo $receive_basis_arr[$dataArray[0][csf('issue_basis')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Req. No:</strong></td> <td><? echo $req_arr[$dataArray[0][csf('req_no')]]; ?></td>
            <td><strong>Batch No :</strong></td><td><? echo $batch_no; ?></td>
			<td><strong>Issue Purpose :</strong></td> <td><? echo $general_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Loan Party:</strong></td> <td><? echo $supplier_library[$dataArray[0][csf('loan_party')]]; ?></td>
            <td><strong>Dyeing Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
            <td><strong>Dyeing Company:</strong></td><td><? 
				echo $com_supp_cond;
			 ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?>&nbsp;</td>
            <td><strong>Recipe No :</strong></td><td><? echo $dataArray[0][csf('lap_dip_no')]; ?></td>
            <td><strong>Buyer Order:</strong></td><td style="word-break:break-all"><? echo $dataArray[0][csf('buyer_job_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Buyer Name:</strong></td> <td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
            <td><strong>Style Ref. :</strong></td><td><? echo $dataArray[0][csf('style_ref')]; ?></td>
            <td><strong>Batch Weight:</strong></td><td><? echo $total_batch; ?></td>
        </tr>
         <tr>
            <td><strong>Color Range:</strong></td> <td><? echo $color_range[$color_range_id]; ?></td>
            <td><strong>File No :</strong></td><td><? echo $file_no; ?></td>
            <td><strong>Ref. No:</strong></td><td><?  echo $ref_no; ?></td>
        </tr>
        <tr>
            <td><strong>Total Liq.(ltr):</strong></td><td><? echo $total_liquor; ?></td>
            <td><strong>Batch Color:</strong></td><td><div style="word-wrap:break-word; width:175px"><? echo $color_arr[$color_id]; ?></div></td>
            <td colspan="2" id="barcode_img_id"></td>
        </tr>
		<tr>
		 <td><strong>Return:</strong></td><td colspan="5"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
		</tr>
    </table>
        <br>
	<div style="width:100%;">
    <table align="" cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <?
			if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56)
			{
				
			}
			else
			{
				?>
				<th width="100">Sub Process</th>
				<?
			}
			?>
            <th width="160" >Store Name</th>
            <th width="80" >Item Cat.</th>
            <th width="80" >Item Group</th>
            <th width="70" >Sub Group</th>
            <th width="60" >Lot</th>
            <th width="170" >Item Description</th>
            <th width="40" >UOM</th>
            <th width="90" >Dose Base</th> 
            <th width="40" >Ratio</th>
            <th width="60" >Recipe Qnty</th>
            <th width="40" >Adj%</th>
            <th width="40" >Adj Type</th> 
            <th>Issue Qnty</th>
        </thead>
        <tbody> 
   
	<?
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0",'id','item_name');
	
 
 
 $sql_dtls = "select b.id, a.issue_number, b.store_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, b.cons_rate, b.cons_amount, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit, b.batch_lot
	  from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
	  where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id  and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and d.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
	  order by d.sub_process "; 
	 // echo $sql_dtls;die;
	  $sql_result= sql_select($sql_dtls);
	  $i=1;
	if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56) $colspan=10; else $colspan=11;
	
		
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
				if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56)
				{
					
				}
				else
				{
					?>
					<td><? echo $dyeing_sub_process[$row[csf("sub_process")]]; ?></td>
					<?
				}
				?>
                <td align="center"><? echo $store_library[$row[csf("store_id")]]; ?></td>
                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
                <td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                <td><? echo $row[csf("sub_group_name")]; ?></td>
                <td><? echo $row[csf("batch_lot")]; ?></td>
                <td><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                <td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("ratio")],6,".",""); ?></td>
                <td align="right"><? echo number_format($row[csf("recipe_qnty")],6,".",""); ?></td>
                <td align="right"><? echo $row[csf("adjust_percent")]; ?></td>
                <td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("req_qny_edit")],6,".",""); ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="<? echo $colspan; ?>" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo number_format($recipe_qnty_sum,6,".",""); ?></td>
                <td align="right" colspan="3"><?php echo number_format($req_qny_edit_sum,6,".",""); ?></td>
            </tr>                           
        </tfoot>
      </table>
        <br>
        <table style="margin-top: 80px;" id="signatureTblId" width="1100">
            <tr>
                <?
                $sql = sql_select("select designation as DESIGNATION, name as NAME, prepared_by as PREPARED_BY from variable_settings_signature where report_id=9 and company_id= $company and status_active=1 and template_id = 1 order by sequence_no");
                //                echo "select designation as DESIGNATION, name as NAME, prepared_by as PREPARED_BY from variable_settings_signature where report_id=9 and company_id= 3 and status_active=1 and template_id = 1 order by sequence_no";
                if(count($sql) > 0){
                    if($sql[0]['PREPARED_BY'] == 1){
                        array_unshift($sql, array('DESIGNATION'=>'Prepared By', 'NAME' => $dataArray[0][csf('user_full_name')]));
                    }
                    $count = count($sql);
                    $td_width = floor(1100 / $count);
                    $standard_width = $count * 150;
                    if ($standard_width > 1100) {
                        $td_width = 150;
                    }
                }
                foreach ($sql as $key => $val){
                    echo '<td width="' . $td_width . '" align="center" valign="top"><strong style="text-decoration:overline">'.$val['DESIGNATION'].'</strong><br>'.$val['NAME'].'</td>';
                }
                ?>
            </tr>
        </table>
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



if($action=="chemical_dyes_issue_print_single_com2")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company=$data[0];
	$location=$data[4];
	$system_no=str_replace("'", "", $data[3]);
	
	$sql="select a.id, a.issue_number, a.issue_date, a.attention, a.store_id, a.issue_basis,a.issue_purpose, a.req_no, a.batch_no, a.issue_purpose, a.loan_party,a.lap_dip_no, a.knit_dye_source, a.knit_dye_company, a.challan_no,a.remarks, a.buyer_job_no,a.order_id, a.buyer_id, a.style_ref from inv_issue_master a where a.id='$data[1]' and a.company_id='$data[0]'";
	$batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where batch_against<>0 ","id","batch_no");
    $dataArray=sql_select($sql);
	$recipe_id=$dataArray[0][csf('lap_dip_no')];
	$order_id=$dataArray[0][csf('order_id')];
	$batch_id_all=explode(",",$dataArray[0][csf('batch_no')]);
    foreach($batch_id_all as $b_id)
	{
		if($batch_no=="") $batch_no=$batch_arr[$b_id];
		else $batch_no.=",".$batch_arr[$b_id];
	}   	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$location_lib=return_library_array( "select id,location_name from lib_location where company_id='$company'", "id", "location_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_weight_arr=return_library_array( "select id, batch_weight from  pro_batch_create_mst", "id", "batch_weight"  );
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$req_arr = return_library_array("select id,requ_no from dyes_chem_issue_requ_mst where status_active=1 and entry_form=156","id","requ_no");
	//$location_arr = return_library_array("select id,location_name from lib_location","id","lib_location");

	

	$job_sql="SELECT d.style_ref_no, d.job_no_prefix_num, d.job_no from wo_po_break_down c, wo_po_details_master d where c.id in($order_id) and c.job_no_mst=d.job_no and c.status_active=1 and c.is_deleted=0";
	//echo $job_sql; die;
	$job_dataArray=sql_select($job_sql);
	$job_no=$job_dataArray[0][csf('job_no')];
	$job_style=$job_dataArray[0][csf('style_ref_no')];

	

	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.SECURITY_LOCK_NO,a.DRIVER_NAME,a.DRIVER_LICENSE_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = ".$company." AND a.basis = 6 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '".$system_no."%'";
	//echo $sql_get_pass; die;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];
				
				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				
				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_lib[$row['LOCATION_ID']];
				}
				
				//for gate pass info
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_location'] =$location_lib[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$row['CHALLAN_NO']]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$row['CHALLAN_NO']]['est_return_date'] = $row['EST_RETURN_DATE'];
				
				$gatePassDataArr[$row['CHALLAN_NO']]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_bag'] += $row['NO_OF_BAGS'];
				
				$gatePassDataArr[$row['CHALLAN_NO']]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$row['CHALLAN_NO']]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$row['CHALLAN_NO']]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$row['CHALLAN_NO']]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
			}
		}
	}
	/*echo "<pre>";
	print_r($gatePassDataArr); die;*/
	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}


	





	$order_id=$dataArray[0][csf('order_id')];
	if($order_id=="") $order_id=0;else $order_id=$order_id;
	$po_sqls=sql_select("Select id as po_id,grouping as ref_no,file_no from  wo_po_break_down where  status_active=1 and is_deleted=0 and id in(".$order_id.")");
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
		$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$dataArray[0][csf('lap_dip_no')].") and entry_form=59 and company_id=".$data[0]."","total_liquor");
		//$total_batch=return_field_value("sum(batch_weight) as batch_weight" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","batch_weight");
		if($recipe_id=="") $recipe_id=0; else $recipe_id=$recipe_id;
		$batch_id=return_field_value("batch_id as batch_id" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and entry_form=59","batch_id");
		
		if($batch_id=="") $batch_id=0; else $batch_id=$batch_id;
		$total_batch=return_field_value("sum(batch_qty) as batch_weight" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and entry_form=59 and batch_id in(".$batch_id.")  ","batch_weight");
				
		$color_range_id=return_field_value("color_range_id as color_range_id" ,"pro_batch_create_mst","id in(".$batch_id.") ","color_range_id");
	}
	?>
	<?
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$dye_company=$dataArray[0][csf('knit_dye_company')];
	$nameArray=sql_select( "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$dye_company");
	$group_id=$nameArray[0][csf('group_id')];
	
	if($dataArray[0][csf('knit_dye_source')]==3)
	{
		$com_supp_cond=$supplier_library[$dataArray[0][csf('knit_dye_company')]];
	}
	else
	{
		$com_supp_cond=$company_library[$dataArray[0][csf('knit_dye_company')]];
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1200px;">
    <table width="100%" cellspacing="0" align="">
        <tr>
        	<td rowspan="3" width="70">
            	<img src="../../../<? echo $com_dtls[2]; ?>" height="60" width="180">
            </td>
            <td colspan="5" align="center" style="font-size:28px">
            	<strong>
				<? 
				//echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$com_supp_cond; 
				echo 'Company : '.$com_dtls[0];;
				?></strong>
            </td>
            <td width="150"  align="right"><?php echo $noOfCopy.($is_gate_pass==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>":'').($is_gate_out==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>":''); ?></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="5" align="center" style="font-size:14px">  
				<?
				echo $com_dtls[1];
					 
					/*foreach ($nameArray as $result)
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
					}*/
                ?> 
            </td>  
            <td>&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td colspan="5" align="center" style="font-size:22px"><strong><u>
			<? 
			//echo 'Owner Company : '.$company_library[$data[0]].'<br/>'.' Dyes & Chemical Issue Note';
			echo 'Dyes & Chemical Issue Challan';
			?>
            </u></strong></td>
            <td>&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td colspan="5" align="center" style="font-size:22px">&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
        </tr>
        <br>

        <tr>
        	<td><strong>Dyeing Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
            <td><strong>Req. No:</strong></td> <td><? echo $req_arr[$dataArray[0][csf('req_no')]]; ?></td>
            <td><strong>Issue No :</strong></td><td><? echo $system_no; ?></td>
			<td>&nbsp;&nbsp;</td>
        </tr>
        <tr>
        	<td><strong>Dyeing Company:</strong></td><td><? echo $com_supp_cond; ?></td>
            <td><strong>Batch No :</strong></td><td><? echo $batch_no; ?></td>
            <td><strong>Issue Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			<td>&nbsp;&nbsp;</td>
        </tr>
        <tr>
        	<td><strong>Loan Party:</strong></td> <td><? echo $supplier_library[$dataArray[0][csf('loan_party')]]; ?></td>
            <td><strong>Batch Weight:</strong></td><td><? echo $total_batch; ?></td>
            <td><strong>Issue Basis :</strong></td> <td><? echo $receive_basis_arr[$dataArray[0][csf('issue_basis')]]; ?></td>
			<td>&nbsp;&nbsp;</td>
        </tr>
        <tr>
        	<td><strong>Attention:</strong></td> <td><? echo $dataArray[0][csf('attention')]; ?></td>
            <td><strong>Batch Color:</strong></td><td><div style="word-wrap:break-word; width:175px"><? echo $color_arr[$color_id]; ?></div></td>
            <td><strong>Issue Purpose :</strong></td> <td><? echo $general_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			<td>&nbsp;&nbsp;</td>
        </tr>
        <tr>
        	<td><strong>Store Name:</strong></td> <td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
            <td><strong>Color Range:</strong></td> <td><? echo $color_range[$color_range_id]; ?></td>
            <td><strong>Job No :</strong></td> <td><? echo $job_no; ?></td>
			<td>&nbsp;&nbsp;</td>
        </tr>
        <tr>
        	<td><strong>Buyer Name:</strong></td> <td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
            <td><strong>Recipe No :</strong></td><td><? echo $dataArray[0][csf('lap_dip_no')]; ?></td>
            <td><strong>Style Ref. :</strong></td><td><? echo $job_style; ?></td>
			<td>&nbsp;&nbsp;</td>
        </tr>
        <tr>
        	<td><strong>Total Liq.(ltr):</strong></td><td><? echo $total_liquor; ?></td>
            <td><strong>Buyer Order:</strong></td><td style="word-break:break-all"><? echo $dataArray[0][csf('buyer_job_no')]; ?></td>
            <!-- <td><strong>Challan No :</strong></td><td><? //echo $dataArray[0][csf('challan_no')]; ?>&nbsp;</td> -->
			<td>&nbsp;&nbsp;</td>
			<td>&nbsp;&nbsp;</td>
        </tr>

         <!-- <tr>
            
            <td><strong>File No :</strong></td><td><? //echo $file_no; ?></td>
            <td><strong>Ref. No:</strong></td><td><?  //echo $ref_no; ?></td>
            <td>&nbsp;&nbsp;</td>
        </tr> -->
        <tr>
           
            
            <td colspan="6" align="center" id="barcode_img_id"></td>
            <td>&nbsp;&nbsp;</td>
        </tr>
		<!-- <tr>
		 <td><strong>Return:</strong></td><td colspan="5"><p><? //echo $dataArray[0][csf('remarks')]; ?></p></td>
		 <td>&nbsp;&nbsp;</td>
		</tr> -->
    </table>
        <br>
	<div style="width:100%;">
    <table align="" cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <?
			if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56)
			{
				
			}
			else
			{
				?>
				<th width="100">Sub Process</th>
				<?
			}
			?>
            
            <th width="100" >Item Category</th>
            <th width="100" >Item Group</th>
           
            <th width="80" >Lot</th>
            <th width="170" >Item Description</th>
            <th width="60" >UOM</th>
            <th width="90" >Dose Base</th> 
            <th width="60" >Ratio</th>
            <th width="60" >Recipe Qnty</th>
            <th width="60" >Adj%</th>
            <th width="60" >Adj Type</th> 
            <th width="80">Issue Qnty</th>
            <th >Remarks</th>
        </thead>
        <tbody> 
   
	<?
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0",'id','item_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	
 
 
 $sql_dtls = "select b.id, a.issue_number, b.store_id, b.cons_uom, c.unit_of_measure, b.cons_quantity, b.cons_amount, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, b.cons_rate, b.cons_amount, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit, b.remarks, b.batch_lot
	  from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
	  where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id  and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and d.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
	  order by d.sub_process "; 
	 // echo $sql_dtls;die;
	  $sql_result= sql_select($sql_dtls);


	$dtls_data_array=array();
	foreach($sql_result as $vals)
	{
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["id"]=$vals[csf("id")];
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["issue_number"].=$vals[csf("issue_number")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["store_id"].=$vals[csf("store_id")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["unit_of_measure"].=$unit_of_measurement[$vals[csf("unit_of_measure")]]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["cons_quantity"]+=$vals[csf("cons_quantity")];
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["cons_amount"]+=$vals[csf("cons_amount")];
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["machine_category"].=$vals[csf("machine_category")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["machine_id"].=$vals[csf("machine_id")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["prod_id"].=$vals[csf("prod_id")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["department_id"].=$vals[csf("department_id")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["section_id"].=$vals[csf("section_id")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["cons_rate"].=$vals[csf("cons_rate")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["item_description"].=$vals[csf("item_description")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["item_group_id"].=$item_group_arr[$vals[csf("item_group_id")]]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["sub_group_name"].=$vals[csf("sub_group_name")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["item_size"].=$vals[csf("item_size")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["sub_process"].=$dyeing_sub_process[$vals[csf("sub_process")]]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["item_category"].=$item_category[$vals[csf("item_category")]]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["dose_base"].=$dose_base[$vals[csf("dose_base")]]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["ratio"].=$vals[csf("ratio")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["recipe_qnty"]+=$vals[csf("recipe_qnty")];
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["adjust_percent"].=$vals[csf("adjust_percent")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["adjust_type"].=$increase_decrease[$vals[csf("adjust_type")]]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["required_qnty"]+=$vals[csf("required_qnty")];
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["req_qny_edit"]+=$vals[csf("req_qny_edit")];
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["remarks"].=$vals[csf("remarks")]."***";
		$dtls_data_array[$vals[csf("unit_of_measure")]][$vals[csf("id")]]["batch_lot"].=$vals[csf("batch_lot")]."***";
		
	}


	/*echo "<pre>";
	print_r($dtls_data_array); die;*/


	  $i=1;
	if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56) $colspan=8; else $colspan=9;
	
		
	foreach($dtls_data_array as $uom_id=> $uom_wise_data)
	{

		$recipe_qnty_sum=0;
		$req_qny_edit_sum=0;
		foreach ($uom_wise_data as $trans_id => $row) {
			
		
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
				
				/*$recipe_qnty=$row['recipe_qnty'];
				$recipe_qnty_sum += $recipe_qnty;
				
				$req_qny_edit=$row['req_qny_edit'];
				$req_qny_edit_sum += $req_qny_edit;*/

				$ratio= implode(", ", array_unique(array_filter(explode("***", $row["ratio"]))));


			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="center"><? echo $i; ?></td>
	                 <?
					if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==56)
					{
						
					}
					else
					{
						?>
						<td><? echo implode(", ", array_unique(array_filter(explode("***", $row["sub_process"])))); ?></td>
						<?
					}
					?>
	                
	                <td><? echo implode(", ", array_unique(array_filter(explode("***", $row["item_category"])))); ?></td>
	                <td><? echo implode(", ", array_unique(array_filter(explode("***", $row["item_group_id"])))); ?></td>
	                <td><? echo implode(", ", array_unique(array_filter(explode("***", $row["batch_lot"]))));  ?></td>
	                <td><? echo implode(", ", array_unique(array_filter(explode("***", $row["item_description"])))).' '.implode(", ", array_unique(array_filter(explode("***", $row["item_size"]))));  ?></td>
	                <td align="center"><? echo implode(", ", array_unique(array_filter(explode("***", $row["unit_of_measure"])))); ?></td>
	                <td><? echo implode(", ", array_unique(array_filter(explode("***", $row["dose_base"]))));  ?></td>
	                <td align="right"><? echo number_format($ratio,6,".",""); ?></td>
	                <td align="right"><? echo number_format($row["recipe_qnty"],6,".",""); ?></td>
	                <td align="right"><? echo implode(", ", array_unique(array_filter(explode("***", $row["adjust_percent"]))));  ?></td>
	                <td><? echo implode(", ", array_unique(array_filter(explode("***", $row["adjust_type"]))));  ?></td>
	                <td align="right"><? echo number_format($row["req_qny_edit"],6,".",""); ?></td>
	                <td><? echo implode(", ", array_unique(array_filter(explode("***", $row["remarks"]))));  ?></td>
				</tr>
				<? $i++;

				$recipe_qnty_sum += $row['recipe_qnty'];
				$req_qny_edit_sum += $row['req_qny_edit'];
		}
		?>
		<tr>
            <td colspan="<? echo $colspan; ?>" align="right"><strong>Sub Total :</strong></td>
            <td align="right"><?php echo number_format($recipe_qnty_sum,6,".",""); ?></td>
            <td align="right">&nbsp;&nbsp;</td>
            <td align="right">&nbsp;&nbsp;</td>
            <td align="right"><?php echo number_format($req_qny_edit_sum,6,".",""); ?></td>
        </tr>  
		<?
	} ?>
        </tbody>
        <!-- <tfoot>
            <tr>
                <td colspan="<? //echo $colspan; ?>" align="right"><strong>Total :</strong></td>
                <td align="right"><?php //echo number_format($recipe_qnty_sum,6,".",""); ?></td>
                <td align="right" colspan="3"><?php //echo number_format($req_qny_edit_sum,6,".",""); ?></td>
            </tr>                           
        </tfoot> -->
      </table>
      	<br>

			<!-- ============= Gate Pass Info Start ========= -->
			<table style="margin-right:-40px;" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                <tr align="center">
                	<td colspan="15"  height="30" style="border-left:hidden;border-right:hidden; text-align: center; font-size:15px;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
                </tr>
                <tr>
                	<td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
                    <td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
                </tr>
                <tr>
                	<td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
                	<td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

                	<td colspan="2"><strong>To Company:</strong></td>
                	<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

                	<td colspan="3"><strong>Carried By:</strong></td>
                	<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>From Location:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
                	<td colspan="2"><strong>To Location:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
                	<td colspan="3"><strong>Driver Name:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
                </tr>
                <tr>
                	<td colspan="2"><strong>Gate Pass ID:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
                	<td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
                	<td align="center" colspan="3"  rowspan="2"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
                	<!-- <td align="center"><strong>Kg</strong></td>
                	<td align="center"><strong>Roll</td>
                	<td align="center"  ><strong>PCS</strong></td> -->
                	<td colspan="3"><strong>Vehicle Number:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>Gate Pass Date:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
                	<!-- <td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
                	<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td>
                	<td align="center" ><?php 
                	if ($gatePassDataArr[$system_no]['gate_pass_id'] !="") 
                	{
                		if ($total_delv>0) {
                		 	echo $total_delv;
                		 } 
                	}
                	?></td> -->
                	<td colspan="3"><strong>Driver License No.:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>Out Date:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
                	<td colspan="2"><strong>Dept. Name:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
                	<td colspan="3"><strong>Mobile No.:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>Out Time:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
                	<td colspan="2"><strong>Attention:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
                	<td colspan="3"><strong>Sequrity Lock No.:</strong></td>
                	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>Returnable:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
                	<td colspan="2"><strong>Purpose:</strong></td>
                	<td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
                </tr>						
                <tr>
                	<td colspan="2"><strong>Est. Return Date:</strong></td>
                	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
                	<td colspan="2"><strong>Remarks:</strong></td>
                	<td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
                </tr>
            </table>
                    <!-- ============= Gate Pass Info End =========== -->


        <br>
        <table width="100%" cellspacing="0" align="left" cellpadding=""  style="margin-left: -50px;float: left;"   >

			<tr>
				<? $width='1100px'; echo signature_table(9, $data[0],$width, ''); ?>
			</tr>

			</table>
		<!--  <?
           // echo signature_table(9, $data[0], "1100px");
         ?> -->
      </div>
   </div> 
    <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <script type="text/javascript" src="../../../js/jquery.qrcode.min.js"></script>
			<script>

				var main_value='<? echo $system_no;?>';
				$('#qrcode').qrcode(main_value);
			</script>
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
	 	//for gate pass barcode
		function generateBarcodeGatePass(valuess)
		{
			var zs = '<?php echo $x; ?>';
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();
			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			$("#gate_pass_barcode_img_id_"+zs).html('11');
			value = {code: value, rect: false};
			$("#gate_pass_barcode_img_id_"+zs).show().barcode(value, btype, settings);
		}
		
		if('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '')
		{
			generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
		}
		</script>
		<div style="page-break-after:always;"></div>
     <?
	 exit(); 
}


if($action=="machine_name_popup")
{
   echo load_html_head_contents("Buyer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_company_name;
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_machine_id').val( id );
			$('#hide_machine_name').val( name );
		}
	
    </script>

	<input type="hidden" id="hide_machine_id" name="hide_machine_id">
    <input type="hidden" id="hide_machine_name" name="hide_machine_name">
	<? 
	$arr=array(); 
	if($db_type==0) $condat="concat(machine_no ,'-', brand) as machine_name"; 
	else if($db_type==2) $condat="machine_no || '-' || brand as machine_name "; 
	$sql="select id, $condat  from lib_machine_name where status_active=1 and is_deleted=0 and company_id='$cbo_company_name' and  category_id in (2,4) order by id";
	
	echo create_list_view("list_view","Machine Name","300","370","280",0,$sql,"js_set_value","id,machine_name",'',1,0,$arr,"machine_name",'','setFilterGrid("list_view",-1);','0','',1);

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
