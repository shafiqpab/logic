<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
//$inpLevelArray = [1=>'In-line Inspection',2=>'Mid-line Inspection',3=>'Final Inspection'];
$inpLevelArray = array(1=>'In-line Inspection',2=>'Mid-line Inspection',3=>'Final Inspection');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$po_number=return_library_array( "select id,po_number from wo_po_break_down where status_active in(1,2,3) and  is_deleted=0", "id", "po_number"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company where status_active =1 and  is_deleted=0", "id", "company_name"  );
function pre($array)
{
	echo "<pre>";
	print_r($array);
	echo "</pre>";

}
//------------------------------------------------------------------------------------------------------

if($action=="load_variable_settings")
{
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=91","is_control");
	echo "document.getElementById('variable_is_controll').value=".$variable_is_control.";\n";
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
} 

if($action=="load_drop_working_company")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
 			echo create_drop_down( "cbo_working_company", 210, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select Working Company ---", $selected, "load_drop_down( 'requires/buyer_inspection_controller', 0, 'load_drop_down_working_location', 'working_location_td' );",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_working_company", 210, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Working Company--", $selected, "load_drop_down( 'requires/buyer_inspection_controller', 0, 'load_drop_down_working_location', 'working_location_td' );" );
		}
	}
 	else if($data==1)
 	{
  		echo create_drop_down( "cbo_working_company", 210, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Working Company --", '', "load_drop_down( 'requires/buyer_inspection_controller', this.value, 'load_drop_down_working_location', 'working_location_td' );",0 );
 	}
 	else
 		echo create_drop_down( "cbo_working_company", 210, $blank_array,"", 1, "--- Select Working Company ---", $selected, "load_drop_down( 'requires/buyer_inspection_controller', 0, 'load_drop_down_working_location', 'working_location_td' );",0,0 );
 	exit();
}

if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 210, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Working Location --", $selected, "load_drop_down( 'requires/buyer_inspection_controller', $data+'**'+this.value, 'load_drop_down_working_floor', 'working_floor_td' );" );
	exit();
}

if ($action=="load_drop_down_working_floor")
{
	$data=explode('**',str_replace("'","",$data));

	echo create_drop_down( "cbo_working_floor", 210, "select id,floor_name from lib_prod_floor where company_id='$data[0]' and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name","id,floor_name", 1, "-- Select Working Floor --", $selected, "" );
	exit();
}


if ($action=="load_drop_down_po_number")
{ 
	//echo "select id,po_number from wo_po_break_down where job_no_mst='$data'";
	echo create_drop_down( "cbo_order_id",100, "select id,po_number from wo_po_break_down where job_no_mst='$data' and status_active in(1,2,3) and is_deleted=0","id,po_number", 1, "--Select--", "", "load_drop_down( 'requires/buyer_inspection_controller', '__'+this.value, 'load_drop_down_week_no', 'week_drop_down_td' );load_drop_down( 'requires/buyer_inspection_controller', '__'+this.value, 'load_drop_down_country_id', 'country_drop_down_td' );get_php_form_data( this.value+','+document.getElementById('cbo_week_no').value+','+document.getElementById('cbo_country_id').value+','+document.getElementById('cbo_inspection_level').value, 'set_po_qnty_ship_date', 'requires/buyer_inspection_controller');","","","","","","" );
}



if ($action=="load_drop_down_week_no")
{ 
	list($job,$po_id)=explode('__',$data);
	if($po_id)$con="b.po_break_down_id=$po_id"; else $con="b.job_no_mst='$job'";
	
	echo create_drop_down( "cbo_week_no",100, "select a.week from week_of_year a,wo_po_color_size_breakdown b where $con and a.week_date =b.country_ship_date  and b.status_active=1 and b.is_deleted=0 group by a.week","week,week", 1, "--Select--", "", "load_drop_down( 'requires/buyer_inspection_controller', '__'+document.getElementById('cbo_order_id').value+'__'+this.value, 'load_drop_down_country_id', 'country_drop_down_td' );get_php_form_data( document.getElementById('cbo_order_id').value+','+this.value+','+document.getElementById('cbo_country_id').value+','+document.getElementById('cbo_inspection_level').value,'set_po_qnty_ship_date', 'requires/buyer_inspection_controller');","","","","","","" );
}

if ($action=="load_drop_down_country_id")
{ 
	list($job,$po_id,$week)=explode('__',$data);
	if($po_id)$con="b.po_break_down_id=$po_id"; else $con="b.job_no_mst='$job'";
	
	if($week)$con_week=" and c.week=$week"; else $con_week="";
	
	//echo create_drop_down( "cbo_country_id",100, "select a.id,a.country_name from lib_country a,wo_po_color_size_breakdown b where $con and a.id=b.country_id group by a.id,a.country_name order by a.country_name","id,country_name", 1, "--Select--", "", "get_php_form_data( document.getElementById('cbo_order_id').value+','+document.getElementById('cbo_week_no').value+','+this.value, 'set_po_qnty_ship_date', 'requires/buyer_inspection_controller')","","","","","","" );
	echo create_drop_down( "cbo_country_id",100, "SELECT a.id,a.country_name from lib_country a inner join wo_po_color_size_breakdown b on a.id=b.country_id left join week_of_year c on c.week_date=b.country_ship_date $con_week  where $con  and b.status_active=1 and b.is_deleted=0 group by a.id,a.country_name order by a.country_name","id,country_name", 1, "--Select--", "", "get_php_form_data( document.getElementById('cbo_order_id').value+','+document.getElementById('cbo_week_no').value+','+this.value+','+document.getElementById('cbo_inspection_level').value+','+document.getElementById('cbo_company_name').value, 'set_po_qnty_ship_date', 'requires/buyer_inspection_controller')","","","","","","" );
}



