<?
session_start();
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }


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

$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
//------------------------------------------------------------------------------------------------------

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/sewing_input_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/sewing_input_controller', this.value+'_'+$('#cbo_sewing_company').val()+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value, 'load_drop_down_sewing_line', 'sewing_line_td' );",0 );
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 150, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "get_php_form_data(document.getElementById('cbo_source').value,'line_disable_enable','requires/sewing_input_controller'); load_drop_down( 'requires/sewing_input_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value+'_'+document.getElementById('cbo_sewing_company').value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );",0 );
}

if($action=="production_process_control")
{
	$dataEx = explode("**", $data);
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
	
	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=4 and company_name=$dataEx[0]");	
    
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }
	exit();
}

if ($action=="load_variable_settings")
{
	echo "setFieldLevelAccess($data);\n";
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select sewing_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("sewing_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}

	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$data and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo!=1) $prod_reso_allo=0;
	echo "document.getElementById('prod_reso_allo').value=".$prod_reso_allo.";\n";
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=50 and page_category_id=4","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";
 	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "txt_search_common", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");     	 
	exit();
}

if($action=="plan_data_action")
{
	$data=explode("**", $data);
	$date_cond="";
	if($db_type==0)
	{
		$date_cond = " and b.plan_date='". date("Y-m-d", strtotime($data[1]))."'";
	}
	else
	{
		$date_cond = " and b.plan_date='". date("d-M-Y", strtotime($data[1]))."'" ;
	}
	$line_arr=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name" );
	 $sql="SELECT a.line_id,sum(b.plan_qnty) as qnty from ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls b where a.plan_id=b.plan_id and a.po_break_down_id='$data[0]' and a.status_active=1 and a.is_deleted=0   $date_cond  group by  a.line_id ";
	 $result= sql_select($sql);
	 if(count($result)>0)
	 {
	 	?>
	 	<table border="2" class="rpt_table" rules="all" >
	 		<tr>
	 			<td align="center" width="95"><strong>Line</strong></td>
	 			<td align="center" width="35"> <strong>Plan</strong></td>
	 		</tr>
	 		<?
	 		foreach($result as $val)
	 		{
	 			?>
	 			<tr>
	 				<td width="95"  align="center"><b><? echo $line_arr[$val[csf("line_id")]]; ?></b></td>
	 				<td width="35" align="center"> <b><? echo $val[csf("qnty")];?></b></td>
	 			</tr>	
	 			<?
	 		}
	 		?>
	 	</table>
	 	<?
	 }
	exit();
}

if($action=="load_drop_down_sewing_input")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		echo create_drop_down( "cbo_sewing_company", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "" );
	}
 	else if($data==1)
  		echo create_drop_down( "cbo_sewing_company", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' ); fnc_company_check(document.getElementById('cbo_source').value);",0,0 );
 	else
 		echo create_drop_down( "cbo_sewing_company", 150, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
 	exit();
}

if($action=="line_disable_enable")
{
	if($data==1)
		echo "disable_enable_fields('cbo_sewing_line',0,'','');\n";
	else
	{
		echo "$('#cbo_sewing_line').val(0);\n";
		echo "disable_enable_fields('cbo_sewing_line',1,'','');\n";
	}
}

if($action=="load_drop_down_sewing_line")
{
	$explode_data = explode("_",$data);
	$location = $explode_data[0];
	$company = $explode_data[1];
	$prod_reso_allocation = $explode_data[2];
	$txt_sewing_date = $explode_data[3];

	//if($location==0 || $location=="") $location="";
	//if($company==0 || $company=="") $company="";
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_sewing_date=="")
		{
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 and company_id='$company' and location_id='$location'");
		}
		else
		{
			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.company_id='$company' and a.location_id='$location' and a.is_deleted=0 and b.is_deleted=0 group by a.id");
			}
			if($db_type==2 || $db_type==1)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");

			}
		}
		$line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$line_library[$val]]=$row[csf('id')];
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		//print_r($new_arr);
		sort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and company_name like '%$company%' and location_name like '%$location%' and location_name!=0 order by sewing_line_serial","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
	}
}

if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);
	$prod_reso_allocation = $explode_data[2];
	$txt_sewing_date = $explode_data[3];
	$cond="";
	$wo_company_id = $explode_data[4];	
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$wo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_sewing_date=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";

			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";

			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id  order by a.prod_resource_num");
			}
			else if($db_type==2 || $db_type==1)
			{
				// echo "select a.id, a.line_number,a.prod_resource_num from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id,a.prod_resource_num, a.line_number  order by a.prod_resource_num";die;


				$line_data=sql_select("select a.id, a.line_number,a.prod_resource_num from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id,a.prod_resource_num, a.line_number  order by a.prod_resource_num");
			}
		}
		$line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$line_library[$val]]=$row[csf('id')];
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
 		ksort($new_arr);

		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}

		//print_r($line_array_new);die;

		echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
	}
}


