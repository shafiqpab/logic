<?php
session_start();
include('../../../../includes/common.php');

extract($_REQUEST);
 
$pc_time= add_time(date("H:i:s",time()),360);  
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));

$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
//---------------------------------------------------- Start


if ($action=="load_drop_down_buyer")
{
	$data=str_replace("'", "",$data);
	echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "load_drop_down( 'requires/hnm_order_status_report_controller', this.value, 'load_drop_down_season', 'season_td' );" );     	 
	exit();
}


if ($action=="load_drop_down_season")
{
	$data=str_replace("'", "",$data);
	if($data)$buyerCon=" and buyer_id=$data";
	echo create_drop_down( "cbo_season", 100, "select id,season_name  from lib_buyer_season where status_active=1 and is_deleted=0 $buyerCon order by season_name","id,season_name", 1, "-- All Season --", $selected, "" );     	 
	exit();
}





if($action=="job_style_order_no_popup")
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
			
			$('#txt_select_no').val( name );
			$('#txt_select_id').val( id );
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
	                    <input type="hidden" name="txt_select_no" id="txt_select_no" value="" />
	                    <input type="hidden" name="txt_select_id" id="txt_select_id" value="" />
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year').value+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $type; ?>', 'create_job_style_order_no_search_list_view', 'search_div', 'hnm_order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_style_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$job_no=$data[7];
	$type=$data[8];
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
	$listagg=",listagg (DISTINCT b.po_number,', ' on overflow truncate with count)  within group (order by b.po_number) po_number";
	if($type==1 || $type==2){


	$sql=" select a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name,a.buyer_name, a.style_ref_no, b.pub_shipment_date $listagg
		 from wo_po_details_master a,
		      wo_po_break_down b
		 where 
		     a.job_no=b.job_no_mst and 
			 a.status_active=1 and 
			 a.is_deleted=0 and 
			 b.status_active=1 and 
			 b.is_deleted=0  
			 $year_cond and 
			 a.company_name=$company_id $search_by_cond $buyer_id_cond $job_no_cond $date_cond
		  group  by a.id, a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name,a.buyer_name, a.style_ref_no , b.pub_shipment_date
		  order by a.id, b.pub_shipment_date";
	}else{
		$sql= "select a.id,b.id as po_id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date 
		from wo_po_details_master a,
		     wo_po_break_down b 
		where 
		    a.job_no=b.job_no_mst and 
			a.status_active=1 and 
			a.is_deleted=0 and 
			b.status_active=1 and 
			b.is_deleted=0 
			$year_cond and 
			a.company_name=$company_id  $search_by_cond $buyer_id_cond $job_no_cond $date_cond 
		order by a.id, b.pub_shipment_date";
	}
	//   echo $sql; die;
		if($type==1){
			echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,job_no_prefix_num","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
		}elseif($type==2){
			echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,style_ref_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
		}else{
			echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "po_id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
		}

   exit(); 
}


