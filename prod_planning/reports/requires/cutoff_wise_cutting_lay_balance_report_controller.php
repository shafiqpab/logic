<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name	= $_SESSION['logic_erp']['user_id'];
$data		= $_REQUEST['data'];
$action		= $_REQUEST['action'];


//--------------------------------------------------------------------------------------------------------------------
if($action=="style_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
   <script>
		function js_set_value( str ) 
		{
			
			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
    </script>
		

    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	//$job_year=str_replace("'","",$job_year);
	if($buyer!=0) $buyer_cond=" and a.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(a.insert_date,'YYYY')";
	}
	
	 $sql = "SELECT a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a where a.company_name=$company $buyer_cond  and is_deleted=0 order by a.id desc"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","410","300",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;	
	//echo "<input type='hidden' id='txt_selected_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref_no;?>';
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
	}
	</script>
    
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'create_order_no_search_list_view', 'search_div', 'cutoff_wise_cutting_lay_balance_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond order by b.id, b.pub_shipment_date desc";
	// echo $sql; die;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
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
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "onchange_buyer();load_drop_down( 'requires/cutoff_wise_cutting_lay_balance_report_controller', this.value, 'load_drop_down_season', 'season_td');load_drop_down( 'requires/cutoff_wise_cutting_lay_balance_report_controller', this.value, 'load_drop_down_brand', 'brand_td');" );     	 
	exit();
}