if ($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>

	<script>

		$(document).ready(function(e) {
            $("#txt_search_common").focus();
						$("#company_search_by").val(<?php echo $_REQUEST['company'] ?>);
        });

		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input 	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value="" onKeyDown="getActionOnEnter(event)"  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value="" onKeyDown="getActionOnEnter(event)" />';
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==4)
			{
				document.getElementById('search_by_th_up').innerHTML="Actual PO No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==5)
			{
				document.getElementById('search_by_th_up').innerHTML="File No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==6)
			{
				document.getElementById('search_by_th_up').innerHTML="Internal Ref";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				load_drop_down( 'sewing_input_controller',document.getElementById('company_search_by').value,'load_drop_down_buyer', 'search_by_td' );
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
			}

		}

	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id);
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_plancut_qnty").val(plan_qnty);
		$("#hidden_country_id").val(country_id);
		$("#hidden_company_id").val(document.getElementById('company_search_by').value);
   		parent.emailwindow.hide();
 	}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
           <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
                <tr>
                    <td align="center" width="100%">
                        <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                             <thead>
							    <tr>
									<th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,1, "-- Select --", $selected, "",0); ?></th>
								</tr>
                                <th width="130" class="must_entry_caption">Company</th>
                                <th width="130">Search By</th>
                                <th width="130" align="center" id="search_by_th_up">Enter Order Number</th>
                                <th width="200">Date Range</th>
                                <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                            </thead>
                            <tr class="general">
                            	<td><? echo create_drop_down( "company_search_by", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "",0 ); ?></td>
                                <td>
                                    <?
                                        $searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref");
                                        echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select --", $selected, "search_populate(this.value)",0 );
                                    ?>
                                </td>
                                <td id="search_by_td"><input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" /></td>
                                <td>
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                                </td>
                                <td><input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'sewing_input_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="middle">
					<?=load_month_buttons(1);  ?>
                        <input type="hidden" id="hidden_mst_id">
                        <input type="hidden" id="hidden_grmtItem_id">
                        <input type="hidden" id="hidden_po_qnty">
                        <input type="hidden" id="hidden_plancut_qnty">
                        <input type="hidden" id="hidden_country_id">
                        <input type="hidden" id="hidden_company_id">
                    </td>
                </tr>
            </table>
            <div style="margin-top:2px" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$garments_nature = $ex_data[5];
 	$year = $ex_data[6];
	$search_type =$ex_data[7];
	if($company == 0)
	{
		//print_r ($data);die;
		echo "Please Select Company First."; die;
	}
 	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name=$company and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];

	$sql_cond="";
	if ($search_type==4){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and a.job_no like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and b.po_number_acc like '%".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and b.file_no like '%".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";
		}
	}
	else if ($search_type==1){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and b.po_number ='$txt_search_common'";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and a.style_ref_no ='$txt_search_common'";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and a.job_no='$txt_search_common'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and b.po_number_acc='$txt_search_common'";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and b.file_no='$txt_search_common'";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and b.grouping='$txt_search_common'";
		}
	}
	else if ($search_type==2){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and b.po_number like '".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and a.style_ref_no like '".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and a.job_no like '".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and b.po_number_acc like '".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and b.file_no like '".trim($txt_search_common)."%'";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and b.grouping like '".trim($txt_search_common)."%'";
		}
	}
	else if ($search_type==3){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and b.po_number like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and a.job_no like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and b.po_number_acc like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and b.file_no like '%".trim($txt_search_common)."'";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and b.grouping like '%".trim($txt_search_common)."'";
		}
	}

	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
	if($year !=0)
	{
		if($db_type==0) { $sql_shipment_year_cond=" and YEAR(a.insert_date)=$year";   }
		if($db_type==2) {$sql_shipment_year_cond=" and to_char(a.insert_date,'YYYY')=$year";}
	}
	
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$company");
    $projected_po_cond = ($is_projected_po_allow==2) ? " and b.is_confirmed=1" : "";
	
	$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_number_acc, b.po_quantity, b.plan_cut, b.grouping, b.file_no,a.insert_date
			from wo_po_details_master a, wo_po_break_down_vw b 
			where
			a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond $sql_shipment_year_cond $projected_po_cond  order by b.shipment_date desc"; 
			// echo	$sql;die;

	/*if(trim($txt_search_by)==4 && trim($txt_search_common)!="")
	{
		$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.grouping,b.file_no,b.po_quantity,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
			where
			a.job_no = b.job_no_mst and
			b.id=c.po_break_down_id and
			a.status_active=1 and
			a.is_deleted=0 and
			b.status_active=1 and
			b.is_deleted=0 and
			c.status_active=1 and
			c.is_deleted=0 and
			a.garments_nature=$garments_nature
			$sql_cond group by b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.grouping,b.file_no,b.po_quantity,b.plan_cut";
	}
	else
	{
 		$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.grouping,b.file_no,b.po_quantity ,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b
			where
			a.job_no = b.job_no_mst and
			a.status_active=1 and
			a.is_deleted=0 and
			b.status_active=1 and
			b.is_deleted=0
			$sql_cond";
	}*/
	// echo $sql;die;
	$result = sql_select($sql);
	$all_po_arr=array();
	foreach($result as $v)
	{
		$all_po_arr[$v[csf("id")]]=$v[csf("id")];
	}

	$all_po_arr_ids=implode(",", $all_po_arr);
    if(!$all_po_arr_ids)$all_po_arr_ids=0;
	$all_po_conds="";
	if(count($all_po_arr)>5 && $db_type==2)
	{
		$chnk=array_chunk($all_po_arr, 5)   ;
		foreach($chnk as $v)
		{
			$ids=implode(",", $v);
			if($all_po_conds=="")$all_po_conds.=" and ( po_break_down_id in($ids) ";
			else $all_po_conds.=" or  po_break_down_id in($ids) ";
		} $all_po_conds.=")";
	}
	else $all_po_conds="  and po_break_down_id in($all_po_arr_ids)";	//echo $all_po_conds;die;
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$po_country_sql=sql_select("SELECT po_break_down_id, country_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $all_po_conds group by po_break_down_id,country_id");
	foreach ($po_country_sql as $key => $value)
	{
		if($po_country_arr[$value[csf("po_break_down_id")]]=="") $po_country_arr[$value[csf("po_break_down_id")]].=$value[csf("country_id")];
		else $po_country_arr[$value[csf("po_break_down_id")]].=','.$value[csf("country_id")];
	}
	//print_r($po_country_arr);

	$po_country_data_arr=array();
	$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $all_po_conds group by po_break_down_id, item_number_id, country_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	}
	if($sewing_level==1)
	{
		$qty=" sum(a.production_quantity) as production_quantity ";
		$status_active_cond=" ";
	}
	else
	{
		$qty= " sum(b.production_qnty) as production_quantity ";
		$status_active_cond =" and b.production_type='4' and b.status_active=1 and b.is_deleted=0  ";
	}

	$total_input_data_arr=array();
	/*$total_input_qty_arr=sql_select( "SELECT a.po_break_down_id, a.item_number_id, a.country_id, $qty from pro_garments_production_mst a left join pro_garments_production_dtls b on a.id=b.mst_id  WHERE   a.status_active=1 and a.is_deleted=0 and a.production_type=4 $status_active_cond   GROUP BY  a.po_break_down_id, a.item_number_id, a.country_id");*/
	
	$total_input_qty_arr=sql_select( "SELECT a.po_break_down_id, a.item_number_id, a.country_id, $qty 
	from pro_garments_production_mst a,pro_garments_production_dtls b
	WHERE   a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.production_type=4 $all_po_conds $status_active_cond   
	GROUP BY  a.po_break_down_id, a.item_number_id, a.country_id"); 

	

	foreach($total_input_qty_arr as $row)
	{
		$total_input_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}
	?>

     <div style="width:1130px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Acc.Order No</th>
                <th width="100">Buyer</th>
                <th width="120">Style</th>
                <th width="70">File No</th>
                <th width="70">Ref. No</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="70">Order Qty</th>
                <th width="70">Total Linking Qty</th>
                <th>Balance</th>
            </thead>
     	</table>
     </div>
     <div style="width:1130px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1110" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));

				$numOfCountry = count($country);

				for($k=0;$k<$numOfItem;$k++)
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach($country as $country_id)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];

						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>');" >
								<td width="30" align="center"><?php echo $i; ?></td>
								<td width="60" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>
								<td width="100" style="word-break:break-all"><?php echo $row[csf("po_number")]; ?></td>
                                <td width="100" style="word-break:break-all"><?php echo $row[csf("po_number_acc")]; ?></td> 
								<td width="100" style="word-break:break-all"><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></td>
								<td width="120" style="word-break:break-all"><?php echo $row[csf("style_ref_no")]; ?></td>
                                <td width="70" style="word-break:break-all"><?php echo $row[csf("file_no")]; ?></td>
                                <td width="70" style="word-break:break-all"><?php echo $row[csf("grouping")]; ?></td>
								<td width="140" style="word-break:break-all"><?php  echo $garments_item[$grmts_item];?></td>
								<td width="100" style="word-break:break-all"><?php echo $country_library[$country_id]; ?>&nbsp;</td>
								<td width="70" align="right"><?php echo $po_qnty; ?>&nbsp;</td>
                                <td width="70" align="right"><?php echo $total_cut_qty=$total_input_data_arr[$row[csf('id')]][$grmts_item][$country_id]; ?>&nbsp;</td>
                                <td align="right"><?php $balance=$po_qnty-$total_cut_qty; echo $balance; ?>&nbsp;</td>
							</tr>
						<?
						$i++;
					}
				}
            }
   		?>
        </table>
    </div>
	<?
	exit();
}

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$preceding_process = $dataArr[3];
	$qty_source=0;
	if($preceding_process==1) $qty_source=1; //Kniting Complete
	else if($preceding_process==100) $qty_source=100;//1st inspection

	$company_id_sql=sql_select("SELECT a.company_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id group by a.company_name");
	$company_id=$company_id_sql[0][csf("company_name")];
	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];


	$mst_table=($preceding_process==123)? "pro_cut_delivery_order_dtls" : "pro_garments_production_mst";
	$dtls_table=($preceding_process==123)? "pro_cut_delivery_color_dtls" : "pro_garments_production_dtls";

	$res = sql_select("select a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name
			from wo_po_break_down a, wo_po_details_master b
			where a.job_no_mst=b.job_no and a.id=$po_id");

 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
 		if($qty_source!=0)
 		{
 			echo "$('#dynamic_msg').html('Total Cut Quantity');\n";
   			$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=$qty_source and b.production_type=$qty_source THEN b.production_qnty END) as totalreceive,SUM(CASE WHEN a.production_type=4 and b.production_type=4  THEN b.production_qnty ELSE 0 END) as totalinput from $mst_table a,$dtls_table b WHERE a.id=b.mst_id and  a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

   			if($qty_source=="9")
   			{
    			$pro_cut_sql=sql_select("SELECT SUM(CASE WHEN a.production_type=4 and b.production_type=4  THEN b.production_qnty ELSE 0 END) as totalinput from pro_garments_production_mst  a,pro_garments_production_dtls b WHERE a.id=b.mst_id and  a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
   			}
   			if($sewing_level==1)
			{
				$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=$qty_source   THEN a.production_quantity END) as totalreceive,SUM(CASE WHEN a.production_type=4   THEN a.production_quantity ELSE 0 END) as totalinput from pro_garments_production_mst a WHERE  a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 ");
			}

	 		foreach($dataArray as $row)
			{
	 			echo "$('#txt_receive_qnty').val('".$row[csf('totalreceive')]."');\n";
				echo "$('#txt_cumul_input_qty').val('".$row[csf('totalinput')]."');\n";
				$yet_to_produced = $row[csf('totalreceive')]-$row[csf('totalinput')];
				if($qty_source=="9")
				{
					echo "$('#txt_cumul_input_qty').val('".$pro_cut_sql[0][csf('totalinput')]."');\n";
					$yet_to_produced = $row[csf('totalreceive')]-$pro_cut_sql[0][csf('totalinput')];
				}
				echo "$('#txt_yet_to_input').attr('placeholder','".$yet_to_produced."');\n";
				echo "$('#txt_yet_to_input').val('".$yet_to_produced."');\n";
			}

 		}

		if($qty_source==0)
		{
			echo "$('#dynamic_msg').html('Total Plan Cut Qnty');\n";
			$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0");

			$total_produced = return_field_value("sum(b.production_qnty)","pro_garments_production_mst a,pro_garments_production_dtls b","a.id = b.mst_id and a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type=4 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_type=4 and a.status_active=1");
			if($sewing_level==1)
			{
				$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst "," po_break_down_id=".$result[csf('id')]." and  item_number_id='$item_id' and  country_id='$country_id' and  production_type=4 and  is_deleted=0   and  status_active=1");
			}
			echo "$('#txt_receive_qnty').val('".$plan_cut_qnty."');\n";
 			echo "$('#txt_cumul_input_qty').val('".$total_produced."');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
 			echo "$('#txt_yet_to_input').val('".$yet_to_produced."');\n";
		}
  	}
 	exit();
}

if($action=="wo_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0)
				document.getElementById('chk_job_wo_po').value=1;
			else
				document.getElementById('chk_job_wo_po').value=0;
		}
		function js_set_value(val)
		{
			$("#hidden_sys_data").val(val);
			//$("#hidden_id").val(id);
			parent.emailwindow.hide();
		}
