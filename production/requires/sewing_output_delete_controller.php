<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

// echo "hello"; die;
function pre($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );     	
    exit(); 
}

/*
|--------------------------------------------------------------------------
| Library Array
|--------------------------------------------------------------------------
|
*/
$size_arr		  = return_library_array( "select id, size_name from lib_size",'id','size_name');
$color_arr		  = return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  ); 
$company_arr 	  = return_library_array( "select id,company_name  from  lib_company", "id", "company_name"  ); 
$location_arr	  = return_library_array( "select id,location_name  from  lib_location", "id", "location_name"  );  
$floor_arr        = return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
$line_library     = return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
$prod_reso_arr	  = return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
$lineDataArr      = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 order by sewing_line_serial"); 
$size_library	  = return_library_array( "select id, size_name from lib_size",'id','size_name');
$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow['ID']]=$lRow['LINE_NAME'];
		$lineSerialArr[$lRow['ID']]=$lRow['SEWING_LINE_SERIAL'];
		// $lastSlNo=$lRow['SEWING_Line_SERIAL'];
	}
/*
|--------------------------------------------------------------------------
| Floor Popup
|--------------------------------------------------------------------------
|
*/
if($action	=="floor_popup")
{
	echo load_html_head_contents("Floor Popup Info","../../", 1, 1, $unicode);
    extract($_REQUEST);  
	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;

    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
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

			$('#hidden_floor_id').val( id );
			$('#hidden_floor_name').val( name );
			parent.emailwindow.hide();
	    }
	</script>
    <?  
    $sql="select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$location' and production_process=5 order by floor_name"; 
	echo  create_list_view("tbl_list_search", "Floor Name", "240","240","180",0, $sql, "js_set_value", "id,floor_name", "", 1, "0", $arr, "floor_name", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);
    echo "<input type='hidden' id='hidden_floor_id' />";
	echo "<input type='hidden' id='hidden_floor_name' />";
	exit();

}

