<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();	 
}
if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $choosenCompany;  
	echo create_drop_down( "cbo_location_name", 130, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
    extract($_REQUEST);
    $choosenLocation = $choosenLocation;  
	echo create_drop_down( "cbo_floor_name", 130, "SELECT id,floor_name from lib_prod_floor where location_id in( $choosenLocation ) and status_active =1 and is_deleted=0 and production_process in(1,4,5,8,9,10,11,13) group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
	exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $buyer;die;
	?>
	
	<script>
    function js_set_value(id)
    {
		//alert(id);
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
    }
    </script>
    </head>
    <body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" id="selected_id" name="selected_id" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">
                        	 <? 
								//echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								if($buyer>0) $buy_cond=" and a.id=$buyer";
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'cutting_and_input_status_report_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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

if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);
	if($company_id==0)
	{
		echo "Please Select Company Name";
		die;
	}
	//echo $company_id."==".$buyer_id."==".$search_type."==".$search_value."==".$cbo_year;die;
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	else $year_cond="";
	
	if($db_type==2)
	{
		$group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		$group_field="group_concat(distinct b.po_number ) as po_number";
		$year_field="YEAR(a.insert_date)";
	}

	$arr=array (2=>$company_arr,3=>$buyer_arr);
	$sql= "SELECT a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year , $group_field
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date
	order by a.id desc";
	//echo $sql;//die;
	$rows=sql_select($sql);
	?>
    <table width="800" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="120">Buyer</th>
                <th width="50">Year</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
                <th>Po number</th>
                
            </tr>
       </thead>
    </table>
    <div style="max-height:820px; overflow:auto;">
    <table id="table_body2" width="800" border="1" rules="all" class="rpt_table">
     <? $rows=sql_select($sql);
         $i=1;
         foreach($rows as $data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="120"><p><? echo $company_arr[$data[csf('company_name')]]; ?></p></td>
                <td width="120"><p><? echo $buyer_short_library[$data[csf('buyer_name')]]; ?></p></td>
                <td align="center" width="50"><p><? echo $data[csf('year')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
                <td><p><? echo $po_num; ?></p></td>
			</tr>
			<? 
			$i++; 
		} 
		?>
    </table>
    </div>
    <?
	 
	
	exit();
}

//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
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
		</script>
	<?
	extract($_REQUEST);
	//echo $job_no;die;
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$job_cond='';
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	else if($cbo_year!=0)
	{
		if($db_type==0) $job_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
		if($db_type==2) $job_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	
	$sql = "SELECT distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$insert_year as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active in(1,2,3)  $company_name $job_cond  $buyer_name $style_cond";
	//echo $sql;//die;
	echo create_list_view("list_view", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

//ref wise browse------------------------------//
if($action=="ref_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
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
		</script>
	<?
	extract($_REQUEST);
	//echo $job_no;die;
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$job_cond='';
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	else if($cbo_year!=0)
	{
		if($db_type==0) $job_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
		if($db_type==2) $job_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	
	$sql = "SELECT distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,a.grouping,$insert_year as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active in(1,2,3)  $company_name $job_cond  $buyer_name $style_cond";
	// echo $sql;die;
	echo create_list_view("list_view", "Year,Job No,Style Ref,Int Ref,Order Number","50,60,100,100,100,","540","310",0, $sql , "js_set_value", "id,grouping", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,grouping,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

$colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$work_company_name 	= str_replace("'","",$cbo_work_company_name);
	$floor_name 		= str_replace("'","",$cbo_floor_name);
	$floor_group 		= str_replace("'","",$txt_floor_group);
	$location_name 		= str_replace("'","",$cbo_location_name);
	$buyer_name 		= str_replace("'","",$cbo_buyer_name);
	$year 				= str_replace("'","",$cbo_year);
	$job_no 			= str_replace("'","",$txt_job_no);
	$shipping_status 	= str_replace("'","",$cbo_shipping_status);
	$hidden_job_id 		= str_replace("'","",$hidden_job_id);
	$order_no 			= str_replace("'","",$txt_order_no);
	$hidden_order_id 	= str_replace("'","",$hidden_order_id);
	$int_ref_no 		= str_replace("'","",$txt_int_ref_no);
	$production_date 	= str_replace("'","",$txt_production_date);	
	
	$lay_cond 	= "";
	$gmt_prod_cond = "";	
	
	$lay_cond .= ($work_company_name != "") ? " and d.working_company_id in($work_company_name)" : "";
	$lay_cond .= ($floor_name != "") 		? " and d.floor_id in($floor_name)" : "";
	$lay_cond .= ($location_name != "") 	? " and d.location_id in($location_name)" : "";
	$lay_cond .= ($buyer_name != 0) 		? " and a.buyer_name in($buyer_name)" : "";
	$lay_cond .= ($year != 0) 				? " and to_char(a.insert_date,'YYYY') = $year" : "";
	// $lay_cond .= ($job_no != "") 			? " and a.job_no_prefix_num in($job_no)" : "";
	$lay_cond .= ($hidden_job_id != "") 	? " and a.id in($hidden_job_id)" : "";
	// $lay_cond .= ($order_no != "") 		? " and b.po_number in($order_no)" : "";
	$lay_cond .= ($hidden_order_id != "") 	? " and b.id in($hidden_order_id)" : "";
	$lay_cond .= ($int_ref_no != "") 		? " and b.grouping like('%$int_ref_no%')" : "";
	$lay_cond .= ($shipping_status != "") 	? " and b.shipping_status in($shipping_status)" : "";
	//======================================================================================
	$gmt_prod_cond .= ($work_company_name != "")? " and a.serving_company in($work_company_name)" : "";
	$gmt_prod_cond .= ($floor_name != "") 		? " and a.floor_id in($floor_name)" : "";
	$gmt_prod_cond .= ($location_name != "") 	? " and a.location in($location_name)" : "";
	$gmt_prod_cond .= ($buyer_name != 0) 		? " and e.buyer_name in($buyer_name)" : "";
	$gmt_prod_cond .= ($year != 0) 				? " and to_char(e.insert_date,'YYYY') = $year" : "";
	// $gmt_prod_cond .= ($job_no != "") 			? " and e.job_no_prefix_num in($job_no)" : "";
	$gmt_prod_cond .= ($hidden_job_id != "") 	? " and e.id in($hidden_job_id)" : "";
	// $gmt_prod_cond .= ($order_no != "") 		? " and b.po_number in($order_no)" : "";
	$gmt_prod_cond .= ($hidden_order_id != "") 	? " and d.id in($hidden_order_id)" : "";
	$gmt_prod_cond .= ($int_ref_no != "") 		? " and b.grouping like('%$int_ref_no%')" : "";
	$gmt_prod_cond .= ($shipping_status != "") 	? " and d.shipping_status in($shipping_status)" : "";	
	
	if($floor_group && $floor_name=="")
	{
		$floor_group_sql=sql_select("SELECT   id  FROM lib_prod_floor where group_name ='$floor_group' and status_active=1 ");
		$floor_group_arr=array();
		foreach($floor_group_sql as $fl)
		{
			$floor_group_arr[$fl[csf("id")]]=$fl[csf("id")];
		}
		$all_floor_by_group=implode(",",$floor_group_arr);

		$lay_cond.= " and d.floor_id in ($all_floor_by_group) ";
		$gmt_prod_cond.= " and a.floor_id in ($all_floor_by_group) ";	

	}

	/*==========================================================================================/
	/									getting cut and lay data 								/
	/==========================================================================================*/ 
	$sql_lay=" SELECT a.id as JOB_ID,A.BUYER_NAME,A.JOB_NO_PREFIX_NUM,A.STYLE_REF_NO,a.CLIENT_ID,b.id as PO_ID,B.PO_NUMBER,B.GROUPING,b.SHIPING_STATUS,C.item_number_id as ITEM_ID,c.COUNTRY_ID,c.country_ship_date as SHIP_DATE,C.color_number_id as COLOR_ID,sum(c.order_quantity) as ORDER_QUANTITY, d.working_company_id as COMP_ID,d.LOCATION_ID,d.FLOOR_ID,sum(case when d.entry_date=$txt_production_date then f.size_qty else 0 end )  as TODAY_LAY,  sum(f.size_qty )  as TOTAL_LAY
		from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, ppl_cut_lay_mst d, ppl_cut_lay_dtls e, ppl_cut_lay_bundle f
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and d.id=e.mst_id and d.id=f.mst_id and e.id=f.dtls_id and c.country_id=f.country_id and c.item_number_id=e.gmt_item_id and c.color_number_id=e.color_id and b.id=f.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $lay_cond
		group by a.id,a.buyer_name,a.job_no_prefix_num,a.style_ref_no,a.client_id,b.id,b.po_number,b.shiping_status,b.grouping,c.item_number_id,c.country_id,c.country_ship_date,c.color_number_id, d.working_company_id,d.location_id,d.floor_id";  
	// echo $sql_lay;die();
	$lay_res = sql_select($sql_lay);
	$data_array = array();
	$po_id_array = array();
	foreach ($lay_res as $val) 
	{
		$data_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['job_no'] = $val['JOB_NO_PREFIX_NUM'];
		$data_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['style'] = $val['STYLE_REF_NO'];
		$data_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['client_id'] = $val['CLIENT_ID'];
		$data_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['po_number'] = $val['PO_NUMBER'];
		$data_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['grouping'] = $val['GROUPING'];
		$data_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['shiping_status'] = $val['SHIPING_STATUS'];
		$data_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['ship_date'] = $val['SHIP_DATE'];
		$data_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['order_quantity'] += $val['ORDER_QUANTITY'];
		$data_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['today_lay'] += $val['TODAY_LAY'];
		$data_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['total_lay'] += $val['TOTAL_LAY'];
		$po_id_array[$val['PO_ID']] = $val['PO_ID'];
	}
	// echo "<pre>";print_r($data_array);die();
	$poIds = implode(",", $po_id_array);
	$po_arr_cond=array_chunk($po_id_array,1000, true);
	$po_id_cond="";
	$pi=0;
	foreach($po_arr_cond as $key=>$value)
	{
	   	if($pi==0)
	   	{
			$po_id_cond=" and ( f.order_id  in(".implode(",",$value).")"; 		
	   	}
	  	else //po_break_down_id
	   	{
			$po_id_cond.=" or f.order_id  in(".implode(",",$value).")";
		
	   	}
	   	$pi++;
	}	
	$po_id_cond.=" )";
	// echo $po_id_cond;die();

	/*==========================================================================================/
	/									getting cut and lay qty 								/
	/==========================================================================================*/ 
	$sql_lay=" SELECT d.working_company_id as COMP_ID,d.LOCATION_ID,d.FLOOR_ID,f.ORDER_ID,e.gmt_item_id as ITEM_ID,f.COUNTRY_ID,e.color_id as COLOR_ID, sum(case when d.entry_date=$txt_production_date then f.size_qty else 0 end )  as TODAY_LAY,  sum(f.size_qty )  as TOTAL_LAY
		from ppl_cut_lay_mst d, ppl_cut_lay_dtls e, ppl_cut_lay_bundle f
		where  d.id=e.mst_id and d.id=f.mst_id and e.id=f.dtls_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $po_id_cond
		group by d.working_company_id,d.location_id,d.floor_id,f.order_id,e.gmt_item_id,f.country_id,e.color_id";  
	 // echo $sql_lay;die();
	$lay_res = sql_select($sql_lay);
	$lay_qty_array = array();
	foreach ($lay_res as $val) 
	{
		$lay_qty_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['ORDER_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['today_lay'] += $val['TODAY_LAY'];
		$lay_qty_array[$val['COMP_ID']][$val['LOCATION_ID']][$val['FLOOR_ID']][$val['ORDER_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['total_lay'] += $val['TOTAL_LAY'];
	}
	// echo "<pre>";print_r($lay_qty_array);die();
		
	/*==========================================================================================/
	/									getting gmts prod data 									/
	/==========================================================================================*/ 	
		
	$sql_prod=" SELECT a.serving_company as COMP_ID, a.LOCATION,a.FLOOR_ID,e.JOB_NO_PREFIX_NUM, e.BUYER_NAME,e.STYLE_REF_NO,e.CLIENT_ID,e.id as JOB_ID,e.job_no_prefix_num as JOB_NO,d.id as PO_ID,d.PO_NUMBER,d.GROUPING,d.SHIPING_STATUS,c.color_number_id as COLOR_ID, a.production_date, c.po_break_down_id as order_id, c.item_number_id as ITEM_ID, c.COUNTRY_ID,c.country_ship_date as SHIP_DATE,sum(c.order_quantity) as ORDER_QUANTITY,		
		sum(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =1 and a.production_type =1 THEN b.production_qnty  ELSE 0 END) AS TODAY_CUTTING_QNTY,
		sum(CASE WHEN      b.production_type =1 and a.production_type =1 THEN b.production_qnty  ELSE 0 END) AS TOTAL_CUTTING_QNTY,
		sum(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =4 and a.production_type =4 THEN b.production_qnty  ELSE 0 END) AS TODAY_INPUT_QNTY,
		sum(CASE WHEN      b.production_type =4 and a.production_type =4 THEN b.production_qnty  ELSE 0 END) AS TOTAL_INPUT_QNTY	

		from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_break_down d,wo_po_details_master e 
		where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=d.id and c.po_break_down_id=d.id and e.id=d.job_id and e.id=c.job_id and c.item_number_id=a.item_number_id and c.country_id=a.country_id and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $gmt_prod_cond
		group by a.serving_company, a.location,a.floor_id,e.job_no_prefix_num, e.buyer_name,e.style_ref_no,e.client_id,e.id,e.job_no_prefix_num,d.id,d.po_number,d.grouping,d.shiping_status,c.color_number_id, a.production_date, c.po_break_down_id, c.item_number_id, c.country_id,c.country_ship_date";		
	// echo $sql_prod;die;
	$prod_res = sql_select($sql_prod);
	foreach ($prod_res as $val) 
	{
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['job_no'] = $val['JOB_NO_PREFIX_NUM'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['style'] = $val['STYLE_REF_NO'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['client_id'] = $val['CLIENT_ID'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['po_number'] = $val['PO_NUMBER'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['grouping'] = $val['GROUPING'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['shiping_status'] = $val['SHIPING_STATUS'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['ship_date'] = $val['SHIP_DATE'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['order_quantity'] += $val['ORDER_QUANTITY'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['today_cutting'] += $val['TODAY_CUTTING_QNTY'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['total_cutting'] += $val['TOTAL_CUTTING_QNTY'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['today_input'] += $val['TODAY_INPUT_QNTY'];
		$data_array[$val['COMP_ID']][$val['LOCATION']][$val['FLOOR_ID']][$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['total_input'] += $val['TOTAL_INPUT_QNTY'];
		$po_id_array[$val['PO_ID']] = $val['PO_ID'];
	}	
	$poIds = implode(",", $po_id_array);

	/*==========================================================================================/
	/									getting order quantity									/
	/==========================================================================================*/
	$po_id_cond = str_replace("f.order_id", "b.id", $po_id_cond);
	$sql_order=" SELECT b.id as PO_ID,C.item_number_id as ITEM_ID,c.COUNTRY_ID,C.color_number_id as COLOR_ID,sum(c.order_quantity) as ORDER_QUANTITY
		from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 $po_id_cond
		group by b.id,c.item_number_id,c.country_id,c.color_number_id";	
	$order_res = sql_select($sql_order);		
	$order_qty_array = array();
	foreach ($order_res as $val) 
	{
		$order_qty_array[$val['PO_ID']][$val['ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']] = $val['ORDER_QUANTITY'];
	}
	ob_start();
	?>
	<fieldset style="width:1920px;">
		<style type="text/css">
			#table_body tr td{word-break: break-all;word-wrap: break-word;}
		</style>
		<div style="width:1920px;">
			<table width="1920"  cellspacing="0"   >
				<tr class="form_caption" style="border:none;">
					<td colspan="25" align="center" style="border:none;font-size:14px; font-weight:bold" >Cutting And Input Status Report</td>
				</tr>
				<tr style="border:none;">
					<td colspan="25" align="center" style="border:none; font-size:16px; font-weight:bold">
						Working Company Name:<? 
						$cbo_work_company_name_arr=explode(",",$work_company_name);
						$workingCompanyName="";
						foreach ($cbo_work_company_name_arr as $workig_cmp_name)
						{
							$workingCompanyName.= $company_arr[$workig_cmp_name].', '; 
						}
						echo chop($workingCompanyName,',');
						?>                                
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="25" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
					</td>
				</tr>
			</table>
			<br />

			<fieldset style="width:1920px; float:left;">
				<!-- <legend>Report Details Part</legend> -->
				<table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="1900" class="rpt_table" align="left">
					<thead>
						<tr >
							<th width="30" 	rowspan="2">SL</th>
							<th width="100" rowspan="2">Location</th>
							<th width="100" rowspan="2">Floor</th>
							<th width="100" rowspan="2">Buyer</th>
							<th width="100" rowspan="2">Style Ref</th>
							<th width="60" 	rowspan="2">Job No</th>								 
							<th width="100" rowspan="2">Order No</th>
							<th width="100" rowspan="2">Int. Ref.</th>
							<th width="100" rowspan="2">Country</th>
							<th width="70" 	rowspan="2">Country Shipdate</th>
							<th width="100" rowspan="2">Garment Item</th>
							<th width="100" rowspan="2">Color</th>
							<th width="70" 	rowspan="2">Order Qty.</th>
							 
							<th width="210" colspan="3">Lay Qty</th>
							<th width="210" colspan="3">Cutting Qty</th>
							<th width="210" colspan="3">Sewing Input</th>

							<th width="70" rowspan="2">Status</th>
							<th width="70" rowspan="2">Remarks</th>
						</tr>
						<tr>								 
							<th width="70">Today </th>
							<th width="70">Total </th>
							<th width="70">Lay% </th>

							<th width="70">Today </th>
							<th width="70">Total </th>
							<th width="70">Cut% </th>

							<th width="70">Today </th>
							<th width="70">Total </th>
							<th width="70">In% </th>	
						</tr>
					</thead>
				</table>
				<div style="max-height:425px; overflow-y:scroll; width:1920px;" id="scroll_body">
					<table  border="1" class="rpt_table"  width="1900" rules="all" id="table_body" >
						<tbody>
							<?
								$i=1;
								$gr_order_qty 		= 0;
								$gr_today_lay_qty 	= 0;
								$gr_total_lay_qty 	= 0;
								$gr_today_cut_qty 	= 0;
								$gr_total_cut_qty 	= 0;
								$gr_today_in_qty 	= 0;
								$gr_total_in_qty 	= 0;
								foreach ($data_array as $com_id => $comp_data) 
								{
									foreach ($comp_data as $loc_id => $loc_data) 
									{
										foreach ($loc_data as $floor_id => $floor_data) 
										{
											foreach ($floor_data as $buyer_id => $buyer_data) 
											{
												foreach ($buyer_data as $job_id => $job_data) 
												{
													foreach ($job_data as $po_id => $po_data) 
													{
														foreach ($po_data as $item_id => $item_data) 
														{
															foreach ($item_data as $country_id => $country_data) 
															{
																foreach ($country_data as $color_id => $row) 
																{
																	$order_qty = $order_qty_array[$po_id][$item_id][$country_id][$color_id];
																	$today_lay = $lay_qty_array[$com_id][$loc_id][$floor_id][$po_id][$item_id][$country_id][$color_id]['today_lay'];
																	$total_lay = $lay_qty_array[$com_id][$loc_id][$floor_id][$po_id][$item_id][$country_id][$color_id]['total_lay'];
																	if($job_no !="" || $order_no !="" || $int_ref_no !="")
																	{
																		if($today_lay>0 || $total_lay>0 || $row['today_cutting']>0 || $row['today_input']>0)
																		{
																			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
																			?>
																			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
																				<td width="30"><? echo $i;?></td>
																				<td width="100"><? echo $location_library[$loc_id];?></td>
																				<td width="100"><? echo $floor_library[$floor_id];?></td>
																				<td width="100"><? echo $buyer_library[$buyer_id];?></td>
																				<td width="100"><? echo $row['style'];?></td>
																				<td width="60" ><? echo $row['job_no'];?></td>
																				<td width="100"><? echo $row['po_number'];?></td>
																				<td width="100"><? echo $row['grouping'];?></td>
																				<td width="100"><? echo $country_library[$country_id];?></td>
																				<td width="70" >
																					<?
																					 if($row["ship_date"]!="" && $row["ship_date"]!='0000-00-00') echo change_date_format($row["ship_date"]); 
																					 ?>
																				</td>
																				<td width="100"><? echo $garments_item[$item_id];?></td>
																				<td width="100"><? echo $color_Arr_library[$color_id];?></td>
																				<td align="right" width="70" ><? echo number_format($order_qty,0);?></td>
																				<td align="right" width="70" ><? echo number_format($today_lay,0);?></td>
																				<td align="right" width="70" ><? echo number_format($total_lay,0);?></td>
																				<td align="right" width="70" ><? echo number_format(($row['total_lay'] / $row['order_quantity'])*100,2);?></td>
																				<td align="right" width="70" ><? echo number_format($row['today_cutting'],0);?></td>
																				<td align="right" width="70" ><? echo number_format($row['total_cutting'],0);?></td>
																				<td align="right" width="70" ><? echo number_format(($row['total_cutting'] / $row['order_quantity'])*100,2);?></td>
																				<td align="right" width="70" ><? echo number_format($row['today_input'],0);?></td>
																				<td align="right" width="70" ><? echo number_format($row['total_input'],0);?></td>
																				<td align="right" width="70" ><? echo number_format(($row['total_input'] / $row['order_quantity'])*100,2);?></td>
																				<td width="70" ><? echo $shipment_status[$row['shiping_status']];?></td>
																				<td width="70" >
																					<a href="##" onClick="openmypage_remarks_popup('<? echo $com_id; ?>' ,'<? echo $loc_id; ?>' ,'<? echo $floor_id; ?>' ,'<? echo $po_id; ?>' ,'<? echo  $item_id; ?>', '<? echo $country_id; ?>', '<? echo $color_id; ?>', '<? echo $row[job_no]; ?>','remarks_popup')">
																						Remarks
																					</a>
																				</td>
																			</tr>
																			<?
																			$i++;
																			$gr_order_qty 		+= $order_qty;
																			$gr_today_lay_qty 	+= $today_lay;
																			$gr_total_lay_qty 	+= $total_lay;
																			$gr_today_cut_qty 	+= $row['today_cutting'];
																			$gr_total_cut_qty 	+= $row['total_cutting'];
																			$gr_today_in_qty 	+= $row['today_input'];
																			$gr_total_in_qty 	+= $row['total_input'];			
																		}
																	}
																	else
																	{
																		if($today_lay>0 || $row['today_cutting']>0 || $row['today_input']>0)
																		{
																			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
																			?>
																			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
																				<td width="30"><? echo $i;?></td>
																				<td width="100"><? echo $location_library[$loc_id];?></td>
																				<td width="100"><? echo $floor_library[$floor_id];?></td>
																				<td width="100"><? echo $buyer_library[$buyer_id];?></td>
																				<td width="100"><? echo $row['style'];?></td>
																				<td width="60" ><? echo $row['job_no'];?></td>
																				<td width="100"><? echo $row['po_number'];?></td>
																				<td width="100"><? echo $row['grouping'];?></td>
																				<td width="100"><? echo $country_library[$country_id];?></td>
																				<td width="70" >
																					<?
																					 if($row["ship_date"]!="" && $row["ship_date"]!='0000-00-00') echo change_date_format($row["ship_date"]); 
																					 ?>
																				</td>
																				<td width="100"><? echo $garments_item[$item_id];?></td>
																				<td width="100"><? echo $color_Arr_library[$color_id];?></td>
																				<td align="right" width="70" ><? echo number_format($order_qty,0);?></td>
																				<td align="right" width="70" ><? echo number_format($today_lay,0);?></td>
																				<td align="right" width="70" ><? echo number_format($total_lay,0);?></td>
																				<td align="right" width="70" ><? echo number_format(($row['total_lay'] / $row['order_quantity'])*100,2);?></td>
																				<td align="right" width="70" ><? echo number_format($row['today_cutting'],0);?></td>
																				<td align="right" width="70" ><? echo number_format($row['total_cutting'],0);?></td>
																				<td align="right" width="70" ><? echo number_format(($row['total_cutting'] / $row['order_quantity'])*100,2);?></td>
																				<td align="right" width="70" ><? echo number_format($row['today_input'],0);?></td>
																				<td align="right" width="70" ><? echo number_format($row['total_input'],0);?></td>
																				<td align="right" width="70" ><? echo number_format(($row['total_input'] / $row['order_quantity'])*100,2);?></td>
																				<td width="70" ><? echo $shipment_status[$row['shiping_status']];?></td>
																				<td width="70" >
																					<a href="##" onClick="openmypage_remarks_popup('<? echo $com_id; ?>' ,'<? echo $loc_id; ?>' ,'<? echo $floor_id; ?>' ,'<? echo $po_id; ?>' ,'<? echo  $item_id; ?>', '<? echo $country_id; ?>', '<? echo $color_id; ?>', '<? echo $row[job_no]; ?>','remarks_popup')">
																						Remarks
																					</a>
																				</td>
																			</tr>
																			<?
																			$i++;
																			$gr_order_qty 		+= $order_qty;
																			$gr_today_lay_qty 	+= $today_lay;
																			$gr_total_lay_qty 	+= $total_lay;
																			$gr_today_cut_qty 	+= $row['today_cutting'];
																			$gr_total_cut_qty 	+= $row['total_cutting'];
																			$gr_today_in_qty 	+= $row['today_input'];
																			$gr_total_in_qty 	+= $row['total_input'];			
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}

							?>
						</tbody>


					</table> 
				</div>  
				 <table border="1" class="rpt_table"  width="1900" rules="all" style="margin-left: 2px;" align="left" id="">
					<tfoot>
						<tr>
							<th width="30"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="60" ></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="70" ></th>
							<th width="100"></th>
							<th width="100">Grand Total</th>
							<th id="gr_order_qty" width="70" ><? echo number_format($gr_order_qty,0);?></th>
							<th id="gr_today_lay_qty" width="70" ><? echo number_format($gr_today_lay_qty,0);?></th>
							<th id="gr_total_lay_qty" width="70" ><? echo number_format($gr_total_lay_qty,0);?></th>
							<th width="70" ></th>
							<th id="gr_today_cut_qty" width="70" ><? echo number_format($gr_today_cut_qty,0);?></th>
							<th id="gr_total_cut_qty" width="70" ><? echo number_format($gr_total_cut_qty,0);?></th>
							<th width="70" ></th>
							<th id="gr_today_in_qty" width="70" ><? echo number_format($gr_today_in_qty,0);?></th>
							<th id="gr_total_in_qty" width="70" ><? echo number_format($gr_total_in_qty,0);?></th>
							<th width="70" ></th>
							<th width="70" ></th>
							<th width="70" ></th>
						</tr>    
					</tfoot>
				</table>  
			</fieldset>  
		</div>     
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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 
}


 

if($action=="remarks_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
	list($po,$item,$country,$color,$job_no,$wo_company,$location,$floor)= explode("**", $data);
    extract($_REQUEST);
	$col_size_arr=return_library_array( "SELECT size_number_id,size_number_id from  wo_po_color_size_breakdown where po_break_down_id='$po' and color_number_id='$color' and is_deleted=0 ", "size_number_id", "size_number_id"  );
	$size_arr=return_library_array( "select id, size_name from  lib_size", "id", "size_name"  );
	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$line_arr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$resource_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");

		$sql_lay=" SELECT a.entry_date,a.remarks, a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id,c.size_id,   sum(c.size_qty )  as total_lay
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id and a.working_company_id=$wo_company and a.location_id=$location and a.floor_id=$floor  and c.order_id=$po and b.gmt_item_id=$item and b.color_id=$color and c.country_id=$country and a.status_active=1 and b.status_active=1 and c.status_active=1 
		group by a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id,c.size_id, a.entry_date,a.remarks";  
		// echo $sql_lay; 
		
		$sql_lay_result=sql_select($sql_lay);
		$type_arr=array(0=>"Cut and Lay",1=>"Cutting",4=>"Sewing Input",5=>"Sewing Output",11=>"Poly",8=>"Packing & Finishing",22=>"Ex-Factory" );
		 
		

		foreach($sql_lay_result as $row)
		{
			 
 			$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("remarks")];
			$production_data_0[$index][0][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("total_lay")];
			$row_data_0[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
			$row_data_0[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
			$row_data_0[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];
			$row_count[0] =111;
			 
 			 
		}

		 $ex_factory_sql="SELECT a.remarks, a.ex_factory_date as entry_date,m.sys_number, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id,c.size_number_id as size_id,    sum(   b.production_qnty  ) as total_ex_fac
		from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
		where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and a.po_break_down_id ='$po' and a.item_number_id='$item' and c.color_number_id='$color' and a.country_id='$country'
		group by a.remarks,  a.ex_factory_date,m.sys_number, a.po_break_down_id, a.item_number_id,c.size_number_id, a.country_id, c.color_number_id";
		
		
		 
		$ex_factory_sql_result=sql_select($ex_factory_sql);
		foreach($ex_factory_sql_result as $row)
		{
			 
 			$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("sys_number")]."**".$row[csf("remarks")];
			$production_data_22[$index][22][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("total_ex_fac")];
			$row_data_22[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
			$row_data_22[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
			$row_data_22[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];
			$row_data_22[$index][$row[csf("color_id")]]["sys_number"] =$row[csf("sys_number")];
			$row_count[22] =311;
			 
 			 
		} 
		

		 $production_sql=" SELECT a.production_type ,a.floor_id,a.sewing_line, a.production_date as entry_date,a.remarks, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id,c.size_number_id as size_id,sum(b.production_qnty) as qnty 	from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
		where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.serving_company=$wo_company and a.location=$location and a.floor_id=$floor and c.po_break_down_id='$po' and c.color_number_id='$color' and c.country_id='$country'
		group by  a.production_type ,a.floor_id,a.sewing_line, a.production_date  ,a.remarks, c.po_break_down_id  , c.item_number_id, c.country_id, c.color_number_id  ,c.size_number_id   ";
		// echo $production_sql;
		$production_sql_result=sql_select($production_sql);
		 
		foreach($production_sql_result as $row)
		{
			$type=$row[csf("production_type")];
			if($type==1) 
			{
				$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("remarks")];
				$production_data_1[$index][$row[csf("production_type")]][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("qnty")];
				$row_data_1[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
				$row_data_1[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
				$row_data_1[$index][$row[csf("color_id")]]["floor_id"] =$row[csf("floor_id")];
				$row_data_1[$index][$row[csf("color_id")]]["sewing_line"] =$row[csf("sewing_line")];
				$row_data_1[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];

			}

			if($type==4) 
			{
				$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("floor_id")]."**".$row[csf("sewing_line")]."**".$row[csf("remarks")];
				$production_data_4[$index][$row[csf("production_type")]][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("qnty")];
				$row_data_4[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
				$row_data_4[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
				$row_data_4[$index][$row[csf("color_id")]]["floor_id"] =$row[csf("floor_id")];
				$row_data_4[$index][$row[csf("color_id")]]["sewing_line"] =$row[csf("sewing_line")];
				$row_data_4[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];

			}

			if($type==5) 
			{
				$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("floor_id")]."**".$row[csf("sewing_line")]."**".$row[csf("remarks")];
				$production_data_5[$index][$row[csf("production_type")]][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("qnty")];
				$row_data_5[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
				$row_data_5[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
				$row_data_5[$index][$row[csf("color_id")]]["floor_id"] =$row[csf("floor_id")];
				$row_data_5[$index][$row[csf("color_id")]]["sewing_line"] =$row[csf("sewing_line")];
				$row_data_5[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];

			}

			if($type==8) 
			{
				$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("floor_id")]."**".$row[csf("sewing_line")]."**".$row[csf("remarks")];
				$production_data_8[$index][$row[csf("production_type")]][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("qnty")];
				$row_data_8[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
				$row_data_8[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
				$row_data_8[$index][$row[csf("color_id")]]["floor_id"] =$row[csf("floor_id")];
				$row_data_8[$index][$row[csf("color_id")]]["sewing_line"] =$row[csf("sewing_line")];
				$row_data_8[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];

			}

			if($type==11) 
			{
				$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("floor_id")]."**".$row[csf("sewing_line")]."**".$row[csf("remarks")];
				$production_data_11[$index][$row[csf("production_type")]][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("qnty")];
				$row_data_11[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
				$row_data_11[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
				$row_data_11[$index][$row[csf("color_id")]]["floor_id"] =$row[csf("floor_id")];
				$row_data_11[$index][$row[csf("color_id")]]["sewing_line"] =$row[csf("sewing_line")];
				$row_data_11[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];

			}

 			
			$row_count[$row[csf("production_type")]] =$row[csf("production_type")];
			 
 			 
		}
		//print_r($row_count);




	 
     
	?>
    <div id="data_panel" align="center" style="width:100%">

    <?
    foreach($type_arr as $kk =>$v )
    {
    	 
    	if($row_count[$kk] )
    	{
    		if($kk==0){$row_data=$row_data_0;}
    		if($kk==1){$row_data=$row_data_1;}
    		if($kk==22){$row_data=$row_data_22;}
    		if($kk==4){$row_data=$row_data_4;}
    		if($kk==5){$row_data=$row_data_5;}
    		if($kk==8){$row_data=$row_data_8;}
    		if($kk==11){$row_data=$row_data_11;}




		    ?>
		     
				 
		    	<div id="data_panel" align="" style="width:100%">
		    		<label> <strong><? echo $v;?> </strong><label/>

		    			<table width="" align="center" border="1" rules="all" class="rpt_table" >
		    				<thead>
		    					<tr>
		    						<th width="30">SL</th>
		    						<th width="70">Date</th>
		    						<?
		    						if($kk==22)
		    						{
		    							?>
		    							<th width="100">Challan</th>

		    							<?
		    						}

		    						?>
		    						<th width="70">Color</th>

		    						<?
		    						foreach($col_size_arr as $key=>$value)
		    						{

		    							?>
		    							<th width="40"><? echo $size_arr[$value];?></th>
		    							<?

		    						}
		    						?>
		    						<th width="80">Production Qnty</th>
		    						<?
		    						if($kk>2 && $kk!=22 )
		    						{
		    							?>
		    							<th width="80">Floor</th>
		    							<th width="80">Line No</th>

		    							<?
		    						}
		    						?>
		    						<th width="80">Remarks</th>

		    					</tr>
		    				</thead>
		    				<tbody>
		    					<?
		    					$k=1;
		    					foreach($row_data as $key=>$index_val)
		    					{
		    						foreach($index_val as $color_id=>$row)
		    						{
		    							?>
		    							<tr>

		    								<td align="center" width="30"><? echo $k++;?></td>
		    								<td align="center" width="70"><? echo $row["dates"];?></td>
		    								<?
		    								if($kk==22)
		    								{
		    									?>
		    									<td width="100"><? echo $row["sys_number"];?></td>

		    									<?
		    								}

		    								?>

		    								<td align="center" width="70"><? echo $color_arr[$color_id];?></td>

		    								<?
		    								$index=$row["order_id"]."**".$row["dates"]."**".$row["remarks"];
		    								if($kk>2 && $kk!=22)$index=$row["order_id"]."**".$row["dates"]."**".$row["floor_id"]."**".$row["sewing_line"]."**".$row["remarks"];
		    								if($kk==22)$index=$row["order_id"]."**".$row["dates"]."**".$row["sys_number"]."**".$row["remarks"];
		    								//if($kk==1)$index=$row["order_id"]."**".$row["dates"]."**".$row[csf("floor_id")]."**0"."**".$row["remarks"];

		    								$tot=0;
		    								foreach($col_size_arr as $key=>$value)
		    								{
		    									if($kk==0){$row_val=$production_data_0[$index][$kk][$color_id][$key];}
		    									if($kk==1){$row_val=$production_data_1[$index][$kk][$color_id][$key];}
		    									if($kk==22){$row_val=$production_data_22[$index][$kk][$color_id][$key];}
		    									if($kk==4){$row_val=$production_data_4[$index][$kk][$color_id][$key];}
		    									if($kk==5){$row_val=$production_data_5[$index][$kk][$color_id][$key];}
		    									if($kk==8){$row_val=$production_data_8[$index][$kk][$color_id][$key];}
		    									if($kk==11){$row_val=$production_data_11[$index][$kk][$color_id][$key];}


		    									?>
		    									<td align="center" width="40"><? echo $row_val;?></td>
		    									<?
		    									$tot+=$row_val;

		    								}
		    								?>
		    								<td align="center" width="80"><? echo $tot;?></td>
		    								<?
		    								if($kk>2 && $kk!=22)
		    								{
		    									?>
		    									<td width="80"><? echo $floor_arr[$row["floor_id"]];?></td>
		    									<td width="80">
		    									<?  
		    									  $lines=explode(",", $resource_arr[$row["sewing_line"]]);
		    									  $line_name="";
		    									  foreach($lines as $line_val)
		    									  {
		    									  	if($line_name=="")$line_name.=$line_arr[$line_val];
		    									  	else $line_name.=','.$line_arr[$line_val];
		    									  }
		    									  echo $line_name;
		    									  ?>
		    									  	
		    									  </td>

		    									<?
		    								}
		    								?>
		    								<td align="center" width="80"><? echo $row["remarks"];?></td>

		    							</tr>
		    							<?


		    						}
		    					}

		    					?>

		    				</tbody>
		    			</table>


		    	</div>
		    	 
		    	</br>
			 <?
		}
	}
}

 
?>