</script>
</head>
<body>
<div style="width:850px;" align="center" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="6">
						<? echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
					</th>
					<th colspan="2" style="text-align:right"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Job</th>
				</tr>
				<tr>
					<th width="120">Buyer Name</th>
					<th width="130">Supplier Name</th>
					<th width="100">WO No</th>
					<th width="100">Job No</th>
                    <th width="100">Style Ref.</th>
					<th width="130" colspan="2"> WO Date Range</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
				</tr>
			</thead>
			<tbody>
				<tr class="general">
				<td><?=create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0); ?></td>
				<td><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $service_company_id, "",0 ); 
				//echo "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.tag_company=$company_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
				
				?></td>
                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:90px"></td>
                
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"/></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /> </td>
                <td>
                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_style_ref').value+'_'+'<? echo $txt_job_no; ?>', 'create_wo_search_list_view', 'search_div', 'sewing_input_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="8">
                    <?=load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_sys_data" value="hidden_sys_data" />
                </td>
            </tr>
        </tbody>
    </table>
    <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_wo_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val=$ex_data[4];
	$search_category=$ex_data[5];
	$booking_prifix=$ex_data[6];
	$job_prifix=$ex_data[7];
	$year_selection=$ex_data[8];
	$chk_job_wo_po=trim($ex_data[9]);
	$style_ref=$ex_data[10];
	$jobno=$ex_data[11];
		
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";
	
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[8]";
	$year_cond=" and to_char(d.insert_date,'YYYY')=$ex_data[8]";
	if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";

	if($search_category==0 || $search_category==4)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '%$job_prifix%' $year_cond "; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$booking_prifix%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($search_category==1)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num ='$job_prifix' "; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num ='$booking_prifix'   "; else $booking_cond="";
	}
	else if($search_category==2)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '$job_prifix%'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '$booking_prifix%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($search_category==3)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '%$job_prifix'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$booking_prifix'  $booking_year_cond  "; else $booking_cond="";
	}

	if($db_type==0) $select_year="year(a.insert_date) as year"; else $select_year="to_char(a.insert_date,'YYYY') as year";
	if($chk_job_wo_po==1)
	{
		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number
		from subcon_wo_mst a
		where a.status_active=1 and a.is_deleted=0 and a.entry_form=643 and a.id not in(select mst_id from subcon_wo_dtls where job_no_id>0 and entry_form=643 and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
	}
	else
	{
		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.service_sweater, TO_CHAR(a.insert_date,'YYYY') as year, d.buyer_name, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(d.style_ref_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.style_ref_no) as style_ref_no from subcon_wo_mst a, subcon_wo_dtls b, wo_po_details_master d where a.id=b.mst_id and b.job_no = d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.pay_mode in (1,2,4) and a.entry_form=643 and b.entry_form=643 and d.job_no='$jobno' $company $supplier $sql_cond $buyer_cond $job_cond $booking_cond $job_ids_cond group by a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.service_sweater, a.insert_date, d.buyer_name order by a.id DESC";
	}
	//echo $sql;
	?>
	<div style="width:850px;" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="100">WO no</th>
                <th width="50">WO Year</th>
                <th width="70">WO Date</th>
                <th width="140">Service Company</th>
                <th width="140">Buyer Name</th>
				<th width="100">Job No</th>
                <th width="120">Style Ref.</th>
				<th >Closing Date</th>
			</thead>
		</table>
		<div style="width:850px; overflow-y:scroll; max-height:270px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search" >
				<?
				$supplier_arr=return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
				$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
				$i=1;
				$nameArray=sql_select( $sql );
				$linkingWoArr=array();
				foreach($nameArray as $row)
				{
					$typeofservice=explode(",",$row[csf("service_sweater")]);
					if (in_array(4, $typeofservice)) {
						$linkingWoArr[$row[csf('id')]]=$row[csf('SUCON_WO_NO')];
					}
				}
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult)
				{
					if($linkingWoArr[$selectResult[csf('id')]]!="")
					{
						$job_no=implode(",",array_unique(explode(",",$selectResult[csf("job_no")])));
						$style_ref_no=implode(",",array_unique(explode(",",$selectResult[csf("style_ref_no")])));
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$supplier=$supplier_arr[$selectResult[csf('supplier_id')]];
						
						$ref_no=implode(",",array_unique(explode(",",chop($po_ref_arr[$selectResult[csf("id")]],","))));
						?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value('<?=$selectResult[csf('id')].'_'.$selectResult[csf('SUCON_WO_NO')]; ?>'); ">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('SUCON_WO_NO')]; ?></td>
							<td width="50" align="center"><?=$selectResult[csf('year')]; ?></td>
							<td width="70"><?=change_date_format($selectResult[csf('booking_date')]); ?></td>
							<td width="140" style="word-break:break-all"><?=$supplier; ?></td>
							<td width="140" style="word-break:break-all"><?=$buyer_arr[$selectResult[csf('buyer_name')]]; ?></td>
							<td width="100" style="word-break:break-all"><?=$job_no; ?></td>
							<td width="120" style="word-break:break-all"><?=$style_ref_no; ?></td>
							<td><?=change_date_format($selectResult[csf('CLOSING_DATE')]); ?></td>
						</tr>
							<?
						$i++;
					}
				}
				?>
			</table>
		</div>
	</div>
		<?
	exit();
}

if ($action=="all_system_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sqls="SELECT  id FROM pro_garments_production_mst Where po_break_down_id = '$po_id' AND production_type = '4'AND status_active = 1 ORDER BY ID asc ";
	$k=1;
	?>
	<table width="310" style="margin: 0px auto;font-weight: bold;" cellspacing="0" cellpadding="0" class="rpt_table" align="left" border="1" rules="all">
		<thead>
			<tr>
				<th width="80">SL</th>
				<th width="230">Sys.Challan No</th>
			</tr>
		</thead>
	</table>
	<div style="width:330px; max-height:200px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="310" class="rpt_table" id="tbl_search_list2">
            <tbody>
            <?
            foreach(sql_select($sqls) as $v)
            {
                ?>
                <tr>
                    <td width="80" align="center"><? echo $k++;?></td>
                    <td width="230" align="center"><? echo $v[csf("id")];?></td>
                </tr>
                <?
            }
            ?>
            </tbody>
		</table>
	</div>
	<script type="text/javascript">setFilterGrid("tbl_search_list2",-1);</script>
	<?
	exit();
}

if($action=="color_and_size_level")
{
		$dataArr = explode("**",$data);
		$po_id = $dataArr[0];
		$item_id = $dataArr[1];
		$variableSettings = $dataArr[2];
		$styleOrOrderWisw = $dataArr[3];
		$country_id = $dataArr[4];
		$garments_nature = $dataArr[6];
		$qty_source=0;
		$preceding_process=$dataArr[5];
		
		if($preceding_process!="") $qty_source=$preceding_process;
		
		// echo "10**".$qty_source;die();
		$mst_table=($preceding_process==123)? "pro_cut_delivery_order_dtls" : "pro_garments_production_mst";
		$dtls_table=($preceding_process==123)? "pro_cut_delivery_color_dtls" : "pro_garments_production_dtls";

		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		if($qty_source!=0)
		{
			if($garments_nature==100) // for sweater
			{
				if( $variableSettings==2 ) // color level
				{
						if($db_type==0)
						{
							$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from $dtls_table pdtls where pdtls.production_type='$qty_source' and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from $dtls_table cur where cur.production_type=4 and cur.is_deleted=0 ) as cur_production_qnty
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
						}
						else
						{
							$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
									sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN c.production_type=4 then b.production_qnty ELSE 0 END) as cur_production_qnty
									from wo_po_color_size_breakdown a
									left join $dtls_table b on a.id=b.color_size_break_down_id and b.is_deleted=0 and b.status_active=1
									left join $mst_table c on c.id=b.mst_id and c.is_deleted=0 and c.status_active=1
									where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

						}

				}
				else if( $variableSettings==3 ) //color and size level
				{

					$dtlsData = sql_select("SELECT a.color_size_break_down_id,
												sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
												sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
												from $dtls_table a,$mst_table b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,4) group by a.color_size_break_down_id");


					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					}

					if($qty_source=="9")
					{
						// for difference of current table and cut delivery table
						$dtlsData = sql_select("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4) group by a.color_size_break_down_id");
						foreach($dtlsData as $row)
						{
	 						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
						}


					}


					$sql = "SELECT id, size_order,item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";


				}
				else // by default color and size level
				{
					$dtlsData = sql_select("SELECT a.color_size_break_down_id,
												sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
												sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
												from $dtls_table a,$mst_table b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,4) group by a.color_size_break_down_id");



					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					}

					$sql = "SELECT id,size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";
				}
			}
			else // for knit and woven
			{
				if( $variableSettings==2 ) // color level
				{
						if($db_type==0)
						{
							$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from $dtls_table pdtls where pdtls.production_type='$qty_source' and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from $dtls_table cur where cur.production_type=4 and cur.is_deleted=0 ) as cur_production_qnty
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
						}
						else
						{
							$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
									sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN c.production_type=4 then b.production_qnty ELSE 0 END) as cur_production_qnty
									from wo_po_color_size_breakdown a
									left join $dtls_table b on a.id=b.color_size_break_down_id and b.is_deleted=0 and b.status_active=1
									left join $mst_table c on c.id=b.mst_id and c.is_deleted=0 and c.status_active=1
									where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

						}

				}
				else if( $variableSettings==3 ) //color and size level
				{

					$dtlsData = sql_select("SELECT a.color_size_break_down_id,
												sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
												sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
												from $dtls_table a,$mst_table b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,4) group by a.color_size_break_down_id");


					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					}

					if($qty_source=="9")
					{
						// for difference of current table and cut delivery table
						$dtlsData = sql_select("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4) group by a.color_size_break_down_id");
						foreach($dtlsData as $row)
						{
	 						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
						}


					}


					$sql = "SELECT id, size_order,item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";


				}
				else // by default color and size level
				{
					$dtlsData = sql_select("SELECT a.color_size_break_down_id,
												sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
												sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
												from $dtls_table a,$mst_table b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,4) group by a.color_size_break_down_id");



					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					}

					$sql = "SELECT id,size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";
				}
			}

		}

		else // if preceding process =0 in variable setting then plan cut quantity will show
		{
			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{

				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN $dtls_table.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from $dtls_table where is_deleted=0 and production_type=4 ) as production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
				}
				else
				{
					$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty
					from wo_po_color_size_breakdown a left join $dtls_table b on a.id=b.color_size_break_down_id and b.production_type=4
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

				}
			}
			else if( $variableSettings==3 ) //color and size level
			{

				$dtlsData = sql_select("select a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty
											from $dtls_table a,$mst_table b where a.status_active=1  and b.status_active=1  and a.is_deleted= 0 and b.is_deleted=0  and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
				}

				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1  order by color_number_id,size_order"; //color_number_id, id 


			}
			else // by default color and size level
			{

				$dtlsData = sql_select("select a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty
											from $dtls_table a,$mst_table b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
				}

				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";//color_number_id, id
			}
		}




		// echo $sql;die();	
		$colorResult = sql_select($sql);
 		//print_r($sql);
  		$colorHTML="";
		$colorID='';
		$chkColor = array();
		$i=0;$totalQnty=0;
		if($qty_source!=0)
		{
			foreach($colorResult as $color)
			{

				if( $variableSettings==2 ) // color level
				{
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onkeyup="fn_colorlevel_total('.($i+1).')"></td></tr>';
					$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{

					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
					}
	 				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];


	 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
				}

				$i++;
			}

		}

		if($qty_source==0)
		{
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut_qnty")]-$color[csf("production_qnty")]).'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';
					$totalQnty += $color[csf("plan_cut_qnty")]-$color[csf("production_qnty")];
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)">  <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
					}
							 $bundle_mst_data="";
							 $bundle_dtls_data="";
						 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";
	 				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];

	 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'></td><td><input type="hidden" name="button" id="button_'.$color[csf("color_number_id")].($i+1).'" value="Click For Bundle" class="formbutton" style="size:30px;" onclick="openmypage_bandle('.$color[csf("id")].','.$color[csf("color_number_id")].($i+1).','.$tmp_col_size.');" /></td></tr>';
				}

				$i++;
			}
		}

		//echo $colorHTML;die;
		if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		//#############################################################################################//
		exit();
}



