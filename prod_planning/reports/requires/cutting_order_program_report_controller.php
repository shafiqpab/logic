<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name			= $_SESSION['logic_erp']['user_id'];
$data				= $_REQUEST['data'];
$action				= $_REQUEST['action'];


//--------------------------------------------------------------------------------------------------------------------

if($action=="style_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_style = new Array; var selected_order = new Array;
		
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
		var job_id="";
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");


			if( jQuery.inArray( str[1], selected_id ) == -1 &&  job_id=="") {
			
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_style.push( str[3] );
				selected_order.push( str[4] );
				job_id=str[1];
				toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			}
			else {

				if(job_id==str[1]){
							
					selected_order.push( str[4] );
					
					toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
				
				}else{

					alert("Not Allow Mix Job NO");
					return;

					// for( var i = 0; i < selected_id.length; i++ ) {

					// 	if( selected_id[i] !== str[1] ){ 

					// 		selected_id.splice( i, 1 );
					// 		selected_name.splice( i, 1 );
					// 		selected_style.splice( i, 1 );
					// 		break;
					// 	}
					// }

				}
			 	
			}

			var id = ''; var name = ''; var style = ''; var order='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				style += selected_style[i] + '*';
				order += selected_order[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			style = style.substr( 0, style.length - 1 );
			order = order.substr( 0, order.length - 1 );
		
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			$('#hide_style_no').val( style );
			$('#hide_order_id').val( selected_order );
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
	                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
	                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
	                    <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
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
	                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No",4=>"Int Ref");
								$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>'+'**'+document.getElementById('cbo_year_selection').value, 'create_style_no_search_list_view', 'search_div', 'cutting_order_program_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	<script type="text/javascript">
		$("#cbo_buyer_name").val('<?=$buyer;?>');
	</script>    
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
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($data[7]>0)  $year_cond=" and to_char(a.insert_date,'YYYY')='$data[7]'"; else $year_cond ="";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT a.id,a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.grouping,b.shipment_date,b.po_number,b.id as po_id from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond $year_cond group by a.id,a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,a.insert_date,b.grouping,b.shipment_date,b.po_number,b.id  order by a.job_no desc";
	// echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Int. Ref. No,Style Ref. No,Po No,Shipment Date", "120,100,50,80,100,140,100,100","870","220",0, $sql , "js_set_value", "id,job_no,style_ref_no,po_id","",1,"company_name,buyer_name,0,0,0,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,grouping,style_ref_no,po_number,shipment_date","",'','0,0,0,0,0','',1) ;
   exit(); 
}


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "onchange_buyer()" );     	 
	exit();
}


if ($action=="color_popup")
{
	//echo "select gmts_item_id from wo_po_details_master where job_no='$data'";
	$gmts_item=return_field_value("gmts_item_id","wo_po_details_master","job_no='$data'","gmts_item_id");
	
	// echo create_drop_down( "cbo_gmts_item", 100, $garments_item,"", 1, "-- Select --", $selected, "","",$gmts_item,"" );  
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	//echo $style_id;die;

	?>
   <script>
		function js_set_value( str ) 
		{
			str_dtls=str.split("_");
			
			$('#txt_selected_id').val(str_dtls[0]);
			$('#txt_selected_no').val(str_dtls[1]);
			parent.emailwindow.hide();
		}
    </script>
		
    <?

		$job_arr=explode("*",$job_no);
		foreach($job_arr as $val){

				$jobArr[$val]=$val;
		}

		 $sql = "SELECT b.id,b.color_name from wo_po_color_size_breakdown a, lib_color b where b.id=a.color_number_id  ".where_con_using_array($jobArr,1,'a.job_no_mst')."   group by b.id,b.color_name order by b.id"; 	
		 echo create_list_view("list_view", "Color","160","300","300",0, $sql , "js_set_value", "id,color_name", "", 1, "", $arr, "color_name", "","setFilterGrid('list_view',-1)","0","","");	
	
		 echo "<input type='hidden' id='txt_selected_no' />";
		 echo "<input type='hidden' id='txt_selected_id' />";
	
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			$("#hide_job_no").val(str); 
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
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"> 					
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $sytle_ref_no; ?>', 'create_job_no_search_list_view', 'search_div', 'cutting_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
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
	//$year_id=$data[4];
	//$month_id=$data[5];
	//echo $month_id;
	
	var_dump($data);
	$company_arr		= return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr			= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}

	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	echo $sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and style_ref_no='$data[4]' and $search_field  like '$search_string' $buyer_id_cond $year_cond order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','','') ;
	exit();
} // Job Search end



