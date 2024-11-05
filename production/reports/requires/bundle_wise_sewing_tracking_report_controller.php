<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

function pre($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($data) 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/bundle_wise_sewing_tracking_report_controller', document.getElementById('cbo_working_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
	and company_id in($ex_data[0]) and location_id in($ex_data[1]) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
	exit();    	 
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_line_id','0','0','','0');\n";
    // echo "setTimeout[($('#floor_td a').attr('onclick','disappear_list(cbo_floor,0);getCompanyId();') ,3000)];\n";
    exit();
}


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="search_by_action")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$lc_company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                  
	                        <td align="center">	
	                    	<?						
								echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", "",'',0 );
							?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $lc_company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'bundle_wise_sewing_tracking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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

if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}

	if($search_by==1) 
		$search_field="a.job_no_prefix_num"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
	$job_year =$data[4];
	
	if($job_year!=0)
	{
		if($db_type==0)
		{
			$job_year_cond=" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$job_year_cond=" and to_char(a.insert_date,'YYYY')='$job_year'";	
		}
	}
	else
	{
		$job_year_cond="";
	}
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	
	$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; 
    // echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "80,80,50,70","550","220",0, $sql , "js_set_value", "id,job_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no","",'','0,0,0,0,0','',1) ;
   exit(); 
}

if($action=="color_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>	
    	// var txt_order_id = $("#txt_order_id").val(); alert(txt_order_id);

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
			// alert(strCon);
			var splitSTR = strCon.split("_");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			// var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				// selected_name.push( selectDESC );
				selected_no.push( str_or );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				// selected_name.splice( i, 1 );
				selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				// name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			// name 	= name.substr( 0, name.length - 1 ); 
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			// $('#txt_selected').val( name ); 
			// $('#txt_selected_no').val( num );
		}
    </script>
    <?
	$job_id=str_replace("'","",$txt_job_id);

	$job_id_arr = explode(",", $job_id);
	if(count($job_id_arr)>999 && $db_type==2)
    {
     	$po_chunk=array_chunk($job_id_arr, 999);
     	$job_ids_cond= "";
     	foreach($po_chunk as $vals)
     	{
     		$imp_ids=implode(",", $vals);
     		if($job_ids_cond=="") 
     		{
     			$job_ids_cond.=" and ( b.id in ($imp_ids) ";
     		}
     		else
     		{
     			$job_ids_cond.=" or b.id in ($imp_ids) ";
     		}
     	}
     	 $job_ids_cond.=" )";
    }
    else
    {
     	$job_ids_cond= " and b.id in($job_id) ";
    }
		
    $company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $arr=array(0=>$company_library,2=>$color_arr);
    // print_r($arr);

	$sql = "SELECT b.id,b.company_name,b.job_no_prefix_num,c.color_number_id from wo_po_break_down a, wo_po_details_master b,wo_po_color_size_breakdown c  where a.job_id=b.id and a.id=c.po_break_down_id and b.id=c.job_id $job_ids_cond and a.status_active in(1,2,3) and b.status_active=1 and c.status_active=1 group by b.id,b.company_name,b.job_no_prefix_num,c.color_number_id order by b.id desc"; 
	// echo $sql; die;
	echo create_list_view("list_view", "Company,Job No,Color","150,50,100","380","310",0, $sql , "js_set_value", "color_number_id", "", 1, "company_name,0,color_number_id", $arr, "company_name,job_no_prefix_num,color_number_id", "","setFilterGrid('list_view',-1)","0,0,0","",1) ;

	// echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No, Po No, Cut No.", "120,100,100,100,140,140","740","290",0, $sql , "js_set_value", "job_no,style_ref_no,po_number,cut_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,job_no,style_ref_no,po_number,cut_no","",'','0,0,0,0,0,0','',1) ;

	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	/*var style_no='<? echo $txt_order_id_no;?>';
	var style_id='<? echo $txt_order_id;?>';
	var style_des='<? echo $txt_order;?>';
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
	}*/
	</script>
    
    <?
	exit();
}

