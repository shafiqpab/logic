<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];




if ($action=="load_drop_down_supplier")
{	//echo "select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b where a.id=b.supplier_id 	 and a.tag_company=$data order by supplier_name";
  echo create_drop_down( "cbo_supplier", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b where a.id=b.supplier_id 	 and b.tag_company=$data order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );  
	/*echo create_drop_down( "cbo_supplier", 170, "select id,supplier_name from lib_supplier where FIND_IN_SET($data,tag_company) order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 ); */ 	 
	exit();
}
$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$location_arr=return_library_array( "select id, location_name from lib_location where status_active=1 and is_deleted=0",'id','location_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
$sample_arr=return_library_array(" select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');
$other_party_name_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');

$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

if($action=="load_drop_down_sent")
{
	$data = explode("_",$data);
	if($data[0]==1)
	{
		
	 echo create_drop_down( "cbo_search_by", 100, "select id,buyer_name from  lib_buyer  where status_active=1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","0" );
    }
	else if($data[0]==2)
	{
	 echo create_drop_down( "cbo_search_by", 100, "select id,supplier_name from  lib_supplier  where status_active=1 and is_deleted=0  order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected,"","0" );
	}
     else if($data[0]==3)
	{
	 echo create_drop_down( "cbo_search_by", 100, "select id,other_party_name from  lib_other_party where status_active=1 and is_deleted=0  order by other_party_name","id,other_party_name", 1, "-- Select Other Party --", $selected,"","0" );
	}
	else
	{
	 echo create_drop_down( "cbo_search_by", 100, $blank_array,"", 1, "-- Select --", $selected, "","","" );	
	}
	
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=223 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

//style search------------------------------//
if($action=="chalan_surch")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
        
	<script>
		function js_set_value(str)
		{
		
			var splitData = str.split("_");	  	 
			$("#hidden_chalan_id").val(splitData[0]); 
			$("#hidden_chalan_no").val(splitData[1]); 
			$("#hidden_search_number").val($("#cbo_search_by").val()); 
		
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th width="150">Search By</th>
						<th id="search_by_td_up" width="200">Enter Booking No</th>
						<th width="200">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<? 
							$search_arr=array(1=>"Chalan No",2=>"System ID"); 
							$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 170, $search_arr,"",1, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td>   
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $category; ?>, 'create_chalan_search_list_view', 'search_div', 'gate_in_and_out_report_contorller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
						</td>
				</tr>
				<tr>                  
					<td align="center" height="40" valign="middle" colspan="4">
						<? echo load_month_buttons(1);  ?>
						<input type="hidden" id="hidden_chalan_id" value="" />
						<input type="hidden" id="hidden_chalan_no" value="" />
						<input type="hidden" id="hidden_search_number" value="" />
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

if($action=="create_chalan_search_list_view")
{
	
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$item_category= $ex_data[5];
 	
	$sql_cond="";
	
	if(trim($txt_search_by)==0) { echo "please select Search By";die;}
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for challan
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";	
		}
		else if(trim($txt_search_by)==2) // for System ID
		{
			$sql_cond .= " and a.sys_number LIKE '%$txt_search_common%'";				
		}
 	}
	
	 if(trim($item_category)!=0) { $sql_cond .= " and b.item_category_id=$item_category"; }
 		if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.in_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	
	
	//if($txt_search_by==1 )
	//{
 		$sql = "select a.id ,a.challan_no as chalan,a.sys_number_prefix_num,a.in_date,b.sample_id,party_type 	from  inv_gate_in_mst a, inv_gate_in_dtl b 
				where a.id=b.mst_id and a.status_active=1 $sql_cond";
				
				//a.supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
	//}
   
   	/*if($txt_search_by==2)
		{
			$sql = "select id ,sys_number as chalan,supplier_name,receive_date,sample 	
					from  inv_gate_in_mst
					where  status_active=1 $sql_cond";//a.supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
		}*/
	//echo $sql;//die;//"select id,sample_name from lib_sample where status_active=1 order by sample_name","id,sample_name"
	$result = sql_select($sql);
  	$party_type_arr=array(1=>"Buyer",2=>"Supplier",3=>"Other Party");
	$sample_arr=return_library_array( "select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');
	$arr=array(2=>$party_type_arr,3=>$sample_arr);
	echo  create_list_view("list_view", "Challan No/System ID, Receive Date, Supplier, Sample","150,100,120,150","600","400",0, $sql , "js_set_value", "id,sys_number_prefix_num", "", 1, "0,0,party_type,sample_id", $arr, "sys_number_prefix_num,in_date,party_type,sample_id", "",'','0,3,0,0,0,0') ;		
	exit();	
	
}

//order search------------------------------//
if($action=="pi_search")
{
	
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
		}
    </script>
	<?
	extract($_REQUEST);
	
	if($company==0) $company_name=""; else $company_name=" and a.company_id=$company";
	if($category==0) $item_cate=""; else $item_cate="and b.item_category_id=$category";
	
	$sql ="select a.id,a.pi_reference,a.sys_number_prefix_num,a.party_type,a.in_date	
					from  inv_gate_in_mst a,inv_gate_in_dtl b 
					where  a.id=b.mst_id and a.status_active=1 $company_name $item_cate "; 
	$arr=array(1=>$supplier_arr);
	echo create_list_view("list_view", "PI/WO/REQ,Sys No,Receive Date","150,150,100,","450","310",0, $sql , "js_set_value", "id,pi_reference", "", 1, "0,0,0", $arr, "pi_reference,sys_number_prefix_num,in_date", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

//order search------------------------------//
if($action=="gatepass_popup")
{
	
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
		}
    </script>
	<?
	extract($_REQUEST);
	
	if($company==0) $company_name=""; else $company_name=" and a.company_id=$company";
	//if($category==0) $item_cate=""; else $item_cate="and b.item_category_id=$category";
	

	$sql = "SELECT a.id, a.company_id, a.sys_number_prefix_num, a.sys_number, a.out_date
	from inv_gate_pass_mst a, inv_gate_pass_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $company_name 
	group by a.id, a.company_id, a.sys_number_prefix_num, a.sys_number, a.out_date order by a.sys_number_prefix_num desc";
	//echo $sql;	
	$arr=array();
	echo create_list_view("list_view", "Gate Pass ID,Gate Pass ID<br> (Suffix),Gate Pass Date","150,150,100,","450","310",0, $sql , "js_set_value", "id,sys_number", "", 1, "0,0,0", $arr, "sys_number,sys_number_prefix_num,out_date", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}



if ($action=="load_drop_down_location")
{
	 
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- All --", 0, "" );
	exit();
}


//report generated here--------------------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_party_type=str_replace("'","",$cbo_party_type);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_pi_no=str_replace("'","",$txt_pi_no);
	$cbo_withingroup=str_replace("'","",$cbo_withingroup);
	$txt_challan=str_replace("'","",$txt_challan);
	$cbo_sample=str_replace("'","",$cbo_sample);
	$report_type=str_replace("'","",$type);
	$sample_chk=str_replace("'","",$sample_chk_id);
	$hidden_pi_id=str_replace("'","",$hidden_pi_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_gate_pass_no=str_replace("'","",$txt_gate_pass_no);
	$txt_gate_pass_id=str_replace("'","",$txt_gate_pass_id);
	$cbo_returnable=str_replace("'","",$cbo_returnable);
	
	$userArr = return_library_array("SELECT id,user_name from user_passwd ","id","user_name");
	
	
	if($cbo_withingroup!=0) $withingroup_cond=" and a.within_group=$cbo_withingroup"; else $withingroup_cond="";
	if($cbo_search_by!=0) $search_by_buyer_cond=" and a.sending_company=$cbo_search_by"; else $search_by_buyer_cond="";
	if($cbo_party_type!=0) $search_by_party_cond=" and a.party_type=$cbo_party_type"; else $search_by_party_cond="";
	if($cbo_location_id!=0) $location_cond=" and a.com_location_id in($cbo_location_id)"; else $location_cond="";
	if($txt_gate_pass_id !="") $gate_pass_cond_one=" and a.inv_gate_pass_mst_id in($txt_gate_pass_id)"; else $gate_pass_cond_one="";
	if($txt_gate_pass_id !="") $gate_pass_cond_two=" and a.id in($txt_gate_pass_id)"; else $gate_pass_cond_two="";

	$gate_pass_cond3=$gate_pass_cond4="";
	if($txt_gate_pass_no !="") $gate_pass_cond3=" and a.gate_pass_no like '%$txt_gate_pass_no'";
	if($txt_gate_pass_no !="") $gate_pass_cond4=" and a.sys_number like '%$txt_gate_pass_no'";


	if($sample_chk==1)
	{
		if($cbo_sample==0) $sample_cond=" and b.sample_id>0";
	}
	else
	{
		 if($cbo_sample!=0) $sample_cond=" and b.sample_id=$cbo_sample"; else $sample_cond="";
		//  $sample_cond="";	
	}
	//echo $sample_cond;
	if($cbo_item_cat!=0) $item_category_cond=" and b.item_category_id=$cbo_item_cat"; else $item_category_cond.="";
	//if($cbo_sample!=0) $sample_cond=" and b.sample_id=$cbo_sample"; else $sample_cond="";
	
	//echo $sample_cond;die;
    if($cbo_company_name!=0) $company_conds.=" and a.company_id=$cbo_company_name"; else $company_conds.="";
    if($cbo_returnable!=0 ) $returnable_conds.=" and a.returnable=$cbo_returnable"; else $returnable_conds="";
	
	$buyer_condition="";
	if($cbo_party_type==1)
	{
		if(str_replace("'","",$cbo_search_by)!="")  $buyer_condition=" and a.buyer_name='".str_replace("'","",$cbo_search_by)."'"; else $buyer_condition="";
	}
	if($cbo_party_type==2)
	{
		//if(str_replace("'","",$txt_challan)!="")  $sql_condition.=" and a.sys_number='".str_replace("'","",$txt_challan)."'"; else $sql_condition.="";
		
	}
	if($report_type==2)
	{
	}
    //if($hidden_pi_id!="") $sql_condition.=" and a.id in ($hidden_pi_id)"; else $sql_condition.="";
    // if($hidden_pi_id==""  && $txt_pi_no!="") $sql_condition.=" and a.pi_wo_req_number='$txt_pi_no'"; else $sql_condition.="";
	if($cbo_party_type!=0) $search_by_cond=" and a.party_type=$cbo_party_type"; else $search_by_cond="";
	//echo  $cbo_party_type;die;
	if($txt_challan!="") $challan_sys_cond=" and a.sys_number_prefix_num='$txt_challan'"; else $challan_sys_cond="";
	//if($txt_challan!=0) $challan_sys_cond=" and a.challan_no=$txt_challan"; else $challan_sys_cond="";
	if($txt_pi_no!="") $pi_refernce_cond=" and a.pi_reference='$txt_pi_no'"; else $pi_refernce_cond="";
	//echo $challan_sys_cond;die;
	
	if($report_type==1 || $report_type==4)
	{
		if($db_type==0)
		{
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond=" and a.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $out_date_cond=""; 
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond_scan=" and c.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $out_date_cond_scan=""; 
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond="and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $out_date_cond="";
			
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond_scan="and c.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $out_date_cond_scan="";
		}
	}
	else if($report_type==3)
	{
		if($db_type==0)
		{
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond=" and a.in_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $out_date_cond=""; 
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond_scan=" and c.in_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $out_date_cond_scan=""; 
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond="and a.in_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $out_date_cond="";
			
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond_scan="and c.in_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $out_date_cond_scan="";
		}
	}
	else
	{
		if($db_type==0)
		{
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond=" and a.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $out_date_cond=""; 
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond_scan=" and c.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $out_date_cond_scan=""; 
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond="and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $out_date_cond="";
			
			if($txt_date_from!="" && $txt_date_to!="") $out_date_cond_scan="and c.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $out_date_cond_scan="";
		}
	}
		
	
	
	// echo $out_date_cond_scan;
	

	
	$order_array=array();
	$order_sql="select a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_condition";

	$order_sql_result=sql_select($order_sql);
	$po_ids_arr=array();
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]][$row[csf('po_number')]]['buyer']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]][$row[csf('po_number')]]['style']=$row[csf('style_ref_no')];
		array_push($po_ids, $row[csf('po_number')]);
		//$po_ids[]=$row[csf('po_number')];
	}

	
	$po_cond='';
	if(!empty($buyer_condition) && count($po_ids))
	{
		$po_ids=array_unique($po_ids);
		$po_cond=where_con_using_array($po_ids,1,"b.buyer_order");
	}
	//echo "<pre>";
	//print_r($po_cond); die;

	if($cbo_company_name!=0) $template_conds=" and template_name=$cbo_company_name"; else $template_conds="";
	$print_report_format_sql=sql_select("SELECT FORMAT_ID,TEMPLATE_NAME from lib_report_template where module_id=6 and report_id=38 $template_conds and is_deleted=0 and status_active=1");
	foreach($print_report_format_sql as $row)
	{
		$gate_pass_format_arr=explode(",",$row["FORMAT_ID"]);
		$gate_pass_format_id[$row["TEMPLATE_NAME"]]=$gate_pass_format_arr[0];
	}

	if($report_type==1)  // Gate Out
	{
		$department_arr=return_library_array( "select id, department_name from lib_department where status_active=1 and is_deleted=0",'id','department_name');
		$section_arr=return_library_array( "select id, section_name from lib_section where status_active=1 and is_deleted=0",'id','section_name');
		$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');


		 //echo "SELECT a.id,a.gate_pass_no,a.company_id,a.com_location_id,a.in_date,a.time_hour,a.time_minute,b.quantity,b.item_category_id,b.item_description,b.reject_qty,b.get_pass_dtlsid from inv_gate_in_mst a,inv_gate_in_dtl b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $gate_pass_cond_one $gate_pass_cond3 group by a.id,a.gate_pass_no,a.company_id,a.com_location_id,a.in_date,a.time_hour,a.time_minute,b.quantity,b.item_category_id,b.item_description,b.reject_qty,b.get_pass_dtlsid order by a.id";die;

		$sql_data=sql_select("SELECT a.id,a.gate_pass_no,a.company_id,a.com_location_id,a.in_date,a.time_hour,a.time_minute,b.quantity,b.item_category_id,b.item_description,b.reject_qty,b.get_pass_dtlsid from inv_gate_in_mst a,inv_gate_in_dtl b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $gate_pass_cond_one $gate_pass_cond3 group by a.id,a.gate_pass_no,a.company_id,a.com_location_id,a.in_date,a.time_hour,a.time_minute,b.quantity,b.item_category_id,b.item_description,b.reject_qty,b.get_pass_dtlsid order by a.id");
		 
		

		$gate_in_array=array();
		foreach($sql_data as $row)
		{
			if($tem_id[$row[csf('gate_pass_no')]]==''){
				$gate_in_array[$row[csf('gate_pass_no')]]['in_date']=$row[csf('in_date')];
				$gate_in_array[$row[csf('gate_pass_no')]]['company_id']=$row[csf('company_id')];
				$gate_in_array[$row[csf('gate_pass_no')]]['com_location_id']=$row[csf('com_location_id')];
				$gate_in_array[$row[csf('gate_pass_no')]]['reject_qty']+=$row[csf('reject_qty')];
				$gate_in_array[$row[csf('gate_pass_no')]]['in_time']=$row[csf('time_hour')].':'.$row[csf('time_minute')];
				$tem_id[$row[csf('gate_pass_no')]]=1;
				//$ret_qty_array[$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('quantity')];
				$ret_qty_array[$row[csf('get_pass_dtlsid')]][$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('quantity')];
				//$rej_qty_array[$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]=$row[csf('reject_qty')];
				$rej_qty_array[$row[csf('get_pass_dtlsid')]][$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('reject_qty')];
				$ret_com_array[$row[csf('gate_pass_no')]]=$row[csf('company_id')];

			}
			else
			{
				//$ret_qty_array[$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('quantity')];
				$ret_qty_array[$row[csf('get_pass_dtlsid')]][$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('quantity')];
				//$rej_qty_array[$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]=$row[csf('reject_qty')];
				$rej_qty_array[$row[csf('get_pass_dtlsid')]][$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('reject_qty')];
				$ret_com_array[$row[csf('gate_pass_no')]]=$row[csf('company_id')];
			}
		} 
		//echo '<pre>';print_r($ret_qty_array);
	
		//var_dump($ret_qty_array);
	
		$sql_out="SELECT a.id,b.id as gpd_id, a.company_id, a.sent_to, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.issue_purpose, a.sent_to, a.returnable, a.est_return_date, a.challan_no, a.carried_by, a.time_hour, a.time_minute, a.get_pass_no, a.basis, a.within_group, a.com_location_id, a.location_id, a.department_id, a.section, a.attention, a.vhicle_number, a.delivery_company, a.inserted_by,a.issue_id, b.buyer_id, b.buyer_order, b.buyer_order_id, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.rate, b.amount, b.uom, b.uom_qty, b.remarks, c.out_date, c.out_time from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_out_scan c where a.id=b.mst_id and c.gate_pass_id=a.sys_number and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_conds $out_date_cond_scan $item_category_cond $sample_cond $challan_sys_cond $withingroup_cond $location_cond $po_cond $gate_pass_cond_two $gate_pass_cond4 $returnable_conds order by b.id"; 
		//echo $sql_out; die;
		$get_out_data=sql_select($sql_out);
		if(count($get_out_data)>0)
		{
			ob_start();	
		?>
        <fieldset style="width:3550;">
		<div style="width:3550;">
			<table width="3540" border="0" cellpadding="0" cellspacing="0" align="left">
				<tr style="border:none;">
					<th colspan="31" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
				</tr>
			</table>
			<? ob_start();?>
			<table width="3540" border="0" cellpadding="0" cellspacing="0" align="left"> 
				<tr style="border:none;">
					<td colspan="31" align="center" style="border:none;font-size:16px; font-weight:bold" >
							Gate Out Report
					</td> 
				</tr>
				<tr>
					<td colspan="31" align="center" style="border:none; font-size:14px;">
						Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
					</td>
				</tr>					
			</table>
			<table width="3540" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left"> 
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="120">Gate Pass No</th>
						<th width="100">Challan No</th>
						<th width="130">Sending Company</th>
						<th width="130">Delivery Company</th>
						<th width="100">Location</th>
						<th width="150">Department</th>
						<th width="150">Section</th>
						<th width="80">Out date</th>
						<th width="80">Out Time</th>
						
						<th width="100">Rcv Company [Location]</th>
						<th width="80">Rcv. date</th>
						<th width="50">Rcv. Time</th>
						
						<th width="60" style="word-break: break-all;">Return able</th>
						<th width="80">Est.Return Date</th>
						<th width="80">Act.Return Date</th>
						<th width="130">Item Category</th>
						<th width="150">Description</th>
						<th width="50">UOM</th>
						<th width="80">Quantity</th>
						<th width="80">Reject Qty</th>
						<th width="80">Rate</th>
						<th width="80">Amount</th>
						<th width="80">Return Qty.</th>
						<th width="80">Not Yet Return</th>
						<th width="100">Sent By</th>
						<th width="100">Sent to</th>
						<th width="100">Attention</th>
						<th width="100">Buyer</th>
						<th width="100">Buyer Order</th>
						<th width="100">Style</th>
						<th width="100">Purpose</th>
						<th width="100">Carried By</th>
						<th width="100">Insert By</th>
						<th width="100">Vehicle Number</th>
						<th>Remarks</th>
					</tr>
				</thead>
			</table> 
			<div style="width:3580px; overflow-y: scroll; max-height:250px;" id="scroll_body" align="left">
				<table width="3540" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
					<tbody>
					<?
					$i=$k=1;$total_out=0;$temp_arr=array(); $tot_reject_qty=0;
					foreach($get_out_data as $val)
					{
						$act_date='';$ret_qty='';$send_to='';
						
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$gate_in_com=$gate_in_array[$val[csf('sys_number')]]['company_id'];
						
						$gate_in_date=$gate_in_array[$val[csf('sys_number')]]['in_date'];
						$gate_in_time=$gate_in_array[$val[csf('sys_number')]]['in_time'];
						//$reject_qty=$gate_in_array[$val[csf('sys_number')]]['reject_qty'];
						
						if($ret_com_array[$val[csf('sys_number')]]==$val[csf("company_id")])
						{
							$act_date=$gate_in_date;
							//$ret_qty=$ret_qty_array[$val[csf('sys_number')]][$val[csf("item_category_id")]][$val[csf('item_description')]];
							//$ret_qty=$ret_qty_array[$val[csf('gpd_id')]][$val[csf('sys_number')]][$val[csf("item_category_id")]][$val[csf('item_description')]];
	
						}
						$ret_qty=$ret_qty_array[$val[csf('gpd_id')]][$val[csf('sys_number')]][$val[csf("item_category_id")]][$val[csf('item_description')]];
						$reject_qty=$rej_qty_array[$val[csf('gpd_id')]][$val[csf('sys_number')]][$val[csf("item_category_id")]][$val[csf('item_description')]];
						
						
						if($val[csf('within_group')]==2){$send_to=$val[csf("sent_to")];} //$supplier_arr[$val[csf("sent_to")]];
						else{$send_to=$company_arr[$val[csf("sent_to")]];}
						
						//else if($val[csf('basis')]==12){$send_to=$supplier_arr[$val[csf("sent_to")]];}

						
						$rec_location=$com_location=$to_location='';
						if($val[csf("com_location_id")])$com_location=$location_arr[$val[csf("com_location_id")]];
						if($gate_in_array[$val[csf('sys_number')]]['com_location_id'])$rec_location='['.$location_arr[$gate_in_array[$val[csf('sys_number')]]['com_location_id']].']';
						if($val[csf("location_id")])$to_location='['.$location_arr[$val[csf("location_id")]].']';
						
						?>
						<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trout_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trout_<? echo $i; ?>">
							<?
							if($temp_arr[$val[csf('sys_number')]]=="")
							{
								$temp_arr[$val[csf('sys_number')]]=$val[csf('sys_number')];
								?>
								<td width="30" align="center" style="word-break: break-all;"><p><? echo $k; ?></p></td>
								<?
								$k++;
							}
							else
							{
								?>
								<td width="30" style="word-break: break-all;"></td>
								<?
							}
							?>
							<td width="120" align="center" style="word-break: break-all;">
								<p><a href='#report_details' onClick="fnc_gate_pass_print('<? echo $gate_pass_format_id[$val[csf('company_id')]]; ?>','<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $val[csf('basis')]; ?>','<? echo $val[csf('com_location_id')]; ?>','<? echo $val[csf('challan_no')]; ?>','<? echo $val[csf('issue_id')]; ?>','<? echo $val[csf('RETURNABLE')]; ?>');"><? echo $val[csf('sys_number')]; ?></a></p>
							</td>
							<td width="100" style="word-break: break-all;"><p><? echo $val[csf("challan_no")]; ?></p></td>
							<td width="130" style="word-break: break-all;"><p><? echo $company_arr[$val[csf("company_id")]]; ?></p></td>
							<td width="130" style="word-break: break-all;"><p><? echo $val[csf("delivery_company")]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $com_location; ?></p></td>

							<td width="150" style="word-break: break-all;"><p><? echo $department_arr[$val[csf("department_id")]]; ?></p></td>
							<td width="150" style="word-break: break-all;"><p><? echo $section_arr[$val[csf("section")]]; ?></p></td>

							<td width="80" align="center" style="word-break: break-all;"><? echo '&nbsp;'.change_date_format($val[csf("out_date")]); ?></td>
							<td width="80" align="center" style="word-break: break-all;"><? echo $val[csf("out_time")]; ?></td>
							<td width="100" style="word-break: break-all;"><? echo $company_arr[$gate_in_com].' '.$rec_location;?></td>
							<td width="80" align="center" style="word-break: break-all;"><? echo '&nbsp;'.change_date_format($gate_in_date); ?></td>
							<td width="50" align="center" style="word-break: break-all;"><?if($gate_in_time){ echo date('h:i a', strtotime($gate_in_time));} ?></td>
							<td width="60" align="center" style="word-break: break-all;"><? echo $yes_no[$val[csf("returnable")]]; ?></td>
							<td width="80" align="center" style="word-break: break-all;"><? echo '&nbsp;'.change_date_format($val[csf("est_return_date")]); ?></td>
							<td width="80" align="center" style="word-break: break-all;"><? echo '&nbsp;'.change_date_format($act_date); ?></td>
							<td width="130" style="word-break: break-all;"><p><? echo $item_category[$val[csf("item_category_id")]]; ?></p></td>
							<td width="150" style="word-break: break-all;"><p><? echo  $val[csf("item_description")]; ?></p></td>
							<td width="50" align="center" style="word-break: break-all;"><? echo $unit_of_measurement[$val[csf("uom")]]; ?></td>
							<td width="80" align="right" style="word-break: break-all;"><p><? echo $val[csf("quantity")]; ?></p></td>						
							<td width="80" align="right" style="word-break: break-all;"><p><? echo $reject_qty; ?></p></td>
							<td width="80" align="right" style="word-break: break-all;"><p><? echo $val[csf("rate")]; ?></p></td>
							<td width="80" align="right" style="word-break: break-all;"><p><? echo $val[csf("amount")]; ?></p></td>
							<?
							if($val[csf("returnable")]==1)
							{
								?>
								<td width="80" align="right" style="word-break: break-all;" title="<? echo $val[csf("sys_number")]."**".$val[csf("item_category_id")]."**".$val[csf("item_description")] ?>"><a href="##" onClick="fnc_get_qty_details('<? echo $val[csf("sys_number")]; ?>', 'return_qty_popup', 'Return Quantity')"><? echo $ret_qty; ?></a></td>
								<?
							}
							else
							{
								?>
								<td width="80" align="right" style="word-break: break-all;" title="<? echo $val[csf("sys_number")]."**".$val[csf("item_category_id")]."**".$val[csf("item_description")] ?>"><p>N/A</p></td>
								<?
							}
							?>
							<td width="80" align="right" style="word-break: break-all;" ><p><? if($val[csf("returnable")]==1)echo number_format($val[csf("quantity")]-$ret_qty,4); ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $val[csf("sent_by")]; ?></p></td>
							
							<td width="100" style="word-break: break-all;"><p><? echo $send_to.' '.$to_location; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $val[csf("attention")]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $buyer_name_arr[$order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['buyer']];
							//echo $buyer_name_arr[$val[csf("buyer_id")]]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $val[csf("buyer_order")]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['style']; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $val[csf("issue_purpose")]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $val[csf("carried_by")]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $user_arr[$val[csf("inserted_by")]]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $val[csf("vhicle_number")]; ?></p></td>
							<td style="word-break: break-all;"><p><? echo $val[csf("remarks")]; ?></p></td>
						</tr>
						<?
						$tot_quantity+=$val[csf("quantity")];
						$tot_reject_qty+=$reject_qty;
						$tot_amt+=$val[csf("amount")];
						if($val[csf("returnable")]==1){$tot_retn_quantity+=$ret_qty;}
						if($val[csf("returnable")]==1){$tot_yet_to_rtnQnty+=$val[csf("quantity")]-$ret_qty;}
						
						$tot_uom_qty+=$val[csf("uom_qty")];
						$i++;
						}
					?>   
					</tbody>
					<tfoot>
						<th colspan="11">&nbsp;</th>
						<th></th>
						<th></th>
						<th><? //echo $tot_uom_qty;?></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
                        <th>  Total </th>						
                        <th><? echo number_format($tot_quantity,4); ?> </th>
                        <th><? echo number_format($tot_reject_qty,2); ?> </th>
                        <th></th>
                        <th><? echo number_format($tot_amt,2); ?> </th>
						<th><? echo number_format($tot_retn_quantity,2); ?> </th>
						<th><? echo number_format($tot_yet_to_rtnQnty,4); ?> </th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tfoot>
				</table>
			</div>
	    </div>
		</fieldset>	
		<?	
		}
	}
	else if($report_type==2) //Gate Out Pending...
	{
		$department_arr=return_library_array( "select id, department_name from lib_department where status_active=1 and is_deleted=0",'id','department_name');
		$section_arr=return_library_array( "select id, section_name from lib_section where status_active=1 and is_deleted=0",'id','section_name');
		$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
		 ?>
               <fieldset style="width:2560px;">
               <div style="width:2560px;">
			   <table width="2560" border="0" cellpadding="0" cellspacing="0" align="left">
				<tr style="border:none;">
					<th colspan="23" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
				</tr>
				</table>
				<? ob_start();?>
                 <table width="2440"  cellpadding="0" cellspacing="0" border="0"  align="left">											 					<tr>
                    <td colspan="23" align="center" style="font-size:16px; font-weight:bold" >Gate Out Pending </td>							
                    </tr>
                    <tr>
                    <td colspan="23" align="center" style="font-size:14px;">
                    <? if(str_replace("'","",$cbo_company_name)!=0) { ?>
                     <? echo 'Company Name :'. $company_arr[str_replace("'","",$cbo_company_name)];
                    } else {echo '';};
                    ?>	
                    </td>
                    </tr>
                </table>
                <table width="2560" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left"> 
                <thead>
                    <tr>
                    <th width="30">SL</th>
                    <th width="120">Gate Pass No</th>
                    <th width="120">Sending Company [Location]</th>
					<th width="120">Delivery Company</th>
                    <th width="150">Basis</th>
                    <th width="100" >Department</th>
                    <th width="100" >Section</th>
                    <th width="80" >Prepared Date</th>
                    <th width="80">Prepared Time</th>
                    <th width="120">Sample Type</th>
                    <th width="120">Item Category</th>
                    <th width="150">Description</th>
                    <th width="80">Quantity</th>
                    <th width="60">UOM</th>
                    <th width="100">Sent By</th>
                    <th width="100">Sent to</th>
                    <th width="100">Attention</th>
                    <th width="60" style="word-break: break-all;">Return able</th>
                    <th width="80">Est.Return Date</th>
                    <th width="110">Buyer Order</th>
                    <th width="90">Buyer</th>
                    <th width="100">Style</th>
                    <th width="100">Purpose</th>
                    <th width="100">Carried By</th>
                    <th width="100">Insert By</th>
                    <th>Remarks</th>
                </tr>
                </thead>
                </table> 
				<div style="width:2580px; overflow-y: scroll; max-height:350px;" id="scroll_body" align="left">
                <table width="2560" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
                <tbody>
                <?
               /* $sql_data=sql_select("select out_date, gate_pass_id,out_time  from  inv_gate_out_scan where status_active=1 and is_deleted=0");
                $gate_scan_array=array();$issue_knit_array=array();
                foreach($sql_data as $row)
                {
                $gate_scan_array[$row[csf('gate_pass_id')]]['out_date']=$row[csf('out_date')];
                $gate_scan_array[$row[csf('gate_pass_id')]]['out_time']=$row[csf('out_time')];
                } 
                $sql_issue_dtls=sql_select("select issue_number,knit_dye_source,knit_dye_company,issue_purpose from inv_issue_master where    and status_active=1 and is_deleted=0");
                $gate_scan_array=array();
                foreach($sql_issue_dtls as $row)
                {
                $issue_knit_array[$row[csf('issue_number')]]['knit_dye_source']=$row[csf('knit_dye_source')];
                $issue_knit_array[$row[csf('issue_number')]]['knit_dye_company']=$row[csf('knit_dye_company')];
                } */
                
                $i=$k=1;$total_out=0;$temp_arr=array();
                //count($get_out_data);
                $sql_pending="select a.id,a.company_id,a.sys_number,a.sys_number_prefix_num,a.sent_by, a.department_id, a.section, a.inserted_by, a.issue_purpose, a.sent_to, a.attention, a.basis, a.within_group, a.out_date,a.returnable, a.est_return_date, a.challan_no, a.carried_by, b.buyer_order, b.buyer_order_id, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, a.com_location_id, a.delivery_company
                from inv_gate_pass_mst a, inv_gate_pass_dtls b	
                where a.id=b.mst_id  and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and a.is_approved in(0,1) and a.sys_number not in(select a.gate_pass_id from inv_gate_out_scan a where  a.status_active=1  and a.is_deleted=0 ) $company_conds $out_date_cond $item_category_cond $sample_cond $challan_sys_cond $withingroup_cond $location_cond $gate_pass_cond4  $returnable_conds order by b.id";
              //echo $sql_pending; 
                $get_pending_data=sql_select($sql_pending);	
                foreach($get_pending_data as $val)
                {
                if ($i%2==0)
                $bgcolor="#E9F3FF";
                else
                $bgcolor="#FFFFFF";
                //$gate_out_date=$gate_scan_array[$val[csf('sys_number')]]['out_date'];
               // $gate_out_time=$gate_scan_array[$val[csf('sys_number')]]['out_time'];
                //$knit_dye_source=$issue_knit_array[$val[csf('challan_no')]]['knit_dye_source'];
                //$knit_dye_company=$issue_knit_array[$val[csf('challan_no')]]['knit_dye_company'];
                // $val[csf("time_hour")] $val[csf("challan_no")]
                $basis=$val[csf("basis")];  
                $within_group=$val[csf("within_group")];
				$sending_company=$company_arr[$val[csf("company_id")]];
                if($within_group==1)
                {
					if($basis==12)
					{
						$to_company=$supplier_arr[$val[csf("sent_to")]];
					}
					else
					{
						$to_company=$company_arr[$val[csf("sent_to")]];
					}
						
                }
				else
				{
					 $to_company=$val[csf("sent_to")];	
				}
                
                $challan_no=$val[csf("challan_no")];
                //$company_arr,$supplier_arr;
               if($val[csf("com_location_id")])$com_location='['.$location_arr[$val[csf("com_location_id")]].']';

			   $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");
			   $print_report_format_arr=explode(',',$print_report_format);
			   $purces_req_button_id=$print_report_format_arr[0];
			   
			   
			    ?>
                <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('troutp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="troutp_<? echo $i; ?>">
                    <?
                    if(!in_array($val[csf("sys_number")],$temp_arr))
                    {
                        $temp_arr[]=$val[csf("sys_number")];
                        ?>
                        <td width="30" align="center" style="word-break: break-all;"><p><? echo $k; ?></p></td>
                        <?
                        $k++;
                    }
                    else
                    {
                        ?>
                        <td width="30" style="word-break: break-all;"></td>
                        <?
                    }
                    ?>
                    
                    <td width="120" style="word-break: break-all;"><p><a href='#report_details' onClick="fnc_get_pass_print('<? echo $purces_req_button_id ?>','<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>',1,'','<? echo $val[csf('com_location_id')]; ?>');"><? echo $val[csf('sys_number')]; ?></a></p></td>
                    <td width="120" style="word-break: break-all;"><p><? echo $sending_company.'<br>'.$com_location; ?></p></td>
					<td width="120" style="word-break: break-all;"><p><? echo $val[csf("delivery_company")]; ?></p></td>
                    <td width="150" style="word-break: break-all;"><p><? echo $get_pass_basis[$val[csf("basis")]]; ?></p></td>
                    <td width="100" style="word-break: break-all;"><p><? echo $department_arr[$val[csf("department_id")]]; ?></p></td>
                    <td width="100" style="word-break: break-all;"><p><? echo $section_arr[$val[csf("section")]]; ?></p></td>
                    <td width="80" style="word-break: break-all;"><p><? echo '&nbsp;'.change_date_format($val[csf("out_date")]); ?></p></td>
                    <td width="80" align="center" style="word-break: break-all;"><p><? echo  $val[csf("time_hour")].':'.$val[csf("time_minute")];?></p></td>
                    <td width="120" style="word-break: break-all;"><p><? echo $sample_arr[$val[csf("sample_id")]]; ?></p></td>
                    <td width="120" style="word-break: break-all;"><p><? echo $item_category[$val[csf("item_category_id")]]; ?></p></td>
                    <td width="150" style="word-break: break-all;"><p><? echo  $val[csf("item_description")]; ?></p></td>
                    <td width="80" align="right" style="word-break: break-all;"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
                    <td width="60" align="center" style="word-break: break-all;"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?></p></td>							<td width="100" align="center"><p><? echo $val[csf("sent_by")]; ?></p></td>
                    <td width="100" align="center" style="word-break: break-all;"><p><? echo $to_company; ?></p></td>
                    <td width="100" align="center" style="word-break: break-all;"><p><? echo $val[csf("attention")]; ?></p></td>
                    <td width="60" align="center" style="word-break: break-all;"><p><? echo  $yes_no[$val[csf("returnable")]]; ?></p></td>
                    <td width="80" style="word-break: break-all;"><p><?  echo  '&nbsp;'.change_date_format($val[csf("est_return_date")]); ?></p></td>								<td width="110" align="center"><p><? echo $val[csf("buyer_order")]; ?></p></td>
                    <td width="90" align="center" style="word-break: break-all;"><p><? echo $buyer_name_arr[$order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['buyer']]; ?></p></td>
                    <td width="100" align="center" style="word-break: break-all;"><p><? echo $order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['style']; ?></p></td>                 
                    <td width="100" align="center"><p><? echo $val[csf("issue_purpose")]; ?></p></td>
                    <td width="100" align="center" style="word-break: break-all;"><p><? echo $val[csf("carried_by")]; ?></p></td>
                    <td width="100" align="center" style="word-break: break-all;"><p><? echo $user_arr[$val[csf("inserted_by")]]; ?></p></td>
                    <td width="" style="word-break: break-all;"><p><? echo $val[csf("remarks")]; ?></p></td>
                </tr>
                <?
                $tot_quantity+=$val[csf("quantity")];
                $tot_uom_qty+=$val[csf("uom_qty")];
                $i++;
                }
                ?>   
                </tbody>
                <tfoot>
                     <th colspan="11">  <? if($cbo_item_cat!=0){ ?> Total <? } ?></th>
                    <? 
					if($cbo_item_cat!=0){ 
					?> 
                    <th align="right"><? echo number_format($tot_quantity,2);?> </th> 
					<? }
					else 
					{ ?>
                    <th></th> 
                    <? }?>
                    <th></th> 
                    <th><? //echo $tot_uom_qty;?></th> 
                    <th></th> 
                    <th></th> 
                    <th></th> 
                    <th></th>
                    <th></th> 
                    <th></th>
                    <th></th> 
                    <th></th> 
                    <th></th> 
                    <th></th>
                </tfoot>
                </table>
                </div>
                </div>
               </fieldset>
		<?
	}
	elseif($report_type==3)  // Gate In
	{
		$lib_buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
		$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
		$lib_other_party_arr=return_library_array( "select id, other_party_name from lib_other_party where status_active=1 and is_deleted=0",'id','other_party_name');

	 	?>
               <fieldset style="width:2580px;">
               <div style="width:2580px;">
			   <table width="2580" border="0" cellpadding="0" cellspacing="0" align="left">
				<tr style="border:none;">
					<th colspan="21" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
				</tr>
				</table>
				<? ob_start();?>
                 <table width="2580"  cellpadding="0" cellspacing="0" border="0"  align="left">											 					<tr>
                    <td colspan="21" align="center" style="font-size:16px; font-weight:bold" >Gate Entry Report</td>							
                    </tr>
                    <tr>
                    <td colspan="21" align="center" style="font-size:14px;">
                    <? if(str_replace("'","",$cbo_company_name)!=0) { ?>
                     <? echo 'Company Name :'. $company_arr[str_replace("'","",$cbo_company_name)];
                    } else {echo '';};
                    ?>	
                    </td>
                    </tr>					
                </table>
                <table width="2560" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left"> 
                <thead>
                   <tr>
                    <th width="30">SL</th>
                    <th width="120">Gate In No</th>
                    <th width="120">Company Name [Location]</th>
                    <th width="80" >In Date</th>
                    <th width="80">In Time</th>
                    <th width="120">Gate Pass No</th>
                    <th width="120">From Company [Location]</th>
                    <th width="100">Receive From</th>
                    <th width="80">Out Date</th>
                    <th width="70">Return able</th>
                    <th width="120">Sample Type</th>
                    <th width="120">Item Category</th>
                    <th width="150">Description</th>
                    <th width="80">Receive Qty</th>
                    <th width="80">Reject Qty</th>
                    <th width="80">Rate</th>
                    <th width="80">Amount</th>
                    <th width="60">UOM</th>
                    <th width="90">Buyer</th>
                    <th width="110">Buyer Order</th>
                    <th width="100">Style</th>
                    <th width="100">Challan No</th>
                    <th width="100">Carried By</th>
                    <th width="100">Insert By</th>
                    <th width="130">Date and Time</th>
                    <th>Remarks</th>
                </tr>
                </thead>
                </table> 
				<div style="width:2580px; overflow-y: scroll; max-height:350px;" id="scroll_body" align="left">
                <table width="2560" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
                <tbody>
                <?
               
                
                $i=$k=1;$total_out=0;$temp_arr=array();
                //count($get_out_data);
				$sql_gate_in="SELECT a.sys_number as SYS_NUMBER,a.company_id as COMPANY_ID,a.com_location_id as COM_LOCATION_ID,a.in_date as IN_DATE,a.time_hour as TIME_HOUR,a.time_minute as TIME_MINUTE,	a.gate_pass_no as GATE_PASS_NO 	,a.sending_company as SENDING_COMPANY,a.out_location_id as OUT_LOCATION_ID,a.out_date as OUT_DATE,a.returnable as RETURNABLE,a.challan_no as CHALLAN_NO,a.carried_by as CARRIED_BY,a.within_group as WITHIN_GROUP, a.inserted_by as INSERTED_BY, a.insert_date as INSERT_DATE, b.sample_id as SAMPLE_ID,b.item_category_id as ITEM_CATEGORY_ID,b.item_description as ITEM_DESCRIPTION,b.quantity as QUANTITY,b.rate as RATE,b.amount as AMOUNT,b.uom as UOM,b.buyer_order as BUYER_ORDER,b.remarks as REMARKS,a.receive_from as RECEIVE_FROM, b.reject_qty as REJECT_QTY, c.buyer_order_id as BUYER_ORDER_ID,a.party_type as PARTY_TYPE
                from inv_gate_in_mst a, inv_gate_in_dtl b
				left join inv_gate_pass_dtls c on b.get_pass_dtlsid=c.id and c.status_active=1 and c.is_deleted=0 	
                where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $search_by_party_cond $search_by_buyer_cond $company_conds $out_date_cond $item_category_cond $sample_cond $challan_sys_cond $withingroup_cond $location_cond $gate_pass_cond3 $returnable_conds order by b.id";
               
			   //and a.sys_number not in(select a.gate_pass_id from inv_gate_out_scan a where  a.status_active=1  and a.is_deleted=0 ) $company_cond $out_date_cond $item_category_cond $sample_cond $challan_sys_cond $withingroup_cond $location_cond;

                // echo $sql_gate_in; die;
                
				$gate_in_data=sql_select($sql_gate_in);	
                foreach($gate_in_data as $val)
                {
					if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$company=$company_arr[$val["COMPANY_ID"]];
					

					if($val["WITHIN_GROUP"]==1){
						$sending_company=$company_arr[$val["SENDING_COMPANY"]];
					}
					else{
						// $sending_company=$lib_supplier_arr[$val["SENDING_COMPANY"]];
						// if($val["PARTY_TYPE"]==1){
						// $sending_company=$lib_buyer_arr[$val["SENDING_COMPANY"]];
						// }elseif($val["PARTY_TYPE"]==2){
						// 	$sending_company=$lib_supplier_arr[$val["SENDING_COMPANY"]];
						// }else{
						// 	$sending_company=$lib_other_party_arr[$val["SENDING_COMPANY"]];
						// }

						$sending_company=$val["SENDING_COMPANY"];					
					}

				//	 $sending_company=$company_arr[$val["SENDING_COMPANY"]];
					$com_location=$out_location='';
					if($val["COM_LOCATION_ID"])$com_location='['.$location_arr[$val["COM_LOCATION_ID"]].']';
					if($val["OUT_LOCATION_ID"])$out_location='['.$location_arr[$val["OUT_LOCATION_ID"]].']';
				
				
				$challan_no=$val["CHALLAN_NO"];
                //$company_arr,$supplier_arr;
                ?>
                <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('troutp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="troutp_<? echo $i; ?>">
                    <?
                    if(!in_array($val["SYS_NUMBER"],$temp_arr))
                    {
                        $temp_arr[]=$val["SYS_NUMBER"];
                        ?>
                        <td width="30" align="center" style="word-break: break-all;"><p><? echo $k; ?></p></td>
                        <?
                        $k++;
                    }
                    else
                    {
                        ?>
                        <td width="30" style="word-break: break-all;"></td>
                        <?
                    }
                    ?>
                    
                    <td width="120" style="word-break: break-all;"><p><a href='#report_details' onClick="fnc_get_in_print('<? echo $val['COMPANY_ID']; ?>','<? echo $val['SYS_NUMBER']; ?>');"><? echo $val['SYS_NUMBER']; ?></a></p></td>
					<td width="120" style="word-break: break-all;"><? echo $company.' '.$com_location; ?></td>
                    <td width="80" align="center" style="word-break: break-all;"><p><? echo '&nbsp;'.change_date_format($val["IN_DATE"]); ?></p></td>
                    <td width="80" align="center" style="word-break: break-all;"><p><? echo  $val["TIME_HOUR"].':'.$val["TIME_MINUTE"];?></p></td>
                    <td width="120" style="word-break: break-all;"><p><? echo $val["GATE_PASS_NO"]; ?></p></td>
                    <td width="120" style="word-break: break-all;"><? echo $sending_company." ".$out_location ?></td>
                    <td width="100" style="word-break: break-all;"><p><? echo $val["RECEIVE_FROM"]; ?></p></td>
                    <td width="80" align="center" style="word-break: break-all;"><p><? echo  '&nbsp;'.change_date_format($val["OUT_DATE"]); ?></p></td>
                    <td width="70" align="center" style="word-break: break-all;"><p><? echo $yes_no[$val["RETURNABLE"]]; ?></p></td>
                    <td width="120" style="word-break: break-all;"><p><? echo $sample_arr[$val["SAMPLE_ID"]]; ?></p></td>
                    <td width="120" style="word-break: break-all;"><p><? echo $item_category[$val["ITEM_CATEGORY_ID"]]; ?></p></td>
                    <td width="150" style="word-break: break-all;"><p><? echo $val["ITEM_DESCRIPTION"]; ?></p></td>
                    <td width="80" align="right" style="word-break: break-all;"><p><? echo number_format($val["QUANTITY"],2); ?></p></td>
                    <td width="80" align="right" style="word-break: break-all;"><p><? echo number_format($val["REJECT_QTY"],2); ?></p></td>
                    <td width="80" align="right" style="word-break: break-all;"><p><? echo number_format($val["RATE"],2); ?></p></td>
                    <td width="80" align="right" style="word-break: break-all;"><p><? echo number_format($val["AMOUNT"],2); ?></p></td>
                    <td width="60" align="center" style="word-break: break-all;"><p><? echo $unit_of_measurement[$val["UOM"]]; ?></p></td>	
                   
                    <td width="90" align="center" style="word-break: break-all;"><p>
						
						<? if($val["WITHIN_GROUP"]==1){
							 echo $buyer_name_arr[$order_array[$val["BUYER_ORDER_ID"]][$val["BUYER_ORDER"]]['buyer']]; 
							}
						?>
					</p></td>
                    <td width="110" align="center" style="word-break: break-all;"><p><? echo $val["BUYER_ORDER"]; ?></p></td>
                    <td width="100" align="center" style="word-break: break-all;"><p><? echo $order_array[$val["BUYER_ORDER_ID"]][$val["BUYER_ORDER"]]['style']; ?></p></td>                 
                    <td width="100" align="center" style="word-break: break-all;"><p><? echo $val["CHALLAN_NO"]; ?></p></td>
                    <td width="100" align="center" style="word-break: break-all;"><p><? echo $val["CARRIED_BY"]; ?></p></td>
                    <td width="100" style="word-break: break-all;"><p><? echo $userArr[$val["INSERTED_BY"]]; ?></p></td>
                    <td width="130" style="word-break: break-all;"><p><? echo $val["INSERT_DATE"]; ?></p></td>
                    <td width="" style="word-break: break-all;"><p><? echo $val["REMARKS"]; ?></p></td>
                </tr>
                <?
                $tot_quantity+=$val["QUANTITY"];
                $reject_qty+=$val["REJECT_QTY"];
                $tot_amt+=$val["AMOUNT"];
                $i++;
                }
                ?>   
                </tbody>
                <tfoot>
                    <th colspan="13">  <? if($cbo_item_cat!=0){ ?> Total <? } ?></th>
                    <? 
					if($cbo_item_cat!=0){ 
					?> 
                    <th align="right" id="td_total_qty"><? echo number_format($tot_quantity,2);?> </th>
                    <th align="right" id="td_total_rej_qty"><? echo number_format($reject_qty,2);?> </th>
                    <th></th> 
                    <th align="right" id="td_total_amt"><? echo number_format($tot_amt,2);?> </th>
					<? }
					else 
					{ ?>
                    <th></th> 
                    <th></th> 
                    <th></th> 
                    <? }?>
                    <th></th> 
                    <th></th> 
                    <th></th> 
                    <th></th>
                    <th></th> 
                    <th></th> 
                    <th></th>
                    <th></th>
                    <th></th>
                </tfoot>
                </table>
                </div>
                </div>
               </fieldset>
		<?
	}
	elseif($report_type==4)  // Gate Out 2
	{
		$department_arr=return_library_array( "select id, department_name from lib_department where status_active=1 and is_deleted=0",'id','department_name');
		$section_arr=return_library_array( "select id, section_name from lib_section where status_active=1 and is_deleted=0",'id','section_name');
		$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');

		$grmts_del_print_rpt_format_sql=sql_select("SELECT FORMAT_ID,TEMPLATE_NAME from lib_report_template where module_id=7 and report_id=86 $template_conds and is_deleted=0 and status_active=1");
		foreach($grmts_del_print_rpt_format_sql as $row)
		{
			$grmts_del_format_arr=explode(",",$row["FORMAT_ID"]);
			$grmts_del_format_id[$row["TEMPLATE_NAME"]]=$grmts_del_format_arr[0];
		}

		$sql_data=sql_select("SELECT a.id,a.gate_pass_no,a.company_id,a.com_location_id,a.in_date,a.time_hour,a.time_minute,b.quantity,b.item_category_id,b.item_description,b.reject_qty,b.get_pass_dtlsid from inv_gate_in_mst a,inv_gate_in_dtl b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $gate_pass_cond_one $gate_pass_cond3 group by a.id,a.gate_pass_no,a.company_id,a.com_location_id,a.in_date,a.time_hour,a.time_minute,b.quantity,b.item_category_id,b.item_description,b.reject_qty,b.get_pass_dtlsid order by a.id");

		$gate_in_array=array();
		foreach($sql_data as $row)
		{
			if($tem_id[$row[csf('gate_pass_no')]]==''){
				$gate_in_array[$row[csf('gate_pass_no')]]['in_date']=$row[csf('in_date')];
				$gate_in_array[$row[csf('gate_pass_no')]]['company_id']=$row[csf('company_id')];
				$gate_in_array[$row[csf('gate_pass_no')]]['com_location_id']=$row[csf('com_location_id')];
				$gate_in_array[$row[csf('gate_pass_no')]]['reject_qty']+=$row[csf('reject_qty')];
				$gate_in_array[$row[csf('gate_pass_no')]]['in_time']=$row[csf('time_hour')].':'.$row[csf('time_minute')];
				$tem_id[$row[csf('gate_pass_no')]]=1;
				//$ret_qty_array[$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('quantity')];
				$ret_qty_array[$row[csf('get_pass_dtlsid')]][$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('quantity')];
				//$rej_qty_array[$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]=$row[csf('reject_qty')];
				$rej_qty_array[$row[csf('get_pass_dtlsid')]][$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('reject_qty')];
				$ret_com_array[$row[csf('gate_pass_no')]]=$row[csf('company_id')];

			}
			else
			{
				//$ret_qty_array[$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('quantity')];
				$ret_qty_array[$row[csf('get_pass_dtlsid')]][$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('quantity')];
				//$rej_qty_array[$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]=$row[csf('reject_qty')];
				$rej_qty_array[$row[csf('get_pass_dtlsid')]][$row[csf('gate_pass_no')]][$row[csf('item_category_id')]][$row[csf('item_description')]]+=$row[csf('reject_qty')];
				$ret_com_array[$row[csf('gate_pass_no')]]=$row[csf('company_id')];
			}
		} 
	
		//var_dump($ret_qty_array);
	
		$sql_out="SELECT a.id,b.id as gpd_id, a.company_id, a.sent_to, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.issue_purpose, a.sent_to, a.returnable, a.est_return_date, a.challan_no, a.carried_by, a.time_hour, a.time_minute, a.get_pass_no, a.basis, a.within_group, a.com_location_id, a.location_id, a.department_id, a.section, a.attention, a.vhicle_number, a.delivery_company, a.inserted_by,a.issue_id, b.buyer_id, b.buyer_order, b.buyer_order_id, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.rate, b.amount, b.uom, b.uom_qty, b.remarks, c.out_date, c.out_time from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_out_scan c where a.id=b.mst_id and c.gate_pass_id=a.sys_number and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_conds $out_date_cond_scan $item_category_cond $sample_cond $challan_sys_cond $withingroup_cond $location_cond $po_cond $gate_pass_cond_two $gate_pass_cond4 $returnable_conds order by b.id"; 
		//echo $sql_out; die;
		$get_out_data=sql_select($sql_out);		
		if(count($get_out_data)>0)
		{
			$grmts_del_challan=array();
			foreach($get_out_data as $val)
			{
				if($val[csf('basis')]==12)
				{
					$grmts_del_arr=explode(',',$val[csf("challan_no")]);
					foreach($grmts_del_arr as $row)
					{
						$grmts_del_challan[$row]=$row;
					}
				}
			}
			if(count($grmts_del_challan)>0)
			{
				$grmts_del_challan_in=where_con_using_array($grmts_del_challan,1,"sys_number");
				$grmts_del_sql=sql_select("SELECT sys_number as SYS_NUMBER,company_id as COMPANY_ID, id as ID, delivery_date as DELIVERY_DATE, delivery_company_id as DELIVERY_COMPANY_ID from pro_ex_factory_delivery_mst where entry_form!=85 $grmts_del_challan_in");
				$grmts_del_info=array();
				foreach($grmts_del_sql as $row)
				{
					$grmts_del_info[$row["SYS_NUMBER"]]["id"]=$row["ID"];
					$grmts_del_info[$row["SYS_NUMBER"]]["challan_no"]=$row["SYS_NUMBER"];
					$grmts_del_info[$row["SYS_NUMBER"]]["company_id"]=$row["COMPANY_ID"];
					$grmts_del_info[$row["SYS_NUMBER"]]["delivery_date"]=$row["DELIVERY_DATE"];
					$grmts_del_info[$row["SYS_NUMBER"]]["del_company_id"]=$row["DELIVERY_COMPANY_ID"];
				}
			}
			?>
			<fieldset style="width:3550;">
			<div style="width:3550;">
			<table width="3540" border="0" cellpadding="0" cellspacing="0" align="left">
				<tr style="border:none;">
					<th colspan="31" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
				</tr>
				</table>
				<? ob_start();?>
				<table width="3540" border="0" cellpadding="0" cellspacing="0" align="left"> 
							<tr style="border:none;">
								<td colspan="31" align="center" style="border:none;font-size:16px; font-weight:bold" >
									Gate Out Report
								</td> 
							</tr>
							<tr>
								<td colspan="31" align="center" style="border:none; font-size:14px;">
									Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
								</td>
							</tr>							
					</table>
					<table width="3540" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left"> 
						<thead>
							<tr>
								<th width="30">SL</th>
								<th width="120">Gate Pass No</th>
								<th width="100">Challan No</th>
								<th width="130">Sending Company</th>
								<th width="130">Delivery Company</th>
								<th width="100">Location</th>
								<th width="150">Department</th>
								<th width="150">Section</th>
								<th width="80">Out date</th>
								<th width="80">Out Time</th>
								
								<th width="100">Rcv Company [Location]</th>
								<th width="80">Rcv. date</th>
								<th width="50">Rcv. Time</th>
								
								<th width="60" style="word-break: break-all;">Return able</th>
								<th width="80">Est.Return Date</th>
								<th width="80">Act.Return Date</th>
								<th width="130">Item Category</th>
								<th width="150">Description</th>
								<th width="50">UOM</th>
								<th width="80">Quantity</th>
								<th width="80">Reject Qty</th>
								<th width="80">Rate</th>
								<th width="80">Amount</th>
								<th width="80">Return Qty.</th>
								<th width="80">Not Yet Return</th>
								<th width="100">Sent By</th>
								<th width="100">Sent to</th>
								<th width="100">Attention</th>
								<th width="100">Buyer</th>
								<th width="100">Buyer Order</th>
								<th width="100">Style</th>
								<th width="100">Purpose</th>
								<th width="100">Carried By</th>
								<th width="100">Insert By</th>
								<th width="100">Vehicle Number</th>
								<th>Remarks</th>
							</tr>
						</thead>
				</table> 
				<div style="width:3580px; overflow-y: scroll; max-height:250px;" id="scroll_body" align="left">
					<table width="3540" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
						<tbody>
						<?
						$i=$k=1;$total_out=0;$temp_arr=array(); $tot_reject_qty=0;
						foreach($get_out_data as $val)
						{
							$act_date='';$ret_qty='';$send_to='';
							
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							$gate_in_com=$gate_in_array[$val[csf('sys_number')]]['company_id'];
							
							$gate_in_date=$gate_in_array[$val[csf('sys_number')]]['in_date'];
							$gate_in_time=$gate_in_array[$val[csf('sys_number')]]['in_time'];
							//$reject_qty=$gate_in_array[$val[csf('sys_number')]]['reject_qty'];
							
							if($ret_com_array[$val[csf('sys_number')]]==$val[csf("company_id")])
							{
								$act_date=$gate_in_date;
								//$ret_qty=$ret_qty_array[$val[csf('sys_number')]][$val[csf("item_category_id")]][$val[csf('item_description')]];
								$ret_qty=$ret_qty_array[$val[csf('gpd_id')]][$val[csf('sys_number')]][$val[csf("item_category_id")]][$val[csf('item_description')]];
		
							}
							
							$reject_qty=$rej_qty_array[$val[csf('gpd_id')]][$val[csf('sys_number')]][$val[csf("item_category_id")]][$val[csf('item_description')]];
							
							
							if($val[csf('within_group')]==2){$send_to=$val[csf("sent_to")];} //$supplier_arr[$val[csf("sent_to")]];
							else{$send_to=$company_arr[$val[csf("sent_to")]];}
							
							//else if($val[csf('basis')]==12){$send_to=$supplier_arr[$val[csf("sent_to")]];}

							
							$rec_location=$com_location=$to_location='';
							if($val[csf("com_location_id")])$com_location=$location_arr[$val[csf("com_location_id")]];
							if($gate_in_array[$val[csf('sys_number')]]['com_location_id'])$rec_location='['.$location_arr[$gate_in_array[$val[csf('sys_number')]]['com_location_id']].']';
							if($val[csf("location_id")])$to_location='['.$location_arr[$val[csf("location_id")]].']';

							$challan_no="";
							if($val[csf('basis')]==12)
							{
								$grmts_del_arr=explode(',',$val[csf("challan_no")]);
								foreach($grmts_del_arr as $row)
								{
									if($grmts_del_info[$row]["id"]!="")
									{
										$challan_no.='<a href="#report_details" onClick="fnc_grmts_del_print(\''.$grmts_del_format_id[$grmts_del_info[$row]["company_id"]].'\',\''.$grmts_del_info[$row]["company_id"].'\',\''.$grmts_del_info[$row]["id"].'\',\''.$grmts_del_info[$row]["delivery_date"].'\',\''.$grmts_del_info[$row]["del_company_id"].'\');">'.$row.'</a>&nbsp;';
									}
									else
									{
										$challan_no.=$row."&nbsp;";
									}
								}
							}
							else
							{
								$challan_no=$val[csf("challan_no")];
							}
							
							?>
							<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trout_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trout_<? echo $i; ?>">
								<?
								if($temp_arr[$val[csf('sys_number')]]=="")
								{
									$temp_arr[$val[csf('sys_number')]]=$val[csf('sys_number')];
									?>
									<td width="30" align="center" style="word-break: break-all;"><p><? echo $k; ?></p></td>
									<?
									$k++;
								}
								else
								{
									?>
									<td width="30" style="word-break: break-all;"></td>
									<?
								}
								?>
								<td width="120" align="center" style="word-break: break-all;">
									<p><a href='#report_details' onClick="fnc_gate_pass_print('<? echo $gate_pass_format_id[$val[csf('company_id')]]; ?>','<? echo $val[csf('company_id')]; ?>','<? echo $val[csf('sys_number')]; ?>','<? echo $val[csf('basis')]; ?>','<? echo $val[csf('com_location_id')]; ?>','<? echo $val[csf('challan_no')]; ?>','<? echo $val[csf('issue_id')]; ?>','<? echo $val[csf('RETURNABLE')]; ?>');"><? echo $val[csf('sys_number')]; ?></a></p>
								</td>
								<td width="100" style="word-break: break-all;"><p><? echo $challan_no; ?></p></td>
								<td width="130" style="word-break: break-all;"><p><? echo $company_arr[$val[csf("company_id")]]; ?></p></td>
								<td width="130" style="word-break: break-all;"><p><? echo $val[csf("delivery_company")]; ?></p></td>
								<td width="100" style="word-break: break-all;"><p><? echo $com_location; ?></p></td>

								<td width="150" style="word-break: break-all;"><p><? echo $department_arr[$val[csf("department_id")]]; ?></p></td>
								<td width="150" style="word-break: break-all;"><p><? echo $section_arr[$val[csf("section")]]; ?></p></td>

								<td width="80" align="center" style="word-break: break-all;"><? echo '&nbsp;'.change_date_format($val[csf("out_date")]); ?></td>
								<td width="80" align="center" style="word-break: break-all;"><? echo $val[csf("out_time")]; ?></td>
								<td width="100" style="word-break: break-all;"><? echo $company_arr[$gate_in_com].' '.$rec_location;?></td>
								<td width="80" align="center" style="word-break: break-all;"><? echo '&nbsp;'.change_date_format($gate_in_date); ?></td>
								<td width="50" align="center" style="word-break: break-all;"><?if($gate_in_time){ echo date('h:i a', strtotime($gate_in_time));} ?></td>
								<td width="60" align="center" style="word-break: break-all;"><? echo $yes_no[$val[csf("returnable")]]; ?></td>
								<td width="80" align="center" style="word-break: break-all;"><? echo '&nbsp;'.change_date_format($val[csf("est_return_date")]); ?></td>
								<td width="80" align="center" style="word-break: break-all;"><? echo '&nbsp;'.change_date_format($act_date); ?></td>
								<td width="130" style="word-break: break-all;"><p><? echo $item_category[$val[csf("item_category_id")]]; ?></p></td>
								<td width="150" style="word-break: break-all;"><p><? echo  $val[csf("item_description")]; ?></p></td>
								<td width="50" align="center" style="word-break: break-all;"><? echo $unit_of_measurement[$val[csf("uom")]]; ?></td>
								<td width="80" align="right" style="word-break: break-all;"><p><? echo $val[csf("quantity")]; ?></p></td>
								<td width="80" align="right" style="word-break: break-all;"><p><? echo $reject_qty; ?></p></td>
								<td width="80" align="right" style="word-break: break-all;"><p><? echo $val[csf("rate")]; ?></p></td>
								<td width="80" align="right" style="word-break: break-all;"><p><? echo $val[csf("amount")]; ?></p></td>
								<td width="80" align="right" style="word-break: break-all;" title="<? echo $val[csf("sys_number")]."**".$val[csf("item_category_id")]."**".$val[csf("item_description")] ?>"><p><? if($val[csf("returnable")]==1)echo $ret_qty; else echo "N/A"; ?></p></td>
								<td width="80" align="right" style="word-break: break-all;" ><p><? if($val[csf("returnable")]==1)echo number_format($val[csf("quantity")]-$ret_qty,4); ?></p></td>
								<td width="100" style="word-break: break-all;"><p><? echo $val[csf("sent_by")]; ?></p></td>
								
								<td width="100" style="word-break: break-all;"><p><? echo $send_to.' '.$to_location; ?></p></td>
								<td width="100" style="word-break: break-all;"><p><? echo $val[csf("attention")]; ?></p></td>
								<td width="100" style="word-break: break-all;"><p><? echo $buyer_name_arr[$order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['buyer']];
								//echo $buyer_name_arr[$val[csf("buyer_id")]]; ?></p></td>
								<td width="100" style="word-break: break-all;"><p><? echo $val[csf("buyer_order")]; ?></p></td>
								<td width="100" style="word-break: break-all;"><p><? echo $order_array[$val[csf("buyer_order_id")]][$val[csf("buyer_order")]]['style']; ?></p></td>
								<td width="100" style="word-break: break-all;"><p><? echo $val[csf("issue_purpose")]; ?></p></td>
								<td width="100" style="word-break: break-all;"><p><? echo $val[csf("carried_by")]; ?></p></td>
								<td width="100" style="word-break: break-all;"><p><? echo $user_arr[$val[csf("inserted_by")]]; ?></p></td>
								<td width="100" style="word-break: break-all;"><p><? echo $val[csf("vhicle_number")]; ?></p></td>
								<td style="word-break: break-all;"><p><? echo $val[csf("remarks")]; ?></p></td>
							</tr>
							<?
							$tot_quantity+=$val[csf("quantity")];
							$tot_reject_qty+=$reject_qty;
							$tot_amt+=$val[csf("amount")];
							if($val[csf("returnable")]==1){$tot_retn_quantity+=$ret_qty;}
							if($val[csf("returnable")]==1){$tot_yet_to_rtnQnty+=$val[csf("quantity")]-$ret_qty;}
							
							$tot_uom_qty+=$val[csf("uom_qty")];
							$i++;
							}
						?>   
						</tbody>
						<tfoot>
							<th colspan="11">&nbsp;</th>
							<th></th>
							<th></th>
							<th><? //echo $tot_uom_qty;?></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th>  Total </th>						
							<th><? echo $tot_quantity; ?> </th>
							<th><? echo $tot_reject_qty; ?> </th>
							<th></th>
							<th><? echo number_format($tot_amt,2); ?> </th>
							<th><? echo $tot_retn_quantity; ?> </th>
							<th><? echo number_format($tot_yet_to_rtnQnty,4); ?> </th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tfoot>
				</table>
				</div>
			</div>
			</fieldset>
		
			<?	
		}
	}
	 
	 
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$report_type";
	exit();
}

if($action=='return_qty_popup') 
{
	echo load_html_head_contents('Popup Info', '../../../', '', '', '');
	extract($_REQUEST);
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", 'id', 'company_name');
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');

	?>
    <table width="850" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="120">Gate In ID</th>
                <th width="120">Gate Pass ID</th>
                <th width="120">Out Company</th>
                <th width="80">In Date</th>
                <th width="100">Challan no</th>
                <th width="80">Qty</th>
                <th>UOM</th>
            </tr>
        </thead>
        <tbody>
		<?
		$sql="select a.id as ID, a.sys_number as GATE_IN_ID, a.gate_pass_no as GATE_PASS_NO, a.in_date as IN_DATE, a.challan_no as CHALLAN_NO, a.sending_company as SENDING_COMPANY, a.within_group as WITHIN_GROUP, b.uom as UOM, b.quantity as QUANTITY from inv_gate_in_mst a, inv_gate_in_dtl b where a.id=b.mst_id and a.gate_pass_no='$gate_pass_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$sql_result=sql_select($sql); 
		$t=1;
		foreach($sql_result as $row)
		{
			if ($t%2==0) $bgcolor="#E9F3FF"; 
			else $bgcolor="#FFFFFF";
			if ($row['WITHIN_GROUP']==1) $sending_company=$companyArr[$row['SENDING_COMPANY']];
			else $sending_company=$supplierArr[$row['SENDING_COMPANY']];
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
        		<td width="30"><p><? echo $t; ?></p></td>
                <td width="120"><p><? echo $row['GATE_IN_ID']; ?></p></td>
				<td width="120"><p><? echo $row['GATE_PASS_NO']; ?></p></td>
				<td width="120"><p><? echo $sending_company; ?></p></td>
				<td width="80" align="center"><p><? echo change_date_format($row['IN_DATE']); ?></p></td>
				<td width="100"><p><? echo $row['CHALLAN_NO']; ?></p></td>
				<td width="80" align="right"><p><? echo $row['QUANTITY']; ?></p></td>
				<td align="center"><p><? echo $unit_of_measurement[$row['UOM']]; ?></p></td>
            </tr>
            <?
            $total_qty += $row['QUANTITY'];
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
               	<th colspan="6">Total</th>
               	<th><? echo $total_qty; ?></th>
				<th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <?
	exit();
}
?>

