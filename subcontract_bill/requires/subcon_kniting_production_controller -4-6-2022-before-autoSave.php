<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');

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
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";

	if($company_id==0 && $location_id==0)
	{
		echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "-- Select Floor --", 0, "",0 );
	}
	else
	{
		echo create_drop_down( "cbo_floor_id", 150, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "load_machine();","" );
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
	if($cbo_location_name>0) $location_cond="location_id=$cbo_location_name";else $location_cond="";
	 $sql = "select id, first_name from lib_employee where  company_id=$cbo_knitting_company  and status_active=1 and is_deleted=0 $location_cond order by first_name";//$cbo_company_id

	echo create_list_view("tbl_list_search", "Operator Name", "150", "270", "160", 0, $sql, "js_set_value", "id,first_name", "", 1, "0", $arr, "first_name", "", 'setFilterGrid("tbl_list_search",-1);', '0', '', 0);
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_string_search_type').value, 'kniting_production_id_search_list_view', 'search_div', 'subcon_kniting_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
		$sql= "select a.id, a.product_no, a.prefix_no_num, TO_CHAR(a.insert_date,'YYYY') as year, a.party_id, a.product_date, a.prod_chalan_no, a.yrn_issu_chalan_no, a.production_basis, listagg((cast(b.order_id as varchar2(4000))),',') within group (order by b.order_id) as order_id, sum(product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.entry_form=159 and a.id=b.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name $buyer_cond $production_date_cond $issue_cond group by a.id, a.product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no, a.yrn_issu_chalan_no, a.production_basis order by a.id DESC";
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
	/*
	if($expData[1] == 1)
	{
		$sql = "
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
		";
	}
	else
	{
		$sql = "
			SELECT 
                spm.production_basis,
				spd.id, spd.mst_id, spd.process, spd.fabric_description, spd.gsm, spd.dia_width, spd.no_of_roll, spd.product_qnty, spd.yarn_lot, spd.yrn_count_id, spd.machine_id,
				sod.order_no
            FROM 
                subcon_production_mst spm
                INNER JOIN subcon_production_dtls spd ON spm.id=spd.mst_id 
                INNER JOIN subcon_planning_plan_dtls sppd ON spd.order_id=sppd.dtls_id 
                INNER JOIN subcon_ord_dtls sod ON sppd.po_id = sod.id 
            WHERE 
                spm.id = '".$expData[0]."'
                --AND spm.entry_form = 159 
                --AND spm.production_basis = 2 
                AND spm.status_active = 1
                AND spm.is_deleted = 0
                AND spd.status_active = 1
                AND spd.is_deleted = 0
                AND sppd.status_active = 1
                AND sppd.is_deleted = 0  
		";
	}
	*/
	$sql = "
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
	";	
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
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="917" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="70">Process</th>
            <th width="70">Order</th>
            <th width="150">Cont. Composition </th>
            <th width="60">GSM</th>
            <th width="60">Dia Width</th>
            <th width="50">No of Roll</th>
            <th width="80">Prod Qty</th>
            <th width="80">Y-Lot</th>
            <th width="130">Y-Count</th>
            <th>Mac-No</th>
        </thead>
    </table>
    <div style="width:920px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="list_view">	
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
            <tr bgcolor="<? echo $bgcolor; ?>" valign="middle" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf("id")]."_".$row[csf("production_basis")].'_'.$expData[2]; ?>','load_php_data_to_form_dtls','requires/subcon_kniting_production_controller'); put_data_dtls_part('<? echo $row[csf('mst_id')]; ?>','', 'requires/subcon_kniting_production_controller');" > 
            <!--<tr bgcolor="<? echo $bgcolor; ?>" valign="middle" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf("mst_id")]."_".$row[csf("production_basis")].'_'.$expData[2]; ?>','load_php_data_to_form_dtls','requires/subcon_kniting_production_controller');" >--> 
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="70"><? echo $conversion_cost_head_array[$row[csf("process")]]; ?></td>
                <td width="70" align="center"><? echo $row[csf("order_no")]; ?></td>
                <td width="150"><p><? echo $row[csf("fabric_description")]; ?></p></td>		
                <td width="60" align="center"><? echo $row[csf("gsm")]; ?></td>
                <td width="60" align="center"><p><? echo $row[csf("dia_width")]; ?></p></td>	
                <td width="50" align="right"><? echo $row[csf("no_of_roll")]; ?></td>	
                <td width="80" align="right"><? echo number_format($row[csf("product_qnty")],2,'.',''); ?></td>	
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
	}
	else
	{
		$productionBasisCondition = " AND spm.production_basis = 2 ";
		/*$sql = "
			SELECT 
                spm.production_basis,
				spd.id, spd.process, spd.fabric_description, spd.cons_comp_id, spd.gsm, spd.dia_width, spd.dia_width_type,spd.machine_dia, spd.machine_gg, spd.no_of_roll, spd.product_qnty, spd.reject_qnty, spd.uom_id, spd.yarn_lot, spd.yrn_count_id, spd.floor_id, spd.machine_id, spd.brand, spd.shift, spd.stitch_len, spd.color_range, spd.color_id, spd.remarks, spd.status_active, 
                sppd.dtls_id AS order_tbl_id,
				sm.job_no AS job_no_mst,
				sod.main_process_id, sod.order_no
            FROM 
                subcon_production_mst spm
                INNER JOIN subcon_production_dtls spd ON spm.id=spd.mst_id 
                INNER JOIN subcon_planning_plan_dtls sppd ON spd.order_id=sppd.po_id 
                INNER JOIN subcon_planning_mst sm ON sppd.mst_id=sm.id 
                INNER JOIN subcon_ord_dtls sod ON sm.subcon_order_id=sod.mst_id 
            WHERE 
                spm.id = '".$expData[0]."'
                --AND spm.entry_form = 159 
                --AND spm.production_basis = 2 
                AND spm.status_active = 1
                AND spm.is_deleted = 0
                AND spd.status_active = 1
                AND spd.is_deleted = 0
                AND sppd.status_active = 1
                AND sppd.is_deleted = 0  
		";*/
	}

	$sql = "
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
	";
	 
	//echo $sql; die;

	$nameArray=sql_select($sql);
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('cbo_process').value		 				= '".$row[csf("process")]."';\n";
		echo "document.getElementById('txt_febric_description').value		 	= '".$row[csf("fabric_description")]."';\n"; 
		echo "document.getElementById('hidd_comp_id').value		 				= '".$row[csf("cons_comp_id")]."';\n"; 
		echo "document.getElementById('txt_gsm').value							= '".$row[csf("gsm")]."';\n"; 
		echo "document.getElementById('txt_width').value						= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('cbo_dia_width_type').value				= '".$row[csf("dia_width_type")]."';\n"; 
		echo "document.getElementById('txt_machine_dia').value					= '".$row[csf("machine_dia")]."';\n";
		echo "document.getElementById('txt_machine_gg').value					= '".$row[csf("machine_gg")]."';\n"; 
		$operatorName = return_field_value("first_name", "lib_employee", "id=" . $row[csf('operator_name')]);
		echo "document.getElementById('txt_operator_id').value					= '".$row[csf("operator_name")]."';\n"; 
		echo "document.getElementById('txt_operator_name').value				= '".$operatorName."';\n"; 
		echo "document.getElementById('txt_roll_qnty').value					= '".$row[csf("no_of_roll")]."';\n";  
		echo "document.getElementById('txt_order_no').value		 				= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txtOrderQty').value		 				= '".$row[csf("order_quantity")]."';\n";
		echo "document.getElementById('order_no_id').value		 				= '".$row[csf("order_tbl_id")]."';\n";
		echo "document.getElementById('txt_operator_id').value		 			= '".$row[csf("operator_name")]."';\n";
		echo "document.getElementById('process_id').value		 				= '".$row[csf("main_process_id")]."';\n"; 
		echo "document.getElementById('txt_product_qnty').value		 			= '".$row[csf("product_qnty")]."';\n";
		echo "document.getElementById('hdnProductQty').value		 			= '".$row[csf("product_qnty")]."';\n";
		echo "document.getElementById('txt_reject_qnty').value            		= '".$row[csf("reject_qnty")]."';\n";
		echo "document.getElementById('cbo_uom').value		 					= '".$row[csf("uom_id")]."';\n"; 
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

		$save_string = '';
		if ($roll_maintained == 1)
		{
			$data_roll_array = sql_select("select id,roll_used, po_breakdown_id, qnty,roll_no, barcode_no, reject_qnty from subcon_pro_roll_details where dtls_id='".$expData[0]."' and entry_form=159 and status_active=1 and is_deleted=0 order by id");
			foreach ($data_roll_array as $row_roll)
			{
				if ($row_roll[csf('roll_used')] == 1)
					$roll_id = $row_roll[csf('id')];
				else
					$roll_id = 0;

				if ($save_string == "")
				{
					$save_string = $row_roll[csf("qnty")] . "_" . $row_roll[csf("reject_qnty")]."_" . $row_roll[csf("po_breakdown_id")] . "_" . $row_roll[csf("roll_no")] . "_" . $row_roll[csf("barcode_no")] . "_" . $row_roll[csf("id")]. "_" . $roll_id;
				}
				else
				{
					$save_string .= "," . $row_roll[csf("qnty")] . "_" . $row_roll[csf("reject_qnty")]."_" . $row_roll[csf("po_breakdown_id")] . "_" . $row_roll[csf("roll_no")] . "_" . $row_roll[csf("barcode_no")] . "_" . $row_roll[csf("id")]. "_" . $roll_id;
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
				if($resultsetDelivery[0][csf('delivery_qty')] >= $row[csf("product_qnty")])
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
				if($resultsetBatch[0][csf('batch_qnty')] >= $row[csf("product_qnty")])
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
		echo "show_list_view('".$row[csf("order_tbl_id")]."_".$row[csf("main_process_id")]."_".$row[csf("production_basis")]."_".$expData[2]."', 'show_fabric_desc_listview', 'list_fabric_desc_container', 'requires/subcon_kniting_production_controller', '');\n";	
		echo "document.getElementById('update_id_dtl').value            		= '".$row[csf("id")]."';\n";
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_programNo').value, 'actn_plan_list_view', 'search_div', 'subcon_kniting_production_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" />
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
			foreach ($nameArray as $row)
			{
				if(empty($productionData[$row[csf('dtls_id')]]))
				{
					if($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					
					$info = $row[csf('dtls_id')].'*'.$row[csf('knitting_source')].'*'.$row[csf('knitting_party')].'*'.$row[csf('order_no')].'*'.$row[csf('color_range')].'*1*'.$row[csf('gsm_weight')].'*'.$row[csf('machine_dia')].'*'.$row[csf('width_dia_type')].'*'.$color_arr[$row[csf('color_id')]].'*'.$row[csf('color_id')].'*'.$row[csf('machine_gg')].'*'.$row[csf('stitch_length')].'*'.$row[csf('machine_dia')].'*'.$row[csf('machine_id')].'*'.$row[csf('program_qnty')].'*'.$row[csf('job_no')].'*'.$row[csf('subcon_order_id')].'*'.$row[csf('location_id')].'*'.$row[csf('determination_id')].'*'.$row[csf('fabric_desc')].'*'.$row[csf('main_process_id')].'*'.$row[csf('po_id')].'*'.$row[csf('party_id')];

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
				else
				{
					if($productionData[$row[csf('dtls_id')]]['balanceQty'] > 0)
					{
						if($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						
						$info = $row[csf('dtls_id')].'*'.$row[csf('knitting_source')].'*'.$row[csf('knitting_party')].'*'.$row[csf('order_no')].'*'.$row[csf('color_range')].'*1*'.$row[csf('gsm_weight')].'*'.$row[csf('machine_dia')].'*'.$row[csf('width_dia_type')].'*'.$color_arr[$row[csf('color_id')]].'*'.$row[csf('color_id')].'*'.$row[csf('machine_gg')].'*'.$row[csf('stitch_length')].'*'.$row[csf('machine_dia')].'*'.$row[csf('machine_id')].'*'.$row[csf('program_qnty')].'*'.$row[csf('job_no')].'*'.$row[csf('subcon_order_id')].'*'.$row[csf('location_id')].'*'.$row[csf('determination_id')].'*'.$row[csf('fabric_desc')].'*'.$row[csf('main_process_id')].'*'.$row[csf('po_id')].'*'.$row[csf('party_id')];
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
	$prod_sql="SELECT d.cons_comp_id, d.dia_width_type, d.gsm, d.dia_width, d.color_id, SUM(d.product_qnty) AS product_qnty FROM subcon_production_mst m, subcon_production_dtls d WHERE m.id = d.mst_id AND d.order_id='".$data[0]."' AND d.product_type=2 AND d.status_active=1 AND d.is_deleted=0 ".$productionBasisCondition." GROUP BY d.cons_comp_id, d.dia_width_type, d.gsm, d.dia_width, d.color_id";
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
	
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="450" style="margin-top:10px;">
        <thead>
            <th width="15">SL</th>
            <th>Fabric Description</th>
            <th width="100">Color</th>
            <th width="60">Order Qty</th>
            <th width="40">Prod. Qty</th>
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
				
				//echo $row[csf('item_id')]."=".$row[csf('dia_width_type')]."=".$row[csf('gsm')]."=".$row[csf('grey_dia')]."=".$row[csf('color_id')]."<br>";
				$orderQty = $row[csf('qnty')];
				$productQty = $production_qty_array[$row[csf('item_id')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('grey_dia')]][$row[csf('color_id')]];
				$balanceQty = $orderQty - $productQty;
				$hdn = $row[csf('item_id')].$row[csf('dia_width_type')].$row[csf('gsm')].$row[csf('grey_dia')].$row[csf('color_id')];
             	?>
                <tr bgcolor="<? echo $bgcolor; ?>" valign="middle" onClick='set_form_data("<? echo $row[csf('item_id')]."**".$item_name."**".$row[csf('gsm')]."**".$color_name."**".$row[csf('dia_width_type')]."**".$row[csf('grey_dia')]."**".$row[csf('color_id')]."**".$productQty."**".$orderQty."**".$balanceQty; ?>"); func_selected_row("<? echo $i; ?>");' style="cursor:pointer" >
                    <td align="center"><? echo $i; ?></td>
                    <td><div style="word-break:break-all"><? echo $item_name.', '.$fabric_typee[$row[csf('dia_width_type')]].', '.$row[csf('gsm')].', '.$row[csf('grey_dia')].', '.$row[csf('finish_dia')]; ?></div></td>
                    <td><div style="word-break:break-all"><? echo $color_name; ?></div></td>
                    <td align="right">
					<? echo number_format($orderQty); ?>
                    <input type="hidden" name="hdnOrderQty[]" id="hdnOrderQty_<? echo $i; ?>" class="text_boxes" value="<? echo $orderQty; ?>" readonly style="width:50px;" />
                    <input type="hidden" name="hddnOrderQty[]" id="hddnOrderQty_<? echo $hdn; ?>" class="text_boxes" value="<? echo $orderQty; ?>" readonly style="width:50px;" />
                    </td>
                    <td align="right">
					<? echo number_format($productQty); ?>
                    <input type="hidden" name="hdnTotalProductQty[]" id="hdnTotalProductQty_<? echo $i; ?>" class="text_boxes" value="<? echo $productQty; ?>" readonly style="width:50px;" />
                    <input type="hidden" name="hddnTotalProductQty[]" id="hddnTotalProductQty_<? echo $hdn; ?>" class="text_boxes" value="<? echo $productQty; ?>" readonly style="width:50px;" />
                    </td>
                    <td align="right">
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
				check_table_status( $_SESSION['menu_id'],0);				
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
		$field_array2="id, mst_id, job_no, order_id, product_type, process, fabric_description, cons_comp_id, gsm, dia_width, dia_width_type, machine_dia, machine_gg, no_of_roll, product_qnty, reject_qnty, uom_id, yarn_lot, yrn_count_id, brand, shift, floor_id, machine_id, stitch_len, color_range, color_id, remarks,operator_name, inserted_by, insert_date";
		$data_array2="(".$id1.",".$id.",".$txt_job_no.",".$order_no_id.",'".$process_knitting."',".$cbo_process.",".$febric_description.",".$hidd_comp_id.",".$txt_gsm.",".$txt_width.",".$cbo_dia_width_type.",".$txt_machine_dia.",".$txt_machine_gg.",".$txt_roll_qnty.",".$txt_product_qnty.",".$txt_reject_qnty.",".$cbo_uom.",".$txt_yarn_lot.",".$cbo_yarn_count.",".$txt_brand.",".$cbo_shift_id.",".$cbo_floor_id.",".$cbo_machine_name.",".$txt_stitch_len.",".$cbo_color_range.",'".$color_name_id."',".$text_new_remarks.",".$txt_operator_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 

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

		$field_array_roll = "id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, reject_qnty, roll_no, booking_without_order, inserted_by, insert_date";
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
			
			$data_array_roll .= "$add_comma(" . $id_roll . "," . $barcode_year . "," . $barcode_suffix_no . "," . $barcode_no . "," . $id . "," . $id1 . "," . $order_id . ",159,'" . $order_qnty_roll_wise . "','" . $order_qnty_roll_wise . "','" . $roll_reject_qty . "','" . $roll_no . "','" .$booking_without_order. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
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
				check_table_status($_SESSION['menu_id'], 0);
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
			$rID3 = sql_insert("subcon_pro_roll_details", $field_array_roll, $data_array_roll, 0);
			if ($flag == 1)
			{
				if ($rID3)
					$flag = 1;
				else
					$flag = 0;
			}
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
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
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
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
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

		$field_array2="job_no*order_id*process*fabric_description*cons_comp_id*gsm*dia_width*dia_width_type*machine_dia*machine_gg*no_of_roll*product_qnty*reject_qnty*uom_id*yarn_lot*yrn_count_id*brand*shift*floor_id*machine_id*stitch_len*color_range*color_id*remarks*operator_name*updated_by*update_date";
		$febric_description=str_replace("(",'[',str_replace(")",']',$txt_febric_description));
		$data_array2="".$txt_job_no."*".$order_no_id."*".$cbo_process."*".$febric_description."*".$hidd_comp_id."*".$txt_gsm."*".$txt_width."*".$cbo_dia_width_type."*".$txt_machine_dia."*".$txt_machine_gg."*".$txt_roll_qnty."*".$txt_product_qnty."*".$txt_reject_qnty."*".$cbo_uom."*".$txt_yarn_lot."*".$cbo_yarn_count."*".$txt_brand."*".$cbo_shift_id."*".$cbo_floor_id."*".$cbo_machine_name."*".$txt_stitch_len."*".$cbo_color_range."*'".$color_name_id."'*".$text_new_remarks."*".$txt_operator_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
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
		$field_array_roll = "id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, reject_qnty, roll_no, booking_without_order, inserted_by, insert_date";
		$field_array_roll_update = "po_breakdown_id*qnty*qc_pass_qnty*reject_qnty*roll_no*updated_by*update_date";

		$save_string = explode(",", str_replace("'", "", $save_data));
		$po_array = array();
		$po_reject_qty_array = array();
		$not_delete_roll_table_id = '';
		$all_barcode_no = "";
		for ($i = 0; $i < count($save_string); $i++)
		{
			$order_dtls = explode("_", $save_string[$i]);
			$order_qnty_roll_wise = $order_dtls[0];
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
					
				$data_array_roll .= "$add_comma(" . $id_roll . "," . $barcode_year . "," . $barcode_suffix_no . "," . $barcode_no . "," . $update_id . "," . $update_id_dtl . "," . $order_id . ",159,'" . $order_qnty_roll_wise . "','" . $order_qnty_roll_wise . "','" . $roll_reject_qty . "','" . $roll_no . "','" .$booking_without_order. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$all_barcode_no .= str_replace("'", "", $barcode_no) . ",";
				$id_roll = $id_roll + 1;
				$barcode_suffix_no = $barcode_suffix_no + 1;
				$barcode_no = $barcode_year . "02" . str_pad($barcode_suffix_no, 7, "0", STR_PAD_LEFT);
			}
			else
			{
				$roll_id_arr[] = $roll_id;
				$roll_data_array_update[$roll_id] = explode("*", ($order_id . "*'" . $order_qnty_roll_wise . "'*'" . $order_qnty_roll_wise . "'*'" . $roll_reject_qty . "'*'" . $roll_no . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
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
				check_table_status($_SESSION['menu_id'], 0);
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
	}
	else
	{
		echo "$('#txt_product_qnty').removeAttr('readonly','readonly');\n";
		echo "$('#txt_product_qnty').removeAttr('onClick','openmypage_po();');\n";
		echo "$('#txt_product_qnty').removeAttr('placeholder','Single Click');\n";
		echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";
		echo "$('#txt_reject_qnty').removeAttr('placeholder','Display');\n";
		echo "$('#txt_roll_qnty').removeAttr('placeholder','Display');\n";
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
                    } else {
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

        function calculate_qc_qnty()
		{
            var total_qc_qnty = '';
			var total_reject_qnty = '';
            $("#tbl_list tbody").find('tr').each(function () {
				var txtqcpassqty = $(this).find('input[name="txtqcpassqty[]"]').val();
				var txtrejectqty = $(this).find('input[name="txtrejectqty[]"]').val();
				total_qc_qnty = total_qc_qnty * 1 + txtqcpassqty * 1;
				total_reject_qnty = total_reject_qnty * 1 + txtrejectqty * 1;
            });

            $('#txt_total_qcpass_qnty').val(total_qc_qnty.toFixed(2)); 
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
					alert('Product qty. is larger than order qty.');
					$('#txtqcpassqty_1').val('0');
					return;
				}
			}
			else if(isUpdate == 1)
			{
				if(hdnOrderQty*1 < (hdnTotalProductQty*1+totalQcQty*1))
				{
					alert('Product qty. is larger than order qty.');
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
                             	<td width="120" align="center"><? echo $order_no; ?>
                                     <input type="hidden" name="all_po_id[]" id="all_po_id_<? echo $k; ?>" class="text_boxes" value="<? echo $sub_po_id; ?>">
                                     <input type="hidden" name="txtRollTableId[]"  id="txtRollTableId_<? echo $k; ?>" value="<? echo $roll_id; ?>">
                                     <input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
                                     <input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
                                </td>
                                <td width="100" align="center"><? echo number_format($po_qty,0); ?></td>
                                <td width="100" align="center"><? echo change_date_format($po_ship_date); ?></td>
                                <td>
                                	<input type="text" name="txtqcpassqty[]" id="txtqcpassqty_<? echo $k; ?>" class="text_boxes_numeric" onKeyUp="func_production_qty_check_zs('1'); calculate_qc_qnty();" style="width:100px;" value="<? echo $qc_qty; ?>"/>
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
					$roll_not_delete_id = 0;
					$orginal_val = 1;
					$k = 1;
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
                             	<td width="120" align="center"><? echo $order_no; ?>
                                 <input type="hidden" name="all_po_id[]" id="all_po_id_<? echo $k; ?>" class="text_boxes" value="<? echo $po_id; ?>">
                                 <input type="hidden" name="txtRollTableId[]"  id="txtRollTableId_<? echo $k; ?>" value="<? echo $roll_id; ?>">
                                 <input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_not_delete_id; ?>">
                                 <input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
                                </td>
                                <td width="100" align="center"><? echo number_format($po_qty,0); ?></td>
                                <td width="100" align="center"><? echo change_date_format($po_ship_date); ?></td>
                                <td>
                                    <input type="text" name="txtqcpassqty[]" id="txtqcpassqty_<? echo $k; ?>" onKeyUp="func_production_qty_check_zs('0'); calculate_qc_qnty();" class="text_boxes_numeric" style="width:100px;" />
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
	//echo $query;
	$caption = "PO No.";
	?>
    <div align="center">
		<?
		//$barcode_generation=2;
		if ($barcode_generation == 2)
		{
			?>
            <input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer"
            	class="formbutton" onClick="fnc_send_printer_text()"/>
			<input type="button" id="btn_barcode_extranal_db" name="btn_barcode_extranal_db" value="Send To Ex. DB" class="formbutton" onClick="fnc_barcode_For_extranal_database()"/>
			<input type="button" id="btn_barcode_128" name="btn_barcode_128" value="Barcode 128" class="formbutton" onClick="fnc_barcode_code128(1)"/>
			<input type="button" id="btn_barcode_128" name="btn_barcode_128" value="Barcode 128 v2" class="formbutton" onClick="fnc_barcode_code128(2)"/>
			<?
		}
		else
		{
			?>
            <input type="button" id="btn_barcode_generation" name="btn_barcode_generation" value="Barcode Generation"
                   class="formbutton" onClick="fnc_barcode_generation()"/>
             <input type="button" id="btn_barcode_128" name="btn_barcode_128" value="Barcode 128 v2"
                   class="formbutton" onClick="fnc_barcode_code128()"/>
			<?
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



	$pdf=new PDF_Code128('P','mm',array(80,65));
	$pdf->AddPage();
	$pdf->SetFont('Times','',10);


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

		$pdf->SetXY($i, $j+14);		
		$pdf->Write(0, "S.Con:" . $order_no .", " . $party_name );

		$pdf->SetXY($i, $j+18);
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
?>