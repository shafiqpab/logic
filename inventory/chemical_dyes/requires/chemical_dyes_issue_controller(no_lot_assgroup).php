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

if ($company_id !='') {
    $company_credential_cond = "and a.company_id in($company_id)";
}
if ($company_location_id !='') {
    $company_location_credential_cond = "and id in($company_location_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and b.store_location_id in($store_location_id)"; 
}
// user credential data prepare end 


//--------------------------------------------------------------------------------------------
if($action=="load_drop_down_dyeing_for_sub")
{
	//echo create_drop_down( "cbo_dying_company", 170, "select a.id ,a.company_name from   lib_company a, lib_supplier_party_type  b, lib_supplier_tag_company c where a.id=c.tag_company and b.supplier_id=c.supplier_id and b.party_type=21 group by a.id ,a.company_name order by a.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
	if($data)$tag_company_con="and c.tag_company=$data"; else $tag_company_con="";
	echo create_drop_down("cbo_dying_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and  a.id=c.supplier_id $tag_company_con and a.status_active=1 and b.party_type=21 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
}

if($action=="load_drop_down_dyeing")
{
	echo create_drop_down( "cbo_dying_company", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/chemical_dyes_issue_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/chemical_dyes_issue_controller', this.value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/chemical_dyes_issue_controller', this.value, 'load_drop_down_loan_party', 'loan_party_td');","" );
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 170, "select id,location_name from lib_location where company_id='$data' $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if ($action=="load_drop_down_store")
{
	
	echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $company_credential_cond $store_location_credential_cond and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", 0, "fn_sub_process_enable(this.value);",0 );  	 
    exit();
}

if ($action=="load_drop_down_loan_party")
{
	echo create_drop_down( "cbo_loan_party", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","","" );
	exit();
}


if ($action=="load_drop_down_sub_process")
{
	$data=explode('**',$data);
	if($data[2]==1) $is_posted_account=1; else $is_posted_account=0;		
	if($db_type==0)
	{
		$sub_process_id=return_field_value("group_concat(b.sub_process_id)  as sub_process_id ","pro_recipe_entry_dtls b, pro_recipe_entry_mst a","a.working_company_id=$data[0] and a.id=b.mst_id and b.ratio>0 and a.id in($data[1]) and b.status_active=1 and b.is_deleted=0",'sub_process_id');
	}
	else if($db_type==2)
	{
	//echo "select  listagg(b.sub_process_id,',') within group (order by b.sub_process_id 	) as sub_process_id from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.working_company_id=$data[0] and b.ratio>0 and a.id=b.mst_id and a.id in($data[1]) and b.status_active=1 and b.is_deleted=0  and b.ratio>0 and a.id=b.mst_id and a.id in($data[1]) and b.status_active=1 and b.is_deleted=0";
		$sub_process_id=return_field_value("listagg(b.sub_process_id,',') within group (order by b.sub_process_id 	) as sub_process_id ","pro_recipe_entry_dtls b, pro_recipe_entry_mst a","a.working_company_id=$data[0] and b.ratio>0 and a.id=b.mst_id and a.id in($data[1]) and b.status_active=1 and b.is_deleted=0",'sub_process_id');
	}
	echo create_drop_down( "cbo_sub_process", 170, $dyeing_sub_process,"", 1, "-- Select--", "", "fnc_item_details(this.value,'','')",$is_posted_account,$sub_process_id,'','',31 ); 	 
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

$liquior_arr=return_library_array("select id,total_liquor from pro_recipe_entry_mst where status_active=1 and is_deleted=0" ,"id","total_liquor");
$rece_arr = return_library_array("select id,labdip_no from pro_recipe_entry_mst","id","labdip_no");
$po_number_arr=return_library_array("select id,po_number from wo_po_break_down","id","po_number");
$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
$po_number_sub_con=return_library_array("select id,order_no from subcon_ord_dtls","id","order_no");

if($db_type==0)
{
	$machine_name_arr=return_library_array( "select id, concat(machine_no, '-', brand) as machine_name from lib_machine_name where is_deleted=0", "id", "machine_name"  );
}
else
{
	$machine_name_arr=return_library_array( "select id, machine_no || '-' || brand as machine_name from lib_machine_name where is_deleted=0", "id", "machine_name"  );
}
//$po_number_sub_con=return_field_value("order_no","subcon_ord_dtls","id='$po_no' and status_active=1 and is_deleted=0 group by order_no",'order_no');
$req_arr = return_library_array("select id,requ_no from dyes_chem_issue_requ_mst","id","requ_no");



if($action=="req_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
		
		var data=mrr.split("_")
 		$("#hidden_requ_no").val(data[0]); // mrr number
		$("#hidden_requ_id").val(data[1]); // mrr number
		$("#hidden_batch_id").val(data[2]); // mrr number
		$("#hidden_receipe_id").val(data[3]); // mrr number
		$("#hidden_mc_id").val(data[4]);
		$("#hidden_mc_name").val(data[5]);
		$("#hidden_lc_company_id").val(data[6]);


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

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="780" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
              <thead>
                     <th width="150" colspan="2"> </th>
                        <th>
                          <?
                           echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                          ?>
                        </th>
                      <th width="150" colspan="2"> </th>
            </thead>
            <thead>
                <tr>                	 
                    <th width="150">Company Name</th>
                    <th width="120" align="center" id="">Requisition No</th>
                   
                    <th width="200">Issue Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?  
                            
						  echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1 );
                        ?>
                    </td>
                    <td width="" align="center" id="">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_requisition_no" id="txt_requisition_no" />	
                    </td> 
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_requisition_no').value+'_'+<?  echo $issue_purpose;  ?>+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<?  echo $issue_basis;  ?>, 'create_reqisition_search_list_view', 'search_div', 'chemical_dyes_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />				
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
	$batch_data = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where working_company_id=$company and batch_against<>0");
	foreach($batch_data as $row)
	{
		$batch_arr[$row[csf('id')]]=$row[csf('batch_no')];
		$batchExt_arr[$row[csf('id')]]=$row[csf('extention_no')];
	}
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	
	$arr=array(1=>$company_arr,3=>$receive_basis_arr,4=>$batch_arr,5=>$receipe_arr);

	if($db_type==0) $null_cond="''";
	else if($db_type==2) $null_cond="'0'";
	if($ex_data[4]==13)
	{
		$sql ="select a.requ_no,a.requ_prefix_num,a.company_id,a.requisition_date,a.requisition_basis,a.batch_id,a.recipe_id,a.id, a.machine_id
	from dyes_chem_issue_requ_mst a where a.company_id=$company and a.recipe_id=$null_cond and a.is_apply_last_update!=2 $sql_cond $req_cond $year_cond";
	//echo "select a.requ_no,a.requ_prefix_num,a.company_id,a.requisition_date,a.requisition_basis,a.batch_id,a.recipe_id,a.id, a.machine_id
	//from dyes_chem_issue_requ_mst a where a.working_company_id=$company and a.recipe_id=$null_cond $sql_cond $req_cond $year_cond";
		
	}
	else
	{
		$sql ="select a.requ_no,a.requ_prefix_num,a.company_id,a.requisition_date,a.requisition_basis,a.batch_id,a.recipe_id,a.id, a.machine_id 
	from dyes_chem_issue_requ_mst a where a.company_id=$company and a.is_apply_last_update!=2  and  a.recipe_id!=$null_cond $sql_cond $req_cond $year_cond";
	}
	//echo $sql;
	$result_sql =sql_select($sql);
	 
	$recipe_arr=sql_select("select a.id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.working_company_id=$company");
	foreach($recipe_arr as $row)
	{
		$receipe_data_arr[$row[csf('id')]]=$row[csf('company_id')];
	}
	//echo "select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.working_company_id=$company";
	/*$sql =sql_select( "select a.requ_no,a.requ_prefix_num,a.company_id,a.requisition_date,a.requisition_basis,a.batch_id,a.recipe_id,a.id from dyes_chem_issue_requ_mst a,
	pro_recipe_entry_mst b,pro_recipe_entry_dtls c
	where a.company_id=$company and  b.id=c.mst_id   and c.sub_process_id=$txt_sub_process $sql_cond $req_cond $find_inset
	group by a.requ_no,a.requ_prefix_num,a.company_id,a.requisition_date,a.requisition_basis,a.batch_id,a.recipe_id,a.id");	*/
	//and c.sub_process_id=$txt_sub_process
	if($txt_sub_process!=0)
	{
		$sub_process_cond="and c.sub_process_id=$txt_sub_process";
	}
	else
	{
		$sub_process_cond="";
	}
	
	/*$sql =sql_select( "select a.requ_no,a.requ_prefix_num,a.company_id,a.requisition_date,a.requisition_basis,a.batch_id,a.recipe_id,a.id from dyes_chem_issue_requ_mst a,
	pro_recipe_entry_mst b,pro_recipe_entry_dtls c
	where a.company_id=$company and  b.id=c.mst_id  $sub_process_cond $sql_cond $req_cond $find_inset
	group by a.requ_no,a.requ_prefix_num,a.company_id,a.requisition_date,a.requisition_basis,a.batch_id,a.recipe_id,a.id");
	echo "select a.requ_no,a.requ_prefix_num,a.company_id,a.requisition_date,a.requisition_basis,a.batch_id,a.recipe_id,a.id from dyes_chem_issue_requ_mst a,
	pro_recipe_entry_mst b,pro_recipe_entry_dtls c
	where a.company_id=$company and  b.id=c.mst_id    $sub_process_cond $sql_cond $req_cond $find_inset
	group by a.requ_no,a.requ_prefix_num,a.company_id,a.requisition_date,a.requisition_basis,a.batch_id,a.recipe_id,a.id";*/
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="120">Company Name</th>
                <th width="100">Requisition No</th>
                <th width="120">Requisition Basis</th>
                <th width="150">Recipe </th>
                <th width="150">Batch No</th>
                <th width="">Requisition Date</th>
            </thead>
    </table>
   <div style="width:820px; overflow-y:scroll; max-height:230px; cursor:pointer" id="buyer_list_view" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table" id="tbl_list_search" >
            <tbody>
            	<? 
				   $i=1;
				   foreach($result_sql as $row)
				   {  
					$receipe_id=$row[csf('recipe_id')];
					$batch_id=explode(",",$row[csf('batch_id')]);
					$batch_id_all="";
					$recipe_id_all="";
					foreach($batch_id as $b_id)
					{
						if($batchExt_arr[$b_id]>0) $ext="-".$batchExt_arr[$b_id]; else $ext="";

						if($batch_id_all!="") $batch_id_all.=",".$batch_arr[$b_id].$ext;
						else $batch_id_all=$batch_arr[$b_id].$ext;
					}
					foreach($receipe_id as $r_id)
					{
						if($recipe_id_all!="") $recipe_id_all.=",".$receipe_arr[$r_id];
						else $recipe_id_all=$receipe_arr[$r_id];
					}
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";
					$recipe_ids=array_unique(explode(",",$row[csf("recipe_id")]));
					$recipe_ids=$recipe_ids[0];
					//echo $ex_data[4].'=='.$row[csf("requisition_basis")];
					if($ex_data[4]==13 && $issue_basis==7) 
					{
						$company_id=$row[csf("company_id")];
					}
					else
					{
						$company_id=$receipe_data_arr[$recipe_ids];
					}
					//echo $row[csf('machine_id')].'DDD'; //
				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('requ_no')]."_".$row[csf('id')]."_".$row[csf('batch_id')]."_".$row[csf('recipe_id')]."_".$row[csf('machine_id')]."_".$machine_name_arr[$row[csf('machine_id')]]."_".$company_id; ?>')">
                		<td width="30"><? echo $i; ?></td>
                    	<td width="120" align="center"><? echo $company_arr[$row[csf("company_id")]]; ?></td>
                        <td width="100" align="center" ><? echo $row[csf('requ_prefix_num')]; ?></td>
                        <td  width="120" align="center"><? echo $receive_basis_arr[$row[csf('requisition_basis')]]; ?></td>
                    	<td width="150" align="center"><? echo $row[csf("recipe_id")]; ?></td>
                        <td width="150" align=""><? echo $batch_id_all; ?></td>
                    	<td width=""  align="center"><? echo change_date_format($row[csf("requisition_date")]); ?></td>
                    </tr>
                <? $i++; } ?>
            </tbody>
        </table>
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
							echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "" );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_company_name').value, 'create_batch_search_list_view', 'search_div', 'chemical_dyes_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$store_arr=return_library_array( "select a.id as id,a.store_name  as store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name", "id", "store_name");
	?>
	<div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1310" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="120">Store Name</th>
                <th width="50">Product ID</th>
                <th width="100">Item Cat.</th>
                <th width="100">Group</th>
                <th width="100">Sub Group</th>
                <th width="140">Item Description</th>
                <th width="32">UOM</th>
                <th width="100">Dose Base</th>
                <th width="50">Ratio</th>
                <th width="70">Stock Qty</th>
                <th width="73">Recipe Qnty.</th>
                <th width="30">Adj%.</th>
                <th width="90">Adj. Type</th>
                <th width="90">Issue Qnty.</th>
                <th>Remarks</th>
            </thead>
        </table>
        <div style="width:1310px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1192" class="rpt_table" id="tbl_list_search">
                <tbody>
                <?
                if($is_update=="")
                {
						
					   $total_issue_sql=sql_select("select a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit 
					   from inv_issue_master a, dyes_chem_issue_dtls b 
					   where a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and a.req_no=$requsn_id and
						b.sub_process='0'");
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
						
					  $sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description,a.item_size, a.unit_of_measure,
					  1 as dose_base,b.id as dtls_id,a.current_stock, b.dose_base as dose_base_curr, b.ratio, b. recipe_qnty, b.required_qnty,
					  b.req_qny_edit 
					  from product_details_master a ,dyes_chem_issue_requ_dtls b 
					  where a.id=b.product_id and b.mst_id=$requsn_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and 
					  a.item_category_id in (5,7) and a.status_active=1 and a.is_deleted=0 order by b.id";
						
						//echo $sql;die;
						$i=1;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $selectResult)
						{
	                       
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$req_qnty=$selectResult[csf('req_qny_edit')]-$total_issue_arr[$selectResult[csf("id")]];
							if(def_number_format($req_qnty,5,"")>0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
								<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td> 
								<td width="120" id="store_td" >
								<? 
								if(str_replace("'","",$cbo_store)!=0)
									  { 
									  echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --", $cbo_store, "",0 ); 
									  }
								else
									  {
									  echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --",0, "",0 ); 	
									  }
								?>
								</td>
								<td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
								</td>
								<td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
								<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
								</td>
								<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
								<td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
								<td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
								<td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
								<td width="100" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 100, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base_curr')],"calculate_receipe_qty($i)",1); ?></td>
								<td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($selectResult[csf('ratio')],5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
								<td width="70" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $selectResult[csf('current_stock')]; ?>"  disabled></td>
								<td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('recipe_qnty')],5,""); ?>" disabled></td>
								<td width="30" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:28px"  value="<? //echo $ratio; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
								<td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -","","calculate_receipe_qty($i)",1); ?></td>
	  
								<td width="90" align="center" id="reqn_qnty_<? echo $i; ?>">
								<input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($req_qnty,5,""); ?>" readonly>
								<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
								<input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
								<input type="hidden" name="stock_check[]" id="stock_check_<? echo $i; ?>" value="<? echo $selectResult[csf('current_stock')]; ?>">
								
								<input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $selectResult[csf('current_stock')]+$req_qnty; ?>)"  value="<? echo def_number_format($selectResult[csf('required_qnty')],5,""); ?>"  >
								<input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($req_qnty,5,""); ?>" / >
								
								</td>
                                <td align="center" id="remarks_<? echo $i; ?>">
	                            <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="" />
								</td>
								
								</tr>
								<?
								$i++;
							}
						}
					    echo '<input type="hidden"  id="txt_isu_qnty" value="'.$issue_total_val.'" >';
						echo '<input type="hidden"  id="txt_isu_num" value="'.$issue_num_all.'" >';
                }
                else
                {
					$sql="select a.id,t.store_id,a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure,a.current_stock, b.id as dtls_id,b.trans_id, b.dose_base,b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,t.remarks from product_details_master a , dyes_chem_issue_dtls b,inv_transaction t where a.id=b.product_id and b.trans_id=t.id and b.mst_id=$is_update and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,7) and a.status_active=1 and a.is_deleted=0 order by a.item_category_id";
					
					//echo $sql;
					$i=1;
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $selectResult)
					{
					if ($i%2==0)  
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
						<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
						<td width="120" id="store_td" >
						<? 
                        if(str_replace("'","",$selectResult[csf('store_id')])!=0)
					    { 
						echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --", $selectResult[csf('store_id')], "",0 ); 
					    }
                        else
					    {
					    echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --",0, "",0 ); 	
					    }
                        ?>
							
						</td>	
						<td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
						<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
						</td>
						<td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
						<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
						</td>
						<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
						<td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
						<td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
						<td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
						<td width="100" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 100, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1); ?></td>
						<td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($selectResult[csf('ratio')],5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
						<td width="70" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $selectResult[csf('current_stock')]; ?>"  disabled></td>
						
						<td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('recipe_qnty')],5,""); ?>" disabled></td>
						<td width="30" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
						<td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)",1); ?></td>
						<td width="90" align="center" id="reqn_qnty_<? echo $i; ?>">
						<input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('required_qnty')],5,""); ?>" readonly>
						<input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $selectResult[csf('current_stock')]+$selectResult[csf('req_qny_edit')]; ?>)"  / >
						<input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>" / >
						<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
						<input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
						</td>
                        <td align="center" id="remarks_<? echo $i; ?>">
                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="<? echo $selectResult[csf('remarks')]; ?>" />
                        </td>
						</tr>
					<?
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





