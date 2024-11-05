<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_and_size_wise_reject_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/line_and_size_wise_reject_report_controller' );",0 );     	 
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_and_size_wise_reject_report_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'load_drop_down_line', 'line_td' );",0 ); 

	// load_drop_down( 'requires/line_and_size_wise_reject_report_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );
	
	//echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_and_size_wise_reject_report_controller', this.value, 'load_drop_down_line', 'line_td' );",0 ); 
	exit();    	 
}


if ($action == "eval_multi_select") {
    // echo "set_multiselect('cbo_floor','0','0','','0');\n";
    // echo "setTimeout[($('#floor_td a').attr('onclick','disappear_list(cbo_floor,0);getCompanyId();') ,3000)];\n";
    exit();
}
if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	// print_r($explode_data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date_from = $explode_data[3];
	$txt_date_to = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date_from=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id in($explode_data[0])";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id in($explode_data[0])";
		 if($db_type==0)	$data_format="and b.pr_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		 if($db_type==2)	$data_format="and b.pr_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	
			$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number order by a.line_number");
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

		echo create_drop_down( "cbo_line", 100,$line_array,"", 1, "-- Select --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_line", 100, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0 );
	}
	exit();
}

if ($action == "load_drop_down_buyer") {
    echo create_drop_down("cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/line_and_size_wise_reject_report_controller', this.value, 'load_drop_down_season_buyer', 'season_td');");
    exit();
}

