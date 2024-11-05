<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_supplier_backup")
{    	 
	//echo create_drop_down( "cbo_supplier", 100, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	echo create_drop_down( "cbo_supplier", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "fn_req_wo_check(this.value);load_drop_down( 'requires/purchase_recap_report3_controller', this.value, 'load_drop_down_category', 'category_td' );",0 );

}

if ($action=="load_drop_down_location")
{    	 
	echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location where company_id='$data' $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down('requires/purchase_recap_report3_controller', this.value+'_'+$data, 'load_drop_down_store','store_td');" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and a.location_id = $data[0] and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"");
	exit();
}

if ($action=="load_drop_down_supplier")
{
	$ex_data = explode('_',$data);
	$company=$ex_data[0];
	$item_category=$ex_data[1];
	$supplier=$ex_data[2];

	if($item_category==0)
	{
		echo create_drop_down( "cbo_supplier", 160, $blank_array,'', 1, '-- Select Supplier --',0,'',0);
	}
	else if($item_category==1)
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,"fn_req_wo_check(this.value);",0);
	}
	else if($item_category==2 || $item_category==3 || $item_category==13 || $item_category==14)
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type =9 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,"fn_req_wo_check(this.value);",0);
	}
	else if($item_category==4)
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,"fn_req_wo_check(this.value);",0);

	}
	else if($item_category==5 || $item_category==6 || $item_category==7)
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type=3 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,"fn_req_wo_check(this.value);",0);
	}
	else if($item_category==9 || $item_category==10)
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type = 6 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,"fn_req_wo_check(this.value);",0);
	}
	else if($item_category==11)
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type = 8 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,"fn_req_wo_check(this.value);",0);
	}
	else if($item_category==12 || $item_category==24 || $item_category==25)
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type in(20,21,22,23,24,30,31,32,35,36,37,38,39) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,"fn_req_wo_check(this.value);",0);
	}
	else if($item_category==31)
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type in(26) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,"fn_req_wo_check(this.value);",0);
	}
	else if($item_category==32)
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type in(92) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,"fn_req_wo_check(this.value);",0);
	}
	else
	{
		echo create_drop_down( "cbo_supplier", 160,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,"fn_req_wo_check(this.value);",0);
	}
	exit();
}

if($action == "load_drop_down_category_backup")
{
	$supplier_res = sql_select("select c.supplier_name,c.id, b.party_type from  lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id  and c.status_active=1 and c.is_deleted=0 and c.id = $data order by c.supplier_name");
	foreach ($supplier_res as $val) 
	{
		$party_types[$val[csf("party_type")]] = $val[csf("party_type")];
	}

	$category="";
	if(empty($party_types)) 
	{
		echo create_drop_down( "cbo_item_category_id", 160, $blank_array,"", 1,"-- Select --",0,"" );
	}
	else 
	{	
		if($party_types["7"])
		{
			  
			  $item_category_all =  $item_category;
			  unset($item_category_all["1"]);
			  unset($item_category_all["2"]);
			  unset($item_category_all["3"]);
			  unset($item_category_all["4"]);
			  unset($item_category_all["5"]);
			  unset($item_category_all["6"]);
			  unset($item_category_all["7"]);
			  unset($item_category_all["9"]);
			  unset($item_category_all["10"]);
			  unset($item_category_all["11"]);
			  unset($item_category_all["13"]);
			  unset($item_category_all["14"]);
			  unset($item_category_all["31"]);
			  unset($item_category_all["32"]);
			  $category .= implode(",", array_keys($item_category_all));
		} 

		if( $party_types["2"])
		{
			if($category)  $category .= ",1"; else $category ="1";
		}
		if($party_types["9"])
		{
			if($category)  $category .=",2,3,13,14"; else $category ="2,3,13,14";
		}
		if($party_types["4"] || $party_types["5"])
		{
			if($category)  $category .=",4"; else $category ="4";
			
		}
		if($party_types["3"])
		{
			if($category) $category .=",5,6,7"; else $category ="5,6,7";
		}
		if($party_types["6"])
		{
			if($category) $category .=",9,10"; else $category ="9,10"; 
		}
		if($party_types["8"])
		{
			if($category) $category .=",11"; else $category ="11";  
		}
		if($party_types["20"] || $party_types["21"] ||$party_types["22"] ||$party_types["23"] ||$party_types["24"] ||$party_types["30"] ||$party_types["31"] ||$party_types["32"] ||$party_types["35"] ||$party_types["36"] ||$party_types["37"] ||$party_types["38"] ||$party_types["39"])
		{
			//if($category) $category .=",12,24,25"; else $category ="12,24,25";
		}
		if($party_types["26"])
		{
			//if($category) $category .= ",31"; else $category ="31";
		}
		if($party_types["92"])
		{
			if($category) $category .= ",32"; else $category ="32";
		}
		
		$category_arr1 = explode(",", $category); 
		$category_arr2 =  explode(",", "2,3,12,13,14,24,25,28,30");
		$show_category = array_diff($category_arr1, $category_arr2);
		//print_r($category);die;
		echo create_drop_down( "cbo_item_category_id", 160, $item_category,'', 1, '-- Select --',0,"",0,implode(",",$show_category),'','','');


	}
	//========================================
}