if($action=="item_details")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$sub_process_id=$data[1];
	$receipe_id=$data[2];
	$issue_basis=$data[5];
	$is_update=$data[6];
	$req_id=$data[7];
	$cbo_store=$data[8];
	$is_posted_account=$data[9];
  
	//echo $req_id;die;
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$store_arr=return_library_array( "select a.id as id,a.store_name  as store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name", "id", "store_name"  );
	?>
	<div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1310" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="120">Store Name</th>
                <th width="50">Product ID</th>
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
                 <th width="90">Issue Qnty.</th>
                <th>Remarks</th>
            </thead>
        </table>
        <div style="width:1310px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1292" class="rpt_table" id="tbl_list_search">
                <tbody>
                <?
                if($is_update=="")
                {
					if($issue_basis==4)
					{
						//$cbo_store
						$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock 
						from product_details_master a, inv_transaction b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(5,6,7,23) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 
						group by a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock 
						order by a.item_category_id ";	
						//echo $sql;die;
						$i=1;
						$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $selectResult)
						{
							
							//$totalIssued = return_field_value("sum(b.cons_quantity) as to_issue","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7) and b.transaction_type=2 group by prod_id","to_issue");
	                       // $totalIssuedReturn = return_field_value("sum(b.cons_quantity+b.cons_reject_qnty) as to_issue_return","inv_receive_master a, inv_transaction b"," a.id=b.mst_id and b.prod_id='".$row[csf("prod_id")]."' and b.item_category  in(5,6,7) and b.transaction_type=4 group by prod_id","to_issue_return");
	                        $issue_remain=$totalIssued-$totalIssuedReturn;
							
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]]['stock'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                            <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td> 
                            <td width="120" id="store_td" >
							<? 
							//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
                            if(str_replace("'","",$cbo_store)!=0)
                                  { 
                                  echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --", $cbo_store, "",1 ); 
                                  }
                            else
                                  {
                                  echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --",0, "",1 ); 	
                                  }
                            ?>
                            </td>
							<td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
							<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
							</td>
							<td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
							<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
							</td>
							<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
							<td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
							<td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
							<td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
							<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1); ?></td>
							<td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($ratio,5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
                            <td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
							<td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($recipe_qnty,5,""); ?>" disabled></td>
							<td width="55" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? //echo $ratio; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
							<td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -","","calculate_receipe_qty($i)",1); ?></td>
  
							<td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
							<input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($recipe_qnty,5,""); ?>" readonly>
                            <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
							<input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                            <input type="hidden" name="stock_check[]" id="stock_check_<? echo $i; ?>" value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>">
                            
							<input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty; ?>)"  value="<? //echo def_number_format($recipe_qnty,5,""); ?>" >
							<input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>" / >
							
							
							</td>
                            <td align="center" id="remarks_<? echo $i; ?>">
	                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="" />
	                         </td>
							
							</tr>
							<?
							$i++;
						}
					}
					if($issue_basis==5)
					{
						$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock 
						from product_details_master a, inv_transaction b  
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(5,6,7,23) and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 
						group by a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock 
						order by a.item_category_id ";	
					
						$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
						$i=1;
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $selectResult)
						{
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]]['stock'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
							<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                            <td width="120" id="store_td" >
								<? 
                                 if(str_replace("'","",$cbo_store)!=0)
								      { 
                                      echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --", $cbo_store, "",1 ); 
								      }
							     else
									  {
									  echo create_drop_down( "cbo_store_name_$i", 120,$store_arr,"", 1, "-- Select --",0, "",1 ); 	
									  }
                                ?>
                            </td>	
							<td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
							<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
							</td>
							<td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
							<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
							</td>
							<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
							<td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
							<td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
							<td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
							<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)"); ?></td>
							<td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? //echo def_number_format($ratio,5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)"/></td>
                            <td width="70" title="<? echo $stock_qty;?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
							<td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? //echo def_number_format($recipe_qnty,5,""); ?>" onClick="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)"  readonly /></td>
							<td width="55" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? //echo $ratio; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" ></td>
							<td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -","","calculate_receipe_qty($i)"); ?></td>
							<td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
							<input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? //echo def_number_format($recipe_qnty,5,""); ?>" readonly>
							<input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? //echo def_number_format($recipe_qnty,5,"");  ?>"  >
							<input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? //echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>" / >
							
							<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
							<input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
							</td>
                            <td align="center" id="remarks_<? echo $i; ?>">
	                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="" />
	                         </td>
							
							</tr>
							<?
							$i++;
						}
					}
					if($issue_basis==7)
					{
						$total_issue_sql=sql_select("select a.issue_number, b.product_id, b.req_qny_edit as req_qny_edit from inv_issue_master a, dyes_chem_issue_dtls b where a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and a.req_no='".$req_id."' and b.sub_process=$sub_process_id");
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
						
						$sql="select a.id,a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure,a.current_stock, b.id as dtls_id, b.dose_base, b.ratio,recipe_qnty,adjust_percent,adjust_type,required_qnty,req_qny_edit 
						from product_details_master a, dyes_chem_issue_requ_dtls b 
						where a.id=b.product_id and b.mst_id='".$req_id."' and b.sub_process=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 order by a.item_category_id ";
						//echo $sql;
						$is_disabled=1;
						$i=1;
						
						$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
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
							$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]]['stock'];
							if(def_number_format($req_qnty,5,"")>0)
							{
							?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
                                    <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                    <td width="120" id="store_td" >
                                    <? 
                                    if(str_replace("'","",$cbo_store)!=0)
                                    { 
                                    echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --", $cbo_store, "",1 ); 
                                    }
                                    else
                                    {
                                    echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --",0, "",1 ); 	
                                    }
                                    ?>
                                    </td>	
                                    <td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                    <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
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
                                    <td width="70" title="<? echo $stock_qty;?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                    <td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('recipe_qnty')],5,""); ?>"  onClick="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)" readonly></td>
                                    <td width="55" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" readonly></td>
                                    <td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)",1); ?></td>
                                    <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                    <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($req_qnty,5,""); ?>" readonly>
                                    <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($req_qnty,5,""); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)"  / >
                                    <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($req_qnty,5,""); ?>" readonly / >
                                    
                                    <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                    <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                    </td>
                                    <td align="center" id="remarks_<? echo $i; ?>">
	                                    <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="" />
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
					$sql="select a.id,t.store_id,a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure,a.current_stock, b.id as dtls_id,b.trans_id, b.dose_base, b.ratio,recipe_qnty,adjust_percent,adjust_type,required_qnty,req_qny_edit,t.remarks  
					from product_details_master a, dyes_chem_issue_dtls b, inv_transaction t 
					where a.id=b.product_id and b.trans_id=t.id and b.mst_id=$is_update and b.sub_process=$sub_process_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 order by a.item_category_id";
					
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
						$stock_qty=$stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]]['stock'];
						if($issue_basis==4)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
							<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
							<td width="120" id="store_td" >
							<? 
							if(str_replace("'","",$selectResult[csf('store_id')])!=0)
							{ 
							echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --", $selectResult[csf('store_id')], "",1 ); 
							}
							else
							{
							echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --",0, "",1 ); 	
							}
							?>
							<input type="hidden" name="txt_store_update_id[]" id="txt_store_update_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
							</td>	
							<td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
							<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
							</td>
							<td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
							<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
							</td>
							<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
							<td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
							<td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
							<td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
							<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)",1); ?></td>
							<td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($selectResult[csf('ratio')],5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
							<td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
							
							<td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('recipe_qnty')],5,""); ?>" disabled></td>
							<td width="55" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" disabled></td>
							<td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)",1); ?></td>
							<td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
							<input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('required_qnty')],5,""); ?>" readonly>
							<input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty+$selectResult[csf('req_qny_edit')];//$selectResult[csf('current_stock')]+$selectResult[csf('req_qny_edit')]; ?>)"  <? echo $desable_cond; ?> />
							<input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>" / >
							<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
							<input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
							</td>
                             <td align="center" id="remarks_<? echo $i; ?>">
	                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="<? echo $selectResult[csf('remarks')]; ?>" />
	                         </td>
							</tr>
						<?
						}
						if($issue_basis==5)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
							<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
							<td width="120" id="store_td" >
							<? 
							if(str_replace("'","",$selectResult[csf('store_id')])!=0)
							{ 
							echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --", $selectResult[csf('store_id')], "",1 ); 
							}
							else
							{
							echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --",0, "",1 ); 	
							}
							?>
							  
							</td>	
							<td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
							<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
							</td>
							<td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
							<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
							</td>
							<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
							<td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
							<td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
							<td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
							<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "- Select Dose Base -",$selectResult[csf('dose_base')],"calculate_receipe_qty($i)"); ?></td>
							<td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo def_number_format($selectResult[csf('ratio')],5,""); ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" ></td>
							<td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>"  disabled></td>
							<td width="70" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo def_number_format($selectResult[csf('recipe_qnty')],5,""); ?>" onClick="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty;//$selectResult[csf('current_stock')]; ?>)" readonly ></td>
							<td width="55" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $selectResult[csf('adjust_percent')]; ?>" onChange="calculate_receipe_qty(<? echo $i; ?>)" ></td>
							<td width="90" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 90, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')],"calculate_receipe_qty($i)"); ?></td>
							<td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
							<input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('required_qnty')],5,""); ?>" readonly>
							<input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>"  onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty+$selectResult[csf('req_qny_edit')];//$selectResult[csf('current_stock')]+$selectResult[csf('req_qny_edit')]; ?>)" <? echo $desable_cond; ?> / >
							<input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>" / >
							<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
							<input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
							</td>
                            <td align="center" id="remarks_<? echo $i; ?>">
	                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="<? echo $selectResult[csf('remarks')]; ?>" />
	                                </td>
							</tr>
						<?
						}
						if($issue_basis==7)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
							<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
							<td width="120" id="store_td" >
								<? 
								 if(str_replace("'","",$selectResult[csf('store_id')])!=0)
									  { 
									  echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --", $selectResult[csf('store_id')], "",1 ); 
									  }
								else
									  {
									   echo create_drop_down( "cbo_store_name_$i", 120,  $store_arr,"", 1, "-- Select --",0, "",1 ); 	
									  }
								?>
							   
							</td>	
							<td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
							<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
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
							<input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo def_number_format($selectResult[csf('req_qny_edit')],5,""); ?>"  onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $stock_qty+$selectResult[csf('req_qny_edit')];//$selectResult[csf('current_stock')]+$selectResult[csf('req_qny_edit')]; ?>)"   <? echo $desable_cond; ?> / >
							<input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo $selectResult[csf('req_qny_edit')]; ?>" readonly  / >
							<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
							<input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
							</td>
                             <td align="center" id="remarks_<? echo $i; ?>">
	                                <input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i; ?>" class="text_boxes" style="width:80%"  value="<? echo $selectResult[csf('remarks')]; ?>" />
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
	$sql="select b.id, b.sub_process as sub_process_id, a.store_id from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id and a.transaction_type=2 and b.mst_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id";
	$nameArray=sql_select( $sql );
	$process_array=array();
	foreach($nameArray as $inf)
	{
		/*if (!in_array( $inf[csf("sub_process_id")],$process_array) )
		{
			$process_array[]=$inf[csf("sub_process_id")];
		}*/
		
		$process_array[$inf[csf("sub_process_id")]]["sub_process_id"]=$inf[csf("sub_process_id")];
		$process_array[$inf[csf("sub_process_id")]]["store_id"]=$inf[csf("store_id")];
	}
    foreach($process_array as $sub_process_val)
	{
	?>
        <h3 align="left" id="accordion_h<? echo $sub_process_val["sub_process_id"]; ?>" style="width:910px" class="accordion_h" onClick="fnc_item_details(<? echo $sub_process_val["sub_process_id"];?>,<? echo $data ?>,<? echo $sub_process_val["store_id"] ?>)"><span style="display:none" id="accordion_h<? echo $sub_process_val["sub_process_id"]; ?>id"><? echo $sub_process_val["sub_process_id"]; ?></span><span id="accordion_h<? echo $sub_process_val["sub_process_id"]; ?>span">+</span><? echo $dyeing_sub_process[$sub_process_val["sub_process_id"]]; ?></h3>
	<?
	}
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
                            $search_by = array(1=>'Issue No',2=>'Challan No');
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td align="center" id="search_by_td">				
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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('cbo_year_selection').value, 'create_mrr_search_list_view', 'search_div', 'chemical_dyes_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
	$batch_no = str_replace("'","",$ex_data[5]);
	$yearid = str_replace("'","",$ex_data[6]);
	$batch_id=return_field_value("id as batch_id" ,"pro_batch_create_mst","batch_no like '$batch_no' and status_active=1 and is_deleted=0","batch_id");
	//echo $batch_id;
	//if($batch_id=="") $batch_id=0;
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
	$sql_cond="";
	
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
	
	$batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where batch_against<>0 ","id","batch_no");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$arr=array(1=>$company_arr,3=>$receive_basis_arr,4=>$batch_arr,5=>$receipe_arr);
		if($db_type==0) $year_cond=" and year(a.insert_date)=$yearid"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$yearid'";

	$sql = sql_select("select a.issue_number_prefix_num, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.batch_no, a.issue_date, a.id, $year_id,
	 a.is_posted_account 
	 from inv_issue_master a where a.company_id=$company and a.entry_form=5 $sql_cond  $batch_cond $year_cond order by a.id");
	//echo $sql;
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


if($action=="populate_batch_receipe_data")
{
	$data=explode("**",$data);
	
	$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in($data[2]) and working_company_id=$data[0]","total_liquor");
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
		
	}
	if($db_type==0)
	{
	$sql = sql_select("select sum(batch_weight)  as batch_weight,group_concat(batch_no) as batch_no from pro_batch_create_mst where working_company_id=$data[0] and id in ($data[1]) and status_active=1 and is_deleted=0 and batch_against<>0"); 
	}
	if($db_type==2)
	{
	$sql = sql_select("select sum(batch_weight) as batch_weight, listagg(cast(batch_no as varchar2(500)),',') within group (order by batch_no)  as batch_no from pro_batch_create_mst where working_company_id=$data[0] and id in ($data[1]) and status_active=1 and is_deleted=0 and batch_against<>0"); 
	}
	
	foreach($sql as $row)
	{
		echo "document.getElementById('txt_batch_no').value = '".$row[csf("batch_no")]."';\n"; 
		echo "document.getElementById('txt_batch_weight').value = '".$total_recipe_batch."';\n"; 
	}
	echo "document.getElementById('txt_tot_liquor').value = '".$total_liquor."';\n";
}
if($action=="populate_buyer_order_for_batch")
{
	$data=explode("**",$data);
	$sql = sql_select("select id, batch_no,color_range_id,booking_without_order, batch_weight, total_liquor,booking_no,entry_form from pro_batch_create_mst where id=$data[1] and status_active=1 and is_deleted=0 and batch_against<>0"); //company_id=$data[0] and 
	//echo "select id, batch_no,color_range_id, batch_weight, total_liquor,booking_no,entry_form from pro_batch_create_mst where id=$data[1] and status_active=1 and is_deleted=0 and batch_against<>0";
	$po_sqls=sql_select("Select id as po_id,grouping as ref_no,file_no from  wo_po_break_down where  status_active=1 and is_deleted=0");
	$po_data_arr=array();
	foreach($po_sqls as $row)
	{
		$po_data_arr[$row[csf('po_id')]]['ref']=$row[csf('ref_no')];
		$po_data_arr[$row[csf('po_id')]]['file']=$row[csf('file_no')];
	}
	foreach($sql as $row)
	{
		$po_no="";$file_no="";$ref_no="";
		if ($row[csf("booking_no")]!='' || $row[csf("booking_no")]!=0)
		{
			if( $row[csf("booking_without_order")]==0)
			{
				$cbo_buyer_name=return_field_value("buyer_id","wo_booking_mst","booking_no='".$row[csf("booking_no")]."'")."";
			}
			else
			{
				$cbo_buyer_name=return_field_value("buyer_id","wo_non_ord_samp_booking_mst","booking_no='".$row[csf("booking_no")]."'")."";
			}
		}
		$po_sql=sql_select("Select po_id from  pro_batch_create_dtls where mst_id=".$row[csf("id")]."");
		foreach(array_unique($po_sql) as $po_row)
		{   
			if($row[csf("entry_form")]==36)
			{
			$po_no.=$po_number_sub_con[$po_row[csf("po_id")]].",";
			}
			else
			{
			$po_no.=$po_number_arr[$po_row[csf("po_id")]].",";	
			$file_no.=$po_data_arr[$po_row[csf("po_id")]]['file'].",";	
			$ref_no.=$po_data_arr[$po_row[csf("po_id")]]['ref'].",";	
			}
		}
		echo "document.getElementById('cbo_buyer_name').value = '".$cbo_buyer_name."';\n";
		echo "document.getElementById('txt_order_no').value = '".rtrim($po_no,',')."';\n";
		echo "document.getElementById('hidden_order_id').value = '".$po_break_down_id."';\n";
		echo "document.getElementById('txt_color_range').value = '".$color_range[$row[csf("color_range_id")]]."';\n";
		echo "document.getElementById('txt_file_no').value = '".rtrim($file_no,',')."';\n";
		echo "document.getElementById('txt_ref_no').value = '".rtrim($ref_no,',')."';\n";
	}
}

