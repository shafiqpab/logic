<?
session_start();
include('../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//------------------------------------------------------------------------------------------------------
if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select sewing_production, production_entry from  variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("sewing_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("sewing_production")].");\n";
	}
	
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$data and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo!=1) $prod_reso_allo=0;
	echo "document.getElementById('prod_reso_allo').value=".$prod_reso_allo.";\n";
	
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$working_company and variable_list=33 and page_category_id=29","is_control");
	if($variable_is_control=='') $variable_is_control=0;
	echo "document.getElementById('variable_is_controll').value=".$variable_is_control.";\n";
	
	$variable_qty_source_poly=return_field_value("qty_source_poly","variable_settings_production","company_name=$working_company and variable_list=42","qty_source_poly");
	if($variable_qty_source_poly=='') $variable_qty_source_poly=1;
	echo "document.getElementById('txt_qty_source').value=".$variable_qty_source_poly.";\n";
	
 	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/poly_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/poly_entry_controller', document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_poly_date').value, 'load_drop_down_sewing_output_line', 'poly_line_td' );" );     	 
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 140, "select id,floor_name from lib_prod_floor where status_active=1 and production_process=5 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", '', "load_drop_down( 'requires/poly_entry_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_poly_date').value, 'load_drop_down_sewing_line_floor', 'poly_line_td' );",0 );  
	exit();   	 
}

/*if($action=="load_drop_down_sewing_output_line_for_company")
{
	echo create_drop_down( "cbo_poly_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and company_name='$data' ","id,line_name", 1, "Select Line", $selected, "" );
}
*/
if($action=="load_drop_down_sewing_output_line")
{
	$explode_data = explode("_",$data);
	$location = $explode_data[0];
	$prod_reso_allocation = $explode_data[1];
	$txt_poly_date = $explode_data[2];
	
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_poly_date=="")
		{ 
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 and location_id='$location'");
		}
		else
		{
			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_poly_date,'yyyy-mm-dd')."' and a.location_id='$location' and a.is_deleted=0 and b.is_deleted=0 group by a.id");
			}
			if($db_type==2 || $db_type==1)
			{	
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_poly_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
				
			}
		}

		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		echo create_drop_down( "cbo_poly_line", 110,$line_array,"", 1, "--- Select ---", $selected, "",0,0 );		
	}
	else
	{
		echo create_drop_down( "cbo_poly_line", 110, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and location_name='$location' and location_name!=0 order by line_name","id,line_name", 1, "Select Line", $selected, "" );
	}
}

if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);	
	$prod_reso_allocation = $explode_data[2];
	$txt_poly_date = $explode_data[3];
	$cond="";
	
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_poly_date=="")
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
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_poly_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
			}
			if($db_type==2 || $db_type==1)
			{	
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_poly_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
				
			}
		}

		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			
			$line_array[$row[csf('id')]]=$line;
		}
		echo create_drop_down( "cbo_poly_line", 110,$line_array,"", 1, "--- Select ---", $explode_data[0], "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";
		
		echo create_drop_down( "cbo_poly_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
	}
}