if ($action == "report_generate" )
{
	// var_dump($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$rept_type			= str_replace( "'", "", $type );
	$company_name		= str_replace( "'", "", $cbo_company_name );
	$buyer_name			= str_replace( "'", "", $cbo_buyer_name );
	$ref_no				= str_replace( "'", "", $txt_ref_no);
	$txt_style_ref_id	= str_replace( "'", "", $txt_style_ref_id);
	$job_no				= str_replace( "'", "", $txt_job_no );
	$job_id_hidden		= str_replace( "'", "", $txt_job_no_hidden);
	$order_no			= str_replace( "'", "", $txt_order_no );
	$hide_order_id		= str_replace( "'", "", $hidden_order_ids );
	$int_ref			= str_replace( "'", "", $int_ref);
	$file_no			= str_replace( "'", "", $file_no );	
	$hidd_gmts_color			= str_replace( "'", "", $hidd_gmts_color );

	$sql_cond	= "";
	$po_cond 	= "";
	
	if($company_name>0) $sql_cond=" AND a.company_id=$company_name";
	if($buyer_name>0) $sql_cond.=" AND c.buyer_name=$buyer_name";
	if($gmts_item>0) $sql_cond.=" AND b.gmt_item_id in($gmts_item)";
	if($gmts_item>0) $gmt_item_cond=" AND item_number_id in($gmts_item)";else $gmt_item_cond="";
	if($order_no>0) $po_id_con=" AND po_break_down_id in($hide_order_id)";else $po_id_con="";
	// if($job_no !="") $sql_cond.=" AND c.job_no='$job_no' ";
	if($job_id_hidden !="") $sql_cond.=" AND c.id in($job_id_hidden)";
	// if($order_no !="") $sql_cond.=" AND d.po_number='$order_no' ";
	if($hide_order_id !="") $sql_cond.=" AND d.id in($hide_order_id) ";
	// if($file_no !="") $sql_cond.=" AND d.file_no='$file_no' ";
	// if($int_ref !="") $sql_cond.=" AND d.grouping='$int_ref' ";
	
	
	
	if($rept_type==1) 
	{
		
	

	
		// ====================================== GETTING SIZE =====================================		
		$size_arr=return_library_array( "SELECT id, size_name from lib_size",'id','size_name');
		$lib_brand=return_library_array( "SELECT id, brand_name from lib_buyer_brand",'id','brand_name');
		$sql_po_data=sql_select( "SELECT id, po_number,grouping,shipment_date  from wo_po_break_down where job_id=$job_id_hidden");
		$color_lib      	= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$buyer_arr			= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
		foreach($sql_po_data as $val){

					$internal_ref_arr[$val[csf('id')]]=$val[csf('grouping')];
					$po_number_arr[$val[csf('id')]]=$val[csf('po_number')];
					$shipment_date_arr[$val[csf('shipment_date')]]=$val[csf('shipment_date')];
					
		}

		$job_details_arr=sql_select( "SELECT * from wo_po_details_master where id=$job_id_hidden");
		$wash_cost_arr=sql_select( "SELECT wash_cost from wo_pre_cost_dtls where job_id=$job_id_hidden");
		
		// echo "<pre>";
		// print_r($po_shipment_date);
		$sql_query=sql_select("SELECT po_break_down_id, country_type, country_id, size_number_id, plan_cut_qnty, order_quantity, country_ship_date, size_order,color_number_id,item_number_id 	from wo_po_color_size_breakdown where  status_active=1 and is_deleted=0 and job_id in($job_id_hidden) and po_break_down_id in ($hide_order_id)   order by size_order");
		
		
		$sizeId_arr=$size_order_data=$order_dtls_arr=array();
		foreach($sql_query as $row)
		{

			if($row[csf('color_number_id')]==$hidd_gmts_color){

				$sizeId_arr[$row[csf('size_number_id')]]=$row[csf("size_number_id")];				
				$size_wise_qnty[$row[csf('size_number_id')]]['order_qty']+=$row[csf('order_quantity')];
				$color_arr[$color_lib[$row[csf('color_number_id')]]]=$color_lib[$row[csf('color_number_id')]];
			}
			    $order_qnty+=$row[csf('order_quantity')];
				$plan_cut_qnty+=$row[csf('plan_cut_qnty')];
				$po_number.=$po_number_arr[$row[csf('po_break_down_id')]].",";
				$internal_ref.=$internal_ref_arr[$row[csf('po_break_down_id')]].",";
				
		}
		
		
		 $internal_ref=implode(",",array_unique(explode(",",$internal_ref)));
		 $all_poid=implode(",",array_unique(explode(",",$po_number)));
		$colors=implode(",",$color_arr);
		// echo "<pre>";
		// print_r($size_order_data);
		// echo count($sizeId_arr);


		// ========================================= FOR COLOR QUANTITY ==================================
		$body_part_type_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");
		$size_lib=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		


		$cad_eff_arr=sql_select("SELECT a.item_number_id, a.body_part_id, b.dia_width, d.fabric_color_id, d.fin_fab_qnty as fin_fab_qnty ,e.cad_eff_percent,e.rmg_process_breakdown FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b,wo_po_color_size_breakdown c, wo_booking_dtls d,wo_booking_mst e   WHERE a.id=b.pre_cost_fabric_cost_dtls_id and c.id=b.color_size_table_id  and b.color_size_table_id=d.color_size_table_id and  b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and  b.po_break_down_id=d.po_break_down_id  and e.id=d.booking_mst_id and d.job_no='$job_no' and d.booking_type=1 and d.is_short=2 and d.fabric_color_id=$hidd_gmts_color and d.po_break_down_id in ($hide_order_id)  and d.status_active=1 and  d.is_deleted=0  and e.status_active=1 and  e.is_deleted=0 ");

		
		


		foreach($cad_eff_arr as $val)
		{	
			$body_part_details_arr[$val[csf('body_part_id')]]['finish_qnty']+=$val[csf('fin_fab_qnty')];
			$body_part_details_arr[$val[csf('body_part_id')]]['dia_width']=$val[csf('dia_width')];
		}

		

		$color_qnty_sql=sql_select("select id, job_no, seq,  item_number_id, body_part_id,  color_type_id, 	construction, composition, fabric_description, source_id, gsm_weight,color, consumption_basis, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, status_active, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, 
		 process_loss_method, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, plan_cut_qty, job_plan_cut_qty, is_apply_last_update,
		 
		uom, body_part_type ,job_id
	   from wo_pre_cost_fabric_cost_dtls where job_id in($job_id_hidden) and status_active=1 and is_deleted=0 order by seq asc");
	   
		$color_qty_bundle=array();
		$color_lay_qty=array();
		foreach($color_qnty_sql as $row)
		{			
			// $color_qty_bundle[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('po_id')]][$row[csf('dia_width')]]+=$row[csf('requirment')];//[$row[csf('cutting_no')]]
			// $color_lay_qty[$row[csf('po_id')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('dia_width')]]+=$row[csf('requirment')];

			$body_part_arr[$row[csf('body_part_id')]]=$row[csf('body_part_id')];
			$body_part_details_arr[$row[csf('body_part_id')]]['gsm']=$row[csf('gsm_weight')];
			$body_part_details_arr[$row[csf('body_part_id')]]['width_dia_type']=$fabric_typee[$row[csf('width_dia_type')]];
			$body_part_details_arr[$row[csf('body_part_id')]]['avg_cons']+=$row[csf('avg_finish_cons')];
			$body_part_details_arr[$row[csf('body_part_id')]]['fabric_description']=$row[csf('fabric_description')];
		}
	
	
		?>
		
		<?
		ob_start();
		
		$col_span=25+count($sizeId_arr);
		$table_width=2640+(count($sizeId_arr)*80);
		$div_width=$table_width+20;
		$i=1; 
		$total_layf_balance=0; 
		$total_markerf_qty=0; 
		$total_sizef_ratio=0; 
		$sizeDataArray=array();
		$plan_cut_qty=array();
	    //print_r($sizeDataArrayplan);die;    
		
		$avg_access_per=(($plan_cut_qnty-$order_qnty)/$order_qnty)*100;
		?>
		<style>
		.tdwidth{
				font-size: 18px;
			} 
			
		</style>
		<div style="width:100%;">
		
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
					
			
				    <tr>
						<th  colspan="5" align="center" ><h1>Cutting Order Program</h1></th>
					</tr>
				    <tr>
						<td  width="120px"><b>Job No</b></td>
						<td colspan="2" width="300px" class="tdwidth"><?=$job_no;?></td>
						<td  width="120px"><b>Int Ref</b></td>
						<td  class="tdwidth"   width="200px"><?=$internal_ref;?></td>
					</tr>
					<tr>
						<td ><b>Buyer Name</b></td>
						<td colspan="2"   class="tdwidth"><?=$buyer_arr[$job_details_arr[0]['BUYER_NAME']];?></td>
						<td ><b>Garments Item</b></td>
						<td  class="tdwidth"><?
						$gmts_id_arr=explode(",",$job_details_arr[0]['GMTS_ITEM_ID']);
						foreach($gmts_id_arr as $gid){
							$gmts_items .=$garments_item[$gid].",";
						}
						echo $gmts_items;?></td>
					</tr>
					<tr>
						<td  ><b>Brand Name</b></td>
						<td colspan="2"  class="tdwidth" ><?=$lib_brand[$job_details_arr[0]['BRAND_ID']];?></td>
						<td  ><b>Fit Name</b></td>
						<td   class="tdwidth"><?=$fit_list_arr[$job_details_arr[0]['FIT_ID']];?></td>
					</tr>
					<tr>
						<td  ><b>Style Ref</b></td>
						<td colspan="2"class="tdwidth"><?=$ref_no;?></td>
						<td  ><b>Wash Status</b></td>
						<td   class="tdwidth"><?	if($wash_cost_arr[0][csf('wash_cost')]>0){	echo "Yes";	}else{	echo "No";	} ;?>	</td>
					</tr>

					<tr>
						<td  rowspan="3"><b>PO No.</b></td>
						<td colspan="2"  class="tdwidth" rowspan="3" ><div style="word-wrap:break-word; width:700px;font-size: 18px"><?=$all_poid;?></div></td>
						<td  ><b>Order Qty</b></td>
						<td   class="tdwidth"><?=$order_qnty;?></td>
					</tr>
					<tr>
						<td   ><b>Plan Cut Order Qty %</b> </td>
						<td    class="tdwidth"><?=$plan_cut_qnty;?></td>
					</tr>
					<tr>
						<td><b>Piping Conjunction 100 Pcs</b> </td>
						<td   class="tdwidth"></td>
					</tr>
					
					<tr>
						
						
						<td  class="tdwidth"><b>Cutting TNA</b></td>
						<td><b>Start:</b></td>
						<td class="tdwidth"></td>
						<td ><b>END</b></td>
						<td  class="tdwidth"></td>
					</tr>
				</table>

	
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tr>
						<td colspan="3" width="7%"><b>Body Part</b></td>
						<?
						foreach($body_part_arr as $bid){?>
						<td  width="15%" class="tdwidth"><?=$body_part_type_arr[$bid];?></td>
						<?}?>
						
					</tr>
					<tr>
						<td colspan="3" width="8%"><b>Booking GSM with Fabrication</b></td>
						<?
						foreach($body_part_arr as $bid){?>
						<td  width="15%" class="tdwidth"><?=$body_part_details_arr[$bid]['gsm'].",".$body_part_details_arr[$bid]['fabric_description'] ;?></td>
						<?}?>
		
					<tr>
						<td colspan="3" ><b>Booking Dia</b></td>
						<?
						foreach($body_part_arr as $bid){?>
						<td  width="15%" class="tdwidth"><?=$body_part_details_arr[$bid]['dia_width'].",".$body_part_details_arr[$bid]['width_dia_type'];?></td>
						<?}?>
					
					</tr>
					<tr>
						<td colspan="3" ><b>Booking Conjunction</b></td>
						<?
						foreach($body_part_arr as $bid){?>
						<td  width="15%" class="tdwidth"><?=$body_part_details_arr[$bid]['avg_cons'];?></td>
						<?}?>
					
					</tr>
					<tr>
						<td colspan="3" ><b>Actual Cutting Conjunction</b></td>
						<?
						foreach($body_part_arr as $bid){?>
						<td  width="15%"></td>
						<?}?>
					</tr>
					<tr>
						<td colspan="3" ><b>Received G.S.M</b></td>
						<?
						foreach($body_part_arr as $bid){?>
						<td  width="15%"></td>
						<?}?>
					</tr>
					<tr>
						<td colspan="3" ><b>Cutting Received Dia</b></td>
						<?
						foreach($body_part_arr as $bid){?>
						<td  width="15%"></td>
						<?}?>
					</tr>
				</table>
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tr>
						<td rowspan="2" colspan="<?=count($body_part_arr);?>" align="center"  width="21%"><b>Booking Qty (Kg)</b></td>
						<td rowspan="2" colspan="5"   align="center"><b>Cutting Status</b></td>
						<td rowspan="2" colspan="2"  width="15%" align="center"><b>Cutting Allowance Budget %</b></td>
						<td colspan="2"  width="10%"><b>Size</b></td>	
						<?
						foreach($sizeId_arr as $sid){?>
						<td width="15%" align="center"><b><?=$size_lib[$sid];?></b></td>
						<?}?>
						
						<td width="10%" align="center"><b>Total</b></td>
						<td colspan="2"width="10%"><b>Booking Eff. %</b></td>
						<td colspan="4" width="10%" class="tdwidth"><?=$cad_eff_arr[0][csf('cad_eff_percent')];
						$booking_per_arr=explode("_",$cad_eff_arr[0][csf('rmg_process_breakdown')]);
						$booking_per=$booking_per_arr[2]+$booking_per_arr[1]+$booking_per_arr[0];
						// echo $booking_per_arr[2]."+".$booking_per_arr[1]."+".$booking_per_arr[0];
						
						?></td>
					</tr>
					<tr>
						<td colspan="2" ><b>Color Order QTY</b></td>
						<?
						$order_total=0;
						foreach($sizeId_arr as $sid){?>
						<td  align="center" class="tdwidth"><?=$size_wise_qnty[$sid]['order_qty'];
						$order_total+=$size_wise_qnty[$sid]['order_qty'];
						?></td>
						<?}?>
						<td align="center"  class="tdwidth"><?=$order_total;?></td>
						<td colspan="2" ><b>Average Eff. %</b></td>
						<td colspan="4"> </td>
					</tr>
					<tr>

						<?
						if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
								<td align="left"   class="tdwidth"><b><?=$body_part_type_arr[$bid];?></b></td>
								<?}
						}else{
							?>
								<td align="center" ></td>
								<?
						}?>
						
					
						<td rowspan="4" width="10%"><b>Received Batch</b></td>
						<td rowspan="4" width="10%"><b>Color</b></td>
						<td rowspan="4" width="10%"><b>Cutting No.</b></td>
						<td rowspan="4" width="10%"><b>Cutting Batch</b></td>
						<td rowspan="4" width="10%"><b>Cutting Fabric Kg / Yds</b></td>
						<td colspan="2" width="150" align="center" class="tdwidth"><?	$allowance_budget=$avg_access_per-$booking_per;	echo number_format($allowance_budget,2);;	?></td>
						<td colspan="2" width="150"><b>Input QTY %</b></td>
						<?
						$input_total=0;
						foreach($sizeId_arr as $sid){?>
						<td width="100" align="center" class="tdwidth"><?=number_format($size_wise_qnty[$sid]['order_qty']+($size_wise_qnty[$sid]['order_qty']*$allowance_budget)/100,0);;
						$input_total+=$size_wise_qnty[$sid]['order_qty']+($size_wise_qnty[$sid]['order_qty']*$allowance_budget)/100;
						?></td>
						<?}?>
						<td align="center" width="100" class="tdwidth"><?=number_format($input_total,0);;?></td>
						<td colspan="2" width="100"><b>Vessel Date:</b></td>
						<td colspan="4" width="100" class="tdwidth"><?=min($shipment_date_arr);?></td>
					</tr>
					<tr>

						<?
						if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td align="center" class="tdwidth"><b><?=number_format($body_part_details_arr[$bid]['finish_qnty'],2);?></b></td>
							<?}
						}else{
							?>
								<td align="center" width="100"></td>
								<?
						}?>
						<td colspan="2" width="100" align="center" class="tdwidth"><? $allowance_budget2=$avg_access_per-$booking_per_arr[0]; echo number_format($allowance_budget2,2);?></td>
						<td colspan="2" width="100"><b>QI Ok QTY %</b></td>
						<?
						$qi_total=0;
						foreach($sizeId_arr as $sid){?>
						<td width="100" align="center" class="tdwidth"><?=number_format($size_wise_qnty[$sid]['order_qty']+($size_wise_qnty[$sid]['order_qty']*$allowance_budget2)/100,0);;
						$qi_total+=$size_wise_qnty[$sid]['order_qty']+($size_wise_qnty[$sid]['order_qty']*$allowance_budget2)/100;
						?></td>
						<?}?>
						<td align="center" width="100" class="tdwidth"><?=number_format($qi_total,0);;?></td>
						<td colspan="2" width="100"><b>Color :</b></td>
						<td colspan="4" width="100" class="tdwidth"><?=$colors;?></td>
					</tr>
					<tr>
						<td colspan="<?=count($body_part_arr);?>" align="center"><b>Received Kg</b></td>					
						<td colspan="2" width="100" align="center" class="tdwidth"><?=number_format($avg_access_per,2);;?></td>
						<td colspan="2" width="100"><b>Plan Cut</b></td>
						<?
						$plan_cut_total=0;
						foreach($sizeId_arr as $sid){?>
						<td width="100" align="center" class="tdwidth"><?=number_format($size_wise_qnty[$sid]['order_qty']+($size_wise_qnty[$sid]['order_qty']*$avg_access_per)/100,0);
						$plan_cut_total+=$size_wise_qnty[$sid]['order_qty']+($size_wise_qnty[$sid]['order_qty']*$avg_access_per)/100;
						?></td>
						<?}?>
						<td align="center" width="100" class="tdwidth"><?=number_format($plan_cut_total,0);;?></td>
						<td colspan="2" width="100"><b>Others</b></td>
						<td colspan="4" width="100"></td>
					</tr>
					<tr>
						<?
						if(count($body_part_arr)>0){
						foreach($body_part_arr as $bid){?>
						<td align="center" class="tdwidth"><b><?=$body_part_type_arr[$bid];?></b></td>
						<?}
						}else{
							?>
								<td align="center" width="100"></td>
								<?
						}?>
						<td width="100"><b>Mar/L.</b></td>
						<td width="100"><b>Mar/W.</b></td>
						<td width="100"><b>Mar.Eff.</b></td>
						<td width="100"><b>M.Ratio</b></td>					
						<td width="100" colspan="<?=count($sizeId_arr);?>">&nbsp;</td>
						
						<td width="100"></td>
					
						<td width="150"><b>Cutt. Date</b></td>
						<td width="150"><b>Batch Correction</b></td>
						<td width="150"><b>C.W.Piping Kg</b></td>
						<td width="150"><b>Cutpis. Kg / Yds</b></td>
						<td width="150"><b>Fab. I/H</b></td>
						<td width="150"><b>Remarks</b></td>
					</tr>
					<tr>
						<?
					 	 if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td style=" padding: 5px;"><b></b></td>
							<?}}else{?>
							<td align="center"  style=" padding: 5px;"></td>
						<?}?>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					
							
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td style=" padding: 5px;"  >&nbsp;</td>
						<?}?>
						<td style=" padding: 5px;" >&nbsp;</td>
					
						<td style=" padding: 5px;" >&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						<?
					 	 if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td style=" padding: 5px;"><b></b></td>
							<?}}else{?>
							<td align="center"  style=" padding: 5px;"></td>
						<?}?>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					
							
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td style=" padding: 5px;"  >&nbsp;</td>
						<?}?>
						<td style=" padding: 5px;" >&nbsp;</td>
					
						<td style=" padding: 5px;" >&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						<?
					 	 if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td style=" padding: 5px;"><b></b></td>
							<?}}else{?>
							<td align="center"  style=" padding: 5px;"></td>
						<?}?>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					
							
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td style=" padding: 5px;"  >&nbsp;</td>
						<?}?>
						<td style=" padding: 5px;" >&nbsp;</td>
					
						<td style=" padding: 5px;" >&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						<?
					 	 if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td style=" padding: 5px;"><b></b></td>
							<?}}else{?>
							<td align="center"  style=" padding: 5px;"></td>
						<?}?>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					
							
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td style=" padding: 5px;"  >&nbsp;</td>
						<?}?>
						<td style=" padding: 5px;" >&nbsp;</td>
					
						<td style=" padding: 5px;" >&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						<?
					 	 if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td style=" padding: 5px;"><b></b></td>
							<?}}else{?>
							<td align="center"  style=" padding: 5px;"></td>
						<?}?>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					
							
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td style=" padding: 5px;"  >&nbsp;</td>
						<?}?>
						<td style=" padding: 5px;" >&nbsp;</td>
					
						<td style=" padding: 5px;" >&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						<?
					 	 if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td style=" padding: 5px;"><b></b></td>
							<?}}else{?>
							<td align="center"  style=" padding: 5px;"></td>
						<?}?>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					
							
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td style=" padding: 5px;"  >&nbsp;</td>
						<?}?>
						<td style=" padding: 5px;" >&nbsp;</td>
					
						<td style=" padding: 5px;" >&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						<?
					 	 if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td style=" padding: 5px;"><b></b></td>
							<?}}else{?>
							<td align="center"  style=" padding: 5px;"></td>
						<?}?>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					
							
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td style=" padding: 5px;"  >&nbsp;</td>
						<?}?>
						<td style=" padding: 5px;" >&nbsp;</td>
					
						<td style=" padding: 5px;" >&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						<?
					 	 if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td style=" padding: 5px;"><b></b></td>
							<?}}else{?>
							<td align="center"  style=" padding: 5px;"></td>
						<?}?>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					
							
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td style=" padding: 5px;"  >&nbsp;</td>
						<?}?>
						<td style=" padding: 5px;" >&nbsp;</td>
					
						<td style=" padding: 5px;" >&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						<?
					 	 if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td style=" padding: 5px;"><b></b></td>
							<?}}else{?>
							<td align="center"  style=" padding: 5px;"></td>
						<?}?>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					
							
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td style=" padding: 5px;"  >&nbsp;</td>
						<?}?>
						<td style=" padding: 5px;" >&nbsp;</td>
					
						<td style=" padding: 5px;" >&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						<?
					 	 if(count($body_part_arr)>0){
							foreach($body_part_arr as $bid){?>
							<td style=" padding: 5px;"><b></b></td>
							<?}}else{?>
							<td align="center"  style=" padding: 5px;"></td>
						<?}?>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					
							
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td style=" padding: 5px;"  >&nbsp;</td>
						<?}?>
						<td style=" padding: 5px;" >&nbsp;</td>
					
						<td style=" padding: 5px;" >&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
						<td style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						
						<td colspan="<?=count($body_part_arr)+8;?>" align="right" style=" padding: 5px;">&nbsp;<b>TOTAL=</b></td>
						<td style=" padding: 5px;">&nbsp;</td>
						<?
						foreach($sizeId_arr as $sid){?>
						<td width="100" >&nbsp;</td>
						<?}?>
						<td >&nbsp;</td>
					
						<td >&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						 <td>&nbsp;</td>
						 <td>&nbsp;</td>
						 <td>&nbsp;</td>
					</tr>

					<tr>
						<td colspan="<?=9+count($sizeId_arr)+7+count($body_part_arr);?>" align="center" style=" padding: 5px;"><b>Others : Fabric Quality Instruction:Need Good Handfeel.Without Pass Test Report No Fabric Will Be Delivered</b></td>						
					</tr>

					<tr>

						<td rowspan="3" style=" padding: 5px;">CPM Method</td>
						
						<td align="left" colspan="2" style=" padding: 5px;">&nbsp;<b>Cutting Production</b></td>
						<td colspan="4" style=" padding: 5px;">&nbsp;</td>
					
						<td width="100" colspan="<?=count($sizeId_arr)+12;?>" rowspan="3" align="center" style=" padding: 5px;">Note: </td>
						
					</tr>

					<tr>

						<td align="left" colspan="2" style=" padding: 5px;">&nbsp;<b>Cutting Quality</b></td>
						<td colspan="4" style=" padding: 5px;">&nbsp;</td>
					
					</tr>

					<tr>
						<td align="left" colspan="2" style=" padding: 5px;">&nbsp;<b>Cutting Rix Asisment</b></td>
						<td colspan="4" style=" padding: 5px;">&nbsp;</td>
					</tr>

				
			
			
				
						
					
			</table>
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<body>
					<tr>
						
						<td colspan="3" align="left" width="165px" style=" padding: 5px;">&nbsp;<b>Prepared by</b></td>
						<td colspan="4" width="190px" style=" padding: 5px;">&nbsp;</td>
						<td colspan="2" width="150px" style=" padding: 5px;" align="left">&nbsp;<b>IE.& Planing Checked</b></td>
						<td colspan="4" width="200px" style=" padding: 5px;">&nbsp;</td>
						<td colspan="2" width="150px" style=" padding: 5px;" align="left">&nbsp;<b>X Checked By</b></td>
						<td colspan="4" width="200px" style=" padding: 5px;">&nbsp;</td>
						<td colspan="2" width="150px" style=" padding: 5px;" align="left">&nbsp;<b>Checked Approval By</b></td>
						<td colspan="4" width="200px" style=" padding: 5px;">&nbsp;</td>
					</tr>
					<tr>
						
						<td colspan="24" align="left" width="150px" style=" padding: 5px;">&nbsp;</td>
						
					</tr>
				</body>	
			</table>

		</div>





	    
		<?

	}

	foreach (glob("*.xls") as $filename)
	{		
		@unlink($filename);

	}
	$name=time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	exit();
}
?>