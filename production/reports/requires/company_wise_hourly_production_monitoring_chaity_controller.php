<?
header('Content-type:text/html; charset=utf-8');
session_start();

//path daynamic.............................
$dirArr = explode(DIRECTORY_SEPARATOR , __FILE__);
$www_key = array_search ('www', $dirArr);
//$pth = $_SERVER['DOCUMENT_ROOT']."/".$dirArr[$www_key+1].'/includes/common.php';
$pth = '../../../includes/common.php';
include($pth);
//.............................path daynamic end; 



$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if($action=="load_drop_down_buyer")
{
	
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1
	and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in (".$data.") $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type 
	where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");  
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in (".$data.") 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/multi_company_wise_hourly_production_monitoring_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/multi_company_wise_hourly_production_monitoring_controller' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 80, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
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




if($action=="report_generate") 
{
 extract($_REQUEST);
 $companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 

 if($is_mail_send==1){

	$previous_date = change_date_format(date('d-M-Y', strtotime('-1 day', time())),'','',1); 
 
	$type=0;
	$cbo_company_id=implode(',',array_keys($companyArr));   
	$cbo_location_id='';
	$cbo_floor_id='';
	$cbo_line='';
	$hidden_line_id='';
	$cbo_buyer_name='0';
	 $txt_date="'{$previous_date}'"; 
	$txt_parcentage='60';
	$txt_file_no='';
	$txt_ref_no='';
	$cbo_no_prod_type='1';
	 $report_title='Hourly Production Monitoring Report';

 

	ob_start();
 }


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
	
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');
	
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
	//echo $actual_time;	
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
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['man_power']=$val[csf('man_power')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['operator']=$val[csf('operator')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['helper']=$val[csf('helper')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['terget_hour']=$val[csf('target_per_hour')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['working_hour']=$val[csf('working_hour')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['day_start']=$val[csf('from_date')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['day_end']=$val[csf('to_date')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['capacity']=$val[csf('capacity')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['smv_adjust']=$val[csf('smv_adjust')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['line_number']=$val[csf('line_number')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['pr_date']=$val[csf('pr_date')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['machine']=$val[csf('active_machine')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['line_chief']=$val[csf('line_chief')];

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

	$prod_start_time=sql_select("select $prod_start_cond  from variable_settings_production where company_name in($cbo_company_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	//echo "select company_name, prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
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
		$sql_item="select b.id, a.total_smv, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$resultItem=sql_select($sql_item);
	
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('total_smv')];
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
		$sql="select  c.job_no_mst as job_no, a.serving_company, a.location, a.floor_id, a.production_date, a.sewing_line,b.job_no, b.buyer_name  as buyer_name,b.style_ref_no,b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type in (4,5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond group by a.serving_company, a.location, a.floor_id,a.po_break_down_id, a.production_date,b.total_set_qnty, a.prod_reso_allo, a.sewing_line,b.job_no, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.unit_price, c.job_no_mst";
	}
	else if($db_type==2)
	{
		$sql="select  a.serving_company, a.location, a.floor_id, a.production_date, a.sewing_line,b.job_no,b.buyer_name  as buyer_name,b.style_ref_no, b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id, c.job_no_mst as job_no, c.po_number as po_number,c.unit_price,
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
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type in(4,5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond group by a.serving_company, a.location, a.floor_id,a.po_break_down_id, a.production_date, a.prod_reso_allo, a.sewing_line, b.job_no,b.total_set_qnty, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.job_no_mst, c.unit_price";
		
	}
	//echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$job_no_arr=array();
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
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')]; 
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

		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')];
		$job_no_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]=$val[csf('job_no')];
	}
	//echo "<pre>";
	//print_r($production_data_arr);
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
	
	
	
	$resout_input_output=sql_select("select 
			a.serving_company,
			a.location,
			a.floor_id,
			a.sewing_line,
			a.po_break_down_id,
			a.production_type,
			a.production_date,
			a.production_quantity
			
			from pro_garments_production_mst a
			where a.production_type in (5,4) and po_break_down_id in($all_po_id)  and  a.status_active=1 and a.is_deleted=0 $company_name"); 
		
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
			
			$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input']+=$i_val[csf('production_quantity')];
			
			if(change_date_format($i_val[csf('production_date')])==$search_prod_date)
			{
				$input_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]][change_date_format($i_val[csf('production_date')])]+=$i_val[csf('production_quantity')];
			}
		}
		else
		{
			$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['output']+=$i_val[csf('production_quantity')];
		}
	}
	
	//print_r($input_output_po_arr);
	
	// subcoutact data **********************************************************************************************************************
	
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
	
	//echo "<pre>";
	//print_r($production_data_arr);die;
	
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
	if ($type == 0) 
	{ 
		?>               
		<fieldset style="width:1290px">
		<table width="1290" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td colspan="27" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="27" align="center"><strong><? echo $report_title; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="27" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
				</tr>
			</table>
			<br />
			<table  width="400" cellpadding="0"  cellspacing="0" align="center" style="padding-left:100px">
				<tr>               
				
					<td bgcolor="#FFFF66" height="18" width="30" ></td>
					<td> &nbsp;Lunch Hour</td>
					<td bgcolor="red" height="18" width="30"></td>
					<td>&nbsp;Efficiency % less than Standard And Production less than Target</td>               
				
				</tr>
			</table>

			<!-- Calculation For header sum show  -->

			<?php
			foreach($prod_resource_array as $company_id=>$com_name)
			{
				$global_start_lanch=$start_time_arr[$company_id][1]['lst'];
				for($k=$hour; $k<=$last_hour; $k++)
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
					if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
				}
				foreach($com_name as $lo_id=>$lo_name)
				{
					ksort($lo_name);
					foreach($lo_name as $f_id=>$flr_data)
					{
						ksort($flr_data);
						foreach($flr_data as $sl_id=>$sl_data)
						{
							foreach($sl_data as $resource_id=>$resource_data)
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
								$today_input=0; $total_smv_achive=0;
								foreach($germents_item as $g_val)
								{
									
									$po_garment_item=explode('**',$g_val);
									if($garment_itemname!='') $garment_itemname.=',';
									$garment_itemname.=$garments_item[$po_garment_item[1]];
									
									if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
									
									$total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input'];
									$total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['output'];
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
									//echo $today_input;die;
									//echo $company_id."*".$f_id."*".$resource_id."*".$po_garment_item[0]."*".$search_prod_date;
									//print_r($input_po_arr);die;
									//echo $input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date]."**";
									//$today_input+=$input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date];
									//echo $today_input."2gsgd";die;
				
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
								//echo $today_input."gsdfgsg";die;
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

					
								//*************************************************************************************************************************************************
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
				
									
									//****************************************************************************************************************
									
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
									$floor_total_wip+=($total_input-$total_output);
									
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
									$grand_total_wip+=($total_input-$total_output);
									$grand_cm_value+=$line_cm_value;
									
									
									$company_today_input+=$today_input;
									$company_total_input+=$total_input;
									$company_total_output+=$total_output;
									$company_total_wip+=($total_input-$total_output);
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
									$string="'";
																									
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
									}									
									if($line_efficiency<=$txt_parcentage)
									{}
									else
									{
									}
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
									//echo $today_input;die;
									$floor_cm_value+=$line_cm_value;
									$floor_total_input+=$total_input;
									$floor_total_output+=$total_output;
									$floor_total_wip+=($total_input-$total_output);
									
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
									$grand_total_wip+=($total_input-$total_output);
									$grand_cm_value+=$line_cm_value;
									
									$company_today_input+=$today_input;
									$company_total_input+=$total_input;
									$company_total_output+=$total_output;
									$company_total_wip+=($total_input-$total_output);
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
									$string="'";
									
									for($k=$hour; $k<=$last_hour; $k++)
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
									}
									if($line_efficiency<=$txt_parcentage)
									{}
									else
									{
									}
								$i++;
								}
								//echo $floor_cm_value."***";die;
							}
						}
						if($cbo_no_prod_type==2 && $line_floor_production>0)
						{
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
							}
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
							}  
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

			}

			?>

			<style type="text/css">
				#talbe_1_gt{
					background: #bababa;
				}
				#talbe_1_gt tr th{
					border: 1px solid black!important;
				}
				.wrd_brk{
					word-break: break-all;
				}
			</style>

			<table  id="talbe_1_gt" width="1550" cellpadding="0"  cellspacing="0" border="1" rules="all">
				<tr>              
					<th width="35"></th>
					<th width="60"></th>
					<th width="110"></th>
					<th width="100"></th>
					<th width="70">Total</th>
					<th align="center" width="50"><?  echo $gnd_total_tgt_h; ?>&nbsp;</th>                
					
					<?
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($k==$last_hour) $hourwidth='40'; else $hourwidth='40';
						?>
							<th align="center" width="<?php echo $hourwidth;?> "><? echo $total_production[$prod_hour]; ?></th>
						<?	
					}
					?>               
					
					<th align="center" width="50"><? echo $grand_total_terget; ?>&nbsp;</th>
					<th align="center" width="50"><? echo $grand_today_input; ?>&nbsp;</th>
					<th align="center" width="50"><? echo $line_total_production; ?>&nbsp;</th>
					<th align="center" width="50"><? echo number_format(($line_total_production/$grand_total_terget)*100,2)."%"; ?>&nbsp;</th>
					<th align="center" width="55" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?>&nbsp;</th> 
					<th width="">&nbsp;</th>         
				</tr>
			</table>
		

			<table id="table_header_1" class="rpt_table" width="1550" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr height="50">
						<th width="35">Line No</th>
						<th width="60">Buyer</th>
						<th width="110">Syle Name</th>
						<th width="100">Order No</th>
						<th width="70">Picture</th>
						<th width="50">Hourly Target</th>                    

						<?				
						for($k=$hour+1; $k<=$last_hour+1; $k++)
						{
							//if($k==$last_hour+1) $hourwidth=''; else $hourwidth='50';
						?>
							<th width="40" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
						<?	
						}
						?>                    
						<th width="50">Today Target</th>
						<th width="50">Today Input</th>
						<th width="50">Today Prod.</th>
						<th width="50">Today Achv %</th>
						<th width="55">Today L. Effi %</th>                    
						<th width=""> Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:1570; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="1550" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
					<?	
					foreach($prod_resource_array as $company_id=>$com_name)
					{
						$global_start_lanch=$start_time_arr[$company_id][1]['lst'];
						?>
						<tr bgcolor="#E8FFFF">
							<td width="" colspan="6"><strong>Company Name:<? echo $companyArr[$company_id]?></strong></td>
							<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								if($k==$last_hour) $hourwidth=''; else $hourwidth='40';?>
								<td align="right" width=<? echo $hourwidth;?> style=<? echo $bg_color; ?>></td>
							<?	
							}
							?>	
							<td width="" colspan="6"></td>								
						</tr>
				
						<?
						foreach($com_name as $lo_id=>$lo_name)
						{
							ksort($lo_name);
							foreach($lo_name as $f_id=>$flr_data)
							{
								?>
								<tr bgcolor="#E8FFFF">
									<td width="" colspan="6"><strong>Floor Name:<? echo $floorArr[$f_id];?></strong></td>
									<?
									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										if($k==$last_hour) $hourwidth=''; else $hourwidth='40';?>
										<td align="right" width=<? echo $hourwidth;?> style=<? echo $bg_color; ?>></td>
									<?	
									}
									?>
									<td width="" colspan="6"></td>
								</tr>
								<?
								ksort($flr_data);
								foreach($flr_data as $sl_id=>$sl_data)
								{
			
									ksort($sl_data);
									foreach($sl_data as $resource_id=>$resource_data)
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
										$today_input=0; $total_smv_achive=0;
										foreach($germents_item as $g_val)
										{
											
											$po_garment_item=explode('**',$g_val);
											if($garment_itemname!='') $garment_itemname.=',';
											$garment_itemname.=$garments_item[$po_garment_item[1]];
											
											if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
											
											$total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input'];
											$total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['output'];
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

							
										//*************************************************************************************************************************************************
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
										
										
										//print_r($production_hour);die;
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
											//echo $total_eff_hour.'aaaa';die;
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
						
											
											//****************************************************************************************************************
											
											//echo $today_input;die;
											
											$man_power=$resource_data['man_power'];	
											$operator=$resource_data['operator'];
											$helper=$resource_data['helper'];
											$terget_hour=$resource_data['terget_hour'];	
											$capacity=$resource_data['capacity'];
											$working_hour=$resource_data['working_hour'];
											//echo $working_hour."System";die;
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
											$floor_total_wip+=($total_input-$total_output);
											
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
											$grand_total_wip+=($total_input-$total_output);
											$grand_cm_value+=$line_cm_value;
											
											
											$company_today_input+=$today_input;
											$company_total_input+=$total_input;
											$company_total_output+=$total_output;
											$company_total_wip+=($total_input-$total_output);
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
											?>
											<tr bgcolor=<? echo $bgcolor;?> onclick=change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>') id=tr_<? echo $i;?>>
											<td align="center" width="35" class="wrd_brk"><? echo $sewing_line;?>&nbsp; </td>
											<td width="60" align="center" class="wrd_brk"><p><? echo $buyer_name;?>&nbsp;</p></td>
											<td width="110" align="center" class="wrd_brk"><p><?=$style;?></td>
											<td width="100" align="center" class="wrd_brk"><p><? echo $production_data_arr[$f_id][$resource_id]['po_number'];?>&nbsp;</p></td>
											<td width="70" onclick="openmypage_image('requires/company_wise_hourly_production_monitoring_chaity_controller.php?action=show_image&job_no=<? echo $job_no;?>','Image View')">
												<img src="../../<?echo $imge_arr[$production_data_arr[$f_id][$resource_id]['job_no']];?>" height="30" width="40" />
											</td>

											<td align="center" width="50"><? echo $terget_hour;?></td>

											<?															
											
											$string="'";
																											
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
									
											?>
												
												<td align="center" width="40"  style=<? echo $bg_color;?>><? echo $production_hour[$prod_hour];?></td>
											<?
											}

											?>
											
											<td align="center" width="50"><? echo $eff_target;?></td>
											<td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'4','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $today_input;?></a></td>
											<td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'5','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $line_production_hour;?></a></td>
											<?
											$today_achive = ($line_production_hour/$eff_target)*100;
											//$today_achive = number_format(($line_production_hour/$eff_target)*100,2);
											if($today_achive<=$txt_parcentage)
											{?>
												<td align="center" width="50" bgcolor="red"><? echo number_format($today_achive,2).'%'; ?></td>
											<?}
											else
											{?>
												<td align="center" width="50" bgcolor="green"><? echo number_format($today_achive,2).'%'; ?></td> 
											<?
											}
											if($line_efficiency<=$txt_parcentage)
											{?>
												<td class="wrd_brk" align="center" width="55" bgcolor="red"><? echo number_format($line_efficiency,2).'%'?></td>
											<?}
											else
											{?>
												<td class="wrd_brk" align="center" width="55" bgcolor="green"><? echo number_format($line_efficiency,2).'%'?></td>	
											<?
											}
											?>
											<td align="center" width="55"><? echo "23edf";?></td>
											<td  class="wrd_brk"><input type="button"  value="View" class="formbutton" onclick="show_line_remarks(<? echo $cbo_company_id;?>,'<? echo $order_no_total;?>','<? echo $f_id;?>','<? echo $resource_id;?>','remarks_popup',<? echo $txt_date;?>)"/></td>	                                
										
											</tr>
										<?
										$i++;
										}
										if($cbo_no_prod_type==1)
										{
											//echo "system";die;
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
											// count = 0;
											// if($working_hour!=0){
											// 	count++;
											// }
											// echo $count."System";die;
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
											$floor_total_wip+=($total_input-$total_output);
											
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
											$grand_total_wip+=($total_input-$total_output);
											$grand_cm_value+=$line_cm_value;
											
											$company_today_input+=$today_input;
											$company_total_input+=$total_input;
											$company_total_output+=$total_output;
											$company_total_wip+=($total_input-$total_output);
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
											
											$job_no=$production_data_arr[$f_id][$resource_id]['job_no'];
											$job_no2_arr=$job_no_arr[$f_id][$resource_id];
											$job_no2 = implode(',', $job_no2_arr);
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
											?>
											
											<tr bgcolor='<? echo $bgcolor;?>' onclick=change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>') id=tr_<? echo $i;?>>
											<td align="center" width="35" class="wrd_brk"><? echo $sewing_line;?>&nbsp;</td>
											<td width="40" align="center" class="wrd_brk"><p><? echo $buyer_name;?>&nbsp;</p></td>
											<td width="110" align="center" class="wrd_brk"><p><?=$style;?></p></td>
											<td width="100" align="center" class="wrd_brk"><p><? echo $production_data_arr[$f_id][$resource_id]['po_number'];?>&nbsp;</p></td>
											<td width="70" onclick="openmypage_image('requires/company_wise_hourly_production_monitoring_chaity_controller.php?action=show_image&job_no=<? echo $job_no2;?>','Image View')">
												<img src="../../<? echo $imge_arr[$job_no]; ?>" height="30" width="40" />
											</td>
											<td align="center" width="50"><? echo $terget_hour;?></td>
											
											<?

											$string="'";
											for($k=$hour; $k<=$last_hour; $k++)
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

												?>
												<td align="center" width="40"  style='<? echo $bg_color;?>'><? echo $production_hour[$prod_hour];?></td>
											<?
											}
											//echo "<pre>";print_r($production_hour);
											?>
											
											<td align="center" width="50"><? echo $eff_target;?></td>

											<td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'4','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $today_input;?></a></td>								    

											<td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'5','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $line_production_hour;?></a></td>
											<?
											$today_achive = ($line_production_hour/$eff_target)*100;
											//$today_achive = number_format(($line_production_hour/$eff_target)*100,2);
											if($today_achive<=$txt_parcentage)
											{?>
												<td align="center" width="50" bgcolor="red"><? echo number_format($today_achive,2).'%'; ?></td>
											<?}
											else
											{?>
												<td align="center" width="50" bgcolor="green"><? echo number_format($today_achive,2).'%'; ?></td> 
											<?
											}
											if($line_efficiency<=$txt_parcentage)
											{?>
												<td class="wrd_brk" align="center" width="55" bgcolor="red"><? echo number_format($line_efficiency,2).'%'?></td>
											<?}
											else
											{?>
												<td class="wrd_brk" align="center" width="55" bgcolor="green"><? echo number_format($line_efficiency,2).'%'?></td>	
											<?
											}
											?>
											<td class="wrd_brk"><input type="button"  value="View" class="formbutton" onclick="show_line_remarks(<? echo $cbo_company_id;?>,'<? echo $order_no_total;?>','<? echo $f_id;?>','<? echo $resource_id;?>','remarks_popup',<? echo $txt_date;?>)"/></td>
											
											</tr>
										<?    
										$i++;
										}
										//echo $floor_cm_value."***";die;
									}
									//l++;
								}
								if($cbo_no_prod_type==2 && $line_floor_production>0)
								{?>
									<tr  bgcolor="#B6B6B6">
									<td width="35">&nbsp;</td>
									<td width="60">&nbsp;</td>
									<td width="110"></td>
									<td width="100">&nbsp;</td>
									<td width="70">&nbsp;</td>

									<td align="center" width="50"><? echo $floor_tgt_h;?></td>																			
									
									<?
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
							
										?>
									
										<td align="center" width="40" style='<? echo $bg_color;?>' ><? echo $floor_production[$prod_hour];?></td>
									<?
									}
									?>								
									
									<td align="center" width="50"><? echo $eff_target_floor;?></td>
									<td align="center" width="50"><? echo $floor_today_input;?></td>
									<td align="center" width="50"><? echo $line_floor_production;?></td>
									<td align="center" width="50"><? echo number_format(($line_floor_production/$eff_target_floor)*100,2).'%';?></td>
									<td align="center" width="55"><? echo number_format($floor_efficency,2).'%'; ?></td>
									<td align="center" width=""></td>
									</tr>
									<?
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
								{?>
									<tr  bgcolor="#B6B6B6">
									<td width="35">&nbsp;</td>
									<td width="60">&nbsp;</td>
									<td width="110"></td>
									<td width="100">&nbsp;</td>
									<td width="70">&nbsp;</td>


									<td align="center" width="50"><? echo $floor_tgt_h;?></td>															

									<?
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
										?>
									
										<td align="center" width="40" style='<? echo $bg_color;?>' ><? echo $floor_production[$prod_hour];?></td>
									<?	
									}
									?>								
									
									<td align="center" width="50"><? echo $eff_target_floor;?></td>
									<td align="center" width="50"><? echo $floor_today_input;?></td>
									<td align="center" width="50"><? echo $line_floor_production;?></td>
									<td align="center" width="50"><? echo number_format(($line_floor_production/$eff_target_floor)*100,2).'%';?></td>
									<td align="center" width="55"><? echo number_format($floor_efficency,2).'%'; ?></td>
									<td align="center" width=""></td>
									</tr>
									<?    
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

					}
					?>		
										
					</tbody>

					<? $smv_for_item="";?>                
					
				</table>
			</div>
		</fieldset>  
	
	<?	
	}



	if($is_mail_send==1){
		$htmlBody = ob_get_contents();
		ob_clean();
	 
		 include(base_path('auto_mail/setting/mail_setting.php'));
	 
	 
		$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=119 and b.mail_user_setup_id=c.id and a.IS_DELETED=0 and b.IS_DELETED=0 and c.IS_DELETED=0 and A.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and c.STATUS_ACTIVE=1";
		$mail_sql=sql_select($sql);
		$mailArr=array();
		foreach($mail_sql as $row)
		{
			if ($row['EMAIL_ADDRESS']==""){$mailArr[$row['EMAIL_ADDRESS']]=$row['EMAIL_ADDRESS']; }
		}
		$to = implode(',',$mailArr);	
	
		$subject="Hourly Production Monitoring Report";

		
		$header=mailHeader();

		if($_REQUEST['isview']==1){
			$mail_item=119;
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $htmlBody;
		}
		else{
			echo sendMailMailer( $to, $subject, $htmlBody, $from_mail );
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
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$type";
	exit();      

}

if($action=="report_generate2") 
{
 extract($_REQUEST);
 $companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
//  echo "kamrul";

 if($is_mail_send==1){

	$previous_date = change_date_format(date('d-M-Y', strtotime('-1 day', time())),'','',1); 
	
	$cbo_company_id=implode(',',array_keys($companyArr));   
	$cbo_location_id='';
	$cbo_floor_id='';
	$cbo_line='';
	$hidden_line_id='';
	$cbo_buyer_name='0';
	 $txt_date="'{$previous_date}'"; 
	$txt_parcentage='60';
	$txt_file_no='';
	$txt_ref_no='';
	$cbo_no_prod_type='1';
	$cbo_shift_name='';
	 $report_title='Hourly Production Monitoring Report';

 

	ob_start();
 }


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
	
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');
	
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
	//echo $actual_time;	
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

	$cbo_shift_name=str_replace("'","",$cbo_shift_name);
	if($cbo_shift_name=="") $shift_name_cond=""; else $shift_name_cond="and a.shift_name in(".$cbo_shift_name.")";

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

	$dataArray_sql=sql_select(" SELECT a.id,a.company_id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity, l.line_name, l.sewing_line_serial, b.line_chief, b.active_machine  from prod_resource_mst a left join lib_sewing_line l on a.line_number=cast(l.id as varchar2(100)),
			prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id in (".$comapny_id.") and b.pr_date=$txt_date and b.is_deleted=0 and c.is_deleted=0 $subcon_location $floor $resource_line order by a.company_id,a.line_marge desc, a.location_id,a.floor_id,l.sewing_line_serial");
	
	
	foreach($dataArray_sql as $val)
	{
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['man_power']=$val[csf('man_power')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['operator']=$val[csf('operator')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['helper']=$val[csf('helper')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['terget_hour']=$val[csf('target_per_hour')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['working_hour']=$val[csf('working_hour')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['day_start']=$val[csf('from_date')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['day_end']=$val[csf('to_date')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['capacity']=$val[csf('capacity')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['smv_adjust']=$val[csf('smv_adjust')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['line_number']=$val[csf('line_number')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['pr_date']=$val[csf('pr_date')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['machine']=$val[csf('active_machine')];
		$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('sewing_line_serial')]][$val[csf('id')]]['line_chief']=$val[csf('line_chief')];

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

	$prod_start_time=sql_select("select $prod_start_cond  from variable_settings_production where company_name in($cbo_company_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	//echo "select company_name, prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
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
		$sql_item="select b.id, a.total_smv, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$resultItem=sql_select($sql_item);
	
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('total_smv')];
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
		$sql="select  c.job_no_mst as job_no, a.serving_company, a.location, a.floor_id, a.production_date, a.sewing_line,b.job_no, b.buyer_name  as buyer_name,b.style_ref_no,b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id,c.po_number as po_number, c.grouping,c.unit_price,a.shift_name,
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type in (4,5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond $shift_name group by a.serving_company, a.location, a.floor_id,a.po_break_down_id, a.production_date,b.total_set_qnty, a.prod_reso_allo, a.sewing_line,b.job_no, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.grouping, c.unit_price, c.job_no_mst";
	}
	else if($db_type==2)
	{
		$sql="select  a.serving_company, a.location, a.floor_id, a.production_date, a.sewing_line,b.job_no,b.buyer_name  as buyer_name,b.style_ref_no, b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id, c.job_no_mst as job_no,  c.grouping, c.po_number as po_number,c.unit_price,a.shift_name,
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
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type in(4,5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond $shift_name_cond group by a.serving_company, a.location, a.floor_id,a.po_break_down_id, a.production_date, a.prod_reso_allo, a.sewing_line, b.job_no,b.total_set_qnty, b.buyer_name, b.style_ref_no, a.item_number_id, c.grouping, c.po_number, c.job_no_mst, c.unit_price,a.shift_name";
		
	}
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$job_no_arr=array();
	$shift_arr=array();
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
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')]; 
		}
		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['grouping']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['grouping']=$val[csf('grouping')];
		}
	 	else
		{
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

		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')];
		$job_no_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]=$val[csf('job_no')];
	}
	// echo "<pre>";
	// print_r($shift_arr);die;
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
	
	
	
	$resout_input_output=sql_select("select 
			a.serving_company,
			a.location,
			a.floor_id,
			a.sewing_line,
			a.po_break_down_id,
			a.production_type,
			a.production_date,
			a.production_quantity
			
			from pro_garments_production_mst a
			where a.production_type in (5,4) and po_break_down_id in($all_po_id)  and  a.status_active=1 and a.is_deleted=0 $company_name"); 
		
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
			
			$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input']+=$i_val[csf('production_quantity')];
			
			if(change_date_format($i_val[csf('production_date')])==$search_prod_date)
			{
				$input_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]][change_date_format($i_val[csf('production_date')])]+=$i_val[csf('production_quantity')];
			}
		}
		else
		{
			$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['output']+=$i_val[csf('production_quantity')];
		}
	}
	
	//print_r($input_output_po_arr);
	
	// subcoutact data **********************************************************************************************************************
	
    if($db_type==0)
    {
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.grouping, c.cust_style_ref,max(c.smv) as smv,
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
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.grouping, c.cust_style_ref,max(c.smv) as smv,
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
		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['grouping']!="")
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['grouping']=$subcon_val[csf('grouping')];  
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['grouping']=$subcon_val[csf('grouping')]; 
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
	
	//echo "<pre>";
	//print_r($production_data_arr);die;
	
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
	if ($type == '1') 
	{ 
		?>               
		<fieldset style="width:1290px">
		<table width="1290" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td colspan="27" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="27" align="center"><strong><? echo $report_title; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="27" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
				</tr>
			</table>
			<br />
			<table  width="400" cellpadding="0"  cellspacing="0" align="center" style="padding-left:100px">
				<tr>               
				
					<td bgcolor="#FFFF66" height="18" width="30" ></td>
					<td> &nbsp;Lunch Hour</td>
					<td bgcolor="red" height="18" width="30"></td>
					<td>&nbsp;Efficiency % less than Standard And Production less than Target</td>               
				
				</tr>
			</table>

			<!-- Calculation For header sum show  -->

			<?php
			foreach($prod_resource_array as $company_id=>$com_name)
			{
				$global_start_lanch=$start_time_arr[$company_id][1]['lst'];
				for($k=$hour; $k<=$last_hour; $k++)
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
					if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
				}
				foreach($com_name as $lo_id=>$lo_name)
				{
					ksort($lo_name);
					foreach($lo_name as $f_id=>$flr_data)
					{
						ksort($flr_data);
						foreach($flr_data as $sl_id=>$sl_data)
						{
							foreach($sl_data as $resource_id=>$resource_data)
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
								$today_input=0; $total_smv_achive=0;
								foreach($germents_item as $g_val)
								{
									
									$po_garment_item=explode('**',$g_val);
									if($garment_itemname!='') $garment_itemname.=',';
									$garment_itemname.=$garments_item[$po_garment_item[1]];
									
									if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
									
									$total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input'];
									$total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['output'];
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
									//echo $today_input;die;
									//echo $company_id."*".$f_id."*".$resource_id."*".$po_garment_item[0]."*".$search_prod_date;
									//print_r($input_po_arr);die;
									//echo $input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date]."**";
									//$today_input+=$input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date];
									//echo $today_input."2gsgd";die;
				
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
								//echo $today_input."gsdfgsg";die;
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

					
								//*************************************************************************************************************************************************
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
				
									
									//****************************************************************************************************************
									
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
									$floor_total_wip+=($total_input-$total_output);
									
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
									$grand_total_wip+=($total_input-$total_output);
									$grand_cm_value+=$line_cm_value;
									
									
									$company_today_input+=$today_input;
									$company_total_input+=$total_input;
									$company_total_output+=$total_output;
									$company_total_wip+=($total_input-$total_output);
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
									$string="'";
																									
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
									}									
									if($line_efficiency<=$txt_parcentage)
									{}
									else
									{
									}
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
									//echo $today_input;die;
									$floor_cm_value+=$line_cm_value;
									$floor_total_input+=$total_input;
									$floor_total_output+=$total_output;
									$floor_total_wip+=($total_input-$total_output);
									
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
									$grand_total_wip+=($total_input-$total_output);
									$grand_cm_value+=$line_cm_value;
									
									$company_today_input+=$today_input;
									$company_total_input+=$total_input;
									$company_total_output+=$total_output;
									$company_total_wip+=($total_input-$total_output);
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
									$string="'";
									
									for($k=$hour; $k<=$last_hour; $k++)
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
									}
									if($line_efficiency<=$txt_parcentage)
									{}
									else
									{
									}
								$i++;
								}
								//echo $floor_cm_value."***";die;
							}
						}
						if($cbo_no_prod_type==2 && $line_floor_production>0)
						{
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
							}
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
							}  
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

			}

			?>

			<style type="text/css">
				#talbe_1_gt{
					background: #bababa;
				}
				#talbe_1_gt tr th{
					border: 1px solid black!important;
				}
				.wrd_brk{
					word-break: break-all;
				}
			</style>

			<table  id="talbe_1_gt" width="1550" cellpadding="0"  cellspacing="0" border="1" rules="all">
				<tr>              
					<th width="35"></th>
					<th width="60"></th>
					<th width="110"></th>
					<th width="100"></th>
					<th width="70">Total</th>
					<th align="center" width="50"><?  echo $gnd_total_tgt_h; ?>&nbsp;</th>                
					
					<?
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($k==$last_hour) $hourwidth='40'; else $hourwidth='40';
						?>
							<th align="center" width="<?php echo $hourwidth;?> "><? echo $total_production[$prod_hour]; ?></th>
						<?	
					}
					?>               
					
					<th align="center" width="50"><? echo $grand_total_terget; ?>&nbsp;</th>
					<th align="center" width="50"><? echo $grand_today_input; ?>&nbsp;</th>
					<th align="center" width="50"><? echo $line_total_production; ?>&nbsp;</th>
					<th align="center" width="50"><? echo number_format(($line_total_production/$grand_total_terget)*100,2)."%"; ?>&nbsp;</th>
					<th align="center" width="55" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?>&nbsp;</th> 
					<th width="">&nbsp;</th>         
				</tr>

			</table>
			<table  width="1550" cellpadding="0"  cellspacing="0" border="1" rules="all">
				<tr>
					<th align="center" style="font-size: 15px;">Shift:<?=$shift_name[$sql_resqlt[0]['SHIFT_NAME']];?>
					</th>
				</tr>
			</table>
			
		

			<table id="table_header_1" class="rpt_table" width="1550" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr height="50">
						<th width="35">Line No</th>
						<th width="60">Buyer</th>
						<th width="110">Syle Name</th>
						<th width="100">Internal Ref.</th>
						<th width="70">Picture</th>
						<th width="50">Hourly Target</th>                    

						<?	
						$q = 1;			
						for($k=$hour+1; $k<=$last_hour+1; $k++)
						{
						
						?>
						
						<?	

								if($p <= 12)
								{
									if($p==12)
									{
										$time = '';
									}
									else
									{
										$time = substr($start_hour_arr[$k],0,5)."-".substr($start_hour_arr[$k+1],0,5);
									}
									?>
									<th width="40" ><?  echo $q. "HR"; ?></th>
									<?	
								}
								
								$q++;
						}
						?>                    
						<th width="50">Today Target</th>
						<th width="50">Today Input</th>
						<th width="50">Today Prod.</th>
						<th width="50">Today Achv %</th>
						<th width="55">Today L. Effi %</th>                    
						<th width=""> Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:1570; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="1550" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
					<?	
					foreach($prod_resource_array as $company_id=>$com_name)
					{
						$global_start_lanch=$start_time_arr[$company_id][1]['lst'];
						?>
						<tr bgcolor="#E8FFFF">
							<td width="" colspan="6"><strong>Company Name:<? echo $companyArr[$company_id]?></strong></td>
							<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								if($k==$last_hour) $hourwidth=''; else $hourwidth='40';?>
								<td align="right" width=<? echo $hourwidth;?> style=<? echo $bg_color; ?>></td>
							<?	
							}
							?>	
							<td width="" colspan="6"></td>								
						</tr>
				
						<?
						foreach($com_name as $lo_id=>$lo_name)
						{
							ksort($lo_name);
							foreach($lo_name as $f_id=>$flr_data)
							{
								?>
								<tr bgcolor="#E8FFFF">
									<td width="" colspan="6"><strong>Floor Name:<? echo $floorArr[$f_id];?></strong></td>
									<?
									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										if($k==$last_hour) $hourwidth=''; else $hourwidth='40';?>
										<td align="right" width=<? echo $hourwidth;?> style=<? echo $bg_color; ?>></td>
									<?	
									}
									?>
									<td width="" colspan="6"></td>
								</tr>
								<?
								ksort($flr_data);
								foreach($flr_data as $sl_id=>$sl_data)
								{
			
									ksort($sl_data);
									foreach($sl_data as $resource_id=>$resource_data)
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
										$today_input=0; $total_smv_achive=0;
										foreach($germents_item as $g_val)
										{
											
											$po_garment_item=explode('**',$g_val);
											if($garment_itemname!='') $garment_itemname.=',';
											$garment_itemname.=$garments_item[$po_garment_item[1]];
											
											if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
											
											$total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input'];
											$total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['output'];
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

							
										//*************************************************************************************************************************************************
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
										
										
										//print_r($production_hour);die;
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
											//echo $total_eff_hour.'aaaa';die;
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
						
											
											//****************************************************************************************************************
											
											//echo $today_input;die;
											
											$man_power=$resource_data['man_power'];	
											$operator=$resource_data['operator'];
											$helper=$resource_data['helper'];
											$terget_hour=$resource_data['terget_hour'];	
											$capacity=$resource_data['capacity'];
											$working_hour=$resource_data['working_hour'];
											//echo $working_hour."System";die;
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
											$floor_total_wip+=($total_input-$total_output);
											
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
											$grand_total_wip+=($total_input-$total_output);
											$grand_cm_value+=$line_cm_value;
											
											
											$company_today_input+=$today_input;
											$company_total_input+=$total_input;
											$company_total_output+=$total_output;
											$company_total_wip+=($total_input-$total_output);
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
											?>
											<tr bgcolor=<? echo $bgcolor;?> onclick=change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>') id=tr_<? echo $i;?>>
											<td align="center" width="35" class="wrd_brk"><? echo $sewing_line;?>&nbsp; </td>
											<td width="60" align="center" class="wrd_brk"><p><? echo $buyer_name;?>&nbsp;</p></td>
											<td width="110" align="center" class="wrd_brk"><p><?=$style;?></td>
											<td width="100" align="center" class="wrd_brk"><p><? echo $production_data_arr[$f_id][$resource_id]['grouping'];?>&nbsp;</p></td>
											<td width="70" onclick="openmypage_image('requires/company_wise_hourly_production_monitoring_chaity_controller.php?action=show_image&job_no=<? echo $job_no;?>','Image View')">
												<img src="../../<?echo $imge_arr[$production_data_arr[$f_id][$resource_id]['job_no']];?>" height="30" width="40" />
											</td>

											<td align="center" width="50"><? echo $terget_hour;?></td>

											<?															
											
											$string="'";
																											
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
									
											?>
												
												<td align="center" width="40"  style=<? echo $bg_color;?>><? echo $production_hour[$prod_hour];?></td>
											<?
											}

											?>
											
											<td align="center" width="50"><? echo $eff_target;?></td>
											<td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'4','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $today_input;?></a></td>
											<td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'5','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $line_production_hour;?></a></td>
											<?
											$today_achive = ($line_production_hour/$eff_target)*100;
											//$today_achive = number_format(($line_production_hour/$eff_target)*100,2);
											if($today_achive<=$txt_parcentage)
											{?>
												<td align="center" width="50" bgcolor="red"><? echo number_format($today_achive,2).'%'; ?></td>
											<?}
											else
											{?>
												<td align="center" width="50" bgcolor="green"><? echo number_format($today_achive,2).'%'; ?></td> 
											<?
											}
											if($line_efficiency<=$txt_parcentage)
											{?>
												<td class="wrd_brk" align="center" width="55" bgcolor="red"><? echo number_format($line_efficiency,2).'%'?></td>
											<?}
											else
											{?>
												<td class="wrd_brk" align="center" width="55" bgcolor="green"><? echo number_format($line_efficiency,2).'%'?></td>	
											<?
											}
											?>
											<td align="center" width="55"><? echo "23edf";?></td>
											<td  class="wrd_brk"><input type="button"  value="View" class="formbutton" onclick="show_line_remarks(<? echo $cbo_company_id;?>,'<? echo $order_no_total;?>','<? echo $f_id;?>','<? echo $resource_id;?>','remarks_popup',<? echo $txt_date;?>)"/></td>	                                
										
											</tr>
										<?
										$i++;
										}
										if($cbo_no_prod_type==1)
										{
											//echo "system";die;
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
											// count = 0;
											// if($working_hour!=0){
											// 	count++;
											// }
											// echo $count."System";die;
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
											$floor_total_wip+=($total_input-$total_output);
											
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
											$grand_total_wip+=($total_input-$total_output);
											$grand_cm_value+=$line_cm_value;
											
											$company_today_input+=$today_input;
											$company_total_input+=$total_input;
											$company_total_output+=$total_output;
											$company_total_wip+=($total_input-$total_output);
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
											
											$job_no=$production_data_arr[$f_id][$resource_id]['job_no'];
											$job_no2_arr=$job_no_arr[$f_id][$resource_id];
											$job_no2 = implode(',', $job_no2_arr);
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
											?>
											
											<tr bgcolor='<? echo $bgcolor;?>' onclick=change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>') id=tr_<? echo $i;?>>
											<td align="center" width="35" class="wrd_brk"><? echo $sewing_line;?>&nbsp;</td>
											<td width="40" align="center" class="wrd_brk"><p><? echo $buyer_name;?>&nbsp;</p></td>
											<td width="110" align="center" class="wrd_brk"><p><?=$style;?></p></td>
											<td width="100" align="center" class="wrd_brk"><p><? echo $production_data_arr[$f_id][$resource_id]['grouping'];?>&nbsp;</p></td>
											<td width="70" onclick="openmypage_image('requires/company_wise_hourly_production_monitoring_chaity_controller.php?action=show_image&job_no=<? echo $job_no2;?>','Image View')">
												<img src="../../<? echo $imge_arr[$job_no]; ?>" height="30" width="40" />
											</td>
											<td align="center" width="50"><? echo $terget_hour;?></td>
											
											<?

											$string="'";
											for($k=$hour; $k<=$last_hour; $k++)
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

												?>
												<td align="center" width="40"  style='<? echo $bg_color;?>'><? echo $production_hour[$prod_hour];?></td>
											<?
											}
											//echo "<pre>";print_r($production_hour);
											?>
											
											<td align="center" width="50"><? echo $eff_target;?></td>

											<td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'4','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $today_input;?></a></td>								    

											<td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'5','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $line_production_hour;?></a></td>
											<?
											$today_achive = ($line_production_hour/$eff_target)*100;
											//$today_achive = number_format(($line_production_hour/$eff_target)*100,2);
											if($today_achive<=$txt_parcentage)
											{?>
												<td align="center" width="50" bgcolor="red"><? echo number_format($today_achive,2).'%'; ?></td>
											<?}
											else
											{?>
												<td align="center" width="50" bgcolor="green"><? echo number_format($today_achive,2).'%'; ?></td> 
											<?
											}
											if($line_efficiency<=$txt_parcentage)
											{?>
												<td class="wrd_brk" align="center" width="55" bgcolor="red"><? echo number_format($line_efficiency,2).'%'?></td>
											<?}
											else
											{?>
												<td class="wrd_brk" align="center" width="55" bgcolor="green"><? echo number_format($line_efficiency,2).'%'?></td>	
											<?
											}
											?>
											<td class="wrd_brk"><input type="button"  value="View" class="formbutton" onclick="show_line_remarks(<? echo $cbo_company_id;?>,'<? echo $order_no_total;?>','<? echo $f_id;?>','<? echo $resource_id;?>','remarks_popup',<? echo $txt_date;?>)"/></td>
											
											</tr>
										<?    
										$i++;
										}
										//echo $floor_cm_value."***";die;
									}
									//l++;
								}
								if($cbo_no_prod_type==2 && $line_floor_production>0)
								{?>
									<tr  bgcolor="#B6B6B6">
									<td width="35">&nbsp;</td>
									<td width="60">&nbsp;</td>
									<td width="110"></td>
									<td width="100">&nbsp;</td>
									<td width="70">&nbsp;</td>

									<td align="center" width="50"><? echo $floor_tgt_h;?></td>																			
									
									<?
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
							
										?>
									
										<td align="center" width="40" style='<? echo $bg_color;?>' ><? echo $floor_production[$prod_hour];?></td>
									<?
									}
									?>								
									
									<td align="center" width="50"><? echo $eff_target_floor;?></td>
									<td align="center" width="50"><? echo $floor_today_input;?></td>
									<td align="center" width="50"><? echo $line_floor_production;?></td>
									<td align="center" width="50"><? echo number_format(($line_floor_production/$eff_target_floor)*100,2).'%';?></td>
									<td align="center" width="55"><? echo number_format($floor_efficency,2).'%'; ?></td>
									<td align="center" width=""></td>
									</tr>
									<?
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
								{?>
									<tr  bgcolor="#B6B6B6">
									<td width="35">&nbsp;</td>
									<td width="60">&nbsp;</td>
									<td width="110"></td>
									<td width="100">&nbsp;</td>
									<td width="70">&nbsp;</td>


									<td align="center" width="50"><? echo $floor_tgt_h;?></td>															

									<?
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
										?>
									
										<td align="center" width="40" style='<? echo $bg_color;?>' ><? echo $floor_production[$prod_hour];?></td>
									<?	
									}
									?>								
									
									<td align="center" width="50"><? echo $eff_target_floor;?></td>
									<td align="center" width="50"><? echo $floor_today_input;?></td>
									<td align="center" width="50"><? echo $line_floor_production;?></td>
									<td align="center" width="50"><? echo number_format(($line_floor_production/$eff_target_floor)*100,2).'%';?></td>
									<td align="center" width="55"><? echo number_format($floor_efficency,2).'%'; ?></td>
									<td align="center" width=""></td>
									</tr>
									<?    
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

					}
					?>		
										
					</tbody>

					<? $smv_for_item="";?>                
					
				</table>
			</div>
		</fieldset>  
	
	<?	
	}



	if($is_mail_send==1){
		$htmlBody = ob_get_contents();
		ob_clean();
	 
		 include(base_path('auto_mail/setting/mail_setting.php'));
	 
	 
		$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=119 and b.mail_user_setup_id=c.id and a.IS_DELETED=0 and b.IS_DELETED=0 and c.IS_DELETED=0 and A.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and c.STATUS_ACTIVE=1";
		$mail_sql=sql_select($sql);
		$mailArr=array();
		foreach($mail_sql as $row)
		{
			if ($row['EMAIL_ADDRESS']==""){$mailArr[$row['EMAIL_ADDRESS']]=$row['EMAIL_ADDRESS']; }
		}
		$to = implode(',',$mailArr);	
	
		$subject="Hourly Production Monitoring Report";

		
		$header=mailHeader();

		if($_REQUEST['isview']==1){
			$mail_item=119;
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $htmlBody;
		}
		else{
			echo sendMailMailer( $to, $subject, $htmlBody, $from_mail );
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
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$type";
	exit();      

}// 2nd Button End
if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name in(".$data.") and module_id=7 and report_id=278 and is_deleted=0 and status_active=1");
	//echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	//echo "print_report_button_setting('".$print_report_format."');\n";
	echo $print_report_format;
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
	$sql_line_remark=sql_select("select remarks,production_hour from pro_garments_production_mst where serving_company in(".$company_id.") and  floor_id=$floor_id and sewing_line in($sewing_line) and po_break_down_id in($po_id) and production_date='".$prod_date."' and status_active=1 and is_deleted=0 group by remarks,production_hour order by production_hour");

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

if($action=="show_image")
{   //echo "System";die;
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $ex_job = explode(",", $job_no);
    $job_no = "'" . implode("','", $ex_job) . "'";
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id in($job_no) and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	
	?>
    <table>
	    <tr>
		    <?
		    foreach ($data_array as $row)
			{ 
				?>
			    	<td><img src='../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
			    <?
			}
			?>
	    </tr>
    </table>
    
    <?
	exit();
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
		<div style="width:500px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
        </div>
		<div id="report_container" align="center">
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
				<caption><strong>Sewing Input Output Details </strong></caption>
                <thead>
                	<th width="30">SL</th>
                    <th width="120">Order No</th>
               		<th width="100">Input Qty.</th>
                    <th width="100">Output Qty.</th>
                    <th width="100">Balance Qty.</th>
				</thead>
                </table>
                <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
                <?
						
	
				$sql_pop="select  c.po_number,a.po_break_down_id, sum( case when a.production_type=4 THEN a.production_quantity ELSE 0 END)  as input_qty ,sum( case when a.production_type=5 THEN a.production_quantity ELSE 0 END)  as output_qty from pro_garments_production_mst a, wo_po_break_down c where a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.po_number,a.po_break_down_id  order by  c.po_number ";
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
                        <td width="100" align="right"><? echo  number_format($row[csf('input_qty')]-$row[csf('output_qty')],2); ?></td> 
					</tr>
					<?
					$total_input_qty+=$row[csf('input_qty')];
					$total_output_qty+=$row[csf('output_qty')];
					$k++;
                }
				?>
                <tr class="tbl_bottom" >
                <td colspan="2"> Total </td>
         		
                 <td align="right"> <? echo number_format($total_input_qty,2);?></td>
                 <td align="right"> <? echo number_format($total_output_qty,2);?></td>
                 <td align="right"> <? echo number_format($total_input_qty-$total_output_qty,2);?></td>
                </tr>
          	</table>
 
                <?
						
				$sql_color_size="select  a.production_type,c.color_number_id,c.size_number_id ,a.po_break_down_id, b.production_qnty  as good_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type in (4,5) and a.production_type in (4,5) and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."   order by  a.po_break_down_id ";
			
			//echo $sql_color_size;	
				$size_arr=array();
				$order_color_arr=array();
				$grand_size_arr=array();
				$grand_total=array();
				$color_size_qty_arr=array();
				$sql_color_size_result=sql_select($sql_color_size);
				foreach($sql_color_size_result as $cs_val)
				{
					$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
					$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];
					
					$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];
					
					$grand_size_arr[$cs_val[csf('size_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];
					$grand_total[$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];
				}
			
			$input_width=460+count($size_arr)*150;
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
                            <th width="150" colspan="3"><?php echo $itemSizeArr[$sid] ;?></th>
                        <?php
                        }
                        ?>
                        <th width="150" colspan="3">Color Total</th>
                    </tr>
                    
                    <tr>
                        <?php 
                        foreach($size_arr as $sid)
                        {
                        ?>
                            <th width="50">Input</th>
                            <th width="50">Output</th>
                        	<th width="50">Balance</th>
                        <?php
                        }
                        ?>
                        <th width="50">Input</th>
                        <th width="50">Output</th>
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
								?>
									<td width="50" align="right"><?php echo $color_value[$sid][4] ;?></td>
                                    <td width="50" align="right"><?php echo $color_value[$sid][5] ;?></td>
                                    <td width="50" align="right"><?php echo $color_value[$sid][4]-$color_value[$sid][5] ;?></td>
								<?php
								}
								?>
                                <td width="50" align="right"><?php echo $order_color_arr[$po_id][$color_id][4] ;?></td>
                                <td width="50" align="right"><?php echo $order_color_arr[$po_id][$color_id][5] ;?></td>
                                <td width="50" align="right"><?php echo $order_color_arr[$po_id][$color_id][4]-$order_color_arr[$po_id][$color_id][5] ;?></td>
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
                        <td width="50" align="right"><?php echo $grand_size_arr[$sid][4]-$grand_size_arr[$sid][5];?></td>
                    <?php
                    }
                    ?>
                    <td width="50" align="right"><?php echo $grand_total[4] ;?></td>
                    <td width="50" align="right"><?php echo $grand_total[5] ;?></td>
                    <td width="50" align="right"><?php echo $grand_total[4]-$grand_total[5] ;?></td>
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