if ($action == "load_all_drop_down") 
{
	// location_td*transfer_com*forwarder_td*del_location_td*del_floor_td
	list($po_id,$wo_company,$wo_location,$source, $inspection_type,$buyer_id,$company_id) = explode("_",$data);
	
	if($po_id)$con="b.po_break_down_id=$po_id";
	
	echo create_drop_down( "cbo_week_no",100, "SELECT a.week from week_of_year a,wo_po_color_size_breakdown b where $con and a.week_date =b.country_ship_date  and b.status_active=1 and b.is_deleted=0 group by a.week","week,week", 1, "--Select--", "", "load_drop_down( 'requires/buyer_inspection_controller', '__'+document.getElementById('cbo_order_id').value+'__'+this.value, 'load_drop_down_country_id', 'country_drop_down_td' );get_php_form_data( document.getElementById('cbo_order_id').value+','+this.value+','+document.getElementById('cbo_country_id').value+','+document.getElementById('cbo_inspection_level').value,'set_po_qnty_ship_date', 'requires/buyer_inspection_controller');","","","","","","" );

	echo "****";
	
	if($po_id)$con="b.po_break_down_id=$po_id";
	echo create_drop_down( "cbo_country_id",100, "SELECT a.id,a.country_name from lib_country a inner join wo_po_color_size_breakdown b on a.id=b.country_id left join week_of_year c on c.week_date=b.country_ship_date  where $con  and b.status_active=1 and b.is_deleted=0 group by a.id,a.country_name order by a.country_name","id,country_name", 1, "--Select--", "", "get_php_form_data( document.getElementById('cbo_order_id').value+','+document.getElementById('cbo_week_no').value+','+this.value+','+document.getElementById('cbo_inspection_level').value+','+document.getElementById('cbo_company_name').value, 'set_po_qnty_ship_date', 'requires/buyer_inspection_controller')","","","","","","" );

	echo "****";

	if($source==3)
	{
		if($db_type==0)
		{
 			echo create_drop_down( "cbo_working_company", 210, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select Working Company ---", $selected, "load_drop_down( 'requires/buyer_inspection_controller', 0, 'load_drop_down_working_location', 'working_location_td' );",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_working_company", 210, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Working Company--", $selected, "load_drop_down( 'requires/buyer_inspection_controller', 0, 'load_drop_down_working_location', 'working_location_td' );" );
		}
	}
 	else if($source==1)
 	{
  		echo create_drop_down( "cbo_working_company", 210, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Working Company --", '', "load_drop_down( 'requires/buyer_inspection_controller', this.value, 'load_drop_down_working_location', 'working_location_td' );",0 );
 	}
 	else
 		echo create_drop_down( "cbo_working_company", 210, $blank_array,"", 1, "--- Select Working Company ---", $selected, "load_drop_down( 'requires/buyer_inspection_controller', 0, 'load_drop_down_working_location', 'working_location_td' );",0,0 );

	echo "****";

	echo create_drop_down( "cbo_working_location", 210, "select id,location_name from lib_location where company_id='$wo_company' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Working Location --", $selected, "load_drop_down( 'requires/buyer_inspection_controller', $wo_company+'**'+this.value, 'load_drop_down_working_floor', 'working_floor_td' );" );

	echo "****";

	echo create_drop_down( "cbo_working_floor", 210, "select id,floor_name from lib_prod_floor where company_id='$wo_company' and location_id='$wo_location' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name","id,floor_name", 1, "-- Select Working Floor --", $selected, "" );

	echo "****";

	
	if($inspection_type==1)
	{
	
		echo create_drop_down( "cbo_inspection_company", 210, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) and buy.id=$buyer_id order by buyer_name","id,buyer_name", 0, "-- Select --", $selected, "",1,0 );
	
	}
	else if($inspection_type==2)
	{
		echo create_drop_down( "cbo_inspection_company", 210, "select distinct a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.id=b.supplier_id and  b.party_type=41 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "" );     	 
	}
	else
	{
		echo create_drop_down( "cbo_inspection_company", 210, $company_library,"", 1, "--- Select---", $selected, "" );     	 
	}
	
	exit();
}

if ($action=="load_drop_down_buyer_party_company")
{
	$data=str_replace("'","",$data);
	list($inspection_type,$buyer,$company,$working_company)=explode(',',trim($data));
	if($inspection_type==1)
	{
	
	echo create_drop_down( "cbo_inspection_company", 210, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) and buy.id=$buyer order by buyer_name","id,buyer_name", 0, "-- Select --", $selected, "",1,0 );
	
	}
	else if($inspection_type==2)
	{
		echo create_drop_down( "cbo_inspection_company", 210, "select distinct a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.id=b.supplier_id and  b.party_type=41 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "" );     	 
	}
	else
	{
		echo create_drop_down( "cbo_inspection_company", 210, $company_library,"", 1, "--- Select---", $working_company, "" );     	 
	}

}

if ($action=="order_search_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);

	?>
     
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( job_no )
	{ //alert(job_no);return;
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
	
    </script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="1100" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
	    	<tr>
	        	<td align="center" width="100%">
	            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
	                    <thead>                	 
	                        <th width="150" class="must_entry_caption">Company Name</th><th width="150">Buyer Name</th><th width="100">Order</th><th width="100">Job No</th><th width="50">File No.</th><th width="100">Internal Ref No.</th><th width="100">Style Ref No.</th><th width="200">Date Range</th><th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
	                    </thead>
	        			<tr>
	                    	<td> <input type="hidden" id="selected_job">
								<? 
									echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'buyer_inspection_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                    </td>
	                   	<td id="buyer_td">
	                     <? 
							echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
						?>	</td>
	                    <td id="search_td">
	                    <input name="txt_order" id="txt_order"  class="text_boxes" style="width:80px">
	                   </td>
	                    <td id="search_td">
	                    <input name="txt_file" id="txt_file"  class="text_boxes" style="width:80px">
	                   </td>
	                   <td id="search_td">
	                    <input name="txt_inter_ref" id="txt_inter_ref"  class="text_boxes" style="width:80px">
	                   </td>
	                   <td >
	                    <input name="txt_style_ref" id="txt_style_ref"  class="text_boxes" style="width:80px">
	                   </td>
	                   <td >
	                    <input name="txt_job" id="txt_job"  class="text_boxes" style="width:80px">
	                   </td>
	                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
						  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						 </td> 
	            		 <td align="center"> 
	                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_order').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('txt_file').value+'_'+document.getElementById('txt_inter_ref').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('cbo_year_selection').value, 'create_po_search_list_view', 'search_div', 'buyer_inspection_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
	        		</tr>
	             </table>
	          </td>
	        </tr>
	        <tr>
	            <td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
	            </td>
	            </tr>
	        <tr>
	            <td align="center" valign="top" id="search_div"> 
		
	            </td>
	        </tr>
	    </table>    
	     
	    </form>
	   </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}
if($action=="open_order_popup")
{

	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>     
	<script> 
	
		function js_set_value( data )
		{ 
			//alert(data);return;
			data=data.split("_"); 
			document.getElementById('hidden_order_val').value=data[1];
			document.getElementById('hidden_order_id').value=data[0];
			document.getElementById('hidden_actual_order_id').value=data[2];
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<input type="hidden" name="hidden_order_val" id="hidden_order_val">
	<input type="hidden" name="hidden_order_id" id="hidden_order_id">
	<input type="hidden" name="hidden_actual_order_id" id="hidden_actual_order_id">
	</form>

	<? 
		$arr=array();
		// $sql= "SELECT a.id,a.po_number,b.id as actual_id,b.acc_po_no from wo_po_break_down a left join wo_po_acc_po_info b on  a.id=b.po_break_down_id and b.status_active=1  where   a.status_active=1  and a.is_deleted=0 and a.job_no_mst='$txt_job_no'";
		$sql= "SELECT a.id,a.po_number,sum(distinct (a.po_quantity*c.total_set_qnty)) as po_quantity,SUM (case when b.inspection_level=3 and b.inspection_status=1 then b.inspection_qnty else 0 end) as insp_qty,sum(distinct (a.po_quantity*c.total_set_qnty))-SUM (case when b.inspection_level=3 and b.inspection_status=1 then b.inspection_qnty else 0 end) as balance from wo_po_details_master c,wo_po_break_down a left join pro_buyer_inspection b on  a.id=b.po_break_down_id and b.status_active=1  where  c.id=a.job_id and a.status_active=1  and a.is_deleted=0 and a.job_no_mst='$txt_job_no' group by  a.id,a.po_number order by a.po_number";
		// echo $sql;
		echo  create_list_view("list_view", "Order No.,Order Qty,Insp Qty,Balance", "150,140,140,150","590","350",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,0,0", $arr , "po_number,po_quantity,insp_qty,balance", "requires/buyer_inspection_controller",'setFilterGrid("list_view",-1);','','');
	 
} 


if($action=="open_actual_order_popup")
{

	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>     
	<script> 
	
		function js_set_value( data )
		{ 
			//alert(data);return;
			data=data.split("_"); 
			document.getElementById('hidden_actual_order_no').value=data[1];
			document.getElementById('hidden_actual_order_id').value=data[0];
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<input type="hidden" name="hidden_actual_order_no" id="hidden_actual_order_no">
	<input type="hidden" name="hidden_actual_order_id" id="hidden_actual_order_id">
	</form>

	<? 
		$arr=array();
		$sql= "SELECT b.id as actual_id,b.acc_po_no from wo_po_acc_po_info b where   b.status_active=1  and b.is_deleted=0 and b.po_break_down_id=$txt_po_no";
		// echo $sql;
		echo  create_list_view("list_view", "Actual Order No.", "100","590","350",0, $sql , "js_set_value", "actual_id,acc_po_no", "", 1, "0", $arr , "acc_po_no", "requires/buyer_inspection_controller",'setFilterGrid("list_view",-1);','','');
	 
} 

if($action=="create_po_search_list_view")
{

	$data=explode('_',$data);
	//var_dump($data);
	
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if (trim($data[5])!='') $order_no = " and b.po_number like '%".trim($data[5])."%'";  else  $order_no="";
	if (trim($data[6])!='') $style_ref = " and a.style_ref_no='$data[6]'";  else  $style_ref="";
	if (trim($data[7])!='') $job_no = " and a.job_no_prefix_num='$data[7]'";  else  $job_no="";
	if (trim($data[8])!='') $file_no = " and b.file_no='$data[8]'";  else  $file_no="";
	if (trim($data[9])!='') $grouping = " and b.grouping='$data[9]'";  else  $grouping="";
	$job_year_cond="";
	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) as year";
		if (trim($data[10])!='') $job_year_cond .= " and YEAR(a.insert_date)=$data[10]"; 
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	 
	else
	{
		if (trim($data[10])!='') $job_year_cond .= " and to_char(a.insert_date,'YYYY')=$data[10]";  
		$year_field="to_char(a.insert_date,'YYYY') as year";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'"; else $shipment_date ="";
	}
	//echo $job_no;die;
	
	//$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
	 
	  
	
	
	if($data[1]==0)$buyer_con=""; else $buyer_con="and a.buyer_name=".trim($data[1]) ;
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$buyer_arr);
	
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$data[0]");
    $projected_po_cond = ($is_projected_po_allow==2) ? " and b.is_confirmed=1" : "";
	
	if ($data[2]==0)
	{
	 	  $sql= "SELECT a.job_no,$year_field,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.grouping,b.file_no,b.po_number,b.po_quantity,b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active in(1,2,3) $job_year_cond $shipment_date $company $buyer_con $order_no $job_no $file_no $grouping $style_ref $projected_po_cond order by a.job_no";  
		 echo  create_list_view("list_view", "Job No,Year,Buyer Name,File No,Internal Ref.,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "90,120,100,50,100,100,100,90,90,90,80","1100","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,file_no,grouping,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,0,0,0,1,3') ;
	}
	else
	{
		$sql= "SELECT a.job_no,$year_field,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0  $job_year_cond $company $buyer_con $job_no $order_no order by a.job_no";
		echo  create_list_view("list_view", "Job No,Year,Buyer Name,Style Ref. No,", "90,120,100,100,90","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,style_ref_no", "",'','0,0,0,0,1,0,2,3') ;
	}
} 

if ($action=="open_insp_qty_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);

	?>
     
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( job_no )
	{ //alert(job_no);return;
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
	function fnc_close( )
		{
			var rowCount = $('#ins_tbl tbody tr').length;
			//alert( rowCount );return;
			var breck_down_data="";
			var sum=0;
			for(var i=1; i<=rowCount; i++)
			{
				sum+=$('#itemColorInsQty_'+i).val()*1;
				if(breck_down_data=="")
				{
					breck_down_data+=$('#itemId_'+i).val()+'_'+($('#colorId_'+i).val()*1)+'_'+($('#colorQty_'+i).val()*1)+'_'+($('#itemColorInsQty_'+i).val()*1)+'_'+($('#sizeId_'+i).val()*1)+'_'+($('#shippedQty_'+i).val()*1);
				}
				else
				{
					breck_down_data+="----"+$('#itemId_'+i).val()+'_'+($('#colorId_'+i).val()*1)+'_'+($('#colorQty_'+i).val()*1)+'_'+($('#itemColorInsQty_'+i).val()*1)+'_'+($('#sizeId_'+i).val()*1)+'_'+($('#shippedQty_'+i).val()*1);
				}
			}
			//alert (sum);
			document.getElementById('hidden_all_data').value=breck_down_data;
			document.getElementById('hidden_qnty_data').value=sum;
			//alert(document.getElementById('hidden_all_data').value);
			 
			parent.emailwindow.hide();
		}

		function validateInsQty(id,avail_qty) 
		{	
			let input = $('#itemColorInsQty_'+id);
			let insQty = input.val() *1;
			let extendQty = avail_qty-insQty;
			if (extendQty<0) 
			{
				alert('Qnty Excceded by '+extendQty);
				input.val("");
			}
		}
    </script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="size_1" id="size_1">
	<?
		$ex_data=explode('_____',$data);
		//var_dump($data);	
		$job_no = $ex_data[0];
		$order = $ex_data[1];
		$country = $ex_data[2];
		$hidden_ins_data = $ex_data[3];
		$garments_nature = $ex_data[4];
		$company_name = $ex_data[5];
		$inspection_level = $ex_data[6];
		$inspection_status = $ex_data[7];

		if($country){$country_cond=" and b.country_id='$country'"; }else{ $country_cond="";}
				
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$arr=array (1=>$garments_item,2=>$color_arr);

		$control_and_preceding=sql_select("SELECT is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=100 and company_name=$company_name");
		$is_control = $control_and_preceding[0][csf("is_control")];
		$preceding_page_id = $control_and_preceding[0][csf("preceding_page_id")];

		$color_size_variable_setting = return_field_value("work_study_integrated","variable_settings_production","company_name=$company_name and variable_list=1","work_study_integrated");

		// echo $color_size_variable_setting; die("**");
		$ins_cond 	= "";
		$ins_cond 	.= $country 			? " and a.country_id=$country" : "";
		$ins_cond 	.= $inspection_level 	? " and a.inspection_level=$inspection_level" : "";
		$ins_cond 	.= ($inspection_status && $inspection_status!=3) 	? " and a.inspection_status=$inspection_status" : "";
		$prod_type	= ($inspection_level == 1 ) ? 5 : 8;

		if ($color_size_variable_setting == 3)  //Color Size Level
		{
		
			if($garments_nature==100 && $preceding_page_id==1) // 100 means sweater
			{
				$country_cond = str_replace("b.country_id", "a.country_id", $country_cond);

				$sql = "SELECT a.po_break_down_id as id, a.color_number_id,a.size_number_id,a.item_number_id,sum(c.production_qnty) as qnty from wo_po_color_size_breakdown a,pro_garments_production_mst b, pro_garments_production_dtls c where a.po_break_down_id=$order and a.job_no_mst='$job_no' and a.po_break_down_id=b.po_break_down_id and b.id=c.mst_id and a.id=c.color_size_break_down_id and a.item_number_id=b.item_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $country_cond and b.production_type=1 group by a.po_break_down_id, a.color_number_id,a.item_number_id,a.size_number_id,a.size_order order by b.color_number_id,a.size_order";
			}
			else
			{
				$sql = "SELECT a.id,b.color_number_id,b.size_number_id,b.item_number_id,sum(b.order_quantity) as qnty from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=$order and a.job_no_mst='$job_no' and a.job_no_mst=b.job_no_mst and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $country_cond group by a.id, b.color_number_id,b.item_number_id,b.size_number_id,b.size_order order by b.color_number_id, b.size_order";
			}
			// echo $sql;die(); 
			$sql_res = sql_select($sql);
			// pre($sql_res);die;
			

			$prod_country_cond = $country ?  " and c.country_id=$country" : "";
			$production_sql="SELECT b.production_qnty as qnty,c.po_break_down_id,c.color_number_id,c.size_number_id,c.item_number_id,c.country_id from wo_po_color_size_breakdown c,pro_garments_production_mst a, pro_garments_production_dtls b where c.po_break_down_id=a.po_break_down_id and c.item_number_id=a.item_number_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=$prod_type and  c.po_break_down_id=$order $prod_country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0";
			// echo $production_sql; die;
			$prod_qty_arry=array();
			foreach (sql_select($production_sql) as $v)
			{
				$prod_qty_arry[$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']][$v['SIZE_NUMBER_ID']]+=$v['QNTY'];
			}
			// echo "<pre>";print_r($prod_qty_arry); die;
			
			$buyer_inp_sql="SELECT a.po_break_down_id,a.country_id,b.item_id,b.color_id,b.size_id,b.ins_qty from pro_buyer_inspection a, pro_buyer_inspection_breakdown b where a.id=b.mst_id and a.entry_form=470 and  a.po_break_down_id=$order  $ins_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			// echo $buyer_inp_sql; die;

			$buyer_inp_qty_arry=array();
			$buyer_inp_res = sql_select($buyer_inp_sql);
			// pre($buyer_inp_res); die;
			foreach ($buyer_inp_res as $r)
			{
				$buyer_inp_qty_arry[$r['ITEM_ID']][$r['COLOR_ID']][$r['SIZE_ID']]+=$r['INS_QTY'];
			}
			// echo "<pre>";print_r($buyer_inp_qty_arry); die;

			?>         
			<table border="1" cellpadding="0" cellspacing="0" id="ins_tbl" rules="all" class="rpt_table" width="580">
				<thead>
					<tr>
						<th width="40">Sl.</th>
						<th width="150">Item Name</th>
						<th width="150">Color Name</th>
						<th width="150">Size Name</th>
						<th width="80"> Qty.</th>
						<th width="80">Ready to Shipped Qty.</th>
						<th width="80">Inspection Qty.</th>
					</tr>
				</thead>
				<input type="hidden" name="hidden_all_data" id="hidden_all_data" class="text_boxes /">
				<input type="hidden" name="hidden_qnty_data" id="hidden_qnty_data" class="text_boxes /">
				<tbody>
					<?
				
					if($hidden_ins_data)
					{
						// echo "string".$hidden_ins_data; die;
						$i=1;
						$data_arr=explode("----", $hidden_ins_data);
						// echo count($data_arr); die;
						// pre($data_arr); die;
						foreach ($data_arr as $all_val) 
						{	
							
							$val=explode("_", $all_val);	
							$item_id   = $val[0];
							$color_id  = $val[1];
							$po_qty    = $val[2];
							$ins_qty   = $val[3];
							$size_id   = $val[4];
							$shipped_qty= $val[5];
							$finish_qty= $prod_qty_arry[$item_id][$color_id][$size_id];	
							$avail_qty = ( $finish_qty + $ins_qty ) - $buyer_inp_qty_arry[$item_id][$color_id][$size_id];
							?>
							<tr>
								<td><?= $i; ?></td>
								<td>
									<?= $garments_item[$item_id];?>
									<input type="hidden" id="itemId_<?= $i;?>" name="itemColorInsQty<?= $i;?>" value="<?= $item_id;?>">
								</td>
								<td>
									<?= $color_arr[$color_id];?>
									<input type="hidden" id="colorId_<?= $i;?>" name="colorId_<?= $i;?>" value="<?= $color_id;?>">
								</td>
								<td>
									<?= $size_library[$size_id];?> 
									<input type="hidden" id="sizeId_<?= $i;?>" name="sizeId_<?= $i;?>" value="<?=$size_id;?>">
								</td>
								<td align="right">
									<?= $val[csf("qnty")];?>
									<input type="text" id="colorQty_<?= $i;?>" name="colorQty_<?= $i;?>" disabled="" readonly value="<?= $po_qty ;?>">
								</td>
								<td>
									<input class="text_boxes_numeric" type="text" id="shippedQty_<?= $i;?>" name="shippedQty_<?= $i;?>" value="<?= $shipped_qty ?>">
								</td> 
								<td>
									<input class="text_boxes_numeric" type="text" id="itemColorInsQty_<?= $i;?>" name="itemColorInsQty_<?= $i;?>" value="<?= $val[3]?>" onkeyup="validateInsQty(<?=$i?>,<?= $avail_qty ?>)" placeholder="<?=$avail_qty?>">
								</td> 
							</tr> 
							<?
							$i++;
						}
						 

					}
					else
					{


						$i=1;
						foreach ($sql_res as $val) 
						{	
							$item_id  = $val[csf("item_number_id")];
							$color_id = $val[csf("color_number_id")];
							$size_id  = $val[csf("size_number_id")];
							$insp_qty = $buyer_inp_qty_arry[$item_id][$color_id][$size_id];
							$prod_qty = $prod_qty_arry[$item_id][$color_id][$size_id];
							$avail_qty= $prod_qty - $insp_qty;		
							?>
							<tr>
								<td><?= $i ;?></td>
								<td>
									<?= $garments_item[$item_id];?>
									<input type="hidden" id="itemId_<?= $i;?>" name="itemColorInsQty<?= $i;?>" value="<?= $item_id; ?>">
								</td>
								<td>
									<?= $color_arr[$color_id];?> 
									<input type="hidden" id="colorId_<?= $i;?>" name="colorId_<?= $i;?>" value="<?= $color_id; ?>">
								</td>
								<td>
									<?= $size_library[$size_id];?> 
									<input type="hidden" id="sizeId_<?= $i;?>" name="sizeId_<?= $i;?>" value="<?= $size_id ;?>">
								</td>
								<td align="right">
									<?= $val["QNTY"];?>
									<input type="hidden" id="colorQty_<?= $i;?>" name="colorQty_<?= $i;?>" value="<?=$val["QNTY"];?>">
								</td>
								<td>
									<input class="text_boxes_numeric" type="text" id="shippedQty_<?= $i;?>" name="shippedQty_<?= $i;?>">
								</td> 
								<td>
									<input  class="text_boxes_numeric" type="text_boxes_numeric" id="itemColorInsQty_<?= $i;?>" name="itemColorInsQty_<?= $i;?>" onkeyup="validateInsQty(<?=$i?>,<?= $avail_qty ?>)" placeholder="<?= $avail_qty ?>" > 
								</td>
								
							</tr>
							<?

							$i++;
						}
					}
					?>
					
				</tbody>
			</table>
			<table>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td align="center" colspan="5">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>	  
				</tr>
			</table>
			</form>
			<?
		}
		else if ($color_size_variable_setting == 2)	 								//Color Level
		{
		
			if($garments_nature==100 && $preceding_page_id==1) // 100 means sweater
			{
				$country_cond = str_replace("b.country_id", "a.country_id", $country_cond);
				$sql = "SELECT a.po_break_down_id as id, a.color_number_id,a.item_number_id,sum(c.production_qnty) as qnty from wo_po_color_size_breakdown a,pro_garments_production_mst b, pro_garments_production_dtls c where a.po_break_down_id=$order and a.job_no_mst='$job_no' and a.po_break_down_id=b.po_break_down_id and b.id=c.mst_id and a.id=c.color_size_break_down_id and a.item_number_id=b.item_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $country_cond and b.production_type=1 group by a.po_break_down_id, a.color_number_id,a.item_number_id order by a.color_number_id";
			}
			else
			{
				$sql = "SELECT a.id, b.color_number_id,b.item_number_id,sum(b.order_quantity) as qnty from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=$order and a.job_no_mst='$job_no' and a.job_no_mst=b.job_no_mst and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $country_cond group by a.id, b.color_number_id,b.item_number_id order by b.color_number_id";
			}
			// echo $sql;die();
			$sql_res = sql_select($sql);
			
			
			
			$prod_country_cond = $country ?  " and c.country_id=$country" : "";

			$production_sql="SELECT b.production_qnty as qnty,c.po_break_down_id,c.color_number_id,c.item_number_id,c.country_id from wo_po_color_size_breakdown c,pro_garments_production_mst a, pro_garments_production_dtls b where c.po_break_down_id=a.po_break_down_id and c.item_number_id=a.item_number_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=$prod_type and  c.po_break_down_id=$order $prod_country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0";
			// echo $production_sql; die;
			$prod_qty_arry=array();
			foreach (sql_select($production_sql) as $v)
			{
				$prod_qty_arry[$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]+=$v['QNTY'];
			}
			// echo "<pre>";print_r($prod_qty_arry); die;
			

			$buyer_inp_sql="SELECT a.po_break_down_id,a.country_id,b.item_id,b.color_id,b.ins_qty from pro_buyer_inspection a, pro_buyer_inspection_breakdown b where a.id=b.mst_id and a.entry_form=470 and  a.po_break_down_id=$order $ins_cond and a.status_active=1 and a.inspection_status<>3 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			// echo $buyer_inp_sql; die;

			$buyer_inp_qty_arry=array();
			foreach (sql_select($buyer_inp_sql) as $r)
			{
				$buyer_inp_qty_arry[$r['ITEM_ID']][$r['COLOR_ID']]+=$r['INS_QTY'];
			}
			// echo "<pre>";print_r($buyer_inp_qty_arry); die;

			?>         
			<table border="1" cellpadding="0" cellspacing="0" id="ins_tbl" rules="all" class="rpt_table" width="580">
				<thead>
					<tr>
						<th width="40">Sl.</th>
						<th width="150">Item Name</th>
						<th width="150">Color Name</th>
						<th width="80">Color Qty.</th>
						<th width="80">Ready to Shipped Qty.</th>
						<th width="80">Inspection Qty.</th>
					</tr>
				</thead>
				<input type="hidden" name="hidden_all_data" id="hidden_all_data" class="text_boxes /">
				<input type="hidden" name="hidden_qnty_data" id="hidden_qnty_data" class="text_boxes /">
				<tbody>
					<?
				
					if($hidden_ins_data)
					{
						//echo "string".$hidden_ins_data;
						$i=1;
						$data_arr=explode("----", $hidden_ins_data);
						foreach ($data_arr as $all_val) 
						{	
							$val=explode("_", $all_val);	
							$item_id  	  = $val[0];
							$color_id 	  = $val[1];
							$po_qty   	  = $val[2];
							$ins_qty  	  = $val[3]; 
							$shipped_qty  = $val[5]; 
							$finish_qty   = $prod_qty_arry[$item_id][$color_id]; 
							$ttl_ins_qty  = $buyer_inp_qty_arry[$item_id][$color_id]; 
							$avail_qty    = ( $finish_qty+$ins_qty ) - $ttl_ins_qty;
							?>
							<tr>
								<td><?= $i ;?></td>
								<td><?= $garments_item[$item_id];?>
								<input type="hidden" id="itemId_<?= $i;?>" name="itemColorInsQty<?= $i;?>" value="<?=$item_id;?>">
									
								</td>
								<td>
									<?= $color_arr[$color_id];?>
									<input type="hidden" id="colorId_<?= $i;?>" name="colorId_<?= $i;?>" value="<?= $color_id;?>">
								</td>
								<td align="right"><?= $val[csf("qnty")];?>
									<input type="text" id="colorQty_<?= $i;?>" name="colorQty_<?= $i;?>" disabled="" readonly value="<?= $po_qty;?>">
									<input type="hidden" id="sizeId_<?= $i;?>" name="sizeId_<?= $i;?>" value="">
								</td>
								
								<td>
									<input class="text_boxes_numeric" type="text" id="shippedQty_<?= $i;?>" name="shippedQty_<?= $i;?>" value="<?= $shipped_qty ?>">
								</td> 
								<td><input class="text_boxes_numeric" type="text" id="itemColorInsQty_<?= $i;?>" name="itemColorInsQty_<?= $i;?>" value="<?= $ins_qty ?>" onkeyup="validateInsQty(<?=$i?>,<?= $avail_qty ?>)" placeholder="<?=$avail_qty?>"></td> 
								
							</tr>
							<?
							$i++;
						}

					}
					else
					{


						$i=1;
						foreach ($sql_res as $val) 
						{			
							$item_id  = $val[csf("item_number_id")];
							$color_id = $val[csf("color_number_id")];
							$insp_qty = $buyer_inp_qty_arry[$item_id][$color_id];
							$prod_qty = $prod_qty_arry[$item_id][$color_id];
							$avail_qty= $prod_qty - $insp_qty;
							?>
							<tr>
								<td><? echo $i ;?></td>
								<td><? echo $garments_item[$item_id];?>
								<input type="hidden" id="itemId_<?echo $i;?>" name="itemColorInsQty<?echo $i;?>" value="<?= $item_id;?>">
									
								</td>
								<td><? echo $color_arr[$color_id];?>
									
									<input type="hidden" id="colorId_<?echo $i;?>" name="colorId_<?echo $i;?>" value="<?= $color_id;?>">
								</td>
								<td align="right"><? echo $val[csf("qnty")];?>
										<input type="hidden" id="colorQty_<?echo $i;?>" name="colorQty_<?echo $i;?>" value="<? echo$val[csf("qnty")];?>">
										<input type="hidden" id="sizeId_<?= $i;?>" name="sizeId_<?= $i;?>">
								</td>
								<td>
									<input class="text_boxes_numeric" type="text" id="shippedQty_<?= $i;?>" name="shippedQty_<?= $i;?>" >
								</td>
								<td><input class="text_boxes_numeric" onkeyup="validateInsQty(<?=$i?>,<?= $avail_qty ?>)" type="text_boxes_numeric" id="itemColorInsQty_<?echo $i;?>" placeholder="<?= $avail_qty ?>" name="itemColorInsQty_<?echo $i;?>" ></td>
								
							</tr>
							<?
							$i++;
						}
					}
					?>
					
				</tbody>
			</table>
			<table>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td align="center" colspan="5">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>	  
				</tr>
			</table>
			</form>
			<?
		}
		else	 								//Gross Level
		{
		
			if($garments_nature==100 && $preceding_page_id==1) // 100 means sweater
			{
				$country_cond = str_replace("b.country_id", "a.country_id", $country_cond);
				$sql = "SELECT a.po_break_down_id as id,a.item_number_id,sum(c.production_qnty) as qnty from wo_po_color_size_breakdown a,pro_garments_production_mst b, pro_garments_production_dtls c where a.po_break_down_id=$order and a.job_no_mst='$job_no' and a.po_break_down_id=b.po_break_down_id and b.id=c.mst_id and a.id=c.color_size_break_down_id and a.item_number_id=b.item_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $country_cond and b.production_type=1 group by a.po_break_down_id,a.item_number_id ";
			}
			else
			{
				$sql = "SELECT a.id,b.item_number_id,sum(b.order_quantity) as qnty from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=$order and a.job_no_mst='$job_no' and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $country_cond group by a.id,b.item_number_id";
			}
			// echo $sql;die();
			$sql_res = sql_select($sql);
			
			
			
			$prod_country_cond = $country ?  " and c.country_id=$country" : "";

			$production_sql="SELECT b.production_qnty as qnty,c.po_break_down_id,c.item_number_id,c.country_id from wo_po_color_size_breakdown c,pro_garments_production_mst a, pro_garments_production_dtls b where c.po_break_down_id=a.po_break_down_id and c.item_number_id=a.item_number_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=$prod_type and  c.po_break_down_id=$order $prod_country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0";
			// echo $production_sql; die;
			$prod_qty_arry=array();
			foreach (sql_select($production_sql) as $v)
			{
				$prod_qty_arry[$v['ITEM_NUMBER_ID']]+=$v['QNTY'];
			}
			// echo "<pre>";print_r($prod_qty_arry); die;
			

			$buyer_inp_sql="SELECT a.po_break_down_id,a.country_id,b.item_id,b.ins_qty from pro_buyer_inspection a, pro_buyer_inspection_breakdown b where a.id=b.mst_id and a.entry_form=470 and  a.po_break_down_id=$order $ins_cond and a.status_active=1 and a.inspection_status<>3 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			// echo $buyer_inp_sql; die;

			$buyer_inp_qty_arry=array();
			foreach (sql_select($buyer_inp_sql) as $r)
			{
				$buyer_inp_qty_arry[$r['ITEM_ID']]+=$r['INS_QTY'];
			}
			// echo "<pre>";print_r($buyer_inp_qty_arry); die;

			?>         
			<table border="1" cellpadding="0" cellspacing="0" id="ins_tbl" rules="all" class="rpt_table" width="430">
				<thead>
					<tr>
						<th width="40">Sl.</th>
						<th width="150">Item Name</th> 
						<th width="80">Qty.</th>
						<th width="80">Ready to Shipped Qty.</th>
						<th width="80">Inspection Qty.</th>
					</tr>
				</thead>
				<input type="hidden" name="hidden_all_data" id="hidden_all_data" class="text_boxes /">
				<input type="hidden" name="hidden_qnty_data" id="hidden_qnty_data" class="text_boxes /">
				<tbody>
					<?
				
					if($hidden_ins_data)
					{
						//echo "string".$hidden_ins_data;
						$i=1;
						$data_arr=explode("----", $hidden_ins_data);
						foreach ($data_arr as $all_val) 
						{	
							$val=explode("_", $all_val);	
							$item_id  	  = $val[0];
							$color_id 	  = $val[1];
							$po_qty   	  = $val[2];
							$ins_qty  	  = $val[3]; 
							$shipped_qty  = $val[5]; 
							$finish_qty   = $prod_qty_arry[$item_id]; 
							$ttl_ins_qty  = $buyer_inp_qty_arry[$item_id]; 
							$avail_qty    = ( $finish_qty+$ins_qty ) - $ttl_ins_qty;
							?>
							<tr>
								<td><?= $i ;?></td>
								<td>
									<?= $garments_item[$item_id];?>
									<input type="hidden" id="itemId_<?= $i;?>" name="itemColorInsQty<?= $i;?>" value="<?=$item_id;?>"> 
								</td> 
								<td align="right"><?= $po_qty;?>
									<input type="hidden" id="colorQty_<?= $i;?>" name="colorQty_<?= $i;?>" value="<?= $po_qty;?>">
									
									<input type="hidden" id="colorId_<?= $i;?>" name="colorId_<?= $i;?>" value="">
									<input type="hidden" id="sizeId_<?= $i;?>" name="sizeId_<?= $i;?>" value="">
								</td>
								
								<td>
									<input class="text_boxes_numeric" type="text" id="shippedQty_<?= $i;?>" name="shippedQty_<?= $i;?>" value="<?= $shipped_qty ?>">
								</td> 
								<td><input class="text_boxes_numeric" type="text" id="itemColorInsQty_<?= $i;?>" name="itemColorInsQty_<?= $i;?>" value="<?= $ins_qty ?>" onkeyup="validateInsQty(<?=$i?>,<?= $avail_qty ?>)" placeholder="<?=$avail_qty?>"></td> 
								
							</tr>
							<?
							$i++;
						}

					}
					else
					{


						$i=1;
						foreach ($sql_res as $val) 
						{			
							$item_id  = $val[csf("item_number_id")];
							// $color_id = $val[csf("color_number_id")];
							$insp_qty = $buyer_inp_qty_arry[$item_id];
							$prod_qty = $prod_qty_arry[$item_id];
							$avail_qty= $prod_qty - $insp_qty;
							?>
							<tr>
								<td><? echo $i ;?></td>
								<td>
									<? echo $garments_item[$item_id];?>
									<input type="hidden" id="itemId_<?echo $i;?>" name="itemColorInsQty<?echo $i;?>" value="<?= $item_id;?>">
									
								</td> 
								<td align="right"><? echo $val[csf("qnty")];?>
										<input type="hidden" id="colorQty_<?echo $i;?>" name="colorQty_<?echo $i;?>" value="<? echo$val[csf("qnty")];?>">
										<input type="hidden" id="sizeId_<?= $i;?>" name="sizeId_<?= $i;?>">
										<input type="hidden" id="colorId_<?echo $i;?>" name="colorId_<?echo $i;?>"  >
								</td>
								<td>
									<input class="text_boxes_numeric" type="text" id="shippedQty_<?= $i;?>" name="shippedQty_<?= $i;?>" >
								</td>
								<td><input class="text_boxes_numeric" onkeyup="validateInsQty(<?=$i?>,<?= $avail_qty ?>)" type="text_boxes_numeric" id="itemColorInsQty_<?echo $i;?>" placeholder="<?= $avail_qty ?>" name="itemColorInsQty_<?echo $i;?>" ></td>
								
							</tr>
							<?
							$i++;
						}
					}
					?>
					
				</tbody>
			</table>
			<table>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td align="center" colspan="5">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>	  
				</tr>
			</table>
			</form>
			<?
		}
		?>	
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
	<?
}


if ($action=="field_level_access")
{
	echo "setFieldLevelAccess($data);\n";
 	exit();
}


if ($action=="populate_order_data_from_search_popup")
{
	//$data=explode("_",$data);
		if($db_type==0) $gro_field="";
	if($db_type==2) $gro_field=" group by a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.style_description,a.order_uom,a.set_break_down,a.gmts_item_id,a.total_set_qnty ";
	else $gro_field="";
	
	//echo "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.style_description,a.order_uom,a.set_break_down,a.gmts_item_id,a.total_set_qnty, sum(b.po_quantity) as po_quantity   from wo_po_details_master a, wo_po_break_down b where  a.job_no ='".$data."' and a.job_no=b.job_no_mst $gro_field";
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.style_description,a.order_uom,a.set_break_down,a.gmts_item_id,a.total_set_qnty, sum(b.po_quantity) as po_quantity   from wo_po_details_master a, wo_po_break_down b where  a.job_no ='".$data."' and a.job_no=b.job_no_mst $gro_field");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_style_no').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_style_des').value = '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('txt_order_qty').value = '".$row[csf("po_quantity")]."';\n";
		//echo "document.getElementById('txt_plancut_qty').value = '".$row[csf("plan_cut")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";

		echo "get_php_form_data('".$row[csf('company_name')]."','field_level_access','requires/buyer_inspection_controller');\n";
    }
	$compa_id= $data_array[0][csf("company_name")];
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$compa_id and variable_list=33 and page_category_id=91","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";

	

	exit();
}

?>
<?
if($action=="open_set_list_view")
{
echo load_html_head_contents("Set Entry","../../", 1, 1, $unicode,'','');
extract($_REQUEST);

?>
<script>
function js_set_value_set()
{
	var rowCount = $('#tbl_set_details tr').length-1;
	var set_breck_down="";
	var item_id=""
	for(var i=1; i<=rowCount; i++)
	{
		if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
		{
			return;
		}
		if(set_breck_down=="")
		{
			set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val();
			item_id+=$('#cboitem_'+i).val();
		}
		else
		{
			set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val();
			item_id+=","+$('#cboitem_'+i).val();
		}
	}
	document.getElementById('set_breck_down').value=set_breck_down;
	document.getElementById('item_id').value=item_id;
	parent.emailwindow.hide();
}
</script>
</head>
<body>
       <div id="set_details"  align="center">            
    	<fieldset>
        	<form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />     
            <input type="hidden" id="item_id" />
            <table width="350" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="250" class="must_entry_caption">Item</th><th class="must_entry_caption">Set Item Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$tot_set_qnty=0;
					$data_array=explode("__",$set_breck_down);
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							$tot_set_qnty=$tot_set_qnty+$data[1];
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
									<? 
										echo create_drop_down( "cboitem_".$i, 250, $garments_item, "",1,"-- Select Item --", $data[0], "",1,'' ); 
									?>
                                    </td>
                                    <td>
                                    <input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:80px"  class="text_boxes_numeric" onChange="set_sum_value_set( 'tot_set_qnty','txtsetitemratio_' )"  value="<? echo $data[1]; ?>"  readonly/> 
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
						
					?>
                    <tr id="settr_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cboitem_1", 240, $garments_item, "",1,"--Select--", 0, '',1,'' ); 
									?>
                                    </td>
                                     <td>
                                    <input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:80px" class="text_boxes_numeric" onChange="set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' )" readonly /> 
                                     </td>
                                </tr>
                    <? 
					} 
					?>
                </tbody>
                </table>
                <table width="350" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="250">Total</th>
                            <th>
                            <input type="text" id="tot_set_qnty" name="tot_set_qnty"  class="text_boxes_numeric" style="width:80px"  value="<? echo $tot_set_qnty; ?>" readonly  />
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <table width="350" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
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

/*
if($action=="set_po_qnty_ship_date")
{
	
	$data_cum_ins=sql_select("select po_break_down_id, sum(inspection_qnty) as inspection_qnty from  pro_buyer_inspection  where po_break_down_id=$data group by  po_break_down_id" );
	$cum_ins_arr=array();
	foreach($data_cum_ins as $row)
	{
		$cum_ins_arr[$row[csf('po_break_down_id')]]['cum_prev']=$row[csf('inspection_qnty')];
	}
	$data_array=sql_select("select id,po_quantity ,plan_cut,pub_shipment_date from  wo_po_break_down  where id=$data");
	foreach ($data_array as $row)
	{
		$cum_previour_qty=$cum_ins_arr[$row[csf('id')]]['cum_prev'];
		echo "document.getElementById('txt_po_quantity').value = '".$row[csf("po_quantity")]."';\n";  
		echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n";
		echo "document.getElementById('txt_cum_inspection_qnty').value = '".$cum_previour_qty."';\n"; 
		echo "$('#txt_cum_inspection_qnty').attr('disabled',true);\n";  
     }
}
*/



if($action=="set_po_qnty_ship_date")
{
	list($order_id,$week_id,$country_id,$inspection_level,$company_id,$update_id)=explode(",",$data);
	
	$preceding_process = return_field_value("preceding_page_id", "variable_settings_production", "company_name=$company_id and variable_list=33 and page_category_id=91", "preceding_page_id");
	$qty_source = 8;
	if ($preceding_process == 29) $qty_source = 5; //Sewing Output
	else if ($preceding_process == 30) $qty_source = 7; //Iron Output
	else if ($preceding_process == 31) $qty_source = 8; //Packing And Finishing
	else if ($preceding_process == 260) $qty_source = 82; //Finish gmts issue
	else if ($preceding_process == 277) $qty_source = 81; //Finish gmts rcv
	else if ($preceding_process == 276) $qty_source = 14; //Garments Finishing Delivery
	else if ($preceding_process == 103) $qty_source = 11; //Poly Entry
	else if ($preceding_process == 250) $qty_source = 80; //Woven Finishing Entry

	echo "document.getElementById('txt_inspection_qnty').value = '';\n";  
	echo "document.getElementById('txt_po_quantity').value = '';\n";
	echo "document.getElementById('txt_finishing_qnty').value = '';\n";  
	echo "document.getElementById('txt_pub_shipment_date').value = '';\n";
	echo "document.getElementById('txt_cum_inspection_qnty').value = '';\n"; 
	echo "document.getElementById('cbo_inspection_status').value = 0;\n";
	echo "document.getElementById('cbo_cause').value = 0;\n";
	
	if($order_id>0)
	{
		// echo "select sum(production_quantity) as production_quantity from pro_garments_production_mst where po_break_down_id=$order_id and production_type=$qty_source  $country_cond and status_active=1 and is_deleted=0";die;
		if($country_id>0) $country_cond=" and country_id=$country_id"; else $country_cond="";
		
		if(str_replace("'","",$update_id)!="") $update_cond=" and id<>$update_id"; else $update_cond="";
		$prev_ins_qnty=return_field_value("sum(inspection_qnty) as inspection_qnty","pro_buyer_inspection","po_break_down_id=$order_id  and inspection_status <> 3 and inspection_level=$inspection_level $country_cond $update_cond and status_active=1 and is_deleted=0","inspection_qnty");

		if ($inspection_level == 1) //In-line Inspection
		{
			$sewing_out_qty=return_field_value("sum(b.production_qnty) as production_qnty","pro_garments_production_mst a,pro_garments_production_dtls b","a.po_break_down_id=$order_id and b.mst_id=a.id and a.production_type=5  $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","production_qnty"); 
			$balance = $sewing_out_qty - $prev_ins_qnty;
			echo "document.getElementById('txt_finishing_qnty').value = '$balance';\n";
		}
		else
		{ 
			$finishing_quantity=return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=$order_id and production_type=$qty_source  $country_cond and status_active=1 and is_deleted=0","production_quantity");
			$finishing_quantity_tran_in=return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=$order_id and production_type=10  and trans_type=5 $country_cond and status_active=1 and is_deleted=0","production_quantity");
			$finishing_quantity_tran_out=return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=$order_id and production_type=10 and trans_type=6  $country_cond and status_active=1 and is_deleted=0","production_quantity"); 
			
			$cu_finish_qnty=($finishing_quantity+$finishing_quantity_tran_in)-($prev_ins_qnty+$finishing_quantity_tran_out);
			echo "document.getElementById('txt_finishing_qnty').value = '$cu_finish_qnty';\n";
		}
	}
	
	if($order_id && $week_id && $country_id)
	{
		$data_cum_ins=sql_select("select po_break_down_id,week_id,country_id,sum(inspection_qnty) as inspection_qnty from  pro_buyer_inspection  where po_break_down_id=$order_id and inspection_level=$inspection_level and inspection_status <> 3 and status_active=1 and is_deleted=0 group by  po_break_down_id,week_id,country_id" );
		$cum_ins_arr=array();
		foreach($data_cum_ins as $row)
		{
			$key=$row[csf('po_break_down_id')].$row[csf('week_id')].$row[csf('country_id')];
			$cum_ins_arr[$key]['cum_prev']=$row[csf('inspection_qnty')];
		}
		
		$data_array=sql_select("SELECT a.po_break_down_id,sum(a.order_quantity) as order_quantity ,a.country_ship_date,a.country_id from wo_po_color_size_breakdown a,week_of_year b,wo_po_break_down c where a.po_break_down_id=$order_id and b.week=$week_id and a.country_ship_date=b.week_date and a.country_id=$country_id and c.id=a.po_break_down_id group by a.po_break_down_id,a.country_ship_date,a.country_id"); // and inspection_level=$inspection_level
		foreach ($data_array as $row)
		{
			$key=$row[csf('po_break_down_id')].$week_id.$country_id;
			$cum_previour_qty=$cum_ins_arr[$key]['cum_prev'];
			echo "document.getElementById('txt_po_quantity').value = '".$row[csf("order_quantity")]."';\n";  
			echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("country_ship_date")], "dd-mm-yyyy", "-")."';\n";
			echo "document.getElementById('txt_cum_inspection_qnty').value = '".$cum_previour_qty."';\n"; 
			echo "$('#txt_cum_inspection_qnty').attr('disabled',true);\n"; 
		 }
	 
	}
	else if($order_id && $week_id && $country_id==0)
	{
		$data_cum_ins=sql_select("select po_break_down_id,week_id, sum(inspection_qnty) as inspection_qnty from  pro_buyer_inspection  where po_break_down_id=$order_id and inspection_level=$inspection_level and inspection_status <> 3 and status_active=1 and is_deleted=0 group by  po_break_down_id,week_id" );
		$cum_ins_arr=array();
		foreach($data_cum_ins as $row)
		{
			$key=$row[csf('po_break_down_id')].$row[csf('week_id')];
			$cum_ins_arr[$key]['cum_prev']=$row[csf('inspection_qnty')];
		}
		
		$data_array=sql_select("SELECT a.po_break_down_id,sum(a.order_quantity) as order_quantity ,c.pub_shipment_date from wo_po_color_size_breakdown a,week_of_year b,wo_po_break_down c where a.po_break_down_id=$order_id  and b.week=$week_id and a.country_ship_date=b.week_date and c.id=a.po_break_down_id group by a.po_break_down_id,c.pub_shipment_date"); // and inspection_level=$inspection_level
		foreach ($data_array as $row)
		{
			$key=$row[csf('po_break_down_id')].$week_id;
			$cum_previour_qty=$cum_ins_arr[$key]['cum_prev'];
			echo "document.getElementById('txt_po_quantity').value = '".$row[csf("order_quantity")]."';\n";  
			echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n";
			echo "document.getElementById('txt_cum_inspection_qnty').value = '".$cum_previour_qty."';\n"; 
			echo "$('#txt_cum_inspection_qnty').attr('disabled',true);\n"; 
		 }
	 
	}
	else if($order_id && $week_id==0 && $country_id)
	{
		$data_cum_ins=sql_select("SELECT po_break_down_id,country_id, sum(inspection_qnty) as inspection_qnty from  pro_buyer_inspection  where po_break_down_id=$order_id and inspection_level=$inspection_level and inspection_status <> 3 and status_active=1 and is_deleted=0 group by  po_break_down_id,country_id" );
		$cum_ins_arr=array();
		foreach($data_cum_ins as $row)
		{
			$key=$row[csf('po_break_down_id')].$row[csf('country_id')];
			$cum_ins_arr[$key]['cum_prev']=$row[csf('inspection_qnty')];
		}
		
		$data_array=sql_select("SELECT po_break_down_id,sum(b.order_quantity) as order_quantity ,b.country_ship_date,b.country_id from wo_po_break_down a,wo_po_color_size_breakdown b where a.id=b.po_break_down_id and b.po_break_down_id=$order_id  and b.country_id=$country_id group by b.po_break_down_id,b.country_ship_date,b.country_id"); // and inspection_level=$inspection_level   
		foreach ($data_array as $row)
		{
			$key=$row[csf('po_break_down_id')].$country_id;
			$cum_previour_qty=$cum_ins_arr[$key]['cum_prev'];
			echo "document.getElementById('txt_po_quantity').value = '".$row[csf("order_quantity")]."';\n";  
			echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("country_ship_date")], "dd-mm-yyyy", "-")."';\n";
			echo "document.getElementById('txt_cum_inspection_qnty').value = '".$cum_previour_qty."';\n"; 
			echo "$('#txt_cum_inspection_qnty').attr('disabled',true);\n"; 
		 }
	}
	else if($order_id && $week_id==0 && $country_id==0)
	{
		
		$data_cum_ins=sql_select("select po_break_down_id, sum(inspection_qnty) as inspection_qnty from  pro_buyer_inspection  where po_break_down_id=$order_id and inspection_level=$inspection_level and inspection_status <> 3 and status_active=1 and is_deleted=0 group by  po_break_down_id" );
		$cum_ins_arr=array();
		foreach($data_cum_ins as $row)
		{
			$cum_ins_arr[$row[csf('po_break_down_id')]]['cum_prev']=$row[csf('inspection_qnty')];
		}
		$data_array=sql_select("select id,po_quantity ,plan_cut,pub_shipment_date from  wo_po_break_down  where id=$order_id");
		foreach ($data_array as $row)
		{
			$cum_previour_qty=$cum_ins_arr[$row[csf('id')]]['cum_prev'];
			echo "document.getElementById('txt_po_quantity').value = '".$row[csf("po_quantity")]."';\n";  
			echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n";
			echo "document.getElementById('txt_cum_inspection_qnty').value = '".$cum_previour_qty."';\n"; 
			echo "$('#txt_cum_inspection_qnty').attr('disabled',true);\n";  
		 }
		
		
	}
	 
}




if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$order_id=str_replace("'","",$cbo_order_id);
	$country_id=str_replace("'","",$cbo_country_id);
	$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=91");
	$preceding_process=return_field_value("preceding_page_id","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=91");
	$qty_source = 8;
	if ($preceding_process == 29) $qty_source = 5; //Sewing Output
	else if ($preceding_process == 30) $qty_source = 7; //Iron Output
	else if ($preceding_process == 31) $qty_source = 8; //Packing And Finishing
	else if ($preceding_process == 260) $qty_source = 82; //Finish gmts issue
	else if ($preceding_process == 277) $qty_source = 81; //Finish gmts rcv
	else if ($preceding_process == 276) $qty_source = 14; //Garments Finishing Delivery
	else if ($preceding_process == 103) $qty_source = 11; //Poly Entry
	else if ($preceding_process == 250) $qty_source = 80; //Woven Finishing Entry
	// echo "10**".$qty_source; die;
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//----------Compare buyer inspection qty and ex-factory qty for validation----------------
		$txt_inspection_qnty=str_replace("'","",$txt_inspection_qnty);
		$cbo_inspection_status=str_replace("'","",$cbo_inspection_status);
		$cbo_inspection_level=str_replace("'","",$cbo_inspection_level);
		
		if($is_control==1 && $user_level!=2)
		{
			if($order_id>0 && $cbo_inspection_status==1)
			{
				if($country_id>0) $country_cond=" and country_id=$country_id"; else $country_cond="";
				$finishing_quantity=return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=$order_id and production_type=$qty_source  $country_cond and status_active=1 and is_deleted=0","production_quantity");
				$prev_ins_qnty=return_field_value("sum(inspection_qnty) as inspection_qnty","pro_buyer_inspection","po_break_down_id=$order_id  $country_cond and inspection_level=$cbo_inspection_level and inspection_status=1 and status_active=1 and is_deleted=0","inspection_qnty");
				$cu_finish_qnty=$finishing_quantity-$prev_ins_qnty;
				$insfec_qnty=str_replace("'","",$txt_inspection_qnty);
				if($insfec_qnty>$cu_finish_qnty)
				{
					echo "35**Inspection Not Over Finishing Quantity";
					disconnect($con);die;
				}
				
			}
		}
		
		
		if($is_control==1 && $user_level!=2)
		{
			
			if($cbo_country_id>0)
			{
				$country_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$cbo_order_id and country_id=$cbo_country_id and inspection_level=$cbo_inspection_level and inspection_status=1 and status_active=1 and is_deleted=0");
				$country_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$cbo_order_id and production_type=$qty_source and country_id=$cbo_country_id and status_active=1 and is_deleted=0");
				$tot_ins_qnty=$country_insfection_qty+$txt_inspection_qnty;
				if($country_finishing_qty < $tot_ins_qnty && $cbo_inspection_status==1)
				{
					echo "25**0";
					disconnect($con);
					die;
				}
			}
			else
			{
				$order_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$cbo_order_id and inspection_level=$cbo_inspection_level and inspection_status=1 and status_active=1 and is_deleted=0");
				$order_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$cbo_order_id and production_type=$qty_source and status_active=1 and is_deleted=0");
				$tot_ins_qnty=$order_insfection_qty+$txt_inspection_qnty;
				if($order_finishing_qty < $tot_ins_qnty && $cbo_inspection_status==1)
				{
					echo "25**0";
					disconnect($con);
					die;
				}
			}
		
		}//--------------------------------------------------------------Compare end;

		$id=return_next_id( "id", "pro_buyer_inspection", 1 ) ;
		$id_dtls=return_next_id( "id", "pro_buyer_inspection_breakdown", 1 ) ;
		$hidden_ins_data2=str_replace("'","",$hidden_ins_data);
		$field_array_dtls=" id,mst_id, item_id, color_id,size_id, color_qty, ins_qty,shipped_qty, inserted_by, insert_date,  status_active, is_deleted";
		$dtls_data=explode("----", $hidden_ins_data2);
		$data_array_dtls="";
		foreach($dtls_data as $data_val)
		{
			$val=explode("_", $data_val);
			if ($data_array_dtls) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$id.",'".$val[0]."','".$val[1]."','".$val[4]."','".$val[2]."','".$val[3]."','".$val[5]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls++;

		}
		$field_array="id,job_no,ins_reason,all_data,po_break_down_id,actual_po_id,inspection_company,source, working_company,working_location, working_floor,inspected_by,week_id,country_id,inspection_date,inspection_qnty,inspection_status,inspection_level,inspection_cause,comments,entry_form,inserted_by,insert_date";
		$data_array="(".$id.",".$txt_job_no.",".$txt_ins_reason.",".$hidden_ins_data.",".$cbo_order_id.",".$cbo_actual_order_id.",".$cbo_inspection_company.",".$cbo_source.",".$cbo_working_company.",".$cbo_working_location.",".$cbo_working_floor.",".$cbo_inspection_by.",".$cbo_week_no.",".$cbo_country_id.",".$txt_inp_date.",".$txt_inspection_qnty.",".$cbo_inspection_status.",".$cbo_inspection_level.",".$cbo_cause.",".$txt_comments.",470,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
 		$rID=sql_insert("pro_buyer_inspection",$field_array,$data_array,0);
 		$rID_dtls=sql_insert("pro_buyer_inspection_breakdown",$field_array_dtls,$data_array_dtls,0);

		if($db_type==0)
		{
			if($rID && $rID_dtls)
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
		if($rID && $rID_dtls)
			{
				oci_commit($con);
				echo "0**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//----------Compare buyer inspection qty and ex-factory qty for validation----------------
		$txt_inspection_qnty=str_replace("'","",$txt_inspection_qnty);
		$cbo_inspection_status=str_replace("'","",$cbo_inspection_status);
		$cbo_inspection_level=str_replace("'","",$cbo_inspection_level);
		
		if($is_control==1 && $user_level!=2)
		{
			if($order_id>0 && $cbo_inspection_status==1)
			{
				if($country_id>0) $country_cond=" and country_id=$country_id"; else $country_cond="";
				$finishing_quantity=return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=$order_id and production_type=$qty_source  $country_cond and status_active=1 and is_deleted=0","production_quantity");
				$prev_ins_qnty=return_field_value("sum(inspection_qnty) as inspection_qnty","pro_buyer_inspection","po_break_down_id=$order_id and id<>$txt_mst_id and inspection_level=$cbo_inspection_level $country_cond and status_active=1 and is_deleted=0","inspection_qnty");
				$cu_finish_qnty=$finishing_quantity-$prev_ins_qnty;
				$insfec_qnty=str_replace("'","",$txt_inspection_qnty);
				if($insfec_qnty>$cu_finish_qnty)
				{
					echo "35**Inspection Not Over Finishing Quantity";
					disconnect($con);die;
				}
				
			}
		}
		
		if($is_control==1 && $user_level!=2)
		{
			
			if($cbo_country_id)
			{
				$country_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$cbo_order_id and country_id=$cbo_country_id and inspection_level=$cbo_inspection_level and inspection_status=1 and status_active=1 and is_deleted=0 and id<>$txt_mst_id");
				$country_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$cbo_order_id and production_type=$qty_source and country_id=$cbo_country_id and status_active=1 and is_deleted=0");
			
				if($country_finishing_qty < $country_insfection_qty+$txt_inspection_qnty && $cbo_inspection_status==1)
				{
					echo "25**0";
					disconnect($con);
					die;
				}
			}
			else
			{
				$order_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$cbo_order_id and inspection_level=$cbo_inspection_level and inspection_status=1 and status_active=1 and is_deleted=0 and id<>$txt_mst_id");
				$order_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$cbo_order_id and production_type=$qty_source and status_active=1 and is_deleted=0");
				if($order_finishing_qty < $order_insfection_qty+$txt_inspection_qnty && $cbo_inspection_status==1)
				{
					echo "25**0";
					disconnect($con);
					die;
				}
			}
		
		}//--------------------------------------------------------------Compare end;
								
		$field_array="job_no*ins_reason*all_data*po_break_down_id*actual_po_id*inspection_company*source*working_company*working_location*working_floor*inspected_by*week_id*country_id*inspection_date*inspection_qnty*inspection_status*inspection_level*inspection_cause*comments*updated_by*update_date";
		$data_array="".$txt_job_no."*".$txt_ins_reason."*".$hidden_ins_data."*".$cbo_order_id."*".$cbo_actual_order_id."*".$cbo_inspection_company."*".$cbo_source."*".$cbo_working_company."*".$cbo_working_location."*".$cbo_working_floor."*".$cbo_inspection_by."*".$cbo_week_no."*".$cbo_country_id."*".$txt_inp_date."*".$txt_inspection_qnty."*".$cbo_inspection_status."*".$cbo_inspection_level."*".$cbo_cause."*".$txt_comments."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("pro_buyer_inspection",$field_array,$data_array,"id","".$txt_mst_id."",1);
		$id_dtls=return_next_id( "id", "pro_buyer_inspection_breakdown", 1 ) ;
		$delete_dtls=execute_query( "delete from pro_buyer_inspection_breakdown where mst_id=$txt_mst_id",0);

		
		$hidden_ins_data2=str_replace("'","",$hidden_ins_data);
		$field_array_dtls=" id,mst_id, item_id, color_id,size_id, color_qty, ins_qty,shipped_qty, inserted_by, insert_date,  status_active, is_deleted";
		$dtls_data=explode("----", $hidden_ins_data2);
		$data_array_dtls="";
		foreach($dtls_data as $data_val)
		{
			$val=explode("_", $data_val);
			if ($data_array_dtls) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$txt_mst_id.",'".$val[0]."','".$val[1]."','".$val[4]."','".$val[2]."','".$val[3]."','".$val[5]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls++;

		}

		$rID_dtls=sql_insert("pro_buyer_inspection_breakdown",$field_array_dtls,$data_array_dtls,0);

		if($db_type==0)
		{
			if($rID && $delete_dtls && $rID_dtls)
			{
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $delete_dtls && $rID_dtls )
			{
				oci_commit($con);
				echo "1**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		//echo $txt_mst_id;
		$rID=sql_delete("pro_buyer_inspection",$field_array,$data_array,"id","".$txt_mst_id."",1);
		//echo "2**".$rID;
		$delete_dtls=execute_query( "delete from pro_buyer_inspection_breakdown where mst_id=$txt_mst_id",0);
		if($db_type==0)
		{
			if($rID && $delete_dtls )
			{
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $delete_dtls  )
			{
				oci_commit($con);
				echo "2**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);die;

	}
}
if($action=="show_active_listview")
{

	
	//$insp_company_library=return_library_array( "select distinct a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.id=b.supplier_id and  b.party_type=41 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name", "id", "supplier_name"  );

	
	$inspection_type=return_field_value("inspected_by","pro_buyer_inspection","job_no='$data' and status_active=1 and is_deleted=0");
	
	
	//$arr=array (0=>$po_number,1=>$insp_company_library,4=>$inspection_status,5=>$inspection_cause);
 	$sql= "SELECT actual_po_id, id,po_break_down_id,inspected_by,inspection_company,inspection_date,inspection_qnty,inspection_status,inspection_level,inspection_cause,comments from  pro_buyer_inspection  where   status_active=1 and is_deleted=0 and job_no='$data'"; 

 	$actual_po_arr=return_library_array( "SELECT id,acc_po_no from wo_po_acc_po_info where status_active =1 and job_no='$data'", "id", "acc_po_no"  );


	 
	 
	//echo  create_list_view("list_view", "PO No,Inspection Company,Inspection Date,Inspection Qnty,Inspection Status,Inspection Cause, Comments", "120,120,100,80,80,80,150","800","220",0, $sql , "get_php_form_data", "id", "'populate_inspection_details_form_data'", 1, "po_break_down_id,inspection_company,0,0,inspection_status,inspection_cause,0", $arr , "po_break_down_id,inspection_company,inspection_date,inspection_qnty,inspection_status,inspection_cause,comments", "requires/buyer_inspection_controller",'','0,0,3,1,0,0,0') ;


	$data_array=sql_select($sql);
	?>
	 
	<table width="1020" class="rpt_table" border="1" rules="all" align="left">
	    <thead>
	        <th width="35">Sl</th>
	        <th width="120">PO No</th>
	        <th width="100">Actual Po</th>
	        <th width="120">Inspection Company</th>
	        <th width="100">Inspection Date</th>
	        <th width="100">Inspection Qnty</th>
	        <th width="80">Inspection Status</th>
	        <th width="120">Inspection Level</th>
	        <th width="80">Inspection Cause</th>
	        <th width="150">Comments</th>
	    </thead>
	 </table>
	  

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table" id="tbl_list_search" align="left">

	<?
	$i=1;
	foreach($data_array as $row){

		if($row[csf('inspected_by')]==1)
		{
		$insp_company_library=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name", "id", "buyer_name" );
		}
		else if($row[csf('inspected_by')]==2)
		{
		$insp_company_library=return_library_array( "select distinct a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.id=b.supplier_id and  b.party_type=41 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name", "id", "supplier_name" );
		}
		else
		{
			$insp_company_library=$company_library;     	 
		}

		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ; 


	?>
	
			<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>','populate_inspection_details_form_data','requires/buyer_inspection_controller')" style="cursor:pointer;">
				<td width="35"><? echo $i;?></td>
				<td width="120"><? echo $po_number[$row[csf('po_break_down_id')]];?></td>
				<td width="100"><? echo $actual_po_arr[$row[csf('actual_po_id')]];?></td>
				<td width="120"><? echo $insp_company_library[$row[csf('inspection_company')]];?></td>
				<td width="100"><? echo change_date_format($row[csf('inspection_date')]);?></td>
				<td width="100" align="right"><? echo $row[csf('inspection_qnty')];?></td>
				<td width="80"><? echo $inspection_status[$row[csf('inspection_status')]];?></td>
				<td width="120"><? echo $inpLevelArray[$row[csf('inspection_level')]];?></td>
				<td width="80"><? echo $inspection_cause[$row[csf('inspection_cause')]];?></td>
				<td><? echo $row[csf('comments')];?></td>
			</tr>
			<?
			$i++;
		}

		?>

		</table>
	 

	<?


}

if($action=="populate_inspection_details_form_data")
{	
	$data_array=sql_select("SELECT b.po_number, a.actual_po_id, working_floor, source, working_company, working_location,all_data,ins_reason, a.po_break_down_id,a.inspection_company,a.inspected_by,a.week_id,a.country_id,a.comments,a.inspection_date,a.inspection_qnty,a.inspection_status,a.inspection_level,a.inspection_cause,a.comments,a.id,b.po_quantity ,b.plan_cut,b.pub_shipment_date from  pro_buyer_inspection a, wo_po_break_down b  where a.po_break_down_id=b.id and a.id =$data"); 
	foreach ($data_array as $row)
	{
		$source=$row[csf("source")];
		// $working_location="'".$row[csf("working_company")].'**'.$row[csf("working_location")]."'";
		$working_location=$row[csf("working_location")];
		$working_company=$row[csf("working_company")];
		$po_id = $row[csf("po_break_down_id")];
		$inspected_by = $row[csf("inspected_by")];

		echo "load_drop_down_multiple( 'requires/buyer_inspection_controller',".$po_id."+'_'+" . $working_company . "+'_'+" . $working_location. "+'_'+" . $source. "+'_'+" . $inspected_by . "+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_all_drop_down', 'week_drop_down_td*country_drop_down_td*working_company_td*working_location_td*working_floor_td*cutt_company_td' );\n";

		// echo "load_drop_down( 'requires/buyer_inspection_controller', '__".$row[csf("po_break_down_id")]."', 'load_drop_down_week_no', 'week_drop_down_td' );";
		// echo "load_drop_down( 'requires/buyer_inspection_controller', '__".$row[csf("po_break_down_id")]."', 'load_drop_down_country_id', 'country_drop_down_td' );";
		 
		//  echo "load_drop_down( 'requires/buyer_inspection_controller', $source, 'load_drop_working_company', 'working_company_td' );";
		//  echo "load_drop_down( 'requires/buyer_inspection_controller', $working_company, 'load_drop_down_working_location', 'working_location_td' );";
		//  echo "load_drop_down( 'requires/buyer_inspection_controller', $working_location, 'load_drop_down_working_floor', 'working_floor_td' );";

		 echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n"; 
		 echo "document.getElementById('cbo_working_company').value = '".$row[csf("working_company")]."';\n"; 

		echo "document.getElementById('cbo_working_location').value = '".$row[csf("working_location")]."';\n"; 
		echo "document.getElementById('cbo_working_floor').value = '".$row[csf("working_floor")]."';\n"; 

		//  echo "load_drop_down( 'requires/buyer_inspection_controller',".$row[csf("inspected_by")]."+','+document.getElementById('cbo_buyer_name').value+','+document.getElementById('cbo_company_name').value, 'load_drop_down_buyer_party_company', 'cutt_company_td' );";
		
	 	echo "get_php_form_data('".$row[csf("po_break_down_id")].",".$row[csf("week_id")].",".$row[csf("country_id")].",".$row[csf("inspection_level")].",".$row[csf("working_company")].",".$row[csf("id")]."','set_po_qnty_ship_date', 'requires/buyer_inspection_controller');\n";
		
		
		echo "document.getElementById('cbo_inspection_company').value = '".$row[csf("inspection_company")]."';\n";  
		echo "document.getElementById('txt_inp_date').value = '".change_date_format($row[csf("inspection_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('cbo_order_id').value = '".$row[csf("po_break_down_id")]."';\n";  
		echo "document.getElementById('cbo_order_val').value = '".$row[csf("po_number")]."';\n";  
		echo "document.getElementById('cbo_actual_order_id').value = '".$row[csf("actual_po_id")]."';\n";  
		//echo "document.getElementById('txt_po_quantity').value = '".$row[csf("po_quantity")]."';\n";  
		echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n"; 
	    echo "document.getElementById('txt_ins_reason').value = '".$row[csf("ins_reason")]."';\n"; 
	    echo "document.getElementById('hidden_ins_data').value = '".$row[csf("all_data")]."';\n"; 
	    echo "document.getElementById('txt_inspection_qnty').value = '".$row[csf("inspection_qnty")]."';\n"; 
		echo "document.getElementById('cbo_inspection_status').value = '".$row[csf("inspection_status")]."';\n";  
		echo "document.getElementById('cbo_inspection_level').value = '".$row[csf("inspection_level")]."';\n";  
		echo "document.getElementById('cbo_cause').value = '".$row[csf("inspection_cause")]."';\n"; 
		echo "document.getElementById('txt_comments').value = '".$row[csf("comments")]."';\n"; 
		echo "document.getElementById('txt_mst_id').value = '".$row[csf("id")]."';\n";  
		
		echo "document.getElementById('cbo_inspection_by').value = '".$row[csf("inspected_by")]."';\n";  
		echo "document.getElementById('cbo_week_no').value = '".$row[csf("week_id")]."';\n";  
		echo "document.getElementById('cbo_country_id').value = '".$row[csf("country_id")]."';\n";  
		
		//echo "document.getElementById('txt_cum_inspection_qnty').value = '';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_buyer_inspection_entry',1);\n";  

     
	 
	 }
}

if($action=="show_image")
{
	echo load_html_head_contents("Buyer Inspection Image","../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        { 
        ?>
        <td><a href="<? $row[csf('image_location')] ?>" target="_new"><img src='../../<? echo $row[csf('image_location')]; ?>' height='350' width='900' align="middle" /></a></td>
        <?
        }
        ?>
        </tr>
    </table>
    <?
}
?>
