<?php
session_start();
include('../../../../includes/common.php');

extract($_REQUEST);
 
$pc_time= add_time(date("H:i:s",time()),360);  
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start

if ($action=="load_drop_down_buyer")
{
	$data=str_replace("'", "",$data);
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "load_drop_down( 'requires/order_wise_finish_fabrics_requirement_controller', this.value, 'load_drop_down_season', 'season_td' );" );     	 
	exit();
}


if ($action=="load_drop_down_season")
{
	$data=str_replace("'", "",$data);
	if($data)$buyerCon=" and buyer_id=$data";
	echo create_drop_down( "cbo_season", 100, "select id,season_name  from lib_buyer_season where status_active=1 and is_deleted=0 $buyerCon order by season_name","id,season_name", 1, "-- All Season --", $selected, "" );     	 
	exit();
}




if($action=="order_no_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
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
				id += selected_id[i] + '*';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#txt_order_no').val( name );
			$('#txt_order_id').val( id );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:810px;">
	            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
	                    <th>Shipment Date</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
	                    <input type="hidden" name="txt_order_no" id="txt_order_no" value="" />
	                    <input type="hidden" name="txt_order_id" id="txt_order_id" value="" />
	                    <input type="hidden" name="cbo_year" id="cbo_year" value="<? echo $cbo_year;?>" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year').value+'**'+'<? echo $job_no; ?>', 'create_order_no_search_list_view', 'search_div', 'order_wise_finish_fabrics_requirement_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
	                    	</td>
	                    </tr>
	                    <tr>
	                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:5px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$job_no=$data[7];
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
	$search_by_cond = "";
	if(trim($data[3]) !="")
	{
		$search_string=trim($data[3]);
	}

	if($search_by==1 && $search_string !="") 
		$search_by_cond=" and b.po_number LIKE '%$search_string%'"; 
	else if($search_by==2 && $search_string !="") 
		$search_by_cond=" and a.style_ref_no='$search_string'"; 	
	else if($search_by==3 && $search_string !="") 
		$search_by_cond=" and a.job_no_prefix_num=$search_string";
	else '';
		
	$start_date =$data[4];
	$end_date =$data[5];	
	$year =$data[6];
	if($year != 0)	
	{
		$year_cond = "and to_char(a.insert_date,'YYYY')=$year";
	}
	
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
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $year_cond and a.company_name=$company_id  $search_by_cond $buyer_id_cond $job_no_cond $date_cond order by b.id, b.pub_shipment_date";
	//echo $sql; die;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		$(function(){
			load_drop_down( 'order_wise_finish_fabrics_requirement_controller',<? echo $companyID;?>, 'load_drop_down_buyer', 'buyer_td' );
		});
		
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
				id += selected_id[i] + '*';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#txt_booking_no').val( name );
			$('#txt_booking_id').val( id );
			//$('#txt_order_id').val( name );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
             <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                        <thead>                	 
                        	<th width="150">Company Name</th>
                            <th width="140">Buyer Name</th>
                            <th width="80">Booking No</th>
                            <th>Booking Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </thead>
                        <tr>
                            <td>
                                <input type="hidden" id="txt_booking_no">
                                <input type="hidden" id="txt_booking_id">
                                <input type="hidden" id="txt_order_id">
                                <input type="hidden" id="job_no">
                                <input type="hidden" id="cbo_year" value="<? echo $cbo_year;?>">
                                <? 
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $companyID, "load_drop_down( 'order_wise_finish_fabrics_requirement_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --" ); ?></td>
                            <td>
                                <input type="text" id="booking_no_prefix_num" name="booking_no_prefix_num" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('booking_no_prefix_num').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'order_wise_finish_fabrics_requirement_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                             </td>
                        </tr>
                        <tr>
                            <td colspan="5"  align="center">
                                <? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                    </table>
        	<div style="margin-top:5px" id="search_div"></div>    
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[4]!=0) $job_no=" and a.job_no='$data[4]'"; else $job_no='';
	if ($data[5]!=0) $booking_no=" and a.booking_no_prefix_num='$data[5]'"; else $booking_no='';
	if ($data[6]!=0) $cbo_year_con=" and to_char(b.insert_date,'YYYY')=$data[6]"; else $cbo_year_con='';

	
	
	
	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	$po_array=array();
	$sql_po= sql_select("select b.booking_no,c.po_number from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id $company $buyer $booking_no $booking_date and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	foreach($sql_po as $row)
	{
		$po_no_array[$row[csf("booking_no")]][$row[csf("po_number")]]=$row[csf("po_number")];
	}
	
	foreach($po_no_array as $booking_number=>$po_no_arr){
		$po_array[$booking_number]=implode(',',$po_no_arr);
	}
	
	//print_r($po_array);die; 
	 
	 
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);
	
	$sql= "SELECT a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no $company $buyer $booking_no $booking_date $cbo_year_con and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 order by a.id Desc";
	// echo $sql; die;
		
	
	echo  create_list_view("tbl_list_search", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Approved,Is-Ready", "100,80,70,100,80,220,110,60,60","1020","230",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,company_id,buyer_id,0,booking_no,item_category,fabric_source,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,booking_no,item_category,fabric_source,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','',1);
   exit(); 
}

