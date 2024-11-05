<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );

//--------------------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
 	echo create_drop_down( "cbo_location", 120, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in('$data') order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/requires/cutting_to_sewing_input_status_report_controller', this.value+'_'+$data, 'load_drop_down_location', 'location_td' );",0 );
	//echo $data;
	exit();  	 

 }

 if($action=="style_no_popup")
 {
	 echo load_html_head_contents("Job No Info", "../../../", 1, 1,'','','');
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
			 
			 $('#hide_style_ref_id').val( id );
			 $('#hide_style_ref_no').val( name );
			 //alert( id);
			 //alert(name);
		 }
	 
	 </script>
 
	 </head>
 
	 <body>
	 <div align="center">
		 <form name="styleRef_form" id="styleRef_form">
			 <fieldset style="width:870px;">
				 <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					 <thead>
						 <th>Buyer</th>
						 <th> Style Year</th>    
						 <th>Search By</th>
							  
						 <th id="search_by_td_up" width="170">Please Enter Style No</th>
						 <th >Shipment Date</th>
						 <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						 <input type="hidden" name="hide_style_ref_no" id="hide_style_ref_no" value="" />
						 <input type="hidden" name="hide_style_ref_id" id="hide_style_ref_id" value="" />
					 </thead>
					 <tbody>
						 <tr>
							 <td align="center">
								  <? 
									 echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								 ?>
							 </td>        
							 <td>
							 <?
							   echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							 ?>
						 </td>         
							 <td align="center">	
							 <?
									$search_by_arr=array(1=>"Style Ref",2=>"Job No",3=>"Order No");
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
								 <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $style_ref_no; ?>'+'**'+document.getElementById('cbo_year').value, 'create_style_no_search_list_view', 'search_div', 'cutting_to_sewing_input_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
