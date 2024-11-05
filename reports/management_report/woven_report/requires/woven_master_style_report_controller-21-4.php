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
$user_brand_id=$_SESSION['logic_erp']["brand_id"];
$user_id=$_SESSION['logic_erp']['user_id'];

$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
$dealing_library=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
$leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");
//"select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name"
//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 0, "-- All Buyer --", $selected, "load_drop_down( 'requires/woven_master_style_report_controller', this.value, 'load_drop_down_brand', 'brand_td');" );     	 
	exit();
}
if ($action=="load_drop_down_brand")
{
	//echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_id_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 100, "select id, brand_name from lib_buyer_brand brand where buyer_id in($data) and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 0, "--Brand--", $selected, "" );
	exit();
}
if ($action=="load_drop_down_season")
{
	//$data_arr = explode("*", $data);
	//if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id in($data) and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 0, "-- Select Season--", "", "" );
	exit();
}

if ($action=="load_drop_down_dealing")
{
	//$data_arr = explode("*", $data);
	//if($data_arr[1] == 1) $width=90; else $width=150;
	if($data) $deal_cond="and team_id in($data)";else $deal_cond="";
	echo create_drop_down( "cbo_dealing_merchant", 140, "select id,team_member_name from lib_mkt_team_member_info where   status_active =1 and is_deleted=0 $deal_cond order by team_member_name","id,team_member_name", 0, "-- Select Dealing--", "", "" );
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
	$cbo_brand_id=trim(str_replace("'","",$cbo_brand_id));
	
	$cbo_team_leader=str_replace("'","",$cbo_team_leader);
	$dealing_merchant=str_replace("'","",$cbo_dealing_merchant);

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
	
	if($cbo_team_leader>0) $team_leader_cond="and a.team_leader in($cbo_team_leader)";else $team_leader_cond="";
	if($dealing_merchant>0) $dealing_cond="and a.dealing_marchant in($dealing_merchant)";else $dealing_cond="";
	if($dealing_merchant>0) $dealing_cond2="and c.dealing_marchant in($dealing_merchant)";else $dealing_cond2="";
	//if($style_owner>0) $style_owner_cond="and a.style_owner in($style_owner)";else $style_owner_cond="";
	//echo $cbo_brand_id; dealing_marchant
	if($cbo_team_leader>0)
	{
		$dealing_cond3="and a.id in($cbo_team_leader)";
	}
	else $dealing_cond3="";
	//echo $brand_cond.'SS';;
	if($cbo_brand_id>0) 
	{ 
	 $brand_name_cond="and a.brand_id in($cbo_brand_id)";
	 $brand_name_cond2="and c.brand_id in($cbo_brand_id)";
	}
	else
	{
		if($user_brand_id)
		{
		 $brand_name_cond="and a.brand_id in($user_brand_id)";
		 $brand_name_cond2="and c.brand_id in($user_brand_id)";
		}
		else
		{
			$brand_name_cond="";
			$brand_name_cond2="";
		}
	}
	//echo $brand_name_cond2;
	//else $brand_name_cond=""; //$user_brand_id
	//if($cbo_brand_id>0) $brand_name_cond2="and c.brand_id in($cbo_brand_id)";else $brand_name_cond2="";
	if($cbo_season_id>0) $season_name_cond="and a.season_buyer_wise in($cbo_season_id)";else $season_name_cond="";
	if($cbo_season_id>0) $season_name_cond2="and c.season_buyer_wise in($cbo_season_id)";else $season_name_cond2="";
	$season_year=str_replace("'","",$cbo_season_year);
	if(trim($season_year)!=0) $season_year_cond=" and a.season_year=$season_year"; else $season_year_cond="";
	if(trim($season_year)!=0) $season_year_cond2=" and c.season_year=$season_year"; else $season_year_cond2="";
	
	if($txt_ref_no=="") $int_ref_cond=""; else $int_ref_cond=" and b.grouping='$txt_ref_no' ";
	if($txt_ref_no=="") $int_ref_cond2=""; else $int_ref_cond2=" and c.style_refernce='$txt_ref_no' ";
	if($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='$txt_style_ref' ";
	
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
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	ob_start();
	$width_td=4910; 
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
                	<th colspan="18">New Development Status (Inquiry)</th>
                    <th colspan="5">Sample Requisition with Booking</th>
                    <th colspan="5">Consumption Entry [CAD] For LA Costing</th>
                    <th colspan="9">Buyer Costing</th>
                    <th colspan="8">SMEE Receive</th>
                    <th colspan="6">Booking Costing</th>
                    <th colspan="4">Work Order</th> 
                    <th colspan="4">Pro Forma Invoice V2</th>
                   
                    
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
                        <th width="80">Body/Wash Color</th>
                       	<th width="80">Season</th>
                       	<th width="80">Brand</th>
                       	<th width="80"> Cons. Rec.Tgt.Date</th>
                        <th width="80">Act. Quot. Date</th>
                        <th width="80">File</th> 
                       	<th width="80">Mail Send Date</th>
                       	<th width="80" title="17">Mail Send Time</th>
                        
                        <th width="80">Requisition Id</th>
                       	<th width="80">Insert By</th>    
                      	<th width="80">Requisition Date</th>
                       <th width="80" title="">Work Order No</th>  
                       <th width="80">Booking Date</th>  
                       
                        
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
                        <th width="80">Dealing Merchant</th>
                        <th width="80">Inset Date</th>
                        <th width="80">No Of Merch Style</th>  
                        <th width="80">No Of Confirm PO</th>
                        <th width="80">Projected Order Qty.</th>
                        <th width="80">Confirm Order Qty.</th>
                        
                        <th width="80">Job No</th>
                        <th width="80">Costing Date</th>
                        <th width="80">Insert By</th>
                       <th width="80">Avg.FOB($) / Pcs</th>
                        <th width="80">Approve Status</th>
                        <th width="80">Remarks</th>
                        
                         
                         <th width="80">Fab. Booking No</th>  
                         <th width="80">Main Fab.<br>Booking  Date</th>
                         <th width="80">Acc. Work Order</th>  
                        <th width="80">Wash Work Order</th>
                        
                         <th width="80">Fab. PI No</th>  
                         <th width="80">Accessories PI No</th>  
                        <th width="80">Wash PI No.</th>
                        <th width="">LC/SC Number<br>(Internal File No)</th>
                    </tr>
            </thead>
        </table>
        <div style="width:<? echo $width_td+20;?>px; max-height:250px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <?
		 $team_leader_chk="";
		if($cbo_team_leader>0)
		{
			 $sql_team=sql_select("select a.project_type,b.id as dealing_id,b.team_member_name from  lib_marketing_team a,lib_mkt_team_member_info b where a.id=b.team_id and a.project_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dealing_cond3");
			 //echo "select a.project_type,b.id as dealing_id,b.team_member_name from  lib_marketing_team a,lib_mkt_team_member_info b where a.id=b.team_id and a.project_type=2 and a.status_active=1 and a.is_deleted=0 $dealing_cond3";
			 foreach($sql_team as $row)
			 {
				 $dealing_marcnd_arr[$row[csf('dealing_id')]]=$row[csf('dealing_id')];
			 }
			 
			 $team_leader_chk="and c.dealing_marchant in(".implode(",", $dealing_marcnd_arr).")";
			// $dealing_cond2="";
		}
		//echo $dealing_cond2.'='.$team_leader_chk;
		$total_po_qty_pcs=$total_sewing_reject_pcs=$total_fabric_defect_reject_pcs=$total_sample_pcs=$total_ok_gmt_after_ship=$total_tot_defect_reject=$total_missing=$total_poly_reject_pcs=0;
		
		 $sql_inq="select c.id as inquery_id,c.inquery_date,c.mail_send_date,c.buyer_id,c.color_id,c.color,c.insert_by,c.company_id as company_name,c.system_number_prefix_num,c.dealing_marchant,c.concern_marchant,c.style_description,c.style_refernce,c.season_buyer_wise, c.season_year,c.brand_id,c.actual_req_quot_date,c.actual_sam_send_date,c.actual_sam_send_date,c.con_rec_target_date,c.system_number from  wo_quotation_inquery c where  c.status_active=1 and c.is_deleted=0   $comp_cond2   $dealing_cond2  $buyer_id_cond2 $season_year_cond2  $brand_name_cond2 $date_cond_target $date_cond $season_name_cond2  $int_ref_cond2    $team_leader_chk order by c.id "; // ".where_con_using_array($inquery_id_arr,0,'c.id')."
		  
		//echo $sql_inq;//die;
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
			$master_stlye_arr[$row[csf('inquery_id')]]['color_id']=$row[csf('color_id')];
			$master_stlye_arr[$row[csf('inquery_id')]]['color']=$row[csf('color')];
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
			if($season_year)
			{
			$master_stlye_arr[$row[csf('inquery_id')]]['season_buyer_wise']=$season_arr[$row[csf('season_buyer_wise')]].'-'.$row[csf('season_year')];
			}
			else 
			{
				$master_stlye_arr[$row[csf('inquery_id')]]['season_buyer_wise']=$season_arr[$row[csf('season_buyer_wise')]];
			}
			$master_stlye_arr[$row[csf('inquery_id')]]['brand_id']=$brand_name_arr[$row[csf('brand_id')]];
			$inquery_id_arr[$row[csf('inquery_id')]]=$row[csf('inquery_id')];
			
			
		}
		$inquery_id_Cond="";
		//if(count($inquery_id_arr)>0)
		//{
			$inquery_id_Cond=".where_con_using_array($inquery_id_arr,0,'c.id').";
		//}
			
      $sql_po="select c.id as inquery_id,a.id as job_id,a.job_no_prefix_num,a.job_quantity,a.inserted_by as po_insert_by,a.insert_date as po_insert_date,a.dealing_marchant as po_dealing_marchant,a.team_leader, a.job_no,a.company_name,a.buyer_name, a.style_ref_no, a.avg_unit_price, a.total_set_qnty as ratio,b.po_quantity, b.id as po_id, b.po_number,b.grouping, b.unit_price, b.shiping_status,b.is_confirmed, b.pub_shipment_date,b.grouping as ref_no,c.inquery_date,c.insert_by,c.system_number_prefix_num,c.dealing_marchant,c.concern_marchant,c.style_description,c.style_refernce,c.season_buyer_wise, c.season_year,c.brand_id,c.actual_req_quot_date,c.actual_sam_send_date,c.actual_sam_send_date,c.con_rec_target_date,c.system_number from wo_po_details_master a, wo_po_break_down b, wo_quotation_inquery c where a.job_no=b.job_no_mst and b.grouping=c.style_refernce and c.id=a.inquiry_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $comp_cond $style_owner_cond  $team_leader_cond $dealing_cond $buyer_id_cond $season_year_cond $job_no_cond   $job_id_cond $order_id_cond_trans $order_no_cond $brand_name_cond $date_cond_target $date_cond $season_name_cond  $int_ref_cond  $style_ref_cond  ".where_con_using_array($inquery_id_arr,0,'c.id')." order by c.id ";
		 
		//echo $sql_po;//die;
		$result_po=sql_select($sql_po); 
	//	$tot_rows=count($result_po);
		foreach($result_po as $row)
		{
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_qty_pcs']+=$row[csf('po_quantity')]*$row[csf('ratio')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_dealing_marchant']=$dealing_library[$row[csf('po_dealing_marchant')]];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_id'].=$row[csf('po_id')].',';
			$master_stlye_arr[$row[csf('inquery_id')]]['grouping'].=$row[csf('grouping')].',';
			$master_stlye_arr[$row[csf('inquery_id')]]['plan_cut']+=$row[csf('plan_cut')];
			$master_stlye_arr[$row[csf('inquery_id')]]['shiping_status']=$row[csf('shiping_status')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_insert_by']=$row[csf('po_insert_by')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_insert_date']=$row[csf('po_insert_date')];
			$master_stlye_arr[$row[csf('inquery_id')]]['job_no']=$row[csf('job_no')];
			
			if($row[csf('is_confirmed')]==1)// Confirm
			{
				$master_stlye_arr[$row[csf('inquery_id')]]['conf_quantity']+=$row[csf('po_quantity')];
			}
			else
			{
				$master_stlye_arr[$row[csf('inquery_id')]]['proj_quantity']+=$row[csf('po_quantity')];
			}
			$master_stlye_arr[$row[csf('inquery_id')]]['team_leader']=$leader_library[$row[csf('team_leader')]];
			
			$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no_prefix_num')];
			$style_ref_arr[$row[csf('job_no')]]=$row[csf('style_ref_no')];
			$master_ref_arr[$row[csf('job_no')]]=$row[csf('ref_no')];
			$master_jobNo_arr[$row[csf('po_id')]]=$row[csf('job_no')];
			$merch_style_arr[$row[csf('inquery_id')]].=$row[csf('style_ref_no')].',';
			
			//$master_Qty_stlye_arr[$row[csf('inquery_id')]][$row[csf('style_ref_no')]]['job_quantity']=$row[csf('job_quantity')]*$row[csf('ratio')];
			
		}
	 	unset($result_po);
	 	$sql_preCosting=sql_select("select a.job_no,a.costing_date,a.inserted_by,a.approved,a.remarks,b.price_pcs_or_set from wo_pre_cost_mst a,wo_pre_cost_dtls b  where a.job_id=b.job_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($job_id_arr,0,'a.job_id')."  order by a.job_no asc");
		//echo "select a.job_no,a.costing_date,a.insert_date,a.approved,a.remarks from wo_pre_cost_mst a,wo_pre_cost_dtls b  where a.job_id=b.job_id and  a.status_active=1 and a.is_deleted=0  ".where_con_using_array($job_id_arr,0,'a.job_id')."  order by a.job_no asc";
		foreach($sql_preCosting as $row)
		{
			$booking_costing_arr[$row[csf('job_no')]]['pre_job_no']=$row[csf('job_no')];
			$booking_costing_arr[$row[csf('job_no')]]['costing_date']=$row[csf('costing_date')];
			$booking_costing_arr[$row[csf('job_no')]]['inserted_by']=$row[csf('inserted_by')];
			$booking_costing_arr[$row[csf('job_no')]]['approved']=$row[csf('approved')];
			$booking_costing_arr[$row[csf('job_no')]]['remarks']=$row[csf('remarks')];
			$booking_costing_arr[$row[csf('job_no')]]['margin_pcs_set']=$row[csf('price_pcs_or_set')];
		}
		unset($sql_preCosting);
		
		$booking_sql=sql_select("SELECT c.id as wo_id,c.is_short,c.booking_type,c.booking_date,c.booking_no_prefix_num as booking_prefix,c.booking_no,a.job_no,a.po_break_down_id as po_id,a.booking_no ,a.fin_fab_qnty,b.grouping as ref_no from wo_booking_mst c, wo_booking_dtls a,wo_po_break_down b  where b.id=a.po_break_down_id and c.booking_no=a.booking_no and  a.booking_type  in(1,4,2,6) and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($job_id_arr,0,'b.job_id')." ");
		//echo "SELECT a.po_break_down_id as po_id,a.booking_no ,a.fin_fab_qnty,b.grouping as ref_no from wo_booking_dtls a,wo_po_break_down b  where b.id=a.po_break_down_id and  a.booking_type=1 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($job_id_arr,0,'b.job_id')." ";
		
		
		foreach($booking_sql as $row)
		{
			//$booking_fin_qnty_arr[$row[csf("job_no")]]["qnty"]+=$row[csf("fin_fab_qnty")];
			$is_short=$row[csf("is_short")];
			$booking_type=$row[csf("booking_type")];
			$wo_id_arr[$row[csf("wo_id")]]=$row[csf("wo_id")];
			
			if($is_short==1 && $booking_type==1)
			{
				$booking_data_arr[$row[csf("job_no")]]["short_booking"]=$row[csf("booking_prefix")];
				$booking_data_arr[$row[csf("job_no")]]["short_booking_no"]=$row[csf("booking_no")];
				$booking_data_arr[$row[csf("job_no")]]["short_s"]='S';
			}
			else if($is_short==2 && $booking_type==1)
			{
				$booking_data_arr[$row[csf("job_no")]]["main_booking"]=$row[csf("booking_prefix")];
				$booking_data_arr[$row[csf("job_no")]]["main_booking_no"]=$row[csf("booking_no")];
				$booking_data_arr[$row[csf("job_no")]]["main_booking_date"]=$row[csf("booking_date")];
				$booking_data_arr[$row[csf("job_no")]]["main_m"]='M';
			}
			else if($booking_type==4)//Sample
			{
				$booking_data_arr[$row[csf("job_no")]]["sample_booking"]=$row[csf("booking_prefix")];
				$booking_data_arr[$row[csf("job_no")]]["sample_booking_no"]=$row[csf("booking_no")];
				$booking_data_arr[$row[csf("job_no")]]["sample_m"]='SM';
			}
			
			if($booking_type==2)//Trim Booking
			{
				$booking_data_arr[$row[csf("job_no")]]["trim_booking"]=$row[csf("booking_prefix")];
				$booking_data_arr[$row[csf("job_no")]]["trim_booking_no"]=$row[csf("booking_no")];
				//$booking_data_arr[$row[csf("job_no")]]["sample_m"]='SM';
			}
			if($booking_type==6)//Emblishment Booking
			{
				$booking_data_arr[$row[csf("job_no")]]["embl_booking"]=$row[csf("booking_prefix")];
				$booking_data_arr[$row[csf("job_no")]]["embl_booking_no"]=$row[csf("booking_no")];
				//$booking_data_arr[$row[csf("job_no")]]["sample_m"]='SM';
			}
			
		}
		unset($booking_sql);
		$sql_pi = "select a.id as pi_mst_id,a.item_category_id as cat_id,a.pi_number,b.work_order_no,b.work_order_dtls_id,(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and a.pi_basis_id=1  and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($wo_id_arr,0,'b.work_order_id')."";//wo_id_arr
		
		$PiSQLresult = sql_select($sql_pi);
		$invoiceArr = array();
		foreach ($PiSQLresult as $key => $val) {
			$PiArr[$val[csf('work_order_no')]][$val[csf('cat_id')]]['pi_number'] = $val[csf('pi_number')];
			$PiArr[$val[csf('work_order_no')]][$val[csf('cat_id')]]['pi_mst_id'] = $val[csf('pi_mst_id')];
		}
		 $sql_sc = "select a.contract_no,a.internal_file_no,b.com_sales_contract_id,b.wo_po_break_down_id as po_id  from com_sales_contract_order_info b, com_sales_contract a where a.id = b.com_sales_contract_id   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'b.wo_po_break_down_id')."";//wo_id_arr
		
		$SCSQLresult = sql_select($sql_sc);
		$invoiceArr = array();
		foreach ($SCSQLresult as $key => $val) {
			$jobNo=$master_jobNo_arr[$val[csf('po_id')]];
			$SCArr[$jobNo]['int_ref'] = $val[csf('internal_file_no')];
			$SCNoArr[$jobNo]['contract_no'] = $val[csf('contract_no')];
		}
		
		//
		//print_r($PiArr);
		  unset($SCSQLresult); 
		
		$sql_consCosting=sql_select("select a.id,a.inquiry_id,a.system_no_prefix_num,a.system_no as system_no,a.inserted_by,a.la_costing_date,a.pattern_master,a.bom_no from consumption_la_costing_mst a  where  a.status_active=1 and a.is_deleted=0  ".where_con_using_array($inquery_id_arr,0,'a.inquiry_id')."  order by a.inquiry_id asc");
		
	//	echo "select a.inquiry_id,a.system_no as system_no,a.inserted_by,a.la_costing_date,a.pattern_master,a.bom_no from consumption_la_costing_mst a  where  a.status_active=1 and a.is_deleted=0  ".where_con_using_array($inquery_id_arr,0,'a.inquiry_id')."  order by a.inquiry_id asc";
		//echo "select a.inquiry_id,a.system_no as system_no,a.inserted_by,a.la_costing_date,a.pattern_master,a.bom_no from consumption_la_costing_mst a, wo_quotation_inquery_fab_dtls d where a.id=d.mst_id and d.status_active=1 and d.is_deleted=0  ".where_con_using_array($inquery_id_arr,0,'a.inquiry_id')."  order by a.inquiry_id asc";
		foreach($sql_consCosting as $row)
		{
		//	$master_ref=$master_ref_arr[$row[csf('job_no')]];
			 
			$cons_costing_arr[$row[csf('inquiry_id')]]['update_id']=$row[csf('id')];
			$cons_costing_arr[$row[csf('inquiry_id')]]['system_no']=$row[csf('system_no')];
			$cons_costing_arr[$row[csf('inquiry_id')]]['pre_fix']=$row[csf('system_no_prefix_num')];
			$cons_costing_arr[$row[csf('inquiry_id')]]['cons_inserted_by']=$row[csf('inserted_by')];
			$cons_costing_arr[$row[csf('inquiry_id')]]['la_costing_date']=$row[csf('la_costing_date')];
			$cons_costing_arr[$row[csf('inquiry_id')]]['bom_no']=$row[csf('bom_no')];
			$cons_costing_arr[$row[csf('inquiry_id')]]['pattern_master']=$row[csf('pattern_master')];
			 
		}
		$sql_buyerCosting=sql_select("select a.qc_no,a.cost_sheet_no,a.inquery_id, a.approved,a.revise_no as revise_no,option_id,a.cost_sheet_no as cost_sheet_no,a.inserted_by,a.costing_date,a.delivery_date as due_date,a.buyer_remarks,b.tot_fob_cost from qc_mst a,qc_tot_cost_summary b where a.qc_no=b.mst_id and a.status_active=1 and a.is_deleted=0  ".where_con_using_array($inquery_id_arr,0,'a.inquery_id')."  order by a.inquery_id asc");
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
			$buyer_costing_arr[$row[csf('inquery_id')]]['qc_no']=$row[csf('qc_no')];
			$buyer_costing_arr[$row[csf('inquery_id')]]['buyer_remarks']=$row[csf('buyer_remarks')];
			 
		}
		 $sql_sample_req="select d.id as req_id,a.booking_date,a.booking_no,c.id as inquery_id,c.system_number,d.style_ref_no,d.requisition_date,d.requisition_number_prefix_num as prefix_no,d.inserted_by from  wo_quotation_inquery c,sample_development_mst d,wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a where  c.style_refernce=d.style_ref_no and d.id=b.style_id and  b.booking_no=a.booking_no and  c.status_active=1 and c.is_deleted=0   $comp_cond2    $buyer_id_cond2 $season_year_cond2  $brand_name_cond2 $date_cond_target $date_cond $season_name_cond2  $int_ref_cond2  $style_ref_cond  order by c.id ";
		$sql_sample_req_result=sql_select($sql_sample_req);
		foreach($sql_sample_req_result as $row)
		{
			$sample_req_arr[$row[csf('inquery_id')]]['requisition_date']=$row[csf('requisition_date')];
			$sample_req_arr[$row[csf('inquery_id')]]['prefix_no']=$row[csf('prefix_no')];
			$sample_req_arr[$row[csf('inquery_id')]]['inserted_by']=$row[csf('inserted_by')];
			$sample_req_arr[$row[csf('inquery_id')]]['booking_date']=$row[csf('booking_date')];
			$sample_req_arr[$row[csf('inquery_id')]]['booking_no']=$row[csf('booking_no')];
			$sample_req_arr[$row[csf('inquery_id')]]['req_id']=$row[csf('req_id')];
		}
		
		$total_conf_qty_pcs=$total_proj_po_qty_pcs=0;
		
		foreach($master_stlye_arr as $inquery_id=>$row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			if($company_name) $company_name=$company_name;else $company_name=0;
			//if($style_owner) $style_owner=$style_owner;else $style_owner=0;
			 
			if($row[('grouping')]!="")
			{
			$merch_style=rtrim($merch_style_arr[$inquery_id],',');
			$no_of_int_ref_noArr=array_unique(explode(",",$merch_style));
			
			//$int_ref_no=rtrim($row[('grouping')],',');
			$po_ids=rtrim($row[('po_id')],',');
			//$no_of_int_ref_noArr=array_unique(explode(",",$int_ref_no));
			$no_of_po_idsArr=array_unique(explode(",",$po_ids));
			$po_idsAll=implode(",",$no_of_po_idsArr);
			}
			//print_r($no_of_int_ref_noArr);
			$cons_update_id=$cons_costing_arr[$inquery_id]['update_id'];
			$cons_system_no=$cons_costing_arr[$inquery_id]['system_no'];
			$cons_pre_fix=$cons_costing_arr[$inquery_id]['pre_fix'];
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
			
			$qc_no=$buyer_costing_arr[$inquery_id]['qc_no'];
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
			$po_insert_date=$master_stlye_arr[$inquery_id]['po_insert_date'];
			//$po_insert_date=$master_stlye_arr[$inquery_id]['po_insert_date'];
			//qc_show_file
			
			$samp_requisition_date=$sample_req_arr[$inquery_id]['requisition_date'];
			$samp_prefix_no=$sample_req_arr[$inquery_id]['prefix_no'];
			$samp_inserted_by=$sample_req_arr[$inquery_id]['inserted_by'];
			$samp_booking_date=$sample_req_arr[$inquery_id]['booking_date'];
			$samp_booking_no=$sample_req_arr[$inquery_id]['booking_no'];
			$samp_req_id=$sample_req_arr[$inquery_id]['req_id'];
			
			$costing_date=$booking_costing_arr[$row[('job_no')]]['costing_date'];
			$inserted_by=$booking_costing_arr[$row[('job_no')]]['inserted_by'];
			$approved=$booking_costing_arr[$row[('job_no')]]['approved'];
			$remarks=$booking_costing_arr[$row[('job_no')]]['remarks'];
			$fob_avg_pcs_set=$booking_costing_arr[$row[('job_no')]]['margin_pcs_set'];
			if($approved==1)
			{
				$approve_msg_txt="Approved";
			}
			else if($approved==3)
			{
				$approve_msg_txt="Partial Approved";
			}
			else $approve_msg_txt="Un-Approved";
			
			$short_booking_prefix=$booking_data_arr[$row[("job_no")]]["short_booking"];
			$short_booking_no=$booking_data_arr[$row[("job_no")]]["booking_no"];
			$short_booking_msg=$booking_data_arr[$row[("job_no")]]["short_s"];
			
			$main_booking_prefix=$booking_data_arr[$row[("job_no")]]["main_booking"];
			$main_booking_no=$booking_data_arr[$row[("job_no")]]["main_booking_no"];
			$main_booking_date=$booking_data_arr[$row[("job_no")]]["main_booking_date"];
			$main_booking_msg=$booking_data_arr[$row[("job_no")]]["main_m"];
			
			$sample_booking_prefix=$booking_data_arr[$row[("job_no")]]["sample_booking"];
			$sample_booking_no=$booking_data_arr[$row[("job_no")]]["sample_booking_no"];
			$sample_booking_msg=$booking_data_arr[$row[("job_no")]]["sample_m"];
			$short_main_sample="";$print_booking_main="";
			if($main_booking_prefix)
			{
				$short_main_sample="M-".$main_booking_prefix;
				$print_booking_main='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$inquery_id."',".$row[('buyer_name')].",'".$main_booking_no."','fabric_booking_report',2".')">'.$short_main_sample.'</a>';
				//echo $main_booking_no.'ss';
			}
			if($short_booking_prefix)
			{
				//$short_main_sample.=","."S-".$short_booking_prefix;
				$short_main_short="S-".$short_booking_prefix;
				$print_booking_main.=",".'<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$inquery_id."',".$row[('buyer_name')].",'".$short_booking_no."','fabric_booking_report',3".')">'.$short_main_short.'</a>';
			}
			if($sample_booking_prefix)
			{
				//$short_main_sample.=","."SM-".$sample_booking_prefix;
				$short_main_sample="SM-".$sample_booking_prefix;
				$print_booking_main.=",".'<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$inquery_id."',".$row[('buyer_name')].",'".$sample_booking_no."','fabric_booking_report',4".')">'.$short_main_sample.'</a>';
			}
			
			
				$trim_booking_prefix=$booking_data_arr[$row[("job_no")]]["trim_booking"];
				$trim_booking_no=$booking_data_arr[$row[("job_no")]]["trim_booking_no"];
				//$booking_data_arr[$row[csf("job_no")]]["sample_m"]='SM';
			
				$embl_booking_prefix=$booking_data_arr[$row[("job_no")]]["embl_booking"];
				$embl_booking_no=$booking_data_arr[$row[("job_no")]]["embl_booking_no"];
				//$booking_data_arr[$row[csf("job_no")]]["sample_m"]='SM';
				$acc_pi_no=$PiArr[$trim_booking_no][4]['pi_number'];
				$fab_pi_no=$PiArr[$main_booking_no][3]['pi_number'];
				
				$embl_pi_no=$PiArr[$embl_booking_no][25]['pi_number'];
				$pi_mst_id=$PiArr[$embl_booking_no][25]['pi_mst_id'];
				$sc_int_ref=$SCArr[$row[("job_no")]]['int_ref'];
				$contract_no=$SCNoArr[$row[("job_no")]]['contract_no'];
				//$acc_pi_no=$PiArr[$trim_booking_no][4]['pi_number'];
				//echo $embl_pi_no.'SAS';
			$pre_job_no=$booking_costing_arr[$row[('job_no')]]['pre_job_no'];
			if($pre_job_no)
			{
			$print_button_job='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$inquery_id."',".$row[('buyer_name')].",'".$po_id_all."','basic_cost',1".')">'.$row[("job_no")].'</a>';
			}
			else $print_button_job="";
			
			if($trim_booking_no)
			{
			$print_booking_trim='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$po_idsAll."',".$row[('buyer_name')].",'".$trim_booking_no."','fabric_booking_report',5".')">View</a>';
			} else $print_booking_trim="";
			if($embl_booking_prefix)
			{
			$print_booking_wash='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$po_idsAll."',".$row[('buyer_name')].",'".$embl_booking_no."','wash_booked_style_popup',6".')">View</a>';
			} else $print_booking_wash="";
			//
			
			if($fab_pi_no) 
			{
			$print_fab_pi_no='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$fab_pi_no."',".$row[('buyer_name')].",'".$main_booking_prefix."','report_generate_woven',7".')">'.$fab_pi_no.'</a>';
			} else $print_fab_pi_no=""; 
			if($acc_pi_no) 
			{
			$print_trim_pi_no='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$fab_pi_no."',".$row[('buyer_name')].",'".$trim_booking_prefix."','report_generate_trims',8".')">View</a>';
			} else $print_trim_pi_no="";
			if($embl_pi_no) 
			{
			$print_wash_pi_no='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$fab_pi_no."',".$row[('buyer_name')].",'".$pi_mst_id."','print',9".')"></a>'.$embl_pi_no;
			} else $print_wash_pi_no=""; 
			if($sc_int_ref) 
			{
			$print_sc_int_ref='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$fab_pi_no."',".$row[('buyer_name')].",'".$contract_no."','report_generate',10".')">'.$sc_int_ref.'</a>';
			} else $print_sc_int_ref=""; 
		//else $print_button_job.=",".'<a href="##" onclick="report_generate_pop('.$company_name.",".$style_owner.",'".$job_prefix."','".$style_ref_nos."',".$buyer_name.",'".$po_id_all."','generate_style_report_with_graph',1".')">'.$job_prefix.'</a>';
		
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
				<td width="80" ><p style="word-break:break-all"><? echo $row[('style_description')]; ?></p></td>
                <td width="80" ><p style="word-break:break-all"><? echo $row[('style_refernce')]; ?></p></td>
                <td width="80" title="Color=<? echo  $color_library[$row[('color_id')]].',Id='.$row[('color_id')];?>" ><p><? echo $row[('color')]; ?></p></td>
				
				<td width="80" align="center"><p><? echo $row[('season_buyer_wise')]; ?></p></td>
				<td width="80" align="center"><p><? echo $row[('brand_id')]; ?></a></p></td>
				
                <td width="80" align="center"><p><? echo change_date_format($row[('con_rec_target_date')]); ?></p></td>
                  <td width="80" align="center"><p><? echo change_date_format($row[('actual_req_quot_date')]); ?></p></td>
				<td width="80" align="center" title=""><p>
                <a href="##" onClick="openmypage_file('<? echo $row[('company_name')]; ?>','<? echo $inquery_id; ?>','show_file',1)"><? echo "View";   ?></a></p></td>
				
                <td width="80" align="center"><p><? $mail_date=explode(" ",$row[('mail_send_date')]);echo change_date_format($mail_date[0]); ?></p></td>
				<td width="80" align="center" title="Woven Recv-Issue"><p><?  echo  $mail_date[1];
				?></p></td>
                
                <td width="80" align="center" ><p><a href="##" onClick="report_generate_samp_req_pop('<? echo $row[('company_name')]; ?>','<? echo $samp_req_id; ?>','<? echo $samp_booking_no; ?>')"><? echo $samp_prefix_no; ?></a><?  ?></p></td>
				<td width="80" align="right" title="Woven Recv-Fabric used"><p><? echo $user_arr[$samp_inserted_by]; ?></p></td>
				<td width="80" align="center" title=""><p><?  echo change_date_format($samp_requisition_date); ?></p></td>
				<td width="80" align="center" title=""><p><?  echo $samp_booking_no;
				 ?></p></td>
				<td width="80" align="center"><p><? echo $samp_booking_date; ?>  </p></td>
                
				<td width="80" align="center" ><p><a href="##" onClick="report_la_costing_popup('<? echo $company_name; ?>','<? echo $cons_update_id; ?>','consumption_report',3)"><? echo $cons_pre_fix; ?></a></p></td>
				<td width="80" align="center" title=""><p><?
				echo $user_arr[$cons_inserted_by]; ?></p></td>
				<td width="80" align="center" title=""><p><?  echo change_date_format($la_costing_date); ?></p></td>
				<td width="80" align="center" title=""><p><? 
				 echo $pattern_master;
				 ?></p></td>
				<td width="80" align="center"><p><? echo $bom_no; ?>  </p></td>
                 
				<td width="80" align="center"><p><a href="##" onClick="fnc_quick_costing_print('<? echo $cost_sheet_no; ?>','<? echo $qc_no; ?>','quick_costing_print',3)"><? echo $cost_sheet_no; ?></a><? //echo $cost_sheet_no; ?></p></p></td>
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
                
				<td width="80" align="center"><p><a href="##" onClick="report_po_popup('<? echo $company_name; ?>','<? echo $row[('style_refernce')]; ?>','<? echo $inquery_id; ?>','show_po_merch_report',4)"><? echo count($no_of_int_ref_noArr); ?></a><? //echo count($no_of_int_ref_noArr); ?></p></td>
				<td width="80" align="center"><p><? echo count($no_of_po_idsArr);; ?></p></td>
				 
				 
				<td align="right" width="80" title=""><p><a href="##" onClick="report_po_popup('<? echo $company_name; ?>','<? echo $row[('style_refernce')]; ?>','<? echo $inquery_id; ?>','show_po_listview_report',3)"><? echo $row[('proj_quantity')]; ?></a></p></td>
                
                <td width="80" align="right" title=""><p><a href="##" onClick="report_po_popup('<? echo $company_name; ?>','<? echo $row[('style_refernce')]; ?>','<? echo $inquery_id; ?>','show_po_listview_report',3)"><? echo $row[('conf_quantity')]; ?></a></p></td>
                
                <td width="80" align="center" title=""><p><? echo $print_button_job;?></p></td>
                <td width="80" align="center" title=""><p> <? echo $costing_date;?></p></td>
                <td width="80" align="center" title=""><p> <? echo $user_arr[$inserted_by];?></p></td>
                <td width="80" align="center" title=""><p> <? echo $fob_avg_pcs_set;?></p></td>
                <td width="80" align="center" title=""><p> <? echo $approve_msg_txt;?></p></td>
                <td width="80" align="center" title="<? echo $remarks;?>"><p> <a href="##" onClick="generate_popup('<? echo $row[("job_no")]; ?>','remark_popup',1)">Veiw</a><? //echo $remarks;?></p></td>
                
                <td width="80" align="center" title=""><p> <? echo $print_booking_main;?></p></td>
                <td width="80" align="center" title=""><p> <? echo $main_booking_date;?></p></td>
                <td width="80" align="center" title=""><p> <? echo $print_booking_trim;?></p></td> 
                <td width="80" align="center" title=""><p> <? echo $print_booking_wash;?></p></td> 
                 
                 <td width="80" align="center" title=""><p> <? echo $print_fab_pi_no;?></p></td>
                <td width="80" align="center" title=""><p> <? echo $print_trim_pi_no;?></p></td>
                <td width="80" align="center" title=""><p> <? echo $print_wash_pi_no;?></p></td>
                <td width="" align="center" title=""><p> <? echo $print_sc_int_ref;?></p></td>
                
			</tr>
			
			<?
				
				$total_conf_qty_pcs+=$row[('conf_quantity')];
				$total_proj_po_qty_pcs+=$row[('proj_quantity')];
			
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
                
                <td  width="80" align="right" id="po_proj_qty_td"><? echo number_format($total_proj_po_qty_pcs,0);?></td>
                <td width="80" align="right" id="po_conf_qty_td"><? echo number_format($total_conf_qty_pcs,0);?></td>
               
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                
                 <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td> 
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                <td width="80"><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                
                <td width=""><? //echo number_format($total_fabric_defect_reject_pcs,2);?></td>
                
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

if($action=="remark_popup") //
{
	echo load_html_head_contents("Po Order Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$size_name_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name");
	$dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	//"select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$data' and status_active=1 and is_deleted=0 order by ultimate_country_code"
	//$code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping",'id','ultimate_country_code');
	
	/*if($type_id==1)
	{
		$td_width=710;
		$row_span=5;	
	}
	else if($type_id==2)
	{
		$td_width=710+80;
		$row_span=5;	
	}*/
	$td_width=350;
	?>
	<script>
		function print_window()
		{
			//$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			//$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width;?>px; margin-left:3px;">
        <div style="width:<? echo $td_width;?>px;" align="center">
        	<input  type="hidden" value="Print Preview" onClick="print_window()" style="width:70px"  class="formbutton"/> &nbsp;
            <div id="report_container_popup"> </div>
        </div>
         <?
         ob_start();
		?>
        <?
         // echo $inquire_id.'ddd';;
				//master_style_ref inquire_id
				 $po_sql="select a.job_no,c.remarks,a.style_ref_no as master_style from wo_po_details_master a,wo_pre_cost_mst c where  a.id=c.job_id and c.job_no in('$job') and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";//and a.inquiry_id=$inquire_id
				$po_sql_result=sql_select($po_sql);
				$tot_conf=$tot_proj=0;
				foreach($po_sql_result as $row)
				{
					
					//$product_dept=$product_dept[$row[csf('product_dept')]];
					$remarksArr[$row[csf('remarks')]]=$row[csf('remarks')];
					//$style_ref_no_arr[$row[csf('master_style')]]['job_no'].=$row[csf('job_no')].',';
				}
				
				//print_r($proj_size_arr);
			$td_width=$td_width+(count($proj_size_ar)*60);	
		?>
        <div id="report_div" align="center" style=" margin-left:10px;">
        
           <table border="1" class="rpt_table" rules="all" width="<? echo $td_width+100;?>" cellpadding="0" cellspacing="0" align="left" >
                <caption> 
                	<b><strong></strong></b>
                </caption>
                 <thead>
                <tr>
                   
                    <th  width="220"> Remarks</th>
                    <th width="110"> Job No</th>
                </tr>
                </thead>
                <?
				$k=1;
                foreach($remarksArr as $remarks=>$val)
				{
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($row['job_no'],',');	
						$job_nos=implode(",",array_unique(explode(",",$job_no)));
				?>
               <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
                    
                    <td width="220"><strong> &nbsp;<? echo $remarks; ?></strong></td>
                    <td width="110"><strong> &nbsp; <? echo $job; ?></strong></td>
                  
                </tr>
                <?
				$k++;
				}
				?>
            </table>
            
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="show_po_merch_report") //
{
	echo load_html_head_contents("Po Order Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$size_name_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name");
	$dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	//"select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$data' and status_active=1 and is_deleted=0 order by ultimate_country_code"
	$code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping",'id','ultimate_country_code');
	
	/*if($type_id==1)
	{
		$td_width=710;
		$row_span=5;	
	}
	else if($type_id==2)
	{
		$td_width=710+80;
		$row_span=5;	
	}*/
	$td_width=430;
	?>
	<script>
		function print_window()
		{
			//$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			//$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width;?>px; margin-left:3px;">
        <div style="width:<? echo $td_width;?>px;" align="center">
        	<input  type="hidden" value="Print Preview" onClick="print_window()" style="width:70px"  class="formbutton"/> &nbsp;
            <div id="report_container_popup"> </div>
        </div>
         <?
         ob_start();
		?>
        <?
         // echo $inquire_id.'ddd';;
				//master_style_ref inquire_id
				 $po_sql="select a.job_no,b.po_number,a.style_ref_no as master_style from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c where  a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id and b.grouping in('$master_style_ref') and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.inquiry_id=$inquire_id";//and a.inquiry_id=$inquire_id
				$po_sql_result=sql_select($po_sql);
				$tot_conf=$tot_proj=0;
				foreach($po_sql_result as $row)
				{
					
					$product_dept=$product_dept[$row[csf('product_dept')]];
					$style_ref_no_arr[$row[csf('master_style')]]['merch']=$row[csf('master_style')];
					$style_ref_no_arr[$row[csf('master_style')]]['job_no'].=$row[csf('job_no')].',';
				}
				
				//print_r($proj_size_arr);
			$td_width=$td_width+(count($proj_size_ar)*60);	
		?>
        <div id="report_div" align="center" style=" margin-left:10px;">
        
           <table border="1" class="rpt_table" rules="all" width="<? echo $td_width+100;?>" cellpadding="0" cellspacing="0" align="left" >
                <caption> 
                	<b><strong> No of Merch Style </strong></b>
                </caption>
                 <thead>
                <tr>
                    <th  width="30"> SL</th>
                    <th  width="150"> Merch Style</th>
                    <th width="150"> Job No</th>
                </tr>
                </thead>
                <?
				$k=1;
                foreach($style_ref_no_arr as $style_no=>$row)
				{
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($row['job_no'],',');	
						$job_nos=implode(",",array_unique(explode(",",$job_no)));
				?>
               <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
                    <td width="30"><strong> &nbsp;<? echo $k; ?></strong></td>
                    <td width="150"><strong> &nbsp;<? echo $style_no; ?></strong></td>
                    <td width="100"><strong> &nbsp; <? echo $job_nos; ?></strong></td>
                  
                </tr>
                <?
				$k++;
				}
				?>
            </table>
            
		</div>
	</fieldset>
	<?
	exit();
} //Po wise button end
if($action=="show_po_listview_report") //show_po_merch_report
{
	echo load_html_head_contents("Po Order Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$size_name_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name");
	$dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	//"select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$data' and status_active=1 and is_deleted=0 order by ultimate_country_code"
	$code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping",'id','ultimate_country_code');
	
	/*if($type_id==1)
	{
		$td_width=710;
		$row_span=5;	
	}
	else if($type_id==2)
	{
		$td_width=710+80;
		$row_span=5;	
	}*/
	$td_width=1000;
	?>
	<script>
		function print_window()
		{
			//$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			//$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width;?>px; margin-left:3px;">
        <div style="width:<? echo $td_width;?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:70px"  class="formbutton"/> &nbsp;
            <div id="report_container_popup"> </div>
        </div>
         <?
         ob_start();
		?>
        <?
         // echo $inquire_id.'ddd';;
				//master_style_ref inquire_id
				$po_sql="select a.job_no,a.job_no_prefix_num,a.buyer_name,a.brand_id,a.product_dept,a.dealing_marchant,a.season_buyer_wise,b.id as po_id, b.po_number,a.style_ref_no as master_style,b.po_received_date,b.shipment_date as orgi_shipment_date,b.is_confirmed,b.pub_shipment_date,b.po_quantity as po_qty, b.unit_price as unit_price,b.up_charge as up_charge,c.country_id,c.size_number_id  as size_id,c.color_number_id as color_id,c.order_quantity,c.order_total,c.code_id from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c where  a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id and b.grouping in('$master_style_ref') and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.inquiry_id=$inquire_id";
				$po_sql_result=sql_select($po_sql);
				$tot_conf=$tot_proj=0;
				foreach($po_sql_result as $row)
				{
					$is_confirmed=$row[csf('is_confirmed')];
					$buyer_name=$buyer_library[$row[csf('buyer_name')]];
					$master_style=$row[csf('master_style')];
					$season_buyer_wise=$season_name_arr[$row[csf('season_buyer_wise')]];
					$brand_name=$brand_arr[$row[csf('brand_id')]];
					$dealing_marchant=$dealing_arr[$row[csf('dealing_marchant')]];
					$product_dept=$product_dept[$row[csf('product_dept')]];
					if($is_confirmed==1)
					{
					$conf_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['color_qty']+=$row[csf('order_quantity')];
					$conf_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
					$conf_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['c_code']=$code_arr[$row[csf('code_id')]];
					$conf_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['po_number']=$row[csf('po_number')];
					$conf_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['master_style']=$row[csf('master_style')];
					$conf_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['job_prefix']=$row[csf('job_no_prefix_num')];
					$conf_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					 $conf_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['size_qty']+=$row[csf('order_quantity')];
					 $conf_size_color_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['size_qty']+=$row[csf('order_quantity')];
					$tot_conf+=$row[csf('order_quantity')];
					}
					else
					{
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['color_qty']+=$row[csf('order_quantity')];
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['c_code']=$code_arr[$row[csf('code_id')]];
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['po_number']=$row[csf('po_number')];
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['master_style']=$row[csf('master_style')];
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['job_prefix']=$row[csf('job_no_prefix_num')];
					$proj_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					 $proj_size_color_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['size_qty']+=$row[csf('order_quantity')];
					$tot_proj+=$row[csf('order_quantity')];
					}
				}
				$tot_balance_diff=$tot_proj-$tot_conf;
				//print_r($proj_size_arr);
			$td_width=$td_width+(count($proj_size_ar)*60);	
		?>
        <div id="report_div" align="center" style=" margin-left:10px;">
        
           <table border="1" class="rpt_table" rules="all" width="<? echo $td_width+100;?>" cellpadding="0" cellspacing="0" align="left" >
                <tr> 
                	<td colspan="10" align="center"><strong> Total Order Qty. Break Down </strong></td>
                </tr>
                <tr bgcolor="#E9F3FF"> 
                  
                    <td width="150"><strong> Buyer:&nbsp;<? echo $buyer_name; ?></strong></td>
                    <td width="130"><strong>Master Style.:&nbsp; <? echo $master_style_ref; ?></strong></td>
                    <td width="150"><strong> Brand:&nbsp;<? echo $brand_name; ?></strong></td>
                    <td width="150"><strong> Season:&nbsp;<? echo $season_buyer_wise; ?></strong></td>
                    <td width="150"><strong> Balance (SMEE-GT):&nbsp;<? echo number_format($tot_balance_diff,0); ?></strong></td>
                    <td width="150"><strong> Prod. Dept.:&nbsp;<? echo $product_dept; ?></strong></td>
                    <td width="150"><strong> Merchant:&nbsp;<? echo $dealing_marchant; ?></strong></td>
                   
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" align="left" >
            <caption><b style="float:left"> Projected Order (SMEE)</b> </caption>
                <thead>
                    <th width="20">SL</th>
                    <th width="80">Merch Style</th>
                    <th width="50">Job No</th>
                    <th width="100">PO No</th>
                    <th width="100">Market</th>
                    <th width="80">Channel</th>
                    <th width="80">Ship / Cancel Date</th>
                    <th width="100">Color</th>
                    <th width="60">Size Total</th>
                    <?
                    foreach($proj_size_arr as $size_id )
					{
					?>
                    <th width="60"> <p><? echo $size_name_arr[$size_id];?> </p></th>
                    <?
					}
					?>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="left" id="table_body_popup">
                <?
				//master_style_ref
				//$po_sql="select  b.po_number,b.po_received_date,b.shipment_date as orgi_shipment_date,b.is_confirmed,b.pub_shipment_date,b.po_quantity as po_qty, b.unit_price as unit_price,b.up_charge as up_charge from wo_po_break_down b,wo_po_details_master a where  a.id=b.job_id and b.grouping in('$master_style_ref') and b.status_active=1 and b.is_deleted=0";
				 $i=1;$proj_tot_po_qty=0;
				foreach($proj_po_arr as $job_no=>$jobData)
				{
					$sub_proj_sizeQty_arr=array();
				foreach($jobData as $po_id=>$poData)
				{
				foreach($poData as $country_id=>$countryData)
				{
				 foreach($countryData as $color_id=>$row)
				 {
				 
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="20"><p><? echo $i; ?></p></td>
						<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[('master_style')]; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[('job_prefix')]; ?></div></td>
                        <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('po_number')]; ?></div></td>
                         
						<td width="100" align="center"><p><? echo $country_arr[$country_id]; ?></p></td>
                        <td width="80" align="center"><p><? echo $row[('c_code')]; ?></p></td>
						<td width="80" align="center"><p><? echo $row[('pub_shipment_date')]; ?></p></td>
						<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $color_name_arr[$color_id];//change_date_format ?></div></td>
                        
                        <td width="60" align="right"><p><? echo number_format($row[('color_qty')],0); ?></p></td>
                        
                        <?
						
						foreach($proj_size_arr as $size_id )
						{
							  $proj_sizeQty=$proj_size_color_po_arr[$job_no][$po_id][$country_id][$color_id][$size_id]['size_qty'];
							  $proj_sizeQty_arr[$size_id]+=$proj_sizeQty;
							  
						?>
						<td width="60" align="right"> <p><? echo number_format($proj_sizeQty,0);?> </p></td>
						<?
						$sub_proj_sizeQty_arr[$size_id]+=$proj_sizeQty;
						}
					?>
                       
					</tr>
					<?
					$proj_tot_po_qty+=$row[('color_qty')];
					$proj_sub_tot_po_qty+=$row[('color_qty')];
					//$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					//$tot_order_value+=$row[csf('po_qty')]*$row[csf('unit_price')];
					$i++;
					 }
					}
				   }
				   ?>
                    
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Merch Total :</td>
						<td align="right"><? echo number_format($proj_sub_tot_po_qty,0);$proj_sub_tot_po_qty=0; ?></td>
                        
                        <?
						$sub_proj_sizeQty=0;
						foreach($proj_size_arr as $size_id )
						{
							$sub_proj_sizeQty+=$sub_proj_sizeQty_arr[$size_id];
						?>
						<td width="60" align="right"> <p><? echo number_format($sub_proj_sizeQty,0);?> </p></td>
						<?
						}
					?>
					</tr>
				 
                   <?
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($proj_tot_po_qty,0); ?></td>
                        
                        <?
						foreach($proj_size_arr as $size_id )
						{
						?>
						<td width="60" align="right"> <p><? echo number_format($proj_sizeQty_arr[$size_id],0);?> </p></td>
						<?
						}
					?>
					</tr>
				</tfoot>
			</table>
            <br>
            <?
			$td_width2=1000;
            $td_width2=$td_width2+(count($conf_size_arr)*60);	
			?>
             <table border="1" class="rpt_table" rules="all" width="<? echo $td_width2?>" cellpadding="0" cellspacing="0" align="left" >
            <caption><b style="float:left"> Confirm Order (GT Nexus)</b> </caption>
                <thead>
                    <th width="20">SL</th>
                    <th width="80">Merch Style</th>
                    <th width="50">Job No</th>
                    <th width="100">PO No</th>
                    <th width="100">Market</th>
                    <th width="80">Channel</th>
                    <th width="80">Ship / Cancel Date</th>
                    <th width="100">Color</th>
                    <th width="60">Size Total</th>
                    <?
                    foreach($conf_size_arr as $size_id )
					{
					?>
                    <th width="60"> <p><? echo $size_name_arr[$size_id];?> </p></th>
                    <?
					}
					?>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width2;?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
				//master_style_ref
				//$po_sql="select  b.po_number,b.po_received_date,b.shipment_date as orgi_shipment_date,b.is_confirmed,b.pub_shipment_date,b.po_quantity as po_qty, b.unit_price as unit_price,b.up_charge as up_charge from wo_po_break_down b,wo_po_details_master a where  a.id=b.job_id and b.grouping in('$master_style_ref') and b.status_active=1 and b.is_deleted=0";
				 $i=1;$conf_tot_po_qty=0;
				foreach($conf_po_arr as $job_no=>$jobData)
				{ $sub_conf_sizeQty_arr=array();
				foreach($jobData as $po_id=>$poData)
				{
				foreach($poData as $country_id=>$countryData)
				{
				 foreach($countryData as $color_id=>$row)
				 {
				 
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="20"><p><? echo $i; ?></p></td>
						<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[('master_style')]; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[('job_prefix')]; ?></div></td>
                        <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('po_number')]; ?></div></td>
                         
						<td width="100" align="center"><p><? echo $country_arr[$country_id]; ?></p></td>
                        <td width="80" align="center"><p><? echo $row[('c_code')]; ?></p></td>
						<td width="80" align="center"><p><? echo $row[('pub_shipment_date')]; ?></p></td>
						<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $color_name_arr[$color_id];//change_date_format ?></div></td>
                        
                        <td width="60" align="right"><p><? echo number_format($row[('color_qty')],0); ?></p></td>
                        
                        <?
						
						foreach($conf_size_arr as $size_id )
						{
							  $conf_sizeQty=$conf_size_color_po_arr[$job_no][$po_id][$country_id][$color_id][$size_id]['size_qty'];
							  $conf_sizeQty_arr[$size_id]+=$conf_sizeQty;
							  $sub_conf_sizeQty_arr[$size_id]+=$conf_sizeQty;
						?>
						<td width="60" align="right"> <p><? echo number_format($conf_sizeQty,0);?> </p></td>
						<?
						}
					?>
                       
					</tr>
					<?
					$conf_tot_po_qty+=$row[('color_qty')];
					$conf_sub_tot_po_qty+=$row[('color_qty')];
					//$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					//$tot_order_value+=$row[csf('po_qty')]*$row[csf('unit_price')];
					$i++;
					 }
					}
				   }
				   ?>
                    
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Merch Total :</td>
						<td align="right"><? echo number_format($conf_sub_tot_po_qty,0);$conf_sub_tot_po_qty=0; ?></td>
                        
                        <?
						foreach($conf_size_arr as $size_id )
						{
						?>
						<td width="60" align="right"> <p><? echo number_format($sub_conf_sizeQty_arr[$size_id],0);?> </p></td>
						<?
						}
					?>
					</tr>
				 
                   <?
				}
				?>
				
				<tfoot>
					 
				 
                    
					<tr class="tbl_bottom">
						<td colspan="8" align="right">G.T Total</td>
						<td align="right"><? echo number_format($conf_tot_po_qty,0); ?></td>
                        
                        <?
						foreach($conf_size_arr as $size_id )
						{
						?>
						<td width="60" align="right"> <p><? echo number_format($conf_sizeQty_arr[$size_id],0);?> </p></td>
						<?
						}
					?>
					</tr>
				</tfoot>
			</table>
          
         <?
         $html=ob_get_contents();
			ob_flush();
			
			foreach (glob(""."*.xls") as $filename) 
			{
			   @unlink($filename);
			}
			
			//html to xls convert
			$name=time();
			$name=$user_id."_".$name.".xls";
			$create_new_excel = fopen(''.$name, 'w');	
			$is_created = fwrite($create_new_excel,$html);
	
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) {
				document.getElementById('report_container_popup').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Button" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
    
		</div>
	</fieldset>
	<?
	exit();
} //Po wise button end

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