<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$inpLevelArray = array(1=>'Pre-Final',2=>'Final');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



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
 			echo create_drop_down( "cbo_working_company", 210, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select Working Company ---", $selected, "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', 0, 'load_drop_down_working_location', 'working_location_td' );",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_working_company", 210, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Working Company--", $selected, "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', 0, 'load_drop_down_working_location', 'working_location_td' );" );
		}
	}
 	else if($data==1)
 	{
  		echo create_drop_down( "cbo_working_company", 210, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Working Company --", '', "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', this.value, 'load_drop_down_working_location', 'working_location_td' );",0 );
 	}
 	else
 		echo create_drop_down( "cbo_working_company", 210, $blank_array,"", 1, "--- Select Working Company ---", $selected, "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', 0, 'load_drop_down_working_location', 'working_location_td' );",0,0 );
 	exit();
}

if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 210, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Working Location --", $selected, "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', $data+'**'+this.value, 'load_drop_down_working_floor', 'working_floor_td' );" );
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
	echo create_drop_down( "cbo_order_id",100, "select id,po_number from wo_po_break_down where job_no_mst='$data' and status_active in(1,2,3) and is_deleted=0","id,po_number", 1, "--Select--", "", "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', '__'+this.value, 'load_drop_down_country_id', 'country_drop_down_td' );get_php_form_data( this.value+','+document.getElementById('cbo_week_no').value+','+document.getElementById('cbo_country_id').value+','+document.getElementById('cbo_inspection_level').value, 'set_po_qnty_ship_date', 'requires/inspection_for_actual_po_entry_controller');","","","","","","" );
}




if ($action=="load_drop_down_country_id")
{ 
	list($job,$po_id,$week)=explode('__',$data);
	if($po_id)$con="b.po_break_down_id=$po_id"; else $con="b.job_no_mst='$job'";
	
	if($week)$con_week=" and c.week=$week"; else $con_week="";
	
	//echo create_drop_down( "cbo_country_id",100, "select a.id,a.country_name from lib_country a,wo_po_color_size_breakdown b where $con and a.id=b.country_id group by a.id,a.country_name order by a.country_name","id,country_name", 1, "--Select--", "", "get_php_form_data( document.getElementById('cbo_order_id').value+','+document.getElementById('cbo_week_no').value+','+this.value, 'set_po_qnty_ship_date', 'requires/inspection_for_actual_po_entry_controller')","","","","","","" );
	echo create_drop_down( "cbo_country_id",100, "SELECT a.id,a.country_name from lib_country a inner join wo_po_color_size_breakdown b on a.id=b.country_id left join week_of_year c on c.week_date=b.country_ship_date $con_week  where $con  and b.status_active=1 and b.is_deleted=0 group by a.id,a.country_name order by a.country_name","id,country_name", 1, "--Select--", "", "get_php_form_data( document.getElementById('cbo_order_id').value+','+document.getElementById('cbo_week_no').value+','+this.value+','+document.getElementById('cbo_inspection_level').value, 'set_po_qnty_ship_date', 'requires/inspection_for_actual_po_entry_controller')","","","","","","" );
}



if ($action=="load_drop_down_buyer_party_company")
{
	$data=str_replace("'","",$data);
	list($inspection_type,$buyer,$company)=explode(',',trim($data));
	$company_library=return_library_array( "select id,company_name from lib_company where status_active =1 and  is_deleted=0", "id", "company_name"  );
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
		echo create_drop_down( "cbo_inspection_company", 210, $company_library,"", 1, "--- Select---", $selected, "" );     	 
	}

}

if ($action=="order_search_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
									echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'inspection_for_actual_po_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
	                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_order').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('txt_file').value+'_'+document.getElementById('txt_inter_ref').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('cbo_year_selection').value, 'create_po_search_list_view', 'search_div', 'inspection_for_actual_po_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}
