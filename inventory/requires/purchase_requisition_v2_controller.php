<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
if($_SESSION['app_notification'] == 1){
	include('../../includes/class4/Notifications.php');
}


$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}

if (!empty($store_location_id)) {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}

if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}

$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");


//========== user credential end ==========

if ($action=="load_drop_down_location")
{
	$sql="select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1  $location_credential_cond";
	$result=sql_select($sql);
	$selected=0;
	if (count($result)==1) {
		$selected=$result[0][csf('id')];
	}

	echo create_drop_down( "cbo_location_name", 160,$sql,"id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/purchase_requisition_v2_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_stor','stor_td');" );
	die;
}

if ($action=="load_drop_down_division")
{
	$sql="select id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1";
	$result=sql_select($sql);
	$selected=0;
	if (count($result)==1) {
		$selected=$result[0][csf('id')];
	}
	
	echo create_drop_down( "cbo_division_name", 160,$sql,"id,division_name", 1, "-- Select --", 0, "load_drop_down( 'requires/purchase_requisition_v2_controller', this.value, 'load_drop_down_department','department_td');" );
	die;
}

if ($action=="load_drop_down_department")
{
	echo create_drop_down( "cbo_department_name", 160,"select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/purchase_requisition_v2_controller', this.value, 'load_drop_down_section','section_td');" );
	die;
}

if ($action=="load_drop_down_section")
{
	if ($data != ''){
		echo create_drop_down( "cbo_section_name", 160,"select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1","id,section_name", 1, "-- Select --", $selected, "" );
	} else {
		echo create_drop_down( "cbo_section_name", 160,$blank_array,"", 1, "-- Select --", $selected, "" );
	}
	die;
}

if ($action=="load_drop_down_stor")
{
	$data = explode("_",$data);
	if($data[1]) $location_cond=" and a.location_id='$data[1]'";
	echo create_drop_down( "cbo_store_name", 160,"select a.id,a.store_name from lib_store_location a, lib_store_location_category b  where a.id=b.store_location_id and a.is_deleted=0 and a.company_id='$data[0]' $location_cond and a.status_active=1 and b.category_type not in(1,2,3,12,13,14,24,25) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "" );
	die;
}

if($action == "load_drop_down_group")
{
	echo create_drop_down( "cbo_item_group", 130,"select a.item_name,a.id from lib_item_group a where a.item_category = $data and a.status_active = 1 and a.is_deleted  = 0 group by a.item_name, a.id order by a.id","id,item_name", 1, "-- Select --", $selected, "" );
	//load_drop_down( 'purchase_requisition_v2_controller', this.value, 'load_drop_down_description','description_td');
	die;
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=39 and is_deleted=0 and status_active=1");
	//echo $print_report_format.jahid;die;
	//$field_name, $table_name, $query_cond, $return_fld_name, $new_conn
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#search1').hide();\n";
	echo "$('#search2').hide();\n";
	echo "$('#search3').hide();\n";
	echo "$('#search4').hide();\n";
	echo "$('#search5').hide();\n";
	echo "$('#search6').hide();\n";
	echo "$('#search7').hide();\n";
	echo "$('#search8').hide();\n";
	echo "$('#search9').hide();\n";
	echo "$('#search10').hide();\n";
	echo "$('#search11').hide();\n";
	echo "$('#search12').hide();\n";
	echo "$('#search13').hide();\n";
	echo "$('#searchnew').hide();\n";
	echo "$('#search14').hide();\n";
	echo "$('#search15').hide();\n";
	echo "$('#search16').hide();\n";
	echo "$('#search17').hide();\n";
	echo "$('#search18').hide();\n";
	echo "$('#search19').hide();\n";
	echo "$('#search_category_wise').hide();\n";
	echo "$('#search20').hide();\n";
	echo "$('#search21').hide();\n";
	echo "$('#search23').hide();\n";
	echo "$('#search24').hide();\n";
	echo "$('#search25').hide();\n";
	echo "$('#search26').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==118){echo "$('#search1').show();\n";}
			if($id==119){echo "$('#search2').show();\n";}
			if($id==120){echo "$('#search3').show();\n";}
			if($id==121){echo "$('#search4').show();\n";}
			if($id==122){echo "$('#search5').show();\n";}
			if($id==123){echo "$('#search6').show();\n";}
			if($id==129){echo "$('#search7').show();\n";}
			if($id==169){echo "$('#search8').show();\n";}
			if($id==165){echo "$('#search9').show();\n";}
			if($id==227){echo "$('#search10').show();\n";}
			if($id==241){echo "$('#search11').show();\n";}
			if($id==580){echo "$('#search12').show();\n";}
			if($id==28){echo "$('#search13').show();\n";}
			if($id==280){echo "$('#searchnew').show();\n";}
			if($id==688){echo "$('#search14').show();\n";}
			if($id==243){echo "$('#search15').show();\n";}
			if($id==310){echo "$('#search_category_wise').show();\n";}
			if($id==304){echo "$('#search16').show();\n";}
			if($id==719){echo "$('#search17').show();\n";}
			if($id==723){echo "$('#search18').show();\n";}
			if($id==339){echo "$('#search19').show();\n";}
			if($id==370){echo "$('#search20').show();\n";}
			if($id==235){echo "$('#search21').show();\n";}
			if($id==382){echo "$('#search23').show();\n";}
			if($id==768){echo "$('#search24').show();\n";}
			if($id==425){echo "$('#search25').show();\n";}
			if($id==419){echo "$('#search26').show();\n";}
            //580,241,227,165,169,129,123,122,121,120,118
		}
	}
	else
	{
		echo "$('#search1').show();\n";
		echo "$('#search2').show();\n";
		echo "$('#search3').show();\n";
		echo "$('#search4').show();\n";
		echo "$('#search5').show();\n";
		echo "$('#search6').show();\n";
		echo "$('#search7').show();\n";
		echo "$('#search8').show();\n";
		echo "$('#search10').show();\n";
		echo "$('#search11').show();\n";
		echo "$('#search12').show();\n";
		echo "$('#search13').show();\n";
		echo "$('#search14').show();\n";
		echo "$('#search15').show();\n";
		echo "$('#searchnew').show();\n";
		echo "$('#search_category_wise').show();\n";
		echo "$('#search16').show();\n";
		echo "$('#search17').show();\n";
		echo "$('#search18').show();\n";
		echo "$('#search19').show();\n";
		echo "$('#search20').show();\n";
		echo "$('#search21').show();\n";
		echo "$('#search23').show();\n";
		echo "$('#search24').show();\n";
		echo "$('#search25').show();\n";
		echo "$('#search26').show();\n";
	}
	$lib_budge_data=sql_select("select BUDGET_VALIDATION_STATUS,BUDGET_VALIDATION_PAGE from VARIABLE_SETTINGS_COMMERCIAL where STATUS_ACTIVE=1 and VARIABLE_LIST = 35 AND COMPANY_NAME = $data");
	if($lib_budge_data[0]["BUDGET_VALIDATION_STATUS"]==1 && $lib_budge_data[0]["BUDGET_VALIDATION_PAGE"]==1)
	{
		echo "$('#budge_validation').val(1);\n";
		echo "$('#req_rate_caption').css('color', 'blue');\n";
	}
	else
	{
		echo "$('#budge_validation').val(0);\n";
		echo "$('#req_rate_caption').css('color', 'black');\n";
	}
	exit();
}

if($action=="rmg_process_loss_popup")
{
	echo load_html_head_contents("Justification Search","../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
function js_set_value_set()
{
	  var value_justification=$('#txt_just_value').val();
	 document.getElementById('txt_justification_value').value=value_justification;
	 parent.emailwindow.hide();
}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
 <!-- <? echo load_freeze_divs ("../../",$permission);  ?> -->
 <?
//  $data=explode($txt_justification_value);
 ?>
<fieldset>
    <form autocomplete="off">
    <input style="width:60px;" type="hidden" class="text_boxes"  name="txt_justification_value" id="txt_justification_value" />
    <table width="280" class="rpt_table" border="1" rules="all">
				<tr><th width="130">Justification</th></tr>
				<tr><td>
                <textarea  style="height:80px;" class="text_boxes" rows="6" cols="50" name="txt_just_value" id="txt_just_value" value="<? echo $txt_justification_value;  ?>" ></textarea>				
                </td> </tr>
                 <tr>
               <td align="center"  class="button_container">
			    <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/>
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
}


if ($action=="purchase_requisition_popup")
{
 	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
		  function js_set_value(id)
		  {
			  document.getElementById('selected_job').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="purchaserequisition_2"  id="purchaserequisition_2" autocomplete="off">
	<table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="950">
                    <thead>
						<tr>
							<th colspan="8"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
						</tr>
						<tr>
							<th width="180">Company Name</th>
							<th width="50" style="display:none">Item Category</th>
							<th width="100">Store Name</th>
							<th width="100">Requisition Year</th>
							<th width="120">Requisition No</th>
							<th width="100">Item Issue Req/Dem</th>
							<th width="200">Date Range</th>
							<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
						</tr>
                    </thead>
        			<tr class="general">
                    	<td align="center"> <input type="hidden" id="selected_job">
							<?
								echo create_drop_down( "cbo_company_name", 152, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, "",1);
                            ?>
                    	</td>
                   		<td style="display:none">
							<?
								echo create_drop_down( "cbo_item_category_id", 50,$item_category,"", 1, "-- Select --", $selected, "","","","","","1,2,3,12,13,14,24,25");
                            ?>
                        </td>
                        <td align="center">
							<?
								 echo create_drop_down( "cbo_store_name", 160,"select a.id,a.store_name from lib_store_location a, lib_store_location_category b  where a.id=b.store_location_id and a.is_deleted=0 and a.company_id='$cbo_company_name' and a.status_active=1 and b.category_type not in(1,2,3,12,13,14,24,25) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
						<td  align="center">
							<?
								$year_current=date("Y");
								echo create_drop_down( "txt_year", 80, $year,"", 1, "All",$year_current);
							?>
						</td>
                        <td align="center">
                            <input name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:120px">
					 	</td>
						 <td align="center">
                            <input name="txt_indent_no" id="txt_indent_no" class="text_boxes" style="width:100px">
						</td>
                    	<td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 	</td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_requisition_no').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('txt_year').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_indent_no').value, 'purchase_requisition_list_view', 'search_div', 'purchase_requisition_v2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                       </td>
        			</tr>
             	</table>
          	</td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
				<? echo load_month_buttons(1);  ?>
            </td>
        </tr>        
    </table>
    <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
    </form>
    </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="purchase_requisition_list_view")
{
	$data=explode('_',$data);
	// echo $data[0]."_".$data[1]."_".$data[2]."_".$data[3]."_".$data[4]."_".$data[5]."_".$data[6]."_".$data[7]."_".$data[8]; die;
	// var_dump($data);
	$requisition_year= $data[6];

	if ($data[0]!=0) $company=" AND A.COMPANY_ID='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $item_category_id=" AND A.ITEM_CATEGORY_ID='$data[1]'"; else $item_category_id="";//{ echo "Please Select item category."; die; }

	$requisition_no=trim(str_replace("'","",$data[4]));
	$get_cond = "";
	if(str_replace("'","",$requisition_no)!="")
	{
		if($data[7]==1)
		{
			$get_cond =" AND A.REQU_PREFIX_NUM='$requisition_no' ";
		}
		else if($data[7]==4 || $data[7]==0)
		{
			$get_cond =" AND A.REQU_PREFIX_NUM LIKE '%$requisition_no%' ";
		}
		else if($data[7]==2)
		{
			$get_cond =" AND A.REQU_PREFIX_NUM LIKE '$requisition_no%' ";
		}
		else if($data[7]==3)
		{
			$get_cond =" AND A.REQU_PREFIX_NUM LIKE '%$requisition_no' ";
		}
	}

	$store_cond = ($data[5]) ? " AND A.STORE_NAME = '" . $data[5] ."'" :  "";

	if($db_type==0)
				{
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " AND A.REQUISITION_DATE BETWEEN '".change_date_format($data[2], "yyyy-mm-dd", "-")."' AND '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $order_rcv_date ="";
				}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " AND A.REQUISITION_DATE BETWEEN '".change_date_format($data[2], 'mm-dd-yyyy','/',1)."' AND '".change_date_format($data[3], 'mm-dd-yyyy','/',1)."'"; else $order_rcv_date ="";
	}

	//For year
	if ($requisition_year != 0){
		if($db_type==2) { $requisition_year_cond=" AND EXTRACT(YEAR FROM A.REQUISITION_DATE)=$requisition_year";}
		if($db_type==0) {$requisition_year_cond=" AND SUBSTRING_INDEX(A.REQUISITION_DATE, '-', 1)=$requisition_year";}
	}
	
	$itm_iss_req_dem = $data[8];

	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_location_id = $userCredential[0][csf('location_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];

	$credientian_cond=""; 
	//if($cre_company_id>0) $credientian_cond=" and company_id in($cre_company_id)";
	//if($cre_location_id>0) $credientian_cond.=" and location_id in($cre_location_id)";
	//if($cre_store_location_id>0) $credientian_cond.=" and store_name in($cre_store_location_id)";
	//if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";

	if($itm_iss_req_dem!='')
	{
		$sql= "SELECT A.ID, A.REQU_PREFIX_NUM, A.REQUISITION_DATE, A.COMPANY_ID, A.ITEM_CATEGORY_ID, A.LOCATION_ID, A.DEPARTMENT_ID, A.SECTION_ID, A.MANUAL_REQ, A.STORE_NAME, A.INSERTED_BY, A.READY_TO_APPROVE, A.REQ_BASIS,A.IS_APPROVED, D.ITEMISSUE_REQ_PREFIX_NUM
		FROM INV_PURCHASE_REQUISITION_MST A,INV_PURCHASE_REQUISITION_DTLS B,INV_ITEMISSUE_REQUISITION_DTLS C,INV_ITEM_ISSUE_REQUISITION_MST D
		WHERE A.ID = B.MST_ID AND B.PRODUCT_ID = C.PRODUCT_ID AND C.MST_ID = D.ID AND A.STATUS_ACTIVE=1 AND 
		A.IS_DELETED=0 AND A.ENTRY_FORM=69 AND A.REQ_VERSION=2 $company  $item_category_id  $order_rcv_date $get_cond $credientian_cond $store_cond $requisition_year_cond AND D.ITEMISSUE_REQ_PREFIX_NUM = $itm_iss_req_dem
		GROUP BY A.ID, A.REQU_PREFIX_NUM, A.REQUISITION_DATE, A.COMPANY_ID, A.ITEM_CATEGORY_ID, A.LOCATION_ID, A.DEPARTMENT_ID, A.SECTION_ID, A.MANUAL_REQ, A.STORE_NAME, A.INSERTED_BY, A.READY_TO_APPROVE, A.REQ_BASIS,A.IS_APPROVED, D.ITEMISSUE_REQ_PREFIX_NUM
		ORDER BY A.ID DESC";
	}
	else
	{
		$sql= "SELECT A.ID, A.REQU_PREFIX_NUM, A.REQUISITION_DATE, A.COMPANY_ID, A.ITEM_CATEGORY_ID, A.LOCATION_ID, A.DEPARTMENT_ID, A.SECTION_ID, A.MANUAL_REQ, A.STORE_NAME, A.INSERTED_BY, A.READY_TO_APPROVE, A.REQ_BASIS,A.IS_APPROVED FROM INV_PURCHASE_REQUISITION_MST A WHERE A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.ENTRY_FORM=69 AND A.REQ_VERSION=2 $company  $item_category_id  $order_rcv_date $get_cond $credientian_cond $store_cond $requisition_year_cond ORDER BY A.ID DESC";
	}
	//echo $sql;
	$sql_res=sql_select($sql);
	foreach($sql_res as $row)
	{
		$indent_arr = $row["ITEMISSUE_REQ_PREFIX_NUM"];
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array("select id,store_name from lib_store_location where company_id='$data[0]' and status_active=1",'id','store_name');
	$user_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");
	$is_approved_arr=array(0=>'No', 1=>'Yes', 2=>'No', 3=>'Partial Approved');

	if($indent_arr!=''){
		$width = 1100;
	}
	else{
		$width = 1000;
	}
	?>
	<div>
		<table width="<?=$width?>" cellpadding="0" cellspacing="0" border="1" style="margin-left:8px;" class="rpt_table" rules="all" align="left">
	        <thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Requisition No</th>
					<th width="100">Requisition Date</th>
					<? if($indent_arr!=''){?>
					<th width="100">Item Issue Req No</th>
					<?}?>
					<th width="100">Basis</th>
					<th width="140">Company</th>
					<th width="130">Location</th>
					<!-- <th width="100">Department</th> -->
					<!-- <th width="100">Section</th> -->
					<th width="140">Store Name</th>
					<!-- <th width="80">Manual Req</th> -->
					<th width="80">Insert By</th>
					<th width="60">Ready To Approve</th>
					<th>Approval Status</th>
				</tr>
	        </thead>
	     </table>
	     <div style="width:<?=$width+20;?>px; overflow-y:scroll; max-height:270px; margin-left:8px">
	     	<table width="<?=$width?>" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
				<?
				$i = 1;
	            foreach($sql_res as $row)
	            {
	                if($i%2==0) $bgcolor="#E9F3FF"; 
	                else $bgcolor="#FFFFFF";
					if($row['REQ_BASIS']==4)
					{ 
						$basis = "Independent";
					}
					elseif ($row['REQ_BASIS']==7) 
					{
						$basis = "Requisition";
					}
					else
					{
					 	$basis = "";
					}
					
					// echo $department_arr[$row["DEPARTMENT_ID"]]; 
					// echo $section_arr[$row["SECTION_ID"]];
					// echo $row["MANUAL_REQ"];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row['ID']; ?>);">
	                	<td width="30"><? echo $i; ?></td>
			            <td width="100"><? echo $row["REQU_PREFIX_NUM"]; ?></td>
			            <td width="100"><? echo change_date_format($row["REQUISITION_DATE"]); ?></td>
						<? if($indent_arr!=''){?>
			            <td width="100"><? echo $row["ITEMISSUE_REQ_PREFIX_NUM"]; ?></td>
						<?}?>
			            <td width="100"><?  echo $basis; ?></td>
			            <td width="140"><? echo $company_arr[$row["COMPANY_ID"]]; ?></td>
			            <td width="130"><? echo $location_arr[$row["LOCATION_ID"]]; ?></td>
			            <td width="140"><? echo $store_arr[$row["STORE_NAME"]]; ?></td>
			            <td width="80"><? echo $user_library[$row["INSERTED_BY"]]; ?></td>
			            <td width="60"><? if ($row["READY_TO_APPROVE"]==1) echo 'Yes'; else echo 'No'; ?></td>
			            <td><? echo $is_approved_arr[$row["IS_APPROVED"]]; ?></td>
					</tr>
	            	<?
					$i++;
	            }
				?>
			</table>
	    </div>
    </div>
	<?

	//echo  create_list_view("list_view", "Requisition No,Requisition Date,Company,Location,Department,Section,Store Name,Manual Req", "80,80,100,120,100,100,120,80","890","250",0, $sql , "js_set_value", "id", "",1,"0,0,company_id,location_id,department_id,section_id,store_name,0", $arr , "requ_prefix_num,requisition_date,company_id,location_id,department_id,section_id,store_name,manual_req","purchase_requisition_v2_controller","",'0,3,0,0,0,0,0,0') ;
	exit();
}

if ($action=="load_php_requ_popup_to_form")
{
	$nameArray=sql_select( "select id, requ_no, company_id, item_category_id, location_id, division_id, department_id, ready_to_approve, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, remarks, reference, manual_req, is_approved, req_by, iso_no, priority_id, requisition_id, tenor, justification_value, req_basis from inv_purchase_requisition_mst where id='$data'" );

	/*---------------additional code--------------*/

	/*$nameArray=sql_select( "select id,requ_no,company_id,item_category_id,location_id,division_id,department_id,section_id,requisition_date,store_name,pay_mode,source,cbo_currency,delivery_date,remarks,brand_name,model,origin,manual_req,is_approved from inv_purchase_requisition_mst where id='$data'" );*/
  $sql_cause="select MAX(id) as id from fabric_booking_approval_cause where  entry_form=1  and booking_id='$data' and approval_type=0 and status_active=1 and is_deleted=0";
	//echo $sql_cause; //die;
  	$app_cause = '';
	$nameArray_cause=sql_select($sql_cause);
	if(count($nameArray_cause)>0){
		foreach($nameArray_cause as $row)
		{
			$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
			$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
		}
	}
	

  foreach ($nameArray as $row)
  {

	  echo "document.getElementById('txt_requisition_no').value 		= '".$row[csf("requ_no")]."';\n";
	  echo "document.getElementById('txt_not_approve_cause').value 		= '".$app_cause."';\n";
	  echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
	  echo "document.getElementById('cbo_req_basis').value 				= '".$row[csf("req_basis")]."';\n";
	//   echo "$('#cbo_company_name').attr('disabled','true')".";\n";
	  //echo "document.getElementById('cbo_item_category_id').value 		= '".$row[csf("item_category_id")]."';\n";
	  //echo "$('#cbo_item_category_id').attr('disabled',true);\n";
	  //echo "show_list_view('".$row[csf("item_category_id")]."','item_category_details', 'item_category_div', 'requires/purchase_requisition_v2_controller', '' );\n";
	  echo "load_drop_down( 'requires/purchase_requisition_v2_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location','location_td');\n";
	  echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
	  echo "load_drop_down( 'requires/purchase_requisition_v2_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_division','division_td');\n";
	  echo "document.getElementById('cbo_division_name').value			= '".$row[csf("division_id")]."';\n";
	  echo "load_drop_down( 'requires/purchase_requisition_v2_controller', document.getElementById('cbo_division_name').value, 'load_drop_down_department','department_td');\n";
	  echo "document.getElementById('cbo_department_name').value		= '".$row[csf("department_id")]."';\n";
	  echo "load_drop_down( 'requires/purchase_requisition_v2_controller', document.getElementById('cbo_department_name').value, 'load_drop_down_section','section_td');\n";
	  echo "document.getElementById('cbo_section_name').value			= '".$row[csf("section_id")]."';\n";
	  echo "document.getElementById('txt_date_from').value				= '".change_date_format($row[csf("requisition_date")])."';\n";
	  echo "load_drop_down( 'requires/purchase_requisition_v2_controller',document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_stor','stor_td');\n";
	  echo "document.getElementById('cbo_store_name').value				= '".$row[csf("store_name")]."';\n";
	  echo "document.getElementById('cbo_pay_mode').value				= '".$row[csf("pay_mode")]."';\n";

	  if($row[csf("pay_mode")]==4)
	  {
		  $mrr_wo_check=return_field_value("a.id as id","inv_receive_master a, inv_transaction b"," a.id=b.mst_id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form = 20 and a.receive_basis = 7 and a.booking_id='".$row[csf("id")]."'","id");
	  }
	  else
	  {
		  $mrr_wo_check=return_field_value("a.id as id","wo_non_order_info_mst a, wo_non_order_info_dtls b, inv_purchase_requisition_dtls c","a.id=b.mst_id and b.requisition_dtls_id=c.id and a.entry_form in(145,146,147) and a.wo_basis_id=1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.mst_id='".$row[csf("id")]."'","id");
	  }

	  /*if($mrr_wo_check !="")
	  {
		  echo "$('#cbo_pay_mode').attr('disabled',true);\n";
	  }
	  else
	  {
		   echo "$('#cbo_pay_mode').attr('disabled',false);\n";
	  }*/

	  echo "document.getElementById('cbo_source').value					= '".$row[csf("source")]."';\n";
	  echo "document.getElementById('cbo_currency_name').value			= '".$row[csf("cbo_currency")]."';\n";
	  echo "document.getElementById('txt_date_delivery').value			= '".change_date_format($row[csf("delivery_date")])."';\n";
	  echo "document.getElementById('txt_remark').value				= '".$row[csf("remarks")]."';\n";
	  echo "document.getElementById('txt_reference').value				= '".$row[csf("reference")]."';\n";
	  echo "document.getElementById('txt_iso_no').value					= '".$row[csf("iso_no")]."';\n";
	  echo "document.getElementById('cbo_priority_id').value			= '".$row[csf("priority_id")]."';\n";
	  echo "document.getElementById('cbo_requisition_id').value			= '".$row[csf("requisition_id")]."';\n";
	  echo "document.getElementById('txt_tenor').value					= '".$row[csf("tenor")]."';\n";
	//   echo "document.getElementById('justification_value').value		= '".$row[csf("justification_value")]."';\n";
	  echo "document.getElementById('txt_req_by').value					= '".$row[csf("req_by")]."';\n";
	  echo "document.getElementById('cbo_ready_to_approved').value		= '".$row[csf("ready_to_approve")]."';\n";
	  /*---------additional code----------------*/

	 /* echo "document.getElementById('txt_brand').value				= '".$row[csf("brand_name")]."';\n";

	  echo "document.getElementById('txt_model_name').value				= '".$row[csf("model")]."';\n";

	  echo "$('#cbo_origin').val('".$row[csf("origin")]."');\n";*/


	  echo "document.getElementById('txt_manual_req').value				= '".$row[csf("manual_req")]."';\n";
	  echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";

	  echo "document.getElementById('is_approved').value          		= '" . $row[csf("is_approved")] . "';\n";
	 if($row[csf("requisition_id")] == 1 ||$row[csf("requisition_id")] == 2)
	  {
		echo "document.getElementById('justification').classList.remove('formbutton');\n";
		echo "document.getElementById('justification').classList.add('formbutton_disabled');\n";
		echo "$('#justification_value').val('');\n";
	  }elseif($row[csf("requisition_id")] == 3 ||$row[csf("requisition_id")] == 4){
		echo "document.getElementById('justification').classList.remove('formbutton_disabled');\n";
		echo "document.getElementById('justification').classList.add('formbutton');\n";
		echo "document.getElementById('justification_value').value		= '".$row[csf("justification_value")]."';\n";
	  }

	  if($row[csf("is_approved")] == 1)
	  {
		 echo "$('#approved').text('Approved');\n";
		 echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
	  }
	  elseif($row[csf("is_approved")] == 3)
	  {
	  	echo "$('#approved').text('Partial Approved');\n";
		echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
	  }
	  else{

		 echo "$('#approved').text('');\n";
		 echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
	  }

	  /*if($row[csf("is_approved")]==3){
		  $is_approved=1;
	  }else{
		   $is_approved=$row[csf("is_approved")];
	  }
	  echo "document.getElementById('is_approved').value          		= '".$is_approved."';\n";

	  if($is_approved==1)
	  {
		 echo "$('#approved').text('Approved');\n";
		 echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
	  }
	  else
	  {
		 echo "$('#approved').text('');\n";
		 echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
	  }*/

	  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_purchase_requisition',1);\n";
  }

  exit();
}

if ($action=="purchase_manual_requisition_popup")
{
 	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
<script>
	  function js_set_value(manual_req)
	  {
		  document.getElementById('txt_manual_req').value=manual_req;
		  parent.emailwindow.hide();
	  }
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="purchaserequisition_6"  id="purchaserequisition_6" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>
                        <th width="150">Company Name</th>
                        <th width="150">Item Category</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="txt_manual_req">
							<?
								echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, "");
                            ?>
                    	</td>
                   		<td>
							<?
								echo create_drop_down( "cbo_item_category_id", 170,$item_category,"", 1, "-- Select --", $selected, "","","","","","1,2,3,4,12,13,14");
                            ?>
                        </td>
                    	<td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 	</td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'manual_purchase_requisition_list_view', 'search_div1', 'purchase_requisition_v2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                       </td>
        			</tr>
             	</table>
          	</td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
				<? echo load_month_buttons(1);  ?>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top" id="search_div1">
            </td>
        </tr>
    </table>
    </form>
   </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="manual_purchase_requisition_list_view")
{
	$data=explode('_',$data);
	
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $item_category_id=" and item_category_id='$data[1]'"; else $item_category_id="";//{ echo "Please Select item category."; die; }
	 if($db_type==2)
	 {
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], 'mm-dd-yyyy','/',1)."' and '".change_date_format($data[3],'mm-dd-yyyy','/',1)."'"; else $order_rcv_date ="";
	 }
	 if($db_type==0)
	 {
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $order_rcv_date ="";
	 }

	 $sql= "select id,requ_prefix_num,requisition_date,company_id,item_category_id,location_id,department_id,section_id,manual_req from inv_purchase_requisition_mst where status_active=1 and is_deleted=0 $company  $item_category_id $order_rcv_date order by id asc";

	$company=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');

	$arr=array (2=>$company,3=>$location,4=>$department,5=>$section);
	//,Item Category
	echo  create_list_view("list_view", "Requisition No,Requisition Date,Company,Location,Department,Section,Manual Req", "80,80,100,100,90,90,80","850","250",0, $sql , "js_set_value", "manual_req", "",1,"0,0,company_id,location_id,department_id,section_id,0", $arr , "requ_prefix_num,requisition_date,company_id,location_id,department_id,section_id,manual_req","purchase_requisition_v2_controller","",'0,3,0,0,0,0,0') ;
	exit();
}

if ($action=="item_category_details")
{
	//echo $data;die;
	/*if($data==5 || $data==6 || $data==7 || $data==8 || $data==9 || $data==10 || $data==11)
	{*/
	$nameArray=sql_select( "select id,user_given_code_status from variable_settings_inventory where item_category_id='$data' and user_given_code_status=1 and status_active=1 and is_deleted=0" );
	foreach ($nameArray as $row)
 	{
		$user_given_code_status=$row[csf('user_given_code_status')];
	}


	?>

	<table class="rpt_table" width="1090px" cellspacing="1" id="tbl_purchase_item">
    <thead>
        <th width="80">Item Account</th>
        <th width="80">Item Group</th>
        <th width="80">Item Sub. Group</th>
        <th width="120">Item Description</th>
        <th width="60">Item Size</th>
        <th width="80">Required For</th>
        <th width="50">UOM</th>
        <th width="50" class="must_entry_caption">Quantity</th>
        <th width="50">Rate</th>
        <th width="55">Amount</th>
        <th width="50">Stock</th>
        <th width="55">Re-Order Level</th>
        <th width="100">Remarks</th>
        <th width="60">Status</th>
         <!-- additional code -->
       <th width="80">Brand</th>
       <th width="80">Model</th>
       <th width="80">Origin</th>
       <!-- <th width="120">Origin</th>
       <th width="120">MOdel</th> -->
    </thead>
    <tbody>
        <tr class="general" >
            <td>
            <?
            if($user_given_code_status==1)
            {
				?>
					<input type="text" name="itemaccount_1" id="itemaccount_1" class="text_boxes" value="" style="width:80px;" maxlength="200" placeholder="Double click"  onDblClick="openmypage()" readonly />
				<?
            }
            else
            {
				?>
                    <input type="text" name="itemaccount_1" id="itemaccount_1" class="text_boxes" value="" style="width:80px;" maxlength="200" readonly/>
				<?
            }
            ?>
            </td>
            <td>
            <?
            if($user_given_code_status==1)
            {
				?>
                    <input type="text" name="txtitemgroupid_1" id="txtitemgroupid_1" class="text_boxes" value="" style="width:80px;" maxlength="200" readonly/>
				<?
            }
            else
            {
				?>
                    <input type="text" name="txtitemgroupid_1" id="txtitemgroupid_1" class="text_boxes" value="" style="width:80px;" maxlength="200" readonly placeholder="Double click"  onDblClick="openmypage()"/>
				<?
            }
            ?>
            </td>
            <td>
                <input type="text" name="sub_group_1" id="sub_group_1" class="text_boxes" value="" style="width:80px;" maxlength="200" readonly />
            </td>
            <td>
                <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:120px;" maxlength="200" readonly />
				<input type="hidden" name="item_1" id="item_1" value="" />
            </td>


            <td id="group_td">
                <input type="hidden" name="hiddenid_1" id="hiddenid_1" />
                <input type="text" name="itemsize_1" id="itemsize_1" class="text_boxes" value="" style="width:60px;" maxlength="200" readonly />
                <input type="hidden" name="hiddenitemgroupid_1" id="hiddenitemgroupid_1" class="text_boxes" value="" style="width:100px;" maxlength="200" readonly />
            </td>
            <td>
                <!--<input type="text" name="txtreqfor_1" id="txtreqfor_1" class="text_boxes" value="" style="width:70px;" maxlength="200" />-->
                <?
					echo create_drop_down( "txtreqfor_1", 90, $use_for,'', 1, '-- Select --',0,'',0,'');
				?>
            </td>
            <td>
            	<?
					echo create_drop_down( "txtuom_1", 62, $unit_of_measurement,'', 1, '-- Select --',0,'',0,'');
				?>
                <!-- <input type="text" name="txtuom_1" id="txtuom_1" class="text_boxes" value="" style="width:50px;" maxlength="200" readonly />
                <input type="hidden" name="hiddentxtuom_1" id="hiddentxtuom_1" class="text_boxes" value="" style="width:60px;" maxlength="200" readonly /> -->
            </td>
            <td>
                <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:60px;" onKeyUp="calculate_val()"/>
            </td>
            <td>
                <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:51px;" onKeyUp="calculate_val()" />
            </td>
            <td>
                <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:49px; text-align:right;" readonly />
            </td>
            <td>
                <input type="text" name="stock_1" id="stock_1" class="text_boxes_numeric" value="" style="width:60px;" maxlength="200" readonly />
            </td>
            <td><input type="hidden" name="update_id" id="update_id" />
                <input type="text" name="reorderlable_1" id="reorderlable_1" class="text_boxes_numeric" value="" style="width:60px;" maxlength="200" readonly />
            </td>
            <td>
                <input type="text" name="txtvehicle_1" id="txtvehicle_1" class="text_boxes" value="" style="width:87px;" />
            </td>
            <td>
                <input type="text" name="txt_remarks_1" id="txt_remarks_1" class="text_boxes" value="" style="width:95px;" />
            </td>
            <td>                
                <input type="hidden" name="hidden_update_id" id="hidden_update_id" readonly= "readonly" /> <!-- for update -->
                <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes_numeric"  readonly= "readonly" value="0" />
				<? echo create_drop_down( "cbostatus_1", 70, $row_status,'', 0, '',1,0); ?>
            </td>

            <!--  additional code -->

              <!-- <td><Input type="text" name="txt_brand" ID="txt_brand"  style="width:100px" class="text_boxes" value="" autocomplete="off" /></td> -->
              <td><Input type="text" name="txtbrand_1" ID="txtbrand_1"  style="width:80px" class="text_boxes" autocomplete="off" /></td>

              <td><Input type="text" name="txtmodelname_1" ID="txtmodelname_1"  style="width:80px" class="text_boxes" autocomplete="off" /></td>

              <td><? //new
              echo create_drop_down( "cboOrigin_1", 155, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0 );
              ?></td>




               <!-- <td>Origin</td>
              <td><?
              echo create_drop_down( "cbo_origin", 155, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0 );
              ?></td>-->



        </tr>
    </tbody>
    </table>
    <table width="100%">
    	<tr><td>&nbsp;</td></tr>
        <tr>
            <td width="80%" height="20" valign="middle" align="center" class="button_container">
                <?
                    echo load_submit_buttons( $permission, "fnc_purchase_requisition_dtls", 0,0 ,"reset_form('purchaserequisition_1*purchaserequisition_2','item_category_div*purchase_requisition_list_view_dtls*approved','','','disable_enable_fields(\'cbo_company_name\');$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();')",2) ;
                ?>
            </td>
        </tr>
    </table>
	<?
	//}
	exit();
}

if($action=="item_description_autocomplete")
{
	$data=explode("_",$data);
	$cbo_item_category_id=$data[0];
	$cbo_item_group=$data[1];
	$search_cond="";
	if ($cbo_item_category_id >0) $search_cond.=" and item_category_id=$cbo_item_category_id";
	if ($cbo_item_group >0) $search_cond.=" and item_group_id=$cbo_item_group";	

	$sql = "select distinct(item_description) as label from product_details_master where item_category_id not in(1,2,3,12,13,14,24,25) and status_active=1 and is_deleted=0 $search_cond";
	$result = sql_select($sql);
	$itemDescriptionArr = array();
	foreach($result as $key =>$val){
		$itemDescriptionArr[$key]["label"]=$val[csf("label")];
	}
	echo json_encode($itemDescriptionArr);
    exit();
}


if($action=="account_order_popup")
{
	echo load_html_head_contents("Item Description Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	list($company,$store_id,$update_id,$req_basis)=explode('_',$data);
	$store_item_cat=return_field_value("item_category_id","lib_store_location","company_id=$company and id=$store_id","item_category_id");
	$sql_select_dtls_data = sql_select("select b.item_category from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id=$update_id and a.entry_form=69 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$check_mixed_category_arr=array();
	foreach($sql_select_dtls_data as $row)
	{
		$check_mixed_category_arr[$row[csf('item_category')]]=$row[csf('item_category')];
	}

	$previous_category_ids="";
	if (!empty($check_mixed_category_arr)) $previous_category_ids=implode(',',$check_mixed_category_arr);

	$category_mixing_variable =return_field_value("allocation","variable_settings_inventory","company_name=$company and variable_list=44 and status_active=1 and is_deleted=0 order by id desc ","allocation");
	//$category_ids_varriable=$previous_category_ids.'##'.$category_mixing_variable;
	?>
	<script>

	var selected_id = new Array(), selected_name = new Array(); selected_attach_id = new Array();
	var category_id_arr_chk = new Array();

	var category_mixing_variable='<? echo $category_mixing_variable; ?>';
	var previous_category_ids='<? echo $previous_category_ids; ?>';
	if (previous_category_ids != ""){
		var category_id_arr_chk=previous_category_ids.split(',');
	}
	
	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
		tbl_row_count = tbl_row_count - 1;

		for( var i = 1; i <= tbl_row_count; i++ ) {
			 eval($('#tr_'+i).attr("onclick"));
		}
	}

	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

	function js_set_value(id)
	{
		var str=id.split("_");
		$('#re_order_lebel').val(str[2]);
		//### reorder level qnty check ##//
		if(str[4]*1 > 0 && str[3]*1 > str[4]*1)
		{
			alert("Stock Should be less then or Equal Re-Order Level");return;
		}

		// Category mix check according to varriable set up
		//alert(category_mixing_variable);
		var category_id=str[5];
		if (category_mixing_variable != 1)
		{
			if(category_id_arr_chk.length==0)
			{
				category_id_arr_chk.push( category_id );
			}
			else if( jQuery.inArray( category_id, category_id_arr_chk )==-1 &&  category_id_arr_chk.length>0)
			{
				alert("Category Mixed is Not Allowed");
				return;
			}
		}
		
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		str=str[1];
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
		}
		var id = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );

		$('#item_1').val( id );


	}

	function auto_complete_itemDescription() 
	{
		var cbo_item_category_id=document.getElementById('cbo_item_category_id').value;
		//alert(cbo_item_category_id);
		var cbo_item_group=document.getElementById('cbo_item_group').value;
		var item_description = return_global_ajax_value( cbo_item_category_id+'_'+cbo_item_group, 'item_description_autocomplete', '', 'purchase_requisition_v2_controller');
		item_descriptionInfo = eval(item_description);
		$("#txt_item_description").autocomplete({
		    source: item_descriptionInfo,
			select: function (e, ui) {
				$(this).val(ui.item.label);
			}
		});
	}
	
	//alternative way
	/*var str_item_description = [<? //echo substr(return_library_autocomplete( "select distinct(item_description) from product_details_master where item_category_id not in(1,2,3,12,13,14,24,25)", "item_description"  ), 0, -1); ?>];

	function auto_complete_itemDescription()
	{
		$("#txt_item_description").autocomplete({
			source: str_item_description
		});
	}*/

	function openmypage_itemSubgroup()
	{
		if( form_validation('cbo_item_category_id','Item Category')==false )
		{
			return;
		}
		var company = <? echo $company; ?>;
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var cbo_item_group = $("#cbo_item_group").val();
		var sub_group_prod_id = $("#sub_group_prod_id").val();
		var txt_item_sub_group = $("#txt_item_sub_group").val();
		var sub_group_no = $("#sub_group_no").val();
		var page_link='purchase_requisition_v2_controller.php?action=item_sub_group_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&cbo_item_group='+cbo_item_group+'&sub_group_prod_id='+sub_group_prod_id+'&txt_item_sub_group='+txt_item_sub_group+'&sub_group_no='+sub_group_no;
		var title="Search Item Sub Group Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_sub_group_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_sub_group_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var item_sub_group_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_item_sub_group").val(item_sub_group_des);
			$("#sub_group_prod_id").val(item_sub_group_id);
			$("#sub_group_no").val(item_sub_group_no);
		}
	}

    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:980px;">
            <table width="970" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="85">Re-Order Label</th>
                    <th width="155">Item Category</th>
                    <th width="135">Item Group</th>
                    <th width="130">Item Sub-Group Name</th>
                    <th width="100">Item Code</th>
                    <th width="100">Item Number</th>
                    <th width="135">Item Description</th>
                    <th width="125">Store Name</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                    <input type="hidden" id="item_1" /> 
                    <input type="hidden" id="re_order_lebel" />
                    <input type="hidden" id="issue_req_dtls_1" value="0" />
                    </th>
                </thead>
                <tbody>
                	<tr class="general">
                    	<td>
                        <?
							echo create_drop_down( "cbo_re_order_level", 80,$yes_no,"", 1, "--Select--",0);
						?>
                        </td>
                    	<td>
                        <?
							echo create_drop_down( "cbo_item_category_id", 150,$item_category,"", 1, "-- Select --", $selected, "load_drop_down( 'purchase_requisition_v2_controller', this.value, 'load_drop_down_group','group_td');","",$store_item_cat,"","","1,2,3,12,13,14,24,25");
						?>
                        </td>
                        <td align="center" id="group_td">
                    		<!-- <input type="text" style="width:130px" class="text_boxes" name="txt_item_group" id="txt_item_group" /> -->
                    		<?
                    			echo create_drop_down("cbo_item_group",130,$blank_array,"",1,"-- Select --",$selected, "" );
                    		?>
                        </td>
                        <td align="center">
                    		<input type="text" style="width:120px" class="text_boxes" name="txt_item_sub_group" id="txt_item_sub_group" onDblClick="openmypage_itemSubgroup()" placeholder="browse" readonly />
                            <input type="hidden" name="sub_group_prod_id" id="sub_group_prod_id"/>
                            <input type="hidden" name="sub_group_no" id="sub_group_no"/>
                        </td>
                        <td align="center">
                    		<input type="text" style="width:90px" class="text_boxes" name="txt_item_code" id="txt_item_code" />
                        </td>
                        <td align="center">
                    		<input type="text" style="width:90px" class="text_boxes" name="txt_item_number" id="txt_item_number" />
                        </td>
                        <td align="center" id="description_td">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description" onFocus="auto_complete_itemDescription();" />
                        	 <?
                    			//echo create_drop_down("cbo_item_description",130,$blank_array,"",1,"-- Select --",$selected, "" );
                    		?>
                        </td>
                        <td align="center">
                        	 <?
                    			echo create_drop_down( "cbo_store_name", 120,"select a.id,a.store_name from lib_store_location a, lib_store_location_category b  where a.id=b.store_location_id and a.is_deleted=0 and a.company_id='$company' and a.status_active=1 and b.category_type not in(1,2,3,12,13,14,24,25) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $store_id, "",1);
                    		?>
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+document.getElementById('cbo_item_group').value+'**'+'<? echo $store_id; ?>'+'**'+'<? echo $store_item_cat; ?>'+'**'+document.getElementById('cbo_re_order_level').value+'**'+document.getElementById('sub_group_prod_id').value+'**'+document.getElementById('txt_item_number').value, 'account_order_popup_list_view', 'search_div', 'purchase_requisition_v2_controller','setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                    	</td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
            <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="account_order_popup_list_view")
{

	echo load_html_head_contents("Item Creation popup", "../../", 1, 1,'','1','');
	extract($_REQUEST);
	list($company_name,$item_category_id,$item_description,$item_code,$item_group,$store_id,$store_item_cat,$re_order_level,$sub_group_prod_id,$txt_item_number)=explode('**',$data);
	//echo $re_order_level."==".$item_sub_group;
    $search_con ="";
    $item_description_cond="";
	if($item_description != "") {$item_description_cond =" and upper(a.item_description) like upper('%$item_description%')";}

	if ($company_name!=0) $company=" and a.company_id='$company_name'"; //else { echo "Please Select Company First."; die; }
	if ($item_category_id!=0) $item_category_list=" and a.item_category_id='$item_category_id'"; //else { echo "Please Select Item Category."; die; }
	//if($item_group ==0 && $item_code =="") { echo "Please Select Item Group or Item Code."; die; }
	//if($item_description !=0){$search_con .=" and a.id = $item_description";}

	if ($item_code != "") $search_con .= " and upper(a.item_code) LIKE upper('%$item_code%')";
	if($txt_item_number!=""){$search_con .= " and a.item_number like('%$txt_item_number')";}
	//if($item_group!=""){$search_con .= " and b.item_name like('%$item_group%')";}
	if($item_group !=0){$search_con .= " and a.item_group_id = '$item_group'";}
	if($sub_group_prod_id !=""){$search_con .= " and a.id in($sub_group_prod_id)";}
	if ($re_order_level==1) {$search_con .= " and a.re_order_label>0";}

	$entry_cond="";
	if(str_replace("'","",$item_category_id)==4) $entry_cond="and entry_form=20";

	
	/*-------------additional code---------------------------*/
	$stor_item_cond="";
	if($store_item_cat!="") $stor_item_cond=" and item_category_id in($store_item_cat)";
	

	$origin_lib=return_library_array("select country_name,id from lib_country where is_deleted=0  and status_active=1", "id", "country_name");
	$item_group_arr=return_library_array("select id, item_name from lib_item_group where is_deleted=0 and status_active=1", "id", "item_name");
	$sql="select min(a.id) as id, a.item_account, a.item_code, a.origin, a.sub_group_name, a.item_category_id, a.item_description, a.brand_name, a.model, a.item_size, a.item_group_id, a.unit_of_measure, a.status_active, a.item_number, a.order_uom, sum(a.current_stock) as current_stock, min(a.re_order_label) as re_order_label, min(a.maximum_label) as maximum_label,
	listagg(cast(a.id as varchar(4000)),',') within group (order by a.id) as ids
	from  product_details_master a
	where a.status_active=1 and a.is_deleted=0 and a.item_group_id>0 $company $search_con $item_category_list $entry_cond $stor_item_cond $item_description_cond
	group by a.item_account, a.item_code, a.origin, a.sub_group_name, a.item_category_id, a.item_description, a.brand_name, a.model, a.item_size, a.item_group_id, a.unit_of_measure, a.status_active, a.item_group_id, a.item_number, a.order_uom
	order by item_description";
	//echo $sql;
	$sql_res=sql_select($sql); $tot_rows=0; $prodIds=''; $prod_arr=array();
	foreach($sql_res as $row)
	{
		$tot_rows++;
		$prodIds.=$row[csf("ids")].",";
		$prod_arr[$row[csf("id")]]["item_account"]=$row[csf("item_account")];
		$prod_arr[$row[csf("id")]]["item_code"]=$row[csf("item_code")];
		$prod_arr[$row[csf("id")]]["origin"]=$row[csf("origin")];
		$prod_arr[$row[csf("id")]]["sub_group_name"]=$row[csf("sub_group_name")];
		$prod_arr[$row[csf("id")]]["item_category_id"]=$row[csf("item_category_id")];
		$prod_arr[$row[csf("id")]]["item_description"]=$row[csf("item_description")];
		$prod_arr[$row[csf("id")]]["brand_name"]=$row[csf("brand_name")];
		$prod_arr[$row[csf("id")]]["model"]=$row[csf("model")];
		$prod_arr[$row[csf("id")]]["item_size"]=$row[csf("item_size")];
		$prod_arr[$row[csf("id")]]["item_group_id"]=$row[csf("item_group_id")];
		$prod_arr[$row[csf("id")]]["unit_of_measure"]=$row[csf("order_uom")];
		$prod_arr[$row[csf("id")]]["current_stock"]=$row[csf("current_stock")];
		$prod_arr[$row[csf("id")]]["re_order_label"]=$row[csf("re_order_label")];
		$prod_arr[$row[csf("id")]]["maximum_label"]=$row[csf("maximum_label")];
		$prod_arr[$row[csf("id")]]["status_active"]=$row[csf("status_active")];
		$prod_arr[$row[csf("id")]]["item_group_id"]=$row[csf("item_group_id")];
		$prod_arr[$row[csf("id")]]["item_number"]=$row[csf("item_number")];
	}
	//echo "<pre>";
	//print_r($prod_arr);
	unset($sql_res);
	$prodIds=chop($prodIds,','); $prodIds_trans_cond="";

	if($db_type==2 && $tot_rows>1000)
	{
		$prodIds_trans_cond=" and (";
		$prodIdsArr=array_chunk(explode(",",$prodIds),999);
		foreach($prodIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$prodIds_trans_cond.=" b.prod_id in ($ids) or ";
		}
		$prodIds_trans_cond=chop($prodIds_trans_cond,'or ');
		$prodIds_trans_cond.=")";
	}
	else
	{
		$prodIds_trans_cond=" and b.prod_id in ($prodIds)";
	}

	if($prodIds!="")
	{
		$sql_trns="Select b.PROD_ID, b.TRANSACTION_TYPE, b.CONS_QUANTITY, a.ITEM_GROUP_ID, a.ITEM_DESCRIPTION, a.SUB_GROUP_NAME, a.ITEM_SIZE, a.MODEL, a.ITEM_NUMBER, a.ITEM_CODE
		from inv_transaction b, product_details_master a 
		where b.prod_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in (1,2,3,4,5,6) and b.store_id=$store_id $prodIds_trans_cond $company";
		//echo $sql_trns;die;
		//sum((case when c.transaction_type in(1,4,5) and c.status_active=1 then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) and c.status_active=1 then c.cons_quantity else 0 end)) as balance_stock
		$sql_trns_res=sql_select($sql_trns); $trns_arr=array();
		foreach($sql_trns_res as $prow)
		{
			$item_key=$prow["ITEM_GROUP_ID"]."**".$prow["ITEM_DESCRIPTION"]."**".$prow["SUB_GROUP_NAME"]."**".$prow["ITEM_SIZE"]."**".$prow["MODEL"]."**".$prow["ITEM_NUMBER"]."**".$prow["ITEM_CODE"];
			if($prow["TRANSACTION_TYPE"]==1 || $prow["TRANSACTION_TYPE"]==4 || $prow["TRANSACTION_TYPE"]==5)
			{
				$trns_arr[$item_key]["rec"]+=$prow["CONS_QUANTITY"];
				$trns_arr[$item_key]["stock"]+=$prow["CONS_QUANTITY"];
			}
			else if($prow["TRANSACTION_TYPE"]==2 || $prow["TRANSACTION_TYPE"]==3 || $prow["TRANSACTION_TYPE"]==6)
			{
				$trns_arr[$item_key]["iss"]+=$prow["CONS_QUANTITY"];
				$trns_arr[$item_key]["stock"]-=$prow["CONS_QUANTITY"];
			}
		}
		unset($sql_trns_res);
	}
	
	//echo "<pre>";print_r($trns_arr);die;
	
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1260" class="rpt_table">
            <thead>
				<tr>
					<th colspan="17"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="90">Item Account</th>
					<th width="60">Item Code</th>
					<th width="60">Item Number</th>
					<th width="90">Item Category</th>
					<th width="60">Brand</th>
					<th width="70">Model</th>
					<th width="60">Origin</th>
					<th width="70">Sub Group Name</th>
					<th width="130">Item Description</th>
					<th width="60">Item Size</th>
					<th width="100">Item Group</th>
					<th width="60">Order UOM</th>
					<th width="80">Stock</th>
					<th width="70">ReOrder Level</th>
					<th width="70">Maximum Level</th>
					<th>Product ID</th>
				</tr>
            </thead>
     	</table>
     </div>
     <div style="width:1260px; max-height:250px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1240" class="rpt_table" id="list_view">
			<?
			$i=1;
            foreach( $prod_arr as $prod_id=>$val )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$item_key=$val["item_group_id"]."**".$val["item_description"]."**".$val["sub_group_name"]."**".$val["item_size"]."**".$val["model"]."**".$val["item_number"]."**".$val["item_code"];
				$stock=0;
				$stock=$trns_arr[$item_key]["rec"]-$trns_arr[$item_key]["iss"];
				if($val['re_order_label']=="") $val['re_order_label']=0;
				//echo $stock."=".$trns_arr[$item_key]["rec"]."=".$trns_arr[$item_key]["iss"]."=".$prod_id."="."<br>";
				if($re_order_level==1)
				{
					if($stock<=$val['re_order_label'])
					{
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $i.'_'.$prod_id.'_'.$re_order_level.'___'.$val['item_category_id']; ?>');" >
                            <td width="30" align="center"><p><? echo $i; ?></p></td>
                            <td width="90"><p><? echo $val["item_account"]; ?></p></td>
                            <td width="60" align="center"><p><? echo $val["item_code"]; ?></p></td>
                            <td width="60"><p><? echo $val["item_number"]; ?></p></td>
                            <td width="90"><p><? echo $item_category[$val["item_category_id"]]; ?></p></td>
                            <td width="60"><p><? echo $val["brand_name"]; ?></p></td>
                            <td width="70"><p><? echo $val["model"]; ?></p></td>
                            <td width="60"><p><? echo $origin_lib[$val["origin"]]; ?></p></td>
                            <td width="70"><p><? echo $val["sub_group_name"]; ?></p></td>
                            <td width="130"><p><? echo $val["item_description"]; ?></p></td>
                            <td width="60" align="center"><p><? echo $val["item_size"]; ?></p></td>
                            <td width="100"><p><? echo $item_group_arr[$val["item_group_id"]]; ?></p></td>
                            <td width="60" align="center"><p><? echo $unit_of_measurement[$val["unit_of_measure"]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($stock, 2); ?></p></td>
                            <td width="70" align="right"><p><? echo $val["re_order_label"]; ?></p></td>
                            <td width="70" align="right"><p><? echo $val["maximum_label"]; ?></p></td>
                            <td align="center"><p><? echo $prod_id; ?></p></td>
                        </tr>
                        <?
                        $i++;
					}
				}
				else
				{
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $i.'_'.$prod_id.'_'.$re_order_level.'_'.$stock.'_'.$val['re_order_label'].'_'.$val['item_category_id']; ?>');" >
                        <td width="30" align="center"><p><? echo $i; ?></p></td>
                        <td width="90"><p><? echo $val["item_account"]; ?></p></td>
                        <td width="60"><p><? echo $val["item_code"]; ?></p></td>
                        <td width="60"><p><? echo $val["item_number"]; ?></p></td>
                        <td width="90"><p><? echo $item_category[$val["item_category_id"]]; ?></p></td>
                        <td width="60"><p><? echo $val["brand_name"]; ?></p></td>
                        <td width="70"><p><? echo $val["model"]; ?></p></td>
                        <td width="60"><p><? echo $origin_lib[$val["origin"]]; ?></p></td>
                        <td width="70"><p><? echo $val["sub_group_name"]; ?></p></td>
                        <td width="130"><p><? echo $val["item_description"]; ?></p></td>
                        <td width="60" align="center"><p><? echo $val["item_size"]; ?></p></td>
                        <td width="100"><p><? echo $item_group_arr[$val["item_group_id"]]; ?></p></td>
                        <td width="60" align="center"><p><? echo $unit_of_measurement[$val["unit_of_measure"]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($stock, 2); ?></p></td>
                        <td width="70" align="right"><p><? echo $val["re_order_label"]; ?></p></td>
                        <td width="70" align="right"><p><? echo $val["maximum_label"]; ?></p></td>
                        <td align="center"><? echo $prod_id; ?></td>
                    </tr>
                    <?
					$i++;
				}
            }
			?>
			</table>
		</div>
        <table width="1240" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	<?
	exit();
}


if ($action == "load_drop_down_location_popup") 
{
	echo create_drop_down("cbo_location_name", 90, "select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1 $location_credential_cond", "id,location_name", 1, "-- Select --", $selected, "");
	exit();
}

if ($action == "load_drop_down_division_popup") 
{
	echo create_drop_down("cbo_division_name", 90, "select id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1", "id,division_name", 1, "-- Select --", $selected, "load_drop_down( 'purchase_requisition_v2_controller', this.value, 'load_drop_down_department_popup','department_td');");
	exit();
}

if ($action == "load_drop_down_department_popup") {
	echo create_drop_down("cbo_department_name", 90, "select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1", "id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'item_issue_requisition_controller', this.value, 'load_drop_down_section_popup','section_td');");
	exit();
}

if ($action == "load_drop_down_section_popup") {
	echo create_drop_down("cbo_section_name", 90, "select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1", "id,section_name", 1, "-- Select --", $selected, "load_drop_down( 'purchase_requisition_v2_controller', this.value, 'load_drop_down_sub_section_popup','sub_section_td');");
	exit();
}

if ($action == "load_drop_down_sub_section_popup") {
	$array = array(0 => "None");
	echo create_drop_down("cbo_sub_section_name", 90, $array, "", 1, "-- Select --", 1);
	exit();
}

if ($action == "item_issue_requisition_popup_search") 
{
	echo load_html_head_contents("Item Issue Requisition search From", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	list($company_id,$store_id,$update_id,$req_basis)=explode('_',$data);
	//echo $company_id.testre;
	$sql_select_dtls_data = sql_select("select b.item_category from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id=$update_id and a.entry_form=69 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$check_mixed_category_arr=array();
	foreach($sql_select_dtls_data as $row)
	{
		$check_mixed_category_arr[$row[csf('item_category')]]=$row[csf('item_category')];
	}

	$previous_category_ids="";
	if (!empty($check_mixed_category_arr)) $previous_category_ids=implode(',',$check_mixed_category_arr);
	
	$category_mixing_variable =return_field_value("allocation","variable_settings_inventory","company_name=$company_id and variable_list=44 and status_active=1 and is_deleted=0 order by id desc ","allocation");
	?>
    <script>

        var selected_id = new Array(), selected_name = new Array(); selected_attach_id = new Array();selected_req_dtls_id = new Array();
		var category_id_arr_chk = new Array();
	
		var category_mixing_variable='<? echo $category_mixing_variable; ?>';
		var previous_category_ids='<? echo $previous_category_ids; ?>';
		if (previous_category_ids != ""){
			var category_id_arr_chk=previous_category_ids.split(',');
		}
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
	
			for( var i = 1; i <= tbl_row_count; i++ ) {
				 eval($('#tr_'+i).attr("onclick"));
			}
		}
	
		function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}
	
		function js_set_value(id)
		{
			var str=id.split("_");
			$('#re_order_lebel').val(str[2]);
			
			//### reorder level qnty check ##//
			//if(str[4]*1 > 0 && str[3]*1 > str[4]*1)
			//{
				//alert("Stock Should be less then or Equal Re-Order Level");return;
			//}
	
			// Category mix check according to varriable set up
			var category_id=str[5];
			if (category_mixing_variable != 1)
			{
				if(category_id_arr_chk.length==0)
				{
					category_id_arr_chk.push( category_id );
				}
				else if( jQuery.inArray( category_id, category_id_arr_chk )==-1 &&  category_id_arr_chk.length>0)
				{
					alert("Category Mixed is Not Allowed");
					return;
				}
			}
			//alert(id);return;
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			
			if( jQuery.inArray(  str[1] , selected_id ) == -1 ) {
				selected_id.push( str[1] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1]  ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			
			
			if( jQuery.inArray(  str[6] , selected_req_dtls_id ) == -1 ) {
				selected_req_dtls_id.push( str[6] );
			}
			else {
				for( var i = 0; i < selected_req_dtls_id.length; i++ ) {
					if( selected_req_dtls_id[i] == str[6]  ) break;
				}
				selected_req_dtls_id.splice( i, 1 );
			}
			var req_id = '';
			for( var i = 0; i < selected_req_dtls_id.length; i++ ) {
				req_id += selected_req_dtls_id[i] + ',';
			}
			req_id = req_id.substr( 0, req_id.length - 1 );
			
	
			$('#item_1').val( id );
			$('#issue_req_dtls_1').val( req_id );
		}

        function item_issue_requisition_popup() {

            if (form_validation('cbo_company_id', 'Company') == false) {
                alert('Pls, Select Company.');
                return;
            }
            show_list_view(document.getElementById('cbo_company_id').value + '**' + document.getElementById('txt_date_from').value  + '**' + document.getElementById('txt_date_to').value + '**' + document.getElementById('txt_required_date').value + '**' + document.getElementById('cbo_location_name').value + '**' + document.getElementById('cbo_division_name').value + '**' + document.getElementById('cbo_department_name').value + '**' + document.getElementById('cbo_section_name').value + '**' + document.getElementById('cbo_sub_section_name').value + '**' + document.getElementById('cbo_delivery_point').value + '**' + document.getElementById('txt_system_id').value+ '**' + document.getElementById('cbo_year_selection').value, 'items_search_list_view', 'search_div', 'purchase_requisition_v2_controller', 'setFilterGrid(\'list_view\',-1)');
        }
        function fnc_sub_section() {
            $('#cbo_sub_section_name').css('display', 'none');
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:1230px;">
        <form name="searchitemreqfrm" id="searchitemreqfrm">
            <fieldset style="width:1230px; margin-left:3px">
                <legend>Search</legend>
                <table cellpadding="0" cellspacing="0" width="1130" class="rpt_table" rules="all">
                    <thead>
                    	<tr>
                            <th class="must_entry_caption">Company</th>
                            <th>Indent No.</th>
                            <th width="160">Indent Date Range</th>
                            <th align="right">Required Date</th>
                            <th align="right">Location</th>
                            <th align="right">Division</th>
                            <th align="right">Department</th>
                            <th align="right">Section</th>
                            <th align="right" style="display:none;">Sub Section</th>
                            <th align="right">Delivery Point</th>
                            <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/><input type="hidden" name="id_field" id="id_field" value=""/>
                            <input type="hidden" id="item_1" /> <input type="hidden" id="re_order_lebel" />
                            <input type="hidden" id="issue_req_dtls_1" value="0" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
							<?
							$company_sql = "select comp.id,comp.company_name from lib_company comp where  comp.status_active=1 and comp.is_deleted=0 $scred_company_id_cond order by company_name";
							echo create_drop_down("cbo_company_id", 144, $company_sql, "id,company_name", 1, "-- Select --",$company_id, "load_drop_down( 'purchase_requisition_v2_controller', this.value, 'load_drop_down_location_popup','location_td');load_drop_down( 'purchase_requisition_v2_controller', this.value, 'load_drop_down_division_popup','division_td');");

							?>
                        </td>
                        <td><input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes"  style="width:70px"></td>
                        <!-- <td><input type="text" name="txt_indent_date" id="txt_indent_date" class="datepicker" style="width:70px"></td> -->
						<td align="center" >
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
	                    </td> 
                        <td><input type="text" name="txt_required_date" id="txt_required_date" class="datepicker" style="width:70px" readonly></td>
                        <td id="location_td">
							<?
							echo create_drop_down("cbo_location_name", 90, "select id,location_name from lib_location where company_id=$company_id and is_deleted=0  and status_active=1 $location_credential_cond", "id,location_name", 1, "-- Select --", 0, "");
							?>
                        </td>
                        <td id="division_td" width="90">
							<?
							echo create_drop_down("cbo_division_name", 90, $blank_array, "", 1, "-- Select --");
							?>
                        </td>
                        <td width="70" id="department_td">
							<?
							echo create_drop_down("cbo_department_name", 90, $blank_array, "", 1, "-- Select --");
							?>
                        </td>
                        <td id="section_td" width="132">
							<?
							echo create_drop_down("cbo_section_name", 90, $blank_array, "", 1, "-- Select --", '');
							?>
                        </td>
                        <td id="sub_section_td" width="90" style="display:none;">
							<?
							echo create_drop_down("cbo_sub_section_name", 90, $blank_array, "", 1, "-- Select --");
							?>
                        </td>
                        <td><input type="text" name="cbo_delivery_point" id="cbo_delivery_point" style="width:90px"
                                   class="text_boxes"></td>
                        <td><input type="hidden" id="hidden_item_issue_id"/>
                            <input type="button" id="search_button" class="formbutton" value="Show" onClick="item_issue_requisition_popup()" style="width:100px;"/>
                        </td>
                    </tr>
                    </tbody>
					<tfoot>
	                <tr>
	                    <td align="center" height="25" valign="middle" colspan="11" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,283) 7%, rgb(194,220,255) 10%, rgb(136,170,283) 96%);">
	                    <? echo load_month_buttons(1);  ?>
	                    </td>
	                </tr>
	            </tfoot> 
                </table>
                <div style="width:100%; margin-top:10px;" id="search_div" align="center"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script type="text/javascript">
    	//$("#cbo_company_id").val(0);
		$('#cbo_location_name').val(0);
		$('#cbo_division_name').val(0);
		$('#cbo_department_name').val(0);
		$('#cbo_section_name').val(0);
		$('#cbo_sub_section_name').val(0);
	</script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>

	<?
	exit();

}

if ($action == "items_search_list_view") 
{
	$data = explode('**', $data);
	$delivery = $data[9];
	$indent_no = trim($data[10]);
	$cbo_year = trim($data[11]);
	//var_dump($data);die;
	if ($data[0] != 0) 
	{
		$company_id = " and a.company_id = $data[0]";
	} 
	else 
	{
		echo "Select Company";
		die;
	}
	
	$location_id=$division_id =$department_id =$section_id =$sub_section_id =$delivery_id =$ind_id ="";
	if ($data[4] != 0) $location_id = " and a.location_id = $data[4]"; 
	if ($data[5] != 0) $division_id = " and a.division_id = $data[5]";
	if ($data[6] != 0) $department_id = " and a.department_id = $data[6]";
	if ($data[7] != 0) $section_id = " and a.section_id = $data[7]";
	if ($data[8] != 0) $sub_section_id = " and a.sub_section_id = $data[8]";
	if ($data[9] != '') $delivery_id = " and a.delivery_point like '$delivery%'";
	if ($data[10] != '') $ind_id = " and a.itemissue_req_sys_id like '%$indent_no'";	
	
	//$date=change_date_format($data[1],'mm-dd-yyyy');
	//if($data[1]!=0){ $indent_date=" and indent_date = $data[1]";}else{ $indent_date=""; }
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$department = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$location = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$division = return_library_array("select id, division_name from lib_division", "id", "division_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$user_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");

	// $date = $data[1];
	$re_date = $data[3];
	$indent_date = $require_date = "";
	if ($data[1] != "" && $data[2] != "")
	{
		if ($db_type == 0) 
		{
			$indent_date = "and a.indent_date between '" . change_date_format($data[1], 'yyyy-mm-dd') . "' and '" . change_date_format($data[2], 'yyyy-mm-dd') . "'";
		} 
		else if ($db_type == 2) 
		{
			$indent_date = "and a.indent_date between '" . change_date_format($data[1], '', '', 1) . "' and '" . change_date_format($data[2], '', '', 1) . "'";
		}
	}

	if ($data[3] != "") 
	{
		if ($db_type == 0) 
		{
			$require_date = "and a.required_date ='" . change_date_format($re_date, 'yyyy-mm-dd') . "'";
		} 
		else if ($db_type == 2) 
		{
			$require_date = "and a.required_date ='" . change_date_format($re_date, '', '', 1) . "'";
		}
	}

	$year_field=$year_cond="";
	if ($db_type == 0) 
	{
		$year_field = "YEAR(a.insert_date) as year";
		$year_cond=" and year(a.insert_date)=".trim($cbo_year);
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(a.insert_date,'YYYY') as year";
		$year_cond=" and to_char(a.insert_date,'YYYY')=".trim($cbo_year);
	}

	
	$is_approved_arr=array(0=>'No', 1=>'Yes', 2=>'No', 3=>'Partial Approved');

	$sql = "select A.ID, A.ITEMISSUE_REQ_PREFIX, A.ITEMISSUE_REQ_PREFIX_NUM, A.ITEMISSUE_REQ_SYS_ID, A.INDENT_DATE, b.ID as ISS_REQ_DTLS_ID, C.ID AS PROD_ID, C.ITEM_ACCOUNT, C.ITEM_CODE, C.ORIGIN, C.SUB_GROUP_NAME, C.ITEM_CATEGORY_ID, C.ITEM_DESCRIPTION, C.BRAND_NAME, C.MODEL, C.ITEM_SIZE, C.ITEM_GROUP_ID, C.UNIT_OF_MEASURE, C.STATUS_ACTIVE, C.ITEM_NUMBER, C.ORDER_UOM, C.CURRENT_STOCK, C.RE_ORDER_LABEL, C.MAXIMUM_LABEL
	from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c 
	where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_purchaser_req=0 $company_id $indent_date $require_date $location_id $division_id $department_id $section_id $sub_section_id $delivery_id $ind_id $user_cond $year_cond 
	order by a.id desc";
	$sql_res=sql_select($sql);
	//echo  $sql;// die;
	?>
	<div style="width: 1200px;">
	<table width="1200" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" align="left">
        <thead>
			<tr>
				<th colspan="16"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
			<tr>
				<th width="30">SL</th>
				<th width="60">Indent No.</th>
				<th width="60">Indent Date</th>
				<th width="120">Item Category</th>
                <th width="60">Brand</th>
                <th width="70">Model</th>
                <th width="60">Origin</th>
                <th width="70">Sub Group Name</th>
                <th width="150">Item Description</th>
                <th width="60">Item Size</th>
                <th width="100">Item Group</th>
                <th width="60">Order UOM</th>
                <th width="80">Stock</th>
                <th width="60">ReOrder Level</th>
                <th width="60">Maximum Level</th>
                <th>Product ID</th>
			</tr>
        </thead>
     </table>
     <div style="width:1200px; overflow-y:scroll; max-height:250px">
     	<table width="1180" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
			<?
			$i = 1;$re_order_level=0;
            foreach($sql_res as $val)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; 
                else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value('<? echo $i.'_'.$val["PROD_ID"].'_'.$re_order_level.'___'.$val['ITEM_CATEGORY_ID'].'_'.$val['ISS_REQ_DTLS_ID']; ?>');">
                	<td width="30" align="center"><? echo $i; ?></td>
		            <td width="60" align="center"><? echo $val["ITEMISSUE_REQ_PREFIX_NUM"]; ?></td>
		            <td width="60"><? echo change_date_format($val["INDENT_DATE"]); ?></td>
		            <td width="120"><p><? echo $item_category[$val["ITEM_CATEGORY_ID"]]; ?></p></td>
                    <td width="60"><p><? echo $val["BRAND_NAME"]; ?></p></td>
                    <td width="70"><p><? echo $val["MODEL"]; ?></p></td>
                    <td width="60"><p><? echo $origin_lib[$val["ORIGIN"]]; ?></p></td>
                    <td width="70"><p><? echo $val["SUB_GROUP_NAME"]; ?></p></td>
                    <td width="150"><p><? echo $val["ITEM_DESCRIPTION"]; ?></p></td>
                    <td width="60" align="center"><p><? echo $val["ITEM_SIZE"]; ?></p></td>
                    <td width="100"><p><? echo $item_group_arr[$val["ITEM_GROUP_ID"]]; ?></p></td>
                    <td width="60" align="center"><p><? echo $unit_of_measurement[$val["UNIT_OF_MEASURE"]]; ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($val["CURRENT_STOCK"], 2); ?></p></td>
                    <td width="60" align="right"><p><? echo $val["RE_ORDER_LABEL"]; ?></p></td>
                    <td width="60" align="right"><p><? echo $val["MAXIMUM_LABEL"]; ?></p></td>
                    <td align="center"><p><? echo $val["PROD_ID"]; ?></p></td>
				</tr>
            	<?
				$i++;
            }
			?>
		</table>
    </div>
    <table width="1240" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
    </div>
    
    <?
    exit();	
}

//item group search------------------------------//
if($action=="item_sub_group_such_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

    	function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$company=str_replace("'","",$company);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$cbo_item_group=str_replace("'","",$cbo_item_group);
	$sub_group_prod_id=str_replace("'","",$sub_group_prod_id);
	$txt_item_sub_group=str_replace("'","",$txt_item_sub_group);
	$sub_group_no=str_replace("'","",$sub_group_no);
	$sql_cond="";
	if($cbo_item_group>0) $sql_cond=" and item_group_id=$cbo_item_group";

	if($db_type==0)
	{
		$sql="SELECT id, sub_group_code, sub_group_name from  product_details_master where  company_id=$company and item_category_id in($cbo_item_category_id) and status_active=1 and is_deleted=0 and (sub_group_code !='' or sub_group_name !='') $sql_cond";
	}
	else
	{
		$sql="SELECT id, sub_group_code, sub_group_name from  product_details_master where  company_id=$company and  item_category_id in($cbo_item_category_id) and status_active=1 and is_deleted=0 and (sub_group_code is not null or sub_group_name is not null ) $sql_cond";
	}

	//echo $sql; die;

	$arr=array();
	echo create_list_view("list_view", "Item Sub Group Code,Item Sub Group Name","120,120","300","300",0, $sql , "js_set_value", "id,sub_group_name", "", 1, "0", $arr, "sub_group_code,sub_group_name", "","setFilterGrid('list_view',-1)","0","",1);

	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
    <script language="javascript" type="text/javascript">
	var txt_item_sub_group_no='<? echo $sub_group_no;?>';
	var txt_item_sub_group_id='<? echo $sub_group_prod_id;?>';
	var txt_item_sub_group='<? echo $txt_item_sub_group;?>';
	//alert(style_id);
	if(txt_item_sub_group_no!="")
	{
		item_sub_group_no_arr=txt_item_sub_group_no.split(",");
		item_sub_group_id_arr=txt_item_sub_group_id.split(",");
		item_sub_group_arr=txt_item_sub_group.split(",");
		var item_sub_group="";
		for(var k=0;k<item_sub_group_no_arr.length; k++)
		{
			item_sub_group=item_sub_group_no_arr[k]+'_'+item_sub_group_id_arr[k]+'_'+item_sub_group_arr[k];
			js_set_value(item_sub_group);
		}
	}
	</script>

    <?
	exit();
}

if ($action=="load_php_popup_to_form")
{
	$explode_data = explode("**",$data);
	$data=$explode_data[0];
	$table_row=$explode_data[1];
	$store_id=$explode_data[2];
	$re_order_lebel=$explode_data[3];
    $update_id=$explode_data[4];
	$req_basis=$explode_data[5];
	$req_dtls_id=$explode_data[6];
	//echo  $data.test;die;
    if($data!="")
	{
		if($req_basis==7)
		{
			$data="";
			$sql_issue_req_dtls=sql_select("select PRODUCT_ID from inv_itemissue_requisition_dtls where id in($req_dtls_id)");
			foreach($sql_issue_req_dtls as $val)
			{
				$data.=$val["PRODUCT_ID"].",";
			}
			$data=chop($data,",");
		}
		else
		{
			$prod_sql="select ITEM_CATEGORY_ID from product_details_master where id in($data)";
			$prod_sql_result=sql_select($prod_sql);
			$is_dyes_category=0;
			foreach($prod_sql_result as $val)
			{
				if($val["ITEM_CATEGORY_ID"]==5 || $val["ITEM_CATEGORY_ID"]==6 || $val["ITEM_CATEGORY_ID"]==7 || $val["ITEM_CATEGORY_ID"]==23) 
				{
					$is_dyes_category=1;
				}
			}
		}
		
        $lastRcvRate = sql_select("select z.cons_rate,z.prod_id, z.id
        from inv_transaction z where z.transaction_type in (1,4,5) and z.prod_id in ($data) and z.status_active = 1 and z.is_deleted = 0
        order by id desc");
        $lastRcvRateArr = array(); $prodIdChk = array();
        foreach($lastRcvRate as $row)
        {
            if($prodIdChk[$row[csf("prod_id")]] == "")
            {
                $prodIdChk[$row[csf("prod_id")]] = $row[csf("prod_id")];
                $lastRcvRateArr[$row[csf("prod_id")]] = $row[csf("cons_rate")];
            }

        }
		
		if($req_basis==7)
		{
			$trans_sql=sql_select("select c.prod_id, sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as balance_stock from inv_transaction c where c.prod_id in($data) and c.store_id=$store_id and c.status_active=1 and c.is_deleted=0
			group by c.prod_id");
			$trans_stock_data=array();
			foreach($trans_sql as $val)
			{
				$trans_stock_data[$val[csf("prod_id")]]=$val[csf("balance_stock")];
			}
			
			$req_cond=" and d.id in($req_dtls_id)";
			// $sql_req="SELECT a.id as id, a.brand_name, a.model, a.origin, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.status_active, b.item_name, a.order_uom, a.unit_of_measure as cons_uom, a.current_stock, a.re_order_label, a.maximum_label, a.current_stock as balance_stock, d.id as req_dtls_id, d.req_qty
			// from lib_item_group b, inv_itemissue_requisition_dtls d, product_details_master a
			// where a.item_group_id=b.id and d.PRODUCT_ID=a.id and a.status_active=1 and d.status_active=1 $req_cond";

			$sql_req="SELECT a.id as id, a.brand_name, a.model, a.origin, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.status_active, b.item_name, a.order_uom, a.unit_of_measure as cons_uom, a.current_stock, a.re_order_label, a.maximum_label, a.current_stock as balance_stock,a.item_number, d.id as req_dtls_id, d.req_qty,e.itemissue_req_prefix_num,e.id as mst_id, d.remarks
			from lib_item_group b, inv_itemissue_requisition_dtls d, inv_item_issue_requisition_mst e, product_details_master a
			where a.item_group_id=b.id and d.PRODUCT_ID=a.id and d.mst_id = e.id and a.status_active=1 and d.status_active=1 $req_cond";
		}
		else
		{
			$req_cond="";
			//if($prod_item_category!=5 && $prod_item_category!=6 && $prod_item_category!=7 && $prod_item_category!=23 && $req_basis!=7)
			if($is_dyes_category!=1)
			{
				$req_cond=" and a.id in($data)";
			}
			else
			{
				$prod_sql="select COMPANY_ID, ITEM_GROUP_ID, ITEM_DESCRIPTION, SUB_GROUP_NAME, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE, ITEM_CATEGORY_ID from product_details_master where id in($data)";
				$prod_sql_result=sql_select($prod_sql);
				$prod_item_group_arr=$prod_item_category_arr=array();
				$prod_item_description=$prod_sub_group_name=$prod_item_size=$prod_model=$prod_item_number=$prod_item_code="";
				foreach($prod_sql_result as $val)
				{
					$prod_com_id=$val["COMPANY_ID"];
					$prod_item_group_arr[$val["ITEM_GROUP_ID"]]=$val["ITEM_GROUP_ID"];
					$prod_item_category_arr[$val["ITEM_CATEGORY_ID"]]=$val["ITEM_CATEGORY_ID"];
					if(trim($val["ITEM_DESCRIPTION"])!="") $prod_item_description.="'".trim($val["ITEM_DESCRIPTION"])."',";
					if(trim($val["SUB_GROUP_NAME"])!="")$prod_sub_group_name.="'".trim($val["SUB_GROUP_NAME"])."',";
					if(trim($val["ITEM_SIZE"])!="")$prod_item_size.="'".trim($val["ITEM_SIZE"])."',";
					if(trim($val["MODEL"])!="")$prod_model.="'".trim($val["MODEL"])."',";
					if(trim($val["ITEM_NUMBER"])!="")$prod_item_number.="'".trim($val["ITEM_NUMBER"])."',";
					if(trim($val["ITEM_CODE"])!="")$prod_item_code.="'".trim($val["ITEM_CODE"])."',";
				}
				
				$prod_item_description=chop($prod_item_description,",");
				$prod_sub_group_name=chop($prod_sub_group_name,",");
				$prod_item_size=chop($prod_item_size,",");
				$prod_model=chop($prod_model,",");
				$prod_item_number=chop($prod_item_number,",");
				$prod_item_code=chop($prod_item_code,",");
				
				
				if($prod_com_id>0) $req_cond.=" and a.COMPANY_ID=$prod_com_id";
				if(count($prod_item_group_arr)>0) $req_cond.=" and a.ITEM_GROUP_ID in(".implode(",",$prod_item_group_arr).")";
				if(count($prod_item_category_arr)>0) $req_cond.=" and a.item_category_id in(".implode(",",$prod_item_category_arr).")";
				if($prod_item_description != "") $req_cond.=" and trim(a.ITEM_DESCRIPTION) in($prod_item_description)";
				if($prod_sub_group_name != "") $req_cond.=" and trim(a.SUB_GROUP_NAME) in($prod_sub_group_name)";
				if($prod_item_size != "") $req_cond.=" and trim(a.ITEM_SIZE) in($prod_item_size)"; else $req_cond.=" and a.ITEM_SIZE is null";
				if($prod_model != "") $req_cond.=" and trim(a.MODEL) in($prod_model)";
				if($prod_item_number != "") $req_cond.=" and trim(a.ITEM_NUMBER) in($prod_item_number)";
				if($prod_item_code != "") $req_cond.=" and trim(a.ITEM_CODE) in($prod_item_code)";
			}
			$sql_req="select max(a.id) as id, a.brand_name, a.model, a.origin, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.status_active, a.item_number, b.item_name, a.order_uom, a.unit_of_measure as cons_uom, sum(a.current_stock) as current_stock, max(a.re_order_label) as re_order_label, max(a.maximum_label) as maximum_label, sum((case when c.transaction_type in(1,4,5) and c.status_active=1 and c.is_deleted=0 then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) and c.status_active=1 and c.is_deleted=0 then c.cons_quantity else 0 end)) as balance_stock, 0 as req_dtls_id, 0 as req_qty
			from lib_item_group b, product_details_master a left join inv_transaction c on a.id=c.prod_id and c.store_id=$store_id 
			where a.item_group_id=b.id and a.status_active=1 $req_cond
			group by a.brand_name,a.model,a.origin, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.status_active, a.item_number, b.item_name, a.order_uom, b.trim_uom";
			//echo $sql_req;
		}
		
		
		//echo $sql_req;//die;
		$nameArray=sql_select($sql_req);
        $sql_select_master_data = sql_select("select company_id, cbo_currency, requisition_date from inv_purchase_requisition_mst where status_active = 1 and is_deleted = 0 and id = $update_id");
        $company_id = $sql_select_master_data[0][csf('company_id')];
        $currency_id = $sql_select_master_data[0][csf('cbo_currency')];
        $requ_date = change_date_format($sql_select_master_data[0][csf('requisition_date')]);

        $date = new DateTime($requ_date);
        $date->modify('first day of this month');
        $firstday= $date->format('d-m-Y');
        $date->modify('last day of this month');
        $lastday= $date->format('d-m-Y');
        $current_month = date("m", strtotime($requ_date));
        $sql_get_current_mon_budget = sql_select("select a.id, a.currency_id, b.category_id, b.budget_amount from lib_category_budget_mst a, lib_category_budget_dtls b  where a.id = b.mst_id and a.company_id = $company_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and (a.APPLYING_DATE_FROM <= to_date($current_month, 'MM') and a.APPLYING_DATE_to >= to_date($current_month, 'MM')) and b.category_id = $prod_item_category");
        $cat_wise_budget_arr = array(); $prev_cat_wise_budget = array();
        if(count($sql_get_current_mon_budget) > 0) 
		{
            foreach ($sql_get_current_mon_budget as $data) 
			{
                $conversion_date = change_date_format($requ_date, "d-M-y", "-", 1);
                $currency_rate = set_conversion_rate($currency_id, $conversion_date, $company_id);
                $cat_wise_budget_arr[$data[csf('category_id')]] = $data[csf('budget_amount')] * $currency_rate; //convert bdt
            }
            $sql_get_cat_amount = sql_select("select a.CBO_CURRENCY, b.ITEM_CATEGORY, sum(b.AMOUNT) as AMOUNT from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id = b.mst_id and a.COMPANY_ID = $company_id and a.status_active = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0
                and a.REQUISITION_DATE BETWEEN to_date('$firstday', 'DD-MM-YY') and to_date('$lastday', 'DD-MM-YY') and b.item_category = $prod_item_category group by a.CBO_CURRENCY, b.ITEM_CATEGORY");
            foreach ($sql_get_cat_amount as $data) {
                $conversion_date = change_date_format($requ_date, "d-M-y", "-", 1);
                if ($data[csf('cbo_currency')] == 0 || $data[csf('cbo_currency')] == '') {
                    $data[csf('cbo_currency')] = 1;
                }
                $currency_rate1 = set_conversion_rate($data[csf('cbo_currency')], $conversion_date, $company_id);
                $prev_cat_wise_budget[$data[csf('item_category')]] = $data['AMOUNT'] * $currency_rate1; //convert bdt
            }
        }
        if(isset($cat_wise_budget_arr[$prod_item_category])){
            $toolTipMsg = "Budgeted Amount: ".$cat_wise_budget_arr[$prod_item_category]." BDT., Already Requisition Created Amount: ".(isset($prev_cat_wise_budget[$prod_item_category]) ? $prev_cat_wise_budget[$prod_item_category] : 0)." BDT.";
        }else{
            $toolTipMsg = '';
        }
		
		foreach ($nameArray as $inf)
		{
			$table_row++;
			if($req_basis==7) $inf[csf("balance_stock")]=$trans_stock_data[$inf[csf("id")]];
			$crnt_req=$inf[csf("maximum_label")]-$inf[csf("balance_stock")];
			$crnt_req_amt=$crnt_req*$lastRcvRateArr[$inf[csf("id")]];
			?>
			<tr class="general" id="tr_<? echo $table_row; ?>" >
                <td width="90">
				<?
                    echo create_drop_down( "cboItemCategory_".$table_row, 90,$item_category,"", 1, "-- Select --", $inf[csf("item_category_id")], "",1,"","","","1,2,3,12,13,14,24,25");
                 ?>
                </td>
				<td width="85">
				 <input type="text" name="txtitemgroupid_<? echo $table_row; ?>" id="txtitemgroupid_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_name")];?>" style="width:72px;"/>
				<input type="hidden" name="hiddenitemgroupid_<? echo $table_row; ?>" id="hiddenitemgroupid_<? echo $table_row; ?>" value="<? echo $inf[csf("item_group_id")];?>"/>
                 <input type="hidden" name="hiddenid_<? echo $table_row; ?>" id="hiddenid_<? echo $table_row; ?>" value="" />
                 <input type="hidden" name="hiddenissreqdtlsid_<? echo $table_row; ?>" id="hiddenissreqdtlsid_<? echo $table_row; ?>" value="<? echo $inf[csf("req_dtls_id")];?>" />
				</td>
                <td width="70">
				<input type="text" name="subgroup_<? echo $table_row; ?>" id="subgroup_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("sub_group_name")];?>" style="width:60px;" maxlength="200" readonly />
				</td>
				<td width="130" id="group_td">
				<input type="text" name="itemdescription_<? echo $table_row; ?>" id="itemdescription_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_description")];?>" style="width:115px;" maxlength="200" readonly />
				<input type="hidden" name="item_<? echo $table_row; ?>" id="item_<? echo $table_row; ?>" value="<? echo $inf[csf("id")];?>" />
				</td>
                <td width="70">				
				<input type="text" name="itemsize_<? echo $table_row; ?>" id="itemsize_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_size")];?>" style="width:55px;" maxlength="200" />
				</td>
				<td width="100">				
				<input type="text" name="itemnumber_<? echo $table_row; ?>" id="itemnumber_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_number")];?>" style="width:80px;" maxlength="200" readonly/>
				</td>
                <td style="display:none;">
                <?
					echo create_drop_down( "txtreqfor_".$table_row, 80, $use_for,'', 1, '-- Select --',0,'',0,'');
				?>
                </td>
				<td width="65">
					<?
					echo create_drop_down( "txtuom_".$table_row, 60, $unit_of_measurement,'', 1, '', $inf[csf("order_uom")],'',1,'');
					?>
				</td>
				<td width="65">
				<input type="text" name="quantity_<? echo $table_row; ?>" id="quantity_<? echo $table_row; ?>" class="text_boxes_numeric" autocomplete="off" style="width:50px;" onKeyUp="calculate_val()" value="<? if($re_order_lebel==1 && $req_basis!=7) echo number_format($crnt_req,2,'.',''); else echo $inf[csf("req_qty")]; ?>"/>
				</td>
				<td width="65">
				<input type="text" name="rate_<? echo $table_row; ?>" id="rate_<? echo $table_row; ?>"  class="text_boxes_numeric" autocomplete="off" value="<? echo number_format($lastRcvRateArr[$inf[csf("id")]],2,'.','');?>" style="width:50px;" onKeyUp="calculate_val()"/>
				</td>
				<td width="65">
				<input type="text" title="<?=$toolTipMsg?>" name="amount_<? echo $table_row; ?>" id="amount_<? echo $table_row; ?>" class="text_boxes_numeric" autocomplete="off" value="<? if($re_order_lebel==1 && $req_basis!=7) echo number_format($crnt_req_amt,2,'.',''); else echo $inf[csf("req_qty")]*$lastRcvRateArr[$inf[csf("id")]]; ?>" style="width:50px;" readonly/>
				</td>
				<td width="65">
				<input type="text" name="stock_<? echo $table_row; ?>" id="stock_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? echo $inf[csf("balance_stock")];?>" style="width:50px;" maxlength="200" readonly />
				</td>
				<td width="65">
				<input type="text" name="reorderlable_<? echo $table_row; ?>" id="reorderlable_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? echo $inf[csf("re_order_label")];?>" style="width:50px;" maxlength="200" readonly />
				</td>
				<td width="80">
                    <input type="text" name="txtvehicle_<? echo $table_row; ?>" id="txtvehicle_<? echo $table_row; ?>" class="text_boxes" value="" style="width:70px;" />
                </td>
                <td width="90">
                	<input type="text" name="txtremarks_<? echo $table_row; ?>" id="txtremarks_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("remarks")];?>" style="width:80px;" />
				</td>
				<td width="65">
				<?
				echo create_drop_down( "cbostatus_".$table_row, 60, $row_status,'', '', '',$inf[csf("status_active")],'',0,'');
				?>
				</td>
				<td style="display:none;">
				<input type="text" name="txtused_<? echo $table_row; ?>" id="txtused_<? echo $table_row; ?>" class="text_boxes" value="" style="width:60px;" maxlength="200" />
				</td>
				<td width="70">
				<input type="text" name="txtbrand_<? echo $table_row; ?>" id="txtbrand_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("brand_name")];?>" style="width:55px;" maxlength="200" />
				</td>
				<td width="70">
				<input type="text" name="txtmodelname_<? echo $table_row; ?>" id="txtmodelname_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("model")];?>" style="width:55px;" maxlength="200" />
				</td>
				<td width="70">
                     <? //new
                       echo create_drop_down( "cboOrigin_".$table_row, 65, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', $inf[csf("origin")] );  ?>
				</td>
                <td><input type="text" name="txtdatedelivery_<? echo $table_row; ?>" style="width:50px"  id="txtdatedelivery_<? echo $table_row; ?>" class="datepicker" value="" /></td>

				<td><input type="text" name="txtIndent_<? echo $table_row; ?>" disabled style="width:50px"  id="txtIndent_<? echo $table_row; ?>" class="text_boxes"  value="<?echo $inf[csf("itemissue_req_prefix_num")]?>" />
				<input type="hidden" name = "mstId_<? echo $table_row; ?>" id="mstId_<? echo $table_row; ?>" value="<? echo $inf[csf("mst_id")]?>">
				</td>
				<td width="70">
					<?								    
					 echo create_drop_down( "cboServiceFor_".$table_row, 90, $service_for_arr, "", 1, "-- Select --",$selected, 0, 1, "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$table_row );						   	
					?>
				</td>
			</tr>
			<?
		}
	}
	exit();
}


if ($action=="purchase_requisition_list_view_dtls")
{
	$arr=array (6=>$use_for, 7=>$unit_of_measurement,15=>$row_status);
	$sql="select a.item_account, a.item_code, a.item_description, a.item_size, a.item_group_id, a.sub_group_name, b.required_for, a.order_uom as unit_of_measure, a.re_order_label, b.id, b.quantity, b.rate, b.amount, b.stock, b.status_active, b.remarks, c.item_name, b.cons_uom, b.delivery_date, b.item_category ,b.itemissue_req_prefix_num 
	from product_details_master a, inv_purchase_requisition_dtls b, lib_item_group c 
	where b.is_deleted=0 and b.mst_id='$data' and a.id=b.product_id and a.item_group_id=c.id and a.status_active in(1,3)
	union all 
	select null as item_account, null as item_code, b.service_details as item_description, null as item_size, 0 as item_group_id, null as sub_group_name, b.required_for, b.cons_uom as unit_of_measure, 0 as re_order_label, b.id, b.quantity, b.rate, b.amount, b.stock, b.status_active, b.remarks, null as item_name, b.cons_uom, b.delivery_date, b.item_category,b.itemissue_req_prefix_num 
	from inv_purchase_requisition_dtls b
	where b.is_deleted=0 and b.mst_id='$data' and b.item_category=114 and b.product_id=0";
	//echo $sql;

	echo create_list_view ("list_view","Item Account,Item Code,Item Description,Item Size,Item Group,Item Sub Group,Required For,UOM,Quantity,Rate,Amount,Stock,Re-Order Level,Remarks,Delivery Date,Item Issue Req/Dem No,Status", "110,80,130,110,110,110,70,70,70,70,70,70,70,90,70,70,70","1490","300",0, $sql, "get_php_form_data", "id,item_category", "'order_details_form_data'", 1, "0,0,0,0,0,0,required_for,unit_of_measure,0,0,0,0,0,0,0,status_active", $arr , "item_account,item_code,item_description,item_size,item_name,sub_group_name,required_for,unit_of_measure,quantity,rate,amount,stock,re_order_label,remarks,delivery_date,itemissue_req_prefix_num,status_active", "requires/purchase_requisition_v2_controller", '','0,0,0,0,0,0,0,0,4,2,2,2,2,0,3,0','',0 );
	exit();
}




if ($action=="order_details_form_data")
{

	$data_ref=explode("_",$data);
	$dtls_id=$data_ref[0];
	$dtls_category=$data_ref[1];
	if($dtls_category==114)
	{
		$nameSql="select a.id, a.brand_name, a.origin, a.model, a.required_for, a.quantity, a.rate, a.amount, a.stock, a.remarks, a.status_active, a.product_id, a.vehicle_no, a.item_category as item_category_id, null as item_account, a.service_details as item_description, null as item_size, 0 as item_group_id, null as sub_group_name, a.cons_uom as unit_of_measure, 0 as re_order_label, null as item_name, a.delivery_date, a.used_for,a.itemissue_req_prefix_num , a.servicesfor_lib_id
		from inv_purchase_requisition_dtls a
		where a.id=$dtls_id and a.is_deleted=0";
	}
	else
	{
		$nameSql="select a.id, a.brand_name, a.origin, a.model, a.required_for, a.quantity, a.rate, a.amount, a.stock, a.remarks, a.status_active, a.product_id, a.vehicle_no, b.item_category_id, b.item_account, b.item_description, b.item_size, b.item_group_id,b.sub_group_name, b.order_uom as unit_of_measure, b.re_order_label,b.item_number, c.item_name, a.delivery_date, a.used_for,a.itemissue_req_prefix_num , a.servicesfor_lib_id
		from inv_purchase_requisition_dtls a, product_details_master b, lib_item_group c
		where a.id=$dtls_id and a.is_deleted=0 and a.product_id=b.id and b.item_group_id=c.id";
	}
	//echo $nameSql;//die;
	$nameArray=sql_select($nameSql);
	/*------------ADDITIONAL CODE----------------*/
	

	foreach ($nameArray as $row)
	{
		echo "$('#itemaccount_1').val('".$row[csf('item_account')]."');\n";
		echo "$('#itemaccount_1').attr('disabled',true);\n";
		echo "$('#itemdescription_1').val('".$row[csf('item_description')]."');\n";
		if($dtls_category==114)
		{
			echo "$('#itemdescription_1').removeAttr('readonly');\n";
		}
		else
		{
			echo "$('#itemdescription_1').attr('readonly',true);\n";
		}
		echo "$('#item_1').val('".$row[csf('product_id')]."');\n";
		echo "$('#cboItemCategory_1').val('".$row[csf('item_category_id')]."');\n";
		echo "$('#itemsize_1').val('".$row[csf('item_size')]."');\n";
		echo "$('#itemnumber_1').val('".$row[csf('item_number')]."');\n";
		echo "$('#txtitemgroupid_1').val('".$row[csf('item_name')]."');\n";
		echo "$('#hiddenitemgroupid_1').val('".$row[csf('item_group_id')]."');\n";
		echo "$('#sub_group_1').val('".$row[csf('sub_group_name')]."');\n";
		echo "$('#txtreqfor_1').val('".$row[csf('required_for')]."');\n";
		echo "$('#txtuom_1').val('".$row[csf('unit_of_measure')]."');\n";
		echo "$('#txtuom_1').attr('disabled',true);\n";
		//echo "$('#hiddentxtuom_1').val('".$row[csf('unit_of_measure')]."');\n";
		// echo "$('#quantity_1').val('".$row[csf('quantity')]."');\n";
		echo "$('#quantity_1').val('".number_format($row[csf('quantity')],3,'.','')."');\n";
		echo "$('#rate_1').val('".number_format($row[csf('rate')],2, '.', '')."');\n";
		echo "$('#amount_1').val('".$row[csf('amount')]."');\n";
		echo "$('#txtremarks_1').val('".$row[csf('remarks')]."');\n";
		echo "$('#stock_1').val('".$row[csf('stock')]."');\n";
		echo "$('#reorderlable_1').val('".$row[csf('re_order_label')]."');\n";
		echo "$('#txtvehicle_1').val('".$row[csf('vehicle_no')]."');\n";
		echo "$('#cbostatus_1').val('".$row[csf('status_active')]."');\n";
		echo "$('#txt_used_1').val('".$row[csf('used_for')]."');\n";
		echo "$('#txtbrand_1').val('".$row[csf('brand_name')]."');\n";
		echo "$('#txtmodelname_1').val('".$row[csf('model')]."');\n";
		echo "$('#cboOrigin_1').val('".$row[csf("origin")]."');\n";
		echo "$('#txtIndent_1').val('".$row[csf("itemissue_req_prefix_num")]."');\n";
		echo "$('#txtdatedelivery_1').val('".change_date_format($row[csf("delivery_date")])."');\n";
 		echo "$('#hiddenid_1').val('".$row[csf('id')]."');\n";
 		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_purchase_requisition_dtls',2);\n";
		 echo "$('#cboServiceFor_1').attr('disabled',false);\n";
		 echo "$('#cboServiceFor_1').val('".$service_for_arr[$row[csf("servicesfor_lib_id")]]."');\n";
 	}
	exit();
}

function getData($ref_id,$user_id='')
{
	$sql = "SELECT A.ID,
					A.REQU_NO,
					B.COMPANY_NAME,
					C.STORE_NAME,
					A.REQUISITION_DATE,
					D.USER_FULL_NAME,
					A.DELIVERY_DATE,
					E.LOCATION_NAME,
					A.REQU_PREFIX_NUM,
					SUM (X.AMOUNT)                  AS AMOUNT,
					LISTAGG (Y.SHORT_NAME, ',')     AS CATEGORY,
					A.PRIORITY_ID
			FROM INV_PURCHASE_REQUISITION_MST A
					LEFT JOIN INV_PURCHASE_REQUISITION_DTLS X
						ON A.ID = X.MST_ID AND X.IS_DELETED = 0
					LEFT JOIN LIB_ITEM_CATEGORY_LIST Y ON X.ITEM_CATEGORY = Y.CATEGORY_ID
					LEFT JOIN USER_PASSWD D ON A.INSERTED_BY = D.ID
					LEFT JOIN LIB_COMPANY B ON A.COMPANY_ID = B.ID
					LEFT JOIN LIB_STORE_LOCATION C ON A.STORE_NAME = C.ID
					LEFT JOIN LIB_LOCATION E ON A.LOCATION_ID = E.ID
			WHERE A.ID =  $ref_id
			GROUP BY A.ID,
					A.REQU_NO,
					B.COMPANY_NAME,
					C.STORE_NAME,
					A.REQUISITION_DATE,
					D.USER_FULL_NAME,
					A.DELIVERY_DATE,
					E.LOCATION_NAME,
					A.REQU_PREFIX_NUM,
					A.PRIORITY_ID
	";

	$result = sql_select($sql);

	$data_arr = array();

	$menu_id=return_field_value("page_id as menu_id","ELECTRONIC_APPROVAL_SETUP","entry_form=1","menu_id");
	if(!empty($user_id))
	{
	  $USER_FULL_NAME = return_field_value("USER_FULL_NAME","USER_PASSWD","id=$user_id","USER_FULL_NAME");
	}

	foreach ($result as $row)
	{
		if(!empty( $USER_FULL_NAME))  $USER_FULL_NAME ="\nForwarded By : ".$USER_FULL_NAME;
		else $USER_FULL_NAME ="\nInserted By : ".$row['USER_FULL_NAME'];
		$desc = "Company: ".$row['COMPANY_NAME'].", Loc: ".$row['LOCATION_NAME']."\nReq. No: ".$row['REQU_PREFIX_NUM'].", Req. date: ".date('d/m/Y',strtotime($row['REQUISITION_DATE']))."\nPriority: ".$priority_array[$row['PRIORITY_ID']].", Store : ".$row['STORE_NAME']."\nCategory: ".implode(",",array_unique(explode(",",$row['CATEGORY'])))."\nReq. For : ".$row['REQ_FOR']."\nReq. Value: ".number_format($row['AMOUNT'],2,".","").$USER_FULL_NAME;
		$data_arr = array(
			'ID' => $row['ID'],
			'DATE' => date('d/m/Y',strtotime($row['REQUISITION_DATE'])),
			'DELIVERY_DATE' => date('d/m/Y',strtotime($row['DELIVERY_DATE'])),
			'COMPANY' => $row['COMPANY_NAME'],
			'BUYER' => '',
			'SYS_NUMBER' => $row['REQU_NO'],
			'SYS_DEF' => '',
			'DESC' => $desc,
			'MENU_ID' => $menu_id
		);
	}
	
	return $data_arr;
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here=======================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if($db_type==0)
		{
			$new_requ_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '','RQSN', date("Y",time()), 5, "select requ_no_prefix,requ_prefix_num from inv_purchase_requisition_mst where company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." and entry_form=69 order by id desc ", "requ_no_prefix", "requ_prefix_num" ));
		}
	    else if($db_type==2)
		{
			$new_requ_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '','RQSN', date("Y",time()), 5, "select requ_no_prefix,requ_prefix_num from inv_purchase_requisition_mst where company_id=$cbo_company_name and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." and entry_form=69 order by id desc ", "requ_no_prefix", "requ_prefix_num" ));
		}
		$id=return_next_id("id","inv_purchase_requisition_mst",1);
		$field_array="id,entry_form,requ_no,requ_no_prefix,requ_prefix_num,company_id,location_id,division_id,department_id,section_id,requisition_date,store_name,pay_mode,source,cbo_currency,delivery_date,remarks,reference,manual_req,ready_to_approve,req_by,iso_no,priority_id,requisition_id,tenor,justification_value,inserted_by,insert_date,status_active,is_deleted,req_version,req_basis";
		$data_array="(".$id.",69,'".$new_requ_no[0]."','".$new_requ_no[1]."',".$new_requ_no[2].",".$cbo_company_name.",".$cbo_location_name.",".$cbo_division_name.",".$cbo_department_name.",".$cbo_section_name.",".$txt_date_from.",".$cbo_store_name.",".$cbo_pay_mode.",".$cbo_source.",".$cbo_currency_name.",".$txt_date_delivery.",".$txt_remark.",".$txt_reference.",".$txt_manual_req.",".$cbo_ready_to_approved.",".$txt_req_by.",".$txt_iso_no.",".$cbo_priority_id.",".$cbo_requisition_id.",".$txt_tenor.",".$justification_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,2,".$cbo_req_basis.")";

		//echo "10**insert into inv_purchase_requisition_mst (".$field_array.") values ".$data_array;die;

		$rID=sql_insert("inv_purchase_requisition_mst",$field_array,$data_array,0);

		if($db_type==0)
		{
			if($rID){
			 	mysql_query("COMMIT");
			  	echo "0**".str_replace("'", '', $new_requ_no[0])."**".$id;
			}
			else{
			  	mysql_query("ROLLBACK");
			  	echo "10**".$id;
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
			 	oci_commit($con);
			 	echo "0**".str_replace("'", '', $new_requ_no[0])."**".$id;
			}
			else
			{
				oci_rollback($con);
			 	echo "10**".$id;
			}
		}

		check_table_status( $_SESSION['menu_id'],0);
		// if($db_type==2) {oci_commit($con);}

		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here==================================================================
	{
		$con = connect();
		if($db_type==0)
	  	{
			mysql_query("BEGIN");
	  	}
		/*###### stop not eligible field from update operation start #########*/
		//company_id*location_id*store_name*pay_mode*cbo_currency*
		//".$cbo_company_name."*".$cbo_location_name."*".$cbo_store_name."*".$cbo_pay_mode."*".$cbo_currency_name."*
		/*###### stop not eligible field from update operation end  pay_mode $cbo_pay_mode #########*/
		$prev_req_basis=return_field_value("req_basis as req_basis","inv_purchase_requisition_mst","id=$update_id","req_basis");
		if(str_replace("'","",$cbo_req_basis)!=$prev_req_basis)
		{
			echo "11**Multiple Basis Not Allow In Same Requistion";oci_rollback($con);disconnect($con);die;
		}
		
		$prev_pay_mode=return_field_value("pay_mode as pay_mode","inv_purchase_requisition_mst","id=$update_id","pay_mode");
		$mrr_wo_check="";
		if($prev_pay_mode==4)
		{
			$mrr_wo_check=return_field_value("a.id as id","inv_receive_master a, inv_transaction b"," a.id=b.mst_id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form = 20 and a.receive_basis = 7 and a.booking_id=$update_id","id");
		}
		else
		{
			$mrr_wo_check=return_field_value("a.id as id","wo_non_order_info_mst a, wo_non_order_info_dtls b, inv_purchase_requisition_dtls c","a.id=b.mst_id and b.requisition_dtls_id=c.id and a.entry_form in(145,146,147) and a.wo_basis_id=1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.mst_id=$update_id","id");
		}

		if($mrr_wo_check !="")
		{
			$field_array="division_id*department_id*section_id*requisition_date*source*delivery_date*remarks*reference*manual_req*ready_to_approve*req_by*iso_no*priority_id*requisition_id*tenor*justification_value*cbo_currency*updated_by*update_date";

	  		$data_array="".$cbo_division_name."*".$cbo_department_name."*".$cbo_section_name."*".$txt_date_from."*".$cbo_source."*".$txt_date_delivery."*".$txt_remark."*".$txt_reference."*".$txt_manual_req."*".$cbo_ready_to_approved."*".$txt_req_by."*".$txt_iso_no."*".$cbo_priority_id."*".$cbo_requisition_id."*".$txt_tenor."*".$justification_value."*".$cbo_currency_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		else
		{
			$field_array="pay_mode*division_id*department_id*section_id*requisition_date*source*delivery_date*remarks*reference*manual_req*ready_to_approve*req_by*iso_no*priority_id*requisition_id*tenor*justification_value*cbo_currency*updated_by*update_date";

	  		$data_array="".$cbo_pay_mode."*".$cbo_division_name."*".$cbo_department_name."*".$cbo_section_name."*".$txt_date_from."*".$cbo_source."*".$txt_date_delivery."*".$txt_remark."*".$txt_reference."*".$txt_manual_req."*".$cbo_ready_to_approved."*".$txt_req_by."*".$txt_iso_no."*".$cbo_priority_id."*".$cbo_requisition_id."*".$txt_tenor."*".$justification_value."*".$cbo_currency_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}

	  	$rID=sql_update("inv_purchase_requisition_mst",$field_array,$data_array,"id",$update_id,1);

		if($db_type==0)
		  {
			  if($rID)
			  {
				  mysql_query("COMMIT");
				  echo "1**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
			  else
			  {
				  mysql_query("ROLLBACK");
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		 else if($db_type==2)
		  {
			  if($rID)
			  {
				 oci_commit($con);
				 if($_SESSION['app_notification'] == 1){
					$not_res = '';
					$cbo_ready_to_approved = str_replace("'","",$cbo_ready_to_approved);

					$category_mixing_variable = return_field_value("allocation","variable_settings_inventory","company_name=$cbo_company_name and variable_list=44 and status_active=1 and is_deleted=0 order by id desc ","allocation");

					$cntSql=sql_select("SELECT COUNT(ID),LISTAGG(ITEM_CATEGORY,',') as ITEM_CATEGORY FROM inv_purchase_requisition_dtls where is_deleted = 0 and mst_id =  ".$update_id);
					$approval_data=getData($update_id); 
					$approval_desc = "";
					if(!empty($approval_data['DESC']))
					{
						$approval_desc = $approval_data['DESC'];
					}
					if($cbo_ready_to_approved == 1 && count($cntSql) > 0)
					{
						$categ = "";
						$categ = implode(",",array_filter(array_unique(explode(",",$cntSql[0]['ITEM_CATEGORY']))));
						$store_id = return_field_value("STORE_NAME","INV_PURCHASE_REQUISITION_MST","id=$update_id ","STORE_NAME");
						if(!empty($categ) && $category_mixing_variable == 2)
						{
							$approval_parameter = array('DEPARTMENT'=>str_replace("'","",$cbo_department_name),'LOCATION'=>str_replace("'","",$cbo_location_name),'STORE_ID'=>$store_id,'ITEM_CATEGORY'=>$categ,'ITEM_CATEGORY'=>$categ,'approval_desc'=>$approval_desc,'approval_data'=>$approval_data);
						}
						else
						{
							$approval_parameter = array('DEPARTMENT'=>str_replace("'","",$cbo_department_name),'LOCATION'=>str_replace("'","",$cbo_location_name),'STORE_ID'=>$store_id,'ITEM_CATEGORY'=>'','approval_desc'=>$approval_desc,'approval_data'=>$approval_data);
						}

						// print_r($approval_parameter);die;
						
						$notification = new Notifications();
						$not_res = $notification->notificationEngine($update_id,$cbo_company_name,1,$approval_parameter);
					}
					else
					{
						$exist=sql_select("SELECT * FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=1 AND REF_ID IN ($update_id) ");
						if(count($exist) > 0 )
						{
							
							$notification = new Notifications();
							$appr_data = array("USER_ID"=>'',"SEQUENCE_NO" =>'',"COMPANY_ID"=> '','NOTIFICATION_TYPE'=>0,'approval_desc'=>'','approval_data'=>$approval_data);
							$not_res=$notification->pushAll($update_id,1,$appr_data);
							if($not_res == 1)
							{
								disconnect($con);
								$con = connect();
								$query5="DELETE FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=1 AND REF_ID IN ($update_id) ";
								$not_delete=execute_query($query5,1);
								if($not_delete == 1)
								{
									oci_commit($con);
								}
								else 
								{
									oci_rollback($con);
								}
								
							} 
						}
						

					}
				}
				echo "1**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id)."**".$not_res."**".$not_delete;
			  }
			  else
			  {
				  oci_rollback($con);
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		  disconnect($con);
		  die;
	}
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//Delete before validation.............................................................
		//select REQU_NO_ID as REQ_DARTA from INV_QUOT_EVALU_MST where REQU_NO_ID=6833;
				
		$res=return_field_value("id","inv_purchase_requisition_mst","id=$update_id and IS_APPROVED=1","id");
		if($res){$message="This purchase requisition is approved, Delete not allow.";}
			
		if($res==''){
			$res=return_field_value("REQU_NO_ID","INV_QUOT_EVALU_MST","REQU_NO_ID=$update_id","REQU_NO_ID");
			if($res){$message="This purchase requisition found in Quotation Evaluation, Delete not allow.";}
		}
		if($res==''){
			$res=return_field_value("REQUISITION_NO","WO_NON_ORDER_INFO_MST","REQUISITION_NO=$update_id","REQUISITION_NO");
			if($res){$message="This purchase requisition found in Work Order, Delete not allow.";}
		}
		if($res==''){				
			$res=return_field_value("REQ_DARTA","REQ_COMPARATIVE_MST","REQ_DARTA=$update_id","REQ_DARTA");
			if($res){$message="This purchase requisition found in Comparative Statement, Delete not allow.";}
		}		
		//....................................................................................end;
		
		
		
		$prev_pay_mode=return_field_value("pay_mode","inv_purchase_requisition_mst","id=$update_id","pay_mode");
		$next_opp_check=0;
		if($prev_pay_mode==4)
		{
			$next_opp_check=return_field_value("id","inv_receive_master","booking_id=$update_id and entry_form in(4,20) and receive_basis = 7","id");
		}
		else
		{
			$next_opp_check=return_field_value("id","wo_non_order_info_dtls","requisition_no='".str_replace("'","",$update_id)."'","id");
		}
		if($next_opp_check)
		{
			echo "11**Next Operation Found, Delete Not Allow";disconnect($con);die;
		}

		$check_comparative_statement=sql_select("select req_item_mst_id as REQ_ITEM_MST_ID from req_comparative_mst where entry_form=481 and basis_id=1 and status_active=1 and is_deleted=0");
		$update_id=str_replace("'","",$update_id);
		if(count($check_comparative_statement)>0)
		{
			foreach($check_comparative_statement as $row)
			{
				$all_req_mst_id=explode(',',$row['REQ_ITEM_MST_ID']);
				foreach($all_req_mst_id as $val)
				{
					if($val==$update_id)
					{
						echo "11**Next Operation Found, Delete Not Allow";disconnect($con);die;
					}
				}
			}
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";

		$rID=sql_delete("inv_purchase_requisition_mst",$field_array,$data_array,"id","".$update_id."",1);
		$rID2=sql_update("inv_purchase_requisition_dtls",'status_active*is_deleted','0*1',"mst_id",$update_id,1);

		  if($db_type==0)
		  {
			  if($rID && $rID2)
			  {
				  mysql_query("COMMIT");
				  echo "2**";
			  }
			  else
			  {
				  mysql_query("ROLLBACK");
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		  else if($db_type==2)
		  {
			  if($rID && $rID2)
			  {
				oci_commit($con);
				  echo "2**";
			  }
			  else
			  {
				 oci_rollback($con);
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		  disconnect($con);
		  die;
	}
}

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)  // Insert Here==================================================================
	{
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}

		$id=return_next_id( "id", "inv_purchase_requisition_dtls",1);
		

        //Plz dont join details table here.
        $sql_select_master_data = sql_select("select company_id, cbo_currency, requisition_date from inv_purchase_requisition_mst where status_active = 1 and is_deleted = 0 and id = $update_id");
        $company_id = $sql_select_master_data[0][csf('company_id')];
        $currency_id = $sql_select_master_data[0][csf('cbo_currency')];
        $requ_date = change_date_format($sql_select_master_data[0][csf('requisition_date')]);

        $sql_select_dtls_data = sql_select("select b.item_category from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id=$update_id and a.entry_form=69 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
        $check_mixed_category_arr=array();
        foreach($sql_select_dtls_data as $row)
        {
            $check_mixed_category_arr[$row[csf('item_category')]]=$row[csf('item_category')];
        }
		
		$item_create_setting =return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$company_id and variable_list=45 and status_active=1 and is_deleted=0","auto_transfer_rcv");

        $all_category_arr = array(); $category_qty = array();$all_prod_id_arr=array();
		for($i=1; $i<=$tot_row; $i++)
		{
			$item_id="item_".$i;
			if(str_replace("'","",$$item_id)) $all_prod_id_arr[str_replace("'","",$$item_id)]=str_replace("'","",$$item_id);
		}
		
		
		
		$prod_sql="Select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, ITEM_SUB_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE from PRODUCT_DETAILS_MASTER where id in(".implode(",",$all_prod_id_arr).")";
		$prod_sql_result=sql_select($prod_sql);
		$prod_category=$prod_group=array();$prod_sub_group=$prod_description="";
		foreach($prod_sql_result as $row)
		{
			$prod_com=$row["COMPANY_ID"];
			$prod_category[$row["ITEM_CATEGORY_ID"]]=$row["ITEM_CATEGORY_ID"];
			$prod_group[$row["ITEM_GROUP_ID"]]=$row["ITEM_GROUP_ID"];
			if(trim($row["SUB_GROUP_NAME"])!="") $prod_sub_group.="'".trim($row["SUB_GROUP_NAME"])."',";
			if(trim($row["ITEM_DESCRIPTION"])!="") $prod_description.="'".trim($row["ITEM_DESCRIPTION"])."',";
		}
		$prod_sub_group=chop($prod_sub_group,",");
		$prod_description=chop($prod_description,",");
		$des_cond="";
		if($prod_sub_group!="") $des_cond.=" and SUB_GROUP_NAME in($prod_sub_group)";
		if($prod_description!="") $des_cond.=" and ITEM_DESCRIPTION in($prod_description)";
		//$des_wise_prod_sql="Select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, ITEM_SUB_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE from PRODUCT_DETAILS_MASTER where COMPANY_ID='$prod_com' and ITEM_CATEGORY_ID in(".implode(",",$prod_category).") and ITEM_GROUP_ID in(".implode(",",$prod_group).") $des_cond";
		
		$des_wise_prod_sql="Select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, ITEM_SUB_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE from PRODUCT_DETAILS_MASTER where status_active=1 and COMPANY_ID=$company_id and ITEM_CATEGORY_ID in(".implode(",",$prod_category).")";
		$des_wise_prod_sql_result=sql_select($des_wise_prod_sql);
		$prod_data_arr=array();
		foreach($des_wise_prod_sql_result as $val)
		{
			// $prod_data_arr[$val["COMPANY_ID"]][$val["ITEM_CATEGORY_ID"]][$val["ITEM_GROUP_ID"]][$val["ITEM_NUMBER"]][trim($val["ITEM_DESCRIPTION"])][trim($val["ITEM_SIZE"])][trim($val["MODEL"])]=$val["ID"];

			$prod_data_arr[$val["COMPANY_ID"]][$val["ITEM_CATEGORY_ID"]][$val["ITEM_GROUP_ID"]][trim($val["ITEM_DESCRIPTION"])][trim($val["ITEM_SIZE"])][trim($val["MODEL"])][trim($val["ITEM_NUMBER"])]=$val["ID"];
		}
		//echo "10**<pre>";print_r($prod_data_arr);die;
		unset($des_wise_prod_sql_result);
		

		$category_mixing_variable =return_field_value("allocation","variable_settings_inventory","company_name=$company_id and variable_list=44 and status_active=1 and is_deleted=0 order by id desc ","allocation");
		$field_array ="id,mst_id,item_category,product_id,service_details,required_for,cons_uom,quantity,approval_pre_quantity,brand_name,model,origin,rate,amount,stock,vehicle_no,remarks,delivery_date,used_for,inserted_by,insert_date,status_active,is_deleted,issue_req_dtls_id,itemissue_mst_id,itemissue_req_prefix_num,servicesfor_lib_id";
		$prod_scrtipt=true;$issue_req_id_arr=array();
		for($i=1; $i<=$tot_row; $i++)
		{
			$item_description="itemdescription_".$i;
			$item_cat_id="cboItemCategory_".$i;
			$item_size="itemsize_".$i;
			$item_group_id="hiddenitemgroupid_".$i;
			$txtreqfor="txtreqfor_".$i;
			//$txt_uom="txtuom_".$i;
			$txtuom="txtuom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$stock="stock_".$i;
			$reorder_lable="reorderlable_".$i;
			$txtvehicle="txtvehicle_".$i;
			$txt_remarks="txtremarks_".$i;
			$cbo_status="cbostatus_".$i;
			/*additional code*/
			$txt_brand="txtbrand_".$i;
			$txt_model_name="txtmodelname_".$i;
			$itemnumber="itemnumber_".$i;
			$cbo_origin="cboOrigin_".$i;
			$item_id="item_".$i;
			$txtdatedelivery="txtdatedelivery_".$i;
			$txt_used_for="txtused_".$i;
			$hiddenissreqdtlsid="hiddenissreqdtlsid_".$i;
			$txt_indent="txtIndent_".$i;
			$mst_id="mstId_".$i;
			$cboServiceFor = "cboServiceFor_".$i;

			$all_prod_id.=str_replace("'","",$$item_id).",";
			$quantity=str_replace(",", '', $$quantity);
			$previous_prod_id=str_replace("'","",$$item_id);
			// if(str_replace("'","",$$txtuom)==0)
			// {
			// 	echo "11**UOM Not Found.";disconnect($con);die;
			// }

			if(str_replace("'","",$$txtuom)==0)
			{
				echo "11**PLEASE FEEL OF THE ITEM DESCRIPTION";disconnect($con);die;
			}
			$prod_id=0;
			if(str_replace("'","",$$item_cat_id) ==5 || str_replace("'","",$$item_cat_id) ==6 || str_replace("'","",$$item_cat_id) ==7 || str_replace("'","",$$item_cat_id) ==23 || $item_create_setting !=1 || str_replace("'","",$$item_cat_id) ==114 || str_replace("'","",$cbo_req_basis) ==7)
			{
				if(str_replace("'","",$$item_id)) $prod_id=str_replace("'","",$$item_id);
			}
			else
			{
				//echo "10**".$company_idstr_replace("'","",$$item_cat_id)."=".str_replace("'","",$$item_group_id)."=".trim(str_replace("'","",$$item_description))."=".trim(str_replace("'","",$$item_size))."=".trim(str_replace("'","",$$txt_model_name))."=".trim(str_replace("'","",$$itemnumber));die;

				$prod_id=$prod_data_arr[$company_id][str_replace("'","",$$item_cat_id)][str_replace("'","",$$item_group_id)][trim(str_replace("'","",$$item_description))][trim(str_replace("'","",$$item_size))][trim(str_replace("'","",$$txt_model_name))][trim(str_replace("'","",$$itemnumber))];  
				if($prod_id>0)
				{
					$prod_id=$prod_id;
				}
				else
				{
					$txt_product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$prod_id=$txt_product_id;
					$prod_scrtipt=execute_query("insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form, item_size, model, inserted_by, insert_date) 
					select	
					'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form,'".str_replace("'","",$$item_size)."','".str_replace("'","",$$txt_model_name)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0");
					if($prod_scrtipt==false)
					{
						echo "10**insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form, item_size, model, inserted_by, insert_date) 
						select	
						'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form,'".str_replace("'","",$$item_size)."','".str_replace("'","",$$txt_model_name)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0";oci_rollback($con);disconnect($con);die;
					}
				}
			}
			
			
			if($quantity!="")
			{
				if ($i!=1) $data_array .=",";
				/*$data_array .="(".$id.",".$update_id.",".$$item_id.",".$$txtreqfor.",".$$hidden_txtuom.",".$$quantity.",".$$txt_brand.",".$$txt_model_name.",".$$rate.",".$$amount.",".$$stock.",".$$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbo_status.",0)";*/
				$data_array .="(".$id.",".$update_id.",".$$item_cat_id.",'".$prod_id."',".$$item_description.",".$$txtreqfor.",".$$txtuom.",".$quantity.",".$quantity.",".$$txt_brand.",".$$txt_model_name.",".$$cbo_origin.",".$$rate.",".$$amount.",".$$stock.",".$$txtvehicle.",".$$txt_remarks.",".$$txtdatedelivery.",".$$txt_used_for.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbo_status.",0,".$$hiddenissreqdtlsid.",".$$mst_id.",".$$txt_indent.",".$$cboServiceFor.")"; 
				$id=$id+1;
                $all_category_arr[str_replace("'", "", $$item_cat_id)] = str_replace("'", "", $$item_cat_id);
                $category_qty[str_replace("'", "", $$item_cat_id)] += str_replace("'", "", $$amount);
				$check_mixed_category_arr[str_replace("'", "", $$item_cat_id)]=str_replace("'", "", $$item_cat_id);
				$issue_req_id_arr[str_replace("'", "", $$hiddenissreqdtlsid)]=str_replace("'", "", $$hiddenissreqdtlsid);
			}
		}
		
		// Check variable setting Category Mixing in Purchase Requisition
		//echo '<pre>';print_r($check_mixed_category_arr);die;
		if ($category_mixing_variable != 1)
		{
			if (count($check_mixed_category_arr) > 1){
				echo "11**Category Mixing Not Allow In Same Requisition.";disconnect($con);die;
			}
		}

		$all_prod_id=chop($all_prod_id,",");
		//echo "10**$all_prod_id";die;
		if($all_prod_id!="" && str_replace("'","",$cbo_req_basis) !=7)
		{
			$prev_data=sql_select("select id, product_id from inv_purchase_requisition_dtls where status_active=1 and is_deleted=0 and mst_id=$update_id and product_id in($all_prod_id)");
			if(count($prev_data)>0)
			{
				echo "11**Duplicate Product Not Allow In Same Requisition.";disconnect($con);die;
			}
		}

        //New Devlopment for monthly category wise budget limit validate -- Md. Jakir Hosen
        $sql_get_veriable_setting = sql_select("select id,budget_validation_status,budget_validation_page from  variable_settings_commercial where company_name=$company_id and variable_list=35 order by id desc" );

        if(count($sql_get_veriable_setting) > 0)
		{
            if($sql_get_veriable_setting[0][csf('budget_validation_status')] == 1 && $sql_get_veriable_setting[0][csf('budget_validation_page')] == 1)
			{
                $date = new DateTime($requ_date);
                $date->modify('first day of this month');
                $firstday= $date->format('d-m-Y');
                $date->modify('last day of this month');
                $lastday= $date->format('d-m-Y');

                $current_month = date("m", strtotime($requ_date));
                $sql_get_current_mon_budget = sql_select("select a.id, a.currency_id, b.category_id, b.budget_amount from lib_category_budget_mst a, lib_category_budget_dtls b  where a.id = b.mst_id and a.company_id = $company_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and (a.APPLYING_DATE_FROM <= to_date($current_month, 'MM') and a.APPLYING_DATE_to >= to_date($current_month, 'MM')) and b.category_id in (".implode(",", $all_category_arr).")");
                // if(count($sql_get_current_mon_budget) > 0){
                    $cat_wise_budget_arr = array();
                    $currency_id = $sql_get_current_mon_budget[0][csf('currency_id')];
                    foreach ($sql_get_current_mon_budget as $data){
                        $conversion_date=change_date_format($requ_date, "d-M-y", "-",1);
                        $currency_rate=set_conversion_rate($currency_id, $conversion_date, $company_id);
                        $cat_wise_budget_arr[$data[csf('category_id')]] = $data[csf('budget_amount')]*$currency_rate; //convert bdt
                    }
                    $sql_get_cat_amount = sql_select("select a.CBO_CURRENCY, b.ITEM_CATEGORY, sum(b.AMOUNT) as AMOUNT from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id = b.mst_id and a.COMPANY_ID = $company_id and a.status_active = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0
                    and a.REQUISITION_DATE BETWEEN to_date('$firstday', 'DD-MM-YY') and to_date('$lastday', 'DD-MM-YY') and b.item_category in (".implode(",", $all_category_arr).") group by a.CBO_CURRENCY, b.ITEM_CATEGORY");
                    $prev_cat_wise_budget = array();
                    $alert_data = "Monthly Category wise Budget Exceeds!\n";
                    foreach ($sql_get_cat_amount as $data){
                        $conversion_date=change_date_format($requ_date, "d-M-y", "-",1);
                        if($data[csf('cbo_currency')] == 0 || $data[csf('cbo_currency')] == ''){
                            $data[csf('cbo_currency')] = 1;
                        }
                        $currency_rate1=set_conversion_rate($data[csf('cbo_currency')], $conversion_date, $company_id);
                        $prev_cat_wise_budget[$data[csf('item_category')]] = $data['AMOUNT']*$currency_rate1; //convert bdt
                    }
                    $error_rpt = 0;
					$error_rpt_2=0;
                    $alert_data .= "Category Name -- Budget Limit(BDT) -- Curr. Bal.(BDT)\n";
                    foreach ($category_qty as $key => $val){
						if (array_key_exists($key,$cat_wise_budget_arr)){
							if(($val+$prev_cat_wise_budget[$key]) > $cat_wise_budget_arr[$key]){
								$error_rpt++;
								$alert_data .= $error_rpt.". ".$general_item_category[$key]." -- ".$cat_wise_budget_arr[$key]." -- ".($cat_wise_budget_arr[$key] - $prev_cat_wise_budget[$key])."\n";
							}
				    	}else
						{
							$error_rpt_2++;

						}
                    }
                    if($error_rpt > 0){
                        echo "55**$alert_data";
                        die;
                    }
					if($error_rpt_2 > 0){
                        echo "55**This category against budget not found";
                        die;
                    }
                // }
            }
        }
        //End Monthly Category Wise Budget Limit Validate
        $rID=$rID2=1;
		//echo "10**insert into inv_purchase_requisition_dtls ($field_array) values $data_array";oci_rollback($con);disconnect($con);die;
		$rID=sql_insert("inv_purchase_requisition_dtls",$field_array,$data_array,0);
		$is_mixed_category=2;
		if ($category_mixing_variable==1) $is_mixed_category=1;
		$rID2=sql_update("inv_purchase_requisition_mst","is_mixed_category","$is_mixed_category","id",str_replace("'", '', $update_id),1);
		$rID3=true;
		if(count($issue_req_id_arr)>0 && str_replace("'","",$cbo_req_basis) ==7)
		{
			$rID3=execute_query("update inv_itemissue_requisition_dtls set is_purchaser_req=1 where id in(".implode(",",$issue_req_id_arr).")");
		}
		
		//echo "10**$rID=$rID2=$prod_scrtipt";oci_rollback($con);disconnect($con);die;
		if($db_type==0)
		{
			if($rID && $rID2 && $prod_scrtipt && $rID3)
			{
				mysql_query("COMMIT");
				//echo "0**".$update_id;
				echo "0**".str_replace("'", '', $update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'", '', $update_id);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2 && $prod_scrtipt)
			{
				oci_commit($con);
				echo "0**".str_replace("'", '', $update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'", '', $update_id);
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here===============================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$sql_other_purces_data_arr = sql_select("select id from wo_non_order_info_mst where status_active = 1 and is_deleted = 0 and REQUISITION_NO = TO_CHAR($update_id)");
		if($sql_other_purces_data_arr[0][csf('id')]>0){
		   echo "24**";die;disconnect($con); oci_rollback($con);
		}
		
		//Plz dont join details table here.
        $sql_select_master_data = sql_select("select company_id, cbo_currency, requisition_date from inv_purchase_requisition_mst where status_active = 1 and is_deleted = 0 and id = $update_id");
        $company_id = $sql_select_master_data[0][csf('company_id')];
        $currency_id = $sql_select_master_data[0][csf('cbo_currency')];
        $requ_date = change_date_format($sql_select_master_data[0][csf('requisition_date')]);		      
		$item_create_setting =return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$company_id and variable_list=45 and status_active=1 and is_deleted=0","auto_transfer_rcv");
        $all_category_arr = array(); $category_qty = array();
		$item_id="item_".$tot_row;
		$item_description="itemdescription_".$tot_row;
		$item_cat_id="cboItemCategory_".$tot_row;
		$item_size="itemsize_".$tot_row;
		$item_group_id="hiddenitemgroupid_".$tot_row;
		$txtreqfor="txtreqfor_".$tot_row;
		//$txt_uom="txtuom_".$tot_row;
		$txtuom="txtuom_".$tot_row;
		$quantity="quantity_".$tot_row;
		$rate="rate_".$tot_row;
		$amount="amount_".$tot_row;
		$stock="stock_".$tot_row;
		$reorder_lable="reorderlable_".$tot_row;
		$txtvehicle="txtvehicle_".$tot_row;
		$txt_remarks="txtremarks_".$tot_row;
		$cboServiceFor = "cboServiceFor_".$tot_row;
		$cbo_status="cbostatus_".$tot_row;
		/*additional code*/
		$txt_brand="txtbrand_".$tot_row;
		$txt_model_name="txtmodelname_".$tot_row;
		$itemnumber="itemnumber_".$i;
		$cbo_origin="cboOrigin_".$tot_row;//new
		$hiddenid_dtls="hiddenid_".$tot_row;
		$txtdatedelivery="txtdatedelivery_".$tot_row;
		$txt_used_for="txtused_".$tot_row;
			 // echo "330309   ".$$hiddenid_dtls;die;
		$hiddDtls=str_replace("'", '', $$hiddenid_dtls);
		$quantity=str_replace(",", '', $$quantity);
		//$prod_id=str_replace(",", '', $$item_id);
		$previous_prod_id=str_replace("'","",$$item_id);
		
		$prod_sql="Select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, ITEM_SUB_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE from PRODUCT_DETAILS_MASTER where id in(".$previous_prod_id.")";
		$prod_sql_result=sql_select($prod_sql);
		$prod_com=$prod_sql_result[0]["COMPANY_ID"];
		$prod_category=$prod_sql_result[0]["ITEM_CATEGORY_ID"];
		$prod_group=$prod_sql_result[0]["ITEM_GROUP_ID"];
		$prod_sub_group=$prod_sql_result[0]["SUB_GROUP_NAME"];
		$prod_description=$prod_sql_result[0]["ITEM_DESCRIPTION"];
		$des_cond="";
		if($prod_sub_group!="") $des_cond.=" and SUB_GROUP_NAME='$prod_sub_group'";
		if($prod_description!="") $des_cond.=" and ITEM_DESCRIPTION='$prod_description'";
		//$des_wise_prod_sql="Select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, ITEM_SUB_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE from PRODUCT_DETAILS_MASTER where COMPANY_ID='$prod_com' and ITEM_CATEGORY_ID='$prod_category' and ITEM_GROUP_ID='$prod_group' $des_cond";
		
		$des_wise_prod_sql="Select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, ITEM_SUB_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE from PRODUCT_DETAILS_MASTER where status_active=1 and COMPANY_ID=$company_id and ITEM_CATEGORY_ID='$prod_category'";
		$des_wise_prod_sql_result=sql_select($des_wise_prod_sql);
		$prod_data_arr=array();
		foreach($des_wise_prod_sql_result as $val)
		{
			$prod_data_arr[$val["COMPANY_ID"]][$val["ITEM_CATEGORY_ID"]][$val["ITEM_GROUP_ID"]][trim($val["ITEM_DESCRIPTION"])][trim($val["ITEM_SIZE"])][trim($val["MODEL"])]=$val["ID"];
		}
		//echo "10**<pre>";print_r($prod_data_arr);die;
		unset($des_wise_prod_sql_result);
		
		
		
		$next_opp_qty_array=array(); $next_opp_check=$next_opp='';
		if(str_replace("'", '', $cbo_pay_mode)==4)
		{
			$sql_receive=sql_select("select a.id,a.recv_number,b.prod_id,b.cons_quantity from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(4,20) and a.receive_basis = 7 and b.prod_id=$previous_prod_id and a.receive_basis = 7 and a.booking_id=$update_id");
			foreach($sql_receive as $row){
				$next_opp_check=$row[csf('id')];
				$next_opp .=$row[csf('recv_number')].',';
				$next_opp_qty_array[$row[csf('prod_id')]] +=$row[csf('cons_quantity')];
			}
		}
		else
		{
			$sql_wo=sql_select("select a.id,a.wo_number,b.item_id,b.supplier_order_quantity from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and requisition_dtls_id='".str_replace("'","",$hiddDtls)."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(145,146,147) and b.item_id=$previous_prod_id ");
			foreach($sql_wo as $row){
				$next_opp_check=$row[csf('id')];
				$next_opp .=$row[csf('wo_number')].',';
				$next_opp_qty_array[$row[csf('item_id')]] +=$row[csf('supplier_order_quantity')];
			}
			//$next_opp_check=return_field_value("id","wo_non_order_info_dtls","requisition_dtls_id='".str_replace("'","",$hiddDtls)."' and status_active=1","id");
		}

		if ($next_opp_check!='') {
			$next_opp_qty=$next_opp_qty_array[str_replace("'","",$prod_id)];
			//echo "10**".$next_opp_qty.'=='.str_replace("'", '', $quantity); disconnect($con);die;
			if($next_opp_qty >str_replace("'", '', $quantity)){
				$next_opp=implode(",",array_unique(explode(",",chop($next_opp,','))));
				echo "11**Requisition Quantity Can't Less Than Next Transaction Quantity. \n Please check $next_opp"; disconnect($con);die;
			}
		}
        $all_category_arr[str_replace("'", "", $$item_cat_id)] = str_replace("'", "", $$item_cat_id);
        $category_qty[str_replace("'", "", $$item_cat_id)] += str_replace("'", "", $$amount);
		
		//echo $rID;die;

        //New Devlopment for monthly category wise budget limit validate -- Md. Jakir Hosen
        $sql_get_veriable_setting = sql_select("select id,budget_validation_status,budget_validation_page from  variable_settings_commercial where company_name=$company_id and variable_list=35 order by id desc" );

        if(count($sql_get_veriable_setting) > 0){
            if($sql_get_veriable_setting[0][csf('budget_validation_status')] == 1 && $sql_get_veriable_setting[0][csf('budget_validation_page')] == 1){

                $date = new DateTime($requ_date);
                $date->modify('first day of this month');
                $firstday= $date->format('d-m-Y');
                $date->modify('last day of this month');
                $lastday= $date->format('d-m-Y');

                $current_month = date("m", strtotime($requ_date));
                $sql_get_current_mon_budget = sql_select("select a.id, a.currency_id, b.category_id, b.budget_amount from lib_category_budget_mst a, lib_category_budget_dtls b  where a.id = b.mst_id and a.company_id = $company_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and (a.APPLYING_DATE_FROM <= to_date($current_month, 'MM') and a.APPLYING_DATE_to >= to_date($current_month, 'MM')) and b.category_id in (".implode(",", $all_category_arr).")");
                if(count($sql_get_current_mon_budget) > 0){
                    $cat_wise_budget_arr = array();
                    $currency_id = $sql_get_current_mon_budget[0][csf('currency_id')];
                    foreach ($sql_get_current_mon_budget as $data){
                        $conversion_date=change_date_format($requ_date, "d-M-y", "-",1);
                        $currency_rate=set_conversion_rate($currency_id, $conversion_date, $company_id);
                        $cat_wise_budget_arr[$data[csf('category_id')]] = $data[csf('budget_amount')]*$currency_rate; //convert bdt
                    }
                    $sql_get_cat_amount = sql_select("select a.CBO_CURRENCY, b.ITEM_CATEGORY, sum(b.AMOUNT) as AMOUNT from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id = b.mst_id and a.COMPANY_ID = $company_id and a.status_active = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0
                and a.REQUISITION_DATE BETWEEN to_date('$firstday', 'DD-MM-YY') and to_date('$lastday', 'DD-MM-YY') and b.item_category in (".implode(",", $all_category_arr).") and b.id <> $hiddDtls group by a.CBO_CURRENCY, b.ITEM_CATEGORY");
                    $prev_cat_wise_budget = array();
                    $alert_data = "Monthly Category wise Budget Exceeds!\n";
                    foreach ($sql_get_cat_amount as $data){
                        $conversion_date=change_date_format($requ_date, "d-M-y", "-",1);
                        if($data[csf('cbo_currency')] == 0 || $data[csf('cbo_currency')] == ''){
                            $data[csf('cbo_currency')] = 1;
                        }
                        $currency_rate1=set_conversion_rate($data[csf('cbo_currency')], $conversion_date, $company_id);
                        $prev_cat_wise_budget[$data[csf('item_category')]] = $data['AMOUNT']*$currency_rate1; //convert bdt
                    }
                    $error_rpt = 0;
					$error_rpt_2=0;
                    $alert_data .= "Category Name -- Budget Limit(BDT) -- Curr. Bal.(BDT)\n";
                    foreach ($category_qty as $key => $val){
						if (array_key_exists($key,$cat_wise_budget_arr)){
							if(($val+$prev_cat_wise_budget[$key]) > $cat_wise_budget_arr[$key]){
								$error_rpt++;
								$alert_data .= $error_rpt.". ".$general_item_category[$key]." -- ".$cat_wise_budget_arr[$key]." -- ".($cat_wise_budget_arr[$key] - $prev_cat_wise_budget[$key])."\n";
							}
				     	}else{
							$error_rpt_2++;

						}
                    }
                    if($error_rpt > 0){
                        echo "55**$alert_data";
                        die;
                    }
					if($error_rpt_2 > 0){
                        echo "55**This category against budget not found";
                        die;
                    }
                }
            }
        }
        //End Monthly Category Wise Budget Limit Validate
		$prod_scrtipt=true;$prod_id=0;
		if(str_replace("'","",$$item_cat_id) ==5 || str_replace("'","",$$item_cat_id) ==6 || str_replace("'","",$$item_cat_id) ==7 || str_replace("'","",$$item_cat_id) ==23 || $item_create_setting !=1 || str_replace("'","",$$item_cat_id) ==114 || str_replace("'","",$cbo_req_basis) ==7)
		{
			if(str_replace("'","",$previous_prod_id)) $prod_id=$previous_prod_id;
		
		}
		else
		{
			$prod_id=$prod_data_arr[$company_id][str_replace("'","",$$item_cat_id)][str_replace("'","",$$item_group_id)][trim(str_replace("'","",$$item_description))][trim(str_replace("'","",$$item_size))][trim(str_replace("'","",$$txt_model_name))];
			//echo "10**";print_r($prod_data_arr); ."=".$prod_id."=".$prod_id."=".$prod_id."=".$prod_id."=".$prod_id die;
			//echo "10**".$prod_sql."=".$prod_id;die;
			if($prod_id>0)
			{
				$prod_id=$prod_id;
			}
			else
			{
				$txt_product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$prod_id=$txt_product_id;
				$prod_scrtipt=execute_query("insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form, item_size, model, inserted_by, insert_date) 
				select	
				'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form,'".str_replace("'","",$$item_size)."','".str_replace("'","",$$txt_model_name)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0");
				if($prod_scrtipt==false)
				{
					echo "10**insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form, item_size, model, inserted_by, insert_date) 
					select	
					'".str_replace("'","",$txt_product_id)."', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, is_compliance, item_sub_group_id, entry_form,'".str_replace("'","",$$item_size)."','".str_replace("'","",$$txt_model_name)."', ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0";oci_rollback($con);disconnect($con);die;
				}
			}
		}
		
		$field_array="required_for*product_id*service_details*cons_uom*quantity*approval_pre_quantity*brand_name*model*origin*rate*amount*stock*vehicle_no*remarks*delivery_date*used_for*status_active*updated_by*update_date*servicesfor_lib_id";
		$data_array ="".$$txtreqfor."*".$prod_id."*".$$item_description."*".$$txtuom."*".$quantity."*".$quantity."*".$$txt_brand."*".$$txt_model_name."*".$$cbo_origin."*".$$rate."*".$$amount."*".$$stock."*".$$txtvehicle."*".$$txt_remarks."*".$$txtdatedelivery."*".$$txt_used_for."*".$$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cboServiceFor."";
		/*additional code*/
		/*$data_array ="".$$txtreqfor."*".$$quantity."*".$$txt_brand."*".$$txt_model_name."*".$$rate."*".$$amount."*".$$stock."*".$$txt_remarks."*".$$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";*/
		$rID=1;
		$rID=sql_update("inv_purchase_requisition_dtls",$field_array,$data_array,"id",$hiddDtls,1);
		//echo "10**$rID=$prod_scrtipt";oci_rollback($con);disconnect($con);die;
		// echo $rID;oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID && $prod_scrtipt)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'", '', $update_id);
			}
		}
		if($db_type==2)
		{
			if($rID && $prod_scrtipt)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'", '', $update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$sql_other_purces_data_arr = sql_select("select id from wo_non_order_info_mst where status_active = 1 and is_deleted = 0 and REQUISITION_NO = TO_CHAR($update_id)");
		if($sql_other_purces_data_arr[0][csf('id')]>0){
		   echo "24**";die;disconnect($con); oci_rollback($con);
		}

		$prev_pay_mode=return_field_value("pay_mode","inv_purchase_requisition_mst","id=$update_id","pay_mode");
		$next_opp_check=0;
		if($prev_pay_mode==4)
		{
			$next_opp_check=return_field_value("a.id as id","inv_receive_master a, inv_transaction b","a.id=b.mst_id and b.transaction_type=1 and a.booking_id=$update_id and a.entry_form in(4,20) and a.receive_basis = 7 and b.prod_id=$item_1 and status_active=1","id");
		}
		else
		{
			$next_opp_check=return_field_value("id","wo_non_order_info_dtls","requisition_dtls_id='".str_replace("'","",$hiddenid_1)."' and status_active=1","id");
		}
		if($next_opp_check)
		{
			echo "11**Next Operation Found, Delete Not Allow";disconnect($con);die;
		}

		$check_comparative_statement=sql_select("select req_item_dtls_id as REQ_ITEM_DTLS_ID from req_comparative_mst where entry_form=481 and basis_id=1 and status_active=1 and is_deleted=0");
		$hiddenid_1=str_replace("'","",$hiddenid_1);
		if(count($check_comparative_statement)>0)
		{
			foreach($check_comparative_statement as $row)
			{
				$all_req_dtls_id=explode(',',$row['REQ_ITEM_DTLS_ID']);
				foreach($all_req_dtls_id as $val)
				{
					if($val==$hiddenid_1)
					{
						echo "11**Next Operation Found, Delete Not Allow";disconnect($con);die;
					}
				}
			}
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";	

		
		$rID=sql_update("inv_purchase_requisition_dtls",$field_array,$data_array,"id",$hiddenid_1,1);

		$rID2=true;
		if(str_replace("'","",$cbo_req_basis) ==7)
		{
			$issue_requ_dtls_id=return_field_value("issue_req_dtls_id","inv_purchase_requisition_dtls","id=$hiddenid_1","issue_req_dtls_id");		
			$rID2=execute_query("update inv_itemissue_requisition_dtls set is_purchaser_req=0 where id=$issue_requ_dtls_id");
		}
		
		//$rID=sql_update("inv_purchase_requisition_dtls",'status_active*is_deleted','0*1',"id",$hiddenid_1,1);

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="unapp_request_popup")
{
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	echo load_html_head_contents("Un Approval Request","../../", 1, 1, $unicode);
	extract($_REQUEST);

	$data_all=explode('_',$data);
	$requ_no=$data_all[0];
	$unapp_request=$data_all[1];

	$wo_id=return_field_value("id", "inv_purchase_requisition_mst", "requ_no='$requ_no' and entry_form=69 and status_active=1 and is_deleted=0");
	//echo $wo_id.'DDDDDDD';
	if($unapp_request=="")
	{
		  $sql_request="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=1 and user_id='$user_id' and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";


		$nameArray_request=sql_select($sql_request);
		foreach($nameArray_request as $row)
		{
			$unapp_request=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf('id')]."' and status_active=1 and is_deleted=0");
		}
	}
	?>
    <script>

		$( document ).ready(function() {
			document.getElementById("unappv_request").value='<? echo $unapp_request; ?>';
		});

		var permission='<? echo $permission; ?>';

		function fnc_appv_entry(operation)
		{
			var unappv_request = $('#unappv_request').val();

			if (form_validation('unappv_request','Un Approval Request')==false)
			{
				if (unappv_request=='')
				{
					alert("Please write request.");
				}
				return;
			}
			else
			{

				var data="action=save_update_delete_unappv_request&operation="+operation+get_submitted_data_string('unappv_request*wo_id*page_id*user_id',"../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","purchase_requisition_v2_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);
				if(reponse[0]==0 || reponse[0]==1)
				{
					//set_button_status(1, permission, 'fnc_appv_entry',1);
				}
				release_freezing();

			}
		}

		function fnc_close()
		{
			//unappv_request= $("#unappv_request").val();

			//document.getElementById('hidden_appv_cause').value=unappv_request;

			parent.emailwindow.hide();
		}

    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_request" id="unappv_request" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
                           /* if($unapp_request!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }*/
							echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="not_approve_cause_popup")
{
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	echo load_html_head_contents("Un Approval Request","../../", 1, 1, $unicode);
	extract($_REQUEST);

	$data_all=explode('_',$data);
	?>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
       
			
            <table align="center" cellspacing="0" width="380" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <thead>
                	<tr>
                		<th>Not Appv. Cause</th>
                	</tr>
                </thead>
                <tbody>
                	<tr>
                		<td><? echo $data_all[0]; ?></td>
                	</tr>
                </tbody>
            </table>

           
            
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="save_update_delete_unappv_request")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$approved_no=return_field_value("MAX(approved_no)","approval_history","entry_form=1 and mst_id=$wo_id");
		if($approved_no!='') $approved_no=$approved_no;else $approved_no=0;

		$unapproved_request=return_field_value("id","fabric_booking_approval_cause","page_id=$page_id and entry_form=1 and user_id=$user_id and booking_id=$wo_id and approval_type=2 and approval_no=$approved_no");

		if($unapproved_request=="")
		{

			$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

			$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id_mst.",".$page_id.",1,".$user_id.",".$wo_id.",2,".$approved_no.",".$unappv_request.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
			// echo "10**insert into fabric_booking_approval_cause (".$field_array.") values ".$data_array;die;

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			if($db_type==2)
			{
				if($rID )
				{
					oci_commit($con);
					echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			if($db_type==1 )
			{

				echo "0**".$rID."**".$wo_id;
			}
			disconnect($con);
			die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
			$data_array="".$page_id."*1*".$user_id."*".$wo_id."*2*".$approved_no."*".$unappv_request."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

			 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$unapproved_request."",0);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}

			if($db_type==2)
			{
				if($rID )
				{
					oci_commit($con);
					echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			if($db_type==1 )
			{
				echo "1**".$rID."**".str_replace("'","",$wo_id);
			}
			disconnect($con);
			die;
		}
	}
	if ($operation==1)  // Update Here
	{

	}
}

if($action=="purchase_requisition_print") // Print Report 2
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$update_id=$data[1];
	$report_title=$data[2];
	$approveID=$data[3];
	$formate_id=$data[4];
	$cbo_template_id=$data[5];
	$company=$data[0];
	$location=$data[6];
	if(count($data)>7)
	{
		$path='../../';
	}
	else{
		$path='../';
	}
	//echo $company.'='.$location;die;
	//echo $data[4];die("with formate id");
	?>
	<style type="text/css">
		.wrd_brk{
			word-wrap:break-word; word-break:break-all;
		}
		.ver_align{
			vertical-align: middle;
		}

		.rpt_table td {
			border: 1px solid black;
		}
		.rpt_table thead th {			
			border: 1px solid black;			
		}
		@media print{
			#signature_bottom{ 
				position:no-repeat; 
				bottom:20px;
			}
			tr.page_break { 
				/* display: block; */ 
				page-break-before: always; }
			/*#table_row{
				position: relative;
				bottom: 0;
				height: 1180px!important;
				  overflow:hidden;
			}*/
		}

		@media screen{
		
		}
		td {
		padding: 6px;
		font-size:16px;
		}

		th {
		
			font-size:16px;
		}
		
	</style>
	<div id="table_row" style="width:1000px;">
		
		<?
		$sql="select a.id, a.requ_no, a.item_category_id, a.requisition_date, a.location_id, a.delivery_date, a.source, a.manual_req, a.department_id, a.section_id, a.store_name, a.pay_mode, a.cbo_currency, a.remarks, a.inserted_by, b.user_full_name, b.designation from inv_purchase_requisition_mst a left join user_passwd b on a.inserted_by=b.id where a.id=$update_id";
		$dataArray=sql_select($sql);
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
		$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
		$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
		$department=return_library_array("select id,department_name from lib_department",'id','department_name');
		$section=return_library_array("select id,section_name from lib_section",'id','section_name');
		$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
        $user_designation_arr=return_library_array( "select id,custom_designation from lib_designation",'id','custom_designation');

		$pay_cash=$dataArray[0][csf('pay_mode')];
		$inserted_by=$dataArray[0][csf('inserted_by')];
        $user_name=$dataArray[0][csf("user_full_name")];
        $user_designation=$user_designation_arr[$dataArray[0][csf("designation")]];
		$comp_logo = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
		//echo $path.$comp_logo;
		//echo $data[4].'##'.$path;
		if ($data[4]!=3)
		{
			
			if ($data[4]==4)
			{
				$widths=130;
				$th_span=2;
			}
			elseif ($data[4]==2 && $pay_cash==4)
			{
				$widths=130;
				$th_span=2;
			}
			else
			{
				$widths=0;
				$th_span=0;
			}
			$com_dtls = fnc_company_location_address($company, $location, 2);
				//echo $data[0].'=='.$location;
			?>
			
			<table width="1000" align="center">
				<tr class="form_caption">
					<td><img src='<? echo $path.$comp_logo;?>' height='70' width='200' align="middle" /></td>
					<td colspan="7" align="center" style="font-size:28px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>					
				</tr>
				<tr class="form_caption">
				<td>&nbsp; </td>
				<td colspan="7" align="center" style="font-size:18px;">
					<?
					echo $com_dtls[1];
					//echo show_company($data[0],'',''); //Aziz
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
					$req=explode('-',$dataArray[0][csf('requ_no')]);
					?>

					</td>
				</tr>
				<tr>
					<td>&nbsp; </td>
					<td colspan="6" align="center" style="font-size:22px;"><strong><u><? echo $data[2] ?></u></strong></td>
				</tr>
				<tr>
					<td width="120" style="font-size:16px"><strong>Req. No:</strong></td>
					<td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('requ_no')];
					//$req[2].'-'.$req[3]; ?></td>
					<td style="font-size:16px" width="130"><strong>Req. Date:</strong></td><td style="font-size:16px" width="175px"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
					<td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:16px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
					<td style="font-size:16px"><strong>Department:</strong></td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
					<td style="font-size:16px"><strong>Section:</strong></td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
					<td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
					<td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
					<td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
					<td style="font-size:16px"><strong>Remarks:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
					<td style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;"><? if($data[3]==1) echo "Approved"; else echo "&nbsp;"; ?></td>
				</tr>
			</table>
			<br>
			<?
			//$margin='-133px;';

			//echo $th_span.'='.$cash_span.'='.$span.'='.$margin.'='.$widths.'='.$cash;
			?>

			<table cellspacing="0" width="<? echo $cash+1340; ?>"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th style="font-size:15px" colspan="<? echo $th_span+13; ?>" width="<? echo $cash+1100; ?>" align="center" ><strong>Item Details</strong></th>
						<th style="font-size:15px" width="90" align="center" rowspan="2"><strong>Last Req. Info (Date+Qty)</strong></th>
						<th style="font-size:15px"  width="120"  rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th style="font-size:15px" width="30">SL</th>
						<th style="font-size:15px" width="120">Item Category</th>
						<th style="font-size:15px" width="50">Item Code</th>
						<th style="font-size:15px" width="140">Item Group</th>
						<th style="font-size:15px" width="170">Item Des.</th>
						<th style="font-size:15px" width="70">Req. For</th>
						<th style="font-size:15px" width="50">UOM</th>
						<th style="font-size:15px" width="60">Req. Qty.</th>
						<?
						if ($data[4]==4)
						{
							?>
							<th style="font-size:15px" width="60">Rate</th>
							<th style="font-size:15px" width="70">Amount</th>
							<?
						}
						if ($data[4]==2 && $pay_cash==4)
						{
							?>
							<th style="font-size:15px" width="50">Rate</th>
							<th style="font-size:15px" width="70">Amount</th>
							<?
						}
						?>
						<th style="font-size:15px" width="70">Stock</th>
						<th style="font-size:15px" width="100">Last Rec. Date</th>
						<th style="font-size:15px" width="60">Last Rec. Qty.</th>
						<th style="font-size:15px" width="110">Last 3 Month Avg. Iss. Qty</th>
						<th style="font-size:15px" width="70">Last Rate</th>
					</tr>
				</thead>
				<tbody>
				<?
				$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
				$receive_array=array();

				$sql_result= sql_select(" select a.id, a.requisition_date,b.id as dtls_id, a.store_name, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  ");

				$all_data_array = array();
				foreach($sql_result as $row)
				{
					$all_prod_ids.=$row[csf('product_id')].",";
					$all_store_ids.=$row[csf('store_name')].",";
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['id'] = $row[csf('id')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
					$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
				}

				$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
				if($all_prod_ids=="") $all_prod_ids=0;
				$all_store_ids=implode(",",array_unique(explode(",",chop($all_store_ids,","))));
				if($all_store_ids=="") $all_store_ids=0;

				$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.store_id in($all_store_ids) and a.entry_form in (4,20) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
				$rec_sql_result= sql_select($rec_sql);
				foreach($rec_sql_result as $row)
				{
					$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
					$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
					$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
				}

				$lastThreeMonthDataArr = array();
				
				if($db_type==0)
				{
					$lastThreeMonthData= sql_select("select sum(a.cons_quantity) as qnty , b.id as product_id from  inv_transaction a,  product_details_master b where a.prod_id=b.id and a.status_active=1 and b.status_active=1 and a.prod_id in($all_prod_ids) and a.store_id in($all_store_ids) and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= DATE_ADD(CURDATE(), INTERVAL -3 MONTH) and a.transaction_type in (2,3,6) group by b.id order by b.id ");
				}
				else
				{
					$lastThreeMonthData= sql_select("select sum(a.cons_quantity) as qnty , b.id as product_id from  inv_transaction a,  product_details_master b where a.prod_id=b.id and a.prod_id in($all_prod_ids) and a.store_id in($all_store_ids) and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= add_months(sysdate,-3) and a.transaction_type in (2,3,6) group by b.id order by b.id ");
				}
				foreach ($lastThreeMonthData as $ldata) {
					$lastThreeMonthDataArr[$ldata[csf("product_id")]] = $ldata[csf("qnty")];
				}
				//SQL> SELECT  hire_date, TO_CHAR(ADD_MONTHS(hire_date, -1), 'DD-MON-YYYY') "Previous month",
				//TO_CHAR(ADD_MONTHS(hire_date, 1), 'DD-MON-YYYY') "Next month"
				//FROM employees 
				//WHERE first_name = 'Lex';
				$i=1;
				// echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";

				//echo "<pre>";
				// print_r($all_data_array);
				foreach ($all_data_array as $cons_uom_id => $cons_uom_data) {
					$total_amount=0;$last_qnty=0;$total_requisition=0;$total_stock=0;
					foreach ($cons_uom_data as $dtls_id => $row) {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$quantity=$row['quantity'];
						$quantity_sum += $quantity;
						$amount=$row['amount'];
						$amount_sum += $amount;

						$current_stock=$row['stock'];
						$current_stock_sum += $current_stock;
						if($db_type==2)
						{
							$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
						}
						if($db_type==0)
						{
							$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
						}
						$last_req_info=explode('_',$last_req_info);
						//print_r($dataaa);

						if ($data[4]==1)
						{
							$item_code=$row['item_account'];
						}
						else
						{
							$item_account=explode('-',$row['item_account']);
							$item_code=$item_account[3];
						}
						/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
						$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
						$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
						$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
						$last_rec_rate=$receive_array[$row['product_id']]['rate'];

						if($i==25 || $i == 56 || $i == 87 || $i == 115 || $i == 149){ //add menual page break after these number of rows.
							$pagebreak = " class='page_break'";
						} else{ 
							$pagebreak = "";
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:20px" <?= $pagebreak; ?>>
							<td width="30" class="ver_align"><? echo $i; ?></td>
							<td width="120" class="wrd_brk ver_align" ><? echo $item_category[$row['item_category_id']];  ?></td>
							<td width="50" class="wrd_brk ver_align"><? echo $item_code; ?></td>
							<td width="140" class="wrd_brk ver_align"><? echo $item_name_arr[$row['item_group_id']]; ?></td>
							<td width="170" class="wrd_brk ver_align">
							<?
							echo $row['item_description'];
							if($row['item_description']!="" && $row['item_size']!="") echo ', ';
							echo $row['item_size'];
							?></td>
							<td width="70" class="wrd_brk ver_align"><? echo $use_for[$row['required_for']]; ?></td>
							<td width="50" class="wrd_brk ver_align"><? echo $unit_of_measurement[$row['cons_uom']]; ?></td>
							<td width="60" class="wrd_brk ver_align" align="right"><? echo $row['quantity']; ?></td>
							<?
							if ($data[4]==4)
							{
								?>
								<td width="60" class="wrd_brk ver_align" align="right"><? echo $row['rate']; ?></td>
								<td width="70" class="wrd_brk ver_align" align="right"><? echo $row['amount']; ?></td>
								<?
							}

							if ($data[4]==2 && $pay_cash==4)
							{
								?>
								<td width="50" class="wrd_brk ver_align" align="right"><? echo $row['rate']; ?></td>
								<td width="70" class="wrd_brk ver_align" align="right"><? echo $row['amount']; ?></td>
								<?
							}
							?>

							<td width="70" class="wrd_brk ver_align" align="right"><? echo number_format($row['stock'],2); ?></td>
							<td width="100" class="wrd_brk ver_align" align="center"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></td>
							<td width="60" class="wrd_brk ver_align" align="right"><? echo number_format($last_rec_qty,0,'',','); ?></td>
							<td width="110" class="wrd_brk ver_align" align="right" placeholder='<? echo $row["product_id"];?>'><? echo number_format(($lastThreeMonthDataArr[$row["product_id"]]/3),2);?></td>
							<td width="100" class="wrd_brk ver_align" align="right"><? echo $last_rec_rate;//$last_req_info[2]; ?></td>
							<td width="90" class="wrd_brk ver_align" align="center">
							<?
							if(trim($last_req_info[0])!="0000-00-00" && trim($last_req_info[0])!="") echo change_date_format($last_req_info[0]).'<br>'; else echo "&nbsp;<br>";
							echo $last_req_info[1];
							?>
							</td>
							<td width="120" class="wrd_brk ver_align"><? echo $row['remarks']; ?></td>
						</tr>
						<?
						$last_qnty += $last_rec_qty;
						$total_amount += $row['amount'];
						$total_requisition += $row['quantity'];
						$total_stock += $row['stock'];
						$i++;
					}
					?>
					<tr bgcolor="#dddddd">
						<td style="font-size:15px" align="right" colspan="7"><strong>Sub Total : </strong></td>
						<td align="right"><? echo number_format($total_requisition,4); ?></td>
						<?
						if ($data[4]==4)
						{
							?>
							<td align="right"></td>
							<td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
							<?
						}
						if ($data[4]==2 && $pay_cash==4)
						{
							?>
							<td align="right" ><? //echo number_format($current_stock_sum,0,'',','); ?></td>
							<td align="right" ><? echo number_format($total_amount,0,'',','); ?></td>
							<?
						}
						?>
						<td align="right" ><? echo number_format($total_stock,2); ?></td>
						<td align="right" ></td>
						<td align="right"><? echo number_format($last_qnty,0,'',','); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				<?
				$Grand_tot_total_amount += $total_amount;
				$Grand_tot_last_qnty += $last_qnty;
				$Grand_tot_total_stock += $total_stock;
				}
				?>
				</tbody>
				<tfoot>
				<tr bgcolor="#B0C4DE">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td style="font-size:15px" align="right"><strong>Total : </strong></td>
					<?
					if ($data[4]==4)
					{
						?>
						<td align="right"></td>
						<td align="right"><? echo number_format($Grand_tot_total_amount,0,'',','); ?></td>
						<?
					}
					if ($data[4]==2 && $pay_cash==4)
					{
						?>
						<td align="right" ><? //echo number_format($current_stock_sum,0,'',','); ?></td>
						<td align="right" ><? echo number_format($Grand_tot_total_amount,0,'',','); ?></td>
						<?
					}
					?>
					<td align="right" ><? echo number_format($Grand_tot_total_stock,2); ?></td>
					<td align="right" ></td>
					<td align="right"><? echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				</tfoot>
			</table>
			<br>
            <div style="margin-top:15px">
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:1050px;text-align:center;" rules="all">
                    <label style="font-size:16px">PR Raised By </label>
                    <thead bgcolor="#dddddd">
                    <tr style="font-weight:bold">
                        <th style="font-size:15px" width="20">SL</th>
                        <th style="font-size:15px" width="250">Name</th>
                        <th style="font-size:15px" width="200">Position</th>
                    </tr>
                    </thead>
                    <tr>
                        <td width="20"><? echo "1"; ?></td>
                        <td width="250"><? echo $user_name; ?></td>
                        <td width="200"><? echo $user_designation; ?></td>
                    </tr>
                </table>
            </div>
			<?

			//approved status
			/*$data_array_approve=sql_select("SELECT b.approved_by,b.approved_no, b.approved_date, c.user_full_name, c.designation, b.un_approved_by from inv_purchase_requisition_mst a, approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and b.entry_form=1 and a.id='$data[1]' order by b.id asc");*/

			$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
			$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=1 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

			/*$approved_sql=sql_select("SELECT  mst_id, approved_by,sequence_no ,min(approved_date) as approved_date from approval_history where entry_form=1 AND  mst_id ='$data[1]' group by mst_id, approved_by,sequence_no order by sequence_no");

			$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]' ");*/

			$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
				$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
			}
			/*echo "<pre>";
			print_r($unapproved_request_arr);*/
			foreach ($approved_his_sql as $key => $row)
			{
				$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
				if ($row[csf('un_approved_date')]!='')
				{
					$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
					$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
				}
			}
			/*echo "<pre>";
			print_r($array_data);*/

			$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
			$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
			?>
			<? if(count($approved_sql) > 0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
						<label style="font-size:16px">Purchase Requisition Approval Status </label>
						<thead>
							<tr style="font-weight:bold">
								<th style="font-size:16px" width="20">SL</th>
								<th style="font-size:16px" width="250">Name</th>
								<th style="font-size:16px" width="200">Designation</th>
								<th style="font-size:16px" width="100">Approval Date</th>
							</tr>
						</thead>
						<? foreach ($approved_sql as $key => $value)
						{
							?>
							<tr>
								<td width="20"><? echo $sl; ?></td>
								<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
							</tr>
							<?
							$sl++;
						}
						?>
					</table>
				</div>
				<?
			}
			?>



			<? if(count($approved_his_sql) > 0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
						<label style="font-size:16px">Purchase Requisition Approval / Un-Approval History </label>
						<thead>
							<tr style="font-weight:bold">
								<th style="font-size:16px" width="20">SL</th>
								<th style="font-size:16px" width="150">Approved / Un-Approved</th>
								<th style="font-size:16px" width="150">Designation</th>
								<th style="font-size:16px" width="50">Approval Status</th>
								<th style="font-size:16px" width="150">Reason for Un-Approval</th>
								<th style="font-size:16px" width="150">Date</th>
							</tr>
						</thead>
						<? foreach ($approved_his_sql as $key => $value)
						{
							if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
								<td  width="20"><? echo $sl; ?></td>
								<td  width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td  width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td  width="50">Yes</td>
								<td  width="150"><? echo $unapproved_request_arr[$value[csf("mst_id")]]; ?></td>
								<td  width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

								// echo change_date_format($approved_date[0])." ".$approved_date[1];
								echo $value[csf("approved_date")];
								
								?></td>
							</tr>
							<?
							$sl++;
							$un_approved_date= explode(" ",$value[csf('un_approved_date')]);
							$un_approved_date=$un_approved_date[0];
							if($db_type==0) //Mysql
							{
								if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
							}

							else
							{
								if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
							}

							if($un_approved_date!="")
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td  width="20"><? echo $sl; ?></td>
									<td  width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td  width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td  width="50">No</td>
									<td  width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td  width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
							}
						}
						?>
					</table>
				</div>
				<?
			}
			?>
			<!-- //approved status end sumon-->
			<br>
			<div id="signature_bottom">
			<?

				$report_width= $cash+1050;
				echo signature_table(25, $data[0], $report_width."px",$cbo_template_id,40,$user_lib_name[$inserted_by]); 
			?>
			</div>
			</div>
			<?
		}
		else
		{
			
			if($data[5]==1)
			{
				$display_col="";
				$width_col=150;
				$span=1;
			}
			else
			{
				$display_col="display:none";
				$width_col='';
				$span='';
			}

			?>
			<table width="970" style=" margin-right:20px;">
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					
					
					<img src='<? echo $path.$comp_logo;?>' height='50' width='50' align="middle" />
					</td>
					<td colspan="5" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
					$req=explode('-',$dataArray[0][csf('requ_no')]);
					?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:18px"><strong><u>Store <? echo $data[2] ?></u></strong></td>
				</tr>
				
				<tr>
					<td width="120"><strong>Req. No:</strong></td><td width="175"><? echo $req[2].'-'.$req[3]; ?></td>
					<td width="130"><strong>Item Catg:</strong></td> <td width="175"><? echo $item_category[$dataArray[0][csf('item_category_id')]]; ?></td>
					<td width="125"><strong>Source:</strong></td><td width="175"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Manual Req.:</strong></td> <td ><? echo $dataArray[0][csf('manual_req')]; ?></td>
					<td><strong>Department:</strong></td><td ><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
					<td><strong>Section:</strong></td><td ><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Req. Date:</strong></td><td><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
					<td><strong>Store Name:</strong></td><td><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
					<td><strong>Pay Mode:</strong></td><td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Location:</strong></td> <td><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
					<td><strong>Currency:</strong></td> <td><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
					<td><strong>Del. Date:</strong></td><td><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
				</tr>
				<tr>
					<td><strong>Remarks:</strong></td> <td><? echo $dataArray[0][csf('remarks')]; ?></td>
					<td></td> <td></td>
					<td></td><td></td>
				</tr>
			</table>
			<br>
			<table width="<? echo $width_col+970; ?>" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" style="height:680px;">
				<thead>
					<tr>
						<th colspan="<? echo $span+12; ?>" align="center" >Item Details</th>
						<th width="70" align="center" style="font-size:11px" rowspan="2">Avg. Monthly issue</th>
						<th rowspan="2" style="font-size:12px">Avg. Monthly Rec.</th>
					</tr>
					<tr style="font-size:12px">
						<th width="30">SL</th>
						<th width="50">Item Code</th>
						<th width="100" style=" <? echo $display_col; ?> ">Item Group</th>
						<th width="180">Item Des.</th>
						<th width="70">Req. For</th>
						<th width="50">UOM</th>
						<th width="60">Req. Qty.</th>
						<th width="50">Rate</th>
						<th width="70">Amount</th>
						<th width="70">Stock</th>
						<th width="70">Last Rec. Date</th>
						<th width="70">Last Rec. Qty.</th>
						<th width="50">Last Rate</th>
					</tr>
				</thead>
				<tbody>
				<?
					$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
					$receive_array=array();
					/* $rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
					$rec_sql_result= sql_select($rec_sql);
					foreach($rec_sql_result as $row)
					{
						$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
						$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
					}*/
					$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form in (4,20) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
					$rec_sql_result= sql_select($rec_sql);
					foreach($rec_sql_result as $row)
					{
						$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
						$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
						$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
					}
					if($db_type==2)
					{
						$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
					}
					elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

					//echo "select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id";die;
					$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
					$prev_issue_data=array();
					foreach($issue_sql as $row)
					{
						$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
						$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
						$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
					}

					//var_dump($prev_issue_data);die;

					$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
					$prev_receive_data=array();
					foreach($receive_sql as $row)
					{
						$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
						$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
						$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
					}

					$i=1;

					// echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
					$sql_result= sql_select(" select a.id, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label, c.item_code
					from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
					where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  ");
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$quantity=$row[csf('quantity')];
						$quantity_sum += $quantity;
						$amount=$row[csf('amount')];
						$amount_sum += $amount;

						$current_stock=$row[csf('stock')];
						$current_stock_sum += $current_stock;
						if($db_type==2)
						{
							$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
						}
						if($db_type==0)
						{
							$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
						}
						$last_req_info=explode('_',$last_req_info);
						//print_r($dataaa);
						$last_rec_qty=0;
						$item_code=$row[csf('item_code')];
						/*if ($data[4]==1)
						{
							$item_code=$row[csf('item_account')];
						}
						else
						{
							$item_account=explode('-',$row[csf('item_account')]);
							$item_code=$item_account[3];
						}*/
						/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
						$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
						$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
						$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
						$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
						
						if($i==25 || $i == 56 || $i == 87 || $i == 115 || $i == 149){ //add menual page break after these number of rows.
							$pagebreak = " class='page_break'";
						} else{ 
							$pagebreak = "";
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" <?= $pagebreak; ?>>
							<td class="ver_align"><? echo $i; ?></td>
							<td width="50" class="ver_align"><? echo $item_code; ?></td>
							<td width="100" class="wrd_brk ver_align" style=" <? echo $display_col; ?> "><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
							<td width="180" class="wrd_brk ver_align">
							<?
							echo $row[csf('item_description')];
							if($row[csf('item_description')]!="" && $row[csf('item_size')]!="") echo ', ';
							echo $row[csf('item_size')];
							?></td>
							<td width="70" class="wrd_brk ver_align"><? echo $row[csf('required_for')]; ?></td>
							<td width="50" class="wrd_brk ver_align"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
							<td width="60" class="wrd_brk ver_align" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
							<td width="50" class="wrd_brk ver_align" align="right"><? echo number_format($row[csf('rate')],2); ?></td>
							<td width="70" class="wrd_brk ver_align" align="right"><? echo number_format($row[csf('amount')],2); ?></td>
							<td width="70" class="wrd_brk ver_align" align="right"><? echo number_format($row[csf('stock')],2); ?></td>
							<td><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></td>
							<td width="70" class="wrd_brk ver_align" align="right"><? echo number_format($last_rec_qty,2); ?></td>
							<td width="70" class="wrd_brk ver_align" align="right"><? echo $last_rec_rate;//$last_req_info[2]; ?></td>
							<td width="50" class="wrd_brk ver_align" align="right">
							<?
							$min_issue_date=$prev_issue_data[$row[csf("product_id")]]["transaction_date"];
							$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
							$year_issue_total=$prev_issue_data[$row[csf("product_id")]]["isssue_qty"];
							$issue_avg=$year_issue_total/$month_issue_diff;
							echo number_format($issue_avg,2);
							//echo $row[csf("product_id")];
							?>
							</td>
							<td width="70" class="wrd_brk ver_align" align="right">
							<?
							$min_receive_date=$prev_receive_data[$row[csf("product_id")]]["transaction_date"];
							$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
							$year_receive_total=$prev_receive_data[$row[csf("product_id")]]["receive_qty"];
							$receive_avg=$year_receive_total/$month_receive_diff;
							echo number_format($receive_avg,2);
							?>
							</td>
						</tr>
						<?
						$total_last_qnty +=$last_rec_qty;
						$total_req_qnty+=$row[csf('quantity')];
						$total_amount+=$row[csf('amount')];
						$total_stock+=$row[csf('stock')];

						$i++;
					}
					$currency_id=$dataArray[0][csf('cbo_currency')];
					$mcurrency="";
					$dcurrency="";
					if($currency_id==1)
					{
						$mcurrency='Taka';
						$dcurrency='Paisa';
					}
					if($currency_id==2)

					{
						$mcurrency='USD';
						$dcurrency='CENTS';
					}
					if($currency_id==3)
					{
						$mcurrency='EURO';
						$dcurrency='CENTS';
					}
				?>
				
				</tbody>
				<tfoot>
					<tr>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th style=" <? echo $display_col; ?> ">&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
						<th >Total:</th>
						<th ><? echo number_format($total_req_qnty,0,'',','); ?></th>
						<th >&nbsp;</th>
						<th ><? echo number_format($total_amount,2);?></th>
						<th ><? echo number_format($total_stock,2);?></th>
						<th>&nbsp;</th>
						<th ><? echo number_format($total_last_qnty,2);?></th>
						<th>&nbsp;</th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
					</tr>
					<tr>
						<th colspan="<? echo 14+$span; ?>"  style="border:1px solid black; text-align: center">
							Total Amount (In Word): <? echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency); ?>
						</th>
					</tr>
				</tfoot>


			</table>
			<br>
			<?

				$approved_sql=sql_select("SELECT  mst_id, approved_by ,approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]' and un_approved_date is null ");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]' ");
				//$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
			?>
			<? if(count($approved_sql) > 0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
						<label><b>Purchase Requisition Approval Status </b></label>
						<thead>
							<tr style="font-weight:bold">
								<th width="20">SL</th>
								<th width="250">Name</th>
								<th width="200">Designation</th>
								<th width="100">Approval Date</th>
							</tr>
						</thead>
						<? foreach ($approved_sql as $key => $value)
						{
							?>
							<tr>
								<td width="20"><? echo $sl; ?></td>
								<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
							</tr>
							<?
							$sl++;
						}
						?>
					</table>
				</div>
				<?
			}
			?>
			<? if(count($approved_his_sql) > 0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
						<label><b>Purchase Requisition Approval / Un-Approval History </b></label>
						<thead>
							<tr style="font-weight:bold">
								<th width="20">SL</th>
								<th width="150">Approved / Un-Approved</th>
								<th width="150">Designation</th>
								<th width="50">Approval Status</th>
								<th width="150">Reason for Un-Approval</th>
								<th width="150">Date</th>
							</tr>
						</thead>
						<tbody>
						<? foreach ($approved_his_sql as $key => $value)
						{
							if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
								<td width="20"><? echo $sl; ?></td>
								<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="50"><? echo empty($value[csf("un_approved_date")]) ? "Yes" : "No";  ?></td>
								<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
								<td width="150"><? echo $value[csf("approved_date")]; ?></td>
							</tr>
							<?
							$sl++;
						}
						?>
						</tbody>
					</table>
				</div>
				<?
			}
			?>
			<!-- //approved status end sumon-->
			<div id="signature_bottom">
			<?
			if($data[5]==1) $rpt_width=150+970; else  $rpt_width=970;

			echo signature_table(25, $data[0], $rpt_width."px",$cbo_template_id,20,$user_lib_name[$inserted_by]); 
			?>
			</div>
			</div>
		<?
		}
	exit();
}

if($action=="purchase_requisition_print_2") // Print Report 3
{
  ?>
	<style type="text/css">

		table,tr, td { 
			
				padding: 9px;
			 }
		
	</style>
	<link rel="stylesheet" href="../css/style_common.css" type="text/css" />
	<?
	extract($_REQUEST);
	$data=explode('*',$data);
	//print($data[5]);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req,division_id, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,inserted_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');

	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<table width="1200">
		<tr class="form_caption">
			<?
			//echo $data[0].'==';
			$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
			?>
			<td  align="left" rowspan="2">
			<?
			foreach($data_array as $img_row)
			{
				if ($formate_id==122)
				{
					?>
					<img src='../../<? echo $com_dtls[2]; ?>' height='70' width='100' align="middle" />
					<?
				}
				else
				{
					?>
					<img src='../<? echo $com_dtls[2]; ?>' height='70' width='100' align="middle" />
					<?
				}
			}
			?>
			</td>
			<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $com_dtls[0]; ?></strong></td>
		</tr>
		<tr class="form_caption">
			<td colspan="5" align="center" style="font-size:18px;">
			<?
			echo $com_dtls[1]; //Aziz
			//$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			//foreach ($nameArray as $result)
			//{
				// if($result[csf('plot_no')]) echo "Plot No: ".$result[csf('plot_no')];
				// if($result[csf('road_no')]) echo "Road No: ".$result[csf('road_no')];
				// if($result[csf('block_no')]) echo "Block No: ".$result[csf('block_no')];
				// if($result[csf('city')]) echo "City No: ".$result[csf('city')];
				// if($result[csf('zip_code')]) echo "Zip Code: ".$result[csf('zip_code')]."<br>";
				// if($result[csf('country_id')]) echo "Country: ".$result[csf('country_id')];
				// if($result[csf('email')]) echo "Email Address: ".$result[csf('email')];
				// if($result[csf('website')]) echo "Website No: ".$result[csf('website')];
			//}
			$req=explode('-',$dataArray[0][csf('requ_no')]);
			//?>
			</td>
		</tr>
        <tr>
            <td>&nbsp; </td>
            <td colspan="5" align="center" style="font-size:22px"><strong><u><? echo $data[2] ?></u></strong></td>
		</tr>
		<tr>
			<td width="120" style="font-size:16px"><strong>Req. No:</strong></td>
			<td width="175" style="font-size:16px"><? echo $dataArray[0][csf('requ_no')];?></td>
			<td style="font-size:16px;" width="130"><strong>Req. Date:</strong></td><td style="font-size:16px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Manual Req.:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
			<td style="font-size:16px"><strong>Department:</strong></td><td style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
			<td style="font-size:16px"><strong>Section:</strong></td><td style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
		</tr>
		<tr>
			 <td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
			<td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
			<td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
			<td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
			<td style="font-size:16px"><strong>Remarks:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
		</tr>
	</table>
	<br>
	<table cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="30">SL</th>
				<th width="50">Item Code</th>
				<th width="150">Item Description</th>
				<th width="80">Brand</th>
                <th width="80">Origin</th>
                <th width="80">Model</th>
				<th width="60">UOM</th>
                <th width="70">Stock</th>
                <th width="70">Re-Order Level</th>
                <th width="70">Max Level</th>
				<th width="70">Req. Qty.</th>
				<th width="70">Rate</th>
				<th width="70">Amount</th>
				<th width="70">Last Rec. Date</th>
				<th width="150">Last Supplier</th>
				<th>Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= " select a.id,b.id as dtls_id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label, c.maximum_label
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by b.item_category, c.item_group_id";
	    //echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$all_prod_ids.=$row[csf('product_id')].",";
			$all_data_array[$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			$all_data_array[$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
			$all_data_array[$row[csf('dtls_id')]]['maximum_label'] = $row[csf('maximum_label')];
		}

		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;
		/*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
		}*/

		$rec_sql="SELECT b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by b.prod_id, b.id";
		//echo $rec_sql;die;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		/*if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//var_dump($prev_issue_data);die;

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}*/


		// echo "<pre>";
		// print_r($all_data_array);

		$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
		$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
		foreach ($all_data_array as $dtls_id => $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$quantity=$row['quantity'];
			$quantity_sum += $quantity;
			$amount=$row['amount'];
			//test
			$sub_group_name=$row['sub_group_name'];
			$amount_sum += $amount;

			$current_stock=$row['stock'];
			$current_stock_sum += $current_stock;
			if($db_type==2)
			{
				$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
			}
			if($db_type==0)
			{
				$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
			}
			$last_req_info=explode('_',$last_req_info);
			//print_r($dataaa);

			$item_account=explode('-',$row['item_account']);
			$item_code=$item_account[3];
			/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
			$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
			$last_rec_supp=$receive_array[$row['product_id']]['supplier_id'];


			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:20px">
				<td align="center"><? echo $i; ?></td>
                <td><div style="word-wrap:break-word:50px;"><? echo $row['item_code']; ?></div></td>
                <td><p> <? echo $row["item_description"];?> </p></td>
                <td><p> <? echo $row["brand_name"];?>&nbsp;</p></td>
                <td><p><? echo $country_arr[$row["origin"]];?>&nbsp;</p></td>
                <td><p><? echo $row["model"];?>&nbsp;</p></td>
                <td align="center"><p><? echo $unit_of_measurement[$row["cons_uom"]]; ?></p></td>
                <td align="right"><? echo number_format($row['stock'],2); ?></td>
                <td align="right"><? echo $row['re_order_label']; ?></td>
                <td align="right"><? echo $row['maximum_label']; ?></td>
                <td align="right"><? echo $row['quantity']; ?></td>
				<td align="right"><? echo $row['rate']; ?></td>
				<td align="right"><? echo $row['amount']; ?></td>
                <td align="center"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
                <td><p><? echo $supplier_array[$last_rec_supp]; ?>&nbsp;</p></td>
                <td><? echo $row['remarks']; ?></td>
			</tr>
			<?
			$total_req_qnty+= $row['quantity'];
			$total_req_amt+= $row['amount'];
			$total_stock += $row['stock'];
			$i++;
		}
		?>
		</tbody>
		<tr bgcolor="#B0C4DE">
			<td align="right" colspan="10"><strong>Total : </strong></td>
			<td align="right"><? echo number_format($total_req_qnty,0,'',','); ?></td>
            <td align="right" ></td>
			<td align="right" ><? echo number_format($total_req_amt,0,'',','); ?></td>
            <td align="right" ></td>
            <td align="right" ></td>
            <td align="right" ></td>
		</tr>
	</table>
	<span><strong>Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($total_req_amt,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span>
	<br>
	<?
	echo signature_table(25, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	exit();
}

if($action=="purchase_requisition_print_15") // Print Report 15
{
	?>
	<!-- <link rel="stylesheet" href="../css/style_common.css" type="text/css" /> -->
	<?
	extract($_REQUEST);
	$data=explode('*',$data);

	$page = $data[8];
	$linkPrefix = '';

	if($page == 'purchase_recap_report3') {
		$linkPrefix = '../';
	}

	echo load_html_head_contents("Report Info","$linkPrefix../", 1, 1, $unicode,'','');
	//print($data[5]);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,inserted_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$origin_lib=return_library_array( "select SHORT_NAME,id from lib_country where is_deleted=0  and status_active=1 order by SHORT_NAME", "id", "SHORT_NAME"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');

	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$com_dtls = fnc_company_location_address($company, $location, 2);
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
	?>
	<table align="center">
        
		<tr>
            <td colspan="6" align="center">
            	<table align="center">
                    <tr class="form_caption">
                        <td  align="left" rowspan="2">
                        <?
                        foreach($data_array as $img_row)
                        {
                            if ($formate_id==122) 
                            {
                                ?>
                                <img src='<? echo $linkPrefix; ?>../../<? echo $com_dtls[2]; ?>' height='70' align="middle" />
                                <?
                            }
                            else
                            {
                                ?>
                                <img src='<? echo $linkPrefix; ?>../<? echo $com_dtls[2]; ?>' height='70' align="middle" />
                                <?
                            }
                        }
                        ?>
                        </td>
                        <td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $com_dtls[0]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="5" align="center" style="font-size:18px;">
                        	<? echo $com_dtls[1]; $req=explode('-',$dataArray[0][csf('requ_no')]);?>
                        </td>
                    </tr>
            	</table>
            </td>
		</tr>
		<tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><u><? echo $data[2] ?></u></strong></td>
		</tr>
		<tr>
			<td width="100" style="font-size:16px"><strong>Req. No</strong></td>
			<td width="300" style="font-size:16px">: <? echo $dataArray[0][csf('requ_no')];?></td>
			<td style="font-size:16px;" width="80"><strong>Req. Date</strong></td>
            <td style="font-size:16px;" width="300">: <? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td width="80" style="font-size:16px"><strong>Source</strong></td>
            <td style="font-size:16px">: <? echo $source[$dataArray[0][csf('source')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Manual Req.</strong></td> 
            <td style="font-size:16px">: <? echo $dataArray[0][csf('manual_req')]; ?></td>
			<td style="font-size:16px"><strong>Department</strong></td>
            <td style="font-size:16px">: <? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
			<td style="font-size:16px"><strong>Section</strong></td>
            <td style="font-size:16px">: <? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
		</tr>
		<tr>
			 <td style="font-size:16px"><strong>Del. Date</strong></td>
             <td style="font-size:16px">: <? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
			<td style="font-size:16px"><strong>Store Name</strong></td>
            <td style="font-size:16px">: <? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
			<td style="font-size:16px"><strong>Pay Mode</strong></td>
            <td style="font-size:16px">: <? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Location</strong></td> 
            <td style="font-size:16px">: <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
			<td style="font-size:16px"><strong>Currency</strong></td> 
            <td style="font-size:16px">: <? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
		   <td style="font-size:16px"><strong>Remarks</strong></td> 
           <td style="font-size:16px">:<? echo $dataArray[0][csf('remarks')]; ?></td>
		</tr>
	</table>

	<table cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="30">SL</th>
				<th width="80">Item Code</th>
                <th width="80">Item Number</th>
                
				<th width="220">Item Description</th>
				<th width="80">Brand</th>
                <th width="30">Origin</th>
				<th width="30">UOM</th>
                <th width="30">Req. Qty.</th>
				<th width="40">Rate</th>
				<th width="60">Amount</th>
                <th width="60">Stock</th>
                <th width="60">Re-Order Level</th>
                <th width="30">Max Level</th>
				<th width="60">Last Rec. Date</th>
                <th width="50">Last Rec. Qnty</th>
                <th width="50">Last Rec. Rate</th>
				<th width="150">Last Supplier</th>
				<th>Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= " SELECT a.id,b.id as dtls_id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label, c.maximum_label, c.sub_group_code, c.sub_group_name,c.item_number 
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by b.item_category, c.item_group_id";
	    //echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$all_prod_ids.=$row[csf('product_id')].",";
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['sub_group_code'] = $row[csf('sub_group_code')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
			$all_data_array[$row[csf('sub_group_name')]][$row[csf('dtls_id')]]['maximum_label'] = $row[csf('maximum_label')];
		}

		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;

		$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id in($all_prod_ids) and a.receive_basis not in(6) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by b.prod_id, b.id";
		//echo $rec_sql;die;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		$total_amount=0;$last_qnty=0;$total_reqsit_value=0; $last_rec_qty_total=0;
		$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
		foreach ($all_data_array as $sub_group_name => $group_name)
		{
			
			?>
			<tr  bgcolor="#B0C4DE">
				<td colspan="19"><strong>Sub Group Name:</strong> <?
				echo $sub_group_name
				?></td>
			</tr>
			<?
			foreach ($group_name as $dtls_id => $row)
			{
			
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row['quantity'];
				$quantity_sum += $quantity;
				$amount=$row['amount'];
				//$sub_group_name=$row['sub_group_name'];
				$amount_sum += $amount;

				$current_stock=$row['stock'];
				$current_stock_sum += $current_stock;
				$item_account=explode('-',$row['item_account']);
				$item_code=$item_account[3];
				$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
				$last_rec_supp=$receive_array[$row['product_id']]['supplier_id'];
				$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
				$last_rec_rate=$receive_array[$row['product_id']]['rate'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:20px">
					<td align="center"><div style="font-size:13px"><? echo $i; ?></div></td>
	                <td><div style="font-size:13px"><? echo $row['item_code']; ?></div></td>
	                <td><div style="font-size:13px"><? echo $row['item_number']; ?></div></td>
	                
	                <td><div style="word-wrap:break-word:50px;font-size:13px"><? echo $row["item_description"];?> </div></td>
	                <td><div style="font-size:13px"><? echo $row["brand_name"];?></div></td>
	                <td align="center"><div style="font-size:13px"><? echo $origin_lib[$row["origin"]]."<br>";?></div></td>
	                <td align="center"><div style="font-size:13px"><? echo $unit_of_measurement[$row["cons_uom"]]; ?></div></td>
	                <td align="right"><div style="font-size:13px"><? echo $row['quantity']; ?></div></td>
					<td align="right"><div style="font-size:13px"><? echo $row['rate']; ?></div></td>
					<td align="right"><div style="font-size:13px"><? echo $row['amount']; ?></div></td>
	                <td align="right"><div style="font-size:13px"><? echo number_format($row['stock'],2); ?></div></td>
	                <td align="right"><div style="font-size:13px"><? echo $row['re_order_label']; ?></div></td>
	                <td align="right"><div style="font-size:13px"><? echo $row['maximum_label']; ?></div></td>
	                <td align="center"><div style="font-size:13px"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></div></td>
	                <td align="right"><div style="font-size:13px"><? echo number_format($last_rec_qty,0,'',','); ?></div></td>
	                <td align="right"><div style="font-size:13px"><? echo $last_rec_rate;?></div></td>
	                <td><div style="word-wrap:break-word:50px;font-size:13px"><? echo $supplier_array[$last_rec_supp]; ?></div></td>
	                <td align="right"><div style="font-size:13px"><? echo $row['remarks']; ?></div></td>
				</tr>
				<?
				$total_req_qnty+= $row['quantity'];
				$total_req_amt+= $row['amount'];
				$total_stock += $row['stock'];
				$last_rec_qty_total += $last_rec_qty;
				$i++;
			}
		}
		?>
		</tbody>
		<tr bgcolor="#B0C4DE">
			<td align="right" colspan="7"><strong>Total : </strong></td>
			<td align="right"><? echo number_format($total_req_qnty,0,'',','); ?></td>
            <td align="right" ></td>
			<td align="right" ><? echo number_format($total_req_amt,0,'',','); ?></td>
            <td align="right" ><? echo number_format($total_stock,0,'',','); ?></td>
            <td align="right" ></td>
            <td align="right" ></td>
            <td align="right" ></td>
            <td align="right" ><? echo number_format($last_rec_qty_total,0,'',','); ?></td>
            <td align="right" ></td>
            <td align="right" ></td>
            <td align="right" ></td>
		</tr>
	</table>
	<span><strong>Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($total_req_amt,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span>
	<br>
	<?
	echo signature_table(25, $data[0], "1350px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	exit();
}

if($action=="purchase_requisition_print_16")
{// echo 1;
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	 //print($data[5]);
	 /*print_r($data);die;*/
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	//print_r($cbo_template_id);
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency,brand_name,model, remarks,req_by from inv_purchase_requisition_mst where id=$update_id";

	$dataArray=sql_select($sql);
	$requisition_date=$dataArray[0][csf("requisition_date")];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
	//echo $requisition_date."==".$requisition_date_last_year;die;
	
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

	$pay_cash=$dataArray[0][csf('pay_mode')];
	?>

  	<style type="text/css">
  		@media print
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
  			border: 1px solid #dccdcd !important;
  		}
  		}
  	</style>
	<div id="table_row" style="max-width:1020px; margin: 0 auto;">

		<table width="1000" class="rpt_tables">
			<tr class="form_caption">
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left" rowspan="2">
				<?
				foreach($data_array as $img_row)
				{
					if ($formate_id==123) 
					{
						?>
						<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
						<?
					}
					else
					{
						?>
						<img src='../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
						<?
					}
				}
				?>
				</td>


				<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<td colspan="5" align="center" style="font-size:18px;">
				<?

				//echo show_company($data[0],'',''); //Aziz
				$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
				foreach ($nameArray as $result)
				{
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')];?>
					City No: <? echo $result[csf('city')];?>
					Zip Code: <? echo $result[csf('zip_code')]; ?>
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
					Email Address: <? echo $result[csf('email')];?>
					Website No: <? echo $result[csf('website')];
				}
				$req=explode('-',$dataArray[0][csf('requ_no')]);
				?>

				</td>
			</tr>
			<tr>
				<td>&nbsp; </td>
				<td colspan="5" align="center" style="font-size:22px"><strong><u><? echo $data[2] ?></u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:16px"><strong>Req. No:</strong></td>
				<td width="175px" style="font-size:16px"><strong><? echo $dataArray[0][csf('requ_no')];
				//$req[2].'-'.$req[3]; ?></strong></td>
				<td style="font-size:16px;" width="130"><strong>Req. Date:</strong></td><td style="font-size:16px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
				<td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
				<td style="font-size:16px"><strong>Department:</strong></td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
				<td style="font-size:16px"><strong>Section:</strong></td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
			</tr>
			<tr>
				 <td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
				<td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
				<td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
				<td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
			   <td style="font-size:16px"><strong>Remarks:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Req. By:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('req_by')]; ?></td>
				<td colspan="4"></td>
			</tr>
		</table>
		<br>
		<?
		//$margin='-133px;';
		//echo $th_span.'='.$cash_span.'='.$span.'='.$margin.'='.$widths.'='.$cash;
		?>

		<table cellspacing="0" width="980"  border="0" rules="all" class="rpt_table rpt_tables" >
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<!-- <th width="980" align="center" ><strong>Item Details</strong></th> -->
					<th colspan="7" width="1160" align="center" ><strong>ITEM DETAILS</strong></th>
				</tr>
				<tr>
					<th width="20">SL</th>
					<th width="80">Item Group</th>
					<th width="180">Item Des & Item Size</th>
					<th width="120">Req. For</th>
					<th width="35">UOM</th>
					<th width="40">Req. Qty.</th>
					
					<th width="70">Remarks</th>

				</tr>
			</thead>
			<tbody>
			<?
			$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
			$receive_array=array();
			/*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
			$rec_sql_result= sql_select($rec_sql);
			foreach($rec_sql_result as $row)
			{
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
				//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			}*/
			
			$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate 
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and a.entry_form in (4,20) and a.receive_basis not in(6) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			order by  b.prod_id,b.id";
			/*echo $rec_sql;die;*/
			$rec_sql_result= sql_select($rec_sql);
			foreach($rec_sql_result as $row)
			{
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
				$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
			}

			if($db_type==2)
			{
				$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
			}
			elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

			$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_issue_data=array();
			foreach($issue_sql as $row)
			{
				$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
			}

			//var_dump($prev_issue_data);die;
			$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_receive_data=array();
			foreach($receive_sql as $row)
			{
				$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
			}

			$i=1; $k=1;
			// echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
			 $sql= " select a.id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0 order by b.item_category,c.item_group_id";
			$sql_result=sql_select($sql);
			/*echo $sql;die;*/
			$item_category_array=array();
			$category_wise_data = array();
			/*foreach ($sql_result as $row) {
				$category_wise_data[$row[csf("item_category")]]['item_group_id']=$row[csf("item_group_id")];
				$category_wise_data[$row[csf("item_category")]]['item_size']=$row[csf("item_size")];
				$category_wise_data[$row[csf("item_category")]]['item_description']=$row[csf("item_description")];
				$category_wise_data[$row[csf("item_category")]]['required_for']=$row[csf("required_for")];
				$category_wise_data[$row[csf("item_category")]]['cons_uom']=$row[csf("cons_uom")];
				$category_wise_data[$row[csf("item_category")]]['quantity']=$row[csf("quantity")];
				$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
				$category_wise_data[$row[csf("item_category")]]['amount']=$row[csf("amount")];
				$category_wise_data[$row[csf("item_category")]]['stock']=$row[csf("stock")];
				$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
				$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
			}*/
			foreach($sql_result as $row)
			{

				if (!in_array($row[csf("item_category")],$item_category_array) )
				{
					if($k!=1)
					{
						?>
						<tr bgcolor="#dddddd">
	                        <td align="right" colspan="7"><strong>Sub Total : </strong></td>
	                        <td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
	                        
	                    </tr>
						<tr bgcolor="#dddddd">
							<td colspan="19" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
						</tr>
						<?
						$total_amount=$total_stock=$total_last_rec_qty=$total_reqsit_value=$total_issue_avg=$total_receive_avg=0;
					}
					else
					{
						?>
						<tr bgcolor="#dddddd">
							<td colspan="19" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
						</tr>
						<?
					}
					$item_category_array[]=$row[csf('item_category')];
					$k++;
				}

				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row[csf('quantity')];
				$quantity_sum += $quantity;
				$amount=$row[csf('amount')];
				//test
				$sub_group_name=$row[csf('sub_group_name')];
				$amount_sum += $amount;
				$remarks=$row[csf('remarks')];
				$current_stock=$row[csf('stock')];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);

				$item_account=explode('-',$row[csf('item_account')]);

				$item_code=$item_account[3];
				/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
				$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
				$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];


				?>
				<tr style="margin: 0px;padding: 0px; font-size:20px" class="main_tbl" bgcolor="<? echo $bgcolor; ?>">
					<td  width="20" align="center"><? echo $i; ?></td>
	                <td  width="80"><p style="font-size: 13px"><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
					<td  width="180" ><p style="font-size: 13px"> <? echo $row[csf("item_description")].', '.$row[csf("item_size")];?> </p></td>
					<td width="120"><p style="font-size: 13px">  <? echo $use_for[$row[csf("required_for")]]; ?></p></td>
					<td width="35" align="center"><p style="font-size: 13px">  <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
					<td width="40" align="right"><p style="font-size: 13px"><? echo $row[csf('quantity')]; ?>&nbsp;</p></td>
					
					<td  width="70" align="left"><p style="font-size: 13px"><? echo $remarks; ?>&nbsp;</p></td>
				</tr>
				<?

				$total_amount+=$row[csf('amount')];
				$total_req_qty+=$row[csf('quantity')];
				$total_stock+=$row[csf('stock')];
				$total_last_rec_qty +=$last_rec_qty;
				$total_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
				$total_issue_avg +=$issue_avg;
				$total_receive_avg +=$receive_avg;

				$Grand_tot_total_amount+=$row[csf('amount')];
				$Grand_total_req_qty+=$row[csf('quantity')];
				$Grand_tot_total_stock+=$row[csf('stock')];
				$Grand_tot_last_qnty +=$last_rec_qty;
				$Grand_tot_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
				$Grand_tot_issue_avg +=$issue_avg;
				$Grand_tot_receive_avg +=$receive_avg;

				$i++;
			}
			?>
			</tbody>
			<tr bgcolor="#dddddd">
				<td align="right" colspan="5"><strong>Sub Total : </strong></td>
				<td align="right"><? echo number_format($total_req_qty,0,'',','); ?></td>
				<!-- <td align="right"><? echo number_format($total_stock,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($total_last_rec_qty,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
				<td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
				<td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td> -->
			</tr>

			<tr bgcolor="#dddddd">
				<td align="right" colspan="5"><strong>Grand Sub Total : </strong></td>
				<td align="right"><? echo number_format($Grand_total_req_qty,0,'',','); ?></td>
				<!-- <td align="right"><? echo number_format($Grand_tot_total_stock,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($Grand_tot_reqsit_value,0,'',','); ?></td>
				<td align="right"><? echo number_format($Grand_tot_issue_avg,0,'',','); ?></td>
				<td align="right"><? echo number_format($Grand_tot_receive_avg,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td> -->
			</tr>
			<tr bgcolor="#dddddd">
				<td align="right" colspan="3"><strong>Grand Total Amount in Word: </strong></td>
				<td align="left" colspan="4">&nbsp;<? echo number_to_words(number_format($Grand_total_req_qty,0,'',','))." Pcs"; ?></td>
			</tr>

		</table>
	</div>
	<?
	echo signature_table(25, $data[0], "1100px",$cbo_template_id);
	exit();
}

if($action=="purchase_requisition_print_3") // Print Report 5
{
	?>
	<!-- <link rel="stylesheet" href="../css/style_common.css" type="text/css" /> -->
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print($data[5]);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];
	$is_approved=$data[8];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,inserted_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<table width="1000">
		<tr class="form_caption">
		<?
			$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
			?>
			<td  align="left" rowspan="2">
			<?
			foreach($data_array as $img_row)
			{
				if ($formate_id==122)
				{
					?>
					<img src='../../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
				else
				{
					?>
					<img src='../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
			}
			?>
			</td>


			<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $com_dtls[0]; ?></strong></td>
		</tr>
		<tr class="form_caption">

			<td colspan="5" align="center" style="font-size:18px;">
			<?
				echo $com_dtls[1];
			//echo show_company($data[0],'',''); //Aziz
			/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			foreach ($nameArray as $result)
			{
				?>
				Plot No: <? echo $result[csf('plot_no')]; ?>
				Road No: <? echo $result[csf('road_no')]; ?>
				Block No: <? echo $result[csf('block_no')];?>
				City No: <? echo $result[csf('city')];?>
				Zip Code: <? echo $result[csf('zip_code')]; ?>
				Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
				Email Address: <? echo $result[csf('email')];?>
				Website No: <? echo $result[csf('website')];
			}*/
			$req=explode('-',$dataArray[0][csf('requ_no')]);
			
			?>

			</td>
		</tr>
		<tr>
            <td>&nbsp; </td>
            <td colspan="5" align="center" style="font-size:22px"><strong><u><? echo $data[2] ?></u></strong></td>
		</tr>
		<tr>
			<td width="120" style="font-size:16px"><strong>Req. No:</strong></td>
			<td width="175" style="font-size:16px"><? echo $dataArray[0][csf('requ_no')];?></td>
			<td style="font-size:16px;" width="130"><strong>Req. Date:</strong></td><td style="font-size:16px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
			<td style="font-size:16px"><strong>Department:</strong></td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
			<td style="font-size:16px"><strong>Section:</strong></td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
		</tr>
		<tr>
			 <td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
			<td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
			<td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
			<td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
		   <td style="font-size:16px"><strong>Remarks:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
		</tr>
	</table>
	<br>
	<style type="text/css">
		table thead tr th, table tbody tr td{
			wordwrap: break-word;
			break-ward: break-word;
		}
		@media print{
			table {
				border:solid #000 !important;
				border-width:1px 0 0 1px !important;				
			}
			thead th, tbody td {
				border:solid #000 !important;
				border-width:0 1px 1px 0 !important;
				font-size:12px!important;
			}
		}
	</style>

	<table cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" style="border: 1px; " >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th colspan="22" align="center" ><strong>Item Details</strong></th>
			</tr>
			<tr>
				<th width="30">SL</th>
				<th width="60">Item Category</th>
				<th width="40">Item Code</th>
				<th width="40">Item Number</th>
				<th width="100">Item Group</th>
				<th width="50">Sub Group Name</th>
				<th width="90">Item Des.</th>
				<th width="70">Brand / Origin / Model</th>
				<th width="40">Req. For</th>
				<th width="40">UOM</th>
				<th width="40">Req. Qty.</th>
				<th width="40">Rate</th>
				<th width="50">Amount</th>
				<th width="50">Stock</th>
				<th width="70">Last Rec. Date</th>
				<th width="50">Last Rec. Qty.</th>
				<th width="40">Last Rate</th>
				<th width="60">Requsition Value</th>
				<th width="50">Avg. Monthly issue</th>
				<th width="50">Avg. Monthly Rec.</th>
				<th width="90">Last Supplier</th>
				<th>Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= " SELECT a.id,b.id as dtls_id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label,c.item_number ,c.order_uom
		from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by b.item_category, c.item_group_id";
	    //echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$all_prod_ids.=$row[csf('product_id')].",";
			
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['order_uom'] = $row[csf('order_uom')];
		}
			$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
			if($all_prod_ids=="") $all_prod_ids=0;

			//print_r($all_prod_ids);
		/*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
		}*/
		 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and a.entry_form in (4,20) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis not in(6) order by  b.prod_id,b.id";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//var_dump($prev_issue_data);die;

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}


		// echo "<pre>";
		// print_r($all_data_array);

		foreach ($all_data_array as $cons_uom_id => $cons_uom_data)
		{
				$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
				$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
	        	foreach ($cons_uom_data as $dtls_id => $row) 
	        	{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$quantity=$row['quantity'];
					$quantity_sum += $quantity;
					$amount=$row['amount'];
					//test
					$sub_group_name=$row['sub_group_name'];
					$amount_sum += $amount;

					$current_stock=$row['stock'];
					$current_stock_sum += $current_stock;
					if($db_type==2)
					{
						$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
					}
					if($db_type==0)
					{
						$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
					}
					$last_req_info=explode('_',$last_req_info);
					//print_r($dataaa);

					$item_account=explode('-',$row['item_account']);
					$item_code=$item_account[3];
					/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
					$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
					$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
					$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
					$last_rec_rate=$receive_array[$row['product_id']]['rate'];
					$last_rec_supp=$receive_array[$row['product_id']]['supplier_id'];


					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:20px">
						<td align="center"><? echo $i; ?></td>
						<td><? echo $item_category[$row['item_category']]; ?></td>
						<td><div style="word-wrap:break-word:50px;"><? echo $row['item_code']; ?></div></td>
						<td><div style="word-wrap:break-word:50px;"><? echo $row['item_number']; ?></div></td>
						<td><p><? echo $item_name_arr[$row['item_group_id']]; ?></td>
						<td><p>
						<?
						echo $row['sub_group_name'];
						// if($row[csf('item_description')]!="" && $row[csf('item_size')]!="") echo ', ';
						// echo $row[csf('item_size')];
						?> </p></td>
						<td><p> <? echo $row["item_description"];?> </p></td>
						<td><p> B:<? echo $row["brand_name"] ."<br>";?></p><p> O: <? echo $origin_lib[$row["origin"]]."<br>";?></p><p> M: <?echo $row["model"];?> </p></td>
						<td><p>  <? echo $row["required_for"]; ?></p></td>
						<td align="center"><p><? echo $unit_of_measurement[$row["order_uom"]]; ?></p></td>
						<td align="right"><p><? echo $row['quantity']; ?>&nbsp;</p></td>
						<td align="right"><? echo $row['rate']; ?></td>
						<td align="right"><? echo $row['amount']; ?></td>
						<td align="right"><p><? echo number_format($row['stock'],2); ?></p></td>
						<td align="center"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
						<td align="right"><p><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
						<td align="right"><p><? echo $last_rec_rate;//$last_req_info[2]; ?>&nbsp;</p></td>
						<td align="right">
							<p>
							<?
								// if(trim($last_req_info[0])!="0000-00-00" && trim($last_req_info[0])!="") echo change_date_format($last_req_info[0]).'<br>'; else echo "&nbsp;<br>";
								//echo $last_req_info[1];
								$reqsit_value="";
								$reqsit_value=$last_rec_qty*$last_rec_rate;
								echo $reqsit_value;
							?>
							&nbsp;</p>
						</td>

						<td align="right">
							<?
							$min_issue_date=$prev_issue_data[$row["product_id"]]["transaction_date"];
							if($min_issue_date=="")
							{
								echo number_format(0,2);
							}
							else
							{
								$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
								$year_issue_total=$prev_issue_data[$row["product_id"]]["isssue_qty"];
								$issue_avg=$year_issue_total/$month_issue_diff;
								echo number_format($issue_avg,2);
							}
							?>
						</td>
						<td align="right">
							<?
							$min_receive_date=$prev_receive_data[$row["product_id"]]["transaction_date"];
							if($min_receive_date=="")
							{
								echo number_format(0,2);
							}
							else
							{
								$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
								$year_receive_total=$prev_receive_data[$row["product_id"]]["receive_qty"];
								$receive_avg=$year_receive_total/$month_receive_diff;
								echo number_format($receive_avg,2);
							}
							?>
						</td>
						<td><p><? echo $supplier_array[$last_rec_supp]; ?>&nbsp;</p></td>
						<td align="right"><? echo $row['remarks']; ?></td>

					</tr>
						<?
						$total_requisition += $row['quantity'];
						$last_qnty += $last_rec_qty;
						$total_stock += $row['stock'];
						$total_amount += $row['amount'];
						$total_reqsit_value += $reqsit_value;
						$total_monthly_iss += $issue_avg;
						$total_monthly_rej += $receive_avg;
						$i++;
				}
				?>
					<tr bgcolor="#dddddd">
						<td align="right" colspan="10"><strong>Sub Total : </strong></td>
						<td align="right"><? echo number_format($total_requisition,0,'',','); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
						<td align="right" ><? echo number_format($total_stock,0,'',','); ?></td>
						<td align="right" ></td>
						<td align="right"><? echo number_format($last_qnty,0,'',','); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right"><? echo $total_reqsit_value;?></td>
						<td align="right"><? echo number_format($total_monthly_iss,0,'',','); ?></td>
						<td align="right"><? echo number_format($total_monthly_rej,0,'',','); ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				<?
			$Grand_tot_total_amount += $total_amount;
			$Grand_tot_last_qnty += $last_qnty;
			$Grand_tot_reqsit_value += $total_reqsit_value;
			$Grand_tot_total_stock += $total_stock;
			$Grand_tot_monthly_iss += $total_monthly_iss;
			$Grand_tot_monthly_rej += $total_monthly_rej;
		}
		?>
		</tbody>
		<tr bgcolor="#B0C4DE">
			<td align="right" colspan="12"><strong>Total : </strong></td>
			<td align="right"><? echo number_format($Grand_tot_total_amount,0,'',','); ?></td>
			<td align="right" ><? echo number_format($Grand_tot_total_stock,0,'',','); ?></td>
			<td align="right" ></td>
			<td align="right"><? echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($Grand_tot_reqsit_value,0,'',',');?></td>
			<td align="right"><? echo number_format($Grand_tot_monthly_iss,0,'',',');?></td>
			<td align="right"><? echo number_format($Grand_tot_monthly_rej,0,'',',');?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<span><strong>Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span>
	<br>
	<?
	$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
    $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=1 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

    foreach ($approved_his_sql as $key => $row)
    {
    	$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
    	if ($row[csf('un_approved_date')]!='')
    	{
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
    	}
    }

    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    if(count($approved_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Requisition Approval Status </b></label>
                <thead>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Designation</th>
	                    <th width="100">Approval Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_sql as $key => $value)
                {
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
                    </tr>
                    <?
                    $sl++;
                }
                ?>
            </table>
        </div>
        <?
    }

	if(count($approved_his_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Requisition Approval / Un-Approval History </b></label>
                <thead>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="150">Approved / Un-Approved</th>
	                    <th width="150">Designation</th>
	                    <th width="50">Approval Status</th>
	                    <th width="150">Reason for Un-Approval</th>
	                    <th width="150">Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_his_sql as $key => $value)
                {
                	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                	?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="20"><? echo $sl; ?></td>
                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="50">Yes</td>
                        <td width="150"><? echo $unapproved_request_arr[$value[csf("mst_id")]]; ?></td>
                        <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    </tr>
                    <?
				    $sl++;
                    $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                    $un_approved_date=$un_approved_date[0];
                    if($db_type==0) //Mysql
                    {
                        if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }
                    else
                    {
                        if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }

                    if($un_approved_date!="")
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
	                        <td width="20"><? echo $sl; ?></td>
	                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
	                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
	                        <td width="50">No</td>
	                        <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
	                        <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);
							echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    	</tr>

						<?
						$sl++;
					}
                }
                ?>
            </table>
        </div>
        <?
    }
	//approved status end
	echo signature_table(25, $data[0], "1100px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	exit();
}

if($action=="purchase_requisition_print_14") // Print Report 14
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	//echo "test";die;
	 //print($data[5]);
	 //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,req_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
	$requisition_date=$dataArray[0][csf("requisition_date")];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
	//echo $requisition_date."==".$requisition_date_last_year;die;
	
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

	$pay_cash=$dataArray[0][csf('pay_mode')];
	?>

  	<style type="text/css">
  		@media print
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
	  			border: 1px solid #dccdcd !important;
	  		}
  		}
  	</style>
	<div id="table_row" style="max-width:1020px; margin-left: 2px;">

		<table width="1000" class="rpt_tables">
			<tr class="form_caption">
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left" rowspan="2">
				<?
				foreach($data_array as $img_row)
				{
					if ($formate_id==123) 
					{
						?>
						<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
						<?
					}
					else
					{
						?>
						<img src='../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
						<?
					}
				}
				?>
				</td>


				<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<td colspan="5" align="center" style="font-size:18px;">
				<?

				//echo show_company($data[0],'',''); //Aziz
				$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
				foreach ($nameArray as $result)
				{
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')];?>
					City No: <? echo $result[csf('city')];?>
					Zip Code: <? echo $result[csf('zip_code')]; ?>
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
					Email Address: <? echo $result[csf('email')];?>
					Website No: <? echo $result[csf('website')];
				}
				$req=explode('-',$dataArray[0][csf('requ_no')]);
				?>

				</td>
			</tr>
			<tr>
				<td>&nbsp; </td>
				<td colspan="5" align="center" style="font-size:24px"><strong><u><? echo $data[2] ?></u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:20px"><strong>Req. No:</strong></td>
				<td width="175px" style="font-size:20px"><strong><? echo $dataArray[0][csf('requ_no')];
				//$req[2].'-'.$req[3]; ?></strong></td>
				<td style="font-size:20px;" width="130"><strong>Req. Date:</strong></td><td style="font-size:20px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
				<td width="125" style="font-size:20px"><strong>Source:</strong></td><td width="175px" style="font-size:20px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:20px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:20px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
				<td style="font-size:20px"><strong>Department:</strong></td><td width="175px" style="font-size:20px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
				<td style="font-size:20px"><strong>Section:</strong></td><td width="175px" style="font-size:20px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
			</tr>
			<tr>
				 <td style="font-size:20px"><strong>Del. Date:</strong></td><td style="font-size:20px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
				<td style="font-size:20px"><strong>Store Name:</strong></td><td style="font-size:20px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
				<td style="font-size:20px"><strong>Pay Mode:</strong></td><td style="font-size:20px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:20px"><strong>Location:</strong></td> <td style="font-size:20px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
				<td style="font-size:20px"><strong>Currency:</strong></td> <td style="font-size:20px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
				<td style="font-size:20px"><strong>Req. By:</strong></td> <td style="font-size:20px"><? echo $dataArray[0][csf('req_by')]; ?></td>
			   
			</tr>
			<tr>
				
				<td style="font-size:20px"><strong>Remarks:</strong></td> <td style="font-size:20px"><? echo $dataArray[0][csf('remarks')]; ?></td>
				<td colspan="4"></td>
			</tr>
		</table>
		
	<br>
	<style type="text/css">
		table thead tr th, table tbody tr td{
			wordwrap: break-word;
			break-ward: break-word;
		}
		
	</style>

	<table cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" style="border: 1px;font-size: 18px;" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th colspan="21" align="center" ><strong>Item Details</strong></th>
			</tr>
			<tr>
				<th width="30">SL</th>
				
				<th width="100">Item Group</th>
				<th width="90">Item Des. & Size</th>
				<th width="70">Brand / Origin / Model</th>
				<th width="40">Req. For</th>
				<th width="40">UOM</th>
				<th width="40">Req. Qty.</th>
				<th width="40">Rate</th>
				<th width="50">Amount</th>
				<th width="50">Stock</th>
				<th width="70">Last Rec. Date</th>

				<th width="50">Last Rec. Qty.</th>
				<th width="40">Last Rate</th>
				<th width="60">Requsition Value</th>
				<th width="50">Avg. Monthly issue</th>
				<th width="50">Last Month issue</th>
				<th width="50">Avg. Monthly Rec.</th>
				<th width="50">Last Month Rec.</th>
				<th width="90">Last Supplier</th>
				<th width="90">Last Brand / Origin / Model</th>
				<th>Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= " SELECT a.id,b.id as dtls_id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label,c.item_number from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by a.id";
	    //echo $sql;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{

			$all_prod_ids.=$row[csf('product_id')].",";
			$all_data_array[$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
			$all_data_array[$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			$all_data_array[$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
		}
			$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
			if($all_prod_ids=="") $all_prod_ids=0;
		/*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
		}*/
		
		//and a.entry_form=20
		 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		// echo  $rec_sql;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		$last_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type in(2,3,6) and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between add_months(trunc(sysdate,'mm'),-1) and last_day(add_months(trunc(sysdate,'mm'),-1)) group by prod_id");
		$last_month_issue_data=array();
		foreach($last_month_issue_sql as $row)
		{
			$last_month_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_month_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//var_dump($prev_issue_data);die;

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		$receive_last_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between add_months(trunc(sysdate,'mm'),-1) and last_day(add_months(trunc(sysdate,'mm'),-1)) group by prod_id");
		$last_month_receive_data=array();
		foreach($receive_last_month_sql as $row)
		{
			$last_month_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_month_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}


		// echo "<pre>";
		// print_r($all_data_array);
		$previos_item_category='';
		$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
		$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
		$last_issue=0;
		$last_receive=0;
		$i=1;
		foreach ($all_data_array as $dtls_id => $row) {
				
				$item_cat=$row['item_category'];
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($previos_item_category!=$item_cat)
				{
					if($i>1)
					{ 	
						?>
						<tr bgcolor="#dddddd">
							<td align="right" colspan="6"><strong>Sub Total : </strong></td>
							<td align="right"><? echo number_format($total_requisition,2); ?></td>
							<td align="right">&nbsp;</td>
							<td align="right"><? echo number_format($total_amount,2); ?></td>
							<td align="right" ><? echo number_format($total_stock,2); ?></td>
							<td align="right" ></td>
							<td align="right"><? echo number_format($last_qnty,2); ?></td>
							<td align="right">&nbsp;</td>
							<td align="right"><? echo $total_reqsit_value;?></td>
							<td align="right"><? echo number_format($total_monthly_iss,2); ?></td>
							<td align="right"><? echo number_format($last_issue,2); ?></td>
					
							<td align="right"><? echo number_format($total_monthly_rej,2); ?></td>
							<td align="right"><? echo number_format($last_receive,2); ?></td>
							
							<td>&nbsp;</td>
							<td></td>
							<td>&nbsp;</td>
						</tr>
						<? 
						$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
						$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
						$previos_item_category=$item_cat;
						$last_issue=0;
						$last_receive=0;
					}

					?>
					<tr bgcolor="#dddddd">
							<td colspan="21" align="center" ><b>Category : <? echo $item_category[$row["item_category"]]; ?></b></td>
					</tr>
						<?


				}
	        	
					
					$quantity=$row['quantity'];
					$quantity_sum += $quantity;
					$amount=$row['amount'];
					//test
					$sub_group_name=$row['sub_group_name'];
					$amount_sum += $amount;

					$current_stock=$row['stock'];
					$current_stock_sum += $current_stock;
					if($db_type==2)
					{
						$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
					}
					if($db_type==0)
					{
						$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
					}
					$last_req_info=explode('_',$last_req_info);
					//print_r($dataaa);

					$item_account=explode('-',$row['item_account']);
					$item_code=$item_account[3];
					/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
					$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
					$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
					$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
					$last_rec_rate=$receive_array[$row['product_id']]['rate'];
					$last_rec_supp=$receive_array[$row['product_id']]['supplier_id'];

					
					$prod_id=$row["product_id"];
					if($db_type==0)
					{
						$last_band_model=sql_select(" SELECT b.product_id as prod_id,b.brand_name as brand_name,b.origin as origin,b.model as model
                                from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b  where a.id=b.mst_id  and b.product_id in($prod_id)  and a.id!=$update_id
                                order by b.id desc limit 1 ");
					}else
					{
						$last_band_model=sql_select("SELECT brand_name,origin,model , prod_id from (
                            SELECT rownum ,rs.* from (
                               SELECT b.product_id as prod_id,b.brand_name as brand_name,b.origin as origin,b.model as model
                                from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b  where a.id=b.mst_id  and b.product_id in($prod_id)  and a.id!=$update_id
                                order by b.id desc
                           ) rs 

                        ) where rownum <= 1
                         
			     		");
					}

					
					


					?>
					<tr bgcolor="<? echo $bgcolor; ?>" >
						<td align="center"><? echo $i; ?></td>
						
						
						<td><p><? echo $item_name_arr[$row['item_group_id']]; ?></td>
						
						<td><p> <? echo $row["item_description"]." , ". $row['item_size'];?> </p></td>
						<td><p> B:<? echo $row["brand_name"] ."<br>";?></p><p> O: <? echo $origin_lib[$row["origin"]]."<br>";?></p><p> M: <?echo $row["model"];?> </p></td>
						<td><p>  <? echo $use_for[$row["required_for"]]; ?></p></td>
						<td align="center"><p><? echo $unit_of_measurement[$row["cons_uom"]]; ?></p></td>
						<td align="right"><p><? echo $row['quantity']; ?>&nbsp;</p></td>
						<td align="right"><? echo $row['rate']; ?></td>
						<td align="right"><? echo $row['amount']; ?></td>
						<td align="right"><p><? echo number_format($row['stock'],2); ?></p></td>
						<td align="center" title="<?= $row['product_id'].te;?>"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
						<td align="right"><p><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
						<td align="right"><p><? echo $last_rec_rate;//$last_req_info[2]; ?>&nbsp;</p></td>
						<td align="right"><p>
							<?
							// if(trim($last_req_info[0])!="0000-00-00" && trim($last_req_info[0])!="") echo change_date_format($last_req_info[0]).'<br>'; else echo "&nbsp;<br>";
							//echo $last_req_info[1];
							$reqsit_value="";
							$reqsit_value=$last_rec_qty*$last_rec_rate;
							echo $reqsit_value;
							?>
							&nbsp;</p>
						</td>

						<td align="right">
							<?
							$min_issue_date=$prev_issue_data[$row["product_id"]]["transaction_date"];
							if($min_issue_date=="")
							{
								echo number_format(0,2);
							}
							else
							{
								$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
								$year_issue_total=$prev_issue_data[$row["product_id"]]["isssue_qty"];
								$issue_avg=$year_issue_total/$month_issue_diff;
								echo number_format($issue_avg,2);
							}
							?>
						</td>
						<td align="right">
							<? echo number_format($last_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
						</td>
						<td align="right">
							<?
							$min_receive_date=$prev_receive_data[$row["product_id"]]["transaction_date"];
							if($min_receive_date=="")
							{
								echo number_format(0,2);
							}
							else
							{
								$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
								$year_receive_total=$prev_receive_data[$row["product_id"]]["receive_qty"];
								$receive_avg=$year_receive_total/$month_receive_diff;
								echo number_format($receive_avg,2);
							}
							?>
						</td>
						<td align="right">
							<? echo number_format($last_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
						</td>
						<td><p><? echo $supplier_array[$last_rec_supp]; ?>&nbsp;</p></td>
						<td><p> B:<? echo $last_band_model[0][csf('brand_name')] ."<br>";?></p><p> O: <? echo $origin_lib[$last_band_model[0][csf('origin')]]."<br>";?></p><p> M: <?echo $last_band_model[0][csf('model')];?> </p></td>
						<td align="right"><? echo $row['remarks']; ?></td>

					</tr>
					<?

					$total_requisition += $row['quantity'];
					$last_qnty += $last_rec_qty;
					$total_stock += $row['stock'];
					$total_amount += $row['amount'];
					$total_reqsit_value += $reqsit_value;
					$total_monthly_iss += $issue_avg;
					$total_monthly_rej += $receive_avg;

					$last_issue+=$last_month_issue_data[$row["product_id"]]["isssue_qty"];
					$last_receive+=$last_month_receive_data[$row["product_id"]]["receive_qty"];

					$Grand_tot_total_amount += $row['amount'];
					$Grand_tot_last_qnty += $last_rec_qty;
					$Grand_tot_reqsit_value += $reqsit_value;
					$Grand_tot_total_stock += $row['stock'];
					$Grand_tot_monthly_iss += $issue_avg;
					$Grand_tot_monthly_rej += $receive_avg;
					$Grand_tot_last_month_issue+=$last_month_issue_data[$row["product_id"]]["isssue_qty"];
					$Grand_tot_last_month_receive+=$last_month_receive_data[$row["product_id"]]["receive_qty"];
					$previos_item_category=$item_cat;
					$i++;
				
			
		}
		?>
		</tbody>
		<tfoot>
			<tr bgcolor="#dddddd">
				<td align="right" colspan="6"><strong>Sub Total : </strong></td>
				<td align="right"><? echo number_format($total_requisition,2); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($total_amount,2); ?></td>
				<td align="right" ><? echo number_format($total_stock,2); ?></td>
				<td align="right" ></td>
				<td align="right"><? echo number_format($last_qnty,2); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo $total_reqsit_value;?></td>
				<td align="right"><? echo number_format($total_monthly_iss,2); ?></td>
				<td align="right"><? echo number_format($last_issue,2); ?></td>
				
				<td align="right"><? echo number_format($total_monthly_rej,2); ?></td>
				<td align="right"><? echo number_format($last_receive,2); ?></td>
				
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr bgcolor="#B0C4DE">
				<td align="right" colspan="8"><strong>Total : </strong></td>
				<td align="right"><? echo number_format($Grand_tot_total_amount,2); ?></td>
				<td align="right" ><? echo number_format($Grand_tot_total_stock,2); ?></td>
				<td align="right" ></td>
				<td align="right"><? echo number_format($Grand_tot_last_qnty,2); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($Grand_tot_reqsit_value,2);?></td>
				<td align="right"><? echo number_format($Grand_tot_monthly_iss,2);?></td>
				<td align="right"><? echo number_format($Grand_tot_last_month_issue,2);?></td>
				
				<td align="right"><? echo number_format($Grand_tot_monthly_rej,2);?></td>
				<td align="right"><? echo number_format($Grand_tot_last_month_receive,2);?></td>
				
				
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</tfoot>
	</table>


















	<span><strong>Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span>
	<br>
	<?
	$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
    $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=1 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

    foreach ($approved_his_sql as $key => $row)
    {
    	$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
    	if ($row[csf('un_approved_date')]!='')
    	{
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
    	}
    }

    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    if(count($approved_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Requisition Approval Status </b></label>
                <thead>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Designation</th>
	                    <th width="100">Approval Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_sql as $key => $value)
                {
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
                    </tr>
                    <?
                    $sl++;
                }
                ?>
            </table>
        </div>
        <?
    }

	if(count($approved_his_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Requisition Approval / Un-Approval History </b></label>
                <thead>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="150">Approved / Un-Approved</th>
	                    <th width="150">Designation</th>
	                    <th width="50">Approval Status</th>
	                    <th width="150">Reason for Un-Approval</th>
	                    <th width="150">Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_his_sql as $key => $value)
                {
                	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                	?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="20"><? echo $sl; ?></td>
                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="50">Yes</td>
                        <td width="150"><? echo $unapproved_request_arr[$value[csf("mst_id")]]; ?></td>
                        <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    </tr>
                    <?
				    $sl++;
                    $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                    $un_approved_date=$un_approved_date[0];
                    if($db_type==0) //Mysql
                    {
                        if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }
                    else
                    {
                        if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }

                    if($un_approved_date!="")
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
	                        <td width="20"><? echo $sl; ?></td>
	                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
	                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
	                        <td width="50">No</td>
	                        <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
	                        <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);
							echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    	</tr>

						<?
						$sl++;
					}
                }
                ?>
            </table>
        </div>
        <?
    }
	//approved status end
	echo signature_table(25, $data[0], "1100px",$cbo_template_id,20,$user_lib_name[$inserted_by]);
	exit();
}

if($action=="purchase_requisition_print_18") // Print Report 15
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print($data[5]);
	 //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	// $sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,req_by,priority_id,requisition_id,justification_value from inv_purchase_requisition_mst where id=$update_id";
	
	$sql="select a.id, a.requ_no, a.item_category_id, a.requisition_date, a.location_id, a.delivery_date, a.source, a.manual_req, a.department_id, a.section_id, a.store_name, a.pay_mode, a.cbo_currency, a.remarks,a.req_by,a.priority_id,a.requisition_id,a.justification_value,a.inserted_by, b.user_full_name, b.designation from inv_purchase_requisition_mst a left join user_passwd b on a.inserted_by=b.id where a.id=$update_id";
	// echo $sql;die;
	$dataArray=sql_select($sql);
	$user_designation_arr=return_library_array( "select id,custom_designation from lib_designation",'id','custom_designation');
	$requisition_date=$dataArray[0][csf("requisition_date")];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$user_name=$dataArray[0][csf("user_full_name")];
	$user_designation=$user_designation_arr[$dataArray[0][csf("designation")]];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
	//echo $requisition_date."==".$requisition_date_last_year;die;
	
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	$pay_cash=$dataArray[0][csf('pay_mode')];
	$approved_date_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
	foreach ($approved_date_sql as $key => $value)
                {
                    $approved_date= $value[csf("approved_date")];
                }

	?>

  	<style type="text/css">
  		@media print
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
	  			border: 1px solid #dccdcd !important;
	  		}
  		}
  	</style>
	<div id="table_row" style="max-width:1020px; margin-left: 2px;">

		<table width="800" class="rpt_tables">
			<tr class="form_caption">
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="" rowspan="2">
				<?
				foreach($data_array as $img_row)
				{
					if ($formate_id==123) 
					{
						?>
						<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='160' align="left" />
						<?
					}
					else
					{
						?>
						<img src='../<? echo $img_row[csf('image_location')]; ?>' height='70' width='160' align="left" />
						<?
					}
				}
				?>
				</td>
				<td colspan="4" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				<td>&nbsp; </td>
			</tr>

			<tr>
				<td colspan="4" align="center" style="font-size:24px"><strong><u><? echo $data[2] ?></u></strong></td>
				<td>&nbsp; </td>
			</tr>
			<tr>
				<td width='160'><strong>Req. No:</strong></td><td width='120' ><strong><? echo $dataArray[0][csf('requ_no')];
				//$req[2].'-'.$req[3]; ?></strong></td>
				<td width='120'><strong>Department:</strong></td><td width='120'><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
				<td width='120'><strong>Store Name:</strong></td><td width='120'><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>	
			</tr>
			<tr>
			<td  ><strong>Req. Date:</strong></td><td  ><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td ><strong>Section:</strong></td><td  ><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
			<td ><strong>Source:</strong></td><td  ><? echo $source[$dataArray[0][csf('source')]]; ?></td>
			</tr>
			<tr>
				 
			<td ><strong>Req. Approve Date:</strong></td>
			<td ><? echo change_date_format($approved_date);?></td>
			<td ><strong>Del. Date:</strong></td><td ><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
			<td ><strong>Req. By:</strong></td> <td ><? echo $dataArray[0][csf('req_by')]; ?></td>
			</tr>
			<tr>
				<td ><strong>Remarks:</strong></td> <td colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td ><strong>Importance:</strong></td> <td colspan="5"><? echo $priority_array[$dataArray[0][csf('priority_id')]]; ?></td>
			</tr>
			<tr>
				<td ><strong>Type of Purchase Requisition:</strong></td> <td colspan="5"><? echo $requisition_array[$dataArray[0][csf('requisition_id')]]; ?></td>
				<td ></td>
			</tr>
		</table>
		<br>	
		<table cellspacing="0" width="750"  border="1" rules="all" class="rpt_table" style="border: 1px;font-size: 18px;" >
		<thead bgcolor="#dddddd">
		<tr>
		<td align="left">Justification for Emergency/ Direct Purchase</td>
		</tr>
		</thead>
		<tbody>
		<tr>
		<td height="50"><? echo $dataArray[0][csf('justification_value')]; ?></td>
		</tr>
		</tbody>
		</table>
	<br>
	<style type="text/css">
		table thead tr th, table tbody tr td{
			wordwrap: break-word;
			break-ward: break-word;
		}
		
	</style>

	<table cellspacing="0" width="750"  border="1" rules="all" class="rpt_table" style="border: 1px;font-size: 18px;" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th colspan="9" align="center" ><strong>Item Details</strong></th>
			</tr>
			<tr>
				<th width="30">SL</th>
				
				<th width="100">ITEM GROUP</th>
				<th width="120">ITEM DESCRIPTION</th>
				<th width="40">UOM</th>
				<th width="70">REQ. QTY.</th>
				<th width="50">STOCK</th>
				<th width="100">LAST RCV. Date</th>
				<th width="100">LAST RCV. Qty.</th>
				<th>REMARKS</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= " select a.id,b.id as dtls_id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label,c.item_number from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by a.id";
	    // echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{

			$all_prod_ids.=$row[csf('product_id')].",";
			$all_data_array[$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
			$all_data_array[$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			$all_data_array[$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
		}
			$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
			if($all_prod_ids=="") $all_prod_ids=0;
		
		//and a.entry_form=20
		$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		// echo  $rec_sql;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		$last_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between add_months(trunc(sysdate,'mm'),-1) and last_day(add_months(trunc(sysdate,'mm'),-1)) group by prod_id");
		$last_month_issue_data=array();
		foreach($issue_sql as $row)
		{
			$last_month_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_month_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//var_dump($prev_issue_data);die;

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		$receive_last_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between add_months(trunc(sysdate,'mm'),-1) and last_day(add_months(trunc(sysdate,'mm'),-1)) group by prod_id");
		$last_month_receive_data=array();
		foreach($receive_last_month_sql as $row)
		{
			$last_month_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_month_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}
		// echo "<pre>";
		// print_r($all_data_array);
		$previos_item_category='';
		$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
		$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
		$last_issue=0;
		$last_receive=0;
		$i=1;
		foreach ($all_data_array as $dtls_id => $row) {
				$item_cat=$row['item_category'];
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($previos_item_category!=$item_cat)
				{
					?>
					<tr bgcolor="#dddddd">
							<td colspan="9" align="left" ><b>Category : <? echo $item_category[$row["item_category"]]; ?></b></td>
					</tr>
						<?
				}
					$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
					$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
					
					$prod_id=$row["product_id"];
					if($db_type==0)
					{
						$last_band_model=sql_select(" SELECT b.product_id as prod_id,b.brand_name as brand_name,b.origin as origin,b.model as model
                                from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b  where a.id=b.mst_id  and b.product_id in($prod_id)  and a.id!=$update_id
                                order by b.id desc limit 1 ");
					}else
					{
						$last_band_model=sql_select("SELECT brand_name,origin,model , prod_id from (
                            SELECT rownum ,rs.* from (
                               SELECT b.product_id as prod_id,b.brand_name as brand_name,b.origin as origin,b.model as model
                                from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b  where a.id=b.mst_id  and b.product_id in($prod_id)  and a.id!=$update_id
                                order by b.id desc
                           ) rs 
                        ) where rownum <= 1  
			     		");
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" >
						<td align="center"><? echo $i; ?></td>
						<td><p><? echo $item_name_arr[$row['item_group_id']]; ?></td>
						<td><p> <? echo $row["item_description"];?> </p></td>
						<td align="center"><p><? echo $unit_of_measurement[$row["cons_uom"]]; ?></p></td>
						<td align="center"><p><? echo $row['quantity']; ?>&nbsp;</p></td>
						<td align="center"><p><? echo number_format($row['stock'],2); ?></p></td>
						<td align="center"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
						<td align="center"><p><?  echo number_format($last_rec_qty,0,'',',')?></p></td>
						<td align="center"><? echo $row['remarks']; ?></td>

					</tr>
					<?
					$previos_item_category=$item_cat;
					$i++;
			
		}
		?>
		</tbody>
	
	</table>

	<br>
	<?
	$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");

	$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=1 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

    foreach ($approved_his_sql as $key => $row)
    {
    	$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
    	if ($row[csf('un_approved_date')]!='')
    	{
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
    	}
    }

    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

         ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:750px;text-align:center;" rules="all">
                <label><b>PR Raised By </b></label>
                <thead>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Position</th>
	                </tr>
            	</thead>
                    <tr>
                        <td width="20"><? echo "1"; ?></td>
                        <td width="250"><? echo $user_name; ?></td>
                        <td width="200"><? echo $user_designation; ?></td>
                    </tr>
            </table>
        </div>
        <?
	if(count($approved_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:750px;text-align:center;" rules="all">
                <label><b>Approval Status </b></label>
                <thead>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Designation</th>
	                    <th width="100">Approval Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_sql as $key => $value)
                {
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
                    </tr>
                    <?
                    $sl++;
                }
                ?>
            </table>
        </div>
        <?
    }
	if(count($approved_his_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:750px;text-align:center;" rules="all">
                <label><b>Approval / Un-Approval History </b></label>
                <thead>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="150">Approved / Un-Approved</th>
	                    <th width="150">Position</th>
	                    <th width="90">Approval Status</th>
	                    <th width="150">Reason for Un-Approval</th>
	                    <th width="150">Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_his_sql as $key => $value)
                {
                	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                	?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="20"><? echo $sl; ?></td>
                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="50">Yes</td>
                        <td width="150"><? echo $unapproved_request_arr[$value[csf("mst_id")]]; ?></td>
                        <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    </tr>
                    <?
				    $sl++;
                    $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                    $un_approved_date=$un_approved_date[0];
                    if($db_type==0) //Mysql
                    {
                        if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }
                    else
                    {
                        if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }

                    if($un_approved_date!="")
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
	                        <td width="20"><? echo $sl; ?></td>
	                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
	                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
	                        <td width="50">No</td>
	                        <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
	                        <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);
							echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    	</tr>

						<?
						$sl++;
					}
                }
                ?>
            </table>
        </div>
        <?
    }
	//approved status end
	echo signature_table(25, $data[0], "750px",$cbo_template_id,20,$user_lib_name[$inserted_by]);
	exit();
}

if($action=="purchase_requisition_print_19") // Print Report 16
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	//echo "test";die;
	 //print($data[5]);
	 //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency,priority_id, remarks,req_by, is_approved, req_by, inserted_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
	$requisition_date=$dataArray[0][csf("requisition_date")];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
	//echo $requisition_date."==".$requisition_date_last_year;die;
	
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$com_location_arr=return_library_array( "select id, address from lib_location",'id','address');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

	$inserted_by=$dataArray[0][csf("inserted_by")];

	$pay_cash=$dataArray[0][csf('pay_mode')];
	?>

  	<style type="text/css">
  		@media print
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
	  			border: 1px solid #dccdcd !important;
	  		}
  		}
  	</style>
	<div id="table_row" style="max-width:1020px; margin-left: 2px;">

		<table width="1100" class="rpt_tables">
			<tr class="form_caption">
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left" rowspan="2">
				<?
				foreach($data_array as $img_row)
				{
					if ($formate_id==123) 
					{
						?>
						<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
						<?
					}
					else
					{
						?>
						<img src='../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
						<?
					}
				}
				?>
				</td>


				<td colspan="6" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<td colspan="6" align="center" style="font-size:18px;" width="300">
				<?
					echo $com_location_arr[$dataArray[0][csf('location_id')]];
				/*echo show_company($data[0],'',''); //Aziz
				$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
				foreach ($nameArray as $result)
				{
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')];?>
					City No: <? echo $result[csf('city')];?>
					Zip Code: <? echo $result[csf('zip_code')]; ?>
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
					Email Address: <? echo $result[csf('email')];?>
					Website No: <? echo $result[csf('website')];
				}
				$req=explode('-',$dataArray[0][csf('requ_no')]);
				$approved=$dataArray[0][csf('is_approved')];*/
				?>

				</td>
			</tr>
			<tr>
				<td>&nbsp; </td>
				<td colspan="6" align="center" style="font-size:24px; padding-bottom:15px;"><strong><u><? echo $data[2] ?></u></strong></td>
			</tr>
			<tr>
				<td width="100" style="font-size:20px;"><strong>Req. No</strong><span style="margin-left:80px; font-weight:bold;">:</span></td>
				<td width="150" style="font-size:20px;"><span style="margin-left:-35px;"><? echo $dataArray[0][csf('requ_no')];
				//$req[2].'-'.$req[3]; ?></span></td>


				<td style="font-size:20px;" width="200"><strong>Req. Date</strong><span style="margin-left:30px; font-weight:bold;">:</span></td>
				<td style="font-size:20px;" width="450"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
				<td width="150" style="font-size:20px"><strong>Source</strong><span style="margin-left:34px; font-weight:bold;">:</span></td>
				<td width="450" style="font-size:20px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
				
				<td width="450" style="font-size:20px;color:red;"><strong><? if($approved==1){echo "Approved";} ?></strong></td>
			</tr>
			<tr>
				<td width="100" style="font-size:20px"><strong>Manual Req. No</strong><span style="margin-left:20px; font-weight:bold;">:</span></td>
				<td width="450" style="font-size:20px"><span style="margin-left:-35px;"><? echo $dataArray[0][csf('manual_req')]; ?></span></td>


				<td style="font-size:20px" width="200"><strong>Department</strong><span style="margin-left:15px; font-weight:bold;">:</span></td>
				<td width="250" style="font-size:20px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
				<td style="font-size:20px" width="200"><strong>Section</strong><span style="margin-left:30px; font-weight:bold;">:</span></td>
				<td width="450" style="font-size:20px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
				<td>&nbsp; </td>
			</tr>
			<tr>
				<td width="100" style="font-size:20px"><strong>Del. Date</strong><span style="margin-left:73px; font-weight:bold;">:</span></td>
				<td width="450" style="font-size:20px"><span style="margin-left:-35px;"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></span></td>

				<td style="font-size:20px" width="200"><strong>Store Name</strong><span style="margin-left:15px; font-weight:bold;">:</span></td>
				<td width="850" style="font-size:20px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
				<td style="font-size:20px" width="200"><strong>Pay Mode</strong><span style="margin-left:14px; font-weight:bold;">:</span></td>
				<td width="450" style="font-size:20px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
				<td>&nbsp; </td>
			</tr>
			<tr>
				<td width="100" style="font-size:20px"><strong>Location</strong><span style="margin-left:74px; font-weight:bold;">:</span></td> 
				<td  width="450" style="font-size:20px"><span style="margin-left:-35px;"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></span></td>

				<td style="font-size:20px" width="200"><strong>Currency</strong><span style="margin-left:34px; font-weight:bold;">:</span></td> 
				<td style="font-size:20px" width="450"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
				<td style="font-size:20px" width="200"><strong>Remarks</strong><span style="margin-left:23px; font-weight:bold;">:</span></td> 
				<td style="font-size:20px"  width="450"><? echo $dataArray[0][csf('remarks')]; ?></td>
				<!-- <td style="font-size:20px"><strong>Req. By:</strong></td> <td style="font-size:20px"><? echo $dataArray[0][csf('req_by')]; ?></td> -->
				<td>&nbsp; </td>
			   
			</tr>
			<tr>
				
				<td width="100" style="font-size:20px"><strong>Priority</strong><span style="margin-left:85px; font-weight:bold;">:</span></td>
				<td width="150" style="font-size:20px;"><span style="margin-left:-35px;"><? echo $priority_array[$dataArray[0][csf('priority_id')]];
				//$req[2].'-'.$req[3]; ?></span></td>
				<td style="font-size:20px" width="200"><strong>Req. By</strong><span style="margin-left:45px; font-weight:bold;">:</span></td> 
				<td style="font-size:20px" width="450"><? echo $dataArray[0][csf('req_by')]; ?></td>
			</tr>
			<tr>
				<!-- <td style="font-size:20px"><strong>Remarks:</strong></td> <td style="font-size:20px"><? echo $dataArray[0][csf('remarks')]; ?></td> -->
				<td colspan="4"></td>
				<td colspan="2">
					<?
						if($dataArray[0][csf("is_approved")] == 1)
						{
							?>
							 	<div id="approved" style="float:left; font-size:24px; color:#FF0000;">Approved</div>
							<?
						}
						if($dataArray[0][csf("is_approved")] == 3)
						{
							?>
							 	<div id="approved" style="float:left; font-size:24px; color:#FF0000;">Partial Approved</div>
							<?
						}
					?>
				</td>
			</tr>
		
		</table>
		
	<br>
	<style type="text/css">
		table thead tr th, table tbody tr td{
			wordwrap: break-word;
			break-ward: break-word;
		}
		
	</style>

	<table cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" style="border: 1px;font-size: 18px;" >
		<thead bgcolor="#dddddd" align="center">
			<!-- <tr>
				<th colspan="23 align="center" ><strong>Item Details</strong></th>
			</tr> -->
			<tr>
				<th width="30" rowspan="3">SL</th>
				<th width="50" rowspan="3">Item Code</th>
				<th width="100" rowspan="3">Item Category</th>
				<th width="100" rowspan="3">Item Group</th>
				<th width="250" rowspan="3">Item Des</th>
				<th width="100" rowspan="3">Item Size</th>
				<th width="70" rowspan="3">Brand</th>
				<th width="70" rowspan="3">Origin</th>
				<th width="40" rowspan="3">UOM</th>
				<th width="40" rowspan="3">Req. Qty.</th>
				<th width="40" rowspan="3">Rate</th>
				<th width="50" rowspan="3">Amount</th>
				<th width="50" rowspan="3">Stock</th>
				<th width="70" rowspan="3">Last Rec. Date</th>
				<th width="50" rowspan="3">Last Rec. Qty.</th>
				<th width="40" rowspan="3">Last Rate</th>
				<th width="60" rowspan="3">Last Rec. Value</th>
				<!-- <th width="50">Avg. Monthly issue</th>
				<th width="50">Last Month issue</th>
				<th width="50">Avg. Monthly Rec.</th>
				<th width="50">Last Month Rec.</th> -->
				<th width="50" rowspan="3">Last Supplier</th>
				<th width="300" colspan="6">Consumption</th>
				<th width="50" rowspan="3">Total Used/Issued Qty</th>
				<th rowspan="3" align="center">Remarks</th>
			</tr>
			<tr>
				<th width="100" colspan="2">Last Month</th>
				<th width="100" colspan="2">Last 3 Month</th>
				<th width="100" colspan="2">Last 6 Month</th>
			</tr>
			<tr>
				<th width="20">Rec.</th>
				<th width="20">Issue</th>
				<th width="20">Rec.</th>
				<th width="20">Issue</th>
				<th width="20">Rec.</th>
				<th width="20">Issue</th>
			</tr>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= " select a.id,b.id as dtls_id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label,c.item_number from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by a.id";
	    //echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{

			$all_prod_ids.=$row[csf('product_id')].",";
			$all_data_array[$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
			$all_data_array[$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			$all_data_array[$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
		}
			$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
			if($all_prod_ids=="") $all_prod_ids=0;
		/*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
		}*/
		
		//and a.entry_form=20
		 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		// echo  $rec_sql;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//$last_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between add_months(trunc(sysdate,'mm'),-1) and last_day(add_months(trunc(sysdate,'mm'),-1)) group by prod_id");

		$last_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) group by prod_id");

		
		$last_month_issue_data=array();
		foreach($last_month_issue_sql as $row)
		{
			$last_month_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_month_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//for last 3 month issue data

		
		$last_three_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-3)  group by prod_id");

		$last_three_month_issue_data=array();
		foreach($last_three_month_issue_sql as $row)
		{
			$last_three_month_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_three_month_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//for last 6 month issue data

		

		$last_six_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-6)  group by prod_id");

		$last_six_month_issue_data=array();
		foreach($last_six_month_issue_sql as $row)
		{
			$last_six_month_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_six_month_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//for total issue data

		$total_issue_data_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 group by prod_id");

		
		$total_issue_data=array();
		foreach($total_issue_data_sql as $row)
		{
			$total_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$total_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//for total issue data
		//var_dump($total_issue_data);

		$total_return_data_sql=sql_select("select prod_id, sum(cons_quantity) as return_qty from  inv_transaction where transaction_type=4 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 group by prod_id");
		$total_return_data=array();
		foreach($total_return_data_sql as $row)
		{
			$total_return_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$total_return_data[$row[csf("prod_id")]]["return_qty"]=$row[csf("return_qty")];
		}

		//var_dump($total_return_data);

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		
		$receive_last_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) group by prod_id");

		//$receive_last_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between add_months(trunc(sysdate,'mm'),-1) and last_day(add_months(trunc(sysdate,'mm'),-1)) group by prod_id");

		$last_month_receive_data=array();
		foreach($receive_last_month_sql as $row)
		{
			$last_month_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_month_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		// for last 3 month received data

		$receive_last_three_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-3) group by prod_id");

		$last_three_month_receive_data=array();
		foreach($receive_last_three_month_sql as $row)
		{
			$last_three_month_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_three_month_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		// for last 6 month received data

		$receive_last_six_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-6) group by prod_id");

		$last_six_month_receive_data=array();
		foreach($receive_last_six_month_sql as $row)
		{
			$last_six_month_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_six_month_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}


		// echo "<pre>";
		// print_r($all_data_array);
		$previos_item_category='';
		$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
		$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
		$last_issue=0;
		$last_receive=0;
		$i=1;
		foreach ($all_data_array as $dtls_id => $row) {
				
				$item_cat=$row['item_category'];
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($previos_item_category!=$item_cat)
				{
					if($i>1)
					{ 	
						?>
						<tr bgcolor="#dddddd">
							<td align="right" colspan="9"><strong>Sub Total : </strong></td>
							<td align="right"><? echo number_format($total_requisition,2); ?></td>
							<td align="right">&nbsp;</td>
							<td align="right"><? echo number_format($total_amount,2); ?></td>
							<td align="right" ><? //echo number_format($total_stock,2); ?></td>
							<td align="right" ></td>
							<td align="right"><? //echo number_format($last_qnty,2); ?></td>
							<td align="right">&nbsp;</td>
							<td align="right"><? //echo $total_reqsit_value;?></td>
							<td align="right">&nbsp;</td>
							<td align="right"><? //echo number_format($last_receive,2); ?></td>
							<td align="right"><? //echo number_format($last_issue,2); ?></td>
					
							<td align="right"><? //echo number_format($last_three_month_receive,2); ?></td>
							<td align="right"><? //echo number_format($last_three_month_issue,2); ?></td>

							<td align="right"><? //echo number_format($last_six_month_receive,2); ?></td>
							<td align="right"><? //echo number_format($last_six_month_issue,2); ?></td>
							
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<? 
						$total_amount=0;$last_qnty=0;$total_reqsit_value=0;$total_requisition=0;
						$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
						$previos_item_category=$item_cat;
						$last_issue=0;
						$last_receive=0;
					}

					?>
					<!-- <tr bgcolor="#dddddd">
							<td colspan="21" align="center" ><b>Category : <? //echo $item_category[$row["item_category"]]; ?></b></td>
					</tr> -->
						<?


				}
	        	
					
					$quantity=$row['quantity'];
					$quantity_sum += $quantity;
					$amount=$row['amount'];
					//test
					$sub_group_name=$row['sub_group_name'];
					$amount_sum += $amount;

					$current_stock=$row['stock'];
					$current_stock_sum += $current_stock;
					if($db_type==2)
					{
						$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
					}
					if($db_type==0)
					{
						$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
					}
					$last_req_info=explode('_',$last_req_info);
					//print_r($dataaa);

					$item_account=explode('-',$row['item_account']);
					$item_code=$item_account[3];
					/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
					$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
					$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
					$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
					$last_rec_rate=$receive_array[$row['product_id']]['rate'];
					$last_rec_supp=$receive_array[$row['product_id']]['supplier_id'];

					
					$prod_id=$row["product_id"];
					if($db_type==0)
					{
						$last_band_model=sql_select(" SELECT b.product_id as prod_id,b.brand_name as brand_name,b.origin as origin,b.model as model
                                from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b  where a.id=b.mst_id  and b.product_id in($prod_id)  and a.id!=$update_id
                                order by b.id desc limit 1 ");
					}else
					{
						$last_band_model=sql_select("SELECT brand_name,origin,model , prod_id from (
                            SELECT rownum ,rs.* from (
                               SELECT b.product_id as prod_id,b.brand_name as brand_name,b.origin as origin,b.model as model
                                from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b  where a.id=b.mst_id  and b.product_id in($prod_id)  and a.id!=$update_id
                                order by b.id desc
                           ) rs 

                        ) where rownum <= 1
                         
			     		");
					}

					
					


					?>
					<tr bgcolor="<? echo $bgcolor; ?>" >
						<td align="center"><? echo $i; ?></td>
						<td align="center"><p><? echo $row['item_code']; ?></p></td>
						<td align="center"><p><? echo $item_category[$row["item_category"]]; ?></p></td>
						<td align="center"><p><? echo $item_name_arr[$row['item_group_id']]; ?></p></td>
						<td align="center"><p> <? echo $row["item_description"]?> </p></td>
						<td align="center"><p><? echo $row['item_size']; ?></p></td>
						<td align="center"><p><? echo $row["brand_name"];?> </p></td>
						<td align="center"><p><? echo $origin_lib[$row["origin"]];?> </p></td>
						<td align="center"><p><? echo $unit_of_measurement[$row["cons_uom"]]; ?></p></td>
						<td align="right"><p><? echo $row['quantity']; ?>&nbsp;</p></td>
						<td align="right"><? echo $row['rate']; ?></td>
						<td align="right"><? echo $row['amount']; ?></td>
						<td align="right"><p><? echo number_format($row['stock'],2); ?></p></td>
						<td align="center" title="<?= $row['product_id'].te;?>"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
						<td align="right"><p><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
						<td align="right"><p><? echo $last_rec_rate;//$last_req_info[2]; ?>&nbsp;</p></td>
						<td align="right"><p>
							<?
							// if(trim($last_req_info[0])!="0000-00-00" && trim($last_req_info[0])!="") echo change_date_format($last_req_info[0]).'<br>'; else echo "&nbsp;<br>";
							//echo $last_req_info[1];
							$reqsit_value="";
							$reqsit_value=$last_rec_qty*$last_rec_rate;
							echo $reqsit_value;
							?>
							&nbsp;</p>
						</td>

						<!-- <td align="right">
							<?
							$min_issue_date=$prev_issue_data[$row["product_id"]]["transaction_date"];
							if($min_issue_date=="")
							{
								echo number_format(0,2);
							}
							else
							{
								$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
								$year_issue_total=$prev_issue_data[$row["product_id"]]["isssue_qty"];
								$issue_avg=$year_issue_total/$month_issue_diff;
								echo number_format($issue_avg,2);
							}
							?>
						</td> -->
						<!-- <td align="right">
							<? echo number_format($last_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
						</td> -->
						<!-- <td align="right">
							<?
							$min_receive_date=$prev_receive_data[$row["product_id"]]["transaction_date"];
							if($min_receive_date=="")
							{
								echo number_format(0,2);
							}
							else
							{
								$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
								$year_receive_total=$prev_receive_data[$row["product_id"]]["receive_qty"];
								$receive_avg=$year_receive_total/$month_receive_diff;
								echo number_format($receive_avg,2);
							}
							?>
						</td> -->
						<!-- <td align="right">
							<? echo number_format($last_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
						</td> -->
						<td><p><? echo $supplier_array[$last_rec_supp]; ?>&nbsp;</p></td>
						<td align="right">
							<? echo number_format($last_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
						</td>
						<td align="right">
							<? echo number_format($last_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
						</td>
						
						<td align="right">
							<? echo number_format($last_three_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
						</td>
						<td align="right">
							<? echo number_format($last_three_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
						</td>
						<td align="right">
							<? echo number_format($last_six_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
						</td>
						<td align="right">
							<? echo number_format($last_six_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
						</td>
						<td align="center"> 
						<?

						?>
							
							<? 
							$totalissQnty =$total_issue_data[$row["product_id"]]["isssue_qty"];
							$totalreturnQnty =$total_return_data[$row["product_id"]]["return_qty"];
							

							echo number_format($totalissQnty-$totalreturnQnty,2);
							?>
						 </td>
						<td align="right"><? echo $row['remarks']; ?></td>

					</tr>
					<?

					$total_requisition += $row['quantity'];
					$last_qnty += $last_rec_qty;
					$total_stock += $row['stock'];
					$total_amount += $row['amount'];
					$total_reqsit_value += $reqsit_value;
					$total_monthly_iss += $issue_avg;
					$total_monthly_rej += $receive_avg;

					$last_issue+=$last_month_issue_data[$row["product_id"]]["isssue_qty"];
					$last_receive+=$last_month_receive_data[$row["product_id"]]["receive_qty"];
					$last_three_month_issue+=$last_three_month_issue_data[$row["product_id"]]["isssue_qty"];
					$last_three_month_receive+=$last_three_month_receive_data[$row["product_id"]]["receive_qty"];
					$last_six_month_issue+=$last_six_month_issue_data[$row["product_id"]]["isssue_qty"];
					$last_six_month_receive+=$last_six_month_receive_data[$row["product_id"]]["receive_qty"];

					$Grand_tot_total_amount += $row['amount'];
					$Grand_tot_req_qty += $row['quantity'];
					$Grand_tot_last_qnty += $last_rec_qty;
					$Grand_tot_reqsit_value += $reqsit_value;
					$Grand_tot_total_stock += $row['stock'];
					$Grand_tot_monthly_iss += $issue_avg;
					$Grand_tot_monthly_rej += $receive_avg;
					$Grand_tot_last_month_issue+=$last_month_issue_data[$row["product_id"]]["isssue_qty"];
					$Grand_tot_last_month_receive+=$last_month_receive_data[$row["product_id"]]["receive_qty"];
					$Grand_tot_last_three_month_issue+=$last_three_month_issue_data[$row["product_id"]]["isssue_qty"];
					$Grand_tot_last_three_month_receive+=$last_three_month_receive_data[$row["product_id"]]["receive_qty"];
					$Grand_tot_last_six_month_issue+=$last_six_month_issue_data[$row["product_id"]]["isssue_qty"];
					$Grand_tot_last_six_month_receive+=$last_six_month_receive_data[$row["product_id"]]["receive_qty"];

					$previos_item_category=$item_cat;
					$i++;
				
			
		}
		?>
		</tbody>
		<tfoot>
			<tr bgcolor="#dddddd">
				<td align="right" colspan="9"><strong>Sub Total : </strong></td>
				<td align="right"><? echo number_format($total_requisition,2); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($total_amount,2); ?></td>
				<td align="right" ><? //echo number_format($total_stock,2); ?></td>
				<td align="right" ></td>
				<td align="right"><? //echo number_format($last_qnty,2); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? //echo $total_reqsit_value;?></td>
				<td align="right">&nbsp;</td>
				
				<td align="right"><? //echo number_format($last_receive,2); ?></td>
				<td align="right"><? //echo number_format($last_issue,2); ?></td>

				<td align="right"><? //echo number_format($last_three_month_receive,2); ?></td>
				<td align="right"><? //echo number_format($last_three_month_issue,2); ?></td>

				<td align="right"><? //echo number_format($last_six_month_receive,2); ?></td>
				<td align="right"><? //echo number_format($last_six_month_issue,2); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr bgcolor="#B0C4DE">
				<td align="right" colspan="9"><strong>Total : </strong></td>
				<td align="right"><? echo number_format($Grand_tot_req_qty,2); ?></td>
				<td>&nbsp;</td>
				<td align="right"><? echo number_format($Grand_tot_total_amount,2); ?></td>
				<td align="right" ><? //echo number_format($Grand_tot_total_stock,2); ?></td>
				<td align="right" ></td>
				<td align="right"><? //echo number_format($Grand_tot_last_qnty,2); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? //echo number_format($Grand_tot_reqsit_value,2);?></td>
				<td>&nbsp;</td>
				<td align="right"><? //echo number_format($Grand_tot_last_month_receive,2);?></td>
				
				<td align="right"><? //echo number_format($Grand_tot_last_month_issue,2);?></td>
				<td align="right"><? //echo number_format($Grand_tot_last_three_month_receive,2);?></td>
				
				<td align="right"><? //echo number_format($Grand_tot_last_three_month_issue,2);?></td>
				
				<td align="right"><? //echo number_format($Grand_tot_last_six_month_receive,2);?></td>
				
				<td align="right"><? //echo number_format($Grand_tot_last_six_month_issue,2);?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<br>
	<span><strong>Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span>
	<br>
	<?
	
	//approved status end
	$signature_arr=return_library_array( "SELECT MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature' ",'MASTER_TBLE_ID','IMAGE_LOCATION');
	$appSql="SELECT APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM=1 and MST_ID = $update_id ";
	 //echo $appSql;die();
	$appSqlRes=sql_select($appSql);
	foreach($appSqlRes as $row){
		$userSignatureArr[$row['APPROVED_BY']]=base_url($signature_arr[$row['APPROVED_BY']]);
	}
	if($signature_arr[$inserted_by]){ $userSignatureArr[$inserted_by]=base_url($signature_arr[$inserted_by]); }

	echo signature_table(25, $data[0], "1100px",$cbo_template_id,20,$inserted_by,$userSignatureArr);

	// echo signature_table(25, $data[0], "1100px",$cbo_template_id,20,$user_lib_name[$inserted_by]);
	// echo "<br><br>";
	// echo "<strong>Note: This is Software Generated Copy , Signature is not Required.</strong>";
	exit();
}
if($action=="purchase_requisition_print_20") // Print Report 17
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	//echo "test";die;
	 //print($data[5]);
	 //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,req_by,inserted_by from inv_purchase_requisition_mst where id=$update_id";

	$dataArray=sql_select($sql);
	$requisition_date=$dataArray[0][csf("requisition_date")];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
	//echo $requisition_date."==".$requisition_date_last_year;die;
	
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	$designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf("inserted_by")];
	$sql_user=sql_select("select id,user_full_name,employee_code,designation, user_email from user_passwd where id=$inserted_by");
	?>

  	<style type="text/css">
  		@media all
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
	  			border: 1px solid #dccdcd !important;
	  		}
  		}
  	</style>
	<div id="table_row" style="max-width:1350px; margin-left: 2px;">

		<table width="1350" class="rpt_tables">
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:35px; margin-bottom:5px;"><strong><u>Ha-Meem Group</u></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:20px; margin-bottom:50px;"><strong><u>Purchase Requisition</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:20px"><strong>Req. No</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="250px" style="font-size:20px"><? echo $dataArray[0][csf('requ_no')];?></td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td style="font-size:20px;" width="130"><strong>Req. Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td style="font-size:20px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
				<td colspan="3"></td>
				<!-- <td colspan="2" width="150px"></td> -->
			</tr>
			<tr>
				<td style="font-size:20px"><strong>Name of Unit</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="175px" style="font-size:20px"><? echo $company_library[$data[0]]; ?></td>
				<td style="font-size:20px" width="150"><strong>Name of Store Personnel</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="175px" style="font-size:20px" width="250"><? echo $sql_user[0][csf('user_full_name')]; ?></td>
				<!-- <td colspan="2" width="150px"></td> -->
				
			</tr>
			<tr>
			<td style="font-size:20px"><strong>Section</strong></td>
			<td width="10"><strong>:</strong></td>
			<td width="175px" style="font-size:20px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
			<td style="font-size:20px"><strong>Designation</strong></td>
			<td width="10"><strong>:</strong></td>
			<td style="font-size:20px"><? echo $designation[$sql_user[0][csf('designation')]]; ?></td>
			</tr>
			<tr>
			<td style="font-size:20px" rowspan="2" valign="top"><strong>Name of User</strong></td>
			<td  rowspan="2" valign="top"><strong>:</strong></td>
			<td width="175px" style="font-size:20px" rowspan="2" valign="top">
				<? $req_by = explode(',',$dataArray[0][csf("req_by")]);
				foreach($req_by as $key=> $value){
					echo $value."</br>";
				}?>
			</td>
			<td style="font-size:20px"><strong>Contact No</strong></td>
			<td ><strong>:</strong></td>
			<td style="font-size:20px"><? echo $sql_user[0][csf('employee_code')]; ?></td>
			</tr>
			<tr>
			<td style="font-size:20px"><strong>Email</strong></td>
			<td ><strong>:</strong></td>
			<td style="font-size:20px"><? echo $sql_user[0][csf('user_email')]; ?></td>
			</tr>

		</table>
		
	<br>
	<style type="text/css">
		table thead tr th, table tbody tr td{
			wordwrap: break-word;
			break-ward: break-word;
		}
		
	</style>

	<table cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" style="border: 1px;font-size: 18px;" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="30" rowspan="2">SL</th>
				<th width="90" rowspan="2">Name of Item(s)</th>
				<th width="40" rowspan="2">Unit</th>
				<th width="70" rowspan="2">Brand</th>
				<th width="70" rowspan="2">Origin</th>
				<th width="40" rowspan="2">Specification</th>
				<th width="40" rowspan="2">Type (Replace/New)</th>
				<th width="50" rowspan="2">Where to be used</th>
				<th width=""  colspan="3">Stock Status (Qty)</th>
				<th width=""  colspan="3">Last Purchase</th>
				<th width="" colspan="3">To Be Procured</th>
				<th width="80" rowspan="2">Expected Delivery Date</th>
				<th width="100" rowspan="2" align="center">Remarks</th>
			</tr>
			<tr>
				<th width="80" >Stock in Hand</th>
				<th width="80" >Safety Stock</th>
				<th width="80" >Consumption (Last Month)</th>
				<th width="50" >Qty</th>
				<th width="60" >Rate</th>
				<th width="60" >Date</th>
				<th width="50" >Qty</th>
				<th width="80" >Rate Appx.</th>
				<th width="80" >Amount</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= " select a.id,b.id as dtls_id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks,b.used_for,b.delivery_date, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label,c.item_number from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by a.id";
	    //echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{

			$all_prod_ids.=$row[csf('product_id')].",";
			$all_data_array[$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('dtls_id')]]['used_for'] = $row[csf('used_for')];
			$all_data_array[$row[csf('dtls_id')]]['delivery_date'] = $row[csf('delivery_date')];
			$all_data_array[$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
			$all_data_array[$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			$all_data_array[$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
		}
			$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
			if($all_prod_ids=="") $all_prod_ids=0;

		 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		// echo  $rec_sql;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		$last_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) group by prod_id");

		
		$last_month_issue_data=array();
		foreach($last_month_issue_sql as $row)
		{
			$last_month_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_month_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}


		$total_issue_data_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 group by prod_id");

		
		$total_issue_data=array();
		foreach($total_issue_data_sql as $row)
		{
			$total_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$total_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		$total_return_data_sql=sql_select("select prod_id, sum(cons_quantity) as return_qty from  inv_transaction where transaction_type=4 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 group by prod_id");
		$total_return_data=array();
		foreach($total_return_data_sql as $row)
		{
			$total_return_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$total_return_data[$row[csf("prod_id")]]["return_qty"]=$row[csf("return_qty")];
		}

		//var_dump($total_return_data);

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		$receive_last_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) group by prod_id");

		$last_month_receive_data=array();
		foreach($receive_last_month_sql as $row)
		{
			$last_month_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_month_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		// echo "<pre>";
		// print_r($all_data_array);
		$previos_item_category='';
		$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
		$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
		$last_issue=0;
		$last_receive=0;
		$i=1;
		foreach ($all_data_array as $dtls_id => $row) {
				
				$item_cat=$row['item_category'];
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$quantity=$row['quantity'];
					$quantity_sum += $quantity;
					$amount=$row['amount'];
					//test
					$sub_group_name=$row['sub_group_name'];
					$amount_sum += $amount;

					$current_stock=$row['stock'];
					$current_stock_sum += $current_stock;
					if($db_type==2)
					{
						$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
					}
					if($db_type==0)
					{
						$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
					}
					$last_req_info=explode('_',$last_req_info);
					//print_r($dataaa);

					$item_account=explode('-',$row['item_account']);
					$item_code=$item_account[3];
					/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
					$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
					$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
					$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
					$last_rec_rate=$receive_array[$row['product_id']]['rate'];
					$last_rec_supp=$receive_array[$row['product_id']]['supplier_id'];

					
					$prod_id=$row["product_id"];
					if($db_type==0)
					{
						$last_band_model=sql_select(" SELECT b.product_id as prod_id,b.brand_name as brand_name,b.origin as origin,b.model as model
                                from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b  where a.id=b.mst_id  and b.product_id in($prod_id)  and a.id!=$update_id
                                order by b.id desc limit 1 ");
					}else
					{
						$last_band_model=sql_select("SELECT brand_name,origin,model , prod_id from (
                            SELECT rownum ,rs.* from (
                               SELECT b.product_id as prod_id,b.brand_name as brand_name,b.origin as origin,b.model as model
                                from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b  where a.id=b.mst_id  and b.product_id in($prod_id)  and a.id!=$update_id
                                order by b.id desc
                           ) rs 

                        ) where rownum <= 1
                         
			     		");
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" >
						<td align="center"><? echo $i; ?></td>
						<td align="center"><p> <? echo $row["item_description"]?> </p></td>
						<td align="center"><p><? echo $unit_of_measurement[$row["cons_uom"]]; ?></p></td>
						<td align="center"><p><? echo $row["brand_name"];?> </p></td>
						<td align="center"><p><? echo $origin_lib[$row["origin"]];?> </p></td>
						<td align="center"><p> <? echo $row["item_size"]?> </p></td>
						<td align="center"><p> <? echo $use_for[$row["required_for"]]?> </p></td>
						<td align="center"><p><? echo $row['used_for']; ?>&nbsp;</p></td>

						<td align="right"><? echo  number_format($row['stock'],0); ?></td>

						<td align="right"><p><? echo number_format($row['re_order_label'],0); ?>&nbsp;</p></td>
						<td align="right">	<? echo number_format($last_month_issue_data[$row["product_id"]]["isssue_qty"],0);?></td>

						<td align="right"><? echo number_format($last_rec_qty,0); ?></td>
						<td align="right"><? echo number_format($last_rec_rate,0); ?></td>
						<td align="center"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></td>

						<td align="right"><p><? echo number_format($row['quantity'],0); ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row['rate'],0); ?></td>
						<td align="right"><? echo number_format($row['amount'],2); ?></td>

						<td align="center"><? echo change_date_format($row['delivery_date']); ?></td>
						<td align="right"><? echo $row['remarks']; ?></td>

					</tr>
					<?

					$total_requisition += $row['quantity'];
					$last_qnty += $last_rec_qty;
					$total_stock += $row['stock'];
					$total_amount += $row['amount'];
					$total_re_order += $row['re_order_label'];
					$total_reqsit_value += $reqsit_value;
					$total_monthly_iss += $issue_avg;
					$total_monthly_rej += $receive_avg;

					$last_issue+=$last_month_issue_data[$row["product_id"]]["isssue_qty"];
					$last_receive+=$last_month_receive_data[$row["product_id"]]["receive_qty"];

					$Grand_tot_total_amount += $row['amount'];
					$Grand_tot_last_qnty += $last_rec_qty;
					$Grand_tot_reqsit_value += $reqsit_value;
					$Grand_tot_total_stock += $row['stock'];
					$Grand_tot_monthly_iss += $issue_avg;
					$Grand_tot_monthly_rej += $receive_avg;
					$Grand_tot_last_month_issue+=$last_month_issue_data[$row["product_id"]]["isssue_qty"];
					$previos_item_category=$item_cat;
					$i++;
				
			
		}
		?>
		</tbody>
		<tfoot>
			<tr bgcolor="#B0C4DE">
				<td>&nbsp;</td>
				<td align="right" colspan="7"><strong>Total : </strong></td>
				<td align="right"><? echo number_format($total_stock,0); ?></td>
				<td align="right"><? echo number_format($total_re_order,0); ?></td>
				<td align="right"><? echo number_format($Grand_tot_last_month_issue,0); ?></td>
				<td align="right"><? echo number_format($last_qnty,0); ?></td>
				<td align="right"></td>
				<td align="right"></td>
				<td align="right"><? echo number_format($total_requisition,0); ?></td>
				<td align="right" ></td>
				<td align="right" ><? echo number_format($Grand_tot_total_amount,2); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<span><strong>Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span>
	<br><br><br><br><br>
	<?
	echo signature_table(25, $data[0], "1100px",$cbo_template_id,20,$user_lib_name[$inserted_by]);
	exit();
}

if($action=="purchase_requisition_print_25") // Print 20
{  
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_id=$data[0];
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$sql="select id, requ_no, req_by, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, division_id, section_id, store_name, pay_mode, cbo_currency, priority_id, remarks, reference, inserted_by, is_approved from inv_purchase_requisition_mst where id=$update_id";

	//echo $sql;die();
	$dataArray=sql_select($sql);
	$requ_no=explode('-', $dataArray[0][csf("requ_no")]);
	$requisition_no=$requ_no[2].'-'.$requ_no[3];
	$requisition_date=$dataArray[0][csf("requisition_date")];
	$inserted_by=$dataArray[0][csf("inserted_by")];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
	//echo $requisition_date."==".$requisition_date_last_year;die;
			
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department_library=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_library=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	?>

  	<style type="text/css">
  		/*@media print
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
	  			border: 1px solid #dccdcd !important;
	  		}	  		
  		}*/
  		.rpt_table td{
	  		border: 1px solid black;
	  	}
	  	.rpt_table thead th{
	  		border: 1px solid black;
	  	}
  	</style>
  	<style type="text/css">
		.wrd_brk{
			wordwrap: break-word;
			break-ward: break-word;			
		}
		.fontsize{
			font-size: 10px;
		}

	</style>
	<table width="1350" class="rpt_tables" style="margin-left: 2px;">
		<tr>
			<?
			//echo "select image_location from common_photo_library where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1";
			$data_array=sql_select("select image_location from common_photo_library where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
			?>
			<td align="left" style="width: 100px;">
			<?
			if ($data[8]!="") $path=$data[8];
			else $path="../";
 
			foreach($data_array as $img_row)
			{
				?>
				<img src='<? echo $path.$img_row[csf('image_location')]; ?>' height='40' width='80' align="middle" />
				<?
			}
			?>
			</td>
			<td colspan="7" style="font-size:24px" align="center"><strong><? echo $company_library[$company_id]; ?></strong></td>
		</tr>
		<tr>
			<td colspan="4" align="left"><strong>Head Office:&nbsp;
			<?
			//echo show_company($data[0],'',''); //Aziz
			$company_info=sql_select("select group_id, plot_no, city from lib_company where id=$company_id and is_deleted=0");
			$group_id=$company_info[0][csf('group_id')];
			$nameArray=sql_select( "select address, country_id, contact_no from lib_group where id=$group_id");
			echo $nameArray[0][csf('address')].', '.$country_arr[$nameArray[0][csf('address')]];
			?></strong>
			</td>
			<td colspan="4" align="right"><strong>Factory Address:&nbsp;
				<? echo $company_info[0][csf('plot_no')].', '.$company_info[0][csf('city')]; ?></strong>
			</td>
		</tr>
		<tr>				
			<td colspan="8" align="left" style="border-bottom: 1px solid black;"><strong>Phone:&nbsp;<? echo $nameArray[0][csf('contact_no')]; ?></strong></td>
		</tr>
		<tr>
			<td colspan="8" align="center" style="font-size: 16px;"><strong><p style="text-decoration: underline; padding: 10px 0px 10px 0px">Material Purchase Requisition</p></strong></td>
		</tr>
	</table>
	<table width="1350" style="margin-left: 2px;">
		<table width="260" style="float: left; border: 2px solid black; margin-right: 102px; margin-left: 2px;" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="115">Reqn. No</td>
				<td width="160"><? echo $requisition_no; ?></td>
			</tr>
			<tr>
				<td width="115">Reqn. Date</td>
				<td width="160"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
			</tr>
		</table>
		<table width="258" style="float: left; border: 2px solid black;  margin-right: 52px;" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="100">Reqn. From</td>
				<td width="155"><? echo $dataArray[0][csf('req_by')]; ?></td>
			</tr>
			<tr>
				<td width="100">Reference</td>
				<td width="155"><? echo$dataArray[0][csf('reference')]; ?></td>
			</tr>
		</table>
		<table width="360" style="float: left; border: 2px solid black;  margin-right: 55px;" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="100">Division</td><td width="185"><? echo $division_library[$dataArray[0][csf('division_id')]]; ?></td>
				<td width="100">Department</td><td width="185"><? echo $department_library[$dataArray[0][csf('department_id')]]; ?></td>
			</tr>
			<tr>
			   <td width="100">Section</td><td width="185"><? echo $section_library[$dataArray[0][csf('section_id')]]; ?></td>
				<td width="100"></td><td width="185"></td>
			</tr>
		</table>
		<table width="260" style="float: left; border: 2px solid black;" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="100">Store Name</td>
				<td width="160"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
			</tr>
			<tr>
				<td width="100">Priority</td>
				<td width="160"><? echo $priority_array[$dataArray[0][csf('priority_id')]]; ?></td>
			</tr>
		</table>
	</table>
	<table width="1350" style="border: 2px solid black;" border="1" rules="all" class="rpt_table">
		<table width="260" style="float: left; border: 2px solid black; margin-left: 2px; margin-right: 102px; margin-top: 5px;" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="115">Approval Status</td>
				<td width="160" style="color: red; font-size: 18px;"><? if ($dataArray[0][csf('is_approved')] == 0) echo "Un-Approved"; else if($dataArray[0][csf('is_approved')] == 1) echo "Approved"; else echo "Partial-Approved"; ?></td>
			</tr>
		</table>				
		<table width="985" style="float: left; border: 2px solid black; margin-top: 5px;" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="100">Remarks</td>
				<td width="885"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
	</table>	
	<br>
	<div style="width: 1350px;">
	<table cellspacing="0" width="1350" border="1" rules="all" class="rpt_table fontsize" style="border-top: 2px solid black; border-left: 2px solid black; border-right: 2px solid black; border-bottom: 1px solid black; margin-left: 2px; margin-top: 20px;" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="30" rowspan="3">SL</th>
				<th width="80" rowspan="3">Item Category</th>
				<th width="100" rowspan="3">Item Group</th>
				<th width="120" rowspan="3">Item Des.</th>
				<th width="120" rowspan="3">Model</th>
				<th width="40" rowspan="3">UOM</th>
				<th width="50" rowspan="3">Reqn. Qty.</th>
				<th width="40" rowspan="3">Rate</th>
				<th width="50" rowspan="3">Amount</th>
				<th width="60" rowspan="3">Stock</th>
				<th width="50" rowspan="3">Last Rec. Date</th>
				<th width="50" rowspan="3">Last Rec. Qty.</th>
				<th width="40" rowspan="3">Last Rate</th>
				<th width="60" rowspan="3">Last Rec. Value</th>
				<th width="100" rowspan="3" style="border-right: 2px solid black;">Last Supplier</th>
				<th width="300" colspan="6">Consumption</th>
				<!-- <th width="50" rowspan="3" style="border-left: 2px solid black;">Total Issued Qty.</th> -->
				<th rowspan="3" align="center">Remarks</th>
			</tr>
			<tr>
				<th width="100" colspan="2">Last Month</th>
				<th width="100" colspan="2">Last 3 Month</th>
				<th width="100" colspan="2">Last 6 Month</th>
			</tr>
			<tr>
				<th width="50">Rec.</th>
				<th width="50">Issue</th>
				<th width="50">Rec.</th>
				<th width="50">Issue</th>
				<th width="50">Rec.</th>
				<th width="50">Issue</th>
			</tr>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= "SELECT a.id,b.id as dtls_id, b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label,c.item_number from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by b.item_category";
	    //echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{

			$all_prod_ids.=$row[csf('product_id')].",";
			$all_data_array[$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
			$all_data_array[$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			$all_data_array[$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
			$row_span_arr[$row[csf('item_category_id')]]++;
		}
		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;
		//echo '<pre>';print_r($row_span_arr);
		//and a.entry_form=20
		 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		// echo  $rec_sql;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		$last_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) group by prod_id");

		
		$last_month_issue_data=array();
		foreach($last_month_issue_sql as $row)
		{
			$last_month_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_month_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//for last 3 month issue data
		
		$last_three_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-3)  group by prod_id");

		$last_three_month_issue_data=array();
		foreach($last_three_month_issue_sql as $row)
		{
			$last_three_month_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_three_month_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//for last 6 month issue data
		

		$last_six_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-6)  group by prod_id");

		$last_six_month_issue_data=array();
		foreach($last_six_month_issue_sql as $row)
		{
			$last_six_month_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_six_month_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//for total issue data

		$total_issue_data_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 group by prod_id");

		
		$total_issue_data=array();
		foreach($total_issue_data_sql as $row)
		{
			$total_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$total_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//for total issue data
		//var_dump($total_issue_data);

		$total_return_data_sql=sql_select("select prod_id, sum(cons_quantity) as return_qty from  inv_transaction where transaction_type=4 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 group by prod_id");
		$total_return_data=array();
		foreach($total_return_data_sql as $row)
		{
			$total_return_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$total_return_data[$row[csf("prod_id")]]["return_qty"]=$row[csf("return_qty")];
		}

		//var_dump($total_return_data);

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}
		
		$receive_last_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) group by prod_id");

		//$receive_last_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between add_months(trunc(sysdate,'mm'),-1) and last_day(add_months(trunc(sysdate,'mm'),-1)) group by prod_id");

		$last_month_receive_data=array();
		foreach($receive_last_month_sql as $row)
		{
			$last_month_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_month_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		// for last 3 month received data

		$receive_last_three_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-3) group by prod_id");

		$last_three_month_receive_data=array();
		foreach($receive_last_three_month_sql as $row)
		{
			$last_three_month_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_three_month_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		// for last 6 month received data

		$receive_last_six_month_sql=sql_select("select prod_id, sum() as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prodcons_quantity_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-6) group by prod_id");

		$last_six_month_receive_data=array();
		foreach($receive_last_six_month_sql as $row)
		{
			$last_six_month_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$last_six_month_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		// echo "<pre>";
		// print_r($all_data_array);
		$previos_item_category='';
		$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
		$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
		$last_issue=0;
		$last_receive=0;
		$i=1;
		foreach ($all_data_array as $dtls_id => $row) 
		{
			$item_cat=$row['item_category'];
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";				
					
			$quantity=$row['quantity'];
			$quantity_sum += $quantity;
			$amount=$row['amount'];
			//test
			$sub_group_name=$row['sub_group_name'];
			$amount_sum += $amount;

			$current_stock=$row['stock'];
			$current_stock_sum += $current_stock;
			if($db_type==2)
			{
				$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
			}
			if($db_type==0)
			{
				$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
			}
			$last_req_info=explode('_',$last_req_info);
			//print_r($dataaa);

			$item_account=explode('-',$row['item_account']);
			$item_code=$item_account[3];
			/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
			$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
			$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
			$last_rec_rate=$receive_array[$row['product_id']]['rate'];
			$last_rec_supp=$receive_array[$row['product_id']]['supplier_id'];
					
			$prod_id=$row["product_id"];
			if($db_type==0)
			{
				$last_band_model=sql_select(" SELECT b.product_id as prod_id, b.brand_name as brand_name, b.origin as origin, b.model as model from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and b.product_id in($prod_id) and a.id!=$update_id order by b.id desc limit 1 ");
			}
			else
			{
				$last_band_model=sql_select("SELECT brand_name, origin, model, prod_id from (SELECT rownum ,rs.* from (
                       SELECT b.product_id as prod_id, b.brand_name as brand_name, b.origin as origin, b.model as model
                        from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and b.product_id in($prod_id) and a.id!=$update_id order by b.id desc
                   ) rs 

                ) where rownum <= 1
                 
	     		");
			}

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" >
				<td  width="30" align="center"><? echo $i; ?></td>
				<? if($check_category[$row["item_category"]]==''){  ?>
				<td  width="80" align="center" class="wrd_brk" rowspan="<? echo $row_span_arr[$row['item_category']]; ?>"><? echo $item_category[$row["item_category"]]; ?></td>
				<? $check_category[$row["item_category"]]=$row["item_category"];} ?>
				<td  width="100" align="center" class="wrd_brk"><? echo $item_name_arr[$row['item_group_id']]; ?></td>
				<td  width="120" align="center" class="wrd_brk"><? if ($row["item_size"] != "") echo $row["item_description"].', '.$row["item_size"]; else echo $row["item_description"]; ?></td>
				<td  width="120" align="center" class="wrd_brk"><? echo $row['model']; ?></td>
				<td width="40" align="center" class="wrd_brk"><? echo $unit_of_measurement[$row["cons_uom"]]; ?></td>
				<td width="50" align="right" class="wrd_brk"><? echo $row['quantity']; ?></td>
				<td width="40" align="right" class="wrd_brk"><? echo number_format($row['rate'],2); ?></td>
				<td width="50" align="right" class="wrd_brk"><? echo number_format($row['amount'],2); ?></td>
				<td width="60" align="right" class="wrd_brk"><? echo number_format($row['stock'],2); ?></td>
				<td width="50" align="center" class="wrd_brk" title="<?= $row['product_id'].te;?>"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></td>
				<td width="50" align="right" class="wrd_brk"><? echo number_format($last_rec_qty,0,'',','); ?></td>
				<td width="40" align="right" class="wrd_brk"><? echo number_format($last_rec_rate,2);//$last_req_info[2]; ?></td>
				<td width="60" align="right" class="wrd_brk">
					<?
					$reqsit_value="";
					$reqsit_value=$last_rec_qty*$last_rec_rate;
					echo number_format($reqsit_value,2);
					?>					
				</td>					
				<td width="100" align="center" class="wrd_brk"  style="border-right: 2px solid black;"><? echo $supplier_array[$last_rec_supp]; ?></td>
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
				</td>
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
				</td>				
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_three_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
				</td>
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_three_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
				</td>
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_six_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
				</td>
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_six_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
				</td>
				
				<td align="right" class="wrd_brk"><? echo $row['remarks']; ?></td>
			</tr>
			<?
			$total_requisition += $row['quantity'];
			$last_qnty += $last_rec_qty;
			$total_stock += $row['stock'];
			$total_amount += $row['amount'];
			$total_reqsit_value += $reqsit_value;
			$total_monthly_iss += $issue_avg;
			$total_monthly_rej += $receive_avg;

			$last_issue+=$last_month_issue_data[$row["product_id"]]["isssue_qty"];
			$last_receive+=$last_month_receive_data[$row["product_id"]]["receive_qty"];
			$last_three_month_issue+=$last_three_month_issue_data[$row["product_id"]]["isssue_qty"];
			$last_three_month_receive+=$last_three_month_receive_data[$row["product_id"]]["receive_qty"];
			$last_six_month_issue+=$last_six_month_issue_data[$row["product_id"]]["isssue_qty"];
			$last_six_month_receive+=$last_six_month_receive_data[$row["product_id"]]["receive_qty"];

			$Grand_tot_total_amount += $row['amount'];
			$Grand_tot_last_qnty += $last_rec_qty;
			$Grand_tot_reqsit_value += $reqsit_value;
			$Grand_tot_total_stock += $row['stock'];
			$Grand_tot_monthly_iss += $issue_avg;
			$Grand_tot_monthly_rej += $receive_avg;
			$Grand_tot_last_month_issue+=$last_month_issue_data[$row["product_id"]]["isssue_qty"];
			$Grand_tot_last_month_receive+=$last_month_receive_data[$row["product_id"]]["receive_qty"];
			$Grand_tot_last_three_month_issue+=$last_three_month_issue_data[$row["product_id"]]["isssue_qty"];
			$Grand_tot_last_three_month_receive+=$last_three_month_receive_data[$row["product_id"]]["receive_qty"];
			$Grand_tot_last_six_month_issue+=$last_six_month_issue_data[$row["product_id"]]["isssue_qty"];
			$Grand_tot_last_six_month_receive+=$last_six_month_receive_data[$row["product_id"]]["receive_qty"];

			$previos_item_category=$item_cat;
			$i++;
		}
		?>
		</tbody>
		<tr bgcolor="#B0C4DE" style="border-bottom: 2px solid black;">				
			<td width="30">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td width="100">&nbsp;</td>
			<td width="120">&nbsp;</td>
			<td width="40">&nbsp;</td>
			<td width="50">&nbsp;</td>
			<td width="40" align="right" class="wrd_brk"><strong>Total :&nbsp;</strong></td>
			<td width="50" align="right" class="wrd_brk"><? echo number_format($Grand_tot_total_amount,2); ?></td>
			<td width="60" align="right"><? //echo number_format($Grand_tot_total_stock,2); ?>&nbsp;</td>
			<td width="50" align="right">&nbsp;</td>
			<td width="50" align="right"><? //echo number_format($Grand_tot_last_qnty,2); ?>&nbsp;</td>
			<td width="46" align="right">&nbsp;</td>
			<td width="60" align="right"><? //echo number_format($Grand_tot_reqsit_value,2);?>&nbsp;</td>
			<td width="100" style="border-right: 2px solid black;">&nbsp;</td>
			<td width="50" align="right"><? //echo number_format($Grand_tot_last_month_receive,2);?>&nbsp;</td>			
			<td width="50" align="right"><? //echo number_format($Grand_tot_last_month_issue,2);?>&nbsp;</td>
			<td width="50" align="right"><? //echo number_format($Grand_tot_last_three_month_receive,2);?>&nbsp;</td>			
			<td width="50" align="right"><? //echo number_format($Grand_tot_last_three_month_issue,2);?>&nbsp;</td>			
			<td width="50" align="right"><? //echo number_format($Grand_tot_last_six_month_receive,2);?>&nbsp;</td>			
			<td width="50" align="right"><? //echo number_format($Grand_tot_last_six_month_issue,2);?>&nbsp;</td>
			<td width="50" style="border-left: 2px solid black;">&nbsp;</td>
			<td width="50" style="border-left: 2px solid black;">&nbsp;</td>
			<!-- <td>&nbsp;</td> -->
		</tr>
	</table>	
	<!-- <table cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table fontsize" style="border-left: 2px solid black; border-right: 2px solid black; border-bottom: 2px solid black; margin-left: 2px; " >		
				
	</table> -->
	</div>
	<span style="margin-left: 2px;"><strong>Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span>
	<br>		
	<?	

$signature_arr=return_library_array( "SELECT MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature' ",'MASTER_TBLE_ID','IMAGE_LOCATION');
/* $appSql="SELECT APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM=1 and MST_ID = $update_id ";
// echo $appSql;
$appSqlRes=sql_select($appSql);
foreach($appSqlRes as $row){
	$userSignatureArr[$row['APPROVED_BY']]=base_url($signature_arr[$row['APPROVED_BY']]);
}
if($signature_arr[$inserted_by]){ $userSignatureArr[$inserted_by]=base_url($signature_arr[$inserted_by]); }

echo signature_table(25, $company_id, "1200px",$cbo_template_id,50,$inserted_by,$userSignatureArr);
exit(); */

	$appSql = "select a.APPROVED_BY, a.APPROVED_DATE,b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from approval_mst a,USER_PASSWD b,LIB_DESIGNATION c where a.mst_id =$update_id and a.APPROVED_BY=b.id and b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0
	union all
	select b.id as APPROVED_BY, a.INSERT_DATE as APPROVED_DATE,b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from inv_purchase_requisition_mst a,USER_PASSWD b,LIB_DESIGNATION c where a.id =$update_id and a.INSERTED_BY=b.id and b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0";
	//echo $appSql;die;
	$appSqlRes=sql_select($appSql);
	$userDtlsArr=array();
	foreach($appSqlRes as $row){
		$userDtlsArr[$row['APPROVED_BY']]=base_url($signature_arr[$row['APPROVED_BY']]);
	}
	// echo "<pre>";
	// print_r($userDtlsArr); 
	//  echo "</pre>";die();
	
	//echo signature_table(25, $company_id, "1200px",$cbo_template_id,50,$inserted_by,$userSignatureArr);
	echo signature_table(25, $company_id,"1200px",$cbo_template_id, 50,$inserted_by,$userDtlsArr); 
//.........................
  
	exit();
}



if($action=="purchase_requisition_print_21") // Print Report 18
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);

	//echo "shakil"; die;
	//echo "test";die;
	 //print($data[5]);
	 //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, priority_id,requisition_id, is_approved, pay_mode, cbo_currency, remarks,req_by,inserted_by from inv_purchase_requisition_mst where id=$update_id";

	//$nameArray=sql_select( "select id,requ_no,company_id,item_category_id,location_id,division_id,department_id,ready_to_approve,section_id,requisition_date,store_name,pay_mode,source,cbo_currency,delivery_date,remarks,manual_req,is_approved,req_by,iso_no,priority_id,requisition_id,tenor,justification_value from inv_purchase_requisition_mst where id='$data'" );

	$dataArray=sql_select($sql);
	$requisition_date=$dataArray[0][csf("requisition_date")];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
	//echo $requisition_date."==".$requisition_date_last_year;die;
	
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	// $origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	$designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf("inserted_by")];
	$sql_user=sql_select("select id,user_full_name,employee_code,designation, user_email from user_passwd where id=$inserted_by");
	?>

  	<style type="text/css">
  		@media all
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
	  			border: 1px solid #dccdcd !important;
	  		}
  		}
  	</style>
	<div id="table_row" style="max-width:1300px; margin-left: 2px;">

		<table width="1270" class="rpt_tables">
			<tr class="form_caption">
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left" rowspan="2" width='140'>
				<?
				foreach($data_array as $img_row)
				{
					if ($formate_id==123) 
					{
						?>
						<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='140' align="middle" />
						<?
					}
					else
					{
						?>
						<img src='../<? echo $img_row[csf('image_location')]; ?>' height='70' width='140' align="middle" />
						<?
					}
				}
				?>
				</td>


				<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<td colspan="5" align="center" style="font-size:18px;">
				<?

				//echo show_company($data[0],'',''); //Aziz
				$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
				foreach ($nameArray as $result)
				{
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')];?>
					City No: <? echo $result[csf('city')];?>
					Zip Code: <? echo $result[csf('zip_code')]; ?>
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
					Email Address: <? echo $result[csf('email')];?>
					Website No: <? echo $result[csf('website')];
				}
				$req=explode('-',$dataArray[0][csf('requ_no')]);
				$approved=$dataArray[0][csf('is_approved')];
				?>

				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:24px"><strong><u><? echo $data[2] ?></u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:20px"><strong>Req. No:</strong></td>
				<td width="175px" style="font-size:20px"><strong><? echo $dataArray[0][csf('requ_no')];
				//$req[2].'-'.$req[3]; ?></strong></td>
				<td style="font-size:20px;" width="130"><strong>Req. Date:</strong></td>
				<td style="font-size:20px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
				<td width="125" style="font-size:20px"><strong>Required Date:</strong></td>
				<td width="175px" style="font-size:20px"><? echo change_date_format($dataArray[0][csf("delivery_date")]); ?></td>
				
			</tr>
			<tr>
				<td style="font-size:20px"><strong>Manual Req. Ref.:</strong></td> 
				<td width="175px" style="font-size:20px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
				<td style="font-size:20px"><strong>Department:</strong></td>
				<td width="175px" style="font-size:20px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
				<td style="font-size:20px"><strong>Section:</strong></td>
				<td width="175px" style="font-size:20px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
				
			</tr>
			<tr>
				 <td style="font-size:20px"><strong>Priority:</strong></td><td style="font-size:20px"><? echo $priority_array[$dataArray[0][csf('priority_id')]]; ?></td>
				<td style="font-size:20px"><strong>Store Name:</strong></td><td style="font-size:20px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
				<td style="font-size:20px"><strong>Pay Mode:</strong></td><td style="font-size:20px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
				
			</tr>
			<tr>
				<td style="font-size:20px"><strong>Location:</strong></td> <td style="font-size:20px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
				<td style="font-size:20px"><strong>Currency:</strong></td> <td style="font-size:20px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
				<td style="font-size:20px"><strong>Approval Status:</strong></td> <td style="font-size:20px;color:red;">
					<?
					if($approved==1){
						$approved_status= "Approved"; 
					}else{
						$approved_status= ""; 
					}
					echo $approved_status; ?>
				</td>
				  
				
			   
			</tr>

		</table>
		
	<br>
	<style type="text/css">
		table thead tr th, table tbody tr td{
			wordwrap: break-word;
			break-ward: break-word;
		}
		.paddingStyle td{
			padding: 5px 0px;
		}
		
	</style>

	<table cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" style="border: 1px;font-size: 18px;" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="30" rowspan="2">SL</th>
				<th width="60" rowspan="2">Product ID</th> 
				<th width="100" rowspan="2">Item Group</th> 
				<th width="100" rowspan="2">Item Sub Group</th>
				<th width="220" rowspan="2">Item Description</th>
				<th width="40" rowspan="2">UOM</th>
				<th width="70" rowspan="2">Brand</th>
				<th colspan="2">Stock Status (Qty)</th>
				<th colspan="3">Last Purchase</th>
				<th width="80" rowspan="2">Purchase Req. Qty</th>
				<th width="80" rowspan="2">Approx Rate</th>
				<th width="80" rowspan="2">Approx Value</th>
				<th rowspan="2" align="center">Remarks</th>
			</tr>
			<tr>
				<th width="50" >Stock in Hand</th>
				<!-- <th width="50" >Re-Order Level</th> -->
				<th width="50" >Last 3 Months Consp .</th>
				<th width="50" >Qty</th>
				<th width="50" >Rate</th>
				<!-- <th width="60" >Date</th> -->
				<th width="100" >Supplier</th>
				
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		// $item_sub_group_arr=return_library_array( "select id, sub_group_name from  lib_item_sub_group",'id','sub_group_name');
		$receive_array=array();
		//company_id, item_category_id, item_group_id, sub_group_name, item_description, item_size, model, item_number, item_code
		$i=1;
		$sql= " SELECT a.id, b.id as dtls_id, c.id as prod_id, a.company_id, b.item_category, b.brand_name, b.origin, b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.remarks, b.used_for, b.delivery_date, c.item_account, c.item_category_id, c.item_description, c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock,  c.item_number 
		from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
		where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  
		order by c.item_group_id";
	    //echo $sql;//die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{

			$all_prod_ids.=$row[csf('product_id')].",";
			
			$all_data_array[$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('dtls_id')]]['prod_id'] = $row[csf('prod_id')];
			$all_data_array[$row[csf('dtls_id')]]['company_id'] = $row[csf('company_id')];
			$all_data_array[$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('dtls_id')]]['used_for'] = $row[csf('used_for')];
			//$all_data_array[$row[csf('dtls_id')]]['delivery_date'] = $row[csf('delivery_date')];
			$all_data_array[$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
			$all_data_array[$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			// $all_data_array[$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
			
		}
		
		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;
		
		$prod_sql="select company_id, item_category_id, item_group_id, sub_group_name, item_description, item_size, model, item_number, item_code 
		from product_details_master where status_active=1 and id in ($all_prod_ids)";
		$prod_sql_result=sql_select($prod_sql);
		foreach($prod_sql_result as $row)
		{
			$prod_company=$row[csf("company_id")];
			$prod_category[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
			$prod_group[$row[csf("item_group_id")]]=$row[csf("item_group_id")];
			$pord_description.="'".$row[csf("item_description")]."',";
		}
		$pord_description=chop($pord_description,",");
		$rcv_cond="";
		if($prod_company) $rcv_cond.=" and c.company_id=$prod_company";
		if(count($prod_category)>0) $rcv_cond.=" and c.item_category_id in(".implode(",",$prod_category).")";
		if(count($prod_group)>0) $rcv_cond.=" and c.item_group_id in(".implode(",",$prod_group).")";
		if($pord_description) $rcv_cond.=" and c.item_description in($pord_description)";
		
		$rec_sql="select b.id, b.item_category, b.prod_id, b.transaction_date as transaction_date, b.supplier_id, b.cons_quantity as rec_qty, cons_rate as cons_rate, c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code 
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_basis not in(6) $rcv_cond 
		order by  b.id ";
		//echo  $rec_sql;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$item_key=$row[csf('company_id')]."*".$row[csf('item_category_id')]."*".$row[csf('item_group_id')]."*".$row[csf('sub_group_name')]."*".$row[csf('item_description')]."*".$row[csf('item_size')]."*".$row[csf('model')]."*".$row[csf('item_number')]."*".$row[csf('item_code')];
			$receive_array[$item_key]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$item_key]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$item_key]['rate']=$row[csf('cons_rate')];
			$receive_array[$item_key]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";
		
		$last_month_issue_sql=sql_select("select c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code, sum(b.cons_quantity) as isssue_qty 
		from  inv_transaction b, product_details_master c  
		where b.prod_id=c.id and b.transaction_type=2 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and transaction_date >= add_months(trunc(sysdate,'mm'),-3) $rcv_cond 
		group by c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code");
		
		$last_month_issue_data=array();
		foreach($last_month_issue_sql as $row)
		{
			$item_key=$row[csf('company_id')]."*".$row[csf('item_category_id')]."*".$row[csf('item_group_id')]."*".$row[csf('sub_group_name')]."*".$row[csf('item_description')]."*".$row[csf('item_size')]."*".$row[csf('model')]."*".$row[csf('item_number')]."*".$row[csf('item_code')];
			$last_month_issue_data[$item_key]["prod_id"]=$row[csf("prod_id")];
			$last_month_issue_data[$item_key]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		$receive_last_month_sql=sql_select("select b.prod_id, c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code, sum(cons_quantity) as receive_qty 
		from  inv_transaction b, product_details_master c  
		where b.prod_id=c.id and b.transaction_type=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) $rcv_cond
		group by b.prod_id, c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code");

		$last_month_receive_data=array();
		foreach($receive_last_month_sql as $row)
		{
			$item_key=$row[csf('company_id')]."*".$row[csf('item_category_id')]."*".$row[csf('item_group_id')]."*".$row[csf('sub_group_name')]."*".$row[csf('item_description')]."*".$row[csf('item_size')]."*".$row[csf('model')]."*".$row[csf('item_number')]."*".$row[csf('item_code')];
			$last_month_receive_data[$item_key]["prod_id"]=$row[csf("prod_id")];
			$last_month_receive_data[$item_key]["receive_qty"]=$row[csf("receive_qty")];
		}

		// echo "<pre>";
		// print_r($all_data_array);
		$previos_item_category='';
		$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
		$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
		$last_issue=0;
		$last_receive=0;
		$i=1;
		foreach ($all_data_array as $dtls_id => $row) 
		{
			$item_cat=$row['item_category'];
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$item_key=$row['company_id']."*".$row['item_category']."*".$row['item_group_id']."*".$row['sub_group_name']."*".$row['item_description']."*".$row['item_size']."*".$row['model']."*".$row['item_number']."*".$row['item_code'];

			$quantity=$row['quantity'];
			$quantity_sum += $quantity;
			$amount=$row['amount'];
			//test
			$sub_group_name=$row['sub_group_name'];
			$amount_sum += $amount;

			$current_stock=$row['stock'];
			$current_stock_sum += $current_stock;
			if($db_type==2)
			{
				$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
			}
			if($db_type==0)
			{
				$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
			}
			$last_req_info=explode('_',$last_req_info);
			//print_r($dataaa);

			$item_account=explode('-',$row['item_account']);
			$item_code=$item_account[3];
			/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
			$last_rec_date=$receive_array[$item_key]['transaction_date'];
			$last_rec_qty=$receive_array[$item_key]['rec_qty'];
			$last_rec_rate=$receive_array[$item_key]['rate'];
			$last_rec_supp=$receive_array[$item_key]['supplier_id'];

			
			$prod_id=$row["product_id"];
			if($db_type==0)
			{
				$last_band_model=sql_select(" SELECT b.product_id as prod_id,b.brand_name as brand_name,b.origin as origin,b.model as model
						from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b  where a.id=b.mst_id  and b.product_id in($prod_id)  and a.id!=$update_id
						order by b.id desc limit 1 ");
			}else
			{
				$last_band_model=sql_select("SELECT brand_name,origin,model , prod_id from (
					SELECT rownum ,rs.* from (
					   SELECT b.product_id as prod_id,b.brand_name as brand_name,b.origin as origin,b.model as model
						from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b  where a.id=b.mst_id  and b.product_id in($prod_id)  and a.id!=$update_id
						order by b.id desc
				   ) rs 

				) where rownum <= 1
				 
				");
			}

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" class='paddingStyle' >
				<td align="center"><? echo $i; ?></td>
				<td align="center"><p> <? echo $row["prod_id"];?> </p></td>
				<td ><p> <? echo $item_name_arr[$row["item_group_id"]];?> </p></td>
				<td ><p> <? echo $row["sub_group_name"];?> </p></td>
				<td ><p> <? echo $row["item_description"]?> </p></td>
				<td align="center"><p><? echo $unit_of_measurement[$row["cons_uom"]]; ?></p></td>
				<td ><p><? echo $row["brand_name"];?> </p></td>

				<td align="right"><? echo  number_format($row['stock'],2); ?></td>
				<!-- <td align="right"><p><? echo number_format($row['re_order_label'],0); ?>&nbsp;</p></td> -->
				<td align="right"><? echo number_format($last_month_issue_data[$item_key]["isssue_qty"],2);?></td>

				<td align="right"><? echo number_format($last_rec_qty,2); ?></td>
				<td align="right"><? echo number_format($last_rec_rate,2); ?></td>
				<!-- <td align="center"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></td> -->
				<td align="center"><p><? echo $supplier_array[$last_rec_supp]; ?>&nbsp;</p></td>

				<td align="right"><p><? echo number_format($row['quantity'],2); ?>&nbsp;</p></td>
				<td align="right"><p><? echo number_format($row['rate'],2); ?>&nbsp;</p></td>
				<td align="right"><p><? echo number_format($row['amount'],2); ?>&nbsp;</p></td>
				<td align="right"><? echo $row['remarks']; ?></td>

			</tr>
			<?

			$total_requisition += $row['quantity'];
			$last_qnty += $last_rec_qty;
			$total_stock += $row['stock'];
			$total_amount += $row['amount'];
			// $total_re_order += $row['re_order_label'];
			$total_reqsit_value += $reqsit_value;
			$total_monthly_iss += $issue_avg;
			$total_monthly_rej += $receive_avg;

			$last_issue+=$last_month_issue_data[$item_key]["isssue_qty"];
			$last_receive+=$last_month_receive_data[$item_key]["receive_qty"];

			$Grand_tot_total_amount += $row['amount'];
			$Grand_tot_last_qnty += $last_rec_qty;
			$Grand_tot_reqsit_value += $reqsit_value;
			$Grand_tot_total_stock += $row['stock'];
			$Grand_tot_monthly_iss += $issue_avg;
			$Grand_tot_monthly_rej += $receive_avg;
			$Grand_tot_last_month_issue+=$last_month_issue_data[$item_key]["isssue_qty"];
			$previos_item_category=$item_cat;

			$word_in_hand += $row['quantity']*$last_rec_rate;

			$i++;
				
			
		}
		?>
		<!-- </tbody>
		<tfoot> -->
			<tr bgcolor="#B0C4DE">
				<td align="right" colspan="7"><strong>Total : </strong></td>
				<td align="right"><? echo number_format($total_stock,2); ?></td>
				
				<!-- <td align="right"><? echo number_format($total_re_order,2); ?></td> -->
				<td align="right"><? echo number_format($Grand_tot_last_month_issue,2); ?></td>
				<td align="right"><? echo number_format($last_qnty,2); ?></td>
				<td align="right"></td>
				<td align="right"></td>
				<td align="right" colspan="2"><strong>Grand Total :</strong></td>
				<!-- <td align="right"><?// echo number_format($total_requisition,2); ?></td> -->
				<td align="right"><? echo number_format($Grand_tot_total_amount,2,'.',','); ?></td>
                <td align="right"></td>
			</tr>
		</tbody>
		<!-- </tfoot> -->
	</table>
	<!-- <span><strong>Total Amount (In Word): &nbsp;<? //echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span> -->
	<!-- <span><strong>Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($word_in_hand,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span> -->
	<span><strong>Remarks:&nbsp;</strong><? echo $dataArray[0][csf('remarks')]; ?></span>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br><br><br><br><br>
	<?
	echo signature_table(25, $data[0], "1300px",$cbo_template_id,20,$user_lib_name[$inserted_by]);
	exit();
}


if($action=="purchase_requisition_print_8") // Print Report 6
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	  //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];

	$sql="select a.id, a.requ_no, a.item_category_id, a.requisition_date, a.location_id, a.delivery_date, a.source, a.manual_req, a.department_id, a.section_id, a.store_name, a.pay_mode, a.cbo_currency, a.remarks,a.req_by,a.inserted_by, to_char(a.insert_date, 'DD-MM-YYYY HH:MI:SS AM') as INSERT_DATE, b.user_full_name as USER_FULL_NAME, c.custom_designation as CUSTOM_DESIGNATION from inv_purchase_requisition_mst a left join user_passwd b on b.id = a.inserted_by left join lib_designation c on c.id = b.designation where a.id=$update_id";

	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$designation_library=return_library_array("select id,custom_designation from lib_designation",'id','custom_designation');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

	$sql_user_info=sql_select("select id, user_name, user_full_name, designation, department_id from user_passwd where valid=1");
    foreach ($sql_user_info as $row){
        $user_arr[$row[csf('id')]]['user_name']=$row[csf('user_name')];
        $user_arr[$row[csf('id')]]['user_full_name']=$row[csf('user_full_name')];
        $user_arr[$row[csf('id')]]['designation_id']=$row[csf('designation')];
        $user_arr[$row[csf('id')]]['department_id']=$row[csf('department_id')];
    }

	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];

	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");

	$tblWidth = "1130";
	$com_dtls = fnc_company_location_address($company, $location, 2);

	$sql_approved="select b.approved_by as APPROVED_BY, to_char(b.approved_date, 'dd-mm-yyyy hh:mi:ss am') as APPROVED_DATE from inv_purchase_requisition_mst a, approval_history b where a.id=b.mst_id and a.id=$update_id and a.entry_form=69 and a.is_approved in(1,3) and b.entry_form=1 and b.current_approval_status=1 and a.status_active=1 and a.is_deleted=0";
    $sql_approved_res=sql_select($sql_approved); 
	?>
	<style>
		.bordertbl tr th,.bordertbl tr td{
			border: 1px solid;
			padding: 3px;
		}
	</style>
	<table width="1032" ><!-- <? //echo $tblWidth; ?> -->
		<tr class="form_caption">
			<td  align="left" rowspan="2" width="230">
			<?
			foreach($data_array as $img_row)
			{
				if ($formate_id==169)
				{
					?>
					<img src='../../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
				else
				{
					?>
					<img src='../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
			}
			?>
			</td>
			<td colspan="5" width="350" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $com_dtls[0]; ?></strong></td>

            <!-- <td  align="center" rowspan="2" width="50">&nbsp;</td> -->

            <td width="90" rowspan="2" colspan="4" align="center" style="font-size:25px; margin-bottom:30px; border: 2px solid black;"><strong><? echo $data[2]; ?></strong></td>

            <!-- <tr> <? //echo $location_arr[$dataArray[0][csf('location_id')]]; ?>
            	<td colspan="7" align="center" style="font-size:18px"><strong><u><? //echo $data[2] ?></u></strong></td>
			</tr> -->

		</tr>
		<tr class="form_caption">

			<td colspan="5" align="center" style="font-size:18px;">
			<?
			echo $com_dtls[1];
			//$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			// foreach ($nameArray as $result)
			// {
			// 	echo $result[csf('city')]; //$location_arr[$dataArray[0][csf('location_id')]].', '.
			// }
			$req=explode('-',$dataArray[0][csf('requ_no')]);
			?>
			</td>
		</tr>
		<!-- <tr>
            <td colspan="7" align="center" style="font-size:18px"><strong><u><? //echo $data[2] ?></u></strong></td>
		</tr> -->
    </table>
    <table width="<? echo $tblWidth; ?>" border="1" rules="all" class="bordertbl" >
		<tr>
			<td width="90" style="font-size:16px">Rqsn. No:</td>
			<td style="font-size:16px"><strong><? echo $dataArray[0][csf('requ_no')];?></strong></td>
			<td width="90" style="font-size:16px;">Rqsn. Date:</td>
            <td style="font-size:16px;" ><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td width="80" style="font-size:16px">Del. Date:</td>
            <td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
        </tr>
		<tr>
            <td style="font-size:16px">Business Unit:</td>
            <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
            <td style="font-size:16px">Pay Mode:</td>
            <td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            <td style="font-size:16px">Source:</td>
            <td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
        </tr>
		<tr>
			<td style="font-size:16px">Store Name:</td>
			<td style="font-size:16px"><strong><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></strong></td>
			<td style="font-size:16px">Department:</td>
			<td width="175px" style="font-size:16px"><strong><? echo $department[$dataArray[0][csf('department_id')]]; ?></strong></td>
			<td style="font-size:16px">Section:</td>
			<td width="175px" style="font-size:16px"><strong><? echo $section[$dataArray[0][csf('section_id')]]; ?></strong></td>
		</tr>
		<tr>
		   <td style="font-size:16px">Remarks:</td> <td colspan="3" style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
            <td style="font-size:16px">Req. By:</td>
            <td width="175px" style="font-size:16px"><strong><? echo $dataArray[0][csf('req_by')] ?></strong></td>
		</tr>
	</table>
	<br>
	<table cellspacing="0" width="<? echo $tblWidth; ?>"  border="1" rules="all" class="bordertbl" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th colspan="16" align="center" style="font-size: 20px" ><strong>Item Details</strong></th>
			</tr>
			<tr>
				<th width="30" style="font-size:14px">SL</th>
				<th width="50" style="font-size:14px">Item Code</th>
				<th width="100" style="font-size:14px">Item Category</th>
				<th width="100" style="font-size:14px">Item Group</th>
				<th width="60" style="font-size:14px">Brand</th>
                <th width="60" style="font-size:14px">Origin</th>
				<th width="160" style="font-size:14px">Item Des.</th>
                <th width="60" style="font-size:14px">Item Size</th>
				<th width="55" style="font-size:14px">UOM</th>
				<th width="55" style="font-size:14px">Required Qty.</th>
				<th width="55" style="font-size:14px">Current Stock</th>
				<th width="55" style="font-size:14px">Last Rec. Qty.</th>
				<th width="55" style="font-size:14px">Last Rate</th>
				<th width="90" style="font-size:14px">Last 3 Month Avg. Rec. Rate</th>
				<th width="75" style="font-size:14px">Last Rec. Date</th>
				<th style="font-size:14px">Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		/*$receive_array=array();

		 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}*/

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//var_dump($prev_issue_data);die;

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		$i=1;
		$sql= " SELECT a.id, a.insert_date, a.store_name, b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id,b.brand_name,b.origin, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by b.item_category, c.item_group_id";
		$sql_result=sql_select($sql);
	    //echo $sql;//die;
		foreach ($sql_result as $row)
		{
			$all_prod_ids.=$row[csf('product_id')].",";
			$all_store_ids.=$row[csf('store_name')].",";
		}

		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;

		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;
		$all_store_ids=implode(",",array_unique(explode(",",chop($all_store_ids,","))));
		if($all_store_ids=="") $all_store_ids=0;

		$receive_array=array();
		/*$rec_sql="SELECT b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id in($all_prod_ids) and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by b.prod_id,b.id";*/

		$rec_sql="SELECT b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,order_rate as cons_rate
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.receive_basis not in(6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by b.prod_id, b.id";

		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		$month = date('m'); $year = date("Y");;
		if($db_type==0){
			$month_field="MONTH(transaction_date) as month";
			$year_field="YEAR(insert_date) as year";
		} else if($db_type==2){
			$month_field="to_char(transaction_date,'YYYY') as month,";
			$year_field="to_char(insert_date,'YYYY') as year,";
		} 
		
		//echo "select transaction_date,to_char(transaction_date,'MM') as month from inv_transaction where prod_id in($all_prod_ids) and store_id in($all_store_ids) and is_deleted=0 and is_deleted=0 and transaction_type in (1)  and to_char(transaction_date,'MM') !=$month and to_char(transaction_date,'YYYY') !=$year  order by id desc ";
		//to_char(transaction_date,'MM') as month
		//and to_char(transaction_date,'YYYY') !=$year and store_id in($all_store_ids)
		$lastRecvMonth=return_field_value("transaction_date","inv_transaction","prod_id in($all_prod_ids)  and is_deleted=0 and is_deleted=0 and transaction_type in (1)  and to_char(transaction_date,'MM') !=$month order by id desc","transaction_date");

		//echo "select sum(a.cons_quantity) as qnty , sum(a.cons_amount) as amt , b.id as product_id from  inv_transaction a,  product_details_master b where a.prod_id=b.id and a.prod_id in($all_prod_ids)  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= add_months('$lastRecvMonth',-2) and a.transaction_type in (1) and to_char(a.transaction_date,'MM') !=$month group by b.id order by b.id ";

		$lastThreeMonthDataArr = array(); 
		if($db_type==0)
		{
			$lastThreeMonthData= sql_select("select sum(a.order_qnty) as qnty , sum(a.order_amount) as amt , b.id as product_id from  inv_transaction a, product_details_master b where a.prod_id=b.id and a.status_active=1 and b.status_active=1 and a.prod_id in($all_prod_ids) and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= DATE_ADD('$lastRecvMonth', INTERVAL -2 MONTH) and a.transaction_type in (1) group by b.id order by b.id ");
		}
		else
		{
			$lastThreeMonthData= sql_select("select sum(a.order_qnty) as qnty , sum(a.order_amount) as amt , b.id as product_id from  inv_transaction a, product_details_master b where a.prod_id=b.id and a.prod_id in($all_prod_ids)  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= add_months('$lastRecvMonth',-2) and a.transaction_type in (1) and to_char(a.transaction_date,'MM') !=$month group by b.id order by b.id ");
		}
        foreach ($lastThreeMonthData as $ldata) {

        	$lastThreeMonthDataArr[$ldata[csf("product_id")]] = $ldata[csf("amt")]/$ldata[csf("qnty")];
        }


		/*echo "<pre>";
		print_r($lastThreeMonthDataArr);*/
		foreach($sql_result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$quantity=$row[csf('quantity')];
			$quantity_sum += $quantity;
			$amount=$row[csf('amount')];
			$insert_date = $row[csf("insert_date")];
			//test
			$sub_group_name=$row[csf('sub_group_name')];
			$amount_sum += $amount;

			$current_stock=$row[csf('stock')];
			$current_stock_sum += $current_stock;
			if($db_type==2)
			{
				$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
			}
			if($db_type==0)
			{
				$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
			}
			$last_req_info=explode('_',$last_req_info);
			//print_r($dataaa);

			$item_account=explode('-',$row[csf('item_account')]);
			$item_code=$item_account[3];
			/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
			$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
			$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
			$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];


			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center" style="font-size: 15px;"><? echo $i; ?></td>
				<td style="font-size: 15px;"><div style="word-wrap:break-word;" align="center"><? echo $row[csf('product_id')]; ?></div></td>
                <td style="font-size: 15px;"><? echo $item_category[$row[csf('item_category')]]; ?></td>
                <td style="font-size: 15px;"><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
				<td style="font-size: 15px;"><p><? echo $row[csf("brand_name")];?></p></td>
				<td style="font-size: 15px;"><p><? echo $country_arr[$row[csf("origin")]];?></p></td>
				<td style="font-size: 15px;"><p><? echo $row[csf("item_description")];?></p></td>
                <td style="font-size: 15px;"><p><? echo $row[csf("item_size")];?> &nbsp;</p></td>
				<td align="center" style="font-size: 15px;"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
				<td align="right" style="font-size: 15px;"><p><strong><? echo $row[csf('quantity')]; ?></strong>&nbsp;</p></td>
				<td align="right" style="font-size: 15px;"><p><? echo number_format($row[csf('stock')],2); ?></p></td>
				<td align="right" style="font-size: 15px;"><p><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
				<td align="right" style="font-size: 15px;"><p><? echo number_format($last_rec_rate,2);//$last_req_info[2]; ?>&nbsp;</p></td>
				<td align="right" style="font-size: 15px;" placeholder='<? echo $row[csf('product_id')];?>'><p><? echo number_format(($lastThreeMonthDataArr[$row[csf('product_id')]]),2);?>&nbsp;</p></td>
				<td align="center" style="font-size: 15px;"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
				<td align="right" style="font-size: 15px;"><? echo $row[csf('remarks')]; ?></td>
			</tr>
			<?
			$last_qnty +=$last_rec_qty;
			$total_quantity+=$row[csf('quantity')];
			$total_stock+=$row[csf('stock')];
			$i++;
		}
		?>
		</tbody>
		<!--<tr bgcolor="#dddddd">
			<td align="right" colspan="5"><strong>Total : </strong></td>
			<td align="right" ><? //echo number_format($total_quantity,2); ?></td>
			<td align="right"><? //echo number_format($total_stock,0,'',','); ?></td>
			<td align="right"><? //echo number_format($last_qnty,0,'',','); ?></td>
			<td align="right"></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>-->
	</table>
	<?
		$sql="select approved_by  from approval_history where mst_id=$data[1] and entry_form=1 and un_approved_by=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$last_approved_by = $row[csf('approved_by')];
		}
		echo "<br><b>Approved By: </b>".$user_lib_name[$last_approved_by];


	//echo signature_table(25, $data[0], $tblWidth."px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
		?>
		<table id="signatureTblId" width="901.5" style="padding-top:70px;">
            <tr>
                <td style="text-align: center; font-size: 18px;" width="345">
                    <strong><?=$dataArray[0]['USER_FULL_NAME']?></strong>
                    <br>
                    <strong><?=$dataArray[0]['CUSTOM_DESIGNATION']?></strong>
                    <br>
                    <?=$dataArray[0]['INSERT_DATE']?>
                </td>
                <td width="95"></td>
                <td style="text-align: center; font-size: 18px;" width="345">
                    <strong><? echo $user_arr[$sql_approved_res[0]['APPROVED_BY']]['user_full_name']; ?></strong>
                    <br>
                    <strong><? echo $designation_library[$user_arr[$sql_approved_res[0]['APPROVED_BY']]['designation_id']]; ?></strong>
                    <br>
                    <? echo $sql_approved_res[0]['APPROVED_DATE']; ?>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Prepared by</strong></td>
                <td width="75"></td>
                <td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Approved by</strong></td>
            </tr>
        </table>
		<?
	if($db_type==0)
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=137 and status_active=1 and is_deleted=0";
        }
        else
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=13 and status_active=1 and is_deleted=0";
		}
		//echo $approval_status;//die;
		$approval_status=sql_select($approval_status);
		//var_dump($approval_status);
		$sql="select updated_by,inserted_by,company_id,is_approved  from inv_purchase_requisition_mst where id=$data[1]";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('updated_by')]!=0){$PreparedBy = $row[csf('updated_by')];}
			else{$PreparedBy = $row[csf('inserted_by')];}
			$company_name=$row[csf('company_id')];//approved_by
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
			//$is_approved=$is_approved;//approved_by
		}

	   //$last_authority = return_field_value("user_id", "electronic_approval_setup", " page_id=412 and entry_form=2 and company_id=$company_name order by sequence_no desc");



        if($approval_status[0][csf('approval_need')] == 1){
            if($is_approved==1){
				echo '<style > body{ background-image: url("../img/approved.gif"); } </style>';
			}else{
				echo '<style > body{ background-image: url("../img/draft.gif"); } </style>';
			}
		}

	exit();
}
if($action=="purchase_requisition_print_26") // Print 21
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	  //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];

	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,req_by,inserted_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];

	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");

	$tblWidth = "1130";
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<style>
		.bordertbl tr th,.bordertbl tr td{
			border: 1px solid;
			padding: 3px;
		}
	</style>
	<table width="1032" ><!-- <? //echo $tblWidth; ?> -->
		<tr class="form_caption">
			<td  align="left" rowspan="2" width="230">
			<?
			foreach($data_array as $img_row)
			{
				if ($formate_id==169)
				{
					?>
					<img src='../../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
				else
				{
					?>
					<img src='../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
			}
			?>
			</td>
			<td colspan="5" width="350" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $com_dtls[0]; ?></strong></td>

            <!-- <td  align="center" rowspan="2" width="50">&nbsp;</td> -->

            <td width="90" rowspan="2" colspan="4" align="center" style="font-size:25px; margin-bottom:30px; border: 2px solid black;"><strong><? echo $data[2]; ?></strong></td>

            <!-- <tr> <? //echo $location_arr[$dataArray[0][csf('location_id')]]; ?>
            	<td colspan="7" align="center" style="font-size:18px"><strong><u><? //echo $data[2] ?></u></strong></td>
			</tr> -->

		</tr>
		<tr class="form_caption">

			<td colspan="5" align="center" style="font-size:18px;">
			<?
			echo $com_dtls[1];
			//$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			// foreach ($nameArray as $result)
			// {
			// 	echo $result[csf('city')]; //$location_arr[$dataArray[0][csf('location_id')]].', '.
			// }
			$req=explode('-',$dataArray[0][csf('requ_no')]);
			?>
			</td>
		</tr>
		<!-- <tr>
            <td colspan="7" align="center" style="font-size:18px"><strong><u><? //echo $data[2] ?></u></strong></td>
		</tr> -->
    </table>
    <table width="<? echo $tblWidth; ?>" border="1" rules="all" class="bordertbl" >
		<tr>
			<td width="90" style="font-size:16px">Rqsn. No:</td>
			<td style="font-size:16px"><strong><? echo $dataArray[0][csf('requ_no')];?></strong></td>
			<td width="90" style="font-size:16px;">Rqsn. Date:</td>
            <td style="font-size:16px;" ><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td width="80" style="font-size:16px">Del. Date:</td>
            <td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
        </tr>
		<tr>
            <td style="font-size:16px">Business Unit:</td>
            <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
            <td style="font-size:16px">Pay Mode:</td>
            <td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            <td style="font-size:16px">Source:</td>
            <td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
        </tr>
		<tr>
			<td style="font-size:16px">Store Name:</td>
			<td style="font-size:16px"><strong><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></strong></td>
			<td style="font-size:16px">Department:</td>
			<td width="175px" style="font-size:16px"><strong><? echo $department[$dataArray[0][csf('department_id')]]; ?></strong></td>
			<td style="font-size:16px">Section:</td>
			<td width="175px" style="font-size:16px"><strong><? echo $section[$dataArray[0][csf('section_id')]]; ?></strong></td>
		</tr>
		<tr>
		   <td style="font-size:16px">Remarks:</td> <td colspan="3" style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
            <td style="font-size:16px">Manual Req.:</td>
            <td width="175px" style="font-size:16px"><strong><? echo $dataArray[0][csf('manual_req')] ?></strong></td>
		</tr>
	</table>
	<br>
	<table cellspacing="0" width="<? echo $tblWidth; ?>"  border="1" rules="all" class="bordertbl" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th colspan="16" align="center" style="font-size: 20px" ><strong>Item Details</strong></th>
			</tr>
			<tr>
				<th width="30" style="font-size:14px">SL</th>
				<th width="50" style="font-size:14px">Item Code</th>
				<th width="100" style="font-size:14px">Item Category</th>
				<th width="100" style="font-size:14px">Item Group</th>
				<th width="60" style="font-size:14px">Brand</th>
                <th width="60" style="font-size:14px">Origin</th>
				<th width="160" style="font-size:14px">Item Des.</th>
                <th width="60" style="font-size:14px">Item Size</th>
				<th width="55" style="font-size:14px">UOM</th>
				<th width="55" style="font-size:14px">Required Qty.</th>
				<th width="55" style="font-size:14px">Current Stock</th>
				<th width="75" style="font-size:14px">Last Rec. Date</th>
				<th style="font-size:14px">Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		/*$receive_array=array();

		 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}*/

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//var_dump($prev_issue_data);die;

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		$i=1;
		$sql= " SELECT a.id, a.insert_date, a.store_name, b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id,b.brand_name,b.origin, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by b.item_category, c.item_group_id";
		$sql_result=sql_select($sql);
	    //echo $sql;//die;
		foreach ($sql_result as $row)
		{
			$all_prod_ids.=$row[csf('product_id')].",";
			$all_store_ids.=$row[csf('store_name')].",";
		}

		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;

		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;
		$all_store_ids=implode(",",array_unique(explode(",",chop($all_store_ids,","))));
		if($all_store_ids=="") $all_store_ids=0;

		$receive_array=array();
		/*$rec_sql="SELECT b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id in($all_prod_ids) and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by b.prod_id,b.id";*/

		$rec_sql="SELECT b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,order_rate as cons_rate
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by b.prod_id, b.id";

		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		$month = date('m'); $year = date("Y");;
		if($db_type==0){
			$month_field="MONTH(transaction_date) as month";
			$year_field="YEAR(insert_date) as year";
		} else if($db_type==2){
			$month_field="to_char(transaction_date,'YYYY') as month,";
			$year_field="to_char(insert_date,'YYYY') as year,";
		} 
		
		//echo "select transaction_date,to_char(transaction_date,'MM') as month from inv_transaction where prod_id in($all_prod_ids) and store_id in($all_store_ids) and is_deleted=0 and is_deleted=0 and transaction_type in (1)  and to_char(transaction_date,'MM') !=$month and to_char(transaction_date,'YYYY') !=$year  order by id desc ";
		//to_char(transaction_date,'MM') as month
		//and to_char(transaction_date,'YYYY') !=$year and store_id in($all_store_ids)
		$lastRecvMonth=return_field_value("transaction_date","inv_transaction","prod_id in($all_prod_ids)  and is_deleted=0 and is_deleted=0 and transaction_type in (1)  and to_char(transaction_date,'MM') !=$month order by id desc","transaction_date");

		//echo "select sum(a.cons_quantity) as qnty , sum(a.cons_amount) as amt , b.id as product_id from  inv_transaction a,  product_details_master b where a.prod_id=b.id and a.prod_id in($all_prod_ids)  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= add_months('$lastRecvMonth',-2) and a.transaction_type in (1) and to_char(a.transaction_date,'MM') !=$month group by b.id order by b.id ";

		$lastThreeMonthDataArr = array(); 
		if($db_type==0)
		{
			$lastThreeMonthData= sql_select("select sum(a.order_qnty) as qnty , sum(a.order_amount) as amt , b.id as product_id from  inv_transaction a, product_details_master b where a.prod_id=b.id and a.status_active=1 and b.status_active=1 and a.prod_id in($all_prod_ids) and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= DATE_ADD('$lastRecvMonth', INTERVAL -2 MONTH) and a.transaction_type in (1) group by b.id order by b.id ");
		}
		else
		{
			$lastThreeMonthData= sql_select("select sum(a.order_qnty) as qnty , sum(a.order_amount) as amt , b.id as product_id from  inv_transaction a, product_details_master b where a.prod_id=b.id and a.prod_id in($all_prod_ids)  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= add_months('$lastRecvMonth',-2) and a.transaction_type in (1) and to_char(a.transaction_date,'MM') !=$month group by b.id order by b.id ");
		}
        foreach ($lastThreeMonthData as $ldata) {

        	$lastThreeMonthDataArr[$ldata[csf("product_id")]] = $ldata[csf("amt")]/$ldata[csf("qnty")];
        }


		/*echo "<pre>";
		print_r($lastThreeMonthDataArr);*/
		foreach($sql_result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$quantity=$row[csf('quantity')];
			$quantity_sum += $quantity;
			$amount=$row[csf('amount')];
			$insert_date = $row[csf("insert_date")];
			//test
			$sub_group_name=$row[csf('sub_group_name')];
			$amount_sum += $amount;

			$current_stock=$row[csf('stock')];
			$current_stock_sum += $current_stock;
			if($db_type==2)
			{
				$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
			}
			if($db_type==0)
			{
				$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
			}
			$last_req_info=explode('_',$last_req_info);
			//print_r($dataaa);

			$item_account=explode('-',$row[csf('item_account')]);
			$item_code=$item_account[3];
			/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
			$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
			$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
			$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];


			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center" style="font-size: 15px;"><? echo $i; ?></td>
				<td style="font-size: 15px;"><div style="word-wrap:break-word;" align="center"><? echo $row[csf('product_id')]; ?></div></td>
                <td style="font-size: 15px;"><? echo $item_category[$row[csf('item_category')]]; ?></td>
                <td style="font-size: 15px;"><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
				<td style="font-size: 15px;"><p><? echo $row[csf("brand_name")];?></p></td>
				<td style="font-size: 15px;"><p><? echo $country_arr[$row[csf("origin")]];?></p></td>
				<td style="font-size: 15px;"><p><? echo $row[csf("item_description")];?></p></td>
                <td style="font-size: 15px;"><p><? echo $row[csf("item_size")];?> &nbsp;</p></td>
				<td align="center" style="font-size: 15px;"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
				<td align="right" style="font-size: 15px;"><p><strong><? echo $row[csf('quantity')]; ?></strong>&nbsp;</p></td>
				<td align="right" style="font-size: 15px;"><p><? echo number_format($row[csf('stock')],2); ?></p></td>
				<td align="center" style="font-size: 15px;"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>

				<td align="right" style="font-size: 15px;"><? echo $row[csf('remarks')]; ?></td>
			</tr>
			<?
			$last_qnty +=$last_rec_qty;
			$total_quantity+=$row[csf('quantity')];
			$total_stock+=$row[csf('stock')];
			$i++;
		}
		?>
		</tbody>
		<!--<tr bgcolor="#dddddd">
			<td align="right" colspan="5"><strong>Total : </strong></td>
			<td align="right" ><? //echo number_format($total_quantity,2); ?></td>
			<td align="right"><? //echo number_format($total_stock,0,'',','); ?></td>
			<td align="right"><? //echo number_format($last_qnty,0,'',','); ?></td>
			<td align="right"></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>-->
	</table>
	<?
		$sql="select approved_by  from approval_history where mst_id=$data[1] and entry_form=1 and un_approved_by=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$last_approved_by = $row[csf(approved_by)];
		}
		echo "<br><b>Approved By: </b>".$user_lib_name[$last_approved_by];
		?>

		<div style="margin-top: -25px;">
			<? echo signature_table(25, $data[0], $tblWidth."px",$cbo_template_id,'',$user_lib_name[$inserted_by]); ?>
		</div>

		<?
	if($db_type==0)
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=137 and status_active=1 and is_deleted=0";
        }
        else
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=13 and status_active=1 and is_deleted=0";
		}
		//echo $approval_status;//die;
		$approval_status=sql_select($approval_status);
		//var_dump($approval_status);
		$sql="select updated_by,inserted_by,company_id,is_approved  from inv_purchase_requisition_mst where id=$data[1]";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('updated_by')]!=0){$PreparedBy = $row[csf('updated_by')];}
			else{$PreparedBy = $row[csf('inserted_by')];}
			$company_name=$row[csf('company_id')];//approved_by
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
			//$is_approved=$is_approved;//approved_by
		}

	   //$last_authority = return_field_value("user_id", "electronic_approval_setup", " page_id=412 and entry_form=2 and company_id=$company_name order by sequence_no desc");



        if($approval_status[0][csf('approval_need')] == 1){
            if($is_approved==1){
				echo '<style > body{ background-image: url("../img/approved.gif"); } </style>';
			}else{
				echo '<style > body{ background-image: url("../img/draft.gif"); } </style>';
			}
		}
		
	exit();
}

if($action=="purchase_requisition_print_22") // Print 19
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	  //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];

	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,req_by,inserted_by,priority_id from inv_purchase_requisition_mst where id=$update_id";
	//echo $sql;
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];

	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");

	$tblWidth = "1130";
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<style>
		.bordertbl tr th,.bordertbl tr td{
			border: 1px solid;
			padding: 3px;
		}
	</style>
	<table width="1032" ><!-- <? //echo $tblWidth; ?> -->
		<tr class="form_caption">
			<td  align="left" rowspan="2" width="230">
			<?
			foreach($data_array as $img_row)
			{
				if ($formate_id==169)
				{
					?>
					<img src='../../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
				else
				{
					?>
					<img src='../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
			}
			?>
			</td>
			<td colspan="5" width="350" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $com_dtls[0]; ?></strong></td>

            <!-- <td  align="center" rowspan="2" width="50">&nbsp;</td> -->
<!-- 
            <td width="90" rowspan="2" colspan="4" align="center" style="font-size:25px; margin-bottom:30px; border: 2px solid black;"><strong><? //echo $data[2]; ?></strong></td> -->

            <!-- <tr> <? //echo $location_arr[$dataArray[0][csf('location_id')]]; ?>
            	<td colspan="7" align="center" style="font-size:18px"><strong><u><? //echo $data[2] ?></u></strong></td>
			</tr> -->

		</tr>
		<tr class="form_caption">

			<td colspan="5" align="center" style="font-size:18px;">
			<?
			echo $com_dtls[1];
			//$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			// foreach ($nameArray as $result)
			// {
			// 	echo $result[csf('city')]; //$location_arr[$dataArray[0][csf('location_id')]].', '.
			// }
			$req=explode('-',$dataArray[0][csf('requ_no')]);
			?>
			</td>
		</tr>
		<tr>
			<td>&nbsp; </td>
            <td colspan="7" align="center" style="font-size:18px"><strong><u><? echo $data[2] ?></u></strong></td>
		</tr>
		<tr>
			<td>&nbsp; </td>
		</tr>
		
    </table>
    <table width="<? echo $tblWidth; ?>" border="1" rules="all" class="bordertbl" >
		<tr>
			<td width="90" style="font-size:16px">Rqsn. No:</td>
			<td width="460" style="font-size:16px"><strong><? echo $dataArray[0][csf('requ_no')];?></strong></td>
			<td width="90" style="font-size:16px;">Rqsn. Date:</td>
            <td width="460" style="font-size:16px;" ><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
        </tr>
		<tr>
            <td width="90" style="font-size:16px">Business Unit:</td>
            <td width="460" style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
            <td width="90" style="font-size:16px">Department:</td>
            <td width="460" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
        </tr>
		<tr>
			<td width="90" style="font-size:16px">Store Name:</td>
			<td width="460" style="font-size:16px"><strong><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></strong></td>
			<td width="90" style="font-size:16px">Section:</td>
			<td width="460" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
		</tr>
		<tr>
		   <td width="90" style="font-size:16px">Importance:</td>
		   <td width="460" style="font-size:16px"><? echo $priority_array[$dataArray[0][csf('priority_id')]]; ?></td>
           <td width="90" style="font-size:16px">Req. By:</td>
           <td width="460" style="font-size:16px"><? echo $dataArray[0][csf('req_by')] ?></td>
		</tr>
		<tr>
		   <td width="90" style="font-size:16px">Remarks:</td> <td colspan="3" style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
		</tr>
	</table>
	<br>
	<table cellspacing="0" width="<? echo $tblWidth; ?>"  border="1" rules="all" class="bordertbl" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th colspan="16" align="center" style="font-size: 20px" ><strong>Item Details</strong></th>
			</tr>
			<tr>
				<th width="30" style="font-size:14px">SL</th>
				<th width="100" style="font-size:14px">Item Name</th>
				<th width="160" style="font-size:14px">Item Des.</th>
                <th width="100" style="font-size:14px">Item Size</th>
				<th width="80" style="font-size:14px">UOM</th>
				<th width="100" style="font-size:14px">Required Qty.</th>
				<th width="160" style="font-size:14px">Sub Group Name</th>
				<th width="100" style="font-size:14px">Model</th>
				<th width="100" style="font-size:14px">Current stok</th>
				<th style="font-size:14px">Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		/*$receive_array=array();

		 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}*/

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//var_dump($prev_issue_data);die;

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		$i=1;
		$sql= " SELECT a.id, a.insert_date, a.store_name, b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id,b.brand_name,b.origin, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by b.item_category, c.item_group_id";
		$sql_result=sql_select($sql);
	    //echo $sql;//die;
		foreach ($sql_result as $row)
		{
			$all_prod_ids.=$row[csf('product_id')].",";
			$all_store_ids.=$row[csf('store_name')].",";
		}

		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;

		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;
		$all_store_ids=implode(",",array_unique(explode(",",chop($all_store_ids,","))));
		if($all_store_ids=="") $all_store_ids=0;

		$receive_array=array();
		/*$rec_sql="SELECT b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id in($all_prod_ids) and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by b.prod_id,b.id";*/

		$rec_sql="SELECT b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,order_rate as cons_rate
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by b.prod_id, b.id";

		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		$month = date('m'); $year = date("Y");;
		if($db_type==0){
			$month_field="MONTH(transaction_date) as month";
			$year_field="YEAR(insert_date) as year";
		} else if($db_type==2){
			$month_field="to_char(transaction_date,'YYYY') as month,";
			$year_field="to_char(insert_date,'YYYY') as year,";
		} 
		
		//echo "select transaction_date,to_char(transaction_date,'MM') as month from inv_transaction where prod_id in($all_prod_ids) and store_id in($all_store_ids) and is_deleted=0 and is_deleted=0 and transaction_type in (1)  and to_char(transaction_date,'MM') !=$month and to_char(transaction_date,'YYYY') !=$year  order by id desc ";
		//to_char(transaction_date,'MM') as month
		//and to_char(transaction_date,'YYYY') !=$year and store_id in($all_store_ids)
		$lastRecvMonth=return_field_value("transaction_date","inv_transaction","prod_id in($all_prod_ids)  and is_deleted=0 and is_deleted=0 and transaction_type in (1)  and to_char(transaction_date,'MM') !=$month order by id desc","transaction_date");

		//echo "select sum(a.cons_quantity) as qnty , sum(a.cons_amount) as amt , b.id as product_id from  inv_transaction a,  product_details_master b where a.prod_id=b.id and a.prod_id in($all_prod_ids)  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= add_months('$lastRecvMonth',-2) and a.transaction_type in (1) and to_char(a.transaction_date,'MM') !=$month group by b.id order by b.id ";

		$lastThreeMonthDataArr = array(); 
		if($db_type==0)
		{
			$lastThreeMonthData= sql_select("select sum(a.order_qnty) as qnty , sum(a.order_amount) as amt , b.id as product_id from  inv_transaction a, product_details_master b where a.prod_id=b.id and a.status_active=1 and b.status_active=1 and a.prod_id in($all_prod_ids) and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= DATE_ADD('$lastRecvMonth', INTERVAL -2 MONTH) and a.transaction_type in (1) group by b.id order by b.id ");
		}
		else
		{
			$lastThreeMonthData= sql_select("select sum(a.order_qnty) as qnty , sum(a.order_amount) as amt , b.id as product_id from  inv_transaction a, product_details_master b where a.prod_id=b.id and a.prod_id in($all_prod_ids)  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= add_months('$lastRecvMonth',-2) and a.transaction_type in (1) and to_char(a.transaction_date,'MM') !=$month group by b.id order by b.id ");
		}
        foreach ($lastThreeMonthData as $ldata) {

        	$lastThreeMonthDataArr[$ldata[csf("product_id")]] = $ldata[csf("amt")]/$ldata[csf("qnty")];
        }


		/*echo "<pre>";
		print_r($lastThreeMonthDataArr);*/
		foreach($sql_result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$quantity=$row[csf('quantity')];
			$quantity_sum += $quantity;
			$amount=$row[csf('amount')];
			$insert_date = $row[csf("insert_date")];
			//test
			$sub_group_name=$row[csf('sub_group_name')];
			$amount_sum += $amount;

			$current_stock=$row[csf('stock')];
			$current_stock_sum += $current_stock;
			if($db_type==2)
			{
				$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
			}
			if($db_type==0)
			{
				$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
			}
			$last_req_info=explode('_',$last_req_info);
			//print_r($dataaa);

			$item_account=explode('-',$row[csf('item_account')]);
			$item_code=$item_account[3];
			/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
			$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
			$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
			$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];


			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center" style="font-size: 15px;"><? echo $i; ?></td>
                <td align="center" style="font-size: 15px;"><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
				<td align="center" style="font-size: 15px;"><p><? echo $row[csf("item_description")];?></p></td>
                <td align="center" style="font-size: 15px;"><p><? echo $row[csf("item_size")];?> &nbsp;</p></td>
				<td align="center" style="font-size: 15px;"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
				<td align="right" style="font-size: 15px;"><p><strong><? echo number_format($row[csf('quantity')],2,'.',','); ?></strong>&nbsp;</p></td>
				<td align="center" style="font-size: 15px;"><p><strong><? echo $sub_group_name; ?></strong>&nbsp;</p></td>
				<td align="center" style="font-size: 15px;"><p><strong><? echo $row[csf('model')]; ?></strong>&nbsp;</p></td>
				<td align="right" style="font-size: 15px;"><p><strong><? echo number_format($row[csf('stock')],2,'.',','); ?></strong>&nbsp;</p></td>
				<td align="center" style="font-size: 15px;"><? echo $row[csf('remarks')]; ?></td>
			</tr>
			<?
			$last_qnty +=$last_rec_qty;
			$total_quantity+=$row[csf('quantity')];
			$total_stock+=$row[csf('stock')];
			$i++;
		}
		?>
		</tbody>
		<tr bgcolor="#dddddd">
			<td align="right" colspan="5"><strong>Total : </strong></td>
			<td align="right" ><? echo number_format($total_quantity,2); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($total_stock,2,'.',','); ?></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<?
		$sql="select approved_by  from approval_history where mst_id=$data[1] and entry_form=1 and un_approved_by=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$last_approved_by = $row[csf(approved_by)];
		}
		echo "<br><b>Approved By: </b>".$user_lib_name[$last_approved_by];


	echo signature_table(25, $data[0], $tblWidth."px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	if($db_type==0)
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=137 and status_active=1 and is_deleted=0";
        }
        else
        {
            $approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($insert_date, "", "",1)."' and company_id='$data[0]')) and page_id=13 and status_active=1 and is_deleted=0";
		}
		//echo $approval_status;//die;
		$approval_status=sql_select($approval_status);
		//var_dump($approval_status);
		$sql="select updated_by,inserted_by,company_id,is_approved  from inv_purchase_requisition_mst where id=$data[1]";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('updated_by')]!=0){$PreparedBy = $row[csf('updated_by')];}
			else{$PreparedBy = $row[csf('inserted_by')];}
			$company_name=$row[csf('company_id')];//approved_by
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
			//$is_approved=$is_approved;//approved_by
		}

	   //$last_authority = return_field_value("user_id", "electronic_approval_setup", " page_id=412 and entry_form=2 and company_id=$company_name order by sequence_no desc");



        if($approval_status[0][csf('approval_need')] == 1){
            if($is_approved==1){
				echo '<style > body{ background-image: url("../img/approved.gif"); } </style>';
			}else{
				echo '<style > body{ background-image: url("../img/draft.gif"); } </style>';
			}
		}

	exit();
}


if($action=="purchase_requisition_print_4") // Print Report 4
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print($data[5]);
	 //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$sql="select id, requ_no, item_category_id,is_approved, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency,brand_name,model, remarks,req_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
	$requisition_date=$dataArray[0][csf("requisition_date")];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
	//echo $requisition_date."==".$requisition_date_last_year;die;
	
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

	$pay_cash=$dataArray[0][csf('pay_mode')];
	?>

  	<style type="text/css">
  		@media print
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
  			border: 1px solid #dccdcd !important;
  		}
  		}
  	</style>
	<div id="table_row" style="max-width:1020px; margin: 0 auto;">

		<table width="1000" class="rpt_tables">
			<tr class="form_caption">
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left" rowspan="2">
				<?
				foreach($data_array as $img_row)
				{
					if ($formate_id==123) 
					{
						?>
						<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
						<?
					}
					else
					{
						?>
						<img src='../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
						<?
					}
				}
				?>
				</td>


				<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<td colspan="5" align="center" style="font-size:18px;">
				<?

				//echo show_company($data[0],'',''); //Aziz
				$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
				foreach ($nameArray as $result)
				{
					?>
					<? echo $result[csf('plot_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')];?>
					City No: <? echo $result[csf('city')];?>
					Zip Code: <? echo $result[csf('zip_code')]; ?>
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
					Email Address: <? echo $result[csf('email')];?>
					Website No: <? echo $result[csf('website')];
				}
				$req=explode('-',$dataArray[0][csf('requ_no')]);
				?>

				</td>
			</tr>
			<tr>
				<td>&nbsp; </td>
				<td colspan="5" align="center" style="font-size:22px"><strong><u><? echo $data[2] ?></u></strong></td>
			</tr>
			<tr>
				<td width="100" style="font-size:16px"><strong>Req. No:</strong></td>
				<td width="200" style="font-size:16px"><strong><? echo $dataArray[0][csf('requ_no')];
				//$req[2].'-'.$req[3]; ?></strong></td>
				<td style="font-size:16px;" width="125"><strong>Req. Date:</strong></td><td style="font-size:16px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
				<td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
				<td style="font-size:16px"><strong>Department:</strong></td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
				<td style="font-size:16px"><strong>Section:</strong></td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
			</tr>
			<tr>
				 <td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
				<td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
				<td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
				<td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
			   <td style="font-size:16px"><strong>Req. By:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('req_by')]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px; text-align: center; color:red;" colspan="2">
					<?  
					if($dataArray[0][csf('is_approved')]==1)
					{
						echo "Approved";

					}
				 	?></td>

				<td colspan="4"></td>
			</tr>
            <tr>
            	<td style="font-size:16px"><strong>Remarks:</strong></td> <td colspan="5" style="font-size:16px; word-break:break-all;"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
		</table>
		<br>
		<?
		//$margin='-133px;';
		//echo $th_span.'='.$cash_span.'='.$span.'='.$margin.'='.$widths.'='.$cash;
		?>

		<table cellspacing="0" width="1060"  border="0" rules="all" class="rpt_table rpt_tables" >
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<!-- <th width="980" align="center" ><strong>Item Details</strong></th> -->
					<th colspan="19" width="1240" align="center" ><strong>ITEM DETAILS</strong></th>
				</tr>
				<tr>
					<th width="20">SL</th>
					<th width="80">Item Group</th>
					<th width="180">Item Des & Item Size</th>
					<th width="120">Req. For</th>
					<th width="35">UOM</th>
					<th width="40">Req. Qty.</th>
					<th width="40">Rate</th>
					<th width="40">Amount</th>
					<th width="40">Stock</th>
					<th width="50">Last Rec. Date</th>
					<th width="40">Last Rec. Qty.</th>
					<th width="40">Last Rate</th>
					<th width="55">Req. Value</th>
					<th width="60">Avg. Monthly issue</th>
					<th width="60">Avg. Monthly Rec.</th>
					<th width="90">Brand Name</th>
					<th width="90">Model Name</th>
					<th width="90">Supplier</th>
					<th width="70">Remarks</th>

				</tr>
			</thead>
			<tbody>
			<?
			$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
			$receive_array=array();
			/*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
			$rec_sql_result= sql_select($rec_sql);
			foreach($rec_sql_result as $row)
			{
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
				//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			}*/
			
			$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate 
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and a.entry_form in (4,20) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			order by  b.prod_id,b.id";
			$rec_sql_result= sql_select($rec_sql);
			foreach($rec_sql_result as $row)
			{
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
				$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
			}

			if($db_type==2)
			{
				$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
			}
			elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

			$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_issue_data=array();
			foreach($issue_sql as $row)
			{
				$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
			}

			//var_dump($prev_issue_data);die;
			$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_receive_data=array();
			foreach($receive_sql as $row)
			{
				$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
			}

			$i=1; $k=1;
			// echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
			$sql= " SELECT a.id,a.is_approved,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label,c.order_uom from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0 order by b.item_category,c.item_group_id, c.item_description";
			$sql_result=sql_select($sql);
			//echo $sql;die;
			$item_category_array=array();
			$category_wise_data = array();
			/*foreach ($sql_result as $row) {
				$category_wise_data[$row[csf("item_category")]]['item_group_id']=$row[csf("item_group_id")];
				$category_wise_data[$row[csf("item_category")]]['item_size']=$row[csf("item_size")];
				$category_wise_data[$row[csf("item_category")]]['item_description']=$row[csf("item_description")];
				$category_wise_data[$row[csf("item_category")]]['required_for']=$row[csf("required_for")];
				$category_wise_data[$row[csf("item_category")]]['cons_uom']=$row[csf("cons_uom")];
				$category_wise_data[$row[csf("item_category")]]['quantity']=$row[csf("quantity")];
				$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
				$category_wise_data[$row[csf("item_category")]]['amount']=$row[csf("amount")];
				$category_wise_data[$row[csf("item_category")]]['stock']=$row[csf("stock")];
				$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
				$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
			}*/
			foreach($sql_result as $row)
			{

				if (!in_array($row[csf("item_category")],$item_category_array) )
				{
					if($k!=1)
					{
						?>
						<tr bgcolor="#dddddd">
	                        <td align="right" colspan="7"><strong>Sub Total : </strong></td>
	                        <td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
	                        <td align="right"><? echo number_format($total_stock,0,'',','); ?></td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right"><? echo number_format($total_last_rec_qty,0,'',','); ?></td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
	                        <td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
	                        <td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right">&nbsp;</td>
	                    </tr>
						<tr bgcolor="#dddddd">
							<td colspan="19" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
						</tr>
						<?
						$total_amount=$total_stock=$total_last_rec_qty=$total_reqsit_value=$total_issue_avg=$total_receive_avg=0;
					}
					else
					{
						?>
						<tr bgcolor="#dddddd">
							<td colspan="19" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
						</tr>
						<?
					}
					$item_category_array[]=$row[csf('item_category')];
					$k++;
				}

				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row[csf('quantity')];
				$quantity_sum += $quantity;
				$amount=$row[csf('amount')];
				//test
				$sub_group_name=$row[csf('sub_group_name')];
				$amount_sum += $amount;
				$remarks=$row[csf('remarks')];
				$current_stock=$row[csf('stock')];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);

				$item_account=explode('-',$row[csf('item_account')]);

				$item_code=$item_account[3];
				/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
				$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
				$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];


				?>
				<tr style="margin: 0px;padding: 0px; font-size:20px" class="main_tbl" bgcolor="<? echo $bgcolor; ?>">
					<td  width="20" align="center"><? echo $i; ?></td>
	                <td  width="80"><p style="font-size: 13px"><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
					<td  width="180" ><p style="font-size: 13px"> <? echo $row[csf("item_description")].', '.$row[csf("item_size")];?> </p></td>
					<td width="120"><p style="font-size: 13px">  <? echo $use_for[$row[csf("required_for")]]; ?></p></td>
					<td width="35" align="center"><p style="font-size: 13px">  <? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p></td>
					<td width="40" align="right"><p style="font-size: 13px"><? echo $row[csf('quantity')]; ?>&nbsp;</p></td>
					<td  width="40" align="right"><? echo $row[csf('rate')]; ?></td>
					<td  width="40" align="right"><? echo $row[csf('amount')]; ?></td>
					<td  width="40" align="right"><p style="font-size: 13px"><? echo number_format($row[csf('stock')],2); ?></p></td>
					<td  width="50" align="center"><p style="font-size: 13px"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
					<td  width="40" align="right"><p style="font-size: 13px"><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
					<td  width="40" align="right"><p style="font-size: 13px"><? echo $last_rec_rate; ?>&nbsp;</p></td>
					<td  width="55" align="right"><p style="font-size: 13px"><? echo number_format($row[csf('quantity')]*$last_rec_rate,0,'',',') ?></p></td>
					<td  width="60" align="right" title="Based on last one year transaction">
					<?
					$min_issue_date=$prev_issue_data[$row[csf("product_id")]]["transaction_date"];
					if($min_issue_date=="")
					{
						echo number_format(0,2);
					}
					else
					{
						$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
						$year_issue_total=$prev_issue_data[$row[csf("product_id")]]["isssue_qty"];
						$issue_avg=$year_issue_total/$month_issue_diff;
						echo number_format($issue_avg,2);
					}
					?>
					</td>
					<td  width="60" align="right" title="Based on last one year transaction">
					<?
					$min_receive_date=$prev_receive_data[$row[csf("product_id")]]["transaction_date"];
					if($min_receive_date=="")
					{
						echo number_format(0,2);
					}
					else
					{
						$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
						$year_receive_total=$prev_receive_data[$row[csf("product_id")]]["receive_qty"];
						$receive_avg=$year_receive_total/$month_receive_diff;
						echo number_format($receive_avg,2);
					}
					?>
					</td>
					<td  width="90" align="right"><? echo $row[csf('brand_name')]; ?></td>
					<td  width="90" align="right"><? echo $row[csf('model')]; ?></td>
					<td  width="90" align="center"><p style="font-size: 13px"><? echo $supplier_array[$last_rec_supp];?>&nbsp;</p></td>
					<td  width="70" align="left" style="word-break:break-all;"><p style="font-size: 13px"><? echo $remarks; ?>&nbsp;</p></td>
				</tr>
				<?

				$total_amount+=$row[csf('amount')];
				$total_stock+=$row[csf('stock')];
				$total_last_rec_qty +=$last_rec_qty;
				$total_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
				$total_issue_avg +=$issue_avg;
				$total_receive_avg +=$receive_avg;

				$Grand_tot_total_amount+=$row[csf('amount')];
				$Grand_tot_total_stock+=$row[csf('stock')];
				$Grand_tot_last_qnty +=$last_rec_qty;
				$Grand_tot_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
				$Grand_tot_issue_avg +=$issue_avg;
				$Grand_tot_receive_avg +=$receive_avg;

				$i++;
			}
			?>
			</tbody>
			<tr bgcolor="#dddddd">
				<td align="right" colspan="7"><strong>Sub Total : </strong></td>
				<td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
				<td align="right"><? echo number_format($total_stock,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($total_last_rec_qty,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
				<td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
				<td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>

			<tr bgcolor="#dddddd">
				<td align="right" colspan="7"><strong>Grand Sub Total : </strong></td>
				<td align="right"><? echo number_format($Grand_tot_total_amount,0,'',','); ?></td>
				<td align="right"><? echo number_format($Grand_tot_total_stock,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($Grand_tot_reqsit_value,0,'',','); ?></td>
				<td align="right"><? echo number_format($Grand_tot_issue_avg,0,'',','); ?></td>
				<td align="right"><? echo number_format($Grand_tot_receive_avg,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
			<tr bgcolor="#dddddd">
				<td align="right" colspan="9"><strong>Grand Total Amount in Word: </strong></td>
				<td align="left" colspan="10">&nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></td>
			</tr>

		</table>
		<br>
	<?
	$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
    $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=1 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

    foreach ($approved_his_sql as $key => $row)
    {
    	$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
    	if ($row[csf('un_approved_date')]!='')
    	{
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
    	}
    }

    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    if(count($approved_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px; margin-left: 20px;">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <thead>
                	<tr>
                		<th colspan="4">Approval Status</th>
                	</tr>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Designation</th>
	                    <th width="100">Approval Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_sql as $key => $value)
                {
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
                    </tr>
                    <?
                    $sl++;
                }
                ?>
            </table>
        </div>
        <?
    }

	if(count($approved_his_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px; margin-left: 20px;">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <thead>
                	<tr>
                		<th colspan="6">Approval / Un-Approval History </th>
                	</tr>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="150">Approved / Un-Approved</th>
	                    <th width="150">Designation</th>
	                    <th width="50">Approval Status</th>
	                    <th width="150">Reason for Un-Approval</th>
	                    <th width="150">Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_his_sql as $key => $value)
                {
                	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                	?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="20"><? echo $sl; ?></td>
                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="50">Yes</td>
                        <td width="150"><? echo $unapproved_request_arr[$value[csf("mst_id")]]; ?></td>
                        <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    </tr>
                    <?
				    $sl++;
                    $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                    $un_approved_date=$un_approved_date[0];
                    if($db_type==0) //Mysql
                    {
                        if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }
                    else
                    {
                        if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }

                    if($un_approved_date!="")
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
	                        <td width="20"><? echo $sl; ?></td>
	                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
	                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
	                        <td width="50">No</td>
	                        <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
	                        <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);
							echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    	</tr>

						<?
						$sl++;
					}
                }
                ?>
            </table>
        </div>
        <?
    }
	?>
	<div style="margin-top:-80px;"></div>
	</div>
	<?
	
	echo signature_table(25, $data[0], "1100px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	exit();
}
/**
 * Print report for AKH
 */
if($action=="purchase_requisition_print_4_akh") // Print Report AKH
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print($data[5]);
	 //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	
	$company=$data[0];
	$location=$data[7];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,req_by,inserted_by,is_approved,division_id from inv_purchase_requisition_mst where id=$update_id";
	//echo $sql;
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');

	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>

  	<style type="text/css">
  		@media print
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
  			border: 1px solid #dccdcd !important;
  		}
  		}
  	</style>
	<div id="table_row" style="max-width:1020px; margin: 0 auto;">

		<table width="1000" class="rpt_tables">
			<tr class="form_caption">
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left" rowspan="2">
				<?
				foreach($data_array as $img_row)
				{
					if ($formate_id==123)
					{
						?>
						<img src='../../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
						<?
					}
					else
					{
						?>
						<img src='../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
						<?
					}
				}
				?>
				</td>


				<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<td colspan="5" align="center" style="font-size:18px;">
				<?
				echo $com_dtls[1];
				$req=explode('-',$dataArray[0][csf('requ_no')]);
				?>

				</td>
			</tr>
			<tr>
				<td>&nbsp; </td>
				<td colspan="5" align="center" style="font-size:22px"><strong><u><? echo $data[2] ?></u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:16px"><strong>Req. No:</strong></td>
				<td width="175px" style="font-size:16px"><strong><? echo $dataArray[0][csf('requ_no')];
				//$req[2].'-'.$req[3]; ?></strong></td>
				<td style="font-size:16px;" width="130"><strong>Req. Date:</strong></td><td style="font-size:16px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
				<td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
				<td style="font-size:16px"><strong>Division:</strong></td><td style="font-size:16px"><? echo $division_library[$dataArray[0][csf('division_id')]]; ?></td>
				<td style="font-size:16px"><strong>Department:</strong></td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Section:</strong></td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
				 <td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
				<td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
				<td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
				<td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Remarks:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
				<td style="font-size:16px"><strong>Req. By:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('req_by')]; ?></td>
				<td colspan="4"></td>
			</tr>
			<tr>
				<td  colspan="5" style="font-size:16px; text-align: center; color:red;">
					<strong>
					<?
						if($dataArray[0][csf('is_approved')] == 1)
						{
							echo "Approved";
						}
						else if($dataArray[0][csf('is_approved')] == 3)
						{
							echo "Partial Approved";
						}
					?>
					</strong>
				</td>
			</tr>
		</table>
		<br>
		<?
		//$margin='-133px;';

		//echo $th_span.'='.$cash_span.'='.$span.'='.$margin.'='.$widths.'='.$cash;
		?>

		<table cellspacing="0" width="980"  border="0" rules="all" class="rpt_table rpt_tables" >
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<!-- <th width="980" align="center" ><strong>Item Details</strong></th> -->
					<th colspan="17" width="980" align="center" ><strong>ITEM DETAILS</strong></th>
				</tr>
				<tr>
					<th width="20">SL</th>
					<th width="80">Item Group</th>
					<th width="180">Item Des & Item Size</th>
					<th width="40">Req. For</th>
					<th width="35">UOM</th>
					<th width="40">Req. Qty.</th>
					<th width="40">Rate</th>
					<th width="40">Amount</th>
					<th width="40">Stock</th>
					<th width="50">Last Rec. Date</th>
					<th width="30">Last Rec. Qty.</th>
					<th width="40">Last Rate</th>
					<th width="45">Req. Value</th>
					<th width="60">3 Month Avg. issue</th>
					<th width="60">3 Month Avg. Rec.</th>
					<th width="110">Supplier</th>
					<th width="70">Remarks</th>

				</tr>
			</thead>
			<tbody>
			<?
			$sql= "SELECT a.id, b.item_category, b.brand_name, b.origin, b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.remarks, c.item_account, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label 
			from  inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b, product_details_master c 
			where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0
			order by b.item_category,c.item_group_id, c.item_size"; //don't changed first order by parameter 
			$sql_result=sql_select($sql);
			//echo $sql;//die;
			foreach ($sql_result as $val) {
				$prod_ids .= $val[csf("product_id")].',';
			}

			$allPoId = implode(",", array_flip(array_flip(explode(',', rtrim($prod_ids,',')))));
			/*foreach ($sql_result as $row) {
				$category_wise_data[$row[csf("item_category")]]['item_group_id']=$row[csf("item_group_id")];
				$category_wise_data[$row[csf("item_category")]]['item_size']=$row[csf("item_size")];
				$category_wise_data[$row[csf("item_category")]]['item_description']=$row[csf("item_description")];
				$category_wise_data[$row[csf("item_category")]]['required_for']=$row[csf("required_for")];
				$category_wise_data[$row[csf("item_category")]]['cons_uom']=$row[csf("cons_uom")];
				$category_wise_data[$row[csf("item_category")]]['quantity']=$row[csf("quantity")];
				$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
				$category_wise_data[$row[csf("item_category")]]['amount']=$row[csf("amount")];
				$category_wise_data[$row[csf("item_category")]]['stock']=$row[csf("stock")];
				$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
				$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
			}*/		
			

			//and a.entry_form=20 
			$receive_array=array();
			$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty, b.cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($allPoId) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.prod_id, b.id";
			$rec_sql_result= sql_select($rec_sql);
			foreach($rec_sql_result as $row)
			{
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
				$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
			}

			if($db_type==2)
			{
				//echo strtotime(change_date_format($pc_date))-31536000;die;
				//echo date('d-M-Y',strtotime(change_date_format($pc_date))-31536000);die;
				$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
			}
			elseif($db_type==0){ 
				$cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";
			}

			$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from inv_transaction where prod_id in($allPoId) and transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_issue_data=array();
			foreach($issue_sql as $row)
			{
				$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
			}			
			
			$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where prod_id in($allPoId) and transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_receive_data=array();
			//var_dump($receive_sql);//die;
			foreach($receive_sql as $row)
			{
				$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
			}

			$i=1; $k=1;
			$item_category_array=array();

			foreach($sql_result as $row)
			{

				if (!in_array($row[csf("item_category")],$item_category_array) )
				{
					if($k!=1)
					{
						?>
						<tr bgcolor="#dddddd">
	                        <td align="right" colspan="7"><strong>Sub Total : </strong></td>
	                        <td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
	                        <td align="right"><? echo number_format($total_stock,0,'',','); ?></td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right"><? echo number_format($total_last_rec_qty,0,'',','); ?></td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
	                        <td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
	                        <td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right">&nbsp;</td>
	                    </tr>
						<tr bgcolor="#dddddd">
							<td colspan="17" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
						</tr>
						<?
						$total_amount=$total_stock=$total_last_rec_qty=$total_reqsit_value=$total_issue_avg=$total_receive_avg=0;
					}
					else
					{
						?>
						<tr bgcolor="#dddddd">
							<td colspan="17" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
						</tr>
						<?
					}
					$item_category_array[]=$row[csf('item_category')];
					$k++;
				}

				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row[csf('quantity')];
				$quantity_sum += $quantity;
				$amount=$row[csf('amount')];
				//test
				$sub_group_name=$row[csf('sub_group_name')];
				$amount_sum += $amount;
				$remarks=$row[csf('remarks')];
				$current_stock=$row[csf('stock')];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);

				$item_account=explode('-',$row[csf('item_account')]);
				$item_code=$item_account[3];
				/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
				$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
				$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];


				?>
				<tr style="margin: 0px;padding: 0px;" class="main_tbl" bgcolor="<? echo $bgcolor; ?>" style="font-size:20px">
					<td  width="20" align="center"><? echo $i; ?></td>
	                <td  width="80"><p style="font-size: 13px"><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
					<td  width="180" ><p style="font-size: 13px"> <? echo $row[csf("item_description")].', '.$row[csf("item_size")];?> </p></td>
					<td width="40"><p style="font-size: 13px">  <? echo $row[csf("required_for")]; ?></p></td>
					<td width="35" align="center"><p style="font-size: 13px">  <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
					<td width="40" align="right"><p style="font-size: 13px"><? echo $row[csf('quantity')]; ?>&nbsp;</p></td>
					<td  width="40" align="right"><? echo $row[csf('rate')]; ?></td>
					<td  width="40" align="right"><? echo $row[csf('amount')]; ?></td>
					<td  width="40" align="right"><p style="font-size: 13px"><? echo number_format($row[csf('stock')],2); ?></p></td>
					<td  width="50" align="center"><p style="font-size: 13px"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
					<td  width="40" align="right"><p style="font-size: 13px"><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
					<td  width="40" align="right"><p style="font-size: 13px"><? echo $last_rec_rate; ?>&nbsp;</p></td>
					<td  width="55" align="right"><p style="font-size: 13px">
					<? echo number_format($last_rec_qty*$last_rec_rate,0,'',',') ?></p>
					</td>
					<td  width="60" align="right">
					<?
					$min_issue_date=$prev_issue_data[$row[csf("product_id")]]["transaction_date"];
					if($min_issue_date=="")
					{
						echo number_format(0,2);
					}
					else
					{
						$month_issue_diff=datediff('q',$min_issue_date,$pc_date);
						$year_issue_total=$prev_issue_data[$row[csf("product_id")]]["isssue_qty"];
						$issue_avg=$year_issue_total/$month_issue_diff;
						echo number_format($issue_avg,2);
					}
					?>
					</td>
					<td  width="60" align="right">
					<?
					$min_receive_date=$prev_receive_data[$row[csf("product_id")]]["transaction_date"];
					
					if($min_receive_date=="")
					{
						echo number_format(0,2);
					}
					else
					{
						 //$difference =strtotime($pc_date,0)  - strtotime($min_receive_date,0); 
						//echo $quarters_difference = floor($difference / 8035200);die(" nabiza");
						$month_receive_diff=datediff('q',$min_receive_date,$pc_date);
						$year_receive_total=$prev_receive_data[$row[csf("product_id")]]["receive_qty"];
						//echo $month_receive_diff." __ ".$year_receive_total." _- ";
						$receive_avg=$year_receive_total/$month_receive_diff;
						echo number_format($receive_avg,2);
					}
					?>
					</td>
					<td  width="90" align="center"><p style="font-size: 13px"><? echo $supplier_array[$last_rec_supp];?>&nbsp;</p></td>
					<td  width="70" align="left"><p style="font-size: 13px"><? echo $remarks; ?>&nbsp;</p></td>
				</tr>
				<?

				$total_amount+=$row[csf('amount')];
				$total_stock+=$row[csf('stock')];
				$total_last_rec_qty +=$last_rec_qty;
				$total_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
				$total_issue_avg +=$issue_avg;
				$total_receive_avg +=$receive_avg;
				$Grand_tot_total_amount+=$row[csf('amount')];
				$Grand_tot_total_stock+=$row[csf('stock')];
				$Grand_tot_last_qnty +=$last_rec_qty;
				$Grand_tot_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
				$Grand_tot_issue_avg +=$issue_avg;
				$Grand_tot_receive_avg +=$receive_avg;
				$i++;
			}
			?>
			</tbody>
			<tr bgcolor="#dddddd">
				<td align="right" colspan="7"><strong>Sub Total : </strong></td>
				<td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
				<td align="right"><? echo number_format($total_stock,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($total_last_rec_qty,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
				<td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
				<td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>

			<tr bgcolor="#dddddd">
				<td align="right" colspan="7"><strong>Grand Sub Total : </strong></td>
				<td align="right"><? echo number_format($Grand_tot_total_amount,0,'',','); ?></td>
				<td align="right"><? echo number_format($Grand_tot_total_stock,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right"><? echo number_format($Grand_tot_reqsit_value,0,'',','); ?></td>
				<td align="right"><? echo number_format($Grand_tot_issue_avg,0,'',','); ?></td>
				<td align="right"><? echo number_format($Grand_tot_receive_avg,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
			<tr bgcolor="#dddddd">
				<td align="right" colspan="7"><strong>Grand Total Amount in Word: </strong></td>
				<td align="left" colspan="10">&nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></td>
			</tr>

		</table>
	</div>
	<?
	echo signature_table(25, $data[0], "1100px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	exit();
}

if($action=="purchase_requisition_print_10") // Print Report 8
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print($data[5]);
	 //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency,is_approved, remarks,req_by,iso_no,inserted_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');	
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	
	//echo "test";die;

	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$show_item=str_replace("'","",$data[5]);

	//echo $show_item.' DD';
	if($show_item==1)
	{
		$row_span_td=16;
		$row_span_td_tot=10;
	}
	else
	{
		$row_span_td=13;
		$row_span_td_tot=7;
	}
	?>

  	<style type="text/css">
  		@media print
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
  			border: 1px solid #dccdcd !important;
  		}
  		}
  	</style>
	<div id="table_row" style="max-width:1100px; margin: 0 auto;">

		<table width="1100" class="rpt_tables">
			<tr class="form_caption">
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				//echo "select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1";
				/*if($dataArray[0][csf('is_approved')]==1 || $dataArray[0][csf('is_approved')]==3){
					$margin="margin-left:350px";
					$colspan=4;
				}
				else{
					$colspan=5;
					$margin='';
				} */
				?>
				<td  align="left" rowspan="2">
				<?
				foreach($data_array as $img_row)
				{
					if ($formate_id==227)
					{
						?>
						<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
						<?
					}
					else
					{
						?>
						<img src='../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
						<?
					}
				}
				?>
				</td>


				<td colspan="5" align="center" style="font-size:28px; <? echo $margin; ?>"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				<?
				/*if($dataArray[0][csf('is_approved')]==1 || $dataArray[0][csf('is_approved')]==3) 
				{
					?>
					<td style="font-size:18px; text-align: right; color:red;">
						<strong>
						<?
							if($dataArray[0][csf('is_approved')] == 1)
							{
								echo "Approved";
							}
							else if($dataArray[0][csf('is_approved')] == 3)
							{
								echo "Partial Approved";
							}
						?>
						</strong>
					</td>
					<?
				}*/
				?>
			</tr>
			<tr class="form_caption">

				<td colspan="5" align="center" style="font-size:18px;">
				<?

				//echo show_company($data[0],'',''); //Aziz
				$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
				foreach ($nameArray as $result)
				{
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')];?>
					City No: <? echo $result[csf('city')];?>
					Zip Code: <? echo $result[csf('zip_code')]; ?>
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
					Email Address: <? echo $result[csf('email')];?>
					Website No: <? echo $result[csf('website')];
				}
				$req=explode('-',$dataArray[0][csf('requ_no')]);
				?>

				</td>
			</tr>
			<tr>
				<td>&nbsp; </td>
				<td colspan="5" align="center" style="font-size:22px"><strong><u><? echo $data[2] ?></u></strong></td>
			</tr>
			<tr>
				<td width="100" style="font-size:16px">Req. No:</td>
				<td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('requ_no')];
				//$req[2].'-'.$req[3]; ?></td>
				<td style="font-size:16px;" width="130">Req. Date:</td><td style="font-size:16px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
				<td width="125" style="font-size:16px">Source:</td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px">Manual Req.:</td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
				<td style="font-size:16px">Department:</td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
				<td style="font-size:16px">Section:</td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
			</tr>
			<tr>
				 <td style="font-size:16px">Del. Date:</td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
				<td style="font-size:16px">Store Name:</td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
				<td style="font-size:16px">Pay Mode:</td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px">Location:</td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
				<td style="font-size:16px">Currency:</td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
				<td style="font-size:16px">Req. By:</td> <td style="font-size:16px"><? echo $dataArray[0][csf('req_by')]; ?></td>
				<td></td>
			</tr>
				<td style="font-size:16px">ISO No.:</td> <td align="right" style="font-size:16px; padding-right: 35px;" ><? echo $dataArray[0][csf('iso_no')]; ?></td>
			<td style="font-size:16px">Remarks:</td> <td style="font-size:16px" colspan="4"><strong><? echo $dataArray[0][csf('remarks')]; ?></strong></td>
			<tr>
			</tr>
			<tr>
				<td  colspan="5" style="font-size:18px; text-align: center; color:red;">
					<strong>
					<?
						if($dataArray[0][csf('is_approved')] == 1)
						{
							echo "Approved";
						}
						else if($dataArray[0][csf('is_approved')] == 3)
						{
							echo "Partial Approved";
						}
					?>
					</strong>
				</td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1100"  border="0" rules="all" class="rpt_table rpt_tables" >
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th colspan="<? echo $row_span_td;?>" width="1100" align="center" ><strong>ITEM DETAILS</strong></th>
				</tr>
				<tr>
					<th width="20">SL</th>
					<th width="80">Item Group</th>
					<th width="180">Item Des & Item Size</th>
					<th width="80">Req. For</th>
					<th width="35">UOM</th>
					<th width="40">Req. Qty.</th>
					<th width="40">Stock</th>
					<?
					if($show_item==1)
					{
						?>
						<th width="50">Last Rec. Date</th>
						<th width="40">Last Rec. Qty.</th>
						
						<th width="40">Last Rate</th>
						
						<th width="55">Req. Value</th>
						<?
					}
					?>
					
					<th width="60">Avg. Monthly issue</th>
					<th width="60">Avg. Monthly Rec.</th>
					<th width="90">Supplier</th>
					<th width="100">Vehicle No</th>
					<th width="70">Remarks</th>
				</tr>
			</thead>
			<tbody>
			<?
			$sql= " select a.id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.vehicle_no, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0 order by b.item_category,c.item_group_id";
			$sql_result=sql_select($sql);
			$selected_prod_id=array();
			foreach($sql_result as $row)
			{
				$selected_prod_id[$row[csf("product_id")]]=$row[csf("product_id")];
			}
			
			if(count($selected_prod_id)>0)
			{
				$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
				$receive_array=array();
				$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate 
				from inv_receive_master a, inv_transaction b 
				where a.id=b.mst_id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id in(".implode(",",$selected_prod_id).")
				order by  b.prod_id,b.id";
				//and a.entry_form=20
				//echo $rec_sql;
				$rec_sql_result= sql_select($rec_sql);
				
				foreach($rec_sql_result as $row)
				{
					$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
					$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
					$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
					$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
				}
				//echo "test5";die;
				// echo '<pre>';
				// print_r($receive_array);
	
				
				if($db_type==2)
				{
					$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
				}
				elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";
	
				$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and prod_id in(".implode(",",$selected_prod_id).") and transaction_date between $cond_date group by prod_id");
				$prev_issue_data=array();
				foreach($issue_sql as $row)
				{
					$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
					$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
					$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
				}
				//var_dump($prev_issue_data);die;
				$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and prod_id in(".implode(",",$selected_prod_id).") and transaction_date between $cond_date group by prod_id");
				$prev_receive_data=array();
				foreach($receive_sql as $row)
				{
					$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
					$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
					$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
				}
			}
			

			$i=1; $k=1;


			// echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
			 
			//echo $sql;die;
			$item_category_array=array();
			$category_wise_data = array();
			foreach($sql_result as $row)
			{
				if (!in_array($row[csf("item_category")],$item_category_array) )
				{
					if($k!=1)
					{
						?>
						<tr bgcolor="#dddddd">
	                        <td align="right" colspan="5"><strong>Category Total : </strong></td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right">&nbsp;</td>
							<?
							// if($show_item==1)
							// {
								//$row_span_less_td="2";
							?>
	                        <td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
							<?
							//}
							?>
	                        <td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
	                        <td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right">&nbsp;</td>
	                        <td align="right">&nbsp;</td>
	                    </tr>
						<tr bgcolor="#dddddd">
							<td colspan="<? echo $row_span_td;?>" align="left" ><b>Category: <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
						</tr>
						<?
						$total_amount=$total_stock=$total_last_rec_qty=$total_reqsit_value=$total_issue_avg=$total_receive_avg=0;
					}
					else
					{
						?>
						<tr bgcolor="#dddddd">
							<td colspan="<? echo $row_span_td;?>" align="left" ><b>Category: <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
						</tr>
						<?
					}
					$item_category_array[]=$row[csf('item_category')];
					$k++;
				}

				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row[csf('quantity')];
				$quantity_sum += $quantity;
				$amount=$row[csf('amount')];
				//test
				$sub_group_name=$row[csf('sub_group_name')];
				$amount_sum += $amount;
				$remarks=$row[csf('remarks')];
				$vehicle_no=$row[csf('vehicle_no')];
				$current_stock=$row[csf('stock')];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);
				$item_account=explode('-',$row[csf('item_account')]);
				$item_code=$item_account[3];
				/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
				$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
				//$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
				$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];
				?>
				<tr style="margin: 0px;padding: 0px; font-size:20px" class="main_tbl" bgcolor="<? echo $bgcolor; ?>">
					<td  width="20" align="center"><? echo $i; ?></td>
	                <td  width="80"><p style="font-size: 13px"><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
					<td  width="180" ><p style="font-size: 13px"> <? echo $row[csf("item_description")].', '.$row[csf("item_size")];?> </p></td>
					<td width="80"><p style="font-size: 13px">  <? echo $use_for[$row[csf("required_for")]]; ?></p></td>
					<td width="35" align="center"><p style="font-size: 13px">  <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
					<td width="40" align="right"><p style="font-size: 13px"><? echo $row[csf('quantity')]; ?>&nbsp;</p></td>
					<td  width="40" align="right"><p style="font-size: 13px"><? echo number_format($row[csf('stock')],2); ?></p></td>
					<?
					if($show_item==1)
					{
						?>
						<td  width="50" align="center"><p style="font-size: 13px"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
						<td  width="40" align="right"><p style="font-size: 13px"><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
						
						<td  width="40" align="right"><p style="font-size: 13px"><? echo $last_rec_rate; ?>&nbsp;</p></td>
							
						<td  width="55" align="right"><p style="font-size: 13px"><? echo number_format($row[csf('quantity')]*$last_rec_rate,0,'',',') ?></p></td>
						<?
					}
					?>
					
					<td  width="60" align="right">
					<?
					$min_issue_date=$prev_issue_data[$row[csf("product_id")]]["transaction_date"];
					if($min_issue_date=="")
					{
						echo number_format(0,2);
					}
					else
					{
						$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
						$year_issue_total=$prev_issue_data[$row[csf("product_id")]]["isssue_qty"];
						$issue_avg=$year_issue_total/$month_issue_diff;
						echo number_format($issue_avg,2);
					}
					?>
					</td>
					<td  width="60" align="right">
					<?
					$min_receive_date=$prev_receive_data[$row[csf("product_id")]]["transaction_date"];
					if($min_receive_date=="")
					{
						echo number_format(0,2);
					}
					else
					{
						$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
						$year_receive_total=$prev_receive_data[$row[csf("product_id")]]["receive_qty"];
						$receive_avg=$year_receive_total/$month_receive_diff;
						echo number_format($receive_avg,2);
					}
					?>
					</td>
					<td  width="90" align="center"><p style="font-size: 13px"><? echo $supplier_array[$last_rec_supp];?>&nbsp;</p></td>
					<td  width="100" align="center"><p style="font-size: 13px"><? echo $vehicle_no; ?>&nbsp;</p></td>
					<td  width="70" align="left"><p style="font-size: 13px"><? echo $remarks; ?>&nbsp;</p></td>
				</tr>
				<?

				$total_amount+=$row[csf('amount')];
				$total_stock+=$row[csf('stock')];
				$total_last_rec_qty +=$last_rec_qty;
				$total_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
				$total_issue_avg +=$issue_avg;
				$total_receive_avg +=$receive_avg;

				$Grand_tot_total_amount+=$row[csf('amount')];
				$Grand_tot_total_stock+=$row[csf('stock')];
				$Grand_tot_last_qnty +=$last_rec_qty;
				$Grand_tot_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
				$Grand_tot_issue_avg +=$issue_avg;
				$Grand_tot_receive_avg +=$receive_avg;

				$i++;
			}
			?>
			</tbody>
			<tr bgcolor="#dddddd">
				<td align="left" colspan="<? echo $row_span_td_tot;?>"><strong>Category Total : </strong></td>
				<?
				if($show_item==1)
				{
				?>
				<td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
				<?
				}
				?>
				<td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
				<td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>

			<tr bgcolor="#dddddd">
				<td align="left" colspan="<? echo $row_span_td_tot;?>"><strong>Grand Total : </strong></td>
				<?
				if($show_item==1)
				{
				?>
				<td align="right"><? echo number_format($Grand_tot_reqsit_value,0,'',','); ?></td>
				<?
				}
				?>
				<td align="right"><? echo number_format($Grand_tot_issue_avg,0,'',','); ?></td>
				<td align="right"><? echo number_format($Grand_tot_receive_avg,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
		</table>
		<?
		if($show_item==1)
		{
		?>
		<table cellspacing="0" width="1100"  border="0" rules="all" >
			<tr>
				<td width="215"><strong>Grand Total Required Amount in Word: </strong></td>
				<td>&nbsp;<? echo number_to_words(number_format($Grand_tot_reqsit_value,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></td>

			</tr>
		</table>
		<?
		}
		echo signature_table(25, $data[0], "1050px",$cbo_template_id,0,$user_lib_name[$inserted_by]);
		?>
	</div>
	<?

	exit();
}


if($action=="purchase_requisition_print_9")
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print($data[5]);
	 //print_r($data);
	$update_id=$data[1];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];

	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,req_by,inserted_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>



		<!--<style>

		@media print{
			html>body table.rpt_table {
			border:solid 1px;
			margin-left:12px;
			}

		}
		.rpt_table tbody tr td {
				font-size: 11pt !important;
			}

		</style>

		</style>-->
		<style type="text/css">
			@media print
			{
			.main_tbl td {
					margin: 0px;padding: 0px;
				}
			}
		</style>
		<div id="table_row" style="width:1020px";>


		<table width="1000" class="rpt_tables">
			<tr class="form_caption">
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left" rowspan="2">
				<?
				foreach($data_array as $img_row)
				{
					?>
					<img src='../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
				?>
				</td>


				<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<td colspan="5" align="center" style="font-size:18px;">
				<?
				echo $com_dtls[1];
				//echo show_company($data[0],'',''); //Aziz
				//$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
				$req=explode('-',$dataArray[0][csf('requ_no')]);
				?>

				</td>
			</tr>
			<tr>
			<td>&nbsp; </td>
				<td colspan="5" align="center" style="font-size:22px"><strong><u><? echo $data[2] ?></u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:16px"><strong>Req. No:</strong></td>
				<td width="175px" style="font-size:16px"><strong><? echo $dataArray[0][csf('requ_no')];
				//$req[2].'-'.$req[3]; ?></strong></td>
				<td style="font-size:16px;" width="130"><strong>Req. Date:</strong></td><td style="font-size:16px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
				<td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
				<td style="font-size:16px"><strong>Department:</strong></td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
				<td style="font-size:16px"><strong>Section:</strong></td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
				<td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
				<td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
				<td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
			<td style="font-size:16px"><strong>Remarks:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px"><strong>Req. By:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('req_by')]; ?></td>
				<td colspan="4"></td>
			</tr>
		</table>
		<br>
		<?
		//$margin='-133px;';

		//echo $th_span.'='.$cash_span.'='.$span.'='.$margin.'='.$widths.'='.$cash;
		?>

		<table cellspacing="0" width="980"  border="0" rules="all" class="rpt_table rpt_tables" >
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<!-- <th width="980" align="center" ><strong>Item Details</strong></th> -->
					<th colspan="17" width="980" align="center" ><strong>ITEM DETAILS</strong></th>
				</tr>
				<tr>
					<th width="20">SL</th>
					<th width="80">Item Group</th>
					<th width="180">Item Des & Item Size</th>
					<th width="40">Req. For</th>
					<th width="35">UOM</th>
					<th width="40">Req. Qty.</th>
					<th width="40">Rate</th>
					<th width="40">Amount</th>
					<th width="40">Stock</th>
					<th width="50">Last Rec. Date</th>
					<th width="40">Last Rec. Qty.</th>
					<th width="40">Last Rate</th>
					<th width="55">Req. Value</th>
					<th width="60">Avg. Monthly issue</th>
					<th width="60">Avg. Monthly Rec.</th>
					<th width="90">Supplier</th>
					<th width="70">Remarks</th>

				</tr>
			</thead>
			<tbody>
			<?
			$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
			$receive_array=array();
			/*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
			$rec_sql_result= sql_select($rec_sql);
			foreach($rec_sql_result as $row)
			{
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
				//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			}*/
			$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form in (4,20) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
			$rec_sql_result= sql_select($rec_sql);
			foreach($rec_sql_result as $row)
			{
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
				$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
			}

			if($db_type==2)
			{
				$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
			}
			elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";
	echo $pc_date.' : ';
	echo $diff_d = date('d-M-Y',strtotime(change_date_format($pc_date))-7884000);//last 3 month 7776000 or 7884000
		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date >= add_months(sysdate,-3) group by prod_id");
		//requisition_date >= '".$row[csf('requisition_date')]."'
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//var_dump($prev_issue_data);//die;

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		$i=1; $k=1;
		// echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
		 $sql= " select a.id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0 order by b.item_category,c.item_group_id";
		$sql_result=sql_select($sql);
		//echo $sql;die;
		$item_category_array=array();
		$category_wise_data = array();
		/*foreach ($sql_result as $row) {
			$category_wise_data[$row[csf("item_category")]]['item_group_id']=$row[csf("item_group_id")];
			$category_wise_data[$row[csf("item_category")]]['item_size']=$row[csf("item_size")];
			$category_wise_data[$row[csf("item_category")]]['item_description']=$row[csf("item_description")];
			$category_wise_data[$row[csf("item_category")]]['required_for']=$row[csf("required_for")];
			$category_wise_data[$row[csf("item_category")]]['cons_uom']=$row[csf("cons_uom")];
			$category_wise_data[$row[csf("item_category")]]['quantity']=$row[csf("quantity")];
			$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
			$category_wise_data[$row[csf("item_category")]]['amount']=$row[csf("amount")];
			$category_wise_data[$row[csf("item_category")]]['stock']=$row[csf("stock")];
			$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
			$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
		}*/
		foreach($sql_result as $row)
		{

			if (!in_array($row[csf("item_category")],$item_category_array) )
			{
				if($k!=1)
				{
					?>
					<tr bgcolor="#dddddd">
                        <td align="right" colspan="7"><strong>Sub Total : </strong></td>
                        <td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
                        <td align="right"><? echo number_format($total_stock,0,'',','); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($total_last_rec_qty,0,'',','); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
                        <td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
                        <td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
					<tr bgcolor="#dddddd">
						<td colspan="17" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
					</tr>
					<?
					$total_amount=$total_stock=$total_last_rec_qty=$total_reqsit_value=$total_issue_avg=$total_receive_avg=0;
				}
				else
				{
					?>
					<tr bgcolor="#dddddd">
						<td colspan="17" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
					</tr>
					<?
				}
				$item_category_array[]=$row[csf('item_category')];
				$k++;
			}

			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$quantity=$row[csf('quantity')];
			$quantity_sum += $quantity;
			$amount=$row[csf('amount')];
			//test
			$sub_group_name=$row[csf('sub_group_name')];
			$amount_sum += $amount;
			$remarks=$row[csf('remarks')];
			$current_stock=$row[csf('stock')];
			$current_stock_sum += $current_stock;
			if($db_type==2)
			{
				$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
			}
			if($db_type==0)
			{
				$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
			}
			$last_req_info=explode('_',$last_req_info);
			//print_r($dataaa);

			$item_account=explode('-',$row[csf('item_account')]);
			$item_code=$item_account[3];
			/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
			$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
			$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
			$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];



			?>
			<tr style="margin: 0px;padding: 0px;" class="main_tbl" bgcolor="<? echo $bgcolor; ?>" style="font-size:20px">
				<td  width="20" align="center"><? echo $i; ?></td>
                <td  width="80"><p style="font-size: 13px"><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
				<td  width="180" ><p style="font-size: 13px"> <? echo $row[csf("item_description")].', '.$row[csf("item_size")];?> </p></td>
				<td width="40"><p style="font-size: 13px">  <? echo $row[csf("required_for")]; ?></p></td>
				<td width="35" align="center"><p style="font-size: 13px">  <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
				<td width="40" align="right"><p style="font-size: 13px"><? echo $row[csf('quantity')]; ?>&nbsp;</p></td>
				<td  width="40" align="right"><? echo $row[csf('rate')]; ?></td>
				<td  width="40" align="right"><? echo $row[csf('amount')]; ?></td>
				<td  width="40" align="right"><p style="font-size: 13px"><? echo number_format($row[csf('stock')],2); ?></p></td>
				<td  width="50" align="center"><p style="font-size: 13px"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
				<td  width="40" align="right"><p style="font-size: 13px"><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
				<td  width="40" align="right"><p style="font-size: 13px"><? echo $last_rec_rate; ?>&nbsp;</p></td>
				<td  width="55" align="right"><p style="font-size: 13px"><? echo number_format($row[csf('quantity')]*$last_rec_rate,0,'',',') ?></p></td>
				<td  width="60" align="right">
				<?
				$min_issue_date=$prev_issue_data[$row[csf("product_id")]]["transaction_date"];
				if($min_issue_date=="")
				{
					echo number_format(0,2);
				}
				else
				{
					$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
					//$interval, $datefrom, $dateto, $using_timestamps = false
					$year_issue_total=$prev_issue_data[$row[csf("product_id")]]["isssue_qty"];
					$issue_avg=$year_issue_total/$month_issue_diff;
					echo number_format($issue_avg,2).'tipu';
				}
				?>
				</td>
				<td  width="60" align="right">
				<?
				$min_receive_date=$prev_receive_data[$row[csf("product_id")]]["transaction_date"];
				if($min_receive_date=="")
				{
					echo number_format(0,2);
				}
				else
				{
					$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
					$year_receive_total=$prev_receive_data[$row[csf("product_id")]]["receive_qty"];
					$receive_avg=$year_receive_total/$month_receive_diff;
					echo number_format($receive_avg,2).'sultan';
				}
				?>
				</td>
				<td  width="90" align="center"><p style="font-size: 13px"><? echo $supplier_array[$last_rec_supp];?>&nbsp;</p></td>
				<td  width="70" align="left"><p style="font-size: 13px"><? echo $remarks; ?>&nbsp;</p></td>
			</tr>
			<?

			$total_amount+=$row[csf('amount')];
			$total_stock+=$row[csf('stock')];
			$total_last_rec_qty +=$last_rec_qty;
			$total_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
			$total_issue_avg +=$issue_avg;
			$total_receive_avg +=$receive_avg;

			$Grand_tot_total_amount+=$row[csf('amount')];
			$Grand_tot_total_stock+=$row[csf('stock')];
			$Grand_tot_last_qnty +=$last_rec_qty;
			$Grand_tot_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
			$Grand_tot_issue_avg +=$issue_avg;
			$Grand_tot_receive_avg +=$receive_avg;

			$i++;
		}
		?>
		</tbody>
		<tr bgcolor="#dddddd">
			<td align="right" colspan="7"><strong>Sub Total : </strong></td>
			<td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
			<td align="right"><? echo number_format($total_stock,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($total_last_rec_qty,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
			<td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
			<td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
		</tr>

		<tr bgcolor="#dddddd">
			<td align="right" colspan="7"><strong>Grand Sub Total : </strong></td>
			<td align="right"><? echo number_format($Grand_tot_total_amount,0,'',','); ?></td>
			<td align="right"><? echo number_format($Grand_tot_total_stock,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($Grand_tot_reqsit_value,0,'',','); ?></td>
			<td align="right"><? echo number_format($Grand_tot_issue_avg,0,'',','); ?></td>
			<td align="right"><? echo number_format($Grand_tot_receive_avg,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
		</tr>
		<tr bgcolor="#dddddd">
			<td align="right" colspan="7"><strong>Grand Total Amount in Word: </strong></td>
			<td align="left" colspan="10">&nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></td>
		</tr>

	</table>
	<br>
	<?
	echo signature_table(25, $data[0], "1100px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	exit();
}

if($action=="purchase_requisition_print_5") // Print Report 5
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	?>
    <style>
    @media print{
		html>body table.rpt_table {
		border:solid 1px;
		margin-left:12px;
  		}

	}
        .rpt_table tbody tr td {
                font-size: 11pt !important;
            }
  	</style>
    <?
	extract($_REQUEST);
	$data=explode('*',$data);
	// echo "<pre>";
	// print_r($data);die;
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];

	?>
	<div id="table_row" style="width:1120px;">
	<?
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,inserted_by,is_approved from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];


	if ($data[4]==4)
	{
		$widths=130;
		$th_span=2;
	}
	elseif ($data[4]==2 && $pay_cash==4)
	{
		$widths=130;
		$th_span=2;
	}
	else
	{
		$widths=0;
		$th_span=0;
	}

	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<table width="1100" align="right" style="font-family: Arial Narrow;">
		<tr>
			<?
			$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
			?>
			<td align="left" width="210">
			<?
			foreach($data_array as $img_row)
			{
				if ($formate_id==129)
				{
					?>
					<img src='../../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
				else
				{
					?>
					<img src='../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
			}
			$req=explode('-',$dataArray[0][csf('requ_no')]);
			?>
			</td>
			<td align="center" style="font-weight:bold;"><span style="font-size:28px;"><? echo $com_dtls[0]; ?></span><br><span style="font-size:18px;"><? echo $com_dtls[1]; ?></span></td>
            <td width="210">&nbsp;</td>
		</tr>
		<tr class="form_caption">
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr class="form_caption">
            <td>&nbsp;</td>			
            <td align="center" style="font-size:22px;"><strong><u><? echo $data[2] ?></u></strong></td>
            <td align="right" style="font-size:22px;"><strong>Location:&nbsp;<? echo $location_arr[$location]; ?></strong></td>
		</tr>
		<tr class="form_caption">
			<td colspan="3">&nbsp;</td>
		</tr>
    </table>
    
    <table width="1100" align="right" style="font-family: Arial Narrow;">
		<tr>
			<td width="120" style="font-size:16px"><strong>Req. No</strong></td>
			<td width="175px" style="font-size:16px"><strong>:&nbsp;&nbsp;<? echo $dataArray[0][csf('requ_no')];
			//$req[2].'-'.$req[3]; ?></strong></td>
			<td width="10">&nbsp;</td>
			<td style="font-size:16px;" width="105"><strong>Req. Date</strong></td>
            <td style="font-size:16px;" width="175">:&nbsp;&nbsp;<? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td width="90">&nbsp;</td>
			<td width="95" style="font-size:16px"><strong>Del. Date</strong></td>
            <td width="175px" style="font-size:16px">:&nbsp;&nbsp;<? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Manual Req No</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $dataArray[0][csf('manual_req')]; ?></td>
			<td>&nbsp; </td>
			<td style="font-size:16px"><strong>Department</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
			<td>&nbsp; </td>
			<td style="font-size:16px"><strong>Section</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
		</tr>
		<tr>
 			<td style="font-size:16px"><strong>Pay Mode</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
			<td>&nbsp; </td>
			<td style="font-size:16px"><strong>Currency</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
            <td>&nbsp; </td>
			<td style="font-size:16px"><strong>Source</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $source[$dataArray[0][csf('source')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Store Name</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
			<td>&nbsp; </td>
			<td style="font-size:16px"><strong>Remarks</strong></td>
            <td style="font-size:16px" colspan="4">:&nbsp;&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
		</tr>
		<tr>
			<td  colspan="5" style="font-size:16px; text-align: center; color:red;">
				<strong>
				<?
					if($dataArray[0][csf('is_approved')] == 1)
					{
						echo "Approved";
					}
					else if($dataArray[0][csf('is_approved')] == 3)
					{
						echo "Partial Approved";
					}
				?>
				</strong>
			</td>
		</tr>
	</table>
	<br>

	<table cellspacing="0" width="1100px"  border="0" rules="all" class="rpt_table" style="font-family: Arial Narrow;margin-left: 20px;" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th colspan="12" width="1100px" align="center" ><strong>ITEM DETAILS</strong></th>

			</tr>
			<tr>
				<th width="30">SL</th>
				<th width="80">ITEM GROUP</th>
				<th width="150">ITEM DES. & SIZE</th>
				<th width="50">UOM</th>
				<th width="60">REQ. QTY.</th>
				<th width="60">RATE</th>
				<th width="70">AMOUNT</th>
				<th width="70">STOCK</th>
				<th width="70">LAST  RCV. RATE</th>
				<th width="80">LAST RCV. INFO (Date + Qty)</th>
				<th width="80">Vehicle No</th>
				<th width="200">REMARKS</th>

			</tr>
		</thead>
		<tbody>
		<?

		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.order_qnty as rec_qty, b.order_rate as rec_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form in (4,20) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('rec_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}
		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		$i=1; $k=1;
		$sql= " select a.id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.vehicle_no, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label
		from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0 order by b.item_category";
		$sql_result=sql_select($sql);

		$item_category_array=array();
		$category_wise_data = array();

		foreach($sql_result as $row)
		{

			if (!in_array($row[csf("item_category")],$item_category_array) )
			{
				$item_category_array[]=$row[csf('item_category')];
				if($k!=1)
				{
					?>
					<tr bgcolor="#dddddd">

                        <td align="right" colspan="6" style="font-family: Arial Narrow; font-size:13px;"><strong>Sub Total : </strong></td>
                        <td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($total_amount,2); ?></strong></td>
                        <td align="right"><? //echo number_format($total_stock,0,'',','); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? //echo number_format($last_qnty,0,'',','); ?></td>
                        <td align="right"><? //echo number_format($total_rate,0,'',','); ?></td>
                    </tr>
                    <tr bgcolor="#dddddd">
                            <td colspan="12" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
                    </tr>
                    <?
					unset($total_amount);

				}
				else
				{
					?>
					<tr bgcolor="#dddddd">
						<td colspan="12" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
					</tr>
					<?
				}
				$k++;
			}
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$quantity=$row[csf('quantity')];
			$quantity_sum += $quantity;
			$amount=$row[csf('amount')];
			$sub_group_name=$row[csf('sub_group_name')];

			$amount_sum += $amount;
			$remarks=$row[csf('remarks')];
			$vehicle_no=$row[csf('vehicle_no')];
			$current_stock=$row[csf('stock')];
			$current_stock_sum += $current_stock;

			$item_account=explode('-',$row[csf('item_account')]);
			$item_code=$item_account[3];
			$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
			$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
			$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
			$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];


			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px" id="tbody_tr_id">
				<td align="center"><? echo $i; ?></td>
				<td align="center"><p><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
				<td align="left">
					<p>
					<?
						//echo $row[csf("item_description")].', '.$row[csf("item_size")].', '.$row[csf("brand_name")].', '.$row[csf("model")];
						$item_descriptions = $row[csf("item_description")];
						if($row[csf("item_size")] != "")
						{
							$item_descriptions .= ", ". $row[csf("item_size")];
						}
						if($row[csf("brand_name")] != "")
						{
							$item_descriptions .= ", ". $row[csf("brand_name")];
						}
						if($row[csf("model")] != "")
						{
							$item_descriptions .= ", ". $row[csf("model")];
						}
						echo $item_descriptions;
					?>
					</p>
				</td>
				<td align="center"><p>  <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                <td align="right"><p><? echo $row[csf('quantity')]; //echo number_format($row[csf('quantity')],3,'',','); ?>&nbsp;&nbsp;</p></td>
				<td align="right"><? echo number_format($row[csf('rate')],2); ?>&nbsp;&nbsp;</td>
				<td align="right"><? echo number_format($row[csf('amount')],2); ?>&nbsp;&nbsp;</td>
                <td align="right"><p><? echo number_format($row[csf('stock')],0,'',','); ?>&nbsp;&nbsp;</p></td>
				<td align="right"><p><? echo number_format($last_rec_rate,2); ?>&nbsp;&nbsp;</p></td>
				<td align="center"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;<br><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
                <td align="right"><p><? echo $vehicle_no; ?>&nbsp;</p></td>
                <td align="left"><p><? echo $remarks; ?>&nbsp;</p></td>
			</tr>
			<?
			$last_qnty +=$last_rec_qty;
			$total_amount+=$row[csf('amount')];
			$total_rate+=$row[csf('rate')];
			$total_stock+=$row[csf('stock')];
			$total_reqsit_value += $reqsit_value;

			$Grand_tot_last_qnty +=$last_rec_qty;
			$Grand_tot_total_amount+=$row[csf('amount')];
			$Grand_tot_total_rate+=$row[csf('rate')];
			$Grand_tot_total_stock+=$row[csf('stock')];
			$Grand_tot_total_reqsit_value += $reqsit_value;

			$i++;
		}
		?>

		<tr bgcolor="#dddddd">

			<td align="right" colspan="6" style="font-family: Arial Narrow;font-size:13px;"><strong>Sub Total : </strong></td>
			<td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($total_amount,2); ?></strong></td>
			<td align="right"><? //echo number_format($total_stock,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? //echo number_format($last_qnty,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
		</tr>

		<tr bgcolor="#dddddd">
			<td  align="right" colspan="6" style="font-family: Arial Narrow;font-size:13px;"><strong>Grand Total : </strong></td>
			<td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($Grand_tot_total_amount,2); ?></strong></td>
			<td align="right"><? //echo number_format($Grand_tot_total_stock,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? //echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="6" align="left"><strong>Total Amount (In Word) : &nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></th>
		</tr>
		 </tbody>
	</table>
	<br/>
        <?
        $booking_no = $dataArray[0][csf('requ_no')];
        $data_array=sql_select("select terms from wo_booking_terms_condition where booking_no='$booking_no'");
        if (count($data_array > 0))
        {
        ?>
            <table  width="1100" class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="margin-left: 20px;">
                <thead>
                    <tr style="border:1px solid black;">
                        <th width="3%" >Sl</th><th width="97%" >Terms & Condition</th>
                    </tr>
                </thead>
                <tbody>
                <?
                    $k=0;
                    foreach( $data_array as $row )
                    {

                        $k++;
                        echo "<tr id='settr_1'> <td align='center'>
                        $k</td><td>".$row[csf('terms')]."</td></tr>";

                    }
                ?>
                </tbody>
            </table>
            <br>
	<?
        }
	$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
    $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=1 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

    foreach ($approved_his_sql as $key => $row)
    {
    	$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
    	if ($row[csf('un_approved_date')]!='')
    	{
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
    	}
    }

    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    if(count($approved_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px; margin-left: 20px;">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <thead>
                	<tr>
                		<th colspan="4">Approval Status</th>
                	</tr>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Designation</th>
	                    <th width="100">Approval Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_sql as $key => $value)
                {
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
                    </tr>
                    <?
                    $sl++;
                }
                ?>
            </table>
        </div>
        <?
    }

	if(count($approved_his_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px; margin-left: 20px;">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <thead>
                	<tr>
                		<th colspan="6">Approval / Un-Approval History </th>
                	</tr>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="150">Approved / Un-Approved</th>
	                    <th width="150">Designation</th>
	                    <th width="50">Approval Status</th>
	                    <th width="150">Reason for Un-Approval</th>
	                    <th width="150">Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_his_sql as $key => $value)
                {
                	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                	?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="20"><? echo $sl; ?></td>
                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="50">Yes</td>
                        <td width="150"><? echo $unapproved_request_arr[$value[csf("mst_id")]]; ?></td>
                        <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    </tr>
                    <?
				    $sl++;
                    $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                    $un_approved_date=$un_approved_date[0];
                    if($db_type==0) //Mysql
                    {
                        if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }
                    else
                    {
                        if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }

                    if($un_approved_date!="")
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
	                        <td width="20"><? echo $sl; ?></td>
	                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
	                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
	                        <td width="50">No</td>
	                        <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
	                        <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);
							echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    	</tr>

						<?
						$sl++;
					}
                }
                ?>
            </table>
        </div>
        <?
    }
	?>
	<div style="margin-top:-80px;">
	<?
	$report_width= $cash+1050;
	echo signature_table(25, $data[0], $report_width."px",$cbo_template_id,70,$user_lib_name[$inserted_by]); ?>
	</div>
	<?
	exit();
}



if($action=="purchase_requisition_print_23")
{
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	?>
    <style>
    @media print{
		html>body table.rpt_table {
		border:solid 1px;
		margin-left:12px;
  		}
                
	}
        .rpt_table tbody tr td {
                font-size: 11pt !important;
            }
  	</style>
    <?
	extract($_REQUEST); 
	$data=explode('*',$data);
	// print($data[4]);
	$update_id=$data[1];
	$cbo_template_id=$data[6];
	?>
	<div id="table_row" style="width:1120px;">
	<?
	$sql="select a.id, a.requ_no, a.item_category_id, a.requisition_date, a.location_id, a.delivery_date, a.source, a.manual_req, a.department_id, a.section_id, a.store_name, a.pay_mode, a.cbo_currency, a.remarks,a.inserted_by,b.user_full_name,b.designation, a.priority_id from inv_purchase_requisition_mst a, user_passwd b where a.id=$update_id and a.inserted_by=b.id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	$user_designation_arr=return_library_array( "select id,custom_designation from lib_designation",'id','custom_designation');
	$user_designation=$user_designation_arr[$dataArray[0][csf("designation")]];
	$full_name=$dataArray[0][csf("user_full_name")];
	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	
	
	if ($data[4]!=3)
	{
		
		if ($data[4]==4)
		{
			$widths=130;
			$th_span=2;	
		}
		elseif ($data[4]==2 && $pay_cash==4)
		{
			$widths=130;
			$th_span=2;
		}
		else
		{
			$widths=0;
			$th_span=0;
		}
		
		?>
		<table width="1100" align="right" style="font-family: Arial Narrow;">
            <tr class="form_caption">
            <?
                $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left" rowspan="2" width="150">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='70' width='150' align="middle" />	
                    <? 
                }
                ?>
                </td>


            	<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px; "><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
				
                <td colspan="5" align="center" style="font-size:18px;">  
                <?
				
				echo chop(show_company($data[0],'',''),","); //kaiyum
                /*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
                foreach ($nameArray as $result)
                { 
					 echo $result[csf('city')].','.$country_arr[$result[csf('country_id')]].'<br>';
					 //echo $result[csf('email')].','.$result[csf('website')]; 
                }
                */
                $req=explode('-',$dataArray[0][csf('requ_no')]);
                ?>  
               
                </td> 
            </tr>
            <tr> 
            <td>&nbsp; </td>
            	<td colspan="5" align="center" style="font-size:22px;"><strong><u><? echo $data[2] ?></u></strong></td>
            </tr>
            </table>
            <table width="1100" align="right" style="font-family: Arial Narrow;">
            <tr>
                <td width="120" style="font-size:16px"><strong>Req. No</strong></td>
                <td width="175px" style="font-size:16px"><strong>:&nbsp;&nbsp;<? echo $dataArray[0][csf('requ_no')];
                //$req[2].'-'.$req[3]; ?></strong></td>
                <td width="10">&nbsp;</td>
                <td style="font-size:16px;" width="105"><strong>Req. Date</strong></td><td style="font-size:16px;" width="175">:&nbsp;&nbsp;<? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
                <td width="90">&nbsp;</td>
                <td width="95" style="font-size:16px"><strong>Source</strong></td><td width="175px" style="font-size:16px">:&nbsp;&nbsp;<? echo $source[$dataArray[0][csf('source')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:16px"><strong>Manual Req No</strong></td> <td width="120px" style="font-size:16px">:&nbsp;&nbsp;<? echo $dataArray[0][csf('manual_req')]; ?></td>
                <td>&nbsp; </td>
                <td style="font-size:16px"><strong>Department</strong></td><td width="175px" style="font-size:16px">:&nbsp;&nbsp;<? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
                <td>&nbsp; </td>
                <td style="font-size:16px"><strong>Section</strong></td><td width="175px" style="font-size:16px">:&nbsp;&nbsp;<? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>
            <tr>
            	<td style="font-size:16px"><strong>Del. Date</strong></td><td style="font-size:16px">:&nbsp;&nbsp;<? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
                <td>&nbsp; </td>
                <td style="font-size:16px"><strong>Store Name</strong></td><td style="font-size:16px">:&nbsp;&nbsp;<? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
                <td>&nbsp; </td>
                <td style="font-size:16px"><strong>Pay Mode</strong></td><td style="font-size:16px">:&nbsp;&nbsp;<? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:16px"><strong>Currency</strong></td> <td style="font-size:16px">:&nbsp;&nbsp;<? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
                <td>&nbsp; </td>
                <td style="font-size:16px"><strong>Remarks</strong></td> <td style="font-size:16px" colspan="2">:&nbsp;&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
                <td style="font-size:16px"><strong>Priority</strong></td> <td style="font-size:16px">:&nbsp;&nbsp;<? echo $priority_array[$dataArray[0][csf('priority_id')]]; ?></td>
            </tr>
		</table>
		<br>
        <?
		//$margin='-133px;';
		
		//echo $th_span.'='.$cash_span.'='.$span.'='.$margin.'='.$widths.'='.$cash;
		?>
        
		<table cellspacing="0" width="1100px"  border="0" rules="all" class="rpt_table" style="font-family: Arial Narrow;margin-left: 20px;" >
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <th colspan="11" width="1100px" align="center" ><strong>ITEM DETAILS</strong></th>
                     
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="80">ITEM GROUP</th>
                    <th width="150">ITEM DES. & SIZE</th>
                    <th width="50">UOM</th>
                    <th width="60">REQ. QTY.</th>
                    <? 
                    $data[4]=4;
					if ($data[4]==4)
                    {
						?>
						<th width="60">RATE</th>
						<th width="70">AMOUNT</th>  
						<?
                    }
                    if ($data[4]==2 && $pay_cash==4)
                    {
						?>
						<th width="50">RATE</th>
						<th width="70">AMOUNT</th>
						<?
                    }
                    ?>
                    <th width="70">STOCK</th>
                    <th width="70">LAST RATE</th>
                    <th width="80">LAST RCV. INFO (Date + Qty)</th>
					<!-- <th width="70">AVG MONTHLY RCV. And ISSUE</th>-->
                    <th width="200">REMARKS</th>                  
                    
                </tr>
            </thead>
            <tbody>
			<?
            $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
            $receive_array=array();
            /*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];			
				//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
            }*/
			 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];			
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
				$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
            }

            if($db_type==2)
			{ 
				$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'"; 
			}
			elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

            $issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_issue_data=array();
			foreach($issue_sql as $row)
			{
				$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];

				$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
			}
			
			//var_dump($prev_issue_data);die;
			
			$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_receive_data=array();
			foreach($receive_sql as $row)
			{
				$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
			}
            
            $i=1; $k=1;
            // echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
             $sql= " select a.id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0 order by b.item_category";
            $sql_result=sql_select($sql);  
            //echo $sql;//die;
            $item_category_array=array();
            $category_wise_data = array(); 
            /*foreach ($sql_result as $row) {
            	$category_wise_data[$row[csf("item_category")]]['item_group_id']=$row[csf("item_group_id")];
            	$category_wise_data[$row[csf("item_category")]]['item_size']=$row[csf("item_size")];
            	$category_wise_data[$row[csf("item_category")]]['item_description']=$row[csf("item_description")];
            	$category_wise_data[$row[csf("item_category")]]['required_for']=$row[csf("required_for")];
            	$category_wise_data[$row[csf("item_category")]]['cons_uom']=$row[csf("cons_uom")];
            	$category_wise_data[$row[csf("item_category")]]['quantity']=$row[csf("quantity")];
            	$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
            	$category_wise_data[$row[csf("item_category")]]['amount']=$row[csf("amount")];
            	$category_wise_data[$row[csf("item_category")]]['stock']=$row[csf("stock")];
            	$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
            	$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
            }*/
            foreach($sql_result as $row)
            {

        if (!in_array($row[csf("item_category")],$item_category_array) )
		{
			if($k!=1)
			{ 
			?>
				<tr bgcolor="#dddddd">
                
                <td align="right" colspan="6" style="font-family: Arial Narrow; font-size:13px;"><strong>Sub Total : </strong></td>
                <?
				if ($data[4]==4)
                { 
					?>
					
					<td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($total_amount,2); ?></strong></td>
					<?
				}
                if ($data[4]==2 && $pay_cash==4)
                {
					?>
                   
                    <td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($total_amount,2); ?></strong></td>
                    <?
                } 
				?>
				<td align="right"><? //echo number_format($total_stock,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
                <td align="right"><? //echo number_format($last_qnty,0,'',','); ?></td>
                <td align="right"><? //echo number_format($total_rate,0,'',','); ?></td>
					<!-- <td align="right">&nbsp;</td>-->
                
                
			</tr>
			<tr bgcolor="#dddddd">
					<td colspan="11" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
			</tr>
			<?
				unset($total_amount);
				unset($last_qnty);
				unset($total_stock);
				unset($total_rate);
				
			}
			else
			{
				?>
				<tr bgcolor="#dddddd">
					<td colspan="11" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
				</tr>
				<?
			}					
			$item_category_array[]=$row[csf('item_category')];            
			$k++;
		}



				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row[csf('quantity')];
				$quantity_sum += $quantity;
				$amount=$row[csf('amount')];
				//test 
				$sub_group_name=$row[csf('sub_group_name')];
				$amount_sum += $amount;
				$remarks=$row[csf('remarks')];
				$current_stock=$row[csf('stock')];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);
				
				if ($data[4]==1)
				{
					$item_code=$row[csf('item_account')];
				}
				else
				{
					$item_account=explode('-',$row[csf('item_account')]);
					$item_code=$item_account[3];
				}
				/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
				$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
				$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];
				
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px" id="tbody_tr_id">
                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                    <td align="left">
                        <p> 
                        <? 
                            //echo $row[csf("item_description")].', '.$row[csf("item_size")].', '.$row[csf("brand_name")].', '.$row[csf("model")];
                            $item_descriptions = $row[csf("item_description")];
                            if($row[csf("item_size")] != "")
                            {
                                $item_descriptions .= ", ". $row[csf("item_size")];
                            }
                            if($row[csf("brand_name")] != "")
                            {
                                $item_descriptions .= ", ". $row[csf("brand_name")];
                            }
                            if($row[csf("model")] != "")
                            {
                                $item_descriptions .= ", ". $row[csf("model")];
                            }
                            echo $item_descriptions;
                        ?> 
                        </p>
                    </td>
                    <td align="center"><p>  <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('quantity')],3); //echo number_format($row[csf('quantity')],3,'',','); ?>&nbsp;&nbsp;</p></td>
                      <?
					if ($data[4]==4)
                    {
						?>
                         <!-- <td align="right"><? echo number_format($row[csf('rate')],2); ?>&nbsp;&nbsp;</td> -->
                         <td align="right"><? echo number_format($row[csf('rate')],2); ?>&nbsp;&nbsp;</td>
                         <td align="right"><? echo number_format($row[csf('amount')],2); ?>&nbsp;&nbsp;</td>   
                        <?
					}
					
                    if ($data[4]==2 && $pay_cash==4)
                    {
						?>
						<td align="right"><? echo number_format($row[csf('rate')],2); ?>&nbsp;&nbsp;</td>
						<td align="right"><? echo number_format($row[csf('amount')],2); ?>&nbsp;&nbsp;</td>
						<? 
                    } 
					?>
                    <td align="right"><p><? echo number_format($row[csf('stock')],0,'',','); ?>&nbsp;&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($last_rec_rate,2); ?>&nbsp;&nbsp;</p></td>
                    <td align="center"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;<br><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
                   
                    
				<!-- <td align="center"><p><? echo number_format($row[csf('stock')],0,'',','); ?><br>
                      <?
						if ($data[4]==4)
	                    {
	                         echo number_format($row[csf('amount')],0,'',',');  
	                        
						}
						
	                    if ($data[4]==2 && $pay_cash==4)
	                    {
							
							 echo number_format($row[csf('amount')],0,'',',');
							 
	                    } 
					 ?>
                    </p></td>-->
                    
                     <td align="left"><p><? echo $remarks; ?>&nbsp;</p></td>
                    
                    
				</tr>
				<?
				$last_qnty +=$last_rec_qty;
				$total_amount+=$row[csf('amount')];
				$total_rate+=$row[csf('rate')];
				$total_stock+=$row[csf('stock')];
                $total_reqsit_value += $reqsit_value;

                $Grand_tot_last_qnty +=$last_rec_qty;
				$Grand_tot_total_amount+=$row[csf('amount')];
				$Grand_tot_total_rate+=$row[csf('rate')];
				$Grand_tot_total_stock+=$row[csf('stock')];
                $Grand_tot_total_reqsit_value += $reqsit_value;

				$i++;
			}
			?>
           
            <tr bgcolor="#dddddd">
                
                <td align="right" colspan="6" style="font-family: Arial Narrow;font-size:13px;"><strong>Sub Total : </strong></td>
                <?
				if ($data[4]==4)
                { 
					?>
					
					<td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($total_amount,2); ?></strong></td>
					<?
				}
                if ($data[4]==2 && $pay_cash==4)
                {
					?>
                   
                    <td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($total_amount,2); ?></strong></td>
                    <?
                } 
				?>
				<td align="right"><? //echo number_format($total_stock,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
                <td align="right"><? //echo number_format($last_qnty,0,'',','); ?></td>
				<!-- <td align="right"><? //echo number_format($total_rate,0,'',','); ?></td>-->
                <td align="right">&nbsp;</td>     
			</tr>

			<tr bgcolor="#dddddd">
                
                <td  align="right" colspan="6" style="font-family: Arial Narrow;font-size:13px;"><strong>Grand Total : </strong></td>
                <?
				if ($data[4]==4)
                { 
					?>
					
					<td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($Grand_tot_total_amount,2); ?></strong></td>
					<?
				}
                if ($data[4]==2 && $pay_cash==4)
                {
					?>
                   
                    <td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($Grand_tot_total_amount,2); ?></strong></td>
                    <?
                } 
				?>
				<td align="right"><? //echo number_format($Grand_tot_total_stock,0,'',','); ?></td>
				<td align="right">&nbsp;</td>
                <td align="right"><? //echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
				<!-- <td align="right"><? //echo number_format($Grand_tot_total_rate,0,'',','); ?></td>-->
                <td align="right">&nbsp;</td>
			</tr>
			 </tbody>
		</table>

		<div style="margin-top:15px; padding-left:18px" >
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:750px;text-align:center;" rules="all">
                <label><b>PR Raised By </b></label>
                <thead>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Position</th>
	                </tr>
            	</thead>
                    <tr>
                        <td width="20"><? echo "1"; ?></td>
                        <td width="250"><? echo $full_name; ?></td>
                        <td width="200"><? echo $user_designation; ?></td>
                    </tr>
            </table>
        </div>
		<div style="margin-top:-80px;">
		<? 
		$report_width= $cash+1050;
		
		echo signature_table(25, $data[0], $report_width."px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
		//echo signature_table(25, $data[0], $report_width."px"); ?>
        </div>
		<?
	}
	else
	{}
	exit();

}

if($action=="purchase_requisition_print_5_(23092020)") // Print Report 5
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	?>
    <style>
    @media print{
		html>body table.rpt_table {
		border:solid 1px;
		margin-left:12px;
  		}

	}
        .rpt_table tbody tr td {
                font-size: 11pt !important;
            }
  	</style>
    <?
	extract($_REQUEST);
	$data=explode('*',$data);
	// echo "<pre>";
	// print_r($data);die;
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];

	?>
	<div id="table_row" style="width:1120px;">
	<?
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,inserted_by,is_approved from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];


	if ($data[4]==4)
	{
		$widths=130;
		$th_span=2;
	}
	elseif ($data[4]==2 && $pay_cash==4)
	{
		$widths=130;
		$th_span=2;
	}
	else
	{
		$widths=0;
		$th_span=0;
	}

	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<table width="1100" align="right" style="font-family: Arial Narrow;">
		<tr>
			<?
			$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
			?>
			<td align="left" width="210">
			<?
			foreach($data_array as $img_row)
			{
				if ($formate_id==129)
				{
					?>
					<img src='../../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
				else
				{
					?>
					<img src='../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
					<?
				}
			}
			$req=explode('-',$dataArray[0][csf('requ_no')]);
			?>
			</td>
			<td align="center" style="font-weight:bold;"><span style="font-size:28px;"><? echo $com_dtls[0]; ?></span><br><span style="font-size:18px;"><? echo $com_dtls[1]; ?></span></td>
            <td width="270">&nbsp;</td>
		</tr>
		<tr class="form_caption">
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr class="form_caption">
            <td>&nbsp;</td>			
            <td align="center" style="font-size:22px;"><strong><u><? echo $data[2] ?></u></strong></td>
            <td>&nbsp;</td>
		</tr>
		<tr class="form_caption">
			<td colspan="3">&nbsp;</td>
		</tr>
    </table>
    
    <table width="1100" align="right" style="font-family: Arial Narrow;">
		<tr>
			<td width="120" style="font-size:16px"><strong>Req. No</strong></td>
			<td width="175px" style="font-size:16px"><strong>:&nbsp;&nbsp;<? echo $dataArray[0][csf('requ_no')];
			//$req[2].'-'.$req[3]; ?></strong></td>
			<td width="10">&nbsp;</td>
			<td style="font-size:16px;" width="105"><strong>Req. Date</strong></td>
            <td style="font-size:16px;" width="175">:&nbsp;&nbsp;<? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td width="90">&nbsp;</td>
			<td width="95" style="font-size:16px"><strong>Location</strong></td>
            <td width="175px" style="font-size:20px">:&nbsp;&nbsp;<? echo $location_arr[$location]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Manual Req No</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $dataArray[0][csf('manual_req')]; ?></td>
			<td>&nbsp; </td>
			<td style="font-size:16px"><strong>Department</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
			<td>&nbsp; </td>
			<td style="font-size:16px"><strong>Source</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $source[$dataArray[0][csf('source')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Del. Date</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
			<td>&nbsp; </td>
			<td style="font-size:16px"><strong>Store Name</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
			<td>&nbsp; </td>
			<td style="font-size:16px"><strong>Section</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
		</tr>
		<tr>
 			<td style="font-size:16px"><strong>Pay Mode</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
			<td>&nbsp; </td>
			<td style="font-size:16px"><strong>Currency</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
			<td>&nbsp; </td>
			<td style="font-size:16px"><strong>Remarks</strong></td>
            <td style="font-size:16px">:&nbsp;&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
		</tr>
		<tr>
			<td  colspan="5" style="font-size:16px; text-align: center; color:red;">
				<strong>
				<?
					if($dataArray[0][csf('is_approved')] == 1)
					{
						echo "Approved";
					}
					else if($dataArray[0][csf('is_approved')] == 3)
					{
						echo "Partial Approved";
					}
				?>
				</strong>
			</td>
		</tr>
	</table>
	<br>

	<table cellspacing="0" width="1100px"  border="0" rules="all" class="rpt_table" style="font-family: Arial Narrow;margin-left: 20px;" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th colspan="11" width="1100px" align="center" ><strong>ITEM DETAILS</strong></th>

			</tr>
			<tr>
				<th width="30">SL</th>
				<th width="80">ITEM GROUP</th>
				<th width="150">ITEM DES. & SIZE</th>
				<th width="50">UOM</th>
				<th width="60">REQ. QTY.</th>
				<th width="60">RATE</th>
				<th width="70">AMOUNT</th>
				<th width="70">STOCK</th>
				<th width="70">LAST  RCV. RATE</th>
				<th width="80">LAST RCV. INFO (Date + Qty)</th>
				<th width="200">REMARKS</th>

			</tr>
		</thead>
		<tbody>
		<?

		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form in (4,20) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}
		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}

		$i=1; $k=1;
		$sql= " select a.id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label
		from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0 order by b.item_category";
		$sql_result=sql_select($sql);

		$item_category_array=array();
		$category_wise_data = array();

		foreach($sql_result as $row)
		{

			if (!in_array($row[csf("item_category")],$item_category_array) )
			{
				$item_category_array[]=$row[csf('item_category')];
				if($k!=1)
				{
					?>
					<tr bgcolor="#dddddd">

                        <td align="right" colspan="6" style="font-family: Arial Narrow; font-size:13px;"><strong>Sub Total : </strong></td>
                        <td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($total_amount,2); ?></strong></td>
                        <td align="right"><? //echo number_format($total_stock,0,'',','); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? //echo number_format($last_qnty,0,'',','); ?></td>
                        <td align="right"><? //echo number_format($total_rate,0,'',','); ?></td>
                    </tr>
                    <tr bgcolor="#dddddd">
                            <td colspan="11" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
                    </tr>
                    <?
					unset($total_amount);

				}
				else
				{
					?>
					<tr bgcolor="#dddddd">
						<td colspan="11" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
					</tr>
					<?
				}
				$k++;
			}
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$quantity=$row[csf('quantity')];
			$quantity_sum += $quantity;
			$amount=$row[csf('amount')];
			$sub_group_name=$row[csf('sub_group_name')];

			$amount_sum += $amount;
			$remarks=$row[csf('remarks')];
			$current_stock=$row[csf('stock')];
			$current_stock_sum += $current_stock;

			$item_account=explode('-',$row[csf('item_account')]);
			$item_code=$item_account[3];
			$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
			$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
			$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
			$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];


			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px" id="tbody_tr_id">
				<td align="center"><? echo $i; ?></td>
				<td align="center"><p><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
				<td align="left">
					<p>
					<?
						//echo $row[csf("item_description")].', '.$row[csf("item_size")].', '.$row[csf("brand_name")].', '.$row[csf("model")];
						$item_descriptions = $row[csf("item_description")];
						if($row[csf("item_size")] != "")
						{
							$item_descriptions .= ", ". $row[csf("item_size")];
						}
						if($row[csf("brand_name")] != "")
						{
							$item_descriptions .= ", ". $row[csf("brand_name")];
						}
						if($row[csf("model")] != "")
						{
							$item_descriptions .= ", ". $row[csf("model")];
						}
						echo $item_descriptions;
					?>
					</p>
				</td>
				<td align="center"><p>  <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
				<td align="right"><p><? echo $row[csf('quantity')]; //echo number_format($row[csf('quantity')],3,'',','); ?>&nbsp;&nbsp;</p></td>
				<td align="right"><? echo number_format($row[csf('rate')],2); ?>&nbsp;&nbsp;</td>
				<td align="right"><? echo number_format($row[csf('amount')],2); ?>&nbsp;&nbsp;</td>
                <td align="right"><p><? echo number_format($row[csf('stock')],0,'',','); ?>&nbsp;&nbsp;</p></td>
				<td align="right"><p><? echo number_format($last_rec_rate,2); ?>&nbsp;&nbsp;</p></td>
				<td align="center"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;<br><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
                <td align="left"><p><? echo $remarks; ?>&nbsp;</p></td>
			</tr>
			<?
			$last_qnty +=$last_rec_qty;
			$total_amount+=$row[csf('amount')];
			$total_rate+=$row[csf('rate')];
			$total_stock+=$row[csf('stock')];
			$total_reqsit_value += $reqsit_value;

			$Grand_tot_last_qnty +=$last_rec_qty;
			$Grand_tot_total_amount+=$row[csf('amount')];
			$Grand_tot_total_rate+=$row[csf('rate')];
			$Grand_tot_total_stock+=$row[csf('stock')];
			$Grand_tot_total_reqsit_value += $reqsit_value;

			$i++;
		}
		?>

		<tr bgcolor="#dddddd">

			<td align="right" colspan="6" style="font-family: Arial Narrow;font-size:13px;"><strong>Sub Total : </strong></td>
			<td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($total_amount,2); ?></strong></td>
			<td align="right"><? //echo number_format($total_stock,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? //echo number_format($last_qnty,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
		</tr>

		<tr bgcolor="#dddddd">

			<td  align="right" colspan="6" style="font-family: Arial Narrow;font-size:13px;"><strong>Grand Total : </strong></td>
			<td align="right" style="font-family: Arial Narrow;font-size:13px;"><strong><? echo number_format($Grand_tot_total_amount,2); ?></strong></td>
			<td align="right"><? //echo number_format($Grand_tot_total_stock,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? //echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="6" align="left"><strong>Total Amount (In Word) : &nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></th>
		</tr>
		 </tbody>
	</table>
	<!-- <span style="padding-left: 411px; "></span> -->
	<br>
	<?
	$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
    $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=1 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

    foreach ($approved_his_sql as $key => $row)
    {
    	$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
    	if ($row[csf('un_approved_date')]!='')
    	{
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
    	}
    }

    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    if(count($approved_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px; margin-left: 20px;">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <thead>
                	<tr>
                		<th colspan="4">Approval Status</th>
                	</tr>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Designation</th>
	                    <th width="100">Approval Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_sql as $key => $value)
                {
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
                    </tr>
                    <?
                    $sl++;
                }
                ?>
            </table>
        </div>
        <?
    }

	if(count($approved_his_sql) > 0)
    {
        $sl=1;
        ?>
        <div style="margin-top:15px; margin-left: 20px;">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <thead>
                	<tr>
                		<th colspan="6">Approval / Un-Approval History </th>
                	</tr>
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="150">Approved / Un-Approved</th>
	                    <th width="150">Designation</th>
	                    <th width="50">Approval Status</th>
	                    <th width="150">Reason for Un-Approval</th>
	                    <th width="150">Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_his_sql as $key => $value)
                {
                	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                	?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="20"><? echo $sl; ?></td>
                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="50">Yes</td>
                        <td width="150"><? echo $unapproved_request_arr[$value[csf("mst_id")]]; ?></td>
                        <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    </tr>
                    <?
				    $sl++;
                    $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                    $un_approved_date=$un_approved_date[0];
                    if($db_type==0) //Mysql
                    {
                        if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }
                    else
                    {
                        if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }

                    if($un_approved_date!="")
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
	                        <td width="20"><? echo $sl; ?></td>
	                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
	                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
	                        <td width="50">No</td>
	                        <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
	                        <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);
							echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    	</tr>

						<?
						$sl++;
					}
                }
                ?>
            </table>
        </div>
        <?
    }
	?>
	<div style="margin-top:-80px;">
	<?
	$report_width= $cash+1050;
	echo signature_table(25, $data[0], $report_width."px",$cbo_template_id,70,$user_lib_name[$inserted_by]); ?>
	</div>
	<?
	exit();
}

if($action=="purchase_requisition_print_11") // Print Report 11
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];
	?>
	<div id="table_row" style="width:1000px;">
	<?
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,inserted_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');

	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];


	if ($data[4]!=3)
	{
		$com_dtls = fnc_company_location_address($company, $location, 2);
		?>
		<table width="1000" align="right">
            <tr class="form_caption">
            	<?
                $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left" rowspan="2">
                <?
                foreach($data_array as $img_row)
                {
					if ($formate_id==121)
					{
						?>
						<img src='../../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
						<?
					}
					else
					{
						?>
						<img src='../<? echo $com_dtls[2]; ?>' height='70' width='200' align="middle" />
						<?
					}
                }
                ?>
                </td>

            	<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $com_dtls[0]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="5" align="center" style="font-size:18px;">
                <?

				echo $com_dtls[1]; //Aziz
                /*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                foreach ($nameArray as $result)
                {
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')];?>
					City No: <? echo $result[csf('city')];?>
					Zip Code: <? echo $result[csf('zip_code')]; ?>
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
					Email Address: <? echo $result[csf('email')];?>
					Website No: <? echo $result[csf('website')];
                }*/
                $req=explode('-',$dataArray[0][csf('requ_no')]);
                ?>

                </td>
            </tr>
            <tr>
            	<td>&nbsp; </td>
            	<td colspan="5" align="center" style="font-size:22px"><strong><u><? echo $data[2]; ?></u></strong></td>
            </tr>
            <tr>
                <td width="120" style="font-size:16px"><strong>Req. No:</strong></td>
                <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('requ_no')];
                //$req[2].'-'.$req[3]; ?></td>
                <td style="font-size:16px" width="130"><strong>Req. Date:</strong></td><td style="font-size:16px" width="175px"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
                <td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:16px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
                <td style="font-size:16px"><strong>Department:</strong></td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
                <td style="font-size:16px"><strong>Section:</strong></td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>
            <tr>
            	<td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
                <td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
                <td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                <td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
                <td style="font-size:16px"><strong>Remarks:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
		</table>
		<br>


		<table cellspacing="0" width="1280"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <th colspan="12" align="center" ><strong>Item Details</strong></th>
                    <th width="100" align="center" style="font-size:14px" rowspan="2"><strong>Required For</strong></th>
                    <th rowspan="2" style="font-size:14px">Remarks</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="200">Item Des.</th>
                    <th width="50">UOM</th>
                    <th width="60">Req. Qty.</th>
                    <th width="70">Stock</th>
                    <th width="70">Re-Order quantity</th>
                    <th width="80">Last Rec. Date</th>
                    <th width="60">Last Rec. Qty.</th>
                    <th width="90">Last 3 Month Avg.Iss.Qty</th>
                    <th width="70">Last Rate</th>
					<th width="90">Last Req. Info (Date+Qty)</th>
                    <th width="80">Total Amount</th>
                </tr>
            </thead>
            <tbody>
			<?
				$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
				$receive_array=array();

				$sql_result= sql_select(" select a.id, a.requisition_date,b.id as dtls_id, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  ");

				$all_data_array = array();
            foreach($sql_result as $row)
            {
				$all_prod_ids.=$row[csf('product_id')].",";
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['id'] = $row[csf('id')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
            	$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
            }

				$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));

				if($all_prod_ids=="") $all_prod_ids=0;

            /*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
				//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
            }*/
			 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and a.entry_form in (4,20) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
            }

            $lastThreeMonthDataArr = array();
            //$lastThreeMonthData= sql_select("select b.quantity, b.product_id  from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0 and a.requisition_date >= add_months(sysdate,-3) ");

			if($db_type==0)
			{
				$lastThreeMonthData= sql_select("select sum(a.cons_quantity) as qnty , b.id as product_id from  inv_transaction a,  product_details_master b where a.prod_id=b.id and a.status_active=1 and b.status_active=1 and a.prod_id in($all_prod_ids) and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= DATE_ADD(CURDATE(), INTERVAL -3 MONTH) and a.transaction_type in (2,3,6) group by b.id order by b.id ");
			}
			else
			{
				$lastThreeMonthData= sql_select("select sum(a.cons_quantity) as qnty , b.id as product_id from  inv_transaction a,  product_details_master b where a.prod_id=b.id and a.prod_id in($all_prod_ids) and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.transaction_date >= add_months(sysdate,-3) and a.transaction_type in (2,3,6) group by b.id order by b.id ");
			}
            foreach ($lastThreeMonthData as $ldata) {
            	$lastThreeMonthDataArr[$ldata[csf("product_id")]] = $ldata[csf("qnty")];
            }

            $i=1;
            // echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";

   		//echo "<pre>";
			// print_r($all_data_array);
			foreach ($all_data_array as $cons_uom_id => $cons_uom_data) {
				$total_amount=0;$last_qnty=0;$total_requisition=0;$total_stock=0;
	        	foreach ($cons_uom_data as $dtls_id => $row) {
	        		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$quantity=$row['quantity'];
					$quantity_sum += $quantity;
					$amount=$row['amount'];
					$amount_sum += $amount;

					$current_stock=$row['stock'];
					$current_stock_sum += $current_stock;
					if($db_type==2)
					{
						$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
					}
					if($db_type==0)
					{
						$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
					}
					$last_req_info=explode('_',$last_req_info);
					//print_r($dataaa);

					if ($data[4]==1)
					{
						$item_code=$row['item_account'];
					}
					else
					{
						$item_account=explode('-',$row['item_account']);
						$item_code=$item_account[3];
					}
					/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
					$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
					$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
					$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
					$last_rec_rate=$receive_array[$row['product_id']]['rate'];

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:20px">
	                    <td><? echo $i; ?></td>
	                    <td>
							<?
							echo $row['item_description'];
							if($row['item_description']!="" && $row['item_size']!="") echo ', ';
							echo $row['item_size'];
							?>
						</td>
	                    <td><? echo $unit_of_measurement[$row['cons_uom']]; ?></td>
	                    <td align="right"><? echo $row['quantity']; ?></td>
	                    <td align="right"><? echo number_format($row['stock'],2); ?></td>
	                    <td align="right"><? echo number_format($row['re_order_label'],2); ?></td>
	                    <td align="center">
							<? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>
						</td>
	                    <td align="right"><? echo number_format($last_rec_qty,0,'',','); ?></td>
	                    <td align="right" placeholder='<? echo $row["product_id"];?>'>
							<? echo number_format(($lastThreeMonthDataArr[$row["product_id"]]/3),2);?>
						</td>
	                    <td align="right"><? echo $last_rec_rate;//$last_req_info[2]; ?></td>
	                    <td align="center">
							<?
								if(trim($last_req_info[0])!="0000-00-00" && trim($last_req_info[0])!="") echo change_date_format($last_req_info[0]).'<br>'; else echo "&nbsp;<br>";
								echo $last_req_info[1];
							?>
	                    </td>
						<td align="right"><? echo $row['amount'];?></td>
						<td><? echo $use_for[$row['required_for']]; ?></td>
	                    <td><? echo $row['remarks']; ?></td>
					</tr>
				<?
					$last_qnty += $last_rec_qty;
					$total_amount += $row['amount'];
					$total_requisition += $row['quantity'];
					$total_stock += $row['stock'];
					$i++;
				}
				?>
				<tr bgcolor="#dddddd">
	                <td align="right" colspan="3"><strong>Total : </strong></td>
	                <td align="right"><? echo number_format($total_requisition,0,'',','); ?></td>

	                <td align="right" ><? echo number_format($total_stock,2); ?></td>
	                <td align="right" ></td>
	                <td align="right" ></td>
	                <td align="right"><? echo number_format($last_qnty,0,'',','); ?></td>
	                <td align="right">&nbsp;</td>
	                <td align="right">&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right"><? echo number_format($total_amount,2); ?></td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
				</tr>
			<?
			}
			?>
            </tbody>
		</table>
		<br>
		<?
		
		$report_width= $cash+1050;
		echo signature_table(25, $data[0], $report_width."px",$cbo_template_id,70,$user_lib_name[$inserted_by]); ?>
		<?
	}
	else
	{
		if($data[5]==1)
		{
			$display_col="";
			$width_col=150;
			$span=1;
		}
		else
		{
			$display_col="display:none";
			$width_col='';
			$span='';
		}

		?>
		<table width="970" style=" margin-right:20px;">
            <tr class="form_caption">
            	<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
				<?
                $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />
                    <?
                }
                ?>
                </td>
                <td colspan="5" align="center" style="font-size:14px">
                <?
                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                foreach ($nameArray as $result)
                {
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?>
					Road No: <? echo $result[csf('road_no')]; ?>
					Block No: <? echo $result[csf('block_no')];?>
					City No: <? echo $result[csf('city')];?>
					Zip Code: <? echo $result[csf('zip_code')]; ?>
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
					Email Address: <? echo $result[csf('email')];?>
					Website No: <? echo $result[csf('website')];
                }
                $req=explode('-',$dataArray[0][csf('requ_no')]);
                ?>
                </td>
            </tr>
            <tr>
            	<td colspan="6" align="center" style="font-size:18px"><strong><u>Store <? echo $data[2] ?></u></strong></td>
            </tr>
            <tr>
                <td width="120"><strong>Req. No:</strong></td><td width="175"><? echo $req[2].'-'.$req[3]; ?></td>
                <td width="130"><strong>Item Catg:</strong></td> <td width="175"><? echo $item_category[$dataArray[0][csf('item_category_id')]]; ?></td>
                <td width="125"><strong>Source:</strong></td><td width="175"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Manual Req.:</strong></td> <td ><? echo $dataArray[0][csf('manual_req')]; ?></td>
                <td><strong>Department:</strong></td><td ><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
                <td><strong>Section:</strong></td><td ><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>
            <tr>
            	<td><strong>Req. Date:</strong></td><td><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
                <td><strong>Store Name:</strong></td><td><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
                <td><strong>Pay Mode:</strong></td><td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Location:</strong></td> <td><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                <td><strong>Currency:</strong></td> <td><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
                <td><strong>Del. Date:</strong></td><td><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
            </tr>
            <tr>
                <td><strong>Remarks:</strong></td> <td><? echo $dataArray[0][csf('remarks')]; ?></td>
                <td></td> <td></td>
                <td></td><td></td>
            </tr>
		</table>
		<br>
		<table width="<? echo $width_col+970; ?>" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th colspan="<? echo $span+12; ?>" align="center" >Item Details</th>
                    <th width="70" align="center" style="font-size:11px" rowspan="2">Avg. Monthly issue</th>
                    <th rowspan="2" style="font-size:12px">Avg. Monthly Rec.</th>
                </tr>
                <tr style="font-size:12px">
                    <th width="30">SL</th>
                    <th width="50">Item Code</th>
                    <th width="100" style=" <? echo $display_col; ?> ">Item Group</th>
                    <th width="180">Item Des.</th>
                    <th width="70">Req. For</th>
                    <th width="50">UOM</th>
                    <th width="60">Req. Qty.</th>
                    <th width="50">Rate</th>
                    <th width="70">Amount</th>
                    <th width="70">Stock</th>
                    <th width="70">Last Rec. Date</th>
                    <th width="70">Last Rec. Qty.</th>
                    <th width="50">Last Rate</th>
                </tr>
            </thead>
			<?
            $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
            $receive_array=array();
           /* $rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
            }*/
			 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form in (4,20) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
            }
            if($db_type==2)
			{
				$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
			}
			elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

			//echo "select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id";die;
			$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_issue_data=array();
			foreach($issue_sql as $row)
			{
				$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
			}

			//var_dump($prev_issue_data);die;

			$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_receive_data=array();
			foreach($receive_sql as $row)
			{
				$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
			}

            $i=1;

            // echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
            $sql_result= sql_select(" select a.id, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label, c.item_code
			from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  ");
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row[csf('quantity')];
				$quantity_sum += $quantity;
				$amount=$row[csf('amount')];
				$amount_sum += $amount;

				$current_stock=$row[csf('stock')];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);
				$last_rec_qty=0;
				$item_code=$row[csf('item_code')];
				/*if ($data[4]==1)
				{
					$item_code=$row[csf('item_account')];
				}
				else
				{
					$item_account=explode('-',$row[csf('item_account')]);
					$item_code=$item_account[3];
				}*/
				$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];
				$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td><? echo $item_code; ?></td>
                    <td style=" <? echo $display_col; ?> "><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                    <td>
					<?
					echo $row[csf('item_description')];
					if($row[csf('item_description')]!="" && $row[csf('item_size')]!="") echo ', ';
					echo $row[csf('item_size')];
					?></td>
                    <td><? echo $row[csf('required_for')]; ?></td>
                    <td><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                    <td align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('stock')],2); ?></td>
                    <td><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></td>
                    <td align="right"><? echo number_format($last_rec_qty,2); ?></td>
                    <td align="right"><? echo $last_rec_rate;//$last_req_info[2]; ?></td>
                    <td align="right">
                    <?
				    $min_issue_date=$prev_issue_data[$row[csf("product_id")]]["transaction_date"];
					$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
					$year_issue_total=$prev_issue_data[$row[csf("product_id")]]["isssue_qty"];
					$issue_avg=$year_issue_total/$month_issue_diff;
					echo number_format($issue_avg,2);
					//echo $row[csf("product_id")];
                    ?>
                    </td>
                    <td align="right">
					<?
					$min_receive_date=$prev_receive_data[$row[csf("product_id")]]["transaction_date"];
					$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
					$year_receive_total=$prev_receive_data[$row[csf("product_id")]]["receive_qty"];
					$receive_avg=$year_receive_total/$month_receive_diff;
					echo number_format($receive_avg,2);
					?>
                    </td>
				</tr>
				<?
				$total_last_qnty +=$last_rec_qty;
				$total_req_qnty+=$row[csf('quantity')];
				$total_amount+=$row[csf('amount')];
				$total_stock+=$row[csf('stock')];

				$i++;
			}
		$currency_id=$dataArray[0][csf('cbo_currency')];
	   $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	   }
	   if($currency_id==2)

	   {
		$mcurrency='USD';
		$dcurrency='CENTS';
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS';
	   }
			?>
            <tfoot>
            	<tr>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th style=" <? echo $display_col; ?> ">&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total:</th>
                    <th ><? echo number_format($total_req_qnty,0,'',','); ?></th>
                    <th >&nbsp;</th>
                    <th ><? echo number_format($total_amount,2);?></th>
                    <th ><? echo number_format($total_stock,2);?></th>
                    <th>&nbsp;</th>
                    <th ><? echo number_format($total_last_qnty,2);?></th>
                    <th>&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                </tr>
                 <tr>
                <th colspan="<? echo 14+$span; ?>"  style="border:1px solid black; text-align: center">
                Total Amount (In Word): <? echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency); ?>
                 </th>

             </tr>

            </tfoot>


		</table>
		<br>
		<?
		if($data[5]==1) $rpt_width=150+970; else  $rpt_width=970;

		echo signature_table(25, $data[0], $rpt_width."px",$cbo_template_id,70,$user_lib_name[$inserted_by]); ?>

		<?
	}
	exit();
}

if($action=="purchase_requisition_print_13") // Print Report 13
{
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$company=$data[0];
	$location=$data[7];
	$is_approved=$data[8];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,inserted_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');

	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	// $origin_lib=return_library_array( "select SHORT_NAME,id from lib_country where is_deleted=0  and status_active=1 order by SHORT_NAME", "id", "SHORT_NAME"  );
	$pay_cash=$dataArray[0][csf('pay_mode')];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$com_dtls = fnc_company_location_address($company, $location, 2);
	
    $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
	
	?>
	<table align="center">
        
		<tr>
            <td colspan="6" align="center">
            	<table align="center">
                    <tr class="form_caption">
                        <td  align="left" rowspan="2">
                        <?
                        foreach($data_array as $img_row)
                        {
                            if ($formate_id==122) 
                            {
                                ?>
                                <img src='../../<? echo $com_dtls[2]; ?>' height='70' align="middle" />
                                <?
                            }
                            else
                            {
                                ?>
                                <img src='../<? echo $com_dtls[2]; ?>' height='70' align="middle" />
                                <?
                            }
                        }
                        ?>
                        </td>
                        <td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $com_dtls[0]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="5" align="center" style="font-size:18px;">
                        	<? echo $com_dtls[1]; $req=explode('-',$dataArray[0][csf('requ_no')]);?>
                        </td>
                    </tr>
            	</table>
            </td>
		</tr>
		<tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><u><? echo $data[2] ?></u></strong></td>
		</tr>
		<tr>
			<td width="100" style="font-size:16px"><strong>Req. No</strong></td>
			<td width="300" style="font-size:16px">: <? echo $dataArray[0][csf('requ_no')];?></td>
			<td style="font-size:16px;" width="80"><strong>Req. Date</strong></td>
            <td style="font-size:16px;" width="300">: <? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td width="80" style="font-size:16px"><strong>Source</strong></td>
            <td style="font-size:16px">: <? echo $source[$dataArray[0][csf('source')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Manual Req.</strong></td> 
            <td style="font-size:16px">: <? echo $dataArray[0][csf('manual_req')]; ?></td>
			<td style="font-size:16px"><strong>Department</strong></td>
            <td style="font-size:16px">: <? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
			<td style="font-size:16px"><strong>Section</strong></td>
            <td style="font-size:16px">: <? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
		</tr>
		<tr>
			 <td style="font-size:16px"><strong>Del. Date</strong></td>
             <td style="font-size:16px">: <? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
			<td style="font-size:16px"><strong>Store Name</strong></td>
            <td style="font-size:16px">: <? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
			<td style="font-size:16px"><strong>Pay Mode</strong></td>
            <td style="font-size:16px">: <? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Location</strong></td> 
            <td style="font-size:16px">: <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
			<td style="font-size:16px"><strong>Currency</strong></td> 
            <td style="font-size:16px">: <? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
		   <td style="font-size:16px"><strong>Remarks</strong></td> 
           <td style="font-size:16px">:<? echo $dataArray[0][csf('remarks')]; ?></td>
		</tr>
	</table>
	
    
	<table cellspacing="0" width="1400"  border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="30" style="font-size:15px">SL</th>
				<th width="50" style="font-size:15px">Item Code</th>
				<th width="70" style="font-size:15px">Item Category</th>
				<th width="80" style="font-size:15px">Item Group</th>
				<!-- <th width="50">Item Number</th>
				<th width="70">Sub Group Name</th> -->
				<th width="190" style="font-size:15px">Item Des.</th>
				<!-- <th width="80">Brand</th>
				<th width="30">Origin</th> -->
				<th width="30" style="font-size:15px">UOM</th>
				<th width="30" style="font-size:15px">Req. Qty.</th>
				<!-- <th width="40">Rate</th>
				<th width="60">Amount</th> -->
				<th width="80" style="font-size:15px">Stock</th>
				<th width="80" style="font-size:15px">Last Rec. Date</th>
				<th width="80" style="font-size:15px">Last Rec. Qty.</th>
				<th width="60" style="font-size:15px">Last Rate</th>
				<!-- <th width="60">Requsition Value</th> -->
				<th width="80" style="font-size:15px">Avg. Monthly issue</th>
				<th width="80" style="font-size:15px">Avg. Monthly Rec.</th>
				<th width="150" style="font-size:15px">Last Supplier</th>
				<th width="150" style="font-size:15px">Item Issue Req/Dem No</th>
				<th style="font-size:15px">Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?

		$receive_array=array();

		$i=1;
		$sql= " SELECT a.id,b.id as dtls_id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks,b.itemissue_req_prefix_num , c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label,c.item_number 
		from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
		where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by b.item_category, c.item_group_id";
	    //echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$all_prod_ids.=$row[csf('product_id')].",";
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
			$all_data_array[$row[csf('cons_uom')]][$row[csf('dtls_id')]]['itemissue_req_prefix_num'] = $row[csf('itemissue_req_prefix_num')];
		}
		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;
		
		
		$rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and a.entry_form in (4,20) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		//var_dump($prev_issue_data);die;

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}



		foreach ($all_data_array as $cons_uom_id => $cons_uom_data) 
		{
			$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
			$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
        	foreach ($cons_uom_data as $dtls_id => $row) 
        	{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row['quantity'];
				$quantity_sum += $quantity;
				$amount=$row['amount'];
				//test
				$sub_group_name=$row['sub_group_name'];
				$amount_sum += $amount;

				$current_stock=$row['stock'];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);

				$item_account=explode('-',$row['item_account']);
				$item_code=$item_account[3];
				
				$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
				$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
				$last_rec_rate=$receive_array[$row['product_id']]['rate'];
				$last_rec_supp=$receive_array[$row['product_id']]['supplier_id'];


				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:15px">
					<td align="center"><div style="font-size:15px"><? echo $i; ?></div></td>
					<td><div style="font-size:15px"><? echo $row['item_code']; ?></div></td>
					<td><? echo $item_category[$row['item_category']]; ?></td>
					<td><? echo $item_name_arr[$row['item_group_id']]; ?></td>
					<!-- <td><div style="font-size:13px"><? echo $row['item_number']; ?></div></td>
					<td><div style="font-size:13px"><? echo $row['sub_group_name']; ?></div></td> -->
					<td><div style="word-wrap:break-word:50px;font-size:15px"><? echo $row["item_description"];?> </div></td>
					<!-- <td><div style="font-size:13px"><? echo $row["brand_name"];?></div></td>
					<td align="center"><div style="font-size:13px"><? echo $origin_lib[$row["origin"]]."<br>";?></div></td> -->
					<td align="center"><div style="font-size:15px"><? echo $unit_of_measurement[$row["cons_uom"]]; ?></div></td>
					<td align="right"><div style="font-size:15px"><? echo $row['quantity']; ?></div></td>
					<!-- <td align="right"><div style="font-size:13px"><? echo $row['rate']; ?></div></td>
					<td align="right"><div style="font-size:13px"><? echo $row['amount']; ?></div></td> -->
					<td align="right"><div style="font-size:15px"><? echo number_format($row['stock'],2); ?></div></td>
					<td align="center"><div style="font-size:15px"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></div></td>
					<td align="right"><div style="font-size:15px"><? echo number_format($last_rec_qty,0,'',','); ?></div></td>
					<td align="right"><div style="font-size:15px"><? echo $last_rec_rate;?></div></td>
					<!-- <td align="right">
	                    <div style="font-size:13px">
							<?
							$reqsit_value="";
							$reqsit_value=$last_rec_qty*$last_rec_rate;

							echo $reqsit_value;
							?>
	                    </div>
					</td> -->
					<td align="right">
	                    <div style="font-size:15px">
							<?
							$min_issue_date=$prev_issue_data[$row["product_id"]]["transaction_date"];
							if($min_issue_date=="")
							{
								echo number_format(0,2);
							}
							else
							{
								$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
								$year_issue_total=$prev_issue_data[$row["product_id"]]["isssue_qty"];
								$issue_avg=$year_issue_total/$month_issue_diff;
								echo number_format($issue_avg,2);
							}
							?>
	                    </div>
					</td>
					<td align="right">
                    	<div style="font-size:15px">
							<?
	                        $min_receive_date=$prev_receive_data[$row["product_id"]]["transaction_date"];
	                        if($min_receive_date=="")
	                        {
	                            echo number_format(0,2);
	                        }
	                        else
	                        {
	                            $month_receive_diff=datediff('m',$min_receive_date,$pc_date);
	                            $year_receive_total=$prev_receive_data[$row["product_id"]]["receive_qty"];
	                            $receive_avg=$year_receive_total/$month_receive_diff;
	                            echo number_format($receive_avg,2);
	                        }
	                        ?>
                       </div>
					</td>
					<td><div style="word-wrap:break-word:50px;font-size:15px"><? echo $supplier_array[$last_rec_supp]; ?></div></td>
					<td><div style="word-wrap:break-word:50px;font-size:15px"><? echo $row["itemissue_req_prefix_num"]; ?></div></td>
					<td ><div style="font-size:15px"><? echo $row['remarks']; ?></div></td>
				</tr>
				<?
				$total_requisition += $row['quantity'];
				$last_qnty += $last_rec_qty;
				$total_stock += $row['stock'];
				$total_amount += $row['amount'];
				$total_reqsit_value += $reqsit_value;
				$total_monthly_iss += $issue_avg;
				$total_monthly_rej += $receive_avg;
				$i++;
			}
			?>
				<tr bgcolor="#dddddd">
					<td align="right" colspan="6" style="font-size:15px"><strong>Sub Total : </strong></td>
					<td align="right" style="font-size:15px"><? echo number_format($total_requisition,0,'',','); ?></td>
					<!-- <td align="right">&nbsp;</td>
					<td align="right"><? echo number_format($total_amount,0,'',','); ?></td> -->
					<td align="right" style="font-size:15px"><? echo number_format($total_stock,0,'',','); ?></td>
					<td align="right" ></td>
					<td align="right" style="font-size:15px"><? echo number_format($last_qnty,0,'',','); ?></td>
					<td align="right">&nbsp;</td>
					<!-- <td align="right"><? echo $total_reqsit_value;?></td> -->
					<td align="right" style="font-size:15px"><? echo number_format($total_monthly_iss,0,'',','); ?></td>
					<td align="right" style="font-size:15px"><? echo number_format($total_monthly_rej,0,'',','); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			<?
			$Grand_tot_total_amount += $total_amount;
			$Grand_tot_last_qnty += $last_qnty;
			$Grand_tot_reqsit_value += $total_reqsit_value;
			$Grand_tot_total_stock += $total_stock;
			$Grand_tot_monthly_iss += $total_monthly_iss;
			$Grand_tot_monthly_rej += $total_monthly_rej;
		}
		?>
		</tbody>
		<!-- <tr bgcolor="#B0C4DE">
			<td align="right" colspan="7" style="font-size:15px"><strong>Total : </strong></td>
			<td align="right"><? echo number_format($Grand_tot_total_amount,0,'',','); ?></td>
			<td align="right" style="font-size:15px" ><? echo number_format($Grand_tot_total_stock,0,'',','); ?></td>
			<td align="right" ></td>
			<td align="right" style="font-size:15px"><? echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($Grand_tot_reqsit_value,0,'',',');?></td>
			<td align="right" style="font-size:15px"><? echo number_format($Grand_tot_monthly_iss,0,'',',');?></td>
			<td align="right" style="font-size:15px"><? echo number_format($Grand_tot_monthly_rej,0,'',',');?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr> -->
	</table>
	<!-- <span><strong style="font-size:15px">Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span> -->
	<br>
 
    
    
    
	<?
	$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
    $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=1 AND  mst_id ='$data[1]' order by  approved_no,approved_date");

    $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

    foreach ($approved_his_sql as $key => $row) 
    {
    	$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
    	if ($row[csf('un_approved_date')]!='') 
    	{
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
    		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
    	}	    
    }
   
    $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
    $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

    if(count($approved_sql) > 0)
    {
        $sl=1; 
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Requisition Approval Status </b></label>  
                <thead>           
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>
	                    <th width="250">Name</th>
	                    <th width="200">Designation</th>
	                    <th width="100">Approval Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_sql as $key => $value) 
                { 
                    ?>
                    <tr>
                        <td width="20"><? echo $sl; ?></td>
                        <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
                    </tr>
                    <? 
                    $sl++; 
                }  
                ?>                
            </table>
        </div>
        <? 
    } 
		
	if(count($approved_his_sql) > 0)
    {
        $sl=1; 
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Purchase Requisition Approval / Un-Approval History </b></label>
                <thead>             
	                <tr style="font-weight:bold">
	                    <th width="20">SL</th>

	                    <th width="150">Approved / Un-Approved</th>
	                    <th width="150">Designation</th>
	                    <th width="50">Approval Status</th>
	                    <th width="150">Reason for Un-Approval</th>
	                    <th width="150">Date</th>
	                </tr>
            	</thead>
                <? foreach ($approved_his_sql as $key => $value) 
                { 
                	if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
                	?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="20"><? echo $sl; ?></td>
                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                        <td width="50">Yes</td>
                        <td width="150"><? echo $unapproved_request_arr[$value[csf("mst_id")]]; ?></td>
                        <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);
						
						echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    </tr>
                    <? 
				    $sl++; 
                    $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                    $un_approved_date=$un_approved_date[0];
                    if($db_type==0) //Mysql
                    {
                        if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }
                    else
                    {
                        if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                    }
                    
                    if($un_approved_date!="")
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
	                        <td width="20"><? echo $sl; ?></td>
	                        <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
	                        <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
	                        <td width="50">No</td>
	                        <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
	                        <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]); 
							echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                    	</tr>
						
						<?
						$sl++; 						
					} 
                }  
                ?>                    
            </table>
        </div>
        <style>
        table thead tr th, table tbody tr td{
			wordwrap: break-word;
			break-ward: break-word;
			font-size:13px!important;
		}
		@media print{
			table {
				border:solid #000 !important;
				border-width:1px 0 0 1px !important;				
			}
			
			table thead th, 
			table tbody th p,
			table tbody td p,
			.rpt_table p{
				border:solid #000 !important;
				border-width:0 1px 1px 0 !important;
				padding:0!important;
				margin:0!important;
			}
			
			.p-font {
				font-size:10px!important;
			}
		}
		
		
		</style>

        <? 
    }
	//approved status end
	echo signature_table(25, $data[0], "1250px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	exit();
}

if($action=="purchase_requisition_category_wise_print") // Category Wise
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Category Wise","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print($data[5]);
	 //print_r($data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$show_item=$data[5];
	$cbo_template_id=$data[6];
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,req_by from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
	$requisition_date=$dataArray[0][csf("requisition_date")];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
	//echo $requisition_date."==".$requisition_date_last_year;die;
	
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, address from lib_location",'id','address');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

	$pay_cash=$dataArray[0][csf('pay_mode')];

	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");

	if($show_item==1){
		$tbl_width=1250;
		$colspan=16;
		$colspan2=14;
		$tot_colspan=10;
	}
	else{
		$tbl_width=1060;
		$colspan=13;
		$colspan2=11;
		$tot_colspan=7;
	}
	?>

  	<style type="text/css">
  		@media print
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
	  			border: 1px solid #dccdcd !important;
	  		}

			@page{
				margin-bottom: 70px;
			}
  		}
		table thead tr th, table tbody tr td{
			wordwrap: break-word;
			break-ward: break-word;
		}
		.left{text-align: left;}
	
  	</style>
	<div style="max-width:<?=$tbl_width;?>px;height: 100%">
	<!-- class="rpt_table" -->
	<table cellspacing="0" width="<?=$tbl_width;?>"  border="1" rules="all" class="rpt_table" style="border: 1px;font-size: 18px;" >
		<thead  align="center">
			<tr >
				<th class="left" colspan="2" rowspan="3" style="border:none;" >
				<?
					if ($formate_id==123) 
					{
						?><img src='../../<?=$data_array[0][csf('image_location')]; ?>' height='70' width='100' align="middle" /><?
					}
					else
					{
						?><img src='../<?=$data_array[0][csf('image_location')]; ?>' height='70' width='100' align="middle" /><?
					}
				?>
				</th>
				<th colspan="<?=$colspan2;?>" align="center" style="font-size:25px;border:none;"><strong><?=$data[2];?></strong></th>
			</tr>
			<tr >
				<th colspan="<?=$colspan2;?>" align="center"  style="font-size:20px;border:none;"><strong><?=$company_library[$data[0]]; ?></strong></th>
			</tr>
			<tr>
				<th colspan="<?=$colspan2;?>" align="center" style="font-size:18px;border:none;"><strong><?=$location_arr[$data[7]]; ?></strong></th>
			</tr>
			<tr>
				<th colspan="2" style="font-size:16px;" class="left"><strong>Req. No:</strong></th>
				<th style="font-size:16px" class="left"><strong><?=$dataArray[0][csf('requ_no')];?></strong></th>				
				<th colspan="2" style="font-size:16px;" class="left"><strong>Req. Date:</strong></th>
				<th colspan="3" style="font-size:16px;" class="left"><?=change_date_format($dataArray[0][csf('requisition_date')]);?></th>
				<th colspan="<?=($show_item==1)?3:2;?>" style="font-size:16px" class="left"><strong>Currency:</strong></th>
				<th colspan="<?=($show_item==1)?5:3;?>" style="font-size:16px" class="left"><?=$currency[$dataArray[0][csf('cbo_currency')]]; ?></th>
			</tr>
			<tr>
				<th colspan="2" style="font-size:16px" class="left"><strong>Department:</strong></th>
				<th style="font-size:16px" class="left"><?=$department[$dataArray[0][csf('department_id')]]; ?></th>
				<th colspan="2" style="font-size:16px" class="left"><strong>Section:</strong></th>
				<th colspan="3" style="font-size:16px" class="left"><?=$section[$dataArray[0][csf('section_id')]]; ?></th>
				<th colspan="<?=($show_item==1)?3:2;?>" style="font-size:16px" class="left"><strong>Store Name:</strong></th>
				<th colspan="<?=($show_item==1)?5:3;?>" style="font-size:16px" class="left"><?=$store_library[$dataArray[0][csf('store_name')]]; ?></th>
			</tr>
			<tr>
				<th colspan="<?=$colspan;?>" style="font-size: 12px;" align="center" ><strong>Item Details</strong></th>
			</tr>
			<tr>
				<th width="30" style="font-size: 12px;">SL</th>
				
				<th width="80" style="font-size: 12px;">Item Group</th>
				<th width="200" style="font-size: 12px;">Item Description</th>
				<?
				if($show_item==1)
				{
					?>
						<th width="60" style="font-size: 12px;">Model / Article</th>
						<th width="50" style="font-size: 12px;">Size/MSR</th>
						<th width="50" style="font-size: 12px;">Brand</th>
					<?
				}
				?>
				<th width="50" style="font-size: 12px;">UOM</th>
				<th width="70" style="font-size: 12px;"> Stock</th>
				<th width="70" style="font-size: 12px;">Req. Qty.</th>
				<th width="80" style="font-size: 12px;">Rate</th>
				<th width="70" style="font-size: 12px;">Total Amount</th>
				<th width="70" style="font-size: 12px;">Last Rcv. Date</th>
				<th width="70" style="font-size: 12px;">Last Rcv. Qty.</th>
				<th width="60" style="font-size: 12px;">Last. Rate</th>
				
				<th width="70" style="font-size: 12px;">Last Month issue</th>
				
				<th style="font-size: 12px;">Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();
		$i=1;
		// $sql= " SELECT a.id,b.id as dtls_id,b.item_category,b.brand_name,b.origin, b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks,b.brand_name, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size,c.brand_name as prod_brand_name, c.model as prod_model, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label, c.item_number, a.company_id 
		// from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
		// where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by b.id";

		$sql="SELECT d.store_name, a.id,a.company_id, a.item_account,b.id as dtls_id, a.item_code, a.item_description, a.item_size, a.item_group_id, a.sub_group_name, b.required_for, a.order_uom as unit_of_measure, a.re_order_label, b.id, b.quantity, b.rate, b.amount, b.stock, b.status_active, b.remarks, c.item_name, b.cons_uom, b.delivery_date, b.item_category,b.brand_name,b.origin,b.model, null as requisition_date,b.product_id, a.item_category_id,a.item_number,a.brand_name as prod_brand_name,a.model as prod_model,a.current_stock from inv_purchase_requisition_mst d, inv_purchase_requisition_dtls b, product_details_master a, lib_item_group c 
		where d.id=b.mst_id and b.product_id=a.id and a.item_group_id=c.id and b.mst_id='$update_id' and b.is_deleted=0 
		union all 
		select 0 as store_name, null as id, null as company_id, null as item_account,b.id as dtls_id, null as item_code, b.service_details as item_description, null as item_size, 0 as item_group_id, null as sub_group_name, b.required_for, b.cons_uom as unit_of_measure, 0 as re_order_label, b.id, b.quantity, b.rate, b.amount, b.stock, b.status_active, b.remarks, null as item_name, b.cons_uom, b.delivery_date, b.item_category,b.brand_name,b.origin,b.model,d.requisition_date,b.product_id, null as item_category_id, null as item_number,  null as prod_brand_name, null as prod_model, 0 as current_stock
		from inv_purchase_requisition_dtls b, inv_purchase_requisition_mst d
		where b.is_deleted=0 and d.id=b.mst_id and b.mst_id='$update_id' and b.product_id=0";
	    // echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{

			$all_prod_ids.=$row[csf('product_id')].",";
			$all_store_ids.=$row[csf('store_name')].",";
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['id'] = $row[csf('id')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['company_id'] = $row[csf('company_id')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_category'] = $row[csf('item_category')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['brand_name'] = $row[csf('brand_name')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['origin'] = $row[csf('origin')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['model'] = $row[csf('model')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['requisition_date'] = $row[csf('requisition_date')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['product_id'] = $row[csf('product_id')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['required_for'] = $row[csf('required_for')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['quantity'] = $row[csf('quantity')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['rate'] = $row[csf('rate')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['amount'] = $row[csf('amount')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['stock'] = $row[csf('stock')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['remarks'] = $row[csf('remarks')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_account'] = $row[csf('item_account')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_category_id'] = $row[csf('item_category_id')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_description'] = $row[csf('item_description')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['sub_group_name'] = $row[csf('sub_group_name')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_code'] = $row[csf('item_code')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_number'] = $row[csf('item_number')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_size'] = $row[csf('item_size')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['prod_brand_name'] = $row[csf('prod_brand_name')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['prod_model'] = $row[csf('prod_model')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['item_group_id'] = $row[csf('item_group_id')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['unit_of_measure'] = $row[csf('unit_of_measure')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['current_stock'] = $row[csf('current_stock')];
			$all_data_array[$row[csf('item_category')]][$row[csf('dtls_id')]]['re_order_label'] = $row[csf('re_order_label')];
		}
		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;
		$all_store_ids=implode(",",array_unique(explode(",",chop($all_store_ids,","))));
		if($all_store_ids=="") $all_store_ids=0;

		$prod_sql="select company_id, item_category_id, item_group_id, sub_group_name, item_description, item_size, model, item_number, item_code 
		from product_details_master where status_active=1 and id in ($all_prod_ids)";
		$prod_sql_result=sql_select($prod_sql);
		foreach($prod_sql_result as $row)
		{
			$prod_company=$row[csf("company_id")];
			$prod_category[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
			$prod_group[$row[csf("item_group_id")]]=$row[csf("item_group_id")];
			$pord_description.="'".$row[csf("item_description")]."',";
		}
		$pord_description=chop($pord_description,",");
		$rcv_cond="";
		if($prod_company) $rcv_cond.=" and c.company_id=$prod_company";
		if(count($prod_category)>0) $rcv_cond.=" and c.item_category_id in(".implode(",",$prod_category).")";
		if(count($prod_group)>0) $rcv_cond.=" and c.item_group_id in(".implode(",",$prod_group).")";
		if($pord_description) $rcv_cond.=" and c.item_description in($pord_description)";
		
		$rec_sql="select b.id, b.item_category, b.prod_id, b.transaction_date as transaction_date, b.supplier_id, b.cons_quantity as rec_qty, cons_rate as cons_rate, c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code 
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_basis not in(6) and b.store_id in($all_store_ids) $rcv_cond 
		order by  b.id ";
		//echo  $rec_sql;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$item_key=$row[csf('company_id')]."*".$row[csf('item_category_id')]."*".$row[csf('item_group_id')]."*".$row[csf('sub_group_name')]."*".$row[csf('item_description')]."*".$row[csf('item_size')]."*".$row[csf('model')]."*".$row[csf('item_number')]."*".$row[csf('item_code')];
			$receive_array[$item_key]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$item_key]['rec_qty']=$row[csf('rec_qty')];
			$receive_array[$item_key]['rate']=$row[csf('cons_rate')];
			$receive_array[$item_key]['supplier_id']=$row[csf('supplier_id')];
		}
		
		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";
		
		$last_month_issue_sql=sql_select("select c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code, sum(b.cons_quantity) as isssue_qty 
		from  inv_transaction b, product_details_master c  
		where b.prod_id=c.id and b.transaction_type=2 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and transaction_date >= add_months(trunc(sysdate,'mm'),-1)  and transaction_date < add_months(trunc(sysdate,'mm'),0) and b.store_id in($all_store_ids) $rcv_cond 
		group by c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code");
		
		$last_month_issue_data=array();
		foreach($last_month_issue_sql as $row)
		{
			$item_key=$row[csf('company_id')]."*".$row[csf('item_category_id')]."*".$row[csf('item_group_id')]."*".$row[csf('sub_group_name')]."*".$row[csf('item_description')]."*".$row[csf('item_size')]."*".$row[csf('model')]."*".$row[csf('item_number')]."*".$row[csf('item_code')];
			$last_month_issue_data[$item_key]["prod_id"]=$row[csf("prod_id")];
			$last_month_issue_data[$item_key]["isssue_qty"]=$row[csf("isssue_qty")];
		}

		

		$receive_last_month_sql=sql_select("select b.prod_id, c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code, sum(cons_quantity) as receive_qty 
		from  inv_transaction b, product_details_master c  

		where b.prod_id=c.id and b.transaction_type=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) $rcv_cond
		group by b.prod_id, c.company_id, c.item_category_id, c.item_group_id, c.sub_group_name, c.item_description, c.item_size, c.model, c.item_number, c.item_code");

		$last_month_receive_data=array();
		foreach($receive_last_month_sql as $row)
		{
			$item_key=$row[csf('company_id')]."*".$row[csf('item_category_id')]."*".$row[csf('item_group_id')]."*".$row[csf('sub_group_name')]."*".$row[csf('item_description')]."*".$row[csf('item_size')]."*".$row[csf('model')]."*".$row[csf('item_number')]."*".$row[csf('item_code')];
			$last_month_receive_data[$item_key]["prod_id"]=$row[csf("prod_id")];
			$last_month_receive_data[$item_key]["receive_qty"]=$row[csf("receive_qty")];
		}


		// echo "<pre>";
		// print_r($all_data_array);
		$previos_item_category='';
		$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
		$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
		$last_issue=0;
		$last_receive=0;
		$i=1;
		foreach ($all_data_array as $category_key => $category_val) 
		{
			foreach($category_val as $dtls_id => $row)
			{
				$item_cat=$row['item_category'];
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($previos_item_category!=$item_cat)
				{
					if($i>1)
					{ 	
						?>
						<tr bgcolor="#dddddd">
							<td  colspan="10"><strong>Sub Total : </strong></td>
							<td align="right"><strong><? echo number_format($total_amount,2); ?></strong></td>
							
							<td colspan="5">&nbsp;</td>
						</tr>
						<? 
						$total_amount=0;$last_qnty=0;$total_reqsit_value=0;
						$total_monthly_rej=0;$total_monthly_iss=0;$total_stock=0;
						$previos_item_category=$item_cat;
						$last_issue=0;
						$last_receive=0;
					}

					?>
					<tr bgcolor="#dddddd">
						<td colspan="16" align="left" >Category : <? echo $item_category[$row["item_category"]]; ?></td>
					</tr>
					<?
				}
				
				$item_key=$row['company_id']."*".$row['item_category']."*".$row['item_group_id']."*".$row['sub_group_name']."*".$row['item_description']."*".$row['item_size']."*".$row['model']."*".$row['item_number']."*".$row['item_code'];
					
				$quantity=$row['quantity'];
				$quantity_sum += $quantity;
				$amount=$row['amount'];
				//test
				$sub_group_name=$row['sub_group_name'];
				$amount_sum += $amount;

				$current_stock=$row['stock'];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);

				$item_account=explode('-',$row['item_account']);
				$item_code=$item_account[3];
				/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
				$last_rec_date=$receive_array[$item_key]['transaction_date'];
				$last_rec_qty=$receive_array[$item_key]['rec_qty'];
				$last_rec_rate=$receive_array[$item_key]['rate'];
				$last_rec_supp=$receive_array[$item_key]['supplier_id'];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px; " >
					<td align="center"><? echo $i; ?></td>
					<td><p><? echo $item_name_arr[$row['item_group_id']]; ?></p></td>
					<td><p> <? echo $row["item_description"];?> </p></td>
					<?
					if($show_item==1)
					{
						$model_artical="";
						if($row["prod_model"]!="" || $row["model"]!="" )
						{
							if($row["prod_model"]!="")
							{
								if($row["prod_model"]!="" && $row["item_number"]!="" )
								{
									$model_artical=$row["prod_model"].' / '.$row["item_number"];
								}
								else
								{
									$model_artical=$row["prod_model"];
								}
							}
							else
							{
								if($row["model"]!="" || $row["item_number"]!="" )
								{
									if($row["model"]!="" && $row["item_number"]!="" )
									{
										$model_artical=$row["model"].' / '.$row["item_number"];
									}
									else
									{
										$model_artical=$row["model"];
									}
								}
							}
						}
						else
						{
							$model_artical=$row["item_number"];
						}
						?>
							<td><p><? echo $model_artical;?> </p></td>
							<td><p><? echo $row["item_size"]; ?></p></td>
							<td><p><? echo ($row["prod_brand_name"]!="")? $row["prod_brand_name"]:$row["brand_name"]; ?></p></td>
						<?
					}
					?>
					<td align="center"><p><? echo $unit_of_measurement[$row["unit_of_measure"]]; ?></p></td>
					<td align="right"><p><? echo number_format($row['stock'],2); ?></p></td>
					<td align="right"><p><? echo $row['quantity']; ?>&nbsp;</p></td>
					<td align="right"><? echo $row['rate']; ?></td>
					<td align="right"><? echo $row['amount']; ?></td>
					<td align="center"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($last_rec_rate,2);//$last_req_info[2]; ?>&nbsp;</p></td>
					
					<td align="right">
						<? echo number_format($last_month_issue_data[$item_key]["isssue_qty"],2); ?>
					</td>
					<td align="left"><? echo $row['remarks']; ?></td>
				</tr>
				<?
				$total_amount += $row['amount'];
				$Grand_tot_total_amount += $row['amount'];
				$previos_item_category=$item_cat;
				$i++;
								
			}
			
		}
		?>
			<tr bgcolor="#dddddd">
				<td  colspan="<?=$tot_colspan;?>"><strong>Sub Total : </strong></td>
				<td align="right"><strong><? echo number_format($total_amount,2); ?></strong></td>
				<td align="right" colspan="5">&nbsp;</td>
				
			</tr>
			<tr bgcolor="#B0C4DE">
				<td  colspan="<?=$tot_colspan;?>"><strong>Grand Total : </strong></td>
				<td align="right"><strong><? echo number_format($Grand_tot_total_amount,2); ?></strong></td>
				
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="<?=$colspan;?>" ><strong>Total Amount (In Word): <? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></td>
			</tr>
			<tr>
				<td colspan="<?=$colspan;?>" align="left"><strong>Remarks: <? echo $dataArray[0][csf('remarks')]; ?></strong></td>
			</tr>
		</tbody>
	</table>
	
	<?=signature_table(25, $data[0], $tbl_width."px",$cbo_template_id,40,$user_lib_name[$inserted_by],"",8);?>

	</div>
	<?
	exit();
}

if($action=="purchase_requisition_print_24") // Print 9
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$sql="SELECT id as  ID, requ_no as  REQU_NO, item_category_id as  ITEM_CATEGORY_ID, requisition_date as  REQUISITION_DATE, location_id as  LOCATION_ID, delivery_date as DELIVERY_DATE, source as SOURCE, manual_req as MANUAL_REQ, department_id as departmEnt_id, section_id as SECTION_ID, store_name as STORE_NAME, pay_mode as PAY_MODE, cbo_currency as CBO_CURRENCY, remarks as REMARKS,req_by as REQ_BY, is_approved as IS_APPROVED from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
	$requisition_date=$dataArray[0]["REQUISITION_DATE"];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
	
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );

	$pay_cash=$dataArray[0]['PAY_MODE'];
	?>

  	<style type="text/css">
  		@media print
  		{
  		 .main_tbl td {
  				margin: 0px;padding: 0px;
  			}
  			.rpt_tables, .rpt_table{
	  			border: 1px solid #dccdcd !important;
	  		}
  		}
  	</style>
	<div id="table_row" style="max-width:1020px; margin-left: 2px;">

	<table cellspacing="0"  width="1350" class="rpt_tables">
		<tr class="form_caption">
				<?
					$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
			<td  align="left" rowspan="2" style="width: 300px;">
				<?
					foreach($data_array as $img_row)
					{
						if ($formate_id==123) 
						{
							?>
							<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='100' width='100' align="middle" />
							<?
						}
						else
						{
							?>
							<img src='../<? echo $img_row[csf('image_location')]; ?>' height='100' width='100' align="middle" />
							<?
						}
					}
				?>
			</td>
			<td colspan="6" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>	
		</tr>
		<tr class="form_caption">
			<td colspan="6" align="center" style="font-size:18px;">
				<?
					echo $location_arr[$dataArray[0]['LOCATION_ID']];
					// $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					// foreach ($nameArray as $result)
					// {
						?>
						<!-- Road No: <? //echo $result[csf('road_no')]; ?> -->
						<!-- Plot No: <?// echo $result[csf('plot_no')]; ?> -->
						<!-- Block No: <?// echo $result[csf('block_no')];?> -->
						<!-- City No: <? //echo $result[csf('city')];?> -->
						<!-- Zip Code: <? //echo $result[csf('zip_code')]; ?> -->
						<!-- Country: <? //echo $country_arr[$result[csf('country_id')]]; ?><br> -->
						<!-- Email Address: <?// echo $result[csf('email')];?> -->
						<!-- Website No:  -->
						<?// echo $result[csf('website')];
					//}
					// $req=explode('-',$dataArray[0]['REQU_NO']);
					// $approved=$dataArray[0]['IS_APPROVED'];
				?>
			</td>
			<td style="width: 300px;">&nbsp;</td>
		</tr> 
	</table>
		
	<br>
	<style type="text/css">
		table thead tr th, table tbody tr td{
			word-break: break-all;
		}

		#tbl_purchase_requisition thead{
			display: table-header-group;
		}

		#tbl_purchase_requisition tbody{
			display: table-row-group;
		}

		#tbl_purchase_requisition tfoot{
			display: table-footer-group;
		}

		#signatureTblId td{
			border: none!important;
		}

		table thead .border_none td{
			border: none!important;
			text-align: left;
		}
		
	</style>

	<table id="tbl_purchase_requisition" cellspacing="0" width="1350"  rules="all" class="rpt_table" style="border-collapse: separate; border: 0px;font-size: 16px;" >
		<thead align="center">
			<tr class="border_none">
				<td colspan="25" align="right" style="font-size:20px;color:red;"><strong><? if($approved==1){echo "Approved";} ?></strong></td>
			</tr>
			<tr class="border_none">
				<td colspan="25"  style="font-size:20px; text-align:center;"><strong><u><? echo $data[2] ?></u></strong></td>
			</tr>
			<tr class="border_none">
				<td colspan="3" width="165px" style="font-size:14px"><strong>Req. No:</strong></td>
				<td colspan="3" width="175px" style="font-size:14px"><strong><? echo $dataArray[0]['REQU_NO'];?></strong></td>
				<td colspan="3" style="font-size:14px;" width="165px"><strong>Req. Date:</strong></td>
				<td colspan="4" style="font-size:14px;" width="165px"><? echo change_date_format($dataArray[0]['REQUISITION_DATE']);?></td>
				<td colspan="3" width="165px" style="font-size:14px"><strong>Source:</strong></td>
				<td colspan="3" width="165px" style="font-size:14px"><? echo $source[$dataArray[0]['SOURCE']]; ?></td>
				<td colspan="3" style="font-size:14px" width="165px"><strong>Currency:</strong></td> 
				<td colspan="3" style="font-size:14px"><? echo $currency[$dataArray[0]['CBO_CURRENCY']]; ?></td>
			</tr>
			<tr class="border_none">
				<td colspan="3" style="font-size:14px"><strong>Manual Req.:</strong></td> 
				<td colspan="3" width="175px" style="font-size:14px"><? echo $dataArray[0]['MANUAL_REQ']; ?></td>
				<td colspan="3" style="font-size:14px"><strong>Department:</strong></td>
				<td colspan="4" width="175px" style="font-size:14px"><? echo $department[$dataArray[0]['DEPARTMENT_ID']]; ?></td>
				<td colspan="3" style="font-size:14px"><strong>Section:</strong></td>
				<td colspan="3" width="175px" style="font-size:14px"><? echo $section[$dataArray[0]['SECTION_ID']]; ?></td>
				<td colspan="3" style="font-size:14px"><strong>Remarks:</strong></td> 
				<td colspan="3" style="font-size:14px"><? echo $dataArray[0]['REMARKS']; ?></td>
			</tr>
			<tr class="border_none">
				<td colspan="3" style="font-size:14px"><strong>Del. Date:</strong></td>
				<td colspan="3" style="font-size:14px"><? echo change_date_format($dataArray[0]['DELIVERY_DATE']);?></td>
				<td colspan="3" style="font-size:14px"><strong>Store Name:</strong></td>
				<td colspan="4" style="font-size:14px"><? echo $store_library[$dataArray[0]['STORE_NAME']]; ?></td>
				<td colspan="3" style="font-size:14px"><strong>Pay Mode:</strong></td>
				<td colspan="3" style="font-size:14px"><? echo $pay_mode[$dataArray[0]['PAY_MODE']]; ?></td>
				<td colspan="3" style="font-size:14px">&nbsp;</td>
				<td colspan="3" style="font-size:14px">&nbsp;</td>
			</tr>
			<tr>
				<!-- <td style="font-size:20px"><strong>Location:</strong></td>  -->
				<!-- <td style="font-size:20px"><?// echo $location_arr[$dataArray[0]['LOCATION_ID']]; ?></td> -->
				<!-- <td style="font-size:20px"><strong>Currency:</strong></td> 
				<td style="font-size:20px"><?// echo $currency[$dataArray[0]['CBO_CURRENCY']]; ?></td> -->
				<!-- <td style="font-size:20px"><strong>Remarks:</strong></td> 
				<td style="font-size:20px"><?// echo $dataArray[0]['REMARKS']; ?></td> -->
			</tr>
			<tr>
				<th width="30" rowspan="3">SL</th>
				<th width="100" rowspan="3">Item Group</th>
				<th width="90" rowspan="3">Item Des</th>
				<th width="68" rowspan="3">Brand</th>
				<th width="66" rowspan="3">Origin</th>
				<th width="40" rowspan="3">UOM</th>
				<th width="50" rowspan="3">Re-Order Level</th>
                <th width="40" rowspan="3">Loan</th>
				<th width="46" rowspan="3">Req. Qty</th>
				<th width="40" rowspan="3">Rate</th>
				<th width="50" rowspan="3">Amount</th>
				<th width="50" rowspan="3">Stock</th>
				<th width="70" rowspan="3">Last Rec. Date</th>
				<th width="50" rowspan="3">Last Rec. Qty.</th>
				<th width="40" rowspan="3">Last Rate</th>
				<th width="60" rowspan="3">Last Rec. Value</th>
				<th width="50" rowspan="3">Last Supplier</th>
				<th width="300" colspan="6">Consumption</th>
				<th width="50" rowspan="3">Total Used/Issued Qty</th>
				<th width="100" rowspan="3" >Remarks</th>
			</tr>
			<tr>
				<th width="100" colspan="2">Last Month</th>
				<th width="100" colspan="2">Last 3 Month</th>
				<th width="100" colspan="2">Last 6 Month</th>
			</tr>
			<tr>
				<th width="20">Rec.</th>
				<th width="20">Issue</th>
				<th width="20">Rec.</th>
				<th width="20">Issue</th>
				<th width="20">Rec.</th>
				<th width="20">Issue</th>
			</tr>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= "SELECT a.id as ID,b.id as DTLS_ID,b.item_category as ITEM_CATEGORY,b.brand_name as BRAND_NAME,b.origin as ORIGIN,b.model as MODEL, a.requisition_date as REQUISITION_DATE, b.product_id as PRODUCT_ID, b.required_for as REQUIRED_FOR, b.cons_uom as CONS_UOM, b.quantity as QUANTITY, b.rate as RATE, b.amount as AMOUNT, b.stock as STOCK,b.remarks as REMARKS, c.item_account as ITEM_ACCOUNT, c.item_category_id as ITEM_CATEGORY_ID, c.item_description as ITEM_DESCRIPTION,c.sub_group_name as SUB_GROUP_NAME,c.item_code as ITEM_CODE, c.item_size as ITEM_SIZE, c.item_group_id as ITEM_GROUP_ID, c.unit_of_measure as UNIT_OF_MEASURE, c.current_stock as CURRENT_STOCK, c.re_order_label as RE_ORDER_LABEL,c.item_number as ITEM_NUMBER 
		from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by a.id";
	    //echo $sql;die;
		$sql_result=sql_select($sql);

        $sql_dyes_chem_loan_recv = sql_select("select c.item_description, c.unit_of_measure, c.item_code, c.item_group_id, sum(b.cons_quantity) as QTY from inv_receive_master a, inv_transaction b, product_details_master c where a.id = b.mst_id and b.prod_id = c.id and a.entry_form = 4 and a.company_id = $data[0] and a.item_category in (5,6,7,23) and a.receive_purpose = 5 and b.transaction_type = 1 and a.status_active = 1 and a.is_deleted = 0 group by  c.item_description, c.unit_of_measure, c.item_code, c.item_group_id");
        $dyes_chem_loan_recv = array();
        foreach ( $sql_dyes_chem_loan_recv as $loanData){
            $dyes_chem_loan_recv[$loanData[csf('item_description')]."**".$loanData[csf('item_group_id')]."**".$loanData[csf('unit_of_measure')]."**".$loanData[csf('item_code')]] = $loanData['QTY'];
        }
        $sql_dyes_chem_loan_recv_rtn = sql_select("select c.item_description, c.unit_of_measure, c.item_code, c.item_group_id, sum(b.cons_quantity) as QTY from inv_issue_master a, inv_transaction b, product_details_master c where a.id = b.mst_id and b.prod_id = c.id and a.entry_form = 28 and a.company_id = $data[0] and a.item_category in (5,6,7,23) and b.transaction_type = 3 and a.status_active = 1 and a.is_deleted = 0 GROUP by  c.item_description, c.unit_of_measure, c.item_code, c.item_group_id");
        $dyes_chem_loan_recv_rtn = array();
        foreach ( $sql_dyes_chem_loan_recv_rtn as $loanData){
            $dyes_chem_loan_recv_rtn[$loanData[csf('item_description')]."**".$loanData[csf('item_group_id')]."**".$loanData[csf('unit_of_measure')]."**".$loanData[csf('item_code')]] = $loanData['QTY'];
        }
        $sql_dyes_chem_loan_issue = sql_select("select c.item_description, c.unit_of_measure, c.item_code, c.item_group_id, sum(b.cons_quantity) as qty from inv_issue_master a, inv_transaction b, product_details_master c where a.id = b.mst_id and b.prod_id = c.id and a.entry_form = 5 and a.company_id = $data[0] and b.transaction_type = 2 and a.status_active = 1 and a.is_deleted = 0 and a.issue_purpose = 5 GROUP by  c.item_description, c.unit_of_measure, c.item_code, c.item_group_id");
        $dyes_chem_loan_issue = array();
        foreach ( $sql_dyes_chem_loan_issue as $loanData){
            $dyes_chem_loan_issue[$loanData[csf('item_description')]."**".$loanData[csf('item_group_id')]."**".$loanData[csf('unit_of_measure')]."**".$loanData[csf('item_code')]] = $loanData['QTY'];
        }
        $sql_dyes_chem_loan_issue_rtn = sql_select("select c.item_description, c.unit_of_measure, c.item_code, c.item_group_id, sum(b.cons_quantity) as qty from inv_receive_master a, inv_transaction b, product_details_master c where a.id = b.mst_id and b.prod_id = c.id and a.entry_form = 29 and a.company_id = $data[0] and b.transaction_type = 4 and a.status_active = 1 and a.is_deleted = 0 GROUP by  c.item_description, c.unit_of_measure, c.item_code, c.item_group_id");
        $dyes_chem_loan_issue_rtn = array();
        foreach ( $sql_dyes_chem_loan_issue_rtn as $loanData){
            $dyes_chem_loan_issue_rtn[$loanData[csf('item_description')]."**".$loanData[csf('item_group_id')]."**".$loanData[csf('unit_of_measure')]."**".$loanData[csf('item_code')]] = $loanData['QTY'];
        }

        $sql_general_item_loan_recv = sql_select("select c.item_description, c.unit_of_measure, c.item_code, c.item_group_id, sum(b.cons_quantity) as QTY from inv_receive_master a, inv_transaction b, product_details_master c where a.id = b.mst_id and b.prod_id = c.id and a.entry_form = 20 and a.company_id = $data[0] and a.receive_purpose = 1 and b.transaction_type = 1 and a.status_active = 1 and a.is_deleted = 0 group by  c.item_description, c.unit_of_measure, c.item_code, c.item_group_id");
        $general_item_loan_recv = array();
        foreach ( $sql_general_item_loan_recv as $loanData){
            $general_item_loan_recv[$loanData[csf('item_description')]."**".$loanData[csf('item_group_id')]."**".$loanData[csf('unit_of_measure')]."**".$loanData[csf('item_code')]] = $loanData['QTY'];
        }
        $sql_general_item_loan_recv_rtn = sql_select("select c.item_description, c.unit_of_measure, c.item_code, c.item_group_id, sum(b.cons_quantity) as QTY from inv_issue_master a, inv_transaction b, product_details_master c where a.id = b.mst_id and b.prod_id = c.id and a.entry_form = 26 and a.company_id = $data[0] and b.transaction_type = 3 and a.status_active = 1 and a.is_deleted = 0 GROUP by  c.item_description, c.unit_of_measure, c.item_code, c.item_group_id");
        $general_item_loan_recv_rtn = array();
        foreach ( $sql_general_item_loan_recv_rtn as $loanData){
            $general_item_loan_recv_rtn[$loanData[csf('item_description')]."**".$loanData[csf('item_group_id')]."**".$loanData[csf('unit_of_measure')]."**".$loanData[csf('item_code')]] = $loanData['QTY'];
        }
        $sql_general_item_loan_issue = sql_select("select c.item_description, c.unit_of_measure, c.item_code, c.item_group_id, sum(b.cons_quantity) as qty from inv_issue_master a, inv_transaction b, product_details_master c where a.id = b.mst_id and b.prod_id = c.id and a.entry_form = 21 and a.company_id = $data[0] and b.transaction_type = 2 and a.status_active = 1 and a.is_deleted = 0 and a.issue_purpose = 5 GROUP by  c.item_description, c.unit_of_measure, c.item_code, c.item_group_id");
        $general_item_loan_issue = array();
        foreach ( $sql_general_item_loan_issue as $loanData){
            $general_item_loan_issue[$loanData[csf('item_description')]."**".$loanData[csf('item_group_id')]."**".$loanData[csf('unit_of_measure')]."**".$loanData[csf('item_code')]] = $loanData['QTY'];
        }
        $sql_general_item_loan_issue_rtn = sql_select("select c.item_description, c.unit_of_measure, c.item_code, c.item_group_id, sum(b.cons_quantity) as qty from inv_receive_master a, inv_transaction b, product_details_master c where a.id = b.mst_id and b.prod_id = c.id and a.entry_form = 27 and a.company_id = $data[0] and b.transaction_type = 4 and a.status_active = 1 and a.is_deleted = 0 GROUP by  c.item_description, c.unit_of_measure, c.item_code, c.item_group_id");
        $general_item_loan_issue_rtn = array();
        foreach ( $sql_general_item_loan_issue_rtn as $loanData){
            $general_item_loan_issue_rtn[$loanData[csf('item_description')]."**".$loanData[csf('item_group_id')]."**".$loanData[csf('unit_of_measure')]."**".$loanData[csf('item_code')]] = $loanData['QTY'];
        }

        foreach($sql_result as $row)
		{

			$all_prod_ids.=$row['PRODUCT_ID'].",";
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['id'] = $row['ID'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['item_category'] = $row['ITEM_CATEGORY'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['brand_name'] = $row['BRAND_NAME'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['origin'] = $row['ORIGIN'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['model'] = $row['MODEL'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['requisition_date'] = $row['REQUISITION_DATE'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['product_id'] = $row['PRODUCT_ID'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['required_for'] = $row['REQUIRED_FOR'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['cons_uom'] = $row['CONS_UOM'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['re_order_label'] = $row['RE_ORDER_LABEL'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['quantity'] = $row['QUANTITY'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['rate'] = $row['RATE'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['amount'] = $row['AMOUNT'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['stock'] = $row['STOCK'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['remarks'] = $row['REMARKS'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['item_account'] = $row['ITEM_ACCOUNT'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['item_category_id'] = $row['ITEM_CATEGORY_ID'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['item_description'] = $row['ITEM_DESCRIPTION'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['sub_group_name'] = $row['SUB_GROUP_NAME'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['item_code'] = $row['ITEM_CODE'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['item_number'] = $row['ITEM_NUMBER'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['item_size'] = $row['ITEM_SIZE'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['item_group_id'] = $row['ITEM_GROUP_ID'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['unit_of_measure'] = $row['UNIT_OF_MEASURE'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['current_stock'] = $row['CURRENT_STOCK'];
			$all_data_array[$row['ITEM_CATEGORY']][$row['DTLS_ID']]['re_order_label'] = $row['RE_ORDER_LABEL'];
		}
		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));

		if($all_prod_ids=="") $all_prod_ids=0;
		 $rec_sql="SELECT b.id as ID,b.item_category as ITEM_CATEGORY, b.prod_id as PROD_ID, b.transaction_date as TRANSACTION_DATE,b.supplier_id as SUPPLIER_ID, b.cons_quantity as REC_QTY, b.cons_rate as CONS_RATE 
		 from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis not in(6) order by  b.prod_id,b.id";
		 //echo  $rec_sql;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row['PROD_ID']]['transaction_date']=$row['TRANSACTION_DATE'];
			$receive_array[$row['PROD_ID']]['rec_qty']=$row['REC_QTY'];
			$receive_array[$row['PROD_ID']]['rate']=$row['CONS_RATE'];
			$receive_array[$row['PROD_ID']]['supplier_id']=$row['SUPPLIER_ID'];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("SELECT prod_id as PROD_ID, min(transaction_date) as TRANSACTION_DATE, sum(cons_quantity) as ISSSUE_QTY from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$prev_issue_data[$row["PROD_ID"]]["transaction_date"]=$row["TRANSACTION_DATE"];
			$prev_issue_data[$row["PROD_ID"]]["isssue_qty"]=$row["ISSSUE_QTY"];
		}


		$last_month_issue_sql=sql_select("SELECT prod_id as PROD_ID, sum(cons_quantity) as ISSSUE_QTY from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) group by prod_id");

		$last_month_issue_data=array();
		foreach($last_month_issue_sql as $row)
		{
			$last_month_issue_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_month_issue_data[$row["PROD_ID"]]["isssue_qty"]=$row["ISSSUE_QTY"];
		}

		$last_three_month_issue_sql=sql_select("SELECT prod_id as PROD_ID, sum(cons_quantity) as ISSSUE_QTY from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-3)  group by prod_id");

		$last_three_month_issue_data=array();
		foreach($last_three_month_issue_sql as $row)
		{
			$last_three_month_issue_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_three_month_issue_data[$row["PROD_ID"]]["isssue_qty"]=$row["ISSSUE_QTY"];
		}

		$last_six_month_issue_sql=sql_select("SELECT prod_id as PROD_ID, sum(cons_quantity) as ISSSUE_QTY from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-6)  group by prod_id");

		$last_six_month_issue_data=array();
		foreach($last_six_month_issue_sql as $row)
		{
			$last_six_month_issue_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_six_month_issue_data[$row["PROD_ID"]]["isssue_qty"]=$row["ISSSUE_QTY"];
		}

		$total_issue_data_sql=sql_select("SELECT prod_id as PROD_ID, sum(cons_quantity) as ISSSUE_QTY from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 group by prod_id");

		$total_issue_data=array();
		foreach($total_issue_data_sql as $row)
		{
			$total_issue_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$total_issue_data[$row["PROD_ID"]]["isssue_qty"]=$row["ISSSUE_QTY"];
		}

		//for total issue data
		//var_dump($total_issue_data);

		$total_return_data_sql=sql_select("SELECT prod_id as PROD_ID, sum(cons_quantity) as RETURN_QTY from  inv_transaction where transaction_type=4 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 group by prod_id");
		$total_return_data=array();
		foreach($total_return_data_sql as $row)
		{
			$total_return_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$total_return_data[$row["PROD_ID"]]["return_qty"]=$row["RETURN_QTY"];
		}
		//var_dump($total_return_data);

		$receive_sql=sql_select("SELECT prod_id as PROD_ID, min(transaction_date) as TRANSACTION_DATE, sum(cons_quantity) as RECEIVE_QTY from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$prev_receive_data[$row["PROD_ID"]]["transaction_date"]=$row["TRANSACTION_DATE"];
			$prev_receive_data[$row["PROD_ID"]]["receive_qty"]=$row["RECEIVE_QTY"];
		}

		$receive_last_month_sql=sql_select("SELECT prod_id as PROD_ID, sum(cons_quantity) as RECEIVE_QTY from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) group by prod_id");

		$last_month_receive_data=array();
		foreach($receive_last_month_sql as $row)
		{
			$last_month_receive_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_month_receive_data[$row["PROD_ID"]]["receive_qty"]=$row["RECEIVE_QTY"];
		}

		// for last 3 month received data
		$receive_last_three_month_sql=sql_select("SELECT prod_id as PROD_ID, sum(cons_quantity) as RECEIVE_QTY from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-3) group by prod_id");

		$last_three_month_receive_data=array();
		foreach($receive_last_three_month_sql as $row)
		{
			$last_three_month_receive_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_three_month_receive_data[$row["PROD_ID"]]["receive_qty"]=$row["RECEIVE_QTY"];
		}

		// for last 6 month received data
		$receive_last_six_month_sql=sql_select("SELECT prod_id as PROD_ID, sum(cons_quantity) as RECEIVE_QTY from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-6) group by prod_id");

		$last_six_month_receive_data=array();
		foreach($receive_last_six_month_sql as $row)
		{
			$last_six_month_receive_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_six_month_receive_data[$row["PROD_ID"]]["receive_qty"]=$row["RECEIVE_QTY"];
		}

		$i=1;
		foreach ($all_data_array as $item_id => $item_category_nam) 
		{
			$total_requisition=$total_amount=0;
			?>
				<tr>
					<td colspan="25" align="left"><b>Item Category :- <?=$item_category[$item_id];?></b></td>
				</tr>
			<?
			foreach($item_category_nam as $dtls_id => $row)
			{
				
				$item_cat=$row['item_category'];
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
				$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
				$last_rec_rate=$receive_array[$row['product_id']]['rate'];
				$last_rec_supp=$receive_array[$row['product_id']]['supplier_id'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" >
					<td align="center"><? echo $i; ?></td>
					<td align="center"><p><? echo $item_name_arr[$row['item_group_id']]; ?></p></td>
					<td align="center"><p> <? echo $row["item_description"]?> </p></td>
					<td align="center"><p><? echo $row["brand_name"];?> </p></td>
					<td align="center"><p><? echo $origin_lib[$row["origin"]];?> </p></td>
					<td align="center"><p><? echo $unit_of_measurement[$row["cons_uom"]]; ?></p></td>
					<td align="right"><p><? echo $row["re_order_label"]; ?></p></td>
                    <?
                    if($item_id == 5 || $item_id == 6 || $item_id == 7 || $item_id == 23){
                        $loanRcv = (isset($dyes_chem_loan_recv[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']]) ? $dyes_chem_loan_recv[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']] : 0) - (isset($dyes_chem_loan_recv_rtn[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']]) ? $dyes_chem_loan_recv_rtn[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']] : 0);
                        $loanIssue = (isset($dyes_chem_loan_issue[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']]) ? $dyes_chem_loan_issue[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']] : 0) - (isset($dyes_chem_loan_issue_rtn[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']]) ? $dyes_chem_loan_issue_rtn[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']] : 0);
                        $totalLoan = $loanRcv - $loanIssue;
                    ?>
                        <td align="right"><p><? echo $totalLoan;?></p></td>
                    <?
                    }else{
                        $loanRcv = (isset($general_item_loan_recv[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']]) ? $general_item_loan_recv[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']] : 0) - (isset($general_item_loan_recv_rtn[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']]) ? $general_item_loan_recv_rtn[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']] : 0);
                        $loanIssue = (isset($general_item_loan_issue[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']]) ? $general_item_loan_issue[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']] : 0) - (isset($general_item_loan_issue_rtn[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']]) ? $general_item_loan_issue_rtn[$row["item_description"]."**".$row['item_group_id']."**".$row['unit_of_measure']."**".$row['item_code']] : 0);
                        $totalLoan = $loanRcv - $loanIssue;
                    ?>
                        <td align="right"><p><? echo $totalLoan;?></p></td>
                    <?
                    }
                    ?>
					<td align="right"><p><? echo $row['quantity']; ?></p></td>
					<td align="right"><? echo $row['rate']; ?></td>
					<td align="right"><? echo $row['amount']; ?></td>
					<td align="right"><p><? echo number_format($row['stock'],2); ?></p></td>
					<td align="center" title="<?= $row['product_id'].te;?>"><p><? echo change_date_format($last_rec_date);?></p></td>
					<td align="right"><p><? echo number_format($last_rec_qty,0,'',','); ?></p></td>
					<td align="right"><p><? echo $last_rec_rate; ?></p></td>
					<td align="right"><p>
						<?
							$reqsit_value="";
							$reqsit_value=$last_rec_qty*$last_rec_rate;
							echo number_format($reqsit_value,2);
						?>
						&nbsp;</p>
					</td>
					<td><p><? echo $supplier_array[$last_rec_supp]; ?>&nbsp;</p></td>
					<td align="right">
						<? echo number_format($last_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
					</td>
					<td align="right">
						<? echo number_format($last_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
					</td>
					
					<td align="right">
						<? echo number_format($last_three_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
					</td>
					<td align="right">
						<? echo number_format($last_three_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
					</td>
					<td align="right">
						<? echo number_format($last_six_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
					</td>
					<td align="right">
						<? echo number_format($last_six_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
					</td>
					<td align="right">
						<? 
						$totalissQnty =$total_issue_data[$row["product_id"]]["isssue_qty"];
						$totalreturnQnty =$total_return_data[$row["product_id"]]["return_qty"];
						echo number_format($totalissQnty-$totalreturnQnty,2);
						?>
					</td>
					<td align="center"><? echo $row['remarks']; ?></td>
				</tr>
				<?
				$total_requisition += $row['quantity'];
				$total_amount += $row['amount'];
				$Grand_tot_total_amount += $row['amount'];
				$i++;
			}
			?>
				<tr bgcolor="#dddddd">
					<td>&nbsp;</td>
					<td colspan="7" align="right"><strong>Sub Total : </strong></td>
					<td align="right"><? echo number_format($total_requisition,2); ?></td>
					<td >&nbsp;</td>
					<td align="right"><? echo number_format($total_amount,2); ?></td>
					<td colspan="14" >&nbsp;</td>
				</tr>			
			<?
		}
		?>

			<tr bgcolor="#B0C4DE">
				<td>&nbsp;</td>
				<td colspan="9" align="right"><strong>Total : </strong></td>
				<td align="right"><? echo number_format($Grand_tot_total_amount,2); ?></td>
				<td colspan="14" >&nbsp;</td>
			</tr>
			<tr>
				<td colspan="25">
					<span><strong>Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></strong></span>
				</td>
			</tr>

		</tbody>
		<tfoot>
			<tr>
					<td colspan="25">
						<? echo signature_table(25, $data[0], "1300px",$cbo_template_id,20,$user_lib_name[$inserted_by], '', 9); ?>
					</td>
				</tr>
		</tfoot>
	</table>
	<?
	exit();

}

if($action=="purchase_requisition_print_27") // Print 22
{
	?>
	<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	<?
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_id=$data[0];
	$update_id=$data[1];
	$formate_id=$data[3];
	$cbo_template_id=$data[6];
	$sql="SELECT id, requ_no, req_by, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, division_id, section_id, store_name, pay_mode, cbo_currency, priority_id, remarks, reference, inserted_by, is_approved from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
	$requ_no=explode('-', $dataArray[0][csf("requ_no")]);
	$requisition_no=$requ_no[2].'-'.$requ_no[3];
	$requisition_date=$dataArray[0][csf("requisition_date")];
	$inserted_by=$dataArray[0][csf("inserted_by")];
	$requisition_date_last_year=change_date_format(add_date($requisition_date, -365),'','',1);
			
 	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$store_library=return_library_array( "SELECT id, store_name from  lib_store_location", "id", "store_name"  );
	$department_library=return_library_array("SELECT id,department_name from lib_department",'id','department_name');
	$section_library=return_library_array("SELECT id,section_name from lib_section",'id','section_name');
	$supplier_array=return_library_array( "SELECT id,supplier_name from lib_supplier",'id','supplier_name');
	$company_info=sql_select("SELECT plot_no, city from lib_company where id=$company_id and is_deleted=0");
	?>

  	<style type="text/css">
  		.rpt_table td{
	  		border: 1px solid black;
	  	}
	  	.rpt_table thead th{
	  		border: 1px solid black;
	  	}
  	</style>
  	<style type="text/css">
		.wrd_brk{
			wordwrap: break-word;
			break-ward: break-word;			
		}
		.fontsize{
			font-size: 10px;
		}

	</style>
	<table width="1350" class="rpt_tables" style="margin-left: 2px;">
		<tr>
			<td align="left" rowspan="2" style="width: 100px;">
				<? 
					$data_array=sql_select("SELECT image_location from common_photo_library where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
					foreach($data_array as $img_row)
					{
						?>
						<img src='<?=base_url($img_row['IMAGE_LOCATION']); ?>' height='60' width='80' align="middle" />
						<?
					}
				?>
			</td>
			<td colspan="7" style="font-size:24px" align="center"><strong><?=$company_library[$company_id]; ?></strong></td>
		</tr>
		<tr>
			<td colspan="7" align="center"><strong>Factory Address:&nbsp;
				<?=$company_info[0]['PLOT_NO'].', '.$company_info[0]['CITY']; ?></strong>
			</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="7" align="center" style="font-size: 16px;"><strong><p style="text-decoration: underline; padding: 10px 0px 10px 0px">Material Purchase Requisition</p></strong></td>
		</tr>
	</table>
	<table width="1350" style="margin:2px;">
		<table width="260" style="float: left; border: 2px solid black; margin-right: 52px; margin-left: 2px;" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="115">Reqn. No</td>
				<td width="160"><?=$requisition_no; ?></td>
			</tr>
			<tr>
				<td >Reqn. Date</td>
				<td ><?=change_date_format($dataArray[0]['REQUISITION_DATE']); ?></td>
			</tr>
			<tr>
				<td >Approval Status</td>
				<? if ($dataArray[0]['IS_APPROVED'] == 0) echo "<td style='color: red; font-size: 18px;'>Un-Approved</td>"; else if($dataArray[0]['IS_APPROVED'] == 1) echo "<td style='color: green; font-size: 18px;'>Approved</td>"; else echo "<td style='color: red; font-size: 18px;'>Partial-Approved</td>"; ?>
			</tr>
		</table>
		<table width="310" style="float: left; border: 2px solid black;  margin-right: 52px;" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="140">Reqn. From</td>
				<td ><?=$dataArray[0]['REQ_BY']; ?></td>
			</tr>
			<tr>
				<td >Reference</td>
				<td ><?=$dataArray[0]['REFERENCE']; ?></td>
			</tr>
			<tr>
				<td >Manual Req. No.</td>
				<td ><?=$dataArray[0]['MANUAL_REQ']; ?></td>
			</tr>
		</table>
		<table width="300" style="float: left; border: 2px solid black;  margin-right: 55px;" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="100">Department</td>
				<td width="200"><?=$department_library[$dataArray[0]['DEPARTMENT_ID']]; ?></td>
			</tr>
			<tr>
			   <td >Section</td>
			   <td ><?=$section_library[$dataArray[0]['SECTION_ID']]; ?></td>
			</tr>
			<tr>
			   <td >Del Date</td>
			   <td ><?=change_date_format($dataArray[0]['DELIVERY_DATE']); ?></td>
			</tr>
		</table>
		<table width="300" style="border: 2px solid black;" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="100">Store Name</td>
				<td ><?=$store_library[$dataArray[0]['STORE_NAME']]; ?></td>
			</tr>
			<tr>
				<td >Priority</td>
				<td ><?=$priority_array[$dataArray[0]['PRIORITY_ID']]; ?></td>
			</tr>
			<tr>
				<td valign="top">Remarks</td>
				<td ><?=$dataArray[0]['REMARKS']; ?></td>
			</tr>
		</table>
	</table>	
	<br>
	<div style="width: 1350px;">
	<table cellspacing="0" width="1350" border="1" rules="all" class="rpt_table fontsize" style="border-top: 2px solid black; border-left: 2px solid black; border-right: 2px solid black; border-bottom: 1px solid black; margin-left: 2px; margin-top: 20px;" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="30" rowspan="3">SL</th>
				<th width="80" rowspan="3">Item Category</th>
				<th width="100" rowspan="3">Item Group</th>
				<th width="120" rowspan="3">Item Des.</th>
				<th width="80" rowspan="3">Brand</th>
				<th width="80" rowspan="3">Model</th>
				<th width="40" rowspan="3">UOM</th>
				<th width="50" rowspan="3">Reqn. Qty.</th>
				<th width="40" rowspan="3">Rate</th>
				<th width="50" rowspan="3">Amount</th>
				<th width="60" rowspan="3">Stock</th>
				<th width="50" rowspan="3">Last Rec. Date</th>
				<th width="50" rowspan="3">Last Rec. Qty.</th>
				<th width="40" rowspan="3">Last Rate</th>
				<th width="60" rowspan="3">Last Rec. Value</th>
				<th width="100" rowspan="3" style="border-right: 2px solid black;">Last Supplier</th>
				<th width="300" colspan="6">Consumption</th>
				<th width="50" rowspan="3" style="border-left: 2px solid black;">Total Issued Qty.</th>
				<th rowspan="3" align="center">Remarks</th>
			</tr>
			<tr>
				<th width="100" colspan="2">Last Month</th>
				<th width="100" colspan="2">Last 3 Month</th>
				<th width="100" colspan="2">Last 6 Month</th>
			</tr>
			<tr>
				<th width="50">Rec.</th>
				<th width="50">Issue</th>
				<th width="50">Rec.</th>
				<th width="50">Issue</th>
				<th width="50">Rec.</th>
				<th width="50">Issue</th>
			</tr>
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();

		$i=1;
		$sql= "SELECT a.id,b.id as dtls_id, b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock,b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label,c.item_number from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  order by b.item_category";
	    //echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{

			$all_prod_ids.=$row['PRODUCT_ID'].",";
			$all_data_array[$row['DTLS_ID']]['id'] = $row['ID'];
			$all_data_array[$row['DTLS_ID']]['item_category'] = $row['ITEM_CATEGORY'];
			$all_data_array[$row['DTLS_ID']]['brand_name'] = $row['BRAND_NAME'];
			$all_data_array[$row['DTLS_ID']]['origin'] = $row['ORIGIN'];
			$all_data_array[$row['DTLS_ID']]['model'] = $row['MODEL'];
			$all_data_array[$row['DTLS_ID']]['requisition_date'] = $row['REQUISITION_DATE'];
			$all_data_array[$row['DTLS_ID']]['product_id'] = $row['PRODUCT_ID'];
			$all_data_array[$row['DTLS_ID']]['required_for'] = $row['REQUIRED_FOR'];
			$all_data_array[$row['DTLS_ID']]['cons_uom'] = $row['CONS_UOM'];
			$all_data_array[$row['DTLS_ID']]['quantity'] = $row['QUANTITY'];
			$all_data_array[$row['DTLS_ID']]['rate'] = $row['RATE'];
			$all_data_array[$row['DTLS_ID']]['amount'] = $row['AMOUNT'];
			$all_data_array[$row['DTLS_ID']]['stock'] = $row['STOCK'];
			$all_data_array[$row['DTLS_ID']]['remarks'] = $row['REMARKS'];
			$all_data_array[$row['DTLS_ID']]['item_account'] = $row['ITEM_ACCOUNT'];
			$all_data_array[$row['DTLS_ID']]['item_category_id'] = $row['ITEM_CATEGORY_ID'];
			$all_data_array[$row['DTLS_ID']]['item_description'] = $row['ITEM_DESCRIPTION'];
			$all_data_array[$row['DTLS_ID']]['sub_group_name'] = $row['SUB_GROUP_NAME'];
			$all_data_array[$row['DTLS_ID']]['item_code'] = $row['ITEM_CODE'];
			$all_data_array[$row['DTLS_ID']]['item_number'] = $row['ITEM_NUMBER'];
			$all_data_array[$row['DTLS_ID']]['item_size'] = $row['ITEM_SIZE'];
			$all_data_array[$row['DTLS_ID']]['item_group_id'] = $row['ITEM_GROUP_ID'];
			$all_data_array[$row['DTLS_ID']]['unit_of_measure'] = $row['UNIT_OF_MEASURE'];
			$all_data_array[$row['DTLS_ID']]['current_stock'] = $row['CURRENT_STOCK'];
			$all_data_array[$row['DTLS_ID']]['re_order_label'] = $row['RE_ORDER_LABEL'];
			$row_span_arr[$row['ITEM_CATEGORY_ID']]++;
		}
		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;

		$rec_sql="SELECT b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis not in(6) order by  b.prod_id,b.id";
		// echo  $rec_sql;
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row['PROD_ID']]['transaction_date']=$row['TRANSACTION_DATE'];
			$receive_array[$row['PROD_ID']]['rec_qty']=$row['REC_QTY'];
			$receive_array[$row['PROD_ID']]['rate']=$row['CONS_RATE'];
			$receive_array[$row['PROD_ID']]['supplier_id']=$row['SUPPLIER_ID'];
		}

		if($db_type==2)
		{
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'";
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$prev_issue_data[$row["PROD_ID"]]["transaction_date"]=$row["TRANSACTION_DATE"];
			$prev_issue_data[$row["PROD_ID"]]["isssue_qty"]=$row["ISSSUE_QTY"];
		}

		$last_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) group by prod_id");

		
		$last_month_issue_data=array();
		foreach($last_month_issue_sql as $row)
		{
			$last_month_issue_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_month_issue_data[$row["PROD_ID"]]["isssue_qty"]=$row["ISSSUE_QTY"];
		}

		//for last 3 month issue data
		$last_three_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-3)  group by prod_id");

		$last_three_month_issue_data=array();
		foreach($last_three_month_issue_sql as $row)
		{
			$last_three_month_issue_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_three_month_issue_data[$row["PROD_ID"]]["isssue_qty"]=$row["ISSSUE_QTY"];
		}

		//for last 6 month issue data
		$last_six_month_issue_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-6)  group by prod_id");

		$last_six_month_issue_data=array();
		foreach($last_six_month_issue_sql as $row)
		{
			$last_six_month_issue_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_six_month_issue_data[$row["PROD_ID"]]["isssue_qty"]=$row["ISSSUE_QTY"];
		}

		//for total issue data
		$total_issue_data_sql=sql_select("select prod_id, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 group by prod_id");

		$total_issue_data=array();
		foreach($total_issue_data_sql as $row)
		{
			$total_issue_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$total_issue_data[$row["PROD_ID"]]["isssue_qty"]=$row["ISSSUE_QTY"];
		}

		//for total issue data
		//var_dump($total_issue_data);

		$total_return_data_sql=sql_select("select prod_id, sum(cons_quantity) as return_qty from  inv_transaction where transaction_type=4 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 group by prod_id");
		$total_return_data=array();
		foreach($total_return_data_sql as $row)
		{
			$total_return_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$total_return_data[$row["PROD_ID"]]["return_qty"]=$row["RETURN_QTY"];
		}

		//var_dump($total_return_data);

		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$prev_receive_data[$row["PROD_ID"]]["transaction_date"]=$row["TRANSACTION_DATE"];
			$prev_receive_data[$row["PROD_ID"]]["receive_qty"]=$row["RECEIVE_QTY"];
		}
		
		$receive_last_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-1) group by prod_id");

		$last_month_receive_data=array();
		foreach($receive_last_month_sql as $row)
		{
			$last_month_receive_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_month_receive_data[$row["PROD_ID"]]["receive_qty"]=$row["RECEIVE_QTY"];
		}

		// for last 3 month received data
		$receive_last_three_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-3) group by prod_id");

		$last_three_month_receive_data=array();
		foreach($receive_last_three_month_sql as $row)
		{
			$last_three_month_receive_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_three_month_receive_data[$row["PROD_ID"]]["receive_qty"]=$row["RECEIVE_QTY"];
		}

		// for last 6 month received data
		$receive_last_six_month_sql=sql_select("select prod_id, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and prod_id in($all_prod_ids) and is_deleted=0 and status_active=1 and transaction_date >= add_months(trunc(sysdate,'mm'),-6) group by prod_id");

		$last_six_month_receive_data=array();
		foreach($receive_last_six_month_sql as $row)
		{
			$last_six_month_receive_data[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
			$last_six_month_receive_data[$row["PROD_ID"]]["receive_qty"]=$row["RECEIVE_QTY"];
		}
		// echo "<pre>";
		// print_r($all_data_array);

		$Grand_tot_total_amount=0;
		$i=1;
		foreach ($all_data_array as $dtls_id => $row) 
		{
			$item_cat=$row['item_category'];
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";				
					
			$quantity=$row['quantity'];
			$quantity_sum += $quantity;
			$amount=$row['amount'];
			//test
			$sub_group_name=$row['sub_group_name'];
			$amount_sum += $amount;

			$current_stock=$row['stock'];
			$current_stock_sum += $current_stock;
			if($db_type==2)
			{
				$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row['requisition_date'],'','',1)."' order by requisition_date desc", "data" );
			}
			if($db_type==0)
			{
				$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row['product_id']."' and  requisition_date<'".$row['requisition_date']."' order by requisition_date desc", "data" );
			}
			$last_req_info=explode('_',$last_req_info);
			//print_r($dataaa);

			$item_account=explode('-',$row['item_account']);
			$item_code=$item_account[3];
			$last_rec_date=$receive_array[$row['product_id']]['transaction_date'];
			$last_rec_qty=$receive_array[$row['product_id']]['rec_qty'];
			$last_rec_rate=$receive_array[$row['product_id']]['rate'];
			$last_rec_supp=$receive_array[$row['product_id']]['supplier_id'];
					
			$prod_id=$row["product_id"];
			if($db_type==0)
			{
				$last_band_model=sql_select(" SELECT b.product_id as prod_id, b.brand_name as brand_name, b.origin as origin, b.model as model from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id  and b.product_id in($prod_id) and a.id!=$update_id order by b.id desc limit 1 ");
			}
			else
			{
				$last_band_model=sql_select("SELECT brand_name, origin, model, prod_id from (SELECT rownum ,rs.* from (
                       SELECT b.product_id as prod_id, b.brand_name as brand_name, b.origin as origin, b.model as model
                        from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and b.product_id in($prod_id) and a.id!=$update_id order by b.id desc
                   ) rs 

                ) where rownum <= 1
                 
	     		");
			}

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" >
				<td  width="30" align="center"><? echo $i; ?></td>
				<? if($check_category[$row["item_category"]]==''){  ?>
				<td  width="80" align="center" class="wrd_brk" rowspan="<? echo $row_span_arr[$row['item_category']]; ?>"><? echo $item_category[$row["item_category"]]; ?></td>
				<? $check_category[$row["item_category"]]=$row["item_category"];} ?>
				<td  width="100" align="center" class="wrd_brk"><? echo $item_name_arr[$row['item_group_id']]; ?></td>
				<td  width="120" align="center" class="wrd_brk"><? if ($row["item_size"] != "") echo $row["item_description"].', '.$row["item_size"]; else echo $row["item_description"]; ?></td>
				<td  width="80" align="center" class="wrd_brk"><?  echo $row["brand_name"]; ?></td>
				<td  width="80" align="center" class="wrd_brk"><?  echo $row["model"]; ?></td>
				<td width="40" align="center" class="wrd_brk"><? echo $unit_of_measurement[$row["cons_uom"]]; ?></td>
				<td width="50" align="right" class="wrd_brk"><? echo $row['quantity']; ?></td>
				<td width="40" align="right" class="wrd_brk"><? echo number_format($row['rate'],2); ?></td>
				<td width="50" align="right" class="wrd_brk"><? echo number_format($row['amount'],2); ?></td>
				<td width="60" align="right" class="wrd_brk"><? echo number_format($row['stock'],2); ?></td>
				<td width="50" align="center" class="wrd_brk" title="<?= $row['product_id'];?>"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></td>
				<td width="50" align="right" class="wrd_brk"><? echo number_format($last_rec_qty,0,'',','); ?></td>
				<td width="40" align="right" class="wrd_brk"><? echo number_format($last_rec_rate,2); ?></td>
				<td width="60" align="right" class="wrd_brk">
					<?
					$reqsit_value="";
					$reqsit_value=$last_rec_qty*$last_rec_rate;
					echo number_format($reqsit_value,2);
					?>					
				</td>					
				<td width="100" align="center" class="wrd_brk"  style="border-right: 2px solid black;"><? echo $supplier_array[$last_rec_supp]; ?></td>
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
				</td>
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
				</td>				
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_three_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
				</td>
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_three_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
				</td>
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_six_month_receive_data[$row["product_id"]]["receive_qty"],2); ?>
				</td>
				<td width="50" align="right" class="wrd_brk">
					<? echo number_format($last_six_month_issue_data[$row["product_id"]]["isssue_qty"],2); ?>
				</td>
				<td width="50" align="center" class="wrd_brk" style="border-left: 2px solid black;"> 					
					<? 
					$totalissQnty =$total_issue_data[$row["product_id"]]["isssue_qty"];
					$totalreturnQnty =$total_return_data[$row["product_id"]]["return_qty"];
					echo number_format($totalissQnty-$totalreturnQnty,2);
					?>
				 </td>
				<td align="right" class="wrd_brk"><? echo $row['remarks']; ?></td>
			</tr>
			<?
			$Grand_tot_total_amount += $row['amount'];
			$i++;
		}
		?>
		</tbody>
		<tr bgcolor="#B0C4DE" style="border-bottom: 2px solid black;">				
			<td width="30">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td width="100">&nbsp;</td>
			<td width="120">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td width="40">&nbsp;</td>
			<td width="50">&nbsp;</td>
			<td width="40" align="right" class="wrd_brk"><strong>Total :&nbsp;</strong></td>
			<td width="50" align="right" class="wrd_brk"><? echo number_format($Grand_tot_total_amount,2); ?></td>
			<td width="60" align="right">&nbsp;</td>
			<td width="50" align="right">&nbsp;</td>
			<td width="50" align="right">&nbsp;</td>
			<td width="46" align="right">&nbsp;</td>
			<td width="60" align="right">&nbsp;</td>
			<td width="100" style="border-right: 2px solid black;">&nbsp;</td>
			<td width="50" align="right">&nbsp;</td>			
			<td width="50" align="right">&nbsp;</td>
			<td width="50" align="right">&nbsp;</td>			
			<td width="50" align="right">&nbsp;</td>			
			<td width="50" align="right">&nbsp;</td>			
			<td width="50" align="right">&nbsp;</td>
			<td width="50" style="border-left: 2px solid black;">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>	

	</div>
	<span style="margin-left: 2px;"><strong>Total Amount (In Word): &nbsp;<? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0]['CBO_CURRENCY']]." only"; ?></strong></span>
	<br>		
	<?	
	$signature_arr=return_library_array( "SELECT MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature' ",'MASTER_TBLE_ID','IMAGE_LOCATION');
	$appSql="SELECT APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM=1 and MST_ID = $update_id ";
	// echo $appSql;
	$appSqlRes=sql_select($appSql);
	foreach($appSqlRes as $row){
		$userSignatureArr[$row['APPROVED_BY']]=base_url($signature_arr[$row['APPROVED_BY']]);
	}
	if($signature_arr[$inserted_by]){ $userSignatureArr[$inserted_by]=base_url($signature_arr[$inserted_by]); }

	echo signature_table(25, $company_id, "1200px",$cbo_template_id,50,$inserted_by,$userSignatureArr);
	exit();
}
?>