/*
|--------------------------------------------------------------------------
| Line Popup
|--------------------------------------------------------------------------
|
*/
if($action=="line_search_popup")
{		  
	echo load_html_head_contents("Popup Info","../../", 1, 1,$unicode,1);
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
			parent.emailwindow.hide();
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
	//echo $company;die;
	$line_library=return_library_array( "SELECT id,line_name from lib_sewing_line", "id", "line_name");
	if($company==0) $company_name=""; else $company_name=" and b.company_name in($company)";//job_no

		$line_array=array();
		if($txt_date=="")
		{
			$data_format="";
		}
		else
		{
			if($db_type==0)	$data_format=" and b.pr_date = '".change_date_format($txt_date,'yyyy-mm-dd')."'";
			if($db_type==2)	$data_format=" and b.pr_date =  '".change_date_format($txt_date,'','',1)."'";
		}
		if( $location!=0 ) $cond .= " and a.location_id in($location)";
		if( $floor_id!=0 ) $cond.= " and a.floor_id in($floor_id)";
		
		// $line_sql="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number";
		$line_sql="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b ,lib_sewing_line c where a.id=b.mst_id and REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=c.id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond order by c.sewing_line_serial";
		// echo $line_sql;
		$line_sql_result=sql_select($line_sql);
		
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="270">Line Name</th>
                </thead>
            </table>
            <div style="width:300px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				
				$i=1;
				$previous_line_arr=explode(",",$line_id);
				 foreach($line_sql_result as $row)
				 {
        			 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 $flag=0;
					 if(in_array($row[csf('id')],$previous_line_arr))
					 {
						 $flag=1;
					 }
        
					$line_val='';
					$line_id=explode(",",$row[csf('line_number')]);
					foreach($line_id as $line_id)
					{
						if($line_val=="") $line_val=$line_library[$line_id]; else $line_val.=','.$line_library[$line_id];
					}
					?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$line_val; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td width="270">
						<? echo $line_val;?> 
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
/*
|--------------------------------------------------------------------------
| Internal Ref Popup
|--------------------------------------------------------------------------
|
*/
if($action=="intref_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../", 1, 1,'','','');
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
			fn_onClosed();
		}
		
		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
	
    </script>

	<?
	extract($_REQUEST);
	
	$company 	= 	str_replace("'",'',$company); 
	$location 	= 	str_replace("'",'',$location);   
	$floor 		= 	str_replace("'",'',$floor); 
	$line 	    = 	str_replace("'",'',$line); 
	$date 	    = 	str_replace("'",'',$prod_date); 

	$prod_date =change_date_format($prod_date,'dd-mm-yyyy','-',1);

	$cond = '';
	$cond .= $company ? " and c.serving_company = $company " : '';
	$cond .= $location ? " and  c.location = $location " : '';
	$cond .= $floor ? " and  c.floor_id = $floor " : '';
	$cond .= $line ? " and  c.sewing_line = $line " : '';
	$cond .= $date ? " and  c.production_date = '$prod_date' " : '';

	$sql = "Select a.id as job_id, b.grouping as int_ref from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cond";
	// echo $sql; die;
	$job_array = array();
	$sql_res = sql_select($sql);
	foreach ($sql_res as  $v) {
		if ($v['INT_REF']) {
			$job_array[$v['JOB_ID']] =$v['INT_REF'];
		}
	}
		// pre($job_array); die;
		?>
            <input type='hidden' id='hide_order_id' />
            <input type='hidden' id='hide_order_no' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="270">Int. Ref</th>
                </thead>
            </table>
            <div style="width:300px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				
				$i=1; 
				 foreach($job_array as $job =>  $int_ref)
				 {
        			 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 ?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$job.'_'.$int_ref; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td width="270">
						<?= $int_ref;?>
                        <input type="hidden" id="hidden_old_id<? echo $i; ?>" name="hidden_old_id<? echo $i; ?>" value="<?php echo $flag; ?>" />
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
 
/*
|--------------------------------------------------------------------------
| style Popup
|--------------------------------------------------------------------------
|
*/
if($action=="style_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../", 1, 1,'','','');
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
			fn_onClosed();
		}
		
		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
	
    </script>

	<?
	extract($_REQUEST);
	
	$company 	= 	str_replace("'",'',$company); 
	$location 	= 	str_replace("'",'',$location);   
	$floor 		= 	str_replace("'",'',$floor); 
	$line 	    = 	str_replace("'",'',$line); 
	$date 	    = 	str_replace("'",'',$prod_date); 

	$prod_date =change_date_format($prod_date,'dd-mm-yyyy','-',1);

	$cond = '';
	$cond .= $company ? " and c.serving_company = $company " : '';
	$cond .= $location ? " and  c.location = $location " : '';
	$cond .= $floor ? " and  c.floor_id = $floor " : '';
	$cond .= $line ? " and  c.sewing_line = $line " : '';
	$cond .= $date ? " and  c.production_date = '$prod_date' " : '';

	$sql = "Select a.id as job_id, style_ref_no as style from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cond";
	// echo $sql; die;
	$job_array = array();
	$sql_res = sql_select($sql);
	foreach ($sql_res as  $v) {
		$job_array[$v['JOB_ID']] =$v['STYLE'];
	}
		// pre($job_array); die;
		?>
            <input type='hidden' id='hide_order_id' />
            <input type='hidden' id='hide_order_no' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="270">Style</th>
                </thead>
            </table>
            <div style="width:300px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				
				$i=1;
				$previous_line_arr=explode(",",$line_id);
				 foreach($job_array as $job =>  $style)
				 {
        			 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 ?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$job.'_'.$style; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td width="270">
						<?= $style;?>
                        <input type="hidden" id="hidden_old_id<? echo $i; ?>" name="hidden_old_id<? echo $i; ?>" value="<?php echo $flag; ?>" />
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
/*
|--------------------------------------------------------------------------
| Color Popup
|--------------------------------------------------------------------------
|
*/
if($action=="color_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../", 1, 1,'','','');
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
			fn_onClosed();
		}
		
		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
	
    </script>

	<?
	extract($_REQUEST);
	
	$company 	= 	str_replace("'",'',$company); 
	$location 	= 	str_replace("'",'',$location);   
	$floor 		= 	str_replace("'",'',$floor); 
	$line 	    = 	str_replace("'",'',$line); 
	$date 	    = 	str_replace("'",'',$prod_date); 

	$prod_date =change_date_format($prod_date,'dd-mm-yyyy','-',1);

	$cond = '';
	$cond .= $company ? " and c.serving_company = $company " : '';
	$cond .= $location ? " and  c.location = $location " : '';
	$cond .= $floor ? " and  c.floor_id = $floor " : '';
	$cond .= $line ? " and  c.sewing_line = $line " : '';
	$cond .= $date ? " and  c.production_date = '$prod_date' " : '';

	$sql = "Select a.id as job_id, d.color_number_id as color from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c,wo_po_color_size_breakdown d where a.id=b.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $cond";
	// echo $sql; die;
	$job_array = array();
	$sql_res = sql_select($sql);
	foreach ($sql_res as  $v) {
		$job_array[$v['COLOR']] = $color_library[$v['COLOR']];
	}
		// pre($job_array); die;
		?>
            <input type='hidden' id='hide_order_id' />
            <input type='hidden' id='hide_order_no' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="270">Color</th>
                </thead>
            </table>
            <div style="width:300px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				
				$i=1;
				$previous_line_arr=explode(",",$line_id);
				 foreach($job_array as $color_id =>  $color)
				 {
        			 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 ?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$color_id.'_'.$color; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td width="270">
						<?= $color;?>
                        <input type="hidden" id="hidden_old_id<? echo $i; ?>" name="hidden_old_id<? echo $i; ?>" value="<?php echo $flag; ?>" />
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
/*
|--------------------------------------------------------------------------
| Po Popup
|--------------------------------------------------------------------------
|
*/
if($action=="po_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../", 1, 1,'','','');
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
			fn_onClosed();
		}
		
		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
	
    </script>

	<?
	extract($_REQUEST);
	
	$company 	= 	str_replace("'",'',$company); 
	$location 	= 	str_replace("'",'',$location);   
	$floor 		= 	str_replace("'",'',$floor); 
	$line 	    = 	str_replace("'",'',$line); 
	$date 	    = 	str_replace("'",'',$prod_date); 

	$prod_date =change_date_format($prod_date,'dd-mm-yyyy','-',1);

	$cond = '';
	$cond .= $company ? " and c.serving_company = $company " : '';
	$cond .= $location ? " and  c.location = $location " : '';
	$cond .= $floor ? " and  c.floor_id = $floor " : '';
	$cond .= $line ? " and  c.sewing_line = $line " : '';
	$cond .= $date ? " and  c.production_date = '$prod_date' " : '';

	$sql = "Select b.id as po_id, b.po_number  from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cond";
	// echo $sql; die;
	$po_array = array();
	$sql_res = sql_select($sql);
	foreach ($sql_res as  $v) {
		$po_array[$v['PO_ID']] =$v['PO_NUMBER'];
	}
		// pre($po_array); die;
		?>
            <input type='hidden' id='hide_order_id' />
            <input type='hidden' id='hide_order_no' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="270">PO NO</th>
                </thead>
            </table>
            <div style="width:300px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				
				$i=1; 
				 foreach($po_array as $po_id =>  $po_no)
				 {
        			 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 ?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$po_id.'_'.$po_no; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td width="270">
						<?= $po_no;?>
                        <input type="hidden" id="hidden_old_id<? echo $i; ?>" name="hidden_old_id<? echo $i; ?>" value="<?php echo $flag; ?>" />
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
/*
|--------------------------------------------------------------------------
| Generate Report For delete
|--------------------------------------------------------------------------
|
*/
if ($action == 'generate_report') 
{
	// echo load_html_head_contents('Search', '../../', 1, 1, '', '', '');
    // pre($_REQUEST);die;

	extract($_REQUEST);
	$company_id = 	str_replace("'",'',$cbo_company_name); 
	$location 	= 	str_replace("'",'',$cbo_location);   
	$floor 		= 	str_replace("'",'',$text_hidden_floor); 
	$line 	    = 	str_replace("'",'',$hidden_line_id); 
	// $style_ref 	= 	str_replace("*",',',$style_ref); 
	$int_ref 	= 	str_replace("'",'',$txt_int_ref); 
	$color 	    = 	str_replace("'",'',$hidden_color_id); 
	$po_id 	    = 	str_replace("'",'',$hidden_po_id); 
	$prod_date 	= 	str_replace("'",'',$txt_date);  
	$job_id 	= 	str_replace("'",'',$hidden_job_id); 
	 
	$cond = ''; 
	if ($company_id) 	$cond .="and c.serving_company = $company_id " ;
	if ($location)   	$cond .="and c.location in ($location) " ; 
	if ($floor)  		$cond .="and c.floor_id in($floor)" ;
	if ($line)   		$cond .="and c.sewing_line in($line) " ; 
	if ($job_id)   		$cond .="and a.id in($job_id) " ; 
	// if ($int_ref)   	$cond .="and b.grouping = $txt_int_ref" ; 
	if ($color)   	    $cond .="and e.color_number_id in ($color) " ; 
	if ($po_id)   	    $cond .="and c.po_break_down_id in($po_id) " ; 
	if ($prod_date )   	$cond .="and c.production_date = $txt_date " ; 

	$sql = "SELECT d.id prod_dtls_id,c.id as prod_id,a.job_no,a.gmts_item_id as item,c.company_id,c.location,c.floor_id,c.sewing_line,c.serving_company,challan_no,a.style_ref_no as style,b.grouping as int_ref,b.po_number,to_char(c.insert_date,'dd-mm-YYYY') as prod_date,e.color_number_id as color_id,e.size_number_id as size_id ,to_char(c.insert_date, 'HH12:MI') as prod_time,d.production_qnty as prod_qty,c.prod_reso_allo from wo_po_details_master a,wo_po_break_down b ,pro_garments_production_mst c, pro_garments_production_dtls d,wo_po_color_size_breakdown e where a.id=b.job_id and b.id=c.po_break_down_id   and c.id=d.mst_id and b.id=e.po_break_down_id and e.id=d.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and c.production_type=5 and d.production_qnty>0 $cond order by e.size_number_id,c.insert_date";

	// echo $sql; die;
	$data_array = sql_select($sql);
	$width = 1230; 
	?>
	
	<style>
        table td,th{
            vertical-align:middle;
        } 
	</style>
	<body>
    <fieldset style="width:<?= $width+20;?>px;"> 
            <form action="">
                <table width="100%" cellspacing="0">  
                    <div align="center" style="height:auto; width:<?= $width+20;?>px; margin:0 auto; padding:0;">  
                        <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?= $width;?>" rules="all" id="rpt_table_header" align="left">
                            <thead class="form_caption" >	
                                <? $content.=ob_get_flush(); ?>	
                                <tr>
                                    <td colspan="16"></td>
                                    <td  style="color:#444" align="center">Sum=  <span id="total_checked">0</span></td> 
									<td></td>
                                </tr>
                                <tr>
                                    <th width="30">SL</th>
                                    <th width="120">Company</th>
                                    <th width="65">Location</th>
                                    <th width="80">Floor</th>
                                    <th width="65">Line No</th>
                                    <th width="100">WC Name</th>
                                    <th width="70">Prod. ID</th>
                                    <th width="100">Job</th>
                                    <th width="80">Style</th> 
                                    <th width="60">IR No</th>
                                    <th width="50">Color</th>
                                    <th width="70">Item</th>
                                    <th width="70">PO</th>
                                    <th width="40">Size</th>
                                    <th width="60">Prod. Date</th>
                                    <th width="40">Prod. Time</th>
                                    <th width="60">Good Qty</th>
                                    <th width="60"> 
										<input id="checkedAll" type="checkbox" onchange="checkAll()"> 
										<label for="checkedAll">All</label>
									</th>
                                </tr> 
                            </thead>
                        </table>
                        <div style="width:<?= $width+20;?>px; max-height:340px; float:left; overflow-y:scroll;" id="scroll_body">
                            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="production_details_table" width="<?= $width; ?>" rules="all" align="left">
                                <tbody>
                                    <?
                                    $i = 0 ; 
                                    foreach ($data_array as $v) 
                                    {
										// echo $v['PROD_RESO_ALLO'];die;
                                        $i++;
										if($v['PROD_RESO_ALLO']==1)
										{
											$line_name = ""; 
											$sewing_line_id_arr=explode(",",$prod_reso_arr[$v['SEWING_LINE']]);
											foreach ($sewing_line_id_arr as $r) 
											{					 
												$line_name .= ($line_name=="") ? $lineArr[$r] : ",". $lineArr[$r];
											}
										} 
										else
										{
											$line_name=$lineArr[$v['SEWING_LINE']];
										}

                                        
                                        if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
                                        ?>
                                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                                <? $content.=ob_get_flush(); ?>		 
                                                <td width="30"> <?= $i; ?> </td>
                                                <td width="120"><p><?= $company_arr[$v['COMPANY_ID']]?></p> </td>
                                                <td width="65"><p><?= $location_arr[$v['LOCATION']]?></p></td>
                                                <td width="80"><p><?= $floor_arr[$v['FLOOR_ID']]?></p></td>
                                                <td width="65"><p><?= $line_name?></td>
                                                <td width="100"><p><?= $company_arr[$v['SERVING_COMPANY']]?></p></td>
                                                <td width="70" align="right"><p><?= $v['PROD_ID']?></p></td>
                                                <td width="100" align="left"><p><?= $v['JOB_NO']?></p></td>
                                                <td width="80"><p><?= $v['STYLE']?></p></td>
                                                <td width="60"><p><?= $v['INT_REF']?></p></td>
                                                <td width="50" align="center"><p><?=$color_arr[$v['COLOR_ID']]?></p></td>
                                                <td width="70" align="center"><p><?= $garments_item[$v['ITEM']]?></p></td>
                                                <td width="70"><p><?= $v['PO_NUMBER']?></p></td>
                                                <td width="40" align="center"><p><?= $size_arr[$v['SIZE_ID']]?></p></td>
                                                <td width="60" align="center"><p><?= $v['PROD_DATE']?></p></td>
                                                <td width="40" align="center"><p><?= $v['PROD_TIME']?></p></td>
                                                <td width="60" align="right"><p id="checkitemqnty_<?=$i;?>"><?= $v['PROD_QTY']?></p></td>
                                                <td width="60" align="center">
                                                    <input type="checkbox" class="checkSingle" value="<?=$v['PROD_DTLS_ID']?>" onchange="count_check()" id="checkitem_<?=$i;?>" data-prodId="<?=$v['PROD_ID'];?>" data-prodQty="<?= $v['PROD_QTY']?>">  
                                                </td> 
                                            </tr> 
                                        <?
                                    }
                                    ?>
                                </tbody>
                            </table> 
                        </div> 
                    </div>
                </table>    
            </form>    
		</fieldset>
		
		<table>
			<tr>
				 
                <td align="center" valign="middle" class="button_container">
                    <input type="button" id="delete_button" class="formbutton" style="width:70px" value="Delete" onClick="delete_data(2)" />
                    <input type="button" id="refresh_button" class="formbutton" style="width:70px" value="Refresh"/>
                </td>
			</tr>
		</table>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		
    </html>
    <?
    exit(); 
}
/*
|--------------------------------------------------------------------------
| Save Update Delete
|--------------------------------------------------------------------------
|
*/
function bulk_update_sql_statement2( $table, $id_column, $update_column, $data_values, $id_count )
{
	$field_array=explode("*",$update_column);
	$id_count_arr=array_chunk($id_count,'999');
	$sql_up.= "UPDATE $table SET ";
	
	 for ($len=0; $len<count($field_array); $len++)
	 {
		 $sql_up.=" ".$field_array[$len]." = CASE $id_column ";
		 for ($id=0; $id<count($id_count); $id++)
		 {
			 if (trim($data_values[$id_count[$id]][$len])=="") $sql_up.=" when ".$id_count[$id]." then  '".$data_values[$id_count[$id]][$len]."'" ;
			 else $sql_up.=" when ".$id_count[$id]." then  ".$data_values[$id_count[$id]][$len]."" ;
		 }
		 if ($len!=(count($field_array)-1)) $sql_up.=" END, "; else $sql_up.=" END ";
	 }
	 if(count($id_count)>999)
	 {
		$sql_up.=" where";
		$p=1;
		foreach($id_count_arr as $id_arr)
		{
			if($p==1) $sql_up .=" $id_column in(".implode(',',$id_arr).")"; else $sql_up .=" or $id_column in(".implode(',',$id_arr).")";
			$p++;
		}
	 }
	 else
	 {
		$sql_up.=" where $id_column in (".implode(",",$id_count).")";
	 }
	 
	 return $sql_up;     
}


if ($action == 'delete_data') 
{	
	// die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
    $con = connect(); 
    $mst_id_array = array();
    $mst_id_wise_qty_array = array();
    $dtls_id_array  = array(); 
    
    for($j=1; $j<= $row_num; $j++)
    { 
        $dtls_id = "prod_dtls_id_".$j;    
        $prod_id 	=	"prod_id_".$j;  
        $prod_qty 	=	"prod_qty_".$j;  
        if($$dtls_id>0 )
		{
			$dtls_id_array[]=$$dtls_id; 
			$mst_id_array[]=$$prod_id; 
			$mst_id_wise_qty_array[$$prod_id]+=$$prod_qty; 
		} 
    }
     
    // print_r($dtls_id_array); die;
    $mst_ids = implode(",",$mst_id_array);
	$sql = "SELECT id,production_quantity from pro_garments_production_mst where id in($mst_ids) and status_active=1 and is_deleted=0";
	// echo $sql;die;
	$res = sql_select($sql);
	$prod_qty_array = array();
	foreach ($res as $v) 
	{
		$prod_qty_array[$v['ID']] += $v['PRODUCTION_QUANTITY'];
	}

	$update_array_mst="production_quantity*update_date";
	foreach($prod_qty_array as $mst_id=>$qty)
	{
		$update_qty = $qty - $mst_id_wise_qty_array[$mst_id];
		$updateID_array_tr[]=$mst_id;
		$update_data_mst[$mst_id]=explode("*",("'".$update_qty."'*'".$pc_date_time."'"));
	}
	// print_r($prod_qty_array);die;

	// echo bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_tr);die;

    $deleted_id = implode(",",$dtls_id_array);
	$rID1=$rID2=$rID3=true;
    if($deleted_id!="")
    {        
        $rID1=execute_query( "update pro_garments_production_dtls SET status_active=0, is_deleted = 1,updated_by='$user_id' , update_date='$pc_date_time' where id in ($deleted_id)",0);

		$rID2=execute_query( "update pro_garments_prod_dtls_piece SET status_active=0, is_deleted = 1,updated_by='$user_id' , update_date='$pc_date_time' where dtls_id in ($deleted_id)",0);

		$rID3=execute_query( "update pro_gmts_prod_dft SET status_active=0, is_deleted = 1,updated_by='$user_id' , update_date='$pc_date_time' where dtls_id in ($deleted_id)",0);

		$rID4=execute_query(bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_tr));
        
    } 
    // die;
    if($rID1 && $rID2 && $rID3 && $rID4)
    {
        oci_commit($con);  
        echo "1**".$id."**".$mst_id;
    }
    else
    {
        oci_rollback($con);
        echo "6**".$id."**".$mst_id;
    } 
    disconnect($con);
    die;
}	
?>