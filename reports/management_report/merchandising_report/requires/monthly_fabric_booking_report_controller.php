<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
$department_library=return_library_array( "select id,department_name from lib_department", "id", "department_name"  );


if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}
if ($action=="load_drop_down_suplier")
{
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 100, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "load_drop_down( 'requires/monthly_fabric_booking_report_controller',this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_location', 'sup_location_td' )",0,"" );
	}
	else{
	   echo create_drop_down( "cbo_supplier_name", 100, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "load_drop_down( 'requires/monthly_fabric_booking_report_controller',this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_location', 'sup_location_td' )",0 );

	}
	exit();
}
if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==5 || $data[1]==3)
	{
		echo create_drop_down( "cbo_supplier_location", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data[0] order by location_name","id,location_name", 1, "-- Select Supp Location --", $selected, "","" );   
	} 
	else
	{
		echo  create_drop_down( "cbo_supplier_location", 100, $blank_array,"", 1, "-- Select Supp Location --", "", "","" );
	} 	
	exit();
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
        function js_set_value(str)
        {
            $("#hide_job_no").val(str);
            parent.emailwindow.hide(); 
        }
    </script>
<?
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)=$cbo_year";
			$year_field="YEAR(insert_date)";
		}
		else
		{
			$year_cond=" and to_char(insert_date,'YYYY')=$cbo_year";	
			$year_field="to_char(insert_date,'YYYY')";
		}
	}
	else $year_cond="";

	$arr=array (2=>$company_library,3=>$buyer_arr);
	$sql= "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a where a.company_name=$company_id $buyer_cond $year_cond order by a.id";
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","320",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}


if($action=="style_refarence_surch")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
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
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'style_refarence_surch_list_view', 'search_div', 'monthly_fabric_booking_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

//style search------------------------------//
if($action=="style_refarence_surch_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_style_ref_no,$txt_style_ref_id,$txt_style_ref)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	
	
	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a where a.company_name=$company $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by a.id  desc"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","200",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}