if($action=="open_order_popup")
{

	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
		echo  create_list_view("list_view", "Order No.,Order Qty,Insp Qty,Balance", "150,140,140,150","590","350",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,0,0", $arr , "po_number,po_quantity,insp_qty,balance", "requires/inspection_for_actual_po_entry_controller",'setFilterGrid("list_view",-1);','','');
	 
} 



if ($action=="open_actual_order_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);

  ?>
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function check_all_data()
	{
		var row_num=$('#list_view tr').length-1;
		for(var i=1;  i<=row_num;  i++)
		{
			if($("#tr_"+i).css("display") != "none")
			{
				$("#tr_"+i).click();
			}
		}
	}

	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];

		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	}

	</script>
     <input type="hidden" id="txt_po_id" />
     <input type="hidden" id="txt_po_val" />
	 
     <?
	// echo $data[0];
	 if ($data[0]==0) $company_name=""; else $company_name=" and a.company_name='$data[0]'";
	 if ($data[1]==0) $buyer_name=""; else $buyer_name=" and a.buyer_name='$data[1]'";
	 if ($data[2]=="") $job_num=""; else $job_num=" and a.job_no_prefix_num='$data[2]'";
	if($db_type==0)
	{
		if(str_replace("'","",$data[3])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[3]).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$data[3])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[3]).""; else $year_cond="";
	}

	$order_type=str_replace("'","",$data[4]);
	
	// echo  $sql;die;
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

	/* $sql= "SELECT a.id as actual_id,a.acc_po_no,a.acc_ship_date ,a.acc_po_qty,b.country_id,sum (b.po_qty) as po_qty,listagg( b.id,', ' on overflow truncate with count)     
	within group (order by b.id) dtls_id  	
		from wo_po_acc_po_info a,wo_po_acc_po_info_dtls b where a.id=b.mst_id and   a.status_active=1  and a.is_deleted=0 and a.job_no='$job_no' $country_id_cond group by a.id,a.acc_po_no,a.acc_ship_date ,a.acc_po_qty,b.country_id";

		$data_result=sql_select($sql);
		foreach($data_result as $row){
			$po_qty_arr[$row[csf('actual_id')]][$row[csf('country_id')]]['qnty']+=$row[csf('po_qty')];
			$po_qty_arr[$row[csf('actual_id')]][$row[csf('country_id')]]['dtls_id']=$row[csf('dtls_id')];

		} */

	$sql= "SELECT a.id as actual_id,b.country_id,b.id as dtls_id , c.prod_qty
	from wo_po_acc_po_info a,wo_po_acc_po_info_dtls b,PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS c where a.id=b.mst_id and b.id=c.actual_po_dtls_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0 and c.production_type=8 and a.job_no='$job_no' $country_id_cond ";
	// echo $sql;
	$data_result=sql_select($sql);
	$k=0;
	foreach($data_result as $row)
	{
		$po_qty_arr[$row[csf('actual_id')]][$row[csf('country_id')]]['qnty']+=$row[csf('prod_qty')];
		if($k==0)
		{
			$po_qty_arr[$row[csf('actual_id')]][$row[csf('country_id')]]['dtls_id'] = $row[csf('dtls_id')];
		}
		else
		{
			$po_qty_arr[$row[csf('actual_id')]][$row[csf('country_id')]]['dtls_id'].=",".$row[csf('dtls_id')];
		}
		$k++;
	}

	$cuml_data=sql_select("SELECT sum(b.current_inspection_qnty) as cuml_insp,b.actual_po_id,b.country_id 
	from pro_buyer_inspection a,pro_buyer_inspection_dtls b where a.id=b.mst_id and b.inspection_status=1 and a.inspection_level=$inspection_level and b.status_active=1 and a.job_no='$job_no' group by b.actual_po_id,b.country_id");
	$cuml_data_arr = array();

	$dtls_id="";
	$p=1;
	foreach($cuml_data as $row)
	{
		$cuml_data_arr[$row[csf('actual_po_id')]][$row[csf('country_id')]]['qty'] += $row['CUML_INSP'];
		/* $po_qty=$po_qty_arr[$row[csf('actual_po_id')]][$row[csf('country_id')]]['qnty'];
		if($row[csf('cuml_insp')]==$po_qty)
		{

			if($p==1){
				$dtls_id .=$po_qty_arr[$row[csf('actual_po_id')]][$row[csf('country_id')]]['dtls_id'];
				$p++;
			}else{
				$dtls_id .=",".$po_qty_arr[$row[csf('actual_po_id')]][$row[csf('country_id')]]['dtls_id'];
			}
		} */

	}

	foreach ($po_qty_arr as $ac_po_id => $ac_data) 
	{
		foreach ($ac_data as $country_id => $v) 
		{
			$cuml_qty = $cuml_data_arr[$ac_po_id][$country_id]['qty'];
			if($cuml_qty >= $v['qnty'])
			{
				if($p==1){
					$dtls_id .=$v['dtls_id'];
					$p++;
				}else{
					$dtls_id .=",".$v['dtls_id'];
				}
			}
		}
	}

	// echo $dtls_id;

	
	
		if($dtls_id !=""){
			$dtls_id_cond=" and b.id not in ($dtls_id)";
		}else{$country_id_cond="";$po_id_cond="";}

	$arr=array(2=>$country_arr);

	if($inspection_level==2)
	{
		$sql = "SELECT c.ACTUAL_PO_ID from WO_PO_BREAK_DOWN b, PRO_GARMENTS_PRODUCTION_MST a, PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS c where b.id=a.po_break_down_id and a.id=c.mst_id and c.status_active=1 and b.job_no_mst='$job_no' and a.IS_PO_OK=1";
		$res = sql_select($sql);
		$ok_po_id_arr = array();
		foreach ($res as $v) 
		{
			$ok_po_id_arr[$v['ACTUAL_PO_ID']] = $v['ACTUAL_PO_ID'];
		}

		$po_id_cond = where_con_using_array($ok_po_id_arr,0,"a.id");
		?>
		<span style="color: red;font-size:16px;">Final inspection data will come when <i><b>Is PO Ok</b></i> option <i><b>Yes</b></i> from Finishing Entry Page.</span>
		<?
	}

	$sql= "SELECT a.id as actual_id,a.acc_po_no,a.acc_ship_date ,a.acc_po_qty,b.country_id,sum (b.po_qty) as po_qty,listagg( b.id,', ' on overflow truncate with count)     
	within group (order by b.id) dtls_id  	
		from wo_po_acc_po_info a,wo_po_acc_po_info_dtls b where a.id=b.mst_id and   a.status_active=1  and a.is_deleted=0 and a.job_no='$job_no' $po_id_cond  $dtls_id_cond group by a.id,a.acc_po_no,a.acc_ship_date ,a.acc_po_qty,b.country_id";
		//echo $sql;
		

	echo  create_list_view("list_view", "Actual Order No,Ship Date,County,Po Qnty", "100,100,100,80","450","360",0, $sql , "js_set_value", "dtls_id,acc_po_no", "", 1, "0,0,country_id,0", $arr , "acc_po_no,acc_ship_date,country_id,po_qty", "",'setFilterGrid("list_view",-1);','0,0,0,0,3,0','',1) ;

	
	
	exit();
}
if ($action=="actual_po_list_view")
{
	$dataArr=explode("_",$data);
	$po_no=$dataArr[0];
	$po_id=$dataArr[1];
	$inspection_level=$dataArr[2];
	
	$cuml_data=sql_select("SELECT b.current_inspection_qnty as cuml_insp,b.actual_po_id,b.country_id  from pro_buyer_inspection a,pro_buyer_inspection_dtls b where a.id=b.mst_id and b.inspection_status=1 and a.inspection_level=$inspection_level and b.status_active=1");


	foreach($cuml_data as $row)
	{
		$cuml_data_arr[$row[csf('actual_po_id')]][$row[csf('country_id')]]['qnty']+=$row[csf('cuml_insp')];
	}

	$fin_fab_data=sql_select("SELECT a.actual_po_dtls_id as id, sum(case when  b.production_type=8 then a.prod_qty else 0 end) as qty, sum(case when  b.production_type=10 and b.trans_type=5 then a.prod_qty else 0 end) - sum(case when  b.production_type=10 and b.trans_type=6 then a.prod_qty else 0 end) as trans_qty,a.actual_po_id
	from pro_garments_prod_actual_po_details a, pro_garments_production_mst b 
	where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	 and b.production_type in(8,10) and  a.actual_po_dtls_id in ($po_id)   group by a.actual_po_dtls_id,a.actual_po_id ");
	 
	

	foreach($fin_fab_data as $row){

		$po_wise_data[$row[csf('actual_po_id')]]['fin_qnty'] += $row[csf('qty')] + $row[csf('trans_qty')];

	}
	if($inspection_level==1)
	{
		// $inspection_status = array_diff($inspection_status,[1]);
		$arr_index = "2,3,4,5";
	}

	$country_arr=return_library_array( "select id,country_name from lib_country where status_active=1 order by id", "id", "country_name"  );
		$sql="SELECT a.id as actual_id,a.acc_po_no,a.acc_ship_date ,a.acc_po_qty,b.country_id,sum(b.po_qty) as po_qty
		from wo_po_acc_po_info a,wo_po_acc_po_info_dtls b where a.id=b.mst_id and   a.status_active=1  and a.is_deleted=0 and b.id in ($po_id) group by a.id,a.acc_po_no,a.acc_ship_date ,a.acc_po_qty,b.country_id";
	
		$sql_data=sql_select($sql);

	?>
   		 <div width="1100px">
			<table cellpadding="0" cellspacing="1" width="100%" class="rpt_table" rules="all" id="table_list_view">
                        <thead>
                          
                            <th width="100">Actual PO</th>
							<th width="90">PO Ship Date </th>
							<th width="80" >Country</th>
							<th width="70">PO Qty</th>
							<th width="80">Finishing Qty</th>
							<th width="80">Cuml. Insp. Qty</th>
							<th width="80">Current<br> Inspec. Qty</th>
                            <th width="80">Minor qty</th>
                            <th width="80">Major qty</th>
							<th width="80">Critical Qty</th>
							<th width="80">Acceptable qty</th>
							<th width="80">Inspec. Status</th>
                            <th width="80">Comments </th>
                        </thead>
						<body>

						<?
						$i=1;$style="";
						foreach($sql_data as $row){
							$cuml_qnty=$cuml_data_arr[$row[csf('actual_id')]][$row[csf('country_id')]]['qnty'];
							$bal_qnty=$po_wise_data[$row[csf('actual_id')]]['fin_qnty']-$cuml_qnty;
							if($cuml_qnty > $row[csf('po_qty')]){

								$style="style='background-color:red;width:80px' disabled";

							}else{
								$style="style='width:80px'";
							}
							?>
                        <tr id="tr_<?=$i;?>">
						
                            <td title="<?=$row[csf('actual_id')];?>"><?  //echo create_drop_down( "cbo_order_id",100, $blank_array, 0, "", $selected, "" ); ?>
                               <input name="actual_order_no_<?=$i;?>" id="actual_order_no_<?=$i;?>"  class="text_boxes" type="text" readonly  style="width:80px"   value="<?=$row[csf('acc_po_no')];?>"/>
                               <input type="hidden" name="actual_order_id_<?=$i;?>" id="actual_order_id_<?=$i;?>" value="<?=$row[csf('actual_id')];?>">
                            </td>
							<td ><input name="txt_shipment_date_<?=$i;?>" id="txt_shipment_date_<?=$i;?>" class="datepicker" type="text" value="<?=change_date_format($row[csf('acc_ship_date')]);?>" style="width:70px;" disabled  /></td>
							<td title="<?=$row[csf('country_id')];?>">
							<? echo create_drop_down( "cbo_country_id_".$i, 80, $country_arr,"", 1, "--- Select ---", $row[csf('country_id')], "" ,1)
							?></td> 
                            <td ><input name="txt_po_quantity_<?=$i;?>" id="txt_po_quantity_<?=$i;?>"  class="text_boxes_numeric" type="text"  style="width:70px"  disabled value="<?=$row[csf('po_qty')];?>"/></td>
							<td ><input name="txt_finishing_qnty_<?=$i;?>" id="txt_finishing_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"  style="width:80px" value="<?=$po_wise_data[$row[csf('actual_id')]]['fin_qnty'];?>" readonly/></td>
							<td ><input name="txt_cuml_insp_qnty_<?=$i;?>" id="txt_cuml_insp_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text" value="<?=$cuml_qnty;?>"  style="width:80px" readonly /></td>
							<td >
                            	 <input name="txt_insp_qnty_<?=$i;?>"  id="txt_insp_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"  value=""  <?=$style;?> placeholder="<?=$bal_qnty;?>" />
							</td>

                            <td><input name="txt_minor_qnty_<?=$i;?>"  id="txt_minor_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"   style="width:80px"   /></td>
                      
                            <td><input name="txt_major_qnty_<?=$i;?>"  id="txt_major_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"   style="width:80px"   /></td>
							<td><input name="txt_critical_qnty_<?=$i;?>"  id="txt_critical_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"   style="width:80px"   /></td>
                      
					        <td><input name="txt_acceptable_qnty_<?=$i;?>"  id="txt_acceptable_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"   style="width:80px"   /></td>
							<td><?  echo create_drop_down( "cbo_insp_status_".$i,80, $inspection_status,"", 1, "-- Select --", $selected, "","",$arr_index ); //change_cause_validation( this.value ) ?></td>
                            <td ><input name="txt_comment_<?=$i;?>" id="txt_comment_<?=$i;?>"  class="text_boxes" type="text"  style="width:100px"  />
							<input name="txt_bal_qnty_<?=$i;?>" id="txt_bal_qnty_<?=$i;?>"  class="text_boxes" type="hidden" value="<?=$bal_qnty;?>"   />
							<input name="txt_prev_qnty_<?=$i;?>" id="txt_prev_qnty_<?=$i;?>"  class="text_boxes" type="hidden" value=""   />
							<input name="update_dtls_id_<?=$i;?>" id="update_dtls_id_<?=$i;?>"  class="text_boxes" type="hidden"  style="width:80px"  value=""/>
						</td>

                        </tr>
                       
					<?$i++;}?>
					</body>
                </table>
         </div>
            
    <?

	exit();
}
if ($action=="actual_po_update_list_view")
{

		$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

		list($mst_id,$dtls_id,$country_id,$inspection_level,$actual_po_id)=explode("_",$data);

		$sql="select id,mst_id,job_no,actual_po_no,actual_po_id,actual_po_ship_date,actual_po_qnty,country_id,finishing_qnty,cuml_inspection_qnty,current_inspection_qnty,minor_qnty,major_qnty,critical_qnty,acceptable_qnty,inspection_status,comments from pro_buyer_inspection_dtls where id=$dtls_id";
		//echo $sql;

		$cuml_qnty=sql_select("select sum(b.current_inspection_qnty) as cuml_insp  from pro_buyer_inspection a,pro_buyer_inspection_dtls b where a.id=b.mst_id and 
		b.actual_po_id=$actual_po_id and b.country_id=$country_id and b.inspection_status=1 and a.status_active=1 and b.status_active=1 and a.inspection_level=$inspection_level");

		
	if($inspection_level==1)
	{
		// $inspection_status = array_diff($inspection_status,[1]);
		$arr_index = "2,3,4,5";
	}

		$fin_fab_data=sql_select("SELECT a.actual_po_dtls_id as id, sum(case when  b.production_type=8 then a.prod_qty else 0 end) as qty, sum(case when  b.production_type=10 and b.trans_type=5 then a.prod_qty else 0 end) - sum(case when  b.production_type=10 and b.trans_type=6 then a.prod_qty else 0 end) as trans_qty,a.actual_po_id
		from pro_garments_prod_actual_po_details a, pro_garments_production_mst b 
		where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		 and b.production_type in(8,10) and  a.actual_po_id in ($actual_po_id)   group by a.actual_po_dtls_id,a.actual_po_id ");

	 
		 
	
		foreach($fin_fab_data as $row){
	
			$po_wise_data[$row[csf('actual_po_id')]]['fin_qnty']+=$row[csf('qty')];
	
		}
	
		
		$sql_data=sql_select($sql);

	?>
   		 <div>
			<table cellpadding="0" cellspacing="1" width="100%" class="rpt_table" rules="all" id="table_list_view">
                        <thead>
                          
                            <th width="100">Actual PO</th>
							<th width="90">PO Ship Date </th>
							<th width="100" >Country</th>
							<th width="70">PO Qty</th>
							<th width="80">Finishing Qty</th>
							<th width="80">Cuml. Insp. Qty</th>
							<th width="80">Current Inspec. Qty</th>
                            <th width="80">Minor qty</th>
                            <th width="80">Major qty</th>
							<th width="80">Critical Qty</th>
							<th width="80">Acceptable qty</th>
							<th width="80">Inspec. Status</th>
                            <th width="">Comments </th>
                        </thead>
						<body>

						<?
						$i=1;$acutal_po="";$acutal_po_id="";
						foreach($sql_data as $row){
							$cuml_insp=$cuml_qnty[0][csf('cuml_insp')];
							$bal_qnty=$po_wise_data[$row[csf('actual_po_id')]]['fin_qnty']-$cuml_insp;
							?>
                        <tr id="tr_<?=$i;?>">
                           
                            <td title="<?=$row[csf('actual_po_id')]."==>dtls id=".$row[csf('id')];?>">
                               <input name="actual_order_no_<?=$i;?>" id="actual_order_no_<?=$i;?>"  class="text_boxes" type="text" readonly  style="width:80px"   value="<?=$row[csf('actual_po_no')];?>"/>
                               <input type="hidden" name="actual_order_id_<?=$i;?>" id="actual_order_id_<?=$i;?>" value="<?=$row[csf('actual_po_id')];?>">							   
							
							   

                            </td>
							<td ><input name="txt_shipment_date_<?=$i;?>" id="txt_shipment_date_<?=$i;?>" class="datepicker" type="text" value="<?=change_date_format($row[csf('actual_po_ship_date')]);?>" style="width:70px;" disabled  /></td>
							<td><?   echo create_drop_down( "cbo_country_id_".$i, 100, $country_arr,"", 1, "--- Select ---", $row[csf('country_id')], "",1 )
							 ?></td> 
                            <td ><input name="txt_po_quantity_<?=$i;?>" id="txt_po_quantity_<?=$i;?>"  class="text_boxes_numeric" type="text"  style="width:70px"  disabled value="<?=$row[csf('actual_po_qnty')];?>"/></td>
							<td ><input name="txt_finishing_qnty_<?=$i;?>" id="txt_finishing_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"  style="width:80px" value="<?=$po_wise_data[$row[csf('actual_po_id')]]['fin_qnty'];?>" readonly /></td>
							<td ><input name="txt_cuml_insp_qnty_<?=$i;?>" id="txt_cuml_insp_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"  style="width:80px" readonly value="<?=$cuml_insp;?>" /></td>
							<td >

                             <input name="txt_insp_qnty_<?=$i;?>"  id="txt_insp_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"   style="width:80px"  value="<?=$row[csf('current_inspection_qnty')];?>" placeholder="<?=$bal_qnty;?>"/>
							</td>
                            <td><input name="txt_minor_qnty_<?=$i;?>"  id="txt_minor_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"   style="width:80px"   value="<?=$row[csf('minor_qnty')];?>"/></td>
                      
                            <td><input name="txt_major_qnty_<?=$i;?>"  id="txt_major_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"   style="width:80px"   value="<?=$row[csf('major_qnty')];?>"/></td>
							<td><input name="txt_critical_qnty_<?=$i;?>"  id="txt_critical_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"   style="width:80px"   value="<?=$row[csf('critical_qnty')];?>"/></td>
                      
					        <td><input name="txt_acceptable_qnty_<?=$i;?>"  id="txt_acceptable_qnty_<?=$i;?>"  class="text_boxes_numeric" type="text"   style="width:80px"   value="<?=$row[csf('acceptable_qnty')];?>"/></td>
							<td><?  echo create_drop_down( "cbo_insp_status_".$i,80, $inspection_status,"", 1, "-- Select --", $row[csf('inspection_status')], "","",$arr_index ); //change_cause_validation( this.value ) ?></td>
                            <td ><input name="txt_comment_<?=$i;?>" id="txt_comment_<?=$i;?>"  class="text_boxes" type="text"  style="width:80px"  value="<?=$row[csf('comments')];?>"/>
							<input name="txt_bal_qnty_<?=$i;?>" id="txt_bal_qnty_<?=$i;?>"  class="text_boxes" type="hidden" value="<?=$bal_qnty;?>"   />
							<input name="txt_prev_qnty_<?=$i;?>" id="txt_prev_qnty_<?=$i;?>"  class="text_boxes" type="hidden" value="<?=$row[csf('current_inspection_qnty')];?>"   />
							<input name="update_dtls_id_<?=$i;?>" id="update_dtls_id_<?=$i;?>"  class="text_boxes" type="hidden"  style="width:80px"  value="<?=$row[csf('id')];?>"/>
						</td>

                        </tr>
                       
					<?$i++;}?>
					</body>
                </table>
         </div>
		 
            
    <?

 
	 
	exit();
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
echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode,'','');
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
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

	echo "document.getElementById('txt_inspection_qnty').value = '';\n";  
	echo "document.getElementById('txt_po_quantity').value = '';\n";
	echo "document.getElementById('txt_finishing_qnty').value = '';\n";  
	echo "document.getElementById('txt_pub_shipment_date').value = '';\n";
	echo "document.getElementById('txt_cum_inspection_qnty').value = '';\n"; 
	echo "document.getElementById('cbo_inspection_status').value = 0;\n";
	echo "document.getElementById('cbo_cause').value = 0;\n";
	
	if($order_id>0)
	{
		if($country_id>0) $country_cond=" and country_id=$country_id"; else $country_cond="";
		$finishing_quantity=return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=$order_id and production_type=$qty_source  $country_cond and status_active=1 and is_deleted=0","production_quantity");
		$finishing_quantity_tran_in=return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=$order_id and production_type=10  and trans_type=5 $country_cond and status_active=1 and is_deleted=0","production_quantity");
		$finishing_quantity_tran_out=return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=$order_id and production_type=10 and trans_type=6  $country_cond and status_active=1 and is_deleted=0","production_quantity");

		if(str_replace("'","",$update_id)!="") $update_cond=" and id<>$update_id"; else $update_cond="";
		$prev_ins_qnty=return_field_value("sum(inspection_qnty) as inspection_qnty","pro_buyer_inspection","po_break_down_id=$order_id and inspection_level=$inspection_level $country_cond $update_cond and status_active=1 and is_deleted=0","inspection_qnty");
		$cu_finish_qnty=($finishing_quantity+$finishing_quantity_tran_in)-($prev_ins_qnty+$finishing_quantity_tran_out);
		echo "document.getElementById('txt_finishing_qnty').value = '$cu_finish_qnty';\n";
	}
	
	if($order_id && $week_id && $country_id)
	{
		$data_cum_ins=sql_select("select po_break_down_id,week_id,country_id,sum(inspection_qnty) as inspection_qnty from  pro_buyer_inspection  where po_break_down_id=$order_id and inspection_level=$inspection_level and status_active=1 and is_deleted=0 group by  po_break_down_id,week_id,country_id" );
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
		$data_cum_ins=sql_select("select po_break_down_id,week_id, sum(inspection_qnty) as inspection_qnty from  pro_buyer_inspection  where po_break_down_id=$order_id and inspection_level=$inspection_level and status_active=1 and is_deleted=0 group by  po_break_down_id,week_id" );
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
		$data_cum_ins=sql_select("select po_break_down_id,country_id, sum(inspection_qnty) as inspection_qnty from  pro_buyer_inspection  where po_break_down_id=$order_id and inspection_level=$inspection_level and status_active=1 and is_deleted=0 group by  po_break_down_id,country_id" );
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
		
		$data_cum_ins=sql_select("select po_break_down_id, sum(inspection_qnty) as inspection_qnty from  pro_buyer_inspection  where po_break_down_id=$order_id and inspection_level=$inspection_level and status_active=1 and is_deleted=0 group by  po_break_down_id" );
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
				$finishing_quantity=return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=$order_id and production_type=8  $country_cond and status_active=1 and is_deleted=0","production_quantity");
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
				$country_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$cbo_order_id and production_type=8 and country_id=$cbo_country_id and status_active=1 and is_deleted=0");
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
				$order_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$cbo_order_id and production_type=8 and status_active=1 and is_deleted=0");
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
		$field_array_dtls=" id,mst_id, item_id, color_id, color_qty, ins_qty, inserted_by, insert_date,  status_active, is_deleted";
		$dtls_data=explode("----", $hidden_ins_data2);
		$data_array_dtls="";
		foreach($dtls_data as $data_val)
		{
			$val=explode("_", $data_val);
			if ($data_array_dtls) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$id.",'".$val[0]."','".$val[1]."','".$val[2]."','".$val[3]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls++;

		}
		$field_array="id,job_no,inspection_company,source, working_company, working_location, working_floor,inspected_by,inspection_date,inspection_level,entry_form,inserted_by,insert_date";
		$data_array="(".$id.",".$txt_job_no.",".$cbo_inspection_company.",".$cbo_source.",".$cbo_working_company.",".$cbo_working_location.",".$cbo_working_floor.",".$cbo_inspection_by.",".$txt_inp_date.",".$cbo_inspection_level.",567,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//==============================start==================dtls===========
		   $id_dtls2=return_next_id( "id", "pro_buyer_inspection_dtls", 1 ) ;
			$field_array_dtls2="id,mst_id,job_no,actual_po_no,actual_po_id,actual_po_ship_date,actual_po_qnty,country_id,finishing_qnty,cuml_inspection_qnty,current_inspection_qnty,minor_qnty,major_qnty,critical_qnty,acceptable_qnty,inspection_status,comments, inserted_by, insert_date";

			$data_array_dtls2="";
		
			$new_array_color=array();
			for ($i=1;$i<=$row_num;$i++)
			{
				
				$actual_order_no="actual_order_no_".$i;
				$actual_order_id="actual_order_id_".$i;
				$txt_shipment_date="txt_shipment_date_".$i;			 
				$cbo_country_id="cbo_country_id_".$i;
				$txt_po_quantity="txt_po_quantity_".$i;
				$txt_finishing_qnty="txt_finishing_qnty_".$i;
				$txt_cuml_insp_qnty="txt_cuml_insp_qnty_".$i;
				$txt_insp_qnty="txt_insp_qnty_".$i;
				$txt_minor_qnty="txt_minor_qnty_".$i;								
				$txt_major_qnty="txt_major_qnty_".$i;
				$txt_critical_qnty="txt_critical_qnty_".$i;
				$txt_acceptable_qnty="txt_acceptable_qnty_".$i;
				$cbo_insp_status="cbo_insp_status_".$i;
				$txt_comment="txt_comment_".$i;
				
			
				
				if ($i!=1) $data_array_dtls2 .=",";
				$data_array_dtls2 .="(".$id_dtls2.",".$id.",".$txt_job_no.",".$$actual_order_no.",".$$actual_order_id.",".$$txt_shipment_date.",".$$txt_po_quantity.",".$$cbo_country_id.",".$$txt_finishing_qnty.",".$$txt_cuml_insp_qnty.",".$$txt_insp_qnty.",".$$txt_minor_qnty.",".$$txt_major_qnty
				.",".$$txt_critical_qnty.",".$$txt_acceptable_qnty.",".$$cbo_insp_status.",".$$txt_comment.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_dtls2=$id_dtls2+1;
			}
		   // echo "10**";die;
			//   echo "10**insert into pro_buyer_inspection($field_array)values".$data_array;die;
			$rID_dtl2=sql_insert("pro_buyer_inspection_dtls",$field_array_dtls2,$data_array_dtls2,0);

		//============================end====================================



 		$rID=sql_insert("pro_buyer_inspection",$field_array,$data_array,0);
 		$rID_dtls=sql_insert("pro_buyer_inspection_breakdown",$field_array_dtls,$data_array_dtls,0);

		if($db_type==0)
		{
			if($rID && $rID_dtls)
			{
				mysql_query("COMMIT");  
				echo "0**".$rID."**".$row_num;
			}
			else
			{
				mysql_query("ROLLBACK")."**".$row_num;
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
		if($rID && $rID_dtls)
			{
				oci_commit($con);
				echo "0**".$rID."**".$row_num;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID."**".$row_num;
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
				$finishing_quantity=return_field_value("sum(production_quantity) as production_quantity","pro_garments_production_mst","po_break_down_id=$order_id and production_type=8  $country_cond and status_active=1 and is_deleted=0","production_quantity");
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
				$country_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$cbo_order_id and production_type=8 and country_id=$cbo_country_id and status_active=1 and is_deleted=0");
			
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
				$order_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$cbo_order_id and production_type=8 and status_active=1 and is_deleted=0");
				if($order_finishing_qty < $order_insfection_qty+$txt_inspection_qnty && $cbo_inspection_status==1)
				{
					echo "25**0";
					disconnect($con);
					die;
				}
			}
		
		}

		$act_po_id_array=array();		
		$insp_qty = 0;
		for ($i=1;$i<=$row_num;$i++)
		{
			$actual_order_id="actual_order_id_".$i;
			$txt_insp_qnty="txt_insp_qnty_".$i;
			$act_po_id_array[$$actual_order_id] = $$actual_order_id;
			$insp_qty += $$txt_insp_qnty;
		}

		$act_po_ids = implode(",",array_filter($act_po_id_array));

		$sql = "SELECT b.actual_po_id, b.ex_fact_qty from PRO_EX_FACTORY_MST a,PRO_EX_FACTORY_ACTUAL_PO_DETAILS b where a.id=b.mst_id and b.actual_po_id in($act_po_ids) and a.status_active=1 and b.status_active=1 and b.ex_fact_qty>0";
		$res = sql_select($sql);
		$prev_ex_qty_arr = array();
		foreach ($res as $v) 
		{
			$prev_ex_qty_arr[$v['ACTUAL_PO_ID']] += $v['EX_FACT_QTY'];
		}
		unset($res);

		// ========================== inspection data ==============================
		$sql = "SELECT c.id as ac_po_id, b.CURRENT_INSPECTION_QNTY from PRO_BUYER_INSPECTION a, PRO_BUYER_INSPECTION_DTLS b, WO_PO_ACC_PO_INFO c where a.id=b.mst_id and b.ACTUAL_PO_ID=c.id and b.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.INSPECTION_STATUS=1 and a.INSPECTION_LEVEL=2 and c.id in($act_po_ids) and a.id != $txt_mst_id and a.entry_form=567";
		$res = sql_select($sql);
		$acpo_insp_qty_arr = array();
		foreach ($res as $v) 
		{
			$acpo_insp_qty_arr[$v['AC_PO_ID']] += $v['CURRENT_INSPECTION_QNTY'];
		}

		foreach ($acpo_insp_qty_arr as $act_po_id => $qty) 
		{
			if($prev_ex_qty_arr[$act_po_id] >  ($insp_qty + $qty))
			{
				echo "10**ফাইনাল ইন্সপেকশন কোয়ান্টিটি এক্স ফ্যাক্টরি কোয়ান্টিটি এর চেয়ে কমানো যাবে না।";
				disconnect($con);
				die;
			}
		}

		
		//--------------------------------------------------------------Compare end;
								
		$field_array="id,job_no,inspection_company,source, working_company, working_location, working_floor,inspected_by,inspection_date,inspection_level,inserted_by,insert_date";
		
		$field_array="job_no*inspection_company*source*working_company*working_location*working_floor*inspected_by*inspection_date*inspection_level*updated_by*update_date";
		$data_array="".$txt_job_no."*".$cbo_inspection_company."*".$cbo_source."*".$cbo_working_company."*".$cbo_working_location."*".$cbo_working_floor."*".$cbo_inspection_by."*".$txt_inp_date."*".$cbo_inspection_level."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("pro_buyer_inspection",$field_array,$data_array,"id","".$txt_mst_id."",1);
		$id_dtls=return_next_id( "id", "pro_buyer_inspection_breakdown", 1 ) ;
		$delete_dtls=execute_query( "delete from pro_buyer_inspection_breakdown where mst_id=$txt_mst_id",0);

		
		$hidden_ins_data2=str_replace("'","",$hidden_ins_data);
		$field_array_dtls=" id,mst_id, item_id, color_id, color_qty, ins_qty, inserted_by, insert_date,  status_active, is_deleted";
		$dtls_data=explode("----", $hidden_ins_data2);
		$data_array_dtls="";
		foreach($dtls_data as $data_val)
		{
			$val=explode("_", $data_val);
			if ($data_array_dtls) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$txt_mst_id.",'".$val[0]."','".$val[1]."','".$val[2]."','".$val[3]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id_dtls++;

		}

	    	$rID_dtls=sql_insert("pro_buyer_inspection_breakdown",$field_array_dtls,$data_array_dtls,0);
			//==============================start==================dtls===========
		
			
			// $data_array_dtls2="";

			// $new_array_color=array();
			// for ($i=1;$i<=$row_num;$i++)
			// {
				
		
				

				
			// 	if ($i!=1) $data_array_dtls2 .=",";
			// 	$data_array_dtls2 .="(".$id_dtls2.",".$id.",".$txt_job_no.",".$$actual_order_no.",".$$actual_order_id.",".$$txt_shipment_date.",".$$cbo_country_id.",".$$txt_po_quantity.",".$$txt_finishing_qnty.",".$$txt_cuml_insp_qnty.",".$$txt_insp_qnty.",".$$txt_minor_qnty.",".$$txt_major_qnty
			// 	.",".$$txt_critical_qnty.",".$$txt_acceptable_qnty.",".$$cbo_insp_status.",".$$txt_comment.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			// 	$id_dtls2=$id_dtls2+1;
			// }
			// // echo "10**";die;
			// //   echo "10**insert into pro_buyer_inspection($field_array)values".$data_array;die;
			// $rID_dtl2=sql_insert("pro_buyer_inspection_dtls",$field_array_dtls2,$data_array_dtls2,0);

			$field_array_up1="cuml_inspection_qnty*current_inspection_qnty*minor_qnty*major_qnty*critical_qnty*acceptable_qnty*inspection_status*comments*updated_by* update_date";
			$new_array_color=array();
			$act_po_id_array=array();
			for ($i=1;$i<=$row_num;$i++)
			{
   
			  	$actual_order_no="actual_order_no_".$i;
				$actual_order_id="actual_order_id_".$i;
				$txt_shipment_date="txt_shipment_date_".$i;			 
				$cbo_country_id="cbo_country_id_".$i;
				$txt_po_quantity="txt_po_quantity_".$i;
				$txt_finishing_qnty="txt_finishing_qnty_".$i;
				$txt_cuml_insp_qnty="txt_cuml_insp_qnty_".$i;
				$txt_insp_qnty="txt_insp_qnty_".$i;
				$txt_minor_qnty="txt_minor_qnty_".$i;								
				$txt_major_qnty="txt_major_qnty_".$i;
				$txt_critical_qnty="txt_critical_qnty_".$i;
				$txt_acceptable_qnty="txt_acceptable_qnty_".$i;
				$cbo_insp_status="cbo_insp_status_".$i;
				$txt_comment="txt_comment_".$i;

			   $updatedtlsid="update_dtls_id_".$i;		 

   
			   if(str_replace("'",'',$$updatedtlsid)!="")
			   {
				   $id_arr[]=str_replace("'",'',$$updatedtlsid);
				   $data_array_up1[str_replace("'",'',$$updatedtlsid)] =explode("*",("".$$txt_cuml_insp_qnty."*".$$txt_insp_qnty."*".$$txt_minor_qnty."*".$$txt_major_qnty."*".$$txt_critical_qnty."*".$$txt_acceptable_qnty."*".$$cbo_insp_status."*".$$txt_comment."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			   }
			   $act_po_id_array[$$actual_order_id] = $$actual_order_id;
			}
		   
		    //   echo "10**".bulk_update_sql_statement( "pro_buyer_inspection_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );
			$rID1=execute_query(bulk_update_sql_statement( "pro_buyer_inspection_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
			//============================end====================================



		if($db_type==0)
		{
			if($rID && $delete_dtls && $rID_dtls)
			{
				mysql_query("COMMIT");  
				echo "1**".$rID."**".$row_num;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$row_num;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $delete_dtls && $rID_dtls )
			{
				oci_commit($con);
				echo "1**".$rID."**".$row_num;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID."**".$row_num;
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
		$field_array2="updated_by*update_date*status_active*is_deleted";
		$data_array2="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		//echo $txt_mst_id;
		$rID=sql_delete("pro_buyer_inspection",$field_array,$data_array,"id","".$txt_mst_id."",1);
		$rID2=sql_delete("pro_buyer_inspection_dtls",$field_array2,$data_array2,"mst_id","".$txt_mst_id."",1);
		//echo "2**".$rID;
		$delete_dtls=execute_query( "delete from pro_buyer_inspection_breakdown where mst_id=$txt_mst_id",0);
		
		if($db_type==0)
		{
			if($rID && $delete_dtls )
			{
				mysql_query("COMMIT");  
				echo "2**".$rID."**".$row_num;;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$row_num;;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $delete_dtls  )
			{
				oci_commit($con);
				echo "2**".$rID."**".$row_num;;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID."**".$row_num;;
			}
		}
		disconnect($con);die;

	}
}
if($action=="show_active_listview")
{
	
	
	$inspection_type=return_field_value("inspected_by","pro_buyer_inspection","job_no='$data' and status_active=1 and is_deleted=0");

	$country_arr=return_library_array( "select id,country_name from lib_country where status_active=1 order by id", "id", "country_name"  );
	
	//$arr=array (0=>$po_number,1=>$insp_company_library,4=>$inspection_status,5=>$inspection_cause);
 	$sql= "SELECT b.actual_po_id, a.id,a.po_break_down_id,a.inspected_by,a.inspection_company,a.inspection_date,b.current_inspection_qnty,a.inspection_level,a.inspection_cause,b.comments,b.actual_po_no,b.actual_po_qnty,b.minor_qnty,b.major_qnty,b.critical_qnty,b.acceptable_qnty,b.inspection_status,b.id as dtls_id,b.country_id,b.actual_po_ship_date
	  from 
	   pro_buyer_inspection a ,
	   pro_buyer_inspection_dtls b 
	   where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and a.job_no='$data'"; 





	$data_array=sql_select($sql);
	?>
	 
	<table width="1205" class="rpt_table" border="1" rules="all" align="left">
	    <thead>
	        <th width="35">Sl</th>	      
	        <th width="100">Actual Po</th>
			<th width="100">Actual Po Ship Date</th>
			<th width="100">Country Name</th>
	        <th width="120">Inspection Company</th>
	        <th width="100">Inspection Date</th>
	        <th width="100">Inspection Qnty</th>
			<th width="80">Minor qty</th>
			<th width="80">Major qty</th>
			<th width="80">Critical Qty</th>
			<th width="80">Acceptable qty</th>
	        <th width="80">Inspection Status</th>
	        <th width="120">Inspection Level</th>	      
	        <th width="150">Comments</th>
	    </thead>
	 </table>
	  

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1205" class="rpt_table" id="tbl_list_search" align="left">

	<?
	$i=1;
	foreach($data_array as $row){

		$company_library=return_library_array( "select id,company_name from lib_company where status_active =1 and  is_deleted=0", "id", "company_name"  );
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
	
			<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="get_php_form_data('<? echo $row[csf('id')]."_".$row[csf('dtls_id')]."_".$row[csf('country_id')]."_".$row[csf('actual_po_id')]; ?>','populate_inspection_details_form_data','requires/inspection_for_actual_po_entry_controller')" style="cursor:pointer;">
				<td width="35"><?=$i;?></td>
				<td width="100"><?=$row[csf('actual_po_no')];?></td>	
				<td width="100"><?=change_date_format($row[csf('actual_po_ship_date')]);?></td>	
				<td width="100"><?=$country_arr[$row[csf('country_id')]];?></td>		
				<td width="120"><?=$insp_company_library[$row[csf('inspection_company')]];?></td>
				<td width="100"><?=change_date_format($row[csf('inspection_date')]);?></td>
				<td width="100" align="right"><?=$row[csf('current_inspection_qnty')];?></td>
				<td width="80" align="right"><?=$row[csf('minor_qnty')];?></td>
				<td width="80" align="right"><?=$row[csf('major_qnty')];?></td>
				<td width="80" align="right"><?=$row[csf('critical_qnty')];?></td>
				<td width="80" align="right"><?=$row[csf('acceptable_qnty')];?></td>
				<td width="80"><?=$inspection_status[$row[csf('inspection_status')]];?></td>
				<td width="120"><?=$inpLevelArray[$row[csf('inspection_level')]];?></td>			
				<td width="150"><?=$row[csf('comments')];?></td>
			</tr>
			<?
			$i++;
			$current_inspection_qnty+=$row[csf('current_inspection_qnty')];
		}

		?>
		<tr>
		<th width="35"></th>
		<th width="100"></th>	
		<th width="100"></th>	
		<th width="100"></th>		
		<th width="120" ></th>
		<th width="100" align="right"><b>Total:</b></th>
		<th width="100" align="right"><b><? echo $current_inspection_qnty;?></b></th>
		<th width="80" align="right"></th>
		<th width="80" align="right"></th>
		<th width="80" align="right"></th>
		<th width="80" align="right"></th>
		<th width="80"></th>
		<th width="120"></th>			
		<th width="150"></th>
	</tr>

		</table>
	 

	<?


}

if($action=="populate_inspection_details_form_data")
{
	
	
	list($mst_id,$dtls_id,$country_id,$actual_po_id)=explode("_",$data);

	$actual_po_no_arr=return_library_array( "select id,actual_po_no from pro_buyer_inspection_dtls", "id", "actual_po_no"  );

	$data_array=sql_select("SELECT b.po_number, a.actual_po_id, working_floor, source, working_company, working_location,all_data,ins_reason, a.po_break_down_id,a.inspection_company,a.inspected_by,a.week_id,a.country_id,a.comments,a.inspection_date,a.inspection_qnty,a.inspection_status,a.inspection_level,a.inspection_cause,a.comments,a.id,b.po_quantity ,b.plan_cut,b.pub_shipment_date from  pro_buyer_inspection a, wo_po_break_down b  where a.job_no=b.job_no_mst and a.id =$mst_id"); 
	foreach ($data_array as $row)
	{
		$source=$row[csf("source")];
		$working_location="'".$row[csf("working_company")].'**'.$row[csf("working_location")]."'";
		$working_company=$row[csf("working_company")];
		$dtls_data=$mst_id."_".$dtls_id."_".$country_id."_".$row[csf("inspection_level")]."_".$actual_po_id;

		echo "document.getElementById('actual_order_id').value = '".$actual_po_no_arr[$dtls_id]."';\n"; 
		echo "document.getElementById('cbo_actual_order_id').value = '".$actual_po_id."';\n"; 

		echo "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', '__".$row[csf("po_break_down_id")]."', 'load_drop_down_country_id', 'country_drop_down_td' );";
		echo "show_list_view('".$dtls_data."','actual_po_update_list_view','po_list_view','requires/inspection_for_actual_po_entry_controller');\n"; 
		 
		 echo "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', $source, 'load_drop_working_company', 'working_company_td' );";
		 echo "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', $working_company, 'load_drop_down_working_location', 'working_location_td' );";
		 echo "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', $working_location, 'load_drop_down_working_floor', 'working_floor_td' );";

		 echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n"; 
		 echo "document.getElementById('cbo_working_company').value = '".$row[csf("working_company")]."';\n"; 
	

		echo "document.getElementById('cbo_working_location').value = '".$row[csf("working_location")]."';\n"; 
		echo "document.getElementById('cbo_working_floor').value = '".$row[csf("working_floor")]."';\n"; 

		 echo "load_drop_down( 'requires/inspection_for_actual_po_entry_controller',".$row[csf("inspected_by")]."+','+document.getElementById('cbo_buyer_name').value+','+document.getElementById('cbo_company_name').value, 'load_drop_down_buyer_party_company', 'cutt_company_td' );";
		
	 	echo "get_php_form_data('".$row[csf("po_break_down_id")].",".$row[csf("week_id")].",".$row[csf("country_id")].",".$row[csf("inspection_level")].",".$row[csf("id")]."','set_po_qnty_ship_date', 'requires/inspection_for_actual_po_entry_controller');\n";
		
		
		echo "document.getElementById('cbo_inspection_company').value = '".$row[csf("inspection_company")]."';\n";  
		echo "document.getElementById('txt_inp_date').value = '".change_date_format($row[csf("inspection_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('cbo_actual_order_id').value = '".$row[csf("actual_po_id")]."';\n";  
		
		echo "document.getElementById('cbo_inspection_level').value = '".$row[csf("inspection_level")]."';\n";  
		echo "document.getElementById('txt_mst_id').value = '".$row[csf("id")]."';\n";  
		echo "document.getElementById('cbo_inspection_by').value = '".$row[csf("inspected_by")]."';\n";  
		// echo "document.getElementById('cbo_week_no').value = '".$row[csf("week_id")]."';\n";  
		// echo "document.getElementById('cbo_country_id').value = '".$row[csf("country_id")]."';\n";  
       // echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n"; 
       // echo "document.getElementById('cbo_order_id').value = '".$row[csf("po_break_down_id")]."';\n";  
		// echo "document.getElementById('cbo_order_val').value = '".$row[csf("po_number")]."';\n";  
		
		//echo "document.getElementById('txt_po_quantity').value = '".$row[csf("po_quantity")]."';\n";  
	
	    // echo "document.getElementById('txt_ins_reason').value = '".$row[csf("ins_reason")]."';\n"; 
	    // echo "document.getElementById('hidden_ins_data').value = '".$row[csf("all_data")]."';\n"; 
	    // echo "document.getElementById('txt_inspection_qnty').value = '".$row[csf("inspection_qnty")]."';\n"; 
		// echo "document.getElementById('cbo_inspection_status').value = '".$row[csf("inspection_status")]."';\n";  
	
		// echo "document.getElementById('cbo_cause').value = '".$row[csf("inspection_cause")]."';\n"; 
		// echo "document.getElementById('txt_comments').value = '".$row[csf("comments")]."';\n"; 
		//echo "document.getElementById('txt_cum_inspection_qnty').value = '';\n";
		//  
	


		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_buyer_inspection_entry',1);\n";  
		
		
		
	 
	 }

	
	 exit;
}

if($action=="show_image")
{
	echo load_html_head_contents("Buyer Inspection Image","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        { 
        ?>
        <td><a href="<? $row[csf('image_location')] ?>" target="_new"><img src='../../../<? echo $row[csf('image_location')]; ?>' height='350' width='900' align="middle" /></a></td>
        <?
        }
        ?>
        </tr>
    </table>
    <?
}
?>