if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season", 110, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", "", "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	// echo create_drop_down( "cbo_brand", 110, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Select--", "", "" );
	echo create_drop_down( "cbo_brand", 110, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_gmts_item")
{
	//echo "select gmts_item_id from wo_po_details_master where job_no='$data'";
	$gmts_item=return_field_value("gmts_item_id","wo_po_details_master","job_no='$data'","gmts_item_id");
	
	echo create_drop_down( "cbo_gmts_item", 100, $garments_item,"", 1, "-- Select --", $selected, "","",$gmts_item,"" );     	 
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $sytle_ref_no; ?>', 'create_job_no_search_list_view', 'search_div', 'cutoff_wise_cutting_lay_balance_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	
	// var_dump($data);
	
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
	if($data[3]!="")
	{
		$search_cond = " and $search_field  like '$search_string'";
	}
	else
	{
		?>
		<div class="alert alert-danger">Please enter job or style reference.</div>
		<?
		die;
	}
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
	
	$company_arr		= return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$buyer_arr			= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_arr			= return_library_array( "select id, season_name from  lib_buyer_season",'id','season_name');
	$table_arr			= return_library_array( "select id, table_no from lib_cutting_table",'id','table_no');

	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "SELECT id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $search_cond  $buyer_id_cond $year_cond order by id desc";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','','') ;
	exit();
} // Job Search end


if($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_buyer=str_replace("'","",$cbo_buyer_name);
	$job_no=str_replace("'","",$txt_job_no);
	$hidd_po=str_replace("'","",$hide_order_id);
	$txt_po_no=str_replace("'","",$txt_order_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$txt_style_ref=str_replace("'","",$txt_ref_no);
	$job_year=str_replace("'","",$cbo_job_year);
	$season_year=str_replace("'","",$cbo_season_year);
	$cbo_season=str_replace("'","",$cbo_season);

	$sqlCond = "";
	$sqlCond .= ($cbo_company==0) ? "" : " and a.company_name=$cbo_company";
	$sqlCond .= ($cbo_buyer==0) ? "" : " and a.buyer_name=$cbo_buyer";
	$sqlCond .= ($job_no=="") ? "" : " and a.job_no='$job_no'";
	$sqlCond .= ($txt_style_ref=="") ? "" : " and a.style_ref_no='$txt_style_ref'";
	$sqlCond .= ($cbo_season==0) ? "" : " and a.season_buyer_wise='$cbo_season'";
	$sqlCond .= ($hidd_po=="") ? "" : " and b.id in ( $hidd_po )";
	if($hidd_po=="")
	{
		$sqlCond .= ($txt_po_no=="") ? "" : " and b.po_number='$txt_po_no'";
	}
	if($date_from!="" && $date_to!="" ) 
	{
		$sqlCond .= " and c.cutup_date between '".$date_from."' and '".$date_to."'";
	}
	
	if($job_year!=0) 
	{
		$sqlCond .= " and to_char(a.insert_date,'YYYY')=$job_year";
	}
	
	if($season_year!=0) 
	{
		$sqlCond .= " and to_char(a.season_year,'YYYY')=$season_year";
	}

	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$sizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$countryArr = return_library_array("select id,country_name from  lib_country ","id","country_name");
	$countryCodeArr = return_library_array("select id,short_name from  lib_country ","id","short_name");
	$colorArr = return_library_array("SELECT id,color_name from lib_color","id","color_name");
	$buyerArr = return_library_array("SELECT id,buyer_name from lib_buyer","id","buyer_name");
	
	
	ob_start();

	$sql="SELECT a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty,c.item_number_id as item_number_id, b.id as po_id,b.file_no,b.grouping, b.po_number, b.pub_shipment_date,b.pack_handover_date, b.shipment_date, b.po_received_date, (b.po_quantity*a.total_set_qnty) as po_quantity, b.excess_cut, b.plan_cut, b.unit_price, b.po_total_price,c.id, c.cutup, c.country_id,c.country_ship_date,c.cutup_date, c.size_number_id,c.size_order, c.color_number_id, c.order_quantity, c.order_rate, c.order_total, c.excess_cut_perc, c.plan_cut_qnty
	FROM wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c
	 
	
	WHERE  a.id=b.job_id and b.id = c.po_break_down_id and b.job_id = c.job_id and b.is_deleted=0 and b.status_active=1 and
	c.id!=0 and a.is_deleted=0 AND a.status_active =1 AND c.is_deleted =0 AND c.status_active =1 $sqlCond  order by c.country_ship_date,c.size_order";
	
	// echo $sql;die;

	$sql_data = sql_select($sql);
	$data_array=array();
	$size_array=array();
	$po_id_arr = array();
	foreach( $sql_data as $row)
	{
		$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		$job_info_array[$row[csf('job_no')]]=array(
			"job_no"=>$row[csf('job_no')],
			"job_quantity"=>$row[csf('job_quantity')],
			"company_name"=>$row[csf('company_name')],
			"buyer_name"=>$row[csf('buyer_name')],
			"style_ref_no"=>$row[csf('style_ref_no')],
			"product_dept"=>$row[csf('product_dept')],
			"dealing_marchant"=>$row[csf('dealing_marchant')],
			"job_id"=>$row[csf('job_id')],
			"order_uom"=>$row[csf('order_uom')],
			"brand_id"=>$row[csf('brand_id')],
			"season"=>$row[csf('season')]
		);

		$size_array[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
		$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_ship_date')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('cutup')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('order_quantity')];
		$data_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_ship_date')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('cutup')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
	}

	// echo "<pre>";print_r($job_no_array);die;
	$po_id_con = where_con_using_array($po_id_arr,0,"a.po_break_down_id");
	$sql = "SELECT a.po_break_down_id as po_id,a.item_number_id as item_id, a.cutup, a.country_id,a.country_ship_date,a.cutup_date, a.size_number_id as size_id, a.color_number_id as color_id,c.size_qty from wo_po_color_size_breakdown a, ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.color_number_id=b.color_id and a.item_number_id=b.gmt_item_id and b.id=c.dtls_id and a.size_number_id=c.size_id and a.po_break_down_id=c.order_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $po_id_con";
	// echo $sql;die;
	$res = sql_select($sql);
	$lay_qty_array = array();
	foreach ($res as $v) 
	{
		$lay_qty_array[$v['PO_ID']][$v['COUNTRY_SHIP_DATE']][$v['ITEM_ID']][$v['COUNTRY_ID']][$v['CUTUP']][$v['COLOR_ID']][$v['SIZE_ID']] += $v['SIZE_QTY'];
	}
	// echo "<pre>";print_r($lay_qty_array);die;

	?>


    <fieldset style="width:<?=$tbl_width+20;?>px;margin:0 auto">

		<?
		$tbl_width = 860+(count($size_array)*60);
		foreach($job_info_array as $rdata=>$det)
		{
			$po_row_span_arr = array();
			$date_row_span_arr = array();
			$item_row_span_arr = array();
			$country_row_span_arr = array();
			foreach($data_array[$det['job_no']] as $po_id=>$po_data)
			{
				$po_rowspan=0;
				foreach($po_data as $ship_date=>$ship_date_data)
				{
					$date_rowspan=0;
					foreach($ship_date_data as $item_id=>$item_data)
					{
						$item_rowspan=0;
						foreach($item_data as $country_id=>$country_data)
						{
							foreach($country_data as $cutup_id=>$cutup_data)
							{
								foreach($cutup_data as $color_id=>$color_data)
								{
									$po_rowspan++;
									$date_rowspan++;
									$item_rowspan++;
									$country_row_span_arr[$po_id][$ship_date][$item_id][$country_id]++;
								}
							}
							$po_rowspan++;	
							$date_rowspan++;	
							$item_rowspan++;	
						}
						$po_rowspan++;					
						$date_rowspan++;						
						$item_row_span_arr[$po_id][$ship_date][$item_id]=$item_rowspan;					
					}
					$po_rowspan++;					
					$date_row_span_arr[$po_id][$ship_date]=$date_rowspan;
				}	
				$po_row_span_arr[$po_id]=$po_rowspan;		  
			}
			// echo "<pre>";print_r($date_row_span_arr);die;
			//ksort($job_size_array[$det['job_no']]);
			?>
			<table width="<?=$tbl_width;?>" align="center" border="1" rules="all">
				<tr style="background-color:#FFF;font-weight:bold;text-align:center;font-size:20px;">
					<td colspan="8" style="font-size:18px;">Cut-Off Wise Cutting Lay Balance Report<hr class="hr-style-1"></td>
				</tr>
				<tr style="background-color:#FFF;font-weight:bold;">
					<td width="60" align="right">Job No: </td><td width="90"><? echo $det['job_no']; ?></td>
					<td width="60" align="right">Buyer: </td><td width="85"><? echo $buyerArr[$det['buyer_name']]; ?></td>
	
					<td width="65" align="right">Style Ref.: </td><td width="85"><? echo $det['style_ref_no']; ?></td>
					<td width="60" align="right">Job Qnty: </td><td width="90"><? echo $det['job_quantity']."(Pcs)"; ?></td>
				</tr>
			</table>
			<br/>
			<table width="<?=$tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="100">PO Number</th>
						<th width="60">Country Ship Date</th>
						<th width="100">Item </th>
						<th width="100">Country</th>
						<th width="30">Country Code</th>
						<th width="70">Cut-off </th>
						<th width="120">Color</th>
						<?
						foreach($size_array as $key=>$value)
						{
							?>
							<th width="60"><? echo $sizeArr[$value];?></th>
							<?
						}					
						?>
						<th width="60">Total Balance in PCS</th>
						<th width="60">Total Balance in Dzn</th>
						<th width="100">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$gr_qty_arr = array();
					foreach($data_array[$det['job_no']] as $po_id=>$po_data)
					{
						$po=0;
						foreach($po_data as $ship_date=>$ship_date_data)
						{
							$dt=0;
							$date_lay_bal_arr = array();
							foreach($ship_date_data as $item_id=>$item_data)
							{
								$it = 0;
								$item_lay_bal_arr = array();
								foreach($item_data as $country_id=>$country_data)
								{
									$ct=0;
									$counry_lay_bal_arr = array();
									foreach($country_data as $cutup_id=>$cutup_data)
									{
										foreach($cutup_data as $color_id=>$row)
										{											
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
												<?
												if($po==0)
												{
													?>
													<td rowspan="<?=$po_row_span_arr[$po_id];?>" width="100"><?=$row['po_number'];?></td>
													<?
													$po++;
												}
												if($dt==0)
												{
													?>
													<td rowspan="<?=$date_row_span_arr[$po_id][$ship_date];?>" width="60" align="center"><?=change_date_format($ship_date);?></td>
													<?
													$dt++;
												}
												if($it==0)
												{
													?>
													<td rowspan="<?=$item_row_span_arr[$po_id][$ship_date][$item_id];?>" width="100"> <?=$garments_item[$item_id];?></td>
													<?
													$it++;
												}
												if($ct==0)
												{
													?>
													<td rowspan="<?=$country_row_span_arr[$po_id][$ship_date][$item_id][$country_id];?>" width="100"><?=$countryArr[$country_id];?></td>
													<td rowspan="<?=$country_row_span_arr[$po_id][$ship_date][$item_id][$country_id];?>" width="30"><?=$countryCodeArr[$country_id];?></td>
													<td rowspan="<?=$country_row_span_arr[$po_id][$ship_date][$item_id][$country_id];?>" width="70"><?=$cut_up_array[$cutup_id];?></td>
													<?
													$ct++;
												}
												?>
												<td width="120"><?=$colorArr[$color_id];?></td>
												<?
												$tot = 0;
												foreach($size_array as $key=>$value)
												{
													$lay_qty = $lay_qty_array[$po_id][$ship_date][$item_id][$country_id][$cutup_id][$color_id][$value];
													?>
													<td width="60" align="right"><?=number_format(($row[$value]['qty'] - $lay_qty),0);?></td>
													<?
													$tot += $row[$value]['qty'] - $lay_qty;
													$counry_lay_bal_arr[$po_id][$ship_date][$item_id][$country_id][$value] += $row[$value]['qty'] - $lay_qty;
													$item_lay_bal_arr[$po_id][$ship_date][$item_id][$value] += $row[$value]['qty'] - $lay_qty;
													$date_lay_bal_arr[$po_id][$ship_date][$value] += $row[$value]['qty'] - $lay_qty;
													$gr_qty_arr[$value] += $row[$value]['qty'] - $lay_qty;
												}					
												?>
												<td width="60"align="right"><?=number_format($tot,0);?></td>
												<td width="60"align="right"><?=number_format(($tot/12),0);?></td>
												<td width="100"></td>
											</tr>
											<?
											$i++;
										}
									}
									?>
									<tr style="text-align: right;font-weight:bold;background:#DFDFDF;">
										<td colspan="4">Country Wise Lay Balance</td>
										<?
										$tot = 0;
										foreach($size_array as $key=>$value)
										{
											?>
											<td width="60" align="right"><?=number_format($counry_lay_bal_arr[$po_id][$ship_date][$item_id][$country_id][$value],0);?></td>
											<?
											$tot += $counry_lay_bal_arr[$po_id][$ship_date][$item_id][$country_id][$value];
										}					
										?>
										<td width="60"align="right"><?=number_format($tot,0);?></td>
										<td width="60"align="right"><?=number_format(($tot/12),0);?></td>
										<td width="100"></td>
									</tr>
									<?
								}
								
								?>
								<tr style="text-align: right;font-weight:bold;background:#dccdcd;">
									<td colspan="5">Item Wise Lay Balance</td>
									<?
									$tot = 0;
									foreach($size_array as $key=>$value)
									{
										?>
										<td width="60" align="right"><?=number_format($item_lay_bal_arr[$po_id][$ship_date][$item_id][$value],0);?></td>
										<?
										$tot += $item_lay_bal_arr[$po_id][$ship_date][$item_id][$value];
									}					
									?>
									<td width="60"align="right"><?=number_format($tot,0);?></td>
									<td width="60"align="right"><?=number_format(($tot/12),0);?></td>
									<td width="100"></td>
								</tr>
								<?						
							}					
								
							?>
							<tr style="text-align: right;font-weight:bold;background:#D1FFF3;">
								<td colspan="6">Ship Date Wise Lay Balance</td>
								<?
								$tot = 0;
								foreach($size_array as $key=>$value)
								{
									?>
									<td width="60" align="right"><?=number_format($date_lay_bal_arr[$po_id][$ship_date][$value],0);?></td>
									<?
									$tot += $date_lay_bal_arr[$po_id][$ship_date][$value];
								}					
								?>
								<td width="60"align="right"><?=number_format($tot,0);?></td>
								<td width="60"align="right"><?=number_format(($tot/12),0);?></td>
								<td width="100"></td>
							</tr>
							<?	
						}			  
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="7">Grand Total Lay Balance</th>
						<?
						$tot = 0;
						foreach($size_array as $key=>$value)
						{
							?>
							<td width="60" align="right"><?=number_format($gr_qty_arr[$value],0);?></td>
							<?
							$tot += $gr_qty_arr[$value];
						}					
						?>
						<td align="right"><?=number_format($tot,0);?></td>
						<td align="right"><?=number_format(($tot/12),0);?></td>
						<th></th>
					</tr>
				</tfoot>
			</table>
			<?
		}
		?>
    </fieldset>


    <?
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
	exit();
}

?>