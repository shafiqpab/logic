<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();	 
}

if ($action=="load_drop_down_buyer")
{
	$ex_data=explode('**',$data);
	if($ex_data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	}
	else if($ex_data[1]==2)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",1,"" );
	}
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
//--------------------------------------------------------------------------------------------------------------------

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
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Ship/Delv Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
							 if($ordType==1)
							 {
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
							 }
							 else if($ordType==2)
							 {
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
							 }
							?>
                        </td>                 
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $yearID; ?>'+'**'+'<? echo $ordType; ?>', 'create_order_no_search_list_view', 'search_div', 'daily_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	if($data[7]==1)
	{
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
			
		$start_date =trim($data[4]);
		$end_date =trim($data[5]);	
		
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
			}
		}
		else
		{
			$date_cond="";
		}
		
		$search_year=$data[6];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date) as year,";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2) 
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			if($search_year!=0)$year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else 
			$year_field="";//defined Later
			
		
		$arr=array(0=>$company_arr,1=>$buyer_arr);
			
		$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond order by b.id, b.pub_shipment_date";
			
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
	}
	else if ($data[7]==2)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.party_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.party_id=$data[1]";
		}
		
		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";
	
		if($search_by==1) 
			$search_field="b.order_no"; 
		else if($search_by==2) 
			$search_field="b.cust_style_ref"; 	
		else 
			$search_field="a.job_no_prefix_num";
			
		$start_date =trim($data[4]);
		$end_date =trim($data[5]);	
		
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
			}
		}
		else
		{
			$date_cond="";
		}
		
		$search_year=$data[6];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date) as year,";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2) 
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			if($search_year!=0)$year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else 
			$year_field="";//defined Later
			
		
		$arr=array(0=>$company_arr,1=>$buyer_arr);
			
		$sql= "select b.id, $year_field a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, b.cust_style_ref, b.order_no, b.delivery_date from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond order by b.id DESC";
			
		echo create_list_view("tbl_list_search", "Company,Party Name,Year,Job No,Cust. Style Ref., Po No, Delivery Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,order_no", "", 1, "company_id,party_id,0,0,0,0,0", $arr , "company_id,party_id,year,job_no_prefix_num,cust_style_ref,order_no,delivery_date", "",'','0,0,0,0,0,0,3','',1) ;
	}
   exit(); 
}

if($action=="job_no_search_popup")
{
	echo load_html_head_contents("Job No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
	  	{ 
			document.getElementById('hide_job_no').value=id;
			parent.emailwindow.hide();
	  	}
    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="120">Please Enter Order No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
							 if($ordType==1)
							 {
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
							 }
							 else if($ordType==2)
							 {
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
							 }
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
					?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $yearID; ?>'+'**'+'<? echo $ordType; ?>', 'create_job_no_search_list_view', 'search_div', 'daily_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	if($data[5]==1)
	{
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
			
		$start_date =trim($data[4]);
		$end_date =trim($data[5]);	
		
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
			}
		}
		else
		{
			$date_cond="";
		}
		
		$search_year=$data[4];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date) as year,";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2) 
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			if($search_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else 
			$year_field="";//defined Later
			
		
		$arr=array(0=>$company_arr,1=>$buyer_arr);
			
		$sql= "select $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by a.id DESC";
			
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "120,130,50,60","560","280",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0,", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no", "",'','0,0,0,0,0','') ;
	}
	else if($data[5]==2)
	{
		if($data[1]==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.party_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.party_id=$data[1]";
		}
		
		$search_by=$data[2];
		$search_string="%".trim($data[3])."%";
	
		if($search_by==1) 
			$search_field="b.order_no"; 
		else if($search_by==2) 
			$search_field="b.cust_style_ref"; 	
		else 
			$search_field="a.job_no_prefix_num";
			
		$start_date =trim($data[4]);
		$end_date =trim($data[5]);	
		
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and b.delivery_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
			}
		}
		else
		{
			$date_cond="";
		}
		
		$search_year=$data[4];
		if($db_type==0)
		{
			$year_field="YEAR(a.insert_date)";
			$style_cond="group_concat(b.cust_style_ref)";
			if($search_year!=0) $year_cond=" and YEAR(a.insert_date)='$search_year'"; else $year_cond="";
		}
		else if($db_type==2) 
		{
			$year_field="to_char(a.insert_date,'YYYY')";
			$style_cond="listagg((cast(b.cust_style_ref as varchar2(4000))),',') within group (order by b.cust_style_ref)";
			if($search_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')='$search_year'"; else $year_cond="";
		}
		else 
			$year_field="";//defined Later
		
		$arr=array(0=>$company_arr,1=>$buyer_arr);
			
		$sql= "select $year_field as year, a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, $style_cond as cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond group by a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, a.insert_date order by a.id DESC";
			
		echo create_list_view("tbl_list_search", "Company,Party Name,Year,Job No,Cust. Style Ref.", "120,130,50,60","560","280",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_id,party_id,0,0,0,", $arr , "company_id,party_id,year,job_no_prefix_num,cust_style_ref", "",'','0,0,0,0,0','') ;
	}
   exit(); 
}

$supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no"  );
$floor_details=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name"  );
$reqsn_details=return_library_array( "select knit_id, requisition_no from ppl_yarn_requisition_entry group by knit_id,requisition_no", "knit_id", "requisition_no"  );
$color_details=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );


$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_type=str_replace("'","",$cbo_type);
	$cbo_year=str_replace("'","",$cbo_year); 
	$txt_job=str_replace("'","",$txt_job);
	$txt_order=str_replace("'","",$txt_order);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$report_type=str_replace("'","",$report_type);
	if($report_type==1)
	{
		$tbl_width=2220+count($shift_name)*155;
		if($cbo_type==1 || $cbo_type==0)
		{
			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_cond=" and f.job_no_prefix_num='$txt_job' "; else $job_cond="";
			if($txt_order!="") $order_cond=" and e.po_number like '%$txt_order%' "; else $order_cond="";
			if (str_replace("'","",$cbo_floor_id)==0) $floor_id=''; else $floor_id=" and b.floor_id=$cbo_floor_id";
			
			if($db_type==0)
			{
				$year_field="YEAR(f.insert_date)";
				$year_field_sam="YEAR(a.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(f.insert_date)=$cbo_year"; else $job_year_cond="";
			}
			else if($db_type==2)
			{
				$year_field="to_char(f.insert_date,'YYYY')";
				$year_field_sam="to_char(a.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_cond=" and to_char(f.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
			}
			else $year_field="";
			
			$from_date=$txt_date_from;
			if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;
			
			if(str_replace("'","",$cbo_knitting_source)==0) $source="%%"; else $source=str_replace("'","",$cbo_knitting_source);
			
			$date_con="";
			if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
			
			$machine_details=array();
			$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			}
	
			$po_array=array();
			$po_data=sql_select("select a.job_no, a.job_no_prefix_num, a.style_ref_no from wo_po_details_master a where a.company_name=$cbo_company_name");
			foreach($po_data as $row)
			{
				//$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
				//$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']=$row[csf('style_ref_no')];
			}
			
			$po_sub_array=array();
			$po_data=sql_select("select a.job_no, a.job_no_prefix_num, $year_field_sam as year, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name ");
			foreach($po_data as $row)
			{
				$po_sub_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')];
				$po_sub_array[$row[csf('id')]]['year']=$row[csf('year')];
				$po_sub_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$po_sub_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
			}
			//var_dump($po_sub_array);
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
			
			$knit_plan_arr=array();
			$plan_data=sql_select("select id, color_range, stitch_length from ppl_planning_info_entry_dtls");
			foreach($plan_data as $row)
			{
				$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
				$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')]; 
			}
		}
		if($cbo_type==2 || $cbo_type==0)
		{
			$machine_details=array();
			$machine_data=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
				$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
			}
			
			if($db_type==0)
			{
				$year_sub_field="YEAR(e.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(e.insert_date)=$cbo_year"; else $job_year_cond="";
			}
			else if($db_type==2)
			{
				$year_sub_field="to_char(e.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_sub_cond=" and to_char(e.insert_date,'YYYY')=$cbo_year";  else $job_year_sub_cond="";
			}
			else $year_sub_field="";
			
			if($db_type==0)
			{
				$select_color=", b.color_id as color_id";
				$group_color=", b.color_id";
			}
			else if($db_type==2)
			{
				$select_color=", nvl(b.color_id,0) as color_id";
				$group_color=", nvl(b.color_id,0)";
			}
			
			$from_date=$txt_date_from;
			if($txt_date_to=="") $to_date=$from_date; else $to_date=$txt_date_to;
			
			if($from_date!="" && $to_date!="") $date_con_sub=" and a.product_date between '$from_date' and '$to_date'";	else $date_con_sub="";
			
			if ($cbo_floor_id!=0) $floor_id_cond=" and b.floor_id='$cbo_floor_id'"; else $floor_id_cond="";
			if (str_replace("'","",$cbo_buyer_name)!=0) $buyer_id_cond=" and a.party_id=$cbo_buyer_name"; else $buyer_id_cond="";
			
			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_no_cond=" and e.job_no_prefix_num='$txt_job' "; else $job_no_cond="";
			if($txt_order!="") $order_no_cond=" and d.order_no like '%$txt_order%' "; else $order_no_cond="";
		}
		if ($cbo_type==0)
		{
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850px">
				<tr>
				<td width="555">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550px" class="rpt_table" >
						<thead>
							<tr>
								<th colspan="6">Knit Production Summary (In-House + Outbound)</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="100">Buyer</th>
								<th width="90">Inhouse</th>
								<th width="90">Outbound-Subcon</th>
								<th width="90">Sample Without Order</th>
								<th width="100">Total</th>
							</tr>
						</thead>
					</table>
					<div style="width:570px; overflow-y:scroll; max-height:220px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550px" class="rpt_table" >
						<tbody>
						<?
							
							$sql_sample_samary=sql_select("select a.buyer_id, sum(case when  b.machine_no_id>0 $floor_id  then b.grey_receive_qnty end ) as sample_qty
							 from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.company_id=$cbo_company_name and a.knitting_source like '$source'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $date_con $floor_id $buyer_cond group by a.buyer_id ");
							 $subcon_buyer_samary=array();
							 foreach($sql_sample_samary as $inf)
							 {
								$subcon_buyer_samary[$inf[csf('buyer_id')]]+= $inf[csf('sample_qty')];
								$subcon_buyer_sammary['total']+= $inf[csf('sample_qty')];
							 }
							 
							 $sql_service_samary=sql_select("select a.buyer_id, sum(b.grey_receive_qnty) as service_qty
							 from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=22 and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_con $buyer_cond group by a.buyer_id");
							 $service_buyer_data=array();
							 foreach($sql_service_samary as $row)
							 {
								 $service_buyer_data[$row[csf("buyer_id")]]=$row[csf("service_qty")];
							 }
						//echo $sql_sample_samary;die;
						
							$sql_qty="Select a.buyer_id, sum(case when a.knitting_source=1 and b.machine_no_id>0 $floor_id  then c.quantity end ) as qtyinhouse, sum(case when a.knitting_source=3 then c.quantity end ) as qtyoutbound from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' $date_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_cond $job_cond $order_cond $job_year_cond group by a.buyer_id ";
							//echo $sql_qty; 
							$k=1;
							$sql_result=sql_select( $sql_qty);
							foreach($sql_result as $rows)
							{
							   if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							   $out_bound_qnty=0;
							   $out_bound_qnty=$rows[csf('qtyoutbound')]+$service_buyer_data[$rows[csf('buyer_id')]];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="40"><? echo $k; ?></td>
									<td width="100"><? echo $buyer_arr[$rows[csf('buyer_id')]]; ?></td>
									<td width="90" align="right"><? echo number_format($rows[csf('qtyinhouse')],2,'.',''); ?>&nbsp;</td>
									<td width="90" align="right"><? echo number_format($out_bound_qnty,2,'.',''); ?>&nbsp;</td>
									<td width="90" align="right"><? echo number_format($subcon_buyer_samary[$rows[csf('buyer_id')]],2,'.',''); $tot_summ=$rows[csf('qtyinhouse')]+$out_bound_qnty+$subcon_buyer_samary[$rows[csf('buyer_id')]]; ?>&nbsp;</td>
									<td width="100" align="right"><? echo  number_format($tot_summ,2,'.',''); ?>&nbsp;</td>
								</tr>
								<?	
								$tot_qtyinhouse+=$rows[csf('qtyinhouse')];
								$tot_qtyoutbound+=$out_bound_qnty;
								$total_summ+=$tot_summ;
								unset($subcon_buyer_samary[$rows[csf('buyer_id')]]);
								$k++;
							}
							if(count($subcon_buyer_samary)>0)
							{
								foreach($subcon_buyer_samary as $key=>$value)
								{
								   if ($k%2==0)  
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td width="40"><? echo $k; ?></td>
										<td width="100"><? echo $buyer_arr[$key]; ?></td>
										<td width="90" align="right">&nbsp;</td>
										<td width="90" align="right">&nbsp;</td>
										<td width="90" align="right"><? echo number_format($value,2,'.',''); ?>&nbsp;</td>
										<td width="100" align="right"><? echo number_format($value,2,'.',''); ?>&nbsp;</td>
									</tr>
								<?	
									$total_summ+=$value;
									$k++;
								}
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2" align="right"><strong>Total</strong></th>
								<th align="right"><? echo number_format($tot_qtyinhouse,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo number_format($tot_qtyoutbound,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo number_format($subcon_buyer_sammary['total'],2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo number_format($total_summ,2,'.',''); ?>&nbsp;</th>
							</tr>
							<tr>
								<th colspan="2"><strong>In %</strong></th>
								<th align="right"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
								<th align="right"><? $qtyoutbound_per=($tot_qtyoutbound/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
								<th align="right"><? $qtyoutbound_per=($subcon_buyer_sammary['total']/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
								<th align="right"><? echo "100 %"; ?></th>
							</tr>
						</tfoot>
					</table>
					</div>
				</td>
				<td width="50">&nbsp;</td>
				<td valign="top">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:260px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>SubCon Order (Inbound) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
						<thead>
							<tr>
								<th colspan="6">Knit Production Summary (Inbound)</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="120">Party </th>
								<th width="100">Total Inbound Production</th>
							</tr>
						</thead>
					</table>
					<div style="width:280px; overflow-y:scroll; max-height:220px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="260px" class="rpt_table" >
						<tbody>
						<?
						$sql_inhouse_sub_summ="select a.party_id, sum(b.product_qnty) as qntysubshift from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e 
							where a.id=b.mst_id and b.machine_id=c.id and b.order_id=d.id and e.subcon_job=d.job_no_mst and a.product_type=2 
							and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond
							group by a.party_id";
						//echo $sql_inhouse_sub_summ;
						$nameArray_inhouse_subcon_summ=sql_select( $sql_inhouse_sub_summ);
							//echo $sql_qty; 
							$k=1;
							
							foreach($nameArray_inhouse_subcon_summ as $rows)
							{
							   if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="40"><? echo $k; ?></td>
									<td width="120"><? echo $buyer_arr[$rows[csf('party_id')]]; ?></td>
									<td width="100" align="right"><? echo  number_format($rows[csf('qntysubshift')],2,'.',''); ?>&nbsp;</td>
								</tr>
							<?	
								$tot_qty_sub_summ+=$rows[csf('qntysubshift')];
								unset($subcon_buyer_samary[$rows[csf('buyer_id')]]);
								$k++;
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2" align="right"><strong>Total</strong></th>
								<th align="right"><? echo number_format($tot_qty_sub_summ,2,'.',''); ?>&nbsp;</th>
							</tr>
							<tr>
								<th colspan="2"><strong>In %</strong></th>
								<th align="right"><? echo "100 %"; ?></th>
							</tr>
						</tfoot>
					</table>
					</div>
				</td>
				</tr>
			</table>
			<br />
			<?
		}
		//ob_start();
		?> 
		<fieldset style="width:<? echo $tbl_width+20; ?>px;">
			<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
				<tr>
				   <td align="center" width="100%" colspan="36" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="36" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
				<tr> 
				   <td align="center" width="100%" colspan="36" class="form_caption" style="font-size:12px" ><strong><? echo "From ".str_replace("'","",$txt_date_from)." To ".str_replace("'","",$txt_date_to); ?></strong></td>
				</tr>
			</table>
			<?
		if($cbo_type==1 || $cbo_type==0)
		{
			if($template==1)
			{
				?>
				<div align="left" style="background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+140; ?>" class="rpt_table" id="table_head" >
						<thead>
							<tr>
								<th width="40" rowspan="2" id="chk_hide"></th>
								<th width="30" rowspan="2">SL</th>
								<th width="55" rowspan="2">Knitting Party</th>
								<th width="60" rowspan="2">M/C No</th>
								<th width="60" rowspan="2">Job No</th>
								<th width="70" rowspan="2">File No.</th>
								<th width="70" rowspan="2">Int. Reff. No.</th>
								<th width="60" rowspan="2">Year</th>
								<th width="70" rowspan="2">Buyer</th>
								<th width="100" rowspan="2">Style</th>
								<th width="110" rowspan="2">Order No</th>
								<th width="90" rowspan="2">Prod. Basis</th>
								<th width="110" rowspan="2">Booking No / Prog. No</th>
								<th width="60" rowspan="2">Prod. No</th>
								<th width="80" rowspan="2">Req. No.</th>
								<th width="80" rowspan="2">Yarn Count</th>
								<th width="90" rowspan="2">Yarn Brand</th>
								<th width="60" rowspan="2">Lot No</th>
								<th width="100" rowspan="2">Color Range</th>
								<th width="100" rowspan="2">Fabric Color</th>
								<th width="150" rowspan="2">Fabric Type</th>
								<th width="50" rowspan="2">M/C Dia</th>
								<th width="80" rowspan="2">M/C Gauge</th>
								<th width="50" rowspan="2">Fab. Dia</th>
								<th width="50" rowspan="2">Stitch</th>
								<th width="60" rowspan="2">Fin GSM</th>
								<?
								foreach($shift_name as $val)
								{
								?>
									<th width="150" colspan="2"><? echo $val; ?></th>
								<?	
								}
								?>
								<th width="150" colspan="2">No Shift</th>
								<th width="150" colspan="2">Total</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<?
								foreach($shift_name as $val)
								{
								?>
									<th width="50" rowspan="2">Roll</th>
									<th width="100" rowspan="2">Qnty</th>
								<?	
								}
								?>
								<th width="50" rowspan="2">Roll</th>
								<th width="100" rowspan="2">Qnty</th>
								<th width="50" rowspan="2">Roll</th>
								<th width="100" rowspan="2">Qnty</th>
							</tr>
						</thead>
					</table>
					<?
						$widths=$tbl_width+20;
						$html="
						
		<fieldset style='width:".$widths."px;'>
				<table cellpadding='0' cellspacing='0' width='".$tbl_width."'>
					<tr>
					   <td align='center' width='100%' colspan='36' class='form_caption' style='font-size:18px'>".$report_title."</td>
					</tr>
					<tr>
					   <td align='center' width='100%' colspan='36' class='form_caption' style='font-size:16px'>".$company_arr[str_replace("'","",$cbo_company_name)]."</td>
					</tr>
					<tr> 
					   <td align='center' width='100%' colspan='36' class='form_caption' style='font-size:12px' ><strong>"."From ".str_replace("'","",$txt_date_from)." To ".str_replace("'","",$txt_date_to)."</strong></td>
					</tr>
				</table>					
							
						
						
						<div align='left' style='background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;'><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong></div>
						
							<table border='1'>
								<tr>
								<th width='30' rowspan='2'>SL</th>
								<th width='55' rowspan='2'>Knitting Party</th>
								<th width='60' rowspan='2'>M/C No</th>
								<th width='60' rowspan='2'>Job No</th>
								<th width='70' rowspan='2'>File No.</th>
								<th width='70' rowspan='2'>Int. Reff. No.</th>
								<th width='60' rowspan='2'>Year</th>
								<th width='70' rowspan='2'>Buyer</th>
								<th width='100' rowspan='2'>Style</th>
								<th width='110' rowspan='2'>Order No</th>
								<th width='90' rowspan='2'>Prod. Basis</th>
								<th width='110' rowspan='2'>Booking No / Prog. No</th>
								<th width='60' rowspan='2'>Prod. No</th>
								<th width='80' rowspan='2'>Req. No.</th>
								<th width='80' rowspan='2'>Yarn Count</th>
								<th width='90' rowspan='2'>Yarn Brand</th>
								<th width='60' rowspan='2'>Lot No</th>
								<th width='100' rowspan='2'>Color Range</th>
								<th width='100' rowspan='2'>Fabric Color</th>
								<th width='150' rowspan='2'>Fabric Type</th>
								<th width='50' rowspan='2'>M/C Dia</th>
								<th width='80' rowspan='2'>M/C Gauge</th>
								<th width='50' rowspan='2'>Fab. Dia</th>
								<th width='50' rowspan='2'>Stitch</th>
								<th width='60' rowspan='2'>Fin GSM</th>";
								
								foreach($shift_name as $val)
								{
									$html.="<th width='150' colspan='2'>".$val."</th>";
								}
								$html.="
								<th width='150' colspan='2'>No Shift</th>
								<th width='150' colspan='2'>Total</th>
								<th rowspan='2'>Remarks</th>
							</tr>
							<tr>";
								foreach($shift_name as $val)
								{
									$html.="<th width='50'>Roll</th>
									<th width='100'>Qnty</th>";
								}
								$html.="
								<th width='50'>Roll</th>
								<th width='100'>Qnty</th>
								<th width='50'>Roll</th>
								<th width='100'>Qnty</th>
							</tr>";
					?>
					<div style="width:<? echo $tbl_width+160; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+140; ?>" class="rpt_table" id="table_body">
							<? 
								$i=1; $tot_rolla=''; $tot_rollb=''; $tot_rollc=''; $tot_rolla_qnty=0; $tot_rollb_qnty=0; $tot_rollc_qnty=0; $grand_tot_roll=''; $grand_tot_qnty=0;$tot_subcontract=0;
								$inside_outside_array=array(); $floor_array=array(); $receive_basis=array(0=>"Independent",1=>"Fabric Booking No",2=>"Knitting Plan");
								
								if($db_type==0)
								{
									$select_color=", b.color_id as color_id";
									$group_color=", b.color_id";
								}
								else if($db_type==2)
								{
									$select_color=", nvl(b.color_id,0) as color_id";
									$group_color=", nvl(b.color_id,0)";
								}
								
								$sql_inhouse="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id, b.floor_id $select_color, c.po_breakdown_id, d.machine_no as machine_name, f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(case when b.shift_name=0 then c.quantity else 0 end ) as qntynoshift, sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift";
								foreach($shift_name as $key=>$val)
								{
									$sql_inhouse.=", sum(case when b.shift_name=$key then b.no_of_roll end ) as roll".strtolower($val)."	
									, sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val);
								}
								$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond
								group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, c.po_breakdown_id, d.machine_no, d.seq_no order by b.floor_id,a.receive_date, d.seq_no";
								//echo $sql_inhouse;
								$sql_subcontract="select c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(c.quantity) as outqntyshift 
								from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f where c.po_breakdown_id=e.id and a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source=3 and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con  $buyer_cond $job_cond $order_cond $job_year_cond 
								group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id, c.po_breakdown_id order by b.floor_id,a.receive_date";
								
								
								$sql_service_receive="select c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(c.quantity) as outqntyshift 
								from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f where c.po_breakdown_id=e.id and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and a.entry_form=22 and c.entry_form=22 and c.trans_type=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con  $buyer_cond $job_cond $order_cond $job_year_cond 
								group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id, c.po_breakdown_id order by b.floor_id,a.receive_date";
											
								//echo $sql_service_receive;die;
								$sql_wout_order="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.stitch_length,b.brand_id, b.machine_no_id, b.floor_id $select_color, 0 as po_breakdown_id, d.machine_no as machine_name, '' as job_no_mst, '' po_number, 0 as qntynoshift, sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift	";	
								foreach($shift_name as $key=>$val)
								{
									$sql_wout_order.=", sum(case when b.shift_name=$key then b.no_of_roll end ) as roll".strtolower($val)."	
									,sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
								}
								
								$sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id  and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  $date_con $floor_id  $buyer_cond group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width ,d.machine_no, d.seq_no order by b.floor_id,a.receive_date, d.seq_no";
							// echo $sql_wout_order;
							 
								$nameArray_inhouse=sql_select( $sql_inhouse);
								$nameArray_subcontract=sql_select( $sql_subcontract);
								$nameArray_service_receive=sql_select( $sql_service_receive);
								$nameArray_without_order=sql_select( $sql_wout_order);
								
								if (count($nameArray_inhouse)>0)
								{
								?>
									<tr  bgcolor="#CCCCCC">
										<td colspan="37" align="left" ><b>In-House</b></td>
									</tr>    
								<?
								$html.="<tr  bgcolor='#CCCCCC'>
										<td align='left' ></td>
										<td colspan='33' align='left' ><b>In-House</b></td>
									</tr>";
									foreach ($nameArray_inhouse as $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											
										$count='';
										$yarn_count=explode(",",$row[csf('yarn_count')]);
										foreach($yarn_count as $count_id)
										{
											if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
										}
										
										$reqsn_no=""; $stitch_length=""; $color="";
										if($row[csf('receive_basis')]==2)
										{
											$reqsn_no=$reqsn_details[$row[csf('booking_id')]]; 
											$stitch_length=$knit_plan_arr[$row[csf('booking_id')]]['sl']; 
											$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
										}
			
										if($row[csf('knitting_source')]==1)
											$knitting_party=$company_arr[$row[csf('knitting_company')]];
										else if($row[csf('knitting_source')]==3)
											$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
										else
											$knitting_party="&nbsp;";
										
										if(!in_array($row[csf('floor_id')],$floor_array))
										{
											if($i!=1)
											{
											?>
												<tr class="tbl_bottom">
													<td colspan="26" align="right"><b>Floor Total</b></td>
													<?
													$floor_tot_qnty_row=0;
													foreach($shift_name as $key=>$val)
													{
														$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
													?>
														<td align="right">&nbsp;</td>
														<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
	
													<?
													}
													?>
													<td align="right">&nbsp;</td>
													<td align="right"><? echo number_format($noshift_total,2,'.',''); ?> </td>
													<td align="right">&nbsp;</td>
													<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
													<td>&nbsp;</td>
													
												</tr>
												
										<?
												$html.="<tr>
													
													<td colspan='25' align='right'><b>Floor Total</b></td>";
													
													$floor_tot_qnty_row=0;
													foreach($shift_name as $key=>$val)
													{
														$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
														$html.="<td align='right'>&nbsp;</td>
														<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
													
													}
													
													$html.="
													<td align='right'>&nbsp;</td>
													<td align='right'>".number_format($noshift_total,2,'.','')."</td>
													<td align='right'>&nbsp;</td>
													<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
													<td>&nbsp;</td>
												</tr>";
												
												unset($noshift_total);
												unset($floor_tot_roll);
											}	
										?>
											<tr><td colspan="37" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") echo "Without Floor"; else echo $floor_details[$row[csf('floor_id')]]; ?></b></td></tr>
										<?
										$html.="<tr><td colspan='36' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b></b></td></tr>";
											$floor_array[$i]=$row[csf('floor_id')];
										}	
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
											<td width="40" align="center" valign="middle" id="chk_hide_dtls">
												<!--<input type="checkbox" id="tbl_<? echo $i;?>" onClick="selected_row(<? //echo $i; ?>);" />-->
												<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
												<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('recv_number_prefix_num')]; ?>" />
												<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
												<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "1"; ?>" /></td>
											<td width="30"><? echo $i; ?></td>
											<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
											<td align="center" width="60"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
											<td width="70"><? echo $row[csf('file_no')]; ?></td>
											<td width="70"><? echo $row[csf('grouping')]; ?></td>
											<td align="center" width="60"><p><? echo $row[csf('year')]; ?></p></td>
											<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
											<td width="100"><p><? echo $po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']; ?></p></td>
											<td width="110"><p><? echo $row[csf('po_number')];//$po_array[$row[csf('po_breakdown_id')]]['no']; ?></p></td>
											<td width="90" id="prog_id_<? echo $i; ?>"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
											<td width="110" id="booking_no_<? echo $i; ?>"><P><? echo $row[csf('booking_no')]; ?></P></td>
											<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
											<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
											<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
											<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
											<td width="60" id="yarn_lot_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
											<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;<? echo $color; ?></p></td>
											<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
											<? 
											//echo $color_details[$row[csf("color_id")]]; 
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color; 
											
											?></p></td>
											<td width="150" id="feb_type_<? echo $i; ?>"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
											<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $machine_details[$row[csf('machine_no_id')]]['dia']; ?></p></td>
											<td width="80" id="mc_gause_<? echo $i; ?>"><p>&nbsp;<? echo $machine_details[$row[csf('machine_no_id')]]['gauge']; ?></p></td>
											<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
											<td width="50" id="stitch_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
											<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html.="<tr> 
											<td width='30'>".$i."</td>
											<td width='55'><p>".$knitting_party."&nbsp;</p></td>
											<td width='60'><p>".$row[csf('machine_name')]."</p></td>
											<td width='60'><p>".$row[csf('job_no_prefix_num')]."</p></td>
											<td width='70'><p>".$row[csf('file_no')]."</p></td>
											<td width='70'><p>".$row[csf('grouping')]."</p></td>
											<td width='60'><p>".$row[csf('year')]."</p></td>
											<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
											<td width='100'><p>".$po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']."</p></td>
											<td width='110'><p>".$row[csf('po_number')]."</p></td>
											<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
											<td width='110'><P>".$row[csf('booking_no')]."</P></td>
											<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
											<td width='80'>".$reqsn_no."</td>
											<td width='80'><p>".$count."</p></td>
											<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
											<td width='100'><p>&nbsp;".$color."</p></td>";
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
											<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
											<td width='50'><p>&nbsp;".$machine_details[$row[csf('machine_no_id')]]['dia']."</p></td>
											<td width='80'><p>&nbsp;".$machine_details[$row[csf('machine_no_id')]]['gauge']."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
											$row_tot_roll=0; 
											$row_tot_qnty=0; 
											foreach($shift_name as $key=>$val)
											{
												$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$source_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$source_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$row_tot_roll+=$row[csf('roll'.strtolower($val))]; 
												$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))]; 
											?>
												<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="100" align="right" >
												<? 
												echo number_format($row[csf('qntyshift'.strtolower($val))],2);
												$machineSamarryDataArr[$row[csf('machine_name')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
												?>
												</td>
											<?
	
											$html.="<td width='50' align='right' >".$row[csf('roll'.strtolower($val))]."</td>
												<td width='100' align='right' >".number_format($row[csf('qntyshift'.strtolower($val))],2)."</td>";
											}
											?>
											<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>
											<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
											<td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
											<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.',''); ?></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
										</tbody>
										<?
										$html.="
											<td width='50' align='right'>".$row[csf('rollnoshift')]."</td>
											<td width='100' align='right'>".number_format($row[csf('qntynoshift')],2)."</td>
											<td width='50' align='right'>".$row_tot_roll."</td>
											<td width='100' align='right'>".number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','')."</td>
											<td><p>".$row[csf('remarks')]."&nbsp;</p></td>
										</tr>
										</tbody>";
											
										$grand_tot_roll+=$row_tot_roll; 
										$grand_tot_qnty+=$row_tot_qnty+$row[csf('qntynoshift')];
										
										$source_grand_tot_roll+=$row_tot_roll; 
										$source_grand_tot_qnty+=$row_tot_qnty;
										
										$noshift_total+=$row[csf('qntynoshift')];
										
										$grand_tot_floor_roll+=$row_tot_roll; 
										$grand_tot_floor_qnty+=$row_tot_qnty;
										$total_qty_noshift+=$row[csf('qntynoshift')];
										
										$i++;
									}
								
								?>
									<tr class="tbl_bottom">
										<td></td>
										<td colspan="25" align="right"><b>Floor Total</b></td>
										<?
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
										?>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
										<?
										}
										?>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
										<td>&nbsp;</td>
									</tr>	
									<tr class="tbl_bottom">
										<td></td>
										<td colspan="25" align="right"><b>In House Total</b></td>
										<?
										//$source_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$source_tot_qnty+=$source_tot_roll[$key]['qty'];
											$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
										?>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($source_tot_qnty_row,2,'.',''); ?></td>
										<?
										unset($source_tot_qnty_row);
										}
										
										
										?>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($total_qty_noshift,2,'.',''); ?> </td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($source_tot_qnty,2,'.',''); ?></td>
										<td>&nbsp;</td>
									</tr>
									<?
									$html.="<tr>
										<td colspan='25' align='right'><b>Floor Total</b></td>";
										
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
										
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
										}
										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($noshift_total,2,'.','')."</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($floor_tot_qnty_row,2,'.','')."</td>
										<td>&nbsp;</td>
									</tr>	
									<tr>
										<td colspan='25' align='right'><b>In House Total</b></td>";
										//$source_tot_qnty_row=0;
										$source_tot_qnty=0;
										foreach($shift_name as $key=>$val)
										{
											$source_tot_qnty+=$source_tot_roll[$key]['qty'];
											$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($source_tot_qnty_row,2,'.','')."</td>";
										
										unset($source_tot_qnty_row);
										}
										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($total_qty_noshift,2,'.','')."</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($source_tot_qnty,2,'.','')."</td>
										<td>&nbsp;</td>
									</tr>"; 
								}
								
								if(count($nameArray_subcontract)>0)
								{
								?>
									<tr  bgcolor="#CCCCCC">
										<td colspan="37" align="left"><b>Outbound-Subcontract Production</b></td>
									</tr>  
									  
								<?
								$html.="<tr  bgcolor='#CCCCCC'>
										<td colspan='36' align='left'><b>Outbound-Subcontract</b></td>
									</tr>";
									foreach ($nameArray_subcontract as $row)
									{
										if ($i%2==0)  
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
											
										$count='';
										$yarn_count=explode(",",$row[csf('yarn_count')]);
										foreach($yarn_count as $count_id)
										{
											if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
										}
										
										$reqsn_no=""; $stitch_length=""; $color="";
										if($row[csf('receive_basis')]==2)
										{
											$reqsn_no=$reqsn_details[$row[csf('booking_id')]]; 
											$stitch_length=$knit_plan_arr[$row[csf('booking_id')]]['sl']; 
											$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
										}
			
										if($row[csf('knitting_source')]==1)
											$knitting_party=$company_arr[$row[csf('knitting_company')]];
										else if($row[csf('knitting_source')]==3)
											$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
										else
											$knitting_party="&nbsp;";
										
									?>  
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
											<td width="40" align="center" valign="middle">
												<!--<input type="checkbox" id="tbl_<? echo $i;?>" onClick="selected_row(<? //echo $i; ?>);" />-->
												<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
												<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('prod_id')]; ?>" />
												<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
												<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "3"; ?>" />
											<td width="30"><? echo $i; ?></td>
											<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
											<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['job_no']; ?></p></td>
											
											<td width="70"><? echo $row[csf('file_no')]; ?></td>
											<td width="70"><? echo $row[csf('grouping')]; ?></td>
											
											<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['year']; ?></p></td>
											<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
											<td width="100"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']; ?></p></td>
											<td width="110"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['po_number']; ?></p></td>
											<td width="90"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
											<td width="110"><P><? echo $row[csf('booking_no')]; ?></P></td>
											<td width="60"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
											<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
											<td width="80"><p><? echo $count; ?>&nbsp;</p></td>
											<td width="90"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
											<td width="100"><p>&nbsp;<? echo $color; ?></p></td>
											<td width="100"><p>&nbsp;
											<? 
											//echo $color_details[$row[csf("color_id")]]; 
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
											<td width="50"><p>&nbsp;<? echo $machine_details[$row[csf('machine_no_id')]]['dia']; ?></p></td>
											<td width="80"><p>&nbsp;<? echo $machine_details[$row[csf('machine_no_id')]]['gauge']; ?></p></td>
											<td width="50"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
											<td width="50"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html.="<tr> 
											<td width='30'>".$i."</td>
											<td width='55'><p>".$knitting_party."&nbsp;</p></td>
											<td width='60'><p>".$row[csf('machine_name')]."</p></td>
											<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['job_no']."</p></td>
											<td width='70'><p>".$row[csf('file_no')]."</p></td>
											<td width='70'><p>".$row[csf('grouping')]."</p></td>
											<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['year']."</p></td>
											<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
											<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
											<td width='110'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['po_number']."</p></td>
											<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
											<td width='110'><P>".$row[csf('booking_no')]."</P></td>
											<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
											<td width='80'>".$reqsn_no."</td>
											<td width='80'><p>".$count."</p></td>
											<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
											<td width='100'><p>&nbsp;".$color."</p></td>";
											
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
											<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
											<td width='50'><p>&nbsp;".$machine_details[$row[csf('machine_no_id')]]['dia']."</p></td>
											<td width='80'><p>&nbsp;".$machine_details[$row[csf('machine_no_id')]]['gauge']."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
		
											$row_tot_roll=0; 
											$row_tot_qnty=0; 
											foreach($shift_name as $key=>$val)
											{
												/*$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$source_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$source_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$row_tot_roll+=$row[csf('roll'.strtolower($val))]; 
												$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))]; */
											?>
												<td width="50" align="right"><? //echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="100" align="right"><? //echo number_format($row[csf('outqntyshift'.strtolower($val))],2); ?></td>
												
											<?
											$html.="<td width='50' align='right' ></td>
												<td width='100' align='right' ></td>";
											}
											?>
											<td width="50" align="right"><? echo $row_tot_roll; ?></td>
											<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
											<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
											<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
										<?
										$html.="
											<td width='50' align='right'>".$row_tot_roll."</td>
											<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>
											<td width='50' align='right'>".$row[csf('no_of_roll')]."</td>
											<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>
											<td><p>".$row[csf('remarks')]."&nbsp;</p></td>
										</tr>";
										//$grand_tot_roll+=$row[csf('no_of_roll')];
										$grand_tot_qnty+=$row[csf('outqntyshift')];
										
										//$source_grand_tot_roll+=$row[csf('no_of_roll')];
										$source_grand_tot_qnty+=$row[csf('outqntyshift')];
										
										$tot_subcontract+=$row[csf('outqntyshift')]; 
										$grand_tot_floor_qnty+=$row_tot_qnty;
										$i++;
									}
									
								?>
									<tr class="tbl_bottom">
										<td colspan="26" align="right"><b>Outbound-Subcontract Total</b></td>
										<?
										$source_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											//$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
										?>
											<td align="right">&nbsp;</td>
											<td align="right"><? //echo number_format($source_tot_roll[$key]['qty'],2,'.',''); ?></td>
										<?
										}
										?>
										<td align="right">&nbsp; </td>
										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($tot_subcontract,2,'.',''); ?></td>
										<td>&nbsp;</td>
									</tr>                            		
								<?
								$html.="<tr>
										<td colspan='25' align='right'><b>Outbound-Subcontract Total</b></td>";
										
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'></td>";
										}
										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($tot_subcontract,2,'.','')."</td>
										<td>&nbsp;</td>
									</tr>"; 
								} 
								
								if(count($nameArray_service_receive)>0)
								{
								?>
									<tr  bgcolor="#CCCCCC">
										<td colspan="37" align="left"><b>Outbound-Subcontract Receive</b></td>
									</tr>  
									  
								<?
								$html.="<tr  bgcolor='#CCCCCC'>
										<td colspan='36' align='left'><b>Outbound-Subcontract</b></td>
									</tr>";
									foreach ($nameArray_service_receive as $row)
									{
										if ($i%2==0)  
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
											
										$count='';
										$yarn_count=explode(",",$row[csf('yarn_count')]);
										foreach($yarn_count as $count_id)
										{
											if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
										}
										
										$reqsn_no=""; $stitch_length=""; $color="";
										if($row[csf('receive_basis')]==2)
										{
											$reqsn_no=$reqsn_details[$row[csf('booking_id')]]; 
											$stitch_length=$knit_plan_arr[$row[csf('booking_id')]]['sl']; 
											$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
										}
			
										if($row[csf('knitting_source')]==1)
											$knitting_party=$company_arr[$row[csf('knitting_company')]];
										else if($row[csf('knitting_source')]==3)
											$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
										else
											$knitting_party="&nbsp;";
										
									?>  
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
											<td width="40" align="center" valign="middle">
												<!--<input type="checkbox" id="tbl_<? echo $i;?>" onClick="selected_row(<? //echo $i; ?>);" />-->
												<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
												<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('prod_id')]; ?>" />
												<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
												<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "3"; ?>" />
											<td width="30"><? echo $i; ?></td>
											<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
											<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['job_no']; ?></p></td>
											
											<td width="70"><? echo $row[csf('file_no')]; ?></td>
											<td width="70"><? echo $row[csf('grouping')]; ?></td>
											
											<td align="center" width="60"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['year']; ?></p></td>
											<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
											<td width="100"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']; ?></p></td>
											<td width="110"><p><? echo $po_sub_array[$row[csf('po_breakdown_id')]]['po_number']; ?></p></td>
											<td width="90"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
											<td width="110"><P><? echo $row[csf('booking_no')]; ?></P></td>
											<td width="60"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
											<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
											<td width="80"><p><? echo $count; ?>&nbsp;</p></td>
											<td width="90"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
											<td width="100"><p>&nbsp;<? echo $color; ?></p></td>
											<td width="100"><p>&nbsp;
											<? 
											//echo $color_details[$row[csf("color_id")]]; 
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
											<td width="50"><p>&nbsp;<? echo $machine_details[$row[csf('machine_no_id')]]['dia']; ?></p></td>
											<td width="80"><p>&nbsp;<? echo $machine_details[$row[csf('machine_no_id')]]['gauge']; ?></p></td>
											<td width="50"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
											<td width="50"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html.="<tr> 
											<td width='30'>".$i."</td>
											<td width='55'><p>".$knitting_party."&nbsp;</p></td>
											<td width='60'><p>".$row[csf('machine_name')]."</p></td>
											<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['job_no']."</p></td>
											<td width='70'><p>".$row[csf('file_no')]."</p></td>
											<td width='70'><p>".$row[csf('grouping')]."</p></td>
											<td width='60'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['year']."</p></td>
											<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
											<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
											<td width='110'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['po_number']."</p></td>
											<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
											<td width='110'><P>".$row[csf('booking_no')]."</P></td>
											<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
											<td width='80'>".$reqsn_no."</td>
											<td width='80'><p>".$count."</p></td>
											<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
											<td width='100'><p>&nbsp;".$color."</p></td>";
											
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
											<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
											<td width='50'><p>&nbsp;".$machine_details[$row[csf('machine_no_id')]]['dia']."</p></td>
											<td width='80'><p>&nbsp;".$machine_details[$row[csf('machine_no_id')]]['gauge']."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>";
		
											$row_tot_roll=0; 
											$row_tot_qnty=0; 
											foreach($shift_name as $key=>$val)
											{
												/*$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$source_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$source_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$row_tot_roll+=$row[csf('roll'.strtolower($val))]; 
												$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))]; */
											?>
												<td width="50" align="right"><? //echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="100" align="right"><? //echo number_format($row[csf('outqntyshift'.strtolower($val))],2); ?></td>
												
											<?
											$html.="<td width='50' align='right' ></td>
												<td width='100' align='right' ></td>";
											}
											?>
											<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
											<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
											<td width="50" align="right"><? echo $row[csf('no_of_roll')]; ?></td>
											<td width="100" align="right"><? echo number_format($row[csf('outqntyshift')],2); ?></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
										<?
										$html.="
											<td width='50' align='right'>".$row[csf('no_of_roll')]."</td>
											<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>
											<td width='50' align='right'>".$row[csf('no_of_roll')]."</td>
											<td width='100' align='right'>".number_format($row[csf('outqntyshift')],2,'.','')."</td>
											<td><p>".$row[csf('remarks')]."&nbsp;</p></td>
										</tr>";
										//$grand_tot_roll+=$row[csf('no_of_roll')];
										$grand_tot_qnty+=$row[csf('outqntyshift')];
										//$source_grand_tot_roll+=$row[csf('no_of_roll')];
										$source_grand_tot_qnty+=$row[csf('outqntyshift')];
										
										$tot_subcontract+=$row[csf('outqntyshift')];
										$total_service_subcontact+= $row[csf('outqntyshift')];
										$grand_tot_floor_qnty+=$row_tot_qnty;
										$i++;
									}
									
								?>
									<tr class="tbl_bottom">
										<td colspan="26" align="right"><b>Outbound-Subcontract Total</b></td>
										<?
										$source_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											//$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
										?>
											<td align="right">&nbsp;</td>
											<td align="right"><? //echo number_format($source_tot_roll[$key]['qty'],2,'.',''); ?></td>
										<?
										}
										?>
										<td align="right">&nbsp; </td>
										<td align="right"><? echo number_format($total_service_subcontact,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($tot_subcontract,2,'.',''); ?></td>
										<td>&nbsp;</td>
									</tr>                            		
								<?
								$html.="<tr>
										<td colspan='25' align='right'><b>Outbound-Subcontract Total</b></td>";
										
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$html.="<td align='right'>&nbsp;</td>
											<td align='right'></td>";
										}
										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($total_service_subcontact,2,'.','')."</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($tot_subcontract,2,'.','')."</td>
										<td>&nbsp;</td>
									</tr>"; 
								} 
								
								 unset($floor_array); $total_qty_noshift=0; 
								 unset($floor_tot_roll); unset($noshift_total); unset($source_tot_roll);  $source_tot_qnty=0;
								 $j=0;
								if (count($nameArray_without_order)>0)
								{
									//$floor_tot_qnty_row=0; $noshift_total=0;
									//unset($floor_tot_roll);unset($tot_roll); unset($row_tot_roll);
								?>
									<tr  bgcolor="#CCCCCC">
										<td colspan="37" align="left" ><b>Sample Without Order</b></td>
									</tr>    
								<?
								$html.="<tr  bgcolor='#CCCCCC'>
										<td colspan='36' align='left' ><b>Sample Without Order</b></td>
									</tr>";
									foreach ($nameArray_without_order as $row)
									{
										if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											
										$count='';
										$yarn_count=explode(",",$row[csf('yarn_count')]);
										foreach($yarn_count as $count_id)
										{
											if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
										}
										
										$reqsn_no=""; $stitch_length=""; $color="";
										if($row[csf('receive_basis')]==2)
										{
											$reqsn_no=$reqsn_details[$row[csf('booking_id')]]; 
											//$stitch_length=$knit_plan_arr[$row[csf('booking_id')]]['sl']; 
											$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
										}
			
										if($row[csf('knitting_source')]==1)
											$knitting_party=$company_arr[$row[csf('knitting_company')]];
										else if($row[csf('knitting_source')]==3)
											$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
										else
											$knitting_party="&nbsp;";
										
										if(!in_array($row[csf('floor_id')],$floor_array))
										{
											if($j!=1)
											{
											?>
												<tr class="tbl_bottom">
													<td colspan="26" align="right"><b>Floor Total</b></td>
													<?
													$html.="<tr>
													<td colspan='25' align='right'><b>Floor Total</b></td>";
													$floor_tot_qnty_row=0;
													foreach($shift_name as $key=>$val)
													{
														$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
													?>
														<td align="right">&nbsp;</td>
														<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
													<?
													$html.="<td align='right'>&nbsp;</td>
														<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
													}
													?>
													<td align="right">&nbsp;</td>
													<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
													<td align="right">&nbsp;</td>
													<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
													<td>&nbsp;</td>
												</tr>
											<?
											$html.="
													<td align='right'>&nbsp;</td>
													<td align='ight'>".number_format($floor_tot_qnty_row+$noshift_total,2,'.','')."</td>
													<td>&nbsp;</td>
												</tr>";
												unset($noshift_total);
												unset($floor_tot_roll);
											}	
										?>
											<tr><td colspan="35" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php if($row[csf('floor_id')]==0 ||$row[csf('floor_id')]=="") echo "Without Floor"; else echo $floor_details[$row[csf('floor_id')]]; ?></b></td></tr>
										<?	
										$html.="<tr><td colspan='34' style='font-size:14px' bgcolor='#CCCCAA'>&nbsp;<b>".$floor_details[$row[csf('floor_id')]]."</b></td></tr>";
											$floor_array[$i]=$row[csf('floor_id')]; 
										}	
																?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
											<td width="40" align="center" valign="middle" id="">
												<!--<input type="checkbox" id="tbl_<? echo $i;?>" onClick="selected_row(<? //echo $i; ?>);" />-->
												<input id="promram_id_<? echo $i;?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('booking_id')]; ?>" />
												<input id="production_id_<? echo $i;?>" name="production_id[]" type="hidden" value="<? echo $row[csf('recv_number_prefix_num')]; ?>" />
												<input id="job_no_<? echo $i;?>" name="job_no[]" type="hidden" value="<? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?>" />
												<input type="hidden" id="source_<? echo $i; ?>" name="source[]" value="<? echo "2"; ?>" /></td>
											   <!-- 2 mean without order-->
											<td width="30"><? echo $i; ?></td>
											<td width="55"><p><? echo $knitting_party; ?>&nbsp;</p></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
											<td align="center" width="60"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
											<td width="70">&nbsp;</td>
											<td width="70">&nbsp;</td>
											
											<td align="center" width="60"><p>&nbsp;<? echo $row[csf('year')]; ?></p></td>
											<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
											<td width="100"><p>&nbsp;<? echo $po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']; ?></p></td>
											<td width="110"><p>&nbsp;<? echo $row[csf('po_number')];//$po_array[$row[csf('po_breakdown_id')]]['no']; ?></p></td>
											<td width="90" id="prog_id_<? echo $i; ?>"><P><? echo $receive_basis[$row[csf('receive_basis')]]; ?></P></td>
											<td width="110" id="booking_no_<? echo $i; ?>"><P><? echo $row[csf('booking_no')]; ?></P></td>
											<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('recv_number_prefix_num')]; ?></P></td>
											<td width="80" align="center"><? echo $reqsn_no; ?>&nbsp;</td>
											<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
											<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $brand_details[$row[csf('brand_id')]]; ?></p></td>
											<td width="60" id="yarn_lot_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
											<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;<? echo $color; ?></p></td>
											<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
											<?
											//echo $color_details[$row[csf("color_id")]]; 
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150" id="feb_type_<? echo $i; ?>"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?>&nbsp;</p></td>
											<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $machine_details[$row[csf('machine_no_id')]]['dia']; ?></p></td>
											<td width="80" id="mc_gauge_<? echo $i; ?>"><p>&nbsp;<? echo $machine_details[$row[csf('machine_no_id')]]['gauge']; ?></p></td>
											<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('width')]; ?></p></td>
											<td width="50" id="stitch_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('stitch_length')]; ?></p></td> 
											<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$html.="<tr> 
											<td width='30'>".$i."</td>
											<td width='55'><p>".$knitting_party."&nbsp;</p></td>
											<td width='60'><p>".$row[csf('machine_name')]."</p></td>
											<td width='60'><p>".$row[csf('job_no_prefix_num')]."</p></td>
											<td width='70'><p></p></td>
											<td width='70'><p></p></td>
											<td width='60'><p>".$row[csf('year')]."</p></td>
											<td width='70'><p>".$buyer_arr[$row[csf('buyer_id')]]."</p></td>
											<td width='100'><p>".$po_sub_array[$row[csf('po_breakdown_id')]]['style_ref_no']."</p></td>
											<td width='110'><p>".$row[csf('po_number')]."</p></td>
											<td width='90'><P>".$receive_basis[$row[csf('receive_basis')]]."</P></td>
											<td width='110'><P>".$row[csf('booking_no')]."</P></td>
											<td width='60'><P>".$row[csf('recv_number_prefix_num')]."</P></td>
											<td width='80'>".$reqsn_no."</td>
											<td width='80'><p>".$count."</p></td>
											<td width='90'><p>&nbsp;".$brand_details[$row[csf('brand_id')]]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('yarn_lot')]."</p></td>
											<td width='100'><p>&nbsp;".$color."</p></td>";
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											$html.="<td width='100'><p>&nbsp;".$all_color."</p></td>
											<td width='150'><p>". $composition_arr[$row[csf('febric_description_id')]]."&nbsp;</p></td>
											<td width='50'><p>&nbsp;".$machine_details[$row[csf('machine_no_id')]]['dia']."</p></td>
											<td width='80'><p>&nbsp;".$machine_details[$row[csf('machine_no_id')]]['gauge']."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('width')]."</p></td>
											<td width='50'><p>&nbsp;".$row[csf('stitch_length')]."</p></td>
											<td width='60'><p>&nbsp;".$row[csf('gsm')]."</p></td>"; 
											$row_tot_roll=0; 
											$row_tot_qnty=0; 
											foreach($shift_name as $key=>$val)
											{
												$tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$source_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$source_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$row_tot_roll+=$row[csf('roll'.strtolower($val))]; 
												$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))]; 
											?>
												<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="100" align="right" >
												<? 
												echo number_format($row[csf('qntyshift'.strtolower($val))],2);
												$machineSamarryDataArr[$row[csf('machine_name')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
												 ?>
												</td>
											<?
											$html.="<td width='50' align='right'>".$row[csf('roll'.strtolower($val))]."</td>
												<td width='100' align='right' >".number_format($row[csf('qntyshift'.strtolower($val))],2)."</td>";
											}
											?>
											<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>
											<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
											<td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_tot_roll; ?></td>
											<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.',''); ?></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
										<?
										$html.="
											<td width='50' align='right'>".$row[csf('rollnoshift')]."</td>
											<td width='100' align='right'>".number_format($row[csf('qntynoshift')],2,'.','')."</td>
											<td width='50' align='right'>".$row_tot_roll."</td>
											<td width='100' align='right'>".number_format($row_tot_qnty+$row[csf('qntynoshift')],2,'.','')."</td>
											<td><p>".$row[csf('remarks')]."</p></td>
										</tr>";
										$grand_tot_roll+=$row_tot_roll; 
										$grand_tot_qnty+=$row_tot_qnty+$row[csf('qntynoshift')];
										
										$source_grand_tot_roll+=$row_tot_roll; 
										$source_grand_tot_qnty+=$row_tot_qnty;
										
										$noshift_total+=$row[csf('qntynoshift')];
										
										$grand_tot_floor_roll+=$row_tot_roll; 
										$grand_tot_floor_qnty+=$row_tot_qnty;
										$total_qty_noshift+=$row[csf('qntynoshift')];
										
										$j++;
										$i++;
									}
								
								?></tbody>
									<tr class="tbl_bottom">
										<td colspan="26" align="right"><b>Floor Total</b></td>
										<?
										$html.="</tbody>
										<tr>
										<td colspan='25' align='right'><b>Floor Total</b></td>";
										$floor_tot_qnty_row=0;
										foreach($shift_name as $key=>$val) 
										{
											$floor_tot_qnty_row+=$floor_tot_roll[$key]['qty'];
										?>
											<td align="right">&nbsp;</td>
											<td align="right"><? echo number_format($floor_tot_roll[$key]['qty'],2,'.',''); ?></td>
										<?
										$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($floor_tot_roll[$key]['qty'],2,'.','')."</td>";
										}
										?>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($noshift_total,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($floor_tot_qnty_row+$noshift_total,2,'.',''); ?></td>
										<td>&nbsp;</td>
									</tr>	
									<tr class="tbl_bottom">
										<td colspan="26" align="right"><b> Sample Without Order Total</b></td>
										<?
										
										$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($floor_tot_qnty_row+$noshift_total,2,'.','')."</td>
										<td>&nbsp;</td>
									</tr>	
									<tr>
										<td colspan='25' align='right'><b> Sample Without Order Total</b></td>";
										//$source_tot_qnty_row=0;
										foreach($shift_name as $key=>$val)
										{
											$source_tot_qnty+=$source_tot_roll[$key]['qty'];
											$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
										?>
											<td align="right">&nbsp;</td>
											<td align="right"><?  echo number_format($source_tot_qnty_row,2,'.',''); ?></td>
										<?
										$html.="<td align='right'>&nbsp;</td>
											<td align='right'>".number_format($source_tot_qnty_row,2,'.','')."</td>";
										unset($source_tot_qnty_row);
										}
										?>
	
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($total_qty_noshift,2,'.',''); ?></td>
										<td align="right">&nbsp;</td>
										<td align="right"><? echo number_format($source_tot_qnty,2,'.',''); ?></td>
										<td>&nbsp;</td>
									</tr>
									<?
									$html.="
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($total_qty_noshift,2,'.','')."</td>
										<td align='right'>&nbsp;</td>
										<td align='right'>".number_format($source_tot_qnty,2,'.','')."</td>
										<td>&nbsp;</td>
									</tr>";
								}
							?>
							<tfoot>
								<th></th>
								<th colspan="25" align="right">Grand Total</th>
								<?
								$html.="<tfoot>
								<th colspan='25' align='right'>Grand Total</th>";
								//$grand_tot_qnty=0;
								foreach($shift_name as $key=>$val)
								{
									$source_tot_qnty_row+=$source_tot_roll[$key]['qty'];
									//$grand_tot_qnty+=$source_tot_qnty_row+$tot_subcontract;
								?>
									<th align="right">&nbsp;</th>
									<th align="right"><? echo number_format($tot_roll[$key]['qty'],2,'.',''); ?></th>
								<?
								$html.="<th align='right'>&nbsp;</th>
									<th align='right'>".number_format($tot_roll[$key]['qty'],2,'.','')."</th>";
								}
								?>
								<th align="right">&nbsp;</th>
								<th align="right"><? echo number_format($total_service_subcontact,2,'.',''); ?></th>
								<th align="right">&nbsp;</th>
								<th align="right"><? echo number_format($grand_tot_qnty,2,'.',''); ?></th>
								<th>&nbsp;</th>
							</tfoot>
						</table> 
						<?
						$html.="
								<th align='right'>&nbsp;</th>
								<th align='right'>".number_format($total_service_subcontact,2,'.','')."</th>
								<th align='right'>&nbsp;</th>
								<th align='right'>".number_format($grand_tot_qnty,2,'.','')."</th>
								<th>&nbsp;</th>
							</tfoot>
						</table>";
						?>
					</div>
				</fieldset> 
				<br> 
				
	<?		
			
			
			
	
	//var_dump($ymcpacityWO_arr);	
	
			
			}
		}
		if($cbo_type==2 || $cbo_type==0)
		{
			$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
			$sql_inhouse_sub="select b.id, a.prefix_no_num, a.product_no, a.product_date, a.party_id, a.remarks, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type, b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id, b.floor_id $select_color, b.order_id, c.machine_no as machine_name, e.job_no_prefix_num, $year_sub_field as year, d.order_no, d.cust_style_ref, sum(case when b.shift=0 then b.product_qnty else 0 end ) as qntynoshift, sum(case when b.shift=0 then b.no_of_roll end ) as rollnoshift";
			foreach($shift_name as $key=>$val)
			{
				$sql_inhouse_sub.=", sum(case when b.shift=$key then b.no_of_roll end ) as roll".strtolower($val)."	
				, sum(case when b.shift=$key then b.product_qnty else 0 end ) as qntyshift".strtolower($val);
			}
			$sql_inhouse_sub.=" from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e 
			where a.id=b.mst_id and b.machine_id=c.id and b.order_id=d.id and e.subcon_job=d.job_no_mst and a.product_type=2 
			 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond
			group by b.id, a.prefix_no_num, a.product_no, a.product_date, a.party_id, a.remarks, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type,  b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id, b.floor_id, b.color_id, b.order_id, c.machine_no, e.job_no_prefix_num, e.insert_date, d.order_no, d.cust_style_ref, c.seq_no order by b.floor_id, a.product_date, c.seq_no";
			//echo $sql_inhouse_sub;
			$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub);
			if(count($nameArray_inhouse_subcon)>0)
			{
				$tbl_width=1690+count($shift_name)*157;
				
				?>
				<fieldset style="width:<? echo $tbl_width+20; ?>px;">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:400px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Subcontract Order (In-bound) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" >
						<thead>
							<tr>
								<th width="30" rowspan="2">SL</th>
								<th width="60" rowspan="2">M/C No</th>
								<th width="60" rowspan="2">Job No</th>
								<th width="60" rowspan="2">Year</th>
								<th width="70" rowspan="2">Party</th>
								<th width="100" rowspan="2">Style</th>
								<th width="110" rowspan="2">Order No</th>
								<th width="60" rowspan="2">Prod. No</th>
								<th width="80" rowspan="2">Yarn Count</th>
								<th width="90" rowspan="2">Yarn Brand</th>
								<th width="60" rowspan="2">Lot No</th>
								<th width="100" rowspan="2">Fabric Color</th>
								<th width="150" rowspan="2">Fabric Type</th>
								<th width="50" rowspan="2">M/C Dia</th>
								<th width="80" rowspan="2">M/C Gauge</th>
								<th width="50" rowspan="2">Fab. Dia</th>
								<th width="50" rowspan="2">Stitch</th>
								<th width="60" rowspan="2">GSM</th>
								<?
								foreach($shift_name as $val)
								{
								?>
									<th width="150" colspan="2"><? echo $val; ?></th>
								<?	
								}
								?>
								<th width="150" colspan="2">No Shift</th>
								<th width="150" colspan="2">Total</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<?
								foreach($shift_name as $val)
								{
								?>
									<th width="50" rowspan="2">Roll</th>
									<th width="100" rowspan="2">Qnty</th>
								<?	
								}
								?>
								<th width="50" rowspan="2">Roll</th>
								<th width="100" rowspan="2">Qnty</th>
								<th width="50" rowspan="2">Roll</th>
								<th width="100" rowspan="2">Qnty</th>
							</tr>
						</thead>
					</table>
					<div style="width:<? echo $tbl_width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_body">
							<? 
							$i=1; $tot_sub_rolla=''; $tot_sub_rollb=''; $tot_sub_rollc=''; $tot_sub_rolla_qnty=0; $tot_sub_rollb_qnty=0; $tot_sub_rollc_qnty=0; $grand_sub_tot_roll=''; $grand_sub_tot_qnty=0;
									foreach ($nameArray_inhouse_subcon as $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											
										$count='';
										$yarn_count=explode(",",$row[csf('yrn_count_id')]);
										foreach($yarn_count as $count_id)
										{
											if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
											<td width="30"><? echo $i; ?></td>
											<td width="60"><p>&nbsp;<? echo $row[csf('machine_name')]; ?></p></td>
											<td align="center" width="60"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
											<td align="center" width="60"><p><? echo $row[csf('year')]; ?></p></td>
											<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo $buyer_arr[$row[csf('party_id')]]; ?></p></td>
											<td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
											<td width="110"><p><? echo $row[csf('order_no')]; ?></p></td>
											<td width="60" id="prod_id_<? echo $i; ?>"><P><? echo $row[csf('prefix_no_num')]; ?></P></td>
											<td width="80" id="yarn_count_<? echo $i; ?>"><p><? echo $count; ?>&nbsp;</p></td>
											<td width="90" id="brand_id_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('brand')]; ?></p></td>
											<td width="60" id="yarn_lot_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('yarn_lot')]; ?></p></td>
											<td width="100" id="color_<? echo $i; ?>"><p>&nbsp;
											<? 
											//echo $color_details[$row[csf("color_id")]]; 
											$color_arr=array_unique(explode(",",$row[csf('color_id')]));
											$all_color="";
											foreach($color_arr as $id)
											{
												$all_color.=$color_details[$id].",";
											}
											$all_color=chop($all_color," , ");
											echo $all_color;
											?></p></td>
											<td width="150" id="feb_type_<? echo $i; ?>"><p><? echo $const_comp_arr[$row[csf('cons_comp_id')]]; ?>&nbsp;</p></td>
											<td width="50" id="mc_dia_<? echo $i; ?>"><p>&nbsp;<? echo $machine_details[$row[csf('machine_id')]]['dia']; ?></p></td>
											<td width="80" id="mc_gauge_<? echo $i; ?>"><p>&nbsp;<? echo $machine_details[$row[csf('machine_id')]]['gauge']; ?></p></td>
											<td width="50" id="fab_dia_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('dia_width')]; ?></p></td>
											<td width="50" id="stitch_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('stitch_len')]; ?></p></td>
											<td width="60" id="fin_gsm_<? echo $i; ?>"><p>&nbsp;<? echo $row[csf('gsm')]; ?></p></td>
											<?
											$row_sub_tot_roll=0; 
											$row_sub_tot_qnty=0; 
											foreach($shift_name as $key=>$val)
											{
												$tot_sub_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$tot_sub_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$source_sub_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$source_sub_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$floor_tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
												$floor_tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
												
												$row_sub_tot_roll+=$row[csf('roll'.strtolower($val))]; 
												$row_sub_tot_qnty+=$row[csf('qntyshift'.strtolower($val))]; 
											?>
												<td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
												<td width="100" align="right" ><? 
												echo number_format($row[csf('qntyshift'.strtolower($val))],2); 
												$machineSamarryDataArr[$row[csf('machine_name')]][$key]+=$row[csf('qntyshift'.strtolower($val))];
												?></td>
											<?
											}
											?>
											<td width="50" align="right" id="noqty_<? echo $i; ?>"><? echo $row[csf('rollnoshift')]; ?></td>
											<td width="100" align="right" id="noqty_<? echo $i; ?>"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
											<td width="50" align="right" id="roll_<? echo $i; ?>"><? echo $row_sub_tot_roll; ?></td>
											<td width="100" align="right" id="qty_<? echo $i; ?>"><? echo number_format($row_sub_tot_qnty+$row[csf('qntynoshift')],2,'.',''); ?></td>
											<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										</tr>
									</tbody>
									<?
		
									$grand_sub_tot_roll+=$row_sub_tot_roll; 
									$grand_sub_tot_qnty+=$row_sub_tot_qnty+$row[csf('qntynoshift')];
									
									$source_sub_grand_tot_roll+=$row_sub_tot_roll; 
									$source_sub_grand_tot_qnty+=$row_sub_tot_qnty;
									
									$noshift_sub_total+=$row[csf('qntynoshift')];
									
									$grand_sub_tot_floor_roll+=$row_sub_tot_roll; 
									$grand_sub_tot_floor_qnty+=$row_sub_tot_qnty;
									$total_sub_qty_noshift+=$row[csf('qntynoshift')];
									
									$i++;
								}
							?>
							<tfoot>
								<th colspan="18" align="right">Grand Total</th>
								<?
								//$grand_tot_qnty=0;
								foreach($shift_name as $key=>$val)
								{
									//$source_sub_tot_qnty_row+=$source_sub_tot_roll[$key]['qty'];
									//$grand_tot_qnty+=$source_tot_qnty_row+$tot_subcontract;
								?>
									<th align="right"><? echo number_format($tot_sub_roll[$key]['roll'],2,'.',''); ?></th>
									<th align="right"><? echo number_format($tot_sub_roll[$key]['qty'],2,'.',''); ?></th>
								<?
								}
								?>
								<th align="right">&nbsp;</th>
								<th align="right"><? echo number_format($total_sub_qty_noshift,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_sub_tot_roll,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_sub_tot_qnty,2,'.',''); ?></th>
								<th>&nbsp;</th>
							</tfoot>
						</table> 
					</div>
				</fieldset>      
			<?
			}
		}
		?>
		<br>
		
				<fieldset style=" width:750px;">
					 <h2>Machine wise summary</h2>
					 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="summary_tab">
					<thead>
						<tr>
							<th rowspan="2">SL</th>
							<th rowspan="2">M/C No</th>
							<th rowspan="2">Capacity</th>
							<th colspan="<? echo count($shift_name);?>">SHIFT NAME</th>
							<th rowspan="2" width="80">Shift Total (kg)</th>
							<th rowspan="2" width="80">Capacity Achieve %</th>
							<th rowspan="2" width="80">Yesterday Prod. Qty.</th>
							<th rowspan="2" width="80">Yesterday Capacity Achieve %</th>
						</tr>
						
					   <?
					   $html.="
							</div>
						</fieldset> 
						<br> 
						
						<fieldset style='width:750px;'>
							 <h2>Machine wise summary</h2>
							 <table class='rpt_table' width='100%' cellpadding='0' cellspacing='0' border='1' rules='all' align='center'>
							<thead>
								<tr>
									<th rowspan='2'>SL</th>
									<th rowspan='2'>M/C No</th>
									<th rowspan='2'>Capacity</th>
									<th colspan=". count($shift_name).">SHIFT NAME</th>
									<th rowspan='2' width='80'>Shift Total (kg)</th>
									<th rowspan='2' width='80'>Capacity Achieve %</th>
									<th rowspan='2' width='80'>Yesterday Prod. Qty.</th>
									<th rowspan='2' width='80'>Yesterday Capacity Achieve %</th>
								</tr>
								 <tr>
							   ";
					   
					   ?> 
						<tr>
							<?
							foreach($shift_name as $key=>$val)
							{
							?>
								<th><? echo $val;?></th>
								
							<?	
							$html.="<th>".$val."</th>";
							}
							?>
						</tr>
					</thead>
					
					
				  <? 
				  $html.="
				   </tr>
					</thead>
				   ";
				   
					if($db_type==0)
					{
						$previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from_date))); 
					}
					else
					{
						$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($from_date))),'','',1); 
					}
				
					$date_con_2=" and a.receive_date between '$previous_date' and '$previous_date'";
				
				
				$ymcpacity_arr=return_library_array( "select d.machine_no,sum(c.quantity) as quantity  from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con_2 $floor_id $buyer_cond $job_cond $order_cond $job_year_cond group by d.machine_no", "machine_no", "quantity" );
				  
				  
				$ymcpacityWO_arr=return_library_array( "select d.machine_no,sum(b.grey_receive_qnty) as quantity  from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id  and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $date_con_2 $floor_id  $buyer_cond group by d.machine_no", "machine_no", "quantity" );
				  
				  
				  $mcpacity_arr=return_library_array( "select machine_no, prod_capacity from lib_machine_name where category_id=1", "machine_no", "prod_capacity"  );
	
				  $i=1;
				  foreach($machineSamarryDataArr as $machine_no=>$row):
					$bgcolor=($i%2==0)? "#E9F3FF":"#FFFFFF";
				  ?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_sm<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_sm<? echo $i; ?>">
						<td align="center"><? echo $i; ?></td>
						<td><? echo $machine_no; ?></td>
						<td align="right"><? echo $mCapacity=$mcpacity_arr[$machine_no]; $totmCapacity+=$mCapacity;?></td>
						<? 
						 $html.="
						<tr bgcolor='".$bgcolor."' id='tr_sm".$i."'>
							<td align='center'>".$i."</td>
							<td>".$machine_no."</td>
							<td align='right'>".$mcpacity_arr[$machine_no]."</td>
						 ";
						
						
						
						
						$totPro=0;
						foreach($row as $key=>$val)
						{
						?>
							<td align="right"><? echo $val; $proQty[$key]+=$val;$totPro+=$val;  ?></td>
						<?
							$html.="<td align='right'>".$val."</td>";	
						}
						
						?>
						<td align="right"><? echo $totPro; $gTotPro+=$totPro;?></td>
						<td align="right"><? echo round(($totPro/$mCapacity)*100);?></td>
						<td align="right">
						<? 
							echo  $html_sum=$ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no];
							$totymc+=$ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no];
						?>
						</td>
						<td align="right"><? echo round((($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no])/$mCapacity)*100);?></td>
					</tr>
					
				  <? 
				  $html.=" 
						<td align='right'>".$totPro."</td>
						<td align='right'>".round(($totPro/$mCapacity)*100)."</td>
						<td align='right'>".$html_sum."</td>
						<td align='right'>".round((($ymcpacityWO_arr[$machine_no]+$ymcpacity_arr[$machine_no])/$mCapacity)*100)."</td>
					</tr>
				  ";
				  $i++;
				  endforeach;?>
					<tfoot>
						<th></th>
						<th>Total</th>
						<th><? echo $totmCapacity;?></th>
						<? 
					$html.="
					<tfoot>
						<th></th>
						<th>Total</th>
						<th>".$totmCapacity."</th>
					   "; 
					   
					   
						foreach($shift_name as $key=>$val)
						{
						?>
							<th><? echo $proQty[$key]; ?></th>
						<?
						$html.="<th>".$proQty[$key]."</th>";	
						}
						?>
						<th><? echo $gTotPro;?></th>
						<th><? echo round(($gTotPro/$totmCapacity)*100);?></th>
						<th><? echo $totymc;?></th>
						<th><? echo round(($totymc/$totmCapacity)*100);?></th>
					</tfoot>
				</table>
				</fieldset>
				
					
			<?
			$html.="
						<th>".$gTotPro."</th>
						<th>".round(($gTotPro/$totmCapacity)*100)."</th>
						<th>".$totymc."</th>
						<th>".round(($totymc/$totmCapacity)*100)."</th>
					</tfoot>
				</table>
				</fieldset>
			";
		
	foreach (glob("../../../ext_resource/tmp_report/$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="../../../ext_resource/tmp_report/".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
	
		
	}
	else
	{
		if($cbo_type==1 || $cbo_type==0)
		{
			if (str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=''; else $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
			if($txt_job!="") $job_cond=" and f.job_no_prefix_num='$txt_job' "; else $job_cond="";
			if($txt_order!="") $order_cond=" and e.po_number like '%$txt_order%' "; else $order_cond="";
			if (str_replace("'","",$cbo_floor_id)==0) $floor_id=''; else $floor_id=" and b.floor_id=$cbo_floor_id";
			
			if($db_type==0)
			{
				$year_field="YEAR(f.insert_date)";
				$year_field_sam="YEAR(a.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(f.insert_date)=$cbo_year"; else $job_year_cond="";
			}
			else if($db_type==2)
			{
				$year_field="to_char(f.insert_date,'YYYY')";
				$year_field_sam="to_char(a.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_cond=" and to_char(f.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
			}
			else $year_field="";
			$from_date=$txt_date_from;
			if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;
			
			if(str_replace("'","",$cbo_knitting_source)==0) $source="%%"; else $source=str_replace("'","",$cbo_knitting_source);
			
			$date_con="";
			if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
			$machine_details=array();
			$machine_data=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
				$machine_details[$row[csf('id')]]['gauge']=$row[csf('gauge')];
				if($row[csf('machine_no')]!="") $total_machine[$row[csf('id')]]=$row[csf('id')];
			}
			
			/*$machine_details=array();
			$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
			foreach($machine_data as $row)
			{
				$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			}
	
			$po_array=array();
			$po_data=sql_select("select a.job_no, a.job_no_prefix_num, a.style_ref_no from wo_po_details_master a where a.company_name=$cbo_company_name");
			foreach($po_data as $row)
			{
				//$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
				//$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$po_array[$row[csf('job_no_prefix_num')]]['style_ref_no']=$row[csf('style_ref_no')];
			}
			*/
			/*$po_sub_array=array();
			$po_data=sql_select("select a.job_no, a.job_no_prefix_num, $year_field_sam as year, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name ");
			foreach($po_data as $row)
			{
				$po_sub_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')];
				$po_sub_array[$row[csf('id')]]['year']=$row[csf('year')];
				$po_sub_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$po_sub_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
			}*/
			//var_dump($po_sub_array);
			
			$composition_arr=$construction_arr=array();
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
						$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
					
					$construction_arr[$row[csf('id')]]=$row[csf('construction')];
				}
			}
			
			$knit_plan_arr=array();
			$plan_data=sql_select("select id, color_range, stitch_length, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
			foreach($plan_data as $row)
			{
				$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
				$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')]; 
				$knit_plan_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
				$knit_plan_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')]; 
			}
		}
		$tbl_width=1870+count($shift_name)*100;
		ob_start();
		
		
		

			?>
			
			<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
				<tr>
				   <td align="center" width="100%" colspan="<? echo $ship_count+23; ?>" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="<? echo $ship_count+23; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
				<tr> 
				   <td align="center" width="100%" colspan="<? echo $ship_count+23; ?>" class="form_caption" style="font-size:12px" ><strong><? if(str_replace("'","",$txt_date_from)!="") echo "From ".str_replace("'","",$txt_date_from); if(str_replace("'","",$txt_date_to)!="") echo " To ".str_replace("'","",$txt_date_to); ?></strong></td>
				</tr>
			</table>            
            
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850px">
				<tr>
				<td width="555">
					<div align="left" style="background-color:#E1E1E1; color:#000; width:420px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House + Outbound) Knitting Production</i></u></strong></div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550px" class="rpt_table" >
						<thead>
							<tr>
								<th colspan="6">Knit Production Summary (In-House + Outbound)</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="100">Buyer</th>
								<th width="90">Inhouse</th>
								<th width="90">Outbound-Subcon</th>
								<th width="90">Sample Without Order</th>
								<th width="100">Total</th>
							</tr>
						</thead>
					</table>
					<div style="width:570px; overflow-y:scroll; max-height:220px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550px" class="rpt_table" >
						<tbody>
						<?
							
							$sql_sample_samary=sql_select("select a.buyer_id, sum(case when  b.machine_no_id>0 $floor_id  then b.grey_receive_qnty end ) as sample_qty
							 from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.company_id=$cbo_company_name and a.knitting_source like '$source'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $date_con $floor_id $buyer_cond group by a.buyer_id ");
							 $subcon_buyer_samary=array();
							 foreach($sql_sample_samary as $inf)
							 {
								$subcon_buyer_samary[$inf[csf('buyer_id')]]+= $inf[csf('sample_qty')];
								$subcon_buyer_sammary['total']+= $inf[csf('sample_qty')];
							 }
							 
							 $sql_service_samary=sql_select("select a.buyer_id, sum(b.grey_receive_qnty) as service_qty
							 from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=22 and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_con $buyer_cond group by a.buyer_id");
							 $service_buyer_data=array();
							 foreach($sql_service_samary as $row)
							 {
								 $service_buyer_data[$row[csf("buyer_id")]]=$row[csf("service_qty")];
							 }
						//echo $sql_sample_samary;die;
						
							$sql_qty="Select a.buyer_id, sum(case when a.knitting_source=1 and b.machine_no_id>0 $floor_id  then c.quantity end ) as qtyinhouse, sum(case when a.knitting_source=3 then c.quantity end ) as qtyoutbound from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' $date_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_cond $job_cond $order_cond $job_year_cond group by a.buyer_id ";
							//echo $sql_qty; 
							$k=1;
							$sql_result=sql_select( $sql_qty);
							foreach($sql_result as $rows)
							{
							   if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							   $out_bound_qnty=0;
							   $out_bound_qnty=$rows[csf('qtyoutbound')]+$service_buyer_data[$rows[csf('buyer_id')]];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="40"><? echo $k; ?></td>
									<td width="100"><? echo $buyer_arr[$rows[csf('buyer_id')]]; ?></td>
									<td width="90" align="right"><? echo number_format($rows[csf('qtyinhouse')],2,'.',''); ?>&nbsp;</td>
									<td width="90" align="right"><? echo number_format($out_bound_qnty,2,'.',''); ?>&nbsp;</td>
									<td width="90" align="right"><? echo number_format($subcon_buyer_samary[$rows[csf('buyer_id')]],2,'.',''); $tot_summ=$rows[csf('qtyinhouse')]+$rows[csf('qtyoutbound')]+$subcon_buyer_samary[$rows[csf('buyer_id')]]; ?>&nbsp;</td>
									<td width="100" align="right"><? echo  number_format($tot_summ,2,'.',''); ?>&nbsp;</td>
								</tr>
								<?	
								$tot_qtyinhouse+=$rows[csf('qtyinhouse')];
								$tot_qtyoutbound+=$out_bound_qnty;
								$total_summ+=$tot_summ;
								unset($subcon_buyer_samary[$rows[csf('buyer_id')]]);
								$k++;
							}
							if(count($subcon_buyer_samary)>0)
							{
								foreach($subcon_buyer_samary as $key=>$value)
								{
								   if ($k%2==0)  
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td width="40"><? echo $k; ?></td>
										<td width="100"><? echo $buyer_arr[$key]; ?></td>
										<td width="90" align="right">&nbsp;</td>
										<td width="90" align="right">&nbsp;</td>
										<td width="90" align="right"><? echo number_format($value,2,'.',''); ?>&nbsp;</td>
										<td width="100" align="right"><? echo number_format($value,2,'.',''); ?>&nbsp;</td>
									</tr>
								<?	
									$total_summ+=$value;
									$k++;
								}
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2" align="right"><strong>Total</strong></th>
								<th align="right"><? echo number_format($tot_qtyinhouse,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo number_format($tot_qtyoutbound,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo number_format($subcon_buyer_sammary['total'],2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo number_format($total_summ,2,'.',''); ?>&nbsp;</th>
							</tr>
							<tr>
								<th colspan="2"><strong>In %</strong></th>
								<th align="right"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
								<th align="right"><? $qtyoutbound_per=($tot_qtyoutbound/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
								<th align="right"><? $qtyoutbound_per=($subcon_buyer_sammary['total']/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
								<th align="right"><? echo "100 %"; ?></th>
							</tr>
						</tfoot>
					</table>
					</div>
				
				</tr>
			</table>
			<br />
        
        <fieldset style="width:<? echo $tbl_width+20; ?>px;">
			
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_head" >
                <thead>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="90" rowspan="2">M/C No.</th>
                        <th width="70" rowspan="2">M/C Brand </th>
                        <th width="70" rowspan="2">Production Date</th>
                        <th width="60" rowspan="2">M/C Dia &  Gauge</th>
                        <th width="70" rowspan="2">Unit  Name</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Program/ Booking No</th>
                        <th width="70" rowspan="2">File No.</th>
                        <th width="70" rowspan="2">Ref No.</th>
                        <th width="70" rowspan="2">Yarn Count</th>
                        <th width="80" rowspan="2">Brand</th>
                        <th width="80" rowspan="2">Lot</th>
                        <th width="100" rowspan="2">Construction</th>
                        <th width="150" rowspan="2">Composition</th>
                        <th width="130" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Color Range</th>
                        <th width="60" rowspan="2">Stitch</th>
                        <th width="60" rowspan="2">GSM</th>
                        <th  colspan="<? echo count($shift_name); ?>">Production</th>
                        <th width="100" rowspan="2">Shift Total</th>
                        <th width="100" rowspan="2">Machine Total</th>
                        <th width="80" rowspan="2">Reject Qty</th>
                        <th rowspan="2"> Remarks</th>
                    </tr>
                    <tr>
                        <?
						$ship_count=0;
                        foreach($shift_name as $val)
                        {
							$ship_count++;
                        	?>
                           <th width="80"><? echo $val; ?></th>
                        	<?	
                        }
                        ?>
                    </tr>
                </thead>
            </table>
            <div style="width:<? echo $tbl_width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_body">
                    <tbody>
					<? 
					//die;
                        $i=1; 
						if($db_type==0)
						{
							$sql_inhouse="select a.receive_basis, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, group_concat(a.remarks) as remarks, group_concat(b.id) as dtls_id, group_concat(b.prod_id) as prod_id, group_concat(b.febric_description_id) as febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width, group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, group_concat(b.stitch_length) as stitch_length, group_concat(b.brand_id) as brand_id, b.machine_no_id,d.brand as mc_brand, b.floor_id as floor_id,  group_concat(b.color_id) as color_id,  group_concat(b.color_range_id) as color_range_id, group_concat(c.po_breakdown_id) as po_breakdown_id, d.seq_no, d.machine_no as machine_name, group_concat(e.po_number) as po_number, group_concat(e.file_no) as file_no,group_concat(e.grouping) as grouping,sum(distinct b.reject_fabric_receive) as reject_qty";
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val);
							}
							$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f 
							where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and e.job_no_mst=f.job_no and  c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond
							group by a.receive_basis, a.receive_date, a.booking_no, b.machine_no_id,d.brand, b.floor_id, d.seq_no, d.machine_no 
							order by a.receive_date,d.seq_no,  b.floor_id";
							
						}
						else
						{
							//"listagg((cast(b.cust_style_ref as varchar2(4000))),',') within group (order by b.cust_style_ref)"
							$sql_inhouse="select a.receive_basis, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id,  listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, d.seq_no, d.machine_no as machine_name,d.brand as mc_brand, listagg((cast(e.po_number as varchar2(4000))),',') within group (order by e.po_number) as po_number, listagg((cast(e.file_no as varchar2(4000))),',') within group (order by e.file_no) as file_no, listagg((cast(e.grouping as varchar2(4000))),',') within group (order by e.grouping) as grouping,sum(distinct b.reject_fabric_receive) as reject_qty";
							foreach($shift_name as $key=>$val)
							{
								$sql_inhouse.=", sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val);
							}
							$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f 
							where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and e.job_no_mst=f.job_no and  c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond
							group by a.receive_basis, a.receive_date, a.booking_no, b.machine_no_id,d.brand, b.floor_id, d.seq_no, d.machine_no 
							order by a.receive_date, d.seq_no, b.floor_id";
						}
						//echo $sql_inhouse;
						
                        
                        //echo $sql_inhouse;die;
                        /*$sql_subcontract="select c.id,b.stitch_length, b.id, b.no_of_roll, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.floor_id $select_color, c.po_breakdown_id,  f.job_no_prefix_num, $year_field as year, e.po_number,e.file_no,e.grouping, sum(c.quantity) as outqntyshift 
                        from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down e, wo_po_details_master f where c.po_breakdown_id=e.id and a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source=3 and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con  $buyer_cond $job_cond $order_cond $job_year_cond 
                        group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number,e.file_no,e.grouping, c.id, b.id, b.no_of_roll, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id, c.po_breakdown_id order by b.floor_id,a.receive_date";*/			
                        //echo $sql_subcontract;
						//$sql_wout_order="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.stitch_length,b.brand_id, b.machine_no_id, b.floor_id $select_color, 0 as po_breakdown_id, d.machine_no as machine_name, '' as job_no_mst, '' po_number, 0 as qntynoshift, sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift	";
						
						if($db_type==0)
						{
							$sql_wout_order="select a.receive_basis, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, group_concat(a.remarks) as remarks, group_concat(b.id) as dtls_id, group_concat(b.prod_id) as prod_id, group_concat(b.febric_description_id) as febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width, group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, group_concat(b.stitch_length) as stitch_length, group_concat(b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id,  group_concat(b.color_id) as color_id,  group_concat(b.color_range_id) as color_range_id, d.seq_no, d.machine_no as machine_name,d.brand as mc_brand,sum(distinct b.reject_fabric_receive) as reject_qty";
							foreach($shift_name as $key=>$val)
							{
								$sql_wout_order.=",sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
							}
							$sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d 
						where a.id=b.mst_id and b.machine_no_id=d.id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  $date_con $floor_id  $buyer_cond 
							group by a.receive_basis, a.receive_date, a.booking_no, b.machine_no_id,d.brand, b.floor_id, d.seq_no, d.machine_no 
							order by  a.receive_date, d.seq_no, b.floor_id";
							
						}
						else
						{
							//"listagg((cast(b.cust_style_ref as varchar2(4000))),',') within group (order by b.cust_style_ref)"
							$sql_wout_order="select a.receive_basis, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id,  listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, d.seq_no, b.machine_no_id,sum(distinct b.reject_fabric_receive) as reject_qty, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, d.machine_no as machine_name,d.brand as mc_brand";
							foreach($shift_name as $key=>$val)
							{
								$sql_wout_order.=",sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
							}
							 $sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d 
						where a.id=b.mst_id and b.machine_no_id=d.id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  $date_con $floor_id  $buyer_cond
							group by a.receive_basis, a.receive_date, a.booking_no, b.machine_no_id,d.brand, b.floor_id, d.seq_no, d.machine_no 
							order by a.receive_date, d.seq_no, b.floor_id";
						}
						
						$yarn_type_arr=return_library_array( "select id, yarn_type from product_details_master where item_category_id=13", "id", "yarn_type");
						
						/*$sql_wout_order="select a.receive_basis, a.receive_date, a.booking_no, a.buyer_id, a.remarks, b.id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, b.floor_id $select_color, 0 as po_breakdown_id, d.machine_no as machine_name, '' as po_number, '' as file_no,  '' as grouping";
                        
                        foreach($shift_name as $key=>$val)
                        {
                            $sql_wout_order.=",sum(case when b.shift_name=$key then b.grey_receive_qnty else 0 end ) as qntyshift".strtolower($val);
                        }
                        
                        $sql_wout_order.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d 
						where a.id=b.mst_id and b.machine_no_id=d.id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  $date_con $floor_id  $buyer_cond 
						group by a.receive_basis, a.receive_date, a.buyer_id, a.remarks, b.id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.machine_no_id, b.floor_id $group_color , d.machine_no 
						order by a.receive_date, d.machine_no, b.floor_id";*/
                     	//echo $sql_wout_order;die;
                     
                        $nameArray_inhouse=sql_select( $sql_inhouse);
                        $nameArray_without_order=sql_select( $sql_wout_order);
						$machine_inhouse_array=$total_running_machine=array();
                        foreach ($nameArray_inhouse as $row)
						{
							$machine_inhouse_array[$row[csf('machine_no_id')]][$row[csf('receive_date')]]++;
							$total_running_machine[$row[csf('machine_no_id')]]=$row[csf('machine_no_id')];
							foreach($shift_name as $key=>$val)
                        	{
								$machine_inhouse_qty[$row[csf('machine_no_id')]]+=$row[csf('qntyshift'.strtolower($val))];
							}
						}
						$machine_without_array=$machine_without_qty=array();
                        foreach ($nameArray_without_order as $row)
						{
							$machine_without_array[$row[csf('machine_no_id')]][$row[csf('receive_date')]]++;
							$total_running_machine[$row[csf('machine_no_id')]]=$row[csf('machine_no_id')];
							foreach($shift_name as $key=>$val)
                        	{
								$machine_without_qty[$row[csf('machine_no_id')]]+=$row[csf('qntyshift'.strtolower($val))];
							}
						}
						
						if($cbo_type==1 || $cbo_type==0)
						{
							if (count($nameArray_inhouse)>0)
                        	{
								?>
								<tr  bgcolor="#CCCCCC">
									<td colspan="<? echo $ship_count+23; ?>" align="left" ><b>In-House</b></td>
								</tr>    
								<?
								$km=0;$tot_reject_qty=0;
								foreach ($nameArray_inhouse as $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										
									$count='';
									$yarn_count=array_unique(explode(",",$row[csf('yarn_count')]));
									foreach($yarn_count as $count_id)
									{
										if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
									}
									
									if($row[csf('receive_basis')]==2)
									{
										$machine_dia_gage=$knit_plan_arr[$row[csf('booking_no')]]['machine_dia']." X ".$knit_plan_arr[$row[csf('booking_no')]]['machine_gg'];
									}
									else
									{
										$machine_dia_gage=$machine_details[$row[csf('machine_no_id')]]['dia_width']." X ".$machine_details[$row[csf('machine_no_id')]]['gauge'];
									}
									
									
									?>
									
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
									    
										<?
										
										if($temp_arr[$row[csf('machine_no_id')]][$row[csf('receive_date')]]=="" )
										{
											$km++;
											?>
											<td width="30" align="center"><? echo $km; ?></td>
											<td width="90" align="center"><p><? echo $row[csf('machine_name')]; ?></p></td>
											<?
										}
										else
										{
											?>
											<td width="30" align="center"></td>
                                            <td width="90" align="center"></td>
                                            <?
										}
										//else
										// echo "<td></td>";
										?>
									   
										
										<td width="70" align="center"><p><? echo $row[csf('mc_brand')]; ?></p></td>
										<td width="70" align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
										<td width="60"  align="center"><p><? echo $machine_dia_gage; ?></p></td>
										<td width="70" align="center"><? echo $floor_details[$row[csf('floor_id')]]; ?></td>
										<td width="70" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
										<td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
										<td width="70" align="center"><p><? echo implode(",",array_unique(explode(",",$row[csf('file_no')]))); ?></p></td>
										<td width="70"  align="center"><p>&nbsp;<? echo implode(",",array_unique(explode(",",$row[csf('grouping')]))); ?></p></td>
										<td width="70" align="center"><p><? echo $count; ?></p></td>
										<td width="80"><P>
										<?
										$brand_arr=array_unique(explode(",",$row[csf('brand_id')]));
										$all_brand="";
										foreach($brand_arr as $id)
										{
											$all_brand.=$brand_details[$id].",";
										}
										$all_brand=chop($all_brand," , ");
										echo $all_brand; 
										?>&nbsp;</P></td>
										<td width="80" align="center"><P><? echo implode(",",array_unique(explode(",",$row[csf('yarn_lot')]))); ?>&nbsp;</P></td>
										<td width="100"><P>
										<?
										$description_arr=array_unique(explode(",",$row[csf('febric_description_id')]));
										$all_construction="";
										foreach($description_arr as $id)
										{
											$all_construction.=$construction_arr[$id].",";
										}
										$all_construction=chop($all_construction," , ");
										echo $all_construction; 
										//echo $composition_arr[$row[csf('febric_description_id')]]; 
										?>&nbsp;</P></td>
                                        <td width="150"><P>
										<?
										$all_composition="";
										foreach($description_arr as $id)
										{
											$all_composition.=$composition_arr[$id].",";
										}
										$all_composition=chop($all_composition," , ");
										echo $all_composition; 
										//echo $composition_arr[$row[csf('febric_description_id')]]; 
										?>&nbsp;</P></td>
										<td width="130"><P>
										<?
										$color_arr=array_unique(explode(",",$row[csf('color_id')]));
										$all_color="";
										foreach($color_arr as $id)
										{
											$all_color.=$color_details[$id].",";
										}
										$all_color=chop($all_color," , ");
										echo $all_color; 
										?>&nbsp;</P></td>
                                        <td width="100"><P>
										<?
										$color_range_arr=array_unique(explode(",",$row[csf('color_range_id')]));
										//print_r($color_range_arr);
										$all_color_range="";
										foreach($color_range_arr as $id)
										{
											$all_color_range.=$color_range[$id].",";
										}
										$all_color_range=chop($all_color_range," , ");
										echo $all_color_range; 
										//echo $row[csf('color_range_id')];//$color_range[];
										?>&nbsp;</P></td>
										<td width="60"  align="center"><p><? echo  implode(",",array_unique(explode(",",$row[csf('stitch_length')]))); ?>&nbsp;</p></td>
										<td width="60"  align="center"><p><? echo  implode(",",array_unique(explode(",",$row[csf('gsm')])));?>&nbsp;</p></td>
										<?
										$row_tot_roll=0; 
										$row_tot_qnty=0; 
										foreach($shift_name as $key=>$val)
										{
											$row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))]; 
											?>
											<td width="80" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($val))],2); ?> </td>
											<?
											$grand_total_ship[$key]+=$row[csf('qntyshift'.strtolower($val))];
											$inhouse_ship[$key]+=$row[csf('qntyshift'.strtolower($val))];
										}
										?>
										<td width="100" align="right" ><? echo number_format($row_tot_qnty,2,'.',''); ?></td>
										<?
										if($temp_arr[$row[csf('machine_no_id')]][$row[csf('receive_date')]]=="" )
										{
											$temp_arr[$row[csf('machine_no_id')]][$row[csf('receive_date')]]=$row[csf('receive_date')];
											?>
											<td width="100" valign="top" align="right" rowspan="<? echo $machine_inhouse_array[$row[csf('machine_no_id')]][$row[csf('receive_date')]]; ?>"><? echo number_format($machine_inhouse_qty[$row[csf('machine_no_id')]],2,'.',''); ?></td>
											<?
											$grand_machine_total+=$machine_inhouse_qty[$row[csf('machine_no_id')]];
											$machine_total_inhouser+=$machine_inhouse_qty[$row[csf('machine_no_id')]];
										}
										
										?>
                                        <td width="80" align="right"><p><? echo number_format($row[csf('reject_qty')],2,'.',''); ?></p></td>
										<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
									</tr>
								   
									<?
									$inhouse_tot_qty+=$row_tot_qnty;
									$grand_tot_qnty+=$row_tot_qnty;
									$grand_reject_qty+=$row[csf('reject_qty')];
									$i++;
								}
							
								?> 
								<tr class="tbl_bottom">
									<td colspan="19" align="right"><b>In-house Total(with order)</b></td>
									<?
									foreach($shift_name as $key=>$val)
									{
										?>
										<td align="right"><? echo number_format($inhouse_ship[$key],2,'.',''); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($inhouse_tot_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($machine_total_inhouser,2,'.',''); ?></td>
									<td align="right"><? echo number_format($grand_reject_qty,2,'.',''); ?></td>
                                    <td>&nbsp;</td>
								</tr>	
								<?
							}
						}
						
                        if($cbo_type==2 || $cbo_type==0)
						{
							if (count($nameArray_without_order)>0)
                        	{
								?>
								<tr  bgcolor="#CCCCCC">
									<td colspan="<? echo $ship_count+23; ?>" align="left" ><b>Sample Without Order</b></td>
								</tr>    
								<?
								$tot_reject_qty=0;$machine_total_non_order=0;
								foreach ($nameArray_without_order as $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										
									$count='';
									$yarn_count=array_unique(explode(",",$row[csf('yarn_count')]));
									foreach($yarn_count as $count_id)
									{
										if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
									}
									
									if($row[csf('receive_basis')]==2)
									{
										$machine_dia_gage=$knit_plan_arr[$row[csf('booking_no')]]['machine_dia']." X ".$knit_plan_arr[$row[csf('booking_no')]]['machine_gg'];
									}
									else
									{
										$machine_dia_gage=$machine_details[$row[csf('machine_no_id')]]['dia_width']." X ".$machine_details[$row[csf('machine_no_id')]]['gauge'];
									}
									
									?>
									
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
										
										<?
										if($temp_ono_order_arr[$row[csf('machine_no_id')]][$row[csf('receive_date')]]=="")
										{
											$j++;
											?>
											<td width="30" align="center" valign="top"><? echo $j; ?></td>
											<td width="90" valign="top" align="center" rowspan="<? echo $machine_without_array[$row[csf('machine_no_id')]]; ?>"><p><? echo $row[csf('machine_name')]; ?></p></td>
											<?
										}
										else
										{
											?>
											<td width="30" align="center"></td>
                                            <td width="90" align="center"></td>
                                            <?
										}
										?>
										
										<td width="70" align="center"><p><? echo $row[csf('mc_brand')];?>&nbsp;</p></td>
                                        <td width="70" align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
										<td width="60" align="center"><p><? echo $machine_dia_gage; ?></p></td>
										<td width="70"  align="center"><? echo $floor_details[$row[csf('floor_id')]]; ?></td>
										<td width="70" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
										<td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
										<td width="70"><p><? echo implode(",",array_unique(explode(",",$row[csf('file_no')]))); ?></p></td>
										<td width="70" id="buyer_id_<? echo $i; ?>"><p>&nbsp;<? echo implode(",",array_unique(explode(",",$row[csf('grouping')]))); ?></p></td>
										<td width="70"><p><? echo $count; ?></p></td>
										<td width="80"><P>
										<? 
										$brand_arr=array_unique(explode(",",$row[csf('brand_id')]));
										$all_brand="";
										foreach($brand_arr as $id)
										{
											$all_brand.=$brand_details[$id].",";
										}
										$all_brand=chop($all_brand," , ");
										echo $all_brand; 
										?>&nbsp;</P></td>
										<td width="80"><P><? echo implode(",",array_unique(explode(",",$row[csf('yarn_lot')]))); ?>&nbsp;</P></td>
										<td width="100"><P>
										<?
										$description_arr=array_unique(explode(",",$row[csf('febric_description_id')]));
										$all_construction="";
										foreach($description_arr as $id)
										{
											$all_construction.=$construction_arr[$id].",";
										}
										$all_construction=chop($all_construction," , ");
										echo $all_construction; 
										//echo $composition_arr[$row[csf('febric_description_id')]]; 
										?>&nbsp;</P></td>
                                        <td width="150"><P>
										<?
										$all_composition="";
										foreach($description_arr as $id)
										{
											$all_composition.=$composition_arr[$id].",";
										}
										$all_composition=chop($all_composition," , ");
										echo $all_composition; 
										//echo $composition_arr[$row[csf('febric_description_id')]]; 
										?>&nbsp;</P></td>
										<td width="130"><P>
										<? 
										$color_arr=array_unique(explode(",",$row[csf('color_id')]));
										$all_color="";
										foreach($color_arr as $id)
										{
											$all_color.=$color_details[$id].",";
										}
										$all_color=chop($all_color," , ");
										echo $all_color;  
										?>&nbsp;</P></td>
                                        <td width="100"><P><? echo $color_range[$row[csf('color_range_id')]];?>&nbsp;</P></td>
										<td width="60"><p><? echo  implode(",",array_unique(explode(",",$row[csf('stitch_length')]))); ?>&nbsp;</p></td>
										<td width="60"><p><? echo implode(",",array_unique(explode(",",$row[csf('gsm')])));?>&nbsp;</p></td>
										<?
										$row_tot_roll=0; 
										$row_tot_qnty=0; $row_tot_qnty_non_order=0;  
										foreach($shift_name as $key=>$val)
										{
											$row_tot_qnty_non_order+=$row[csf('qntyshift'.strtolower($val))]; 
											?>
											<td width="80" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($val))],2); ?> </td>
											<?
											$grand_total_ship[$key]+=$row[csf('qntyshift'.strtolower($val))];
											$inhouse_ship_non_order[$key]+=$row[csf('qntyshift'.strtolower($val))];
										}
										?>
										<td width="100" align="right" ><? echo number_format($row_tot_qnty_non_order,2,'.',''); ?></td>
										<?
										if($temp_ono_order_arr[$row[csf('machine_no_id')]][$row[csf('receive_date')]]=="")
										{
											$temp_ono_order_arr[$row[csf('machine_no_id')]][$row[csf('receive_date')]]=$row[csf('machine_no_id')];
											?>
											<td width="100" valign="top" align="right" rowspan="<? echo $machine_without_array[$row[csf('machine_no_id')]]; ?>"><? echo number_format($machine_without_qty[$row[csf('machine_no_id')]],2,'.',''); ?></td>
											<?
											$grand_machine_total+=$machine_without_qty[$row[csf('machine_no_id')]];
											$machine_total_non_order+=$machine_without_qty[$row[csf('machine_no_id')]];
										}
										?>
                                        <td width="80" align="right"><p><? echo number_format($row[csf('reject_qty')],2,'.',''); ?></p></td>
										<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
									</tr>
									<?
									$inhouse_tot_qty_non_order+=$row_tot_qnty_non_order;
									$grand_tot_qnty+=$row_tot_qnty_non_order;
									$grand_reject_qty+=$row[csf('reject_qty')];
									
									$i++;
								}
							
								?>
								<tr class="tbl_bottom">
									<td colspan="19" align="right"><b>In-house Total(without order)</b></td>
									<?
									foreach($shift_name as $key=>$val)
									{
										?>
										<td align="right"><? echo number_format($inhouse_ship_non_order[$key],2,'.',''); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($inhouse_tot_qty_non_order,2,'.',''); ?></td>
									<td align="right"><? echo number_format($machine_total_non_order,2,'.',''); ?></td>
									<td align="right"><? echo number_format($grand_reject_qty,2,'.',''); ?></td>
                                    <td>&nbsp;</td>
								</tr>	
								<?
							}
						}
                        $j=0;
                        
                    ?>
                    </tbody>
                </table> 
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="rpt_tbl_footer">
                <tfoot>
                    <tr>
                        <th width="30" >&nbsp;</th>
                        <th width="90" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
                        <th width="60" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
                        <th width="70" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="80" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="150" >&nbsp;</th>
                        <th width="130" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th colspan="2">Grand Total</th>
                        <?
                        foreach($shift_name as $key=>$val)
                        {
                        ?>
                        <th align="right" width="80"><? echo number_format($grand_total_ship[$key],2,'.',''); ?></th>
                        <?
                        }
                        ?>
                        <th align="right" width="100"><? echo number_format($grand_tot_qnty,2,'.',''); ?></th>
                        <th align="right" width="100"><? echo number_format($grand_machine_total,2,'.',''); ?></th>
                        <th width="80" align="right"><? echo number_format($grand_reject_qty,2,'.',''); ?></th>
                        <th width="95">&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <br />
            <?
			if($txt_date_from!="")
			{
                if($txt_date_to=="") $txt_date_to=$txt_date_from;
				$date_distance=datediff("d",$txt_date_from, $txt_date_to);
				$month_name=date('F',strtotime($txt_date_from));
				$year_name=date('Y',strtotime($txt_date_from));
				$day_of_month=explode("-",$txt_date_from);
				if($db_type==0)
				{
					$fist_day_of_month=$day_of_month[2]*1;
				}
				else
				{
					$fist_day_of_month=$day_of_month[0]*1;
				}
				
				//echo $fist_day_of_month.jahid;
				$tot_machine=count($total_machine);
				//$running_machine=count($machine_inhouse_array)+count($machine_without_array);total_running_machine
				$running_machine=count($total_running_machine);
				$stop_machine=$tot_machine-$running_machine;
				$running_machine_percent=(($running_machine/$tot_machine)*100);
				$stop_machine_percent=(($stop_machine/$tot_machine)*100);
				if($date_distance==1 && $fist_day_of_month>1)
				{
					$query_cond_month=date('m',strtotime($txt_date_from));
					$query_cond_year=date('Y',strtotime($txt_date_from));
					$sql_cond="";
					if($db_type==0) $sql_cond="  and month(a.receive_date)='$query_cond_month' and year(a.receive_date)='$query_cond_year'"; else $sql_cond="  and to_char(a.receive_date,'mm')='$query_cond_month' and to_char(a.receive_date,'yyyy')='$query_cond_year'";
					if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
					//echo "select a.receive_date, sum(c.quantity ) as qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date<'".$txt_date_from."' $sql_cond group by a.receive_date <br>";
					$sql_montyly_inhouse=sql_select("select sum(c.quantity ) as qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date<'".$txt_date_from."' $sql_cond");
					
					
					$sql_monthly_wout_order=sql_select("select sum( b.grey_receive_qnty) as qnty  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 and a.receive_date<'".$txt_date_from."' $sql_cond");
					
					$yesterday_prod=$sql_montyly_inhouse[0][csf("qnty")]+$sql_monthly_wout_order[0][csf("qnty")];
					$today_prod=$yesterday_prod+$grand_tot_qnty;
				}
				//echo date('Y-m',strtotime($txt_date_from)).jahid;
				?>
				<table width="<? echo $tbl_width; ?>">
					<tr>
						<td width="25%"  valign="top">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
								<tr>
									<td>Total number of m/c running</td>
									<td width="100" align="right"><? echo $running_machine; ?></td>
									<td align="right" width="100"><? if( $running_machine_percent>0) echo number_format($running_machine_percent,2,'.','')." % "; ?></td>
								</tr>
								<tr>
									<td>Total number of m/c stop</td>
									<td align="right"><? echo $stop_machine; ?></td>
									<td align="right"><? if( $running_machine_percent>0) echo number_format($stop_machine_percent,2,'.','')." % "; ?></td>
								</tr>
								<tr>
									<td>Total production</td>
									<td align="right"><? echo number_format($grand_tot_qnty,2); ?></td>
									<td align="center">Kg</td>
								</tr>
							</table>
						</td>
						<td width="10%"  valign="top">&nbsp; </td>
						<td  width="25%" valign="top">
						<?
						if($date_distance==1 && $fist_day_of_month>1)
						{
							?>
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
                                <tr>
                                    <td>Upto yesterday Production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
                                    <td  align="right" width="100"><? echo number_format($yesterday_prod,2); ?></td>
                                    <td align="center" width="100">Kg</td>
                                </tr>
								<tr>
									<td>Upto today production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
									<td align="right"><? echo number_format($today_prod,2); ?> </td>
									<td align="center">Kg</td>
								</tr>
							</table>
							<?
						}
						?>
						</td>
						<td  valign="top">&nbsp; </td>
					</tr>
				</table>
                <?
			}
			?>
            
        </fieldset> 
        <br> 
	<?
	
	foreach (glob("../../../ext_resource/tmp_report/$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="../../../ext_resource/tmp_report/".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="../../../ext_resource/tmp_report/".$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();		
			
	}
	
	
}

if($action=="report_generate_today")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_type=str_replace("'","",$cbo_type);
	$cbo_year=str_replace("'","",$cbo_year); 
	$txt_job=str_replace("'","",$txt_job);
	$txt_order=str_replace("'","",$txt_order);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$from_date=$txt_date_from;
	if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;
	
	$tbl_width=2030+count($shift_name)*155;
	$col_span=25+count($shift_name)*2;
	ob_start();
	?>
    <fieldset style="width:<? echo $tbl_width+20; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
			   <td align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:18px"><? echo "Daily Inhouse Knitting Production Report"; ?></td>
			</tr>
			<tr>
			   <td align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
			</tr>
			<tr> 
			   <td align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:12px" ><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
			</tr>
		</table>
        <?
        if($cbo_type==1 || $cbo_type==0)
		{
		?>
        <div>
        <div align="left" style="background-color:#E1E1E1; color:#000; width:350px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Order (In-House) Knitting Production</i></u></strong></div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" >
                <thead>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="60" rowspan="2">Job No</th>
                        <th width="60" rowspan="2">Year</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Style</th>
                        <th width="110" rowspan="2">Order No</th>
                        <th width="60" rowspan="2">Lot No</th>
                        <th width="150" rowspan="2">Fabric Type</th>
                        <th width="50" rowspan="2">Stitch</th>
                        <th width="60" rowspan="2">Fin GSM</th>
                        <th width="100" rowspan="2">Fabric Color</th>
                        <th width="90" rowspan="2">Req. Qty.</th>
                        <th width="150" colspan="2">Prev. Production</th>
                        <?
                        foreach($shift_name as $val)
                        {
                        ?>
                            <th width="150" colspan="2"><? echo $val; ?></th>
                        <?	
                        }
                        ?>
                        <th width="150" colspan="2">No Shift</th>
                        <th width="150" colspan="2">Today Production</th>
                        <th width="150" colspan="2">Total Production</th>
                        <th width="100" rowspan="2">Yet To Production</th>
                        <th width="70" rowspan="2">Rate</th>
                        <th width="100" rowspan="2">Today Revenue</th>
                        <th width="100" rowspan="2">Total Revenue</th>
                        <th rowspan="2">Remarks</th>
                    </tr>
                    <tr>
                        <th width="50">Roll</th>
                        <th width="100">Qnty</th>
                        <?
                        foreach($shift_name as $val)
                        {
                        ?>
                            <th width="50">Roll</th>
                            <th width="100">Qnty</th>
                        <?	
                        }
                        ?>
                        <th width="50">Roll</th>
                        <th width="100">Qnty</th>
                        <th width="50">Roll</th>
                        <th width="100">Qnty</th>
                        <th width="50">Roll</th>
                        <th width="100">Qnty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<? echo $tbl_width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_body">
                <?
				$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
				$buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
				$curr_value=return_library_array( "select job_no,exchange_rate from wo_pre_cost_mst", "job_no", "exchange_rate");
				
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
				
				/*$convirsioncostArray=array();
				$convirsioncostDataArray=sql_select("select job_no, sum(req_qnty) as req_qnty, sum(amount) as amount from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($convirsioncostDataArray as $conRow)
				{
				   $convirsioncostArray[$conRow[csf('job_no')]]=$conRow[csf('req_qnty')]."**".$conRow[csf('amount')].",";
				}*/
				$req_qty_arr=array();
				$sql_req="select a.po_break_down_id, a.gsm_weight, a.dia_width, b.lib_yarn_count_deter_id, sum(a.grey_fab_qnty) as req_qty, sum(c.charge_unit) as rate from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_cost_fab_conv_cost_dtls c where a.pre_cost_fabric_cost_dtls_id=b.id and b.id=c.fabric_description and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.cons_process=1 group by a.po_break_down_id, a.gsm_weight, a.dia_width, b.lib_yarn_count_deter_id";
				$sql_req_result=sql_select($sql_req);
				foreach( $sql_req_result as $row )
				{
					$req_qty_arr[$row[csf('po_break_down_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['req_qty']=$row[csf('req_qty')];
					$req_qty_arr[$row[csf('po_break_down_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['rate']=$row[csf('rate')];
				}
				
				$prev_production_arr=array();
				$prev_sql="select d.febric_description_id, d.gsm, d.width, e.po_breakdown_id, sum(e.quantity) as  beforeqnty, sum(d.no_of_roll) as beforeroll from inv_receive_master c, pro_grey_prod_entry_dtls d, order_wise_pro_details e where c.id=d.mst_id and d.id=e.dtls_id and c.entry_form=2 and c.item_category=13 and e.entry_form=2 and e.trans_type=1 and c.knitting_source=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.receive_date<'".$from_date."' group by d.febric_description_id, d.gsm, d.width, e.po_breakdown_id";
				$prev_sql_result=sql_select($prev_sql);
				foreach( $prev_sql_result as $row )
				{
					$prev_production_arr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['qty']=$row[csf('beforeqnty')];
					$prev_production_arr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['roll']=$row[csf('beforeroll')];
				}
				//print_r($prev_production_arr);
				$i=1; $tot_rolla=''; $tot_rollb=''; $tot_rollc=''; $tot_rolla_qnty=0; $tot_rollb_qnty=0; $tot_rollc_qnty=0; $grand_tot_roll=''; $grand_tot_qnty=0; $tot_subcontract=0;
				if($db_type==0)
				{
					$year_field="YEAR(a.insert_date)";
					$year_field_sam="YEAR(a.insert_date)";
					if($cbo_year!=0) $job_year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $job_year_cond="";
				}
				else if($db_type==2)
				{
					$year_field="to_char(a.insert_date,'YYYY')";
					$year_field_sam="to_char(a.insert_date,'YYYY')";
					if($cbo_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";
				}
				else $year_field="";
				
				$date_con="";
				if($from_date!="" && $to_date!="") $date_con=" and c.receive_date between '$from_date' and '$to_date'";
				
				if($db_type==0)
				{
					$select_color=", d.color_id as color_id";
					$group_color=", d.color_id";
				}
				else if($db_type==2)
				{
					$select_color=", nvl(d.color_id,0) as color_id";
					$group_color=", nvl(d.color_id,0)";
				}
				 
				$sql_inhouse="select a.job_no, a.job_no_prefix_num, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, d.yarn_lot, d.prod_id, d.stitch_length, d.gsm $select_color, d.febric_description_id, d.width,
				sum(case when d.shift_name=0 then e.quantity else 0 end ) as qntynoshift, 
				sum(case when d.shift_name=0 then d.no_of_roll end ) as rollnoshift";
				foreach($shift_name as $key=>$val)
				{
					$sql_inhouse.=", sum(case when d.shift_name=$key then d.no_of_roll end ) as roll".strtolower($val)."	
					, sum(case when d.shift_name=$key then e.quantity else 0 end ) as qntyshift".strtolower($val);
				}
				$sql_inhouse.=" from wo_po_details_master a, wo_po_break_down b, inv_receive_master c, pro_grey_prod_entry_dtls d, order_wise_pro_details e where c.company_id=$cbo_company_name and a.job_no=b.job_no_mst and c.id=d.mst_id and d.id=e.dtls_id and e.po_breakdown_id=b.id and c.entry_form=2 and c.item_category=13 and e.entry_form=2 and e.trans_type=1 and c.knitting_source=1 
				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_con $job_year_cond
				group by a.job_no, a.job_no_prefix_num, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, d.yarn_lot, d.prod_id, d.stitch_length, d.gsm, d.color_id,  d.febric_description_id, d.width order by a.job_no_prefix_num DESC
				";
				//echo $sql_inhouse;
				
				/* b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id, b.floor_id $select_color, c.po_breakdown_id, d.machine_no as machine_name, f.job_no_prefix_num, $year_field as year, e.po_number, sum(case when b.shift_name=0 then c.quantity else 0 end ) as qntynoshift, sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift";
				foreach($shift_name as $key=>$val)
				{
					$sql_inhouse.=", sum(case when b.shift_name=$key then b.no_of_roll end ) as roll".strtolower($val)."	
					, sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val);
				}
				$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond
				group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, c.po_breakdown_id, d.machine_no, d.seq_no order by b.floor_id,a.receive_date, d.seq_no";*/
				
				
				
				/*$sql_inhouse="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count,b.stitch_length, b.brand_id, b.machine_no_id, b.floor_id $select_color, c.po_breakdown_id, d.machine_no as machine_name, f.job_no_prefix_num, $year_field as year, e.po_number, sum(case when b.shift_name=0 then c.quantity else 0 end ) as qntynoshift, sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift";
				foreach($shift_name as $key=>$val)
				{
					$sql_inhouse.=", sum(case when b.shift_name=$key then b.no_of_roll end ) as roll".strtolower($val)."	
					, sum(case when b.shift_name=$key then c.quantity else 0 end ) as qntyshift".strtolower($val);
				}
				$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond
				group by a.recv_number,b.stitch_length, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.floor_id, b.machine_no_id, b.yarn_lot, b.yarn_count, b.brand_id $group_color, f.job_no_prefix_num, f.insert_date, e.po_number, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, c.po_breakdown_id, d.machine_no, d.seq_no order by b.floor_id,a.receive_date, d.seq_no";*/
				$nameArray_inhouse=sql_select( $sql_inhouse); $z=0;
				foreach($nameArray_inhouse as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$req_qty=$req_qty_arr[$row[csf('id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['req_qty'];
					$avg_rate=$req_qty_arr[$row[csf('id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['rate'];
					$prev_qty=$prev_production_arr[$row[csf('id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['qty'];
					$prev_roll=$prev_production_arr[$row[csf('id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['roll'];
					//echo $prev_qty.'==';
					$exchange_rate=$curr_value[$row[csf('job_no')]]*$avg_rate;
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                        <td width="30"><? echo $i; ?></td>
                        <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="60"><? echo $row[csf('year')]; ?></td>
                        <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                        <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="60"><p><? echo $row[csf('yarn_lot')]; ?></p></td>
                        <td width="150"><p><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></p></td>
                        <td width="50"><P><? echo $row[csf('stitch_length')]; ?></P></td>
                        <td width="60"><P><? echo $row[csf('gsm')]; ?></P></td>
                        <td width="100"><P>
						<? 
						//echo $color_library[$row[csf("color_id")]];
						$color_arr=array_unique(explode(",",$row[csf('color_id')]));
						$all_color="";
						foreach($color_arr as $id)
						{
							$all_color.=$color_library[$id].",";
						}
						$all_color=chop($all_color," , ");
						echo $all_color; 
						?></P></td>
                        <td width="90" align="right"><? echo number_format($req_qty,2,'.',''); ?></td>
                        <td width="50" align="right"><? echo number_format($prev_roll,2,'.',''); ?></td>
                        <td width="100" align="right"><? echo number_format($prev_qty,2,'.',''); ?></td>
                        <?
                        $row_tot_roll=0; 
                        $row_tot_qnty=0; 
                        foreach($shift_name as $key=>$val)
                        {
                            $tot_roll[$key]['roll']+=$row[csf('roll'.strtolower($val))];
                            $tot_roll[$key]['qty']+=$row[csf('qntyshift'.strtolower($val))];
                            
                            $row_tot_roll+=$row[csf('roll'.strtolower($val))]; 
                            $row_tot_qnty+=$row[csf('qntyshift'.strtolower($val))]; 
                        ?>
                            <td width="50" align="right" ><? echo $row[csf('roll'.strtolower($val))]; ?></td>
                            <td width="100" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($val))],2); ?></td>
                        <?
                        }
                        ?>
                        <td width="50" align="right"><? echo $row[csf('rollnoshift')]; ?></td>
                        <td width="100" align="right"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
                        
                        <td width="50" align="right"><? $today_production_roll=$row_tot_roll+$row[csf('rollnoshift')]; echo $today_production_roll; ?></td>
                        <td width="100" align="right"><? $today_production_qty=$row_tot_qnty+$row[csf('qntynoshift')]; echo number_format($today_production_qty,2,'.',''); ?></td>

                        <?
							$tot_production_roll=$prev_roll+$row_tot_roll+$row[csf('rollnoshift')];
							$tot_production_qty=$prev_qty+$row_tot_qnty+$row[csf('qntynoshift')];
						?>
                        <td width="50" align="right"><? echo $tot_production_roll; ?></td>
                        <td width="100" align="right"><? echo number_format($tot_production_qty,2); ?></td>
                        
                        <td width="100" align="right"><? $yet_prod=$req_qty-$tot_production_qty; echo number_format($yet_prod,2); ?></td>
                        <td width="70" align="right"><? echo number_format($exchange_rate,4); ?></td>
                        <td width="100" align="right"><? $today_revenue=$today_production_qty*$exchange_rate; echo number_format($today_revenue,2); ?></td>
                        <td width="100" align="right"><? $tot_revenue=$tot_production_qty*$exchange_rate; echo number_format($tot_revenue,2); ?></td>
                        <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                    </tr>
					<?
                    $total_req_qty+=$req_qty;
                    $total_prev_roll+=$prev_roll;
                    $total_prev_qty+=$prev_qty;
                    $total_noshift_roll+=$row[csf('rollnoshift')];
                    $total_noshift_qty+=$row[csf('qntynoshift')];
                    $total_today_production_roll+=$today_production_roll;
                    $total_today_production_qty+=$today_production_qty;
                    $total_production_roll+=$tot_production_roll;
                    $total_production_qty+=$tot_production_qty;
                    $total_yet_production+=$yet_prod;
                    $total_today_revenue+=$today_revenue;
                    $total_revenue+=$tot_revenue;
                    //$z++;
                    $i++;
				}
				?>
            </table>
            </div>
            <table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all">
                <tr>
                    <td align="right" width="30">&nbsp;</td>
                    <td align="right" width="60">&nbsp;</td>
                    <td align="right" width="60">&nbsp;</td>
                    <td align="right" width="70">&nbsp;</td>
                    <td align="right" width="100">&nbsp;</td>
                    <td align="right" width="110">&nbsp;</td>
                    <td align="right" width="60">&nbsp;</td>
                    <td align="right" width="150">&nbsp;</td>
                    <td align="right" width="50">&nbsp;</td>
                    <td align="right" width="60">&nbsp;</td>
                    <td align="right" width="100"><strong>Total</strong></td>
                    <td align="right" width="90"><? echo number_format($total_req_qty,2); ?></td>
                    <td align="right" width="50"><? echo number_format($total_prev_roll,2); ?></td>
                    <td align="right" width="100"><? echo number_format($total_prev_qty,2); ?></td>
                    <?
					foreach($shift_name as $key=>$val)
					{
					?>
						<td align="right" width="50"><? echo number_format($tot_roll[$key]['roll'],2,'.',''); ?></td>
						<td align="right" width="100"><? echo number_format($tot_roll[$key]['qty'],2,'.',''); ?></td>
					<?
					}
					?>
                    <td align="right" width="50"><? echo number_format($total_noshift_roll,2); ?></td>
                    <td align="right" width="100"><? echo number_format($total_noshift_qty,2); ?></td>
                    <td align="right" width="50"><? echo number_format($total_today_production_roll,2); ?></td>
                    <td align="right" width="100"><? echo number_format($total_today_production_qty,2); ?></td>
                    <td align="right" width="50"><? echo number_format($total_production_roll,2); ?></td>
                    <td align="right" width="100"><? echo number_format($total_production_qty,2); ?></td>
                    <td align="right" width="100"><? echo number_format($total_yet_production,2); ?></td>
                    <td align="right" width="70">&nbsp;</td>
                    <td align="right" width="100"><? echo number_format($total_today_revenue,2); ?></td> 
                    <td align="right" width="100"><? echo number_format($total_revenue,2); ?></td> 
                    <td align="right">&nbsp;</td> 
                </tr>
            </table>
        </div>
        <?
		}
		if($cbo_type==2 || $cbo_type==0)
		{
			$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
			$buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
			
			if($db_type==0)
			{
				$year_sub_field="YEAR(a.insert_date)";
				if($cbo_year!=0) $job_year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $job_year_cond="";
			}
			else if($db_type==2)
			{
				$year_sub_field="to_char(a.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_sub_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";  else $job_year_sub_cond="";
			}
			else $year_sub_field="";
			
			if($db_type==0)
			{
				$select_color=", d.color_id as color_id";
				$group_color=", d.color_id";
			}
			else if($db_type==2)
			{
				$select_color=", nvl(d.color_id,0) as color_id";
				$group_color=", nvl(d.color_id,0)";
			}
			
			$req_qty_arr=array();
			$sql_req="select order_id, item_id, sum(qnty) as req_qty, avg(rate) as rate from  subcon_ord_breakdown group by order_id, item_id";
			$sql_req_result=sql_select( $sql_req);
			foreach($sql_req_result as $row)
			{
				$req_qty_arr[$row[csf('order_id')]][$row[csf('item_id')]]['req_qty']=$row[csf('req_qty')];
				$req_qty_arr[$row[csf('order_id')]][$row[csf('item_id')]]['rate']=$row[csf('rate')];
			}
			
			if($from_date!="" && $to_date!="") $date_con_sub=" and c.product_date between '$from_date' and '$to_date'"; else $date_con_sub="";
			$prev_produ_arr=array();
			$sql_prev="select b.order_id, b.cons_comp_id, b.gsm, b.dia_width, sum(b.product_qnty) as prev_qty, sum(b.no_of_roll) as prev_roll from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.product_date<'".$from_date."'  group by b.order_id, b.cons_comp_id, b.gsm, b.dia_width";
			$sql_prev_result=sql_select( $sql_prev);
			foreach($sql_prev_result as $row)
			{
				$prev_produ_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_qty']=$row[csf('prev_qty')];
				$prev_produ_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_roll']=$row[csf('prev_roll')];
			}
			//var_dump($prev_produ_arr);
			if($db_type==0)
			{
				$job_ord_cond="and d.order_id=b.id";
			}
			else if ($db_type==2)
			{
				$job_ord_cond="and d.job_no=b.job_no_mst";
			}
			$sql_inhouse_sub="select a.job_no_prefix_num, a.party_id, $year_sub_field as year, d.order_id as id, b.order_no, b.cust_style_ref, d.cons_comp_id, d.gsm, d.dia_width, d.yarn_lot, d.stitch_len $select_color, sum(case when d.shift=0 then d.product_qnty else 0 end ) as qntynoshift, sum(case when d.shift=0 then d.no_of_roll end ) as rollnoshift";
			foreach($shift_name as $key=>$val)
			{
				$sql_inhouse_sub.=", sum(case when d.shift=$key then d.no_of_roll end ) as roll".strtolower($val)."	
				, sum(case when d.shift=$key then d.product_qnty else 0 end ) as qntyshift".strtolower($val);
			}
			$sql_inhouse_sub.="
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_production_mst c, subcon_production_dtls d where c.company_id=$cbo_company_name and a.subcon_job=b.job_no_mst and c.id=d.mst_id $job_ord_cond and c.product_type=2 $job_year_sub_cond $date_con_sub group by a.job_no_prefix_num, a.party_id, a.insert_date, d.order_id, b.order_no, b.cust_style_ref, d.cons_comp_id, d.gsm, d.dia_width, d.yarn_lot, d.stitch_len, d.color_id order by a.job_no_prefix_num DESC ";
			
			/*
			$sql_inhouse_sub="select b.id, a.prefix_no_num, a.product_no, a.product_date, a.party_id, a.remarks, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type, b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id, b.floor_id $select_color, b.order_id, c.machine_no as machine_name, e.job_no_prefix_num, $year_sub_field as year, d.order_no, d.cust_style_ref, sum(case when b.shift=0 then b.product_qnty else 0 end ) as qntynoshift, sum(case when b.shift=0 then b.no_of_roll end ) as rollnoshift";
			foreach($shift_name as $key=>$val)
			{
				$sql_inhouse_sub.=", sum(case when b.shift=$key then b.no_of_roll end ) as roll".strtolower($val)."	
				, sum(case when b.shift=$key then b.product_qnty else 0 end ) as qntyshift".strtolower($val);
			}
			$sql_inhouse_sub.=" from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d, subcon_ord_mst e 
			where a.id=b.mst_id and b.machine_id=c.id and b.order_id=d.id and e.subcon_job=d.job_no_mst and a.product_type=2 
			 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond
			group by b.id, a.prefix_no_num, a.product_no, a.product_date, a.party_id, a.remarks, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type,  b.yarn_lot, b.yrn_count_id, b.stitch_len, b.brand, b.machine_id, b.floor_id, b.color_id, b.order_id, c.machine_no, e.job_no_prefix_num, e.insert_date, d.order_no, d.cust_style_ref, c.seq_no order by b.floor_id, a.product_date, c.seq_no";*/
			//echo $sql_inhouse_sub;
			$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub); $k=1; $tot_roll_sub=array();
			if(count($nameArray_inhouse_subcon)>0)
			{
				//$tbl_width=1600+count($shift_name)*157;
				?>
                <br>
                <div>
               <div align="left" style="background-color:#E1E1E1; color:#000; width:350px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Sub-Contract Order Knitting Production</i></u></strong></div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" >
                <thead>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="60" rowspan="2">Job No</th>
                        <th width="60" rowspan="2">Year</th>
                        <th width="70" rowspan="2">Party</th>
                        <th width="100" rowspan="2">Cust Style</th>
                        <th width="110" rowspan="2">Order No</th>
                        <th width="60" rowspan="2">Lot No</th>
                        <th width="150" rowspan="2">Fabric Type</th>
                        <th width="50" rowspan="2">Stitch</th>
                        <th width="60" rowspan="2">Fin GSM</th>
                        <th width="100" rowspan="2">Fabric Color</th>
                        <th width="90" rowspan="2">Req. Qty.</th>
                        <th width="150" colspan="2">Prev. Production</th>
                        <?
                        foreach($shift_name as $val)
                        {
                        ?>
                            <th width="150" colspan="2"><? echo $val; ?></th>
                        <?	
                        }
                        ?>
                        <th width="150" colspan="2">No Shift</th>
                        <th width="150" colspan="2">Today Production</th>
                        <th width="150" colspan="2">Total Production</th>
                        <th width="100" rowspan="2">Yet To Production</th>
                        <th width="70" rowspan="2">Rate</th>
                        <th width="100" rowspan="2">Today Revenue</th>
                        <th width="100" rowspan="2">Total Revenue</th>
                        <th rowspan="2">Remarks</th>
                    </tr>
                    <tr>
                        <th width="50">Roll</th>
                        <th width="100">Qnty</th>
                        <?
                        foreach($shift_name as $val)
                        {
                        ?>
                            <th width="50">Roll</th>
                            <th width="100">Qnty</th>
                        <?	
                        }
                        ?>
                        <th width="50">Roll</th>
                        <th width="100">Qnty</th>
                        <th width="50">Roll</th>
                        <th width="100">Qnty</th>
                        <th width="50">Roll</th>
                        <th width="100">Qnty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<? echo $tbl_width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_body">
                <?
				foreach($nameArray_inhouse_subcon as $row)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$req_qty=$req_qty_arr[$row[csf('id')]][$row[csf('cons_comp_id')]]['req_qty'];
					$avg_rate=$req_qty_arr[$row[csf('id')]][$row[csf('cons_comp_id')]]['rate'];
					$prev_qty=$prev_produ_arr[$row[csf('id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_qty'];
					$prev_roll=$prev_produ_arr[$row[csf('id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['prev_roll'];
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trw_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trw_<? echo $k; ?>"> 
                        <td width="30"><? echo $k; ?></td>
                        <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="60"><? echo $row[csf('year')]; ?></td>
                        <td width="70"><p><? echo $buyer_library[$row[csf('party_id')]]; ?></p></td>
                        <td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
                        <td width="110"><p><? echo $row[csf('order_no')]; ?></p></td>
                        <td width="60"><p><? echo $row[csf('yarn_lot')]; ?></p></td>
                        <td width="150"><p><? echo $const_comp_arr[$row[csf('cons_comp_id')]]; ?></p></td>
                        <td width="50"><P><? echo $row[csf('stitch_len')]; ?></P></td>
                        <td width="60"><P><? echo $row[csf('gsm')]; ?></P></td>
                        <td width="100"><P>
						<? 
						//echo $color_library[$row[csf("color_id")]];
						$color_arr=array_unique(explode(",",$row[csf('color_id')]));
						$all_color="";
						foreach($color_arr as $id)
						{
							$all_color.=$color_library[$id].",";
						}
						$all_color=chop($all_color," , ");
						echo $all_color; 
						?></P></td>
                        <td width="90" align="right"><? echo number_format($req_qty,2,'.',''); ?></td>
                        <td width="50" align="right"><? echo number_format($prev_roll,2,'.',''); ?></td>
                        <td width="100" align="right"><? echo number_format($prev_qty,2,'.',''); ?></td>
                        <?
                        $row_tot_roll=0; 
                        $row_tot_qnty=0; 
                        foreach($shift_name as $key=>$name)
                        {
							//echo strtolower($val);
                            $tot_roll_sub[$key]['roll']+=$row[csf('roll'.strtolower($name))];
                            $tot_roll_sub[$key]['qty']+=$row[csf('qntyshift'.strtolower($name))];
                            
                            $row_tot_roll+=$row[csf('roll'.strtolower($name))]; 
                            $row_tot_qnty+=$row[csf('qntyshift'.strtolower($name))]; 
                        ?>
                            <td width="50" align="right" ><? echo $row[csf('roll'.strtolower($name))]; ?></td>
                            <td width="100" align="right" ><? echo number_format($row[csf('qntyshift'.strtolower($name))],2); ?></td>
                        <?
                        }
                        ?>
                        <td width="50" align="right"><? echo $row[csf('rollnoshift')]; ?></td>
                        <td width="100" align="right"><? echo number_format($row[csf('qntynoshift')],2); ?></td>
                        
                        <td width="50" align="right"><? $today_production_roll=$row_tot_roll+$row[csf('rollnoshift')]; echo $today_production_roll; ?></td>
                        <td width="100" align="right"><? $today_production_qty=$row_tot_qnty+$row[csf('qntynoshift')]; echo number_format($today_production_qty,2,'.',''); ?></td>

                        <?
							$tot_production_roll=$prev_roll+$row_tot_roll+$row[csf('rollnoshift')];
							$tot_production_qty=$prev_qty+$row_tot_qnty+$row[csf('qntynoshift')];
						?>
                        <td width="50" align="right"><? echo $tot_production_roll; ?></td>
                        <td width="100" align="right"><? echo number_format($tot_production_qty,2); ?></td>
                        
                        <td width="100" align="right"><? $yet_prod=$req_qty-$tot_production_qty; echo number_format($yet_prod,2); ?></td>
                        <td width="70" align="right"><? echo number_format($avg_rate,4); ?></td>
                        <td width="100" align="right"><? $today_revenue=$today_production_qty*$avg_rate; echo number_format($today_revenue,2); ?></td>
                        <td width="100" align="right"><? $tot_revenue=$tot_production_qty*$avg_rate; echo number_format($tot_revenue,2); ?></td>
                        <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                    </tr>
					<?
                    $sub_total_req_qty+=$req_qty;
                    $sub_total_prev_roll+=$prev_roll;
                    $sub_total_prev_qty+=$prev_qty;
                    $sub_total_noshift_roll+=$row[csf('rollnoshift')];
                    $sub_total_noshift_qty+=$row[csf('qntynoshift')];
                    $sub_total_today_production_roll+=$today_production_roll;
                    $sub_total_today_production_qty+=$today_production_qty;
                    $sub_total_production_roll+=$tot_production_roll;
                    $sub_total_production_qty+=$tot_production_qty;
                    $sub_total_yet_production+=$yet_prod;
                    $sub_total_today_revenue+=$today_revenue;
                    $sub_total_revenue+=$tot_revenue;
                    //$z++;
                    $k++;
				}
				?>
                </table>
            </div>
            <table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all">
                <tr>
                    <td align="right" width="30">&nbsp;</td>
                    <td align="right" width="60">&nbsp;</td>
                    <td align="right" width="60">&nbsp;</td>
                    <td align="right" width="70">&nbsp;</td>
                    <td align="right" width="100">&nbsp;</td>
                    <td align="right" width="110">&nbsp;</td>
                    <td align="right" width="60">&nbsp;</td>
                    <td align="right" width="150">&nbsp;</td>
                    <td align="right" width="50">&nbsp;</td>
                    <td align="right" width="60">&nbsp;</td>
                    <td align="right" width="100"><strong>Total</strong></td>
                    <td align="right" width="90"><? echo number_format($sub_total_req_qty,2); ?></td>
                    <td align="right" width="50"><? echo number_format($sub_total_prev_roll,2); ?></td>
                    <td align="right" width="100"><? echo number_format($sub_total_prev_qty,2); ?></td>
                    <?
					foreach($shift_name as $key=>$val)
					{
					?>
						<td align="right" width="50"><? echo number_format($tot_roll_sub[$key]['roll'],2,'.',''); ?></td>
						<td align="right" width="100"><? echo number_format($tot_roll_sub[$key]['qty'],2,'.',''); ?></td>
					<?
					}
					?>
                    <td align="right" width="50"><? echo number_format($sub_total_noshift_roll,2); ?></td>
                    <td align="right" width="100"><? echo number_format($sub_total_noshift_qty,2); ?></td>
                    <td align="right" width="50"><? echo number_format($sub_total_today_production_roll,2); ?></td>
                    <td align="right" width="100"><? echo number_format($sub_total_today_production_qty,2); ?></td>
                    <td align="right" width="50"><? echo number_format($sub_total_production_roll,2); ?></td>
                    <td align="right" width="100"><? echo number_format($sub_total_production_qty,2); ?></td>
                    <td align="right" width="100"><? echo number_format($sub_total_yet_production,2); ?></td>
                    <td align="right" width="70">&nbsp;</td>
                    <td align="right" width="100"><? echo number_format($sub_total_today_revenue,2); ?></td> 
                    <td align="right" width="100"><? echo number_format($sub_total_revenue,2); ?></td> 
                    <td align="right">&nbsp;</td> 
                </tr>
            </table>
        </div>
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
	echo "$total_data####$filename";
	
	disconnect($con);
	exit();
}

if($action=="delivery_challan_print")
{
	echo load_html_head_contents("Delivery Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);	
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$datas=explode('_',$data);
	$program_ids = $datas[0];
	$source_ids = $datas[1];
	$company = $datas[2];
	$from_date = $datas[3];
	$to_date = $datas[4];
	$in_out_data=explode(',',$datas[1]);
	//echo $from_date;
	$company_details=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	//$poNumber_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	
	$machine_details=array();
	$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
	foreach($machine_data as $row)
	{
		$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}
	
	$po_array=array();
	$po_data=sql_select("select a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id, b.po_number as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company group by b.po_number, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id");
	foreach($po_data as $row)
	{
		$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')];
		$po_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
	}
	
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
	
	$knit_plan_arr=array();
	$plan_data=sql_select("select id, color_range, stitch_length from ppl_planning_info_entry_dtls");
	foreach($plan_data as $row)
	{
		$knit_plan_arr[$row[csf('id')]]['cr']=$row[csf('color_range')];
		$knit_plan_arr[$row[csf('id')]]['sl']=$row[csf('stitch_length')]; 
	}	
	
	?>
	<div style="width:1360px;">
		<table width="1350" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="17" align="center" style="font-size:x-large"><strong><? echo $company_details[$company]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="17" align="center">
					<?
						$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$company"); 
						foreach ($nameArray as $result)
						{ 
						?>
							Plot No: <? echo $result[csf('plot_no')]; ?> 
							Level No: <? echo $result[csf('level_no')]?>
							Road No: <? echo $result[csf('road_no')]; ?> 
							Block No: <? echo $result[csf('block_no')];?> 
							City No: <? echo $result[csf('city')];?> 
							Zip Code: <? echo $result[csf('zip_code')]; ?> 
							Province No: <?php echo $result[csf('province')];?> 
							Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
							Email Address: <? echo $result[csf('email')];?> 
							Website No: <? echo $result[csf('website')];
						}
					?> 
				</td>
			</tr>
			<tr>
				<td colspan="17" align="center" style="font-size:18px"><strong><u>Delivery Challan</u></strong></center></td>
			</tr>
			<tr>
				<td colspan="17" align="center" style="font-size:16px"><strong><u>Knitting Section</u></strong></center></td>
			</tr>
            <tr >
				<td colspan="17"  style="font-size:14px"><strong><? echo "Date Range :"." ". $from_date." "."To"." ".$to_date; ?></strong></center></td>
			</tr>
        </table>
    </div>
    <div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30" >SL</th>
                    <th width="60" >Job No</th>
                    <th width="90" >Order No</th>
                    <th width="60" >Buyer</th>
                    <th width="50" >Prod. ID</th>
                    <th width="60" >M/C No</th>
                    <th width="60" >Req. No</th>
                    <th width="90" >Booking No/ Prog. No</th>
                    <th width="60" >Yarn Count</th>
                    <th width="70" >Yarn Brand</th>
                    <th width="70" >Lot No</th>
                    <th width="100" >Color</th>
                    <th width="" >Fabric Type</th>
                    <th width="50" >Stitch</th>
                    <th width="50" >Fin GSM</th>
                    <th width="50" >Fab. Dia</th>
                    <th width="50" >M/C Dia</th>
                    <th width="50" >Total Roll</th>
                    <th width="70" >Total Qty</th>
                </tr>
            </thead>
        </table>
    <div style="width:1350px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" >
          
	<?	
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no" );
	$reqsn_details=return_library_array( "select knit_id, requisition_no from ppl_yarn_requisition_entry group by knit_id", "knit_id", "requisition_no"  );
	
	if($db_type==2) $date_cond="'".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
	if($db_type==0) $date_cond="'".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	if($in_out_data[0]==1)
	{
		$sql="select c.id, b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, c.po_breakdown_id,sum(case when c.entry_form=2 then b.no_of_roll else 0 end)  as roll_no, sum(case when c.entry_form=2 then c.quantity else 0 end)  as outqntyshift  
		from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
		where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.trans_type=1 and a.company_id=$company and a.knitting_source=1 and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
		group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id, c.po_breakdown_id, c.id, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id	
		order by a.receive_date";	
	}
	else if ($in_out_data[0]==3)
	{
		$sql="select c.id, b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, c.po_breakdown_id, sum(case when c.entry_form=2 then c.quantity else 0 end)  as outqntyshift  from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.trans_type=1 and a.company_id=$company and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
		group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id, c.po_breakdown_id, c.id, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id 
		order by b.floor_id,a.receive_date";	
	}
	else
	{
		$sql="select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id,sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift, sum(case when a.entry_form=2 then b.grey_receive_qnty else 0 end)  as outqntyshift		
				 from inv_receive_master a, pro_grey_prod_entry_dtls b
				 where  a.item_category=13 and a.id=b.mst_id and a.company_id=$company and a.knitting_source=1 and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=2 
				 and a.booking_without_order=1
				 group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id	
				 order by a.receive_date";
	}
	//echo $sql;
	$nameArray=sql_select( $sql); $i=1; $tot_roll=0; $tot_qty=0;
	foreach($nameArray as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			
		$count='';
		$yarn_count=explode(",",$row[csf('yarn_count')]);
		foreach($yarn_count as $count_id)
		{
			if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
		}
		
		$reqsn_no=""; $stitch_length=""; $color="";
		if($row[csf('receive_basis')]==2)
		{
			$reqsn_no=$reqsn_details[$row[csf('booking_id')]]; 
			$stitch_length=$knit_plan_arr[$row[csf('booking_id')]]['sl']; 
			$color=$color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
		}
	?>
        <tr bgcolor="<? echo $bgcolor; ?>">
        	<td width="30"><div style="word-wrap:break-word; width:30px;"><? echo $i; ?></div></td>
            <td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?></div></td>
            <td width="90"><div style="word-wrap:break-word; width:90px;"><? echo $po_array[$row[csf('po_breakdown_id')]]['no']; ?></div></td>
            <td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div></td>
            <td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
            <td width="60" align="center"><div style="word-wrap:break-word; width:60px;"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></div></td>
            <td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $reqsn_no; ?></div></td>
            <td width="90"><div style="word-wrap:break-word; width:90px;"><? echo $row[csf('booking_no')]; ?></div></td>
            <td width="60"><div style="word-wrap:break-word; width:60px;"><? echo $count; ?></div></td>
            <td width="70"><div style="word-wrap:break-word; width:70px;"><? echo $brand_details[$row[csf('brand_id')]]; ?></div></td>
            <td width="70"><div style="word-wrap:break-word; width:70px;"><? echo $row[csf('yarn_lot')]; ?></div></td>
            <td width="100"><div style="word-wrap:break-word; width:100px;"><? echo $color; ?></div></td>
            <td width=""><div style="word-wrap:break-word; width:210px;"><? echo $composition_arr[$row[csf('febric_description_id')]];; ?></div></td>
            <td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $stitch_length; ?></div></td>
            <td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('gsm')]; ?></div></td>
            <td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('width')]; ?></div></td>
            <td width="50"><div style="word-wrap:break-word; width:50px;"><? echo $machine_details[$row[csf('machine_no_id')]]['dia']; ?></div></td>
            <td width="50" align="right"><div style="word-wrap:break-word; width:50px;"><? echo $row[csf('roll_no')]; $tot_roll+=$row[csf('roll_no')]; ?>&nbsp;</div></td>
            <td width="70" align="right"><div style="word-wrap:break-word; width:70px;"><? echo $row[csf('outqntyshift')]; $tot_qty+=$row[csf('outqntyshift')]; ?>&nbsp;</div></td>
        </tr>
    <?
		$i++;
	}
	?>
        	<tr> 
                <td align="right" colspan="17" ><strong>Total:</strong></td>
                <td align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</td>
                <td align="right" ><? echo number_format($tot_qty,2,'.',''); ?>&nbsp;</td>
			</tr>
            <tr>
                <td colspan="2" align="left"><b>Remarks: </b></td>
                <td colspan="17" ><? //echo number_to_words($tot_qty); ?>&nbsp;</td>
            </tr>
		</table>
        <br>
		 <?
            echo signature_table(44, $company, "1340px");
         ?>
	</div>
	</div>
	<?
    exit();
}
?>