<?
session_start();
//ini_set('memory_limit','3072M');

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.fabrics.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$supplier_library=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/woven_style_closing_report_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/woven_style_closing_report_controller', this.value, 'load_drop_down_season', 'season_td');" );     	 
	exit();
}
if ($action=="load_drop_down_season")
{
	//$data_arr = explode("*", $data);
	//if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id in($data) and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	 //echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_id_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 100, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}


if($action=="style_ref_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
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
				selected_no.push( str );
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
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_no').val( num );
		}
		
		/*function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}*/
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_job_no; ?>'+'**'+'<? echo $txt_job_id; ?>'+'**'+'<? echo $style_owner; ?>', 'style_ref_list_view', 'search_div', 'woven_style_closing_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                    </td>
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

//style search------------------------------//
if($action=="style_ref_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_job_no,$txt_job_id,$style_owner)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$style_owner=str_replace("'","",$style_owner);
	
	if($style_owner>0) $style_owner_cond="and a.style_owner=$style_owner";else $style_owner_cond="";
	if($company>0) $comp_cond="and a.company_name=$company";else $comp_cond="";
	
	//echo $txt_job_no.'d';
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
	if($db_type==0) { //$job_cond=" and year(a.insert_date)='$cbo_year'";
	$slect_year="year(a.insert_date) as job_year";
	}
	else if($db_type==2) { //$job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	$slect_year="to_char(a.insert_date,'YYYY') as job_year";
	}

	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";
	}



	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$slect_year  from wo_po_details_master a where  is_deleted=0  $buyer_cond $year_cond $job_cond $search_con  $style_owner_cond $comp_cond order by a.id DESC";
	//echo $sql;  
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","200",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year", "","","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_job_no;?>';
	var style_id='<? echo $txt_job_id;?>';
	var style_des='<? echo $txt_style_ref;?>';
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
	echo load_html_head_contents("File No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $companyID;
	?>
	<script>
		/*var selected_id = new Array; var selected_name = new Array;
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
	*/
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
		//	alert (splitData[0]);
			$("#hide_order_id").val(splitData[0]); 
			$("#hide_ref_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer<? //echo $companyID.'fff';?></th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_ref_no" id="hide_ref_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
					<input type="hidden" name="txt_job_id" id="txt_job_id" value="<? echo $txt_job_id;?>" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--","","",0 );//$buyerID
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('txt_job_id').value+'**'+'<? echo $buyerID; ?>'+'**'+'<? echo $type; ?>'+'**'+'<? echo $style_owner; ?>', 'create_order_no_search_list_view', 'search_div', 'woven_style_closing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$txt_job_id=$data[6];
	$buyerID=$data[7];
	$type_id=$data[8];
	$style_owner=$data[9];
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$data[1]";
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($style_owner>0) $style_owner_cond="and a.style_owner=$style_owner";else $style_owner_cond="";
	if($company_id>0) $comp_cond="and a.company_name=$company_id";else $comp_cond="";

	if($search_by==1) $search_field="b.po_number"; 
	else if($search_by==2) $search_field="a.style_ref_no"; 	
	else $search_field="a.job_no";
		
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
	else $date_cond="";
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	if($pre_cost_ver_id==1) $entry_form_cond="and c.entry_from=111";
	else $entry_form_cond="and c.entry_from=158";
	if($type_id==1)
	{
	$js_select="id,grouping";
	} else $js_select="job_id,style_ref_no";
	
 $sql="select b.id,a.id as job_id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and  b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grouping is not null and $search_field like '$search_string' $buyer_id_cond $date_cond $entry_form_cond $style_owner_cond $comp_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Master Style, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "$js_select","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,grouping,pub_shipment_date","",'','0,0,0,0,0,0,3','',0) ;
   exit(); 
}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst",'job_no','costing_per');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	$style_owner=str_replace("'","",$cbo_style_owner);
	$txt_job_no=trim(str_replace("'","",$txt_job_no));
	$txt_job_id=trim(str_replace("'","",$txt_job_id));
	$hide_order_id=trim(str_replace("'","",$hide_order_id));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	$txt_style_ref=trim(str_replace("'","",$txt_style_ref));
	$txt_style_id=trim(str_replace("'","",$txt_style_id));
	$shipping_status_id=trim(str_replace("'","",$cbo_shipping_status));
	
	$cbo_brand_id=trim(str_replace("'","",$cbo_brand_id));
	$cbo_season_id=trim(str_replace("'","",$cbo_season_id));
	$cbo_season_year=trim(str_replace("'","",$cbo_season_year));
	
	if($cbo_season_id>0) $season_name_cond="and a.season_buyer_wise in($cbo_season_id)";else $season_name_cond="";
	if(trim($cbo_season_year)!=0) $season_year_cond=" and a.season_year=$cbo_season_year"; else $season_year_cond="";
	
	if($company_name>0) $comp_cond="and a.company_name in($company_name)";else $comp_cond="";
	if($style_owner>0) $style_owner_cond="and a.style_owner in($style_owner)";else $style_owner_cond="";
	if($cbo_brand_id>0) $brand_name_cond="and a.brand_id in($cbo_brand_id)";else $brand_name_cond="";
	
	if($txt_ref_no=="") $int_ref_cond=""; else $int_ref_cond=" and b.grouping='$txt_ref_no' ";
	if($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='$txt_style_ref' ";
	//if($hide_order_id=="") $hide_po_id_cond=""; else $hide_po_id_cond=" and b.grouping in($hide_order_id) ";
	//$job_no=str_replace("'","",$txt_job_no);
	//echo $job_no;die;
	if ($txt_job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($txt_job_no) ";
	if($txt_job_id!="")
	{
	 $job_id_cond=" and a.id in($txt_job_id) ";
	 $job_no_cond="";
	}
	else { 
	  	 $job_id_cond=""; 
	}
	//echo $txt_job_id.'=='.$job_no_cond;die;
	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond=" $year_field_con=$cbo_year"; else $year_cond="";
	}

	$order_status_id=str_replace("'","",$cbo_order_status);
	$order_status_cond='';
	if($order_status_id==0)
	{
		$order_status_cond=" and b.is_confirmed in(1,2)";
	}
	else if($order_status_id!=0)
	{
		$order_status_cond=" and b.is_confirmed=$order_status_id";
	}

	//$date_from=str_replace("'","",$txt_date_from);
	//$date_to=str_replace("'","",$txt_date_to);



	 
	

	
	ob_start();
	
	if($report_type==1)//Show button
	{
	 $width_td=3550;
	 ?>
	 <br/>
	 <div>
        <table width="<? echo $width_td;?>">
            <tr class="form_caption">
                <td colspan="41" align="center"><strong><? echo $hader_caption; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="41" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
              <tr>
               	<th colspan="21"></th>
                <th colspan="2">Excess Cut Against Order</th>
                <th>&nbsp;</th>
                <th colspan="2">Cut To Ship Ratio</th>
                <th colspan="2">Exc/Short Ship Against Order</th>
                <th colspan="5"></th>
                <th colspan="4">Finishing Quality Deatails	</th>
                 <th colspan="8"></th>
              </tr>
               <tr style="font-size:13px">
                       	<th width="20">SL</th>
                       	<th width="110">Buyer</th>
                       	<th width="100">Master Style</th>
                        <th width="100">Body/Wash color</th>
                        <th width="100">Job No</th>
                       	<th width="100">March Style</th>
                      	<th width="100">PO NO</th>
                       	<th width="80">O/Qty Pcs</th>
                       	<th width="80">Plan Cut Qty.</th>
                        <th width="80">Order Status</th>
                        
                       	<th width="80">Marker Length</th>
                       	<th width="80">Cons/Pcs with 1%</th>
                       	<th width="80">Lay Qty.</th>
                       	<th width="80">Cut Prod.Qty (pcs)</th>
                       <th width="80">Required <br> Fabric(yds)</th>
                       <th width="80">Fabric Rcvd (yds)</th> 
                       
                       	<th width="80">Fabric Issued (yds)</th>
                       	<th width="80">Fabric Balance<br> in Store</th>
                       	<th width="80">Fabric Used(yds)</th>
                       	<th width="80">Fabric Balance<br> after Cut</th>    
                       	<th width="80">Fabric saved<br> by Cutting</th>
                        
                       <th width="80" title="Exc/Short Ship Against Order">Qty</th>  
                       <th width="80">%</th>  
                       <th width="80">Ship Qty</th>
                       <th width="80" title="Cut To Ship Ratio">Qty</th>  
                       <th width="80">%</th> 
                        <th width="80" title="Exc/Short Ship Against Order">Qty</th>  
                        <th width="80">%</th>  
                        <th width="80" title="">Out put</th>
                        <th width="80">Send Wash</th>  
                        <th width="80">Rcvd Wash</th>  
                        <th width="80">Sewing Defect</th>
                        <th width="80">Short Rcvd from Wash</th> 
                        <th width="80">Wash reject/Pcs</th>
                        <th width="80">Sewing reject/Pcs</th>
                        <th width="80">Finish reject/Pcs</th>
                        <th width="80">Fabric defect/<br> reject/Pcs</th>  
                        <th width="80">Sample</th>
                        <th width="80">Ok gmts <br>after ship</th>
                        <th width="80">Total</th>
                        <th width="80">Missing</th>
                        
                        <th width="80">Wash Factory</th>
                        <th width="">Last ship date</th>
                        
                    </tr>
            </thead>
        </table>
        <div style="width:<? echo $width_td+20;?>px; max-height:250px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <?
		
		$total_lay_cut_qty=$total_po_qty_pcs=$total_plan_cut=$total_cut_prod_qty=$total_first_length=$total_cad_marker_cons=$total_wv_recv_fin=$total_wv_issue_fin=$total_req_fin_fab_qty=$total_fab_bal_store_qty=$total_fabric_used_qty=$total_fabric_bal_after_cut_qty=$total_fabric_saved_cuting_qty=$total_excess_cut_against=$total_ship_qty=$total_cut_to_ship_ratio=$total_excess_short_against_order=$total_sewing_out=$total_send_wash=$total_recv_wash=$total_sewing_defect=$total_short_recv_from_wash=$total_wash_reject_pcs=$total_sewing_reject_pcs=$total_fabric_defect_reject_pcs=$total_sample_pcs=$total_ok_gmt_after_ship=$total_tot_defect_reject=$total_missing=$total_poly_reject_pcs=0;
			

		 
			  $sql_po="select a.id as job_id,a.job_no_prefix_num, a.job_no, a.buyer_name,a.body_wash_color, a.style_ref_no, a.avg_unit_price, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.unit_price, b.shiping_status,b.is_confirmed, b.pub_shipment_date,b.grouping as ref_no, b.shiping_status, (c.order_quantity) as po_quantity, (c.plan_cut_qnty) as plan_cut, (c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $style_owner_cond  $comp_cond $style_owner_cond   $buyer_id_cond $year_cond $job_no_cond $season_name_cond  $season_year_cond $job_id_cond $order_id_cond_trans $order_no_cond $brand_name_cond $order_status_cond  $int_ref_cond  $style_ref_cond  order by b.id ";
		 
		//echo $sql_po;//die;
		$result_po=sql_select($sql_po); $i=1;
		$tot_rows=count($result_po);
		foreach($result_po as $row)
		{
			$is_confirmed=$row[csf('is_confirmed')];
			$job_no=$row[csf('job_no')];
			if($is_confirmed==1)
			{
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['conf_po_id'].=$row[csf('po_id')].',';
			}
			else
			{
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['proj_po_id'].=$row[csf('po_id')].',';
			}
			
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['job_no'].=$row[csf('job_no')].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['job_no_prefix'].=$row[csf('job_no_prefix_num')].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['pub_ship_date'].=$row[csf('pub_shipment_date')].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['buyer_name']=$row[csf('buyer_name')];
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['style_ref_no']=$row[csf('style_ref_no')];
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['po_number'].=$row[csf('po_number')].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['po_id'].=$row[csf('po_id')].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['file_no']=$row[csf('file_no')];
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['body_wash_color']=$row[csf('body_wash_color')];
			if($is_confirmed==1)
			{
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['po_qty_pcs']+=$row[csf('po_quantity')]*$row[csf('ratio')];
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['po_total_price']+=$row[csf('po_total_price')];
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['plan_cut']+=$row[csf('plan_cut')];
			}
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['shiping_status']=$row[csf('shiping_status')];
			$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no_prefix_num')];
			$style_ref_arr[$row[csf('job_no')]]=$row[csf('style_ref_no')];
			$master_ref_arr[$row[csf('job_no')]]=$row[csf('ref_no')];
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		}
		$sql_cut=sql_select("select d.id,a.job_no,d.cutting_no as cutting_no,d.marker_length,d.cad_marker_cons,c.marker_qty from wo_po_details_master a, ppl_cut_lay_mst d,ppl_cut_lay_dtls c where a.job_no=d.job_no   and d.id=c.mst_id and d.entry_form=289 and c.status_active=1 and c.is_deleted=0    $style_owner_cond  $comp_cond   $buyer_id_cond $year_cond $job_no_cond    $style_ref_cond $job_id_cond  $brand_name_cond  ".where_con_using_array($job_id_arr,0,'a.id')."  order by d.id asc");
		
		//echo "select d.id,a.job_no,d.cutting_no as cutting_no,d.marker_length,d.cad_marker_cons,c.marker_qty from wo_po_details_master a, ppl_cut_lay_mst d,ppl_cut_lay_dtls c where a.job_no=d.job_no   and d.id=c.mst_id and d.entry_form=289 and c.status_active=1 and c.is_deleted=0    $style_owner_cond  $comp_cond   $buyer_id_cond $year_cond $job_no_cond    $style_ref_cond $job_id_cond  $brand_name_cond  ".where_con_using_array($job_id_arr,1,'a.id')."  order by d.id asc";
		 
		foreach($sql_cut as $row)
		{
			$master_ref=$master_ref_arr[$row[csf('job_no')]];
			$cut_no_lay_arr[$master_ref]['cutting_no'].=$row[csf('cutting_no')].',';
			$cut_lay_arr[$row[csf('job_no')]]['lay_cut']+=$row[csf('marker_qty')];
			if($row[csf('marker_length')])
			{
			$cut_lay_first_arr[$row[csf('cutting_no')]]['first_length']=$row[csf('marker_length')];
			}
			if($row[csf('cad_marker_cons')])
			{
			$cut_lay_first_arr[$row[csf('cutting_no')]]['cad_marker_cons']=$row[csf('cad_marker_cons')];
			}
		}
		//print_r($cut_no_lay_arr);
		$booking_fin_qnty_arr=array();
		$booking_sql=sql_select("SELECT a.po_break_down_id as po_id,a.booking_no ,a.fin_fab_qnty,b.grouping as ref_no from wo_booking_dtls a,wo_po_break_down b  where b.id=a.po_break_down_id and  a.booking_type=1 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'a.po_break_down_id')." ");
		//echo "SELECT a.po_break_down_id as po_id,a.booking_no ,a.fin_fab_qnty,b.grouping as ref_no from wo_booking_dtls a,wo_po_break_down b  where b.id=a.po_break_down_id and  a.booking_type=1 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,1,'a.po_break_down_id')." ";
		// echo "SELECT a.po_break_down_id as po_id ,a.fin_fab_qnty from wo_booking_dtls a  where a.booking_type=1 and  a.status_active=1 and a.is_deleted=0 ".where_con_using_array($po_id_arr,1,'a.po_break_down_id')." ";
		
		foreach($booking_sql as $row)
		{
			$booking_fin_qnty_arr[$row[csf("po_id")]]["qnty"]+=$row[csf("fin_fab_qnty")];
			$booking_no_arr[$row[csf("ref_no")]]["booking_no"]=$row[csf("booking_no")];
		}
		unset($booking_sql);
			
		$cut_qc_arr=sql_select("SELECT a.po_break_down_id as po_id,(b.production_qnty) as qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'a.po_break_down_id')." ");
		foreach( $cut_qc_arr as $row )
		{
			 $cuting_prod_arr[$row[csf("po_id")]]["qnty"]+=$row[csf("qnty")];
		}
		unset($cut_qc_arr);
		$act_ahip_arr=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as shipout_qty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty
		from  pro_ex_factory_mst where status_active=1 and is_deleted=0 ".where_con_using_array($po_id_arr,0,'po_break_down_id')." group by po_break_down_id");// 
		
		foreach($act_ahip_arr as $row)
		{
			$actual_shipout_arr[$row[csf('po_break_down_id')]]['shipout_qty']=$row[csf('shipout_qty')]-$row[csf('return_qnty')];
		}
	 unset($act_ahip_arr);
	 $sew_out_sql=sql_select("select a.po_break_down_id as po_id,
	 (CASE WHEN  a.production_type=5 THEN b.production_qnty else 0 END)  as sewing_out,
	 (CASE WHEN  a.production_type=5 THEN b.reject_qty else 0 END)  as rej_sewing_out,
	 (CASE WHEN  a.production_type=2 THEN b.production_qnty else 0 END)  as send_wash,
	 (CASE WHEN  a.production_type=3 THEN b.production_qnty else 0 END)  as recv_wash ,
	 (CASE WHEN  a.production_type=11 THEN b.production_qnty else 0 END)  as poly_fin,
	 (CASE WHEN  a.production_type=80 THEN b.production_qnty else 0 END)  as fin_qty,
	 (CASE WHEN  a.production_type=80 THEN b.reject_qty else 0 END)  as rej_fin_qty
	   
	  from pro_garments_production_mst a,pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type in(2,3,5,80) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'po_break_down_id')."");
	
	foreach($sew_out_sql as $row)
	{
		$sewing_out_arr[$row[csf('po_id')]]['sewing_out']+=$row[csf('sewing_out')];
		$sewing_out_arr[$row[csf('po_id')]]['sewing_out_rej']+=$row[csf('rej_sewing_out')];
		$sewing_out_arr[$row[csf('po_id')]]['fin_qty']+=$row[csf('fin_qty')];
		$sewing_out_arr[$row[csf('po_id')]]['fin_qty_rej']+=$row[csf('rej_fin_qty')];
		
		$sewing_out_arr[$row[csf('po_id')]]['send_wash']+=$row[csf('send_wash')];
		$sewing_out_arr[$row[csf('po_id')]]['recv_wash']+=$row[csf('recv_wash')];
	}
	 unset($sew_out_sql);
							
	 
		//print_r($cuting_prod_arr);
		unset($cut_qc_arr);$wv_recv_qnty_arr=array();$wv_issue_qnty_arr=array();
		$wv_fin_sql=sql_select("SELECT po_breakdown_id as po_id, entry_form,(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(18,17,19)  ".where_con_using_array($po_id_arr,0,'po_breakdown_id')." ");
		//echo "SELECT po_breakdown_id as po_id, entry_form,(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(18,17,19)  ".where_con_using_array($po_id_arr,0,'po_breakdown_id')." ";
			
			foreach($wv_fin_sql as $row)
			{		 	
			 	$entry_form_id=$row[csf("entry_form")];
				if($entry_form_id==17)
				{
				$wv_recv_qnty_arr[$row[csf("po_id")]]+=$row[csf("qnty")];
				}
				if($entry_form_id==18 || $entry_form_id==19)  //Issue
				{
				$wv_issue_qnty_arr[$row[csf("po_id")]]+=$row[csf("qnty")];
				}
			}
			unset($wv_fin_sql);
		
		foreach($master_stlye_arr as $master_style=>$style_data)
		{
		 foreach($style_data as $job_no=>$row)
		 {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			if($company_name) $company_name=$company_name;else $company_name=0;
			if($style_owner) $style_owner=$style_owner;else $style_owner=0;
			
			$job_no=rtrim($row[('job_no')],',');
			$job_nos=implode(",",array_unique(explode(",",$job_no)));
			$job_no_prefix=rtrim($row[('job_no_prefix')],',');
			$buyer_name=$row[('buyer_name')]; 
			$job_noArr=array_unique(explode(",",$job_no));
			$job_no_prefixArr=array_unique(explode(",",$job_no_prefix));
			$style_ref_no=rtrim($row[('style_ref_no')],',');
			$po_number=rtrim($row[('po_number')],',');
			$po_id=rtrim($row[('po_id')],',');
			$proj_po_id=rtrim($row[('proj_po_id')],',');//proj_po_id
			$conf_po_id=rtrim($row[('conf_po_id')],',');
			
			$po_idArr=array_unique(explode(",",$po_id));
			$po_id_all=implode(",",$po_idArr);
			//echo $po_id_all.', ';
			$proj_po_idArr=array_unique(explode(",",$proj_po_id));
			$conf_po_idArr=array_unique(explode(",",$conf_po_id));
			
			$style_ref_nos=implode(",",array_unique(explode(",",$style_ref_no)));
			$po_numbers=implode(",",array_unique(explode(",",$po_number)));
			$lay_cut_qty=0;$print_button_job="";
			foreach($job_noArr as $job)
			{
				
				$job_prefix=$job_no_arr[$job];$style_ref_nos=$style_ref_arr[$job];
			$cutting_no=$cut_lay_arr[$job]['cutting_no'];
			$lay_cut_qty+=$cut_lay_arr[$job]['lay_cut'];
		if($print_button_job=="") $print_button_job='<a href="##" onclick="report_generate_pop('.$company_name.",".$style_owner.",'".$job_prefix."','".$style_ref_nos."',".$buyer_name.",'".$po_id_all."','generate_style_report_with_graph',1".')">'.$job_prefix.'</a>';
			else $print_button_job.=",".'<a href="##" onclick="report_generate_pop('.$company_name.",".$style_owner.",'".$job_prefix."','".$style_ref_nos."',".$buyer_name.",'".$po_id_all."','generate_style_report_with_graph',1".')">'.$job_prefix.'</a>';
			
			}
			$cutting_no=$cut_no_lay_arr[$master_ref]['cutting_no'];
			$cutting_noS=rtrim($cutting_no,',');
				$cutting_noArr=array_unique(explode(",",$cutting_noS));
				$min_cutting_no=min($cutting_noArr);
				
			$cut_prod_qty=0;$wv_recv_fin=0;$wv_issue_fin=0;$ship_qty=$sewing_out=$send_wash=$recv_wash=$fin_qty_rej=$fin_qty=0;;
			$origin_po_all='';$sewing_out_rej=0;
			foreach($po_idArr as $pid) //all po
			{
			//$cutting_no=$cut_lay_arr[$job]['cutting_no'];
			$cut_prod_qty+=$cuting_prod_arr[$pid]['qnty'];
			$wv_recv_fin+=$wv_recv_qnty_arr[$pid];
			$ship_qty+=$actual_shipout_arr[$pid]['shipout_qty'];
			
			$sewing_out+=$sewing_out_arr[$pid]['sewing_out'];
			$sewing_out_rej+=$sewing_out_arr[$pid]['sewing_out_rej'];
			//$fin_qty+=$sewing_out_arr[$pid]['fin_qty'];
			$fin_qty_rej+=$sewing_out_arr[$pid]['fin_qty_rej'];
			
			$send_wash+=$sewing_out_arr[$pid]['send_wash'];
			$recv_wash+=$sewing_out_arr[$pid]['recv_wash'];
			if($origin_po_all=='') $origin_po_all=$pid;else $origin_po_all.=",".$pid;
			
			 $wv_issue_fin+=$wv_issue_qnty_arr[$pid];
			
			}
			$recv_wash=9480;
			$sewing_defect=1;
			$short_recv_from_wash=10;
			$wash_reject_pcs=91;
			$sewing_reject_pcs=$sewing_out_rej;
			$finish_reject_pcs=$fin_qty_rej;
			$fabric_defect_reject_pcs=117;
			$sample_pcs=10;
			$wash_factory='CWL-2';
			$ok_gmt_after_ship=0;
			

			$req_fin_fab_qty=0;
			foreach($proj_po_idArr as $pid) //Projected po
			{
			//$cutting_no=$cut_lay_arr[$job]['cutting_no'];
			$req_fin_fab_qty+=$booking_fin_qnty_arr[$pid]["qnty"];
			 
			}
			$conf_po_all='';
			foreach($conf_po_idArr as $pid) //Confirm po
			{
			
			 if($conf_po_all=='') $conf_po_all=$pid;else $conf_po_all.=",".$pid;
			}
			
			$first_length=$cut_lay_first_arr[$min_cutting_no]['first_length'];
			$cad_marker_cons=$cut_lay_first_arr[$min_cutting_no]['cad_marker_cons'];
		// $cuting_prod_arr[$row[csf("order_id")]]["qnty"];
			$pub_ship_date=rtrim($row[('pub_ship_date')],',');
			$pub_ship_dateArr=array_unique(explode(",",$pub_ship_date));
			$conf_po_allArr=array_unique(explode(",",$conf_po_all));
			$last_ship_date=max($pub_ship_dateArr);
			$booking_noArr=explode("-",$booking_no_arr[$master_style]["booking_no"]);
			$booking_no=ltrim($booking_noArr[3],'0');
			
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
				<td width="20" ><? echo $i; ?></td>
				<td width="110"><p><? echo $buyer_library[$row[('buyer_name')]]; ?></p></td>
				<td width="100" ><p><? echo $master_style; ?></p></td>
                <td width="100"><p><? echo  $color_arr[$row[('body_wash_color')]]; ?></p></td>
				<td width="100"><p><? echo  $print_button_job; ?></p></td>
                <td width="100"><p><? echo  $style_ref_nos; ?></p></td>
				<td width="100"><p><a href="##" onClick="generate_popup('<? echo $conf_po_all; ?>','po_popup',1)"><? echo count($conf_po_allArr); ?></a></p></td>
				<td width="80" align="right"><p><? echo number_format($row[('po_qty_pcs')],0); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($row[('plan_cut')],0);; ?></p></td>
				<td width="80" ><p><? echo $shipment_status[$row[('shiping_status')]]; ?></p></td>
				<td width="80"  title="<? echo $cutting_no;?>" align="right"><p> <? echo  $first_length; ?> 	</p></td>
				
				<td width="80" align="right"><p><? echo $cad_marker_cons; ?></p></td>
				<td width="80" align="right"><p><a href="##" onClick="report_generate_pop('<? echo $company_name; ?>','<? echo $style_owner; ?>','<? echo $job_nos; ?>','<? echo $style_ref_nos; ?>','<? echo $buyer_name; ?>','<? echo $po_id_all?>','report_generate',2)"><? echo number_format($lay_cut_qty,2); ?></a></p></td>
				
                <td width="80" align="right"><p><a href="##" onClick="generate_popup('<? echo $po_id_all; ?>','cuting_qc_popup',2)"><? echo $cut_prod_qty; ?></a></p></td>
                <td width="80" align="right"><p><? echo number_format($req_fin_fab_qty,2); ?></p></td>
				<td width="80" align="right" title="Woven Fin Recv"><p><a href="##" onClick="report_generate_popup('<? echo $company_name; ?>','<? echo $style_owner; ?>','<? echo $booking_no; ?>','report_generate_woven',3)"><? echo number_format($wv_recv_fin,2); ?></a></p></td>
				
                <td width="80" align="right"><p><? echo number_format($wv_issue_fin,2); ?></p></td>
				<td width="80" align="right" title="Woven Recv-Issue"><p><? $fab_bal_store_qty=$wv_recv_fin-$wv_issue_fin;echo number_format($fab_bal_store_qty,2); 
				$fabric_used_qty=($cad_marker_cons/12)*$lay_cut_qty;
				?></p></td>
				<td width="80" align="right" title="Cad marker Cons(<? echo $cad_marker_cons;?>)/12*Lay Qty=<? echo $lay_cut_qty;?>"><p><? 
				echo number_format($fabric_used_qty,4); ?></p></td>
				<td width="80" align="right" title="Woven Recv-Fabric used"><p><? $fabric_bal_after_cut_qty=$wv_recv_fin-$fabric_used_qty;
				echo number_format($fabric_bal_after_cut_qty,2); ?></p></td>
				<td width="80" align="right" title="Req Fin Fab Qty-Fabric used"><p><? $fabric_saved_cuting_qty=$req_fin_fab_qty-$fabric_used_qty; echo number_format($fabric_saved_cuting_qty,2); ?></p></td>
				<td width="80" align="right" title="Lay Qty-Po Qty Pcs"><p><? $excess_cut_against=$lay_cut_qty-$row[('po_qty_pcs')];
				 $excess_cut_against_per=$excess_cut_against/$row[('po_qty_pcs')]*100;
				 if($excess_cut_against_per>3)
				 {
					 $color_td="red";
				 }
				 else  $color_td="black";
				  echo "<font  style='color: $color_td;'>".number_format($excess_cut_against,2)."</font>";
				 ?></p></td>
				<td width="80" align="right" title="Excess Cut against Qty/Po Qty Pcs*100"><p><font style="color:<? echo $color_td; ?>"><?
				 echo number_format($excess_cut_against_per,2); ?> </font> </p></td>
                 
				<td width="80" align="right"><p><? echo number_format($ship_qty,2); ?></p></td>
				<td width="80" align="right"  title="Lay Qty-Ship Qty"><p><? $cut_to_ship_ratio=$lay_cut_qty-$ship_qty;echo number_format($cut_to_ship_ratio,2); ?></p></td>
				<td width="80" align="right" title="Cut To Ship Ratio/Cutting Prod Qty"><p><? $cut_to_ship_ratio_per=$cut_to_ship_ratio/$cut_prod_qty*100;echo number_format($cut_to_ship_ratio_per,2); ?></p></td>
				<td width="80" align="right" title="Ship Qty-Po Qty Pcs"><p><? $excess_short_against_order=$ship_qty-$row[('po_qty_pcs')]; echo number_format($excess_short_against_order,2); ?></p></td>
				<td width="80" align="right" title="Excess Short_against/PO Pcs Qty"><p><? $excess_short_against_order_per=$excess_short_against_order/$row[('po_qty_pcs')];echo number_format($excess_short_against_order_per,2); ?></p></td>

				<td width="80" align="right"><p><a href="##"  onClick="generate_line_popup('<? echo $po_id_all; ?>','<? echo $job_nos; ?>','<? echo $style_ref_nos; ?>','line_date_wise_popup',3)"><? echo number_format($sewing_out,2); ?></a></p></td>
				<td width="80" align="right"><p><? echo number_format($send_wash,2); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($recv_wash,2); ?></p></td>

				<td width="80" align="right" title=""><p><? echo number_format($sewing_defect,2); ?></p></td>
				<td width="80" align="right" title=""><p><? echo number_format($short_recv_from_wash,2); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($wash_reject_pcs,2); ?></p></td>
				<td width="80" align="right" title="Defect"><p><a href="##" onClick="generate_line_defect_popup('<? echo $po_id_all; ?>','<? echo $job_nos; ?>','<? echo $master_style; ?>','sewing_line_defect_wise_popup',4)"><? echo number_format($sewing_reject_pcs,2); ?></a></p></td>
                <td width="80" align="right" title="Defect"><p><a href="##" onClick="generate_line_defect_popup('<? echo $po_id_all; ?>','<? echo $job_nos; ?>','<? echo $master_style; ?>','finish_line_defect_wise_popup',4)"><? echo number_format($finish_reject_pcs,2); ?></a></p></td>
                
				<td width="80" align="right"><p><? echo number_format($fabric_defect_reject_pcs,2); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($sample_pcs,2); ?></p></td>
				<td width="80" align="right" title=""><p><? echo number_format($ok_gmt_after_ship,2); ?></p></td>
				<td width="80" align="right" title="Sewing Defect+short recv+wash Reject+sewing Reject+Fab Defect+sample "><p><? 
				$tot_defect_reject=$sewing_defect+$short_recv_from_wash+$wash_reject_pcs+$sewing_reject_pcs+$fabric_defect_reject_pcs+$sample_pcs+$ok_gmt_after_ship;
				echo number_format($tot_defect_reject,2); ?></p></td>
				<td width="80" align="right" title="Cut to Ratio-Total Defect"><p><? $missing=$cut_to_ship_ratio-$tot_defect_reject;echo number_format($missing,2); ?></p></td>
				<td width="80" align="center" title=""><p><? echo $wash_factory; ?></p></td>
				<td align="center" title=""><p><? echo $last_ship_date; ?></p></td>
			</tr>
			
			<?
				$total_lay_cut_qty+=$lay_cut_qty;
				$total_po_qty_pcs+=$row[('po_qty_pcs')];
				$total_plan_cut+=$row[('plan_cut')];
				$total_cut_prod_qty+=$cut_prod_qty;
				$total_first_length+=$first_length;
				$total_cad_marker_cons+=$cad_marker_cons;
				$total_wv_recv_fin+=$wv_recv_fin;
				$total_wv_issue_fin+=$wv_issue_fin;
				$total_req_fin_fab_qty+=$req_fin_fab_qty;
				$total_fab_bal_store_qty+=$fab_bal_store_qty;
				$total_fabric_used_qty+=$fabric_used_qty;
				$total_fabric_bal_after_cut_qty+=$fabric_bal_after_cut_qty;
				$total_fabric_saved_cuting_qty+=$fabric_saved_cuting_qty;
				$total_excess_cut_against+=$excess_cut_against;
				$total_ship_qty+=$ship_qty;
				$total_cut_to_ship_ratio+=$cut_to_ship_ratio;
				$total_excess_short_against_order+=$excess_short_against_order;
				$total_sewing_out+=$sewing_out;
				$total_send_wash+=$send_wash;
				
				$total_recv_wash+=$recv_wash;
				$total_sewing_defect+=$sewing_defect;
				$total_short_recv_from_wash+=$short_recv_from_wash;
				$total_wash_reject_pcs+=$wash_reject_pcs;
				$total_sewing_reject_pcs+=$sewing_reject_pcs;
				$total_poly_reject_pcs+=$finish_reject_pcs;
				$total_fabric_defect_reject_pcs+=$fabric_defect_reject_pcs;
				$total_sample_pcs+=$sample_pcs;
				$total_ok_gmt_after_ship+=$ok_gmt_after_ship;
				$total_tot_defect_reject+=$tot_defect_reject;
				$total_missing+=$missing;
				
				 
				//$total_lay_cut_qty=$total_po_qty_pcs=$total_plan_cut=$total_cut_prod_qty=$total_first_length=$total_cad_marker_cons=$total_wv_recv_fin=$total_wv_issue_fin=$total_req_fin_fab_qty=$total_fab_bal_store_qty=$total_fabric_used_qty=$total_fabric_bal_after_cut_qty=$total_fabric_saved_cuting_qty=$total_excess_cut_against=$total_ship_qty=$total_cut_to_ship_ratio=$total_excess_short_against_order=$total_sewing_out=$total_send_wash=$total_recv_wash=$total_sewing_defect=$total_short_recv_from_wash=$total_wash_reject_pcs=$total_sewing_reject_pcs=$total_fabric_defect_reject_pcs=$total_sample_pcs=$total_ok_gmt_after_ship=$total_tot_defect_reject=$total_missing=0;
			
			$i++;
		  }
		}
		//echo $tot_order_value;
		?>
        </table>
        </div>
        <table class="tbl_bottom" width="<? echo $width_td;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
        	<tr>
                <td width="20"></td>
                <td width="110"></td>
                <td width="100"></td>
                 <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100">Total</td>
               
                <td width="80"><? echo number_format($total_po_qty_pcs,0);?></td>
                <td width="80"><? echo number_format($total_plan_cut,0);?></td>
                <td width="80"></td>
                <td width="80"><? echo number_format($total_first_length,0);?></td>
                <td width="80"><? echo number_format($total_cad_marker_cons,2);?></td>
                
                <td width="80"><? echo number_format($total_lay_cut_qty,2);?></td>
                <td width="80"><? echo number_format($total_cut_prod_qty,2);?></td>
              
                <td width="80"><? echo number_format($total_req_fin_fab_qty,2);?></td>
                <td width="80"><? echo number_format($total_wv_recv_fin,2);?></td>
                <td width="80"><? echo number_format($total_wv_issue_fin,2);?></td>
                <td width="80"><? echo number_format($total_fab_bal_store_qty,2);?></td>
                <td width="80"><? echo number_format($total_fabric_used_qty,4);?></td>
                
                <td width="80"><? echo number_format($total_fabric_bal_after_cut_qty,2);?></td>
                <td width="80"><? echo number_format($total_fabric_saved_cuting_qty,2);?></td>
                <td width="80"><? echo number_format($total_excess_cut_against,2);?></td>
                
                <td width="80"><? //echo number_format($total_ship_qty,2);?></td>
                <td width="80"><? echo number_format($total_ship_qty,2);?></td>
                <td width="80"><? echo number_format($total_cut_to_ship_ratio,2);?></td>
                
                <td width="80"><? //echo number_format($total_other_cost_conv,2);?></td>

                <td width="80"><? echo number_format($total_excess_short_against_order,2);?></td>
                <td width="80"><? //echo number_format($total_tot_embell_cost,2);?></td>

                <td width="80"><? echo number_format($total_sewing_out,2);?></td>
                
                <td width="80"><? echo number_format($total_send_wash,2);?></td>
                <td width="80"><? echo number_format($total_recv_wash,2);?></td>

                <td width="80"><? echo number_format($total_sewing_defect,2);?></td>
                
                <td width="80"><? echo number_format($total_short_recv_from_wash,2);?></td>
                <td width="80"><? echo number_format($total_wash_reject_pcs,2);?></td>
                
                <td width="80"><? echo number_format($total_sewing_reject_pcs,2);?></td>
                <td width="80"><? echo number_format($total_poly_reject_pcs,2);?></td>
                
                <td width="80"><? echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80"><? echo number_format($total_sample_pcs,2);?></td>
                <td width="80"><? echo number_format($total_ok_gmt_after_ship,2);?></td>
                <td width="80"><? echo number_format($total_tot_defect_reject,2);?></td>
                <td width="80"><? echo number_format($total_missing,2);?></td>
               
                <td>&nbsp;</td>
            </tr>
            
        </table>
    </div>
    <?
	}
	else //Show 2
	{
		$width_td=6020;
						
		 ?>
	<div>
        <table width="<? echo $width_td;?>">
            <tr class="form_caption">
                <td colspan="41" align="center"><strong><? echo $hader_caption; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="41" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
            </tr>
        </table>
		
        <table class="rpt_table" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
              <tr>
               	<th colspan="13" style="background:#ffc299">Order Status</th>
                <th colspan="4" style="background:#80bfff">Approval Status</th>
                <th colspan="6"  style="background:#8c6699">Consumption Varriance / Yds / Pcs</th>
                <th colspan="5" style="background:#a6a659">Total Consumption Varriance Yds</th>
                
                <th colspan="8" style="background:#996666">Fabric Receive Status</th>
                <th width="80" rowspan="2" style="background:#ffc299">Possible Cut Qty./Budget</th> 
                <th colspan="6" style="background:#80bfff">Cutting Status (Pcs)</th>
                <th colspan="5" style="background:#8c6699">Sewing Status (Pcs)</th>
                <th colspan="4" style="background:#a6a659">Washing Status</th>
                <th colspan="5" style="background:#cccc00">Finishing Status</th>
                <th colspan="5" style="background:#b3b3f">Shipment  Status</th>
                <th colspan="8" style="background:#c68c53">Comemrcial Status</th>
                <th colspan="3" style="background:#c68c53">&nbsp;</th>
                 
              </tr>
               <tr style="font-size:13px">
                       	<th width="20">SL</th>
                       	<th width="110">Buyer</th>
                        <th width="100">Brand</th>
                        <th width="100">Season</th>
                        <th width="60">Season Year</th>
                        
                       	<th width="100">Master Style</th>
                        <th width="100">Body Wash Color</th>
                        <th width="100">Job No</th>
                       	<th width="100">March Style</th>
                      	<th width="100">PO NO</th>
                       	<th width="80">O/Qty Pcs</th>
                       	<th width="80">Plan Cut Qty.</th>
                        <th width="80">Order Status</th>
                        
                        <th width="80">Sample</th>
                        <th width="80">Labdip</th>
                        <th width="80">Trims</th>
                        <th width="80">Emblishment</th>
                        
                        
                       	<th width="80">Buyer Cons</th>
                       	<th width="80">Booking Cons</th>
                       	<th width="80">Actual Cons</th>
                       	<th width="80">Marker Cons.</th>
                       <th width="80">Budget VS Actual</th>
                       <th width="80">Marker VS Actual</th>
                       
                       <th width="80">Buyer Cons<hr>Yds</th> 
                       <th width="80">Booking Cons<hr>Yds</th>
                       <th width="80">Actual Cons<hr>Yds</th>
                       <th width="80">Budget VS Actual</th>
                       <th width="80">Marker VS Actual</th>
                        
                       	<th width="80">Required(Yds)</th>    
                       	<th width="80">Receive (Yds)</th>
                       <th width="80" title="Fab Recv">Transfer In (Yds)</th> 
                       <th width="80">Transfer Out (Yds)</th>  
                       <th width="80">Issue (Yds)</th>
                       <th width="80" title="">Used (Yds)</th>  
                       <th width="80">Laftover Fab.(Yds)</th> 
                       <th width="80" title="">Balance In Store</th>  
                        							 
                        <th width="80" title="">Cut & Lay Qty.</th>
                        <th width="80">QC Pass Qty.</th>  
                        <th width="80">Defect Qty.</th>  
                        <th width="80">DHU %</th>
                        <th width="80">Excess Cut %</th> 
                        <th width="80">Delivery To Input</th>
                        
                        <th width="80">Input Qty.</th>
                        <th width="80">QC Pass Qty.</th>
                        <th width="80">Reject Qty.</th>  
                        <th width="80">Defect %</th>
                        <th width="80">Send Wash</th>
                        
                        <th width="80">WashReceive</th>
                        <th width="80">Wash Production</th>
                        <th width="80">Send To Gmt.</th>
                        <th width="80">Wash In Hand</th>
                        
                        <th width="80">Receive <br>From Wash</th>
                        <th width="80">QC Pass Qty.</th>
                        <th width="80">Wash Reject</th>
                        <th width="80">Finishing Reject</th>
                        <th width="80">Defect %</th>
                        
                        <th width="80">Ex-Factory Qty.</th>
                        <th width="80">Short / Excess  Qty.</th>
                        <th width="80">Short / Excess  %</th>
                        <th width="80">Cut To Ship Qty. (Short/Excess)</th>
                        <th width="80">Cut To Ship Ratio %</th>
                        
                        <th width="80">Invoice Qty.</th>
                        <th width="80">Invoice$</th>
                        
                        <th width="80">Discunt$</th>
                        <th width="80">NetInvoice$</th>
                        <th width="80">Buyer Sub.$</th>
                        <th width="80">Bank Sub.$</th>
                        
                        <th width="80">Payment Rcvd $</th>
                        <th width="80">Short/Excess Ship$</th>
                        
                        <th width="80">Sample</th>
                        <th width="80">Wash Factory</th>
                        <th width="">Last ship date</th>
                        
                        
                        
                    </tr>
            </thead>
        </table>
        <div style="width:<? echo $width_td+20;?>px; max-height:250px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <?
		
		$total_lay_cut_qty=$total_po_qty_pcs=$total_plan_cut=$total_cut_prod_qty=$total_first_length=$total_cad_marker_cons=$total_wv_recv_fin=$total_wv_issue_fin=$total_req_fin_fab_qty=$total_fab_bal_store_qty=$total_fabric_used_qty=$total_fabric_bal_after_cut_qty=$total_fabric_saved_cuting_qty=$total_excess_cut_against=$total_ship_qty=$total_cut_to_ship_ratio=$total_excess_short_against_order=$total_sewing_out=$total_send_wash=$total_recv_wash=$total_sewing_defect=$total_short_recv_from_wash=$total_wash_reject_pcs=$total_sewing_reject_pcs=$total_fabric_defect_reject_pcs=$total_sample_pcs=$total_ok_gmt_after_ship=$total_tot_defect_reject=$total_missing=$total_poly_reject_pcs=0;
			

		 
			$sql_po="select a.id as job_id,a.job_no_prefix_num, a.job_no, a.buyer_name,a.inquiry_id,a.season_buyer_wise,a.season_year,a.brand_id,a.body_wash_color, a.style_ref_no, a.avg_unit_price, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.unit_price, b.shiping_status,b.is_confirmed, b.pub_shipment_date,b.grouping as ref_no, (c.order_quantity) as po_quantity, (c.plan_cut_qnty) as plan_cut, (c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.id=b.job_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $style_owner_cond  $comp_cond $style_owner_cond   $buyer_id_cond $year_cond $job_no_cond  $season_name_cond  $season_year_cond  $job_id_cond $order_id_cond_trans $order_no_cond $brand_name_cond $order_status_cond  $int_ref_cond  $style_ref_cond  order by b.id ";
		 
		//  echo $sql_po;//die;
		$result_po=sql_select($sql_po); $i=1;
		$tot_rows=count($result_po);
		foreach($result_po as $row)
		{
			$is_confirmed=$row[csf('is_confirmed')];
			$job_no=$row[csf('job_no')];
			if($is_confirmed==1)
			{
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['conf_po_id'].=$row[csf('po_id')].',';
			}
			else
			{
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['proj_po_id'].=$row[csf('po_id')].',';
			}
			$inquiry_id_arr[$row[csf('inquiry_id')]]=$row[csf('inquiry_id')];
			$inquiry_id_jobWise_arr[$row[csf('job_no')]]=$row[csf('inquiry_id')];
			
			$inquiry_wise_jobQty_arr[$row[csf('inquiry_id')]]+=$row[csf('po_quantity')]*$row[csf('ratio')];
			//season_buyer_wise,a.season_year,a.brand_id
			
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['job_no'].=$row[csf('job_no')].',';
			
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['season'].=$season_arr[$row[csf('season_buyer_wise')]].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['brand_id'].=$brand_name_arr[$row[csf('brand_id')]].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['season_year'].=$row[csf('season_year')].',';
			
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['job_no_prefix'].=$row[csf('job_no_prefix_num')].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['pub_ship_date'].=$row[csf('pub_shipment_date')].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['buyer_name']=$row[csf('buyer_name')];
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['style_ref_no']=$row[csf('style_ref_no')];
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['body_wash_color']=$row[csf('body_wash_color')];
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['po_number'].=$row[csf('po_number')].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['po_id'].=$row[csf('po_id')].',';
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['file_no']=$row[csf('file_no')];
			if($is_confirmed==1)
			{
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['po_qty_pcs']+=$row[csf('po_quantity')]*$row[csf('ratio')];
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['po_total_price']+=$row[csf('po_total_price')];
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['plan_cut']+=$row[csf('plan_cut')];
			}
			$master_stlye_arr[$row[csf('ref_no')]][$job_no]['shiping_status']=$row[csf('shiping_status')];
			$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no_prefix_num')];
			$style_ref_arr[$row[csf('job_no')]]=$row[csf('style_ref_no')];
			$master_ref_arr[$row[csf('job_no')]]=$row[csf('ref_no')];
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			
			$jobIdNo_arr[$row[csf('job_id')]]=$row[csf('job_no')];
			 
		}
	//   print_r($po_id_arr);
		//if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_year";
		//if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		if($db_type==0) $year_field="  SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	if($db_type==2) $year_field="  to_char(a.insert_date,'YYYY') as year";
		$sql_data_pre=sql_select("select $year_field,b.job_no,b.exchange_rate from wo_po_details_master a,wo_pre_cost_mst b where  a.id=b.job_id ".where_con_using_array($job_id_arr,0,'a.id')."");
		//echo "select $year_field,b.job_no,b.exchange_rate from wo_po_details_master a,wo_pre_cost_mst b where  a.id=b.job_id ".where_con_using_array($job_id_arr,0,'a.id')."";
		foreach($sql_data_pre as $row)
		{
			$master_ref=$master_ref_arr[$row[csf('job_no')]];
			$job_ex_rate_arr[$master_ref]['rate']=$row[csf('exchange_rate')];
			$job_ex_rate_arr[$master_ref]['year']=$row[csf('year')];
		}
		//print_r($job_ex_rate_arr);
	 
		$invoice_qnty = "select a.po_breakdown_id,c.discount_ammount, sum(a.current_invoice_qnty) as invoice_qnty, sum(a.current_invoice_value) as invoice_value from com_export_invoice_ship_dtls a, com_export_invoice_ship_mst c where a.mst_id=c.id and a.is_deleted=0 and a.status_active=1 and a.current_invoice_qnty>0  ".where_con_using_array($po_id_arr,0,'a.po_breakdown_id')." group by c.discount_ammount,a.po_breakdown_id";
		$invoiceSQLresult = sql_select($invoice_qnty);
		$invoiceArr = array();
		foreach ($invoiceSQLresult as $key => $val) {
			$invoiceArr[$val[csf('po_breakdown_id')]]['invoice_qnty'] = $val[csf('invoice_qnty')];
			$invoiceArr[$val[csf('po_breakdown_id')]]['invoice_value'] = $val[csf('invoice_value')];
			$invoiceArr[$val[csf('po_breakdown_id')]]['discount_ammount'] = $val[csf('discount_ammount')];
		}
		//print_r($invoiceArr);
		  unset($invoiceSQLresult); 
		  
		  $buyer_sub_sql = "select e.entry_form,a.po_breakdown_id,c.discount_ammount, sum(a.current_invoice_qnty) as invoice_qnty, sum(d.net_invo_value) as invoice_value from com_export_invoice_ship_dtls a, com_export_invoice_ship_mst c,com_export_doc_submission_invo d,com_export_doc_submission_mst e where a.mst_id=c.id and d.invoice_id=c.id and e.id=d.doc_submission_mst_id and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.current_invoice_qnty>0 and e.entry_form in(39,40) ".where_con_using_array($po_id_arr,0,'a.po_breakdown_id')." group by e.entry_form,c.discount_ammount,a.po_breakdown_id";
		$buyer_sub_result = sql_select($buyer_sub_sql);
		 
		foreach ($buyer_sub_result as $key => $val) {
			if($val[csf('entry_form')]==39)
			{
				$invoiceArr[$val[csf('po_breakdown_id')]]['buyer_value']= $val[csf('invoice_value')];
			}
			else
			{
				$invoiceArr[$val[csf('po_breakdown_id')]]['bank_value']= $val[csf('invoice_value')];
			}
		}
		  unset($buyer_sub_result);
		  $proc_reliza_sql = "select e.id as rlz_dtls_id,e.type,a.po_breakdown_id, (e.document_currency) as document_currency 
		   from com_export_invoice_ship_dtls a, com_export_doc_submission_invo c,com_export_proceed_realization d,com_export_proceed_rlzn_dtls e
		   where a.mst_id=c.invoice_id and c.doc_submission_mst_id=d.invoice_bill_id and e.mst_id=d.id and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 and d.is_deleted=0 and d.status_active=1   ".where_con_using_array($po_id_arr,0,'a.po_breakdown_id')." ";
		$proc_reliza_result = sql_select($proc_reliza_sql);
		 
		foreach ($proc_reliza_result as $key => $val) {
			if($po_rlz_chk[$val[csf('po_breakdown_id')]][$val[csf('rlz_dtls_id')]]=="")//rlz_dtls_id
			{
				if($val[csf('type')]==1)
				{
					$invoiceArr[$val[csf('po_breakdown_id')]]['distribute_value']+= $val[csf('document_currency')];
				}
				else
				{
					$invoiceArr[$val[csf('po_breakdown_id')]]['deduct_value']+= $val[csf('document_currency')];
				}
				$po_rlz_chk[$val[csf('po_breakdown_id')]][$val[csf('rlz_dtls_id')]]=$val[csf('rlz_dtls_id')];
			}
		}
		  unset($proc_reliza_result);
		  
		  //
		  
		/*if ($db_type == 0)
		$date_diff = "DATEDIFF(approval_status_date,target_approval_date)";
		else
		$date_diff = "trunc(approval_status_date-target_approval_date)";*/
		$sampleSQL = "select po_break_down_id, approval_status, sample_type_id as delay_day from wo_po_sample_approval_info where approval_status<>4 and current_status=1 and is_deleted=0 and status_active=1 ".where_con_using_array($po_id_arr,0,'po_break_down_id')." ";
		//echo $sampleSQL;die;
		$sampleSQLresult = sql_select($sampleSQL);
		$AppStatusArr=array();
		foreach ($sampleSQLresult as $key => $val) {
		if ($val[csf('approval_status')] == 3) {
		$AppStatusArr[$val[csf('po_break_down_id')]]['sam_apprv_status'] += 1;
		} 
		$AppStatusArr[$val[csf('po_break_down_id')]]['sam_total_po'] += 1;
		}
		unset($embelSQLresult);
		//print_r($sampleSQLresult);
		
		
		//for lapdip approval percentage----------------------------------//
		$lapdipSQL = "select po_break_down_id,approval_status from wo_po_lapdip_approval_info where current_status=1 and is_deleted=0 and status_active=1   ".where_con_using_array($po_id_arr,0,'po_break_down_id')." ";
		//echo $lapdipSQL;die;
		$lapdipSQLresult = sql_select($lapdipSQL);
		 
		foreach ($lapdipSQLresult as $key => $val) {
		if ($val[csf('approval_status')] == 3) {
		$AppStatusArr[$val[csf('po_break_down_id')]]['lab_apprv_status'] += 1;
		 
		}
		$AppStatusArr[$val[csf('po_break_down_id')]]['lab_total_po'] += 1;
		}
		unset($lapdipSQLresult);
		//for trims/accessories approval percentage----------------------------------//
		$trimsSQL = "select po_break_down_id,SUM(CASE WHEN approval_status=3 THEN 1 ELSE 0 END) as apprv_status,
		SUM(CASE WHEN approval_status<>4 THEN 1 ELSE 0 END) as total_po
		from wo_po_trims_approval_info where current_status=1 and is_deleted=0 and status_active=1 ".where_con_using_array($po_id_arr,0,'po_break_down_id')." group by po_break_down_id";
		$trimsSQLresult = sql_select($trimsSQL);
	//	$trimsAPParr = array();
		foreach ($trimsSQLresult as $key => $val) {
		$AppStatusArr[$val[csf('po_break_down_id')]]['trim_apprv_status'] = $val[csf('apprv_status')];
		$AppStatusArr[$val[csf('po_break_down_id')]]['trim_total_po'] = $val[csf('total_po')];
		}
		unset($trimsSQLresult);
		//for Embellishment Approval approval percentage----------------------------------//
		$embelSQL = "select po_break_down_id,SUM(CASE WHEN approval_status=3 THEN 1 ELSE 0 END) as apprv_status,
		SUM(CASE WHEN approval_status<>4 THEN 1 ELSE 0 END) as total_po
		from wo_po_embell_approval where current_status=1 and is_deleted=0 and status_active=1 ".where_con_using_array($po_id_arr,0,'po_break_down_id')." group by po_break_down_id";
		$embelSQLresult = sql_select($embelSQL);
		//$embelAPParr = array();
		foreach ($embelSQLresult as $key => $val) {
		$AppStatusArr[$val[csf('po_break_down_id')]]['embl_apprv_status'] = $val[csf('apprv_status')];
		$AppStatusArr[$val[csf('po_break_down_id')]]['embl_total_po'] = $val[csf('total_po')];
		}
		unset($embelSQLresult);
		//Approval End
		if(!empty($inquiry_id_arr))
		{
			$quaOfferQnty=0; $quaConfirmPrice=0; $quaConfirmPriceDzn=0; $quaPriceWithCommnPcs=0; $quaCostingPer=0; $quaCostingPerQty=0;
				
			  $sqlQc="select a.inquery_id,a.qc_no,a.offer_qty, 1 as costing_per, b.confirm_fob from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($inquiry_id_arr,0,'a.inquery_id')."";
			$dataQc=sql_select($sqlQc);
			//print_r($dataQc);
			foreach($dataQc as $qcrow)
			{
				//echo $qcrow[csf('offer_qty')].'=';
				$inquiry_wise_jobQty=$inquiry_wise_jobQty_arr[$qcrow[csf('inquery_id')]];
				$quaOfferQnty=$jobQty;//$qcrow[csf('offer_qty')];
				$quaConfirmPrice=$qcrow[csf('confirm_fob')];
				$quaConfirmPriceDzn=$qcrow[csf('confirm_fob')];
				$quaPriceWithCommnPcs=$qcrow[csf('confirm_fob')];
				$quaCostingPerArr[$qcrow[csf('inquery_id')]]=$qcrow[csf('costing_per')];
				$qc_no=$qcrow[csf('qc_no')];
				$quaCostingPerQty=0; 
				//if($quaCostingPer==1) 
				$quaCostingPerQty=1;
			}
			unset($dataQc);
			
			$sql_cons_rate="select a.inquery_id,b.id, b.mst_id, b.item_id, b.type, b.particular_type_id, b.consumption, b.ex_percent, b.tot_cons, b.unit, b.is_calculation, b.rate, b.rate_data, b.value from qc_cons_rate_dtls b,qc_mst a where  b.mst_id=a.qc_no and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($inquiry_id_arr,0,'a.inquery_id')." order by b.id asc";
			$sql_result_cons_rate=sql_select($sql_cons_rate);
			foreach ($sql_result_cons_rate as $rowConsRate)
			{
				if($rowConsRate[csf("type")]==1) //Fabric
				{
					if(($rowConsRate[csf("rate")]*1)>0 && $rowConsRate[csf('tot_cons')]>0)
					{
						//$edata=explode("~~",$rowConsRate[csf("rate_data")]);
						$quaOfferQnty=$inquiry_wise_jobQty_arr[$rowConsRate[csf('inquery_id')]];
						$quaCostingPerQty=$quaCostingPerArr[$rowConsRate[csf('inquery_id')]];
						$index=""; $mktcons=$mktamt=0;
						$mktcons=$mktamt=0;
						//$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
						$mktcons=$rowConsRate[csf('tot_cons')];
						$mktamt=$mktcons*$rowConsRate[csf('rate')];
						$ConsRate=$rowConsRate[csf('value')]/$rowConsRate[csf('tot_cons')];
						$fabPurQtyArr[$rowConsRate[csf('inquery_id')]]['mkt']['qty']+=$mktcons;
						$fabPurQtyArr[$rowConsRate[csf('inquery_id')]]['mkt']['amount']+=$ConsRate*$mktcons;
					}
					
				}
			}
		} //Inquery chk end
		//print_r($fabPurQtyArr);
		//Budget Start here....
		/*$job_id=implode(",",$job_id_arr);
		$condition= new condition();
		if(str_replace("'","",$job_id) !=''){
			$condition->jobid_in("$job_id");
		}
	
		$condition->init();
		$fabric= new fabric($condition);
		//echo $fabric->getQuery();die;
		//getQtyArray_by_order_knitAndwoven_greyAndfinish
		$wvn_fabric_QtyArr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();*/
	// print_r($wvn_fabric_QtyArr);die;
	 $sql_fab_budget = "select id, job_no,item_number_id,uom, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where fabric_source=2 and status_active=1 and is_deleted=0 ".where_con_using_array($job_id_arr,0,'job_id')."";
		$data_fabPur=sql_select($sql_fab_budget);
		foreach($data_fabPur as $row)
		{
			$fabric_booking_arr[$row[csf('job_no')]]+=$row[csf('avg_cons')];
			
		}
		
		unset($data_fabPur);
			
			
		$sql_cut=sql_select("select d.id,a.job_no,d.cutting_no as cutting_no,d.marker_length,d.cad_marker_cons,c.marker_qty from wo_po_details_master a, ppl_cut_lay_mst d,ppl_cut_lay_dtls c where a.job_no=d.job_no   and d.id=c.mst_id and d.entry_form=289 and c.status_active=1 and c.is_deleted=0    $style_owner_cond  $comp_cond   $buyer_id_cond $year_cond $job_no_cond    $style_ref_cond $job_id_cond  $brand_name_cond  ".where_con_using_array($job_id_arr,0,'a.id')."  order by d.id asc");
		
		//echo "select d.id,a.job_no,d.cutting_no as cutting_no,d.marker_length,d.cad_marker_cons,c.marker_qty from wo_po_details_master a, ppl_cut_lay_mst d,ppl_cut_lay_dtls c where a.job_no=d.job_no   and d.id=c.mst_id and d.entry_form=289 and c.status_active=1 and c.is_deleted=0    $style_owner_cond  $comp_cond   $buyer_id_cond $year_cond $job_no_cond    $style_ref_cond $job_id_cond  $brand_name_cond  ".where_con_using_array($job_id_arr,1,'a.id')."  order by d.id asc";
		 
		foreach($sql_cut as $row)
		{
			$master_ref=$master_ref_arr[$row[csf('job_no')]];
			$cut_no_lay_arr[$master_ref]['cutting_no'].=$row[csf('cutting_no')].',';
			$cut_lay_arr[$row[csf('job_no')]]['lay_cut']+=$row[csf('marker_qty')];
			if($row[csf('marker_length')])
			{
			$cut_lay_first_arr[$row[csf('cutting_no')]]['first_length']=$row[csf('marker_length')];
			}
			if($row[csf('cad_marker_cons')])
			{
			$cut_lay_first_arr[$row[csf('cutting_no')]]['cad_marker_cons']=$row[csf('cad_marker_cons')];
			}
		}
		unset($sql_cut);
		//buyer_po_id
		//Wash Order for Wash Production Last Sequence No
		$buyer_poId=where_con_using_array($po_id_arr,0,'a.buyer_po_id');
		
		  $wash_subcon="select max(b.prod_sequence_no) as prod_sequence_no,a.buyer_po_id from subcon_ord_breakdown b,subcon_ord_dtls a where a.id=b.mst_id and a.status_active=1 $buyer_poId group by a.buyer_po_id  order by prod_sequence_no desc";//die;
		$wash_subcon_result=sql_select($wash_subcon);
		foreach($wash_subcon_result as $row)
		{
			$last_wash_procesArr[$row[csf("buyer_po_id")]]=$row[csf("prod_sequence_no")];
		}
		unset($wash_subcon_result);
		//print_r($last_wash_procesArr);
		//$lastProdSeqNo=where_con_using_array($po_id_arr,0,'b.prod_sequence_no');
		
		 $wash_subcon_dtls="select b.prod_sequence_no as prod_sequence_no,a.buyer_po_id,b.embellishment_type,b.process  from subcon_ord_breakdown b,subcon_ord_dtls a where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $buyer_poId    order by b.prod_sequence_no desc";//die;
		$wash_subcon_dtls_result=sql_select($wash_subcon_dtls);
		foreach($wash_subcon_dtls_result as $row)
		{
			$prod_sequence_no=$last_wash_procesArr[$row[csf("buyer_po_id")]];
			if($prod_sequence_no==$row[csf("prod_sequence_no")])
			{
			
			$wash_prodArr[$row[csf("buyer_po_id")]]=$row[csf("process")].'_'.$row[csf("embellishment_type")];
			}
		}
		unset($wash_subcon_result);
	// print_r($wash_prodArr);
	  $sql_prod="select a.entry_form, b. po_id, b.buyer_po_id, b.qcpass_qty, b.order_qty,b.process_id, b.wash_type_id  from  subcon_embel_production_mst a,subcon_embel_production_dtls b where  a.id=b.mst_id and  a.entry_form in(301,342) and a.status_active=1 and b.status_active=1";
	$sql_prod_result=sql_select($sql_prod);
	foreach($sql_prod_result as $row)
	{
		$wash_prod=explode('_',$wash_prodArr[$row[csf("buyer_po_id")]]);
		$process=$wash_prod[0];
		$wash_typeId=$wash_prod[1];
		if($process==$row[csf("process_id")] && $wash_typeId==$row[csf("wash_type_id")])
		{
		$prod_prodAtyArr[$row[csf("buyer_po_id")]]+=$row[csf("qcpass_qty")];
		}
	}
	unset($sql_prod_result);
	//print_r($prod_prodAtyArr);
	//$job_id_arr
	$sample_styleCond=where_con_using_array($job_id_arr,0,'c.quotation_id');
	
	 $sql_sample="select c.quotation_id as job_id,b.ex_factory_qty,b.sample_ex_factory_mst_id, b.sample_development_id from sample_ex_factory_mst a,sample_ex_factory_dtls b,sample_development_mst c where a.id=b.sample_ex_factory_mst_id and c.id=b.sample_development_id  and b.entry_form_id=396 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  $sample_styleCond"; 
	$sample_result_sql=sql_select($sql_sample);
	foreach($sample_result_sql as $row)
		{
			$jobno=$jobIdNo_arr[$row[csf('quotation_id')]];
			$sample_prod_Arr[$jobno]+=$row[csf("ex_factory_qty")];
		}
	
		
		//print_r($cut_no_lay_arr);
		
	/**/	$booking_fin_qnty_arr=array();
		$booking_sql=sql_select("SELECT a.booking_type,c.pay_mode,c.supplier_id,a.job_no,a.po_break_down_id as po_id,a.booking_no ,a.fin_fab_qnty,b.grouping as ref_no from wo_booking_mst c,wo_booking_dtls a,wo_po_break_down b  where b.id=a.po_break_down_id and c.booking_no=a.booking_no and  a.booking_type in(1,6) and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'a.po_break_down_id')." ");
	//echo "SELECT c.pay_mode,c.supplier_id,a.job_no,a.po_break_down_id as po_id,a.booking_no ,a.fin_fab_qnty,b.grouping as ref_no from wo_booking_mst c,wo_booking_dtls a,wo_po_break_down b  where b.id=a.po_break_down_id and c.booking_no=a.booking_no and  a.booking_type=6 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'a.po_break_down_id')." ";
		
		foreach($booking_sql as $row)
		{
			if($row[csf("booking_type")]==6) //Emblishment
			{
			
				if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5 )
				{
					$wash_factory_arr[$row[csf("job_no")]]["factory"].=$company_short_library[$row[csf("supplier_id")]].',';
				}
				else
				{
					$wash_factory_arr[$row[csf("job_no")]]["factory"].=$supplier_library[$row[csf("supplier_id")]].',';
				}
			}
			else
			{
			//$booking_fin_qnty_arr[$row[csf("po_id")]]["qnty"]+=$row[csf("fin_fab_qnty")];
			//$booking_no_arr[$row[csf("ref_no")]]["booking_no"]=$row[csf("booking_no")];
				$booking_no_arr[$row[csf("ref_no")]]["booking_no"]=$row[csf("booking_no")];
			}
		}
		unset($booking_sql);

		$wash_company_sql="SELECT a.po_break_down_id as po_id, a.serving_company,a.production_source from pro_garments_production_mst a WHERE a.production_type=3 and a.embel_name=3 and a.production_source IN(1,3) and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($po_id_arr,0,'a.po_break_down_id')."";
        $total_wash_company_sql=sql_select($wash_company_sql);
		$wash_company_arr=array();
		foreach($total_wash_company_sql as $row)
		{
			if($row[csf("production_source")]==1)
			{
				$wash_company_arr['serving_company'].=$company_library[$row[csf('serving_company')]].",";

			}
			if($row[csf("production_source")]==3)
			{
				
				$wash_company_arr['serving_company'].=$supplier_arr[$row[csf('serving_company')]].",";

			
			}
		     
		}
		//    print_r($wash_company_arr);



			
		$cut_qc_arr=sql_select("SELECT a.po_break_down_id as po_id,(b.production_qnty) as qnty,b.reject_qty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and b.cut_no is not null and b.color_size_break_down_id is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'a.po_break_down_id')." ");
		//echo "SELECT a.po_break_down_id as po_id,(b.production_qnty) as qnty,b.reject_qty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and b.cut_no is not null and b.color_size_break_down_id is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'a.po_break_down_id')." ";
	 
		foreach( $cut_qc_arr as $row )
		{
			 $cuting_prod_arr[$row[csf("po_id")]]["qnty"]+=$row[csf("qnty")];
			 $cuting_prod_arr[$row[csf("po_id")]]["reject_qty"]+=$row[csf("reject_qty")];
		}
		unset($cut_qc_arr);
			/*$cutting_del_input=sql_select("SELECT b.po_break_down_id as po_id,(b.cut_delivery_qnty) as qnty from pro_gmts_delivery_mst a,pro_cut_delivery_order_dtls b where a.id=b.delivery_mst_id and a.entry_form=347 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'b.po_break_down_id')." ");
			//echo "SELECT b.po_break_down_id as po_id,(b.cut_delivery_qnty) as qnty from pro_cut_delivery_mst a,pro_cut_delivery_order_dtls b where a.id=b.delivery_mst_id and b.production_type=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'b.po_break_down_id')." ";
		foreach($cutting_del_input as $row )
		{
			$cuting_del_input_arr[$row[csf("po_id")]]["qnty"]+=$row[csf("qnty")];
		}
		unset($cutting_del_input);*/
		//print_r($cuting_del_input_arr);
		//
		$act_ahip_arr=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as shipout_qty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty
		from  pro_ex_factory_mst where status_active=1 and is_deleted=0 ".where_con_using_array($po_id_arr,0,'po_break_down_id')." group by po_break_down_id");// 
		
		foreach($act_ahip_arr as $row)
		{
			$actual_shipout_arr[$row[csf('po_break_down_id')]]['shipout_qty']=$row[csf('shipout_qty')]-$row[csf('return_qnty')];
		}
	 unset($act_ahip_arr);
	 $sew_out_sql=sql_select("select a.po_break_down_id as po_id,
	  (CASE WHEN  a.production_type=4 THEN b.production_qnty else 0 END)  as sewing_in,
	  (CASE WHEN  a.production_type=4 THEN b.reject_qty else 0 END)  as rej_sewing_in,
	  (CASE WHEN  a.production_type=4 THEN a.reject_qnty else 0 END)  as mst_rej_sewing_in,
	 (CASE WHEN  a.production_type=5 THEN b.production_qnty else 0 END)  as sewing_out,
	 (CASE WHEN  a.production_type=5 THEN b.reject_qty else 0 END)  as rej_sewing_out,
	 (CASE WHEN  a.production_type=5 THEN a.reject_qnty else 0 END)  as mst_rej_sewing_out,
	 (CASE WHEN  a.production_type=2 THEN b.production_qnty else 0 END)  as send_wash,
	 (CASE WHEN  a.production_type=3 THEN b.production_qnty else 0 END)  as recv_wash ,
	 (CASE WHEN  a.production_type=3 THEN b.reject_qty else 0 END)  as recv_wash_reject ,
	 (CASE WHEN  a.production_type=11 THEN b.production_qnty else 0 END)  as poly_fin,
	 (CASE WHEN  a.production_type=8 THEN b.production_qnty else 0 END)  as fin_qty,
	 (CASE WHEN  a.production_type=8 THEN b.reject_qty else 0 END)  as rej_fin_qty,
	 (CASE WHEN  a.production_type=9 THEN b.reject_qty else 0 END)  as cutting_del_input_qty
	   
	  from pro_garments_production_mst a,pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type in(2,3,4,5,9,8) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'po_break_down_id')."");


	
	
	
	 
	
	
	foreach($sew_out_sql as $row)
	{
		$sewing_out_arr[$row[csf('po_id')]]['sewing_in']+=$row[csf('sewing_in')];
		$sewing_out_arr[$row[csf('po_id')]]['sewing_out']+=$row[csf('sewing_out')];
		$mst_rej_sewing_out=$row[csf('mst_rej_sewing_out')];
		$mst_rej_sewing_in=$row[csf('mst_rej_sewing_in')];
		/*if($row[csf('rej_sewing_in')] || $row[csf('rej_sewing_out')] )
		{
			$rej_sewing_inOut=$row[csf('rej_sewing_in')]+$row[csf('rej_sewing_out')];
		}
		else
		{
			
		}*/
		$rej_sewing_inOut=$mst_rej_sewing_in+$mst_rej_sewing_out;
		if($rej_sewing_inOut>0)
		{
		$sewing_out_arr[$row[csf('po_id')]]['sewing_out_rej']=$rej_sewing_inOut;
		}
		$sewing_out_arr[$row[csf('po_id')]]['fin_qty']+=$row[csf('fin_qty')];
		$sewing_out_arr[$row[csf('po_id')]]['fin_qty_rej']+=$row[csf('rej_fin_qty')];
		
		$sewing_out_arr[$row[csf('po_id')]]['send_wash']+=$row[csf('send_wash')];
		$sewing_out_arr[$row[csf('po_id')]]['recv_wash']+=$row[csf('recv_wash')];
		$sewing_out_arr[$row[csf('po_id')]]['recv_wash_rej']+=$row[csf('recv_wash_reject')];
		$cuting_del_input_arr[$row[csf("po_id")]]["qnty"]+=$row[csf("cutting_del_input_qty")];
	}
	 unset($sew_out_sql);
	  $sew_defect_sql=sql_select("select b.po_break_down_id as po_id,
	  (CASE WHEN  b.production_type in(5) THEN b.defect_qty else 0 END)  as defect_qty,
	  (CASE WHEN  b.production_type in(1) THEN b.defect_qty else 0 END)  as cut_defect_qty,
	   (CASE WHEN  b.production_type in(80) THEN b.defect_qty else 0 END)  as fin_defect_qty
	  from pro_gmts_prod_dft b
	where  b.production_type in(1,5,80) and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.po_break_down_id')."");

	foreach($sew_defect_sql as $row)
	{
		$sewing_defect_arr[$row[csf("po_id")]]+=$row[csf("defect_qty")];
		$fin_defect_arr[$row[csf("po_id")]]+=$row[csf("fin_defect_qty")];
		$cuting_defect_arr[$row[csf("po_id")]]+=$row[csf("cut_defect_qty")];
	}
	//print_r($cuting_defect_arr); 
	 unset($sew_defect_sql);
		$wash_recv_sql=sql_select("select b.buyer_po_id as po_id, b.quantity  as wash_qty
		from sub_material_mst a,sub_material_dtls b
		where  a.id=b.mst_id and  a.entry_form=296  and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."");
		/*echo "select b.buyer_po_id as po_id, b.quantity  as wash_qty
		from sub_material_mst a,sub_material_dtls b
		where  a.id=b.mst_id and  a.entry_form=296  and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."";*/
		
		foreach($wash_recv_sql as $row)
		{
		$wash_recv_arr[$row[csf("po_id")]]+=$row[csf("wash_qty")];
		}
		unset($wash_recv_sql);
	 
		 $wash_prod_sql=sql_select("select a.entry_form,b.buyer_po_id as po_id, b.qcpass_qty  as wash_qty,b.reje_qty
		  from subcon_embel_production_mst a,subcon_embel_production_dtls b
		where  a.id=b.mst_id and  a.entry_form in(301,302)  and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."");

		
			foreach($wash_prod_sql as $row)
			{
				if($row[csf("entry_form")]==301)
				{
				$wash_wet_prod_arr[$row[csf("po_id")]]+=$row[csf("wash_qty")];
				}
				else{
				 $wash_recv_qc_arr[$row[csf("po_id")]]['wash_qty']+=$row[csf("wash_qty")]; 
				 $wash_recv_qc_arr[$row[csf("po_id")]]['reje_qty']+=$row[csf("reje_qty")];
				}
			}
		 unset($wash_prod_sql); 
		//print_r($cuting_prod_arr);subcon_delivery_mst
		 $wash_del_sql=sql_select("select b.buyer_po_id as po_id, b.delivery_qty  as wash_qty
		  from subcon_delivery_mst a,subcon_delivery_dtls b
		where  a.id=b.mst_id and  a.entry_form=303  and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."");
		
			foreach($wash_del_sql as $row)
			{
				$wash_del_arr[$row[csf("po_id")]]+=$row[csf("wash_qty")];
			}
		 unset($wash_del_sql); 
		//print_r($cuting_prod_arr);subcon_delivery_mst
		
		$wv_recv_qnty_arr=array();$wv_issue_qnty_arr=array();
		$wv_fin_sql=sql_select("SELECT po_breakdown_id as po_id, entry_form,(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(18,17,19)  ".where_con_using_array($po_id_arr,0,'po_breakdown_id')." ");
		//echo "SELECT po_breakdown_id as po_id, entry_form,(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(18,17,19)  ".where_con_using_array($po_id_arr,0,'po_breakdown_id')." ";
			foreach($wv_fin_sql as $row)
			{		 	
			 	$entry_form_id=$row[csf("entry_form")];
				if($entry_form_id==17)
				{
				$wv_recv_qnty_arr[$row[csf("po_id")]]+=$row[csf("qnty")];
				}
				if($entry_form_id==18 || $entry_form_id==19)  //Issue
				{
				$wv_issue_qnty_arr[$row[csf("po_id")]]+=$row[csf("qnty")];
				}
			}
			unset($wv_fin_sql);
			// Transfer Fin Actual
		$fin_fab_trans_array=array();
		 $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
			sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, c.from_order_id
		  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258,15) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($po_id_arr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
		$result_fin_trans=sql_select( $sql_fin_trans );
		$fin_from_order_id='';$fin_fab_trans_qty=$fin_fab_trans_amt=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
		foreach ($result_fin_trans as $row)
		{
			$fin_fab_trans_qnty_arr[$row[csf('po_breakdown_id')]]['in']=$row[csf('in_qty')];
			$fin_fab_trans_qnty_arr[$row[csf('po_breakdown_id')]]['out']=$row[csf('out_qty')];
		}
		
		$total_buyer_costing_qty=$total_booking_fabric_Qty=$total_booking_cost_yds=$total_buyerYdsQty=$total_fabric_recv_req_yds_qty=$total_balance_in_storeQty=$total_fin_fab_in=$total_wv_issue_fin=$total_fin_fab_out=$total_cut_prod_qty=$total_lay_cut_prod_qty=$total_used_yds=$total_leftOver_fabYds=$total_costing_vs_actual_pcs=$total_tot_costinf_vs_costing_yds=$total_tot_actual_costing_yds=$total_tot_costing_vs_costing_yds=$total_tot_actual_costing_yds=$total_possible_cut_budget=$total_defect_qty=$total_wash_wet_prod=$total_wash_del=$total_wash_recv_from_qc=$total_fin_qty_rej=$total_ship_short_ex_qty=$total_cut_to_shipQty=$total_invoice_qnty=$total_invoice_value=$total_inv_discount_ammount=$total_net_invoice_amount=$total_doc_sub_buyer_value=$total_doc_sub_bank_value=$total_doc_distribute_value=$total_doc_deduct_value=$total_wash_in_hand=0;
				
		foreach($master_stlye_arr as $master_style=>$data_style)
		{
			//  echo "abc".$master_style;
			foreach($data_style as $job_no=>$row)
			{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			if($company_name) $company_name=$company_name;else $company_name=0;
			if($style_owner) $style_owner=$style_owner;else $style_owner=0;
			
			$job_no=rtrim($row[('job_no')],',');
			$job_nos=implode(",",array_unique(explode(",",$job_no)));
			
			// $serving_company=rtrim($row[('serving_company')],',');
			// echo $serving_company;
			
			
			$season_name=rtrim($row[('season')],',');
			$season_name=implode(",",array_unique(explode(",",$season_name)));
			
			$season_year=rtrim($row[('season_year')],',');
			$season_year=implode(",",array_unique(explode(",",$season_year)));
			$brand_name=rtrim($row[('brand_id')],',');
			$brand_name=implode(",",array_unique(explode(",",$brand_name)));
			
			$job_no_prefix=rtrim($row[('job_no_prefix')],',');
			$buyer_name=$row[('buyer_name')]; 
			$job_noArr=array_unique(explode(",",$job_no));
			$job_no_prefixArr=array_unique(explode(",",$job_no_prefix));
			$job_no_prefixAll=implode(",",array_unique(explode(",",$job_no_prefix)));
			$style_ref_no=rtrim($row[('style_ref_no')],',');
			$po_number=rtrim($row[('po_number')],',');
			$po_id=rtrim($row[('po_id')],',');
			$proj_po_id=rtrim($row[('proj_po_id')],',');//proj_po_id
			$conf_po_id=rtrim($row[('conf_po_id')],',');
			
			$po_idArr=array_unique(explode(",",$po_id));
			$po_id_all=implode(",",$po_idArr);
			//echo $po_id_all.', ';
			$proj_po_idArr=array_unique(explode(",",$proj_po_id));
			$conf_po_idArr=array_unique(explode(",",$conf_po_id));
			
			$style_ref_nos=implode(",",array_unique(explode(",",$style_ref_no)));
			$po_numbers=implode(",",array_unique(explode(",",$po_number)));
			$lay_cut_qty=0;$print_button_job="";$buyer_costing_qty=$booking_fabric_Qty=0;
			$wash_factoryAll="";$sample_pcs=0;
			foreach($job_noArr as $job)
			{
				$inquiry_id=$inquiry_id_jobWise_arr[$job];
				//echo $inquiry_id.'DD';
				$buyer_costing_qty+=$fabPurQtyArr[$inquiry_id]['mkt']['qty'];
				$booking_fabric_Qty+=$fabric_booking_arr[$job];
				
			$job_prefix=$job_no_arr[$job];$style_ref_nos=$style_ref_arr[$job];
			$cutting_no=$cut_lay_arr[$job]['cutting_no'];
			$lay_cut_qty+=$cut_lay_arr[$job]['lay_cut'];
			
		if($print_button_job=="") $print_button_job='<a href="##" onclick="report_generate_pop('.$company_name.",".$style_owner.",'".$job."','".$style_ref_nos."',".$buyer_name.",'".$po_id_all."','basic_cost',1".')">'.$job_prefix.'</a>';
		else $print_button_job.=",".'<a href="##" onclick="report_generate_pop('.$company_name.",".$style_owner.",'".$job."','".$style_ref_nos."',".$buyer_name.",'".$po_id_all."','basic_cost',1".')">'.$job_prefix.'</a>';
		
		$wash_factory=rtrim($wash_company_arr["serving_company"],',');
		$wash_factoryAll.=$wash_factory.',';
		
		$sample_pcs+=$sample_prod_Arr[$job];
			
			}
			$wash_factoryAll=rtrim($wash_factoryAll,',');
			$wash_factoryAllArr=implode(",",array_unique(explode(",",$wash_factoryAll)));
			
			$cutting_no=$cut_no_lay_arr[$master_ref]['cutting_no'];
			$cutting_noS=rtrim($cutting_no,',');$cutting_noArr=array_unique(explode(",",$cutting_noS));
			$min_cutting_no=min($cutting_noArr);
				
			$cut_prod_qty=0;$wv_recv_fin=0;$wv_issue_fin=0;$ship_qty=$sewing_out=$send_wash=$recv_wash=$fin_qty_rej=$fin_qty=0;;
			//$origin_po_all='';$sewing_out_rej=0;
			$wvn_fabric_Qty=$fin_fab_in=$fin_fab_out=$cuting_del_input=0;$wv_recv_fin=$cuting_del_input=$sewing_in=$sewing_defect=$mat_wash_recv=$wash_wet_prod=$wash_del=0;
			$sam_apprv_status=$sam_total_po=$lab_apprv_status=$lab_apprv_status=$trim_apprv_status=$trim_apprv_status=$embl_apprv_status=$embl_apprv_status=$wash_recv_from_qc=$wash_recv_from_rejet_qty=$fin_defect=$invoice_qnty=$invoice_value=$inv_discount_ammount=$doc_sub_buyer_value=$doc_sub_bank_value=$doc_distribute_value=$doc_deduct_value=$cut_reject_qty=$total_marker_acl_pcs_cons=$total_tot_costing_vs_actual_yds=$cutting_defect=0;
			$wv_issue_fin=0;
			foreach($po_idArr as $pid) //all po
			{
			//$cutting_no=$cut_lay_arr[$job]['cutting_no'];
			
		//$booking_fabric_Qty+=array_sum($wvn_fabric_QtyArr['woven']['finish'][$pid]);
			$cut_prod_qty+=$cuting_prod_arr[$pid]['qnty'];
			$cut_reject_qty+=$cuting_prod_arr[$pid]['reject_qty'];
			$wv_recv_fin+=$wv_recv_qnty_arr[$pid];
			$ship_qty+=$actual_shipout_arr[$pid]['shipout_qty'];
			$cuting_del_input+=$cuting_del_input_arr[$pid]["qnty"];
			
			$sewing_out+=$sewing_out_arr[$pid]['sewing_out'];
			$sewing_in+=$sewing_out_arr[$pid]['sewing_in'];
			
			$sewing_out_rej+=$sewing_out_arr[$pid]['sewing_out_rej'];
			$sewing_defect+=$sewing_defect_arr[$pid];
			$fin_defect+=$fin_defect_arr[$pid];
			$cutting_defect+=$cuting_defect_arr[$pid];//$cuting_defect_arr[$row[csf("po_id")]]
			//echo $sewing_in.'D,';
			$fin_qty+=$sewing_out_arr[$pid]['fin_qty'];
			
			$mat_wash_recv+=$wash_recv_arr[$pid];
			$wash_wet_prod+=$prod_prodAtyArr[$pid];//$wash_wet_prod_arr[$pid]; 
			$wash_del+=$wash_del_arr[$pid];
			$wash_recv_from_qc+=$wash_recv_qc_arr[$pid]['wash_qty'];
			$wash_recv_from_rejet_qty+=$sewing_out_arr[$pid]['recv_wash_rej'];
			$fin_qty_rej+=$sewing_out_arr[$pid]['fin_qty_rej'];
			
			$send_wash+=$sewing_out_arr[$pid]['send_wash'];
			$recv_wash+=$sewing_out_arr[$pid]['recv_wash'];
			if($origin_po_all=='') $origin_po_all=$pid;else $origin_po_all.=",".$pid;
			$sam_apprv_status+=$AppStatusArr[$pid]['sam_apprv_status'];
			$sam_total_po+=$AppStatusArr[$pid]['sam_total_po'];
			$lab_apprv_status+=$AppStatusArr[$pid]['lab_apprv_status'];
			$lab_total_po+=$AppStatusArr[$pid]['lab_total_po'];
			$trim_apprv_status+=$AppStatusArr[$pid]['trim_apprv_status'];
			$trim_total_po+=$AppStatusArr[$pid]['sam_total_po'];
			$embl_apprv_status+=$AppStatusArr[$pid]['embl_apprv_status'];
			$embl_total_po+=$AppStatusArr[$pid]['embl_total_po'];
 			$fin_fab_in+=$fin_fab_trans_qnty_arr[$pid]['in'];
			$fin_fab_out+=$fin_fab_trans_qnty_arr[$pid]['out'];
			$invoice_qnty+=$invoiceArr[$pid]['invoice_qnty'];
			$invoice_value+=$invoiceArr[$pid]['invoice_value'];
			$inv_discount_ammount+=$invoiceArr[$pid]['discount_ammount'];
			$doc_sub_buyer_value+=$invoiceArr[$pid]['buyer_value'];
			$doc_sub_bank_value+=$invoiceArr[$pid]['bank_value'];
			
			$doc_distribute_value+=$invoiceArr[$pid]['distribute_value'];
			$doc_deduct_value+=$invoiceArr[$pid]['deduct_value'];
			 $wv_issue_fin+=$wv_issue_qnty_arr[$pid];
			 //$prod_prodAtyArr[$row[csf("buyer_po_id")]]
			}
			//$send_wash=$mat_wash_recv;
			//$recv_wash=9480;
			//$sewing_defect=1;
			/*$short_recv_from_wash=10;
			$wash_reject_pcs=91;
			$sewing_reject_pcs=$sewing_out_rej;
			$finish_reject_pcs=$fin_qty_rej;
			$fabric_defect_reject_pcs=117;*/
			
			//$wash_factory='CWL-2';
			//$ok_gmt_after_ship=0;
			

			$req_fin_fab_qty=0;//$wv_issue_fin=0;
			foreach($proj_po_idArr as $pid) //Projected po
			{
			//$cutting_no=$cut_lay_arr[$job]['cutting_no'];
			$req_fin_fab_qty+=$booking_fin_qnty_arr[$pid]["qnty"];
			// echo $wv_issue_qnty_arr[$pid].'dd';;
			 
			}
			$conf_po_all='';
			foreach($conf_po_idArr as $pid) //Confirm po
			{
			
			 if($conf_po_all=='') $conf_po_all=$pid;else $conf_po_all.=",".$pid;
			}
			
			
			//$first_length=$cut_lay_first_arr[$min_cutting_no]['first_length'];
			$cad_marker_cons=$cut_lay_first_arr[$min_cutting_no]['cad_marker_cons']/12;
		// $cuting_prod_arr[$row[csf("order_id")]]["qnty"];
			$pub_ship_date=rtrim($row[('pub_ship_date')],',');
			$pub_ship_dateArr=array_unique(explode(",",$pub_ship_date));
			$conf_po_allArr=array_unique(explode(",",$conf_po_all));
			$last_ship_date=max($pub_ship_dateArr);
			$booking_noArr=explode("-",$booking_no_arr[$master_style]["booking_no"]);
			$booking_no=ltrim($booking_noArr[3],'0');
			
			$booking_cost_yds=$booking_fabric_Qty*$row[('plan_cut')];
			
			 $used_yds=$lay_cut_qty*$cad_marker_cons;
			$leftOver_fabYds=$wv_issue_fin-$used_yds;
			$actual_cost_vari_pcs=($wv_issue_fin-$leftOver_fabYds)/$lay_cut_qty;
			$costing_vs_actual_pcs=($booking_fabric_Qty-$cad_marker_cons);
			
			$tot_actual_costing_yds=($lay_cut_qty*$actual_cost_vari_pcs);
			$tot_costing_vs_actual_yds=($booking_cost_yds-$tot_actual_costing_yds);
			 $possible_cut_budget=$booking_cost_yds/$cad_marker_cons;
			 

			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
				<td width="20" ><? echo $i; ?></td>
				<td width="110"><p><? echo $buyer_library[$row[('buyer_name')]]; ?></p></td>
                <td width="100"><p><? echo $brand_name; ?></p></td>
                <td width="100"><p><? echo $season_name; ?></p></td>
                <td width="60"><p><? echo $season_year; ?></p></td>
                
				<td width="100" ><p><? $print_button_masterStyle='<a href="##" onclick="report_generate_pop('.$company_name.",'".$job_ex_rate_arr[$master_style]['rate']."','".$job_no_prefixAll."','".$master_style."','".$job_ex_rate_arr[$master_style]['year']."','".$po_id_all."','report_generate',3".')">'.$master_style.'</a>'; echo $print_button_masterStyle;
				
				 ?></p></td>
                  <td width="100"><p><? echo  $color_arr[$row[('body_wash_color')]]; ?></p></td>
				<td width="100"><p><? echo  $print_button_job; ?></p></td>
               
                <td width="100"><p><? echo  $style_ref_nos; ?></p></td>
				<td width="100" align="center"><p><a href="##" onClick="generate_popup('<? echo $conf_po_all; ?>','po_popup',1)"><? echo count($conf_po_allArr); ?></a></p></td>
				<td width="80" align="right"><p><? echo number_format($row[('po_qty_pcs')],0); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($row[('plan_cut')],0);; ?></p></td>
				<td width="80" ><p><? echo $shipment_status[$row[('shiping_status')]]; ?></p></td>
                
                <td width="80"  align="right"><p><? $sample_percents = $sam_apprv_status*100/$sam_total_po; 
				if(is_infinite($sample_percents) || is_nan($sample_percents)){$sample_percents=0;}
                    echo number_format(($sample_percents), 2) . " %"; ?></p></td>
                <td width="80"  align="right"><p><? $lab_percents = $lab_apprv_status*100/$lab_total_po; 
					if(is_infinite($lab_percents) || is_nan($lab_percents)){$lab_percents=0;}
                    echo number_format(($lab_percents), 2) . " %";  ?></p></td>
                 <td width="80"  align="right" ><p><? $trim_percents = $trim_apprv_status*100/$trim_total_po; 	if(is_infinite($trim_percents) || is_nan($trim_percents)){$trim_percents=0;}
                    echo number_format(($trim_percents), 2) . " %";    ?></p></td>
                 <td width="80"  align="right"><p><? $embl_percents = $embl_apprv_status*100/$embl_total_po; if(is_infinite($embl_percents) || is_nan($embl_percents)){$embl_percents=0;}
                    echo number_format(($embl_percents), 2) . " %";; ?></p></td>
                
				<td width="80"   align="right"><p> <? echo  number_format($buyer_costing_qty,4); ?> 	</p></td>
				
				<td width="80" align="right"><p><? echo number_format($booking_fabric_Qty,4);//$cad_marker_cons; ?></p></td>
				<td width="80" align="right" title="Issue-LeftOver/Cut Lay"><p><a href="##" onClick="report_generate_pop('<? echo $company_name; ?>','<? echo $style_owner; ?>','<? echo $job_nos; ?>','<? echo $style_ref_nos; ?>','<? echo $buyer_name; ?>','<? echo $po_id_all?>','report_generate',2)"><? //echo number_format($lay_cut_qty,2); ?></a><? echo number_format($actual_cost_vari_pcs,4);?></p></td>
				
                <td width="80" align="right"><p><? echo number_format($cad_marker_cons,4); ?></p></td>
                
                <td width="80" align="right" title="Booking Cons Pcs-marker Cons"><p><? echo number_format($costing_vs_actual_pcs,2); ?></p></td>
                <td width="80" align="right" title="Actual Cons-marker Cons"><p><? $marker_acl_pcs_cons=$cad_marker_cons-$cad_marker_cons;echo number_format($marker_acl_pcs_cons,2); ?></p></td>
                
				<td width="80" align="right" title="Buyer Cost Avg Cons*Plan Cut"><p><a href="##" onClick="report_generate_popup('<? echo $company_name; ?>','<? echo $style_owner; ?>','<? echo $booking_no; ?>','report_generate_woven',3)"><? //echo number_format($wv_recv_fin,2); ?></a><?
				 $tot_buyerYdsQty=$buyer_costing_qty*$row[('plan_cut')];
				echo  number_format($tot_buyerYdsQty,2);?></p></td>
                <td width="80" align="right"><p><? echo number_format($booking_cost_yds,2); ?></p></td>
				<td width="80" align="right" title="Lay Cut*Actual Cons"><p><? //$fab_bal_store_qty=$wv_recv_fin-$wv_issue_fin;
				echo number_format($tot_actual_costing_yds,2); 
				//$fabric_used_qty=($cad_marker_cons/12)*$lay_cut_qty;wv_issue_fin
				?> </p></td>
                
				<td width="80" align="right" title="Tot Booking Cons-Actual Cons"><p><? 
				 
				echo number_format($tot_costing_vs_actual_yds,2); ?></p></td>
                <td width="80" align="right" title="marker Cons*LayCut-Actual Cons"><p><?  $marker_acl_vari_cons=($cad_marker_cons*$lay_cut_qty)-$tot_actual_costing_yds;
				echo number_format($marker_acl_vari_cons,2); ?></p></td>
                
				<td width="80" align="right" title="PlanCut*Booking pcs Qty"><p><? $fabric_recv_req_yds_qty=$row[('plan_cut')]*$booking_fabric_Qty;
				echo number_format($fabric_recv_req_yds_qty,2); ?></p></td>
				<td width="80" align="right" title="Recv Qty"><p><a href="##" onClick="report_generate_popup('<? echo $company_name; ?>','<? echo $style_owner; ?>','<? echo $booking_no; ?>','report_generate_woven',3)"><? echo number_format($wv_recv_fin,2); ?></a><?  //echo number_format($wv_recv_fin,2); ?></p></td>
				<td width="80" align="right" title=""><p><?  echo number_format($fin_fab_in,2);
				 ?></p></td>
				<td width="80" align="right" title=""><p><font style="color:<? echo $color_td; ?>"><?
				 echo number_format($fin_fab_out,2); ?> </font> </p></td>
                 
				<td width="80" align="right"><p><? echo number_format($wv_issue_fin,2); ?></p></td>
				<td width="80" align="right"  title="Lay Cut*Markar Cons"><p><? echo number_format($used_yds,2); ?></p></td>
				<td width="80" align="right" title="Issue-Used Yds"><p><? echo number_format($leftOver_fabYds,2); ?></p></td>
				<td width="80" align="right" title="Recv+Transf In-(Issue+Transfer Out)"><p><? $balance_in_storeQty=($wv_recv_fin+$fin_fab_in)-($wv_issue_fin+$fin_fab_out); echo number_format($balance_in_storeQty,2); ?></p></td>
                <td width="80" align="right" title="Booking Cons/Marker Cons"><p><? echo number_format($possible_cut_budget,2); ?></p></td>
                <td width="80" align="right" title="<? echo $cutting_no;?>"><p><a href="##" onClick="report_generate_pop('<? echo $company_name; ?>','<? echo $style_owner; ?>','<? echo $job_nos; ?>','<? echo $style_ref_nos; ?>','<? echo $buyer_name; ?>','<? echo $po_id_all?>','report_generate',2)"><? echo number_format($lay_cut_qty,2); ?></a><? //echo number_format($lay_cut_qty,0); ?></p></td>
                <td width="80" align="right"><p><a href="##" onClick="generate_popup('<? echo $po_id_all; ?>','cuting_qc_popup',2)"><? echo $cut_prod_qty; ?></a><? //echo number_format($cut_prod_qty,0); ?></p></td>
                <td width="80" align="right" title="QC Defect"><p><? $defect_qty=$cutting_defect;echo number_format($defect_qty,0); ?></p></td>
                
                
				<td width="80" align="right" title="Count Of Defect Qty/Audit Qty*100"><p><?  $dhu_per=($defect_qty/$cut_prod_qty*100);echo number_format($dhu_per,2); ?></p></td>

				<td width="80" align="right" title="Lay Cut-PO Qty/Po Qty*100"><p><? $excess_cut_per=(($lay_cut_qty-$row[('po_qty_pcs')])/$row[('po_qty_pcs')])*100; echo number_format($excess_cut_per,2); ?></p></td>
				<td width="80" align="right"><p> <?  
				$cuting_del_input=$sewing_in; 
				echo number_format($cuting_del_input,0); ?></p></td>
                
                
				<td width="80" align="right"><p><? echo number_format($sewing_in,0); ?></p></td>

				<td width="80" align="right" title=""><p><a href="##"  onClick="generate_line_popup('<? echo $po_id_all; ?>','<? echo $job_nos; ?>','<? echo $style_ref_nos; ?>','line_date_wise_popup',3)"><? echo number_format($sewing_out,2); ?></a><? //echo number_format($sewing_out,0); ?></p></td>
				<td width="80" align="right" title="Reject Qty"><p><? $tot_sew_reject=$sewing_out_rej;echo number_format($tot_sew_reject,0); ?></p></td>
				<td width="80" align="right" title="Count of Sewing Defect(<? echo $sewing_defect;?>)/Sewing Out*100<? //echo $sewing_defect;?>"><p><? $tot_sew_defect_per=$sewing_defect/$sewing_out*100; ?><a href="##" onClick="generate_sew_defect_popup('<? echo $company_name; ?>','<? echo $po_id_all; ?>','<? echo $master_style; ?>','sewing_defect_popup',1)"><? echo  number_format($tot_sew_defect_per,2);?></a></p></td>
				<td width="80" align="right" title="Send To Wash"><p><? echo number_format($send_wash,0); ?></p></td>
                <td width="80" align="right" title="Defect"><p><? echo number_format($mat_wash_recv,0); ?></p></td>
                <?
                 $wash_in_hand=$mat_wash_recv-$wash_del; 
				//$wash_recv_from_qc=$wash_del;
				$wash_recv_from_qc=$recv_wash;
				?>
				<td width="80" align="right"><p><? echo number_format($wash_wet_prod,0); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($wash_del,0); ?></p></td>
                <td width="80" align="right"><p><?  echo number_format($wash_in_hand,0); ?></p></td>
				<td width="80" align="right" title="Emblishmnet Recv"><p><? echo number_format($wash_recv_from_qc,0); ?></p></td>
				<td width="80" align="right" title=""><p><? 
				echo number_format($fin_qty,2); ?></p></td> 
				<td width="80" align="right" title="Cut to Ratio-Total Defect"><p><?  
				echo number_format($wash_recv_from_rejet_qty,0); ?></p></td>
				<td width="80" align="center" title=""><p><? echo $fin_qty_rej; ?></p></td>
                <td width="80" title="Count Of Defect(<? echo $fin_defect;?>)/Qc Pass Qty*100" align="right"><p><? $fin_defect_per=$fin_defect/$fin_qty*100;//echo number_format($fin_defect_per,2); ?><a href="##" onClick="generate_sew_defect_popup('<? echo $company_name; ?>','<? echo $po_id_all; ?>','<? echo $master_style; ?>','fin_defect_popup',2)"><? echo  number_format($fin_defect_per,2);?></a></p></td>
                
                <td width="80" align="right"><p><? echo number_format($ship_qty,0); ?></p></td>
                <td width="80" title="Ex-FactQty-PoQty Pcs" align="right"><p><? $short_excess_qty=$ship_qty-$row[('po_qty_pcs')];echo number_format($short_excess_qty,0); ?></p></td>
                <td width="80" align="right" title="((Ex-Fact-Po Qty Pcs/Po Qty Pcs)*100)"><p><? $short_excess_qty_per=(($ship_qty-$row[('po_qty_pcs')])/$row[('po_qty_pcs')]*100); echo number_format($short_excess_qty_per,2); ?></p></td>
                <td width="80" title="Ex-Fact-Lay Cut+Sample" align="right"><p><? $cut_to_shipQty=($ship_qty-$lay_cut_qty)+$sample_pcs;echo number_format($cut_to_shipQty,0); ?></p></td>
                <td width="80" title="Ex-Fact/Lay Cut*100" align="right"><p><?  $cut_to_shipQty_ratio=$ship_qty/$lay_cut_qty*100;echo number_format($cut_to_shipQty_ratio,2); ?></p></td>
                
                 <td width="80" align="right"><p><? echo number_format($invoice_qnty,0); ?></p></td>
                <td width="80" align="right"><p><? echo number_format($invoice_value,0); ?></p></td>
                <td width="80" align="right"><p><? echo number_format($inv_discount_ammount,0); ?></p></td>
				<td width="80" align="right" title="Invoice$-Discount$"><p><? $net_invoice_amount=$invoice_value-$inv_discount_ammount; echo number_format($net_invoice_amount,0); ?></p></td>
                <td width="80" align="right"><p><? echo number_format($doc_sub_buyer_value,0); ?></p></td>
				<td width="80" align="right" title=""><p><? echo number_format($doc_sub_bank_value,0); ?></p></td>
                
                <td width="80" align="right"><p><? echo number_format($doc_distribute_value,0); ?></p></td>
				<td width="80" align="right" title=""><p><? echo number_format($doc_deduct_value,0); ?></p></td>
               
                <td width="80" align="right"><p><? echo number_format($sample_pcs,0); ?></p></td>
                <td width="80" align="center"><p><? echo $wash_factoryAllArr; ?></p></td>
				<td align="center" title=""><p><? echo $last_ship_date; ?></p></td>
			</tr>
			
			<?
				$total_lay_cut_qty+=$lay_cut_qty;
				$total_po_qty_pcs+=$row[('po_qty_pcs')];
				$total_plan_cut+=$row[('plan_cut')];
				$total_booking_fabric_Qty+=$booking_fabric_Qty;
				$total_buyer_costing_qty+=$buyer_costing_qty;
				$total_cad_marker_cons+=$cad_marker_cons;
				$total_buyerYdsQty+=$tot_buyerYdsQty;
				$total_booking_cost_yds+=$booking_cost_yds;
				$total_fabric_recv_req_yds_qty+=$fabric_recv_req_yds_qty;
				$total_wv_recv_fin+=$wv_recv_fin;
				$total_fin_fab_in+=$fin_fab_in;
				$total_fin_fab_out+=$fin_fab_out;
				$total_wv_issue_fin+=$wv_issue_fin;
				$total_balance_in_storeQty+=$balance_in_storeQty;
				$total_lay_cut_prod_qty+=$lay_cut_qty;
				$total_cut_prod_qty+=$cut_prod_qty;
				$total_used_yds+=$used_yds;
				$total_leftOver_fabYds+=$leftOver_fabYds;
				$total_actual_cost_vari_pcss+=$actual_cost_vari_pcs;
				$total_costing_vs_actual_pcs+=$costing_vs_actual_pcs;
				
				$total_tot_actual_costing_yds+=$tot_actual_costing_yds;
				$total_tot_costing_vs_costing_yds+=$tot_costing_vs_actual_yds;
				$total_possible_cut_budget+=$possible_cut_budget;
				$total_defect_qty+=$defect_qty;
				$total_cuting_del_input+=$cuting_del_input;
				$total_sewing_in+=$sewing_in;
				$total_sewing_out+=$sewing_out;
				$total_send_wash+=$send_wash;
				$total_sewing_reject_pcs+=$tot_sew_reject;
				$total_mat_wash_recv+=$mat_wash_recv;
				$total_wash_wet_prod+=$wash_wet_prod;
				$total_wash_del+=$wash_del;
				$total_wash_in_hand+=$wash_in_hand;
				$total_fin_qty+=$fin_qty;
				$total_wash_recv_from_qc+=$wash_recv_from_qc;
				$total_wash_recv_from_rejet_qty+=$wash_recv_from_rejet_qty;
				$total_fin_qty_rej+=$fin_qty_rej;
				$total_ship_qty+=$ship_qty;
				$total_ship_short_ex_qty+=$short_excess_qty;
				$total_cut_to_shipQty+=$cut_to_shipQty;
				$total_sample_pcs+=$sample_pcs;
				$total_invoice_qnty+=$invoice_qnty;
				$total_invoice_value+=$invoice_value;
				$total_inv_discount_ammount+=$inv_discount_ammount;
				$total_net_invoice_amount+=$net_invoice_amount;
				$total_doc_sub_buyer_value+=$doc_sub_buyer_value;
				$total_doc_sub_bank_value+=$doc_sub_bank_value;
				$total_doc_distribute_value+=$doc_distribute_value;
				$total_doc_deduct_value+=$doc_deduct_value;
				
				$total_marker_acl_pcs_cons+=$marker_acl_pcs_cons;
				$total_tot_costing_vs_actual_yds+=$marker_acl_vari_cons;
				
			
			$i++;
			}
		}
		//echo $tot_order_value;
		?>
        </table>
        </div>
        <table class="tbl_bottom" width="<? echo $width_td;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
        	<tr>
                <td width="20"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="60"></td>
                
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100">Total</td>
                <td width="80"><? echo number_format($total_po_qty_pcs,0);?></td>
                <td width="80"><? echo number_format($total_plan_cut,0);?></td>
                <td width="80"></td>
                <td width="80"></td>
                <td width="80"></td>
                <td width="80"></td>
                <td width="80"><? //echo number_format($total_buyer_costing_qty,2);?></td>
                <td width="80"><? echo number_format($total_buyer_costing_qty,4);?></td>
                <td width="80"><? echo number_format($total_booking_fabric_Qty,4)//echo number_format($total_cad_marker_cons,2);?></td>
                <td width="80"><? echo number_format($total_actual_cost_vari_pcss,4);?></td>
                <td width="80"><? echo number_format($total_cad_marker_cons,4);?></td>
                <td width="80"><? echo number_format($total_costing_vs_actual_pcs,2);?></td>
                <td width="80"><? echo number_format($total_marker_acl_pcs_cons,2);?></td>
                <td width="80"><? echo number_format($total_buyerYdsQty,2);?></td>
                <td width="80"><? echo number_format($total_booking_cost_yds,2);?></td>
                
                <td width="80"><? echo number_format($total_tot_actual_costing_yds,2);?></td>
                <td width="80"><? echo number_format($total_tot_costing_vs_costing_yds,2);?></td>
                 <td width="80"><? echo number_format($total_tot_costing_vs_actual_yds,2);?></td>
                <td width="80"><? echo number_format($total_fabric_recv_req_yds_qty,2);?></td>
                <td width="80"><? echo number_format($total_wv_recv_fin,2);?></td>
                <td width="80"><? echo number_format($total_fin_fab_in,2);?></td>
                
                <td width="80"><? echo number_format($total_fin_fab_out,2);?></td>
                
                 
                <td width="80"><? echo number_format($total_wv_issue_fin,2);?></td>
                <td width="80"><? echo number_format($total_used_yds,2);?></td>
                
                <td width="80"><? echo number_format($total_leftOver_fabYds,0);?></td>

                <td width="80"><? echo number_format($total_balance_in_storeQty,0);?></td>
                <td width="80"><? echo number_format($total_possible_cut_budget,0);?></td>
                <td width="80"><? echo number_format($total_lay_cut_prod_qty,0);?></td> 
                <td width="80"><? echo number_format($total_cut_prod_qty,0);?></td>
                <td width="80"><? echo number_format($total_defect_qty,0);?></td>
                <td width="80"><? //echo number_format($total_tot_embell_cost,2);?></td>
                <td width="80"><? //echo number_format($total_sewing_out,2);?></td>
                
                
                <td width="80"><? echo number_format($total_cuting_del_input,2);?></td>
                <td width="80"><? echo number_format($total_sewing_in,2);?></td>
                <td width="80"><? echo number_format($total_sewing_out,2);?></td>
                
                <td width="80"><? echo number_format($total_sewing_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_wash_reject_pcs,2);?></td>
                <td width="80"><? echo number_format($total_send_wash,2);?></td>
                
                <td width="80"><? echo number_format($total_mat_wash_recv,2);?></td>
                <td width="80"><? echo number_format($total_wash_wet_prod,2);?></td>
                
                <td width="80"><? echo number_format($total_wash_del,2);?></td>
                 <td width="80"><? echo number_format($total_wash_in_hand,2);?></td>
                <td width="80"><? echo number_format($total_wash_recv_from_qc,2);?></td>
                <td width="80"><? echo number_format($total_fin_qty,2);?></td>
             	 <td width="80"><? echo number_format($total_wash_recv_from_rejet_qty,2);?></td>
                <td width="80"><? echo number_format($total_fin_qty_rej,2);?></td>
                <td width="80"><? //echo number_format($total_fin_qty_rej,2);?></td>
                <td width="80"><? echo number_format($total_ship_qty,2);?></td>
                <td width="80"><? echo number_format($total_ship_short_ex_qty,2);?></td>
                <td width="80"><? //echo number_format($total_ok_gmt_after_ship,2);?></td>
                <td width="80"><? echo number_format($total_cut_to_shipQty,2);?></td>
                <td width="80"><? //echo number_format($total_invoice_qnty,2);?></td>
                <td width="80"><? echo number_format($total_invoice_qnty,0);?></td>
                <td width="80"><? echo number_format($total_invoice_value,0);?></td>
                 <td width="80"><? echo number_format($total_inv_discount_ammount,0);?></td>
                <td width="80"><? echo number_format($total_net_invoice_amount,0);?></td>
                 <td width="80"><?  echo number_format($total_doc_sub_buyer_value,0);?></td>
                <td width="80"><? echo number_format($total_doc_sub_bank_value,0);?></td>
                
                <td width="80"><?  echo number_format($total_doc_distribute_value,2);?></td>
                <td width="80"><? echo number_format($total_doc_deduct_value,2);?></td>
                <td width="80"><? //echo number_format($total_missing,2);?></td>
                <td width="80">&nbsp; </td>
                 <td>&nbsp;</td>
            </tr>
            
        </table>
    </div>
  <? }
	$html = ob_get_contents();
    ob_clean();
	 foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
	$name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$report_type";

	//echo "$total_data****$filename****$tot_rows";
	exit();
}
if($action=="sewing_defect_popup") //show 2 button
{
	//echo load_html_head_contents("Po Detail Details", "../../../../", "", $popup,$unicode,'',$amchart);
	//echo load_html_head_contents("Order Wise Budget Report","../../../", 1, 1, $unicode,1,1);
	echo load_html_head_contents("Graph", "../../../../", "", $popup, 1,1);

	//echo load_html_head_contents("Finishing Capacity and Achivment(Iron)", "", "", $popup, $unicode, '', $amchart);//this $amchart is not wark in this page

	extract($_REQUEST);
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$floor_nameArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	
	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_ids'", "id", "po_number");
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
  
    $sql_sew="SELECT a.serving_company,a.floor_id,a.prod_reso_allo,a.sewing_line,a.production_quantity,a.production_date,b.id as defect_dtls_id,  e.id as po_id,f.style_ref_no,f.buyer_name, b.bundle_no,b.defect_type_id, b.defect_point_id, b.defect_qty from pro_garments_production_mst a, wo_po_break_down e, wo_po_details_master f,pro_gmts_prod_dft b where a.id=b.mst_id and a.po_break_down_id=e.id and e.job_no_mst=f.job_no and e.id=b.po_break_down_id  and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0    and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.grouping=$style_ref  ";
	
	$sew_sql_result=sql_select($sql_sew);$defect_chk_arr=array();
	foreach($sew_sql_result as $row)
	{
		if($row[csf('prod_reso_allo')]==1)
		{
		$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $resource_id : ",".$resource_id;
			}
			$sewing_line=$line_name;
		}
		else $sewing_line=$row[csf('sewing_line')];
		
		 $floor_name=$floor_nameArr[$row[csf('floor_id')]];
		
		$sew_line_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['defect_qty']+=$row[csf('defect_qty')];
		if($defect_chk_arr[$row[csf('defect_dtls_id')]]=="")
		{
		//$sew_line_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['production_quantity']+=$row[csf('production_quantity')];
		$defect_chk_arr[$row[csf('defect_dtls_id')]]=999;
		}
		//$sew_line_arr[$sewing_line][$row[csf('style_ref_no')]]['production_quantity']=$row[csf('production_quantity')];
		//$sew_line_alert_qty_arr[$sewing_line][$row[csf('style_ref_no')]][$row[csf('defect_type_id')]][$row[csf('defect_point_id')]]['defect_qty']+=$row[csf('defect_qty')];
	}
	 $sql_sewOut="SELECT a.serving_company,a.floor_id,a.prod_reso_allo,a.sewing_line,a.production_quantity,a.production_date, e.id as po_id,f.style_ref_no,f.buyer_name from pro_garments_production_mst a, wo_po_break_down e, wo_po_details_master f where   a.po_break_down_id=e.id and e.job_no_mst=f.job_no    and a.production_type=5 and a.status_active=1 and a.is_deleted=0  and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.grouping=$style_ref  ";
	 $sew_out_result=sql_select($sql_sewOut);
	foreach($sew_out_result as $row)
	{
		$sew_line_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['production_quantity']+=$row[csf('production_quantity')];
	}
	 
	$td_width=750;
	?>
    <div style="width:<? echo $td_width;?>px; margin-left:3px">
        <div style="width:<? echo $td_width;?>px;" align="center">
        	<input  type="hidden" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div">
        
        	<table border="1" class="rpt_table" rules="all" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" >
            <caption><b> Sewing Defect Details</b> </caption>
               <thead>
               <tr>
                	 <th width="30">#SL</th>
                     <th width="150">Sewing Company</th>
                 	 <th width="70">Date</th>
                  	 <th width="70">Total defect qty</th>
                  	 <th width="70">Total audit qty</th>
                  	 <th width="">Defect %</th>
               </tr>
               </thead>
               <tbody>
               <?
               $i=1;
				$total_defect_qty=$total_aduit_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0;
				foreach($sew_line_arr as $company_id=>$comp_data)
				{
					foreach($comp_data as $date_key=>$row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30" align="center"><p><? echo $i; ?></p></td>
						<td width="150"><div style="word-wrap:break-word; width:145px"><? echo $company_library[$company_id]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo change_date_format($date_key); ?></div></td>
                        <td width="100" align="right"><div style="word-wrap:break-word; width:100px"><? echo number_format($row['defect_qty'],0); ?></div></td>
                        <td width="100" align="right"> <div style="word-wrap:break-word; width:100px"><? echo number_format($row['production_quantity'],0); ?></div></td>
                        <td width="100" align="right" title="DefectQty/AuditQty*100"><div style="word-wrap:break-word; width:100px"><? echo number_format(($row['defect_qty']/$row['production_quantity'])*100,2); ?></div></td>
                    </tr>
                    <?
					$i++;
					$total_defect_qty+=$row['defect_qty'];
					$total_aduit_qty+=$row['production_quantity'];
					}
				}
					?>
                    </tbody>
                      <tfoot>
                      <tr>
                      <th colspan="3"> Total</th>
                      <th><? echo number_format($total_defect_qty,0);?> </th>
                      <th><? echo number_format($total_aduit_qty,0);?> </th>
                      <th><?  echo number_format($total_defect_qty/$total_aduit_qty*100,2);?> </th>
                      </tr>
                      </tfoot>  
                        
               </tbody>
               </table>
               
        </div>
        
        
    </div>
        
        <?
	
  
exit();
}
if($action=="fin_defect_popup") //show 2 button
{
	//echo load_html_head_contents("Po Detail Details", "../../../../", "", $popup,$unicode,'',$amchart);
	//echo load_html_head_contents("Order Wise Budget Report","../../../", 1, 1, $unicode,1,1);
	echo load_html_head_contents("Graph", "../../../../", "", $popup, 1,1);

	//echo load_html_head_contents("Finishing Capacity and Achivment(Iron)", "", "", $popup, $unicode, '', $amchart);//this $amchart is not wark in this page

	extract($_REQUEST);
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$floor_nameArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	
	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_ids'", "id", "po_number");
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
  
    $sql_fin="SELECT a.serving_company,a.floor_id,a.prod_reso_allo,a.sewing_line,a.production_quantity,a.production_date,b.id as defect_dtls_id,  e.id as po_id,f.style_ref_no,f.buyer_name, b.bundle_no,b.defect_type_id, b.defect_point_id, b.defect_qty from pro_garments_production_mst a, wo_po_break_down e, wo_po_details_master f,pro_gmts_prod_dft b where a.id=b.mst_id and a.po_break_down_id=e.id and e.job_no_mst=f.job_no and e.id=b.po_break_down_id  and a.production_type=80 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0    and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.grouping=$style_ref  ";
	
	$fin_sql_result=sql_select($sql_fin);$defect_chk_arr=array();
	foreach($fin_sql_result as $row)
	{
		if($row[csf('prod_reso_allo')]==1)
		{
		$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $resource_id : ",".$resource_id;
			}
			$sewing_line=$line_name;
		}
		else $sewing_line=$row[csf('sewing_line')];
		
		 $floor_name=$floor_nameArr[$row[csf('floor_id')]];
		$defect_type_id=$row[csf('defect_type_id')];
		//echo $defect_type_id.'DSDS';
		$summary_defect_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['defect_qty']+=$row[csf('defect_qty')];
		if($defect_type_id==1) //Inside
		{
		$inside_defect_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['defect_qty']+=$row[csf('defect_qty')];
		}
		if($defect_type_id==2) //Topside
		{
		$topside_defect_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['defect_qty']+=$row[csf('defect_qty')];
		}
		if($defect_type_id==3) //GetUp
		{
		$getup_defect_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['defect_qty']+=$row[csf('defect_qty')];
		}
		if($defect_type_id==4) //Wash
		{
		$wash_defect_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['defect_qty']+=$row[csf('defect_qty')];
		}
		if($defect_type_id==5) //Measure
		{
		$measure_defect_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['defect_qty']+=$row[csf('defect_qty')];
		}
		if($defect_type_id==6) //Final
		{
		$final_defect_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['defect_qty']+=$row[csf('defect_qty')];
		}
		
		//$sew_line_arr[$sewing_line][$row[csf('style_ref_no')]]['production_quantity']=$row[csf('production_quantity')];
		//$sew_line_alert_qty_arr[$sewing_line][$row[csf('style_ref_no')]][$row[csf('defect_type_id')]][$row[csf('defect_point_id')]]['defect_qty']+=$row[csf('defect_qty')];
	}
	//print_r($getup_defect_arr);
	 $sql_sewOut="SELECT a.serving_company,a.floor_id,a.prod_reso_allo,a.sewing_line,a.production_quantity,a.production_date, e.id as po_id,f.style_ref_no,f.buyer_name from pro_garments_production_mst a, wo_po_break_down e, wo_po_details_master f where   a.po_break_down_id=e.id and e.job_no_mst=f.job_no    and a.production_type=80 and a.status_active=1 and a.is_deleted=0  and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.grouping=$style_ref  ";
	 $sew_out_result=sql_select($sql_sewOut);
	foreach($sew_out_result as $row)
	{
		$sew_line_arr[$row[csf('serving_company')]][$row[csf('production_date')]]['production_quantity']+=$row[csf('production_quantity')];
	}
	 
	$td_width=700;$minus_width=100;
	?>
    <div style="width:<? echo $td_width;?>px; margin-left:3px">
        <div style="width:<? echo $td_width;?>px;" align="center">
        	<input  type="hidden" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
            <br>
        </div>
        <div id="report_div">
         <br>
        	<table border="1" class="rpt_table" rules="all" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" >
            <caption><b> Finishing Defect</b> </caption>
            <tr>
              <td>
            	<table border="1" style="margin:5px;"  class="rpt_table" rules="all" width="<? echo $td_width-$minus_width;?>" cellpadding="0" cellspacing="0" >
          		  <caption><b style="float:left"> Inside Defect</b> </caption>
               <thead>
               <tr>
                	 <th width="30">#SL</th>
                     <th width="150">Sewing Company</th>
                 	 <th width="70">Date</th>
                  	 <th width="70">Total defect qty</th>
                  	 <th width="70">Total audit qty</th>
                  	 <th width="">Defect %</th>
               </tr>
               </thead>
               <tbody>
               <?
               $i=1;
				$total_defect_qty=$total_aduit_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0;
				foreach($inside_defect_arr as $company_id=>$comp_data)
				{
					foreach($comp_data as $date_key=>$row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					$production_quantity=$sew_line_arr[$company_id][$date_key]['production_quantity'];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30" align="center"><p><? echo $i; ?></p></td>
						<td width="150"><div style="word-wrap:break-word; width:145px"><? echo $company_library[$company_id]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo change_date_format($date_key); ?></div></td>
                        <td width="100" align="right"><div style="word-wrap:break-word; width:100px"><? echo number_format($row['defect_qty'],0); ?></div></td>
                        <td width="100" align="right"> <div style="word-wrap:break-word; width:100px"><? echo number_format($production_quantity,0); ?></div></td>
                        <td width="100" align="right" title="DefectQty/AuditQty*100"><div style="word-wrap:break-word; width:100px"><? echo number_format(($row['defect_qty']/$production_quantity)*100,2); ?></div></td>
                    </tr>
                    <?
					$i++;
					$total_defect_qty+=$row['defect_qty'];
					$total_aduit_qty+=$production_quantity;
					}
				}
					?>
                    </tbody>
                      <tfoot>
                      <tr>
                      <th colspan="3"> Total</th>
                      <th><? echo number_format($total_defect_qty,0);?> </th>
                      <th><? echo number_format($total_aduit_qty,0);?> </th>
                      <th><?  echo number_format($total_defect_qty/$total_aduit_qty*100,2);?> </th>
                      </tr>
                      </tfoot>  
                        
             	  </tbody>
             	  </table>
              
               </td>
               <td>
            	<table border="1" class="rpt_table" style="margin:5px;"   rules="all" width="<? echo $td_width-$minus_width;?>" cellpadding="0" cellspacing="0" >
          		  <caption><b style="float:left"> Top Side Defect</b> </caption>
               <thead>
               <tr>
                	 <th width="30">#SL</th>
                     <th width="150">Sewing Company</th>
                 	 <th width="70">Date</th>
                  	 <th width="70">Total defect qty</th>
                  	 <th width="70">Total audit qty</th>
                  	 <th width="">Defect %</th>
               </tr>
               </thead>
               <tbody>
               <?
               $i=1;
				$total_defect_qty=$total_aduit_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0;
				foreach($topside_defect_arr as $company_id=>$comp_data)
				{
					foreach($comp_data as $date_key=>$row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					$production_quantity=$sew_line_arr[$company_id][$date_key]['production_quantity'];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30" align="center"><p><? echo $i; ?></p></td>
						<td width="150"><div style="word-wrap:break-word; width:145px"><? echo $company_library[$company_id]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo change_date_format($date_key); ?></div></td>
                        <td width="100" align="right"><div style="word-wrap:break-word; width:100px"><? echo number_format($row['defect_qty'],0); ?></div></td>
                        <td width="100" align="right"> <div style="word-wrap:break-word; width:100px"><? echo number_format($production_quantity,0); ?></div></td>
                        <td width="100" align="right" title="DefectQty/AuditQty*100"><div style="word-wrap:break-word; width:100px"><? echo number_format(($row['defect_qty']/$production_quantity)*100,2); ?></div></td>
                    </tr>
                    <?
					$i++;
					$total_defect_qty+=$row['defect_qty'];
					$total_aduit_qty+=$production_quantity;
					}
				}
					?>
                    </tbody>
                      <tfoot>
                      <tr>
                      <th colspan="3"> Total</th>
                      <th><? echo number_format($total_defect_qty,0);?> </th>
                      <th><? echo number_format($total_aduit_qty,0);?> </th>
                      <th><?  echo number_format($total_defect_qty/$total_aduit_qty*100,2);?> </th>
                      </tr>
                      </tfoot>  
                        
             	  </tbody>
             	  </table>
              
                 </td>
                </tr>
                <tr>
             	 <td>
            	<table border="1" class="rpt_table" style="margin:5px;"  rules="all" width="<? echo $td_width-$minus_width;?>" cellpadding="0" cellspacing="0" >
          		  <caption><b style="float:left"> Gateup Defect</b> </caption>
               <thead>
               <tr>
                	 <th width="30">#SL</th>
                     <th width="150">Sewing Company</th>
                 	 <th width="70">Date</th>
                  	 <th width="70">Total defect qty</th>
                  	 <th width="70">Total audit qty</th>
                  	 <th width="">Defect %</th>
               </tr>
               </thead>
               <tbody>
               <?
               $i=1;
				$total_defect_qty=$total_aduit_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0;
				foreach($getup_defect_arr as $company_id=>$comp_data)
				{
					foreach($comp_data as $date_key=>$row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$production_quantity=$sew_line_arr[$company_id][$date_key]['production_quantity'];
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30" align="center"><p><? echo $i; ?></p></td>
						<td width="150"><div style="word-wrap:break-word; width:145px"><? echo $company_library[$company_id]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo change_date_format($date_key); ?></div></td>
                        <td width="100" align="right"><div style="word-wrap:break-word; width:100px"><? echo number_format($row['defect_qty'],0); ?></div></td>
                        <td width="100" align="right"> <div style="word-wrap:break-word; width:100px"><? echo number_format($production_quantity,0); ?></div></td>
                        <td width="100" align="right" title="DefectQty/AuditQty*100"><div style="word-wrap:break-word; width:100px"><? echo number_format(($row['defect_qty']/$production_quantity)*100,2); ?></div></td>
                    </tr>
                    <?
					$i++;
					$total_defect_qty+=$row['defect_qty'];
					$total_aduit_qty+=$production_quantity;
					}
				}
					?>
                    </tbody>
                      <tfoot>
                      <tr>
                      <th colspan="3"> Total</th>
                      <th><? echo number_format($total_defect_qty,0);?> </th>
                      <th><? echo number_format($total_aduit_qty,0);?> </th>
                      <th><?  echo number_format($total_defect_qty/$total_aduit_qty*100,2);?> </th>
                      </tr>
                      </tfoot>  
                        
             	  </tbody>
             	  </table>
              
               </td>
               <td>
            	<table border="1" style="margin:5px;" class="rpt_table" rules="all" width="<? echo $td_width-$minus_width;?>" cellpadding="0" cellspacing="0" >
          		  <caption><b style="float:left"> Wash Defect</b> </caption>
               <thead>
               <tr>
                	 <th width="30">#SL</th>
                     <th width="150">Sewing Company</th>
                 	 <th width="70">Date</th>
                  	 <th width="70">Total defect qty</th>
                  	 <th width="70">Total audit qty</th>
                  	 <th width="">Defect %</th>
               </tr>
               </thead>
               <tbody>
               <?
               $i=1;
				$total_defect_qty=$total_aduit_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0;
				foreach($wash_defect_arr as $company_id=>$comp_data)
				{
					foreach($comp_data as $date_key=>$row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					$production_quantity=$sew_line_arr[$company_id][$date_key]['production_quantity'];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30" align="center"><p><? echo $i; ?></p></td>
						<td width="150"><div style="word-wrap:break-word; width:145px"><? echo $company_library[$company_id]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo change_date_format($date_key); ?></div></td>
                        <td width="100" align="right"><div style="word-wrap:break-word; width:100px"><? echo number_format($row['defect_qty'],0); ?></div></td>
                        <td width="100" align="right"> <div style="word-wrap:break-word; width:100px"><? echo number_format($production_quantity,0); ?></div></td>
                        <td width="100" align="right" title="DefectQty/AuditQty*100"><div style="word-wrap:break-word; width:100px"><? echo number_format(($row['defect_qty']/$production_quantity)*100,2); ?></div></td>
                    </tr>
                    <?
					$i++;
					$total_defect_qty+=$row['defect_qty'];
					$total_aduit_qty+=$production_quantity;
					}
				}
					?>
                    </tbody>
                      <tfoot>
                      <tr>
                      <th colspan="3"> Total</th>
                      <th><? echo number_format($total_defect_qty,0);?> </th>
                      <th><? echo number_format($total_aduit_qty,0);?> </th>
                      <th><?  echo number_format($total_defect_qty/$total_aduit_qty*100,2);?> </th>
                      </tr>
                      </tfoot>  
                        
             	  </tbody>
             	  </table>
              
                 </td>
                </tr>
                 <tr>
             	 <td>
            	<table border="1" class="rpt_table" style="margin:5px;"  rules="all" width="<? echo $td_width-$minus_width;?>" cellpadding="0" cellspacing="0" >
          		  <caption><b style="float:left"> Mesurement Check</b> </caption>
               <thead>
               <tr>
                	 <th width="30">#SL</th>
                     <th width="150">Sewing Company</th>
                 	 <th width="70">Date</th>
                  	 <th width="70">Total defect qty</th>
                  	 <th width="70">Total audit qty</th>
                  	 <th width="">Defect %</th>
               </tr>
               </thead>
               <tbody>
               <?
               $i=1;
				$total_defect_qty=$total_aduit_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0;
				foreach($measure_defect_arr as $company_id=>$comp_data)
				{
					foreach($comp_data as $date_key=>$row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					$production_quantity=$sew_line_arr[$company_id][$date_key]['production_quantity'];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30" align="center"><p><? echo $i; ?></p></td>
						<td width="150"><div style="word-wrap:break-word; width:145px"><? echo $company_library[$company_id]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo change_date_format($date_key); ?></div></td>
                        <td width="100" align="right"><div style="word-wrap:break-word; width:100px"><? echo number_format($row['defect_qty'],0); ?></div></td>
                        <td width="100" align="right"> <div style="word-wrap:break-word; width:100px"><? echo number_format($production_quantity,0); ?></div></td>
                        <td width="100" align="right" title="DefectQty/AuditQty*100"><div style="word-wrap:break-word; width:100px"><? echo number_format(($row['defect_qty']/$production_quantity)*100,2); ?></div></td>
                    </tr>
                    <?
					$i++;
					$total_defect_qty+=$row['defect_qty'];
					$total_aduit_qty+=$production_quantity;
					}
				}
					?>
                    </tbody>
                      <tfoot>
                      <tr>
                      <th colspan="3"> Total</th>
                      <th><? echo number_format($total_defect_qty,0);?> </th>
                      <th><? echo number_format($total_aduit_qty,0);?> </th>
                      <th><?  echo number_format($total_defect_qty/$total_aduit_qty*100,2);?> </th>
                      </tr>
                      </tfoot>  
                        
             	  </tbody>
             	  </table>
              
               </td>
               <td>
            	<table border="1" style="margin:5px;" class="rpt_table" rules="all" width="<? echo $td_width-$minus_width;?>" cellpadding="0" cellspacing="0" >
          		  <caption><b style="float:left"> Final Body Check</b> </caption>
               <thead>
               <tr>
                	 <th width="30">#SL</th>
                     <th width="150">Sewing Company</th>
                 	 <th width="70">Date</th>
                  	 <th width="70">Total defect qty</th>
                  	 <th width="70">Total audit qty</th>
                  	 <th width="">Defect %</th>
               </tr>
               </thead>
               <tbody>
               <?
               $i=1;
				$total_defect_qty=$total_aduit_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0;
				foreach($final_defect_arr as $company_id=>$comp_data)
				{
					foreach($comp_data as $date_key=>$row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$production_quantity=$sew_line_arr[$company_id][$date_key]['production_quantity'];
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30" align="center"><p><? echo $i; ?></p></td>
						<td width="150"><div style="word-wrap:break-word; width:145px"><? echo $company_library[$company_id]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo change_date_format($date_key); ?></div></td>
                        <td width="100" align="right"><div style="word-wrap:break-word; width:100px"><? echo number_format($row['defect_qty'],0); ?></div></td>
                        <td width="100" align="right"> <div style="word-wrap:break-word; width:100px"><? echo number_format($production_quantity,0); ?></div></td>
                        <td width="100" align="right" title="DefectQty/AuditQty*100"><div style="word-wrap:break-word; width:100px"><? echo number_format(($row['defect_qty']/$production_quantity)*100,2); ?></div></td>
                    </tr>
                    <?
					$i++;
					$total_defect_qty+=$row['defect_qty'];
					$total_aduit_qty+=$production_quantity;
					}
				}
					?>
                    </tbody>
                      <tfoot>
                      <tr>
                      <th colspan="3"> Total</th>
                      <th><? echo number_format($total_defect_qty,0);?> </th>
                      <th><? echo number_format($total_aduit_qty,0);?> </th>
                      <th><?  echo number_format($total_defect_qty/$total_aduit_qty*100,2);?> </th>
                      </tr>
                      </tfoot>  
                        
             	  </tbody>
             	  </table>
              
                 </td>
                </tr>
                <tr>
                 <td>
            	<table border="1" style="margin:5px;" class="rpt_table" rules="all" width="<? echo $td_width-$minus_width;?>" cellpadding="0" cellspacing="0" >
          		  <caption><b> Defect Summary</b> </caption>
               <thead>
               <tr>
                	 <th width="30">#SL</th>
                     <th width="150">Sewing Company</th>
                 	 <th width="70">Date</th>
                  	 <th width="70">Total defect qty</th>
                  	 <th width="70">Total audit qty</th>
                  	 <th width="">Defect %</th>
               </tr>
               </thead>
               <tbody>
               <?
               $i=1;
				$total_defect_qty=$total_aduit_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0;
				foreach($summary_defect_arr as $company_id=>$comp_data)
				{
					foreach($comp_data as $date_key=>$row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$production_quantity=$sew_line_arr[$company_id][$date_key]['production_quantity'];
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30" align="center"><p><? echo $i; ?></p></td>
						<td width="150"><div style="word-wrap:break-word; width:145px"><? echo $company_library[$company_id]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo change_date_format($date_key); ?></div></td>
                        <td width="100" align="right"><div style="word-wrap:break-word; width:100px"><? echo number_format($row['defect_qty'],0); ?></div></td>
                        <td width="100" align="right"> <div style="word-wrap:break-word; width:100px"><? echo number_format($production_quantity,0); ?></div></td>
                        <td width="100" align="right" title="DefectQty/AuditQty*100"><div style="word-wrap:break-word; width:100px"><? echo number_format(($row['defect_qty']/$production_quantity)*100,2); ?></div></td>
                    </tr>
                    <?
					$i++;
					$total_defect_qty+=$row['defect_qty'];
					$total_aduit_qty+=$production_quantity;
					}
				}
					?>
                    </tbody>
                      <tfoot>
                      <tr>
                      <th colspan="3"> Total</th>
                      <th><? echo number_format($total_defect_qty,0);?> </th>
                      <th><? echo number_format($total_aduit_qty,0);?> </th>
                      <th><?  echo number_format($total_defect_qty/$total_aduit_qty*100,2);?> </th>
                      </tr>
                      </tfoot>  
                        
             	  </tbody>
             	  </table>
              
                 </td>
                </tr>
                
                
               </table>
              
               
        </div>
        
        
    </div>
        
        <?
	
  
exit();
}


if($action=="line_date_wise_popup") //2nd Button Start...
{
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
            .break_all
            {
            	word-wrap: break-word;
            	word-break: break-all;
            }
          
        </style> 
	<?
	extract($_REQUEST);
	$process = array( &$_POST );
	
	//change_date_format($txt_date);die;
	
	if($db_type==0)	$txt_date=change_date_format($txt_date,'yyyy-mm-dd');
	if($db_type==2)	$txt_date=change_date_format($txt_date,'','',1);
	$txt_date="'".$txt_date."'";
	
	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=str_replace("'","",$style_owner);
	$cbo_company_id=str_replace("'","",$style_owner);
	$style_owner=str_replace("'","",$style_owner);
	$buyer_id=str_replace("'","",$buyer_id);
	$style_ref=str_replace("'","",$style_ref);
	$job_no=str_replace("'","",$job_no);
	$po_ids=str_replace("'","",$po_ids);
	//echo $job_no.'po_ids';die;
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 
	and status_active=1");
	//echo $prod_reso_allo[0].'DDDDDDDDDDDDDDDD'; die;
	//wo_po_details_master b, wo_po_break_down c
	$sql_po=sql_select("select c.id as po_id,a.sewing_line,a.prod_reso_allo from   wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.id in($po_ids)");
	//echo "select c.id as po_id,a.sewing_line,a.prod_reso_allo from   wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.id in($po_ids)";die; 
	foreach($sql_po as $row)
	{
		$prod_reso_allo_id=$prod_reso_allo[0];
		$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		if($prod_reso_allo_id==1)
		{
			$resource_id_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
		}
		else
		{
			$sew_id_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
		}
	}
//print_r($sew_id_arr);die;

	$prod_res_lineDataArr = sql_select("select b.mst_id, b.dtls_id, b.po_id from prod_resource_color_size b,wo_po_break_down c where c.id=b.po_id and b.is_deleted=0 and b.status_active=1 and b.mst_id in(".implode(",",$resource_id_arr).")   order by po_id "); 
	// echo "select b.mst_id, b.dtls_id, b.po_id from prod_resource_color_size b,wo_po_break_down c where c.id=b.po_id and b.is_deleted=0 and b.status_active=1 and b.mst_id in(".implode(",",$sew_id_arr).")   order by po_id ";die;
	 
	foreach($prod_res_lineDataArr as $row)
	{
		$resource_mst_id.=$row[csf('mst_id')].',';
		
	}
	$resource_mst_id=rtrim($resource_mst_id,',');
	$res_mst_ids=implode(",",array_unique(explode(",",$resource_mst_id)));
	//echo $resource_mst_id.'f';die;
	//***************************************************************************************************************************
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}
	
	$res_mst_ids_cond="and a.id in($res_mst_ids)";
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1   and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and b.mst_id in(".implode(",",$resource_id_arr).")","line_start_time");	
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1  and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and b.mst_id in(".implode(",",$resource_id_arr).")","line_start_time");
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
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");	
	}
	
	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}
	
	$prod_start_hour=$start_time_arr[1]['pst'];
	$global_start_lanch=$start_time_arr[1]['lst'];
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
	$acturl_hour_minute=date("H:i",strtotime($pc_date_time));	
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
	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company=".str_replace("'","",$cbo_company_id)."";
	
	if(str_replace("'","",$cbo_location_id)==0) 
	{
		$subcon_location="";
		$location="";
	}
	else 
	{
		$location=" and a.location=".str_replace("'","",$cbo_location_id)."";
		$subcon_location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
	}
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	if($cbo_floor_id==0) $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
    if(str_replace("'","",$hidden_line_id)==0)
	{ 
		$line=""; 
		$subcon_line="";
	}
	else 
	{
		$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
		$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
	}
	$cbo_no_prod_type=1;
	$file_no=str_replace("'","",$txt_file_no);
	$ref_no=str_replace("'","",$txt_ref_no);
	if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
	if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
	//echo $file_cond;
	
	//if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date>$txt_date";
	// echo $txt_date_from; die;
	
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();

		 
		$dataArray_sql=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and a.id in(".implode(",",$resource_id_arr).")");
		foreach($dataArray_sql as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		}
		//var_dump($prod_resource_array);
		if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

		if($db_type==0)
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id in(".implode(",",$resource_id_arr).") "); 
		}
		else
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0  and a.id in(".implode(",",$resource_id_arr).")  ");
		}
		
		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
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
	
	
	if($db_type==0) $prod_start_cond="prod_start_time";
	else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')";
	
	$variable_start_time_arr='';
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
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
		$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 
	and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.id in($po_ids)";
		$resultItem=sql_select($sql_item);
	
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
		}
	}
	else
	{
		 $sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.id in($po_ids) ";
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
	
	if($db_type==0) //a.production_date
	{
		$sql="select  a.company_id, a.location, a.production_date,a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.prod_reso_allo, a.production_date, a.sewing_line,
		b.buyer_name  as buyer_name,b.style_ref_no,b.job_no,a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty,"; 
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and  c.id in($po_ids)  $company_name  $buyer_id_cond   group by b.job_no,a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.production_date,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no order by a.location, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
	}
	else if($db_type==2)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id,a.production_date, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,sum(d.production_qnty) as good_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 
				THEN production_qnty else 0 END) AS $prod_hour,";
			}
			$first++;
		}
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23 
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and  c.id in($po_ids) $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond 
		GROUP BY b.job_no, a.company_id, a.location,a.production_date, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping 
		ORDER BY a.location,a.floor_id,a.sewing_line";
	}
	//echo $sql; die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array(); $reso_line_ids=''; $all_po_id="";
	$active_days_arr=array();
	$duplicate_date_arr=array();
	foreach($sql_resqlt as $val)
	{
		 $prod_date=$val[csf('production_date')];
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
			$reso_line_ids.=$val[csf('sewing_line')].',';
		}
		else
		{
			$sewing_line_id=$val[csf('sewing_line')];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];
		
		if($val[csf('prod_reso_allo')]==1)
		{
		$line_resource_mst_arr=explode(",",$prod_reso_arr[$val[csf('sewing_line')]]);
							$line_name="";
							foreach($line_resource_mst_arr as $resource_id)
							{
								$line_name .= ($line_name == "") ? $resource_id : ",".$resource_id;
							}
							$val[csf('sewing_line')]=$line_name;
		}
		else $val[csf('sewing_line')]=$val[csf('sewing_line')];
		
		 
							
		$production_serial_arr[$prod_date][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
		
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
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)]; 
			
			if(str_replace("'","",$prod_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)]; 
				} 	
			}
			
			if(str_replace("'","",$prod_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
			}
		}
		
		if(str_replace("'","",$prod_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
			} 	
		}
		else
		{
			$production_po_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
		}
		
	 	$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];  
		$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
		
	 	if($production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
		{
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
		}
	
	 	if($production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
		{
			//echo $val[csf('job_no')].',A';
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no'].=",".$val[csf('job_no')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')]; 
		}
	 	else
		{ //echo $val[csf('job_no')].',B';
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')]; 
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')]; 
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['file']=$val[csf('file_no')]; 
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref']=$val[csf('ref')]; 
		}
		$fob_rate_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')]; 
		
		if($production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
		{
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
		}
		else
		{
			 $production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
		}
		$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];
		
		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
	}
	//print_r($production_data_arr_qty);
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	$po_numIds=chop($all_po_id,',');
	$poIds_cond="";
	$poIds_cond2="";
	if($all_po_id!='' || $all_po_id!=0)
	{
		if($db_type==2 && $po_ids>1000)
		{
			$poIds_cond=" and (";
			$poIds_cond2=" and (";
			$poIdsArr=array_chunk(explode(",",$po_numIds),990);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" b.id  in($ids) or ";
				$poIds_cond2.=" c.id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond2=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			$poIds_cond2.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in($all_po_id)";
			$poIds_cond2=" and  c.id  in($all_po_id)";
		}
	}


    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and  c.id in(".implode(",",$po_id_arr).") $company_name $location $floor $line $buyer_id_cond   $file_cond $ref_cond $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
    //echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
		{
			$active_days_arr[$vals[csf('floor_id')]][$vals[csf('sewing_line')]]+=1;
			$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
			$duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
		}

	}
	//print_r($duplicate_date_arr);
		
	$sql_item_rate="select b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and  b.id in(".implode(",",$po_id_arr).")  $file_cond $ref_cond  $poIds_cond";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}
	
	// subcoutact data **********************************************************************************************************************
	
  /*  if($db_type==0)
    {
		$sql_sub_contuct= "select  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,b.subcon_job as job_no, max(c.smv) as smv,sum(a.production_qnty) as good_qnty,"; 
		
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
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,b.subcon_job ,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.production_date,a.floor_id,d.floor_serial_no,e.sewing_line_serial,  a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,"; 
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
		
	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
		
	}
	//echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();		
	foreach($sub_result as $subcon_val)
	{
		$prod_date=$val[csf('production_date')];
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$subcon_val[csf('sewing_line')]];
		}
		else
		{
			$sewing_line_id=$subcon_val[csf('sewing_line')];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];
		//$production_serial_arr[$subcon_val[csf('floor_id')]][$slNo][$subcon_val[csf('sewing_line')]]=$subcon_val[csf('sewing_line')];
		
		$production_po_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
		if($production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']!="")
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')]; 
		}
		else
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']=$subcon_val[csf('buyer_name')]; 
		}
	
		if($production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']!="")
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no'].=",".$subcon_val[csf('job_no')];
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style'].=",".$subcon_val[csf('cust_style_ref')];  
		}
		else
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')]; 
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no']=$subcon_val[csf('job_no')]; 
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')]; 
		}
	
		if($production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']!="")
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')]; 
		}
		else
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=$subcon_val[csf('order_id')]; 
		}
		$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
		$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['quantity']+=$subcon_val[csf('good_qnty')];
		
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
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$prod_hour]+=$subcon_val[csf($prod_hour)]; 
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				 if( $h>=$line_start_hour && $h<=$actual_time)
				 {
				 $production_po_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	                 } 
			}
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				$production_po_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	            }
		 }
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
			} 	
		}
		else
		{
			$production_po_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
		}
		$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('line_id')]]['prod_hour23']+=$val[csf('prod_hour23')];
	}*/
	//For Summary Report New Add No Prodcut
	 
	
	//echo "<pre>";
	//var_dump($production_serial_arr);die;
    $avable_min=0;
	$today_product=0;
    $floor_name="";   
    $floor_man_power=0;
	$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
	$total_operator=$total_helper=$gnd_hit_rate=0;   
    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
	$j=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;
	foreach($production_serial_arr as $date_key=>$date_fname)
	{
	foreach($date_fname as $f_id=>$fname)
	{
		ksort($fname);
		foreach($fname as $sl=>$s_data)
		{
			
			foreach($s_data as $l_id=>$ldata)
			{
			  $po_value=$production_data_arr[$date_key][$f_id][$ldata]['po_number'];
			  if($po_value)
			  {

				//}
				
				if($i!=1)
				{
					if(!in_array($f_id, $check_arr))
					{
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 $html.='<tr  bgcolor="#B6B6B6">
							<td class="break_all" width="40">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="100">&nbsp;</td>
							<td class="break_all" width="140">&nbsp;</td>
							<td class="break_all" width="100">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							
							
							<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>';
							
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


								$html.='<td class="break_all" align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
							}
							$html.='</tr>';
							
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
						  $j++;
					}
				}
				$floor_row++;
				//$item_ids=$production_data_arr[$f_id][$ldata]['item_number_id'];
				$germents_item=array_unique(explode('****',$production_data_arr[$date_key][$f_id][$ldata]['item_number_id']));
			
				$buyer_neme_all=array_unique(explode(',',$production_data_arr[$date_key][$f_id][$ldata]['buyer_name']));
				$buyer_name="";
				foreach($buyer_neme_all as $buy)
				{
					if($buyer_name!='') $buyer_name.=',';
					$buyer_name.=$buyerArr[$buy];
				}
				$garment_itemname='';
				$active_days='';
				$item_smv="";$item_ids='';
				$smv_for_item="";
				$produce_minit="";
				$order_no_total="";
				$efficiency_min=0;
				$tot_po_qty=0;$fob_val=0;
				foreach($germents_item as $g_val)
				{
					
					$po_garment_item=explode('**',$g_val);
					if($garment_itemname!='') $garment_itemname.=',';
					$garment_itemname.=$garments_item[$po_garment_item[1]];
					if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
					if($active_days=="")$active_days=$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
					else $active_days.=','.$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
					
					
					//echo $item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'].'<br>';
					$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
					$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
					if($item_smv!='') $item_smv.='/';
					//echo $po_garment_item[0].'='.$po_garment_item[1];
					$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
					if($order_no_total!="") $order_no_total.=",";
					$order_no_total.=$po_garment_item[0];
					if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
					else
					$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
					$produce_minit+=$production_po_data_arr[$date_key][$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
					$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
					$prod_qty=$production_data_arr_qty[$date_key][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
					//echo $prod_qty.'<br>';
					if(is_nan($fob_rate)){ $fob_rate=0; }
					$fob_val+=$prod_qty*$fob_rate;
				}
				//$fob_rate=$tot_po_amt/$tot_po_qty;
				
				$subcon_po_id=array_unique(explode(',',$production_data_arr[$date_key][$f_id][$ldata]['order_id']));
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
					$produce_minit+=$production_po_data_arr[$date_key][$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
					if($subcon_order_id!="") $subcon_order_id.=",";
					$subcon_order_id.=$sub_val;
				}
				if($order_no_total!="")
				{
					$day_run_sql=sql_select("select min(production_date) as min_date from pro_garments_production_mst
					where po_break_down_id in(".$order_no_total.")  and production_type=4");
					foreach($day_run_sql as $row_run)
					{
					$sewing_day=$row_run[csf('min_date')];
					}
					if($sewing_day!="")
					{
					$days_run=datediff("d",$sewing_day,$pr_date);
					}
					else  $days_run=0;
				}
				$type_line=$production_data_arr[$date_key][$f_id][$ldata]['type_line'];
				$prod_reso_allo=$production_data_arr[$date_key][$f_id][$ldata]['prod_reso_allo'];
				/*if($type_line==2)
				{
					 $sewing_line='';
					if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
					{
						$line_number='';
						$line_number=explode(",",$ldata);
						foreach($line_data as $lin_id)
						{
							//echo $lin_id.'dd';
							$line_number=explode(",",$prod_reso_arr[$lin_id]);
						}
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}
					}
					else $sewing_line=$lineArr[$lin_id];
				}
				else
				{*/
					$sewing_line='';
					if($production_data_arr[$date_key][$f_id][$ldata]['prod_reso_allo']==1)
					{
						$line_number=explode(",",$ldata);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}
					}
					else $sewing_line=$lineArr[$ldata];
				//}
				
				/*$sewing_line='';
				if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
				{
				$line_number=explode(",",$prod_reso_arr[$ldata]);
				foreach($line_number as $val)
				{
				if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
				}
				}
				else $sewing_line=$lineArr[$ldata];*/
					  
		 
				$lunch_start="";
				$lunch_start=$line_number_arr[$ldata][$pr_date]['lunch_start_time'];  
				$lunch_hour=$start_time_arr[$row[1]]['lst']; 
				if($lunch_start!="") 
				{ 
				$lunch_start_hour=$lunch_start; 
				}
				else
				{
				$lunch_start_hour=$lunch_hour; 
				}
			  	  
				$production_hour=array();
				for($h=$hour;$h<=$last_hour;$h++)
				{
					 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
					 $production_hour[$prod_hour]=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
					 $floor_production[$prod_hour]+=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
					 $total_production[$prod_hour]+=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
				}				
				
 				$floor_production['prod_hour24']+=$production_data_arr[$date_key][$f_id][$ldata]['prod_hour23'];
				$total_production['prod_hour24']+=$production_data_arr[$date_key][$f_id][$ldata]['prod_hour23'];
				$production_hour['prod_hour24']=$production_data_arr[$date_key][$f_id][$ldata]['prod_hour23']; 
				$line_production_hour=0;
				if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
				{
					if($type_line==2) //No Profuction Line
					{
						$line_start=$production_data_arr[$date_key][$f_id][$l_id]['prod_start_time'];
					}
					else
					{
						$line_start=$line_number_arr[$date_key][$ldata][$pr_date]['prod_start_time'];
					}
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
						 $line_production_hour+=$production_data_arr[$date_key][$f_id][$ldata][$line_hour];
						$line_floor_production+=$production_data_arr[$date_key][$f_id][$ldata][$line_hour];
						$line_total_production+=$production_data_arr[$date_key][$f_id][$ldata][$line_hour];
						$actual_time_hour=$start_hour_arr[$lh+1];
						}
					}
 					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
					
					if($type_line==2)
					{
						if($total_eff_hour>$production_data_arr[$date_key][$f_id][$l_id]['working_hour'])
						{
							 $total_eff_hour=$production_data_arr[$date_key][$f_id][$l_id]['working_hour'];
						}
					}
					else
					{
						if($total_eff_hour>$prod_resource_array[$ldata][$pr_date]['working_hour'])
						{
							$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
						}
					}
					
				}
				if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
				{
					for($ah=$hour;$ah<=$last_hour;$ah++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
						$line_production_hour+=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
						$line_floor_production+=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
						$line_total_production+=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
					}
					if($type_line==2)
					{
						$total_eff_hour=$production_data_arr[$date_key][$f_id][$l_id]['working_hour'];
					}
					else
					{
						$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];	
					}
				}
				 
				if($sewing_day!="")
				{
					$days_run= $diff=datediff("d",$sewing_day,$pr_date);
					$days_active= $active_days_arr[$f_id][$l_id];
				}
				else 
				{
					 $days_run=0;
					 $days_active=0;
				} 
				 
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
					if($type_line==2) //No Production Line
					{
						$smv_adjustmet_type=$production_data_arr[$date_key][$f_id][$l_id]['smv_adjust_type'];
						$eff_target=($production_data_arr[$date_key][$f_id][$l_id]['terget_hour']*$total_eff_hour);

						if($total_eff_hour>=$production_data_arr[$date_key][$f_id][$l_id]['working_hour'])
						{
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$date_key][$f_id][$l_id]['smv_adjust'];
							if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$date_key][$f_id][$l_id]['smv_adjust'])*(-1);
						}
						$efficiency_min+=$total_adjustment+($production_data_arr[$date_key][$f_id][$l_id]['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
					}
					else
					{
						$smv_adjustmet_type=$prod_resource_array[$ldata][$pr_date]['smv_adjust_type'];
						$eff_target=($prod_resource_array[$ldata][$pr_date]['terget_hour']*$total_eff_hour);
						
						if($total_eff_hour>=$prod_resource_array[$ldata][$pr_date]['working_hour'])
						{
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$ldata][$pr_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$ldata][$pr_date]['smv_adjust'])*(-1);
						}
						
						/*$actual_hours=date("H",time())-$line_start_hour; for metro
						$cur_time=date("H",time());
						if($cur_time>$line_start_hour){
							$actual_hours=$actual_hours-1;
						}
						else
						{
							$actual_hours=$actual_hours	;
						}
						$total_eff_hour_custom=($actual_hours>$total_eff_hour)?$total_eff_hour:$actual_hours;

	  					$producting_day=strtotime("Y-m-d",$txt_producting_day);
						
	  					if($producting_day>$today_date)
						{
							
							$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour*60;
							//echo $total_eff_hour."_";
						}
						else
						{
							$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour_custom*60;
							//echo $total_eff_hour_custom."_";
						}*/
						
						
						
						
						$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
					}
				
				
				
				
				if($type_line==2) //No Production Line
				{
					$man_power=$production_data_arr[$date_key][$f_id][$l_id]['man_power'];
					$operator=$production_data_arr[$date_key][$f_id][$l_id]['operator'];
					$helper=$production_data_arr[$date_key][$f_id][$l_id]['helper'];
					$terget_hour=$production_data_arr[$date_key][$f_id][$l_id]['target_hour'];	
					$capacity=$production_data_arr[$date_key][$f_id][$l_id]['capacity'];
					$working_hour=$production_data_arr[$date_key][$f_id][$l_id]['working_hour']; 
					
					$floor_working_hour+=$production_data_arr[$date_key][$f_id][$l_id]['working_hour']; 
					$eff_target_floor+=$eff_target;
					$floor_today_product+=$today_product;
					$floor_avale_minute+=$efficiency_min;
					$floor_produc_min+=$produce_minit; 
					$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
					$floor_capacity+=$production_data_arr[$date_key][$f_id][$l_id]['capacity'];
					$floor_helper+=$production_data_arr[$date_key][$ldata][$l_id]['helper'];
					$floor_man_power+=$production_data_arr[$date_key][$f_id][$l_id]['man_power'];
					$floor_operator+=$production_data_arr[$date_key][$f_id][$l_id]['operator'];
					$total_operator+=$production_data_arr[$date_key][$f_id][$l_id]['operator'];
					$total_man_power+=$production_data_arr[$date_key][$f_id][$l_id]['man_power'];	
					$total_helper+=$production_data_arr[$date_key][$f_id][$l_id]['helper'];
					$total_capacity+=$production_data_arr[$date_key][$f_id][$l_id]['capacity'];
					$floor_tgt_h+=$production_data_arr[$date_key][$f_id][$l_id]['target_hour'];
					$total_working_hour+=$production_data_arr[$date_key][$f_id][$l_id]['working_hour']; 
					$gnd_total_tgt_h+=$production_data_arr[$date_key][$f_id][$l_id]['target_hour'];
					$total_terget+=$eff_target;	
					$grand_total_product+=$today_product;
					$gnd_avable_min+=$efficiency_min;
					$gnd_product_min+=$produce_minit;
					
					$gnd_total_fob_val+=$fob_val; 
					$gnd_final_total_fob_val+=$fob_val;
				}
				else
				{
					$man_power=$prod_resource_array[$ldata][$pr_date]['man_power'];	
					$operator=$prod_resource_array[$ldata][$pr_date]['operator'];
					$helper=$prod_resource_array[$ldata][$pr_date]['helper'];
					$terget_hour=$prod_resource_array[$ldata][$pr_date]['terget_hour'];	
					$capacity=$prod_resource_array[$ldata][$pr_date]['capacity'];
					$working_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
					
					$floor_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
					$floor_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
					$floor_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
					$floor_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
					$floor_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];	
					$floor_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour']; 
					$eff_target_floor+=$eff_target;
					$floor_today_product+=$today_product;
					$floor_avale_minute+=$efficiency_min;
					$floor_produc_min+=$produce_minit; 
					$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
					
					$total_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
					$total_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
					$total_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
					$total_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
					$total_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour']; 
					$gnd_total_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
					$total_terget+=$eff_target;
					$grand_total_product+=$today_product;
					$gnd_avable_min+=$efficiency_min;
					$gnd_product_min+=$produce_minit;
					$gnd_total_fob_val+=$fob_val;
					$gnd_final_total_fob_val+=$fob_val; 
					
				} 
				$po_id=rtrim($production_data_arr[$date_key][$f_id][$ldata]['po_id'],',');
				$garment_itemnameArr=rtrim($garment_itemname,',');
				$po_id=array_unique(explode(",",$po_id));
				$style=rtrim($production_data_arr[$date_key][$f_id][$ldata]['style']);
				$style=implode(",",array_unique(explode(",",$style)));
				$garment_itemname=implode(",",array_unique(explode(',',$garment_itemnameArr)));
				$cbo_get_upto=str_replace("'","",$cbo_get_upto);
				$txt_parcentage=str_replace("'","",$txt_parcentage);
			  
				$floor_name=$floorArr[$f_id];	
				$floor_smv+=$item_smv;
				
				$floor_days_run+=$days_run; 
				$floor_days_active+=$days_active;				
				
				$po_id=$production_data_arr[$date_key][$f_id][$ldata]['po_id'];//$item_ids//$subcon_order_id
				$styles=explode(",",$style);
				 $style_button='';//
				foreach($styles as $sid)
				{
					if( $style_button=='') 
					{ 
						$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
					}
					else
					{
						$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
					}
				}
				$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
				$as_on_current_hour_target=$terget_hour*$cla_cur_time;
				$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;
				 $job_nos=implode(",",array_unique(explode(",",$production_data_arr[$date_key][$f_id][$ldata]['job_no'])));
				  $po_numbers=implode(",",array_unique(explode(",",$production_data_arr[$date_key][$f_id][$ldata]['po_number'])));
				  $file_no=implode(",",array_unique(explode(",",$production_data_arr[$date_key][$f_id][$ldata]['file'])));
			  $ref_no=implode(",",array_unique(explode(",",$production_data_arr[$date_key][$f_id][$ldata]['ref'])));
				if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$html.='<tbody>';
				$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
				$html.='<td class="break_all" width="40">'.$i.'&nbsp;</td>
						<td class="break_all" width="80">'.$date_key.'&nbsp; </td>
						<td class="break_all" width="80">'.$floor_name.'&nbsp; </td>
						<td class="break_all" align="center" width="80" >'. $sewing_line.'&nbsp; </td>
						<td class="break_all" width="80"><p>'.$buyer_name.'&nbsp;</p></td>
						<td class="break_all" width="100"><p>'.$job_nos.'&nbsp;</p></td>
						<td class="break_all" width="140"><p>'.$po_numbers.'&nbsp;</p></td>
						<td class="break_all" width="100"><p>'.$style_button.'&nbsp;</p></td>
						<td class="break_all" width="80"><p>'.$file_no.'&nbsp;</p></td>
						<td class="break_all" width="80"><p>'.$ref_no.'&nbsp;</p></td>
						<td class="break_all" width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
						
						
						<td class="break_all" width="75" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.$line_production_hour.'</a></td>'; 
						
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
							
							$html.='<td class="break_all" align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
							//$html.='<td class="break_all" align="right" width="50"  style=" background-color:#FFFF66" >'.$production_hour[$prod_hour].'&nbsp;kk</td>';
						}
				$html.='</tr>';
				$i++;
				$check_arr[]=$f_id;
			  }
			}
		
		}
	  
	}
	 
	 }//end
			$html.='<tr  bgcolor="#B6B6B6">
					<td class="break_all" width="40">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="140">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="120">&nbsp;</td>
					
					
					
					<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>';
					
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						
						if($floor_tgt_h>$floor_production[$prod_hour])
						{
							$bg_color='background:red';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else if($floor_tgt_h<$floor_production[$prod_hour])
						{
							$bg_color='background:green';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else if($start_hour_arr[$k]==$global_start_lanch)
						{
							 $bg_color='background:yellow';
						//$html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						else
						{
							 $bg_color='';
						//$html.='<td class="break_all" align="right" width="50">'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						$html.='<td class="break_all" align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
					}
									
				   $html.='</tr> </tbody>';
				  
				?>
               
	<fieldset style="width:2230px">
       <table width="2200" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $report_title; ?> &nbsp;Houly Production Monitor Report</strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? //echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
            </tr>
        </table>
        <br />
        <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>
                
               
                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>
                
            
            </tr>
        </table>
       <br/>
        <table id="table_header_1" class="rpt_table" width="2240" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th class="break_all" width="40">SL</th>
                    <th class="break_all" width="80">Prod Date</th>
                    <th class="break_all" width="80">Floor Name</th>
                    <th class="break_all" width="80">Line No</th>
                    <th class="break_all" width="80">Buyer</th>
                    <th class="break_all" width="100">Job</th>
                    <th class="break_all" width="140">Order No</th>
                    <th class="break_all" width="100">Style Ref.</th>
                    <th class="break_all" width="80">File No</th>
                    <th class="break_all" width="80">Ref. No</th>
                    <th class="break_all" width="120">Garments Item</th>
                   <!-- <th class="break_all" width="60">SMV</th>
                    <th class="break_all" width="70">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="60">ManPower</th>
                    <th class="break_all" width="70">Hourly <br>Target (Pcs)</th>
                    <th class="break_all" width="60">Days Run</th> 
                    <th class="break_all" width="60">Active <br>Prod.Days</th>
                    <th class="break_all" width="70">Capacity</th>
                    <th class="break_all" width="60">Working Hour</th>
                    <th class="break_all" width="60">Current Hour</th>
                    <th class="break_all" width="60">As On Current <br>Hour Target (Pcs)</th>
                    <th class="break_all" width="80">Total Target</th>-->
                    <th class="break_all" width="80">Total Prod.</th>
                    <!--<th class="break_all" width="80">As On Current <br>Hour Prod.Variance</th>
                    <th class="break_all" width="80">Total <br>Variance(Pcs)</th>
                    <th class="break_all" width="100">Available<br> Minutes</th>
                    <th class="break_all" width="100">Produce <br>Minutes</th>
                    <th class="break_all" width="60">Target <br>Hit rate</th>
                    <th class="break_all" width="90">Line Effi %</th>
                    <th class="break_all" width="70">FOB Val.</th>-->
                   <?
				
                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
					?>
                      <th class="break_all" width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?	
					}
                ?>
                </tr>
            </thead>
        </table>
        <div style="width:2260px; max-height:520px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table"    width="2240" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th class="break_all" width="40">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                         <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="140">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="120">Total</th>
                        
                         
                        <th class="break_all" align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
                       
                       <!-- <th class="break_all" align="right" width="80">&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo $line_total_production-$total_terget; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_avable_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_product_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60"><? echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="90" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?>&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo number_format($gnd_final_total_fob_val,2);?>&nbsp;</th>-->
					    <? 
						for($k=$hour; $k<=$last_hour; $k++)
						{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						<th class="break_all" align="right" width="50"><? echo $total_production[$prod_hour]; ?></th>
						<?	
						}
                        ?>
                    </tr>
                </tfoot>
            </table>
		</div>
	</fieldset>
  <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>    
	<?    
	/*foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";*/
	exit();      

} // 2nd Button End

if($action=="sewing_line_defect_wise_popup") //finish_line_defect_wise_popup
{
	//echo load_html_head_contents("Po Detail Details", "../../../../", "", $popup,$unicode,'',$amchart);
	//echo load_html_head_contents("Order Wise Budget Report","../../../", 1, 1, $unicode,1,1);
	echo load_html_head_contents("Graph", "../../../../", "", $popup, 1,1);

	//echo load_html_head_contents("Finishing Capacity and Achivment(Iron)", "", "", $popup, $unicode, '', $amchart);//this $amchart is not wark in this page

	extract($_REQUEST);
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$floor_nameArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	
	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_ids'", "id", "po_number");
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
  
   $sql_sew="SELECT a.floor_id,a.prod_reso_allo,a.sewing_line,a.production_quantity, e.id as po_id,f.style_ref_no,f.buyer_name, b.bundle_no,b.defect_type_id, b.defect_point_id, b.defect_qty from pro_garments_production_mst a, wo_po_break_down e, wo_po_details_master f,pro_gmts_prod_dft b where a.id=b.mst_id and a.po_break_down_id=e.id and e.job_no_mst=f.job_no and e.id=b.po_break_down_id  and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0    and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.grouping='$style_ref'   ";
	
	$sew_sql_result=sql_select($sql_sew);
	foreach($sew_sql_result as $row)
	{
		if($row[csf('prod_reso_allo')]==1)
		{
		$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $resource_id : ",".$resource_id;
			}
			$sewing_line=$line_name;
		}
		else $sewing_line=$row[csf('sewing_line')];
		
		 $floor_name=$floor_nameArr[$row[csf('floor_id')]];
		
		$sew_line_arr[$sewing_line][$row[csf('style_ref_no')]]['buyer_name']=$row[csf('buyer_name')];
		$sew_line_arr[$sewing_line][$row[csf('style_ref_no')]]['production_quantity']=$row[csf('production_quantity')];
		$sew_line_alert_qty_arr[$sewing_line][$row[csf('style_ref_no')]][$row[csf('defect_type_id')]][$row[csf('defect_point_id')]]['defect_qty']+=$row[csf('defect_qty')];
	}
	//die;
	
	$sew_fin_alter_defect_type_checkArr=array(1,4,5,6,9,10,11,13,12,16,17,21,22,25,32,36,39,41,42,43,44,45,46,47,48,49,50,51,52,53);
	$sew_fin_spot_defect_type_checkArr=array(2);
	foreach($sew_fin_alter_defect_type as $type_id=>$defect_type)
	 {
		 if(in_array($type_id,$sew_fin_alter_defect_type_checkArr))
		 {
			 $sew_fin_alter_defect_type_new[$type_id]=$defect_type;
		 }
	 }
	 
	 foreach($sew_fin_spot_defect_type as $spot_type_id=>$spot_type)
	 {
		 if(in_array($spot_type_id,$sew_fin_spot_defect_type_checkArr))
		 {
			 $sew_fin_spot_defect_type_new[$type_id]=$spot_type;
		 }
	 }
	
	if($type==4)
	{
		$measurement_discrepancy_arr=array(1=>"WAIST/CHEST",2=>"F/BK LENGTH/ SEAT/HIP",3=>"SLV.LENGTH/THIGH",4=>"SWEEP/INSEAM");
		//echo count($measurement_discrepancy_arr);
		//$tot_defect_spot=count($sew_fin_spot_defect_type);
		$tot_alter_spot_defect=count($sew_fin_alter_defect_type_new)+count($sew_fin_spot_defect_type_new);
		$td_width=720+(70*$tot_alter_spot_defect+count($measurement_discrepancy_arr));
		$row_span=4+$tot_alter_spot_defect;	
	}
	 asort($sew_fin_alter_defect_type_new);
	 asort($sew_fin_spot_defect_type_new);
	 
					
	?>
	<script>
		function print_window()
		{
			//$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="all"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			//$("#table_body_popup tr:first").show();
		}	
	</script>	
	

	<script src="../../../../ext_resource/hschart/hschart.js"></script>
	
	<!--For Graph start-->
<script type="text/javascript">

function hs_chart(gtype,cData,dataTitle){
	//	alert(cData);
	var cData=eval(cData);

    $('#container'+gtype).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
			animation:true,
			borderColor: "#4572A7"
        },
        title: {
            text: 'TOP 6 DEFECT '+dataTitle,
			style: {
				 fontSize: '16px',
				 fontWeight: 'bold'
			  }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth:2
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: dataTitle,
            colorByPoint: true,
				data: cData
        		}]
    });

}
//Measurement Start
function hs_chart_mm(gtype,cData,dataTitle){
		
	var cData=eval(cData);
	//alert(cData);
    $('#container'+gtype).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
			animation:true,
			borderColor: "#4572A7"
        },
        title: {
            text: 'Measurement Top '+dataTitle,
			style: {
				 fontSize: '16px',
				 fontWeight: 'bold'
			  }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth:2
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: dataTitle,
            colorByPoint: true,
				data: cData
        		}]
    });

}
//MM End
function hs_chart_colmun(DefectVal,LineName){
	//alert(DefectVal);
	$('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Defective rates of work (SEWING)'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories:eval(LineName),
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: '',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth: 0
        },
		
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Line',
            data: eval(DefectVal)
        }]
    });
		
}	

