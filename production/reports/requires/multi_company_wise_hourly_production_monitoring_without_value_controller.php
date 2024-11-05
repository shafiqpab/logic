<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if($action=="load_drop_down_buyer")
{
	
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1
	and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in (".$data.") $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type 
	where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");  
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in (".$data.") 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/multi_company_wise_hourly_production_monitoring_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/multi_company_wise_hourly_production_monitoring_controller' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
	and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
	exit();    	 
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor_id','0','0','','0');\n";
    exit();
}



if($action=="line_search_popup")
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
	//echo $company;die;
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";//job_no

		$line_array=array();
		if($txt_date=="")
		{
			$data_format="";
		}
		else
		{
			if($db_type==0)	$data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
			if($db_type==2)	$data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";
		}
		if( $location!="" ) $cond .= " and a.location_id in(".$location.")";
		if( $floor_id!="" ) $cond.= " and a.floor_id in(".$floor_id.")";
		
		$line_sql="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number";
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

if($action=="company_search_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
	//echo $company;die;

		
		$company_sql="select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name";
		//echo $company_sql;die;
		$company_sql_result=sql_select($company_sql);
		
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="270">Company Name</th>
                </thead>
            </table>
            <div style="width:300px; max-height:350px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				$i=1;
				$previous_company_arr=explode(",",$company);
				
				 foreach($company_sql_result as $row)
				 {
					 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 $flag=0;
					 if(in_array($row[csf('id')],$previous_company_arr))
					 {
						 $flag=1;
					 }
        		?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$row[csf('company_name')]; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td><? echo $row[csf('company_name')];?>
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

if($action=="location_search_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
	
		$location_sql="select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in (".$company.") 
	order by location_name";
		//echo $company_sql;die;
		$location_sql_result=sql_select($location_sql);
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="270">Location Name</th>
                </thead>
            </table>
            <div style="width:300px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				$i=1;
				$previous_location_arr=explode(",",$location);
				
				 foreach($location_sql_result as $row)
				 {
					 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 $flag=0;
					 if(in_array($row[csf('id')],$previous_location_arr))
					 {
						 $flag=1;
					 }
        			
					?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$row[csf('location_name')]; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td><? echo $row[csf('location_name')];?>
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

if($action=="floor_search_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
	//echo $company;die;

	$floor_sql="select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
	and location_id in(".$location.") order by floor_name";
		//echo $company_sql;die;
	$floor_result=sql_select($floor_sql);
		
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="260">Floor Name</th>
                </thead>
            </table>
            <div style="width:300px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				$i=1;
				$previous_floor_arr=explode(",",$floor);
				
				 foreach($floor_result as $row)
				 {
					 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 $flag=0;
					 if(in_array($row[csf('id')],$previous_floor_arr))
					 {
						 $flag=1;
					 }
        			
					?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$row[csf('floor_name')]; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td><? echo $row[csf('floor_name')];?>
                        	<input type="hidden" id="hidden_old_id<? echo $i; ?>" name="hidden_old_id<? echo $i; ?>" value="<?php echo $flag; ?>" />
                        </td>
                    </tr>
                 <?
				 $i++;
				 }
				 ?>
              </table>
              
           </div>
       		<table width="270">
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




if($action=="report_generate") //2nd Button Start...
{
 
	?>
		<style type="text/css">
	            .block_div { 
	                    width:auto;
	                    height:auto;
	                    text-wrap:normal;
	                    vertical-align:bottom;
	                    display: block;
	                    position: !important; 
	                    -webkit-transform: rotate(-90deg);
	                    -moz-transform: rotate(-90deg);
	            }
	          
	        </style> 
	<?
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
	
	
	
	$comapny_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";
	//***************************************************************************************************************************
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}
	
	
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and  a.company_id in($comapny_id) and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");	
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($comapny_id) and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
		
	}
	
	
	
	//==============================shift time===================================================================================================
	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
		
		$group_prod_start_time=sql_select("select min(TIME_FORMAT( prod_start_time, '%H:%i' )) from variable_settings_production where company_name in($comapny_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
		
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
	
	
		$group_prod_start_time=sql_select("select min(TO_CHAR(prod_start_time,'HH24:MI')) as prod_start_time  from variable_settings_production where company_name in($comapny_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
		
	}
	
	
	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}


	$prod_start_hour=$group_prod_start_time[0][csf('prod_start_time')];
	if($prod_start_hour=="") $prod_start_hour="08:00";
	$start_time=explode(":",$prod_start_hour);
	$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
	$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$last_hour;$j++)
	{
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
	//echo $pc_date_time;die;
	$start_hour_arr[$j+1]='23:59';
	if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
	$actual_date=date("Y-m-d");
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
	$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
	$generated_hourarr=array();
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';
	
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else  $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_name";
	}
	
	//if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
	if(str_replace("'","",$cbo_company_id)=="" || str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company in(".str_replace("'","",$cbo_company_id).")";
	
	if(str_replace("'","",$cbo_location_id)=="") 
	{
		$subcon_location="";
		$location="";
	}
	else 
	{
		$location=" and a.location in (".str_replace("'","",$cbo_location_id).")";
		$subcon_location=" and a.location_id in(".str_replace("'","",$cbo_location_id).") ";
	}
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	if($cbo_floor_id=="") $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
    if(str_replace("'","",$hidden_line_id)==0)
	{ 
		$line=""; 
		$subcon_line="";
		$resource_line="";
	}
	else 
	{
		$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
		$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		$resource_line="and a.id in(".str_replace("'","",$hidden_line_id).")";
	}
	$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
	$file_no=str_replace("'","",$txt_file_no);
	$ref_no=str_replace("'","",$txt_ref_no);
	if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
	if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
	//echo $file_cond;
	
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	// echo $txt_date_from; die;
	
	
	$prod_resource_array=array();

	$dataArray_sql=sql_select(" select a.id,a.company_id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity, l.line_name, l.sewing_line_serial, b.line_chief, b.active_machine  from prod_resource_mst a left join lib_sewing_line l on a.line_number=cast(l.id as varchar2(100)),
			prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id in (".$comapny_id.") and b.pr_date=$txt_date and b.is_deleted=0 and c.is_deleted=0 $subcon_location $floor $resource_line order by a.company_id,a.line_marge desc, a.location_id,a.floor_id,l.sewing_line_serial");
	
	
	foreach($dataArray_sql as $val)
	{
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['man_power']=$val[csf('man_power')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['operator']=$val[csf('operator')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['helper']=$val[csf('helper')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['terget_hour']=$val[csf('target_per_hour')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['working_hour']=$val[csf('working_hour')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['day_start']=$val[csf('from_date')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['day_end']=$val[csf('to_date')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['capacity']=$val[csf('capacity')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['smv_adjust']=$val[csf('smv_adjust')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['line_number']=$val[csf('line_number')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['pr_date']=$val[csf('pr_date')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['machine']=$val[csf('active_machine')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['line_chief']=$val[csf('line_chief')];

	}
	
	//print_r($prod_resource_array);die;
	if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

	if($db_type==0)
	{
		$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id in (".$comapny_id.") and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con"); 
	}
	else
	{
		
		$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id in (".$comapny_id.") and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
	}
	
	$line_number_arr=array();
	foreach($dataArray as $val)
	{
		$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
		$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
		$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
	}
	//********************************************************************************************************************************************************
  	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	
	
	if($db_type==0) $prod_start_cond=" min(prod_start_time) as prod_start_time";
	else if($db_type==2) $prod_start_cond="min(TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')) as prod_start_time";
	
	$variable_start_time_arr='';

	$cbo_com_id = str_replace("'","",$cbo_company_id);
	$prod_start_time=sql_select("select $prod_start_cond  from variable_settings_production where company_name in($cbo_com_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	// echo "select $prod_start_cond  from variable_settings_production where company_name in($cbo_com_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		if($db_type==0) $variable_start_time_arr=$row[csf('prod_start_time')];
		else if($db_type==2) $variable_start_time_arr=$ex_time[1];
	}//die;
	//echo $variable_start_time_arr;
	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$variable_date=change_date_format(str_replace("'","",$txt_date)).' '.$variable_start_time_arr;
	//echo $variable_date.'='.$current_date_time;
	$datediff=datediff("n",$variable_date,$current_date_time);
	
	$ex_date_time=explode(" ",$current_date_time);
	$current_date=$ex_date_time[0];
	$current_time=$ex_date_time[1];
	$ex_time=explode(":",$current_time);
	
	$search_prod_date=change_date_format(str_replace("'","",$txt_date));
	//echo $search_prod_date;die;
	
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	//echo $current_date.'='.$search_prod_date;
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=number_format($datediff/60,2);
	$dif_hour_min=date("H", strtotime($dif_time));
	
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
	// echo $smv_source;
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	
    if($smv_source==3)
	{
		$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 
	and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$resultItem=sql_select($sql_item);
	
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
		}
	}
	else
	{
		 $sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$resultItem=sql_select($sql_item);
		
		foreach($resultItem as $itemData)
		{
			if($smv_source==1)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
			}
			if($smv_source==2)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
			}
		}
	}
 
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$txt_date);
		$pr_date_old=explode("-",str_replace("'","",$txt_date));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	if($db_type==0)
	{
		$pr_date=str_replace("'","",$txt_date);
	}
	
	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html="";
	$floor_html="";
    $check_arr=array();
	
	if($db_type==0)
	{
		$sql="select  a.serving_company, a.location, a.floor_id, a.production_date, a.sewing_line,b.job_no, b.buyer_name  as buyer_name,b.style_ref_no,b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,a.remarks,a.production_type,
		sum(CASE WHEN a.production_type=5 THEN production_quantity else 0 END) as good_qnty,
		sum(CASE WHEN a.production_type=4 THEN production_quantity else 0 END) as input_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN   a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			$first=$first+1;
		}
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type in (4,5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond group by a.serving_company, a.location, a.floor_id,a.po_break_down_id, a.production_date,b.total_set_qnty, a.prod_reso_allo, a.sewing_line,b.job_no, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.unit_price,a.remarks,a.production_type";
	}
	else if($db_type==2)
	{
		$sql="select  a.serving_company, a.location, a.floor_id, a.production_date, a.sewing_line,b.job_no,b.buyer_name  as buyer_name,b.style_ref_no, b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,a.remarks,a.production_type,c.grouping,
		sum(CASE WHEN a.production_type=5 THEN production_quantity else 0 END) as good_qnty,
		sum(CASE WHEN a.production_type=4 THEN production_quantity else 0 END) as input_qnty,"; 
		
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			$first++;
		}
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type in(4,5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond group by a.serving_company, a.location, a.floor_id,a.po_break_down_id, a.production_date, a.prod_reso_allo, a.sewing_line, b.job_no,b.total_set_qnty, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.unit_price,a.remarks,a.production_type,c.grouping"; 
		
	}
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$remarks_arr=array();
	$production_serial_arr=array(); $reso_line_ids=''; $all_po_id="";
	foreach($sql_resqlt as $val)
	{

		$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
		$reso_line_ids.=$val[csf('sewing_line')].',';
		
		$line_start=$line_number_arr[$val[csf('sewing_line')]][$val[csf('production_date')]]['prod_start_time'];
		if($line_start!="") 
		{ 
			$line_start_hour=substr($line_start,0,2); 
			if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
		}
		else
		{
			$line_start_hour=$hour; 
		}
		
	 	for($h=$hour;$h<$last_hour;$h++)
		{
			
			//if(
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)]; 
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)]; 
				} 	
			}
			
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
			}
		}
		
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
			} 	
		}
		else
		{
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
		}
		
	 	$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];  
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];
		if($remarks_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val['PRODUCTION_TYPE']]=="")
		{
			$remarks_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val['PRODUCTION_TYPE']]=$val[csf('remarks')];
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['input_qnty']+=$val[csf('input_qnty')];
		
		
		
	 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
		}
	
	 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['grouping'].=",".$val[csf('grouping')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['grouping']=$val[csf('grouping')]; 
		}
		$fob_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')]; 
		
		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('job_no')]."**".$val[csf('ratio')]; 
		}
		else
		{
			 $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('job_no')]."**".$val[csf('ratio')]; 
		}
		
		
		
		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
	}
	// echo "<pre>";
	// print_r($production_data_arr);
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	$po_numIds=chop($all_po_id,','); $poIds_cond="";
	if($all_po_id!='' || $all_po_id!=0)
	{
		if($db_type==2 && $po_ids>1000)
		{
			$poIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$po_numIds),990);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" b.id  in ($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in ($all_po_id)";
		}
	}
	
		
	$sql_item_rate="select b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}
	
	
	
	$input_output_po_arr = array();
	$resout_input_output=sql_select("select a.serving_company, a.location, a.floor_id, a.sewing_line, a.po_break_down_id, a.production_type, a.production_date, a.production_quantity,a.reject_qnty from pro_garments_production_mst a where a.production_type in (5,4) and po_break_down_id in($all_po_id)  and  a.status_active=1 and a.is_deleted=0 $company_name $floor $line");// and a.production_date=$txt_date 
		
	foreach($resout_input_output as $i_val)
	{
		if($i_val[csf('production_type')]==4)
		{
			
			if($input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input_date']!='')
			{
				if(strtotime($input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input_date'])>strtotime($i_val[csf('production_date')]))
				{
					$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input_date']=$i_val[csf('production_date')];
				}
			}
			else
			{
				$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input_date']=$i_val[csf('production_date')];
			}
			
			// $input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]][$i_val[csf('production_type')]]['input']+=$i_val[csf('production_quantity')];
			$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('production_type')]]['input']+=$i_val[csf('production_quantity')];
			
			if(change_date_format($i_val[csf('production_date')])==$search_prod_date)
			{
				$input_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]][change_date_format($i_val[csf('production_date')])]+=$i_val[csf('production_quantity')];
			}
		}
		else
		{
			// $input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]][$i_val[csf('production_type')]]['output']+=$i_val[csf('production_quantity')];
			$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('production_type')]]['output']+=$i_val[csf('production_quantity')];
			$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('production_type')]]['reject']+=$i_val[csf('reject_qnty')];
		}
		$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('production_type')]]['po_break_down_id'].=$i_val[csf('po_break_down_id')].",";
	}
	// echo "<pre>";
	// print_r($input_output_po_arr);
	// echo "</pre>";
	// die();
	
	//print_r($input_output_po_arr);
	
	// subcoutact data *************************************************************************************
	
    if($db_type==0)
    {
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,max(c.smv) as smv,
		sum(CASE WHEN  a.production_type=2 THEN a.production_qnty else 0 END) as good_qnty,
		sum(CASE WHEN  a.production_type=7 THEN a.production_qnty else 0 END) as input_qnty,";  
		
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_sub_contuct.="sum(CASE WHEN  a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_sub_contuct.="sum(CASE WHEN a.hour>'$bg' and a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";	
			}
			$first=$first+1;
   		}
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a  where a.production_type in(2,7) and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 and a.company_id in (".$comapny_id.") $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref order by a.location_id";
	}
	else
	{
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,max(c.smv) as smv,
		sum(CASE WHEN  a.production_type=2 THEN a.production_qnty else 0 END) as good_qnty,
		sum(CASE WHEN a.production_type=7 THEN a.production_qnty else 0 END) as input_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_sub_contuct.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";	
			}
			$first++;
		}
		
	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=5 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a  where a.production_type in (2,7) and a.prod_reso_allo=1 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id in(".$comapny_id.") $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,a.order_id, a.production_date, a.line_id,b.party_id,c.order_no,c.cust_style_ref ";
		
	}
	
	//echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();		
	foreach($sub_result as $subcon_val)
	{
		
		
		$sewing_line_id=$prod_reso_arr[$subcon_val[csf('sewing_line')]];
		
		$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
		$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['input_qnty']+=$subcon_val[csf('input_qnty')];
		
		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']!="")
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']=$subcon_val[csf('buyer_name')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']!="")
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style'].=",".$subcon_val[csf('cust_style_ref')];  
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')]; 
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']!="")
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=$subcon_val[csf('order_id')]; 
		}
		$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
		$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['quantity']+=$subcon_val[csf('good_qnty')];
		
	 	$line_start=$line_number_arr[$val[csf('line_id')]][$val[csf('production_date')]]['prod_start_time']	;
	 	if($line_start!="") 
	 	{ 
			$line_start_hour=substr($line_start,0,2); 
			if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
	 	}
		else
	 	{
			$line_start_hour=$hour; 
	 	}
		for($h=$hour;$h<=$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2).""; 
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$prod_hour]+=$subcon_val[csf($prod_hour)]; 
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				 if( $h>=$line_start_hour && $h<=$actual_time)
				 {
				 $production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	                 } 
			}
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	            }
		 }
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
			} 	
		}
		else
		{
			$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('line_id')]]['prod_hour23']+=$val[csf('prod_hour23')];
	}
	
	// echo "<pre>";
	// print_r($production_data_arr);die;
	
    $avable_min=0;
	$today_product=0;
    $floor_name="";   
    $floor_man_power=0;
	$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
	$total_operator=$total_helper=$gnd_hit_rate=0;   
    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
	$j=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;
	
	//echo "<pre>";
	//print_r($prod_resource_array);die;