//for machine wash data
if($action=="populate_machine_wish_data")
{
	//$data=explode("**",$data);
	//$sql_machine_data=sql_select("select machine_id, tot_liquor from dyes_chem_issue_requ_mst where company_id=$data[0] and id=$data[1] and status_active=1 and is_deleted=0");
	$sql_machine_data=sql_select("select machine_id, tot_liquor from dyes_chem_issue_requ_mst where id=$data and status_active=1 and is_deleted=0");
	foreach($sql_machine_data as $value)
	{
		//$machine_name=return_field_value("machine_no","lib_machine_name","id=".$value[csf('machine_id')]);
		$machine_name=$value[csf("machine_id")];
		echo "document.getElementById('txt_machine_name').value = '".$machine_name."';\n";
		echo "document.getElementById('txt_tot_liquor').value = '".$value[csf("tot_liquor")]."';\n";	
		echo "document.getElementById('txt_machine_id').value = '".$value[csf("machine_id")]."';\n";
		
	}
	
}


if($action=="populate_buyer_order_no_data")
{
	$data=explode("**",$data);
	
	$po_sqls=sql_select("Select id as po_id,grouping as ref_no,file_no from  wo_po_break_down where  status_active=1 and is_deleted=0");
	$po_data_arr=array();
	foreach($po_sqls as $row)
	{
		$po_data_arr[$row[csf('po_id')]]['ref']=$row[csf('ref_no')];
		$po_data_arr[$row[csf('po_id')]]['file']=$row[csf('file_no')];
	}
	
	if($db_type==0)
	{
	 $buyer_id=return_field_value("buyer_id","pro_recipe_entry_mst","working_company_id=$data[0] and id in($data[2]) limit 1");
	 $bookint_type=return_field_value("booking_without_order","pro_batch_create_mst","working_company_id=$data[0] and id in($data[1])  limit 1");
	 $sql = sql_select("select a.entry_form,a.color_range_id,group_concat(b.po_id) as po_id 	 from pro_batch_create_mst a,pro_batch_create_dtls b where a.working_company_id=$data[0] and a.id in($data[1]) and a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and a.batch_against<>0 group by a.entry_form,a.color_range_id"); 
	}
	if($db_type==2)
	{
	$sql = sql_select("select a.entry_form,a.color_range_id,listagg(cast(b.po_id as varchar2(1000)),',') within group (order by b.po_id 	) as po_id 	 from pro_batch_create_mst a,pro_batch_create_dtls b where a.working_company_id=$data[0] and a.id in($data[1]) and a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and a.batch_against<>0 group by a.entry_form,a.color_range_id"); 
	 $buyer_id=return_field_value("buyer_id","pro_recipe_entry_mst","working_company_id=$data[0] and id in($data[2]) and rownum=1");
	 $bookint_type=return_field_value("booking_without_order","pro_batch_create_mst","working_company_id=$data[0] and id in($data[1]) and rownum=1");
	}
		foreach($sql as $po_row)
		{
			$po_no=explode(",",$po_row[csf("po_id")]);
			$file_no='';$ref_no='';
			foreach(array_unique($po_no) as $inf)
			{  
			   if($po_row[csf("entry_form")]==36)
			   {
			   if($po_number!="") $po_number.=",".$po_number_sub_con[$inf];
			   else               $po_number.=$po_number_sub_con[$inf];
			   }
			   else
			   {
			   if($po_number!="") $po_number.=",".$po_number_arr[$inf];
			   else               $po_number.=$po_number_arr[$inf];  
			   if($file_no!='') $file_no.=",".$po_data_arr[$inf]['file']; else  $file_no=$po_data_arr[$inf]['file'];	
			   if($ref_no!='') $ref_no.=",".$po_data_arr[$inf]['ref'].","; else  $ref_no=$po_data_arr[$inf]['ref'];	
			   }
			}
			$po_id=implode(",",array_unique(explode(",",$po_row[csf("po_id")])));
			echo "document.getElementById('txt_order_no').value = '".trim($po_number)."';\n";
			echo "document.getElementById('hidden_order_id').value = '".$po_id."';\n";
			echo "document.getElementById('txt_color_range').value = '".$color_range[$po_row[csf("color_range_id")]]."';\n";
			echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
			echo "document.getElementById('txt_ref_no').value = '".$ref_no."';\n";
		}
		echo "document.getElementById('hidden_booking_type').value = '".$bookint_type."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$buyer_id."';\n";
}

