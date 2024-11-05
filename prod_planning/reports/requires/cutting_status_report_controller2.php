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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $job_no; ?>', 'create_order_no_search_list_view', 'search_div', 'cutting_status_report_controller2', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond order by b.id, b.pub_shipment_date";
	//echo $sql; die;
		
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
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "onchange_buyer();load_drop_down( 'requires/cutting_status_report_controller2', this.value, 'load_drop_down_season', 'season_td');load_drop_down( 'requires/cutting_status_report_controller2', this.value, 'load_drop_down_brand', 'brand_td');" );     	 
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

$company_arr		= return_library_array( "select id, company_name from lib_company",'id','company_name');
$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$order_no_library	= return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$buyer_arr			= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$season_arr			= return_library_array( "select id, season_name from  lib_buyer_season",'id','season_name');
$brand_arr			= return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
$table_arr			= return_library_array( "select id, table_no from lib_cutting_table",'id','table_no');


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name	= str_replace( "'", "", $cbo_company_name );
	$rept_type	= str_replace( "'", "", $type );
	$hide_order_id	= str_replace( "'", "", $hide_order_id );
	$order_no	= str_replace( "'", "", $txt_order_no );
	//echo $repttype;
	$buyer_name		= str_replace( "'", "", $cbo_buyer_name );
	$brand_name		= str_replace( "'", "", $cbo_brand );
	$season_name	= str_replace( "'", "", $cbo_season );
	$season_year	= str_replace( "'", "", $cbo_season_year );
	$gmts_item		= str_replace( "'", "", $cbo_gmts_item );
	$job_no			= str_replace( "'", "", $txt_job_no );
	$file_no		= str_replace( "'", "", $txt_file_no );
	
	$cutting_no		= str_replace( "'", "", $txt_cutting_no );
	$table_no		= str_replace( "'", "", $txt_table_no );
	$from_date		= str_replace( "'", "", $txt_date_from );
	$to_date		= str_replace( "'", "", $txt_date_to );
	$working_company_id		= str_replace( "'", "", $cbo_working_company_name );
	$ref_no		= str_replace( "'", "", $txt_ref_no);
	$txt_job_no_hidden= str_replace( "'", "", $txt_job_no_hidden);
	
	$sql_cond="";$po_cond="";
	
	if($company_name>0) $sql_cond=" AND a.company_id=$company_name";
	if($buyer_name>0) $sql_cond.=" AND c.buyer_name=$buyer_name";
	if($brand_name>0) $sql_cond.=" AND c.brand_id=$brand_name";
	if($season_name>0) $sql_cond.=" AND c.season_buyer_wise=$season_name";
	if($gmts_item>0) $sql_cond.=" AND b.gmt_item_id in($gmts_item)";
	if($gmts_item>0) $gmt_item_cond=" AND item_number_id in($gmts_item)";else $gmt_item_cond="";
	if($working_company_id>0) $sql_cond.=" AND a.working_company_id=$working_company_id";
	if($job_no!="") $sql_cond.=" AND c.job_no='$job_no' ";
	//	if($order_no!="") $sql_cond.=" AND d.po_number='$order_no' ";
	if($from_date!="" && $to_date!= "") $sql_cond.= " AND a.entry_date between $txt_date_from AND $txt_date_to";

	if($db_type==0)
	{
		if(str_replace("'","",$season_year)!=0) $sql_cond=" and c.season_year=$season_year"; else $year_cond="";
	}
	else if ($db_type==2)
	{
		if(str_replace("'","",$season_year)!=0) $sql_cond=" and c.season_year=$season_year"; else $year_cond="";
	}
	
	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and e.id in(".$po_id.")";
		}
		else
		{
			$po_number="%".trim($order_no)."%";
			$po_cond="and e.po_number like '$po_number'";
		}
	}
	
	if($rept_type==1) // show button start here
	{	
		if($order_no!="")
		{
			//echo "<div align='center'><font style='color:#F00; font-size:18px; font-weight:bold'>PO No Search Not Allow For This button.</font></div>";
			//die;
		}
		
		
		if($db_type==0)
		{
			$sql="SELECT a.id as cut_id, a.entry_form, a.company_id, a.location_id, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.working_company_id, d.order_id as order_id, b.order_cut_no,b.gmt_item_id as gmt_id, b.color_id, b.plies, c.buyer_name, c.style_ref_no, c.style_description,c.brand_id,c.season_buyer_wise,c.season_year, SUM(b.marker_qty) AS marker_qty
			FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, wo_po_details_master c, wo_po_break_down e, ppl_cut_lay_bundle d
			WHERE b.mst_id=a.id and a.id=d.mst_id and b.id=d.dtls_id and d.order_id!='' AND a.job_no=c.job_no and  c.job_no=e.job_no_mst and e.id=d.order_id $sql_cond $po_cond
			 order by b.gmt_item_id,b.color_id,a.cutting_no ASC";
		}
		else
		{
			$sql="SELECT a.id as cut_id, a.entry_form, a.company_id, a.location_id, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.working_company_id,b.id as dtls_id, d.order_id as order_id,b.gmt_item_id as gmt_id, b.order_cut_no, b.color_id, b.plies, c.buyer_name, c.style_ref_no, c.style_description,c.brand_id,c.season_buyer_wise,c.season_year
			FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, wo_po_details_master c, wo_po_break_down e, ppl_cut_lay_bundle d
			WHERE b.mst_id=a.id and a.id=d.mst_id and b.id=d.dtls_id and  a.job_no=c.job_no  and  c.job_no=e.job_no_mst and e.id=d.order_id and d.order_id is not null $sql_cond $po_cond and a.status_active=1 and b.status_active=1 and d.status_active=1 and c.status_active=1 and e.status_active=1
			 order by b.gmt_item_id,b.color_id,a.cutting_no ASC";
		}
		//}
		//echo $sql;//GROUP BY a.id, a.company_id, a.location_id, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.working_company_id, b.order_ids, b.order_cut_no,b.gmt_item_id, b.color_id, b.plies, c.buyer_name, c.style_ref_no, c.style_description
		// echo $sql;
		//$cut_color_arr=array();
		$sql_result=sql_select($sql);
		$subtotal_marker_qty=array();
		foreach($sql_result as $row)
		{
			if($row[csf('order_id')]!="") $all_po_id.=$row[csf('order_id')].",";

			$all_po_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
			
			$all_cut_id.=$row[csf('cut_id')].",";
			$all_cut_id_arr[$row[csf('cut_id')]]=$row[csf('cut_id')];
			
			$all_cut_id.=$row[csf('cut_id')].",";
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cuting_date'] 		= $row[csf('entry_date')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cutting_no']		= $row[csf('cutting_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['company_id']		= $row[csf('company_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['location_id']		= $row[csf('location_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['working_company']	= $row[csf('working_company_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['table_no']			= $table_arr[$row[csf('table_no')]];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_id']			= $row[csf('order_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_cut_no']		= $row[csf('order_cut_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['job_no']			= $row[csf('job_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['batch_id']			= $row[csf('batch_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['plies']				= $row[csf('plies')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['buyer_name']		= $row[csf('buyer_name')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['brand_id']		= $row[csf('brand_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['season_year']		= $row[csf('season_year')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['season']		= $row[csf('season_buyer_wise')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_ref_no']		= $row[csf('style_ref_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_description']	= $row[csf('style_description')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['lay_fabric_wght']	= $row[csf('lay_fabric_wght')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cad_marker_cons']	= $row[csf('cad_marker_cons')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['marker_qty'] 		= $row[csf('marker_qty')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['gmt_item_id'] 		= $row[csf('gmt_item_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['mst_id'] 			= $row[csf('cut_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['dtls_id'] 			= $row[csf('dtls_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['entry_form'] 		= $row[csf('entry_form')];
		}
		//print_r($cut_color_arr);
		
		$all_cut_id=chop($all_cut_id,",");
		$all_po_id=chop($all_po_id,",");
		$unique_ids = array_unique(explode(",", $all_po_id));
		
		
		if($all_po_id=="")
		{
			echo " No Data Found.";
			die;
		}

		$con = connect();
		execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_name and entry_form =79 and ref_from in(1,2,3)");
		oci_commit($con);

		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 79, 1,$unique_ids, $empty_arr);

		$po_no_arr=return_library_array( "select a.id, a.po_number from wo_po_break_down a,GBL_TEMP_ENGINE g where a.id=g.ref_val and g.user_id=$user_name  and g.entry_form =79 and g.ref_from=1 ",'id','po_number');
		
		$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
		$all_cut_id=implode(",",array_unique(explode(",",$all_cut_id)));


		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 79, 2,$all_po_id_arr, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 79, 3,$all_cut_id_arr, $empty_arr);

		$bundle_array=array();
		$bundle_sql=sql_select("SELECT a.cutting_no, b.color_id, b.gmt_item_id as gmt_id,c.size_id, c.size_qty
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in($all_cut_id)");
		$bundle_data_array=array();
		$cut_tot_bundle=array();
		foreach($bundle_sql as $row)
		{
			$bundle_data_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('size_qty')];
			$cut_tot_bundle[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]]++;
		}
		
		
		$sratio_sql=sql_select("SELECT a.id, a.cutting_no, c.color_id, c.size_ratio,b.gmt_item_id as gmt_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_size_dtls c,GBL_TEMP_ENGINE g  where a.id=b.mst_id and b.id=c.dtls_id and a.id=g.ref_val and g.user_id=$user_name  and g.entry_form =79 and g.ref_from=3  and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");
		//  echo $sratio_sql;die();
		// echo $sratio_sql;die();
		
		$size_ratio_arr=array();
		foreach($sratio_sql as $row)
		{
			$size_ratio_arr[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]]+=$row[csf('size_ratio')];
			
		}
		// echo "<pre>";print_r($size_ratio_arr);die;
		$batch_sql=("SELECT a.id,  a.cutting_no, c.batch_no,b.color_id,b.gmt_item_id as gmt_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,pro_roll_details c, GBL_TEMP_ENGINE g  where a.id=b.mst_id and b.id=c.dtls_id and a.id=g.ref_val and g.user_id=$user_name  and g.entry_form =79 and g.ref_from=3  and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=99 and c.status_active=1 and c.is_deleted=0 ");
		// echo $batch_sql;die;
		
		$batch_array=array();
		foreach(sql_select($batch_sql) as $row)
		{
			$batch_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]].=$row[csf('batch_no')].',';
			
		}
		//print_r($size_ratio_array);
		//print_r($cut_color_arr);die;
		//echo $sql;
		
		ob_start();
		
		$size_arr=return_library_array( "SELECT id, size_name from lib_size",'id','size_name');
		$sql_query=sql_select("SELECT po_break_down_id, country_type, country_id, size_number_id, plan_cut_qnty, order_quantity, country_ship_date, size_order 
		from wo_po_color_size_breakdown where  status_active=1 and is_deleted=0 and po_break_down_id in($all_po_id) $gmt_item_cond order by size_order");
		
		
		
		$sizeId_arr=$size_order_data=$order_dtls_arr=array();
		foreach($sql_query as $row)
		{
			$sizeId_arr[$row[csf('size_number_id')]]=$row[csf("size_number_id")];
			$size_order_data[$row[csf('size_number_id')]]['order_quantity']+=$row[csf('order_quantity')];
			$size_order_data[$row[csf('size_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
		}
		//echo "<pre>";
		//print_r($sizeId_arr);die;
		$col_span=15+count($sizeId_arr);
		$table_width=1840+(count($sizeId_arr)*80)+80;
		$div_width=$table_width+20;
		$i=1; $total_layf_balance=0; $total_markerf_qty=0; $total_sizef_ratio=0; $sizeDataArray=array();$plan_cut_qty=array();
		
	      //print_r($sizeDataArrayplan);die;      
		execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_name and entry_form =79 and ref_from in(1,2,3)");
		oci_commit($con);
		disconnect($con);		   
		?>
	    	<div style="width:<? echo $div_width; ?>px;">
	        <table class="rpt_table" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <caption style="font-size:20px; font-weight:bold;">
	            <?
	            $com_name = str_replace( "'", "", $cbo_company_name );
	            echo $company_arr[$com_name]."<br/>"."Cutting Status Report";
	            ?>
	                <div style="color:red; text-align:left; font-size:16px;"></div>
	            </caption>
	           </table>
	       
	            <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	            	<tr>
	                	<td colspan="19" style=" border-color:transparent"></td>
	                    <th>Size</th> <?
						foreach($sizeId_arr as $key=>$value)
						{
							echo '<th>'.$size_arr[$key].'</th>';
						} 
						?>
	                    <th width="80"><b>Total <span style="color:red; font-size:16px;">(Pcs)</span></b></th>
	                   </tr>
		            <tr>
	                <td colspan="19" style=" border-color:transparent"></td>
	                <th>Order QTY</th>
	                <?
						$total_order_qty='';
						foreach($sizeId_arr as $key=>$value)
						{
							
							echo '<td width="80" align="right">'.$size_order_data[$key]['order_quantity'].'</td>';
							$total_order_qty+=$size_order_data[$key]['order_quantity'];
						} 
					 ?>
	                <td align="right"><? echo $total_order_qty; ?></td>
	                </tr>
		            <tr>
	                <td colspan="19" style=" border-color:transparent"></td>
	                <th>Plan Cut</th>
					<?
						$total_plan_qty='';
						foreach($sizeId_arr as $key=>$value)
						{
							echo '<td width="80" align="right">'.$size_order_data[$key]['plan_cut_qnty'].'</td>';
							$total_plan_qty+=$size_order_data[$key]['plan_cut_qnty'];
						} 
					 ?>
	                 <td align="right"><? echo $total_plan_qty; ?></td>
	               </tr>
	                <tr>
	                    <th width="40">Sl</th>
	                    <th width="100">Company Name</th>
	                    <th width="100">Working company</th>
	                    <th width="70">Cutting Date</th>
	                    <th width="100">System Cut No.</th>
	                    <th width="50">Order Cut No.</th>
	                    <th width="100">Buyer Name</th>
	                    <th width="100">Brand Name</th>
	                    <th width="100">Season Year</th>
	                    <th width="100">Job No</th>
	                    <th width="100">Style Reff</th>
	                    <th width="100">Style Description</th>
	                    <th width="100">Gmts Item</th>
	                    <th width="160">PO No</th>
	                    <th width="100">Color Name</th>
	                    <th width="60">Table No</th>
	                    <th width="70">Batch No</th>
	                    <th width="70">Total Size Ratio</th>
	                    <th width="70">Plies</th>
	                    <th width="70">Total Bandle No</th>
	                    <?
	                  	foreach($sizeId_arr as $key=>$value)
	                    {
	                        echo '<th width="80"></th>';
	                    }
	                    ?>
	                    <th width="80"></th>
	                   
	                </tr>
	        </thead>
	    </table>
	    
	    <div style=" max-height:350px; width:<? echo $div_width; ?>px; overflow-y:scroll;" id="scroll_body">
	        <table class="rpt_table" id="table_body" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tbody>
	            	<? 
					$sl=1;$k=1;$total_size_ratio=0;$total_cut_tot_bundle=0;$total_plies=0;
					/*$color_subtot_arr=array();
					$grand_total_fini_req_qty			= 0;
					$grand_total_fini_rcv_qty			= 0;
					$grand_total_rmg_color_qty			= 0;
					$grand_total_plan_cut_qty			= 0;
					$grand_total_yet_to_cut				= 0;
					$grand_total_lay_fabric_weight		= 0;
					$grand_total_cad_marker_cons		= 0;
					$grand_total_marker_qty				= 0;
					$grand_total_qc_pass_qty			= 0;
					$grand_total_replace_qty			= 0;
					$grand_total_reject_qty				= 0;
					$grand_total_cut_cons_qty			= 0;
					$grand_total_qc_pass_cons_qty		= 0;
					$grand_total_cons_variation_qty		= 0;
					$grand_total_cons_variation_percn	= 0;
					$grand_total_reject_kg				= 0;
					$grand_total_reject_percn			= 0;*/
							
					foreach($cut_color_arr as $gmt_id=>$gmt_vals)
					{
						
						$totsize_ratio=0;$totplies_qty=0;
						$tot_size_qty=0;
						$size_total=array();
						foreach($gmt_vals as $color_ids=>$color_vals)
						{
							foreach($color_vals as $cut_no=>$cutting_vals)
							{							
							
							/*$total_fini_req_qty			= 0;
							$total_fini_rcv_qty			= 0;
							$total_rmg_color_qty		= 0;
							$total_plan_cut_qty			= 0;
							$total_yet_to_cut			= 0;
							$total_lay_fabric_weight	= 0;
							$total_cad_marker_cons		= 0;
							$total_marker_qty			= 0;
							$total_qc_pass_qty			= 0;
							$total_replace_qty			= 0;
							$total_reject_qty			= 0;
							$total_cut_cons_qty			= 0;
							$total_qc_pass_cons_qty		= 0;
							$total_cons_variation_qty	= 0;
							$total_cons_variation_percn	= 0;
							$total_reject_kg			= 0;
							$total_reject_percn			= 0;*/
						
							$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
							//echo $cut_no.'='.$color_ids;//$size_ratio_array[$cut_no][$color_ids].'g';
							$size_ratio=$size_ratio_arr[$cut_no][$gmt_id][$color_ids];
							$totsize_ratio+=$size_ratio;
							$totplies_qty+=$cutting_vals['plies'];
							$batch_no=rtrim($batch_array[$cut_no][$gmt_id][$color_ids],',');
							$batch_nos=implode(",",array_unique(explode(",",$batch_no)));

							if($cutting_vals['entry_form']==289){ $print_path='../cutting_plan/requires/woven_cut_and_lay_ratio_wise_entry_controller.php'; }
							else if($cutting_vals['entry_form']==99){ $print_path='../cutting_plan/requires/cut_and_lay_ratio_wise_entry_controller_urmi.php'; }
							else{ $print_path='#'; }
							?>
							<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
								<td width="40"><? echo $sl; ?></td>
								<td width="100"><p><? echo $company_arr[$cutting_vals['company_id']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $company_arr[$cutting_vals['working_company']]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo change_date_format($cutting_vals['cuting_date']); ?>&nbsp;</p></td>
								<td width="100">								
									<!-- <a href="#" onClick="generate_report_lay_chart('<? //echo $cutting_vals['cutting_no']."*".$cutting_vals['job_no']."*".$cutting_vals['working_company']."*".$cutting_vals['location_id']; ?>')"><? //echo $cutting_vals['cutting_no']; ?>
										
									</a> -->
									<!-- ======================= for amtranet ================ -->
									<a href="#" onClick="fnc_print_bundle('<? echo $cutting_vals['company_id']."*".$cutting_vals['mst_id']."*".$cutting_vals['dtls_id'];?>*Lay and Bundle Information','cut_lay_bundle_print','<?=$print_path;?>')"> 
										<? echo $cutting_vals['cutting_no']; ?>
										
									</a>
								</td>
								<td width="50"><p><? echo $cutting_vals['order_cut_no']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $buyer_arr[$cutting_vals['buyer_name']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $brand_arr[$cutting_vals['brand_id']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $season_arr[$cutting_vals['season']].'-'.substr($cutting_vals['season_year'],2,4); ?>&nbsp;</p></td>
								<td width="100"><p><? echo $cutting_vals['job_no']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $cutting_vals['style_ref_no']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $cutting_vals['style_description'] ?>&nbsp;</p></td>
	                            <td width="100"><p><? echo $garments_item[$gmt_id]; ?>&nbsp;</p></td>
								<td width="160"><p>
								<?
								$order_id_arr= array_unique(explode(",",$cutting_vals['order_id'])); 
								$all_po="";
								foreach($order_id_arr as $po_id)
								{
									$all_po.=$po_no_arr[$po_id].",";
								}
								$all_po=chop($all_po,",");
								echo $all_po;
								?>&nbsp;</p></td>
								<td width="100"><p><? echo $color_library[$color_ids]; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $cutting_vals['table_no']; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $batch_nos; ?>&nbsp;</p></td><!--Batch No-->
	                            <td width="70"  align="right"><p><? echo $size_ratio; ?>&nbsp;</p></td>
								<td width="70" align="right"><? echo $cutting_vals['plies'];?></td>
								<td width="70" align="center"><? echo $cut_tot_bundle[$cut_no][$gmt_id][$color_ids]; $total_cut_tot_bundle+=$cut_tot_bundle[$cut_no][$gmt_id][$color_ids]; ?></td>
								
	                            <?
								$row_total=0;
								foreach($sizeId_arr as $key=>$value)
								{
									?>
	                                <td width="80" align="right">
									<? 
										echo number_format($bundle_data_array[$cut_no][$gmt_id][$color_ids][$key],0) ;
										$row_total+= $bundle_data_array[$cut_no][$gmt_id][$color_ids][$key];
										$size_total[$key]+=$bundle_data_array[$cut_no][$gmt_id][$color_ids][$key];
										$totsize_total[$key]=$bundle_data_array[$cut_no][$gmt_id][$color_ids][$key];
									?> 
	                                </td>
	                                <?
								}
								?>
	                            <td width="80" align="right"><? echo number_format($row_total,0); ?> </td>
							</tr>
							<?
							
							 $total_size_ratio+=$size_ratio;
							 $total_plies+=$cutting_vals['plies'];
							 
							  $grand_total_size_ratio+=$size_ratio;
							 $grand_total_plies+=$cutting_vals['plies'];
							$sl++;
						}
						?>
	                   		<tr bgcolor="#F4F3C4">
	                            <td colspan="17" align="right"><strong>Sub Total </strong></td>
	                            <td width="70" align="right"><strong><? echo $totsize_ratio;$totsize_ratio=0;?></strong></td>
	                            <td width="70" align="right"><strong><? echo $totplies_qty;$totplies_qty=0;?></strong></td>
	                            <td width="70"><? //echo $total_cut_tot_bundle;?></td>
	                                
	                                 <?
								$size_row_total=0;$tot_size_total=0;
								foreach($sizeId_arr as $key=>$value)
								{
									?>
	                                <td width="80" align="right"><strong>
									<? 
									$size_row_total=$size_total[$key];
									echo $size_total[$key];$size_total[$key]=0;
									$sub_total_size_qty+=$size_row_total;
									$gt_total_size_qty[$key]+=$size_row_total;//$gt_total_size_qty[$key]=0;
										
									?> 
	                                </strong>
	                                </td>
	                                <?
									//$tot_size_total+=$size_row_total;
								}
								?>
	                            <td width="80" align="right"><strong><? echo number_format($sub_total_size_qty,0);$sub_total_size_qty=0; ?></strong> </td>
	                        </tr>
	                    	<?
						}
					}
					
					?>
	                
	            </tbody>
	            <tfoot>
	                <tr style="font-weight:bold;">
	                    <th colspan="17" align="right"> <strong>GrandTotal:</strong></th>
	                     <th align="right"><? echo $grand_total_size_ratio; ?></th>
	                    <th align="right"><? echo $grand_total_plies; ?></th>
	                    <th></th>
	                   
	                    <?
						$gt_row_total=0;
						foreach($sizeId_arr as $key=>$value)
						{
							?>
							<th align="right"><? echo number_format($gt_total_size_qty[$key],0); ?></th>
							<?
							$gt_row_total+=$gt_total_size_qty[$key];
						}
						?>
	                    <th align="right"><? echo number_format($gt_row_total,0); ?></th>
	                </tr>
	            
	            </tfoot>
	        </table>
	        
	    </div>
	    </div>
		<?
	}
	else if($rept_type==2) //Po wise start here.
	{
			
		
				
		$sql=("SELECT a.id as cut_id, a.entry_form, a.company_id, a.location_id, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.working_company_id,b.order_ids as order_ids, d.order_id as po_id,b.gmt_item_id as gmt_id, b.order_cut_no, b.color_id, b.plies,b.id as dtls_id, c.buyer_name, c.style_ref_no, c.style_description,c.brand_id,c.season_year,c.season_buyer_wise
			FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, wo_po_details_master c,wo_po_break_down e, ppl_cut_lay_bundle d
			WHERE b.mst_id=a.id and a.id=d.mst_id and b.id=d.dtls_id and  e.id=d.order_id and e.job_no_mst=c.job_no and d.order_id is not null AND a.job_no=c.job_no $sql_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1
			 $po_cond order by a.cutting_no,b.color_id ASC ");
		// echo $sql;	
		$sql_res=sql_select($sql);
		

		//$cut_color_arr=array();
		$all_po_id='';
		$subtotal_marker_qty=array();
		foreach($sql_res as $row)
		{
			if($row[csf('po_id')]!="") $all_po_id.=$row[csf('po_id')].",";
			
			$all_cut_id.=$row[csf('cut_id')].",";
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cuting_date'] 		= $row[csf('entry_date')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cutting_no']		= $row[csf('cutting_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['company_id']		= $row[csf('company_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['location_id']		= $row[csf('location_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['working_company']	= $row[csf('working_company_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['table_no']			= $table_arr[$row[csf('table_no')]];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_id']			= $row[csf('order_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_cut_no']		= $row[csf('order_cut_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['job_no']			= $row[csf('job_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['batch_id']			= $row[csf('batch_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['plies']				= $row[csf('plies')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['buyer_name']		= $row[csf('buyer_name')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['brand_id']		= $row[csf('brand_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['season']		= $row[csf('season_buyer_wise')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['season_year']		= $row[csf('season_year')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_ref_no']		= $row[csf('style_ref_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_description']	= $row[csf('style_description')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['lay_fabric_wght']	= $row[csf('lay_fabric_wght')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cad_marker_cons']	= $row[csf('cad_marker_cons')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['marker_qty'] 		= $row[csf('marker_qty')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['gmt_item_id'] 		= $row[csf('gmt_item_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['mst_id'] 			= $row[csf('cut_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['dtls_id'] 			= $row[csf('dtls_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['entry_form'] 		= $row[csf('entry_form')];
		}
		//echo $all_po_id;
		$all_po_id=chop($all_po_id,",");
		$all_cut_id=chop($all_cut_id,",");
		
		
		if($all_po_id=="")
		{
			echo " No Data Found.";
			die;
		}
		
				$po_chnk=array_chunk(array_unique(explode(",",$all_po_id)),1000, true);
				 $po_cond="";
				   $x=0;
				   foreach($po_chnk as $key=> $value)
				   {
				   if($x==0)
				   {
						$po_cond=" and id  in(".implode(",",$value).")"; 
				
				   }
				   else
				   {
						$po_cond.=" or id  in(".implode(",",$value).")";
				
				   }
				   $x++;
				   }
				   
		$po_no_arr=return_library_array( "select id, po_number from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond",'id','po_number');
		//print_r($po_no_arr);
		$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
		$all_cut_id=implode(",",array_unique(explode(",",$all_cut_id)));
		$bundle_array=array();
		$bundle_sql=sql_select("select a.cutting_no, b.color_id,b.gmt_item_id as gmt_id, c.size_id, c.size_qty,b.order_id as po_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in($all_cut_id)");
		$bundle_data_array=array();
		$cut_tot_bundle=array();
		foreach($bundle_sql as $row)
		{
			$bundle_data_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('po_id')]][$row[csf('size_id')]]+=$row[csf('size_qty')];
			$cut_tot_bundle[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('po_id')]]++;
		}
		
		
		$sratio_sql=sql_select("select a.cutting_no, c.color_id, c.size_ratio,b.order_id,b.gmt_item_id as gmt_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_size_dtls c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in($all_cut_id)");
		
		$size_ratio_array=array();
		foreach($sratio_sql as $row)
		{
			//$order_ids=array_unique(explode(",",$row[csf('order_id')]));
			//foreach($order_ids as $po_id)
			//{
				$size_ratio_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('order_id')]]+=$row[csf('size_ratio')];
			//}
			
		}
		$batch_sql=sql_select("select a.cutting_no, c.batch_no,b.order_id,b.color_id,b.gmt_item_id as gmt_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,pro_roll_details c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form!=99 and c.status_active=1 and c.is_deleted=0 and a.id in($all_cut_id)");
		
		$batch_array=array();
		foreach($batch_sql as $row)
		{
					
				$batch_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('order_id')]].=$row[csf('batch_no')].',';
		}
		//print_r($size_ratio_array);
		//print_r($cut_color_arr);die;
		//echo $sql;
		
		ob_start();
		
		$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
		//and po_break_down_id in($all_po_id)
		$sql_query=sql_select("select po_break_down_id, country_type, country_id, size_number_id, plan_cut_qnty, order_quantity, country_ship_date, size_order 
		from wo_po_color_size_breakdown where  status_active=1 and is_deleted=0 and job_no_mst='$job_no' $gmt_item_cond order by size_order");
		
		$sizeId_arr=$size_order_data=$order_dtls_arr=array();
		foreach($sql_query as $row)
		{
			$sizeId_arr[$row[csf('size_number_id')]]=$row[csf("size_number_id")];
			$size_order_data[$row[csf('size_number_id')]]['order_quantity']+=$row[csf('order_quantity')];
			$size_order_data[$row[csf('size_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
		}
		//echo "<pre>";
		//print_r($sizeId_arr);die;
		$col_span=15+count($sizeId_arr);
		$table_width=1840+(count($sizeId_arr)*80)+80;
		$div_width=$table_width+20;
		$i=1; $total_layf_balance=0; $total_markerf_qty=0; $total_sizef_ratio=0; $sizeDataArray=array();$plan_cut_qty=array();
	              //print_r($sizeDataArrayplan);die;            
		?>
	    	<div style="width:<? echo $div_width; ?>px;">
	        <table class="rpt_table" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <caption style="font-size:20px; font-weight:bold;">
	            <?
	            $com_name = str_replace( "'", "", $cbo_company_name );
	            echo $company_arr[$com_name]."<br/>"."Cutting Status Report(PO Wise) ";
	            ?>
	                <div style="color:red; text-align:left; font-size:16px;"></div>
	            </caption>
	           </table>
	       
	            <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	            	<tr>
	                	<td colspan="19" style=" border-color:transparent"></td>
	                    <th>Size</th> <?
						foreach($sizeId_arr as $key=>$value)
						{
							echo '<th>'.$size_arr[$key].'</th>';
						} 
						?>
	                    <th width="80"><b>Total <span style="color:red; font-size:16px;">(Pcs)</span></b></th>
	                   </tr>
		            <tr>
	                <td colspan="19" style=" border-color:transparent"></td>
	                <th>Order QTY</th>
	                <?
						$total_order_qty='';
						foreach($sizeId_arr as $key=>$value)
						{
							
							echo '<td width="80" align="right">'.$size_order_data[$key]['order_quantity'].'</td>';
							$total_order_qty+=$size_order_data[$key]['order_quantity'];
						} 
					 ?>
	                <td align="right"><? echo $total_order_qty; ?></td>
	                </tr>
		            <tr>
	                <td colspan="19" style=" border-color:transparent"></td>
	                <th>Plan Cut</th>
					<?
						$total_plan_qty='';
						foreach($sizeId_arr as $key=>$value)
						{
							echo '<td width="80" align="right">'.$size_order_data[$key]['plan_cut_qnty'].'</td>';
							$total_plan_qty+=$size_order_data[$key]['plan_cut_qnty'];
						} 
					 ?>
	                 <td align="right"><? echo $total_plan_qty; ?></td>
	               </tr>
	                <tr>
	                    <th width="40">Sl</th>
	                    <th width="100">Company Name</th>
	                    <th width="100">Working company</th>
	                    <th width="70">Cutting Date</th>
	                    <th width="100">System Cut No.</th>
	                    <th width="50">Order Cut No.</th>
	                    <th width="100">Buyer Name</th>
	                    <th width="100">Brand Name</th>
	                    <th width="100">Season/Year</th>
	                    <th width="100">Job No</th>
	                    <th width="100">Style Reff</th>
	                    <th width="100">Style Description</th>
	                    <th width="100">Gmts Item</th>
	                    <th width="160">PO No</th>
	                    <th width="100">Color Name</th>
	                    <th width="60">Table No</th>
	                    <th width="70">Batch No</th>
	                    <th width="70">Total Size Ratio</th>
	                    <th width="70">Plies</th>
	                    <th width="70">Total Bandle No</th>
	                    <?
	                  	foreach($sizeId_arr as $key=>$value)
	                    {
	                        echo '<th width="80"></th>';
	                    }
	                    ?>
	                    <th width="80"></th>
	                   
	                </tr>
	        </thead>
	    </table>
	    
	    <div style=" max-height:350px; width:<? echo $div_width; ?>px; overflow-y:scroll;" id="scroll_body">
	        <table class="rpt_table" id="table_body" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tbody>
	            	<? 
					$sl=1;$k=1;$total_size_ratio=0;$total_cut_tot_bundle=0; $grand_total_size_ratio=$grand_total_plies=0;
					$tot_size_total=0;
					foreach($cut_color_arr as $gmt_id=>$gmt_val)
					{
						//$color_subtot_arr['job_ids']=$job_ids;$cut_no
							$totsize_ratio=0;$totplies_qty=0;
							$tot_size_qty=0;
							$size_total=array();	
						foreach($gmt_val as $po_id=>$po_val)
						{
													
							foreach($po_val as $color_ids=>$color_val)
							{
								foreach($color_val as $cut_no=>$cutting_vals)
								{
							
							$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
							
							$size_ratio=$size_ratio_array[$cutting_vals['cutting_no']][$gmt_id][$color_ids][$po_id];
							$total_size_ratio+=$size_ratio;
							$totsize_ratio+=$size_ratio;
							$totplies_qty+=$cutting_vals['plies'];
							$batch_no=rtrim($batch_array[$cut_no][$gmt_id][$color_ids][$po_id],',');
							$batch_nos=implode(",",array_unique(explode(",",$batch_no)));
							if($cutting_vals['entry_form']==289){ $print_path='../cutting_plan/requires/woven_cut_and_lay_ratio_wise_entry_controller.php'; }
							else if($cutting_vals['entry_form']==99){ $print_path='../cutting_plan/requires/cut_and_lay_ratio_wise_entry_controller_urmi.php'; }
							else{ $print_path='#'; }
							?>
							<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
								<td width="40"><? echo $sl; ?></td>
								<td width="100"><p><? echo $company_arr[$cutting_vals['company_id']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $company_arr[$cutting_vals['working_company']]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo change_date_format($cutting_vals['cuting_date']); ?>&nbsp;</p></td>
								<td width="100">
									<!-- <a href="#" onClick="generate_report_lay_chart('<? //echo $cutting_vals['cutting_no']."*".$cutting_vals['job_no']."*".$cutting_vals['working_company']."*".$cutting_vals['location_id']; ?>')">
										<? //echo $cutting_vals['cutting_no']; ?>											
									</a> -->
									<!-- ======================= for amtranet ================ -->
									<a href="#" onClick="fnc_print_bundle('<? echo $cutting_vals['company_id']."*".$cutting_vals['mst_id']."*".$cutting_vals['dtls_id'];?>*Lay and Bundle Information','cut_lay_bundle_print','<?=$print_path;?>')"> 
										<? echo $cutting_vals['cutting_no']; ?>
										
									</a>
								</td>
								<td width="50"><p><? echo $cutting_vals['order_cut_no']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $buyer_arr[$cutting_vals['buyer_name']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $brand_arr[$cutting_vals['brand_id']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $season_arr[$cutting_vals['season']].'-'.substr($cutting_vals['season_year'],2,4); ?>&nbsp;</p></td>
								<td width="100"><p><? echo $cutting_vals['job_no']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $cutting_vals['style_ref_no']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $cutting_vals['style_description'] ?>&nbsp;</p></td>
	                            <td width="100"><p><? echo $garments_item[$gmt_id]; ?>&nbsp;</p></td>
								<td width="160"><p>
								<?
								
								echo $po_no_arr[$po_id];
								?>&nbsp;</p></td>
								<td width="100"><p><? echo $color_library[$color_ids]; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $cutting_vals['table_no']; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $batch_nos; ?>&nbsp;</p></td><!--Batch No-->
	                            <td width="70"  align="right"><p><? echo $size_ratio; ?>&nbsp;</p></td>
								<td width="70" align="right"><? echo $cutting_vals['plies']; $total_plies+=$cutting_vals['plies'];?></td>
								<td width="70" align="center"><? echo $cut_tot_bundle[$cut_no][$gmt_id][$color_ids][$po_id]; $total_cut_tot_bundle+=$cut_tot_bundle[$cut_no][$gmt_id][$color_ids][$po_id]; ?></td>
								
	                            <?
								//$tot_size_qty+=$bundle_data_array[$cut_no][$color_ids][$po_id][$key];
								$row_total=0;//$totsize_total=array();
								foreach($sizeId_arr as $key=>$value)
								{
									?>
	                                <td width="80" align="right">
									<? 
										echo number_format($bundle_data_array[$cut_no][$gmt_id][$color_ids][$po_id][$key],0) ;
										$row_total+= $bundle_data_array[$cut_no][$gmt_id][$color_ids][$po_id][$key];
										$size_total[$key]+=$bundle_data_array[$cut_no][$gmt_id][$color_ids][$po_id][$key];
										$totsize_total[$key]+=$bundle_data_array[$cut_no][$gmt_id][$color_ids][$po_id][$key];
										$totalsize_total[$key]+=$bundle_data_array[$cut_no][$gmt_id][$color_ids][$po_id][$key];
									?> 
	                                </td>
	                                <?
								}
								?>
	                            <td width="80" align="right"><? echo number_format($row_total,0); ?> </td>
							</tr>
							<?
								 $grand_total_size_ratio+=$size_ratio;
							 	$grand_total_plies+=$cutting_vals['plies'];
								$sl++;
							}
							?>
	                     	<tr  bgcolor="#F4F3C4">
	                     		<td width="40"></td>
								<td width="100"><p>&nbsp;</p></td>
								<td width="100"><p>&nbsp;</p></td>
								<td width="70"><p>&nbsp;</p></td>
								<td width="100"><p>&nbsp;</p></td>
								<td width="50"><p>&nbsp;</p></td>
								<td width="100"><p>&nbsp;</p></td>
								<td width="100"><p>&nbsp;</p></td>
								<td width="100"><p>&nbsp;</p></td>
								<td width="100"><p>&nbsp;</p></td>
								<td width="100"><p>&nbsp;</p></td>
								<td width="100"><p>&nbsp;</p></td>
	                            <td width="100"><p>&nbsp;</p></td>
								<td width="160"><p>&nbsp;</p></td>
								<td width="100"><p>&nbsp;</p></td>
								<td width="60"><p>&nbsp;</p></td>
								<td width="70" align="right"><strong>Sub Total</strong></td>
	                            <td width="70"  align="right"><strong><? echo number_format($totsize_ratio,0); ?></strong></td>
								<td width="70" align="right"><strong><? echo $totplies_qty; ?></strong></td>
								<td width="70" align="center"></td>
								
	                            <?
								$size_row_total=$row_total=$sub_total_size_qty=0;$totsize_total=array();
								foreach($sizeId_arr as $key=>$value)
								{
									?>
	                                <td width="80" align="right">
	                                <strong>
									<? 
									$size_row_total=$size_total[$key];
									echo $size_total[$key];$size_total[$key]=0;
									
									?>
	                                </strong>
	                                </td>
	                                <?
									$sub_total_size_qty+=$size_row_total;
									$gt_total_size_qty[$key]+=$size_row_total;
								}
								?>
	                            <td width="80" align="right"><strong><? echo number_format($sub_total_size_qty,0);$sub_total_size_qty=0;?></strong></td>
	                     </tr>
	                    <?
								}
						 }
					}
					
					?>
	               
	            </tbody>
	          
	            	<tfoot>
	                <tr style="font-weight:bold;">
	                    <th colspan="17" align="right"> <strong>GrandTotal:</strong></th>
	                     <th align="right" width="70"><? echo $grand_total_size_ratio; ?></th>
	                    <th align="right" width="70"><? echo $grand_total_plies; ?></th>
	                    <th></th>
	                   
	                    <?
						$gt_row_total=0;
						foreach($sizeId_arr as $key=>$value)
						{
							?>
							<th align="right"><? echo number_format($gt_total_size_qty[$key],0); ?></th>
							<?
							$gt_row_total+=$gt_total_size_qty[$key];
						}
						?>
	                    <th align="right"><? echo number_format($gt_row_total,0); ?></th>
	                </tr>
	            
	            </tfoot>
	        </table>
	        
	    </div>
	    </div>
		<?

	}
	else if($rept_type==3) //Country wise start here.
	{
			
		
		$sql=("SELECT a.id as cut_id, a.entry_form, a.company_id, a.location_id, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.working_company_id,d.order_id as po_id,b.id as dtls_id, d.country_id,b.gmt_item_id as gmt_id, b.order_cut_no, b.color_id, b.plies, c.buyer_name, c.style_ref_no, c.style_description,c.brand_id,c.season_year,c.season_buyer_wise
			FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, wo_po_details_master c,ppl_cut_lay_bundle d,wo_po_break_down e 
			WHERE b.mst_id=a.id and b.id=d.dtls_id and a.id=d.mst_id and e.id=d.order_id and e.job_no_mst=c.job_no  and d.order_id is not null AND a.job_no=c.job_no $sql_cond $po_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1
			order by a.cutting_no,d.country_id,b.color_id ASC ");
		//echo $sql;
		$sql_res=sql_select($sql);
		//$cut_color_arr=array();
		$subtotal_marker_qty=array();
		foreach($sql_res as $row)
		{
			if($row[csf('po_id')]!="") $all_po_id.=$row[csf('po_id')].",";
			
			$all_cut_id.=$row[csf('cut_id')].",";
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cuting_date'] 		= $row[csf('entry_date')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cutting_no']		= $row[csf('cutting_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['company_id']		= $row[csf('company_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['location_id']		= $row[csf('location_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['working_company']	= $row[csf('working_company_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['table_no']			= $table_arr[$row[csf('table_no')]];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['po_id']			.= $row[csf('po_id')].',';
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_cut_no']		= $row[csf('order_cut_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['job_no']			= $row[csf('job_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['batch_id']			= $row[csf('batch_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['plies']				= $row[csf('plies')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['buyer_name']		= $row[csf('buyer_name')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['brand_id']		= $row[csf('brand_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['season_year']		= $row[csf('season_year')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['season']		= $row[csf('season_buyer_wise')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_ref_no']		= $row[csf('style_ref_no')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_description']	= $row[csf('style_description')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['lay_fabric_wght']	= $row[csf('lay_fabric_wght')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cad_marker_cons']	= $row[csf('cad_marker_cons')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['marker_qty'] 		= $row[csf('marker_qty')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['gmt_item_id'] 	= $row[csf('gmt_item_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['mst_id'] 			= $row[csf('cut_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['dtls_id'] 		= $row[csf('dtls_id')];
			$cut_color_arr[$row[csf('gmt_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['entry_form'] 		= $row[csf('entry_form')];
			
		}
		$all_po_id=chop($all_po_id,",");
		$all_cut_id=chop($all_cut_id,",");
		if($all_po_id=="")
		{
			echo " No Data Found.";
			die;
		}
		
				$po_chnk=array_chunk(array_unique(explode(",",$all_po_id)),1000, true);
				 $po_cond="";
				   $x=0;
				   foreach($po_chnk as $key=> $value)
				   {
					   if($x==0)
					   {
							$po_cond=" and id  in(".implode(",",$value).")"; 
					
					   }
					   else
					   {
							$po_cond.=" or id  in(".implode(",",$value).")";
					
					   }
				  	 $x++;
				   }
				   
		$po_no_arr=return_library_array( "select id, po_number from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond",'id','po_number');
		$country_name_arr=return_library_array( "select id, short_name from  lib_country",'id','short_name');
		$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
		$all_cut_id=implode(",",array_unique(explode(",",$all_cut_id)));
		$bundle_array=array();
		$bundle_sql=sql_select("select a.cutting_no, b.color_id,b.gmt_item_id as gmt_id, c.size_id, c.size_qty,c.country_id as country_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in($all_cut_id)");
		$bundle_data_array=array();
		$cut_tot_bundle=array();
		foreach($bundle_sql as $row)
		{
			$bundle_data_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('country_id')]][$row[csf('size_id')]]+=$row[csf('size_qty')];
			$cut_tot_bundle[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('country_id')]]++;
		}
		
		
		$sratio_sql=sql_select("select a.cutting_no, c.color_id,b.gmt_item_id as gmt_id, c.size_ratio,d.country_id as country_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_size_dtls c ,ppl_cut_lay_bundle d where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id  and b.id=d.dtls_id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in($all_cut_id) group by  a.cutting_no,b.gmt_item_id, c.color_id,c.size_ratio ,d.country_id");
		
		$size_ratio_array=array();
		foreach($sratio_sql as $row)
		{
			
				$size_ratio_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('country_id')]]+=$row[csf('size_ratio')];
			
		}
		$batch_sql=sql_select("select a.cutting_no,b.gmt_item_id as gmt_id, c.batch_no,d.country_id as country_id,b.color_id
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,pro_roll_details c,ppl_cut_lay_bundle d  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id  and b.id=d.dtls_id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form!=99 and c.status_active=1 and c.is_deleted=0 and a.id in($all_cut_id) group by  a.cutting_no,b.gmt_item_id, c.batch_no,d.country_id,b.color_id");
		
		$batch_array=array();
		foreach($batch_sql as $row)
		{
			$batch_array[$row[csf('cutting_no')]][$row[csf('gmt_id')]][$row[csf('color_id')]][$row[csf('country_id')]].=$row[csf('batch_no')].',';
		}
		ob_start();
		$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
		//and po_break_down_id in($all_po_id)
		$sql_query=sql_select("select po_break_down_id, country_type, country_id, size_number_id, plan_cut_qnty, order_quantity, country_ship_date, size_order 
		from wo_po_color_size_breakdown where  status_active=1 and is_deleted=0 and job_no_mst='$job_no' $gmt_item_cond order by size_order");
		
		$sizeId_arr=$size_order_data=$order_dtls_arr=array();
		foreach($sql_query as $row)
		{
			$sizeId_arr[$row[csf('size_number_id')]]=$row[csf("size_number_id")];
			$size_order_data[$row[csf('size_number_id')]]['order_quantity']+=$row[csf('order_quantity')];
			$size_order_data[$row[csf('size_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
		}
		//echo "<pre>";
		//print_r($sizeId_arr);die;
		$col_span=15+count($sizeId_arr);
		$table_width=1810+(count($sizeId_arr)*80)+80;
		$div_width=$table_width+20;
		$i=1; $total_layf_balance=0; $total_markerf_qty=0; $total_sizef_ratio=0; $sizeDataArray=array();$plan_cut_qty=array();
	              //print_r($sizeDataArrayplan);die;            
		?>
	    	<div style="width:<? echo $div_width; ?>px;">
	        <table class="rpt_table" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <caption style="font-size:20px; font-weight:bold; text-align:center">
	            <?
	            $com_name = str_replace( "'", "", $cbo_company_name );
	            echo $company_arr[$com_name]."<br/>"."Cutting Status Report(Country Wise) ";
	            ?>
	                <div style="color:red; text-align:left; font-size:16px;"></div>
	            </caption>
	           </table>
	       
	            <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	            	<tr>
	                	<td colspan="20" style=" border-color:transparent"></td>
	                    <th>Size</th> <?
						foreach($sizeId_arr as $key=>$value)
						{
							echo '<th>'.$size_arr[$key].'</th>';
						} 
						?>
	                    <th width="80"><b>Total <span style="color:red; font-size:16px;">(Pcs)</span></b></th>
	                   </tr>
		            <tr>
	                <td colspan="20" style=" border-color:transparent"></td>
	                <th>Order QTY</th>
	                <?
						$total_order_qty='';
						foreach($sizeId_arr as $key=>$value)
						{
							
							echo '<td width="80" style="background:#FFFFFF" align="right">'.$size_order_data[$key]['order_quantity'].'</td>';
							$total_order_qty+=$size_order_data[$key]['order_quantity'];
						} 
					 ?>
	                <td align="right"><? echo $total_order_qty; ?></td>
	                </tr>
		            <tr>
	                <td colspan="20" style=" border-color:transparent" ></td>
	                <th>Plan Cut</th>
					<?
						$total_plan_qty='';
						foreach($sizeId_arr as $key=>$value)
						{
							echo '<td width="80" style="background:#E9F3FF" align="right">'.$size_order_data[$key]['plan_cut_qnty'].'</td>';
							$total_plan_qty+=$size_order_data[$key]['plan_cut_qnty'];
						} 
					 ?>
	                 <td align="right"><? echo $total_plan_qty; ?></td>
	               </tr>
	                <tr>
	                    <th width="40">Sl</th>
	                    <th width="100">Company Name</th>
	                    <th width="100">Working company</th>
	                    <th width="70">Cutting Date</th>
	                    <th width="100">System Cut No.</th>
	                    <th width="50">Order Cut No.</th>
	                    <th width="100">Buyer Name</th>
	                    <th width="100">Brand Name</th>
	                    <th width="100">Season/Year</th>
	                    <th width="100">Job No</th>
	                    <th width="100">Style Reff</th>
	                    <th width="100">Style Description</th>
	                    <th width="100">Gmts Item</th>
	                    <th width="160">PO No</th>
	                    <th width="70">Country</th>
	                    <th width="100">Color Name</th>
	                    <th width="60">Table No</th>
	                    <th width="70">Batch No</th>
	                    <th width="70">Total Size Ratio</th>
	                    <th width="70">Plies</th>
	                    <th width="70">Total Bandle No</th>
	                    <?
	                  	foreach($sizeId_arr as $key=>$value)
	                    {
	                        echo '<th width="80"></th>';
	                    }
	                    ?>
	                    <th width="80"></th>
	                   
	                </tr>
	        </thead>
	    </table>
	    
	    <div style=" max-height:350px; width:<? echo $div_width; ?>px; overflow-y:scroll;" id="scroll_body">
	        <table class="rpt_table" id="table_body" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tbody>
	            	<? 
					$sl=0;$k=1;$total_size_ratio=0;$total_cut_tot_bundle=0;
					/*$color_subtot_arr=array();
					$grand_total_fini_req_qty			= 0;
					$grand_total_fini_rcv_qty			= 0;
					$grand_total_rmg_color_qty			= 0;
					$grand_total_plan_cut_qty			= 0;
					$grand_total_yet_to_cut				= 0;
					$grand_total_lay_fabric_weight		= 0;
					$grand_total_cad_marker_cons		= 0;
					$grand_total_marker_qty				= 0;
					$grand_total_qc_pass_qty			= 0;
					$grand_total_replace_qty			= 0;
					$grand_total_reject_qty				= 0;
					$grand_total_cut_cons_qty			= 0;
					$grand_total_qc_pass_cons_qty		= 0;
					$grand_total_cons_variation_qty		= 0;
					$grand_total_cons_variation_percn	= 0;
					$grand_total_reject_kg				= 0;
					$grand_total_reject_percn			= 0;*/
					//$size_total=array();$subgroup_arr=array();
					foreach($cut_color_arr as $gmt_id=>$gmt_val)
					{
						//$color_subtot_arr['job_ids']=$job_ids;
						$totsize_ratio=0;$totplies_qty=0;
						$tot_size_qty=0;
						$size_total=array();
						foreach($gmt_val as $country_id=>$country_val)
						{							
							
							foreach($country_val as $color_ids=>$color_val)
							{
								foreach($color_val as $cut_no=>$cutting_vals)
								{
							
								
								
							$sl++;
							$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
							
							$size_ratio=$size_ratio_array[$cutting_vals['cutting_no']][$gmt_id][$color_ids][$country_id];
							$total_size_ratio+=$size_ratio;
							
							$totsize_ratio+=$size_ratio;
							$totplies_qty+=$cutting_vals['plies'];
							$batch_no=rtrim($batch_array[$cut_no][$gmt_id][$color_ids][$country_id],',');
							$batch_nos=implode(",",array_unique(explode(",",$batch_no)));

							if($cutting_vals['entry_form']==289){ $print_path='../cutting_plan/requires/woven_cut_and_lay_ratio_wise_entry_controller.php'; }
							else if($cutting_vals['entry_form']==99){ $print_path='../cutting_plan/requires/cut_and_lay_ratio_wise_entry_controller_urmi.php'; }
							else{ $print_path='#'; }
							?>
							<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
								<td width="40"><? echo $sl; ?></td>
								<td width="100"><p><? echo $company_arr[$cutting_vals['company_id']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $company_arr[$cutting_vals['working_company']]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo change_date_format($cutting_vals['cuting_date']); ?>&nbsp;</p></td>
								<td width="100">
									<!-- <a href="#" onClick="generate_report_lay_chart('<? //echo $cutting_vals['cutting_no']."*".$cutting_vals['job_no']."*".$cutting_vals['working_company']."*".$cutting_vals['location_id']; ?>')">
										<? //echo $cutting_vals['cutting_no']; ?>											
									</a> -->
									<!-- ======================= for amtranet ================ -->
									<a href="#" onClick="fnc_print_bundle('<? echo $cutting_vals['company_id']."*".$cutting_vals['mst_id']."*".$cutting_vals['dtls_id'];?>*Lay and Bundle Information','cut_lay_bundle_print','<?=$print_path;?>')"> 
										<? echo $cutting_vals['cutting_no']; ?>
										
									</a>
								</td>
								<td width="50"><p><? echo $cutting_vals['order_cut_no']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $buyer_arr[$cutting_vals['buyer_name']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $brand_arr[$cutting_vals['brand_id']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $season_arr[$cutting_vals['season']].'-'.substr($cutting_vals['season_year'],2,4); ?>&nbsp;</p></td>
								<td width="100"><p><? echo $cutting_vals['job_no']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $cutting_vals['style_ref_no']; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $cutting_vals['style_description'] ?>&nbsp;</p></td>
	                            <td width="100"><p><? echo $garments_item[$gmt_id]; ?>&nbsp;</p></td>
								<td width="160"><p>
								<?
								$po_ids=rtrim($cutting_vals['po_id'],',');
								$order_id_arr= array_unique(explode(",",$po_ids)); 
								$all_po="";
								foreach($order_id_arr as $po_id)
								{
									$all_po.=$po_no_arr[$po_id].",";
								}
								$all_po=chop($all_po,",");
								echo $all_po;
								?>&nbsp;</p></td>
	                            <td width="70"><p><? echo $country_name_arr[$country_id]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $color_library[$color_ids]; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $cutting_vals['table_no']; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $batch_nos; ?>&nbsp;</p></td><!--Batch No-->
	                            <td width="70"  align="right"><p><? echo $size_ratio; ?>&nbsp;</p></td>
								<td width="70" align="right"><? echo $cutting_vals['plies']; $total_plies+=$cutting_vals['plies'];?></td>
								<td width="70" align="center"><? echo $cut_tot_bundle[$cut_no][$gmt_id][$color_ids][$country_id]; $total_cut_tot_bundle+=$cut_tot_bundle[$cut_no][$gmt_id][$color_ids][$country_id]; ?></td>
								
	                            <?
								$row_total=0;
								foreach($sizeId_arr as $key=>$value)
								{
									?>
	                                <td width="80" align="right">
									<? 
										echo number_format($bundle_data_array[$cut_no][$gmt_id][$color_ids][$country_id][$key],0) ;
										$row_total+= $bundle_data_array[$cut_no][$gmt_id][$color_ids][$country_id][$key];
										$size_total[$key]+=$bundle_data_array[$cut_no][$gmt_id][$color_ids][$country_id][$key];
									?> 
	                                </td>
	                                <?
								}
								?>
	                            <td width="80" align="right"><? echo number_format($row_total,0); ?> </td>
							</tr>
							<?
							}
							?>
	                        	<tr bgcolor="#F4F3C4">
	                                <td colspan="18" align="right"><strong>Sub Total</strong></td>
	                                <td width="70" align="right"><strong><? echo number_format($totsize_ratio,0);$totsize_ratio=0;?></strong> </td>
	                                <td width="70" align="right"><strong><? echo $totplies_qty;$totplies_qty=0;?></strong></td>
	                                <td width="70"></td>
	                                
	                                 <?
									$size_row_total=0;
									foreach($sizeId_arr as $key=>$value)
									{
										?>
										<td width="80" align="right">
										<strong>
										<? 
										$size_row_total=$size_total[$key];
										echo $size_total[$key];$size_total[$key]=0;
										
										?> 
										</strong>
										</td>
										<?
										$sub_total_size_qty+=$size_row_total;
										$gt_total_size_qty[$key]+=$size_row_total;
									}
									?>
	                            <td width="80" align="right"><? echo number_format($sub_total_size_qty,0);$sub_total_size_qty=0; ?> </td>
	                            </tr>
	                        <?
								}
						}
					}
					?>
	                
	            	</tbody>
	            	<tfoot>
	                <tr style="font-weight:bold;">
	                    <th colspan="18" align="right"> <strong>GrandTotal:</strong></th>
	                     <th align="right" width="70"><? echo $total_size_ratio; ?></th>
	                    <th align="right" width="70"><? echo $total_plies; ?></th>
	                    <th></th>
	                   
	                    <?
						$gt_row_total=0;
						foreach($sizeId_arr as $key=>$value)
						{
							?>
							<th align="right"><? echo number_format($gt_total_size_qty[$key],0); ?></th>
							<?
							$gt_row_total+=$gt_total_size_qty[$key];
						}
						?>
	                    <th align="right"><? echo number_format($gt_row_total,0); ?></th>
	                </tr>
	            </tfoot>
	        </table>
	        
	    </div>
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

if($action=="cutting_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_cutting_value(strCon ) 
		{
			document.getElementById('hdn_cut_no').value=strCon;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table width="950" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
	            <thead>
	                <tr>                	 
	                    <th width="140">Company name</th>
	                    <th width="130">Cutting No</th>
	                    <th width="130">Job No</th>
	                    <th width="130">Order No</th>
	                    <th width="250">Date Range</th>
	                    <th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
	                </tr>
	            </thead>
	            <tbody>
	                  <tr>                    
	                        <td>
	                              <? 
	                              echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1);
	                             ?>
	                        </td>
	                      
	                        <td align="center" >
	                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes"/>
	                                <input type="hidden" id="hdn_cut_no" name="hdn_cut_no" />
	                        </td>
	                        <td align="center">
	                               <input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:120px"  />
	                        </td>
	                        <td align="center">
	                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
	                        </td>
	                        <td align="center" width="250">
	                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
	                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
	                        </td>
	                        <td align="center">
	                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_cutting_search_list_view', 'search_div', 'cutting_status_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
	                        </td>
	                 </tr>
	        		 <tr>                  
	                        <td align="center" height="40" valign="middle" colspan="6">
	                            <? echo load_month_buttons(1);  ?>
	                        </td>
	                </tr>   
	            </tbody>
	         </tr>         
	      </table> 
	     <div align="center" valign="top" id="search_div"> </div>  
	  </form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_cutting_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
	       {
			      $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		   }
	  if($db_type==2)
	       {
			      $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		   }
	}
	
	$sql_order="select a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,d.order_id,d.color_id,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where  a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.entry_form=76 and c.id=d.order_id $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond order by id";
	$arr=array(2=>$table_arr,4=>$order_no_library,5=>$color_library);
	echo create_list_view("list_view", "Cut No,Year,Table No,Job No,Order NO,Color,Marker Length,Markar Width,Fabric Width,Entry Date","90,50,60,120,120,100,80,80,80,120","950","270",0, $sql_order , "js_set_cutting_value", "cut_num_prefix_no", "", 1, "0,0,table_no,0,order_id,color_id,0,0,0,0,0", $arr, "cut_num_prefix_no,year,table_no,job_no,order_id,color_id,marker_length,marker_width,fabric_width,entry_date", "","setFilterGrid('list_view',-1)") ;
}



?>