if ($action=="report_generate")
{	
	// ======================= GETTING SEARCH PARAMETER ===========================
	extract($_REQUEST);
	$company_id		= str_replace("'","",$cbo_company_name);
	$buyer_id 		= str_replace("'","",$cbo_buyer_name);
	$job_no 	    = str_replace("'","",$txt_job_no);
	$job_id 	= str_replace("'","",$txt_job_id);
	$style_no 	    = str_replace("'","",$txt_style_no);
	$style_id 	= str_replace("'","",$txt_style_id);
	$order_id 		= str_replace("'","",$txt_order_id);
	$txt_order_no   = str_replace("'","",$txt_order_no);
	$cbo_ship_status= str_replace("'","",$cbo_ship_status);
	$txt_date_from 	= str_replace("'","",trim($txt_date_from));
	$txt_date_to 	= str_replace("'","",trim($txt_date_to));	
	$cbo_year 		= str_replace("'","",$cbo_year);
	$cbo_season 	= str_replace("'","",$cbo_season);
	$order_status 	= str_replace("'","",$cbo_order_status);
	$cbo_week 	= str_replace("'","",$cbo_week);

	

	// =======================================================================
	$job_no = str_replace("*", "','", $job_no);
	$job_id = str_replace("*", ",", $job_id);
	$style_no = str_replace("*", "','", $style_no);
	$style_id = str_replace("*", ",", $style_id);
	$order_no 	= str_replace("*", "','", $txt_order_no);
	$order_id 	= str_replace("*", ",", $order_id);
	$date_cond=" and to_char(insert_date,'YYYY')=$cbo_year";
	//======================================================================
	$sql_cond  = "";
	$sql_cond .= (trim($cbo_season)!=0) ? " and a.season_buyer_wise=$cbo_season" : "";
	$sql_cond .= ($company_id 	!="" && $company_id !=0) ? " and a.company_name =$company_id" : "";
	$sql_cond .= ($buyer_id 	!="" && $buyer_id 	!=0) ? " and a.buyer_name=$buyer_id" : "";	
	$sql_cond .= ($style_id !="" && $style_id 	!=0) ? " and a.id in($style_id)" : "";
	$sql_cond .= ($style_id =="" && $style_no 	!="") ? " and style_ref_no like('%$style_no%')" : "";
	$sql_cond .= ($order_status !="" && $order_status 	!=0) ? " and b.is_confirmed =$order_status" : "";


	if(count(explode('*',$txt_order_no))==1){
		$sql_cond .= ($order_no !='') ? " and b.po_number like('%$order_no%')" : "";
	}
	else{
		$sql_cond .= ($order_id =="" && $order_no !='') ? " and b.po_number in('$order_no')" : "";
		$sql_cond .= ($order_id !="" && $order_id 	!=0) ? " and b.id in($order_id)" : "";
	}
	
	
	$sql_cond .= ($job_id !="" && $job_no !="") ? " and a.id in($job_id)" : "";
	$sql_cond .= ($order_id !="" && $order_no 	!="") ? " and b.id in($order_id)" : "";
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0){ $sql_cond.=" and YEAR(a.insert_date)=$cbo_year";}
		else if($db_type==2){ $sql_cond.=" and to_char(a.insert_date,'YYYY')=$cbo_year";};
	}


		if($cbo_season ==0 && $job_id=="" && $style_id=="" && $order_id=="" && $cbo_week  !=0 ){
			$from_date=return_field_value( "from_date", "lib_hnm_calendar ","week=$cbo_week $date_cond");
			$to_date=return_field_value( "to_date", "lib_hnm_calendar ","week=$cbo_week $date_cond");
			if($txt_date_from=="" && $txt_date_to==""){
				$txt_date_from=date("d-M-Y",strtotime($from_date));
				$txt_date_to=date("d-M-Y",strtotime($to_date));
			}
		}

	

		//=============START================sub query=============================================================
		   $cond="";
			if($job_id=="" && $job_no !="" ){
				$cond.=" and a.job_no_prefix_num=$job_no";
			}
			if($job_id!=""){
				$cond.=" and a.id in($job_id)";
			}
			if($style_id=="" && $style_no !=""){
				$cond.=" and a.style_ref_no like('%$style_no%')";
			}
			if($style_id!=""){
				$cond.=" and a.id in($style_id)";
			}
		
			if($order_id=="" && $order_no !=""){
				$cond.=" and b.po_number like('%$order_no%')";
			}
			if($order_id!=""){
				$cond.=" and b.id in($order_id)";
			}
			if($cbo_season!=0){
				$cond.=" and a.season_buyer_wise=$cbo_season";
			}

			if($txt_date_from!="" && $txt_date_to!=""){
				$cond.=" and c.country_ship_date between '$txt_date_from' and '$txt_date_to'";
			}
			

			$all_data_arr=sql_select(" SELECT a.id as job_id,country_ship_date as  ship_date,c.po_break_down_id as po_ids 
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			WHERE a.job_no=b.job_no_mst and 
				b.id=c.po_break_down_id and 
				a.status_active=1 and 
				b.status_active=1 and 
				c.status_active=1 and 
				a.company_name =$company_id $sql_cond $cond 
			GROUP BY a.id,country_ship_date,c.po_break_down_id 
			order by ship_date");

			foreach($all_data_arr as $row){
				$s_date=date("Y-m-d",strtotime($row[csf('ship_date')]));
				$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
				$ship_date_arr[$s_date]=$s_date;
				$poididarr[$row[csf('po_ids')]]=$row[csf('po_ids')];
			}
	
			$con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (7) and ENTRY_FORM=2001");	
			oci_commit($con);
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2001, 7, $poididarr, $empty_arr);//PO ID
	//=============END================sub query================================================================
		

		if($txt_date_from=="" && $txt_date_to==""){
			$mindate=min($ship_date_arr);
			$maxdate=max($ship_date_arr);
			$txt_date_from=date("d-M-Y",strtotime($mindate));
			$txt_date_to=date("d-M-Y",strtotime($maxdate));
		}
		
	
  	// $poididarr=array_unique(explode(",",$poIds));

	



	//==============START=================Week Query===================================================
	$week_cond .= ($cbo_week !="" && $cbo_week 	!=0) ? " and week=$cbo_week" : "";	
	// $week_cond .= ($txt_date_from!="" && $txt_date_to!="")? " and from_date between '$txt_date_from' and '$txt_date_to'" : "";
	$sql_week_header=sql_select("select from_date,to_date,week from lib_hnm_calendar where status_active=1 and year=$cbo_year $week_cond   order by from_date,week");
	

	
		$from_date="";$to_date="";
		foreach ($sql_week_header as $row)
		{
			$from_date=date("Y-m-d",strtotime($row[csf('from_date')]));
			$to_date=date("Y-m-d",strtotime($row[csf('to_date')]));
		

			// if($txtDateFrom <= $from_date ){
			// 	if($txtDateTo <= $from_date){
				$month = date('m', strtotime($row[csf('from_date')]));
				
				
				$all_week_date_arr[$month][$row[csf('week')]]['from_date']=$from_date;
				$all_week_date_arr[$month][$row[csf('week')]]['to_date']=$to_date;
				

				// $month_name = date('F', strtotime($row[csf('from_date')]));
				// $month_arr[$month]=$month_name;
	
				$from_date1=date("d-M-Y",strtotime($row[csf('from_date')]));
				$to_date1=date("d-M-Y",strtotime($row[csf('to_date')]));
			//}}

			$week_date_arr[$from_date][$to_date]=$row[csf('week')];
			
			
		}
	//===================END============Week Query===================================================
		


	// ====================== create sql condition ===============================



	
	// ============================ library  ===========================


	$company_lib	= return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$color_lib		= return_library_array( "select id, color_name from lib_color where  status_active=1 and is_deleted=0", "id", "color_name"  );	
	$country_code_lib = return_library_array( "select id,ultimate_country_code  from lib_country_loc_mapping where status_active=1 and is_deleted=0", "id", "ultimate_country_code"  );

	if($txt_date_from!="" && $txt_date_to!=""){
		$sql_cond.=" and c.country_ship_date between '$txt_date_from' and '$txt_date_to'";
	}


	
	
	// ========================================== MAIN QUERY =====================================
		$sql ="SELECT a.season_buyer_wise,a.company_name, a.job_no_prefix_num,a.job_no,a.product_code,a.product_dept, a.set_smv,a.style_ref_no,b.id as po_id,b.po_number,b.pub_shipment_date,b.po_received_date,c.country_id,c.order_quantity,c.color_number_id,c.code_id,c.country_ship_date 
		   from 
				wo_po_details_master a,
				wo_po_break_down b,
				wo_po_color_size_breakdown c, 
				gbl_temp_engine d
		   WHERE 
				a.job_no=b.job_no_mst  and 
				b.id=c.po_break_down_id and
				b.id=d.ref_val and
				c.po_break_down_id=d.ref_val and 
				d.user_id =$user_id and 
				d.ref_from in (7) and 
				d.entry_form=2001 and
				
				a.status_active=1 and
				b.status_active=1 and
				c.status_active=1 
		    $sql_cond 
		   GROUP BY a.season_buyer_wise,a.company_name,a.job_no,a.product_code,a.product_dept, a.job_no_prefix_num, a.set_smv,a.style_ref_no,b.id,b.po_number,b.pub_shipment_date,b.po_received_date,c.country_id,c.order_quantity,c.color_number_id,c.code_id,c.country_ship_date 
		   order by c.country_ship_date";

	//echo $sql;

	
	$sql_res = sql_select($sql);	

	
	foreach ($sql_res as  $row) 
	{

		
		$string=$row[csf('po_id')]."*".$row[csf('country_id')]."*".$row[csf('color_number_id')];		
		$main_data_arr[$row[csf('job_no')]][$string]['prod_number']=$row[csf('product_code')];
		$main_data_arr[$row[csf('job_no')]][$string]['prod_dept']=$row[csf('product_dept')];
		$main_data_arr[$row[csf('job_no')]][$string]['style_ref_no']=$row[csf('style_ref_no')];
		$main_data_arr[$row[csf('job_no')]][$string]['po_number']=$row[csf('po_number')];
		$main_data_arr[$row[csf('job_no')]][$string]['set_smv']=$row[csf('set_smv')];
		$main_data_arr[$row[csf('job_no')]][$string]['code_id']=$row[csf('code_id')];
		$main_data_arr[$row[csf('job_no')]][$string]['country_ship_date']=$row[csf('country_ship_date')];
		$main_data_arr[$row[csf('job_no')]][$string]['po_recv_date']=$row[csf('po_received_date')];		
		$main_data_arr[$row[csf('job_no')]][$string]['order_qnty']+=$row[csf('order_quantity')];

		$month = date('m', strtotime($row[csf('country_ship_date')]));
		$month2 = date('m', strtotime($row[csf('po_received_date')]));
		
	
		$ship_date=date("Y-m-d",strtotime($row[csf('country_ship_date')]));
		$po_rcv_date=date("Y-m-d",strtotime($row[csf('po_received_date')]));

		$ship_date_arr[$ship_date]=$ship_date;
			foreach($all_week_date_arr as $mId=>$month_data){
				foreach($month_data as $wId=>$week_data){

				
					if( ($week_data['from_date'] <= $ship_date) &&  ($ship_date <= $week_data['to_date'])){						

						$week_wise_qnty[$row[csf('job_no')]][$string][$month][$wId]['order_qnty']+=$row[csf('order_quantity')];
						$main_data_arr[$row[csf('job_no')]][$string]['tod_week']=$wId;
						$month_name = date('F', strtotime($week_data['from_date']));
						$month_arr[$month]=$month_name;
						$week_check_head[$month][$wId]=$week_data['from_date'];
					
				    	}

						if(($week_data['from_date'] <= $po_rcv_date) &&  ($po_rcv_date <= $week_data['to_date']) ){
							// echo $weekData['from_date']."===>".$weekData['to_date']."==>".$wid."==>".$po_rcv_date."<br>";
						$main_data_arr[$row[csf('job_no')]][$string]['opd_week']=$wId;

						}

				
			  }
				
			}
			
	}
	
	// echo "<pre>";
	// print_r($mains_data_arr);
	
	$no_of_week="";
	foreach($month_arr as $key_m=>$m_name){
		foreach($week_check_head[$key_m] as $week){

			$no_of_week+=1;
		}}
		$week_width=$no_of_week*80+1320;
	ob_start();	
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (7) and ENTRY_FORM=2001");	
	oci_commit($con);
			
	?>
	
   <div style="width:<?=$week_width;?>px; margin-top:5px;">
        <div style="text-align: center;font-size: 18px;color: red;"><? if(count($sql_res) == 0){?>Data not available!<? die();}?></div>
        
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="14" align="center" style="font-size:20px; font-weight:bold;"><? echo $company_lib[$company_id]; ?></td></tr>
            <tr>
                <td colspan="14" align="center" style="font-size:14px;">HnM Order Status Report</td>
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
        <table width="<?=$week_width;?>" align="left" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"> 
        	
            <thead>
                <tr style="font-size:12px"> 
                    <th width="30" rowspan="3">Sl</th>	
                    <th width="80" rowspan="3">Job No</th>                   
                    <th width="80" rowspan="3">STYLE</th>
                    <th width="60" rowspan="3">PRODUCT NUMBER</th>
					<th width="50" rowspan="3">DEPT.</th>
					<th width="100" rowspan="3">Order No</th>                    
                    <th width="150" rowspan="3">COLOR WAY</th>
                    <th width="80" rowspan="3">CHANNEL</th>
                    <th width="80" rowspan="2" colspan="2">OPD</th>	
                    <th width="80" rowspan="2" colspan="2">TOD</th>					
					<th width="70" rowspan="3">FOB/PC/   SET</th>	
					<th width="100" rowspan="3">BUSINESS / PLAN</th>	
					<?
					foreach($month_arr as $m=>$m_name){?>
                    <th width="80"  colspan="<?=count($week_check_head[$m]);?>"><?=$m_name;?></th>
					<?}?>
                </tr>       
				<tr style="font-size:12px"> 
                   
					<?
					foreach($month_arr as $key_m=>$m_name){
						foreach($week_check_head[$key_m] as $week=>$w_date){
						?>
                    <th width="80">WK-<?=$week;?></th>
					<?}}?>
                </tr>  
				<tr style="font-size:12px"> 
                    <th width="80">WK</th>	
                    <th width="80">DATE</th>					
					<th width="80">WK</th>	
                    <th width="80">DATE</th>
					<?
					// date("d-M-y",strtotime($tmp))
					foreach($month_arr as $key_m=>$m_name){
						foreach($week_check_head[$key_m] as $week=>$w_date){
						?>
                    <th width="80"><?=$w_date;?></th>
					<?}}?>
                </tr>                                	
            </thead>
			
        </table>
        <div style="width:<?=$week_width+20;?>px; overflow-y:scroll; max-height:300px;" id="scroll_body" > 
            <table align="left" width="<?=$week_width;?>"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <tbody>
					<?
					$i=1;
					$sub_order_qnty=0;
					foreach ($main_data_arr as $job_no => $order_data){
						foreach ($order_data as $key_id => $row){

								list($order_id,$country_id,$color_id)=explode("*",$key_id);								
							      $bgcolor=($ii%2==0)?"#E9F3FF":"#FFFFFF";
										?>
									<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px; cursor:pointer;">
										<td  valign="top" width="30"><? echo $i;?></td>	
										<td  valign="top" width="80"><p><?=$job_no;?></p></td>
										<td  valign="top" width="80" align="center"><p><?=$row['style_ref_no'];?></p></td>	
										<td  valign="top" width="60" ><p><?=$row['prod_number'];?></p></td>	
										<td  valign="top" width="50"><p><?=$product_dept[$row['prod_dept']];?></p></td>
										<td  valign="top" width="100"><?=$row['po_number'];?></td>
										<td  valign="top" width="150"  align="left"><?=$color_lib[$color_id];?></td>	
										<td  valign="top" width="80" align="left"><?=$country_code_lib[$row['code_id']];?></td>									
										<td  valign="top" width="80" align="center">WK-<?=$row['opd_week'];?></td>
										<td  valign="top" width="80" align="center"><?=$row['po_recv_date'];?></td>
										<td  valign="top" width="80" align="center">WK-<?=$row['tod_week'];?></td>
										<td  valign="top" width="80" align="center"><?=$row['country_ship_date'];?></td>
										<td  width="70" align="right"><?=$row['set_smv'];?></td>
										<td  width="100" align="right"><?=$row['order_qnty'];?></td>
										<?
										foreach($month_arr as $key_m=>$m_name){
											foreach($week_check_head[$key_m] as $week=>$w_date){
											?>
										<td width="80" align="right"><?=$week_wise_qnty[$job_no][$key_id][$key_m][$week]['order_qnty'];?></td>
										<?
										$week_total[$job_no][$key_m][$week]+=$week_wise_qnty[$job_no][$key_id][$key_m][$week]['order_qnty'];
										$week_grand_tot[$key_m][$week]+=$week_wise_qnty[$job_no][$key_id][$key_m][$week]['order_qnty'];
										}}?>
									</tr>
						                <?				                
						                $sub_order_qnty+=$row['order_qnty'];
										$grand_order_qnty+=$row['order_qnty'];
										
										
										$i++;
		                		
						}
            			?>
            				<tr>
            					<td colspan="13" align="right"><strong>Style Wise </strong></td>
            					<td align="right" ><? echo number_format($sub_order_qnty,0); ?></td>
								<?
								foreach($month_arr as $keym=>$m_name){
									foreach($week_check_head[$keym] as $w=>$w_date){
											?>
            					<td width="80" align="right"><?=$week_total[$job_no][$keym][$w];?></td>
								<?}}?>
            				
            				</tr>
            			<?
					}
					?>
							
                
            
         </tbody>
		     <tfoot>
					<td colspan="13" align="right"><strong>Grand Total </strong></td>
					<td align="right" ><? echo number_format($grand_order_qnty,0); ?></td>
					<?
					foreach($month_arr as $keym=>$m_name){
						foreach($week_check_head[$keym] as $w=>$w_date){
								?>
					<td width="80" align="right"><?=$week_grand_tot[$keym][$w];?></td>
					<?}}?>
      			</tfoot>
		</table>
        </div>
       
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

