<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date wise Knitting Report Controller
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	15-03-2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="openJobNoPopup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			$("#hide_style_no").val(splitData[2]); 
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
                </thead>
                <tbody>
                	<tr class="general">
                        <td><? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 ); ?></td>                 
                        <td>	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td id="search_by_td"><input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" /></td> 	
                        <td><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'date_wise_knitting_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" /></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and buyer_name=$data[1]";
	
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
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by id Desc";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end

if($action=="order_no_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() 
    	{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($('#tr_' + i).is(':visible'))
				{
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
			}
		}
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
			var splitSTR = strCon.split("_");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_po').val( id );
			$('#txt_selected_style').val( name ); 
			$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$w_company=str_replace("'","",$w_company);
	$lc_company=str_replace("'","",$lc_company);
	$job_year=str_replace("'","",$job_year);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	
	if($lc_company!=0) $lc_company_cond="and b.company_name=$lc_company"; else $lc_company_cond="";
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($job_no!="") $job_no_cond="and b.job_no_prefix_num=$job_no"; else $job_no_cond="";
	if($db_type==0) $select_date=" year(b.insert_date)"; else if($db_type==2) $select_date=" to_char(b.insert_date,'YYYY')";
	
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $lc_company_cond $buyer_cond $job_no_cond and a.status_active in(1,2,3) and b.status_active=1  and b.garments_nature=100 order by a.id desc"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Year,Job No,Style Ref No,Order NO","70,50,150,150","500","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	

	echo "<input type='hidden' id='txt_selected_po' />";
	echo "<input type='hidden' id='txt_selected_style' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}

if($action=="lotratio_popup")
{
  	echo load_html_head_contents("Lot Ratio Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
		<script>
			function js_set_cutting_value(strCon ) 
			{
				document.getElementById('hide_cutno').value=strCon;
				parent.emailwindow.hide();
			}
	    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="140">Company name</th>
                    <th width="90">Lot Ratio No</th>
                    <th width="100">Style Ref.</th>
                    <th width="90">Job No</th>
                    <th width="250">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">                    
                    <td><? echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1); ?></td>
                    
                    <td>
                        <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:80px"  class="text_boxes_numeric"/>
                        <input type="hidden" id="hide_cutno" name="hide_cutno" />
                    </td>
                    <td><input name="txt_style_search" id="txt_style_search" class="text_boxes" style="width:90px" /></td>
                    <td><input name="txt_job_search" id="txt_job_search" class="text_boxes_numeric" style="width:80px" /></td>
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                    </td>
                    <td>
                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_style_search').value, 'create_cutting_search_list_view', 'search_div', 'date_wise_knitting_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
                </tr>
                <tr>                  
                	<td align="center" valign="middle" colspan="6"><? echo load_month_buttons(1);  ?></td>
                </tr>   
            </tbody>
        </table> 
        <div align="center" valign="top" id="search_div"> </div>  
    </form>
    </div>    
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
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
	$style_serch_no= $ex_data[6];

    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$style_serch_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no  like '%".$style_serch_no."%' ";
	
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
	
	$sql_order="select a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.cad_marker_cons, a.marker_width, a.fabric_width,b.style_ref_no,c.color_id, c.marker_qty, c.order_cut_no,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,ppl_cut_lay_dtls c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.mst_id=a.id and a.job_no=b.job_no and a.entry_form=253 $conpany_cond $cut_cond $job_cond $sql_cond $style_cond order by id Desc";
	//echo $sql_order;die;
	$table_no_arr=return_library_array( "select id,table_no from lib_cutting_table",'id','table_no');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	
	$arr=array(5=>$color_arr);//,4=>$order_number_arr,5=>$color_arr,Order NO,Color
	echo create_list_view("list_view", "System No,Year,Order Cut No,Job No,Style Ref.,Color,Ratio Qty,Cons/Dzn(Lbs),Entry Date","60,50,60,90,140,200,80,90,80","950","270",0, $sql_order , "js_set_cutting_value", "cut_num_prefix_no", "", 1, "0,0,0,0,0,color_id,0,0,0,0", $arr, "cut_num_prefix_no,year,order_cut_no,job_no,style_ref_no,color_id,marker_qty,cad_marker_cons,entry_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,1,2,3") ;
	exit();
}

if($action=="report_generate") 
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr 		= return_library_array("select id,short_name from lib_buyer","id","short_name");
	$colorArr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$sizeArr 		= return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor where company_id=$cbo_company_id and production_process=5 
	order by floor_name","id","floor_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	// ================================= GETTING FORM DATA ====================================
	$lc_company_id 		= str_replace("'","",$cbo_company_id);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$job_no 			= str_replace("'","",$txt_job_no);
	$style_ref_no 		= str_replace("'","",$txt_style_ref_no);
	$order_no 			= str_replace("'","",$txt_order_no);
	$order_id 			= str_replace("'","",$hiden_order_id);
	$report_title 		= str_replace("'","",$report_title);
	$txt_lotratio_no 	= str_replace("'","",$txt_lotratio_no);
	$txt_bundle_no 		= str_replace("'","",$txt_bundle_no);
	$txt_qr_no 		 	= str_replace("'","",$txt_qr_no);	
	$txt_date_from		= str_replace("'","",$txt_date_from);
	$txt_date_to		= str_replace("'","",$txt_date_to);
	$cbo_wo_company_name= str_replace("'","",$cbo_wo_company_name);
	$cbo_ship_status	= str_replace("'","",$cbo_ship_status);
	$cbo_pro_process	= str_replace("'","",$cbo_pro_process);

	if($cbo_pro_process==1){
		$plan_qty_name="Plan all Qty";
		$qty_name="all Qty";
		$production_type="and a.production_type in(1,3,4,5,8,111,112,114,67)";
	}else if($cbo_pro_process==1984){
				$plan_qty_name="Plan Knit Qty";
				$qty_name="Knit Qty";
				$production_type= "and a.production_type=1";
			
	}else if($cbo_pro_process==1986){
				$plan_qty_name="Plan Linking Qty";
					$qty_name="Linking Qty";
					$production_type= "and a.production_type=4";
					$entry_page="";
	}else if($cbo_pro_process==2013){
				$plan_qty_name="Plan Trimming Qty";
					$qty_name="Trimming Qty";
					$production_type= "and a.production_type=111";
					$entry_page="";
	}else if($cbo_pro_process==2018){
				$plan_qty_name="Plan Mending Qty";
					$qty_name="Mending Qty";
					$production_type= "and a.production_type=112";
					$entry_page="";
	}else if($cbo_pro_process==1987){
				$plan_qty_name="Plan Wash Qty";
					$qty_name="Wash Qty";
					$production_type= "and a.production_type=3";
					$production_type2= "and c.production_type=3";
					$entry_page="";
					$emble_name="and a.embel_name=3";
	}else if($cbo_pro_process==1988){
				$plan_qty_name="Plan Attachment Qty";
					$qty_name="Attachment Qty";
					$production_type= "and a.production_type=11";
					$entry_page="";
	}else if($cbo_pro_process==1989){
				$plan_qty_name="Plan Sewing Qty";
					$qty_name="Sewing Qty";
					$production_type= "and a.production_type=5";
					$entry_page="";
	}else if($cbo_pro_process==2020){
				$plan_qty_name="Plan PQC Qty";
					$qty_name="PQC Qty";
					$production_type= "and a.production_type=114";
					$entry_page="";
	}else if($cbo_pro_process==1548){
				$plan_qty_name="Plan Iron Qty";
					$qty_name="Iron Qty";
					 $production_type= "and a.production_type=67";
					 $entry_page="";
	}else if($cbo_pro_process==1550){
				$plan_qty_name="Plan Packing/Finishing Qty";
					$qty_name="Packing/Finishing Qty";
					$production_type= "and a.production_type=8";
					$entry_page="";
	}


   $ship_status_cond="";
    if($cbo_ship_status==1) $ship_status_cond="and e.shiping_status in (1,2)"; else if($cbo_ship_status==2) $ship_status_cond="and e.shiping_status in (3)";
    

	if ($txt_date_from != "" && $txt_date_to != "") {
		if ($db_type == 0) {
			$date_cond = " and g.delivery_date between '" . change_date_format(trim($txt_date_from), "yyyy-mm-dd") . "' and '" . change_date_format(trim($txt_date_to), "yyyy-mm-dd") . "'";
			$date_cond2 = " and a.delivery_date between '" . change_date_format(trim($txt_date_from), "yyyy-mm-dd") . "' and '" . change_date_format(trim($txt_date_to), "yyyy-mm-dd") . "'";
			$date_cond3 = " and a.production_date '" . change_date_format(trim($txt_date_from), "yyyy-mm-dd") . "' and '" . change_date_format(trim($txt_date_to), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and g.delivery_date between '" . change_date_format(trim($txt_date_from), '', '', 1) . "' and '" . change_date_format(trim($txt_date_to), '', '', 1) . "'";
			$date_cond2 = " and a.delivery_date between '" . change_date_format(trim($txt_date_from), '', '', 1) . "' and '" . change_date_format(trim($txt_date_to), '', '', 1) . "'";
			$date_cond3 = " and a.production_date between '" . change_date_format(trim($txt_date_from), '', '', 1) . "' and '" . change_date_format(trim($txt_date_to), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
		$date_cond2 = "";
		$date_cond3 = "";
	}
	//******************************************* MAKE QUERY CONDITION ************************************************
	
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$buyer_id";
	
	if($lc_company_id==0) $companyCond=""; else $companyCond="and f.company_name=$lc_company_id";
	if($job_no==""){ $jobCond="";$jobCond2="";} else{ $jobCond="and f.job_no_prefix_num='$job_no'";$jobCond2="and f.job_no_prefix_num='$job_no'";};
	if($style_ref_no=="") $styleCond=""; else $styleCond="and f.style_ref_no ='$style_ref_no'";
	if($order_id =="" && $order_no !="") $poCond= " and b.po_number ='$order_no'";
	if($order_id !="")
	{
		$po_id_arr = explode(",", $order_id);
		if(count($po_id_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_arr, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") $po_ids_cond.=" and ( b.id in ($imp_ids) ";
	     		else $po_ids_cond.=" or b.id in ($imp_ids) ";
	     	}
	     	$po_ids_cond.=" )";
	    }
	    else $po_ids_cond= " and b.id in($order_id) ";
	}
	if($txt_lotratio_no!="") $lotratioCond="and f.cut_num_prefix_no='$txt_lotratio_no'"; else $lotratioCond="";
	if($txt_bundle_no!="") $bundleNoCond="and c.bundle_no='$txt_bundle_no'"; else $bundleNoCond="";
	if($txt_qr_no!="") $qcNoCond="and c.barcode_no='$txt_qr_no'"; else $qcNoCond="";
	
	// echo $sql_cond;die();
	ob_start();
	?>
   
    <?
	
	
	
	
	if($db_type==0) $year_field="YEAR(a.insert_date)"; else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";


if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and a.job_no_prefix_num=$job_no";
if ($style_ref_no =="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='$style_ref_no'";

if($lc_company_id==0) $company_id=""; else $company_id="and a.company_name=$lc_company_id";
 $sql_3="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order, d.color_order

 FROM wo_po_details_master a
 LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
 AND c.is_deleted =0
 AND c.status_active =1
 LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
 AND c.id = d.po_break_down_id
 AND d.is_deleted =0
 AND d.status_active =1
 WHERE
 a.is_deleted =0
 AND a.status_active =1
$company_id $job_num_mst $style_ref_cond  order by d.size_order, a.job_no, c.id, d.country_ship_date,d.color_order";
$sql_res_3 = sql_select($sql_3);
$po_color_qnty_array=array();
$po_color_size_qnty_array=array();
foreach($sql_res_3 as $rows){
	$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	$po_color_size_qnty_array[$rows[csf('job_no_prefix_num')]][$rows[csf('po_id')]][$rows[csf('item_number_id')]][$rows[csf('country_id')]] [$rows[csf('color_number_id')]][$rows[csf('size_number_id')]]=$rows[csf('order_quantity')];
	$size_array[$rows[csf("job_no")]][$rows[csf("size_order")]]=$rows[csf("size_number_id")];
	$color_array[$rows[csf("job_no")]][$rows[csf("color_order")]]=$rows[csf("color_number_id")];
	
	$iron_color_size_qty[$rows[csf("job_no")]][$rows[csf("color_order")]][$rows[csf("size_order")]]=$rows[csf("id")];
	$job_color_size_qty[$rows[csf("job_no")]][$rows[csf("color_order")]][$rows[csf("size_order")]] +=$rows[csf("plan_cut_qnty")];
}

$sql_iron="select a.id, a.delivery_mst_id, a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id, b.production_qnty, b.alter_qty, b.spot_qty, b.reject_qty, b.re_production_qty,c.job_no from pro_garments_production_mst a, pro_garments_production_dtls b, pro_gmts_delivery_mst c where  a.id=b.mst_id and c.id=b.delivery_mst_id and a.status_active=1 $entry_page  $date_cond3 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
$sql_iron_data = sql_select($sql_iron);

foreach($sql_iron_data as $row){

	$iron_data_arr[$row[csf("job_no")]][$row[csf("color_size_break_down_id")]] +=$row[csf("production_qnty")];
}

// echo $proddate_sql;


	 $proddate_sql="SELECT $year_field as year,f.job_no,a.id as pro_id, d.item_number_id, d.size_number_id, d.color_number_id, c.production_qnty as production_qnty,d.color_order,d.size_order, c.color_size_break_down_id,a.production_type
	 from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where a.id=c.mst_id $companyCond $jobCond $qcNoCond $styleCond and  c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no $production_type $emble_name $production_type2 $entry_page and f.garments_nature=100  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $date_cond3 order by f.job_no desc";

// echo $proddate_sql;
	$receive_qty_data = sql_select($proddate_sql);
	$iron=0;
	foreach($receive_qty_data as $row){
		
		$date_production_qty[$row[csf("job_no")]][$row[csf("color_order")]][$row[csf("size_order")]] +=$row[csf("production_qnty")];
		$pro_type_wise_qty[$row[csf("job_no")]][$row[csf("production_type")]][$row[csf("color_order")]][$row[csf("size_order")]] +=$row[csf("production_qnty")];
	
	
		 $date_production_iron_qty[$row[csf("job_no")]][$row[csf("color_order")]][$row[csf("size_order")]] +=$iron_data_arr[$row[csf("job_no")]][$row[csf("color_size_break_down_id")]];;
		 
	}


	if($db_type==2){ $year=" extract(year from a.insert_date) as year";}else  if($db_type==0){$year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}

	if($lc_company_id==0) $company_id=""; else $company_id="and a.company_id=$lc_company_id";
	

	$job_wise_sql="SELECT f.job_no_prefix_num,$year_field as year, f.buyer_name,f.job_no, f.style_ref_no, d.size_number_id, d.color_number_id,d.color_order,d.size_order,a.id as pro_id, d.item_number_id,a.production_type,a.entry_form,a.production_type
	from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where a.id=c.mst_id $companyCond $jobCond $qcNoCond $styleCond $entry_page and  c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no $production_type $emble_name $production_type2  and f.garments_nature=100  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $date_cond3 order by f.job_no desc";

//echo $job_wise_sql;
//  echo "<pre>";
//   print_r($pro_type_wise_qty);
	$job_wise_data = sql_select($job_wise_sql);

	$job_no_arr=array();
	foreach($job_wise_data as $row){
		$production_type_arr[$row[csf("production_type")]]["production_type"]=$row[csf("production_type")];
		$production_job_arr[$row[csf("production_type")]][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
		$job_no_arr[$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
	
		$buyer_name_arr[$row[csf("job_no")]]=$row[csf("buyer_name")];
		$style_ref_no_arr[$row[csf("job_no")]]=$row[csf("style_ref_no")];	
		$job_no_arr[$row[csf("job_no")]][$row[csf("color_order")]][$row[csf("size_order")]]["pro_id"]=$row[csf("pro_id")];	
		$date_iron_pro_id_arr[$row[csf("job_no")]][$row[csf("pro_id")]]['job_id']=$row[csf("pro_id")];
	}
	    
	// echo "<pre>";
 	// print_r($production_job_arr);
	?>
		<div style="margin-bottom:5px">  
				<table align='right'  width="1500" cellspacing="0">
					<tr class="form_caption" style="border:none;">
						<td colspan="26" align="center" ><strong style="font-size: 19px"><u><?=$companyArr[$lc_company_id]; ?></u></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="26" align="center"><strong style="font-size: 17px"><?=$report_title; ?></strong></td>
					</tr>		        
				</table>				
		</div>
		<br>

	<?
	$i=1;


	foreach($production_type_arr as $pro_id=>$pro_data){
		$old_caption='';
		foreach($production_job_arr[$pro_id] as $job_id=>$job_data){

				$rowspan=3*count($color_array[$job_id]);		
				$colspan=1*count($size_array[$job_id]);
				if($pro_data['production_type']==1){
					$plan_qty_name="Plan Knit Qty";
					$qty_name="Knit Qty";
					$caption="Knit ";
					
				}else if($pro_data['production_type']==4){
					$plan_qty_name="Plan Linking Qty";
					$qty_name=" Linking Qty";
					$caption="Linking ";
				}else if($pro_data['production_type']==111){
					$plan_qty_name="Plan Trimming Qty";
					$qty_name=" Trimming Qty";
					$caption="Trimming";
				}else if($pro_data['production_type']==112){
					$plan_qty_name="Plan Mending Qty";
					$qty_name=" Mending Qty";
					$caption="Mending";
				}else if($pro_data['production_type']==3){
					$plan_qty_name="Plan Wash Qty";
					$qty_name=" Wash Qty";
					$caption="Wash";
				}else if($pro_data['production_type']==11){
					$plan_qty_name="Plan Attachment Qty";
					$qty_name=" Attachment Qty";
					$caption="Attachment";
				}else if($pro_data['production_type']==5){
					$plan_qty_name="Plan Sewing Qty";
					$qty_name=" Sewing Qty";
					$caption="Sewing";
				}else if($pro_data['production_type']==114){
					$plan_qty_name="Plan PQC Qty";
					$qty_name=" PQC Qty";
					$caption="PQC";
				}else if($pro_data['production_type']==8){
					$plan_qty_name="Plan Packing/Finishing Qty";
					$qty_name=" Packing/Finishing Qty";
					$caption="Packing/Finishing";
				}else if($pro_data['production_type']==67 ){
					$plan_qty_name="Plan Iron Qty";
					$qty_name=" Iron Qty";
					$caption="Iron";
				}
		
	?>			
	<div>
	<div style="margin:5px">  
				<table align='left'  width="1500" cellspacing="0">
					
					<tr class="form_caption" style="border:none;">
						<td colspan="26" align="left"><strong style="font-size: 17px"><?
					
						if($old_caption !==$caption){
							echo $caption;$old_caption=$caption;
						}
						
						?></strong></td>
					</tr>		        
				</table>				
		</div>
		<br>
        <table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" width="1430">
	
            <thead style="font-size:13px">
                <tr>					
                    <th width="70" rowspan="2">Buyer</th>
                    <th width="85" rowspan="2">Style</th>
                    <th width="80" rowspan="2">Job</th>                  
                    <th width="80" rowspan="2">Order Qty. (Pcs)</th>                   
                    <th width="85" rowspan="2">Color</th>
					<th width="85" rowspan="2">Color Wise Size Qty</th>
					<th width="85" colspan="<?=$colspan;?>">size</th>
					<th width="60" rowspan="2">Total Qty</th>    
				</tr>
				<tr>
			   		 <?foreach($size_array[$job_id] as $key=>$value)
						{
							if($value !=""){?><th width="60"><? echo $itemSizeArr[$value];?></th><?}
						}
					 ?>     
                     
                </tr>
            </thead>
        </table>

		<div style="width:1460px; overflow-y: scroll; max-height:350px" id="scroll_body">
		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="1430" id="table_body_5">
		<?
		
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($i==4) $colortd="red"; else $colortd="";
			
			?>
			  <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_1nd<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_1nd<?=$i; ?>" style="font-size:13px">
			
                <td width="105" style="font-size:13px"rowspan="<?= $rowspan ?>" align="center" style="word-break:break-all"><br><?=$buyerArr[$buyer_name_arr[$job_id]]; ?></td>
				<td width="120" style="font-size:13px"rowspan="<?=$rowspan ?>" align="center"  style="word-break:break-all"><br><?=$style_ref_no_arr[$job_id]; ?></td>
                <td width="120" style="font-size:13px"rowspan="<?= $rowspan ?>" align="center" ><br><?=$job_no_arr[$job_id]["job_no"]; ?></td>                    
                <td width="120" style="font-size:13px"rowspan="<?= $rowspan ?>" align="center" ><br>
				<?
					foreach($color_array[$job_id] as $color_id=>$val){	
						foreach($job_color_size_qty[$job_id][$color_id] as $key=>$value)
						{
						if($value !=""){ $sum_order_qty+=$value;}
						}
					}
					echo $sum_order_qty;$sum_order_qty='';
				?>  
				
				</td>  
				<?					
				foreach($color_array[$job_id] as $color_id=>$val){	
				?>
				<td width='125' style="font-size:13px"align='center' style='word-break:break-all;'rowspan='3'><?= $colorArr[$val];?></td>
				<td width='125' style="font-size:13px"align='center' style='word-break:break-all;'><?=$plan_qty_name;
	
				?></td>						 
				<?foreach($job_color_size_qty[$job_id][$color_id] as $key=>$value)
					{
					if($value !=""){?><td width="90" style="font-size:13px" align='center' style='word-break:break-all;'><? echo $value;$sum_order_value+=$value;?></td><?}
					}
					?>     						  
				<td width='90' style="font-size:13px"align='center' style='word-break:break-all;'><?=$sum_order_value;$total_order+=$sum_order_value;$sum_order_value=0?></td>
			  </tr>
			  <tr>
				<td width='90'style="font-size:13px"align='center' style='word-break:break-all;'><?=$qty_name;?></td>

					 <? foreach($job_color_size_qty[$job_id][$color_id] as $key=>$value)
							{
								if($cbo_pro_process==1548){
									if($value !=""){?><td width="90"style="font-size:13px"  align='center' style='word-break:break-all;'><? 
										
										echo	$pro_type_wise_qty[$job_id][$pro_id][$color_id][$key];
										//  echo $date_production_qty[$job_id][$color_id][$key];;
										// $sum_rcv_iron_value +=$date_production_iron_qty[$job_id][$color_id][$key];
										$sum_rcv_value +=$pro_type_wise_qty[$job_id][$pro_id][$color_id][$key];
										
										?></td><?}
								}else{

									if($value !=""){?><td width="90" align='center'style="font-size:13px"  style='word-break:break-all;'><? 
										// echo $date_production_qty[$job_id][$color_id][$key];
										echo	$pro_type_wise_qty[$job_id][$pro_id][$color_id][$key];
										$sum_rcv_value +=$pro_type_wise_qty[$job_id][$pro_id][$color_id][$key];?></td><?}
								}							
							}
					 ?>   
							<td width='90'style="font-size:13px" align='center' style='word-break:break-all;'><?
									echo $sum_rcv_value;$total_rcv+=$sum_rcv_value;$sum_rcv_value=0
							?></td>
						 </tr>
			 <tr>
				  <td width='92' align='center'style="font-size:13px"  style='word-break:break-all;'>Excess <?=$qty_name;?></td>
				 <?foreach($job_color_size_qty[$job_id][$color_id] as $key=>$value)
					{
					if($value !=""){?><td width="90" align='center' style="font-size:13px"  style='word-break:break-all;'><? echo $pro_type_wise_qty[$job_id][$pro_id][$color_id][$key]-$value;$sum_excess_value+=$pro_type_wise_qty[$job_id][$pro_id][$color_id][$key]-$value;?></td><?}
					}
					?>  		 
				  <td align='center' style='word-break:break-all;'style="font-size:13px"><?=$sum_excess_value;$total_excess+=$sum_excess_value;$sum_excess_value='';?></td>
			 </tr>
			 <?	
			}
			?>
        </table>
		
	</div>
	<div class="">
		<table class="tbl_bottom" cellspacing="0" cellpadding="0" border="1" rules="all" width="1430">
			<tr>
				<td colspan="14" style="font-size:13px"align='center'  width="120">Total <?=$plan_qty_name;?></td>
				<td width="90"style="font-size:13px"><?= $total_order;$total_order=0;?></td>
				<td width="90"style="font-size:13px">&nbsp;</td>
				<td width="90"style="font-size:13px">&nbsp;</td>
				<td width="90"style="font-size:13px">&nbsp;</td>
				<td width="90"style="font-size:13px">&nbsp;</td>
				<td width="90"style="font-size:13px">&nbsp;</td>		
			</tr>
			<tr>
				<td colspan="14" align='center'style="font-size:13px"  width="120">Total <?=$qty_name;?></td>
				<td width="90"style="font-size:13px"><?=$total_rcv;$total_rcv=0;?></td>
				<td width="90"style="font-size:13px">&nbsp;</td>
				<td width="90"style="font-size:13px">&nbsp;</td>
				<td width="90"style="font-size:13px">&nbsp;</td>
				<td width="90"style="font-size:13px">&nbsp;</td>	
				<td width="90"style="font-size:13px">&nbsp;</td>				
			</tr>
			<tr>
				<td colspan="14"style="font-size:13px" align='center' width="120">Total Excess <?=$qty_name;?></td>
				<td width="90"style="font-size:13px"><?=$total_excess;$total_excess=0;?></td>
				<td width="90"style="font-size:13px">&nbsp;</td>
				<td width="90"style="font-size:13px">&nbsp;</td>
				<td width="90"style="font-size:13px">&nbsp;</td>
				<td width="90"style="font-size:13px">&nbsp;</td>				
				<td width="90"style="font-size:13px">&nbsp;</td>
			</tr>
			
		</table> 
	</div>
	
	
<br>
<?
//  echo "<pre>";
//  print_r($date_production_iron_qty);
$i++;
	}
	}?>
	
    <?
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
	echo "$total_data####$filename";
	exit();       
} 



if($action=="bundleDtls_popup")
{
	echo load_html_head_contents("Bundle Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:1080px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1080px; margin-left:3px">
		<div id="report_container">
        <?
		$colorArr=return_library_array("select id, color_name from lib_color", "id", "color_name");
		$lotArr=return_library_array( "select color_id, lot from ppl_cut_lay_prod_dtls where status_active=1 and is_deleted=0", "color_id", "lot");
		$machineArr=return_library_array( "select id, machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no", "id", "machine_no");
		$sizeArr=return_library_array( "select id,size_name from lib_size", "id", "size_name");
		
		$sql="select a.size_set_no, b.machine_id, c.bundle_no, c.barcode_no, c.size_id, c.size_qty, c.number_start, c.number_end, d.gmts_color, d.yarn_color, d.sample_color, d.issue_qty as issue_qtylbs from pro_gmts_delivery_mst a, pro_garments_production_dtls b, ppl_cut_lay_bundle c, pro_gmts_knitting_issue_dtls d 
		where a.company_id='$companyid' and b.bundle_no='$bundleNo' and a.production_type=50 and a.id=b.delivery_mst_id and b.bundle_no=c.bundle_no and b.barcode_no=c.barcode_no and
		a.id=d.delivery_mst_id and b.delivery_mst_id=d.delivery_mst_id
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0"; 
		 echo $sql; 
		$dataArray=sql_select($sql); 
		$sampleColorArr=array(); $comboColorArr=array(); $bundleDataArr=array(); $lbcQtyArr=array();
		foreach($dataArray as $row)
		{
			$sampleColorArr[$row[csf('sample_color')]]=$row[csf('sample_color')];
			$comboColorArr[$row[csf('sample_color')]][$row[csf('yarn_color')]]=$row[csf('yarn_color')];
			
			$bundleDataArr[$row[csf('bundle_no')]][$row[csf('barcode_no')]][$row[csf('machine_id')]]=$row[csf('size_set_no')].'***'.$row[csf('gmts_color')].'***'.$row[csf('size_id')].'***'.$row[csf('size_qty')].'***'.$row[csf('number_start')].'-'.$row[csf('number_end')];
			
			$lbcQtyArr[$row[csf('bundle_no')]][$row[csf('barcode_no')]][$row[csf('sample_color')]][$row[csf('yarn_color')]]=$row[csf('issue_qtylbs')];
		}
		unset($dataArray);
		$countSampleSpan=count($sampleColorArr);
		$tblWidth=($countSampleSpan*70)+830;
		
		?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" rowspan="4">SL</th>
                        <th width="100" rowspan="4">Size Set No</th>
                        <th width="80" rowspan="4">Bundle No</th>
                        <th width="85" rowspan="4">Barcode No</th>
                        <th width="70" rowspan="4">MC No</th>
                        <th width="110" rowspan="4">GMT. Color</th>
                        <th width="70" rowspan="4">Size</th>
                        
                        <th width="80" rowspan="4">Bundle Qty.(Pcs</th>
                        <th rowspan="2">RMG No.</th>
                        <th colspan="<?=$countSampleSpan; ?>">Yarn Color Wise Cons Qty. (Lbs)</th>
                        <th rowspan="4">Color Qty.</th>
                    </tr>
                    <tr>
                    	<?
						foreach($sampleColorArr as $scolor)
						{
						?>
                    	<th width="70"><?=$colorArr[$scolor]; ?></th>
                       	<? } ?>
                    </tr>
                    <tr>
                    	<th width="70">Combo Color</th>
                        <?
						foreach($sampleColorArr as $scolor)
						{
							foreach($comboColorArr[$scolor] as $ccolor)
							{
						?>
                        <th width="70" style="word-break:break-all"><?=$colorArr[$ccolor]; ?></th>
                        <? }} ?>
                    </tr>
                    <tr>
                    	<th width="70">Yarn Lot</th>
                        <?
						foreach($sampleColorArr as $scolor)
						{
							foreach($comboColorArr[$scolor] as $ccolor)
							{
						?>
                        <th width="70" style="word-break:break-all"><?=$lotArr[$ccolor]; ?></th>
                        <? }} ?>
                    </tr>
				</thead>
             </table>
             <div style="width:<?=$tblWidth; ?>px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth-20; ?>" cellpadding="0" cellspacing="0">
                    <? $i=1;
        			foreach($bundleDataArr as $bundle_no=>$bundledata)
                    {
						foreach($bundledata as $barcode_no=>$barcodedata)
						{
							foreach($barcodedata as $machine_id=>$mcdata)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$exmc=explode("***",$mcdata);	
								$size_set_no=$gmts_color=$size_id=$size_qty=$norange='';
								$size_set_no=$exmc[0];
								$gmts_color=$exmc[1];
								$size_id=$exmc[2];
								$size_qty=$exmc[3];
								$norange=$exmc[4];
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:13px">
                                    <td width="30" align="center"><?=$i; ?></td>
                                    <td width="100" style="word-break:break-all"><?=$size_set_no; ?></td>
                                    <td width="80" style="word-break:break-all"><?=$bundle_no; ?></td>
                                    <td width="85" style="word-break:break-all"><?=$barcode_no; ?></td>
                                    
                                    <td width="70" style="word-break:break-all"><?=$machineArr[$machine_id]; ?></td>
                                    <td width="110" style="word-break:break-all"><?=$colorArr[$gmts_color]; ?></td>
                                    <td width="70" style="word-break:break-all"><?=$sizeArr[$size_id]; ?></td>
                                    
                                    <td width="80" align="right"><?=$size_qty; ?></td>
                                    <td width="70" align="center" style="word-break:break-all"><?=$norange; ?></td>
                                    
                                    <?
									$rcolorQty=0;
									foreach($sampleColorArr as $scolor)
									{
										foreach($comboColorArr[$scolor] as $ccolor)
										{
											$actualQtyLbs=0;
											$actualQtyLbs=$lbcQtyArr[$bundle_no][$barcode_no][$scolor][$ccolor];
											$rcolorQty+=$actualQtyLbs;
											?>
											<td width="70" align="right"><?=number_format($actualQtyLbs,4); ?></td>
									<? }
									} ?>
                                    <td align="right" style="word-break:break-all"><?=number_format($rcolorQty,4); ?></td>
                                </tr>
								<?
								$i++;
							}
						}
                    }
                    ?>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
    exit();	
}
?>