if ($action=="report_generate")
{	
	// ======================= GETTING SEARCH PARAMETER ===========================
	extract($_REQUEST);
	$company_id		= str_replace("'","",$cbo_company_name);
	$buyer_id 		= str_replace("'","",$cbo_buyer_name);
	$booking_no 	= str_replace("'","",$txt_booking_no);
	$booking_id 	= str_replace("'","",$txt_booking_id);
	$order_id 		= str_replace("'","",$txt_order_id);
	$txt_order_no   = str_replace("'","",$txt_order_no);
	$cbo_ship_status= str_replace("'","",$cbo_ship_status);
	$txt_date_from 	= str_replace("'","",trim($txt_date_from));
	$txt_date_to 	= str_replace("'","",trim($txt_date_to));	
	$cbo_year 		= str_replace("'","",$cbo_year);
	$cbo_season 	= str_replace("'","",$cbo_season);
	
	
	// ====================== create sql condition ===============================
	$booking_no = str_replace("*", "','", $booking_no);
	$booking_id = str_replace("*", ",", $booking_id);
	$order_no 	= str_replace("*", "','", $txt_order_no);
	$order_id 	= str_replace("*", ",", $order_id);


	$sql_cond  = "";
	$sql_cond .= ($company_id 	!="" && $company_id !=0) ? " and a.company_name =$company_id" : "";
	$sql_cond .= ($buyer_id 	!="" && $buyer_id 	!=0) ? " and a.buyer_name=$buyer_id" : "";
	
	if(count(explode('*',$txt_booking_no))==1){
		$sql_cond .= ($booking_no !='') ? " and c.booking_no like('%$booking_no%')" : "";
	}
	else{
		$sql_cond .= ($booking_id 	== "" && $booking_no !='') ? " and c.booking_no in('$booking_no')" : "";
		$sql_cond .= ($booking_id 	!="" && $booking_id !=0) ? " and c.id in($booking_id)" : "";
	}
	
	
	if(count(explode('*',$txt_order_no))==1){
		$sql_cond .= ($order_no !='') ? " and b.po_number like('%$order_no%')" : "";
	}
	else{
		$sql_cond .= ($order_id =="" && $order_no !='') ? " and b.po_number in('$order_no')" : "";
		$sql_cond .= ($order_id !="" && $order_id 	!=0) ? " and b.id in($order_id)" : "";
	}
	
	
	
	$sql_cond .= ($txt_date_from!="" && $txt_date_to!="")? " and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'" : "";
	
	
	  //echo $sql_cond;die;
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $sql_cond.=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $sql_cond.=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	
	
	
	if(trim($cbo_season)!=0) 
	{
		$sql_cond.=" and a.season_buyer_wise=$cbo_season";
	}
	
	
	
	// ============================ library  ===========================

	$buyer_library	= return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$company_lib	= return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$color_lib		= return_library_array( "select id, color_name from lib_color where  status_active=1 and is_deleted=0", "id", "color_name"  );
	
	
	
	$season_name_lib = return_library_array( "select id,season_name  from lib_buyer_season where status_active=1 and is_deleted=0 order by season_name", "id", "season_name"  );
	
	
	

	// ========================================== MAIN QUERY =====================================
	$sql = "SELECT a.season_buyer_wise,d.pre_cost_fabric_cost_dtls_id,a.company_name, a.job_no_prefix_num, a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,b.pub_shipment_date, c.booking_no,c.booking_no_prefix_num as booking_num,c.booking_date,c.delivery_date,c.is_short,d.gmts_color_id,d.fabric_color_id,d.fin_fab_qnty,d.construction,d.copmposition 
		from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d
		WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no and c.booking_no=d.booking_no and b.id=d.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 AND d.status_active = 1 and d.po_break_down_id is not null and d.gmts_color_id !=0 and d.booking_type=1 and d.fin_fab_qnty>0 $order_cond $sql_cond ORDER BY c.booking_no_prefix_num,a.company_name";

		//echo $sql;
		
	$sql_res = sql_select($sql);	
	$main_array = array();
	$po_id_array = array();
	foreach ($sql_res as  $row) 
	{
		$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		
		
		$pre_cost_fabric_cost_dtls_id_array[$row[csf('pre_cost_fabric_cost_dtls_id')]] = $row[csf('pre_cost_fabric_cost_dtls_id')];

		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['pre_cost_fabric_cost_dtls_id'] = $row[csf('pre_cost_fabric_cost_dtls_id')];
		
		
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['company_name'] 		= $row[csf('company_name')];
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['buyer_name'] 		= $row[csf('buyer_name')];
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['job_no_prefix_num']	= $row[csf('job_no_prefix_num')];
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['style_ref_no'] 		= $row[csf('style_ref_no')];
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['po_number'] = $row[csf('po_number')];
		
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['booking_date'] 		= $row[csf('booking_date')];
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['pub_shipment_date'] = $row[csf('pub_shipment_date')];
		
		
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['delivery_date'] 		= $row[csf('delivery_date')];
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['copmposition'] 		= $row[csf('copmposition')];
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['construction'] 		= $row[csf('construction')];
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['season_buyer_wise'] = $row[csf('season_buyer_wise')];
		
		$main_array[$row[csf('booking_num')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]]['is_short'] = $row[csf('is_short')];
		
	}
	$poIds = implode(",", $po_id_array);

	// ============================== FOR ORDER QUANTITY =======================
	$sql_order_qnty = "SELECT b.id as po_id,sum(b.po_quantity) as po_quantity 
	FROM  wo_po_details_master a, wo_po_break_down b
	WHERE a.job_no=b.job_no_mst  and a.status_active=1 and b.status_active=1 and b.id in($poIds)
	GROUP BY b.id";
	$sql_order_qnty_res = sql_select($sql_order_qnty);
	$po_qnty_array 		= array();
	foreach ($sql_order_qnty_res as $val) 
	{
		$po_qnty_array[$val[csf('po_id')]] = $val[csf('po_quantity')];
	}

	
/*	$pre_cost_sql="select a.id  FROM  wo_pre_cost_fabric_cost_dtls a WHERE a.color_size_sensitive=3 and  a.id in(".implode(',',$pre_cost_fabric_cost_dtls_id_array).")";
	$pre_cost_data = sql_select($pre_cost_sql);
	foreach ($pre_cost_data as $row) 
	{
		$is_color_size_sensitive[$row[csf('id')]] = 1;
	}
*/	
	$pre_cost_sql="select a.id,a.color_size_sensitive, a.construction,a.composition  FROM  wo_pre_cost_fabric_cost_dtls a WHERE a.id in(".implode(',',$pre_cost_fabric_cost_dtls_id_array).")";
	$pre_cost_data = sql_select($pre_cost_sql);
	foreach ($pre_cost_data as $row) 
	{
		if($row[csf('color_size_sensitive')]==3){$is_color_size_sensitive[$row[csf('id')]] = 1;}
		$con_com_arr[$row[csf('id')]]['construction'] = $row[csf('construction')];
		$con_com_arr[$row[csf('id')]]['composition'] = $row[csf('composition')];
	}


	
	
	// ======================================== FOR GREY. FAB. QNTY =============================
	$grey_qnty_sql="SELECT po_break_down_id as po_id, fabric_color_id as color_id, construction,copmposition, sum(grey_fab_qnty) as grey_qnty, sum(fin_fab_qnty) as fin_fab_qnty
        FROM wo_booking_dtls 
   		WHERE po_break_down_id in($poIds) and status_active=1
   		GROUP by po_break_down_id, fabric_color_id, construction, copmposition";

		$grey_qnty_sql_res = sql_select($grey_qnty_sql);
		$grey_qty_arr=array();
		foreach($grey_qnty_sql_res as $row)
		{
			$grey_qty_arr[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('construction')]][$row[csf('copmposition')]] += $row[csf('grey_qnty')];
			$fin_fab_qnty_arr[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('construction')]][$row[csf('copmposition')]] += $row[csf('fin_fab_qnty')];
		}

	// ======================================== FOR ROWSPAN ==================================
	$rowSpanArray = array();
	foreach ($main_array as $booking_id => $booking_data) 
	{
		foreach ($booking_data as $po_id => $po_data) 
		{
			foreach ($po_data as $gmts_color_id => $color_data) 
			{
				foreach ($color_data as $cons_id => $cons_data) 
				{
					foreach ($cons_data as $comp_id => $row) 
					{
						$rowSpanArray[$booking_id][$po_id]++;
					}
				}
			}
		}
	}
	// print_r($main_array);
	// die();
	ob_start();	
	?>
	
   <div style="width:1280px; margin-top:5px;">
        <div style="text-align: center;font-size: 18px;color: red;"><? if(count($sql_res) == 0){?>Data not available!<? die();}?></div>
        
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="14" align="center" style="font-size:20px; font-weight:bold;"><? echo $company_lib[$company_id]; ?></td></tr>
            <tr>
                <td colspan="14" align="center" style="font-size:14px;">Order wise Finish Fabrics Requirement</td>
            </tr>
            <? if($txt_date_from!='' && $txt_date_to!=''){?>
            <tr>
             	<td colspan="14" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?> 
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <? }?>
        </table>
        <table width="1260" align="left" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"> 
        	
            <thead>
                <tr style="font-size:12px"> 
                    <th width="30">Sl</th>	
                    <th width="80">Buyer</th>                   
                    <th width="80">Booking Date</th>
                    <th width="60">Booking No</th>
					<th width="50">Job No</th>
					<th width="100">Style</th>                    
                    <th width="100">Order No</th>
                    <th width="80">Order Qnty</th>
                    <th width="80">Pub Shipment Date</th>	
                    <th width="80">Fab. Req. Date</th>					
					<th width="280">Fab. Description</th>	
					<th width="110">Fab. Color</th>	
                    <th>Req. Qnty</th>
                </tr>                            	
            </thead>
        </table>
        <div style="width:1280px; overflow-y:scroll; max-height:300px;" id="scroll_body" > 
            <table align="left" width="1260"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <tbody>
					<?
					$i=1;$ii=1;
					$gr_order_qnty = 0;
					$gr_color_qnty = 0;
					foreach ($main_array as $booking_no => $booking_data) 
					{
						$sub_order_qnty = 0;
						$sub_color_qnty = 0;
						foreach ($booking_data as $po_id => $po_data) 
						{	$r=0;
							foreach ($po_data as $gmts_color_id => $color_data) 
							{
								foreach ($color_data as $construction => $construction_data) 
								{
									foreach ($construction_data as $copmposition => $row) 
									{	$bgcolor=($ii%2==0)?"#E9F3FF":"#FFFFFF";
										
										$color=($is_color_size_sensitive[$row['pre_cost_fabric_cost_dtls_id']]==1)?"#F00":"#000";
										$color2=($row['is_short']==1)?"#F00":"#000";
										
										?>
						                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $ii; ?>" style="font-size:12px; cursor:pointer;"">
						                	<? if($r==0){?>
						                    <td style="word-break: break-all;word-wrap: break-word;" rowspan="<? echo $rowSpanArray[$booking_no][$po_id];?>" valign="top" width="30"><? echo $i;?></td>	
						                    <td style="word-break: break-all;word-wrap: break-word;" rowspan="<? echo $rowSpanArray[$booking_no][$po_id];?>" valign="top" width="80"><p>
											<? echo $buyer_library[$row['buyer_name']]."<br>".$season_name_lib[$row['season_buyer_wise']];
											?></p></td>
						                    <td style="word-break: break-all;word-wrap: break-word;" rowspan="<? echo $rowSpanArray[$booking_no][$po_id];?>" valign="top" width="80" align="center"><p><? echo change_date_format($row['booking_date']);?></p></td>	
						                    <td style="word-break: break-all;word-wrap: break-word; color:<? echo $color2;?>" rowspan="<? echo $rowSpanArray[$booking_no][$po_id];?>" valign="top" width="60" ><p><? echo $booking_no;?></p></td>	
											<td style="word-break: break-all;word-wrap: break-word;" rowspan="<? echo $rowSpanArray[$booking_no][$po_id];?>" valign="top" width="50"><p><? echo $row['job_no_prefix_num'];?></p></td>
						                    <td rowspan="<? echo $rowSpanArray[$booking_no][$po_id];?>" valign="top" width="100"><div style=" width:100px; word-break: break-all;word-wrap: break-word; overflow-wrap: break-word;"><? echo $row['style_ref_no'];?></div></td>
						                    <td rowspan="<? echo $rowSpanArray[$booking_no][$po_id];?>" valign="top" width="100"  align="left"><div style=" width:100px; word-break: break-all;word-wrap: break-word; overflow-wrap: break-word;"><? echo $row['po_number'];?></div></td>	
						                    <td style="word-break: break-all;word-wrap: break-word;" rowspan="<? echo $rowSpanArray[$booking_no][$po_id];?>" valign="top" width="80" align="right"><? echo number_format($po_qnty_array[$po_id],0);?></td>
						                    <td style="word-break: break-all;word-wrap: break-word;" rowspan="<? echo $rowSpanArray[$booking_no][$po_id];?>" valign="top" width="80" align="center"><? echo change_date_format($row['pub_shipment_date']);?></td>
											
                                            <td style="word-break: break-all;word-wrap: break-word;" rowspan="<? echo $rowSpanArray[$booking_no][$po_id];?>" valign="top" width="80" align="center"><? echo change_date_format($row['delivery_date']);?></td>
											<? 
						                	$sub_order_qnty += $po_qnty_array[$po_id]; 
						                	$gr_order_qnty  += $po_qnty_array[$po_id];$i++;} $r++;?>
											<td style="word-break: break-all;word-wrap: break-word;" width="280" align="left">
											<? 
											if(!empty($construction)){
												echo $construction." ".$copmposition;
											}
											else
											{
												echo $con_com_arr[$row['pre_cost_fabric_cost_dtls_id']]['construction']." ".$con_com_arr[$row['pre_cost_fabric_cost_dtls_id']]['composition'];
											}
											?>
                                            </td>
						                    <td style="word-break: break-all;word-wrap: break-word; color:<? echo $color;?>" width="110" align="center"><? echo $color_lib[$gmts_color_id]; ?></td>
											<td style="word-break: break-all;word-wrap: break-word;"  align="right"><? echo number_format($fin_fab_qnty_arr[$po_id][$gmts_color_id][$construction][$copmposition],2);?></td>
						                </tr>
						                <?				                
						                $sub_color_qnty += $fin_fab_qnty_arr[$po_id][$gmts_color_id][$construction][$copmposition];
						                $gr_color_qnty += $fin_fab_qnty_arr[$po_id][$gmts_color_id][$construction][$copmposition];
										$ii++;
		                			}
		                		}
		                	}
						}
            			?>
            				<tr>
            					<th colspan="7" align="right"><strong>Booking Wise Subtotal </strong></th>
            					<th align="right"><? echo number_format($sub_order_qnty,0); ?></th>
            					<th colspan="4"></th>
            					<th align="right"><? echo number_format($sub_color_qnty,2); ?></th>
            				</tr>
            			<?
					}
                ?>
            
         </tbody>
		</table>
        </div>
        <table align="left" width="1260" border="1"  cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
         	<tfoot>
                <td width="30">&nbsp; </td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>	
                <td width="60">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100"><strong>Grand Total</strong></td>	
                <td width="80" align="right"><strong><? echo number_format($gr_order_qnty,0);?></strong></td>	
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
				<td width="280"></td>
				<td width="110">&nbsp;</td>
				<td align="right"><strong><? echo number_format($gr_color_qnty,2);?></strong></td>
      		</tfoot>
        </table>   
	</div>	
	<?
	$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";
	exit();	
}
?>