if($action=="show_dtls_listview")
{
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	$sewing_floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$prod_reso_allo = $dataArr[3];
	$company_id_sql=sql_select("SELECT a.company_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id group by a.company_name");
	$company_id=$company_id_sql[0][csf("company_name")];
	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];

	?>
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="150" align="center">Item Name</th>
                <th width="110" align="center">Country</th>
                <th width="80" align="center">Production Date</th>
                <th width="80" align="center">Production Qnty</th>
                <th width="110" align="center">Floor</th>
                <th width="110" align="center">Sewing Line</th>
                <th width="120" align="center">Serving Company</th>
                <th width="120" align="center">Location</th>
                <th width="100" align="center">Challan No</th>
				<th width="120" align="center">Wo. No</th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php
			$i=1;
			$total_production_qnty=0;
			if($sewing_level==1)
			{
				$qty=" sum(a.production_quantity) as production_quantity ";
				$status_active_cond=" ";
			}
			else
			{
				$qty= " sum(b.production_qnty) as production_quantity ";
				$status_active_cond =" and b.production_type='4' and b.status_active=1 and b.is_deleted=0  ";
			}
			$sqlResult ="SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id,a.production_date, $qty ,a.production_source,a.serving_company,a.sewing_line,a.location,a.prod_reso_allo,a.challan_no,a.floor_id,a.wo_order_no from pro_garments_production_mst a left join pro_garments_production_dtls b on a.id=b.mst_id where  a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='4' and a.status_active=1 and a.is_deleted=0  $status_active_cond
				GROUP BY a.id,a.po_break_down_id,a.item_number_id,a.country_id,a.production_date, a.production_source,a.serving_company,a.sewing_line,a.location,a.prod_reso_allo,a.challan_no,a.floor_id,a.wo_order_no ORDER BY a.production_date DESC" ;
			foreach(sql_select($sqlResult) as $selectResult){

				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('production_quantity')];

				$sewing_line='';
				//if($prod_reso_allo==1)
				if($selectResult[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$selectResult[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_arr[$val]; else $sewing_line.=",".$sewing_line_arr[$val];
					}
				}
				else $sewing_line=$sewing_line_arr[$selectResult[csf('sewing_line')]];
  			?>

			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" >
				<td width="40" align="center"><? //echo $i; ?>

					<input type="checkbox" id="tbl_<? echo $i; ?>"  onClick="fnc_checkbox_check(<? echo $i; ?>);"  />&nbsp;&nbsp;&nbsp; <? //echo $i; ?>
                   <input type="hidden" id="mstidall_<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>" style="width:30px"/>
                    <input type="hidden" id="servingCompany_<? echo $i; ?>"   width="30" value="<? echo $selectResult[csf('serving_company')]; ?>" />
                    <input type="hidden" id="productionsource_<? echo $i; ?>"   width="30" value="<? echo $selectResult[csf('production_source')]; ?>" />

                     <input type="hidden" id="servingLocation_<? echo $i; ?>"   width="30" value="<? echo $selectResult[csf('location')]; ?>" />
				</td>
                <td width="150" align="center" onClick="fnc_load_from_dtls(<? echo $selectResult[csf('id')]; ?>);"><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></td>
                <td width="110" align="center" onClick="fnc_load_from_dtls(<? echo $selectResult[csf('id')]; ?>);"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                <td width="80" align="center" onClick="fnc_load_from_dtls(<? echo $selectResult[csf('id')]; ?>);"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                <td width="80" align="center" onClick="fnc_load_from_dtls(<? echo $selectResult[csf('id')]; ?>);"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
				<td width="110" align="center" onClick="fnc_load_from_dtls(<? echo $selectResult[csf('id')]; ?>);"><? echo $sewing_floor_arr[$selectResult[csf('floor_id')]]; ?></td>
                <td width="110" align="center" onClick="fnc_load_from_dtls(<? echo $selectResult[csf('id')]; ?>);"><? echo $sewing_line; ?></td>
				<?php
                       	$source= $selectResult[csf('production_source')];
					   	if($source==3) $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
						else $serving_company= $company_arr[$selectResult[csf('serving_company')]];
                 ?>
                <td width="120" align="center" onClick="fnc_load_from_dtls(<? echo $selectResult[csf('id')]; ?>);"><?php echo $serving_company; ?></td>

                <td width="100" align="center" onClick="fnc_load_from_dtls(<? echo $selectResult[csf('id')]; ?>);"><? echo $location_arr[$selectResult[csf('location')]];; ?></td>
                <td  width="100" align="center" onClick="fnc_load_from_dtls(<? echo $selectResult[csf('id')]; ?>);"><?  echo $selectResult[csf('challan_no')]; ?></td>
				<td  width="120"align="center" onClick="fnc_load_from_dtls(<? echo $selectResult[csf('id')]; ?>);"><?  echo $selectResult[csf('wo_order_no')]; ?></td>
			</tr>
			<?php
			$i++;
			}
			?>
            <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>-->
		</table>
	</div>
<?
	exit();
}

if($action=="show_country_listview")
{
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="360" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="100">Item Name</th>
            <th width="80">Country</th>
            <th width="60">Shipment Date</th>
            <th width="50">Order Qty.</th>
            <th width="50">Sew.Input</th>
        </thead>
    </table>
    <div style="width:380px;max-height:300px; overflow:y-scroll" align="left">
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="360" class="rpt_table" id="country_list_search">
			<?
			$issue_qnty_arr=sql_select("SELECT a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty as cutting_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type=4 and b.production_type=4  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$issue_data_arr=array();
			foreach($issue_qnty_arr as $row)
			{
				$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("cutting_qnty")];
			}
			$i=1;
			$sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
			foreach($sqlResult as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$issue_qnty=$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);">
					<td width="20"><? echo $i; ?></td>
					<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
					<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
					<td width="60" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
					<td align="right" width="50"><?  echo $row[csf('order_qnty')]; ?></td>
	                <td align="right" width="50"><?  echo $issue_qnty; ?></td>
				</tr>
			<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}


