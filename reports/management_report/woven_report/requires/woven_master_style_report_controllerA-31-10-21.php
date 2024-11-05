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
	} 
	else $js_select="inquiry_id,style_ref_no";
	//echo $js_select;
	
	if($type_id==1)
	{
  $sql="select b.id,c.id as inquery_id,a.id as job_id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b,wo_quotation_inquery c where a.job_no=b.job_no_mst and b.grouping=c.style_refernce  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grouping is not null and $search_field like '$search_string' $buyer_id_cond $date_cond $entry_form_cond $style_owner_cond $comp_cond order by b.id, b.pub_shipment_date";
 echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Master Style, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "$js_select","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,grouping,pub_shipment_date","",'','0,0,0,0,0,0,3','',0) ;
   exit(); 
	}
	else
	{
		$sql="select a.inquiry_id ,b.id,a.id as job_id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and  b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grouping is not null and $search_field like '$search_string' $buyer_id_cond $date_cond $entry_form_cond $style_owner_cond $comp_cond order by b.id, b.pub_shipment_date";
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
	if($cbo_team_leader>0) $team_leader_cond3="and c.team_leader in($cbo_team_leader)";else $team_leader_cond3="";
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
	if($txt_style_id=="") $inqu_ref_cond=""; else $inqu_ref_cond=" and c.id=$txt_style_id ";
	
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
	$width_td=5090; 
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
                	<th colspan="19">New Development Status (Inquiry)</th>
                    <th colspan="5">Sample Requisition with Booking</th>
                    <th colspan="5">Consumption Entry [CAD] For LA Costing</th>
                    <th colspan="9">Buyer Costing</th>
                    <th colspan="9">SMEE Receive</th>
                    <th colspan="6">Booking Costing</th>
                    <th colspan="4">Work Order</th> 
                    <th colspan="4">Pro Forma Invoice V2</th>
                   
                    
              <tr/>
               <tr style="font-size:13px">
                       	<th width="20">SL</th>
                       	<th width="110">Com. Short Name</th>
                       	<th width="100">Inquiry Date</th>
                        <th width="100">Insert By</th>
                        <th width="100">Team Leader</th>
                       	<th width="100">Dealing Marchant</th>
                      	<th width="100">Sample Merchant</th>
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
                        <th width="80">Confirm Bal. Qty.</th>
                        
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
		
		$sql_inq="select c.id as inquery_id,c.inquery_date,c.mail_send_date,c.team_leader,c.buyer_id,c.color_id,c.color,c.insert_by,c.company_id as company_name,c.system_number_prefix_num,c.dealing_marchant,c.concern_marchant,c.style_description,c.style_refernce,c.season_buyer_wise, c.season_year,c.brand_id,c.actual_req_quot_date,c.actual_sam_send_date,c.actual_sam_send_date,c.con_rec_target_date,c.system_number from  wo_quotation_inquery c where  c.status_active=1 and c.is_deleted=0   $comp_cond2   $team_leader_cond3 $dealing_cond2  $buyer_id_cond2 $season_year_cond2  $brand_name_cond2 $date_cond_target $date_cond $season_name_cond2  $int_ref_cond2   $inqu_ref_cond $team_leader_chk order by c.id "; // ".where_con_using_array($inquery_id_arr,0,'c.id')."
		  
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
			$master_stlye_arr[$row[csf('inquery_id')]]['leader']=$leader_library[$row[csf('team_leader')]];
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
			
     $sql_po="select c.id as inquery_id,a.id as job_id,a.job_no_prefix_num,a.job_quantity,a.inserted_by as po_insert_by,a.insert_date as po_insert_date,a.dealing_marchant as po_dealing_marchant,a.team_leader, a.job_no,a.company_name,a.buyer_name, a.style_ref_no, a.avg_unit_price, a.total_set_qnty as ratio,b.po_quantity, b.id as po_id, b.po_number,b.grouping, b.unit_price, b.shiping_status,b.is_confirmed, b.pub_shipment_date,b.grouping as ref_no,c.inquery_date,c.insert_by,c.system_number_prefix_num,c.dealing_marchant,c.concern_marchant,c.style_description,c.style_refernce,c.season_buyer_wise, c.season_year,c.brand_id,c.actual_req_quot_date,c.actual_sam_send_date,c.actual_sam_send_date,c.con_rec_target_date,c.system_number,d.order_quantity,d.plan_cut_qnty,d.proj_qty from wo_po_details_master a, wo_po_break_down b, wo_quotation_inquery c,wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and d.po_break_down_id=b.id and  a.job_no=d.job_no_mst and b.grouping=c.style_refernce and c.id=a.inquiry_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0   $comp_cond $style_owner_cond  $team_leader_cond $dealing_cond $buyer_id_cond $season_year_cond $job_no_cond   $job_id_cond $order_id_cond_trans $order_no_cond $brand_name_cond $date_cond_target $date_cond $season_name_cond  $int_ref_cond  $style_ref_cond  ".where_con_using_array($inquery_id_arr,0,'c.id')." order by c.id ";
		 
		//echo $sql_po;//die;
		$result_po=sql_select($sql_po); 
	//	$tot_rows=count($result_po);
		foreach($result_po as $row)
		{
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_qty_pcs']+=$row[csf('order_quantity')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_dealing_marchant']=$dealing_library[$row[csf('po_dealing_marchant')]];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_id'].=$row[csf('po_id')].',';
			$master_stlye_arr[$row[csf('inquery_id')]]['grouping'].=$row[csf('grouping')].',';
			$master_stlye_arr[$row[csf('inquery_id')]]['plan_cut']+=$row[csf('plan_cut_qnty')];
			$master_stlye_arr[$row[csf('inquery_id')]]['shiping_status']=$row[csf('shiping_status')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_insert_by']=$row[csf('po_insert_by')];
			$master_stlye_arr[$row[csf('inquery_id')]]['po_insert_date']=$row[csf('po_insert_date')];
			$master_stlye_arr[$row[csf('inquery_id')]]['job_no']=$row[csf('job_no')];
			
			if($row[csf('is_confirmed')]==1)// Confirm
			{
				$master_stlye_arr[$row[csf('inquery_id')]]['conf_quantity']+=$row[csf('order_quantity')];
			}
			else
			{
				$master_stlye_arr[$row[csf('inquery_id')]]['proj_quantity']+=$row[csf('proj_qty')];
			}
			$master_stlye_arr[$row[csf('inquery_id')]]['team_leader']=$leader_library[$row[csf('team_leader')]];
			
			$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no_prefix_num')];
			$style_ref_arr[$row[csf('job_no')]]=$row[csf('style_ref_no')];
			$master_ref_arr[$row[csf('job_no')]]=$row[csf('ref_no')];
			$master_jobNo_arr[$row[csf('po_id')]]=$row[csf('job_no')];
			if($row[csf('style_ref_no')])
			{
			$merch_style_arr2[$row[csf('inquery_id')]].=$row[csf('style_ref_no')].',';
			}
			
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
		
		$booking_sql=sql_select("SELECT c.id as wo_id,c.is_short,c.booking_type,c.booking_date,c.booking_no_prefix_num as booking_prefix,c.booking_no,a.job_no,a.po_break_down_id as po_id,a.booking_no ,a.fin_fab_qnty,b.grouping as ref_no from wo_booking_mst c, wo_booking_dtls a,wo_po_break_down b  where b.id=a.po_break_down_id and c.booking_no=a.booking_no and  a.booking_type  in(1,4,2,6) and  c.status_active=1 and c.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($job_id_arr,0,'b.job_id')." ");
		/*echo "SELECT c.id as wo_id,c.is_short,c.booking_type,c.booking_date,c.booking_no_prefix_num as booking_prefix,c.booking_no,a.job_no,a.po_break_down_id as po_id,a.booking_no ,a.fin_fab_qnty,b.grouping as ref_no from wo_booking_mst c, wo_booking_dtls a,wo_po_break_down b  where b.id=a.po_break_down_id and c.booking_no=a.booking_no and  a.booking_type  in(1,4,2,6) and  c.status_active=1 and c.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($job_id_arr,0,'b.job_id')." ";*/
		
		
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
				
				$booking_data_arr[$row[csf("job_no")]]["all_booking_no"].=$row[csf("booking_no")].',';
			}
			else if($is_short==2 && $booking_type==1)
			{
				$booking_data_arr[$row[csf("job_no")]]["main_booking"].=$row[csf("booking_prefix")].',';
				$booking_data_arr[$row[csf("job_no")]]["main_booking_no"].=$row[csf("booking_no")].',';
				$booking_data_arr[$row[csf("job_no")]]["main_booking_date"]=$row[csf("booking_date")];
				$booking_data_arr[$row[csf("job_no")]]["main_m"]='M';
				$booking_data_arr[$row[csf("job_no")]]["all_booking_no"].=$row[csf("booking_no")].',';
			}
			else if($booking_type==4)//Sample
			{
				$booking_data_arr[$row[csf("job_no")]]["sample_booking"]=$row[csf("booking_prefix")];
				$booking_data_arr[$row[csf("job_no")]]["sample_booking_no"]=$row[csf("booking_no")];
				$booking_data_arr[$row[csf("job_no")]]["sample_m"]='SM';
				$booking_data_arr[$row[csf("job_no")]]["all_booking_no"].=$row[csf("booking_no")].',';
			}
			
			if($booking_type==2)//Trim Booking
			{
				//echo $row[csf("booking_prefix")].'D';
				$booking_data_arr[$row[csf("job_no")]]["trim_booking"].=$row[csf("booking_prefix")].',';
				$booking_data_arr[$row[csf("job_no")]]["trim_booking_no"].=$row[csf("booking_no")].',';
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
			$PiArr[$val[csf('work_order_no')]][$val[csf('cat_id')]]['pi_number'] .= $val[csf('pi_number')].',';
			$PiArr[$val[csf('work_order_no')]][$val[csf('cat_id')]]['pi_mst_id'] .= $val[csf('pi_mst_id')].',';
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
		 $sql_sample_mst="select d.id as req_id,c.id as inquery_id,c.system_number,d.style_ref_no,d.requisition_date,d.requisition_number_prefix_num as prefix_no,d.inserted_by from  wo_quotation_inquery c,sample_development_mst d where  c.style_refernce=d.style_ref_no and d.quotation_id=c.id and  c.status_active=1 and c.is_deleted=0  and  d.status_active=1 and d.is_deleted=0  ".where_con_using_array($inquery_id_arr,0,'c.id')."   $comp_cond2    $buyer_id_cond2 $season_year_cond2  $brand_name_cond2 $date_cond_target $date_cond $season_name_cond2  $int_ref_cond2  $style_ref_cond  order by c.id ";
		$sql_sample_mst_result=sql_select($sql_sample_mst);
		foreach($sql_sample_mst_result as $row)
		{
			$sample_req_arr[$row[csf('inquery_id')]]['requisition_date']=$row[csf('requisition_date')];
			$sample_req_arr[$row[csf('inquery_id')]]['prefix_no']=$row[csf('prefix_no')];
			$sample_req_arr[$row[csf('inquery_id')]]['inserted_by']=$row[csf('inserted_by')];
			//$sample_req_arr[$row[csf('inquery_id')]]['booking_date']=$row[csf('booking_date')];
			//$sample_req_arr[$row[csf('inquery_id')]]['booking_no']=$row[csf('booking_no')];
			$sample_req_arr[$row[csf('inquery_id')]]['req_id']=$row[csf('req_id')];
		}
		unset($sql_sample_mst_result);
		
		$sql_sample_req="select d.id as req_id,a.booking_date,a.booking_no,c.id as inquery_id,c.system_number,d.style_ref_no,d.requisition_date,d.requisition_number_prefix_num as prefix_no,d.inserted_by from  wo_quotation_inquery c,sample_development_mst d,wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a where  c.style_refernce=d.style_ref_no and d.id=b.style_id and d.quotation_id=c.id and  b.booking_no=a.booking_no and  c.status_active=1 and c.is_deleted=0  and  d.status_active=1 and d.is_deleted=0 and  a.status_active=1 and a.is_deleted=0   ".where_con_using_array($inquery_id_arr,0,'c.id')." $comp_cond2    $buyer_id_cond2 $season_year_cond2  $brand_name_cond2 $date_cond_target $date_cond $season_name_cond2  $int_ref_cond2  $style_ref_cond  order by c.id ";
		$sql_sample_req_result=sql_select($sql_sample_req);
		foreach($sql_sample_req_result as $row)
		{
			//$sample_req_arr[$row[csf('inquery_id')]]['requisition_date']=$row[csf('requisition_date')];
			//$sample_req_arr[$row[csf('inquery_id')]]['prefix_no']=$row[csf('prefix_no')];
			//$sample_req_arr[$row[csf('inquery_id')]]['inserted_by']=$row[csf('inserted_by')];
			$sample_req_arr[$row[csf('inquery_id')]]['booking_date']=$row[csf('booking_date')];
			$sample_req_arr[$row[csf('inquery_id')]]['booking_no']=$row[csf('booking_no')];
			//$sample_req_arr[$row[csf('inquery_id')]]['req_id']=$row[csf('req_id')];
		}
		
		$total_conf_qty_pcs=$total_proj_po_qty_pcs=$total_balance_qty=0;
		
		foreach($master_stlye_arr as $inquery_id=>$row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			if($company_name) $company_name=$company_name;else $company_name=0;
			//if($style_owner) $style_owner=$style_owner;else $style_owner=0;
			$job_no=$row[('job_no')];
			if($row[('grouping')]!="")
			{
				
				 $merch_style=0;
			$merch_style=rtrim($merch_style_arr2[$inquery_id],',');
			$no_of_int_ref_noArr=array_unique(explode(",",$merch_style));
				
			//echo $inquery_id.'='.$merch_style.'<br>';
			//$int_ref_no=rtrim($row[('grouping')],',');
			$po_ids=rtrim($row[('po_id')],',');
			//$no_of_int_ref_noArr=array_unique(explode(",",$int_ref_no));
			$no_of_po_idsArr=array_unique(explode(",",$po_ids));
			$po_idsAll=implode(",",$no_of_po_idsArr);
			}
			//echo $job_no.'='.$merch_style.'<br>';
			//print_r($no_of_int_ref_noArr);
		//	echo "<br>";
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
			
			$all_booking_no=rtrim($booking_data_arr[$row[("job_no")]]["all_booking_no"],',');
			$all_booking_nos=implode(",",array_unique(explode(",",$all_booking_no)));
			
			$short_booking_prefix=$booking_data_arr[$row[("job_no")]]["short_booking"];
			$short_booking_no=$booking_data_arr[$row[("job_no")]]["short_booking_no"];
			$short_booking_msg=$booking_data_arr[$row[("job_no")]]["short_s"];
			
			$main_booking_prefixs=rtrim($booking_data_arr[$row[("job_no")]]["main_booking"],',');
			$main_booking_prefix=implode(",",array_unique(explode(",",$main_booking_prefixs)));
			//echo $main_booking_no;
			$main_booking_nos=rtrim($booking_data_arr[$row[("job_no")]]["main_booking_no"],',');
			$main_booking_noArr= array_unique(explode(",",$main_booking_nos));
			//echo $main_booking_no;
			//$fab_pi_cond=="";
				foreach($main_booking_noArr as $fbook)
				{
					//$fab_pi_nos=rtrim($PiArr[$fbook][3]['pi_number'],',');
				//	echo $fbook;
				//	if($fab_pi_cond=="" ) $fab_pi_cond=$fab_pi_nos;else $fab_pi_cond.=",".$fab_pi_nos;
					//$pi_mst_id=rtrim($PiArr[$tbook][4]['pi_mst_id'],',');
					//echo $acc_pi_nos.'DX';
					
				}
			
			//$fab_pi_nos=rtrim($PiArr[$main_booking_no][3]['pi_number'],',');
				//echo $fab_pi_nos.'Dxz';
			$fab_pi_no=implode(",",array_unique(explode(",",$fab_pi_cond)));
			//echo $fab_pi_no;
				
			$main_booking_date=$booking_data_arr[$row[("job_no")]]["main_booking_date"];
			$main_booking_msg=$booking_data_arr[$row[("job_no")]]["main_m"];
			//echo $main_booking_prefix.'DD'.$all_booking_no;
			$sample_booking_prefix=$booking_data_arr[$row[("job_no")]]["sample_booking"];
			$sample_booking_no=$booking_data_arr[$row[("job_no")]]["sample_booking_no"];
			$sample_booking_msg=$booking_data_arr[$row[("job_no")]]["sample_m"];
			if($all_booking_no)
			{
			$print_booking_main='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$row[('style_refernce')]."',".$row[('buyer_name')].",'".$all_booking_nos."','fabric_booking_report',2".')">View</a>';
			}
			else $print_booking_main="";
			
			
			$short_main_sample="";//$print_booking_main="";
			if($main_booking_prefix)
			{
				$short_main_sample="M-".$main_booking_prefix;
				//$print_booking_main='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$inquery_id."',".$row[('buyer_name')].",'".$main_booking_no."','fabric_booking_report',2".')">'.$short_main_sample.'</a>';
				//echo $main_booking_no.'ss';
			}
			if($short_booking_prefix)
			{
				//$short_main_sample.=","."S-".$short_booking_prefix;
				$short_main_short="S-".$short_booking_prefix;
			//	$print_booking_main.=",".'<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$inquery_id."',".$row[('buyer_name')].",'".$short_booking_no."','fabric_booking_report',3".')">'.$short_main_short.'</a>';
			}
			if($sample_booking_prefix)
			{
				//$short_main_sample.=","."SM-".$sample_booking_prefix;
				$short_main_sample="SM-".$sample_booking_prefix;
				//$print_booking_main.=",".'<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$inquery_id."',".$row[('buyer_name')].",'".$sample_booking_no."','fabric_booking_report',4".')">'.$short_main_sample.'</a>';
			}
			
			
				$trim_booking_prefix=rtrim($booking_data_arr[$row[("job_no")]]["trim_booking"],',');
				$trim_booking_no=rtrim($booking_data_arr[$row[("job_no")]]["trim_booking_no"],',');
				//echo $trim_booking_no.'D';
				$trim_booking_noArr=array_unique(explode(",",$trim_booking_no));
				$acc_pi_cond="";$acc_pi_id_cond="";
				foreach($trim_booking_noArr as $tbook)
				{
					$acc_pi_nos=rtrim($PiArr[$tbook][4]['pi_number'],',');
					$pi_mst_id=rtrim($PiArr[$tbook][4]['pi_mst_id'],',');
					//echo $pi_mst_id.'d';
					if($acc_pi_nos)
					{
					if($acc_pi_cond=="" ) $acc_pi_cond=$acc_pi_nos;else $acc_pi_cond.=",".$acc_pi_nos;
					}
					if($pi_mst_id)
					{ 
					if($acc_pi_id_cond=="" ) $acc_pi_id_cond=$pi_mst_id;else $acc_pi_id_cond.=",".$pi_mst_id;
					}
					 
					//$pi_mst_id=rtrim($PiArr[$tbook][4]['pi_mst_id'],',');
					//echo $acc_pi_nos.'DX';
					
				}
				//echo $acc_pi_cond.'DS';
				$acc_pi_no=implode(",",array_unique(explode(",",$acc_pi_cond)));
				$acc_pi_id_cond=implode(",",array_unique(explode(",",$acc_pi_id_cond)));
				//echo $acc_pi_id_cond;
				//$booking_data_arr[$row[csf("job_no")]]["sample_m"]='SM';
			
				$embl_booking_prefix=$booking_data_arr[$row[("job_no")]]["embl_booking"];
				$embl_booking_no=$booking_data_arr[$row[("job_no")]]["embl_booking_no"];
				//$booking_data_arr[$row[csf("job_no")]]["sample_m"]='SM';
				
				
				
				
				
				$embl_pi_nos=rtrim($PiArr[$embl_booking_no][25]['pi_number'],',');
				$embl_pi_no=implode(",",array_unique(explode(",",$embl_pi_nos)));
				$pi_mst_id=rtrim($PiArr[$embl_booking_no][25]['pi_mst_id'],',');
				$pi_mst_id=implode(",",array_unique(explode(",",$pi_mst_id)));
				$sc_int_ref=$SCArr[$row[("job_no")]]['int_ref'];
				$contract_no=$SCNoArr[$row[("job_no")]]['contract_no'];
				//$acc_pi_no=$PiArr[$trim_booking_no][4]['pi_number'];
				//echo $pi_mst_id.'SAS';
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
			//echo $main_booking_prefix.'dds';
			if($main_booking_prefix!="") 
			{
			$print_fab_pi_no='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",".$row[('company_name')].",'".$row[("job_no")]."','".$fab_pi_no."',".$row[('buyer_name')].",'".$main_booking_prefix."','report_generate_woven',7".')">View</a>';
			} else $print_fab_pi_no=""; 
			if($acc_pi_cond) 
			{
				$trim_booking_prefix=rtrim($trim_booking_prefix,',');
			$print_trim_pi_no='<a href="##" onclick="report_generate_pop('.$row[('company_name')].",'".$acc_pi_id_cond."','".$row[("job_no")]."','".$acc_pi_cond."',".$row[('buyer_name')].",'".$trim_booking_prefix."','report_generate_trims',8".')">View</a>';
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
                <td width="100"><p><? echo  $row[('leader')]; ?></p></td>
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
                
				<td width="80" align="center" title="Job=<? echo $job_no;?>"><p><a href="##" onClick="report_po_popup('<? echo $company_name; ?>','<? echo $row[('style_refernce')]; ?>','<? echo $inquery_id; ?>','show_po_merch_report',4)"><? if($job_no) echo count($no_of_int_ref_noArr); else echo " "; ?></a><? //echo count($no_of_int_ref_noArr); ?></p></td>
				<td width="80" align="center"><p><? if($job_no && $row[('proj_quantity')]) echo count($no_of_po_idsArr);else echo " "; ?></p></td>
				 
				 
				<td align="right" width="80" title=""><p><a href="##" onClick="report_po_popup('<? echo $company_name; ?>','<? echo $row[('style_refernce')]; ?>','<? echo $inquery_id; ?>','show_po_listview_report',5)"><? echo $row[('proj_quantity')]; ?></a></p></td>
                
                <td width="80" align="right" title=""><p><a href="##" onClick="report_po_popup('<? echo $company_name; ?>','<? echo $row[('style_refernce')]; ?>','<? echo $inquery_id; ?>','show_po_listview_report',6)"><? echo $row[('conf_quantity')]; ?></a></p></td>
                
                <td width="80" align="right" title=""><p><? $balance_qty=$row[('proj_quantity')]-$row[('conf_quantity')];echo $balance_qty;?></p></td>
                
                <td width="80" align="center" title=""><p><?   echo $print_button_job;?></p></td>
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
				$total_balance_qty+=$balance_qty;
			
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
               
                <td width="80" align="right"><? echo number_format($total_balance_qty,0);?></td>
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
				// $po_sql="select a.job_no,b.po_number,a.style_ref_no as master_style from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c where  a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id and b.grouping in('$master_style_ref') and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.inquiry_id=$inquire_id";//and a.inquiry_id=$inquire_id
				 $po_sql="select a.job_no,b.po_number,a.style_ref_no as master_style from wo_po_break_down b,wo_po_details_master a where  a.id=b.job_id  and b.grouping in('$master_style_ref') and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and a.inquiry_id=$inquire_id";
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
				$po_sql="select a.job_no,a.job_no_prefix_num,a.buyer_name,a.brand_id,a.product_dept,a.dealing_marchant,a.season_buyer_wise,b.id as po_id, b.po_number,a.style_ref_no as master_style,b.po_received_date,b.shipment_date as orgi_shipment_date,b.is_confirmed,b.pub_shipment_date,b.po_quantity as po_qty, b.unit_price as unit_price,b.up_charge as up_charge,c.country_id,c.size_number_id  as size_id,c.color_number_id as color_id,c.order_quantity,c.order_total,c.code_id,c.proj_qty from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c where  a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id and b.grouping in('$master_style_ref') and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.inquiry_id=$inquire_id";
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
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['color_qty']+=$row[csf('proj_qty')];
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['c_code']=$code_arr[$row[csf('code_id')]];
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['po_number']=$row[csf('po_number')];
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['master_style']=$row[csf('master_style')];
					$proj_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]]['job_prefix']=$row[csf('job_no_prefix_num')];
					$proj_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
					 $proj_size_color_po_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['size_qty']+=$row[csf('proj_qty')];
					$tot_proj+=$row[csf('proj_qty')];
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
            <?
            if($type==5)
			{
			?>
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
			}
			if($type==6)
			{
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
			}
			
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
if($action == "fabric_booking_report") 
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);//Main Fabric
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$merch_style=str_replace("'","",$inquire_ids);
	//echo $inquire_ids.'DDDD';
	$booking_no=str_replace("'","",$txt_booking_no);
	$booking_noArr=array_unique(explode(",",$booking_no));
	//$booking_noAll=implode("'",$booking_noArr);
	//echo $booking_noAll;die;
	$sql_req="SELECT a.id, a.requisition_number, a.style_ref_no,b.booking_no from sample_development_mst a,wo_non_ord_samp_booking_dtls b where  a.id=b.style_id and a.style_ref_no='$merch_style' and a.entry_form_id=449 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
    $req_dataArray=sql_select($sql_req);
	foreach($req_dataArray as $row)
	{
		$req_no_arr[$row[csf("id")]]=$row[csf("requisition_number")];
		$req_no_booking_arr[$row[csf("id")]]=$row[csf("booking_no")];
	}
					
	$all_booking="";
	foreach($booking_noArr as $book)
	{
		if($all_booking=="") $all_booking="'".$book."'";else $all_booking.=","."'".$book."'";
	}
	//echo $booking_no;die;
	$sql_booking=sql_select("select a.is_approved,a.fabric_source,a.is_short,a.booking_type,a.booking_no,b.job_no,a.po_break_down_id as all_po from wo_booking_mst a,wo_booking_dtls b where  a.booking_no=b.booking_no and a.booking_no in($all_booking) and a.status_active=1 and b.status_active=1 group by a.is_short,a.booking_type,a.booking_no,a.is_approved,a.fabric_source,a.po_break_down_id,b.job_no");
	// echo "select a.is_short,a.booking_type,a.booking_no,b.job_no from wo_booking_mst a,wo_booking_dtls b where  a.booking_no=b.booking_no and a.booking_no in($all_booking) and a.status_active=1 and b.status_active=1";
	foreach($sql_booking as $row)
	{
		$is_short=$row[csf("is_short")];
		$booking_type=$row[csf("booking_type")];
		 
			if($is_short==1 && $booking_type==1) //short
			{
				 
				$booking_short_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
				$booking_short_job_arr[$row[csf("booking_no")]]=$row[csf("job_no")];
			}
			else if($is_short==2 && $booking_type==1) // Main 
			{
			
				$booking_main_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
			}
			else if($booking_type==4)//Sample
			{
				 
				$booking_sample_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
				$booking_sample_job_arr[$row[csf("booking_no")]]=$row[csf("job_no")];
				$booking_sample_po_arr[$row[csf("booking_no")]]=$row[csf("all_po")];
				$booking_sample_approv_arr[$row[csf("booking_no")]]=$row[csf("is_approved")];
				$booking_sample_source_arr[$row[csf("booking_no")]]=$row[csf("fabric_source")];
			}
	}
	//print_r($booking_main_arr);die;
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$marchentr_email = return_library_array("select id,team_member_email from lib_mkt_team_member_info ","id","team_member_email");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$company_info=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");
	?>
    <div>
    <?
	$summ_main_fab_arr=array();
	foreach($booking_main_arr as $txt_booking_no) //Main Booking
	{
	 $txt_booking_no="'".$txt_booking_no."'";
	$po_booking_info=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0");
	$path=str_replace("'","",$path);
	if($path!="") $path=$path; else $path="../../../";
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_date_row)=$nameArray_approved_date;
	$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_comments_row)=$nameArray_approved_comments;
	$uom=0;
	$job_data_arr=array();
	foreach ($po_booking_info as $result_buy){
	$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
	$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
	$job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
	$job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
	$job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
	$job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
	$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
	$job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
	$job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
	$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
	$job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]=$marchentr_email[$result_buy[csf('dealing_marchant')]];
	$job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
	$job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
	$job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
	$job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
	}
	$job_no= implode(",",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
	$product_code=implode(",",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
	$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
	$style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(",",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
	$dealing_marchant_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
	$season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
	$client_id= implode(",",array_unique($job_data_arr['client']));

	$po_data=array();
	if($db_type==0){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0   group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	if($db_type==2){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	foreach ($nameArray_job as $result_job){
		$po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
	}
	$grouping=implode(",",array_unique(array_filter($po_data['grouping'])));

	$nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition,a.delivery_address, a.pay_mode, a.currency_id from wo_booking_mst a  where   a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 ");

    ?>
	<table style="border:1px solid black;table-layout: fixed; " width="100%">
		<tr>
			<td><img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' /></td>
			<td style="text-align: center;">
				<span style=" font-size:20px; font-weight:bold"><? echo $company_library[$cbo_company_name]; ?></span><br>
				<?
                            $nameArray2=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray2 as $result)
                            {
                            ?>
                                <? echo $result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$result[csf('city')].' '.$result[csf('zip_code')].' '.$result[csf('province')].' '.$country_arr[$result[csf('country_id')]]; ?><br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website: <? echo $result[csf('website')].'<br>';
                            }
                            ?>
				<span style="font-size:16px; font-weight:bold">Fabric Purchase Order</span>
			</td>
			<td style="text-align: right; padding-right: 30px"><span style="font-size:16px; font-weight:bold;">Booking No: <? echo $nameArray[0][csf('booking_no')];?><? echo "(".$fabric_source[$nameArray[0][csf('fabric_source')]].")"?></span></td>
		</tr>
	</table>
	<? foreach ($nameArray as $result) {
		$currency_id=$result[csf('currency_id')];
		$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
	 ?>
    <table style="margin-top: 5px" class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
        <tr>
        	<th width="175" style="text-align: left">Supplier Name </th>
            <td width="175"> <?
				if($result[csf('pay_mode')]==5){
				echo $company_library[$result[csf('supplier_id')]];
				}
				else{
				echo $supplier_name_arr[$result[csf('supplier_id')]];
				}
			?>
			</td>
			<th width="175" style="text-align: left">Dealing Merchant </th>
            <td width="175" > <? echo $dealing_marchant; ?></td>
            <th width="175" style="text-align: left">Buyer/Agent Name</th>
            <td width="175"> <? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>

        </tr>
        <tr>
        	<th style="text-align: left">Attention </th>
            <td> <? echo $result[csf('attention')]; ?></td>
            <th style="text-align: left">Merchant E-Mail id </th>
            <td> <? echo $dealing_marchant_email ?></td>
            <th style="text-align: left">Garments Item </th>
            <td> <?
	            $gmts_item_name="";
				$gmts_item=explode(',',$gmts_item_id);
				for($g=0;$g<=count($gmts_item); $g++)
				{
				$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
			?>
			</td>
        </tr>
        <tr>
            <th width="175" style="text-align: left">Booking Date </th>
            <td width="175"> <? echo change_date_format($booking_date,'dd-mm-yyyy','-','');?></td>
            <th style="text-align: left">Fabric ETD </th>
            <td> <? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
            <th style="text-align: left">Internal Ref No. </th>
            <td> <?echo $grouping ?></td>
        </tr>
        <tr>
        	<?
        	$delivery_address=explode("\n",$result[csf('delivery_address')]);
        	?>
        	<th style="text-align: left">Delivery Address </th>
            <td> <? if(count($delivery_address)>0){
            	foreach ($delivery_address as $key => $value) { ?>
            	<? echo $value ?><br>
            	<? }
            } ?></td>
            <th style="text-align: left">Pay Mode</th>
            <td> <? echo $pay_mode[$result[csf('pay_mode')]] ?></td>
            <th style="text-align: left">Currency</th>
            <td> <? echo $currency[$result[csf('currency_id')]] ?></td>
        </tr>
        <tr>
        	<th style="text-align: left">Remarks </th>
            <td colspan="5"> <? echo $result[csf('remarks')]?></td>
        </tr>
    </table>
    <? } ?>
    <?
    $nameArray_fabric_description = "select a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id, a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia, a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no, c.style_description,  c.job_no_prefix_num, d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color, b.po_number,d.remark FROM wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b, wo_po_details_master c, wo_booking_dtls d WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and a.job_no = c.job_no and d.job_no=c.job_no and d.booking_no = $txt_booking_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 and b.job_no_mst=d.job_no and b.id=d.po_break_down_id and b.is_deleted=0 and b.status_active=1 group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type , b.po_number, c.style_description,d.remark order by a.job_no,d.fabric_color_id";
    	//echo $nameArray_fabric_description; die;
    	$result_set=sql_select($nameArray_fabric_description);
		 foreach( $result_set as $row)
		 {
		 	$uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
			$fabric_attr = array('uom','construction','composition','c_type','gsm','dia','dia_type','gmt_color','style_ref_no','po_number','style_description','remark');
			foreach ($fabric_attr as $attr) {
				$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]][$attr][] = $row[csf($attr)];
			}

			$color_attr = array('rates');
			foreach ($color_attr as $attr) {
				$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]][$attr] = $row[csf($attr)];
			}
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['amounts'] += $row[csf('amounts')];
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['fin_fab_qntys'] += $row[csf('fin_fab_qntys')];

			$summery_attr = array('body_part_id','construction','composition','po_number','rates','amounts','fin_fab_qntys','c_type','gsm','dia','dia_type','fab_color');
			foreach ($summery_attr as $attr) {
				$string = $row[csf("body_part_id")].'**'.$row[csf("construction")].'**'.$row[csf("composition")];
				if($attr == 'fab_color'){
					$fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $color_library[$row[csf($attr)]];
				}
				else{
					$fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $row[csf($attr)];
				}
			}


		 }
		 /*echo '<pre>';
		 print_r($fabric_detail_arr); die;*/

		 foreach ($fabric_detail_arr as $job_no => $uom_data_value) {
		 	foreach ($uom_data_value as $uom_key=> $po_data_arr) {
		 		foreach ($po_data_arr as $po_number => $construction_arr){
		 			foreach ($construction_arr as $cons_key => $body_part_arr) {
		 				foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {		 					
							foreach ($gmt_color_data as $gmt_color_key => $fabric_dtls){
								foreach ($fabric_dtls as $fabric_dtls_id => $body_part_dtls) {
									$total_fab_color[$job_no][$uom_key]+=count($body_part_dtls['fab_color']);
								}								
							}
    					}
		 			}
    			}
		 	}
		 }

		 	/*echo '<pre>';
			print_r($total_fab_color); die;*/
			//$uom_val='';
		 	$grand_fin_fab_qty_sum =0;
			$grand_amount_sum =0;
			foreach($uom_data_arr as $uom_id=>$uom_val){?>
			    <div style="margin-top:15px">
			        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
			            <tr style="font-weight:bold">
			            	<td width="150">Job No.</td>
			            	<td width="150">Style Ref No.</td>
			            	<td width="150">Po. No.</td>
			                <td width="150">Body Part</td>
			                <td width="200">Fabric Construction</td>
			                <td width="200">Fabric Composition</td>
			                <td width="100">Color Type</td>
			                <td width="50">GSM</td>
			                <td width="100">Dia/C-Width</td>
			                <td width="100">Gmts. Color</td>
			                <td width="100">Fabric Color</td>
			                <td width="150">Lab Dip No/Mill Ref. No</td>
			                <td width="100">Fin Fab Qty(<? echo $uom_val ?>)</td>
			                <td width="50">Rate(<? echo $currency[$currency_id] ?>)</td>
			                <td width="50">Amount(<? echo $currency[$currency_id] ?>)</td>
			                <td width="100">Remarks</td>
			            </tr>
    <?
    	$fab_color_row = '';
    	foreach ($fabric_detail_arr as $job_no => $uom_data_value) {
    		$job_fin_fab_qty_sum =0;
    		$job_amount_sum =0;
    		$job_row='';

    		foreach ($uom_data_value as $uom_key=> $po_data_arr) {
    			$job_row=count($po_data_arr);
    			if($uom_id == $uom_key){
    				$poNum=1;
    				foreach ($po_data_arr as $po_number => $construction_arr) {
    					foreach ($construction_arr as $cons_key => $body_part_arr) {
    						foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {
    							//$total_fab_color_row='';
    							foreach ($gmt_color_data as $gmt_color_key => $fabric_dtls){
    								foreach ($fabric_dtls as $fabric_dtls_id => $body_part_dtls) {
    									$color = 1;
				    					$fin_fab_qty_sum = 0;
				    					$amount_sum = 0;
				    					$fab_color_row = count($body_part_dtls['fab_color']);

				    					foreach ($body_part_dtls['fab_color'] as $fab_color_key => $fab_color_dtls) {
				    						if($color == 1){
				    							$fin_fab_qty_sum += $fab_color_dtls['fin_fab_qntys'];
				    							$amount_sum += $fab_color_dtls['amounts'];
													//$summ_main_fab_arr[1]['qty']+=$fab_color_dtls['fin_fab_qntys'];
													//$summ_main_fab_arr[1]['amt']+=$fab_color_dtls['amounts'];
				    						 	?>
				    							<tr>
				    								<? if($poNum==1){ ?>
				    								<td rowspan="<? echo $total_fab_color[$job_no][$uom_key] ?>"><? echo $job_no ?></td>
				    								<td rowspan="<? echo $total_fab_color[$job_no][$uom_key] ?>"><? echo implode(", ",array_unique($body_part_dtls['style_ref_no'])); ?></td>
				    								<? } ?>
				    								<td rowspan="<? echo $fab_color_row ?>"><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['po_number'])) ?></span></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo $body_part[$body_part_key] ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['construction'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['composition'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? 
									            	echo $color_type[$body_part_dtls['c_type'][0]];
									            	/*foreach ($body_part_dtls['c_type'] as $value) {
									            		$color_type_text[$fabric_dtls_id] = $color_type[$value];
									            	}
									            	echo implode(",",$color_type_text[$fabric_dtls_id])*/
									            	 ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['gsm'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['dia'])).','.$fabric_typee[implode(",",array_unique($body_part_dtls['dia_type']))] ?></td>
									            	<td><? echo $color_library[$gmt_color_key] ?></td>
									            	<td><? echo $color_library[$fab_color_key] ?></td>
									            	<td><? $lapdip_no="";
													$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$fab_color_key."");
													if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; ?></td>
									            	<td><? echo number_format($fab_color_dtls['fin_fab_qntys'],3) ?></td>
									            	<td><? echo number_format($fab_color_dtls['rates'],4) ?></td>
									            	<td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
									            	<td><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['remark'])) ?></span></td>
									            </tr>
				    						<? } else{
				    						$fin_fab_qty_sum += $fab_color_dtls['fin_fab_qntys'];
				    						$amount_sum += $fab_color_dtls['amounts'];
											//$summ_main_fab_arr[1]['qty']+=$fab_color_dtls['fin_fab_qntys'];
											//$summ_main_fab_arr[1]['amt']+=$fab_color_dtls['amounts'];
				    					 	?>
				    							<tr>
				    								<td><? echo $color_library[$gmt_color_key] ?></td>
				    								<td><? echo $color_library[$fab_color_key] ?></td>
									            	<td><? $lapdip_no="";
													$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$fab_color_key."");
													if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; ?></td>
									            	<td><? echo number_format($fab_color_dtls['fin_fab_qntys'],3) ?></td>
									            	<td><? echo number_format($fab_color_dtls['rates'],4) ?></td>
									            	<td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
									            	<td><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['remark'])) ?></span></td>
				    							</tr>
				    						<? }
				    						$color++;
				    					}
					    					$job_fin_fab_qty_sum += $fin_fab_qty_sum;
					        				$job_amount_sum += $amount_sum;
					        				$poNum++;
    								}			    					
				        		}
				        	}
				        }
    			 	}

    					$grand_fin_fab_qty_sum +=$job_fin_fab_qty_sum;
    					$grand_amount_sum += $job_amount_sum;
	    				?>
						<tr>
							<th colspan="11">&nbsp</th>
							<th>Job Total</th>
							<th><? echo def_number_format($job_fin_fab_qty_sum,2); ?></th>
							<th>&nbsp</th>
							<th><? echo def_number_format($job_amount_sum,2); ?></th>
							<th>&nbsp</th>
						</tr>
	    				<?

    			}

    		}

    	}

    ?>

        </table>
    </div>
    <? 		} ?>
    <?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS';
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS';
	   }
	  
	  $summ_main_fab_arr[1]['qty']=$grand_fin_fab_qty_sum;
	  $summ_main_fab_arr[1]['amt']=$grand_amount_sum;
	   ?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
	    <tr>
			<th colspan="11" width="350">&nbsp</th>
			<th width="83">Grand Total</th>
			<th width="75"><? echo def_number_format($grand_fin_fab_qty_sum,2); ?></th>
			<th width="74">&nbsp</th>
			<th width="99"><? echo def_number_format($grand_amount_sum,2); ?></th>
			<th width="74">&nbsp</th>
	    </tr>
	</table>
	<div style="margin-top:15px">
		<span style="font-weight: bold; margin-bottom: 2px;">Summary:</span>
		<? foreach ($fabric_detail_summery as $uom_id => $summery_data_arr) {
		?>
		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center; margin-top: 15px" rules="all">
			<tr style="font-weight:bold">
				<td>Fabric Type</td>
				<td>Construction</td>
				<td>Composition</td>
				<td>Color Type</td>
				<td>GSM</td>
				<td>Dia/C-Width</td>
				<td>Fabric Color</td>
				<td>No. of PO</td>
				<td>Consumption(<? echo $unit_of_measurement[$uom_id] ?>)</td>
				<td>Rate(<? echo $currency[$currency_id] ?>)</td>
	            <td>Amount(<? echo $currency[$currency_id] ?>)</td>
			</tr>
		<?
		foreach ($summery_data_arr as $summery_data) { ?>
			<tr>
				<td><? echo $body_part[implode(",",array_unique($summery_data['body_part_id']))] ?></td>
				<td><? echo implode(",",array_unique($summery_data['construction']))?></td>
				<td><? echo implode(",",array_unique($summery_data['composition']))?></td>
				<td><? 
					foreach ($summery_data['c_type'] as $value)
					{
	            		$color_type_text[$value] = $color_type[$value];
	            	}
	            	echo implode(",",$color_type_text);
				//echo $color_type[implode(",",array_unique($summery_data['c_type']))] 
				?></td>
				<td><? echo implode(",",array_unique($summery_data['gsm']))?></td>
				<td><? echo implode(",",array_unique($summery_data['dia'])).','.$fabric_typee[implode(",",array_unique($summery_data['dia_type']))] ?></td>
				<td><? echo implode(", ",array_unique($summery_data['fab_color'])) ?></td>
				<td><? echo count(array_unique($summery_data['po_number'])) ?></td>
				<td><? echo array_sum($summery_data['fin_fab_qntys']) ?></td>
				<td><? echo number_format(array_sum($summery_data['amounts'])/array_sum($summery_data['fin_fab_qntys']),4) ?></td>
				<td><? echo array_sum($summery_data['amounts']) ?></td>
			</tr>

		<? }
		}
		?>
		</table>
	</div>
	<div style="margin-top: 10px;">
		<table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($grand_amount_sum,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($grand_amount_sum,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
	</div>

    <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px; ">
		<tr>
			<td width="50%" style="border:1px solid; border-color:#000;" valign="top">
				<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
						<th width="97%" align="left" height="30" colspan="2">Terms &amp; Condition</th>
						</tr>
					</thead>
					<tbody>
					<?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");
					if ( count($data_array)>0)
						{
						$i=0;
						foreach( $data_array as $row )
						{
						$i++;
						?>
						<tr id="settr_1" valign="top">
							<td><? echo $i;?>)&nbsp</td>
							<td><? echo $row[csf('terms')]; ?></td>
						</tr>
						<?
						}
						}
					?>
					</tbody>
				</table>
			</td>
			<td width="50%" valign="top" style="border:1px solid; border-color:#000;">
				<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
					<tr align="center">
						<th width="97%" align="left" height="30">Approved Instructions</th>
					</tr>
					<tr>
						<td><?  echo $nameArray_approved_comments_row[csf('comments')];  ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<? echo signature_table(121, $cbo_company_name, "1330px", 1);
   
   } 
   foreach($booking_short_arr as $booking_no)
   {
	    
		$txt_booking_no="'".$booking_no."'";
		//$job_no=$booking_short_job_arr[$booking_no];
		$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
    $season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
    $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
    $marchentr_email = return_library_array("select id,team_member_email from lib_mkt_team_member_info ","id","team_member_email");
    $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
    $supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

    $company_info=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");
    if($company_info[0][csf('plot_no')] != ''){
		$address.=$company_info[0][csf('plot_no')].',';
	}
	if($company_info[0][csf('level_no')] != ''){
		$address.=" ".$company_info[0][csf('level_no')].',';
	}
	if($company_info[0][csf('road_no')] != ''){
		$address.=" ".$company_info[0][csf('road_no')].',';
	}
	if($company_info[0][csf('block_no')] != ''){
		$address.=" ".$company_info[0][csf('block_no')].'<br>';
	}
	if($company_info[0][csf('city')] != ''){
		$address.=$company_info[0][csf('city')].',';
	}
	if($company_info[0][csf('zip_code')] != 0 && $company_info[0][csf('zip_code')] != ''){
		$address.='-'.$company_info[0][csf('zip_code')].',';
	}
	if($company_info[0][csf('province')] != ''){
		$address.=$company_info[0][csf('province')].','.$country_arr[$company_info[0][csf('country_id')]];
	}
	if($company_info[0][csf('email')] != ''){
		$add_info.='Email: '.$company_info[0][csf('email')];
	}
	if($company_info[0][csf('website')] != ''){
		$add_info.=', Website: '.$company_info[0][csf('website')];
	}

    $po_booking_info=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0");
    $path=str_replace("'","",$path);
    if($path!="") $path=$path; else $path="../../../";
    $nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 ");
    list($nameArray_approved_row)=$nameArray_approved;
    $nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
    list($nameArray_approved_date_row)=$nameArray_approved_date;
    $nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
    list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    $uom=0;
    $job_data_arr=array();
    foreach ($po_booking_info as $result_buy){
    $job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
    $job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
    $job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
    $job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];

    $job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
    $job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
    $job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
    $job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
    $job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
    $job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
    $job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]=$marchentr_email[$result_buy[csf('dealing_marchant')]];
    $job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
    $job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
    $job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
    $job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
    }
    $job_no= implode(",",array_unique($job_data_arr['job_no']));
    $job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
    $product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
    $product_code=implode(",",array_unique($job_data_arr['product_code']));
    $pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
    $gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
    $style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
    $style_description=implode(",",array_unique($job_data_arr['style_description']));
    $dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
    $dealing_marchant_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
    $season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
    $order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
    $qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
    $client_id= implode(",",array_unique($job_data_arr['client']));

    $po_data=array();
    if($db_type==0){
    $nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0   group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
    }
    if($db_type==2){
    $nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
    }
    foreach ($nameArray_job as $result_job){
        $po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
    }
    $grouping=implode(",",array_unique(array_filter($po_data['grouping'])));

    $nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition,a.delivery_address, a.currency_id from wo_booking_mst a  where   a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 ");

    ?>
    <table style="border:1px solid black;table-layout: fixed; " width="100%">
        <tr>
            <td><img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' /></td>
            <td style="text-align: center;">
                <span style=" font-size:20px; font-weight:bold"><? echo $company_library[$cbo_company_name]; ?></span><br>
                <span><? echo $address; ?></span><br>
                <span><? echo $add_info ?></span><br>
                <span style="font-size:16px; font-weight:bold">Short Fabric Purchase Order</span>
            </td>
            <td style="text-align: right; padding-right: 30px"><span style="font-size:16px; font-weight:bold;">Booking No: <? echo $nameArray[0][csf('booking_no')];?><? echo "(".$fabric_source[$nameArray[0][csf('fabric_source')]].")"?></span></td>
        </tr>
    </table>
    <? foreach ($nameArray as $result) {
        $currency_id=$result[csf('currency_id')];
        $booking_date=$result[csf('update_date')];
            if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
            $booking_date=$result[csf('insert_date')];
            }
     ?>
    <table style="margin-top: 5px" class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
        <tr>
            <th width="175" style="text-align: left">Supplier Name </th>
            <td width="175"><?
                if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
                echo $company_library[$result[csf('supplier_id')]];
                }
                else{
                echo $supplier_name_arr[$result[csf('supplier_id')]];
                }
            ?>
            </td>
            <th width="175" style="text-align: left">Dealing Merchant </th>
            <td width="175" ><? echo $dealing_marchant; ?></td>
            <th width="175" style="text-align: left">Buyer/Agent Name</th>
            <td width="175"><? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>

        </tr>
        <tr>
            <th style="text-align: left">Attention </th>
            <td><? echo $result[csf('attention')]; ?></td>
            <th style="text-align: left">Merchant E-Mail id </th>
            <td><? echo $dealing_marchant_email ?></td>
            <th style="text-align: left">Garments Item </th>
            <td><?
                $gmts_item_name="";
                $gmts_item=explode(',',$gmts_item_id);
                for($g=0;$g<=count($gmts_item); $g++)
                {
                $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                }
                echo rtrim($gmts_item_name,',');
            ?>
            </td>
        </tr>
        <tr>
            <th width="175" style="text-align: left">Booking Date </th>
            <td width="175"><? echo change_date_format($booking_date,'dd-mm-yyyy','-','');?></td>
            <th style="text-align: left">Fabric ETD </th>
            <td><? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
            <th style="text-align: left">Internal Ref No. </th>
            <td><?echo $grouping ?></td>
        </tr>
        <tr>
        	<?
        	$delivery_address=explode("\n",$result[csf('delivery_address')]);
        	?>
        	<th style="text-align: left">Delivery Address </th>
            <td><? if(count($delivery_address)>0){
            	foreach ($delivery_address as $key => $value) { ?>
            	<? echo $value ?><br>
            	<? }
            } ?></td>
            <th style="text-align: left">Pay Mode</th>
            <td><? echo $pay_mode[$result[csf('pay_mode')]]?></td>
            <th style="text-align: left">Currency </th>
            <td><? echo $currency[$result[csf('currency_id')]]?></td>
        </tr>
        <tr>
            <th style="text-align: left">Remarks </th>
            <td colspan="5"><? echo $result[csf('remarks')]?></td>
        </tr>
    </table>
    <? } ?>
    <?
    //$nameArray_fabric_description= "select  a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id,a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia,a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no,c.job_no_prefix_num,d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d,wo_po_details_master c WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and a.job_no = c.job_no  and d.job_no=c.job_no and d.booking_no = $txt_booking_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type order by a.job_no,d.fabric_color_id ";
    $nameArray_fabric_description = "select a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id, a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia, a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys, sum(grey_fab_qnty) as grey_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no, c.style_description,  c.job_no_prefix_num, d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color, b.po_number FROM wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b, wo_po_details_master c,  wo_booking_dtls d WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and a.job_no = c.job_no and d.job_no=c.job_no and d.booking_no = $txt_booking_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 and b.job_no_mst=d.job_no and b.id=d.po_break_down_id and b.is_deleted=0 and b.status_active=1 group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type , b.po_number, c.style_description order by a.job_no,d.fabric_color_id";
        //echo $nameArray_fabric_description; die;
        $result_set=sql_select($nameArray_fabric_description);
         foreach( $result_set as $row)
         {
            $uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
            $main_data=array('style_ref_no','style_description');
            foreach ($main_data as $mainAttr) {
                $fabric_detail_arr[$row[csf("job_no")]][$mainAttr] = $row[csf($mainAttr)];
            }
            $fabric_attr = array('uom','construction','composition','c_type','gsm','dia','dia_type','gmt_color','style_ref_no','po_number','style_description');
            foreach ($fabric_attr as $attr) {
                $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$attr][] = $row[csf($attr)];
            }

            $color_attr = array('rates');
            foreach ($color_attr as $attr) {
                $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['fab_color'][$row[csf("fab_color")]][$attr] = $row[csf($attr)];
            }
            $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['fab_color'][$row[csf("fab_color")]]['amounts'] += $row[csf('amounts')];
            $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['fab_color'][$row[csf("fab_color")]]['grey_fab_qntys'] += $row[csf('grey_fab_qntys')];

            $summery_attr = array('body_part_id','construction','composition','po_number','rates','amounts','grey_fab_qntys','c_type','gsm','dia','dia_type','fab_color');
            foreach ($summery_attr as $attr) {
                $string = $row[csf("body_part_id")].'**'.$row[csf("construction")].'**'.$row[csf("composition")];
                if($attr == 'fab_color'){
                    $fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $color_library[$row[csf($attr)]];
                }
                else{
                    $fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $row[csf($attr)];
                }
            }


         }

            /*echo '<pre>';
            print_r($fabric_detail_arr); die;*/
            //$uom_val='';
            $grand_fin_fab_qty_sum =0;
            $grand_amount_sum =0;
            foreach($uom_data_arr as $uom_id=>$uom_val){?>  
                <div style="margin-top:15px">
                    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
                    <caption><b style="float:left"> <? echo 'Job No. : '.$job_no.', Style Ref. : '. $style_sting.', Style Des. : '.$style_description; ?></b> </caption>
                        <tr style="font-weight:bold">
                            <td width="150">Po. No.</td>
                            <td width="150">Body Part</td>
                            <td width="200">Fabric Construction</td>
                            <td width="200">Fabric Composition</td>
                            <td width="100">Color Type</td>
                            <td width="50">GSM</td>
                            <td width="100">Dia/C-Width</td>
                            <td width="100">Gmts. Color</td>
                            <td width="100">Fabric Color</td>
                            <td width="150">Lab Dip No/Mill Ref. No</td>
                            <td width="100">Fin Fab Qty(<? echo $uom_val ?>)</td>
                            <td width="50">Rate(<? echo $currency[$currency_id] ?>)</td>
                            <td width="50">Amount(<? echo $currency[$currency_id] ?>)</td>
                        </tr>
    <?
        $fab_color_row = '';
        foreach ($fabric_detail_arr as $job_no => $uom_data_arr) {

            $job_fin_fab_qty_sum =0;
            $job_amount_sum =0;
            foreach ($uom_data_arr as $uom_key => $construction_arr) {
                if($uom_id == $uom_key){
                   
                    foreach ($construction_arr as $cons_key => $body_part_arr) {
                        foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {
                            foreach ($gmt_color_data as $gmt_color_key => $body_part_dtls){
                        $color = 1;
                        $fin_fab_qty_sum = 0;
                        $amount_sum = 0;
                        $fab_color_row = count($body_part_dtls['fab_color']);
                        foreach ($body_part_dtls['fab_color'] as $fab_color_key => $fab_color_dtls) {
                            if($color == 1){
                                $fin_fab_qty_sum += $fab_color_dtls['grey_fab_qntys'];
                                $amount_sum += $fab_color_dtls['amounts'];
                                ?>
                                <tr>
                                    <td rowspan="<? echo $fab_color_row ?>"><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['po_number'])) ?></span></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo $body_part[$body_part_key] ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['construction'])) ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['composition'])) ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo $color_type[implode(",",array_unique($body_part_dtls['c_type']))] ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['gsm'])) ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['dia'])).','.$fabric_typee[implode(",",array_unique($body_part_dtls['dia_type']))] ?></td>
                                    <td><? echo $color_library[$gmt_color_key] ?></td>
                                    <td><? echo $color_library[$fab_color_key] ?></td>
                                    <td><? $lapdip_no="";
                                    $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$fab_color_key."");
                                    if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; ?></td>
                                    <td><? echo number_format($fab_color_dtls['grey_fab_qntys'],3) ?></td>
                                    <td><? echo number_format($fab_color_dtls['rates'],2)?></td>
                                    <td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
                                </tr>
                            <? } else{
                            $fin_fab_qty_sum += $fab_color_dtls['grey_fab_qntys'];
                            $amount_sum += $fab_color_dtls['amounts'];
                            ?>
                                <tr>
                                    <td><? echo $color_library[$fab_color_key] ?></td>
                                    <td><? $lapdip_no="";
                                    $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$fab_color_key."");
                                    if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; ?></td>
                                    <td><? echo number_format($fab_color_dtls['grey_fab_qntys'],3) ?></td>
                                    <td><? echo number_format($fab_color_dtls['rates'],2)?></td>
                                    <td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
                                </tr>
                        <? }
                            $color++;
                        }?>
                                <!-- <tr>
                                    <th colspan="9">&nbsp</th>
                                    <th>Sub Total</th>
                                    <th><? echo def_number_format($fin_fab_qty_sum,2); ?></th>
                                    <th>&nbsp</th>
                                    <th><? echo def_number_format($amount_sum,2); ?></th>
                                </tr> -->
                    <?
                        $job_fin_fab_qty_sum += $fin_fab_qty_sum;
                        $job_amount_sum += $amount_sum;

                     }
                        }


                    }
                    $grand_fin_fab_qty_sum +=$job_fin_fab_qty_sum;
                    $grand_amount_sum += $job_amount_sum;
                    ?>
                            <tr>
                                <th colspan="9">&nbsp</th>
                                <th>Job Total</th>
                                <th><? echo def_number_format($job_fin_fab_qty_sum,2); ?></th>
                                <th>&nbsp</th>
                                <th><? echo def_number_format($job_amount_sum,2); ?></th>
                            </tr>

                    <?


                 }
            }
        }

    ?>

        </table>
    </div>
    <?
    $mcurrency="";
   $dcurrency="";
   if($currency_id==1)
   {
	$mcurrency='Taka';
	$dcurrency='Paisa';
   }
   if($currency_id==2)
   {
	$mcurrency='USD';
	$dcurrency='CENTS';
   }
   if($currency_id==3)
   {
	$mcurrency='EURO';
	$dcurrency='CENTS';
   }
       } ?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
        <tr>
            <th colspan="9" width="350">&nbsp </th>
            <th width="106">Grand Total</th>
            <th width="86"><? echo def_number_format($grand_fin_fab_qty_sum,2); ?></th>
            <th width="74">&nbsp </th>
            <th width="99"><? echo def_number_format($grand_amount_sum,2); ?></th>
        </tr>
    </table>
    <div style="margin-top:15px">
        <? foreach ($fabric_detail_summery as $uom_id => $summery_data_arr) {
        ?>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center; margin-top: 15px" rules="all">
        	<tr><td colspan="11" style="font-weight:bold; font-size: 16px">Report Summery</td></tr>
            <tr style="font-weight:bold">
                <td>Fabric Type</td>
                <td>Construction</td>
                <td>Composition</td>
                <td>Color Type</td>
                <td>GSM</td>
                <td>Dia/C-Width</td>
                <td>Fabric Color</td>
                <td>No. of PO</td>
                <td>Consumption(<? echo $unit_of_measurement[$uom_id] ?>)</td>
                <td>Rate(<? echo $currency[$currency_id] ?>)</td>
                <td>Amount(<? echo $currency[$currency_id] ?>)</td>
            </tr>
        <?
        foreach ($summery_data_arr as $summery_data) { ?>
            <tr>
                <td><? echo $body_part[implode(",",array_unique($summery_data['body_part_id']))] ?></td>
                <td><? echo implode(",",array_unique($summery_data['construction']))?></td>
                <td><? echo implode(",",array_unique($summery_data['composition']))?></td>
                <td><? echo $color_type[implode(",",array_unique($summery_data['c_type']))] ?></td>
                <td><? echo implode(",",array_unique($summery_data['gsm']))?></td>
                <td><? echo implode(",",array_unique($summery_data['dia'])).','.$fabric_typee[implode(",",array_unique($summery_data['dia_type']))] ?></td>
                <td><? echo implode(", ",array_unique($summery_data['fab_color'])) ?></td>
                <td><? echo count($summery_data['po_number']) ?></td>
                <td><? echo array_sum($summery_data['grey_fab_qntys']) ?></td>
                <td><? echo number_format(array_sum($summery_data['rates']),2)?></td>
                <td><? echo array_sum($summery_data['amounts']) ?></td>
            </tr>

        <? }
        }
        ?>
        </table>
    </div>

    <div style="margin-top: 10px;">
		<table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($grand_amount_sum,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($grand_amount_sum,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
	</div>
<?
$summ_main_fab_arr[2]['qty']+=$grand_fin_fab_qty_sum;
$summ_main_fab_arr[2]['amt']+=$grand_amount_sum;
 
?>

    <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px; ">
        <tr>
            <td width="50%" style="border:1px solid; border-color:#000;" valign="top">
                <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                        <th width="97%" align="left" height="30" colspan="2">Terms &amp; Condition</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");
                    if ( count($data_array)>0)
                        {
                        $i=0;
                        foreach( $data_array as $row )
                        {
                        $i++;
                        ?>
                        <tr id="settr_1" valign="top">
                            <td><? echo $i;?>)&nbsp</td>
                            <td><? echo $row[csf('terms')]; ?></td>
                        </tr>
                        <?
                        }
                        }
                    ?>
                    </tbody>
                </table>
            </td>
            <td width="50%" valign="top" style="border:1px solid; border-color:#000;">
			    <?
			     $desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
				 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_booking_no and b.entry_form=12 and is_approved=1 order by b.id asc");
				?>
				<table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
				    <thead>
				    <tr style="border:1px solid black;">
				        <th colspan="3" style="border:1px solid black;">Approval Status</th>
				        </tr>

				    </thead>
				    <tbody>
				    <?
					$s=1;
					if(count($data_array) > 0){ ?>
						<tr style="border:1px solid black;">
				        <th width="3%" style="border:1px solid black;">Sl</th><th width="25%" style="border:1px solid black;">Name</th><th width="25%" style="border:1px solid black;">Designation</th><th width="27%" style="border:1px solid black;">Approval Date</th>
				        </tr>
					<?
						foreach($data_array as $row){
						?>
				        <tr style="border:1px solid black;">
				            <td width="3%" style="border:1px solid black;"><? echo $s;?></td><td width="25%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="25%" style="border:1px solid black;"><? echo $desg_name[$row[csf('designation')]]; ?></td><td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>
				            </tr>
				            <?
							$s++;
						}
					}
					else{?>
				       <tr style="border:1px solid black;">
				       	<td colspan="3" Style="font-weight:bold; text-align:center; font-size:24px;">Draft</td>
				       </tr>
				    <? }
				    ?>
				    </tbody>
				</table>
            </td>
        </tr>
    </table>
    <? echo signature_table(121, $cbo_company_name, "1330px", 1);  
   }
   
    foreach($booking_sample_arr as $sam_booking_no) //Sample booking
	{
		$txt_booking_no="'".$sam_booking_no."'";
		$job_no="'".$booking_sample_job_arr[$sam_booking_no]."'";
		$txt_order_no_id=$booking_sample_po_arr[$sam_booking_no];
		$id_approved_id=$booking_sample_approv_arr[$sam_booking_no];
		$cbo_fabric_source=$booking_sample_source_arr[$sam_booking_no];
		$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$sample_name_name_arr=return_library_array( "select id,sample_name from    lib_sample",'id','sample_name');
	?>
	<div style="width:1330px" align="center">       
    										<!--    Header Company Information         --> 
    <?php
		$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13"); 
		
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    ?>	
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
              	 <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;"><? echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
								$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name"); 
								foreach ($nameArray as $result)
								{ 
									?>
									Plot No: <? echo $result[csf('plot_no')]; ?> 
									Level No: <? echo $result[csf('level_no')]?>
									Road No: <? echo $result[csf('road_no')]; ?> 
									Block No: <? echo $result[csf('block_no')];?> 
									City No: <? echo $result[csf('city')];?> 
									Zip Code: <? echo $result[csf('zip_code')]; ?> 
									Province No: <?php echo $result[csf('province')];?> 
									Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
									Email Address: <? echo $result[csf('email')];?> 
									Website No: <? echo $result[csf('website')];
								}
                            ?>   
                            </td> 
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">  
                            	<strong><? echo $report_title;?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                            </td> 
                            
                              <td style="font-size:20px"> 
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>
								 
								  <?
								 }
							  	?>
                             </td>
                        </tr>
                    </table>
                </td>
                 <td width="250" id="barcode_img_id"> 
                 
               </td>       
            </tr>
       </table>
		<?
        $job_no='';
        $total_set_qnty=0;
        $colar_excess_percent=0;
        $cuff_excess_percent=0;
        $nameArray=sql_select( "select a.id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.fabric_composition,a.delivery_date,a.is_apply_last_update,a.pay_mode,a.fabric_source,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.company_id='$cbo_company_name' and b.company_name='$cbo_company_name'"); //2 compnay check for sample job is FAL-15-00586 in development
        foreach ($nameArray as $result)
        {
			$total_set_qnty=$result[csf('total_set_qnty')];
			$colar_excess_percent=$result[csf('colar_excess_percent')];
			$cuff_excess_percent=$result[csf('cuff_excess_percent')];
			$po_no="";$file_no="";$ref_no="";
			$shipment_date="";
			 $sql_po= "select po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no"; 
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $row_po)
			{
				$po_no.=$row_po[csf('po_number')].", ";
				$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
				$ref_no=$row_po[csf('grouping')].",";
				$file_no=$row_po[csf('file_no')].",";
			}
      		 //$file_no= rtrim($file_no,','); $ref_no= rtrim($ref_no,',');
			$lead_time="";
			if($db_type==0)
			{
				$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			}
        
			if($db_type==2)
			{
				$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			}
			$data_array_lead_time=sql_select($sql_lead_time);
			foreach ($data_array_lead_time as $row_lead_time)
			{
				$lead_time.=$row_lead_time[csf('date_diff')].",";
				//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
			}
        
			$po_received_date="";
			$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			$data_array_po_received_date=sql_select($sql_po_received_date);
			foreach ($data_array_po_received_date as $row_po_received_date)
			{
				$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
				//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
			}
        
			$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,shiping_status from wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,shiping_status"; 
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $rows)
			{
				$daysInHand.=(datediff('d',$result[csf('delivery_date')],$rows[csf('pub_shipment_date')])-1).",";
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				$WOPreparedAfter.=(datediff('d',$rows[csf('insert_date')],$booking_date)-1).",";
				
				if($rows[csf('shiping_status')]==1)
				{
					$shiping_status.= "FP".",";
				}
				else if($rows[csf('shiping_status')]==2)
				{
					$shiping_status.= "PS".",";
				}
				else if($rows[csf('shiping_status')]==3)
				{
					$shiping_status.= "FS".",";
				}
			}
			
			$varcode_booking_no=$result[csf('booking_no')];
			if($result[csf('style_ref_no')])$style_sting.=$result[csf('style_ref_no')].'_';
        ?>
            <table width="100%" style="border:1px solid black" >                    	
                <tr>
                	<td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
                </tr>                                                
                <tr>
                    <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                    <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                    <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                    <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>	
                    <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                    <td width="110">:&nbsp;<? echo $po_qnty_tot." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                    <td width="110">:&nbsp;
                    <? 
						$gmts_item_name="";
						$gmts_item=explode(',',$result[csf('gmts_item_id')]);
						for($g=0;$g<=count($gmts_item); $g++)
						{
							$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
						}
						echo rtrim($gmts_item_name,',');
                    ?>
                    </td>
                    <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                    <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?> </b>   </td>
                </tr>
                <tr>
                    <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                    <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                    <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                    <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                    <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                    <td width="110">:&nbsp;<? 
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
					//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                    <td width="100" style="font-size:12px"><b>Fab. Delivery Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td> 
                    <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                </tr> 
                <tr>
                    <td width="100" style="font-size:12px"><b>Season</b></td>
                    <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                    <td width="100" style="font-size:12px"><b>Attention</b></td>
                    <td width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                    <td width="100" style="font-size:12px"><b>Po Received Date</b></td>
                    <td width="110" >:&nbsp;<? echo $po_received_date; ?></td>
                </tr>  
                <tr>
                    <td width="100" style="font-size:18px"><b>Order No</b></td>
                    <td width="110" style="font-size:18px" colspan="5">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
                </tr> 
                <tr>
                    <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                    <td width="110" colspan="5"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
                </tr> 
                <tr>
                    <td width="110" style="font-size:12px"><b>WO Prepared After</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($WOPreparedAfter,',').' Days' ?></td>
                    <td width="100" style="font-size:12px"><b>Ship.days in Hand</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($daysInHand,',').' Days'?></td>
                    <td width="100" style="font-size:12px"><b>Ex-factory status</b></td>
                    <td> :&nbsp;<? echo rtrim($shiping_status,','); ?></td>
                </tr> 
                 <tr>
                    <td width="110" style="font-size:12px"><b>File No</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($file_no,','); ?></td>
                    <td width="100" style="font-size:12px"><b>Internal Ref.</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($ref_no,',');?></td>
                    <td width="100" style="font-size:12px"></td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr>
                    <td width="110" style="font-size:12px"><b>Fabric Composition</b></td>
                    <td  colspan="5">: &nbsp;<? echo $result[csf('fabric_composition')]; ?></td>
                    
                </tr>
            </table> 
        <?
		}
		?>
      <br/>   									 <!--  Here will be the main portion  -->
     <style>
		 .main_table tr th{
			 border:1px solid black;
			 font-size:13px;
			 outline: 0;
		 }
		  .main_table tr td{
			 border:1px solid black;
			 font-size:13px;
			 outline: 0;
		 }
	</style>
    <?
	$costing_per="";
	$costing_per_qnty=0;
	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	if($costing_per_id==1)
	{
		$costing_per="1 Dzn";
		$costing_per_qnty=12;
	}
	if($costing_per_id==2)
	{
		$costing_per="1 Pcs";
		$costing_per_qnty=1;
	}
	if($costing_per_id==3)
	{
		$costing_per="2 Dzn";
		$costing_per_qnty=24;
	}
	if($costing_per_id==4)
	{
		$costing_per="3 Dzn";
		$costing_per_qnty=36;
	}
	if($costing_per_id==5)
	{
		$costing_per="4 Dzn";
		$costing_per_qnty=48;
	}
	//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");
			
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=18 and item_category_id=$cbo_fabric_natu and status_active=1 and is_deleted=0");

	if(str_replace("'","",$cbo_fabric_source)==1)
	{
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and	
	b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent order by a.body_part_id");
	    ?>
        <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="3" align="left">Body Part</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
                    else echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
                }
                ?>
            	<td rowspan="8" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?>)</p></td>
            	<td rowspan="8" width="50"><p>Process Loss %</p></td>
            </tr> 
            <tr align="center"><th colspan="3" align="left">Color Type</th>
                <? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
					else echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
                }
                ?>
            </tr>  
            <tr align="center"><th colspan="3" align="left">Construction</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
					else echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";			
                }
                ?>
            </tr>       
            <tr align="center"><th   colspan="3" align="left">Composition</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
					else echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">GSM</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
					else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
                    else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">Process Loss%</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='2'>&nbsp</td>";
					else echo "<td align='center' colspan='2'>". $result_fabric_description[csf('process_loss_percent')]."</td>";			
                }
                ?>
            </tr>
            <tr>
                <th  width="100" align="left">Sample Name</th>
                <th  width="100" align="left">Fabric Color</th>
                <th  width="100" align="left">Lapdip No</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                    echo "<th width='50'>Finish</th><th width='50' >Gray</th>";			
                }
                ?>
            </tr>
       		<?
	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			if($db_type==0) $sample_type_id="group_concat(sample_type)";
			else if($db_type==2) $sample_type_id="listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type)";
			
			$color_wise_wo_sql=sql_select("select job_no, fabric_color_id, $sample_type_id as sample_type
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by job_no, fabric_color_id");
			foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				?> 
				<tr>
                    <td  width="100" align="left">
						<?
                        $sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                   		echo $sample_type_val; 
                    ?></td>
                    <td  width="100" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                        ?>
                    </td>
                    <td  width="100" align="left">
						<? 
                        $lapdip_no="";
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."","lapdip_no");
                        if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
                        ?>
                    </td>
                    <?
                    $total_fin_fab_qnty=0;$total_fin_fab_amt=0;
                    $total_grey_fab_qnty=0;
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if($db_type==0)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															  FROM 
															  wo_pre_cost_fabric_cost_dtls a,
															  wo_booking_dtls b 
															  WHERE 
															  b.booking_no =$txt_booking_no  and
															  a.id=b.pre_cost_fabric_cost_dtls_id and
															  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															  a.construction='".$result_fabric_description[csf('construction')]."' and 
															  a.composition='".$result_fabric_description[csf('composition')]."' and 
															  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															  b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and 
															  b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
															  b.status_active=1 and
															  b.is_deleted=0");
						}
						if($db_type==2)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.amount) as amt
															  FROM 
															  wo_pre_cost_fabric_cost_dtls a,
															  wo_booking_dtls b 
															  WHERE 
															  b.booking_no =$txt_booking_no  and
															  a.id=b.pre_cost_fabric_cost_dtls_id and
															  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															  nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and 
															  nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
															  b.status_active=1 and
															  b.is_deleted=0");
						}
                    	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                    ?>
                    <td width='50' align='right'>
						<? 
                        if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
                        {
							echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
							$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
							$total_fin_fab_amt+=$color_wise_wo_result_qnty[csf('amt')];
                        }
                        ?>
                    </td>
                    <td width='50' align='right' > 
						<? 
                        if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                        {
							echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2); 
							$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                        }
                        ?>
                    </td>
                    <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                    <td align="right">
                    <?
                    
                    if($process_loss_method==1)
                    {
                   		$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
                    }
                    
                    if($process_loss_method==2)
                    {
						//$devided_val = 1-(($total_grey_fab_qnty-$total_fin_fab_qnty)/100);
						//$process_percent=$total_grey_fab_qnty/$devided_val;
						$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
                    }
                    echo number_format($process_percent,2);
                    ?>
                    </td>
				</tr>
				<?
			}
			?>
			<tr>
				<td  width="120" align="left">&nbsp;</td>
				<td  width="120" align="left">&nbsp;</td>
				<td  width="120" align="left"><strong>Total</strong></td>
				<?
				foreach($nameArray_fabric_description as $result_fabric_description)
				{
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															a.construction='".$result_fabric_description[csf('construction')]."' and 
															a.composition='".$result_fabric_description[csf('composition')]."' and 
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.status_active=1 and
															b.is_deleted=0");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.amount) as amt
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															b.status_active=1 and
															b.is_deleted=0");
					}
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
				<?
				}
				?>
				<td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
				<td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
				<td align="right">
				<?
					if($process_loss_method==1)
					{
						$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
					}
					if($process_loss_method==2)
					{
						$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
					}
					echo number_format($totalprocess_percent,2);
					?>
				</td>
			</tr> 
        </table>
        <br/>
        <?
		$summ_main_fab_arr[3]['qty']+=$grand_total_grey_fab_qnty;
		$summ_main_fab_arr[3]['amt']+=$total_fin_fab_amt;
	 }
	 //echo  $cbo_fabric_source;
	if(str_replace("'","",$cbo_fabric_source)==2)
	{
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and	
		b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent order by a.body_part_id");
		?>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="3" align="left">Body Part</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
                }
                ?>
                <td rowspan="8" width="50"><p>Total  Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
                <td rowspan="8" width="50"><p>Avg. Rate</p></td>
                <td rowspan="8" width="50"><p>Amount</p></td>
            </tr> 
            <tr align="center"><th colspan="3" align="left">Color Type</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
                }
                ?>
            </tr>  
            <tr align="center"><th colspan="3" align="left">Construction</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";			
                }
                ?>
            </tr>       
            <tr align="center"><th   colspan="3" align="left">Composition</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
					else echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">GSM</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">Process Loss%</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td align='center' colspan='3'>". $result_fabric_description[csf('process_loss_percent')]."</td>";			
                }
                ?>
            </tr>
            <tr>
                <th  width="100" align="left">Sample Name</th>
                <th  width="100" align="left">Fabric Color</th>
                <th  width="100" align="left">Lapdip No</th>
                <? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
               		echo "<th width='50'>Fab Qty</th><th width='50'>Rate</th><th width='50'>Amount</th>";			
                }
                ?>
            </tr>
            <?
            $grand_total_fin_fab_qnty=0;
            $grand_total_grey_fab_qnty=0;
            $grand_totalcons_per_finish=0;
            $grand_totalcons_per_grey=0;
            if($db_type==0) $sample_type_id="group_concat(sample_type)";
            else if($db_type==2) $sample_type_id="listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type)";
            
            $color_wise_wo_sql=sql_select("select job_no, fabric_color_id, $sample_type_id as sample_type
											FROM 
											wo_booking_dtls
											WHERE 
											booking_no =$txt_booking_no and
											status_active=1 and
											is_deleted=0
											group by job_no, fabric_color_id");
            foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				?> 
				<tr>
                    <td  width="100" align="left">
						<?
                        $sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                        echo $sample_type_val; 
                    ?></td>
                    <td width="100" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                        ?>
                    </td>
                    <td width="100" align="left">
						<? 
                        $lapdip_no="";
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."","lapdip_no");
                        if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
                        ?>
                    </td>
                    <?
                    $total_fin_fab_qnty=0;
                    $total_grey_fab_qnty=0;
                    $total_amount=0;
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if($db_type==0)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
																FROM 
																wo_pre_cost_fabric_cost_dtls a,
																wo_booking_dtls b 
																WHERE 
																b.booking_no =$txt_booking_no  and
																a.id=b.pre_cost_fabric_cost_dtls_id and
																a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
																a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
																a.construction='".$result_fabric_description[csf('construction')]."' and 
																a.composition='".$result_fabric_description[csf('composition')]."' and 
																a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
																b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
																b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and 
																b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
																b.status_active=1 and
																b.is_deleted=0");
						}
						if($db_type==2)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
																FROM 
																wo_pre_cost_fabric_cost_dtls a,
																wo_booking_dtls b 
																WHERE 
																b.booking_no =$txt_booking_no  and
																a.id=b.pre_cost_fabric_cost_dtls_id and
																nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
																nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
																nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
																nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
																nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
																nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
																nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and 
																nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
																b.status_active=1 and
																b.is_deleted=0");
						}
						list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
						?>
						<td width='50' align='right' > 
							<? 
                            if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2); 
								$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                            }
                            ?>
						</td>
						<td width='50' align='right'>
							<? 
                            if($color_wise_wo_result_qnty[csf('rate')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('rate')],2) ;
								//$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                            }
                            ?>
						</td>
						<td width='50' align='right'>
							<? 
                            if($color_wise_wo_result_qnty[csf('amount')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('amount')],2) ;
								//$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                            }
                            ?>
						</td>
						<?
						$total_amount+=$color_wise_wo_result_qnty[csf('amount')];
                    }
                    ?>
                    <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_amount/$total_grey_fab_qnty,2); //$grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_amount,2); $grand_total_amount+=$total_amount;?></td>
				</tr>
				<?
			}
            ?>
            <tr>
                <td width="100" align="left">&nbsp;</td>
                <td width="100" align="left">&nbsp;</td>
                <td width="100" align="left"><strong>Total</strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															a.construction='".$result_fabric_description[csf('construction')]."' and 
															a.composition='".$result_fabric_description[csf('composition')]."' and 
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.status_active=1 and
															b.is_deleted=0");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															b.status_active=1 and
															b.is_deleted=0");
					}
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('rate')],2) ;?></td>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('amount')],2) ;?></td>
					<?
                }
                ?>
                <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
                <td align="right"><? echo number_format($grand_total_amount/$grand_total_grey_fab_qnty,2);?></td>
                <td align="right"><? echo number_format($grand_total_amount,2);?></td>
            </tr> 
		</table>
		<br/>
	<?
	$summ_main_fab_arr[3]['qty']+=$grand_total_grey_fab_qnty;
	$summ_main_fab_arr[3]['amt']+=$grand_total_amount;
	}
	if(str_replace("'","",$cbo_fabric_source)==1)
	{
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');			
		//$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		//echo "SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id order by po_break_down_id";
		
		$yarn_sql_array=sql_select("SELECT a.fabric_cost_dtls_id,min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id,sum(b.grey_fab_qnty) as grey_fab_qnty, a.cons_ratio from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id,a.fabric_cost_dtls_id, a.cons_ratio order by po_break_down_id ");
		?>
		<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr align="center">
                        	<td colspan="7"><b>Yarn Required Summary</b></td>
                        </tr>
                        <tr align="center">
                            <td>Sl</td>
                            <td>PO</td>
                            <td>Yarn Description</td>
                            <td>Brand</td>
                            <td>Lot</td>
								<?
                                if($show_yarn_rate==1)
                                {
									?>
									<td>Rate</td>
									<?
                                }
                                ?>
                            <td>Cons for <? echo $costing_per; ?> Gmts</td>
                            <td>Total (KG)</td>
                        </tr>
                        <?
                        $i=0;
                        $total_yarn=0;
                        foreach($yarn_sql_array  as $row)
                        {
							$i++;
							?>
							<tr align="center">
                                <td><? echo $i; ?></td>
                                 <td><? echo $po_number[$row[csf('po_break_down_id')]]; ?></td>
                                <td>
									<?
                                    $yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
                                    if($row['copm_two_id'] !=0)
                                    {
                                    	$yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
                                    }
                                    $yarn_des.=$yarn_type[$row[csf('type_id')]];
                                    //echo $yarn_count_arr[$row['count_id']]." ".$composition[$row['copm_one_id']]." ".$row['percent_one']."%  ".$composition[$row['copm_two_id']]." ".$row['percent_two']."%  ".$yarn_type[$row['type_id']]; 
                                    echo $yarn_des;
                                    ?>
                                </td>
                                <td></td>
                                <td></td>
									<?
                                    if($show_yarn_rate==1)
                                    {
                                    ?>
                                    	<td><? echo number_format($row[csf('rate')],4); ?></td>
                                    <?
                                    }
                                    ?>
                                <td><? echo number_format($row[csf('yarn_required')],4); ?></td>
                                <!--<td><? //echo number_format(($row['yarn_required']/$po_qnty_tot)*$costing_per_qnty,2); ?></td>-->
                                <td align="right"><? $yarn=($row[csf('grey_fab_qnty')]*$row[csf('cons_ratio')])/100; echo number_format($yarn,2); $total_yarn+=$yarn; ?></td>
							</tr>
							<?
                        }
                        ?>
                        <tr align="center">
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
								<?
                                if($show_yarn_rate==1)
                                {
									?>
									<td></td>
									<?
                                }
                                ?>
                            <td></td>
                            <td align="right"><? echo number_format($total_yarn,2); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <?
                $yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
                if(count($yarn_sql_array)>0)
                {
					?>
					<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr align="center">
                        	<td colspan="7"><b>Allocated Yarn</b></td>
                        </tr>
                        <tr align="center">
                            <td>Sl</td>
                            <td>Yarn Description</td>
                            <td>Brand</td>
                            <td>Lot</td>
                            <td>Allocated Qty (Kg)</td>
                        </tr>
                        <?
                        $total_allo=0;
                        $item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
                        $supplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                        //$yarn_sql_array=sql_select("SELECT a.item_id, a.qnty,b.supplier_id,b.lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0");
                        $i=0;
                        $total_yarn=0;
                        foreach($yarn_sql_array  as $row)
                        {
							$i++;
							?>
							<tr align="center">
                                <td><? echo $i; ?></td>
                                <td><? echo $item[$row[csf('item_id')]]; ?></td>
                                <td><? echo $supplier[$row[csf('supplier_id')]]; ?></td>
                                <td><? echo $row[csf('lot')]; ?></td>
                                <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
							</tr>
							<?
                        }
                        ?>
                        <tr align="center">
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"><? echo number_format($total_allo,4); ?></td>
                        </tr>
					</table>
					<?
                }
                else
                {
					$is_yarn_allocated=return_field_value("allocation","variable_settings_inventory","company_name=$cbo_company_name and variable_list=18 and item_category_id=1"); 
					if($is_yarn_allocated==1)
					{
						?>
						<font style=" font-size:30px"><b>Draft</b></font>
						<?
					}
					else
					{
						echo "";
					}
                }
                ?>
                </td>
            </tr>
		</table>
		<?
	}
		?>
        <br/>


        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
              
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="4"><b>Approval Status</b></td>
                    
                    </tr>

                    <tr>
                    	    <th width="30">Sl   <? $bookingId=$nameArray[0][csf('id')]?> </th>
                            <th width="250">Name/Designation</th>
                            <th width="150">Approval Date</th>
                            <th width="80">Approval No</th>
                    </tr>
                     
                    <?
                    $user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
                    $desg_arr=return_library_array( "select id, designation from user_passwd", "id", "designation"  );
 					$desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
 					$sel=sql_select("select approved_by,approved_no,approved_date from approval_history where mst_id in(select id from wo_booking_mst where id=$bookingId) and entry_form=13 ");
					$i=1;
					foreach ($sel as $rows) {
                    
					?>

					<tr id="settr_1" align="">
                                    <td width="30"><? echo $i ?></td>
                                    <td width="250"><? echo $user_arr[$rows[csf('approved_by')]]." /".$desg_name[$desg_arr[$rows[csf('approved_by')]]] ?></td>
                                    <td width="150"><? echo $rows[csf('approved_date')] ?></td>
                                    <td width="80"><? echo $rows[csf('approved_no')] ?></td>
                                     
                                </tr>
                                <?
                                $i++;

                            }
                            ?>
                    
                    
                </table>
                   
                </td>
            </tr>
        </table>
        <br/>



        <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>
                    
                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {
						$i++;
						?>
						<tr align="center">
                            <td><? echo $i; ?></td>
                            <td><? echo $emblishment_name_array[$row_embelishment[csf('emb_name')]]; ?></td>
                            <td>
								<?
                                if($row_embelishment[csf('emb_name')]==1)
                                {
                                echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
                                }
                                if($row_embelishment[csf('emb_name')]==2)
                                {
                                echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
                                }
                                if($row_embelishment[csf('emb_name')]==3)
                                {
                                echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
                                }
                                if($row_embelishment[csf('emb_name')]==4)
                                {
                                echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
                                }
                                if($row_embelishment[csf('emb_name')]==5)
                                {
                                echo $row_embelishment[csf('emb_type')];
                                }
                            	?>
                            </td>
                            <td><? echo $row_embelishment[csf('cons_dzn_gmts')]; ?></td>
                            <td><? echo $row_embelishment[csf('rate')]; ?></td>
                            <td><? echo $row_embelishment[csf('amount')]; ?></td>
						</tr>
						<?
					}
					?>
                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td><b>Approved Instructions</b></td>
                    
                    </tr>
                    <tr>
                    <td>
                <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                </td>
                </tr>
                </table>
                   
                </td>
            </tr>
        </table>
        <br/>


 		<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <table align="left" cellspacing="0" width="<? echo $width?>"  border="1" rules="all" class="rpt_table" >
            
            <?
				   $sql_req=("select gmts_color_id as gmts_color,gmts_size,sum(bh_qty) as bh_qty,sum(rf_qty) as rf_qty  FROM wo_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and gmts_size!=0 and bh_qty>0 group by gmts_color_id,gmts_size  order by gmts_size"); 
				$sql_data =sql_select($sql_req);
				$size_array=array();$qnty_array_bh=array();$qnty_array_rf=array();
				foreach($sql_data as $row)
				{
					$size_array[$row[csf('gmts_size')]]=$row[csf('gmts_size')];
					$qnty_array_bh[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')];
					$qnty_array_rf[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('rf_qty')];
					$qnty_array[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')]+$row[csf('rf_qty')];
				}
				 $sql_color=("select gmts_color_id as gmts_color,sum(bh_qty) as bh_qty  FROM wo_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and gmts_size!=0 and bh_qty>0 group by gmts_color_id  order by gmts_color"); 
				$sql_data_color =sql_select($sql_color);
				$color_array=array();
				foreach($sql_data_color as $row)
				{
					$color_array[$row[csf('gmts_color')]]=$row[csf('gmts_color')];
				}
				 $sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
				 $colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
				 $width=400+(count($size_array)*150);
				 //count($size_array);
				 ?>
            
            
		        <thead align="center">
		         <tr>
		           		 <th align="left" colspan="<? echo count($size_array)+5;?>" width="30"><strong>Sample Requirement</strong></th>
		           </tr>
		            <tr>
		            <th width="30" rowspan="2">SL</th>
		            <th width="80" rowspan="2" align="center">Color/Size</th>
		            <?
		            foreach ($size_array as $sizid)
		            {
		            //$size_count=count($sizid);
		            ?>
		            <th width="" colspan="2"><strong><? echo  $sizearr[$sizid];  ?></strong>
		            </th>
		            
		            <?
		            } ?>
		           <th width="80" rowspan="2" align="center">Total Qnty.</th>
		            </tr>
		            <tr>
		             <?
		            foreach ($size_array as $sizid)
		            {
		            //$size_count=count($sizid);
		            ?>
		            <th width="75"> BH &nbsp;</th> <th width="75"> Rf.&nbsp;</th>
		            <?
		            } ?>
		            </tr>
		        </thead>
		        <tbody>
					<?
		            //$mrr_no=$dataArray[0][csf('issue_number')];
		            $i=1;
		            $tot_qnty=array();
		                foreach($color_array as $cid)
		                {
		                    if ($i%2==0)  
		                        $bgcolor="#E9F3FF";
		                    else
		                        $bgcolor="#FFFFFF";
							$color_count=count($cid);
		                    ?>
		                    <tr>
		                        <td><? echo $i;  ?></td>
		                        <td><? echo $colorarr[$cid]; ?></td>
		                       
		                         <?
								foreach ($size_array as $sizval)
								{
								//$size_count=count($sizid);
								$tot_qnty[$cid]+=$qnty_array[$cid][$sizval];
								$tot_qnty_size_bh[$sizval]+=$qnty_array_bh[$cid][$sizval];
								$tot_qnty_size_rf[$sizval]+=$qnty_array_rf[$cid][$sizval];
								?>
								<td width="75" align="right"> <? echo $qnty_array_bh[$cid][$sizval]; ?></td> <td width="75" align="right"> <? echo $qnty_array_rf[$cid][$sizval]; ?></td>
								<?
								
								} ?>
											
		                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
		                    </tr>
		                    <?
							$production_quantity+=$tot_qnty[$cid];
							$i++;
		                }
		            ?>
		        </tbody>
		        <tr>
		            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
		            <?
						foreach ($size_array as $sizval)
						{
							?>
		                    <td align="right"><?php echo $tot_qnty_size_bh[$sizval]; ?></td>
		                    <td align="right"><?php echo $tot_qnty_size_rf[$sizval]; ?></td>
		                    <?
						}
					?>
		            <td align="right"><?php echo $production_quantity; ?></td>
		        </tr>                           
		    </table>
       		<br>
        </table>



        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                	<thead>
                    	<tr>
                        	<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top"><? echo $i;?></td>
                                    <td><strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong></td>
                                </tr>
                            <?
						}
					}
					/*else
					{
				    $i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td valign="top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row['terms']; ?>
                                    </td>
                                    
                                </tr>
                    <? 
						}
					} */
					?>
                </tbody>
                </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
                <?
                if(str_replace("'","",$cbo_fabric_source)==1)
                {
					?>
                   <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                   <tr align="center">
                    <td colspan="10"><b>Comments</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Po NO</td>
                    <td>Ship Date</td>
                    <td>Pre-Cost Qty</td>
                    <td>Mn.Book Qty</td>
                    <td>Sht.Book Qty</td>
                    <td>Smp.Book Qty</td>
                    <td>Tot.Book Qty</td>
                    <td>Balance</td>
                    <td>Comments</td>
                    </tr>
                    <?
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_mst_id,size_mst_id,item_mst_id", "id", "plan_cut_qnty");
	
	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_po_break_down c
WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id=c.id and
	b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by a.id");
	$count=0;
	$tot_grey_req_as_pre_cost_arr=array();
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	                $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;
					/*$booking_qnty=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type in(1,4)  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");*/
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					//echo "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id";
					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id");
					foreach($sql_data  as $row)
                    {
					$col++;
					?>
                    <tr align="center">
                    <td><? echo $col; ?></td>
                    <td><? echo $row[csf("po_number")]; ?></td>
                     <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
                    <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
                    <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
                    <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
                    <td align="right">
					<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
                    </td>
                    <td>
					<? 
					if( $balance>0)
					{
						echo "Less Booking";
					}
					else if ($balance<0) 
					{
						echo "Over Booking";
					} 
					else
					{
						echo "";
					}
					?>
                    </td>
                    </tr>
                    <?
					}
					?>
                    <tfoot>
                    
                    <tr>
                    <td colspan="3">Total:</td>
                    
                    <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
                     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
                    <td align="right"><? echo number_format($tot_balance,2); ?></td>
                    <td></td>
                    </tr>
                    </tfoot>
                    </table>
                    <?
				}
					?>
                </td>
                
            </tr>
        </table>
        
        
          <?
		 	echo signature_table(5, $cbo_company_name, "1330px");
	}
	
	foreach($req_no_arr as $mst_id=>$req_no) //Req  with booking
	{  
		 
             
              //  extract($_REQUEST);
                //$data=explode('*',$data);
				//$data[0]=$cbo_company_name;
				$req_no_booking=$req_no_booking_arr[$mst_id];
                $cbo_template_id=1;
                $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
                $supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
                $company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$cbo_company_name' and form_name='company_details' and is_deleted=0 and file_type=1");
                $buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
                $dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
                $team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
            
                $sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
                $size_library=return_library_array( "select id, size_name from lib_size where status_active =1 and is_deleted=0 ", "id", "size_name");
                $color_library=return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0 ", "id", "color_name");
                $season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name");
                $brandArr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
                $trims_group_lib=return_library_array( "select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
                //concate(buyer_name,'_',contact_person)
                $appDate=return_field_value("approved_date","approval_history","entry_form=25 and mst_id='$mst_id' order by id desc");
                $appBy=return_field_value("approved_by","approval_history","entry_form=25 and mst_id='$mst_id'");
                $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
                $imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
             ?>
                <style>
                   /* #mstDiv {
                        margin:0px auto;
                        width:1130px;
                    }
                    #mstDiv @media print {
                        thead {display: table-header-group;}
                    }
                    @media print{
                        html>body table.rpt_table {
                            margin-left:2px;
                        }
                    }*/
                </style>
                <div id="mstDiv">
                    <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;" >
                        <tr>
                            <td rowspan="4" valign="top" width="300"><img width="150" height="80" src="../../../<? echo $company_img[0][csf("image_location")]; ?>" ></td>
                            <td colspan="4" style="font-size:20px;"><strong><b><? echo $company_library[$cbo_company_name]; ?></b></strong></td>
                            <td width="200">
                                <?
                                $nameArray_approved=sql_select( "SELECT approved_by,approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no='$req_no_booking' and b.entry_form=9 and a.status_active =1 and a.is_deleted=0 order by b.id desc ");
                                $approved_by= $user_arr[$nameArray_approved[0][csf("approved_by")]];
                                $approved_date= change_date_format($nameArray_approved[0][csf("approved_date")]);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <?
                                $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                                echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                                echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                                echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                                echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                                echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                                echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                                echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                                echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                                echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                                echo($val[0][csf('website')])?    $val[0][csf('website')]: "";
            
                                $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id from sample_development_mst where  id='$mst_id' and entry_form_id=449 and is_deleted=0 and status_active=1";
                                $dataArray=sql_select($sql);
                                $barcode_no=$dataArray[0][csf('requisition_number')];
                                $quotation_id=$dataArray[0][csf("quotation_id")];
                                if($dataArray[0][csf("sample_stage_id")]==1)
                                {
                                    $job_lib=return_library_array( "SELECT a.id,min(b.shipment_date) as shipment_date  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$cbo_company_name' GROUP BY a.id", "id", "shipment_date");
                                }
                                if($dataArray[0][csf("sample_stage_id")]==2)
                                {
                                   $bodywashcolor=return_field_value("color","wo_quotation_inquery","id='$quotation_id'");
                                }
                                $sqls="SELECT style_desc, supplier_id, revised_no,team_leader, buyer_req_no, source, booking_date, attention from wo_non_ord_samp_booking_mst where booking_no='$req_no_booking' and is_deleted=0 and status_active=1";
                                $dataArray_book=sql_select($sqls);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="font-size:medium"><strong style="font-size:18px"> <u>Sample Program Without Order</u></strong></td>
                            <td colspan="2" width="250"><b>Approved By :<?=$approved_by ?></b> </br><b>Approved Date :<?=$approved_date ?></b> </td>
                        </tr>
                    </table>
            
                    <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;" >
                        <tr>
                            <td width="130"><strong>System No.: </strong></td>
                            <td width="130"><strong><?=$dataArray[0][csf("requisition_number")];?></strong></td>
                            <td width="130"><strong>Booking Date:</strong></td>
                            <td width="130"><?=$dataArray_book[0][csf('booking_date')];?></td>
                            <td width="130"><strong>Sample Stage:</strong></td>
                            <td width="130"><?=$sample_stage[$dataArray[0][csf('sample_stage_id')]];?></td>
                            <td width="130"><strong>Revise:</strong></td>
                            <td><?=$dataArray_book[0][csf('revised_no')];?></td>
                        </tr>
                        <tr>
                            <td><strong>W/O No: </strong></td>
                            <td><?=$data[2];?></td>
                            <td><strong>Style Ref:</strong></td>
                            <td><?=$dataArray[0][csf('style_ref_no')];?></td>
                            <td><strong>Style Desc.:</strong></td>
                            <td><?=$dataArray_book[0][csf('style_desc')];?></td>
                            <td><strong>Sample Sub Date:</strong></td>
                            <td><?=change_date_format($dataArray[0][csf('material_delivery_date')]);?></td>
                        </tr>
                        <tr>
                            <td><strong>Buyer Name: </strong></td>
                            <td><?=$buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
                            <td><strong>Season:</strong></td>
                            <td><?=$season_arr[$dataArray[0][csf('season_buyer_wise')]];?></td>
                            <td><strong>Season Year: </strong></td>
                            <td><?=$dataArray[0][csf('season_year')];?></td>
                            <td><strong>Brand:</strong></td>
                            <td><?=$brandArr[$dataArray[0][csf('brand_id')]];?></td>
                        </tr>
                        <tr>
                            <td><strong>BH Merchant:</strong></td>
                            <td><?=$dataArray[0][csf('bh_merchant')];?></td>
                            <td><strong>Attention:</strong></td>
                            <td style="word-wrap: break-word;word-break: break-all;" ><?=$dataArray_book[0][csf('attention')];?></td>
                            <td><strong>Buyer Ref:</strong></td>
                            <td><?=$dataArray[0][csf('buyer_ref')];?></td>
                            <td><strong>Product Dept:</strong></td>
                            <td><?=$product_dept[$dataArray[0][csf('product_dept')]];?></td>
                        </tr>
                        <tr>
                            <td><strong>Supplier:</strong></td>
                            <td><?=$supplier_library[$dataArray_book[0][csf('supplier_id')]];?></td>
                            <td><strong>Est. Ship Date:</strong></td>
                            <td><?=change_date_format($dataArray[0][csf('estimated_shipdate')]);?></td>
                            <td><strong>Team Leader:</strong></td>
                            <td><?=$team_leader_arr[$dataArray_book[0][csf('team_leader')]];?></td>
                            <td title="Booking "><strong>Dealing Merchant:</strong></td>
                            <td><?=$dealing_merchant_library[$dataArray[0][csf('dealing_marchant')]];?></td>
                        </tr>
                        <tr>
                            <td><strong>Body/Wash Color:</strong></td>
                            <td><?=$bodywashcolor; ?></td>
                            <td><strong>Remarks/Desc:</strong></td>
                            <td colspan="5" style="word-wrap: break-word;word-break: break-all;"><?=$dataArray[0][csf('remarks')];?></td>
                        </tr>
                    </table>
                    <br>
                    <?
                    $sql_fab="SELECT a.sample_name, a.process_loss_percent, a.gmts_item_id, b.color_id, b.contrast, b.qnty, a.delivery_date, a.fabric_description, a.determination_id, a.body_part_id, a.fabric_source, a.remarks_ra, a.gsm, a.dia, a.color_type_id, a.width_dia_id, a.uom_id, b.process_loss_percent, a.weight_type, a.cuttable_width, b.grey_fab_qnty, b.fabric_color from sample_development_fabric_acc a, sample_development_rf_color b, wo_non_ord_samp_booking_dtls c where a.id=b.dtls_id and a.sample_mst_id=b.mst_id and a.id=c.dtls_id and c.fabric_color=b.fabric_color and c.gmts_color=b.color_id and c.dtls_id=b.dtls_id  and b.grey_fab_qnty=c.grey_fabric and a.form_type=1 and b.qnty>0 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id='$mst_id' and b.mst_id='$mst_id'  ";
                    //echo $sql_fab; die;req_no_booking
                    $sql_fab_arr=array(); $determination_id='';
                    foreach(sql_select($sql_fab) as $vals)
                    {
                        $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["qnty"]+=$vals[csf("qnty")];
                        
                        $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["exdata"]=change_date_format($vals[csf("delivery_date")]).'__'.$vals[csf("fabric_source")].'__'.$vals[csf("uom_id")].'__'.$vals[csf("width_dia_id")].'__'.$vals[csf("remarks_ra")].'__'.$vals[csf("color_type_id")].'__'.$vals[csf("weight_type")].'__'.$vals[csf("cuttable_width")].'__'.$vals[csf("determination_id")];
            
                        $sql_fab_arr[$vals[csf("sample_name")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("fabric_description")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("contrast")]]["grey_fab_qnty"]+=$vals[csf("grey_fab_qnty")];
                        if($determination_id=="") $determination_id=$vals[csf("determination_id")]; else $determination_id.=','.$vals[csf("determination_id")];
                    }
                    $sample_item_wise_span=array();
                    
                    $sqlRd="select id, fabric_ref, rd_no from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0 and id in ($determination_id)";
                    $sqlRdData=sql_select($sqlRd); $rdRefArr=array();
                    foreach($sqlRdData as $row)
                    {
                        $rdRefArr[$row[csf("id")]]['ref']=$row[csf("fabric_ref")];
                        $rdRefArr[$row[csf("id")]]['rd_no']=$row[csf("rd_no")];
                    }
                    
                    /*echo '<pre>';
                    print_r($sql_fab_arr); die;*/
            
                    foreach($sql_fab_arr as $sample_type=>$colorType_data)
                    {
                        foreach($colorType_data as $colorType=>$gmts_color_data)
                        {
            
                            foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                            {
                                $sample_span=0;
                                foreach($body_part_data as $body_part_id=>$fab_desc_data)
                                {
                                    //$kk=0;
                                    foreach($fab_desc_data as $fab_id=>$gsm_data)
                                    {
                                        foreach($gsm_data as $gsm_id=>$dia_data)
                                        {
                                            foreach($dia_data as $dia_id=>$color_data)
                                            {
                                                foreach($color_data as $contrast_id=>$row)
                                                {
                                                    $sample_span++;
                                                    //$kk++;
                                                }
                                            }
                                        }
                                    }
                                    //$bodypart_item_wise_span[$sample_type][$gmts_item_id][$body_part_id]=$kk;
                                }
                                $sample_item_wise_span[$sample_type][$gmts_color_id]=$sample_span;
                            }
                        }
                    }
            /*        echo "<pre>";
                    print_r($sample_item_wise_span);die;*/
            
                    $sql_sample_dtls= "SELECT a.sample_name, a.article_no, a.sample_color from sample_development_dtls a, lib_color b where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=449  and sample_mst_id='$mst_id' and b.status_active=1 and b.id=a.sample_color  group by a.sample_name, a.article_no, a.sample_color";
                    foreach(sql_select($sql_sample_dtls) as $key=>$value)
                    {
                        if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=="")
                        {
                            $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]]=$value[csf("article_no")];
                        }
                        else
                        {
                            if(!in_array($value[csf("article_no")], $sample_wise_article_no))
                            {
                                $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]].= ', '.$value[csf("article_no")];
                            }
                        }
                    }
                    // echo "<pre>"; print_r($sample_wise_article_no);die;
            
                    ?>
                    <table class="rpt_table" width="1460"  border="1" cellpadding="0" cellspacing="0" rules="all">
                        <thead>
                            <tr>
                                <th colspan="19">Required Fabric</th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th width="110">Sample Type</th>
                                <th width="80">Fab. Deli Date</th>
                                <th width="40">Fabric Source</th>
                                <th width="120">Body Part</th>
                                <th width="80">RD No</th>
                                <th width="80">Ref. No</th>
                                <th width="200">Fabric Desc & Composition</th>
                                <th width="80">Color Type</th>
                                <th width="80">Gmt. Color</th>
                                <th width="80">Fab. Color</th>
                                <th width="55">Fabric Weight</th>
                                <th width="55">F.Weight Type</th>
                                <th width="60">Full Width</th>
                                
                                <th width="55">Cuttable Width</th>
                                <th width="55">Width Type</th>
                                
                                <th width="40">UOM</th>
                                <th width="60">Fin Fabric Qty</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $p=1; $total_finish=0; 
                            foreach($sql_fab_arr as $sample_type=>$colorType_data)
                            {
                                foreach($colorType_data as $colorType=>$gmts_color_data)
                                {
                                    foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
                                    {
                                        $nn=0;
                                        foreach($body_part_data as $body_part_id=>$fab_desc_data)
                                        {
                                            foreach($fab_desc_data as $fab_id=>$gsm_data)
                                            {
                                                foreach($gsm_data as $gsm_id=>$dia_data)
                                                {
                                                    foreach($dia_data as $dia_id=>$color_data)
                                                    {
                                                        //$i=0;
                                                        foreach($color_data as $contrast_id=>$value)
                                                        {
                                                            $exData=explode("__",$value["exdata"]);
                                                            $delivery_date=$fabricSource=$uom_id=$width_dia_id=$remarks_ra=$color_type_id=$weight_type=$cuttable_width=$determination_id='';
                                                            
                                                            $delivery_date=$exData[0];
                                                            $fabricSource=$exData[1];
                                                            $uom_id=$exData[2];
                                                            $width_dia_id=$exData[3];
                                                            $remarks_ra=$exData[4];
                                                            $color_type_id=$exData[5];
                                                            $weight_type=$exData[6];
                                                            $cuttable_width=$exData[7];
                                                            $determination_id=$exData[8];
                                                            
                                                            ?>
                                                            <tr>
                                                                <td align="center" style="word-wrap: break-word;word-break: break-all;"><?=$p;$p++;?></td>
                                                                <?
                                                               /* if($nn==0)
                                                                {*/
                                                                    $rowspan=0;
                                                                    //$rowspan=$sample_item_wise_span[$sample_type][$gmts_color_id];
                                                                    ?>
                                                                    <td rowspan="<?=$rowspan;?>" align="center"><?=$sample_library[$sample_type];?></td>
                                                                    
                                                                    <?
                                                                    $nn++;
                                                                /*}*/
                                                                ?>
                                                                <td align="center"><?=$delivery_date; ?> </td>
                                                                <td style="word-break:break-all"><?=$fabric_source[$fabricSource]; ?></td>
                                                                <td style="word-break:break-all"><?=$body_part[$body_part_id]; ?></td>
                                                                <td style="word-break:break-all"><?=$rdRefArr[$determination_id]['rd_no']; ?></td>
                                                                <td style="word-break:break-all"><?=$rdRefArr[$determination_id]['ref']; ?></td>
                                                                <td style="word-break:break-all"><?=$fab_id;?></td>
                                                                
                                                                <td style="word-break:break-all"><?=$color_type[$colorType]; ?></td>
                                                                <td style="word-break:break-all"><?=$color_library[$gmts_color_id]; ?></td>
                                                                <td style="word-break:break-all"><?=$contrast_id; ?></td>
                                                                <td style="word-break:break-all"><?=$gsm_id; ?></td>
                                                                <td style="word-break:break-all"><?=$fabric_weight_type[$weight_type]; ?></td>
                                                                <td style="word-break:break-all"><?=$dia_id; ?></td>
                                                                <td style="word-break:break-all"><?=$cuttable_width; ?></td>
                                                                <td style="word-break:break-all"><?=$fabric_typee[$width_dia_id]; ?></td>
                                                                <td style="word-break:break-all"><?=$unit_of_measurement[$uom_id]; ?></td>
                                                                <td align="right"><?=number_format($value["qnty"], 2); ?></td>
                                                                <td style="word-break:break-all"><?=$remarks_ra; ?></td>
                                                            </tr>
                                                            <?
                                                            //$i++;
                                                            $total_finish +=$value["qnty"];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                            <tr>
                                <th colspan="17" align="right"><b>Total</b></th>
                                <th align="right"><?=number_format($total_finish, 2); ?></th>
                                <th>&nbsp;</th>
                            </tr>
                        </tbody>
                    </table>
                    <br/>
                    <?
                    $sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color");
                    $sql_qry="SELECT id, sample_mst_id, sample_name, gmts_item_id, smv, article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, sent_to_buyer_date, comments from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=449 and sample_mst_id='$mst_id' order by id asc";
            
                    $sql_qry_color="SELECT a.id, a.sample_mst_id, a.sample_name, a.gmts_item_id, a.smv, a.article_no, a.sample_color, a.sample_prod_qty, a.submission_qty, a.delv_start_date, a.delv_end_date, a.sample_charge, a.sample_curency, a.sent_to_buyer_date, a.comments, c.dtls_id, c.size_id, c.bh_qty, c.self_qty, c.test_qty, c.plan_qty, c.dyeing_qty from sample_development_dtls a, sample_development_size c where a.id=c.dtls_id and a.status_active =1 and a.is_deleted=0 and a.entry_form_id=449 and a.sample_mst_id='$mst_id' order by a.id asc";
                    $size_type_arr=array(1=>"BH Qty",2=>"Self Qty",3=>"Test qty",4=>"Plan Qty",5=>"Wash Qty");
                    $color_size_arr=array();
                    foreach(sql_select($sql_qry_color) as $vals)
                    {
                        if($vals[csf("bh_qty")]>0)
                        {
                            $color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
                            $bh_qty=$vals[csf("bh_qty")];
                            $color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]]=$bh_qty;
                        }
                        if($vals[csf("self_qty")]>0)
                        {
                            $color_size_arr[2][$vals[csf("size_id")]]='Self Qty';
                            $color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("self_qty")];
                        }
                        if($vals[csf("test_qty")]>0)
                        {
                            $color_size_arr[3][$vals[csf("size_id")]]='Test Qty';
                            $color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("test_qty")];
                        }
                        if($vals[csf("plan_qty")]>0)
                        {
                            $color_size_arr[4][$vals[csf("size_id")]]='Plan Qty';
                            $color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("plan_qty")];
                        }
                        if($vals[csf("dyeing_qty")]>0)
                        {
                            $color_size_arr[5][$vals[csf("size_id")]]='Wash Qty';
                            $color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]]=$vals[csf("dyeing_qty")];
                        }
                    }
                    $tot_row=count($color_size_arr);
                    $result=sql_select($sql_qry);
                    ?>
                    <div style="width: 1100px">
                        <div style="width: 1100px; float: left">
                            <table align="left" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all">
                                <thead>
                                    <tr>
                                        <td width="150" colspan="<? echo 12+$tot_row;?>" align="center"><strong>Sample Details </strong></td>
                                    </tr>
                                    <tr>
                                        <th width="30" rowspan="2">SL</th>
                                        <th width="100" rowspan="2">Sample Name</th>
                                        <th width="120" rowspan="2">Garment Item</th>
                                        <th width="70" rowspan="2">Sample Delv.  Date</th>
                                        <th width="70" rowspan="2">Color</th>
                                            <?
                                            $tot_row_td=0;
                                            foreach($color_size_arr as $type_id=>$val)
                                            {
                                                ?>
                                                <th width="45" align="center" colspan="<?=count($val);?>"><?=$size_type_arr[$type_id];?></th>
                                                <?
                                            }
                                            ?>
                                        <th rowspan="2" width="55">Total</th>
                                        <th rowspan="2" width="55">Submn Qty</th>
                                        <th rowspan="2"  width="70">Buyer Submisstion Date</th>
                                        <th rowspan="2">Remarks</th>
                                    </tr>
                                    <tr>
                                        <?
                                        foreach($color_size_arr as $type_id=>$data_size)
                                        {
                                            foreach($data_size as $size_id=>$data_val)
                                            {
                                                $tot_row_td++;
                                                ?>
                                                <th width="40" align="center"><?=$size_library[$size_id];?></th>
                                                <?
                                            }
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                    $i=1; $k=0; $gr_tot_sum=0; $gr_sub_sum=0;
                                    foreach($result as $row)
                                    {
                                        $dtls_ids=$row[csf('id')];
                                        $prod_sum=$prod_sum+$row[csf('sample_prod_qty')];
                                        $sub_sum=$sub_sum+$row[csf('submission_qty')];
                                        $k++;
                                        ?>
                                        <tr>
                                            <td align="center"><?=$k;?></td>
                                            <td align="left"><?=$sample_library[$row[csf('sample_name')]];?></td>
                                            <td align="left"><?=$garments_item[$row[csf('gmts_item_id')]];?></td>
                                            <td align="left"><?=change_date_format($row[csf('delv_end_date')]);?></td>
                                            <td align="left"><?=$color_library[$row[csf('sample_color')]];?></td>
                                            <?
                                            $total_sizes_qty=0;  $total_sizes_qty_subm=0;
                                            foreach($color_size_arr as $type_id=>$data_size)
                                            {
                                                foreach($data_size as $size_id=>$data_val)
                                                {
                                                    $size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
                                                    ?>
                                                    <td align="right"><?=$size_qty;?></td>
                                                    <?
                                                    if($type_id==1)
                                                    {
                                                    $total_sizes_qty_subm+=$size_qty;
                                                    }
                                                    $total_sizes_qty+=$size_qty;
                                                }
                                            }
                                            ?>
                                            <td align="right"><?=$total_sizes_qty;?></td>
                                            <td align="right"><?=$total_sizes_qty_subm;?></td>
                                            <td align="left"><?=change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
                                            <td align="left"><?=$row[csf('comments')];?> </td>
                                        </tr>
                                        <?
                                        $gr_tot_sum+=$total_sizes_qty;
                                        $gr_sub_sum+=$total_sizes_qty_subm;
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="<?=5 + $tot_row_td;?>" align="right"><b>Total</b></td>
                                        <td align="right"><b><?=$gr_tot_sum;?> </b></td>
                                        <td align="right"><b><?=$gr_sub_sum;?> </b></td>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>&nbsp;
                            <table align="left" cellspacing="0" border="1" width="1100" class="rpt_table" rules="all">
                                <thead>
                                    <tr>
                                        <td colspan="10" align="center"><strong>Required Accessories </strong></td>
                                    </tr>
                                    <tr>
                                        <th width="30">Sl</th>
                                        <th width="100">Sample Name</th>
                                        <th width="120">Garment Item</th>
                                        <th width="100">Trims Group</th>
                                        <th width="100">Description</th>
                                        <th width="100">Supplier</th>
                                        <th width="100">Brand/Supp.Ref</th>
                                        <th width="30">UOM</th>
                                        <th width="30">Req/Dzn</th>
                                        <th width="30">Req/Qty</th>
                                        <th width="80">Acc. Source</th>
                                        <th width="100">Acc Delivery Date</th>
                                        <th>Remarks </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                    $sql_qryA="SELECT id, sample_mst_id, sample_name_ra, gmts_item_id_ra, trims_group_ra, description_ra, brand_ref_ra, uom_id_ra, req_dzn_ra, req_qty_ra, remarks_ra, delivery_date, supplier_id, nominated_supp_multi, fabric_source from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$mst_id' order by id asc";
            
                                    $resultA=sql_select($sql_qryA);
                                    $i=1;$k=0; $req_dzn_ra=0; $req_qty_ra=0;
                                    foreach($resultA as $rowA)
                                    {
                                        $req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
                                        $req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];
                                        
                                        $nominated_supp_str="";
                                         $exnominated_supp=explode(",",$rowA[csf('nominated_supp_multi')]);
                                         foreach($exnominated_supp as $supp)
                                         {
                                            if($nominated_supp_str=="") $nominated_supp_str=$supplier_library[$supp]; else $nominated_supp_str.=','.$supplier_library[$supp];
                                         }
                                        $k++;
                                        ?>
                                        <tr>
                                            <td align="center"><? echo $k;?></td>
                                            <td style="word-break:break-all"><? echo $sample_library[$rowA[csf('sample_name_ra')]];?></td>
                                            <td style="word-break:break-all"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                                            <td style="word-break:break-all"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                                            <td style="word-break:break-all"><? echo $rowA[csf('description_ra')];?></td>
                                            <td style="word-break:break-all"><?=$nominated_supp_str;?></td>
                                            <td align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                                            <td align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                                            <td align="right"><? echo $rowA[csf('req_dzn_ra')];?></td>
                                            <td align="right"><? echo $rowA[csf('req_qty_ra')];?></td>
                                            <td align="left"><? echo $fabric_source[$rowA[csf('fabric_source')]];?></td>
                                            <td align="left"><? echo change_date_format($rowA[csf('delivery_date')]);?></td>
                                            <td align="left"><? echo $rowA[csf('remarks_ra')];?></td>
                                        </tr>
                                        <?
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="8" align="center"><b>Total </b></td>
                                        <td align="right"><b><? echo number_format($req_qty_ra,2);?> </b></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="width: 400px; float: left">
                            <? 
                                $image_arr = sql_select("select image_location,form_name  from common_photo_library  where master_tble_id='$mst_id' and form_name in ('samplereqbackimage_1','samplereqfrontimage_1') and is_deleted=0 and file_type=1");
                                foreach ($image_arr as $row) {
                                    if($row[csf('form_name')] == 'samplereqfrontimage_1')
                                    {
                                        $samplereqfrontimage = $row[csf('image_location')];
                                    }
                                    if($row[csf('form_name')] == 'samplereqbackimage_1')
                                    {
                                        $samplereqbackimage = $row[csf('image_location')];
                                    }
                                }
                            ?>
                            <table align="left" cellspacing="0" border="1" width="340" class="rpt_table" rules="all">
                            <tr>
                                <td width="170" align="center">Front Image</td>
                                <td width="170" align="center">Back Image</td>
                            </tr>
                            <tr>
                                <td><img width="170" height="300" src="../../../<? echo $samplereqfrontimage; ?>"/> </td>
                                <td><img width="170" height="300" src="../../../<? echo $samplereqbackimage; ?>"/></td>
            
                            </tr>
                            </table>
                        </div>
                    </div>
                    
                    <br>
                    
                    <?
                    $sqlEmbl="SELECT name_re from sample_development_fabric_acc where sample_mst_id='$mst_id' and form_type=3 and is_deleted=0  and status_active=1 group by name_re order by name_re DESC";
                    $sqlEmblData=sql_select($sqlEmbl);
                    
                    foreach($sqlEmblData as $erow)
                    {
                        $embNameId=$erow[csf('name_re')];
                    ?>
                    <table align="left" cellspacing="0" border="1" width="740px" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <td colspan="8" align="center"><strong>Required <?=$emblishment_name_array[$erow[csf('name_re')]]; ?></strong></td>
                            </tr>
                            <tr>
                                <th width="30">Sl</th>
                                <th width="100">Sample Name</th>
                                <th width="110">Garment Item</th>
                                <th width="110">Body Part</th>
                                <th width="100">Supplier</th>
                                <th width="70"><?=$emblishment_name_array[$erow[csf('name_re')]]; ?> Type</th>
                                <th width="100"><?=$emblishment_name_array[$erow[csf('name_re')]]; ?> Del.Date</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $sql_qry="SELECT id, sample_mst_id, sample_name_re, gmts_item_id_re, name_re, type_re, remarks_re, body_part_id, delivery_date, supplier_id from sample_development_fabric_acc where sample_mst_id='$mst_id' and form_type=3 and is_deleted=0 and name_re='$embNameId' and status_active=1 order by id asc";
            
                            $result=sql_select($sql_qry); $k=0;
                           // $type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
                            foreach($result as $row)
                            {
                                $k++;
                                ?>
                                <tr>
                                    <td align="center"><? echo $k;?></td>
                                    <td align="left"><? echo $sample_library[$row[csf('sample_name_re')]];?></td>
                                    <td align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
                                    <td align="left"><? echo $body_part[$row[csf('body_part_id')]];?></td>
                                    <td align="left"><? echo $supplier_library[$row[csf('supplier_id')]];?></td>
                                    <td align="left">
                                        <?
                                        if($row[csf('name_re')]==1) echo $emblishment_print_type[$row[csf('type_re')]];
                                        if($row[csf('name_re')]==2) echo $emblishment_embroy_type[$row[csf('type_re')]];
                                        if($row[csf('name_re')]==3) echo $emblishment_wash_type[$row[csf('type_re')]];
                                        if($row[csf('name_re')]==4) echo $emblishment_spwork_type[$row[csf('type_re')]];
                                        if($row[csf('name_re')]==5) echo $emblishment_gmts_type[$row[csf('type_re')]];
                                        ?>
                                    </td>
                                    <td align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                                    <td align="left"><? echo $row[csf('remarks_re')];?></td>
                                </tr>
                                <?
                            }
                            ?>
                        </tbody>
                    </table>
                    <br>
                    <?
                    }
                    ?>
                      
                            
                    <table style="margin-top:10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                        <thead>
                            <tr>
                                <th width="40">Sl</th>
                                <th>Special Instruction</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$req_no_booking'");
                            if(count($data_array)>0)
                            {
                                $l=1;
                                foreach( $data_array as $key=>$row )
                                {
                                    ?>
                                    <tr>
                                        <td><? echo $l;?> </td>
                                        <td style="word-break:break-all"><? echo $row[csf("terms")]; ?> </td>
                                    </tr>
                                    <?
                                    $l++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    </br>
                    <? echo signature_table(207, $cbo_company_name, "930px",$cbo_template_id); ?>
                </div>
               <script type="text/javascript" src="../../../js/jquery.js"></script>
               <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
               <script>
                    function fnc_generate_Barcodes( valuess, img_id )
                    {
                        var value = valuess;//$("#barcodeValue").val();
                        var btype = 'code39';//$("input[name=btype]:checked").val();
                        var renderer ='bmp';// $("input[name=renderer]:checked").val();
                        var settings = {
                          output:renderer,
                          bgColor: '#FFFFFF',
                          color: '#000000',
                          barWidth: 1,
                          barHeight: 60,
                          moduleSize:5,
                          posX: 10,
                          posY: 20,
                          addQuietZone: 1
                        };
                        $("#"+img_id).html('11');
                         value = {code:value, rect: false};
                        $("#"+img_id).show().barcode(value, btype, settings);
                    }
               </script>
               <script type="text/javascript">
                fnc_generate_Barcodes('<? echo $barcode_no; ?>','barcode_img_id');
               </script>
                <?
              //  exit();
	
      }
 ?>
		
        
        <table width="30%" align="center"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <caption> <b>Summary </b> </caption>
        <tr align="center">
        <td> <b>Type </b> </td>
        <td>  <b>Fab. Qty  </b></td>
        <td> <b> Rate  </b></td>
        <td> <b> Amount </b> </td>
        </tr>
        <tr>
        <td align="center"> <b>Main Fabric </b></td>
         <td align="right"> <? echo number_format($summ_main_fab_arr[1]['qty'],2);?>  </td>
          <td align="right"> <? echo number_format($summ_main_fab_arr[1]['amt']/$summ_main_fab_arr[1]['qty'],2);?>  </td>
          <td align="right"> <? echo number_format($summ_main_fab_arr[1]['amt'],2);?>  </td>
        </tr>
        <tr>
        <td align="center"> <b>Short Fabric </b></td>
         <td align="right"> <? echo number_format($summ_main_fab_arr[2]['qty'],2);?>  </td>
          <td align="right"> <? echo number_format($summ_main_fab_arr[2]['amt']/$summ_main_fab_arr[2]['qty'],2);?>  </td>
          <td align="right"> <? echo number_format($summ_main_fab_arr[2]['amt'],2);?>  </td>
        </tr>
        <tr>
        <td align="center"> <b>Sample Fabric </b></td>
          <td align="right"> <? echo number_format($summ_main_fab_arr[3]['qty'],2);?>  </td>
          <td align="right"> <? echo number_format($summ_main_fab_arr[3]['amt']/$summ_main_fab_arr[3]['qty'],2);?>  </td>
          <td align="right"> <? echo number_format($summ_main_fab_arr[3]['amt'],2);?>  </td>
        </tr>
        <tr>
        <td align="center"><b>Sample Req.</b></td>
         <td align="right"> <? echo number_format($total_finish,2);?>  </td>
          <td align="right"> <? // echo number_format($grand_amount_sum/$grand_fin_fab_qty_sum,2);?>  </td>
          <td align="right"> <? //echo number_format($grand_amount_sum,2);?>  </td>
        </tr>
        <tr>
         <td align="right"> <b>Total  </b> </td>
          <td align="right"> <b><? echo number_format($summ_main_fab_arr[1]['qty']+$summ_main_fab_arr[2]['qty']+$summ_main_fab_arr[3]['qty']+$total_finish,2);?> </b> </td>
           <td align="right"> <b><? //echo number_format($grand_amount_sum/$grand_fin_fab_qty_sum,2);?>  </b></td>
          <td align="right"> <b><? echo number_format($summ_main_fab_arr[1]['amt']+$summ_main_fab_arr[2]['amt']+$summ_main_fab_arr[3]['amt'],2);?></b>  </td>
        </tr>
        
        </table>
        
</div>
<? 

 } //End

?>