if($action=="order_no_search_popup")
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
			<fieldset style="width:870px;">
	            <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
						<th> Order  Year</th>
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
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>   
							<td>
							<?
                              echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>        
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year_id;  ?>'+'**'+document.getElementById('cbo_year').value,'create_order_no_search_list_view', 'search_div', 'cutting_to_sewing_input_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
if($action=="create_style_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$job_no=$data[6];
	$cbo_year=$data[7];
	$style_ref_no=$data[8];

	if($job_no!='') $job_no_cond="and a.job_no='$job_no'";else $job_no_cond="";
	
	
	
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
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="a.style_ref_no"; 
	else if($search_by==2) 
		$search_field="a.job_no"; 	
	else 
		$search_field="b.po_number";
		
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
	
	// if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	// else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	// else $year_field="";//defined Later
	
	 if($db_type==2) $year_cond="and to_char(a.insert_date,'YYYY')=$data[7]";
	else $year_cond="";
	
	
	$sql= "select b.id, a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond $year_cond  order by b.id, b.pub_shipment_date";
	//echo $sql;die();
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,style_ref_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job No Info", "../../../", 1, 1,'','','');
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
			<fieldset style="width:870px;">
	            <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
						<th> Job Year</th>    
	                    <th>Search By</th>
						     
	                    <th id="search_by_td_up" width="170">Please Enter JOB No</th>
	                    <th >Shipment Date</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
	                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>        
							<td>
                            <?
                              echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>         
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>'+'**'+document.getElementById('cbo_year').value, 'create_job_no_search_list_view', 'search_div', 'cutting_to_sewing_input_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$job_no=$data[6];
	$cbo_year=$data[7];
	if($job_no!='') $job_no_cond="and a.job_no='$job_no'";else $job_no_cond="";
	
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
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
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
	
	// if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	// else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	// else $year_field="";//defined Later
	if($db_type==2) $year_cond="and to_char(a.insert_date,'YYYY')=$data[7]";
	else $year_cond="";
	
	$sql= "select b.id, a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond  $year_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$job_no=$data[6];
	$cbo_year=$data[7];
	if($job_no!='') $job_no_cond="and a.job_no='$job_no'";else $job_no_cond="";
	
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
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
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
	
	// if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	// else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	// else $year_field="";//defined Later
	
	 if($db_type==2) $year_cond="and to_char(a.insert_date,'YYYY')=$data[7]";
	else $year_cond="";
	
	$sql= "select b.id, a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond $year_cond order by b.id, b.pub_shipment_date";
	//echo $sql;die();
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,job_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}
if($action=="size_wise_repeat_cut_no")
{
	$size_wise_repeat_cut_no=return_field_value("gmt_num_rep_sty","variable_order_tracking","company_name='$data' and variable_list=28 and is_deleted=0 and status_active=1"); 
	if($size_wise_repeat_cut_no==1) $size_wise_repeat_cut_no=$size_wise_repeat_cut_no; else $size_wise_repeat_cut_no=0;
	echo "document.getElementById('size_wise_repeat_cut_no').value = '".$size_wise_repeat_cut_no."';\n";
	exit();	
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "onchange_buyer()" );     	 
	exit();
}

 
if($action=="report_generate")
{ 
	// var_dump($_REQUEST) ;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name"  ); 	
	$country_library=return_library_array( "select id,country_name from lib_country ", "id", "country_name"  ); 
	$size_library=return_library_array( "select id,size_name from lib_size ", "id", "size_name"  ); 
	$body_title_array=return_library_array("select id,bundle_use_for from ppl_bundle_title" , "id", "bundle_use_for" ); 
	//echo $body_part;
	
	// =======================GETTING SEARCH PARAMETER==========================
	$company_name=str_replace("'","",$cbo_company_name);
	$source_name=str_replace("'","",$cbo_source_name);
	
	$cbo_working_company=str_replace("'","",$cbo_working_company_name);
	$cbo_location=str_replace("'","",$cbo_location_name);
	$cbo_buyer=str_replace("'","",$cbo_buyer_name);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$txt_job_no=str_replace("'","",$txt_job_no);
	//echo $txt_job_no;

	$txt_date = $txt_date;
	$order_no = str_replace("'","",$txt_order_no);
	
	

	if($type==1)// Cutting
	{
		$company_cond="";
		$company_cond_lay="";
		$company_cond_lay_a="";
		$company_cond_delv="";
		$str_po_cond="";
		$str_po_cond2="";
		if($cbo_working_company!=0) $company_cond.=" and d.serving_company in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_lay.=" and d.working_company_id in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_lay_a.=" and a.working_company_id in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_delv.=" and d.delivery_company_id in($cbo_working_company)";		
		if($txt_job_no!= 0) $str_po_cond.=" and a.job_no=$txt_job_no";
		if($txt_job_no!= 0) $str_po_cond2.=" and a.job_no=$txt_job_no";
		
		if ($txt_internal_ref_no=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping= '$txt_internal_ref_no'";
		if($cbo_buyer!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer";
		if($cbo_buyer!=0) $str_po_cond2.=" and a.buyer_name=$cbo_buyer";
		if($cbo_location) $str_po_cond.=" and d.location_id in($cbo_location)";
		if($cbo_location) $str_po_cond2.=" and d.location in($cbo_location)"; 
		if($order_no) $str_po_cond.=" and b.po_number in($txt_order_no)"; 

		$location_name=str_replace("'", "", $cbo_location_name);
		if($location_name) $location_cond.=" and a.location_id in($location_name)";
		if($location_name) $location_cond2.=" and a.location in($location_name)";

		
		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);
		$str_com_cond_lay=str_replace("d.working_company_id", "a.working_company_id", $company_cond_lay);
			
		// =================================== GETTING TODAY CUTTING PO ID =================================
		$today_lay_sql="SELECT b.color_id,c.order_id as po_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.mst_id=c.mst_id  and a.entry_date=$txt_date and company_id in($company_name) $str_com_cond_lay $location_cond and a.status_active in(1) and a.is_deleted=0 and b.status_active in(1) and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 " ; 
		$today_lay_res = sql_select($today_lay_sql);
	 //	echo $today_lay_sql;
		$po_id_arr = [];
		$color_id_arr = [];
	    	foreach ($today_lay_res as $key => $val) {
			if($val[csf('po_id')] != "")
			{
				$po_id_arr[$val[csf('po_id')]] = $val[csf('po_id')];
				$color_id_arr[$val[csf('color_id')]] = $val[csf('color_id')];
			}
			
		}

		// ================================ TODAY PRODUCTION PO ID ================================
		$str_com_cond_prod = str_replace("d.serving_company", "a.serving_company", $company_cond);
		$today_pro_sql="SELECT b.color_number_id,a.po_break_down_id as po_id from pro_garments_production_mst a,wo_po_color_size_breakdown b,pro_garments_production_dtls c 
		where a.id=c.mst_id and c.color_size_break_down_id=b.id and  a.po_break_down_id=b.po_break_down_id and c.status_active=1 and a.production_date=$txt_date and a.company_id in($company_name) $str_com_cond_prod $location_cond2 and b.status_active in(1) and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.production_type in(1,2,3,4) group by b.color_number_id,a.po_break_down_id" ; // $str_com_cond_prod
		$today_pro_res = sql_select($today_pro_sql);
		
		foreach ($today_pro_res as $key => $val) {
			if($val[csf('po_id')] != "")
			{
				$po_id_arr[$val[csf('po_id')]] = $val[csf('po_id')];
				$color_id_arr[$val[csf('color_id')]] = $val[csf('color_id')];
			}
			
		}


		 $po_ids =trim(implode(',', $po_id_arr),",");
		 $color_ids = trim(implode(',', $color_id_arr),",");
	 	// echo "<pre>";
		// print_r($po_ids); 
	    //	echo "</pre>";die();
		// ========================= FOR PO ID ARRAY ==========================
		if(count($po_id_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_arr, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") 
	     		{
	     			$po_ids_cond.=" and ( d.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or   d.po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	    }
	    else
	    {
	     	$po_ids_cond= " and d.po_break_down_id in($po_ids) ";
	    }

	     // ========================= FOR COLOR ID ARRAY ==========================

	    if(count($color_id_arr)>999 && $db_type==2)
	    {
	     	$color_chunk=array_chunk($color_id_arr, 999);
	     	$color_ids_cond= "";
	     	foreach($color_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($color_ids_cond=="") 
	     		{
	     			$color_ids_cond.=" and ( e.color_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$color_ids_cond.=" or   e.color_id in ($imp_ids) ";
	     		}
	     	}
	     	 $color_ids_cond.=" )";
	     }
	     else
	     {
	     	$color_ids_cond= " and e.color_id in($color_ids) ";
	     }

	    // ================================== FOR CUT AND LAY ===================================
	    $prod_po_id_lay = str_replace('d.po_break_down_id', 'f.order_id', $po_ids_cond);
		$lay_sql = "SELECT e.color_id as color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,c.excess_cut_perc,c.plan_cut_qnty,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,g.cons
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f,wo_pre_cos_fab_co_avg_con_dtls g
			where a.job_no=b.job_no_mst  and b.id=c.po_break_down_id and d.job_no=a.job_no and b.id=f.order_id and d.id=e.mst_id  and a.job_no=c.job_no_mst and e.mst_id=f.mst_id and f.order_id=b.id and b.id=g.po_break_down_id and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1)  and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.company_id in($company_name)   $str_po_cond $internal_ref_cond    $prod_po_id_lay 
			group by e.color_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,c.excess_cut_perc,c.plan_cut_qnty,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status,b.grouping,g.cons "; // $company_cond_lay
			// echo $lay_sql;die();
			$production_main_array=[];
			foreach(sql_select($lay_sql) as $row) 
			{
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["internal_ref"]=$row[csf("grouping")];	

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["excess_cut_perc"]=$row[csf("excess_cut_perc")];	

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["plan_cut_qnty"]=$row[csf("plan_cut_qnty")];	
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]["cons"]=$row[csf("cons")];	
					

				$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
				unset($prod_po_id_array[$row[csf("po_id")]]);

			}

			// echo "<pre>";
			// print_r($production_main_array);die();
			//=========================== FOR FINISH FAB QNTY ===========================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'a.po_break_down_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'b.color_number_id', $color_ids_cond);

			$booking_no_fin_qnty_array=array();
			$booking_sql=sql_select("SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $prod_po_id_lay $prod_color_id_lay ");

			foreach($booking_sql as $vals)
			{
				$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];
			}
           
			// ==================================== FOR ISSUE ====================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'po_breakdown_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'color_id', $color_ids_cond);

			$issue_sql=sql_select("SELECT po_breakdown_id,color_id,trans_id, sum(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=18 $prod_po_id_lay $prod_color_id_lay group by po_breakdown_id,color_id,trans_id");		
			foreach($issue_sql as $values)
			{		 	
			 	$issue_qnty_arr[$values[csf("po_breakdown_id")]][$values[csf("color_id")]]+=$values[csf("qnty")];
			}
			//======================================= FOR CONSUMPTION ===========================================
			
		
			
			

			// ========================================FOR LAY QUANTITY=====================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'c.order_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'b.color_id', $color_ids_cond);

			$lay_sqls="SELECT  a.job_no,c.order_id, b.gmt_item_id,b.color_id,sum( case when a.entry_date=$txt_date then  c.size_qty else 0 end) as today_lay,sum(CASE WHEN a.entry_date <= $txt_date THEN c.size_qty ELSE 0 END) as total_lay from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and b.mst_id=c.mst_id and a.entry_form=99 and company_id in($company_name) $str_com_cond_lay $location_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $prod_po_id_lay  group by a.job_no,c.order_id, b.gmt_item_id,b.color_id ";
			//echo $lay_sqls;

			$lay_qnty_array=array();
			foreach(sql_select($lay_sqls) as $vals)
			{
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("color_id")]]["today_lay"]+=$vals[csf("today_lay")];
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("total_lay")];
			}

			// =================================== FOR ORDER QUANTITY ==================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'po_break_down_id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'color_number_id', $color_ids_cond);
			$order_qnty_array=array();
			$order_qnty_sqls="SELECT po_break_down_id,color_number_id,order_quantity,item_number_id from wo_po_color_size_breakdown where status_active in(1) and is_deleted=0 $prod_po_id_lay ";
			 foreach(sql_select($order_qnty_sqls) as $values)
			 {
			 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("color_number_id")]]+=$values[csf("order_quantity")];
			 }
			 // echo "<pre>";
			 // print_r($order_qnty_array);
			 // =================================== FOR PRODUCTION DATA ==================================
			$prod_po_id_lay = str_replace('d.po_break_down_id', 'b.id', $po_ids_cond);
			$prod_color_id_lay = str_replace('e.color_id', 'c.color_number_id', $color_ids_cond);  
			$location_cond_prod = str_replace('a.location', 'd.location', $location_cond2);  

			$order_sql="SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no  ,   c.color_number_id, sum(c.order_quantity) as order_quantity, 

			sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_date then e.production_qnty else 0 end ) as today_cutting ,
			sum(case when d.production_type=1 and e.production_type=1 and d.production_date<=$txt_date then e.production_qnty else 0 end ) as total_cutting ,
			sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_date then e.production_qnty else 0 end ) as today_sewing_input ,
			sum(case when d.production_type=4 and e.production_type=4 and d.production_date<=$txt_date then e.production_qnty else 0 end ) as total_sewing_input ,
			sum(CASE WHEN d.production_type =2 and embel_name in(1,2) and d.production_date=$txt_date THEN e.production_qnty ELSE 0 END) AS today_emb_send_qnty,
			sum(CASE WHEN d.production_type =2 and embel_name in(1,2) and d.production_date<=$txt_date THEN e.production_qnty ELSE 0 END) AS total_emb_send_qnty,
			sum(CASE WHEN d.production_type =3 and embel_name in(1,2) and d.production_date=$txt_date THEN e.production_qnty ELSE 0 END) AS today_emb_rcv_qnty,
			sum(CASE WHEN d.production_type =3 and embel_name in(1,2) and d.production_date<=$txt_date THEN e.production_qnty ELSE 0 END) AS total_emb_rcv_qnty

			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $po_ids_cond  $company_cond $location_cond_prod and d.production_type in(1,2,3,4) 
			group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number, c.item_number_id, c.color_number_id,e.cut_no ";
            	//echo $po_ids_cond;die();
            $pro_po_id= str_replace('d.po_break_down_id','b.id' ,$po_ids_cond);
			$buyer_sql="SELECT  a.buyer_name,b.id,b.po_quantity,b.po_total_price,sum(c.production_quantity) as total_qty from wo_po_details_master a, wo_po_break_down b,
			pro_garments_production_mst c	
			 where a.id=b.job_id and b.id=c.po_break_down_id
			 and  a.status_active=1 and a.is_deleted=0 
			 and b.status_active=1  and b.is_deleted=0 
			 and c.status_active=1  and c.is_deleted=0 
			 and c.production_type = 1
			 $pro_po_id group by a.buyer_name,b.id,b.po_quantity,b.po_total_price ";
			
			//echo $buyer_sql;die();

			$buyer_array=array();
			foreach(sql_select($buyer_sql) as $val)

			{
				$buyer_array[$val[csf("buyer_name")]]["buyer_name"]=$val[csf("buyer_name")];	
				$buyer_array[$val[csf("buyer_name")]]["po_quantity"]+=$val[csf("po_quantity")];	
				$buyer_array[$val[csf("buyer_name")]]["po_total_price"]+=$val[csf("po_total_price")];	
				$buyer_array[$val[csf("buyer_name")]]["total_qty"]=$val[csf("total_qty")];
				

			}
			// echo "<pre>";
		    // print_r($buyer_array); 
   	      	// echo "</pre>";die();
			 // echo $order_sql;die;
			$serving_company_check = [];
			$order_sql_res = sql_select($order_sql); 
			foreach($order_sql_res as $vals)
			{
				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];			 

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];			 

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];
				 
				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_emb_send_qnty"]+=$vals[csf("today_emb_send_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_emb_send_qnty"]+=$vals[csf("total_emb_send_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["today_emb_rcv_qnty"]+=$vals[csf("today_emb_rcv_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["total_emb_rcv_qnty"]+=$vals[csf("total_emb_rcv_qnty")];

				$cutting_sewing_data[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]]["order_quantity"]+=$vals[csf("order_quantity")];
				$serving_company_check[$vals[csf("style_ref_no")]][$vals[csf("job_no")]][$vals[csf("po_id")]][$vals[csf("color_number_id")]][$vals[csf("serving_company")]]=$vals[csf("serving_company")];

			}
			// echo "<pre>";
			// print_r($serving_company_check);die;
			 
			
			if(count($production_main_array)==0)
			{
				echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px;text-align:center">No Data Found.</div>'; die;
			}

			// echo "<pre>";
			// print_r($production_main_array);//die;
			 
			ob_start();
			
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
	            hr {
	                border: 0; 
	                background-color: #000;
	                height: 1px;
	            }  
	            .gd-color
	            {
					background: #f0f9ff; /* Old browsers */
					background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
				}
				.gd-color2
				{
					background: rgb(247,251,252); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(247,251,252,1) 0%, rgba(217,237,242,1) 40%, rgba(173,217,228,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7fbfc', endColorstr='#add9e4',GradientType=0 ); /* IE6-9 */
					font-weight: bold;
				}
				.gd-color2 td
				{
					border: 1px solid #777;
					text-align: right;
				}
				.gd-color3
				{
					background: rgb(254,255,255); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
					border: 1px solid #dccdcd;
					font-weight: bold;
				}

	        </style> 
		<div>
			<table align="left" cellspacing="0" border="1" width="650" rules="all" class="rpt_table" style="margin-top: 10px;">
		      <thead>
					<tr>
					   <th  bgcolor="#dddddd" align="left" colspan="6">Buyer Wise Summary</th>
				   </tr>
							<tr>
								<th align="center"><p>SL </p></th>
								<th align="center"><p>Buyer Name</p></th>
								<th align="center"><p>Order Qty.(Pcs)</p></th>
								<th align="center"><p>Order Value</p></th>
								<th align="center"><p>Total Cut Qty</p></th>
								<th align="center"><p>Cutting Balance</p></th>
			
			 			 </tr>
				</thead>
				<tbody>
			    	<?
                       
					  
							$i=1;
							$total_order_qty=0;
							$total_order_val=0;
							$total_cut_qty=0;
							$total_cut_bla=0;



					    foreach($buyer_array as $row)
					    {
					  
							$cutting_blance=$val[csf("po_quantity")]- $val[csf("total_qty")];
							$total_order_qty+=$val[csf("po_quantity")];
							$total_order_val+=$val[csf("po_total_price")];
							$total_cut_qty+=$val[csf("total_qty")];
							$total_cut_bla+=$cutting_blance;
				
                         ?>
						   <tr>	
								<td align="center"><p><? echo $i  ?></p></td>
								<td align="center"><p><? echo $buyer_library[$row['buyer_name']];?></p></td> 
								<td align="center"><p><? echo $val[csf("po_quantity")];?></p></td>
								<td align="center"><p><? echo $val[csf("po_total_price")];?></p></td>
								<td align="center"><p><? echo $val[csf("total_qty")];?></p></td>
								<td align="center"><p><? echo $cutting_blance ?> </p></td>
	
						    </tr>

					      <?
				 	       $i++;						  
			        	}
			 
			
			        ?>
	
				</tbody>
				    <tfoot>
								<tr bgcolor="#dddddd" >
									<td align="center" colspan="2"><strong>Total</strong></td>
									<td align="center"><strong><? echo $total_order_qty;?></strong></td>
									<td align="center"><strong><?echo $total_order_val;?></strong></td>
									<td align="center"><strong><?echo $total_cut_qty;?></strong></td>
									<td align="center"><strong><?echo $total_cut_bla; ?></strong></td>
								
								</tr>
					
				    </tfoot>
	        </table>
	   	  </div>
                <div style="width: 1900px;; margin: 0 auto"> 
                    <table width="1900px" cellspacing="0" style="margin: 20px 0"> 
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td align="center" style="border:none; font-size:16px; font-weight: bold;" width="40%">                                	
                            Company Name  :    
							<?=$company_library[$company_name];?>
                            </td>
                           
                        </tr>
						<tr style="border:none;">
						      <td>&nbsp;</td>
									<td align="center" style="border:none; font-size:17px; font-weight: bold;"        width="40%">                                	
									 Cutting Production Report Details            
								 </td>                                
                        </tr>  
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td align="center" style="border:none; font-size:16px;font-weight: bold;" width="40%">                        
                                Date :  <? echo change_date_format(str_replace("'", "", $txt_date)); ?>  

                            </td>
                           
                           
                        </tr>  
                    </table> 
                    	
					
				</div>
                <div>
                    <table  width="1900" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                        <thead> 	 	 	 	 	 	
                    			<tr>
											<th  rowspan="2" width="20"><p>Sl.</p></th>  
											<th rowspan="2" width="170"><p>Working Company</p></th>  
											<th  rowspan="2" width="130"><p>Location</p></th>  
											<th  rowspan="2" width="170"><p>Buyer</p></th>
											<th  rowspan="2" width="130"><p>Job No.</p></th>
											<th rowspan="2" width="110"><p>Internal Ref.</p></th>
											<th  rowspan="2" width="200"><p>Style ref.</p></th>
											<th  rowspan="2" width="110"><p>Ship Status</p></th>
											<th  rowspan="2" width="90"><p>Ship Date</p></th>
											<th  rowspan="2" width="150"><p>Order No.</p></th>
											<th  colspan="8" ><p>Fabric Status</p></th>
											<th  colspan="4" ><p>Cutting Status</p></th>     
								</tr>
                                <tr>
                             	
											<th width="160"><p>Color Name</p></th>
											<th width="160"><p>Color Qty</p></th>
											
											<th  width="160"><p>Plan Cut %</p></th>
											<th width="160"><p>Plan Cut Qty</p></th>
											<th  width="90"><p>F.Fab Req.</p></th>
											<th  width="120"><p>F.Fab Cons.</p></th>
											<th  width="100"><p>Fin.Fab Issue</p></th>
											<th  width="100"><p>F.Issued Bal.</p></th>
											

											<th width="90"><p>Today</p></th>
											<th  width="90"><p>Total</p></th>
											<th  width="90"><p>Order Balance</p></th>
											<th  width="90"><p>Plan Cut Bal.</p></th>
            
                               </tr>
                        </thead>
                    </table>
				</div>	
                    <div>
                        <table class="rpt_table" width="1900" cellpadding="0" cellspacing="0"  rules="all" id="tbody" >
						    <tbody>
										<?
							
									        	$i=1;
																
												$grand_color_total = 0;
												// fabric status sum
												$grand_fab_req = 0;
												$grand_fin_fab_req = 0;
												$grand_fab_issued_balance = 0;
												
												
												// cutting status sum
												$grand_today_cutting=0;
												$grand_total_cutting=0;
												$grand_cut_balance=0;
										
													
												foreach($production_main_array as $style_id=>$job_data)
												{								
													foreach($job_data as $job_id=>$po_data)
													{		
															$order_wise_color_total = 0;
															// fabric status sum
															$order_wise_fab_req = 0;
															$order_wise_fin_fab_req = 0;
															$order_wise_fab_issued_balance = 0;
															
																			
													foreach($po_data as $po_id=>$color_data)
													{
													foreach($color_data as $color_id=>$row)
														{

																	$today_lay_qnty 				=$lay_qnty_array[$job_id][$po_id][$color_id]["today_lay"];
																$today_cutting_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["today_cutting"];
																

																if($today_lay_qnty != "" || $today_cutting_qnty != "" || $today_sewing_input != "" || $today_emb_send_qnty != "" || $today_emb_rcv_qnty != "")
																{
																$color_wise_today_cutting		=0;
																$color_wise_total_cutting		=0;
																
																$fin_req 						=0;															
																$fin_req 						=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
																$issue_qty 						=$issue_qnty_arr[$po_id][$color_id];
																$req_issue_bal					=($fin_req-$issue_qty);
																$possible_cut_pcs 				=$issue_qty/$result_consumtion[$job_id];
																	$total_lay_qnty 				=$lay_qnty_array[$job_id][$po_id][$color_id]["total_lay"]; 
																	$order_qty 						= $order_qnty_array[$po_id][$color_id];
																$total_cutting_qnty 			= $cutting_sewing_data[$style_id][$job_id][$po_id][$color_id]["total_cutting"];
																$color_wise_today_cutting    	+= $today_cutting_qnty;
																$color_wise_total_cutting 		+= $total_cutting_qnty;

																
																

																// order wise
																$order_wise_color_total 		+= $order_qty;

																$order_wise_fab_req 			+= $fin_req;
																$order_wise_fin_fab_req 		+= $issue_qty;
																$order_wise_fab_issued_balance 	+= $req_issue_bal;
															

																$order_wise_today_cutting 		+=$today_cutting_qnty;
																$order_wise_total_cutting 		+=$total_cutting_qnty;
																$order_wise_cut_balance 		+= ($order_qty-$total_cutting_qnty);

																
																$order_wise_today_lay 			+=$today_lay_qnty;
																$order_wise_total_lay 			+=$total_lay_qnty;
																$order_wise_lay_balance 		+= ($order_qty - $total_lay_qnty);

																
																// grand total
																$grand_color_total 				+= $order_qty;

																$grand_fab_req 					+= $fin_req;
																$grand_fin_fab_req 				+= $issue_qty;
																$grand_fab_issued_balance 		+= $req_issue_bal;
																$grand_fab_cons                  +=$row['cons'];
															

																$grand_today_cutting 			+=$today_cutting_qnty;
																$grand_total_cutting 			+=$total_cutting_qnty;
																$grand_cut_balance 				+= ($order_qty-$total_cutting_qnty);
																$grand_cut_qty_total             +=$row['plan_cut_qnty'];
																$grand_order_balance		        +=($order_qty)-$total_cutting_qnty;
																$grand_plan_cut_bal              +=$row['plan_cut_qnty']-$total_cutting_qnty;



																

																

																$serving_company = implode(",", $serving_company_check[$style_id][$job_id][$po_id][$color_id]);
																$order_blance=($order_qty)-$total_cutting_qnty;
																$plan_cut_bal=$row['plan_cut_qnty']-$total_cutting_qnty;
																
																	
																	
									?>
																	
													<tr>
														
														<td  width="20"><p><?echo $i;?></p></td> 
														<td  width="170"><p><?  echo $company_library[$row['working_company_id']];?></p><td> 
														<td  width="130"><p><? echo $location_library[$row['location_id']];?></p></td> 
														<td  width="170"><p><? echo $buyer_library[$row['buyer_name']];?></p></td> 
														<td  width="130"><p><? echo $job_id;?></p></td> 
														<td  width="110"><p><?php echo $row['internal_ref'];?></p></td> 
														<td  width="200"><p><? echo $style_id; ?></p></td> 
														<td  width="110"><p><? echo $row['shiping_status']; ?></p></td> 
														<td  width="90"><p><? echo change_date_format($row['pub_shipment_date']);?></p></td> 
														<td  width="150"><p><? echo $row['po_number'];?></p></td>  	

														<td  width="160"><p><? echo $color_library[$color_id]; ?></p></td> 
														<td  align="center" width="160"><p><? echo number_format($order_qty,0); ?></p></td> 
														<td  align="center" width="160"><p><? echo $row['excess_cut_perc'];?></p></td> 
														<td  align="center" width="160"><p><? echo $row['plan_cut_qnty'];?></p></td> 	
														<td  align="center"  width="90"><p><?echo number_format($fin_req,2);?></p></td> 
														<td  align="center" width="120"><p><? echo number_format($row['cons'],3);?></p></td> 
														<td  align="center"  width="100"><p><? echo number_format($issue_qty,2);?></p></td> 
														<td  align="center"  width="100"><p><?echo number_format($req_issue_bal,2);?></p></td> 
														
														<td  align="center"  width="90"><p><? echo $today_cutting_qnty;?></p></td> 
														<td  align="center" width="90"><p><? echo $total_cutting_qnty;?></p></td> 
														<td  align="center" width="90"><p><? echo $order_blance ;?></p></td>
														<td  align="center" width="90"><p><? echo $plan_cut_bal?></p></td>
													</tr>					 				
																						
													<?
													$i++;
													}
													}
											    }
										    }
								    	}
							   ?>										
												
						    </tbody>	

									      
								
						
						</table>
				    </div>
						<table  border="1" class=""  width="1900" rules="all" id="" cellpadding="0" cellspacing="0">
							<tr bgcolor="#dddddd">
											<th   width="20"><p></p></th> 
											<th width="170"><p></p><th> 
											<th  width="130"><p></p></th> 
											<th width="170"><p></p></th> 
											<th width="130"><p></p></th> 
											<th  width="110"><p></p></th> 
											<th  width="200"><p></p></th> 
											<th width="110"><p></p></th> 
											<th width="90"><p></p></th> 
											<th width="160"><p></p></th>  	

											 <th width="160"><p>Grand Total:</p></th> 
											<th align="center" width="160"><p><? echo $grand_color_total?></p></th> 
											<th align="center" width="160"><p></p></th> 
											<th align="center" width="160"><p><p><? echo $grand_cut_qty_total ?></p></th> 	
											<th align="center" width="90"><p><? echo   $grand_fab_req ?></p></th> 
											<th  align="center" width="120"><p><? echo $grand_fab_cons ?></p></th> 
											<th align="center"  width="100"><p><? echo $grand_fin_fab_req ?></p></th> 
											<th align="center"  width="100"><p><? echo $grand_fab_issued_balance?></p></th> 
											
											<th align="center" width="90"><p><? echo  $grand_today_cutting ?></p></th> 
											<th align="center" width="90"><p><? echo $grand_total_cutting ?></p></th> 
											<th align="center" width="90"><p><?echo $grand_order_balance?><p><t/h>
											<th align="center"  width="90"><p><? echo $grand_plan_cut_bal?></p></th>
					         </tr>	
            	
				            
				            
				            	
						</table>
                    </div>
             
    		   </div><!-- end main div -->
    	<?
 	}
    else if ($type==2)//Emb. Issue 
    {
       
		$company_cond="";
		$company_cond_lay="";
		$company_cond_lay_a="";
		$company_cond_delv="";
		$str_po_cond="";
		$str_po_cond2="";
		if($cbo_working_company!=0) $company_cond.=" and d.serving_company in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_lay.=" and d.working_company_id in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_lay_a.=" and a.working_company_id in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_delv.=" and d.delivery_company_id in($cbo_working_company)";		
		if($txt_job_no=="") $job_no_cond.=""; else $job_no_cond.= " and a.job_no='$txt_job_no'";
		
		if ($txt_ref_no=="") $ref_no_cond=""; else $ref_no_cond=" and a.style_ref_no= '$txt_ref_no'";
		if ($txt_date) $date_cond=" and d.production_date=$txt_date";

		
		if($cbo_buyer!=0) $buyer_cond.=" and a.buyer_name=$cbo_buyer";
		
		if($cbo_location) $str_loaction_cond.=" and d.location_id in($cbo_location)";
		if($cbo_location) $str_po_cond2.=" and d.location in($cbo_location)"; 
		if($order_no) $str_po_cond.=" and b.po_number in($txt_order_no)"; 

		$location_name=str_replace("'", "", $cbo_location_name);
		if($location_name) $location_cond.=" and a.location_id in($location_name)";
		if($location_name) $location_cond2.=" and a.location in($location_name)";
		$source_name=str_replace("'", "", $cbo_source_name);
		if($source_name) $source_cond.=" and d.production_source in($source_name)";
		

		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);
		$str_com_cond_lay=str_replace("d.working_company_id", "a.working_company_id", $company_cond_lay);




		$str_com_cond_prod = str_replace("d.serving_company", "a.serving_company", $company_cond);
		$location_cond_prod = str_replace('a.location', 'd.location', $location_cond2);  

		//$body_part=return_field_value( "bundle_use_for","ppl_bundle_title","bundle_use_for"  ); 

		$emb_order_sql="SELECT a.job_no,a.buyer_name,a.style_ref_no,a.style_description, b.id as po_id, b.po_number,b.grouping,c.item_number_id,c.color_number_id,c.country_id,c.size_number_id,d.embel_name,d.production_type,e.cut_no,e.production_qnty,e.bundle_no,f.sys_number,f.body_part,f.remarks
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_delivery_mst f
		where  a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and f.id=d.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.production_type=2 $company_cond $source_cond  $location_cond_prod  $buyer_cond $ref_no_cond $job_no_cond $str_po_cond $date_cond 
		 ";

		// echo $emb_order_sql;die();
		$emb_order_arry=array();
		$size_id_arry=array();
		$bundle_chk=array();
		
		foreach(sql_select($emb_order_sql) as $row)
		{
			$size_id_arry[$row[csf("size_number_id")]]=$row[csf("size_number_id")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["job_no"]=$row[csf("job_no")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["style_description"]=$row[csf("style_description")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["item_number_id"]=$row[csf("item_number_id")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["country_id"]=$row[csf("country_id")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["color_number_id"]=$row[csf("color_number_id")];

			
			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["size_number_id"]=$row[csf("size_number_id")];

		

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["cut_no"]=$row[csf("cut_no")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["production_qnty"]+=$row[csf("production_qnty")];
			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["body_part"]=$row[csf("body_part")];
			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["remarks"]=$row[csf("remarks")];


			if($bundle_chk[$row[csf("embel_name")]][$row[csf("bundle_no")]]=="")
			{
				$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["tot_bundle"]++;
			}
		}
		    // echo "<pre>";
		    // print_r($emb_order_arry); 
   	      	// echo "</pre>";die();



		$emb_sql="SELECT a.cutting_no,b.color_id,b.order_cut_no,c.order_id as po_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.mst_id=c.mst_id  and a.entry_date=$txt_date and company_id in($company_name) $str_com_cond_lay $location_cond and a.status_active in(1) and a.is_deleted=0 and b.status_active in(1) and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 group by a.cutting_no,b.color_id,c.order_id,b.order_cut_no" ; 
		 //echo $emb_sql;die();
		//$emb_res = sql_select($emb_sql);
		$emb_array=array();
		foreach(sql_select($emb_sql) as $vals)

		{
			$emb_array[$vals[csf("cutting_no")]][$vals[csf("po_id")]][$vals[csf("color_id")]]["order_cut_no"]=$vals[csf("order_cut_no")];
		}
            //  echo "<pre>";
		    //  print_r($emb_array); 
   	    	// echo "</pre>";die();
		     $tabl_width=1260+count($size_id_array)*50;
			?>
                <div style="width:<?=$tabl_width;?>px; margin: 0 auto"> 
				<table width="<?=$tabl_width;?>px" cellspacing="0" style="margin: 20px 0"> 
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td  style="border:none; font-size:16px; font-weight: bold;" width="40%">                                	
                            Company Name  : 
							<?=$company_library[$company_name];?>                
                            </td>
                           
                            
                        </tr>
						<tr style="border:none;">
						<td>&nbsp;</td>
                            <td  style="border:none; font-size:17px; font-weight: bold;" width="40%">                                	
							Embellishment Issue Report Details       
                            </td>                                
                        </tr>  
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td  style="border:none; font-size:16px;font-weight: bold;" width="40%">                        
                                Date :   
								<? echo change_date_format(str_replace("'", "", $txt_date)); ?>                    
                            </td>
                           
                           
                        </tr>  
                    </table> 
                    	
					<?
					$i=1;
							
							$total_issue_qty=0;
							$size_wise_qty_arr = array();
							

					      foreach($emb_order_arry as $sys_number=>$sys_data)
						  {    
								$challan_wise_size_qty_arr = array();
								
							     
								foreach($sys_data as $embel_name=>$embel_data)
								{
									?>
			<table align="left" cellspacing="0" width="<?=$tabl_width;?>" border="1" rules="all" class="rpt_table" style="margin-top: 10px;">
              	<thead bgcolor="#dddddd" align="center">
				<tr>
                    <th align="center" colspan="11">Challan No:&nbsp;&nbsp;<? echo $sys_number;?> &nbsp; Embel Type:&nbsp;&nbsp;<? echo $emblishment_name_array[$embel_name];?> </th>
                     
                     <th colspan="<?=count($size_id_arry);?>"><p>Size</p></th>
					 <th></th> 
					 <th ></th> 
					 <th></th> 
					 <th></th> 
				</tr>              
				<tr>
				      <th width="50"><p>SL No</p></th>
                      <th width="90"><p>Buyer</p></th>
                      <th width="90"><p>Job No</p></th>
                      <th width="90"><p>Order No</p></th>
                      <th width="80"><p>Int. Ref.</p></th>
                      
                      <th width="150"><p>Style ref</p></th>
                      <th width="80"><p>Style Des</p></th>
                      <th width="80"><p>Country</p></th>
					  <th width="80"><p>Color</p></th>
                      <th width="80"><p>Cutting No</p></th>
                      <th width="100"><p>Order Cut No</p></th>
					  <?
					  foreach($size_id_arry as $val)
					  {
						?>
						<th width="50"><p><? echo $size_library[$val['size_number_id'] ] ?></p></th>
						<?
					  }
					  ?>
					  <th width="80" ><p>Total Issue Qty</p></th>
					
                      <th width="60"><p>Number  of Bundle</p></th>
                      <th width="70"><p>Body Part</p></th>
                     
                      <th width="90"><p>Remarks</p></th>  
					 

				</tr>
                      
                </thead>
				<tbody>
					     <? 
					    	
									foreach($embel_data as $po_id=>$po_data)
									{
										foreach($po_data as $cut_no=>$cut_data)
										{ 
											foreach($cut_data as $color_id=>$row)
											{      
												
								              
							        	 		?> 
												
												<tr bgcolor="#dddddd">
													<td width="50"><p><? echo $i ;?></p></td>
													<td width="90"><p><? echo $buyer_library[$row['buyer_name']];?></p></td> 
													<td width="90"><p><? echo $row['job_no'];?></p></td>
													<td width="90"><p><? echo $row['po_number'];?></p></td>
													<td width="80"><p><? echo $row['grouping'] ;?></p></td>
													
													<td width="150"><p><? echo $row['style_ref_no'];?></p></td>
													<td width="80"><p><? echo $row['style_description']; ?></p></td>
													<td width="80"><p><? echo $country_library[$row['country_id']]; ?></p></td>
													<td width="80"><p><? echo $color_library[$row['color_number_id']]; ?></p></th>
													<td width="80"><p><? echo $row['cut_no']; ?></td>
													<td width="100"><p><? echo $emb_array[$cut_no][$po_id][$color_id]['order_cut_no']; ?></p></td>
													<?
													$total_issue_qty=0;
													
													foreach($size_id_arry as $val)
													{
														?>
														<td align="right" width="50"><p><? echo $row[$val]['production_qnty']; ?></p></td>
														<?
														$challan_wise_size_qty_arr[$sys_number][$val]+=$row[$val]['production_qnty'];
														$total_issue_qty+=$row[$val]['production_qnty'];
														
													}
													?>
													<td align="right"  width="80"><p><? echo $total_issue_qty;?></p></td>
													
													<td width="60"><p><? echo $row['tot_bundle'];?></p></td>
													<td width="70"><p><? echo $body_title_array[$row['body_part']];?></p></td>
													
													<td width="90"><p><?echo $row['remarks'];?></p></td>  
													</tr>
														<?
														$i++;
                                           
							
								            }
								        }
										
						 	        }

						        
								?>
								
								
						  </tbody>
						 
						  <tfoot>
										
				                         
									

						
						  <tr bgcolor="#dddddd">
								        	<th>&nbsp;</th> 
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 	
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 	
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 
											<th><strong><p>Sub Total</strong></p></th> 
											<?
											foreach($size_id_arry as $val)
											{
												?>
												<th  width="50"><p><? echo $challan_wise_size_qty_arr[$sys_number][$val];?></th>
												<?
												$challan_wise_size_qty_arr[$sys_number][$val]+=$row[$val]['production_qnty'];
											}
											?> 
											<th><? echo $total_issue_qty;?></th> 
											<th>&nbsp;</th> 
											<th>&nbsp;</th>
											<th>&nbsp;</th> 
											
											
									</tr>
				</tfoot>
		</table>
		<?
						    }
						}
					
						  ?>
						  <table align="left" cellspacing="0" width="<?=$tabl_width;?>" border="1" rules="all" class="rpt_table">
						  <tfoot>
						
										<tr bgcolor="#dddddd">
														<th width="50">&nbsp;</th> 
														<th width="90">&nbsp;</th> 
														<th width="90">&nbsp;</th> 	
														<th width="90">&nbsp;</th> 
														<th width="80">&nbsp;</th> 		
														<th width="150">&nbsp;</th> 
														<th width="80">&nbsp;</th> 
														<th width="80">&nbsp;</th> 
														<th width="80">&nbsp;</th> 
														<th width="80">&nbsp;</th> 
														<th width="100"><p>Grand  Total</p></td> 
														<?
														foreach($size_id_arry as $val)
														{
															?>
														<th  width="50"><p><? echo $size_wise_qty_arr[$val]+=$row[$val]['production_qnty'];?></p></th>
														<?
														
														}
														?> 
														<th width="80"><p><? echo $total_issue_qty?></p></th> 
														<th width="60">&nbsp;</th> 
														<th width="70">&nbsp;</th>
														<th width="90">&nbsp;</th> 
										</tr>	
							</tfoot>
					</table>
			
    		</div><!-- end main div -->
    	<?    

    }   
	
	else if ($type==3)//Emb. receive
    {
       
		$company_cond="";
		$company_cond_lay="";
		$company_cond_lay_a="";
		$company_cond_delv="";
		$str_po_cond="";
		$str_po_cond2="";
		if($cbo_working_company!=0) $company_cond.=" and d.serving_company in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_lay.=" and d.working_company_id in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_lay_a.=" and a.working_company_id in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_delv.=" and d.delivery_company_id in($cbo_working_company)";		
		if($txt_job_no=="") $job_no_cond.=""; else $job_no_cond.= " and a.job_no='$txt_job_no'";
		
		if ($txt_ref_no=="") $ref_no_cond=""; else $ref_no_cond=" and a.style_ref_no= '$txt_ref_no'";
		if ($txt_date) $date_cond=" and d.production_date=$txt_date";

		
		if($cbo_buyer!=0) $buyer_cond.=" and a.buyer_name=$cbo_buyer";
		
		if($cbo_location) $str_loaction_cond.=" and d.location_id in($cbo_location)";
		if($cbo_location) $str_po_cond2.=" and d.location in($cbo_location)"; 
		if($order_no) $str_po_cond.=" and b.po_number in($txt_order_no)"; 

		$location_name=str_replace("'", "", $cbo_location_name);
		if($location_name) $location_cond.=" and a.location_id in($location_name)";
		if($location_name) $location_cond2.=" and a.location in($location_name)";
		$source_name=str_replace("'", "", $cbo_source_name);
		if($source_name) $source_cond.=" and d.production_source in($source_name)";
		

		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);
		$str_com_cond_lay=str_replace("d.working_company_id", "a.working_company_id", $company_cond_lay);




		$str_com_cond_prod = str_replace("d.serving_company", "a.serving_company", $company_cond);
		$location_cond_prod = str_replace('a.location', 'd.location', $location_cond2);  

		//$body_part=return_field_value( "bundle_use_for","ppl_bundle_title","bundle_use_for"  ); 

		$emb_order_sql="SELECT a.job_no,a.buyer_name,a.style_ref_no,a.style_description, b.id as po_id, b.po_number,b.grouping,c.item_number_id,c.color_number_id,c.country_id,c.size_number_id,d.embel_name,d.production_type,e.cut_no,e.production_qnty,e.bundle_no,f.sys_number,f.body_part,f.remarks
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_delivery_mst f
		where  a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and f.id=d.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.production_type=3 $company_cond $source_cond  $location_cond_prod  $buyer_cond $ref_no_cond $job_no_cond $str_po_cond $date_cond 
		 ";

		// echo $emb_order_sql;die();
		$emb_order_arry=array();
		$size_id_arry=array();
		$bundle_chk=array();
		
		foreach(sql_select($emb_order_sql) as $row)
		{
			$size_id_arry[$row[csf("size_number_id")]]=$row[csf("size_number_id")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["job_no"]=$row[csf("job_no")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["style_description"]=$row[csf("style_description")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["item_number_id"]=$row[csf("item_number_id")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["country_id"]=$row[csf("country_id")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["color_number_id"]=$row[csf("color_number_id")];

			
			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["size_number_id"]=$row[csf("size_number_id")];

		

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["cut_no"]=$row[csf("cut_no")];

			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["production_qnty"]+=$row[csf("production_qnty")];
			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["body_part"]=$row[csf("body_part")];
			$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["remarks"]=$row[csf("remarks")];


			if($bundle_chk[$row[csf("embel_name")]][$row[csf("bundle_no")]]=="")
			{
				$emb_order_arry[$row[csf("sys_number")]][$row[csf("embel_name")]][$row[csf("po_id")]][$row[csf("cut_no")]][$row[csf("color_number_id")]]["tot_bundle"]++;
			}
		}
		    // echo "<pre>";
		    // print_r($emb_order_arry); 
   	      	// echo "</pre>";die();



		$emb_sql="SELECT a.cutting_no,b.color_id,b.order_cut_no,c.order_id as po_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.mst_id=c.mst_id  and a.entry_date=$txt_date and company_id in($company_name) $str_com_cond_lay $location_cond and a.status_active in(1) and a.is_deleted=0 and b.status_active in(1) and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 group by a.cutting_no,b.color_id,c.order_id,b.order_cut_no" ; 
		 //echo $emb_sql;die();
		//$emb_res = sql_select($emb_sql);
		$emb_array=array();
		foreach(sql_select($emb_sql) as $vals)

		{
			$emb_array[$vals[csf("cutting_no")]][$vals[csf("po_id")]][$vals[csf("color_id")]]["order_cut_no"]=$vals[csf("order_cut_no")];
		}
            //  echo "<pre>";
		    //  print_r($emb_array); 
   	    	// echo "</pre>";die();
		     $tabl_width=1260+count($size_id_array)*50;
			?>
                <div style="width:<?=$tabl_width;?>px; margin: 0 auto"> 
				<table width="<?=$tabl_width;?>" cellspacing="0" style="margin: 20px 0"> 
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td style="border:none; font-size:16px; font-weight: bold;" width="40%">                                	
                            Company Name  : 
							<?=$company_library[$company_name];?>                
                            </td>
                           
                            
                        </tr>
						<tr style="border:none;">
						<td>&nbsp;</td>
                            <td style="border:none; font-size:17px; font-weight: bold;" width="40%">                                	
							Embellishment Receive Report Details       
                            </td>                                
                        </tr>  
                        <tr style="border:none;">
                        	<td>&nbsp;</td>
                            <td  style="border:none; font-size:16px;font-weight: bold;" width="40%">                        
                                Date :   
								<? echo change_date_format(str_replace("'", "", $txt_date)); ?>                    
                            </td>
                           
                           
                        </tr>  
                    </table> 
                    	
					<?
					$i=1;
							
							$total_issue_qty=0;
							$size_wise_qty_arr = array();
							

					      foreach($emb_order_arry as $sys_number=>$sys_data)
						  {    
								$challan_wise_size_qty_arr = array();
								
							     
								foreach($sys_data as $embel_name=>$embel_data)
								{
									?>
			<table align="left" cellspacing="0" width="<?=$tabl_width;?>" border="1" rules="all" class="rpt_table" style="margin-top: 10px;">
              	<thead bgcolor="#dddddd" align="center">
				<tr>
                    <th align="center" colspan="11">Challan No:&nbsp;&nbsp;<? echo $sys_number;?> &nbsp; Embel Type:&nbsp;&nbsp;<? echo $emblishment_name_array[$embel_name];?> </th>
                     
                     <th colspan="<?=count($size_id_arry);?>"><p>Size</p></th>
					 <th></th> 
					 <th ></th> 
					 <th></th> 
					 <th></th> 
				</tr>              
				<tr>
				      <th width="50"><p>SL No</p></th>
                      <th width="90"><p>Buyer</p></th>
                      <th width="90"><p>Job No</p></th>
                      <th width="90"><p>Order No</p></th>
                      <th width="80"><p>Int. Ref.</p></th>
                      
                      <th width="150"><p>Style ref</p></th>
                      <th width="80"><p>Style Des</p></th>
                      <th width="80"><p>Country</p></th>
					  <th width="80"><p>Color</p></th>
                      <th width="80"><p>Cutting No</p></th>
                      <th width="100"><p>Order Cut No</p></th>
					  <?
					  foreach($size_id_arry as $val)
					  {
						?>
						<th width="50"><p><? echo $size_library[$val['size_number_id'] ] ?></p></th>
						<?
					  }
					  ?>
					  <th width="80" ><p>Total Issue Qty</p></th>
					
                      <th width="60"><p>Number  of Bundle</p></th>
                      <th width="70"><p>Body Part</p></th>
                     
                      <th width="90"><p>Remarks</p></th>  
					 

				</tr>
                      
                </thead>
				<tbody>
					     <? 
					    	
									foreach($embel_data as $po_id=>$po_data)
									{
										foreach($po_data as $cut_no=>$cut_data)
										{ 
											foreach($cut_data as $color_id=>$row)
											{      
												
								              
							        	 		?> 
												
												<tr bgcolor="#dddddd">
													<td width="50"><p><? echo $i ;?></p></td>
													<td width="90"><p><? echo $buyer_library[$row['buyer_name']];?></p></td> 
													<td width="90"><p><? echo $row['job_no'];?></p></td>
													<td width="90"><p><? echo $row['po_number'];?></p></td>
													<td width="80"><p><? echo $row['grouping'] ;?></p></td>
													
													<td width="150"><p><? echo $row['style_ref_no'];?></p></td>
													<td width="80"><p><? echo $row['style_description']; ?></p></td>
													<td width="80"><p><? echo $country_library[$row['country_id']]; ?></p></td>
													<td width="80"><p><? echo $color_library[$row['color_number_id']]; ?></p></th>
													<td width="80"><p><? echo $row['cut_no']; ?></td>
													<td width="100"><p><? echo $emb_array[$cut_no][$po_id][$color_id]['order_cut_no']; ?></p></td>
													<?
													$total_issue_qty=0;
													
													foreach($size_id_arry as $val)
													{
														?>
														<td align="right" width="50"><p><? echo $row[$val]['production_qnty']; ?></p></td>
														<?
														$challan_wise_size_qty_arr[$sys_number][$val]+=$row[$val]['production_qnty'];
														$total_issue_qty+=$row[$val]['production_qnty'];
														
													}
													?>
													<td align="right"  width="80"><p><? echo $total_issue_qty;?></p></td>
													
													<td width="60"><p><? echo $row['tot_bundle'];?></p></td>
													<td width="70"><p><? echo $body_title_array[$row['body_part']];?></p></td>
													
													<td width="90"><p><?echo $row['remarks'];?></p></td>  
													</tr>
														<?
														$i++;
                                           
							
								            }
								        }
										
						 	        }

						        
								?>
								
								
						  </tbody>
						 
						  <tfoot>
										
				                         
									

						
						  <tr bgcolor="#dddddd">
								        	<th>&nbsp;</th> 
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 	
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 	
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 
											<th>&nbsp;</th> 
											<th><strong><p>Sub Total</strong></p></th> 
											<?
											foreach($size_id_arry as $val)
											{
												?>
												<th  width="50"><p><? echo $challan_wise_size_qty_arr[$sys_number][$val];?></th>
												<?
												$challan_wise_size_qty_arr[$sys_number][$val]+=$row[$val]['production_qnty'];
											}
											?> 
											<th><? echo $total_issue_qty;?></th> 
											<th>&nbsp;</th> 
											<th>&nbsp;</th>
											<th>&nbsp;</th> 
											
											
									</tr>
				</tfoot>
		</table>
		<?
						    }
						}
					
						  ?>
						  <table align="left" cellspacing="0" width="<?=$tabl_width;?>" border="1" rules="all" class="rpt_table">
						  <tfoot>
						
										<tr bgcolor="#dddddd">
														<th width="50">&nbsp;</th> 
														<th width="90">&nbsp;</th> 
														<th width="90">&nbsp;</th> 	
														<th width="90">&nbsp;</th> 
														<th width="80">&nbsp;</th> 		
														<th width="150">&nbsp;</th> 
														<th width="80">&nbsp;</th> 
														<th width="80">&nbsp;</th> 
														<th width="80">&nbsp;</th> 
														<th width="80">&nbsp;</th> 
														<th width="100"><p>Grand  Total</p></td> 
														<?
														foreach($size_id_arry as $val)
														{
															?>
														<th  width="50"><p><? echo $size_wise_qty_arr[$val]+=$row[$val]['production_qnty'];?></p></th>
														<?
														
														}
														?> 
														<th width="80"><p><? echo $total_issue_qty?></p></th> 
														<th width="60">&nbsp;</th> 
														<th width="70">&nbsp;</th>
														<th width="90">&nbsp;</th> 
										</tr>	
							</tfoot>
					</table>
			
    		</div><!-- end main div -->
    	<?    

    }    
	
	else if ($type==4) //Sew. Input
    {
	
		$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
		$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
		$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

		$company_cond="";
		$company_cond_lay="";
		$company_cond_lay_a="";
		$company_cond_delv="";
		$str_po_cond="";
		$str_po_cond2="";
		if($cbo_working_company!=0) $company_cond.=" and d.serving_company in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_lay.=" and d.working_company_id in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_lay_a.=" and a.working_company_id in($cbo_working_company)";		
		if($cbo_working_company!=0) $company_cond_delv.=" and d.delivery_company_id in($cbo_working_company)";		
		if($txt_job_no=="") $job_no_cond.=""; else $job_no_cond.= " and a.job_no='$txt_job_no'";
		
		if ($txt_ref_no=="") $ref_no_cond=""; else $ref_no_cond=" and a.style_ref_no= '$txt_ref_no'";
		if ($txt_date) $date_cond=" and d.production_date=$txt_date";

		
		if($cbo_buyer!=0) $buyer_cond.=" and a.buyer_name=$cbo_buyer";
		
		if($cbo_location) $str_loaction_cond.=" and d.location_id in($cbo_location)";
		if($cbo_location) $str_po_cond2.=" and d.location in($cbo_location)"; 
		if($order_no) $str_po_cond.=" and b.po_number in($txt_order_no)"; 

		$location_name=str_replace("'", "", $cbo_location_name);
		if($location_name) $location_cond.=" and a.location_id in($location_name)";
		if($location_name) $location_cond2.=" and a.location in($location_name)";
		$source_name=str_replace("'", "", $cbo_source_name);
		if($source_name) $source_cond.=" and d.production_source in($source_name)";
		

		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);
		$str_com_cond_lay=str_replace("d.working_company_id", "a.working_company_id", $company_cond_lay);




		$str_com_cond_prod = str_replace("d.serving_company", "a.serving_company", $company_cond);
		$location_cond_prod = str_replace('a.location', 'd.location', $location_cond2);  
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
		$company_cond $source_cond $company_cond_lay $location_cond_prod  $buyer_cond $ref_no_cond $job_no_cond $str_po_cond $date_cond 
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
     // echo $sql; die();
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
        


        GROUP BY d.sewing_line,b.id,c.color_number_id,d.serving_company
        ORDER BY d.sewing_line, c.color_number_id, d.serving_company";
        //echo $production_sql;
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
		// echo $sql_order_qnty;die();
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
				
				<tr class="form_caption">
					<td align="center"><p style="font-size:21px; font-weight:bold;"><? echo $companyArr[$company_id]; ?>Sewing Input</p></td> 
				</tr>
				<!-- <tr class="form_caption">
					<td align="center"><p style="font-size:18px; font-weight:bold;"><? echo "Date: (As On : ".change_date_format( str_replace("'","",trim($txt_date)) ).")"; ?></p></td> 
				</tr> -->
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
					<th width="100" style="color:red;">TODAY OUTPUT</th>
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
			
		</div>
		</fieldset>  
	 <?
    	  

    } 
	$floor_name = implode(',', $floor_arr);
	$floor_wise_total = implode(',', $floor_total_arr);
		
	
	foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,ob_get_contents());
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "####".$name;
	exit();
}

?>