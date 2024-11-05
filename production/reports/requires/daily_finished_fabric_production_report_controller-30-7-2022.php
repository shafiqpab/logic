<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//--------------------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_unit")
{
	echo create_drop_down( "cbo_unit_id", 140, "Select id, location_name from   lib_location where company_id=$data and status_active=1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", 0, "",0 );
	exit();     	 
}


if ($action == "load_drop_down_knitting_com") 
{
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		echo create_drop_down("cbo_working_company", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", "", "load_drop_down('requires/daily_finished_fabric_production_report_controller', this.value+'_'+document.getElementById('cbo_working_source').value, 'load_drop_down_knitting_location', 'knitting_location_td' );", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_working_company", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "");
	} else {
		echo create_drop_down("cbo_working_company", 140, $blank_array, "", 1, "--Select Knit Company--", 0, "");
	}
	exit();
}
if ($action=="load_drop_down_knitting_location")
{	
	$data = explode("_", $data);
	$company_id = $data[0];
		if ($data[1]==1) {
			echo create_drop_down( "cbo_working_location", 140, "Select id, location_name from   lib_location where company_id=$company_id  and status_active=1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down('requires/daily_finished_fabric_production_report_controller', this.value+'_'+document.getElementById('cbo_working_company').value, 'load_drop_down_knitting_floor', 'knitting_floor_td' );",0 );
		}
		else{
			// echo create_drop_down("cbo_working_location",140,$blank_array,"", 1, "-- All --", 0,"",0,'');
		}
		
	exit();     	 
}
if ($action=="load_drop_down_knitting_floor")
{	
	$data = explode("_", $data);
	$location_id = $data[0];
	$company_id = $data[1];
		echo create_drop_down( "cbo_working_floor", 140, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id  and a.company_id=$company_id and b.location_id=$location_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "-- Select --", $selected, "",0 );
	exit();     	 
}

if($action=="order_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
				$("#tr_"+i).click();
			}
		}
	function js_set_value(id)
	{ //alert(id);
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
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="130">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Order No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $txt_job_no; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'order_wise_search_list_view', 'search_div', 'daily_finished_fabric_production_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
        <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="order_wise_search_list_view")
{
	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_name,$txt_job_no,$buyer_name,$search_type,$search_value)=explode('**',$data);
	
	//print ($data[1]);
	?>
      <input type="hidden" id="txt_po_id" />
     <input type="hidden" id="txt_po_val" />
 <?
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and b.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.po_number like('%$search_value%')";	
	}

	if ($company_name==0) $company_id=""; else $company_id=" and company_name=$company_name";
	if ($txt_job_no=="") $job_no_cond="";  
    else
	 { 
	    if($db_type==0) $job_no_cond="  and FIND_IN_SET(b.job_no_prefix_num,'$txt_job_no')";
		if($db_type==2) $job_no_cond="  and b.job_no_prefix_num in($txt_job_no)";
	 }
	if($db_type==0) $year_field="YEAR(b.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	$sql ="select distinct a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_id $search_con $job_no_cond"; 
	echo create_list_view("list_view", "Order Number,Job No, Year","150,100,50","450","250",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();
}

if($action=="batch_wise_search")
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
				$("#tr_"+i).click();
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
		$('#hdn_batch_id').val( id );
		$('#hdn_batch_val').val( ddd );
	} 
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Batch No</th>
                    <th>Booking No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	<input type="text" style="width:130px" class="text_boxes" name="txt_batch_no" id="txt_batch_no" value="<? echo $txt_batch_no;?>" />
                        </td>                     
                        <td align="center">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" value="<? echo $txt_booking_no;?>" />	
                        </td> 	
                        <td align="center">
                        <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('txt_batch_no').value +'**'+ document.getElementById('txt_booking_no').value, 'batch_wise_search_list_view', 'search_div', 'daily_finished_fabric_production_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
        <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="batch_wise_search_list_view")
{
	extract($_REQUEST);
	list($company_name,$txt_batch_no,$txt_booking_no)=explode('**',$data);
	?>
	<input type="hidden" id="hdn_batch_id" />
	<input type="hidden" id="hdn_batch_val" />
	<?

	if($txt_booking_no!='')
	{
		$booking_con=" and booking_no like '%$txt_booking_no%'";	
	}

	if($txt_batch_no!='')
	{
		$batch_con=" and batch_no like '%$txt_batch_no%'";	
	}

	if ($company_name==0) $company_id=""; else $company_id=" and company_id=$company_name";

	if($db_type==0) $year_field="YEAR(b.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	$sql ="select id, batch_no,booking_no, extention_no, batch_against from pro_batch_create_mst where status_active =1 and is_deleted =0 $company_id and entry_form in (7,0) $booking_con $batch_con"; 
	$arr=array(3=>$batch_against);
	echo create_list_view("list_view", "Batch No,Booking No, Extention No, Batch Against","100,100,50,100","450","250",0, $sql , "js_set_value", "id,batch_no", "", 1, "0,0,0,batch_against", $arr, "batch_no,booking_no,extention_no,batch_against", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();
}


if($action=="jobnumbershow")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
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
		$('#hide_job_id').val( id );
		$('#hide_job_no').val( ddd );
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
                    <th id="search_by_td_up" width="130">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--","","",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_unit_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'jobnumbershow_search_list_view', 'search_div', 'daily_finished_fabric_production_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
        <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );


if($action=="jobnumbershow_search_list_view")
{
	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$cbo_year_id,$cbo_unit_id,$buyer_name,$search_type,$search_value)=explode('**',$data);

	?>
	<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
	<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
	<?
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and style_ref_no like('%$search_value%')";	
	}
	if($buyer_name!=0){
		$search_con .=" and buyer_name=$buyer_name";	
	}
	echo $search_con;
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date) as year "; 
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY') as year ";
	if($db_type==0) $month_field_by="and month(insert_date)"; 
	else if($db_type==2) $month_field_by="and to_char(insert_date,'MM')";
	if($db_type==0) $year_field="and year(insert_date)=$year_id"; 
	else if($db_type==2) $year_field="and to_char(insert_date,'YYYY')";
	$year_id=str_replace("'","",$cbo_year_id);
	if($db_type==2) $yearcond=" and to_char(insert_date,'YYYY')=$year_id";
	else if($db_type==0) $yearcond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$year_id";
	//echo $year_cond;
	if($cbo_unit_id!=0) $unit_id_cond="and location_name=$cbo_unit_id"; else $unit_id_cond="";
	if($year_id!=0) $year_cond="$year_field"; else $year_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, $year_field_by, style_ref_no from wo_po_details_master where status_active=1 and is_deleted=0 $search_con and company_name=$company_id  $unit_id_cond $yearcond order by job_no";
	echo create_list_view("list_view", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","250",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;
   exit(); 
}

if($action=="bookingnumbershow")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>

	var cbo_production_type = '<? echo $cbo_production_type;  ?>';

	

	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
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
		$('#hide_booking_id').val( id );
		$('#hide_booking_no').val( ddd );
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
                    <th id="search_by_td_up" width="130">Please Enter Booking No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--","","",0 );
							?>
                        </td>                 
                           
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_unit_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_production_type;?>', 'bookingnumbershow_search_list_view', 'search_div', 'daily_finished_fabric_production_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
        <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="bookingnumbershow_search_list_view")
{
	
	extract($_REQUEST);
	//var_dump($_REQUEST);

	list($company_id,$cbo_year_id,$cbo_unit_id,$buyer_name,$search_value,$production_type)=explode('**',$data);
	
	?>
	<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
	<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
	<?
	$search_con = "";

	if($buyer_name!=0){
		$search_con .=" and buyer_id=$buyer_name";	
	}

	if($search_value!= ""){
		$search_con .=" and booking_no_prefix_num=$search_value";	
	}
	
	if($db_type==0) $year_field_by="year(insert_date) as year "; 
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY') as year ";
	if($db_type==0) $month_field_by="and month(insert_date)"; 
	else if($db_type==2) $month_field_by="and to_char(insert_date,'MM')";
	if($db_type==0) $year_field="and year(insert_date)=$year_id"; 
	else if($db_type==2) $year_field="and to_char(insert_date,'YYYY')";
	$year_id=str_replace("'","",$cbo_year_id);
	if($db_type==2) $yearcond=" and to_char(insert_date,'YYYY')=$year_id";
	else if($db_type==0) $yearcond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$year_id";
	//echo $year_cond;
	//if($cbo_unit_id!=0) $unit_id_cond="and location_name=$cbo_unit_id"; else $unit_id_cond="";
	if($year_id!=0) $year_cond="$year_field"; else $year_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr, 2=>'', 3=>'', 4=>$booking_type);

	if($production_type>0 && $production_type==2)
	{
		$sql = "select id, booking_no, booking_no_prefix_num, company_id, buyer_id, booking_type, $year_field_by from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0 and company_id=$company_id $yearcond $search_con";
	}else if($production_type>0 && $production_type==1) {
		$sql ="select id, booking_no, booking_no_prefix_num, company_id, buyer_id, booking_type, $year_field_by from wo_booking_mst where status_active=1 and is_deleted=0 and booking_type in(4) and company_id=$company_id $yearcond $search_con";
	}else{
		$sql = "(select id, booking_no, booking_no_prefix_num, company_id, buyer_id, booking_type, $year_field_by from wo_booking_mst where status_active=1 and is_deleted=0 and booking_type in(1,4,7) and company_id=$company_id $yearcond $search_con ) union all (select id, booking_no, booking_no_prefix_num, company_id, buyer_id, booking_type, $year_field_by from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $search_con and company_id=$company_id $yearcond)";
	}

	echo create_list_view("list_view", "Company,Buyer Name,booking No,Year,Booking Type", "120,130,80,60","620","250",0, $sql , "js_set_value", "id,booking_no_prefix_num", "", 1, "company_id,buyer_id,0,0,booking_type", $arr , "company_id,buyer_id,booking_no_prefix_num,year,booking_type", "","setFilterGrid('list_view',-1)","0","",1) ;
   exit(); 
}


$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name");
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");