if($action=="populate_data_from_data")
{
	$po_sqls=sql_select("Select id as po_id,grouping as ref_no,file_no from  wo_po_break_down where  status_active=1 and is_deleted=0");
	$po_data_arr=array();
	foreach($po_sqls as $row)
	{
		$po_data_arr[$row[csf('po_id')]]['ref']=$row[csf('ref_no')];
		$po_data_arr[$row[csf('po_id')]]['file']=$row[csf('file_no')];
	}
	$batch_arr_color_range=return_library_array( "select  id,color_range_id from  pro_batch_create_mst", "id", "color_range_id"  );

	
	$sql = sql_select("select location_id, issue_date, issue_basis, req_no ,machine_id, batch_no, issue_purpose, company_id, loan_party, knit_dye_source, knit_dye_company, challan_no, lap_dip_no, order_id, buyer_id, style_ref, store_id, buyer_job_no, is_posted_account, remarks, lc_company from inv_issue_master where id=$data and entry_form=5");
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
		echo "document.getElementById('txt_req_no').value = '".$req_arr[$row[csf("req_no")]]."';\n"; 
		echo "document.getElementById('txt_req_id').value = '".$row[csf("req_no")]."';\n"; 
		echo "document.getElementById('txt_batch_id').value = '".$row[csf("batch_no")]."';\n";
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("lc_company")]."';\n"; 
		 
		echo "document.getElementById('cbo_issue_purpose').value = '".$row[csf("issue_purpose")]."';\n";
		if($row[csf("store_id")]=='' || $row[csf("store_id")]==0) $row[csf("store_id")]=0;else $row[csf("store_id")]=$row[csf("store_id")];
		echo "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("knit_dye_company")]."', 'load_drop_down_location', 'location_td');\n"; 
		echo  "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("knit_dye_company")]."', 'load_drop_down_loan_party', 'loan_party_td');\n";
		echo  "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("knit_dye_company")]."', 'load_drop_down_store', 'store_td');\n";   
		echo "document.getElementById('cbo_loan_party').value = '".$row[csf("loan_party")]."';\n"; 
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_id")]."';\n";  
		echo "document.getElementById('cbo_dying_source').value = '".$row[csf("knit_dye_source")]."';\n";
		 
		echo "set_dying_company(".$row[csf("knit_dye_source")].");\n";
		echo "document.getElementById('cbo_dying_company').value = '".$row[csf("knit_dye_company")]."';\n"; 
		echo "document.getElementById('txt_challan_no').value = '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_return').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_store_name').value = '".$row[csf("store_id")]."';\n";
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
			echo "disable_enable_fields( 'cbo_company_name*cbo_issue_basis*txt_issue_date*txt_challan_no*cbo_dying_source*cbo_sub_process*cbo_dying_company', 1, '', '' );\n"; // disable true
		}
		else
		{
			echo "$('#cbo_issue_basis').attr('disabled',false);\n";
			echo "$('#txt_issue_date').attr('disabled',false);\n";
			echo "$('#txt_challan_no').attr('disabled',false);\n";
			echo "$('#cbo_dying_source').attr('disabled',false);\n";
			echo "$('#cbo_sub_process').attr('disabled',false);\n";
			echo "$('#cbo_dying_company').attr('disabled',false);\n";
		}
		$po_id=array_unique(explode(",",$row[csf("order_id")]));
		$file_no='';$ref_no='';$color_range_arr='';
		foreach($po_id as $poId)
		{
			if($file_no!='') $file_no.=",".$po_data_arr[$poId]['file']; else  $file_no=$po_data_arr[$poId]['file'];	
			if($ref_no!='') $ref_no.=",".$po_data_arr[$poId]['ref'].","; else  	$ref_no=$po_data_arr[$poId]['ref'];	
		}
		$batch_id_all=explode(",",$row[csf("batch_no")]);
		foreach($batch_id_all as $b_id)
		{
			if($batch_no=="") $batch_no=$batch_arr[$b_id];
			else $batch_no.=",".$batch_arr[$b_id];
			if($color_range_arr=="") $color_range_arr=$color_range[$batch_arr_color_range[$b_id]]; else $color_range_arr.=",".$color_range[$batch_arr_color_range[$b_id]];
			
		}
		echo "document.getElementById('txt_batch_no').value = '".$batch_no."';\n";
		 
		
		if(trim($row[csf("issue_basis")])!=7)
		{
			if(trim($row[csf("batch_no")])!="")
			{
			   $sql_batch = sql_select("select  batch_weight, total_liquor from pro_batch_create_mst where id in(".$row[csf("batch_no")].")  and status_active=1 and is_deleted=0");
			   foreach($sql_batch as $row_batch)
			   {
				   echo "document.getElementById('txt_batch_weight').value = '".$row_batch[csf("batch_weight")]."';\n"; 
				   echo "document.getElementById('txt_tot_liquor').value = '".$row_batch[csf("total_liquor")]."';\n"; 	
			   }
			}
	    }
		else if(trim($row[csf("issue_basis")])==7)
		{
			$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$row[csf("lap_dip_no")].") and company_id=".$row[csf("company_id")]."","total_liquor");
			echo "load_drop_down( 'requires/chemical_dyes_issue_controller', '".$row[csf("company_id")]."**".$row[csf("lap_dip_no")]."**".$row[csf("is_posted_account")]."', 'load_drop_down_sub_process', 'sub_process_td');\n"; 
			//$total_batch=return_field_value("sum(batch_weight) as batch_weight" ,"pro_batch_create_mst","id in(".$row[csf("batch_no")].") and company_id=".$row[csf("company_id")]."","batch_weight");
			//$batch_id=return_field_value("batch_id as batch_id" ,"pro_recipe_entry_mst","id in(".$row[csf("lap_dip_no")].") ","batch_id");
			//$total_batch=return_field_value("sum(new_batch_weight) as batch_weight" ,"pro_recipe_entry_mst","id in(".$row[csf("lap_dip_no")].") and batch_id in(".$batch_id.")  and entry_form=60 and dyeing_re_process=1 ","batch_weight");
			$array_data=sql_select("select id, entry_form, batch_id, total_liquor, new_batch_weight,batch_qty from pro_recipe_entry_mst where id in(".$row[csf("lap_dip_no")].")");
			$batch_new_qty=0;
			foreach($array_data as $re_val)
			{
				//$total_liquor+=$row[csf("total_liquor")];
				if($re_val[csf("entry_form")]==60)
				{
					$batch_new_qty+=$re_val[csf("new_batch_weight")];
				}
				if($re_val[csf("entry_form")]==59) //New Add from Recipe page
				{
					$batch_new_qty+=$re_val[csf("batch_qty")];
				}
			}
			echo "document.getElementById('txt_tot_liquor').value = '".$total_liquor."';\n"; 
			echo "document.getElementById('txt_batch_weight').value = '".$batch_new_qty."';\n"; 
			
		} 
		echo "document.getElementById('txt_color_range').value = '".$color_range_arr."';\n";
		echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
		echo "document.getElementById('txt_ref_no').value = '".$ref_no."';\n";

		
	}
	exit();
}

