<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$userId=$_SESSION['logic_erp']['user_id'];
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$process_knitting="2";

/*
|--------------------------------------------------------------------------
| load_drop_down_location
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_knit_location
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_knit_location")
{
	if($data==0)
	{
		echo create_drop_down( "cbo_knit_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
	}
	else
	{
		echo create_drop_down( "cbo_knit_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_floor();","","","","","",3 );
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_party_name
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_party_name")
{
	echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",4 );
	exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_floor
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_floor")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	$txtVariableAutoSave=$data[2];
	$floor_id=$data[3];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and a.id=$floor_id";
	//if($txtVariableAutoSave==1){$location_cond="";}else{$location_cond=$location_cond;}

	if($company_id==0 && $location_id==0)
	{
		echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "-- Select Floor --", 0, "",0 );
	}
	else
	{
		//echo "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name";

		echo create_drop_down( "cbo_floor_id", 150, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond $floor_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "load_machine();","" );
	}
  exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_machine
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_machine")
{
	$data_ex=explode("_",$data);
	$company_id=$data_ex[0];
	$floor_id=$data_ex[1];
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";
	if($db_type==0)
	{
		$sql="select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}
	else if($db_type==2)
	{
		$sql="select id, machine_no || '-' || brand as machine_name from lib_machine_name where category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}

	if($company_id==0 && $floor_id==0)
	{
		echo create_drop_down( "cbo_machine_name", 150, $blank_array,"", 1, "-- Select Machine --", 0, "",0 );
	}
	else
	{
		echo create_drop_down( "cbo_machine_name", 150, $sql,"id,machine_name", 1, "-- Select Machine --", 0, "","" );
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_knitting_com
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_knitting_com")
{
	$data = explode("_", $data);
	$company_id = $data[1];
	if ($data[0] == 1)
	{
		echo create_drop_down("cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", "", "load_location(); load_floor(); load_machine();", "");
	}
	else if ($data[0] == 3)
	{
		echo create_drop_down("cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location(); load_floor(); load_machine();");
	}
	else
	{
		echo create_drop_down("cbo_knitting_company", 152, $blank_array, "", 1, "--Select Knit Company--", 0, "");
	}
	exit();
}
if($action=="load_drop_down_knitting_com_new")
{
	extract($data);
	$newData = explode("_",$data);
	  
	$company_id = $newData[0];
	

	if ($newData[1] == 1)
	{
		// echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 AND comp.id = $company_id  order by comp.company_name";
      
		echo create_drop_down("cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", "", "load_location(); load_floor(); load_machine();", "");
	}
	else if ($newData[1] == 3)
	{
	
		$sql = " SELECT a.id, a.supplier_name	 FROM lib_supplier a, lib_supplier_party_type b WHERE a.id = b.supplier_id AND b.party_type = 20 AND a.status_active = 1 GROUP BY a.id, a.supplier_name UNION ALL	SELECT c.id, c.supplier_name	 FROM lib_supplier c, lib_supplier_party_type b, SUBCON_PRODUCTION_MST a  WHERE    c.id = b.supplier_id		  AND b.party_type = 20 AND c.id = a.PARTY_ID	AND c.status_active IN (1, 3) GROUP BY c.id, c.supplier_name ORDER BY supplier_name";
		//echo $sql ;
		echo create_drop_down("cbo_knitting_company", 152, "$sql", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location(); load_floor(); load_machine();");
	}
	else
	{
		echo create_drop_down("cbo_knitting_company", 152, $blank_array, "", 1, "--Select Knit Company--", 0, "");
	}
	exit();
}
/*
|--------------------------------------------------------------------------
| load_drop_down_knitting_com_plan
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_knitting_com_plan")
{
	$data = explode("_", $data);
	$company_id = $data[1];
	echo create_drop_down("cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $company_id, "", "");
}
if ($action == "operator_name_popup") {
	echo load_html_head_contents("Operator Name Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(id) {
			$('#operator_hdn').val(id);
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" name="operator_hdn" id="operator_hdn" value=""/>
	<?
	if($cbo_location_name>0) $location_cond=" and location_id=$cbo_location_name";else $location_cond="";
	 $sql = "select id, first_name from lib_employee where  company_id=$cbo_knitting_company  and status_active=1 and is_deleted=0 $location_cond order by first_name";//$cbo_company_id

	echo create_list_view("tbl_list_search", "Operator Name", "150", "270", "160", 0, $sql, "js_set_value", "id,first_name", "", 1, "0", $arr, "first_name", "", 'setFilterGrid("tbl_list_search",-1);', '0', '', 0);
	exit();
}
if($action=="check_auto_save_variable")
{
	$sql_info ="select id, production_entry,auto_print,apply_for from variable_settings_production where company_name='$data' and variable_list=70 and status_active=1 and is_deleted=0";
	//echo $sql_info;// die;
	$result_dtls = sql_select($sql_info);
	$autoSave=$result_dtls[0]['PRODUCTION_ENTRY'];
	$autoPrint=$result_dtls[0]['AUTO_PRINT'];
	$applyFor=$result_dtls[0]['APPLY_FOR'];
	echo "1"."_".$autoSave."_".$autoPrint."_".$applyFor;
	exit();
}


/*
|--------------------------------------------------------------------------
| production_id_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "production_id_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$ex_data=explode("_",$data);
	?>
	  <script>
		  function js_set_value(id)
		  {
			  document.getElementById('product_id').value=id;
			  parent.emailwindow.hide();
		  }
	  </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
        <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            <tr>
                 <th colspan="5"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="150">Company Name</th>
                <th width="150">Party Name</th>
                <th width="110">Production ID</th>
                <th width="200">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td> <input type="hidden" id="product_id">
                    	<input type="hidden" id="roll_maintain_variable" value="<? echo $ex_data[2]; ?>">
						<?
							echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'subcon_kniting_production_controller', this.value, 'load_drop_down_party_name', 'party_td' );",0 );
                        ?>
                    </td>
                    <td id="party_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $ex_data[1], "" );
                        ?>
                    </td>
                    <td>
                        <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:95px" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('roll_maintain_variable').value, 'kniting_production_id_search_list_view', 'search_div', 'subcon_kniting_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" valign="middle"><?=load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
        </form>
        </div>
        <div id="search_div"></div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

/*
|--------------------------------------------------------------------------
| kniting_production_id_search_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "kniting_production_id_search_list_view")
{
	$data=explode('_',$data);
	$search_type=$data[5];
	$rollMaintainedVariable=$data[6];
	if ($data[0]!=0)
	{
		$company_name=" and a.company_id='".$data[0]."'";
	}
	else
	{
		echo "Please Select Company First.";
		die;
	}
	//if ($data[1]!="" &&  $data[2]!="") $return_date = "and a.product_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $return_date="";

	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and a.product_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $production_date_cond ="";
	}
	else
	{
		if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and a.product_date between '".change_date_format($data[1], "", "",1)."' and '".change_date_format($data[2], "", "",1)."'"; else $production_date_cond ="";
	}
	if ($data[4]!=0) $buyer_cond=" and a.party_id='$data[4]'"; else $buyer_cond="";

	if($search_type==1)
	{
		if ($data[3]!='') $issue_cond=" and a.prefix_no_num='$data[3]'"; else $issue_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($data[3]!='') $issue_cond=" and a.prefix_no_num like  '%$data[3]%'"; else $issue_cond="";
	}
	else if($search_type==2)
	{
		if ($data[3]!='') $issue_cond=" and a.prefix_no_num like  '$data[3]%'"; else $issue_cond="";
	}
	else if($search_type==3)
	{
		if ($data[3]!='') $issue_cond=" and a.prefix_no_num like  '%$data[3]'"; else $issue_cond="";
	}

	if($db_type==0)
	{
		$sql= "select a.id, a.product_no, a.prefix_no_num, year(a.insert_date)as year, a.party_id, a.product_date, a.prod_chalan_no, a.yrn_issu_chalan_no,  a.production_basis, group_concat(distinct(b.order_id)) as order_id, sum(product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.entry_form=159 and a.id=b.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name $buyer_cond $production_date_cond $issue_cond group by a.id order by a.id DESC";
	}
	else if($db_type==2)
	{

		if($rollMaintainedVariable==1)
		{
			$sql= "select a.id, a.product_no, a.prefix_no_num, TO_CHAR(a.insert_date,'YYYY') as year, a.party_id, a.product_date, a.prod_chalan_no, a.yrn_issu_chalan_no, a.production_basis, listagg((cast(b.order_id as varchar2(4000))),',') within group (order by b.order_id) as order_id, sum(c.qnty) as product_qnty
			from subcon_production_mst a, subcon_production_dtls b,subcon_pro_roll_details c
			where a.entry_form=159 and a.id=b.mst_id and b.id=c.dtls_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_name $buyer_cond $production_date_cond $issue_cond
			group by a.id, a.product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no, a.yrn_issu_chalan_no, a.production_basis order by a.id DESC";
		}
		else
		{
			$sql= "select a.id, a.product_no, a.prefix_no_num, TO_CHAR(a.insert_date,'YYYY') as year, a.party_id, a.product_date, a.prod_chalan_no, a.yrn_issu_chalan_no, a.production_basis, listagg((cast(b.order_id as varchar2(4000))),',') within group (order by b.order_id) as order_id, sum(product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.entry_form=159 and a.id=b.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name $buyer_cond $production_date_cond $issue_cond group by a.id, a.product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no, a.yrn_issu_chalan_no, a.production_basis order by a.id DESC";
		}
	}
	//echo $sql;
	/*
	|--------------------------------------------------------------------------
	| query result checking for
	| $sql
	|--------------------------------------------------------------------------
	|
	*/
	$result = sql_select($sql);
	if(empty($result))
	{
		echo get_empty_data_msg();
		die;
	}

	/*
	|--------------------------------------------------------------------------
	| cust_style_ref and order_no
	| array preparing here
	|--------------------------------------------------------------------------
	|
	*/
	$return_to = get_buyer_array();
	$style_ref_arr = array();
	$po_arr = array();
	$orderIdArr = array();
	foreach( $result as $row )
	{
		$order_id=array_unique(explode(",",$row[csf("order_id")]));
		foreach($order_id as $val)
		{
			$orderIdArr[$val] = $val;
		}
	}

	$sqlOrderDtls = "SELECT id, cust_style_ref, order_no FROM subcon_ord_dtls WHERE id IN(".implode(',', $orderIdArr).")";
	$resultOrderDtls = sql_select($sqlOrderDtls);
	foreach( $resultOrderDtls as $row )
	{
		$style_ref_arr[$row[csf("id")]] = $row[csf("cust_style_ref")];
		$po_arr[$row[csf("id")]] = $row[csf("order_no")];
	}
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="867" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Product ID</th>
                <th width="60">Year</th>
                <th width="100">Party </th>
                <th width="70">Date</th>
                <th width="80">Product Challan</th>
                <th width="80">Issue Chalan</th>
                <th width="130">Order No</th>
                <th width="130">Style</th>
                <th>Qty</th>
            </thead>
     	</table>
     	<div style="width:870px; max-height:270px;overflow-y:scroll;">
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="list_view">
        	<?
            $i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				$order_no='';
				$style_ref='';
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="")
						$order_no=$po_arr[$val];
					else
						$order_no.=", ".$po_arr[$val];

					if($style_ref=="")
						$style_ref=$style_ref_arr[$val];
					else
						$style_ref.=", ".$style_ref_arr[$val];
				}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")].'_'.$row[csf("production_basis")]; ?>');">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="60" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="60" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="100"><p><? echo $return_to[$row[csf("party_id")]]; ?></p></td>
						<td width="70"><? echo change_date_format($row[csf("product_date")]); ?></td>
						<td width="80"><p><? echo $row[csf("prod_chalan_no")]; ?></p></td>
                        <td width="80"><? echo $row[csf("yrn_issu_chalan_no")]; ?></td>
                        <td width="130"><p><? echo  $order_no; ?></p></td>
                        <td width="130"><p><? echo $style_ref; ?></p></td>
						<td align="right"><? echo number_format($row[csf("product_qnty")],2,'.',''); ?></td>
					</tr>
				<?
				$i++;
            }
   			?>
			</table>
		</div>
     </div>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| load_php_data_to_form_mst
|--------------------------------------------------------------------------
|
*/
if ($action == "load_php_data_to_form_mst")
{
	$expData = explode('_', $data);
	$nameArray=sql_select( "SELECT id, product_no, company_id, location_id, party_id, product_date, prod_chalan_no, yrn_issu_chalan_no, remarks,knitting_source, knitting_company, knit_location_id, production_basis, program_no FROM subcon_production_mst WHERE id='".$expData[0]."'" );
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_production_id').value 			= '".$row[csf("product_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_kniting_production_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_kniting_production_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_party_name', 'party_td' );\n";
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('txt_production_date').value 			= '".change_date_format($row[csf("product_date")])."';\n";
		echo "document.getElementById('txt_prod_chal_no').value				= '".$row[csf("prod_chalan_no")]."';\n";
		echo "document.getElementById('txt_yarn_issue_challan_no').value	= '".$row[csf("yrn_issu_chalan_no")]."';\n";
		echo "document.getElementById('txt_remarks').value					= '".$row[csf("remarks")]."';\n";
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
	    echo "document.getElementById('cbo_knitting_source').value			= '".$row[csf("knitting_source")]."';\n";
		echo "get_php_form_data( '".$row[csf("company_id")]."','roll_maintained' ,'requires/subcon_kniting_production_controller');\n";

	    echo "load_drop_down( 'requires/subcon_kniting_production_controller', document.getElementById('cbo_knitting_source').value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');\n";
	    echo "document.getElementById('cbo_knitting_company').value			= '".$row[csf("knitting_company")]."';\n";
	    echo "load_location();\n";
	    echo "document.getElementById('cbo_knit_location_name').value		= '".$row[csf("knit_location_id")]."';\n";

		if($row[csf("production_basis")] == 1)
		{
			$row[csf("program_no")] = '';
		}
	    echo "document.getElementById('cbo_production_basis').value			= '".$row[csf("production_basis")]."';\n";
	    echo "document.getElementById('txt_program_no').value				= '".$row[csf("program_no")]."';\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'subcon_kniting_production',1);\n";
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| cons_comp_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "cons_comp_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('comp_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="550" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <th width="150">Company Name</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td> <input type="hidden" id="comp_id">
								<?
									echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data,"",0 );
                                ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'kniting_production_cons_comp_list_view', 'search_div', 'subcon_kniting_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center" valign="top" id=""><div id="search_div"></div></td>
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