if($action=="populate_input_form_data")
{
	$color_size_not_null_cond="";
	if($variableSettings==3)
	{
		$color_size_not_null_cond=" and( b.color_size_break_down_id is not null or b.color_size_break_down_id<>0) ";
	}

	$company_id_sql=sql_select("SELECT  company_id from  pro_garments_production_mst where  id=$data and status_active=1 and is_deleted=0 ");
	$company_id=$company_id_sql[0][csf("company_id")];
	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];
	if($sewing_level==1)
	{
		$qty=" sum(a.production_quantity) as production_quantity ";
		$alt_rej=" ";
		$status_active_cond=" ";
	}
	else
	{
		$qty= " sum(b.production_qnty) as production_quantity ";
		$alt_rej=" ,b.alter_qty,b.reject_qty ";
		$status_active_cond =" and b.production_type='4' and b.status_active=1 and b.is_deleted=0  ";
	}

	$sqlResult =sql_select("SELECT a.id, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.production_source, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date, $qty, a.production_type, a.entry_break_down_type, a.production_hour, a.sewing_line, a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.total_produced, a.yet_to_produced, a.wo_order_id, a.wo_order_no, a.man_cutt_no $alt_rej from pro_garments_production_mst a left join pro_garments_production_dtls b on a.id=b.mst_id where a.id='$data' and a.production_type='4' and a.status_active=1 and a.is_deleted=0   $color_size_not_null_cond  $status_active_cond  group by a.id, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.production_source, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date, a.production_type, a.entry_break_down_type, a.production_hour, a.sewing_line, a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.total_produced, a.yet_to_produced, a.wo_order_id, a.wo_order_no, a.man_cutt_no $alt_rej order by id");
	if($sqlResult[0][csf('production_source')]==1)
	{
		$company_id=$sqlResult[0][csf('serving_company')];
	}
	else
	{
		$company_id=$sqlResult[0][csf('company_id')];
	}

	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=4 and company_name='$company_id'");
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
	$qty_source=0;
	if($preceding_process==1) $qty_source=1; //Kniting Input
	else if($preceding_process==100) $qty_source=100;//1st Inspection

	$mst_table=($preceding_process==123)? "pro_cut_delivery_order_dtls" : "pro_garments_production_mst";
	$dtls_table=($preceding_process==123)? "pro_cut_delivery_color_dtls" : "pro_garments_production_dtls";


	foreach($sqlResult as $result)
	{
		echo "$('#txt_wo_no').val('".$result[csf('wo_order_no')]."');\n";
		echo "$('#txt_wo_id').val('".$result[csf('wo_order_id')]."');\n";
		echo "$('#txt_sewing_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#txt_input_qnty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
  		echo "$('#txt_man_cutting_no').val('".$result[csf('man_cutt_no')]."');\n";

		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "$('#txt_iss_id').val('".$data."');\n";
		echo "load_drop_down( 'requires/sewing_input_controller', ".$result[csf('production_source')].", 'load_drop_down_sewing_input', 'sew_company_td' );\n";
		echo "$('#cbo_sewing_company').val('".$result[csf('serving_company')]."');\n";
		echo "load_drop_down( 'requires/sewing_input_controller',".$result[csf('serving_company')].", 'load_drop_down_location', 'location_td' );";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/sewing_input_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "load_drop_down( 'requires/sewing_input_controller', document.getElementById('cbo_floor').value+'_'+$('#cbo_location').val()+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value+'_'+document.getElementById('cbo_sewing_company').value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );\n";

		echo "$('#cbo_sewing_line').val('".$result[csf('sewing_line')]."');\n";
		echo "get_php_form_data('".$result[csf('production_source')]."','line_disable_enable','requires/sewing_input_controller');\n";


		if($qty_source!=0)
		{
			$dataArray=sql_select("select SUM(CASE WHEN production_type=$qty_source THEN production_quantity END) as totalReceive,SUM(CASE WHEN production_type=4 THEN production_quantity ELSE 0 END) as totalInput from $mst_table WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and is_deleted=0");
			foreach($dataArray as $row)
			{
	 			echo "$('#txt_receive_qnty').val('".$row[csf('totalReceive')]."');\n";
				echo "$('#txt_cumul_input_qty').val('".$row[csf('totalInput')]."');\n";
				$yet_to_produced = $row[csf('totalReceive')]-$row[csf('totalInput')];
				echo "$('#txt_yet_to_input').attr('placeholder','".$yet_to_produced."');\n";
				echo "$('#txt_yet_to_input').val('".$yet_to_produced."');\n";
			}

		}
		else
		{
			$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and status_active=1 and is_deleted=0");

			$total_produced = return_field_value("sum(b.production_qnty)","$mst_table a,$dtls_table b","a.id=b.mst_id and  a.po_break_down_id=".$result[csf('po_break_down_id')]." and a.item_number_id=".$result[csf('item_number_id')]." and a.country_id=".$result[csf('country_id')]." and a.production_type=4 and b.production_type=4  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			echo "$('#txt_input_quantity').val('".$plan_cut_qnty."');\n";
			echo "$('#txt_cumul_poly_qty').val('".$total_produced."');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
			echo "$('#txt_yet_to_poly').val('".$yet_to_produced."');\n";
		}

		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_sewing_input_entry',1,1);\n";

		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		$variableSettings = $result[csf('entry_break_down_type')];
		if($qty_source!=0)
		{
			if( $variableSettings!=1 ) // gross level
			{
				$po_id = $result[csf('po_break_down_id')];
				$item_id = $result[csf('item_number_id')];
				$country_id = $result[csf('country_id')];
 				$sql_dtls = sql_select("select color_size_break_down_id,production_qnty,size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
				foreach($sql_dtls as $row)
				{

					if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
				  	$amountArr[$index] = $row[csf('production_qnty')];
				}

				if( $variableSettings==2 ) // color level
				{

						if($db_type==0)
						{
							$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from $dtls_table pdtls where pdtls.production_type=$qty_source and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from $dtls_table cur where cur.production_type=4 and cur.is_deleted=0 ) as cur_production_qnty
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
						}
						else
						{
							$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
									sum(CASE WHEN c.production_type=$qty_source then b.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN c.production_type=4 then b.production_qnty ELSE 0 END) as cur_production_qnty
									from wo_po_color_size_breakdown a
									left join $dtls_table b on a.id=b.color_size_break_down_id
									left join $mst_table c on c.id=b.mst_id
									where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

						}



				}
				else if( $variableSettings==3 ) //color and size level
				{
  					$dtlsData = sql_select("select a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=$qty_source then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
											from $dtls_table a,$mst_table b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,4) group by a.color_size_break_down_id");



					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					}

					if($qty_source=="9")
					{
						// for difference of current table and cut delivery table
						$dtlsData = sql_select("select a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4) group by a.color_size_break_down_id");
						foreach($dtlsData as $row)
						{
	 						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
						}


					}

					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, size_order";


				}
				else // by default color and size level
				{
					$dtlsData = sql_select("select a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=$qty_source then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
											from $dtls_table a,$mst_table b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,4) group by a.color_size_break_down_id");


					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					}

					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";
				}

	 			$colorResult = sql_select($sql);
	 			//print_r($sql);die;
				$colorHTML="";
				$colorID='';
				$chkColor = array();
				$i=0;$totalQnty=0;$colorWiseTotal=0;
				foreach($colorResult as $color)
				{

					if( $variableSettings==2 ) // color level
					{
						$amount = $amountArr[$color[csf("color_number_id")]];
						$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onkeyup="fn_colorlevel_total('.($i+1).');fn_chk_next_process_qty('.$color[csf("color_number_id")].','.($i+1).','.$color[csf("size_number_id")].')"><input type="hidden" name="colorSizeUpQty" id="colSizeUpQty_'.$color[csf("color_number_id")].($i+1).'" value="'.$amount.'" ></td></tr>';
						$totalQnty += $amount;
						$colorID .= $color[csf("color_number_id")].",";
					}
					else //color and size level
					{
						$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];
						$amount = $amountArr[$index];
						if( !in_array( $color[csf("color_number_id")], $chkColor ) )
						{
							if( $i!=0 ) $colorHTML .= "</table></div>";
							$i=0;$colorWiseTotal=0;
							$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
							$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
							$chkColor[] = $color[csf("color_number_id")];
							$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
						}
	 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

						$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
						$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];

						$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).');fn_chk_next_process_qty('.$color[csf("color_number_id")].','.($i+1).','.$color[csf("size_number_id")].')" onkeyup="" value="'.$amount.'" ><input type="hidden" name="colorSizeUpQty" id="colSizeUpQty_'.$color[csf("color_number_id")].($i+1).'" value="'.$amount.'" ><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
						$colorWiseTotal += $amount;
					}

					$i++;
				}
				if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
				echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
				if( $variableSettings==3 )echo "$totalFn;\n";
				$colorList = substr($colorID,0,-1);
				echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
			}

		}

		if($qty_source==0)
		{
			if( $variableSettings!=1 ) // gross level
			{
				$po_id = $result[csf('po_break_down_id')];
				$item_id = $result[csf('item_number_id')];
				$country_id = $result[csf('country_id')];


				$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from $dtls_table a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");

				foreach($sql_dtls as $row)
				{
					if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
				  	$amountArr[$index] = $row[csf('production_qnty')];
					$rejectArr[$index] = $row[csf('reject_qty')];
				}

				if( $variableSettings==2 ) // color level
				{
					if($db_type==0)
					{

						$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN $dtls_table.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from $dtls_table where is_deleted=0 and  	production_type=4 ) as production_qnty, (select sum(CASE WHEN $dtls_table.color_size_break_down_id=wo_po_color_size_breakdown.id then reject_qty ELSE 0 END) from $dtls_table where is_deleted=0 and production_type=4 ) as reject_qty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 group by color_number_id";
					}
					else
					{
						$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty, sum(b.reject_qty) as reject_qty
					from wo_po_color_size_breakdown a left join $dtls_table b on a.id=b.color_size_break_down_id and b.production_type=4
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

					}
				}
				else if( $variableSettings==3 ) //color and size level
				{

						$dtlsData = sql_select("select a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=4 then a.reject_qty ELSE 0 END) as reject_qty
											from $dtls_table a,$mst_table b where a.status_active=1 and a.mst_id=b.id  and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4) group by a.color_size_break_down_id");
						//and b.id='$data'

						foreach($dtlsData as $row)
						{
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
						}

						$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";


				}
				else // by default color and size level
				{


					$dtlsData = sql_select("select a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=4 then a.reject_qty ELSE 0 END) as reject_qty
											from $dtls_table a,$mst_table b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4) group by a.color_size_break_down_id");

					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
					}
					//print_r($color_size_qnty_array);

					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";

				}

				if($variableSettingsRej!=1)
				{
					$disable="";
				}
				else
				{
					$disable="disabled";
				}

	 			$colorResult = sql_select($sql);
	 			//print_r($sql_dtls);die;
				$colorHTML="";
				$colorID='';
				$chkColor = array();
				$i=0;$totalQnty=0;$colorWiseTotal=0;
				foreach($colorResult as $color)
				{

					if( $variableSettings==2 ) // color level
					{
						$amount = $amountArr[$color[csf("color_number_id")]];
						$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
						$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut_qnty")]-$color[csf("production_qnty")]+$amount).'" value="'.$amount.'" onkeyup="fn_colorlevel_total('.($i+1).');fn_chk_next_process_qty('.$color[csf("color_number_id")].','.($i+1).','.$color[csf("size_number_id")].')"><input type="hidden" name="colorSizeUpQty" id="colSizeUpQty_'.$color[csf("color_number_id")].($i+1).'" value="'.$amount.'" ></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';
						$totalQnty += $amount;
						$totalRejQnty += $rejectAmt;
						$colorID .= $color[csf("color_number_id")].",";
					}
					else //color and size level
					{
						$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];

						$amount = $amountArr[$index];
						//$amount = $color[csf("size_number_id")]."*".$color[csf("color_number_id")];
						if( !in_array( $color[csf("color_number_id")], $chkColor ) )
						{
							if( $i!=0 ) $colorHTML .= "</table></div>";
							$i=0;
							$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].': <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
							$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
							$chkColor[] = $color[csf("color_number_id")];
							$totalFn .= "fn_total(".$color[csf("color_number_id")].");";

						}


						 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";
	 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
						$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];
							$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];


						$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty+$amount).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).');fn_chk_next_process_qty('.$color[csf("color_number_id")].','.($i+1).','.$color[csf("size_number_id")].')" value="'.$amount.'" ><input type="hidden" name="colorSizeUpQty" id="colSizeUpQty_'.$color[csf("color_number_id")].($i+1).'" value="'.$amount.'" ><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'></td><td><input type="hidden" name="button" value="Click For Bundle" class="formbutton" style="size:30px;" onclick="openmypage_bandle('.$color[csf("id")].','.$color[csf("color_number_id")].($i+1).','.$tmp_col_size.');" /></td></tr>';
						//$colorWiseTotal += $amount;
						 $bundle_dtls_data="";
						 $bundle_dtls_data="";
					}
					$i++;
				}
				//echo $colorHTML;die;
				if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$result[csf('production_quantity')].'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
				echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
				if( $variableSettings==3 )echo "$totalFn;\n";
				$colorList = substr($colorID,0,-1);
				echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
			}
		}
	}
 	exit();
}