if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
	
		function js_set_value(id,item_id)
		{
			$("#hidden_mst_id").val(id);
			$("#hidden_grmtItem_id").val(item_id);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
            	<tr>
                    <th width="150" align="center">Buyer Name</th>               	 
                    <th width="100" align="center">Job Search</th>
                    <th width="100" align="center">Style Search</th>
                    <th  width="100" align="center">Order No</th>
                    <th width="170">Date Range</th>
                    <th ><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>  
						<? 
							echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",'', "",0 );   	 
                        ?>
                    </td>
                    <td width="100" align="center">				
                        <input type="text" style="width:95px" class="text_boxes"  name="txt_search_job" id="txt_search_job" placeholder="Job Search" />			
                    </td>
                    <td width="100" align="center">				
                        <input type="text" style="width:95px" class="text_boxes"  name="txt_search_style" id="txt_search_style" placeholder="Style Search" />			
                    </td>
                    <td width="100" align="center">				
                        <input type="text" style="width:95px" class="text_boxes"  name="txt_search_order" id="txt_search_order" placeholder="Order Search" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />			
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"> To
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_style').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'poly_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                    </td>
                </tr>
				<tr>
					<td colspan="6" align="center" valign="middle">
						<? echo load_month_buttons(1);  ?>
						<input type="hidden" id="hidden_mst_id">
						<input type="hidden" id="hidden_grmtItem_id">
					</td>
				</tr>
            </tbody>
		</table>    
		</form>
        <div id="search_div"></div>
	</div>
	</body>            
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();		
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$party = $ex_data[0];
	$search_job = $ex_data[1];
	$search_style = $ex_data[2];
	$search_order = $ex_data[3];
	$date_from = $ex_data[4];
	$date_to = $ex_data[5];
	$company = $ex_data[6];
 	$garments_nature = $ex_data[7];
	$search_type= $ex_data[8];

	if($party!=0) $party_cond=" and a.party_id='$party'"; else $party_cond="";
	
	if($search_type==1)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num='$search_job'"; else $search_job_cond="";
		if($search_style!='') $search_style_cond=" and b.cust_style_ref='$search_style'"; else $search_style_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no='$search_order'"; else $search_order_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '%$search_job%'"; else $search_job_cond="";
		if($search_style!='') $search_style_cond=" and b.cust_style_ref like '%$search_style'%"; else $search_style_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '%$search_order%'"; else $search_order_cond="";
	}
	else if($search_type==2)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '$search_job%'"; else $search_job_cond="";
		if($search_style!='') $search_style_cond=" and b.cust_style_ref like '$search_style'%"; else $search_style_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '$search_order%'"; else $search_order_cond="";
	}
	else if($search_type==3)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '%$search_job'"; else $search_job_cond="";
		if($search_style!='') $search_style_cond=" and b.cust_style_ref like '%$search_style'"; else $search_style_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '%$search_order'"; else $search_order_cond="";
	}
	
	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") $delivery_date_cond = "and b.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $delivery_date_cond ="";
	}
	else
	{
		if ($date_from!="" &&  $date_to!="") $delivery_date_cond = "and b.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'"; else $delivery_date_cond ="";
	}
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
	if($db_type==0)
	{
		$year_cond= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	
 	$sql_sew = "select a.subcon_job, a.job_no_prefix_num, $year_cond, a.party_id, b.id, b.order_no, b.main_process_id, b.process_id, b.order_uom, b.cust_buyer, b.cust_style_ref, b.delivery_date, c.item_id, sum(c.qnty) as order_quantity from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id and b.id=c.order_id and a.company_id='$company' $delivery_date_cond $search_job_cond $search_style_cond $search_order_cond group by a.id, b.id, b.order_uom, b.cust_buyer, b.cust_style_ref, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.party_id, b.main_process_id, b.process_id, b.delivery_date, b.order_no,c.item_id order by a.id DESC ";
	
	//$arr=array (4=>$party_arr,6=>$garments_item,8=>$production_process);
	//echo  create_list_view("tbl_po_list", "Delivery Date,Job,Year,Order No,Party,Style,Item,Order Qty,Process", "70,50,40,80,100,80,100,80,80","750","250",0, $sql , "js_set_value", "po_id,item_id,order_quantity,order_no,subcon_job,main_process_id,party_id,cust_style_ref", "", 1, "0,0,0,0,party_id,0,item_id,0,main_process_id", $arr , "delivery_date,job_no_prefix_num,year,order_no,party_id,cust_style_ref,item_id,order_quantity,main_process_id", "requires/poly_entry_controller",'','3,0,0,0,0,0,0,2,0');
	
?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="50">Job </th>
                <th width="50">Year</th>
                <th width="100">Party</th>
                <th width="90">Style No</th>
                <th width="90">PO No</th>
                <th width="90">Process</th>
                <th width="80">PO Qty</th>
                <th width="120">Item</th>
                <th>Delivery Date</th>
            </thead>
        </table>
        <div style="width:820px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="803" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$sql_sew_result=sql_select($sql_sew);
				foreach($sql_sew_result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//echo $row[csf('process_id')];
					$processid=explode(",",$row[csf('process_id')]);
					$sew_array=array(124);
					//$query_arr=array_intersect($knit_array,$processid);
					//print_r ($query_arr); 
					if(array_intersect($sew_array,$processid))
					{
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,<? echo $row[csf('item_id')]; ?>)"> 
                        <td width="30" align="center"><? echo $i; ?></td>	
                        <td width="50" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="100"><p><? echo $party_arr[$row[csf('party_id')]]; ?></p></td>
                        <td width="90"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
                        <td width="90"><p><? echo $row[csf('order_no')]; ?></p></td>
                        <td width="90"><p><? echo $production_process[$row[csf('main_process_id')]]; ?></p></td>
                        <td width="80" align="right"><? echo number_format( $row[csf('order_quantity')],2); ?>&nbsp;</td> 
                        <td width="120" align="center"><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
                        <td align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
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

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$qty_source = $dataArr[2];
	
	if($qty_source==1) $qty_source_cond=2; //Sweing Output
	else if($qty_source==2) $qty_source_cond=3;//Iron Output
	
	if($db_type==0)
	{
		$group_cond= " group by a.id";
	}
	else if($db_type==2)
	{
		$group_cond= " group by a.id, a.delivery_date, a.main_process_id, a.order_no, a.order_quantity, a.cust_style_ref ,b.subcon_job, b.company_id, b.location_id, b.party_id,  c.item_id";
	}
	//echo "select a.id, a.delivery_date, a.main_process_id, a.order_no, a.order_quantity, a.cust_style_ref ,b.subcon_job, b.company_id, b.location_id, b.party_id, c.order_id as order_id, c.item_id as item_id, sum(qnty) as qnty, sum(plan_cut) as plan_cut from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c where b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and a.job_no_mst=b.subcon_job  and a.id=c.order_id and a.id='$po_id' and c.item_id='$item_id' $group_cond";
	$res = "select a.id, a.delivery_date, a.main_process_id, a.order_no, a.order_quantity, a.cust_style_ref ,b.subcon_job, b.company_id, b.location_id, b.party_id, c.item_id as item_id, sum(c.qnty) as qnty, sum(c.plan_cut) as plan_cut from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c where b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and a.job_no_mst=b.subcon_job and a.id=c.order_id and a.id='$po_id' and c.item_id='$item_id' $group_cond"; 
	$sql_res=sql_select($res);
 	foreach($sql_res as $result)
	{
		echo "document.getElementById('txt_order_no').value 			= '".$result[csf("order_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 				= '".$result[csf("subcon_job")]."';\n";
		echo "document.getElementById('hidden_po_break_down_id').value	= '".$result[csf('id')]."';\n";
		echo "document.getElementById('process_id').value				= '".$result[csf('main_process_id')]."';\n";
		echo "document.getElementById('cbo_item_name').value			= '".$result[csf('item_id')]."';\n";
		echo "document.getElementById('txt_order_qty').value 			= '".$result[csf("qnty")]."';\n";
		echo "document.getElementById('cbo_location').value 			= '".$result[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 			= '".$result[csf("party_id")]."';\n";
		echo "document.getElementById('txt_style_no').value 			= '".$result[csf("cust_style_ref")]."';\n";
		//echo "document.getElementById('txt_plancut_qty').value 			= '".$result[csf("plan_cut")]."';\n";
		echo "$('#txt_cutting_qty').attr('placeholder','');\n";
		
		$total_produced = return_field_value("sum(production_qnty)","subcon_gmts_prod_dtls","order_id='$po_id' and gmts_item_id='$item_id' and production_type=5 and is_deleted=0");
		echo "$('#txt_cumul_poly_qty').attr('placeholder','".$total_produced."');\n";
		echo "$('#txt_cumul_poly_qty').val('".$total_produced."');\n";
		$yet_to_produced = $result[csf('qnty')]*1 - $total_produced;
		echo "$('#txt_yet_to_poly').attr('placeholder','".$yet_to_produced."');\n";
		echo "$('#txt_yet_to_poly').val('".$yet_to_produced."');\n";
		echo "document.getElementById('txt_mst_id').value  			= '".$result[csf("id")]."';\n"; 
		echo "set_button_status(0, '".$_SESSION['page_permission']."', '',1);\n";
	}
	exit();		
}

if($action=="color_and_size_level")
{ 
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	//$styleOrOrderWisw = $dataArr[3];
	
	$qty_source=0;
	if($dataArr[3]==1) $qty_source=2; //Sweing Output
	else if($dataArr[3]==2) $qty_source=3;//Iron Output
	
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	//#############################################################################################//
	if( $variableSettings==2 ) // color level
	{
		$sql="SELECT id, item_id, color_id, sum(qnty) as qnty FROM subcon_ord_breakdown WHERE order_id='$po_id' and item_id='$item_id' GROUP BY color_id";
	}
	else if( $variableSettings==3 )//color and size level//
	{
		$sql_prod=sql_select("select b.ord_color_size_id, sum(CASE WHEN b.production_type='$qty_source' then b.prod_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN b.production_type=5 then b.prod_qnty ELSE 0 END) as cur_production_qnty
									from subcon_gmts_prod_dtls a, subcon_gmts_prod_col_sz b where a.status_active=1 and a.id=b.dtls_id and a.order_id='$po_id' and a.gmts_item_id='$item_id' and b.production_type in ($qty_source,5) group by b.ord_color_size_id");
		foreach($sql_prod as $row)
		{				  
			$color_size_qty_array[$row[csf('ord_color_size_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qty_array[$row[csf('ord_color_size_id')]]['rcv']= $row[csf('cur_production_qnty')];
		}
		// print_r($color_size_qty_array);
		 
		$sql="select id, order_id, item_id, size_id, color_id, qnty from subcon_ord_breakdown where order_id='$po_id' and item_id='$item_id' order by color_id, size_id";
	}
	else// by default color and size level
	{
		//$sql="SELECT id, item_id, color_id, size_id, qnty, plan_cut, (SELECT sum(CASE WHEN order_no THEN order_quantity ELSE 0 END) FROM subcon_ord_dtls) as production_qnty FROM subcon_ord_breakdown WHERE order_id='$po_id' GROUP BY color_id, size_id";
		$sql_prod=sql_select("select b.ord_color_size_id, sum(CASE WHEN b.production_type='$qty_source' then b.prod_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN b.production_type=5 then b.prod_qnty ELSE 0 END) as cur_production_qnty
									from subcon_gmts_prod_dtls a, subcon_gmts_prod_col_sz b where a.status_active=1 and a.id=b.dtls_id and a.order_id='$po_id' and a.gmts_item_id='$item_id' and b.production_type in ($qty_source,5) group by b.ord_color_size_id");
		foreach($sql_prod as $row)
		{				  
			$color_size_qty_array[$row[csf('ord_color_size_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qty_array[$row[csf('ord_color_size_id')]]['rcv']= $row[csf('cur_production_qnty')];
		}
		// print_r($color_size_qty_array);
		 
		$sql="select id, order_id, item_id, size_id, color_id, qnty from subcon_ord_breakdown where order_id='$po_id' and item_id='$item_id' order by color_id, size_id";
	}
	//echo $sql;
	$colorResult = sql_select($sql);
	$colorHTML="";
	$colorID='';
	$chkColor = array();
	$i=0;$totalQnty=0;
	$j=0;$k=0;
	foreach($colorResult as $color)
	{
		$i++;
		if( $variableSettings==2 ) // color level
		{ 
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_id")]].'</td><td><input type="text" name="colSize" id="colSize_'.$i.'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("qnty")]-$color[csf("production_qnty")]).'" onblur="fn_colorlevel_total('.$i.')"><input type="hidden" id="txt_colo_size_mst_id_'.$i.'" name="txt_colo_size_mst_id_'.$i.'" value="'.$color[csf("id")].'"></td></tr>';				
			$totalQnty += $color[csf("qnty")]-$color[csf("prod_qnty")];
			$colorID .= $color[csf("color_id")].",";
		}
		else //color and size level
		{
			if( !in_array( $color[csf("color_id")], $chkColor ) )
			{
				$iss_qnty=0;  $rcv_qnty=0;
				$iss_qnty=$color_size_qty_array[$color[csf('id')]]['iss'];
				$rcv_qnty=$color_size_qty_array[$color[csf('id')]]['rcv'];
				$z=1;
				$k++;
				if( $j!=0 ) $colorHTML .= "</table></div></form>";
				$j=0;
				$colorHTML .= '<h3 align="left" id="accordion_h'.$k.'" style="width:230px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$k.'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_id")].'span">+</span>'.$color_library[$color[csf("color_id")]].' : <span  id="total_'.$k.'" ></span></h3>';
				$colorHTML .= '<div id="content_search_panel_'.$k.'" style="display:none" class="accord_close"><table id="table_'.$k.'">';
				$chkColor[] = $color[csf("color_id")];			
			}
			$colorID .= $color[csf("size_id")]."*".$color[csf("color_id")].",";
			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_id")]].'</td><td><input type="text" name="colSize" id="colSize'.$k.'___'.$z.'"  class="text_boxes" style="text-align:right;width:100px;"  placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="sum_qnty('.$k.','.$z.' )" ><input type="hidden" id="txt_colo_size_mst_id'.$k.'___'.$z.'" name="txt_colo_size_mst_id'.$k.'___'.$z.'" value="'.$color[csf("id")].'" ></td></tr>';		//fn_total('.$i.')
		}
		$z++;
		$j++;
	}
	$colorHTML .='<input type="hidden" id="txt_span_count" name="txt_span_count" value="'.$k.'">';
	$colorHTML .='<input type="hidden" id="txt_total_row_count" name="txt_total_row_count" value="'.$i.'">';
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'</tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="show_dtls_listview")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$sewing_production_variable=$dataArr[2];
	
	$qty_source=0;
	if($dataArr[3]==1) $qty_source=2; //Sweing Output
	else if($dataArr[3]==2) $qty_source=3;//Iron Output
	?>	
	<div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80" align="center">Poly Date</th>
                <th width="80" align="center">Rep. Hour</th>
                <th width="80" align="center">Poly Qty</th>
                <th width="80" align="center">Reject Qty</th>                    
                <th width="80" align="center">Alter Qnty</th>
                <th width="120" align="center">Location</th>
                <th width="80" align="center">Floor</th>
                <th width="" align="center">Level</th>                    
            </thead>
		<? 
		if($db_type==0) $hour_field=" TIME_FORMAT(hour, '%H:%i' ) as hour"; 
		else if($db_type==2) $hour_field=" TO_CHAR(hour,'HH24:MI')  as hour"; 
			$i=1;
			 $sql="select id, production_date, production_qnty, reject_qnty, floor_id, alter_qnty, spot_qnty, supervisor, challan_no, remarks, line_id, location_id, entry_break_down_type, $hour_field, minute from  subcon_gmts_prod_dtls where production_type=5 and order_id='$po_id' and gmts_item_id='$item_id' and status_active=1 and is_deleted=0 and entry_break_down_type='$sewing_production_variable' order by id ";
			
			$sqlResult =sql_select($sql);
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_sub_sewing_form_data','requires/poly_entry_controller');" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="80" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                    <td width="80" align="center"><?php echo $selectResult[csf('hour')]; ?> </td> 
                    <td width="80" align="right"><?php echo $selectResult[csf('production_qnty')]; ?>&nbsp;</td>
                    <td width="80" align="right"><?php echo $selectResult[csf('reject_qnty')]; ?>&nbsp;</td>
                    <td width="80" align="right"><?php echo $selectResult[csf('alter_qnty')]; ?>&nbsp;</td>
                        <?php 
                            $location_name= return_field_value("location_name","lib_location","id='".$selectResult[csf('location_id')]."'");
                        ?>
                    <td width="120" align="center"><p><? echo $location_name; ?></p></td>
                        <?php
                            $floor_name = return_field_value( "floor_name","lib_prod_floor","id='".$selectResult[csf('floor_id')]."'");
                        ?>                
                    <td width="80" align="center"><p><?php echo $floor_name; ?></p></td>  
                                        
                    <td width="" align="center"><p><?php echo $production_update_areas[$selectResult[csf('entry_break_down_type')]]; ?></p></td>                    
                </tr>
			<?php
			$i++;
			}
			?>
		</table>
    </div>
	<?
	exit();
}

if($action=="populate_sub_sewing_form_data")
{
	$sqlResult =sql_select("select c.color_id,sum(b.prod_qnty) as prod_qnty from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b,subcon_ord_breakdown c  where a.id=b.dtls_id and a.production_date=b.production_date and a.status_active=1 and a.is_deleted=0 and a.id='$data' and c.id=b.ord_color_size_id group by c.color_id");
 	$prod_qnty_sub=array();
	foreach($sqlResult as $result)
	{
		$prod_qnty_sub[$result[csf("color_id")]]=$result[csf("prod_qnty")];
	}
	
	if($db_type==0) $hour_field=" TIME_FORMAT(a.hour, '%H:%i' ) as hour"; 
	else if($db_type==2) $hour_field=" TO_CHAR(a.hour,'HH24:MI')  as hour"; 
	$sqlResult =sql_select("select c.color_id, c.size_id, a.id, a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.order_id, a.production_date, a.production_qnty, a.reject_qnty, a.alter_qnty, a.spot_qnty, a.supervisor, a.production_type, a.entry_break_down_type, a.table_no, a.challan_no, a.remarks, $hour_field, a.minute, b.production_type, b.gmts_item_id, b.production_date, b.ord_color_size_id, b.prod_qnty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b,subcon_ord_breakdown c where a.id=b.dtls_id and a.production_date=b.production_date and a.status_active=1 and a.is_deleted=0 and a.id='$data' and c.id=b.ord_color_size_id 	
 order by c.color_id");
	$i=0;$k=0;
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$break_size_library=return_library_array( "select id, size_id from  subcon_ord_breakdown",'id','size_id');
	$break_color_library=return_library_array( "select id, color_id from  subcon_ord_breakdown",'id','color_id');
	$break_plan_cut_library=return_library_array( "select id, plan_cut from  subcon_ord_breakdown",'id','plan_cut');
	$prod_qnty= return_field_value("production_qnty","subcon_gmts_prod_dtls","id");
	 
	$color_size_array=array();
  	foreach($sqlResult as $result)
	{
		if($i==0)
		{
			echo "$('#cbo_location').val('".$result[csf('location_id')]."');\n";
			echo "load_drop_down( 'requires/poly_entry_controller', document.getElementById('cbo_location').value, 'load_drop_down_floor', 'floor_td' );\n";			
			echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n"; 
			echo "$('#prod_reso_allo').val('".$result[csf('prod_reso_allo')]."');\n"; 
			echo "$('#txt_poly_date').val('".change_date_format($result[csf('production_date')])."');\n";
			echo "load_drop_down( 'requires/poly_entry_controller', document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_poly_date').value, 'load_drop_down_sewing_line_floor', 'poly_line_td' );\n";
			
			echo "$('#cbo_poly_line').val('".$result[csf('line_id')]."');\n"; 
			echo "$('#txt_sewing_qty').val('".$result[csf('production_qnty')]."');\n";
			echo "$('#txt_reject_qnty').val('".$result[csf('reject_qnty')]."');\n";    	
			echo "$('#txt_alter_qnty').val('".$result[csf('alter_qnty')]."');\n";
			echo "$('#txt_spot_qnty').val('".$result[csf('spot_qnty')]."');\n";
			echo "$('#txt_super_visor').val('".$result[csf('supervisor')]."');\n";
		
			//if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_hours;else $reportTime = 12+str_replace("'","",$txt_hours);
			echo "$('#txt_hours').val('".$result[csf('hour')]."');\n";
			//echo "$('#cbo_time').val('".$time."');\n";	
			echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
			echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
			echo "$('#txt_cumul_poly_qty').val('".$result[csf('total_produced')]."');\n";
			echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
			echo "set_button_status(1, permission, 'fnc_subcon_sewing_output_entry',1);\n";
			
			
		}
	$i++;
		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$variableSettings =$result[csf('entry_break_down_type')];
		if( $variableSettings==1 ) // gross level
		{
			die; 
		}
		else if( $variableSettings==2 ) // Color level
		{
			$colorHTML .='<tr><td>'.$color_library[$break_color_library[$result[csf("ord_color_size_id")]]].'</td><td><input type="text" name="colorSize" id="colSize_'.$i.'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($break_plan_cut_library[$result[csf("plan_cut_qnty")]]-$result[csf("prod_qnty")]).'" title="'.($break_plan_cut_library[$result[csf("plan_cut_qnty")]]-$result[csf("prod_qnty")]).'" value="'.$result[csf("prod_qnty")].'" onblur="fn_colorlevel_total('.$i.')"><input type="hidden" id="txt_colo_size_mst_id_'.$i.'" name="txt_colo_size_mst_id_'.$i.'" value="'.$result[csf("ord_color_size_id")].'"></td></tr>';				
			$totalQnty =$result[csf("production_qnty")];
			$colorID .= $color_library[$break_color_library[$result[csf("ord_color_size_id")]]].",";
		}
		else if( $variableSettings==3 ) // Color and size level
		{	
			if ( !in_array( $result[csf("color_id")],$color_size_array) )
			{	
				$color_size_array[]=$result[csf("color_id")];
				$z=1;
				$k++;
				if( $j!=0 ) $colorHTML .= "</table></div>";
				$j=0;

				$colorHTML .= '<h3 align="left" id="accordion_h'.$k.'" style="width:230px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$k.'\', \'\',1);"> <span id="accordion_h'.$color_library[$break_color_library[$result[csf("ord_color_size_id")]]].'span">+</span>'.$color_library[$break_color_library[$result[csf("ord_color_size_id")]]].' : <span id="total_'.$k.'">'.$prod_qnty_sub[$result[csf("color_id")]].'</span></h3>';
				$colorHTML .= '<div id="content_search_panel_'.$k.'" style="display:none" class="accord_close"><table id="table_'.$k.'">';
				$chkColor[] = $result[csf("color_id")];
			}
			$colorID .= $result[csf("ord_color_size_id")]."*".$result[csf("ord_color_size_id")].",";
			$colorHTML .='<tr><td>'.$size_library[$break_size_library[$result[csf("ord_color_size_id")]]].'</td><td><input type="text" name="colSize" id="colSize'.$k.'___'.$z.'"  class="text_boxes_numeric" style="text-align:right;width:100px;" placeholder="'.($break_plan_cut_library[$result[csf("plan_cut_qnty")]]-$result[csf("prod_qnty")]).'"  onblur="sum_qnty('.$k.','.$z.' )" title="'.($break_plan_cut_library[$result[csf("plan_cut_qnty")]]-$result[csf("prod_qnty")]).'" value="'.$result[csf("prod_qnty")].'"><input type="hidden" id="txt_colo_size_mst_id'.$k.'___'.$z.'" name="txt_colo_size_mst_id'.$k.'___'.$z.'" value="'.$result[csf("ord_color_size_id")].'"></td></tr>';	
			$z++;
		}
		$j++; 
	}
	$colorHTML .='<input type="hidden" id="txt_span_count" name="txt_span_count" value="'.$k.'">';
	$colorHTML .='<input type="hidden" id="txt_total_row_count" name="txt_total_row_count" value="'.$i.'">';
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	if( $variableSettings==3 )echo "$totalFn;\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)//Insert Here=================		
	{
		$con = connect();
		if($db_type==0)	
		{ 
		mysql_query("BEGIN"); 
		}
		$id=return_next_id("id","subcon_gmts_prod_dtls", 1);
		//if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_hours;else $reportTime = 12+str_replace("'","",$txt_hours);

		$field_array="id, company_id, location_id, floor_id, line_id, prod_reso_allo, order_id, gmts_item_id, production_date, production_qnty, reject_qnty, alter_qnty, spot_qnty, hour, supervisor, production_type, entry_break_down_type, challan_no, remarks, is_deleted, status_active, inserted_by, insert_date";
		if($db_type==0)
		{
		$data_array="(".$id.",".$cbo_company_name.",".$cbo_location.",".$cbo_floor.",".$cbo_poly_line.",".$prod_reso_allo.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$txt_poly_date.",".$txt_sewing_qty.",".$txt_reject_qnty.",".$txt_alter_qnty.",".$txt_spot_qnty.",".$txt_hours.",".$txt_super_visor.",5,".$sewing_production_variable.",".$txt_challan.",".$txt_remark.",0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		else
		{
			$txt_hours=str_replace("'","",$txt_poly_date)." ".str_replace("'","",$txt_hours);
			$txt_hours="to_date('".$txt_hours."','DD MONTH YYYY HH24:MI:SS')";
			$data_array="INSERT INTO subcon_gmts_prod_dtls (".$field_array.") VALUES(".$id.",".$cbo_company_name.",".$cbo_location.",".$cbo_floor.",".$cbo_poly_line.",".$prod_reso_allo.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$txt_poly_date.",".$txt_sewing_qty.",".$txt_reject_qnty.",".$txt_alter_qnty.",".$txt_spot_qnty.",".$txt_hours.",".$txt_super_visor.",5,".$sewing_production_variable.",".$txt_challan.",".$txt_remark.",0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		//echo $data_array;die;
		//echo "insert into subcon_gmts_prod_dtls (".$field_array.") values ".$data_array;
		if($db_type==0)
		{
 		$rID=sql_insert("subcon_gmts_prod_dtls",$field_array,$data_array,0);
		}
		else
		{
			$rID=execute_query($data_array);
			
		}
//==========================Insert subcon_gmts_prod_col_sz Here=================		
		$dtls_id=return_next_id("id", "subcon_gmts_prod_col_sz", 1);
		$field_array1="id,dtls_id,production_type,gmts_item_id,production_date,ord_color_size_id,prod_qnty";
		
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise 
		{		
			for($i=1; $i<=$total_row; $i++)
			{
				$txt_colo_size_mst_id="txt_colo_size_mst_id_".$i;
				$colSize="colSize_".$i;
				if ($i!=1) $data_array1 .=",";
				$data_array1 .= "(".$dtls_id.",".$id.",5,".$cbo_item_name.",".$txt_poly_date.",".$$txt_colo_size_mst_id.",".$$colSize.")";
				
				$dtls_id=$dtls_id+1;
			}
		}
		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise 
		{
			$k=1;$j=1;$v=0;$tt=0;
			$tot_row=explode(",",$tot_row);
			for($k=1; $k<=$tot_span; $k++)
			{
				$tt=$k-1;
				for($i=1; $i<=$tot_row[$tt]; $i++)
				{
					$v++;
					$txt_colo_size_mst_id="txt_colo_size_mst_id".$k."___".$i;
					$colSize="colSize".$k."___".$i;
					//echo $colSize; die;
					if ($v!=1) $data_array1 .=",";
					$data_array1 .= "(".$dtls_id.",".$id.",5,".$cbo_item_name.",".$txt_poly_date.",".$$txt_colo_size_mst_id.",".$$colSize.")";
					$dtls_id=$dtls_id+1;
				}
				$j++;
			}
		}
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{ 
			//echo "insert into subcon_gmts_prod_col_sz (".$field_array1.") values ".$data_array1; die;
			$dtlsrID=sql_insert("subcon_gmts_prod_col_sz",$field_array1,$data_array1,1);
		}
		
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
			}else{
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
				if($rID && $dtlsrID)
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
		disconnect($con);
		die;
	}
//==========================Update Here=================		
	else if ($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
//==========================Update subcon_gmts_prod_dtls Here=================		
		$id=str_replace("'",'',$txt_mst_id);
		
		//if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_hours;else $reportTime = 12+str_replace("'","",$txt_hours);
		
		$field_array="location_id*floor_id*line_id*prod_reso_allo*production_qnty*production_date*reject_qnty*alter_qnty*spot_qnty*hour*supervisor*challan_no*remarks*is_deleted*status_active*updated_by*update_date";
		if($db_type==2)
		{
			$txt_hours=str_replace("'","",$txt_poly_date)." ".str_replace("'","",$txt_hours);
			$txt_hours="to_date('".$txt_hours."','DD MONTH YYYY HH24:MI:SS')";
		}			
		$data_array="".$cbo_location."*".$cbo_floor."*".$cbo_poly_line."*".$prod_reso_allo."*".$txt_sewing_qty."*".$txt_poly_date."*".$txt_reject_qnty."*".$txt_alter_qnty."*".$txt_spot_qnty."*".$txt_hours."*".$txt_super_visor."*".$txt_challan."*".$txt_remark."*'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo "update subcon_gmts_prod_dtls  SET (".$field_array.") values ".$data_array; die;			
		$rID=sql_update("subcon_gmts_prod_dtls",$field_array,$data_array,"id","".$txt_mst_id."",0);
//==========================Update subcon_gmts_prod_col_sz Here=================		
		$dtlsrDelete = execute_query("delete from subcon_gmts_prod_col_sz where dtls_id=$txt_mst_id",0);//color level wise 
		$dtls_id=return_next_id("id", "subcon_gmts_prod_col_sz", 1);
		$field_array1="id,dtls_id,production_type,gmts_item_id,production_date,ord_color_size_id,prod_qnty";
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise 
		{
			for($i=1; $i<=$total_row; $i++)
			{
				$txt_colo_size_mst_id="txt_colo_size_mst_id_".$i;
				$colSize="colSize_".$i;
				if ($i!=1) $data_array1 .=",";
				$data_array1 .= "(".$dtls_id.",".$id.",5,".$cbo_item_name.",".$txt_poly_date.",".$$txt_colo_size_mst_id.",".$$colSize.")";
				
				$dtls_id=$dtls_id+1;
			}
		}
		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise 
		{
			$k=1;$j=1;$v=0;$tt=0;
			$tot_row=explode(",",$tot_row);
			for($k=1; $k<=$tot_span; $k++)
			{
				$tt=$k-1;
				for($i=1; $i<=$tot_row[$tt]; $i++)
				{
					$v++;
					$txt_colo_size_mst_id="txt_colo_size_mst_id".$k."___".$i;
					$colSize="colSize".$k."___".$i;
					if ($v!=1) $data_array1 .=",";
					$data_array1 .= "(".$dtls_id.",".$id.",5,".$cbo_item_name.",".$txt_poly_date.",".$$txt_colo_size_mst_id.",".$$colSize.")";
					$dtls_id=$dtls_id+1;
				}
				$j++;
			}
		}
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{  
			$dtlsrID=sql_insert("subcon_gmts_prod_col_sz",$field_array1,$data_array1,1);
		}
		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID && $dtlsrDelete)
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
				if($rID && $dtlsrID && $dtlsrDelete)
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
		disconnect($con);
		die;
	}
}
?>