/*
|--------------------------------------------------------------------------
| kniting_production_cons_comp_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "kniting_production_cons_comp_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and comapny_id='$data[0]'"; else { echo "Please Select Company First."; die; }

	$sql= "select id,comapny_id,body_part,const_comp,gsm,yarn_description,in_house_rate,uom_id,status_active from lib_subcon_charge where is_deleted=0 and rate_type_id=2 $company_name";

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$body_part,4=>$unit_of_measurement);

	echo  create_list_view ( "list_view", "Body Part,Construction & Composition,GSM,Yarn Description,UOM", "100,160,60,120,60","550","220",1, $sql, "js_set_value", "id","", 1, "body_part,0,0,0,uom_id", $arr , "body_part,const_comp,gsm,yarn_description,uom_id", "subcon_kniting_production_controller", 'setFilterGrid("list_view",-1);','0,0,2,0,0' ) ;
	exit();
}

/*
|--------------------------------------------------------------------------
| load_php_data_to_form_comp
|--------------------------------------------------------------------------
|
*/
if ($action == "load_php_data_to_form_comp")
{
	$nameArray=sql_select( "select id,const_comp,gsm from lib_subcon_charge where id='$data'" );
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_febric_description').value 			= '".$row[csf("const_comp")]."';\n";
	    echo "document.getElementById('txt_gsm').value            				= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('hidd_comp_id').value            			= '".$row[csf("id")]."';\n";
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| kniting_production_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "kniting_production_list_view")
{
	//echo $data;
	$expData = explode('_',$data);

	if($expData[1] == 1)
	{
		$sql = "
			SELECT
                spm.production_basis,
				spd.id, spd.mst_id, spd.process, spd.fabric_description, spd.gsm, spd.dia_width, spd.no_of_roll, spd.product_qnty,spd.product_qnty_pcs, spd.yarn_lot, spd.yrn_count_id, spd.machine_id,
                sod.order_no
            FROM
                subcon_production_mst spm
                INNER JOIN subcon_production_dtls spd ON spm.id = spd.mst_id
                INNER JOIN subcon_ord_dtls sod ON spd.order_id = sod.id
            WHERE
                spm.id = '".$expData[0]."'
				--AND spm.entry_form = 159
                --AND spm.production_basis = 1
                AND spm.status_active = 1
                AND spm.is_deleted = 0
                AND spd.status_active = 1
                AND spd.is_deleted = 0
                AND sod.status_active = 1
                AND sod.is_deleted = 0
		";
	}
	else
	{
		/*$sql = "
			SELECT
                spm.production_basis,
				spd.id, spd.mst_id, spd.process, spd.fabric_description, spd.gsm, spd.dia_width, spd.no_of_roll, spd.product_qnty, spd.yarn_lot, spd.yrn_count_id, spd.machine_id,
				sod.order_no
            FROM
                subcon_production_mst spm
                INNER JOIN subcon_production_dtls spd ON spm.id=spd.mst_id
                INNER JOIN subcon_planning_plan_dtls sppd ON spd.order_id=sppd.po_id
                INNER JOIN subcon_ord_dtls sod ON sppd.po_id = sod.id
            WHERE
                spm.id = '".$expData[0]."'
                AND spm.entry_form = 159
                AND spm.production_basis = 2
                AND spm.status_active = 1
                AND spm.is_deleted = 0
                AND spd.status_active = 1
                AND spd.is_deleted = 0
                AND sppd.status_active = 1
                AND sppd.is_deleted = 0
                group by spm.production_basis, spd.id, spd.mst_id, spd.process, spd.fabric_description, spd.gsm, spd.dia_width, spd.no_of_roll, spd.product_qnty, spd.yarn_lot, spd.yrn_count_id, spd.machine_id, sod.order_no
		";*/

		if($expData[3]==1)
		{

			$sql = "SELECT
                a.production_basis,
                b.id, b.mst_id, b.process, b.fabric_description, b.gsm, b.dia_width, b.no_of_roll, sum(c.qnty) as  product_qnty,b.product_qnty_pcs, b.yarn_lot, b.yrn_count_id, b.machine_id,d.order_no,a.company_id
            FROM
                subcon_production_mst a,subcon_production_dtls b,subcon_pro_roll_details c,subcon_ord_dtls d

            WHERE
                a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and
                a.id = '".$expData[0]."'
                AND a.entry_form = 159 AND c.entry_form = 159
                AND a.production_basis = 2
                AND a.status_active = 1
                AND a.is_deleted = 0
                AND b.status_active = 1
                AND b.is_deleted = 0
                AND d.status_active = 1
                AND d.is_deleted = 0
                group by a.production_basis, b.id, b.mst_id, b.process, b.fabric_description, b.gsm, b.dia_width, b.no_of_roll, b.yarn_lot, b.yrn_count_id, b.machine_id, d.order_no,b.product_qnty_pcs,a.company_id";
		}
		else
		{
			$sql = "
			SELECT
                a.production_basis,
				b.id, b.mst_id, b.process, b.fabric_description, b.gsm, b.dia_width, b.no_of_roll, b.product_qnty,b.product_qnty_pcs, b.yarn_lot, b.yrn_count_id, b.machine_id,e.order_no,a.company_id
            FROM
                subcon_production_mst a,subcon_production_dtls b,subcon_planning_plan_dtls d,subcon_ord_dtls e

            WHERE
            	a.id=b.mst_id and b.order_id=d.po_id and d.po_id = e.id and
                a.id = '".$expData[0]."'
                AND a.entry_form = 159
                AND a.production_basis = 2
                AND a.status_active = 1
                AND a.is_deleted = 0
                AND b.status_active = 1
                AND b.is_deleted = 0
                AND d.status_active = 1
                AND d.is_deleted = 0
                group by a.production_basis, b.id, b.mst_id, b.process, b.fabric_description, b.gsm, b.dia_width, b.no_of_roll, b.product_qnty,b.product_qnty_pcs, b.yarn_lot, b.yrn_count_id, b.machine_id, e.order_no,a.company_id
			";
		}

	}

	/*$sql = "
		SELECT
			spm.production_basis,
			spd.id, spd.mst_id, spd.process, spd.fabric_description, spd.gsm, spd.dia_width, spd.no_of_roll, spd.product_qnty, spd.yarn_lot, spd.yrn_count_id, spd.machine_id,
			sod.order_no
		FROM
			subcon_production_mst spm
			INNER JOIN subcon_production_dtls spd ON spm.id = spd.mst_id
			INNER JOIN subcon_ord_dtls sod ON spd.order_id = sod.id
		WHERE
			spm.id = '".$expData[0]."'
			--AND spm.entry_form = 159
			--AND spm.production_basis = 1
			AND spm.status_active = 1
			AND spm.is_deleted = 0
			AND spd.status_active = 1
			AND spd.is_deleted = 0
			AND sod.status_active = 1
			AND sod.is_deleted = 0
	";	*/

	/*$sql = "
		SELECT
			a.production_basis,
			b.id, b.mst_id, b.process, b.fabric_description, b.gsm, b.dia_width, b.no_of_roll, b.product_qnty, b.yarn_lot, b.yrn_count_id, b.machine_id,
			c.order_no
		FROM
			subcon_production_mst a
			INNER JOIN subcon_production_dtls b ON a.id = b.mst_id
			INNER JOIN subcon_ord_dtls c ON b.order_id = c.id
		WHERE
			a.id = '".$expData[0]."'
			--AND a.entry_form = 159
			--AND a.production_basis = 1
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.status_active = 1
			AND c.is_deleted = 0
	";	*/


	//echo $sql;

	/*
	|--------------------------------------------------------------------------
	| query result checking for
	| $sql
	|--------------------------------------------------------------------------
	|
	*/
	$result = sql_select($sql);
	if(empty($result))
	{
		echo get_empty_data_msg();
		die;
	}

	$yearn_count_arr = get_yarn_count_array();
	$machine_arr = get_machine_array();
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1017" class="rpt_table" style="float: left;">
        <thead>
            <th width="30">SL</th>
            <th width="70">Process</th>
            <th width="100">Order</th>
            <th width="150">Cont. Composition </th>
            <th width="60">GSM</th>
            <th width="60">Dia Width</th>
            <th width="50">No of Roll</th>
            <th width="80">Prod Qty</th>
			<th width="80">Prod Qty(Pcs)</th>
            <th width="80">Y-Lot</th>
            <th width="130">Y-Count</th>
            <th>Mac-No</th>
        </thead>
    </table>
    <div style="width:1020px; max-height:270px;overflow-y:scroll;float: left;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="list_view" style="float: left;">
		<?
        $i=1;
        foreach( $result as $row )
        {
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

            $yarn_count=array_unique(explode(",",$row[csf('yrn_count_id')]));
            $count_name="";
            foreach($yarn_count as $val)
            {
                if($count_name=="") $count_name=$yearn_count_arr[$val]; else $count_name.=", ".$yearn_count_arr[$val];
            }
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" valign="middle" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf("id")]."_".$row[csf("production_basis")].'_'.$expData[2]."_".$row[csf("company_id")]; ?>','load_php_data_to_form_dtls','requires/subcon_kniting_production_controller'); put_data_dtls_part('<? echo $row[csf('mst_id')]; ?>','', 'requires/subcon_kniting_production_controller');" >

                <td width="30" align="center"><? echo $i; ?></td>
                <td width="70"><? echo $conversion_cost_head_array[$row[csf("process")]]; ?></td>
                <td width="100" align="center"><p><? echo $row[csf("order_no")]; ?></p></td>
                <td width="150"><p><? echo $row[csf("fabric_description")]; ?></p></td>
                <td width="60" align="center"><? echo $row[csf("gsm")]; ?></td>
                <td width="60" align="center"><p><? echo $row[csf("dia_width")]; ?></p></td>
                <td width="50" align="right"><? echo $row[csf("no_of_roll")]; ?></td>
                <td width="80" align="right"><? echo number_format($row[csf("product_qnty")],2,'.',''); ?></td>
				<td width="80" align="right"><? echo number_format($row[csf("product_qnty_pcs")],2,'.',''); ?></td>
                <td width="80"><p><? echo $row[csf("yarn_lot")]; ?></p></td>
                <td width="130"><p><? echo $count_name; ?></p></td>
                <td><? echo $machine_arr[$row[csf("machine_id")]]; ?></td>
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

/*
|--------------------------------------------------------------------------
| load_php_data_to_form_dtls
|--------------------------------------------------------------------------
|
*/
if ($action == "load_php_data_to_form_dtls")
{
	$color_arr = get_color_array();
	$expData = explode('_',$data);
	$roll_maintained = return_field_value("a.roll_maintained as roll_maintained", "subcon_production_mst a, subcon_production_dtls b ", " a.entry_form=159 and a.id=b.mst_id and b.id='".$expData[0]."'", "roll_maintained");
	//$count_arr = get_yarn_count_array();
	//1=1201_1_1
	//echo $roll_maintained.'='.$data; die;
	//echo $roll_maintained;
	if($expData[1] == 1)
	{
		$productionBasisCondition = " AND spm.production_basis = 1 ";

		//$nameArray=sql_select( "select a.id, a.process, a.fabric_description, a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type,a.machine_dia,a.machine_gg, a.no_of_roll, a.product_qnty, a.reject_qnty, a.uom_id, a.yarn_lot, a.yrn_count_id, a.floor_id, a.machine_id, a.brand,a.shift, a.stitch_len, a.color_range, a.color_id, a.remarks, a.status_active, b.id as order_tbl_id, b.job_no_mst, b.order_no, b.order_uom, b.main_process_id from subcon_production_dtls a,subcon_ord_dtls b where a.order_id=b.id and a.id='".$expData[0]."'" );

		/*$sql = "
			SELECT
                spm.production_basis,
				spd.id, spd.process, spd.fabric_description, spd.cons_comp_id, spd.gsm, spd.dia_width, spd.dia_width_type,spd.machine_dia, spd.machine_gg, spd.no_of_roll, spd.product_qnty, spd.reject_qnty, spd.uom_id, spd.yarn_lot, spd.yrn_count_id, spd.floor_id, spd.machine_id, spd.brand, spd.shift, spd.stitch_len, spd.color_range, spd.color_id, spd.remarks, spd.status_active,
				sod.id as order_tbl_id, sod.job_no_mst, sod.order_no, sod.order_uom, sod.main_process_id
            FROM
                subcon_production_mst spm
                INNER JOIN subcon_production_dtls spd ON spm.id = spd.mst_id
                INNER JOIN subcon_ord_dtls sod ON spd.order_id = sod.id
            WHERE
                spm.id = '".$expData[0]."'
				--AND spm.entry_form = 159
                --AND spm.production_basis = 1
                AND spm.status_active = 1
                AND spm.is_deleted = 0
                AND spd.status_active = 1
                AND spd.is_deleted = 0
                AND sod.status_active = 1
                AND sod.is_deleted = 0
		";*/







		$sql = "
		SELECT
			spm.COMPANY_ID,   spm.production_basis,spm.location_id,spm.party_id,spm.knitting_source,spm.knitting_company,spm.knit_location_id,
			spd.id, spd.process, spd.fabric_description, spd.cons_comp_id, spd.gsm, spd.dia_width, spd.dia_width_type,spd.machine_dia, spd.machine_gg, spd.no_of_roll, spd.product_qnty, spd.reject_qnty, spd.uom_id, spd.yarn_lot, spd.yrn_count_id, spd.floor_id, spd.machine_id, spd.brand, spd.shift, spd.stitch_len,spd.operator_name, spd.color_range, spd.color_id, spd.remarks, spd.status_active,
			sod.id as order_tbl_id, sod.job_no_mst, sod.order_no,sod.order_quantity, sod.order_uom, sod.main_process_id,spd.product_qnty_pcs
		FROM
			subcon_production_mst spm
			INNER JOIN subcon_production_dtls spd ON spm.id = spd.mst_id
			INNER JOIN subcon_ord_dtls sod ON spd.order_id = sod.id
		WHERE
			spd.id = '".$expData[0]."'
			AND spm.entry_form = 159
			AND spm.production_basis = 1
			AND spm.status_active = 1
			AND spm.is_deleted = 0
			AND spd.status_active = 1
			AND spd.is_deleted = 0
			AND sod.status_active = 1
			AND sod.is_deleted = 0
			".$productionBasisCondition."
		";
	}
	else
	{

		if($roll_maintained==1)
		{
			$productionBasisCondition = " AND a.production_basis = 2 ";
			/*$sql = "
				SELECT
	                spm.production_basis,spm.location_id,spm.party_id,spm.knitting_source,spm.knitting_company,spm.knit_location_id,
					spd.id, spd.process, spd.fabric_description, spd.cons_comp_id, spd.gsm, spd.dia_width, spd.dia_width_type,spd.machine_dia, spd.machine_gg, spd.no_of_roll, spd.product_qnty, spd.reject_qnty, spd.uom_id, spd.yarn_lot, spd.yrn_count_id, spd.floor_id, spd.machine_id, spd.brand, spd.shift, spd.stitch_len, spd.color_range, spd.color_id, spd.remarks, spd.status_active,
	                sppd.po_id AS order_tbl_id,
					sm.job_no AS job_no_mst,
					sod.main_process_id, sod.order_no
	            FROM
	                subcon_production_mst spm
	                INNER JOIN subcon_production_dtls spd ON spm.id=spd.mst_id
	                INNER JOIN subcon_ord_dtls sod ON spd.order_id=sod.id
	                INNER JOIN subcon_planning_plan_dtls sppd ON sod.id=sppd.po_id
	                INNER JOIN subcon_planning_mst sm ON sppd.mst_id=sm.id
	            WHERE
	                spd.id = '".$expData[0]."'
	                AND spm.entry_form = 159
	                AND spm.production_basis = 2
	                AND spm.status_active = 1
	                AND spm.is_deleted = 0
	                AND spd.status_active = 1
	                AND spd.is_deleted = 0
	                AND sppd.status_active = 1
	                AND sppd.is_deleted = 0
	                group by spm.production_basis,spm.location_id,spm.party_id,spm.knitting_source,spm.knitting_company,spm.knit_location_id, spd.id, spd.process, spd.fabric_description, spd.cons_comp_id, spd.gsm, spd.dia_width, spd.dia_width_type,spd.machine_dia, spd.machine_gg, spd.no_of_roll,spd.product_qnty, spd.reject_qnty, spd.uom_id, spd.yarn_lot, spd.yrn_count_id, spd.floor_id, spd.machine_id, spd.brand, spd.shift, spd.stitch_len, spd.color_range, spd.color_id, spd.remarks, spd.status_active, sppd.po_id, sm.job_no, sod.main_process_id, sod.order_no
			";*/

			$sql = "
				SELECT a.COMPANY_ID,
	                a.production_basis,a.location_id,a.party_id,a.knitting_source,a.knitting_company,a.knit_location_id,
					b.id, b.process, b.fabric_description, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type,b.machine_dia, b.machine_gg, b.no_of_roll, b.product_qnty, b.reject_qnty, b.uom_id, b.yarn_lot, b.yrn_count_id, b.floor_id, b.machine_id, b.brand, b.shift, b.stitch_len, b.color_range, b.color_id, b.remarks, b.status_active,b.operator_name,
	                d.po_id AS order_tbl_id,d.program_qnty as order_quantity,
					e.job_no AS job_no_mst,
					c.main_process_id, c.order_no,b.product_qnty_pcs,c.order_uom
	            FROM
	                subcon_production_mst a,
	                subcon_production_dtls b,
	                subcon_ord_dtls c,
	                subcon_planning_plan_dtls d ,
	                subcon_planning_mst e
	            WHERE
	                b.id = '".$expData[0]."'
	                AND a.id=b.mst_id and c.id=b.order_id and c.id=d.po_id and d.mst_id=e.id
	                AND a.entry_form = 159
	                AND a.production_basis = 2
	                AND a.status_active = 1
	                AND a.is_deleted = 0
	                AND b.status_active = 1
	                AND b.is_deleted = 0
	                AND d.status_active = 1
	                AND d.is_deleted = 0
	                group by  a.COMPANY_ID, a.production_basis,a.location_id,a.party_id,a.knitting_source,a.knitting_company,a.knit_location_id, b.id, b.process, b.fabric_description, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type,b.machine_dia, b.machine_gg, b.no_of_roll,b.product_qnty, b.reject_qnty, b.uom_id, b.yarn_lot, b.yrn_count_id, b.floor_id, b.machine_id, b.brand, b.shift, b.stitch_len, b.color_range, b.color_id, b.remarks, b.status_active,b.operator_name, d.po_id,d.program_qnty, e.job_no, c.main_process_id, c.order_no ,b.product_qnty_pcs,c.order_uom
			";
			//AND d.dtls_id=$expData[2]

			$productionQntySql=sql_select("SELECT b.id as dtls_id, sum(c.qnty) as  product_qnty, sum(c.qc_pass_qnty_pcs) as  product_qnty_pcs,c.product_size FROM subcon_production_mst a,subcon_production_dtls b,subcon_pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.id = '".$expData[0]."' AND a.entry_form = 159 AND c.entry_form = 159 AND a.production_basis = 2 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0  group by b.id,c.product_size");
			foreach ($productionQntySql as $row)
			{
				$productionQntyArr[$row[csf("dtls_id")]]["product_qnty"]+=$row[csf("product_qnty")];
				$productionQntyArr[$row[csf("dtls_id")]]["product_qnty_pcs"]+=$row[csf("product_qnty_pcs")];
				$productionQntyArr[$row[csf("dtls_id")]]["product_size"]=$row[csf("product_size")];
			}

		}
		else
		{

			$productionBasisCondition = " AND spm.production_basis = 2 ";
			$sql = "
				SELECT
				    spm.COMPANY_ID,
	                spm.production_basis,spm.location_id,spm.party_id,spm.knitting_source,spm.knitting_company,spm.knit_location_id,
					spd.id, spd.process, spd.fabric_description, spd.cons_comp_id, spd.gsm, spd.dia_width, spd.dia_width_type,spd.machine_dia, spd.machine_gg, spd.no_of_roll, spd.product_qnty, spd.reject_qnty, spd.uom_id, spd.yarn_lot, spd.yrn_count_id, spd.floor_id, spd.machine_id, spd.brand, spd.shift, spd.stitch_len, spd.color_range, spd.color_id, spd.remarks, spd.status_active,
	                sppd.po_id AS order_tbl_id,
					sm.job_no AS job_no_mst,
					sod.main_process_id, sod.order_no,
					spd.product_qnty_pcs
	            FROM
	                subcon_production_mst spm
	                INNER JOIN subcon_production_dtls spd ON spm.id=spd.mst_id
	                INNER JOIN subcon_ord_dtls sod ON spd.order_id=sod.id
	                INNER JOIN subcon_planning_plan_dtls sppd ON sod.id=sppd.po_id
	                INNER JOIN subcon_planning_mst sm ON sppd.mst_id=sm.id
	            WHERE
	                spd.id = '".$expData[0]."'
	                AND spm.entry_form = 159
	                AND spm.production_basis = 2
	                AND spm.status_active = 1
	                AND spm.is_deleted = 0
	                AND spd.status_active = 1
	                AND spd.is_deleted = 0
	                AND sppd.status_active = 1
	                AND sppd.is_deleted = 0
	                group by spm.COMPANY_ID, spm.production_basis,spm.location_id,spm.party_id,spm.knitting_source,spm.knitting_company,spm.knit_location_id, spd.id, spd.process, spd.fabric_description, spd.cons_comp_id, spd.gsm, spd.dia_width, spd.dia_width_type,spd.machine_dia, spd.machine_gg, spd.no_of_roll,spd.product_qnty, spd.reject_qnty, spd.uom_id, spd.yarn_lot, spd.yrn_count_id, spd.floor_id, spd.machine_id, spd.brand, spd.shift, spd.stitch_len, spd.color_range, spd.color_id, spd.remarks, spd.status_active, sppd.po_id, sm.job_no, sod.main_process_id, sod.order_no ,spd.product_qnty_pcs
			";
		}
	}

	 /*$sql = "
		SELECT
			spm.production_basis,
			spd.id, spd.process, spd.fabric_description, spd.cons_comp_id, spd.gsm, spd.dia_width, spd.dia_width_type,spd.machine_dia, spd.machine_gg, spd.no_of_roll, spd.product_qnty, spd.reject_qnty, spd.uom_id, spd.yarn_lot, spd.yrn_count_id, spd.floor_id, spd.machine_id, spd.brand, spd.shift, spd.stitch_len,spd.operator_name, spd.color_range, spd.color_id, spd.remarks, spd.status_active,
			sod.id as order_tbl_id, sod.job_no_mst, sod.order_no,sod.order_quantity, sod.order_uom, sod.main_process_id
		FROM
			subcon_production_mst spm
			INNER JOIN subcon_production_dtls spd ON spm.id = spd.mst_id
			INNER JOIN subcon_ord_dtls sod ON spd.order_id = sod.id
		WHERE
			spd.id = '".$expData[0]."'
			--AND spm.entry_form = 159
			--AND spm.production_basis = 1
			AND spm.status_active = 1
			AND spm.is_deleted = 0
			AND spd.status_active = 1
			AND spd.is_deleted = 0
			AND sod.status_active = 1
			AND sod.is_deleted = 0
			".$productionBasisCondition."
	";*/

	//echo $sql; //die;


	$nameArray=sql_select($sql);

	if(count($nameArray)==0){
		echo "document.getElementById('update_id_dtl').value= '".$expData[0]."';\n";
	}
	foreach ($nameArray as $row)
	{

		if($expData[1] == 2 && $roll_maintained==1)
		{
			$productionQnty=$productionQntyArr[$row[csf("id")]]["product_qnty"];
			$product_qnty_pcs=$productionQntyArr[$row[csf("id")]]["product_qnty_pcs"];
			$product_size=$productionQntyArr[$row[csf("id")]]["product_size"];
			$balanceQnty=$row[csf("order_quantity")]-$productionQnty;
		}
		else
		{
			$productionQnty=$row[csf("product_qnty")];
			$product_qnty_pcs=$row[csf("product_qnty_pcs")];
			$product_size=$row[csf("product_size")];
		}

		echo "document.getElementById('cbo_location_name').value		 		= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_party_name').value		 			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_knitting_source').value		 		= '".$row[csf("knitting_source")]."';\n";
		echo "document.getElementById('cbo_knitting_company').value		 		= '".$row[csf("knitting_company")]."';\n";
		echo "load_location();\n";
		echo "document.getElementById('cbo_knit_location_name').value		 	= '".$row[csf("knit_location_id")]."';\n";

		echo "document.getElementById('cbo_process').value		 				= '".$row[csf("process")]."';\n";
		echo "document.getElementById('txt_febric_description').value		 	= '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('hidd_comp_id').value		 				= '".$row[csf("cons_comp_id")]."';\n";
		echo "document.getElementById('txt_gsm').value							= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_width').value						= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('cbo_dia_width_type').value				= '".$row[csf("dia_width_type")]."';\n";
		echo "document.getElementById('txt_machine_dia').value					= '".$row[csf("machine_dia")]."';\n";
		echo "document.getElementById('txt_machine_gg').value					= '".$row[csf("machine_gg")]."';\n";
		if($row[csf('operator_name')]!="")
		{
			$operatorName = return_field_value("first_name", "lib_employee", "id=" . $row[csf('operator_name')]);
		}

		echo "document.getElementById('txt_operator_id').value					= '".$row[csf("operator_name")]."';\n";
		echo "document.getElementById('txt_operator_name').value				= '".$operatorName."';\n";
		echo "document.getElementById('txt_roll_qnty').value					= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('txt_order_no').value		 				= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txtOrderQty').value		 				= '".$row[csf("order_quantity")]."';\n";
		echo "document.getElementById('order_no_id').value		 				= '".$row[csf("order_tbl_id")]."';\n";
		echo "document.getElementById('txt_operator_id').value		 			= '".$row[csf("operator_name")]."';\n";
		echo "document.getElementById('process_id').value		 				= '".$row[csf("main_process_id")]."';\n";
		echo "document.getElementById('txt_product_qnty').value		 			= '".number_format($productionQnty,3)."';\n";
		echo "document.getElementById('txt_product_qnty_pcs').value		 			= '".number_format($product_qnty_pcs,3)."';\n";
		echo "document.getElementById('hdnProductQty').value		 			= '".$productionQnty."';\n";
		//echo "document.getElementById('txtsize').value		 					= '".$product_size."';\n";
		echo "document.getElementById('txt_reject_qnty').value            		= '".$row[csf("reject_qnty")]."';\n";
		echo "document.getElementById('cbo_uom').value		 					= '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('txt_yarn_lot').value						= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('cbo_yarn_count').value					= '".$row[csf("yrn_count_id")]."';\n";
		echo "set_multiselect('cbo_yarn_count','0','1','".$row[csf("yrn_count_id")]."','0');\n";
		echo "document.getElementById('txt_brand').value						= '".$row[csf("brand")]."';\n";
		echo "document.getElementById('cbo_shift_id').value		 				= '".$row[csf("shift")]."';\n";
		echo "load_floor();\n";
		echo "document.getElementById('cbo_floor_id').value		 				= '".$row[csf("floor_id")]."';\n";
		echo "load_machine();\n";
		echo "document.getElementById('cbo_machine_name').value		 			= '".$row[csf("machine_id")]."';\n";
		echo "document.getElementById('txt_job_no').value		 				= '".$row[csf("job_no_mst")]."';\n";
		echo "document.getElementById('txtBalanceQty').value		 			= '".$row[csf("balanceQnty")]."';\n";
	    $company=$row['COMPANY_ID'] ;
		$source = $row[csf("knitting_source")] ;
		echo "load_drop_down( 'requires/subcon_kniting_production_controller', '".$company."_" .$source."', 'load_drop_down_knitting_com_new', 'knitting_com' );\n";
		$save_string = '';
		if ($roll_maintained == 1)
		{
			$data_roll_array = sql_select("select id,roll_used, po_breakdown_id, qnty,product_size,roll_no, barcode_no, reject_qnty,qc_pass_qnty_pcs from subcon_pro_roll_details where dtls_id='".$expData[0]."' and entry_form=159 and status_active=1 and is_deleted=0 order by id");
			foreach ($data_roll_array as $row_roll)
			{
				if ($row_roll[csf('roll_used')] == 1)
					$roll_id = $row_roll[csf('id')];
				else
					$roll_id = 0;

					if ($row_roll[csf('reject_qnty')] == 0 || $row_roll[csf('reject_qnty')] == "") $row_roll[csf('reject_qnty')]=0;



				if ($save_string == "")
				{
					$save_string = $row_roll[csf("qnty")] . "_" . $row_roll[csf("reject_qnty")]."_" . $row_roll[csf("po_breakdown_id")] . "_" . $row_roll[csf("roll_no")] . "_" . $row_roll[csf("barcode_no")] . "_" . $row_roll[csf("id")]. "_" . $roll_id. "_" . $row_roll[csf("qc_pass_qnty_pcs")]. "_" . $row_roll[csf("product_size")];;
				}
				else
				{
					$save_string .= "," . $row_roll[csf("qnty")] . "_" . $row_roll[csf("reject_qnty")]."_" . $row_roll[csf("po_breakdown_id")] . "_" . $row_roll[csf("roll_no")] . "_" . $row_roll[csf("barcode_no")] . "_" . $row_roll[csf("id")]. "_" . $roll_id. "_" . $row_roll[csf("qc_pass_qnty_pcs")]. "_" . $row_roll[csf("product_size")];
				}
			}
		}
		echo "document.getElementById('save_data').value 						= '" . $save_string . "';\n";
		echo "document.getElementById('txt_deleted_id').value 					= '';\n";
		echo "document.getElementById('txt_stitch_len').value		 			= '".$row[csf("stitch_len")]."';\n";
		echo "document.getElementById('cbo_color_range').value		 			= '".$row[csf("color_range")]."';\n";
		echo "document.getElementById('txt_color').value		 				= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('hdnColorId').value		 				= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('text_new_remarks').value            		= '".$row[csf("remarks")]."';\n";


		//if($row[csf("process")] == 1)
		if($row[csf("main_process_id")] == 2)
		{
			//$sqlDelivery = "SELECT SUM(delivery_qty) AS delivery_qty FROM subcon_delivery_dtls WHERE status_active =1 AND is_deleted = 0 AND order_id = ".$row[csf("order_tbl_id")]." AND item_id = ".$row[csf("cons_comp_id")]." AND gsm = ".$row[csf("gsm")]." AND dia = ".$row[csf("dia_width")]."";
			$sqlDelivery = "SELECT SUM(delivery_qty) AS delivery_qty FROM subcon_delivery_dtls WHERE status_active =1 AND is_deleted = 0 AND order_id = ".$row[csf("order_tbl_id")]." AND item_id = ".$row[csf("cons_comp_id")]." AND gsm = ".$row[csf("gsm")]." AND dia = ".$row[csf("dia_width")]." AND width_dia_type = ".$row[csf("dia_width_type")]." AND color_id = ".$row[csf("color_id")]." AND yarn_lot = ".$row[csf("yarn_lot")]."";
			//echo $sqlDelivery;
			$resultsetDelivery=sql_select($sqlDelivery);
			if(!empty($resultsetDelivery))
			{
				if($resultsetDelivery[0][csf('delivery_qty')] >= $productionQnty)
				{
					echo "document.getElementById('hdnisNextProcess').value = '1';\n";
				}
				else
				{
					echo "document.getElementById('hdnisNextProcess').value = '0';\n";
				}
			}
			else
			{
				echo "document.getElementById('hdnisNextProcess').value = '0';\n";
			}
		}
		else
		{
			$sqlBatch = "SELECT SUM(b.batch_qnty) AS batch_qnty FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id = b.mst_id AND a.status_active =1 AND a.is_deleted = 0 AND b.status_active =1 AND b.is_deleted = 0 AND a.entry_form = 36 AND b.po_id = ".$row[csf("order_tbl_id")]."";
			$resultsetBatch=sql_select($sqlBatch);
			if(!empty($resultsetBatch))
			{
				if($resultsetBatch[0][csf('batch_qnty')] >= $productionQnty)
				{
					echo "document.getElementById('hdnisNextProcess').value = '1';\n";
				}
				else
				{
					echo "document.getElementById('hdnisNextProcess').value = '0';\n";
				}
			}
			else
			{
				echo "document.getElementById('hdnisNextProcess').value = '0';\n";
			}
		}


		//echo "show_list_view(document.getElementById('order_no_id').value+'_'+document.getElementById('process_id').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_kniting_production_controller','');\n";
		echo "show_list_view('".$row[csf("order_tbl_id")]."_".$row[csf("main_process_id")]."_".$row[csf("production_basis")]."_".$expData[2]."_".$expData[3]."_".$expData[0]."', 'show_fabric_desc_listview', 'list_fabric_desc_container', 'requires/subcon_kniting_production_controller', '');\n";
		echo "document.getElementById('update_id_dtl').value= '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'subcon_kniting_production',1);\n";
	}

	exit();
}

/*if ($action == "is_machine_no_or_not")
{
	$nameArray=sql_select( "select id,machine_no from lib_machine_name where machine_no=$data" );
	if(!$nameArray){echo "1";}
	exit();
}
*/

/*
|--------------------------------------------------------------------------
| order_no_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	//$data=explode('_',$data);
	//echo $data[1];
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
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search Job</th>
                            <th width="100">Search Order</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td> <input type="hidden" id="selected_job">
								<?
									$data=explode("_",$data);
									echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1);
                                ?>
                            </td>
                            <td>
								<? echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[1], "",1 );
								?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td>
                            <td >
                                 <input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Job" />
                            </td>
                            <td align="center" id="search_by_td">
                                <input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Order" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('cbo_string_search_type').value, 'job_search_list_view', 'search_div', 'subcon_kniting_production_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" />
                            </td>

                        </tr>
                        <tr>
                            <td colspan="6" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="search_div"></div>
            </form>
        </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| job_search_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "job_search_list_view")
{
	$data=explode('_',$data);
	$search_job=str_replace("'","",$data[4]);
	$search_order=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];

	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else  $company="";
	if ($data[1]!=0) $buyer=" and party_id='$data[1]'"; else $buyer="";

	if($search_type==1)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num='$search_job'";
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no='$search_order'";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '%$search_job%'";
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '%$search_order%'";
	}
	else if($search_type==2)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '$search_job%'";
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '$search_order%'";
	}
	else if($search_type==3)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '%$search_job'";
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '%$search_order'";
	}

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}

	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($db_type==0)
	{
		$sql= "select b.id as ord_id, a.subcon_job, a.job_no_prefix_num, year(a.insert_date)as year, b.process_id, a.party_id, b.order_no, b.delivery_date, b.cust_style_ref, b.main_process_id, b.order_quantity, b.order_uom from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond order by a.id DESC";
	}
	else if($db_type==2)
	{
		$sql= "select b.id as ord_id, a.subcon_job, a.job_no_prefix_num, TO_CHAR(a.insert_date,'YYYY') as year, b.process_id, a.party_id, b.order_no, b.delivery_date, b.cust_style_ref, b.main_process_id, b.order_quantity, b.order_uom from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond order by a.id DESC";
	}
	//echo $sql; die;
?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="768" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="50">Job </th>
                <th width="60">Year</th>
                <th width="130">Style No</th>
                <th width="100">PO No</th>
                <th width="100">Process</th>
                <th width="80">PO Quantity</th>
                <th width="40">UOM</th>
                <th>Delivery Date</th>
            </thead>
        </table>
        <div style="width:768px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$processid=explode(",",$selectResult[csf('process_id')]);
					$knit_array=array(1,3,4);
					//$query_arr=array_intersect($knit_array,$processid);
					//print_r ($query_arr);
					if(array_intersect($knit_array,$processid))
					{
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('ord_id')]; ?>)">
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="50" align="center"><p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="60" align="center"><? echo $selectResult[csf('year')]; ?></td>
                        <td width="130"><p><? echo $selectResult[csf('cust_style_ref')]; ?></p></td>
                        <td width="100"><p><? echo $selectResult[csf('order_no')]; ?></p></td>
                        <td width="100"><p><? echo $production_process[$selectResult[csf('main_process_id')]]; ?></p></td>
                        <td width="80" align="right"><? echo number_format( $selectResult[csf('order_quantity')],2); ?>&nbsp;</td>
                        <td width="40" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
                        <td align="center"><? echo change_date_format($selectResult[csf('delivery_date')]); ?></td>
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

/*
|--------------------------------------------------------------------------
| actn_browse_plan
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_browse_plan")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}

		function fnc_show()
		{
			if($("#txt_search_job").val().trim() =="" && $("#txt_search_order").val().trim() =="" && $("#txt_search_programNo").val().trim() =="" &&  ($("#txt_date_from").val().trim() =="" || $("#txt_date_to").val().trim() =="") )
			{
				alert("please select any search criteria.");
				return;
			}
			show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_programNo').value, 'actn_plan_list_view', 'search_div', 'subcon_kniting_production_controller', 'setFilterGrid(\'tbl_list_search\',-1)')
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="150">Date Range</th>
                            <th width="100">Search Job</th>
                            <th width="100">Search Order</th>
                            <th width="100">Program No</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="hidden" id="selected_job">
								<?
									$data=explode("_",$data);
									echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1);
                                ?>
                            </td>
                            <td>
								<? echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --","0", "","" );
								?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td>
                            <td>
                                 <input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Job" />
                            </td>
                            <td align="center" id="search_by_td">
                                <input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Order" />
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_programNo" id="txt_search_programNo" class="text_boxes" style="width:100px" placeholder="Program" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_show();" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1); ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="search_div"></div>
            </form>
        </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| actn_plan_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_plan_list_view")
{
	$data = explode('_',$data);
	$search_job = str_replace("'","",$data[4]);
	$search_order = trim(str_replace("'","",$data[5]));
	$search_type = $data[6];
	$programNo = $data[7];
	$search_programNo = $data[7];

	$company_cond = '';
	$buyer_cond = '';
	$search_job_cond = '';
	$search_order_cond = '';
	$search_program_cond = '';
	$program_date_cond = '';
	$query_year = '';

	if ($data[0] != 0)
		$company_cond = " AND spm.company_id = '".$data[0]."'";

	if ($data[1] != 0)
		$buyer_cond = " AND spm.buyer_id = '".$data[1]."'";

	/*
	|--------------------------------------------------------------------------
	| Searching Type
	| exact
	|--------------------------------------------------------------------------
	|
	*/
	if($search_type==1)
	{
		if($search_job != '')
			$search_job_cond = "AND som.job_no_prefix_num = '".$search_job."'";

		if($search_order != '')
			$search_order_cond = " AND sod.order_no = '".$search_order."'";

		if($search_programNo != '')
			$search_program_cond = " AND spd.id = '".$search_programNo."'";
	}

	/*
	|--------------------------------------------------------------------------
	| Searching Type
	| Contents
	|--------------------------------------------------------------------------
	|
	*/
	else if($search_type==4 || $search_type==0)
	{
		if($search_job != '')
			$search_job_cond = "AND som.job_no_prefix_num LIKE '%".$search_job."%'";

		if($search_order != '')
			$search_order_cond = " AND sod.order_no LIKE '%".$search_order."%'";

		if($search_programNo != '')
			$search_program_cond = " AND spd.id LIKE '%".$search_programNo."%'";
	}

	/*
	|--------------------------------------------------------------------------
	| Searching Type
	| Starts with
	|--------------------------------------------------------------------------
	|
	*/
	else if($search_type==2)
	{
		if($search_job != '')
			$search_job_cond = "AND som.job_no_prefix_num LIKE '".$search_job."%'";

		if($search_order != '')
			$search_order_cond = " AND sod.order_no LIKE '".$search_order."%'";

		if($search_programNo != '')
			$search_program_cond = " AND spd.id LIKE '".$search_programNo."%'";
	}

	/*
	|--------------------------------------------------------------------------
	| Searching Type
	| Ends with
	|--------------------------------------------------------------------------
	|
	*/
	else if($search_type==3)
	{
		if($search_job != '')
			$search_job_cond = "AND som.job_no_prefix_num LIKE '%".$search_job."'";

		if($search_order != '')
			$search_order_cond = " AND sod.order_no LIKE '%".$search_order."'";

		if($search_programNo != '')
			$search_program_cond = " AND spd.id LIKE '%".$search_programNo."'";
	}

	/*
	|--------------------------------------------------------------------------
	| MYSQL Database
	|--------------------------------------------------------------------------
	|
	*/
	if($db_type==0)
	{
		$query_year = "year(spm.insert_date) AS year";

		if ($data[2]!="" &&  $data[3]!="")
			$program_date_cond = "AND spd.program_date BETWEEN '".change_date_format($data[2],'yyyy-mm-dd')."' AND '".change_date_format($data[3],'yyyy-mm-dd')."'";
	}

	/*
	|--------------------------------------------------------------------------
	| ORACLE Database
	|--------------------------------------------------------------------------
	|
	*/
	else
	{
		$query_year = "TO_CHAR(spm.insert_date,'YYYY') AS year";

		if ($data[2]!="" &&  $data[3]!="")
			$program_date_cond = "AND spd.program_date BETWEEN '".change_date_format($data[2], "", "",1)."' AND '".change_date_format($data[3], "", "",1)."'";
	}

	/*
	|--------------------------------------------------------------------------
	| main query
	|--------------------------------------------------------------------------
	|
	*/
	$sql = "
		SELECT
			distinct spd.id,spm.buyer_id AS party_id, spm.gsm_weight, spm.job_no, ".$query_year.", spm.subcon_order_id,
			spd.knitting_source, spd.knitting_party, spd.color_id, spd.color_range, spd.machine_dia, spd.width_dia_type, spd.machine_gg, spd.fabric_dia, spd.program_qnty, spd.stitch_length, spd.spandex_stitch_length, spd.draft_ratio, spd.machine_id, spd.machine_capacity, spd.distribution_qnty, spd.status, spd.start_date, spd.end_date, spd.program_date, spd.feeder, spd.remarks, spd.save_data, spd.location_id, spd.advice,
			sppd.id, sppd.mst_id, sppd.dtls_id, sppd.po_id, sppd.determination_id, sppd.gsm_weight, sppd.dia, sppd.buyer_id, sppd.fabric_desc,
			sod.main_process_id, sod.order_no,spd.yarn_details_breakdown
		FROM
			subcon_planning_mst spm
			INNER JOIN subcon_planning_dtls spd ON spm.id = spd.mst_id
			INNER JOIN subcon_planning_plan_dtls sppd ON spd.id = sppd.dtls_id
			INNER JOIN subcon_ord_mst som ON spm.subcon_order_id = som.id
			INNER JOIN subcon_ord_dtls sod ON som.id=sod.mst_id and sppd.po_id=sod.id
		WHERE
			spd.status_active = 1
			AND spd.is_deleted = 0
			and spm.status_active = 1 AND spm.is_deleted = 0 and sppd.status_active = 1 AND sppd.is_deleted = 0 and som.status_active = 1 AND som.is_deleted = 0 and sod.status_active = 1 AND sod.is_deleted = 0
			".$company_cond."
			".$buyer_cond."
			".$search_job_cond."
			".$search_order_cond."
			".$search_program_cond."
			".$program_date_cond."
		ORDER BY
			sppd.mst_id DESC
	";
	//echo $sql;

	/*
	|--------------------------------------------------------------------------
	| query result checking for
	| $sql
	|--------------------------------------------------------------------------
	|
	*/
	$nameArray=sql_select( $sql );
	if(empty($nameArray))
	{
		echo get_empty_data_msg();
		die;
	}

	$color_arr = get_color_array();

	/*
	|--------------------------------------------------------------------------
	| for production information
	|--------------------------------------------------------------------------
	|
	*/
	$idArr = array();
	foreach($nameArray as $row)
	{
		$idArr[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
	}

	/* $sqlProduction = "
		SELECT
			spm.subcon_order_id,
			spd.program_qnty,
			sppd.id, sppd.mst_id, sppd.dtls_id,
			spdd.product_qnty
		FROM
			subcon_planning_mst spm
			INNER JOIN subcon_planning_dtls spd ON spm.id = spd.mst_id
			INNER JOIN subcon_planning_plan_dtls sppd ON spd.id = sppd.dtls_id
			INNER JOIN subcon_production_dtls spdd ON sppd.dtls_id = spdd.order_id
		WHERE
			spd.id IN(".implode(',', $idArr).")
			AND spd.status_active = 1
			AND spd.is_deleted = 0
			AND spdd.status_active = 1
			AND spdd.is_deleted = 0
	";

	$rsltProduction = sql_select( $sqlProduction );
	$productionData = array();
	foreach($rsltProduction as $row)
	{
		$productionData[$row[csf('dtls_id')]]['programQty'] = $row[csf('program_qnty')];
		$productionData[$row[csf('dtls_id')]]['productQty'] = $row[csf('product_qnty')];
		$productionData[$row[csf('dtls_id')]]['balanceQty'] = $row[csf('program_qnty')] - $row[csf('product_qnty')];
	} */

	$sqlProduction = "SELECT  a.program_no, c.program_qnty, sum(b.product_qnty) as product_qnty
from subcon_production_mst a, subcon_production_dtls b, subcon_planning_dtls c
where a.program_no in(".implode(',', $idArr).") and a.id = b.mst_id and a.program_no=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.program_no, c.program_qnty";

	$rsltProduction = sql_select( $sqlProduction );
	$productionData = array();
	foreach($rsltProduction as $row)
	{
		$productionData[$row[csf('program_no')]]['programQty'] = $row[csf('program_qnty')];
		$productionData[$row[csf('program_no')]]['productQty'] = $row[csf('product_qnty')];
		$productionData[$row[csf('program_no')]]['balanceQty'] = $row[csf('program_qnty')] - $row[csf('product_qnty')];
	}

	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="508" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="60">Year</th>
                <th width="100">Job No</th>
                <th width="100">Order No</th>
                <th width="50">Program No</th>
                <th width="80">Program Qty</th>
                <th>Program Date</th>
            </thead>
        </table>
        <div style="width:508px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="490" class="rpt_table" id="tbl_list_search" >
            <?
			$i=1;
			$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

			foreach ($nameArray as $row)
			{
				if(empty($productionData[$row[csf('dtls_id')]]))
				{
					if($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						$yarn_details_arr=explode("__",$row[csf('yarn_details_breakdown')]);
						$y=1; $ycountUniqArr=array();
						foreach($yarn_details_arr as $val){
							$yarn_arr=explode("_",$val);
							if($y==1){
								if($ycountUniqArr[$yarn_arr[0]]!=$yarn_arr[0])
								{
									$ycount.=$yarn_arr[0];
									$ycountUniqArr[$yarn_arr[0]]=$yarn_arr[0];
								}
								$ylot.=$yarn_arr[1];
								$ybrand.=$yarn_arr[2];
								$yqnty.=$yarn_arr[3];
								$y++;
							}else{
								if($ycountUniqArr[$yarn_arr[0]]!=$yarn_arr[0])
								{
									$ycount.=",".$yarn_arr[0];
									$ycountUniqArr[$yarn_arr[0]]=$yarn_arr[0];
								}
								$ylot.=",".$yarn_arr[1];
								$ybrand.=",".$yarn_arr[2];
								$yqnty.=",".$yarn_arr[3];
							}
						}

					$info = $row[csf('dtls_id')].'*'.$row[csf('knitting_source')].'*'.$row[csf('knitting_party')].'*'.$row[csf('order_no')].'*'.$row[csf('color_range')].'*1*'.$row[csf('gsm_weight')].'*'.$row[csf('machine_dia')].'*'.$row[csf('width_dia_type')].'*'.$color_arr[$row[csf('color_id')]].'*'.$row[csf('color_id')].'*'.$row[csf('machine_gg')].'*'.$row[csf('stitch_length')].'*'.$row[csf('machine_dia')].'*'.$row[csf('machine_id')].'*'.$row[csf('program_qnty')].'*'.$row[csf('job_no')].'*'.$row[csf('subcon_order_id')].'*'.$row[csf('location_id')].'*'.$row[csf('determination_id')].'*'.$row[csf('fabric_desc')].'*'.$row[csf('main_process_id')].'*'.$row[csf('po_id')].'*'.$row[csf('party_id')].'*'.$ycount.'*'.$ylot.'*'.$ybrand.'*'.$row[csf('fabric_dia')];

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $info; ?>')">
						<td width="30" align="center"><? echo $i ?></td>
						<td width="60" align="center"><? echo $row[csf('year')]; ?></td>
						<td width="100" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('order_no')]; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf('dtls_id')]; ?></p></td>
						<td width="80" align="right"><? echo number_format( $row[csf('program_qnty')],2); ?>&nbsp;</td>
						<td align="center"><? echo change_date_format($row[csf('program_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
				else
				{
					if($productionData[$row[csf('dtls_id')]]['balanceQty'] > 0)
					{
						if($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$yarn_details_arr=explode("__",$row[csf('yarn_details_breakdown')]);
						$y=1;
						foreach($yarn_details_arr as $val){
							$yarn_arr=explode("_",$val);
							if($y==1){
								$ycount.=$yarn_arr[0];
								$ylot.=$yarn_arr[1];
								$ybrand.=$yarn_arr[2];
								$yqnty.=$yarn_arr[3];
								$y++;
							}else{
								$ycount.=",".$yarn_arr[0];
								$ylot.=",".$yarn_arr[1];
								$ybrand.=",".$yarn_arr[2];
								$yqnty.=",".$yarn_arr[3];
							}
						}



						if($ycount=="") $ycount="";if($ylot=="") $ylot="";if($ybrand=="") $ybrand="";

						$info = $row[csf('dtls_id')].'*'.$row[csf('knitting_source')].'*'.$row[csf('knitting_party')].'*'.$row[csf('order_no')].'*'.$row[csf('color_range')].'*1*'.$row[csf('gsm_weight')].'*'.$row[csf('machine_dia')].'*'.$row[csf('width_dia_type')].'*'.$color_arr[$row[csf('color_id')]].'*'.$row[csf('color_id')].'*'.$row[csf('machine_gg')].'*'.$row[csf('stitch_length')].'*'.$row[csf('machine_dia')].'*'.$row[csf('machine_id')].'*'.$row[csf('program_qnty')].'*'.$row[csf('job_no')].'*'.$row[csf('subcon_order_id')].'*'.$row[csf('location_id')].'*'.$row[csf('determination_id')].'*'.$row[csf('fabric_desc')].'*'.$row[csf('main_process_id')].'*'.$row[csf('po_id')].'*'.$row[csf('party_id')].'*'.$ycount.'*'.$ylot.'*'.$ybrand.'*'.$row[csf('fabric_dia')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $info; ?>')">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="60" align="center"><? echo $row[csf('year')]; ?></td>
							<td width="100" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
							<td width="100"><p><? echo $row[csf('order_no')]; ?></p></td>
							<td width="50" align="center"><p><? echo $row[csf('dtls_id')]; ?></p></td>
							<td width="80" align="right"><? echo number_format( $row[csf('program_qnty')],2); ?>&nbsp;</td>
							<td align="center"><? echo change_date_format($row[csf('program_date')]); ?></td>
						</tr>
						<?
						$i++;
					}
				}
			}
			?>
            </table>
        </div>
	</div>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| actn_plan_list_view
| don't use
|--------------------------------------------------------------------------
|
*/
if($action == "actn_plan_dtls_listview")
{
	$color_arr = get_color_array();
	//$data=explode('_',$data);
	//$order_id=$data[0];
	//$process_id=$data[1];
	//$item_arr=return_library_array( "SELECT id,const_comp FROM lib_subcon_charge",'id','const_comp');
	//$gsm_arr=return_library_array( "SELECT id,gsm FROM lib_subcon_charge",'id','gsm');

	//new
	/*
	$sql = "
		SELECT
			sm.id AS smid, sm.party_id,
            sd.id AS sdid, sd.order_no, sd.order_quantity, sd.order_rcv_date,
			sb.id AS sbid, sb.mst_id, sb.order_id, sb.item_id, sb.color_id, sb.gsm, sb.dia_width_type, sb.finish_dia
		FROM
			subcon_ord_mst sm
			INNER JOIN subcon_ord_dtls sd ON sm.id = sd.mst_id
			INNER JOIN subcon_ord_breakdown sb ON sm.id = sb.mst_id
		WHERE
			sm.subcon_job = sd.job_no_mst
			AND sd.id = sb.order_id
			--AND sm.main_process_id = 2
			AND sm.entry_form = 238
			AND sm.status_active = 1
			AND sm.is_deleted = 0
			AND sm.company_id = ".$company_name."
			".$partyCondition."
			".$orderNoCondition."
			".$receiveDateCondition."
		GROUP BY
			sm.id, sm.party_id,
            sd.id, sd.order_no, sd.order_quantity, sd.order_rcv_date,
			sb.id, sb.mst_id, sb.order_id, sb.item_id, sb.color_id, sb.gsm, sb.dia_width_type, sb.finish_dia
		ORDER BY sb.finish_dia ASC
	";


	$sqlPlan = "SELECT a.subcon_order_id, c.id, c.mst_id, c.determination_id, c.gsm_weight, c.dia, ".$queryProgNo." SUM(c.program_qnty) AS program_qnty, c.status_active
	FROM
		subcon_planning_mst a,
		subcon_planning_dtls b,
		subcon_planning_plan_dtls c
	WHERE
		a.id = b.mst_id
		AND b.id = c.dtls_id
		AND a.subcon_order_id IN(".implode(",",$mstIdArr).")
		AND b.status_active = 1
		AND b.is_deleted = 0
		AND c.is_revised=0
	GROUP BY
		a.subcon_order_id,
		c.id, c.mst_id, c.yarn_desc, c.body_part_id, c.determination_id, c.gsm_weight, c.dia, c.status_active
	";
	*/
	$sql = "
		SELECT
			spm.subcon_order_id, spm.fabric_desc,
			sppd.id, sppd.mst_id, sppd.determination_id, sppd.gsm_weight, sppd.dia, sppd.program_qnty, sppd.color_id,
			sod.order_quantity
		FROM
			subcon_planning_mst spm
			INNER JOIN subcon_planning_dtls spd ON spm.id = spd.mst_id
			INNER JOIN subcon_planning_plan_dtls sppd ON spd.id = sppd.dtls_id
			INNER JOIN subcon_ord_mst som ON spm.subcon_order_id = som.id
			INNER JOIN subcon_ord_dtls sod ON som.id = sod.mst_id
			--INNER JOIN subcon_ord_breakdown sob ON som.id = sob.mst_id
		WHERE
			spd.id = ".$data."
			AND spd.status_active = 1
			AND spd.is_deleted = 0
			AND sppd.status_active = 1
			AND sppd.is_deleted = 0
	";
	//echo $sql;
	$data_array=sql_select($sql);


	//old
	$production_qty_array=array();
	$prod_sql="Select cons_comp_id,color_id, sum(product_qnty) as product_qnty from subcon_production_dtls where order_id='$data[0]' and product_type=2 and status_active=1 and is_deleted=0 group by cons_comp_id,color_id";
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		//$production_qty_array[$row[csf('cons_comp_id')]][$row[csf('color_id')]]=$row[csf('product_qnty')];
		$production_qty_array[$row[csf('cons_comp_id')]] += $row[csf('product_qnty')];
	}
	//var_dump($production_qty_array);
	$sql = "select item_id,gsm,grey_dia,color_id, sum(qnty) as qnty from subcon_ord_breakdown where order_id='$data[0]' group by item_id,gsm,grey_dia,color_id";
	//$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
        <thead>
            <th width="15">SL</th>
            <th>Fabric Description</th>
            <th width="50">Color</th>
            <th width="60">Order Qty</th>
            <th width="40">Prog. Qty</th>
            <th width="60">Bal. Qty</th>
        </thead>
        <tbody>
            <?
            $i=1;
            foreach($data_array as $row)
            {
                if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				$colorName = $color_arr[$row[csf('color_id')]];
				$balanceQty = $row[csf('order_quantity')] - $row[csf('program_qnty')];

				/*if($process_id==2 || $process_id==3 || $process_id==4 || $process_id==6 || $process_id==7)
				{
					$item_name=$item_arr[$row[csf('item_id')]];
					//$gsm_val=$gsm_arr[$row[csf('item_id')]];
				}
				else
				{
					$item_name=$garments_item[$row[csf('item_id')]];
					//$gsm_val='';
				}*/

             	?>
                <tr bgcolor="<? echo $bgcolor; ?>" valign="middle" onClick='set_form_data("<? //echo $row[csf('item_id')]."**".$item_name."**".$row[csf('gsm')]."**".$colorName; ?>")' style="cursor:pointer" >
                    <td align="center"><? echo $i; ?></td>
                    <td><? echo $row[csf('fabric_desc')].', '.$row[csf('gsm_weight')].', '.$row[csf('dia')]; ?></td>
                    <td><? echo $colorName; ?></td>
                    <td align="right"><? echo number_format($row[csf('order_quantity')]); ?></td>
                    <td align="right"><? echo number_format($row[csf('program_qnty')]); ?></td>
                    <td align="right"><? echo number_format($balanceQty); ?></td>
                </tr>
				<?
                $i++;
            }
            ?>
        </tbody>
    </table>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| load_php_data_to_form_dtls_order
|--------------------------------------------------------------------------
|
*/
if($action == "load_php_data_to_form_dtls_order")
{
	$nameArray=sql_select( "select id, order_no, order_uom, main_process_id, job_no_mst,process_id from subcon_ord_dtls where id='$data'" );
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_order_no').value		= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_job_no').value		= '".$row[csf("job_no_mst")]."';\n";
		echo "document.getElementById('cbo_uom').value			= '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('process_id').value		= '".$row[csf("main_process_id")]."';\n";
		echo "document.getElementById('order_no_id').value		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_process').value			 		= '".$row[csf("process_id")]."';\n";
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| show_fabric_desc_listview
|--------------------------------------------------------------------------
|
*/
if($action == "show_fabric_desc_listview")
{
	$data=explode('_',$data);
	//$order_id=$data[0];
	//$process_id=$data[1];
	//$productionBasis=$data[2];
	 $compay_id=$data[4];
	//echo $compay_id.'D';
	/*
	|--------------------------------------------------------------------------
	| subcon_production_dtls
	|--------------------------------------------------------------------------
	|
	*/
	if($data[2] == 1)
	{
		$productionBasisCondition = " AND m.production_basis = 1 ";
	}
	else
	{
		$productionBasisCondition = " AND m.production_basis = 2 ";
	}

	$production_qty_array=array();
	if($data[2] == 1)
	{
		$prod_sql="SELECT d.cons_comp_id, d.dia_width_type, d.gsm, d.dia_width, d.color_id,d.uom_id, SUM(d.product_qnty) AS product_qnty, SUM(d.product_qnty_pcs) AS product_qnty_pcs FROM subcon_production_mst m, subcon_production_dtls d WHERE m.id = d.mst_id AND d.order_id='".$data[0]."' AND d.product_type=2 AND d.status_active=1 AND d.is_deleted=0 ".$productionBasisCondition." GROUP BY d.cons_comp_id, d.dia_width_type, d.gsm, d.dia_width, d.color_id,d.uom_id";
		//echo $prod_sql;
		$prod_data_sql=sql_select($prod_sql);
		foreach($prod_data_sql as $row)
		{
			$production_qty_array[$row[csf('cons_comp_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom_id')]][$row[csf('color_id')]]['qnty'] += $row[csf('product_qnty')];
			$prod_qtyPcs_array[$row[csf('cons_comp_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom_id')]][$row[csf('color_id')]]['product_qnty_pcs'] += $row[csf('product_qnty_pcs')];
		}
	}
	else
	{
		$prod_sql="SELECT d.cons_comp_id, d.dia_width_type, d.gsm, d.dia_width, d.color_id,d.uom_id, SUM(d.product_qnty) AS product_qnty, SUM(d.product_qnty_pcs) AS product_qnty_pcs,m.program_no FROM subcon_production_mst m, subcon_production_dtls d WHERE m.id = d.mst_id AND d.order_id='".$data[0]."'  AND d.product_type=2 AND d.status_active=1 AND d.is_deleted=0 ".$productionBasisCondition." GROUP BY d.cons_comp_id, d.dia_width_type, d.gsm, d.dia_width, d.color_id,m.program_no,d.uom_id";
		 //echo $prod_sql;
		//and d.id='".$data[5]."'
		$prod_data_sql=sql_select($prod_sql);
		foreach($prod_data_sql as $row)
		{
			$production_qty_array[$row[csf('cons_comp_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom_id')]][$row[csf('color_id')]][$row[csf('program_no')]]['qnty'] += $row[csf('product_qnty')];
			$prod_qtyPcs_array[$row[csf('cons_comp_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom_id')]][$row[csf('color_id')]][$row[csf('program_no')]]['product_qnty_pcs'] = $row[csf('product_qnty_pcs')];
		}
	}
	// echo "<pre>";
	// print_r($production_qty_array);

	/*
	|--------------------------------------------------------------------------
	| Fabric details
	|--------------------------------------------------------------------------
	|
	*/

	if($data[2] == 1)
	{
		$sql = "SELECT item_id, dia_width_type, gsm, grey_dia, finish_dia, color_id, SUM(qnty) AS qnty,order_id as po_id FROM subcon_ord_breakdown WHERE order_id='".$data[0]."' GROUP BY item_id, dia_width_type, gsm, grey_dia, finish_dia, color_id,order_id";
	}
	else
	{
		$programCondition = '';
		if($data[3] != '')
		{
			$programCondition = " AND b.dtls_id = ".$data['3']." ";
		}
		//$sql = "SELECT determination_id AS item_id, width_dia_type AS dia_width_type, gsm_weight AS gsm, dia AS grey_dia, color_id, SUM(program_qnty) AS qnty FROM subcon_planning_plan_dtls WHERE po_id = '".$data[0]."' ".$programCondition." GROUP BY determination_id, width_dia_type, gsm_weight, dia, color_id";


		 $sql = "SELECT b.determination_id AS item_id, b.width_dia_type AS dia_width_type, b.gsm_weight AS gsm, a.fabric_dia AS grey_dia, b.color_id, SUM(b.program_qnty) AS qnty, b.dtls_id,b.po_id FROM SUBCON_PLANNING_DTLS a, subcon_planning_plan_dtls b WHERE a.id=b.dtls_id and b.po_id = '".$data[0]."' ".$programCondition." GROUP BY b.determination_id, b.width_dia_type, b.gsm_weight, a.fabric_dia, b.color_id, b.dtls_id,b.po_id";


		//$sql = "SELECT determination_id AS item_id, width_dia_type AS dia_width_type, gsm_weight AS gsm, dia AS grey_dia, color_id, SUM(program_qnty) AS qnty, dtls_id,po_id FROM subcon_planning_plan_dtls b WHERE po_id = '".$data[0]."' ".$programCondition." GROUP BY determination_id, width_dia_type, gsm_weight, dia, color_id, dtls_id,po_id";

	}
				$fabricData = sql_select("select dyeing_fin_bill,allow_per from variable_settings_subcon where company_id ='$compay_id' and variable_list in(16) and is_deleted=0 and status_active=1");
				$vari_material_issue=0;$allow_per=0;

				foreach($fabricData as $row)
				{
					$vari_material_issue=$row[csf('dyeing_fin_bill')];
					$allow_per=$row[csf('allow_per')];

				}
				//Validation about Yarn Issue and Knitting Production********Variable Seting*********
			//	$msg_ttl="Order";

				if($vari_material_issue==1)
				{
					 $po_sql= "SELECT b.id AS ord_id, a.quantity, b.order_no, b.delivery_date, b.order_quantity FROM sub_material_dtls a, subcon_ord_dtls b WHERE a.order_id = b.id AND a.status_active = 1 AND b.status_active = 1 AND b.id = ".$data[0]."  and a.item_category_id=1  ORDER BY b.id asc";
					$po_sql_res=sql_select($po_sql);
					foreach($po_sql_res as $row)
					{
						$yarn_issue_qty+=$row[csf('quantity')];

					}

					$tot_yarn_issueQty=$yarn_issue_qty+(($yarn_issue_qty*$allow_per)/100);
					//$msg_ttl="Yarn Issue";
				}
				//echo $vari_material_issue.'D';
				// echo $hdnOrderQty.'='.$yarn_issue_qty.'d';

	//$sql = "SELECT item_id, dia_width_type, gsm, grey_dia, finish_dia, color_id, SUM(qnty) AS qnty FROM subcon_ord_breakdown WHERE order_id='".$data[0]."' GROUP BY item_id, dia_width_type, gsm, grey_dia, finish_dia, color_id";
	//echo $sql;

	/*
	|--------------------------------------------------------------------------
	| query result checking for
	| $sql
	|--------------------------------------------------------------------------
	|
	*/
	$data_array=sql_select($sql);
	if(empty($data_array))
	{
		echo get_empty_data_msg();
		die;
	}

	$color_arr = get_color_array();

	/*
	|--------------------------------------------------------------------------
	| Fabric construction, consumption and gsm
	| array preparing here
	|--------------------------------------------------------------------------
	|
	*/
	$itemIdArr = array();
	foreach($data_array as $row)
    {
		$itemIdArr[$row[csf('item_id')]] = $row[csf('item_id')];
	}

	$item_arr = array();
	//$gsm_arr = array();
	$sqlCharge = "SELECT id, const_comp, gsm FROM lib_subcon_charge WHERE id IN(".implode(',', $itemIdArr).")";
	$resultCharge = sql_select($sqlCharge);
	foreach($resultCharge as $row)
    {
		$item_arr[$row[csf('id')]] = $row[csf('const_comp')];
		//$gsm_arr[$row[csf('id')]] = $row[csf('gsm')];
	}
	$order_uom_lib=return_library_array( "select id, order_uom from subcon_ord_dtls", "id", "order_uom");
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="490" style="margin-top:10px;">
        <thead>
            <th width="15">SL</th>
            <th>Fabric Description</th>
            <th width="100">Color</th>
			<th width="60">UOM</th>
            <th width="60"><? if($data[2] == 1) echo "Order Qty";else echo "Plan Qty";?></th>
            <th width="40">Prod. Qty</th>
			<th width="40">Prod. Qty(pcs)</th>
            <th width="60">Bal. Qty</th>
        </thead>
        <tbody>
            <?
            $i=1;
            foreach($data_array as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($data[1]==2 || $data[1]==3 || $data[1]==4 || $data[1]==6 || $data[1]==7)
				{
					$item_name=$item_arr[$row[csf('item_id')]];
				}
				else
				{
					$item_name=$garments_item[$row[csf('item_id')]];
				}

				$color_name = $color_arr[$row[csf('color_id')]];
				$uom=$order_uom_lib[$row[csf('po_id')]];

				//echo $row[csf('item_id')]."=".$row[csf('dia_width_type')]."=".$row[csf('gsm')]."=".$row[csf('grey_dia')]."=".$row[csf('color_id')]."<br>";
				if($vari_material_issue==1)
				{
					$orderQty =$tot_yarn_issueQty;
					//echo $orderQty;
				}
				else
				{
					$orderQty = $row[csf('qnty')];
					//echo $row[csf('qnty')].'=A';
				}

				if($data[2] == 1)
				{

					$order_planQty = $row[csf('qnty')];
					$productQty = $production_qty_array[$row[csf('item_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('grey_dia')]][$uom][$row[csf('color_id')]]['qnty'];
					$productQtyPcs = $prod_qtyPcs_array[$row[csf('item_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('grey_dia')]][$row[csf('color_id')]]['product_qnty_pcs'];
					// echo $productQty.'='.$row[csf('item_id')].'='.$row[csf('dia_width_type')].'='.$row[csf('gsm')].'='.$row[csf('grey_dia')].'='.$uom.'='.$row[csf('color_id')];

				}
				else
				{
					$order_planQty = $row[csf('qnty')];
					$productQty = $production_qty_array[$row[csf('item_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('grey_dia')]][$uom][$row[csf('color_id')]][$row[csf('dtls_id')]]['qnty'];
					$productQtyPcs = $prod_qtyPcs_array[$row[csf('item_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('grey_dia')]][$uom][$row[csf('color_id')]][$row[csf('dtls_id')]]['product_qnty_pcs'];
					//echo $productQty.'D';

				}

				if($uom==12){
					$balanceQty = $orderQty - $productQty;
				}else{
					$balanceQty = $orderQty - $productQtyPcs;
				}

				$hdn = $row[csf('item_id')].$row[csf('dia_width_type')].$row[csf('gsm')].$row[csf('grey_dia')].$row[csf('color_id')];
             	?>
                <tr bgcolor="<? echo $bgcolor; ?>" valign="middle" onClick='set_form_data("<? echo $row[csf('item_id')]."**".$item_name."**".$row[csf('gsm')]."**".$color_name."**".$row[csf('dia_width_type')]."**".$row[csf('grey_dia')]."**".$row[csf('color_id')]."**".number_format($productQty)."**".$orderQty."**".$balanceQty."**".$uom; ?>"); func_selected_row("<? echo $i; ?>");' style="cursor:pointer" >
                    <td align="center"><? echo $i; ?></td>
                    <td><div style="word-break:break-all"><? echo $item_name.', '.$fabric_typee[$row[csf('dia_width_type')]].', '.$row[csf('gsm')].', '.$row[csf('grey_dia')].', '.$row[csf('finish_dia')]; ?></div></td>
                    <td><div style="word-break:break-all"><? echo $color_name; ?></div></td>
					<td><div style="word-break:break-all"><? echo $unit_of_measurement[$uom]; ?></div></td>
                    <td align="right">
					<? echo number_format($order_planQty); ?>
                    <input type="hidden" name="hdnOrderQty[]" id="hdnOrderQty_<? echo $i; ?>" class="text_boxes" value="<? echo $order_planQty; ?>" readonly style="width:50px;" />
                    <input type="hidden" name="hddnOrderQty[]" id="hddnOrderQty_<? echo $hdn; ?>" class="text_boxes" value="<? echo $order_planQty; ?>" readonly style="width:50px;" />
                    </td>
                    <td align="right">
					<? echo number_format($productQty); ?>
                    <input type="hidden" name="hdnTotalProductQty[]" id="hdnTotalProductQty_<? echo $i; ?>" class="text_boxes" value="<? echo $productQty; ?>" readonly style="width:50px;" />
                    <input type="hidden" name="hddnTotalProductQty[]" id="hddnTotalProductQty_<? echo $hdn; ?>" class="text_boxes" value="<? echo $productQty; ?>" readonly style="width:50px;" />
                    </td>
					<td align="right">
					<? echo number_format($productQtyPcs); ?>
                    <input type="hidden" name="hdnTotalProductQtyPcs[]" id="hdnTotalProductQtyPcs_<? echo $i; ?>" class="text_boxes" value="<? echo $productQtyPcs; ?>" readonly style="width:50px;" />
                    <input type="hidden" name="hddnTotalProductQtyPcs[]" id="hddnTotalProductQtyPcs_<? echo $hdn; ?>" class="text_boxes" value="<? echo $productQtyPcs; ?>" readonly style="width:50px;" />
                    </td>
                    <td align="right" title="With Excess <?=$allow_per.'%,'.$orderQty.'-'.$productQty;?>">
					<? echo number_format($balanceQty); ?>
                    <input type="hidden" name="hdnBalanceQty[]" id="hdnBalanceQty_<? echo $i; ?>" class="text_boxes" value="<? echo $balanceQty; ?>" readonly style="width:50px;" />
                    <input type="hidden" name="hddnBalanceQty[]" id="hddnBalanceQty_<? echo $hdn; ?>" class="text_boxes" value="<? echo $balanceQty; ?>" readonly style="width:50px;" />
                    </td>
                </tr>
            <?
            $i++;
            }
            ?>
        </tbody>
    </table>
	<?
	exit();
}


/*
|--------------------------------------------------------------------------
| save_update_delete
|--------------------------------------------------------------------------
|
*/
if ($action == "save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$process_knitting="2";
	$txt_yarn_lot = str_replace(' ', '', $txt_yarn_lot);

	$color_arr = get_color_array();
	$brand_arr = get_brand_array();

	if ($operation!=2)
	{
		$sql_variable="select variable_list, dyeing_fin_bill, allow_per from variable_settings_subcon where company_id=$cbo_company_id and variable_list in (16) and status_active=1 and is_deleted=0";
		$resultVariable = sql_select($sql_variable);
		$isvalidate=0; $allow_per=0;
		foreach($resultVariable as $row)
		{
			$isvalidate=$row[csf('dyeing_fin_bill')];
			$allow_per=$row[csf('allow_per')];
		}
		unset($resultVariable);
		if($isvalidate==1)
		{
			if($operation==1) $upsqlCond="and b.id!=$update_id_dtl"; else $upsqlCond="";
			$issueQty=return_field_value("sum(b.quantity) as issueqty","sub_material_mst a, sub_material_dtls b","a.id=b.mst_id and b.order_id=$order_no_id and a.trans_type=2 and a.entry_form=343 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=1","issueqty");
			//"select sum(b.product_qnty) as prevProdQty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.order_id=$order_no_id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$prevProdQty=return_field_value("sum(b.product_qnty) as prevProdQty","subcon_production_mst a, subcon_production_dtls b","a.id=b.mst_id and b.order_id=$order_no_id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $upsqlCond","prevProdQty");

			$issueQtyWithExcess=$issueQty+($issueQty*($allow_per/100));

			//echo "10**".$issueQty.'--'.$prevProdQty.'--'.$issueQtyWithExcess; die;
			if((str_replace("'","",$txt_product_qnty)+$prevProdQty)>$issueQtyWithExcess)
			{
				echo "17**Knitting production Exceeds Issue Qty.\n Prod. Qty=".str_replace("'","",$txt_product_qnty).",\n Prev. Prod Qty=".$prevProdQty.",\n Issue Qty=".$issueQty."+".($issueQty*($allow_per/100));
				//check_table_status( $_SESSION['menu_id'],0);
				die;
			}
		}
	}
	/*
	|--------------------------------------------------------------------------
	| Insert
	|--------------------------------------------------------------------------
	|
	*/
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if($db_type==0) $year_cond=" and YEAR(insert_date)"; else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";

		/*
		|--------------------------------------------------------------------------
		| subcon_production_mst
		| data preparing for
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'KNT', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_production_mst where entry_form=159 and company_id=$cbo_company_id and product_type='$process_knitting' $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));

		if(str_replace("'",'',$update_id)=="")
		{
			$cbo_production_basis = str_replace("'",'',$cbo_production_basis);
			if($cbo_production_basis == 1)
			{
				$txt_program_no = 0;
			}

			$id=return_next_id( "id", "subcon_production_mst", 1 ) ;
			$field_array="id,entry_form,prefix_no,prefix_no_num,product_no,product_type,company_id,location_id,party_id,product_date,prod_chalan_no,yrn_issu_chalan_no,roll_maintained,remarks,inserted_by,insert_date,knitting_source,knitting_company,knit_location_id,production_basis,program_no";
			$data_array="(".$id.",159,'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."','".$process_knitting."',".$cbo_company_id.",".$cbo_location_name.",".$cbo_party_name.",".$txt_production_date.",".$txt_prod_chal_no.",".$txt_yarn_issue_challan_no.",".$txt_roll_maintained.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_knitting_source.",".$cbo_knitting_company.",".$cbo_knit_location_name.",".$cbo_production_basis.",".$txt_program_no.")";

			//$rID=sql_insert("subcon_production_mst",$field_array,$data_array,0);
			$return_no=$new_return_no[0];
		}
		else
		{
			$id=$update_id;
			$field_array="location_id*party_id*product_date*prod_chalan_no*yrn_issu_chalan_no*remarks*updated_by*update_date";
			$data_array="".$cbo_location_name."*".$cbo_party_name."*".$txt_production_date."*".$txt_prod_chal_no."*".$txt_yarn_issue_challan_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=$txt_production_id;
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_production_dtls
		| data preparing for
		| $field_array2
		|--------------------------------------------------------------------------
		|
		*/
		if(str_replace("'","",$txt_brand)!="")
		{
			if (!in_array(str_replace("'","",$txt_brand),$brand_arr))
			{
				$brand_name_id = return_id( str_replace("'","",$txt_brand), $brand_arr, "lib_brand", "id,brand_name","159");
				$brand_arr[$brand_name_id]=str_replace("'","",$txt_brand);
			}
			else $brand_name_id =  array_search(str_replace("'","",$txt_brand), $brand_arr);
		}
		else $brand_name_id=0;

		//$brand_name_id=return_id( $txt_brand, $brand_arr, "lib_brand", "id,brand_name");


		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$color_arr))
			{
				$color_name_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","159");
				$color_arr[$color_name_id]=str_replace("'","",$txt_color);
			}
			else $color_name_id =  array_search(str_replace("'","",$txt_color), $color_arr);
		}
		else $color_name_id=0;

		/*if(str_replace("'","",$txt_color)!="")
		{
			$color_name_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		}
		else
		{
			$color_name_id=0;
		}*/

		$id1=return_next_id("id","subcon_production_dtls",1);
		$febric_description=str_replace("(",'[',str_replace(")",']',$txt_febric_description));
		$field_array2="id, mst_id, job_no, order_id, product_type, process, fabric_description, cons_comp_id, gsm, dia_width, dia_width_type, machine_dia, machine_gg, no_of_roll, product_qnty,product_qnty_pcs, reject_qnty, uom_id, yarn_lot, yrn_count_id, brand, shift, floor_id, machine_id, stitch_len, color_range, color_id, remarks,operator_name, inserted_by, insert_date";
		$data_array2="(".$id1.",".$id.",".$txt_job_no.",".$order_no_id.",'".$process_knitting."',".$cbo_process.",".$febric_description.",".$hidd_comp_id.",".$txt_gsm.",".$txt_width.",".$cbo_dia_width_type.",".$txt_machine_dia.",".$txt_machine_gg.",".$txt_roll_qnty.",".$txt_product_qnty.",".$txt_product_qnty_pcs.",".$txt_reject_qnty.",".$cbo_uom.",".$txt_yarn_lot.",".$cbo_yarn_count.",".$txt_brand.",".$cbo_shift_id.",".$cbo_floor_id.",".$cbo_machine_name.",".$txt_stitch_len.",".$cbo_color_range.",'".$color_name_id."',".$text_new_remarks.",".$txt_operator_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		/*
		|--------------------------------------------------------------------------
		| subcon_pro_roll_details
		| data preparing for
		| $data_array_roll and
		| $roll_data_array_update
		|--------------------------------------------------------------------------
		|
		*/
		$booking_without_order=0;
		$barcode_year = date("y");
		$barcode_suffix_no = return_field_value("max(barcode_suffix_no) as suffix_no", "subcon_pro_roll_details", "barcode_year=$barcode_year", "suffix_no") + 1;// and entry_form=2
		$barcode_no = $barcode_year . "02" . str_pad($barcode_suffix_no, 7, "0", STR_PAD_LEFT);

		$field_array_roll = "id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty,qc_pass_qnty_pcs, reject_qnty, roll_no, booking_without_order, product_size, inserted_by, insert_date";
		//$field_array_roll_update = "po_breakdown_id*qnty*qc_pass_qnty*reject_qnty*roll_no*updated_by*update_date";

		$id_roll = return_next_id("id", "subcon_pro_roll_details", 1);
		if (str_replace("'", "", $txt_roll_maintained) == 1)
		{
			$roll_arr = return_library_array("select po_breakdown_id,max(roll_no) as roll_no from subcon_pro_roll_details where entry_form in(159)  group by po_breakdown_id", 'po_breakdown_id', 'roll_no');

		}

		$save_string = explode(",", str_replace("'", "", $save_data));
		$po_array = array();
		$po_reject_qty_array = array();
		for ($i = 0; $i < count($save_string); $i++)
		{
			$order_dtls = explode("_", $save_string[$i]);
			$order_qnty_roll_wise = $order_dtls[0];
			$order_qnty_pcs_roll_wise = $order_dtls[7];
			$size = $order_dtls[8];
			$roll_reject_qty = $order_dtls[1];
			$order_id = trim($order_dtls[2]);
			$roll_no = $order_dtls[3];

			$roll_no = $roll_arr[$order_id] + 1;
			$roll_arr[$order_id] += 1;

			if ($order_id <= 0)
			{
				$order_id = '';
			}

			if ($i == 0)
				$add_comma = "";
			else
				$add_comma = ",";

			$data_array_roll .= "$add_comma(" . $id_roll . "," . $barcode_year . "," . $barcode_suffix_no . "," . $barcode_no . "," . $id . "," . $id1 . "," . $order_id . ",159,'" . $order_qnty_roll_wise . "','" . $order_qnty_roll_wise. "','" . $order_qnty_pcs_roll_wise . "','" . $roll_reject_qty . "','" . $roll_no . "','" .$booking_without_order. "','" .$size. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			$all_barcode_no .= str_replace("'", "", $barcode_no) . ",";
			$id_roll = $id_roll + 1;
			$barcode_suffix_no = $barcode_suffix_no + 1;
			$barcode_no = $barcode_year . "02" . str_pad($barcode_suffix_no, 7, "0", STR_PAD_LEFT);

			if (array_key_exists($order_id, $po_array))
			{
				$po_array[$order_id] += $order_qnty_roll_wise;
				$po_array_pcs[$order_id] += $order_qnty_roll_wise_pcs;
				$po_reject_qty_array[$order_id] += $roll_reject_qty;
			}
			else
			{
				$po_array[$order_id] = $order_qnty_roll_wise;
				$po_array_pcs[$order_id] = $order_qnty_roll_wise_pcs;
				$po_reject_qty_array[$order_id] += $roll_reject_qty;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| barcode check before operation
		|--------------------------------------------------------------------------
		|
		*/
		if ($data_array_roll != "" && str_replace("'", "", $txt_roll_maintained) == 1)
		{
			$all_barcode_no = chop($all_barcode_no, ",");
			$barcod_check = return_field_value("barcode_no", "subcon_pro_roll_details", "status_active=1 and barcode_no in($all_barcode_no)", "barcode_no");

			if ($barcod_check != "") {
				echo "15**0**Database is busy now.";
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				die;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_production_mst
		| data inserting and updatins
		|--------------------------------------------------------------------------
		|
		*/
		if (str_replace("'", "", $update_id) == "")
		{
			$rID=sql_insert("subcon_production_mst",$field_array,$data_array,0);
			if ($rID)
				$flag = 1;
			else
				$flag = 0;
		}
		else
		{
			$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0);
			if ($rID)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_production_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		//echo "10**insert into subcon_production_dtls (".$field_array2.") values ".$data_array2;die;
		$rID2=sql_insert("subcon_production_dtls",$field_array2,$data_array2,0);//die;
		if ($flag == 1)
		{
			if ($rID2)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_pro_roll_details
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($data_array_roll != "" && str_replace("'", "", $txt_roll_maintained) == 1)// && str_replace("'","",$booking_without_order)!=1
		{
			//echo "10**insert into subcon_pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rID3 = sql_insert("subcon_pro_roll_details", $field_array_roll, $data_array_roll, 0);
			if ($flag == 1)
			{
				if ($rID3)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		// echo "10**".$rID."**".$rID2."**".$rID3;oci_rollback($con);die;


		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==0)
		{
			if ($flag == 1)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$id_roll-1);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$id_roll-1);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==2)
		{
			if ($flag == 1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$id_roll-1);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$id_roll-1);
			}
		}
		disconnect($con);
		die;
	}

	/*
	|--------------------------------------------------------------------------
	| Update
	|--------------------------------------------------------------------------
	|
	*/
	else if ($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_production_mst
		| data preparing for
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$field_array="product_no*location_id*party_id*product_date*prod_chalan_no*yrn_issu_chalan_no*roll_maintained*remarks*updated_by*update_date*knitting_source*knitting_company*knit_location_id";
		$data_array="".$txt_production_id."*".$cbo_location_name."*".$cbo_party_name."*".$txt_production_date."*".$txt_prod_chal_no."*".$txt_yarn_issue_challan_no."*".$txt_roll_maintained."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_knitting_source."*".$cbo_knitting_company."*".$cbo_knit_location_name."";

		/*
		|--------------------------------------------------------------------------
		| subcon_production_dtls
		| data preparing for
		| $field_array2
		|--------------------------------------------------------------------------
		|
		*/
		if(str_replace("'","",$txt_brand)!="")
		{
			if (!in_array(str_replace("'","",$txt_brand),$brand_arr))
			{
				$brand_name_id = return_id( str_replace("'","",$txt_brand), $brand_arr, "lib_brand", "id,brand_name","159");
				$brand_arr[$brand_name_id]=str_replace("'","",$txt_brand);
			}
			else $brand_name_id =  array_search(str_replace("'","",$txt_brand), $brand_arr);
		}
		else $brand_name_id=0;

		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$color_arr))
			{
				$color_name_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","159");
				$color_arr[$color_name_id]=str_replace("'","",$txt_color);
			}
			else $color_name_id =  array_search(str_replace("'","",$txt_color), $color_arr);
		}
		else $color_name_id=0;

		/*if(str_replace("'","",$txt_color)!="")
		{
			$color_name_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		}
		else
		{
			$color_name_id=0;
		}*/
		/*$brand_name_id=return_id( $txt_brand, $brand_arr, "lib_brand", "id,brand_name");
		if(str_replace("'","",$txt_color)!="")
		{
			$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		}
		else
		{
			$color_id=0;
		}*/

		$field_array2="job_no*order_id*process*fabric_description*cons_comp_id*gsm*dia_width*dia_width_type*machine_dia*machine_gg*no_of_roll*product_qnty*product_qnty_pcs*reject_qnty*uom_id*yarn_lot*yrn_count_id*brand*shift*floor_id*machine_id*stitch_len*color_range*color_id*remarks*operator_name*updated_by*update_date";
		$febric_description=str_replace("(",'[',str_replace(")",']',$txt_febric_description));
		$data_array2="".$txt_job_no."*".$order_no_id."*".$cbo_process."*".$febric_description."*".$hidd_comp_id."*".$txt_gsm."*".$txt_width."*".$cbo_dia_width_type."*".$txt_machine_dia."*".$txt_machine_gg."*".$txt_roll_qnty."*".$txt_product_qnty."*".$txt_product_qnty_pcs."*".$txt_reject_qnty."*".$cbo_uom."*".$txt_yarn_lot."*".$cbo_yarn_count."*".$txt_brand."*".$cbo_shift_id."*".$cbo_floor_id."*".$cbo_machine_name."*".$txt_stitch_len."*".$cbo_color_range."*'".$color_name_id."'*".$text_new_remarks."*".$txt_operator_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		/*
		|--------------------------------------------------------------------------
		| subcon_pro_roll_details
		| data preparing for
		| $data_array_roll and
		| $roll_data_array_update
		|--------------------------------------------------------------------------
		|
		*/
		$booking_without_order=0;
		$id_roll = return_next_id("id", "subcon_pro_roll_details", 1);
		if (str_replace("'", "", $txt_roll_maintained) == 1)
		{
			$roll_arr = return_library_array("select po_breakdown_id,max(roll_no) as roll_no from subcon_pro_roll_details where entry_form in(159)  group by po_breakdown_id", 'po_breakdown_id', 'roll_no');

		}

		$barcode_year = date("y");
		$barcode_suffix_no = return_field_value("max(barcode_suffix_no) as suffix_no", "subcon_pro_roll_details", "barcode_year=$barcode_year", "suffix_no") + 1;// and entry_form=2
		$barcode_no = $barcode_year . "02" . str_pad($barcode_suffix_no, 7, "0", STR_PAD_LEFT);
		$field_array_roll = "id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty,qc_pass_qnty_pcs, reject_qnty, roll_no, booking_without_order,product_size, inserted_by, insert_date";
		$field_array_roll_update = "po_breakdown_id*qnty*qc_pass_qnty*qc_pass_qnty_pcs*reject_qnty*roll_no*product_size*updated_by*update_date";

		$save_string = explode(",", str_replace("'", "", $save_data));
		$po_array = array();
		$po_reject_qty_array = array();
		$not_delete_roll_table_id = '';
		$all_barcode_no = "";
		for ($i = 0; $i < count($save_string); $i++)
		{
			$order_dtls = explode("_", $save_string[$i]);
			$order_qnty_roll_wise = $order_dtls[0];
			$order_qnty_pcs_roll_wise = $order_dtls[7];
			$size = $order_dtls[8];
			$roll_reject_qty = $order_dtls[1];
			$order_id = trim($order_dtls[2]);
			$roll_no = $order_dtls[3];
			$roll_id = $order_dtls[5];
			$roll_not_delete_id = $order_dtls[6];
			$roll_no = $roll_arr[$order_id] + 1;
			$roll_arr[$order_id] += 1;

			if ($order_id <= 0)
			{
				$order_id = '';
			}

			if ($roll_no == "")
			{
				$roll_no = $roll_arr[$order_id] + 1;
				$roll_arr[$order_id] += 1;
			}


			if ($roll_id == "" || $roll_id == 0)
			{
				if ($i == 0)
					$add_comma = "";
				else
					$add_comma = ",";

				$data_array_roll .= "$add_comma(" . $id_roll . "," . $barcode_year . "," . $barcode_suffix_no . "," . $barcode_no . "," . $update_id . "," . $update_id_dtl . "," . $order_id . ",159,'" . $order_qnty_roll_wise . "','". $order_qnty_roll_wise . "','" . $order_qnty_pcs_roll_wise . "','" . $roll_reject_qty . "','" . $roll_no . "','" .$booking_without_order. "','" .$size. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$all_barcode_no .= str_replace("'", "", $barcode_no) . ",";
				$id_roll = $id_roll + 1;
				$barcode_suffix_no = $barcode_suffix_no + 1;
				$barcode_no = $barcode_year . "02" . str_pad($barcode_suffix_no, 7, "0", STR_PAD_LEFT);
			}
			else
			{

				$roll_id_arr[] = $roll_id;
				$roll_data_array_update[$roll_id] = explode("*", ($order_id . "*'" . $order_qnty_roll_wise . "'*'" . $order_qnty_roll_wise. "'*'" . $order_qnty_pcs_roll_wise . "'*'" . $roll_reject_qty . "'*'" . $roll_no. "'*'" . $size  . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
			}

			if (array_key_exists($order_id, $po_array))
			{
				$po_array[$order_id] += $order_qnty_roll_wise;
				$po_array_pcs[$order_id] += $order_qnty_roll_wise_pcs;
				$po_reject_qty_array[$order_id] += $roll_reject_qty;
			}
			else
			{
				$po_array[$order_id] = $order_qnty_roll_wise;
				$po_array_pcs[$order_id] = $order_qnty_roll_wise_pcs;
				$po_reject_qty_array[$order_id] += $roll_reject_qty;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| barcode check before operation
		|--------------------------------------------------------------------------
		|
		*/
		if ($data_array_roll != "" && str_replace("'", "", $txt_roll_maintained) == 1)
		{
			$all_barcode_no = chop($all_barcode_no, ",");
			$barcod_check = return_field_value("barcode_no", "subcon_pro_roll_details", "status_active=1 and barcode_no in($all_barcode_no)", "barcode_no");

			if ($barcod_check != "")
			{
				echo "15**0**Database is busy now.";
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				die;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_production_mst
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0);
		if ($rID)
			$flag = 1;
		else
			$flag = 0;

		/*
		|--------------------------------------------------------------------------
		| subcon_production_dtls
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		if($flag == 1)
		{
			$rID2=sql_update("subcon_production_dtls",$field_array2,$data_array2,"id",$update_id_dtl,1);
			if ($rID2)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_pro_roll_details
		| data updating and inserting
		|--------------------------------------------------------------------------
		|
		*/
		if (str_replace("'", "", $txt_roll_maintained) == 1)
		{

			$txt_deleted_id = str_replace("'", "", $txt_deleted_id);
			if ($txt_deleted_id != "")
			{
				if ($flag == 1)
				{

					$field_array_status = "updated_by*update_date*status_active*is_deleted";
					$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
					$statusChange = sql_multirow_update("subcon_pro_roll_details", $field_array_status, $data_array_status, "id", $txt_deleted_id, 0);
					if ($statusChange)
						$flag = 1;
					else
						$flag = 0;
				}
			}

			if (count($roll_data_array_update) > 0)
			{
				if ($flag == 1)
				{


					$rollUpdate = execute_query(bulk_update_sql_statement("subcon_pro_roll_details", "id", $field_array_roll_update, $roll_data_array_update, $roll_id_arr));
					if ($rollUpdate)
						$flag = 1;
					else
						$flag = 0;
				}
			}

			if ($data_array_roll != "")
			{
				if ($flag == 1)
				{

					$rID5 = sql_insert("subcon_pro_roll_details", $field_array_roll, $data_array_roll, 0);
					if ($rID5)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
			//echo "10**".$rID.'='.$rID2.'='.$rollUpdate.'='.$rID5.'='.$flag;die;
		//	echo "10**insert into subcon_pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;

		if($db_type==0)
		{
			if ($flag == 1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_production_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_production_id);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==2)
		{
			if ($flag == 1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_production_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_production_id);
			}
		}
		disconnect($con);
 		die;
	}

	/*
	|--------------------------------------------------------------------------
	| Delete
	|--------------------------------------------------------------------------
	|
	*/
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_production_dtls
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("subcon_production_dtls",$field_array,$data_array,"id","".$update_id_dtl."",1);
		if ($rID)
			$flag = 1;
		else
			$flag = 0;

		/*
		|--------------------------------------------------------------------------
		| subcon_pro_roll_details
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$rID2=sql_delete("subcon_pro_roll_details",$field_array,$data_array,"mst_id","".$update_id."",1);
		if ($flag == 1)
		{
			if ($rID2)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==0)
		{
			if ($flag == 1)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		else
		{
			if ($flag == 1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl);
			}
		}
		disconnect($con);
		die;
	}
}

/*
|--------------------------------------------------------------------------
| roll_maintained
|--------------------------------------------------------------------------
|
*/
if ($action == "roll_maintained")
{
	/*
	$barcode_generation = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id ='$data' and variable_list=6 and is_deleted=0 and status_active=1");
	if ($barcode_generation == 2)
		$barcode_generation = $barcode_generation;
	else
		$barcode_generation = 1;
	*/

	$roll_maintained = '';
	$barcode_generation = 1;
	$inBoundSubContractProgram = 2;

	$fabricData = sql_select("select variable_list, dyeing_fin_bill from variable_settings_subcon where company_id ='$data' and variable_list in(5,6,15) and is_deleted=0 and status_active=1");
	foreach ($fabricData as $row)
	{
		if ($row[csf('variable_list')] == 5)
		{
			$roll_maintained = $row[csf('dyeing_fin_bill')];
		}

		if ($row[csf('variable_list')] == 6 && $row[csf('dyeing_fin_bill')] == 2)
		{
			$barcode_generation = 2;
		}

		if ($row[csf('variable_list')] == 15 && $row[csf('dyeing_fin_bill')] == 1)
		{
			$inBoundSubContractProgram = 1;
		}
	}

	//for production basis
	if($inBoundSubContractProgram == 1)
	{
		echo "$('#cbo_production_basis').val('2').attr('disabled', 'disabled');\n";
		echo "func_onchange_productionBasis(2);\n";
	}
	elseif($inBoundSubContractProgram == 2)
	{
		echo "$('#cbo_production_basis').val('1').attr('disabled', 'disabled');\n";
		echo "func_onchange_productionBasis(1);\n";
	}


	//for roll_maintained
	if ($roll_maintained == 1)
		$roll_maintained = $roll_maintained;
	else
		$roll_maintained = 0;

	echo "document.getElementById('txt_roll_maintained').value = '" . $roll_maintained . "';\n";
	echo "document.getElementById('barcode_generation').value = '" . $barcode_generation . "';\n";
	echo "document.getElementById('txt_product_qnty').value = '';\n";
	echo "document.getElementById('txt_reject_qnty').value = '';\n";
	echo "document.getElementById('txt_roll_qnty').value = '';\n";
	echo "document.getElementById('save_data').value = '';\n";
	echo "$('#roll_details_list_view').html('');\n";

	if ($roll_maintained == 1)
	{
		echo "$('#txt_product_qnty').attr('readonly','readonly');\n";
		echo "$('#txt_product_qnty').attr('onClick','openmypage_po();');\n";
		echo "$('#txt_product_qnty').attr('placeholder','Single Click');\n";
		echo "$('#txt_reject_qnty').attr('readonly','readonly');\n";
		echo "$('#txt_reject_qnty').attr('placeholder','Display');\n";
		echo "$('#txt_roll_qnty').attr('placeholder','Display');\n";
		echo "$('#txt_product_qnty_pcs').attr('readonly','readonly');\n";
	}
	else
	{
		echo "$('#txt_product_qnty').removeAttr('readonly','readonly');\n";
		echo "$('#txt_product_qnty').removeAttr('onClick','openmypage_po();');\n";
		echo "$('#txt_product_qnty').removeAttr('placeholder','Single Click');\n";
		echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";
		echo "$('#txt_reject_qnty').removeAttr('placeholder','Display');\n";
		echo "$('#txt_roll_qnty').removeAttr('placeholder','Display');\n";
		echo "$('#txt_product_qnty_pcs').removeAttr('readonly','readonly');\n";
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| remarks_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "remarks_popup")
{
	echo load_html_head_contents("SubCon Remarks Entry", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
		<script>
			function window_close()
			{
				var save_txt_remarks= $('#add_txt_remarks').val();
				$('#hidden_txt_remarks').val(save_txt_remarks);
				parent.emailwindow.hide();
			}
		</script>
	</head>
    <body>
    <div align="center">
    	<input type="hidden" name="hidden_txt_remarks" id="hidden_txt_remarks">
    	<textarea style="width:220px;height:220px; border: 1px solid #99B9E2;" id="add_txt_remarks" name="add_txt_remarks"><? echo $txt_remarks; ?></textarea>
    	<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px"/>
    </div>
	<?
}

/*
|--------------------------------------------------------------------------
| po_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "po_popup")
{
	echo load_html_head_contents("SubCon Prod Entry", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	  // echo $hdnOrderQty.'='.$hdnTotalProductQty;

	  		$fabricData = sql_select("select dyeing_fin_bill,allow_per from variable_settings_subcon where company_id ='$cbo_company_id' and variable_list in(16) and is_deleted=0 and status_active=1");
				$vari_material_issue=0;$allow_per=0;
				foreach($fabricData as $row)
				{
					$vari_material_issue=$row[csf('dyeing_fin_bill')];
					$allow_per=$row[csf('allow_per')];

				}
				//Validation about Yarn Issue and Knitting Production********Variable Seting*********
				//echo $vari_material_issue.'d';
				if($cbo_production_basis==1){
					$msg_ttl="Order";}else{
					$msg_ttl="Program";}

				if($vari_material_issue==1)
				{
					 $po_sql= "SELECT b.id AS ord_id, a.quantity, b.order_no, b.delivery_date, b.order_quantity FROM sub_material_dtls a, subcon_ord_dtls b WHERE a.order_id = b.id AND a.status_active = 1 AND b.status_active = 1 AND b.id = ".$all_po_id."  and a.item_category_id=1  ORDER BY b.id asc";
					$po_sql_res=sql_select($po_sql);
					foreach($po_sql_res as $row)
					{
						$yarn_issue_qty+=$row[csf('quantity')];

					}
					$hdnOrderQty=0;
					$hdnOrderQty=$yarn_issue_qty+(($yarn_issue_qty*$allow_per)/100);
					$msg_ttl="Yarn Issue";
				}
				 // echo $hdnOrderQty.'='.$yarn_issue_qty.'d';
	?>
    <script>
        var permission = '<? echo $permission; ?>';
		var roll_maintained =<? echo $roll_maintained; ?>;

        function fn_addRow_row(i) {
            //var row_num=$('#tbl_item_details tbody tr').length;
            //var lastTrId = $('#tbl_list tbody tr:last').attr('id').split('_');
            //alert(lastTrId);
            //var row_num=lastTrId[1];
            var row_num = $('#tbl_list tbody tr').length;
            //alert(lastTrId[1]);
            //if (row_num != i) {
                //return false;
            //}
           // else {
                i++;

                $("#tbl_list tbody tr:last").clone().find("input,select").each(function () {

                    $(this).attr({
                        'id': function (_, id) {
                            var id = id.split("_");
                            return id[0] + "_" + i
                        },
                        'name': function (_, name) {
                            return name
                        },
                        'value': function (_, value) {
                            return value
                        }
                    });

                }).end().appendTo("#tbl_list");

               // $('#slTd_' + i).val('');
                $('#txtqcpassqty_' + i).val('');
                $('#txtqcpassqtypcs_' + i).val('');
                $('#txtrejectqty_' + i).val('');
                $('#txtrollno_' + i).val('');
				$('#txtsize_' + i).val('');
				 $('#txtbarcode_' + i).val('');
				 $('#txtRollTableId_' + i).val('');
                $("#tbl_list tbody tr:last").removeAttr('id').attr('id', 'tr_' + i);
               // $('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id', 'slTd_' + i);
                //$('#tr_' + i).find("td:eq(0)").text(i);

                $('#increase_' + i).removeAttr("value").attr("value", "+");
                $('#decrease_' + i).removeAttr("value").attr("value", "-");
                $('#increase_' + i).removeAttr("onclick").attr("onclick", "fn_addRow_row(" + i + ");");
                $('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
            //}
            set_all_onclick();
        }

        function fn_deleteRow(rowNo) {

            var row_num = $('#tbl_list tbody tr').length;
          //  if (row_num != 1) {
                //alert(row_num);
               // $("#tr_" + rowNo).remove();
				 var txtOrginal = $('#txtOrginal_' + rowNo).val() * 1;
                var txtBarcodeNo = $('#txtBarcodeNo_' + rowNo).val();
                var txtRollId = $('#txtRollTableId_' + rowNo).val();
                var txt_deleted_id = $('#hide_deleted_id').val();
                var selected_id = '';
               // if (txtOrginal == 0) {
                    if (txtBarcodeNo != '') {
                        if (txt_deleted_id == '') selected_id = txtRollId; else selected_id = txt_deleted_id + ',' + txtRollId;
                        $('#hide_deleted_id').val(selected_id);
						$("#tr_" + rowNo).remove();
                   // }


                }

           // }
			 calculate_qc_qnty();
        }

        function window_close()
		{
            var save_string = '';
            var tot_qc_qnty = '';
             var tot_qc_qnty_pcs = '';
			var tot_reject_qnty = '';
			var no_of_roll = '';
			var hdnOrderQty = '<?php echo $hdnOrderQty*1; ?>';
			var hdnTotalProductQty = '<?php echo $hdnTotalProductQty*1; ?>';
			var totalQcQty = '';
			var flag=0;
			$("#tbl_list tbody").find('tr').each(function () {
				var qcQty = $(this).find('input[name="txtqcpassqty[]"]').val();
            	totalQcQty = totalQcQty * 1 + qcQty * 1;
            	if(hdnOrderQty*1 < (hdnTotalProductQty*1+totalQcQty*1))
				{
					flag+=1;
				}
            });

            $("#tbl_list").find('tr').each(function () {
                var txtqcpassqty = $(this).find('input[name="txtqcpassqty[]"]').val();
                var txtqcpassqtypcs = $(this).find('input[name="txtqcpassqtypcs[]"]').val();
                var txtrejectqty = $(this).find('input[name="txtrejectqty[]"]').val();
                var txtrollno = $(this).find('input[name="txtrollno[]"]').val();
				var txtbarcode = $(this).find('input[name="txtbarcode[]"]').val();
				var all_po_id = $(this).find('input[name="all_po_id[]"]').val();
				var txtRollTableId = $(this).find('input[name="txtRollTableId[]"]').val();
				var txtRollId = $(this).find('input[name="txtRollId[]"]').val();
				var txtsize = $(this).find('input[name="txtsize[]"]').val();

				if(roll_maintained==1)
				{
					txtrollno=txtrollno;
					txtbarcode=txtbarcode;
				}
				else
				{
					txtrollno=0;
					txtbarcode=0;
				}

                if (txtqcpassqty * 1 > 0) {

                    if (save_string == "") {
                        save_string = txtqcpassqty + "_" + txtrejectqty + "_" + all_po_id + "_" + txtrollno + "_" + txtbarcode+ "_" + txtRollTableId+ "_" + txtRollId+ "_" + txtqcpassqtypcs+ "_" + txtsize;
                    } else {
                        save_string += "," + txtqcpassqty + "_" + txtrejectqty + "_" + all_po_id + "_" + txtrollno + "_" + txtbarcode+ "_" + txtRollTableId+ "_" + txtRollId+ "_" + txtqcpassqtypcs+ "_" + txtsize;
                    }
                    tot_qc_qnty = tot_qc_qnty * 1 + txtqcpassqty * 1;
                     tot_qc_qnty_pcs = tot_qc_qnty_pcs * 1 + txtqcpassqtypcs * 1;
					tot_reject_qnty = tot_reject_qnty * 1 + txtrejectqty * 1;
					no_of_roll = no_of_roll * 1 + 1;
                }
            });
            if(flag==1){alert('Product qty. is larger than <? echo $msg_ttl;?> qty.');return;}
           // alert(save_string);
            $('#save_string').val(save_string);
            $('#tot_qc_qnty').val(tot_qc_qnty);
             $('#tot_qc_qnty_pcs').val(tot_qc_qnty_pcs);
			$('#tot_reject_qnty').val(tot_reject_qnty);
			$('#number_of_roll').val(no_of_roll);
			//$('#po_id').val(all_po_id);
            parent.emailwindow.hide();
        }

        function calculate_qc_qnty()
		{
            var total_qc_qnty = '';
            var total_qc_qnty_pcs = '';
			var total_reject_qnty = '';
            $("#tbl_list tbody").find('tr').each(function () {
				var txtqcpassqty = $(this).find('input[name="txtqcpassqty[]"]').val();
				var txtqcpassqtypcs = $(this).find('input[name="txtqcpassqtypcs[]"]').val();
				var txtrejectqty = $(this).find('input[name="txtrejectqty[]"]').val();
				total_qc_qnty = total_qc_qnty * 1 + txtqcpassqty * 1;
				total_qc_qnty_pcs = total_qc_qnty_pcs * 1 + txtqcpassqtypcs * 1;
				total_reject_qnty = total_reject_qnty * 1 + txtrejectqty * 1;
            });

            $('#txt_total_qcpass_qnty').val(total_qc_qnty.toFixed(2));
              $('#txt_total_qcpass_qnty_pcs').val(total_qc_qnty_pcs.toFixed(2));
			$('#txt_total_reject_qnty').val(total_reject_qnty.toFixed(2));

        }

		function func_production_qty_check_zs(isUpdate)
		{
			//alert('su..re');
			var txtProductQty = '<?php echo $txt_product_qnty*1; ?>';
			var hdnProductQty = '<?php echo $hdnProductQty*1; ?>';
			var hdnOrderQty = '<?php echo $hdnOrderQty*1; ?>';
			var hdnTotalProductQty = '<?php echo $hdnTotalProductQty*1; ?>';
			var hdnBalanceQty = '<?php echo $hdnBalanceQty*1; ?>';
			//alert(txtProductQty+'='+hdnProductQty+'='+hdnOrderQty+'='+hdnTotalProductQty+'='+hdnBalanceQty);
			//20=20=100=20=80

			var totalQcQty = '';
			var totalRejectQty = '';
            $("#tbl_list tbody").find('tr').each(function () {
				var qcQty = $(this).find('input[name="txtqcpassqty[]"]').val();
				var rejectQty = $(this).find('input[name="txtrejectqty[]"]').val();
				totalQcQty = totalQcQty * 1 + qcQty * 1;
				totalRejectQty = totalRejectQty * 1 + rejectQty * 1;
            });

			if(isUpdate == 0)
			{
				//alert(hdnOrderQty+'='+(hdnTotalProductQty*1+totalQcQty*1));
				if(hdnOrderQty*1 < (hdnTotalProductQty*1+totalQcQty*1))
				{
					//alert('Product qty. is larger than <? echo $msg_ttl;?> qty.');
					alert('Previous Prod Qty='+hdnTotalProductQty+'\nTotal Production Qty not allowed over than <? echo $msg_ttl;?> Qty');
					$('#txtqcpassqty_1').val('0');
					return;
				}
			}
			else if(isUpdate == 1)
			{
				if(hdnOrderQty*1 < (hdnTotalProductQty*1+totalQcQty*1))
				{
					//alert('Product qty. is larger than <? echo $msg_ttl;?> qty.');
					alert('Previous Prod Qty='+hdnTotalProductQty+'\n  Total Production Qty not allowed over than <? echo $msg_ttl;?> Qty');
					$('#txtqcpassqty_1').val(txtProductQty);
					return;
				}
			}
		}
    </script>
    </head>
    <body>
    <div align="center">
		<? echo load_freeze_divs("../../", $permission, 1); ?>
        <form name="ProductionQty_1" id="ProductionQty_1">
			<?

			$disable="";
			if($uom==1){
				$disablePcs="disabled";
				$disableSize="disabled";
				$disableKg="disabled";
			}
			if($uom==12){
				$disableKg="disabled";
			}

			if($roll_maintained==1)
			{
				$width = "1020";
			}
			else
			{
				$width = "820";
			}

			//Check delivery
			$sql_chk_delivery_fin=sql_select("select b.order_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id=".$all_po_id." AND a.company_id = ".$cbo_company_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
			if(count($sql_chk_delivery_fin)>0)
			{
				$disableKg="disabled";
			}
			else
			{
				$disableKg="";
			}


			?>
            <fieldset style="width:<? echo $width;?>px; margin-top:10px">
                <legend>SubCon Knitting Production Pop Up</legend>
				<?

				//echo $hdnOrderQty.'DDS';
				//$po_sql= "select b.id as ord_id, a.subcon_job, b.order_no, b.delivery_date, b.order_quantity from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.id=$all_po_id and a.company_id=$cbo_company_id order by a.id DESC";
				$po_sql= "SELECT b.id AS ord_id, a.subcon_job, b.order_no, b.delivery_date, b.order_quantity FROM subcon_ord_mst a, subcon_ord_dtls b WHERE a.subcon_job = b.job_no_mst AND a.status_active = 1 AND b.status_active = 1 AND b.id = ".$all_po_id." AND a.company_id = ".$cbo_company_id." ORDER BY a.id DESC";
				//echo $po_sql;
				$po_result=sql_select($po_sql);
				foreach($po_result as $row)
				{
					$po_qty=$row[csf('order_quantity')];
					$po_ship_date=$row[csf('delivery_date')];
					$order_no=$row[csf('order_no')];
					$po_id=$row[csf('ord_id')];
				}
				$po_qty=$hdnOrderQty;

				if ($save_data != "")
				{
					?>
                    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $width;?>" id="tbl_list">
                        <thead>
                        <th width="120">PO No</th>
                        <th width="100">PO/Program Qnty</th>
                        <th width="100">Ship. Date</th>
                        <th width="100">QC Pass Qty.</th>
                         <th width="100">QC Pass Qty.(pcs)</th>
						<th width="80">Size</th>
                        <th width="100">Reject Qty.</th>
                        <? if($roll_maintained==1)
						{
						 ?>
                        <th width="100">Roll</th>
                        <th width="100">Barcode No.</th>
                        <?
						}
                        ?>
                        <th  width="100"></th>
                        <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
                        <input type="hidden" name="tot_qc_qnty" id="tot_qc_qnty" class="text_boxes">

                         <input type="hidden" name="tot_qc_qnty_pcs" id="tot_qc_qnty_pcs" class="text_boxes">
                        <input type="hidden" name="tot_reject_qnty" id="tot_reject_qnty" class="text_boxes">
                        <input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
                        <input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
                        </thead>
                        <tbody>
						<?
						$tot_qc_qty = 0;
						$tot_reject_qty = 0;
						$orginal_val = 0;
						$k = 0;$po_array = array();
						$explSaveData = explode(",", $save_data);

						for ($z = 0; $z < count($explSaveData); $z++) {
							$data_all = explode("_", $explSaveData[$z]);
							$qc_qty = $data_all[0];
							$reject_qty = $data_all[1];
							$sub_po_id = $data_all[2];
							$roll_no = $data_all[3];
							$barcode_no = $data_all[4];
							$roll_id = $data_all[5];
							$roll_not_delete_id = $data_all[6];
							$qc_qty_pcs = $data_all[7];
							$size = $data_all[8];
							$tot_qc_qty += $qc_qty;
							$tot_qc_qty_pcs += $qc_qty_pcs;
							$tot_reject_qty += $reject_qty;
							//
							if ($roll_maintained == 1) {
								$roll_used = return_field_value("roll_used", "subcon_pro_roll_details", "id='$roll_id'");

								if (!(in_array($sub_po_id, $po_array))) {
									//echo $sub_po_id;
									$orginal_val = 1;
									$po_array[] = $sub_po_id;
								} else {
									if ($roll_used == 1) $orginal_val = 1; else $orginal_val = 0;
								}

								if ($roll_used == 1) {
									$disable = "disabled='disabled'";
									$roll_not_delete_id = $roll_not_delete_id;
								} else {
									$disable = "";
									$roll_not_delete_id = 0;
								}
							}
							$k++;




							?>
                            <tr id="tr_<? echo $k; ?>">
                             	<td width="120" align="center"><? echo $order_no; ?>
                                     <input type="hidden" name="all_po_id[]" id="all_po_id_<? echo $k; ?>" class="text_boxes" value="<? echo $sub_po_id; ?>">
                                     <input type="hidden" name="txtRollTableId[]"  id="txtRollTableId_<? echo $k; ?>" value="<? echo $roll_id; ?>">
                                     <input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
                                     <input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
                                </td>
                                <td width="100" align="center"><? echo number_format($po_qty,0); ?></td>
                                <td width="100" align="center"><? echo change_date_format($po_ship_date); ?></td>
                                <td>
                                	<input type="text" name="txtqcpassqty[]" id="txtqcpassqty_<? echo $k; ?>" class="text_boxes_numeric" onKeyUp="func_production_qty_check_zs('1'); calculate_qc_qnty();" style="width:100px;" value="<? echo number_format($qc_qty,3); ?>" <?=$disableKg;?>/>
                                </td>
                                 <td>
                                	<input type="text" name="txtqcpassqtypcs[]" id="txtqcpassqtypcs_<? echo $k; ?>" class="text_boxes_numeric" onKeyUp="func_production_qty_check_zs('1'); calculate_qc_qnty();" style="width:100px;" value="<? echo number_format($qc_qty_pcs,3); ?>" <?=$disablePcs;?>/>
                                </td>
								<td>
                                	<input type="text" name="txtsize[]" id="txtsize_<? echo $k; ?>" class="text_boxes"  style="width:80px;" value="<? echo $size; ?>" readonly/>
                                </td>
                                <td>
                                    <input type="text" name="txtrejectqty[]" id="txtrejectqty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate_qc_qnty();" value="<? echo $reject_qty; ?>"/>
                                </td>
								<?
								if($roll_maintained==1)
                                {
                                ?>
                                <td>

									<input type="text" name="txtrollno[]" id="txtrollno_<? echo $k; ?>" class="text_boxes" style="width:100px;" value="<? echo $roll_no; ?>" readonly/>
                                </td>
                                 <td>
                                    <input type="text" name="txtbarcode[]" id="txtbarcode_<? echo $k; ?>" class="text_boxes" style="width:100px;" value="<? echo $barcode_no; ?>" readonly/>
                                </td>
                                <?
									}
                                ?>

                                <td>
                                    <input type="button" id="increase_<? echo $k; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_row(<? echo $k; ?>)"/>
                                    <input type="button" id="decrease_<? echo $k; ?>" name="decrease[]"  style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $k; ?>);"/>
                                </td>
                            </tr>
							<?
						}
						?>

                        </tbody>
                        <tfoot class="tbl_bottom">
                         <td colspan="3">Sum</td>
                        <td><input type="text" name="txt_total_qcpass_qnty" id="txt_total_qcpass_qnty" class="text_boxes_numeric"  value="<? echo $tot_qc_qty; ?>" style="width:100px" readonly/></td>
                         <td><input type="text" name="txt_total_qcpass_qnty_pcs" id="txt_total_qcpass_qnty_pcs" class="text_boxes_numeric"  value="<? echo $tot_qc_qty_pcs; ?>" style="width:100px" readonly/></td>
						 <td>&nbsp;</td>
                        <td><input type="text" name="txt_total_reject_qnty" id="txt_total_reject_qnty" class="text_boxes_numeric"  value="<? echo $tot_reject_qty; ?>" style="width:100px" readonly/></td>
                        <? if($roll_maintained==1)
							{
						 ?>
                         <td colspan="3">&nbsp;</td>
                         <?
							}
						 ?>
                        </tfoot>
                    </table>
                    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $width;?>">

                        <tr>
                            <td colspan="9" align="center">

                                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px"/>

                            </td>
                        </tr>
                    </table>
					<?
				}
				else
				{
					$roll_id=0;
					$roll_not_delete_id = 0;
					$orginal_val = 1;
					$k = 1;
					?>
                    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $width;?>" id="tbl_list">
                        <thead>
                        <th width="120">PO No</th>
                        <th width="100">PO/Program Qnty</th>
                        <th width="100">Ship. Date</th>
                        <th width="100">QC Pass Qty.</th>
                        <th width="100">QC Pass Qty.(pcs)</th>
						<th width="80">Size</th>
                        <th width="100">Reject Qty.</th>
                        <?
						if($roll_maintained==1)
						{
							?>
							<th width="100">Roll</th>
							<th width="100">Barcode No.</th>
							<?
						}
						?>
                        <th  width="100"></th>
                        <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
                        <input type="hidden" name="tot_qc_qnty" id="tot_qc_qnty" class="text_boxes">
                         <input type="hidden" name="tot_qc_qnty_pcs" id="tot_qc_qnty_pcs" class="text_boxes">
                        <input type="hidden" name="tot_reject_qnty" id="tot_reject_qnty" class="text_boxes">
                        <input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
                        <input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
                        </thead>
                        <tbody>
                        <tr id="tr_1">
                             	<td width="120" align="center"><? echo $order_no; ?>
                                 <input type="hidden" name="all_po_id[]" id="all_po_id_<? echo $k; ?>" class="text_boxes" value="<? echo $po_id; ?>">
                                 <input type="hidden" name="txtRollTableId[]"  id="txtRollTableId_<? echo $k; ?>" value="<? echo $roll_id; ?>">
                                 <input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
                                 <input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
                                </td>
                                <td width="100" align="center"><? echo number_format($po_qty,0); ?></td>
                                <td width="100" align="center"><? echo change_date_format($po_ship_date); ?></td>

                                 <td>
                                    <input type="text" name="txtqcpassqty[]" id="txtqcpassqty_<? echo $k; ?>" onKeyUp="func_production_qty_check_zs('0'); calculate_qc_qnty();" class="text_boxes_numeric" style="width:100px;"  <?=$disable;?> />
                                </td>
                                 <td>
                                    <input type="text" name="txtqcpassqtypcs[]" id="txtqcpassqtypcs_<? echo $k; ?>" onKeyUp="func_production_qty_check_zs('0'); calculate_qc_qnty();" class="text_boxes_numeric" style="width:100px;" value="<? echo $product_qnty_pcs; ?>"/>
                                </td>
								<td>
                                    <input type="text" name="txtsize[]" id="txtsize_<? echo $k; ?>" class="text_boxes" style="width:80px;" value="<? echo $size; ?>"/>
                                </td>
                                <td>
                                    <input type="text" name="txtrejectqty[]" id="txtrejectqty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate_qc_qnty();" />
                                </td>
								<?
								if($roll_maintained==1)
                                {
                                ?>
                                <td>
                                    <input type="text" name="txtrollno[]" id="txtrollno_<? echo $k; ?>" class="text_boxes" style="width:100px;" readonly/>
                                </td>
                                <td>
                                    <input type="text" name="txtbarcode[]" id="txtbarcode_<? echo $k; ?>" class="text_boxes" style="width:100px;" readonly />
                                </td>
								<?
                                }
                                ?>
                                <td>
                                    <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_row(1)"/>
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="tbl_bottom">
                        <td colspan="3">Sum</td>
                        <td><input type="text" name="txt_total_qcpass_qnty" id="txt_total_qcpass_qnty" class="text_boxes_numeric" style="width:100px" readonly/></td>
                         <td><input type="text" name="txt_total_qcpass_qnty_pcs" id="txt_total_qcpass_qnty_pcs" class="text_boxes_numeric" style="width:100px" readonly/></td>
						 <td >&nbsp;</td>
                        <td><input type="text" name="txt_total_reject_qnty" id="txt_total_reject_qnty" class="text_boxes_numeric" style="width:100px" readonly/></td>
						<?
						if($roll_maintained==1)
                        {
							?>
							<td colspan="3">&nbsp;</td>
							<?
                        }
                        ?>
                        </tfoot>
                    </table>
                    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $width;?>">
                        <tr>
                            <td colspan="9" align="center">
                                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px"/>

                            </td>
                        </tr>
                    </table>
				<?
                }
                ?>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}

if ($action == "po_popup---")
{
	echo load_html_head_contents("SubCon Prod Entry", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//echo $all_po_id;
	?>
    <script>
        var permission = '<? echo $permission; ?>';
		var roll_maintained =<? echo $roll_maintained; ?>;

        function fn_addRow_row(i) {
            //var row_num=$('#tbl_item_details tbody tr').length;
            //var lastTrId = $('#tbl_list tbody tr:last').attr('id').split('_');
            //alert(lastTrId);
            //var row_num=lastTrId[1];
            var row_num = $('#tbl_list tbody tr').length;
            //alert(lastTrId[1]);
            if (row_num != i) {
                return false;
            }
            else {
                i++;

                $("#tbl_list tbody tr:last").clone().find("input,select").each(function () {

                    $(this).attr({
                        'id': function (_, id) {
                            var id = id.split("_");
                            return id[0] + "_" + i
                        },
                        'name': function (_, name) {
                            return name
                        },
                        'value': function (_, value) {
                            return value
                        }
                    });

                }).end().appendTo("#tbl_list");

               // $('#slTd_' + i).val('');
                $('#txtqcpassqty_' + i).val('');
                $('#txtrejectqty_' + i).val('');
                $('#txtrollno_' + i).val('');
				 $('#txtbarcode_' + i).val('');
				 $('#txtRollTableId_' + i).val('');
                $("#tbl_list tbody tr:last").removeAttr('id').attr('id', 'tr_' + i);
               // $('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id', 'slTd_' + i);
                //$('#tr_' + i).find("td:eq(0)").text(i);

                $('#increase_' + i).removeAttr("value").attr("value", "+");
                $('#decrease_' + i).removeAttr("value").attr("value", "-");
                $('#increase_' + i).removeAttr("onclick").attr("onclick", "fn_addRow_row(" + i + ");");
                $('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
            }
            set_all_onclick();
        }

        function fn_deleteRow(rowNo) {

            var row_num = $('#tbl_list tbody tr').length;
          //  if (row_num != 1) {
                //alert(row_num);
               // $("#tr_" + rowNo).remove();
				 var txtOrginal = $('#txtOrginal_' + rowNo).val() * 1;
                var txtBarcodeNo = $('#txtBarcodeNo_' + rowNo).val();
                var txtRollId = $('#txtRollTableId_' + rowNo).val();
                var txt_deleted_id = $('#hide_deleted_id').val();
                var selected_id = '';
               // if (txtOrginal == 0) {
                    if (txtBarcodeNo != '') {
                        if (txt_deleted_id == '') selected_id = txtRollId; else selected_id = txt_deleted_id + ',' + txtRollId;
                        $('#hide_deleted_id').val(selected_id);
						$("#tr_" + rowNo).remove();
                   // }


                }

           // }
			 calculate_qc_qnty();
        }

        function window_close() {
            var save_string = '';
            var tot_qc_qnty = '';
			var tot_reject_qnty = '';
			  var no_of_roll = '';

            $("#tbl_list").find('tr').each(function () {
                var txtqcpassqty = $(this).find('input[name="txtqcpassqty[]"]').val();
                var txtrejectqty = $(this).find('input[name="txtrejectqty[]"]').val();
                var txtrollno = $(this).find('input[name="txtrollno[]"]').val();
				var txtbarcode = $(this).find('input[name="txtbarcode[]"]').val();
				var all_po_id = $(this).find('input[name="all_po_id[]"]').val();
				var txtRollTableId = $(this).find('input[name="txtRollTableId[]"]').val();
				var txtRollId = $(this).find('input[name="txtRollId[]"]').val();

				if(roll_maintained==1)
				{
					txtrollno=txtrollno;
					txtbarcode=txtbarcode;
				}
				else
				{
					txtrollno=0;
					txtbarcode=0;
				}

                if (txtqcpassqty * 1 > 0) {

                    if (save_string == "") {
                        save_string = txtqcpassqty + "_" + txtrejectqty + "_" + all_po_id + "_" + txtrollno + "_" + txtbarcode+ "_" + txtRollTableId+ "_" + txtRollId;
                    }
                    else {
                        save_string += "," + txtqcpassqty + "_" + txtrejectqty + "_" + all_po_id + "_" + txtrollno + "_" + txtbarcode+ "_" + txtRollTableId+ "_" + txtRollId;
                    }
                    tot_qc_qnty = tot_qc_qnty * 1 + txtqcpassqty * 1;
					tot_reject_qnty = tot_reject_qnty * 1 + txtrejectqty * 1;
					no_of_roll = no_of_roll * 1 + 1;

                }

            });
           // alert(save_string);
            $('#save_string').val(save_string);
            $('#tot_qc_qnty').val(tot_qc_qnty);
			$('#tot_reject_qnty').val(tot_reject_qnty);
			 $('#number_of_roll').val(no_of_roll);
			//$('#po_id').val(all_po_id);
            parent.emailwindow.hide();
        }

        function calculate_qc_qnty() {
            var total_qc_qnty = '';  var total_reject_qnty = '';
            $("#tbl_list tbody").find('tr').each(function () {
                var txtqcpassqty = $(this).find('input[name="txtqcpassqty[]"]').val();
				 var txtrejectqty = $(this).find('input[name="txtrejectqty[]"]').val();
                total_qc_qnty = total_qc_qnty * 1 + txtqcpassqty * 1;
				 total_reject_qnty = total_reject_qnty * 1 + txtrejectqty * 1;
            });

            $('#txt_total_qcpass_qnty').val(total_qc_qnty.toFixed(2));
			$('#txt_total_reject_qnty').val(total_reject_qnty.toFixed(2));

        }

    </script>
    </head>
    <body>
    <div align="center">
		<? echo load_freeze_divs("../../", $permission, 1); ?>
        <form name="ProductionQty_1" id="ProductionQty_1">
			<?
			if($roll_maintained==1)
			{
				$width = "820";
			}
			else
			{
				$width = "620";
			}
			?>
            <fieldset style="width:<? echo $width;?>px; margin-top:10px">
                <legend>SubCon Knitting Production Pop Up</legend>
				<?
				$po_sql= "select b.id as ord_id, a.subcon_job, b.order_no, b.delivery_date, b.order_quantity from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.id=$all_po_id and a.company_id=$cbo_company_id order by a.id DESC";
				//echo $po_sql;
				$po_result=sql_select($po_sql);
				foreach($po_result as $row)
				{
					$po_qty=$row[csf('order_quantity')];
					$po_ship_date=$row[csf('delivery_date')];
					$order_no=$row[csf('order_no')];
					$po_id=$row[csf('ord_id')];
				}

				if ($save_data != "")
				{
					?>
                    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $width;?>" id="tbl_list">
                        <thead>
                        <th width="120">PO No</th>
                        <th width="100">PO Qnty</th>
                        <th width="100">Ship. Date</th>
                        <th width="100">QC Pass Qty.</th>
                        <th width="100">Reject Qty.</th>
                        <? if($roll_maintained==1)
						{
						 ?>
                        <th width="100">Roll</th>
                        <th width="100">Barcode No.</th>
                        <?
						}
                        ?>
                        <th  width="100"></th>
                        <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
                        <input type="hidden" name="tot_qc_qnty" id="tot_qc_qnty" class="text_boxes">
                        <input type="hidden" name="tot_reject_qnty" id="tot_reject_qnty" class="text_boxes">
                         <input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
                         <input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
                        </thead>
                        <tbody>

						<?
						$tot_qc_qty = 0;$tot_reject_qty = 0;$orginal_val = 0;
						$k = 0;$po_array = array();
						$explSaveData = explode(",", $save_data);
						for ($z = 0; $z < count($explSaveData); $z++) {
							$data_all = explode("_", $explSaveData[$z]);
							$qc_qty = $data_all[0];
							$reject_qty = $data_all[1];
							$sub_po_id = $data_all[2];
							$roll_no = $data_all[3];
							$barcode_no = $data_all[4];
							$roll_id = $data_all[5];
							$roll_not_delete_id = $data_all[6];
							$tot_qc_qty += $qc_qty;
							$tot_reject_qty += $reject_qty;
							//
							if ($roll_maintained == 1) {
							$roll_used = return_field_value("roll_used", "subcon_pro_roll_details", "id='$roll_id'");

							if (!(in_array($sub_po_id, $po_array))) {
								//echo $sub_po_id;
								$orginal_val = 1;
								$po_array[] = $sub_po_id;
							} else {
								if ($roll_used == 1) $orginal_val = 1; else $orginal_val = 0;
							}

							if ($roll_used == 1) {
								$disable = "disabled='disabled'";
								$roll_not_delete_id = $roll_not_delete_id;
							} else {
								$disable = "";
								$roll_not_delete_id = 0;
							}
						 }
							$k++;

							?>
                            <tr id="tr_<? echo $k; ?>">
                             	<td width="120"><? echo $order_no; ?>
                                 <input type="hidden" name="all_po_id[]" id="all_po_id_<? echo $k; ?>" class="text_boxes" value="<? echo $sub_po_id; ?>">
                                 <input type="hidden" name="txtRollTableId[]"  id="txtRollTableId_<? echo $k; ?>" value="<? echo $roll_id; ?>">
                                  <input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
                                   <input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
                                </td>
                                 <td width="100" align="right"><? echo number_format($po_qty,0); ?></td>
                                <td width="100"><? echo change_date_format($po_ship_date); ?></td>
                                <td>
                                    <input type="text" name="txtqcpassqty[]" id="txtqcpassqty_<? echo $k; ?>" class="text_boxes_numeric" onKeyUp="calculate_qc_qnty();" style="width:100px;" value="<? echo $qc_qty; ?>"/>
                                </td>
                                <td>
                                    <input type="text" name="txtrejectqty[]" id="txtrejectqty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate_qc_qnty();" value="<? echo $reject_qty; ?>"/>
                                </td>
                                  <? if($roll_maintained==1)
									{
						 			?>
                                <td>

                                    <input type="text" name="txtrollno[]" id="txtrollno_<? echo $k; ?>" class="text_boxes" style="width:100px;" value="<? echo $roll_no; ?>" readonly/>
                                </td>
                                 <td>
                                    <input type="text" name="txtbarcode[]" id="txtbarcode_<? echo $k; ?>" class="text_boxes" style="width:100px;" value="<? echo $barcode_no; ?>" readonly/>
                                </td>
                                <?
									}
                                ?>

                                <td>
                                    <input type="button" id="increase_<? echo $k; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_row(<? echo $k; ?>)"/>
                                    <input type="button" id="decrease_<? echo $k; ?>" name="decrease[]"  style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $k; ?>);"/>
                                </td>
                            </tr>
							<?
						}
						?>

                        </tbody>
                        <tfoot class="tbl_bottom">
                         <td colspan="3">Sum</td>
                        <td><input type="text" name="txt_total_qcpass_qnty" id="txt_total_qcpass_qnty" class="text_boxes_numeric"  value="<? echo $tot_qc_qty; ?>" style="width:100px" readonly/></td>
                        <td><input type="text" name="txt_total_reject_qnty" id="txt_total_reject_qnty" class="text_boxes_numeric"  value="<? echo $tot_reject_qty; ?>" style="width:100px" readonly/></td>
                        <? if($roll_maintained==1)
							{
						 ?>
                         <td colspan="3">&nbsp;</td>
                         <?
							}
						 ?>
                        </tfoot>
                    </table>
                    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $width;?>">

                        <tr>
                            <td colspan="7" align="center">

                                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px"/>

                            </td>
                        </tr>
                    </table>

					<?
				}
				else
				{
					$roll_id=0;
					$roll_not_delete_id = 0;$orginal_val = 1;
					?>
                    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $width;?>" id="tbl_list">
                        <thead>
                        <th width="120">PO No</th>
                        <th width="100">PO Qnty</th>
                        <th width="100">Ship. Date</th>
                        <th width="100">QC Pass Qty.</th>
                        <th width="100">Reject Qty.</th>
                        <?
						if($roll_maintained==1)
						{
						?>
                        <th width="100">Roll</th>
                        <th width="100">Barcode No.</th>
                        <?
						}
						?>
                        <th  width="100"></th>
                        <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
                        <input type="hidden" name="tot_qc_qnty" id="tot_qc_qnty" class="text_boxes">
                        <input type="hidden" name="tot_reject_qnty" id="tot_reject_qnty" class="text_boxes">
                        <input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
                        <input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
                        </thead>
                        <tbody>
                        <tr id="tr_1">
                             	<td width="120"><? echo $order_no; ?>
                                 <input type="hidden" name="all_po_id[]" id="all_po_id_<? echo $k; ?>" class="text_boxes" value="<? echo $po_id; ?>">
                                 <input type="hidden" name="txtRollTableId[]"  id="txtRollTableId_<? echo $k; ?>" value="<? echo $roll_id; ?>">
                                 <input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
                                 <input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
                                </td>
                                <td width="100" align="right"><? echo number_format($po_qty,0); ?></td>
                                <td width="100"><? echo change_date_format($po_ship_date); ?></td>
                                <td>
                                    <input type="text" name="txtqcpassqty[]" id="txtqcpassqty_<? echo $k; ?>" onKeyUp="calculate_qc_qnty();" class="text_boxes_numeric" style="width:100px;" />
                                </td>
                                <td>
                                    <input type="text" name="txtrejectqty[]" id="txtrejectqty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate_qc_qnty();" />
                                </td>
                                  <? if($roll_maintained==1)
									{
								 ?>
                                <td>
                                    <input type="text" name="txtrollno[]" id="txtrollno_<? echo $k; ?>" class="text_boxes" style="width:100px;" readonly/>
                                </td>
                                 <td>
                                    <input type="text" name="txtbarcode[]" id="txtbarcode_<? echo $k; ?>" class="text_boxes" style="width:100px;" readonly />
                                </td>
                                <?
									}
								?>

                            <td>
                                <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_row(1)"/>
                                <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                            </td>
                        </tr>

                        </tbody>
                        <tfoot class="tbl_bottom">
                        <td colspan="3">Sum</td>
                        <td><input type="text" name="txt_total_qcpass_qnty" id="txt_total_qcpass_qnty" class="text_boxes_numeric" style="width:100px" readonly/></td>
                        <td><input type="text" name="txt_total_reject_qnty" id="txt_total_reject_qnty" class="text_boxes_numeric" style="width:100px" readonly/></td>
                          <? if($roll_maintained==1)
							{
						 ?>
                         <td colspan="3">&nbsp;</td>
                         <?
							}
						 ?>
                        </tfoot>
                    </table>
                    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $width;?>">
                        <tr>
                            <td colspan="7" align="center">
                                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px"/>

                            </td>
                        </tr>
                    </table>
				<?
                }
                ?>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}

/*
|--------------------------------------------------------------------------
| show_roll_listview
|--------------------------------------------------------------------------
|
*/
if ($action == "show_roll_listview")
{
	$data = explode("**", str_replace("'", "", $data));
	$mst_id = $data[0];
	$barcode_generation = $data[1];
	$query = "select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.order_no from subcon_pro_roll_details a, subcon_ord_dtls b where a.po_breakdown_id=b.id and a.mst_id=$mst_id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 order by a.id";
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data[2]."' and module_id=8 and report_id=217 and is_deleted=0 and status_active=1");

	// echo $print_report_format;die;
	$report_format_id=explode(',',$print_report_format);
	//echo $query;
	$caption = "PO No.";
	?>
    <div align="center">
		<?
		//$barcode_generation=2;
		if ($barcode_generation == 2)
		{

			foreach($report_format_id as $result){

				if($result==315){?>

					<input type="button" id="btn_barcode_extranal_db" name="btn_barcode_extranal_db" value="Send To Ex. DB" class="formbutton" onClick="fnc_barcode_For_extranal_database()"/>

				<?}elseif($result==316){?>
					<input type="button" id="btn_barcode_128" name="btn_barcode_128" value="Barcode 128" class="formbutton" onClick="fnc_barcode_code128(1)"/>

				<?}elseif($result==317){?>
					<input type="button" id="btn_barcode_128" name="btn_barcode_128" value="Barcode 128 v2" class="formbutton" onClick="fnc_barcode_code128(2)"/>
				<?}elseif($result==320){?>
					<input type="button" id="btn_barcode_128" name="btn_barcode_128" value="Direct Print" class="formbutton" onClick="fnc_barcode_code128(5)"/>
				<?}elseif($result==300){?>
					<input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer"
            	class="formbutton" onClick="fnc_send_printer_text()"/>
				<?}elseif($result==136){?>
				<input type="button" id="btn_barcode_direct3" name="btn_barcode_direct3" value="Direct Print 3" class="formbutton" onClick="fnc_barcode_code128(6)"/>
		    	<?}
				elseif($result==502){?>
				<input type="button" id="btn_barcode_128_v3" name="btn_barcode_128_v3" value="Barcode 128 v3" class="formbutton" onClick="fnc_barcode_code128(7)"/>
		    	<?}
				elseif($result==137){?>
					<input type="button" id="btn_barcode_direct4" name="btn_barcode_direct4" value="Direct Print 4" class="formbutton" onClick="fnc_barcode_code128(8)"/>
				<?}
				elseif($result==810){?>
					<input type="button" id="btn_barcode_ccl" name="btn_barcode_ccl" value="Barcode CCL" class="formbutton" onClick="fnc_barcode_code128(9)"/>
				<?}


			}



		}
		else
		{

			foreach($report_format_id as $result){

				if($result==317){?>
					<input type="button" id="btn_barcode_128" name="btn_barcode_128" value="Barcode 128 v2" class="formbutton" onClick="fnc_barcode_code128(2)"/>
				<?}elseif($result==320){?>
					<input type="button" id="btn_barcode_128" name="btn_barcode_128" value="Direct Print" class="formbutton" onClick="fnc_barcode_code128(5)"/>
				<?}elseif($result==334){?>
					<input type="button" id="btn_barcode_generation" name="btn_barcode_generation" value="Barcode Generation"
                   class="formbutton" onClick="fnc_barcode_generation()"/>
				<?}elseif($result==136){?>
				<input type="button" id="btn_barcode_direct3" name="btn_barcode_direct3" value="Direct Print 3" class="formbutton" onClick="fnc_barcode_code128(6)"/>
			<?}elseif($result==502){?>
				<input type="button" id="btn_barcode_128_v3" name="btn_barcode_128_v3" value="Barcode 128 v3" class="formbutton" onClick="fnc_barcode_code128(7)"/>
			<?}elseif($result==137){?>
					<input type="button" id="btn_barcode_direct4" name="btn_barcode_direct4" value="Direct Print 4" class="formbutton" onClick="fnc_barcode_code128(8)"/>
			<?}
			elseif($result==810){?>
				<input type="button" id="btn_barcode_ccl" name="btn_barcode_ccl" value="Barcode CCL" class="formbutton" onClick="fnc_barcode_code128(9)"/>
			<?}
			}
		}

		?>

        <!-- <input type="button" id="btn_barcode_for_database" name="btn_barcode_for_database" value="Send To Database" class="formbutton" onClick="fnc_barcode_For_database()"/>-->
         <input type="hidden" id="btn_barcode_extranal_db" name="btn_barcode_extranal_db" value="Send To Ex. DB" class="formbutton" onClick="fnc_barcode_For_extranal_database()"/>
    </div>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="100%">
        <thead>
        <th width="90"><? echo $caption; ?></th>
        <th width="45">Roll No.</th>
        <th width="60">Roll Qty.</th>
        <th width="70">Barcode No.</th>
        <th>Check All <input type="checkbox" name="check_all" id="check_all" onClick="check_all_report()"></th>
        </thead>
    </table>
    <div style="width:100%; max-height:200px; overflow-y:scroll" id="list_container" align="left">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="100%"
               id="tbl_list_search">
			<?
			$i = 1;
			$result = sql_select($query);
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                    <td width="90">
                        <p><? echo $row[csf('order_no')]; ?></p>
                        <input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>"
							value="<? echo $row[csf('id')]; ?>">
                    </td>
                    <td width="43" style="padding-left:2px"><? echo $row[csf('roll_no')]; ?></td>
                    <td align="right" width="58" style="padding-right:2px"><? echo $row[csf('qnty')]; ?></td>
                    <td width="68" style="padding-left:2px"><? echo $row[csf('barcode_no')]; ?></td>
                    <td align="center" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
						id="chkBundle_<? echo $i; ?>" type="checkbox" name="chkBundle"></td>
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

if ($action == "print_barcode_one_88") {
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);
	//$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	//$brand_id_arr = return_library_array("select lot, brand from product_details_master where item_category_id=1", 'lot', 'brand');
	///print_r($brand_id_arr['6112018']);die;
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
 //quality_level
	$sql = "select a.company_id,a.receive_basis,a.recv_number_prefix_num, a.booking_id,a.location_id,a.store_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name,b.body_part_id,b.floor_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	foreach ($result as $row)
	{
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
			$location_name = return_field_value("location_name", "lib_location", "id=" . $row[csf('location_id')]);
			//$floor_name = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
			$floor_name = return_field_value("floor_name", "lib_prod_floor", "id=" . $row[csf('floor_id')]);
			$location_store=",".$location_name.", ".$floor_name;
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		$body_part_type=return_field_value("body_part_type","lib_body_part","id=".$row[csf('body_part_id')]." and status_active=1");
		//echo $body_part_type;die;
		$receive_date=$row[csf('receive_date')];
		$booking_no = $row[csf('booking_no')];
		$booking_id = $row[csf('booking_id')];
		$booking_without_order = $row[csf('booking_without_order')];
		$receive_basis= $row[csf('receive_basis')];
		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));
		$body_part_name=$body_part[$row[csf('body_part_id')]];
		$avg_color=$color_range[$row[csf('color_range_id')]];
		$order_id = $row[csf('order_id')];
		$brand_id=$row[csf('brand_id')];
		$recv_number_prefix_num = $row[csf('recv_number_prefix_num')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$operator_name = $row[csf('operator_name')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];

		/*$brand='';
		$lot_string = explode(",", $row[csf('yarn_lot')]);
		foreach ($lot_string as $val) {
			if ($val!="") $brand .= $brand_arr[$brand_id_arr[$val]] . ",";
		}
		$brand = chop($brand, ',');*/
		//$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		if ($row[csf("receive_basis")] == 2) {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];

			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
		} else {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
		}

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

		$comp = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") {
				//$comp = $determination_sql[0][csf('construction')] . ", ";
				$constuction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) {
		if ($receive_basis == 4) {
			$sales_info = sql_select("select a.job_no_prefix_num,a.job_no,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id='" . $booking_id . "'");
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $sales_info[0][csf('buyer_id')]);
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_booking_mst", "booking_no='" . $sales_info[0][csf('sales_booking_no')] . "'");
			$order_no = $sales_info[0][csf('job_no')];
		} else {

			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$non_internal_ref= return_field_value("grouping", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
		}
	}
	else
	{
		$is_salesOrder = 0;
		if ($receive_basis == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $booking_id);
		}

		if ($is_salesOrder == 1) {
			$po_sql = sql_select("select a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no,b.buyer_id,b.quality_level from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id in($order_id)");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				if($row[csf('quality_level')]>0)
				{
				$po_array[$row[csf('id')]]['nature'] = $row[csf('quality_level')];
				}
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
			}
		} else {

			if ($receive_basis == 2)
			{
				$planning_booking_sql = sql_select("select a.quality_level,a.booking_no_prefix_num from wo_booking_mst a,ppl_planning_entry_plan_dtls b where a.booking_no=b.booking_no and   b.dtls_id='" . $booking_id . "'");
			}
			$po_sql = sql_select("select a.job_no,a.job_no_prefix_num,a.buyer_name,b.id,b.grouping,b.po_number,d.quality_level,d.booking_no_prefix_num,d.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.booking_type=1 and d.is_short=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.id in($order_id) group by a.job_no,a.job_no_prefix_num,a.buyer_name,b.id,b.grouping,b.po_number,d.quality_level,d.booking_no_prefix_num,d.booking_no");

			foreach ($po_sql as $row1) {
				$po_array[$row1[csf('id')]]['no'] = $row1[csf('po_number')];
				$po_array[$row1[csf('id')]]['job_no'] = $row1[csf('job_no')];
				$po_array[$row1[csf('id')]]['prefix'] = $row1[csf('job_no_prefix_num')];
				$po_array[$row1[csf('id')]]['grouping'] = $row1[csf('grouping')];
				if($row1[csf('quality_level')]>0)
				{
				$po_array[$row1[csf('id')]]['nature'] = $row1[csf('quality_level')];
				}
				$po_array[$row1[csf('id')]]['booking_no'] = $row1[csf('booking_no')];


				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row1[csf('buyer_name')]);
			}
		}
	}


	if ($receive_basis == 2)
	{
		$probram_no=$booking_no;
	}
	else $probram_no="";
	$barcode_array = array();
	//$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, b.fabric_grade from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.id in($data[0])";
	$query = "select a.id,a.inserted_by, a.roll_no,b.reject_qnty,a.qc_pass_qnty_pcs,a.coller_cuff_size, a.po_breakdown_id, a.barcode_no, a.qnty, b.fabric_grade,c.shift_name from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no left join pro_grey_prod_entry_dtls c on a.dtls_id=c.id where a.id in($data[0])";
	$res = sql_select($query);

	$brand_arr = return_library_array("select id, brand_name from lib_brand where id=$brand_id", 'id', 'brand_name');
	/*if($body_part_type==40 || $body_part_type==50)
	{
		$pdf=new PDF_Code128('P','mm',array(88,53));
	}
	else */
	$pdf=new PDF_Code128('P','mm',array(88,53));

	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();
	$pdf->SetFont('Times','',7);


	$i=1; $j=1; $k=0; $br=0; $n=0;
	foreach ($res as $row) {


		$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
		$order_nature = $fbooking_order_nature[$po_array[$row[csf('po_breakdown_id')]]['nature']];
		//echo $nature.'DDDDDDDDD';
		if ($receive_basis == 1)
		{
			$fabric_booking_no=$booking_no;
		}
		else
		{
			$fabric_booking_no=$po_array[$row[csf('po_breakdown_id')]]['booking_no'];
		}
		if ($receive_basis == 1 && $booking_without_order=1)
		{
			$internal_ref=$non_internal_ref;
		}
		else
		{
			if($po_array[$row[csf('po_breakdown_id')]]['grouping']!="") $internal_ref=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
			else $internal_ref="";
		}
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=1; $j=1; $k=0;
		}

		$pdf->SetXY($i, $j+2.5);
		$pdf->Write(0, $party_name."".$location_store.",".$buyer_name.",".$order_nature);
		$pdf->Code128($i,$j+5,$row[csf("barcode_no")],50,8);
		$pdf->SetXY($i+52, $j+6);
		$pdf->Write(0, $row[csf("barcode_no")]);
		$pdf->SetXY($i+52, $j+9);
		$pdf->Write(0, $probram_no);
		$pdf->SetXY($i+52, $j+12);
		$pdf->Write(0, "$internal_ref");
		$pdf->SetXY($i, $j+15.5);
		$pdf->Write(0, "M/C:" . $machine_name . "," . $machine_dia_width . "X" . $machine_gauge. ",SL:". trim($stitch_length).",".trim($tube_type));

		$pdf->SetXY($i, $j+19);
		$pdf->Write(0,  $constuction . "," . $body_part_name );
		$pdf->SetXY($i, $j+23);
		$pdf->Write(0,  substr($comp, 0, 80));

		$pdf->SetXY($i, $j+27);
		$pdf->Write(0, "YC:".substr($yarn_count, 0, 35).", Lot:".substr($yarn_lot, 0, 45));
		$pdf->SetXY($i, $j+31);
		$pdf->Write(0, "Brnd:". substr($brand_arr[$brand_id], 0, 70));
		$pdf->SetXY($i, $j+35);
		$pdf->Write(0, $avg_color.",Color:" .substr($color, 0, 50));
		$pdf->SetXY($i, $j+39);
		$pdf->Write(0, "Qty:" .$row[csf("qnty")].", Rej. Qty:" .$row[csf("reject_qnty")].", Roll No:" .$row[csf('roll_no')].", Shift:" .$shift_name[$row[csf('shift_name')]]);
		$pdf->SetXY($i, $j+43);
		//." B: " . $po_array[$row[csf('po_breakdown_id')]]['booking_no']
		$pdf->Write(0, "P.ID:".$recv_number_prefix_num.", P Date:".change_date_format($receive_date).",FB No:" . $fabric_booking_no);
		$pdf->SetXY($i, $j+47);
		//." B: " . $po_array[$row[csf('po_breakdown_id')]]['booking_no']
		if($body_part_type==40)
		{
			$pdf->Write(0, "Clr Size:".$row[csf('coller_cuff_size')].", Qnty:".$row[csf('qc_pass_qnty_pcs')].",GSM:". $gsm . ",F Dia:" . $finish_dia);
		}

		else if($body_part_type==50)
		{
			$pdf->Write(0, "Cuff Size:".$row[csf('coller_cuff_size')].", Qnty:".$row[csf('qc_pass_qnty_pcs')].",GSM:". $gsm . ",F Dia:" . $finish_dia);
		}
		else $pdf->Write(0, "GSM:". $gsm . ",F Dia:" . $finish_dia);

		//$pdf->Write(0, $row[csf("barcode_no")]. ",Dt:".change_date_format($receive_date). ",Pg:".$program_no. ",S:".$shift_name[$row[csf('shift_name')]]);

		//$pdf->SetXY($i, $j+14);
		//." B: " . $po_array[$row[csf('po_breakdown_id')]]['booking_no']
		//$pdf->Write(0, "$company_short_name.":" . $po_array[$row[csf('po_breakdown_id')]]['booking_no'].","M/C:" . $machine_name . "," . $machine_dia_width . "X" . $machine_gauge. ",RW:" . number_format($row[csf('qnty')], 2, '.', ''));

		//$pdf->SetXY($i, $j+18);
		//$pdf->Write(0, $buyer_name . ",Po:" . $order_no);//24

		//$pdf->SetXY($i, $j+22);
		//$pdf->Write(0, "Clr:" .substr($color, 0, 35));

		//$pdf->SetXY($i, $j+26);
		//$pdf->Write(0, "Ct:".$yarn_count.",Lt:".$yarn_lot);

		//$pdf->SetXY($i, $j+30);
		//$pdf->Write(0, "Br:". $brand.",".$constuction);
 //
		//$pdf->SetXY($i, $j+34);
		//$pdf->Write(0, substr($comp, 0, 45));

		//$pdf->SetXY($i, $j+38);
		//$pdf->Write(0, "G/F Dia:" . $grey_dia. "," . trim($finish_dia).",GSM:". $gsm.",SL:" . trim($stitch_length));

		//$pdf->SetXY($i, $j+42);
		//$pdf->Write(0, "Prd:".$row[csf('recv_number_prefix_num')]. ",RL No:" . $row[csf('roll_no')] .",ID:" .$user_arr[$row[csf('inserted_by')]]);
	//"D/T: " .trim($tube_type).

		$k++;
		$br++;
	}

	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}
if ($action == "direct_print_barcode") {
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');

	$sql = "SELECT a.company_id,a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name, b.shift_name, b.body_part_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';$yarn_type_cond = '';
	//$yarn_type = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$operator_name = '';
	foreach ($result as $row) {
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		if ($row[csf('knitting_source')] == 1) {
			$party_name_full = return_field_value("company_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name_full = return_field_value("supplier_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}
		$booking_id=$row[csf("booking_id")];
		$recieve_basis=$row[csf("receive_basis")];
		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		// $prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$operator_name = $operator_name_arr[$row[csf('operator_name')]];
		$shift_name_id = $row[csf('shift_name')];
		$body_part_id = $row[csf('body_part_id')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		//$yarn_type = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];

			}
		}

		if ($row[csf("receive_basis")] == 2) {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg,a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];
			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
			$bookingNo = $planning_data[0][csf('booking_no')];
		} else {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
		}

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

			if ($row[csf("within_group")] == 1)
			$buyer_name_full = return_field_value("company_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name_full = return_field_value("buyer_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


		$comp = '';$yarn_type_cond = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
			//$yarn_typeId = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id,b.type_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);
			//$yarn_typeId = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);
			//echo "select yarn_type from product_details_master where  id=".$row[csf('prod_id')]." ";
			//echo "select a.construction, b.copmposition_id,b.type_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')];
			//echo $yarn_typeId.'DD';
				$yarn_type_cond=$yarn_type[$yarn_typeId];
			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";

				//$yarn_type_cond .= $yarn_type[$d_row[csf('type_id')]];
			}
		}
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);


	}

	/// yarn Type start booking_id

	if ($recieve_basis == 1) {
		if ($booking_without_order == 0) {
			$sql_yarn = "select  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and  a.booking_id=$booking_id and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and a.knit_dye_source in(1,3) group by  c.yarn_type";


		} else {
			$sql_yarn = "select  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and  a.booking_no='$booking_no' and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and a.knit_dye_source in(1,3) group by c.yarn_type";
		}
	} else if ($recieve_basis == 2) {
		$reqsition_sql = sql_select("select  requisition_no from ppl_yarn_requisition_entry where knit_id='$booking_id'");
		$reqsition_number = "";
		foreach ($reqsition_sql as $inf) {
			if (trim($reqsition_number) != "") {
				$reqsition_number .= ",'" . $inf[csf('requisition_no')] . "'";
			} else {
				$reqsition_number = "'" . $inf[csf('requisition_no')] . "'";
			}
		}

		$sql_yarn = "select  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.requisition_no in($reqsition_number) and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by  c.yarn_type";
	}
	$sql_yarn_result = sql_select($sql_yarn);
	//echo $sql_yarn;
	$yarn_typeCond="";
	foreach ($sql_yarn_result as $row)
	{
		$yarn_typeCond.=$yarn_type[$row[csf('yarn_type')]].',';
	}
	$yarntypeCond=rtrim($yarn_typeCond,',');
	$yarn_type_cond=implode(",",array_unique(explode(",",$yarntypeCond)));
	//echo $yarn_type_cond.'SS';
	//Yarn Type
	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) {
		if ($row[csf("receive_basis")] == 4) {
			$sales_info = sql_select("select a.job_no_prefix_num,a.job_no,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id='" . $row[csf("booking_id")] . "'");
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $sales_info[0][csf('buyer_id')]);
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_booking_mst", "booking_no='" . $sales_info[0][csf('sales_booking_no')] . "'");
			$order_no = $sales_info[0][csf('job_no')];
			$bookingNo = $sales_info[0][csf('sales_booking_no')];
		} else {
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$non_internal_ref= return_field_value("grouping", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$bookingNo = $booking_no;
		}
	} else {
		$is_salesOrder = 0;
		if ($recieve_basis == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $booking_id);
		}
		if ($is_salesOrder == 1) {
			$po_sql = sql_select("select a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id in($order_id)");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = 0; //CRM ID: 22402
				$bookingNo = $row[csf('po_number')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
			}
		} else {
			$po_sql = sql_select("select a.job_no, a.job_no_prefix_num, b.id,b.grouping, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id) group by a.job_no,a.job_no_prefix_num,b.id,b.grouping,b.po_number");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
			}
			//$order_no = $po_array[$order_id]['no'];
		}
	}

	$body_part_type = return_field_value("body_part_type", "lib_body_part", "id=$body_part_id and body_part_type in(40,50) and status_active=1 and is_deleted=0" );

	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, a.qc_pass_qnty_pcs as qnty_pcs, a.coller_cuff_size, b.fabric_grade from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.id in($data[0])";
	// echo $query;
	$res = sql_select($query);
	$pdf=new PDF_Code128('P','mm', array(65,55));
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',8);

	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin(0);

	$i=2; $j=3; $k=0; $br=0; $n=0;
	foreach ($res as $row)
	{
		if ($is_salesOrder == 1) {
			$poNO=0;
			$order_no = $bookingNo;
		}
		else
		{
			$poNO=$po_array[$row[csf('po_breakdown_id')]]['prefix'];
			$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
		}



		if ($row[csf("receive_basis")] == 1 && $booking_without_order=1)
		{
			$internal_ref=$non_internal_ref;
		}
		else
		{
			if($po_array[$row[csf('po_breakdown_id')]]['grouping']!="") $internal_ref=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
			else $internal_ref="";
		}
		$coller_cuff_size="";$qnty_pcs='';
		if ($body_part_type == 40)
		{
			$qnty_pcs = "Qty:" . $row[csf('qnty_pcs')]. " Pcs";
			$coller_cuff_size = "Collar; SZ: " . $row[csf('coller_cuff_size')];
		}
		elseif ($body_part_type == 50)
		{
			$qnty_pcs = "Qty:" . $row[csf('qnty_pcs')]. " Pcs";
			$coller_cuff_size = "Cuff; SZ: " . $row[csf('coller_cuff_size')];
		}

		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
		}


		$bookingNoArr=explode("-", $bookingNo);
		$bookingNos=$bookingNoArr[1]."-".$bookingNoArr[2]."-".$bookingNoArr[3];


		/*	if ($booking_without_order == 1) {
			$txt = $row[csf('barcode_no')] . "; " . $party_name . " Booking No." . $booking_no_prefix . ";<br>";
		} else {
			$txt = $row[csf('barcode_no')] . "; " . $party_name . " Job No." . $po_array[$row[csf('po_breakdown_id')]]['prefix'] . ";<br>";
		}
		$txt .= "M/C: " . $machine_name . "; M/C Dia X Gauge-" . $machine_dia_width . "X" . $machine_gauge . ";<br>";
		$txt .= "Date: " . $prod_date . ";<br>";
		$txt .= "Buyer: " . $buyer_name . ", Order No: " . $order_no . ";<br>";
		$txt .= $comp . "<br>";
		$txt .= "G/Dia: " . $grey_dia . "; SL: " . trim($stitch_length) . "; " . trim($tube_type) . "; F/Dia: " . trim($finish_dia) . ";<br>";
		$txt .= "GSM: " . $gsm . "; ";
		$txt .= $yarn_count . "; Lot: " . $yarn_lot . ";<br>";
		$txt .= "Prg: " . $program_no . "; Roll Wt: " . number_format($row[csf('qnty')], 2, '.', '') . " Kg;<br>";
		$txt .= "Custom Roll No: " . $row[csf('roll_no')] . ";";
		if (trim($color) != "") $txt .= " Color: " . trim($color) . ";<br>";

		if (trim($row[csf('fabric_grade')]) != "") $txt .= "Grade: " . trim($row[csf('fabric_grade')]) . ";";
		if ($operator_name != "") $txt .= "OP: " . $operator_name_arr[$operator_name] . ";";*/



		//if ($operator_name != "") $operator_name .= "OP: " . $operator_name_arr[$operator_name] ;
		if ($operator_name != "") $operator_names = ";OP:" . $operator_name;
			//$coller_cuff_size = "Cuff; SZ: " . $row[csf('coller_cuff_size')];

		$pdf->SetXY($i, $j);
		$pdf->Write(0, "WC:" . substr($party_name_full,0,45) );

		$pdf->SetXY($i, $j+3.5);
		$pdf->Write(0, "D:" . $prod_date ."; B:" . $buyer_name);

		$pdf->SetXY($i, $j+6.5);
		$pdf->Write(0, $company_short_name."; Job No." . $poNO."; IR:".$internal_ref."; Sft:".$shift_name[$shift_name_id]);

		$pdf->SetXY($i, $j+9.5);
		$pdf->Write(0, "Po:" . substr($order_no,0,25) );

		$pdf->SetXY($i, $j+12.5);
		$pdf->Write(0, substr($comp,0,45) );

		$pdf->SetXY($i, $j+16);
		$pdf->Write(0, "F/GSM: " . $gsm.";Clr: " .substr($color, 0, 25));

		$pdf->SetXY($i, $j+19);
		$pdf->Write(0, "C:".$yarn_count . "; L: " . $yarn_lot);
		//$pdf->Write(0, "C: " . substr($gsm, 0, 25) .";Clr: " .substr($color, 0, 25));

		$pdf->SetXY($i, $j+22.5);
		$pdf->Write(0, "Br: " .  $brand .";T: " . $yarn_type_cond);

		$pdf->SetXY($i, $j+25.5);
		$pdf->Write(0, "M/C: " . $machine_name . "; DiaXGG-" . $machine_dia_width . "X" . $machine_gauge . "; B:" . $bookingNos);//24

		$pdf->SetXY($i, $j+28.5);
		$pdf->Write(0, "F/Dia: " . trim($finish_dia).";D/Type: " .trim($tube_type));

		$pdf->SetXY($i, $j+31.5);
		$pdf->Write(0, "SL: " . trim($stitch_length).";Prg: " .$program_no . $operator_names);

		$pdf->SetXY($i, $j+34.5);
		$pdf->Write(0, "Roll No: " . $row[csf('roll_no')] ."; Roll Wt: " . number_format($row[csf('qnty')], 2, '.', ''). " Kg;" . $qnty_pcs);


		$pdf->SetXY($i, $j+37.5);
		$pdf->Write(0, $row[csf('barcode_no')].";" .$coller_cuff_size);



		$pdf->Code128($i+1,$j+40.5,$row[csf("barcode_no")],50,8);

		$k++;
		$br++;
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}


if ($action == "save_barcode_for_extranal_database") {
	$data = explode("***", $data);
	// For "Grey Fabric Bar-code Striker Export Report" report page
	if ($data[2] != '' || $data[3] != '') {
		$batch_no_condition = ($data[2] != "") ? " and a.batch_no='" . $data[2] . "'" : "";
		$barcode_condition = ($data[3] != "") ? " and b.barcode_no='" . $data[3] . "'" : "";

		$barcodeData = sql_select("select a.id, a.batch_no,b.barcode_no, b.roll_id, c.dtls_id from pro_batch_create_mst a
			inner join pro_batch_create_dtls b on a.id = b.mst_id inner join pro_roll_details c on b.roll_id = c.id where a.company_id=1 and a.status_active=1 and a.is_deleted=0 $batch_id_condition $batch_no_condition $barcode_condition");
		if (!empty($barcodeData)) {
			foreach ($barcodeData as $value) {
				$barcode_nos .= $value[csf('roll_id')] . ',';
				$dtls_id = $value[csf('dtls_id')];
				$batch_no = $value[csf('batch_no')];
			}
			$data[0] = rtrim($barcode_nos, ',');
			$data[1] = $dtls_id;
		} else {
			echo "Not Found";
		}
	}
	// For "Grey Fabric Bar-code Striker Export Report" report page (end)

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand'); // Temporary
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$sql_yarn_info=sql_select("select a.prod_id,b.brand,b.yarn_type,b.yarn_comp_type1st,b.yarn_comp_type2nd,b.yarn_comp_percent1st,b.yarn_comp_percent2nd,b.lot,b.yarn_count_id from pro_material_used_dtls a,product_details_master b  where a.prod_id=b.id and  a.mst_id=".$data[4]." and a.dtls_id=".$data[1]." and a.entry_form=2
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	$yarn_information_string=""; $all_yarn_type='';
	foreach($sql_yarn_info as $p_val)
	{
		$costing_yarn_composition='';
		$costing_yarn_band=$brand_arr[$p_val[csf('brand')]];
		$costing_yarn_lot=trim($p_val[csf('lot')]);
		$costing_yarn_count=$count_arr[$p_val[csf('yarn_count_id')]];
		$costing_yarn_composition=$composition[$p_val[csf('yarn_comp_type1st')]] . " " . $p_val[csf('yarn_comp_percent1st')] . "%";
		if ($p_val[csf('yarn_comp_type2nd')] != 0) $costing_yarn_composition .= " " . $composition[$p_val[csf('yarn_comp_type2nd')]] . " " . $p_val[csf('yarn_comp_percent2nd')] . "%";
		$yarn_information_string.=$costing_yarn_band." ".$costing_yarn_lot." ".$costing_yarn_count." ".$costing_yarn_composition. "\r\n";
		$all_yarn_type.=",".$yarn_type[$p_val[csf('yarn_type')]];

	}

	$sql = "select a.company_id, a.recv_number, a.location_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.shift_name, b.insert_date,b.operator_name, b.color_range_id, b.floor_id  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$shiftName = '';
	$colorRange = '';
	$productionId = '';
	$receive_basis=$row[csf('receive_basis')];
	foreach ($result as $row) {
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}
		$yarn_type_data = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);

		$booking_no = $row[csf('booking_no')];
		$booking_id = $row[csf('booking_id')];
		$operator_name = $operator_name_arr[$row[csf('operator_name')]];
		$floor_name = $floor_name_arr[$row[csf('floor_id')]];

		$booking_without_order = $row[csf('booking_without_order')];
		$productionId = $row[csf('recv_number')];

		//$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		//$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));

		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$shiftName = $shift_name[$row[csf('shift_name')]];
		$colorRange = $color_range[$row[csf('color_range_id')]];

		//$color=$color_arr[$row[csf('color_id')]];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');
		if (trim($color) != "") {
			//$color=", ".$color;
			//$color="".$color;
		}

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 4) {
			$machine_data = sql_select("select machine_no, dia_width, gauge,brand from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			//$machine_dia_width=$machine_data[0][csf('dia_width')];
			//$machine_gauge=$machine_data[0][csf('gauge')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
			$machine_brand = $row[csf('brand')];
			if($row[csf('receive_basis')]==1)
			{

				$sql_precost_tube=sql_select("select  b.width_dia_type from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id=".$row[csf('febric_description_id')]."");
				foreach($sql_precost_tube as $t_val)
				{
					$tube_type = $fabric_typee[$t_val[csf('width_dia_type')]];
				}
			//	echo $sql_precost_tube;die;
				//$grey_dia = $program_data[0][csf('machine_dia')];
				//$tube_type = $fabric_typee[$program_data[0][csf('width_dia_type')]];
			}

		} else if ($row[csf('receive_basis')] == 2) //Knitting Plan
		{
			$program_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg, b.machine_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$program_no = $row[csf('booking_id')];
			$grey_dia = $program_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$program_data[0][csf('width_dia_type')]];
			$machine_dia_width = $program_data[0][csf('machine_dia')];
			$machine_gauge = $program_data[0][csf('machine_gg')];
			//$machine_no_arr
			$machine_brand = $machine_brand_arr[$row[csf('machine_no_id')]];
			$machine_name = $machine_no_arr[$row[csf('machine_no_id')]];
			//$machine_name=explode(",",$program_data[0][csf('machine_id')]);
			$row[csf("within_group")] = $program_data[0][csf('within_group')];
		}

		//$location_name=return_field_value("location_name","lib_location", "id=".$row[csf('location_id')]);
		//$buyer_name=return_field_value("short_name","lib_buyer", "id=".$row[csf('buyer_id')]);
		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id='" . $row[csf('buyer_id')] . "'");
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


		$comp = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
				$construction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				$composi .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
	}


	//echo "select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)";
	//echo $booking_id;die;
	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) {
		//$booking_no_prefix=return_field_value("booking_no_prefix_num","wo_non_ord_samp_booking_mst", "booking_no='".$booking_no."'");
		if ($row[csf("receive_basis")] == 4) {

			$fb_sales_sql = "select id,job_no_prefix_num,job_no,style_ref_no,within_group from fabric_sales_order_mst where id = " . $row[csf('booking_id')];
			$fb_salesResult = sql_select($fb_sales_sql);
			$booking_no_prefix = $fb_salesResult[0][csf('job_no_prefix_num')];
			$full_booking_no = $fb_salesResult[0][csf('job_no')];
			$style_ref_no = $fb_salesResult[0][csf('style_ref_no')];
			$sales_id = $fb_salesResult[0][csf('id')];

			//$booking_no_prefix=return_field_value("job_no_prefix_num","fabric_sales_order_mst", "id='".$row[csf("booking_id")]."'");
			//$full_booking_no=return_field_value("job_no","fabric_sales_order_mst", "id='".$row[csf("booking_id")]."'");
			$no_arr = explode("-", $full_booking_no);
			array_shift($no_arr); //remove 1st index
			$full_booking_no = implode("-", $no_arr);
			//$style_ref_no=return_field_value("style_ref_no","fabric_sales_order_mst", "id='".$row[csf("booking_id")]."'");
			//$sales_id=return_field_value("id","fabric_sales_order_mst", "id='".$row[csf("booking_id")]."'");
			$po_array[$sales_id]['style_ref'] = $style_ref_no;

		} else {
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$full_booking_no = $booking_no;
		}
	} else {
		$is_salesOrder = 0;
		if ($row[csf("receive_basis")] == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id='" . $row[csf("booking_id")] . "'");
			$booking_no = return_field_value("b.booking_no as booking_no", "ppl_planning_info_entry_dtls a,ppl_planning_info_entry_mst b", " b.id=a.mst_id and a.id='" . $booking_id . "'", "booking_no");
		}
		if ($is_salesOrder == 1) {
			//echo "select a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no=b.booking_no and id in($order_id)";
			if ($row[csf("within_group")] == 1) {
				$po_sql = sql_select("select a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,b.buyer_id,a.within_group from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no=b.booking_no and a.id in($order_id)");
			} else {
				$po_sql = sql_select("select a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,a.buyer_id,a.within_group from fabric_sales_order_mst a where a.id in($order_id)");
			}
			foreach ($po_sql as $row) {
				$no_arr = explode("-", $row[csf('job_no')]);
				array_shift($no_arr); //remove 1st index
				$full_booking_no = implode("-", $no_arr);

				$po_no_arr = explode("-", $row[csf('po_number')]);
				array_shift($po_no_arr); //remove 1st index
				$po_no_arr = implode("-", $po_no_arr);
				$po_array[$row[csf('id')]]['no'] = $po_no_arr;//$row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $full_booking_no;
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_id')];

			}

			//print_r($po_array);

		} else {
			$po_sql = sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
			}
		}
	}
	$within_group = $row[csf("within_group")];



	$i = 1;
	$year = date("y");
	$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty,a.reject_qnty, b.batch_wgt from pro_roll_details a left join pro_grey_batch_dtls b on a.id = b.roll_id where a.id in($data[0]) order by a.barcode_no asc";
	$res = sql_select($query);
	$split_data_arr = array();
	foreach ($res as $row) {
		$split_roll_id = $row[csf('id')];
		$roll_split_query = sql_select("select a.barcode_no, a.qnty, a.id, a.roll_split_from from pro_roll_details a where a.roll_id = $split_roll_id and a.roll_split_from != 0");
		if ($booking_without_order == 1) {
			$field1=$party_name." Job No.".$booking_no_prefix." M/C".$machine_name . "-" . $machine_dia_width . "X" . $machine_gauge;
		} else {

			$field1=$party_name." Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix']." M/C".$machine_name . "-" . $machine_dia_width . "X" . $machine_gauge;
		}
		//print_r($roll_split_query);
		if (!empty($roll_split_query)) {
			$qnty = number_format($roll_split_query[0]['qnty'], 2, '.', '');
			$barcode = $roll_split_query[0]['barcode_no'];
		} else {
			$qnty = number_format($row[csf('QNTY')], 2, '.', '');
			$barcode = $row[csf('barcode_no')];
		}
		$field2= $barcode;
		$field3="ID:".$barcode." D:".$prod_date." ".$buyer_name;
		$field4="Order No: ". $po_array[$row[csf('po_breakdown_id')]]['no'];
		$field5=$comp;
		$field6="G/F-Dia:".trim($grey_dia) ."/ ".trim($finish_dia)." ".trim($stitch_length);
		$field7="GSM:".$gsm." ".$yarn_count." ".$brand."Lot:".$yarn_lot;
		if($receive_basis==2) $program_no=$booking_no;
		$field8="Prg:".$program_no." Wt:".$qnty." Kg.Sft:".$shiftName;
		$field9="Roll No:".$row[csf('roll_no')]." ".trim($color)." ".trim($colorRange);
		if($data_array_print!="") $data_array_print.=",";
		$data_array_print.="('".$field1."','".$field2."','".$field3."','".$field4."','".$field5."','".$field6."','".$field7."','".$field8."','".$field9."','')";

		$i++;
	}

	/*	function extranal_connect( $server='localhost', $user='root', $passwd='', $db_name='logic_erp_2nd_version' )
	{
		$new_con_insert = mysql_connect( $server, $user, $passwd );
		if(!$new_con_insert)
		{
			trigger_error("Problem connecting to server");
		}
		$DB =  mysql_select_db($db_name, $new_con_insert);
		if(!$DB)
		{
			trigger_error("Problem selecting database");
		}
		//mysql_query("START TRANSACTION");
		return $new_con_insert;
	}*/

	function sql_insert_new($strTable,$arrNames,$arrValues, $commit )
	{
		//if($db_type
		$server='localhost';
		$user='norsel';
		$passwd='norsel@123';
		$db_name='norsel'; // extranal database
		$new_con_insert = mysql_connect( $server, $user, $passwd );
		if(!$new_con_insert)
		{
			trigger_error("Problem connecting to server");
		}

		$DB =  mysql_select_db($db_name, $new_con_insert);
		if(!$DB)
		{
			trigger_error("Problem selecting database");
		}

		$strQuery= "INSERT INTO ".$strTable." (".$arrNames.") VALUES ".$arrValues."";
		 //return $strQuery; die;
		mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
		$result=mysql_query($strQuery);
		return $result;
		disconnect($new_con_insert);
		die;
	}

	$field_array_bundle="I1,I2,I3,I4,I5,I6,I7,I8,I9,I10";
		//echo "insert into LABEL_OUT($field_array_bundle)values".$data_array_print;die;
	$rID=sql_insert_new("LABEL_OUT",$field_array_bundle,$data_array_print,1);

	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");
			echo 0;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo 10;
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);
			echo 0;
		}
		else
		{
			oci_rollback($con);
			echo 10;
		}
	}
	disconnect($con);
	die;
}





/*
|--------------------------------------------------------------------------
| report_barcode_text_file
|--------------------------------------------------------------------------
|
*/
if ($action == "report_barcode_text_file")
{
	$userid=$_SESSION['logic_erp']['user_id'];

	$data = explode("**", $data);
	// For "Grey Fabric Bar-code Striker Export Report" report page
	if ($data[2] != '' || $data[3] != '') {
		//$batch_no_condition = ($data[2] != "") ? " and a.batch_no='" . $data[2] . "'" : "";
		$barcode_condition = ($data[2] != "") ? " and a.id=" . $data[2] . "" : "";

		$barcodeSql ="select c.barcode_no, c.id as roll_id, c.dtls_id from subcon_production_mst a
			inner join subcon_production_dtls b on a.entry_form=159 and a.id = b.mst_id inner join subcon_pro_roll_details c on b.id = c.dtls_id where c.entry_form=159  and c.id in($data[0]) and a.status_active=1 and a.is_deleted=0 $barcode_condition";
			$barcodeData=sql_select($barcodeSql);
		if (!empty($barcodeData)) {
			foreach ($barcodeData as $value) {
				$barcode_nos .= $value[csf('roll_id')] . ',';
				$dtls_id = $value[csf('dtls_id')];
				$batch_no = $value[csf('batch_no')];
			}
			$data[0] = rtrim($barcode_nos, ',');
			$data[1] = $dtls_id;
		} else {
			echo "Not Found";
		}
	}
	// For "Grey Fabric Bar-code Striker Export Report" report page (end)

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand'); // Temporary
	$buyer_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');


	$sql = "select a.company_id, a.knitting_company, a.product_no as recv_number, a.location_id,a.program_no,a.production_basis, a.knit_location_id, a.product_date as receive_date, a.party_id as buyer_id, b.order_id, b.cons_comp_id as prod_id, b.gsm, b.dia_width as width, b.yarn_lot, b.yrn_count_id as yarn_count, b.brand as brand_id, b.machine_id as machine_no_id, b.stitch_len as stitch_length, b.machine_dia as machine_dia, b.machine_gg as machine_gg, b.color_id, b.fabric_description as fabric_description, b.shift as shift_name, b.insert_date, b.color_range as color_range_id, b.floor_id  from subcon_production_mst a, subcon_production_dtls b where a.entry_form=159 and a.id=b.mst_id and b.id=$data[1]";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';$company_short_name = '';$cust_buyer = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$program_no = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$shiftName = '';
	$colorRange = '';
	$productionId = '';
	$booking_no='';
	foreach ($result as $row) {
		if($data[3]==1)
		{
			$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		}
		else
		{
			$company_short_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		$floor_name = $floor_name_arr[$row[csf('floor_id')]];
		$productionId = $row[csf('recv_number')];
		$program_no = $row[csf('program_no')];

		//$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		//$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		$prod_basis_id = $row[csf('production_basis')];
		if($prod_basis_id==2)
		{
			$booking_no = return_field_value("b.booking_no as booking_no", "ppl_planning_info_entry_dtls a,ppl_planning_info_entry_mst b", " b.id=a.mst_id and a.id='" . $program_no . "'", "booking_no");
		}

		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$location_name=return_field_value("location_name","lib_location", "id=".$row[csf('knit_location_id')]);
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];

		$finish_dia = $row[csf('width')];
		$dia_width = $row[csf('dia_width')];
		$fabric_description = $row[csf('fabric_description')];
		$party_id = $row[csf('buyer_id')];
		$shiftName = $shift_name[$row[csf('shift_name')]];
		$colorRange = $color_range[$row[csf('color_range_id')]];

		//$color=$color_arr[$row[csf('color_id')]];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');
		if (trim($color) != "") {
			//$color=", ".$color;
			//$color="".$color;
		}

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}


			$machine_data = sql_select("select machine_no, dia_width, gauge,brand from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];

			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
			$machine_brand = $row[csf('brand')];
			$buyer_name = return_field_value("company_short_name", "lib_company", "id='" . $row[csf('buyer_id')] . "'");

		$comp = '';
		$comp = $item_arr[$row[csf('prod_id')]];
	}



	$po_array = array();
	$booking_no_prefix = '';
			$po_sql = "select a.subcon_job as job_no,b.cust_buyer,b.order_uom, b.cust_style_ref as style_ref_no, a.party_id as buyer_name, a.job_no_prefix_num as job_no_prefix_num, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.id in($order_id)";
			$po_result = sql_select($po_sql);
			foreach ($po_result as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
				$po_array[$row[csf('id')]]['cust_buyer'] = $row[csf('cust_buyer')];
				$po_array[$row[csf('id')]]['order_uom'] =$unit_of_measurement[$row[csf('order_uom')]];
			}

	//$within_group = $row[csf("within_group")];
	//foreach (glob("" . "*.zip") as $filename) {
	foreach (glob('norsel_bundle_'.$userid."*.zip") as $filename) {
		@unlink($filename);
	}
	//echo $within_group;
	//exit;
	$i = 1;
	$zip = new ZipArchive();            // Load zip library
	//$filename = str_replace(".sql", ".zip", 'norsel_bundle.sql');            // Zip name
	$filename = str_replace(".sql",".zip",'norsel_bundle_'.$userid.".sql");
	if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {        // Opening zip file to load files
		$error .= "* Sorry ZIP creation failed at this time<br/>";
		echo $error;
	}

	$i = 1;$roll_qnty=0;
	$year = date("y");
	$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, sum(a.qnty) as qnty,sum(a.reject_qnty) as reject_qnty, sum(b.product_qnty) as product_qnty,b.dia_width,b.stitch_len,b.dia_width_type,b.yarn_lot, b.yrn_count_id as yarn_count, b.brand, b.machine_id as machine_no_id,b.shift,b.color_range,b.color_id from subcon_pro_roll_details a left join subcon_production_dtls b on a.dtls_id = b.id where a.id in($data[0]) group by a.id, a.roll_no, a.po_breakdown_id, a.barcode_no,b.dia_width,b.stitch_len,b.dia_width_type,b.yarn_lot, b.yrn_count_id, b.brand, b.machine_id,b.shift,b.color_range,b.color_id  order by a.barcode_no asc";
	$res = sql_select($query);
	$split_data_arr = array();
	foreach ($res as $row) {

		//$file_name = "NORSEL-IMPORT_" . $i;
		$file_name = "NORSEL-IMPORT_".$userid."_" . $i;
		$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");
		//$txt = "Norsel_imp\r\n1\r\n";
		//$txt = "Norsel_imp\r\n";
		$txt = "Norsel_imp\r\n1\r\n";
		$fab_dia_width=$row[csf('dia_width')];
		$stitch_len=$row[csf('stitch_len')];
		$brand=$row[csf('brand')];
		$stitch_length = $row[csf('stitch_len')];
		$yarn_lot = $row[csf('yarn_lot')];
		$program_no = $program_no;
		$shiftName = $shift_name[$row[csf('shift')]];
		$colorRange = $color_range[$row[csf('color_range')]];
		$dia_width_type=$fabric_typee[$row[csf('dia_width_type')]];
		$fab_color_name=$color_arr[$row[csf('color_id')]];
		$cust_buyer=$po_array[$row[csf('po_breakdown_id')]]['cust_buyer'];


		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

			$txt .= $company_short_name . "\r\n";
			//$txt .= $cust_buyer . "\r\n";
			$txt .= $cust_buyer.','.$po_array[$row[csf('po_breakdown_id')]]['prefix'] . "\r\n";
			//$txt .=$machine_dia_width . "X" . $machine_gauge . "\r\n";
			//$txt .= $machine_name. "\r\n";
			$txt .= $machine_name . "-" . $machine_dia_width . "X" . $machine_gauge . "\r\n";

			//$full_job_no = $po_array[$row[csf('po_breakdown_id')]]['job_no'];
			//$txt .=$party_name." Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix']." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";
			//$txt .=$party_name." Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix']." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";

		$party_name=$buyer_name_arr[$po_array[$row[csf('po_breakdown_id')]]['buyer_name']];
		$order_uom=$po_array[$row[csf('po_breakdown_id')]]['order_uom'];
		$roll_qnty = number_format($row[csf('qnty')], 2, '.', '');
		$barcode = $row[csf('barcode_no')];
		$txt .= $barcode . "\r\n";//4
		//$txt .="Barcode No: ".$row[csf('barcode_no')]."\r\n";
		$txt .= $barcode . "\r\n";//5
		$txt .= "" . $prod_date . "\r\n";//6



	  //  $txt .= $po_array[$row[csf('po_breakdown_id')]]['no'] . "\r\n";
		$txt .= "" . $fabric_description . "\r\n";
	    $txt .= "" . $po_array[$row[csf('po_breakdown_id')]]['no'] . "\r\n";//8


		$txt .= "" . trim($stitch_len) . "\r\n";
		$txt .= "" . '' . "\r\n";
		$txt .= "" . '' . "\r\n";
		$txt .= "" . trim($fab_dia_width).','.trim($stitch_len).','.trim($dia_width_type) . "\r\n";//12
		$txt .= "" . $gsm . "\r\n";//13
		$txt .= $yarn_count . "\r\n";//14
		$txt .= trim($yarn_lot) . "\r\n";//15
		$txt .= $brand . "\r\n";//16
		$txt .= "" . $program_no . "\r\n";//17
		$txt .= $roll_qnty . "Kg\r\n"; //18
		$txt .= $shiftName . "\r\n";//19
		$txt .= "" . $row[csf('roll_no')] . "\r\n";//20
		$txt .= "" . trim($fab_color_name) . "\r\n";//21
		$txt .= "" . trim($colorRange) . "\r\n";//22
		$txt .= "" . $po_array[$row[csf('po_breakdown_id')]]['style_ref'] . "\r\n";//23
		$txt .= "" . '' . "\r\n";//24
		$txt .= ''.', Party:'.$party_name.',PID:'.$productionId . "\r\n";//25

		//$txt .= "" . $location_name . "\r\n";
		//$txt .= "" . $floor_name . "\r\n";
		//$txt .= "" . $machine_name  . "\r\n";
		//$txt .= "" . $productionId . "\r\n";



		//$txt .= "" . $order_uom . "\r\n";



		fwrite($myfile, $txt);
		fclose($myfile);

		$i++;
	}
	//foreach (glob("" . "*.txt") as $filenames) {
	foreach (glob("NORSEL-IMPORT_".$userid."*.txt") as $filenames){
		$zip->addFile($file_folder.$filenames);
	}
	$zip->close();

	//foreach (glob("" . "*.txt") as $filename) {
	foreach (glob("NORSEL-IMPORT_".$userid."*.txt") as $filename){
		@unlink($filename);
	}
	echo 'norsel_bundle_'.$userid;
	//echo "norsel_bundle";
	exit();
}

if ($action == "report_barcode_text_file_old")//Not Used
{
	$data = explode("**", $data);
	// For "Grey Fabric Bar-code Striker Export Report" report page
	if ($data[2] != '' || $data[3] != '') {
		//$batch_no_condition = ($data[2] != "") ? " and a.batch_no='" . $data[2] . "'" : "";
		$barcode_condition = ($data[2] != "") ? " and a.id=" . $data[2] . "" : "";

		$barcodeSql ="select c.barcode_no, c.id as roll_id, c.dtls_id from subcon_production_mst a
			inner join subcon_production_dtls b on a.entry_form=159 and a.id = b.mst_id inner join subcon_pro_roll_details c on b.id = c.dtls_id where c.entry_form=159  and c.id in($data[0]) and a.status_active=1 and a.is_deleted=0 $barcode_condition";
			$barcodeData=sql_select($barcodeSql);
		if (!empty($barcodeData)) {
			foreach ($barcodeData as $value) {
				$barcode_nos .= $value[csf('roll_id')] . ',';
				$dtls_id = $value[csf('dtls_id')];
				$batch_no = $value[csf('batch_no')];
			}
			$data[0] = rtrim($barcode_nos, ',');
			$data[1] = $dtls_id;
		} else {
			echo "Not Found";
		}
	}
	// For "Grey Fabric Bar-code Striker Export Report" report page (end)

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand'); // Temporary
	$buyer_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');


	$sql = "select a.company_id, a.knitting_company, a.product_no as recv_number, a.location_id, a.knit_location_id, a.product_date as receive_date, a.party_id as buyer_id, b.order_id, b.cons_comp_id as prod_id, b.gsm, b.dia_width as width, b.yarn_lot, b.yrn_count_id as yarn_count, b.brand as brand_id, b.machine_id as machine_no_id, b.stitch_len as stitch_length, b.machine_dia as machine_dia, b.machine_gg as machine_gg, b.color_id, b.fabric_description as fabric_description, b.shift as shift_name, b.insert_date, b.color_range as color_range_id, b.floor_id  from subcon_production_mst a, subcon_production_dtls b where a.entry_form=159 and a.id=b.mst_id and b.id=$data[1]";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';$company_short_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';

	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$shiftName = '';
	$colorRange = '';
	$productionId = '';
	foreach ($result as $row) {
		if($data[3]==1)
		{
			$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		}
		else
		{
			$company_short_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		$floor_name = $floor_name_arr[$row[csf('floor_id')]];
		$productionId = $row[csf('recv_number')];

		//$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		//$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$location_name=return_field_value("location_name","lib_location", "id=".$row[csf('knit_location_id')]);
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$dia_width = $row[csf('dia_width')];
		$fabric_description = $row[csf('fabric_description')];
		$shiftName = $shift_name[$row[csf('shift_name')]];
		$colorRange = $color_range[$row[csf('color_range_id')]];

		//$color=$color_arr[$row[csf('color_id')]];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');
		if (trim($color) != "") {
			//$color=", ".$color;
			//$color="".$color;
		}

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}


			$machine_data = sql_select("select machine_no, dia_width, gauge,brand from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];

			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
			$machine_brand = $row[csf('brand')];
			$buyer_name = return_field_value("company_short_name", "lib_company", "id='" . $row[csf('buyer_id')] . "'");

		$comp = '';
		$comp = $item_arr[$row[csf('prod_id')]];
	}



	$po_array = array();
	$booking_no_prefix = '';
			$po_sql = "select a.subcon_job as job_no,b.cust_buyer,b.order_uom, b.cust_style_ref as style_ref_no, a.party_id as buyer_name, a.job_no_prefix_num as job_no_prefix_num, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.id in($order_id)";
			$po_result = sql_select($po_sql);
			foreach ($po_result as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
				$po_array[$row[csf('id')]]['cust_buyer'] = $row[csf('cust_buyer')];
				$po_array[$row[csf('id')]]['order_uom'] =$unit_of_measurement[$row[csf('order_uom')]];
			}

	//$within_group = $row[csf("within_group")];
	foreach (glob("" . "*.zip") as $filename) {
		@unlink($filename);
	}
	//echo $within_group;
	//exit;
	$i = 1;
	$zip = new ZipArchive();            // Load zip library
	$filename = str_replace(".sql", ".zip", 'norsel_bundle.sql');            // Zip name
	if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {        // Opening zip file to load files
		$error .= "* Sorry ZIP creation failed at this time<br/>";
		echo $error;
	}

	$i = 1;$roll_qnty=0;
	$year = date("y");
	$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, sum(a.qnty) as qnty,sum(a.reject_qnty) as reject_qnty, sum(b.product_qnty) as product_qnty,b.dia_width,b.stitch_len,b.dia_width_type,b.yarn_lot, b.yrn_count_id as yarn_count, b.brand, b.machine_id as machine_no_id,b.shift,b.color_range,b.color_id from subcon_pro_roll_details a left join subcon_production_dtls b on a.dtls_id = b.id where a.id in($data[0]) group by a.id, a.roll_no, a.po_breakdown_id, a.barcode_no,b.dia_width,b.stitch_len,b.dia_width_type,b.yarn_lot, b.yrn_count_id, b.brand, b.machine_id,b.shift,b.color_range,b.color_id  order by a.barcode_no asc";
	$res = sql_select($query);
	$split_data_arr = array();
	foreach ($res as $row) {

		$file_name = "NORSEL-IMPORT_" . $i;
		$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");
		//$txt = "Norsel_imp\r\n1\r\n";
		$txt = "Norsel_imp\r\n";
		$fab_dia_width=$row[csf('dia_width')];
		$stitch_len=$row[csf('stitch_len')];
		$brand=$row[csf('brand')];
		$stitch_length = $row[csf('stitch_len')];
		$yarn_lot = $row[csf('yarn_lot')];
		$shiftName = $shift_name[$row[csf('shift')]];
		$colorRange = $color_range[$row[csf('color_range')]];
		$dia_width_type=$fabric_typee[$row[csf('dia_width_type')]];
		$fab_color_name=$color_arr[$row[csf('color_id')]];


		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

			$txt .= $company_short_name . "\r\n";
			$txt .= $po_array[$row[csf('po_breakdown_id')]]['prefix'] . "\r\n";
			$txt .=$machine_dia_width . "X" . $machine_gauge . "\r\n";
			//$full_job_no = $po_array[$row[csf('po_breakdown_id')]]['job_no'];
			//$txt .=$party_name." Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix']." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";
			//$txt .=$party_name." Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix']." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";
		$cust_buyer=$po_array[$row[csf('po_breakdown_id')]]['cust_buyer'];
		$party_name=$buyer_name_arr[$po_array[$row[csf('po_breakdown_id')]]['buyer_name']];
		$order_uom=$po_array[$row[csf('po_breakdown_id')]]['order_uom'];
		$roll_qnty = number_format($row[csf('qnty')], 2, '.', '');
		$barcode = $row[csf('barcode_no')];
		$txt .= $barcode . "\r\n";
		//$txt .="Barcode No: ".$row[csf('barcode_no')]."\r\n";
		$txt .= $barcode . "\r\n";
		$txt .= "" . $prod_date . "\r\n";
		$txt .= $cust_buyer . "\r\n";
		$txt .= "" . $fabric_description . "\r\n";
		$txt .= "" . $fab_dia_width . "\r\n";
		$txt .= "" . $stitch_len . "\r\n";
		$txt .= "" . $dia_width_type . "\r\n";
		$txt .= "" . $gsm . "\r\n";
		$txt .= $yarn_count . "\r\n";
		$txt .= $yarn_lot . "\r\n";
		$txt .= $brand . "\r\n";
		$txt .= $roll_qnty . "\r\n";
		$txt .= $shiftName . "\r\n";
		$txt .= "" . $row[csf('roll_no')] . "\r\n";
		$txt .= "" . $colorRange . "\r\n";
		$txt .= "" . $fab_color_name . "\r\n";
		$txt .= "" . $location_name . "\r\n";
		$txt .= "" . $floor_name . "\r\n";
		$txt .= "" . $machine_name  . "\r\n";
		$txt .= "" . $productionId . "\r\n";
		$txt .= "" . $party_name . "\r\n";
		$txt .= "" . $order_uom . "\r\n";



		fwrite($myfile, $txt);
		fclose($myfile);

		$i++;
	}
	foreach (glob("" . "*.txt") as $filenames) {
		$zip->addFile($file_folder . $filenames);
	}
	$zip->close();

	foreach (glob("" . "*.txt") as $filename) {
		@unlink($filename);
	}
	echo "norsel_bundle";
	exit();
}

/*
|--------------------------------------------------------------------------
| print_barcode_one_128
|--------------------------------------------------------------------------
|
*/
if ($action == "print_barcode_one_128") {
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');

	// $sql = "select a.company_id,a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";

	$sql="SELECT a.id, a.product_no, a.company_id, a.location_id, a.party_id, a.product_date, a.prod_chalan_no,a.knitting_source, a.knitting_company, a.knit_location_id, a.production_basis, a.program_no,b.gsm, b.dia_width,b.color_id, b.dia_width_type,b.machine_dia ,
	b.yarn_lot,b.yrn_count_id,b.stitch_len,b.machine_gg,b.machine_dia,c.job_no_mst
	FROM subcon_production_mst a,subcon_production_dtls b,subcon_ord_dtls c WHERE a.id=$data[1] and b.id=$data[2] and a.id=b.mst_id and b.order_id = c.id ";

	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$job_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$machine_name='';
	$finish_dia = '';



	foreach ($result as $row) {
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}
		$job_no=$row[csf('job_no_mst')];

		$prod_date = date("d-m-Y", strtotime($row[csf('product_date')]));
		$prod_time = date("H:i", strtotime($row[csf('product_date')]));
		$grey_dia=$row[csf('dia_width')];
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$program_no=$row[csf('program_no')];
		$machine_name=$row[csf('machine_dia')];
		$machine_gauge=$row[csf('machine_gg')];
		$tube_type =$fabric_typee[$row[csf('dia_width_type')]];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_len')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yrn_count_id')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	$po_array = array();
	$booking_no_prefix = '';


	$i = 1;
	$barcode_array = array();
	$query = "select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.order_no 	from subcon_pro_roll_details a, subcon_ord_dtls b where a.po_breakdown_id=b.id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 and a.id in($data[0]) order by a.id";
	$res = sql_select($query);
	$pdf=new PDF_Code128('P','mm','a128');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',10);


	$i=2; $j=3; $k=0; $br=0; $n=0;
	foreach ($res as $row) {


		$order_no = $row[csf('order_no')];
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
		}



		$job=explode("-",$job_no);
		$pdf->SetXY($i, $j);
		$pdf->Write(0, $row[csf("barcode_no")]. " ".$company_short_name." Job No." . $job[2]);

		$pdf->SetXY($i, $j+3.3);
		$pdf->Write(0, "M/C: " . $machine_name . "; M/C Dia X GG-" . $machine_dia_width . "X" . $machine_gauge );//24

		$pdf->SetXY($i, $j+6.6);
		$pdf->Write(0, "Date: " . $prod_date ." ".$buyer_name);


		$pdf->SetXY($i, $j+9.9);
		$pdf->Write(0, "Po: " . $order_no);//24 $style_name


		$pdf->SetXY($i, $j+13.2);
		$pdf->Write(0, $comp);
		//$pdf->Write(0, "G/Dia: " . $grey_dia . "; SL: " . trim($stitch_length) . "; " . trim($tube_type) . "; F/Dia: " . trim($finish_dia));
		$pdf->SetXY($i, $j+16.5);
		$pdf->Write(0, "G/Dia: " . $grey_dia. "; F/Dia: " . trim($finish_dia). ";F/GSM: " . $gsm);

		$pdf->SetXY($i, $j+19.8);
		$pdf->Write(0, "Count:".$yarn_count . "; Lot: " . $yarn_lot);
		$pdf->SetXY($i, $j+23.1);
		$pdf->Write(0, "SL: " . trim($stitch_length).";Clr: " .substr($color, 0, 25));

		$pdf->SetXY($i, $j+26.4);
		$pdf->Write(0, "Prg: " . $program_no . "; Roll Wt: " . number_format($row[csf('qnty')], 2, '.', ''). " Kg");

		$pdf->SetXY($i, $j+29.7);
		$pdf->Write(0, "Roll No: " . $row[csf('roll_no')] .";D/Type: " .trim($tube_type));

		$pdf->Code128($i+1,$j+32.5,$row[csf("barcode_no")],50,8);

		$k++;
		$br++;
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

/*
|--------------------------------------------------------------------------
| print_barcode_one_128_v2
|--------------------------------------------------------------------------
|
*/
if ($action == "print_barcode_one_128_v2")
{
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');

	$data = explode("***", $data);
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

	$sql = "select a.company_id,a.basis, a.product_date as receive_date,a.party_id as buyer_id,b.order_id, b.cons_comp_id, b.gsm,b.dia_width, b.yarn_lot, b.yrn_count_id, b.brand, b.machine_id,b.shift, b.stitch_len, b.machine_dia, b.machine_gg, b.color_id, b.fabric_description, b.insert_date, b.color_range, a.knitting_source, a.knitting_company from subcon_production_mst a, subcon_production_dtls b where a.entry_form=159 and a.id=b.mst_id and b.id=$data[2]";
	//echo $sql;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$knit_comp = '';

	foreach ($result as $row) {

	$party_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));
		$receive_date=$row[csf('receive_date')];
		$shift_name=$shift_name[$row[csf('shift')]];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$grey_dia = $row[csf('dia_width')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_len')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $row[csf('brand')];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yrn_count_id')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		$machine_data = sql_select("select machine_no || '-' || brand as machine_name, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_name')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];

		$comp = $item_arr[$row[csf('cons_comp_id')]];
		$order_id .= $row[csf('order_id')] . ",";

		if($row[csf('knitting_source')]==1)
		{
			$knit_comp = $company_library[$row[csf('knitting_company')]];
		}
		else
		{
			$knit_comp = $supplier_arr[$row[csf('knitting_company')]];
		}


	}
	$order_id = chop($order_id, ',');

	$subcon_po_array = array();

	 $po_sql = sql_select("select a.subcon_job as job_no, b.cust_style_ref as style_ref_no, a.party_id as buyer_name, a.job_no_prefix_num as job_no_prefix_num, b.id, b.order_no as po_number, b.cust_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.id in($order_id)");
			foreach ($po_sql as $row) {
				$subcon_po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$subcon_po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$subcon_po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$subcon_po_array[$row[csf('id')]]['cust_buyer'] = $row[csf('cust_buyer')];
			}

	$i = 1;
	$barcode_array = array();
	 $query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty from subcon_pro_roll_details a left join subcon_production_dtls b on a.dtls_id=b.id where a.id in($data[0]) and a.entry_form=159";
	$res = sql_select($query);



	$pdf=new PDF_Code128('P','mm',array(65,50));
	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();
	$pdf->SetFont('Times','',8);


	$i=2; $j=1; $k=0; $br=0; $n=0;
	foreach ($res as $row) {
		$order_no = $subcon_po_array[$row[csf('po_breakdown_id')]]['no'];
		$cust_buyer = $subcon_po_array[$row[csf('po_breakdown_id')]]['cust_buyer'];
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=1; $k=0;
		}

		$pdf->Code128($i+5,$j,$row[csf("barcode_no")],50,8);
		$pdf->SetXY($i+5, $j+10);
		$pdf->Write(0, $row[csf("barcode_no")]. ",Dt:".change_date_format($receive_date). ",S:".$shift_name);

		$pdf->SetXY($i, $j+12);
		$pdf->Write(2, "S.Con:" . $order_no  );
		$pdf->SetXY($i, $j+14);
		$pdf->Write(4,",".$party_name );

		$pdf->SetXY($i, $j+19);
		$pdf->Write(0, "M/C:" .$machine_name. "," . $machine_dia_width . "X" . $machine_gauge. ",RW:" . number_format($row[csf('qnty')], 2, '.', ''));//24

		$pdf->SetXY($i, $j+22);
		$pdf->Write(0, "Clr:" .substr($color, 0, 35));

		$pdf->SetXY($i, $j+26);
		$pdf->Write(0, "Ct:".substr($yarn_count, 0, 12).",Lt:".substr($yarn_lot, 0, 22 ));

		$pdf->SetXY($i, $j+30);
		$pdf->Write(0, "Br:". $brand);

		$pdf->SetXY($i, $j+34);
		$pdf->Write(0, substr($comp, 0, 45));

		$pdf->SetXY($i, $j+38);
		$pdf->Write(0, "G/F Dia:" . $grey_dia. ",GSM:". $gsm.",SL:" . trim($stitch_length));

		$pdf->SetXY($i, $j+42);
		$pdf->Write(0, "Cust Buyer:" . $cust_buyer);

		$pdf->SetXY($i, $j+46);
		$pdf->Write(0, "Knit Com:" . $knit_comp);

		$k++;
		$br++;
	}

	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

/*
|--------------------------------------------------------------------------
| print_barcode_one_128_v2
|--------------------------------------------------------------------------
|
*/
if ($action == "print_barcode_one_128_v3")
{
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');

	$data = explode("***", $data);
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');

	$sql = "select a.company_id,a.basis, a.product_date as receive_date,a.party_id as buyer_id,b.order_id, b.cons_comp_id, b.gsm,b.dia_width, b.yarn_lot, b.yrn_count_id, b.brand, b.machine_id,b.shift, b.stitch_len, b.machine_dia, b.machine_gg, b.color_id, b.fabric_description, b.insert_date, b.color_range from subcon_production_mst a, subcon_production_dtls b where a.entry_form=159 and a.id=b.mst_id and b.id=$data[2]";
	//echo $sql;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	foreach ($result as $row) {

	$party_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));
		$receive_date=$row[csf('receive_date')];
		$shift_name=$shift_name[$row[csf('shift')]];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$grey_dia = $row[csf('dia_width')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_len')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $row[csf('brand')];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yrn_count_id')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		$machine_data = sql_select("select machine_no || '-' || brand as machine_name, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_name')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];

		$comp = $item_arr[$row[csf('cons_comp_id')]];
		$order_id .= $row[csf('order_id')] . ",";
	}
	$order_id = chop($order_id, ',');

	$subcon_po_array = array();
	 $po_sql = sql_select("select a.subcon_job as job_no, b.cust_style_ref as style_ref_no, a.party_id as buyer_name, a.job_no_prefix_num as job_no_prefix_num, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.id in($order_id)");
			foreach ($po_sql as $row) {
				$subcon_po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$subcon_po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$subcon_po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
			}

	$i = 1;
	$barcode_array = array();
	 $query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty from subcon_pro_roll_details a left join subcon_production_dtls b on a.dtls_id=b.id where a.id in($data[0]) and a.entry_form=159";
	$res = sql_select($query);



	// $pdf=new PDF_Code128('P','mm',array(307,259));
	$pdf=new PDF_Code128('P','mm', array(65,55));
	$pdf->AddPage();
	// $pdf->SetFont('Times','',10);
	$pdf->SetFont('Calibri','',10);
	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin(0);

	$i=2; $j=1; $k=0; $br=0; $n=0;
	foreach ($res as $row) {
		$order_no = $subcon_po_array[$row[csf('po_breakdown_id')]]['no'];
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=1; $k=0;
		}

		$pdf->Code128($i+1,$j,$row[csf("barcode_no")],50,8);
		$pdf->SetXY($i, $j+10);
		$pdf->Write(0, $row[csf("barcode_no")]. ",Dt:".change_date_format($receive_date). ",S:".$shift_name);

		$pdf->SetFont('Calibri','',8);
		$pdf->SetXY($i, $j+12);
		$pdf->Write(2, "S.Con:" . $order_no  );
		$pdf->SetXY($i, $j+14);
		$pdf->Write(4,",".$party_name );

		$pdf->SetXY($i, $j+19);
		$pdf->Write(0, "M/C:" .$machine_name. "," . $machine_dia_width . "X" . $machine_gauge. ",RW:" . number_format($row[csf('qnty')], 2, '.', ''));//24

		$pdf->SetXY($i, $j+22);
		$pdf->Write(0, "Clr:" .substr($color, 0, 35));

		$pdf->SetXY($i, $j+26);
		$pdf->Write(0, "Ct:".$yarn_count.",Lt:".$yarn_lot);

		$pdf->SetXY($i, $j+30);
		$pdf->Write(0, "Br:". $brand);

		$pdf->SetXY($i, $j+34);
		$pdf->Write(0, substr($comp, 0, 45));

		$pdf->SetXY($i, $j+38);
		$pdf->Write(0, "G/F Dia:" . $grey_dia. ",GSM:". $gsm.",SL:" . trim($stitch_length));

		$k++;
		$br++;
	}

	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if ($action == "print_barcode_ccl_Old") {
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$item_arr=return_library_array("select id,const_comp from lib_subcon_charge",'id','const_comp');
	$machine_arr=return_library_array("select id, machine_no || '-' || brand as machine_name from lib_machine_name where category_id=1  and status_active=1 and is_deleted=0 and is_locked=0  order by machine_name",'id','machine_name');

	$party_arr = return_library_array("select buy.id,buy.buyer_name,buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", 'id', 'short_name');

	// $sql = "select a.company_id,a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";

	$sql="SELECT a.id, a.product_no, a.company_id, a.location_id, a.party_id, a.product_date, a.prod_chalan_no,a.knitting_source, a.knitting_company, a.knit_location_id, a.production_basis, a.program_no,b.gsm, b.dia_width,b.color_id, b.dia_width_type,b.machine_dia ,
	b.yarn_lot,b.yrn_count_id,b.stitch_len,b.machine_gg,b.machine_dia,b.shift,b.cons_comp_id,b.machine_id,b.color_range,b.brand,b.operator_name,c.job_no_mst
	FROM subcon_production_mst a,subcon_production_dtls b,subcon_ord_dtls c WHERE a.id=$data[1] and b.id=$data[2] and a.id=b.mst_id and b.order_id = c.id ";

	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$job_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$dia_width = '';
	$machine_name='';
	$finish_dia = '';
	$shift = '';
	$comp = '';
	$machine_dia_width = '';
	$machine_gauge = '';
	$colorRange = '';
	$operator_name = '';
	$color = '';



	foreach ($result as $row) {
		if ($row[csf('knitting_source')] == 1) {
			$knitting_company = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$knitting_company = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}
		$job_no=$row[csf('job_no_mst')];

		$prod_date = date("d-m-Y", strtotime($row[csf('product_date')]));
		$prod_time = date("H:i", strtotime($row[csf('product_date')]));
		$grey_dia=$row[csf('dia_width')];
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$dia_width = $row[csf('dia_width')];
		$finish_dia = $row[csf('width')];
		$shift = $shift_name[$row[csf('shift')]];
		$program_no=$row[csf('program_no')];
		$machine_dia_width=$row[csf('machine_dia')];
		$machine_gauge=$row[csf('machine_gg')];
		$party_name=$party_arr[$row[csf('party_id')]];
		$tube_type =$fabric_typee[$row[csf('dia_width_type')]];
		$machine_name =$machine_arr[$row[csf('machine_id')]];
		$colorRange =$color_range[$row[csf('color_range')]];
		$operator_name =$operator_name_arr[$row[csf('operator_name')]];
		$color_name =$color_arr[$row[csf('color_id')]];

		$cons_comp =$item_arr[$row[csf('cons_comp_id')]];
		$cons_comp_data = explode(',',$cons_comp);
		$cons = $cons_comp_data[0];
		$comp = $cons_comp_data[1];

		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_len')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $row[csf('brand')];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yrn_count_id')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	$po_array = array();
	$booking_no_prefix = '';


	$i = 1;
	$barcode_array = array();
	$query = "select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.order_no 	from subcon_pro_roll_details a, subcon_ord_dtls b where a.po_breakdown_id=b.id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 and a.id in($data[0]) order by a.id";
	//echo $query;
	$res = sql_select($query);
	$pdf=new PDF_Code128('P','mm', array(65,45));
	//$pdf=new PDF_Code128('P','mm','a128');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',10);
	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin(0);


	$i=2; $j=3; $k=0; $br=0; $n=0;
	foreach ($res as $row) {


		$order_no = $row[csf('order_no')];
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
		}

		$time = date("h:i:a");

		$pdf->Code128($i+5,$j,$row[csf("barcode_no")],50,8);

		$pdf->SetXY($i+5, $j+10);
		$pdf->Write(0, $row[csf("barcode_no")].' Time : '.$time);

		$pdf->SetXY($i, $j+13.5);
		$pdf->SetFont('Arial','B',8);
		$pdf->Write(0, $prod_date .", M/C:".$machine_name .", Shift:".$shift .", KC:".$knitting_company);

		$pdf->SetXY($i, $j+16.5);
		$pdf->Write(0, "Party# " . $party_name.", P:".$program_no);

		$pdf->SetXY($i, $j+19.5);
		$pdf->Write(0, "Ord#" . $order_no );

		$pdf->SetXY($i, $j+22.5);
		$pdf->Write(0, trim($cons) . " " . $machine_dia_width . "X" . $machine_gauge. " " . trim($gsm). " " . trim($dia_width) . " " . trim($tube_type) );

		$pdf->SetXY($i, $j+25.5);
		$pdf->Write(0, trim($comp) );

		$pdf->SetXY($i, $j+28.5);
		$pdf->Write(0, "Fab Clr: ".$color_name);

		$pdf->SetXY($i, $j+31.5);
		$pdf->Write(0, $colorRange.", YC: ".$yarn_count);

		$pdf->SetXY($i, $j+34.5);
		$pdf->Write(0, "st/L:" . trim($stitch_length).", B:".$brand.", L:".$yarn_lot);

		$pdf->SetXY($i, $j+37.5);
		$pdf->Write(0, substr($operator_name, 0, 25) );


		$pdf->SetXY($i+38, $j+37.5);
		$pdf->Write(0, " R.WT: ");
		$pdf->Write(0, number_format($row[csf('qnty')], 2, '.', ''). " Kg" );

		$k++;
		$br++;
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}
if ($action == "print_barcode_ccl") {
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$item_arr=return_library_array("select id,const_comp from lib_subcon_charge",'id','const_comp');
	$machine_arr=return_library_array("select id, machine_no || '-' || brand as machine_name from lib_machine_name where category_id=1  and status_active=1 and is_deleted=0 and is_locked=0  order by machine_name",'id','machine_name');

	$party_arr = return_library_array("select buy.id,buy.buyer_name,buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", 'id', 'short_name');

	$prog_fab_dia_arr = return_library_array("select a.program_no,c.fabric_dia from subcon_production_mst a,subcon_production_dtls b,subcon_planning_dtls c WHERE a.id=$data[1] and b.id=$data[2] and a.id=b.mst_id and c.id=a.program_no", 'program_no', 'fabric_dia');


	// $sql = "select a.company_id,a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";

	$sql="SELECT a.id, a.product_no, a.company_id, a.location_id, a.party_id, a.product_date, a.prod_chalan_no,a.knitting_source, a.knitting_company, a.knit_location_id, a.production_basis, a.program_no,b.gsm, b.dia_width,b.color_id, b.dia_width_type,b.machine_dia ,
	b.yarn_lot,b.yrn_count_id,b.stitch_len,b.machine_gg,b.machine_dia,b.shift,b.cons_comp_id,b.machine_id,b.color_range,b.brand,b.operator_name,c.job_no_mst,a.program_no
	FROM subcon_production_mst a,subcon_production_dtls b,subcon_ord_dtls c WHERE a.id=$data[1] and b.id=$data[2] and a.id=b.mst_id and b.order_id = c.id ";

	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$job_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$dia_width = '';
	$machine_name='';
	$finish_dia = '';
	$shift = '';
	$comp = '';
	$machine_dia_width = '';
	$machine_gauge = '';
	$colorRange = '';
	$operator_name = '';
	$color = '';



	foreach ($result as $row) {
		if ($row[csf('knitting_source')] == 1) {
			$knitting_company = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$knitting_company = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}
		$job_no=$row[csf('job_no_mst')];

		$prod_date = date("d-m-Y", strtotime($row[csf('product_date')]));
		$prod_time = date("H:i", strtotime($row[csf('product_date')]));
		$grey_dia=$row[csf('dia_width')];
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$dia_width = $row[csf('dia_width')];
		$finish_dia = $row[csf('width')];
		$shift = $shift_name[$row[csf('shift')]];
		$program_no=$row[csf('program_no')];
		$machine_dia_width=$row[csf('machine_dia')];
		$machine_gauge=$row[csf('machine_gg')];
		$party_name=$party_arr[$row[csf('party_id')]];
		$tube_type =$fabric_typee[$row[csf('dia_width_type')]];
		$machine_name =$machine_arr[$row[csf('machine_id')]];
		$colorRange =$color_range[$row[csf('color_range')]];
		$operator_name =$operator_name_arr[$row[csf('operator_name')]];
		$color_name =$color_arr[$row[csf('color_id')]];
		$prog_fab_dia =$prog_fab_dia_arr[$row[csf('program_no')]];

		$cons_comp =$item_arr[$row[csf('cons_comp_id')]];
		$cons_comp_data = explode(',',$cons_comp);
		$cons = $cons_comp_data[0];
		$comp = $cons_comp_data[1];

		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_len')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $row[csf('brand')];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yrn_count_id')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	$po_array = array();
	$booking_no_prefix = '';


	$i = 1;
	$barcode_array = array();
	$query = "select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.order_no 	from subcon_pro_roll_details a, subcon_ord_dtls b where a.po_breakdown_id=b.id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 and a.id in($data[0]) order by a.id";
	//echo $query;
	$res = sql_select($query);
	$pdf=new PDF_Code128('P','mm', array(65,45));
	//$pdf=new PDF_Code128('P','mm','a128');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',10);
	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin(0);


	$i=2; $j=3; $k=0; $br=0; $n=0;
	foreach ($res as $row) {


		$order_no = $row[csf('order_no')];
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
		}

		$time = date("h:i:a");

		$pdf->Code128($i+5,$j,$row[csf("barcode_no")],50,8);

		$pdf->SetXY($i+5, $j+10);
		$pdf->Write(0, $row[csf("barcode_no")].' Time : '.$time);

		$pdf->SetXY($i, $j+13.5);
		$pdf->SetFont('Arial','B',8);
		$pdf->Write(0, $prod_date .", M/C:".$machine_name .", Shift:".$shift .", KC:".$knitting_company);

		$pdf->SetXY($i, $j+16.5);
		$pdf->Write(0, "Party# " . $party_name.", P:".$program_no);

		$pdf->SetXY($i, $j+19.5);
		$pdf->Write(0, "Ord#" . $order_no );

		$pdf->SetXY($i, $j+22.5);
		$pdf->Write(0, trim($cons) . " " . $machine_dia_width . "X" . $machine_gauge. " " . trim($gsm). " " . trim($dia_width) . " " . trim($tube_type) );

		//$pdf->SetXY($i, $j+25.5);
		//$pdf->Write(0, trim($comp) );
		$pdf->SetXY($i, $j+25.5);
		$pdf->Write(0, "F Dia:".trim($prog_fab_dia).", Fab Clr:".$color_name);

		$pdf->SetXY($i, $j+28.5);
		//$pdf->Write(0, $colorRange);
		$pdf->Write(0, "L:" . trim($yarn_lot).", YC: ".$yarn_count);

		$pdf->SetXY($i, $j+31.5);
		$pdf->Write(0, "B:".$brand.", S.L:".trim($stitch_length));

		$pdf->SetXY($i, $j+34.5);
		//$pdf->Write(0, "st/L:" . trim($stitch_length).", B:".$brand.", L:".$yarn_lot);
		$pdf->Write(0, substr($operator_name, 0, 25) );

		//$pdf->SetXY($i, $j+37.5);



		$pdf->SetXY($i+38, $j+37.5);
		$pdf->Write(0, " R.WT: ");
		$pdf->Write(0, number_format($row[csf('qnty')], 2, '.', ''). " Kg" );

		$k++;
		$br++;
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}


if ($action == "direct_print_barcode_4_auto")
{
	//require('../../ext_resource/pdf/code128.php');
	//define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	//$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');

	$machine_library = sql_select("select id, machine_no, brand, norsel_printer_api, norsel_printer from lib_machine_name");
	foreach ($machine_library as $val)
	{
		$machine_no_arr[$val[csf("id")]] = $val[csf("machine_no")];
		$machine_brand_arr[$val[csf("id")]] = $val[csf("brand")];
		//$machine_norsel_data[$val[csf("id")]]["norsel_printer_api"] = $val[csf("norsel_printer_api")];
		//$machine_norsel_data[$val[csf("id")]]["norsel_printer"] = $val[csf("norsel_printer")];
	}

	$user_api_data = sql_select("select norsel_printer_api, norsel_printer from user_passwd where id=$userId and status_active=1 ");
	$norsel_printer_api = $user_api_data[0][csf("norsel_printer_api")];
	$norsel_printer = $user_api_data[0][csf("norsel_printer")];

	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');




	$sql="SELECT a.id, a.product_no, a.company_id, a.location_id, a.party_id, a.product_date, a.prod_chalan_no,a.knitting_source, a.knitting_company, a.knit_location_id, a.production_basis, a.program_no,b.gsm, b.dia_width,b.color_id, b.dia_width_type,b.machine_dia, b.yarn_lot,b.yrn_count_id,b.stitch_len,b.machine_gg, c.job_no_mst, a.insert_date, b.shift, b.operator_name, b.machine_id, b.fabric_description, b.brand, b.yarn_lot,c.cust_style_ref
	FROM subcon_production_mst a,subcon_production_dtls b,subcon_ord_dtls c WHERE a.id=$data[1] and b.id=$data[2] and a.id=b.mst_id and b.order_id = c.id ";


	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$job_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$machine_dia='';
	$finish_dia = '';
	$shift_name_id = '';
	$operatorName = '';
	$machine_id = '';
	$machicompne_id = '';
	$cust_style_ref = '';
	$party_id = '';



	foreach ($result as $row) {
		// if ($row[csf('knitting_source')] == 1) {
		// 	$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		// } else if ($row[csf('knitting_source')] == 3) {
		// 	$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		// }
		$job_no=$row[csf('job_no_mst')];

		$prod_date = date("d-m-Y", strtotime($row[csf('product_date')]));
		$prod_time = date("H:i:a", strtotime($row[csf('insert_date')]));
		$grey_dia=$row[csf('dia_width')];
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$shift_name_id = $row[csf('shift')];
		$operatorName = $row[csf('operator_name')];
		$machine_id = $row[csf('machine_id')];
		$party_id = $row[csf('party_id')];
		$cust_style_ref = $row[csf('cust_style_ref')];
		$comp = $row[csf('fabric_description')];
		$program_no=$row[csf('program_no')];
		$machine_dia=$row[csf('machine_dia')];
		$machine_gauge=$row[csf('machine_gg')];
		$tube_type =$fabric_typee[$row[csf('dia_width_type')]];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_len')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yrn_count_id')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		$company_name = return_field_value("company_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	$po_array = array();
	$booking_no_prefix = '';


	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.order_no 	from subcon_pro_roll_details a, subcon_ord_dtls b where a.po_breakdown_id=b.id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 and a.id in($data[0]) order by a.id";
	//echo $query;
	$res = sql_select($query);

	foreach ($res as $row)
	{
		$order_no = $row[csf('order_no')];
		$response = array(
			"printer_id" => $norsel_printer,
			"company_name"=>$company_name,
			"program_no"=>$program_no ,
			"prod_date"=>$prod_date ,
			"prod_time"=>$prod_time ,
			"shift_name"=>$shift_name[$shift_name_id],
			"operatorName"=>$operatorName,
			"order_no"=>$order_no,
			"buyer_name"=>$buyer_arr[$party_id],
			"cust_style_ref"=>$cust_style_ref,
			"machine_id"=>$machine_id,
			"machine_dia"=>$machine_dia,
			"machine_gauge"=>$machine_gauge,
			"finish_dia"=>trim($finish_dia),
			"stitch_length"=>trim($stitch_length),
			"gsm"=>$gsm,
			"color"=>$color,
			"yarn_count"=>$yarn_count,
			"brand"=>$brand,
			"yarn_type_cond"=>$yarn_type_cond,
			"yarn_lot"=>$yarn_lot,
			"yarn_type_cond"=>$yarn_type_cond,
			"yarn_lot"=>$yarn_lot,
			"roll_no"=>$row[csf('roll_no')],
			"qnty"=>$row[csf('qnty')],
			"barcode"=>$row[csf("barcode_no")]

    	);
	}

	//echo json_encode($response);

	$data_json = json_encode($response);

	/*echo "<pre>";
	print_r($data_json);
	die;*/

	// API URL to send data
	$url = "http://".$norsel_printer_api;
	//$url = 'http://192.168.10.233:9080/api/print';

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$headers = array(
	   "Content-Type: application/json",
	);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

	curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);

	//for debug only!
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	$resp = curl_exec($curl);

	/*$error = curl_error($curl);
	if($error){
		die("Curl returned some errors: ". $error);
	}*/

	curl_close($curl);
	//var_dump($resp);
}
if ($action == "direct_print_barcode_4")
{
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');

	$sql="SELECT a.id, a.product_no, a.company_id, a.location_id, a.party_id, a.product_date, a.prod_chalan_no,a.knitting_source, a.knitting_company, a.knit_location_id, a.production_basis, a.program_no,b.gsm, b.dia_width,b.color_id, b.dia_width_type,b.machine_dia, b.yarn_lot,b.yrn_count_id,b.stitch_len,b.machine_gg, c.job_no_mst, a.insert_date, b.shift, b.operator_name, b.machine_id, b.fabric_description, b.brand, b.yarn_lot,c.cust_style_ref
	FROM subcon_production_mst a,subcon_production_dtls b,subcon_ord_dtls c WHERE a.id=$data[1] and b.id=$data[2] and a.id=b.mst_id and b.order_id = c.id ";


	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$job_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$machine_dia='';
	$finish_dia = '';
	$shift_name_id = '';
	$operatorName = '';
	$machine_id = '';
	$machicompne_id = '';
	$cust_style_ref = '';
	$party_id = '';



	foreach ($result as $row) {
		// if ($row[csf('knitting_source')] == 1) {
		// 	$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		// } else if ($row[csf('knitting_source')] == 3) {
		// 	$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		// }
		$job_no=$row[csf('job_no_mst')];

		$prod_date = date("d-m-Y", strtotime($row[csf('product_date')]));
		$prod_time = date("H:i:a", strtotime($row[csf('insert_date')]));
		$grey_dia=$row[csf('dia_width')];
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$shift_name_id = $row[csf('shift')];
		$operatorName = $row[csf('operator_name')];
		$machine_id = $row[csf('machine_id')];
		$party_id = $row[csf('party_id')];
		$cust_style_ref = $row[csf('cust_style_ref')];
		$comp = $row[csf('fabric_description')];
		$program_no=$row[csf('program_no')];
		$machine_dia=$row[csf('machine_dia')];
		$machine_gauge=$row[csf('machine_gg')];
		$tube_type =$fabric_typee[$row[csf('dia_width_type')]];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_len')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yrn_count_id')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		$company_name = return_field_value("company_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	$po_array = array();
	$booking_no_prefix = '';


	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.order_no 	from subcon_pro_roll_details a, subcon_ord_dtls b where a.po_breakdown_id=b.id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 and a.id in($data[0]) order by a.id";
	//echo $query;
	$res = sql_select($query);

	$pdf=new PDF_Code128('P','mm', array(65,55));
	$pdf->AddPage();
	$pdf->SetFont('Arial','',10);

	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin(0);


	$i=2; $j=3; $k=0; $br=0; $n=0;
	foreach ($res as $row)
	{
		$order_no = $row[csf('order_no')];
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
		}

		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY($i, $j);
		$pdf->Write(0, substr($company_name,0,45).", P:".$program_no );

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($i, $j+3.2);
		$pdf->Write(0, $prod_date .", ". $prod_time .", Sh:" . $shift_name[$shift_name_id].", OP:".$operatorName);

		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY($i, $j+7.2);
		$pdf->Write(0, "OR- " . $order_no.', ');
		$pdf->SetFont('Arial','',8);
		$pdf->Write(0, $buyer_arr[$party_id]);

		$pdf->SetXY($i, $j+11.2);
		$pdf->Write(0, "Style/PO: ".$cust_style_ref );

		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($i, $j+15.2);
		$pdf->Write(0, "M/C: " . $machine_id );
		$pdf->SetFont('Arial','B',8);
		$pdf->Write(0, ", ".$machine_dia."X".$machine_gauge.", F/Dia: " . trim($finish_dia).", St/L: " . trim($stitch_length) );

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($i, $j+19.2);
		$pdf->Write(0, substr($comp,0,45) );

		$pdf->SetXY($i, $j+23.2);
		$pdf->Write(0, "GSM: " . $gsm.", CLR: " .substr($color, 0, 25));

		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY($i, $j+27.2);
		$pdf->Write(0, "Y/C:".$yarn_count . ", Y/B: " . $brand);

		$pdf->SetXY($i, $j+31.2);
		$pdf->Write(0, "Y/L: " . $yarn_lot. ", Y/T: " .$yarn_type_cond );

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($i, $j+35.2);
		$pdf->Write(0, "Roll: " . $row[csf('roll_no')] );
		$pdf->SetFont('Arial','B',8);
		$pdf->Write(0, ", W/T: " . number_format($row[csf('qnty')], 2, '.', ''). " Kg");

		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY($i, $j+39.2);
		$pdf->Write(0, $row[csf("barcode_no")]);

		$pdf->Code128($i+1,$j+42.2,$row[csf("barcode_no")],50,8);

		$k++;
		$br++;
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}


/*
|--------------------------------------------------------------------------
| report_barcode_generation
|--------------------------------------------------------------------------
|
*/
if ($action == "report_barcode_generation")
{
	$data = explode("***", $data);
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');

	$sql = "select a.company_id,a.basis, a.product_date as receive_date,a.party_id as buyer_id,b.order_id, b.cons_comp_id, b.gsm,b.dia_width, b.yarn_lot, b.yrn_count_id, b.brand, b.machine_id, b.stitch_len, b.machine_dia, b.machine_gg, b.color_id, b.fabric_description, b.insert_date, b.color_range from subcon_production_mst a, subcon_production_dtls b where a.entry_form=159 and a.id=b.mst_id and b.id=$data[1]";
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	foreach ($result as $row) {

	$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
		//$booking_no = $row[csf('booking_no')];
		//$booking_without_order = $row[csf('booking_without_order')];

		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		//$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		//$operator_name = $row[csf('operator_name')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];


			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

		//$comp = '';
		$comp = $item_arr[$row[csf('cons_comp_id')]];
		$order_id .= $row[csf('order_id')] . ",";
	}
	$order_id = chop($order_id, ',');
	//$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
	$subcon_po_array = array();
	 $po_sql = sql_select("select a.subcon_job as job_no, b.cust_style_ref as style_ref_no, a.party_id as buyer_name, a.job_no_prefix_num as job_no_prefix_num, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.id in($order_id)");
			foreach ($po_sql as $row) {
				$subcon_po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$subcon_po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$subcon_po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
			}
	//print_r($po_array);
	$i = 1;
	$barcode_array = array();
	 $query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty from subcon_pro_roll_details a left join subcon_production_dtls b on a.dtls_id=b.id where a.id in($data[0]) and a.entry_form=159";
	$res = sql_select($query);
	echo '<table width="800" border="0"><tr>';
	foreach ($res as $row) {
		$barcode_array[$i] = $row[csf('barcode_no')];
		 $order_nos= $subcon_po_array[$row[csf('po_breakdown_id')]]['no'];
		//echo $order_nos.'ddddddddd';
		$txt = $row[csf('barcode_no')] . "; " . $party_name . " Job No." . $subcon_po_array[$row[csf('po_breakdown_id')]]['prefix'] . ";<br>";
		$txt .= "M/C: " . $machine_name . "; M/C Dia X Gauge-" . $machine_dia_width . "X" . $machine_gauge . ";<br>";
		$txt .= "Date: " . $prod_date . ";<br>";
		$txt .= "Buyer: " . $buyer_name . ", Order No: " . $order_nos . ";<br>";
		$txt .= $comp . "<br>";
		$txt .= "G/Dia: " . $grey_dia . "; SL: " . trim($stitch_length) . "; " . trim($tube_type) . "; F/Dia: " . trim($finish_dia) . ";<br>";
		$txt .= "GSM: " . $gsm . "; ";
		$txt .= $yarn_count . "; Lot: " . $yarn_lot . ";<br>";
		$txt .= "Prg: " . $program_no . "; Roll Wt: " . number_format($row[csf('qnty')], 2, '.', '') . " Kg;<br>";
		$txt .= "Custom Roll No: " . $row[csf('roll_no')] . ";";
		if (trim($color) != "") $txt .= " Color: " . trim($color) . ";<br>";
		if (trim($row[csf('fabric_grade')]) != "") $txt .= "Grade: " . trim($row[csf('fabric_grade')]) . ";";
		if ($operator_name != "") $txt .= "OP: " . $operator_name_arr[$operator_name] . ";";

		echo '<td style="padding-left:7px;padding-top:10px;padding-bottom:5px"><div id="div_' . $i . '"></div>' . $txt . '</td>';//border:dotted;
		if ($i % 3 == 0) echo '</tr><tr>';
		$i++;
	}
	echo '</tr></table>';
	?>

    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
        var barcode_array =<? echo json_encode($barcode_array); ?>;
        function generateBarcode(td_no, valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
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
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#div_" + td_no).show().barcode(value, btype, settings);
        }

        for (var i in barcode_array) {
            generateBarcode(i, barcode_array[i]);
        }
    </script>
	<?
	exit();
}


if($action=="auto_populate_program_data")
{
	$data=explode("**", $data);
	$companyID=$data[0];
	$progranNo=$data[1];
	$machine_floor_arr = return_library_array("select id, floor_id from lib_machine_name where status_active=1", "id", "floor_id");
	$shift_duration_entry_arr = sql_select("select shift_name, start_time, end_time from shift_duration_entry where production_type=5 and status_active=1 order by shift_name");

	$shift_name="";
	foreach ($shift_duration_entry_arr as $val)
	{

		/* 	$start_time = strtotime($val[csf('start_time')]);
			$end_time = strtotime($val[csf('end_time')]);
			if($start_time > $end_time){
				$end_time = strtotime('+1 day',$end_time);
			}
			$curr_time = strtotime(date("G:i"));
			if( $curr_time >= $start_time && $curr_time <= $end_time)
			{
				$shift_name = $val[csf('shift_name')];
			}
		 */
		$curr_time = new DateTime('now');
		$s_time = new DateTime($val[csf('start_time')]);
		$e_time = new DateTime($val[csf('end_time')]);

		$current_time = $curr_time->format('Y-m-d H:i:s a');
		$start_time = $s_time->format('Y-m-d H:i:s a');
		$end_time = $e_time->format('Y-m-d H:i:s a');

		if($start_time > $end_time){
			$end_time = $e_time->modify('+1 day')->format('Y-m-d H:i:s a');
			$start_time = $s_time->modify('-1 day')->format('Y-m-d H:i:s a');
			//if($current_time<$start_time)
		}
		//echo $current_time."=".$start_time."=".$end_time."<br>";
		if( $current_time >= $start_time && $current_time <= $end_time && $shift_name=="")
		{
			$shift_name = $val[csf('shift_name')];
		}

		//$current_time."=".$start_time."=".$end_time.
		//2022-03-28 00:31:42 =  2022-03-28 22:01:00 =  2022-03-29 06:00:00
	}


	$sql = "
		SELECT
			distinct spd.id,spm.buyer_id AS party_id, spm.gsm_weight, spm.job_no, spm.subcon_order_id,
			spd.knitting_source, spd.knitting_party, spd.color_id, spd.color_range, spd.machine_dia, spd.width_dia_type, spd.machine_gg, spd.fabric_dia, spd.program_qnty, spd.stitch_length, spd.spandex_stitch_length, spd.draft_ratio, spd.machine_id, spd.machine_capacity, spd.distribution_qnty, spd.status, spd.start_date, spd.end_date, spd.program_date, spd.feeder, spd.remarks, spd.save_data, spd.location_id,som.location_id as order_location_id, spd.advice,
			sppd.id, sppd.mst_id, sppd.dtls_id, sppd.po_id, sppd.determination_id, sppd.gsm_weight, sppd.dia, sppd.buyer_id, sppd.fabric_desc,
			sod.main_process_id, sod.order_no
		FROM
			subcon_planning_mst spm
			INNER JOIN subcon_planning_dtls spd ON spm.id = spd.mst_id
			INNER JOIN subcon_planning_plan_dtls sppd ON spd.id = sppd.dtls_id
			INNER JOIN subcon_ord_mst som ON spm.subcon_order_id = som.id
			INNER JOIN subcon_ord_dtls sod ON som.id=sod.mst_id
		WHERE
			spd.status_active = 1
			AND spd.is_deleted = 0
			AND spd.id = $progranNo
			AND spm.company_id = $companyID
		ORDER BY
			sppd.mst_id ASC
	";
	//echo $sql; die;


	$nameArray=sql_select( $sql );
	if(empty($nameArray))
	{
		echo get_empty_data_msg();
		die;
	}
	$color_arr = get_color_array();

	$idArr = array();
	foreach($nameArray as $row)
	{
		$idArr[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
	}

	$sqlProduction = "
		SELECT
			spm.subcon_order_id,
			spd.program_qnty,
			sppd.id, sppd.mst_id, sppd.dtls_id,
			spdd.product_qnty
		FROM
			subcon_planning_mst spm
			INNER JOIN subcon_planning_dtls spd ON spm.id = spd.mst_id
			INNER JOIN subcon_planning_plan_dtls sppd ON spd.id = sppd.dtls_id
			INNER JOIN subcon_production_dtls spdd ON sppd.dtls_id = spdd.order_id
		WHERE
			spd.id IN(".implode(',', $idArr).")
			AND spd.status_active = 1
			AND spd.is_deleted = 0
			AND spdd.status_active = 1
			AND spdd.is_deleted = 0
	";

	$rsltProduction = sql_select( $sqlProduction );
	$productionData = array();
	foreach($rsltProduction as $row)
	{
		$productionData[$row[csf('dtls_id')]]['programQty'] = $row[csf('program_qnty')];
		$productionData[$row[csf('dtls_id')]]['productQty'] = $row[csf('product_qnty')];
		$productionData[$row[csf('dtls_id')]]['balanceQty'] = $row[csf('program_qnty')] - $row[csf('product_qnty')];
	}


	/*$sql_count_feed = sql_select("SELECT dtls_id,listagg(cast(count_id as varchar(4000)),',') within group(order by count_id) as count_id,listagg(cast(yarn_lot as varchar(4000)),',') within group(order by yarn_lot) as yarn_lot  FROM subcon_planning_feeding_dtls WHERE dtls_id in(".implode(',', $idArr).") group by dtls_id");
	foreach($sql_count_feed as $row)
	{
		$yarnDataArr[$row[csf('dtls_id')]]['count_id'] = $row[csf('count_id')];
		$yarnDataArr[$row[csf('dtls_id')]]['yarn_lot'] = $row[csf('yarn_lot')];
	}*/

	// confirm by Jahid Hasan vai
	$sql_yarn_dtls=sql_select("SELECT subcon_planning_dtls_id as dtls_id, listagg(cast(yarn_count_id as varchar(4000)),',') within group(order by yarn_count_id) as count_id, listagg(cast(yarn_lot as varchar(4000)),',') within group(order by yarn_lot) as yarn_lot, listagg(cast(brand as varchar(4000)),',') within group(order by brand) as brand from subcon_planning_yarn_dtls_breakdown where subcon_planning_dtls_id in(".implode(',', $idArr).") group by subcon_planning_dtls_id");
	foreach($sql_yarn_dtls as $row)
	{
		$yarnDataArr[$row[csf('dtls_id')]]['count_id'] = $row[csf('count_id')];
		$yarnDataArr[$row[csf('dtls_id')]]['yarn_lot'] = $row[csf('yarn_lot')];
		$yarnDataArr[$row[csf('dtls_id')]]['brand'] = $row[csf('brand')];
	}



	$rsltProduction = sql_select( $sqlProduction );
	$productionData = array();
	foreach($rsltProduction as $row)
	{
		$productionData[$row[csf('dtls_id')]]['programQty'] = $row[csf('program_qnty')];
		$productionData[$row[csf('dtls_id')]]['productQty'] = $row[csf('product_qnty')];
		$productionData[$row[csf('dtls_id')]]['balanceQty'] = $row[csf('program_qnty')] - $row[csf('product_qnty')];
	}
	foreach ($nameArray as $row)
	{
		if(empty($productionData[$row[csf('dtls_id')]]))
		{
			$info = $row[csf('dtls_id')].'*'.$row[csf('knitting_source')].'*'.$row[csf('knitting_party')].'*'.$row[csf('order_no')].'*'.$row[csf('color_range')].'*1*'.$row[csf('gsm_weight')].'*'.$row[csf('machine_dia')].'*'.$row[csf('width_dia_type')].'*'.$color_arr[$row[csf('color_id')]].'*'.$row[csf('color_id')].'*'.$row[csf('machine_gg')].'*'.$row[csf('stitch_length')].'*'.$row[csf('machine_dia')].'*'.$row[csf('machine_id')].'*'.$row[csf('program_qnty')].'*'.$row[csf('job_no')].'*'.$row[csf('subcon_order_id')].'*'.$row[csf('location_id')].'*'.$row[csf('determination_id')].'*'.$row[csf('fabric_desc')].'*'.$row[csf('main_process_id')].'*'.$row[csf('po_id')].'*'.$row[csf('party_id')].'*'.$yarnDataArr[$row[csf('dtls_id')]]['yarn_lot'].'*'.$yarnDataArr[$row[csf('dtls_id')]]['count_id'].'*'.$row[csf('order_location_id')].'*'.$machine_floor_arr[$row[csf('machine_id')]].'*'.$shift_name.'*'.$yarnDataArr[$row[csf('dtls_id')]]['brand'];
		}
		else
		{
			if($productionData[$row[csf('dtls_id')]]['balanceQty'] > 0)
			{
				$info = $row[csf('dtls_id')].'*'.$row[csf('knitting_source')].'*'.$row[csf('knitting_party')].'*'.$row[csf('order_no')].'*'.$row[csf('color_range')].'*1*'.$row[csf('gsm_weight')].'*'.$row[csf('machine_dia')].'*'.$row[csf('width_dia_type')].'*'.$color_arr[$row[csf('color_id')]].'*'.$row[csf('color_id')].'*'.$row[csf('machine_gg')].'*'.$row[csf('stitch_length')].'*'.$row[csf('machine_dia')].'*'.$row[csf('machine_id')].'*'.$row[csf('program_qnty')].'*'.$row[csf('job_no')].'*'.$row[csf('subcon_order_id')].'*'.$row[csf('location_id')].'*'.$row[csf('determination_id')].'*'.$row[csf('fabric_desc')].'*'.$row[csf('main_process_id')].'*'.$row[csf('po_id')].'*'.$row[csf('party_id')].'*'.$yarnDataArr[$row[csf('dtls_id')]]['yarn_lot'].'*'.$yarnDataArr[$row[csf('dtls_id')]]['count_id'].'*'.$row[csf('order_location_id')].'*'.$machine_floor_arr[$row[csf('machine_id')]].'*'.$shift_name.'*'.$yarnDataArr[$row[csf('dtls_id')]]['brand'];
			}
		}
	}
	echo	$info;
	exit();
}

if($action == "auto_populate_from_side_list_view")
{
	$data=explode('_',$data);
	//$order_id=$data[0];
	//$process_id=$data[1];
	//$productionBasis=$data[2];

	/*
	|--------------------------------------------------------------------------
	| subcon_production_dtls
	|--------------------------------------------------------------------------
	|
	*/
	if($data[2] == 1)
	{
		$productionBasisCondition = " AND m.production_basis = 1 ";
	}
	else
	{
		$productionBasisCondition = " AND m.production_basis = 2 ";
	}

	$production_qty_array=array();

	if($data[2] == 2)
	{
		$prod_sql="SELECT d.cons_comp_id, d.dia_width_type, d.gsm, d.dia_width, d.color_id, SUM(d.product_qnty) AS product_qnty FROM subcon_production_mst m, subcon_production_dtls d WHERE m.id = d.mst_id AND d.order_id='".$data[0]."' and m.program_no='".$data[3]."' AND d.product_type=2 AND d.status_active=1 AND d.is_deleted=0 ".$productionBasisCondition." GROUP BY d.cons_comp_id, d.dia_width_type, d.gsm, d.dia_width, d.color_id";
	}
	else
	{

		$prod_sql="SELECT d.cons_comp_id, d.dia_width_type, d.gsm, d.dia_width, d.color_id, SUM(d.product_qnty) AS product_qnty FROM subcon_production_mst m, subcon_production_dtls d WHERE m.id = d.mst_id AND d.order_id='".$data[0]."' AND d.product_type=2 AND d.status_active=1 AND d.is_deleted=0 ".$productionBasisCondition." GROUP BY d.cons_comp_id, d.dia_width_type, d.gsm, d.dia_width, d.color_id";
	}
	//echo $prod_sql;
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('cons_comp_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]] += $row[csf('product_qnty')];
	}
	//echo "<pre>";
	//print_r($production_qty_array);

	/*
	|--------------------------------------------------------------------------
	| Fabric details
	|--------------------------------------------------------------------------
	|
	*/

	if($data[2] == 1)
	{
		$sql = "SELECT item_id, dia_width_type, gsm, grey_dia, finish_dia, color_id, SUM(qnty) AS qnty FROM subcon_ord_breakdown WHERE order_id='".$data[0]."' GROUP BY item_id, dia_width_type, gsm, grey_dia, finish_dia, color_id";
	}
	else
	{
		$programCondition = '';
		if($data[3] != '')
		{
			$programCondition = " AND dtls_id = ".$data['3']." ";
		}
		$sql = "SELECT determination_id AS item_id, width_dia_type AS dia_width_type, gsm_weight AS gsm, dia AS grey_dia, color_id, SUM(program_qnty) AS qnty FROM subcon_planning_plan_dtls WHERE po_id = '".$data[0]."' ".$programCondition." GROUP BY determination_id, width_dia_type, gsm_weight, dia, color_id";
	}

	//$sql = "SELECT item_id, dia_width_type, gsm, grey_dia, finish_dia, color_id, SUM(qnty) AS qnty FROM subcon_ord_breakdown WHERE order_id='".$data[0]."' GROUP BY item_id, dia_width_type, gsm, grey_dia, finish_dia, color_id";
	//echo $sql;

	/*
	|--------------------------------------------------------------------------
	| query result checking for
	| $sql
	|--------------------------------------------------------------------------
	|
	*/
	$data_array=sql_select($sql);
	if(empty($data_array))
	{
		echo get_empty_data_msg();
		die;
	}

	$color_arr = get_color_array();

	/*
	|--------------------------------------------------------------------------
	| Fabric construction, consumption and gsm
	| array preparing here
	|--------------------------------------------------------------------------
	|
	*/
	$itemIdArr = array();
	foreach($data_array as $row)
    {
		$itemIdArr[$row[csf('item_id')]] = $row[csf('item_id')];
	}

	$item_arr = array();
	//$gsm_arr = array();
	$sqlCharge = "SELECT id, const_comp, gsm FROM lib_subcon_charge WHERE id IN(".implode(',', $itemIdArr).")";
	$resultCharge = sql_select($sqlCharge);
	foreach($resultCharge as $row)
    {
		$item_arr[$row[csf('id')]] = $row[csf('const_comp')];
		//$gsm_arr[$row[csf('id')]] = $row[csf('gsm')];
	}

    foreach($data_array as $row)
    {
		if($data[1]==2 || $data[1]==3 || $data[1]==4 || $data[1]==6 || $data[1]==7)
		{
			$item_name=$item_arr[$row[csf('item_id')]];
		}
		else
		{
			$item_name=$garments_item[$row[csf('item_id')]];
		}

		$color_name = $color_arr[$row[csf('color_id')]];

		//echo $row[csf('item_id')]."=".$row[csf('dia_width_type')]."=".$row[csf('gsm')]."=".$row[csf('grey_dia')]."=".$row[csf('color_id')]."<br>";
		$orderQty = $row[csf('qnty')];
		$productQty = $production_qty_array[$row[csf('item_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('grey_dia')]][$row[csf('color_id')]];
		$balanceQty = $orderQty - $productQty;
		$hdn = $row[csf('item_id')].$row[csf('dia_width_type')].$row[csf('gsm')].$row[csf('grey_dia')].$row[csf('color_id')];
		echo $row[csf('item_id')]."**".$item_name."**".$row[csf('gsm')]."**".$color_name."**".$row[csf('dia_width_type')]."**".$row[csf('grey_dia')]."**".$row[csf('color_id')]."**".$productQty."**".$orderQty."**".$balanceQty;
    }

	exit();
}


if ($action=="load_weight_machine_data")
{

	//$api_data=sql_select("select norsel_weight_api from lib_machine_name where id=$machine_id  and status_active=1 and is_deleted=0");
	$api_data=sql_select("select norsel_weight_api from user_passwd where id=$userId and status_active=1 ");

	// $url = "http://".$api_data[0][csf("norsel_weight_api")];
	$url = "http://".str_replace("http://",'',$api_data[0][csf("norsel_weight_api")]);

	// echo $url;die;

	//$api=$_SESSION["user_machine_api"];
	//$url = "$api";
	//$url = "http://192.168.10.233";


	$curl_handle=curl_init();
	curl_setopt($curl_handle, CURLOPT_URL,"$url");
	curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_handle, CURLOPT_HEADER, false);
	$postoffice_data = curl_exec($curl_handle);
	curl_close($curl_handle);
	$postoffice_data = json_decode($postoffice_data);
	echo json_encode($postoffice_data);
	exit;
}
if ($action =="load_operator_name") {

	//$operator_name= return_field_value("first_name","lib_employee","id_card_no ='$data' and designation_id in (1113, 1115) and is_deleted=0 and status_active=1");
	$dataArr = explode("_",$data);
	$id_card_no = $dataArr[0];
	$machine_id = $dataArr[1];

	$machine_data = sql_select("select pipe_weight as PIPE_WEIGHT from lib_machine_name where id='" . $machine_id. "'");
	$operator_sql = sql_select("SELECT id, first_name from lib_employee where id_card_no ='$id_card_no' and designation_id in (1113) and is_deleted=0 and status_active=1");

	if(!empty($operator_sql))
	{
		echo $operator_sql[0][csf("id")]."**".$operator_sql[0][csf("first_name")]."**".$machine_data[0]['PIPE_WEIGHT'];
	}else{
		echo "0";
	}
	exit();
}
if ($action =="po_popup_auto")
{
	$data=explode('_',$data);
	$cbo_company_id=$data[0];
	$all_po_id=$data[1];
	$roll_maintained=$data[2];
	$barcode_generation=$data[3];
	$txt_product_qnty=$data[4];
	$cbo_process=$data[5];
	$txt_gsm=$data[6];
	$txt_width=$data[7];
	$fabric_desc_id=$data[8];
	$$txt_deleted_id=$data[9];
	$txt_order_no=$data[10];
	$hdnProductQty=$data[11];
	$hdnOrderQty=$data[12];
	$hdnTotalProductQty=$data[13];
	$hdnBalanceQty=$data[14];
	$hdnColorId=$data[15];
	$api_weight=$data[16];


	$po_sql= "SELECT b.id AS ord_id, a.subcon_job, b.order_no, b.delivery_date, b.order_quantity FROM subcon_ord_mst a, subcon_ord_dtls b WHERE a.subcon_job = b.job_no_mst AND a.status_active = 1 AND b.status_active = 1 AND b.id = ".$all_po_id." AND a.company_id = ".$cbo_company_id." ORDER BY a.id DESC";
	//echo $po_sql;
	$po_result=sql_select($po_sql);
	foreach($po_result as $row)
	{
		//$po_qty=$row[csf('order_quantity')];
		$po_ship_date=$row[csf('delivery_date')];
		$order_no=$row[csf('order_no')];
		$po_id=$row[csf('ord_id')];
	}
	//$po_qty=$hdnOrderQty;

	$roll_id=0;
	$roll_not_delete_id = 0;
	$orginal_val = 1;


	if ($api_weight> 0) {
		$txtrejectqty="";
		$txtrollno="";
		$txtbarcode="";
		$txtRollTableId=0;
		$txtRollId=0;
		$save_string = $api_weight."_".$txtrejectqty."_".$all_po_id."_".$txtrollno."_".$txtbarcode."_".$txtRollTableId."_".$txtRollId;

        $tot_qc_qnty = $tot_qc_qnty*1 + $api_weight * 1;
		$tot_reject_qnty = $tot_reject_qnty * 1 + $txtrejectqty * 1;
		$no_of_roll = $no_of_roll * 1 + 1;
		$anotherDatas=$tot_qc_qnty."_".$tot_reject_qnty."_".$no_of_roll;

		echo $save_string."##".$anotherDatas;

    }
}

if ($action == "auto_create_and_print_pdf_file")
{
	$data = explode("***", $data);

	//$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	//$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');

	$machine_library = sql_select("select id, machine_no, brand, norsel_printer_api, norsel_printer from lib_machine_name");
	foreach ($machine_library as $val)
	{
		$machine_no_arr[$val[csf("id")]] = $val[csf("machine_no")];
		$machine_brand_arr[$val[csf("id")]] = $val[csf("brand")];
		//$machine_norsel_data[$val[csf("id")]]["norsel_printer_api"] = $val[csf("norsel_printer_api")];
		//$machine_norsel_data[$val[csf("id")]]["norsel_printer"] = $val[csf("norsel_printer")];
	}

	$user_api_data = sql_select("select norsel_printer_api, norsel_printer from user_passwd where id=$userId and status_active=1 ");
	$norsel_printer_api = $user_api_data[0][csf("norsel_printer_api")];
	$norsel_printer = $user_api_data[0][csf("norsel_printer")];

	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');




	$sql="SELECT a.id, a.product_no, a.company_id, a.location_id, a.party_id, a.product_date, a.prod_chalan_no,a.knitting_source, a.knitting_company, a.knit_location_id, a.production_basis, a.program_no,b.gsm, b.dia_width,b.color_id, b.dia_width_type,b.machine_dia, b.yarn_lot,b.yrn_count_id,b.stitch_len,b.machine_gg, c.job_no_mst, a.insert_date, b.shift, b.operator_name, b.machine_id, b.fabric_description, b.brand, b.yarn_lot,c.cust_style_ref, c.cust_buyer
	FROM subcon_production_mst a,subcon_production_dtls b,subcon_ord_dtls c WHERE a.id=$data[0] and b.id=$data[1] and a.id=b.mst_id and b.order_id = c.id ";


	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$job_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$machine_dia='';
	$finish_dia = '';
	$shift_name_id = '';
	$operatorName = '';
	$machine_id = '';
	$machicompne_id = '';
	$cust_style_ref = '';
	$cust_buyer = '';
	$party_id = '';



	foreach ($result as $row) {
		// if ($row[csf('knitting_source')] == 1) {
		// 	$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		// } else if ($row[csf('knitting_source')] == 3) {
		// 	$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		// }
		$job_no=$row[csf('job_no_mst')];

		$prod_date = date("d-m-Y", strtotime($row[csf('product_date')]));
		$prod_time = date("H:i:a", strtotime($row[csf('insert_date')]));
		$grey_dia=$row[csf('dia_width')];
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('dia_width')];
		$shift_name_id = $row[csf('shift')];
		$operatorName = $operator_name_arr[$row[csf('operator_name')]];
		$machine_id = $row[csf('machine_id')];
		$party_id = $row[csf('party_id')];
		$cust_style_ref = $row[csf('cust_style_ref')];
		$cust_buyer = $row[csf('cust_buyer')];
		$comp = $row[csf('fabric_description')];
		$program_no=$row[csf('program_no')];
		$machine_dia=$row[csf('machine_dia')];
		$machine_gauge=$row[csf('machine_gg')];
		$tube_type =$fabric_typee[$row[csf('dia_width_type')]];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_len')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $row[csf('brand')];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yrn_count_id')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}
		$machine_dia_gauge = $machine_dia . "X" . $machine_gauge;

		$company_name = return_field_value("company_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	$po_array = array();
	$booking_no_prefix = '';


	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.order_no 	from subcon_pro_roll_details a, subcon_ord_dtls b where a.po_breakdown_id=b.id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 and a.dtls_id in($data[1]) order by a.id";
	//echo $query;
	$res = sql_select($query);

	foreach ($res as $row)
	{
		$order_no = $row[csf('order_no')];
		/*$response = array(
			"printer_id" => $norsel_printer,
			"company_name"=>$company_name,
			"program_no"=>$program_no ,
			"prod_date"=>$prod_date ,
			"prod_time"=>$prod_time ,
			"shift_name"=>$shift_name[$shift_name_id],
			"operatorName"=>$operatorName,
			"order_no"=>$order_no,
			"buyer_name"=>$buyer_arr[$party_id],
			"cust_style_ref"=>$cust_style_ref,
			"machine_id"=>$machine_id,
			"machine_dia"=>$machine_dia,
			"machine_gauge"=>$machine_gauge,
			"finish_dia"=>trim($finish_dia),
			"stitch_length"=>trim($stitch_length),
			"gsm"=>$gsm,
			"color"=>$color,
			"yarn_count"=>$yarn_count,
			"brand"=>$brand,
			"yarn_type_cond"=>$yarn_type_cond,
			"yarn_lot"=>$yarn_lot,
			"yarn_type_cond"=>$yarn_type_cond,
			"yarn_lot"=>$yarn_lot,
			"roll_no"=>$row[csf('roll_no')],
			"qnty"=>$row[csf('qnty')],
			"barcode"=>$row[csf("barcode_no")]
    	);*/
    	$response = array(
			//"1" => $norsel_printer,
			"1"=>$company_name,
			"2"=>$program_no ,
			"3"=>$prod_date ,
			"4"=>$prod_time ,
			"5"=>$shift_name[$shift_name_id],
			"6"=>$operatorName,
			"7"=>$order_no,
			"8"=>$buyer_arr[$party_id],
			"9"=>$cust_style_ref,
			"10"=>$machine_no_arr[$machine_id],
			"11"=>$machine_dia_gauge,
			"12"=>trim($finish_dia),
			"13"=>trim($stitch_length),
			"14"=>trim($comp),
			"15"=>$gsm,
			"16"=>$color,
			"17"=>$yarn_count,
			"18"=>$brand,
			"19"=>$yarn_type_cond,
			"20"=>$yarn_lot,
			"21"=>$row[csf('roll_no')],
			"22"=>$row[csf('qnty')],
			"23"=>$row[csf("barcode_no")],
			"24"=>$row[csf("barcode_no")],//Barcode image
			"25"=>$cust_buyer
    	);
	}

	$reference_array = array(
		1=>'company_full_name',
		2=>"program_no",
		3=>"prod_date",
		4=>"prod_time",//new
		5=>"shiftName",
		6=>"operator_name",
		7=>"po_no",//order_no
		8=>"buyer_name",
		9=>"style_ref",//$cust_style_ref
		10=>"machine_name",
		11=>"machine_dia_gauge",
		12=>"finish_dia",
		13=>"stitch_length",
		14=>"comp",
		15=>"gsm",
		16=>"color",
		17=>"yarn_count",
		18=>"brand",
		19=>"yarn_type",//$yarn_type_cond
		20=>"yarn_lot",
		21=>"roll_no",
		22=>"qnty",
		23=>"Barcode",
		24=>"barcode_no",//Barcode image
		25=>"cust_buyer"
	);

	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');

	ob_start();

	$sqlResult = sql_select("SELECT a.HEIGHT,a.WIDTH,a.BOTTOM_PADDING,a.LEFT_PADDING,a.RIGHT_PADDING,a.TOP_PADDING, a.LINE_SPACE,a.ORIENTATION, a.FONT_COLOR,a.LINE_BREAK,a.FONT,b.FIELD_NAME, b.FIELD_ID, b.SERIAL_NUMBER, b.FONT_SIZE, b.FONT_WEIGHT from pdf_formate_mst a, pdf_formate_details b where a.id=b.mst_id and a.entry_form=159 and a.id=(select max(id) from pdf_formate_mst where entry_form=159)  order by b.SERIAL_NUMBER asc");

	$mb = ($sqlResult[0]['BOTTOM_PADDING']) ? $sqlResult[0]['BOTTOM_PADDING'] : "1";
	$ml = ($sqlResult[0]['LEFT_PADDING']) ? $sqlResult[0]['LEFT_PADDING'] : "2";
	$mr = ($sqlResult[0]['RIGHT_PADDING']) ? $sqlResult[0]['RIGHT_PADDING'] : "1";
	$mt = ($sqlResult[0]['TOP_PADDING']) ? $sqlResult[0]['TOP_PADDING'] : "1";
	$width = ($sqlResult[0]['WIDTH']) ? $sqlResult[0]['WIDTH'] : "72";
	$height = ($sqlResult[0]['HEIGHT']) ? $sqlResult[0]['HEIGHT'] : "60";
	$ls = ($sqlResult[0]['LINE_SPACE']) ? $sqlResult[0]['LINE_SPACE'] : "10";
	$orientation = ($sqlResult[0]['ORIENTATION']) ? $sqlResult[0]['ORIENTATION'] : "P";
	$line_break = ($sqlResult[0]['LINE_BREAK']) ? $sqlResult[0]['LINE_BREAK'] :31;
	$font = ($sqlResult[0]['FONT']) ? $sqlResult[0]['FONT'] :'Arial';


	$pdf=new PDF_Code128($orientation,'mm', array($width,$height));
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin($mr);

	$line_space = $mt;
	foreach($sqlResult as $row)
	{

		$field_id_arr = explode(",", $row['FIELD_ID']);
		$field_name_arr = explode(",", $row['FIELD_NAME']);
		$line_data_arr = array();
		$barcode = false;
		foreach ($field_id_arr as  $j=>$val)
		{
			if($val == 24){$barcode = $response[$val];}
			else{$line_data_arr[]= $field_name_arr[$j] . $response[$val];}
		}

		if($barcode == false){
			$line_string_array = str_split(implode(',',$line_data_arr),$line_break);
			foreach ($line_string_array as  $strval)
			{
				$pdf->SetXY($ml, $line_space);
				$pdf->SetFont($font,$row['FONT_WEIGHT'],$row['FONT_SIZE']);
				$pdf->Write($ml, $strval);
				$line_space+=$ls;
			}
		}
		else{
		  $pdf->SetXY($ml, $line_space);
		  $pdf->Code128($ml+2,$line_space,$barcode,50,8);
		  $line_space+=6;
		  $line_space+=$ls;
		}
	}

	$REAL_FILE_NAME = 'knitting_barcode_'.$userId.'.pdf';
	$pdf->Output( $REAL_FILE_NAME, 'F');
	echo 'requires/'.$REAL_FILE_NAME;

	exit();
}
