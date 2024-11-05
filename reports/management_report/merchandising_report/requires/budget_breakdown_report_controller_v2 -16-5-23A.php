<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
//ini_set('memory_limit','3072M');
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
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.washes.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$lib_user_arr=return_library_array( "select id,user_name from user_passwd", "id", "user_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
//$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}
if($action=="search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Season No Info", "../../../../", 1, 1,'','','');
	?>
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
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
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_season_id').val( id );
			$('#hide_season').val( name );
		}
    </script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:350px;">
                    <input type="hidden" name="hide_season" id="hide_season" value="" />
                     <input type="hidden" name="hide_season_id" id="hide_season_id" value="" />
                    <?
					$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
                        if($buyerID==0)
                        {
                            if ($_SESSION['logic_erp']["data_level_secured"]==1)
                            {
                                if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
                            }
                            else $buyer_id_cond="";
                        }
                        else $buyer_id_cond=" and buyer_id=$buyerID";
                        
                     
							
							$arr=array (1=>$buyer_name_arr);
						
						  $sql="select id,season_name,buyer_id from lib_buyer_season where status_active=1 and is_deleted=0 $buyer_id_cond order by season_name";
                        //echo $sql;	
                        echo create_list_view("tbl_list_search", "season_name,Buyer", "100,200","300","280",0, $sql , "js_set_value", "id,season_name", "", 1, "0,buyer_id", $arr , "season_name,buyer_id", "","",'0,0','',1) ;
                        ?>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="job_no_popup")//Not used
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'budget_breakdown_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	//echo $sql= "select a.id, a.job_no, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a,wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond group by a.id, a.job_no, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no,a.insert_date  order by a.job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit();
} // Job Search end

if($action=="style_refarence_search")
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'create_list_style_search', 'search_div', 'budget_breakdown_report_controller_v2', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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
if($action=="create_list_style_search")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_style_ref_no,$txt_style_ref_id,$txt_style_ref)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
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
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
		
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	//echo $search_type;
	if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
	else if ($db_type==0) $select_date=" year(a.insert_date)";
	
	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==2) $grp_con="LISTAGG(cast(b.grouping as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.grouping) as ref_no";
	else $grp_con="group_concat(distinct b.grouping)";
	
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as job_year,$grp_con from wo_po_details_master a,wo_po_break_down b where  a.job_no=b.job_no_mst  and a.company_name=$company $buyer_cond $year_cond $job_cond $search_con and a.is_deleted=0 and b.is_deleted=0  group by a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.insert_date order by a.job_no_prefix_num"; 
	
	//echo $sql;  
	echo create_list_view("list_view", "Style Ref No,Job No,Year,Int.Ref No","160,90,100,100","500","200",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year,ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
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
if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#order_no_id").val(splitData[0]);
			$("#order_no_val").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="order_no_id" />
	<input type="hidden" id="order_no_val" />
	<?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if($db_type==0)
	{
		//if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and FIND_IN_SET(b.job_no_prefix_num,'$data[2]')";
	}
	else if($db_type==2)
	{
		//if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and  b.job_no_prefix_num in($data[2]) ";
	}
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and  b.job_no_prefix_num in($data[2]) ";

	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);

	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "budget_breakdown_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	$season=str_replace("'","",$txt_season);
	$txt_season_id=str_replace("'","",$txt_season_id);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	//echo $cbo_search_type.'='.$report_type;

	if($company_name){
		$company_con_name=" and company_name=$company_name";
		$company_con_id=" and company_id=$company_name";
		$company_con_name_a=" and a.company_name=$company_name";
	}
	else
	{
		$company_con_name="";
		$company_con_id="";
		$company_con_name_a="";
	}

	if(str_replace("'","",$txt_file_no) != '') $file_con_b = "and b.file_no=$txt_file_no"; else $file_con_b = " ";
	if(str_replace("'","",$txt_ref_no) != '') $ref_con_b ="and b.grouping=$txt_ref_no"; else $ref_con_b =" ";


	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)

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
	if($order_status_id==0) $order_status_cond=" and b.is_confirmed in(1,2)"; else if($order_status_id!=0) $order_status_cond=" and b.is_confirmed=$order_status_id";

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$start_month=date("Y-m",strtotime($date_from));
	$end_month=date("Y-m",strtotime($date_to));
	$date_to2=date("Y-m-d",strtotime($end_date));

	if($db_type==2)
	{
		$date_from=change_date_format($date_from,'yyyy-mm-dd','-',1);
		$date_to2=change_date_format($date_to2,'yyyy-mm-dd','-',1);
	}
	$total_months=datediff("m",$start_month,$end_month);

	//$last_month=date("Y-m", strtotime($end_month));
	$month_array=array();
	$st_month=$start_month;
	$month_array[]=$st_month;
	for($i=0; $i<$total_months;$i++)
	{
		$start_month=date("Y-m", strtotime("+1 Months", strtotime($start_month)));
		$month_array[]=$start_month;
	}

	$date_cond='';
	if(str_replace("'","",$cbo_search_date)==1)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}

			$order_by="order by b.pub_shipment_date, b.id";

	}
	else if(str_replace("'","",$cbo_search_date)==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.po_received_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}//applying_period_date,applying_period_to_date

		$order_by="order by b.po_received_date, b.id";
	}
	else if(str_replace("'","",$cbo_search_date)==3)// PO Insert Date
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				$date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$insert_date_cond="between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				$date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$insert_date_cond="between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}//applying_period_date,applying_period_to_date
	$order_by="order by b.insert_date, b.id";
	}
	else if(str_replace("'","",$cbo_search_date)==4)// PO Insert Date
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				$date_cond=" and b.update_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				$date_cond=" and b.update_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}//applying_period_date,applying_period_to_date
	$order_by="order by b.update_date, b.id";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	//if($season=="") $season_cond=""; else $season_cond=" and a.season in('".implode("','",explode(",",$season))."')";
	if($txt_season_id!='') $season_cond=" and a.season_buyer_wise in($txt_season_id)"; 
	else  $season_cond="";

	$order_no=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and b.id in ($order_no)";
	else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";

	if($db_type==0) $jobYearCond="and YEAR(a.insert_date)=$cbo_year"; else if($db_type==2) $jobYearCond=" and to_char(a.insert_date,'YYYY')=$cbo_year";


	if(str_replace("'","",$cbo_approved_status)==1)
		$approvalCon="and c.approved=2 and c.ready_to_approved <>1 and c.partial_approved=2";
	else if(str_replace("'","",$cbo_approved_status)==2)
		$approvalCon="and c.approved=2 and c.ready_to_approved =1 and c.partial_approved=2";
	else if(str_replace("'","",$cbo_approved_status)==3)
		$approvalCon="and c.approved=2 and c.ready_to_approved =1 and c.partial_approved=1";
	else if(str_replace("'","",$cbo_approved_status)==4)
		$approvalCon="and c.approved=1 and c.ready_to_approved =1 and c.partial_approved=2";
	else $approvalCon="";
$team_member_arr=return_library_array( "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0",'id','team_leader_name');
if($cbo_search_type==2) //Order Wise
{
	
	if($report_type==1)
	{
		if($template==1)
		{

			$style1="#E9F3FF";
			$style="#FFFFFF";

			$cm_cost_formula_sql=sql_select( "select id, cm_cost_method,company_name from variable_order_tracking where variable_list=22 $company_con_name order by id");
			foreach($cm_cost_formula_sql as $row){
				$cm_cost_formula[$row[csf('company_name')]]=$cm_cost_predefined_method[$row[csf('cm_cost_method')]];
			}
		//$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
		//echo $print_report_format.'ZZZZZZXCCCCCCCCCCCC';
		//$print_button=array_shift(array_values(explode(",",$print_report_format)));

			$asking_profit_arr=array(); //$yarn_desc_array=array();
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $company_con_id");//$date_max_profit
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$asking_profit_arr[$ask_row[csf('company_id')]][$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
				}
			}



			$cpm_arr=array(); //$yarn_desc_array=array();
			$sql_cpm=sql_select("select applying_period_date, applying_period_to_date, cost_per_minute,company_id from lib_standard_cm_entry where is_deleted=0 and status_active=1 $company_con_id ");//$date_max_profit
			foreach($sql_cpm as $cpMrow )
			{
				$applying_period_date=change_date_format($cpMrow[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($cpMrow[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$cpm_arr[$cpMrow[csf('company_id')]][$newdate]['cpm']=$cpMrow[csf('cost_per_minute')];
				}
			}

			unset($sql_cpm);
			/*$smv_eff_arr=array(); $costing_library=array();
			$sql_smv_cpm=sql_select("select a.job_no, a.costing_date, a.sew_smv, a.sew_effi_percent,b.company_name from wo_pre_cost_mst a,wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($sql_smv_cpm as $smv_cpm )
			{
				$costing_library[$smv_cpm[csf('job_no')]]=$smv_cpm[csf('costing_date')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['smv']=$smv_cpm[csf('sew_smv')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['eff']=$smv_cpm[csf('sew_effi_percent')];
				//echo change_date_format($smv_cpm[csf('costing_date')],'','',1);
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['cpm']=$cpm_arr[$smv_cpm[csf('company_name')]][change_date_format($smv_cpm[csf('costing_date')],'','',1)]['cpm'];
			}
			//var_dump($smv_eff_arr);
			unset($sql_smv_cpm);*/

			$asking_profit=array();
		 	/*$sql_budget="select a.company_name,a.job_no_prefix_num, b.insert_date, b.update_date, a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, b.is_confirmed, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $order_by";*/

			  $sql_budget="select A.ID as JOBID,A.COMPANY_NAME,A.JOB_NO_PREFIX_NUM, B.INSERT_DATE, C.INSERT_DATE AS PRE_INSERT_DATE, C.COSTING_DATE, B.UPDATE_DATE, A.JOB_NO, A.BUYER_NAME, A.STYLE_REF_NO, A.ORDER_UOM, B.IS_CONFIRMED, A.AGENT_NAME, A.AVG_UNIT_PRICE, A.DEALING_MARCHANT, A.GMTS_ITEM_ID, A.TOTAL_SET_QNTY AS RATIO, B.PLAN_CUT, B.ID AS PO_ID, B.PO_NUMBER, B.PUB_SHIPMENT_DATE, B.PO_RECEIVED_DATE, B.PO_QUANTITY, B.UNIT_PRICE, B.PO_TOTAL_PRICE, B.GROUPING, B.FILE_NO,C.ENTRY_FROM,A.QUOTATION_ID from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id and c.entry_from=158 $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $approvalCon $file_con_b $ref_con_b $order_by";
			 //echo $sql_budget; die;

			$result_sql_budget=sql_select($sql_budget);
			$jobIdArr=array();
			foreach($result_sql_budget as $row)
			{
				$jobIdArr[$row['JOBID']]=$row['JOBID'];
				$POWiseArr[$row['po_id']]['JOB_NO_PREFIX_NUM']=$row['JOB_NO_PREFIX_NUM'];
				$POWiseArr[$row['po_id']]['company']=$row['COMPANY_NAME'];
				$POWiseArr[$row['po_id']]['pre_insert_date']=$row['PRE_INSERT_DATE'];
				$POWiseArr[$row['po_id']]['insert_date']=$row['INSERT_DATE'];
				$POWiseArr[$row['po_id']]['costing_date']=$row['COSTING_DATE'];
				$POWiseArr[$row['po_id']]['update_date']=$row['UPDATE_DATE'];
				$POWiseArr[$row['po_id']]['job_no']=$row['JOB_NO'];
				$POWiseArr[$row['po_id']]['buyer_name']=$row['BUYER_NAME'];
				$POWiseArr[$row['po_id']]['style_ref_no']=$row['STYLE_REF_NO'];
				$POWiseArr[$row['po_id']]['order_uom']=$row['ORDER_UOM'];
				$POWiseArr[$row['po_id']]['is_confirmed']=$row['IS_CONFIRMED'];
				$POWiseArr[$row['po_id']]['agent_name']=$row['AGENT_NAME'];
				$POWiseArr[$row['po_id']]['avg_unit_price']=$row['AVG_UNIT_PRICE'];
				$POWiseArr[$row['po_id']]['dealing_marchant']=$row['DEALING_MARCHANT'];
				$POWiseArr[$row['po_id']]['gmts_item_id']=$row['GMTS_ITEM_ID'];
				$POWiseArr[$row['po_id']]['ratio']=$row['RATIO'];
				$POWiseArr[$row['po_id']]['plan_cut']=$row['PLAN_CUT'];
				$POWiseArr[$row['po_id']]['ratio']=$row['RATIO'];
				$POWiseArr[$row['po_id']]['po_number']=$row['PO_NUMBER'];
				$POWiseArr[$row['po_id']]['pub_shipment_date']=$row['PUB_SHIPMENT_DATE'];
				$POWiseArr[$row['po_id']]['po_received_date']=$row['PO_RECEIVED_DATE'];
				$POWiseArr[$row['po_id']]['po_quantity']=$row['PO_QUANTITY'];
				$POWiseArr[$row['po_id']]['unit_price']=$row['UNIT_PRICE'];
				$POWiseArr[$row['po_id']]['po_total_price']=$row['PO_TOTAL_PRICE'];
				$POWiseArr[$row['po_id']]['grouping']=$row['GROUPING'];
				$POWiseArr[$row['po_id']]['file_no']=$row['FILE_NO'];
				$POWiseArr[$row['po_id']]['entry_form']=$row['ENTRY_FROM'];
				$POWiseArr[$row['po_id']]['quotation_id']=$row['QUOTATION_ID'];
				
				
			}
			
			$tot_rows_budget=count($result_sql_budget);
			$budget_data_arr=array();
			
			$jobId_cond=where_con_using_array($jobIdArr,0,'a.job_id');
			$smv_eff_arr=array(); $costing_library=array();
			$sql_smv_cpm=sql_select("select a.job_no, a.costing_date, a.sew_smv, a.sew_effi_percent,b.company_name from wo_pre_cost_mst a,wo_po_details_master b where a.job_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobId_cond");
			foreach($sql_smv_cpm as $smv_cpm )
			{
				$costing_library[$smv_cpm[csf('job_no')]]=$smv_cpm[csf('costing_date')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['smv']=$smv_cpm[csf('sew_smv')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['eff']=$smv_cpm[csf('sew_effi_percent')];
				//echo change_date_format($smv_cpm[csf('costing_date')],'','',1);
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['cpm']=$cpm_arr[$smv_cpm[csf('company_name')]][change_date_format($smv_cpm[csf('costing_date')],'','',1)]['cpm'];
			}
			//var_dump($smv_eff_arr);
			unset($sql_smv_cpm);


			?>
			<script>
			var order_amt=document.getElementById('total_order_amount').innerHTML.replace(/,/g ,'');
			//var aaa=document.getElementById('total_yarn_cost').innerHTML.replace(/,/g ,'');
			//alert(aaa);
			document.getElementById('yarn_cost').innerHTML=document.getElementById('total_yarn_cost').innerHTML;
			document.getElementById('yarn_cost_per').innerHTML=number_format_common((document.getElementById('total_yarn_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			document.getElementById('purchase_cost').innerHTML=document.getElementById('total_purchase_cost').innerHTML;
			document.getElementById('purchase_cost_per').innerHTML=number_format_common((document.getElementById('total_purchase_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			document.getElementById('knit_cost').innerHTML=document.getElementById('total_knitting_cost').innerHTML;
			document.getElementById('knit_cost_per').innerHTML=number_format_common((document.getElementById('total_knitting_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			document.getElementById('ydyeing_cost').innerHTML=document.getElementById('total_yarn_dyeing_cost').innerHTML;
			document.getElementById('ydyeing_cost_per').innerHTML=number_format_common((document.getElementById('total_yarn_dyeing_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			document.getElementById('aop_cost').innerHTML=document.getElementById('all_over_print_cost').innerHTML;
			document.getElementById('aop_cost_per').innerHTML=number_format_common((document.getElementById('all_over_print_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			
			document.getElementById('other_dyefin_cost').innerHTML=document.getElementById('total_fabric_process_cost').innerHTML;
			
			var other_fab_process_val=(parseFloat(document.getElementById('other_dyefin_cost').innerHTML.replace(/,/g ,'')));
			
			document.getElementById('other_dyefin_cost_per').innerHTML=number_format_common((other_fab_process_val/order_amt)*100,2) + ' %';
				
			

			var dyefin_val=(parseFloat(document.getElementById('total_fabric_dyeing_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_finishing_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_heat_setting_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_washing_cost').innerHTML.replace(/,/g ,'')));
			document.getElementById('dyefin_cost').innerHTML=number_format_common(dyefin_val,2);
			document.getElementById('dyefin_cost_per').innerHTML=number_format_common((dyefin_val/order_amt)*100,2) + ' %';
			document.getElementById('trim_cost').innerHTML=document.getElementById('total_trim_cost').innerHTML;
			document.getElementById('trim_cost_per').innerHTML=number_format_common((document.getElementById('total_trim_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			var embelishment_val=(parseFloat(document.getElementById('total_print_amount').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_embroidery_amount').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_special_amount').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_wash_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_other_amount').innerHTML.replace(/,/g ,'')));
			document.getElementById('embelishment_cost').innerHTML=number_format_common(embelishment_val,2);
			document.getElementById('embelishment_cost_per').innerHTML=number_format_common((embelishment_val/order_amt)*100,2) + ' %';
			document.getElementById('commercial_cost').innerHTML=document.getElementById('total_commercial_cost').innerHTML;
			document.getElementById('commercial_cost_per').innerHTML=number_format_common((document.getElementById('total_commercial_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			var comission_val=(parseFloat(document.getElementById('total_foreign_amount').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_local_amount').innerHTML.replace(/,/g ,'')));
			document.getElementById('commission_cost').innerHTML=number_format_common(comission_val,2);
			document.getElementById('commission_cost_per').innerHTML=number_format_common((comission_val/order_amt)*100,2) + ' %';
			document.getElementById('testing_cost').innerHTML=document.getElementById('total_test_cost_amount').innerHTML;
			document.getElementById('testing_cost_per').innerHTML=number_format_common((document.getElementById('total_test_cost_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			document.getElementById('freight_cost').innerHTML=document.getElementById('total_freight_amount').innerHTML;
			document.getElementById('freight_cost_per').innerHTML=number_format_common((document.getElementById('total_freight_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			document.getElementById('inspection_cost').innerHTML=document.getElementById('total_inspection_amount').innerHTML;
			document.getElementById('inspection_cost_per').innerHTML=number_format_common((document.getElementById('total_inspection_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			document.getElementById('certificate_cost').innerHTML=document.getElementById('total_certificate_amount').innerHTML;
			document.getElementById('certificate_cost_percent').innerHTML=number_format_common((document.getElementById('total_certificate_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';

			document.getElementById('commn_cost').innerHTML=document.getElementById('total_common_oh_amount').innerHTML;
			document.getElementById('commn_cost_per').innerHTML=number_format_common((document.getElementById('total_common_oh_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';

			document.getElementById('courier_cost').innerHTML=document.getElementById('total_currier_amount').innerHTML;
			document.getElementById('courier_cost_per').innerHTML=number_format_common((document.getElementById('total_currier_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
			document.getElementById('cm_cost').innerHTML=document.getElementById('total_cm_amount').innerHTML;
			document.getElementById('cm_cost_per').innerHTML=number_format_common((document.getElementById('total_cm_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';

			document.getElementById('cost_cost').innerHTML=document.getElementById('total_tot_cost').innerHTML;
			document.getElementById('cost_cost_per').innerHTML=number_format_common((document.getElementById('total_tot_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';

			document.getElementById('order_id').innerHTML=number_format_common(order_amt,2);
			document.getElementById('order_percent').innerHTML=number_format_common((order_amt/order_amt)*100,2);

			//document.getElementById('fab_profit_id').innerHTML=document.getElementById('total_fabric_profit').innerHTML;
			//document.getElementById('profit_fab_percentage').innerHTML=number_format_common((document.getElementById('total_fabric_profit').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			//document.getElementById('expected_id').innerHTML=document.getElementById('total_expected_profit').innerHTML;
			//document.getElementById('profit_expt_fab_percentage').innerHTML=number_format_common((document.getElementById('total_expected_profit').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			//document.getElementById('expt_p_variance_id').innerHTML=document.getElementById('total_expected_variance').innerHTML;
			//document.getElementById('expt_p_percent').innerHTML=number_format_common((document.getElementById('total_expected_variance').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			var matr_ser_cost=(parseFloat(document.getElementById('yarn_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('purchase_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('knit_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('ydyeing_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('aop_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('dyefin_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('other_dyefin_cost').innerHTML.replace(/,/g ,'')));
			document.getElementById('tot_matr_ser_cost').innerHTML=number_format_common(matr_ser_cost,2);
			document.getElementById('tot_matr_ser_per').innerHTML=number_format_common((matr_ser_cost/order_amt)*100,2) + ' %';
			//var aa=order_amt;
			//alert(aa);

				function toggle()
				{
					var ele = document.getElementById("yarn_summary");
					//alert(ele);
					var text = document.getElementById("displayText");
					if(ele.style.display!= "none")
					{
						ele.style.display = "none";
						text.innerHTML = "Show Yarn Summary";
					}
					else
					{
						ele.style.display = "block";
						text.innerHTML = "Hide Yarn Summary";
					}
				}

			</script>
			<div style="width:4270px;">
			<div style="width:900px;" align="left">
				<table width="900" cellpadding="0" cellspacing="2" border="0">
					<tr>
						<td width="350" align="left">
							<table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
								<thead align="center">
									<tr>
										<th colspan="4">Order Wise Budget Cost Summary</th>
									</tr>
									<tr>
										<th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
									</tr>
								</thead>
								<tr bgcolor="<? echo $style1; ?>">
									<td width="20">1</td>
									<td width="130">Yarn Cost</td>
									<td width="120" align="right" id="yarn_cost"></td>
									<td width="80" align="right" id="yarn_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>2</td>
									<td>Fabric Purchase</td>
									<td align="right" id="purchase_cost"></td>
									<td align="right" id="purchase_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>3</td>
									<td>Knitting Cost</td>
									<td align="right" id="knit_cost"></td>
									<td align="right" id="knit_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>4</td>
									<td>Yarn Dyeing Cost</td>
									<td align="right" id="ydyeing_cost"></td>
									<td align="right" id="ydyeing_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>5</td>
									<td>AOP Cost</td>
									<td align="right" id="aop_cost"></td>
									<td align="right" id="aop_cost_per"></td>
								</tr>
								
                                <tr bgcolor="<? echo $style; ?>">
									<td>6</td>
									<td>Dyeing & Finishing Cost</td>
									<td align="right" id="dyefin_cost"></td>
									<td align="right" id="dyefin_cost_per"></td>
								</tr>
                                <tr bgcolor="<? echo $style; ?>">
									<td>7</td>
									<td> Fabric Others Process Cost </td>
									<td align="right" id="other_dyefin_cost"></td>
									<td align="right" id="other_dyefin_cost_per"></td>
								</tr>
                                
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Material & Service Cost</strong></td>
									<td align="right" id="tot_matr_ser_cost"></td>
									<td align="right" id="tot_matr_ser_per"></td>
								</tr>
								<tr bgcolor="<?  echo $style1; ?>">
									<td>8</td>
									<td>Trims Cost</td>
									<td align="right" id="trim_cost"></td>
									<td align="right" id="trim_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>9</td>
									<td>Print/ Emb. /Wash Cost</td>
									<td align="right" id="embelishment_cost"></td>
									<td align="right" id="embelishment_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>10</td>
									<td>Commercial Cost</td>
									<td align="right" id="commercial_cost"></td>
									<td align="right" id="commercial_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>11</td>
									<td>Commision Cost</td>
									<td align="right" id="commission_cost"></td>
									<td align="right" id="commission_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>12</td>
									<td>Testing Cost</td>
									<td align="right" id="testing_cost"></td>
									<td align="right" id="testing_cost_per"></td>
								</tr>
									<tr bgcolor="<? echo $style1; ?>">
									<td>13</td>
									<td>Freight Cost</td>
									<td align="right" id="freight_cost"></td>
									<td align="right" id="freight_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td width="20">14</td>
									<td width="100">Inspection Cost</td>
									<td align="right" id="inspection_cost"></td>
									<td align="right" id="inspection_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>15</td>
									<td>Certificate Cost</td>
									<td align="right" id="certificate_cost"></td>
									<td align="right" id="certificate_cost_percent"></td>
								</tr>
									<tr bgcolor="<? echo $style; ?>">
									<td>16</td>
									<td>Operating Exp.</td>
									<td align="right" id="commn_cost"></td>
									<td align="right" id="commn_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>17</td>
									<td>Courier Cost</td>
									<td align="right" id="courier_cost"></td>
									<td align="right" id="courier_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>18</td>
									<td>CM Cost</td>
									<td align="right" id="cm_cost"></td>
									<td align="right" id="cm_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>19</td>
									<td>Total Cost</td>
									<td align="right" id="cost_cost"></td>
									<td align="right" id="cost_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>20</td>
									<td>Total Order Value</td>
									<td align="right" id="order_id"></td>
									<td align="right" id="order_percent"></td>
								</tr>
							</table>
						</td>
						<!--<td colspan="5" style="min-height:900px; max-height:100%" align="center" valign="top">
							<div id="chartdiv" style="width:580px; height:900px;" align="center"></div>
						</td>-->
					</tr>
				</table>
			</div>
			<br/>
			<?
			ob_start();
			?>
			<h3 align="left" id="accordion_h2" style="width:5070px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
			<fieldset style="width:100%;" id="content_search_panel2">
			<table width="5270">
					<tr class="form_caption">
						<td colspan="50" align="center"><strong>Cost Break Down Report</strong></td>
					</tr>
					<tr colspan="50" align="center" class="form_caption">
						<td><strong>Details Report </strong>
						<span style="height:15px; width:15px; background-color:#FF0000; float:left;  margin-left:10px; margin-right:10px;"></span><span style="float:left;">1 Day  delay from order entry date </span>

						</td>
					</tr>
			</table>
			   <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit'];

					if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else if(str_replace("'","",$cbo_search_date)==3) $caption="PO Insert Date";
					else $caption="Cancelled Date";
			   ?>
			<table id="table_header_1" class="rpt_table" width="5270" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40" rowspan="2">SL</th>
						<th width="100" rowspan="2">Company</th>
						<th width="70" rowspan="2">Buyer</th>
						<th width="70" rowspan="2">Job No</th>
						<th width="70" rowspan="2">File No</th>
						<th width="70" rowspan="2">Ref No</th>
						<th width="100" rowspan="2">Order No</th>
						<th width="100" rowspan="2">Order Status</th>
						<th width="110" rowspan="2">Style</th>
						<th width="110" rowspan="2">Item Name</th>
						<th width="110" rowspan="2">Dealing</th>
						<th width="70" rowspan="2"><? echo $caption; ?></th>
						<!--<th width="70" rowspan="2">Pre Cost Insert date</th>-->
						<th width="70" rowspan="2">Costing  date</th>
						<th width="90" rowspan="2">Order Qty</th>
                        <th width="90" rowspan="2">Order Uom</th>
						<th width="70" rowspan="2">Avg Unit Price</th>
						<th width="100" rowspan="2">Order Value</th>
                        <th width="90" rowspan="2">Order Qty (Pcs)</th>
						<th colspan="16">Fabric Cost</th>
						<th width="100" rowspan="2">Trim Cost</th>
						<th colspan="5">Embell. Cost</th>
						<th width="120" rowspan="2">Commercial Cost</th>
						<th colspan="2">Commission</th>
						<th width="100" rowspan="2">Testing Cost</th>
						<th width="100" rowspan="2">Freight Cost</th>
						<th width="120" rowspan="2">Inspection Cost</th>
						<th width="100" rowspan="2">Certificate Cost</th>
						<th width="100" rowspan="2">Operating Exp</th>
						<th width="100" rowspan="2">Courier Cost</th>

						<th width="100" rowspan="2">Deffd. LC Cost</th>
						<th width="100" rowspan="2">Design Cost</th>
						<th width="100" rowspan="2">Studio Cost</th>

                        <th width="100" rowspan="2">CM Value</th>
						<th width="120" rowspan="2">CM Cost/DZN</th>
						<th width="100" rowspan="2" title="<?php echo $cm_cost_formula[$company_name]; ?>">CM Cost</th>
						<th rowspan="2"  width="100">Total Cost</th>

					</tr>
					<tr>
						<th width="100">Avg Yarn Rate</th>
						<th width="80">Yarn Cost</th>
						<th width="80">Yarn Cost %</th>
						<th width="100">Fabric Purchase</th>
						<th width="80">Knit/ Weav Cost/Dzn</th>
						<th width="80">Knitting/ Weav Cost</th>
						<th width="100">Yarn Dye<br> Cost/Dzn </th>
						<th width="110">Yarn Dyeing Cost </th>
						<th width="120">Fab.Dye <br>Cost/Dzn</th>
						<th width="100">Fabric <br>Dyeing Cost</th>
                        
                        <th width="100"> Fabric Others <br>Process <br>Cost/Dzn</th>
						<th width="100"> Fabric Others <br>Process Cost </th>
                        
						<th width="90">Heat Setting</th>
						<th width="100">Finishing Cost</th>
						<th width="90">Washing Cost</th>
						<th width="90">All Over Print</th>
						<th width="80">Printing</th>
						<th width="85">Embroidery</th>
						<th width="80">Special Works</th>
						<th width="80">Wash Cost</th>
						<th width="80">Other</th>
						<th width="120">Foreign</th>
						<th width="120">Local</th>
					</tr>
				</thead>
			</table>
			<div style="width:5290px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="5270" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
			$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;$total_other_fabric_dyeing_cost=0;
			$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=$total_deffdlc_cost=$total_design_cost=$total_studio_cost=0;
		$print_report_format=return_field_value("format_id","lib_report_template","template_name in($cbo_company_name) and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
		//echo $print_report_format.'DD';
		$format_ids=explode(",",$print_report_format);
		$print_button=$format_ids[0];
	
			 $condition= new condition();
			 if(str_replace("'","",$cbo_company_name)>0){
			 $condition->company_name("=$cbo_company_name");
			 }

			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			$job_no=str_replace("'","",$txt_job_no);
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num(" in($job_no)");
			 }
			 if(str_replace("'","",$cbo_year) !=0){
				  $condition->job_year("$jobYearCond");
			 }
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
				 //and b.po_received_date between '$start_date' and '$end_date'
				// echo 'FFGG';
			 }
			 if(str_replace("'","",$cbo_search_date) ==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				  $condition->insert_date("$insert_date_cond");
				 //and b.po_received_date between '$start_date' and '$end_date' //$insert_date_cond="between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				// echo 'FFGG';
			 }


			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no");
			 }
			 if(str_replace("'","",$txt_file_no)!='')
			 {
				$condition->file_no("=$txt_file_no"); 
			 }
			 if(str_replace("'","",$txt_ref_no)!='')
			 {
				$condition->grouping("=$txt_ref_no"); 
			 }
			 /*if(str_replace("'","",$txt_season)!='')
			 {
				$condition->season("in('".implode("','",explode(",",$season))."')");
				//$condition->season("in($txt_season)");
			 }*/
			 $condition->init();
		     $costing_per_arr=$condition->getCostingPerArr();
			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();

			//print_r($costing_per_arr);
			//$fabric->unsetDataArray();

			$yarn= new yarn($condition);
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			//print_r($yarn_costing_arr);
			//$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			//$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();

			//$yarn->unsetDataArray();
			//echo $yarn->getQuery(); die;
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			
			$conversion= new conversion($condition);
			$conversion_qty_arr=$conversion->getQtyArray_by_OrderFabricProcessAndDiaWidth();
			//print_r($conversion_qty_arr);
			
			//echo $conversion->getQuery();
			//print_r($conversion_costing_arr_process);
			//$conversion->unsetDataArray();

			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			//$trims->unsetDataArray();

			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

			/*$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79);
			$aop_cost_arr=array(35,36,37);
			$fab_finish_cost_arr=array(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129);
			$washing_cost_arr=array(64,82,89);*/

			$knit_cost_arr=array(1,2,3,4);
			$yarn_dying_cost_arr=array(30);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149,158,163);
			//$fabric_dyeingCost_arr=array(25,26,31,39,60,61,62,101,133,137,138,139,146,147,149);
			//$aop_cost_arr=array(35,36,37);
			$aop_cost_arr=array(35,36,37,40);
			//$fab_finish_cost_arr=array(33,34,65,66,67,68,69,70,71,72,73,75,76,77,88,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,100,125,127,128,129,132,144);
			$fab_finish_cost_arr=array(34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			
			
			//print_r($all_existing_processArr2); 
			//echo count($all_existing_processArr).'DDD';;
			
			//$washing_cost_arr=array(64,82,89);
			$washing_cost_arr=array(140,142,148,64);
			//$washing_qty_arr=array(140,142);
			$washing_qty_arr=array(140,142,148,64);
			
			$all_existing_processArr=array_merge($knit_cost_arr,$fabric_dyeingCost_arr,$aop_cost_arr,$fab_finish_cost_arr,$washing_cost_arr,$yarn_dying_cost_arr);
			$process_idArr=array();
			foreach($conversion_cost_head_array as $keyId=>$val)
			{
				$process_idArr[$keyId]=$keyId;
			}
			$all_existing_processArr2=array_diff($process_idArr,$all_existing_processArr);

			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				else if(str_replace("'","",$cbo_search_date)==4)
				{
					$update_date=explode(" ",$row[csf('update_date')]);
					$ship_po_recv_date=change_date_format($update_date[0]);
				}
				$dzn_qnty=$costing_per_arr[$row[csf('job_no')]];
				/*$dzn_qnty=0;
				$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;*/

				//$pre_insert_date=$row[csf('pre_insert_date')];
				$po_insert_date_time=explode(" ",$row[csf('insert_date')]);
				$po_insert_date=$po_insert_date_time[0];
				$po_insert_date=date('d-m-Y',strtotime($po_insert_date));
				$pre_costing_date=date('d-m-Y',strtotime($row[csf('costing_date')]));

				$dateTodelay_days=datediff( 'd', $po_insert_date, $pre_costing_date);
				if($dateTodelay_days>1)
				{
					$td_color="red";
				}
				else
				{
					$td_color="";
				}


				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];

				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];

				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];

				//$costing_library=return_library_array( "select job_no, costing_date from wo_pre_cost_mst", "job_no", "costing_date"  );

				$costing_date=$costing_library[$row[csf('job_no')]];
				//$total_order_amount+=$order_value;
				$total_plancut_amount+=$plancut_value;
				if($print_button==50){$action='preCostRpt'; } //report_btn_1;
				else if($print_button==51){$action='preCostRpt2';} //report_btn_2;
				else if($print_button==52){$action='bomRpt';} //report_btn_3;
				else if($print_button==63){$action='bomRpt2';} //report_btn_4;
				else if($print_button==156){$action='accessories_details';} //report_btn_5;
				else if($print_button==157){$action='accessories_details2';} //report_btn_6;
				else if($print_button==158){$action='preCostRptWoven';} //report_btn_7;
				else if($print_button==159){$action='bomRptWoven';} //report_btn_8;
				else if($print_button==170){$action='preCostRpt3';} //report_btn_9;
				else if($print_button==171){$action='preCostRpt4';} //report_btn_10;
				else if($print_button==173){$action='preCostRpt5';} //report_btn_10;
				else if($print_button==211){$action='mo_sheet';}
				else if($print_button==142){$action='preCostRptBpkW';}
				else if($print_button==197){$action='bomRpt3';}
				else if($print_button==192){$action='checkListRpt';}
				else if($print_button==221){$action='fabric_cost_detail';}
				else if($print_button==238){$action='summary';}
				else if($print_button==215){$action='budget3_details';}
				else $print_button=$row[csf('po_number')];

				$report_button="precost_bom_pop('".$action."','".$row[csf('job_no')]."',".$row[csf('company_name')].",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$costing_date."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."');"; 
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					 <td width="40"><? echo $i; ?></td>
					 <td width="100"><p><? echo $company_library[$row[csf('company_name')]]; ?></p></td>
					 <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $buyer_library[$row[csf('buyer_name')]]; ?></div></td>
					 <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					 <td width="70"><p><? echo $row[csf('file_no')]; ?></p></td>
					 <td width="70"><p><? echo $row[csf('grouping')]; ?></p></td>
					 <td width="100"><div style="word-wrap:break-word; width:100px">
                    <a href='##'  onclick="<? echo $report_button; ?>"><? echo $row[csf('po_number')]; ?></a>
                     <!--<a href="javascript:precost_bom_pop('<?echo $row[csf('po_id')]; ?>','<?echo $row[csf('job_no')]; ?>','<?echo $company_name; ?>','<?echo $row[csf('buyer_name')]; ?>','<?echo $row[csf('style_ref_no')]; ?>','<?echo $costing_date; ?>');"><?echo $row[csf('po_number')]; ?></a>--></div></td>
					 <td width="100"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
					 <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('style_ref_no')]; ?></div></td>
					 <td width="110"><div style="word-wrap:break-word; width:110px"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
					 <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></div></td>
					 <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $ship_po_recv_date; ?></div></td>
					 <!-- <td width="70"><div style="word-wrap:break-word; width:70px"><? //echo $pre_insert_date; ?></div></td>-->
					   <td width="70" title="PO Insert Date<? echo $po_insert_date;?>" bgcolor="<? echo $td_color;?>"><div style="word-wrap:break-word; width:70px"><? echo $pre_costing_date; ?></div></td>

					 <td width="90" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                     <td width="90"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                     <td width="70" align="right"><p><? echo number_format($row[csf('unit_price')],2); ?></p></td>
                     <td width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
					 <?
						$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];
						$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
						$avg_rate=$yarn_costing/$yarn_req_qty_arr[$row[csf('po_id')]];
						$yarn_cost_percent=($yarn_costing/$order_value)*100;

						$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
						$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
						$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;

						$knit_cost=0;
						foreach($knit_cost_arr as $process_id)
						{
							$knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);
						}

						$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;

						$fabric_dyeing_cost=0;
						foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
							$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);
						}

						$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
						$heat_setting_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][33]);

						$fabric_finish=0;
						foreach($fab_finish_cost_arr as $fin_process_id)
						{
							$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fin_process_id]);
						}

						$all_over_cost=0;
						foreach($aop_cost_arr as $aop_process_id)
						{
							$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);
						}

						$washing_cost=0;
						foreach($washing_cost_arr as $w_process_id)
						{
							$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);
						}
						$other_fab_process_cost=0;//conversion_cost_head_array
						foreach($all_existing_processArr2 as $key_id=>$val)
						{
							//if(in_array($key_id,$conversion_cost_head_array))
							//{
								 	
								$tot_other_processCost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$key_id]);
							//	echo $tot_other_processCost.', ';
								if($tot_other_processCost>0)
								{
								
								$other_fab_process_cost+=$tot_other_processCost;
								}
							//}
						}
							$other_fab_process_cost_dzn=($other_fab_process_cost/$plan_cut_qnty)*12;

						$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
						$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
						$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
						$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
						$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
						$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
						$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
						$local=$commission_costing_arr[$row[csf('po_id')]][2];
						$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
						$freight_cost=$other_costing_arr[$row[csf('po_id')]]['freight'];
						$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
						$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
						$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
						$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];

						$deffdlc_cost=$other_costing_arr[$row[csf('po_id')]]['deffdlc_cost'];
						$design_cost=$other_costing_arr[$row[csf('po_id')]]['design_cost'];
						$studio_cost=$other_costing_arr[$row[csf('po_id')]]['studio_cost'];

						$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;

						$total_cost=$yarn_costing+$fab_purchase+$knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$other_fab_process_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost+$deffdlc_cost+$design_cost+$studio_cost;
						// echo $total_cost.'='.$other_fab_process_cost.'k';
						//$cm_value=($order_value-$total_cost)+$cm_cost;
						//echo $cm_cost;
						$others_cost_value = $total_cost -($cm_cost+$freight_cost+$commercial_cost+($foreign+$local));
						$net_order_val=$order_value-(($foreign+$local)+$commercial_cost+$freight_cost);
						//echo $net_order_val;
						//echo $cm_cost."+".$freight_cost."+".$commercial_cost."+".$foreign."+".$local."</br>";
						//echo $others_cost_value;
						//echo  $net_order_val;
						$cm_value=$net_order_val-$others_cost_value;

						//echo $cm_cost+$freight_cost+$common_oh+$foreign+$local;
						//echo $total_cost-($cm_cost-$freight_cost-$commercial_cost-$foreign-$local);
						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100;
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;



						$expect_variance=$total_profit-$expected_profit;

						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
						if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
						if($commercial_cost<=0) $color_com="red"; else $color_com="";
					 ?>
					 <td width="100" align="right"><a href="##" onClick="generate_pre_cost_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','precost_yarn_detail')"><? echo number_format($avg_rate,2); ?></a></td>
					 <td width="80" align="right" bgcolor="<? echo $color_yarn; ?>"><? echo number_format($yarn_costing,2); ?></td>
					 <td width="80" align="right"><? echo number_format($yarn_cost_percent,2); ?></td>
					 <td width="100" align="right"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></td>
					 <td width="80" align="right"><? echo number_format($knit_cost_dzn,4); ?></td>
					 <td width="80" align="right" bgcolor="<? echo $color_knit; ?>"><a href="##" onClick="generate_pre_cost_knit_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cons_process; ?>','precost_knit_detail')"><? echo number_format($knit_cost,2); ?></a></td>
					 <td width="100" align="right"><? echo number_format($yarn_dyeing_cost_dzn ,2); ?></td>
					 <td width="110" align="right"><? echo number_format($yarn_dyeing_cost ,2); ?></td>
					 <td width="120" align="right"><? echo number_format($fabric_dyeing_cost_dzn,2); ?></td>
                     
                     
                       
					 <td width="100" align="right" bgcolor="<? echo $color_fab; ?>"><a href="##" onClick="generate_precost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_dyeing_detail')"><? echo number_format($fabric_dyeing_cost,2); ?></a></td>
                     
                      <td width="100" align="right"><? echo number_format($other_fab_process_cost_dzn,2); ?></td>
                      <td width="100" align="right"><? echo number_format($other_fab_process_cost,2); ?></td>
                      
					 <td width="90" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
					 <td width="100" align="right" ><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_finishing_detail')"><? echo number_format($fabric_finish,2); ?></a></td>
					 <td width="90" align="right"><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_washing_detail')"><? echo number_format($washing_cost,2); ?></a></td>
					 <td width="90" align="right"><a href="##" onClick="generate_precost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_all_over_detail')"><? echo number_format($all_over_cost,2); ?></a></td>
				<?
					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;

					$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;

					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_cm_amount+=$cm_cost;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];

					if($trim_amount<=0) $color_trim="red"; else $color_trim="";
					if($cm_cost<=0) $color="red"; else $color="";
					$smv=0; $eff=0; $cpm=0; $cost_dzn_cm_title=0;
					$smv=$smv_eff_arr[$row[csf('job_no')]]['smv'];
					$eff=$smv_eff_arr[$row[csf('job_no')]]['eff'];
					$cpm=$smv_eff_arr[$row[csf('job_no')]]['cpm'];
					$cost_dzn_cm_title='Swe. SMV: '.$smv.'; Swe. EFF: '.$eff.'; CPM: '.$cpm;

					?>
					 <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><!--<a href="##" onClick="generate_precost_trim_cost_detail('<? //echo $row[csf('po_id')]; ?>','<? //echo $row[csf('job_no')];?>','<? //echo $company_name; ?>','<? //echo $row[csf('buyer_name')]; ?>','<? //echo $row[csf('style_ref_no')]; ?>','trim_cost_detail')"><? //echo number_format($trim_amount,2); ?></a>--><? echo number_format($trim_amount,2); ?></td>
					 <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','print_cost_detail')"><? echo number_format($print_amount,2); ?></a></td>
					 <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','embroidery_cost_detail')"><? echo number_format($embroidery_amount,2); ?></a></td>
					 <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
					 <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','wash_cost_detail')"><? echo number_format($wash_cost,2); ?></a></td>
					 <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
					 <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
					 <td width="120" align="right"><? echo number_format($local,2) ?></td>
					 <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
					 <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($inspection,2);?></td>
					 <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
					 <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
					 <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>

					  <td width="100" align="right"><? echo number_format($deffdlc_cost,2);?></td>
					  <td width="100" align="right"><? echo number_format($design_cost,2);?></td>
					  <td width="100" align="right"><? echo number_format($studio_cost,2);?></td>

                     <td width="100" align="right"><? echo number_format($cm_value,2);?></td>
					 <td width="120" align="right" title="<?php echo $cost_dzn_cm_title; ?>"><? echo number_format($cm_cost_dzn,2);?></td>
					 <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
					 <td align="right"  width="100"><? echo number_format($total_cost,2); ?></td>
					<?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";
						else $color_pl="";
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
						$expect_variance=$total_profit-$expected_profit_per;

					?>
				  </tr>
				<?
				$total_order_qty_pcs+=$order_qty_pcs;
				$total_order_qty+=$row[csf('po_quantity')];
				$total_order_amount+=$order_value;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_other_fabric_dyeing_cost+=$other_fab_process_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$all_over_print_cost+=$all_over_cost;
				$total_trim_cost+=$trim_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_other_fabric_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;

				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_cm_value+=$cm_value;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;

				$total_deffdlc_cost+=$deffdlc_cost;
				$total_design_cost+=$design_cost;
				$total_studio_cost+=$studio_cost;

				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;
				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			?>
			</table>
			</div>
			<table class="tbl_bottom" width="5270" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="40">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="72">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="102">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="113">&nbsp;</td>
					<td width="114">&nbsp;</td>
					<td width="112"><strong>Total:</strong></td>
					<td width="73">&nbsp;</td>
					<td width="72">&nbsp;</td>

                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
                    <td width="90">&nbsp;</td>

                    <td width="70">&nbsp;</td>
					<td width="100" align="right" id="total_order_amount"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="90" align="right" id="total_order_qnty_pcs"><? echo number_format($total_order_qty_pcs,2); ?></td>
					<td width="100">&nbsp;</td>
					<td width="80" align="right" id="total_yarn_cost"><? echo number_format($total_yarn_cost,2); ?></td>
					<td width="80">&nbsp;</td>
					<td width="100" align="right" id="total_purchase_cost"><? echo number_format($total_purchase_cost,2); ?></td>
					<td width="80">&nbsp;</td>
					<td width="80" align="right" id="total_knitting_cost"><? echo number_format($total_knitting_cost,2); ?></td>
					<td width="100">&nbsp;</td>
					<td width="110" align="right" id="total_yarn_dyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
                    
                   
                    
					<td width="120">&nbsp;</td>
					<td width="100" align="right" id="total_fabric_dyeing_cost"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
                    
                     <td width="100">&nbsp;</td>
                    <td width="100" id="total_fabric_process_cost"><? echo number_format($total_other_fabric_dyeing_cost,2); ?></td>
                    
					<td width="90" align="right" id="total_heat_setting_cost"><? echo number_format($total_heat_setting_cost,2); ?></td>
					<td width="100" align="right" id="total_finishing_cost"><? echo number_format($total_finishing_cost,2); ?></td>
					<td width="90" align="right" id="total_washing_cost"><? echo number_format($total_washing_cost,2); ?></td>
					<td width="90" align="right" id="all_over_print_cost"><? echo number_format($all_over_print_cost,2); ?></td>
					<td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
					<td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
					<td width="80" align="right" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2); ?></td>
					<td width="120" align="right" id="total_foreign_amount"><? echo number_format($total_foreign_amount,2); ?></td>
					<td width="120" align="right" id="total_local_amount"><? echo number_format($total_local_amount,2); ?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2); ?></td>
					<td width="100" align="right" id="total_freight_amount"><? echo number_format($total_freight_amount,2); ?></td>
					<td width="120" align="right" id="total_inspection_amount"><? echo number_format($total_inspection_amount,2); ?></td>
					<td width="100" align="right" id="total_certificate_amount"><? echo number_format($total_certificate_amount,2); ?></td>
					<td width="100" align="right" id="total_common_oh_amount"><? echo number_format($total_common_oh_amount,2); ?></td>
					<td width="100" align="right" id="total_currier_amount"><? echo number_format($total_currier_amount,2); ?></td>

					<td width="100" align="right" id="total_deffdlc_amount"><? echo number_format($total_deffdlc_cost,2); ?></td>
					<td width="100" align="right" id="total_design_amount"><? echo number_format($total_design_cost,2); ?></td>
					<td width="100" align="right" id="total_studio_amount"><? echo number_format($total_studio_cost,2); ?></td>

                    <td width="100" align="right" id="total_cm_value"><? echo number_format($total_cm_value,2); ?></td>
					<td width="120">&nbsp;</td>
					<td width="100" align="right" id="total_cm_amount"><? echo number_format($total_cm_amount,2); ?></td>
					<td align="right"  width="100" id="total_tot_cost"><? echo number_format($total_tot_cost,2); ?></td>
				</tr>
			</table>
			<table>
				<tr>
					<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_cost_up,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');

					//$chart_data_qnty="Fabric Cost;".$total_fab_cost."\nTrimCost;".$total_trim_cost."\nEmbelishment Cost;".$total_embelishment_cost."\nCommercial Cost;".$total_commercial_cost."\nCommission Cost;".$total_commssion."\nTesting Cost;".$total_testing_cost."\nFreightCost;".$total_freight_cost."\nCM Cost;".$total_cm_cost."\nInspection Cost;".$total_inspection."\nCertificate Cost;".$total_certificate_cost."\nCommn OH Cost;".$total_common_oh."\nCurrier Cost;".$total_currier_cost."\n Profit/Loss;".$total_fabric_profit_up."\n";
					?>
					<input type="hidden" id="graph_data" value="<? //echo substr($chart_data_qnty,0,-1); ?>"/>
				</tr>
			</table>
			<br>
			<a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>

			<div style="width:600px; display:none" id="yarn_summary" >
				<div id="data_panel2" align="center" style="width:500px">
					 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
				 </div>

                <table class="rpt_table" width="590" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                	<tr>
                                    	<th colspan="7">Yarn Cost Summary</th>
                                    </tr>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="140">Composition</th>
                                        <th width="60">Yarn Count</th>
                                        <th width="80">Type</th>
                                        <th width="100">Req. Qty</th>
                                        <th width="70">Avg. Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <?
                                $s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
                                foreach($yarn_des_data as $count=>$count_value)
                                {
								foreach($count_value as $Composition=>$composition_value)
                                {
								foreach($composition_value as $percent=>$percent_value)
                                {
								foreach($percent_value as $type=>$type_value)
                                {
                                    if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    //$yarn_desc=explode("**",$key);

                                    $tot_yarn_req_qnty+=$type_value['qty'];
                                    $tot_yarn_req_amnt+=$type_value['amount'];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
                                        <td><? echo $s; ?></td>
                                        <td><div style="word-wrap:break-word; width:140px"><? echo $composition[$Composition]." ".$percent."%"; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:60px"><? echo $yarn_count_library[$count]; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:80px"><? echo $yarn_type[$type]; ?></div></td>
                                        <td align="right"><? echo number_format($type_value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($type_value['amount']/$type_value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($type_value['amount'],2); ?></td>
                                    </tr>
                                    <?
                                    $s++;
								}
								}
								}
                                }
                                ?>
                                <tfoot>
                                    <th colspan="4" align="right">Total</th>
                                    <th align="right"><? echo number_format($tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt,2); ?></th>
                                </tfoot>
                            </table>
			</div>
			</fieldset>
			</div>
			<?
		}
	} //1st button end
	else if($report_type==3) //Report button
	{
		if($template==1)
		{
			$app_status_arr=array(
				222=>'Not Submit',
				202=>'Not Submit',
				212=>'Ready For Approval',
				211=>'Partial Approved',
				111=>'Full Approved'
			);
			$style1="#E9F3FF";$style="#FFFFFF";
			$cm_cost_formula_sql=sql_select( "select id, cm_cost_method from variable_order_tracking where  variable_list=22 $company_con_name order by id");
			$cm_cost_formula=$cm_cost_predefined_method[$cm_cost_formula_sql[0][csf('cm_cost_method')]];
			$asking_profit_arr=array();
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $company_con_id");//$date_max_profit
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
				}
			}
		$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
		//echo $print_report_format.'ZZZZZZXCCCCCCCCCCCC';
		$print_button=array_shift(array_values(explode(",",$print_report_format)));
	
			$asking_profit=array();
			

		 $sql_budget="select a.id,a.company_name,a.job_no_prefix_num,a.team_leader,a.set_smv, max(b.insert_date) as insert_date, c.insert_date as pre_insert_date, c.costing_date, max(b.update_date) as update_date, a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, sum(b.plan_cut) as plan_cut,sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved,b.pub_shipment_date,b.po_received_date,b.insert_date,b.id as po_id,b.po_number,b.is_confirmed,c.entry_from,a.quotation_id from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id and c.entry_from=158 $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $approvalCon $file_con_b $ref_con_b group by  a.id,a.company_name,a.job_no_prefix_num, c.insert_date , c.costing_date,a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.team_leader,a.dealing_marchant,a.set_smv, a.gmts_item_id, a.total_set_qnty,b.pub_shipment_date,c.entry_from,a.quotation_id,b.po_received_date,b.insert_date,b.id,b.po_number,b.is_confirmed,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved order by b.id asc";
			//echo $sql_budget; die;
		

			$result_sql_budget=sql_select($sql_budget);
			$tot_rows_budget=count($result_sql_budget);
			$budget_data_arr=array();$jobIdArr=array();
			foreach($result_sql_budget as $row )
			{
				$job_id.=$row[csf('id')].',';
				$jobIdArr[$row[csf('id')]]=$row[csf('id')];
			}
			$jobIds=chop($job_id,',');  
			$job_ids=count(array_unique(explode(",",$job_id)));
			if($db_type==2 && $job_ids>1000)
			{
				$job_cond_for_in=" and (";
				$jobIdsArr=array_chunk(explode(",",$jobIds),999);
				foreach($jobIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$job_cond_for_in.=" b.id in($ids) or"; 
				}
				$job_cond_for_in=chop($job_cond_for_in,'or ');
				$job_cond_for_in.=")";
			}
			else
			{
				$job_cond_for_in=" and b.id in($jobIds)";
			}
			$cpm_arr=array();$cpm_date_arr=array(); //$yarn_desc_array=array();
			$finance_arr=array();
			$sql_cpm=sql_select("select applying_period_date, applying_period_to_date, cost_per_minute,interest_expense from lib_standard_cm_entry where 1=1 $company_con_id");
			foreach($sql_cpm as $cpMrow )
			{
				$applying_period_date=change_date_format($cpMrow[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($cpMrow[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate = date("d-m-Y",strtotime(add_date(str_replace("'","",$applying_period_date),$j)));
					$cpm_arr[$newdate]['cpm']=$cpMrow[csf('cost_per_minute')];
					$finance_arr[$newdate]['finPercent']=$cpMrow[csf('interest_expense')];
				}
			}
			$smv_eff_arr=array(); $costing_library=array();
			$jobId_cond=where_con_using_array($jobIdArr,0,'a.job_id');
			$sql_smv_cpm=sql_select("select a.job_no, a.costing_date, a.sew_smv, a.sew_effi_percent,a.exchange_rate,b.company_name from wo_pre_cost_mst a,wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobId_cond");
			foreach($sql_smv_cpm as $smv_cpm )
			{
				$dateKey=date("d-m-Y",strtotime($smv_cpm[csf('costing_date')]));
				
				$costing_library[$smv_cpm[csf('job_no')]]=$smv_cpm[csf('costing_date')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['smv']=$smv_cpm[csf('sew_smv')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['eff']=$smv_cpm[csf('sew_effi_percent')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['cpm']=$cpm_arr[$dateKey]['cpm'];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['exc_rate']=$smv_cpm[csf('exchange_rate')]; 
			}
			unset($sql_smv_cpm);
			
			//var_dump($smv_eff_arr);die;
 			$condition= new condition();
			 if(str_replace("'","",$cbo_company_name)>0){
				 $condition->company_name("=$cbo_company_name");
			 }
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			$job_no=str_replace("'","",$txt_job_no);
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num("in($job_no)");
			 }
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
			 }
			  if(str_replace("'","",$cbo_search_date) ==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				$condition->insert_date("$insert_date_cond");
			 }

			if(str_replace("'","",$cbo_year) !=0){
				  $condition->job_year("$jobYearCond");
			 }
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("in($txt_order_no)");
			 }

			$condition->init();

		    $costing_per_arr=$condition->getCostingPerArr();
			$fabric= new fabric($condition);
			//$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_costing_arr=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			$fabric_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_purchase_qty_arr=$fabric->getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			//print_r($fabric_purchase_qty_arr);
			$yarn= new yarn($condition);
		//	echo $yarn->getQuery();die;
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();

			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();
			//print_r($yarn_des_data);
			$yarn= new yarn($condition);
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			$conversion_qty_arr_process=$conversion->getQtyArray_by_orderAndProcess();
			$conversion_costing_po_arr=$conversion->getAmountArray_by_order();

			$conversion_qty= new conversion($condition);
			$conversion_qty_arr_process = $conversion_qty->getQtyArray_by_orderAndProcess();
			$conversion_job_amount_arr_process = $conversion_qty->getAmountArray_by_jobAndProcess();
			$conversion_job_qty_arr_process = $conversion_qty->getQtyArray_by_jobAndProcess();
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149,158);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
			$washing_qty_arr=array(140,142,148,64);	
			$not_process_id_print_array = array(1,30,35); 
			
			$total_print_amount=$total_embroidery_amount=$total_special_amount=$total_other_amount=$total_wash_cost=$total_gmtdying_amount=0;
			foreach($result_sql_budget as $row )
			{
				$total_order_amount+=$row[csf('po_total_price')];
				$poId=$row[csf('po_id')];
					$total_yarn_cost+=$yarn_costing_arr[$poId];
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$poId][2]);
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$poId][2]);
					$total_purchase_cost+=($fab_purchase_knit+$fab_purchase_woven);
					$total_trim_cost+=$trims_costing_arr[$poId];
					$total_yarn_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][30]);
					$all_over_cost=0;
					foreach($aop_cost_arr as $aop_process_id)
					{
						$all_over_cost+=array_sum($conversion_costing_arr_process[$poId][$aop_process_id]);
					}
					$all_over_print_cost+=$all_over_cost;				
					$knit_cost=0;
					foreach($knit_cost_arr as $process_id)
					{
						$knit_cost+=array_sum($conversion_costing_arr_process[$poId][$process_id]);
					}
					$total_knit_cost+=$knit_cost;
					$fabric_dyeing_cost=0;
					/*foreach($fabric_dyeingCost_arr as $fab_process_id)
					{
						$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][$fab_process_id]);
					}*/
					foreach($conversion_cost_head_array as $key=>$val)
						{
							if (!in_array($key, $not_process_id_print_array)) 
							{
								$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][$key]);
							}
							
						}
						
					$total_fabric_dyeing_cost+=$fabric_dyeing_cost;				
					$fabric_finish=0;
					foreach($fab_finish_cost_arr as $fin_process_id)
					{
						$fabric_finish+=array_sum($conversion_costing_arr_process[$poId][$fin_process_id]);
					}
					$total_finishing_cost+=$fabric_finish;
					$total_heat_setting_cost+=array_sum($conversion_costing_arr_process[$poId][33]);
					$washing_cost=0;
					foreach($washing_cost_arr as $w_process_id)
					{
						$washing_cost+=array_sum($conversion_costing_arr_process[$poId][$w_process_id]);
					}
					$total_washing_cost+=$washing_cost;
					$total_print_amount+=$emblishment_costing_arr_name[$poId][1];
					$total_embroidery_amount+=$emblishment_costing_arr_name[$poId][2];
					$total_special_amount+=$emblishment_costing_arr_name[$poId][4];
					$total_gmtdying_amount+=$emblishment_costing_arr_name[$poId][5];
					$total_other_amount+=$emblishment_costing_arr_name[$poId][99];
					$total_wash_cost+=$emblishment_costing_arr_name_wash[$poId][3];
					$total_commercial_cost+=$commercial_costing_arr[$poId];
					$total_foreign_amount+=$commission_costing_arr[$poId][1];
					$total_local_amount+=$commission_costing_arr[$poId][2];
					$total_testing_cost+=$other_costing_arr[$poId]['lab_test'];
					$total_freight_cost+=$other_costing_arr[$poId]['freight'];
					$total_inspection_cost+=$other_costing_arr[$poId]['inspection'];
					$total_certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
					$total_common_oh+=$other_costing_arr[$poId]['common_oh'];
					$total_currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
					$total_cm_cost+=$other_costing_arr[$poId]['cm_cost'];				
					$total_deffdlc_cost+=$other_costing_arr[$poId]['deffdlc_cost'];
					$total_design_cost+=$other_costing_arr[$poId]['design_cost'];
					$total_studio_cost+=$other_costing_arr[$poId]['studio_cost'];

				
			}
			
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			$total_purchase_cost_percentage=$total_purchase_cost/$total_order_amount*100;
			$total_trim_cost_percentage=$total_trim_cost/$total_order_amount*100;
			$total_yarn_dyeing_percentage=$total_yarn_dyeing_cost/$total_order_amount*100;
			$all_over_print_cost_percentage=$all_over_print_cost/$total_order_amount*100;
			$total_knit_cost_percentage=$total_knit_cost/$total_order_amount*100;
			
			$total_dyefin_cost=$total_fabric_dyeing_cost+$total_finishing_cost+$total_heat_setting_cost;//+$total_washing_cost
			$total_dyefin_cost_percentage=$total_dyefin_cost/$total_order_amount*100;
			//echo $total_print_amount.'='.$total_embroidery_amount.'='.$total_special_amount.'='.$total_other_amount.'='.$total_wash_cost;
			$total_embelishment_cost=$total_print_amount+$total_embroidery_amount+$total_special_amount+$total_gmtdying_amount+$total_other_amount+$total_wash_cost;
			$total_embelishment_cost_percentage=$total_embelishment_cost/$total_order_amount*100;
			$total_commercial_cost_percentage=$total_commercial_cost/$total_order_amount*100;
			$total_commission_cost=$total_foreign_amount+$total_local_amount;
			$total_commission_cost_percentage=$total_commission_cost/$total_order_amount*100;
			$total_testing_cost_percentage=$total_testing_cost/$total_order_amount*100;
			$total_freight_cost_percentage=$total_freight_cost/$total_order_amount*100;
			$total_inspection_cost_percentage=$total_inspection_cost/$total_order_amount*100;
			$total_certificate_cost_percentage=$total_certificate_cost/$total_order_amount*100;
			$total_common_oh_percentage=$total_common_oh/$total_order_amount*100;
			$total_currier_cost_percentage=$total_currier_cost/$total_order_amount*100;
			$total_cm_cost_percentage=$total_cm_cost/$total_order_amount*100;
			
			
			$total_tot_cost=$total_yarn_cost+$total_purchase_cost+$total_knit_cost+$total_washing_cost+$all_over_print_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_trim_cost+$total_testing_cost+$total_print_amount+$total_embroidery_amount+$total_special_amount+$total_gmtdying_amount+$total_other_amount+$total_wash_cost+$total_commercial_cost+$total_foreign_amount+$total_local_amount+$total_freight_cost+$total_inspection_cost+$total_certificate_cost+$total_common_oh+$total_currier_cost+$total_cm_cost;			
			$total_tot_cost_percentage=$total_tot_cost/$total_order_amount*100;
			
			$total_material_cost=$total_yarn_cost+$total_purchase_cost+$total_trim_cost+$total_yarn_dyeing_cost+$all_over_print_cost;
			$total_material_cost_percentage=$total_material_cost/$total_order_amount*100;
			
			$total_service_cost=$total_knit_cost+$total_dyefin_cost+$total_embelishment_cost;
			$total_service_cost_percentage=$total_service_cost/$total_order_amount*100;			
			
			
			?>
			
		<script>
			function toggle()
			{
				var ele = document.getElementById("yarn_summary");
				//alert(ele);
				var text = document.getElementById("displayText");
				if(ele.style.display!= "none")
				{
					ele.style.display = "none";
					text.innerHTML = "Show Yarn Summary";
				}
				else
				{
					ele.style.display = "block";
					text.innerHTML = "Hide Yarn Summary";
				}
			}

	  </script>
			
			<? 
				$width=6605;
				ob_start(); 
			?>
			
		<div style="width:<? echo $width;?>px;">
			<div style="width:900px;" align="left">
				<table width="900" cellpadding="0" cellspacing="2" border="0">
					<tr>
						<td width="350" align="left">
							<table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
								<thead align="center">
									<tr>
										<th colspan="4">Order Wise Budget Cost Summary</th>
									</tr>
									<tr>
										<th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
									</tr>
								</thead>
								<tr bgcolor="<? echo $style1; ?>">
									<td width="20">1</td>
									<td width="130">Yarn Cost</td>
									<td width="120" align="right"><? echo number_format($total_yarn_cost,2);?></td>
									<td align="right"><? echo number_format($total_yarn_cost_percentage,2);?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>2</td>
									<td>Fabric Purchase</td>
									<td align="right"><? echo number_format($total_purchase_cost,2);?></td>
									<td align="right" ><? echo number_format($total_purchase_cost_percentage,2);?></td>
								</tr>
								<tr bgcolor="<?  echo $style1; ?>">
									<td>3</td>
									<td>Trims Cost</td>
									<td align="right"><? echo number_format($total_trim_cost,2);?></td>
									<td align="right"><? echo number_format($total_trim_cost_percentage,2);?></td>
								</tr>
                                <tr bgcolor="#CCCCCC">
									<td></td>
									<td><strong>Actual Material Cost</strong></td>
									<td align="right" title="Yarn Cost+Trim Cost+Finished Fabric Purchase Cost"><? 
									$total_act_material_cost=$total_yarn_cost+$total_purchase_cost+$total_trim_cost;
									echo number_format($total_act_material_cost,2); ?></td>
									<td align="right"><? echo number_format($total_act_material_cost/$total_order_amount*100,2); ?></td>
								</tr>
                                
								<tr bgcolor="<? echo $style; ?>">
									<td>4</td>
									<td>Yarn Dyeing Cost</td>
									<td align="right"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
									<td align="right"><? echo number_format($total_yarn_dyeing_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>5</td>
									<td>AOP Cost</td>
									<td align="right"><? echo number_format($all_over_print_cost,2); ?></td>
									<td align="right"><? echo number_format($all_over_print_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total B2B Cost</strong></td>
									<td align="right"><? echo number_format($total_material_cost,2); ?></td>
									<td align="right"><? echo number_format($total_material_cost_percentage,2); ?></td>
								</tr>

                                <tr bgcolor="<? echo $style1; ?>">
									<td>6</td>
									<td>Knitting Cost</td>
									<td align="right"><? echo number_format($total_knit_cost,2); ?></td>
									<td align="right"><? echo number_format($total_knit_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>7</td>
									<td>Dyeing & Finishing Cost</td>
									<td align="right"><? echo number_format($total_dyefin_cost,2); ?></td>
									<td align="right"><? echo number_format($total_dyefin_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>8</td>
									<td>Print/ Emb./Special/Gmt Dying/Other /Wash Cost</td>
									<td align="right"><? echo number_format($total_embelishment_cost,2); ?></td>
									<td align="right"><? echo number_format($total_embelishment_cost_percentage,2); ?></td>
								</tr>
                                <tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Actual Service Cost:</strong></td>
									<td align="right" title="Y/D Cost+AOP Cost+Knit Cost+Dye&Fin Cost+Printing Cost+Embroidery Cost+WashCost+Special+Other ServiceCost(Embellishment Other Cost+Dyeing"><? 
									$total_act_service_cost=$total_yarn_dyeing_cost+$all_over_print_cost+$total_knit_cost+$total_dyefin_cost+$total_embelishment_cost;
									echo number_format($total_act_service_cost,2); ?></td>
									<td align="right"><? echo number_format($total_service_cost_percentage,2); ?></td>
								</tr>
                                
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Service Cost</strong></td>
									<td align="right"><? echo number_format($total_service_cost,2); ?></td>
									<td align="right"><? echo number_format($total_service_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Material & Service</strong></td>
									<td align="right"><? echo number_format($total_service_cost+$total_material_cost,2); ?></td>
									<td align="right"><? echo number_format(($total_service_cost+$total_material_cost)/$total_order_amount*100,2); ?></td>
								</tr>

                                
								<tr bgcolor="<? echo $style; ?>">
									<td>9</td>
									<td>Commercial Cost</td>
									<td align="right"><? echo number_format($total_commercial_cost,2); ?></td>
									<td align="right"><? echo number_format($total_commercial_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>10</td>
									<td>Commision Cost</td>
									<td align="right"><? echo number_format($total_commission_cost,2); ?></td>
									<td align="right"><? echo number_format($total_commission_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>11</td>
									<td>Testing Cost</td>
									<td align="right"><? echo number_format($total_testing_cost,2); ?></td>
									<td align="right"><? echo number_format($total_testing_cost_percentage,2); ?></td>
								</tr>
                                <tr bgcolor="<? echo $style1; ?>">
									<td>12</td>
									<td>Freight Cost</td>
									<td align="right"><? echo number_format($total_freight_cost,2); ?></td>
									<td align="right"><? echo number_format($total_freight_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>13</td>
									<td>Inspection Cost</td>
									<td align="right"><? echo number_format($total_inspection_cost,2); ?></td>
									<td align="right"><? echo number_format($total_inspection_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>14</td>
									<td>Certificate Cost</td>
									<td align="right"><? echo number_format($total_certificate_cost,2); ?></td>
									<td align="right"><? echo number_format($total_certificate_cost_percentage,2); ?></td>
								</tr>
									<tr bgcolor="<? echo $style; ?>">
									<td>15</td>
									<td>Operating Exp.</td>
									<td align="right"><? echo number_format($total_common_oh,2); ?></td>
									<td align="right"><? echo number_format($total_common_oh_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>16</td>
									<td>Courier Cost</td>
									<td align="right"><? echo number_format($total_currier_cost,2); ?></td>
									<td align="right"><? echo number_format($total_currier_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>17</td>
									<td>CM Cost</td>
									<td align="right"><? echo number_format($total_cm_cost,2); ?></td>
									<td align="right"><? echo number_format($total_cm_cost_percentage,2); ?></td>
								</tr>
								
                                <tr bgcolor="<? echo $style1; ?>">
									<td>18</td>
									<td>Total Cost</td>
									<td align="right"><? echo number_format($total_tot_cost,2); ?></td>
									<td align="right"><? echo number_format($total_tot_cost_percentage,2); ?></td>
								</tr>
                                  <tr bgcolor="<? echo $style; ?>">
									<td>19</td>
									<td>Margin</td>
									<td align="right" title="Total Order Value-Total Cost"><? 
									$tot_margin=$total_order_amount-$total_tot_cost;
									$total_tot_margin_percentage=$tot_margin/$total_order_amount*100;
									echo number_format($tot_margin,2); ?></td>
									<td align="right"><? echo number_format($total_tot_margin_percentage,2); ?></td>
								</tr>
								
                                <tr bgcolor="<? echo $style1; ?>">
									<td>20</td>
									<td>Total Order Value</td>
									<td align="right"><? echo number_format($total_order_amount,2); ?></td>
									<td align="right"><? echo number_format($total_order_amount/$total_order_amount*100,2); ?></td>
								</tr>
							</table>
						</td>
						
					</tr>
				</table>
			</div>
			<br/>			
			
			<?
			
			if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else if(str_replace("'","",$cbo_search_date)==3) $caption="PO Insert Date";
					else $caption="Cancelled Date";
					
					
						
			?>
			<fieldset style="width:100%;" id="content_search_panel2">
            		<div>
                       <span style="font-size:20px; font-weight:bold;"><? echo $company_library[$company_name]; ?></span><br/>
                       <span style="font-size:16px; font-weight:bold;">Cost Breakdown Analysis<br/></span>
                       <strong>From  <? echo $caption.' &nbsp;'.$date_from;  ?>  To  <? echo $date_to; ?></strong>
                    </div>
			   <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit'];?>
			<table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
                        <th width="110">Team Leader</th>
						<th width="100">Company</th>
                        <th width="70">Buyer</th>
                        <th width="110">Style</th>
						<th width="70">Job No</th>
                        <th width="110">Gmts Item Name</th>
                        <th width="110">SMV</th>
                        <th width="60">CPM</th>
                        <th width="110">Efficiency</th>
                        <th width="100">PO No</th>
                        <th width="100">Order Status</th>
                        <th width="110">Ship Date</th>
                        <th width="90">Style Qty</th>
                        <th width="60">UOM</th>
                        <th width="90">Total SMV</th>
                        <th width="90">Style Qty (Pcs)</th>
                        <th width="70">Excess Cut %</th>
                        <th width="90">Plan Cut Qty (Pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">Style Value</th>
                        <th width="60">TTL Y/Qty</th>
                        <th width="60">TTL Y/Cost</th>
                        <th width="60">%</th>
                        <th width="60">Fin Fab Purchase Qty</th>
                        <th width="60">Fin Fab Purchase Cost</th>
                        <th width="60">%</th>
                        <th width="100">Trim Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">Y/D Qty</th>
                        <th width="60">Y/D Cost</th>
                        <th width="60">%</th>
                        <th width="60">AOP Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">Total B2B Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">TTL Knit Qty</th>
                        <th width="60">TTL Knit Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">TTL Dye & Fin Qty</th>
                        <th width="60">TTL Dye & Fin Cost</th>
                        <th width="60">%</th>
                        
                        <th width="80">Printing</th>
                        <th width="60">%</th>
						<th width="85">Embroidery</th>
                        <th width="60">%</th>
                        <th width="85">Gmts Dying</th>
                        <th width="60">%</th>
						<th width="80">Special Works</th>
                        <th width="60">%</th>
						<th width="80">Wash Cost</th>
                        <th width="60">%</th>
						<th width="80">Other</th>
                        
                        <th width="100">Total Service Cost</th>
                        <th width="60">%</th>
                        <th width="60">Actual Material Cost</th>
                        <th width="60">%</th>
                        <th width="100">Actual Service Cost</th>
                        <th width="60">%</th>
                       
						
                        <th width="100">Total Mat+Svc Cost</th>
                        <th width="60">%</th>
                        <th width="120">Commercial Cost</th>
                        <th width="60">%</th>
						<th width="100">Testing Cost</th>
						<th width="100">Freight Cost</th>
						<th width="120">Inspection Cost</th>
						<th width="100">Certificate Cost</th>
                        <th width="100">Operating Exp.</th>
                        
						<th width="100">Courier Cost</th>
                       
                       
                        <th width="120">Foreign Comm</th>
						<th width="120">Local Comm</th>
                        <th width="110">Total Comm</th>
                       
						<th width="100" title="<?php echo $cm_cost_formula; ?>">CM Cost</th>
                        <th width="60">%</th>
                        <th width="100">Total Cost</th>
                        <th width="60">%</th>
                        <th width="110">Total Margin</th>
                     
                        <th width="60">Margin %</th>
                     
                        <th width="110">Approved By</th>
                        <th width="110">Appv. Date & Time</th>
                        <th>Appv. Status</th>
					</tr>

				</thead>
			</table>
			<div style="width:<? echo $width+18;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
				
				$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; 	
				$total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
		$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=$total_service_cost=$total_print_amount=$total_gmt_dying_cost=0;
		$total_embroidery_amount=$total_special_amount=$total_other_amount=$total_wash_cost=$total_certificate_cost=$total_currier_cost=0;
		$total_all_over_print_cost=$total_b2b_cost=$total_act_material_cost=$total_act_service_cost=$total_material_service_cost=$total_order_amount=$total_cost=$total_common_oh=0;
					

			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				else if(str_replace("'","",$cbo_search_date)==4)
				{
					$update_date=explode(" ",$row[csf('update_date')]);
					$ship_po_recv_date=change_date_format($update_date[0]);
				}
				$dzn_qnty=$costing_per_arr[$row[csf('job_no')]];
				$costing_date=$costing_library[$row[csf('job_no')]];
				

				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$order_qty=$row[csf('po_quantity')];
				$order_uom=$row[csf('order_uom')];
				$costing_date=$row[csf('costing_date')];

				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];

				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];

			
				$total_plancut_amount+=$plancut_value;
				$pid=$row[csf('po_id')];
			
				$commercial_cost=$commercial_costing_arr[$pid];
				$yarn_costing=$yarn_costing_arr[$pid];
				$yarn_req_qty=$yarn_req_qty_arr[$pid];
				$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$pid][2]);
				$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$pid][2]);
				
				$fab_purchase_knit_qty=array_sum($fabric_purchase_qty_arr['knit']['grey'][$pid][2]);
				$fab_purchase_woven_qty=array_sum($fabric_purchase_qty_arr['woven']['grey'][$pid][2]);
				
				$conv_cost_poWise=array_sum($conversion_costing_po_arr[$pid]);
				$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$pid][30]);
				$heat_setting_cost=array_sum($conversion_costing_arr_process[$pid][33]);
				
						
				$trim_amount= $trims_costing_arr[$pid];
				$print_amount=$emblishment_costing_arr_name[$pid][1];
				$embroidery_amount=$emblishment_costing_arr_name[$pid][2];
				$special_amount=$emblishment_costing_arr_name[$pid][4];
				$wash_cost=$emblishment_costing_arr_name_wash[$pid][3];
				$gmt_dying_amount=$emblishment_costing_arr_name[$pid][5];
				$other_amount=$emblishment_costing_arr_name[$pid][99];
				$foreign=$commission_costing_arr[$pid][1];
				$local=$commission_costing_arr[$pid][2];
				$test_cost=$other_costing_arr[$pid]['lab_test'];
				$freight_cost=$other_costing_arr[$pid]['freight'];
				$inspection=$other_costing_arr[$pid]['inspection'];
				$certificate_cost=$other_costing_arr[$pid]['certificate_pre_cost'];
				$common_oh=$other_costing_arr[$pid]['common_oh'];
				$currier_cost=$other_costing_arr[$pid]['currier_pre_cost'];
				$cm_cost=$other_costing_arr[$pid]['cm_cost'];
				$dfc_cost=$other_costing_arr[$pid]['deffdlc_cost'];//$deffdlc_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;
				$interest_cost=$interest_cost_arr[$pid]*$order_qty_pcs;
				$incometax_cost=$incometax_cost_arr[$pid]*$order_qty_pcs;
				
				if($print_button==50){$action='preCostRpt'; } //report_btn_1;
				else if($print_button==51){$action='preCostRpt2';} //report_btn_2;
				else if($print_button==52){$action='bomRpt';} //report_btn_3;
				else if($print_button==63){$action='bomRpt2';} //report_btn_4;
				else if($print_button==156){$action='accessories_details';} //report_btn_5;
				else if($print_button==157){$action='accessories_details2';} //report_btn_6;
				else if($print_button==158){$action='preCostRptWoven';} //report_btn_7;
				else if($print_button==159){$action='bomRptWoven';} //report_btn_8;
				else if($print_button==170){$action='preCostRpt3';} //report_btn_9;
				else if($print_button==171){$action='preCostRpt4';} //report_btn_10;
				else if($print_button==173){$action='preCostRpt5';} //report_btn_10;
				else if($print_button==211){$action='mo_sheet';}
				else if($print_button==142){$action='preCostRptBpkW';}
				else if($print_button==197){$action='bomRpt3';}
				else if($print_button==192){$action='checkListRpt';}
				else if($print_button==221){$action='fabric_cost_detail';}
				else if($print_button==238){$action='summary';}
				else if($print_button==215){$action='budget3_details';}
				else $print_button=$row[csf('po_number')];

				$report_button="precost_bom_pop('".$action."','".$row[csf('job_no')]."',".$row[csf('company_name')].",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$costing_date."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."');"; 
				
					
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					 <td width="40"><? echo $i; ?></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $team_member_arr[$row[csf('team_leader')]]; ?></div></td>

                     <td width="100"><p><? echo $company_library[$row[csf('company_name')]]; ?></p></td>
                     <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('style_ref_no')]; ?></div></td>
					 <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110" align="right"><p><? echo $row[csf('set_smv')]; ?></p></td>
                     <td width="60" align="right">
						<? 
							$cpmVal=$smv_eff_arr[$row[csf('job_no')]]['cpm']/$smv_eff_arr[$row[csf('job_no')]]['exc_rate'];
							echo  number_format($cpmVal*100/$smv_eff_arr[$row[csf('job_no')]]['eff'],3); 
                        ?>
                     </td>
                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $smv_eff_arr[$row[csf('job_no')]]['eff']; ?></div></td>
                     <td width="100" align="center"><p><a href='##'  onclick="<? echo $report_button; ?>"><? echo $row[csf('po_number')]; ?></a><? //echo $row[csf('po_number')]; ?></p></td>
                    <td width="100" align="center"><p><? echo $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                    <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo change_date_format($ship_po_recv_date); ?></div></td>
                  
                    <td width="90" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                    <td width="60" align="center"><p><? echo $unit_of_measurement[$order_uom]; ?></p></td>
                    <td width="90" align="right"><p><? echo number_format($order_qty*$row[csf('set_smv')],2); ?></p></td>

                    <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                    <td width="70" align="right"><div style="word-wrap:break-word; width:70px"><? echo $row[csf('excess_cut')]; ?></div></td>
                  
                    <td width="90" align="right"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('plan_cut')]; ?></div></td>
                    <td width="70" align="right"><p><? echo number_format($order_value/$order_qty,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>

                    <?
						$avg_rate=$yarn_costing/$yarn_req_qty;
						$yarn_cost_percent=($yarn_costing/$order_value)*100;
						$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
						$fab_purchase_qty=$fab_purchase_knit_qty+$fab_purchase_woven_qty;
						
						$tot_fabric_cost=$yarn_costing+$fab_purchase+$conv_cost_poWise;
						$knit_cost=0;$knite_qty=0;
						foreach($knit_cost_arr as $process_id)
						{ //conversion_job_amount_arr_process
							$knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);
							$knite_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$process_id]);
						}
						$all_over_print_cost=$all_over_print_qty=0;
						foreach($aop_cost_arr as $aop_process_id)
						{
							$all_over_print_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);
							$all_over_print_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$aop_process_id]);
						}
							
						$yarn_dye_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
						$yarn_dye_qty=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][30]);
						
						$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;

						$fabric_dyeing_cost=0;
						$fabric_dyeing_qty=$fab_purchase_woven_qty+$fab_purchase_knit_qty;
						/*foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
							$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);
							$fabric_dyeing_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$fab_process_id]);
						}*/
						foreach($conversion_cost_head_array as $key=>$val)
						{
							if (!in_array($key, $not_process_id_print_array)) 
							{
								$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$key]);
							}
						}
						
						
						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_finish=0;
						foreach($fab_finish_cost_arr as $fin_process_id)
						{
							$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fin_process_id]);
						}
						$washing_cost=0;$washing_qty=0;
						foreach($washing_cost_arr as $w_process_id)
						{
							$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);
							$washing_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$w_process_id]);
						}
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;
						$finance_chrg=$order_value*$smv_eff_arr[$row[csf('po_id')]]['finPercent']/100;

						$total_material_cost=$total_cost_new;
						$others_cost_value = $total_cost -($cm_cost+$freight_cost+$commercial_cost+($foreign+$local));
						$net_order_val=$order_value-(($foreign+$local)+$commercial_cost+$freight_cost);
						$cm_value=$net_order_val-$others_cost_value;

						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100;
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;

						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
						if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
						if($commercial_cost<=0) $color_com="red"; else $color_com="";

					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;
					$total_gmt_dying_cost+=$gmt_dying_amount;
					$total_test_cost_amount+=$test_cost;


					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_meterial_amount+=$total_material_cost;
					$total_cm_new_amount+=$tot_cmValue;
					$total_cm_amount+=$cm_cost;
					$total_margin_amount+=$tot_margin;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];

					if($trim_amount<=0) $color_trim="red"; else $color_trim="";
					if($cm_cost<=0) $color="red"; else $color="";
					$smv=0; $eff=0; $cpm=0; $cost_dzn_cm_title=0;
					$smv=$smv_eff_arr[$row[csf('job_no')]]['smv'];
					$eff=$smv_eff_arr[$row[csf('job_no')]]['eff'];
					$cpm=$smv_eff_arr[$row[csf('job_no')]]['cpm'];
					$cost_dzn_cm_title='Swe. SMV: '.$smv.'; Swe. EFF: '.$eff.'; CPM: '.$cpm;
						//echo $fabric_cost_pcs_arr[$row[csf('po_id')]].'='.$plan_cut_qnty;
					?>


                    <td width="60" align="right"><p><? echo number_format($yarn_req_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($yarn_costing,2); ?></p></td>
                    <td width="60" align="right" title="YarnCost/PoValue*100"><p><? echo number_format($yarn_costing/$order_value*100,2); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($fab_purchase_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fab_purchase,2); ?></p></td>
                    <td width="60" align="right" title="FabCost/PoValue*100"><p><? echo number_format($fab_purchase/$order_value*100,2); ?></p></td>
                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><? echo number_format($trim_amount,2);?></td>
					<td width="60" align="right" title="TrimCost/PoQty*100"><p><? echo number_format($trim_amount/$order_value*100,2); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($yarn_dye_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($yarn_dye_cost,2); ?></p></td>
                    <td width="60" align="right" title="YCost/PoValue*100"><p><? echo number_format($yarn_dye_cost/$order_value*100,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($all_over_print_cost,2); ?></p></td>
                    <td width="60" align="right" title="AopCost/PoValue*100"><p><? echo number_format($all_over_print_cost/$order_value*100,2); ?></p></td>
                    
                     <td width="60" align="right" title="Yarn+Purchase+Trims+YarnDye+AOP Cost"><p><?
					 $tot_b2b_cost=$yarn_costing+$fab_purchase+$trim_amount+$yarn_dye_cost+$all_over_print_cost;
					
					  echo number_format($tot_b2b_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot B2bCost/PoValue*100"><p><? echo number_format($tot_b2b_cost/$order_value*100,2); ?></p></td>
                    
                    
                    <td width="60" align="right"><p><? echo number_format($knite_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($knit_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot KnitCost/PoValue*100"><p><? echo number_format($knit_cost/$order_value*100,2); ?></p></td>
                    <td width="60" align="right" title="Pre Cost Fabric"><p><? echo number_format($fabric_dyeing_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fabric_dyeing_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot DyeCost/PoValue*100"><p><? echo number_format($fabric_dyeing_cost/$order_value*100,2); ?></p></td>
                     <td width="80" align="right"><? echo number_format($print_amount,2); ?></td>
                     <td width="60" align="right" title="PrintCost/PoValue*100"><p><? echo number_format($print_amount/$order_value*100,2); ?></p></td>
					 <td width="85" align="right"><? echo number_format($embroidery_amount,2); ?></td>
                 	  <td width="60" align="right" title="EmbroCost/PoValue*100"><p><? echo number_format($embroidery_amount/$order_value*100,2); ?></p></td>
                     <td width="85" align="right"><? echo number_format($gmt_dying_amount,2); ?></td>
                      <td width="60" align="right" title="GmtDyingCost/PoValue*100"><p><? echo number_format($gmt_dying_amount/$order_value*100,2); ?></p></td>
					 <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                    <td width="60" align="right" title="SpecialCost/PoValue*100"><p><? echo number_format($special_amount/$order_value*100,2); ?></p></td>
					 <td width="80" align="right"><? echo number_format($wash_cost,2); ?></td>
                    <td width="60" align="right" title="WashCost/PoValue*100"><p><? echo number_format($wash_cost/$order_value*100,2); ?></p></td>
					 <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
                     <td width="100" align="right" title="Knit+Fin Dying+Print_Embroidery+Special+GmtDying+Wash+OtherCost"><p><? 
					 $tot_service_cost=$knit_cost+$fabric_dyeing_cost+$print_amount+$embroidery_amount+$special_amount+$gmt_dying_amount+$wash_cost+$other_amount;
					 $total_service_cost+=$tot_service_cost; echo  number_format($tot_service_cost,2);
					 
					  $tot_act_material_cost=$yarn_costing+$fab_purchase+$trim_amount;
					   $total_act_material_cost+=$tot_act_material_cost;
					   $tot_act_service_cost=$yarn_dye_cost+$all_over_print_cost+$knit_cost+$fabric_dyeing_cost+$print_amount+$special_amount+$other_amount+$embroidery_amount+$gmt_dying_amount+$wash_cost;
					    $total_act_service_cost+=$tot_act_service_cost;
						 $tot_material_service_cost=$tot_b2b_cost+$tot_service_cost;
						 $total_material_service_cost+=$tot_material_service_cost;
					  ?></p></td>
                    <td width="60" align="right" title="Tot ServiceCost/PoValue*100"><p><? echo number_format($tot_service_cost/$order_value*100,2); ?></p></td>
                    <td width="60" align="right" title="Yarn+Purchase+Trims Cost"><p><?  echo number_format($tot_act_material_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot Act. MaterialCost/PoValue*100"><p><? echo number_format($tot_act_material_cost/$order_value*100,2); ?></p></td>
                    <td width="100" align="right" title="YarnDye+Aop+Kint+FabricDye+Print+Embro+GmtDying+Wash Cost+Special+Other Cost"><p><? echo number_format($tot_act_service_cost,2); ?></p></td>
                 	<td width="60" align="right" title="Tot Act.ServiceCost/PoValue*100"><p><? echo number_format($tot_act_service_cost/$order_value*100,2); ?></p></td>
                    <td width="100" align="right" title="Tot B2B+Service Cost"><p><?  echo number_format($tot_material_service_cost,2); ?></p></td>
                     <td width="60" align="right" title="Tot Material+ServiceCost/PoValue*100"><p><? echo number_format($tot_material_service_cost/$order_value*100,2); ?></p></td>
					 <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
					 <td width="60" align="right" title="Tot commercial costt/PoValue*100"><p><? echo number_format($commercial_cost/$order_value*100,2); ?></p></td>
                     <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
					 <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($inspection,2);?></td>
					 <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
					 <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>
                     <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
					 <td width="120" align="right"><? echo number_format($local,2) ?></td>
                     <td width="110" align="right"><? $tot_forg_local=$foreign+$local; echo number_format($tot_forg_local,2);  ?></td>
					 <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
					<td width="60" align="right" title="Tot CM cost/PoValue*100"><p><? echo number_format($cm_cost/$order_value*100,2); ?></p></td>
					 <td width="100" align="right" title="Tot Mat+Service+Commercial+Test+Freight+Inspection+Certificate+HO Exp.+Currier+Foreign+Local+CM Cost"><p><?
					 $tot_cost=$tot_material_service_cost+$commercial_cost+$test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$tot_forg_local+$cm_cost;
					 $total_cost+=$tot_cost;
					  echo number_format($tot_cost,2); ?></p></td>
					 <td width="60" align="right" title="Tot Cost/PoValue*100"><p><? echo number_format($tot_cost/$order_value*100,2); ?></p></td>
                     <td width="110" align="right" title="PO Value-Total Cost"><? $tot_margin=$order_value-$tot_cost; echo  number_format($tot_margin,2); ?></td>
                     <td width="60" align="right"><div style="word-wrap:break-word; width:60px"><? echo number_format(($tot_margin/$order_value*100),2); ?></div></td>
                      <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo $lib_user_arr[$row[csf('approved_by')]]; ?></div></td>
                      <td width="110" align="center"><div style="word-wrap:break-word;"><? echo $row[csf('approved_date')]; ?></div></td>
                      <td><? echo $approval_type_arr[$row[csf('approved')]];?></td>
					<?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";
						else $color_pl="";
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
						$expect_variance=$total_profit-$expected_profit_per;

					?>
				  </tr>
				<?
				$total_order_qty_pcs+=$order_qty_pcs;
				$total_plan_cut_qnty_pcs+=$plan_cut_qnty;
				$total_order_qty+=$row[csf('po_quantity')];
				$total_order_amount+=$order_value;
				$total_yarn_required+=$yarn_req;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$total_all_over_print_cost+=$all_over_print_cost;
				$total_trim_cost+=$trim_amount;$total_foreign_cost+=$foreign;$total_local_cost+=$local;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;
				 $total_b2b_cost+=$tot_b2b_cost;
				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$gmt_dying_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_cm_value+=$cm_value;
				$total_cm_new_value+=$tot_cmValue;
				$total_forg_local_amount_new+=$tot_forg_local;
				$total_FOB_val_amount+=$tot_netFOB;

				$total_margin_new_amount+=$tot_margin;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_finance_chrg_cost+=$finance_chrg;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;

				$total_yarn_qty+=$yarn_req_qty;
				$total_knite_qty+=$knite_qty;
				$total_fabric_dyeing_qty+=$fabric_dyeing_qty;
				$total_washing_qty+=$washing_qty;

				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			?>
			</table>
			</div>
			<table class="tbl_bottom" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="60">&nbsp;</td>
                    <td width="110">Total:</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
					<td width="110">&nbsp;</td>
                    <td width="90">&nbsp;</td>
					<td width="60">&nbsp;</td>

                   
					<td width="90">&nbsp;</td>
                    <td width="90" align="right" id="total_order_qnty_pcs"><? echo number_format($total_order_qty_pcs,2); ?></td>
                    <td width="70">&nbsp;</td>
					<td width="90"><? echo number_format($total_plan_cut_qnty_pcs,2); ?></td>
					

                    <td width="70" align="right" id=""><? //echo number_format($total_order_amount,2); ?></td>

                    <td width="100" id="total_order_amount"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="60"><? echo number_format($total_yarn_qty); ?></td>
                    <td width="60"><? echo number_format($total_yarn_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                     <td width="60"><? //echo number_format($total_yarn_qty); ?></td>
                    <td width="60"><? //echo number_format($total_yarn_cost,2); ?></td>
                    
                    
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_all_over_print_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60" id="total_b2b_cost"><? echo number_format($total_b2b_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                          
                    <td width="60"><? echo number_format($total_knite_qty,2);//number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_knitting_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_fabric_dyeing_cost,2);//number_format($total_washing_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_fabric_dyeing_qty,2);//number_format($total_washing_qty,2); ?></td>
                    <td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_embroidery_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_wash_cost,2); ?></td>
                    <td width="85" align="right" id="total_gmt_dying_amount"><? echo number_format($total_gmt_dying_cost,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
                    <td width="60" align="right"><? //echo number_format($tot_fab_cost,2);total_special_amount ?></td>

					<td width="80" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
				
					<td width="60" align="right" id=""><? //echo number_format($total_commercial_cost,2); ?></td>
					<td width="80" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
                    <td width="100" id="total_service_cost"><? echo number_format($total_service_cost,2); ?></td>
                    <td width="60" align="right" id=""><? //echo number_format($total_test_cost_amount,2); ?></td>
					<td width="60" align="right" id="total_act_material_cost"><? echo number_format($total_act_material_cost,2); ?></td>
                    <td width="60" align="right"><? //echo number_format($tot_interest_cost,2);?></td>
					<td width="100" align="right" id="total_act_service_cost"><? echo number_format($total_act_service_cost,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_material_service_cost,2); ?></td>
                    <td width="100" align="right" id="total_material_service_cost"><? echo number_format($total_material_service_cost,2); ?></td>

					<td width="60" align="right" id=""><? //echo number_format($total_currier_amount,2); ?></td>
					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2);?></td>
					<td width="60" align="right"><? //echo number_format($tot_interest_cost,2);?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2);?></td>

                    <td width="100" align="right" id="total_freight_cost"><? echo number_format($total_freight_cost,2); ?></td>


                    <td width="120" align="right" id="total_inspection"><? echo number_format($total_inspection,2); ?></td>
					<td width="100" align="right" id="total_certificate_cost"><? echo number_format($total_certificate_cost,2); ?></td>
                    <td width="100" align="right" id="total_common_oh"><? echo number_format($total_common_oh_amount,2); ?></td>
                    <td width="100" align="right" id="total_currier_cost"><? echo number_format($total_currier_cost,2); ?></td>
                   

                    <td width="120" align="right" id="total_foreign_cost"><? echo number_format($total_foreign_cost,2); ?></td>
					<td width="120" id="total_local_cost"><? echo number_format($total_local_cost,2); //total_FOB_val_amount?></td>
					<td width="110"><? echo number_format($total_forg_local_amount_new,2); //total_FOB_val_amount?></td>
					
                    <td width="100"><? echo number_format($total_cm_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2); ?></td>
                    <td width="100"><? echo number_format($total_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2);?></td>
                    <td width="110"><? echo number_format($total_margin_new_amount,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2); ?></td>
                  
                    <td width="110"><? //echo number_format($total_cm_cost,2); ?></td>
                    <td width="110"><? //echo number_format($total_cm_cost,2); ?></td>
                   
                          
                    <td>&nbsp;</td>

				</tr>
			</table>
			<table>
				<tr>
					<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_cost_up,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');
					?>
					<input type="hidden" id="graph_data" value=""/>
				</tr>
			</table>
			<br>
            <a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>
            <div style="width:600px; display:none" id="yarn_summary" >
            <div id="data_panel2" align="center" style="width:500px">
					 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
				 </div>
            <table class="rpt_table" width="590" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                	<tr>
                                    	<th colspan="7">Yarn Cost Summary</th>
                                    </tr>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="140">Composition</th>
                                        <th width="60">Yarn Count</th>
                                        <th width="80">Type</th>
                                        <th width="100">Req. Qty</th>
                                        <th width="70">Avg. Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <?
								$yarn_des_data_summ_arr=array();
								foreach($yarn_des_data as $count=>$count_value)
                                {
									foreach($count_value as $Composition=>$composition_value)
									{
											//ksort($composition_value);
											foreach($composition_value as $percent=>$percent_value)
											{
													foreach($percent_value as $type=>$type_value)
													{
														$compo_val=$composition[$Composition];
														$count_name=$yarn_count_library[$count];
														$yarn_type_name=$yarn_type[$type];
														
													$yarn_des_data_summ_arr[$compo_val][$count_name][$yarn_type_name][$percent]['qty']=$type_value['qty'];	
													$yarn_des_data_summ_arr[$compo_val][$count_name][$yarn_type_name][$percent]['amount']=$type_value['amount'];
													}
											}
									}
								}
								
                                $s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
								ksort($yarn_des_data_summ_arr);
                                foreach($yarn_des_data_summ_arr as $CompositionKey=>$comp_value)
                                {
									ksort($comp_value);
									foreach($comp_value as $count=>$count_value)
									{
											ksort($count_value);
											foreach($count_value as $type=>$type_value)
											{
													foreach($type_value as $percent=>$value)
													{
                                    if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    //$yarn_desc=explode("**",$key);

                                    $tot_yarn_req_qnty+=$value['qty'];
                                    $tot_yarn_req_amnt+=$value['amount'];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
                                        <td><? echo $s; ?></td>
                                        <td><div style="word-wrap:break-word; width:140px"><? echo $CompositionKey." ".$percent."%"; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:60px"><? echo $count; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:80px"><? echo $type; ?></div></td>
                                        <td align="right"><? echo number_format($value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($value['amount']/$value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($value['amount'],2); ?></td>
                                    </tr>
                                    <?
                                    $s++;
													}
												  }
												 }
                                				}
                                ?>
                                <tfoot>
                                    <th colspan="4" align="right">Total</th>
                                    <th align="right"><? echo number_format($tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt,2); ?></th>
                                </tfoot>
                            </table>
                     </div>
			</fieldset>
			</div>
			<?
		}
	
	}
	else
	{
			$style1="#E9F3FF";
			$style="#FFFFFF";
			$sql_budget2="select a.job_no_prefix_num, b.insert_date, a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price, b.grouping, b.file_no,a.id as job_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $file_con_b $ref_con_b $order_by";

			$sql_budget_data=sql_select($sql_budget2);
			foreach ($sql_budget_data as  $value) {
				 $jobIds.=$value[csf('job_id')].",";
				 $poIds.=$value[csf('po_id')].",";
				 $jobNos.=$value[csf('job_no')].",";
			} 
			$poIdArr =array_unique(explode(",",rtrim($poIds,",")));
			$jobIdArr =array_unique(explode(",",rtrim($jobIds,",")));;

			$poIds =implode(",",array_unique(explode(",",rtrim($poIds,","))));
			$jobIds =implode(",",array_unique(explode(",",rtrim($jobIds,","))));;
			//print_r($po_id_array);die;
			$con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=23");
			oci_commit($con);
			
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 23, 1, $poIdArr, $empty_arr);//PO ID Ref from=1
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 23, 2, $jobIdArr, $empty_arr);//Sales ID Ref from=2
	 

			$fab_precost_arr=array();$commission_array=array();$knit_arr=array(); $fabriccostArray=array(); $fab_emb=array();$fabric_data_Array=array();$asking_profit_arr=array(); $yarncostArray=array(); $yarn_desc_array=array();  $costing_date_arr=array();

			$yarncostDataArray=sql_select("select a.job_no, a.count_id, a.type_id, sum(a.cons_qnty) as cons_qnty, sum(a.amount) as amount from wo_pre_cost_fab_yarn_cost_dtls a,gbl_temp_engine g where a.job_id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=23 and a.status_active=1 and a.is_deleted=0   group by a.job_no, a.count_id, a.type_id");

		 
			
			foreach($yarncostDataArray as $yarnRow)
			{
				$yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
			}
			 
			/*$costing_date_sql=sql_select("select job_no, costing_date from wo_pre_cost_mst where status_active=1 and is_deleted=0");
			foreach($costing_date_sql as $row )
			{
				$costing_date_arr[$row[csf('job_no')]]=$row[csf('costing_date')];
			}*/

			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $company_con_id");//$date_max_profit
			 
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					//$newDate=add_date(str_replace("'","",$applying_period_date),$j);
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);

					$asking_profit_arr[$ask_row[csf('company_id')]][$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
					//$asking_profit_arr[$newDate]['max_profit']=$ask_row[csf('max_profit')];
				}
			}
			//var_dump($asking_profit_arr);die;
			$costing_date_sql=sql_select("select a.job_no, a.costing_date,b.company_name from wo_pre_cost_mst a,wo_po_details_master b,gbl_temp_engine g where a.job_no=b.job_no and b.id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=23 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 ");
			
		
			foreach($costing_date_sql as $row )
			{
				$cost_date=change_date_format($row[csf('costing_date')],'','',1);
				//echo $cost_date=change_date_format($row[csf('costing_date')]);
				$costing_date_arr[$row[csf('job_no')]]['ask']=$asking_profit_arr[$row[csf('company_name')]][$cost_date]['asking_profit'];
				$costing_date_arr[$row[csf('job_no')]]['max']=$asking_profit_arr[$row[csf('company_name')]][$cost_date]['max_profit'];
			}
			//var_dump($costing_date_arr);die;

			$fab_arr=sql_select("select a.job_no, a.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, sum(a.requirment) as requirment, sum(a.pcs) as pcs from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fabric_cost_dtls b,gbl_temp_engine g where a.pre_cost_fabric_cost_dtls_id=b.id and a.job_no=b.job_no and b.job_id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=23 and  b.status_active=1 and b.is_deleted=0   group by a.po_break_down_id, a.pre_cost_fabric_cost_dtls_id, a.job_no");
			 
			foreach($fab_arr as $row_pre)
			{
				$fab_precost_arr[$row_pre[csf('job_no')]][$row_pre[csf('po_break_down_id')]].=$row_pre[csf('requirment')]."**".$row_pre[csf('pcs')].",";
			}
			
			$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b,gbl_temp_engine g where a.job_no=b.job_no and a.job_id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=23  and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
			
			foreach($fabricDataArray as $fabricRow)
			{
				$fabric_data_Array[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('yarn_cons_qnty')]."**".$fabricRow[csf('yarn_amount')].",";
			}//Pre cost end

			$data_array_emb=("select  a.job_no,
			sum(CASE WHEN a.emb_name=1 THEN a.amount END) AS print_amount,
			sum(CASE WHEN a.emb_name=2 THEN a.amount END) AS embroidery_amount,
			sum(CASE WHEN a.emb_name=3 THEN a.amount END) AS wash_amount,
			sum(CASE WHEN a.emb_name=4 THEN a.amount END) AS special_amount,
			sum(CASE WHEN a.emb_name=5 THEN a.amount END) AS other_amount
			from  wo_pre_cost_embe_cost_dtls a,gbl_temp_engine g  where   a.job_id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=23 and a.status_active=1 and  a.is_deleted=0   group by a.job_no");
			
			$embl_array=sql_select($data_array_emb);
			foreach($embl_array as $row_emb)
			{
				$fab_emb[$row_emb[csf('job_no')]]['print']=$row_emb[csf('print_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['embroidery']=$row_emb[csf('embroidery_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['special']=$row_emb[csf('special_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['other']=$row_emb[csf('other_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['wash']=$row_emb[csf('wash_amount')];
			}

			$fabriccostDataArray=sql_select("select a.job_no, a.costing_per_id, a.trims_cost, a.embel_cost, a.cm_cost, a.commission, a.common_oh, a.lab_test, a.inspection, a.freight, a.comm_cost,a.certificate_pre_cost,a.currier_pre_cost from wo_pre_cost_dtls a,gbl_temp_engine g  where a.job_id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=23 and a.status_active=1 and a.is_deleted=0   ");
			
			foreach($fabriccostDataArray as $fabRow)
			{
				$fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
				$fabriccostArray[$fabRow[csf('job_no')]]['trims_cost']=$fabRow[csf('trims_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
				$fabriccostArray[$fabRow[csf('job_no')]]['common_oh']=$fabRow[csf('common_oh')];
				$fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
				$fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
				$fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
				$fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['certificate_pre_cost']=$fabRow[csf('certificate_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['c_cost']=$fabRow[csf('cm_cost')];
			}

			$knit_data=sql_select("select a.job_no,
			sum(CASE WHEN a.cons_process=1 THEN a.amount END) AS knit_charge,
			sum(CASE WHEN a.cons_process=2 THEN a.amount END) AS weaving_charge,
			sum(CASE WHEN a.cons_process=3 THEN a.amount END) AS knit_charge_collar_cuff,
			sum(CASE WHEN a.cons_process=4 THEN a.amount END) AS knit_charge_feeder_stripe,
			sum(CASE WHEN a.cons_process in(64,82,89) THEN a.amount END) AS washing_cost,
			sum(CASE WHEN a.cons_process in(35,36,37) THEN a.amount END) AS all_over_cost,
			sum(CASE WHEN a.cons_process=30 THEN a.amount END) AS yarn_dyeing_cost,
			sum(CASE WHEN a.cons_process=33 THEN a.amount END) AS heat_setting_cost,
			sum(CASE WHEN a.cons_process in(25,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79) THEN a.amount END) AS fabric_dyeing_cost,
			sum(CASE WHEN a.cons_process in(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129) THEN a.amount END) AS fabric_finish_cost
			from wo_pre_cost_fab_conv_cost_dtls a,gbl_temp_engine g where a.job_id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=23 and a.status_active=1 and a.is_deleted=0    group by a.job_no");
		
			foreach($knit_data as $row_knit)
			{
				$knit_arr[$row_knit[csf('job_no')]]['knit']=$row_knit[csf('knit_charge')];
				$knit_arr[$row_knit[csf('job_no')]]['weaving']=$row_knit[csf('weaving_charge')];
				$knit_arr[$row_knit[csf('job_no')]]['collar_cuff']=$row_knit[csf('knit_charge_collar_cuff')];
				$knit_arr[$row_knit[csf('job_no')]]['feeder_stripe']=$row_knit[csf('knit_charge_feeder_stripe')];
				$knit_arr[$row_knit[csf('job_no')]]['washing']=$row_knit[csf('washing_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['all_over']=$row_knit[csf('all_over_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['fabric_dyeing']=$row_knit[csf('fabric_dyeing_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['yarn_dyeing']=$row_knit[csf('yarn_dyeing_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['heat']=$row_knit[csf('heat_setting_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['fabric_finish']=$row_knit[csf('fabric_finish_cost')];
			}
		
			$data_array=sql_select("select  a.job_no,
			sum(CASE WHEN a.particulars_id=1 THEN a.commission_amount END) AS foreign_comm,
			sum(CASE WHEN a.particulars_id=2 THEN a.commission_amount END) AS local_comm
			from  wo_pre_cost_commiss_cost_dtls a,gbl_temp_engine g where a.job_id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=23 and  a.status_active=1 and a.is_deleted=0   group by a.job_no");// quotation_id='$data'
			foreach($data_array as $row_fl )
			{
				$commission_array[$row_fl[csf('job_no')]]['foreign']=$row_fl[csf('foreign_comm')];
				$commission_array[$row_fl[csf('job_no')]]['local']=$row_fl[csf('local_comm')];
			}
			//Trim cost from BOM calculation
			$trim_qty_arr=array();$trim_po_country_arr=array();$trim_poqty_country_arr=array();
			$dtls_data=sql_select("select a.po_break_down_id,a.country_id,a.wo_pre_cost_trim_cost_dtls_id from wo_pre_cost_trim_co_cons_dtls a,gbl_temp_engine g  where a.po_break_down_id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (1) and g.entry_form=23 and   a.cons !=0 ");
			 
			foreach($dtls_data as $row)
			{

			 $trim_po_country_arr[$row[csf('wo_pre_cost_trim_cost_dtls_id')]].=$row[csf('po_break_down_id')].'**'.$row[csf('country_id')].',';

			}//var_dump($trim_po_country_arr);
			$sql_trim = "select a.id, a.job_no, a.trim_group,a.description,a.brand_sup_ref, a.cons_uom, a.cons_dzn_gmts, a.rate, a.amount, a.apvl_req, a.nominated_supp,a.status_active
			from wo_pre_cost_trim_cost_dtls a,gbl_temp_engine g where a.job_id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=23 and a.status_active=1    ";//where job_no=".$$all_job_id."
			//echo $all_job_id;die;
			$sql_po_qty=sql_select("select b.id,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,gbl_temp_engine g where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id     and a.id=g.ref_val and  g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=23 and a.status_active=1 and b.status_active=1 and c.status_active=1   group by b.id,a.total_set_qnty");
			foreach($sql_po_qty as $row)
			{

			$trim_poqty_country_arr[$row[csf('id')]]=$row[csf('order_quantity_set')];

			}
			
			$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst", "job_no", "costing_per");
			
			$data_array_trim=sql_select($sql_trim);
			$total_trims_cost=0;
            foreach( $data_array_trim as $row_trim )
            {

			   $trim_data=explode(",",$trim_po_country_arr[$row_trim[csf('id')]]);
			   foreach($trim_data as $val )
			   {
				 $exp_data=explode("**",$val);
				 $po_id=$exp_data[0];
				 $country_id=$exp_data[1];
				 //echo $po_id.'='. $country_id;
				   if($country_id==0)
					 {
						$po_qty=$trim_poqty_country_arr[$po_id];
					 }
					 else
					 {
						$po_qty=$trim_poqty_country_arr[$po_id];
					 }
					// echo $po_qty.'dd';
					 $costing_per_qty=0;
					$costing_per=$costing_per_arr[$row_trim[csf('job_no')]];
						if($costing_per==1)
						{
						$costing_per_qty=12	;
						}
						if($costing_per==2)
						{
						$costing_per_qty=1;
						}
						if($costing_per==3)
						{
						$costing_per_qty=24	;
						}
						if($costing_per==4)
						{
						$costing_per_qty=36	;
						}
						if($costing_per==5)
						{
						$costing_per_qty=48	;
						}

					 $trim_qty_arr[$po_id]+=$row_trim[csf('amount')]/$costing_per_qty*$po_qty;
			   }
			} //Trims End

			
			$result_sql_budget2=sql_select($sql_budget2); $tot_rows_budget=count($result_sql_budget2); $budget_data_arr=array();

			?>
			<script>
			var order_amt=document.getElementById('total_order_amount2').innerHTML.replace(/,/g ,'');
			document.getElementById('yarn_cost').innerHTML=document.getElementById('total_yarn_cost2').innerHTML;
			document.getElementById('yarn_cost_per').innerHTML=document.getElementById('total_yarn_cost_per').innerHTML + ' %';
			document.getElementById('purchase_cost').innerHTML=document.getElementById('total_purchase_cost').innerHTML;
			document.getElementById('purchase_cost_per').innerHTML=number_format_common((document.getElementById('total_purchase_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('knit_cost').innerHTML=document.getElementById('total_knitting_cost').innerHTML;
			document.getElementById('knit_cost_per').innerHTML=number_format_common((document.getElementById('total_knitting_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('ydyeing_cost').innerHTML=document.getElementById('total_yarn_dyeing_cost').innerHTML;
			document.getElementById('ydyeing_cost_per').innerHTML=number_format_common((document.getElementById('total_yarn_dyeing_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('aop_cost').innerHTML=document.getElementById('all_over_print_cost').innerHTML;
			document.getElementById('aop_cost_per').innerHTML=number_format_common((document.getElementById('all_over_print_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			var dyefin_val=(parseFloat(document.getElementById('total_fabric_dyeing_cost4').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_finishing_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_heat_setting_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_washing_cost').innerHTML.replace(',','')));
			document.getElementById('dyefin_cost').innerHTML=number_format_common(dyefin_val,2);
			document.getElementById('dyefin_cost_per').innerHTML=number_format_common((dyefin_val/order_amt)*100,2) + ' %';
			document.getElementById('trim_cost').innerHTML=document.getElementById('total_trim_cost').innerHTML;
			document.getElementById('trim_cost_per').innerHTML=number_format_common((document.getElementById('total_trim_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			var embelishment_val=(parseFloat(document.getElementById('total_print_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_embroidery_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_special_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_wash_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_other_amount').innerHTML.replace(',','')));
			document.getElementById('embelishment_cost').innerHTML=number_format_common(embelishment_val,2);
			document.getElementById('embelishment_cost_per').innerHTML=number_format_common((embelishment_val/order_amt)*100,2) + ' %';
			document.getElementById('commercial_cost').innerHTML=document.getElementById('total_commercial_cost').innerHTML;
			document.getElementById('commercial_cost_per').innerHTML=number_format_common((document.getElementById('total_commercial_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			var comission_val=(parseFloat(document.getElementById('total_foreign_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_local_amount').innerHTML.replace(',','')));
			document.getElementById('commission_cost').innerHTML=number_format_common(comission_val,2);
			document.getElementById('commission_cost_per').innerHTML=number_format_common((comission_val/order_amt)*100,2) + ' %';
			document.getElementById('testing_cost').innerHTML=document.getElementById('total_test_cost_amount').innerHTML;
			document.getElementById('testing_cost_per').innerHTML=number_format_common((document.getElementById('total_test_cost_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('freight_cost').innerHTML=document.getElementById('total_freight_amount').innerHTML;
			document.getElementById('freight_cost_per').innerHTML=number_format_common((document.getElementById('total_freight_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('inspection_cost').innerHTML=document.getElementById('total_inspection_amount').innerHTML;
			document.getElementById('inspection_cost_per').innerHTML=number_format_common((document.getElementById('total_inspection_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('certificate_cost').innerHTML=document.getElementById('total_certificate_amount').innerHTML;
			document.getElementById('certificate_cost_percent').innerHTML=number_format_common((document.getElementById('total_certificate_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			document.getElementById('commn_cost').innerHTML=document.getElementById('total_common_oh_amount').innerHTML;
			document.getElementById('commn_cost_per').innerHTML=number_format_common((document.getElementById('total_common_oh_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			document.getElementById('courier_cost').innerHTML=document.getElementById('total_currier_amount').innerHTML;
			document.getElementById('courier_cost_per').innerHTML=number_format_common((document.getElementById('total_currier_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('cm_cost').innerHTML=document.getElementById('total_cm_amount').innerHTML;
			document.getElementById('cm_cost_per').innerHTML=number_format_common((document.getElementById('total_cm_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			document.getElementById('cost_cost').innerHTML=document.getElementById('total_tot_cost').innerHTML;
			document.getElementById('cost_cost_per').innerHTML=number_format_common((document.getElementById('total_tot_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			document.getElementById('order_id').innerHTML=number_format_common(order_amt,2);
			document.getElementById('order_percent').innerHTML=number_format_common((order_amt/order_amt)*100,2);
			

		 

			
			var tot_margin=$("#tot_margin").val();
			document.getElementById('margin').innerHTML=tot_margin;
			var  margin=document.getElementById('margin').innerHTML.replace(/,/g ,'');
			document.getElementById('margin_percent').innerHTML=number_format_common((margin/order_amt)*100,2)+ ' %';;

			var tot_contri_margin=$("#tot_contri_margin").val();
			document.getElementById('contribution_margin').innerHTML=tot_contri_margin;
			var contri_margin=document.getElementById('contribution_margin').innerHTML.replace(/,/g ,'');
			document.getElementById('contribution_margin_percent').innerHTML=number_format_common((contri_margin/order_amt)*100,2)+ ' %';;
			


		
			//document.getElementById('fab_profit_id').innerHTML=document.getElementById('total_fabric_profit').innerHTML;
			//document.getElementById('profit_fab_percentage').innerHTML=number_format_common((document.getElementById('total_fabric_profit').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			//document.getElementById('expected_id').innerHTML=document.getElementById('total_expected_profit').innerHTML;
			//document.getElementById('profit_expt_fab_percentage').innerHTML=number_format_common((document.getElementById('total_expected_profit').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			//document.getElementById('expt_p_variance_id').innerHTML=document.getElementById('total_expected_variance').innerHTML;
			//document.getElementById('expt_p_percent').innerHTML=number_format_common((document.getElementById('total_expected_variance').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			var matr_ser_cost=(parseFloat(document.getElementById('yarn_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('purchase_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('knit_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('ydyeing_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('aop_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('dyefin_cost').innerHTML.replace(',','')));
			document.getElementById('tot_matr_ser_cost').innerHTML=number_format_common(matr_ser_cost,2);
			document.getElementById('tot_matr_ser_per').innerHTML=number_format_common((matr_ser_cost/order_amt)*100,2) + ' %';
			//var aa=order_amt;
			//alert(aa);

				function toggle()
				{
					var ele = document.getElementById("yarn_summary");
					//alert(ele);
					var text = document.getElementById("displayText");
					if(ele.style.display!= "none")
					{
						ele.style.display = "none";
						text.innerHTML = "Show Yarn Summary";
					}
					else
					{
						ele.style.display = "block";
						text.innerHTML = "Hide Yarn Summary";
					}
				}

			</script>
			<div style="width:4270px;">
			<div style="width:900px;" align="left">
				<table width="900" cellpadding="0" cellspacing="2" border="0">
					<tr>
						<td width="350" align="left">
							<table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
								<thead align="center">
									<tr>
										<th colspan="4">Order Wise Budget Cost Summary Group</th>
									</tr>
									<tr>
										<th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
									</tr>
								</thead>
								<tr bgcolor="<? echo $style1; ?>">
									<td width="20">1</td>
									<td width="130">Yarn Cost</td>
									<td width="120" align="right" id="yarn_cost"></td>
									<td width="80" align="right" id="yarn_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>2</td>
									<td>Fabric Purchase</td>
									<td align="right" id="purchase_cost"></td>
									<td align="right" id="purchase_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>3</td>
									<td>Knitting Cost</td>
									<td align="right" id="knit_cost"></td>
									<td align="right" id="knit_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>4</td>
									<td>Yarn Dyeing Cost</td>
									<td align="right" id="ydyeing_cost"></td>
									<td align="right" id="ydyeing_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>5</td>
									<td>AOP Cost</td>
									<td align="right" id="aop_cost"></td>
									<td align="right" id="aop_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>6</td>
									<td>Dyeing & Finishing Cost</td>
									<td align="right" id="dyefin_cost"></td>
									<td align="right" id="dyefin_cost_per"></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Material & Service Cost</strong></td>
									<td align="right" id="tot_matr_ser_cost"></td>
									<td align="right" id="tot_matr_ser_per"></td>
								</tr>
								<tr bgcolor="<?  echo $style1; ?>">
									<td>7</td>
									<td>Trims Cost</td>
									<td align="right" id="trim_cost"></td>
									<td align="right" id="trim_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>8</td>
									<td>Print/ Emb. /Wash Cost</td>
									<td align="right" id="embelishment_cost"></td>
									<td align="right" id="embelishment_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>9</td>
									<td>Commercial Cost</td>
									<td align="right" id="commercial_cost"></td>
									<td align="right" id="commercial_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>10</td>
									<td>Commision Cost</td>
									<td align="right" id="commission_cost"></td>
									<td align="right" id="commission_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>11</td>
									<td>Testing Cost</td>
									<td align="right" id="testing_cost"></td>
									<td align="right" id="testing_cost_per"></td>
								</tr>
									<tr bgcolor="<? echo $style1; ?>">
									<td>12</td>
									<td>Freight Cost</td>
									<td align="right" id="freight_cost"></td>
									<td align="right" id="freight_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td width="20">13</td>
									<td width="100">Inspection Cost</td>
									<td align="right" id="inspection_cost"></td>
									<td align="right" id="inspection_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>14</td>
									<td>Certificate Cost</td>
									<td align="right" id="certificate_cost"></td>
									<td align="right" id="certificate_cost_percent"></td>
								</tr>
									<tr bgcolor="<? echo $style; ?>">
									<td>15</td>
									<td>Operating Exp.</td>
									<td align="right" id="commn_cost"></td>
									<td align="right" id="commn_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>16</td>
									<td>Courier Cost</td>
									<td align="right" id="courier_cost">></td>
									<td align="right" id="courier_cost_per">></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>17</td>
									<td>CM Cost</td>
									<td align="right" id="cm_cost"></td>
									<td align="right" id="cm_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>18</td>
									<td>Total Cost</td>
									<td align="right" id="cost_cost"></td>
									<td align="right" id="cost_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>19</td>
									<td>Total Order Value</td>
									<td align="right" id="order_id"></td>
									<td align="right" id="order_percent"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>20</td>
									<td>Margin</td>
									<td align="right" id="margin"></td>
									<td align="right" id="margin_percent"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>21</td>
									<td>Contribution Margin</td>
									<td align="right" id="contribution_margin"></td>
									<td align="right" id="contribution_margin_percent"></td>
								</tr>
							</table>
						</td>
						<!--<td colspan="5" style="min-height:900px; max-height:100%" align="center" valign="top">
							<div id="chartdiv" style="width:580px; height:900px;" align="center"></div>
						</td>-->
					</tr>
				</table>
			</div>
			<br/>
			<?
		
			ob_start();
			?>
			<h3 align="left" id="accordion_h2" style="width:4270px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
			<fieldset style="width:100%;" id="content_search_panel2">
			<table width="4170">
					<tr class="form_caption">
						<td colspan="44" align="center"><strong>Cost Break Down Report</strong></td>
					</tr>
					<tr class="form_caption">
						<td colspan="44" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
					</tr>
					<tr colspan="44" align="center" class="form_caption">
						<td><strong>Details Report </strong></td>
					</tr>
			</table>
			   <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit'];

					if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else $caption="PO Insert Date";
			   ?>
			<table id="table_header_1" class="rpt_table" width="4150" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40" rowspan="2">SL</th>
						<th width="70" rowspan="2">Buyer</th>
						<th width="70" rowspan="2">Job No</th>
						<th width="70" rowspan="2">File No</th>
						<th width="70" rowspan="2">Ref No</th>
						<th width="100" rowspan="2">Order No</th>
						<th width="100" rowspan="2">Order Status</th>
						<th width="110" rowspan="2">Style</th>
						<th width="110" rowspan="2">Item Name</th>
						<th width="110" rowspan="2">Dealing</th>
						<th width="70" rowspan="2"><? echo $caption; ?></th>
						<th width="90" rowspan="2">Order Qty</th>
						<!--<th width="90" rowspan="2">Avg Unit Price</th>
						<th width="100" rowspan="2">Order Value</th>-->
						<th colspan="14">Fabric Cost</th>
						<th width="100" rowspan="2">Trim Cost</th>
						<th colspan="5">Embell. Cost</th>
						<th width="120" rowspan="2">Commercial Cost</th>
						<th colspan="2">Commission</th>
						<th width="100" rowspan="2">Testing Cost</th>
						<th width="100" rowspan="2">Freight Cost</th>
						<th width="120" rowspan="2">Inspection Cost</th>
						<th width="100" rowspan="2">Certificate Cost</th>
						<th width="100" rowspan="2">Operating Exp</th>
						<th width="100" rowspan="2">Courier Cost</th>
						<th width="120" rowspan="2">CM Cost/DZN</th>
						<th width="100" rowspan="2">CM Cost</th>
						<th width="100" rowspan="2">Total Cost</th>
						<th width="100" rowspan="2">Margin</th>
					</tr>
					<tr>
						<th width="100">Avg Yarn Rate</th>
						<th width="80">Yarn Cost</th>
						<th width="80">Yarn Cost %</th>
						<th width="100">Fabric Purchase</th>
						<th width="80">Knit/ Weav Cost/Dzn</th>
						<th width="80">Knitting/ Weav Cost</th>
						<th width="100">Yarn Dye Cost/Dzn </th>
						<th width="110">Yarn Dyeing Cost </th>
						<th width="120">Fab.Dye Cost/Dzn</th>
						<th width="100">Fabric Dyeing Cost</th>
						<th width="90">Heat Setting</th>
						<th width="100">Finishing Cost</th>
						<th width="90">Washing Cost</th>
						<th width="90">All Over Print</th>
						<th width="80">Printing</th>
						<th width="85">Embroidery</th>
						<th width="80">Special Works</th>
						<th width="80">Wash Cost</th>
						<th width="80">Other</th>
						<th width="120">Foreign</th>
						<th width="120">Local</th>
					</tr>
				</thead>
			</table>
			<div style="width:4170px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="4150" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
			$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
		$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;

		
			/*$JobArr=array();
			foreach($result_sql_budget2 as $budget_row){
				$JobArr[]="'".$budget_row[csf('job_no')]."'";
			}
			$yarn= new yarn($JobArr,'job');
			*/
			 $condition= new condition();
			 if(str_replace("'","",$cbo_company_name)>0){
			 $condition->company_name("=$cbo_company_name");
			 }

			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 $job_no=str_replace("'","",$txt_job_no);
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num(" in($job_no)");
			 }
			  $all_job_id=implode(",",$jobIdArr);
				// echo  $all_job_id.'d';
			if($all_job_id!='')
			{
			  $condition->jobid_in($all_job_id);
			}
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
				 //and b.po_received_date between '$start_date' and '$end_date'
				// echo 'FFGG';
			 }
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no");
			 }
			
			 $condition->init();
			$yarn= new yarn($condition);
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			
			
			foreach($result_sql_budget2 as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}

				$dzn_qnty=0;
				$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;

				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];

				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];

				//$total_order_amount+=$order_value;
				$total_plancut_amount+=$plancut_value;
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					 <td width="40"><? echo $i; ?></td>
					 <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
					 <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					 <td width="70"><p><? echo $row[csf('file_no')]; ?></p></td>
					 <td width="70"><p><? echo $row[csf('grouping')]; ?></p></td>
					 <td width="100"><p><a href="#" onClick="precost_bom_pop('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')]; ?>','<?  echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>');"><? echo $row[csf('po_number')]; ?></a></p></td>
					 <td width="100"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
					 <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					 <td width="110"><p><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></p></td>
					 <td width="110"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
					 <td width="70"><p><? echo $ship_po_recv_date; ?></p></td>
					 <td width="90" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
					 <!--<td width="90" align="right"><p><? //echo number_format($row[csf('avg_unit_price')],2); ?></p></td>
					 <td width="100" align="right"><p><? //echo number_format($order_value,2); ?></p></td>-->
					 <?
						$commercial_cost=($fabriccostArray[$row[csf('job_no')]]['comm_cost']/$dzn_qnty)*$order_qty_pcs;

						$fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
						$fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));
						 $fab_purchase=0; //$yarn_costing=0;
						foreach($fabricData as $fabricRow)
						{
							$fabricRow=explode("**",$fabricRow);
							$fab_nature_id=$fabricRow[0];
							$fab_source_id=$fabricRow[1];
							$fab_rate=$fabricRow[2];
							$yarn_qty=$fabricRow[3];
							$yarn_amount=$fabricRow[4];
							//$yarn_costing=0;
							if($fab_source_id==2)
							{
								foreach($fab_precost_Data as $fab_row)
								{
									$fab_dataRow=explode("**",$fab_row);
									$fab_requirment=$fab_dataRow[0];
									$fab_pcs=$fab_dataRow[1];
									$fab_purchase_qty=$fab_requirment/$fab_pcs*$plan_cut_qnty;
									//echo $fab_pcs;
									$fab_purchase=$fab_purchase_qty*$fab_rate;
								}
							}
							else if($fab_source_id==1 || $fab_source_id==3)
							{
								//$avg_rate=$yarn_amount/$yarn_qty;
								//$yarn_costing=$yarn_amount/$dzn_qnty*$plan_cut_qnty;
							}
						}
						$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];

						$avg_rate=$yarn_costing/$yarn_req_qty_arr[$row[csf('po_id')]];
						$yarn_cost_percent=($yarn_costing/$order_value)*100;

						$kniting_cost=$knit_arr[$row[csf('job_no')]]['knit']+$knit_arr[$row[csf('job_no')]]['weaving']+$knit_arr[$row[csf('job_no')]]['collar_cuff']+$knit_arr[$row[csf('job_no')]]['feeder_stripe'];
						$knit_cost_dzn=$kniting_cost;
						$knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
						$yarn_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['yarn_dyeing'];
						$yarn_dyeing_cost=($yarn_dyeing_cost_dzn/$dzn_qnty)*$plan_cut_qnty;
						$fabric_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['fabric_dyeing'];
						$fabric_dyeing_cost=($fabric_dyeing_cost_dzn/$dzn_qnty)*$plan_cut_qnty;
						$heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
						$fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
						$washing_cost=($knit_arr[$row[csf('job_no')]]['washing']/$dzn_qnty)*$plan_cut_qnty;
						$all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
						//echo  $trim_qty_arr[$row[csf('job_no')]][$row[csf('po_id')]];
						$trim_amount= $trims_costing_arr[$row[csf('po_id')]];//$fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty*$order_qty_pcs;
						$print_amount=($fab_emb[$row[csf('job_no')]]['print']/$dzn_qnty)*$order_qty_pcs;
						$embroidery_amount=($fab_emb[$row[csf('job_no')]]['embroidery']/$dzn_qnty)*$order_qty_pcs;
						$special_amount=($fab_emb[$row[csf('job_no')]]['special']/$dzn_qnty)*$order_qty_pcs;
						$wash_cost=($fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty)*$order_qty_pcs;
						$other_amount=($fab_emb[$row[csf('job_no')]]['other']/$dzn_qnty)*$order_qty_pcs;
						$foreign=$commission_array[$row[csf('job_no')]]['foreign']/$dzn_qnty*$order_qty_pcs;
						$local=$commission_array[$row[csf('job_no')]]['local']/$dzn_qnty*$order_qty_pcs;
						$test_cost=$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$order_qty_pcs;
						$freight_cost= $fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$order_qty_pcs;
						$inspection=$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$order_qty_pcs;
						$certificate_cost=$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$order_qty_pcs;
						$common_oh=$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$order_qty_pcs;
						$currier_cost=$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
						//echo $currier_cost;
						$cm_cost_dzn=$fabriccostArray[$row[csf('job_no')]]['c_cost'];
						$cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$order_qty_pcs;
						$total_cost=$yarn_costing+$fab_purchase+$knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;

						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100;
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;

						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
						if($kniting_cost<=0) $color_knit="red"; else $color_knit="";
						if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
						if($commercial_cost<=0) $color_com="red"; else $color_com="";
					 ?>
					 <td width="100" align="right"><a href="##" onClick="generate_pre_cost_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','precost_yarn_detail')"><? echo number_format($avg_rate,2); ?></a></td>
					 <td width="80" align="right" bgcolor="<? echo $color_yarn; ?>"><? echo number_format($yarn_costing,2); ?></td>
					 <td width="80" align="right"><? echo number_format($yarn_cost_percent,2); ?></td>
					 <td width="100" align="right"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></td>
					 <td width="80" align="right"><? echo number_format($knit_cost_dzn,4); ?></td>
					 <td width="80" align="right" bgcolor="<? echo $color_knit; ?>"><a href="##" onClick="generate_pre_cost_knit_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cons_process; ?>','precost_knit_detail')"><? echo number_format($knit_cost,2); ?></a></td>
					 <td width="100" align="right"><? echo number_format($yarn_dyeing_cost_dzn ,2); ?></td>
					 <td width="110" align="right"><? echo number_format($yarn_dyeing_cost ,2); ?></td>
					 <td width="120" align="right"><? echo number_format($fabric_dyeing_cost_dzn,2); ?></td>
					 <td width="100" align="right" bgcolor="<? echo $color_fab; ?>"><a href="##" onClick="generate_precost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_dyeing_detail')"><? echo number_format($fabric_dyeing_cost,2); ?></a></td>
					 <td width="90" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
					 <td width="100" align="right" ><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_finishing_detail')"><? echo number_format($fabric_finish,2); ?></a></td>
					 <td width="90" align="right"><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_washing_detail')"><? echo number_format($washing_cost,2); ?></a></td>
					 <td width="90" align="right"><a href="##" onClick="generate_precost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_all_over_detail')"><? echo number_format($all_over_cost,2); ?></a></td>
				<?
					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;

					$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;

					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_cm_amount+=$cm_cost;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];

					if($trim_amount<=0) $color_trim="red"; else $color_trim="";
					if($cm_cost<=0) $color="red"; else $color="";

					$yarnData=explode(",",substr($yarncostArray[$row[csf('job_no')]],0,-1));
					//print_r($yarnData);
					foreach($yarnData as $yarnRow)
					{
						$yarnRow=explode("**",$yarnRow);
						$count_id=$yarnRow[0];
						$type_id=$yarnRow[1];
						$cons_qnty=$yarnRow[2];
						$amount=$yarnRow[3];

						$yarn_desc=$yarn_count_library[$count_id]."**".$yarn_type[$type_id];
						$req_qnty=($plan_cut_qnty/$dzn_qnty_yarn)*$cons_qnty;
						$req_amnt=($plan_cut_qnty/$dzn_qnty_yarn)*$amount;

						$yarn_desc_array[$yarn_desc]['qnty']+=$req_qnty;
						$yarn_desc_array[$yarn_desc]['amnt']+=$req_amnt;
					}
					//var_dump($yarn_desc_array);
					?>
					 <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_precost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','trim_cost_detail')"><? echo number_format($trim_amount,2); ?></a></td>
					 <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','print_cost_detail')"><? echo number_format($print_amount,2); ?></a></td>
					 <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','embroidery_cost_detail')"><? echo number_format($embroidery_amount,2); ?></a></td>
					 <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
					 <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','wash_cost_detail')"><? echo number_format($wash_cost,2); ?></a></td>
					 <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
					 <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
					 <td width="120" align="right"><? echo number_format($local,2) ?></td>
					 <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
					 <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($inspection,2);?></td>
					 <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
					 <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
					 <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>
					 <td width="120" align="right"><? echo number_format($cm_cost_dzn,2);?></td>
					 <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
					 <td width="100" align="right"><? echo number_format($total_cost,2); ?></td>
					 <td width="100" align="right"><?
					 $margin=$order_value-$total_cost;
					  echo number_format($margin,2); ?></td>
					<?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";
						else $color_pl="";
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
						$expect_variance=$total_profit-$expected_profit_per;

					?>
				  </tr>
				<?
				$total_order_qty+=$order_qty_pcs;
				$total_order_amount+=$order_value;
				$total_margin+=$margin;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$all_over_print_cost+=$all_over_cost;
				$total_trim_cost+=$trim_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;

				//echo $total_cost.'<br>';
				//$total_fab_cost_amount2+=$total_fab_cost_amount;
				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;
				//echo $total_fab_cost_amount;
				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			//$total_purchase_cost_percentage=$total_purchase_cost/$total_order_amount*100;
			?>
			</table>
			</div>
			<table class="tbl_bottom" width="4150" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="40"></td>
					<td width="70"></td>
					<td width="70"></td>
					<td width="70"></td>
					<td width="70"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="110"></td>
					<td width="110"></td>
					<td width="110"></td>
					<td width="70"></td>
					<td width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
					<!--<td width="90"></td>-->
					<td style="display:none;" width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></td>
					<td width="100"></td>
					<td width="80" align="right" id="total_yarn_cost2"><? echo number_format($total_yarn_cost,2); ?></td>
					<td width="80" align="right" id="total_yarn_cost_per"><? echo number_format($total_yarn_cost_percentage,2); ?></td>
					<td width="100" align="right" id="total_purchase_cost"><? echo number_format($total_purchase_cost,2); ?></td>
					<td width="80"></td>
					<td width="80" align="right" id="total_knitting_cost"><? echo number_format($total_knitting_cost,2); ?></td>
					<td width="100"></td>
					<td width="110" align="right" id="total_yarn_dyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
					<td width="120"></td>
					<td width="100" align="right" id="total_fabric_dyeing_cost4"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
					<td width="90" align="right" id="total_heat_setting_cost"><? echo number_format($total_heat_setting_cost,2); ?></td>
					<td width="100" align="right" id="total_finishing_cost"><? echo number_format($total_finishing_cost,2); ?></td>
					<td width="90" align="right" id="total_washing_cost"><? echo number_format($total_washing_cost,2); ?></td>
					<td width="90" align="right" id="all_over_print_cost"><? echo number_format($all_over_print_cost,2); ?></td>
					<td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
					<td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
					<td width="80" align="right" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2); ?></td>
					<td width="120" align="right" id="total_foreign_amount"><? echo number_format($total_foreign_amount,2); ?></td>
					<td width="120" align="right" id="total_local_amount"><? echo number_format($total_local_amount,2); ?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2); ?></td>
					<td width="100" align="right" id="total_freight_amount"><? echo number_format($total_freight_amount,2); ?></td>
					<td width="120" align="right" id="total_inspection_amount"><? echo number_format($total_inspection_amount,2); ?></td>
					<td width="100" align="right" id="total_certificate_amount"><? echo number_format($total_certificate_amount,2); ?></td>
					<td width="100" align="right" id="total_common_oh_amount"><? echo number_format($total_common_oh_amount,2); ?></td>
					<td width="100" align="right" id="total_currier_amount"><? echo number_format($total_currier_amount,2); ?></td>
					<td width="120"></td>
					<td width="100" align="right" id="total_cm_amount"><? echo number_format($total_cm_amount,2); ?></td>
					<td width="100" align="right" id="total_tot_cost"><? echo number_format($total_tot_cost,2); ?></td>
					<td width="100" align="right" id="total_margin"><? echo number_format($total_margin,2); ?><input type="hidden" id="tot_margin" value="<?=number_format($total_margin,2); ?>"><input type="hidden" id="tot_contri_margin" value="<?=number_format($total_margin+$total_cm_amount,2); ?>"></td>
				</tr>
			</table>
			<table>
				<tr>
					<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_cost_up,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');

					//$chart_data_qnty="Fabric Cost;".$total_fab_cost."\nTrimCost;".$total_trim_cost."\nEmbelishment Cost;".$total_embelishment_cost."\nCommercial Cost;".$total_commercial_cost."\nCommission Cost;".$total_commssion."\nTesting Cost;".$total_testing_cost."\nFreightCost;".$total_freight_cost."\nCM Cost;".$total_cm_cost."\nInspection Cost;".$total_inspection."\nCertificate Cost;".$total_certificate_cost."\nCommn OH Cost;".$total_common_oh."\nCurrier Cost;".$total_currier_cost."\n Profit/Loss;".$total_fabric_profit_up."\n";
					?>
					<input type="hidden" id="graph_data" value="<? //echo substr($chart_data_qnty,0,-1); ?>"/>
				</tr>
			</table>
			<br>
			<a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>

			<div style="width:600px; display:none" id="yarn_summary" >
				<div id="data_panel2" align="center" style="width:500px">
					 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
				 </div>
				<table width="500">
					<tr class="form_caption">
						<td colspan="6" align="center"><strong>Yarn Cost Summary </strong></td>
					</tr>
				</table>
				<table class="rpt_table" width="500" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="80">Yarn Count</th>
						<th width="120">Type</th>
						<th width="120">Req. Qnty</th>
						<th width="80">Avg. rate</th>
						<th>Amount</th>
					</thead>
					<?
					$s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
					foreach($yarn_desc_array as $key=>$value)
					{
						if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$yarn_desc=explode("**",$key);

						$tot_yarn_req_qnty+=$yarn_desc_array[$key]['qnty'];
						$tot_yarn_req_amnt+=$yarn_desc_array[$key]['amnt'];
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
							<td><? echo $s; ?></td>
							<td align="center"><? echo $yarn_desc[0]; ?></td>
							<td><? echo $yarn_desc[1]; ?></td>
							<td align="right"><? echo number_format($yarn_desc_array[$key]['qnty'],2); ?></td>
							<td align="right"><? echo number_format($yarn_desc_array[$key]['amnt']/$yarn_desc_array[$key]['qnty'],2); ?></td>
							<td align="right"><? echo number_format($yarn_desc_array[$key]['amnt'],2); ?></td>
						</tr>
						<?
						$s++;
					}
					?>
					<tfoot>
						<th colspan="3" align="right">Total</th>
						<th align="right"><? echo number_format($tot_yarn_req_qnty,2); ?></th>
						<th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
						<th align="right"><? echo number_format($tot_yarn_req_amnt,2); ?></th>
					</tfoot>
				</table>
			</div>
			</fieldset>
			</div>
			<?
			$con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=23");
			oci_commit($con);
			disconnect($con);

	}
}
else //Job/Style Wise
{
	//echo $report_type.'X';
	if($report_type==1)
	{
		if($template==1)
		{

			$app_status_arr=array(
				222=>'Not Submit',
				202=>'Not Submit',
				212=>'Ready For Approval',
				211=>'Partial Approved',
				111=>'Full Approved'
			);
			$style1="#E9F3FF";
			$style="#FFFFFF";
			//$nameArray=sql_select( "select cm_cost_method,id from  variable_order_tracking where company_name='$company_id' and variable_list=22 order by id" );
			$cm_cost_formula_sql=sql_select( "select id, cm_cost_method from variable_order_tracking where  variable_list=22 $company_con_name order by id");
			$cm_cost_formula=$cm_cost_predefined_method[$cm_cost_formula_sql[0][csf('cm_cost_method')]];

			$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
			//echo $print_report_format.'ZZZZZZXCCCCCCCCCCCC';
			$print_button=array_shift(array_values(explode(",",$print_report_format)));

			$asking_profit_arr=array(); //$yarn_desc_array=array();
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $company_con_id");//$date_max_profit
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
				}
			}
			

			$asking_profit=array();
		 
		
			
			if($db_type==0)
			{
				
				$po_grp="group_concat(b.id) as po_id";
			}
			else
			{
				$po_grp="listagg(CAST(b.id as VARCHAR(4000)),',') within group (order by b.id) as po_id";
			}

		 $sql_budget2="select a.id,a.company_name,a.job_no_prefix_num,a.team_leader,a.set_smv, max(b.insert_date) as insert_date, c.insert_date as pre_insert_date, c.costing_date, max(b.update_date) as update_date, a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, sum(b.plan_cut) as plan_cut,sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved,c.entry_from,a.quotation_id,$po_grp from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id and c.entry_from=158 $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $approvalCon $file_con_b $ref_con_b group by  a.id,a.company_name,a.job_no_prefix_num, c.insert_date , c.costing_date,a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.team_leader,a.dealing_marchant,a.set_smv, a.gmts_item_id, a.total_set_qnty,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved,c.entry_from,a.quotation_id";
			//echo $sql_budget;// die;
		

			$result_sql_budget2=sql_select($sql_budget2);
			foreach($result_sql_budget2 as $row )
			{
				//$job_id.=$row[csf('id')].',';
				$job_idArr[$row[csf('id')]]=$row[csf('id')];
			}
			$job_id_cond=where_con_using_array($job_idArr,0,'a.id');
			
			
			  $sql_budget="select a.id, a.company_name, a.job_no_prefix_num, a.team_leader, a.set_smv, max(b.insert_date) as insert_date, c.insert_date as pre_insert_date, c.costing_date, max(b.update_date) as update_date, a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, sum(b.plan_cut) as plan_cut,sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price, c.job_no, c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved,c.entry_from,a.quotation_id,$po_grp from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id and c.entry_from=158 $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $approvalCon $file_con_b $ref_con_b $job_id_cond group by  a.id,a.company_name,a.job_no_prefix_num, c.insert_date , c.costing_date,a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.team_leader,a.dealing_marchant,a.set_smv, a.gmts_item_id, a.total_set_qnty,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved,c.entry_from,a.quotation_id";
		//	echo $sql_budget2; //die;
		

			$result_sql_budget=sql_select($sql_budget);
			
			$tot_rows_budget=count($result_sql_budget);
			$budget_data_arr=array();
			foreach($result_sql_budget as $row )
			{
				$job_id.=$row[csf('id')].',';
			}
			$jobIds=chop($job_id,',');  
			$job_ids=count(array_unique(explode(",",$job_id)));
			if($db_type==2 && $job_ids>1000)
			{
				$job_cond_for_in=" and (";
				$jobIdsArr=array_chunk(explode(",",$jobIds),999);
				foreach($jobIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$job_cond_for_in.=" b.id in($ids) or"; 
				}
				$job_cond_for_in=chop($job_cond_for_in,'or ');
				$job_cond_for_in.=")";
			}
			else
			{
				$job_cond_for_in=" and b.id in($jobIds)";
			}
			$cpm_arr=array();$cpm_date_arr=array(); //$yarn_desc_array=array();
			$finance_arr=array();
			$sql_cpm=sql_select("select applying_period_date, applying_period_to_date, cost_per_minute,interest_expense from lib_standard_cm_entry where 1=1 $company_con_id");//$date_max_profit
			//echo "select applying_period_date, applying_period_to_date, cost_per_minute,interest_expense from lib_standard_cm_entry where 1=1 $company_con_id";
			foreach($sql_cpm as $cpMrow )
			{
				$applying_period_date=change_date_format($cpMrow[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($cpMrow[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$cpm_arr[$newdate]['cpm']=$cpMrow[csf('cost_per_minute')];
					$finance_arr[$newdate]['finPercent']=$cpMrow[csf('interest_expense')];
				}
			}
			
			 $smv_eff_arr=array(); $costing_library=array();
			$sql_smv_cpm=sql_select("select a.job_no, a.costing_date, a.sew_smv, a.sew_effi_percent, a.exchange_rate, b.company_name from wo_pre_cost_mst a,wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond_for_in");
			//echo "select a.job_no, a.costing_date, a.sew_smv, a.sew_effi_percent,b.company_name from wo_pre_cost_mst a,wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond_for_in";
			foreach($sql_smv_cpm as $smv_cpm )
			{
				$costing_library[$smv_cpm[csf('job_no')]]=$smv_cpm[csf('costing_date')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['smv']=$smv_cpm[csf('sew_smv')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['exc_rate']=$smv_cpm[csf('exchange_rate')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['eff']=$smv_cpm[csf('sew_effi_percent')];
				//echo change_date_format($smv_cpm[csf('costing_date')],'','',1);
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['cpm']=$cpm_arr[change_date_format($smv_cpm[csf('costing_date')],'','',1)]['cpm'];
			}
			//print_r($smv_eff_arr);
			unset($sql_smv_cpm);
			?>
			
				<script>
					var order_amt=document.getElementById('total_order_amount').innerHTML.replace(/,/g ,'');
					//var aaa=document.getElementById('total_yarn_cost').innerHTML.replace(/,/g ,'');
					//alert(aaa);
					if(document.querySelector("#total_yarn_cost"))
					{

						document.getElementById('yarn_cost').innerHTML=document.getElementById('total_yarn_cost').innerHTML;
						document.getElementById('yarn_cost_per').innerHTML=number_format_common((document.getElementById('total_yarn_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					if(document.querySelector("#total_purchase_cost"))
					{
						document.getElementById('purchase_cost').innerHTML=document.getElementById('total_purchase_cost').innerHTML;
						document.getElementById('purchase_cost_per').innerHTML=number_format_common((document.getElementById('total_purchase_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					
					if(document.querySelector("#total_purchase_cost"))
					{
						document.getElementById('knit_cost').innerHTML=document.getElementById('total_knitting_cost').innerHTML;
						document.getElementById('knit_cost_per').innerHTML=number_format_common((document.getElementById('total_knitting_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					if(document.querySelector("#total_yarn_dyeing_cost"))
					{
						document.getElementById('ydyeing_cost').innerHTML=document.getElementById('total_yarn_dyeing_cost').innerHTML;
						document.getElementById('ydyeing_cost_per').innerHTML=number_format_common((document.getElementById('total_yarn_dyeing_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					
					if(document.querySelector("#all_over_print_cost"))
					{
						document.getElementById('aop_cost').innerHTML=document.getElementById('all_over_print_cost').innerHTML;
						document.getElementById('aop_cost_per').innerHTML=number_format_common((document.getElementById('all_over_print_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					var dyefin_val=0;
					if(document.querySelector("#total_fabric_dyeing_cost") && document.querySelector("#total_finishing_cost")&& document.querySelector("#total_heat_setting_cost")  && document.querySelector("#total_washing_cost"))
					{
						dyefin_val=(parseFloat(document.getElementById('total_fabric_dyeing_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_finishing_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_heat_setting_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_washing_cost').innerHTML.replace(/,/g ,'')));
					}
					
					
					document.getElementById('dyefin_cost').innerHTML=number_format_common(dyefin_val,2);
					document.getElementById('dyefin_cost_per').innerHTML=number_format_common((dyefin_val/order_amt)*100,2) + ' %';

					if(document.querySelector("#total_trim_cost"))
					{
						document.getElementById('trim_cost').innerHTML=document.getElementById('total_trim_cost').innerHTML;
						document.getElementById('trim_cost_per').innerHTML=number_format_common((document.getElementById('total_trim_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					
					var embelishment_val=0;
					if(document.querySelector("#total_print_amount") && document.querySelector("#total_embroidery_amount")&& document.querySelector("#total_special_amount")  && document.querySelector("#total_wash_cost") && document.querySelector("#total_other_amount"))
					{
						embelishment_val=(parseFloat(document.getElementById('total_print_amount').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_embroidery_amount').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_special_amount').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_wash_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_other_amount').innerHTML.replace(/,/g ,'')));
					}

				

					document.getElementById('embelishment_cost').innerHTML=number_format_common(embelishment_val,2);
					document.getElementById('embelishment_cost_per').innerHTML=number_format_common((embelishment_val/order_amt)*100,2) + ' %';

					if(document.querySelector("#total_commercial_cost"))
					{
						document.getElementById('commercial_cost').innerHTML=document.getElementById('total_commercial_cost').innerHTML;
					 document.getElementById('commercial_cost_per').innerHTML=number_format_common((document.getElementById('total_commercial_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					var comission_val=0;
					if(document.querySelector("#total_foreign_amount") && document.querySelector("#total_local_amount"))
					{
						 comission_val=(parseFloat(document.getElementById('total_foreign_amount').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('total_local_amount').innerHTML.replace(/,/g ,'')));
					}
					

					document.getElementById('commission_cost').innerHTML=number_format_common(comission_val,2);
					document.getElementById('commission_cost_per').innerHTML=number_format_common((comission_val/order_amt)*100,2) + ' %';

					if(document.querySelector("#total_test_cost_amount"))
					{
						document.getElementById('testing_cost').innerHTML=document.getElementById('total_test_cost_amount').innerHTML;
						document.getElementById('testing_cost_per').innerHTML=number_format_common((document.getElementById('total_test_cost_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}

					if(document.querySelector("#total_freight_amount"))
					{
						document.getElementById('freight_cost').innerHTML=document.getElementById('total_freight_amount').innerHTML;
						document.getElementById('freight_cost_per').innerHTML=number_format_common((document.getElementById('total_freight_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					
					if(document.querySelector("#total_inspection_amount"))
					{
						document.getElementById('inspection_cost').innerHTML=document.getElementById('total_inspection_amount').innerHTML;
						document.getElementById('inspection_cost_per').innerHTML=number_format_common((document.getElementById('total_inspection_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}

					if(document.querySelector("#total_certificate_amount"))
					{
						document.getElementById('certificate_cost').innerHTML=document.getElementById('total_certificate_amount').innerHTML;
					   document.getElementById('certificate_cost_percent').innerHTML=number_format_common((document.getElementById('total_certificate_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}

					if(document.querySelector("#total_common_oh_amount"))
					{
							document.getElementById('commn_cost').innerHTML=document.getElementById('total_common_oh_amount').innerHTML;
							document.getElementById('commn_cost_per').innerHTML=number_format_common((document.getElementById('total_common_oh_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					
					if(document.querySelector("#total_currier_amount"))
					{
						document.getElementById('courier_cost').innerHTML=document.getElementById('total_currier_amount').innerHTML;
						document.getElementById('courier_cost_per').innerHTML=number_format_common((document.getElementById('total_currier_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					
					if(document.querySelector("#total_cm_amount"))
					{
						document.getElementById('cm_cost').innerHTML=document.getElementById('total_cm_amount').innerHTML;
					    document.getElementById('cm_cost_per').innerHTML=number_format_common((document.getElementById('total_cm_amount').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}
					
					if(document.querySelector("#total_tot_cost"))
					{
						document.getElementById('cost_cost').innerHTML=document.getElementById('total_tot_cost').innerHTML;
					    document.getElementById('cost_cost_per').innerHTML=number_format_common((document.getElementById('total_tot_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					}



					
					
					
					
					document.getElementById('order_id').innerHTML=number_format_common(order_amt,2);
					document.getElementById('order_percent').innerHTML=number_format_common((order_amt/order_amt)*100,2);

					var matr_ser_cost=0;
					if(document.querySelector("#yarn_cost") && document.querySelector("#purchase_cost")&& document.querySelector("#knit_cost")  && document.querySelector("#ydyeing_cost") && document.querySelector("#aop_cost") && document.querySelector("#dyefin_cost"))
					{
						 matr_ser_cost=(parseFloat(document.getElementById('yarn_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('purchase_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('knit_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('ydyeing_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('aop_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('dyefin_cost').innerHTML.replace(/,/g ,'')));
					}
					//document.getElementById('tot_matr_ser_cost').innerHTML=number_format_common(matr_ser_cost,2);
					//document.getElementById('tot_matr_ser_per').innerHTML=number_format_common((matr_ser_cost/order_amt)*100,2) + ' %';
					//var aa=order_amt;
					//alert(aa);
					
					var  total_new_cost=(parseFloat(document.getElementById('yarn_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('purchase_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('knit_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('ydyeing_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('aop_cost').innerHTML.replace(/,/g ,''))) + (parseFloat(document.getElementById('dyefin_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('trim_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('embelishment_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('commercial_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('testing_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('freight_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('inspection_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('certificate_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('commn_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('courier_cost').innerHTML.replace(/,/g ,'')))+ (parseFloat(document.getElementById('cm_cost').innerHTML.replace(/,/g ,'')));
					
					document.getElementById('cost_cost').innerHTML=number_format_common(total_new_cost,2);
					document.getElementById('cost_cost_per').innerHTML=number_format_common((document.getElementById('cost_cost').innerHTML.replace(/,/g ,'')/order_amt)*100,2) + ' %';
					
					
					
						function toggle() 
						{
							var ele = document.getElementById("yarn_summary");
							//alert(ele);
							var text = document.getElementById("displayText");
							if(ele.style.display!= "none") 
							{
								ele.style.display = "none";
								text.innerHTML = "Show Yarn Summary";
							}
							else 
							{
								ele.style.display = "block";
								text.innerHTML = "Hide Yarn Summary";
							}
						} 
						
			</script>
			<div style="width:1800px;">
			<div style="width:900px;" align="left">
				<table width="900" cellpadding="0" cellspacing="2" border="0">
					<tr>
						<td width="350" align="left">
							<table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
								<thead align="center">
									<tr>
										<th colspan="4">Order Wise Budget Cost Summary</th>
									</tr>
									<tr>
										<th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
									</tr>
								</thead>
								<tr bgcolor="<? echo $style1; ?>">
									<td width="20">1</td>
									<td width="130">Yarn Cost</td>
									<td width="120" align="right" id="yarn_cost"></td>
									<td width="80" align="right" id="yarn_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>2</td>
									<td>Fabric Purchase</td>
									<td align="right" id="purchase_cost"></td>
									<td align="right" id="purchase_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>3</td>
									<td>Knitting Cost</td>
									<td align="right" id="knit_cost"></td>
									<td align="right" id="knit_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>4</td>
									<td>Yarn Dyeing Cost</td>
									<td align="right" id="ydyeing_cost"></td>
									<td align="right" id="ydyeing_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>5</td>
									<td>AOP Cost</td>
									<td align="right" id="aop_cost"></td>
									<td align="right" id="aop_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>6</td>
									<td>Dyeing & Finishing Cost</td>
									<td align="right" id="dyefin_cost"></td>
									<td align="right" id="dyefin_cost_per"></td>
								</tr>
								<!--<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Material & Service Cost</strong></td>
									<td align="right" id="tot_matr_ser_cost"></td>
									<td align="right" id="tot_matr_ser_per"></td>
								</tr>-->
								<tr bgcolor="<?  echo $style1; ?>">
									<td>7</td>
									<td>Trims Cost</td>
									<td align="right" id="trim_cost"></td>
									<td align="right" id="trim_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>8</td>
									<td>Print/ Emb. /Wash Cost</td>
									<td align="right" id="embelishment_cost"></td>
									<td align="right" id="embelishment_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>9</td>
									<td>Commercial Cost</td>
									<td align="right" id="commercial_cost"></td>
									<td align="right" id="commercial_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>10</td>
									<td>Commision Cost</td>
									<td align="right" id="commission_cost"></td>
									<td align="right" id="commission_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>11</td>
									<td>Testing Cost</td>
									<td align="right" id="testing_cost"></td>
									<td align="right" id="testing_cost_per"></td>
								</tr>
									<tr bgcolor="<? echo $style1; ?>">
									<td>12</td>
									<td>Freight Cost</td>
									<td align="right" id="freight_cost"></td>
									<td align="right" id="freight_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td width="20">13</td>
									<td width="100">Inspection Cost</td>
									<td align="right" id="inspection_cost"></td>
									<td align="right" id="inspection_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>14</td>
									<td>Certificate Cost</td>
									<td align="right" id="certificate_cost"></td>
									<td align="right" id="certificate_cost_percent"></td>
								</tr>
									<tr bgcolor="<? echo $style; ?>">
									<td>15</td>
									<td>Operating Exp.</td>
									<td align="right" id="commn_cost"></td>
									<td align="right" id="commn_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>16</td>
									<td>Courier Cost</td>
									<td align="right" id="courier_cost"></td>
									<td align="right" id="courier_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>17</td>
									<td>CM Cost</td>
									<td align="right" id="cm_cost"></td>
									<td align="right" id="cm_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>18</td>
									<td>Total Cost</td>
									<td align="right" id="cost_cost"></td>
									<td align="right" id="cost_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>19</td>
									<td>Total Order Value</td>
									<td align="right" id="order_id"></td>
									<td align="right" id="order_percent"></td>
								</tr>
							</table>
						</td>
						<!--<td colspan="5" style="min-height:900px; max-height:100%" align="center" valign="top">
							<div id="chartdiv" style="width:580px; height:900px;" align="center"></div>
						</td>-->
					</tr>
				</table>
			</div>
			<br/> 
			<?
			ob_start();
			if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else if(str_replace("'","",$cbo_search_date)==3) $caption="PO Insert Date";
					else $caption="Cancelled Date";
			?>
			<fieldset style="width:100%;" id="content_search_panel2">
            		<div>
                       <span style="font-size:20px; font-weight:bold;"><? echo $company_library[$company_name]; ?></span><br/>
                       <span style="font-size:16px; font-weight:bold;">Cost Breakdown Analysis<br/></span>
                       <strong>From  <? echo $caption.' &nbsp;'.$date_from;  ?>  To  <? echo $date_to; ?></strong>
                    </div>
			   <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit'];

			   ?>
			<table id="table_header_1" class="rpt_table" width="5190" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
                        <th width="110">Team Leader</th>
						<th width="100">Company</th>
                        <th width="70">Buyer</th>
                        <th width="110">Style</th>
						<th width="70">Job No</th>
                        <th width="110">Gmts Item Name</th>
                        <th width="110">SMV</th>
                        <th width="110">Efficiency</th>
                        <th width="110">Costing Date</th>
                        <th width="90">Style Qty</th>
                        <th width="60">UOM</th>
                        <th width="90">Total SMV</th>
                        <th width="90">Style Qty (Pcs)</th>
                        <th width="110">Excess Cut %</th>
                        <th width="110">Plan Cut Qty (Pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">Style Value</th>
                        <th width="60">TTL Y/Qty</th>
                        <th width="60">TTL Knit Qty</th>
                        <th width="60">TTL Dye & Fin Qty</th>
                        <th width="60">TTL Wash Qty</th>
                        <th width="100">Fabric Cost</th>
                        <th width="100">Trim Cost</th>
						<th width="80">Printing</th>
						<th width="85">Embroidery</th>
						<th width="80">Special Works</th>
						<th width="80">Wash Cost</th>
						<th width="80">Other</th>
                        <th width="120">Commercial Cost</th>
						<th width="100">Testing Cost</th>
						<th width="100">Freight Cost</th>
						<th width="120">Inspection Cost</th>
						<th width="100">Certificate Cost</th>
                        <th width="100">Finance Cost</th>
						<th width="100">Courier Cost</th>
                        <th width="60">Deffd. LC Cost</th>
                        <th width="60">Interest</th>
                        <th width="60">Income Tax</th>
                        <th width="110">Total Material Cost</th>
                        <th width="120">Foreign Comm</th>
						<th width="120">Local Comm</th>
                        <th width="110">Total Comm</th>
                        <th width="110">Net FOB</th>
                        <th width="100">CM Value (Contribution)</th>
						<th width="100" title="<?php echo $cm_cost_formula; ?>">CM Cost</th>
                        <th width="110">Total Margin</th>
                        <th width="110">Margin/Pcs</th>
                        <th width="60">Margin %</th>
                        <th width="110">CPM</th>
                        <th width="110">EPM</th>
                        <th width="110">Approved By</th>
                        <th width="110">Appv. Date & Time</th>
                        <th>Appv. Status</th>
					</tr>

				</thead>
			</table>
			<div style="width:5210px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="5190" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
			$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
			$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;



			 $condition= new condition();
			 if(str_replace("'","",$cbo_company_name)>0){
				 $condition->company_name("=$cbo_company_name");
			 }

			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			$job_no=str_replace("'","",$txt_job_no);
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num(" in($job_no)");
			 }
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			// echo $cbo_search_type.',';die;
			 if($cbo_search_type==1) //Job/Style Wise
			 {
				 $all_job_id=implode(",",$job_idArr);
				// echo  $all_job_id.'d';
				if($all_job_id!='')
				{
				  $condition->jobid_in($all_job_id);
				}
			 }
			 else
			 {
				 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
					  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
				 }
				 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				 {
					 $condition->po_received_date(" between '$start_date' and '$end_date'");
					 //and b.po_received_date between '$start_date' and '$end_date'
					// echo 'FFGG';
				 }
				 if(str_replace("'","",$cbo_search_date) ==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				 {
						 $condition->insert_date("$insert_date_cond");
				 }
			 }


			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no");
			 }

			 /*if(str_replace("'","",$txt_season)!='')
			 {
				$condition->season("in('".implode("','",explode(",",$season))."')");
				//$condition->season("in($txt_season)");
			 }*/
			 $condition->init();

		    // $costing_per_arr=$condition->getCostingPerArr();


			$fabric= new fabric($condition);
			// echo $fabric->getQuery(); //die;
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();

			//print_r($fabric_qty_arr);
			//$fabric->unsetDataArray();

			$yarn= new yarn($condition);

			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			//print_r($yarn_costing_arr);

			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();
			$yarn= new yarn($condition);
			//$yarn_required=$yarn->getOrderWiseYarnQtyArray();


			//$yarn->unsetDataArray();
			//echo $yarn->getQuery(); die;
			$conversion= new conversion($condition);
			//echo $conversion->getQuery();
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			$conversion_costing_po_arr=$conversion->getAmountArray_by_order();

			$conversion_qty= new conversion($condition);
			$conversion_qty_arr_process = $conversion_qty->getQtyArray_by_orderAndProcess();
			$conversion_job_amount_arr_process = $conversion_qty->getAmountArray_by_jobAndProcess();
			$conversion_job_qty_arr_process = $conversion_qty->getQtyArray_by_jobAndProcess();

			//$conversion->unsetDataArray();

			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			//$trims->unsetDataArray();

			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();


			 //print_r($other_costing_arr);die;

			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149,158);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
			$washing_qty_arr=array(140,142,148,64);	
			//*************************************************************************************
			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				else if(str_replace("'","",$cbo_search_date)==4)
				{
					$update_date=explode(" ",$row[csf('update_date')]);
					$ship_po_recv_date=change_date_format($update_date[0]);
				}
				$dzn_qnty=$costing_per_arr[$row[csf('job_no')]];
				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$order_qty=$row[csf('po_quantity')];
				$order_uom=$row[csf('order_uom')];
				$costing_date=$row[csf('costing_date')];				

				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];

				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];
				$total_plancut_amount+=$plancut_value;				
				$po_ids=array_unique(explode(",",$row[csf('po_id')]));
						$commercial_cost=$yarn_costing=$yarn_req_qty=$fab_purchase_knit=$fab_purchase_woven=$conv_cost_poWise=$yarn_dyeing_cost=$heat_setting_cost=$trim_amount=$print_amount=$embroidery_amount=$special_amount=$wash_cost=$other_amount=$foreign=$local=$test_cost=$freight_cost=$inspection=$certificate_cost=$common_oh=$currier_cost=$cm_cost=$dfc_cost=$interest_cost=$incometax_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$total_tot_cost_new=0;
						foreach($po_ids as $pid)
						{
							$commercial_cost+=$commercial_costing_arr[$pid];
							$yarn_costing+=$yarn_costing_arr[$pid];
							$yarn_req_qty+=$yarn_req_qty_arr[$pid];
							$fab_purchase_knit+=array_sum($fabric_costing_arr['knit']['grey'][$pid]);
							$fab_purchase_woven+=array_sum($fabric_costing_arr['woven']['grey'][$pid]);
							$conv_cost_poWise+=array_sum($conversion_costing_po_arr[$pid]);
							$yarn_dyeing_cost+=array_sum($conversion_costing_arr_process[$pid][30]);
							$heat_setting_cost+=array_sum($conversion_costing_arr_process[$pid][33]);
							
							$trim_amount+= $trims_costing_arr[$pid];
							$print_amount+=$emblishment_costing_arr_name[$pid][1];
							$embroidery_amount+=$emblishment_costing_arr_name[$pid][2];
							$special_amount+=$emblishment_costing_arr_name[$pid][4];
							$wash_cost+=$emblishment_costing_arr_name_wash[$pid][3];
							$other_amount+=$emblishment_costing_arr_name[$pid][5];
							$foreign+=$commission_costing_arr[$pid][1];
							$local+=$commission_costing_arr[$pid][2];
							$test_cost+=$other_costing_arr[$pid]['lab_test'];
							$freight_cost+=$other_costing_arr[$pid]['freight'];
							$inspection+=$other_costing_arr[$pid]['inspection'];
							$certificate_cost+=$other_costing_arr[$pid]['certificate_pre_cost'];
							$common_oh+=$other_costing_arr[$pid]['common_oh'];
							$currier_cost+=$other_costing_arr[$pid]['currier_pre_cost'];
							$cm_cost+=$other_costing_arr[$pid]['cm_cost'];
							$dfc_cost+=$other_costing_arr[$pid]['deffdlc_cost'];//$deffdlc_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;
							$interest_cost+=$interest_cost_arr[$pid]*$order_qty_pcs;
							$incometax_cost+=$incometax_cost_arr[$pid]*$order_qty_pcs;
						}
						if($print_button==50){$action='preCostRpt'; } //report_btn_1;
						else if($print_button==51){$action='preCostRpt2';} //report_btn_2;
						else if($print_button==52){$action='bomRpt';} //report_btn_3;
						else if($print_button==63){$action='bomRpt2';} //report_btn_4;
						else if($print_button==156){$action='accessories_details';} //report_btn_5;
						else if($print_button==157){$action='accessories_details2';} //report_btn_6;
						else if($print_button==158){$action='preCostRptWoven';} //report_btn_7;
						else if($print_button==159){$action='bomRptWoven';} //report_btn_8;
						else if($print_button==170){$action='preCostRpt3';} //report_btn_9;
						else if($print_button==171){$action='preCostRpt4';} //report_btn_10;
						else if($print_button==173){$action='preCostRpt5';} //report_btn_10;
						else if($print_button==211){$action='mo_sheet';}
						else if($print_button==142){$action='preCostRptBpkW';}
						else if($print_button==197){$action='bomRpt3';}
						else if($print_button==192){$action='checkListRpt';}
						else if($print_button==221){$action='fabric_cost_detail';}
						else if($print_button==238){$action='summary';}
						else if($print_button==215){$action='budget3_details';}
						//else $print_button=$row[csf('po_number')];
						$costing_date=$costing_library[$row[csf('job_no')]];
						$report_button="precost_bom_pop('".$action."','".$row[csf('job_no')]."',".$row[csf('company_name')].",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$costing_date."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."');";
						
						
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					 <td width="40"><? echo $i; ?></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $team_member_arr[$row[csf('team_leader')]]; ?></div></td>

                     <td width="100"><p><? echo $company_library[$row[csf('company_name')]]; ?></p></td>
                     <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('style_ref_no')]; ?></div></td>
					 <td width="70"><p><a href='##'  onclick="<? echo $report_button; ?>"><? echo $row[csf('job_no_prefix_num')]; ?></a></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110" align="right"><p><? echo $row[csf('set_smv')]; ?></p></td>
                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $smv_eff_arr[$row[csf('job_no')]]['eff']; ?></div></td>
					
                    <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo change_date_format($costing_date); ?></div></td>
                    <td width="90" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                    <td width="60" align="center"><p><? echo $unit_of_measurement[$order_uom]; ?></p></td>
                    <td width="90" align="right"><p><? echo number_format($order_qty*$row[csf('set_smv')],2); ?></p></td>
                    <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                    <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('excess_cut')]; ?></div></td>
                    <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('plan_cut')]; ?></div></td>
                    <td width="70" align="right"><p><? echo number_format($order_value/$order_qty,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>
                    <?						
						$avg_rate=$yarn_costing/$yarn_req_qty;
						$yarn_cost_percent=($yarn_costing/$order_value)*100;
						$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
						$tot_fabric_cost=$yarn_costing+$fab_purchase+$conv_cost_poWise;
						$knit_cost=0;$knite_qty=0;
						foreach($knit_cost_arr as $process_id)
						{ //conversion_job_amount_arr_process
							$knit_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$process_id]);
							$knite_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$process_id]);
						}
						$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_cost=0;$fabric_dyeing_qty=0;
						foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
							$fabric_dyeing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fab_process_id]);
							$fabric_dyeing_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$fab_process_id]);
						}

						
						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
						

						$fabric_finish=0;
						foreach($fab_finish_cost_arr as $fin_process_id)
						{
							$fabric_finish+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fin_process_id]);
						}

						$all_over_cost=0;
						foreach($aop_cost_arr as $aop_process_id)
						{
							$all_over_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$aop_process_id]);
						}

						$washing_cost=0;
						foreach($washing_cost_arr as $w_process_id)
						{
							$washing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$w_process_id]);
						}

						$washing_qty=0;
						foreach($washing_qty_arr as $w_process_id)
						{
							$washing_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$w_process_id]);
						}



						
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;

						$finance_chrg=$order_value*$smv_eff_arr[$row[csf('job_no')]]['finPercent']/100;

						//------
						

						$total_cost_new=($tot_fabric_cost)+$trim_amount+$print_amount+$embroidery_amount+$special_amount+$wash_cost+$other_amount+$commercial_cost+$test_cost+$freight_cost+$inspection+$certificate_cost+$finance_chrg+$currier_cost+$dfc_cost+$interest_cost+$incometax_cost;


						$total_material_cost=$total_cost_new;
						$others_cost_value = $total_cost -($cm_cost+$freight_cost+$commercial_cost+($foreign+$local));
						$net_order_val=$order_value-(($foreign+$local)+$commercial_cost+$freight_cost);
						$cm_value=$net_order_val-$others_cost_value;

						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100;
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;

						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
						if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
						if($commercial_cost<=0) $color_com="red"; else $color_com="";

					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;

					$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_forg_local_amount+=$tot_forg_local;
					$total_FOB_amount+=$tot_netFOB;

					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;
					$total_finance_chrg_amount+=$finance_chrg;

					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_meterial_amount+=$total_material_cost;
					$total_cm_new_amount+=$tot_cmValue;
					$total_cm_amount+=$cm_cost;
					$total_margin_amount+=$tot_margin;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];

					if($trim_amount<=0) $color_trim="red"; else $color_trim="";
					if($cm_cost<=0) $color="red"; else $color="";
					$smv=0; $eff=0; $cpm=0; $cost_dzn_cm_title=0;
					$smv=$smv_eff_arr[$row[csf('job_no')]]['smv'];
					$eff=$smv_eff_arr[$row[csf('job_no')]]['eff'];
					$cpm=$smv_eff_arr[$row[csf('job_no')]]['cpm'];
					$cost_dzn_cm_title='Swe. SMV: '.$smv.'; Swe. EFF: '.$eff.'; CPM: '.$cpm;
						//echo $fabric_cost_pcs_arr[$row[csf('po_id')]].'='.$plan_cut_qnty;
					?>


                    <td width="60" align="right"><p><? echo number_format($yarn_req_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($knite_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fabric_dyeing_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($washing_qty,2); ?></p></td>


                    <td width="100" align="right"><p><? /*$fab_cost=$fabric_cost_pcs_arr[$row[csf('po_id')]]*$plan_cut_qnty;*/
					$fab_cost=$tot_fabric_cost;
					echo number_format($fab_cost,2);$tot_fab_cost+=$fab_cost; ?></p></td>


					 <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><? echo number_format($trim_amount,2);?></td>
					 <td width="80" align="right"><? echo number_format($print_amount,2); ?></td>
					 <td width="85" align="right"><? echo number_format($embroidery_amount,2); ?></td>
					 <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
					 <td width="80" align="right"><? echo number_format($wash_cost,2); ?></td>
					 <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
					 <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
					 <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
					 <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($inspection,2);?></td>
					 <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($finance_chrg,2);  ?></td>
					 <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>

					 <td width="60" align="right"><? echo number_format($dfc_cost,2);$tot_dfc_cost+=$dfc_cost; ?></td>
					 <td width="60" align="right"><? echo number_format($interest_cost,2);$tot_interest_cost+=$interest_cost; ?></td>
					 <td width="60" align="right"><? echo number_format($incometax_cost,2);$tot_incometax_cost+=$incometax_cost; ?></td>

                     <td width="110" align="right"><? echo number_format($total_material_cost,2); ?></td>


                     <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
					 <td width="120" align="right"><? echo number_format($local,2) ?></td>
                     <td width="110" align="right"><? $tot_forg_local=$foreign+$local; echo number_format($tot_forg_local,2);  ?></td>
                     <td width="110" align="right"><? $tot_netFOB=$order_value-$tot_forg_local; echo number_format($tot_netFOB,2); //.'_'.$row[csf('avg_unit_price')] ?></td>

                     <td width="100" align="right"><? $tot_cmValue=$tot_netFOB-$total_material_cost; echo number_format($tot_cmValue,2);       //echo number_format($order_value-$total_material_cost,2);?></td>
					 <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     <td width="110" align="right"><? $tot_margin=$tot_cmValue-$cm_cost; echo  number_format($tot_cmValue-$cm_cost,2); ?></td>
                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo number_format($tot_margin/$order_qty_pcs,2); ?></div></td>

                    <td width="60"><?php echo number_format(($tot_margin / $order_value) * 100, 2); ?></td>
                    
                    <?
					//echo $smv_eff_arr[$row[csf('job_no')]]['cpm'].'/'.$smv_eff_arr[$row[csf('job_no')]]['exc_rate'];
					?>

                    <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? $cpmVal=$smv_eff_arr[$row[csf('job_no')]]['cpm']/$smv_eff_arr[$row[csf('job_no')]]['exc_rate']; echo  number_format($cpmVal*100/$smv_eff_arr[$row[csf('job_no')]]['eff'],3); ?></div></td>

                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px" title="<?=$tot_cmValue.'='.$order_qty_pcs.'='.$row[csf('set_smv')].'='.$row[csf('ratio')]; ?>"><? echo number_format(($tot_cmValue/$order_qty_pcs)/($row[csf('set_smv')]/$row[csf('ratio')]),3); ?></div></td>

                      <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo $lib_user_arr[$row[csf('approved_by')]]; ?></div></td>
                      <td width="110" align="center"><div style="word-wrap:break-word;"><? echo $row[csf('approved_date')]; ?></div></td>
                      <td><? echo $app_status_arr[$row[csf('approved')].$row[csf('ready_to_approved')].$row[csf('partial_approved')]];?></td>
					<?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";
						else $color_pl="";
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
						$expect_variance=$total_profit-$expected_profit_per;

					?>
				  </tr>
				<?
				$total_order_qty_pcs+=$order_qty_pcs;
				$total_plan_cut_qnty_pcs+=$plan_cut_qnty;
				$total_order_qty+=$row[csf('po_quantity')];
				$total_order_amount+=$order_value;
				$total_yarn_required+=$yarn_req;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$all_over_print_cost+=$all_over_cost;
				$total_trim_cost+=$trim_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;

				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_cm_value+=$cm_value;
				$total_cm_new_value+=$tot_cmValue;
				$total_forg_local_amount_new+=$tot_forg_local;
				$total_FOB_val_amount+=$tot_netFOB;

				$total_margin_new_amount+=$tot_margin;
				$total_tot_cost+=$total_cost;
				$total_tot_cost_new+=$total_cost_new;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_finance_chrg_cost+=$finance_chrg;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;

				$total_yarn_qty+=$yarn_req_qty;
				$total_knite_qty+=$knite_qty;
				$total_fabric_dyeing_qty+=$fabric_dyeing_qty;
				$total_washing_qty+=$washing_qty;

				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			?>
			</table>
			</div>
			
			<table class="tbl_bottom" width="5190" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>                    
                    <td width="110">Total:</td>
					<td width="90">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="90">&nbsp;</td>
                    <td width="90" align="right" id="total_order_qnty_pcs"><? echo number_format($total_order_qty_pcs,2); ?></td>
					<td width="110">&nbsp;</td>
					<td width="110"><? echo number_format($total_plan_cut_qnty_pcs,2); ?></td>
					<td width="70" >&nbsp;</td>
                    <td width="100" align="right" id="total_order_amount"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="60"><? echo number_format($total_yarn_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_washing_qty,2); ?></td>
                    <td width="100" align="right"><? echo number_format($tot_fab_cost,2);?></td>
					<td width="100" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>		
					<td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
					<td width="80" align="right" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>

					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2); ?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2); ?></td>
					<td width="100" align="right" id="total_freight_amount"><? echo number_format($total_freight_amount,2); ?></td>
					<td width="120" align="right" id="total_inspection_amount"><? echo number_format($total_inspection_amount,2); ?></td>
					<td width="100" align="right" id="total_certificate_amount"><? echo number_format($total_certificate_amount,2); ?></td>
                    <td width="100" align="right"><? echo number_format($total_finance_chrg_amount,2); ?></td>
					<td width="100" align="right" id="total_currier_amount"><? echo number_format($total_currier_amount,2); ?></td>
					<td width="60" align="right"><? echo number_format($tot_dfc_cost,2);?></td>
					<td width="60" align="right"><? echo number_format($tot_interest_cost,2);?></td>
					<td width="60" align="right"><? echo number_format($tot_incometax_cost,2);?></td>

                    <td width="110" align="right" id="total_tot_cost" title="TTLCost=<? echo $total_tot_cost_new;?>"><? echo number_format($total_meterial_amount,2); ?></td>


                    <td width="120" align="right" id="total_foreign_amount"><? echo number_format($total_foreign_amount,2); ?></td>
					<td width="120" align="right" id="total_local_amount"><? echo number_format($total_local_amount,2); ?></td>
                    <td width="110" align="right"><? echo number_format($total_forg_local_amount_new,2); ?></td>
                    <td width="110" align="right"><? echo number_format($total_FOB_val_amount,2); ?></td>

					<td width="100" align="right" id="total_cm_value"><? echo number_format($total_cm_new_value,2); ?></td>
					<td width="100" align="right" id="total_cm_amount"><? echo number_format($total_cm_amount,2); ?></td>
					<td width="110"><? echo number_format($total_margin_new_amount,2); ?></td>
					<td width="110">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td>&nbsp;</td>

				</tr>
			</table>
			

			<!-- Order Wise Budget Cost Summary data comes from this table -->
			<table class="tbl_bottom" width="4970" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" style="display: none;">
				<tr>
					<td width="40">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110"><strong>Total:</strong></td>
					<td width="70">&nbsp;</td>
					<td width="70">&nbsp;</td>
					
                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
                    <td width="90">&nbsp;</td>
					
                    <td width="70">&nbsp;</td>
					<td width="100" align="right" id="total_order_amount"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="90" align="right" id="total_order_qnty_pcs"><? echo number_format($total_order_qty_pcs,2); ?></td>
					<td width="100">&nbsp;</td>
					<td width="80" align="right" id="total_yarn_cost"><? echo number_format($total_yarn_cost,2); ?></td>
					<td width="80">&nbsp;</td>
					<td width="100" align="right" id="total_purchase_cost"><? echo number_format($total_purchase_cost,2); ?></td>
					<td width="80">&nbsp;</td>
					<td width="80" align="right" id="total_knitting_cost"><? echo number_format($total_knitting_cost,2); ?></td>
					<td width="100">&nbsp;</td>
					<td width="110" align="right" id="total_yarn_dyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
					<td width="120">&nbsp;</td>
					<td width="100" align="right" id="total_fabric_dyeing_cost"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
					<td width="90" align="right" id="total_heat_setting_cost"><? echo number_format($total_heat_setting_cost,2); ?></td>		
					<td width="100" align="right" id="total_finishing_cost"><? echo number_format($total_finishing_cost,2); ?></td>
					<td width="90" align="right" id="total_washing_cost"><? echo number_format($total_washing_cost,2); ?></td>
					<td width="90" align="right" id="all_over_print_cost"><? echo number_format($all_over_print_cost,2); ?></td>
					<td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
					<td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
					<td width="80" align="right" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2); ?></td>
					<td width="120" align="right" id="total_foreign_amount"><? echo number_format($total_foreign_amount,2); ?></td>
					<td width="120" align="right" id="total_local_amount"><? echo number_format($total_local_amount,2); ?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2); ?></td>
					<td width="100" align="right" id="total_freight_amount"><? echo number_format($total_freight_amount,2); ?></td>
					<td width="120" align="right" id="total_inspection_amount"><? echo number_format($total_inspection_amount,2); ?></td>
					<td width="100" align="right" id="total_certificate_amount"><? echo number_format($total_certificate_amount,2); ?></td>
					<td width="100" align="right" id="total_common_oh_amount"><? echo number_format($total_common_oh_amount,2); ?></td>
					<td width="100" align="right" id="total_currier_amount"><? echo number_format($total_currier_amount,2); ?></td>
					
					<td width="100" align="right" id="total_deffdlc_amount"><? echo number_format($total_deffdlc_cost,2); ?></td>
					<td width="100" align="right" id="total_design_amount"><? echo number_format($total_design_cost,2); ?></td>
					<td width="100" align="right" id="total_studio_amount"><? echo number_format($total_studio_cost,2); ?></td>
					
                    <td width="100" align="right" id="total_cm_value"><? echo number_format($total_cm_value,2); ?></td>
					<td width="120">&nbsp;</td>
					<td width="100" align="right" id="total_cm_amount"><? echo number_format($total_cm_amount,2); ?></td>
					<td align="right" id="total_tot_cost"><? echo number_format($total_tot_cost,2); ?></td>
				</tr>
			</table>
			<table>
				<tr>
					<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_cost_up,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');

					//$chart_data_qnty="Fabric Cost;".$total_fab_cost."\nTrimCost;".$total_trim_cost."\nEmbelishment Cost;".$total_embelishment_cost."\nCommercial Cost;".$total_commercial_cost."\nCommission Cost;".$total_commssion."\nTesting Cost;".$total_testing_cost."\nFreightCost;".$total_freight_cost."\nCM Cost;".$total_cm_cost."\nInspection Cost;".$total_inspection."\nCertificate Cost;".$total_certificate_cost."\nCommn OH Cost;".$total_common_oh."\nCurrier Cost;".$total_currier_cost."\n Profit/Loss;".$total_fabric_profit_up."\n";
					?>
					<input type="hidden" id="graph_data" value="<? //echo substr($chart_data_qnty,0,-1); ?>"/>
				</tr>
			</table>
			<br>
			<a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>
			
			<div style="width:600px; display:none" id="yarn_summary" >
				<div id="data_panel2" align="center" style="width:500px">
					 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
				 </div>
				
                <table class="rpt_table" width="590" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                	<tr>
                                    	<th colspan="7">Yarn Cost Summary</th>
                                    </tr>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="140">Composition</th>
                                        <th width="60">Yarn Count</th>
                                        <th width="80">Type</th>
                                        <th width="100">Req. Qty</th>
                                        <th width="70">Avg. Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <?
                                $s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
                                foreach($yarn_des_data as $count=>$count_value)
                                {
								foreach($count_value as $Composition=>$composition_value)
                                {
								foreach($composition_value as $percent=>$percent_value)
                                {
								foreach($percent_value as $type=>$type_value)
                                {
                                    if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    //$yarn_desc=explode("**",$key);
                                    
                                    $tot_yarn_req_qnty+=$type_value['qty']; 
                                    $tot_yarn_req_amnt+=$type_value['amount'];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
                                        <td><? echo $s; ?></td>
                                        <td><div style="word-wrap:break-word; width:140px"><? echo $composition[$Composition]." ".$percent."%"; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:60px"><? echo $yarn_count_library[$count]; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:80px"><? echo $yarn_type[$type]; ?></div></td>
                                        <td align="right"><? echo number_format($type_value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($type_value['amount']/$type_value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($type_value['amount'],2); ?></td>
                                    </tr>
                                    <?	
                                    $s++;
								}
								}
								}
                                }
                                ?>
                                <tfoot>
                                    <th colspan="4" align="right">Total</th>
                                    <th align="right"><? echo number_format($tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt,2); ?></th>
                                </tfoot>
                            </table>
			</div>
			</fieldset>
			</div>
			<?
		}
	}
	
	if($report_type==3 && $cbo_search_type==1) //Report button //Job Wise
	{
		if($template==1)
		{

			$app_status_arr=array(
				222=>'Not Submit',
				202=>'Not Submit',
				212=>'Ready For Approval',
				211=>'Partial Approved',
				111=>'Full Approved'
			);
			$style1="#E9F3FF";$style="#FFFFFF";
			$cm_cost_formula_sql=sql_select( "select id, cm_cost_method from variable_order_tracking where  variable_list=22 $company_con_name order by id");
			$cm_cost_formula=$cm_cost_predefined_method[$cm_cost_formula_sql[0][csf('cm_cost_method')]];
			$asking_profit_arr=array();
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $company_con_id");//$date_max_profit
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
				}
			}
			$asking_profit=array();
			if($db_type==0)
			{
				
				$po_grp="group_concat(b.id) as po_id";
			}
			else
			{
				$po_grp="listagg(CAST(b.id as VARCHAR(4000)),',') within group (order by b.id) as po_id";
			}

		  $sql_budget="select a.id,a.company_name,a.job_no_prefix_num,a.team_leader,a.set_smv, max(b.insert_date) as insert_date, c.insert_date as pre_insert_date, c.costing_date, max(b.update_date) as update_date, a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, sum(b.plan_cut) as plan_cut,sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved,$po_grp from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.entry_from=158 $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $approvalCon $file_con_b $ref_con_b group by  a.id,a.company_name,a.job_no_prefix_num, c.insert_date , c.costing_date,a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.team_leader,a.dealing_marchant,a.set_smv, a.gmts_item_id, a.total_set_qnty,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved order by a.job_no";
			 //echo $sql_budget; die;
		

			$result_sql_budget=sql_select($sql_budget);
			$tot_rows_budget=count($result_sql_budget);
			$budget_data_arr=array();
			foreach($result_sql_budget as $row )
			{
				$job_id.=$row[csf('id')].',';
			}
			$jobIds=chop($job_id,',');  
			$job_ids=count(array_unique(explode(",",$job_id)));
			if($db_type==2 && $job_ids>1000)
			{
				$job_cond_for_in=" and (";
				$jobIdsArr=array_chunk(explode(",",$jobIds),999);
				foreach($jobIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$job_cond_for_in.=" b.id in($ids) or"; 
				}
				$job_cond_for_in=chop($job_cond_for_in,'or ');
				$job_cond_for_in.=")";
			}
			else
			{
				$job_cond_for_in=" and b.id in($jobIds)";
			}
			$cpm_arr=array();$cpm_date_arr=array(); //$yarn_desc_array=array();
			$finance_arr=array();
			$sql_cpm=sql_select("select applying_period_date, applying_period_to_date, cost_per_minute,interest_expense from lib_standard_cm_entry where 1=1 $company_con_id");
			foreach($sql_cpm as $cpMrow )
			{
				$applying_period_date=change_date_format($cpMrow[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($cpMrow[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate = date("d-m-Y",strtotime(add_date(str_replace("'","",$applying_period_date),$j)));
					$cpm_arr[$newdate]['cpm']=$cpMrow[csf('cost_per_minute')];
					$finance_arr[$newdate]['finPercent']=$cpMrow[csf('interest_expense')];
				}
			}
			$smv_eff_arr=array(); $costing_library=array();
			$sql_smv_cpm=sql_select("select a.job_no, a.costing_date, a.sew_smv, a.sew_effi_percent,a.exchange_rate,b.company_name from wo_pre_cost_mst a,wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond_for_in");
			foreach($sql_smv_cpm as $smv_cpm )
			{
				$dateKey=date("d-m-Y",strtotime($smv_cpm[csf('costing_date')]));
				
				$costing_library[$smv_cpm[csf('job_no')]]=$smv_cpm[csf('costing_date')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['smv']=$smv_cpm[csf('sew_smv')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['eff']=$smv_cpm[csf('sew_effi_percent')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['cpm']=$cpm_arr[$dateKey]['cpm'];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['exc_rate']=$smv_cpm[csf('exchange_rate')]; 
			}
			unset($sql_smv_cpm);
			
			//var_dump($smv_eff_arr);die;
 			$condition= new condition();
			 if(str_replace("'","",$cbo_company_name)>0){
				 $condition->company_name("=$cbo_company_name");
			 }
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			$job_no=str_replace("'","",$txt_job_no);
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num("in($job_no)");
			 }
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
			 }
			  if(str_replace("'","",$cbo_search_date) ==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				$condition->insert_date("$insert_date_cond");
			 }
			if(str_replace("'","",$cbo_year) !=0){
				  $condition->job_year("$jobYearCond");
			 }

			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no");
			 }

			$condition->init();

		    $costing_per_arr=$condition->getCostingPerArr();
			$fabric= new fabric($condition);
			//echo $fabric->getQuery();die;
			//$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_costing_arr=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			$fabric_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_purchase_qty_arr=$fabric->getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			//print_r($fabric_purchase_qty_arr);
			$yarn= new yarn($condition);
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();

			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();
			$yarn= new yarn($condition);
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			$conversion_qty_arr_process=$conversion->getQtyArray_by_orderAndProcess();
			$conversion_costing_po_arr=$conversion->getAmountArray_by_order();

			$conversion_qty= new conversion($condition);
			$conversion_qty_arr_process = $conversion_qty->getQtyArray_by_orderAndProcess();
			$conversion_job_amount_arr_process = $conversion_qty->getAmountArray_by_jobAndProcess();
			$conversion_job_qty_arr_process = $conversion_qty->getQtyArray_by_jobAndProcess();
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149,158);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
			$washing_qty_arr=array(140,142,148,64);	
			
			$not_process_id_print_array = array(1,30,35); 
			$total_print_amount=$total_embroidery_amount=$total_special_amount=$total_other_amount=$total_gmtdying_amount=0;$tot_fab_purchase_knit_qty=$tot_fab_purchase_woven_qty=0;
			foreach($result_sql_budget as $row )
			{
				$total_order_amount+=$row[csf('po_total_price')];
				$poIdArr=array_unique(explode(",",$row[csf('po_id')]));
				foreach($poIdArr as $poId)
				{

					$total_yarn_cost+=$yarn_costing_arr[$poId];
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['finish'][$poId][2]);
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['finish'][$poId][2]);
					
					//$tot_fab_purchase_knit_qty+=array_sum($fabric_purchase_qty_arr['knit']['grey'][$poId][2]);
					//$tot_fab_purchase_woven_qty+=array_sum($fabric_purchase_qty_arr['woven']['grey'][$poId][2]);
					//echo $fab_purchase_knit.'='.$fab_purchase_woven;
					$total_purchase_cost+=($fab_purchase_knit+$fab_purchase_woven);
					$total_trim_cost+=$trims_costing_arr[$poId];
					$total_yarn_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][30]);
					$all_over_cost=0;
					foreach($aop_cost_arr as $aop_process_id)
					{
						$all_over_cost+=array_sum($conversion_costing_arr_process[$poId][$aop_process_id]);
					}
					$all_over_print_cost+=$all_over_cost;				
					
					$knit_cost=0;
					foreach($knit_cost_arr as $process_id)
					{
						$knit_cost+=array_sum($conversion_costing_arr_process[$poId][$process_id]);
					}
					$total_knit_cost+=$knit_cost;
					
					/*$fabric_dyeing_cost=0;
					foreach($fabric_dyeingCost_arr as $fab_process_id)
					{
						$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][$fab_process_id]);
					}*/
					$fabric_dyeing_cost=0;
					/*foreach($fabric_dyeingCost_arr as $fab_process_id)
					{
						$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][$fab_process_id]);
					}*/
					foreach($conversion_cost_head_array as $key=>$val)
						{
							if (!in_array($key, $not_process_id_print_array)) 
							{
								$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][$key]);
							}
							
						}
						
					$total_fabric_dyeing_cost+=$fabric_dyeing_cost;				
								
					$fabric_finish=0;
					foreach($fab_finish_cost_arr as $fin_process_id)
					{
						$fabric_finish+=array_sum($conversion_costing_arr_process[$poId][$fin_process_id]);
					}
					$total_finishing_cost+=$fabric_finish;
					
					$total_heat_setting_cost+=array_sum($conversion_costing_arr_process[$poId][33]);
					
					$washing_cost=0;
					foreach($washing_cost_arr as $w_process_id)
					{
						$washing_cost+=array_sum($conversion_costing_arr_process[$poId][$w_process_id]);
					}
					$total_washing_cost+=$washing_cost;
					
					$total_print_amount+=$emblishment_costing_arr_name[$poId][1];
					$total_embroidery_amount+=$emblishment_costing_arr_name[$poId][2];
					$total_special_amount+=$emblishment_costing_arr_name[$poId][4];
					$total_other_amount+=$emblishment_costing_arr_name[$poId][99];
					$total_wash_cost+=$emblishment_costing_arr_name_wash[$poId][3];
					$total_gmtdying_amount+=$emblishment_costing_arr_name[$poId][5];
					
					$total_commercial_cost+=$commercial_costing_arr[$poId];
					
					$total_foreign_amount+=$commission_costing_arr[$poId][1];
					$total_local_amount+=$commission_costing_arr[$poId][2];
					
					
					$total_testing_cost+=$other_costing_arr[$poId]['lab_test'];
					$total_freight_cost+=$other_costing_arr[$poId]['freight'];
					
					
					$total_inspection_cost+=$other_costing_arr[$poId]['inspection'];
					$total_certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
					$total_common_oh+=$other_costing_arr[$poId]['common_oh'];
					$total_currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
					$total_cm_cost+=$other_costing_arr[$poId]['cm_cost'];				
					
					
					$total_deffdlc_cost+=$other_costing_arr[$poId]['deffdlc_cost'];
					$total_design_cost+=$other_costing_arr[$poId]['design_cost'];
					$total_studio_cost+=$other_costing_arr[$poId]['studio_cost'];

				}
			}
			
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			$total_purchase_cost_percentage=$total_purchase_cost/$total_order_amount*100;
			$total_trim_cost_percentage=$total_trim_cost/$total_order_amount*100;
			$total_yarn_dyeing_percentage=$total_yarn_dyeing_cost/$total_order_amount*100;
			$all_over_print_cost_percentage=$all_over_print_cost/$total_order_amount*100;
			$total_knit_cost_percentage=$total_knit_cost/$total_order_amount*100;
			
			$total_dyefin_cost=$total_fabric_dyeing_cost+$total_finishing_cost+$total_heat_setting_cost;//+$total_washing_cost
			$total_dyefin_cost_percentage=$total_dyefin_cost/$total_order_amount*100;
			
			
			$total_embelishment_cost=$total_print_amount+$total_embroidery_amount+$total_special_amount+$total_gmtdying_amount+$total_other_amount+$total_wash_cost;
			$total_embelishment_cost_percentage=$total_embelishment_cost/$total_order_amount*100;

			$total_commercial_cost_percentage=$total_commercial_cost/$total_order_amount*100;

			$total_commission_cost=$total_foreign_amount+$total_local_amount;
			$total_commission_cost_percentage=$total_commission_cost/$total_order_amount*100;
			
			
			$total_testing_cost_percentage=$total_testing_cost/$total_order_amount*100;
			$total_freight_cost_percentage=$total_freight_cost/$total_order_amount*100;
			$total_inspection_cost_percentage=$total_inspection_cost/$total_order_amount*100;
			$total_certificate_cost_percentage=$total_certificate_cost/$total_order_amount*100;
			$total_common_oh_percentage=$total_common_oh/$total_order_amount*100;
			$total_currier_cost_percentage=$total_currier_cost/$total_order_amount*100;
			$total_cm_cost_percentage=$total_cm_cost/$total_order_amount*100;
			
			
			$total_tot_cost=$total_yarn_cost+$total_purchase_cost+$total_knit_cost+$total_washing_cost+$all_over_print_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_trim_cost+$total_testing_cost+$total_print_amount+$total_embroidery_amount+$total_gmtdying_amount+$total_special_amount+$total_other_amount+$total_wash_cost+$total_commercial_cost+$total_foreign_amount+$total_local_amount+$total_freight_cost+$total_inspection_cost+$total_certificate_cost+$total_common_oh+$total_currier_cost+$total_cm_cost;			
			$total_tot_cost_percentage=$total_tot_cost/$total_order_amount*100;
			
			$total_material_cost=$total_yarn_cost+$total_purchase_cost+$total_trim_cost+$total_yarn_dyeing_cost+$all_over_print_cost;
			$total_material_cost_percentage=$total_material_cost/$total_order_amount*100;
			
			$total_service_cost=$total_knit_cost+$total_dyefin_cost+$total_embelishment_cost;
			$total_service_cost_percentage=$total_service_cost/$total_order_amount*100;			
			
			
			?>
			
	 <script>
			function toggle()
			{
				var ele = document.getElementById("yarn_summary");
				//alert(ele);
				var text = document.getElementById("displayText");
				if(ele.style.display!= "none")
				{
					ele.style.display = "none";
					text.innerHTML = "Show Yarn Summary";
				}
				else
				{
					ele.style.display = "block";
					text.innerHTML = "Hide Yarn Summary";
				}
			}

	  </script>
			
			<? 
				$width=6405;
				ob_start(); 
			?>
			
		<div style="width:<? echo $width;?>px;">
			<div style="width:900px;" align="left">
				<table width="900" cellpadding="0" cellspacing="2" border="0">
					<tr>
						<td width="350" align="left">
							<table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
								<thead align="center">
									<tr>
										<th colspan="4">Order Wise Budget Cost Summary</th>
									</tr>
									<tr>
										<th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
									</tr>
								</thead>
								<tr bgcolor="<? echo $style1; ?>">
									<td width="20">1</td>
									<td width="130">Yarn Cost</td>
									<td width="120" align="right"><? echo number_format($total_yarn_cost,2);?></td>
									<td align="right"><? echo number_format($total_yarn_cost_percentage,2);?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>2</td>
									<td>Fabric Purchase</td>
									<td align="right"><? echo number_format($total_purchase_cost,2);?></td>
									<td align="right" ><? echo number_format($total_purchase_cost_percentage,2);?></td>
								</tr>
								<tr bgcolor="<?  echo $style1; ?>">
									<td>3</td>
									<td>Trims Cost</td>
									<td align="right"><? echo number_format($total_trim_cost,2);?></td>
									<td align="right"><? echo number_format($total_trim_cost_percentage,2);?></td>
								</tr>
                                <tr bgcolor="#CCCCCC">
									<td></td>
									<td><strong>Actual Material Cost</strong></td>
									<td align="right" title="Yarn Cost+Trim Cost+Finished Fabric Purchase Cost"><? 
									$total_act_material_cost=$total_yarn_cost+$total_purchase_cost+$total_trim_cost;
									echo number_format($total_act_material_cost,2); ?></td>
									<td align="right"><? echo number_format($total_act_material_cost/$total_order_amount*100,2); ?></td>
								</tr>
                                
								<tr bgcolor="<? echo $style; ?>">
									<td>4</td>
									<td>Yarn Dyeing Cost</td>
									<td align="right"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
									<td align="right"><? echo number_format($total_yarn_dyeing_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>5</td>
									<td>AOP Cost</td>
									<td align="right"><? echo number_format($all_over_print_cost,2); ?></td>
									<td align="right"><? echo number_format($all_over_print_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total B2B Cost</strong></td>
									<td align="right"><? echo number_format($total_material_cost,2); ?></td>
									<td align="right"><? echo number_format($total_material_cost_percentage,2); ?></td>
								</tr>

                                <tr bgcolor="<? echo $style1; ?>">
									<td>6</td>
									<td>Knitting Cost</td>
									<td align="right"><? echo number_format($total_knit_cost,2); ?></td>
									<td align="right"><? echo number_format($total_knit_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>7</td>
									<td>Dyeing & Finishing Cost</td>
									<td align="right" title="<?=$total_fabric_dyeing_cost.'='.$total_finishing_cost.'='.$total_heat_setting_cost; ?>"><? echo number_format($total_dyefin_cost,2); ?></td>
									<td align="right"><? echo number_format($total_dyefin_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>8</td>
									<td>Print/ Embro./Wash Cost/Special/Others</td>
									<td align="right"><? echo number_format($total_embelishment_cost,2); ?></td>
									<td align="right"><? echo number_format($total_embelishment_cost_percentage,2); ?></td>
								</tr>
                                <tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Actual Service Cost:</strong></td>
									<td align="right" title="Y/D Cost+AOP Cost+Knit Cost+Dye&Fin Cost+Printing Cost+Embroidery Cost+WashCost+Other ServiceCost(Embellishment Other Cost+Dyeing"><? 
									$total_act_service_cost=$total_yarn_dyeing_cost+$all_over_print_cost+$total_knit_cost+$total_dyefin_cost+$total_embelishment_cost;
									echo number_format($total_act_service_cost,2); ?></td>
									<td align="right"><? echo number_format($total_service_cost_percentage,2); ?></td>
								</tr>
                                
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Service Cost</strong></td>
									<td align="right"><? echo number_format($total_service_cost,2); ?></td>
									<td align="right"><? echo number_format($total_service_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Material & Service</strong></td>
									<td align="right"><? echo number_format($total_service_cost+$total_material_cost,2); ?></td>
									<td align="right"><? echo number_format(($total_service_cost+$total_material_cost)/$total_order_amount*100,2); ?></td>
								</tr>

                                
								<tr bgcolor="<? echo $style; ?>">
									<td>9</td>
									<td>Commercial Cost</td>
									<td align="right"><? echo number_format($total_commercial_cost,2); ?></td>
									<td align="right"><? echo number_format($total_commercial_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>10</td>
									<td>Commision Cost</td>
									<td align="right"><? echo number_format($total_commission_cost,2); ?></td>
									<td align="right"><? echo number_format($total_commission_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>11</td>
									<td>Testing Cost</td>
									<td align="right"><? echo number_format($total_testing_cost,2); ?></td>
									<td align="right"><? echo number_format($total_testing_cost_percentage,2); ?></td>
								</tr>
                                <tr bgcolor="<? echo $style1; ?>">
									<td>12</td>
									<td>Freight Cost</td>
									<td align="right"><? echo number_format($total_freight_cost,2); ?></td>
									<td align="right"><? echo number_format($total_freight_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>13</td>
									<td>Inspection Cost</td>
									<td align="right"><? echo number_format($total_inspection_cost,2); ?></td>
									<td align="right"><? echo number_format($total_inspection_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>14</td>
									<td>Certificate Cost</td>
									<td align="right"><? echo number_format($total_certificate_cost,2); ?></td>
									<td align="right"><? echo number_format($total_certificate_cost_percentage,2); ?></td>
								</tr>
									<tr bgcolor="<? echo $style; ?>">
									<td>15</td>
									<td>Operating Exp.</td>
									<td align="right"><? echo number_format($total_common_oh,2); ?></td>
									<td align="right"><? echo number_format($total_common_oh_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>16</td>
									<td>Courier Cost</td>
									<td align="right"><? echo number_format($total_currier_cost,2); ?></td>
									<td align="right"><? echo number_format($total_currier_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>17</td>
									<td>CM Cost</td>
									<td align="right"><? echo number_format($total_cm_cost,2); ?></td>
									<td align="right"><? echo number_format($total_cm_cost_percentage,2); ?></td>
								</tr>
								
                                <tr bgcolor="<? echo $style1; ?>">
									<td>18</td>
									<td>Total Cost</td>
									<td align="right"><? echo number_format($total_tot_cost,2); ?></td>
									<td align="right"><? echo number_format($total_tot_cost_percentage,2); ?></td>
								</tr>
                                  <tr bgcolor="<? echo $style; ?>">
									<td>19</td>
									<td>Margin</td>
									<td align="right" title="Total Order Value-Total Cost"><? 
									$tot_margin=$total_order_amount-$total_tot_cost;
									$total_tot_margin_percentage=$tot_margin/$total_order_amount*100;
									echo number_format($tot_margin,2); ?></td>
									<td align="right"><? echo number_format($total_tot_margin_percentage,2); ?></td>
								</tr>
								
                                <tr bgcolor="<? echo $style1; ?>">
									<td>20</td>
									<td>Total Order Value</td>
									<td align="right"><? echo number_format($total_order_amount,2); ?></td>
									<td align="right"><? echo number_format($total_order_amount/$total_order_amount*100,2); ?></td>
								</tr>
							</table>
						</td>
						
					</tr>
				</table>
			</div>
			<br/>			
			
			<?
			
			if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else if(str_replace("'","",$cbo_search_date)==3) $caption="PO Insert Date";
					else $caption="Cancelled Date";
					
					
						
			?>
			<fieldset style="width:100%;" id="content_search_panel2">
            		<div>
                       <span style="font-size:20px; font-weight:bold;"><? echo $company_library[$company_name]; ?></span><br/>
                       <span style="font-size:16px; font-weight:bold;">Cost Breakdown Analysis<br/></span>
                       <strong>From  <? echo $caption.' &nbsp;'.$date_from;  ?>  To  <? echo $date_to; ?></strong>
                    </div>
			   <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit'];?>
			<table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
                        <th width="110">Team Leader</th>
						<th width="100">Company</th>
                        <th width="70">Buyer</th>
                        <th width="110">Style</th>
						<th width="70">Job No</th>
                        <th width="110">Gmts Item Name</th>
                        <th width="110">SMV</th>
                        <th width="60">CPM</th>
                        <th width="110">Efficiency</th>
                        <th width="110">Costing Date</th>
                        <th width="90">Style Qty</th>
                        <th width="60">UOM</th>
                        <th width="90">Total SMV</th>
                        <th width="90">Style Qty (Pcs)</th>
                        <th width="70">Excess Cut %</th>
                        <th width="90">Plan Cut Qty (Pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">Style Value</th>
                        <th width="60">TTL Y/Qty</th>
                        <th width="60">TTL Y/Cost</th>
                        <th width="60">%</th>
                        <th width="60">Fin Fab Purchase Qty</th>
                        <th width="60">Fin Fab Purchase Cost</th>
                        <th width="60">%</th>
                        <th width="100">Trim Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">Y/D Qty</th>
                        <th width="60">Y/D Cost</th>
                        <th width="60">%</th>
                        <th width="60">AOP Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">Total B2B Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">TTL Knit Qty</th>
                        <th width="60">TTL Knit Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">TTL Dye & Fin Qty</th>
                        <th width="60">TTL Dye & Fin Cost</th>
                        <th width="60">%</th>
                        
                        <th width="80">Printing</th>
                        <th width="60">%</th>
						<th width="85">Embroidery</th>
                        <th width="60">%</th>
                        <th width="85">Gmt Dying</th>
                        <th width="60">%</th>
						<th width="80">Special Works</th>
                        <th width="60">%</th>
						<th width="80">Wash Cost</th>
                        <th width="60">%</th>
						<th width="80">Other</th>
                        
                        <th width="100">Total Service Cost</th>
                        <th width="60">%</th>
                        <th width="60">Actual Material Cost</th>
                        <th width="60">%</th>
                        <th width="100">Actual Service Cost</th>
                        <th width="60">%</th>
                       
						
                        <th width="100">Total Mat+Svc Cost</th>
                        <th width="60">%</th>
                        <th width="120">Commercial Cost</th>
                        <th width="60">%</th>
						<th width="100">Testing Cost</th>
						<th width="100">Freight Cost</th>
						<th width="120">Inspection Cost</th>
						<th width="100">Certificate Cost</th>
                        <th width="100">Operating Exp.</th>
						<th width="100">Courier Cost</th>
                        <th width="120">Foreign Comm</th>
						<th width="120">Local Comm</th>
                        <th width="110">Total Comm</th>
						<th width="100" title="<?php echo $cm_cost_formula; ?>">CM Cost</th>
                        <th width="60">%</th>
                        <th width="100">Total Cost</th>
                        <th width="60">%</th>
                        <th width="110">Total Margin</th>
                        <th width="60">Margin %</th>
                        <th width="110">Approved By</th>
                        <th width="110">Appv. Date & Time</th>
                        <th>Appv. Status</th>
					</tr>

				</thead>
			</table>
			<div style="width:<? echo $width+18;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
				
				$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; 	
				$total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
	$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=$total_service_cost=$total_print_amount=$total_gmtdying_amount=0;
	$total_embroidery_amount=$total_special_amount=$total_other_amount=$total_wash_cost=$total_certificate_cost=$total_currier_cost=0;
	$total_all_over_print_cost=$total_b2b_cost=$total_act_material_cost=$total_act_service_cost=$total_material_service_cost=$total_order_amount=$total_cost=$total_common_oh=0;
					

			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				else if(str_replace("'","",$cbo_search_date)==4)
				{
					$update_date=explode(" ",$row[csf('update_date')]);
					$ship_po_recv_date=change_date_format($update_date[0]);
				}
				$dzn_qnty=$costing_per_arr[$row[csf('job_no')]];
				

				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$order_qty=$row[csf('po_quantity')];
				$order_uom=$row[csf('order_uom')];
				$costing_date=$row[csf('costing_date')];

				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];

				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];

			
				$total_plancut_amount+=$plancut_value;
				$po_ids=array_unique(explode(",",$row[csf('po_id')]));
						$commercial_cost=$yarn_costing=$yarn_req_qty=$fab_purchase_knit=$fab_purchase_woven=$conv_cost_poWise=$yarn_dyeing_cost=$heat_setting_cost=$trim_amount=$print_amount=$embroidery_amount=$special_amount=$wash_cost=$other_amount=$foreign=$local=$test_cost=$freight_cost=$inspection=$certificate_cost=$common_oh=$currier_cost=$cm_cost=$dfc_cost=$interest_cost=$incometax_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$fab_purchase_knit_qty=$gmtdying_amount=$fab_purchase_woven_qty=0;
						$fabric_dyeing_cost=0;
						foreach($po_ids as $pid)
						{
							$commercial_cost+=$commercial_costing_arr[$pid];
							$yarn_costing+=$yarn_costing_arr[$pid];
							$yarn_req_qty+=$yarn_req_qty_arr[$pid];
							$fab_purchase_knit+=array_sum($fabric_costing_arr['knit']['grey'][$pid][2]);
							$fab_purchase_woven+=array_sum($fabric_costing_arr['woven']['grey'][$pid][2]);
							
							$fab_purchase_knit_qty+=array_sum($fabric_purchase_qty_arr['knit']['grey'][$pid][2]);
							$fab_purchase_woven_qty+=array_sum($fabric_purchase_qty_arr['woven']['grey'][$pid][2]);
							
							$conv_cost_poWise+=array_sum($conversion_costing_po_arr[$pid]);
							$yarn_dyeing_cost+=array_sum($conversion_costing_arr_process[$pid][30]);
							$heat_setting_cost+=array_sum($conversion_costing_arr_process[$pid][33]);
							
							$trim_amount+= $trims_costing_arr[$pid];
							$print_amount+=$emblishment_costing_arr_name[$pid][1];
							$embroidery_amount+=$emblishment_costing_arr_name[$pid][2];
							$special_amount+=$emblishment_costing_arr_name[$pid][4];
							$wash_cost+=$emblishment_costing_arr_name_wash[$pid][3];
							$gmtdying_amount+=$emblishment_costing_arr_name[$pid][5];
							$other_amount+=$emblishment_costing_arr_name[$pid][99];
							$foreign+=$commission_costing_arr[$pid][1];
							$local+=$commission_costing_arr[$pid][2];
							$test_cost+=$other_costing_arr[$pid]['lab_test'];
							$freight_cost+=$other_costing_arr[$pid]['freight'];
							$inspection+=$other_costing_arr[$pid]['inspection'];
							$certificate_cost+=$other_costing_arr[$pid]['certificate_pre_cost'];
							$common_oh+=$other_costing_arr[$pid]['common_oh'];
							$currier_cost+=$other_costing_arr[$pid]['currier_pre_cost'];
							$cm_cost+=$other_costing_arr[$pid]['cm_cost'];
							$dfc_cost+=$other_costing_arr[$pid]['deffdlc_cost'];//$deffdlc_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;
							$interest_cost+=$interest_cost_arr[$pid]*$order_qty_pcs;
							$incometax_cost+=$incometax_cost_arr[$pid]*$order_qty_pcs;
							
							foreach($conversion_cost_head_array as $key=>$val)
							{
								if (!in_array($key, $not_process_id_print_array)) 
								{
									//$fabric_dyeing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fab_process_id]);
									$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$pid][$key]);
								}
								
							}
						
						}
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					 <td width="40"><? echo $i; ?></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $team_member_arr[$row[csf('team_leader')]]; ?></div></td>

                     <td width="100"><p><? echo $company_library[$row[csf('company_name')]]; ?></p></td>
                     <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('style_ref_no')]; ?></div></td>
					 <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110" align="right"><p><? echo $row[csf('set_smv')]; ?></p></td>
                     <td width="60" align="right">
						<? 
							$cpmVal=$smv_eff_arr[$row[csf('job_no')]]['cpm']/$smv_eff_arr[$row[csf('job_no')]]['exc_rate'];
							echo  number_format($cpmVal*100/$smv_eff_arr[$row[csf('job_no')]]['eff'],3); 
                        ?>
                     </td>
                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $smv_eff_arr[$row[csf('job_no')]]['eff']; ?></div></td>
                    <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo change_date_format($costing_date); ?></div></td>
                  
                    <td width="90" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                    <td width="60" align="center"><p><? echo $unit_of_measurement[$order_uom]; ?></p></td>
                    <td width="90" align="right"><p><? echo number_format($order_qty*$row[csf('set_smv')],2); ?></p></td>

                    <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                    <td width="70" align="right"><div style="word-wrap:break-word; width:70px"><? echo $row[csf('excess_cut')]; ?></div></td>
                    <td width="90" align="right"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('plan_cut')]; ?></div></td>
                    <td width="70" align="right"><p><? echo number_format($order_value/$order_qty,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>

                    <?
						$avg_rate=$yarn_costing/$yarn_req_qty;
						$yarn_cost_percent=($yarn_costing/$order_value)*100;
						$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
						$fab_purchase_qty=$fab_purchase_knit_qty+$fab_purchase_woven_qty;
						//$fab_purchase_knit_qty+=array_sum($fabric_qty_arr['knit']['grey'][$pid]);
							//$fab_purchase_woven_qty
						$tot_fabric_cost=$yarn_costing+$fab_purchase+$conv_cost_poWise;


						$knit_cost=0;$knite_qty=0;
						foreach($knit_cost_arr as $process_id)
						{ //conversion_job_amount_arr_process
							$knit_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$process_id]);
							$knite_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$process_id]);
						}
						$all_over_print_cost=$all_over_print_qty=0;
						foreach($aop_cost_arr as $aop_process_id)
						{
							$all_over_print_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$aop_process_id]);
							$all_over_print_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$aop_process_id]);
						}
							
						$yarn_dye_cost=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][30]);
						$yarn_dye_qty=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][30]);
						
						$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_qty=$fab_purchase_knit_qty+$fab_purchase_woven_qty;
						//$fabric_dyeing_cost=0;
						//foreach($fabric_dyeingCost_arr as $fab_process_id)
						//{
							//$fabric_dyeing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fab_process_id]);
							//$fabric_dyeing_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$fab_process_id]);
						//}
						
						/*foreach($conversion_cost_head_array as $key=>$val)
						{
							if (!in_array($key, $not_process_id_print_array)) 
							{
								$fabric_dyeing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fab_process_id]);
							}
							
						}*/
						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_finish=0;
						foreach($fab_finish_cost_arr as $fin_process_id)
						{
							$fabric_finish+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fin_process_id]);
						}
						$washing_cost=0;
						foreach($washing_cost_arr as $w_process_id)
						{
							$washing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$w_process_id]);
						}

						$washing_qty=0;
						foreach($washing_qty_arr as $w_process_id)
						{
							$washing_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$w_process_id]);
						}


					//conversion_qty_arr_process
						
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;
						$finance_chrg=$order_value*$smv_eff_arr[$row[csf('job_no')]]['finPercent']/100;
						
						

						$total_material_cost=$total_cost_new;
						$others_cost_value = $total_cost -($cm_cost+$freight_cost+$commercial_cost+($foreign+$local));
						$net_order_val=$order_value-(($foreign+$local)+$commercial_cost+$freight_cost);
						$cm_value=$net_order_val-$others_cost_value;

						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100;
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;

						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
						if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
						if($commercial_cost<=0) $color_com="red"; else $color_com="";

					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_gmtdying_amount+=$gmtdying_amount;
					$total_wash_cost+=$wash_cost;
					$total_test_cost_amount+=$test_cost;

					/*$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_forg_local_amount+=$tot_forg_local;
					$total_FOB_amount+=$tot_netFOB;

					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;
					$total_finance_chrg_amount+=$finance_chrg;*/

					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_meterial_amount+=$total_material_cost;
					$total_cm_new_amount+=$tot_cmValue;
					$total_cm_amount+=$cm_cost;
					$total_margin_amount+=$tot_margin;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];

					if($trim_amount<=0) $color_trim="red"; else $color_trim="";
					if($cm_cost<=0) $color="red"; else $color="";
					$smv=0; $eff=0; $cpm=0; $cost_dzn_cm_title=0;
					$smv=$smv_eff_arr[$row[csf('job_no')]]['smv'];
					$eff=$smv_eff_arr[$row[csf('job_no')]]['eff'];
					$cpm=$smv_eff_arr[$row[csf('job_no')]]['cpm'];
					$cost_dzn_cm_title='Swe. SMV: '.$smv.'; Swe. EFF: '.$eff.'; CPM: '.$cpm;
						//echo $fabric_cost_pcs_arr[$row[csf('po_id')]].'='.$plan_cut_qnty;
					?>


                    <td width="60" align="right"><p><? echo number_format($yarn_req_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($yarn_costing,2); ?></p></td>
                    <td width="60" align="right" title="YarnCost/YarnQty*100"><p><? echo number_format($yarn_costing/$order_value*100,2); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($fab_purchase_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fab_purchase,2); ?></p></td>
                    <td width="60" align="right" title="FabCost/POValue*100"><p><? echo number_format($fab_purchase/$order_value*100,2); ?></p></td>
                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><? echo number_format($trim_amount,2);?></td>
					<td width="60" align="right" title="TrimCost/POValue*100"><p><? echo number_format($trim_amount/$order_value*100,2); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($yarn_dye_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($yarn_dye_cost,2); ?></p></td>
                    <td width="60" align="right" title="YCost/POValue*100"><p><? echo number_format($yarn_dye_cost/$order_value*100,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($all_over_print_cost,2); ?></p></td>
                    <td width="60" align="right" title="AopCost/PoValue*100"><p><? echo number_format($all_over_print_cost/$order_value*100,2); ?></p></td>
                    
                     <td width="60" align="right" title="Yarn+Purchase+Trims+YarnDye+AOP Cost"><p><?
					 $tot_b2b_cost=$yarn_costing+$fab_purchase+$trim_amount+$yarn_dye_cost+$all_over_print_cost;
					
					  echo number_format($tot_b2b_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot B2bCost/PoValue*100"><p><? echo number_format($tot_b2b_cost/$order_value*100,2); ?></p></td>
                    
                    
                    <td width="60" align="right"><p><? echo number_format($knite_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($knit_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot KnitCost/PoValue*100"><p><? echo number_format($knit_cost/$order_value*100,2); ?></p></td>
                    
                    
                    <td width="60" align="right"><p><? echo number_format($fabric_dyeing_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fabric_dyeing_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot DyeCost/PoValue*100"><p><? echo number_format($fabric_dyeing_cost/$order_value*100,2); ?></p></td>
                     <td width="80" align="right"><? echo number_format($print_amount,2); ?></td>
                  	<td width="60" align="right" title="Tot PrintCost/PoValue*100"><p><? echo number_format($print_amount/$order_value*100,2); ?></p></td>
					 <td width="85" align="right"><? echo number_format($embroidery_amount,2); ?></td>
                    <td width="60" align="right" title="Tot EmbroCost/PoValue*100"><p><? echo number_format($embroidery_amount/$order_value*100,2); ?></p></td>
                     <td width="85" align="right"><? echo number_format($gmtdying_amount,2); ?></td>
                     <td width="60" align="right" title="Tot GmtDyingCost/PoValue*100"><p><? echo number_format($gmtdying_amount/$order_value*100,2); ?></p></td>
					 <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                     <td width="60" align="right" title="Tot SpecialCost/PoValue*100"><p><? echo number_format($special_amount/$order_value*100,2); ?></p></td>
					 <td width="80" align="right"><? echo number_format($wash_cost,2); ?></td>
                     <td width="60" align="right" title="Tot WashCost/PoValue*100"><p><? echo number_format($wash_cost/$order_value*100,2); ?></p></td>
					 <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
                     <td width="100" align="right" title="Knit+Fin Dying+Print_Embroidery+GmtDying+Special+Wash+OtherCost"><p><? 
					 $tot_service_cost=$knit_cost+$fabric_dyeing_cost+$print_amount+$embroidery_amount+$gmtdying_amount+$special_amount+$wash_cost+$other_amount;
					 $total_service_cost+=$tot_service_cost; echo  number_format($tot_service_cost,2);
					 
					  $tot_act_material_cost=$yarn_costing+$fab_purchase+$trim_amount;
					   $total_act_material_cost+=$tot_act_material_cost;
					   $tot_act_service_cost=$yarn_dye_cost+$all_over_print_cost+$knit_cost+$fabric_dyeing_cost+$print_amount+$embroidery_amount+$special_amount+$gmtdying_amount+$other_amount+$wash_cost;
					    $total_act_service_cost+=$tot_act_service_cost;
						
						 $tot_material_service_cost=$tot_b2b_cost+$tot_service_cost;
						 $total_material_service_cost+=$tot_material_service_cost;
					  ?></p></td>
                    <td width="60" align="right" title="Tot ServiceCost/PoValue*100"><p><? echo number_format($tot_service_cost/$order_value*100,2); ?></p></td>
                    <td width="60" align="right" title="Yarn+Purchase+Trims Cost"><p><?  echo number_format($tot_act_material_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot Act. MaterialCost/PoValue*100"><p><? echo number_format($tot_act_material_cost/$order_value*100,2); ?></p></td>
                    
                    <td width="100" align="right" title="YarnDye+Aop+Kint+FabricDye+Print+Embro+Wash Cost"><p><? echo number_format($tot_act_service_cost,2); ?></p></td>
                 	<td width="60" align="right" title="Tot Act.ServiceCost/PoValue*100"><p><? echo number_format($tot_act_service_cost/$order_value*100,2); ?></p></td>
                    <td width="100" align="right" title="Tot B2B+Service Cost"><p><?  echo number_format($tot_material_service_cost,2); ?></p></td>
                   
                     <td width="60" align="right" title="Tot Material+ServiceCost/PoValue*100"><p><? echo number_format($tot_material_service_cost/$order_value*100,2); ?></p></td>
					 <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
					 <td width="60" align="right" title="Tot commercial costt/PoValue*100"><p><? echo number_format($commercial_cost/$order_value*100,2); ?></p></td>
                     <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
					 <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($inspection,2);?></td>
					 <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
                    
					 <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>

                     <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
					 <td width="120" align="right"><? echo number_format($local,2) ?></td>
                     <td width="110" align="right"><? $tot_forg_local=$foreign+$local; echo number_format($tot_forg_local,2);  ?></td>
                   
					 <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     
					<td width="60" align="right" title="Tot CM cost/PoValue*100"><p><? echo number_format($cm_cost/$order_value*100,2); ?></p></td>
					 <td width="100" align="right" title="Tot Mat+Service+Commercial+Test+Freight+Inspection+Certificate+HO Exp.+Currier+Foreign+Local+CM Cost"><p><?
					 $tot_cost=$tot_material_service_cost+$commercial_cost+$test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$tot_forg_local+$cm_cost;
					 $total_cost+=$tot_cost;
					  echo number_format($tot_cost,2); ?></p></td>
					  <td width="60" align="right" title="Tot Cost/PoValue*100"><p><? echo number_format($tot_cost/$order_value*100,2); ?></p></td>
                     <td width="110" align="right" title="PO Value-Total Cost"><? $tot_margin=$order_value-$tot_cost; echo  number_format($tot_margin,2); ?></td>
                     <td width="60" align="right"><div style="word-wrap:break-word; width:60px"><? echo number_format(($tot_margin/$order_value*100),2); ?></div></td>
                      <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo $lib_user_arr[$row[csf('approved_by')]]; ?></div></td>
                      <td width="110" align="center"><div style="word-wrap:break-word;"><? echo $row[csf('approved_date')]; ?></div></td>
                      <td><? echo $approval_type_arr[$row[csf('approved')]];?></td>
					<?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";
						else $color_pl="";
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
						$expect_variance=$total_profit-$expected_profit_per;

					?>
				  </tr>
				<?
				$total_order_qty_pcs+=$order_qty_pcs;
				$total_plan_cut_qnty_pcs+=$plan_cut_qnty;
				$total_order_qty+=$row[csf('po_quantity')];
				$total_order_amount+=$order_value;
				$total_yarn_required+=$yarn_req;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$total_all_over_print_cost+=$all_over_print_cost;
				$total_trim_cost+=$trim_amount;$total_foreign_cost+=$foreign;$total_local_cost+=$local;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;
				
				 $total_b2b_cost+=$tot_b2b_cost;
				 

				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_cm_value+=$cm_value;
				$total_cm_new_value+=$tot_cmValue;
				$total_forg_local_amount_new+=$tot_forg_local;
				$total_FOB_val_amount+=$tot_netFOB;

				$total_margin_new_amount+=$tot_margin;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_finance_chrg_cost+=$finance_chrg;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;

				$total_yarn_qty+=$yarn_req_qty;
				$total_knite_qty+=$knite_qty;
				$total_fabric_dyeing_qty+=$fabric_dyeing_qty;
				$total_washing_qty+=$washing_qty;

				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			?>
			</table>
			</div>
			<table class="tbl_bottom" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="60">&nbsp;</td>
                    <td width="110">Total:</td>
					<td width="110">&nbsp;</td>
                    <td width="90">&nbsp;</td>
					<td width="60">&nbsp;</td>

                   
					<td width="90">&nbsp;</td>
                    <td width="90" align="right" id="total_order_qnty_pcs"><? echo number_format($total_order_qty_pcs,2); ?></td>
                    <td width="70">&nbsp;</td>
					<td width="90"><? echo number_format($total_plan_cut_qnty_pcs,2); ?></td>
					

                    <td width="70" align="right" id=""><? //echo number_format($total_order_amount,2); ?></td>

                    <td width="100" id="total_order_amount"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="60"><? echo number_format($total_yarn_qty); ?></td>
                    <td width="60"><? echo number_format($total_yarn_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                     <td width="60"><? //echo number_format($total_yarn_qty); ?></td>
                    <td width="60"><? //echo number_format($total_yarn_cost,2); ?></td>
                    
                    
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_all_over_print_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60" id="total_b2b_cost"><? echo number_format($total_b2b_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                          
                    <td width="60"><? echo number_format($total_knite_qty,2);//number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_knitting_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_fabric_dyeing_cost,2);//number_format($total_washing_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_fabric_dyeing_qty,2);//number_format($total_washing_qty,2); ?></td>
                    <td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_embroidery_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_wash_cost,2); ?></td>
                    <td width="85" align="right" id="total_gmtdying_amount"><? echo number_format($total_gmtdying_amount,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
                    <td width="60" align="right"><? //echo number_format($tot_fab_cost,2);total_special_amount ?></td>

					<td width="80" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
				
					<td width="60" align="right" id=""><? //echo number_format($total_commercial_cost,2); ?></td>
					<td width="80" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
                    <td width="100" id="total_service_cost"><? echo number_format($total_service_cost,2); ?></td>
                    <td width="60" align="right" id=""><? //echo number_format($total_test_cost_amount,2); ?></td>
					<td width="60" align="right" id="total_act_material_cost"><? echo number_format($total_act_material_cost,2); ?></td>
                    <td width="60" align="right"><? //echo number_format($tot_interest_cost,2);?></td>
					<td width="100" align="right" id="total_act_service_cost"><? echo number_format($total_act_service_cost,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_material_service_cost,2); ?></td>
                    <td width="100" align="right" id="total_material_service_cost"><? echo number_format($total_material_service_cost,2); ?></td>

					<td width="60" align="right" id=""><? //echo number_format($total_currier_amount,2); ?></td>
					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2);?></td>
					<td width="60" align="right"><? //echo number_format($tot_interest_cost,2);?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2);?></td>

                    <td width="100" align="right" id="total_freight_cost"><? echo number_format($total_freight_cost,2); ?></td>


                    <td width="120" align="right" id="total_inspection"><? echo number_format($total_inspection,2); ?></td>
					<td width="100" align="right" id="total_certificate_cost"><? echo number_format($total_certificate_cost,2); ?></td>
                    <td width="100" align="right" id="total_common_oh"><? echo number_format($total_common_oh_amount,2); ?></td>
                    <td width="100" align="right" id="total_currier_cost"><? echo number_format($total_currier_cost,2); ?></td>
                   

                    <td width="120" align="right" id="total_foreign_cost"><? echo number_format($total_foreign_cost,2); ?></td>
					<td width="120" id="total_local_cost"><? echo number_format($total_local_cost,2); //total_FOB_val_amount?></td>
					<td width="110"><? echo number_format($total_forg_local_amount_new,2); //total_FOB_val_amount?></td>
					
                    <td width="100"><? echo number_format($total_cm_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2); ?></td>
                    <td width="100"><? echo number_format($total_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2);?></td>
                    <td width="110"><? echo number_format($total_margin_new_amount,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2); ?></td>
                  
                    <td width="110"><? //echo number_format($total_cm_cost,2); ?></td>
                    <td width="110"><? //echo number_format($total_cm_cost,2); ?></td>
                   
                          
                    <td>&nbsp;</td>

				</tr>
			</table>
			<table>
				<tr>
					<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_cost_up,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');
					?>
					<input type="hidden" id="graph_data" value=""/>
				</tr>
			</table> 
			<br>
            <a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>
            <div style="width:600px; display:none" id="yarn_summary" >
            <div id="data_panel2" align="center" style="width:500px">
					 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
				 </div>
            <table class="rpt_table" width="590" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                	<tr>
                                    	<th colspan="7">Yarn Cost Summary</th>
                                    </tr>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="140">Composition</th>
                                        <th width="60">Yarn Count</th>
                                        <th width="80">Type</th>
                                        <th width="100">Req. Qty</th>
                                        <th width="70">Avg. Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <?
								$yarn_des_data_summ_arr=array();
								foreach($yarn_des_data as $count=>$count_value)
                                {
									foreach($count_value as $Composition=>$composition_value)
									{
											//ksort($composition_value);
											foreach($composition_value as $percent=>$percent_value)
											{
													foreach($percent_value as $type=>$type_value)
													{
														$compo_val=$composition[$Composition];
														$count_name=$yarn_count_library[$count];
														$yarn_type_name=$yarn_type[$type];
														
													$yarn_des_data_summ_arr[$compo_val][$count_name][$yarn_type_name][$percent]['qty']=$type_value['qty'];	
													$yarn_des_data_summ_arr[$compo_val][$count_name][$yarn_type_name][$percent]['amount']=$type_value['amount'];
													}
											}
									}
								}
								
                                $s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
								ksort($yarn_des_data_summ_arr);
                                foreach($yarn_des_data_summ_arr as $Composition_key=>$composition_value)
                                {
										ksort($composition_value);
								foreach($composition_value as $count_key=>$count_value)
                                {
								ksort($count_value);
								foreach($count_value as $type=>$type_value)
                                {
								foreach($type_value as $percent=>$value)
                                {
                                    if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    //$yarn_desc=explode("**",$key);

                                    $tot_yarn_req_qnty+=$value['qty'];
                                    $tot_yarn_req_amnt+=$value['amount'];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
                                        <td><? echo $s; ?></td>
                                        <td title="<? echo $Composition_key;?>"><div style="word-wrap:break-word; width:140px"><? echo $Composition_key." ".$percent."%"; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:60px"><? echo $count_key; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:80px"><? echo $type; ?></div></td>
                                        <td align="right"><? echo number_format($value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($value['amount']/$value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($value['amount'],2); ?></td>
                                    </tr>
                                    <?
                                    $s++;
								}
								}
								}
                                }
                                ?>
                                <tfoot>
                                    <th colspan="4" align="right">Total</th>
                                    <th align="right"><? echo number_format($tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt,2); ?></th>
                                </tfoot>
                            </table>
                     </div>
			</fieldset>
			</div>
			<?
		}
	}
	else if($report_type==3 && $cbo_search_type==2) //Report button //PO  Wise
	{
		 
		if($template==1)
		{

			$app_status_arr=array(
				222=>'Not Submit',
				202=>'Not Submit',
				212=>'Ready For Approval',
				211=>'Partial Approved',
				111=>'Full Approved'
			);
			$style1="#E9F3FF";$style="#FFFFFF";
			$cm_cost_formula_sql=sql_select( "select id, cm_cost_method from variable_order_tracking where  variable_list=22 $company_con_name order by id");
			$cm_cost_formula=$cm_cost_predefined_method[$cm_cost_formula_sql[0][csf('cm_cost_method')]];
			$asking_profit_arr=array();
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $company_con_id");//$date_max_profit
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
				}
			}
			$asking_profit=array();
			if($db_type==0)
			{
				
				$po_grp="group_concat(b.id) as po_id";
			}
			else
			{
				$po_grp="listagg(CAST(b.id as VARCHAR(4000)),',') within group (order by b.id) as po_id";
			}

		  $sql_budget="select a.id,a.company_name,a.job_no_prefix_num,a.team_leader,a.set_smv, max(b.insert_date) as insert_date, c.insert_date as pre_insert_date, c.costing_date, max(b.update_date) as update_date, a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, sum(b.plan_cut) as plan_cut,sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved,b.id as po_id,b.po_number from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.entry_from=158 $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $approvalCon $file_con_b $ref_con_b group by  a.id,a.company_name,a.job_no_prefix_num, c.insert_date , c.costing_date,a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.team_leader,a.dealing_marchant,a.set_smv, a.gmts_item_id, a.total_set_qnty,b.id,b.po_number,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved";
			 //echo $sql_budget; die;
		

			$result_sql_budget=sql_select($sql_budget);
			$tot_rows_budget=count($result_sql_budget);
			$budget_data_arr=array();
			foreach($result_sql_budget as $row )
			{
				$job_id.=$row[csf('id')].',';
			}
			$jobIds=chop($job_id,',');  
			$job_ids=count(array_unique(explode(",",$job_id)));
			if($db_type==2 && $job_ids>1000)
			{
				$job_cond_for_in=" and (";
				$jobIdsArr=array_chunk(explode(",",$jobIds),999);
				foreach($jobIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$job_cond_for_in.=" b.id in($ids) or"; 
				}
				$job_cond_for_in=chop($job_cond_for_in,'or ');
				$job_cond_for_in.=")";
			}
			else
			{
				$job_cond_for_in=" and b.id in($jobIds)";
			}
			$cpm_arr=array();$cpm_date_arr=array(); //$yarn_desc_array=array();
			$finance_arr=array();
			$sql_cpm=sql_select("select applying_period_date, applying_period_to_date, cost_per_minute,interest_expense from lib_standard_cm_entry where 1=1 $company_con_id");
			foreach($sql_cpm as $cpMrow )
			{
				$applying_period_date=change_date_format($cpMrow[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($cpMrow[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate = date("d-m-Y",strtotime(add_date(str_replace("'","",$applying_period_date),$j)));
					$cpm_arr[$newdate]['cpm']=$cpMrow[csf('cost_per_minute')];
					$finance_arr[$newdate]['finPercent']=$cpMrow[csf('interest_expense')];
				}
			}
			$smv_eff_arr=array(); $costing_library=array();
			$sql_smv_cpm=sql_select("select a.job_no, a.costing_date, a.sew_smv, a.sew_effi_percent,a.exchange_rate,b.company_name from wo_pre_cost_mst a,wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond_for_in");
			foreach($sql_smv_cpm as $smv_cpm )
			{
				$dateKey=date("d-m-Y",strtotime($smv_cpm[csf('costing_date')]));
				
				$costing_library[$smv_cpm[csf('job_no')]]=$smv_cpm[csf('costing_date')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['smv']=$smv_cpm[csf('sew_smv')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['eff']=$smv_cpm[csf('sew_effi_percent')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['cpm']=$cpm_arr[$dateKey]['cpm'];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['exc_rate']=$smv_cpm[csf('exchange_rate')]; 
			}
			unset($sql_smv_cpm);
			
			//var_dump($smv_eff_arr);die;
 			$condition= new condition();
			 if(str_replace("'","",$cbo_company_name)>0){
				 $condition->company_name("=$cbo_company_name");
			 }
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num("=$txt_job_no");
			 }
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
			 }
			  if(str_replace("'","",$cbo_search_date) ==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				$condition->insert_date("$insert_date_cond");
			 }


			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no");
			 }

			$condition->init();

		    $costing_per_arr=$condition->getCostingPerArr();
			$fabric= new fabric($condition);
			//$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_costing_arr=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			$fabric_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_purchase_qty_arr=$fabric->getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			//print_r($fabric_purchase_qty_arr);
			$yarn= new yarn($condition);
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();

			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();
			$yarn= new yarn($condition);
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			$conversion_qty_arr_process=$conversion->getQtyArray_by_orderAndProcess();
			$conversion_costing_po_arr=$conversion->getAmountArray_by_order();

			$conversion_qty= new conversion($condition);
			$conversion_qty_arr_process = $conversion_qty->getQtyArray_by_orderAndProcess();
			$conversion_job_amount_arr_process = $conversion_qty->getAmountArray_by_jobAndProcess();
			$conversion_job_qty_arr_process = $conversion_qty->getQtyArray_by_jobAndProcess();
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149,158);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
			$washing_qty_arr=array(140,142,148,64);	
			
			
			
			foreach($result_sql_budget as $row )
			{
				$total_order_amount+=$row[csf('po_total_price')];
				$poIdArr=array_unique(explode(",",$row[csf('po_id')]));
				foreach($poIdArr as $poId)
				{

					$total_yarn_cost+=$yarn_costing_arr[$poId];
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$poId][2]);
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$poId][2]);
					$total_purchase_cost+=($fab_purchase_knit+$fab_purchase_woven);
					$total_trim_cost+=$trims_costing_arr[$poId];
					$total_yarn_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][30]);
					$all_over_cost=0;
					foreach($aop_cost_arr as $aop_process_id)
					{
						$all_over_cost+=array_sum($conversion_costing_arr_process[$poId][$aop_process_id]);
					}
					$all_over_print_cost+=$all_over_cost;				
					
					$knit_cost=0;
					foreach($knit_cost_arr as $process_id)
					{
						$knit_cost+=array_sum($conversion_costing_arr_process[$poId][$process_id]);
					}
					$total_knit_cost+=$knit_cost;
					
					$fabric_dyeing_cost=0;
					foreach($fabric_dyeingCost_arr as $fab_process_id)
					{
						$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][$fab_process_id]);
					}
					$total_fabric_dyeing_cost+=$fabric_dyeing_cost;				
								
					$fabric_finish=0;
					foreach($fab_finish_cost_arr as $fin_process_id)
					{
						$fabric_finish+=array_sum($conversion_costing_arr_process[$poId][$fin_process_id]);
					}
					$total_finishing_cost+=$fabric_finish;
					
					$total_heat_setting_cost+=array_sum($conversion_costing_arr_process[$poId][33]);
					
					$washing_cost=0;
					foreach($washing_cost_arr as $w_process_id)
					{
						$washing_cost+=array_sum($conversion_costing_arr_process[$poId][$w_process_id]);
					}
					$total_washing_cost+=$washing_cost;
					
					$total_print_amount+=$emblishment_costing_arr_name[$poId][1];
					$total_embroidery_amount+=$emblishment_costing_arr_name[$poId][2];
					$total_special_amount+=$emblishment_costing_arr_name[$poId][4];
					$total_other_amount+=$emblishment_costing_arr_name[$poId][5];
					$total_wash_cost+=$emblishment_costing_arr_name_wash[$poId][3];
					
					$total_commercial_cost+=$commercial_costing_arr[$poId];
					
					$total_foreign_amount+=$commission_costing_arr[$poId][1];
					$total_local_amount+=$commission_costing_arr[$poId][2];
					
					
					$total_testing_cost+=$other_costing_arr[$poId]['lab_test'];
					$total_freight_cost+=$other_costing_arr[$poId]['freight'];
					
					
					$total_inspection_cost+=$other_costing_arr[$poId]['inspection'];
					$total_certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
					$total_common_oh+=$other_costing_arr[$poId]['common_oh'];
					$total_currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
					$total_cm_cost+=$other_costing_arr[$poId]['cm_cost'];				
					
					
					$total_deffdlc_cost+=$other_costing_arr[$poId]['deffdlc_cost'];
					$total_design_cost+=$other_costing_arr[$poId]['design_cost'];
					$total_studio_cost+=$other_costing_arr[$poId]['studio_cost'];

				}
			}
			
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			$total_purchase_cost_percentage=$total_purchase_cost/$total_order_amount*100;
			$total_trim_cost_percentage=$total_trim_cost/$total_order_amount*100;
			$total_yarn_dyeing_percentage=$total_yarn_dyeing_cost/$total_order_amount*100;
			$all_over_print_cost_percentage=$all_over_print_cost/$total_order_amount*100;
			$total_knit_cost_percentage=$total_knit_cost/$total_order_amount*100;
			
			$total_dyefin_cost=$total_fabric_dyeing_cost+$total_finishing_cost+$total_heat_setting_cost+$total_washing_cost;
			$total_dyefin_cost_percentage=$total_dyefin_cost/$total_order_amount*100;
			
			
			$total_embelishment_cost=$total_print_amount+$total_embroidery_amount+$total_special_amount+$total_other_amount+$total_wash_cost;
			$total_embelishment_cost_percentage=$total_embelishment_cost/$total_order_amount*100;

			$total_commercial_cost_percentage=$total_commercial_cost/$total_order_amount*100;

			$total_commission_cost=$total_foreign_amount+$total_local_amount;
			$total_commission_cost_percentage=$total_commission_cost/$total_order_amount*100;
			
			
			$total_testing_cost_percentage=$total_testing_cost/$total_order_amount*100;
			$total_freight_cost_percentage=$total_freight_cost/$total_order_amount*100;
			$total_inspection_cost_percentage=$total_inspection_cost/$total_order_amount*100;
			$total_certificate_cost_percentage=$total_certificate_cost/$total_order_amount*100;
			$total_common_oh_percentage=$total_common_oh/$total_order_amount*100;
			$total_currier_cost_percentage=$total_currier_cost/$total_order_amount*100;
			$total_cm_cost_percentage=$total_cm_cost/$total_order_amount*100;
			
			
			$total_tot_cost=$total_yarn_cost+$total_purchase_cost+$total_knit_cost+$total_washing_cost+$all_over_print_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_trim_cost+$total_testing_cost+$total_print_amount+$total_embroidery_amount+$total_special_amount+$total_other_amount+$total_wash_cost+$total_commercial_cost+$total_foreign_amount+$total_local_amount+$total_freight_cost+$total_inspection_cost+$total_certificate_cost+$total_common_oh+$total_currier_cost+$total_cm_cost;			
			$total_tot_cost_percentage=$total_tot_cost/$total_order_amount*100;
			
			$total_material_cost=$total_yarn_cost+$total_purchase_cost+$total_trim_cost+$total_yarn_dyeing_cost+$all_over_print_cost;
			$total_material_cost_percentage=$total_material_cost/$total_order_amount*100;
			
			$total_service_cost=$total_knit_cost+$total_dyefin_cost+$total_embelishment_cost;
			$total_service_cost_percentage=$total_service_cost/$total_order_amount*100;			
			
			
			?>
			
	<script>
			function toggle()
			{
				var ele = document.getElementById("yarn_summary");
				//alert(ele);
				var text = document.getElementById("displayText");
				if(ele.style.display!= "none")
				{
					ele.style.display = "none";
					text.innerHTML = "Show Yarn Summary";
				}
				else
				{
					ele.style.display = "block";
					text.innerHTML = "Hide Yarn Summary";
				}
			}

	  </script>
			
			<? 
				$width=6260;
				ob_start(); 
			?>
			
		<div style="width:<? echo $width;?>px;">
			<div style="width:900px;" align="left">
				<table width="900" cellpadding="0" cellspacing="2" border="0">
					<tr>
						<td width="350" align="left">
							<table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
								<thead align="center">
									<tr>
										<th colspan="4">Order Wise Budget Cost Summary</th>
									</tr>
									<tr>
										<th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
									</tr>
								</thead>
								<tr bgcolor="<? echo $style1; ?>">
									<td width="20">1</td>
									<td width="130">Yarn Cost</td>
									<td width="120" align="right"><? echo number_format($total_yarn_cost,2);?></td>
									<td align="right"><? echo number_format($total_yarn_cost_percentage,2);?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>2</td>
									<td>Fabric Purchase</td>
									<td align="right"><? echo number_format($total_purchase_cost,2);?></td>
									<td align="right" ><? echo number_format($total_purchase_cost_percentage,2);?></td>
								</tr>
								<tr bgcolor="<?  echo $style1; ?>">
									<td>3</td>
									<td>Trims Cost</td>
									<td align="right"><? echo number_format($total_trim_cost,2);?></td>
									<td align="right"><? echo number_format($total_trim_cost_percentage,2);?></td>
								</tr>
                                <tr bgcolor="#CCCCCC">
									<td></td>
									<td><strong>Actual Material Cost</strong></td>
									<td align="right" title="Yarn Cost+Trim Cost+Finished Fabric Purchase Cost"><? 
									$total_act_material_cost=$total_yarn_cost+$total_purchase_cost+$total_trim_cost;
									echo number_format($total_act_material_cost,2); ?></td>
									<td align="right"><? echo number_format($total_act_material_cost/$total_order_amount*100,2); ?></td>
								</tr>
                                
								<tr bgcolor="<? echo $style; ?>">
									<td>4</td>
									<td>Yarn Dyeing Cost</td>
									<td align="right"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
									<td align="right"><? echo number_format($total_yarn_dyeing_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>5</td>
									<td>AOP Cost</td>
									<td align="right"><? echo number_format($all_over_print_cost,2); ?></td>
									<td align="right"><? echo number_format($all_over_print_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total B2B Cost</strong></td>
									<td align="right"><? echo number_format($total_material_cost,2); ?></td>
									<td align="right"><? echo number_format($total_material_cost_percentage,2); ?></td>
								</tr>

                                <tr bgcolor="<? echo $style1; ?>">
									<td>6</td>
									<td>Knitting Cost</td>
									<td align="right"><? echo number_format($total_knit_cost,2); ?></td>
									<td align="right"><? echo number_format($total_knit_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>7</td>
									<td>Dyeing & Finishing Cost</td>
									<td align="right"><? echo number_format($total_dyefin_cost,2); ?></td>
									<td align="right"><? echo number_format($total_dyefin_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>8</td>
									<td>Print/ Emb. /Wash Cost</td>
									<td align="right"><? echo number_format($total_embelishment_cost,2); ?></td>
									<td align="right"><? echo number_format($total_embelishment_cost_percentage,2); ?></td>
								</tr>
                                <tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Actual Service Cost:</strong></td>
									<td align="right" title="Y/D Cost+AOP Cost+Knit Cost+Dye&Fin Cost+Printing Cost+Embroidery Cost+WashCost+Other ServiceCost(Embellishment Other Cost+Dyeing"><? 
									$total_act_service_cost=$total_yarn_dyeing_cost+$all_over_print_cost+$total_knit_cost+$total_dyefin_cost+$total_embelishment_cost;
									echo number_format($total_act_service_cost,2); ?></td>
									<td align="right"><? echo number_format($total_service_cost_percentage,2); ?></td>
								</tr>
                                
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Service Cost</strong></td>
									<td align="right"><? echo number_format($total_service_cost,2); ?></td>
									<td align="right"><? echo number_format($total_service_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Material & Service</strong></td>
									<td align="right"><? echo number_format($total_service_cost+$total_material_cost,2); ?></td>
									<td align="right"><? echo number_format(($total_service_cost+$total_material_cost)/$total_order_amount*100,2); ?></td>
								</tr>

                                
								<tr bgcolor="<? echo $style; ?>">
									<td>9</td>
									<td>Commercial Cost</td>
									<td align="right"><? echo number_format($total_commercial_cost,2); ?></td>
									<td align="right"><? echo number_format($total_commercial_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>10</td>
									<td>Commision Cost</td>
									<td align="right"><? echo number_format($total_commission_cost,2); ?></td>
									<td align="right"><? echo number_format($total_commission_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>11</td>
									<td>Testing Cost</td>
									<td align="right"><? echo number_format($total_testing_cost,2); ?></td>
									<td align="right"><? echo number_format($total_testing_cost_percentage,2); ?></td>
								</tr>
                                <tr bgcolor="<? echo $style1; ?>">
									<td>12</td>
									<td>Freight Cost</td>
									<td align="right"><? echo number_format($total_freight_cost,2); ?></td>
									<td align="right"><? echo number_format($total_freight_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>13</td>
									<td>Inspection Cost</td>
									<td align="right"><? echo number_format($total_inspection_cost,2); ?></td>
									<td align="right"><? echo number_format($total_inspection_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>14</td>
									<td>Certificate Cost</td>
									<td align="right"><? echo number_format($total_certificate_cost,2); ?></td>
									<td align="right"><? echo number_format($total_certificate_cost_percentage,2); ?></td>
								</tr>
									<tr bgcolor="<? echo $style; ?>">
									<td>15</td>
									<td>Operating Exp.</td>
									<td align="right"><? echo number_format($total_common_oh,2); ?></td>
									<td align="right"><? echo number_format($total_common_oh_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>16</td>
									<td>Courier Cost</td>
									<td align="right"><? echo number_format($total_currier_cost,2); ?></td>
									<td align="right"><? echo number_format($total_currier_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>17</td>
									<td>CM Cost</td>
									<td align="right"><? echo number_format($total_cm_cost,2); ?></td>
									<td align="right"><? echo number_format($total_cm_cost_percentage,2); ?></td>
								</tr>
								
                                <tr bgcolor="<? echo $style1; ?>">
									<td>18</td>
									<td>Total Cost</td>
									<td align="right"><? echo number_format($total_tot_cost,2); ?></td>
									<td align="right"><? echo number_format($total_tot_cost_percentage,2); ?></td>
								</tr>
                                  <tr bgcolor="<? echo $style; ?>">
									<td>19</td>
									<td>Margin</td>
									<td align="right" title="Total Order Value-Total Cost"><? 
									$tot_margin=$total_order_amount-$total_tot_cost;
									$total_tot_margin_percentage=$tot_margin/$total_order_amount*100;
									echo number_format($tot_margin,2); ?></td>
									<td align="right"><? echo number_format($total_tot_margin_percentage,2); ?></td>
								</tr>
								
                                <tr bgcolor="<? echo $style1; ?>">
									<td>20</td>
									<td>Total Order Value</td>
									<td align="right"><? echo number_format($total_order_amount,2); ?></td>
									<td align="right"><? echo number_format($total_order_amount/$total_order_amount*100,2); ?></td>
								</tr>
							</table>
						</td>
						
					</tr>
				</table>
			</div>
			<br/>			
			
			<?
			
			if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else if(str_replace("'","",$cbo_search_date)==3) $caption="PO Insert Date";
					else $caption="Cancelled Date";
					
					
						
			?>
			<fieldset style="width:100%;" id="content_search_panel2">
            		<div>
                       <span style="font-size:20px; font-weight:bold;"><? echo $company_library[$company_name]; ?></span><br/>
                       <span style="font-size:16px; font-weight:bold;">Cost Breakdown Analysis<br/></span>
                       <strong>From  <? echo $caption.' &nbsp;'.$date_from;  ?>  To  <? echo $date_to; ?></strong>
                    </div>
			   <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit'];?>
			<table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
                        <th width="110">Team Leader</th>
						<th width="100">Company</th>
                        <th width="70">Buyer</th>
                        <th width="110">Style</th>
						<th width="70">Job No</th>
                        <th width="110">Gmts Item Name</th>
                        <th width="110">SMV</th>
                        <th width="60">CPM</th>
                        <th width="110">Efficiency</th>
                        <th width="110">Costing Date</th>
                        <th width="90">Style Qty</th>
                        <th width="60">UOM</th>
                        <th width="90">Total SMV</th>
                        <th width="90">Style Qty (Pcs)</th>
                        <th width="70">Excess Cut %</th>
                        <th width="90">Plan Cut Qty (Pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">Style Value</th>
                        <th width="60">TTL Y/Qty</th>
                        <th width="60">TTL Y/Cost</th>
                        <th width="60">%</th>
                        <th width="60">Fin Fab Purchase Qty</th>
                        <th width="60">Fin Fab Purchase Cost</th>
                        <th width="60">%</th>
                        <th width="100">Trim Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">Y/D Qty</th>
                        <th width="60">Y/D Cost</th>
                        <th width="60">%</th>
                        <th width="60">AOP Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">Total B2B Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">TTL Knit Qty</th>
                        <th width="60">TTL Knit Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">TTL Dye & Fin Qty</th>
                        <th width="60">TTL Dye & Fin Cost</th>
                        <th width="60">%</th>
                        
                        <th width="80">Printing</th>
                        <th width="60">%</th>
						<th width="85">Embroidery</th>
                        <th width="60">%</th>
						<th width="80">Special Works</th>
                        <th width="60">%</th>
						<th width="80">Wash Cost</th>
                        <th width="60">%</th>
						<th width="80">Other</th>
                        
                        <th width="100">Total Service Cost</th>
                        <th width="60">%</th>
                        <th width="60">Actual Material Cost</th>
                        <th width="60">%</th>
                        <th width="100">Actual Service Cost</th>
                        <th width="60">%</th>
                       
						
                        <th width="100">Total Mat+Svc Cost</th>
                        <th width="60">%</th>
                        <th width="120">Commercial Cost</th>
                        <th width="60">%</th>
						<th width="100">Testing Cost</th>
						<th width="100">Freight Cost</th>
						<th width="120">Inspection Cost</th>
						<th width="100">Certificate Cost</th>
                        <th width="100">Operating Exp.</th>
                        
						<th width="100">Courier Cost</th>
                       
                       
                        <th width="120">Foreign Comm</th>
						<th width="120">Local Comm</th>
                        <th width="110">Total Comm</th>
                       
						<th width="100" title="<?php echo $cm_cost_formula; ?>">CM Cost</th>
                        <th width="60">%</th>
                        <th width="100">Total Cost</th>
                        <th width="60">%</th>
                        <th width="110">Total Margin</th>
                     
                        <th width="60">Margin %</th>
                     
                        <th width="110">Approved By</th>
                        <th width="110">Appv. Date & Time</th>
                        <th>Appv. Status</th>
					</tr>

				</thead>
			</table>
			<div style="width:<? echo $width+18;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
				
				$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; 	
				$total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
	$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=$total_service_cost=$total_print_amount=0;
	$total_embroidery_amount=$total_special_amount=$total_other_amount=$total_wash_cost=$total_certificate_cost=$total_currier_cost=0;
	$total_all_over_print_cost=$total_b2b_cost=$total_act_material_cost=$total_act_service_cost=$total_material_service_cost=$total_order_amount=$total_cost=$total_common_oh=0;
					

			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				else if(str_replace("'","",$cbo_search_date)==4)
				{
					$update_date=explode(" ",$row[csf('update_date')]);
					$ship_po_recv_date=change_date_format($update_date[0]);
				}
				$dzn_qnty=$costing_per_arr[$row[csf('job_no')]];
				

				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$order_qty=$row[csf('po_quantity')];
				$order_uom=$row[csf('order_uom')];
				$costing_date=$row[csf('costing_date')];

				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];

				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];

			
				$total_plancut_amount+=$plancut_value;
				$po_ids=array_unique(explode(",",$row[csf('po_id')]));
						$commercial_cost=$yarn_costing=$yarn_req_qty=$fab_purchase_knit=$fab_purchase_woven=$conv_cost_poWise=$yarn_dyeing_cost=$heat_setting_cost=$trim_amount=$print_amount=$embroidery_amount=$special_amount=$wash_cost=$other_amount=$foreign=$local=$test_cost=$freight_cost=$inspection=$certificate_cost=$common_oh=$currier_cost=$cm_cost=$dfc_cost=$interest_cost=$incometax_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$fab_purchase_knit_qty=$fab_purchase_woven_qty=0;
						foreach($po_ids as $pid)
						{
							$commercial_cost+=$commercial_costing_arr[$pid];
							$yarn_costing+=$yarn_costing_arr[$pid];
							$yarn_req_qty+=$yarn_req_qty_arr[$pid];
							$fab_purchase_knit+=array_sum($fabric_costing_arr['knit']['grey'][$pid][2]);
							$fab_purchase_woven+=array_sum($fabric_costing_arr['woven']['grey'][$pid][2]);
							
							$fab_purchase_knit_qty+=array_sum($fabric_purchase_qty_arr['knit']['grey'][$pid][2]);
							$fab_purchase_woven_qty+=array_sum($fabric_purchase_qty_arr['woven']['grey'][$pid][2]);
							
							$conv_cost_poWise+=array_sum($conversion_costing_po_arr[$pid]);
							$yarn_dyeing_cost+=array_sum($conversion_costing_arr_process[$pid][30]);
							$heat_setting_cost+=array_sum($conversion_costing_arr_process[$pid][33]);
							
									
							$trim_amount+= $trims_costing_arr[$pid];
							$print_amount+=$emblishment_costing_arr_name[$pid][1];
							$embroidery_amount+=$emblishment_costing_arr_name[$pid][2];
							$special_amount+=$emblishment_costing_arr_name[$pid][4];
							$wash_cost+=$emblishment_costing_arr_name_wash[$pid][3];
							$other_amount+=$emblishment_costing_arr_name[$pid][5];
							$foreign+=$commission_costing_arr[$pid][1];
							$local+=$commission_costing_arr[$pid][2];
							$test_cost+=$other_costing_arr[$pid]['lab_test'];
							$freight_cost+=$other_costing_arr[$pid]['freight'];
							$inspection+=$other_costing_arr[$pid]['inspection'];
							$certificate_cost+=$other_costing_arr[$pid]['certificate_pre_cost'];
							$common_oh+=$other_costing_arr[$pid]['common_oh'];
							$currier_cost+=$other_costing_arr[$pid]['currier_pre_cost'];
							$cm_cost+=$other_costing_arr[$pid]['cm_cost'];
							$dfc_cost+=$other_costing_arr[$pid]['deffdlc_cost'];//$deffdlc_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;
							$interest_cost+=$interest_cost_arr[$pid]*$order_qty_pcs;
							$incometax_cost+=$incometax_cost_arr[$pid]*$order_qty_pcs;
						}
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					 <td width="40"><? echo $i; ?></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $team_member_arr[$row[csf('team_leader')]]; ?></div></td>

                     <td width="100"><p><? echo $company_library[$row[csf('company_name')]]; ?></p></td>
                     <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('style_ref_no')]; ?></div></td>
					 <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110" align="right"><p><? echo $row[csf('set_smv')]; ?></p></td>
                     <td width="60" align="right">
						<? 
							$cpmVal=$smv_eff_arr[$row[csf('job_no')]]['cpm']/$smv_eff_arr[$row[csf('job_no')]]['exc_rate'];
							echo  number_format($cpmVal*100/$smv_eff_arr[$row[csf('job_no')]]['eff'],3); 
                        ?>
                     </td>
                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $smv_eff_arr[$row[csf('job_no')]]['eff']; ?></div></td>
                    <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo change_date_format($costing_date); ?></div></td>
                  
                    <td width="90" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                    <td width="60" align="center"><p><? echo $unit_of_measurement[$order_uom]; ?></p></td>
                    <td width="90" align="right"><p><? echo number_format($order_qty*$row[csf('set_smv')],2); ?></p></td>

                    <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                    <td width="70" align="right"><div style="word-wrap:break-word; width:70px"><? echo $row[csf('excess_cut')]; ?></div></td>
                    <td width="90" align="right"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('plan_cut')]; ?></div></td>
                    <td width="70" align="right"><p><? echo number_format($order_value/$order_qty,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>

                    <?
						$avg_rate=$yarn_costing/$yarn_req_qty;
						$yarn_cost_percent=($yarn_costing/$order_value)*100;
						$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
						$fab_purchase_qty=$fab_purchase_knit_qty+$fab_purchase_woven_qty;
						//$fab_purchase_knit_qty+=array_sum($fabric_qty_arr['knit']['grey'][$pid]);
							//$fab_purchase_woven_qty
						$tot_fabric_cost=$yarn_costing+$fab_purchase+$conv_cost_poWise;


						$knit_cost=0;$knite_qty=0;
						foreach($knit_cost_arr as $process_id)
						{ //conversion_job_amount_arr_process
							$knit_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$process_id]);
							$knite_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$process_id]);
						}
						$all_over_print_cost=$all_over_print_qty=0;
						foreach($aop_cost_arr as $aop_process_id)
						{
							$all_over_print_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$aop_process_id]);
							$all_over_print_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$aop_process_id]);
						}
							
						$yarn_dye_cost=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][30]);
						$yarn_dye_qty=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][30]);
						
						$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;

						$fabric_dyeing_cost=0;$fabric_dyeing_qty=0;
						foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
							$fabric_dyeing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fab_process_id]);
							$fabric_dyeing_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$fab_process_id]);
						}
						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_finish=0;
						foreach($fab_finish_cost_arr as $fin_process_id)
						{
							$fabric_finish+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fin_process_id]);
						}
						$washing_cost=0;
						foreach($washing_cost_arr as $w_process_id)
						{
							$washing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$w_process_id]);
						}

						$washing_qty=0;
						foreach($washing_qty_arr as $w_process_id)
						{
							$washing_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$w_process_id]);
						}


					//conversion_qty_arr_process
						
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;
						$finance_chrg=$order_value*$smv_eff_arr[$row[csf('job_no')]]['finPercent']/100;
						
						

						$total_material_cost=$total_cost_new;
						$others_cost_value = $total_cost -($cm_cost+$freight_cost+$commercial_cost+($foreign+$local));
						$net_order_val=$order_value-(($foreign+$local)+$commercial_cost+$freight_cost);
						$cm_value=$net_order_val-$others_cost_value;

						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100;
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;

						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
						if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
						if($commercial_cost<=0) $color_com="red"; else $color_com="";

					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;
					$total_test_cost_amount+=$test_cost;

					/*$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_forg_local_amount+=$tot_forg_local;
					$total_FOB_amount+=$tot_netFOB;

					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;
					$total_finance_chrg_amount+=$finance_chrg;*/

					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_meterial_amount+=$total_material_cost;
					$total_cm_new_amount+=$tot_cmValue;
					$total_cm_amount+=$cm_cost;
					$total_margin_amount+=$tot_margin;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];

					if($trim_amount<=0) $color_trim="red"; else $color_trim="";
					if($cm_cost<=0) $color="red"; else $color="";
					$smv=0; $eff=0; $cpm=0; $cost_dzn_cm_title=0;
					$smv=$smv_eff_arr[$row[csf('job_no')]]['smv'];
					$eff=$smv_eff_arr[$row[csf('job_no')]]['eff'];
					$cpm=$smv_eff_arr[$row[csf('job_no')]]['cpm'];
					$cost_dzn_cm_title='Swe. SMV: '.$smv.'; Swe. EFF: '.$eff.'; CPM: '.$cpm;
						//echo $fabric_cost_pcs_arr[$row[csf('po_id')]].'='.$plan_cut_qnty;
					?>


                    <td width="60" align="right"><p><? echo number_format($yarn_req_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($yarn_costing,2); ?></p></td>
                    <td width="60" align="right" title="YarnCost/YarnQty*100"><p><? echo number_format($yarn_costing/$yarn_req_qty*100,2); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($fab_purchase_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fab_purchase,2); ?></p></td>
                    <td width="60" align="right" title="FabCost/FabQty*100"><p><? echo number_format($fab_purchase/$fab_purchase_qty*100,2); ?></p></td>
                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><? echo number_format($trim_amount,2);?></td>
					<td width="60" align="right" title="TrimCost/PoQty*100"><p><? echo number_format($trim_amount/$order_value*100,2); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($yarn_dye_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($yarn_dye_cost,2); ?></p></td>
                    <td width="60" align="right" title="YCost/YQty*100"><p><? echo number_format($yarn_dye_qty/$yarn_dye_cost*100,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($all_over_print_cost,2); ?></p></td>
                    <td width="60" align="right" title="AopCost/PoValue*100"><p><? echo number_format($all_over_print_cost/$order_value*100,2); ?></p></td>
                    
                     <td width="60" align="right" title="Yarn+Purchase+Trims+YarnDye+AOP Cost"><p><?
					 $tot_b2b_cost=$yarn_costing+$fab_purchase+$trim_amount+$yarn_dye_cost+$all_over_print_cost;
					
					  echo number_format($tot_b2b_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot B2bCost/PoValue*100"><p><? echo number_format($tot_b2b_cost/$order_value*100,2); ?></p></td>
                    
                    
                    <td width="60" align="right"><p><? echo number_format($knite_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($knit_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot KnitCost/PoValue*100"><p><? echo number_format($knit_cost/$order_value*100,2); ?></p></td>
                    
                    
                    <td width="60" align="right"><p><? echo number_format($fabric_dyeing_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fabric_dyeing_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot DyeCost/PoValue*100"><p><? echo number_format($fabric_dyeing_cost/$order_value*100,2); ?></p></td>
                     <td width="80" align="right"><? echo number_format($print_amount,2); ?></td>
                     <td width="60" align="right"><p><? //echo $row[csf('xxxxxx')]; ?></p></td>
					 <td width="85" align="right"><? echo number_format($embroidery_amount,2); ?></td>
                     <td width="60" align="right"><p><? //echo $row[csf('xxxxxx')]; ?></p></td>
					 <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                     <td width="60" align="right"><p><? //echo $row[csf('xxxxxx')]; ?></p></td>
					 <td width="80" align="right"><? echo number_format($wash_cost,2); ?></td>
                     <td width="60" align="right"><p><? //echo $row[csf('xxxxxx')]; ?></p></td>
					 <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
                     <td width="100" align="right" title="Knit+Fin Dying+Print_Embroidery+Special+Wash+OtherCost"><p><? 
					 $tot_service_cost=$knit_cost+$fabric_dyeing_cost+$print_amount+$embroidery_amount+$special_amount+$wash_cost+$other_amount;
					 $total_service_cost+=$tot_service_cost; echo  number_format($tot_service_cost,2);
					 
					  $tot_act_material_cost=$yarn_costing+$fab_purchase+$trim_amount;
					   $total_act_material_cost+=$tot_act_material_cost;
					   $tot_act_service_cost=$yarn_dye_cost+$all_over_print_cost+$knit_cost+$fabric_dyeing_cost+$print_amount+$embroidery_amount+$wash_cost;
					    $total_act_service_cost+=$tot_act_service_cost;
						
						 $tot_material_service_cost=$tot_b2b_cost+$tot_service_cost;
						 $total_material_service_cost+=$tot_material_service_cost;
					  ?></p></td>
                    <td width="60" align="right" title="Tot ServiceCost/PoValue*100"><p><? echo number_format($tot_service_cost/$order_value*100,2); ?></p></td>
                    <td width="60" align="right" title="Yarn+Purchase+Trims Cost"><p><?  echo number_format($tot_act_material_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot Act. MaterialCost/PoValue*100"><p><? echo number_format($tot_act_material_cost/$order_value*100,2); ?></p></td>
                    
                    <td width="100" align="right" title="YarnDye+Aop+Kint+FabricDye+Print+Embro+Wash Cost"><p><? echo number_format($tot_act_service_cost,2); ?></p></td>
                 	<td width="60" align="right" title="Tot Act.ServiceCost/PoValue*100"><p><? echo number_format($tot_act_service_cost/$order_value*100,2); ?></p></td>
                    <td width="100" align="right" title="Tot B2B+Service Cost"><p><?  echo number_format($tot_material_service_cost,2); ?></p></td>
                   
                     <td width="60" align="right" title="Tot Material+ServiceCost/PoValue*100"><p><? echo number_format($tot_material_service_cost/$order_value*100,2); ?></p></td>
					 <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
					 <td width="60" align="right" title="Tot commercial costt/PoValue*100"><p><? echo number_format($commercial_cost/$order_value*100,2); ?></p></td>
                     <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
					 <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($inspection,2);?></td>
					 <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
                    
					 <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>

                     <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
					 <td width="120" align="right"><? echo number_format($local,2) ?></td>
                     <td width="110" align="right"><? $tot_forg_local=$foreign+$local; echo number_format($tot_forg_local,2);  ?></td>
                   
					 <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     
					<td width="60" align="right" title="Tot CM cost/PoValue*100"><p><? echo number_format($cm_cost/$order_value*100,2); ?></p></td>
					 <td width="100" align="right" title="Tot Mat+Service+Commercial+Test+Freight+Inspection+Certificate+HO Exp.+Currier+Foreign+Local+CM Cost"><p><?
					 $tot_cost=$tot_material_service_cost+$commercial_cost+$test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$tot_forg_local+$cm_cost;
					 $total_cost+=$tot_cost;
					  echo number_format($tot_cost,2); ?></p></td>
					  <td width="60" align="right" title="Tot Cost/PoValue*100"><p><? echo number_format($tot_cost/$order_value*100,2); ?></p></td>
                     
                     
                      
                     <td width="110" align="right" title="PO Value-Total Cost"><? $tot_margin=$order_value-$tot_cost; echo  number_format($tot_margin,2); ?></td>
                     <td width="60" align="right"><div style="word-wrap:break-word; width:60px"><? echo number_format($tot_margin/$order_value,2); ?></div></td>

                    


                   
                      <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo $lib_user_arr[$row[csf('approved_by')]]; ?></div></td>
                      <td width="110" align="center"><div style="word-wrap:break-word;"><? echo $row[csf('approved_date')]; ?></div></td>
                      <td><? echo $app_status_arr[$row[csf('approved')].$row[csf('ready_to_approved')].$row[csf('partial_approved')]];?></td>
					<?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";
						else $color_pl="";
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
						$expect_variance=$total_profit-$expected_profit_per;

					?>
				  </tr>
				<?
				$total_order_qty_pcs+=$order_qty_pcs;
				$total_plan_cut_qnty_pcs+=$plan_cut_qnty;
				$total_order_qty+=$row[csf('po_quantity')];
				$total_order_amount+=$order_value;
				$total_yarn_required+=$yarn_req;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$total_all_over_print_cost+=$all_over_print_cost;
				$total_trim_cost+=$trim_amount;$total_foreign_cost+=$foreign;$total_local_cost+=$local;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;
				
				 $total_b2b_cost+=$tot_b2b_cost;
				 

				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_cm_value+=$cm_value;
				$total_cm_new_value+=$tot_cmValue;
				$total_forg_local_amount_new+=$tot_forg_local;
				$total_FOB_val_amount+=$tot_netFOB;

				$total_margin_new_amount+=$tot_margin;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_finance_chrg_cost+=$finance_chrg;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;

				$total_yarn_qty+=$yarn_req_qty;
				$total_knite_qty+=$knite_qty;
				$total_fabric_dyeing_qty+=$fabric_dyeing_qty;
				$total_washing_qty+=$washing_qty;

				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			?>
			</table>
			</div>
			<table class="tbl_bottom" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="60">&nbsp;</td>
                    <td width="110">Total:</td>
					<td width="110">&nbsp;</td>
                    <td width="90">&nbsp;</td>
					<td width="60">&nbsp;</td>

                   
					<td width="90">&nbsp;</td>
                    <td width="90" align="right" id="total_order_qnty_pcs"><? echo number_format($total_order_qty_pcs,2); ?></td>
                    <td width="70">&nbsp;</td>
					<td width="90"><? echo number_format($total_plan_cut_qnty_pcs,2); ?></td>
					

                    <td width="70" align="right" id=""><? //echo number_format($total_order_amount,2); ?></td>

                    <td width="100" id="total_order_amount"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="60"><? echo number_format($total_yarn_qty); ?></td>
                    <td width="60"><? echo number_format($total_yarn_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                     <td width="60"><? //echo number_format($total_yarn_qty); ?></td>
                    <td width="60"><? //echo number_format($total_yarn_cost,2); ?></td>
                    
                    
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_all_over_print_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60" id="total_b2b_cost"><? echo number_format($total_b2b_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                          
                    <td width="60"><? echo number_format($total_knite_qty,2);//number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_knitting_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_fabric_dyeing_cost,2);//number_format($total_washing_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_fabric_dyeing_qty,2);//number_format($total_washing_qty,2); ?></td>
                    <td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_embroidery_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
                    <td width="60" align="right"><? //echo number_format($tot_fab_cost,2);total_special_amount ?></td>

					<td width="80" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
				
					<td width="60" align="right" id=""><? //echo number_format($total_commercial_cost,2); ?></td>
					<td width="80" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
                    <td width="100" id="total_service_cost"><? echo number_format($total_service_cost,2); ?></td>
                    <td width="60" align="right" id=""><? //echo number_format($total_test_cost_amount,2); ?></td>
					<td width="60" align="right" id="total_act_material_cost"><? echo number_format($total_act_material_cost,2); ?></td>
                    <td width="60" align="right"><? //echo number_format($tot_interest_cost,2);?></td>
					<td width="100" align="right" id="total_act_service_cost"><? echo number_format($total_act_service_cost,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_material_service_cost,2); ?></td>
                    <td width="100" align="right" id="total_material_service_cost"><? echo number_format($total_material_service_cost,2); ?></td>

					<td width="60" align="right" id=""><? //echo number_format($total_currier_amount,2); ?></td>
					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2);?></td>
					<td width="60" align="right"><? //echo number_format($tot_interest_cost,2);?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2);?></td>

                    <td width="100" align="right" id="total_freight_cost"><? echo number_format($total_freight_cost,2); ?></td>


                    <td width="120" align="right" id="total_inspection"><? echo number_format($total_inspection,2); ?></td>
					<td width="100" align="right" id="total_certificate_cost"><? echo number_format($total_certificate_cost,2); ?></td>
                    <td width="100" align="right" id="total_common_oh"><? echo number_format($total_common_oh_amount,2); ?></td>
                    <td width="100" align="right" id="total_currier_cost"><? echo number_format($total_currier_cost,2); ?></td>
                   

                    <td width="120" align="right" id="total_foreign_cost"><? echo number_format($total_foreign_cost,2); ?></td>
					<td width="120" id="total_local_cost"><? echo number_format($total_local_cost,2); //total_FOB_val_amount?></td>
					<td width="110"><? echo number_format($total_forg_local_amount_new,2); //total_FOB_val_amount?></td>
					
                    <td width="100"><? echo number_format($total_cm_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2); ?></td>
                    <td width="100"><? echo number_format($total_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2);?></td>
                    <td width="110"><? echo number_format($total_margin_new_amount,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2); ?></td>
                  
                    <td width="110"><? //echo number_format($total_cm_cost,2); ?></td>
                    <td width="110"><? //echo number_format($total_cm_cost,2); ?></td>
                   
                          
                    <td>&nbsp;</td>

				</tr>
			</table>
			<table>
				<tr>
					<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_cost_up,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');
					?>
					<input type="hidden" id="graph_data" value=""/>
				</tr>
			</table>
			<br>
			</fieldset>
			</div>
			<?
		}
	}
	


	if($report_type==4 && $cbo_search_type==1) //Report 2 button //Job Wise
	{
		if($template==1)
		{

			$app_status_arr=array(
				222=>'Not Submit',
				202=>'Not Submit',
				212=>'Ready For Approval',
				211=>'Partial Approved',
				111=>'Full Approved'
			);
			$style1="#E9F3FF";$style="#FFFFFF";
			$cm_cost_formula_sql=sql_select( "select id, cm_cost_method from variable_order_tracking where  variable_list=22 $company_con_name order by id");
			$cm_cost_formula=$cm_cost_predefined_method[$cm_cost_formula_sql[0][csf('cm_cost_method')]];
			$asking_profit_arr=array();
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $company_con_id");//$date_max_profit
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
				}
			}
			$asking_profit=array();
			if($db_type==0)
			{
				
				$po_grp="group_concat(b.id) as po_id";
			}
			else
			{
				$po_grp="listagg(CAST(b.id as VARCHAR(4000)),',') within group (order by b.id) as po_id";
			}

		   $sql_budget="select a.id,a.company_name,a.job_no_prefix_num,a.team_leader,a.set_smv, max(b.insert_date) as insert_date, c.insert_date as pre_insert_date, c.costing_date, max(b.update_date) as update_date, a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, sum(b.plan_cut) as plan_cut,sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved,$po_grp from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no  $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $approvalCon $file_con_b $ref_con_b group by  a.id,a.company_name,a.job_no_prefix_num, c.insert_date , c.costing_date,a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.team_leader,a.dealing_marchant,a.set_smv, a.gmts_item_id, a.total_set_qnty,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved order by a.job_no";
			 //echo $sql_budget; die;
		

			$result_sql_budget=sql_select($sql_budget);
			$tot_rows_budget=count($result_sql_budget);
			$budget_data_arr=array();
			foreach($result_sql_budget as $row )
			{
				$job_id.=$row[csf('id')].',';
			}
			$jobIds=chop($job_id,',');  
			$job_ids=count(array_unique(explode(",",$job_id)));
			if($db_type==2 && $job_ids>1000)
			{
				$job_cond_for_in=" and (";
				$jobIdsArr=array_chunk(explode(",",$jobIds),999);
				foreach($jobIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$job_cond_for_in.=" b.id in($ids) or"; 
				}
				$job_cond_for_in=chop($job_cond_for_in,'or ');
				$job_cond_for_in.=")";
			}
			else
			{
				$job_cond_for_in=" and b.id in($jobIds)";
			}
			$cpm_arr=array();$cpm_date_arr=array(); //$yarn_desc_array=array();
			$finance_arr=array();
			$sql_cpm=sql_select("select applying_period_date, applying_period_to_date, cost_per_minute,interest_expense from lib_standard_cm_entry where 1=1 $company_con_id");
			foreach($sql_cpm as $cpMrow )
			{
				$applying_period_date=change_date_format($cpMrow[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($cpMrow[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate = date("d-m-Y",strtotime(add_date(str_replace("'","",$applying_period_date),$j)));
					$cpm_arr[$newdate]['cpm']=$cpMrow[csf('cost_per_minute')];
					$finance_arr[$newdate]['finPercent']=$cpMrow[csf('interest_expense')];
				}
			}
			$smv_eff_arr=array(); $costing_library=array();
			$sql_smv_cpm=sql_select("select a.job_no, a.costing_date, a.sew_smv, a.sew_effi_percent,a.exchange_rate,b.company_name from wo_pre_cost_mst a,wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond_for_in");
			foreach($sql_smv_cpm as $smv_cpm )
			{
				$dateKey=date("d-m-Y",strtotime($smv_cpm[csf('costing_date')]));
				
				$costing_library[$smv_cpm[csf('job_no')]]=$smv_cpm[csf('costing_date')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['smv']=$smv_cpm[csf('sew_smv')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['eff']=$smv_cpm[csf('sew_effi_percent')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['cpm']=$cpm_arr[$dateKey]['cpm'];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['exc_rate']=$smv_cpm[csf('exchange_rate')]; 
			}
			unset($sql_smv_cpm);
			
			//var_dump($smv_eff_arr);die;
 			$condition= new condition();
			 if(str_replace("'","",$cbo_company_name)>0){
				 $condition->company_name("=$cbo_company_name");
			 }
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			$job_no=str_replace("'","",$txt_job_no);
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num("in($job_no)");
			 }
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
			 }
			  if(str_replace("'","",$cbo_search_date) ==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				$condition->insert_date("$insert_date_cond");
			 }
			if(str_replace("'","",$cbo_year) !=0){
				  $condition->job_year("$jobYearCond");
			 }

			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no");
			 }

			$condition->init();

		    $costing_per_arr=$condition->getCostingPerArr();
			$fabric= new fabric($condition);
			//echo $fabric->getQuery();die;
			//$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_costing_arr=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			$fabric_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_purchase_qty_arr=$fabric->getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			$fabric_purchase_cost_arr=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			// echo "<pre>";
			// print_r($fabric_purchase_qty_arr);
			
			$yarn= new yarn($condition);
			//echo $yarn->getQuery();die;
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			//$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			//$yarn= new yarn($condition);
			
			$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();
			//$yarn= new yarn($condition);
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			$conversion_qty_arr_process=$conversion->getQtyArray_by_orderAndProcess();
			$conversion_costing_po_arr=$conversion->getAmountArray_by_order();

			$conversion_qty= new conversion($condition);
			$conversion_qty_arr_process = $conversion_qty->getQtyArray_by_orderAndProcess();
			$conversion_job_amount_arr_process = $conversion_qty->getAmountArray_by_jobAndProcess();
			$conversion_job_qty_arr_process = $conversion_qty->getQtyArray_by_jobAndProcess();
			$trims= new trims($condition);
				
			$trims_costing_arr=$trims->getAmountArray_by_order();
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
			//	echo "=AAAAAAAAAA==";die;

			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149,158);
			$aop_cost_arr=array(35,36,37,40);
			// $fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$fab_finish_cost_arr=array(34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
			$washing_qty_arr=array(140,142,148,64);	
		
			// $not_process_id_print_array = array(1,30,35); 
			$not_process_id_print_array = array(1,30,35,33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144); 
			$total_print_amount=$total_embroidery_amount=$total_special_amount=$total_other_amount=$total_gmtdying_amount=0;$tot_fab_purchase_knit_qty=$tot_fab_purchase_woven_qty=0;
			foreach($result_sql_budget as $row )
			{
				$total_order_amount+=$row[csf('po_total_price')];
				$total_po_quantity+=$row[csf('po_quantity')]*$row[csf('ratio')];
				

				$poIdArr=array_unique(explode(",",$row[csf('po_id')]));
				foreach($poIdArr as $poId)
				{

					$total_yarn_cost+=$yarn_costing_arr[$poId];
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$poId][2]);
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$poId][2]);
					
					//$tot_fab_purchase_knit_qty+=array_sum($fabric_purchase_qty_arr['knit']['grey'][$poId][2]);
					//$tot_fab_purchase_woven_qty+=array_sum($fabric_purchase_qty_arr['woven']['grey'][$poId][2]);
					//echo $fab_purchase_knit.'='.$fab_purchase_woven;
					$total_purchase_cost+=($fab_purchase_knit+$fab_purchase_woven);
					$total_trim_cost+=$trims_costing_arr[$poId];
					$total_yarn_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][30]);
					$all_over_cost=0;
					foreach($aop_cost_arr as $aop_process_id)
					{
						$all_over_cost+=array_sum($conversion_costing_arr_process[$poId][$aop_process_id]);
					}
					$all_over_print_cost+=$all_over_cost;				
					
					$knit_cost=0;
					foreach($knit_cost_arr as $process_id)
					{
						$knit_cost+=array_sum($conversion_costing_arr_process[$poId][$process_id]);
					}
					$total_knit_cost+=$knit_cost;
					
					/*$fabric_dyeing_cost=0;
					foreach($fabric_dyeingCost_arr as $fab_process_id)
					{
						$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][$fab_process_id]);
					}*/
					$fabric_dyeing_cost=0;
					/*foreach($fabric_dyeingCost_arr as $fab_process_id)
					{
						$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][$fab_process_id]);
					}*/
				
					foreach($conversion_cost_head_array as $key=>$val)
						{
							if (!in_array($key, $not_process_id_print_array)) 
							{
								$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][$key]);
								
							}
							
						}
						
					$total_fabric_dyeing_cost+=$fabric_dyeing_cost;				
								
					$fabric_finish=0;
					foreach($fab_finish_cost_arr as $fin_process_id)
					{
						$fabric_finish+=array_sum($conversion_costing_arr_process[$poId][$fin_process_id]);
						
					}
					$total_finishing_cost+=$fabric_finish;
					
					$total_heat_setting_cost+=array_sum($conversion_costing_arr_process[$poId][33]);
					
					$washing_cost=0;
					foreach($washing_cost_arr as $w_process_id)
					{
						$washing_cost+=array_sum($conversion_costing_arr_process[$poId][$w_process_id]);
					}
					$total_washing_cost+=$washing_cost;
					
					$total_print_amount+=$emblishment_costing_arr_name[$poId][1];
					$total_embroidery_amount+=$emblishment_costing_arr_name[$poId][2];
					$total_special_amount+=$emblishment_costing_arr_name[$poId][4];
					$total_other_amount+=$emblishment_costing_arr_name[$poId][99];
					$total_wash_cost+=$emblishment_costing_arr_name_wash[$poId][3];
					$total_gmtdying_amount+=$emblishment_costing_arr_name[$poId][5];
					
					$total_commercial_cost+=$commercial_costing_arr[$poId];
					
					$total_foreign_amount+=$commission_costing_arr[$poId][1];
					$total_local_amount+=$commission_costing_arr[$poId][2];
					
					
					$total_testing_cost+=$other_costing_arr[$poId]['lab_test'];
					$total_freight_cost+=$other_costing_arr[$poId]['freight'];
					
					
					$total_inspection_cost+=$other_costing_arr[$poId]['inspection'];
					$total_certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
					$total_common_oh+=$other_costing_arr[$poId]['common_oh'];
					$total_currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
					$total_cm_cost+=$other_costing_arr[$poId]['cm_cost'];				
					
					
					$total_deffdlc_cost+=$other_costing_arr[$poId]['deffdlc_cost'];
					$total_design_cost+=$other_costing_arr[$poId]['design_cost'];
					$total_studio_cost+=$other_costing_arr[$poId]['studio_cost'];

				}
			}
			
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			$total_purchase_cost_percentage=$total_purchase_cost/$total_order_amount*100;
			$total_trim_cost_percentage=$total_trim_cost/$total_order_amount*100;
			$total_yarn_dyeing_percentage=$total_yarn_dyeing_cost/$total_order_amount*100;
			$all_over_print_cost_percentage=$all_over_print_cost/$total_order_amount*100;
			$total_knit_cost_percentage=$total_knit_cost/$total_order_amount*100;
			
			// $total_dyefin_cost=$total_fabric_dyeing_cost+$total_finishing_cost+$total_heat_setting_cost;//+$total_washing_cost
			$total_dyefin_cost=$total_fabric_dyeing_cost+$total_finishing_cost;//+$total_washing_cost
			$total_dyefin_cost_percentage=$total_dyefin_cost/$total_order_amount*100;
			
			
			$total_embelishment_cost=$total_print_amount+$total_embroidery_amount+$total_special_amount+$total_gmtdying_amount+$total_other_amount+$total_wash_cost;
			$total_embelishment_cost_percentage=$total_embelishment_cost/$total_order_amount*100;

			$total_commercial_cost_percentage=$total_commercial_cost/$total_order_amount*100;

			$total_commission_cost=$total_foreign_amount+$total_local_amount;
			$total_commission_cost_percentage=$total_commission_cost/$total_order_amount*100;
			
			
			$total_testing_cost_percentage=$total_testing_cost/$total_order_amount*100;
			$total_freight_cost_percentage=$total_freight_cost/$total_order_amount*100;
			$total_inspection_cost_percentage=$total_inspection_cost/$total_order_amount*100;
			$total_certificate_cost_percentage=$total_certificate_cost/$total_order_amount*100;
			$total_common_oh_percentage=$total_common_oh/$total_order_amount*100;
			$total_currier_cost_percentage=$total_currier_cost/$total_order_amount*100;
			$total_cm_cost_percentage=$total_cm_cost/$total_order_amount*100;
			
			
			$total_tot_cost=$total_yarn_cost+$total_purchase_cost+$total_knit_cost+$total_washing_cost+$all_over_print_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_trim_cost+$total_testing_cost+$total_print_amount+$total_embroidery_amount+$total_gmtdying_amount+$total_special_amount+$total_other_amount+$total_wash_cost+$total_commercial_cost+$total_foreign_amount+$total_local_amount+$total_freight_cost+$total_inspection_cost+$total_certificate_cost+$total_common_oh+$total_currier_cost+$total_cm_cost;			
			$total_tot_cost_percentage=$total_tot_cost/$total_order_amount*100;
			
			$total_material_cost=$total_yarn_cost+$total_purchase_cost+$total_trim_cost+$total_yarn_dyeing_cost+$all_over_print_cost;
			$total_material_cost_percentage=$total_material_cost/$total_order_amount*100;
			
			$total_service_cost=$total_knit_cost+$total_dyefin_cost+$total_embelishment_cost;
			$total_service_cost_percentage=$total_service_cost/$total_order_amount*100;			
			
			
			?>
			
	 <script>
			function toggle()
			{
				var ele = document.getElementById("yarn_summary");
				//alert(ele);
				var text = document.getElementById("displayText");
				if(ele.style.display!= "none")
				{
					ele.style.display = "none";
					text.innerHTML = "Show Yarn Summary";
				}
				else
				{
					ele.style.display = "block";
					text.innerHTML = "Hide Yarn Summary";
				}
			}

	  </script>
			
			<? 
				$width=6765;
				ob_start(); 
			?>
			
		<div style="width:<? echo $width;?>px;">
			<div style="width:900px;" align="left">
				<table width="900" cellpadding="0" cellspacing="2" border="0">
					<tr>
						<td width="350" align="left">
							<table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
								<thead align="center">
									<tr>
										<th colspan="4">Order Wise Budget Cost Summary</th>
									</tr>
									<tr>
										<th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
									</tr>
								</thead>
								<tr bgcolor="<? echo $style1; ?>">
									<td width="20">1</td>
									<td width="130">Yarn Cost</td>
									<td width="120" align="right"><? echo number_format($total_yarn_cost,4);?></td>
									<td align="right"><? echo number_format($total_yarn_cost_percentage,4);?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>2</td>
									<td>Fabric Purchase</td>
									<td align="right"><? echo number_format($total_purchase_cost,4);?></td>
									<td align="right" ><? echo number_format($total_purchase_cost_percentage,4);?></td>
								</tr>
								<tr bgcolor="<?  echo $style1; ?>">
									<td>3</td>
									<td>Trims Cost</td>
									<td align="right"><? echo number_format($total_trim_cost,4);?></td>
									<td align="right"><? echo number_format($total_trim_cost_percentage,4);?></td>
								</tr>
                                <tr bgcolor="#CCCCCC">
									<td></td>
									<td><strong>Actual Material Cost</strong></td>
									<td align="right" title="Yarn Cost+Trim Cost+Finished Fabric Purchase Cost"><? 
									$total_act_material_cost=$total_yarn_cost+$total_purchase_cost+$total_trim_cost;
									echo number_format($total_act_material_cost,4); ?></td>
									<td align="right"><? echo number_format($total_act_material_cost/$total_order_amount*100,4); ?></td>
								</tr>
                                
								<tr bgcolor="<? echo $style; ?>">
									<td>4</td>
									<td>Yarn Dyeing Cost</td>
									<td align="right"><? echo number_format($total_yarn_dyeing_cost,4); ?></td>
									<td align="right"><? echo number_format($total_yarn_dyeing_percentage,4); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>5</td>
									<td>AOP Cost</td>
									<td align="right"><? echo number_format($all_over_print_cost,4); ?></td>
									<td align="right"><? echo number_format($all_over_print_cost_percentage,4); ?></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total B2B Cost</strong></td>
									<td align="right"><? echo number_format($total_material_cost,4); ?></td>
									<td align="right"><? echo number_format($total_material_cost_percentage,4); ?></td>
								</tr>

                                <tr bgcolor="<? echo $style1; ?>">
									<td>6</td>
									<td>Knitting Cost</td>
									<td align="right"><? echo number_format($total_knit_cost,4); ?></td>
									<td align="right"><? echo number_format($total_knit_cost_percentage,4); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>7</td>
									<td>Dyeing & Finishing Cost</td>
									<td align="right" title="<?=$total_fabric_dyeing_cost.'='.$total_finishing_cost.'='.$total_heat_setting_cost; ?>"><? echo number_format($total_dyefin_cost,4); ?></td>
									<td align="right"><? echo number_format($total_dyefin_cost_percentage,4); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>8</td>
									<td>Print/ Embro./Wash Cost/Special/Others</td>
									<td align="right"><? echo number_format($total_embelishment_cost,4); ?></td>
									<td align="right"><? echo number_format($total_embelishment_cost_percentage,4); ?></td>
								</tr>
                                <tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Actual Service Cost:</strong></td>
									<td align="right" title="Y/D Cost+AOP Cost+Knit Cost+Dye&Fin Cost+Printing Cost+Embroidery Cost+WashCost+Other ServiceCost(Embellishment Other Cost+Dyeing"><? 
									$total_act_service_cost=$total_yarn_dyeing_cost+$all_over_print_cost+$total_knit_cost+$total_dyefin_cost+$total_embelishment_cost;
									echo number_format($total_act_service_cost,4); ?></td>
									<td align="right"><? echo number_format($total_service_cost_percentage,4); ?></td>
								</tr>
                                
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Service Cost</strong></td>
									<td align="right"><? echo number_format($total_service_cost,4); ?></td>
									<td align="right"><? echo number_format($total_service_cost_percentage,4); ?></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Material & Service</strong></td>
									<td align="right"><? echo number_format($total_service_cost+$total_material_cost,4); ?></td>
									<td align="right"><? echo number_format(($total_service_cost+$total_material_cost)/$total_order_amount*100,4); ?></td>
								</tr>

                                
								<tr bgcolor="<? echo $style; ?>">
									<td>9</td>
									<td>Commercial Cost</td>
									<td align="right"><? echo number_format($total_commercial_cost,4); ?></td>
									<td align="right"><? echo number_format($total_commercial_cost_percentage,4); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>10</td>
									<td>Commision Cost</td>
									<td align="right"><? echo number_format($total_commission_cost,4); ?></td>
									<td align="right"><? echo number_format($total_commission_cost_percentage,4); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>11</td>
									<td>Testing Cost</td>
									<td align="right"><? echo number_format($total_testing_cost,4); ?></td>
									<td align="right"><? echo number_format($total_testing_cost_percentage,4); ?></td>
								</tr>
                                <tr bgcolor="<? echo $style1; ?>">
									<td>12</td>
									<td>Freight Cost</td>
									<td align="right"><? echo number_format($total_freight_cost,4); ?></td>
									<td align="right"><? echo number_format($total_freight_cost_percentage,4); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>13</td>
									<td>Inspection Cost</td>
									<td align="right"><? echo number_format($total_inspection_cost,4); ?></td>
									<td align="right"><? echo number_format($total_inspection_cost_percentage,4); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>14</td>
									<td>Certificate Cost</td>
									<td align="right"><? echo number_format($total_certificate_cost,4); ?></td>
									<td align="right"><? echo number_format($total_certificate_cost_percentage,4); ?></td>
								</tr>
									<tr bgcolor="<? echo $style; ?>">
									<td>15</td>
									<td>Operating Exp.</td>
									<td align="right"><? echo number_format($total_common_oh,4); ?></td>
									<td align="right"><? echo number_format($total_common_oh_percentage,4); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>16</td>
									<td>Courier Cost</td>
									<td align="right"><? echo number_format($total_currier_cost,4); ?></td>
									<td align="right"><? echo number_format($total_currier_cost_percentage,4); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>17</td>
									<td>CM Cost</td>
									<td align="right"><? echo number_format($total_cm_cost,4); ?></td>
									<td align="right"><? echo number_format($total_cm_cost_percentage,4); ?></td>
								</tr>
								
                                <tr bgcolor="<? echo $style1; ?>">
									<td>18</td>
									<td>Total Cost</td>
									<td align="right"><? echo number_format($total_tot_cost,4); ?></td>
									<td align="right"><? echo number_format($total_tot_cost_percentage,4); ?></td>
								</tr>
                                  <tr bgcolor="<? echo $style; ?>">
									<td>19</td>
									<td>Margin</td>
									<td align="right" title="Total Order Value-Total Cost"><? 
									$tot_margin=$total_order_amount-$total_tot_cost;
									$total_tot_margin_percentage=$tot_margin/$total_order_amount*100;
									echo number_format($tot_margin,4); ?></td>
									<td align="right"><? echo number_format($total_tot_margin_percentage,4); ?></td>
								</tr>
								
                                <tr bgcolor="<? echo $style1; ?>">
									<td>20</td>
									<td>Total Order Value</td>
									<td align="right"><? echo number_format($total_order_amount,4); ?></td>
									<td align="right"><? echo number_format($total_order_amount/$total_order_amount*100,4); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>21</td>
									<td>Total Order Qty</td>
									<td align="right"><? echo number_format($total_po_quantity,4); ?></td>
									<td align="right"><? //echo number_format($total_order_amount/$total_order_amount*100,4); ?></td>
								</tr>
							</table>
						</td>
						
					</tr>
				</table>
			</div>
			<br/>			
			
			<?
			
			if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else if(str_replace("'","",$cbo_search_date)==3) $caption="PO Insert Date";
					else $caption="Cancelled Date";
					
					
						
			?>
			<fieldset style="width:100%;" id="content_search_panel2">
            		<div>
                       <span style="font-size:20px; font-weight:bold;"><? echo $company_library[$company_name]; ?></span><br/>
                       <span style="font-size:16px; font-weight:bold;">Cost Breakdown Analysis<br/></span>
                       <strong>From  <? echo $caption.' &nbsp;'.$date_from;  ?>  To  <? echo $date_to; ?></strong>
                    </div>
			   <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit'];?>
			<table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
                        <th width="110">Team Leader</th>
						<th width="100">Company</th>
                        <th width="70">Buyer</th>
                        <th width="110">Style</th>
						<th width="70">Job No</th>
                        <th width="110">Gmts Item Name</th>
                        <th width="110">SMV</th>
                        <th width="60">CPM</th>
                        <th width="110">Efficiency</th>
                        <th width="110">Costing Date</th>
                        <th width="90">Style Qty</th>
                        <th width="60">UOM</th>
                        <th width="90">Total SMV</th>
                        <th width="90">Style Qty (Pcs)</th>
                        <th width="70">Excess Cut %</th>
                        <th width="90">Plan Cut Qty (Pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">Style Value</th>
                        <th width="100">TTL Y/Qty</th>
                        <th width="100">TTL Y/Cost</th>
                        <th width="60">%</th>
                        <th width="60">Fin Fab Purchase Qty</th>
                        <th width="60">Fin Fab Purchase Cost</th>
                        <th width="60">%</th>
                        <th width="100">Trim Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">Y/D Qty</th>
                        <th width="60">Y/D Cost</th>
                        <th width="60">%</th>
                        <th width="100">AOP Cost</th>
                        <th width="60">%</th>
                        
                        <th width="100">Total B2B Cost</th>
                        <th width="60">%</th>
                        
                        <th width="100">TTL Knit Qty</th>
                        <th width="100">TTL Knit Cost</th>
                        <th width="60">%</th>
                        
                        <th width="100">TTL Dye & Fin Qty</th>
                        <th width="100">TTL Dye & Fin Cost</th>
                        <th width="60">%</th>
                        
                        <th width="80">Printing</th>
                        <th width="60">%</th>
						<th width="85">Embroidery</th>
                        <th width="60">%</th>
                        <th width="85">Gmt Dying</th>
                        <th width="60">%</th>
						<th width="80">Special Works</th>
                        <th width="60">%</th>
						<th width="80">Wash Cost</th>
                        <th width="60">%</th>
						<th width="80">Other</th>
                        
                        <th width="100">Total Service Cost</th>
                        <th width="60">%</th>
                        <th width="100">Actual Material Cost</th>
                        <th width="60">%</th>
                        <th width="100">Actual Service Cost</th>
                        <th width="60">%</th>
                       
						
                        <th width="100">Total Mat+Svc Cost</th>
                        <th width="60">%</th>
                        <th width="120">Commercial Cost</th>
                        <th width="60">%</th>
						<th width="100">Testing Cost</th>
						<th width="100">Freight Cost</th>
						<th width="120">Inspection Cost</th>
						<th width="100">Certificate Cost</th>
                        <th width="100">Operating Exp.</th>
						<th width="100">Courier Cost</th>
                        <th width="120">Foreign Comm</th>
						<th width="120">Local Comm</th>
                        <th width="110">Total Comm</th>
						<th width="100" title="<?php echo $cm_cost_formula; ?>">CM Cost</th>
                        <th width="60">%</th>
                        <th width="100">Total Cost</th>
                        <th width="60">%</th>
                        <th width="110">Total Margin</th>
                        <th width="60">Margin %</th>
                        <th width="110">Approved By</th>
                        <th width="110">Appv. Date & Time</th>
                        <th>Appv. Status</th>
					</tr>

				</thead>
			</table>
			<div style="width:<? echo $width+18;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
			
				
				$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; 	
				$total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
	 $all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=$total_service_cost=$total_print_amount=$total_gmtdying_amount=0;
	 $total_embroidery_amount=$total_special_amount=$total_other_amount=$total_wash_cost=$total_certificate_cost=$total_currier_cost=0;
	 $total_all_over_print_cost=$total_b2b_cost=$total_act_material_cost=$total_act_service_cost=$total_material_service_cost=$total_order_amount=$total_cost=$total_common_oh=0;
					

			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				else if(str_replace("'","",$cbo_search_date)==4)
				{
					$update_date=explode(" ",$row[csf('update_date')]);
					$ship_po_recv_date=change_date_format($update_date[0]);
				}
				$dzn_qnty=$costing_per_arr[$row[csf('job_no')]];
				

				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$order_qty=$row[csf('po_quantity')];
				$order_uom=$row[csf('order_uom')];
				$costing_date=$row[csf('costing_date')];

				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];

				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];

			
				$total_plancut_amount+=$plancut_value;
				$po_ids=array_unique(explode(",",$row[csf('po_id')]));
						$commercial_cost=$yarn_costing=$yarn_req_qty=$fab_purchase_knit=$fab_purchase_woven=$conv_cost_poWise=$yarn_dyeing_cost=$heat_setting_cost=$trim_amount=$print_amount=$embroidery_amount=$special_amount=$wash_cost=$other_amount=$foreign=$local=$test_cost=$freight_cost=$inspection=$certificate_cost=$common_oh=$currier_cost=$cm_cost=$dfc_cost=$interest_cost=$incometax_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$fab_purchase_knit_qty=$gmtdying_amount=$fab_purchase_woven_qty=0;
						$fabric_dyeing_cost=0;$total_finishing_cost=$fab_purchase_knit_cost=$fab_purchase_woven_cost=0;
						foreach($po_ids as $pid)
						{
							$commercial_cost+=$commercial_costing_arr[$pid];
							$yarn_costing+=$yarn_costing_arr[$pid];
							$yarn_req_qty+=$yarn_req_qty_arr[$pid];
							$fab_purchase_knit+=array_sum($fabric_costing_arr['knit']['grey'][$pid][2]);
							$fab_purchase_woven+=array_sum($fabric_costing_arr['woven']['grey'][$pid][2]);
							
							// $fab_purchase_knit_qty+=array_sum($fabric_purchase_qty_arr['knit']['grey'][$pid][2]);
							// $fab_purchase_woven_qty+=array_sum($fabric_purchase_qty_arr['woven']['grey'][$pid][2]);

							$fab_purchase_knit_qty+=array_sum($fabric_purchase_qty_arr['knit']['grey'][$pid][2]);
							$fab_purchase_woven_qty+=array_sum($fabric_purchase_qty_arr['woven']['grey'][$pid][2]);
							
							//$fab_purchase_knit_cost+=array_sum($fabric_purchase_cost_arr['knit']['grey'][$pid][1]);
							//$fab_purchase_woven_cost+=array_sum($fabric_purchase_cost_arr['woven']['grey'][$pid][1]);
							
							//fabric_purchase_cost_arr
							//echo $fab_purchase_knit_qty.'DDD';
							
							$conv_cost_poWise+=array_sum($conversion_costing_po_arr[$pid]);
							$yarn_dyeing_cost+=array_sum($conversion_costing_arr_process[$pid][30]);
							$heat_setting_cost+=array_sum($conversion_costing_arr_process[$pid][33]);
							
							$trim_amount+= $trims_costing_arr[$pid];
							$print_amount+=$emblishment_costing_arr_name[$pid][1];
							$embroidery_amount+=$emblishment_costing_arr_name[$pid][2];
							$special_amount+=$emblishment_costing_arr_name[$pid][4];
							$wash_cost+=$emblishment_costing_arr_name_wash[$pid][3];
							$gmtdying_amount+=$emblishment_costing_arr_name[$pid][5];
							$other_amount+=$emblishment_costing_arr_name[$pid][99];
							$foreign+=$commission_costing_arr[$pid][1];
							$local+=$commission_costing_arr[$pid][2];
							$test_cost+=$other_costing_arr[$pid]['lab_test'];
							$freight_cost+=$other_costing_arr[$pid]['freight'];
							$inspection+=$other_costing_arr[$pid]['inspection'];
							$certificate_cost+=$other_costing_arr[$pid]['certificate_pre_cost'];
							$common_oh+=$other_costing_arr[$pid]['common_oh'];
							$currier_cost+=$other_costing_arr[$pid]['currier_pre_cost'];
							$cm_cost+=$other_costing_arr[$pid]['cm_cost'];
							$dfc_cost+=$other_costing_arr[$pid]['deffdlc_cost'];//$deffdlc_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;
							$interest_cost+=$interest_cost_arr[$pid]*$order_qty_pcs;
							$incometax_cost+=$incometax_cost_arr[$pid]*$order_qty_pcs;
							
							foreach($conversion_cost_head_array as $key=>$val)
							{
								if (!in_array($key, $not_process_id_print_array)) 
								{
									//$fabric_dyeing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fab_process_id]);
									$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$pid][$key]);
									
								}
								
							}
							$fabric_finish=0;
							foreach($fab_finish_cost_arr as $fin_process_id)
							{
								$fabric_finish+=array_sum($conversion_costing_arr_process[$pid][$fin_process_id]);
								
							}
							$total_finishing_cost+=$fabric_finish;
						
							
						}
						$total_fab_dyeing_cost =$fabric_dyeing_cost+$total_finishing_cost;
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					 <td width="40"><? echo $i; ?></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $team_member_arr[$row[csf('team_leader')]]; ?></div></td>

                     <td width="100"><p><? echo $company_library[$row[csf('company_name')]]; ?></p></td>
                     <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('style_ref_no')]; ?></div></td>
					 <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110" align="right"><p><? echo $row[csf('set_smv')]; ?></p></td>
                     <td width="60" align="right">
						<? 
							$cpmVal=$smv_eff_arr[$row[csf('job_no')]]['cpm']/$smv_eff_arr[$row[csf('job_no')]]['exc_rate'];
							echo  number_format($cpmVal*100/$smv_eff_arr[$row[csf('job_no')]]['eff'],4); 
                        ?>
                     </td>
                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $smv_eff_arr[$row[csf('job_no')]]['eff']; ?></div></td>
                    <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo change_date_format($costing_date); ?></div></td>
                  
                    <td width="90" align="right"><p><? echo number_format($order_qty,4); ?></p></td>
                    <td width="60" align="center"><p><? echo $unit_of_measurement[$order_uom]; ?></p></td>
                    <td width="90" align="right"><p><? echo number_format($order_qty*$row[csf('set_smv')],4); ?></p></td>

                    <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                    <td width="70" align="right"><div style="word-wrap:break-word; width:70px"><? echo $row[csf('excess_cut')]; ?></div></td>
                    <td width="90" align="right"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('plan_cut')]; ?></div></td>
                    <td width="70" align="right"><p><? echo number_format($order_value/$order_qty,4); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($order_value,4); ?></p></td>

                    <?
						$avg_rate=$yarn_costing/$yarn_req_qty;
						$yarn_cost_percent=($yarn_costing/$order_value)*100;
						$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
						$fab_purchase_qty=$fab_purchase_knit_qty+$fab_purchase_woven_qty;
						//$fab_purchase_knit_qty+=array_sum($fabric_qty_arr['knit']['grey'][$pid]);
							//$fab_purchase_woven_qty
						$tot_fabric_cost=$yarn_costing+$fab_purchase+$conv_cost_poWise;


						$knit_cost=0;$knite_qty=0;
						foreach($knit_cost_arr as $process_id)
						{ //conversion_job_amount_arr_process
							$knit_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$process_id]);
							$knite_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$process_id]);
						}
						$all_over_print_cost=$all_over_print_qty=0;
						foreach($aop_cost_arr as $aop_process_id)
						{
							$all_over_print_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$aop_process_id]);
							$all_over_print_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$aop_process_id]);
						}
							
						$yarn_dye_cost=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][30]);
						$yarn_dye_qty=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][30]);
						
						$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_qty=$fab_purchase_knit_qty+$fab_purchase_woven_qty;
						//$fabric_dyeing_cost=0;
						//foreach($fabric_dyeingCost_arr as $fab_process_id)
						//{
							//$fabric_dyeing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fab_process_id]);
							//$fabric_dyeing_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$fab_process_id]);
						//}
						
						/*foreach($conversion_cost_head_array as $key=>$val)
						{
							if (!in_array($key, $not_process_id_print_array)) 
							{
								$fabric_dyeing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fab_process_id]);
							}
							
						}*/
						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_finish=0;
						foreach($fab_finish_cost_arr as $fin_process_id)
						{
							$fabric_finish+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fin_process_id]);
						}
						$washing_cost=0;
						foreach($washing_cost_arr as $w_process_id)
						{
							$washing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$w_process_id]);
						}

						$washing_qty=0;
						foreach($washing_qty_arr as $w_process_id)
						{
							$washing_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$w_process_id]);
						}


					//conversion_qty_arr_process
						
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;
						$finance_chrg=$order_value*$smv_eff_arr[$row[csf('job_no')]]['finPercent']/100;
						
						

						$total_material_cost=$total_cost_new;
						$others_cost_value = $total_cost -($cm_cost+$freight_cost+$commercial_cost+($foreign+$local));
						$net_order_val=$order_value-(($foreign+$local)+$commercial_cost+$freight_cost);
						$cm_value=$net_order_val-$others_cost_value;

						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100;
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;

						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
						if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
						if($commercial_cost<=0) $color_com="red"; else $color_com="";

					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_gmtdying_amount+=$gmtdying_amount;
					$total_wash_cost+=$wash_cost;
					$total_test_cost_amount+=$test_cost;

					/*$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_forg_local_amount+=$tot_forg_local;
					$total_FOB_amount+=$tot_netFOB;

					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;
					$total_finance_chrg_amount+=$finance_chrg;*/

					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_meterial_amount+=$total_material_cost;
					$total_cm_new_amount+=$tot_cmValue;
					$total_cm_amount+=$cm_cost;
					$total_margin_amount+=$tot_margin;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];

					if($trim_amount<=0) $color_trim="red"; else $color_trim="";
					if($cm_cost<=0) $color="red"; else $color="";
					$smv=0; $eff=0; $cpm=0; $cost_dzn_cm_title=0;
					$smv=$smv_eff_arr[$row[csf('job_no')]]['smv'];
					$eff=$smv_eff_arr[$row[csf('job_no')]]['eff'];
					$cpm=$smv_eff_arr[$row[csf('job_no')]]['cpm'];
					$cost_dzn_cm_title='Swe. SMV: '.$smv.'; Swe. EFF: '.$eff.'; CPM: '.$cpm;
						//echo $fabric_cost_pcs_arr[$row[csf('po_id')]].'='.$plan_cut_qnty;
					?>


                    <td width="100" align="right"><p><? echo number_format($yarn_req_qty,4); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($yarn_costing,4); ?></p></td>
                    <td width="60" align="right" title="YarnCost/YarnQty*100"><p><? echo number_format($yarn_costing/$order_value*100,4); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($fab_purchase_qty,4); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fab_purchase,4); ?></p></td>
                    <td width="60" align="right" title="FabCost/POValue*100"><p><? echo number_format($fab_purchase/$order_value*100,4); ?></p></td>
                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><? echo number_format($trim_amount,2);?></td>
					<td width="60" align="right" title="TrimCost/POValue*100"><p><? echo number_format($trim_amount/$order_value*100,4); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($yarn_dye_qty,4); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($yarn_dye_cost,4); ?></p></td>
                    <td width="60" align="right" title="YCost/POValue*100"><p><? echo number_format($yarn_dye_cost/$order_value*100,4); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($all_over_print_cost,2); ?></p></td>
                    <td width="60" align="right" title="AopCost/PoValue*100"><p><? echo number_format($all_over_print_cost/$order_value*100,4); ?></p></td>
                    
                     <td width="100" align="right" title="Yarn+Purchase+Trims+YarnDye+AOP Cost"><p><?
					 $tot_b2b_cost=$yarn_costing+$fab_purchase+$trim_amount+$yarn_dye_cost+$all_over_print_cost;
					
					  echo number_format($tot_b2b_cost,4); ?></p></td>
                    <td width="60" align="right" title="Tot B2bCost/PoValue*100"><p><? echo number_format($tot_b2b_cost/$order_value*100,4); ?></p></td>
                    
                    
                    <td width="100" align="right"><p><? echo number_format($knite_qty,4); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($knit_cost,4); ?></p></td>
                    <td width="60" align="right" title="Tot KnitCost/PoValue*100"><p><? echo number_format($knit_cost/$order_value*100,4); ?></p></td>
                    
                    
                    <td width="100" align="right"><p><? echo number_format($fabric_dyeing_qty,4); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($total_fab_dyeing_cost,4); ?></p></td>
                    <td width="60" align="right" title="Tot DyeCost/PoValue*100"><p><? echo number_format($total_fab_dyeing_cost/$order_value*100,4); ?></p></td>

                     <td width="80" align="right"><? echo number_format($print_amount,4); ?></td>
                  	<td width="60" align="right" title="Tot PrintCost/PoValue*100"><p><? echo number_format($print_amount/$order_value*100,4); ?></p></td>
					 <td width="85" align="right"><? echo number_format($embroidery_amount,4); ?></td>
                    <td width="60" align="right" title="Tot EmbroCost/PoValue*100"><p><? echo number_format($embroidery_amount/$order_value*100,4); ?></p></td>
                     <td width="85" align="right"><? echo number_format($gmtdying_amount,4); ?></td>
                     <td width="60" align="right" title="Tot GmtDyingCost/PoValue*100"><p><? echo number_format($gmtdying_amount/$order_value*100,4); ?></p></td>
					 <td width="80" align="right"><? echo number_format($special_amount,4); ?></td>
                     <td width="60" align="right" title="Tot SpecialCost/PoValue*100"><p><? echo number_format($special_amount/$order_value*100,4); ?></p></td>
					 <td width="80" align="right"><? echo number_format($wash_cost,4); ?></td>
                     <td width="60" align="right" title="Tot WashCost/PoValue*100"><p><? echo number_format($wash_cost/$order_value*100,4); ?></p></td>
					 <td width="80" align="right"><? echo number_format($other_amount,4); ?></td>
                     <td width="100" align="right" title="Knit+Fin Dying+Print_Embroidery+GmtDying+Special+Wash+OtherCost"><p><? 
					 $tot_service_cost=$knit_cost+$total_fab_dyeing_cost+$print_amount+$embroidery_amount+$gmtdying_amount+$special_amount+$wash_cost+$other_amount;
					 $total_service_cost+=$tot_service_cost; echo  number_format($tot_service_cost,4);
					 
					  $tot_act_material_cost=$yarn_costing+$fab_purchase+$trim_amount;
					   $total_act_material_cost+=$tot_act_material_cost;
					   $tot_act_service_cost=$yarn_dye_cost+$all_over_print_cost+$knit_cost+$total_fab_dyeing_cost+$print_amount+$embroidery_amount+$special_amount+$gmtdying_amount+$other_amount+$wash_cost;
					    $total_act_service_cost+=$tot_act_service_cost;
						
						 $tot_material_service_cost=$tot_b2b_cost+$tot_service_cost;
						 $total_material_service_cost+=$tot_material_service_cost;
					  ?></p></td>
                    <td width="60" align="right" title="Tot ServiceCost/PoValue*100"><p><? echo number_format($tot_service_cost/$order_value*100,4); ?></p></td>
                    <td width="100" align="right" title="Yarn+Purchase+Trims Cost"><p><?  echo number_format($tot_act_material_cost,4); ?></p></td>
                    <td width="60" align="right" title="Tot Act. MaterialCost/PoValue*100"><p><? echo number_format($tot_act_material_cost/$order_value*100,4); ?></p></td>
                    
                    <td width="100" align="right" title="YarnDye+Aop+Kint+FabricDye+Print+Embro+Wash Cost"><p><? echo number_format($tot_act_service_cost,4); ?></p></td>
                 	<td width="60" align="right" title="Tot Act.ServiceCost/PoValue*100"><p><? echo number_format($tot_act_service_cost/$order_value*100,4); ?></p></td>
                    <td width="100" align="right" title="Tot B2B+Service Cost"><p><?  echo number_format($tot_material_service_cost,4); ?></p></td>
                   
                     <td width="60" align="right" title="Tot Material+ServiceCost/PoValue*100"><p><? echo number_format($tot_material_service_cost/$order_value*100,4); ?></p></td>
					 <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,4); ?></td>
					 <td width="60" align="right" title="Tot commercial costt/PoValue*100"><p><? echo number_format($commercial_cost/$order_value*100,4); ?></p></td>
                     <td width="100" align="right"><? echo number_format($test_cost,4);?></td>
					 <td width="100" align="right"><? echo number_format($freight_cost,4); ?></td>
					 <td width="120" align="right"><? echo number_format($inspection,4);?></td>
					 <td width="100" align="right"><? echo number_format($certificate_cost,4); ?></td>
                     <td width="100" align="right"><? echo number_format($common_oh,4); ?></td>
                    
					 <td width="100" align="right"><? echo number_format($currier_cost,4);?></td>

                     <td width="120" align="right"><? echo number_format($foreign,4) ?></td>
					 <td width="120" align="right"><? echo number_format($local,4) ?></td>
                     <td width="110" align="right"><? $tot_forg_local=$foreign+$local; echo number_format($tot_forg_local,4);  ?></td>
                   
					 <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,4);?></td>
                     
					<td width="60" align="right" title="Tot CM cost/PoValue*100"><p><? echo number_format($cm_cost/$order_value*100,4); ?></p></td>
					 <td width="100" align="right" title="Tot Mat+Service+Commercial+Test+Freight+Inspection+Certificate+HO Exp.+Currier+Foreign+Local+CM Cost"><p><?
					 $tot_cost=$tot_material_service_cost+$commercial_cost+$test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$tot_forg_local+$cm_cost+$heat_setting_cost;;
					 $total_cost+=$tot_cost;
					  echo number_format($tot_cost,4); ?></p></td>
					  <td width="60" align="right" title="Tot Cost/PoValue*100"><p><? echo number_format($tot_cost/$order_value*100,4); ?></p></td>
                     <td width="110" align="right" title="PO Value-Total Cost"><? $tot_margin=$order_value-$tot_cost; echo  number_format($tot_margin,4); ?></td>
                     <td width="60" align="right"><div style="word-wrap:break-word; width:60px"><? echo number_format(($tot_margin/$order_value*100),4); ?></div></td>
                      <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo $lib_user_arr[$row[csf('approved_by')]]; ?></div></td>
                      <td width="110" align="center"><div style="word-wrap:break-word;"><? echo $row[csf('approved_date')]; ?></div></td>
                      <td><? echo $approval_type_arr[$row[csf('approved')]];?></td>
					<?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";
						else $color_pl="";
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
						$expect_variance=$total_profit-$expected_profit_per;

					?>
				  </tr>
				<?
				$total_order_qty_pcs+=$order_qty_pcs;
				$total_plan_cut_qnty_pcs+=$plan_cut_qnty;
				$total_order_qty+=$row[csf('po_quantity')];
				$total_order_amount+=$order_value;
				$total_yarn_required+=$yarn_req;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$total_fab_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$total_all_over_print_cost+=$all_over_print_cost;
				$total_trim_cost+=$trim_amount;$total_foreign_cost+=$foreign;$total_local_cost+=$local;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;
				
				 $total_b2b_cost+=$tot_b2b_cost;
				 

				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_cm_value+=$cm_value;
				$total_cm_new_value+=$tot_cmValue;
				$total_forg_local_amount_new+=$tot_forg_local;
				$total_FOB_val_amount+=$tot_netFOB;

				$total_margin_new_amount+=$tot_margin;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_finance_chrg_cost+=$finance_chrg;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;

				$total_yarn_qty+=$yarn_req_qty;
				$total_knite_qty+=$knite_qty;
				$total_fabric_dyeing_qty+=$fabric_dyeing_qty;
				$total_washing_qty+=$washing_qty;

				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			?>
			</table>
			</div>
			<table class="tbl_bottom" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="60">&nbsp;</td>
                    <td width="110">Total:</td>
					<td width="110">&nbsp;</td>
                    <td width="90">&nbsp;</td>
					<td width="60">&nbsp;</td>

                   
					<td width="90">&nbsp;</td>
                    <td width="90" align="right" id="total_order_qnty_pcs"><? echo number_format($total_order_qty_pcs,4); ?></td>
                    <td width="70">&nbsp;</td>
					<td width="90"><? echo number_format($total_plan_cut_qnty_pcs,4); ?></td>
					

                    <td width="70" align="right" id=""><? //echo number_format($total_order_amount,2); ?></td>

                    <td width="100" id="total_order_amount"><? echo number_format($total_order_amount,4); ?></td>
                    <td width="100"><? echo number_format($total_yarn_qty,4); ?></td>
                    <td width="100"><? echo number_format($total_yarn_cost,4); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                     <td width="60"><? //echo number_format($total_yarn_qty); ?></td>
                    <td width="60"><? //echo number_format($total_yarn_cost,2); ?></td>
                    
                    
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,4); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="100"><? echo number_format($total_all_over_print_cost,4); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="100" id="total_b2b_cost"><? echo number_format($total_b2b_cost,4); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                          
                    <td width="100"><? echo number_format($total_knite_qty,4);//number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="100"><? echo number_format($total_knitting_cost,4); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="100"><? echo number_format($total_fabric_dyeing_qty,4); ?></td>
                    <td width="100"><? echo number_format($total_fabric_dyeing_cost,4);//number_format($total_washing_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_fabric_dyeing_qty,2);//number_format($total_washing_qty,2); ?></td>
                    <td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,4); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_embroidery_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,4); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_wash_cost,2); ?></td>
                    <td width="85" align="right" id="total_gmtdying_amount"><? echo number_format($total_gmtdying_amount,4); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,4); ?></td>
                    <td width="60" align="right"><? //echo number_format($tot_fab_cost,2);total_special_amount ?></td>

					<td width="80" id="total_wash_cost"><? echo number_format($total_wash_cost,4); ?></td>
				
					<td width="60" align="right" id=""><? //echo number_format($total_commercial_cost,2); ?></td>
					<td width="80" id="total_other_amount"><? echo number_format($total_other_amount,4); ?></td>
                    <td width="100" id="total_service_cost"><? echo number_format($total_service_cost,4); ?></td>
                    <td width="60" align="right" id=""><? //echo number_format($total_test_cost_amount,2); ?></td>
					<td width="100" align="right" id="total_act_material_cost"><? echo number_format($total_act_material_cost,4); ?></td>
                    <td width="60" align="right"><? //echo number_format($tot_interest_cost,2);?></td>
					<td width="100" align="right" id="total_act_service_cost"><? echo number_format($total_act_service_cost,4); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_material_service_cost,2); ?></td>
                    <td width="100" align="right" id="total_material_service_cost"><? echo number_format($total_material_service_cost,4); ?></td>

					<td width="60" align="right" id=""><? //echo number_format($total_currier_amount,2); ?></td>
					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,4);?></td>
					<td width="60" align="right"><? //echo number_format($tot_interest_cost,2);?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,4);?></td>

                    <td width="100" align="right" id="total_freight_cost"><? echo number_format($total_freight_cost,4); ?></td>


                    <td width="120" align="right" id="total_inspection"><? echo number_format($total_inspection,4); ?></td>
					<td width="100" align="right" id="total_certificate_cost"><? echo number_format($total_certificate_cost,4); ?></td>
                    <td width="100" align="right" id="total_common_oh"><? echo number_format($total_common_oh_amount,4); ?></td>
                    <td width="100" align="right" id="total_currier_cost"><? echo number_format($total_currier_cost,4); ?></td>
                   

                    <td width="120" align="right" id="total_foreign_cost"><? echo number_format($total_foreign_cost,4); ?></td>
					<td width="120" id="total_local_cost"><? echo number_format($total_local_cost,4); //total_FOB_val_amount?></td>
					<td width="110"><? echo number_format($total_forg_local_amount_new,4); //total_FOB_val_amount?></td>
					
                    <td width="100"><? echo number_format($total_cm_cost,4); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2); ?></td>
                    <td width="100"><? echo number_format($total_cost,4); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2);?></td>
                    <td width="110"><? echo number_format($total_margin_new_amount,4); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2); ?></td>
                  
                    <td width="110"><? //echo number_format($total_cm_cost,2); ?></td>
                    <td width="110"><? //echo number_format($total_cm_cost,2); ?></td>
                   
                          
                    <td>&nbsp;</td>

				</tr>
			</table>
			<table>
				<tr>
					<?
					$total_fab_cost=number_format($total_fab_cost_amount,4,'.','');
					$total_trim_cost=number_format($total_trim_cost,4,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,4,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,4,'.','');
					$total_commssion=number_format($total_commssion,4,'.','');
					$total_testing_cost=number_format($total_testing_cost,4,'.','');
					$total_freight_cost=number_format($total_freight_cost,4,'.','');
					$total_cost_up=number_format($total_cost_up,4,'.','');
					$total_cm_cost=number_format($total_cm_cost,4,'.','');
					$total_order_amount=number_format($total_order_amount,4,'.','');
					$total_inspection=number_format($total_inspection,4,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,4,'.','');
					$total_common_oh=number_format($total_common_oh,4,'.','');
					$total_currier_cost=number_format($total_currier_cost,4,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,4,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,4,'.','');
					?>
					<input type="hidden" id="graph_data" value=""/>
				</tr>
			</table> 
			<br>
            <a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>
            <div style="width:600px; display:none" id="yarn_summary" >
            <div id="data_panel2" align="center" style="width:500px">
					 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
				 </div>
            <table class="rpt_table" width="590" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                	<tr>
                                    	<th colspan="7">Yarn Cost Summary</th>
                                    </tr>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="140">Composition</th>
                                        <th width="60">Yarn Count</th>
                                        <th width="80">Type</th>
                                        <th width="100">Req. Qty</th>
                                        <th width="70">Avg. Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <?
								$yarn_des_data_summ_arr=array();
								foreach($yarn_des_data as $count=>$count_value)
                                {
									foreach($count_value as $Composition=>$composition_value)
									{
											//ksort($composition_value);
											foreach($composition_value as $percent=>$percent_value)
											{
													foreach($percent_value as $type=>$type_value)
													{
														$compo_val=$composition[$Composition];
														$count_name=$yarn_count_library[$count];
														$yarn_type_name=$yarn_type[$type];
														
													$yarn_des_data_summ_arr[$compo_val][$count_name][$yarn_type_name][$percent]['qty']=$type_value['qty'];	
													$yarn_des_data_summ_arr[$compo_val][$count_name][$yarn_type_name][$percent]['amount']=$type_value['amount'];
													}
											}
									}
								}
								
                                $s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
								ksort($yarn_des_data_summ_arr);
                                foreach($yarn_des_data_summ_arr as $Composition_key=>$composition_value)
                                {
										ksort($composition_value);
								foreach($composition_value as $count_key=>$count_value)
                                {
								ksort($count_value);
								foreach($count_value as $type=>$type_value)
                                {
								foreach($type_value as $percent=>$value)
                                {
                                    if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    //$yarn_desc=explode("**",$key);

                                    $tot_yarn_req_qnty+=$value['qty'];
                                    $tot_yarn_req_amnt+=$value['amount'];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
                                        <td><? echo $s; ?></td>
                                        <td title="<? echo $Composition_key;?>"><div style="word-wrap:break-word; width:140px"><? echo $Composition_key." ".$percent."%"; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:60px"><? echo $count_key; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:80px"><? echo $type; ?></div></td>
                                        <td align="right"><? echo number_format($value['qty'],4); ?></td>
                                        <td align="right"><? echo number_format($value['amount']/$value['qty'],4); ?></td>
                                        <td align="right"><? echo number_format($value['amount'],4); ?></td>
                                    </tr>
                                    <?
                                    $s++;
								}
								}
								}
                                }
                                ?>
                                <tfoot>
                                    <th colspan="4" align="right">Total</th>
                                    <th align="right"><? echo number_format($tot_yarn_req_qnty,4); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,4); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt,4); ?></th>
                                </tfoot>
                            </table>
                     </div>
			</fieldset>
			</div>
			<?
		}
	}
	else if($report_type==4 && $cbo_search_type==2) //Report button //PO  Wise
	{
		 
		if($template==1)
		{

			$app_status_arr=array(
				222=>'Not Submit',
				202=>'Not Submit',
				212=>'Ready For Approval',
				211=>'Partial Approved',
				111=>'Full Approved'
			);
			$style1="#E9F3FF";$style="#FFFFFF";
			$cm_cost_formula_sql=sql_select( "select id, cm_cost_method from variable_order_tracking where  variable_list=22 $company_con_name order by id");
			$cm_cost_formula=$cm_cost_predefined_method[$cm_cost_formula_sql[0][csf('cm_cost_method')]];
			$asking_profit_arr=array();
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $company_con_id");//$date_max_profit
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
				}
			}
			$asking_profit=array();
			if($db_type==0)
			{
				
				$po_grp="group_concat(b.id) as po_id";
			}
			else
			{
				$po_grp="listagg(CAST(b.id as VARCHAR(4000)),',') within group (order by b.id) as po_id";
			}

		  $sql_budget="select a.id,a.company_name,a.job_no_prefix_num,a.team_leader,a.set_smv, max(b.insert_date) as insert_date, c.insert_date as pre_insert_date, c.costing_date, max(b.update_date) as update_date, a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, sum(b.plan_cut) as plan_cut,sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved,b.id as po_id,b.po_number from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.entry_from=158 $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $approvalCon $file_con_b $ref_con_b group by  a.id,a.company_name,a.job_no_prefix_num, c.insert_date , c.costing_date,a.job_no, a.buyer_name, a.style_ref_no, a.order_uom, a.agent_name, a.avg_unit_price, a.team_leader,a.dealing_marchant,a.set_smv, a.gmts_item_id, a.total_set_qnty,b.id,b.po_number,c.job_no,c.approved_by,c.approved_date,c.approved,c.ready_to_approved,c.partial_approved";
			 //echo $sql_budget; die;
		

			$result_sql_budget=sql_select($sql_budget);
			$tot_rows_budget=count($result_sql_budget);
			$budget_data_arr=array();
			foreach($result_sql_budget as $row )
			{
				$job_id.=$row[csf('id')].',';
			}
			$jobIds=chop($job_id,',');  
			$job_ids=count(array_unique(explode(",",$job_id)));
			if($db_type==2 && $job_ids>1000)
			{
				$job_cond_for_in=" and (";
				$jobIdsArr=array_chunk(explode(",",$jobIds),999);
				foreach($jobIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$job_cond_for_in.=" b.id in($ids) or"; 
				}
				$job_cond_for_in=chop($job_cond_for_in,'or ');
				$job_cond_for_in.=")";
			}
			else
			{
				$job_cond_for_in=" and b.id in($jobIds)";
			}
			$cpm_arr=array();$cpm_date_arr=array(); //$yarn_desc_array=array();
			$finance_arr=array();
			$sql_cpm=sql_select("select applying_period_date, applying_period_to_date, cost_per_minute,interest_expense from lib_standard_cm_entry where 1=1 $company_con_id");
			foreach($sql_cpm as $cpMrow )
			{
				$applying_period_date=change_date_format($cpMrow[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($cpMrow[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate = date("d-m-Y",strtotime(add_date(str_replace("'","",$applying_period_date),$j)));
					$cpm_arr[$newdate]['cpm']=$cpMrow[csf('cost_per_minute')];
					$finance_arr[$newdate]['finPercent']=$cpMrow[csf('interest_expense')];
				}
			}
			$smv_eff_arr=array(); $costing_library=array();
			$sql_smv_cpm=sql_select("select a.job_no, a.costing_date, a.sew_smv, a.sew_effi_percent,a.exchange_rate,b.company_name from wo_pre_cost_mst a,wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond_for_in");
			foreach($sql_smv_cpm as $smv_cpm )
			{
				$dateKey=date("d-m-Y",strtotime($smv_cpm[csf('costing_date')]));
				
				$costing_library[$smv_cpm[csf('job_no')]]=$smv_cpm[csf('costing_date')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['smv']=$smv_cpm[csf('sew_smv')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['eff']=$smv_cpm[csf('sew_effi_percent')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['cpm']=$cpm_arr[$dateKey]['cpm'];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['exc_rate']=$smv_cpm[csf('exchange_rate')]; 
			}
			unset($sql_smv_cpm);
			
			//var_dump($smv_eff_arr);die;
 			$condition= new condition();
			 if(str_replace("'","",$cbo_company_name)>0){
				 $condition->company_name("=$cbo_company_name");
			 }
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num("=$txt_job_no");
			 }
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
			 }
			  if(str_replace("'","",$cbo_search_date) ==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				$condition->insert_date("$insert_date_cond");
			 }


			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no");
			 }

			$condition->init();

		    $costing_per_arr=$condition->getCostingPerArr();
			$fabric= new fabric($condition);
			//$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_costing_arr=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			$fabric_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_purchase_qty_arr=$fabric->getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			//print_r($fabric_purchase_qty_arr);
			$yarn= new yarn($condition);
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();

			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();
			$yarn= new yarn($condition);
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			$conversion_qty_arr_process=$conversion->getQtyArray_by_orderAndProcess();
			$conversion_costing_po_arr=$conversion->getAmountArray_by_order();

			$conversion_qty= new conversion($condition);
			$conversion_qty_arr_process = $conversion_qty->getQtyArray_by_orderAndProcess();
			$conversion_job_amount_arr_process = $conversion_qty->getAmountArray_by_jobAndProcess();
			$conversion_job_qty_arr_process = $conversion_qty->getQtyArray_by_jobAndProcess();
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149,158);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
			$washing_qty_arr=array(140,142,148,64);	
			
			
			
			foreach($result_sql_budget as $row )
			{
				$total_order_amount+=$row[csf('po_total_price')];
				$total_po_quantity+=$row[csf('po_quantity')]*$row[csf('ratio')];
				$poIdArr=array_unique(explode(",",$row[csf('po_id')]));
				foreach($poIdArr as $poId)
				{

					$total_yarn_cost+=$yarn_costing_arr[$poId];
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$poId][2]);
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$poId][2]);
					$total_purchase_cost+=($fab_purchase_knit+$fab_purchase_woven);
					$total_trim_cost+=$trims_costing_arr[$poId];
					$total_yarn_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][30]);
					$all_over_cost=0;
					foreach($aop_cost_arr as $aop_process_id)
					{
						$all_over_cost+=array_sum($conversion_costing_arr_process[$poId][$aop_process_id]);
					}
					$all_over_print_cost+=$all_over_cost;				
					
					$knit_cost=0;
					foreach($knit_cost_arr as $process_id)
					{
						$knit_cost+=array_sum($conversion_costing_arr_process[$poId][$process_id]);
					}
					$total_knit_cost+=$knit_cost;
					
					$fabric_dyeing_cost=0;
					foreach($fabric_dyeingCost_arr as $fab_process_id)
					{
						$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$poId][$fab_process_id]);
					}
					$total_fabric_dyeing_cost+=$fabric_dyeing_cost;				
								
					$fabric_finish=0;
					foreach($fab_finish_cost_arr as $fin_process_id)
					{
						$fabric_finish+=array_sum($conversion_costing_arr_process[$poId][$fin_process_id]);
					}
					$total_finishing_cost+=$fabric_finish;
					
					$total_heat_setting_cost+=array_sum($conversion_costing_arr_process[$poId][33]);
					
					$washing_cost=0;
					foreach($washing_cost_arr as $w_process_id)
					{
						$washing_cost+=array_sum($conversion_costing_arr_process[$poId][$w_process_id]);
					}
					$total_washing_cost+=$washing_cost;
					
					$total_print_amount+=$emblishment_costing_arr_name[$poId][1];
					$total_embroidery_amount+=$emblishment_costing_arr_name[$poId][2];
					$total_special_amount+=$emblishment_costing_arr_name[$poId][4];
					$total_other_amount+=$emblishment_costing_arr_name[$poId][5];
					$total_wash_cost+=$emblishment_costing_arr_name_wash[$poId][3];
					
					$total_commercial_cost+=$commercial_costing_arr[$poId];
					
					$total_foreign_amount+=$commission_costing_arr[$poId][1];
					$total_local_amount+=$commission_costing_arr[$poId][2];
					
					
					$total_testing_cost+=$other_costing_arr[$poId]['lab_test'];
					$total_freight_cost+=$other_costing_arr[$poId]['freight'];
					
					
					$total_inspection_cost+=$other_costing_arr[$poId]['inspection'];
					$total_certificate_cost+=$other_costing_arr[$poId]['certificate_pre_cost'];
					$total_common_oh+=$other_costing_arr[$poId]['common_oh'];
					$total_currier_cost+=$other_costing_arr[$poId]['currier_pre_cost'];
					$total_cm_cost+=$other_costing_arr[$poId]['cm_cost'];				
					
					
					$total_deffdlc_cost+=$other_costing_arr[$poId]['deffdlc_cost'];
					$total_design_cost+=$other_costing_arr[$poId]['design_cost'];
					$total_studio_cost+=$other_costing_arr[$poId]['studio_cost'];

				}
			}
			
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			$total_purchase_cost_percentage=$total_purchase_cost/$total_order_amount*100;
			$total_trim_cost_percentage=$total_trim_cost/$total_order_amount*100;
			$total_yarn_dyeing_percentage=$total_yarn_dyeing_cost/$total_order_amount*100;
			$all_over_print_cost_percentage=$all_over_print_cost/$total_order_amount*100;
			$total_knit_cost_percentage=$total_knit_cost/$total_order_amount*100;
			
			$total_dyefin_cost=$total_fabric_dyeing_cost+$total_finishing_cost+$total_heat_setting_cost+$total_washing_cost;
			$total_dyefin_cost_percentage=$total_dyefin_cost/$total_order_amount*100;
			
			
			$total_embelishment_cost=$total_print_amount+$total_embroidery_amount+$total_special_amount+$total_other_amount+$total_wash_cost;
			$total_embelishment_cost_percentage=$total_embelishment_cost/$total_order_amount*100;

			$total_commercial_cost_percentage=$total_commercial_cost/$total_order_amount*100;

			$total_commission_cost=$total_foreign_amount+$total_local_amount;
			$total_commission_cost_percentage=$total_commission_cost/$total_order_amount*100;
			
			
			$total_testing_cost_percentage=$total_testing_cost/$total_order_amount*100;
			$total_freight_cost_percentage=$total_freight_cost/$total_order_amount*100;
			$total_inspection_cost_percentage=$total_inspection_cost/$total_order_amount*100;
			$total_certificate_cost_percentage=$total_certificate_cost/$total_order_amount*100;
			$total_common_oh_percentage=$total_common_oh/$total_order_amount*100;
			$total_currier_cost_percentage=$total_currier_cost/$total_order_amount*100;
			$total_cm_cost_percentage=$total_cm_cost/$total_order_amount*100;
			
			
			$total_tot_cost=$total_yarn_cost+$total_purchase_cost+$total_knit_cost+$total_washing_cost+$all_over_print_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_trim_cost+$total_testing_cost+$total_print_amount+$total_embroidery_amount+$total_special_amount+$total_other_amount+$total_wash_cost+$total_commercial_cost+$total_foreign_amount+$total_local_amount+$total_freight_cost+$total_inspection_cost+$total_certificate_cost+$total_common_oh+$total_currier_cost+$total_cm_cost;			
			$total_tot_cost_percentage=$total_tot_cost/$total_order_amount*100;
			
			$total_material_cost=$total_yarn_cost+$total_purchase_cost+$total_trim_cost+$total_yarn_dyeing_cost+$all_over_print_cost;
			$total_material_cost_percentage=$total_material_cost/$total_order_amount*100;
			
			$total_service_cost=$total_knit_cost+$total_dyefin_cost+$total_embelishment_cost;
			$total_service_cost_percentage=$total_service_cost/$total_order_amount*100;			
			
			
			?>
			
	 <script>
			function toggle()
			{
				var ele = document.getElementById("yarn_summary");
				//alert(ele);
				var text = document.getElementById("displayText");
				if(ele.style.display!= "none")
				{
					ele.style.display = "none";
					text.innerHTML = "Show Yarn Summary";
				}
				else
				{
					ele.style.display = "block";
					text.innerHTML = "Hide Yarn Summary";
				}
			}

	  </script>
			
			<? 
				$width=6260;
				ob_start(); 
			?>
			
		<div style="width:<? echo $width;?>px;">
			<div style="width:900px;" align="left">
				<table width="900" cellpadding="0" cellspacing="2" border="0">
					<tr>
						<td width="350" align="left">
							<table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
								<thead align="center">
									<tr>
										<th colspan="4">Order Wise Budget Cost Summary</th>
									</tr>
									<tr>
										<th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
									</tr>
								</thead>
								<tr bgcolor="<? echo $style1; ?>">
									<td width="20">1</td>
									<td width="130">Yarn Cost</td>
									<td width="120" align="right"><? echo number_format($total_yarn_cost,2);?></td>
									<td align="right"><? echo number_format($total_yarn_cost_percentage,2);?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>2</td>
									<td>Fabric Purchase</td>
									<td align="right"><? echo number_format($total_purchase_cost,2);?></td>
									<td align="right" ><? echo number_format($total_purchase_cost_percentage,2);?></td>
								</tr>
								<tr bgcolor="<?  echo $style1; ?>">
									<td>3</td>
									<td>Trims Cost</td>
									<td align="right"><? echo number_format($total_trim_cost,2);?></td>
									<td align="right"><? echo number_format($total_trim_cost_percentage,2);?></td>
								</tr>
                                <tr bgcolor="#CCCCCC">
									<td></td>
									<td><strong>Actual Material Cost</strong></td>
									<td align="right" title="Yarn Cost+Trim Cost+Finished Fabric Purchase Cost"><? 
									$total_act_material_cost=$total_yarn_cost+$total_purchase_cost+$total_trim_cost;
									echo number_format($total_act_material_cost,2); ?></td>
									<td align="right"><? echo number_format($total_act_material_cost/$total_order_amount*100,2); ?></td>
								</tr>
                                
								<tr bgcolor="<? echo $style; ?>">
									<td>4</td>
									<td>Yarn Dyeing Cost</td>
									<td align="right"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
									<td align="right"><? echo number_format($total_yarn_dyeing_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>5</td>
									<td>AOP Cost</td>
									<td align="right"><? echo number_format($all_over_print_cost,2); ?></td>
									<td align="right"><? echo number_format($all_over_print_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total B2B Cost</strong></td>
									<td align="right"><? echo number_format($total_material_cost,2); ?></td>
									<td align="right"><? echo number_format($total_material_cost_percentage,2); ?></td>
								</tr>

                                <tr bgcolor="<? echo $style1; ?>">
									<td>6</td>
									<td>Knitting Cost</td>
									<td align="right"><? echo number_format($total_knit_cost,2); ?></td>
									<td align="right"><? echo number_format($total_knit_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>7</td>
									<td>Dyeing & Finishing Cost</td>
									<td align="right"><? echo number_format($total_dyefin_cost,2); ?></td>
									<td align="right"><? echo number_format($total_dyefin_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>8</td>
									<td>Print/ Emb. /Wash Cost</td>
									<td align="right"><? echo number_format($total_embelishment_cost,2); ?></td>
									<td align="right"><? echo number_format($total_embelishment_cost_percentage,2); ?></td>
								</tr>
                                <tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Actual Service Cost:</strong></td>
									<td align="right" title="Y/D Cost+AOP Cost+Knit Cost+Dye&Fin Cost+Printing Cost+Embroidery Cost+WashCost+Other ServiceCost(Embellishment Other Cost+Dyeing"><? 
									$total_act_service_cost=$total_yarn_dyeing_cost+$all_over_print_cost+$total_knit_cost+$total_dyefin_cost+$total_embelishment_cost;
									echo number_format($total_act_service_cost,2); ?></td>
									<td align="right"><? echo number_format($total_service_cost_percentage,2); ?></td>
								</tr>
                                
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Service Cost</strong></td>
									<td align="right"><? echo number_format($total_service_cost,2); ?></td>
									<td align="right"><? echo number_format($total_service_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Material & Service</strong></td>
									<td align="right"><? echo number_format($total_service_cost+$total_material_cost,2); ?></td>
									<td align="right"><? echo number_format(($total_service_cost+$total_material_cost)/$total_order_amount*100,2); ?></td>
								</tr>

                                
								<tr bgcolor="<? echo $style; ?>">
									<td>9</td>
									<td>Commercial Cost</td>
									<td align="right"><? echo number_format($total_commercial_cost,2); ?></td>
									<td align="right"><? echo number_format($total_commercial_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>10</td>
									<td>Commision Cost</td>
									<td align="right"><? echo number_format($total_commission_cost,2); ?></td>
									<td align="right"><? echo number_format($total_commission_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>11</td>
									<td>Testing Cost</td>
									<td align="right"><? echo number_format($total_testing_cost,2); ?></td>
									<td align="right"><? echo number_format($total_testing_cost_percentage,2); ?></td>
								</tr>
                                <tr bgcolor="<? echo $style1; ?>">
									<td>12</td>
									<td>Freight Cost</td>
									<td align="right"><? echo number_format($total_freight_cost,2); ?></td>
									<td align="right"><? echo number_format($total_freight_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>13</td>
									<td>Inspection Cost</td>
									<td align="right"><? echo number_format($total_inspection_cost,2); ?></td>
									<td align="right"><? echo number_format($total_inspection_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>14</td>
									<td>Certificate Cost</td>
									<td align="right"><? echo number_format($total_certificate_cost,2); ?></td>
									<td align="right"><? echo number_format($total_certificate_cost_percentage,2); ?></td>
								</tr>
									<tr bgcolor="<? echo $style; ?>">
									<td>15</td>
									<td>Operating Exp.</td>
									<td align="right"><? echo number_format($total_common_oh,2); ?></td>
									<td align="right"><? echo number_format($total_common_oh_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>16</td>
									<td>Courier Cost</td>
									<td align="right"><? echo number_format($total_currier_cost,2); ?></td>
									<td align="right"><? echo number_format($total_currier_cost_percentage,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>17</td>
									<td>CM Cost</td>
									<td align="right"><? echo number_format($total_cm_cost,2); ?></td>
									<td align="right"><? echo number_format($total_cm_cost_percentage,2); ?></td>
								</tr>
								
                                <tr bgcolor="<? echo $style1; ?>">
									<td>18</td>
									<td>Total Cost</td>
									<td align="right"><? echo number_format($total_tot_cost,2); ?></td>
									<td align="right"><? echo number_format($total_tot_cost_percentage,2); ?></td>
								</tr>
                                  <tr bgcolor="<? echo $style; ?>">
									<td>19</td>
									<td>Margin</td>
									<td align="right" title="Total Order Value-Total Cost"><? 
									$tot_margin=$total_order_amount-$total_tot_cost;
									$total_tot_margin_percentage=$tot_margin/$total_order_amount*100;
									echo number_format($tot_margin,2); ?></td>
									<td align="right"><? echo number_format($total_tot_margin_percentage,2); ?></td>
								</tr>
								
                                <tr bgcolor="<? echo $style1; ?>">
									<td>20</td>
									<td>Total Order Value</td>
									<td align="right"><? echo number_format($total_order_amount,2); ?></td>
									<td align="right"><? echo number_format($total_order_amount/$total_order_amount*100,2); ?></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>21</td>
									<td>Total Order Qty</td>
									<td align="right"><? echo number_format($total_po_quantity,4); ?></td>
									<td align="right"><? //echo number_format($total_order_amount/$total_order_amount*100,4); ?></td>
								</tr>
							</table>
						</td>
						
					</tr>
				</table>
			</div>
			<br/>			
			
			<?
			
			if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else if(str_replace("'","",$cbo_search_date)==3) $caption="PO Insert Date";
					else $caption="Cancelled Date";
					
					
						
			?>
			<fieldset style="width:100%;" id="content_search_panel2">
            		<div>
                       <span style="font-size:20px; font-weight:bold;"><? echo $company_library[$company_name]; ?></span><br/>
                       <span style="font-size:16px; font-weight:bold;">Cost Breakdown Analysis<br/></span>
                       <strong>From  <? echo $caption.' &nbsp;'.$date_from;  ?>  To  <? echo $date_to; ?></strong>
                    </div>
			   <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit'];?>
			<table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
                        <th width="110">Team Leader</th>
						<th width="100">Company</th>
                        <th width="70">Buyer</th>
                        <th width="110">Style</th>
						<th width="70">Job No</th>
                        <th width="110">Gmts Item Name</th>
                        <th width="110">SMV</th>
                        <th width="60">CPM</th>
                        <th width="110">Efficiency</th>
                        <th width="110">Costing Date</th>
                        <th width="90">Style Qty</th>
                        <th width="60">UOM</th>
                        <th width="90">Total SMV</th>
                        <th width="90">Style Qty (Pcs)</th>
                        <th width="70">Excess Cut %</th>
                        <th width="90">Plan Cut Qty (Pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">Style Value</th>
                        <th width="60">TTL Y/Qty</th>
                        <th width="60">TTL Y/Cost</th>
                        <th width="60">%</th>
                        <th width="60">Fin Fab Purchase Qty</th>
                        <th width="60">Fin Fab Purchase Cost</th>
                        <th width="60">%</th>
                        <th width="100">Trim Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">Y/D Qty</th>
                        <th width="60">Y/D Cost</th>
                        <th width="60">%</th>
                        <th width="60">AOP Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">Total B2B Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">TTL Knit Qty</th>
                        <th width="60">TTL Knit Cost</th>
                        <th width="60">%</th>
                        
                        <th width="60">TTL Dye & Fin Qty</th>
                        <th width="60">TTL Dye & Fin Cost</th>
                        <th width="60">%</th>
                        
                        <th width="80">Printing</th>
                        <th width="60">%</th>
						<th width="85">Embroidery</th>
                        <th width="60">%</th>
						<th width="80">Special Works</th>
                        <th width="60">%</th>
						<th width="80">Wash Cost</th>
                        <th width="60">%</th>
						<th width="80">Other</th>
                        
                        <th width="100">Total Service Cost</th>
                        <th width="60">%</th>
                        <th width="60">Actual Material Cost</th>
                        <th width="60">%</th>
                        <th width="100">Actual Service Cost</th>
                        <th width="60">%</th>
                       
						
                        <th width="100">Total Mat+Svc Cost</th>
                        <th width="60">%</th>
                        <th width="120">Commercial Cost</th>
                        <th width="60">%</th>
						<th width="100">Testing Cost</th>
						<th width="100">Freight Cost</th>
						<th width="120">Inspection Cost</th>
						<th width="100">Certificate Cost</th>
                        <th width="100">Operating Exp.</th>
                        
						<th width="100">Courier Cost</th>
                       
                       
                        <th width="120">Foreign Comm</th>
						<th width="120">Local Comm</th>
                        <th width="110">Total Comm</th>
                       
						<th width="100" title="<?php echo $cm_cost_formula; ?>">CM Cost</th>
                        <th width="60">%</th>
                        <th width="100">Total Cost</th>
                        <th width="60">%</th>
                        <th width="110">Total Margin</th>
                     
                        <th width="60">Margin %</th>
                     
                        <th width="110">Approved By</th>
                        <th width="110">Appv. Date & Time</th>
                        <th>Appv. Status</th>
					</tr>

				</thead>
			</table>
			<div style="width:<? echo $width+18;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
				
				$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; 	
				$total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
	 $all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=$total_service_cost=$total_print_amount=0;
	 $total_embroidery_amount=$total_special_amount=$total_other_amount=$total_wash_cost=$total_certificate_cost=$total_currier_cost=0;
	 $total_all_over_print_cost=$total_b2b_cost=$total_act_material_cost=$total_act_service_cost=$total_material_service_cost=$total_order_amount=$total_cost=$total_common_oh=0;
					

			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				else if(str_replace("'","",$cbo_search_date)==4)
				{
					$update_date=explode(" ",$row[csf('update_date')]);
					$ship_po_recv_date=change_date_format($update_date[0]);
				}
				$dzn_qnty=$costing_per_arr[$row[csf('job_no')]];
				

				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$order_qty=$row[csf('po_quantity')];
				$order_uom=$row[csf('order_uom')];
				$costing_date=$row[csf('costing_date')];

				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];

				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];

			
				$total_plancut_amount+=$plancut_value;
				$po_ids=array_unique(explode(",",$row[csf('po_id')]));
						$commercial_cost=$yarn_costing=$yarn_req_qty=$fab_purchase_knit=$fab_purchase_woven=$conv_cost_poWise=$yarn_dyeing_cost=$heat_setting_cost=$trim_amount=$print_amount=$embroidery_amount=$special_amount=$wash_cost=$other_amount=$foreign=$local=$test_cost=$freight_cost=$inspection=$certificate_cost=$common_oh=$currier_cost=$cm_cost=$dfc_cost=$interest_cost=$incometax_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$dfc_cost=$fab_purchase_knit_qty=$fab_purchase_woven_qty=0;
						foreach($po_ids as $pid)
						{
							$commercial_cost+=$commercial_costing_arr[$pid];
							$yarn_costing+=$yarn_costing_arr[$pid];
							$yarn_req_qty+=$yarn_req_qty_arr[$pid];
							$fab_purchase_knit+=array_sum($fabric_costing_arr['knit']['grey'][$pid][2]);
							$fab_purchase_woven+=array_sum($fabric_costing_arr['woven']['grey'][$pid][2]);
							
							$fab_purchase_knit_qty+=array_sum($fabric_purchase_qty_arr['knit']['grey'][$pid][2]);
							$fab_purchase_woven_qty+=array_sum($fabric_purchase_qty_arr['woven']['grey'][$pid][2]);
							
							$conv_cost_poWise+=array_sum($conversion_costing_po_arr[$pid]);
							$yarn_dyeing_cost+=array_sum($conversion_costing_arr_process[$pid][30]);
							$heat_setting_cost+=array_sum($conversion_costing_arr_process[$pid][33]);
							
									
							$trim_amount+= $trims_costing_arr[$pid];
							$print_amount+=$emblishment_costing_arr_name[$pid][1];
							$embroidery_amount+=$emblishment_costing_arr_name[$pid][2];
							$special_amount+=$emblishment_costing_arr_name[$pid][4];
							$wash_cost+=$emblishment_costing_arr_name_wash[$pid][3];
							$other_amount+=$emblishment_costing_arr_name[$pid][5];
							$foreign+=$commission_costing_arr[$pid][1];
							$local+=$commission_costing_arr[$pid][2];
							$test_cost+=$other_costing_arr[$pid]['lab_test'];
							$freight_cost+=$other_costing_arr[$pid]['freight'];
							$inspection+=$other_costing_arr[$pid]['inspection'];
							$certificate_cost+=$other_costing_arr[$pid]['certificate_pre_cost'];
							$common_oh+=$other_costing_arr[$pid]['common_oh'];
							$currier_cost+=$other_costing_arr[$pid]['currier_pre_cost'];
							$cm_cost+=$other_costing_arr[$pid]['cm_cost'];
							$dfc_cost+=$other_costing_arr[$pid]['deffdlc_cost'];//$deffdlc_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;
							$interest_cost+=$interest_cost_arr[$pid]*$order_qty_pcs;
							$incometax_cost+=$incometax_cost_arr[$pid]*$order_qty_pcs;
						}
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					 <td width="40"><? echo $i; ?></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $team_member_arr[$row[csf('team_leader')]]; ?></div></td>

                     <td width="100"><p><? echo $company_library[$row[csf('company_name')]]; ?></p></td>
                     <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('style_ref_no')]; ?></div></td>
					 <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110" align="right"><p><? echo $row[csf('set_smv')]; ?></p></td>
                     <td width="60" align="right">
						<? 
							$cpmVal=$smv_eff_arr[$row[csf('job_no')]]['cpm']/$smv_eff_arr[$row[csf('job_no')]]['exc_rate'];
							echo  number_format($cpmVal*100/$smv_eff_arr[$row[csf('job_no')]]['eff'],3); 
                        ?>
                     </td>
                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $smv_eff_arr[$row[csf('job_no')]]['eff']; ?></div></td>
                    <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo change_date_format($costing_date); ?></div></td>
                  
                    <td width="90" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                    <td width="60" align="center"><p><? echo $unit_of_measurement[$order_uom]; ?></p></td>
                    <td width="90" align="right"><p><? echo number_format($order_qty*$row[csf('set_smv')],2); ?></p></td>

                    <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                    <td width="70" align="right"><div style="word-wrap:break-word; width:70px"><? echo $row[csf('excess_cut')]; ?></div></td>
                    <td width="90" align="right"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('plan_cut')]; ?></div></td>
                    <td width="70" align="right"><p><? echo number_format($order_value/$order_qty,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>

                    <?
						$avg_rate=$yarn_costing/$yarn_req_qty;
						$yarn_cost_percent=($yarn_costing/$order_value)*100;
						$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
						$fab_purchase_qty=$fab_purchase_knit_qty+$fab_purchase_woven_qty;
						//$fab_purchase_knit_qty+=array_sum($fabric_qty_arr['knit']['grey'][$pid]);
							//$fab_purchase_woven_qty
						$tot_fabric_cost=$yarn_costing+$fab_purchase+$conv_cost_poWise;


						$knit_cost=0;$knite_qty=0;
						foreach($knit_cost_arr as $process_id)
						{ //conversion_job_amount_arr_process
							$knit_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$process_id]);
							$knite_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$process_id]);
						}
						$all_over_print_cost=$all_over_print_qty=0;
						foreach($aop_cost_arr as $aop_process_id)
						{
							$all_over_print_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$aop_process_id]);
							$all_over_print_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$aop_process_id]);
						}
							
						$yarn_dye_cost=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][30]);
						$yarn_dye_qty=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][30]);
						
						$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;

						$fabric_dyeing_cost=0;$fabric_dyeing_qty=0;
						foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
							$fabric_dyeing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fab_process_id]);
							$fabric_dyeing_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$fab_process_id]);
						}
						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_finish=0;
						foreach($fab_finish_cost_arr as $fin_process_id)
						{
							$fabric_finish+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$fin_process_id]);
						}
						$washing_cost=0;
						foreach($washing_cost_arr as $w_process_id)
						{
							$washing_cost+=array_sum($conversion_job_amount_arr_process[$row[csf('job_no')]][$w_process_id]);
						}

						$washing_qty=0;
						foreach($washing_qty_arr as $w_process_id)
						{
							$washing_qty+=array_sum($conversion_job_qty_arr_process[$row[csf('job_no')]][$w_process_id]);
						}


					//conversion_qty_arr_process
						
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;
						$finance_chrg=$order_value*$smv_eff_arr[$row[csf('job_no')]]['finPercent']/100;
						
						

						$total_material_cost=$total_cost_new;
						$others_cost_value = $total_cost -($cm_cost+$freight_cost+$commercial_cost+($foreign+$local));
						$net_order_val=$order_value-(($foreign+$local)+$commercial_cost+$freight_cost);
						$cm_value=$net_order_val-$others_cost_value;

						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100;
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;

						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
						if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
						if($commercial_cost<=0) $color_com="red"; else $color_com="";

					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;
					$total_test_cost_amount+=$test_cost;

					/*$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_forg_local_amount+=$tot_forg_local;
					$total_FOB_amount+=$tot_netFOB;

					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;
					$total_finance_chrg_amount+=$finance_chrg;*/

					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_meterial_amount+=$total_material_cost;
					$total_cm_new_amount+=$tot_cmValue;
					$total_cm_amount+=$cm_cost;
					$total_margin_amount+=$tot_margin;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];

					if($trim_amount<=0) $color_trim="red"; else $color_trim="";
					if($cm_cost<=0) $color="red"; else $color="";
					$smv=0; $eff=0; $cpm=0; $cost_dzn_cm_title=0;
					$smv=$smv_eff_arr[$row[csf('job_no')]]['smv'];
					$eff=$smv_eff_arr[$row[csf('job_no')]]['eff'];
					$cpm=$smv_eff_arr[$row[csf('job_no')]]['cpm'];
					$cost_dzn_cm_title='Swe. SMV: '.$smv.'; Swe. EFF: '.$eff.'; CPM: '.$cpm;
						//echo $fabric_cost_pcs_arr[$row[csf('po_id')]].'='.$plan_cut_qnty;
					?>


                    <td width="60" align="right"><p><? echo number_format($yarn_req_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($yarn_costing,2); ?></p></td>
                    <td width="60" align="right" title="YarnCost/YarnQty*100"><p><? echo number_format($yarn_costing/$yarn_req_qty*100,2); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($fab_purchase_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fab_purchase,2); ?></p></td>
                    <td width="60" align="right" title="FabCost/FabQty*100"><p><? echo number_format($fab_purchase/$fab_purchase_qty*100,2); ?></p></td>
                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><? echo number_format($trim_amount,2);?></td>
					<td width="60" align="right" title="TrimCost/PoQty*100"><p><? echo number_format($trim_amount/$order_value*100,2); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($yarn_dye_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($yarn_dye_cost,2); ?></p></td>
                    <td width="60" align="right" title="YCost/YQty*100"><p><? echo number_format($yarn_dye_qty/$yarn_dye_cost*100,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($all_over_print_cost,2); ?></p></td>
                    <td width="60" align="right" title="AopCost/PoValue*100"><p><? echo number_format($all_over_print_cost/$order_value*100,2); ?></p></td>
                    
                     <td width="60" align="right" title="Yarn+Purchase+Trims+YarnDye+AOP Cost"><p><?
					 $tot_b2b_cost=$yarn_costing+$fab_purchase+$trim_amount+$yarn_dye_cost+$all_over_print_cost;
					
					  echo number_format($tot_b2b_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot B2bCost/PoValue*100"><p><? echo number_format($tot_b2b_cost/$order_value*100,2); ?></p></td>
                    
                    
                    <td width="60" align="right"><p><? echo number_format($knite_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($knit_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot KnitCost/PoValue*100"><p><? echo number_format($knit_cost/$order_value*100,2); ?></p></td>
                    
                    
                    <td width="60" align="right"><p><? echo number_format($fabric_dyeing_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fabric_dyeing_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot DyeCost/PoValue*100"><p><? echo number_format($fabric_dyeing_cost/$order_value*100,2); ?></p></td>
                     <td width="80" align="right"><? echo number_format($print_amount,2); ?></td>
                     <td width="60" align="right"><p><? //echo $row[csf('xxxxxx')]; ?></p></td>
					 <td width="85" align="right"><? echo number_format($embroidery_amount,2); ?></td>
                     <td width="60" align="right"><p><? //echo $row[csf('xxxxxx')]; ?></p></td>
					 <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                     <td width="60" align="right"><p><? //echo $row[csf('xxxxxx')]; ?></p></td>
					 <td width="80" align="right"><? echo number_format($wash_cost,2); ?></td>
                     <td width="60" align="right"><p><? //echo $row[csf('xxxxxx')]; ?></p></td>
					 <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
                     <td width="100" align="right" title="Knit+Fin Dying+Print_Embroidery+Special+Wash+OtherCost"><p><? 
					 $tot_service_cost=$knit_cost+$fabric_dyeing_cost+$print_amount+$embroidery_amount+$special_amount+$wash_cost+$other_amount;
					 $total_service_cost+=$tot_service_cost; echo  number_format($tot_service_cost,2);
					 
					  $tot_act_material_cost=$yarn_costing+$fab_purchase+$trim_amount;
					   $total_act_material_cost+=$tot_act_material_cost;
					   $tot_act_service_cost=$yarn_dye_cost+$all_over_print_cost+$knit_cost+$fabric_dyeing_cost+$print_amount+$embroidery_amount+$wash_cost;
					    $total_act_service_cost+=$tot_act_service_cost;
						
						 $tot_material_service_cost=$tot_b2b_cost+$tot_service_cost;
						 $total_material_service_cost+=$tot_material_service_cost;
					  ?></p></td>
                    <td width="60" align="right" title="Tot ServiceCost/PoValue*100"><p><? echo number_format($tot_service_cost/$order_value*100,2); ?></p></td>
                    <td width="60" align="right" title="Yarn+Purchase+Trims Cost"><p><?  echo number_format($tot_act_material_cost,2); ?></p></td>
                    <td width="60" align="right" title="Tot Act. MaterialCost/PoValue*100"><p><? echo number_format($tot_act_material_cost/$order_value*100,2); ?></p></td>
                    
                    <td width="100" align="right" title="YarnDye+Aop+Kint+FabricDye+Print+Embro+Wash Cost"><p><? echo number_format($tot_act_service_cost,2); ?></p></td>
                 	<td width="60" align="right" title="Tot Act.ServiceCost/PoValue*100"><p><? echo number_format($tot_act_service_cost/$order_value*100,2); ?></p></td>
                    <td width="100" align="right" title="Tot B2B+Service Cost"><p><?  echo number_format($tot_material_service_cost,2); ?></p></td>
                   
                     <td width="60" align="right" title="Tot Material+ServiceCost/PoValue*100"><p><? echo number_format($tot_material_service_cost/$order_value*100,2); ?></p></td>
					 <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
					 <td width="60" align="right" title="Tot commercial costt/PoValue*100"><p><? echo number_format($commercial_cost/$order_value*100,2); ?></p></td>
                     <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
					 <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($inspection,2);?></td>
					 <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
                    
					 <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>

                     <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
					 <td width="120" align="right"><? echo number_format($local,2) ?></td>
                     <td width="110" align="right"><? $tot_forg_local=$foreign+$local; echo number_format($tot_forg_local,2);  ?></td>
                   
					 <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     
					<td width="60" align="right" title="Tot CM cost/PoValue*100"><p><? echo number_format($cm_cost/$order_value*100,2); ?></p></td>
					 <td width="100" align="right" title="Tot Mat+Service+Commercial+Test+Freight+Inspection+Certificate+HO Exp.+Currier+Foreign+Local+CM Cost"><p><?
					 $tot_cost=$tot_material_service_cost+$commercial_cost+$test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$tot_forg_local+$cm_cost;
					 $total_cost+=$tot_cost;
					  echo number_format($tot_cost,2); ?></p></td>
					  <td width="60" align="right" title="Tot Cost/PoValue*100"><p><? echo number_format($tot_cost/$order_value*100,2); ?></p></td>
                     
                     
                      
                     <td width="110" align="right" title="PO Value-Total Cost"><? $tot_margin=$order_value-$tot_cost; echo  number_format($tot_margin,2); ?></td>
                     <td width="60" align="right"><div style="word-wrap:break-word; width:60px"><? echo number_format($tot_margin/$order_value,2); ?></div></td>

                    


                   
                      <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo $lib_user_arr[$row[csf('approved_by')]]; ?></div></td>
                      <td width="110" align="center"><div style="word-wrap:break-word;"><? echo $row[csf('approved_date')]; ?></div></td>
                      <td><? echo $app_status_arr[$row[csf('approved')].$row[csf('ready_to_approved')].$row[csf('partial_approved')]];?></td>
					<?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";
						else $color_pl="";
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
						$expect_variance=$total_profit-$expected_profit_per;

					?>
				  </tr>
				<?
				$total_order_qty_pcs+=$order_qty_pcs;
				$total_plan_cut_qnty_pcs+=$plan_cut_qnty;
				$total_order_qty+=$row[csf('po_quantity')];
				$total_order_amount+=$order_value;
				$total_yarn_required+=$yarn_req;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$total_all_over_print_cost+=$all_over_print_cost;
				$total_trim_cost+=$trim_amount;$total_foreign_cost+=$foreign;$total_local_cost+=$local;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;
				
				 $total_b2b_cost+=$tot_b2b_cost;
				 

				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_cm_value+=$cm_value;
				$total_cm_new_value+=$tot_cmValue;
				$total_forg_local_amount_new+=$tot_forg_local;
				$total_FOB_val_amount+=$tot_netFOB;

				$total_margin_new_amount+=$tot_margin;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_finance_chrg_cost+=$finance_chrg;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;

				$total_yarn_qty+=$yarn_req_qty;
				$total_knite_qty+=$knite_qty;
				$total_fabric_dyeing_qty+=$fabric_dyeing_qty;
				$total_washing_qty+=$washing_qty;

				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			?>
			</table>
			</div>
			<table class="tbl_bottom" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="60">&nbsp;</td>
                    <td width="110">Total:</td>
					<td width="110">&nbsp;</td>
                    <td width="90">&nbsp;</td>
					<td width="60">&nbsp;</td>

                   
					<td width="90">&nbsp;</td>
                    <td width="90" align="right" id="total_order_qnty_pcs"><? echo number_format($total_order_qty_pcs,2); ?></td>
                    <td width="70">&nbsp;</td>
					<td width="90"><? echo number_format($total_plan_cut_qnty_pcs,2); ?></td>
					

                    <td width="70" align="right" id=""><? //echo number_format($total_order_amount,2); ?></td>

                    <td width="100" id="total_order_amount"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="60"><? echo number_format($total_yarn_qty); ?></td>
                    <td width="60"><? echo number_format($total_yarn_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                     <td width="60"><? //echo number_format($total_yarn_qty); ?></td>
                    <td width="60"><? //echo number_format($total_yarn_cost,2); ?></td>
                    
                    
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_all_over_print_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60" id="total_b2b_cost"><? echo number_format($total_b2b_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                          
                    <td width="60"><? echo number_format($total_knite_qty,2);//number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_knitting_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_fabric_dyeing_cost,2);//number_format($total_washing_qty,2); ?></td>
                    <td width="60"><? //echo number_format($total_fabric_dyeing_qty,2);//number_format($total_washing_qty,2); ?></td>
                    <td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_embroidery_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
                    <td width="60" align="right"><? //echo number_format($tot_fab_cost,2);total_special_amount ?></td>

					<td width="80" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
				
					<td width="60" align="right" id=""><? //echo number_format($total_commercial_cost,2); ?></td>
					<td width="80" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
                    <td width="100" id="total_service_cost"><? echo number_format($total_service_cost,2); ?></td>
                    <td width="60" align="right" id=""><? //echo number_format($total_test_cost_amount,2); ?></td>
					<td width="60" align="right" id="total_act_material_cost"><? echo number_format($total_act_material_cost,2); ?></td>
                    <td width="60" align="right"><? //echo number_format($tot_interest_cost,2);?></td>
					<td width="100" align="right" id="total_act_service_cost"><? echo number_format($total_act_service_cost,2); ?></td>
					<td width="60" align="right" id=""><? //echo number_format($total_material_service_cost,2); ?></td>
                    <td width="100" align="right" id="total_material_service_cost"><? echo number_format($total_material_service_cost,2); ?></td>

					<td width="60" align="right" id=""><? //echo number_format($total_currier_amount,2); ?></td>
					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2);?></td>
					<td width="60" align="right"><? //echo number_format($tot_interest_cost,2);?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2);?></td>

                    <td width="100" align="right" id="total_freight_cost"><? echo number_format($total_freight_cost,2); ?></td>


                    <td width="120" align="right" id="total_inspection"><? echo number_format($total_inspection,2); ?></td>
					<td width="100" align="right" id="total_certificate_cost"><? echo number_format($total_certificate_cost,2); ?></td>
                    <td width="100" align="right" id="total_common_oh"><? echo number_format($total_common_oh_amount,2); ?></td>
                    <td width="100" align="right" id="total_currier_cost"><? echo number_format($total_currier_cost,2); ?></td>
                   

                    <td width="120" align="right" id="total_foreign_cost"><? echo number_format($total_foreign_cost,2); ?></td>
					<td width="120" id="total_local_cost"><? echo number_format($total_local_cost,2); //total_FOB_val_amount?></td>
					<td width="110"><? echo number_format($total_forg_local_amount_new,2); //total_FOB_val_amount?></td>
					
                    <td width="100"><? echo number_format($total_cm_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2); ?></td>
                    <td width="100"><? echo number_format($total_cost,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2);?></td>
                    <td width="110"><? echo number_format($total_margin_new_amount,2); ?></td>
                    <td width="60"><? //echo number_format($total_cm_cost,2); ?></td>
                  
                    <td width="110"><? //echo number_format($total_cm_cost,2); ?></td>
                    <td width="110"><? //echo number_format($total_cm_cost,2); ?></td>
                   
                          
                    <td>&nbsp;</td>

				</tr>
			</table>
			<table>
				<tr>
					<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_cost_up,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');
					?>
					<input type="hidden" id="graph_data" value=""/>
				</tr>
			</table>
			<br>
			</fieldset>
			</div>
			<?
		}
	}
	
}
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();

}

// action for excel report generate
if($action=="report_generate_excel")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$excel_type = str_replace("'","",$excel_type);
	//echo $report_type.'=='.$excel_type;die;
	//echo $cbo_search_date;die;
	$company_name=str_replace("'","",$cbo_company_name);

	$team_member_arr=return_library_array( "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0",'id','team_leader_name');


	if($company_name){
		$company_con_name=" and company_name=$company_name";
		$company_con_id=" and company_id=$company_name";
		$company_con_name_a=" and a.company_name=$company_name";

	}
	else
	{
		$company_con_name="";
		$company_con_id="";
		$company_con_name_a="";
	}



	$season=str_replace("'","",$txt_season);
	//$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst",'job_no','costing_per');

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

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	//echo 'original date from'.$date_from;

	$start_month=date("Y-m",strtotime($date_from));
	$end_month=date("Y-m",strtotime($date_to));
	$date_to2=date("Y-m-d",strtotime($end_date));
	//echo 'original month'.$start_month;
	if($db_type==2)
	{
		$date_from=change_date_format($date_from,'yyyy-mm-dd','-',1);
		$date_to2=change_date_format($date_to2,'yyyy-mm-dd','-',1);
	}
	$total_months=datediff("m",$start_month,$end_month);

	//$last_month=date("Y-m", strtotime($end_month));
	$month_array=array();
	$st_month=$start_month;
	$month_array[]=$st_month;
	for($i=0; $i<$total_months;$i++)
	{
		$start_month=date("Y-m", strtotime("+1 Months", strtotime($start_month)));
		$month_array[]=$start_month;
	}

	$date_cond='';
	if(str_replace("'","",$cbo_search_date)==1)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}

		$order_by="order by b.pub_shipment_date, b.id";

	}
	else if(str_replace("'","",$cbo_search_date)==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.po_received_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}//applying_period_date,applying_period_to_date

		$order_by="order by b.po_received_date, b.id";
	}
	else if(str_replace("'","",$cbo_search_date)==3)// PO Insert Date
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				$date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$insert_date_cond="between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				$date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$insert_date_cond="between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}//applying_period_date,applying_period_to_date
	$order_by="order by b.insert_date, b.id";
	}
	else if(str_replace("'","",$cbo_search_date)==4)// Cancel Date
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				$date_cond=" and b.update_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				$date_cond=" and b.update_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}//applying_period_date,applying_period_to_date
	$order_by="order by b.update_date, b.id";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if($season=="") $season_cond=""; else $season_cond=" and a.season in('".implode("','",explode(",",$season))."')";

	$order_no=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and b.id in ($order_no)";
	else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";


	if(str_replace("'","",$cbo_approved_status)==1){
		$approvalCon="and c.approved=2 and c.ready_to_approved <>1 and c.partial_approved=2";
		}
	else if(str_replace("'","",$cbo_approved_status)==2){
		$approvalCon="and c.approved=2 and c.ready_to_approved =1 and c.partial_approved=2";
		}
	else if(str_replace("'","",$cbo_approved_status)==3){
		$approvalCon="and c.approved=2 and c.ready_to_approved =1 and c.partial_approved=1";
		}
	else if(str_replace("'","",$cbo_approved_status)==4){
		//$approvalCon="and c.approved=1 and c.ready_to_approved =1 and c.partial_approved=1";
		$approvalCon="and c.approved=1 and c.ready_to_approved =1";

		}
	else{$approvalCon="";}


	$app_status_arr=array(
		222=>'Not Submit',
		202=>'Not Submit',
		212=>'Ready For Approval',
		211=>'Partial Approved',
		111=>'Full Approved'
	);



	if($report_type==1)
	{
		if($template==1)
		{

			$style1="#E9F3FF";
			$style="#FFFFFF";
			//$nameArray=sql_select( "select cm_cost_method,id from  variable_order_tracking where company_name='$company_id' and variable_list=22 order by id" );
			$cm_cost_formula_sql=sql_select( "select id, cm_cost_method from variable_order_tracking where  variable_list=22 $company_con_name order by id");
			$cm_cost_formula=$cm_cost_predefined_method[$cm_cost_formula_sql[0][csf('cm_cost_method')]];

			$asking_profit_arr=array(); //$yarn_desc_array=array();
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $company_con_id");//$date_max_profit
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
				}
			}

			$cpm_arr=array();$cpm_date_arr=array(); //$yarn_desc_array=array();
			$finance_arr=array();
			$sql_cpm=sql_select("select applying_period_date, applying_period_to_date, cost_per_minute,interest_expense from lib_standard_cm_entry where 1=1 $company_con_id");//$date_max_profit
			foreach($sql_cpm as $cpMrow )
			{
				$applying_period_date=change_date_format($cpMrow[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($cpMrow[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$cpm_arr[$newdate]['cpm']=$cpMrow[csf('cost_per_minute')];
					$finance_arr[$newdate]['finPercent']=$cpMrow[csf('interest_expense')];
				}
			}
			//var_dump($cpm_date_arr);

			unset($sql_cpm);
			
			$lib_user_arr=return_library_array( "select id,user_full_name from user_passwd", "id","user_full_name" );


			$asking_profit=array();
		 	/*$sql_budget="select a.job_no_prefix_num, b.insert_date, b.update_date, a.job_no, a.buyer_name, a.style_ref_no, a.order_uom,a.set_smv,a.team_leader,b.excess_cut,b.plan_cut, b.is_confirmed, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $order_by";*/


			$sql_budget="select a.total_set_qnty,a.company_name,a.job_no_prefix_num, b.insert_date, b.update_date, a.job_no, a.buyer_name, a.style_ref_no, a.order_uom,a.set_smv,a.team_leader,b.excess_cut,b.plan_cut, b.is_confirmed, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price,c.approved,c.ready_to_approved,c.partial_approved from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.entry_from=158 $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $approvalCon $order_by";



			//echo $sql_budget;die;
			$result_sql_budget=sql_select($sql_budget);
			$tot_rows_budget=count($result_sql_budget);
			$budget_data_arr=array();$jobIdArr=array();
			foreach($result_sql_budget2 as $row )
			{
			
				$jobIdArr[$row[csf('id')]]=$row[csf('id')];
			}

			//echo $po_ids;die;
			$jobId_cond=where_con_using_array($jobIdArr,0,'job_id');
			$jobId_cond2=where_con_using_array($jobIdArr,0,'a.job_id');
			$jobId_cond3=where_con_using_array($jobIdArr,0,'b.job_id');
			
			$smv_eff_arr=array(); $costing_library=array();$preCostApprove_arr=array();
			$sql_smv_cpm=sql_select("select job_no,approved, approved_by,approved_date, costing_date, sew_smv, sew_effi_percent,exchange_rate from wo_pre_cost_mst where status_active=1 and is_deleted=0 $jobId_cond");
			foreach($sql_smv_cpm as $smv_cpm )
			{
				//$costing_library //$costing_library=return_library_array( "select job_no, costing_date from wo_pre_cost_mst", "job_no", "costing_date"  );
				$costing_library[$smv_cpm[csf('job_no')]]=$smv_cpm[csf('costing_date')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['smv']=$smv_cpm[csf('sew_smv')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['eff']=$smv_cpm[csf('sew_effi_percent')];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['exc_rate']=$smv_cpm[csf('exchange_rate')]; // new
				//echo change_date_format($smv_cpm[csf('costing_date')],'','',1); //exchange_rate //sew_effi_percent
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['cpm']=$cpm_arr[change_date_format($smv_cpm[csf('costing_date')],'','',1)]['cpm'];
				$smv_eff_arr[$smv_cpm[csf('job_no')]]['finPercent']=$finance_arr[change_date_format($smv_cpm[csf('costing_date')],'','',1)]['finPercent'];
				
				$preCostApprove_arr[$smv_cpm[csf('job_no')]]['apprv_by']=$smv_cpm[csf('approved_by')];
				$preCostApprove_arr[$smv_cpm[csf('job_no')]]['apprv_date']=$smv_cpm[csf('approved_date')];
			}
			//var_dump($smv_eff_arr);
			unset($sql_smv_cpm);

			$preCostingData_arr=array();
			$preCostData=sql_select("select job_no, margin_pcs_set from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond ");
			foreach($preCostData as $fabCostData)
			{
				$preCostingData_arr[$fabCostData[csf('job_no')]]['margin_pcs_set']=$fabCostData[csf('margin_pcs_set')];
			}
			unset($preCostData);


		  	
			$preCostAppr=sql_select("select a.job_no,max(a.update_date) as update_date,max(b.un_approved_date) as un_approved_date from wo_pre_cost_mst a,approval_history b where a.id=b.mst_id and b.entry_form=15 and a.status_active=1 and a.is_deleted=0 and a.approved in(2,3) and a.status_active=1 and a.is_deleted=0 $jobId_cond2 group by a.job_no  ");
			foreach($preCostAppr as $approveData)
			{
				//$approved=$approveData[csf('approved')];
				$preCostLastApprove_arr[$approveData[csf('job_no')]]['last_unapp_date']=$approveData[csf('un_approved_date')];
				
			}
			unset($preCostAppr);

			?>

			<div style="width:1800px;">
			<div style="width:900px;" align="left">

			</div>
			<br/>
			<?
			ob_start();
			if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else if(str_replace("'","",$cbo_search_date)==3) $caption="PO Insert Date";
					else $caption="Cancelled Date";
			?>
			<fieldset style="width:100%;" id="content_search_panel2">
            		<div>
                       <span style="font-size:20px; font-weight:bold;"><? echo $company_library[$company_name]; ?></span><br/>
                       <span style="font-size:16px; font-weight:bold;">Cost Breakdown Analysis<br/></span>
                       <strong>From  <? echo $caption.' &nbsp;'.$date_from;  ?>  To  <? echo $date_to; ?></strong>
                    </div>
			   <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit'];

			   ?>
			<table id="table_header_1" class="rpt_table" width="6600" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
                        <th width="110">Team Leader</th>
						<th width="100">Company</th>
                        <th width="70">Buyer</th>
                        <th width="110">Style</th>
						<th width="70">Job No</th>
                        <th width="110">Gmts Item Name</th>
                        <th width="110">SMV</th>
                        <th width="110">Efficiency</th>
						<th width="100">Order No</th>
						<th width="100">Order Status</th>
                        <th width="110">Costing Date</th>
                        <th width="70"><? echo $caption; ?></th>

                        <th width="90">Order Qty</th>
                        <th width="60">UOM</th>
                        <th width="50">Total SMV</th>

                        <th width="90">Order Qty (Pcs)</th>
                        <th width="110">Excess Cut %</th>
                        <th width="110">Plan Cut Qty (Pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">Order Value</th>

                        <th width="60">TTL Y/Qty</th>
                        <th width="60">TTL Knit Qty</th>
                        <th width="60">TTL Dye & Fin Qty</th>
                        <th width="60">TTL Wash Qty</th>
                        
                        <th width="60">TTL Print Qty</th>
                        <th width="60">Embroidery Qty</th>
                        <th width="60">Special Works Qty</th>
                        <th width="60">Fabric Cost Purchase</th>

                        <th width="100">Fabric Cost Production</th>
                        
                        <th width="100">Trim Cost</th>
						<th width="80">Printing Cost</th>
						<th width="85">Embroidery Cost</th>
						<th width="80">Special Works Cost</th>
						<th width="80">Wash Cost</th>
						<th width="80">Other</th>
                        <th width="120">Commercial Cost</th>
						<th width="100">Testing Cost</th>
						<th width="100">Freight Cost</th>
						<th width="120">Inspection Cost</th>
						<th width="100">Certificate Cost</th>
                        <th width="100">Finance Cost</th>
						<th width="100">Courier Cost</th>
                        <th width="60">Deffd. LC Cost</th>
                        <th width="60">Interest</th>
                        <th width="60">Income Tax</th>
                        <th width="110">Total Material Cost</th>
                        <th width="120">Foreign Comm</th>
						<th width="120">Local Comm</th>
                        <th width="110">Total Comm</th>
                        <th width="110">Net FOB</th>
                        <th width="100">CM Value (Contribution)</th>
						<th width="100" title="<?php echo $cm_cost_formula; ?>">CM Cost</th>
                        <th width="110">Total Margin</th>
                        <th width="110">Margin/Pcs</th>
                        <th width="60">Margin %</th>
                        <th width="110">CPM</th>
                        <th width="110">EPM</th>
                        <th width="110">Approved By</th>
                        <th width="110">Appv. Date & Time</th>
                        <th width="100">Last UN-approve date</th>
                        <th>Appv. Status</th>
					</tr>

				</thead>
			</table>
			<div style="width:6620px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="6600" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
			$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
	$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;



			 $condition= new condition();
			 if(str_replace("'","",$cbo_company_name)>0){
				 $condition->company_name("=$cbo_company_name");
			 }

			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num("=$txt_job_no");
			 }
			  $all_job_id=implode(",",$jobIdArr);
				// echo  $all_job_id.'d';
				if($all_job_id!='')
				{
				  $condition->jobid_in($all_job_id);
				}
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
				 //and b.po_received_date between '$start_date' and '$end_date'
				// echo 'FFGG';
			 }
			  if(str_replace("'","",$cbo_search_date) ==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
					 $condition->insert_date("$insert_date_cond");
			 }


			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no");
			 }

			 /*if(str_replace("'","",$txt_season)!='')
			 {
				$condition->season("in('".implode("','",explode(",",$season))."')");
				//$condition->season("in($txt_season)");
			 }*/
			 $condition->init();

		     $costing_per_arr=$condition->getCostingPerArr();


			$fabric= new fabric($condition);
			// echo $fabric->getQuery(); die;
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_fab_source_costing_arr=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();

			//print_r($costing_per_arr);
			//$fabric->unsetDataArray();

			$yarn= new yarn($condition);

			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			//print_r($yarn_costing_arr);

			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();
			$yarn= new yarn($condition);
			$yarn_required=$yarn->getOrderWiseYarnQtyArray();


			//$yarn->unsetDataArray();
			//echo $yarn->getQuery(); die;
			$conversion= new conversion($condition);
			//echo $conversion->getQuery();
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			$conversion_costing_po_arr=$conversion->getAmountArray_by_order();

			$conversion_qty= new conversion($condition);
			$conversion_qty_arr_process = $conversion_qty->getQtyArray_by_orderAndProcess();

			//$conversion->unsetDataArray();

			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			//$trims->unsetDataArray();

			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$emblishment_qty_arr_name=$emblishment->getQtyArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();


			 //print_r($other_costing_arr);die;

			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
			$emblishment_qty_arr_name_wash=$wash->getQtyArray_by_orderAndEmbname();

			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149,158);
			//$fabric_dyeingCost_arr=array(25,26,31,39,60,61,62,101,133,137,138,139,146,147,149);
			//$aop_cost_arr=array(35,36,37);
			$aop_cost_arr=array(35,36,37,40);
			//$fab_finish_cost_arr=array(33,34,65,66,67,68,69,70,71,72,73,75,76,77,88,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,100,125,127,128,129,132,144);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			//$washing_cost_arr=array(64,82,89);
			$washing_cost_arr=array(140,142,148,64);
			//$washing_qty_arr=array(140,142);
			$washing_qty_arr=array(140,142,148,64);



			//**********************************************************************************************
		    if(str_replace("'","",$start_date)!="" && str_replace("'","",$end_date)!="")
			{
				$date_condition=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			}
			else
			{
				$date_condition="";
			}


			$fabric_cost_result=sql_select( "select a.total_set_qnty,c.job_no,c.fabric_cost,b.id as po_id,c.deffdlc_cost,c.incometax_cost,c.interest_cost from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_dtls c
			where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no $company_con_name_a  $date_condition $jobId_cond3" );

			foreach($fabric_cost_result as $row)
			{
				$fabric_cost_pcs_arr[$row[csf('po_id')]]=($row[csf('fabric_cost')]/12)/$row[csf('total_set_qnty')];

				$deffdlc_cost_arr[$row[csf('po_id')]]=($row[csf('deffdlc_cost')]/12)/$row[csf('total_set_qnty')];
				$interest_cost_arr[$row[csf('po_id')]]=($row[csf('interest_cost')]/12)/$row[csf('total_set_qnty')];
				$incometax_cost_arr[$row[csf('po_id')]]=($row[csf('incometax_cost')]/12)/$row[csf('total_set_qnty')];

			}


			//*************************************************************************************

		$tot_print_qty=$tot_embroidery_qty=$tot_special_qty=$tot_fab_purchase=0;
			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				else if(str_replace("'","",$cbo_search_date)==4)
				{
					$update_date=explode(" ",$row[csf('update_date')]);
					$ship_po_recv_date=change_date_format($update_date[0]);
				}
				$dzn_qnty=$costing_per_arr[$row[csf('job_no')]];
				/*$dzn_qnty=0;
				$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;*/

				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$order_qty=$row[csf('po_quantity')];
				$order_uom=$row[csf('order_uom')];


				$yarn_req=$yarn_required[$row[csf('po_id')]];

				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];

				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];

				//$costing_library=return_library_array( "select job_no, costing_date from wo_pre_cost_mst", "job_no", "costing_date"  );

				$costing_date=$costing_library[$row[csf('job_no')]];
				//$total_order_amount+=$order_value;
				$total_plancut_amount+=$plancut_value;
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					 <td width="40"><? echo $i; ?></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $team_member_arr[$row[csf('team_leader')]]; ?></div></td>

                     <td width="100"><p><? echo $company_library[$row[csf('company_name')]]; ?></p></td>
                     <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('style_ref_no')]; ?></div></td>
					 <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                     <td width="110"><div style="word-wrap:break-word; width:110px"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110" align="right"><p><? echo $row[csf('set_smv')]; ?></p></td>
                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $smv_eff_arr[$row[csf('job_no')]]['eff']; ?></div></td>
					 <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('po_number')]; ?></div></td>
					<td width="100"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                    <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo $costing_library[$row[csf('job_no')]]; ?></div></td>
                    <td width="70"><div style="word-wrap:break-word; width:70px"><? echo  date("d-M-Y", strtotime($ship_po_recv_date)); ?></div></td>


                    <td width="90" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                    <td width="60" align="center"><p><? echo $unit_of_measurement[$order_uom]; ?></p></td>
                    <td width="50" align="right"><p><? echo number_format($order_qty*$row[csf('set_smv')],2); ?></p></td>

                    <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                    <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('excess_cut')]; ?></div></td>
                    <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('plan_cut')]; ?></div></td>
                    <td width="70" align="right"><p><? echo number_format($row[csf('unit_price')],2); ?></p></td>


                    <td width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>


                    <?
						$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];
						$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
						$avg_rate=$yarn_costing/$yarn_req_qty_arr[$row[csf('po_id')]];
						$yarn_cost_percent=($yarn_costing/$order_value)*100;

						$fab_purchase_knit=array_sum($fabric_fab_source_costing_arr['knit']['grey'][$row[csf('po_id')]][1]);
						$fab_purchase_woven=array_sum($fabric_fab_source_costing_arr['woven']['grey'][$row[csf('po_id')]][1]);
						$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;//production
						
						$fab_purchase_cost_knit=array_sum($fabric_fab_source_costing_arr['knit']['grey'][$row[csf('po_id')]][2]);
						$fab_purchase_cost_woven=array_sum($fabric_fab_source_costing_arr['woven']['grey'][$row[csf('po_id')]][2]);
						$fab_purchase_cost=$fab_purchase_cost_knit+$fab_purchase_cost_woven;

						$conv_cost_poWise=array_sum($conversion_costing_po_arr[$row[csf('po_id')]]);
						$tot_fabric_cost=$yarn_costing+$fab_purchase+$conv_cost_poWise;


						$knit_cost=0;$knite_qty=0;
						foreach($knit_cost_arr as $process_id)
						{
							$knit_cost_chk=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);
							if($knit_cost_chk>0)
							{
							$knit_cost+=$knit_cost_chk;
							$knite_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$process_id]);
							}
							 
						}

						$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;

						$fabric_dyeing_cost=0;$fabric_dyeing_qty=0;
						foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
							$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);
							$fabric_dyeing_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$fab_process_id]);
						}

						$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
						$heat_setting_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][33]);

						$fabric_finish=0;
						foreach($fab_finish_cost_arr as $fin_process_id)
						{
							$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fin_process_id]);
						}

						$all_over_cost=0;
						foreach($aop_cost_arr as $aop_process_id)
						{
							$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);
						}

						$washing_cost=0;
						foreach($washing_cost_arr as $w_process_id)
						{
							$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);
						}

						/*$washing_qty=0;
						foreach($washing_qty_arr as $w_process_id)
						{
							$washing_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$w_process_id]);
						}

						$washing_qty=0;*/
						//emblishment_qty_arr_name
						$print_qty=$emblishment_qty_arr_name[$row[csf('po_id')]][1];
						$emb_washing_qty=$emblishment_qty_arr_name_wash[$row[csf('po_id')]][3];
						$embroidery_qty=$emblishment_qty_arr_name[$row[csf('po_id')]][2];
						$special_qty=$emblishment_qty_arr_name[$row[csf('po_id')]][4];
					 
						
						$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
						$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
						$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
						$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
						$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
						$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
						$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
						$local=$commission_costing_arr[$row[csf('po_id')]][2];
						$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
						$freight_cost=$other_costing_arr[$row[csf('po_id')]]['freight'];
						$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
						$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
						$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
						$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];

						$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;

						$finance_chrg=$order_value*$smv_eff_arr[$row[csf('job_no')]]['finPercent']/100;

						//------
						$dfc_cost=$other_costing_arr[$row[csf('po_id')]]['deffdlc_cost'];//$deffdlc_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;
						$interest_cost=$interest_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;
						$incometax_cost=$incometax_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;
						/*$total_cost_new=$yarn_costing+$fab_purchase+$knit_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$washing_cost+$all_over_cost+$trim_amount+$print_amount+$embroidery_amount+$special_amount+$wash_cost+$other_amount+$commercial_cost+$test_cost+$freight_cost+$inspection+$certificate_cost+$finance_chrg+$currier_cost;*/


						$total_cost_new=($tot_fabric_cost+$fab_purchase_cost)+$trim_amount+$print_amount+$embroidery_amount+$special_amount+$wash_cost+$other_amount+$commercial_cost+$test_cost+$freight_cost+$inspection+$certificate_cost+$finance_chrg+$currier_cost+$dfc_cost+$interest_cost+$incometax_cost;


						$total_material_cost=$total_cost_new;
						$others_cost_value = $total_cost -($cm_cost+$freight_cost+$commercial_cost+($foreign+$local));
						$net_order_val=$order_value-(($foreign+$local)+$commercial_cost+$freight_cost);
						$cm_value=$net_order_val-$others_cost_value;

						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100;
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;

						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
						if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
						if($commercial_cost<=0) $color_com="red"; else $color_com="";

					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;

					$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_forg_local_amount+=$tot_forg_local;
					$total_FOB_amount+=$tot_netFOB;

					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;
					$total_finance_chrg_amount+=$finance_chrg;

					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_meterial_amount+=$total_material_cost;
					$total_cm_new_amount+=$tot_cmValue;
					$total_cm_amount+=$cm_cost;
					$total_margin_amount+=$tot_margin;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];

					if($trim_amount<=0) $color_trim="red"; else $color_trim="";
					if($cm_cost<=0) $color="red"; else $color="";
					$smv=0; $eff=0; $cpm=0; $cost_dzn_cm_title=0;
					$smv=$smv_eff_arr[$row[csf('job_no')]]['smv'];
					$eff=$smv_eff_arr[$row[csf('job_no')]]['eff'];
					$cpm=$smv_eff_arr[$row[csf('job_no')]]['cpm'];
					$cost_dzn_cm_title='Swe. SMV: '.$smv.'; Swe. EFF: '.$eff.'; CPM: '.$cpm;
						//echo $fabric_cost_pcs_arr[$row[csf('po_id')]].'='.$plan_cut_qnty;
					
					
					
					?>


                    <td width="60" align="right"><p><? echo number_format($yarn_required[$row[csf('po_id')]],2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($knite_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fabric_dyeing_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($emb_washing_qty,2); ?></p></td>
                    
                    <td width="60" align="right"><p><? echo number_format($print_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($embroidery_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($special_qty,2); ?></p></td>
                    <td width="60" align="right"><p><? echo number_format($fab_purchase_cost,2); ?></p></td>


                    <td width="100" align="right"><p><? /*$fab_cost=$fabric_cost_pcs_arr[$row[csf('po_id')]]*$plan_cut_qnty;*/
					$fab_cost=$tot_fabric_cost;
					echo number_format($fab_cost,2);$tot_fab_cost+=$fab_cost; ?></p></td>


					 <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><? echo number_format($trim_amount,2);?></td>
					 <td width="80" align="right"><? echo number_format($print_amount,2); ?></td>
					 <td width="85" align="right"><? echo number_format($embroidery_amount,2); ?></td>
					 <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
					 <td width="80" align="right"><? echo number_format($wash_cost,2); ?></td>
					 <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
					 <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
					 <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
					 <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($inspection,2);?></td>
					 <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($finance_chrg,2);  ?></td>
					 <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>

					 <td width="60" align="right"><? echo number_format($dfc_cost,2);$tot_dfc_cost+=$dfc_cost; ?></td>
					 <td width="60" align="right"><? echo number_format($interest_cost,2);$tot_interest_cost+=$interest_cost; ?></td>
					 <td width="60" align="right"><? echo number_format($incometax_cost,2);$tot_incometax_cost+=$incometax_cost; ?></td>

                     <td width="110" align="right"><? echo number_format($total_material_cost,2); ?></td>


                     <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
					 <td width="120" align="right"><? echo number_format($local,2) ?></td>
                     <td width="110" align="right"><? $tot_forg_local=$foreign+$local; echo number_format($tot_forg_local,2);  ?></td>
                     <td width="110" align="right"><? $tot_netFOB=$order_value-$tot_forg_local; echo number_format($tot_netFOB,2); //.'_'.$row[csf('avg_unit_price')] ?></td>



                     <td width="100" align="right"><? $tot_cmValue=$tot_netFOB-$total_material_cost; echo number_format($tot_cmValue,2);       //echo number_format($order_value-$total_material_cost,2);?></td>
					 <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     <td width="110" align="right"><? $tot_margin=$tot_cmValue-$cm_cost; echo  number_format($tot_cmValue-$cm_cost,2); ?></td>
                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo number_format($tot_margin/$order_qty_pcs,2); ?></div></td>

                    <td width="60"><?php echo number_format(($tot_margin / $order_value) * 100, 2); ?></td>

                    <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? $cpmVal=$smv_eff_arr[$row[csf('job_no')]]['cpm']/$smv_eff_arr[$row[csf('job_no')]]['exc_rate']; echo  number_format($cpmVal*100/$smv_eff_arr[$row[csf('job_no')]]['eff'],3); ?></div></td>

                     <td width="110" align="right"><div style="word-wrap:break-word; width:110px"><? echo number_format(($tot_cmValue/$order_qty_pcs)/($row[csf('set_smv')]/$row[csf('total_set_qnty')]),3); ?></div></td>

                      <td width="110" align="center"><div style="word-wrap:break-word; width:110px"><? echo $lib_user_arr[$preCostApprove_arr[$row[csf('job_no')]]['apprv_by']]; ?></div></td>
                      <td width="110" align="center"><div style="word-wrap:break-word;"><? echo $preCostApprove_arr[$row[csf('job_no')]]['apprv_date']; ?></div></td>
                      <td width="100" align="center"><div style="word-wrap:break-word;"><? echo $preCostLastApprove_arr[$row[csf('job_no')]]['last_unapp_date']; ?></div></td>
                      <td><? echo $app_status_arr[$row[csf('approved')].$row[csf('ready_to_approved')].$row[csf('partial_approved')]];?></td>
					<?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";
						else $color_pl="";
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
						$expect_variance=$total_profit-$expected_profit_per;

					?>
				  </tr>
				<?
				$total_order_qty_pcs+=$order_qty_pcs;
				$total_plan_cut_qnty_pcs+=$plan_cut_qnty;
				$total_order_qty+=$row[csf('po_quantity')];
				$total_order_amount+=$order_value;
				$total_yarn_required+=$yarn_req;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$all_over_print_cost+=$all_over_cost;
				$total_trim_cost+=$trim_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;

				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_cm_value+=$cm_value;
				$total_cm_new_value+=$tot_cmValue;
				$total_forg_local_amount_new+=$tot_forg_local;
				$total_FOB_val_amount+=$tot_netFOB;

				$total_margin_new_amount+=$tot_margin;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_finance_chrg_cost+=$finance_chrg;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;

				$total_yarn_qty+=$yarn_required[$row[csf('po_id')]];
				$total_knite_qty+=$knite_qty;
				$total_fabric_dyeing_qty+=$fabric_dyeing_qty;
				$total_washing_qty+=$emb_washing_qty;
				
				$tot_print_qty+=$print_qty;
				$tot_embroidery_qty+=$embroidery_qty;
				$tot_special_qty+=$special_qty;
				$tot_fab_purchase+=$fab_purchase_cost;

				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			?>
			</table>
			</div>
			<table class="tbl_bottom" width="6600" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
                    <td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
                    <td width="110">&nbsp;</td>
					<td width="70"><strong>Total:</strong></td>

					<td width="90">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="50">&nbsp;</td>

                    <td width="90" align="right" id="total_order_qnty_pcs"><? echo number_format($total_order_qty_pcs,2); ?></td>
					<td width="110">&nbsp;</td>
					<td width="110"><? echo number_format($total_plan_cut_qnty_pcs,2); ?></td>
					<td width="70">&nbsp;</td>

                    <td width="100" align="right" id="total_order_amount"><? echo number_format($total_order_amount,2); ?></td>

                    <td width="60"><? echo number_format($total_yarn_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_knite_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_fabric_dyeing_qty,2); ?></td>
                    <td width="60"><? echo number_format($total_washing_qty,2); ?></td>
                    <td width="60"><? echo number_format($tot_print_qty,2); ?></td>
                    <td width="60"><? echo number_format($tot_embroidery_qty,2); ?></td>
                    <td width="60"><? echo number_format($tot_special_qty,2); ?></td>
                    <td width="60"><? echo number_format($tot_fab_purchase,2); ?></td>



                    <td width="100" align="right"><? echo number_format($tot_fab_cost,2);?></td>

					<td width="100" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
					<td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
					<td width="80" align="right" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2); ?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2); ?></td>
					<td width="100" align="right" id="total_freight_amount"><? echo number_format($total_freight_amount,2); ?></td>
					<td width="120" align="right" id="total_inspection_amount"><? echo number_format($total_inspection_amount,2); ?></td>
					<td width="100" align="right" id="total_certificate_amount"><? echo number_format($total_certificate_amount,2); ?></td>
                    <td width="100" align="right"><? echo number_format($total_finance_chrg_amount,2); ?></td>

					<td width="100" align="right" id="total_currier_amount"><? echo number_format($total_currier_amount,2); ?></td>
					<td width="60" align="right"><? echo number_format($tot_dfc_cost,2);?></td>
					<td width="60" align="right"><? echo number_format($tot_interest_cost,2);?></td>
					<td width="60" align="right"><? echo number_format($tot_incometax_cost,2);?></td>

                    <td width="110" align="right" id="total_tot_cost"><? echo number_format($total_meterial_amount,2); ?></td>


                    <td width="120" align="right" id="total_foreign_amount"><? echo number_format($total_foreign_amount,2); ?></td>
					<td width="120" align="right" id="total_local_amount"><? echo number_format($total_local_amount,2); ?></td>
                    <td width="110" align="right"><? echo number_format($total_forg_local_amount_new,2); ?></td>
                    <td width="110" align="right"><? echo number_format($total_FOB_val_amount,2); ?></td>

					<td width="100" align="right" id="total_cm_value"><? echo number_format($total_cm_new_value,2); ?></td>
					<td width="100" align="right" id="total_cm_amount"><? echo number_format($total_cm_amount,2); ?></td>
					<td width="110"><? echo number_format($total_margin_new_amount,2); ?></td>
					<td width="110">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td>&nbsp;</td>

				</tr>
			</table>
			<table>
				<tr>
					<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_cost_up,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');

					//$chart_data_qnty="Fabric Cost;".$total_fab_cost."\nTrimCost;".$total_trim_cost."\nEmbelishment Cost;".$total_embelishment_cost."\nCommercial Cost;".$total_commercial_cost."\nCommission Cost;".$total_commssion."\nTesting Cost;".$total_testing_cost."\nFreightCost;".$total_freight_cost."\nCM Cost;".$total_cm_cost."\nInspection Cost;".$total_inspection."\nCertificate Cost;".$total_certificate_cost."\nCommn OH Cost;".$total_common_oh."\nCurrier Cost;".$total_currier_cost."\n Profit/Loss;".$total_fabric_profit_up."\n";
					?>
					<input type="hidden" id="graph_data" value="<? //echo substr($chart_data_qnty,0,-1); ?>"/>
				</tr>
			</table>
			<br>
			</fieldset>
			</div>
			<?
		}
	} //1st button end
	else
	{


			$style1="#E9F3FF";
			$style="#FFFFFF";

			$fab_precost_arr=array();$commission_array=array();$knit_arr=array(); $fabriccostArray=array(); $fab_emb=array();$fabric_data_Array=array();$asking_profit_arr=array(); $yarncostArray=array(); $yarn_desc_array=array();  $costing_date_arr=array();

			

			/*$costing_date_sql=sql_select("select job_no, costing_date from wo_pre_cost_mst where status_active=1 and is_deleted=0");
			foreach($costing_date_sql as $row )
			{
				$costing_date_arr[$row[csf('job_no')]]=$row[csf('costing_date')];
			}*/

			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$company_name");//$date_max_profit
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					//$newDate=add_date(str_replace("'","",$applying_period_date),$j);
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);

					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
					//$asking_profit_arr[$newDate]['max_profit']=$ask_row[csf('max_profit')];
				}
			}
			//var_dump($asking_profit_arr);die;
			
			

			$sql_budget2="select a.id,a.job_no_prefix_num, b.insert_date, a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_status and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $order_by";
			$result_sql_budget2=sql_select($sql_budget2); 
			$tot_rows_budget=count($result_sql_budget2); 
			$budget_data_arr=array();$jobIdArr=array();
			foreach($result_sql_budget2 as $row )
			{
				$jobIdArr[$row[csf('id')]]=$row[csf('id')];
			}
			$jobId_cond=where_con_using_array($jobIdArr,0,'job_id');
			$jobId_cond2=where_con_using_array($jobIdArr,0,'b.job_id');
			
			$yarncostDataArray=sql_select("select job_no, count_id, type_id, sum(cons_qnty) as cons_qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond group by job_no, count_id, type_id");
			foreach($yarncostDataArray as $yarnRow)
			{
				$yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
			}
			$costing_date_sql=sql_select("select job_no, costing_date from wo_pre_cost_mst where status_active=1 and is_deleted=0 $jobId_cond");
			foreach($costing_date_sql as $row )
			{
				$cost_date=change_date_format($row[csf('costing_date')],'','',1);
				//echo $cost_date=change_date_format($row[csf('costing_date')]);
				$costing_date_arr[$row[csf('job_no')]]['ask']=$asking_profit_arr[$cost_date]['asking_profit'];
				$costing_date_arr[$row[csf('job_no')]]['max']=$asking_profit_arr[$cost_date]['max_profit'];
			}
			//var_dump($costing_date_arr);die;

			$fab_arr=sql_select("select a.job_no, a.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, sum(a.requirment) as requirment, sum(a.pcs) as pcs from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 $jobId_cond2 group by a.po_break_down_id, a.pre_cost_fabric_cost_dtls_id, a.job_no");
			foreach($fab_arr as $row_pre)
			{
				$fab_precost_arr[$row_pre[csf('job_no')]][$row_pre[csf('po_break_down_id')]].=$row_pre[csf('requirment')]."**".$row_pre[csf('pcs')].",";
			}

			$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobId_cond2");
			foreach($fabricDataArray as $fabricRow)
			{
				$fabric_data_Array[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('yarn_cons_qnty')]."**".$fabricRow[csf('yarn_amount')].",";
			}//Pre cost end

			$data_array_emb=("select  job_no,
			sum(CASE WHEN emb_name=1 THEN amount END) AS print_amount,
			sum(CASE WHEN emb_name=2 THEN amount END) AS embroidery_amount,
			sum(CASE WHEN emb_name=3 THEN amount END) AS wash_amount,
			sum(CASE WHEN emb_name=4 THEN amount END) AS special_amount,
			sum(CASE WHEN emb_name=5 THEN amount END) AS other_amount
			from  wo_pre_cost_embe_cost_dtls where  status_active=1 and  is_deleted=0  group by job_no");
			$embl_array=sql_select($data_array_emb);
			foreach($embl_array as $row_emb)
			{
				$fab_emb[$row_emb[csf('job_no')]]['print']=$row_emb[csf('print_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['embroidery']=$row_emb[csf('embroidery_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['special']=$row_emb[csf('special_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['other']=$row_emb[csf('other_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['wash']=$row_emb[csf('wash_amount')];
			}

			$fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost,certificate_pre_cost,currier_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0  $jobId_cond");
			foreach($fabriccostDataArray as $fabRow)
			{
				$fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
				$fabriccostArray[$fabRow[csf('job_no')]]['trims_cost']=$fabRow[csf('trims_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
				$fabriccostArray[$fabRow[csf('job_no')]]['common_oh']=$fabRow[csf('common_oh')];
				$fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
				$fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
				$fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
				$fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['certificate_pre_cost']=$fabRow[csf('certificate_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['c_cost']=$fabRow[csf('cm_cost')];
			}

			$knit_data=sql_select("select job_no,
			sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
			sum(CASE WHEN cons_process=2 THEN amount END) AS weaving_charge,
			sum(CASE WHEN cons_process=3 THEN amount END) AS knit_charge_collar_cuff,
			sum(CASE WHEN cons_process=4 THEN amount END) AS knit_charge_feeder_stripe,
			sum(CASE WHEN cons_process in(64,82,89) THEN amount END) AS washing_cost,
			sum(CASE WHEN cons_process in(35,36,37) THEN amount END) AS all_over_cost,
			sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dyeing_cost,
			sum(CASE WHEN cons_process=33 THEN amount END) AS heat_setting_cost,
			sum(CASE WHEN cons_process in(25,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79) THEN amount END) AS fabric_dyeing_cost,
			sum(CASE WHEN cons_process in(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129) THEN amount END) AS fabric_finish_cost
			from wo_pre_cost_fab_conv_cost_dtls where  status_active=1 and is_deleted=0 $jobId_cond group by job_no");
			foreach($knit_data as $row_knit)
			{
				$knit_arr[$row_knit[csf('job_no')]]['knit']=$row_knit[csf('knit_charge')];
				$knit_arr[$row_knit[csf('job_no')]]['weaving']=$row_knit[csf('weaving_charge')];
				$knit_arr[$row_knit[csf('job_no')]]['collar_cuff']=$row_knit[csf('knit_charge_collar_cuff')];
				$knit_arr[$row_knit[csf('job_no')]]['feeder_stripe']=$row_knit[csf('knit_charge_feeder_stripe')];
				$knit_arr[$row_knit[csf('job_no')]]['washing']=$row_knit[csf('washing_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['all_over']=$row_knit[csf('all_over_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['fabric_dyeing']=$row_knit[csf('fabric_dyeing_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['yarn_dyeing']=$row_knit[csf('yarn_dyeing_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['heat']=$row_knit[csf('heat_setting_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['fabric_finish']=$row_knit[csf('fabric_finish_cost')];
			}

			$data_array=sql_select("select  job_no,
			sum(CASE WHEN particulars_id=1 THEN commission_amount END) AS foreign_comm,
			sum(CASE WHEN particulars_id=2 THEN commission_amount END) AS local_comm
			from  wo_pre_cost_commiss_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond group by job_no");// quotation_id='$data'
			foreach($data_array as $row_fl )
			{
				$commission_array[$row_fl[csf('job_no')]]['foreign']=$row_fl[csf('foreign_comm')];
				$commission_array[$row_fl[csf('job_no')]]['local']=$row_fl[csf('local_comm')];
			}
			//Trim cost from BOM calculation
			$trim_qty_arr=array();$trim_po_country_arr=array();$trim_poqty_country_arr=array();
			$dtls_data=sql_select("select po_break_down_id,country_id,wo_pre_cost_trim_cost_dtls_id from wo_pre_cost_trim_co_cons_dtls where   cons !=0 $jobId_cond");
			foreach($dtls_data as $row)
			{
			 $trim_po_country_arr[$row[csf('wo_pre_cost_trim_cost_dtls_id')]].=$row[csf('po_break_down_id')].'**'.$row[csf('country_id')].',';
			}//var_dump($trim_po_country_arr);
			$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls where status_active=1  $jobId_cond ";//where job_no=".$$all_job_id."
			//echo $all_job_id;die;
			$sql_po_qty=sql_select("select b.id,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id    and a.status_active=1 and b.status_active=1 and c.status_active=1 $jobId_cond2 group by b.id,a.total_set_qnty");
			foreach($sql_po_qty as $row)
			{

			$trim_poqty_country_arr[$row[csf('id')]]=$row[csf('order_quantity_set')];

			}
			$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst where 1=1 $jobId_cond ", "job_no", "costing_per");

			$data_array_trim=sql_select($sql_trim);
			$total_trims_cost=0;
            foreach( $data_array_trim as $row_trim )
            {

			   $trim_data=explode(",",$trim_po_country_arr[$row_trim[csf('id')]]);
			   foreach($trim_data as $val )
			   {
				 $exp_data=explode("**",$val);
				 $po_id=$exp_data[0];
				 $country_id=$exp_data[1];
				 //echo $po_id.'='. $country_id;
				   if($country_id==0)
					 {
				$po_qty=$trim_poqty_country_arr[$po_id];
					 }
					 else
					 {
				$po_qty=$trim_poqty_country_arr[$po_id];
					 }
					// echo $po_qty.'dd';
					 $costing_per_qty=0;
					$costing_per=$costing_per_arr[$row_trim[csf('job_no')]];
						if($costing_per==1)
						{
						$costing_per_qty=12	;
						}
						if($costing_per==2)
						{
						$costing_per_qty=1;
						}
						if($costing_per==3)
						{
						$costing_per_qty=24	;
						}
						if($costing_per==4)
						{
						$costing_per_qty=36	;
						}
						if($costing_per==5)
						{
						$costing_per_qty=48	;
						}

					 $trim_qty_arr[$po_id]+=$row_trim[csf('amount')]/$costing_per_qty*$po_qty;
			   }
			} //Trims End

			?>
			<script>
			var order_amt=document.getElementById('total_order_amount2').innerHTML.replace(/,/g ,'');
			document.getElementById('yarn_cost').innerHTML=document.getElementById('total_yarn_cost2').innerHTML;
			document.getElementById('yarn_cost_per').innerHTML=document.getElementById('total_yarn_cost_per').innerHTML + ' %';
			document.getElementById('purchase_cost').innerHTML=document.getElementById('total_purchase_cost').innerHTML;
			document.getElementById('purchase_cost_per').innerHTML=number_format_common((document.getElementById('total_purchase_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('knit_cost').innerHTML=document.getElementById('total_knitting_cost').innerHTML;
			document.getElementById('knit_cost_per').innerHTML=number_format_common((document.getElementById('total_knitting_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('ydyeing_cost').innerHTML=document.getElementById('total_yarn_dyeing_cost').innerHTML;
			document.getElementById('ydyeing_cost_per').innerHTML=number_format_common((document.getElementById('total_yarn_dyeing_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('aop_cost').innerHTML=document.getElementById('all_over_print_cost').innerHTML;
			document.getElementById('aop_cost_per').innerHTML=number_format_common((document.getElementById('all_over_print_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			var dyefin_val=(parseFloat(document.getElementById('total_fabric_dyeing_cost4').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_finishing_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_heat_setting_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_washing_cost').innerHTML.replace(',','')));
			document.getElementById('dyefin_cost').innerHTML=number_format_common(dyefin_val,2);
			document.getElementById('dyefin_cost_per').innerHTML=number_format_common((dyefin_val/order_amt)*100,2) + ' %';
			document.getElementById('trim_cost').innerHTML=document.getElementById('total_trim_cost').innerHTML;
			document.getElementById('trim_cost_per').innerHTML=number_format_common((document.getElementById('total_trim_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			var embelishment_val=(parseFloat(document.getElementById('total_print_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_embroidery_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_special_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_wash_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_other_amount').innerHTML.replace(',','')));
			document.getElementById('embelishment_cost').innerHTML=number_format_common(embelishment_val,2);
			document.getElementById('embelishment_cost_per').innerHTML=number_format_common((embelishment_val/order_amt)*100,2) + ' %';
			document.getElementById('commercial_cost').innerHTML=document.getElementById('total_commercial_cost').innerHTML;
			document.getElementById('commercial_cost_per').innerHTML=number_format_common((document.getElementById('total_commercial_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			var comission_val=(parseFloat(document.getElementById('total_foreign_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_local_amount').innerHTML.replace(',','')));
			document.getElementById('commission_cost').innerHTML=number_format_common(comission_val,2);
			document.getElementById('commission_cost_per').innerHTML=number_format_common((comission_val/order_amt)*100,2) + ' %';
			document.getElementById('testing_cost').innerHTML=document.getElementById('total_test_cost_amount').innerHTML;
			document.getElementById('testing_cost_per').innerHTML=number_format_common((document.getElementById('total_test_cost_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('freight_cost').innerHTML=document.getElementById('total_freight_amount').innerHTML;
			document.getElementById('freight_cost_per').innerHTML=number_format_common((document.getElementById('total_freight_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('inspection_cost').innerHTML=document.getElementById('total_inspection_amount').innerHTML;
			document.getElementById('inspection_cost_per').innerHTML=number_format_common((document.getElementById('total_inspection_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('certificate_cost').innerHTML=document.getElementById('total_certificate_amount').innerHTML;
			document.getElementById('certificate_cost_percent').innerHTML=number_format_common((document.getElementById('total_certificate_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			document.getElementById('commn_cost').innerHTML=document.getElementById('total_common_oh_amount').innerHTML;
			document.getElementById('commn_cost_per').innerHTML=number_format_common((document.getElementById('total_common_oh_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			document.getElementById('courier_cost').innerHTML=document.getElementById('total_currier_amount').innerHTML;
			document.getElementById('courier_cost_per').innerHTML=number_format_common((document.getElementById('total_currier_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('cm_cost').innerHTML=document.getElementById('total_cm_amount').innerHTML;
			document.getElementById('cm_cost_per').innerHTML=number_format_common((document.getElementById('total_cm_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			document.getElementById('cost_cost').innerHTML=document.getElementById('total_tot_cost').innerHTML;
			document.getElementById('cost_cost_per').innerHTML=number_format_common((document.getElementById('total_tot_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			document.getElementById('order_id').innerHTML=number_format_common(order_amt,2);
			document.getElementById('order_percent').innerHTML=number_format_common((order_amt/order_amt)*100,2);

			//document.getElementById('fab_profit_id').innerHTML=document.getElementById('total_fabric_profit').innerHTML;
			//document.getElementById('profit_fab_percentage').innerHTML=number_format_common((document.getElementById('total_fabric_profit').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			//document.getElementById('expected_id').innerHTML=document.getElementById('total_expected_profit').innerHTML;
			//document.getElementById('profit_expt_fab_percentage').innerHTML=number_format_common((document.getElementById('total_expected_profit').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			//document.getElementById('expt_p_variance_id').innerHTML=document.getElementById('total_expected_variance').innerHTML;
			//document.getElementById('expt_p_percent').innerHTML=number_format_common((document.getElementById('total_expected_variance').innerHTML.replace(',','')/order_amt)*100,2) + ' %';

			var matr_ser_cost=(parseFloat(document.getElementById('yarn_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('purchase_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('knit_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('ydyeing_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('aop_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('dyefin_cost').innerHTML.replace(',','')));
			document.getElementById('tot_matr_ser_cost').innerHTML=number_format_common(matr_ser_cost,2);
			document.getElementById('tot_matr_ser_per').innerHTML=number_format_common((matr_ser_cost/order_amt)*100,2) + ' %';
			//var aa=order_amt;
			//alert(aa);

				function toggle()
				{
					var ele = document.getElementById("yarn_summary");
					//alert(ele);
					var text = document.getElementById("displayText");
					if(ele.style.display!= "none")
					{
						ele.style.display = "none";
						text.innerHTML = "Show Yarn Summary";
					}
					else
					{
						ele.style.display = "block";
						text.innerHTML = "Hide Yarn Summary";
					}
				}

			</script>
			<div style="width:4270px;">
			<div style="width:900px;" align="left">
				<table width="900" cellpadding="0" cellspacing="2" border="0">
					<tr>
						<td width="350" align="left">
							<table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
								<thead align="center">
									<tr>
										<th colspan="4">Order Wise Budget Cost Summary Group</th>
									</tr>
									<tr>
										<th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
									</tr>
								</thead>
								<tr bgcolor="<? echo $style1; ?>">
									<td width="20">1</td>
									<td width="130">Yarn Cost</td>
									<td width="120" align="right" id="yarn_cost"></td>
									<td width="80" align="right" id="yarn_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>2</td>
									<td>Fabric Purchase</td>
									<td align="right" id="purchase_cost"></td>
									<td align="right" id="purchase_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>3</td>
									<td>Knitting Cost</td>
									<td align="right" id="knit_cost"></td>
									<td align="right" id="knit_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>4</td>
									<td>Yarn Dyeing Cost</td>
									<td align="right" id="ydyeing_cost"></td>
									<td align="right" id="ydyeing_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>5</td>
									<td>AOP Cost</td>
									<td align="right" id="aop_cost"></td>
									<td align="right" id="aop_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>6</td>
									<td>Dyeing & Finishing Cost</td>
									<td align="right" id="dyefin_cost"></td>
									<td align="right" id="dyefin_cost_per"></td>
								</tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Total Material & Service Cost</strong></td>
									<td align="right" id="tot_matr_ser_cost"></td>
									<td align="right" id="tot_matr_ser_per"></td>
								</tr>
								<tr bgcolor="<?  echo $style1; ?>">
									<td>7</td>
									<td>Trims Cost</td>
									<td align="right" id="trim_cost"></td>
									<td align="right" id="trim_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>8</td>
									<td>Print/ Emb. /Wash Cost</td>
									<td align="right" id="embelishment_cost"></td>
									<td align="right" id="embelishment_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>9</td>
									<td>Commercial Cost</td>
									<td align="right" id="commercial_cost"></td>
									<td align="right" id="commercial_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>10</td>
									<td>Commision Cost</td>
									<td align="right" id="commission_cost"></td>
									<td align="right" id="commission_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>11</td>
									<td>Testing Cost</td>
									<td align="right" id="testing_cost"></td>
									<td align="right" id="testing_cost_per"></td>
								</tr>
									<tr bgcolor="<? echo $style1; ?>">
									<td>12</td>
									<td>Freight Cost</td>
									<td align="right" id="freight_cost"></td>
									<td align="right" id="freight_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td width="20">13</td>
									<td width="100">Inspection Cost</td>
									<td align="right" id="inspection_cost"></td>
									<td align="right" id="inspection_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>14</td>
									<td>Certificate Cost</td>
									<td align="right" id="certificate_cost"></td>
									<td align="right" id="certificate_cost_percent"></td>
								</tr>
									<tr bgcolor="<? echo $style; ?>">
									<td>15</td>
									<td>Operating Exp.</td>
									<td align="right" id="commn_cost"></td>
									<td align="right" id="commn_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>16</td>
									<td>Courier Cost</td>
									<td align="right" id="courier_cost">></td>
									<td align="right" id="courier_cost_per">></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>17</td>
									<td>CM Cost</td>
									<td align="right" id="cm_cost"></td>
									<td align="right" id="cm_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style1; ?>">
									<td>18</td>
									<td>Total Cost</td>
									<td align="right" id="cost_cost"></td>
									<td align="right" id="cost_cost_per"></td>
								</tr>
								<tr bgcolor="<? echo $style; ?>">
									<td>19</td>
									<td>Total Order Value</td>
									<td align="right" id="order_id"></td>
									<td align="right" id="order_percent"></td>
								</tr>
							</table>
						</td>
						<!--<td colspan="5" style="min-height:900px; max-height:100%" align="center" valign="top">
							<div id="chartdiv" style="width:580px; height:900px;" align="center"></div>
						</td>-->
					</tr>
				</table>
			</div>
			<br/>
			<?
			ob_start();
			?>
			<h3 align="left" id="accordion_h2" style="width:4270px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
			<fieldset style="width:100%;" id="content_search_panel2">
			<table width="4170">
					<tr class="form_caption">
						<td colspan="43" align="center"><strong>Cost Break Down Report</strong></td>
					</tr>
					<tr class="form_caption">
						<td colspan="43" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
					</tr>
					<tr colspan="43" align="center" class="form_caption">
						<td><strong>Details Report </strong></td>
					</tr>
			</table>
			   <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit'];

					if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else $caption="PO Insert Date";
			   ?>
			<table id="table_header_1" class="rpt_table" width="4050" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40" rowspan="2">SL</th>
						<th width="70" rowspan="2">Buyer</th>
						<th width="70" rowspan="2">Job No</th>
						<th width="100" rowspan="2">Order No</th>
						<th width="100" rowspan="2">Order Status</th>
						<th width="110" rowspan="2">Style</th>
						<th width="110" rowspan="2">Item Name</th>
						<th width="110" rowspan="2">Dealing</th>
						<th width="70" rowspan="2"><? echo $caption; ?></th>
						<th width="90" rowspan="2">Order Qty</th>
						<!--<th width="90" rowspan="2">Avg Unit Price</th>
						<th width="100" rowspan="2">Order Value</th>-->
						<th colspan="14">Fabric Cost</th>
						<th width="100" rowspan="2">Trim Cost</th>
						<th colspan="5">Embell. Cost</th>
						<th width="120" rowspan="2">Commercial Cost</th>
						<th colspan="2">Commission</th>
						<th width="100" rowspan="2">Testing Cost</th>
						<th width="100" rowspan="2">Freight Cost</th>
						<th width="120" rowspan="2">Inspection Cost</th>
						<th width="100" rowspan="2">Certificate Cost</th>
						<th width="100" rowspan="2">Operating Exp</th>
						<th width="100" rowspan="2">Courier Cost</th>
						<th width="120" rowspan="2">CM Cost/DZN</th>
						<th width="100" rowspan="2">CM Cost</th>
						<th rowspan="2">Total Cost</th>

					</tr>
					<tr>
						<th width="100">Avg Yarn Rate</th>
						<th width="80">Yarn Cost</th>
						<th width="80">Yarn Cost %</th>
						<th width="100">Fabric Purchase</th>
						<th width="80">Knit/ Weav Cost/Dzn</th>
						<th width="80">Knitting/ Weav Cost</th>
						<th width="100">Yarn Dye Cost/Dzn </th>
						<th width="110">Yarn Dyeing Cost </th>
						<th width="120">Fab.Dye Cost/Dzn</th>
						<th width="100">Fabric Dyeing Cost</th>
						<th width="90">Heat Setting</th>
						<th width="100">Finishing Cost</th>
						<th width="90">Washing Cost</th>
						<th width="90">All Over Print</th>
						<th width="80">Printing</th>
						<th width="85">Embroidery</th>
						<th width="80">Special Works</th>
						<th width="80">Wash Cost</th>
						<th width="80">Other</th>
						<th width="120">Foreign</th>
						<th width="120">Local</th>
					</tr>
				</thead>
			</table>
			<div style="width:4170px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="4050" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
			$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
	$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;


			/*$JobArr=array();
			foreach($result_sql_budget2 as $budget_row){
				$JobArr[]="'".$budget_row[csf('job_no')]."'";
			}*/
			 $condition= new condition();
			$all_job_id=implode(",",$jobIdArr);
			// echo  $all_job_id.'d';
			if($all_job_id!='')
			{
			$condition->jobid_in($all_job_id);
			}
			 $condition->init();
			$yarn= new yarn($condition);
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			foreach($result_sql_budget2 as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}

				$dzn_qnty=0;
				$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;

				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];

				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];

				//$total_order_amount+=$order_value;
				$total_plancut_amount+=$plancut_value;
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					 <td width="40"><? echo $i; ?></td>
					 <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
					 <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					 <td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
					 <td width="100"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
					 <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					 <td width="110"><p><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></p></td>
					 <td width="110"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
					 <td width="70"><p><? echo $ship_po_recv_date; ?></p></td>
					 <td width="90" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
					 <!--<td width="90" align="right"><p><? //echo number_format($row[csf('avg_unit_price')],2); ?></p></td>
					 <td width="100" align="right"><p><? //echo number_format($order_value,2); ?></p></td>-->
					 <?
						$commercial_cost=($fabriccostArray[$row[csf('job_no')]]['comm_cost']/$dzn_qnty)*$order_qty_pcs;

						$fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
						$fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));
						 $fab_purchase=0; //$yarn_costing=0;
						foreach($fabricData as $fabricRow)
						{
							$fabricRow=explode("**",$fabricRow);
							$fab_nature_id=$fabricRow[0];
							$fab_source_id=$fabricRow[1];
							$fab_rate=$fabricRow[2];
							$yarn_qty=$fabricRow[3];
							$yarn_amount=$fabricRow[4];
							//$yarn_costing=0;
							if($fab_source_id==2)
							{
								foreach($fab_precost_Data as $fab_row)
								{
									$fab_dataRow=explode("**",$fab_row);
									$fab_requirment=$fab_dataRow[0];
									$fab_pcs=$fab_dataRow[1];
									$fab_purchase_qty=$fab_requirment/$fab_pcs*$plan_cut_qnty;
									//echo $fab_pcs;
									$fab_purchase=$fab_purchase_qty*$fab_rate;
								}
							}
							else if($fab_source_id==1 || $fab_source_id==3)
							{
								//$avg_rate=$yarn_amount/$yarn_qty;
								//$yarn_costing=$yarn_amount/$dzn_qnty*$plan_cut_qnty;
							}
						}
						$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];

						$avg_rate=$yarn_costing/$yarn_req_qty_arr[$row[csf('po_id')]];
						$yarn_cost_percent=($yarn_costing/$order_value)*100;

						$kniting_cost=$knit_arr[$row[csf('job_no')]]['knit']+$knit_arr[$row[csf('job_no')]]['weaving']+$knit_arr[$row[csf('job_no')]]['collar_cuff']+$knit_arr[$row[csf('job_no')]]['feeder_stripe'];
						$knit_cost_dzn=$kniting_cost;
						$knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
						$yarn_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['yarn_dyeing'];
						$yarn_dyeing_cost=($yarn_dyeing_cost_dzn/$dzn_qnty)*$plan_cut_qnty;
						$fabric_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['fabric_dyeing'];
						$fabric_dyeing_cost=($fabric_dyeing_cost_dzn/$dzn_qnty)*$plan_cut_qnty;
						$heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
						$fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
						$washing_cost=($knit_arr[$row[csf('job_no')]]['washing']/$dzn_qnty)*$plan_cut_qnty;
						$all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
						//echo  $trim_qty_arr[$row[csf('job_no')]][$row[csf('po_id')]];
						$trim_amount= $trim_qty_arr[$row[csf('po_id')]];//$fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty*$order_qty_pcs;
						$print_amount=($fab_emb[$row[csf('job_no')]]['print']/$dzn_qnty)*$order_qty_pcs;
						$embroidery_amount=($fab_emb[$row[csf('job_no')]]['embroidery']/$dzn_qnty)*$order_qty_pcs;
						$special_amount=($fab_emb[$row[csf('job_no')]]['special']/$dzn_qnty)*$order_qty_pcs;
						$wash_cost=($fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty)*$order_qty_pcs;
						$other_amount=($fab_emb[$row[csf('job_no')]]['other']/$dzn_qnty)*$order_qty_pcs;
						$foreign=$commission_array[$row[csf('job_no')]]['foreign']/$dzn_qnty*$order_qty_pcs;
						$local=$commission_array[$row[csf('job_no')]]['local']/$dzn_qnty*$order_qty_pcs;
						$test_cost=$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$order_qty_pcs;
						$freight_cost= $fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$order_qty_pcs;
						$inspection=$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$order_qty_pcs;
						$certificate_cost=$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$order_qty_pcs;
						$common_oh=$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$order_qty_pcs;
						$currier_cost=$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
						//echo $currier_cost;
						$cm_cost_dzn=$fabriccostArray[$row[csf('job_no')]]['c_cost'];
						$cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$order_qty_pcs;
						$total_cost=$yarn_costing+$fab_purchase+$knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;

						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100;
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;

						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
						if($kniting_cost<=0) $color_knit="red"; else $color_knit="";
						if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
						if($commercial_cost<=0) $color_com="red"; else $color_com="";
					 ?>
					 <td width="100" align="right"><? echo number_format($avg_rate,2); ?></td>
					 <td width="80" align="right" bgcolor="<? echo $color_yarn; ?>"><? echo number_format($yarn_costing,2); ?></td>
					 <td width="80" align="right"><? echo number_format($yarn_cost_percent,2); ?></td>
					 <td width="100" align="right"><? echo number_format($fab_purchase,2); ?></td>
					 <td width="80" align="right"><? echo number_format($knit_cost_dzn,4); ?></td>
					 <td width="80" align="right" bgcolor="<? echo $color_knit; ?>"><? echo number_format($knit_cost,2); ?></td>
					 <td width="100" align="right"><? echo number_format($yarn_dyeing_cost_dzn ,2); ?></td>
					 <td width="110" align="right"><? echo number_format($yarn_dyeing_cost ,2); ?></td>
					 <td width="120" align="right"><? echo number_format($fabric_dyeing_cost_dzn,2); ?></td>
					 <td width="100" align="right" bgcolor="<? echo $color_fab; ?>"><a href="##" onClick="generate_precost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_dyeing_detail')"><? echo number_format($fabric_dyeing_cost,2); ?></a></td>
					 <td width="90" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
					 <td width="100" align="right" ><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_finishing_detail')"><? echo number_format($fabric_finish,2); ?></a></td>
					 <td width="90" align="right"><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_washing_detail')"><? echo number_format($washing_cost,2); ?></a></td>
					 <td width="90" align="right"><a href="##" onClick="generate_precost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_all_over_detail')"><? echo number_format($all_over_cost,2); ?></a></td>
				<?
					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;

					$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;

					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_cm_amount+=$cm_cost;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];

					if($trim_amount<=0) $color_trim="red"; else $color_trim="";
					if($cm_cost<=0) $color="red"; else $color="";

					$yarnData=explode(",",substr($yarncostArray[$row[csf('job_no')]],0,-1));
					//print_r($yarnData);
					foreach($yarnData as $yarnRow)
					{
						$yarnRow=explode("**",$yarnRow);
						$count_id=$yarnRow[0];
						$type_id=$yarnRow[1];
						$cons_qnty=$yarnRow[2];
						$amount=$yarnRow[3];

						$yarn_desc=$yarn_count_library[$count_id]."**".$yarn_type[$type_id];
						$req_qnty=($plan_cut_qnty/$dzn_qnty_yarn)*$cons_qnty;
						$req_amnt=($plan_cut_qnty/$dzn_qnty_yarn)*$amount;

						$yarn_desc_array[$yarn_desc]['qnty']+=$req_qnty;
						$yarn_desc_array[$yarn_desc]['amnt']+=$req_amnt;
					}
					//var_dump($yarn_desc_array);
					?>
					 <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_precost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','trim_cost_detail')"><? echo number_format($trim_amount,2); ?></a></td>
					 <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','print_cost_detail')"><? echo number_format($print_amount,2); ?></a></td>
					 <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','embroidery_cost_detail')"><? echo number_format($embroidery_amount,2); ?></a></td>
					 <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
					 <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','wash_cost_detail')"><? echo number_format($wash_cost,2); ?></a></td>
					 <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
					 <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
					 <td width="120" align="right"><? echo number_format($local,2) ?></td>
					 <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
					 <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
					 <td width="120" align="right"><? echo number_format($inspection,2);?></td>
					 <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
					 <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
					 <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>
					 <td width="120" align="right"><? echo number_format($cm_cost_dzn,2);?></td>
					 <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
					 <td align="right"><? echo number_format($total_cost,2); ?></td>
					<?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";
						else $color_pl="";
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
						$expect_variance=$total_profit-$expected_profit_per;

					?>
				  </tr>
				<?
				$total_order_qty+=$order_qty_pcs;
				$total_order_amount+=$order_value;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$all_over_print_cost+=$all_over_cost;
				$total_trim_cost+=$trim_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;

				//echo $total_cost.'<br>';
				//$total_fab_cost_amount2+=$total_fab_cost_amount;
				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;
				//echo $total_fab_cost_amount;
				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			//$total_purchase_cost_percentage=$total_purchase_cost/$total_order_amount*100;
			?>
			</table>
			</div>
			<table class="tbl_bottom" width="4050" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="40"></td>
					<td width="70"></td>
					<td width="70"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="110"></td>
					<td width="110"></td>
					<td width="110"></td>
					<td width="70"></td>
					<td width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
					<!--<td width="90"></td>-->
					<td style="display:none;" width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></td>
					<td width="100"></td>
					<td width="80" align="right" id="total_yarn_cost2"><? echo number_format($total_yarn_cost,2); ?></td>
					<td width="80" align="right" id="total_yarn_cost_per"><? echo number_format($total_yarn_cost_percentage,2); ?></td>
					<td width="100" align="right" id="total_purchase_cost"><? echo number_format($total_purchase_cost,2); ?></td>
					<td width="80"></td>
					<td width="80" align="right" id="total_knitting_cost"><? echo number_format($total_knitting_cost,2); ?></td>
					<td width="100"></td>
					<td width="110" align="right" id="total_yarn_dyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
					<td width="120"></td>
					<td width="100" align="right" id="total_fabric_dyeing_cost4"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
					<td width="90" align="right" id="total_heat_setting_cost"><? echo number_format($total_heat_setting_cost,2); ?></td>
					<td width="100" align="right" id="total_finishing_cost"><? echo number_format($total_finishing_cost,2); ?></td>
					<td width="90" align="right" id="total_washing_cost"><? echo number_format($total_washing_cost,2); ?></td>
					<td width="90" align="right" id="all_over_print_cost"><? echo number_format($all_over_print_cost,2); ?></td>
					<td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
					<td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
					<td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
					<td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
					<td width="80" align="right" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
					<td width="80" align="right" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
					<td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2); ?></td>
					<td width="120" align="right" id="total_foreign_amount"><? echo number_format($total_foreign_amount,2); ?></td>
					<td width="120" align="right" id="total_local_amount"><? echo number_format($total_local_amount,2); ?></td>
					<td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2); ?></td>
					<td width="100" align="right" id="total_freight_amount"><? echo number_format($total_freight_amount,2); ?></td>
					<td width="120" align="right" id="total_inspection_amount"><? echo number_format($total_inspection_amount,2); ?></td>
					<td width="100" align="right" id="total_certificate_amount"><? echo number_format($total_certificate_amount,2); ?></td>
					<td width="100" align="right" id="total_common_oh_amount"><? echo number_format($total_common_oh_amount,2); ?></td>
					<td width="100" align="right" id="total_currier_amount"><? echo number_format($total_currier_amount,2); ?></td>
					<td width="120"></td>
					<td width="100" align="right" id="total_cm_amount"><? echo number_format($total_cm_amount,2); ?></td>
					<td align="right" id="total_tot_cost"><? echo number_format($total_tot_cost,2); ?></td>
				</tr>
			</table>
			<table>
				<tr>
					<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_cost_up,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');

					//$chart_data_qnty="Fabric Cost;".$total_fab_cost."\nTrimCost;".$total_trim_cost."\nEmbelishment Cost;".$total_embelishment_cost."\nCommercial Cost;".$total_commercial_cost."\nCommission Cost;".$total_commssion."\nTesting Cost;".$total_testing_cost."\nFreightCost;".$total_freight_cost."\nCM Cost;".$total_cm_cost."\nInspection Cost;".$total_inspection."\nCertificate Cost;".$total_certificate_cost."\nCommn OH Cost;".$total_common_oh."\nCurrier Cost;".$total_currier_cost."\n Profit/Loss;".$total_fabric_profit_up."\n";
					?>
					<input type="hidden" id="graph_data" value="<? //echo substr($chart_data_qnty,0,-1); ?>"/>
				</tr>
			</table>
			<br>
			<a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>

			<div style="width:600px; display:none" id="yarn_summary" >
				<div id="data_panel2" align="center" style="width:500px">
					 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
				 </div>
				<table width="500">
					<tr class="form_caption">
						<td colspan="6" align="center"><strong>Yarn Cost Summary </strong></td>
					</tr>
				</table>
				<table class="rpt_table" width="500" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="80">Yarn Count</th>
						<th width="120">Type</th>
						<th width="120">Req. Qnty</th>
						<th width="80">Avg. rate</th>
						<th>Amount</th>
					</thead>
					<?
					$s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
					foreach($yarn_desc_array as $key=>$value)
					{
						if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$yarn_desc=explode("**",$key);

						$tot_yarn_req_qnty+=$yarn_desc_array[$key]['qnty'];
						$tot_yarn_req_amnt+=$yarn_desc_array[$key]['amnt'];
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
							<td><? echo $s; ?></td>
							<td align="center"><? echo $yarn_desc[0]; ?></td>
							<td><? echo $yarn_desc[1]; ?></td>
							<td align="right"><? echo number_format($yarn_desc_array[$key]['qnty'],2); ?></td>
							<td align="right"><? echo number_format($yarn_desc_array[$key]['amnt']/$yarn_desc_array[$key]['qnty'],2); ?></td>
							<td align="right"><? echo number_format($yarn_desc_array[$key]['amnt'],2); ?></td>
						</tr>
						<?
						$s++;
					}
					?>
					<tfoot>
						<th colspan="3" align="right">Total</th>
						<th align="right"><? echo number_format($tot_yarn_req_qnty,2); ?></th>
						<th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
						<th align="right"><? echo number_format($tot_yarn_req_amnt,2); ?></th>
					</tfoot>
				</table>
			</div>
			</fieldset>
			</div>
			<?

	}

	//echo $report_type ; die;
	if($excel_type==10) //Convert to Excel Button
	{
		$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$html####$filename";
		//echo "$filename####$excel_type";
		exit();
	}
	else
	{
		$html = ob_get_contents();
	    ob_clean();
	    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	    foreach (glob("*.xls") as $filename) {
	    //if( @filemtime($filename) < (time()-$seconds_old) )
	    @unlink($filename);
	    }
	    //---------end------------//
	    $name=time();
	    $filename=$user_id."_".$name.".xls";
	    $create_new_doc = fopen($filename, 'w');
	    $is_created = fwrite($create_new_doc, $html);
	    echo "$html**$filename";
	    exit();
	}


}