if ($type == 0) // Show Button
{
		
	$html.='<tbody>';
	$floor_html.='<tbody>';
	foreach($prod_resource_array as $company_id=>$com_name)
	{
		$global_start_lanch=$start_time_arr[$company_id][1]['lst'];
		
		$html.='<tr  bgcolor="#E8FFFF">
					<td width="" colspan="34"><strong>Company Name:'.$companyArr[$company_id].'</strong></td>';
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
						$html.='<td align="right" width='.$hourwidth.' style='.$bg_color.' ></td>';
					}
					
					
		$html.='<td></td></tr>';
		
		$floor_html.='<tr  bgcolor="#CFCFA0">
					<td width="" colspan="19"><strong>Company Name:'.$companyArr[$company_id].'</strong></td>';
					
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
						$floor_html.='<td align="right" width='.$hourwidth.' style='.$bg_color.' ></td>';
					}
		$floor_html.='</tr>';
		
		foreach($com_name as $lo_id=>$lo_name)
		{
			ksort($lo_name);
			foreach($lo_name as $f_id=>$fname)
			{
				foreach($fname as $resource_id=>$resource_data)
				{
					$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$resource_id]['item_number_id']));
				
					$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['buyer_name']));
					$buyer_name="";
					foreach($buyer_neme_all as $buy)
					{
						if($buyer_name!='') $buyer_name.=',';
						$buyer_name.=$buyerArr[$buy];
					}
					$garment_itemname='';
					$item_smv="";$item_ids='';
					$smv_for_item="";
					$produce_minit="";
					$order_no_total="";
					$efficiency_min=0;
					$tot_po_qty=0;$fob_val=0;$days_run=0;
					$total_input=0; $total_output=0; $min_input_date=''; $total_wip=0; $line_cm_value=0;
					$today_input=0; $total_smv_achive=0;$total_reject=0;

					$total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][4]['input'];
					// echo $total_input."<br>";
					$total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][5]['output'];
					$total_reject+=$input_output_po_arr[$company_id][$f_id][$resource_id][5]['reject'];

					$input_po_ids = implode(",", array_unique(explode(",",chop($input_output_po_arr[$company_id][$f_id][$resource_id][4]['po_break_down_id'],','))));
					$output_po_ids = implode(",", array_unique(explode(",",chop($input_output_po_arr[$company_id][$f_id][$resource_id][5]['po_break_down_id'],','))));
					// echo $input_po_ids."<br>";
					foreach($germents_item as $g_val)
					{
						
						$po_garment_item=explode('**',$g_val);
						if($garment_itemname!='') $garment_itemname.=',';
						$garment_itemname.=$garments_item[$po_garment_item[1]];
						
						if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
						
						// $total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input'];
						// $total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['output'];
						if($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date']!='')
						{
							if($min_input_date!='')
							{
								if(strtotime($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'])<strtotime($min_input_date))
								{
									$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
								}
							}
							else
							{
								$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
							}
						}
						//echo $company_id."*".$f_id."*".$resource_id."*".$po_garment_item[0]."*".$search_prod_date;
						//print_r($input_po_arr);die;
						//echo $input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date]."**";
					
	
						$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
						$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
						if($item_smv!='') $item_smv.='/';
						//echo $po_garment_item[0].'='.$po_garment_item[1];
						$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						
						
						$total_smv_achive+=$input_output_po_arr[$po_garment_item[0]]['output']*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						
						if($order_no_total!="") $order_no_total.=",";
						$order_no_total.=$po_garment_item[0];
						if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						else
						$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
						
						$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						
						$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
						
						$prod_qty=$production_data_arr_qty[$f_id][$resource_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
						$remarks=$remarks_arr[$f_id][$resource_id][$po_garment_item[0]][$po_garment_item[1]][5];
						$btnColor = isset($remarks) ? "Red" : "";
						$dzn_qnty=0; $cm_value=0;
						if($costing_per_arr[$po_garment_item[2]]==1) $dzn_qnty=12;
						else if($costing_per_arr[$po_garment_item[2]]==3) $dzn_qnty=12*2;
						else if($costing_per_arr[$po_garment_item[2]]==4) $dzn_qnty=12*3;
						else if($costing_per_arr[$po_garment_item[2]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$po_garment_item[3];
						$cm_value=($tot_cost_arr[$po_garment_item[2]]/$dzn_qnty)*$prod_qty;
						if(is_nan($cm_value)){ $cm_value=0; }
						
						$line_cm_value+=$cm_value;
						if(is_nan($fob_rate)){ $fob_rate=0; }
						$fob_val+=$prod_qty*$fob_rate;
					}
				
				
					$today_input+=$production_data_arr[$f_id][$resource_id]['input_qnty'];
					//echo $today_input;die;
					//$fob_rate=$tot_po_amt/$tot_po_qty;
					$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['order_id']));
					$subcon_order_id="";
					foreach($subcon_po_id as $sub_val)
					{
						$subcon_po_smv=explode(',',$sub_val); 
						if($sub_val!=0)
						{
							if($item_smv!='') $item_smv.='/';
							if($item_smv!='') $item_smv.='/';
							$item_smv.=$subcon_order_smv[$sub_val];
						}
						$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$sub_val]*$subcon_order_smv[$sub_val];
						if($subcon_order_id!="") $subcon_order_id.=",";
						$subcon_order_id.=$sub_val;
					}
					
					
					if($min_input_date!="")
					{
						$days_run=datediff("d",$min_input_date,$pr_date);
					}
					else  $days_run=0;
					
					$type_line=$production_data_arr[$f_id][$resource_id]['type_line'];
					$prod_reso_allo=$production_data_arr[$f_id][$resource_id]['prod_reso_allo'];
				
					$sewing_line='';
					$line_number=explode(",",$resource_data['line_number']);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
					}

		
					//*********************************************************************************************
					$lunch_start="";
					$lunch_start=$line_number_arr[$resource_id][$pr_date]['lunch_start_time']; 
					$lunch_hour=$start_time_arr[$company_id][1]['lst']; 
					if($lunch_start!="") 
					{ 
						$lunch_start_hour=$lunch_start; 
					}
					else
					{
						$lunch_start_hour=$lunch_hour; 
					}
				 //***************************************************************************************************************************			  
					$production_hour=array();
					for($h=$hour;$h<=$last_hour;$h++)
					{
						 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
						 $production_hour[$prod_hour]=$production_data_arr[$f_id][$resource_id][$prod_hour];
						 $company_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
						 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
						 $total_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
					}
					
					
					// print_r($production_hour);
					$floor_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
					$total_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
					$production_hour['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
					$company_production['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];  
					$line_production_hour=0;
					if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
					{
						
						$line_start=$line_number_arr[$resource_id][$pr_date]['prod_start_time'];
						
						if($line_start!="") 
						{ 
							$line_start_hour=substr($line_start,0,2); 
							if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
						}
						else
						{
							$line_start_hour=$hour; 
						}
						$actual_time_hour=0;
						$total_eff_hour=0;
						for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
						{
							$bg=$start_hour_arr[$lh];
							if($lh<$actual_time)
							{
								$total_eff_hour=$total_eff_hour+1;;	
								$line_hour="prod_hour".substr($bg,0,2)."";
								$line_production_hour+=$production_data_arr[$f_id][$resource_id][$line_hour];
								$line_floor_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
								$line_total_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
								$actual_time_hour=$start_hour_arr[$lh+1];
							}
						}
						// echo $total_eff_hour.'aaaa';
						if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
						
						if($total_eff_hour>$production_data_arr[$f_id][$resource_id]['working_hour'])
						{
							 $total_eff_hour=$production_data_arr[$f_id][$resource_id]['working_hour'];
						}
					}
					
					if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
					{
						for($ah=$hour;$ah<=$last_hour;$ah++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
							$line_production_hour+=$production_data_arr[$f_id][$resource_id][$prod_hour];
							//echo $production_data_arr[$f_id][$ldata][$prod_hour];
							$line_floor_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
							$line_total_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
						}
						
						$total_eff_hour=$resource_data['working_hour'];	
					}
					
					//rtdfgdfgfd 88888888888888888888888888888888888888888888888888888888888888888

					if($cbo_no_prod_type==2 && $line_production_hour>0)
					{
						$current_wo_time=0;
						if($current_date==$search_prod_date)
						{
							$prod_wo_hour=$total_eff_hour;
							if ($dif_time<$prod_wo_hour)//
							{
								$current_wo_time=$dif_hour_min;
								$cla_cur_time=$dif_time;
							}
							else
							{
								$current_wo_time=$prod_wo_hour;
								$cla_cur_time=$prod_wo_hour;
							}
						}
						else
						{
							$current_wo_time=$total_eff_hour;
							$cla_cur_time=$total_eff_hour;
						}
						
						$total_adjustment=0;
						
						$smv_adjustmet_type=$resource_data['smv_adjust_type'];
						$eff_target=($resource_data['terget_hour']*$total_eff_hour);
						
					
						if($total_eff_hour>=$resource_data['working_hour'])
						{
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$resource_data['smv_adjust'];
							if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($resource_data['smv_adjust'])*(-1);
						}
						
						$efficiency_min+=$total_adjustment+($resource_data['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
	
						
						//***************************************************************************************************
						
						//echo $today_input;die;
						
						$man_power=$resource_data['man_power'];	
						$operator=$resource_data['operator'];
						$helper=$resource_data['helper'];
						$terget_hour=$resource_data['terget_hour'];	
						$capacity=$resource_data['capacity'];
						$working_hour=$resource_data['working_hour'];
						
						$floor_capacity+=$resource_data['capacity'];
						$floor_man_power+=$resource_data['man_power'];
						$floor_operator+=$resource_data['operator'];
						$floor_helper+=$resource_data['helper'];
						$floor_tgt_h+=$resource_data['terget_hour'];	
						$floor_working_hour+=$resource_data['working_hour']; 
						$eff_target_floor+=$eff_target;
						$floor_today_product+=$line_production_hour;
						$floor_avale_minute+=$efficiency_min;
						$floor_produc_min+=$produce_minit; 
						$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
						
						$floor_cm_value+=$line_cm_value;
						$floor_total_input+=$total_input;
						$floor_total_output+=$total_output;
						$floor_today_input+=$today_input;
						$floor_total_wip+=($total_input-$total_output-$total_reject);
						
						$total_operator+=$resource_data['operator'];
						$total_man_power+=$resource_data['man_power'];
						$total_helper+=$resource_data['helper'];
						$total_capacity+=$resource_data['capacity'];
						$total_working_hour+=$resource_data['working_hour']; 
						$gnd_total_tgt_h+=$resource_data['terget_hour'];
						$grand_total_terget+=$eff_target;
						$grand_total_product+=$line_production_hour;
						$gnd_avable_min+=$efficiency_min;
						$gnd_product_min+=$produce_minit;
						$gnd_total_fob_val+=$fob_val;
						$gnd_final_total_fob_val+=$fob_val; 
						
						$grand_today_input+=$today_input;
						$grand_total_input+=$total_input;
						$grand_total_output+=$total_output;
						$grand_total_wip+=($total_input-$total_output-$total_reject);
						$grand_cm_value+=$line_cm_value;
						
						
						$company_today_input+=$today_input;
						$company_total_input+=$total_input;
						$company_total_output+=$total_output;
						$company_total_wip+=($total_input-$total_output-$total_reject);
						$company_operator+=$resource_data['operator'];
						$company_man_power+=$resource_data['man_power'];
						$company_helper+=$resource_data['helper'];
						$company_capacity+=$resource_data['capacity'];
						$company_working_hour+=$resource_data['working_hour']; 
						$company_total_tgt_h+=$resource_data['terget_hour'];
						$company_total_terget+=$eff_target;
						$company_total_product+=$line_production_hour;
						$company_avable_min+=$efficiency_min;
						$company_product_min+=$produce_minit;
						$company_total_fob_val+=$fob_val;
						$company_final_total_fob_val+=$fob_val; 
						$company_cm_value+=$line_cm_value;
						
						$floor_total_smv_achive+=$total_smv_achive;
						$company_total_smv_achive+=$total_smv_achive;
						$grand_total_smv_achive+=$total_smv_achive;	
						
						$floor_total_machine+=$resource_data['machine'];
						$company_total_machine+=$resource_data['machine'];
						$grand_total_machine+=$resource_data['machine'];
							
						
						$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
						$po_id=array_unique(explode(",",$po_id));
						$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
						$style=implode(",",array_unique(explode(",",$style)));
			
						$cbo_get_upto=str_replace("'","",$cbo_get_upto);
						$txt_parcentage=str_replace("'","",$txt_parcentage);
					   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
						$floor_name=$floorArr[$f_id];	
						$floor_smv+=$item_smv;
	
						$floor_days_run+=$days_run;
		
						$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
						$styles=explode(",",$style);
						$style_button='';//
						foreach($styles as $sid)
						{
							if( $style_button=='') 
							{ 
								$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
							}
							else
							{
								$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
							}
						}

						$internal_ref_all=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['grouping']));
						$internal_ref="";
						foreach($internal_ref_all as $inter_ref)
						{
							if($internal_ref!='') $internal_ref.=',';
							$internal_ref.=$inter_ref;
						}
						$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
						$as_on_current_hour_target=$terget_hour*$cla_cur_time;
						$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;
						 
						if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td style="word-wrap:break-word; word-break: break-all;" width="40">'.$i.'&nbsp;</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80">'.$floor_name.'&nbsp; </td>
								<td align="center" width="80" style="word-wrap:break-word; word-break: break-all;">'. $sewing_line.'&nbsp; </td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"><p>'.$buyer_name.'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="140"><p>'.$production_data_arr[$f_id][$resource_id]['po_number'].'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$internal_ref.'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$style_button.'&nbsp;</p></td>
								
								<td width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60"><p>'.$item_smv.'</p></td>
								
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$terget_hour.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'.$days_run.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$capacity.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'.$working_hour.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'.$cla_cur_time.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$eff_target.'</td>';
								$string="'";
								$html.='<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'4','".$company_id."',".$txt_date.')">'.$today_input.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'5','".$company_id."',".$txt_date.')">'.$line_production_hour.'</a></td>

								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. number_format($as_on_current_hour_variance,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80" title='.$fob_rate.' align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$resource_id.",'tot_fob_value_popup','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($fob_val,2).'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.number_format($line_cm_value,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.change_date_format($min_input_date).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'1','".$company_id."',".$txt_date.')">'.$total_input.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'2','".$company_id."',".$txt_date.')">'.$total_output.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'3','".$company_id."',".$txt_date.')">'.($total_wip=$total_input-$total_output-$total_reject).'</a></td>
								
								<td style="word-wrap:break-word; word-break: break-all;" width="100" align="right">'. number_format($efficiency_min,2).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$resource_id.",'tot_prod','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($produce_minit,2).'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60" >'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>';
								 
								if($line_efficiency<=$txt_parcentage)
								{
									$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90" bgcolor="red">'.number_format($line_efficiency,2).'%</td>';
								}
								else
								{
									$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90">'.number_format($line_efficiency,2).'%</td>'; 
								}
								
								$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$resource_data['machine'].'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$operator.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="50">'.$helper.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'.$man_power.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'.$resource_data['line_chief'].'</td>';
								
								
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
									
									if($start_hour_arr[$k]==$lunch_start_hour)
									{
										 $bg_color='background:yellow';
									}
									else if($terget_hour>$production_hour[$prod_hour])
									{
										$bg_color='background:red';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else if($terget_hour<$production_hour[$prod_hour])
									{
										$bg_color='background:green';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										$bg_color="";
									}
									
									$html.='<td style="word-wrap:break-word; word-break: break-all;'.$bg_color.'" align="right" width="50" >'.$production_hour[$prod_hour].'</td>';
								}
								$html.='<td style="word-wrap:break-word; word-break: break-all;"><input type="button"  value="View" class="formbutton" onclick="show_line_remarks('.$cbo_company_id.",'".$order_no_total."','".$f_id."','".$resource_id."','remarks_popup',".$txt_date.')"/></td>';
							
						$html.='</tr>';
						$i++;
					}
					if($cbo_no_prod_type==1)
					{
						$current_wo_time=0;
						$cla_cur_time =0;
						if($current_date==$search_prod_date)
						{
							$prod_wo_hour=$total_eff_hour;
							if ($dif_time<$prod_wo_hour)//
							{
								$current_wo_time=$dif_hour_min;
								$cla_cur_time=$dif_time;
							}
							else
							{
								$current_wo_time=$prod_wo_hour;
								$cla_cur_time=$prod_wo_hour;
							}
						}
						else
						{
							$current_wo_time=$total_eff_hour;
							$cla_cur_time=$total_eff_hour;
						}
						
						$total_adjustment=0;
						
						$smv_adjustmet_type=$resource_data['smv_adjust_type'];
						$eff_target=($resource_data['terget_hour']*$total_eff_hour);
						
					
						if($total_eff_hour>=$resource_data['working_hour'])
						{
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$resource_data['smv_adjust'];
							if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($resource_data['smv_adjust'])*(-1);
						}
						
						$efficiency_min+=$total_adjustment+($resource_data['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
	
						
						//****************************************************************************************************************
							
						$man_power=$resource_data['man_power'];	
						$operator=$resource_data['operator'];
						$helper=$resource_data['helper'];
						$terget_hour=$resource_data['terget_hour'];	
						$capacity=$resource_data['capacity'];
						$working_hour=$resource_data['working_hour'];
						
						$floor_capacity+=$resource_data['capacity'];
						$floor_man_power+=$resource_data['man_power'];
						$floor_operator+=$resource_data['operator'];
						$floor_helper+=$resource_data['helper'];
						$floor_tgt_h+=$resource_data['terget_hour'];	
						$floor_working_hour+=$resource_data['working_hour']; 
						$eff_target_floor+=$eff_target;
						$floor_today_product+=$today_product;
						$floor_avale_minute+=$efficiency_min;
						$floor_produc_min+=$produce_minit; 
						$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
						
						$floor_today_input+=$today_input;
						$floor_cm_value+=$line_cm_value;
						$floor_total_input+=$total_input;
						$floor_total_output+=$total_output;
						$floor_total_wip+=($total_input-$total_output)-$total_reject;;
						
						$total_operator+=$resource_data['operator'];
						$total_man_power+=$resource_data['man_power'];
						$total_helper+=$resource_data['helper'];
						$total_capacity+=$resource_data['capacity'];
						$total_working_hour+=$resource_data['working_hour']; 
						$gnd_total_tgt_h+=$resource_data['terget_hour'];
						$grand_total_terget+=$eff_target;
						$grand_total_product+=$today_product;
						$gnd_avable_min+=$efficiency_min;
						$gnd_product_min+=$produce_minit;
						$gnd_total_fob_val+=$fob_val;
						$gnd_final_total_fob_val+=$fob_val; 
						
						$grand_today_input+=$today_input;
						$grand_total_input+=$total_input;
						$grand_total_output+=$total_output;
						$grand_total_wip+=($total_input-$total_output)-$total_reject;
						$grand_cm_value+=$line_cm_value;
						
						$company_today_input+=$today_input;
						$company_total_input+=$total_input;
						$company_total_output+=$total_output;
						$company_total_wip+=($total_input-$total_output)-$total_reject;;
						$company_operator+=$resource_data['operator'];
						$company_man_power+=$resource_data['man_power'];
						$company_helper+=$resource_data['helper'];
						$company_capacity+=$resource_data['capacity'];
						$company_working_hour+=$resource_data['working_hour']; 
						$company_total_tgt_h+=$resource_data['terget_hour'];
						$company_total_terget+=$eff_target;
						$company_total_product+=$line_production_hour;
						$company_avable_min+=$efficiency_min;
						$company_product_min+=$produce_minit;
						$company_total_fob_val+=$fob_val;
						$company_final_total_fob_val+=$fob_val; 
						$company_cm_value+=$line_cm_value;
						
						
						$floor_total_smv_achive+=$total_smv_achive;
						$company_total_smv_achive+=$total_smv_achive;
						$grand_total_smv_achive+=$total_smv_achive;	
						
						
						$floor_total_machine+=$resource_data['machine'];
						$company_total_machine+=$resource_data['machine'];
						$grand_total_machine+=$resource_data['machine'];	
						
						$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
						$po_id=array_unique(explode(",",$po_id));
						$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
						$style=implode(",",array_unique(explode(",",$style)));
			
						$cbo_get_upto=str_replace("'","",$cbo_get_upto);
						$txt_parcentage=str_replace("'","",$txt_parcentage);
					   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
						$floor_name=$floorArr[$f_id];	
						$floor_smv+=$item_smv;
	
						$floor_days_run+=$days_run;
		
						$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
						$styles=explode(",",$style);
						$style_button='';//
						foreach($styles as $sid)
						{
							if( $style_button=='') 
							{ 
								$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
							}
							else
							{
								$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
							}
						}

						$internal_ref_all=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['grouping']));
						$internal_ref="";
						foreach($internal_ref_all as $inter_ref)
						{
							if($internal_ref!='') $internal_ref.=',';
							$internal_ref.=$inter_ref;
						}

						$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
						$as_on_current_hour_target=$terget_hour*$cla_cur_time;
						$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;
						 
						if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td style="word-wrap:break-word; word-break: break-all;" width="40">'.$i.'&nbsp;</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"><p>'.$floor_name.'&nbsp; </p></td>
								<td align="center" width="80" style="word-wrap:break-word; word-break: break-all;"><p>'. $sewing_line.'&nbsp; </p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"><p>'.$buyer_name.'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="140"><p>'.$production_data_arr[$f_id][$resource_id]['po_number'].'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$internal_ref.'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$style_button.'&nbsp;</p></td>
								<td width="120" style="word-wrap:break-word; word-break: break-all;"><p>'.$garment_itemname.'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60"><p>'.$item_smv.'</p></td>
								
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70"><p>'.$terget_hour.'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60"><p>'.$days_run.'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70"><p>'.$capacity.'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60"><p>'.$working_hour.'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60"><p>'.$cla_cur_time.'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80"><p>'.$eff_target.'</p></td>';
								$string="'";
								$html.='<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><p><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'4','".$company_id."',".$txt_date.')">'.$today_input.'</a></p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><p><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'5','".$company_id."',".$txt_date.')">'.$line_production_hour.'</a></p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80"><p>'. number_format($as_on_current_hour_variance,2).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80" title='.$fob_rate.' align="right"><p><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$resource_id.",'tot_fob_value_popup','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($fob_val,2).'</a></p></td>
								
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80"><p>'.number_format($line_cm_value,2).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80"><p>'.change_date_format($min_input_date).'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><p><a href="##" onclick="generate_in_out_popup('.$string.$input_po_ids."','tot_input_output_popup',".$f_id.",".$resource_id.",'1','".$company_id."',".$txt_date.')">'.$total_input.'</a></p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><p><a href="##" onclick="generate_in_out_popup('.$string.$output_po_ids."','tot_input_output_popup',".$f_id.",".$resource_id.",'2','".$company_id."',".$txt_date.')">'.$total_output.'</a></p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><p><a href="##" onclick="generate_in_out_popup('.$string.$input_po_ids.",".$output_po_ids."','tot_input_output_popup',".$f_id.",".$resource_id.",'3','".$company_id."',".$txt_date.')">'.($total_wip=$total_input-$total_output-$total_reject).'</a></p></td>
								
								
								<td style="word-wrap:break-word; word-break: break-all;" width="100" align="right"><p>'. number_format($efficiency_min,2).'</p></td>
								
								<td style="word-wrap:break-word; word-break: break-all;" width="100" align="right"><p><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$resource_id.",'tot_prod','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($produce_minit,2).'</a></p></td>
								
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60" ><p>'. number_format(($line_production_hour/$eff_target)*100,2).'%</p></td>';
								 
								if($line_efficiency<=$txt_parcentage)
								{
									$html.='<td style="word-wrap:break-word; word-break: break-all;" title="Today line efficiency =((produce_minit)*100)/efficiency_min" align="right" width="90" bgcolor="red"><p>'.number_format($line_efficiency,2).'%</p></td>';
								}
								else
								{
									$html.='<td style="word-wrap:break-word; word-break: break-all;" title="Today line efficiency =((produce_minit)*100)/efficiency_min" align="right" width="90"><p>'.number_format($line_efficiency,2).'%</p></td>'; 
								}
								
								$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70"><p>'.$resource_data['machine'].'</p></td>
								
								
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70"><p>'.$operator.'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="50"><p>'.$helper.'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60"><p>'.$man_power.'</p></td>
								
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100"><p>'.$resource_data['line_chief'].'</p></td>';
								$bg_color='';
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									
									if($start_hour_arr[$k]==$lunch_start_hour)
									{
										 $bg_color='background-color:yellow';
									}
									else if($terget_hour>$production_hour[$prod_hour])
									{
										$bg_color='background-color:red';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else if($terget_hour<$production_hour[$prod_hour])
									{
										$bg_color='background-color:green';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										$bg_color="";
									}
									
									$html.='<td title="target='.$terget_hour.',prod='.$production_hour[$prod_hour].'" style="word-wrap:break-word; word-break: break-all;'.$bg_color.'" align="right" width="50"><p>'.$production_hour[$prod_hour].'</p></td>';
									//$html.='<td align="right" width="50"  style=" background-color:#FFFF66" >'.$production_hour[$prod_hour].'&nbsp;kk</td>';
								}
						$html.='<td style="word-wrap:break-word; word-break: break-all;"><p><input type="button"  value="View" class="formbutton" style="background:'.$btnColor.'" onclick="show_line_remarks('.$cbo_company_id.",'".$order_no_total."','".$f_id."','".$resource_id."','remarks_popup',".$txt_date.')"/></p></td>';	
								
						$html.='</tr>';
						$i++;
					}
					//echo $floor_cm_value."***";die;
				}
				
				if($cbo_no_prod_type==2 && $line_floor_production>0)
				{
					$html.='<tr  bgcolor="#B6B6B6">
							<td style="word-wrap:break-word; word-break: break-all;" width="40">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="140">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="120">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">&nbsp;</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'. $floor_tgt_h.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_days_run.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$floor_capacity.'</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_working_hour.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$eff_target_floor.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$floor_today_input.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$line_floor_production.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. ($line_floor_production-$eff_target_floor).';</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.number_format($gnd_total_fob_val,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.number_format($floor_cm_value,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80"></td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_input.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_output.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_wip.'</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. number_format($floor_avale_minute,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. number_format($floor_produc_min,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90">'.number_format($floor_efficency,2).' %</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$floor_total_machine.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$floor_operator.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="50">'. $floor_helper.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_man_power.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100"></td>
							';
							
							$gnd_total_fob_val=0;
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								
								if($start_hour_arr[$k]==$global_start_lanch)
								{
									 $bg_color='background:yellow';
								}
								if($floor_tgt_h>$floor_production[$prod_hour])
								{
									$bg_color='background:red';
									if($floor_production[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else
								{
									 $bg_color='';
								}
						
							
								$html.='<td style="word-wrap:break-word; word-break: break-all;'.$bg_color.'" align="right" width="50">'. $floor_production[$prod_hour].'</td>';
							}
							
						$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width=""></td></tr>';
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
						$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" width="40">'.$j.'&nbsp;</td>
									<td style="word-wrap:break-word; word-break: break-all;" width="80" align="center">'.$floor_name.'&nbsp; </td>
									<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'. $floor_tgt_h.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.$floor_capacity.'</td>
									
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_working_hour.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $eff_target_floor.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$floor_today_input.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$line_floor_production.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'.  number_format($floor_avale_minute,2).'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. number_format($floor_produc_min,2).'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>';
									if($floor_efficency<=$txt_parcentage)
									{
										$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).' %</td>';
									}
									else
									{
										$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90" >'.number_format($floor_efficency,2).' %</td>';
									}
									
						$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_input.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_output.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_wip.'</td>
									
									<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.$floor_operator.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" width="50" align="right">'. $floor_helper.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_man_power.'</td>';
						
									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										if($start_hour_arr[$k]==$global_start_lanch)
										{
										$floor_html.='<td align="right" width="50" style=" background-color:#FFFF66";word-wrap:break-word; word-break: break-all; >'. $floor_production[$prod_hour].'</td>';
										}
										else
										{
										$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="50" >'. $floor_production[$prod_hour].'</td>';
										}
									}	
						
					  $floor_html.='</tr>';
					  $floor_name="";
					  $floor_smv=0;
					  $floor_row=0;
					  $floor_operator=0;
					  $floor_helper=0;
					  $floor_tgt_h=0;
					  $floor_man_power=0;
					  $floor_days_run=0;
					  $eff_target_floor=0;
					  unset($floor_production);
					  $floor_working_hour=0;
					  $line_floor_production=0;
					  $floor_today_product=0;
					  $floor_avale_minute=0;
					  $floor_produc_min=0;
					  $floor_efficency=0;
					  $floor_man_power=0;
					  $floor_capacity=0;
					  $floor_total_machine=0;
					  $floor_today_input=0;
					  $floor_total_input=0;
					  $floor_total_output=0;
					 $floor_total_wip=0;
					 $floor_cm_value=0;
				$j++;	
				}
				if($cbo_no_prod_type==1)
				{
					$html.='<tr  bgcolor="#B6B6B6">
							<td style="word-wrap:break-word; word-break: break-all;" width="40">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="140">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="120">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">&nbsp;</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'. $floor_tgt_h.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_days_run.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$floor_capacity.'</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_working_hour.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$eff_target_floor.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$floor_today_input.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$line_floor_production.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. ($line_floor_production-$eff_target_floor).';</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.number_format($gnd_total_fob_val,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.number_format($floor_cm_value,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80"></td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_input.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_output.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_wip.'</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. number_format($floor_avale_minute,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. number_format($floor_produc_min,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90">'.number_format($floor_efficency,2).' %</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$floor_total_machine.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$floor_operator.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="50">'. $floor_helper.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_man_power.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100"></td>
							';
							
							$gnd_total_fob_val=0;
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								
								if($start_hour_arr[$k]==$global_start_lanch)
								{
									 $bg_color='background:yellow';
								}
								if($floor_tgt_h>$floor_production[$prod_hour])
								{
									$bg_color='background:red';
									if($floor_production[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else
								{
									 $bg_color='';
								}
						
							
								$html.='<td style="word-wrap:break-word; word-break: break-all;'.$bg_color.'" align="right" width="50">'. $floor_production[$prod_hour].'</td>';
							}
							
						$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width=""></td></tr>';
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
						$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" width="40">'.$j.'&nbsp;</td>
									<td style="word-wrap:break-word; word-break: break-all;" width="80" align="center">'.$floor_name.'&nbsp; </td>
									<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'. $floor_tgt_h.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.$floor_capacity.'</td>
									
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_working_hour.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $eff_target_floor.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$floor_today_input.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$line_floor_production.'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'.  number_format($floor_avale_minute,2).'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. number_format($floor_produc_min,2).'</td>
									<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>';
									if($floor_efficency<=$txt_parcentage)
									{
										$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).' %</td>';
									}
									else
									{
										$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90" >'.number_format($floor_efficency,2).' %</td>';
									}
									
									$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_input.'</td>
										<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_output.'</td>
										<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_wip.'</td>
										
										<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.$floor_operator.'</td>
										<td style="word-wrap:break-word; word-break: break-all;" width="50" align="right">'. $floor_helper.'</td>
										<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_man_power.'</td>';
									
									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										// $prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									
										if($start_hour_arr[$k]==$lunch_start_hour)
										{
											 $bg_color='background-color:yellow';
										}
										else if($floor_tgt_h>$floor_production[$prod_hour])
										{
											$bg_color='background-color:red';
											if($floor_production[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else if($floor_tgt_h<$floor_production[$prod_hour])
										{
											$bg_color='background-color:green';
											if($floor_production[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else
										{
											$bg_color="";
										}
										if($start_hour_arr[$k]==$global_start_lanch)
										{
											$floor_html.='<td align="right" width="50" style="'.$bg_color.';word-wrap:break-word; word-break: break-all;" >'. $floor_production[$prod_hour].'</td>';
										}
										else
										{
											$floor_html.='<td style="'.$bg_color.';word-wrap:break-word; word-break: break-all;" align="right" width="50" >'. $floor_production[$prod_hour].'</td>';
										}
									}	
									
								  $floor_html.='</tr>';
								  $floor_name="";
								  $floor_smv=0;
								  $floor_row=0;
								  $floor_operator=0;
								  $floor_helper=0;
								  $floor_tgt_h=0;
								  $floor_man_power=0;
								  $floor_days_run=0;
								  $eff_target_floor=0;
								  unset($floor_production);
								  $floor_working_hour=0;
								  $line_floor_production=0;
								  $floor_today_product=0;
								  $floor_avale_minute=0;
								  $floor_produc_min=0;
								  $floor_efficency=0;
								  $floor_man_power=0;
								  $floor_capacity=0;
								  $floor_total_machine=0;
								  $floor_today_input=0;
								  $floor_total_input=0;
								  $floor_total_output=0;
								 $floor_total_wip=0;
								 $floor_cm_value=0;
							$j++;	
				}
			}
		}
		
		// company total
		$html.='<tr class="tbl_bottom">
				<td style="word-wrap:break-word; word-break: break-all;" width="700" colspan="9">'.$companyArr[$company_id].' Company Total</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'. $company_total_tgt_h.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60"></td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$company_capacity.'</td>
				
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $company_working_hour.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">&nbsp;</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$company_total_terget.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$company_today_input.'</td>
			
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$company_total_product.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. ($company_total_product-$company_total_terget).'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.number_format($company_final_total_fob_val,2).'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.number_format($company_cm_value,2).'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80"></td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $company_total_input.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $company_total_output.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $company_total_wip.'</td>
				
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. number_format($company_avable_min,2).'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. number_format($company_product_min,2).'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. number_format(($company_total_product/$company_total_terget)*100,2).'%</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90">'.number_format(($company_product_min*100/$company_avable_min),2).' %</td>
				
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$company_total_machine.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70">'.$company_operator.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="50">'. $company_helper.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $company_man_power.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="70"></td>';
				
				
				//$gnd_total_fob_val=0;
				for($k=$hour; $k<=$last_hour; $k++)
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
					//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
					
					if($start_hour_arr[$k]==$global_start_lanch)
					{
						 $bg_color='background:yellow';
					}
					if($floor_tgt_h>$floor_production[$prod_hour])
					{
						$bg_color='background:red';
						if($floor_production[$prod_hour]==0)
						{
							$bg_color='';
						}
					}
					else
					{
						 $bg_color='';
					}
					$html.='<td style="word-wrap:break-word; word-break: break-all;'.$bg_color.'" align="right" width="50">'. $company_production[$prod_hour].'</td>';
				}
			
			
		$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width=""></td></tr>';
		
		$floor_html.='<tr class="tbl_bottom">
					<td style="word-wrap:break-word; word-break: break-all;" width="120" colspan="2">Company Total</td>
					<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'. $company_total_tgt_h.'</td>
					<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.$company_capacity.'</td>
					
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $company_working_hour.'</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $company_total_terget.'</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$company_today_input.'</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$company_total_product.'</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. ($company_total_product-$company_total_terget).'</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'.  number_format($company_avable_min,2).'</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. number_format($company_product_min,2).'</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90">'. number_format(($company_total_product/$company_total_terget)*100,2).'%</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90" >'.number_format(($company_product_min*100/$company_avable_min),2).' %</td>';
					
		$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $company_total_input.'</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $company_total_output.'</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $company_total_wip.'</td>
					<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.$company_operator.'</td>
					<td style="word-wrap:break-word; word-break: break-all;" width="50" align="right">'. $company_helper.'</td>
					<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $company_man_power.'</td>';
					
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($start_hour_arr[$k]==$global_start_lanch)
						{
							$floor_html.='<td align="right" width="50" style=" background-color:#FFFF66";word-wrap:break-word; word-break: break-all; >'. $company_production[$prod_hour].'</td>';
						}
						else
						{
							$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="50" >'. $company_production[$prod_hour].'</td>';
						}
					}
		$floor_html.='</tr>';	
		
		$company_total_tgt_h=0;
		$company_capacity=0;
		$company_working_hour=0;
		$company_final_total_fob_val=0;
		$company_total_terget=0;
		$company_total_product=0;
		$company_avable_min=0;
		$company_product_min=0;
		$company_man_power=0;
		$company_helper=0;
		$company_operator=0;
		$company_total_input=0;
		$company_total_output=0;
		$company_total_wip=0;
		$company_cm_value=0;
		$company_today_input=0;
		$company_total_machine=0;
		$company_total_smv_achive=0;
		unset($company_production);
	}
			
			
									
		$html.='</tbody>';
		$floor_html.='</tbody>';
		$smv_for_item="";


		$width_cal=($last_hour-$hour+1)*50+2750;
	?>
               
	<fieldset style="width:2530px">
       <table width="2200" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $report_title; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
            </tr>
        </table>
        <br />
        <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>
                
               
                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>
                
            
            </tr>
        </table>
        <label> <strong>Report Sumarry:-</strong></label> 
          <table id="table_header_2" class="rpt_table" width="2300" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th width="40">SL</th>
                    <th width="80">Floor Name</th>
                    <th width="70">Hourly Target</th>
                    <th width="70">Capacity</th>
                    
                    <th width="60">Line Hour</th>
                    <th width="80">Day Target</th>
                    <th width="80">Today Input</th>
                    <th width="80">Today Prod.</th>
                    <th width="80"> Today Variance </th>
                    <th width="100">Today SMV Avail</th>
                    <th width="100">Today SMV Achv</th>
                    <th width="90">Today Achv %</th>
                    <th width="90">Today Floor Eff. %</th>
                    <th width="80">Total Input</th>
                    <th width="80">Total Prod.</th>
                    <th width="80">WIP</th>
                    <th width="70">Operator</th>
                    <th width="50">Helper</th>
                    <th width="60">Total Man Power</th>
                   
                	<?
                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
					?>
                    	<th width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?	
					}
                	?>
                </tr>
            </thead>
        </table>
        <div style="width:2320px; max-height:400px; overflow-y:scroll" id="scroll_body">
           <table class="rpt_table" width="2300" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
           <?  echo $floor_html; ?> 
            <tfoot>
                   <tr>
                        <th width="40"></th>
                        <th width="80">Group Total </th>
                        <th width="70"><? echo $gnd_total_tgt_h;   ?> </th>
                        <th width="70" align="right"><? echo $total_capacity; ?> </th>
                        
                        <th align="right" width="60"><? echo $total_working_hour; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_terget; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_today_input; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $line_total_production-$grand_total_terget; ?>&nbsp;</th>
                        <th align="right" width="100"><? echo  number_format($gnd_avable_min,2); ?>&nbsp;</th>
                        <th align="right" width="100"><? echo number_format($gnd_product_min,2); ?>&nbsp;</th>
                        <th align="right" width="90"><?    echo number_format(($line_total_production/$grand_total_terget)*100,2)."%"; ?>&nbsp;</th>
                        <th align="center" width="90"><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_input; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_output; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_wip; ?>&nbsp;</th>
                        
                        <th width="70"><? echo $total_operator; ?></th>
                        <th width="50"><? echo $total_helper; ?></th>
                        <th width="60"><? echo $total_man_power; ?>&nbsp;</th>
                        <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						  <th width="50" ><?  echo $total_production[$prod_hour];   ?></th>
						<?	
						}
                		?>
                   </tr>
               </tfoot>

          </table>
        
        </div>
    </br><br/>
        <table id="table_header_1" class="rpt_table" width="<?echo $width_cal; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th style="word-wrap:break-word; word-break: break-all;" width="40">SL</th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Floor Name</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Line No</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Buyer</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="140"><p>Order No</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="100"><p>Internal Ref No</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="100"><p>Style Ref.</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;"  width="120"><p>Garments Item</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="60"><p>SMV</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="70"><p>Hourly Target (Pcs)</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="60"><p>Days Run</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="70"><p>Capacity</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="60"><p>Working Hour</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="60"><p>Current Hour</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Today Target</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Today Input</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Today Prod.</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Today Variance (Pcs)</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Today Prod. FOB value</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Today CM Value</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>1st Input Date</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Total Input</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80"><p>Total Prod.</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="80" title="(Total input - Total Output) - Total reject">WIP</th>
                   
                    <th style="word-wrap:break-word; word-break: break-all;" width="100"><p>Today Avail. Mint</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="100"><p>Today Produce Mint</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="60"><p>Today Achv %</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="90"><p>Today Line Effi %</p></th>
                 
                    <th style="word-wrap:break-word; word-break: break-all;" width="70"><p>Machine</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="70"><p>Operator</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="50"><p>Helper</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="60"><p>TTL Man Power</p></th>
                    <th style="word-wrap:break-word; word-break: break-all;" width="100"><p>Line Chief</p></th>

                   <?
				
                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
						//if($k==$last_hour+1) $hourwidth=''; else $hourwidth='50';
					?>
                      	<th style="word-wrap:break-word; word-break: break-all;" width="50" style="vertical-align:middle"><p><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></p></th>
					<?	
					}
                ?>
                	<th style="word-wrap:break-word; word-break: break-all;" width=""><p> Remarks</p></th>
                </tr>
            </thead>
        </table>
        <div style="width:<?echo $width_cal+20; ?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="<?echo $width_cal; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

            	<tbody>
            		<!-- <tr height="50">
                    <td width="40"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="140"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="120"></td>
                    <td width="60"></td>
                    <td width="70"></td>
                    <td width="60"></td>
                    <td width="70"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80" ></td>
                   
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="60"></td>
                    <td width="90"></td>
                 
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="50"></td>
                    <td width="60"></td>
                    <td width="100"></td>

                   <?
				
                	//for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
						//if($k==$last_hour+1) $hourwidth=''; else $hourwidth='50';
					?>
                      	<td width="50" style="vertical-align:middle"></td>
					<?	
					}
                ?>
                	<td width=""> </td>
                </tr> -->
             	 <? echo $html;  ?>
             	 </tbody>
           		 <tfoot>
                    <tr>
                        <th width="40">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="120">Group Total</th>
                        <th align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
                        
                        <th align="right" width="70"><?  echo $gnd_total_tgt_h; ?>&nbsp;</th>
                        <th align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
                        <th align="right" width="70"><? echo $total_capacity; ?></th>
                        <th align="right" width="60"><? echo $total_working_hour; ?></th>
                        <th align="right" width="60">&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_terget; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_today_input; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $line_total_production-$grand_total_terget; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo number_format($gnd_final_total_fob_val,2);?>&nbsp;</th>
                        <th align="right" width="80"><? echo number_format($grand_cm_value,2);?>&nbsp;</th>
                        <th align="right" width="80"></th>
                        <th align="right" width="80"><? echo $grand_total_input; ?></th>
                        <th align="right" width="80"><? echo $grand_total_output; ?></th>
                        <th align="right" width="80"><? echo $grand_total_wip; ?></th>

                      
                        <th align="right" width="100"><? echo number_format($gnd_avable_min,2); ?>&nbsp;</th>
                        <th align="right" width="100"><? echo number_format($gnd_product_min,2); ?>&nbsp;</th>
                        <th align="right" width="60"><? echo number_format(($line_total_production/$grand_total_terget)*100,2)."%"; ?>&nbsp;</th>
                        <th align="right" width="90" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?>&nbsp;</th>

                        <th align="right" width="70"><? echo $grand_total_machine; ?>&nbsp;</th>
                        <th align="right" width="70"><? echo $total_operator; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_helper; ?>&nbsp;</th>
                        <th align="right" width="60"><? echo $total_man_power; ?>&nbsp;</th>
                        <th align="right" width="100"></th>
                        <?
                        for($k=$hour; $k<=$last_hour; $k++)
                        {
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
							//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
							?>
								<th align="right" width="50"><? echo $total_production[$prod_hour]; ?></th>
							<?	
                        }
                        ?>
                        <th width=""></th>
                    </tr>
                </tfoot>                   
                
            </table>
		</div>
	</fieldset>  
   
 <?    
}
else  // Line wise Summary start
{
		
	$html.='<tbody>';
	foreach($prod_resource_array as $company_id=>$com_name)
	{
		$global_start_lanch=$start_time_arr[$company_id][1]['lst'];
		
		$html.='<tr  bgcolor="#E8FFFF">
					<td width="" colspan="22"><strong>Company Name:'.$companyArr[$company_id].'</strong></td>';
					/*for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
						$html.='<td align="right" width='.$hourwidth.' style='.$bg_color.' ></td>';
					}
					*/
					
		$html.='</tr>';
			
		
		foreach($com_name as $lo_id=>$lo_name)
		{
			ksort($lo_name);
			foreach($lo_name as $f_id=>$fname)
			{
				foreach($fname as $resource_id=>$resource_data)
				{
					$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$resource_id]['item_number_id']));
				
					$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['buyer_name']));
					$buyer_name="";
					foreach($buyer_neme_all as $buy)
					{
						if($buyer_name!='') $buyer_name.=',';
						$buyer_name.=$buyerArr[$buy];
					}
					$garment_itemname='';
					$item_smv="";$item_ids='';
					$smv_for_item="";
					$produce_minit="";
					$order_no_total="";
					$input_po_ids="";
					$output_po_ids="";
					$efficiency_min=0;
					$tot_po_qty=0;$fob_val=0;$days_run=0;
					$total_input=0; $total_output=0; $min_input_date=''; $total_wip=0; $line_cm_value=0;
					$today_input=0; $total_smv_achive=0;$total_reject=0;

					$total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][4]['input'];
					$total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][5]['output'];
					$total_reject+=$input_output_po_arr[$company_id][$f_id][$resource_id][5]['reject'];
					$input_po_ids = implode(",", array_unique(explode(",",chop($input_output_po_arr[$company_id][$f_id][$resource_id][4]['po_break_down_id'],','))));
					$output_po_ids = implode(",", array_unique(explode(",",chop($input_output_po_arr[$company_id][$f_id][$resource_id][5]['po_break_down_id'],','))));
					
					foreach($germents_item as $g_val)
					{
						
						$po_garment_item=explode('**',$g_val);
						if($garment_itemname!='') $garment_itemname.=',';
						$garment_itemname.=$garments_item[$po_garment_item[1]];
						
						if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
						
						// $total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input'];
						// $total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['output'];
						if($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date']!='')
						{
							if($min_input_date!='')
							{
								if(strtotime($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'])<strtotime($min_input_date))
								{
									$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
								}
							}
							else
							{
								$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
							}
						}
						
						
						//echo $company_id."*".$f_id."*".$resource_id."*".$po_garment_item[0]."*".$search_prod_date;
						//print_r($input_po_arr);die;
					//echo $input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date]."**";
						
	
						$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
						$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
						if($item_smv!='') $item_smv.='/';
						//echo $po_garment_item[0].'='.$po_garment_item[1];
						$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						
						
						$total_smv_achive+=$input_output_po_arr[$po_garment_item[0]]['output']*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						
						if($order_no_total!="") $order_no_total.=",";
						$order_no_total.=$po_garment_item[0];
						if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						else
						$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
						
						$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						
						$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
						
						$prod_qty=$production_data_arr_qty[$f_id][$resource_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
						
						$dzn_qnty=0; $cm_value=0;
						if($costing_per_arr[$po_garment_item[2]]==1) $dzn_qnty=12;
						else if($costing_per_arr[$po_garment_item[2]]==3) $dzn_qnty=12*2;
						else if($costing_per_arr[$po_garment_item[2]]==4) $dzn_qnty=12*3;
						else if($costing_per_arr[$po_garment_item[2]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$po_garment_item[3];
						$cm_value=($tot_cost_arr[$po_garment_item[2]]/$dzn_qnty)*$prod_qty;
						if(is_nan($cm_value)){ $cm_value=0; }
						
						$line_cm_value+=$cm_value;
						if(is_nan($fob_rate)){ $fob_rate=0; }
						$fob_val+=$prod_qty*$fob_rate;
					}
				
				
					$today_input+=$production_data_arr[$f_id][$resource_id]['input_qnty'];
					//echo $today_input;die;
					//$fob_rate=$tot_po_amt/$tot_po_qty;
					$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['order_id']));
					$subcon_order_id="";
					foreach($subcon_po_id as $sub_val)
					{
						$subcon_po_smv=explode(',',$sub_val); 
						if($sub_val!=0)
						{
							if($item_smv!='') $item_smv.='/';
							if($item_smv!='') $item_smv.='/';
							$item_smv.=$subcon_order_smv[$sub_val];
						}
						$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$sub_val]*$subcon_order_smv[$sub_val];
						if($subcon_order_id!="") $subcon_order_id.=",";
						$subcon_order_id.=$sub_val;
					}
					
					
					if($min_input_date!="")
					{
						$days_run=datediff("d",$min_input_date,$pr_date);
					}
					else  $days_run=0;
					
					$type_line=$production_data_arr[$f_id][$resource_id]['type_line'];
					$prod_reso_allo=$production_data_arr[$f_id][$resource_id]['prod_reso_allo'];
				
					$sewing_line='';
					$line_number=explode(",",$resource_data['line_number']);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
					}

		
					//********************************************************************************************************
					$lunch_start="";
					$lunch_start=$line_number_arr[$resource_id][$pr_date]['lunch_start_time']; 
					$lunch_hour=$start_time_arr[$company_id][1]['lst']; 
					if($lunch_start!="") 
					{ 
						$lunch_start_hour=$lunch_start; 
					}
					else
					{
						$lunch_start_hour=$lunch_hour; 
					}
				 //*********************************************************************************************			  
					$production_hour=array();
					for($h=$hour;$h<=$last_hour;$h++)
					{
						 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
						 $production_hour[$prod_hour]=$production_data_arr[$f_id][$resource_id][$prod_hour];
						 $company_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
						 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
						 $total_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
					}
					
					
					// print_r($production_hour);
					$floor_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
					$total_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
					$production_hour['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
					$company_production['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];  
					$line_production_hour=0;
					if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
					{
						
						$line_start=$line_number_arr[$resource_id][$pr_date]['prod_start_time'];
						
						if($line_start!="") 
						{ 
							$line_start_hour=substr($line_start,0,2); 
							if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
						}
						else
						{
							$line_start_hour=$hour; 
						}
						$actual_time_hour=0;
						$total_eff_hour=0;
						for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
						{
							$bg=$start_hour_arr[$lh];
							if($lh<$actual_time)
							{
								$total_eff_hour=$total_eff_hour+1;;	
								$line_hour="prod_hour".substr($bg,0,2)."";
								$line_production_hour+=$production_data_arr[$f_id][$resource_id][$line_hour];
								$line_floor_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
								$line_total_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
								$actual_time_hour=$start_hour_arr[$lh+1];
							}
						}
						//echo $total_eff_hour.'aaaa';
						if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
						
						if($total_eff_hour>$production_data_arr[$f_id][$resource_id]['working_hour'])
						{
							 $total_eff_hour=$production_data_arr[$f_id][$resource_id]['working_hour'];
						}
					}
					
					if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
					{
						for($ah=$hour;$ah<=$last_hour;$ah++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
							$line_production_hour+=$production_data_arr[$f_id][$resource_id][$prod_hour];
							//echo $production_data_arr[$f_id][$ldata][$prod_hour];
							$line_floor_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
							$line_total_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
						}
						
						$total_eff_hour=$resource_data['working_hour'];	
					}
					
					

					if($cbo_no_prod_type==2 && $line_production_hour>0)
					{
						$current_wo_time=0;
						if($current_date==$search_prod_date)
						{
							$prod_wo_hour=$total_eff_hour;
							if ($dif_time<$prod_wo_hour)//
							{
								$current_wo_time=$dif_hour_min;
								$cla_cur_time=$dif_time;
							}
							else
							{
								$current_wo_time=$prod_wo_hour;
								$cla_cur_time=$prod_wo_hour;
							}
						}
						else
						{
							$current_wo_time=$total_eff_hour;
							$cla_cur_time=$total_eff_hour;
						}
						
						$total_adjustment=0;
						
						$smv_adjustmet_type=$resource_data['smv_adjust_type'];
						$eff_target=($resource_data['terget_hour']*$total_eff_hour);
						
					
						if($total_eff_hour>=$resource_data['working_hour'])
						{
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$resource_data['smv_adjust'];
							if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($resource_data['smv_adjust'])*(-1);
						}
						
						$efficiency_min+=$total_adjustment+($resource_data['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
						
						//echo $today_input;die;
						
						$man_power=$resource_data['man_power'];	
						$operator=$resource_data['operator'];
						$helper=$resource_data['helper'];
						$terget_hour=$resource_data['terget_hour'];	
						$capacity=$resource_data['capacity'];
						$working_hour=$resource_data['working_hour'];
						
						$floor_capacity+=$resource_data['capacity'];
						$floor_man_power+=$resource_data['man_power'];
						$floor_operator+=$resource_data['operator'];
						$floor_helper+=$resource_data['helper'];
						$floor_tgt_h+=$resource_data['terget_hour'];	
						$floor_working_hour+=$resource_data['working_hour']; 
						$eff_target_floor+=$eff_target;
						$floor_today_product+=$line_production_hour;
						$floor_avale_minute+=$efficiency_min;
						$floor_produc_min+=$produce_minit; 
						$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
						
						$floor_cm_value+=$line_cm_value;
						$floor_total_input+=$total_input;
						$floor_total_output+=$total_output;
						$floor_today_input+=$today_input;
						$floor_total_wip+=($total_input-$total_output-$total_reject);
						
						$total_operator+=$resource_data['operator'];
						$total_man_power+=$resource_data['man_power'];
						$total_helper+=$resource_data['helper'];
						$total_capacity+=$resource_data['capacity'];
						$total_working_hour+=$resource_data['working_hour']; 
						$gnd_total_tgt_h+=$resource_data['terget_hour'];
						$grand_total_terget+=$eff_target;
						$grand_total_product+=$line_production_hour;
						$gnd_avable_min+=$efficiency_min;
						$gnd_product_min+=$produce_minit;
						$gnd_total_fob_val+=$fob_val;
						$gnd_final_total_fob_val+=$fob_val; 
						
						$grand_today_input+=$today_input;
						$grand_total_input+=$total_input;
						$grand_total_output+=$total_output;
						$grand_total_wip+=($total_input-$total_output-$total_reject);
						$grand_cm_value+=$line_cm_value;
						
						
						$company_today_input+=$today_input;
						$company_total_input+=$total_input;
						$company_total_output+=$total_output;
						$company_total_wip+=($total_input-$total_output-$total_reject);
						$company_operator+=$resource_data['operator'];
						$company_man_power+=$resource_data['man_power'];
						$company_helper+=$resource_data['helper'];
						$company_capacity+=$resource_data['capacity'];
						$company_working_hour+=$resource_data['working_hour']; 
						$company_total_tgt_h+=$resource_data['terget_hour'];
						$company_total_terget+=$eff_target;
						$company_total_product+=$line_production_hour;
						$company_avable_min+=$efficiency_min;
						$company_product_min+=$produce_minit;
						$company_total_fob_val+=$fob_val;
						$company_final_total_fob_val+=$fob_val; 
						$company_cm_value+=$line_cm_value;
						
						$floor_total_smv_achive+=$total_smv_achive;
						$company_total_smv_achive+=$total_smv_achive;
						$grand_total_smv_achive+=$total_smv_achive;	
						
						$floor_total_machine+=$resource_data['machine'];
						$company_total_machine+=$resource_data['machine'];
						$grand_total_machine+=$resource_data['machine'];
							
						
						$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
						$po_id=array_unique(explode(",",$po_id));
						$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
						$style=implode(",",array_unique(explode(",",$style)));
			
						$cbo_get_upto=str_replace("'","",$cbo_get_upto);
						$txt_parcentage=str_replace("'","",$txt_parcentage);
					   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
						$floor_name=$floorArr[$f_id];	
						$floor_smv+=$item_smv;
	
						$floor_days_run+=$days_run;
		
						$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
						$styles=explode(",",$style);
						$style_button='';//
						foreach($styles as $sid)
						{
							if( $style_button=='') 
							{ 
								$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
							}
							else
							{
								$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
							}
						}
						$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
						$as_on_current_hour_target=$terget_hour*$cla_cur_time;
						$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;
						 
						if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td style="word-wrap:break-word; word-break: break-all;" width="40">'.$i.'&nbsp;</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80">'.$floor_name.'&nbsp; </td>
								<td align="center" width="80" style="word-wrap:break-word; word-break: break-all;">'. $sewing_line.'&nbsp; </td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"><p>'.$buyer_name.'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="140"><p>'.$production_data_arr[$f_id][$resource_id]['po_number'].'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$style_button.'&nbsp;</p></td>
								<td width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60"><p>'.$item_smv.'</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'.$working_hour.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$eff_target.'</td>';
								$string="'";
								$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.change_date_format($min_input_date).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'4','".$company_id."',".$txt_date.')">'.$today_input.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$input_po_ids."','tot_input_output_popup',".$f_id.",".$resource_id.",'1','".$company_id."',".$txt_date.')">'.$total_input.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'5','".$company_id."',".$txt_date.')">'.$line_production_hour.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$output_po_ids."','tot_input_output_popup',".$f_id.",".$resource_id.",'2','".$company_id."',".$txt_date.')">'.$total_output.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$input_po_ids.','.$output_po_ids."','tot_input_output_popup',".$f_id.",".$resource_id.",'3','".$company_id."',".$txt_date.')">'.($total_wip=$total_input-$total_output-$total_reject).'</a></td>

								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$as_on_current_hour_variance.'</td>

								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60" >'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>';
								$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'.$resource_data['line_chief'].'</td>';
								
								
								/*for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
									
									if($start_hour_arr[$k]==$lunch_start_hour)
									{
										 $bg_color='background:yellow';
									}
									else if($terget_hour>$production_hour[$prod_hour])
									{
										$bg_color='background:red';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else if($terget_hour<$production_hour[$prod_hour])
									{
										$bg_color='background:green';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										$bg_color="";
									}
									
									$html.='<td align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
								}*/
								$html.='<td style="word-wrap:break-word; word-break: break-all;"><input type="button"  value="View" class="formbutton" onclick="show_line_remarks('.$cbo_company_id.",'".$order_no_total."','".$f_id."','".$resource_id."','remarks_popup',".$txt_date.')"/></td>';
							
						$html.='</tr>';
						$i++;
					}
					if($cbo_no_prod_type==1)
					{
						$current_wo_time=0;
						if($current_date==$search_prod_date)
						{
							$prod_wo_hour=$total_eff_hour;
							if ($dif_time<$prod_wo_hour)//
							{
								$current_wo_time=$dif_hour_min;
								$cla_cur_time=$dif_time;
							}
							else
							{
								$current_wo_time=$prod_wo_hour;
								$cla_cur_time=$prod_wo_hour;
							}
						}
						else
						{
							$current_wo_time=$total_eff_hour;
							$cla_cur_time=$total_eff_hour;
						}
						
						$total_adjustment=0;
						
						$smv_adjustmet_type=$resource_data['smv_adjust_type'];
						$eff_target=($resource_data['terget_hour']*$total_eff_hour);
						
					
						if($total_eff_hour>=$resource_data['working_hour'])
						{
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$resource_data['smv_adjust'];
							if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($resource_data['smv_adjust'])*(-1);
						}
						
						$efficiency_min+=$total_adjustment+($resource_data['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
	
						
						//****************************************************************************************************************
							
						$man_power=$resource_data['man_power'];	
						$operator=$resource_data['operator'];
						$helper=$resource_data['helper'];
						$terget_hour=$resource_data['terget_hour'];	
						$capacity=$resource_data['capacity'];
						$working_hour=$resource_data['working_hour'];
						
						$floor_capacity+=$resource_data['capacity'];
						$floor_man_power+=$resource_data['man_power'];
						$floor_operator+=$resource_data['operator'];
						$floor_helper+=$resource_data['helper'];
						$floor_tgt_h+=$resource_data['terget_hour'];	
						$floor_working_hour+=$resource_data['working_hour']; 
						$eff_target_floor+=$eff_target;
						$floor_today_product+=$today_product;
						$floor_avale_minute+=$efficiency_min;
						$floor_produc_min+=$produce_minit; 
						$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
						
						$floor_today_input+=$today_input;
						$floor_cm_value+=$line_cm_value;
						$floor_total_input+=$total_input;
						$floor_total_output+=$total_output;
						$floor_total_wip+=($total_input-$total_output-$total_reject);
						
						$total_operator+=$resource_data['operator'];
						$total_man_power+=$resource_data['man_power'];
						$total_helper+=$resource_data['helper'];
						$total_capacity+=$resource_data['capacity'];
						$total_working_hour+=$resource_data['working_hour']; 
						$gnd_total_tgt_h+=$resource_data['terget_hour'];
						$grand_total_terget+=$eff_target;
						$grand_total_product+=$today_product;
						$gnd_avable_min+=$efficiency_min;
						$gnd_product_min+=$produce_minit;
						$gnd_total_fob_val+=$fob_val;
						$gnd_final_total_fob_val+=$fob_val; 
						
						$grand_today_input+=$today_input;
						$grand_total_input+=$total_input;
						$grand_total_output+=$total_output;
						$grand_total_wip+=($total_input-$total_output-$total_reject);
						$grand_cm_value+=$line_cm_value;
						
						$company_today_input+=$today_input;
						$company_total_input+=$total_input;
						$company_total_output+=$total_output;
						$company_total_wip+=($total_input-$total_output-$total_reject);
						$company_operator+=$resource_data['operator'];
						$company_man_power+=$resource_data['man_power'];
						$company_helper+=$resource_data['helper'];
						$company_capacity+=$resource_data['capacity'];
						$company_working_hour+=$resource_data['working_hour']; 
						$company_total_tgt_h+=$resource_data['terget_hour'];
						$company_total_terget+=$eff_target;
						$company_total_product+=$line_production_hour;
						$company_avable_min+=$efficiency_min;
						$company_product_min+=$produce_minit;
						$company_total_fob_val+=$fob_val;
						$company_final_total_fob_val+=$fob_val; 
						$company_cm_value+=$line_cm_value;
						
						
						$floor_total_smv_achive+=$total_smv_achive;
						$company_total_smv_achive+=$total_smv_achive;
						$grand_total_smv_achive+=$total_smv_achive;	
						
						
						$floor_total_machine+=$resource_data['machine'];
						$company_total_machine+=$resource_data['machine'];
						$grand_total_machine+=$resource_data['machine'];	
						
						$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
						$po_id=array_unique(explode(",",$po_id));
						$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
						$style=implode(",",array_unique(explode(",",$style)));
			
						$cbo_get_upto=str_replace("'","",$cbo_get_upto);
						$txt_parcentage=str_replace("'","",$txt_parcentage);
					   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
						$floor_name=$floorArr[$f_id];	
						$floor_smv+=$item_smv;
	
						$floor_days_run+=$days_run;
		
						$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
						$styles=explode(",",$style);
						$style_button='';//
						foreach($styles as $sid)
						{
							if( $style_button=='') 
							{ 
								$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
							}
							else
							{
								$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
							}
						}
						$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
						$as_on_current_hour_target=$terget_hour*$cla_cur_time;
						$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;
						 
						if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td style="word-wrap:break-word; word-break: break-all;" width="40">'.$i.'&nbsp;</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80">'.$floor_name.'&nbsp; </td>
								<td align="center" width="80" style="word-wrap:break-word; word-break: break-all;">'. $sewing_line.'&nbsp; </td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"><p>'.$buyer_name.'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="140"><p>'.$production_data_arr[$f_id][$resource_id]['po_number'].'&nbsp;</p></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$style_button.'&nbsp;</p></td>
								<td width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60"><p>'.$item_smv.'</p></td>
								
								
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'.$working_hour.'</td>

								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$eff_target.'</td>';
								$string="'";

								$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.change_date_format($min_input_date).'</td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'4','".$company_id."',".$txt_date.')">'.$today_input.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$input_po_ids."','tot_input_output_popup',".$f_id.",".$resource_id.",'1','".$company_id."',".$txt_date.')">'.$total_input.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'5','".$company_id."',".$txt_date.')">'.$line_production_hour.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$output_po_ids."','tot_input_output_popup',".$f_id.",".$resource_id.",'2','".$company_id."',".$txt_date.')">'.$total_output.'</a></td>
								<td style="word-wrap:break-word; word-break: break-all;" width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$input_po_ids.','.$output_po_ids."','tot_input_output_popup',".$f_id.",".$resource_id.",'3','".$company_id."',".$txt_date.')">'.($total_wip=$total_input-$total_output-$total_reject).'</a></td>

								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$as_on_current_hour_variance.'</td>
								<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60" >'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>';	

								$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'.$resource_data['line_chief'].'</td>';
								
								/*for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									
									if($start_hour_arr[$k]==$lunch_start_hour)
									{
										 $bg_color='background:yellow';
									}
									else if($terget_hour>$production_hour[$prod_hour])
									{
										$bg_color='background:red';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else if($terget_hour<$production_hour[$prod_hour])
									{
										$bg_color='background:green';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										$bg_color="";
									}
									
									$html.='<td align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
									//$html.='<td align="right" width="50"  style=" background-color:#FFFF66" >'.$production_hour[$prod_hour].'&nbsp;kk</td>';
								}*/
						$html.='<td style="word-wrap:break-word; word-break: break-all;"><input type="button"  value="View" class="formbutton" onclick="show_line_remarks('.$cbo_company_id.",'".$order_no_total."','".$f_id."','".$resource_id."','remarks_popup',".$txt_date.')"/></td>';	
								
						$html.='</tr>';
						$i++;
					}
					//echo $floor_cm_value."***";die;
				}
				
				if($cbo_no_prod_type==2 && $line_floor_production>0)
				{
					$html.='<tr  bgcolor="#B6B6B6">
							<td style="word-wrap:break-word; word-break: break-all;" width="40">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="140">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="120">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">&nbsp;</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_working_hour.'</td>							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$eff_target_floor.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80"></td>

							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$floor_today_input.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_input.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$line_floor_production.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_output.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_wip.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. ($line_floor_production-$eff_target_floor).';</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>

							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100"></td>
							';
							
							$gnd_total_fob_val=0;
							/*for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								
								if($start_hour_arr[$k]==$global_start_lanch)
								{
									 $bg_color='background:yellow';
								}
								if($floor_tgt_h>$floor_production[$prod_hour])
								{
									$bg_color='background:red';
									if($floor_production[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else
								{
									 $bg_color='';
								}
						
							
								$html.='<td align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
							}*/
							
						$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width=""></td></tr>';
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
					  $floor_name="";
					  $floor_smv=0;
					  $floor_row=0;
					  $floor_operator=0;
					  $floor_helper=0;
					  $floor_tgt_h=0;
					  $floor_man_power=0;
					  $floor_days_run=0;
					  $eff_target_floor=0;
					  unset($floor_production);
					  $floor_working_hour=0;
					  $line_floor_production=0;
					  $floor_today_product=0;
					  $floor_avale_minute=0;
					  $floor_produc_min=0;
					  $floor_efficency=0;
					  $floor_man_power=0;
					  $floor_capacity=0;
					  $floor_total_machine=0;
					  $floor_today_input=0;
					  $floor_total_input=0;
					  $floor_total_output=0;
					 $floor_total_wip=0;
					 $floor_cm_value=0;
				$j++;	
				}
				if($cbo_no_prod_type==1)
				{
					$html.='<tr  bgcolor="#B6B6B6">
							<td style="word-wrap:break-word; word-break: break-all;" width="40">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="80">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="140">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="120">&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">&nbsp;</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $floor_working_hour.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$eff_target_floor.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80"></td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$floor_today_input.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_input.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$line_floor_production.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_output.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $floor_total_wip.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. ($line_floor_production-$eff_target_floor).';</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
							
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100"></td>
							';
							
							$gnd_total_fob_val=0;
							/*for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								
								if($start_hour_arr[$k]==$global_start_lanch)
								{
									 $bg_color='background:yellow';
								}
								if($floor_tgt_h>$floor_production[$prod_hour])
								{
									$bg_color='background:red';
									if($floor_production[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else
								{
									 $bg_color='';
								}
						
							
								$html.='<td align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
							}*/
							
						$html.='<td align="right" width=""></td></tr>';
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
								  $floor_name="";
								  $floor_smv=0;
								  $floor_row=0;
								  $floor_operator=0;
								  $floor_helper=0;
								  $floor_tgt_h=0;
								  $floor_man_power=0;
								  $floor_days_run=0;
								  $eff_target_floor=0;
								  unset($floor_production);
								  $floor_working_hour=0;
								  $line_floor_production=0;
								  $floor_today_product=0;
								  $floor_avale_minute=0;
								  $floor_produc_min=0;
								  $floor_efficency=0;
								  $floor_man_power=0;
								  $floor_capacity=0;
								  $floor_total_machine=0;
								  $floor_today_input=0;
								  $floor_total_input=0;
								  $floor_total_output=0;
								 $floor_total_wip=0;
								 $floor_cm_value=0;
							$j++;	
				}
			}
		}
		
		// company total
		$html.='<tr class="tbl_bottom">
				<td style="word-wrap:break-word; word-break: break-all;" width="700" colspan="8">'.$companyArr[$company_id].' Company Total</td>			
				
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. $company_working_hour.'</td>

				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$company_total_terget.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80"></td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$company_today_input.'</td>	
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $company_total_input.'</td>						
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'.$company_total_product.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $company_total_output.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. $company_total_wip.'</td>
				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="80">'. ($company_total_product-$company_total_terget).'</td>

				<td style="word-wrap:break-word; word-break: break-all;" align="right" width="60">'. number_format(($company_total_product/$company_total_terget)*100,2).'%</td>';

				//$gnd_total_fob_val=0;
				/*for($k=$hour; $k<=$last_hour; $k++)
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
					//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
					
					if($start_hour_arr[$k]==$global_start_lanch)
					{
						 $bg_color='background:yellow';
					}
					if($floor_tgt_h>$floor_production[$prod_hour])
					{
						$bg_color='background:red';
						if($floor_production[$prod_hour]==0)
						{
							$bg_color='';
						}
					}
					else
					{
						 $bg_color='';
					}
					$html.='<td align="right" width="50" style='.$bg_color.' >'. $company_production[$prod_hour].'</td>';
				}*/
			
			
		$html.='<td style="word-wrap:break-word; word-break: break-all;" align="right" width=""></td></tr>';
			
		
		$company_total_tgt_h=0;
		$company_capacity=0;
		$company_working_hour=0;
		$company_final_total_fob_val=0;
		$company_total_terget=0;
		$company_total_product=0;
		$company_avable_min=0;
		$company_product_min=0;
		$company_man_power=0;
		$company_helper=0;
		$company_operator=0;
		$company_total_input=0;
		$company_total_output=0;
		$company_total_wip=0;
		$company_cm_value=0;
		$company_today_input=0;
		$company_total_machine=0;
		$company_total_smv_achive=0;
		unset($company_production);
	}
			
			
									
		$html.='</tbody>';
		$smv_for_item="";
	?>
               
	<fieldset style="width:2030px">
       <table width="1700" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $report_title; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
            </tr>
        </table>
        <br />
        <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>                
               
                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>                
            
            </tr>
        </table>
        
    </br><br/>
        <table id="table_header_1" class="rpt_table" width="1820" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th width="40">SL</th>
                    <th width="80">Floor Name</th>
                    <th width="80">Line No</th>
                    <th width="80">Buyer</th>
                    <th width="140">Order No</th>
                    <th width="100">Style Ref.</th>
                    <th width="120">Garments Item</th>
                    <th width="60">SMV</th>

                    <th width="60">Working Hour</th>
                    <th width="80">Today Target</th>
                    <th width="80">1st Input Date</th>
                    <th width="80">Today Input</th>
                    <th width="80">Total Input</th>
                    <th width="80">Today Prod.</th>
                    <th width="80">Total Prod.</th>
                    <th width="80">WIP</th>
                    <th width="80">Today Variance (Pcs)</th>
                    <th width="60">Today Achv %</th>
                    <th width="100">Line Chief</th>
                   <!-- <?
				
                	//for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
						//if($k==$last_hour+1) $hourwidth=''; else $hourwidth='50';
					?>
                      	<th width="50" style="vertical-align:middle"><div class="block_div"><?  //echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?	
					}
                ?> -->
                	<th width=""> Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:1820; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="1820" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
             	 <? echo $html;  ?>
           		 <tfoot>
                    <tr>
                        <th width="40">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="120">Group Total</th>
                        <th align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
                        
                      
                        <th align="right" width="60"><? echo $total_working_hour; ?></th>

                        <th align="right" width="80"><? echo $grand_total_terget; ?>&nbsp;</th>
                        <th align="right" width="80"></th>
                        <th align="right" width="80"><? echo $grand_today_input; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_input; ?></th>
                        <th align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_output; ?></th>
                        <th align="right" width="80"><? echo $grand_total_wip; ?></th>
                        <th align="right" width="80"><? echo $line_total_production-$grand_total_terget; ?>&nbsp;</th>

                        <th align="right" width="60"><? echo number_format(($line_total_production/$grand_total_terget)*100,2)."%"; ?>&nbsp;</th>   
                                               
                        <th align="right" width="100"></th>
                        <!-- <?
                       // for($k=$hour; $k<=$last_hour; $k++)
                        {
							//$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
							//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
							?>
								<th align="right" width="<?php //echo $hourwidth;?> "><? //echo $total_production[$prod_hour]; ?></th>
							<?	
                        }
                        ?> -->
                        <th width=""></th>
                    </tr>
                </tfoot>                   
                
            </table>
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
	echo "$total_data####$filename####$type";
	exit();      

} // 2nd Button End
// ============================= New Print Button Start ======================================
if($action=="report_generate_new") 
{
	extract($_REQUEST);
	ob_start();
	if ($type == 2) // Production summary
	{
		$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
		$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
		$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
		$garments_item = return_library_array("select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name", "id", "item_name");
		// ================================= GETTING FORM DATA ====================================
		$company_id = str_replace("'","",$cbo_company_id);

		// ================================= MAKE QUERY CONDITION ====================================
		if($company_id == "" || $company_id == 0) $pro_resource_company_name=""; else $pro_resource_company_name="and a.company_id in($company_id)";
		if($company_id == "" || $company_id == 0) $pro_company_name=""; else $pro_company_name="and c.serving_company in($company_id)";

		$date_from = str_replace("'", "", $txt_date);
		$month_year_date_from = date('M-Y',strtotime($date_from));
		$date_from = '01-'.$month_year_date_from;

		$date_to = str_replace("'", "", $txt_date);

		function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y' ) 
		{
			$dates = array();
			$current = strtotime($first);
			$last = strtotime($last);

			while( $current <= $last ) {

				$dates[] = date($output_format, $current);
				$current = strtotime($step, $current);
			}

			return $dates;
		}
		$date_range_arr = get_date_range($date_from,$date_to); 
		//  ============================================= Prod. Resource Sql Query ================================ 
		$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}

		$prod_resource_sql ="SELECT a.company_id,a.location_id,a.floor_id,a.line_number,a.id,c.target_per_hour,c.working_hour,c.pr_date,b.line_chief,c.smv_adjust, b.helper, b.operator
		FROM prod_resource_mst a, prod_resource_dtls_mast b, prod_resource_dtls c
		WHERE     a.id = b.mst_id
		AND a.id = c.mst_id
		AND b.id = c.mast_dtl_id
		AND c.pr_date BETWEEN '$date_to' and '$date_to' $pro_resource_company_name
		AND a.is_deleted = 0
		AND b.is_deleted = 0
		AND c.is_deleted = 0
		ORDER BY a.company_id, a.line_number ASC";
		// echo $prod_resource_sql;

		$prod_resource_sql_result = sql_select($prod_resource_sql);
        
		$company_line_wise_prod_resource_array = array();
		foreach($prod_resource_sql_result as $row){

				$sewing_line_ids=$row[csf('line_number')];
				$sl_ids_arr = explode(",", $sewing_line_ids);
				$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			// echo $sewing_line_id."**".$lineSerialArr[$sewing_line_id]."**".$slNo."<br>";

			$company_line_wise_prod_resource_array[$row[csf('company_id')]][$slNo][$row[csf('line_number')]]['target_per_hour'] = $row[csf('target_per_hour')];
			$company_line_wise_prod_resource_array[$row[csf('company_id')]][$slNo][$row[csf('line_number')]]['working_hour'] = $row[csf('working_hour')];
			$company_line_wise_prod_resource_array[$row[csf('company_id')]][$slNo][$row[csf('line_number')]]['line_chief'] = $row[csf('line_chief')];
			$company_line_wise_prod_resource_array[$row[csf('company_id')]][$slNo][$row[csf('line_number')]]['smv_adjust'] = $row[csf('smv_adjust')];
			$company_line_wise_prod_resource_array[$row[csf('company_id')]][$slNo][$row[csf('line_number')]]['operator'] = $row[csf('operator')];
			$company_line_wise_prod_resource_array[$row[csf('company_id')]][$slNo][$row[csf('line_number')]]['helper'] = $row[csf('helper')];

		}
		// echo "<pre>";
		// print_r($company_line_wise_prod_resource_array);

       // =========================================== Extra Hour SMV SQL ======================================
		$extra_hour_smv_sql = "SELECT a.company_id,
		a.line_number,
		c.pr_date,
		SUM (CASE WHEN d.adjustment_source = 1 THEN d.total_smv ELSE 0 END)
			AS plus_extra_hour_smv,
		SUM (CASE WHEN d.adjustment_source in (2,3,4,5,6,7) THEN d.total_smv ELSE 0 END)
			AS minus_extra_hour_smv
        FROM prod_resource_mst a,prod_resource_dtls_mast b, prod_resource_dtls c, prod_resource_smv_adj  d
        WHERE     a.id = b.mst_id
		AND a.id = c.mst_id
		AND b.id = c.mast_dtl_id
		AND a.id = d.mst_id
		AND b.id = d.mast_dtl_id
		AND c.id = d.dtl_id
		AND c.pr_date BETWEEN '$date_to' and '$date_to' $pro_resource_company_name
		AND a.is_deleted = 0
		AND b.is_deleted = 0
		AND c.is_deleted = 0
		AND d.is_deleted = 0
        GROUP BY a.company_id,a.line_number,c.pr_date
        ORDER BY a.company_id, a.line_number ASC";

		// echo $extra_hour_smv_sql;

		$extra_hour_smv_sql_result = sql_select($extra_hour_smv_sql);

		$company_line_wise_extra_hour_array = array();
		foreach($extra_hour_smv_sql_result as $row){

			$company_line_wise_extra_hour_array[$row[csf('company_id')]][$row[csf('line_number')]]['plus_extra_hour_smv'] = $row[csf('plus_extra_hour_smv')];
			$company_line_wise_extra_hour_array[$row[csf('company_id')]][$row[csf('line_number')]]['minus_extra_hour_smv'] = $row[csf('minus_extra_hour_smv')];

		}
		// echo "<pre>";
		// print_r($company_line_wise_extra_hour_array);

		// =========================================== Production Information SQL ======================================
		$production_sql = " SELECT c.production_date, c.serving_company, c.production_type, c.sewing_line, c.prod_reso_allo, a.buyer_name, a.style_ref_no, a.set_smv, a.avg_unit_price,a.gmts_item_id,b.id,
							SUM(CASE WHEN c.production_type=5 AND d.production_type=5 THEN d.production_qnty ELSE 0 END) AS sewing_output,
							(case when c.production_type=4 then c.production_date end) as input_date
							
							FROM wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d
							WHERE     
							    a.id = b.job_id 
                            AND b.id = c.po_break_down_id
							AND c.id = d.mst_id
							AND c.production_date between '$date_to' and '$date_to' $pro_company_name
							AND c.PRODUCTION_TYPE in (4,5)
							AND a.status_active = 1
							AND a.is_deleted = 0
							AND b.status_active = 1
							AND b.is_deleted = 0
							AND c.status_active = 1
							AND c.is_deleted = 0
							AND d.status_active = 1
							AND d.is_deleted = 0
							GROUP BY c.production_date, c.serving_company, c.production_type, c.sewing_line, c.prod_reso_allo , a.buyer_name, a.style_ref_no, a.set_smv,a.avg_unit_price,a.gmts_item_id,b.id
							ORDER BY c.serving_company, c.sewing_line ASC";
        // echo $production_sql;
		$result_production_sql = sql_select($production_sql);

		$company_wise_line_array = array();
		$po_id_array =array();
		foreach($result_production_sql as $row)
		{
			if($row[csf("prod_reso_allo")]==1)
			{
				// echo $row[csf('sewing_line')]."**";die;
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$resource_id.", ";
				}
				$line_name=chop($line_name," , ");
		
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['sewing_line'] = $row[csf('sewing_line')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['buyer_name'] = $row[csf('buyer_name')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['style_ref_no'] = $row[csf('style_ref_no')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['set_smv'] = $row[csf('set_smv')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['gmts_item_id'] = $row[csf('gmts_item_id')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['sewing_output'] += $row[csf('sewing_output')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['prod_reso_allo'] = $row[csf('prod_reso_allo')];
				// $company_wise_line_array[$row[csf('serving_company')]][$line_name]['input_date'] =  change_date_format($row[csf('input_date')]);
				$po_id_string .= $row[csf('id')].",";
			}
			else
			{
				$line_name=$row[csf('sewing_line')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['sewing_line'] = $row[csf('sewing_line')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['buyer_name'] = $row[csf('buyer_name')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['style_ref_no'] = $row[csf('style_ref_no')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['set_smv'] = $row[csf('set_smv')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['gmts_item_id'] = $row[csf('gmts_item_id')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['sewing_output'] += $row[csf('sewing_output')];
				$company_wise_line_array[$row[csf('serving_company')]][$line_name]['prod_reso_allo'] = $row[csf('prod_reso_allo')];
				// $company_wise_line_array[$row[csf('serving_company')]][$line_name]['input_date'] =  change_date_format($row[csf('input_date')]);
				$po_id_string .= $row[csf('id')].",";
			}

		}
		// echo "$po_id_string";
		$po_id_array = array_unique(array_filter(explode(",",$po_id_string)));
		// echo "<pre>";
		// print_r($po_id_array);

		// =========================================== Production Line Total SQL ======================================
		$production_line_total_sql =" SELECT c.serving_company,c.production_type,c.sewing_line,c.prod_reso_allo,a.style_ref_no,
		SUM (
			CASE
				WHEN c.production_type = 5 AND d.production_type = 5 AND c.production_date <='$date_to'
				THEN
					d.production_qnty
				ELSE
					0
			END)
			AS sewing_output
        FROM wo_po_details_master        a,
		wo_po_break_down            b,
		pro_garments_production_mst c,
		pro_garments_production_dtls d
        WHERE     a.id = b.job_id
		AND b.id = c.po_break_down_id
		AND c.id = d.mst_id
		$pro_company_name
		AND c.PRODUCTION_TYPE IN (5)
		AND a.status_active = 1
		AND a.is_deleted = 0
		AND b.status_active = 1
		AND b.is_deleted = 0
		AND c.status_active = 1
		AND c.is_deleted = 0
		AND d.status_active = 1
		AND d.is_deleted = 0
        GROUP BY 
		c.serving_company,
		c.production_type,
		c.sewing_line,
		c.prod_reso_allo,
		a.style_ref_no
		ORDER BY c.serving_company, c.sewing_line ASC";
		// echo $production_line_total_sql;
        
		$result_production_line_total_sql = sql_select($production_line_total_sql);

        $company_wise_line_style_total_array = array();
		foreach($result_production_line_total_sql as $row){
			if($row[csf("prod_reso_allo")]==1)
			{
				// echo $row[csf('sewing_line')]."**";die;
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$resource_id.", ";
				}
				$line_name=chop($line_name," , ");
		
				$company_wise_line_total_array[$row[csf('serving_company')]][$line_name]['sewing_output'] += $row[csf('sewing_output')];
				$company_wise_line_total_array[$row[csf('serving_company')]][$line_name]['style_ref_no'] .= $row[csf('style_ref_no')]."***";

				$company_wise_line_style_total_array[$row[csf('serving_company')]][$line_name][$row[csf('style_ref_no')]] += $row[csf('sewing_output')];
			}
			else
			{
				$line_name=$row[csf('sewing_line')];
                
				$company_wise_line_total_array[$row[csf('serving_company')]][$line_name]['sewing_output'] += $row[csf('sewing_output')];
				$company_wise_line_total_array[$row[csf('serving_company')]][$line_name]['style_ref_no'] .= $row[csf('style_ref_no')]."***";
                
				$company_wise_line_style_total_array[$row[csf('serving_company')]][$line_name][$row[csf('style_ref_no')]] += $row[csf('sewing_output')];
			}

		}
		// echo "<pre>";
		// print_r($company_wise_line_style_total_array);

		// =========================================== Sewing Input Date SQL ======================================
		$po_id_cond = where_con_using_array($po_id_array,0,"c.po_break_down_id");

		$sewing_input_date_sql = "SELECT c.sewing_line,
		c.serving_company,c.prod_reso_allo,e.style_ref_no,
		MIN (CASE WHEN c.production_type = 4 THEN c.production_date END)
			AS sewing_input_date
        FROM 
		  pro_garments_production_mst   c,
		  wo_po_break_down d,
          wo_po_details_master e
         WHERE  
		 c.po_break_down_id = d.id 
         AND d.job_id = e.id
		 AND c.production_type IN (4) 
		 $pro_company_name $po_id_cond
		 AND c.status_active = 1
         AND c.is_deleted = 0
		 AND d.status_active = 1
         AND d.is_deleted = 0
         AND e.status_active = 1
         AND e.is_deleted = 0
		 GROUP BY c.sewing_line,
         c.serving_company,
         c.prod_reso_allo,
		 e.style_ref_no
		ORDER BY c.serving_company, c.sewing_line ASC";

		// echo $sewing_input_date_sql;
		$result_sewing_input_date_sql = sql_select($sewing_input_date_sql);

        $sewing_input_date_array = array();
		foreach($result_sewing_input_date_sql as $row){
			if($row[csf("prod_reso_allo")]==1)
			{
				// echo $row[csf('sewing_line')]."**";die;
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$resource_id.", ";
				}
				$line_name=chop($line_name," , ");
		
				$sewing_input_date_array[$row[csf('serving_company')]][$line_name]['sewing_input_date'] =  change_date_format($row[csf('sewing_input_date')]);
			}
			else
			{
				$line_name=$row[csf('sewing_line')];

				$sewing_input_date_array[$row[csf('serving_company')]][$line_name]['sewing_input_date'] =  change_date_format($row[csf('sewing_input_date')]);
			}

		}
		
		// echo "<pre>";
		// print_r($sewing_input_date_array);

	 ?>

		<fieldset style="width:1740px">
			<table width="1740" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td align="center"><p style="font-size:25px; font-weight:bold;"><? echo $companyArr[$company_id]; ?><p></td> 
				</tr>
				<tr class="form_caption">
					<td align="center"><p style="font-size:21px; font-weight:bold;">Daily Production Forecast</p></td> 
				</tr>
				<tr class="form_caption">
					<td align="center"><p style="font-size:18px; font-weight:bold;"><? echo "Date: (As On : ".change_date_format( str_replace("'","",trim($txt_date)) ).")"; ?></p></td> 
				</tr>
			</table>
			<br />
			<table id="table_header_1" class="rpt_table" width="1760" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr height="50">
					<th width="100">LINE</th>
					<th width="100">Line Chief Name</th>
					<th width="100">BUYER</th>
					<th width="100">STYLE</th>
					<th width="140">ITEM</th>
					<th width="40">SMV</th>
					<th width="100">LINE INPUT DATE</th>
					<th width="100">TARGET EFFICIENCY</th>
					<th width="100">ACHIEVED EFFICIENCY</th>
					<th width="100">TARGET </th>
					<th width="100">ACHIVED</th>
					<th width="100">SHORT/ EXCESS</th>
					<th width="100"><b>TOTAL OUTPUT</b></th>
				</tr>
			</thead>
			</table>
		<div style="width:1760; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="1760" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
                    <?
					$k = 0;
					// $grand_total_target     = 0;
					// $grand_total_achieved   = 0;
					foreach($company_line_wise_prod_resource_array as $company_id=>$company_value)
					{
						    $total_target                = 0;
							$total_achieved              = 0;
							$total_target_efficieynecy   = 0;
							$total_achieved_efficieynecy = 0;

							$count_target_efficieynecy = 0;
							$count_achieved_efficieynecy = 0;
						ksort($company_value);
						foreach($company_value as $serial_key=>$serial_value)
						{
							foreach($serial_value as $line_key=>$line_value)
							{
								if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
									<td width="100">
										<? 
										    $sewing_line_arr=explode(",",$line_key);
											$sewing_line_name="";
											foreach($sewing_line_arr as $sewing_line_id)
											{
												$sewing_line_name.=$lineArr[$sewing_line_id].",";
											}
											$sewing_line_name=chop($sewing_line_name,",");
											echo $sewing_line_name;
											// echo $lineArr[$line_key];
										?>
									</td>
									<td width="100" align="left"><? echo $line_value['line_chief']; ?></td>
									<td width="100" align="left"><? echo $buyer_library[$company_wise_line_array[$company_id][$line_key]['buyer_name']]; ?></td>
									<td width="100" align="left"><? echo $company_wise_line_array[$company_id][$line_key]['style_ref_no']; ?></td>
									<td width="140" align="left"><? echo $garments_item[$company_wise_line_array[$company_id][$line_key]['gmts_item_id']]; ?></td>
									<td width="40" align="right">
										<? 
										$smv = $company_wise_line_array[$company_id][$line_key]['set_smv']; 
										echo number_format($smv,0); 
										?>
									</td>
									<td width="100" align="center"><? echo $sewing_input_date_array[$company_id][$line_key]['sewing_input_date']; ?></td>
									<td width="100" align="right">
										<?
										$adjust_minute = $company_line_wise_extra_hour_array[$company_id][$line_key]['plus_extra_hour_smv'] - $company_line_wise_extra_hour_array[$company_id][$line_key]['minus_extra_hour_smv'];
										//    echo $adjust_minute."**";

										$tagret_min = ($line_value['target_per_hour'] * $line_value['working_hour']) * $smv;
										$tagret_minute = $tagret_min + $adjust_minute;
										$availavail_minute =  (($line_value['operator'] + $line_value['helper']) * $line_value['working_hour'] * 60) + $adjust_minute;
											$target_efficieynecy = ($tagret_minute / $availavail_minute)*100;
										echo number_format($target_efficieynecy,2)."%";
										$total_target_efficieynecy += $target_efficieynecy;
									
										if($target_efficieynecy>0){
											$count_target_efficieynecy++;
										}
										// echo $count_target_efficieynecy;
										?>
									</td>
									<td width="100" align="right">
										<? 
										$produce_hour = ($smv * $company_wise_line_array[$company_id][$line_key]['sewing_output'])/60;
										//   echo $produce_hour."__";
										//   $achieved_efficieynecy = $produce_hour / ($line_value['working_hour'] * 100);
										$achieved_efficieynecy = ($produce_hour / ($line_value['working_hour'] * 100))*100;
										echo number_format($achieved_efficieynecy,2)."%"; 
										$total_achieved_efficieynecy += $achieved_efficieynecy;

										if($achieved_efficieynecy>0){
											$count_achieved_efficieynecy++;
										}
										// echo $count_achieved_efficieynecy;
										?>
									</td>
									<td width="100" align="right">
										<? 
											$target = $line_value['target_per_hour'] * $line_value['working_hour']; 
											$total_target += $target; 
											echo number_format($target,0);  
										?>
									</td>
									<td width="100" align="right">
										<?  
											$achieved = $company_wise_line_array[$company_id][$line_key]['sewing_output'];
											$total_achieved += $achieved;
											echo number_format($achieved,0);  
										?>
									</td>
									<td width="100" align="right"><? echo $short_excess = $target - $achieved; ?></td>
									<td width="100" align="right" style="font-weight:bold;">
										<? 
										 $style = $company_wise_line_array[$company_id][$line_key]['style_ref_no'];
										 echo number_format($company_wise_line_style_total_array[$company_id][$line_key][$style],0); 
										?>
								    </td>
								</tr>
							<?
							$k++;
							}
					    }
						?>
						<tr>
							<td colspan="5" align="center"style="font-weight:bold;"> <? echo $companyArr[$company_id]; ?> Sewing Line Total</td>
							<td width="40">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100" align="right"style="font-weight:bold;">
							    <? 
								  $total_avg_target_efficieynecy = $total_target_efficieynecy/$count_target_efficieynecy; 
								  echo number_format($total_avg_target_efficieynecy,2)."%"; 

								  $grand_total_target_efficieynecy += $total_avg_target_efficieynecy;
								  if($grand_total_target_efficieynecy>0){
									$count_grand_total_target_efficieynecy++;
								  }
								//   echo $count_grand_total_target_efficieynecy;
								?>
							</td>
							<td width="100" align="right"style="font-weight:bold;">
							    <? 
								  $total_avg_achieved_efficieynecy = $total_achieved_efficieynecy/$count_achieved_efficieynecy; 
								  echo number_format($total_avg_achieved_efficieynecy,2)."%"; 

								  $grand_total_achieved_efficieynecy += $total_avg_achieved_efficieynecy;
								  if($grand_total_achieved_efficieynecy>0){
									$count_grand_total_achieved_efficieynecy++;
								  }
								//   echo $count_grand_total_achieved_efficieynecy;
								?>
							</td>
							<td width="100" align="right"style="font-weight:bold;">
								<? 
								  echo number_format($total_target,0); 
								  $grand_total_target += $total_target;
								?>
							</td>
							<td width="100" align="right"style="font-weight:bold;">
								<? 
								   echo number_format($total_achieved,0);
								   $grand_total_achieved += $total_achieved; 
								?>
						    </td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
						</tr> 
						<?
					}
					?>
				</tbody> 
				<tfoot>
					<tr style="background:#dfdfdf;">
						<td colspan="5" align="center"style="font-weight:bold;"> Grand Total</td>
						<td width="40">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100" align="right"style="font-weight:bold;">
						 <? 
						   $grand_total_avg_target_efficieynecy = $grand_total_target_efficieynecy/$count_grand_total_target_efficieynecy;
						  echo number_format($grand_total_avg_target_efficieynecy,2)."%";
						 ?>
					    </td>
						<td width="100" align="right"style="font-weight:bold;">
						  <?
						   $grand_total_avg_achieved_efficieynecy = $grand_total_achieved_efficieynecy/$count_grand_total_achieved_efficieynecy;
						    echo number_format($grand_total_avg_achieved_efficieynecy,2)."%"; 
						  ?>
						 </td>
						<td width="100" align="right"style="font-weight:bold;"><? echo number_format($grand_total_target,0); ?></td>
						<td width="100" align="right"style="font-weight:bold;"><? echo number_format($grand_total_achieved,0); ?></td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
					</tr> 
				</tfoot>                  
			</table>
		</div>
		</fieldset>  
	 <?
	}
	else if ($type == 3) // Monthly Production Report
	{
		$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
		// ================================= GETTING FORM DATA ====================================
		$company_id = str_replace("'","",$cbo_company_id);

		// ================================= MAKE QUERY CONDITION ====================================
		if($company_id == "" || $company_id == 0) $company_name=""; else $company_name="and a.working_company_id in($company_id)";
		if($company_id == "" || $company_id == 0) $pro_company_name=""; else $pro_company_name="and c.serving_company in($company_id)";

		$date_from = str_replace("'", "", $txt_date);
		$month_year_date_from = date('M-Y',strtotime($date_from));
		$date_from = '01-'.$month_year_date_from;

		$date_to = str_replace("'", "", $txt_date);

		function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y' ) 
		{
			$dates = array();
			$current = strtotime($first);
			$last = strtotime($last);

			while( $current <= $last ) {

				$dates[] = date($output_format, $current);
				$current = strtotime($step, $current);
			}

			return $dates;
		}
		$date_range_arr = get_date_range($date_from,$date_to); 

		// =========================================== Cut Lay Information SQL ================================================================
		$cutting_sql ="SELECT a.entry_date, a.working_company_id, b.marker_qty
					FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b
					WHERE     a.id = b.mst_id
					AND a.entry_date between '$date_from' and '$date_to' $company_name
					AND a.entry_form IN (99,289)
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND b.status_active = 1
					AND b.is_deleted = 0";
			//  echo $cutting_sql;
		$result_cutting_sql=sql_select($cutting_sql);

		$cutting_array = array();
		$company_wise_cutting_array = array();
		$company_wise_cutting_date_array = array();

		foreach($result_cutting_sql as $row){
			$cutting_array[date('d-M-Y',strtotime($row[csf('entry_date')]))]['cutting_qty'] += $row[csf('marker_qty')];
			$company_wise_cutting_array[$row[csf('working_company_id')]]['cutting_qty'] += $row[csf('marker_qty')];

			$company_wise_cutting_date_array[$row[csf('working_company_id')]][date('d-M-Y',strtotime($row[csf('entry_date')]))]['cutting_qty'] += $row[csf('marker_qty')];
		}
        
		$cuttig_date_count_array =array();
		foreach($company_wise_cutting_date_array as $working_com_id => $working_com_value){
			foreach($working_com_value as $cutting_date => $cutting_date_value){
			    if($cutting_date_value['cutting_qty'] !=0){
                       $cuttig_date_count_array[$working_com_id]['cutting_qty']++ ;
				}
			}
		}
		// echo "<pre>";
		// print_r($cuttig_date_count_array);
        
		// =========================================== Production Information SQL ================================================================
		$production_sql = " SELECT c.production_date, c.serving_company, c.production_type,
		                    SUM(CASE WHEN c.production_type=1 AND d.production_type=1 THEN d.production_qnty ELSE 0 END) AS cutting_qnty,
							SUM(CASE WHEN c.production_type=4 AND d.production_type=4 THEN d.production_qnty ELSE 0 END) AS sewing_input,
							SUM(CASE WHEN c.production_type=5 AND d.production_type=5 THEN d.production_qnty ELSE 0 END) AS sewing_output,
							SUM(CASE WHEN c.production_type=8 AND d.production_type=8 THEN d.production_qnty ELSE 0 END) AS packing_finishing,
							SUM(CASE WHEN c.production_type=2 AND d.production_type=2 AND c.embel_name=3 THEN d.production_qnty ELSE 0 END) AS wash_send,
							SUM(CASE WHEN c.production_type=3 AND d.production_type=3 AND c.embel_name=3 THEN d.production_qnty ELSE 0 END) AS wash_received
							FROM pro_garments_production_mst c, pro_garments_production_dtls d
							WHERE     c.id = d.mst_id
							AND c.production_date between '$date_from' and '$date_to' $pro_company_name
							AND c.PRODUCTION_TYPE in (1,2,3,4,5,8)
							AND c.status_active = 1
							AND c.is_deleted = 0
							AND d.status_active = 1
							AND d.is_deleted = 0
							GROUP BY c.production_date, c.serving_company, c.production_type";
		// echo $production_sql;die;

		$result_production_sql = sql_select($production_sql);

		$production_sql_array = array();
		$company_wise_production_sql_array = array();
		$company_wise_production_date_array = array();

		foreach($result_production_sql as $row){
			$production_sql_array[date('d-M-Y',strtotime($row[csf('production_date')]))]['cutting_qnty'] += $row[csf('cutting_qnty')];
			$production_sql_array[date('d-M-Y',strtotime($row[csf('production_date')]))]['sewing_input'] += $row[csf('sewing_input')];
			$production_sql_array[date('d-M-Y',strtotime($row[csf('production_date')]))]['sewing_output'] += $row[csf('sewing_output')];
			$production_sql_array[date('d-M-Y',strtotime($row[csf('production_date')]))]['packing_finishing'] += $row[csf('packing_finishing')];
			$production_sql_array[date('d-M-Y',strtotime($row[csf('production_date')]))]['wash_send'] += $row[csf('wash_send')];
			$production_sql_array[date('d-M-Y',strtotime($row[csf('production_date')]))]['wash_received'] += $row[csf('wash_received')];
			$company_wise_production_sql_array[$row[csf('serving_company')]]['cutting_qnty'] += $row[csf('cutting_qnty')];
			$company_wise_production_sql_array[$row[csf('serving_company')]]['sewing_input'] += $row[csf('sewing_input')];
			$company_wise_production_sql_array[$row[csf('serving_company')]]['sewing_output'] += $row[csf('sewing_output')];
			$company_wise_production_sql_array[$row[csf('serving_company')]]['packing_finishing'] += $row[csf('packing_finishing')];
			$company_wise_production_sql_array[$row[csf('serving_company')]]['wash_send'] += $row[csf('wash_send')];
			$company_wise_production_sql_array[$row[csf('serving_company')]]['wash_received'] += $row[csf('wash_received')];

			$company_wise_production_date_array[$row[csf('serving_company')]][date('d-M-Y',strtotime($row[csf('production_date')]))]['cutting_qnty'] += $row[csf('cutting_qnty')];
			$company_wise_production_date_array[$row[csf('serving_company')]][date('d-M-Y',strtotime($row[csf('production_date')]))]['sewing_input'] += $row[csf('sewing_input')];
			$company_wise_production_date_array[$row[csf('serving_company')]][date('d-M-Y',strtotime($row[csf('production_date')]))]['sewing_output'] += $row[csf('sewing_output')];
			$company_wise_production_date_array[$row[csf('serving_company')]][date('d-M-Y',strtotime($row[csf('production_date')]))]['packing_finishing'] += $row[csf('packing_finishing')];
			$company_wise_production_date_array[$row[csf('serving_company')]][date('d-M-Y',strtotime($row[csf('production_date')]))]['wash_send'] += $row[csf('wash_send')];
			$company_wise_production_date_array[$row[csf('serving_company')]][date('d-M-Y',strtotime($row[csf('production_date')]))]['wash_received'] += $row[csf('wash_received')];

		}
		

		$date_count_array = array();

		foreach($company_wise_production_date_array as $com_id=>$com_data){
			foreach($com_data as $date=>$date_value){
			     if($date_value['cutting_qnty'] != 0 ){
					 $date_count_array[$com_id][1]++ ;
				 }
				 if($date_value['sewing_input'] != 0 ){
					$date_count_array[$com_id][4]++ ;
				 }
			     if($date_value['sewing_output'] != 0 ){
					 $date_count_array[$com_id][5]++ ;
				 }
			     if($date_value['packing_finishing'] != 0 ){
					 $date_count_array[$com_id][8]++ ;
				 }
			     if($date_value['wash_send'] != 0 ){
					 $date_count_array[$com_id][2]++ ;
				 }
			     if($date_value['wash_received'] != 0 ){
					 $date_count_array[$com_id][3]++ ;
				 }
			}
		}

		// echo "<pre>";
		// print_r($date_count_array);

	 ?>
		<table>
			<tr>
				<td valign="top">
					<fieldset style="width:700px">
						<table width="700" cellpadding="0" cellspacing="0"> 
							<tr class="form_caption">
								<td align="center"><p style="font-size:18px; font-weight:bold;"><? echo $companyArr[$company_id]; ?><p></td> 
							</tr>
							<tr class="form_caption">
								<td align="center"><p style="font-size:14px; font-weight:bold;">Monthly Production Reports - Month Of <? echo $month_year_date_from; ?></p></td> 
							</tr>
							<tr class="form_caption">
								<td align="center"><p style="font-size:12px; font-weight:bold;"><? echo "Date: (As On : ".change_date_format( str_replace("'","",trim($txt_date)) ).")"; ?></p></td> 
							</tr>
						</table>
						<br />
						<table id="table_header_1" class="rpt_table" width="720" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr height="50">
									<th width="100">Date</th>
									<th width="100">Cutting</th>
									<th width="100">Input</th>
									<th width="100">Output</th>
									<th width="100">Wash Send</th>
									<th width="100">Wash Rcvd</th>
									<th width="100">Packing and Finishing</th>
								</tr>
							</thead>
						</table>
					<div style="width:720; max-height:400px; overflow-y:scroll" id="scroll_body">
						<table class="rpt_table" width="720" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
							<tbody>
								<?
								$k=0;
								foreach($date_range_arr as $date_data)
								{
									if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
										<td width="100" align="center"><? echo $date_data; ?></td>
										<td width="100" align="right"><? echo number_format($production_sql_array[$date_data]['cutting_qnty'],0); ?></td>
										<td width="100" align="right"><? echo number_format($production_sql_array[$date_data]['sewing_input'] ,0); ?></td>
										<td width="100" align="right"><? echo number_format($production_sql_array[$date_data]['sewing_output'],0); ?></td>
										<td width="100" align="right"><? echo number_format($production_sql_array[$date_data]['wash_send'],0); ?></td>
										<td width="100" align="right"><? echo number_format($production_sql_array[$date_data]['wash_received'],0); ?></td>
										<td width="100" align="right"><? echo number_format($production_sql_array[$date_data]['packing_finishing'],0); ?></td>
									</tr>
								<?
								$k++;
									$total_cutting_qty       += $production_sql_array[$date_data]['cutting_qnty'];
									$total_sewing_in_qty     += $production_sql_array[$date_data]['sewing_input'];
									$total_sewing_out_qty    += $production_sql_array[$date_data]['sewing_output'];
									$total_wash_send_qty     += $production_sql_array[$date_data]['wash_send'];
									$total_wash_received_qty += $production_sql_array[$date_data]['wash_received'];
									$total_packing_qty       += $production_sql_array[$date_data]['packing_finishing'];
								}
								
								?>
								<tr>
									<td width="100" align="center">Total=</td>
									<td width="100" align="right"><? echo number_format($total_cutting_qty,0) ; ?></td>
									<td width="100" align="right"><? echo number_format($total_sewing_in_qty,0) ; ?></td>
									<td width="100" align="right"><? echo number_format($total_sewing_out_qty,0) ; ?></td>
									<td width="100" align="right"><? echo number_format($total_wash_send_qty,0) ; ?></td>
									<td width="100" align="right"><? echo number_format($total_wash_received_qty,0) ; ?></td>
									<td width="100" align="right"><? echo number_format($total_packing_qty,0) ; ?></td>
								</tr> 
							</tbody>                   
						</table>
					</div>
					</fieldset>  
				</td>
				<td valign="top">
					<?
					foreach($company_wise_production_sql_array as $comp_id => $com_value)
					{
						?>

					    <table id="table_header_1" class="rpt_table" width="420" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th colspan="4"><? echo $companyArr[$comp_id]; ?></th>
								</tr>
								<tr height="50">
									<th width="100">Section</th>
									<th width="100">Working Day</th>
									<th width="100">Total Production</th>
									<th width="100">Day Average Production</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td width="100" align="center" valign="top">Cutting</td>
									<td width="100" align="center"><? $working_day=$date_count_array[$comp_id][1]; echo number_format($working_day,0); ?></td>
									<td width="100" align="center"><? echo number_format($com_value['cutting_qnty'] ,0); ?></td>
									<td width="100" align="center"><? echo number_format($day_average_production = $com_value['cutting_qnty']/$working_day,0); ?> </td>
								</tr>
								<tr>
									<td width="100" align="center" valign="top">Input</td>
									<td width="100" align="center"><? $working_day=$date_count_array[$comp_id][4]; echo number_format($working_day,0); ?></td>
									<td width="100" align="center"><? echo number_format($com_value['sewing_input'],0); ?></td>
									<td width="100" align="center"><? echo number_format($day_average_production = $com_value['sewing_input']/$working_day,0); ?> </td>
								</tr>
								<tr>
									<td width="100" align="center" valign="top">Sewing</td>
									<td width="100" align="center"><? $working_day=$date_count_array[$comp_id][5]; echo number_format($working_day,0); ?></td>
									<td width="100" align="center"><? echo number_format($com_value['sewing_output'],0); ?></td>
									<td width="100" align="center"><? echo number_format($day_average_production = $com_value['sewing_output']/$working_day,0); ?> </td>
								</tr>
								<tr>
									<td width="100" align="center" valign="top">Wash Send</td>
									<td width="100" align="center"><? $working_day=$date_count_array[$comp_id][2]; echo number_format($working_day,0); ?></td>
									<td width="100" align="center"><? echo number_format($com_value['wash_send'],0); ?></td>
									<td width="100" align="center"><? echo number_format($day_average_production = $com_value['wash_send']/$working_day,0); ?> </td>
								</tr>
								<tr>
									<td width="100" align="center" valign="top">Wash Rcvd</td>
									<td width="100" align="center"><? $working_day=$date_count_array[$comp_id][3]; echo number_format($working_day,0); ?></td>
									<td width="100" align="center"><? echo number_format($com_value['wash_received'],0); ?></td>
									<td width="100" align="center"><? echo number_format($day_average_production = $com_value['wash_received']/$working_day,0); ?> </td>
								</tr>
								<tr>
									<td width="100" align="center" valign="top">Packing and Finishing</td>
									<td width="100" align="center"><? $working_day=$date_count_array[$comp_id][8]; echo number_format($working_day,0); ?></td>
									<td width="100" align="center"><? echo number_format($com_value['packing_finishing'],0); ?></td>
									<td width="100" align="center"><? echo number_format($day_average_production = $com_value['packing_finishing']/$working_day,0); ?> </td>
								</tr>
							</tbody> 
						</table>
						<br>
						<?

					}
					 ?>
				</td>
			</tr>
		<table>
	 <?
	}
	else if ($type == 4) // Monthly Production Summary
	{
		$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
		$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
		$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
		// ================================= GETTING FORM DATA ====================================
		$company_id = str_replace("'","",$cbo_company_id);

		// ================================= MAKE QUERY CONDITION ====================================
		if($company_id == "" || $company_id == 0) $pro_company_name=""; else $pro_company_name="and c.serving_company in($company_id)";

		$date_from = str_replace("'", "", $txt_date);
		$month_year_date_from = date('M-Y',strtotime($date_from));
		$date_from = '01-'.$month_year_date_from;

		$date_to = str_replace("'", "", $txt_date);

		function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y' ) 
		{
			$dates = array();
			$current = strtotime($first);
			$last = strtotime($last);

			while( $current <= $last ) {

				$dates[] = date($output_format, $current);
				$current = strtotime($step, $current);
			}

			return $dates;
		}
		$date_range_arr = get_date_range($date_from,$date_to); 

		$date_width = count($date_range_arr)*100;
		$width = (1050 + $date_width)."px";
		// echo $width;die;
		// echo "<pre>";
		// print_r($date_range_arr);

		// =========================================== Production Information SQL ================================================================
        $lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		// echo"<pre>";
		// print_r($lineSerialArr);die;

		$production_sql = " SELECT c.production_date, c.serving_company, c.production_type, c.sewing_line, c.prod_reso_allo, a.buyer_name, a.style_ref_no, a.set_smv, a.avg_unit_price, e.cm_cost,
							SUM(CASE WHEN c.production_type=5 AND d.production_type=5 THEN d.production_qnty ELSE 0 END) AS sewing_output
							FROM wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d, wo_pre_cost_dtls e
							WHERE     
							    a.id = b.job_id 
                            AND b.id = c.po_break_down_id
							AND c.id = d.mst_id
							AND a.id = e.job_id
							AND c.production_date between '$date_from' and '$date_to' $pro_company_name
							AND c.PRODUCTION_TYPE in (5)
							AND a.status_active = 1
							AND a.is_deleted = 0
							AND b.status_active = 1
							AND b.is_deleted = 0
							AND c.status_active = 1
							AND c.is_deleted = 0
							AND d.status_active = 1
							AND d.is_deleted = 0
							AND e.status_active = 1
							AND e.is_deleted = 0
							GROUP BY c.production_date, c.serving_company, c.production_type, c.sewing_line, c.prod_reso_allo , a.buyer_name, a.style_ref_no, a.set_smv,a.avg_unit_price,e.cm_cost";
        // echo $production_sql;
		$result_production_sql = sql_select($production_sql);

		
		$line_style_date_wise_production_sql_array = array();
		$line_style_wise_production_sql_array = array();
		$line_sql_array = array();
		$date_wise_production_sql_array = array();
		$company_wise_production_sql_array = array();

		foreach($result_production_sql as $row){

			if($row[csf('prod_reso_allo')]==1)
			{
				$sewing_line_ids=$prod_reso_line_arr[$row[csf('sewing_line')]];
				$sl_ids_arr = explode(",", $sewing_line_ids);
				$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take
			}
			else
			{
				$sewing_line_id=$row[csf('sewing_line')];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			// echo $sewing_line_id."**".$lineSerialArr[$sewing_line_id]."**".$slNo."<br>";

				$line_style_date_wise_production_sql_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]][date('d-M-Y',strtotime($row[csf('production_date')]))]['sewing_output'] += $row[csf('sewing_output')];

				$line_style_wise_production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$line_style_wise_production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['buyer_name'] = $row[csf('buyer_name')];
				$line_style_wise_production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['set_smv'] = $row[csf('set_smv')];
				$line_style_wise_production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['sewing_output'] += $row[csf('sewing_output')];
				$line_style_wise_production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['cm_cost'] = $row[csf('cm_cost')];
				$line_style_wise_production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['avg_unit_price'] = $row[csf('avg_unit_price')];

				$line_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]]['sewing_line'] = $row[csf('sewing_line')];
				$line_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]]['sewing_output'] += $row[csf('sewing_output')];
				$line_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]]['prod_reso_allo'] = $row[csf('prod_reso_allo')];

				$company_wise_production_sql_array[$row[csf('serving_company')]]['serving_company'] = $row[csf('serving_company')];

				$date_wise_production_sql_array[date('d-M-Y',strtotime($row[csf('production_date')]))]['sewing_output'] += $row[csf('sewing_output')];

		}
		// echo "<pre>";
		// print_r($line_style_wise_production_sql_array);

		$line_rowspan_array =array();
		$style_count_array =array();
			foreach($line_style_date_wise_production_sql_array as $comp_id => $com_value)
			{
				ksort($com_value);
				foreach($com_value as $serial_key=>$serial_value)
				{
					foreach($serial_value as $line_id => $line_value)
					{
						foreach($line_value as $style => $style_value)
						{
						$line_rowspan_array[$comp_id][$line_id]++;
						$style_count_array[$comp_id][$line_id][$style]++;
						}
					}
				}	
			}
			// echo "<pre>";
		    // print_r($style_count_array);
			$style_change_array =array();
			foreach($line_rowspan_array as $com => $line)
			{
				foreach($line as $line_key => $line_val)
				{
					if($line_val > 1){
					$style_change_array[$com] += $line_val -1;
					}
				}

			}
			// echo "<pre>";
		    // print_r($style_change_array);

	 ?> 
		<fieldset style="width:<? echo $width ?>">
			<table width="<? echo $width ?>" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td align="center"><p style="font-size:25px; font-weight:bold;"><? echo $companyArr[$company_id]; ?><p></td> 
				</tr>
				<tr class="form_caption">
					<td align="center"><p style="font-size:21px; font-weight:bold;">Monthly Line & Style wise Production Summary</p></td> 
				</tr>
				<tr class="form_caption">
					<td align="center"><p style="font-size:18px; font-weight:bold;"><? echo "Month of ". $month_year_date_from ; ?></p></td> 
				</tr>
			</table>
			<br />
			<table width="500" class="rpt_table"  cellpadding="0" cellspacing="0" border="1">
			        <? 
					 foreach($line_style_date_wise_production_sql_array as $comp_id => $com_value)
						{
							?>
								<tr>
									<td width="100" align="center" style="color:red; font-weight: bold;"> No of Style Change =</td>
									<td width="200" align="center" style="font-weight: bold;"><? echo $companyArr[$comp_id]; ?></td>
									<td width="100" align="center" style="color:red; font-weight: bold;"><? echo number_format($style_change_array[$comp_id],0); $style_change_total += $style_change_array[$comp_id];?></td>
								</tr>	
					        <?
						}
					?>
						        <tr>
									<td width="100" align="center"></td>
									<td width="200" align="center" style="color:red; font-weight: bold;">Total</td>
									<td width="100" align="center" style="color:red; font-weight: bold;"><? echo number_format($style_change_total,0);?></td>
					            </tr>
			</table>
			<table id="table_header_1" class="rpt_table" width="<? echo $width ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr height="50">
						<th width="100">Factory</th>
						<th width="100">Line</th>
						<th width="100">Buyer</th>
						<th width="100">STYLE</th>
						<th width="40">SMV</th>
						    <? 
								foreach($date_range_arr as $date_value){
									?>
									<th width="70"><? echo $date_value ; ?></th>
									<?
								}
							?>
						<th width="100">Total</th>
						<th width="100">Line Total</th>
						<th width="100">CM Per Pcs</th>
						<th width="100">Total CM Earn </th>
						<th width="100">FOB </th>
						<th width="100">Total FOB Value</th>
					</tr>
				</thead>
			</table>
				<div style="width:<? echo $width-20 ; ?>; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="<? echo $width ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
							<tbody>
							<?
								$k=0;
								// echo"<pre>";
								// print_r($line_style_date_wise_production_sql_array);die;
								foreach($line_style_date_wise_production_sql_array as $comp_id => $com_value)
								{
									ksort($com_value);
									$count_cm_per_pcs = 0;
									$count_fob = 0;
									foreach($com_value as $serial_key=>$serial_value)
									{
										foreach($serial_value as $line_id => $line_value)
										{
											$l =0;
											foreach($line_value as $style => $style_value)
											{
												$style_count[$comp_id] += $style_count_array[$comp_id][$line_id][$style];

												if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
													<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
														<td width="100" align="center"><? echo $companyArr[$comp_id]; ?></td>
														<td width="100" align="center">
															<? 
																if($line_sql_array[$comp_id][$line_id]['prod_reso_allo'] == 1)
																{
																	$sewing_line=$prod_reso_line_arr[$line_id];
																	$sewing_line_arr=explode(",",$sewing_line);
																	$sewing_line_name="";
																	foreach($sewing_line_arr as $sewing_line_id)
																	{
																		$sewing_line_name.=$lineArr[$sewing_line_id].",";
																	}
																	$sewing_line_name=chop($sewing_line_name,",");
																	echo $sewing_line_name;
																}
																else
																{
																	echo $lineArr[$sewing_line_id];
																}
																// echo $lineArr[$line_id];
															?>
														</td>
														<td width="100" align="center"><? echo $buyer_library[$line_style_wise_production_sql_array[$comp_id][$line_id][$style]['buyer_name']]; ?></td>
														<td width="100" align="center"><? echo $line_style_wise_production_sql_array[$comp_id][$line_id][$style]['style_ref_no']; ?></td>
														<td width="40" align="right">
														<? 
														echo number_format($line_style_wise_production_sql_array[$comp_id][$line_id][$style]['set_smv'],2); 
														$total_smv[$comp_id] += $line_style_wise_production_sql_array[$comp_id][$line_id][$style]['set_smv']; 
														?>
														</td>
														
															<? 
																foreach($date_range_arr as $date_value){
																	?>
																	
																	<td width="70" align="right"> 
																		<? echo number_format($line_style_date_wise_production_sql_array[$comp_id][$serial_key][$line_id][$style][$date_value]['sewing_output'],0) ; 

																		$total_com_date_wise[$comp_id][$date_value]  += $line_style_date_wise_production_sql_array[$comp_id][$serial_key][$line_id][$style][$date_value]['sewing_output'] ;
																		
																	?> 
																	</td>
																	<?
																}
															?>
														
														<td width="100" align="right"><? echo $total = $line_style_wise_production_sql_array[$comp_id][$line_id][$style] ['sewing_output']; 
															$total_com_wise[$comp_id]  += $line_style_wise_production_sql_array[$comp_id][$line_id][$style]['sewing_output'];
															?>
														</td>
															<?
															if ($l==0)
															{ 
																?>
																
																	<td width="100" align="right" valign="middle" rowspan="<? echo $line_rowspan_array[$comp_id][$line_id]; ?>">
																		<?
																		echo number_format($line_sql_array[$comp_id][$line_id]['sewing_output']);
																		$total_line[$comp_id] += $line_sql_array[$comp_id][$line_id]['sewing_output'];
																		?>
																	</td>
																<?
																$l++;
															}
															?>
													
														
														<td width="100" align="right">
															<? 
															$cm_per_pcs = ($line_style_wise_production_sql_array[$comp_id][$line_id][$style]['cm_cost']/12);
															echo "$ ". number_format($cm_per_pcs,2);

															if($cm_per_pcs>0){
																$count_cm_per_pcs++;
															}
															// echo $count_cm_per_pcs; 
															// $total_cm_per_pcs[$comp_id] += $line_style_wise_production_sql_array[$comp_id][$line_id][$style]['cm_cost'];
															$total_cm_per_pcs[$comp_id] += $line_style_wise_production_sql_array[$comp_id][$line_id][$style]['cm_cost']/12;
															?>
														</td>
														<td width="100" align="right">
															<? 
																$cm_per_pcs_data = number_format($cm_per_pcs,2);
																echo "$ ". number_format($total_cm_earn =  $total * $cm_per_pcs_data,2);
																$tot_total_cm_earn[$comp_id] +=  $total_cm_earn; 
															?>
														</td>
														<td width="100" align="right">
															<?
															$fob = $line_style_wise_production_sql_array[$comp_id][$line_id][$style]['avg_unit_price'];
															echo "$ ". number_format($fob,2);
															$total_fob_value[$comp_id] += number_format($fob,2);
															if($fob>0){
																$count_fob++;
															}
															// echo $count_fob;
															?>
														</td>
														<td width="100" align="right">
															<?
															    $fob_val = number_format($fob,2);
																$total_fob = $fob_val*$total;
																$com_total_fob[$comp_id] += $total_fob;
																echo "$ ". number_format($total_fob,2);
															?>
														</td>
													</tr>
														
												<?
												$k++;

											}
										}
									}	
											?>
												<tr style="background: #8DAFDA;">
													<td colspan="4" align="center" style="font-weight: bold;"><? echo $companyArr[$comp_id]; ?> total =</td>
													<td width="40" align="right" style="font-weight: bold;">
													    <?   
															$avg_total_smv = $total_smv[$comp_id]/$style_count[$comp_id];   
															echo number_format($avg_total_smv,2);   
															$grand_smv_total += $avg_total_smv; 
														?>
													</td>
														<? 
															foreach($date_range_arr as $date_value){
																?>
																<td width="70" align="right" style="font-weight: bold;"><? echo number_format($total_com_date_wise[$comp_id][$date_value],0) ; ?></td>
																<?
															}
														?>
													<td width="100" align="right" style="font-weight: bold;">
																<? echo number_format($total_com_wise[$comp_id],0); ?>
													</td>
													<td width="100"align="right" style="font-weight: bold;">
																<? 
																echo number_format($total_line[$comp_id]);   
																$grand_line_total += $total_line[$comp_id]; 
																?>
													</td>
													<td width="100"align="right" style="font-weight: bold;">
																<? 
																$total_avg_cm_per_pcs = $total_cm_per_pcs[$comp_id]/$count_cm_per_pcs; 
																echo "$ ". number_format($total_avg_cm_per_pcs,2); 

																if($total_avg_cm_per_pcs>0){
																	$count_total_avg_cm_per_pcs++;
																}
																// echo $count_total_avg_cm_per_pcs;

																$grand_total_cm_per_pcs += $total_avg_cm_per_pcs; 
																?>
													</td>
													<td width="100"align="right" style="font-weight: bold;">
																<? 
																echo "$ ". number_format($tot_total_cm_earn[$comp_id],2); 
																$grand_tot_total_cm_earn += $tot_total_cm_earn[$comp_id]; 
																?>
													</td>
													<td width="100"align="right" style="font-weight: bold;">
														<?
															$tot_total_fob_value = number_format($total_fob_value[$comp_id],2)/$count_fob;
															echo "$ ". number_format($tot_total_fob_value,2); 

															if($tot_total_fob_value>0){
																$count_total_avg_fob++;
															}
															// echo $count_total_avg_fob;

															$grand_tot_total_fob_value+= $tot_total_fob_value; 
														?>
													</td>
													<td width="100"align="right" style="font-weight: bold;">
													    <? 
															echo "$ ". number_format($com_total_fob[$comp_id],2); 
															$grand_total_fob += $com_total_fob[$comp_id]; 
														?>
													</td>
												</tr> 
												
											<?
								}
							?>
							</tbody> 
							<tfoot>
								<tr style="background: gray;">
									<td colspan="4" align="center" style="font-weight: bold;"> Grand total =</td>
									<td width="40" align="right" style="font-weight: bold;">
										<? 
										   $com_count = count($company_wise_production_sql_array);
										   echo number_format($grand_smv_total/$com_count,2); 
										?>
								    </td>
										<? 
											foreach($date_range_arr as $date_value){
												?>
												<td width="70" align="right" style="font-weight: bold;">
													<? 
														echo $date_wise_production_sql_array[$date_value]['sewing_output']; 
														$grand_total += $date_wise_production_sql_array[$date_value]['sewing_output']; 
													?>
												</td>
												<?
											}
										?>
									<td width="100" align="right" style="font-weight: bold;"><? echo number_format($grand_total,0); ?></td>
									<td width="100" align="right" style="font-weight: bold;"><? echo number_format($grand_line_total); ?></td>
									<td width="100" align="right" style="font-weight: bold;">
									<? 
									   $grand_avg_total_cm_per_pcs = $grand_total_cm_per_pcs/$count_total_avg_cm_per_pcs; 
									   echo "$ ". number_format($grand_avg_total_cm_per_pcs,2); 
									?>
								    </td>
									<td width="100" align="right" style="font-weight: bold;"><? echo "$ ". number_format($grand_tot_total_cm_earn,2); ?></td>
									<td width="100" align="right" style="font-weight: bold;">
									<? 
									   $grand_avg_total_fob_value = $grand_tot_total_fob_value/$count_total_avg_fob; 
									   echo "$ ". number_format($grand_avg_total_fob_value,2); 
									?>
								    </td>
									<td width="100" align="right" style="font-weight: bold;"><? echo "$ ". number_format($grand_total_fob,2); ?></td>
								</tr> 
						    </tfoot>                  
					</table>
				</div>             
		</fieldset>   
	 <?
	}
	else if ($type == 5) // Target vs Achievement
	{
		$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
		$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
		$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
		// ================================= GETTING FORM DATA ====================================
		$company_id = str_replace("'","",$cbo_company_id);

		// ================================= MAKE QUERY CONDITION ====================================
		if($company_id == "" || $company_id == 0) $pro_resource_company_name=""; else $pro_resource_company_name="and a.company_id in($company_id)";
		// if($company_id == "" || $company_id == 0) $pro_company_name=""; else $pro_company_name="and a.company_id in($company_id)";

		$date_from = str_replace("'", "", $txt_date);
		$month_year_date_from = date('M-Y',strtotime($date_from));
		$date_from = '01-'.$month_year_date_from;

		$date_to = str_replace("'", "", $txt_date);

		function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y' ) 
		{
			$dates = array();
			$current = strtotime($first);
			$last = strtotime($last);

			while( $current <= $last ) {

				$dates[] = date($output_format, $current);
				$current = strtotime($step, $current);
			}

			return $dates;
		}
		$date_range_arr = get_date_range($date_from,$date_to); 

		$date_width = count($date_range_arr)*70;
		$width = (600 + $date_width)."px";
        
		//  ============================================= Prod. Resource Sql Query ================================ 
        $lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}

		$prod_resource_sql ="SELECT a.id,a.company_id,a.location_id,a.floor_id,a.line_number,c.target_per_hour,c.working_hour,c.pr_date
        FROM prod_resource_mst a, prod_resource_dtls_mast b, prod_resource_dtls c
        WHERE     a.id = b.mst_id
		AND a.id = c.mst_id
		AND b.id = c.mast_dtl_id
		AND c.pr_date BETWEEN '$date_from' and '$date_to' $pro_resource_company_name
		AND a.is_deleted = 0
		AND b.is_deleted = 0
		AND c.is_deleted = 0
        ORDER BY a.company_id, a.line_number ASC";
		// echo $prod_resource_sql;
		$prod_resource_sql_result = sql_select($prod_resource_sql);
        
		$prod_resource_sql_array = array();
		$company_wise_prod_resource_sql_array = array();
		$company_line_date_wise_prod_resource_sql_array = array();
		foreach($prod_resource_sql_result as $row){

				$sewing_line_ids=$row[csf('line_number')];
				$sl_ids_arr = explode(",", $sewing_line_ids);
				$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			// echo $sewing_line_id."**".$lineSerialArr[$sewing_line_id]."**".$slNo."<br>";

			$prod_resource_sql_array[$row[csf('company_id')]][$slNo][$row[csf('id')]][date('d-M-Y',strtotime($row[csf('pr_date')]))]['target_per_hour'] = $row[csf('target_per_hour')];
			$prod_resource_sql_array[$row[csf('company_id')]][$slNo][$row[csf('id')]][date('d-M-Y',strtotime($row[csf('pr_date')]))]['working_hour'] = $row[csf('working_hour')];

			$company_wise_prod_resource_sql_array[$row[csf('company_id')]][$slNo][$row[csf('id')]]['target_per_hour'] = $row[csf('company_id')];

			$company_line_date_wise_prod_resource_sql_array[$row[csf('company_id')]][$row[csf('id')]][date('d-M-Y',strtotime($row[csf('pr_date')]))]['target_per_hour'] = $row[csf('target_per_hour')];
			$company_line_date_wise_prod_resource_sql_array[$row[csf('company_id')]][$row[csf('id')]][date('d-M-Y',strtotime($row[csf('pr_date')]))]['working_hour'] = $row[csf('working_hour')];

		}
		// echo "<pre>";
		// print_r($prod_resource_sql_array);

		//  ============================================= Production Sql Query ================================ 
        $production_sql = " SELECT d.production_date, d.serving_company, d.production_type, d.sewing_line, d.prod_reso_allo,
		    SUM(CASE WHEN d.production_type=5 THEN d.production_quantity ELSE 0 END) AS sewing_output
		    FROM pro_garments_production_mst d
		    WHERE     
			d.production_date  BETWEEN '$date_from' and '$date_to'
		    AND d.PRODUCTION_TYPE in (5)
		    AND d.status_active = 1
		    AND d.is_deleted = 0
		    GROUP BY d.production_date, d.serving_company, d.production_type, d.sewing_line, d.prod_reso_allo";
        //    echo $production_sql;
		$result_production_sql = sql_select($production_sql);

		$date_wise_production_sql_array = array();
		$company_line_wise_production_sql_array = array();
		$company_line_date_wise_production_sql_array = array();
		
		foreach($result_production_sql as $row)
		{
            if($row[csf("prod_reso_allo")]==1)
			{
				// echo $row[csf('sewing_line')]."**";//die;
				$line_resource_mst_arr=explode(",",$row[csf('sewing_line')]);
				// $line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
				// echo "<pre>";
				// print_r($line_resource_mst_arr);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$resource_id.", ";
				}
				$line_name=chop($line_name," , ");
				// echo $line_name;//die;

				$date_wise_production_sql_array[$line_name][date('d-M-Y',strtotime($row[csf('production_date')]))]['sewing_output'] += $row[csf('sewing_output')];

				$company_line_wise_production_sql_array[$row[csf('serving_company')]][$line_name]['sewing_output'] += $row[csf('sewing_output')];

				$company_line_date_wise_production_sql_array[$row[csf('serving_company')]][$line_name][date('d-M-Y',strtotime($row[csf('production_date')]))]['sewing_output'] += $row[csf('sewing_output')];
			}
			else
			{
				$date_wise_production_sql_array[$line_name][date('d-M-Y',strtotime($row[csf('production_date')]))]['sewing_output'] += $row[csf('sewing_output')];

				$company_line_wise_production_sql_array[$row[csf('serving_company')]][$line_name]['sewing_output'] += $row[csf('sewing_output')];

				$company_line_date_wise_production_sql_array[$row[csf('serving_company')]][$line_name][date('d-M-Y',strtotime($row[csf('production_date')]))]['sewing_output'] += $row[csf('sewing_output')];
			}
		}
		// echo "<pre>";
		// print_r($date_wise_production_sql_array);
		
        

	 ?>

		<fieldset style="<? echo $width; ?>">
			<table width="<? echo $width; ?>" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td align="center"><p style="font-size:25px; font-weight:bold;"><? echo $companyArr[$company_id]; ?><p></td> 
				</tr>
				<tr class="form_caption">
					<td align="center"><p style="font-size:21px; font-weight:bold;">Monthly Line wise Target vs Achievement</p></td> 
				</tr>
				<tr class="form_caption">
					<td align="center"><p style="font-size:18px; font-weight:bold;"><? echo "Month of ". $month_year_date_from ; ?></p></td> 
				</tr>
			</table>
			<br />
			<table id="table_header_1" class="rpt_table" width="<? echo $width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr height="50">
						<th width="100" style="font-weight:bold;">LINE</th>
						<th width="100" style="font-weight:bold;">Date</th>
						    <? 
								foreach($date_range_arr as $date_value){
									?>
									<th width="70"><? echo $date_value ; ?></th>
									<?
								}
							?>
						<th width="100" style="font-weight:bold;">TTL-Target</th>
						<th width="100" style="font-weight:bold;">TTL-Achvd</th>
						<th width="100" style="font-weight:bold;">Deviation</th>
						<th width="100" style="font-weight:bold;"><b>Achvd %</b></th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $width-20 ?>; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="<? echo $width ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?
						$k=0;
						foreach($prod_resource_sql_array as $company_key=>$company_value)
						{
							ksort($company_value);
							foreach($company_value as $serial_key=>$serial_value)
						    {
								foreach($serial_value as $line_id=>$line_value)
								{
									$total_target     = 0;
									$total_achvd      = 0;
									$total_deviation  = 0;
									$total_achvd_per  = 0;
									if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
										<td width="100" rowspan="4" title="<? echo $line_id; ?>" valign="middle" style="font-weight:bold;">
										   <? 
												$sewing_line_arr=explode(",",$prod_reso_line_arr[$line_id]);
												// echo "<pre>";
												// print_r($sewing_line_arr);
												$sewing_line_name="";
												foreach($sewing_line_arr as $sewing_line_id)
												{
													$sewing_line_name.=$lineArr[$sewing_line_id].",";
												}
												$sewing_line_name=chop($sewing_line_name,",");
												echo $sewing_line_name;
										   ?>
									    </td>
										<td width="100" style="font-weight:bold;">Target</td>
											<? 
												foreach($date_range_arr as $date_value){
													?>
													<td width="70" align="right" style="font-weight:bold;">
														<? 
														$target = $prod_resource_sql_array[$company_key][$serial_key][$line_id][$date_value]['target_per_hour'] * $prod_resource_sql_array[$company_key][$serial_key][$line_id][$date_value]['working_hour']; 
														$total_target += $target; 
														echo number_format($target,0); 
														// achvd value
														$achvd = $date_wise_production_sql_array[$line_id][$date_value]['sewing_output'];
														$total_achvd += $achvd;
														?>
													</td>
													<?
												}
											?>
										<td width="100" align="right" rowspan="4" valign="middle" style="font-weight:bold;"> 
											<? echo number_format($total_target,0); ?> 
										</td>
										<td width="100" align="right" rowspan="4" valign="middle" style="font-weight:bold;">
											<? 
												echo number_format($total_achvd,0); 
											?>
										</td>
										<td width="100" align="right" rowspan="4" valign="middle" style="font-weight:bold;">
											<? 
												$total_deviation = $total_achvd - $total_target;
												if ($total_deviation<0){
													?>
													<span style="color:red;"><?echo number_format($total_deviation,0);?><span>
													<?
												}else{
													echo number_format($total_deviation,0);
												}
													
											?>
										</td>
										<td width="100" align="right" rowspan="4" valign="middle" style="font-weight:bold;">
											<? 
												$total_achvd_per = ($total_achvd/$total_target)*100; 
												echo number_format($total_achvd_per,2)."%"; 
											?>
										</td>
									</tr>
									<tr>
										<td width="100" style="font-weight:bold;">Achvd</td>
											<? 
												foreach($date_range_arr as $date_value){
													?>
													<td width="70" align="right"><?  
														$achvd = $date_wise_production_sql_array[$line_id][$date_value]['sewing_output']; 
														echo number_format($achvd,0); ?>
													</td>
													<?
												}
											?>
									</tr>
									<tr>
										<td width="100" style="font-weight:bold;">Deviation</td>
											<? 
												foreach($date_range_arr as $date_value){
													?>
													<td width="70" align="right" style="color:red;"><? 
														$achvd = $date_wise_production_sql_array[$line_id][$date_value]['sewing_output']; 
														$target = $prod_resource_sql_array[$company_key][$serial_key][$line_id][$date_value]['target_per_hour'] * $prod_resource_sql_array[$company_key][$serial_key][$line_id][$date_value]['working_hour']; 
														$deviation = $achvd - $target;  
														echo number_format($deviation,0); ?>
													</td>
													<?
												}
											?>
									</tr>
									<tr>
										<td width="100" style="font-weight:bold;">Achvd %</td>
											<? 
												foreach($date_range_arr as $date_value){
													?>
													<td width="70" align="right"><? 
														$achvd = $date_wise_production_sql_array[$line_id][$date_value]['sewing_output']; 
														$target = $prod_resource_sql_array[$company_key][$serial_key][$line_id][$date_value]['target_per_hour'] * $prod_resource_sql_array[$company_key][$serial_key][$line_id][$date_value]['working_hour'];
														$achvd_per = ($achvd/$target)*100; 
														echo number_format($achvd_per,2)."%"; ?>
													</td>
													<?
												}
											?>
									</tr>
									<?
									$k++;
								}
							}	
						}	
						?>
					</tbody>                   
				</table>
				<br>
				<table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<thead>
							<tr style="background: #CDD0D4;">
								<td colspan="6" align="center" style="font-weight: bold;">Monthly Production Summary</td>
							</tr>
							<tr height="50">
								<th width="100">Factory</th>
								<th width="100">Line</th>
								<th width="100">Target</th>
								<th width="100">Achvd</th>
								<th width="100">Deviation</th>
								<th width="100">Achvd %</th>
							</tr>
						</thead>
						<tbody>
							<?
							    $grand_total_monthly_target    = 0;
							    $grand_total_monthly_achvd     = 0;
							    $grand_total_monthly_deviation = 0;
							    $grand_total_monthly_deviation = 0;
							foreach( $company_wise_prod_resource_sql_array as $comp_id => $company_value)
							{ 
								$total_monthly_target      = 0;
								$total_monthly_achvd       = 0;
								$total_monthly_deviation   = 0;
								$total_monthly_achvd_per   = 0;

								$count_monthly_achvd_per   = 0;
                                
								ksort($company_value);
								foreach($company_value as $serial_key=>$serial_value)
								{
									foreach( $serial_value as $line_key => $line_data)
									{ 
										$monthly_target = 0;
										$monthly_achvd  = 0;

									 ?>
										<tr>
											<td width="100" align="center"><? echo $companyArr[$comp_id]; ?></td>
											<td width="100" align="center">
												<? 
													// $sewing_line_arr=explode(",",$line_key);
													$sewing_line_arr=explode(",",$prod_reso_line_arr[$line_key]);
													$sewing_line_name="";
													foreach($sewing_line_arr as $sewing_line_id)
													{
														$sewing_line_name.=$lineArr[$sewing_line_id].",";
													}
													$sewing_line_name=chop($sewing_line_name,",");
													echo $sewing_line_name;		
												?>
											</td>
												<? 
													foreach($date_range_arr as $date_value)
													{
														$month_wise_target = $company_line_date_wise_prod_resource_sql_array[$comp_id][$line_key][$date_value]['target_per_hour'] * $company_line_date_wise_prod_resource_sql_array[$comp_id][$line_key][$date_value]['working_hour'];
														$monthly_target += $month_wise_target;

														// achvd value
														$month_wise_achvd = $company_line_date_wise_production_sql_array[$comp_id][$line_key][$date_value]['sewing_output'];
														$monthly_achvd += $month_wise_achvd;
													}
												?>
											<td width="100" align="right">
												<? 
													echo number_format($monthly_target,0); 
													$total_monthly_target += $monthly_target;
												?>
											</td>
											<td width="100" align="right">
												<? 
													echo number_format($monthly_achvd,0);
													$total_monthly_achvd += $monthly_achvd; 
												?>
											</td>
											<td width="100" align="right" style="color:red;">
												<?
													$monthly_deviation = $monthly_achvd - $monthly_target;  
													echo number_format($monthly_deviation,0); 

													$total_monthly_deviation += $monthly_deviation; 
												?>
											</td>
											<td width="100" align="right">
												<?
													$monthly_achvd_per = ($monthly_achvd/$monthly_target)*100; 
													echo number_format($monthly_achvd_per,2)."%";
													if($monthly_achvd_per>0){
                                                       $count_monthly_achvd_per++;
													}
													// echo $count_monthly_achvd_per;

													$total_monthly_achvd_per += $monthly_achvd_per;
												?>
											</td>
										</tr>
									 <?
									}
								}		
									 ?>
									
									<tr style="background: #8DAFDA;">
										<td colspan="2" align="center" style="font-weight: bold;"><? echo $companyArr[$comp_id]; ?> Total =</td>
										<td width="100" align="right" style="font-weight: bold;">
										    <? 
										        echo number_format($total_monthly_target,2); 
										        $grand_total_monthly_target += $total_monthly_target; 
										    ?>
									    </td>
										<td width="100" align="right" style="font-weight: bold;">
											<? 
											    echo number_format($total_monthly_achvd,2); 
												$grand_total_monthly_achvd += $total_monthly_achvd; 
											?>
										</td>	
										<td width="100" align="right" style="font-weight: bold; color:red;">
											<? 
											    echo number_format($total_monthly_deviation,2); 
												$grand_total_monthly_deviation += $total_monthly_deviation; 
											?>
										</td>
										<td width="100"align="right" style="font-weight: bold;">
										    <? 
											    $total_avg_monthly_achvd_per = $total_monthly_achvd_per/$count_monthly_achvd_per; 
											    echo number_format($total_avg_monthly_achvd_per,2)."%"; 
												if($total_avg_monthly_achvd_per>0){
                                                   $count_total_avg_monthly_achvd_per++;
												}
												// echo $count_total_avg_monthly_achvd_per;
												$grand_total_monthly_achvd_per += number_format($total_avg_monthly_achvd_per,2);
											?>
										</td>
									</tr> 
									<?
							}
							?>
						</tbody> 
						<tfoot>
							<tr style="background: gray;">
								<td colspan="2" align="center" style="font-weight: bold;"> Grand Total =</td>
								<td width="100" align="right" style="font-weight: bold;"><? echo number_format($grand_total_monthly_target,2); ?></td>
								<td width="100" align="right" style="font-weight: bold;">
								<? echo number_format($grand_total_monthly_achvd,2); ?>
								</td>
								<td width="100" align="right" style="font-weight: bold; color:red;"><? echo number_format($grand_total_monthly_deviation,2); ?></td>
								<td width="100" align="right" style="font-weight: bold;">
								<? 
								$grand_total_avg_monthly_achvd_per=$grand_total_monthly_achvd_per/$count_total_avg_monthly_achvd_per; 
								echo number_format($grand_total_avg_monthly_achvd_per,2)."%"; 
								?>
								</td>
							</tr> 
						</tfoot>                  
			    </table>
			</div>
		</fieldset>  
	 <?
	}
    else if ($type == 6) // Sewing WIP 
	{
		$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
		$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
		$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		// ================================= GETTING FORM DATA ====================================
		$company_id = str_replace("'","",$cbo_company_id);

		// ================================= MAKE QUERY CONDITION ====================================
		if($company_id == "" || $company_id == 0) $company_name=""; else $company_name="and d.serving_company in($company_id)";

		$date = str_replace("'", "", $txt_date);
        // ================================= For Date Range ====================================
		$date_from = str_replace("'", "", $txt_date);
		$month_year_date_from = date('M-Y',strtotime($date_from));
		$date_from = '01-'.$month_year_date_from;

		$date_to = str_replace("'", "", $txt_date);

		function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y' ) 
		{
			$dates = array();
			$current = strtotime($first);
			$last = strtotime($last);

			while( $current <= $last ) {

				$dates[] = date($output_format, $current);
				$current = strtotime($step, $current);
			}

			return $dates;
		}
		$date_range_arr = get_date_range($date_from,$date_to); 
		// echo "<pre>";
		// print_r($date_range_arr);

        // ================================= Main Query ===============================================
        $lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}

		$sql= "SELECT d.sewing_line,
		a.buyer_name,
		a.style_ref_no,
		a.job_no,
		b.po_number,
		b.id as po_id,
		c.color_number_id,
		c.size_number_id,
		b.po_quantity,
		c.order_quantity,
		d.serving_company,
		d.production_date,
		TO_CHAR (d.production_hour, 'hh24:mi') as production_hour,
		d.prod_reso_allo,
		min(case when d.production_type=4 then d.production_date end) as input_first_date,
		min(case when d.production_type=5 then d.production_date end) as output_first_date,
		max(case when d.production_type=5 then d.production_date end) as output_last_date,
	    sum (case when d.production_type=4 and e.production_type=4 and d.production_date = '$date' then e.production_qnty else 0 end ) as today_sewing_input,
	    sum (case when d.production_type=4 and e.production_type=4 and d.production_date <= '$date' then e.production_qnty else 0 end ) as total_sewing_input,
	    sum (case when d.production_type=5 and e.production_type=5 and d.production_date = '$date' then e.production_qnty else 0 end ) as today_sewing_output,
	    sum (case when d.production_type=5 and e.production_type=5 and d.production_date <= '$date' then e.production_qnty else 0 end ) as total_sewing_output,
		sum(case when to_char(d.production_hour, 'hh24:mi') >= '08:00' and to_char(d.production_hour, 'hh24:mi') <= '17:00' then e.production_qnty else 0 end ) as normal_hour_production_qty,
		sum(case when to_char(d.production_hour, 'hh24:mi') >= '17:01' and to_char(d.production_hour, 'hh24:mi') <= '24:00' then e.production_qnty else 0 end ) as ot_hour_production_qty
        FROM wo_po_details_master          a,
		wo_po_break_down              b,
		wo_po_color_size_breakdown    c,
		pro_garments_production_mst   d,
		pro_garments_production_dtls  e
        WHERE     a.id = b.job_id
		AND b.id = c.po_break_down_id
		AND b.id = d.po_break_down_id
		AND d.id = e.mst_id
		AND c.id = e.color_size_break_down_id
		$company_name
		AND d.production_type IN (4, 5)
		AND d.production_date <= '$date' 

 --       and d.sewing_line='67'

		AND a.status_active = 1
		AND a.is_deleted = 0
		AND b.status_active = 1
		AND b.is_deleted = 0
		AND c.status_active = 1
		AND c.is_deleted = 0
		and d.status_active=1
		and d.is_deleted=0
		and e.status_active=1
		and e.is_deleted=0
		group by d.sewing_line,
		a.buyer_name,
		a.style_ref_no,
		a.job_no,
		b.po_number,
		b.id,
		c.color_number_id,
		c.size_number_id,
		b.po_quantity,
		c.order_quantity,
		d.serving_company,
		d.production_hour,
		d.prod_reso_allo,
		d.production_date 
		order by d.sewing_line,c.color_number_id,d.serving_company";
        // echo $sql;
		$sql_result = sql_select($sql);
        $data_array = array();
		$company_wise_production_qty_array = array();
		$po_id_array =array();
		foreach($sql_result as $row){

			if($row[csf('prod_reso_allo')]==1)
			{
				$sewing_line_ids=$prod_reso_line_arr[$row[csf('sewing_line')]];
				$sl_ids_arr = explode(",", $sewing_line_ids);
				$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take
			}
			else
			{
				$sewing_line_id=$row[csf('sewing_line')];
			}

			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			// echo $sewing_line_id."**".$lineSerialArr[$sewing_line_id]."**".$slNo."<br>";
            // ======================
			// echo "<pre>";
		    // print_r($row);
           $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['buyer_name'] = $row[csf('buyer_name')];
           $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
           $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['po_number'] = $row[csf('po_number')];
		   $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['po_quantity'] = $row[csf('po_quantity')];
           $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['color_number_id'] = $row[csf('color_number_id')];
		   $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['order_quantity'] += $row[csf('order_quantity')];
		   $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['today_sewing_input'] += $row[csf('today_sewing_input')];
		   $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['today_sewing_output'] += $row[csf('today_sewing_output')];
		   $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['prod_reso_allo'] = $row[csf('prod_reso_allo')];
		   $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['normal_hour_production_qty'] += $row[csf('normal_hour_production_qty')];
		   $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['ot_hour_production_qty'] += $row[csf('ot_hour_production_qty')];
		   $data_array[$row[csf('serving_company')]][$slNo][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['production_date'] = change_date_format($row[csf('production_date')]);

		   $company_wise_production_qty_array[$row[csf('serving_company')]]['normal_hour_production_qty'] += $row[csf('normal_hour_production_qty')];
		   $company_wise_production_qty_array[$row[csf('serving_company')]]['ot_hour_production_qty'] += $row[csf('ot_hour_production_qty')];

		   $po_id_string .= $row[csf('po_id')].",";

		}
		// echo $po_id_string;
		$po_id_array = array_unique(array_filter(explode(",",$po_id_string)));
		// echo "<pre>";
		// print_r($data_array);

		 // ================================= Working Hour ===============================================
		// $working_hour_sql= "SELECT d.sewing_line,
		// b.po_number,
		// b.id as po_id,
		// c.color_number_id,
		// d.serving_company,
		// TO_CHAR (d.production_hour, 'hh24:mi') as production_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '08:00' and to_char(d.production_hour, 'hh24:mi') <= '08:59' then e.production_qnty else 0 end ) as eight_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '09:00' and to_char(d.production_hour, 'hh24:mi') <= '09:59' then e.production_qnty else 0 end ) as nine_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '10:00' and to_char(d.production_hour, 'hh24:mi') <= '10:59' then e.production_qnty else 0 end ) as ten_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '11:00' and to_char(d.production_hour, 'hh24:mi') <= '11:59' then e.production_qnty else 0 end ) as eleven_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '12:00' and to_char(d.production_hour, 'hh24:mi') <= '12:59' then e.production_qnty else 0 end ) as twelve_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '13:00' and to_char(d.production_hour, 'hh24:mi') <= '13:59' then e.production_qnty else 0 end ) as thirteen_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '14:00' and to_char(d.production_hour, 'hh24:mi') <= '14:59' then e.production_qnty else 0 end ) as fourteen_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '15:00' and to_char(d.production_hour, 'hh24:mi') <= '15:59' then e.production_qnty else 0 end ) as fifteen_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '16:00' and to_char(d.production_hour, 'hh24:mi') <= '16:59' then e.production_qnty else 0 end ) as sixteen_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '17:00' and to_char(d.production_hour, 'hh24:mi') <= '17:59' then e.production_qnty else 0 end ) as seventeen_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '18:00' and to_char(d.production_hour, 'hh24:mi') <= '18:59' then e.production_qnty else 0 end ) as eighteen_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '19:00' and to_char(d.production_hour, 'hh24:mi') <= '19:59' then e.production_qnty else 0 end ) as nineteen_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '20:00' and to_char(d.production_hour, 'hh24:mi') <= '20:59' then e.production_qnty else 0 end ) as twenty_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '21:00' and to_char(d.production_hour, 'hh24:mi') <= '21:59' then e.production_qnty else 0 end ) as twenty_one_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '22:00' and to_char(d.production_hour, 'hh24:mi') <= '22:59' then e.production_qnty else 0 end ) as twenty_two_hour,
		// SUM(case when to_char(d.production_hour, 'hh24:mi') >= '23:00' and to_char(d.production_hour, 'hh24:mi') <= '23:59' then e.production_qnty else 0 end ) as twenty_three_hour

        // FROM wo_po_details_master          a,
		// wo_po_break_down              b,
		// wo_po_color_size_breakdown    c,
		// pro_garments_production_mst   d,
		// pro_garments_production_dtls  e
        // WHERE     a.id = b.job_id
		// AND b.id = c.po_break_down_id
		// AND b.id = d.po_break_down_id
		// AND d.id = e.mst_id
		// AND c.id = e.color_size_break_down_id
		// $company_name
		// AND d.production_type IN (4, 5)
		// AND d.production_date BETWEEN '$date' AND '$date'

		// AND a.status_active = 1
		// AND a.is_deleted = 0
		// AND b.status_active = 1
		// AND b.is_deleted = 0
		// AND c.status_active = 1
		// AND c.is_deleted = 0
		// and d.status_active=1
		// and d.is_deleted=0
		// and e.status_active=1
		// and e.is_deleted=0
		// group by d.sewing_line,
		// b.po_number,
		// b.id,
		// c.color_number_id,
		// d.serving_company,
		// d.production_hour,
		// d.production_date 
		// order by d.sewing_line,c.color_number_id,d.serving_company";
        // // echo $working_hour_sql;
		// $working_hour_sql_result = sql_select($working_hour_sql);

        // $working_hour_array = array();
		
		// foreach($working_hour_sql_result as $row){
		// 	// echo "<pre>";
		//     // print_r($row);
         
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['eight_hour'] += $row[csf('eight_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['nine_hour'] += $row[csf('nine_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['ten_hour'] += $row[csf('ten_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['eleven_hour'] += $row[csf('eleven_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['twelve_hour'] += $row[csf('twelve_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['thirteen_hour'] += $row[csf('thirteen_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['fourteen_hour'] += $row[csf('fourteen_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['fifteen_hour'] += $row[csf('fifteen_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['sixteen_hour'] += $row[csf('sixteen_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['seventeen_hour'] += $row[csf('seventeen_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['eighteen_hour'] += $row[csf('eighteen_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['nineteen_hour'] += $row[csf('nineteen_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['twenty_hour'] += $row[csf('twenty_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['twenty_one_hour'] += $row[csf('twenty_one_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['twenty_two_hour'] += $row[csf('twenty_two_hour')];
		//    $working_hour_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['twenty_three_hour'] += $row[csf('twenty_three_hour')];
		// }
		// echo "<pre>";
		// print_r($working_hour_array);

		// $working_hour_count = array();
        // foreach($working_hour_array as $company_key=>$company_data)
		// {
		// 	foreach($company_data as $line_key=>$line_value)
		// 	{
		// 		foreach($line_value as $po_key=>$po_value)
		// 		{
		// 			foreach($po_value as $color_key=>$color_value)
		// 			{
		// 				foreach($color_value as $data)
		// 				{
		// 					if($data>0){
		// 						$working_hour_count[$company_key][$line_key]++;
		// 					}
							
		// 				}
		// 			}
		// 		}
		// 	}
			
		// }

		// echo "<pre>";
		// print_r($working_hour_count);

		// ================================= Total Production Query ===============================================
		$production_sql= "SELECT d.sewing_line,
		b.id
			AS po_id,
		c.color_number_id,
		d.serving_company,
		MIN (CASE WHEN d.production_type = 4 THEN d.production_date END)
			AS input_first_date,
		MIN (CASE WHEN d.production_type = 5 THEN d.production_date END)
			AS output_first_date,
		MAX (CASE WHEN d.production_type = 5 THEN d.production_date END)
			AS output_last_date,
		SUM (
			CASE
				WHEN     d.production_type = 4
					 AND e.production_type = 4
					 AND d.production_date <= '$date'
				THEN
					e.production_qnty
				ELSE
					0
			END)
			AS total_sewing_input,
		SUM (
			CASE
				WHEN     d.production_type = 5
					 AND e.production_type = 5
					 AND d.production_date <= '$date'
				THEN
					e.production_qnty
				ELSE
					0
			END)
			AS total_sewing_output,
		sum(case when to_char(d.production_hour, 'hh24:mi') >= '08:00' and to_char(d.production_hour, 'hh24:mi') <= '17:00' and d.production_date between '$date_from' and '$date_to' then e.production_qnty else 0 end ) as monthly_normal_hour_production_qty,
		sum(case when to_char(d.production_hour, 'hh24:mi') >= '17:01' and to_char(d.production_hour, 'hh24:mi') <= '24:00' and d.production_date between '$date_from' and '$date_to' then e.production_qnty else 0 end ) as monthly_ot_hour_production_qty
        FROM wo_po_details_master        a,
		wo_po_break_down            b,
		wo_po_color_size_breakdown  c,
		pro_garments_production_mst d,
		pro_garments_production_dtls e
        WHERE     a.id = b.job_id
		AND b.id = c.po_break_down_id
		AND b.id = d.po_break_down_id
		AND d.id = e.mst_id
		AND c.id = e.color_size_break_down_id
		$company_name
		AND d.production_type IN (4, 5)
		AND a.status_active = 1
		AND a.is_deleted = 0
		AND b.status_active = 1
		AND b.is_deleted = 0
		AND c.status_active = 1
		AND c.is_deleted = 0
		and d.status_active=1
		and d.is_deleted=0
		and e.status_active=1
		and e.is_deleted=0
        
--		and d.sewing_line='67'

        GROUP BY d.sewing_line,b.id,c.color_number_id,d.serving_company
        ORDER BY d.sewing_line, c.color_number_id, d.serving_company";
        // echo $production_sql;
		$production_sql_result = sql_select($production_sql);

		$production_sql_array = array();
		$company_production_hour_qty_array = array();
		foreach($production_sql_result as $row){
			// echo "<pre>";
		    // print_r($row);
		   $production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['total_sewing_input'] += $row[csf('total_sewing_input')];
		   $production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['total_sewing_output'] += $row[csf('total_sewing_output')];

		   $production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['input_first_date'] = change_date_format($row[csf('input_first_date')]);
		   $production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['output_first_date'] = change_date_format($row[csf('output_first_date')]);
		   $production_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['output_last_date'] = change_date_format($row[csf('output_last_date')]);
		   
		   $company_production_hour_qty_array[$row[csf('serving_company')]]['monthly_normal_hour_production_qty'] += $row[csf('monthly_normal_hour_production_qty')];
		   $company_production_hour_qty_array[$row[csf('serving_company')]]['monthly_ot_hour_production_qty'] += $row[csf('monthly_ot_hour_production_qty')];
		}
		// echo "<pre>";
		// print_r($production_line_sql_array);
		// ================================= Color Wise Order Qnty =============================================
		  $po_id_cond = where_con_using_array($po_id_array,0,"c.po_break_down_id");
          $sql_order_qnty =" SELECT d.sewing_line,
		  b.id
			  AS po_id,
		  c.color_number_id,
		  c.size_number_id,
		  c.order_quantity,
		  d.serving_company
	      FROM wo_po_details_master        a,
		  wo_po_break_down            b,
		  wo_po_color_size_breakdown  c,
		  pro_garments_production_mst d
	      WHERE     a.id = b.job_id
			AND b.id = c.po_break_down_id
			AND b.id = d.po_break_down_id
			$company_name $po_id_cond 
			AND d.production_type IN (4, 5)
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.status_active = 1
			AND c.is_deleted = 0
			and d.status_active=1
			and d.is_deleted=0
          GROUP BY d.sewing_line,
		  b.id,
		  c.color_number_id,
		  c.size_number_id,
		  c.order_quantity,
		  d.serving_company
          ORDER BY d.sewing_line, c.color_number_id, d.serving_company";
		//   echo $sql_order_qnty;
		  $sql_order_qnty_result = sql_select($sql_order_qnty);
		  $color_wise_order_sql_array = array();
		  foreach($sql_order_qnty_result as $row){
			$color_wise_order_sql_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['order_quantity'] += $row[csf('order_quantity')];
		  }
		//   echo "<pre>";
		// print_r($color_wise_order_sql_array);

        // ================================= Row span ===============================================
		$sewing_line_rowspan_array =array();
		$wip_data=array();
		foreach($data_array as $company_id => $company_value)
		{
			foreach($company_value as $serial_id => $serial_value)
			{
				foreach($serial_value as $sewing_line_id => $sewing_value)
				{
					$line_wip = 0;

					foreach($sewing_value as $po_id => $po_value)
					{
						foreach($po_value as $color_id => $color_value)
						{
							

							$output_bal = $production_sql_array[$company_id][$sewing_line_id][$po_id][$color_id]['total_sewing_output'] - $production_sql_array[$company_id][$sewing_line_id][$po_id][$color_id]['total_sewing_input'];
							// if($output_bal != 0 || (strtotime($date) == strtotime($color_value['production_date'])))
							if( $color_value['today_sewing_output'] !=0)
							// if(strtotime($date) == strtotime($color_value['production_date']))
							{
								$line_wip += $output_bal;
							    $sewing_line_rowspan_array[$company_id][$serial_id][$sewing_line_id]++;
							}
							
						}
					}
					$wip_data[$company_id][$serial_id][$sewing_line_id] = $line_wip ;
				}
			}
		}	
		// echo "<pre>";
		// print_r($sewing_line_rowspan_array);	

	 ?>

		<fieldset style="width:1730px">
			<table width="1800" cellpadding="0" cellspacing="0"> 
				<!-- <tr class="form_caption">
					<td align="center"><p style="font-size:25px; font-weight:bold;"> Radiance Group<p></td> 
				</tr> -->
				<tr class="form_caption">
					<td align="center"><p style="font-size:21px; font-weight:bold;"><? echo $companyArr[$company_id]; ?> Production Status</p></td> 
				</tr>
				<tr class="form_caption">
					<td align="center"><p style="font-size:18px; font-weight:bold;"><? echo "Date: (As On : ".change_date_format( str_replace("'","",trim($txt_date)) ).")"; ?></p></td> 
				</tr>
			</table>
			<br />
			<table id="table_header_1" class="rpt_table" width="1750" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr height="50">
					<th width="100">LINE</th>
					<th width="100">BUYER</th>
					<th width="100">STYLE</th>
					<th width="100">PO</th>
					<th width="100">Order Qty</th>
					<th width="100">Color</th>
					<th width="100">Color Qty</th>
					<th width="100">1st INPUT-DATE style or PO</th>
					<th width="100" style="color:red;">TODAY INPUT</th>
					<th width="100">TTL INPUT </th>
					<th width="100">PRODUCTION <br>(Normal Working Hour)</th>
					<th width="100">PRODUCTION <br>(O.T Working Hour)</th>
					<th width="100">1st Output Date </th>
					<th width="100" style="color:red;">DAY OUTPUT</th>
					<th width="100">TTL OUTPUT</th>
					<th width="100"><b>Last Output Date</b></th>
					<th width="70">OUTPUT BALANCE</th>
					<th width="60"><b>WIP</b></th>
					<!-- <th width="70">Working Hour</th> -->
				</tr>
			</thead>
			</table>
		<div style="width:1750; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="1750" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					    $k = 0;
					    foreach($data_array as $company_id => $company_value)
						{
							$total_today_sewing_input          = 0;
							$total_ttl_input                   = 0;
							$total_normal_hour_production_qty  = 0;
							$total_ot_hour_production_qty      = 0;
							$total_today_sewing_output         = 0;
							$total_ttl_output                  = 0;
							$total_output_balance              = 0;

							ksort($company_value);
							foreach($company_value as $serial_id => $serial_value)
							{
								foreach($serial_value as $sewing_line_id => $sewing_value)
								{
									$l = 0;
									$w = 0;
									$working_h = 0;
									foreach($sewing_value as $po_id => $po_value)
									{
										foreach($po_value as $color_id => $color_value)
										{
											if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

											$total_in = $production_sql_array[$company_id][$sewing_line_id][$po_id][$color_id]['total_sewing_input'];
											$total_out = $production_sql_array[$company_id][$sewing_line_id][$po_id][$color_id]['total_sewing_output'];
											$output_balance_qty = ($total_out - $total_in);
											// echo $output_balance_qty. "__".$total_out."__".$total_in."<br/>";

                                            // echo $output_balance_qty."__".strtotime($date)."__".strtotime($color_value['production_date'])."<br/>";
											// if(strtotime($date) == strtotime($color_value['production_date']) ) 
											if( $color_value['today_sewing_output'] !=0 )
											// if( $color_value['today_sewing_output'] !=0 )
											// if($output_balance_qty != 0 || (strtotime($date) == strtotime($color_value['production_date'])))
											{
												// echo $output_balance_qty."__".strtotime($date)."__".strtotime($color_value['production_date'])."<br/>";
												// echo $output_balance_qty."__".$date."__".$color_value['production_date']."<br/>";
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
													<? if ($l==0)
													{ 
														?>
														<td width="100" title="<?=$sewing_line_id;?>" valign="middle" rowspan="<? echo $sewing_line_rowspan_array[$company_id][$serial_id][$sewing_line_id]; ?>">
																	<? 
																		if($color_value['prod_reso_allo']==1)
																		{
																			$sewing_line=$prod_reso_line_arr[$sewing_line_id];
																			$sewing_line_arr=explode(",",$sewing_line);
																			$sewing_line_name="";
																			foreach($sewing_line_arr as $line_id)
																			{
																				$sewing_line_name.=$lineArr[$line_id].",";
																			}
																			$sewing_line_name=chop($sewing_line_name,",");
																			echo $sewing_line_name;
																		}
																		else
																		{
																			echo $lineArr[$line_id];
																		}
																	?>
														</td>
														<?
														$l++;
													}
													?>
													<td width="100"><? echo $buyer_library[$color_value['buyer_name']];?></td>
													<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $color_value['style_ref_no'];?></td>
													<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $color_value['po_number'];?></td>
													<td width="100" align="right"><? echo $color_value['po_quantity'];?></td>
													<td width="100" title="<?=$color_value['color_number_id'];?>"><? echo $color_library[$color_value['color_number_id']];?></td>
													<td width="100" align="right">
														<? 
														echo $color_wise_order_sql_array[$company_id][$sewing_line_id][$po_id][$color_id]['order_quantity'];

														?>
													</td>
													<td width="100" align="center"><? echo $production_sql_array[$company_id][$sewing_line_id][$po_id][$color_id]['input_first_date']; ?></td>
													<td width="100" align="right" style="color:red; font-weight:bold;"><? echo $color_value['today_sewing_input'];?></td>
													<td width="100" align="right" style="font-weight:bold;">
														<? 
															$total_input = $production_sql_array[$company_id][$sewing_line_id][$po_id][$color_id]['total_sewing_input'];
															echo number_format($total_input,0);
														?>
													</td>
													<td width="100" align="right"><? echo $color_value['normal_hour_production_qty'];?></td>
													<td width="100" align="right"><? echo $color_value['ot_hour_production_qty'];?></td>
													<td width="100" align="center"><? echo $production_sql_array[$company_id][$sewing_line_id][$po_id][$color_id]['output_first_date'];?></td>
													<td width="100" align="right" style="color:red; font-weight:bold;"><? echo $color_value['today_sewing_output'];?></td>
													<td width="100" align="right" style="font-weight:bold;">
														<? 
															$total_output = $production_sql_array[$company_id][$sewing_line_id][$po_id][$color_id]['total_sewing_output'];
															echo number_format($total_output,0);
														?>
													</td>
													<td width="100" align="center"><? echo $production_sql_array[$company_id][$sewing_line_id][$po_id][$color_id]['output_last_date'];?></td>
													<td width="70" align="right">
														<? 
															$output_balance = ($total_output - $total_input);
															echo number_format($output_balance ,0);
															// echo number_format($output_balance_qty ,0);
														?>
													</td>
													<? if ($w==0)
													{ 
														?>
														<td width="60" align="right" valign="middle" rowspan="<? echo $sewing_line_rowspan_array[$company_id][$serial_id][$sewing_line_id]; ?>">
															<? 
																echo number_format($wip_data[$company_id][$serial_id][$sewing_line_id],0);
															?>
														</td>
														<?
														$w++;
													}
													?>	
													<!-- <?// if ($working_h==0)
													{ 
														?>
														<td width="70" align="center" valign="middle" rowspan="<? //echo $sewing_line_rowspan_array[$company_id][$sewing_line_id]; ?>">
															<?// echo $working_hour_count[$company_key][$line_key]++;?>
														</td>
													<?
													//	$working_h++;
													}
													?>	 -->
												</tr>
												<?
												
												$k++;

												$total_today_sewing_input          += $color_value['today_sewing_input'];
												$total_ttl_input                   += $total_input;
												$total_normal_hour_production_qty  += $color_value['normal_hour_production_qty'];
												$total_ot_hour_production_qty      += $color_value['ot_hour_production_qty'];
												$total_today_sewing_output         += $color_value['today_sewing_output'];
												$total_ttl_output                  += $total_output; 
												$total_output_balance              += $output_balance; 
											}

										}
									}
								}
							}
							 ?>
								<tr style="background:#dfdfdf;">
									<td colspan="6" align="center"><b> <? echo $companyArr[$company_id];?> Total<b></td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100" align="right" style="font-weight:bold; color: red;"><? echo number_format($total_today_sewing_input ,0);?></td>
									<td width="100" align="right" style="font-weight:bold;"><? echo number_format($total_ttl_input ,0);?></td>
									<td width="100" align="right" style="font-weight:bold;"><? echo number_format($total_normal_hour_production_qty ,0);?></td>
									<td width="100" align="right" style="font-weight:bold;"><? echo number_format($total_ot_hour_production_qty ,0);?></td>
									<td width="100">&nbsp;</td>
									<td width="100" align="right" style="font-weight:bold; color: red;"><? echo number_format($total_today_sewing_output ,0);?></td>
									<td width="100" align="right" style="font-weight:bold;"><? echo number_format($total_ttl_output ,0);?></td>
									<td width="100">&nbsp;</td>
									<td width="70" align="right" style="font-weight:bold;"><? echo number_format($total_output_balance ,0);?></td>
									<td width="60">&nbsp;</td>
									<!-- <td width="70">&nbsp;</td> -->
								</tr>
							 <?

						}	
					?> 
				</tbody>                   
			</table>
			<br>
			<table id="table_header_1" class="rpt_table" width="820" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td valign="top">
					    <table class="rpt_table" width="420" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th colspan="4">Daily Production Summary</th>
								</tr>
								<tr height="50">
									<th width="100">Unit</th>
									<th width="100">PRODUCTION(Normal Working Hour)</th>
									<th width="100">PRODUCTION(O.T Working Hour)</th>
									<th width="100">Total Production</th>
								</tr>
							</thead>
							<tbody>
								<?
								foreach( $company_wise_production_qty_array as $com_id => $com_val)
								{
									?>
										<tr>
											<td width="100" align="center" valign="top"><? echo $companyArr[$com_id];?></td>
											<td width="100" align="center"><? echo $com_val['normal_hour_production_qty']; ?></td>
											<td width="100" align="center"><? echo $com_val['ot_hour_production_qty']; ?></td>
											<td width="100" align="center"><? echo number_format(($com_val['normal_hour_production_qty'] + $com_val['ot_hour_production_qty']),0); ?> </td>
										</tr>
									<?
								}
								?>
								
							</tbody> 
						</table>
					</td>

					<td width="50" style="margin-left:100px;">&nbsp;</td>

					<td valign="top">
					    <table class="rpt_table" width="420" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th colspan="4">Monthly Production Summary</th>
								</tr>
								<tr height="50">
									<th width="100">Unit</th>
									<th width="100">PRODUCTION(Normal Working Hour)</th>
									<th width="100">PRODUCTION(O.T Working Hour)</th>
									<th width="100">Total Production</th>
								</tr>
							</thead>
							<tbody>
							    <?
									foreach( $company_production_hour_qty_array as $com_key => $com_val)
									{
										// $grand_total_working_hour =0;
										?>
											<tr>
												<td width="100" align="center" valign="top"><? echo $companyArr[$com_key];?></td>
												<td width="100" align="center">
													<? 
													echo $com_val['monthly_normal_hour_production_qty']; 
													$grand_total_normal_working_hour += $com_val['monthly_normal_hour_production_qty']; 
													?>
												</td>
												<td width="100" align="center">
													<? 
													echo $com_val['monthly_ot_hour_production_qty']; 
													$grand_total_o_t_working_hour += $com_val['monthly_ot_hour_production_qty'];
													?>
												</td>
												<td width="100" align="center">
													<? 
													 $total_production_hour = $com_val['monthly_normal_hour_production_qty'] + $com_val['monthly_ot_hour_production_qty']; 
													 echo number_format($total_production_hour,0);
													 $grand_total_production_hour += $total_production_hour; 
													?> 
												</td>
											</tr>
										<?
									}
								?>
								        <tr>
											<td width="100" align="center" valign="top" style="font-weight:bold;">Grand Total</td>
											<td width="100" align="center" style="font-weight:bold;">
												<? 
												   echo number_format($grand_total_normal_working_hour,0); 
												?>
										    </td>
											<td width="100" align="center" style="font-weight:bold;">
												<? 
												   echo number_format($grand_total_o_t_working_hour,0); 
												?>
											</td>
											<td width="100" align="center" style="font-weight:bold;">
													<? 
													  echo number_format($grand_total_production_hour,0);
													?> 
											</td>
										</tr>
							</tbody> 
						</table>
					</td>
				</tr>
			</table>
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
	echo "$total_data####$filename####$type";
	exit();      

}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $company_id;
	//$sewing_line=explode("*",$sewing_line);
	//$sewing_line=implode(",",$sewing_line);
	$po_id=explode("*",$po_id);
	$po_id=implode(",",$po_id);
	$sql_line_remark=sql_select("SELECT remarks,production_hour from pro_garments_production_mst where serving_company in(".$company_id.") and  floor_id=$floor_id and sewing_line in($sewing_line) and po_break_down_id in($po_id) and production_date='".$prod_date."' and production_type=5 and status_active=1 and is_deleted=0 group by remarks,production_hour order by production_hour");


	?>
	<fieldset style="width:520px;  ">
		<div id="report_container">
        
        		<h4>Remarks From Sewing Output</h4>
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<thead>
                    	<tr>
                            <th width="40">SL</th>
                            <th width="460">Remarks</th>
                        </tr>
					</thead>
					<tbody>
					<?
					$i=1;
					foreach($sql_line_remark as $inf)
					{
						 if ($i%2==0)    $bgcolor="#E9F3FF";
						 else            $bgcolor="#FFFFFF";
						 if(trim($inf[csf('remarks')])!="")
						 {
						 ?>		
						   <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td align="left"><? echo $inf[csf('remarks')]; ?>&nbsp;</td>
						</tr>
						<?
						$i++;
						 }
					}
					?>
					</tbody>
				</table>
		</div>
	</fieldset>
    <br/>
    
    <fieldset style="width:520px;  ">
		<div id="report_container">
        
        		<h4>Remarks From Actual Resource</h4>
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<thead>
                    	<tr>
                            <th width="40">SL</th>
                            <th width="460">Remarks</th>
                        </tr>
					</thead>
					<tbody>
					<?
					
					$sql_Actual_remarks=sql_select("select d.remarks from prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d where a.id='".$sewing_line."' and a.id=b.mst_id and b.mast_dtl_id =d.mast_dtl_id and b.mst_id=d.mst_id and  a.company_id  in(".$company_id.") and pr_date='".$prod_date."' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0  ");
					
					$i=1;
					foreach($sql_Actual_remarks as $inf)
					{
						 if ($i%2==0)    $bgcolor="#E9F3FF";
						 else            $bgcolor="#FFFFFF";
						 if(trim($inf[csf('remarks')])!="")
						 {
							 ?>		
							   <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td align="left"><? echo $inf[csf('remarks')]; ?>&nbsp;</td>
							</tr>
							<?
							$i++;
						 }
					}
					?>
					</tbody>
				</table>
		</div>
	</fieldset>
	   
		  <?
}


if($action=="tot_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:520px; ">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<th width="30">SL</th>
                    <th width="120">Order No</th>
                    <th width="70">Item Smv</th>
                    <th width="100">Production Qnty</th>
                    <th width="100">Produced Min.</th>
				</thead>
       <?
		$new_smv=array();
		$item_smv_pop=explode("****",$item_smv);
		$order_id="";
		foreach($item_smv_pop as $po_id_smv) 
		{
		   $po_id_smv_pop=explode("**",$po_id_smv);
		   $new_smv[$po_id_smv_pop[0]]=$po_id_smv_pop[1];
		}
					
		$actual_date=date("Y-m-d");
	    $actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
		if($db_type==0)
		{	
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				$sql_pop=sql_select("select  c.po_number,a.po_break_down_id,
				sum(CASE WHEN a.production_hour>'$line_date'  and a.production_hour<='$actual_time'  and a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty 
				
				from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
				where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id in (".$company_id.")  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
			}
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
			{
				$sql_pop=sql_select("select  c.po_number,a.po_break_down_id,
				sum(CASE WHEN a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty 
				
				from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
				where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id in (".$company_id.") and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
			}
		}
		
		
		else
		{
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				$sql_pop=sql_select("select  c.po_number,a.po_break_down_id,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$line_date'  and TO_CHAR(a.production_hour,'HH24:MI')<='$actual_time'  and a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty 
				
				from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
				where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id in (".$company_id.") and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
			
			}
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
			{
			
				$sql_pop=sql_select("select  c.po_number,a.po_break_down_id,
				sum(CASE WHEN  a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty 
				
				from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
				where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id in (".$company_id.")  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
			
			}
		}
		
		
        $subcon_production_data_arr=array();
		foreach($sql_pop as $pro_val)
		{
			  $subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]]['po_number']=$pro_val[csf('po_number')];	
			  $subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]]['po_qty']=$pro_val[csf('good_qnty')];	
			  $subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]]['item_smv']=$new_smv[$pro_val[csf('po_break_down_id')]];	
			
		}
				
		if($subcon_order!="")
		{
	         if($db_type==0)
			 {
				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					$sql_subcon=sql_select("select  
					a.order_id,c.smv,
					c.order_no as po_number,
					sum(CASE WHEN a.hour>'$line_date' and a.hour<='$actual_time' and a.production_type=2  THEN a.production_qnty else 0 END) AS good_qnty
					from subcon_gmts_prod_dtls a, subcon_ord_dtls c
					where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id in (".$company_id.")  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'                         	   group by a.order_id, c.order_no,c.smv");
				}
				if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
				{
				
					$sql_subcon=sql_select("select  
					a.order_id,c.smv,
					c.order_no as po_number,
					sum(a.production_qnty ) AS good_qnty
					from subcon_gmts_prod_dtls a, subcon_ord_dtls c
					where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id in (".$company_id.")  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'                         	   group by a.order_id, c.order_no,c.smv");
				}
			 }
			 else
			 {
				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					$sql_subcon=sql_select("select  
					a.order_id,c.smv,
					c.order_no as po_number,
					sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$line_date' and TO_CHAR(a.hour,'HH24:MI')<='$actual_time' and a.production_type=2  THEN a.production_qnty else 0 END) AS good_qnty
					from subcon_gmts_prod_dtls a, subcon_ord_dtls c
					where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id in (".$company_id.")  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'                         	   group by a.order_id, c.order_no,c.smv"); 
				}
				
				if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
				{
					$sql_subcon=sql_select("select  
					a.order_id,c.smv,
					c.order_no as po_number,
					sum(a.production_qnty) AS good_qnty
					from subcon_gmts_prod_dtls a, subcon_ord_dtls c
					where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id in (".$company_id.")  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'                         	   group by a.order_id, c.order_no,c.smv"); 
				}
			 }
		}
		
		foreach($sql_subcon as $sub_val)
		{
			$subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['po_number']=$sub_val[csf('po_number')];	
			$subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['po_qty']=$sub_val[csf('good_qnty')];	
			$subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['item_smv']=$sub_val[csf('smv')];	
		}		   
							   
					//print_r($subcon_production_data_arr);
                 
					
			$total_producd_min=0;
			$i=1; $total_qnty=0;
			foreach($subcon_production_data_arr as $sub_id=>$pop_val)
			{
				foreach($pop_val as $po_id=>$pop_val)
				{
				
				   if ($i%2==0)  
						$bgcolor="#E9F3FF";
				   else
						$bgcolor="#FFFFFF";	
				
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="120" align="center"><? echo $pop_val['po_number']; ?></td>
						<td align="right"><? echo $pop_val['item_smv']; ?>&nbsp;</td>
						<td align="right"><? $total_po_qty+=$pop_val['po_qty']; echo $pop_val['po_qty']; ?>&nbsp;</td>
						<td align="right">
							 <?
							   $producd_min=$pop_val['po_qty']*$pop_val['item_smv'];  $total_producd_min+=$producd_min;
							  echo $producd_min;
							  ?>&nbsp;</td>
					</tr>
				<?
				$i++;
				}
			}
                    ?>
                    <tfoot>
                        <th colspan="3" align="right">Total</th>
                        <th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
                        <th align="right"><? echo $total_producd_min; ?>&nbsp;</th>
                    </tfoot>
                </table>
           
        </div>
	</fieldset>   
	<?
	exit();
}




if($action=="tot_input_output_popup")
{
	echo load_html_head_contents("FOB Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$po_number_Arr = return_library_array("select id,po_number from  wo_po_break_down ","id","po_number");
	
	?>
	
     <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			$("#table_body tr:first").hide();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			$("#table_body tr:first").show();
		}	
	</script>
    
    <?php
	
	
	if($type==1)
	{
		?>
		
	    <fieldset style="width:1000px; ">
			<div style="width:500px;" align="center">
	        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
	        </div>
			<div id="report_container" align="center">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Details</strong></caption>
	                <thead>
	                	<th width="30">SL</th>
	                    <th width="120">Order No</th>
	               
	                    <th width="100">Input Qty.</th>
					</thead>
	                </table>
	                <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
	                <?
							
		
					$sql_pop="select  c.po_number,a.po_break_down_id, sum(a.production_quantity)  as good_qnty from pro_garments_production_mst a, wo_po_break_down c where a.production_type=4 and a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor." group by c.po_number,a.po_break_down_id  order by  c.po_number ";
				
				//and a.production_date='".$production_date."'
				//echo $sql_pop;die;	
					$sql_result=sql_select($sql_pop);
					$k=1;$total_amount=0;$total_prod_qty=0;
					foreach($sql_result as $row)
					{
						  if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
					   ?>
						  <tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>
				
							<td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  number_format($row[csf('good_qnty')],2); ?></td> 
						</tr>
						<?
						$total_prod_qty+=$row[csf('good_qnty')];
						$k++;
	                }
					?>
	                <tr class="tbl_bottom" >
	                <td colspan="2"> Total </td>
	         
	                 <td align="right"> <? echo number_format($total_prod_qty,2);?></td>
	                </tr>
	          	</table>
				
	                <?
							
					$sql_color_size="select  c.color_number_id,c.size_number_id ,a.po_break_down_id, sum(b.production_qnty)  as good_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type=4 and a.production_type=4 and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.color_number_id,c.size_number_id ,a.po_break_down_id  order by  a.po_break_down_id ";
				
				//echo $sql_color_size;die;	
					$size_arr=array();
					$order_color_arr=array();
					$grand_size_arr=array();
					$grand_total=0;
					$color_size_qty_arr=array();
					$sql_color_size_result=sql_select($sql_color_size);
					foreach($sql_color_size_result as $cs_val)
					{
						$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
						$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						
						$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]]+=$cs_val[csf('good_qnty')];
						
						$grand_size_arr[$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						$grand_total+=$cs_val[csf('good_qnty')];
					}
				
				$input_width=450+count($size_arr)*50;
					?>
	                <table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Color Size Breakdown</strong></caption>
	                <thead>
	                	<th width="30">SL</th>
	                    <th width="180">Order No</th>
	                    <th width="150">Color</th>
	               		<?php 
						foreach($size_arr as $sid)
						{
						?>
	                    	<th width="70"><?php echo $itemSizeArr[$sid] ;?></th>
	                    <?php
						}
						?>
	                    <th width="100">Color Total</th>
					</thead>
	                </table>
	                <table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center" id="table_body">
	                
	                <?php
					$r=1;
					foreach($color_size_qty_arr as $po_id=>$po_value)
					{
						
						foreach($po_value as $color_id=>$color_value)
						{
						  	if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						  	?>
	                         	<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
	                                <td width="30" rowspan="<?php // echo $order_color_arr[$po_id]; ?> "><? echo $r; ?></td>
	                                <td width="180" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $po_number_Arr[$po_id]; ?></td>
	                    
	                                <td width="150" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo  $colorArr[$color_id]; ?></td> 			<?php 
									foreach($size_arr as $sid)
									{
									?>
										<td width="70" align="right"><?php echo $color_value[$sid] ;?></td>
									<?php
									}
									?>
	                                <td width="100" align="right"><?php echo $order_color_arr[$po_id][$color_id] ;?></td>
	                        	</tr>
							<?
							$r++;
						}
	                }
					?>
	                <tr class="tbl_bottom" >
	                    <td colspan="3"> Total </td>
	             
	                    <?php 
	                    foreach($size_arr as $sid)
	                    {
	                    ?>
	                        <td width="70" align="right"><?php echo $grand_size_arr[$sid] ;?></td>
	                    <?php
	                    }
	                    ?>
	                    <td width="100" align="right"><?php echo $grand_total ;?></td>
	                </tr>
	          	</table>
	         </div>
	          <script>
	 		setFilterGrid("table_body",-1);
	 		</script>
	     <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>
	                
	                
		<?
	}
	else if($type==2)
	{
		?>
		
	    <fieldset style="width:1000px; ">
			<div style="width:500px;" align="center">
	        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
	        </div>
			<div id="report_container" align="center">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Output Details</strong></caption>
	                <thead>
	                	<th width="30">SL</th>
	                    <th width="120">Order No</th>
	               
	                    <th width="100">Output Qty.</th>
					</thead>
	                </table>
	                <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
	                <?
							
		
					$sql_pop="select  c.po_number,a.po_break_down_id, sum(a.production_quantity)  as good_qnty from pro_garments_production_mst a, wo_po_break_down c where a.production_type=5 and a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor." group by c.po_number,a.po_break_down_id  order by  c.po_number ";
					
					$sql_result=sql_select($sql_pop);
					$k=1;$total_amount=0;$total_prod_qty=0;
					foreach($sql_result as $row)
					{
						  if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
					   ?>
						  <tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>
				
							<td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  number_format($row[csf('good_qnty')],2); ?></td> 
						</tr>
						<?
						$total_prod_qty+=$row[csf('good_qnty')];
						$k++;
	                }
					?>
	                <tr class="tbl_bottom" >
	                <td colspan="2"> Total </td>
	         
	                 <td align="right"> <? echo number_format($total_prod_qty,2);?></td>
	                </tr>
	          	</table>
	                <?
		
					$sql_color_size="select  c.color_number_id,c.size_number_id ,a.po_break_down_id, sum(b.production_qnty)  as good_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type=5 and a.production_type=5 and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.color_number_id,c.size_number_id ,a.po_break_down_id  order by  a.po_break_down_id ";
				
				//echo $sql_color_size;	
					$size_arr=array();
					$order_color_arr=array();
					$grand_size_arr=array();
					$grand_total=0;
					$color_size_qty_arr=array();
					$sql_color_size_result=sql_select($sql_color_size);
					foreach($sql_color_size_result as $cs_val)
					{
						$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
						$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						
						$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]]+=$cs_val[csf('good_qnty')];
						
						$grand_size_arr[$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						$grand_total+=$cs_val[csf('good_qnty')];
					}
				
				$input_width=450+count($size_arr)*50;
					?>
	                <table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Output Color Size Breakdown</strong></caption>
	                <thead>
	                	<th width="30">SL</th>
	                    <th width="180">Order No</th>
	                    <th width="150">Color</th>
	               		<?php 
						foreach($size_arr as $sid)
						{
						?>
	                    	<th width="70"><?php echo $itemSizeArr[$sid] ;?></th>
	                    <?php
						}
						?>
	                    <th width="100">Color Total</th>
					</thead>
	                </table>
	                <table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center" id="table_body">
	                
	                <?php
					$r=1;
					foreach($color_size_qty_arr as $po_id=>$po_value)
					{
						
						foreach($po_value as $color_id=>$color_value)
						{
						  	if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						  	?>
	                         	<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
	                                <td width="30" rowspan="<?php // echo $order_color_arr[$po_id]; ?> "><? echo $r; ?></td>
	                                <td width="180" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $po_number_Arr[$po_id]; ?></td>
	                    
	                                <td width="150" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo  $colorArr[$color_id]; ?></td> 			<?php 
									foreach($size_arr as $sid)
									{
									?>
										<td width="70" align="right"><?php echo $color_value[$sid] ;?></td>
									<?php
									}
									?>
	                                <td width="100" align="right"><?php echo $order_color_arr[$po_id][$color_id] ;?></td>
	                        	</tr>
							<?
							$r++;
						}
	                }
					?>
	                <tr class="tbl_bottom" >
	                    <td colspan="3"> Total </td>
	             
	                    <?php 
	                    foreach($size_arr as $sid)
	                    {
	                    ?>
	                        <td width="70" align="right"><?php echo $grand_size_arr[$sid] ;?></td>
	                    <?php
	                    }
	                    ?>
	                    <td width="100" align="right"><?php echo $grand_total ;?></td>
	                </tr>
	          	</table>
	         </div>
	          <script>
	 		setFilterGrid("table_body",-1);
	 		</script>
	     <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>
	                
	                
		<?
	}
	else if($type==3)
	{
		?>
		
	    <fieldset style="width:1000px; ">
			<div style="width:600px;" align="center">
	        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
	        </div>
			<div id="report_container" align="center">
				<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Output Details </strong></caption>
	                <thead>
	                	<th width="30">SL</th>
	                    <th width="120">Order No</th>
	               		<th width="100">Input Qty.</th>
	                    <th width="100">Output Qty.</th>
	                    <th width="100">Reject Qty.</th>
	                    <th width="100">Balance Qty.</th>
					</thead>
	                </table>
	                <table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center" id="table_body">
	                <?
							
		
					$sql_pop="SELECT  c.po_number,a.po_break_down_id, sum( case when a.production_type=4 THEN a.production_quantity ELSE 0 END)  as input_qty ,sum( case when a.production_type=5 THEN a.production_quantity ELSE 0 END)  as output_qty,sum( case when a.production_type=5 THEN a.reject_qnty ELSE 0 END)  as reject_qnty from pro_garments_production_mst a, wo_po_break_down c where a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.po_number,a.po_break_down_id  order by  c.po_number ";
				//echo $sql_pop;die;
					$sql_result=sql_select($sql_pop);
					$k=1;$total_amount=0;$total_prod_qty=0;
					foreach($sql_result as $row)
					{
						  if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
					   ?>
						  <tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>
				
							<td width="100" align="right"><? echo  number_format($row[csf('input_qty')],2); ?></td>
	                        <td width="100" align="right"><? echo  number_format($row[csf('output_qty')],2); ?></td>
	                        <td width="100" align="right"><? echo  number_format($row[csf('reject_qnty')],2); ?></td>
	                        <td width="100" align="right"><? echo  number_format($row[csf('input_qty')]-$row[csf('output_qty')]-$row[csf('reject_qnty')],2); ?></td> 
						</tr>
						<?
						$total_input_qty+=$row[csf('input_qty')];
						$total_output_qty+=$row[csf('output_qty')];
						$total_reject_qnty+=$row[csf('reject_qnty')];
						$k++;
	                }
					?>
	                <tr class="tbl_bottom" >
	                <td colspan="2"> Total </td>
	         		
	                 <td align="right"> <? echo number_format($total_input_qty,2);?></td>
	                 <td align="right"> <? echo number_format($total_output_qty,2);?></td>
	                 <td align="right"> <? echo number_format($total_reject_qnty,2);?></td>
	                 <td align="right"> <? echo number_format($total_input_qty-$total_output_qty-$total_reject_qnty,2);?></td>
	                </tr>
	          	</table>
	 
	                <?
							
					$sql_color_size="SELECT  a.production_type,c.color_number_id,c.size_number_id ,a.po_break_down_id, b.production_qnty  as good_qnty,b.reject_qty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type in (4,5) and a.production_type in (4,5) and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."   order by  a.po_break_down_id ";
				
				//echo $sql_color_size;	
					$size_arr=array();
					$order_color_arr=array();
					$order_color_rej_arr=array();
					$grand_size_arr=array();
					$grand_size_rej_arr=array();
					$grand_total=array();
					$grand_total_rej=array();
					$color_size_qty_arr=array();
					$color_size_reject_qty_arr=array();
					$sql_color_size_result=sql_select($sql_color_size);
					foreach($sql_color_size_result as $cs_val)
					{
						$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
						$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];
						$color_size_reject_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('reject_qty')];
						
						$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];
						$order_color_rej_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('reject_qty')];
						
						$grand_size_arr[$cs_val[csf('size_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];
						$grand_size_rej_arr[$cs_val[csf('size_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('reject_qty')];
						$grand_total[$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];
						$grand_total_rej[$cs_val[csf('production_type')]]+=$cs_val[csf('reject_qty')];
					}
				
				$input_width=460+count($size_arr)*200;
					?>
	                <table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Output Color Size Breakdown</strong></caption>
	                <thead>
	                	<tr>
	                        <th width="30" rowspan="2">SL</th>
	                        <th width="150" rowspan="2">Order No</th>
	                        <th width="120" rowspan="2">Color</th>
	                        <?php 
	                        foreach($size_arr as $sid)
	                        {
	                        ?>
	                            <th width="200" colspan="4"><?php echo $itemSizeArr[$sid] ;?></th>
	                        <?php
	                        }
	                        ?>
	                        <th width="200" colspan="4">Color Total</th>
	                    </tr>
	                    
	                    <tr>
	                        <?php 
	                        foreach($size_arr as $sid)
	                        {
		                        ?>
		                            <th width="50">Input</th>
		                            <th width="50">Output</th>
		                        	<th width="50">Reject</th>
		                        	<th width="50">Balance</th>
		                        <?php
	                        }
	                        ?>
	                        <th width="50">Input</th>
	                        <th width="50">Output</th>
	                        <th width="50">Reject</th>
	                        <th width="50">Balance</th>
	                    </tr>
					</thead>
	                </table>
	                <table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center" id="table_body">
	                
	                <?php
					$r=1;
					foreach($color_size_qty_arr as $po_id=>$po_value)
					{
						
						foreach($po_value as $color_id=>$color_value)
						{
						  	if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						  	?>
	                         	<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
	                                <td width="30" rowspan="<?php // echo $order_color_arr[$po_id]; ?> "><? echo $r; ?></td>
	                                <td width="150" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $po_number_Arr[$po_id]; ?></td>
	                    
	                                <td width="120" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo  $colorArr[$color_id]; ?></td> 			<?php 
									foreach($size_arr as $sid)
									{
										$rejQty = $color_size_reject_qty_arr[$po_id][$color_id][$sid][5];
										?>
											<td width="50" align="right"><?php echo $color_value[$sid][4] ;?></td>
		                                    <td width="50" align="right"><?php echo $color_value[$sid][5] ;?></td>
		                                    <td width="50" align="right"><?php echo $rejQty ;?></td>
		                                    <td width="50" align="right"><?php echo $color_value[$sid][4]-$color_value[$sid][5]-$rejQty ;?></td>
										<?php
									}
									?>
	                                <td width="50" align="right"><?php echo $order_color_arr[$po_id][$color_id][4] ;?></td>
	                                <td width="50" align="right"><?php echo $order_color_arr[$po_id][$color_id][5] ;?></td>
	                                <td width="50" align="right"><?php echo $order_color_rej_arr[$po_id][$color_id][5] ;?></td>
	                                <td width="50" align="right"><?php echo $order_color_arr[$po_id][$color_id][4]-$order_color_arr[$po_id][$color_id][5]-$order_color_rej_arr[$po_id][$color_id][5] ;?></td>
	                        	</tr>
							<?
							$r++;
						}
	                }
					?>
	                <tr class="tbl_bottom" >
	                    <td colspan="3"> Total </td>
	             
	                    <?php 
	                    foreach($size_arr as $sid)
	                    {
		                    ?>
		                        <td width="50" align="right"><?php echo $grand_size_arr[$sid][4] ;?></td>
		                        <td width="50" align="right"><?php echo $grand_size_arr[$sid][5] ;?></td>
		                        <td width="50" align="right"><?php echo $grand_size_rej_arr[$sid][5] ;?></td>
		                        <td width="50" align="right"><?php echo $grand_size_arr[$sid][4]-$grand_size_arr[$sid][5]-$grand_size_rej_arr[$sid][5];?></td>
		                    <?php
	                    }
	                    ?>
	                    <td width="50" align="right"><?php echo $grand_total[4] ;?></td>
	                    <td width="50" align="right"><?php echo $grand_total[5] ;?></td>
	                    <td width="50" align="right"><?php echo $grand_total_rej[5] ;?></td>
	                    <td width="50" align="right"><?php echo $grand_total[4]-$grand_total[5]-$grand_total_rej[5] ;?></td>
	                </tr>
	          	</table>
	         </div>
	          <script>
	 		setFilterGrid("table_body",-1);
	 		</script>
	     <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>
	                
	                
		<?
	}
	if($type==4)
	{
		?>
		
	    <fieldset style="width:1000px; ">
			<div style="width:500px;" align="center">
	        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
	        </div>
			<div id="report_container" align="center">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Details</strong></caption>
	                <thead>
	                	<th width="30">SL</th>
	                    <th width="120">Order No</th>
	               
	                    <th width="100">Input Qty.</th>
					</thead>
	                </table>
	                <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
	                <?
							
		
					$sql_pop="select  c.po_number,a.po_break_down_id, sum(a.production_quantity)  as good_qnty from pro_garments_production_mst a, wo_po_break_down c where a.production_type=4 and a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.") and a.production_date='".$production_date."' and a.sewing_line=".$sewing_line." and a.floor_id=".$floor." group by c.po_number,a.po_break_down_id  order by  c.po_number ";
				
				//and a.production_date='".$production_date."'
				//echo $sql_pop;die;	
					$sql_result=sql_select($sql_pop);
					$k=1;$total_amount=0;$total_prod_qty=0;
					foreach($sql_result as $row)
					{
						  if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
					   ?>
						  <tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>
				
							<td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  number_format($row[csf('good_qnty')],2); ?></td> 
						</tr>
						<?
						$total_prod_qty+=$row[csf('good_qnty')];
						$k++;
	                }
					?>
	                <tr class="tbl_bottom" >
	                <td colspan="2"> Total </td>
	         
	                 <td align="right"> <? echo number_format($total_prod_qty,2);?></td>
	                </tr>
	          	</table>

	                <?
							
		
					$sql_color_size="select  c.color_number_id,c.size_number_id ,a.po_break_down_id, sum(b.production_qnty)  as good_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type=4 and a.production_type=4 and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.") and a.production_date='".$production_date."'  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.color_number_id,c.size_number_id ,a.po_break_down_id  order by  a.po_break_down_id ";
				
				//echo $sql_color_size;die;	
					$size_arr=array();
					$order_color_arr=array();
					$grand_size_arr=array();
					$grand_total=0;
					$color_size_qty_arr=array();
					$sql_color_size_result=sql_select($sql_color_size);
					foreach($sql_color_size_result as $cs_val)
					{
						$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
						$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						
						$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]]+=$cs_val[csf('good_qnty')];
						
						$grand_size_arr[$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						$grand_total+=$cs_val[csf('good_qnty')];
					}
				
				$input_width=450+count($size_arr)*50;
					?>
	                <table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Color Size Breakdown</strong></caption>
	                <thead>
	                	<th width="30">SL</th>
	                    <th width="180">Order No</th>
	                    <th width="150">Color</th>
	               		<?php 
						foreach($size_arr as $sid)
						{
						?>
	                    	<th width="70"><?php echo $itemSizeArr[$sid] ;?></th>
	                    <?php
						}
						?>
	                    <th width="100">Color Total</th>
					</thead>
	                </table>
	                <table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center" id="table_body">
	                
	                <?php
					$r=1;
					foreach($color_size_qty_arr as $po_id=>$po_value)
					{
						
						foreach($po_value as $color_id=>$color_value)
						{
						  	if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						  	?>
	                         	<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
	                                <td width="30" rowspan="<?php // echo $order_color_arr[$po_id]; ?> "><? echo $r; ?></td>
	                                <td width="180" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $po_number_Arr[$po_id]; ?></td>
	                    
	                                <td width="150" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo  $colorArr[$color_id]; ?></td> 			<?php 
									foreach($size_arr as $sid)
									{
									?>
										<td width="70" align="right"><?php echo $color_value[$sid] ;?></td>
									<?php
									}
									?>
	                                <td width="100" align="right"><?php echo $order_color_arr[$po_id][$color_id] ;?></td>
	                        	</tr>
							<?
							$r++;
						}
	                }
					?>
	                <tr class="tbl_bottom" >
	                    <td colspan="3"> Total </td>
	             
	                    <?php 
	                    foreach($size_arr as $sid)
	                    {
	                    ?>
	                        <td width="70" align="right"><?php echo $grand_size_arr[$sid] ;?></td>
	                    <?php
	                    }
	                    ?>
	                    <td width="100" align="right"><?php echo $grand_total ;?></td>
	                </tr>
	          	</table>
	         </div>
	          <script>
	 		setFilterGrid("table_body",-1);
	 		</script>
	     <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>
	                
	                
		<?
	}
	else if($type==5)
	{
		?>
		
	    <fieldset style="width:1000px; ">
			<div style="width:500px;" align="center">
	        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
	        </div>
			<div id="report_container" align="center">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Output Details</strong></caption>
	                <thead>
	                	<th width="30">SL</th>
	                    <th width="120">Order No</th>
	               
	                    <th width="100">Output Qty.</th>
					</thead>
	                </table>
	                <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
	                <?
							
		
					$sql_pop="select  c.po_number,a.po_break_down_id, sum(a.production_quantity)  as good_qnty from pro_garments_production_mst a, wo_po_break_down c where a.production_type=5 and a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.production_date='".$production_date."' and a.floor_id=".$floor." group by c.po_number,a.po_break_down_id  order by  c.po_number ";
					
					$sql_result=sql_select($sql_pop);
					$k=1;$total_amount=0;$total_prod_qty=0;
					foreach($sql_result as $row)
					{
						  if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
					   ?>
						  <tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>
				
							<td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  number_format($row[csf('good_qnty')],2); ?></td> 
						</tr>
						<?
						$total_prod_qty+=$row[csf('good_qnty')];
						$k++;
	                }
					?>
	                <tr class="tbl_bottom" >
	                <td colspan="2"> Total </td>
	         
	                 <td align="right"> <? echo number_format($total_prod_qty,2);?></td>
	                </tr>
	          	</table>
	                <?
							
		
					$sql_color_size="select  c.color_number_id,c.size_number_id ,a.po_break_down_id, sum(b.production_qnty)  as good_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type=5 and a.production_type=5 and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.") and a.production_date='".$production_date."' and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.color_number_id,c.size_number_id ,a.po_break_down_id  order by  a.po_break_down_id ";
				
				//echo $sql_color_size;	
					$size_arr=array();
					$order_color_arr=array();
					$grand_size_arr=array();
					$grand_total=0;
					$color_size_qty_arr=array();
					$sql_color_size_result=sql_select($sql_color_size);
					foreach($sql_color_size_result as $cs_val)
					{
						$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
						$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						
						$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]]+=$cs_val[csf('good_qnty')];
						
						$grand_size_arr[$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						$grand_total+=$cs_val[csf('good_qnty')];
					}
				
				$input_width=450+count($size_arr)*50;
					?>
	                <table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Output Color Size Breakdown</strong></caption>
	                <thead>
	                	<th width="30">SL</th>
	                    <th width="180">Order No</th>
	                    <th width="150">Color</th>
	               		<?php 
						foreach($size_arr as $sid)
						{
						?>
	                    	<th width="70"><?php echo $itemSizeArr[$sid] ;?></th>
	                    <?php
						}
						?>
	                    <th width="100">Color Total</th>
					</thead>
	                </table>
	                <table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center" id="table_body">
	                
	                <?php
					$r=1;
					foreach($color_size_qty_arr as $po_id=>$po_value)
					{
						
						foreach($po_value as $color_id=>$color_value)
						{
						  	if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						  	?>
	                         	<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
	                                <td width="30" rowspan="<?php // echo $order_color_arr[$po_id]; ?> "><? echo $r; ?></td>
	                                <td width="180" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $po_number_Arr[$po_id]; ?></td>
	                    
	                                <td width="150" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo  $colorArr[$color_id]; ?></td> 			<?php 
									foreach($size_arr as $sid)
									{
									?>
										<td width="70" align="right"><?php echo $color_value[$sid] ;?></td>
									<?php
									}
									?>
	                                <td width="100" align="right"><?php echo $order_color_arr[$po_id][$color_id] ;?></td>
	                        	</tr>
							<?
							$r++;
						}
	                }
					?>
	                <tr class="tbl_bottom" >
	                    <td colspan="3"> Total </td>
	             
	                    <?php 
	                    foreach($size_arr as $sid)
	                    {
	                    ?>
	                        <td width="70" align="right"><?php echo $grand_size_arr[$sid] ;?></td>
	                    <?php
	                    }
	                    ?>
	                    <td width="100" align="right"><?php echo $grand_total ;?></td>
	                </tr>
	          	</table>
	         </div>
	          <script>
	 		setFilterGrid("table_body",-1);
	 		</script>
	     <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>
	                
	                
		<?
	}
	exit();
}

if($action=="tot_smv_used")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$fstinput_date=$prod_type;
	
	$prod_resource_array=array();
	
	$dataArray=sql_select("select b.pr_date, b.man_power, b.working_hour, b.smv_adjust, b.smv_adjust_type from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.id='$sewing_line'");

	foreach($dataArray as $row)
	{
		$prod_resource_array[$row[csf('pr_date')]]['smv']=$row[csf('man_power')]*$row[csf('working_hour')]*60;
		$prod_resource_array[$row[csf('pr_date')]]['mp']=$row[csf('man_power')];
		$prod_resource_array[$row[csf('pr_date')]]['wh']=$row[csf('working_hour')];
		$prod_resource_array[$row[csf('pr_date')]]['smv_adjust']=$row[csf('smv_adjust')];
		$prod_resource_array[$row[csf('pr_date')]]['smv_adjust_type']=$row[csf('smv_adjust_type')];
	}
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:680px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="660" cellpadding="0" cellspacing="0">
				<thead>
                	<th width="40">SL</th>
                    <th width="90">Production Date</th>
                    <th width="70">Manpower</th>
                    <th width="80">Working Hour</th>
                    <th width="80">SMV</th>
                    <th width="80">Adj. Type</th>
                    <th width="80">Adj. SMV</th>
                    <th>Actual SMV</th>
				</thead>
             </table>
             <div style="width:678px; max-height:280px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="660" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_smv_used=0;
                    $sql="select b.date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id='$company_id' and b.date_calc between '$fstinput_date' and '$prod_date' and day_status=1";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    	
						$total_adjustment=0;
						$smv_adjustmet_type=$prod_resource_array[$row[csf('date_calc')]]['smv_adjust_type'];
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$row[csf('date_calc')]]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$row[csf('date_calc')]]['smv_adjust'])*(-1);
						
						$day_smv=$prod_resource_array[$row[csf('date_calc')]]['smv']+$total_adjustment;
                        $total_smv_used+=$day_smv;
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="90" align="center"><? echo change_date_format($row[csf('date_calc')]); ?></td>
                            <td width="70" align="center"><? echo $prod_resource_array[$row[csf('date_calc')]]['mp']; ?>&nbsp;</td>
                            <td width="80" align="center"><? echo $prod_resource_array[$row[csf('date_calc')]]['wh']; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo $prod_resource_array[$row[csf('date_calc')]]['smv']; ?>&nbsp;</td>
                            <td width="80" align="center"><? echo $increase_decrease[$prod_resource_array[$row[csf('date_calc')]]['smv_adjust_type']]; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo $prod_resource_array[$row[csf('date_calc')]]['smv_adjust']; ?>&nbsp;</td>
                            <td align="right"><? echo number_format($day_smv,2); ?>&nbsp;</td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_smv_used,2); ?>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>     
<?
exit();
}



if($action=="tot_fob_value_popup")
{
	echo load_html_head_contents("FOB Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	
     <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			$("#table_body tr:first").hide();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			$("#table_body tr:first").show();
		}	
	</script>	
    <fieldset style="width:500px; ">
    	<div style="width:500px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
        </div>
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
				<caption><strong>FOB Value </strong></caption>
                <thead>
                	<th width="30">SL</th>
                    <th width="120">Order No</th>
                    <th width="120">Item</th>
                    <th width="80">Prod. Qnty</th>
                    <th width="60">Unit Price</th> 
                    <th width="100">Amount</th>
				</thead>
                </table>
                <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
                <?
						$sql_item_rate="select b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and c.po_break_down_id in(".$po_id.") ";
						$resultRate=sql_select($sql_item_rate);
						$item_po_array=array();
						foreach($resultRate as $row)
						{
							$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
							$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
						}
	
						$sql_pop=("select  c.po_number,a.po_break_down_id,a.item_number_id,avg(c.unit_price) as unit_price,
		                sum(CASE WHEN  a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty 
						from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.serving_company in(".$company_id.")  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."' group by c.po_number,a.po_break_down_id,a.item_number_id  order by  c.po_number ");
						//echo $sql_pop;die;
						$sql_result=sql_select($sql_pop);
						$k=1;$total_amount=0;$total_prod_qty=0;
					  foreach($sql_result as $row)
					   {
					   if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						$po_amount=$item_po_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['amt'];
						$po_qty=$item_po_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['qty'];
						//echo $po_amount.'=='.$po_qty.'<br>';
						$fob_rate=$po_amount/$po_qty;
			   ?>
                  <tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
					<td width="30"><? echo $k; ?></td>
					<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>
                    <td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo number_format($row[csf('good_qnty')],0); ?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo number_format($fob_rate,6);?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  number_format($row[csf('good_qnty')]*$fob_rate,2); ?></td> 
                </tr>
                <?
				$total_amount+=$row[csf('good_qnty')]*$fob_rate;
				$total_prod_qty+=$row[csf('good_qnty')];
				$k++;
                  }
				?>
                <tr class="tbl_bottom" >
                <td colspan="3"> Total </td>
                 <td align="right"> <? echo number_format($total_prod_qty);?> </td>
                 <td> </td>
                 <td align="right"> <? echo number_format($total_amount,2);?></td>
                </tr>
                </table>
         </div>
          <script>
 		setFilterGrid("table_body",-1);
 		</script>
     <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</fieldset>
                
                
<?
	exit();
}

if($action=="show_style_line_generate_report")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
	<div style="width:1080px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1070px; margin-left:5px">
		<div id="report_container" >
        <table border="1" class="rpt_table" rules="all" width="1070" cellpadding="0" cellspacing="0">
        
        <caption> <strong>Style Details</strong></caption>
        <?
		$buyerArr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
        $sqlPo="select a.job_no,a.buyer_name,a.set_smv,b.po_number,b.id as po_id from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and b.id in(".$po_id.") and a.style_ref_no='$style'";
		$po_no='';$po_ids='';
		$dataPo=sql_select($sqlPo);
		foreach( $dataPo as $row)
		{
			if($po_no!='') $po_no.=",".$row[csf('po_number')];else $po_no=$row[csf('po_number')];
			if($po_ids!='') $po_ids.=",".$row[csf('po_id')];else $po_ids=$row[csf('po_id')];
			
			//if($po_ids!='') $po_ids.=",".$row[csf('po_id')];else $po_ids=$row[csf('po_id')];
			
			$set_smv=$row[csf('set_smv')];
			$job_no=$row[csf('job_no')];
			//echo $row[csf('buyer_name')];
			$buyer_name=$buyerArr[$row[csf('buyer_name')]];
			
		}
		//echo $buyerArr[$buyer_name];
		//$buyerArr
		$germents_id=array_unique(explode(",",$item_id));
		$item_name='';
		foreach($germents_id as $g_val)
		{
			//$item_name=$garments_item[$g_val];
			if($item_name!='') $item_name.=",".$garments_item[$g_val];else $item_name=$garments_item[$g_val];
		}
		//garments_item
		?>
        <tr>
             <td width="50"> Buyer</td> <td width="100">  <? echo $buyer_name;?></td>
             <td width="70"> Order No</td> <td width="100"> <? echo $po_no;?></td>
             <td width="100"> Style Ref</td> <td width="100"> <? echo $style;?></td>
             <td width="100"> Garments Item</td> <td> <? echo $item_name;?></td>
             <td width="50"> SMV</td> <td width="60">  <? echo $set_smv;?></td>
        </tr>
        </table>
			<table border="1" class="rpt_table" rules="all" width="1070" cellpadding="0" cellspacing="0">
           
                <?
				
				if($db_type==0)
				{
					$dataArray=sql_select("select TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( d.prod_start_time, '%H' ) as start_hour, TIME_FORMAT( d.prod_start_time, '%i' ) as start_min from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and d.shift_id=1 and b.pr_date='$prod_date' and a.id='$sewing_line'");
					$prod_start_hour=$dataArray[0][csf('prod_start_time')];
					if($prod_start_hour=="") $prod_start_hour="08:00";
					$start_time=explode(":",$prod_start_hour);
					$hour=$start_time[0]; $minutes=$start_time[1]; $last_hour=23;
					$start_hour_arr=array(); $s=1;
					
					$start_hour=$prod_start_hour;
					for($j=$hour;$j<$last_hour;$j++)
					{
						$start_hour=add_time($start_hour,60);
						$start_hour_arr[$j+1]=$start_hour;
					}
					$start_hour_arr[$j+1]='23:59:59';
					
					$sql="SELECT "; 
					foreach($start_hour_arr as $val)
					{
						$z++;
						if($s==1)
						{
							$sql.=" sum(case when production_hour<='$val' then production_quantity else 0 end) AS am$z ";
						}
						else
						{
							$sql.=", sum(case when production_hour>'$prev_hour' and production_hour<='$val' then production_quantity else 0 end) AS am$z ";
						}
						
						$prev_hour=$val;
						$s++;
					}
					
					$sql.="from pro_garments_production_mst where po_break_down_id in(".$po_ids.") and item_number_id in($item_id) and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and production_type=5 and production_date='$prod_date' and is_deleted=0 and status_active=1";
				
				}
				else
				{
				
									
					$dataArray=sql_select("select TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(d.prod_start_time,'HH24') as start_hour, TO_CHAR(d.prod_start_time,'MI') as start_min, TO_CHAR(d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and d.shift_id=1 and b.pr_date='$prod_date' and a.id='$sewing_line'");
					$prod_start_hour=$dataArray[0][csf('prod_start_time')];
					if($prod_start_hour=="") $prod_start_hour="08:00";
					$start_time=explode(":",$prod_start_hour);
					$hour=$start_time[0]; $minutes=$start_time[1]; $last_hour=23;
					$start_hour_arr=array(); $s=1;
					
					$start_hour=$prod_start_hour;
					for($j=$hour;$j<$last_hour;$j++)
					{
						$start_hour=add_time($start_hour,60);
						$start_hour_arr[$j+1]=$start_hour;
					}
					$start_hour_arr[$j+1]='23:59:59';
					
					$sql="SELECT "; 
					foreach($start_hour_arr as $val)
					{
						$z++;
						if($s==1)
						{
							$sql.="sum(case when TO_CHAR(production_hour,'HH24:MI:SS')<='$val' then production_quantity else 0 end) AS am$z ";
						}
						else
						{
							$sql.=", sum(case when TO_CHAR(production_hour,'HH24:MI:SS')>'$prev_hour' and TO_CHAR(production_hour,'HH24:MI:SS')<='$val' then production_quantity else 0 end) AS am$z ";
						}
						
						$prev_hour=$val;
						$s++;
					}
					
					$sql.="from pro_garments_production_mst where po_break_down_id in(".$po_ids.") and item_number_id in(".$item_id.") and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and production_type=5 and production_date='$prod_date' and is_deleted=0 and status_active=1";
					//echo $sql;
				}

				$result=sql_select($sql);
				foreach($result as $row);
				//$total_qnty=$row[csf('am1')]+$row[csf('am2')]+$row[csf('am3')]+$row[csf('am4')]+$row[csf('am5')]+$row[csf('am6')]+$row[csf('am7')]+$row[csf('am8')]+$row[csf('am9')]+$row[csf('am10')]+$row[csf('am11')]+$row[csf('pm12')]+$row[csf('pm13')]+$row[csf('pm14')]+$row[csf('pm15')]+$row[csf('pm16')]+$row[csf('pm17')]+$row[csf('pm18')]+$row[csf('pm19')]+$row[csf('pm20')]+$row[csf('pm21')]+$row[csf('pm22')]+$row[csf('pm23')]+$row[csf('pm24')];
				// bgcolor="#E9F3FF"
				echo '<thead><tr>';
				$x=1;
				foreach($start_hour_arr as $val)
				{
					if($x<13)
					{
						echo '<th width="70">'.$val.'</th>';
						$x++;
					}
				}
				echo '</tr></thead><tr bgcolor="#E9F3FF">';
				
				$x=1; $total_qnty=0;
				foreach($start_hour_arr as $val)
				{
					if($x<13)
					{
						echo '<td width="70" align="right">'.$row[csf('am'.$x)].'&nbsp;&nbsp;</td>';
						$total_qnty+=$row[csf('am'.$x)];
						$x++;
					}
				}
				echo '</tr>';

				array_splice($start_hour_arr,0, 12);
				$x=13;
				if(count($start_hour_arr)>0)
				{
					echo '<thead><tr>';
					foreach($start_hour_arr as $val)
					{
						echo '<th width="70">'.$val.'</th>';
						$x++;
					}
					$x=13;
					echo '</tr></thead><tr bgcolor="#E9F3FF">';
					foreach($start_hour_arr as $val)
					{
						echo '<td width="70" align="right">'.$row[csf('am'.$x)].'&nbsp;&nbsp;</td>';
						$total_qnty+=$row[csf('am'.$x)];
						$x++;
					}
					echo '</tr>';
				}
				?>
                <tr><td colspan="12"><strong>Total: &nbsp;&nbsp;<? echo  $total_qnty;?> </strong></td></tr>
			</table>
            <br>
            <table border="1" class="rpt_table" rules="all" style="width:auto" cellpadding="0" cellspacing="0">
            <?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
				$data_file=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=2");
			?>
            	<tr>
                <td width="60">  <b> Image </b></td>
                 <?
				foreach ($data_array as $row)
				{ 
				?>
				<td width="150"><a href="<? $row['image_location'] ?>" target="_new"><img src='../../../<? echo $row[csf('image_location')]; ?>' height='120' width='150' align="middle" /></a></td>
				<?
				}
				?>
                </tr>
                <tr>
                <td  width="60"> <b>File </b></td> 
             	  <?
					foreach ($data_file as $row)
					{ 
					?>
					<td><a href="../../../<? echo $row[csf('image_location')] ?>" target="_new"> 
						<img src="../../../file_upload/blank_file.png" width="80" height="60"> </a>
					</td>
					<?
					}
					?>
                </tr>
            </table>
            
        </div>
	</fieldset>   
<?
exit();
}
?>