if($action=="report_generate")
{
	// var_dump($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_unit=str_replace("'","",$cbo_unit_id);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_booking_id=str_replace("'","",$txt_booking_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_batch_id=str_replace("'","",$txt_batch_id);
	$txt_batch_no= trim(str_replace("'","",$txt_batch_no));
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	$cbo_production_type=str_replace("'","",$cbo_production_type);
	$cbo_working_source=str_replace("'","",$cbo_working_source);
	$cbo_working_company=str_replace("'","",$cbo_working_company);
	$cbo_working_floor=str_replace("'","",$cbo_working_floor);
	$cbo_working_location=str_replace("'","",$cbo_working_location);
	$type=str_replace("'","",$type);
	
	if ($cbo_working_company !=0) {$ref_company_cond=" and a.company_id=$cbo_working_company";}else{$ref_company_cond=" and a.company_id=$cbo_company";}

	if($db_type==0) 
	{
		$date_from=change_date_format($date_from,'yyyy-mm-dd');
		$date_to=change_date_format($date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$date_from=change_date_format($date_from,'','',1);
		$date_to=change_date_format($date_to,'','',1);
	}
	else  
	{
		$date_from=""; $date_to="";
	}
	if($date_from!=="" and $date_to!=="")
	{
		$date_cond=" and a.receive_date between '$date_from' and '$date_to'";	
	}
	if($cbo_unit==0) $unit_name=""; else $unit_name="and a.location_id=$cbo_unit";

	if ($txt_job_no=="") $job_no_cond=""; else $job_no_cond=" and d.job_no_prefix_num in ($txt_job_no) ";
	if ($txt_booking_id =="" ) $booking_no_cond=""; else $booking_no_cond="and a.booking_no_id in ($txt_booking_id) ";

	if(str_replace("'","",$txt_order_no_id)!="") $order_cond=" and e.id in(".$txt_order_id.")";
	else if(str_replace("'","",$txt_order_no)!="") $order_cond=" and e.po_number in('".$txt_order_no."')"; 
	else $order_cond="";

	if(str_replace("'","",$txt_order_no_id)!="") $order_cond_1=" and b.id in(".$txt_order_no_id.")";
	else if(str_replace("'","",$txt_order_no)!="") $order_cond_1=" and b.po_number in('".$txt_order_no."')"; 
	else $order_cond_1="";

	if(str_replace("'","",$txt_batch_id)!="") 
	{
		$batch_cond=" and c.id in(".$txt_batch_id.")";
	}
	else if(str_replace("'","",$txt_batch_no)!="") 
	{
		$txt_batch_no = "'".implode("','",explode(",", $txt_batch_no))."'";
		$batch_cond=" and c.batch_no in(".$txt_batch_no.")";
	}
	else $batch_cond="";

	if ($txt_job_no != "") $job_no_cond_1=" and a.job_no_prefix_num in ($txt_job_no) ";
	else if ($txt_job_id !="") $job_no_cond_1=" and a.id in ($txt_job_id) ";
	else  $job_no_cond_1="";


	$file_ref_cond="";
	if($txt_file_no!="") $file_ref_cond=" and e.file_no =".$txt_file_no."";
	if($txt_int_ref_no!="") $file_ref_cond.=" and e.grouping ='".$txt_int_ref_no."'";
	
	if($txt_file_no!="") $file_cond=" and b.file_no =".$txt_file_no."";else $file_cond="";
	if($txt_int_ref_no!="") $ref_cond=" and b.grouping ='".$txt_int_ref_no."'";else $ref_cond="";

	if ($txt_booking_no=="") 
	{
		$booking_no_cond_1=""; 
	}
	else 
	{	$booking_no_arr = explode(",", $txt_booking_no);
	
		$booking_no_cond_1 = " and (";
		foreach ($booking_no_arr as $val) 
		{
			$booking_no_cond_1 .=" c.booking_no like '%$val%' or";
		}
		$booking_no_cond_1 = chop($booking_no_cond_1," or");
		$booking_no_cond_1 .=" ) ";
	}


	$knitting_ref_cond="";
	if($cbo_working_source)
	{
		$knitting_ref_cond = " and a.knitting_source= $cbo_working_source ";
	}

	if($cbo_working_company)
	{
		$knitting_ref_cond .= " and a.knitting_company= $cbo_working_company ";
	}

	if($cbo_working_floor)
	{
		$knitting_ref_cond .= " and b.floor= $cbo_working_floor ";
	}
	if($cbo_working_location)
	{
		$knitting_ref_cond .= " and a.location_id= $cbo_working_location ";
	}

	if($cbo_production_type==1)
	{

	}
	else if($cbo_production_type==2)
	{

	}
	
	$job_no_arr=array();$all_po_id=''; $all_non_book_id='';

	$sql_po_1="";
	if ($type==1) // Show Start
	{
		/*if($txt_order_no!="" || $txt_job_no!="" ||  $txt_file_no!="" ||  $txt_int_ref_no!="" || $txt_booking_no!="" && $cbo_company !=0)
		{
			if($cbo_production_type==1 || $cbo_production_type==0)
			{
				$sql_po_1 = "select b.id, b.job_no_mst, b.po_number, b.po_quantity, b.file_no, b.grouping as int_ref_no, a.total_set_qnty, a.style_ref_no, a.buyer_name , c.booking_no, 1 as type from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where b.job_no_mst=a.job_no and b.id = c.po_break_down_id and a.company_name = $cbo_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $file_cond $ref_cond $order_cond_1  $job_no_cond_1 $booking_no_cond_1";
			}

			if($cbo_production_type==2 || $cbo_production_type==0)
			{
				if($sql_po_1 != "")
				{
					$sql_po_1 .= " union all ";
				}

				$sql_po_1 .= " select c.id, null as job_no_mst, null as po_number, null as po_quantity, null as file_no, null as int_ref_no, null as total_set_qnty, null as style_ref_no, c.buyer_id as buyer_name, c.booking_no, 2 as type from wo_non_ord_samp_booking_mst c where c.company_id = $cbo_company and c.status_active =1 and c.is_deleted=0 $booking_no_cond_1";
			}

			$sql_po=sql_select($sql_po_1);

			foreach($sql_po as $row)
			{
				if($row[csf('type')] == 1)	
				{
					$po_qty=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
					$job_no_arr[$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
					$job_no_arr[$row[csf('id')]]['po_quantity']=$po_qty;
					$job_no_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$job_no_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
					$job_no_arr[$row[csf('id')]]['file_no']=$row[csf('file_no')];
					$job_no_arr[$row[csf('id')]]['int_ref_no']=$row[csf('int_ref_no')];
					if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];

				}
				else
				{
					if($all_non_book_id=="") $all_non_book_id=$row[csf('id')]; else $all_non_book_id.=",".$row[csf('id')]; 
				}

				$all_booking_no_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
			}
		}*/

		/*if(count($all_booking_no_arr)>0)
		{
			$all_booking_nos= "'".implode("','",$all_booking_no_arr)."'";
			$all_booking_no_cond=""; $bookCond=""; 
			if($db_type==2 && count($all_booking_nos)>999)
			{
				$all_booking_no_chunk=array_chunk($all_booking_nos,999) ;
				foreach($all_barcode_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);	
					$bookCond.="  c.booking_no in($chunk_arr_value) or ";	
				}
				$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";
			}
			else
			{
				$all_booking_no_cond=" and c.booking_no in($all_booking_nos)";	 
			}
		}*/
		
		if($txt_order_no!="" || $txt_job_no!="" ||  $txt_file_no!="" ||  $txt_int_ref_no!="" || $txt_booking_no!="" && $cbo_company !=0)
		{
			if($cbo_production_type==1 || $cbo_production_type==0)
			{
				$sql_po_1 = "SELECT b.id, b.job_no_mst, b.po_number, b.po_quantity, b.file_no, b.grouping as int_ref_no, a.total_set_qnty, a.style_ref_no, a.buyer_name , c.booking_no, 1 as type from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c 
				where b.job_no_mst=a.job_no and b.id = c.po_break_down_id and a.company_name = $cbo_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $file_cond $ref_cond $order_cond_1  $job_no_cond_1 $booking_no_cond_1";
			}
			if($txt_order_no="" || $txt_job_no="" ||  $txt_file_no="" ||  $txt_int_ref_no="" || $txt_booking_no="")
			{
				if($cbo_production_type==2 || $cbo_production_type==0)
				{
					if($sql_po_1 != "")
					{
						$sql_po_1 .= " union all ";
					}
		
					$sql_po_1 .= " select c.id, null as job_no_mst, null as po_number, null as po_quantity, null as file_no, null as int_ref_no, null as total_set_qnty, null as style_ref_no, c.buyer_id as buyer_name, c.booking_no, 2 as type from wo_non_ord_samp_booking_mst c where c.company_id = $cbo_company and c.status_active =1 and c.is_deleted=0 $booking_no_cond_1";
				}
				
			}

			$sql_po=sql_select($sql_po_1);

			foreach($sql_po as $row)
			{
				if($row[csf('type')] == 1)	
				{
					$po_qty=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
					$job_no_arr[$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
					$job_no_arr[$row[csf('id')]]['po_quantity']=$po_qty;
					$job_no_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$job_no_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
					$job_no_arr[$row[csf('id')]]['file_no']=$row[csf('file_no')];
					$job_no_arr[$row[csf('id')]]['int_ref_no']=$row[csf('int_ref_no')];
					if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];

				}
				else
				{
					if($all_non_book_id=="") $all_non_book_id=$row[csf('id')]; else $all_non_book_id.=",".$row[csf('id')]; 
				}

				$all_booking_no_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
			}
		}
		
		if(count($all_booking_no_arr)>0)
		{
			//$all_booking_nos= "'".implode("','",$all_booking_no_arr)."'";
			$all_booking_no_cond=""; $bookCond=""; 
			if($db_type==2 && count($all_booking_no_arr)>999)
			{
				$all_booking_no_chunk=array_chunk($all_booking_no_arr,999) ;
				foreach($all_booking_no_chunk as $chunk_arr)
				{
					/*echo "<pre>";
					print_r(array_unique($chunk_arr));*/
					$chunk_arr_value="'".implode("','",$chunk_arr)."'";
					$bookCond.="  c.booking_no in($chunk_arr_value) or ";	
				}
				$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";
			}
			else
			{
				$all_booking_nos= "'".implode("','",$all_booking_no_arr)."'";
				$all_booking_no_cond=" and c.booking_no in($all_booking_nos)";	 
			}

		}

		/*if($cbo_production_type==0) 
		{
			$sql_batch_sql="SELECT a.id, sum(b.batch_qnty) as batch_qnty,a.batch_no,a.booking_no,a.booking_no_id,a.booking_without_order,a.extention_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.company_id=$cbo_company and a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 $booking_no_cond $booking_no_write_cond group by  a.id, a.batch_no,a.booking_no,a.booking_no_id,a.booking_without_order,a.extention_no ";
		} 
		else 
		{
			if($cbo_production_type==2)
			{
				//commit 12:54
				$sql_batch_sql="SELECT a.id, sum(b.batch_qnty) as batch_qnty,a.batch_no,a.booking_no,a.booking_no_id,a.booking_without_order,a.extention_no from pro_batch_create_mst a, pro_batch_create_dtls b, wo_non_ord_samp_booking_mst c where a.company_id=$cbo_company and a.id=b.mst_id and a.booking_no_id=c.id and c.booking_type=4  and a.booking_without_order=1 and  a.status_active=1 and a.is_deleted=0 $booking_no_cond $booking_no_write_cond  group by  a.id, a.batch_no,a.booking_no,a.booking_no_id,a.booking_without_order,a.extention_no ";
			}else {

				$sql_batch_sql="SELECT a.id, sum(b.batch_qnty) as batch_qnty,a.batch_no,a.booking_no,a.booking_no_id,a.booking_without_order,a.extention_no from pro_batch_create_mst a, pro_batch_create_dtls b, wo_booking_mst c where a.company_id=$cbo_company and a.id=b.mst_id and a.booking_no_id=c.id and c.booking_type=4  and booking_without_order=0 and  a.status_active=1 and a.is_deleted=0 $booking_no_cond $booking_no_write_cond group by  a.id, a.batch_no,a.booking_no,a.booking_no_id,a.booking_without_order,a.extention_no ";
			}

		};*/


		$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
		$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
		$floor_arr=return_library_array( "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id","floor_room_rack_name");	


		ob_start();
		?>	

		<div>
	  	<style>
			.word_wrap_break{
				word-wrap: break-word;
				word-break: break-all;
			}
	    </style>
	  	<fieldset style="width:2890px;">
	        <table width="1940px" cellpadding="0" cellspacing="0" id="caption" align="center">
	            <tr>
	               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$cbo_company];?></strong></td>
	            </tr> 
	            <tr>  
	               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr>  
	               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
	            </tr>  
	        </table>
	        <table  width="2970" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
	        	<thead>
	                <th width="40">SL</th>
	                <th width="100">Working Company</th>
	                <th width="100">Working Floor</th>
	                <th width="120">Production ID</th>
	                <th width="80">Production Date</th>
	                <th width="100">Challan No</th>
	                <th width="80">Dying Start Date</th>
	                <th width="70">Execution Days</th>
	                <th width="100">Shipment Date</th>
	                <th width="80">Unit Name</th>
	                <th width="120">Booking No</th>
	                <th width="120">Job No</th>
	                <th width="100">Buyer Name</th>
	                <th width="100">Style Ref.</th>
	                <th width="100">Order No</th>
	                <th width="70">File No</th>
	                <th width="80">Int. Ref. No</th>
	                <th width="80">Order Qty(Pcs)</th>
	                <th width="250">Fabric Description</th>
	                <th width="100">Yarn Brand</th>
	                <th width="60">F.GSM</th>
	                <th width="60">Fabric Dia</th>
	                <th width="100">Fabric Color</th>
	                <th width="80">Required Qty.</th>
	                <th width="100">Batch/Lot No</th>
	                <th width="100">Extension Number</th>
	                <th width="80">Batch Qty.</th>
	                <th width="80">QC Pass Qty</th>
	                <th width="80">Grey Used Qty</th>
	                <th width="80">Reject Qty</th>
	                <th width="">Remarks</th>
	            </thead>
	        </table>
	        <div style="width:3000px; overflow-y:scroll; max-height:350px;" id="scroll_body">
	        <table width="2970" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="tbl_dyeing">
	        <?
	        $con = connect();
            $r_id=execute_query("delete from tmp_barcode_no where userid=$user_name");
			
			if ($cbo_company !=0) {$lcCompany_cond=" and a.company_id=$cbo_company";}else{$lcCompany_cond="";}
			if ($cbo_company !=0) {$lcCompany_cond_2=" and a.company_name=$cbo_company";}else{$lcCompany_cond_2="";}

			$sql_dtls="SELECT a.knitting_source, a.knitting_company, b.floor, a.recv_number, a.receive_date,a.location_id,b.order_id,b.batch_id,b.gsm,b.width,b.grey_used_qty,b.receive_qnty as qc_pass_qty,b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id , c.booking_no, c.booking_without_order, c.batch_no,c.extention_no,b.remarks,b.prod_id 
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c
			where  a.id=b.mst_id $lcCompany_cond and a.entry_form in(7) and b.batch_id = c.id and c.status_active =1 and a.item_category=2 and a.status_active=1 and a.is_deleted=0  $date_cond $unit_name $knitting_ref_cond  $all_booking_no_cond $batch_cond";
			//echo $sql_dtls;
			$order_ids = "";$nonOrdBooking= "";
			$sql_dtls_result=sql_select($sql_dtls);
			foreach ($sql_dtls_result as $key => $row) 
			{
				$str_ref=$row[csf('batch_id')].'*'.$row[csf('order_id')].'*'.$row[csf('prod_id')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['knitting_source']=$row[csf('knitting_source')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['knitting_company']=$row[csf('knitting_company')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['floor']=$row[csf('floor')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['receive_date']=$row[csf('receive_date')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['location_id']=$row[csf('location_id')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['gsm']=$row[csf('gsm')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['width']=$row[csf('width')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['color_id']=$row[csf('color_id')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['fabric_description_id']=$row[csf('fabric_description_id')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['buyer_id']=$row[csf('buyer_id')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['booking_no']=$row[csf('booking_no')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['booking_without_order']=$row[csf('booking_without_order')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['batch_no']=$row[csf('batch_no')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['extention_no']=$row[csf('extention_no')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['remarks'].=$row[csf('remarks')].',';
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['grey_used_qty']+=$row[csf('grey_used_qty')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['reject_qty']+=$row[csf('reject_qty')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['qc_pass_qty']+=$row[csf('qc_pass_qty')];
				
				$order_ids .=$row[csf('order_id')].",";
				$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
				if($row[csf('booking_without_order')]==1)
				{
					$nonOrdBooking .="'".$row[csf('booking_no')]."',";
				}
			}
			// echo $order_ids.'#<br>';

			$sql_roll_dtls="SELECT a.knitting_source, a.knitting_company, b.floor, a.recv_number, a.receive_date,a.location_id,b.order_id,b.batch_id,b.gsm,b.width,b.grey_used_qty,b.receive_qnty as qc_pass_qty,b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id , c.booking_no, c.booking_without_order, c.batch_no,c.extention_no,b.remarks, b.barcode_no, b.prod_id
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c
			where  a.id=b.mst_id $lcCompany_cond and a.entry_form in(66) and b.batch_id = c.id and c.status_active =1 and a.item_category=2 and a.status_active=1 and a.is_deleted=0  $date_cond $unit_name $knitting_ref_cond  $all_booking_no_cond $batch_cond";
			 //echo $sql_roll_dtls;
			$sql_roll_dtls_result=sql_select($sql_roll_dtls);
			foreach ($sql_roll_dtls_result as $key => $row) 
			{
				if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
                {
                    $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
                    $barcodeno = $row[csf('barcode_no')];
                    // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_name,$barcodeno)";
                    $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_name,$barcodeno)");
                }
			}
			oci_commit($con);

			$batch_sql = sql_select("SELECT b.barcode_no, b.batch_qnty from pro_batch_create_dtls b, tmp_barcode_no c 
			where b.barcode_no=c.barcode_no and b.status_active=1 and c.userid=$user_name");
			$batchBarcodeData = array();
	        foreach ($batch_sql as $row)
	        {
	            $batchBarcodeData[$row[csf("barcode_no")]]+=$row[csf("batch_qnty")];
	        }

	        foreach ($sql_roll_dtls_result as $key => $row) 
			{
				$str_ref=$row[csf('batch_id')].'*'.$row[csf('order_id')].'*'.$row[csf('prod_id')];
				$batch_dtls_qty=$batchBarcodeData[$row[csf("barcode_no")]];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['knitting_source']=$row[csf('knitting_source')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['knitting_company']=$row[csf('knitting_company')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['floor']=$row[csf('floor')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['receive_date']=$row[csf('receive_date')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['location_id']=$row[csf('location_id')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['gsm']=$row[csf('gsm')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['width']=$row[csf('width')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['color_id']=$row[csf('color_id')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['fabric_description_id']=$row[csf('fabric_description_id')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['buyer_id']=$row[csf('buyer_id')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['booking_no']=$row[csf('booking_no')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['booking_without_order']=$row[csf('booking_without_order')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['batch_no']=$row[csf('batch_no')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['extention_no']=$row[csf('extention_no')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['remarks']=$row[csf('remarks')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['grey_used_qty']+=$batch_dtls_qty;
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['reject_qty']+=$row[csf('reject_qty')];
				$production_data_arr[$row[csf('recv_number')]][$str_ref]['qc_pass_qty']+=$row[csf('qc_pass_qty')];

				$order_ids .=$row[csf('order_id')].",";
				$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
				if($row[csf('booking_without_order')]==1)
				{
					$nonOrdBooking .="'".$row[csf('booking_no')]."',";
				}
			}
			// echo $order_ids.'==';die;
			// echo "<pre>";print_r($production_data_arr);


			$r_id=execute_query("delete from tmp_barcode_no where userid=$user_name");


			$fabric_desc_arr=return_library_array("select id, item_description from product_details_master where item_category_id=2","id","item_description");
			$composition_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
			$data_array=sql_select($sql_deter);
			if(count($data_array)>0)
			{
				foreach( $data_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}

			if(empty($sql_dtls_result))
			{
				echo "<span style='color:red; font-size:14;font-weight:bold;'> Data Not Found </span>";
				exit();
			}
			else
			{
				$order_ids = chop($order_ids,",");
				$nonOrdBooking = chop($nonOrdBooking,",");

				$order_ids=implode(",",array_filter(array_unique(explode(',',$order_ids))));
				$nonOrdBooking=implode(",",array_filter(array_unique(explode(',',$nonOrdBooking))));

			    if($nonOrdBooking!="")
			    {
			        $nonOrdBooking=explode(",",$nonOrdBooking);  
			        $nonOrdBooking_chnk=array_chunk($nonOrdBooking,999);
			        $nonOrdBooking_chnk_cond=" and";
			        foreach($nonOrdBooking_chnk as $dtls_id)
			        {
			        	if($nonOrdBooking_chnk_cond==" and")  $nonOrdBooking_chnk_cond.="(a.booking_no in(".implode(',',$dtls_id).")"; else $nonOrdBooking_chnk_cond.=" or a.booking_no in(".implode(',',$dtls_id).")";
			        }
			        $nonOrdBooking_chnk_cond.=")";
			        //$nonOrdBooking_arr = return_library_array("SELECT a.booking_no,a.grouping from wo_non_ord_samp_booking_mst a where a.entry_form_id=140  $ref_company_cond  $nonOrdBooking_chnk_cond  and a.booking_type=4 and a.item_category=2 and a.status_active=1 and a.is_deleted=0", "booking_no", "grouping");
			        $nonOrdBooking_sql="SELECT b.booking_no, c.style_ref_no, a.grouping, b.style_id 
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c 
					where a.booking_no=b.booking_no and b.style_id=c.id and b.entry_form_id=140 and a.booking_type=4 and a.item_category=2 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $ref_company_cond  $nonOrdBooking_chnk_cond
					group by b.booking_no, c.style_ref_no, a.grouping, b.style_id";
					//and b.booking_no='AOPL-SMN-21-00206' 
					// echo $nonOrdBooking_sql;die;
					$nonOrdBooking_data=sql_select($nonOrdBooking_sql);
					$nonOrdBooking_arr=array();$nonOrdStyle_arr=array();
					foreach ($nonOrdBooking_data as $key => $row) 
					{
						$nonOrdBooking_arr[$row[csf('booking_no')]]=$row[csf('grouping')];
						$nonOrdStyle_arr[$row[csf('booking_no')]]=$row[csf('style_ref_no')];
						$sample_requisition_arr[$row[csf('booking_no')]]=$row[csf('style_id')];
					}
					if(!empty($sample_requisition_arr))
					{
						$requisition_id = implode(",", $sample_requisition_arr);
						$reqIdCond = $req_id_cond = "";
						if($db_type==2 && count($sample_requisition_arr)>999)
						{
							$req_id_arr_chunk=array_chunk($sample_requisition_arr,999) ;
							foreach($req_id_arr_chunk as $chunk_arr)
							{
								$reqIdCond.=" a.id in(".implode(",",$chunk_arr).") or ";
							}
							$req_id_cond.=" and (".chop($reqIdCond,'or ').")";
						}
						else
						{
							$req_id_cond=" and a.id in($requisition_id)";
						}
				        // echo "<pre>"; print_r($nonOrdStyle_arr);
						$sample_qty_sql="SELECT a.id, sum(sample_prod_qty) as sample_qty from sample_development_mst a, sample_development_dtls b where a.id=b.sample_mst_id and a.entry_form_id=117 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_id_cond group by a.id"; //and a.id=15910
						$sample_qty_data=sql_select($sample_qty_sql);
						$sample_order_qty=array();
						foreach ($sample_qty_data as $key => $row) 
						{
							$sample_order_qty[$row[csf('id')]]+=$row[csf('sample_qty')];
						}
					}

					$smn_req_qty_arr=array();
					$sql_smple_req="SELECT b.booking_no,  a.grouping, b.style_id, b.grey_fabric, b.fabric_color, b.lib_yarn_count_deter_id
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
					where a.booking_no=b.booking_no and a.booking_type=4 and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
					and b.is_deleted=0  $ref_company_cond  $nonordbooking_chnk_cond";
					// echo $sql_req;die;
					$sql_sample_req_result=sql_select($sql_smple_req);
					foreach( $sql_sample_req_result as $row )
					{
						$smn_req_qty_arr[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color')]]['req_qty']+=$row[csf('grey_fabric')];
					}
			    }

				$order_id_arr=explode(',',$order_ids);
				$all_po_ref_cond = "";
				$all_po_ref_cond2 = "";
				$all_po_ref_cond3 = "";
				if($order_ids != "")
				{
					if($db_type==2 && count($order_id_arr)>999)
					{
						$all_po_ref_chunk=array_chunk($order_id_arr,999) ;
						foreach($all_po_ref_chunk as $chunk_arr)
						{
							$poId.=" id in(".implode(",",$chunk_arr).") or ";	
							$poId2.=" a.po_break_down_id in(".implode(",",$chunk_arr).") or ";	
							$poId3.=" a.po_break_down_id in(".implode(",",$chunk_arr).") or ";	
						}
								
						$all_po_ref_cond.=" and (".chop($poId,'or ').")";					
						$all_po_ref_cond2.=" and (".chop($poId2,'or ').")";					
						$all_po_ref_cond3.=" and (".chop($poId3,'or ').")";					
					}
					else
					{ 			
						$all_po_ref_cond=" and b.id in($order_ids)";
						$all_po_ref_cond2=" and a.po_break_down_id in($order_ids)";
						$all_po_ref_cond3=" and b.order_id in($order_ids)";
					}



					$challan_sql = "SELECT a.id, a.sys_number, b.order_id, b.batch_id, b.product_id from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b
					where a.id=b.mst_id and a.entry_form in(54,67) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_sales=0 and a.company_id=$cbo_company $all_po_ref_cond3 
					group by a.id, a.sys_number, b.order_id, b.batch_id, b.product_id
					union all
					SELECT a.id, a.sys_number, b.order_id, b.batch_id, b.product_id from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b, fabric_sales_order_mst c
					where a.id=b.mst_id and b.order_id=c.id and a.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_sales=1 and a.company_id=$cbo_company $all_po_ref_cond3
					group by a.id, a.sys_number, b.order_id, b.batch_id, b.product_id";

					//echo $sql;//die;
					$challan_result = sql_select($challan_sql);
					$challanNoArr=array();
					foreach ($challan_result as $row) 
					{
						$challanNoArr[$row[csf('order_id')]][$row[csf('batch_id')]][$row[csf('product_id')]]['sys_number']=$row[csf('sys_number')];
					}
					
					unset($challan_result);


					$order_sql=sql_select("SELECT b.id, b.job_no_mst, b.po_number, b.po_quantity, b.file_no, b.grouping as int_ref_no, a.total_set_qnty, a.style_ref_no,b.shipment_date, a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.job_no_mst=a.job_no $lcCompany_cond_2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_po_ref_cond");
					//LISTAGG(b.shipment_date, ', ') WITHIN GROUP (ORDER BY b.shipment_date)  as  shipment_date

					$poArr=array();
					foreach($order_sql as $val)
					{
						$order_arr[$val[csf('id')]]=$val[csf('po_number')];
						$order_qty_arr[$val[csf('id')]]=$val[csf('po_quantity')];

						$po_qty=$val[csf('po_quantity')]*$val[csf('total_set_qnty')];
						$job_no_arr[$val[csf('id')]]['job_no_mst']=$val[csf('job_no_mst')];
						$job_no_arr[$val[csf('id')]]['po_quantity']=$po_qty;
						$job_no_arr[$val[csf('id')]]['style_ref_no']=$val[csf('style_ref_no')];
						$job_no_arr[$val[csf('id')]]['buyer_name']=$val[csf('buyer_name')];
						$job_no_arr[$val[csf('id')]]['file_no']=$val[csf('file_no')];
						$job_no_arr[$val[csf('id')]]['int_ref_no']=$val[csf('int_ref_no')];
						$job_no_arr[$val[csf('id')]]['shipment_date']=$val[csf('shipment_date')];
						array_push($poArr,$val[csf('id')]);
					}


					$po_sql=	"SELECT a.po_breakdown_id,a.prod_id,c.job_no FROM order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e WHERE a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.item_category=1 and a.trans_type=2 and e.issue_purpose=1 and e.entry_form=3 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.company_name='$cbo_company' ".where_con_using_array($poArr,0,"a.po_breakdown_id")." GROUP BY a.po_breakdown_id,a.prod_id,c.job_no ORDER BY c.job_no  ";

					//echo $prod_dtls_sql;
					$po_sql_result=sql_select($po_sql);	 
					
					$order_id_arr = array();
					$prod_id_arr = array();
					foreach ($po_sql_result as $rows) 
					{
						$order_id_arr[$rows[csf('po_breakdown_id')]]['prod_id']=$rows[csf('prod_id')];
						array_push($prod_id_arr,$rows[csf('prod_id')]);
					}
					unset($po_sql_result);
				
					$product_array=array();
					$prod_dtls_sql="Select id,  brand from product_details_master where item_category_id=1 and company_id=$cbo_company and status_active=1 and is_deleted=0 ".where_con_using_array($prod_id_arr,0,"id")." ";
					//echo $prod_dtls_sql;
					$result_prod_dtls_sql=sql_select($prod_dtls_sql);
					foreach($result_prod_dtls_sql as $rows)
					{
						$product_array[$rows[csf('id')]]['brand']=$rows[csf('brand')];
					}
					unset($result_prod_dtls_sql);


					$req_qty_arr=array();
					$sql_req="SELECT a.booking_no, a.po_break_down_id, a.fabric_color_id, a.gsm_weight, a.dia_width, b.lib_yarn_count_deter_id, sum(a.grey_fab_qnty) as req_qty 
					from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b 
					where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_po_ref_cond2
					group by a.booking_no, a.po_break_down_id, a.fabric_color_id, a.gsm_weight, a.dia_width, b.lib_yarn_count_deter_id"; // and a.booking_no='RpC-Fb-22-00027'
					// echo $sql_req;die;
					$sql_req_result=sql_select($sql_req);
					foreach( $sql_req_result as $row )
					{
						$req_qty_arr[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]]['req_qty']+=$row[csf('req_qty')];
					}
				}
				// echo "<pre>";print_r($req_qty_arr);die;

				if(!empty($batch_id_arr))
				{					
					$all_batch_ref_cond = "";$batchId="";
					$batchall_id=implode(",",array_filter(array_unique($batch_id_arr)));

					$batch_id_arr=explode(',',$batchall_id);

					if($db_type==2 && count($batch_id_arr)>999)
					{
						$all_batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
						foreach($all_batch_id_arr_chunk as $chunk_arr)
						{
							$batchId.=" a.id in(".implode(",",$chunk_arr).") or ";	
						}
								
						$all_batch_ref_cond.=" and (".chop($batchId,'or ').")";					
					}
					else
					{ 			
						$all_batch_ref_cond=" and a.id in($batchall_id)";
					}
				}

				$sql=sql_select("select f.process_end_date,a.id from pro_batch_create_mst a, pro_fab_subprocess f where   f.batch_id=a.id $lcCompany_cond  and  f.entry_form=35 and f.load_unload_id=1 and  f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_batch_ref_cond");
				$proce_start_date=array();
				foreach($sql as $p_date)
				{
					$proce_start_date[$p_date[csf('id')]]=$p_date[csf('process_end_date')];
				}

				$sql_batch_sql="SELECT a.id,b.po_id, b.batch_qnty , a.entry_form, c.gsm as gsm_7, d.gsm as gsm_66, c.fabric_description_id, d.detarmination_id from pro_batch_create_mst a, pro_batch_create_dtls b left join pro_finish_fabric_rcv_dtls c on b.dtls_id = c.id left join product_details_master d on b.prod_id = d.id where  a.id=b.mst_id $lcCompany_cond and  a.status_active=1 and a.is_deleted=0 $all_batch_ref_cond";

				$batch_no_arr=array();
				$sql_batch_result=sql_select($sql_batch_sql);

				foreach($sql_batch_result as $val)
				{
					if($val[csf('entry_form')] == 7)
					{
						$batch_qnty_arr[$val[csf('id')]][$val[csf('po_id')]][$val[csf('gsm_7')]][$val[csf('fabric_description_id')]]['batch_qnty'] += $val[csf('batch_qnty')];
					}
					else
					{
						$batch_qnty_arr[$val[csf('id')]][$val[csf('po_id')]][$val[csf('gsm_66')]][$val[csf('detarmination_id')]]['batch_qnty'] += $val[csf('batch_qnty')];
						$sample_batch_qnty_arr[$val[csf('id')]][$val[csf('gsm_66')]][$val[csf('detarmination_id')]]['batch_qnty'] += $val[csf('batch_qnty')];
					}
				}
			}

			$i=1;
			foreach ($production_data_arr as $recv_number => $recv_number_data)
			{
				foreach($recv_number_data as $str_ref => $row)
				{
					$str_ref_data = explode("*", $str_ref);
					$batch_id=$str_ref_data[0];
					$order_id=$str_ref_data[1];
					$prod_id=$str_ref_data[2];

				 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$order_number_arr='';
					$yrnbrand='';
					$job_no='';
					$buyer='';
					$po_qty='';
					$shipment_date='';
					$style='';$file_no=$int_ref_no='';$batch_qty="";
					$order_number=array_unique(explode(",",$order_id));
					foreach($order_number as $po_id)
					{
						if($po_id>0)
						{
							if($yrnbrand=="") $yrnbrand = $brand_arr[$product_array[$order_id_arr[$po_id]['prod_id']]['brand']]; else $yrnbrand =",".$brand_arr[$product_array[$order_id_arr[$po_id]['prod_id']]['brand']];
							
							if($order_number_arr=="") $order_number_arr=$order_arr[$po_id]; else $order_number_arr.=",".$order_arr[$po_id];
							if($job_no=="") $job_no=$job_no_arr[$po_id]['job_no_mst']; else $job_no.=",".$job_no_arr[$po_id]['job_no_mst'];

							if($po_qty=="") $po_qty=$job_no_arr[$po_id]['po_quantity']; else $po_qty.=",".$job_no_arr[$po_id]['po_quantity'];
							if($style=="") $style=$job_no_arr[$po_id]['style_ref_no']; else $style.=",".$job_no_arr[$po_id]['style_ref_no'];
							if($buyer=="") $buyer=$job_no_arr[$po_id]['buyer_name']; else $buyer.=",".$job_no_arr[$po_id]['buyer_name'];

							$file_no.=$job_no_arr[$po_id]['file_no'].",";
							$int_ref_no.=$job_no_arr[$po_id]['int_ref_no'].",";

							$batch_qty += $batch_qnty_arr[$batch_id][$po_id][$row['gsm']][$row['fabric_description_id']]['batch_qnty'];

							if($shipment_date=="") $shipment_date= change_date_format($job_no_arr[$po_id]['shipment_date']); else $shipment_date.=",". change_date_format($job_no_arr[$po_id]['shipment_date']);
						}
						else
						{
							$style=$nonOrdStyle_arr[$row['booking_no']];
							$po_qty=$sample_order_qty[$sample_requisition_arr[$row['booking_no']]];
							$batch_qty += $sample_batch_qnty_arr[$batch_id][$row['gsm']][$row['fabric_description_id']]['batch_qnty'];
						}
					}
					$shipmentDate=explode(',', $shipment_date);
					$shipmentDate=$shipmentDate[0];

					$file_no=implode(',',array_unique(explode(',',chop($file_no,','))));
					$int_ref_no=implode(',',array_unique(explode(',',chop($int_ref_no,','))));
					$yrnbrand_cond=implode(',',array_unique(explode(',',chop($yrnbrand,','))));
					$job_mst_id=array_unique(explode(",",$job_no));
					$job_num='';
					foreach($job_mst_id as $job_id)
					{	
						if($job_num=="") $job_num=$job_id; else $job_num.=", ".$job_id;
					}
					$buyer_id_arr=array_unique(explode(",",$buyer));
					 //print_r($buyer_id_arr);
					$buyer_name='';
					foreach($buyer_id_arr as $buyerid)
					{	
						if($buyer_name=="") $buyer_name=$buyer_library[$buyerid]; else $buyer_name.=", ".$buyer_library[$buyerid];
					}
					$style_arr=array_unique(explode(",",$style));
					$style_ref_arr='';
					foreach($style_arr as $style_ref)
					{	
						if($style_ref_arr=="") $style_ref_arr=$style_ref; else $style_ref_arr.=", ".$style_ref;
					}
					$order_qty_arr_m='';
					$order_qty_arr=explode(",",$po_qty);
					foreach($order_qty_arr as $po_qty)
					{	
						$order_qty_arr_m+=$po_qty;
					}
					$batch_no=$row['batch_no'];
					$extention_no=$row['extention_no'];
					$booking_no=$row['booking_no'];

					if($row['booking_without_order']==0)
					{
						$req_qty=$req_qty_arr[$row['booking_no']][$row['fabric_description_id']][$row['color_id']]['req_qty'];
					}
					else
					{
						$req_qty=$smn_req_qty_arr[$row['booking_no']][$row['fabric_description_id']][$row['color_id']]['req_qty'];
					}

					//$batch_qty=$batch_no_arr[$row[csf('batch_id')]]['batch_qnty'];
					//$extention_no=$batch_no_arr[$row[csf('batch_id')]]['extention_no'];
					//$process_loss_qty=$batch_qty-$row[csf('qc_pass_qty')];
					//$process_loss_qty_percent=($process_loss_qty/$batch_qty)*100;

					$knitting_company="";
					if($row['knitting_source'] == 1)
					{
						$knitting_company = $company_arr[$row['knitting_company']];
					}else{
						$knitting_company = $supplier_arr[$row['knitting_company']];
					}
					
					?>
				
		            <tr  bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                <td  width="40"><? echo $i;?> </td>
		                <td  width="100" class="word_wrap_break"><p><? echo $knitting_company;?></p> </td>
		                <td  width="100"><p><? echo $floor_arr[$row['floor']];?></p> </td>
		                <td  width="120"><p><? echo $recv_number;?></p> </td>
		                <td width="80"><p><? echo change_date_format($row['receive_date']);?></p>  </td>
		                <td width="100"><p><? echo $challanNoArr[$order_id][$batch_id][$prod_id]['sys_number'];?></p>  </td>
		                <td width="80"><p><? if($proce_start_date[$batch_id]!="") echo change_date_format($proce_start_date[$batch_id]);?></p>  </td>
		                <td width="70"><p><?  if($proce_start_date[$batch_id]!="") echo datediff("d",$proce_start_date[$batch_id],$row['receive_date']);?></p>  </td>
		                <td width="100" class="word_wrap_break"><p><? echo $shipmentDate;?></p> </td>
		                <td width="80" class="word_wrap_break"><p><? echo $location_library[$row['location_id']];?></p> </td>
		                <td  width="120"><p><? echo $booking_no;?></p>  </td>
		                <td  width="120"><p><? echo $job_num;?></p>  </td>
		                <td  width="100"><p><? 
						// 	echo $buyer_library[$row['buyer_id']];
						echo $buyer_name;
						?></p> </td>
		                <td width="100" class="word_wrap_break"><p><? echo $style_ref_arr;?></p> </td>
		                <td width="100" class="word_wrap_break"><p><? echo $order_number_arr;?></p>  </td>
		                <td width="70" class="word_wrap_break"><p><? echo $file_no;?></p>  </td>
		                <td width="80" class="word_wrap_break"><p><? if($row['booking_without_order']==1){ echo $nonOrdBooking_arr[$row['booking_no']]; }else{echo $int_ref_no;} ?></p></td>
		                <td width="80" align="right"><p><? echo number_format($order_qty_arr_m);?></p>  </td>
		                <td width="250" class="word_wrap_break" title="<? echo $row['fabric_description_id']; ?>"><p><? echo $composition_arr[$row['fabric_description_id']];?></p> </td>
		                <td width="100" class="word_wrap_break"> <p><? echo trim($yrnbrand_cond,',');?></p> </td>
		                <td width="60"> <p><? echo $row['gsm'];?></p> </td>
		                <td width="60"> <p><? echo $row['width'];?></p> </td>
		                <td width="100" class="word_wrap_break" title="<? echo $row['color_id']; ?>"><p><? echo $color_library[$row['color_id']];?></p> </td>

		                <td width="80" class="word_wrap_break"><p><? echo number_format($req_qty,2);?></p> </td>

		                <td width="100" class="word_wrap_break"><p><? echo $batch_no; ?></p>  </td>
		                 <td width="100"><p><? echo $extention_no; ?></p>  </td>
		                <td width="80"  align="right" title="<? echo 'Batch ID:'.$batch_id; ?>"> <p><? echo number_format($batch_qty,2); ?></p> </td>
		                <td width="80"  align="right" id="total_qc_qty"><p><? echo number_format($row['qc_pass_qty'],2);?></p>  </td>
		                <td width="80"  align="right" id="total_grey_qty"><p><? echo number_format($row['grey_used_qty'],2);?></p>  </td>
		                <td width="80"  align="right" id="total_rj_qty"><p><? echo number_format($row['reject_qty'],2);?></p>  </td>
		                <td width="" class="word_wrap_break"><p><?php echo chop($row['remarks'],','); ?></p></td>
		            </tr>

		            <?
					$total_qc_qty+=$row['qc_pass_qty'];
					$tot_grey_used_qty+=$row['grey_used_qty'];
					$total_batch_qty+=$batch_qty;
					//$total_process_loss_qty+=$process_loss_qty;
					$total_rj_qty+=$row['reject_qty'];
					$i++;
				}
			}
			?>
	    	</table>
		    <table class="rpt_table" width="2970" cellpadding="0" cellspacing="0" border="1" rules="all">
		    <tfoot>
		        <tr>
		            <th width="40">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="80">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="80">&nbsp;</th>
		            <th width="70">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="80">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="70">&nbsp;</th>
		            <th width="80">&nbsp;</th>
		            <th width="80">&nbsp;</th>
		            <th width="250">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="60">&nbsp;</th>
		            <th width="60">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="80"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="80" id="value_total_batch_qty"><? echo number_format($total_batch_qty,2); ?></th>
		            <th width="80" id="value_total_qc_qty"><? echo number_format($total_qc_qty,2); ?></th>
		            <th width="80" id="value_total_grey_used_qty"><? echo number_format($tot_grey_used_qty,2); ?></th>
		            <th width="80" id="value_total_rj_qty"><? echo number_format($total_rj_qty,2); ?></th>
		            <th width="">&nbsp;</th>
		        </tr>
		    </tfoot>
		    </table>
	   		</div>
	   	</fieldset>
	  	</div>  
		<?
		$html = ob_get_contents();
	    ob_clean();
	    foreach (glob("*.xls") as $filename) {
	    	@unlink($filename);
	    }
	    //---------end------------//
	    $name=time();
	    $filename=$user_name."_".$name.".xls";
	    $create_new_doc = fopen($filename, 'w');
	    $is_created = fwrite($create_new_doc, $html);
	    echo "$html####$filename";

		exit();
	} // Show End

	if ($type==2) // Show 2 Start
	{
		if($txt_order_no!="" || $txt_job_no!="" ||  $txt_file_no!="" ||  $txt_int_ref_no!="" || $txt_booking_no!="" && $cbo_company !=0)
		{
			if($cbo_production_type==1 || $cbo_production_type==0)
			{
				$sql_po_1 = "SELECT b.id, b.job_no_mst, b.po_number, b.po_quantity, b.file_no, b.grouping as int_ref_no, a.total_set_qnty, a.style_ref_no, a.buyer_name , c.booking_no, 1 as type from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c 
				where b.job_no_mst=a.job_no and b.id = c.po_break_down_id and a.company_name = $cbo_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $file_cond $ref_cond $order_cond_1  $job_no_cond_1 $booking_no_cond_1";
			}
			if($txt_order_no="" || $txt_job_no="" ||  $txt_file_no="" ||  $txt_int_ref_no="" || $txt_booking_no="")
			{
				if($cbo_production_type==2 || $cbo_production_type==0)
				{
					if($sql_po_1 != "")
					{
						$sql_po_1 .= " union all ";
					}
		
					$sql_po_1 .= " select c.id, null as job_no_mst, null as po_number, null as po_quantity, null as file_no, null as int_ref_no, null as total_set_qnty, null as style_ref_no, c.buyer_id as buyer_name, c.booking_no, 2 as type from wo_non_ord_samp_booking_mst c where c.company_id = $cbo_company and c.status_active =1 and c.is_deleted=0 $booking_no_cond_1";
				}
				
			}

			$sql_po=sql_select($sql_po_1);

			foreach($sql_po as $row)
			{
				if($row[csf('type')] == 1)	
				{
					$po_qty=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
					$job_no_arr[$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
					$job_no_arr[$row[csf('id')]]['po_quantity']=$po_qty;
					$job_no_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$job_no_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
					$job_no_arr[$row[csf('id')]]['file_no']=$row[csf('file_no')];
					$job_no_arr[$row[csf('id')]]['int_ref_no']=$row[csf('int_ref_no')];
					if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];

				}
				else
				{
					if($all_non_book_id=="") $all_non_book_id=$row[csf('id')]; else $all_non_book_id.=",".$row[csf('id')]; 
				}

				$all_booking_no_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
			}
		}
		
		if(count($all_booking_no_arr)>0)
		{
			//$all_booking_nos= "'".implode("','",$all_booking_no_arr)."'";
			$all_booking_no_cond=""; $bookCond=""; 
			if($db_type==2 && count($all_booking_no_arr)>999)
			{
				$all_booking_no_chunk=array_chunk($all_booking_no_arr,999) ;
				foreach($all_booking_no_chunk as $chunk_arr)
				{
					/*echo "<pre>";
					print_r(array_unique($chunk_arr));*/
					$chunk_arr_value="'".implode("','",$chunk_arr)."'";
					$bookCond.="  c.booking_no in($chunk_arr_value) or ";	
				}
				$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";
			}
			else
			{
				$all_booking_nos= "'".implode("','",$all_booking_no_arr)."'";
				$all_booking_no_cond=" and c.booking_no in($all_booking_nos)";	 
			}
		}

		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
		$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
		$floor_arr=return_library_array( "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id","floor_room_rack_name");	

		ob_start();
		?>	

		<div>
	  	<style>
			.word_wrap_break{
				word-wrap: break-word;
				word-break: break-all;
			}
	    </style>
	  	<fieldset style="width:2610px;">
	        <table width="1660px" cellpadding="0" cellspacing="0" id="caption" align="center">
	            <tr>
	               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$cbo_company];?></strong></td>
	            </tr> 
	            <tr>  
	               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr>  
	               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
	            </tr>  
	        </table>
	        
	        <?
			
			if ($cbo_company !=0) {$lcCompany_cond=" and a.company_id=$cbo_company";}else{$lcCompany_cond="";}
			if ($cbo_company !=0) {$lcCompany_cond_2=" and a.company_name=$cbo_company";}else{$lcCompany_cond_2="";}
			$fabric_desc_arr=return_library_array("select id, item_description from product_details_master where item_category_id=2","id","item_description");
			$composition_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
			$data_array=sql_select($sql_deter);
			if(count($data_array)>0)
			{
				foreach( $data_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}

			$sql_dtls="SELECT a.knitting_source, a.knitting_company, b.floor, a.recv_number, a.receive_date,a.location_id,b.order_id,b.batch_id,b.gsm,b.width,b.grey_used_qty,b.receive_qnty as qc_pass_qty,b.reject_qty, c.color_range_id, b.color_id,b.fabric_description_id,b.buyer_id , c.booking_no, c.booking_without_order, c.batch_no,c.extention_no,b.remarks 
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c
			where  a.id=b.mst_id $lcCompany_cond and a.entry_form in(7,66) and b.batch_id = c.id and c.status_active =1 and a.item_category=2 and a.status_active=1 and a.is_deleted=0  $date_cond $unit_name $knitting_ref_cond  $all_booking_no_cond $batch_cond
			group by a.knitting_source, a.knitting_company, b.floor, a.recv_number,a.receive_date,a.location_id,b.batch_id,b.order_id,b.gsm,b.width,b.grey_used_qty,b.receive_qnty,b.reject_qty, c.color_range_id, b.color_id,b.fabric_description_id,b.buyer_id , c.booking_no, c.booking_without_order, c.batch_no,c.extention_no,b.remarks
			order by a.recv_number";

			//echo $sql_dtls; 
			
			$sql_dtls_result=sql_select($sql_dtls);

			if(empty($sql_dtls_result))
			{
				echo "<span style='color:red; font-size:14;font-weight:bold;'> Data Not Found </span>";
				exit();
			}
			else
			{
				$order_ids = "";$nonOrdBooking= "";
				foreach($sql_dtls_result as $row)
				{
					$order_ids .=$row[csf('order_id')].",";

					$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
					if($row[csf('booking_without_order')]==1)
					{
						$nonOrdBooking .="'".$row[csf('booking_no')]."',";
					}
				}

				$order_ids = chop($order_ids,",");
				$nonOrdBooking = chop($nonOrdBooking,",");

				$order_ids=implode(",",array_filter(array_unique(explode(',',$order_ids))));
				$nonOrdBooking=implode(",",array_filter(array_unique(explode(',',$nonOrdBooking))));


			    if($nonOrdBooking!="")
			    {
			        $nonOrdBooking=explode(",",$nonOrdBooking);  
			        $nonOrdBooking_chnk=array_chunk($nonOrdBooking,999);
			        $nonOrdBooking_chnk_cond=" and";
			        foreach($nonOrdBooking_chnk as $dtls_id)
			        {
			        	if($nonOrdBooking_chnk_cond==" and")  $nonOrdBooking_chnk_cond.="(a.booking_no in(".implode(',',$dtls_id).")"; else $nonOrdBooking_chnk_cond.=" or a.booking_no in(".implode(',',$dtls_id).")";
			        }
			        $nonOrdBooking_chnk_cond.=")";
			        //$nonOrdBooking_arr = return_library_array("SELECT a.booking_no,a.grouping from wo_non_ord_samp_booking_mst a where a.entry_form_id=140  $ref_company_cond  $nonOrdBooking_chnk_cond  and a.booking_type=4 and a.item_category=2 and a.status_active=1 and a.is_deleted=0", "booking_no", "grouping");
			        $nonOrdBooking_sql="SELECT b.booking_no, c.style_ref_no, a.grouping, b.style_id 
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c 
					where a.booking_no=b.booking_no and b.style_id=c.id and b.entry_form_id=140 and a.booking_type=4 and a.item_category=2 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $ref_company_cond  $nonOrdBooking_chnk_cond
					group by b.booking_no, c.style_ref_no, a.grouping, b.style_id";
					//and b.booking_no='AOPL-SMN-21-00206' 
					$nonOrdBooking_data=sql_select($nonOrdBooking_sql);
					$nonOrdBooking_arr=array();$nonOrdStyle_arr=array();
					foreach ($nonOrdBooking_data as $key => $row) 
					{
						$nonOrdBooking_arr[$row[csf('booking_no')]]=$row[csf('grouping')];
						$nonOrdStyle_arr[$row[csf('booking_no')]]=$row[csf('style_ref_no')];
						$sample_requisition_arr[$row[csf('booking_no')]]=$row[csf('style_id')];
					}
					if(!empty($sample_requisition_arr))
					{
						$requisition_id = implode(",", $sample_requisition_arr);
						$reqIdCond = $req_id_cond = "";
						if($db_type==2 && count($sample_requisition_arr)>999)
						{
							$req_id_arr_chunk=array_chunk($sample_requisition_arr,999) ;
							foreach($req_id_arr_chunk as $chunk_arr)
							{
								$reqIdCond.=" a.id in(".implode(",",$chunk_arr).") or ";
							}
							$req_id_cond.=" and (".chop($reqIdCond,'or ').")";
						}
						else
						{
							$req_id_cond=" and a.id in($requisition_id)";
						}
				        // echo "<pre>"; print_r($nonOrdStyle_arr);
						$sample_qty_sql="SELECT a.id, sum(sample_prod_qty) as sample_qty from sample_development_mst a, sample_development_dtls b where a.id=b.sample_mst_id and a.entry_form_id=117 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_id_cond group by a.id"; //and a.id=15910
						$sample_qty_data=sql_select($sample_qty_sql);
						$sample_order_qty=array();
						foreach ($sample_qty_data as $key => $row) 
						{
							$sample_order_qty[$row[csf('id')]]+=$row[csf('sample_qty')];
						}
					}
			    }

				$order_id_arr=explode(',',$order_ids);
				$all_po_ref_cond = "";
				if($order_ids != "")
				{
					if($db_type==2 && count($order_id_arr)>999)
					{
						$all_po_ref_chunk=array_chunk($order_id_arr,999) ;
						foreach($all_po_ref_chunk as $chunk_arr)
						{
							$poId.=" id in(".implode(",",$chunk_arr).") or ";	
						}
								
						$all_po_ref_cond.=" and (".chop($poId,'or ').")";					
					}
					else
					{ 			
						$all_po_ref_cond=" and b.id in($order_ids)";
					}

					$order_sql=sql_select("select b.id, b.job_no_mst, b.po_number, b.po_quantity, b.file_no, b.grouping as int_ref_no, a.total_set_qnty, a.style_ref_no, a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.job_no_mst=a.job_no $lcCompany_cond_2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_po_ref_cond");

					foreach($order_sql as $val)
					{
						$order_arr[$val[csf('id')]]=$val[csf('po_number')];
						$order_qty_arr[$val[csf('id')]]=$val[csf('po_quantity')];

						$po_qty=$val[csf('po_quantity')]*$val[csf('total_set_qnty')];
						$job_no_arr[$val[csf('id')]]['job_no_mst']=$val[csf('job_no_mst')];
						$job_no_arr[$val[csf('id')]]['po_quantity']=$po_qty;
						$job_no_arr[$val[csf('id')]]['style_ref_no']=$val[csf('style_ref_no')];
						$job_no_arr[$val[csf('id')]]['buyer_name']=$val[csf('buyer_name')];
						$job_no_arr[$val[csf('id')]]['file_no']=$val[csf('file_no')];
						$job_no_arr[$val[csf('id')]]['int_ref_no']=$val[csf('int_ref_no')];
					}
				}


				if(!empty($batch_id_arr))
				{
					
					$all_batch_ref_cond = "";$batchId="";
					$batchall_id=implode(",",array_filter(array_unique($batch_id_arr)));

					$batch_id_arr=explode(',',$batchall_id);

					if($db_type==2 && count($batch_id_arr)>999)
					{
						$all_batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
						foreach($all_batch_id_arr_chunk as $chunk_arr)
						{
							$batchId.=" a.id in(".implode(",",$chunk_arr).") or ";	
						}
								
						$all_batch_ref_cond.=" and (".chop($batchId,'or ').")";					
					}
					else
					{ 			
						$all_batch_ref_cond=" and a.id in($batchall_id)";
					}
				}

				$sql=sql_select("select f.process_end_date,a.id from pro_batch_create_mst a, pro_fab_subprocess f where   f.batch_id=a.id $lcCompany_cond  and  f.entry_form=35 and f.load_unload_id=1 and  f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_batch_ref_cond");
				$proce_start_date=array();
				foreach($sql as $p_date)
				{
					$proce_start_date[$p_date[csf('id')]]=$p_date[csf('process_end_date')];
				}

				$sql_batch_sql="SELECT a.id,b.po_id, b.batch_qnty , a.entry_form, c.gsm as gsm_7, d.gsm as gsm_66, c.fabric_description_id, d.detarmination_id from pro_batch_create_mst a, pro_batch_create_dtls b left join pro_finish_fabric_rcv_dtls c on b.dtls_id = c.id left join product_details_master d on b.prod_id = d.id where  a.id=b.mst_id $lcCompany_cond and  a.status_active=1 and a.is_deleted=0 $all_batch_ref_cond";

				$batch_no_arr=array();
				$sql_batch_result=sql_select($sql_batch_sql);

				foreach($sql_batch_result as $val)
				{
					if($val[csf('entry_form')] == 7)
					{
						$batch_qnty_arr[$val[csf('id')]][$val[csf('po_id')]][$val[csf('gsm_7')]][$val[csf('fabric_description_id')]]['batch_qnty'] += $val[csf('batch_qnty')];
					}
					else
					{
						$batch_qnty_arr[$val[csf('id')]][$val[csf('po_id')]][$val[csf('gsm_66')]][$val[csf('detarmination_id')]]['batch_qnty'] += $val[csf('batch_qnty')];
						$sample_batch_qnty_arr[$val[csf('id')]][$val[csf('gsm_66')]][$val[csf('detarmination_id')]]['batch_qnty'] += $val[csf('batch_qnty')];
					}
				}
			}
			?>

			<!-- =============== Summary Start ======================== -->
			<div style="padding-bottom:100px;">
		 	<div style="float: left; margin-left: 20px;">
		 	<table width="370" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th colspan="3">Color Range Wise Finish Production Summary</th>
					</tr>
					<tr>
						<th>Details </th>
						<th>Prod. Qty. </th>
						<th>%</th>
					</tr>
				</thead>
				<tbody>
					<?

					$color_range_arr=array();					
					foreach($sql_dtls_result as $row)
					{
						$color_range_arr[$row[csf("color_range_id")]]['qc_pass_qty']+=$row[csf("qc_pass_qty")];
					}
					
					$total_color_finish_prod_qty = 0;
					$total_color_finish_prod_qty_percentage =0;
					foreach ($color_range_arr as $color_id => $color_data) 
					{
						foreach ($color_data as $batch_id => $finish_data) 
						{
							$total_color_finish_prod_qty_percentage+=$finish_data;
						}
					}
					$j=1;
					// echo "<pre>";print_r($total_color_finish_prod_qty_percentage);die;
					foreach ($color_range_arr as $color_id => $color_data) 
					{
						foreach ($color_data as $batch_id => $finish_data) 
						{
							if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$total_color_finish_prod_qty+=$finish_data;
                			?>
							<tr bgcolor="<? echo $bgcolor;  ?>" >								
								<td width="150">
								<? echo $color_range[$color_id] ; ?>
								</td>
								<td width="100" align="right" ><? echo number_format($finish_data,2,'.',','); ?></td>
								<td align="right" >
								<?									 
								$colorrangedata = ($finish_data/$total_color_finish_prod_qty_percentage)*100;
								echo number_format($colorrangedata,2);
								?>								
								</td>
							</tr>
							<?
							$j++;
							$colorrangetotpercen +=$colorrangedata;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th align="right">Total </th>
						<th align="right"><b><? echo number_format($total_color_finish_prod_qty,2,'.',''); ?></b> </th>
						<th align="right"><? echo number_format($colorrangetotpercen,2,'.','').'%'; ?></th>
					</tr>
				</tfoot>
			</table>
		 	</div>

		 	<div style="float: left; margin-left: 20px;">
            <table width="370" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th colspan="3">Buyer Wise Finish Production Summary</th>
					</tr>
					<tr>
						<th>Details </th>
						<th>Prod. Qty. </th>
						<th>%</th>
					</tr>
				</thead>
				<tbody>
					<?
					$buyer_wise_arr=array();
					foreach($sql_dtls_result as $row)
					{
						$buyer_wise_arr[$row[csf('buyer_id')]]['qc_pass_qty']+=$row[csf("qc_pass_qty")];
					}
					
					$total_buyer_fin_prod_qty = 0;
					$total_buyer_fin_qty_percentage =0;
					foreach ($buyer_wise_arr as $buyer_id => $buyer_data) 
					{									
						foreach ($buyer_data as $finis_qty => $fin_prod_data) 
						{
							$total_buyer_fin_qty_percentage+=$fin_prod_data;
						}
					}
					$j=1;
					foreach ($buyer_wise_arr as $buyer_id => $buyer_data) 
					{
						foreach ($buyer_data as $finis_qty => $fin_prod_data) 
						{
							if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_buyer_fin_prod_qty+=$fin_prod_data;
							
                			?>
						 	<tr bgcolor="<? echo $bgcolor;  ?>" >
								<td width="150"><? echo $buyer_arr[$buyer_id]; ?></td>
								<td width="100" align="right" ><? echo number_format($fin_prod_data,2,'.',','); ?></td>
								<td align="right" >
								<?
									$buyerdata = ($fin_prod_data/$total_buyer_fin_qty_percentage)*100;
									echo number_format($buyerdata,2);
								 ?>
								</td>
							</tr>
							<?
							$j++;
							$buyertotalpercen +=$buyerdata;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th align="right">Total </th>
						<th align="right"><b><? echo number_format($total_buyer_fin_prod_qty,2,'.',''); ?></b> </th>
						<th align="right"><? echo number_format($buyertotalpercen,2,'.','').'%'; ?></th>
					</tr>
				</tfoot>
			</table>
		 	</div>
		 	<br/>
			</div>
			<!-- =============== Summary End ======================== -->

			<!-- =============== Details Data Show Start ======================== -->
			<table  width="2690" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
	        	<thead>
	                <th width="40">SL</th>
	                <th width="100">Working Company</th>
	                <th width="100">Working Floor</th>
	                <th width="120">Production ID</th>
	                <th width="80">Production Date</th>
	                <th width="80">Dying Start Date</th>
	                <th width="70">Execution Days</th>
	                <th width="80">Unit Name</th>
	                <th width="120">Booking No</th>
	                <th width="120">Job No</th>
	                <th width="100">Buyer Name</th>
	                <th width="100">Style Ref.</th>
	                <th width="100">Order No</th>
	                <th width="70">File No</th>
	                <th width="80">Int. Ref. No</th>
	                <th width="80">Order Qty(Pcs)</th>
	                <th width="250">Fabric Description</th>
	                <th width="60">F.GSM</th>
	                <th width="60">Fabric Dia</th>
	                <th width="100">Color Range</th>
	                <th width="100">Fabric Color</th>
	                <th width="100">Batch/Lot No</th>
	                <th width="100">Extension Number</th>
	                <th width="80">Batch Qty.</th>
	                <th width="80">QC Pass Qty</th>
	                <th width="80">Grey Used Qty</th>
	                <th width="80">Reject Qty</th>
	                <th width="">Remarks</th>
	            </thead>
	        </table>
	        <div style="width:2720px; overflow-y:scroll; max-height:350px;" id="scroll_body">
	        <table width="2690" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="tbl_dyeing">

			<?
			$i=1;	
			foreach($sql_dtls_result as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_id=$row[csf('order_id')];

				$order_number_arr='';
				$job_no='';
				$buyer='';
				$po_qty='';
				$style='';$file_no=$int_ref_no='';$batch_qty="";
				$order_number=array_unique(explode(",",$order_id));
				foreach($order_number as $po_id)
				{	if($po_id>0)
					{
						if($order_number_arr=="") $order_number_arr=$order_arr[$po_id]; else $order_number_arr.=",".$order_arr[$po_id];
						if($job_no=="") $job_no=$job_no_arr[$po_id]['job_no_mst']; else $job_no.=",".$job_no_arr[$po_id]['job_no_mst'];

						if($po_qty=="") $po_qty=$job_no_arr[$po_id]['po_quantity']; else $po_qty.=",".$job_no_arr[$po_id]['po_quantity'];
						if($style=="") $style=$job_no_arr[$po_id]['style_ref_no']; else $style.=",".$job_no_arr[$po_id]['style_ref_no'];
						$file_no.=$job_no_arr[$po_id]['file_no'].",";
						$int_ref_no.=$job_no_arr[$po_id]['int_ref_no'].",";

						$batch_qty += $batch_qnty_arr[$row[csf('batch_id')]][$po_id][$row[csf('gsm')]][$row[csf('fabric_description_id')]]['batch_qnty'];
					}
					else
					{
						$style=$nonOrdStyle_arr[$row[csf('booking_no')]];
						$po_qty=$sample_order_qty[$sample_requisition_arr[$row[csf('booking_no')]]];
						$batch_qty += $sample_batch_qnty_arr[$row[csf('batch_id')]][$row[csf('gsm')]][$row[csf('fabric_description_id')]]['batch_qnty'];
					}
				}

				$file_no=implode(',',array_unique(explode(',',chop($file_no,','))));
				$int_ref_no=implode(',',array_unique(explode(',',chop($int_ref_no,','))));
				$job_mst_id=array_unique(explode(",",$job_no));
				$job_num='';
				foreach($job_mst_id as $job_id)
				{	
					if($job_num=="") $job_num=$job_id; else $job_num.=", ".$job_id;
				}
				$buyer_id_arr=array_unique(explode(",",$buyer));
				 //print_r($buyer_id_arr);
				$buyer_id='';
				foreach($buyer_id_arr as $buyerid)
				{	
					if($buyer_id=="") $buyer_id=$buyerid; else $buyer_id.=", ".$buyerid;
				}
				$style_arr=array_unique(explode(",",$style));
				$style_ref_arr='';
				foreach($style_arr as $style_ref)
				{	
					if($style_ref_arr=="") $style_ref_arr=$style_ref; else $style_ref_arr.=", ".$style_ref;
				}
				$order_qty_arr_m='';
				$order_qty_arr=explode(",",$po_qty);
				foreach($order_qty_arr as $po_qty)
				{	
					$order_qty_arr_m+=$po_qty;
				}
				$batch_no=$row[csf('batch_no')];
				$extention_no=$row[csf('extention_no')];
				$booking_no=$row[csf('booking_no')];

				//$batch_qty=$batch_no_arr[$row[csf('batch_id')]]['batch_qnty'];
				//$extention_no=$batch_no_arr[$row[csf('batch_id')]]['extention_no'];
				//$process_loss_qty=$batch_qty-$row[csf('qc_pass_qty')];
				//$process_loss_qty_percent=($process_loss_qty/$batch_qty)*100;

				$knitting_company="";
				if($row[csf('knitting_source')] == 1)
				{
					$knitting_company = $company_arr[$row[csf('knitting_company')]];
				}else{
					$knitting_company = $supplier_arr[$row[csf('knitting_company')]];
				}
				?>

				 
	            <tr  bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                <td  width="40"><? echo $i;?> </td>
	                <td  width="100" class="word_wrap_break"><p><? echo $knitting_company;?></p> </td>
	                <td  width="100"><p><? echo $floor_arr[$row[csf('floor')]];?></p> </td>
	                <td  width="120"><p><? echo $row[csf('recv_number')];?></p> </td>
	                <td width="80"><p><? echo change_date_format($row[csf('receive_date')]);?></p>  </td>
	                <td width="80"><p><? if($proce_start_date[$row[csf('batch_id')]]!="") echo change_date_format($proce_start_date[$row[csf('batch_id')]]);?></p>  </td>
	                <td width="70"><p><?  if($proce_start_date[$row[csf('batch_id')]]!="") echo datediff("d",$proce_start_date[$row[csf('batch_id')]],$row[csf('receive_date')]);?></p>  </td>
	                <td width="80" class="word_wrap_break"><p><? echo $location_library[$row[csf('location_id')]];?></p> </td>
	                <td  width="120"><p><? echo $booking_no;?></p>  </td>
	                <td  width="120"><p><? echo $job_num;?></p>  </td>
	                <td  width="100"><p><? echo $buyer_library[$row[csf('buyer_id')]];?></p> </td>
	                <td width="100" class="word_wrap_break"><p><? echo $style_ref_arr;?></p> </td>
	                <td width="100" class="word_wrap_break"><p><? echo $order_number_arr;?></p>  </td>
	                <td width="70" class="word_wrap_break"><p><? echo $file_no;?></p>  </td>
	                <td width="80" class="word_wrap_break"><p><? if($row[csf('booking_without_order')]==1){ echo $nonOrdBooking_arr[$row[csf('booking_no')]]; }else{echo $int_ref_no;} ?></p></td>
	                <td width="80" align="right"><p><? echo number_format($order_qty_arr_m);?></p>  </td>
	                <td width="250" class="word_wrap_break"><p><? echo $composition_arr[$row[csf('fabric_description_id')]];?></p> </td>
	                <td width="60"> <p><? echo $row[csf('gsm')];?></p> </td>
	                <td width="60"> <p><? echo $row[csf('width')];?></p> </td>
	                <td width="100" class="word_wrap_break"><p><? echo $color_range[$row[csf('color_range_id')]];?></p> </td>
	                <td width="100" class="word_wrap_break"><p><? echo $color_library[$row[csf('color_id')]];?></p> </td>
	                <td width="100" class="word_wrap_break"><p><? echo $batch_no; ?></p>  </td>
	                <td width="100"><p><? echo $extention_no; ?></p>  </td>
	                <td width="80"  align="right" title="<? echo 'Batch ID:'.$row[csf('batch_id')]; ?>"> <p><? echo number_format($batch_qty,2); ?></p> </td>
	                <td width="80"  align="right" id="total_qc_qty"><p><? echo number_format($row[csf('qc_pass_qty')],2);?></p>  </td>
	                <td width="80"  align="right" id="total_grey_qty"><p><? echo number_format($row[csf('grey_used_qty')],2);?></p>  </td>
	                <td width="80"  align="right" id="total_rj_qty"><p><? echo number_format($row[csf('reject_qty')],2);?></p>  </td>
	                <td width="" class="word_wrap_break"><p><?php echo $row[csf('remarks')]; ?></p>  </td>
	            </tr>
	            <?
				$total_qc_qty+=$row[csf('qc_pass_qty')];
				$tot_grey_used_qty+=$row[csf('grey_used_qty')];
				$total_batch_qty+=$batch_qty;
				//$total_process_loss_qty+=$process_loss_qty;
				$total_rj_qty+=$row[csf('reject_qty')];
				$i++;
			}
			?>
	    	</table>
		    <table class="rpt_table" width="2690" cellpadding="0" cellspacing="0" border="1" rules="all">
		    <tfoot>
		        <tr>
		            <th width="40">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="80">&nbsp;</th>
		            <th width="80">&nbsp;</th>
		            <th width="70">&nbsp;</th>
		            <th width="80">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="120">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="70">&nbsp;</th>
		            <th width="80">&nbsp;</th>
		            <th width="80">&nbsp;</th>
		            <th width="250">&nbsp;</th>
		            <th width="60">&nbsp;</th>
		            <th width="60">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100">&nbsp;</th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="80" id="value_total_batch_qty"><? echo number_format($total_batch_qty,2); ?></th>           
		            <th width="80" id="value_total_qc_qty"><? echo number_format($total_qc_qty,2); ?></th>
		            <th width="80" id="value_total_grey_used_qty"><? echo number_format($tot_grey_used_qty,2); ?></th>
		            <th width="80" id="value_total_rj_qty"><? echo number_format($total_rj_qty,2); ?></th>
		            <th width="">&nbsp;</th>
		        </tr>
		    </tfoot>
		    </table>
	   		</div>
	   	</fieldset>
	  	</div>  
	  	<!-- =============== Details Data Show End ======================== -->
		<?

		$html = ob_get_contents();
	    ob_clean();
	    foreach (glob("*.xls") as $filename) {
	    	@unlink($filename);
	    }
	    //---------end------------//
	    $name=time();
	    $filename=$user_name."_".$name.".xls";
	    $create_new_doc = fopen($filename, 'w');
	    $is_created = fwrite($create_new_doc, $html);
	    echo "$html####$filename";
	    
		exit();
	} // Show 2 End
}
?>