if($action=="work_order_popup")
{

	echo load_html_head_contents("Work Order No Info", "../../../../", 1, 1,'','','');
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
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );				
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

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" width="100%" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                   
                    <th>Booking Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                         
                       <!--cbo_year_selection-->
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_style_ref; ?>', 'work_order_list_view', 'search_div', 'monthly_fabric_booking_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="work_order_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$start_date,$end_date,$cbo_year,$txt_style_ref)=explode('**',$data);
	?>
    <script>
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_year=str_replace("'","",$cbo_year);
	//echo $cbo_year.'dd';
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(b.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year";	
		}
	}
	
	/*if($search_type==1 && $search_value!=''){
		$search_con=" and a.po_number like('%$search_value')";	
	}
	elseif($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value')";		
	}
	elseif($search_type==3 && $search_value!=''){
		$search_con=" and a.job_no_mst like('%$search_value')";		
	}*/
	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and a.booking_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	
	
	
	if($buyer!=0) $buyer_cond="and a.buyer_id=$buyer"; else $buyer_cond="";
	if($txt_style_ref!="")
	{
		if($db_type==0) $style_cond="and b.job_no_prefix_num in($txt_style_ref)  "; 
		else $style_cond="and b.job_no_prefix_num in($txt_style_ref)  ";
	}
	else $style_cond="";
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	
	$arr=array (1=>$buyer_arr,3=>$pay_mode,9=>$item_category,10=>$suplier,11=>$approved,12=>$yes_no);
	
	//echo $style_cond."jahid";die;
	$sql = "select a.id,a.buyer_id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.pay_mode from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type in(1,4) and a.company_id=$company $buyer_cond  $date_cond and a.status_active=1  and b.status_active=1 group by a.id,a.buyer_id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.pay_mode order by a.id desc"; 
	 //echo $sql;
	echo create_list_view("list_view", "Booking No,Buyer,Booking Date,Pay Mode","150,100,100,100","500","150",0, $sql , "js_set_value", "id,booking_no_prefix_num", "", 1, "0,buyer_id,0,pay_mode", $arr, "booking_no,buyer_id,booking_date,pay_mode", "","","0,0,3,0,0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_order_id_no;?>';
	var style_id='<? echo $txt_wo_id;?>';
	var style_des='<? echo $txt_wo_no;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
			//alert(str_ref);
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
	
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$txt_wo_id=str_replace("'","",$txt_wo_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_internal_ref=str_replace("'","",$txt_internal_ref);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$cbo_year_id=str_replace("'","",$cbo_year_selection);
	
	$cbo_pay_mode=str_replace("'","",$cbo_pay_mode);
	$cbo_supplier_name=str_replace("'","",$cbo_supplier_name);
	$cbo_supplier_location=str_replace("'","",$cbo_supplier_location);
	//cbo_year_id
	$cbo_year=str_replace("'","",$cbo_year_selection);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond=" $year_field_con=$cbo_year"; else $year_cond="";
	}
	
	if($cbo_pay_mode!=0) $pay_mode_cond="and a.pay_mode=$cbo_pay_mode ";else $pay_mode_cond="";
	if($cbo_supplier_name!=0) $supplier_cond="and a.supplier_id=$cbo_supplier_name ";else $supplier_cond="";
	if($cbo_supplier_location!=0) $supplier_loc_cond="and a.supplier_location_id =$cbo_supplier_location ";else $supplier_loc_cond="";
	if($cbo_company_name!=0) $company_name_cond="and a.company_id in($cbo_company_name) ";else $company_name_cond="";
	if($txt_internal_ref!="") $internal_ref_cond="and b.grouping='$txt_internal_ref' ";else $internal_ref_cond="";
	
	// echo $internal_ref_cond."=>".$txt_internal_ref;
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
	}
	
	$booking_date_cond="";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$booking_date_cond="and a.booking_date between '$txt_date_from' and '$txt_date_to' ";
	}
	
	
	$job_no_cond="";
	if(trim($style_ref_id)!="") $job_no_cond="and d.id  in($style_ref_id)";
	
	$job_no_cond2="";
	if(trim($txt_style_ref)!="") $job_no_cond2="and d.job_no_prefix_num  in($txt_style_ref)";
	
	$wo_order_cond="";
	if($txt_wo_id!="") $wo_order_cond="and a.id in($txt_wo_id)";
	$wo_order_no_cond="";
	if($txt_wo_no!="") $wo_order_no_cond="and a.booking_no_prefix_num in($txt_wo_no)";
	$wo_type_cond="";
	if($cbo_search_type==1) //Sample
	{
		$wo_type_cond="and a.booking_type in(4)";
	}
	else if($cbo_search_type==2) //short
	{
		$wo_type_cond="and a.booking_type in(1) and a.is_short=1";
	}
	else if($cbo_search_type==3) //Main
	{
		$wo_type_cond="and a.booking_type in(1) and a.is_short=2";
	}
	else
	{
		$wo_type_cond="and a.booking_type in(1,4)";
	}
	
	
		$sql_wo="select a.buyer_id,a.company_id,a.supplier_location_id as location_id,a.supplier_id,a.booking_date,a.pay_mode,a.booking_no, a.job_no,b.id as po_id, b.job_no_mst,c.responsible_dept,c.responsible_person,c.reason, c.fin_fab_qnty, c.grey_fab_qnty,b.grouping as internal_ref_no	
		from wo_booking_mst a,wo_booking_dtls c, wo_po_break_down b,wo_po_details_master d
		where a.booking_no=c.booking_no and c.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no and b.id=c.po_break_down_id  and c.grey_fab_qnty>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $wo_type_cond $buyer_id_cond $job_no_cond2 $job_no_cond $wo_order_no_cond $wo_order_cond $booking_date_cond $pay_mode_cond $supplier_cond $year_cond $supplier_loc_cond  $company_name_cond $internal_ref_cond order by a.job_no, b.pub_shipment_date, b.id";
	
	
	$sql_wo_result=sql_select($sql_wo);
	$result_data_arr=$result_job_wise=array();$all_wo_no=""; $JobArr=array();
	foreach($sql_wo_result as $row)
	{
		if($all_wo_no=="") $all_wo_no=$row[csf("booking_no")]; else $all_wo_no.=",".$row[csf("booking_no")];
		//Company Wise-----Start---------
		$company_wise_arr[$row[csf("company_id")]]["fin_fab_qnty"]+=$row[csf("fin_fab_qnty")];
		$company_wise_arr[$row[csf("company_id")]]["grey_fab_qnty"]+=$row[csf("grey_fab_qnty")];
		$company_wise_arr[$row[csf("company_id")]]["booking_no"].=$row[csf("booking_no")].',';
		
		//Buyer Month Wise-----Start---------
		$mon_year=date("Y-M",strtotime($row[csf("booking_date")]));
		$month_arr[$mon_year]=$mon_year;
		$buyer_wise_arr[$row[csf("buyer_id")]]["booking_no"].=$row[csf("booking_no")].',';
		$buyer_wise_data_arr[$mon_year][$row[csf("buyer_id")]]["fin_fab_qnty"]+=$row[csf("fin_fab_qnty")];
		$buyer_wise_data_arr[$mon_year][$row[csf("buyer_id")]]["grey_fab_qnty"]+=$row[csf("grey_fab_qnty")];
		
		//Supplier -Location Month Wise-----Start---------\
		if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
		{
			$supplier_com_id=$company_library[$row[csf("supplier_id")]];
		}
		else
		{
			$supplier_com_id=$supplier_library[$row[csf("supplier_id")]];
		}
		
		$mon_year=date("Y-M",strtotime($row[csf("booking_date")]));
		$month_arr[$mon_year]=$mon_year;
		$supplier_location_wise_arr[$row[csf("supplier_id")]][$row[csf("location_id")]]["supplier_com"]=$supplier_com_id;
		$supplier_location_wise_arr[$row[csf("supplier_id")]][$row[csf("location_id")]]["booking_no"].=$row[csf("booking_no")].',';
		$supplier_location_wise_data_arr[$mon_year][$row[csf("supplier_id")]][$row[csf("location_id")]]["fin_fab_qnty"]+=$row[csf("fin_fab_qnty")];
		$supplier_location_wise_data_arr[$mon_year][$row[csf("supplier_id")]][$row[csf("location_id")]]["grey_fab_qnty"]+=$row[csf("grey_fab_qnty")];
		
		//WO  No Wise-----Start---------
		$wo_wise_arr[$row[csf("booking_no")]]['booking_no'].=$row[csf("booking_no")].',';
		$wo_wise_arr[$row[csf("booking_no")]]['fin_fab_qnty']+=$row[csf("fin_fab_qnty")];
		$wo_wise_arr[$row[csf("booking_no")]]['grey_fab_qnty']+=$row[csf("grey_fab_qnty")];
		$wo_wise_arr[$row[csf("booking_no")]]['internal_ref_no']=$row[csf("internal_ref_no")];
	//	echo $row[csf("grey_fab_qnty")].'d';
	
		$wo_wise_arr[$row[csf("booking_no")]]['location_id']=$row[csf("location_id")];
		
		
		
		if($row[csf("supplier_id")]>0)
		{
			$wo_wise_arr[$row[csf("booking_no")]]['supplier_id']=$supplier_com_id;
			//echo $supplier_com_id.'T';
		}
		
		$wo_wise_arr[$row[csf("booking_no")]]['booking_date']=$row[csf("booking_date")];
		$wo_wise_arr[$row[csf("booking_no")]]['buyer_id']=$row[csf("buyer_id")];
		$wo_wise_arr[$row[csf("booking_no")]]['pay_mode']=$row[csf("pay_mode")];
		$responsible_dept=explode(",",$row[csf("responsible_dept")]);
		foreach($responsible_dept as $depart_id)
		{
			if($depart_id!='')
			{
			//$department=department_library[$depart_id];
			//echo $depart_id.'<br>';
			$wo_wise_arr_department_arr[$row[csf("booking_no")]]['responsible_dept'].=$department_library[$depart_id].',';
			}
		}
		//$wo_wise_arr[$row[csf("booking_no")]]["responsible_dept"]=$row[csf("responsible_dept")];
		if($row[csf("responsible_person")]!='')
		{
		$wo_wise_arr[$row[csf("booking_no")]]['responsible_person'].=$row[csf("responsible_person")].',';
		}
		if($row[csf("reason")]!='')
		{
		$wo_wise_arr[$row[csf("booking_no")]]['reason'].=$row[csf("reason")].',';
		
		}
	}
	//print_r($wo_wise_arr_department_arr);

	$all_wo_no=implode(",",array_unique(explode(",",$all_wo_no)));
	
	if(empty($all_wo_no))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Work Order not found.</h></div>'; die;
	}
	
	$tbl_width_main=900;
	$tbl_width_com=430;
	$tbl_width_wo=1120;
	//$tbl_width_buyer=630;
	$rowcount=0;
	foreach($month_arr as $mon_key=>$val)
	{
		$rowcount+=count($val);
	}
		 $rowb=$rowcount*1;
		 $tbl_width_buyer=250+($rowcount*100)+$rowb;
		 $tbl_width_supplier=400+($rowcount*100)+$rowb;
		// echo  $tbl_width_buyer;
	
	ob_start();
	?>
        <div style="width:<? echo $tbl_width_main;?>px; margin-left:10px;">
			<fieldset style="width:100%;">	
             <table width="<? echo $tbl_width_main;?>">
                <tr>
                    <td align="center" width="100%" colspan="70" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)].'<br/>'.$report_title.'<br>';
					if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo 'From : '.change_date_format(str_replace("'","",$txt_date_from)).' To : '.change_date_format(str_replace("'","",$txt_date_to)) 
					 ?></td>
                </tr>
            </table>
            <table width="<? echo $tbl_width_com;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
			<caption><strong style="float:left">Company Wise Summary</strong> </caption>
                <thead>
                	<tr style="font-size:13px">
                        <th width="30">SL</th>
                        <th width="120">Company</th>
                        <th width="70" >Total WO</th>
                        <th width="100">Finish Qty (Kg)</th>
                        <th width="">Grey Qty (Kg)</th>
                    </tr>
                </thead>
				<tbody>
				<?
				$k=1;$total_fin_qty=$total_grey_fab_qnty=0;
				foreach($company_wise_arr  as $company_key=>$val)
				{
				  $bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
				  $booking_no=rtrim($val['booking_no'],',');
				 $tot_booking=array_unique(explode(",",$booking_no));
				 // echo $booking_no;
				  $booking_no_tot=count($tot_booking);
				?>
					 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trcom_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trcom_<? echo $k; ?>" style="font-size:12px">
						 <td width="30" align="center"><? echo $k;?></td>
                   		 <td width="120"><p><? echo  $company_library[$company_key]; ?></p></td>
						 <td width="70" align="center"><p><? echo $booking_no_tot; ?></p></td>
						 <td width="100" align="right"><p><? echo number_format($val['fin_fab_qnty'],2); ?></p></td>
						 <td width="" align="right"><p><? echo number_format($val['grey_fab_qnty'],2); ?></p></td>
					</tr>
					<?
					$k++;
					$total_fin_qty+=$val['fin_fab_qnty'];
					$total_grey_fab_qnty+=$val['grey_fab_qnty'];
				}
					?>
					
				</tbody>
				<tfoot>
				<tr>
				<th colspan="3"> Total </th>
				<th  align="right"> <? echo number_format($total_fin_qty,2); ?></th>
				<th  align="right"> <? echo number_format($total_grey_fab_qnty,2); ?></th>
				</tr>
				</tfoot>
           </table>
		   <br>
		   <table width="<? echo $tbl_width_buyer;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		   <caption><strong style="float:left">Buyer Wise Summary</strong> </caption>
                <thead>
                	<tr style="font-size:13px">
					 <th  style="width:auto" colspan="3" >&nbsp;</th>
						  <?
						foreach($month_arr as $mon_key=>$val)
						{
						
						$tot_row=count($val);
						$tot_row=2;
						?>
						<th  style="width:auto"  align="center" colspan="<? echo $tot_row ?>">
						<? 
						echo $mon_key;
						?>
						</th>
						<?
						}
           					 ?>
					</tr>
					
					<tr style="font-size:13px">
                        <th width="30" >SL</th>
                        <th width="130">Buyer</th>
                        <th width="70" >Total WO</th>
					<?
						foreach($month_arr as $mon_key=>$val)
						{
						?>
                        <th width="100">Finish Qty (Kg)</th>
                        <th width="100">Grey Qty (Kg)</th>
						<?
						}
						?>
					</tr>
					
                </thead>
				<tbody>
				<?
				$j=1;$total_buyer_fin_qty=$total_buyer_grey_fab_qnty=$total_booking_no_tot=0;
				foreach($buyer_wise_arr  as $buyer_key=>$val)
				{
				  $bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF";
				  $booking_no=rtrim($val['booking_no'],',');
				 $tot_booking=array_unique(explode(",",$booking_no));
				 // echo $booking_no;
				  $booking_no_tot=count($tot_booking);
				?>
					 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trbuyer_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trbuyer_<? echo $j; ?>" style="font-size:12px">
						 <td width="30" align="center"><? echo $j;?></td>
                   		 <td width="130"><p><? echo  $buyer_arr[$buyer_key]; ?></p></td>
						 <td width="70" align="center"><p><? echo $booking_no_tot; ?></p></td>
						  <?
						 // $mon_year=date("Y-M",strtotime($row[csf("booking_date")]));
						foreach($month_arr as $mon_key=>$val)
						{
						$fin_fab_qnty=$buyer_wise_data_arr[$mon_key][$buyer_key]["fin_fab_qnty"];
						$grey_fab_qnty=$buyer_wise_data_arr[$mon_key][$buyer_key]["grey_fab_qnty"];
						
						$total_buyer_fin_arr[$mon_key]+=$fin_fab_qnty;
						$total_buyer_grey_arr[$mon_key]+=$grey_fab_qnty;
						?>
						 <td width="100" align="right"><p><? echo number_format($fin_fab_qnty,2); ?></p></td>
						 <td width="100" align="right"><p><? echo number_format($grey_fab_qnty,2); ?></p></td>
						 <?
						 }
						 ?>
					</tr>
					<?
					$j++;
					$total_buyer_fin_qty+=$fin_fab_qnty;
					$total_buyer_grey_fab_qnty+=$grey_fab_qnty;
					$total_booking_no_tot+=$booking_no_tot;
					
				}
					?>
					
				</tbody>
				<tfoot>
				<tr>
				<th colspan="2"> Total </th>
				<th  align="center"> <? echo $total_booking_no_tot; ?></th>
				  <?
				foreach($month_arr as $mon_key=>$val)
				{
						?>
				<th  width="100" align="right"> <? echo number_format($total_buyer_fin_arr[$mon_key],2); ?></th>
				<th  width="100" align="right"> <? echo number_format($total_buyer_grey_arr[$mon_key],2); ?></th>
				<?
				}
				?>
				</tr>
				</tfoot>
           </table>
		    <br>
		   <table width="<? echo $tbl_width_supplier;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		   <caption><strong style="float:left">Supplier Location Wise Summary</strong> </caption>
                <thead>
                	<tr style="font-size:13px">
					 <th  style="width:auto" colspan="4" >&nbsp;</th>
						  <?
						foreach($month_arr as $mon_key=>$val)
						{
						
						$tot_row=count($val);
						$tot_row=2;
						?>
						<th  style="width:auto"  align="center" colspan="<? echo $tot_row ?>">
						<? 
						echo $mon_key;
						?>
						</th>
						<?
						}
           					 ?>
					</tr>
					
					<tr style="font-size:13px">
                        <th width="30" >SL</th>
                        <th width="200">Supplier</th>
						 <th width="100">Location</th>
                        <th width="70" >Total WO</th>
					<?
						foreach($month_arr as $mon_key=>$val)
						{
						?>
                        <th width="100">Finish Qty (Kg)</th>
                        <th width="100">Grey Qty (Kg)</th>
						<?
						}
						?>
					</tr>
					
                </thead>
				<tbody>
				<?
				$j=1;$total_supp_fin_qty=$total_supp_grey_fab_qnty=$total_booking_supp_no_tot=0;
				foreach($supplier_location_wise_arr  as $supplier_key=>$supplier_data)
				{
				foreach($supplier_data  as $location_key=>$val)
				{
				  $bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF";
				  $booking_no=rtrim($val['booking_no'],',');$supplier_com=$val['supplier_com'];
				 $tot_booking=array_unique(explode(",",$booking_no));
				 // echo $booking_no;
				  $booking_no_tot=count($tot_booking);
				 // $supplier_library=return_library_array( "select id,supplier_name from lib_location", "id", "supplier_name"  );
//$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );

				?>
					 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trbuyer_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trbuyer_<? echo $j; ?>" style="font-size:12px">
						 <td width="30" align="center"><? echo $j;?></td>
                   		 <td width="200"><p><? echo  $supplier_com; ?></p></td>
						  <td width="100"><p><? echo  $location_library[$location_key]; ?></p></td>
						 <td width="70" align="center"><p><? echo $booking_no_tot; ?></p></td>
						  <?
						 // $mon_year=date("Y-M",strtotime($row[csf("booking_date")]));
						foreach($month_arr as $mon_key=>$val)
						{
						$fin_fab_qnty=$supplier_location_wise_data_arr[$mon_key][$supplier_key][$location_key]["fin_fab_qnty"];
						$grey_fab_qnty=$supplier_location_wise_data_arr[$mon_key][$supplier_key][$location_key]["grey_fab_qnty"];
						
						$total_supp_fin_arr[$mon_key]+=$fin_fab_qnty;
						$total_supp_grey_arr[$mon_key]+=$grey_fab_qnty;
						?>
						 <td width="100" align="right"><p><? echo number_format($fin_fab_qnty,2); ?></p></td>
						 <td width="100" align="right"><p><? echo number_format($grey_fab_qnty,2); ?></p></td>
						 <?
						 }
						 ?>
					</tr>
					<?
					$j++;
					$total_supp_fin_qty+=$fin_fab_qnty;
					$total_supp_grey_fab_qnty+=$grey_fab_qnty;
					$total_booking_supp_no_tot+=$booking_no_tot;
					}
					
				}
					?>
					
				</tbody>
				<tfoot>
				<tr>
				<th colspan="3"> Total </th>
				<th  align="center"> <? echo $total_booking_supp_no_tot; ?></th>
				  <?
				foreach($month_arr as $mon_key=>$val)
				{
						?>
				<th  width="100" align="right"> <? echo number_format($total_supp_fin_arr[$mon_key],2); ?></th>
				<th  width="100" align="right"> <? echo number_format($total_supp_grey_arr[$mon_key],2); ?></th>
				<?
				}
				?>
				</tr>
				</tfoot>
           </table>
		    <br>
		   <table width="<? echo $tbl_width_wo;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		   <caption><strong style="float:left">Work Order Wise </strong> </caption>
                <thead>
                	
					<tr style="font-size:13px">
                        <th width="30">SL</th>
                        <th width="100">WO Num</th>
						<th width="80">Int. Ref. No</th>
						<th width="80">WO Date</th>
					
						<th width="100">Buyer</th>
						<th width="130">Supplier</th>
					   <th width="100">Location</th>
					   <th width="100">Department</th>
					   <th width="100">Responsible Person</th> 
					   <th width="100">Reason</th>
                       <th width="100">Finish Qty (Kg)</th>
                       <th width="100">Grey Qty (Kg)</th>
					</tr>
					
                 </thead>
           		</table>	
                <div class="scroll_div_inner"  style="width:<? echo $tbl_width_wo+20;?>px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body">
				<table class="rpt_table" width="<? echo $tbl_width_wo;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
				$j=1;$total_wo_fin_qty=$total_wo_grey_fab_qnty=0;
				foreach($wo_wise_arr  as $wo_key=>$value)
				{
					
				  	$bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF";
				 	 $booking_no=rtrim($value['booking_no'],',');
					 $pay_mode=$value['pay_mode'];
					 $responsible_person=rtrim($value['responsible_person'],',');
					 $reason=rtrim($value['reason'],','); $supplier_com=$value['supplier_id'];
					 $tot_booking=array_unique(explode(",",$booking_no));
				 // echo $booking_no;
				 	 $booking_no_tot=count($tot_booking);
					 $responsible_dept=$wo_wise_arr_department_arr[$wo_key]['responsible_dept'];
					$wo_fin_fab_qnty=$value['fin_fab_qnty'];
					$wo_grey_fab_qnty=$value['grey_fab_qnty'];
					//echo value['fin_fab_qnty'].'FF';
				?>
					 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trwo_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trwo_<? echo $j; ?>" style="font-size:12px">
						 <td width="30" align="center"><? echo $j;?></td>
						  <td width="100" align="center"><p><? echo $wo_key; ?></p></td>
						  <td width="80" align="center"><p><? echo $value['internal_ref_no']; ?></p></td>
						  <td width="80" align="center"><p><? echo change_date_format($value['booking_date']); ?></p></td>
						  <td width="100" align="center"><p><? echo $buyer_arr[$value['buyer_id']]; ?></p></td>
						  <td width="130" align="center"><p><? echo $supplier_com; ?></p></td>
						  <td width="100"><p><? echo  $location_library[$value['location_id']]; ?></p></td>
                   		 <td width="100"><p><? echo  $responsible_dept; ?></p></td>
						 
						 <td width="100" align="center"><p><? echo $responsible_person; ?></p></td>
						 <td width="100" align="center"><p><? echo $reason; ?></p></td>
						 
						 <td width="100" align="right"><p><? echo number_format($wo_fin_fab_qnty,2); ?></p></td>
						 <td width="100" align="right"><p><? echo number_format($wo_grey_fab_qnty,2); ?></p></td>
					</tr>
					<?
					$j++;
					$total_wo_fin_qty+=$wo_fin_fab_qnty;
					$total_wo_grey_fab_qnty+=$wo_grey_fab_qnty;
				
				}
					?>
				 
					
				</table>
				
           </div>
     	 <table width="<? echo $tbl_width_wo;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
				<td colspan="10"> Total </td>
				<td  width="100" align="right"> <? echo number_format($total_wo_fin_qty,0); ?></td>
				<td  width="100" align="right"> <? echo number_format($total_wo_grey_fab_qnty,0); ?></td>
				</tr>
			</table>
           
          </fieldset>
        </div>