</script>
<!--For Graph end-->

 
	<fieldset style="width:<? echo $td_width;?>px; margin-left:3px">
        <div style="width:<? echo $td_width;?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div">
        <style type="text/css">
            .block_div { 
                    width:auto;
                    height:auto; font-size:12px;
                    text-wrap:normal;
                    vertical-align:bottom;
                    display: block;
                    position: !important; 
                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
					padding:0px !important; margin:0px !important;
            }
            .break_all
            {
            	word-wrap: break-word;
            	word-break: break-all;padding:0px !important; margin:0px !important;
            }
        </style> 
            <table rules="all" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="<? echo $row_span;?>" align="center"><strong> <? echo $company_library[$style_owner].'<br>'.'Defective rates of work (SEWING)'.'<br>'.'NAME OF UNIT :-'.$floor_name;?> </strong></td>
                </tr>
               <?
			   ?>
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" >
               <thead>
               <tr>
                 <th colspan="<? echo $row_span;?>" >QUALITY DEFECT</th>
                  <th colspan="4">MEASUREMENT DISCREPANCY</th>
                   <th width="70">&nbsp;</th>
                   <th width="70">&nbsp;</th>
                   <th width="70">&nbsp;</th>
                   <th width="70">&nbsp;</th>
               </tr>
                <tr height="120">
                    <th  width="30">SL</th>
                    <th  width="80">LINE NO</th>
                    <th   width="100">BUYER</th>
                    <th width="100">STYLE</th>
                    <? //sew_fin_spot_defect_type
					
					$id=1;
                     foreach($sew_fin_alter_defect_type_new as $type_id=>$defect_type)
                     {
						
					?>
                    <th  width="70"  style="vertical-align:middle" title="<? echo $id;?>"><div class="block_div" style=" word-break:break-all"><?  echo $defect_type;   ?></div></th>

                     <?
					 $id++;
						
					}
					 foreach($sew_fin_spot_defect_type_new as $spot_type_id=>$spot_defect_type)
                     {
						 
					?>
                    <th   width="70"  style="vertical-align:middle" title="<? echo $id;?>"><div class="block_div"  style=" word-break:break-all"><?  echo $spot_defect_type;   ?></div></th>

                     <?
					  $id++;
						 
					 }
					 foreach($measurement_discrepancy_arr as $measure_type_id=>$measurement_discrep_type)
                     {
					?>
                    <th   width="70"  style="vertical-align:middle" title="<? echo $id;?>"><div class="block_div"  style=" word-break:break-all"><?  echo $measurement_discrep_type;   ?></div></th>

                     <?
					  $id++;
					 }
					 ?>
                    
                    <th  style="vertical-align:middle" width="70"><div class="block_div">TOTAL DEFECT QTY</div></th>
                    <th  style="vertical-align:middle" width="70"><div class="block_div">TOTAL AUDIT QTY</div></th>
                    <th  style="vertical-align:middle" width="70">%</th>
                    <th  style="vertical-align:middle" width="70"><div class="block_div" style=" word-break:break-all">RESPONSEBLE PERSON</div></th>
                   
                </tr>
                 </thead>
                <tbody>
              <!--  <div style="width:<? //echo $td_width+20;?>px; max-height:420px; overflow-y:scroll" id="scroll_body">-->
                <!-- <table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0" id="table_body_popup">-->
                <?
				$i=1;
				$total_defect_qty=$total_adult_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0;
				foreach($sew_line_arr as $line_id=>$line_data)
				{
					foreach($line_data as $style_ref=>$row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="80"><div style="word-wrap:break-word; width:75px"><? echo $lineArr[$line_id]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:90px"><? echo $buyer_library[$row[('buyer_name')]]; ?></div></td>
                        <td width="100"><div style="word-wrap:break-word; width:90px"><? echo $style_ref; ?></div></td>
                         <? //sew_fin_spot_defect_type
                     $alter_defect_qty_arr=array();  $line_alter_defect_qty_arr=array();
					 foreach($sew_fin_alter_defect_type_new as $type_id=>$defect_type)
                     {
						$alter_defect_qty= $sew_line_alert_qty_arr[$line_id][$style_ref][1][$type_id]['defect_qty'];
					?>
                    <td  width="70"  style="" align="right"><?  echo $alter_defect_qty;   ?></td>

                     <?
					 $alter_defect_qty_arr[$type_id]+=$alter_defect_qty;
					  $line_alter_defect_qty_arr[$line_id]+=$alter_defect_qty;
					}
					 foreach($sew_fin_spot_defect_type_new as $spot_type_id=>$spot_defect_type)
                     {
						 $spot_defect_qty= $sew_line_alert_qty_arr[$sewing_line][$style_ref][2][$type_id]['defect_qty'];
					?>
                    <td  width="70" class="" style="" align="right"><?  echo $spot_defect_qty;   ?></td>

                     <?
					  $spot_defect_qty_arr[$spot_type_id]+=$spot_defect_qty;
					    $line_alter_defect_qty_arr[$line_id]+=$spot_defect_qty;
					 }
					 $mm=4;
					 foreach($measurement_discrepancy_arr as $measure_type_id=>$measurement_discrep_type)
                     {
						 $mesurement_qty=$mm+1;
					?>
                    <td  width="70" class="" align="right" style=""><?  echo $mesurement_qty;   ?></td>

                     <?
					  $mesurement_defect_qty_arr[$measure_type_id]+=$mesurement_qty;
					  
					 $line_alter_defect_qty_arr[$line_id]+=$mesurement_qty;
					 $mm++;
					 }
					 ?>
                    
                    <td width="70" align="right"><? echo $line_alter_defect_qty_arr[$line_id];?></td>
                    <td width="70" align="right"> <? echo $row[('production_quantity')];?></td>
                    <td width="70" align="right" title="Total Defect Qty/Adult qty*100"><? 
					$line_percent=$line_alter_defect_qty_arr[$line_id]/$row[('production_quantity')]*100;
					//Graph here
					$line_graph_percent_arr[$lineArr[$line_id]]=$line_percent;
					$line_graph_name_arr[$lineArr[$line_id]]=$lineArr[$line_id];
					
					echo number_format($line_percent,2);?></td>
                     <td width="70" align="right">AK Khan</td>
                    
                       
					</tr>
					<?
					$total_defect_qty+=$line_alter_defect_qty_arr[$line_id];
					$total_adult_qty+=$row[('production_quantity')];
					$i++; 
					}
				}
				?>
				</tbody>
                
				<tfoot>
					<tr class="tbl_bottom">
					<td colspan="4" align="right">Total</td>
                          <? //sew_fin_spot_defect_type
					$sew_defect_top6_arr=array();
                     foreach($sew_fin_alter_defect_type_new as $type_id=>$defect_type)
                     {
					?>
                    <td  width="70" style="" title="DefectType=<? echo $type_id; ?>"  align="right"><?  echo $alter_defect_qty_arr[$type_id];   ?></td>
                     <?
					 $sew_defect_top6_arr[$sew_fin_alter_defect_type[$type_id]]=$alter_defect_qty_arr[$type_id];
					}
					 foreach($sew_fin_spot_defect_type_new as $spot_type_id=>$spot_defect_type)
                     {
					?>
                    <td  width="70" class=""  align="right" style=""><?   echo  $spot_defect_qty_arr[$spot_type_id];   ?></td>

                     <?
					 $sew_defect_top6_arr[$sew_fin_spot_defect_type[$spot_type_id]]=$spot_defect_qty_arr[$spot_type_id];
					 }
					 foreach($measurement_discrepancy_arr as $measure_type_id=>$measurement_discrep_type)
                     {
					?>
                    <td  width="70" class="" align="right" style=""><?  echo $mesurement_defect_qty_arr[$measure_type_id];   ?></td>
                     <?
					 }
					 ?>
                    
                   
                    
                    <td  align="right"><? echo $total_defect_qty;?></td>
                    <td  align="right"><? echo $total_adult_qty;?></td>
                    <td  align="right">&nbsp;</td>
                    <td  align="right">&nbsp;</td>
					</tr>
				</tfoot>
			 </table>
             <br>
             <?
			// ksort($line_graph_percent_arr);
			//print_r($line_graph_percent_arr);
			//ksort($sew_defect_top6_arr);
			foreach($sew_defect_top6_arr as $key=>$val)
			{ 
				if($val>0)
				{
				$sew_defect_top6_arr2[$key]=$val;
				}
			}
			
			  arsort($sew_defect_top6_arr2) ;
			//echo $max_val.',';
			//print_r($sew_defect_top6_arr2);
			 ?>
             <table>
             <tr>
             <td>
			   <table border="1" class="rpt_table" rules="all" width="420" cellpadding="0" cellspacing="0" >
               <caption><b> TOP  6 DEFECT </b> </caption>
               <thead>
               
               <tr>
                 <th>SL</th>
                 <th width="150">Defect description</th>
                 <th width="100">Total Audit qty</th>
                 <th width="100">Top 6 Defect</th>
                 <th>%</th>
               </tr>
               
                 </thead>
                 <tbody>
                 <?
				 $t=1;
                 foreach($sew_defect_top6_arr2 as $key_type=>$top_defect_qty)
				 {
				  
                 	if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					if($t<=6)
					{
						$top_six_percent_graph_arr[$key_type]=($top_defect_qty/$total_adult_qty)*100;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trtop_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trtop_<? echo $t;?>">
                    <td  align="center"><? echo $t;?></td>
                    <td  align="center"><? echo $key_type;?></td>
                    <td  align="right"><? echo $total_adult_qty;?></td>
                    <td  align="right"><? echo $top_defect_qty;?></td>
                    <td  align="right" title="Defect Qty/Audit Qty*100"><? echo number_format((($top_defect_qty/$total_adult_qty)*100),2); ?></td>
                    </tr>
                    <?
					$t++;
					$total_top_defect_qty+=$top_defect_qty;
					}
				 }
					?>
                 </tbody>
                 <tfoot>
                 <tr>
                 <td> <? ?></td>
                 </tr>
                 </tfoot>
                 </table>
                 </td>
                 <td>
                 	 <table border="1" class="rpt_table" rules="all" width="420" cellpadding="0" cellspacing="0" >
                       <caption><b> MEASUREMENT </b> </caption>
                       <thead>
                       
                       <tr>
                         <th>SL</th>
                         <th width="150">DEFECT DESCRIPTION</th>
                         <th width="100">Total Audit qty</th>
                         <th width="100">NO OF DEFECT</th>
                         <th>%</th>
                       </tr>
                       
                         </thead>
                         <tbody>
                         <?
						 $m=1;//$mesurement_defect_qty_arr[$measure_type_id]
						 $measurement_defect_gr_arr=array();
                          foreach($measurement_discrepancy_arr as $measure_type_id=>$measurement_name)
						  {
							  	if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								$measurement_qty=$mesurement_defect_qty_arr[$measure_type_id];
						 ?>
                          	<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trmeasure_<? echo $m; ?>','<? echo $bgcolor;?>')" id="trmeasure_<? echo $m;?>">
                            <td  align="center"><? echo $m;?></td>
                            <td  align="center"><? echo $measurement_name;?></td>
                            <td  align="right"><? echo $total_adult_qty;?></td>
                            <td  align="right"><? echo $measurement_qty;?></td>
                            <td  align="right" title="Defect Qty/Audit Qty*100"><? echo number_format((($measurement_qty/$total_adult_qty)*100),2); ?></td>
                            </tr>
                            <?
							$measurement_defect_gr_arr[$measurement_discrepancy_arr[$measure_type_id]]=($measurement_qty/$total_adult_qty)*100;
							$m++;
						  }
						 // print_r($measurement_defect_gr_arr);
							$caption="Defective rates of work (SEWING)";
						  $line=0;	$lineName_data="[";	$linePercent_data="[";	
						  $tt=count($line_graph_name_arr);
						  foreach($line_graph_name_arr as $lname=>$val)
						  {
							//$linePercent=$linePercent_data[$lname];
							//if($line==0)  $lineName_data.="'".$lname."']"; else $lineName_data.="'".$lname."']".",";
							$line_name_Graph_arr[]=$lname;
							$line_per_Graph_arr[]=$line_graph_percent_arr[$lname];
							//if($line!=$tt)  $linePercent_data.="'".$linePercent."'".",";else   $linePercent_data.="'".$linePercent."']";
						  }
						// echo $lineName_data;
						 
						 $linePercent_dataArr= json_encode($line_per_Graph_arr); 
						$line_graph_name_Arr= json_encode($line_name_Graph_arr); 
						//$month_array= json_encode($month_array); 
							?>
                         </tbody>
                       </table>
                 </td>
                 </tr>
                 <tr>
                 <td width="300">
                 	   
                       <div style="width:100%; height:400px; position:relative; margin-left:10px; border:solid 1px">
                        <table style="margin-left:60px; font-size:12px" align="center">
                            <tr>
                                <td colspan="4" align="center"><b style="margin-left:220px;">Defective rates of work (SEWING)</b></td>
                            </tr>
                        </table>
                        <canvas id="canvas2" height="245" width="700"></canvas>
                    </div>
        
                  </td>
                   <td  width="400">
                   <?
                   //$top_six_percent_graph_arr[$key_type]
				 
				 $lineSixData='[';
				   foreach($top_six_percent_graph_arr as $top_six_defect=>$val)
				   {
					  // $top_six_defect = wordwrap($top_six_defect, 10, "\n", 1);
					 
					$lineSixData.="{name: '".$top_six_defect.':'.number_format($val,2,'.','').'%'."',y: ".$val."},";
				   }
				  	$lineSixData=rtrim($lineSixData,',');
					$lineSixData.=']';
				  // echo  $chart_data_qntyArr.'M';
				  $m=1;
				   ?>
                  <div style="width:100%; height:400px; position:relative; margin-left:10px; border:solid 1px">   
                   
                          <div id="container<? echo $m;?>"></div>
						 <script>hs_chart(<? echo $m;?>,<? echo $lineSixData;?>,'Value');</script>
                    
                    </div>
        
                  </td>
                  <td width="10">&nbsp;
                  
                  </td>
                  <td  width="300">
                   <?
                   //$top_six_percent_graph_arr[$key_type]
				// print_r($measurement_defect_gr_arr);
					$mm_defectData='[';
				   foreach($measurement_defect_gr_arr as $mm_defect=>$mmval)
				   {
					  // $top_six_defect = wordwrap($top_six_defect, 10, "\n", 1);
					// echo $mmval.'m';
					$mm_defectData.="{name: '".$mm_defect.':'.number_format($mmval,2,'.','').'%'."',y: ".$mmval."},";
				   }
				  	$mm_defectData=rtrim($mm_defectData,',');
					$mm_defectData.=']';
				  // echo  $chart_data_qntyArr.'M';
				  $mmm=3;
				   ?>
                  <div style="width:100%; height:400px; position:relative; margin-left:10px; border:solid 1px">   
                   
                          <div id="container<? echo $mmm;?>"></div>
						 <script>hs_chart_mm(<? echo $mmm?>,<? echo $mm_defectData;?>,'Value');</script>
                    
                    </div>
        
                  </td>
                  
                 </tr>
                 </table>
          