if($action=="save_update_delete")
{	  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
        //--------------------------for check issue date with all product id's last receive date
        for ($i=1;$i<$total_row;$i++)
        {
            $txt_prod_id="txt_prod_id_".$i;
            $txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
            
            if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
            {
                $prod_ids.=str_replace("'",'',$$txt_prod_id).",";
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
		            echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot";
		            die;
			}
        } 
		
        //-----------------------------------------------------------------------------

	$product_arr=array();
	//$issue_store_id=str_replace("'","",$cbo_store_name);
	$dataArray = sql_select("select id, avg_rate_per_unit, current_stock, stock_value from product_details_master where item_category_id in (5,6,7,23)");
	foreach($dataArray as $row)
	{
		$product_arr[$row[csf("id")]]['qty']=$row[csf("current_stock")];
		$product_arr[$row[csf("id")]]['rate']=$row[csf("avg_rate_per_unit")];
		$product_arr[$row[csf("id")]]['val']=$row[csf("stock_value")];
	}
	$sql_store = sql_select("select id,prod_id,store_id,category_id as cat_id,rate as avg_rate,cons_qty as current_stock,amount as stock_value,last_purchased_qnty as last_qty from inv_store_wise_qty_dtls where  company_id=$cbo_dying_company and store_id=$issue_store_id ");
			$store_arr=array();$store_idarr=array();
	foreach($sql_store as $row)
	{
		
		$store_arr[$row[csf("id")]]['qty']=$row[csf("current_stock")];
		$store_arr[$row[csf("id")]]['avg_rate']=$row[csf("avg_rate")];
		//$store_arr[$row[csf("id")]]['val']=$row[csf("stock_value")];
		//$store_arr[$row[csf("id")]]['last_qty']=$row[csf("last_qty")];
		$store_idarr[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("cat_id")]]['id']=$row[csf("id")];
		
	}
	
	
	//echo "10**".$flag;die;
	$lifoFifoArr=return_library_array("select item_category_id, store_method from variable_settings_inventory where company_name=$cbo_dying_company and variable_list=17 and item_category_id in (5,6,7,23) and status_active=1 and is_deleted=0","item_category_id","store_method");
			
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//check_table_status( $_SESSION['menu_id'],0);
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$dys_che_issue_num=''; $dys_che_update_id=''; $product_id=0;
	
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==2 || $db_type==1) { $year_id=" extract(year from insert_date)="; }
			if($db_type==0)  { $year_id="YEAR(insert_date)="; }
			
			
			/*$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'DCI', date("Y",time()), 5, "select 
			issue_number_prefix, issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_name and entry_form=5 and
			$year_id".date('Y',time())." order by issue_number_prefix_num desc ", "issue_number_prefix", "issue_number_prefix_num" ));
			
			$id=return_next_id( "id", "inv_issue_master", 1 );*/
			
			$new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_dying_company,'DCI',5,date("Y",time()) ));
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			
			
			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number,issue_basis, issue_purpose, entry_form, company_id,location_id,buyer_id,buyer_job_no,style_ref,req_no,batch_no, issue_date, knit_dye_source, knit_dye_company, challan_no,loan_party,lap_dip_no, order_id,machine_id,remarks,inserted_by, insert_date, lc_company";
			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_basis.",".$cbo_issue_purpose.",5,".$cbo_dying_company.",".$cbo_location_name.",".$cbo_buyer_name.",".$txt_order_no.",".$txt_style_no.",".$txt_req_id.",".$txt_batch_id.",".$txt_issue_date.",".$cbo_dying_source.",".$cbo_dying_company.",".$txt_challan_no.",".$cbo_loan_party.",".$txt_recipe_id.",".$hidden_order_id.",".$txt_machine_id.",".$txt_return.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_company_name.")";
			//$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			$dys_che_issue_num=$new_system_id[0];
			$dys_che_update_id=$id;
		}
		else
		{
			$field_array_update="issue_basis*issue_purpose*entry_form*company_id*location_id*buyer_id*buyer_job_no*style_ref*req_no*batch_no*issue_date*knit_dye_source*knit_dye_company*challan_no*loan_party*lap_dip_no*order_id*machine_id*remarks*updated_by*update_date*lc_company";
			$data_array_update="".$cbo_issue_basis."*".$cbo_issue_purpose."*5*".$cbo_dying_company."*".$cbo_location_name."*".$cbo_buyer_name."*".$txt_order_no."*".$txt_style_no."*".$txt_req_id."*".$txt_batch_id."*".$txt_issue_date."*".$cbo_dying_source."*".$cbo_dying_company."*".$txt_challan_no."*".$cbo_loan_party."*".$txt_recipe_id."*".$hidden_order_id."*".$txt_machine_id."*".$txt_return."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_name."";
			
			$dys_che_issue_num=str_replace("'","",$txt_mrr_no);
			$dys_che_update_id=str_replace("'","",$update_id);
			//$rID=sql_update("inv_issue_master", $field_array_update, $data_array_update, "id", $update_id,1); 
		}
		
		$all_prod_id_c=''; $all_prod_id_d=''; $all_prod_id_ac=''; $all_prod_ids=''; $all_store_ids='';$all_cat_ids='';$all_reqn_qnty_edit='';$store_stock_arr=array();
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$cbo_store_name="cbo_store_name_".$i;
			$txt_reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			if(str_replace("'",'',$$txt_item_cat)==5) $all_prod_id_c.=str_replace("'",'',$$txt_prod_id).",";
			else if(str_replace("'",'',$$txt_item_cat)==6) $all_prod_id_d.=str_replace("'",'',$$txt_prod_id).",";
			else $all_prod_id_ac.=str_replace("'",'',$$txt_prod_id).",";
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$prod_id=str_replace("'",'',$$txt_prod_id);
				//$store_id=str_replace("'",'',$$cbo_store_name);
				$cat_id=str_replace("'",'',$$txt_item_cat);
				
			$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
			$store_stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
			$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
			$txt_reqn_qnty_edit=str_replace("'",'',$$txt_reqn_qnty_edit);
			$storeid=$store_idarr[$prod_id][$issue_store_id][$cat_id]['id'];
			$store_stock_arr[$storeid][$prod_id][$issue_store_id][$cat_id]['issue_qty']=$txt_reqn_qnty_edit;
				
			$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
			$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
			//$all_store_ids.=str_replace("'",'',$$cbo_store_name).",";
			//$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
			
			}
		}
		
		//$cbo_store_id=str_replace("'",'',$cbo_store_name);
		//echo "10**".$cbo_store_id;die;
		$cbo_dying_company=str_replace("'",'',$cbo_dying_company);
		$stock_store_arr=fnc_store_wise_qty_operation($cbo_dying_company,$issue_store_id,$all_cat_ids,$all_prod_ids,2);
		//fnc_store_wise_qty_operation($company_id,$store_id,$category,$prod_id,$trans_type)
		//print_r($stock_store_arr);
		//echo "10**";die;
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date"; 
		$store_arrId=array_unique(explode(",",$stock_store_arr));
		$reg_issue_qty=0;//$curr_stock=0;
		for ($i=1;$i<$total_row;$i++)
		{
			$prod_id="txt_prod_id_".$i;
			$item_cat="txt_item_cat_".$i;
			//$store_name="cbo_store_name_".$i;
			$reqn_qnty_edit="txt_reqn_qnty_edit_".$i;
			
			$prod_id=str_replace("'",'',$$prod_id);
			$cat_id=str_replace("'",'',$$item_cat);
			$storeId=$store_idarr[$prod_id][$issue_store_id][$cat_id]['id'];
			if(str_replace("'",'',$$reqn_qnty_edit)>0)
			{
			if(str_replace("'",'',$storeId)!="")
				{
					$reg_issue_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$cat_id]['issue_qty'];
					$curr_stock=$store_arr[$storeId]['qty']-$reg_issue_qty;
					$s_avg_rate=$store_arr[$storeId]['avg_rate'];
					$store_StockValue=$curr_stock*$s_avg_rate;
					$sid_arr[]=str_replace("'",'',$storeId);
					$data_array_store[str_replace("'",'',$storeId)] =explode(",",("".$reg_issue_qty.",".$curr_stock.",".$store_StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
			}
		}
		//echo "10**";die;
		
		$chemDataArr=array(); $dyesDataArr=array(); $auxChemDataArr=array();
		
		$isLIFOfifoC=$lifoFifoArr[5];
		if($isLIFOfifoC==2) $cond_lifofifoC=" DESC"; else $cond_lifofifoC=" ASC";
		
		$isLIFOfifoD=$lifoFifoArr[6];
		if($isLIFOfifoD==2) $cond_lifofifoD=" DESC"; else $cond_lifofifoD=" ASC";
		
		$isLIFOfifoA=$lifoFifoArr[7];
		if($isLIFOfifoA==2) $cond_lifofifoA=" DESC"; else $cond_lifofifoA=" ASC";
		
		$all_prod_id_c=chop($all_prod_id_c,',');
		$all_prod_id_d=chop($all_prod_id_d,',');
		$all_prod_id_ac=chop($all_prod_id_ac,',');
		
		if($all_prod_id_c!="")
		{
			$chemData = sql_select("select prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_c) and balance_qnty>0 and transaction_type in (1,4,5) and item_category=5 order by transaction_date,id $cond_lifofifoC");
			foreach($chemData as $row)
			{
				$chemDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		
		if($all_prod_id_d!="")
		{
			$dyesData = sql_select("select prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_d) and balance_qnty>0 and transaction_type in (1,4,5) and item_category=6 order by transaction_date,id $cond_lifofifoD");
			foreach($dyesData as $row)
			{
				$dyesDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		
		if($all_prod_id_ac!="")
		{
			$auxChemData = sql_select("select prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_ac) and balance_qnty>0 and transaction_type in (1,4,5) and item_category=7 order by transaction_date,id $cond_lifofifoA");
			foreach($auxChemData as $row)
			{
				$auxChemDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}

		$updateID_array=array();
		$update_data=array();
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id,requisition_no,receive_basis,batch_id, company_id, prod_id, item_category, transaction_type, transaction_date, cons_uom, cons_quantity, cons_rate, cons_amount, issue_challan_no, store_id, inserted_by, insert_date,remarks";
		
		//$id_dtls=return_next_id( "id", "dyes_chem_issue_dtls", 1 ) ;
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		
		$field_array_dtls="id,mst_id,trans_id,requ_no,batch_id,recipe_id,requisition_basis,sub_process,product_id,item_category,dose_base,ratio,recipe_qnty,adjust_percent,adjust_type, required_qnty,req_qny_edit,inserted_by,insert_date";
		$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
		$field_array_mrr="id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		
		$adcomma=1;
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$cbo_store_name="cbo_store_name_".$i;
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
			
			
			if(str_replace("'",'',$$txt_reqn_qnty_edit)>0)
			{
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("DYES_CHEM_ISSUE_DTLS_PK_SEQ", "dyes_chem_issue_dtls", $con);
				/*$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=".$$txt_prod_id."");
				$avg_rate=$stock_qnty=$stock_value=0;
				foreach($sql as $result)
				{
					$avg_rate = $result[csf("avg_rate_per_unit")];
					$stock_qnty = $result[csf("current_stock")];
					$stock_value = $result[csf("stock_value")];
				}*/
				
				$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
				$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
				$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
					
				$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
				$issue_stock_value = $avg_rate*$txt_reqn_qnty_e;
								
				if ($adcomma!=1) $data_array_trans .=",";
				$data_array_trans.="(".$id_trans.",".$dys_che_update_id.",".$txt_req_id.",".$cbo_issue_basis.",".$txt_batch_id.",".$cbo_dying_company.",".$$txt_prod_id.",".$$txt_item_cat.",2,".$txt_issue_date.",12,".$txt_reqn_qnty_e.",".$avg_rate.",".$issue_stock_value.",".$txt_challan_no.",".$$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$txt_remarks.")";
				
				if ($adcomma!=1) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$dys_che_update_id.",".$id_trans.",'".$dys_che_issue_num."',".$txt_batch_id.",".$txt_recipe_id.",".$cbo_issue_basis.",".$cbo_sub_process.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
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
							$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",5,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
							
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
							$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",5,".$$txt_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
		
		
		if(str_replace("'","",$update_id)=="")
		{
			//echo "5**insert into inv_issue_master (".$field_array.") values ".$data_array;die;
		 	$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
		}
		else
		{		
			$rID=sql_update("inv_issue_master", $field_array_update, $data_array_update, "id", $update_id,1); 
		}
		if($rID) $flag=1; else $flag=0;
		
		// echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
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
		$upTrID=true;
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array),1);
			if($flag==1) { if($upTrID) $flag=1; else $flag=0;} 
		}
		$store_ID=true;
		if(count($sid_arr)>0)
		{
		 $store_ID=execute_query(bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr ),1);
        if($flag==1) { if($store_ID) $flag=1; else $flag=0; } 
		}
		//echo "10**$rID##$rID2##$rID3##$rID4##$mrrWiseIssueID##$upTrID##$store_ID";die;
		//echo "10**".bulk_update_sql_statement( "inv_store_wise_qty_dtls", "id", $field_array_store, $data_array_store, $sid_arr );die;
		  //echo "10**".$rID.'='.$rID2.'='.$rID3.'='.$rID4.'='.$store_ID.$data_array_dtls;die;
		//echo "5**0**FF**".$flag;die;
		//echo "5**".$flag;die;
 		//oci_rollback($con);echo "10**".$flag;die;
		//echo "5**".$flag;die;
		//echo "5**0**"."&nbsp;"."**0**".$flag;die;
		
		
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
			$cat_id=str_replace("'",'',$$txt_item_cat);
			
			
			$storeid=$store_idarr[$prod_id][$issue_store_id][$cat_id]['id'];
			$store_stock_arr[$storeid][$prod_id][$issue_store_id][$cat_id]['issue_qty']=$txt_reqn_qnty_edit;
			$store_stock_arr[$storeid][$prod_id][$issue_store_id][$cat_id]['hidden_qty']=$hidtxt_reqn_qnty_edit;
			//$store_stock_arr[str_replace("'",'',$$txt_prod_id)]['hidd_qty']=str_replace("'",'',$$hidtxt_reqn_qnty_edit);
			$all_prod_ids.=str_replace("'",'',$$txt_prod_id).",";
			$all_cat_ids.=str_replace("'",'',$$txt_item_cat).",";
			//$all_store_ids.=str_replace("'",'',$$cbo_store_name).",";
			
			
			}
		}
		$cbo_dying_company=str_replace("'",'',$cbo_dying_company);
		$stock_store_arr=fnc_store_wise_qty_operation($cbo_dying_company,$issue_store_id,$all_cat_ids,$all_prod_ids,2);
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
			$chemData = sql_select("select prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_c) and balance_qnty>0 and transaction_type in (1,4,5) and item_category=5 order by transaction_date $cond_lifofifoC");
			foreach($chemData as $row)
			{
				$chemDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		
		if($all_prod_id_d!="")
		{
			$dyesData = sql_select("select prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_d) and balance_qnty>0 and transaction_type in (1,4,5) and item_category=6 order by transaction_date $cond_lifofifoD");
			foreach($dyesData as $row)
			{
				$dyesDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		
		if($all_prod_id_ac!="")
		{
			$auxChemData = sql_select("select prod_id, id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id in ($all_prod_id_ac) and balance_qnty>0 and transaction_type in (1,4,5) and item_category=7 order by transaction_date $cond_lifofifoA");
			foreach($auxChemData as $row)
			{
				$auxChemDataArr[$row[csf("prod_id")]].=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
			}
		}
		//echo "10**select a.item_category, a.prod_id, b.issue_trans_id, a.id, a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id in($all_transId) and b.entry_form=5";die;
		$prev_prod_id_arr=array();
		$transData = sql_select("select a.item_category, a.prod_id, b.issue_trans_id, a.id, a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id in($all_transId) and b.entry_form=5"); 
		foreach($transData as $row)
		{
			$adjTransDataArr[$row[csf("item_category")]][$row[csf("issue_trans_id")]].=$row[csf("id")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")]."**".$row[csf("issue_qnty")]."**".$row[csf("rate")]."**".$row[csf("amount")].",";
			
			/*if($row[csf("balance_qnty")]<=0)
			{
				$prev_prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
				$prev_prod_data_arr[str_replace("'",'',$$txt_prod_id)].=$row[csf("id")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")]."**".$row[csf("issue_qnty")]."**".$row[csf("rate")]."**".$row[csf("amount")].",";
			}*/
		}
		
		$updateID_array=array(); $update_data=array();
		
		$field_array_update="issue_basis*issue_purpose*entry_form*company_id*location_id*buyer_id*buyer_job_no*style_ref*req_no*batch_no*issue_date*knit_dye_source*knit_dye_company*challan_no*loan_party*lap_dip_no*order_id*machine_id*remarks*updated_by*update_date*lc_company";
		$data_array_update="".$cbo_issue_basis."*".$cbo_issue_purpose."*5*".$cbo_dying_company."*".$cbo_location_name."*".$cbo_buyer_name."*".$txt_order_no."*".$txt_style_no."*".$txt_req_id."*".$txt_batch_id."*".$txt_issue_date."*".$cbo_dying_source."*".$cbo_dying_company."*".$txt_challan_no."*".$cbo_loan_party."*".$txt_recipe_id."*".$hidden_order_id."*".$txt_machine_id."*".$txt_return."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_company_name."";
		//echo $data_array_update;
		$dys_che_issue_num=$txt_mrr_no;
		$dys_che_update_id=$update_id;
		
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		
		$up_field_array_trans="prod_id*item_category*transaction_type*transaction_date*cons_uom* cons_quantity*cons_rate*cons_amount*
		issue_challan_no*store_id*updated_by*update_date*remarks";
		$up_field_array_dtls="sub_process*product_id*item_category*dose_base*ratio*recipe_qnty*adjust_percent*adjust_type*required_qnty*req_qny_edit*updated_by*update_date";
		$field_array_prod= "last_issued_qnty*current_stock*stock_value*updated_by*update_date"; 
		//echo $total_row;die;
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$user_id=$_SESSION['logic_erp']['user_id']; 
		for ($i=1;$i<$total_row;$i++)
		{
			$txt_prod_id="txt_prod_id_".$i;
			$txt_item_cat="txt_item_cat_".$i;
			$cbo_store_name="cbo_store_name_".$i;
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
			
			
			//print_r($store_stock_qnty);
			/*$stock=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=".$$txt_prod_id."");
			$adjust_curr_stock=$stock[0][csf('current_stock')]+str_replace("'", '',$$hidtxt_reqn_qnty_edit);
			$adjust_rate=$stock[0][csf('avg_rate_per_unit')];
			$adjust_value=$adjust_curr_stock*$adjust_rate;
			$field_array_prod_adject="current_stock*stock_value*updated_by*update_date";
			$data_array_prod_adject="'".$adjust_curr_stock."'*'".$adjust_value."'*'".$user_id."'*'".$pc_date_time."'";
			$adjust_prod=sql_update("product_details_master",$field_array_prod_adject,$data_array_prod_adject,"id",$$txt_prod_id,1);
			//echo $adjust_prod;die;
			if($adjust_prod) $flag=1; else $flag=0;
			//========================================================
			$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=".$$txt_prod_id."");
			$avg_rate=$stock_qnty=$stock_value=0;
			foreach($sql as $result)
			{
				$avg_rate = $result[csf("avg_rate_per_unit")];
				$stock_qnty = $result[csf("current_stock")];
				$stock_value = $result[csf("stock_value")];
			}*/
			
			$avg_rate = $product_arr[str_replace("'",'',$$txt_prod_id)]['rate'];
			$stock_qnty = $product_arr[str_replace("'",'',$$txt_prod_id)]['qty'];
			$stock_value = $product_arr[str_replace("'",'',$$txt_prod_id)]['val'];
			
			$txt_reqn_qnty_e=str_replace("'","",$$txt_reqn_qnty_edit);
			$issue_stock_value = $avg_rate*str_replace("'","",$txt_reqn_qnty_e);
			
			if(str_replace("'",'',$$transId)!="")
			{
				//fnc_store_wise_qty_operation($operation,$cbo_company_name,$$cbo_store_name,$$txt_item_cat,$$txt_prod_id,$txt_reqn_qnty_e,$avg_rate,$issue_stock_value,$pc_date_time,2);
				$id_arr_trans[]=str_replace("'",'',$$transId);
				$data_array_trans[str_replace("'",'',$$transId)] =explode("*",("".$$txt_prod_id."*".$$txt_item_cat."*2*".$txt_issue_date."*12*".$txt_reqn_qnty_e."*".$avg_rate."*".$issue_stock_value."*".$txt_challan_no."*".$$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$txt_remarks.""));
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
			$cat_id=str_replace("'",'',$$txt_item_cat);
			$storeId=$store_idarr[$prod_id][$issue_store_id][$cat_id]['id'];
			// echo $$txt_reqn_qnty_e.'aaz';
			if(str_replace("'",'',$txt_reqn_qnty_e)>0)
			{
				//echo $$txt_reqn_qnty_e.'a4';
			if(in_array($storeId,$storeArrId))
			{
			if(str_replace("'",'',$storeId)!="")
				{
					$req_hidden_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$cat_id]['hidden_qty'];
					$reg_issue_qty=$store_stock_arr[$storeId][$prod_id][$issue_store_id][$cat_id]['issue_qty'];
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",5,".$$txt_prod_id.",".$txt_reqn_qnty_e.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".str_replace("'",'',$$transId).",5,".$$txt_prod_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
		
		
		
		//print_r($prod_arr);
		//echo "10**";
	
		/*foreach($storeId as $pid)
		{
			$curr_stock=$store_stock_arr[str_replace("'",'',$pid)]['curr_stock'];
			$req_qty=$store_stock_arr[str_replace("'",'',$pid)]['qty'];
			$s_avg_rate=$store_stock_arr[str_replace("'",'',$pid)]['avg_rate'];
			//$hidd_qty=$store_stock_arr[str_replace("'",'',$pid)]['hidd_qty'];
			$store_StockValue=$curr_stock*$s_avg_rate;
			
			if(str_replace("'",'',$pid)!="")
				{
					$pid_arr[]=str_replace("'",'',$pid);
					$data_array_store[str_replace("'",'',$pid)] =explode(",",("".$req_qty.",".$curr_stock.",".$store_StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
			
		}*/
		/*print_r($update_data);die;
		//echo "10**";die;
		echo bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array);
		echo "<br>Fuad<br>";die;
		echo bulk_update_sql_statement( "product_details_master", "id", $field_array_prod, $data_array_prod, $id_arr );
		print_r($id_arr);
		die;*/
		//echo " bulk_update_sql_statement( "inv_store_wise_qty_dtls", "prod_id", $field_array_store, $data_array_store, $pid_arr )";
		//echo "10**".bulk_update_sql_statement( "inv_store_wise_qty_dtls", "prod_id", $field_array_store, $data_array_store, $pid_arr );
		
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array);die;
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
		
	}		
}


if($action=="chemical_dyes_issue_cost_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
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
	
	$sql="select a.id, a.issue_number, a.issue_date, a.issue_basis,a.issue_purpose, a.req_no, a.batch_no, a.issue_purpose, a.loan_party,a.lap_dip_no, a.knit_dye_source, a.knit_dye_company, a.challan_no,a.remarks, a.buyer_job_no,a.order_id, a.buyer_id, a.style_ref from inv_issue_master a where a.id='$data[1]' and a.company_id='$data[0]'";
    $dataArray=sql_select($sql);
	$batch_id_all=explode(",",$dataArray[0][csf('batch_no')]);
    foreach($batch_id_all as $b_id)
	{
	if($batch_no=="") $batch_no=$batch_arr[$b_id]; else $batch_no.=",".$batch_arr[$b_id];
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
	$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$dataArray[0][csf('lap_dip_no')].") and company_id=".$data[0]."","total_liquor");
	$total_batch=return_field_value("sum(batch_weight) as batch_weight" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","batch_weight");
	$color_range_id=return_field_value("color_range_id as color_range_id" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","color_range_id");
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
?>
<div style="width:1100px; ">
    <table width="1100" cellspacing="0" align="center">
        <tr>
        	<td rowspan="3" width="70">
            	<img src="../../../<? echo $image_location; ?>" height="60" width="180">
            </td>
            <td colspan="6" align="center" style="font-size:20px">
            	<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$supplier_com_library; ?></strong>
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
			<td><strong>Issue Purpose :</strong></td> <td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
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
    <table align="" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="200" >Store Name</th>
            <th width="80" >Item Cat.</th>
            <th width="80" >Item Group</th>
            <th width="100" >Sub Group</th>
            <th width="170" >Item Description</th>
            <th width="50" >UOM</th>
            <th width="110" >Dose Base</th> 
            <th width="40" >Ratio</th>
            <th width="60" >Recipe Qnty</th>
            <th width="50" >Adj%</th>
            <th width="60" >Adj Type</th> 
            <th width="60" >Issue Qnty</th>
            <th width="60" >Unit Price</th> 
            <th width="90" >Amount(BDT)</th>
        </thead>
        <tbody> 
   
<?
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0",'id','item_name');
// ****************************previous sql***************************************************
      $sql_dtls ="select b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity,b.cons_amount, b.machine_category,b.machine_id, b.prod_id,
	              b.department_id,b.section_id,b.cons_rate,b.cons_amount,c.item_description, c.item_group_id, c.sub_group_name, c.item_size,d.sub_process,
	              d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit
	              from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
	              where a.id=d.mst_id and b.id=d.trans_id and d.product_id=c.id and b.transaction_type=2 and a.entry_form=5
				  and   b.item_category in (5,6,7,23) and d.mst_id=$data[1]  order by d.sub_process "; 
	//  ******************************************************************************************************************************************************	
	$sql_result= sql_select($sql_dtls);
	$i=1;$m=1;
	$arr_sub_process=array();$sub_process_sum=array();
	if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==13)
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
				<td><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
				<td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
				<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
				<td align="right"><? echo number_format($row[csf("ratio")],2); ?></td>
				<td align="right"><? echo number_format($row[csf("recipe_qnty")],4); ?></td>
				<td align="right"><? echo $row[csf("adjust_percent")]; ?></td>
				<td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
				<td align="right"><? echo number_format($row[csf("req_qny_edit")],2); ?></td>
				<td align="right"><?php echo number_format($row[csf("cons_rate")],2); ?></td>
				<td align="right" colspan="3"><?php echo number_format($row[csf("cons_amount")],2); ?></td>
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
				<td colspan="9" align="right"><strong>Total :</strong></td>
				<td align="right"><?php echo number_format($recipe_qnty_sum,1); ?></td>
				<td align="right" colspan="3"><?php echo number_format($req_qny_edit_sum,1); ?></td>
				<td align="right" colspan="2"><?php echo number_format($total_amount_sum,2); ?></td>
			</tr>
			<?
			$recipe_qnty_sum=0; $req_qny_edit_sum=0; $total_amount_sum=0;
			}
		?> 
			<tr>
				 <td colspan="15" style="font-size:15px; text-align:center"><strong>Dyeing Sub-Process: <? echo  $dyeing_sub_process[$row[csf("sub_process")]] ?></strong></td>
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
				<td><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
				<td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
				<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
				<td align="right"><? echo number_format($row[csf("ratio")],2); ?></td>
				<td align="right"><? echo number_format($row[csf("recipe_qnty")],4); ?></td>
				<td align="right"><? echo $row[csf("adjust_percent")]; ?></td>
				<td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
				<td align="right"><? echo number_format($row[csf("req_qny_edit")],2); ?></td>
				<td align="right"><?php echo number_format($row[csf("cons_rate")],2); ?></td>
				<td align="right" colspan="3"><?php echo number_format($row[csf("cons_amount")],2); ?></td>
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
				<td colspan="9" align="right"><strong>Total :</strong></td>
				<td align="right"><?php echo number_format($recipe_qnty_sum,1); ?></td>
				<td align="right" colspan="3"><?php echo number_format($req_qny_edit_sum,1); ?></td>
				<td align="right" colspan="2"><?php echo number_format($total_amount_sum,2); ?></td>
				
			</tr>
		</tbody>
		<?
		
		
		
	}
	?>
    <tfoot>
        <tr>
            <td colspan="9" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><?php echo number_format($grand_total_receive,1); ?></td>
            <td align="right" colspan="3"><?php echo number_format($total_req_qny_edit,1); ?></td>
            <td align="right" colspan="2"><?php echo number_format($grand_totao_amount,2); ?></td>
        </tr> 
        <tr>
            <td colspan="13" align="right"><strong>Cost Per Kg</strong></td>
            <td align="right" colspan="2"><?php echo number_format(($grand_totao_amount/$total_batch),3); ?></td>
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
        <td align="right"><? echo number_format($sval,2); ?></td>
        <?
        if($row_span==1)
		{ ?>
         <td align="right" rowspan="<? echo $tot_row;?>"><? echo $total_batch; ?></td>
        
       <? } ?>
        <td align="right"><? echo number_format($sval/$total_batch,3); ?></td>
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
            <td align="right"><?php echo number_format($total_summary,2); ?></td>
            <td>&nbsp; </td>
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
if($action=="chemical_dyes_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select a.id, a.issue_number, a.issue_date, a.issue_basis,a.issue_purpose, a.req_no, a.batch_no, a.issue_purpose, a.loan_party,a.lap_dip_no, a.knit_dye_source, a.knit_dye_company, a.challan_no,a.remarks, a.buyer_job_no,a.order_id, a.buyer_id, a.style_ref from inv_issue_master a where a.id='$data[1]' and a.company_id='$data[0]'";
	
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
		$total_liquor=return_field_value("sum(total_liquor) as total_liquor" ,"pro_recipe_entry_mst","id in(".$dataArray[0][csf('lap_dip_no')].") and company_id=".$data[0]."","total_liquor");
		//$total_batch=return_field_value("sum(batch_weight) as batch_weight" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","batch_weight");
		if($recipe_id=="") $recipe_id=0; else $recipe_id=$recipe_id;
		$batch_id=return_field_value("batch_id as batch_id" ,"pro_recipe_entry_mst","id in(".$recipe_id.") ","batch_id");
		if($batch_id=="") $batch_id=0; else $batch_id=$batch_id;
		$total_batch=return_field_value("sum(batch_qty) as batch_weight" ,"pro_recipe_entry_mst","id in(".$recipe_id.") and batch_id in(".$batch_id.") and company_id=".$data[0]." ","batch_weight");
				
		$color_range_id=return_field_value("color_range_id as color_range_id" ,"pro_batch_create_mst","id in(".$dataArray[0][csf('batch_no')].") and company_id=".$data[0]."","color_range_id");
	
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
?>
<div style="width:1200px;">
    <table width="1200" cellspacing="0" align="">
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
        	<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="125"><strong>Issue Date:</strong></td><td width="150px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="140"><strong>Issue Basis :</strong></td> <td width="250px"><? echo $receive_basis_arr[$dataArray[0][csf('issue_basis')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Req. No:</strong></td> <td><? echo $req_arr[$dataArray[0][csf('req_no')]]; ?></td>
            <td><strong>Batch No :</strong></td><td><? echo $batch_no; ?></td>
			<td><strong>Issue Purpose :</strong></td> <td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
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
			if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==13)
			{
				
			}
			else
			{
				?>
				<th width="80" >Sub Process</th>
				<?
			}
			?>
            <th width="200" >Store Name</th>
            <th width="80" >Item Cat.</th>
            <th width="80" >Item Group</th>
            <th width="100" >Sub Group</th>
            <th width="170" >Item Description</th>
            <th width="50" >UOM</th>
            <th width="110" >Dose Base</th> 
            <th width="40" >Ratio</th>
            <th width="60" >Recipe Qnty</th>
            <th width="50" >Adj%</th>
            <th width="60" >Adj Type</th> 
            <th width="80">Issue Qnty</th>
            <th>Remarks</th>
        </thead>
        <tbody> 
   
<?
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0",'id','item_name');
	
 
 
 $sql_dtls = "select b.id,a.issue_number,b.store_id,
	  b.cons_uom, b.cons_quantity,b.cons_amount, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id,       b.section_id,b.cons_rate,b.cons_amount,
	  c.item_description, c.item_group_id, c.sub_group_name, c.item_size,
	  d.sub_process, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit,b.remarks
	  from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
	  where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id  and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and d.mst_id=$data[1]  order by d.sub_process "; 
	 // echo $sql_dtls;die;
	  $sql_result= sql_select($sql_dtls);
	  $i=1;
	if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==13) $colspan=9; else $colspan=10;
	
		
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
				if(trim($dataArray[0][csf('issue_basis')])==7 && trim($dataArray[0][csf('issue_purpose')])==13)
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
                <td><? echo $row[csf("item_description")].' '.$row[csf("item_size")]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                <td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("ratio")],4); ?></td>
                <td align="right"><? echo number_format($row[csf("recipe_qnty")],4); ?></td>
                <td align="right"><? echo $row[csf("adjust_percent")]; ?></td>
                <td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("req_qny_edit")],4); ?></td>
                <td><? echo $row[csf("remarks")]; ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="<? echo $colspan; ?>" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo number_format($recipe_qnty_sum,4); ?></td>
                <td align="right" colspan="3"><?php echo number_format($req_qny_edit_sum,4); ?></td>
                <td>&nbsp;</td>
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
			  $sql_data=sql_select("select id,company_id,category_id,prod_id,cons_qty,rate,amount
		 from  inv_store_wise_qty_dtls where  company_id=$company_id  and status_active=1 and is_deleted=0 $prod_cond $cat_cond");
			}
			else if($trans_type==1 || $trans_type==4) //Recv && Issue Return;
			{
				 $sql_data=sql_select("select id,company_id,category_id,store_id,prod_id,cons_qty,rate,amount
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
		 
		 $result=sql_select("select	 category_id,prod_id,cons_qty 
		 from  inv_store_wise_qty_dtls where  company_id=$company_id and store_id=$store_id and status_active=1 and is_deleted=0");
		$stock_qty_arr=array();
		 foreach($result as $row)
		 {
			 // $stock_qty_arr[$company_id][$store_id][$row[csf('category_id')]][$row[csf('prod_id')]]['stock']=($row[csf('recv_qty')]+$row[csf('issue_ret_qty')]+$row[csf('transfer_in')])-($row[csf('issue_qty')]+$row[csf('recv_ret_qty')]+$row[csf('transfer_out')]); 
			 $stock_qty_arr[$company_id][$store_id][$row[csf('category_id')]][$row[csf('prod_id')]]['stock']=$row[csf('cons_qty')]; 
		 }
		 return $stock_qty_arr;
	}
?>