if($action=="chk_next_process_qty")
{
	extract($_REQUEST);
	$size_cond = "";
	if(str_replace("'", "", $sewing_production_variable)==3)
	{
		$size_cond = " and c.size_number_id=$sizeId";
	}
	// $col_size_id = explode("*", str_replace("'", "", $hidden_colorSizeID));
	$sql = "SELECT sum(case when a.production_type=4 then b.production_qnty else 0 end) as input_qty,sum(case when a.production_type=5 then b.production_qnty else 0 end) as output_qty from pro_garments_production_mst a,pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name and c.color_number_id=$colorId $size_cond and c.status_active=1 and c.is_deleted=0 and a.po_break_down_id=$hidden_po_break_down_id and a.production_type in(4,5) and c.id=b.color_size_break_down_id";
	// echo $sql;
	$sql_res = sql_select($sql);
	echo $sql_res[0]['OUTPUT_QTY']."****".$sql_res[0]['INPUT_QTY'];
	die();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_name");
	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active in(1,2,3) and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{			
			echo "786**Projected PO is not allowed to production. Please check variable settings";die();
		}
	}
	//echo "10**".$hidden_po_break_down_id."##".$cbo_country_name."##".$cbo_item_name;die;

	//$budget_emblishment=sql_select("select b.emb_name from wo_po_break_down a, wo_pre_cost_embe_cost_dtls b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$hidden_po_break_down_id");
	$budget_emblishment=sql_select("SELECT b.emb_name from wo_po_break_down a, wo_pre_cost_embe_cost_dtls b where a.job_no_mst=b.job_no and b.cons_dzn_gmts >0 and b.emb_name !=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$hidden_po_break_down_id order by b.id asc");
	$emb_name_end=end($budget_emblishment);
	$emb_name_id=$emb_name_end[csf('emb_name')];

	if(!str_replace("'","",$sewing_production_variable)) $sewing_production_variable=3;

	$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_sewing_company and variable_list=33 and page_category_id=28","is_control");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_sewing_company and variable_list=23 and status_active=1 and is_deleted=0");
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		//----------Compare by finishing qty and iron qty qty for validation----------------

		//echo "10**".$is_control."**".$user_level;die;
		/*if($is_control==1 && $user_level!=2)
		{
			$txt_input_qnty=str_replace("'","",$txt_input_qnty);

			$cutting_entry=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=1 and country_id=$cbo_country_name and status_active=1 and is_deleted=0");

			$country_sewing_input_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=4 and country_id=$cbo_country_name and status_active=1 and is_deleted=0");



			//echo $country_sewing_output_qty .'<'. $country_iron_qty.'+'.$txt_iron_qty;die;
			//$sewing_tot_in=$country_sewing_input_qty+$txt_input_qnty;
			if($cutting_entry < $country_sewing_input_qty+$txt_input_qnty)
			{
				echo "25**0";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}
		}*/
		//--------------------------------------------------------------Compare end;
		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
		$txt_challan_no=(str_replace("'", "", $txt_challan)==0)? $id : $txt_challan;
   		$field_array1="id, garments_nature, company_id, challan_no,man_cutt_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date,produced_by, production_quantity, production_type, entry_break_down_type, sewing_line, remarks, floor_id, total_produced, yet_to_produced, prod_reso_allo,  wo_order_id, wo_order_no, inserted_by, insert_date";
		$data_array1="(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan_no.",".$txt_man_cutting_no.",".$hidden_po_break_down_id.",".$cbo_item_name.", ".$cbo_country_name.",".$cbo_source.",".$cbo_sewing_company.",".$cbo_location.",".$txt_sewing_date.",".$cbo_produced_by.",".$txt_input_qnty.",4,".$sewing_production_variable.",".$cbo_sewing_line.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_input_qty.",".$txt_yet_to_input.",'".$prod_reso_allo."',".$txt_wo_id.",".$txt_wo_no.",".$user_id.",'".$pc_date_time."')";

 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		//echo $data_array;die;

		// pro_garments_production_dtls table entry here ----------------------------------///
		$field_array="id, mst_id,production_type,color_size_break_down_id,production_qnty";

		if(count($budget_emblishment)>0)
		{
			$dtlsData = sql_select("select a.color_size_break_down_id,
				sum(CASE WHEN (a.production_type=3 and b.embel_name=$emb_name_id) then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b
				where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(3,4)
				group by a.color_size_break_down_id");
		}
		else
		{
			$dtlsData = sql_select("select a.color_size_break_down_id,
				sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b
				where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(1,4)
				group by a.color_size_break_down_id");
		}

		$color_pord_data=array();
		foreach($dtlsData as $row)
		{
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}

		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{

			$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}

			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 			$rowEx = array_filter(explode("**",$colorIDvalue));
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				/*if($is_control==1 && $user_level!=2)
				{
					if($colorSizeNumberIDArr[1]>0)
					{
						if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
						{
							echo "35**Production Quantity Not Over Cutting Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}*/

				if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
				{
					//4 for Sewing Input Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",4,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					else $data_array .= ",(".$dtls_id.",".$id.",4,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					//$dtls_id=$dtls_id+1;
	 				$j++;
	 			}
	 			else
	 			{
	 				echo "420**";die();
	 			}
			}
 		}//color level wise

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{

			$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val)
			{
				$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}

			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
 			$rowEx = array_filter(explode("***",$colorIDvalue));
 			// echo "10**";print_r($rowEx);die;
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;

				/*if($is_control==1 && $user_level!=2)
				{
					if($colorSizeValue>0)
					{
						if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
						{
							echo "35**Production Quantity Not Over Cutting Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}*/

				if($colSizeID_arr[$index]!="")
				{
					//4 for Sewing Input Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",4,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					else $data_array .= ",(".$dtls_id.",".$id.",4,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					//$dtls_id=$dtls_id+1;
	 				$j++;
 				}
	 			else
	 			{
	 				echo "420**";die();
	 			}
			}
		}//color and size wise
		// echo "10**insert into pro_garments_production_dtls (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,0);

		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,0);
		}

		//echo "10**".$rID.'=='.$dtlsrID;die;
		//release lock table


		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}


		//----------Compare by finishing qty and iron qty qty for validation----------------
		/*if($is_control==1 && $user_level!=2)
		{
			$txt_input_qnty=str_replace("'","",$txt_input_qnty);
			$txt_mst_id=str_replace("'","",$txt_mst_id);

			$cutting_entry=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=1 and country_id=$cbo_country_name and status_active=1 and is_deleted=0");

			$country_sewing_input_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=4 and country_id=$cbo_country_name and status_active=1 and is_deleted=0 and id <> $txt_mst_id");


			//echo $country_sewing_output_qty .'<'. $country_iron_qty.'+'.$txt_iron_qty;die;
			if($cutting_entry < $country_sewing_input_qty+$txt_input_qnty)
			{
				echo "25**0";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}

		}*/
		//--------------------------------------------------------------Compare end;



		// pro_garments_production_mst table data entry here
 		$field_array1="production_source*serving_company*location*man_cutt_no*production_date*produced_by*production_quantity*production_type*entry_break_down_type*sewing_line*challan_no*remarks*floor_id*total_produced*yet_to_produced*prod_reso_allo*wo_order_id*wo_order_no*updated_by*update_date";
		$data_array1="".$cbo_source."*".$cbo_sewing_company."*".$cbo_location."*".$txt_man_cutting_no."*".$txt_sewing_date."*".$cbo_produced_by."*".$txt_input_qnty."*4*".$sewing_production_variable."*".$cbo_sewing_line."*".$txt_challan."*".$txt_remark."*".$cbo_floor."*".$txt_cumul_input_qty."*".$txt_yet_to_input."*'".$prod_reso_allo."'*".$txt_wo_id."*".$txt_wo_no."*".$user_id."*'".$pc_date_time."'";

 		//$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		//echo $data_array;die;

		// pro_garments_production_dtls table data entry here
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) // check is not gross level
		{
			if(count($budget_emblishment)>0)
			{
				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN (a.production_type=3 and b.embel_name=$emb_name_id) then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(3,4) and b.id!=$txt_mst_id
										group by a.color_size_break_down_id");
			}
			else
			{
				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(1,4) and b.id!=$txt_mst_id
										group by a.color_size_break_down_id");
			}

			$color_pord_data=array();
			foreach($dtlsData as $row)
			{
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}


 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0  order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = array_filter(explode("**",$colorIDvalue));
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					/*if($is_control==1 && $user_level!=2)
					{
						if($colorSizeNumberIDArr[1]>0)
						{
							if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
							{
								echo "35**Production Quantity Not Over Cutting Qnty";
								//check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}*/
					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{
						//4 for Sewing Input Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",4,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",4,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
						//	$dtls_id=$dtls_id+1;
						$j++;
					}
					else
					{
						echo "420**";die();
					}
				}
			}

			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				$rowEx = array_filter(explode("***",$colorIDvalue));
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;

					/*if($is_control==1 && $user_level!=2)
					{
						if($colorSizeValue>0)
						{
							if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
							{
								echo "35**Production Quantity Not Over Cutting Qnty";
								//check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}*/
					if($colSizeID_arr[$index]!="")
					{

						//4 for Sewing Input Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",4,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",4,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
					else
					{
						echo "420**";die();
					}
				}
			}
 			//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}//end cond


		$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",0);
		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",0);

		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,0);
		}

		//release lock table


		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrDelete && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}else{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

 		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);

 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$hidden_po_break_down_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$hidden_po_break_down_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="sewing_input_print")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sewing Input Challan Print", "../", 1, 1,'','','');
	$data=explode('*',$data);
    $mst_id=implode(',',explode("_",$data[1]));
	$mst_update_id=str_pad($data[1],10,'0',STR_PAD_LEFT);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$order_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$season_arr=return_library_array( "select id, season_name from  lib_buyer_season",'id','season_name');

	$sql="SELECT id, company_id, challan_no, sewing_line, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date,prod_reso_allo, production_quantity, production_type, remarks, floor_id,man_cutt_no, sewing_line from pro_garments_production_mst where production_type=4 and id in($mst_id) and status_active=1 and is_deleted=0 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$poId = $dataArray[0][csf('po_break_down_id')];
	$season_sql = "SELECT a.season_buyer_wise,b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$poId and a.status_active=1 and a.is_deleted=0";
	$season_res = sql_select($season_sql);
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						<? echo $result[csf('plot_no')]; ?> &nbsp;
						<? echo $result[csf('level_no')]?>&nbsp;
						<? echo $result[csf('road_no')]; ?> &nbsp;
						<? echo $result[csf('block_no')];?> &nbsp;
						<? echo $result[csf('city')];?> &nbsp;
						<? echo $result[csf('zip_code')]; ?> &nbsp;
						<? echo $result[csf('province')];?> &nbsp;
						<? echo $country_arr[$result[csf('country_id')]]; ?><br>
						<? echo $result[csf('email')];?> &nbsp;
						<? echo $result[csf('website')];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><? echo $data[2]; ?> Challan ( <? echo $country_arr[$dataArray[0][csf('country_id')]]; ?> )</strong></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
				foreach($dataArray as $row)
				{
					$job_no=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"job_no");
					$buyer_val=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"buyer_name");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"style_ref_no");



					$internal_ref=return_field_value("f.grouping"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"grouping");

				}
				$order_qnty=return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","country_id=".$dataArray[0][csf('country_id')]." and po_break_down_id=".$row[csf("po_break_down_id")]." and status_active=1 and is_deleted=0","order_quantity");s

            ?>
        	<td width="270" rowspan="4" valign="top" colspan="2"><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></td>
            <td width="125"><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>

            <td><strong>Input Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
        </tr>
         <tr>
            <td><strong>Order No :</strong></td><td ><? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?></td>
            <td><strong>Buyer:</strong></td><td><? echo $buyer_library[$buyer_val]; ?></td>
        </tr>
        <tr>
        	<td><strong>Internal Ref:</strong></td> <td><? echo $internal_ref; ?></td>

            <td><strong>Style Ref.:</strong></td> <td><? echo $style_val; ?></td>
        </tr>
        <tr>
        	<td><strong>Job No :</strong></td><td ><? echo $job_no; ?></td>
            <td><strong>Order Qnty:</strong></td><td><? echo $order_qnty;//$dataArray[0][csf('production_quantity')]; ?></td>
        </tr>
		<tr>
            <td colspan="2"><strong>Location: </strong>&nbsp;&nbsp;&nbsp;
			<? echo $location_arr[$row[csf('location')]];?></td>
            <td><strong>Item:</strong></td> <td ><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
            <td><strong>Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Sewing Line: </strong>&nbsp;&nbsp;&nbsp;
			<?
			 // echo $dataArray[0][csf('sewing_line')];
			    $sewing_line='';

			    foreach ($dataArray as $row) {
			    	
			    	if($row[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_line_arr[$val]; else $sewing_line.=", ".$sewing_line_arr[$val];
						}
					}
					else {$sewing_line=$sewing_line_arr[$row[csf('sewing_line')]];}

			    }

				//if($prod_reso_allo==1)
				/*if($dataArray[0][csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$dataArray[0][csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_arr[$val]; else $sewing_line.=",".$sewing_line_arr[$val];
					}
				}
				else {$sewing_line=$sewing_line_arr[$dataArray[0][csf('sewing_line')]];}*/
			    echo $sewing_line;
			 ?></td>
            <td><strong>Floor:</strong></td> <td><? echo $floor_arr[$dataArray[0][csf('floor_id')]]; ?></td>
            <td><strong>Season:</strong></td> <td><? echo $season_arr[$season_res[0][csf('season_buyer_wise')]]; ?></td>
        </tr>
        <tr height="25">
            <td colspan="4"><strong><p>Remarks:  <? echo $dataArray[0][csf('remarks')]; ?></p></strong></td>
            <td colspan="2" id="barcode_img_id" style="font-size:24px"></td>
        </tr>
        <tr>
        	<td><strong><p>Man Cut No.:  <? echo $dataArray[0][csf('man_cutt_no')]; ?></p></strong></td>
        </tr>
    </table>
    <br>
    <br>
        <?
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			//$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id";
			$sql="SELECT b.id, a.production_qnty as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.color_number_id, b.id";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in ($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id order by b.color_number_id";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>
         	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="60">Particulars</th>
            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Qty.</th>
        </thead>
        <tbody>
			<?
				$i=1;
				$tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr>
                        <td rowspan="2"><? echo $i;  ?></td>
                        <td> <? echo "Input";  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
							$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <tr>
                    	<td><? echo "No Of Bundle";  ?></td>
                        <td align="center">''</td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? //echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? //echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <!--<tr bgcolor="#99CCFF">
                    	<td><? /*echo "QC Pass Qty";  ?></td>
                        <td align="center">''</td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? //echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? //echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <tr bgcolor="#99CCFF">
                    	<td><? echo "Alter Qty";  ?></td>
                        <td align="center">''</td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? //echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? //echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <tr bgcolor="#99CCFF">
                    	<td><? echo "Spot Qty";  ?></td>
                        <td align="center">''</td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? //echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? //echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <tr bgcolor="#99CCFF">
                    	<td><? echo "Reject Qty";  ?></td>
                        <td align="center">''</td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? //echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                        }*/
                        ?>
                        <td align="right"><? //echo $tot_qnty[$cid]; ?></td>
                    </tr>-->
                    <?
					$production_quantity+=$tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>
    </table>
        <br>
		 <?
            echo signature_table(28, $data[0], "900px");
         ?>
	</div>
	</div>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
    <script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			  //alert(value)
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
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $mst_update_id; ?>');
	</script>
<?
exit();
}

