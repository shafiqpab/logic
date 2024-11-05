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

$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
$dealing_library=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
$leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");
//"select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name"
//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/woven_master_style_report_controller', this.value, 'load_drop_down_brand', 'brand_td');" );     	 
	exit();
}
if ($action=="load_drop_down_brand")
{
	//echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_id_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 100, "select id, brand_name from lib_buyer_brand brand where buyer_id in($data) and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
if ($action=="load_drop_down_season")
{
	//$data_arr = explode("*", $data);
	//if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_season_id", $width, "select id, season_name from lib_buyer_season where buyer_id in($data) and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_job_no; ?>'+'**'+'<? echo $txt_job_id; ?>'+'**'+'<? echo $style_owner; ?>', 'style_ref_list_view', 'search_div', 'woven_master_style_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$slect_year  from wo_po_details_master a where  is_deleted=0  $buyer_cond $year_cond $job_cond $search_con  $style_owner_cond $comp_cond order by job_no_prefix_num";
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
                    <th>Buyer<? //echo $companyID.'fff';
					 
                       		if($type==1)
							{
							 $msg="Inqury Id";
							  $msg_date="Shipment";
							}
							else {
							  $msg="Order No";
							  $msg_date="Shipment";
							}
								?>
					 </th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter <? echo $msg;?></th>
                    <th><? echo $msg_date;?> Date</th>
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
                       		if($type==1)
							{
								$search_by_arr=array(1=>"Inquery No",2=>"Master Style");
							}
							else
							{
								$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							}
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('txt_job_id').value+'**'+'<? echo $buyerID; ?>'+'**'+'<? echo $type; ?>'+'**'+'<? echo $style_owner; ?>', 'create_order_no_search_list_view', 'search_div', 'woven_master_style_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
//	if($style_owner>0) $style_owner_cond="and a.style_owner=$style_owner";else $style_owner_cond="";
	if($company_id>0) $comp_cond="and a.company_name in($company_id)";else $comp_cond="";
	if($type_id==1)
	{
	if($search_by==1) $search_field="c.system_number_prefix_num"; //grouping
	else if($search_by==2) $search_field="b.grouping"; 	
	else $search_field="a.job_no";
	}
	else
	{
		if($search_by==1) $search_field="b.po_number"; //grouping
	else if($search_by==2) $search_field="a.style_ref_no"; 	
	else $search_field="a.job_no";
	}
		
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
	//if($pre_cost_ver_id==1) $entry_form_cond="and c.entry_from=111";
	//else $entry_form_cond="and c.entry_from=158";
	if($type_id==1)
	{
	$js_select="inquery_id,grouping";
	} else $js_select="job_id,style_ref_no";
	
	if($type_id==1)
	{
  $sql="select b.id,c.id as inquery_id,a.id as job_id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b,wo_quotation_inquery c where a.job_no=b.job_no_mst and b.grouping=c.style_refernce  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grouping is not null and $search_field like '$search_string' $buyer_id_cond $date_cond $entry_form_cond $style_owner_cond $comp_cond order by b.id, b.pub_shipment_date";
 echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Master Style, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "$js_select","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,grouping,pub_shipment_date","",'','0,0,0,0,0,0,3','',0) ;
   exit(); 
	}
	else
	{
		$sql="select b.id,a.id as job_id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and  b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grouping is not null and $search_field like '$search_string' $buyer_id_cond $date_cond $entry_form_cond $style_owner_cond $comp_cond order by b.id, b.pub_shipment_date";
 echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Master Style, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "$js_select","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,grouping,pub_shipment_date","",'','0,0,0,0,0,0,3','',0) ;
   exit(); 
	}
		
	
}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst",'job_no','costing_per');
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	
	$txt_job_no=trim(str_replace("'","",$txt_job_no));
	$txt_job_id=trim(str_replace("'","",$txt_job_id));
	$hide_order_id=trim(str_replace("'","",$hide_order_id));
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	$txt_style_ref=trim(str_replace("'","",$txt_style_ref));
	$txt_style_id=trim(str_replace("'","",$txt_style_id));
	//$shipping_status_id=trim(str_replace("'","",$cbo_shipping_status));
	$cbo_brand_id=trim(str_replace("'","",$cbo_brand_id));
	

	$txt_date_from_rec=trim(str_replace("'","",$txt_date_from_rec));
	$txt_date_to_rec=trim(str_replace("'","",$txt_date_to_rec));
	
	$txt_date_from_target=trim(str_replace("'","",$txt_date_from_target));
	$txt_date_to_target=trim(str_replace("'","",$txt_date_to_target));
	
	$date_cond="";$date_cond_target="";
	//echo $txt_date_from_rec.'ds';
	if(str_replace("'","",$txt_date_from_rec)!="" && str_replace("'","",$txt_date_to_rec)!="")
		{
			if($db_type==0)
			{
				$start_date_rec=change_date_format(str_replace("'","",$txt_date_from_rec),"yyyy-mm-dd","");
				$end_date_rec=change_date_format(str_replace("'","",$txt_date_to_rec),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date_rec=change_date_format(str_replace("'","",$txt_date_from_rec),"","",1);
				$end_date_rec=change_date_format(str_replace("'","",$txt_date_to_rec),"","",1);
			}
			$date_cond=" and c.inquery_date between '$start_date_rec' and '$end_date_rec'";
			//$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}//applying_period_date,applying_period_to_date
		if(str_replace("'","",$txt_date_from_target)!="" && str_replace("'","",$txt_date_to_target)!="")
		{
			if($db_type==0)
			{
				$start_date_rec_target=change_date_format(str_replace("'","",$txt_date_from_target),"yyyy-mm-dd","");
				$end_date_rec_target=change_date_format(str_replace("'","",$txt_date_to_target),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date_rec_target=change_date_format(str_replace("'","",$txt_date_from_target),"","",1);
				$end_date_rec_target=change_date_format(str_replace("'","",$txt_date_to_target),"","",1);
			}
			$date_cond_target=" and c.con_rec_target_date between '$start_date_rec' and '$end_date_rec'";
			//$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}//applying_period_date,applying_period_to_date
		//echo $date_cond.'d';
		
	
	if($company_name>0) $comp_cond="and a.company_name in($company_name)";else $comp_cond="";
	if($company_name>0) $comp_cond2="and c.company_id in($company_name)";else $comp_cond2="";
	//if($style_owner>0) $style_owner_cond="and a.style_owner in($style_owner)";else $style_owner_cond="";
	if($cbo_brand_id>0) $brand_name_cond="and a.brand_id in($cbo_brand_id)";else $brand_name_cond="";
	if($cbo_brand_id>0) $brand_name_cond2="and c.brand_id in($cbo_brand_id)";else $brand_name_cond2="";
	if($cbo_season_id>0) $season_name_cond="and a.season_buyer_wise in($cbo_season_id)";else $season_name_cond="";
	if($cbo_season_id>0) $season_name_cond2="and c.season_buyer_wise in($cbo_season_id)";else $season_name_cond2="";
	$season_year=str_replace("'","",$cbo_season_year);
	if(trim($season_year)!=0) $season_year_cond=" and a.season_year=$season_year"; else $season_year_cond="";
	if(trim($season_year)!=0) $season_year_cond2=" and c.season_year=$season_year"; else $season_year_cond2="";
	
	if($txt_ref_no=="") $int_ref_cond=""; else $int_ref_cond=" and b.grouping='$txt_ref_no' ";
	if($txt_ref_no=="") $int_ref_cond2=""; else $int_ref_cond2=" and c.style_refernce='$txt_ref_no' ";
	if($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='$txt_style_ref' ";
	//if($hide_order_id=="") $hide_po_id_cond=""; else $hide_po_id_cond=" and b.grouping in($hide_order_id) ";
	//$job_no=str_replace("'","",$txt_job_no);
	//echo $job_no;die;
	/*if ($txt_job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($txt_job_no) ";
	if($txt_job_id!="")
	{
	 $job_id_cond=" and a.id in($txt_job_id) ";
	 $job_no_cond="";
	}
	else { 
	  	 $job_id_cond=""; 
	}*/
	//echo $txt_job_id.'=='.$job_no_cond;die;
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") {
				 $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				  $buyer_id_cond2=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				 } else
				  {
					  $buyer_id_cond="";
					  $buyer_id_cond2="";
				  }
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and c.buyer_id in($cbo_buyer_name)";
	}

	
	
	/*if($db_type==0)
		{
			if ($start_date=="" && $end_date=="") $date_cond=""; else $date_cond="and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
		}
		else
		{
			if ($start_date=="" && $end_date=="") $date_cond=""; else $date_cond="and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
		}
		*/
	/*if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond=" $year_field_con=$cbo_year"; else $year_cond="";
	}
*/
	

	//$date_from=str_replace("'","",$txt_date_from);
	//$date_to=str_replace("'","",$txt_date_to);



	 
	

	
	ob_start();
	$width_td=3150;
	?>
	<br/>
	<div>
        <table width="<? echo $width_td;?>">
            <tr class="form_caption">
                <td colspan="38" align="center"><strong><? echo $hader_caption; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="38" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
             
              <tr style="font-size:13px">
                	<th colspan="17">New Development Status (Inquiry)</th>
                    <th colspan="5">Consumption Entry [CAD] For LA Costing</th>
                    <th colspan="9">Buyer Costing</th>
                    <th colspan="7">SMEE Receive</th>
                   
                    
              <tr/>
               <tr style="font-size:13px">
                       	<th width="20">SL</th>
                       	<th width="110">Com. Short Name</th>
                       	<th width="100">Inquiry Date</th>
                        <th width="100">Insert By</th>
                       	<th width="100">Dealing Marchant</th>
                      	<th width="100">Concern Merchant</th>
                       	<th width="80">Inquiry ID</th>
                       	<th width="80">Buyer</th>
                        <th width="80">Style Des.</th>
                       	<th width="80">Master Style</th>
                       	<th width="80">Season</th>
                       	<th width="80">Brand</th>
                       	<th width="80"> Cons. Rec.Tgt.Date</th>
                       <th width="80">Act. Quot. Date</th>
                       <th width="80">File</th> 
                       	<th width="80">Mail Send Date</th>
                       	<th width="80" title="17">Mail Send Time</th>
                        
                       	<th width="80">System No.</th>
                       	<th width="80">Insert By</th>    
                      	<th width="80"> Cons. Date</th>
                       <th width="80" title="">Pattern Master Name</th>  
                       <th width="80">BOM NO.</th>  
                       <th width="80">Cost Sheet No</th>
                       <th width="80" title=""> Costing Date</th>  
                       <th width="80">Costing Due Date</th> 
                        <th width="80" title="">Insert By</th>  
                        <th width="80">FOB ($)/Pcs</th>  
                        <th width="80" title="">Approve Status</th>
                        <th width="80">Last Revise. No</th>  
                        <th width="80">Last Opti. No</th>  
                        <th width="80">Meeting Remarks</th>
                        <th width="80">Insert By</th> 
                        <th width="80">Team Leaders</th>
                        <th width="80">Concern Merchan</th>
                        <th width="80">Inset Date</th>
                        <th width="80">No Of March Style</th>  
                        <th width="80">No Of Confirm PO</th>
                        <th width="">Total Order Qty.</th>
                    </tr>
            </thead>
        </table>
        <div style="width:<? echo $width_td+20;?>px; max-height:250px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <?
		
		$total_po_qty_pcs=$total_sewing_reject_pcs=$total_fabric_defect_reject_pcs=$total_sample_pcs=$total_ok_gmt_after_ship=$total_tot_defect_reject=$total_missing=$total_poly_reject_pcs=0;
			
 $sql_po="select c.id as inquery_id,a.id as job_id,a.job_no_prefix_num,a.inserted_by as po_insert_by,a.insert_date as po_insert_date,a.dealing_marchant as po_dealing_marchant,a.team_leader, a.job_no,a.company_name,a.buyer_name, a.style_ref_no, a.avg_unit_price, a.total_set_qnty as ratio,b.po_quantity, b.id as po_id, b.po_number,b.grouping, b.unit_price, b.shiping_status,b.is_confirmed, b.pub_shipment_date,b.grouping as ref_no,c.inquery_date,c.insert_by,c.system_number_prefix_num,c.dealing_marchant,c.concern_marchant,c.style_description,c.style_refernce,c.season_buyer_wise, c.season_year,c.brand_id,c.actual_req_quot_date,c.actual_sam_send_date,c.actual_sam_send_date,c.con_rec_target_date,c.system_number from wo_po_details_master a, wo_po_break_down b, wo_quotation_inquery c where a.job_no=b.job_no_mst and b.grouping=c.style_refernce and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $comp_cond $style_owner_cond   $buyer_id_cond $season_year_cond $job_no_cond   $job_id_cond $order_id_cond_trans $order_no_cond $brand_name_cond $date_cond_target $date_cond $season_name_cond  $int_ref_cond  $style_ref_cond  order by c.id ";
		 
		//echo $sql_po;//die;
		$result_po=sql_select($sql_po); 
	//	$tot_rows=count($result_po);
		foreach($result_po as $row)
		{
			$master_stlye_arr[$row[csf('inquery_id')]]['po_qty_pcs']+=$row[csf('po_quantity')]*$row[csf('ratio')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_dealing_marchant']=$dealing_library[$row[csf('po_dealing_marchant')]];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_id'].=$row[csf('po_id')].',';
			$master_stlye_arr[$row[csf('inquery_id')]]['grouping'].=$row[csf('grouping')].',';
			$master_stlye_arr[$row[csf('inquery_id')]]['plan_cut']+=$row[csf('plan_cut')];
			$master_stlye_arr[$row[csf('inquery_id')]]['shiping_status']=$row[csf('shiping_status')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_insert_by']=$row[csf('po_insert_by')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_insert_date']=$row[csf('po_insert_date')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_quantity']+=$row[csf('po_quantity')];
			$master_stlye_arr[$row[csf('inquery_id')]]['team_leader']=$leader_library[$row[csf('team_leader')]];
			
			$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no_prefix_num')];
			$style_ref_arr[$row[csf('job_no')]]=$row[csf('style_ref_no')];
			$master_ref_arr[$row[csf('job_no')]]=$row[csf('ref_no')];
			
			
			$inquery_id_arr[$row[csf('inquery_id')]]=$row[csf('inquery_id')];
		}
	 unset($result_po);
		  $sql_inq="select c.id as inquery_id,c.inquery_date,c.mail_send_date,c.buyer_id,c.concern_marchant,c.insert_by,c.company_id as company_name,c.system_number_prefix_num,c.dealing_marchant,c.concern_marchant,c.style_description,c.style_refernce,c.season_buyer_wise, c.season_year,c.brand_id,c.actual_req_quot_date,c.actual_sam_send_date,c.actual_sam_send_date,c.con_rec_target_date,c.system_number from  wo_quotation_inquery c where  c.status_active=1 and c.is_deleted=0   $comp_cond2    $buyer_id_cond2 $season_year_cond2  $brand_name_cond2 $date_cond_target $date_cond $season_name_cond2  $int_ref_cond2  $style_ref_cond  order by c.id ";
		 
		//echo $sql_po;//die;
		$result_sql_inq=sql_select($sql_inq); $i=1;
		$tot_rows=count($result_sql_inq);
		foreach($result_sql_inq as $row)
		{
			if(strtotime($row[csf('season_year')])!='')
			{
			$season_year=date('y',strtotime($row[csf('season_year')]));
			} else $season_year="";
			//echo $season_year.'d';
			//$season_yearArr=explode("-",$season_year);
			
			
			$master_stlye_arr[$row[csf('inquery_id')]]['insert_by']=$row[csf('insert_by')];
			$master_stlye_arr[$row[csf('inquery_id')]]['inquery_id']=$row[csf('inquery_id')];
			$master_stlye_arr[$row[csf('inquery_id')]]['system_number']=$row[csf('system_number')];
			$master_stlye_arr[$row[csf('inquery_id')]]['sys_prefixNo']=$row[csf('system_number_prefix_num')];
			$master_stlye_arr[$row[csf('inquery_id')]]['inquery_date']=$row[csf('inquery_date')];
			$master_stlye_arr[$row[csf('inquery_id')]]['con_rec_target_date']=$row[csf('con_rec_target_date')];
			$master_stlye_arr[$row[csf('inquery_id')]]['actual_req_quot_date']=$row[csf('actual_req_quot_date')];
			$master_stlye_arr[$row[csf('inquery_id')]]['actual_sam_send_date']=$row[csf('actual_sam_send_date')];
			$master_stlye_arr[$row[csf('inquery_id')]]['mail_send_date']=$row[csf('mail_send_date')];
			$master_stlye_arr[$row[csf('inquery_id')]]['concern_marchant']=$dealing_library[$row[csf('concern_marchant')]];
			$master_stlye_arr[$row[csf('inquery_id')]]['buyer_name']=$row[csf('buyer_id')];
			$master_stlye_arr[$row[csf('inquery_id')]]['company_name']=$row[csf('company_name')];
			$master_stlye_arr[$row[csf('inquery_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$master_stlye_arr[$row[csf('inquery_id')]]['dealing_marchant']=$dealing_library[$row[csf('dealing_marchant')]];
			//$master_stlye_arr[$row[csf('inquery_id')]]['concern_marchant']=$dealing_library[$row[csf('concern_marchant')]];
			
	
			$master_stlye_arr[$row[csf('inquery_id')]]['style_description']=$row[csf('style_description')];
			$master_stlye_arr[$row[csf('inquery_id')]]['style_refernce']=$row[csf('style_refernce')];
			$master_stlye_arr[$row[csf('inquery_id')]]['season_buyer_wise']=$season_arr[$row[csf('season_buyer_wise')]].'-'.$season_year;
			$master_stlye_arr[$row[csf('inquery_id')]]['brand_id']=$brand_name_arr[$row[csf('brand_id')]];
		
			
			//season_buyer_wise, c.season_year,c.brand_id
			//$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
//$brand_name_arr
			/*$master_stlye_arr[$row[csf('inquery_id')]]['po_qty_pcs']+=$row[csf('po_quantity')]*$row[csf('ratio')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_dealing_marchant']=$dealing_library[$row[csf('po_dealing_marchant')]];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_id'].=$row[csf('po_id')].',';
			$master_stlye_arr[$row[csf('inquery_id')]]['grouping'].=$row[csf('grouping')].',';
			$master_stlye_arr[$row[csf('inquery_id')]]['plan_cut']+=$row[csf('plan_cut')];
			$master_stlye_arr[$row[csf('inquery_id')]]['shiping_status']=$row[csf('shiping_status')];
			
			
			$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no_prefix_num')];
			$style_ref_arr[$row[csf('job_no')]]=$row[csf('style_ref_no')];
			$master_ref_arr[$row[csf('job_no')]]=$row[csf('ref_no')];*/
			$inquery_id_arr[$row[csf('inquery_id')]]=$row[csf('inquery_id')];
		}
		$sql_consCosting=sql_select("select a.inquiry_id,a.system_no as system_no,a.inserted_by,a.la_costing_date,a.pattern_master,a.bom_no from consumption_la_costing_mst a  where  a.status_active=1 and a.is_deleted=0  ".where_con_using_array($inquery_id_arr,0,'a.inquiry_id')."  order by a.inquiry_id asc");
	//	echo "select a.inquiry_id,a.system_no as system_no,a.inserted_by,a.la_costing_date,a.pattern_master,a.bom_no from consumption_la_costing_mst a  where  a.status_active=1 and a.is_deleted=0  ".where_con_using_array($inquery_id_arr,0,'a.inquiry_id')."  order by a.inquiry_id asc";
		//echo "select a.inquiry_id,a.system_no as system_no,a.inserted_by,a.la_costing_date,a.pattern_master,a.bom_no from consumption_la_costing_mst a, wo_quotation_inquery_fab_dtls d where a.id=d.mst_id and d.status_active=1 and d.is_deleted=0  ".where_con_using_array($inquery_id_arr,0,'a.inquiry_id')."  order by a.inquiry_id asc";
		foreach($sql_consCosting as $row)
		{
		//	$master_ref=$master_ref_arr[$row[csf('job_no')]];
			 
			$cons_costing_arr[$row[csf('inquiry_id')]]['system_no']=$row[csf('system_no')];
			$cons_costing_arr[$row[csf('inquiry_id')]]['cons_inserted_by']=$row[csf('inserted_by')];
			$cons_costing_arr[$row[csf('inquiry_id')]]['la_costing_date']=$row[csf('la_costing_date')];
			$cons_costing_arr[$row[csf('inquiry_id')]]['bom_no']=$row[csf('bom_no')];
			$cons_costing_arr[$row[csf('inquiry_id')]]['pattern_master']=$row[csf('pattern_master')];
			 
		}
		$sql_buyerCosting=sql_select("select a.cost_sheet_no,a.inquery_id, a.approved,a.revise_no as revise_no,option_id,a.cost_sheet_no as cost_sheet_no,a.inserted_by,a.costing_date,a.delivery_date as due_date,a.buyer_remarks,b.tot_fob_cost from qc_mst a,qc_tot_cost_summary b where a.qc_no=b.mst_id and a.status_active=1 and a.is_deleted=0  ".where_con_using_array($inquery_id_arr,0,'a.inquery_id')."  order by a.inquery_id asc");
		//echo "select a.cost_sheet_no,a.inquery_id, a.revise_no as revise_no,option_id,a.cost_sheet_no as cost_sheet_no,a.inserted_by,a.costing_date,a.delivery_date as due_date,a.buyer_remarks from qc_mst a where  a.status_active=1 and a.is_deleted=0  ".where_con_using_array($inquery_id_arr,0,'a.inquery_id')."  order by a.inquery_id asc";
	 
		foreach($sql_buyerCosting as $row)
		{
		//	$master_ref=$master_ref_arr[$row[csf('job_no')]];
			 
			$buyer_costing_arr[$row[csf('inquery_id')]]['revise_no'].=$row[csf('revise_no')].',';
			$buyer_costing_arr[$row[csf('inquery_id')]]['option_id'].=$row[csf('option_id')].',';
			$buyer_costing_arr[$row[csf('inquery_id')]]['cons_inserted_by']=$row[csf('inserted_by')];
			$buyer_costing_arr[$row[csf('inquery_id')]]['due_date']=$row[csf('due_date')];
			$buyer_costing_arr[$row[csf('inquery_id')]]['costing_date']=$row[csf('costing_date')];
			$buyer_costing_arr[$row[csf('inquery_id')]]['cost_sheet_no']=$row[csf('cost_sheet_no')];
			$buyer_costing_arr[$row[csf('inquery_id')]]['approved']=$yes_no[$row[csf('approved')]];
			$buyer_costing_arr[$row[csf('inquery_id')]]['tot_fob_cost']=$row[csf('tot_fob_cost')];
			$buyer_costing_arr[$row[csf('inquery_id')]]['buyer_remarks']=$row[csf('buyer_remarks')];
			 
		}
	
		
		foreach($master_stlye_arr as $inquery_id=>$row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			if($company_name) $company_name=$company_name;else $company_name=0;
			//if($style_owner) $style_owner=$style_owner;else $style_owner=0;
			
			/*$job_no=rtrim($row[('job_no')],',');
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
			
			}*/
			 
			$int_ref_no=rtrim($row[('grouping')],',');
			$po_ids=rtrim($row[('po_id')],',');
			$no_of_int_ref_noArr=array_unique(explode(",",$int_ref_no));
			$no_of_po_idsArr=array_unique(explode(",",$po_ids));
		
			 
			
			$cons_system_no=$cons_costing_arr[$inquery_id]['system_no'];
			$cons_inserted_by=$cons_costing_arr[$inquery_id]['cons_inserted_by'] ;
			$la_costing_date=$cons_costing_arr[$inquery_id]['la_costing_date'];
			$bom_no=$cons_costing_arr[$inquery_id]['bom_no'];
			$pattern_master=$cons_costing_arr[$inquery_id]['pattern_master'];
			
			$revise_no=rtrim($buyer_costing_arr[$row[csf('inquiry_id')]]['revise_no'],',');
			$option_id=rtrim($buyer_costing_arr[$row[csf('inquiry_id')]]['option_id'],',');
			
			$revise_noArr=array_unique(explode(",",$revise_no));
			$last_revise_no=max($revise_noArr);
			$option_idArr=array_unique(explode(",",$option_id));
			$last_option_id=max($option_idArr);
			
			$buyer_inserted_by=$buyer_costing_arr[$inquery_id]['cons_inserted_by'];
			$buyer_due_date=$buyer_costing_arr[$inquery_id]['due_date'];
			$buyer_costing_date=$buyer_costing_arr[$inquery_id]['costing_date'];
			$cost_sheet_no=$buyer_costing_arr[$inquery_id]['cost_sheet_no'];
			$buyer_approved=$buyer_costing_arr[$inquery_id]['approved'];
			$meeting_remarks=$buyer_costing_arr[$inquery_id]['buyer_remarks'];
			$tot_fob_cost=$buyer_costing_arr[$inquery_id]['tot_fob_cost'];
			
			$po_dealing_marchant=$master_stlye_arr[$inquery_id]['po_dealing_marchant'];
			$po_insert_by=$master_stlye_arr[$inquery_id]['po_insert_by'];
			$po_team_leader=$master_stlye_arr[$inquery_id]['team_leader'];
			$po_insert_date=$master_stlye_arr[$inquery_id]['po_insert_date'];$po_insert_date=$master_stlye_arr[$inquery_id]['po_insert_date'];
			//qc_show_file
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
				<td width="20" ><? echo $i; ?></td>
				<td width="110"><p><? echo $company_library[$row[('company_name')]]; ?></p></td>
				<td width="100" ><p><? echo change_date_format($row[('inquery_date')]); ?></p></td>
				<td width="100"><p><? echo  $user_arr[$row[('insert_by')]]; ?></p></td>
                <td width="100"><p><? echo  $row[('dealing_marchant')]; ?></p></td>
				<td width="100"><p> <? echo  $row[('concern_marchant')]; ?></p></td>
				<td width="80" title="SysNo=<? echo $row[('system_number')];?>" align="center"><p><a href="##" onClick="report_generate_inquery_pop('<? echo $row[('company_name')]; ?>','<? echo $row[('system_number')]; ?>','<? echo $inquery_id; ?>','report_file_generate',2)"><? echo $row[('sys_prefixNo')]; ?></a></p></td>
				<td width="80"><p><? echo $buyer_library[$row[('buyer_name')]]; ?></p></td>
				<td width="80" ><p><? echo $row[('style_description')]; ?></p></td>
                <td width="80" ><p><? echo $row[('style_refernce')]; ?></p></td>
				
				<td width="80" align="center"><p><? echo $row[('season_buyer_wise')]; ?></p></td>
				<td width="80" align="center"><p><? echo $row[('brand_id')]; ?></a></p></td>
				
                <td width="80" align="center"><p><? echo change_date_format($row[('con_rec_target_date')]); ?></p></td>
                  <td width="80" align="center"><p><? echo change_date_format($row[('actual_req_quot_date')]); ?></p></td>
				<td width="80" align="center" title=""><p>
                <a href="##" onClick="openmypage_file('<? echo $row[('company_name')]; ?>','<? echo $inquery_id; ?>','show_file',1)"><? echo "View";   ?></a></p></td>
				
                <td width="80" align="center"><p><? $mail_date=explode(" ",$row[('mail_send_date')]);echo change_date_format($mail_date[0]); ?></p></td>
				<td width="80" align="center" title="Woven Recv-Issue"><p><?  echo  $mail_date[1];
				?></p></td>
				<td width="80" align="center" ><p><? echo $cons_system_no; ?></p></td>
				<td width="80" align="right" title="Woven Recv-Fabric used"><p><?
				echo $user_arr[$cons_inserted_by]; ?></p></td>
				<td width="80" align="center" title=""><p><?  echo change_date_format($la_costing_date); ?></p></td>
				<td width="80" align="center" title=""><p><? 
				 echo $pattern_master;
				 ?></p></td>
				<td width="80" align="center"><p><? echo $bom_no; ?>  </p></td>
                 
				<td width="80" align="center"><p><? echo $cost_sheet_no; ?></p></td>
				<td width="80" align="center"  title=""><p><? echo $buyer_costing_date; ?></p></td>
				<td width="80" align="center" title=""><p><? echo $buyer_due_date; ?></p></td>
				<td width="80" align="center" title=" "><p><?   echo $user_arr[$buyer_inserted_by]; ?></p></td>
				<td width="80" align="right" title="FOB"><p><? echo $tot_fob_cost; ?></p></td>

				<td width="80" align="center"><p><? echo $buyer_approved; ?></p></td>
				<td width="80" align="center"><p><? echo $last_revise_no; ?></p></td>
				<td width="80" align="center"><p><? echo $last_option_id; ?></p></td>

				<td width="80" align="center" title="<? echo $meeting_remarks;?>"><p><a href="##" onClick="report_meeting_popup('<? echo $company_name; ?>','<? echo $inquery_id; ?>','report_generate_meeting_remark',3)"><? if($meeting_remarks!="") echo "View";else echo ""; ?></a></p></td>
				<td width="80" align="center" title=""><p><? echo $user_arr[$po_insert_by]; ?></p></td>
				<td width="80" align="center"><p><? echo $po_team_leader; ?></p></td>
				<td width="80" align="center" title=""><p><? echo $po_dealing_marchant; ?></p></td>
                <td width="80" align="center" title=""><p> <? echo $po_insert_date;?></p></td>
                
				<td width="80" align="center"><p><? echo count($no_of_int_ref_noArr); ?></p></td>
				<td width="80" align="center"><p><? echo count($no_of_po_idsArr);; ?></p></td>
				 
				 
				<td align="right" title=""><p><? echo $row[('po_quantity')]; ?></p></td>
			</tr>
			
			<?
				
				$total_po_qty_pcs+=$row[('po_quantity')];
			
			$i++;
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
               
                <td width="80"><? //echo number_format($total_po_qty_pcs,0);?></td>
                <td width="80"><? //echo number_format($total_plan_cut,0);?></td>
                <td width="80"></td>
                <td width="80"><? //echo number_format($total_first_length,0);?></td>
                <td width="80"><? //echo number_format($total_cad_marker_cons,2);?></td>
                
                <td width="80"><? //echo number_format($total_lay_cut_qty,2);?></td>
                <td width="80"><? //echo number_format($total_cut_prod_qty,2);?></td>
              
                <td width="80"><? //echo number_format($total_req_fin_fab_qty,2);?></td>
                <td width="80"><? //echo number_format($total_wv_recv_fin,2);?></td>
                <td width="80"><? //echo number_format($total_wv_issue_fin,2);?></td>
                <td width="80"><? //echo number_format($total_fab_bal_store_qty,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_used_qty,4);?></td>
                
                <td width="80"><? //echo number_format($total_fabric_bal_after_cut_qty,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_saved_cuting_qty,2);?></td>
                <td width="80"><? //echo number_format($total_excess_cut_against,2);?></td>
                
                <td width="80"><? //echo number_format($total_ship_qty,2);?></td>
                <td width="80"><? //echo number_format($total_ship_qty,2);?></td>
                <td width="80"><? //echo number_format($total_cut_to_ship_ratio,2);?></td>
                
                <td width="80"><? //echo number_format($total_other_cost_conv,2);?></td>

                <td width="80"><? //echo number_format($total_excess_short_against_order,2);?></td>
                <td width="80"><? //echo number_format($total_tot_embell_cost,2);?></td>

                <td width="80"><? //echo number_format($total_sewing_out,2);?></td>
                
                <td width="80"><? //echo number_format($total_send_wash,2);?></td>
                <td width="80"><? //echo number_format($total_recv_wash,2);?></td>

                <td width="80"><? //echo number_format($total_sewing_defect,2);?></td>
                
                <td width="80"><? //echo number_format($total_short_recv_from_wash,2);?></td>
                <td width="80"><? //echo number_format($total_wash_reject_pcs,2);?></td>
                
                <td width="80"><? //echo number_format($total_sewing_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_poly_reject_pcs,2);?></td>
                
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80">Total</td>
                
               
                <td align="right" id="po_qty_td"><? echo number_format($total_po_qty_pcs,2);?></td>
            </tr>
            
        </table>
    </div>
    <?
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



//Ex-Factory Delv. and Return


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
if($action=="show_file") //report_generate_meeting_remark
{
	echo load_html_head_contents("File","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$update_id' and form_name='quotation_inquery' and is_deleted=0 and file_type=2";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$update_id' and form_name='quotation_inquery' and is_deleted=0 and file_type=2");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        { 
		 
        ?>
         
          <td><a href="../../../../<? echo $row[csf('image_location')]; ?>" target="_new"> 
        <img src="../../../../file_upload/blank_file.png" width="80" height="60"> </a>
        </td>
        <?
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}

if($action=="report_generate_meeting_remark") //report_generate_meeting_remark
{
	echo load_html_head_contents("Meeting Remark","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$update_id' and form_name='quotation_inquery' and is_deleted=0 and file_type=2";
	$data_array=sql_select("select a.revise_no,a.option_id,b.remarks  from qc_mst a,qc_meeting_mst b  where a.qc_no=b.mst_id and a.inquery_id='$inquery_id' and a.buyer_remarks is not null  and a.is_deleted=0 ");
	// echo "select a.revise_no,a.option_id,a.buyer_remarks  from qc_mst a  where a.inquery_id='$inquery_id' and a.buyer_remarks is not null  and a.is_deleted=0";
	foreach ($data_array as $row)
	{
		if($row[csf('remarks')]!="")
		{
		$revise_arr[$row[csf('revise_no')]]=$row[csf('remarks')];
		$option_arr[$row[csf('option_id')]]=$row[csf('remarks')];
		}
	}
	unset($data_array);
	ksort($revise_arr);
	ksort($option_arr);
		//print_r($revise_arr);
		$td_width=550;
	?>
    <div align="center">
    <fieldset style="width:<? echo $td_width?>px; margin-left:3px">
    <table class="rpt_table" width="500" cellpadding="0" cellspacing="0" border="0" rules="all">
    <caption> <b> Meeting Remarks</b></caption>
    <tr>
    <td>
   
        <?
		$k=1;
        foreach ($revise_arr as $revise_key=>$revise_val)
        { 
		?>
         <table class="rpt_table" width="250" cellpadding="0" cellspacing="0" border="1" rules="all">
        <tr>
        <th align="left"> Revised No -<? echo $revise_key;?></th>
        </tr>
        <?
		 	if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
        ?>
          <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
         <td title="<? echo $revise_key;?>"> 
        <?  echo $revise_val;?>
        </td>
         </tr>
          </table>
        <?
		$k++;
        }
        ?>
       
       </td>
       <td>
      
        <?
		$m=1;
        foreach ($option_arr as $option_key=>$option_val)
        { 
		?>
         <table class="rpt_table" width="250" cellpadding="0" cellspacing="0" border="1" rules="all">
        <tr>
        <th  align="left"> Option No-<? echo $option_key;?></th>
        </tr>
        <?
		 	if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
        ?>
          <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trm_<? echo $m; ?>','<? echo $bgcolor;?>')" id="trm_<? echo $m;?>">
          <td title="<? echo $option_key;?>"> 
        <?  echo $option_val;?>
        </td>
         </tr>
         </table>
        <?
		$m++;
        }
        ?>
        
     
       </td>
        </tr>
       
    </table>
    </fieldset>
    </div>
    <?
}

?>