if($action=="cutting_no_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>	
    	// var txt_order_id = $("#txt_order_id").val(); alert(txt_order_id);

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
			// alert(strCon);
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
			$('#txt_selected_id').val( name );
			// $('#txt_selected').val( name ); 
			// $('#txt_selected_no').val( num );
		}
    </script>
    <?
	$job_id=str_replace("'","",$txt_job_id);
	$job_ids_cond= "";
	if($job_id)	{
		$job_id_arr = explode(",", $job_id);
		if(count($job_id_arr)>999 && $db_type==2)
		{
			$po_chunk=array_chunk($job_id_arr, 999);
			foreach($po_chunk as $vals)
			{
				$imp_ids=implode(",", $vals);
				if($job_ids_cond=="") 
				{
					$job_ids_cond.=" and ( a.id in ($imp_ids) ";
				}
				else
				{
					$job_ids_cond.=" or a.id in ($imp_ids) ";
				}
			}
			$job_ids_cond.=" )";
		}
		else
		{
			$job_ids_cond= " and a.id in($job_id) ";
		}
	}
    if($color_id!=""){$color_cond=" and c.color_id in($color_id)";}
		
    $company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $arr=array(0=>$company_library,2=>$color_arr);
    // print_r($arr);

	$sql = "SELECT a.id,a.company_name,a.job_no_prefix_num,b.cutting_no,c.color_id from wo_po_details_master a, ppl_cut_lay_mst b,ppl_cut_lay_dtls c  where a.job_no=b.job_no and b.id=c.mst_id $job_ids_cond $color_cond and a.status_active in(1,2,3) and b.status_active=1 and c.status_active=1 group by a.id,a.company_name,a.job_no_prefix_num,b.cutting_no,c.color_id order by a.id desc "; 
	// echo $sql; die;
	echo create_list_view("list_view", "Company,Job No,Color,Cutting No","150,50,100,100","480","310",0, $sql , "js_set_value", "color_id,cutting_no", "", 1, "company_name,0,color_id,0", $arr, "company_name,job_no_prefix_num,color_id,cutting_no", "","setFilterGrid('list_view',-1)","0,0,0,0","",1) ;

	// echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No, Po No, Cut No.", "120,100,100,100,140,140","740","290",0, $sql , "js_set_value", "job_no,style_ref_no,po_number,cut_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,job_no,style_ref_no,po_number,cut_no","",'','0,0,0,0,0,0','',1) ;

	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	/*var style_no='<? echo $txt_order_id_no;?>';
	var style_id='<? echo $txt_order_id;?>';
	var style_des='<? echo $txt_order;?>';
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
	}*/
	</script>
    
    <?
	exit();
}