if ($action=="load_drop_down_season_buyer")
{
    echo create_drop_down( "cbo_season_name", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
    exit();
}

if($action=="intref_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
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
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
	            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
	                    <th>Shipment Date</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
	                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
	                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Internal Ref",2=>"Style Ref",3=>"Job No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td align="center">
	                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
	                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
	                        </td>	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'intref_search_list_view', 'search_div', 'line_and_size_wise_reject_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="intref_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$job_no=$data[6];
	if($job_no!='') $job_no_cond="and a.job_no_prefix_num=$job_no";else $job_no_cond="";
	
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
		$search_field="b.grouping"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no_prefix_num";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $date_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping order by a.id, b.grouping"; 
    // echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Int Ref", "80,80,50,70,170","620","220",0, $sql , "js_set_value", "id,grouping","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,grouping","",'','0,0,0,0,0,0','',1) ;
   exit(); 
}

if($action=="po_search_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1,$unicode,1);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {

			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			 {
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
			//alert(strCon)
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
		
		function set_all_data() {

			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			 {
				 
				if(($('#hidden_old_id' + i).val()*1)==1)
				{ 
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
			 }
		}
		
		
		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
    </script>
	<?
	extract($_REQUEST);
	$int_ref = "'".implode("','", explode("*", $txt_int_ref))."'";
	$jobId = implode(",", explode("*", $job_id));
		
		$sql="SELECT ID,PO_NUMBER from wo_po_break_down where status_active=1 and is_deleted=0 and grouping in($int_ref) and job_id in($jobId)";
		// echo $sql;
		$sql_result=sql_select($sql);
		
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="270">PO Number</th>
                </thead>
            </table>
            <div style="width:300px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				
				$i=1;
				 foreach($sql_result as $row)
				 {
				 	$po_no = $row['PO_NUMBER'];
					?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row['ID'].'_'.$po_no; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td width="270">
						<? echo $po_no;?>
                        </td>
                    </tr>
                 	<?
				 	$i++;
				 }
				 ?>
              </table>
           </div>
        <table width="250">
            <tr align="center">
                <td>
            		<div align="left" style="width:50%; float:left">
                        <input id="check_all" type="checkbox" onclick="check_all_data()" name="check_all">
                            Check / Uncheck All
                    </div>
                    <div align="left" style="width:50%; float:left">
                        <input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" />
                    </div>
               </td>
            </tr>
        </table>
         <script>
			set_all_data();
			setFilterGrid("list_view",-1);
		</script>
        <?

	exit();
}

if($action=="color_search_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1,$unicode,1);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {

			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			 {
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
			//alert(strCon)
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
		
		function set_all_data() {

			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			 {
				 
				if(($('#hidden_old_id' + i).val()*1)==1)
				{ 
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
			 }
		}
		
		
		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
    </script>
	<?
	$lib_color=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	extract($_REQUEST);
	$int_ref = "'".implode("','", explode("*", $txt_int_ref))."'";
	$jobId = implode(",", explode("*", $job_id));
	if($txt_order_no!="")
	{
		$po_no = "'".implode("','", explode(",", $txt_order_no))."'";
		$po_no_cond = " and a.po_number in($po_no)";
	}
	
		
	$sql="SELECT a.PO_NUMBER, b.color_number_id as COLOR_ID from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and a.grouping in($int_ref) and a.job_id in($jobId) $po_no_cond and b.status_active in(1,2,3) and b.is_deleted=0 group by a.po_number, b.color_number_id";
	// echo $sql;
	$sql_result=sql_select($sql);
		
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="100">PO Number</th>
                    <th width="170">Color Name</th>
                </thead>
            </table>
            <div style="width:300px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				
				$i=1;
				 foreach($sql_result as $row)
				 {
				 	$color_id = $row['COLOR_ID'];
				 	$color_name = $lib_color[$row['COLOR_ID']];
					?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$color_id.'_'.$color_name; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td width="100"><? echo $row['PO_NUMBER'];?></td>
                        <td width="170"><? echo $lib_color[$color_id];?></td>
                    </tr>
                 	<?
				 	$i++;
				 }
				 ?>
              </table>
           </div>
        <table width="250">
            <tr align="center">
                <td>
            		<div align="left" style="width:50%; float:left">
                        <input id="check_all" type="checkbox" onclick="check_all_data()" name="check_all">
                            Check / Uncheck All
                    </div>
                    <div align="left" style="width:50%; float:left">
                        <input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" />
                    </div>
               </td>
            </tr>
        </table>
         <script>
			set_all_data();
			setFilterGrid("list_view",-1);
		</script>
        <?

	exit();
}

if($action=="report_generate")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name"  ); 
	$size_library=return_library_array( "select id,size_name from lib_size ", "id", "size_name"  ); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
		
	$company_name 	= str_replace("'","",$cbo_company_name);
	$wo_company_name= str_replace("'","",$cbo_wo_company_name);
	$cbo_floor 		= str_replace("'","",$cbo_floor);
	$cbo_location 	= str_replace("'","",$cbo_location);
	$cbo_line 		= str_replace("'","",$cbo_line);
	$buyer_name 	= str_replace("'","",$cbo_buyer_name);
	$season_name 	= str_replace("'","",$cbo_season_name);
	$int_ref 		= str_replace("'","",$txt_int_ref);
	$job_no 		= str_replace("'","",$txt_job_no);
	$style_ref 		= str_replace("'","",$txt_style_ref);
	$order_no 		= str_replace("'","",$txt_order_no);
	$color_id 		= str_replace("'","",$hidden_color_id);
	$date_from 		=  str_replace("'","",$txt_date_from);
	$date_to 		=  str_replace("'","",$txt_date_to);
	if($int_ref!="")
	{
		$int_ref = "'".implode("','", explode("*", $int_ref))."'";
	}
	if($order_no!="")
	{
		$order_no = "'".implode("','", explode(",", $order_no))."'";
	}
	

	$prod_con = "";
	$prod_con .= ($company_name==0) ? "" : " and a.company_id=$company_name";
	$prod_con .= ($wo_company_name==0) ? "" : " and a.serving_company=$wo_company_name";
	$prod_con .= ($cbo_location==0) ? "" : " and a.location=$cbo_location";
	$prod_con .= ($cbo_floor==0) ? "" : " and a.floor_id in($cbo_floor)";
	$prod_con .= ($cbo_line==0) ? "" : " and a.sewing_line=$cbo_line";
	$prod_con .= ($buyer_name==0) ? "" : " and d.buyer_name=$buyer_name";
	$prod_con .= ($season_name==0) ? "" : " and d.season_buyer_wise=$season_name";
	$prod_con .= ($int_ref =="") ? "" : " and e.grouping in($int_ref)";
	$prod_con .= ($job_no =="") ? "" : " and d.job_no_prefix_num=$job_no";
	$prod_con .= ($style_ref =="") ? "" : " and d.style_ref_no='$style_ref'";
	$prod_con .= ($order_no =="") ? "" : " and e.po_number in($order_no)";
	$prod_con .= ($color_id =="") ? "" : " and c.color_number_id in($color_id)";

	if($date_from !="" && $date_to !="")
    {
        if($db_type==0)
        {
            $start_date=change_date_format($date_from,"yyyy-mm-dd","");
            $end_date=change_date_format($date_to,"yyyy-mm-dd","");
        }
        else
        {
            $start_date=date("j-M-Y",strtotime($date_from));
            $end_date=date("j-M-Y",strtotime($date_to));
        }

        $prod_con .= " and a.production_date between '$start_date' and '$end_date'";
      
    }
	// echo $prod_con;die();
	 
	$prod_resource_array=array();
	$dataArray=sql_select("select a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.style_ref_id from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$company_name");

	foreach($dataArray as $row)
	{
		$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_per_hour']=$row[csf('target_per_hour')];
		$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
	}
	
	
    $prod_sql= "SELECT a.FLOOR_ID,a.SEWING_LINE,a.PROD_RESO_ALLO,d.BUYER_NAME,c.color_number_id as COLOR_ID,c.size_number_id as SIZE_ID,a.po_break_down_id as PO_ID, d.style_ref_no as STYLE,e.PO_NUMBER,e.GROUPING,b.PRODUCTION_QNTY,b.REJECT_QTY,a.PRODUCTION_TYPE from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e where a.production_type=b.production_type and a.production_type in(4,5) and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id=e.job_id AND e.id=a.po_break_down_id AND e.id=c.po_break_down_id and d.status_active=1 and d.is_deleted=0 $prod_con order by a.floor_id,a.sewing_line,d.buyer_name,e.po_number"; 
    // echo $prod_sql;
    $prod_res = sql_select($prod_sql);
    $data_arr=array(); 
    $order_qty_arr=array(); 
    $array_check = array();
    $po_id_array = array();
    foreach ($prod_res as  $val) 
    {
    	if($val["PROD_RESO_ALLO"]==1)
	    {
	    	$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$val['SEWING_LINE']]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
			}
			//$line_name=chop($line_name," , ");
			if($val['PRODUCTION_TYPE']==4)
			{
				$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['input_qty'] += $val['PRODUCTION_QNTY'];
			}
			else
			{
				$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['output_qty'] += $val['PRODUCTION_QNTY'];
				$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['rejqty'] += $val['REJECT_QTY'];
			}
			$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['buyer_name'] = $val['BUYER_NAME'];
			$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['style'] = $val['STYLE'];
			$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['po_number'] = $val['PO_NUMBER'];
			$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['grouping'] = $val['GROUPING'];
		}
		else
		{
			$line_name=$lineArr[$val['SEWING_LINE']];
			if($val['PRODUCTION_TYPE']==4)
			{
				$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['input_qty'] += $val['PRODUCTION_QNTY'];
			}
			else
			{
				$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['output_qty'] += $val['PRODUCTION_QNTY'];
				$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['rejqty'] += $val['REJECT_QTY'];
			}
			$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['buyer_name'] = $val['BUYER_NAME'];
			$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['style'] = $val['STYLE'];
			$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['po_number'] = $val['PO_NUMBER'];
			$data_arr[$val['FLOOR_ID']][$line_name][$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['grouping'] = $val['GROUPING'];
		}
		$po_id_array[$val['PO_ID']] = $val['PO_ID'];
    }
   	// echo "<pre>";print_r($data_arr);
   	$all_po_ids = implode(",", $po_id_array);
   	$sql = "SELECT PO_BREAK_DOWN_ID,COLOR_NUMBER_ID,SIZE_NUMBER_ID,ORDER_QUANTITY from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 and po_break_down_id in($all_po_ids)";
   	// echo $sql;
   	$sqlRes = sql_select($sql);
   	foreach ($sqlRes as $val) 
   	{
   		$order_qty_arr[$val['PO_BREAK_DOWN_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']] += $val['ORDER_QUANTITY'];
   	}
			
		 
	ob_start();
	
	?>
    <div style="width:1120px; margin: 0 auto"> 
    	<style type="text/css">
    		table tr th,table tr td{word-wrap: break-word;word-break: break-all;}
    	</style>
        <table width="1100" cellspacing="0" style="margin: 20px 0"> 
            <tr style="border:none;">
            	<td width="1%"></td>
                <td align="right" style="border:none; font-size:14px; font-weight: bold;" width="40%">                                	
                    Company Name                  
                </td>
                <td align="center" style="border:none; font-size:14px;font-weight: bold;" width="5%">                                	
                     :               
                </td>
                <td align="left" style="border:none; font-size:14px;font-weight: bold;" width="55%">                                	
                    <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                 
                </td>
            </tr>
            <tr style="border:none;">
            	<td width="1%"></td>
                <td align="right" style="border:none; font-size:14px;font-weight: bold;" width="40%"> 
                    Location                    
                </td>
                <td align="center" style="border:none; font-size:14px;font-weight: bold;" width="5%"> 
                     :                     
                </td>
                <td align="left" style="border:none; font-size:14px;font-weight: bold;" width="55%"> 
                    <? echo $location_library[str_replace("'","",$cbo_location)]; ?>                     
                </td>
            </tr> 
        </table> 

        <div style="width:1120px">
            <table width="1100" cellspacing="0" border="1" class="rpt_table" rules="all">
                <thead> 	 	 	 	 	 	
                    <tr>
                        <th width="20">Sl.</th>  
                        <th width="100">Floor</th>  
                        <th width="80">Line</th>  
                        <th width="100">Buyer</th>
                        <th width="100">Int Ref</th>
                        <th width="100">Style</th>
                        <th width="100">PO</th>
                        <th width="100">Color</th>
                        <th width="80">Size</th>
                        <th width="80">PO Qty</th>
                        <th width="80">Input</th>
                        <th width="80">Output</th>
                        <th width="80">Reject</th>
                        <!-- <th width="80">WIP</th>
                        <th width="80">Input Bal</th> -->
                     </tr>
                </thead>
            </table>
            <div style="max-height:350px; overflow-y:auto; width:1120px" id="scroll_body">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="1100" rules="all" id="table_body" >
                	<tbody>
                    <?
                    $i=1;
                    $tot_po_qty 	= 0;
                    $tot_in_qty 	= 0;
                    $tot_out_qty 	= 0;
                    $tot_rej_qty 	= 0;
                    $tot_wip_qty 	= 0;
                    $tot_in_bal 	= 0;
                    foreach ($data_arr as $f_id => $f_data) 
                    {
                     	foreach ($f_data as $l_name => $l_data) 
                     	{
                     		foreach ($l_data as $po_id => $po_data) 
                     		{
	                     		foreach ($po_data as $c_id => $c_data) 
	                     		{
	                     			foreach ($c_data as $s_id => $row) 
	                     			{
	                     				$oder_qty = $order_qty_arr[$po_id][$c_id][$s_id];
				                     	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
				                     	?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >										
											<td width="20"><? echo $i;?></td>
											<td width="100"><? echo $floor_library[$f_id];?></td>
											<td width="80"><? echo $l_name;?></td>
											<td width="100"><? echo $buyer_library[$row['buyer_name']];?></td>
											<td width="100"><? echo $row['grouping'];?></td>
											<td width="100"><? echo $row['style'];?></td>
											<td width="100"><? echo $row['po_number'];?></td>
											<td width="100"><? echo $color_library[$c_id];?></td>
											<td width="80"><? echo $size_library[$s_id];?></td>
											<td width="80"><? echo number_format($oder_qty,0);?></td>
											<td align="right" width="80"><? echo number_format($row['input_qty'],0);?></td>
											<td align="right" width="80"><? echo number_format($row['output_qty'],0);?></td>
											<td align="right" width="80"><? echo number_format($row['rejqty'],0);?></td>
											<!-- <td align="right" width="80"><? echo number_format(($row['input_qty'] - $row['output_qty'] - $row['rejqty']),0);?></td>
											<td align="right" width="80"><? echo number_format(($oder_qty-$row['input_qty']),0);?></td> -->
										 </tr>
										<?
										$i++;
					                    $tot_po_qty 	+= $oder_qty;
					                    $tot_in_qty 	+= $row['input_qty'];
					                    $tot_out_qty 	+= $row['output_qty'];
					                    $tot_rej_qty 	+= $row['rejqty'];
					                    $tot_wip_qty 	+= $row['input_qty'] - $row['output_qty'] - $row['rejqty'];
					                    $tot_in_bal 	+= $oder_qty-$row['input_qty'];
									}
								}
							}
						}
					}		                            	
                   ?>
                   </tbody>
               </table>
           </div>
           <div>
               <table width="1100" cellspacing="0" border="1" class="rpt_table" rules="all">
                    <tfoot>	 	 	 	 	 	
	                    <tr>
	                        <th width="20"></th>  
	                        <th width="100"></th>  
	                        <th width="80"></th>  
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80">Grand Total</th>
	                        <th width="80"><? echo number_format($tot_po_qty,0);?></th>
	                        <th width="80"><? echo number_format($tot_in_qty,0);?></th>
	                        <th width="80"><? echo number_format($tot_out_qty,0);?></th>
	                        <th width="80"><? echo number_format($tot_rej_qty,0);?></th>
	                        <!-- <th width="80"><? echo number_format($tot_wip_qty,0);?></th>
	                        <th width="80"><? echo number_format($tot_in_bal,0);?></th> -->
	                    </tr>
	                </tfoot>
                </table>	
            </div>    
        </div>
        <br />
    </div><!-- end main div -->
         
	<?	
	foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w') or die('can not open');	
	$is_created = fwrite($create_new_excel,ob_get_contents()) or die('can not write');
	echo "####".$name;
	exit();
}
?>