<script src="../../../../Chart.js-master/Chart.js"></script>

<script>

var barChartData2 = {
	labels : <? echo $line_graph_name_Arr; ?>,
	datasets : [
			{
				fillColor : "green",
				//strokeColor : "rgba(220,220,220,0.8)",
				//highlightFill: "rgba(220,220,220,0.75)",
				//highlightStroke: "rgba(220,220,220,1)",
				data : <? echo $linePercent_dataArr; ?>
			}
		]
	}
	
	var ctx2 = document.getElementById("canvas2").getContext("2d");
	window.myBar = new Chart(ctx2).Bar(barChartData2, {
		responsive : true
	});
</script>
		</div>
	</fieldset>
	<?
	exit();
}
if($action=="finish_line_defect_wise_popup") //
{
	
	//echo load_html_head_contents("Po Detail Details", "../../../../", "", $popup,$unicode,'',$amchart);
	//echo load_html_head_contents("Order Wise Budget Report","../../../", 1, 1, $unicode,1,1);
	echo load_html_head_contents("Graph", "../../../../", "", $popup, 1,1);

	//echo load_html_head_contents("Finishing Capacity and Achivment(Iron)", "", "", $popup, $unicode, '', $amchart);//this $amchart is not wark in this page

	extract($_REQUEST);
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$floor_nameArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	
	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_ids'", "id", "po_number");
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
  
    $sql_sew="SELECT a.prod_reso_allo,a.floor_id,a.sewing_line,a.production_quantity, e.id as po_id,f.style_ref_no,f.buyer_name, b.bundle_no,b.defect_type_id, b.defect_point_id, b.defect_qty from pro_garments_production_mst a, wo_po_break_down e, wo_po_details_master f,pro_gmts_prod_dft b where a.id=b.mst_id and a.po_break_down_id=e.id and e.job_no_mst=f.job_no and e.id=b.po_break_down_id  and a.production_type=80 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0    and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.grouping='$style_ref'";
	
	$sew_sql_result=sql_select($sql_sew);
	foreach($sew_sql_result as $row)
	{
		if($row[csf('prod_reso_allo')]==1)
		{
		$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $resource_id : ",".$resource_id;
			}
			$sewing_line=$line_name;
		}
		else $sewing_line=$row[csf('sewing_line')];
		
		 $floor_name=$floor_nameArr[$row[csf('floor_id')]];

		//$sewing_line=$row[csf('sewing_line')];
		$sew_line_arr[$sewing_line][$row[csf('style_ref_no')]]['buyer_name']=$row[csf('buyer_name')];
		$sew_line_arr[$sewing_line][$row[csf('style_ref_no')]]['production_quantity']=$row[csf('production_quantity')];
		$sew_line_alert_qty_arr[$sewing_line][$row[csf('style_ref_no')]][$row[csf('defect_type_id')]][$row[csf('defect_point_id')]]['defect_qty']+=$row[csf('defect_qty')];
	}
	
	  $sql_fin_prod="SELECT a.prod_reso_allo,a.floor_id,a.sewing_line,a.production_quantity, e.id as po_id,f.style_ref_no,f.buyer_name from pro_garments_production_mst a, wo_po_break_down e, wo_po_details_master f where  a.po_break_down_id=e.id and e.job_no_mst=f.job_no  and a.production_type=80 and a.status_active=1 and a.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.grouping='$style_ref'";
	
	$fin_sql_result=sql_select($sql_fin_prod);
	foreach($fin_sql_result as $row)
	 {
		if($row[csf('prod_reso_allo')]==1)
		{
		$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $resource_id : ",".$resource_id;
			}
			$sewing_line=$line_name;
		}
		else $sewing_line=$row[csf('sewing_line')];
		
		 $sew_line_prod_qty_arr[$sewing_line][$row[csf('style_ref_no')]]['production_quantity']+=$row[csf('production_quantity')];
	 }
	//die;
	
	$sew_fin_alter_defect_type_checkArr=array(1,2,3,4,5,6,9,10,11,13,12,16,17,21,22);
	$sew_fin_spot_defect_type_checkArr=array(2);
	foreach($sew_fin_woven_defect_array as $type_id=>$defect_type)
	 {
		 if(in_array($type_id,$sew_fin_alter_defect_type_checkArr))
		 {
			 $sew_fin_alter_defect_type_new[$type_id]=$defect_type;
		 }
	 }
	 
	 foreach($sew_fin_woven_defect_array as $spot_type_id=>$spot_type)
	 {
		 if(in_array($spot_type_id,$sew_fin_spot_defect_type_checkArr))
		 {
			 $sew_fin_spot_defect_type_new[$type_id]=$spot_type;
		 }
	 }
	
	if($type==4)
	{
		//$measurement_discrepancy_arr=array(1=>"WAIST/CHEST",2=>"HIP/SWEEP",3=>"THIGH/F/BK LENGTH",4=>"INSEAM/SLV LENGTH");
		//echo count($measurement_discrepancy_arr);
		//$tot_defect_spot=count($sew_fin_spot_defect_type);
		$tot_alter_spot_defect=count($sew_fin_alter_defect_type_new)+count($sew_fin_spot_defect_type_new);
		$td_width=720+(70*$tot_alter_spot_defect+count($sew_fin_measurment_check_array))+count($sew_fin_wash_check_array);
		$row_span=4+$tot_alter_spot_defect;	
	}
	 asort($sew_fin_alter_defect_type_new);
	 asort($sew_fin_spot_defect_type_new);
	 
					
	?>
	<script>
		function print_window()
		{
			//$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="all"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			//$("#table_body_popup tr:first").show();
		}	
	</script>	
	

	<script src="../../../../ext_resource/hschart/hschart.js"></script>
	
	<!--For Graph start-->