if($action=="sewing_input_print2")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sewing Input Challan Print", "../", 1, 1,'','','');
	$data=explode('*',$data);
    $mst_id=implode(',',explode("_",$data[1]));
	$mst_update_id=str_pad($data[1],10,'0',STR_PAD_LEFT);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$order_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$country_shortname_arr=return_library_array( "select id, short_name from lib_country", "id", "short_name");
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$season_arr=return_library_array( "select id, season_name from  lib_buyer_season",'id','season_name');

	$sql="SELECT id, company_id, challan_no, sewing_line, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date,prod_reso_allo, production_quantity, production_type, remarks, floor_id,man_cutt_no, sewing_line from pro_garments_production_mst where production_type=4 and id in($mst_id) and status_active=1 and is_deleted=0 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$poId = $dataArray[0][csf('po_break_down_id')];
	$season_sql = "SELECT a.season_buyer_wise,b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$poId and a.status_active=1 and a.is_deleted=0";
	$season_res = sql_select($season_sql);
	//echo $season_sql;
    $supp_add=$dataArray[0][csf('serving_company')];
    $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
    foreach ($nameArray as $result)
    {
        $address="";
        if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
    }
	//echo $address;
	foreach($dataArray as $row)
	{
		$job_no=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"job_no");
		$buyer_val=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"buyer_name");
		$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"style_ref_no");



		$internal_ref=return_field_value("f.grouping"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"grouping");

	}
	$order_qnty=return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","country_id=".$dataArray[0][csf('country_id')]." and po_break_down_id=".$row[csf("po_break_down_id")]." and status_active=1 and is_deleted=0","order_quantity");
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="4" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            <td> <strong> Job No <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </strong> </td>
            <td> <? echo $job_no; ?> </td>
        </tr>

        <tr class="">
        	<td colspan="4" align="center" style="font-size:14px" class="form_caption">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						<? echo $result[csf('plot_no')]; ?> &nbsp;
						<? echo $result[csf('level_no')]?>&nbsp;
						<? echo $result[csf('road_no')]; ?> &nbsp;
						<? echo $result[csf('block_no')];?> &nbsp;
						<? echo $result[csf('city')];?> &nbsp;
						<? echo $result[csf('zip_code')]; ?> &nbsp;
						<? echo $result[csf('province')];?> &nbsp;
						<? echo $country_arr[$result[csf('country_id')]]; ?><br>
						<? echo $result[csf('email')];?> &nbsp;
						<? echo $result[csf('website')];
					}
                ?>
            </td>
            <td> <strong> Style Ref <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </strong> </td>
            <td> <? echo $style_val; ?> </td>
        </tr>

        <tr>
            <td colspan="4" align="center" style="font-size:18px"><strong><? echo $data[2]; ?> Challan ( <? echo $country_arr[$dataArray[0][csf('country_id')]]; ?> )</strong></td>
            <td> <strong> Internal Ref No <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </strong> </td>
            <td> <? echo $internal_ref; ?> </td>
        </tr>
         <tr>
         	<td></td>
         	<td></td>
         	<td></td>
         	<td></td>
            <td><strong>Season <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></strong></td> <td><? echo $season_arr[$season_res[0][csf('season_buyer_wise')]]; ?></td>
        </tr>

        <tr><td colspan="6">&nbsp;  </td></tr>

        <tr>
        	<td style="font-size:18px" align="left" colspan="2"><strong>To</strong></td>
        	<td><strong> Buyer <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </strong></td>
        	<td><? echo $buyer_library[$buyer_val]; ?></td>
        </tr>

        <tr>
        	<td colspan="2">
        		<strong>
        			<?
        				if($dataArray[0][csf('production_source')]==1)
        				{
        					echo $company_library[$dataArray[0][csf('serving_company')]];

        					$com=$dataArray[0][csf('serving_company')];
        					$com_array=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$com");
							foreach ($com_array as $result)
							{
								$addr=$result[csf('plot_no')]." ".$result[csf('level_no')]." ".$result[csf('road_no')]." ".$result[csf('block_no')]." ".$result[csf('city')]." ".$result[csf('zip_code')]." ".$result[csf('province')]." ".$country_arr[$result[csf('country_id')]];
							}
        				}
        				else if($dataArray[0][csf('production_source')]==3)
        				{
        					echo $supplier_library[$dataArray[0][csf('serving_company')]];
        					$addr=$address ;
        				}
        			?>
        		</strong>
        	</td>
        	<td><strong> Order No. <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </strong></td>
        	<td> <? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?> </td>
        </tr>

        <tr>
        	<td colspan="2"><strong><? echo $addr; ?></strong> </td>
        	<td><strong> Order Qty. <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </strong></td>
        	<td><? echo $order_qnty; ?></td>
        </tr>

        <tr>
        	<td><strong> Location <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </strong></td>
        	<td><? echo $location_arr[$row[csf('location')]];?></td>
        	<td><strong> Item <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </strong></td>
        	<td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        </tr>

        <tr>
        	<td><strong> Remarks <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </strong></td>
        	<td><? echo $dataArray[0][csf('remarks')]; ?></td>
        	<td><strong> Sewing Line <span style="float:right">: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </strong></td>
        	<td>
			<?
			 // echo $dataArray[0][csf('sewing_line')];
			    $sewing_line='';


			    foreach ($dataArray as $row) {
			    	
			    	if($row[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_line_arr[$val]; else $sewing_line.=", ".$sewing_line_arr[$val];
						}
					}
					else {$sewing_line=$sewing_line_arr[$row[csf('sewing_line')]];}
				
			    }
			    
				//if($prod_reso_allo==1)
				/*if($dataArray[0][csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$dataArray[0][csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_arr[$val]; else $sewing_line.=",".$sewing_line_arr[$val];
					}
				}
				else {$sewing_line=$sewing_line_arr[$dataArray[0][csf('sewing_line')]];}*/
			    echo $sewing_line;
			 ?>
			</td>
        </tr>
    </table>
    <br><br>