if($action=="report_generate") 
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr 		= return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$colorArr 		= return_library_array("select id,color_name from lib_color","id","color_name"); 
	$sizeArr 		= return_library_array("select id,size_name from lib_size","id","size_name"); 
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	// ================================= GETTING FORM DATA ====================================
	$working_company_id =str_replace("'","",$cbo_working_company_id);
	$location_id 		=str_replace("'","",$cbo_location_id);
	$floor_id 			=str_replace("'","",$cbo_floor_id);
	$lc_company_id 		=str_replace("'","",$cbo_lc_company_id);
	$buyer_id 			=str_replace("'","",$cbo_buyer_id);
	$job_id 			=str_replace("'","",$txt_job_id);
	$file_no 			=str_replace("'","",$txt_file_no);
	$int_ref 			=str_replace("'","",$txt_int_ref);
	$color_id 			=str_replace("'","",$color_id);
	$cutting_no 		=str_replace("'","",$txt_cutting_no);
	$bunle_no 			=str_replace("'","",$txt_bunle_no);
	$txt_date_from 		=str_replace("'","",$txt_date_from);
	$txt_date_to 		=str_replace("'","",$txt_date_to);	

	$cut_ex = explode(",", $cutting_no);
	$all_cut_no = "'".implode("','", $cut_ex)."'";

	$col_size_break_down_id_arr = return_library_array( "select id, id from wo_po_color_size_breakdown where color_number_id in($color_id) and job_id in($job_id)", "id", "id"  );
	$col_size_break_down_id =  implode(",", $col_size_break_down_id_arr);
	if($type==1)
	{
		//******************************************* MAKE QUERY CONDITION ************************************************
		$sql_cond = "";
		$sql_cond .= ($working_company_id==0) 	? "" : " and d.serving_company in($working_company_id)";
		$sql_cond .= ($location_id==0) 			? "" : " and d.location in($location_id)";
		$sql_cond .= ($floor_id==0) 			? "" : " and d.floor_id in($floor_id)";
		$sql_cond .= ($lc_company_id==0) 		? "" : " and a.company_name=$lc_company_id";
		$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
		$sql_cond .= ($job_id=="") 				? "" : " and a.id in($job_id)";
		$sql_cond .= ($file_no=="") 			? "" : " and b.file_no ='$file_no'";
		$sql_cond .= ($int_ref=="") 			? "" : " and b.grouping='$int_ref'";
		$sql_cond .= ($color_id=="") 			? "" : " and e.color_size_break_down_id in($col_size_break_down_id)";
		$sql_cond .= ($cutting_no=="") 			? "" : " and e.cut_no in($all_cut_no)";
		$sql_cond .= ($bunle_no=="") 			? "" : " and e.bundle_no='$bunle_no'";
		

		if($txt_date_from!="" && $txt_date_to!="")
		{
			if($db_type==0)
			{
				$txt_datefrom=change_date_format($txt_date_from,'yyyy-mm-dd');
				$txt_dateto=change_date_format($txt_date_to,'yyyy-mm-dd');
			}
			else if($db_type==2)
			{
				$txt_datefrom=change_date_format($txt_date_from,'','',-1);
				$txt_dateto=change_date_format($txt_date_to,'','',-1);
			}
			$sql_cond .= " and d.production_date between '$txt_datefrom' and '$txt_dateto'";
		}

		// echo $sql_cond;die();
			
		// ================================================ MAIN QUERY ==================================================
		$sql="SELECT  a.COMPANY_NAME,a.BUYER_NAME,a.style_ref_no as STYLE,a.JOB_NO,b.id as PO_ID,b.PO_NUMBER,b.FILE_NO,b.GROUPING,c.color_number_id as COLOR_ID,e.CUT_NO,e.BUNDLE_NO,d.PRODUCTION_DATE,d.floor_id,d.sewing_line,d.prod_reso_allo,e.PRODUCTION_QNTY,e.ALTER_QTY,e.SPOT_QTY,e.REJECT_QTY,e.REPLACE_QTY,e.PRODUCTION_TYPE,e.IS_RESCAN,to_char(d.PRODUCTION_HOUR, 'hh24:mi') as PRODUCTION_HOUR
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e  
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.status_active=1 and d.production_type in(4,5)
		order by a.job_no_prefix_num,b.id,e.CUT_NO,e.BUNDLE_NO";
		// echo $sql;die();
		$sql_res = sql_select($sql);
		if (count($sql_res) < 1) 
		{	
			?>
			<style type="text/css">
				.alert 
				{
					padding: 12px 35px 12px 14px;
					margin-bottom: 18px;
					text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
					background-color: #fcf8e3;
					border: 1px solid #fbeed5;
					-webkit-border-radius: 4px;
					-moz-border-radius: 4px;
					border-radius: 4px;
					color: #c09853;
					font-size: 16px;
				}
				.alert strong{font-size: 18px;}
				.alert-danger,
				.alert-error 
				{
				  	background-color: #f2dede;
				  	border-color: #eed3d7;
				  	color: #b94a48;
				}
			</style>
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-error">
				  <strong>Oh Snap!</strong> Data Not Found. Please Try Again.
				</div>
			</div>
			<?
			die();
		}

		$data_array = array();
		$poId_arr = array();
		foreach ($sql_res as $row) 
		{
			$line_name = '';
			if($row['PROD_RESO_ALLO']==1)
			{
				$line_resource_mst_arr=array_unique(explode(",",$prod_reso_line_arr[$row['SEWING_LINE']]));
				
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}
			}
			else
			{
				$line_name=$lineArr[$row['SEWING_LINE']];
			}

			// echo $row['PRODUCTION_HOUR']."<br>";
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']]['company'] = $row['COMPANY_NAME'];
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']]['style'] = $row['STYLE'];
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']]['po_number'] = $row['PO_NUMBER'];
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']]['floor_id'] = $row['FLOOR_ID'];
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']]['sewing_line'] = $line_name;
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']][$row['PRODUCTION_TYPE']]['date'] = $row['PRODUCTION_DATE'];
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']][$row['PRODUCTION_TYPE']]['time'] = $row['PRODUCTION_HOUR'];

			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']][$row['PRODUCTION_TYPE']][$row['IS_RESCAN']]['qc_qty'] += $row['PRODUCTION_QNTY'];
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']][$row['PRODUCTION_TYPE']][$row['IS_RESCAN']]['reject_qty'] += $row['REJECT_QTY'];
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']][$row['PRODUCTION_TYPE']][$row['IS_RESCAN']]['replace_qty'] += $row['REPLACE_QTY'];
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']][$row['PRODUCTION_TYPE']][$row['IS_RESCAN']]['alter_qty'] += $row['ALTER_QTY'];
			$data_array[$row['BUYER_NAME']][$row['JOB_NO']][$row['FILE_NO']][$row['GROUPING']][$row['COLOR_ID']][$row['CUT_NO']][$row['BUNDLE_NO']][$row['PRODUCTION_TYPE']][$row['IS_RESCAN']]['spot_qty'] += $row['SPOT_QTY'];


			$poId_arr[$row['PO_ID']] = $row['PO_ID'];
		}

		// echo "<pre>";print_r($data_array);echo "</pre>";

		$all_po_id = implode(",", $poId_arr);
		if(count($poId_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($poId_arr, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") 
	     		{
	     			$po_ids_cond.=" and ( order_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or order_id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	    }
	    else
	    {
	     	$po_ids_cond= " and order_id in($all_po_id) ";
	    }
		
		// ============================= getting bundle qty ==============================
		$bundle_qty_arr = return_library_array( "select bundle_no, size_qty from ppl_cut_lay_bundle where status_active=1 $po_ids_cond ", "bundle_no", "size_qty"  );
		// echo "<pre>";print_r($bundle_qty_arr);echo "</pre>";
		
		// ============================= getting batch no ==============================
		$batch_po_cond = str_replace("order_id", "b.po_id", $po_ids_cond);
		$batch_no_arr = return_library_array( "select a.id,a.batch_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id $batch_po_cond and a.status_active=1 and b.status_active=1", "id", "batch_no"  );
		// echo "<pre>";print_r($batch_no_arr);echo "</pre>";

		// ============================= getting bundle wise batch id ==============================
		$po_ids_cond = str_replace("order_id", "b.order_id", $po_ids_cond);
		$bundle_wise_batch_id_arr = return_library_array( "select a.batch_id,b.bundle_no from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where a.id=b.dtls_id $po_ids_cond and a.status_active=1 and b.status_active=1", "bundle_no", "batch_id"  );
		// echo "<pre>";print_r($bundle_wise_batch_id_arr);echo "</pre>";


		ob_start();
		?>
		
		<fieldset>
			<table width="100%" cellspacing="0">
		        <tr class="form_caption" style="border:none;">
		            <td colspan="37" align="center" ><font size="3"><strong><u><? echo $company_details[$lc_company_id]; ?></u></strong></font></td>
		        </tr>
		        <tr class="form_caption" style="border:none;">
		            <td colspan="37" align="center"><font size="2"><strong>Bundle Wise Sewing Tracking Report</strong></font></td>
		        </tr>
		    </table>
		    <div>
		    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="2350">
		    		<thead>
		    			<tr>
		    				<th colspan="18">Style Information</th>
		    				<th colspan="6">Scan Bundle Info.</th>
		    				<th colspan="6">Re-Scan Bundle Info.(Reject)</th>
		    				<th colspan="6">Re-Scan Bundle Info.(Alt+Spot)</th>
			    			<th rowspan="2" width="80">Output Bal</th>
		    			</tr>
		    			<tr>
			    			<th width="30">Sl.</th>
			    			<th width="110"><p>LC Company</p></th>
			    			<th width="100"><p>Floor</p></th>
			    			<th width="100"><p>Buyer Name</p></th>
			    			<th width="80"><p>Job No.</p></th>
			    			<th width="100"><p>PO</p></th>
			    			<th width="100"><p>Style Ref.</p></th>
			    			<th width="80"><p>File</p></th>
			    			<th width="80"><p>Internal Ref.</p></th>
			    			<th width="80"><p>Batch</p></th>
			    			<th width="100"><p>Color Name</p></th>
			    			<th width="90"><p>Cutting No</p></th>
			    			<th width="80"><p>Bundle No</p></th>
			    			<th width="50"><p>Bundle Suffix</p></th>
			    			<th width="70"><p>Sewing Line</p></th>
			    			<th width="70"><p>Input Date</p></th>
			    			<th width="70"><p>Output <br>Date</p></th>
			    			<th width="60"><p>Output<br> Time</p></th>

			    			<th width="50"><p>Bundle Qty</p></th>
			    			<th width="50"><p>Alter</p></th>
			    			<th width="50"><p>Spot</p></th>
			    			<th width="50"><p>Reject</p></th>
			    			<th width="50"><p>Replace</p></th>
			    			<th width="50"><p>QC Qty</p></th>

			    			<th width="50"><p>Bundle Qty</p></th>
			    			<th width="50"><p>Alter</p></th>
			    			<th width="50"><p>Spot</p></th>
			    			<th width="50"><p>Reject</p></th>
			    			<th width="50"><p>Replace</p></th>
			    			<th width="50"><p>QC Qty</p></th>

			    			<th width="50"><p>Bundle Qty</p></th>
			    			<th width="50"><p>Alter</p></th>
			    			<th width="50"><p>Spot</p></th>
			    			<th width="50"><p>Reject</p></th>
			    			<th width="50"><p>Replace</p></th>
			    			<th width="50"><p>QC Qty</p></th>
			    		</tr>

		    		</thead>
		    	</table>
		    	<div style="width: 2370px; overflow-y: scroll; max-height: 400px" id="scroll_body">
		    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="2350" id="table_body">
		    			<?
		    			$i=1;
		    			$scan_bundle_qty 	= 0;
		    			$scan_alter_qty 	= 0;
		    			$scan_spot_qty 		= 0;
		    			$scan_reject_qty 	= 0;
		    			$scan_replace_qty 	= 0;
		    			$scan_qc_qty 		= 0;

		    			$rescan_bundle_qty 	= 0;
		    			$rescan_alter_qty 	= 0;
		    			$rescan_spot_qty 	= 0;
		    			$rescan_reject_qty 	= 0;
		    			$rescan_replace_qty = 0;
		    			$rescan_qc_qty 		= 0;

		    			$rescan2_bundle_qty 	= 0;
		    			$rescan2_alter_qty 		= 0;
		    			$rescan2_spot_qty 		= 0;
		    			$rescan2_reject_qty 	= 0;
		    			$rescan2_replace_qty 	= 0;
		    			$rescan2_qc_qty 		= 0;

		    			$tot_out_bal = 0;

		    			foreach ($data_array as $b_key => $b_value) 
		    			{
		    				foreach ($b_value as $j_key => $j_value) 
		    				{
		    					foreach ($j_value as $f_key => $f_value) 
		    					{
		    						foreach ($f_value as $in_key => $in_value) 
		    						{
		    							foreach ($in_value as $col_key => $col_value) 
		    							{
		    								foreach ($col_value as $cut_key => $cut_value)
		    								{
		    									foreach ($cut_value as $bun_key => $row) 
		    									{
		    										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		    										// $output_bal = $bundle_qty_arr[$bun_key] - $row[5][0]['qc_qty'] - $row[5][1]['qc_qty'] - $row[5][1]['qc_qty'];

													// Note : This formula is applicable when replace against both defect and reject
													$output_bal = ($row[5][0]['alter_qty']+$row[5][0]['spot_qty']+$row[5][0]['reject_qty']) - ($row[5][0]['replace_qty']+$row[5][1]['qc_qty']);
									    			?>
									    			<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i;?>">
									    				<td width="30"><?=$i;?></td>
										    			<td width="110"><p><?=$companyArr[$row['company']];?></p></td>
										    			<td width="100"><p><?=$floorArr[$row['floor_id']];?></p></td>
										    			<td width="100"><p><?=$buyerArr[$b_key];?></p></td>
										    			<td width="80"><p><?=$j_key;?></p></td>
										    			<td width="100"><p><?=$row['po_number'];?></p></td>
										    			<td width="100"><p><?=$row['style'];?></p></td>
										    			<td width="80"><p><?=$f_key;?></p></td>
										    			<td width="80"><p><?=$in_key;?></p></td>
										    			<td width="80"><p><?=$batch_no_arr[$bundle_wise_batch_id_arr[$bun_key]];?></p></td>
										    			<td width="100"><p><?=$colorArr[$col_key];?></p></td>
										    			<td width="90"><p><?=$cut_key;?></p></td>
										    			<td width="80"><p><?=$bun_key;?></p></td>
										    			<td width="50"><p><? $bun_ex = explode("-", $bun_key); echo $bun_ex[3];?></p></td>
										    			<td width="70"><p><?=$row['sewing_line'];?></p></td>
										    			<td width="70" align="center"><p><?= change_date_format($row[4]['date']);?></p></td>
										    			<td width="70" align="center"><p><?= change_date_format($row[5]['date']);?></p></td>
										    			<td width="60" align="center"><p><?= $row[5]['time'];?></p></td>

										    			<td align="right" width="50"><?=number_format($bundle_qty_arr[$bun_key],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][0]['alter_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][0]['spot_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][0]['reject_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][0]['replace_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][0]['qc_qty'],0);?></td>

										    			<td align="right" width="50"><?=number_format($row[5][0]['reject_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][1]['alter_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][1]['spot_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][1]['reject_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][1]['replace_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][1]['qc_qty'],0);?></td>

										    			<td align="right" width="50"><?=number_format(($row[5][0]['alter_qty']+$row[5][1]['spot_qty']),0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][1]['alter_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][1]['spot_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][1]['reject_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][1]['replace_qty'],0);?></td>
										    			<td align="right" width="50"><?=number_format($row[5][1]['qc_qty'],0);?></td>

										    			<td align="right" width="80"><?=number_format($output_bal,0);?></td>
										    		</tr>
									    			<?
									    			$i++;
									    			$scan_bundle_qty 	+= $bundle_qty_arr[$bun_key];
									    			$scan_alter_qty 	+= $row[5][0]['alter_qty'];
									    			$scan_spot_qty 		+= $row[5][0]['spot_qty'];
									    			$scan_reject_qty 	+= $row[5][0]['reject_qty'];
									    			$scan_replace_qty 	+= $row[5][0]['replace_qty'];
									    			$scan_qc_qty 		+= $row[5][0]['qc_qty'];

									    			$rescan_bundle_qty 	+= $row[5][0]['reject_qty'];
									    			$rescan_alter_qty 	+= $row[5][1]['alter_qty'];
									    			$rescan_spot_qty 	+= $row[5][1]['spot_qty'];
									    			$rescan_reject_qty 	+= $row[5][1]['reject_qty'];
									    			$rescan_replace_qty += $row[5][1]['replace_qty'];
									    			$rescan_qc_qty 		+= $row[5][1]['qc_qty'];

									    			$rescan2_bundle_qty 	+= $row[5][0]['alter_qty']+$row[5][1]['spot_qty'];
									    			$rescan2_alter_qty 		+= $row[5][1]['alter_qty'];
									    			$rescan2_spot_qty 		+= $row[5][1]['spot_qty'];
									    			$rescan2_reject_qty 	+= $row[5][1]['reject_qty'];
									    			$rescan2_replace_qty 	+= $row[5][1]['replace_qty'];
									    			$rescan2_qc_qty 		+= $row[5][1]['qc_qty'];

									    			$tot_out_bal += $output_bal;
		    									}
		    								}
		    							}
		    						}
		    					}
		    				}
		    			}
		    			?>
		    		</table>
		    	</div>
		    	<div style="width: 2370px;"">
		    		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="2350">
		    			<tfoot>
		    				<th width="30"></th>
			    			<th width="110"></th>
			    			<th width="100"></th>
			    			<th width="100"></th>
			    			<th width="80"></th>
			    			<th width="100"></th>
			    			<th width="100"></th>
			    			<th width="80"></th>
			    			<th width="80"></th>
			    			<th width="80"></th>
			    			<th width="100"></th>
			    			<th width="90"></th>
			    			<th width="80"></th>
			    			<th width="50"></th>
			    			<th width="70"></th>
			    			<th width="70"></th>
			    			<th width="70"></th>
			    			<th width="60">Total</th>

			    			<th width="50"><?=number_format($scan_bundle_qty,0);?></th>
			    			<th width="50"><?=number_format($scan_alter_qty,0);?></th>
			    			<th width="50"><?=number_format($scan_spot_qty,0);?></th>
			    			<th width="50"><?=number_format($scan_reject_qty,0);?></th>
			    			<th width="50"><?=number_format($scan_replace_qty,0);?></th>
			    			<th width="50"><?=number_format($scan_qc_qty,0);?></th>

			    			<th width="50"><?=number_format($rescan_bundle_qty,0);?></th>
			    			<th width="50"><?=number_format($rescan_alter_qty,0);?></th>
			    			<th width="50"><?=number_format($rescan_spot_qty,0);?></th>
			    			<th width="50"><?=number_format($rescan_reject_qty,0);?></th>
			    			<th width="50"><?=number_format($rescan_replace_qty,0);?></th>
			    			<th width="50"><?=number_format($rescan_qc_qty,0);?></th>

			    			<th width="50"><?=number_format($rescan2_bundle_qty,0);?></th>
			    			<th width="50"><?=number_format($rescan2_alter_qty,0);?></th>
			    			<th width="50"><?=number_format($rescan2_spot_qty,0);?></th>
			    			<th width="50"><?=number_format($rescan2_reject_qty,0);?></th>
			    			<th width="50"><?=number_format($rescan2_replace_qty,0);?></th>
			    			<th width="50"><?=number_format($rescan2_qc_qty,0);?></th>

			    			<th width="80"><?=number_format($tot_out_bal,0);?></th>
		    			</tfoot>
		    		</table>
		    </div>
	    </fieldset>
	   <?
	}
	if($type==2)
	{ 
		
		//******************************************* MAKE QUERY CONDITION ************************************************
		$sql_cond = "";
		// $sql_cond .= ($working_company_id==0) 	? "" : " and d.serving_company in($working_company_id)";
		$sql_cond .= ($location_id==0) 			? "" : " and d.location in($location_id)";
		$sql_cond .= ($floor_id==0) 			? "" : " and d.floor_id in($floor_id)";
		$sql_cond .= ($lc_company_id==0) 		? "" : " and a.company_name=$lc_company_id";
		$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
		$sql_cond .= ($job_id=="") 				? "" : " and a.id in($job_id)";
		$sql_cond .= ($file_no=="") 			? "" : " and b.file_no ='$file_no'";
		$sql_cond .= ($int_ref=="") 			? "" : " and b.grouping='$int_ref'";
		$sql_cond .= ($color_id=="") 			? "" : " and e.color_size_break_down_id in($col_size_break_down_id)";
		$sql_cond .= ($cutting_no=="") 			? "" : " and e.cut_no in($all_cut_no)";
		$sql_cond .= ($bunle_no=="") 			? "" : " and e.bundle_no='$bunle_no'";
		

		if($txt_date_from!="" && $txt_date_to!="")
		{
			if($db_type==0)
			{
				$txt_datefrom=change_date_format($txt_date_from,'yyyy-mm-dd');
				$txt_dateto=change_date_format($txt_date_to,'yyyy-mm-dd');
			}
			else if($db_type==2)
			{
				$txt_datefrom=change_date_format($txt_date_from,'','',-1);
				$txt_dateto=change_date_format($txt_date_to,'','',-1);
			}
			$sql_cond .= " and d.production_date between '$txt_datefrom' and '$txt_dateto'";
		}

		// echo $sql_cond;die();
			
		// ================================================ MAIN QUERY ==================================================
		$sql="SELECT b.id as po_id,e.delivery_mst_id, e.barcode_no, e.cut_no,c.size_number_id as size_id,e.bundle_no,d.production_date,d.floor_id,d.sewing_line,d.prod_reso_allo,d.embel_name,e.production_qnty,e.production_type,e.bundle_qtygm from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id  $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0 and e.is_deleted=0  and d.production_type in(1,2,3,4,5) order by e.cut_no asc,e.barcode_no asc";
		// echo $sql;die();
		// pro_gmts_delivery_mst f  ,f.sys_number as challan and d.delivery_mst_id=f.id and f.status_active=1 and f.is_deleted=0
		$sql_res = sql_select($sql);
		// pre($sql_res); die;
		$data_array = array();
		$po_id_arr = array();
		$delivery_mst_id_arr = array();
		foreach ($sql_res as $v) 
		{
			if ($v['PRODUCTION_TYPE'] == 4) { 
				$line_name = '';
				if($v['PROD_RESO_ALLO']==1)
				{
					$line_resource_mst_arr=array_unique(explode(",",$prod_reso_line_arr[$v['SEWING_LINE']]));
					
					foreach($line_resource_mst_arr as $resource_id)
					{
						$line_name.=($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
					}
				}
				else
				{
					$line_name=$lineArr[$v['SEWING_LINE']];
				}
				$data_array[$v['BARCODE_NO']] ['FLOOR'] = $v['FLOOR_ID'];  
				$data_array[$v['BARCODE_NO']] ['SEW_LINE'] = $line_name; 
				$delivery_mst_id_arr[$v['DELIVERY_MST_ID']] = $v['DELIVERY_MST_ID'];
			}
			
			$po_id_arr[$v['PO_ID']]= $v['PO_ID'];

			$data_array[$v['BARCODE_NO']] ['CUT_NO'] = $v['CUT_NO'];
			$data_array[$v['BARCODE_NO']] ['SIZE'] = $v['SIZE_ID'];
			$data_array[$v['BARCODE_NO']] ['BUNDLE_NO'] = $v['BUNDLE_NO']; 
			
			if ( in_array($v['PRODUCTION_TYPE'],[2,3]) ) {
				$data_array[$v['BARCODE_NO']] ['PROD_DATE']['EMBL'][$v['PRODUCTION_TYPE']][$v['EMBEL_NAME']]= $v['PRODUCTION_DATE'];
				$data_array[$v['BARCODE_NO']] ['PROD_QTY']['EMBL'][$v['PRODUCTION_TYPE']][$v['EMBEL_NAME']] += $v['PRODUCTION_QNTY'];
			} 
			$data_array[$v['BARCODE_NO']] ['PROD_DATE'][$v['PRODUCTION_TYPE']] = $v['PRODUCTION_DATE'];
			$data_array[$v['BARCODE_NO']] ['PROD_QTY'][$v['PRODUCTION_TYPE']] += $v['PRODUCTION_QNTY'];
		}
		// pre($data_array); die;

		//=================================== Delete Order Id From TEMP ENGINE ====================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 29 and ref_from in(1,2)");
    	oci_commit($con);  
		//=================================== Insert order_id into TEMP ENGINE ====================================

		fnc_tempengine("gbl_temp_engine", $user_id, 29, 1,$po_id_arr, $empty_arr); 
		fnc_tempengine("gbl_temp_engine", $user_id, 29, 2,$delivery_mst_id_arr, $empty_arr); 
		oci_commit($con);  	 

		// =====================================  Get Bundle Quantity  =============================================
		// $bundle_cond = where_con_using_array($po_id_arr,1,'a.order_id');
		$bundle_sql = "SELECT a.size_qty,a.bundle_no from ppl_cut_lay_bundle a,gbl_temp_engine b where a.order_id=b.ref_val and a.status_active=1 and a.is_deleted=0 and b.entry_form=29 and b.ref_from=1 and b.user_id=$user_id";
		// echo $bundle_sql; die;
		$bundle_sql_res = sql_select($bundle_sql);
		$bundle_qty_arr=[];
		foreach($bundle_sql_res as   $v)
		{
			$bundle_qty_arr[$v['BUNDLE_NO']] += $v['SIZE_QTY'] ;
		} 
		// pre($bundle_qty_ar);
		// ===================================== Get Input Callan  =============================================

		$challan_sql = "SELECT a.sys_number as challan,b.barcode_no from pro_gmts_delivery_mst a, pro_garments_production_dtls b,gbl_temp_engine c where a.id=b.delivery_mst_id and a.id=c.ref_val and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=29 and c.ref_from=2 and c.user_id=$user_id";
		// echo $challan_sql; die;
		$challan_sql_res = sql_select($challan_sql);
		$challan_array = array();
		foreach($challan_sql_res as   $v)
		{ 
			// $data_array[$v['BARCODE_NO']] ['CHALLAN'] = $v['CHALLAN'];  
			$challan_array[$v['BARCODE_NO']] = $v['CHALLAN'];  
		} 
		
		//=================================== Delete Order Id From TEMP ENGINE ====================================
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=29 and ref_from in(1,2)");
    	oci_commit($con);  
		disconnect($con);

		ob_start();
		$width = 1410;
		?> 
		<style>
			.tableFixHead { max-height: 400px !important; overflow: auto; margin: 20px 0;}
			.tableFixHead thead th { position: sticky; top: -2px; z-index: 1;}
			.success {color:#42ba96;}
			.danger {color:#FF0000;}
		</style>
		<fieldset> 
			<table width="100%" cellspacing="0"> 
			<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="24" align="center" style="font-size:20px;"><? echo $companyArr[$lc_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="24" align="center" style="font-size:14px; font-weight:bold" >Bundle Wise Sewing Tracking Report</td>
					</tr>  
				</thead>
			</table>	
			<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
				<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead class="form_caption" >	
						<? $content.=ob_get_flush(); ?>		 
						<tr>
							<th width="30">Sl.</th>
							<th width="90">Barcode </th>
							<th width="90">S.Cutting No </th>
							<th width="30">Size </th> 
							<th width="40">B. No </th>
							<th width="40">Cutting QC </th>
							<th width="60">Cutting Date </th>

							<th width="30">Print Issue </th>
							<th width="60">Print. Issue Date. </th>
							<th width="30">Print Rcv. </th>
							<th width="60">Print Rcv. Date. </th> 

							<th width="60">Embroidary Issue </th>
							<th width="80">Embroidary issue Date. </th>
							<th width="60">Embroidary Rcv. </th>
							<th width="80">Embroidary Rcv. Date. </th>

							<th width="60">Sewing Input Scan </th>
							<th width="60">Input date </th>
							<th width="110">Input Challan No </th>
							<th width="40">Sewing output </th>
							<th width="60">Sewing Output Date </th>
							<th width="80">Sew. Floor </th>
							<th width="60">Sew. Line </th>
							<th width="50">Bundle Qty </th>
							<th width="50">Output Qty </th>
						</tr>
					</thead>
				</table>
				<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
						<tbody>
							<?
							$i = 0 ; 
							foreach ($data_array as $barcode_no => $v) 
							{
								$i++;
								$s_color = '#0fabaa';
								$qc_date = $v['PROD_DATE'][1];
								// pre($v['PROD_DATE']['EMBL']);die;
								$print_issue = $v['PROD_DATE']['EMBL'][2][1];
								$print_rcv = $v['PROD_DATE']['EMBL'][3][1];

								$embro_issue = $v['PROD_DATE']['EMBL'][2][2];
								$embro_rcv = $v['PROD_DATE']['EMBL'][3][2];

								$sew_in = $v['PROD_DATE'][4];
								$sew_out = $v['PROD_DATE'][5];
								$sew_out_qty = $v['PROD_QTY'][5];
								$input_challan = $challan_array[$barcode_no];
								if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td width="30"> <?= $i; ?> </td>
										<td width="90"> <p> <?= $barcode_no; ?> </p> </td>
										<td width="90"> <p> <?= $v['CUT_NO'] ?> </p> </td>
										<td width="30" align="center"> <p> <?= $sizeArr[$v['SIZE']] ?> </p> </td> 
										<td width="40" align="right"> <p> <?= end(explode('-',$v['BUNDLE_NO']))  ?> </p> </td>
										<td width="40" align="center"> <p class="<?= $qc_date!=''?'success':'danger' ;?>"> <?= $qc_date!=''?'Yes':'No' ;?> </p> </td>
										<td width="60"> <p> <?= $qc_date ;?> </p> </td>

										<td width="30" align="center"> <p class="<?= $print_issue!=''?'success':'danger' ;?>" > <?= $print_issue!=''?'Yes':'No' ;?> </p> </td>
										<td width="60"> <p> <?= $print_issue ;?> </p> </td>
										<td width="30" align="center"> <p class="<?= $print_rcv!=''?'success':'danger' ;?>" > <?= $print_rcv!=''?'Yes':'No' ;?> </p> </td>
										<td width="60"> <p> <?= $print_rcv ;?> </p> </td>  

										<td width="60"  align="center"> <p class="<?= $embro_issue!=''?'success':'danger' ;?>" > <?= $embro_issue!=''?'Yes':'No' ;?> </p> </td>
										<td width="80" > <p> <?= $embro_issue ;?> </p> </td>
										<td width="60"  align="center"> <p class="<?= $embro_rcv!=''?'success':'danger' ;?>" > <?= $embro_rcv!=''?'Yes':'No' ;?> </p> </td>
										<td width="80" > <p> <?= $embro_rcv ;?> </p> </td>  

										<td width="60"  align="center"> <p class="<?= $sew_in!=''?'success':'danger' ;?>" > <?= $sew_in!=''?'Yes':'No' ;?> </p> </td>
										<td width="60" > <p> <?= $sew_in ;?> </p> </td> 
										<td width="110" > <p> <?=$input_challan;?> </p> </td>  
										<td width="40"  align="center"> <p class="<?= $sew_out!=''?'success':'danger' ;?>" > <?= $sew_out!=''?'Yes':'No' ;?> </p> </td>
										<td width="60" > <p> <?= $sew_out ;?> </p> </td> 
										<td width="80" > <p> <?= $floorArr[$v['FLOOR']] ?> </p> </td>
										<td width="60"  align="center"> <p> <?= $v['SEW_LINE'] ?> </p> </td> 
										<td width="50"  align="right"> <p> <?= $bundle_qty_arr[$v['BUNDLE_NO']] ?> </p> </td>
										<td width="50"  align="right"> <p> <?= $sew_out_qty ?> </p> </td>
									</tr> 
								<?
							}
							?>
						</tbody>
					</table> 
				</div> 
			</div>
		</fieldset>
	   <? 
	}	

	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();      
}

?>