<script type="text/javascript">

function hs_chart(gtype,cData,dataTitle){
		//alert(gtype);
	var cData=eval(cData);

    $('#container'+gtype).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
			animation:true,
			borderColor: "#4572A7"
        },
        title: {
            text: 'TOP 6 DEFECT '+dataTitle,
			style: {
				 fontSize: '16px',
				 fontWeight: 'bold'
			  }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth:2
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: dataTitle,
            colorByPoint: true,
				data: cData
        		}]
    });

}
//Measurement Start
function hs_chart_mm(gtype,cData,dataTitle){
		
	var cData=eval(cData);
	//alert(cData);
    $('#container'+gtype).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
			animation:true,
			borderColor: "#4572A7"
        },
        title: {
            text: 'Major defects on workmanship '+dataTitle,
			style: {
				 fontSize: '16px',
				 fontWeight: 'bold'
			  }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth:2
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: dataTitle,
            colorByPoint: true,
				data: cData
        		}]
    });

}
//MM End
function hs_chart_colmun(DefectVal,LineName){
	//alert(DefectVal);
	$('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Defective rates of work (Finishing)'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories:eval(LineName),
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: '',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth: 0
        },
		
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Line',
            data: eval(DefectVal)
        }]
    });
		
}	

</script>
<!--For Graph end-->

	
 

	<fieldset style="width:<? echo $td_width+300;?>px; margin-left:3px">
        <div style="width:<? echo $td_width+300;?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div">
        <style type="text/css">
            .block_div { 
                    width:auto;
                    height:auto; font-size:12px;
                    text-wrap:normal;
                    vertical-align:bottom;
                    display: block;
                    position: !important; 
                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
					padding:0px !important; margin:0px !important;
            }
            .break_all
            {
            	word-wrap: break-word;
            	word-break: break-all;padding:0px !important; margin:0px !important;
            }
          
        </style> 
            <table rules="all" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="<? echo $row_span;?>" align="center"><strong> <? echo $company_library[$style_owner].'<br>'.'Defective rates of work (Finishing)'.'<br>'.'NAME OF UNIT :-'.$floor_name;?> </strong></td>
                </tr>
               <?
               
			   ?>
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" >
               <thead>
               
               <tr>
                 <th colspan="<? echo $row_span;?>" >QUALITY DEFECT</th>
                  <th colspan="3">WASH DEFECTS</th>
                  <th colspan="4">MEASUREMENT DISCREPANCY</th>
                  
                   <th width="70">&nbsp;</th>
                   <th width="70">&nbsp;</th>
                   <th width="70">&nbsp;</th>
                   <th width="70">&nbsp;</th>
               </tr>
                <tr height="120">
                    <th  width="30">SL</th>
                    <th  width="80">LINE NO</th>
                    <th   width="100">BUYER</th>
                    <th width="100">STYLE</th>
                    <? //sew_fin_spot_defect_type
					
					$id=1;
                     foreach($sew_fin_alter_defect_type_new as $type_id=>$defect_type)
                     {
						
					?>
                    <th  width="70"  style="vertical-align:middle" title="<? echo $id;?>"><div class="block_div" style=" word-break:break-all"><?  echo $defect_type;   ?></div></th>

                     <?
					 $id++;
						
					}
					 foreach($sew_fin_spot_defect_type_new as $spot_type_id=>$spot_defect_type)
                     {
						 
					?>
                    <th   width="70"  style="vertical-align:middle" title="<? echo $id;?>"><div class="block_div"  style=" word-break:break-all"><?  echo $spot_defect_type;   ?></div></th>
                     <?
					  $id++;
					 }
					 foreach($sew_fin_wash_check_array as $wash_type_id=>$wash_discrep_type)
                     {
					?>
                    <th   width="70"  style="vertical-align:middle" title="<? echo $id;?>"><div class="block_div"  style=" word-break:break-all"><?  echo $wash_discrep_type;   ?></div></th>
                     <?
					  $id++;
					 }
					 foreach($sew_fin_measurment_check_array as $measure_type_id=>$measurement_discrep_type)
                     {
					?>
                    <th   width="70"  style="vertical-align:middle" title="<? echo $id;?>"><div class="block_div"  style=" word-break:break-all"><?  echo $measurement_discrep_type;   ?></div></th>

                     <?
					  $id++;
					 }
					 ?>
                    
                    <th  style="vertical-align:middle" width="70"><div class="block_div">TOTAL DEFECT QTY</div></th>
                    <th  style="vertical-align:middle" width="70"><div class="block_div">TOTAL AUDIT QTY</div></th>
                    <th  style="vertical-align:middle" width="70">%</th>
                    <th  style="vertical-align:middle" width="70"><div class="block_div" style=" word-break:break-all">RESPONSEBLE PERSON</div></th>
                </tr>
                 </thead>
                <tbody>
              <!--  <div style="width:<? //echo $td_width+20;?>px; max-height:420px; overflow-y:scroll" id="scroll_body">-->
                <!-- <table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0" id="table_body_popup">-->
                <?
				$i=1;$summary_line_wise_defect_arr=array();
				$total_defect_qty=$total_adult_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0;
				foreach($sew_line_arr as $line_id=>$line_data)
				{
					foreach($line_data as $style_ref=>$row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="80"><div style="word-wrap:break-word; width:75px"><? echo $lineArr[$line_id]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:90px"><? echo $buyer_library[$row[('buyer_name')]]; ?></div></td>
                        <td width="100"><div style="word-wrap:break-word; width:90px"><? echo $style_ref; ?></div></td>
                         <? //sew_fin_spot_defect_type
                     $alter_defect_qty_arr=array();  $line_alter_defect_qty_arr=array();
					 foreach($sew_fin_alter_defect_type_new as $type_id=>$defect_type)
                     {
						$alter_defect_qty= $sew_line_alert_qty_arr[$line_id][$style_ref][1][$type_id]['defect_qty']+$sew_line_alert_qty_arr[$line_id][$style_ref][3][$type_id]['defect_qty'];
					?>
                    <td  width="70"  style="" align="right"><?  echo $alter_defect_qty;   ?></td>

                     <?
					 $alter_defect_qty_arr[$type_id]+=$alter_defect_qty;
					
					  $line_alter_defect_qty_arr[$line_id]+=$alter_defect_qty;
					  
					  $summary_line_wise_defect_arr[$line_id][$type_id]+=$alter_defect_qty;
					  // $inside_alter_defect_qty_arr[$line_id][$type_id]+=$alter_defect_qty;
					}
					 foreach($sew_fin_spot_defect_type_new as $spot_type_id=>$spot_defect_type)
                     {
						 $spot_defect_qty= $sew_line_alert_qty_arr[$line_id][$style_ref][2][$spot_type_id]['defect_qty'];
					?>
                    <td  width="70" class="" style="" align="right"><?  echo $spot_defect_qty;   ?></td>

                     <?
					  $spot_defect_qty_arr[$spot_type_id]+=$spot_defect_qty;
					    $line_alter_defect_qty_arr[$line_id]+=$spot_defect_qty;
						$summary_line_wise_defect_arr[$line_id][$spot_type_id]+=$spot_defect_qty;
					 }
					  $nn=4;
					 foreach($sew_fin_wash_check_array as $wash_type_id=>$wash_discrep_type)
                     {
						// echo $sew_line_alert_qty_arr[$line_id][$style_ref][5][$wash_type_id]['defect_qty'].'d';
						 $wash_qty=$sew_line_alert_qty_arr[$line_id][$style_ref][4][$wash_type_id]['defect_qty'];
					?>
                    <td  width="70" class="" align="right" title="Type=<? echo $wash_type_id;?>"><?  echo $wash_qty;   ?></td>

                     <?
					  $wash_defect_qty_arr[$measure_type_id]+=$wash_qty;
					  
					 $line_alter_defect_qty_arr[$line_id]+=$wash_qty;
					 $summary_line_wise_defect_arr[$line_id][$wash_type_id]+=$wash_qty;
					 $nn++;
					 }
					 
					 $mm=4;
					 foreach($sew_fin_measurment_check_array as $measure_type_id=>$measurement_discrep_type)
                     {
						 //echo $sew_line_alert_qty_arr[$line_id][$style_ref][5][$measure_type_id]['defect_qty'].'d';
						 $mesurement_qty=$sew_line_alert_qty_arr[$line_id][$style_ref][5][$measure_type_id]['defect_qty'];
					?>
                    <td  width="70" class="" align="right" title="Type=<? echo $measure_type_id;?>"><?  echo $mesurement_qty;   ?></td>

                     <?
					  $mesurement_defect_qty_arr[$measure_type_id]+=$mesurement_qty;
					  
					 $line_alter_defect_qty_arr[$line_id]+=$mesurement_qty;
					  $summary_line_wise_defect_arr[$line_id][$measure_type_id]+=$mesurement_qty;
					 $mm++;
					 }
					 ?>
                    
                    <td width="70" align="right"><? echo $line_alter_defect_qty_arr[$line_id];?></td>
                    <td width="70" align="right"> <? echo  $production_quantity=$sew_line_prod_qty_arr[$line_id][$style_ref]['production_quantity'];?></td>
                    <td width="70" align="right" title="Total Defect Qty/Adult qty*100"><? 
					$line_percent=$line_alter_defect_qty_arr[$line_id]/$production_quantity*100;
					//Graph here
					$line_graph_percent_arr[$lineArr[$line_id]]=$line_percent;
					$line_graph_name_arr[$lineArr[$line_id]]=$lineArr[$line_id];
					$line_audit_qty_arr[$line_id]+=$production_quantity;
					
					echo number_format($line_percent,2);?></td>
                     <td width="70" align="right">&nbsp;</td>
                    
                       
					</tr>
					<?
					$total_defect_qty+=$line_alter_defect_qty_arr[$line_id];
					$total_adult_qty+=$production_quantity;
					$i++; 
					}
				}
				//print_r($summary_line_wise_defect_arr);
				?>
				</tbody>
                
				<tfoot>
					<tr class="tbl_bottom">
					<td colspan="4" align="right">Total</td>
                          <? //sew_fin_spot_defect_type
					$sew_defect_top6_arr=array();
                     foreach($sew_fin_alter_defect_type_new as $type_id=>$defect_type)
                     {
					?>
                    <td  width="70" style="" title="DefectType=<? echo $type_id; ?>"  align="right"><?  echo $alter_defect_qty_arr[$type_id];   ?></td>
                     <?
					 $sew_defect_top6_arr[$sew_fin_woven_defect_array[$type_id]]=$alter_defect_qty_arr[$type_id];
					 
					 
					}
					 foreach($sew_fin_spot_defect_type_new as $spot_type_id=>$spot_defect_type)
                     {
					?>
                    <td  width="70" class=""  align="right" style=""><?   echo  $spot_defect_qty_arr[$spot_type_id];   ?></td>

                     <?
					 $sew_defect_top6_arr[$sew_fin_spot_defect_type[$spot_type_id]]=$spot_defect_qty_arr[$spot_type_id];
					 }
					  foreach($sew_fin_wash_check_array as $wash_type_id=>$wash_discrep_type)
                     {
					?>
                    <td  width="70" class="" align="right" style=""><?  echo $wash_defect_qty_arr[$wash_type_id];   ?></td>
                     <?
					  $sew_defect_top6_arr[$sew_fin_wash_check_array[$wash_type_id]]=$wash_defect_qty_arr[$wash_type_id];
					 }
					 foreach($sew_fin_measurment_check_array as $measure_type_id=>$measurement_discrep_type)
                     {
					?>
                    <td  width="70" class="" align="right" style=""><?  echo $mesurement_defect_qty_arr[$measure_type_id];   ?></td>
                     <?
					 }
					 ?>
                    <td  align="right"><? echo $total_defect_qty;?></td>
                    <td  align="right"><? echo $total_adult_qty;?></td>
                    <td  align="right">&nbsp;</td>
                    <td  align="right">&nbsp;</td>
					</tr>
				</tfoot>
			 </table>
             <br>
             <?
			// ksort($line_graph_percent_arr);
			//print_r($sew_defect_top6_arr);
			//ksort($sew_defect_top6_arr);
			foreach($sew_defect_top6_arr as $key=>$val)
			{ 
				if($val>0)
				{
				$sew_defect_top6_arr2[$key]=$val;
				}
			}
			
			  arsort($sew_defect_top6_arr2) ;
			//echo $max_val.',';
			//print_r($sew_defect_top6_arr2);
			 ?>
             <table>
             <tr>
             <td>
			   <table border="1" class="rpt_table" rules="all" width="420" cellpadding="0" cellspacing="0" >
               <caption><b> Major defects on workmanship  </b> </caption>
               <thead>
               
               <tr>
                 <th>SL</th>
                 <th width="150">Defect description</th>
                 <th width="100">Total Audit qty</th>
                 <th width="100">Top 6 Defect</th>
                 <th>%</th>
               </tr>
               
                 </thead>
                 <tbody>
                 <?
				 $t=1;
                 foreach($sew_defect_top6_arr2 as $key_type=>$top_defect_qty)
				 {
				  
                 	if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					if($t<=6)
					{
						$top_six_percent_graph_arr[$key_type]=($top_defect_qty/$total_adult_qty)*100;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trtop_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trtop_<? echo $t;?>">
                    <td  align="center"><? echo $t;?></td>
                    <td  align="center"><? echo $key_type;?></td>
                    <td  align="right"><? echo $total_adult_qty;?></td>
                    <td  align="right"><? echo $top_defect_qty;?></td>
                    <td  align="right" title="Defect Qty/Audit Qty*100"><? echo number_format((($top_defect_qty/$total_adult_qty)*100),2); ?></td>
                    </tr>
                    <?
					$t++;
					$total_top_defect_qty+=$top_defect_qty;
					}
				 }
					?>
                 </tbody>
                 <tfoot>
                 <tr>
                 <td> <? ?></td>
                 </tr>
                 </tfoot>
                 </table>
                 </td>
                 <td>
                 	 <table border="1" class="rpt_table" rules="all" width="420" cellpadding="0" cellspacing="0" >
                       <caption><b> MEASUREMENT </b> </caption>
                       <thead>
                       
                       <tr>
                         <th>SL</th>
                         <th width="150">DEFECT DESCRIPTION</th>
                         <th width="100">Total Audit qty</th>
                         <th width="100">NO OF DEFECT</th>
                         <th>%</th>
                       </tr>
                       
                         </thead>
                         <tbody>
                         <?
						 $m=1;//$mesurement_defect_qty_arr[$measure_type_id]
						 $measurement_defect_gr_arr=array();
                          foreach($sew_fin_measurment_check_array as $measure_type_id=>$measurement_name)
						  {
							  	if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								$measurement_qty=$mesurement_defect_qty_arr[$measure_type_id];
						 ?>
                          	<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trmeasure_<? echo $m; ?>','<? echo $bgcolor;?>')" id="trmeasure_<? echo $m;?>">
                            <td  align="center"><? echo $m;?></td>
                            <td  align="center"><? echo $measurement_name;?></td>
                            <td  align="right"><? echo $total_adult_qty;?></td>
                            <td  align="right"><? echo $measurement_qty;?></td>
                            <td  align="right" title="Defect Qty/Audit Qty*100"><? echo number_format((($measurement_qty/$total_adult_qty)*100),2); ?></td>
                            </tr>
                            <?
							$measurement_defect_gr_arr[$sew_fin_measurment_check_array[$measure_type_id]]=($measurement_qty/$total_adult_qty)*100;
							$m++;
						  }
						 // print_r($measurement_defect_gr_arr);
							$caption="Defective rates of work (SEWING)";
						  $line=0;	$lineName_data="[";	$linePercent_data="[";	
						  $tt=count($line_graph_name_arr);
						  foreach($line_graph_name_arr as $lname=>$val)
						  {
							//$linePercent=$linePercent_data[$lname];
							//if($line==0)  $lineName_data.="'".$lname."']"; else $lineName_data.="'".$lname."']".",";
							$line_name_Graph_arr[]=$lname;
							$line_per_Graph_arr[]=$line_graph_percent_arr[$lname];
							//if($line!=$tt)  $linePercent_data.="'".$linePercent."'".",";else   $linePercent_data.="'".$linePercent."']";
						  }
						// echo $lineName_data;
						 
						 $linePercent_dataArr= json_encode($line_per_Graph_arr); 
						$line_graph_name_Arr= json_encode($line_name_Graph_arr); 
						//$month_array= json_encode($month_array); 
							?>
                         </tbody>
                       </table>
                 </td>
                 </tr>
                 <tr>
                 <td width="300">
                 	   
                       <div style="width:100%; height:400px; position:relative; margin-left:10px; border:solid 1px">
                        <table style="margin-left:60px; font-size:12px" align="center">
                            <tr>
                                <td colspan="4" align="center"><b style="margin-left:220px;">Defective rates of work (Finishing)</b></td>
                            </tr>
                        </table>
                        <canvas id="canvas2" height="245" width="700"></canvas>
                    </div>
        
                  </td>
                   <td  width="400">
                   <?
                   //$top_six_percent_graph_arr[$key_type]
				// print_r($top_six_percent_graph_arr);
				 $lineSixData='[';
				   foreach($top_six_percent_graph_arr as $top_six_defect=>$val)
				   {
					  // $top_six_defect = wordwrap($top_six_defect, 10, "\n", 1);
					 
					$lineSixData.="{name: '".$top_six_defect.':'.number_format($val,2,'.','').'%'."',y: ".$val."},";
				   }
				  	$lineSixData=rtrim($lineSixData,',');
					$lineSixData.=']';
				  // echo  $chart_data_qntyArr.'M';
				  $m=1;
				   ?>
                  <div style="width:100%; height:400px; position:relative; margin-left:10px; border:solid 1px">   
                   
                          <div id="container<? echo $m;?>"></div>
						 <script>hs_chart(<? echo $m;?>,<? echo $lineSixData;?>,'Value');</script>
                    
                    </div>
        
                  </td>
                  <td width="10">&nbsp;
                  
                  </td>
                  <td  width="300">
                   <?
                   //$top_six_percent_graph_arr[$key_type]
				// print_r($measurement_defect_gr_arr);
					$mm_defectData='[';
				   foreach($measurement_defect_gr_arr as $mm_defect=>$mmval)
				   {
					  // $top_six_defect = wordwrap($top_six_defect, 10, "\n", 1);
					// echo $mmval.'m';
					$mm_defectData.="{name: '".$mm_defect.':'.number_format($mmval,2,'.','').'%'."',y: ".$mmval."},";
				   }
				  	$mm_defectData=rtrim($mm_defectData,',');
					$mm_defectData.=']';
				  // echo  $chart_data_qntyArr.'M';
				  $mmm=3;
				   ?>
                  <div style="width:100%; height:400px; position:relative; margin-left:10px; border:solid 1px">   
                   
                          <div id="container<? echo $mmm;?>"></div>
						 <script>hs_chart_mm(<? echo $mmm?>,<? echo $mm_defectData;?>,'Value');</script>
                    
                    </div>
        
                  </td>
                   <td  width="100">
                   </td>
                    <td  width="650">
                    <table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" >
                       
                       <thead>
                       <tr>
                         <th rowspan="2" width="100"><div class=""  style=" word-break:break-all">LINE NO</div></th>
                         <th colspan="4" width="">IN SIDE</th>
                         <th colspan="4" width="">Top SIDE</th>
                         <th colspan="4" width="">GetUp</th>
                         </tr>
                         <tr>
                         <th width="100">AUDIT</th> 
                         <th width="100">DEFECT</th>
                         <th width="100">PASS</th>
                         <th>%</th>
                          <th width="100">AUDIT</th> 
                         <th width="100">DEFECT</th>
                         <th width="100">PASS</th>
                         <th>%</th>
                          <th width="100">AUDIT</th> 
                         <th width="100">DEFECT</th>
                         <th width="100">PASS</th>
                         <th>%</th>
                        </tr>
                        </thead>
                        <?
						$s=1;$total_audit_inside_qty=$total_inside_qty=$total_inside_pass_qty=$total_topside_qty=$total_topside_pass_qty=$total_getup_qty=$total_inside_dif_qty=$total_inside_dif_qty=$total_getup_pass_qty=0;
						//print_r($summary_line_wise_defect_arr);
                        foreach($sew_line_arr as $lineid=>$val)
						{
						
							 if ($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							 
						?>
                     
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trsum_<? echo $s; ?>','<? echo $bgcolor;?>')" id="trsum_<? echo $s;?>">
                        <td  width="70" class="" align="right" style=""><?  echo $lineArr[$lineid];   ?></td>
                        <td  width="70" class="" align="right" style=""><?  echo $line_audit_qty_arr[$lineid];  ?></td>
                        <td  width="70" class="" align="right" style=""><?   echo $summary_line_wise_defect_arr[$line_id][1];   ?></td>
                        <td  width="70" class="" align="right" style=""><?   $inside_dif_qty=$line_audit_qty_arr[$lineid]-$summary_line_wise_defect_arr[$line_id][1]; 
																				echo number_format($inside_dif_qty,2);   ?></td>
                        <td  width="" class="" align="right" style="" title="Defect/Audit*100"><?  echo number_format(($summary_line_wise_defect_arr[$line_id][1]/$line_audit_qty_arr[$lineid])*100,2);   ?></td>
                        <td  width="70" class="" align="right" style=""><?  echo $line_audit_qty_arr[$lineid];   ?></td>
                        <td  width="70" class="" align="right" style=""><?  echo  $summary_line_wise_defect_arr[$line_id][2];   ?></td>
                        <td  width="70" class="" align="right" style=""><?   $topside_dif_qty=$line_audit_qty_arr[$lineid]-$summary_line_wise_defect_arr[$line_id][2]; 
																				echo number_format($topside_dif_qty,2); ;   ?></td>
                        <td  width="" class="" align="right" style=""><?  echo number_format(($summary_line_wise_defect_arr[$line_id][2]/$line_audit_qty_arr[$lineid])*100,2);    ?></td>
                        <td  width="70" class="" align="right" style=""><?  echo $line_audit_qty_arr[$lineid];   ?></td>
                        <td  width="70" class="" align="right" style=""><?  echo $summary_line_wise_defect_arr[$line_id][3];;   ?></td>
                        <td  width="70" class="" align="right" style=""><?  $getup_dif_qty=$line_audit_qty_arr[$lineid]-$summary_line_wise_defect_arr[$line_id][3]; 
																				echo number_format($getup_dif_qty,2);echo $getup_dif_qty;   ?></td>
                        <td  width="" class="" align="right" style=""><?    echo number_format(($summary_line_wise_defect_arr[$line_id][3]/$line_audit_qty_arr[$lineid])*100,2);;   ?></td>
                        
                       </tr>
                        <?
						$s++;
						 $total_audit_inside_qty+=$line_audit_qty_arr[$lineid];
						  $total_inside_qty+=$summary_line_wise_defect_arr[$line_id][1];
						 $total_topside_qty+=$summary_line_wise_defect_arr[$line_id][2];
						 $total_getup_qty+=$summary_line_wise_defect_arr[$line_id][3];
						  $total_inside_pass_qty+=$inside_dif_qty;
						  $total_topside_pass_qty+=$topside_dif_qty;
						  $total_getup_pass_qty+=$getup_dif_qty;/**/
							
						}
						?>
                        <tfoot>
                        <tr>
                        <td> PROCESS TOTAL</td>
                        <td align="right"> <? echo number_format($total_audit_inside_qty,2); ?></td>
                        <td align="right"> <? echo number_format($total_inside_qty,2); ?></td>
                        <td align="right"> <? echo number_format($total_inside_pass_qty,2); ?></td>
                         <td align="right"> <? echo number_format(($total_inside_qty/$total_audit_inside_qty*100),2); ?></td>
                         
                         <td align="right"> <? echo number_format($total_audit_inside_qty,2); ?></td>
                         <td align="right"> <? echo number_format($total_topside_qty,2); ?></td>
                          <td align="right"> <? echo number_format($total_topside_pass_qty,2); ?></td>
                         <td align="right"> <? echo number_format(($total_topside_qty/$total_audit_inside_qty*100),2); ?></td>
                        
                          <td> <? echo number_format($total_audit_inside_qty,2); ?></td>
                         <td align="right"> <? echo number_format($total_getup_qty,2); ?></td>
                          <td align="right"> <? echo number_format($total_getup_pass_qty,2); ?></td>
                         <td align="right"> <? echo number_format(($total_getup_qty/$total_audit_inside_qty*100),2); ?></td>
                         
                        </tr>
                         <tr>
                        <td> Grand TOTAL</td>
                        <td colspan="4" align="right"> <? $tot_grd_audit_inside=$total_audit_inside_qty+$total_audit_inside_qty+$total_audit_inside_qty;echo number_format($tot_grd_audit_inside,2); ?></td>
                        <td colspan="4" align="right"> <? $tot_grd_audit_top=$total_inside_qty+$total_topside_qty+$total_getup_qty;echo number_format($tot_grd_audit_top,2); ?></td>
                        <td colspan="3" align="right"> <? echo number_format($total_topside_pass_qty+$total_inside_pass_qty+$total_getup_pass_qty,2); ?></td>
                         <td align="right"> <? echo number_format(($tot_grd_audit_top/$tot_grd_audit_inside)*100,2); ?></td>
                         
                        </tr>
                        </tfoot>
                          
                         
                         </table>
                    </td>
                 </tr>
                 </table>
          


<?
        if(count($line_graph_name_Arr)>0)
		{
		?>
         <script src="../../../../Chart.js-master/Chart.js"></script>
<script>

var barChartData2 = {
	labels : <? echo $line_graph_name_Arr; ?>,
	datasets : [
			{
				fillColor : "green",
				//strokeColor : "rgba(220,220,220,0.8)",
				//highlightFill: "rgba(220,220,220,0.75)",
				//highlightStroke: "rgba(220,220,220,1)",
				data : <? echo $linePercent_dataArr; ?>
			}
		]
	}
	
	var ctx2 = document.getElementById("canvas2").getContext("2d");
	window.myBar = new Chart(ctx2).Bar(barChartData2, {
		responsive : true
	});
</script>
<?
	}
?>
</div>
</fieldset>
	
	<?
	exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>

                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;

		$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date,
		CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
		CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);

                foreach($sql_dtls as $row_real)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <?
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}
if($action=="po_popup")
{
	echo load_html_head_contents("Po Detail Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	
	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_ids'", "id", "po_number");
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	if($type==1)
	{
		$td_width=710;
		$row_span=4;	
	}
	else if($type_id==2)
	{
		$td_width=710+80;
		$row_span=5;	
	}
	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px;">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center" style="margin-left:10px;">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="5" align="left"><strong> Order Details </strong></td>
                </tr>
               
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="80">Master Style</th>
                    <th width="80">Style Ref</th>
                    <th width="80">PO No</th>
                    <th width="60">PO Qty.</th>
                    <th width="80">Plan Cut Qty.</th>
                      <th width="80">Lay Cut Qty.</th>
                    <th width="80">Cutting Qty.</th>
                  
                    <th width="80">Ship Qty.</th>
                   
                    <th>Cut To Ship Ratio</th>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
					
				$sql_lay_cut=sql_select("select e.order_id as po_id,d.id,a.job_no,d.cutting_no as cutting_no,d.marker_length,d.cad_marker_cons,c.marker_qty,e.marker_qty as size_qty from wo_po_details_master a, ppl_cut_lay_mst d,ppl_cut_lay_dtls c,ppl_cut_lay_size e where a.job_no=d.job_no   and d.id=c.mst_id and e.dtls_id=c.id and e.mst_id=d.id  and d.entry_form=289 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and e.order_id in($po_ids)  order by d.id asc");
			 
				foreach($sql_lay_cut as $row)
				{
					$lay_cut_arr[$row[csf('po_id')]]['laycut']+=$row[csf('size_qty')];
				}
		
				$act_ahip_arr=sql_select("select po_break_down_id,
				sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as shipout_qty,
				sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty
				from  pro_ex_factory_mst where status_active=1 and is_deleted=0 and po_break_down_id in($po_ids) group by po_break_down_id");//
				 
				
				foreach($act_ahip_arr as $row)
				{
					$actual_shipout_arr[$row[csf('po_break_down_id')]]['shipout_qty']=$row[csf('shipout_qty')]-$row[csf('return_qnty')];
				}
			 unset($act_ahip_arr);
		$cut_qc_arr=sql_select("SELECT a.po_break_down_id as po_id,(b.production_qnty) as qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_break_down_id in($po_ids)");
				foreach( $cut_qc_arr as $row )
				{
					 $cuting_prod_arr[$row[csf("po_id")]]["qnty"]+=$row[csf("qnty")];
				}
				unset($cut_qc_arr);
	 
				 $po_sql="select  a.style_ref_no,a.total_set_qnty as ratio,b.id as po_id,b.plan_cut,b.po_number,b.po_received_date,b.shipment_date as orgi_shipment_date,b.grouping as ref_no	,b.is_confirmed,b.pub_shipment_date,b.po_quantity as po_qty, b.unit_price as unit_price from wo_po_break_down b,wo_po_details_master a where  a.id=b.job_id and b.id in($po_ids) and b.status_active=1 and b.is_deleted=0";
				$po_sql_result=sql_select($po_sql); $i=1;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=$tot_cutting_qty=$tot_lay_cut=0;
				foreach($po_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$shipout_qty=$actual_shipout_arr[$row[csf('po_id')]]['shipout_qty'];
					 $cuting_prod_qty=$cuting_prod_arr[$row[csf("po_id")]]["qnty"];
					 if($shipout_qty>0) $cut_to_ship_ratio=($shipout_qty/$cuting_prod_qty)*100;else $cut_to_ship_ratio=0;
					 $lay_cut=$lay_cut_arr[$row[csf('po_id')]]['laycut'];
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('ref_no')]; ?></div></td>
						<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('style_ref_no')]; ?></div></td>
                        <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('po_number')]; ?></div></td>
                         
						<td width="60" align="right"><p><? echo number_format($row[csf('po_qty')]*$row[csf('ratio')],0); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf('plan_cut')],0); ?></p></td>
                        
                         <td width="80" align="right"><p><? echo number_format($lay_cut,0); ?></p></td>
                          <td width="80" align="right"><p><? echo number_format($cuting_prod_qty,0); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($shipout_qty,0); ?></p></td>
                       
						<td width="" align="right" title="Ship Qty(<? echo $cuting_prod_qty;?>)/Cuti Qty*100"><p><? echo number_format($cut_to_ship_ratio,2); ?></p></td>
                       
					</tr>
					<?
					$tot_po_qty+=$row[csf('po_qty')]*$row[csf('ratio')];
					$tot_plan_cut_qty+=$row[csf('plan_cut')];
						$tot_shipout_qty+=$shipout_qty;
					$tot_cutting_qty+=$cuting_prod_qty;
					$tot_cut_to_ship_ratio+=$cut_to_ship_ratio;
					$tot_lay_cut+=$lay_cut;
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="<? echo $row_span ?>" align="right">Total</td>
                        
						<td align="right"><? echo number_format($tot_po_qty,0); ?></td>
                        <td align="right"><? echo number_format($tot_plan_cut_qty,0); ?>&nbsp;</td>
                        
                         <td align="right"><? echo number_format($tot_lay_cut,0); ?>&nbsp;</td>
                          <td align="right"><? echo number_format($tot_cutting_qty,0); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_shipout_qty,0); ?>&nbsp;</td>
                       
                       
                        <td align="right"><? echo number_format($tot_shipout_qty/$tot_cutting_qty*100,0); ?></td>
					</tr>
				</tfoot>
			</table>
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
} //Po wise button end
if($action=="cuting_qc_popup")
{
	echo load_html_head_contents("Cutting QC Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	
//	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_ids'", "id", "po_number");
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	if($type==2)
	{
		$td_width=670;
		$row_span=4;	
	}
	
	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px; margin-left:3px">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="5" align="left"><strong> Cutting QC Details </strong></td>
                </tr>
               
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Cut Date</th>
                    <th width="110">Cut No</th>
                    <th width="80">Total Bundle No</th>
                    <th width="60">Cut Qty.</th>
                    <th width="90">QC Pass Qty</th>
                    <th width="90">Rej Qty.</th>
                    <th>Rep Qty.</th>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
				
	
				$cut_qc_arr="SELECT a.cut_no,a.production_date,sum(b.production_qnty) as qnty,sum(b.reject_qty) as reject_qty,sum(b.replace_qty) as replace_qty,sum(c.size_qty) as size_qty from pro_garments_production_mst a,pro_garments_production_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and b.bundle_no=c.bundle_no and a.production_type=1 and b.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.po_break_down_id in($po_ids)  group by a.production_date,a.cut_no";
		
				$cut_sql_result=sql_select($cut_qc_arr); $i=1;
				$tot_cut_qty=$tot_size_qty=$tot_reject_qty=$tot_replace_qty=0;
				foreach($cut_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('production_date')]; ?></div></td>
						<td width="110"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('cut_no')]; ?></div></td>
                        <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('po_number')]; ?></div></td>
                         
						<td width="60" align="right"><p><? echo number_format($row[csf('qnty')],0); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row[csf('size_qty')]); ?></p></td>
						<td width="90" align="right"><p><? echo number_format($row[csf('reject_qty')],0); ?></p></td>
						<td width="" align="right" title=""><p><? echo number_format($row[csf('replace_qty')],0); ?></p></td>
                       
					</tr>
					<?
					$tot_cut_qty+=$row[csf('qnty')];
					$tot_size_qty+=$row[csf('size_qty')];
					$tot_reject_qty+=$row[csf('reject_qty')];
					$tot_replace_qty+=$row[csf('replace_qty')];
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="<? echo $row_span ?>" align="right">Total</td>
                        
						<td align="right"><? echo number_format($tot_cut_qty,0); ?></td>
                        <td align="right"><? echo number_format($tot_size_qty,0); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_reject_qty,0); ?>&nbsp;</td>
                       
                        <td align="right"><? echo number_format($tot_replace_qty,0); ?></td>
					</tr>
				</tfoot>
			</table>
            <?
            //TNA Information actual_start_date ,actual_finish_date
		$tna_start_sql=sql_select( "select id,po_number_id,
								
								(case when task_number=84 then task_start_date else null end) as cutting_start_date,
								(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
								
								(case when task_number=84 then actual_start_date else null end) as actual_start_date,
								(case when task_number=84 then task_finish_date else null end) as actual_finish_date
								
		from tna_process_mst
		where status_active=1 and po_number_id in($po_ids)");
		
		$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
		$tna_date_task_arr=array();
		foreach($tna_start_sql as $row){
			 
			 
					if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
					{
						$cutting_start_date=$row[csf("cutting_start_date")];
						$cutting_end_date=$row[csf("cutting_end_date")];
					}
					if($row[csf("actual_start_date")]!="" && $row[csf("actual_start_date")]!="0000-00-00")
					{
						$actual_start_date=$row[csf("actual_start_date")];
						$actual_finish_date=$row[csf("actual_finish_date")];
					}
					 
		}

			?>
            <br>
            
            	<table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="200" colspan="2">&nbsp;</th>
                    <th width="70">Start</th>
                    <th width="80">End</th>
                </thead>
                <tbody>
                <tr>
                <td rowspan="3">Cutting TNA</td>
                <td>Plan</td>
                <td><? echo $cutting_start_date;?></td>
                <td><? echo $cutting_end_date;?></td>
                </tr>
                <tr>
                
                <td>Actual</td>
                <td><? echo $actual_start_date;?></td>
                <td><? echo $actual_finish_date;?></td>
                </tr>
                <tr>
                 
                <td>delay/Early</td>
                <td><? echo $plan_diff1 = datediff( "d", $actual_start_date, $cutting_start_date);?></td>
                <td><? echo $actual_diff1 = datediff( "d", $actual_finish_date, $cutting_end_date);?></td>
                </tr>
                
                </tbody>
                </table>
            </table>
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
} //Po wise button end
?>