<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}

if($action=='trims_popup')
{
	echo load_html_head_contents("Trims Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id."*".$tot_po_qnty;die;
	
	//echo $ratio;die;

?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:650px;" >
<legend>Accessories Status pop up</legend>
    <div style="100%" id="report_container">
       <table width="650" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="7">Accessories Status</th>
                </tr>
                <tr>
                    <th width="110">Item</th>
                    <th width="70">UOM</th>
                    <th width="90">Req. Qty.</th>
                    <th width="90">Received</th>
                    <th width="90">Recv. Balance</th>
                    <th width="90">Issued</th>
                    <th>Left Over</th>
                </tr>
            </thead>
            <?
			$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
			$trims_array=array();
			$trimsDataArr=sql_select("select b.item_group_id,  
									sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
									sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
									from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in($po_break_down_id) and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.item_group_id");
			foreach($trimsDataArr as $row)	
			{
				$trims_array[$row[csf('item_group_id')]]['recv']=$row[csf('recv_qnty')];
				$trims_array[$row[csf('item_group_id')]]['iss']=$row[csf('issue_qnty')];
			}
			
			
			//$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per"  );
			$trimsDataArr=sql_select("select c.po_break_down_id, max(a.costing_per) as costing_per, b.trim_group, max(b.cons_uom) as cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b , wo_pre_cost_trim_co_cons_dtls c where a.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and b.status_active=1 and b.is_deleted=0 and c.po_break_down_id=$po_break_down_id group by b.trim_group, c.po_break_down_id");
			$i=1; $tot_accss_req_qnty=0; $tot_recv_qnty=0; $tot_iss_qnty=0; $tot_recv_bl_qnty=0; $tot_trims_left_over_qnty=0;
			foreach($trimsDataArr as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$dzn_qnty='';
				if($row[csf('costing_per')]==1) $dzn_qnty=12;
				else if($row[csf('costing_per')]==3) $dzn_qnty=12*2;
				else if($row[csf('costing_per')]==4) $dzn_qnty=12*3;
				else if($row[csf('costing_per')]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$ratio;
        		$accss_req_qnty=($row[csf('cons_dzn_gmts')]/$dzn_qnty)*$tot_po_qnty;
				
				$trims_recv=$trims_array[$row[csf('trim_group')]]['recv'];
				$trims_issue=$trims_array[$row[csf('trim_group')]]['iss'];
				$recv_bl=$accss_req_qnty-$trims_recv;
				$trims_left_over=$trims_recv-$trims_issue;
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>">
                    <td><p><? echo $item_library[$row[csf('trim_group')]]; ?>&nbsp;</p></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($accss_req_qnty,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_recv,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($recv_bl,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_issue,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_left_over,2,'.',''); ?>&nbsp;</td>
                </tr>
            <?
				$tot_accss_req_qnty+=$accss_req_qnty;
				$tot_recv_qnty+=$trims_recv;
				$tot_recv_bl_qnty+=$recv_bl;
				$tot_iss_qnty+=$trims_issue;
				$tot_trims_left_over_qnty+=$trims_left_over;
				$i++;
			}
			$tot_trims_left_over_qnty_perc=($tot_trims_left_over_qnty/$tot_recv_qnty)*100;
			?>
            <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_accss_req_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_iss_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_trims_left_over_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>  
        </div>
    </fieldset>
<?	

	exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>
                       
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
              
				$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date, 
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty 
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);
                
                foreach($sql_dtls as $row_real)
                { 
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";                               
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td> 
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <? 
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div> 
		</fieldset>
	</div>    
	<?
    exit();	
}

if ($action=="reject_qty")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	
	//echo $po_id;
	?>
     <div style="width:500px;" align="center"> 
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" > 
             <thead>
             	<tr>
                	<th colspan="7">Reject Qty Details</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="75">Cutting Reject Qty</th>
                    <th width="75">Embellishment Reject Qty</th>
                    <th width="75">Sewing Out Reject Qty</th>
                    <th width="75">Iron Reject Qty</th>
                    <th width="75">Finish Reject Qty.</th>
                    <th >Total Reject Qty.</th>
                 </tr>
              </thead>
              <tbody> 
			 <?
			$po_id=str_replace("'","",$po_id); 
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";
			 
			$sql_qry="Select a.po_break_down_id,
							sum(CASE WHEN a.production_type ='1' THEN b.reject_qty ELSE 0 END) AS cutting_rej_qnty,
							sum(CASE WHEN a.production_type ='3' THEN b.reject_qty ELSE 0 END) AS emb_rej_qnty,
							sum(CASE WHEN a.production_type ='7' THEN b.reject_qty ELSE 0 END) AS iron_rej_qnty,
			 				sum(CASE WHEN a.production_type ='8' THEN b.reject_qty ELSE 0 END) AS finish_rej_qnty,
							sum(CASE WHEN a.production_type ='5' THEN b.reject_qty ELSE 0 END) AS sewingout_rej_qnty
							from pro_garments_production_mst a, pro_garments_production_dtls b
							where a.id=b.mst_id and  a.po_break_down_id in ($po_id)  and a.status_active=1 and a.is_deleted=0  group by a.po_break_down_id";
			  //echo $sql_qry;
			$sql_result=sql_select($sql_qry);

			$i=1;	 
			foreach($sql_result as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo number_format($row[csf('cutting_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('emb_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('sewingout_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('iron_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('finish_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? $total_reject=$row[csf('cutting_rej_qnty')]+$row[csf('emb_rej_qnty')]+$row[csf('iron_rej_qnty')]+$row[csf('sewingout_rej_qnty')]+$row[csf('finish_rej_qnty')]; echo $total_reject; ?>&nbsp;</td>
                 </tr>   
             <? 
			  	$i++; 
			 } 
			 ?> 
             </tbody>
            
         </table>
     </div>    
	<?
	exit();
}
//disconnect($con);
?>