<!-- ######################################  Body Part ########################################### -->
        <?
			//$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, a.remarks, a.country_id, b.production_qnty, c.color_number_id, c.size_number_id,c.size_order from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.production_type=4 and b.production_type=4 and a.id in($mst_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by c.size_order";

			$result=sql_select($sql);
			$size_array=array ();
			$color_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$qun_array[$row[csf('id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>

	<div style="width:100%;">
	    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="60">Input Date</th>
            <th width="60">Manual Cut No</th>
            <th width="60">Challan no</th>
            <th width="60">Floor</th>
            <th width="120">Remarks</th>
            <th width="60">Country</th>

            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
                    ?>
                        <th width="50"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qty.</th>
        </thead>
    <tbody>
    	<?
    		$sql_prod="SELECT a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, a.remarks, a.country_id, c.color_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.production_type=4 and b.production_type=4 and a.id in($mst_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, a.remarks, a.country_id, c.color_number_id";
    		$result_prod=sql_select($sql_prod);
			$i=1;
			$tot_specific_size_qnty=array();
			//$grand_tot_color_size_qty=0;
			foreach ($result_prod as $val)
			{
				$tot_color_size_qty=0;
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
				<tr>
                    <td> <? echo $i;  ?> </td>
                    <td> <? echo change_date_format($val[csf("production_date")]);  ?> </td>
                    <td> <? echo $val[csf("man_cutt_no")]; ?> </td>
                    <td>
                    	<?
                    		
                    	if($val[csf("challan_no")])echo $val[csf("challan_no")];
                    	else echo $val[csf("id")];
                    	?>
                    </td>
                    <td> <? echo $floor_arr[$val[csf("floor_id")]]; ?> </td>
                    <td> <? echo $val[csf("remarks")]?> </td>
                    <td> <? echo $country_shortname_arr[$val[csf("country_id")]]; ?> </td>
                    <td> <? echo $colorarr[$val[csf("color_number_id")]]; ?> </td>
                    <?
                    foreach ($size_array as $sizval)
                    {
                    ?>
                        <td align="right"><? echo $qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval]; ?></td>
                    <?
                       $tot_color_size_qty+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
                       //$grand_tot_color_size_qty+=$tot_color_size_qty;
                       $tot_specific_size_qnty[$sizval]+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
                    }
                    ?>
                    <td align="right">
                    	<?
                    	echo $tot_color_size_qty;
                    	?>
                    </td>
                 </tr>
        <?
			$i++;	}
		?>
    </tbody>
        <tr>
            <td colspan="8" align="right"><strong>Grand Total : &nbsp;</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_specific_size_qnty[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo array_sum($tot_specific_size_qnty); //$grand_tot_color_size_qty; ?></td>
        </tr>
    </table>

    <br>
	 <?
        echo signature_table(28, $data[0], "900px");
     ?>
	</div>
	</div>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
    <script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			  //alert(value)
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
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $mst_update_id; ?>');
	</script>
<?
exit();
}
?>

<script type="text/javascript">
	function getActionOnEnter(event){
			if (event.keyCode == 13){
				document.getElementById('btn_show').click();
			}

	}
</script>