if ($action=="report_generate2")
{	
	// ======================= GETTING SEARCH PARAMETER ===========================
	extract($_REQUEST);
	$company_id		= str_replace("'","",$cbo_company_name);
	$buyer_id 		= str_replace("'","",$cbo_buyer_name);
	$job_no 	    = str_replace("'","",$txt_job_no);
	$job_id 	= str_replace("'","",$txt_job_id);
	$style_no 	    = str_replace("'","",$txt_style_no);
	$style_id 	= str_replace("'","",$txt_style_id);
	$order_id 		= str_replace("'","",$txt_order_id);
	$txt_order_no   = str_replace("'","",$txt_order_no);
	$cbo_ship_status= str_replace("'","",$cbo_ship_status);
	$txt_date_from 	= str_replace("'","",trim($txt_date_from));
	$txt_date_to 	= str_replace("'","",trim($txt_date_to));	
	$cbo_year 		= str_replace("'","",$cbo_year);
	$cbo_season 	= str_replace("'","",$cbo_season);
	$order_status 	= str_replace("'","",$cbo_order_status);
	$cbo_week 	= str_replace("'","",$cbo_week);

	

	// =======================================================================
	$job_no = str_replace("*", "','", $job_no);
	$job_id = str_replace("*", ",", $job_id);
	$style_no = str_replace("*", "','", $style_no);
	$style_id = str_replace("*", ",", $style_id);
	$order_no 	= str_replace("*", "','", $txt_order_no);
	$order_id 	= str_replace("*", ",", $order_id);
	$date_cond=" and to_char(insert_date,'YYYY')=$cbo_year";
	//======================================================================
	$sql_cond  = "";
	$sql_cond .= (trim($cbo_season)!=0) ? " and a.season_buyer_wise=$cbo_season" : "";
	$sql_cond .= ($company_id 	!="" && $company_id !=0) ? " and a.company_name =$company_id" : "";
	$sql_cond .= ($buyer_id 	!="" && $buyer_id 	!=0) ? " and a.buyer_name=$buyer_id" : "";	
	$sql_cond .= ($style_id !="" && $style_id 	!=0) ? " and a.id in($style_id)" : "";
	$sql_cond .= ($style_id =="" && $style_no 	!="") ? " and style_ref_no like('%$style_no%')" : "";
	$sql_cond .= ($order_status !="" && $order_status 	!=0) ? " and b.is_confirmed =$order_status" : "";


	if(count(explode('*',$txt_order_no))==1){
		$sql_cond .= ($order_no !='') ? " and b.po_number like('%$order_no%')" : "";
	}
	else{
		$sql_cond .= ($order_id =="" && $order_no !='') ? " and b.po_number in('$order_no')" : "";
		$sql_cond .= ($order_id !="" && $order_id 	!=0) ? " and b.id in($order_id)" : "";
	}
	
	
	$sql_cond .= ($job_id !="" && $job_no !="") ? " and a.id in($job_id)" : "";
	$sql_cond .= ($order_id !="" && $order_no 	!="") ? " and b.id in($order_id)" : "";
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0){ $sql_cond.=" and YEAR(a.insert_date)=$cbo_year";}
		else if($db_type==2){ $sql_cond.=" and to_char(a.insert_date,'YYYY')=$cbo_year";};
	}


		if($cbo_season ==0 && $job_id=="" && $style_id=="" && $order_id=="" && $cbo_week  !=0 ){
			$from_date=return_field_value( "from_date", "lib_hnm_calendar ","week=$cbo_week $date_cond");
			$to_date=return_field_value( "to_date", "lib_hnm_calendar ","week=$cbo_week $date_cond");
			if($txt_date_from=="" && $txt_date_to==""){
				$txt_date_from=date("d-M-Y",strtotime($from_date));
				$txt_date_to=date("d-M-Y",strtotime($to_date));
			}
		}

	

		//=============START================sub query================================================================
		   $cond="";
			if($job_id=="" && $job_no !="" ){
				$cond.=" and a.job_no_prefix_num=$job_no";
			}
			if($job_id!=""){
				$cond.=" and a.id in($job_id)";
			}
			if($style_id=="" && $style_no !=""){
				$cond.=" and a.style_ref_no like('%$style_no%')";
			}
			if($style_id!=""){
				$cond.=" and a.id in($style_id)";
			}
		
			if($order_id=="" && $order_no !=""){
				$cond.=" and b.po_number like('%$order_no%')";
			}
			if($order_id!=""){
				$cond.=" and b.id in($order_id)";
			}
			if($cbo_season!=0){
				$cond.=" and a.season_buyer_wise=$cbo_season";
			}

			if($txt_date_from!="" && $txt_date_to!=""){
				$cond.=" and c.country_ship_date between '$txt_date_from' and '$txt_date_to'";
			}
			

			$all_data_arr=sql_select(" SELECT a.id as job_id,country_ship_date as  ship_date,c.po_break_down_id as po_ids 
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			WHERE a.job_no=b.job_no_mst and 
				b.id=c.po_break_down_id and 
				a.status_active=1 and 
				b.status_active=1 and 
				c.status_active=1 and 
				a.company_name =$company_id $sql_cond $cond 
			GROUP BY a.id,country_ship_date,c.po_break_down_id 
			order by ship_date");

			foreach($all_data_arr as $row){
				$s_date=date("Y-m-d",strtotime($row[csf('ship_date')]));
				$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
				$ship_date_arr[$s_date]=$s_date;
				$poididarr[$row[csf('po_ids')]]=$row[csf('po_ids')];
			}
	
			$con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (7) and ENTRY_FORM=2001");	
			oci_commit($con);
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2001, 7, $poididarr, $empty_arr);//PO ID
	//=============END================sub query================================================================
		

		if($txt_date_from=="" && $txt_date_to==""){
			$mindate=min($ship_date_arr);
			$maxdate=max($ship_date_arr);
			$txt_date_from=date("d-M-Y",strtotime($mindate));
			$txt_date_to=date("d-M-Y",strtotime($maxdate));
		}
		
	
  	// $poididarr=array_unique(explode(",",$poIds));

	



	//==============START=================Week Query===================================================
	$week_cond .= ($cbo_week !="" && $cbo_week 	!=0) ? " and week=$cbo_week" : "";	
	$week_cond .= ($txt_date_from!="" && $txt_date_to!="")? " and from_date between '$txt_date_from' and '$txt_date_to'" : "";
	$sql_week_header=sql_select("select from_date,to_date,week from lib_hnm_calendar where status_active=1 and year=$cbo_year $week_cond   order by from_date,week");
	

	
		$from_date="";$to_date="";
		
		foreach ($sql_week_header as $row_week_header)
		{
			
			$first_cut_date = date('y-M-d ', strtotime('-1 day', strtotime($row_week_header[csf('from_date')])));
			$second_cut_date = date('y-M-d ', strtotime('+3 day', strtotime($row_week_header[csf('from_date')])));
			$from_date=date("y-M-d",strtotime($row_week_header[csf('from_date')]));
		
			$week_check_head[$row_week_header[csf('week')]]['from_date']=$from_date;
			$week_check_head[$row_week_header[csf('week')]]['to_date']=$row_week_header[csf('to_date')];


			$week_check_head[$row_week_header[csf('week')]]['first_cut_date']=$first_cut_date;
			$week_check_head[$row_week_header[csf('week')]]['second_cut_date']=$second_cut_date;

			
		}
	//===================END============Week Query===================================================
		


	// ====================== create sql condition ===============================



	

	
	
	// print_r($week_check_head);


	
	// ============================ library  ===========================


	$company_lib	= return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$color_lib		= return_library_array( "select id, color_name from lib_color where  status_active=1 and is_deleted=0", "id", "color_name"  );	
	$country_code_lib = return_library_array( "select id,ultimate_country_code  from lib_country_loc_mapping where status_active=1 and is_deleted=0", "id", "ultimate_country_code"  );

	if($txt_date_from!="" && $txt_date_to!=""){
		$sql_cond.=" and c.country_ship_date between '$txt_date_from' and '$txt_date_to'";
	}


	
	
	// ========================================== MAIN QUERY =====================================
		$sql ="SELECT a.season_buyer_wise,a.company_name, a.job_no_prefix_num,a.job_no,a.product_code,a.product_dept, a.set_smv,a.style_ref_no,b.id as po_id,b.po_number,b.pub_shipment_date,b.po_received_date,c.country_id,c.order_quantity,c.color_number_id,c.code_id,c.country_ship_date 
		   from 
				wo_po_details_master a,
				wo_po_break_down b,
				wo_po_color_size_breakdown c, 
				gbl_temp_engine d
		   WHERE 
				a.job_no=b.job_no_mst  and 
				b.id=c.po_break_down_id and
				b.id=d.ref_val and
				c.po_break_down_id=d.ref_val and 
				d.user_id =$user_id and 
				d.ref_from in (7) and 
				d.entry_form=2001 and
				
				a.status_active=1 and
				b.status_active=1 and
				c.status_active=1 
		    $sql_cond 
		   GROUP BY a.season_buyer_wise,a.company_name,a.job_no,a.product_code,a.product_dept, a.job_no_prefix_num, a.set_smv,a.style_ref_no,b.id,b.po_number,b.pub_shipment_date,b.po_received_date,c.country_id,c.order_quantity,c.color_number_id,c.code_id,c.country_ship_date 
		   order by c.country_ship_date";

	//echo $sql;

	
	$sql_res = sql_select($sql);	

	
	foreach ($sql_res as  $row) 
	{

		
		$string=$row[csf('po_id')]."*".$row[csf('color_number_id')];		
		$main_data_arr[$row[csf('job_no')]][$string]['prod_number']=$row[csf('product_code')];
		$main_data_arr[$row[csf('job_no')]][$string]['prod_dept']=$row[csf('product_dept')];
		$main_data_arr[$row[csf('job_no')]][$string]['style_ref_no']=$row[csf('style_ref_no')];
		$main_data_arr[$row[csf('job_no')]][$string]['po_number']=$row[csf('po_number')];	
		$main_data_arr[$row[csf('job_no')]][$string]['po_recv_date']=$row[csf('po_received_date')];		
		$main_data_arr[$row[csf('job_no')]][$string]['order_qnty']+=$row[csf('order_quantity')];		
		$ship_date=date("y-M-d",strtotime($row[csf('country_ship_date')]));

			
	
		foreach($week_check_head as $wId=>$week_data){

			


			if(strtotime($week_data['second_cut_date']) == strtotime($ship_date)){

				 $week_wise_qnty[$row[csf('job_no')]][$string][$wId][$week_data['second_cut_date']]['order_qnty']+=$row[csf('order_quantity')];
				}
				
			if(strtotime($week_data['first_cut_date']) == strtotime($ship_date)){
			
			   $week_wise_qnty[$row[csf('job_no')]][$string][$wId][$week_data['first_cut_date']]['order_qnty']+=$row[csf('order_quantity')];
		   }


			

		}
	
		

		
	}
	

	
	// echo "<pre>";
	// print_r($week_wise_qnty);
	// die();
	$no_of_week="";
	
		foreach($week_check_head as $week){

			$no_of_week+=1;
		}
		$week_width=$no_of_week*60*3+1260;
	ob_start();	
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (7) and ENTRY_FORM=2001");	
	oci_commit($con);
	
	?>
	
	<div style="width:<?=$week_width;?>px; margin-top:5px;">
        <div style="text-align: center;font-size: 18px;color: red;"><? if(count($sql_res) == 0){?>Data not available!<? die();}?></div>
        
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="14" align="center" style="font-size:20px; font-weight:bold;"><? echo $company_lib[$company_id]; ?></td></tr>
            <tr>
                <td colspan="14" align="center" style="font-size:14px;">HnM Order Status Report</td>
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
        <table width="<?=$week_width;?>" align="left" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"> 
        	
            <thead>
                <tr style="font-size:12px"> 
                    <th width="30" rowspan="3">Sl</th>	
                    <th width="80" rowspan="3">Job No</th>                   
                    <th width="80" rowspan="3">STYLE</th>
                    <th width="60" rowspan="3">PRODUCT NUMBER</th>
					<th width="50" rowspan="3">DEPT.</th>
					<th width="100" rowspan="3">Order No</th>                    
                    <th width="100" rowspan="3">COLOR</th>
                  			
					
					<th width="80" rowspan="3">QTYS</th>	
					<?
					foreach($week_check_head as $week=>$w_date){?>
                    <th width="60" colspan="3">WK-<?=$week;?></th>
					<?}?>
                </tr>       
				<tr style="font-size:12px"> 
                   
					<?
				
						foreach($week_check_head as $week=>$w_date){
						?>
						<th width="60" colspan="2"><?=date("y-M",strtotime($w_date['from_date']));?></th>
						<th width="60" rowspan="2">Total</th>
					<?}?>
                </tr>  
				<tr style="font-size:12px"> 
                   
					<?
					// date("d-M-y",strtotime($tmp))
					
						foreach($week_check_head as $week=>$w_date){
						?>
                    <th width="60"><?=date("y-M",strtotime($w_date['first_cut_date']));?></th>
					<th width="60"><?=date("y-M",strtotime($w_date['second_cut_date']));?></th>
				
					<?}?>
                </tr>                                	
            </thead>
			
        </table>
        <div style="width:<?=$week_width+20;?>px; overflow-y:scroll; max-height:300px;" id="scroll_body" > 
            <table align="left" width="<?=$week_width;?>"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <tbody>
					<?
					$i=1;
					$sub_order_qnty=0;
					foreach ($main_data_arr as $job_no => $order_data){
						
						foreach ($order_data as $key_id => $row){

								list($order_id,$color_id)=explode("*",$key_id);								
							      $bgcolor=($ii%2==0)?"#E9F3FF":"#FFFFFF";
										?>
									<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px; cursor:pointer;">
										<td  valign="top" width="30"><? echo $i;?></td>	
										<td  valign="top" width="80"><p><?=$job_no;?></p></td>
										<td  valign="top" width="80" ><p><?=$row['style_ref_no'];?></p></td>	
										<td  valign="top" width="60" ><p><?=$row['prod_number'];?></p></td>	
										<td  valign="top" width="50"><p><?=$product_dept[$row['prod_dept']];?></p></td>
										<td  valign="top" width="100"><?=$row['po_number'];?></td>
										<td  valign="top" width="100"><?=$color_lib[$color_id];?></td>
										<td  width="80" align="right"><?=$row['order_qnty'];?></td>
										<?
										$color_total=0;
										foreach($week_check_head as $week=>$w_date){
											$color_total=$week_wise_qnty[$job_no][$key_id][$week][$w_date['first_cut_date']]['order_qnty']+$week_wise_qnty[$job_no][$key_id][$week][$w_date['second_cut_date']]['order_qnty'];
											?>
										<td width="60" align="right"><?=$week_wise_qnty[$job_no][$key_id][$week][$w_date['first_cut_date']]['order_qnty'];;?></td>
										<td width="60" align="right"><?=$week_wise_qnty[$job_no][$key_id][$week][$w_date['second_cut_date']]['order_qnty'];?></td>
										<td width="60" align="right"><?=$color_total;?></td>
										<?
										$week_total[$job_no][$week][$w_date['first_cut_date']]+=$week_wise_qnty[$job_no][$key_id][$week][$w_date['first_cut_date']]['order_qnty'];;
										$week_total[$job_no][$week][$w_date['second_cut_date']]+=$week_wise_qnty[$job_no][$key_id][$week][$w_date['second_cut_date']]['order_qnty'];;
										$week_total[$job_no][$week]['job_total']+=$color_total;
										$week_grand_tot[$week][$w_date['first_cut_date']]+=$week_wise_qnty[$job_no][$key_id][$week][$w_date['first_cut_date']]['order_qnty'];
										$week_grand_tot[$week][$w_date['second_cut_date']]+=$week_wise_qnty[$job_no][$key_id][$week][$w_date['second_cut_date']]['order_qnty'];
										$week_grand_tot[$week]['g_total']+=$color_total;
										}?>
									</tr>
						                <?				                
						                $sub_order_qnty+=$row['order_qnty'];
										$grand_order_qnty+=$row['order_qnty'];
										
										
										$i++;
		                		
						}
            			?>
            				<tr>
            					<td colspan="7" align="right"><strong>Style Wise </strong></td>
            					<td align="right" ><? echo number_format($sub_order_qnty,0); ?></td>
								<?
								
									foreach($week_check_head as $w=>$w_date){
											?>
            					<td width="60" align="right"><?=$week_total[$job_no][$w][$w_date['first_cut_date']];?></td>
								<td width="60" align="right"><?=$week_total[$job_no][$w][$w_date['second_cut_date']];?></td>
								<td width="60" align="right"><?=$week_total[$job_no][$w]['job_total'];?></td>
								<?}?>
            				
            				</tr>
            			<?
					}
					?>
							
                
            
         </tbody>
		     <tfoot>
					<td colspan="7" align="right"><strong>Grand Total </strong></td>
					<td align="right" ><? echo number_format($grand_order_qnty,0); ?></td>
					<?
					
						foreach($week_check_head as $w=>$w_date){
								?>
					<td width="60" align="right"><?=$week_grand_tot[$w][$w_date['first_cut_date']];?></td>
					<td width="60" align="right"><?=$week_grand_tot[$w][$w_date['second_cut_date']];?></td>
					<td width="60" align="right"><?=$week_grand_tot[$w]['g_total'];?></td>
					<?}?>
      			</tfoot>
		</table>
        </div>
       
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