if($action=="wo_no_popup")
{
	echo load_html_head_contents("WO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
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
				//selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					//alert(selected_id);
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_no.splice( i, 1 ); 
			}

			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				//num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			//num 	= num.substr( 0, num.length - 1 );
			//alert(name);
			$('#hide_wo_id').val( id );
			$('#hide_wo_no').val( name ); 
			//$('#hide_wo_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter WO No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_wo_id" id="hide_wo_id" value="" />
                    <input type="hidden" name="hide_wo_no" id="hide_wo_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($cbo_company_name) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		//$search_by_arr=array(1=>"Job No",2=>"Style Ref");
                       		$search_by_arr=array(3=>"WO No");
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $item_category_id; ?>', 'create_wo_no_search_list_view', 'search_div', 'purchase_recap_report3_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="create_wo_no_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$item_category_id)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0){
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}else{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	$sql_cond =" and a.company_id=$company";	
	$sql_cond .=" and a.item_category=$item_category_id";	
	if($search_type==3 && $search_value!=''){
		$sql_cond .=" and a.booking_no_prefix_num='$search_value'";	
	}

	/*else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no='$search_value'";	
	}*/
    if($buyer!=0) $buyer_cond="and a.buyer_id=$buyer"; else $buyer_cond="";
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as job_year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as job_year";
	else $year_field="";

	$sql_wo="select a.id, a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.buyer_id
	from wo_booking_mst a, wo_booking_dtls b
	where a.booking_no=b.booking_no and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $buyer_cond $sql_cond group by a.id, a.booking_no, a.booking_no_prefix_num, a.booking_date, a.buyer_id";

	//and a.booking_type=2 and b.booking_type=2 
	//if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	//$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$year_field from wo_po_details_master a where a.company_name in($company) $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by job_no_prefix_num"; 
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$arr=array(2=>$buyer_arr);
	echo create_list_view("list_view", "WO No,WO Date,Buyer","100,90,160","400","200",0, $sql_wo , "js_set_value", "id,wo_number", "", 1, "0,0,buyer_id", $arr, "wo_number,wo_date,buyer_id", "","setFilterGrid('list_view',-1)","0","",1) ;

	echo "<input type='hidden' id='hide_wo_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='hide_wo_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	//var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}

if($action=="pi_no_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
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
				//selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					//alert(selected_id);
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_no.splice( i, 1 ); 
			}

			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				//num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			//num 	= num.substr( 0, num.length - 1 );
			//alert(name);
			$('#hide_pi_id').val( id );
			$('#hide_pi_no').val( name ); 
			//$('#hide_wo_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter PI No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_pi_id" id="hide_pi_id" value="" />
                    <input type="hidden" name="hide_pi_no" id="hide_pi_no" value="" />
                </thead>
                <tbody>
                	<tr>                                      
                        <td align="center">	
                    	<?
                       		//$search_by_arr=array(1=>"Job No",2=>"Style Ref");
                       		$search_by_arr=array(3=>"PI No");
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $item_category_id; ?>', 'create_pi_no_search_list_view', 'search_div', 'purchase_recap_report3_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="create_pi_no_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$search_type,$search_value,$cbo_year,$item_category_id)=explode('**',$data);

	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0){
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}else{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	$sql_cond =" and a.importer_id=$company";	
	$sql_cond .=" and a.item_category_id=$item_category_id";	
	if($search_type==3 && $search_value!=''){
		$sql_cond .=" and a.pi_number like '%$search_value%'";	
	}


	if($db_type==0) $year_field="YEAR(a.insert_date) as job_year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as job_year";
	else $year_field="";

	$sql_pi="select a.id, a.pi_number
	from com_pi_master_details a
	where a.status_active=1 and a.is_deleted=0  $sql_cond";

	echo create_list_view("list_view", "PI No, System ID","190,160","400","200",0, $sql_pi , "js_set_value", "id,pi_number", "", 1, "0,0", $arr, "pi_number,id", "","setFilterGrid('list_view',-1)","0","",1) ;

	echo "<input type='hidden' id='hide_pi_id' />";
	echo "<input type='hidden' id='hide_pi_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	//var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}

if($action=="lc_no_popup")
{
    echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    <script>
        var selected_id = new Array;
        var selected_name = new Array;
        var selected_no = new Array;
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
                //selected_no.push( str );
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    //alert(selected_id);
                    if( selected_id[i] == selectID ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
                //selected_no.splice( i, 1 );
            }

            var id = ''; var name = ''; var job = ''; var num='';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
                //num += selected_no[i] + ',';
            }
            id 		= id.substr( 0, id.length - 1 );
            name 	= name.substr( 0, name.length - 1 );
            //num 	= num.substr( 0, num.length - 1 );
            //alert(name);
            $('#hide_lc_id').val( id );
            $('#hide_lc_no').val( name );
            //$('#hide_wo_no').val( num );
        }
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter LC No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_lc_id" id="hide_lc_id" value="" />
                    <input type="hidden" name="hide_lc_no" id="hide_lc_no" value="" />
                    </thead>
                    <tbody>
                    <tr>
                        <td align="center">
                            <?
                            //$search_by_arr=array(1=>"Job No",2=>"Style Ref");
                            $search_by_arr=array(4=>"LC No");
                            //$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
                            echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $item_category_id; ?>', 'create_lc_no_search_list_view', 'search_div', 'purchase_recap_report3_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div style="margin-top:15px" id="search_div"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_lc_no_search_list_view")
{
    extract($_REQUEST);
    list($company,$search_type,$search_value,$cbo_year,$item_category_id)=explode('**',$data);

    $company=str_replace("'","",$company);
    $cbo_year=str_replace("'","",$cbo_year);
    if(trim($cbo_year)!=0)
    {
        if($db_type==0){
            $year_cond=" and YEAR(insert_date)=$cbo_year";
        }else{
            $year_cond=" and to_char(insert_date,'YYYY')=$cbo_year";
        }
    }
    $sql_cond =" and importer_id=$company";
    $sql_cond .=" and PI_ENTRY_FORM='".$category_wise_entry_form[$item_category_id]."'";
    if($search_type==4 && $search_value!=''){
        $sql_cond .=" and lc_number like '%$search_value%'";
    }


    if($db_type==0) $year_field="YEAR(insert_date) as job_year";
    else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as job_year";
    else $year_field="";

    $sql_lc="select id, lc_number, btb_prefix_number, importer_id from com_btb_lc_master_details where status_active=1 and is_deleted=0  $sql_cond";
	//echo $sql_lc;
    $company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $arr=array(0=>$company_arr);

    echo create_list_view("list_view", "Company,LC No,System ID","160,190","500","200",0, $sql_lc , "js_set_value", "id,lc_number", "", 1, "importer_id,0,0", $arr, "importer_id,lc_number,btb_prefix_number", "","setFilterGrid('list_view',-1)","0","",1) ;

    echo "<input type='hidden' id='hide_pi_id' />";
    echo "<input type='hidden' id='hide_pi_no' />";
    ?>
    <script language="javascript" type="text/javascript">
        var style_no='<? echo $txt_style_ref_no;?>';
        var style_id='<? echo $txt_style_ref_id;?>';
        //var style_des='<? echo $txt_style_ref;?>';
        //alert(style_id);
        if(style_no!="")
        {
            style_no_arr=style_no.split(",");
            style_id_arr=style_id.split(",");
            style_des_arr=style_des.split(",");
            var str_ref="";
            for(var k=0;k<style_no_arr.length; k++)
            {
                str_ref=style_no_arr[k]+'_'+style_id_arr[k];
                js_set_value(str_ref);
            }
        }
    </script>
    <?
    exit();
}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_store_name=str_replace("'","",$cbo_store_name);

	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_pi_no=trim(str_replace("'","",$txt_pi_no));
	$txt_lc_no=trim(str_replace("'","",$txt_lc_no));
	$txt_lc_id=trim(str_replace("'","",$txt_lc_id));


	$cbo_supplier=str_replace("'","",$cbo_supplier);
	if($cbo_supplier>0) $supplier_cond =" and a.supplier_id='$cbo_supplier' ";else $supplier_cond= "";
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_wo_po_no=trim(str_replace("'","",$txt_wo_po_no));

	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}
	
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$prod_sql=sql_select("select id as prod_id, item_group_id, sub_group_code, sub_group_name, item_code, item_description, product_name_details, unit_of_measure, order_uom from product_details_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name ");

	$report_format_arr[145] = array(0=>"", 78=>"dyes_chemical_work_print", 84=>"dyes_chemical_work_print2", 85=>"dyes_chemical_work_print3", 430=>"dyes_chemical_work_po_print2", 732=>"dyes_chemical_work_po_print");

	$report_format_arr[146] = array(0=>"", 66=>"stationary_work_order_print", 72=>"stationary_work_order_print6", 85=>"stationary_work_order_print3", 129=>"stationary_work_order_print5", 134=>"stationary_work_print", 137=>"stationary_work_order_print4", 430=>"stationary_work_order_po_print2", 732=>"stationary_work_order_po_print");

	$report_format_arr[147] = array(0=>"", 84=>"spare_parts_work_order_print2", 85=>"spare_parts_work_order_print3", 129=>"spare_parts_work_print", 134=>"spare_parts_work_order_po_print", 137=>"spare_parts_work_print", 191=>"spare_parts_work_print_urmi", 227=>"spare_parts_work_order_print8", 235=>"spare_parts_work_order_print9", 241=>"spare_parts_work_order_po_print_11", 274=>"spare_parts_work_order_print10", 354=>"spare_parts_work_print_8", 427=>"spare_parts_work_order_print12", 430=>"spare_parts_work_order_po_print2", 732=>"spare_parts_work_order_po_print");

	$order_print_arr = array();

    $order_print_sql = sql_select("select report_id, format_id from lib_report_template where template_name='$cbo_company_name' and module_id=5 and report_id in (61,30,132) and is_deleted=0 and status_active=1");

    foreach ($order_print_sql as $print_id) {

        $arr=explode(",",$print_id[csf('format_id')]);
        $cnt=count($arr);
        if($cnt>0){

           $order_print_arr[$print_id[csf('report_id')]] = $arr[0];
        }else{

            $order_print_arr[$print_id[csf('report_id')]] =0;
        }
    }


    $order_sql = sql_select("select a.company_name, a.wo_number, a.supplier_id, a.wo_date, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.delivery_date, a.attention, a.requisition_no, a.delivery_place, a.id, a.wo_number_prefix_num, a.location_id,  a.contact, a.payterm_id, a.remarks, a.tenor, a.reference, a.wo_type, a.inco_term_id, a.entry_form from wo_non_order_info_mst a where a.entry_form in(145,146,147) and a.status_active=1 and a.is_deleted=0 and a.company_name=$cbo_company_name");

    $order_data_arr = array();

    foreach($order_sql as $data)
    {

    	$order_data_arr[$data[csf('id')]]['cbo_location'] = $data[csf('location_id')];
    	$order_data_arr[$data[csf('id')]]['txt_contact'] = $data[csf('contact')];
    	$order_data_arr[$data[csf('id')]]['hidden_delivery_info_dtls'] = $data[csf('delivery_place')];
    	$order_data_arr[$data[csf('id')]]['cbo_payterm_id'] = $data[csf('payterm_id')];
    	$order_data_arr[$data[csf('id')]]['txt_remarks_mst'] = $data[csf('remarks')];
    	$order_data_arr[$data[csf('id')]]['txt_tenor'] = $data[csf('tenor')];
    	$order_data_arr[$data[csf('id')]]['cbo_company_name'] = $data[csf('company_name')];
    	$order_data_arr[$data[csf('id')]]['update_id'] = $data[csf('id')];
    	$order_data_arr[$data[csf('id')]]['txt_req_numbers_id'] = $data[csf('requisition_no')];
    	$order_data_arr[$data[csf('id')]]['txt_reference'] = $data[csf('reference')];
    	$order_data_arr[$data[csf('id')]]['cbo_wo_type'] = $data[csf('wo_type')];
    	$order_data_arr[$data[csf('id')]]['cbo_inco_term'] = $data[csf('inco_term_id')];
    	$order_data_arr[$data[csf('id')]]['wo_number'] = $data[csf('wo_number')];
    	$order_data_arr[$data[csf('id')]]['supplier_id'] = $data[csf('supplier_id')];
    	$order_data_arr[$data[csf('id')]]['wo_date'] = $data[csf('wo_date')];
    	$order_data_arr[$data[csf('id')]]['currency_id'] = $data[csf('currency_id')];
    	$order_data_arr[$data[csf('id')]]['wo_basis_id'] = $data[csf('wo_basis_id')];
    	$order_data_arr[$data[csf('id')]]['pay_mode'] = $data[csf('pay_mode')];
    	$order_data_arr[$data[csf('id')]]['source'] = $data[csf('source')];
    	$order_data_arr[$data[csf('id')]]['delivery_date'] = $data[csf('delivery_date')];
    	$order_data_arr[$data[csf('id')]]['attention'] = $data[csf('attention')];
    	$order_data_arr[$data[csf('id')]]['delivery_place'] = $data[csf('delivery_place')];
    	$order_data_arr[$data[csf('id')]]['wo_number_prefix_num'] = $data[csf('wo_number_prefix_num')];
    	$order_data_arr[$data[csf('id')]]['entry_form'] = $data[csf('entry_form')];

    }
    unset($order_sql); 


	$prod_data_array=array();
	foreach($prod_sql as $row)
	{
		$prod_data_array[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
		$prod_data_array[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
		$prod_data_array[$row[csf("prod_id")]]["sub_group_code"]=$row[csf("sub_group_code")];
		$prod_data_array[$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
		$prod_data_array[$row[csf("prod_id")]]["item_code"]=$row[csf("item_code")];
		$prod_data_array[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
		$prod_data_array[$row[csf("prod_id")]]["product_name_details"]=$row[csf("product_name_details")];
		//$prod_data_array[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
		$prod_data_array[$row[csf("prod_id")]]["unit_of_measure"]=$row[csf("order_uom")];
	}
	
	$inv_id_array=array();
	if( ($cbo_date_type==1 && ($txt_date_from !="" && $txt_date_to !="")) || ($txt_req_no!="") ) 
	{	
		//echo "here   "."$txt_req_no && $cbo_supplier==0 && $txt_wo_po_no=='' && $cbo_date_type!=1";die;
		$sql_cond="";
		if($cbo_company_name) $sql_cond.=" and a.company_id='$cbo_company_name' ";
		if($cbo_item_category_id) $sql_cond.=" and b.item_category='$cbo_item_category_id' ";
		if($cbo_location) $sql_cond.=" and a.location_id='$cbo_location' ";
		if($cbo_store_name) $sql_cond.=" and a.store_name='$cbo_store_name' ";
		if($txt_req_no !="") 
		{
			$sql_cond.=" and a.requ_prefix_num = '$txt_req_no' ";
		}
		if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and a.requisition_date between  '$txt_date_from' and '$txt_date_to'";
		if($db_type==2) $cat_null_cond=" and b.item_category is not null"; else $cat_null_cond=" and b.item_category !=''";
		/*$req_sql="select a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, a.item_category_id, a.req_by, b.item_category, b.id as req_dtsl_id, b.product_id as prod_id, b.required_for, b.quantity, b.rate, b.amount, b.color_id, b.count_id, b.composition_id, b.yarn_type_id, b.cons_uom, a.cbo_currency, a.is_approved, a.location_id
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category not in (0,2,3,12,13,14,24,25,28,30) $sql_cond $cat_null_cond";*/
		$req_sql="SELECT a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, a.item_category_id, a.req_by, b.item_category, b.id as req_dtsl_id, b.product_id as prod_id, b.required_for, b.quantity, b.rate, b.amount, b.color_id, b.count_id, b.composition_id, b.yarn_type_id, b.cons_uom, a.cbo_currency, a.is_approved, a.location_id, c.approved_date
		from inv_purchase_requisition_dtls b, inv_purchase_requisition_mst a 
		left join approval_history c on a.id=c.mst_id and c.entry_form=1 and c.current_approval_status=1
		where b.mst_id=a.id and a.entry_form=69 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category not in (0,1,2,3,12,13,14,24,25,28,30) $sql_cond $cat_null_cond
		union all
		select a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, a.item_category_id, a.req_by, b.item_category, b.id as req_dtsl_id, b.product_id as prod_id, b.required_for, b.quantity, b.rate, b.amount, b.color_id, b.count_id, b.composition_id, b.yarn_type_id, b.cons_uom, a.cbo_currency, a.is_approved, a.location_id, c.approved_date
		from inv_purchase_requisition_dtls b, inv_purchase_requisition_mst a 
		left join approval_history c on a.id=c.mst_id and c.entry_form=20 and c.current_approval_status=1 
		where b.mst_id=a.id and a.entry_form=70 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category in (1) $sql_cond $cat_null_cond";		
		//echo $req_sql;die;
		
		$req_result=sql_select($req_sql);

		if(count($req_result) < 1)
		{
			echo "<span style='font-size:23;font-weight:bold;text-align:center;width:100%'>Data Not Found</span>";die;
		}

		$req_no_arr=$req_dtls_id_arr=$all_data_arr=array();
		foreach($req_result as $row)
		{
			$req_dtls_id_arr[$row[csf("req_dtsl_id")]]=$row[csf("req_dtsl_id")];
			$req_no_arr[$row[csf("req_dtsl_id")]]=$row[csf("requ_no")];
			if($row[csf("item_category")]==1)
			{
				$key=$row[csf("requ_no")]."__".$row[csf("color_id")]."__".$row[csf("count_id")]."__".$row[csf("composition_id")]."__".$row[csf("yarn_type_id")];
			}
			else
			{
				$key=$row[csf("requ_no")]."__".$row[csf("prod_id")];
			}
			
			$all_data_arr[$row[csf("item_category")]][$key]["prod_id"]=$row[csf("prod_id")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_req_id"]=$row[csf("req_id")];
			$all_data_arr[$row[csf("item_category")]][$key]["req_dtsl_id"].=$row[csf("req_dtsl_id")].",";
			$all_data_arr[$row[csf("item_category")]][$key]["requ_no"]=$row[csf("requ_no")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_requisition_date"]=$row[csf("requisition_date")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_store_name"]=$row[csf("store_name")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_pay_mode"]=$row[csf("pay_mode")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_delivery_date"]=$row[csf("delivery_date")];
			$all_data_arr[$row[csf("item_category")]][$key]["req_by"]=$row[csf("req_by")];
			$all_data_arr[$row[csf("item_category")]][$key]["approved_date"]=$row[csf("approved_date")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_prod_id"]=$row[csf("prod_id")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_required_for"]=$row[csf("required_for")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_quantity"]=$row[csf("quantity")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_rate"]=$row[csf("rate")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_amount"]=$row[csf("amount")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_color_id"]=$row[csf("color_id")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_count_id"]=$row[csf("count_id")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_composition_id"]=$row[csf("composition_id")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_yarn_type_id"]=$row[csf("yarn_type_id")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_cons_uom"]=$row[csf("cons_uom")];
			$all_data_arr[$row[csf("item_category")]][$key]["requ_cbo_currency"]=$row[csf("cbo_currency")];
			$all_data_arr[$row[csf("item_category")]][$key]["is_approved"]=$row[csf("is_approved")];
			$all_data_arr[$row[csf("item_category")]][$key]["location_id"]=$row[csf("location_id")];
		}

		//var_dump($all_data_arr);die;
		
		if(!empty($req_dtls_id_arr))
		{
			$req_dtls_idsArr = array_flip(array_flip($req_dtls_id_arr));
	        $req_dtls_ids_cond = '';
	        $req_dtls_ids_cond2='';

	        if($db_type==2 && count($req_dtls_idsArr>1000))
	        {
	            $req_dtls_ids_cond = ' and (';
	            $req_dtls_ids_cond2 = ' and (';
	            $reqDtlsIdsArr = array_chunk($req_dtls_idsArr,999);
	            foreach($reqDtlsIdsArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $req_dtls_ids_cond .= " b.requisition_dtls_id in($ids) or ";
	                $req_dtls_ids_cond2 .= " c.requisition_dtls_id in($ids) or ";
	            }
	            $req_dtls_ids_cond = rtrim($req_dtls_ids_cond,'or ');
	            $req_dtls_ids_cond2 = rtrim($req_dtls_ids_cond2,'or ');
	            $req_dtls_ids_cond .= ')';
	            $req_dtls_ids_cond2 .= ')';
	        }
	        else
	        {
	            $req_dtls_ids = implode(',', $req_dtls_idsArr);
	            $req_dtls_ids_cond=" and b.requisition_dtls_id in ($req_dtls_ids)";
	            $req_dtls_ids_cond2=" and c.requisition_dtls_id in ($req_dtls_ids)";
	        }

			if($cbo_item_category_id) $wo_category_cond = "and b.item_category_id in($cbo_item_category_id) "; else $wo_category_cond = "";
			
			$sql_wo=sql_select("select a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date ,b.inserted_by, a.supplier_id, b.id as wo_dtls_id, b.requisition_dtls_id, b.item_id as prod_id, b.supplier_order_quantity, b.rate, b.amount, b.color_name, b.yarn_count, b.yarn_comp_type1st, b.yarn_type, b.item_category_id, b.uom, a.currency_id
			from wo_non_order_info_mst a, wo_non_order_info_dtls b 
			where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name $req_dtls_ids_cond $wo_category_cond and b.item_category_id not in (0,2,3,12,13,14,24,25,28,30) ");  
			  //$supplier_cond
			$wo_data_array=array();
			foreach($sql_wo as $row)
			{
				if($row[csf("item_category_id")]==1)
				{
					$key=$req_no_arr[$row[csf("requisition_dtls_id")]]."__".$row[csf("color_name")]."__".$row[csf("yarn_count")]."__".$row[csf("yarn_comp_type1st")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$req_no_arr[$row[csf("requisition_dtls_id")]]."__".$row[csf("prod_id")];
				}
				
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_mst_id"].=$row[csf("wo_mst_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_number"].=$row[csf("wo_number")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_number_prefix_num"].=$row[csf("wo_number_prefix_num")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_date"]=$row[csf("wo_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_id"]=$row[csf("supplier_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["inserted_by"]=$row[csf("inserted_by")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_prod_id"]=$row[csf("prod_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_amount"]+=$row[csf("amount")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_color_name"]=$row[csf("color_name")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_yarn_count"]=$row[csf("yarn_count")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_yarn_comp_type1st"]=$row[csf("yarn_comp_type1st")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_yarn_type"]=$row[csf("yarn_type")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_uom"]=$row[csf("uom")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["wo_currency_id"]=$row[csf("currency_id")];
			}
			
			//var_dump($all_data_arr);die;
			
			$sql_pi="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.item_category_id, b.uom, b.quantity, b.amount, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_type, c.id as wo_dtls_id, c.requisition_dtls_id 
			from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_category_cond  and b.item_category_id not in (0,4,2,3,12,13,14,24,25,28,30) $req_dtls_ids_cond2";
			//echo $sql_pi;die;
			$sql_pi_result=sql_select($sql_pi);
			$pi_req_arr=$pi_id_arr=array();
			foreach($sql_pi_result as $row)
			{
				$pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				$pi_req_arr[$row[csf("pi_dtls_id")]]=$req_no_arr[$row[csf("requisition_dtls_id")]];
				if($row[csf("item_category_id")]==1)
				{
					$key=$req_no_arr[$row[csf("requisition_dtls_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$req_no_arr[$row[csf("requisition_dtls_id")]]."__".$row[csf("prod_id")];
				}
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_id"].=$row[csf("pi_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_number"].=$row[csf("pi_number")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_date"]=$row[csf("pi_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_prod_id"]=$row[csf("prod_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_intendor_name"]=$row[csf("intendor_name")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_uom"]=$row[csf("uom")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_quantity"]+=$row[csf("quantity")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_amount"]+=$row[csf("amount")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_color_id"]=$row[csf("color_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_count_name"]=$row[csf("count_name")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_yarn_type"]=$row[csf("yarn_type")];
			} 
		}
		
		//var_dump($all_data_arr);die;
		
		if(!empty($pi_id_arr))
		{
			$pi_idsArr = array_flip(array_flip($pi_id_arr));
	        $pi_ids_cond = '';

	        if($db_type==2 && count($pi_idsArr>1000))
	        {
	            $pi_ids_cond = ' and (';
	            $piIdsArr = array_chunk($pi_idsArr,999);
	            foreach($piIdsArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $pi_ids_cond .= " c.id in($ids) or ";
	            }
	            $pi_ids_cond = rtrim($pi_ids_cond,'or ');
	            $pi_ids_cond .= ')';
	        }
	        else
	        {
	            $pi_ids = implode(',', $pi_idsArr);
	            $pi_ids_cond=" and c.id in ($pi_ids)";
	        }

			$sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.issuing_bank_id, a.etd_date 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_ids_cond";
			//echo $sql_btb;die;
			
			$btb_result=sql_select($sql_btb);
			$btb_data_array=array();
			foreach($btb_result as $row)
			{
				if($row[csf("item_category_id")]==1)
				{
					$key=$pi_req_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$pi_req_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("prod_id")];
				}
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_id"]=$row[csf("lc_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_number"]=$row[csf("lc_number")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_date"]=$row[csf("lc_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_tenor"]=$row[csf("tenor")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_issuing_bank_id"]=$row[csf("issuing_bank_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_etd_date"]=$row[csf("etd_date")];
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["lc_value"]+=$row[csf("amount")];
				}
				
			}
			
			//var_dump($all_data_arr);die;
			
			
			$sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.eta_date
			from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
			where a.id = b.import_invoice_id and b.pi_id = c.pi_id and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0 $pi_ids_cond";
			//echo $sql_invoice;die;
			$invoice_result=sql_select($sql_invoice);
			foreach($invoice_result as $row)
			{
				$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
				if($row[csf("item_category_id")]==1)
				{
					$key=$pi_req_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$pi_req_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("prod_id")];
				}
				
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_date"]=$row[csf("invoice_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_container_no"]=$row[csf("container_no")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];
				
				
				if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
				{
					$inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_pkg_quantity"]+=$row[csf("quantity")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_value"]+=$row[csf("amount")];
				}
			}
			
			//var_dump($all_data_arr);die;
			if(!empty($inv_id_array))
			{
				$inv_idsArr = array_flip(array_flip($inv_id_array));
		        $inv_ids_cond = '';

		        if($db_type==2 && count($inv_idsArr>1000))
		        {
		            $inv_ids_cond = ' and (';
		            $invIdsArr = array_chunk($inv_idsArr,999);
		            foreach($invIdsArr as $ids)
		            {
		                $ids = implode(',',$ids);
		                $inv_ids_cond .= " invoice_id in($ids) or ";
		            }
		            $inv_ids_cond = rtrim($inv_ids_cond,'or ');
		            $inv_ids_cond .= ')';
		        }
		        else
		        {
		            $inv_ids = implode(',', $inv_idsArr);
		            $inv_ids_cond=" and invoice_id in ($inv_ids)";
		        }
				$sql_pay="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment where status_active=1 $inv_ids_cond";
				$pay_result=sql_select($sql_pay);
				$payment_data_arr=array();
				foreach($pay_result as $row)
				{
					$payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
					$payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
					$payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
				}
			}
			
				
		}	
	
	}
		
	if(($cbo_date_type==2 &&  ($txt_date_from && $txt_date_to)) ||  ($txt_wo_po_no!=""))
	{
		$sql_cond="";
		if($cbo_company_name) $sql_cond.=" and a.company_name='$cbo_company_name' ";
		if($cbo_item_category_id) $sql_cond.=" and b.item_category_id='$cbo_item_category_id' ";
		if($cbo_location) $sql_cond.=" and d.location_id='$cbo_location' ";
		if($cbo_store_name) $sql_cond.=" and d.store_name='$cbo_store_name' ";
		if($txt_wo_po_no !="") 
		{
			$sql_cond.=" and a.wo_number_prefix_num = '$txt_wo_po_no' ";
		}
		if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and a.wo_date between  '$txt_date_from' and '$txt_date_to'";
		if($db_type==2) $sql_cond.=" and b.item_category_id is not null"; else $sql_cond.=" and b.item_category_id !=''";
		
		$sql_wo="SELECT a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.supplier_id, b.id as wo_dtls_id, b.requisition_dtls_id, b.item_id as prod_id, b.supplier_order_quantity, b.rate, b.amount, b.item_category_id, b.uom, a.currency_id, d.id as req_id, d.requ_no, d.requ_prefix_num, d.requisition_date, d.store_name, d.pay_mode, d.source, d.cbo_currency, d.delivery_date, c.id as req_dtsl_id, c.required_for, c.quantity, c.rate as req_rate, c.amount as requ_amount, c.cons_uom, c.color_id, c.count_id, c.composition_id, c.yarn_type_id
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, inv_purchase_requisition_dtls c, inv_purchase_requisition_mst d
		where a.id=b.mst_id and b.requisition_dtls_id=c.id and c.mst_id=d.id and a.wo_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id not in (0,2,3,12,13,14,24,25,28,30) $sql_cond  $supplier_cond";
		
		
		//echo $sql_wo;die;
		
		$req_result=sql_select($sql_wo);

		$req_no_arr=$req_dtls_id_arr=$all_data_arr=array();
		foreach($req_result as $row)
		{
			$req_dtls_id_arr[$row[csf("req_dtsl_id")]]=$row[csf("req_dtsl_id")];
			$req_no_arr[$row[csf("req_dtsl_id")]]=$row[csf("requ_no")];
			if($row[csf("item_category_id")]==1)
			{
				$key=$row[csf("requ_no")]."__".$row[csf("color_id")]."__".$row[csf("count_id")]."__".$row[csf("composition_id")]."__".$row[csf("yarn_type_id")];
			}
			else
			{
				$key=$row[csf("requ_no")]."__".$row[csf("prod_id")];
			}
			
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_req_id"]=$row[csf("req_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["req_dtsl_id"].=$row[csf("req_dtsl_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_no"]=$row[csf("requ_no")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_requisition_date"]=$row[csf("requisition_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_store_name"]=$row[csf("store_name")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_pay_mode"]=$row[csf("pay_mode")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_delivery_date"]=$row[csf("delivery_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_prod_id"]=$row[csf("prod_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_required_for"]=$row[csf("required_for")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_quantity"]=$row[csf("quantity")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_rate"]=$row[csf("rate")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_amount"]=$row[csf("amount")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_cons_uom"]=$row[csf("cons_uom")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_cbo_currency"]=$row[csf("cbo_currency")];
			
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_mst_id"].=$row[csf("wo_mst_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_number"].=$row[csf("wo_number")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_number_prefix_num"].=$row[csf("wo_number_prefix_num")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_date"]=$row[csf("wo_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_id"]=$row[csf("supplier_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_prod_id"]=$row[csf("prod_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_amount"]+=$row[csf("amount")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_uom"]=$row[csf("uom")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_currency_id"]=$row[csf("currency_id")];
		}

		//var_dump($all_data_arr);die;
		///############################## based on requistion start ##############################################/////////////
		if(!empty($req_dtls_id_arr))
		{
			$req_dtls_idsArr = array_flip(array_flip($req_dtls_id_arr));
	        $req_dtls_ids_cond='';

	        if($db_type==2 && count($req_dtls_idsArr>1000))
	        {
	            $req_dtls_ids_cond = ' and (';
	            $reqDtlsIdsArr = array_chunk($req_dtls_idsArr,999);
	            foreach($reqDtlsIdsArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $req_dtls_ids_cond .= " c.requisition_dtls_id in($ids) or ";
	            }
	            $req_dtls_ids_cond = rtrim($req_dtls_ids_cond,'or ');
	            $req_dtls_ids_cond .= ')';
	        }
	        else
	        {
	            $req_dtls_ids = implode(',', $req_dtls_idsArr);
	            $req_dtls_ids_cond=" and c.requisition_dtls_id in ($req_dtls_ids)";
	        }

			$sql_pi="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.item_category_id, b.uom, b.quantity, b.amount, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_type, c.id as wo_dtls_id, c.requisition_dtls_id 
			from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_category_cond  and b.item_category_id not in (0,4,2,3,12,13,14,24,25,28,30) $req_dtls_ids_cond";
			//echo $sql_pi;die;
			$sql_pi_result=sql_select($sql_pi);
			$pi_req_arr=$pi_id_arr=array();
			foreach($sql_pi_result as $row)
			{
				$pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				$pi_req_arr[$row[csf("pi_dtls_id")]]=$req_no_arr[$row[csf("requisition_dtls_id")]];
				if($row[csf("item_category_id")]==1)
				{
					$key=$req_no_arr[$row[csf("requisition_dtls_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$req_no_arr[$row[csf("requisition_dtls_id")]]."__".$row[csf("prod_id")];
				}
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_id"].=$row[csf("pi_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_number"].=$row[csf("pi_number")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_date"]=$row[csf("pi_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_prod_id"]=$row[csf("prod_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_intendor_name"]=$row[csf("intendor_name")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_uom"]=$row[csf("uom")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_quantity"]+=$row[csf("quantity")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_amount"]+=$row[csf("amount")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_color_id"]=$row[csf("color_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_count_name"]=$row[csf("count_name")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_yarn_type"]=$row[csf("yarn_type")];
			} 
		}
		
		//var_dump($all_data_arr);die;
		
		if(!empty($pi_id_arr))
		{
			$pi_idsArr = array_flip(array_flip($pi_id_arr));
	        $pi_ids_cond = '';

	        if($db_type==2 && count($pi_idsArr>1000))
	        {
	            $pi_ids_cond = ' and (';
	            $piIdsArr = array_chunk($pi_idsArr,999);
	            foreach($piIdsArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $pi_ids_cond .= " c.id in($ids) or ";
	            }
	            $pi_ids_cond = rtrim($pi_ids_cond,'or ');
	            $pi_ids_cond .= ')';
	        }
	        else
	        {
	            $pi_ids = implode(',', $pi_idsArr);
	            $pi_ids_cond=" and c.id in ($pi_ids)";
	        }

			$sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.issuing_bank_id, a.etd_date 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_ids_cond";
			//echo $sql_btb;die;
			
			$btb_result=sql_select($sql_btb);
			$btb_data_array=$wo_lc_pi=array();
			foreach($btb_result as $row)
			{
				$wo_lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				if($row[csf("item_category_id")]==1)
				{
					$key=$pi_req_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$pi_req_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("prod_id")];
				}
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_id"]=$row[csf("lc_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_number"]=$row[csf("lc_number")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_date"]=$row[csf("lc_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_tenor"]=$row[csf("tenor")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_issuing_bank_id"]=$row[csf("issuing_bank_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_etd_date"]=$row[csf("etd_date")];
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["lc_value"]+=$row[csf("amount")];
				}
			}
			
			//var_dump($all_data_arr);die;
			
			if(count($wo_lc_pi)>0)
			{
				$wo_lc_piArr = array_flip(array_flip($wo_lc_pi));
		        $wo_lc_pi_cond = '';

		        if($db_type==2 && count($wo_lc_piArr>1000))
		        {
		            $wo_lc_pi_cond = ' and (';
		            $woLcPiArr = array_chunk($wo_lc_piArr,999);
		            foreach($woLcPiArr as $ids)
		            {
		                $ids = implode(',',$ids);
		                $wo_lc_pi_cond .= " c.id in($ids) or ";
		            }
		            $wo_lc_pi_cond = rtrim($wo_lc_pi_cond,'or ');
		            $wo_lc_pi_cond .= ')';
		        }
		        else
		        {
		            $wo_lc_pi_ids = implode(',', $wo_lc_piArr);
		            $wo_lc_pi_cond=" and c.id in ($wo_lc_pi_ids)";
		        }
				$sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.eta_date
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0 $wo_lc_pi_cond";
				//echo $sql_invoice;die;
				$invoice_result=sql_select($sql_invoice);
				foreach($invoice_result as $row)
				{
					$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
					if($row[csf("item_category_id")]==1)
					{
						$key=$pi_req_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
					}
					else
					{
						$key=$pi_req_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("prod_id")];
					}
					
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_date"]=$row[csf("invoice_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_container_no"]=$row[csf("container_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];
					
					if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
					{
						$inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
						$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_pkg_quantity"]+=$row[csf("quantity")];
						$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_value"]+=$row[csf("amount")];
					}
				}
			}
			
			//var_dump($all_data_arr);die;
		}
		
		
		///########################### based on requistion end ##############################################/////////////
		
		///########################## based on independent wo start ##############################################/////////////
			
		$sql_wo="select a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.supplier_id, b.id as wo_dtls_id, b.requisition_dtls_id, b.item_id as prod_id, b.supplier_order_quantity, b.rate, b.amount, b.color_name, b.yarn_count, b.yarn_comp_type1st, b.yarn_type, b.item_category_id, b.uom, a.currency_id
		from wo_non_order_info_mst a, wo_non_order_info_dtls b 
		where a.id=b.mst_id and a.wo_basis_id <>1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id not in (0,2,3,12,13,14,24,25,28,30) $sql_cond $supplier_cond";
		
		//echo $sql_wo;die;
		
		$wo_result=sql_select($sql_wo);
		
		if(count($req_result) < 1 && count($wo_result) < 1)
		{
			echo "<span style='font-size:23;font-weight:bold;text-align:center;width:100%'>Data Not Found</span>";die;
		}
		
		$wo_data_array=$all_wo_dtls_id=array();
		foreach($wo_result as $row)
		{
			$all_wo_dtls_id[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
			if($row[csf("item_category_id")]==1)
			{
				$key=$row[csf("wo_number")]."__".$row[csf("color_name")]."__".$row[csf("yarn_count")]."__".$row[csf("yarn_comp_type1st")]."__".$row[csf("yarn_type")];
			}
			else
			{
				$key=$row[csf("wo_number")]."__".$row[csf("prod_id")];
			}
			
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_mst_id"]=$row[csf("wo_mst_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_number"]=$row[csf("wo_number")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_number_prefix_num"]=$row[csf("wo_number_prefix_num")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_date"]=$row[csf("wo_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_id"]=$row[csf("supplier_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_prod_id"]=$row[csf("prod_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_amount"]+=$row[csf("amount")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_uom"]=$row[csf("uom")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_currency_id"]=$row[csf("currency_id")];
		}
		
		
		if(!empty($all_wo_dtls_id))
		{
			$all_wo_dtls_Arr = array_flip(array_flip($all_wo_dtls_id));
	        $all_wo_dtls_condition = '';

	        if($db_type==2 && count($all_wo_dtls_Arr>1000))
	        {
	            $all_wo_dtls_condition = ' and (';
	            $allWoDtlsArr = array_chunk($all_wo_dtls_Arr,999);
	            foreach($allWoDtlsArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $all_wo_dtls_condition .= " b.work_order_dtls_id in($ids) or ";
	            }
	            $all_wo_dtls_condition = rtrim($all_wo_dtls_condition,'or ');
	            $all_wo_dtls_condition .= ')';
	        }
	        else
	        {
	            $wo_dtls_ids = implode(',', $all_wo_dtls_Arr);
	            $all_wo_dtls_condition=" and b.work_order_dtls_id in ($wo_dtls_ids)";
	        }
			
			$sql_pi="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.item_prod_id as prod_id, b.item_category_id, b.uom, b.quantity, b.amount, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_type 
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_category_cond  and b.item_category_id not in (0,4,2,3,12,13,14,24,25,28,30) $all_wo_dtls_condition";
			//echo $sql_pi;die;
			$sql_pi_result=sql_select($sql_pi);
			$pi_req_arr=$pi_id_arr=$inde_wo_pi_id_arr=array();
			foreach($sql_pi_result as $row)
			{
				$inde_wo_pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				$pi_wo_arr[$row[csf("pi_dtls_id")]]=$row[csf("work_order_no")];
				if($row[csf("item_category_id")]==1)
				{
					$key=$row[csf("work_order_no")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$row[csf("work_order_no")]."__".$row[csf("prod_id")];
					$test_key.=$key.",";
				}
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_id"].=$row[csf("pi_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_number"].=$row[csf("pi_number")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_date"]=$row[csf("pi_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_prod_id"]=$row[csf("prod_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_intendor_name"]=$row[csf("intendor_name")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_uom"]=$row[csf("uom")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_quantity"]+=$row[csf("quantity")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_amount"]+=$row[csf("amount")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["pi_wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
			} 
		}
		//echo $test_key;
		//var_dump($all_data_arr);die;
		
		if(!empty($inde_wo_pi_id_arr))
		{
			$inde_wo_pi_idArr = array_flip(array_flip($inde_wo_pi_id_arr));
	        $inde_wo_pi_id_cond = '';
			if($db_type==2 && count($inde_wo_pi_idArr>1000))
	        {
	            $inde_wo_pi_id_cond = ' and (';
	            $indeWoPiIdArr = array_chunk($inde_wo_pi_idArr,999);
	            foreach($indeWoPiIdArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $inde_wo_pi_id_cond .= " c.id in($ids) or ";
	            }
	            $inde_wo_pi_id_cond = rtrim($inde_wo_pi_id_cond,'or ');
	            $inde_wo_pi_id_cond .= ')';
	        }
	        else
	        {
	            $inde_wo_pi_ids = implode(',', $inde_wo_pi_idArr);
	            $inde_wo_pi_id_cond=" and c.id in ($inde_wo_pi_ids)";
	        }

			$sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.issuing_bank_id, a.etd_date, c.work_order_no, c.work_order_id, c.work_order_dtls_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $inde_wo_pi_id_cond";
			//echo $sql_btb;die;
			
			$btb_result=sql_select($sql_btb);
			$btb_data_array=array();$lc_pi=array();
			foreach($btb_result as $row)
			{
				$lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				if($row[csf("item_category_id")]==1)
				{
					$key=$row[csf("work_order_no")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$row[csf("work_order_no")]."__".$row[csf("prod_id")];
				}
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_id"]=$row[csf("lc_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_number"]=$row[csf("lc_number")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_date"]=$row[csf("lc_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_tenor"]=$row[csf("tenor")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_issuing_bank_id"]=$row[csf("issuing_bank_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_etd_date"]=$row[csf("etd_date")];
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["lc_value"]+=$row[csf("amount")];
				}
				
			}
			
			//var_dump($all_data_arr);die;
			if(count($lc_pi)>0)
			{
				$lc_pi_idArr = array_flip(array_flip($lc_pi));
		        $lc_pi_id_cond = '';
				if($db_type==2 && count($lc_pi_idArr>1000))
		        {
		            $lc_pi_id_cond = ' and (';
		            $lcPiIdArr = array_chunk($lc_pi_idArr,999);
		            foreach($lcPiIdArr as $ids)
		            {
		                $ids = implode(',',$ids);
		                $lc_pi_id_cond .= " c.id in($ids) or ";
		            }
		            $lc_pi_id_cond = rtrim($lc_pi_id_cond,'or ');
		            $lc_pi_id_cond .= ')';
		        }
		        else
		        {
		            $lc_pi_ids = implode(',', $lc_pi_idArr);
		            $lc_pi_id_cond=" and c.id in ($lc_pi_ids)";
		        }
				$sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.eta_date, c.work_order_no, c.work_order_id, c.work_order_dtls_id
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0 $lc_pi_id_cond";
				//echo $sql_invoice;die;
				$invoice_result=sql_select($sql_invoice);
				$inv_id_array=array();
				foreach($invoice_result as $row)
				{
					$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
					if($row[csf("item_category_id")]==1)
					{
						$key=$row[csf("work_order_no")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
					}
					else
					{
						$key=$row[csf("work_order_no")]."__".$row[csf("prod_id")];
					}
					
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_date"]=$row[csf("invoice_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_container_no"]=$row[csf("container_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];
					
					
					if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
					{
						$inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
						$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_pkg_quantity"]+=$row[csf("quantity")];
						$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_value"]+=$row[csf("amount")];
					}
				}
			}
			
		}
		
		//var_dump($all_data_arr);die;
		
		
		///########################## based on independent wo end    ##############################################/////////////
		
		if(!empty($inv_id_array))
		{
			$inv_idsArr = array_flip(array_flip($inv_id_array));
	        $inv_ids_cond = '';

	        if($db_type==2 && count($inv_idsArr>1000))
	        {
	            $inv_ids_cond = ' and (';
	            $invIdsArr = array_chunk($inv_idsArr,999);
	            foreach($invIdsArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $inv_ids_cond .= " invoice_id in($ids) or ";
	            }
	            $inv_ids_cond = rtrim($inv_ids_cond,'or ');
	            $inv_ids_cond .= ')';
	        }
	        else
	        {
	            $inv_ids = implode(',', $inv_idsArr);
	            $inv_ids_cond=" and invoice_id in ($inv_ids)";
	        }
			$sql_pay="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment where status_active=1 $inv_ids_cond";
			$pay_result=sql_select($sql_pay);
			$payment_data_arr=array();
			foreach($pay_result as $row)
			{
				$payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
				$payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				$payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
			}
		}
	}
	
	if(($cbo_date_type==3 &&  ($txt_date_from && $txt_date_to)) ||  ($txt_pi_no!=""))
	{
		
		$sql_cond="";
		if($cbo_company_name) $sql_cond.=" and a.importer_id='$cbo_company_name' ";
		if($cbo_item_category_id) $sql_cond.=" and b.item_category_id='$cbo_item_category_id' ";
		if($cbo_location) $sql_cond.=" and f.location_id='$cbo_location' ";
		if($cbo_store_name) $sql_cond.=" and f.store_name='$cbo_store_name' ";
		if($txt_pi_no !="") 
		{
			$sql_cond.=" and a.pi_number = '$txt_pi_no' ";
		}
		if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and a.pi_date between  '$txt_date_from' and '$txt_date_to'";
		if($db_type==2) $sql_cond.=" and b.item_category_id is not null"; else $sql_cond.=" and b.item_category_id !=''";
		
		
		///############################## based on requistion Wo PI start ##############################################/////////////
		
		
		$sql_pi_wo_req="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.item_category_id, b.uom, b.quantity as pi_qnty, b.amount as pi_amt, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_type, d.id as wo_mst_id, d.wo_number, d.wo_number_prefix_num, d.wo_date, d.supplier_id as wo_supplier, c.id as wo_dtls_id, c.requisition_dtls_id, c.supplier_order_quantity, c.amount as wo_amount, c.uom as wo_uom, d.currency_id as wo_currency, f.id as req_id, f.requ_no, f.requ_prefix_num, f.requisition_date, f.store_name, f.pay_mode, f.source, f.cbo_currency, f.delivery_date, e.required_for, e.quantity as req_qnty, e.amount as req_amt, e.cons_uom
		from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, wo_non_order_info_mst d, inv_purchase_requisition_dtls e, inv_purchase_requisition_mst f 
		where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and c.requisition_dtls_id=e.id and e.mst_id=f.id and d.wo_basis_id=1 and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id not in (0,4,2,3,12,13,14,24,25,28,30) $sql_cond  $supplier_cond";
		
		// echo $sql_pi_wo_req;//die;
		
		
		
		$req_result=sql_select($sql_pi_wo_req);
		//var_dump($req_result);die;

		$req_no_arr=$req_dtls_id_arr=$all_data_arr=$req_pi_id_arr=array();
		foreach($req_result as $row)
		{
			$req_pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
			$req_no_arr[$row[csf("pi_dtls_id")]]=$row[csf("requ_no")];
			if($row[csf("item_category_id")]==1)
			{
				$key=$row[csf("requ_no")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
			}
			else
			{
				$key=$row[csf("requ_no")]."__".$row[csf("prod_id")];
			}
			
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_req_id"]=$row[csf("req_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["req_dtsl_id"].=$row[csf("req_dtsl_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_no"]=$row[csf("requ_no")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_requisition_date"]=$row[csf("requisition_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_store_name"]=$row[csf("store_name")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_pay_mode"]=$row[csf("pay_mode")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_delivery_date"]=$row[csf("delivery_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_required_for"]=$row[csf("required_for")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_quantity"]=$row[csf("req_qnty")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_amount"]=$row[csf("req_amt")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_cons_uom"]=$row[csf("cons_uom")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["requ_cbo_currency"]=$row[csf("cbo_currency")];
			
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_mst_id"].=$row[csf("wo_mst_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_number"].=$row[csf("wo_number")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_number_prefix_num"].=$row[csf("wo_number_prefix_num")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_date"]=$row[csf("wo_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_id"]=$row[csf("supplier_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_amount"]+=$row[csf("wo_amount")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_uom"]=$row[csf("wo_uom")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_currency_id"]=$row[csf("wo_currency")];
			
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_id"].=$row[csf("pi_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_number"].=$row[csf("pi_number")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_date"]=$row[csf("pi_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_last_shipment_date"]=$row[csf("last_shipment_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_intendor_name"]=$row[csf("intendor_name")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_uom"]=$row[csf("uom")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_quantity"]+=$row[csf("pi_qnty")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_amount"]+=$row[csf("pi_amt")];
		}
		
		
		//var_dump($all_data_arr);die;
		
		
		
		
		if(!empty($req_pi_id_arr))
		{
			$req_pi_id_Arr = array_flip(array_flip($req_pi_id_arr));
	        $req_pi_ids_cond = '';

	        if($db_type==2 && count($req_pi_id_Arr>1000))
	        {
	            $req_pi_ids_cond = ' and (';
	            $reqPiIdArr = array_chunk($req_pi_id_Arr,999);
	            foreach($reqPiIdArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $req_pi_ids_cond .= " c.id in($ids) or ";
	            }
	            $req_pi_ids_cond = rtrim($req_pi_ids_cond,'or ');
	            $req_pi_ids_cond .= ')';
	        }
	        else
	        {
	            $req_pi_ids = implode(',', $req_pi_id_Arr);
	            $req_pi_ids_cond=" and c.id in ($req_pi_ids)";
	        }

			$sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.issuing_bank_id, a.etd_date 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_pi_ids_cond";
			//echo $sql_btb;die;
			
			$btb_result=sql_select($sql_btb);
			$btb_data_array=$wo_lc_pi=array();
			foreach($btb_result as $row)
			{
				$wo_lc_pi[$row[csf("pi_id")]]=$row[csf("pi_id")];
				if($row[csf("item_category_id")]==1)
				{
					$key=$req_no_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$req_no_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("prod_id")];
				}
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_id"]=$row[csf("lc_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_number"]=$row[csf("lc_number")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_date"]=$row[csf("lc_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_tenor"]=$row[csf("tenor")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_issuing_bank_id"]=$row[csf("issuing_bank_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_etd_date"]=$row[csf("etd_date")];
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["lc_value"]+=$row[csf("amount")];
				}
			}
			//ksort($all_data_arr);
			//var_dump($all_data_arr);die;
			
			if(count($wo_lc_pi)>0)
			{
				$wo_lc_pi_Arr = array_flip(array_flip($wo_lc_pi));
		        $wo_lc_pi_cond = '';

		        if($db_type==2 && count($wo_lc_pi_Arr>1000))
		        {
		            $wo_lc_pi_cond = ' and (';
		            $woLcPiArr = array_chunk($wo_lc_pi_Arr,999);
		            foreach($woLcPiArr as $ids)
		            {
		                $ids = implode(',',$ids);
		                $wo_lc_pi_cond .= " c.id in($ids) or ";
		            }
		            $wo_lc_pi_cond = rtrim($wo_lc_pi_cond,'or ');
		            $wo_lc_pi_cond .= ')';
		        }
		        else
		        {
		            $wo_lc_pi_ids = implode(',', $wo_lc_pi_Arr);
		            $wo_lc_pi_cond=" and c.id in ($wo_lc_pi_ids)";
		        }

				$sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.eta_date
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0 $wo_lc_pi_cond";
				//echo $sql_invoice;die;
				$invoice_result=sql_select($sql_invoice);
				foreach($invoice_result as $row)
				{
					$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
					if($row[csf("item_category_id")]==1)
					{
						$key=$req_no_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
					}
					else
					{
						$key=$req_no_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("prod_id")];
					}
					
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_date"]=$row[csf("invoice_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_container_no"]=$row[csf("container_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];
					
					if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
					{
						$inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
						$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_pkg_quantity"]+=$row[csf("quantity")];
						$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_value"]+=$row[csf("amount")];
					}
				}
			}
			
			//var_dump($all_data_arr);die;
		}
		
		///############################## based on requistion Wo PI End ##############################################/////////////
		
		
		///########################## based on independent wo PI start ##############################################/////////////
			
		$sql_pi_wo="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.item_category_id, b.uom, b.quantity as pi_qnty, b.amount as pi_amt, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_type, d.id as wo_mst_id, d.wo_number, d.wo_number_prefix_num, d.wo_date, d.supplier_id as wo_supplier, c.id as wo_dtls_id, c.supplier_order_quantity, c.amount as wo_amount, c.uom as wo_uom, d.currency_id as wo_currency
		from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, wo_non_order_info_mst d
		where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and d.wo_basis_id <>1 and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id not in (0,4,2,3,12,13,14,24,25,28,30) $sql_cond  $supplier_cond ";
		
		
		
		//echo $sql_pi_wo;//die;
		
		$wo_result=sql_select($sql_pi_wo);
		
		$wo_data_array=$all_inde_wo_pi_id=array();
		foreach($wo_result as $row)
		{
			$all_inde_wo_pi_id[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
			if($row[csf("item_category_id")]==1)
			{
				$key=$row[csf("wo_number")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
			}
			else
			{
				$key=$row[csf("wo_number")]."__".$row[csf("prod_id")];
			}
			
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_mst_id"]=$row[csf("wo_mst_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_number"]=$row[csf("wo_number")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_number_prefix_num"]=$row[csf("wo_number_prefix_num")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_date"]=$row[csf("wo_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_id"]=$row[csf("wo_supplier")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_amount"]+=$row[csf("wo_amount")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_uom"]=$row[csf("wo_uom")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["wo_currency_id"]=$row[csf("wo_currency")];
			
			
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_id"].=$row[csf("pi_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_number"].=$row[csf("pi_number")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_date"]=$row[csf("pi_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_last_shipment_date"]=$row[csf("last_shipment_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_intendor_name"]=$row[csf("intendor_name")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_uom"]=$row[csf("uom")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_quantity"]+=$row[csf("pi_qnty")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_amount"]+=$row[csf("pi_amt")];
			
		}
		
		
		//var_dump($all_data_arr);die;
		
		
		if(!empty($all_inde_wo_pi_id))
		{
			$all_inde_wo_pi_idsArr = array_flip(array_flip($all_inde_wo_pi_id));
	        $all_inde_wo_pi_ids_cond = '';

	        if($db_type==2 && count($all_inde_wo_pi_idsArr>1000))
	        {
	            $all_inde_wo_pi_ids_cond = ' and (';
	            $allIndeWoPiIdsArr = array_chunk($all_inde_wo_pi_idsArr,999);
	            foreach($allIndeWoPiIdsArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $all_inde_wo_pi_ids_cond .= " c.id in($ids) or ";
	            }
	            $all_inde_wo_pi_ids_cond = rtrim($all_inde_wo_pi_ids_cond,'or ');
	            $all_inde_wo_pi_ids_cond .= ')';
	        }
	        else
	        {
	            $all_inde_wo_pi_ids = implode(',', $all_inde_wo_pi_idsArr);
	            $all_inde_wo_pi_ids_cond=" and c.id in ($all_inde_wo_pi_ids)";
	        }

			$sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.issuing_bank_id, a.etd_date, c.work_order_no, c.work_order_id, c.work_order_dtls_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_inde_wo_pi_ids_cond";
			//echo $sql_btb;die;
			
			$btb_result=sql_select($sql_btb);
			$btb_data_array=array();$lc_pi=$inde_wo_lc_pi=array();
			foreach($btb_result as $row)
			{
				$inde_wo_lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				if($row[csf("item_category_id")]==1)
				{
					$key=$row[csf("work_order_no")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$row[csf("work_order_no")]."__".$row[csf("prod_id")];
				}
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_id"]=$row[csf("lc_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_number"]=$row[csf("lc_number")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_date"]=$row[csf("lc_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_tenor"]=$row[csf("tenor")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_issuing_bank_id"]=$row[csf("issuing_bank_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_etd_date"]=$row[csf("etd_date")];
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["lc_value"]+=$row[csf("amount")];
				}
				
			}
			
			//var_dump($all_data_arr);die;
			if(count($inde_wo_lc_pi)>0)
			{
				$inde_wo_lc_piArr = array_flip(array_flip($inde_wo_lc_pi));
		        $inde_wo_lc_pi_cond = '';

		        if($db_type==2 && count($inde_wo_lc_piArr>1000))
		        {
		            $inde_wo_lc_pi_cond = ' and (';
		            $indeWoLcPiArr = array_chunk($inde_wo_lc_piArr,999);
		            foreach($indeWoLcPiArr as $ids)
		            {
		                $ids = implode(',',$ids);
		                $inde_wo_lc_pi_cond .= " c.id in($ids) or ";
		            }
		            $inde_wo_lc_pi_cond = rtrim($inde_wo_lc_pi_cond,'or ');
		            $inde_wo_lc_pi_cond .= ')';
		        }
		        else
		        {
		            $inde_wo_lc_pis = implode(',', $inde_wo_lc_piArr);
		            $inde_wo_lc_pi_cond=" and c.id in ($inde_wo_lc_pis)";
		        }

				$sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.eta_date, c.work_order_no, c.work_order_id, c.work_order_dtls_id
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0 $inde_wo_lc_pi_cond";
				//echo $sql_invoice;die;
				$invoice_result=sql_select($sql_invoice);
				$inv_id_array=array();
				foreach($invoice_result as $row)
				{
					$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
					if($row[csf("item_category_id")]==1)
					{
						$key=$row[csf("work_order_no")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
					}
					else
					{
						$key=$row[csf("work_order_no")]."__".$row[csf("prod_id")];
					}
					
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_date"]=$row[csf("invoice_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_container_no"]=$row[csf("container_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];
					
					
					if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
					{
						$inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
						$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_pkg_quantity"]+=$row[csf("quantity")];
						$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_value"]+=$row[csf("amount")];
					}
				}
			}
			
		}
		
		
		//var_dump($all_data_arr);die;
		
		
		///########################## based on independent wo PI end ##############################################/////////////
		
		
		///########################## based on independent PI start ##############################################/////////////
		
		
		$sql_pi_inde="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.item_category_id, b.uom, b.quantity as pi_qnty, b.amount as pi_amt, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_type
		from com_pi_master_details a, com_pi_item_details b
		where a.id=b.pi_id and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id not in (0,4,2,3,12,13,14,24,25,28,30) $sql_cond $supplier_cond";
		
		//echo $sql_pi_inde;//die;
		
		$sql_pi_result=sql_select($sql_pi_inde);
		
		if(count($req_result) < 1 && count($wo_result) < 1 && count($sql_pi_result) < 1)
		{
			echo "<span style='font-size:23;font-weight:bold;text-align:center;width:100%'>Data Not Found</span>";die;
		}
		
		$pi_req_arr=$pi_id_arr=$inde_pi_id_arr=array();
		foreach($sql_pi_result as $row)
		{
			$inde_pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
			$inde_pi_num_arr[$row[csf("pi_id")]]=$row[csf("pi_number")];
			if($row[csf("item_category_id")]==1)
			{
				$key=$row[csf("pi_number")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
			}
			else
			{
				$key=$row[csf("pi_number")]."__".$row[csf("prod_id")];
			}
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_id"]=$row[csf("pi_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_number"]=$row[csf("pi_number")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_date"]=$row[csf("pi_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_prod_id"]=$row[csf("prod_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_last_shipment_date"]=$row[csf("last_shipment_date")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_intendor_name"]=$row[csf("intendor_name")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_dtls_id"]=$row[csf("pi_dtls_id")].",";
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_uom"]=$row[csf("uom")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_quantity"]+=$row[csf("pi_qnty")];
			$all_data_arr[$row[csf("item_category_id")]][$key]["pi_amount"]+=$row[csf("pi_amt")];
		} 
		
		//var_dump($all_data_arr);die;
		
		if(!empty($inde_pi_id_arr))
		{
			$inde_pi_idArr = array_flip(array_flip($inde_pi_id_arr));
	        $inde_pi_id_cond = '';

	        if($db_type==2 && count($inde_pi_idArr>1000))
	        {
	            $inde_pi_id_cond = ' and (';
	            $indePiIdArr = array_chunk($inde_pi_idArr,999);
	            foreach($indePiIdArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $inde_pi_id_cond .= " c.id in($ids) or ";
	            }
	            $inde_pi_id_cond = rtrim($inde_pi_id_cond,'or ');
	            $inde_pi_id_cond .= ')';
	        }
	        else
	        {
	            $inde_pi_ids = implode(',', $inde_pi_idArr);
	            $inde_pi_id_cond=" and c.id in ($inde_pi_ids)";
	        }

			$sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.issuing_bank_id, a.etd_date, c.work_order_no, c.work_order_id, c.work_order_dtls_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $inde_pi_id_cond";
			//echo $sql_btb;die;
			
			$btb_result=sql_select($sql_btb);
			$btb_data_array=array();$lc_pi=array();$inde_lc_pi=array();
			foreach($btb_result as $row)
			{
				$inde_lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				if($row[csf("item_category_id")]==1)
				{
					$key=$inde_pi_num_arr[$row[csf("pi_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
				}
				else
				{
					$key=$inde_pi_num_arr[$row[csf("pi_id")]]."__".$row[csf("prod_id")];
				}
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_id"]=$row[csf("lc_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_number"]=$row[csf("lc_number")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_date"]=$row[csf("lc_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_tenor"]=$row[csf("tenor")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_issuing_bank_id"]=$row[csf("issuing_bank_id")];
				$all_data_arr[$row[csf("item_category_id")]][$key]["lc_etd_date"]=$row[csf("etd_date")];
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["lc_value"]+=$row[csf("amount")];
				}
			}
			
			//var_dump($all_data_arr);die;
			if(count($inde_lc_pi)>0)
			{
				$inde_lc_piArr = array_flip(array_flip($inde_lc_pi));
		        $inde_lc_pi_cond = '';

		        if($db_type==2 && count($inde_lc_piArr>1000))
		        {
		            $inde_lc_pi_cond = ' and (';
		            $indeLcPiArr = array_chunk($inde_lc_piArr,999);
		            foreach($indeLcPiArr as $ids)
		            {
		                $ids = implode(',',$ids);
		                $inde_lc_pi_cond .= " c.id in($ids) or ";
		            }
		            $inde_lc_pi_cond = rtrim($inde_lc_pi_cond,'or ');
		            $inde_lc_pi_cond .= ')';
		        }
		        else
		        {
		            $inde_lc_pi_ids = implode(',', $inde_lc_piArr);
		            $inde_lc_pi_cond=" and c.id in ($inde_lc_pi_ids)";
		        }

				$sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.eta_date, c.work_order_no, c.work_order_id, c.work_order_dtls_id
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0 $inde_lc_pi_cond";
				//echo $sql_invoice;die;
				$invoice_result=sql_select($sql_invoice);
				$inv_id_array=array();
				foreach($invoice_result as $row)
				{
					$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
					if($row[csf("item_category_id")]==1)
					{
						$key=$inde_pi_num_arr[$row[csf("pi_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
					}
					else
					{
						$key=$inde_pi_num_arr[$row[csf("pi_id")]]."__".$row[csf("prod_id")];
					}
					
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_date"]=$row[csf("invoice_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_container_no"]=$row[csf("container_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
					$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];
					
					
					if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
					{
						$inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
						$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_pkg_quantity"]+=$row[csf("quantity")];
						$all_data_arr[$row[csf("item_category_id")]][$key]["invoice_value"]+=$row[csf("amount")];
					}
				}
			}
			
		}
		//var_dump($all_data_arr);die;
		
		
		///########################## based on independent wo end    ##############################################/////////////
		//var_dump($inv_id_array);die;
		if(!empty($inv_id_array))
		{
			$inv_idsArr = array_flip(array_flip($inv_id_array));
	        $inv_ids_cond = '';

	        if($db_type==2 && count($inv_idsArr>1000))
	        {
	            $inv_ids_cond = ' and (';
	            $invIdsArr = array_chunk($inv_idsArr,999);
	            foreach($invIdsArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $inv_ids_cond .= " invoice_id in($ids) or ";
	            }
	            $inv_ids_cond = rtrim($inv_ids_cond,'or ');
	            $inv_ids_cond .= ')';
	        }
	        else
	        {
	            $inv_ids = implode(',', $inv_idsArr);
	            $inv_ids_cond=" and invoice_id in ($inv_ids)";
	        }

			$sql_pay="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment where status_active=1 $inv_ids_cond";
			//echo $sql_pay;die;
			$pay_result=sql_select($sql_pay);
			$payment_data_arr=array();
			foreach($pay_result as $row)
			{
				$payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
				$payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				$payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
			}
		}
	
	}

    if(($cbo_date_type==4 &&  ($txt_date_from && $txt_date_to)) ||  ($txt_lc_no!=""))
    {

        $sql_cond="";
        if($cbo_company_name) $sql_cond.=" and a.importer_id='$cbo_company_name' ";
       if($cbo_item_category_id) $sql_cond.=" and a.item_category_id='$cbo_item_category_id' ";
        if($cbo_location) $sql_cond.=" and f.location_id='$cbo_location' ";
        if($cbo_store_name) $sql_cond.=" and f.store_name='$cbo_store_name' ";
        if($txt_lc_no !="")
        {
            $sql_cond.=" and g.lc_number = '$txt_lc_no' ";
        }
        if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and g.lc_date between  '$txt_date_from' and '$txt_date_to'";
        if($db_type==2) $sql_cond.=" and g.item_category_id is not null"; else $sql_cond.=" and g.item_category_id !=''";


        ///############################## based on requistion Wo PI start ##############################################/////////////

		/*$sql_pi_wo_req="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.item_category_id, b.uom, b.quantity as pi_qnty, b.amount as pi_amt, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_type, d.id as wo_mst_id, d.wo_number, d.wo_number_prefix_num, d.wo_date, d.supplier_id as wo_supplier, c.id as wo_dtls_id, c.requisition_dtls_id, c.supplier_order_quantity, c.amount as wo_amount, c.uom as wo_uom, d.currency_id as wo_currency, f.id as req_id, f.requ_no, f.requ_prefix_num, f.requisition_date, f.store_name, f.pay_mode, f.source, f.cbo_currency, f.delivery_date, e.required_for, e.quantity as req_qnty, e.amount as req_amt, e.cons_uom
		from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, wo_non_order_info_mst d, inv_purchase_requisition_dtls e, inv_purchase_requisition_mst f, com_btb_lc_master_details g, com_btb_lc_pi h 
		where a.id=b.pi_id and b.work_order_dtls_id=c.id and h.pi_id=b.pi_id and g.id=h.com_btb_lc_master_details_id and c.mst_id=d.id and c.requisition_dtls_id=e.id and e.mst_id=f.id and d.wo_basis_id=1 and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id not in (0,4,2,3,12,13,14,24,25,28,30) $sql_cond  $supplier_cond";*/


        $sql_pi_wo_req="SELECT a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.item_category_id, b.uom, b.quantity as pi_qnty, b.amount as pi_amt, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_type, d.id as wo_mst_id, d.wo_number, d.wo_number_prefix_num, d.wo_date, d.supplier_id as wo_supplier, c.id as wo_dtls_id, c.requisition_dtls_id, c.supplier_order_quantity, c.amount as wo_amount, c.uom as wo_uom, d.currency_id as wo_currency, f.id as req_id, f.requ_no, f.requ_prefix_num, f.requisition_date, f.store_name, f.pay_mode, f.source, f.cbo_currency, f.delivery_date, e.required_for, e.quantity as req_qnty, e.amount as req_amt, e.cons_uom, e.product_id
		from com_pi_master_details a, com_pi_item_details b 
		left join wo_non_order_info_dtls c on b.work_order_dtls_id=c.id and c.status_active=1
		left join wo_non_order_info_mst d on  c.mst_id=d.id and d.wo_basis_id=1 and d.status_active=1
		left join inv_purchase_requisition_dtls e on c.requisition_dtls_id=e.id and e.status_active=1
		left join inv_purchase_requisition_mst f on e.mst_id=f.id  and f.status_active=1,
		com_btb_lc_master_details g, com_btb_lc_pi h 
		where a.id=b.pi_id and h.pi_id=b.pi_id and g.id=h.com_btb_lc_master_details_id  and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id in (1,2) and a.status_active=1  and g.status_active=1 and h.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id not in (0,4,2,3,12,13,14,24,25,28,30) $sql_cond  $supplier_cond";

        // echo $sql_pi_wo_req;//die;



        $req_result=sql_select($sql_pi_wo_req);
        //var_dump($req_result);die;

        $req_no_arr=$req_dtls_id_arr=$all_data_arr=$req_pi_id_arr=array();
        foreach($req_result as $row)
        {
            $req_pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
            $req_no_arr[$row[csf("pi_dtls_id")]]=$row[csf("requ_no")];
            if($row[csf("item_category_id")]==1)
            {
                $key=$row[csf("requ_no")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
            }
            else
            {
                $key=$row[csf("requ_no")]."__".$row[csf("prod_id")];
            }

            $all_data_arr[$row[csf("item_category_id")]][$key]["product_id"]=$row[csf("product_id")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_req_id"]=$row[csf("req_id")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["req_dtsl_id"].=$row[csf("req_dtsl_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_no"]=$row[csf("requ_no")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_requisition_date"]=$row[csf("requisition_date")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_store_name"]=$row[csf("store_name")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_pay_mode"]=$row[csf("pay_mode")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_delivery_date"]=$row[csf("delivery_date")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_required_for"]=$row[csf("required_for")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_quantity"]=$row[csf("req_qnty")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_amount"]=$row[csf("req_amt")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_cons_uom"]=$row[csf("cons_uom")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["requ_cbo_currency"]=$row[csf("cbo_currency")];

            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_mst_id"].=$row[csf("wo_mst_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_number"].=$row[csf("wo_number")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_number_prefix_num"].=$row[csf("wo_number_prefix_num")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_date"]=$row[csf("wo_date")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_id"]=$row[csf("supplier_id")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_amount"]+=$row[csf("wo_amount")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_uom"]=$row[csf("wo_uom")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_currency_id"]=$row[csf("wo_currency")];

            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_id"].=$row[csf("pi_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_number"].=$row[csf("pi_number")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_date"]=$row[csf("pi_date")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_last_shipment_date"]=$row[csf("last_shipment_date")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_intendor_name"]=$row[csf("intendor_name")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_uom"]=$row[csf("uom")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_quantity"]+=$row[csf("pi_qnty")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_amount"]+=$row[csf("pi_amt")];
        }


        //var_dump($all_data_arr);die;




        if(!empty($req_pi_id_arr))
        {
            $req_pi_id_Arr = array_flip(array_flip($req_pi_id_arr));
            $req_pi_ids_cond = '';

            if($db_type==2 && count($req_pi_id_Arr>1000))
            {
                $req_pi_ids_cond = ' and (';
                $reqPiIdArr = array_chunk($req_pi_id_Arr,999);
                foreach($reqPiIdArr as $ids)
                {
                    $ids = implode(',',$ids);
                    $req_pi_ids_cond .= " c.id in($ids) or ";
                }
                $req_pi_ids_cond = rtrim($req_pi_ids_cond,'or ');
                $req_pi_ids_cond .= ')';
            }
            else
            {
                $req_pi_ids = implode(',', $req_pi_id_Arr);
                $req_pi_ids_cond=" and c.id in ($req_pi_ids)";
            }

            $sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.issuing_bank_id, a.etd_date 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_pi_ids_cond";
            //echo $sql_btb;die;

            $btb_result=sql_select($sql_btb);
            $btb_data_array=$wo_lc_pi=array();
            foreach($btb_result as $row)
            {
                $wo_lc_pi[$row[csf("pi_id")]]=$row[csf("pi_id")];
                if($row[csf("item_category_id")]==1)
                {
                    $key=$req_no_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
                }
                else
                {
                    $key=$req_no_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("prod_id")];
                }
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_id"]=$row[csf("lc_id")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_number"]=$row[csf("lc_number")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_date"]=$row[csf("lc_date")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_tenor"]=$row[csf("tenor")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_issuing_bank_id"]=$row[csf("issuing_bank_id")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_etd_date"]=$row[csf("etd_date")];

                if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
                {
                    $pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["lc_value"]+=$row[csf("amount")];
                }
            }
            //ksort($all_data_arr);
            //var_dump($all_data_arr);die;

            if(count($wo_lc_pi)>0)
            {
                $wo_lc_pi_Arr = array_flip(array_flip($wo_lc_pi));
                $wo_lc_pi_cond = '';

                if($db_type==2 && count($wo_lc_pi_Arr)>1000)
                {
                    $wo_lc_pi_cond = ' and (';
                    $woLcPiArr = array_chunk($wo_lc_pi_Arr,999);
                    foreach($woLcPiArr as $ids)
                    {
                        $ids = implode(',',$ids);
                        $wo_lc_pi_cond .= " c.pi_id in($ids) or ";
                    }
                    $wo_lc_pi_cond = rtrim($wo_lc_pi_cond,'or ');
                    $wo_lc_pi_cond .= ')';
                }
                else
                {
                    $wo_lc_pi_ids = implode(',', $wo_lc_pi_Arr);
                    $wo_lc_pi_cond=" and c.pi_id in ($wo_lc_pi_ids)";
                }
				//  if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and g.lc_date between  '$txt_date_from' and '$txt_date_to'";


                $sql_invoice=" SELECT a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.eta_date
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0 $wo_lc_pi_cond";
                // echo $sql_invoice;die;
                $invoice_result=sql_select($sql_invoice);
                foreach($invoice_result as $row)
                {
                    $inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
                    if($row[csf("item_category_id")]==1)
                    {
                        $key=$req_no_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
                    }
                    else
                    {
                        $key=$req_no_arr[$row[csf("pi_dtls_id")]]."__".$row[csf("prod_id")];
                    }

                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_date"]=$row[csf("invoice_date")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_container_no"]=$row[csf("container_no")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];

                    if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
                    {
                        $inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                        $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_pkg_quantity"]+=$row[csf("quantity")];
                        $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_value"]+=$row[csf("amount")];
                    }
                }
            }

            //var_dump($all_data_arr);die;
        }

        ///############################## based on requistion Wo PI End ##############################################/////////////


        ///########################## based on independent wo PI start ##############################################/////////////

        $sql_pi_wo="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.item_category_id, b.uom, b.quantity as pi_qnty, b.amount as pi_amt, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_type, d.id as wo_mst_id, d.wo_number, d.wo_number_prefix_num, d.wo_date, d.supplier_id as wo_supplier, c.id as wo_dtls_id, c.supplier_order_quantity, c.amount as wo_amount, c.uom as wo_uom, d.currency_id as wo_currency
		from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, wo_non_order_info_mst d
		where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and d.wo_basis_id <>1 and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id not in (0,4,2,3,12,13,14,24,25,28,30) $sql_cond  $supplier_cond ";



        //echo $sql_pi_wo;//die;

        $wo_result=sql_select($sql_pi_wo);

        $wo_data_array=$all_inde_wo_pi_id=array();
        foreach($wo_result as $row)
        {
            $all_inde_wo_pi_id[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
            if($row[csf("item_category_id")]==1)
            {
                $key=$row[csf("wo_number")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
            }
            else
            {
                $key=$row[csf("wo_number")]."__".$row[csf("prod_id")];
            }

            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_mst_id"]=$row[csf("wo_mst_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_number"]=$row[csf("wo_number")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_number_prefix_num"]=$row[csf("wo_number_prefix_num")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_date"]=$row[csf("wo_date")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_id"]=$row[csf("wo_supplier")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_amount"]+=$row[csf("wo_amount")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_uom"]=$row[csf("wo_uom")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["wo_currency_id"]=$row[csf("wo_currency")];


            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_id"].=$row[csf("pi_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_number"].=$row[csf("pi_number")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_date"]=$row[csf("pi_date")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_last_shipment_date"]=$row[csf("last_shipment_date")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_intendor_name"]=$row[csf("intendor_name")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_uom"]=$row[csf("uom")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_quantity"]+=$row[csf("pi_qnty")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_amount"]+=$row[csf("pi_amt")];

        }


        //var_dump($all_data_arr);die;


        if(!empty($all_inde_wo_pi_id))
        {
            $all_inde_wo_pi_idsArr = array_flip(array_flip($all_inde_wo_pi_id));
            $all_inde_wo_pi_ids_cond = '';

            if($db_type==2 && count($all_inde_wo_pi_idsArr>1000))
            {
                $all_inde_wo_pi_ids_cond = ' and (';
                $allIndeWoPiIdsArr = array_chunk($all_inde_wo_pi_idsArr,999);
                foreach($allIndeWoPiIdsArr as $ids)
                {
                    $ids = implode(',',$ids);
                    $all_inde_wo_pi_ids_cond .= " c.id in($ids) or ";
                }
                $all_inde_wo_pi_ids_cond = rtrim($all_inde_wo_pi_ids_cond,'or ');
                $all_inde_wo_pi_ids_cond .= ')';
            }
            else
            {
                $all_inde_wo_pi_ids = implode(',', $all_inde_wo_pi_idsArr);
                $all_inde_wo_pi_ids_cond=" and c.id in ($all_inde_wo_pi_ids)";
            }

            $sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.issuing_bank_id, a.etd_date, c.work_order_no, c.work_order_id, c.work_order_dtls_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_inde_wo_pi_ids_cond";
            //echo $sql_btb;die;

            $btb_result=sql_select($sql_btb);
            $btb_data_array=array();$lc_pi=$inde_wo_lc_pi=array();
            foreach($btb_result as $row)
            {
                $inde_wo_lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                if($row[csf("item_category_id")]==1)
                {
                    $key=$row[csf("work_order_no")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
                }
                else
                {
                    $key=$row[csf("work_order_no")]."__".$row[csf("prod_id")];
                }
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_id"]=$row[csf("lc_id")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_number"]=$row[csf("lc_number")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_date"]=$row[csf("lc_date")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_tenor"]=$row[csf("tenor")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_issuing_bank_id"]=$row[csf("issuing_bank_id")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_etd_date"]=$row[csf("etd_date")];

                if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
                {
                    $pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["lc_value"]+=$row[csf("amount")];
                }

            }

            //var_dump($all_data_arr);die;
            if(count($inde_wo_lc_pi)>0)
            {
                $inde_wo_lc_piArr = array_flip(array_flip($inde_wo_lc_pi));
                $inde_wo_lc_pi_cond = '';

                if($db_type==2 && count($inde_wo_lc_piArr>1000))
                {
                    $inde_wo_lc_pi_cond = ' and (';
                    $indeWoLcPiArr = array_chunk($inde_wo_lc_piArr,999);
                    foreach($indeWoLcPiArr as $ids)
                    {
                        $ids = implode(',',$ids);
                        $inde_wo_lc_pi_cond .= " c.id in($ids) or ";
                    }
                    $inde_wo_lc_pi_cond = rtrim($inde_wo_lc_pi_cond,'or ');
                    $inde_wo_lc_pi_cond .= ')';
                }
                else
                {
                    $inde_wo_lc_pis = implode(',', $inde_wo_lc_piArr);
                    $inde_wo_lc_pi_cond=" and c.id in ($inde_wo_lc_pis)";
                }

                $sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.eta_date, c.work_order_no, c.work_order_id, c.work_order_dtls_id
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0 $inde_wo_lc_pi_cond";
                //echo $sql_invoice;die;
                $invoice_result=sql_select($sql_invoice);
                $inv_id_array=array();
                foreach($invoice_result as $row)
                {
                    $inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
                    if($row[csf("item_category_id")]==1)
                    {
                        $key=$row[csf("work_order_no")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
                    }
                    else
                    {
                        $key=$row[csf("work_order_no")]."__".$row[csf("prod_id")];
                    }

                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_date"]=$row[csf("invoice_date")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_container_no"]=$row[csf("container_no")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];


                    if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
                    {
                        $inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                        $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_pkg_quantity"]+=$row[csf("quantity")];
                        $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_value"]+=$row[csf("amount")];
                    }
                }
            }

        }


        //var_dump($all_data_arr);die;


        ///########################## based on independent wo PI end ##############################################/////////////


        ///########################## based on independent PI start ##############################################/////////////


        $sql_pi_inde="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.item_category_id, b.uom, b.quantity as pi_qnty, b.amount as pi_amt, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_type
		from com_pi_master_details a, com_pi_item_details b
		where a.id=b.pi_id and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id not in (0,4,2,3,12,13,14,24,25,28,30) $sql_cond $supplier_cond";

        //echo $sql_pi_inde;//die;

        $sql_pi_result=sql_select($sql_pi_inde);

        if(count($req_result) < 1 && count($wo_result) < 1 && count($sql_pi_result) < 1)
        {
            echo "<span style='font-size:23;font-weight:bold;text-align:center;width:100%'>Data Not Found</span>";die;
        }

        $pi_req_arr=$pi_id_arr=$inde_pi_id_arr=array();
        foreach($sql_pi_result as $row)
        {
            $inde_pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
            $inde_pi_num_arr[$row[csf("pi_id")]]=$row[csf("pi_number")];
            if($row[csf("item_category_id")]==1)
            {
                $key=$row[csf("pi_number")]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
            }
            else
            {
                $key=$row[csf("pi_number")]."__".$row[csf("prod_id")];
            }
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_id"]=$row[csf("pi_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_number"]=$row[csf("pi_number")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_date"]=$row[csf("pi_date")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_prod_id"]=$row[csf("prod_id")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_last_shipment_date"]=$row[csf("last_shipment_date")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_intendor_name"]=$row[csf("intendor_name")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_dtls_id"]=$row[csf("pi_dtls_id")].",";
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_uom"]=$row[csf("uom")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_quantity"]+=$row[csf("pi_qnty")];
            $all_data_arr[$row[csf("item_category_id")]][$key]["pi_amount"]+=$row[csf("pi_amt")];
        }

        //var_dump($all_data_arr);die;

        if(!empty($inde_pi_id_arr))
        {
            $inde_pi_idArr = array_flip(array_flip($inde_pi_id_arr));
            $inde_pi_id_cond = '';

            if($db_type==2 && count($inde_pi_idArr>1000))
            {
                $inde_pi_id_cond = ' and (';
                $indePiIdArr = array_chunk($inde_pi_idArr,999);
                foreach($indePiIdArr as $ids)
                {
                    $ids = implode(',',$ids);
                    $inde_pi_id_cond .= " c.id in($ids) or ";
                }
                $inde_pi_id_cond = rtrim($inde_pi_id_cond,'or ');
                $inde_pi_id_cond .= ')';
            }
            else
            {
                $inde_pi_ids = implode(',', $inde_pi_idArr);
                $inde_pi_id_cond=" and c.id in ($inde_pi_ids)";
            }

            $sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.issuing_bank_id, a.etd_date, c.work_order_no, c.work_order_id, c.work_order_dtls_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $inde_pi_id_cond";
            //echo $sql_btb;die;

            $btb_result=sql_select($sql_btb);
            $btb_data_array=array();$lc_pi=array();$inde_lc_pi=array();
            foreach($btb_result as $row)
            {
                $inde_lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                if($row[csf("item_category_id")]==1)
                {
                    $key=$inde_pi_num_arr[$row[csf("pi_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
                }
                else
                {
                    $key=$inde_pi_num_arr[$row[csf("pi_id")]]."__".$row[csf("prod_id")];
                }
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_id"]=$row[csf("lc_id")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_number"]=$row[csf("lc_number")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_date"]=$row[csf("lc_date")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_tenor"]=$row[csf("tenor")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_issuing_bank_id"]=$row[csf("issuing_bank_id")];
                $all_data_arr[$row[csf("item_category_id")]][$key]["lc_etd_date"]=$row[csf("etd_date")];

                if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
                {
                    $pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["lc_value"]+=$row[csf("amount")];
                }
            }

            //var_dump($all_data_arr);die;
            if(count($inde_lc_pi)>0)
            {
                $inde_lc_piArr = array_flip(array_flip($inde_lc_pi));
                $inde_lc_pi_cond = '';

                if($db_type==2 && count($inde_lc_piArr>1000))
                {
                    $inde_lc_pi_cond = ' and (';
                    $indeLcPiArr = array_chunk($inde_lc_piArr,999);
                    foreach($indeLcPiArr as $ids)
                    {
                        $ids = implode(',',$ids);
                        $inde_lc_pi_cond .= " c.id in($ids) or ";
                    }
                    $inde_lc_pi_cond = rtrim($inde_lc_pi_cond,'or ');
                    $inde_lc_pi_cond .= ')';
                }
                else
                {
                    $inde_lc_pi_ids = implode(',', $inde_lc_piArr);
                    $inde_lc_pi_cond=" and c.id in ($inde_lc_pi_ids)";
                }

                $sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.id as pi_dtls_id, c.item_prod_id as prod_id, c.item_category_id, c.uom, c.quantity, c.amount, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_type, a.eta_date, c.work_order_no, c.work_order_id, c.work_order_dtls_id
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0 $inde_lc_pi_cond";
                //echo $sql_invoice;die;
                $invoice_result=sql_select($sql_invoice);
                $inv_id_array=array();
                foreach($invoice_result as $row)
                {
                    $inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
                    if($row[csf("item_category_id")]==1)
                    {
                        $key=$inde_pi_num_arr[$row[csf("pi_id")]]."__".$row[csf("color_id")]."__".$row[csf("count_name")]."__".$row[csf("yarn_composition_item1")]."__".$row[csf("yarn_type")];
                    }
                    else
                    {
                        $key=$inde_pi_num_arr[$row[csf("pi_id")]]."__".$row[csf("prod_id")];
                    }

                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_date"]=$row[csf("invoice_date")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_container_no"]=$row[csf("container_no")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
                    $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];


                    if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
                    {
                        $inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                        $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_pkg_quantity"]+=$row[csf("quantity")];
                        $all_data_arr[$row[csf("item_category_id")]][$key]["invoice_value"]+=$row[csf("amount")];
                    }
                }
            }

        }
        //var_dump($all_data_arr);die;


        ///########################## based on independent wo end    ##############################################/////////////
        //var_dump($inv_id_array);die;
        if(!empty($inv_id_array))
        {
            $inv_idsArr = array_flip(array_flip($inv_id_array));
            $inv_ids_cond = '';

            if($db_type==2 && count($inv_idsArr>1000))
            {
                $inv_ids_cond = ' and (';
                $invIdsArr = array_chunk($inv_idsArr,999);
                foreach($invIdsArr as $ids)
                {
                    $ids = implode(',',$ids);
                    $inv_ids_cond .= " invoice_id in($ids) or ";
                }
                $inv_ids_cond = rtrim($inv_ids_cond,'or ');
                $inv_ids_cond .= ')';
            }
            else
            {
                $inv_ids = implode(',', $inv_idsArr);
                $inv_ids_cond=" and invoice_id in ($inv_ids)";
            }

            $sql_pay="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment where status_active=1 $inv_ids_cond";
            //echo $sql_pay;die;
            $pay_result=sql_select($sql_pay);
            $payment_data_arr=array();
            foreach($pay_result as $row)
            {
                $payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
                $payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
                $payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
            }
        }

    }
	ksort($all_data_arr);
	//var_dump($payment_data_arr);die;
	
	if($cbo_item_category_id) 
    {
        if ($cbo_item_category_id == 11) $rcv_category_cond = " and b.item_category  in($cbo_item_category_id,4)"; 
        else $rcv_category_cond = " and b.item_category  in($cbo_item_category_id)";
    }
    else $rcv_category_cond= "";
 	
 	$rcv_return_sql=sql_select("select a.received_id,b.receive_basis, b.pi_wo_batch_no, b.item_category, a.pi_id, b.prod_id, c.color, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, b.cons_quantity, b.cons_amount, c.entry_form
		from inv_issue_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and b.company_id=$cbo_company_name and c.entry_form<>24 $rcv_category_cond ");
		
	$return_data=array();
	foreach($rcv_return_sql as $row)
	{
		/*$return_data[$row[csf("received_id")]]["cons_quantity"]=$row[csf("cons_quantity")];
		$return_data[$row[csf("received_id")]]["cons_amount"]=$row[csf("cons_amount")];

		$return_data[$row[csf("pi_id")]][$row[csf("color")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["cons_quantity"]+=$row[csf("cons_quantity")];
		$return_data[$row[csf("pi_id")]][$row[csf("color")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["cons_amount"]+=$row[csf("cons_amount")];
		if($row[csf("entry_form")]==24 && $row[csf("item_category")]==4)
		{
			$return_data[$row[csf("receive_basis")]][$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
			$return_data[$row[csf("receive_basis")]][$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
		}
		else
		{
			$return_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
			$return_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
		}*/

		$return_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
		$return_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];

	}
	//$conversion_factor = return_library_array("select id,conversion_factor from lib_item_group where item_category=4","id","conversion_factor");
	$sql_receive=sql_select("select a.receive_basis, a.booking_id, a.exchange_rate, a.receive_date,a.challan_no, b.prod_id, b.item_category, c.color, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, b.order_qnty as rcv_qnty, b.order_amount as rcv_amt, c.entry_form, a.id as received_id, c.item_group_id, c.item_description, c.item_color, c.item_size, c.brand_supplier 
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id= b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) $rcv_category_cond and b.item_category not in (0,2,3,12,13,14,24,25,28,30)");
	$recv_data_array=array();
	foreach($sql_receive as $row)
	{
		/*if($row[csf("item_category")]==1)
		{
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("color")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["rcv_qnty"]+=$row[csf("rcv_qnty")];
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("color")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["rcv_amt"]+=$row[csf("rcv_amt")];
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("color")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["exRate"]=$row[csf("exchange_rate")];
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("color")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["prod_id"].=$row[csf("prod_id")].",";
		}
		else
		{
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("prod_id")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("prod_id")]]["rcv_amt"]=$row[csf("rcv_amt")];
		}elseif ($row[csf("item_category")]==4 && $row[csf("item_category")]==24) 
		{
			$key=$row[csf("item_group_id")]."**".$row[csf("item_description")]."**".$row[csf("item_color")]."**".$row[csf("item_size")]."**".$row[csf("brand_supplier")];
		 	$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["rcv_qnty"]=$row[csf("rcv_qnty")]-($return_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"]/$conversion_factor[$row[csf("item_group_id")]]);
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["rcv_amt"]=$row[csf("rcv_amt")]-($return_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]/$row[csf("exchange_rate")]);
		}*/

		if($row[csf("item_category")]==1)
		{
			$key=$row[csf("color")]."**".$row[csf("yarn_count_id")]."**".$row[csf("yarn_comp_type1st")]."**".$row[csf("yarn_type")];
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["rcv_qnty"]+=$row[csf("rcv_qnty")]-$return_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"];
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["rcv_amt"]+=$row[csf("rcv_amt")]-($return_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]/$row[csf("exchange_rate")]);
			
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["prod_id"].=$row[csf("prod_id")].",";
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["receive_date"].=$row[csf("receive_date")].',';
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["challan_no"].=$row[csf("challan_no")].',';
		}
		else
		{
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("prod_id")]]["rcv_qnty"]+=$row[csf("rcv_qnty")]-$return_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"];
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("prod_id")]]["rcv_amt"]+=$row[csf("rcv_amt")]-($return_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]/$row[csf("exchange_rate")]);
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("prod_id")]]["receive_date"].=$row[csf("receive_date")].',';
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("prod_id")]]["challan_no"].=$row[csf("challan_no")].',';
		}
		
	}
	// echo "<pre>";print_r($recv_data_array);die;

	$sql_data=sql_select("SELECT a.id, a.sys_number, a.req_item_mst_id, b.prod_id from req_comparative_mst a, req_comparative_dtls b where a.id=b.mst_id and a.entry_form=481 and a.is_deleted=0 and a.status_active=1");

	$cs_data_array=array();
	foreach($sql_data as $row){
		$cs_data_array[$row[csf('req_item_mst_id')]][$row[csf('prod_id')]]["sys_number"]=$row[csf('sys_number')];
		$cs_data_array[$row[csf('req_item_mst_id')]][$row[csf('prod_id')]]["id"]=$row[csf('id')];
	}

	/*$rcv_return_sql=sql_select("select b.prod_id, a.received_id, b.cons_quantity, b.cons_quantity, b.cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=8 and b.transaction_type=3 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$rcv_rtn_data=array();
	foreach($rcv_return_sql as $row)
	{
		$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
		$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
	}*/
	
	//$exchange_rate_array = return_library_array("select id,exchange_rate from  inv_receive_master where atatus_active=1 and is_deleted=0 ","id","exchange_rate");	
	//echo "<pre>";print_r($recv_data_array);die;

	if($cbo_item_category_id) $wo_qnty_arr_category_cond = " and  b.item_category_id in ($cbo_item_category_id)" ; else $wo_qnty_arr_category_cond= "";
	$wo_qty_arr=sql_select("select b.requisition_dtls_id, b.item_id as prod_id, sum(b.supplier_order_quantity) as qty 
	from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_name $wo_qnty_arr_category_cond and  b.item_category_id not in (0,1,2,3,12,13,14,24,25,28,30)  and a.wo_basis_id=1 and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.requisition_dtls_id>0 
	group by b.requisition_dtls_id,b.item_id");
	$wo_pipe_array=array();
	foreach($wo_qty_arr as $row)
	{
		$wo_pipe_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]] += $row[csf("qty")];
	}
	
	if ($cbo_item_category_id) $pi_qnty_arr_category_cond = "  and c.item_category_id in($cbo_item_category_id)"; else $pi_qnty_arr_category_cond = "";
	$pi_qty_arr=sql_select("select c.requisition_dtls_id, b.item_prod_id as prod_id, sum(b.quantity) as qty 
	from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c 
	where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.importer_id=$cbo_company_name $pi_qnty_arr_category_cond and c.item_category_id not in (0,4,1,2,3,12,13,14,24,25,28,30) and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by c.requisition_dtls_id,b.item_prod_id"); 
	$pi_pipe_array=array();
	foreach($pi_qty_arr as $row)
	{
		$pi_pipe_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]=$row[csf("qty")];
	}
	
	$item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
	$store_array = return_library_array("select id,store_name from  lib_store_location ","id","store_name");
	$suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
	$indentor_name_array = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40","id","supplier_name");
	$category_summary=array();
	ob_start();
	?>
	<div style="width:5760px; margin-left:10px">
		<fieldset style="width:100%;">	 
			<table width="5760" cellpadding="0" cellspacing="0" id="caption">
				<tr>
					<td align="center" width="100%" colspan="21" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" colspan="21" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>  
			</table>
			<br />
			
				<?
				$i=1;$j=1;$p=1;$q=1;
				// $is_approved copied from array_function. added 0 index
				$is_approved = array(0 => 'No', 1 => 'Yes', 2 => 'No', 3 => 'Partial Approved');
				$btb_tem_lc_array=$inv_temp_array=array();

				$print_report_format=return_field_value('format_id', 'lib_report_template', "template_name =".$cbo_company_name." and module_id=5 and report_id=69 and is_deleted=0 and status_active=1");
				$format_ids=explode(',', $print_report_format);

				$variable='';

				foreach($all_data_arr as $category_id=>$category_val)
				{
					if($category_id==1)
					{
						if($p==1)
						{
							?>
							<table width="6328" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th colspan="16" >Requisiton Details</th>
									<th colspan="13" >Work Order Details</th>
									<th colspan="12" >PI Details</th>
									<th colspan="10">L/C Details</th>
									<th colspan="15">Invoice Details</th>
									<th colspan="4">Payment Details</th>
									<th colspan="5">Store details</th>
								</tr>
								<tr>
									<!--990 requisition details 12 -->    
									<th width="30">SL</th>
									<th width="50">Req. No</th>
									<th width="50">CS Number</th>
									<th width="50">Apprv. Status</th>
									<th width="70">Req. Date</th>
									<th width="120">Req. Appr. Date</th>
									<th width="80">Req. By</th>									
									<th width="100">Color</th>
									<th width="70">Yarn Count</th>
									<th width="120">Composition</th> 
									<th width="80">Yarn Type</th>
									<th width="70">UOM</th>
									<th width="100">Req. Qnty</th>
									<th width="70">Unit Price</th>
									<th width="100">Req. Amount </th>
									<th width="70">Currency</th>
									
									
									<!--1050 wo details 12 -->
									<th width="50">WO No</th>
									<th width="70">WO Date</th>
									<th width="150">Supplier</th>
									<th width="100">Insert User</th>
									<th width="100">Color</th>
									<th width="70">Yarn Count</th>
									<th width="120">Composition</th> 
									<th width="80">Yarn Type</th>
									<th width="70">UOM</th>
									<th width="100">WO Qnty</th>
									<th width="70">Unit Price</th>
									<th width="100">WO Amount </th>
									<th width="70">Currency</th>
									
									
									<!--1050 pi details 12 -->
									<th width="50">PI No</th>
									<th width="70">PI Date</th>
									<th width="150">Supplier</th>
									<th width="100">Color</th>
									<th width="70">Yarn Count</th>
									<th width="120">Composition</th> 
									<th width="80">Yarn Type</th>
									<th width="70">UOM</th>
									<th width="100">PI Qnty</th>
									<th width="70">Unit Price</th>
									<th width="100">PI Amount </th>
									<th width="70">Currency</th>
									
									<!--830 lc details 10 -->
									<th width="70">LC Date</th>
									<th width="120">LC No</th>
									<th width="120">Issuing Bank</th>
									<th width="80">Pay Term</th>
									<th width="50">Tenor</th>
									<th width="80">LC Amount</th>
									<th width="70">Shipment Date</th>
									<th width="80">Expiry Date</th>
									<th width="80">ETD Date</th>
									<th width="80">ETD Port</th>
									
									<!--1340 Invoice details 15 -->
									<th width="80">ETA Date</th>
									<th width="80">ETA Port</th>
									<th width="150">Invoice No</th>
									<th width="70">Invoice Date</th>
									<th width="80">Incoterm</th>
									<th width="100">Incoterm Place</th>
									<th width="80">B/L No</th>
									<th width="70">BL Date</th>
									<th width="100">Mother Vassel</th>
									<th width="100">Feedar Vassel</th>
									<th width="100">Continer No</th>
									<th width="80">Pkg Qty</th>
									<th width="100">Doc Send to CNF</th>
									<th width="70">NN Doc Received Date</th>
									<th width="80">Bill Of Entry No</th>
									
									<!--290 Payment details 4 -->
									<th width="70">Maturity Date</th>
									<th width="70">Maturity Month</th>
									<th width="70">Payment Date</th>
									<th width="80">Paid Amount</th>
									
									<!--240 MRR details 3 -->
									<th width="80">MRR Qnty</th>
									<th width="80">MRR Value</th>
									<th  width="80">Short Value</th>
									<th width="80">1st Rcv Date</th>
									<th width="80">last Rcv Date</th>
								</tr>
							</thead>
							</table>
							<div style="width:6328px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
							<table width="6328" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tbody id="table_body">
                            	<tr>
                                    <th colspan="75" style="text-align: left !important; color: black" bgcolor="#FFFFCC"><? echo  $item_category[$category_id]; ?> :</th>
                                </tr>
							<?
						}

						foreach($category_val as $prod_data_ref=>$prod_val)
						{
							//$row[csf("requ_no")]."__".$row[csf("color_id")]."__".$row[csf("count_id")]."__".$row[csf("composition_id")]."__".$row[csf("yarn_type_id")];
							$prod_data_ref=explode("__",$prod_data_ref);
							$requ_no=$prod_data_ref[0];
							$color_id=$prod_data_ref[1];
							$count_id=$prod_data_ref[2];
							$composition_id=$prod_data_ref[3];
							$yarn_type_id=$prod_data_ref[4];
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";

							$req_id = $prod_val['requ_req_id'];

							switch ($format_ids[0]) {
								case 134:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"fnc_yarn_req_entry(4, $req_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 135:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"fnc_yarn_req_entry(6, $req_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 136:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"fnc_yarn_req_entry(7, $req_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 137:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"fnc_yarn_req_entry(8, $req_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 64:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"fnc_yarn_req_entry(9, $req_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								
								default:
									$variable = $prod_val["requ_prefix_num"];
									break;
							}

						
							$sys_data= $cs_data_array[$prod_val['requ_req_id']][$prod_val['prod_id']]["sys_number"];
							$sys_id= $cs_data_array[$prod_val['requ_req_id']][$prod_val['prod_id']]["id"];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<!--990 requisition details 12 -->    
								<td  width="30" align="center"><? echo $i; ?></td>
								<td  width="50" align="center">
									<p>
										<a href='##' style='color:#000'>
											<?php echo $variable; ?>
										</a>
									</p> 
								</td>
								<td width="50" align="center">
									<p><a href="##" onClick="generate_report_comparative_statement('<?=$sys_id;?>','<?=$sys_data;?>')">
										<?php echo $sys_data; ?>
										</a> 
									</p>
								</td>
								<td width="50" align="center"><p><?php echo $is_approved[$prod_val['is_approved']]; ?></p></td>
								<td width="70" align="center"><? if($prod_val["requ_requisition_date"]!="" &&  $prod_val["requ_requisition_date"]!="0000-00-00") echo change_date_format($prod_val["requ_requisition_date"]); ?></td>
								<td width="120" align="center"><p><? echo $prod_val["approved_date"]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $prod_val["req_by"]; ?>&nbsp;</p></td>
								<td width="100"><p><? if($prod_val["requ_prefix_num"]) echo $color_name_arr[$color_id]; ?>&nbsp;</p></td>
								<td width="70"><p><? if($prod_val["requ_prefix_num"]) echo $count_arr[$count_id]; ?>&nbsp;</p></td>
								<td width="120"><p><? if($prod_val["requ_prefix_num"]) echo $composition[$composition_id]; ?>&nbsp;</p></td> 
								<td width="80"><p><? if($prod_val["requ_prefix_num"]) echo $yarn_type[$yarn_type_id]; ?>&nbsp;</p></td>
								<td width="70" align="center"><p><? echo $unit_of_measurement[$prod_val["requ_cons_uom"]]; ?>&nbsp;</p></td>
								<td width="100" align="right"><? echo  number_format($prod_val["requ_quantity"],2);$total_req_qnty+=$prod_val["requ_quantity"]; ?></td>
								<td width="70" align="right"><? if($prod_val["requ_amount"]>0 && $prod_val["requ_quantity"]>0) { $req_rate=$prod_val["requ_amount"]/$prod_val["requ_quantity"]; echo  number_format($req_rate,2);} else echo "0.00"; ?></td>
								<td width="100" align="right"><? echo  number_format($prod_val["requ_amount"],2);$total_req_amount+=$prod_val["requ_amount"]; ?></td>
								<td width="70" align="center"><p><? echo $currency[$prod_val["requ_cbo_currency"]]; ?> &nbsp;</p></td>
								
								
								<!--1050 wo details 12 -->
								<td  width="50" align="center"><p><? echo chop($prod_val["wo_number_prefix_num"],","); ?>&nbsp;</p></td>
								<td  width="70" align="center"><? if($prod_val["wo_date"]!="" &&  $prod_val["wo_date"]!="0000-00-00")echo change_date_format($prod_val["wo_date"]); ?></td>
								<td  width="150"><p><? echo $suplier_array[$prod_val["wo_supplier_id"]];?>&nbsp;</p></td>
								<td  width="100"><p><? echo $user_library[$prod_val["inserted_by"]];?>&nbsp;</p></td>
								<td  width="100"><p><? if($prod_val["wo_number_prefix_num"]) echo $color_name_arr[$color_id]; ?>&nbsp;</p></td>
								<td  width="70"><p><? if($prod_val["wo_number_prefix_num"])echo $count_arr[$count_id]; ?>&nbsp;</p></td>
								<td  width="120"><p><? if($prod_val["wo_number_prefix_num"])echo $composition[$composition_id]; ?>&nbsp;</p></td> 
								<td  width="80"><p><? if($prod_val["wo_number_prefix_num"])echo $yarn_type[$yarn_type_id]; ?>&nbsp;</p></td>
								<td  width="70" align="center"><p><? echo $unit_of_measurement[$prod_val["wo_uom"]]; ?>&nbsp;</p></td>
								<td  width="100" align="right"><? echo  number_format($prod_val["wo_supplier_order_quantity"],2);$total_wo_qnty+=$prod_val["wo_supplier_order_quantity"]; ?></td>
								<td  width="70" align="right"><? if($prod_val["wo_amount"]>0 && $prod_val["wo_supplier_order_quantity"]>0) { $wo_rate=$prod_val["wo_amount"]/$prod_val["wo_supplier_order_quantity"]; echo  number_format($wo_rate,2);} else echo "0.00"; ?></td>
								<td  width="100" align="right"><? echo number_format($prod_val["wo_amount"],2);$total_wo_amt+=$prod_val["wo_amount"]; ?></td>
								<td  width="70" align="center"><p><? echo $currency[$prod_val["wo_currency_id"]]; ?> &nbsp;</p></td>
								
								
								<!--1050 pi details 12 -->
								<td width="50"><p><? echo chop($prod_val["pi_number"],","); ?> &nbsp;</p></td>
								<td width="70" align="center"><? if($prod_val["pi_date"]!="" &&  $prod_val["pi_date"]!="0000-00-00")echo change_date_format($prod_val["pi_date"]); ?></td>
								<td width="150"><p><? echo $suplier_array[$prod_val["pi_supplier_id"]];?>&nbsp;</p></td>
								<td width="100"><p><? if(chop($prod_val["pi_number"],",")!="") echo $color_name_arr[$color_id]; ?>&nbsp;</p></td>
								<td width="70"><p><? if(chop($prod_val["pi_number"],",")!="") echo $count_arr[$count_id]; ?>&nbsp;</p></td>
								<td width="120"><p><? if(chop($prod_val["pi_number"],",")!="") echo $composition[$composition_id]; ?>&nbsp;</p></td> 
								<td width="80"><p><? if(chop($prod_val["pi_number"],",")!="") echo $yarn_type[$yarn_type_id]; ?>&nbsp;</p></td>
								<td width="70" align="center"><p><? echo $unit_of_measurement[$prod_val["pi_uom"]]; ?>&nbsp;</p></td>
								<td width="100" align="right"><? echo number_format($prod_val["pi_quantity"],2);$total_pi_qnty+=$prod_val["pi_quantity"]; ?></td>
								<td width="70" align="right"><? if($prod_val["pi_amount"]>0 && $prod_val["pi_quantity"]>0) { $pi_rate=$prod_val["pi_amount"]/$prod_val["pi_quantity"]; echo  number_format($pi_rate,2);} else echo "0.00"; ?></td>
								<td width="100" align="right"><? echo number_format($prod_val["pi_amount"],2);$total_pi_amt+=$prod_val["pi_amount"]; ?></td>
								<td width="70" align="center"><p><? echo $currency[$prod_val["pi_currency_id"]]; ?> &nbsp;</p></td>
								
								<!--830 lc details 10 -->
								<td width="70" align="center"><? if($prod_val["lc_date"]!="" &&  $prod_val["lc_date"]!="0000-00-00")echo change_date_format($prod_val["lc_date"]); ?></td>
								<td width="120"><p><? echo $prod_val["lc_number"]; ?> &nbsp;</p></td>
								<td width="120"><p><? echo $bank_arr[$prod_val["lc_issuing_bank_id"]]; ?> &nbsp;</p></td>
								<td width="80"><p><? echo $pay_term[$prod_val["lc_payterm_id"]]; ?> &nbsp;</p></td>
								<td width="50" align="right"><p><? echo $prod_val["lc_tenor"]; ?> &nbsp;</p></td>
								<td width="80" align="right"><? echo number_format($prod_val["lc_value"],2);$total_lc_amt+=$prod_val["lc_value"]; ?></td>
								<td width="70" align="center"><? if($prod_val["lc_last_shipment_date"]!="" &&  $prod_val["lc_last_shipment_date"]!="0000-00-00")echo change_date_format($prod_val["lc_last_shipment_date"]); ?></td>
								<td width="80" align="center"><? if($prod_val["lc_expiry_date"]!="" &&  $prod_val["lc_expiry_date"]!="0000-00-00")echo change_date_format($prod_val["lc_expiry_date"]); ?></td>
								<td width="80" align="center"><? if($prod_val["lc_etd_date"]!="" &&  $prod_val["lc_etd_date"]!="0000-00-00")echo change_date_format($prod_val["lc_etd_date"]); ?></td>
								<td width="80">&nbsp;</td>
								
								
								
								
								<!--1340 Invoice details 15 -->
								<td width="80" align="center"><? if($prod_val["invoice_eta_date"]!="" &&  $prod_val["invoice_eta_date"]!="0000-00-00")echo change_date_format($prod_val["invoice_eta_date"]); ?></td>
								<td width="80">&nbsp;</td>
								<td width="150"><p><? echo chop($prod_val["invoice_no"],","); ?> &nbsp;</p></td>
								<td width="70" align="center"><? if($prod_val["invoice_date"]!="" &&  $prod_val["invoice_date"]!="0000-00-00")echo change_date_format($prod_val["invoice_date"]); ?></td>
								<td width="80"><p><? echo $prod_val["invoice_inco_term"]; ?> &nbsp;</p></td>
								<td width="100"><p><? echo $prod_val["invoice_inco_term_place"]; ?> &nbsp;</p></td>
								<td width="80"><p><? echo $prod_val["invoice_bill_no"]; ?> &nbsp;</p></td>
								<td width="70" align="center"><? if($prod_val["invoice_bill_date"]!="" &&  $prod_val["invoice_bill_date"]!="0000-00-00")echo change_date_format($prod_val["invoice_bill_date"]); ?></td>
								<td width="100"><p><? echo $prod_val["invoice_mother_vessel"]; ?> &nbsp;</p></td>
								<td width="100"><p><? echo $prod_val["invoice_feeder_vessel"]; ?> &nbsp;</p></td>
								<td width="100"><p><? echo $prod_val["invoice_container_no"]; ?> &nbsp;</p></td>
								<td width="80" align="right"><? echo number_format($prod_val["invoice_pkg_quantity"],2);$total_pkg_qnty+=$prod_val["invoice_pkg_quantity"]; ?></td>
								<td width="100" align="center"><? if($prod_val["invoice_doc_to_cnf"]!="" &&  $prod_val["invoice_doc_to_cnf"]!="0000-00-00")echo change_date_format($prod_val["invoice_doc_to_cnf"]); ?></td>
								<td width="70" align="center"></td>
								<td width="80"><p><? echo $prod_val["invoice_bill_of_entry_no"]; ?> &nbsp;</p></td>
								
								<!--290 Payment details 4 -->
								<td width="70" align="center"><? if($prod_val["invoice_maturity_date"]!="" &&  $prod_val["invoice_maturity_date"]!="0000-00-00")echo change_date_format($prod_val["invoice_maturity_date"]); ?></td>
								<td width="70" align="center"><? if($prod_val["invoice_maturity_date"]!="" &&  $prod_val["invoice_maturity_date"]!="0000-00-00")echo change_date_format($prod_val["invoice_maturity_date"]); ?></td>
								<?
								$all_inv_id=array_unique(explode(",",chop($prod_val["invoice_id"],",")));
								$pay_amt=0;
								foreach($all_inv_id as $inv_id)
								{
									$pay_date=$payment_data_arr[$inv_id]["payment_date"];
									$pay_amt+=$payment_data_arr[$inv_id]["accepted_ammount"];
								}
								?>
								<td width="70" align="center"><? if($pay_date!="" &&  $pay_date!="0000-00-00")echo change_date_format($pay_date); ?></td>
								<td width="80" align="right"><? echo number_format($pay_amt,2);$total_pay_amt+=$pay_amt; ?></td>
								
								<!--240 MRR details 3 -->
								<?
								$receive_date_array=array();
								$rcv_qnty=$rcv_value='';
								$all_req_dtls_id_arr=array_unique(explode(",",chop($prod_val["req_dtsl_id"],",")));
								$pi_id_all=array_unique(explode(",",chop($prod_val["pi_id"],",")));
								$wo_mst_id_all=array_unique(explode(",",chop($prod_val["wo_mst_id"],",")));
								
								$recv_pi_wo_req='';
								foreach($pi_id_all as $val)
								{
									//echo $val.'nn'.$color_id.'='.$count_id.'='.$composition_id.'='.$yarn_type_id;
									$rcv_qnty+=$recv_data_array[1][$val][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["rcv_qnty"];
									$rcv_value+=$recv_data_array[1][$val][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["rcv_amt"];
									$mrr_prod_id=chop($recv_data_array[1][$val][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["prod_id"],",");
									$recv_pi_wo_req.=$val.",";

									$receive_date=$recv_data_array[1][$val][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["receive_date"];
									$receive_date_array=explode(',',rtrim($receive_date,','));
									asort($receive_date_array);
									$first_receive_date = current($receive_date_array);
									$last_receive_date = end($receive_date_array);
									
								}
								$recv_pi_wo_req=chop($recv_pi_wo_req,",");
								if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**1";
								if($rcv_qnty=="")
								{
									$recv_pi_wo_req="";
									foreach($wo_mst_id_all as $val)
									{
										$rcv_qnty+=$recv_data_array[2][$val][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["rcv_qnty"];
										$rcv_value+=$recv_data_array[2][$val][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["rcv_amt"];
										$mrr_prod_id=chop($recv_data_array[2][$val][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["prod_id"],",");
										$recv_pi_wo_req.=$val.",";

										$receive_date=$recv_data_array[2][$val][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["receive_date"];
										$receive_date_array=explode(',',rtrim($receive_date,','));
										asort($receive_date_array);
										$first_receive_date = current($receive_date_array);
										$last_receive_date = end($receive_date_array);
									}
									$recv_pi_wo_req=chop($recv_pi_wo_req," , ");
									if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**2";
								}
								if($rcv_qnty=="")
								{
									$recv_pi_wo_req="";
									$rcv_qnty=$recv_data_array[7][$prod_val["requ_req_id"]][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["rcv_qnty"]["rcv_qnty"];
									$rcv_value=$recv_data_array[7][$prod_val["requ_req_id"]][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["rcv_qnty"]["rcv_amt"];
									$mrr_prod_id=chop($recv_data_array[7][$prod_val["requ_req_id"]][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["rcv_qnty"]["prod_id"],",");

									$recv_pi_wo_req=$req_data_array[$req_dtls_id]["req_id"];
									if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**7";

									$receive_date=$recv_data_array[7][$prod_val["requ_req_id"]][$color_id."**".$count_id."**".$composition_id."**".$yarn_type_id]["receive_date"];
									$receive_date_array=explode(',',rtrim($receive_date,','));
									asort($receive_date_array);
									$first_receive_date = current($receive_date_array);
									$last_receive_date = end($receive_date_array);
								}
								
								?> 

								<td width="80" align="right" title="<? echo "color=".$color_id." count=".$count_id." composition=".$composition_id." type=".$yarn_type_id;  ?>"><a href="##" onClick="openmypage_popup('<? echo $recv_pi_wo_req; ?>','<? echo $mrr_prod_id; ?>','Receive Info','receive_popup');" > <? echo number_format($rcv_qnty,2); $total_mrr_qnty+=$rcv_qnty; ?> </a></td>
								<td width="80" align="right"><? echo number_format($rcv_value,2); $total_mrr_amt+=$rcv_value; ?></td>								
								<td width="80" align="right" title="Wo Value-Receive Value">
								<?
								if($prod_val["wo_amount"]) $short_value=$prod_val["wo_amount"]-$rcv_value; else $short_value=$prod_val["pi_amount"]-$rcv_value;
								echo number_format($short_value,2);  $total_short_amt+=$short_value; 
								?></td>
								<td width="80" align="center"><? echo change_date_format($first_receive_date); ?></td>
								<td width="80" align="center"><? echo change_date_format($last_receive_date); ?></td>
							</tr>
							<?
							$i++;
						}
						$p++;
						
						?>
						
						<tr bgcolor="#CCCCCC">
							<!--990 requisition details 12 -->    
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td> 
							<td></td>
							<td align="right">Category Tot:</td>
							<td align="right"><? echo number_format($total_req_qnty,2);
								$category_summary[$category_id]['total_req_qnty']=$total_req_qnty; ?> </td>
							<td></td>
							<td align="right"><? echo number_format($total_req_amount,2);
								$category_summary[$category_id]['total_req_amount']=$total_req_amount;  ?> </td>
							<td></td>
							
							
							<!--1150 wo details 12 -->
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td> 
							<td></td>
							<td></td>
							<td align="right"><? echo number_format($total_wo_qnty,2); 
								$category_summary[$category_id]['total_wo_qnty']=$total_wo_qnty;?></td>
							<td></td>
							<td align="right"><? echo number_format($total_wo_amt,2); 
								$category_summary[$category_id]['total_wo_amt']=$total_wo_amt;?></td>
							<td></td>
							
							
							<!--1050 pi details 12 -->
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td> 
							<td></td>
							<td></td>
							<td align="right"><? echo number_format($total_pi_qnty,2); 
								$category_summary[$category_id]['total_pi_qnty']=$total_pi_qnty;?></td>
							<td></td>
							<td align="right"><? echo number_format($total_pi_amt,2); 
								$category_summary[$category_id]['total_pi_amt']=$total_pi_amt;?></td>
							<td></td>
							
							<!--830 lc details 10 -->
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><? echo number_format($total_lc_amt,2); ?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							
							<!--1340 Invoice details 15 -->
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><? echo number_format($total_pkg_qnty,2); ?></td>
							<td></td>
							<td></td>
							<td></td>
							
							<!--290 Payment details 4 -->
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><? echo number_format($total_pay_amt,2); ?></td>
							
							<!--240 MRR details 3 -->
							<td align="right"><? echo number_format($total_mrr_qnty,2); ?></td>
							<td align="right"><? echo number_format($total_mrr_amt,2); ?></td>
							<td align="right"><? echo number_format($total_short_amt,2); ?></td>
							<td></td>
							<td></td>							
						</tr>
						</table>
						</div>
						<?
						$total_req_qnty=$total_req_amount=$total_wo_qnty=$total_wo_amt=$total_wo_balanc=$total_pi_qnty=$total_pi_amt=$total_lc_amt=$total_pkg_qnty=$total_pay_amt=$total_mrr_qnty=$total_mrr_amt=$total_short_amt=$total_pipe_line=0;
					}
					else
					{
						if($q==1)
						{
							?>
							<table width="6170" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
								<thead>
									<tr>
										<th colspan="15">Requisiton Details</th>
										<th colspan="12">Work Order Details</th>
										<th colspan="11">PI Details</th>
										<th colspan="7">L/C Details</th>
										<th colspan="13">Invoice Details</th>
										<th colspan="4">Payment Details</th>
										<th colspan="8">Store details</th>
									</tr>
									<tr>
										<!--1260 requisition details-->    
										<th width="30">SL</th>
										<th width="50">Req. No</th>
										<th width="50">CS Number</th>
										<th width="50">Apprv. Status</th>
										<th width="70">Req. Date</th>
										<th width="120">Req. Appr. Date</th>
										<th width="80">Req. By</th>
										<th width="150">Store Name</th>
										<th width="70">Delivery Date</th>
										<th width="100">Item Group</th> 
										<th width="80">Item Code</th>
										<th width="150">Item Description</th>
										<th width="100">Required For</th>
										<th width="70"> UOM</th>
										<th width="100">Req. Quantity </th>
										<th width="80">Req. Amount </th>
										
										<!--1110 wo details-->
										<th width="50">WO No</th>
										<th width="100">Item Group</th>
										<th width="80">Item Code</th>
										<th width="150">Item Description</th>
										<th width="80">WO Qnty</th>
										<th width="70">UOM</th>
										<th width="80">Wo Rate</th>
										<th width="80">WO Amount</th>
										<th width="70">Currency</th>
										<th width="70">WO Date</th>
										<th width="80">WO Balance</th>									
										<th width="150">Supplier</th>										
										
										<!--840 pi details-->
										<th width="130">PI No</th>
										<th width="70">PI Date</th>
										<th width="150">Supplier</th>
										<th width="100">Item Group</th>
										<th width="150">Item Description</th>
										<th width="70">UOM</th>
										<th width="80">PI Quantity</th>
										<th width="80">Unit Price</th>
										<th width="80">PI Value</th>
										<th width="70">Currency</th>
										<th width="100">Indentor Name</th>
										
										<!--550 lc details-->
										<th width="70">LC Date</th>
										<th width="120">LC No</th>
										<th width="80">Pay Term</th>
										<th width="50">Tenor</th>
										<th width="80">LC Amount</th>
										<th width="70">Shipment Date</th>
										<th width="80">Expiry Date</th>
										
										<!--1100 Invoice details-->
										<th width="150">Invoice No</th>
										<th width="70">Invoice Date</th>
										<th width="80">Incoterm</th>
										<th width="100">Incoterm Place</th>
										<th width="80">B/L No</th>
										<th width="70">BL Date</th>
										<th width="100">Mother Vassel</th>
										<th width="100">Feedar Vassel</th>
										<th width="100">Continer No</th>
										<th width="80">Pkg Qty</th>
										<th width="100">Doc Send to CNF</th>
										<th width="70">NN Doc Received Date</th>
										<th width="80">Bill Of Entry No</th>
										
										<!--290 Payment details-->
										<th width="70">Maturity Date</th>
										<th width="70">Maturity Month</th>
										<th width="70">Payment Date</th>
										<th width="80">Paid Amount</th>
										
										<!--340 MRR details-->
										<th width="80">MRR Qnty</th>
										<th width="80">MRR Value</th>
										<th width="80">Challan NO</th>
										<th width="80">Short Value</th>
										<th>Pipeline</th>
										<th width="80">1st Rcv Date</th>
										<th width="80">last Rcv Date</th>
									</tr>
								</thead>
							</table>
							<div style="width:6188px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
							<table width="6170" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
							
							<tbody>
							<?
						}
						?>
						<tr>
							<th colspan="70" style="text-align: left !important; color: black" bgcolor="#FFFFCC"><? echo  $item_category[$category_id]; ?> :</th>
						</tr>
						<?
						// $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=39 and is_deleted=0 and status_active=1");
						$print_report_format=return_field_value('format_id', 'lib_report_template', "template_name =".$cbo_company_name." and module_id=6 and report_id=39 and is_deleted=0 and status_active=1");

						$format_ids=explode(',', $print_report_format);

						$variable='';
						foreach($category_val as $prod_data_ref=>$prod_val)
						{
							$prod_data_ref=explode("__",$prod_data_ref);
							$product_id=$prod_data_ref[1];
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";

							$req_id = $prod_val['requ_req_id'];
							$location_id = $prod_val['location_id'];
							$isApproved = $prod_val['is_approved'];

							switch ($format_ids[0]) {
								case 118:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(1, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 119:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(2, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 120:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(3, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 121:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(4, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 122:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(5, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 123:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(6, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 129:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(7, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 169:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(8, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 165:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(9, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 227:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(10, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 241:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(11, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 580:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(12, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 28:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(13, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 280:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(14, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 688:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(15, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								case 243:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(16, $req_id, $location_id)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
								
								default:
									$variable="<a href='#' title='".$format_ids[0]."' onClick=\"generate_report_purchase(16, $req_id, 243, $location_id, $isApproved)\"> ".$prod_val["requ_prefix_num"]." <a/>";
									break;
							}


							$order_data = chop($prod_val["wo_number_prefix_num"],",");
							$order_id = chop($prod_val["wo_mst_id"],",");

							$order_pre_num_arr = explode(",",$order_data);
							$order_id_arr = explode(",",$order_id);

							$dtls_data = '';
							$orders_data = '';

							$k = 0;
							foreach($order_pre_num_arr as $order_pre_num)
							{
								$order_id = $order_id_arr[$k];
								if($orders_data!='')
								{
									$orders_data .=", ";
								}

								if($order_data_arr[$order_id]!='')
								{
									if($order_data_arr[$order_id]['entry_form']==146)
									{
										if($order_print_arr[61]==66)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['wo_number']."*"."*".$order_data_arr[$order_id]['supplier_id']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['currency_id']."*".$order_data_arr[$order_id]['wo_basis_id']."*".$order_data_arr[$order_id]['pay_mode']."*".$order_data_arr[$order_id]['source']."*".$order_data_arr[$order_id]['delivery_date']."*".$order_data_arr[$order_id]['attention']."*"."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."*".$order_data_arr[$order_id]['delivery_place']."*".$order_data_arr[$order_id]['update_id']."*"."Purchase Order"."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*".$order_data_arr[$order_id]['txt_contact']."*1'";

										}
										else if($order_print_arr[61]==72)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*"."WORK ORDER"."*"."1"."*"."1'";

										}
										else if($order_print_arr[61]==85)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['wo_number']."*"."*".$order_data_arr[$order_id]['supplier_id']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['currency_id']."*".$order_data_arr[$order_id]['wo_basis_id']."*".$order_data_arr[$order_id]['pay_mode']."*".$order_data_arr[$order_id]['source']."*".$order_data_arr[$order_id]['delivery_date']."*".$order_data_arr[$order_id]['attention']."*"."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."*".$order_data_arr[$order_id]['delivery_place']."*".$order_data_arr[$order_id]['update_id']."*"."PURCHASE ORDER"."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*".$order_data_arr[$order_id]['hidden_delivery_info_dtls']."*1'";

										}
										else if($order_print_arr[61]==129)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['wo_number']."*"."*".$order_data_arr[$order_id]['supplier_id']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['currency_id']."*".$order_data_arr[$order_id]['wo_basis_id']."*".$order_data_arr[$order_id]['pay_mode']."*".$order_data_arr[$order_id]['source']."*".$order_data_arr[$order_id]['delivery_date']."*".$order_data_arr[$order_id]['attention']."*".""."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*".""."*".$order_data_arr[$order_id]['delivery_place']."*".$order_data_arr[$order_id]['update_id']."*"."PURCHASE ORDER"."*".$order_data_arr[$order_id]['cbo_location']."*".$order_data_arr[$order_id]['cbo_payterm_id']."*".$order_data_arr[$order_id]['txt_remarks_mst']."*".$order_data_arr[$order_id]['txt_contact']."*".$order_data_arr[$order_id]['txt_tenor']."*"."1"."*".$order_data_arr[$order_id]['cbo_inco_term']."*".$order_data_arr[$order_id]['cbo_wo_type']."*".$order_data_arr[$order_id]['txt_reference']."*1'";

										}
										else if($order_print_arr[61]==134)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['wo_number']."*"."*".$order_data_arr[$order_id]['supplier_id']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['currency_id']."*".$order_data_arr[$order_id]['wo_basis_id']."*".$order_data_arr[$order_id]['pay_mode']."*".$order_data_arr[$order_id]['source']."*".$order_data_arr[$order_id]['delivery_date']."*".$order_data_arr[$order_id]['attention']."*"."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."*".$order_data_arr[$order_id]['delivery_place']."*".$order_data_arr[$order_id]['update_id']."*"."PURCHASE ORDER"."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*".$order_data_arr[$order_id]['hidden_delivery_info_dtls']."*1'";

										}
										else if($order_print_arr[61]==137)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['wo_number']."*"."*".$order_data_arr[$order_id]['supplier_id']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['currency_id']."*".$order_data_arr[$order_id]['wo_basis_id']."*".$order_data_arr[$order_id]['pay_mode']."*".$order_data_arr[$order_id]['source']."*".$order_data_arr[$order_id]['delivery_date']."*".$order_data_arr[$order_id]['attention']."*"."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."*".$order_data_arr[$order_id]['delivery_place']."*".$order_data_arr[$order_id]['update_id']."*"."PURCHASE ORDER"."*".$order_data_arr[$order_id]['cbo_location']."*".$order_data_arr[$order_id]['cbo_payterm_id']."*".$order_data_arr[$order_id]['txt_remarks_mst']."*".$order_data_arr[$order_id]['txt_contact']."*".$order_data_arr[$order_id]['txt_tenor']."*"."1"."*"."1'";

										}
										else if($order_print_arr[61]==430)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*"."Stationary Purchase Order"."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."1"."*"."1'";

										}
										else if($order_print_arr[61]==732)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['wo_number']."*"."*".$order_data_arr[$order_id]['supplier_id']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['currency_id']."*".$order_data_arr[$order_id]['wo_basis_id']."*".$order_data_arr[$order_id]['pay_mode']."*".$order_data_arr[$order_id]['source']."*".$order_data_arr[$order_id]['delivery_date']."*".$order_data_arr[$order_id]['attention']."*"."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."*".$order_data_arr[$order_id]['delivery_place']."*".$order_data_arr[$order_id]['update_id']."*"."Purchase Order- General Purchase"."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*"."1"."*"."1'";
										}

										if($order_print_arr[61]!="")
										{
											$orders_data .="<a href='#' onClick=\"print_report($dtls_data,'".$report_format_arr[$order_data_arr[$order_id]['entry_form']][$order_print_arr[61]]."','../work_order/requires/stationary_work_order_controller')\"> ".$order_pre_num." <a/>";
										}
										else
										{
											$orders_data .=$order_pre_num;
										}

									}
									else if($order_data_arr[$order_id]['entry_form']==145)
									{

										$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."Dyes And Chemical Purchase Order"."*1'";

										if($order_print_arr[132]!="")
										{
											$orders_data .="<a href='#' onClick=\"print_report($dtls_data,'".$report_format_arr[$order_data_arr[$order_id]['entry_form']][$order_print_arr[132]]."','../work_order/requires/dyes_and_chemical_work_order_controller')\"> ".$order_pre_num." <a/>";
										}
										else
										{
											$orders_data .=$order_pre_num;
										}

									}
									else if($order_data_arr[$order_id]['entry_form']==147)
									{
										
										if($order_print_arr[30]==84)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==85)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['wo_number']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==129)
										{

			 								$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['cbo_location']."*"."6"."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==134)
										{

			 								$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['wo_number']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==137)
										{

			 								$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['cbo_location']."*"."5"."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==191)
										{

			 								$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['cbo_location']."*"."7"."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==227)
										{

			 								$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['cbo_location']."*"."8"."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==235)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['wo_number']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==241)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['wo_number']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==274)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['wo_number']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==354)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==427)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['wo_number']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==430)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*"."Others Purchase Order"."*"."1"."*"."1'";

										}
										else if($order_print_arr[30]==732)
										{

											$dtls_data = "'".$order_data_arr[$order_id]['cbo_company_name']."*".$order_data_arr[$order_id]['update_id']."*".$order_data_arr[$order_id]['txt_req_numbers_id']."*"."Others Purchase Order"."*".$order_data_arr[$order_id]['wo_number']."*".$order_data_arr[$order_id]['wo_date']."*".$order_data_arr[$order_id]['cbo_location']."*"."1"."*"."1"."*"."1'";

										}

										if($order_print_arr[30]!="")
										{
											$orders_data .="<a href='#' onClick=\"print_report($dtls_data,'".$report_format_arr[$order_data_arr[$order_id]['entry_form']][$order_print_arr[30]]."','../work_order/requires/spare_parts_work_order_controller')\"> ".$order_pre_num." <a/>";
										}
										else
										{
											$orders_data .=$order_pre_num;
										}

									}
									else
									{
										$orders_data .=$order_pre_num;
									}

								}
								else
								{
									$orders_data .=$order_pre_num;
								}
								$k++; 
							}
							// $i."_".$prod_val['requ_req_id']."_".$prod_val['prod_id']."____";
							 $sys_data= $cs_data_array[$prod_val['requ_req_id']][$prod_val['prod_id']]["sys_number"];
							$sys_id= $cs_data_array[$prod_val['requ_req_id']][$prod_val['prod_id']]["id"];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<!--1210 requisition details-->    
								<td  width="30" align="center"><? echo $i; ?></td>
								<td  width="50" align="center">
									<p>
										<a href='##' style='color:#000'>
											<?php echo $variable; ?>
										</a>
									</p>
								</td>
								<!-- <td align="center"><p><? // echo $prod_val["requ_prefix_num"]; ?>&nbsp;</p></td> -->
								<td width="50" align="center">
									<p>
										<a href="##" onClick="generate_report_comparative_statement('<?=$sys_id;?>','<?=$sys_data;?>')">
										<?php echo $sys_data; ?>
										</a> 
									</p>
								</td>
								<td  width="50" align="center"><p><?php echo $is_approved[$prod_val['is_approved']]; ?></p></td>
								<td  width="70" align="center"><? if($prod_val["requ_requisition_date"]!="" &&  $prod_val["requ_requisition_date"]!="0000-00-00")echo change_date_format($prod_val["requ_requisition_date"]); ?></td>
								<td  width="120" align="center"><p><? echo $prod_val["approved_date"]; ?>&nbsp;</p></td>
								<td  width="80"><p><? echo $prod_val["req_by"]; ?>&nbsp;</p></td>
								<td  width="150"><p><? echo $store_array[$prod_val["requ_store_name"]]; ?>&nbsp;</p></td>
								<td  width="70" align="center"><? if($prod_val["requ_delivery_date"]!="" &&  $prod_val["requ_delivery_date"]!="0000-00-00")echo change_date_format($prod_val["requ_delivery_date"]); ?></td>
								<td  width="100"><p><? if($prod_val["requ_prefix_num"]) echo $item_group_array[$prod_data_array[$product_id]["item_group_id"]]; ?>&nbsp;</p></td> 
								<td  width="80" align="center"><p><? if($prod_val["requ_prefix_num"]) echo $prod_data_array[$product_id]["item_code"]; ?>&nbsp;</p></td>
								<td  width="150"><p><? if($prod_val["requ_prefix_num"]) echo $prod_data_array[$product_id]["item_description"]; ?>&nbsp;</p></td>
								<td  width="100"><p><? echo $use_for[$prod_val["requ_required_for"]]; ?>&nbsp;</p></td>
								<td  width="70" align="center"><p><? echo $unit_of_measurement[$prod_data_array[$product_id]["unit_of_measure"]]; ?>&nbsp;</p></td>
								<td  width="100" align="right"><? echo  number_format($prod_val["requ_quantity"],2);$total_req_qnty+=$prod_val["requ_quantity"]; ?></td>
								<td  width="80" align="right"><? echo  number_format($prod_val["requ_amount"],2);$total_req_amount+=$prod_val["requ_amount"]; ?></td>
								
								<!--1110 wo details-->
								<td width="50" align="center" title="<? echo $product_id.jahid; ?>"><p><? echo $orders_data; ?>&nbsp;</p></td>
								<!-- if(chop($prod_val["wo_number_prefix_num"],",")!="") -->
								<td width="100"><p><? echo $item_group_array[$prod_data_array[$product_id]["item_group_id"]]; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><?  echo $prod_data_array[$product_id]["item_code"]; ?>&nbsp;</p></td>
								<td width="150"><p><? echo $prod_data_array[$product_id]["item_description"]; ?>&nbsp;</p></td>
								<td width="80" align="right"><? echo  number_format($prod_val["wo_supplier_order_quantity"],2);$total_wo_qnty+=$prod_val["wo_supplier_order_quantity"]; ?></td>
								<td width="70"><p><? echo $unit_of_measurement[$prod_val["wo_uom"]];?>&nbsp;</p></td>
								<td width="80" align="right"><? if($prod_val["wo_amount"]>0 && $prod_val["wo_supplier_order_quantity"]>0) { $wo_rate=$prod_val["wo_amount"]/$prod_val["wo_supplier_order_quantity"]; echo  number_format($wo_rate,2);} else echo "0.00"; ?></td>
								<td width="80" align="right"><? echo number_format($prod_val["wo_amount"],2);$total_wo_amt+=$prod_val["wo_amount"]; ?></td>
								<td width="70"><p><? echo $currency[$prod_val["wo_currency_id"]];;?>&nbsp;</p></td>
								<td width="70" align="center"><p><? if($prod_val["wo_date"]!="" &&  $prod_val["wo_date"]!="0000-00-00")echo change_date_format($prod_val["wo_date"]); ?></p></td>
								<td width="80"><? $wo_balance=$prod_val["requ_quantity"]-$prod_val["wo_supplier_order_quantity"]; echo number_format($wo_balance,2); $total_wo_balance+=$wo_balance; ?></td>								
								<td width="150"><p><? echo $suplier_array[$prod_val["wo_supplier_id"]];?>&nbsp;</p></td>
																
								<!--840 pi details-->
								<td  width="130" style="word-break:break-all;"><p><? echo chop($prod_val["pi_number"],","); ?> &nbsp;</p></td>
								<td  width="70" align="center"><? if($prod_val["pi_date"]!="" &&  $prod_val["pi_date"]!="0000-00-00")echo change_date_format($prod_val["pi_date"]); ?></td>
								<td  width="150"><p><? echo $suplier_array[$prod_val["pi_supplier_id"]];?>&nbsp;</p></td>
								<td  width="100"><p><? if(chop($prod_val["pi_number"],",")!="") echo $item_group_array[$prod_data_array[$product_id]["item_group_id"]]; ?>&nbsp;</p></td>
								<td  width="150"><p><? if(chop($prod_val["pi_number"],",")!="") echo $prod_data_array[$product_id]["item_description"]; ?>&nbsp;</p></td>
								<td  width="70" align="center"><p><? if(chop($prod_val["pi_number"],",")!="") echo $unit_of_measurement[$prod_data_array[$product_id]["unit_of_measure"]]; ?>&nbsp;</p></td>
								<td  width="80" align="right"><? echo number_format($prod_val["pi_quantity"],2);$total_pi_qnty+=$prod_val["pi_quantity"]; ?></td>
								<td  width="80" align="right"><? if($prod_val["pi_amount"]>0 && $prod_val["pi_quantity"]>0) { $pi_rate=$prod_val["pi_amount"]/$prod_val["pi_quantity"]; echo  number_format($pi_rate,2);} else echo "0.00"; ?></td>
								<td  width="80" align="right"><? echo number_format($prod_val["pi_amount"],2);$total_pi_amt+=$prod_val["pi_amount"]; ?></td>
								<td  width="70" align="center"><p><? echo $currency[$prod_val["pi_currency_id"]]; ?> &nbsp;</p></td>
								<td  width="100"><p><? echo $indentor_name_array[$prod_val["pi_intendor_name"]]; ?> &nbsp;</p></td>
								
								<!--550 lc details-->
								<td width="70" align="center"><? if($prod_val["lc_date"]!="" &&  $prod_val["lc_date"]!="0000-00-00")echo change_date_format($prod_val["lc_date"]); ?></td>
								<td width="120"><p><? echo $prod_val["lc_number"]; ?> &nbsp;</p></td>
								<td width="80"><p><? echo $pay_term[$prod_val["lc_payterm_id"]]; ?> &nbsp;</p></td>
								<td width="50" align="right"><p><? echo $prod_val["lc_tenor"]; ?> &nbsp;</p></td>
								<td  width="80" align="right"><? echo number_format($prod_val["lc_value"],2);$total_lc_amt+=$prod_val["lc_value"]; ?></td>
								<td width="70" align="center"><? if($prod_val["lc_last_shipment_date"]!="" &&  $prod_val["lc_last_shipment_date"]!="0000-00-00")echo change_date_format($prod_val["lc_last_shipment_date"]); ?></td>
								<td width="80" align="center"><? if($prod_val["lc_expiry_date"]!="" &&  $prod_val["lc_expiry_date"]!="0000-00-00")echo change_date_format($prod_val["lc_expiry_date"]); ?></td>
								
								<!--1100 Invoice details-->
								<td width="150" style="word-break:break-all;"><p><? echo chop($prod_val["invoice_no"],","); ?> &nbsp;</p></td>
								<td width="70" align="center"><? if($prod_val["invoice_date"]!="" &&  $prod_val["invoice_date"]!="0000-00-00")echo change_date_format($prod_val["invoice_date"]); ?></td>
								<td width="80"><p><? echo $prod_val["invoice_inco_term"]; ?> &nbsp;</p></td>
								<td width="100"><p><? echo $prod_val["invoice_inco_term_place"]; ?> &nbsp;</p></td>
								<td width="80"><p><? echo $prod_val["invoice_bill_no"]; ?> &nbsp;</p></td>
								<td width="70" align="center"><? if($prod_val["invoice_bill_date"]!="" &&  $prod_val["invoice_bill_date"]!="0000-00-00")echo change_date_format($prod_val["invoice_bill_date"]); ?></td>
								<td width="100"><p><? echo $prod_val["invoice_mother_vessel"]; ?> &nbsp;</p></td>
								<td width="100"><p><? echo $prod_val["invoice_feeder_vessel"]; ?> &nbsp;</p></td>
								<td width="100"><p><? echo $prod_val["invoice_container_no"]; ?> &nbsp;</p></td>
								<td width="80" align="right"><p><? echo number_format($prod_val["invoice_pkg_quantity"],2);$total_pkg_qnty+=$prod_val["invoice_pkg_quantity"]; ?></p></td>
								<td width="100" align="center"><p><? if($prod_val["invoice_doc_to_cnf"]!="" &&  $prod_val["invoice_doc_to_cnf"]!="0000-00-00")echo change_date_format($prod_val["invoice_doc_to_cnf"]); ?></p></td>
								<td width="70" align="center"></td>
								<td width="80"><p><? echo $prod_val["invoice_bill_of_entry_no"]; ?> &nbsp;</p></td>
								
								<!--290 Payment details-->
								<td width="70" align="center"><p><? if($prod_val["invoice_maturity_date"]!="" &&  $prod_val["invoice_maturity_date"]!="0000-00-00")echo change_date_format($prod_val["invoice_maturity_date"]); ?></p></td>
								<td width="70" align="center"><p><? if($prod_val["invoice_maturity_date"]!="" &&  $prod_val["invoice_maturity_date"]!="0000-00-00")echo change_date_format($prod_val["invoice_maturity_date"]); ?></p></td>
								<?
								$all_inv_id=array_unique(explode(",",chop($prod_val["invoice_id"],",")));
								$pay_amt=0;
								foreach($all_inv_id as $inv_id)
								{
									$pay_date=$payment_data_arr[$inv_id]["payment_date"];
									$pay_amt+=$payment_data_arr[$inv_id]["accepted_ammount"];
								}
								?>
								<td width="70" align="center"><p><? if($pay_date!="" &&  $pay_date!="0000-00-00")echo change_date_format($pay_date); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($pay_amt,2);$total_pay_amt+=$pay_amt; ?></p></td>
								
								<!--340 MRR details-->
								<?
								$rcv_qnty=$rcv_value='';
								$all_req_dtls_id_arr=array_unique(explode(",",chop($prod_val["req_dtsl_id"],",")));
								$pi_id_all=array_unique(explode(",",chop($prod_val["pi_id"],",")));
								$wo_mst_id_all=array_unique(explode(",",chop($prod_val["wo_mst_id"],",")));
								
								$receive_date_array=array();
								$recv_pi_wo_req="";$rcv_qnty="";
								foreach($pi_id_all as $val)
								{
									$rcv_qnty+=$recv_data_array[1][$val][$product_id]["rcv_qnty"];
									$rcv_value+=$recv_data_array[1][$val][$product_id]["rcv_amt"];
									$recv_pi_wo_req.=$val.",";

									$receive_date=$recv_data_array[1][$val][$product_id]["receive_date"];
									$challan_no=$recv_data_array[1][$val][$product_id]["challan_no"];
									$receive_date_array=explode(',',rtrim($receive_date,','));
									asort($receive_date_array);
									$first_receive_date = current($receive_date_array);
									$last_receive_date = end($receive_date_array);
								}
								$recv_pi_wo_req=chop($recv_pi_wo_req,",");
								if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**1";
								if($rcv_qnty=="")
								{
									$recv_pi_wo_req="";
									foreach($wo_mst_id_all as $val)
									{
										$rcv_qnty+=$recv_data_array[2][$val][$product_id]["rcv_qnty"];
										$rcv_value+=$recv_data_array[2][$val][$product_id]["rcv_amt"];
										$recv_pi_wo_req.=$val.",";
									}
									$recv_pi_wo_req=chop($recv_pi_wo_req," , ");
									if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**2";

									$receive_date=$recv_data_array[2][$val][$product_id]["receive_date"];
									$challan_no=$recv_data_array[2][$val][$product_id]["challan_no"];
									$receive_date_array=explode(',',rtrim($receive_date,','));
									asort($receive_date_array);
									$first_receive_date = current($receive_date_array);
									$last_receive_date = end($receive_date_array);
								}
								if($rcv_qnty=="")
								{
									$recv_pi_wo_req="";
									$rcv_qnty=$recv_data_array[7][$prod_val["requ_req_id"]][$product_id]["rcv_qnty"];
									$rcv_value=$recv_data_array[7][$prod_val["requ_req_id"]][$product_id]["rcv_amt"];
									$recv_pi_wo_req=$req_data_array[$req_dtls_id]["req_id"];
									if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**7";

									$receive_date=$recv_data_array[7][$prod_val["requ_req_id"]][$product_id]["receive_date"];
									$challan_no=$recv_data_array[7][$prod_val["requ_req_id"]][$product_id]["challan_no"];
									$receive_date_array=explode(',',rtrim($receive_date,','));
									asort($receive_date_array);
									$first_receive_date = current($receive_date_array);
									$last_receive_date = end($receive_date_array);
								}
								$pipe_pi_qnty="";$pipe_wo_qnty="";
								foreach($all_req_dtls_id_arr as $val)
								{
									$pipe_wo_qnty+=$wo_pipe_array[$val][$product_id];
									$pipe_pi_qnty+=$pi_pipe_array[$val][$product_id];
								}
								
								?>
								<td width="80" align="right"><p><a href="##" onClick="openmypage_popup('<? echo $recv_pi_wo_req; ?>','<? echo $product_id; ?>','Receive Info','receive_popup');" > <? echo number_format($rcv_qnty,2); $total_mrr_qnty+=$rcv_qnty; ?> </a></p></td>
								<td width="80" align="right"><p><? echo number_format($rcv_value,2); $total_mrr_amt+=$rcv_value; ?></p></td>
								<td width="80" align="right" style="word-break:break-all;"><p><? echo chop($challan_no,','); ?></p></td>
								<td width="80" align="right" title="Wo Value-Receive Value"><p><? $short_value=$prod_val["wo_amount"]-$rcv_value; echo number_format($short_value,2);  $total_short_amt+=$short_value; ?></p></td>
								<?
								
								$pipe_line=(($pipe_wo_qnty+$pipe_pi_qnty)-$rcv_qnty);
								?>
								<td align="right"><p><? echo number_format($pipe_line,2); $total_pipe_line+=$pipe_line;?></p></td>
								<td width="80" align="center"><p><? echo change_date_format($first_receive_date); ?></p></td>
								<td width="80" align="center"><p><? echo change_date_format($last_receive_date); ?></p></td>
							</tr>
							<?
							$i++;
						}
						$q++;
						
						?>
						<tr bgcolor="#CCCCCC">
							<!--1260 requisition details-->
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right">Category Tot:</td>
							<td align="right"><p><? echo number_format($total_req_qnty,2); 
								$category_summary[$category_id]['total_req_qnty']=$total_req_qnty;?> </p></td>
							
							<!--1110 wo details-->
							<td align="right"><p><? echo number_format($total_req_amount,2); 
								$category_summary[$category_id]['total_req_amount']=$total_req_amount;?></p> </td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><p><? echo number_format($total_wo_qnty,2); 
								$category_summary[$category_id]['total_wo_qnty']=$total_wo_qnty;?></p></td>
							<td></td>
							<td></td>
							<td align="right"><p><? echo number_format($total_wo_amt,2); 
								$category_summary[$category_id]['total_wo_amt']=$total_wo_amt;?></p></td>
							<td></td>
							<td></td>
							<td align="right"><p><? echo number_format($total_wo_balanc,2); ?></p></td>
							<td></td>							
							
							<!--840 pi details-->
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><p><? echo number_format($total_pi_qnty,2); 
								$category_summary[$category_id]['total_pi_qnty']=$total_pi_qnty;?></p></td>
							<td></td>
							<td align="right"><p><? echo number_format($total_pi_amt,2); 
								$category_summary[$category_id]['total_pi_amt']=$total_pi_amt;?></p></td>
							<td></td>
							<td></td>
							
							<!--550 lc details-->
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><p><? echo number_format($total_lc_amt,2); ?></p></td>
							<td></td>
							<td></td>
							
							<!--1100 Invoice details-->
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><p><? echo number_format($total_pkg_qnty,2); ?></p></td>
							<td ></td>
							<td></td>
							<td></td>
							
							<!--290 Payment details-->
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><p><? echo number_format($total_pay_amt,2); ?></p></td>
							
							<!--340 MRR details-->
							<td align="right"><p><? echo number_format($total_mrr_qnty,2); ?></p></td>
							<td align="right"><p><? echo number_format($total_mrr_amt,2); ?></p></td>
							<td align="right"><p></p></td>
							<td align="right"><p><? echo number_format($total_short_amt,2); ?></p></td>
							<td align="right"><p><? echo number_format($total_pipe_line,2); ?></p></td>
							<td></td>
							<td></td>
						</tr>
						
						<?
						$total_req_qnty=$total_req_amount=$total_wo_qnty=$total_wo_amt=$total_wo_balanc=$total_pi_qnty=$total_pi_amt=$total_lc_amt=$total_pkg_qnty=$total_pay_amt=$total_mrr_qnty=$total_mrr_amt=$total_short_amt=$total_pipe_line=0;
					}
				}
				?>
				</tbody>
				
					
			</table><br>
			</div>	
			<table width="760" cellpadding="0" cellspacing="0" class="rpt_table"  border="1" rules="all">
				<thead>
					<tr>
						<th>Sl</th>
						<th>Category</th>
						<th>Req. Qty</th>
						<th>Amount</th>
						<th>WO Qty</th>
						<th>Amount</th>
						<th>PI Qty</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
				<?
					$m=0;
					foreach($category_summary as $key=>$value){
						$m++;
						if ($m%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><?= $m; ?></td>
							<td><? echo  $item_category[$key]; ?></td>
							<td align="right"><? echo  number_format($value['total_req_qnty'],2); ?></td>
							<td align="right"><? echo  number_format($value['total_req_amount'],2); ?></td>
							<td align="right"><? echo  number_format($value['total_wo_qnty'],2); ?></td>
							<td align="right"><? echo  number_format($value['total_wo_amt'],2); ?></td>
							<td align="right"><? echo  number_format($value['total_pi_qnty'],2); ?></td>
							<td align="right"><? echo  number_format($value['total_pi_amt'],2); ?></td>
						</tr>
						<?
						$grand_total_req_qnty+=$value['total_req_qnty'];
						$grand_total_req_amount+=$value['total_req_amount'];
						$grand_total_wo_qnty+=$value['total_wo_qnty'];
						$grand_total_wo_amt+=$value['total_wo_amt'];
						$grand_total_pi_qnty+=$value['total_pi_qnty'];
						$grand_total_pi_amt+=$value['total_pi_amt'];
					}
				?>
				</tbody>
				<tfoot>
				<tr>
					<th colspan="2">Total &nbsp;</th>
					<th><? echo  number_format($grand_total_req_qnty,2); ?></th>
					<th><? echo  number_format($grand_total_req_amount,2); ?></th>
					<th><? echo  number_format($grand_total_wo_qnty,2); ?></th>
					<th><? echo  number_format($grand_total_wo_amt,2); ?></th>
					<th><? echo  number_format($grand_total_pi_qnty,2); ?></th>
					<th><? echo  number_format($grand_total_pi_amt,2); ?></th>
				</tr>
				</tfoot>
			</table>
		</fieldset>
	</div>
	<?
	
		
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
	echo "$total_data####$filename"; 
	exit();
}

if($action=="report_generate_trims")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	//$cbo_item_category_id=4;
	
	//echo "test";die;

	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	$txt_pi_no=trim(str_replace("'","",$txt_pi_no));
	$txt_pi_id=trim(str_replace("'","",$txt_pi_id));
    $txt_lc_no=trim(str_replace("'","",$txt_lc_no));
    $txt_lc_id=trim(str_replace("'","",$txt_lc_id));
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	

	$cbo_supplier=str_replace("'","",$cbo_supplier);
	if($cbo_supplier>0) $supplier_cond =" and a.supplier_id='$cbo_supplier' ";else $supplier_cond= "";
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_wo_po_no=trim(str_replace("'","",$txt_wo_po_no));

	$lib_buyer=return_library_array("select ID, BUYER_NAME FROM LIB_BUYER","ID","BUYER_NAME");
	//echo $cbo_date_type;

	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}
	
	$inv_id_array=array();
	if(($cbo_date_type==2 &&  ($txt_date_from && $txt_date_to)) ||  ($txt_wo_po_no!=""))
	{
		$sql_cond="";
		if($cbo_company_name) $sql_cond.=" and a.company_id='$cbo_company_name' ";
		if($cbo_item_category_id) $sql_cond.=" and a.item_category='$cbo_item_category_id' ";
		if($txt_wo_po_no !="") 
		{
			$sql_cond.=" and a.booking_no_prefix_num  in($txt_wo_po_no) ";
		}
		if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and a.booking_date between  '$txt_date_from' and '$txt_date_to'";
		
	     $user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
		
		$sql_wo="select a.id as wo_mst_id, a.booking_no as wo_number, a.buyer_id, a.booking_no_prefix_num as wo_number_prefix_num,a.inserted_by, a.booking_date as wo_date, a.supplier_id, b.id as wo_dtls_id, b.trim_group, c.description, b.uom, c.cons as qnty, c.rate, c.amount, a.currency_id
		from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c
		where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.booking_type=2 and b.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $supplier_cond";
		
		//echo $sql_wo; //die;
		
		$req_result=sql_select($sql_wo);

		$req_no_arr=$req_dtls_id_arr=$all_data_arr=array();
		foreach($req_result as $row)
		{
			$key=$row[csf("trim_group")]."__".strtoupper(trim($row[csf("description")]));
			
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_mst_id"]=$row[csf("wo_mst_id")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_number"]=$row[csf("wo_number")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_number_prefix_num"]=$row[csf("wo_number_prefix_num")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_date"]=$row[csf("wo_date")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_supplier_id"]=$row[csf("supplier_id")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_trim_group"]=$row[csf("trim_group")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_description"]=$row[csf("description")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_uom"]=$row[csf("uom")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_currency_id"]=$row[csf("currency_id")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_qnty"]+=$row[csf("qnty")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_amount"]+=$row[csf("amount")];
			$all_data_arr[$row[csf("wo_number")]][$key]["inserted_by"]=$row[csf("inserted_by")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_buyer_id"]=$row[csf("BUYER_ID")];
			
			$all_wo_id[$row[csf("wo_mst_id")]]=$row[csf("wo_mst_id")];
			if($wo_dtls_id_check[$row[csf("wo_dtls_id")]]=="")
			{
				$wo_dtls_id_check[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
				$all_wo_dtls_id[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
			}
		}
		
		
		//var_dump($all_data_arr);die;
		///############################## based on requistion start ##############################################/////////////
		if(!empty($all_wo_dtls_id))
		{

			//echo "sumon";die;
			$all_wo_dtls_id_array=array_chunk($all_wo_dtls_id,999, true);
			//print_r($pi_id_array);die;
			$all_wo_dtls_id_cond="";
			$si=0;
			foreach($all_wo_dtls_id_array as $key=> $value)
			{
				if($si==0)
				{
					$all_wo_dtls_id_cond=" and (b.work_order_dtls_id  in(".implode(",",$value).")"; 				
				}
				else
				{
					$all_wo_dtls_id_cond.=" or b.work_order_dtls_id  in(".implode(",",$value).")";				
				}
				
				$si++;
			}
			$all_wo_dtls_id_cond.=")";
			//echo $all_wo_dtls_id_cond;die;

			$sql_pi="SELECT a.id as pi_id, a.pi_number, a.pi_date, a.supplier_id, a.currency_id, b.id as pi_dtls_id, b.work_order_no, b.item_group, b.item_description, b.uom, b.quantity, b.amount, b.net_pi_amount
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 $all_wo_dtls_id_cond";

			$sql_pi_result=sql_select($sql_pi);

			$pi_req_arr=$pi_id_arr=array();
			foreach($sql_pi_result as $row)
			{
				$pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				$pi_req_arr[$row[csf("pi_dtls_id")]]=$req_no_arr[$row[csf("requisition_dtls_id")]];
				
				$key=$row[csf("item_group")]."__".strtoupper(trim($row[csf("item_description")]));
				
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_id"].=$row[csf("pi_id")].",";
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_number"].=$row[csf("pi_number")].",";
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_work_order_no"]=$row[csf("work_order_no")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_date"]=$row[csf("pi_date")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_item_group"]=$row[csf("item_group")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_item_description"]=$row[csf("item_description")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_uom"]=$row[csf("uom")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_quantity"]+=$row[csf("quantity")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_amount"]+=$row[csf("amount")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["net_pi_amount"]+=$row[csf("net_pi_amount")];
			} 
		}
		
		//var_dump($all_data_arr);die;
		
		if(!empty($pi_id_arr))
		{
			//echo "sumon";die;
			$pi_id_array=array_chunk($pi_id_arr,999, true);
			//print_r($pi_id_array);die;
			$pi_id_cond="";
			$di=0;
			foreach($pi_id_array as $key=> $value)
			{
				if($di==0)
				{
					$pi_id_cond=" and (c.id  in(".implode(",",$value).")"; 				
				}
				else
				{
					$pi_id_cond.=" or c.id  in(".implode(",",$value).")";				
				}
				
				$di++;
			}
			$pi_id_cond.=")";
			//echo $pi_id_cond;die;

			$sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.work_order_no, c.item_group, c.item_description, c.uom, c.quantity, c.amount, a.issuing_bank_id, a.etd_date 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_id_cond";
			//echo $sql_btb;die;
			
			$btb_result=sql_select($sql_btb);
			$btb_data_array=array();
			foreach($btb_result as $row)
			{
				$wo_lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				
				$key=$row[csf("item_group")]."__".strtoupper(trim($row[csf("item_description")]));
				
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_id"]=$row[csf("lc_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_number"]=$row[csf("lc_number")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_date"]=$row[csf("lc_date")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_tenor"]=$row[csf("tenor")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["lc_value"]+=$row[csf("amount")];
				}
			}
			
			//var_dump($all_data_arr);die;
			
			if(count($wo_lc_pi)>0)
			{
				//echo "sumon";die;
				$wo_lc_pi_array=array_chunk($wo_lc_pi,999, true);
				//print_r($pi_id_array);die;
				$wo_lc_pi_cond="";
				$qi=0;
				foreach($wo_lc_pi_array as $key=> $value)
				{
					if($qi==0)
					{
						$wo_lc_pi_cond=" and (c.id  in(".implode(",",$value).")"; 				
					}
					else
					{
						$wo_lc_pi_cond.=" or c.id  in(".implode(",",$value).")";				
					}
					
					$qi++;
				}
				$wo_lc_pi_cond.=")";

				 $sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no, a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, a.bill_of_entry_no, c.id as pi_dtls_id, c.work_order_no, c.item_group, c.item_description, c.uom, c.quantity, c.amount, a.eta_date
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id $wo_lc_pi_cond and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0";
				//echo $sql_invoice;die;
				$invoice_result=sql_select($sql_invoice);
				foreach($invoice_result as $row)
				{
					$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
					
					$key=$row[csf("item_group")]."__".strtoupper(trim($row[csf("item_description")]));
					
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_date"]=$row[csf("invoice_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_container_no"]=$row[csf("container_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];
					
					
					if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
					{
						$inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
						$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_value"]+=$row[csf("amount")];
					}
					
					if($inv_pack_check[$row[csf("work_order_no")]][$key][$row[csf("inv_id")]]=="")
					{
						$inv_pack_check[$row[csf("work_order_no")]][$key][$row[csf("inv_id")]]=$row[csf("inv_id")];
						$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_pkg_quantity"]+=$row[csf("pkg_quantity")];
					}
				}
			}
			
			//var_dump($all_data_arr);die;
		}
		//echo "<pre>";print_r($all_data_arr);die;
		
		if(!empty($inv_id_array))
		{
			$sql_pay="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment where status_active=1 and invoice_id in (".implode(",",$inv_id_array).")";
			$pay_result=sql_select($sql_pay);
			$payment_data_arr=array();
			foreach($pay_result as $row)
			{
				$payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
				$payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				$payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
			}
		}
	}
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_pi_no=str_replace("'","",$txt_pi_no);
	
	if(($cbo_date_type==3 &&  ($txt_date_from && $txt_date_to)) ||  ($txt_pi_no!=""))
	{
        $sql_cond="";
		if($cbo_company_name) $sql_cond.=" and a.company_id='$cbo_company_name' ";
		if($cbo_item_category_id) $sql_cond.=" and a.item_category='$cbo_item_category_id' ";
		if($txt_pi_no !="") 
		{
			$sql_cond.=" and e.id in($txt_pi_id) ";
		}
		//if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and a.booking_date between  '$txt_date_from' and '$txt_date_to'";
		if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and e.pi_date between  '$txt_date_from' and '$txt_date_to'";
		
		 $sql_wo_pi="SELECT a.id as wo_mst_id, a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.supplier_id, a.BUYER_ID, b.id as wo_dtls_id, b.trim_group, c.id as trim_book_con_dtls_id, c.description, b.uom, c.cons as qnty, c.rate, c.amount, a.currency_id, e.id as pi_id, e.pi_number, e.pi_date, e.supplier_id as pi_supplier_id, e.currency_id as pi_currency_id, d.id as pi_dtls_id, d.work_order_no, d.item_group, d.item_description, d.uom as pi_uom, d.quantity as pi_qnty, d.amount as pi_amount, d.net_pi_amount
		from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c, com_pi_item_details d, com_pi_master_details e  
		where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.booking_no=c.booking_no and b.id=d.work_order_dtls_id and a.id=d.work_order_id and d.pi_id=e.id and a.booking_type=2 and b.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $supplier_cond and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0";
		//echo $sql_wo_pi;//die;
		
		$req_result=sql_select($sql_wo_pi);
		//var_dump($req_result);die;
		if(count($req_result)>0)
		{
			//echo $cbo_date_type.'XX'.$txt_pi_no;//die;
			$req_no_arr=$req_dtls_id_arr=$all_data_arr=array();
			foreach($req_result as $row)
			{
				$key=$row[csf("trim_group")]."__".strtoupper(trim($row[csf("description")]));
				
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_mst_id"]=$row[csf("wo_mst_id")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_number"]=$row[csf("wo_number")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_number_prefix_num"]=$row[csf("wo_number_prefix_num")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_date"]=$row[csf("wo_date")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_supplier_id"]=$row[csf("supplier_id")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_trim_group"]=$row[csf("trim_group")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_description"]=$row[csf("description")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_uom"]=$row[csf("uom")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_currency_id"]=$row[csf("currency_id")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_buyer_id"]=$row[csf("BUYER_ID")];
				if($wo_con_dtls_id_check[$row[csf("trim_book_con_dtls_id")]]=="")
				{
					$wo_con_dtls_id_check[$row[csf("trim_book_con_dtls_id")]]=$row[csf("trim_book_con_dtls_id")];
					$all_data_arr[$row[csf("wo_number")]][$key]["wo_qnty"]+=$row[csf("qnty")];
					$all_data_arr[$row[csf("wo_number")]][$key]["wo_amount"]+=$row[csf("amount")];
				}
				
				$all_wo_id[$row[csf("wo_mst_id")]]=$row[csf("wo_mst_id")];
				if($wo_dtls_id_check[$row[csf("wo_dtls_id")]]=="")
				{
					$wo_dtls_id_check[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
					$all_wo_dtls_id[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
					$all_data_arr[$row[csf("wo_number")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
				}
				
				$pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				
				if($pi_id_check[$row[csf("wo_number")]][$key][$row[csf("pi_id")]]=="")
				{
					$pi_id_check[$row[csf("wo_number")]][$key][$row[csf("pi_id")]]=$row[csf("pi_id")];
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_id"].=$row[csf("pi_id")].",";
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_number"].=$row[csf("pi_number")].",";
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_work_order_no"]=$row[csf("work_order_no")];
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_date"]=$row[csf("pi_date")];
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
				}
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_item_group"]=$row[csf("item_group")];
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_item_description"]=$row[csf("item_description")];
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_uom"]=$row[csf("uom")];
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_quantity"]+=$row[csf("pi_qnty")];
					$all_data_arr[$row[csf("wo_number")]][$key]["pi_amount"]+=$row[csf("pi_amount")];
					$all_data_arr[$row[csf("wo_number")]][$key]["net_pi_amount"]+=$row[csf("net_pi_amount")];
				}
			}
		}
		//var_dump($pi_id_arr);die;
		
		if(!empty($pi_id_arr))
		{
			//echo "sumon";die;
			$pi_id_array=array_chunk($pi_id_arr,999, true);
			//print_r($pi_id_array);die;
			$pi_id_cond="";
			$ji=0;
			foreach($pi_id_array as $key=> $value){
				if($ji==0){
					$pi_id_cond=" and (c.id  in(".implode(",",$value ).")"; 
				}else{
					$pi_id_cond.=" or c.id  in(".implode(",",$value ).")";
				}
				$ji++;
			}
			$pi_id_cond.=")";

			$sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.work_order_no, c.item_group, c.item_description, c.uom, c.quantity, c.amount, a.issuing_bank_id, a.etd_date 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_id_cond";
			//echo $pi_id_cond;die;
			
			$btb_result=sql_select($sql_btb);
			$btb_data_array=array();
			foreach($btb_result as $row)
			{
				$wo_lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				
				$key=$row[csf("item_group")]."__".strtoupper(trim($row[csf("item_description")]));
				
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_id"]=$row[csf("lc_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_number"]=$row[csf("lc_number")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_date"]=$row[csf("lc_date")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_tenor"]=$row[csf("tenor")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["lc_value"]+=$row[csf("amount")];
				}
			}
			//var_dump($wo_lc_pi);die;
			
			if(count($wo_lc_pi)>0)
			{
				//echo "sumon";die;
				$wo_lc_pi_array=array_chunk($wo_lc_pi,999, true);
				//print_r($pi_id_array);die;
				$wo_lc_pi_cond="";
				$ji=0;
				foreach($wo_lc_pi_array as $key=> $value)
				{
					if($ji==0){
						$wo_lc_pi_cond=" and (c.id  in(".implode(",",$value).")"; 				
					}else{
						$wo_lc_pi_cond.=" or c.id  in(".implode(",",$value).")";				
					}
					$ji++;
				}
				$wo_lc_pi_cond.=")";

				$sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, a.bill_of_entry_no, c.id as pi_dtls_id, c.work_order_no, c.item_group, c.item_description, c.uom, c.quantity, c.amount, a.eta_date
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id $wo_lc_pi_cond and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0";
				//echo $sql_invoice;//die;
				$invoice_result=sql_select($sql_invoice);
				foreach($invoice_result as $row)
				{
					$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
					
					$key=$row[csf("item_group")]."__".strtoupper(trim($row[csf("item_description")]));
					
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_date"]=$row[csf("invoice_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_container_no"]=$row[csf("container_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];
					
					if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
					{
						$inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
						$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_value"]+=$row[csf("amount")];
					}
					
					if($inv_pack_check[$row[csf("inv_id")]]=="")
					{
						$inv_pack_check[$row[csf("inv_id")]]=$row[csf("inv_id")];
						$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_pkg_quantity"]+=$row[csf("pkg_quantity")];
					}
				}
			}
			//var_dump($all_data_arr);die;
		}
		
		if(!empty($inv_id_array))
		{
			$sql_pay="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment where status_active=1 and invoice_id in (".implode(",",$inv_id_array).")";
			$pay_result=sql_select($sql_pay);
			$payment_data_arr=array();
			foreach($pay_result as $row)
			{
				$payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
				$payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				$payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
			}
		}
	}

    if(($cbo_date_type==4 &&  ($txt_date_from && $txt_date_to)) ||  ($txt_lc_no!=""))
    {
        $sql_cond="";
        if($cbo_company_name) $sql_cond.=" and a.company_id='$cbo_company_name' ";
        if($cbo_item_category_id) $sql_cond.=" and f.PI_ENTRY_FORM='".$category_wise_entry_form[$cbo_item_category_id]."'";
		
        if($txt_lc_no !="")
        {
            $sql_cond.=" and f.id in($txt_lc_id) ";
        }
        if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and f.lc_date between  '$txt_date_from' and '$txt_date_to'";

        $sql_wo_pi="SELECT a.id as wo_mst_id, a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.supplier_id, b.id as wo_dtls_id, b.trim_group, c.id as trim_book_con_dtls_id, c.description, b.uom, c.cons as qnty, c.rate, c.amount, a.currency_id, e.id as pi_id, e.pi_number, e.pi_date, e.supplier_id as pi_supplier_id, e.currency_id as pi_currency_id, d.id as pi_dtls_id, d.work_order_no, d.item_group, d.item_description, d.uom as pi_uom, d.quantity as pi_qnty, d.amount as pi_amount,  d.net_pi_amount
		from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c, com_pi_item_details d, com_pi_master_details e, com_btb_lc_master_details f, com_btb_lc_pi g  
		where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and d.pi_id=g.pi_id and f.id=g.com_btb_lc_master_details_id and a.booking_no=c.booking_no and b.id=d.work_order_dtls_id and a.id=d.work_order_id and d.pi_id=e.id and a.booking_type=2 and b.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $supplier_cond and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0";
		// echo $sql_wo_pi; die;

        $req_result=sql_select($sql_wo_pi);
        //var_dump($req_result);die;
        if(count($req_result)>0)
        {
            //echo $cbo_date_type.'XX'.$txt_pi_no;//die;
            $req_no_arr=$req_dtls_id_arr=$all_data_arr=array();
            foreach($req_result as $row)
            {
                $key=$row[csf("trim_group")]."__".strtoupper(trim($row[csf("description")]));

                $all_data_arr[$row[csf("wo_number")]][$key]["wo_mst_id"]=$row[csf("wo_mst_id")];
                $all_data_arr[$row[csf("wo_number")]][$key]["wo_number"]=$row[csf("wo_number")];
                $all_data_arr[$row[csf("wo_number")]][$key]["wo_number_prefix_num"]=$row[csf("wo_number_prefix_num")];
                $all_data_arr[$row[csf("wo_number")]][$key]["wo_date"]=$row[csf("wo_date")];
                $all_data_arr[$row[csf("wo_number")]][$key]["wo_supplier_id"]=$row[csf("supplier_id")];
                $all_data_arr[$row[csf("wo_number")]][$key]["wo_trim_group"]=$row[csf("trim_group")];
                $all_data_arr[$row[csf("wo_number")]][$key]["wo_description"]=$row[csf("description")];
                $all_data_arr[$row[csf("wo_number")]][$key]["wo_uom"]=$row[csf("uom")];
                $all_data_arr[$row[csf("wo_number")]][$key]["wo_currency_id"]=$row[csf("currency_id")];
                if($wo_con_dtls_id_check[$row[csf("trim_book_con_dtls_id")]]=="")
                {
                    $wo_con_dtls_id_check[$row[csf("trim_book_con_dtls_id")]]=$row[csf("trim_book_con_dtls_id")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["wo_qnty"]+=$row[csf("qnty")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["wo_amount"]+=$row[csf("amount")];
                }

                $all_wo_id[$row[csf("wo_mst_id")]]=$row[csf("wo_mst_id")];
                if($wo_dtls_id_check[$row[csf("wo_dtls_id")]]=="")
                {
                    $wo_dtls_id_check[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
                    $all_wo_dtls_id[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
                }

                $pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];

                if($pi_id_check[$row[csf("wo_number")]][$key][$row[csf("pi_id")]]=="")
                {
                    $pi_id_check[$row[csf("wo_number")]][$key][$row[csf("pi_id")]]=$row[csf("pi_id")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_id"].=$row[csf("pi_id")].",";
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_number"].=$row[csf("pi_number")].",";
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_work_order_no"]=$row[csf("work_order_no")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_date"]=$row[csf("pi_date")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_currency_id"]=$row[csf("currency_id")];

                }

                if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
                {
                    $pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_item_group"]=$row[csf("item_group")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_item_description"]=$row[csf("item_description")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_uom"]=$row[csf("uom")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_quantity"]+=$row[csf("pi_qnty")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["pi_amount"]+=$row[csf("pi_amount")];
                    $all_data_arr[$row[csf("wo_number")]][$key]["net_pi_amount"]+=$row[csf("net_pi_amount")];
                }
            }
        }
        //var_dump($pi_id_arr);die;

        if(!empty($pi_id_arr))
        {
            //echo "sumon";die;
            $pi_id_array=array_chunk($pi_id_arr,999, true);
            //print_r($pi_id_array);die;
            $pi_id_cond="";
            $ji=0;
            foreach($pi_id_array as $key=> $value){
                if($ji==0){
                    $pi_id_cond=" and (c.id  in(".implode(",",$value ).")";
                }else{
                    $pi_id_cond.=" or c.id  in(".implode(",",$value ).")";
                }
                $ji++;
            }
            $pi_id_cond.=")";

            $sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.work_order_no, c.item_group, c.item_description, c.uom, c.quantity, c.amount, a.issuing_bank_id, a.etd_date 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_id_cond";
            //echo $pi_id_cond;die;

            $btb_result=sql_select($sql_btb);
            $btb_data_array=array();
            foreach($btb_result as $row)
            {
                $wo_lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];

                $key=$row[csf("item_group")]."__".strtoupper(trim($row[csf("item_description")]));

                $all_data_arr[$row[csf("work_order_no")]][$key]["lc_id"]=$row[csf("lc_id")];
                $all_data_arr[$row[csf("work_order_no")]][$key]["lc_number"]=$row[csf("lc_number")];
                $all_data_arr[$row[csf("work_order_no")]][$key]["lc_date"]=$row[csf("lc_date")];
                $all_data_arr[$row[csf("work_order_no")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
                $all_data_arr[$row[csf("work_order_no")]][$key]["lc_tenor"]=$row[csf("tenor")];
                $all_data_arr[$row[csf("work_order_no")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
                $all_data_arr[$row[csf("work_order_no")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];

                if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
                {
                    $pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["lc_value"]+=$row[csf("amount")];
                }
            }
            //var_dump($wo_lc_pi);die;

            if(count($wo_lc_pi)>0)
            {
                //echo "sumon";die;
                $wo_lc_pi_array=array_chunk($wo_lc_pi,999, true);
                //print_r($pi_id_array);die;
                $wo_lc_pi_cond="";
                $ji=0;
                foreach($wo_lc_pi_array as $key=> $value)
                {
                    if($ji==0){
                        $wo_lc_pi_cond=" and (c.id  in(".implode(",",$value).")";
                    }else{
                        $wo_lc_pi_cond.=" or c.id  in(".implode(",",$value).")";
                    }
                    $ji++;
                }
                $wo_lc_pi_cond.=")";

                $sql_invoice=" SELECT a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, a.bill_of_entry_no, c.id as pi_dtls_id, c.work_order_no, c.item_group, c.item_description, c.uom, c.quantity, c.amount, a.eta_date
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id $wo_lc_pi_cond and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0";
                //echo $sql_invoice;//die;
                $invoice_result=sql_select($sql_invoice);
                foreach($invoice_result as $row)
                {
                    $inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];

                    $key=$row[csf("item_group")]."__".strtoupper(trim($row[csf("item_description")]));

                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_date"]=$row[csf("invoice_date")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_container_no"]=$row[csf("container_no")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
                    $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];

                    if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
                    {
                        $inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
                        $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_value"]+=$row[csf("amount")];
                    }

                    if($inv_pack_check[$row[csf("inv_id")]]=="")
                    {
                        $inv_pack_check[$row[csf("inv_id")]]=$row[csf("inv_id")];
                        $all_data_arr[$row[csf("work_order_no")]][$key]["invoice_pkg_quantity"]+=$row[csf("pkg_quantity")];
                    }
                }
            }
            //var_dump($all_data_arr);die;
        }

        if(!empty($inv_id_array))
        {
            $sql_pay="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment where status_active=1 and invoice_id in (".implode(",",$inv_id_array).")";
            $pay_result=sql_select($sql_pay);
            $payment_data_arr=array();
            foreach($pay_result as $row)
            {
                $payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
                $payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
                $payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
            }
        }
    }
	//echo $cbo_date_type.'AA';
	ksort($all_data_arr);
	//echo "<pre>";print_r($all_data_arr);die;

	$sql_receive=sql_select("SELECT a.receive_basis, b.booking_id, b.item_group_id, b.item_description, sum(b.receive_qnty) as rcv_qnty, sum(b.receive_qnty*b.RATE) as rcv_amt 
	from inv_receive_master a, inv_trims_entry_dtls b
	where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2) and a.entry_form =24
	group by a.receive_basis, b.booking_id, b.item_group_id, b.item_description");
	$recv_data_array=array();
	foreach($sql_receive as $row)
	{
		$key=$row[csf("item_group_id")]."__".strtoupper(trim($row[csf("item_description")]));
		$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["rcv_qnty"]+=$row[csf("rcv_qnty")];
		$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["rcv_amt"]+=$row[csf("rcv_amt")];
	}
	//echo "<pre>";print_r($recv_data_array);// die;

	$sql_rcv_rtrn= sql_select("SELECT b.BOOKING_ID, b.ITEM_GROUP_ID, b.ISSUE_QNTY, c.ITEM_DESCRIPTION
	from INV_ISSUE_MASTER a, INV_TRIMS_ISSUE_DTLS b, PRODUCT_DETAILS_MASTER c
	where a.ID=b.MST_ID and b.prod_id=c.id and c.ITEM_CATEGORY_ID=4 and a.ENTRY_FORM=49 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0");
	$rcv_rtrn_data_array=array();
	foreach($sql_rcv_rtrn as $row)
	{
		$key=$row[csf("item_group_id")]."__".strtoupper(trim($row[csf("item_description")]));
		$rcv_rtrn_data_array[$row[csf("booking_id")]][$key]["issue_qnty"]+=$row[csf("issue_qnty")];
		// $rcv_rtrn_data_array[$row[csf("booking_id")]][$key]["issue_amt"]=$row[csf("issue_amt")];
		// $rcv_rtrn_data_array[$row[csf("booking_id")]][$row[csf("item_group_id")]]["issue_amt"]=$row[csf("issue_amt")];
	}
	// echo "<pre>";print_r($rcv_rtrn_data_array);die;
	
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
	$suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
	//$indentor_name_array = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40","id","supplier_name");
	ob_start();
	?>
	<div style="width:4500px; margin-left:10px">
		<fieldset style="width:100%;">	 
			<table width="4500" cellpadding="0" cellspacing="0" id="caption">
				<tr>
					<td align="center" width="100%" colspan="21" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" colspan="21" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>  
			</table>
            <table width="4500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th colspan="11" >Work Order Details</th>
                        <th colspan="11" >PI Details</th>
                        <th colspan="7">L/C Details</th>
                        <th colspan="13">Invoice Details</th>
                        <th colspan="4">Payment Details</th>
                        <th colspan="5">Store details</th>
                    </tr>
                    <tr>
                        <!--880 wo details 10 -->
                        <th width="30">SL No</th>
                        <th width="50">WO No</th>
                        <th width="100">Insert User</th>
                        <th width="70">WO Date</th>
                        <th width="140">Supplier</th>
                        <th width="100">Item Group</th>
                        <th width="150">Item Description</th> 
                        <th width="60">UOM</th>
                        <th width="80">WO Qnty</th>
                        <th width="70">Unit Price</th>
                        <th width="100">WO Amount </th>
                                                
                        <!--950 pi details 10 -->
						<th width="150">Buyer</th>
                        <th width="150">PI No</th>
                        <th width="70">PI Date</th>
                        <th width="140">Supplier</th>
                        <th width="100">Item Group</th>
                        <th width="150">Item Description</th> 
                        <th width="60">UOM</th>
                        <th width="80">PI Qnty</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PI Amount </th>
                        <th width="100">Net PI Amount </th>
                        <th width="70">Currency</th>
                        
                        <!--550 lc details 7 -->
                        <th width="70">LC Date</th>
                        <th width="120">LC No</th>
                        <th width="80">Pay Term</th>
                        <th width="50">Tenor</th>
                        <th width="100">LC Amount</th>
                        <th width="70">Shipment Date</th>
                        <th width="80">Expiry Date</th>
                        
                        <!--1180 Invoice details 13 -->
                        <th width="150">Invoice No</th>
                        <th width="70">Invoice Date</th>
                        <th width="80">Incoterm</th>
                        <th width="100">Incoterm Place</th>
                        <th width="80">B/L No</th>
                        <th width="70">BL Date</th>
                        <th width="100">Mother Vassel</th>
                        <th width="100">Feedar Vassel</th>
                        <th width="100">Continer No</th>
                        <th width="80">Pkg Qty</th>
                        <th width="100">Doc Send to CNF</th>
                        <th width="70">NN Doc Received Date</th>
                        <th width="80">Bill Of Entry No</th>
                        
                        <!--290 Payment details 4 -->
                        <th width="70">Maturity Date</th>
                        <th width="70">Maturity Month</th>
                        <th width="70">Payment Date</th>
                        <th width="80">Paid Amount</th>
                        
                        <!--320 MRR details 4 -->
                        <th width="80">MRR Qnty</th>
                        <th width="100">MRR Value</th>
                        <th width="100">Short Value</th>
                        <th>Pipe Line</th>
                    </tr>
                </thead>
                <tbody id="table_body">
				<?
				// echo "<pre>"; print_r($all_data_arr); die;
				$i=1;
				$btb_tem_lc_array=$inv_temp_array=array();
				$total_req_qnty=$total_wo_qnty=$total_wo_amt=$total_wo_balanc=$total_pi_qnty=$total_pi_amt=$total_lc_amt=$total_pkg_qnty=$total_pay_amt=$total_mrr_qnty=$total_mrr_amt=$total_short_amt=$total_pipe_line=$receive_value=$rcv_rtrn_qnty=0;
				foreach($all_data_arr as $wo_no=>$wo_val)
				{
					foreach($wo_val as $item_ref=>$prod_val)
					{
						$prod_data_ref=explode("__",$item_ref);
						$item_group_id=$prod_data_ref[0];
						$item_description=$prod_data_ref[1];
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><p><? echo $i; ?>&nbsp;</p></td>
							<td align="center" title="<? echo $prod_data_ref.jahid; ?>"><p><? echo $prod_val["wo_number_prefix_num"]; ?>&nbsp;</p></td>
							<td align="center"><p><? echo $user_library[$prod_val["inserted_by"]]; ?>&nbsp;</p></td>
                            <td align="center"><? if($prod_val["wo_date"]!="" &&  $prod_val["wo_date"]!="0000-00-00")echo change_date_format($prod_val["wo_date"]); ?></td>
                            <td><p><? echo $suplier_array[$prod_val["wo_supplier_id"]];?>&nbsp;</p></td>
							<td><p><? echo $item_group_array[$prod_val["wo_trim_group"]]; ?>&nbsp;</p></td>
							<td><p><? echo $prod_val["wo_description"]; ?>&nbsp;</p></td>
							<td align="center"><p><? echo $unit_of_measurement[$prod_val["wo_uom"]]; ?>&nbsp;</p></td>
							<td align="right"><? echo number_format($prod_val["wo_qnty"],2);$total_wo_qnty+=$prod_val["wo_qnty"]; ?></td>
							<td align="right"><? if($prod_val["wo_amount"]>0 && $prod_val["wo_qnty"]>0) { $wo_rate=$prod_val["wo_amount"]/$prod_val["wo_qnty"]; echo  number_format($wo_rate,4);} else echo "0.00"; ?></td>
                            <td align="right"><? echo  number_format($prod_val["wo_amount"],2);$total_wo_amt+=$prod_val["wo_amount"]; ?></td>

							<td align="center" title="<? ?>"><p><? echo  $lib_buyer[$prod_val["wo_buyer_id"]]; ?> &nbsp;</p></td>

							<td title="<? echo $prod_val["pi_work_order_no"]; ?>"><p><? echo implode(",",array_unique(explode(",",chop($prod_val["pi_number"],",")))); ?> &nbsp;</p></td>
							<td align="center"><? if($prod_val["pi_date"]!="" &&  $prod_val["pi_date"]!="0000-00-00")echo change_date_format($prod_val["pi_date"]); ?></td>
							<td><p><? echo $suplier_array[$prod_val["pi_supplier_id"]];?>&nbsp;</p></td>
							<td><p><? echo $item_group_array[$prod_val["pi_item_group"]]; ?>&nbsp;</p></td>
							<td><p><? echo $prod_val["pi_item_description"]; ?>&nbsp;</p></td>
							<td align="center"><p><? echo $unit_of_measurement[$prod_val["pi_uom"]]; ?>&nbsp;</p></td>
							<td align="right"><? echo number_format($prod_val["pi_quantity"],2);$total_pi_qnty+=$prod_val["pi_quantity"]; ?></td>
							<td align="right"><? if($prod_val["pi_amount"]>0 && $prod_val["pi_quantity"]>0) { $pi_rate=$prod_val["pi_amount"]/$prod_val["pi_quantity"]; echo  number_format($pi_rate,2);} else echo "0.00"; ?></td>
							<td align="right"><? echo number_format($prod_val["pi_amount"],2);$total_pi_amt+=$prod_val["pi_amount"]; ?></td>
							<td align="right"><? echo number_format($prod_val["net_pi_amount"],2);$total_net_pi_amt+=$prod_val["net_pi_amount"]; ?></td>
							<td align="center"><p><? echo $currency[$prod_val["pi_currency_id"]]; ?>&nbsp;</p></td>
							<td align="center"><? if($prod_val["lc_date"]!="" &&  $prod_val["lc_date"]!="0000-00-00")echo change_date_format($prod_val["lc_date"]); ?></td>
							<td><p><? echo $prod_val["lc_number"]; ?> &nbsp;</p></td>
							<td><p><? echo $pay_term[$prod_val["lc_payterm_id"]]; ?> &nbsp;</p></td>
							<td align="right"><p><? echo $prod_val["lc_tenor"]; ?> &nbsp;</p></td>
							<td align="right"><? echo number_format($prod_val["lc_value"],2);$total_lc_amt+=$prod_val["lc_value"]; ?></td>
							<td align="center"><? if($prod_val["lc_last_shipment_date"]!="" &&  $prod_val["lc_last_shipment_date"]!="0000-00-00")echo change_date_format($prod_val["lc_last_shipment_date"]); ?></td>
							<td align="center"><? if($prod_val["lc_expiry_date"]!="" &&  $prod_val["lc_expiry_date"]!="0000-00-00")echo change_date_format($prod_val["lc_expiry_date"]); ?></td>
							<td><p><? echo implode(',', array_flip(array_flip(explode(',', rtrim($prod_val["invoice_no"],','))))); ?> &nbsp;</p></td>
							<td align="center"><? if($prod_val["invoice_date"]!="" &&  $prod_val["invoice_date"]!="0000-00-00") echo change_date_format($prod_val["invoice_date"]); ?></td>
							<td><p><? echo $prod_val["invoice_inco_term"]; ?> &nbsp;</p></td>
							<td><p><? echo $prod_val["invoice_inco_term_place"]; ?> &nbsp;</p></td>
							<td><p><? echo $prod_val["invoice_bill_no"]; ?> &nbsp;</p></td>
							<td align="center"><? if($prod_val["invoice_bill_date"]!="" &&  $prod_val["invoice_bill_date"]!="0000-00-00")echo change_date_format($prod_val["invoice_bill_date"]); ?></td>
							<td><p><? echo $prod_val["invoice_mother_vessel"]; ?> &nbsp;</p></td>
							<td><p><? echo $prod_val["invoice_feeder_vessel"]; ?> &nbsp;</p></td>
							<td><p><? echo $prod_val["invoice_container_no"]; ?> &nbsp;</p></td>

							<td align="right"><? echo number_format($prod_val["invoice_pkg_quantity"],2);$total_pkg_qnty+=$prod_val["invoice_pkg_quantity"]; ?></td>
							<td align="center"><? if($prod_val["invoice_doc_to_cnf"]!="" &&  $prod_val["invoice_doc_to_cnf"]!="0000-00-00")echo change_date_format($prod_val["invoice_doc_to_cnf"]); ?></td>
							<td align="center"></td>
							<td><p><? echo $prod_val["invoice_bill_of_entry_no"]; ?> &nbsp;</p></td>
							<td align="center"><? if($prod_val["invoice_maturity_date"]!="" &&  $prod_val["invoice_maturity_date"]!="0000-00-00") echo change_date_format($prod_val["invoice_maturity_date"]); ?></td>
							<td align="center"><? if($prod_val["invoice_maturity_date"]!="" &&  $prod_val["invoice_maturity_date"]!="0000-00-00") echo change_date_format($prod_val["invoice_maturity_date"]); ?></td>
							<?
							$all_inv_id=array_unique(explode(",",chop($prod_val["invoice_id"],",")));
							$pay_amt=0;
							foreach($all_inv_id as $inv_id)
							{
								$pay_date=$payment_data_arr[$inv_id]["payment_date"];
								$pay_amt+=$payment_data_arr[$inv_id]["accepted_ammount"];
							}
							?>
							<td align="center"><? if($pay_date!="" &&  $pay_date!="0000-00-00")echo change_date_format($pay_date); ?></td>
							<td align="right"><? echo number_format($pay_amt,2);$total_pay_amt+=$pay_amt; ?></td>
							<?
							$pi_id_all=array_unique(explode(",",chop($prod_val["pi_id"],",")));
							// echo "<pre>";print_r($pi_id_all);die;
							$wo_mst_id_all=array_unique(explode(",",chop($prod_val["wo_mst_id"],",")));
							// echo "<pre>";print_r($wo_mst_id_all);die;
							
							$recv_pi_wo_req="";$rcv_qnty="";$rcv_value="";

						
							foreach($pi_id_all as $val)
							{
								
								$rcv_qnty+=$recv_data_array[1][$val][$item_ref]["rcv_qnty"];
								$rcv_value+=$recv_data_array[1][$val][$item_ref]["rcv_amt"];
								$recv_pi_wo_req.=$val.",";
								
							}
							$recv_pi_wo_req=chop($recv_pi_wo_req,",");
							if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**1";
							if($rcv_qnty=="")
							{
								$recv_pi_wo_req="";
								foreach($wo_mst_id_all as $val)
								{
									$rcv_qnty+=$recv_data_array[2][$val][$item_ref]["rcv_qnty"];
									$rcv_value+=$recv_data_array[2][$val][$item_ref]["rcv_amt"];
									$recv_pi_wo_req.=$val.",";
								}
								$recv_pi_wo_req=chop($recv_pi_wo_req," , ");
								if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**2";
							}
							$pipe_pi_qnty="";$pipe_wo_qnty="";
							/*foreach($all_req_dtls_id_arr as $val)
							{
								$pipe_wo_qnty+=$wo_pipe_array[$val][$product_id];
								$pipe_pi_qnty+=$pi_pipe_array[$val][$product_id];
							}*/
							
							?>
							<td align="right" title="<?= $item_ref;?>"><a href="##" onClick="openmypage_popup('<? echo $recv_pi_wo_req; ?>','<? echo $item_ref; ?>','Receive Info','receive_popup_trims');" >
								<? 
								$pi_data = explode("**",$recv_pi_wo_req);
								// $item_ref_data = explode("__",$item_ref);
								$rcv_rtrn_qnty=$rcv_rtrn_data_array[ $pi_data[0]][$item_ref]["issue_qnty"];
								// echo $rcv_rtrn_qnty;
								echo number_format($rcv_qnty-$rcv_rtrn_qnty,2);
								$total_mrr_qnty+=$rcv_qnty;
								?>
						 	</a></td>
							<td align="right">
								<? 
								$receive_value=($rcv_qnty-$rcv_rtrn_qnty)*($rcv_value/$rcv_qnty);
								if(is_nan($receive_value)) $receive_value=0;
								echo number_format($receive_value,2); 
								$total_mrr_amt+=$receive_value; 
								?>
							</td>
							<td align="right" title="Wo Value-Receive Value"><? $short_value=$prod_val["wo_amount"]-$receive_value; echo number_format($short_value,2);  $total_short_amt+=$short_value; ?></td>
							<?
							$pipe_line=$prod_val["wo_qnty"]-$rcv_qnty+$rcv_rtrn_qnty;
							?>
							<td align="right"><? echo number_format($pipe_line,2); $total_pipe_line+=$pipe_line;?></td>
						</tr>
						<?
						$i++;
					}
					
				}
				?>
				</tbody>
                <tfoot>
                	<tr bgcolor="#CCCCCC">
						<!--1110 wo details-->
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
                        <td></td>
						<td></td>
						<td></td>
						<td align="right"><? //echo number_format($total_wo_qnty,2); ?></td>
						<td></td>
						<td align="right"><? echo number_format($total_wo_amt,2); ?></td>
						
						<!--840 pi details-->
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"><? //echo number_format($total_pi_qnty,2); ?></td>
						<td></td>
						<td align="right"><? echo number_format($total_pi_amt,2); ?></td>
						<td align="right"><? echo number_format($total_net_pi_amt,2); ?></td>
						<td></td>
						
						<!--550 lc details-->
						<td></td>
						<td></td>
						<td></td>
                        <td></td>
						<td align="right"><? echo number_format($total_lc_amt,2); ?></td>
						<td></td>
						<td></td>
						
						<!--1100 Invoice details-->
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"><? //echo number_format($total_pkg_qnty,2); ?></td>
						<td ></td>
						<td></td>
						<td></td>
						
						<!--290 Payment details-->
						<td></td>
						<td></td>
						<td></td>
						<td align="right"><? echo number_format($total_pay_amt,2); ?></td>
						
						<!--340 MRR details-->
						<td align="right"><? //echo number_format($total_mrr_qnty,2); ?></td>
						<td align="right"><? echo number_format($total_mrr_amt,2); ?></td>
						<td align="right"><? echo number_format($total_short_amt,2); ?></td>
						<td align="right"><? //echo number_format($total_pipe_line,2); ?></td>
					</tr>
                </tfoot>	
			</table>
		</fieldset>
	</div>
	<?
	
		
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
	echo "$total_data####$filename####2";
	exit();
}

if($action=="report_generate_woven")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	//$cbo_item_category_id=3;
	
	//echo "test";die;

	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	$txt_pi_no=trim(str_replace("'","",$txt_pi_no));
	
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	// if($txt_pi_no) $pi_no_cond="and a.pi_number='$txt_pi_no' ";else $pi_no_cond="";

	$pi_number_txt =str_replace("'","",$txt_pi_no);
	$pi_no_arr=array_unique(explode(",",$pi_number_txt));
	$all_pi="";
	foreach($pi_no_arr as $pi_id)
	{
		$all_pi.="'".$pi_id."'"." , ";
	}
	 $all_txt_pi_no=chop($all_pi, " , ");
	if($txt_pi_no ) $pi_lc_no_cond="and e.pi_number in ($all_txt_pi_no) ";else $pi_lc_no_cond="";
	if($txt_pi_no ) $pi_no_cond="and a.pi_number in ($all_txt_pi_no) ";else $pi_no_cond="";

	$txt_lc_no=trim(str_replace("'","",$txt_lc_no));
	$lc_number_txt =str_replace("'","",$txt_lc_no);
	$lc_no_arr=array_unique(explode(",",$lc_number_txt));
	$all_lc="";
	foreach($lc_no_arr as $lc_id)
	{
		$all_lc.="'".$lc_id."'"." , ";
	}
	$all_txt_lc_no=chop($all_lc, " , ");
	if($txt_lc_no ) $lc_no_cond="and e.lc_number in ($all_txt_lc_no) ";else $lc_no_cond="";


	$cbo_supplier=str_replace("'","",$cbo_supplier);
	//echo $cbo_supplier;
	if($cbo_supplier>0) $supplier_cond =" and a.supplier_id='$cbo_supplier' ";else $supplier_cond= "";
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_wo_po_no=trim(str_replace("'","",$txt_wo_po_no));

	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}
	$inv_id_array=array();
	//if(($cbo_date_type==3 &&  ($txt_date_from && $txt_date_to)) ||  ($txt_wo_po_no!="" ) )
	if(($cbo_date_type==3) ||  ($txt_wo_po_no!="" ) )
	{
		
		//echo "nazim";
		$sql_cond="";
		if($cbo_company_name) $sql_cond.=" and a.company_id='$cbo_company_name' ";
		if($cbo_item_category_id) $sql_cond.=" and a.item_category='$cbo_item_category_id' ";
		if($txt_wo_po_no !="") 
		{
			$sql_cond.=" and a.booking_no in ('$txt_wo_po_no') ";
		}
		if($txt_date_from !="" && $txt_date_to !="") {
			$sql_cond.=" and a.booking_date between  '$txt_date_from' and '$txt_date_to'";
		}
		$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		$item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
		$suplier_arr = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
		$currency_library = return_library_array("select currency, conversion_rate from currency_conversion_rate where company_id=$cbo_company_name and is_deleted=0 and status_active=1", 'currency', 'conversion_rate');
		$lib_buyer=return_library_array("select ID, BUYER_NAME FROM LIB_BUYER","ID","BUYER_NAME");
		
	
		$pi_lc_chk_sql ="SELECT a.id as wo_mst_id, e.id as pi_id, e.pi_number from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c, com_pi_item_details d, com_pi_master_details e where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and 
		a.id=d.work_order_id and d.pi_id=e.id and c.lib_yarn_count_deter_id=d.determination_id and a.booking_type=1 and b.booking_type=1 and a.status_active=1 and
		a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $supplier_cond $pi_lc_no_cond group by a.id, e.id, e.pi_number";
		//echo $pi_lc_chk_sql;
		$pi_lc_chk_sql_res=sql_select($pi_lc_chk_sql);
		foreach($pi_lc_chk_sql_res as $row )	
		{
			$wo_pi_number_chk .="'".$row[csf("wo_mst_id")]."'"." , ";
		}
		$all_wo_pi_number_chk=chop($wo_pi_number_chk, " , ");
		if($txt_pi_no) $pi_wo_cond="and a.id in ($all_wo_pi_number_chk) ";else $pi_wo_cond="";


		$sql_lc_btb="SELECT a.id as wo_mst_id,e.lc_number as lc_number
		from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c, com_btb_lc_master_details e, com_btb_lc_pi f, com_pi_item_details d
		where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and  f.pi_id=d.pi_id and 
		e.id=f.com_btb_lc_master_details_id and
		a.id=d.work_order_id and c.lib_yarn_count_deter_id=d.determination_id and a.booking_type=1 and b.booking_type=1 and a.status_active=1 and
		a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $supplier_cond $lc_no_cond group by a.id,e.lc_number ";
		//echo $sql_lc_btb;
		$lc_chk_sql_res=sql_select($sql_lc_btb);
		foreach($lc_chk_sql_res as $row )	
		{
			$wo_lc_number_chk .="'".$row[csf("wo_mst_id")]."'"." , ";
		}
		$all_wo_lc_number_chk=chop($wo_lc_number_chk, " , ");
		if($txt_lc_no) $lc_wo_cond="and a.id in ($all_wo_lc_number_chk) ";else $lc_wo_cond="";



		//print_r($suplier_arr); //die
		$composition_arr=array(); $construction_arr=array(); $composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$sql_deter_res=sql_select($sql_deter);
		if(count($sql_deter_res)>0)
		{
			foreach( $sql_deter_res as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					$construction_arr[$row[csf('id')]]=$row[csf('construction')];
				}
			}
		}
		unset($sql_deter_res);

		$sql_wo="select a.id as wo_mst_id,a.buyer_id, a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.supplier_id, a.currency_id, b.id as wo_dtls_id, a.item_category,  b.pre_cost_fabric_cost_dtls_id , b.trim_group, b.fin_fab_qnty as qnty, b.grey_fab_qnty, b.req_qty, c.uom, b.rate, b.amount, c.lib_yarn_count_deter_id
		from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c
		where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type in(1,4) and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $supplier_cond $sql_cond $pi_wo_cond $lc_wo_cond";
	
		//echo $sql_wo;//die;
		
		$req_result=sql_select($sql_wo);

		$req_no_arr=$req_dtls_id_arr=$all_data_arr=array();
		foreach($req_result as $row)
		{
			$key=$row[csf("lib_yarn_count_deter_id")];
			
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_mst_id"]=$row[csf("wo_mst_id")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_number"]=$row[csf("wo_number")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_number_prefix_num"]=$row[csf("wo_number_prefix_num")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_date"]=$row[csf("wo_date")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_supplier_id"]=$suplier_arr[$row[csf("supplier_id")]];
			$all_data_arr[$row[csf("wo_number")]][$key]["item_category"]=$row[csf("item_category")];
			$all_data_arr[$row[csf("wo_number")]][$key]["copmposition"]=$composition_arr[$row[csf("lib_yarn_count_deter_id")]];
			$all_data_arr[$row[csf("wo_number")]][$key]["construction"]=$construction_arr[$row[csf("lib_yarn_count_deter_id")]];
			//$all_data_arr[$row[csf("wo_number")]][$key]["wo_description"]=$row[csf("description")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_uom"]=$row[csf("uom")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_currency_id"]=$row[csf("currency_id")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_qnty"]+=$row[csf("qnty")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_amount"]+=$row[csf("amount")];
			$all_data_arr[$row[csf("wo_number")]][$key]["buyer_id"]=$row[csf("buyer_id")];
			
			$all_wo_id[$row[csf("wo_mst_id")]]=$row[csf("wo_mst_id")];
			if($wo_dtls_id_check[$row[csf("wo_dtls_id")]]=="")
			{
				$wo_dtls_id_check[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
				$all_wo_dtls_id[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
			}

			$all_data_arr[$row[csf("wo_number")]][$key]["currency_rate"] = $currency_library[$row[csf("currency_id")]];
		}		
		
		//var_dump($all_data_arr);die;
		///############################## based on requistion start ##############################################/////////////
		if(!empty($all_wo_id))
		{
			//echo "sumon";die;
			$all_wo_id_array=array_chunk($all_wo_id,999, true);
			//print_r($pi_id_array);die;
			$all_wo_id_cond="";
			$si=0;
			foreach($all_wo_id_array as $key=> $value)
			{
				if($si==0)
				{
					$all_wo_id_cond=" and (b.work_order_id  in(".implode(",",$value).")"; 				
				}
				else
				{
					$all_wo_id_cond.=" or b.work_order_id  in(".implode(",",$value).")";				
				}
				
				$si++;
			}
			$all_wo_id_cond.=")";
			//echo $all_wo_dtls_id_cond;die;

			 $sql_pi="select a.id as pi_id, a.pi_number, a.pi_date, a.supplier_id, a.currency_id, b.id as pi_dtls_id, b.work_order_no, b.determination_id, b.item_group, b.item_description, b.uom, b.quantity, b.amount
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.after_goods_source=1 and b.after_goods_source=1 and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=3 $pi_no_cond $all_wo_id_cond";
			//echo $sql_pi;die;
			$sql_pi_result=sql_select($sql_pi);

			$pi_req_arr=$pi_id_arr=array();
			foreach($sql_pi_result as $row)
			{
				$pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				$pi_req_arr[$row[csf("pi_dtls_id")]]=$req_no_arr[$row[csf("requisition_dtls_id")]];
				
				$key=$row[csf("determination_id")];
				
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_id"].=$row[csf("pi_id")].",";
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_number"].=$row[csf("pi_number")].",";
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_work_order_no"]=$row[csf("work_order_no")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_date"]=$row[csf("pi_date")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_supplier_id"]=$suplier_arr[$row[csf("supplier_id")]];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["copmposition"]=$composition_arr[$row[csf("determination_id")]];
				$all_data_arr[$row[csf("work_order_no")]][$key]["construction"]=$construction_arr[$row[csf("determination_id")]];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_uom"]=$row[csf("uom")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_quantity"]+=$row[csf("quantity")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["pi_amount"]+=$row[csf("amount")];
			} 
		}
		
		//var_dump($all_data_arr);die;
		
		if(!empty($pi_id_arr))
		{
			//echo "sumon";die;
			$pi_id_array=array_chunk($pi_id_arr,999, true);
			//print_r($pi_id_array);die;
			$pi_id_cond="";
			$di=0;
			foreach($pi_id_array as $key=> $value)
			{
				if($di==0)
				{
					$pi_id_cond=" and (c.id  in(".implode(",",$value).")"; 				
				}
				else
				{
					$pi_id_cond.=" or c.id  in(".implode(",",$value).")";				
				}
				
				$di++;
			}
			$pi_id_cond.=")";
			//echo $pi_id_cond;die;

			$sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.work_order_no, c.determination_id ,c.item_group, c.item_description, c.uom, c.quantity, c.amount, a.issuing_bank_id, a.etd_date 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_id_cond";
			//echo $sql_btb;die;
			
			$btb_result=sql_select($sql_btb);
			$btb_data_array=array();
			foreach($btb_result as $row)
			{
				$wo_lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				
				$key=$row[csf("determination_id")];
				
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_id"]=$row[csf("lc_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_number"]=$row[csf("lc_number")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_date"]=$row[csf("lc_date")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_tenor"]=$row[csf("tenor")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["lc_value"]+=$row[csf("amount")];
				}
			}
			
			//var_dump($all_data_arr);die;
			
			if(count($wo_lc_pi)>0)
			{
				//echo "sumon";die;
				$wo_lc_pi_array=array_chunk($wo_lc_pi,999, true);
				//print_r($pi_id_array);die;
				$wo_lc_pi_cond="";
				$qi=0;
				foreach($wo_lc_pi_array as $key=> $value)
				{
					if($qi==0)
					{
						$wo_lc_pi_cond=" and (c.id  in(".implode(",",$value).")"; 				
					}
					else
					{
						$wo_lc_pi_cond.=" or c.id  in(".implode(",",$value).")";				
					}
					
					$qi++;
				}
				$wo_lc_pi_cond.=")";

				$sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no, a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, a.bill_of_entry_no, c.id as pi_dtls_id, c.work_order_no, c.determination_id, c.item_group, c.item_description, c.uom, c.quantity, c.amount, a.eta_date
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id $wo_lc_pi_cond and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0";
				//echo $sql_invoice;die;
				$invoice_result=sql_select($sql_invoice);
				foreach($invoice_result as $row)
				{
					$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
					
					$key=$row[csf("determination_id")];
					
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_date"]=$row[csf("invoice_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_container_no"]=$row[csf("container_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];
					
					
					if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
					{
						$inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
						$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_value"]+=$row[csf("amount")];
					}
					
					if($inv_pack_check[$row[csf("work_order_no")]][$key][$row[csf("inv_id")]]=="")
					{
						$inv_pack_check[$row[csf("work_order_no")]][$key][$row[csf("inv_id")]]=$row[csf("inv_id")];
						$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_pkg_quantity"]+=$row[csf("pkg_quantity")];
					}
				}
			}
			
			//var_dump($all_data_arr);die;
		}
		//echo "<pre>";print_r($all_data_arr);die;
		
		if(!empty($inv_id_array))
		{
			$sql_pay="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment where status_active=1 and invoice_id in (".implode(",",$inv_id_array).")";
			$pay_result=sql_select($sql_pay);
			$payment_data_arr=array();
			foreach($pay_result as $row)
			{
				$payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
				$payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				$payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
			}
		}
	}
	
	/*if(($cbo_date_type==3 &&  ($txt_date_from && $txt_date_to)) ||  ($txt_pi_no!=""))
	{
		//echo "dfgfdg";
		$sql_cond="";
		if($cbo_company_name) $sql_cond.=" and a.company_id='$cbo_company_name' ";
		if($cbo_item_category_id) $sql_cond.=" and a.item_category='$cbo_item_category_id' ";
		if($txt_pi_no !="") 

		{
			$sql_cond.=" and e.pi_number = '$txt_pi_no' ";
		}

		if($txt_wo_po_no !="") 
		{
			$sql_cond.=" and a.booking_no_prefix_num = '$txt_wo_po_no' ";
		}
		//if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and a.booking_date between  '$txt_date_from' and '$txt_date_to'";
		if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and e.pi_date between  '$txt_date_from' and '$txt_date_to'";
		//and b.id=d.work_order_dtls_id this condition not applicable for woven fabric
		$sql_wo_pi="select a.id as wo_mst_id, a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.supplier_id, a.currency_id, b.id as wo_dtls_id, b.fin_fab_qnty as qnty, b.grey_fab_qnty, b.req_qty, b.uom, b.rate, b.amount,c.lib_yarn_count_deter_id, e.id as pi_id, e.pi_number, e.pi_date, e.supplier_id as pi_supplier_id, e.currency_id as pi_currency_id, d.id as pi_dtls_id, d.work_order_no, d.item_group, d.item_description, d.uom as pi_uom, d.quantity as pi_qnty, d.amount as pi_amount
		from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c, com_pi_item_details d, com_pi_master_details e  
		where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.id=d.work_order_id and d.pi_id=e.id and c.lib_yarn_count_deter_id=d.determination_id and a.booking_type=1 and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $supplier_cond";
		//echo $sql_wo_pi;//die;
		
		/*$sql_wo="select a.id as wo_mst_id, a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.supplier_id, a.currency_id, b.id as wo_dtls_id, b.pre_cost_fabric_cost_dtls_id , b.trim_group, b.fin_fab_qnty as qnty, b.grey_fab_qnty, b.req_qty, b.uom, b.rate, b.amount,c.lib_yarn_count_deter_id
		from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c
		where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=1 and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond";
		
		$req_result=sql_select($sql_wo_pi);
		//var_dump($req_result);die;

		$req_no_arr=$req_dtls_id_arr=$all_data_arr=array();
		foreach($req_result as $row)
		{
			$key=$row[csf("lib_yarn_count_deter_id")];
			
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_mst_id"]=$row[csf("wo_mst_id")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_number"]=$row[csf("wo_number")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_number_prefix_num"]=$row[csf("wo_number_prefix_num")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_date"]=$row[csf("wo_date")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_supplier_id"]=$row[csf("supplier_id")];
			$all_data_arr[$row[csf("wo_number")]][$key]["copmposition"]=$composition_arr[$row[csf("determination_id")]];
			$all_data_arr[$row[csf("wo_number")]][$key]["construction"]=$construction_arr[$row[csf("determination_id")]];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_uom"]=$row[csf("uom")];
			$all_data_arr[$row[csf("wo_number")]][$key]["wo_currency_id"]=$row[csf("currency_id")];
			if($wo_con_dtls_id_check[$row[csf("trim_book_con_dtls_id")]]=="")
			{
				$wo_con_dtls_id_check[$row[csf("trim_book_con_dtls_id")]]=$row[csf("trim_book_con_dtls_id")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_qnty"]+=$row[csf("qnty")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_amount"]+=$row[csf("amount")];
			}
			
			
			$all_wo_id[$row[csf("wo_mst_id")]]=$row[csf("wo_mst_id")];
			if($wo_dtls_id_check[$row[csf("wo_dtls_id")]]=="")
			{
				$wo_dtls_id_check[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
				$all_wo_dtls_id[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
				$all_data_arr[$row[csf("wo_number")]][$key]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
			}
			
			
			$pi_id_arr[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
			
			if($pi_id_check[$row[csf("wo_number")]][$key][$row[csf("pi_id")]]=="")
			{
				$pi_id_check[$row[csf("wo_number")]][$key][$row[csf("pi_id")]]=$row[csf("pi_id")];
				$all_data_arr[$row[csf("wo_number")]][$key]["pi_id"].=$row[csf("pi_id")].",";
				$all_data_arr[$row[csf("wo_number")]][$key]["pi_number"].=$row[csf("pi_number")].",";
				$all_data_arr[$row[csf("wo_number")]][$key]["pi_work_order_no"]=$row[csf("work_order_no")];
				$all_data_arr[$row[csf("wo_number")]][$key]["pi_date"]=$row[csf("pi_date")];
				$all_data_arr[$row[csf("wo_number")]][$key]["pi_supplier_id"]=$row[csf("supplier_id")];
				$all_data_arr[$row[csf("wo_number")]][$key]["pi_currency_id"]=$row[csf("currency_id")];
				
			}
			
			if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
			{
				$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				$all_data_arr[$row[csf("wo_number")]][$key]["copmposition"]=$composition_arr[$row[csf("determination_id")]];
				$all_data_arr[$row[csf("wo_number")]][$key]["construction"]=$construction_arr[$row[csf("determination_id")]];

				$all_data_arr[$row[csf("wo_number")]][$key]["pi_item_description"]=$row[csf("item_description")];
				$all_data_arr[$row[csf("wo_number")]][$key]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
				$all_data_arr[$row[csf("wo_number")]][$key]["pi_uom"]=$row[csf("uom")];
				$all_data_arr[$row[csf("wo_number")]][$key]["pi_quantity"]+=$row[csf("pi_qnty")];
				$all_data_arr[$row[csf("wo_number")]][$key]["pi_amount"]+=$row[csf("amount")];
			}
		}
		
		
		//var_dump($pi_id_arr);die;
		
		if(!empty($pi_id_arr))
		{
			//echo "sumon";die;
			$pi_id_array=array_chunk($pi_id_arr,999, true);
			//print_r($pi_id_array);die;
			$pi_id_cond="";
			$ji=0;
			foreach($pi_id_array as $key=> $value){
				if($ji==0){
					$pi_id_cond=" and (c.id  in(".implode(",",$value ).")"; 
				}else{
					$pi_id_cond.=" or c.id  in(".implode(",",$value ).")";
				}
				$ji++;
			}
			$pi_id_cond.=")";

			$sql_btb="select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id, c.id as pi_dtls_id, c.work_order_no,c.determination_id, c.item_group, c.item_description, c.uom, c.quantity, c.amount, a.issuing_bank_id, a.etd_date 
			from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_id_cond";
			//echo $pi_id_cond;die;
			
			$btb_result=sql_select($sql_btb);
			$btb_data_array=array();
			foreach($btb_result as $row)
			{
				$wo_lc_pi[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				
				$key=$row[csf("determination_id")];
				
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_id"]=$row[csf("lc_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_number"]=$row[csf("lc_number")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_date"]=$row[csf("lc_date")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_payterm_id"]=$row[csf("payterm_id")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_tenor"]=$row[csf("tenor")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_last_shipment_date"]=$row[csf("last_shipment_date")];
				$all_data_arr[$row[csf("work_order_no")]][$key]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
				
				if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
				{
					$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["lc_value"]+=$row[csf("amount")];
				}
			}
			
			//var_dump($wo_lc_pi);die;
			
			if(count($wo_lc_pi)>0)
			{
				//echo "sumon";die;
				$wo_lc_pi_array=array_chunk($wo_lc_pi,999, true);
				//print_r($pi_id_array);die;
				$wo_lc_pi_cond="";
				$ji=0;
				foreach($wo_lc_pi_array as $key=> $value)
				{
					if($ji==0){
						$wo_lc_pi_cond=" and (c.id  in(".implode(",",$value).")"; 				
					}else{
						$wo_lc_pi_cond.=" or c.id  in(".implode(",",$value).")";				
					}
					$ji++;
				}
				$wo_lc_pi_cond.=")";

				$sql_invoice=" select a.id as inv_id, b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, a.bill_of_entry_no, c.id as pi_dtls_id, c.work_order_no, c.determination_id , c.item_group, c.item_description, c.uom, c.quantity, c.amount, a.eta_date
				from com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_item_details c 
				where a.id = b.import_invoice_id and b.pi_id = c.pi_id $wo_lc_pi_cond and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.current_acceptance_value>0";
				//echo $sql_invoice;//die;
				$invoice_result=sql_select($sql_invoice);
				foreach($invoice_result as $row)
				{
					$inv_id_array[$row[csf("inv_id")]]=$row[csf("inv_id")];
					
					$key=$row[csf("determination_id")];
					
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_id"].=$row[csf("inv_id")].",";
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_no"].=$row[csf("invoice_no")].",";
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_date"]=$row[csf("invoice_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_inco_term"]=$row[csf("inco_term")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_inco_term_place"]=$row[csf("inco_term_place")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_no"]=$row[csf("bill_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_date"]=$row[csf("bill_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_mother_vessel"]=$row[csf("mother_vessel")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_feeder_vessel"]=$row[csf("feeder_vessel")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_container_no"]=$row[csf("container_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_maturity_date"]=$row[csf("maturity_date")];
					$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_eta_date"]=$row[csf("eta_date")];
					
					if($inv_pi_check[$row[csf("pi_dtls_id")]]=="")
					{
						$inv_pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
						$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_value"]+=$row[csf("amount")];
					}
					
					if($inv_pack_check[$row[csf("inv_id")]]=="")
					{
						$inv_pack_check[$row[csf("inv_id")]]=$row[csf("inv_id")];
						$all_data_arr[$row[csf("work_order_no")]][$key]["invoice_pkg_quantity"]+=$row[csf("pkg_quantity")];
					}
					
				}
			}
			
			//var_dump($all_data_arr);die;
		}
		
		
		if(!empty($inv_id_array))
		{
			$sql_pay="select id, invoice_id, payment_date, accepted_ammount, domistic_currency from com_import_payment where status_active=1 and invoice_id in (".implode(",",$inv_id_array).")";
			$pay_result=sql_select($sql_pay);
			$payment_data_arr=array();
			foreach($pay_result as $row)
			{
				$payment_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
				$payment_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				$payment_data_arr[$row[csf("invoice_id")]]["domistic_currency"]+=$row[csf("domistic_currency")];
			}
		}
	}*/
	
	ksort($all_data_arr);
	//echo "<pre>";print_r($all_data_arr);die;

	/*echo "select a.receive_basis, a.booking_id, b.fabric_description_id, sum(b.receive_qnty) as rcv_qnty, sum(b.amount) as rcv_amt 
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b
	where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2) and a.entry_form =17
	group by a.receive_basis, a.booking_id, b.fabric_description_id";*/
	$sql_receive=sql_select("select a.receive_basis, a.booking_id, b.fabric_description_id, sum(b.receive_qnty) as rcv_qnty, sum(b.amount) as rcv_amt 
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b
	where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2) and a.entry_form =17
	group by a.receive_basis, a.booking_id, b.fabric_description_id");
	$recv_data_array=array();
	foreach($sql_receive as $row)
	{
		$key=$row[csf("fabric_description_id")];
		$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["rcv_qnty"]=$row[csf("rcv_qnty")];
		$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$key]["rcv_amt"]=$row[csf("rcv_amt")];
	}
	
	//echo "<pre>";print_r($recv_data_array);die;

	/*if($cbo_item_category_id) $wo_qnty_arr_category_cond = " and  b.item_category_id in ($cbo_item_category_id)" ; else $wo_qnty_arr_category_cond= "";
	$wo_qty_arr=sql_select("select b.requisition_dtls_id, b.item_id as prod_id, sum(b.supplier_order_quantity) as qty 
	from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_name $wo_qnty_arr_category_cond and  b.item_category_id not in (0,1,2,3,12,13,14,24,25,28,30)  and a.wo_basis_id=1 and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.requisition_dtls_id>0 
	group by b.requisition_dtls_id,b.item_id");
	$wo_pipe_array=array();
	foreach($wo_qty_arr as $row)
	{
		$wo_pipe_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]] += $row[csf("qty")];
	}
	
	if ($cbo_item_category_id) $pi_qnty_arr_category_cond = "  and c.item_category_id in($cbo_item_category_id)"; else $pi_qnty_arr_category_cond = "";
	$pi_qty_arr=sql_select("select c.requisition_dtls_id, b.item_prod_id as prod_id, sum(b.quantity) as qty 
	from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c 
	where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.importer_id=$cbo_company_name $pi_qnty_arr_category_cond and c.item_category_id not in (0,4,1,2,3,12,13,14,24,25,28,30) and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by c.requisition_dtls_id,b.item_prod_id"); 
	$pi_pipe_array=array();
	foreach($pi_qty_arr as $row)
	{
		$pi_pipe_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]=$row[csf("qty")];
	}*/
	
	//$indentor_name_array = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40","id","supplier_name");
	ob_start();
	?>
	<div style="width:4420px; margin-left:10px">
		<fieldset style="width:100%;">	 
			<table width="4420" cellpadding="0" cellspacing="0" id="caption">
				<tr>
					<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>  
			</table>
            <table width="4420" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th colspan="11" >Work Order Details</th>
                        <th colspan="11" >PI Details</th>
                        <th colspan="7">L/C Details</th>
                        <th colspan="13">Invoice Details</th>
                        <th colspan="4">Payment Details</th>
                        <th colspan="4">Store details</th>
                    </tr>
                    <tr>
                        <!--880 wo details 11 -->
                        <th width="30">SL No</th>
                        <th width="50">WO No</th>
                        <th width="70">WO Date</th>
                        <th width="140">Supplier</th>
                        <th width="100">Item Category</th>
                        <th width="70">Construction</th>
                        <th width="70">Composition</th> 
                        <th width="60">UOM</th>
                        <th width="80">WO Qnty</th>
                        <th width="70">Unit Price</th>
                        <th width="100">WO Amount </th>
                                                
                        <!--950 pi details 11 -->
						<th width="120">Buyer Name</th>
                        <th width="150">PI No</th>
                        <th width="70">PI Date</th>
                        <th width="140">Supplier</th>
                        <th width="100">Item Category</th>
                        <th width="70">Construction</th> 
                        <th width="70">Composition</th> 
                        <th width="60">UOM</th>
                        <th width="80">PI Qnty</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PI Amount </th>
                        <th width="70">Currency</th>
                        
                        <!--550 lc details 7 -->
                        <th width="70">LC Date</th>
                        <th width="120">LC No</th>
                        <th width="80">Pay Term</th>
                        <th width="50">Tenor</th>
                        <th width="100">LC Amount</th>
                        <th width="70">Shipment Date</th>
                        <th width="80">Expiry Date</th>
                        
                        <!--1180 Invoice details 13 -->
                        <th width="150">Invoice No</th>
                        <th width="70">Invoice Date</th>
                        <th width="80">Incoterm</th>
                        <th width="100">Incoterm Place</th>
                        <th width="80">B/L No</th>
                        <th width="70">BL Date</th>
                        <th width="100">Mother Vassel</th>
                        <th width="100">Feedar Vassel</th>
                        <th width="100">Continer No</th>
                        <th width="80">Pkg Qty</th>
                        <th width="100">Doc Send to CNF</th>
                        <th width="70">NN Doc Received Date</th>
                        <th width="80">Bill Of Entry No</th>
                        
                        <!--290 Payment details 4 -->
                        <th width="70">Maturity Date</th>
                        <th width="70">Maturity Month</th>
                        <th width="70">Payment Date</th>
                        <th width="80">Paid Amount</th>
                        
                        <!--320 MRR details 4 -->
                        <th width="80">MRR Qnty</th>
                        <th width="100">MRR Value</th>
                        <th width="100">Short Value</th>
                        <th>Pipe Line</th>
                    </tr>
                </thead>
            </table>
            <div style="width:4350px; overflow-y:scroll; max-height:300px" id="scroll_body">
			    <table class="rpt_table" border="1" rules="all" width="4420" cellpadding="0" cellspacing="0" id="table_body">
			    <tbody>
                <!-- <tbody id="table_body"> -->
				<?
				$i=1;
				$btb_tem_lc_array=$inv_temp_array=array();
				$total_req_qnty=$total_wo_qnty=$total_wo_amt=$total_wo_balanc=$total_pi_qnty=$total_pi_amt=$total_lc_amt=$total_pkg_qnty=$total_pay_amt=$total_mrr_qnty=$total_mrr_amt=$total_short_amt=$total_pipe_line=0;
				foreach($all_data_arr as $wo_no=>$wo_val)
				{
					foreach($wo_val as $determination_id=>$prod_val)
					{
						//echo $determination_id;
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30" align="center"><p><? echo $i; ?>&nbsp;</p></td>
							<td width="50" align="center" title="<? echo $prod_data_ref; ?>"><p><? echo $prod_val["wo_number_prefix_num"]; ?>&nbsp;</p></td>
                            <td width="70" align="center"><? if($prod_val["wo_date"]!="" &&  $prod_val["wo_date"]!="0000-00-00") echo change_date_format($prod_val["wo_date"]); ?>&nbsp;</td>
                            <td width="140"><p><? echo $prod_val["wo_supplier_id"];?>&nbsp;</p></td>
							<td width="100"><p><? echo $item_category[$prod_val["item_category"]]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $prod_val["construction"]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $prod_val["copmposition"]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $unit_of_measurement[$prod_val["wo_uom"]]; ?>&nbsp;</p></td>
							<td width="80" align="right"><? echo number_format($prod_val["wo_qnty"],2);$total_wo_qnty+=$prod_val["wo_qnty"]; ?></td>
							<td width="70" align="right"><? if($prod_val["wo_amount"]>0 && $prod_val["wo_qnty"]>0) { $wo_rate=$prod_val["wo_amount"]/$prod_val["wo_qnty"]; echo  number_format($wo_rate,2);} else echo "0.00"; ?></td>
                            <td width="100" align="right"><? echo  number_format($prod_val["wo_amount"],2);$total_wo_amt+=$prod_val["wo_amount"]; ?></td>
							
						    <td width="120" title=""><p><? echo $lib_buyer[$prod_val["buyer_id"]]; ?> &nbsp;</p></td>


							<td width="150" title="<? echo $prod_val["pi_work_order_no"]; ?>"><p><? echo implode(",",array_unique(explode(",",chop($prod_val["pi_number"],",")))); ?> &nbsp;</p></td>
							<td width="70" align="center"><? if($prod_val["pi_date"]!="" &&  $prod_val["pi_date"]!="0000-00-00")echo change_date_format($prod_val["pi_date"]); ?>&nbsp;</td>
							<td width="140"><p><? echo $prod_val["pi_supplier_id"];?>&nbsp;</p></td>
							<td width="100"><p><? echo $item_category[$prod_val["item_category"]]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $prod_val["construction"]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $prod_val["copmposition"]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $unit_of_measurement[$prod_val["pi_uom"]]; ?>&nbsp;</p></td>
							<td width="80" align="right"><? echo number_format($prod_val["pi_quantity"],2);$total_pi_qnty+=$prod_val["pi_quantity"]; ?></td>
							<td width="70" align="right"><? if($prod_val["pi_amount"]>0 && $prod_val["pi_quantity"]>0) { $pi_rate=$prod_val["pi_amount"]/$prod_val["pi_quantity"]; echo  number_format($pi_rate,2);} else echo "0.00"; ?></td>
							<td width="100" align="right"><? echo number_format($prod_val["pi_amount"],2);$total_pi_amt+=$prod_val["pi_amount"]; ?></td>
							<td width="70" align="center"><p><? echo $currency[$prod_val["pi_currency_id"]]; ?>&nbsp;</p></td>


							<td width="70" align="center"><? if($prod_val["lc_date"]!="" &&  $prod_val["lc_date"]!="0000-00-00")echo change_date_format($prod_val["lc_date"]); ?>&nbsp;</td>
							<td width="120"><p><? echo $prod_val["lc_number"]; ?> &nbsp;</p></td>
							<td width="80"><p><? echo $pay_term[$prod_val["lc_payterm_id"]]; ?> &nbsp;</p></td>
							<td width="50" align="right"><p><? echo $prod_val["lc_tenor"]; ?> &nbsp;</p></td>
							<td width="100" align="right"><? echo number_format($prod_val["lc_value"],2);$total_lc_amt+=$prod_val["lc_value"]; ?></td>
							<td width="70" align="center"><? if($prod_val["lc_last_shipment_date"]!="" &&  $prod_val["lc_last_shipment_date"]!="0000-00-00") echo change_date_format($prod_val["lc_last_shipment_date"]); ?>&nbsp;</td>
							<td width="80" align="center"><? if($prod_val["lc_expiry_date"]!="" &&  $prod_val["lc_expiry_date"]!="0000-00-00") echo change_date_format($prod_val["lc_expiry_date"]); ?>&nbsp;</td>


							<td width="150" ><p><? echo implode(",",array_unique(explode(",",chop($prod_val["invoice_no"],",")))); ?> &nbsp;</p></td>
							<td width="70" align="center"><? if($prod_val["invoice_date"]!="" &&  $prod_val["invoice_date"]!="0000-00-00") echo change_date_format($prod_val["invoice_date"]); ?>&nbsp;</td>
							<td width="80"><p><? echo $prod_val["invoice_inco_term"]; ?> &nbsp;</p></td>
							<td width="100" ><p><? echo $prod_val["invoice_inco_term_place"]; ?> &nbsp;</p></td>
							<td width="80" ><p><? echo $prod_val["invoice_bill_no"]; ?> &nbsp;</p></td>
							<td width="70" align="center"><? if($prod_val["invoice_bill_date"]!="" &&  $prod_val["invoice_bill_date"]!="0000-00-00") echo change_date_format($prod_val["invoice_bill_date"]); ?>&nbsp;</td>
							<td width="100" ><p><? echo $prod_val["invoice_mother_vessel"]; ?> &nbsp;</p></td>
							<td width="100" ><p><? echo $prod_val["invoice_feeder_vessel"]; ?> &nbsp;</p></td>
							<td width="100" ><p><? echo $prod_val["invoice_container_no"]; ?> &nbsp;</p></td>
							<td width="80" align="right"><? echo number_format($prod_val["invoice_pkg_quantity"],2);$total_pkg_qnty+=$prod_val["invoice_pkg_quantity"]; ?></td>
							<td width="100" align="center"><? if($prod_val["invoice_doc_to_cnf"]!="" &&  $prod_val["invoice_doc_to_cnf"]!="0000-00-00") echo change_date_format($prod_val["invoice_doc_to_cnf"]); ?>&nbsp;</td>
							<td width="70" align="center"></td>
							<td width="80"><p><? echo $prod_val["invoice_bill_of_entry_no"]; ?> &nbsp;</p></td>
							

							<td width="70" align="center"><? if($prod_val["invoice_maturity_date"]!="" &&  $prod_val["invoice_maturity_date"]!="0000-00-00") echo change_date_format($prod_val["invoice_maturity_date"]); ?>&nbsp;</td>
							<td width="70" align="center"><? if($prod_val["invoice_maturity_date"]!="" &&  $prod_val["invoice_maturity_date"]!="0000-00-00") echo change_date_format($prod_val["invoice_maturity_date"]); ?>&nbsp;</td>
							<?
							$all_inv_id=array_unique(explode(",",chop($prod_val["invoice_id"],",")));
							$pay_amt=0;
							foreach($all_inv_id as $inv_id)
							{
								$pay_date=$payment_data_arr[$inv_id]["payment_date"];
								$pay_amt+=$payment_data_arr[$inv_id]["accepted_ammount"];
							}
							?>
							<td width="70" align="center"><? if($pay_date!="" &&  $pay_date!="0000-00-00") echo change_date_format($pay_date); ?>&nbsp;</td>
							<td width="80" align="right"><? echo number_format($pay_amt,2);$total_pay_amt+=$pay_amt; ?></td>
							
	
							<?
							//$rcv_qnty=$rcv_value
							$pi_id_all=array_unique(explode(",",chop($prod_val["pi_id"],",")));
							$wo_mst_id_all=array_unique(explode(",",chop($prod_val["wo_mst_id"],",")));
							
							$recv_pi_wo_req="";$rcv_qnty="";$rcv_value="";
							foreach($pi_id_all as $val)
							{
								//echo $val.'=='; 
								$rcv_qnty+=$recv_data_array[1][$val][$determination_id]["rcv_qnty"];
								$rcv_value+=$recv_data_array[1][$val][$determination_id]["rcv_amt"];
								$recv_pi_wo_req.=$val.",";
							}
							$recv_pi_wo_req=chop($recv_pi_wo_req,",");
							if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**1";
							if($rcv_qnty=="")
							{
								$recv_pi_wo_req="";
								foreach($wo_mst_id_all as $val)

								{
									$rcv_qnty+=$recv_data_array[2][$val][$determination_id]["rcv_qnty"];
									$rcv_value+=$recv_data_array[2][$val][$determination_id]["rcv_amt"];
									$recv_pi_wo_req.=$val.",";
								}
								$recv_pi_wo_req=chop($recv_pi_wo_req," , ");
								if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**2";
							}
							$pipe_pi_qnty="";$pipe_wo_qnty="";
							/*foreach($all_req_dtls_id_arr as $val)
							{
								$pipe_wo_qnty+=$wo_pipe_array[$val][$product_id];
								$pipe_pi_qnty+=$pi_pipe_array[$val][$product_id];
							}*/
							?>
							<td width="80" align="right"><a href="##" onClick="openmypage_popup('<? echo $recv_pi_wo_req; ?>','<? echo $determination_id; ?>','Receive Info','receive_popup_woven');" > <? echo number_format($rcv_qnty,2); $total_mrr_qnty+=$rcv_qnty; ?> </a></td>

							<td width="100" align="right">
								<?
									$rcv_value /= $prod_val['currency_rate'];
									echo number_format($rcv_value,2);
									$total_mrr_amt+=$rcv_value; ?>									
							</td>
							<td align="right" title="Wo Value-Receive Value"><? $short_value=$prod_val["wo_amount"]-$rcv_value; echo number_format($short_value,2);  $total_short_amt+=$short_value; ?></td>
							<?
							$pipe_line=$prod_val["wo_qnty"]-$rcv_qnty;
							?>
							<td align="right" title="Wo Qty-MRR Qty"><? echo number_format($pipe_line,2); $total_pipe_line+=$pipe_line;?></td>
						</tr>
						<?
						$i++;
					}
					
				}
				?>
				</tbody>
				</table>

			</div>
			<table class="rpt_table" border="1" rules="all" width="4420" cellpadding="0" cellspacing="0">
				<tfoot>
	        	<tr bgcolor="#CCCCCC">
					<!--1110 wo details-->
					<td width="30"></td>
					<td width="50"></td>
					<td width="70"></td>
					<td width="140"></td>
	                <td width="100"></td>
					<td width="70"></td>
					<td width="70"></td>
					<td width="60"></td>
					<td width="80" align="right"><? //echo number_format($total_wo_qnty,2); ?></td>
					<td width="70"></td>
					<td width="100" align="right"><? echo number_format($total_wo_amt,2); ?></td>

					<!--840 pi details-->
					<td width="120"></td>
					<td width="150"></td>
					<td width="70"></td>
					<td width="140"></td>
					<td width="100"></td>
					<td width="70"></td>
					<td width="70"></td>
					<td width="60"></td>
					<td width="80" align="right"><? //echo number_format($total_pi_qnty,2); ?></td>
					<td width="70"></td>
					<td width="100" align="right"><? echo number_format($total_pi_amt,2); ?></td>
					<td width="70"></td>

					<!--550 lc details-->
					<td width="70"></td>
					<td width="120"></td>
					<td width="80"></td>
	                <td width="50"></td>
					<td width="100" align="right"><? echo number_format($total_lc_amt,2); ?></td>
					<td width="70"></td>
					<td width="80"></td>
					
					<!--1100 Invoice details-->
					<td width="150"></td>
					<td width="70"></td>
					<td width="80"></td>
					<td width="100"></td>
					<td width="80"></td>
					<td width="70"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="80" align="right"><? //echo number_format($total_pkg_qnty,2); ?></td>
					<td width="100" ></td>
					<td width="70"></td>
					<td width="80"></td>
					
					<!--290 Payment details-->
					<td  width="70"></td>
					<td  width="70"></td>
					<td  width="70"></td>
					<td  width="80" align="right"><? echo number_format($total_pay_amt,2); ?></td>
					
					<!--340 MRR details-->
					<td width="80" align="right"><? //echo number_format($total_mrr_qnty,2); ?></td>
					<td width="100" align="right"><? echo number_format($total_mrr_amt,2); ?></td>
					<td width="100" align="right"><? echo number_format($total_short_amt,2); ?></td>
					<td align="right"><? //echo number_format($total_pipe_line,2); ?></td>
				</tr>
	        	</tfoot>	
			</table>
		</fieldset>
	</div>
	<?
	
		
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
	echo "$total_data####$filename####3";
	exit();
}

if($action=="receive_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_pi_req=str_replace("'","",$wo_pi_req);
	$wo_pi_req_arr=explode("**",$wo_pi_req);
	$wo_pi_req_no=$wo_pi_req_arr[0];
	$rcv_basis=$wo_pi_req_arr[1];
	?>
	<script>
	function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
	</script>	
	<p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
    <br />
    <div id="report_container" align="center" style="width:700px">
	<fieldset style="width:700px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="700" cellpadding="0" cellspacing="0">
             	<thead>
                    <th width="50">Product Id</th>
                    <th width="140">Item Category</th>
                    <th width="100">Item Group</th>
                    <th width="100">Item Sub-group</th>
                    <th width="200">Item Description</th>
                    <th>Item Size</th>
                </thead>
                <tbody>
                <?
				$item_group_arr=return_library_array("select id, item_name from  lib_item_group","id","item_name");
				$sql="SELECT id,item_category_id,item_group_id,sub_group_name, case when item_category_id =1 then product_name_details else item_description end as item_description, item_size from  product_details_master where id in($prod_id)";
				$result=sql_select($sql);
				foreach($result as $row)  
				{
					?>
					<tr>
						<td align="center"><? echo $row[csf('id')]; ?></td>
						<td><p><? echo $item_category[$row[csf('item_category_id')]]; ?></p></td>
						<td><? echo $item_group_arr[$row[csf('item_group_id')]]; ?></td>
						<td><? echo $row[csf('sub_group_name')]; ?></td>
						<td><? echo $row[csf('item_description')]; ?></td>
                        <td><? echo $row[csf('item_size')]; ?></td>
					</tr>
					<?
				}
				?>
                </tbody>   
            </table>
            <br />
            <table class="rpt_table" border="1" rules="all" width="700" cellpadding="0" cellspacing="0">
                <thead>
                    <th width="40">SL</th>
                    <th width="120">MRR No.</th>
                    <th width="80">Receive Date</th>
                    <th width="60">UOM</th>
                    <th width="70">Qty</th>
                    <th width="70">Rate</th>
                    <th width="80">Value</th>
                    <th>Remarks</th>
                </thead>
                <tbody>
                <? 
                    $i=1; $total_qty=0; 
                    $sql_rcv="select a.recv_number, a.receive_date, a.remarks, b.cons_uom, b.order_qnty as qnty, b.order_rate, b.order_amount as rcv_amt 
					from inv_receive_master a, inv_transaction b 
					where a.id=b.mst_id and a.receive_basis=$rcv_basis and a.booking_id in($wo_pi_req_no) and b.prod_id in($prod_id) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					//echo $sql_rcv;
                    $result_rcv=sql_select($sql_rcv);
                    foreach($result_rcv as $row)  
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td><? echo $i; ?></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('qnty')],2);  $total_qty += $row[csf('qnty')]; ?></td>
                            <td align="right"><? echo number_format($row[csf('order_rate')],2);  ?></td>
							<td align="right"><? echo number_format($row[csf('rcv_amt')],2); $total_amt+=$row[csf('rcv_amt')]; ?></td>
                            <td><? echo $row[csf('remarks')]; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                </tbody>
                
                 <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($total_qty,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($total_amt,2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
    <br/>
        <?
			$rcv_return_sql=sql_select("select a.issue_number, a.issue_date, b.cons_uom, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt 
			from inv_issue_master a, inv_transaction b, product_details_master c 
			where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prod_id) and b.pi_wo_batch_no in($wo_pi_req_no) and c.entry_form<>24 group by a.issue_number, a.issue_date, b.cons_uom");
			?>
            <table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="130">MRR No.</th>
                        <th width="70">Return Date</th>
                        <th width="50">UOM</th>
                        <th>Return Qty</th>
                    </tr>
                </thead>
                <tbody>
					<?
					$i=1;
					foreach($rcv_return_sql as $row)
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                            <td align="center"><? echo $i;?></td>
                            <td><p><? echo $row[csf("issue_number")]; ?></p></td>
                            <td align="center"><p><? echo change_date_format($row[csf("issue_date")]); ?></p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                            <td align="right"><? echo number_format($row[csf("qnty")],2); $total_rtn+=$row[csf("qnty")]; ?></td>
                        </tr>
                        <?
						$i++;$k++;
					}
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total Rtn</th>
                        <th><? echo number_format($total_rtn,2); ?></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Balance</th>
                        <th><? $balance_qnty=($total_qty-$total_rtn); echo number_format($balance_qnty,2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
	<?
    exit();

}

if($action=="receive_popup_trims")
{
	
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_pi_req=str_replace("'","",$wo_pi_req);
	$wo_pi_req_arr=explode("**",$wo_pi_req);
	$wo_pi_req_no=$wo_pi_req_arr[0];
	$rcv_basis=$wo_pi_req_arr[1];
	$prod_data=explode("__",$prod_id);
	?>
	<script>
	function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
	</script>	
	<p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
    <br />
    <div id="report_container" align="center" style="width:780px">
		<fieldset style="width:780px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="780" cellpadding="0" cellspacing="0">
                <thead>
					<tr>
						<th colspan="9" align="center">Received Details</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="120">MRR No.</th>
						<th width="80">Receive Date</th>
						<th width="80">Challan No.</th>
						<th width="60">UOM</th>
						<th width="70">Qty</th>
						<th width="70">Rate</th>
						<th width="80">Value</th>
						<th>Remarks</th>
					</tr>

                </thead>
                <tbody>
                <?
                    $i=1; $total_qty=0; 
                    $sql_rcv="SELECT a.recv_number, a.receive_date, a.remarks, a.challan_no, b.cons_uom, b.receive_qnty as qnty, b.rate as order_rate, b.amount as rcv_amt 
					from inv_receive_master a, inv_trims_entry_dtls b 
					where a.id=b.mst_id and a.receive_basis=$rcv_basis and b.booking_id in($wo_pi_req_no) and a.entry_form=24 and b.item_group_id=$prod_data[0] and trim(REGEXP_REPLACE(b.ITEM_DESCRIPTION, '\s{2,}', ' '))=trim(REGEXP_REPLACE('".str_replace("'","",$prod_data[1])."', '\s{2,}', ' '))  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					//echo $sql_rcv;// die;
                    $result_rcv=sql_select($sql_rcv);
                    foreach($result_rcv as $row)  
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td><? echo $i; ?></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td align="center"><? echo $row[csf('challan_no')]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('qnty')],2);  $total_qty += $row[csf('qnty')]; ?></td>
                            <td align="right"><? echo number_format($row[csf('order_rate')],4);  ?></td>
							<td align="right"><? echo number_format($row[csf('rcv_amt')],2); $total_amt+=$row[csf('rcv_amt')]; ?></td>
                            <td><? echo $row[csf('remarks')]; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total</td>
                        <td align="right"><? echo number_format($total_qty,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($total_amt,2); ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
            <table class="rpt_table" border="1" rules="all" width="780" cellpadding="0" cellspacing="0">
                <thead>
					<tr>
						<th colspan="7" align="center">Received Return Details</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="100">Prod. ID</th>
						<th width="80">Item Group</th>
						<th width="80">Return ID</th>
						<th width="70">Return Date</th>
						<th width="70">Item Desc.</th>
						<th width="80">Return Qnty.</th>
						<!-- <th width="80">Challan No.</th>
						<th>Remarks</th> -->
					</tr>

                </thead>
                <tbody>
                	<?
					$item_group_arr = return_library_array("SELECT ID, ITEM_NAME from LIB_ITEM_GROUP where STATUS_ACTIVE=1 and IS_DELETED=0","ID","ITEM_NAME");
					$i=1; $total_rtrn_qty=0; 
					$sql_rcv_rtn="SELECT a.ISSUE_NUMBER, a.ISSUE_DATE, b.PROD_ID, b.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, b.ISSUE_QNTY
					from INV_ISSUE_MASTER a, INV_TRIMS_ISSUE_DTLS b, PRODUCT_DETAILS_MASTER c
					where a.ID=b.MST_ID and b.prod_id=c.id and c.ITEM_CATEGORY_ID=4 and a.ENTRY_FORM=49 and b.BOOKING_ID in($wo_pi_req_no) and b.ITEM_GROUP_ID=$prod_data[0] and trim(REGEXP_REPLACE(c.ITEM_DESCRIPTION, '\s{2,}', ' '))=trim(REGEXP_REPLACE('".str_replace("'","",$prod_data[1])."', '\s{2,}', ' ')) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0";
					// echo $sql_rcv_rtn;die;
					$result_rcv_rtn=sql_select($sql_rcv_rtn);
					foreach($result_rcv_rtn as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtrn_tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="rtrn_tr_<? echo $i; ?>">
							<td><? echo $i; ?></td>
							<td align="center"><p><? echo $row['PROD_ID']; ?></p></td>
							<td align="center"><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?></td>
							<td align="center"><? echo $row['ISSUE_NUMBER']; ?></td>
							<td align="center"><? echo change_date_format($row['ISSUE_DATE'], 'd-m-Y'); ?></td>
							<td align="center"><? echo $row['ITEM_DESCRIPTION'];  ?></td>
							<td align="right"><? echo number_format($row['ISSUE_QNTY'],2);  $total_rtrn_qty += $row['ISSUE_QNTY']; ?></td>
						</tr>
						<?
						$i++;
					}
					?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                    	<td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($total_rtrn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="6" align="right">Balance</td>
                        <td align="right"><? echo number_format($total_qty-$total_rtrn_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
	<?
    exit();
}

if($action=="receive_popup_woven")
{
	
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_pi_req=str_replace("'","",$wo_pi_req);
	$wo_pi_req_arr=explode("**",$wo_pi_req);
	$wo_pi_req_no=$wo_pi_req_arr[0];
	$rcv_basis=$wo_pi_req_arr[1];
	$fabric_description_id=$prod_id;
	?>
	<script>
	function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
	</script>	
	<p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
    <br />
    <div id="report_container" align="center" style="width:800px">
	<fieldset style="width:800px; margin-left:10px">
        <table class="rpt_table" border="1" rules="all" width="800" cellpadding="0" cellspacing="0">
            <thead>
                <th width="40">SL</th>
                <th width="60">Product ID</th>
                <th width="120">MRR No.</th>
                <th width="80">Receive Date</th>
                <th width="60">UOM</th>
                <th width="70">Qty</th>
                <th width="70">Rate</th>
                <th width="80">Value</th>
                <th width="120">Challan No</th>
                <th>Remarks</th>
                
            </thead>
            <tbody>
            <? 
                $i=1; $total_qty=0; 
                $sql_rcv="select a.recv_number, a.receive_date, a.remarks, b.uom, b.receive_qnty as qnty, b.rate as order_rate, b.amount as rcv_amt, b.prod_id, a.challan_no 
				from inv_receive_master a, pro_finish_fabric_rcv_dtls b 
				where a.id=b.mst_id and a.receive_basis=$rcv_basis and a.booking_id in($wo_pi_req_no) and a.entry_form=17 and b.fabric_description_id=$fabric_description_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				//echo $sql_rcv;die;
                $result_rcv=sql_select($sql_rcv);
                foreach($result_rcv as $row)  
                {
                   
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td><? echo $i; ?></td>
						<td><? echo $row[csf('prod_id')]; ?></td>
						<td><p><? echo $row[csf('recv_number')]; ?></p></td>
						<td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2);  $total_qty += $row[csf('qnty')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('order_rate')],2);  ?></td>
						<td align="right"><? echo number_format($row[csf('rcv_amt')],2); $total_amt+=$row[csf('rcv_amt')]; ?></td>
                        <td align="center"><? echo $row[csf('challan_no')]; ?></td>
                        <td><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?
                    $i++;
				}
				?>
            </tbody>
            
             <tfoot>
                <tr class="tbl_bottom">
                    <td colspan="5" align="right">Total</td>
                    <td align="right"><? echo number_format($total_qty,2); ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_amt,2); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
	<?
    exit();
}

if($action=="pipe_line_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$prod_id=str_replace("'","",$prod_id);
	$wo_pi_req=str_replace("'","",$wo_pi_req);
	$wo_pi_req_arr=explode("**",$wo_pi_req);
	$wo_pi_req_no=$wo_pi_req_arr[0];
	$rcv_basis=$wo_pi_req_arr[1];
	
	$product_sql=sql_select("select id,  item_category_id, item_group_id, sub_group_name, item_description, product_name_details, item_size from product_details_master where id=$prod_id");
	$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	
	?>
	<script>
	function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
	</script>	
	<p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
    <br />
    <div id="report_container" style="width:700px">
    <table width="630" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="50">Product Id</th>
                <th width="120">Item Category</th>
                <th width="100">Item Group</th>
                <th width="100">Item Sub-group</th>
                <th width="150">Item Description</th>
                <th >Item Size</th>
            </tr>
        </thead>
        <tbody>
        	<tr>
            	<td align="center"><p><? echo $product_sql[0][csf("id")]; ?>&nbsp;</p></td>
                <td><p><? echo $item_category[$product_sql[0][csf("item_category_id")]]; ?>&nbsp;</p></td>
                <td><p><? echo $item_group_name; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("sub_group_name")]; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("item_description")]; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("item_size")]; ?>&nbsp;</p></td>
            </tr>
        </tbody>
    </table>
    <br />
    <table width="680" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="70">WO/PI Date</th>
                <th width="100">WO/PI No</th>
                <th width="30">Type</th>
                <th width="70">Pay Mode</th>
                <th width="70">UOM</th>
                <th width="80">WO/PI Qty.</th>
                <th width="80">Rcv. Qnty</th>
                <th width="80">Balance</th>
                <th >Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		$rcv_qnty_array=return_library_array("select a.booking_id, sum(b.cons_quantity) as cons_quantity from  inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.prod_id=$prod_id and b.status_active=1 group by a.booking_id","booking_id","cons_quantity");
		
		if($rcv_basis==1)
		{
			$details_sql="select b.id as wo_po_id, b.pi_number as wo_po_no, b.pi_date as wo_po_date, 0 as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.quantity) as wo_po_qnty, 2 as type 
			from com_pi_master_details b, com_pi_item_details c 
			where b.id=c.pi_id and b.id in($wo_pi_req_no) and c.item_prod_id=$prod_id and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			group by b.id, b.pi_number, b.pi_date";
		}
		else if($rcv_basis==2)
		{
			$details_sql="select b.id as wo_po_id, b.wo_number as wo_po_no, b.wo_date as wo_po_date, b.pay_mode as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.supplier_order_quantity) as wo_po_qnty, 1 as type 
			from wo_non_order_info_mst b,  wo_non_order_info_dtls c 
			where b.id=c.mst_id and b.id in($wo_pi_req_no) and c.item_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and b.pay_mode<>2 and c.is_deleted=0 
			group by b.id, b.wo_number, b.wo_date, b.pay_mode";
		}
		
		
		/*$details_sql="select b.id as wo_po_id, b.wo_number as wo_po_no, b.wo_date as wo_po_date, b.pay_mode as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.supplier_order_quantity) as wo_po_qnty, 1 as type from wo_non_order_info_mst b,  wo_non_order_info_dtls c where b.id=c.mst_id and c.item_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and b.item_category in (5,6,7) and b.pay_mode<>2 and c.is_deleted=0 group by b.id, b.wo_number, b.wo_date, b.pay_mode
		union all
		select b.id as wo_po_id, b.pi_number as wo_po_no, b.pi_date as wo_po_date, 0 as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.quantity) as wo_po_qnty, 2 as type from com_pi_master_details b, com_pi_item_details c where b.id=c.pi_id and b.item_category_id in (5,6,7) and c.item_prod_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, b.pi_number, b.pi_date";*/
		//echo $details_sql;
		$sql_result=sql_select($details_sql);
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$rcv_qnty=$rcv_qnty_array[$row[csf("wo_po_id")]];
			$balance=$row[csf("wo_po_qnty")]-$rcv_qnty;
			if($row[csf("type")]==1) $type="WO"; else $type="PI";
			
        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                <td><p><? if($row[csf("wo_po_date")]!="" && $row[csf("wo_po_date")]!="0000-00-00") echo change_date_format($row[csf("wo_po_date")]); ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("wo_po_no")]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $type; ?>&nbsp;</p></td>
                <td><p><? echo $pay_mode[$row[csf("wo_po_mode")]]; ?>&nbsp;</p></td>
                <td><p><? echo $unit_of_measurement[$row[csf("wo_po_uom")]]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($row[csf("wo_po_qnty")],0); $total_wo_qnty+=$row[csf("wo_po_qnty")]; ?></p></td>
                <td align="right"><p><? echo number_format($rcv_qnty,0); $total_rcv_qnty+=$rcv_qnty; ?></p></td>
                <td align="right"><p><? echo number_format($balance,0); $total_bal_qnty+=$balance;  ?></p></td>
                <td><p><? echo $row[csf("")]; ?>&nbsp;</p></td>
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >Total</th>
                <th ><? echo number_format($total_wo_qnty,0); ?></th>
                <th ><? echo number_format($total_rcv_qnty,0); ?></th>
                <th ><? echo number_format($total_bal_qnty,0); ?></th>
                <th >&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    </div>
    <?
}

disconnect($con);
?>
