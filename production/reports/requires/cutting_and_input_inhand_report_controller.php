<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
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
	echo create_drop_down( "cbo_location_name", 200, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
	
	exit();
}

if ($action=="load_drop_down_floor")
{
    extract($_REQUEST);
    $choosenLocation = $choosenLocation;  
	echo create_drop_down( "cbo_floor_name", 200, "SELECT id,floor_name from lib_prod_floor where location_id in( $choosenLocation ) and status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'cutting_and_input_inhand_report_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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
		// $group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		// $group_field="group_concat(distinct b.po_number ) as po_number";
		$year_field="YEAR(a.insert_date)";
	}

	$arr=array (2=>$company_arr,3=>$buyer_arr);
	$sql= "SELECT a.ID, a.job_no_prefix_num, a.JOB_NO, a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,$year_field as YEAR,b.PO_NUMBER
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date,b.po_number
	order by a.id desc";
	//echo $sql;//die;
	$rows=sql_select($sql);
	$data_array = array();
	foreach ($rows as $row) 
	{
		$data_array[$row['JOB_NO']]['id'] 			= $row['ID'];
		$data_array[$row['JOB_NO']]['company_name'] = $row['COMPANY_NAME'];
		$data_array[$row['JOB_NO']]['buyer_name'] 	= $row['BUYER_NAME'];
		$data_array[$row['JOB_NO']]['year'] 		= $row['YEAR'];
		$data_array[$row['JOB_NO']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$data_array[$row['JOB_NO']]['po_number'] 	.= $row['PO_NUMBER'].", ";
	}
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
     <? 
         $i=1;
         foreach($data_array as $job_no=>$data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			// $po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data['id']; ?>'+'_'+'<? echo $job_no; ?>')" style="cursor:pointer;">
                <td valign="middle" width="30" align="center"><? echo $i; ?></td>
                <td valign="middle" width="120"><p><? echo $company_arr[$data['company_name']]; ?></p></td>
                <td valign="middle" width="120"><p><? echo $buyer_short_library[$data['buyer_name']]; ?></p></td>
                <td valign="middle" align="center" width="50"><p><? echo $data['year']; ?></p></td>
                <td valign="middle" width="120"><p><? echo $job_no; ?></p></td>
                <td valign="middle" width="120"><p><? echo $data['style_ref_no']; ?></p></td>
                <td><p><? echo chop($data['po_number'],','); ?></p></td>
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

 
 if($action=="style_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	// $report_type=$data[3];
	// print_r($data);
	//echo $batch_type."AAZZZ";
	?>
	<script type="text/javascript">
	  function js_set_value(id)
		  {
			//alert(id);
			document.getElementById('selected_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
	    else  if (str_replace("'","",$job_no)!="") $job_cond="and b.job_no_mst='".$job_no."'";
		if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
		$job_year_cond="";
		if($cbo_year!=0)
		{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
	    if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
		}
		if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		
		if($db_type==2) $group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number"; 
		else if($db_type==0) $group_field="group_concat(distinct b.po_number ) as po_number";

		$sql="select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num as job_prefix,$year_field,$group_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company $buyer_name $year_cond $job_cond and a.is_deleted=0 group by  a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.insert_date ";	


	//$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	?>
	<table width="500" border="1" rules="all" class="rpt_table">
		<thead>
	        <tr>
	            <th width="30">SL</th>
	             <th width="40">Year</th>
	             <th width="50">Job no</th>
	            <th width="100">Style</th>
	            <th width="">Po number</th>
	            
	        </tr>
	   </thead>
	</table>
	<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
	 <? $rows=sql_select($sql);
		 $i=1;
		 foreach($rows as $data)
		 {
			 	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
	  ?>
		<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('style_ref_no')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
	        <td align="center" width="40"><p><? echo $data[csf('year')]; ?></p></td>
			<td align="center"  width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
	        <td width=""><p><? echo $po_num; ?></p></td>
			
		</tr>
	    <? $i++; } ?>
	</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	disconnect($con);
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

$colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");








if($action=="generate_report2")
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");  
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
	$rpt_type=str_replace("'","",$type);
	$job_cond_id=""; 
	$style_cond="";
	$order_cond="";
   	if(str_replace("'","",$cbo_company_name)=="") $company_name=""; else $company_name=" and b.company_name in(".str_replace("'","",$cbo_company_name).")";
   		if(str_replace("'","",$cbo_company_name)=="") $company_name_lay=""; else $company_name_lay=" and e.company_id in(".str_replace("'","",$cbo_company_name).")";

   	if(str_replace("'","",$cbo_working_company_name)==0) $working_company_name=""; else $working_company_name=" and e.serving_company=".str_replace("'","",$cbo_working_company_name)."";
   	if(str_replace("'","",$cbo_working_company_name)==0) $working_company_name_lay=""; else $working_company_name_lay=" and e.working_company_id=".str_replace("'","",$cbo_working_company_name)."";
   	
	if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_name=""; else $buyer_name="and b.buyer_name=".str_replace("'","",$cbo_buyer_name)."";
	if(str_replace("'","",$cbo_location_name)=="")  $location_name=""; else $location_name="and e.location in(".str_replace("'","",$cbo_location_name).")";
	if(str_replace("'","",$cbo_location_name)=="")  $location_name_lay=""; else $location_name_lay="and e.location_id in(".str_replace("'","",$cbo_location_name).")";
	if(str_replace("'","",$cbo_floor_name)=="")  $floor_name=""; else $floor_name="and e.floor_id in(".str_replace("'","",$cbo_floor_name).")";
	if(str_replace("'","",$cbo_floor_name)=="")  $floor_name_lay=""; else $floor_name_lay="and e.floor_id in(".str_replace("'","",$cbo_floor_name).")";
	$job_year_cond="";
	if(str_replace("'","",$cbo_year)!=0) 
	{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
    	if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	

	if(str_replace("'","",$hidden_job_id)!="") { $job_cond_id="and b.id in(".str_replace("'","",$hidden_job_id).")";}
	else if (str_replace("'","",$txt_job_no)!="") { $job_cond_id=" and b.job_no_prefix_num=".str_replace("'","",$txt_job_no)." $job_year_cond  "; }
	else  $job_cond_id=" $job_year_cond  ";
	if(str_replace("'","",$hidden_style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$hidden_style_id).")";
	else  if (str_replace("'","",$txt_style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no='".$txt_style_no."'";
	if (str_replace("'","",$hidden_order_id)!=""){ $order_cond="and a.id in (".str_replace("'","",$hidden_order_id).")";$job_cond=""; }
	else if (str_replace("'","",$txt_order_no)=="") $order_cond=""; else $order_cond="and a.po_number='".str_replace("'","",$txt_order_no)."'"; 
	
	
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
 	$po_number_data=array();
	$production_data_arr=array();
	$po_number_id=array();
	$production_qty_arr=array();

	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $country_ship_date="";
	else $country_ship_date=" and d.country_ship_date between $txt_date_from and $txt_date_to";

	if(str_replace("'","",trim($txt_prod_from))=="" || str_replace("'","",trim($txt_prod_to))=="") $production_date="";
	else $production_date=" and e.production_date between $txt_prod_from and $txt_prod_to";

	if(str_replace("'","",trim($txt_prod_from))=="" || str_replace("'","",trim($txt_prod_to))=="") $production_date_lay="";
	else $production_date_lay=" and e.entry_date between $txt_prod_from and $txt_prod_to";

	if($db_type==0) { $group_cond="group by d.po_break_down_id,d.color_number_id"; }
	if($db_type==2) { $group_cond="group by a.id,a.job_no_mst,a.po_number, d.po_break_down_id,d.color_number_id,b.buyer_name,b.style_ref_no,
	b.job_no_prefix_num,b.insert_date"; }





	
	if($rpt_type==1) 
	{	
	    /*$sql="SELECT  a.id,a.job_no_mst,a.po_number,b.order_uom,
		  d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year 
		  as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no, e.location,e.floor_id, e.remarks,
		  a.pub_shipment_date as ship_date
		  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d,pro_garments_production_mst e
		  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id  and  a.job_no_mst=d.job_no_mst and a.id=e.po_break_down_id and d.po_break_down_id=e.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and 
		  b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and 
		  b.status_active=1 and e.status_active=1 and e.is_deleted=0 $company_name $working_company_name $location_name $floor_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond $production_date and e.production_type in(1,4)
		  order by  b.buyer_name,a.job_no_mst";*/

		  $sql="SELECT  a.id,a.job_no_mst,a.po_number,b.order_uom,
		  d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year 
		  as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no, e.location_id,e.floor_id, e.remarks,
		  a.pub_shipment_date as ship_date
		  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d, ppl_cut_lay_mst e
		  where a.job_id=b.id and a.id=d.po_break_down_id  and  a.job_id=d.job_id and b.job_no=e.job_no and a.is_deleted=0 and a.status_active=1 and 
		  b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and 
		  b.status_active=1 and e.status_active=1 and e.is_deleted=0 $company_name $working_company_name_lay $location_name_lay $floor_name_lay $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond $production_date_lay";
		   // echo $sql; die;

		  $res = sql_select($sql);
		  foreach($res as $row)
		  {
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['id']=$row[csf('id')];
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['order_uom']=$row[csf('order_uom')];
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['ship_date']=$row[csf('ship_date')];
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no_mst')];
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['gmt_id']=$row[csf('gmt_id')];

			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['floor_id']=$row[csf('floor_id')];
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['remarks']=$row[csf('remarks')];
			  // $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['po_quantity']+=$row[csf('order_qty')];
			  // $po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['plan_qty']+=$row[csf('plan_qty')];
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['buyer_name']=$row[csf('buyer_name')];
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['style']=$row[csf('style')];
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['job_prifix']=$row[csf('job_no_prefix_num')];
			  $po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['year']=$row[csf('year')];
			  //$po_number_data[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['color_id']=$row[csf('color_number_id')];
			  //$production_qty_arr[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]][$row[csf('production_type')]]+=$row[csf('production_qnty')];
			  //$po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['file_no']=$row[csf('file_no')];
			  //$po_number_data[$row[csf('id')]][$row[csf('color_number_id')]]['int_ref_no']=$row[csf('int_ref_no')];
			  $po_number_id[$row[csf('id')]]=$row[csf('id')];	
		  }
		  unset($res);
		  // echo "<pre>";print_r($production_qty_arr);
		  $poIds = implode(",", $po_number_id);
		if(count($po_number_id)>0)
		{
			if($db_type==2)
			{
				$po_cond_for_in=" and (";
				$poIdsArr=array_chunk($po_number_id,999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" a.id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
			}
			else
			{
				$po_cond_for_in=" and a.id in($poIds)";
				
			}
		}


		  $sewing_sql="SELECT  a.id,a.job_no_mst,a.po_number,b.order_uom,
		  d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,b.buyer_name,b.style_ref_no as style,b.job_no_prefix_num,$insert_year 
		  as year ,d.color_number_id, a.file_no, a.grouping as int_ref_no, e.location,e.floor_id, e.remarks,
		  f.production_type,f.production_qnty,a.pub_shipment_date as ship_date
		  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d,pro_garments_production_mst e, pro_garments_production_dtls f
		  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and f.color_size_break_down_id=d.id and  a.job_no_mst=d.job_no_mst and a.id=e.po_break_down_id and d.po_break_down_id=e.po_break_down_id and e.id=f.mst_id and a.is_deleted=0 and a.status_active=1 and 
		  b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and 
		  b.status_active=1 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $company_name $working_company_name $location_name $floor_name $buyer_name $style_cond $order_cond $sql_cond $job_cond_id $country_ship_date $shipping_cond $production_date and e.production_type=4 $po_cond_for_in 
		  order by  b.buyer_name,a.job_no_mst";
		   //echo $sql; die;

		  $sewing_res = sql_select($sewing_sql);
		  $sewing_data_arr=array();
		  foreach($sewing_res as $row)
		  {
			  
			  $sewing_data_arr[$row[csf('id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]+=$row[csf('production_qnty')];
			  
			  	
		  }
		  unset($sewing_res);

		  // ======================== FOR ORDER QUANTITY ====================================
		  $pcon = str_replace("a.id", "d.po_break_down_id", $po_cond_for_in);
		  $pro_qty_sql=sql_select ("SELECT d.po_break_down_id,  d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,d.item_number_id as gmt_id,d.color_number_id
		  from  wo_po_color_size_breakdown d
		  where  d.status_active=1 and d.is_deleted=0  $pcon");
		  $job_qty_data = [];
		  foreach($pro_qty_sql as $row)
		  {			  
			  $job_qty_data[$row[csf('po_break_down_id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['po_quantity']+=$row[csf('order_qty')];
			  $job_qty_data[$row[csf('po_break_down_id')]][$row[csf('gmt_id')]][$row[csf('color_number_id')]]['plan_qty']+=$row[csf('plan_qty')];
		  }
		  unset($pro_qty_sql);
		$pcon = str_replace("a.id", "c.order_id", $po_cond_for_in);
		$sql_lay=" SELECT  b.gmt_item_id, c.order_id, b.color_id, sum(c.size_qty )  as total_lay
		from ppl_cut_lay_mst e, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c,wo_po_break_down d 
		where e.id=b.mst_id and b.id=c.dtls_id and c.order_id=d.id and d.is_deleted=0 and e.status_active=1 and b.status_active=1 and c.status_active=1 $company_name_lay $working_company_name_lay $location_name_lay $floor_name_lay $production_date_lay $pcon 
		group by  b.gmt_item_id, c.order_id, b.color_id ";  

		//echo $sql_lay; die;

		$sql_lay_result=sql_select($sql_lay);
		$cut_and_lay_data=array();
 		

		foreach($sql_lay_result as $row)
		{
			
			$cut_and_lay_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]]["total_lay"] =$row[csf("total_lay")];
			 
		}

		 
		 ob_start();
	 		//and po_number_id in (".str_replace("'","",$po_number_id).")
	 		?>
	  		<fieldset style="width:1630px;">
	        	   <table width="1600"  cellspacing="0"   >
	                    <tr class="form_caption" style="border:none;">
	                           <td colspan="30" align="center" style="border:none;font-size:14px; font-weight:bold" > Cutting And Input Inhand Report</td>
	                     </tr>
	                    <tr style="border:none;">
	                            <td colspan="30" align="center" style="border:none; font-size:16px; font-weight:bold">
	                            <?
	                            if (str_replace("'","",$cbo_company_name)!='') {
	                            	$multi_com_id=array_unique(explode(",",str_replace("'","",$cbo_company_name)));
		                            $multi_com_name='';
		                            foreach ($multi_com_id as $key => $value) {
		                            	$multi_com_name.=$company_library[$value].",";
		                            }
	                            }else{
	                            	$multi_com_id=array_unique(explode(",",str_replace("'","",$cbo_working_company_name)));
	                            	$multi_com_name='';
		                            foreach ($multi_com_id as $key => $value) {
		                            	$multi_com_name.=$company_library[$value].",";
		                            }
	                            }

	                            
	                            
	                            ?>
	                            Company Name:<? echo chop($multi_com_name,","); //echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                              
	                            </td>
	                      </tr>
	                      <tr style="border:none;">
	                            <td colspan="30" align="center" style="border:none;font-size:12px; font-weight:bold">
	                            <? echo "Date: ". str_replace("'","",$txt_prod_from)." To ". str_replace("'","",$txt_prod_to) ;?>
	                            </td>
	                      </tr>
	                </table>
	             <br />	
	             <table cellspacing="0"  border="1" rules="all"  width="1600" class="rpt_table">
	                <thead>
	                	<tr >
	                        <th width="40" rowspan="2">SL</th>
	                        <th width="80" rowspan="2">Buyer</th>
	                        <th width="60" rowspan="2">Job No</th>
	                        <th width="50" rowspan="2">Year</th>
	                        <th width="100" rowspan="2">Order No</th>
	                        <th width="80" rowspan="2">Shipment Date</th>
	                        <th width="100" rowspan="2">Style</th>
	                        <th width="120" rowspan="2">Item</th>
	                        <th width="100" rowspan="2">Color</th>
	                        <th width="70" rowspan="2">Order Qty.</th>
	                        <th width="70" rowspan="2">Order UOM</th>
	                        <th width="100" rowspan="2">Cutting Floor</th>
	                        <th width="210" colspan="3">Cutting</th>
	                        <th width="210" colspan="3"> Sewing Input</th>
	                        <th width="70" rowspan="2" >Input Inhand</th>
	                        <th  rowspan="2">Remarks</th>
	                    </tr>
	                    <tr>
	                       
	                        <th width="70" rowspan="2">Total </th>
	                        <th width="70" rowspan="2">%</th>
	                        <th width="70" rowspan="2">Bal.</th>
	                        
	                        <th width="70" rowspan="2">Total</th>
	                        <th width="70" rowspan="2">%</th>
	                        <th width="70" rowspan="2">Balance</th>
	                        
	                       
	                    </tr>
	                </thead>
	            </table>
	             <div style="max-height:425px; overflow-y:scroll; width:1620px;" id="scroll_body">
	                    <table  border="1" class="rpt_table"  width="1600" rules="all" id="table_body" >
	                    <?
	                     
	                    $i=1;$k=1;
	                    $summery_arr=array();
		  	foreach($po_number_data as $po_id=>$po_arr)	
			{
				foreach($po_arr as $item_id=>$item_arr)	
			    {
					 foreach($item_arr as $color_id=>$row)	
				     {
	 				
	 					$total_cut=$cut_and_lay_data[$po_id][$item_id][$color_id]['total_lay'];
	 					$total_sew_input=$sewing_data_arr[$po_id][$item_id][$color_id];
	 					$total_order=$job_qty_data[$po_id][$item_id][$color_id]['po_quantity'];
	 					$cut_percentage=($total_cut/$total_order)*100;
	 					$cut_balance=$total_order-$total_cut;
	 					$input_percentage=($total_sew_input/$total_order)*100;
	 					$sew_input_balance=$total_order-$total_sew_input;
	 					$inhand_cut_input=$total_cut-$total_sew_input;
						  
						
						$grand_total_order+=$total_order;
						$grand_total_cut+=$total_cut;
						$grand_cutting_balance+=$cut_balance;
						$grand_total_sew+=$total_sew_input;
						$grand_total_sew_bal+=$sew_input_balance;
						$grand_inhand+=$inhand_cut_input;


						//Summery data
						$summery_arr[$row['buyer_name']]['total_cut']+= $total_cut;
						$summery_arr[$row['buyer_name']]['total_order']+= $total_order;
						$summery_arr[$row['buyer_name']]['sew_input']+= $total_sew_input;
						//$summery_arr[$row['buyer_name']]['total_cut']+= $total_cut;

	                    ?>
	 					
	                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
	                    <td width="40"><? echo $i; ?></td>
	                    <td width="80" style="word-break:break-all;"><p><? echo $buyer_short_library[$row['buyer_name']]; ?></p></td>
	                    <td width="60" align="center"><? echo $row['job_prifix']; ?></td>
	                    <td width="50" align="right"><? echo $row['year']; ?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $row['po_number']; ?></p></td>
	                    
	                    <td width="80" align="center"><?  echo  change_date_format($row['ship_date']);  ?></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $row['style']; ?></p></td>
	                    <td width="120" align="center" style="word-break:break-all;"><p><? echo $garments_item[$row['gmt_id']]; ?></p></td>
	                    <td width="100" align="center" style="word-break:break-all;"><p><? echo $colorname_arr[$color_id]; ?></p></td>
	                    <td width="70" align="right"><?  echo number_format($total_order,0); ?></td>
	                    <td width="70" align="center"><?  echo $unit_of_measurement[$row['order_uom']]; ?></td>
	                    <td width="100" align="center"><?
	                    if ($total_cut!=0) {
	                    	echo $floor_arr[$row['floor_id']];
	                    }
	                        ?></td>
	                    <td width="70" align="right"><?  echo number_format($total_cut,0); ?></a></td>
	                    <td width="70" align="right"><? echo number_format($cut_percentage,2);   ?></a></td>
	                    <td width="70" align="right"><?  echo number_format($cut_balance,0);  ?></td>
	                    <td width="70" align="right"> <? echo number_format($total_sew_input,0); ?></a></td>
	                    <td width="70" align="right" title="(Total Sewing Input*100)/Order Qty"><?  echo number_format($input_percentage,2); ?></td>
	                    <td width="70" align="right"><?  echo number_format($sew_input_balance,0);  ?></td>
	                    
	                     <td width="70" align="right" title="Inhand= Cutting Delivery To Input  - Delivery to  Embellishment &#13; + Receive from Embellishment -Sewing Input" ><?  echo number_format($inhand_cut_input,2); ?></td>
	                    <td  align="center"><? echo $row['remarks']; ?></td>
	        	  </tr>
							<?	
					 $job_arr[]=$po_number_data[$po_id][$color_id]['job_no'];
					 $buyer_arr[]=$po_number_data[$po_id][$color_id]['buyer_name'];
					 $i++;
	                } //end foreach 2nd

	            }
			 		
	 		}
			
				?>
	                        
                    <tfoot>
                         <tr>
                            <th width="40"><? // echo $i;?></th>
                            <th width="80"><? //echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></th>
                            <th width="60"></td>
                            <th width="50"></td>
                            <th width="100"><? //echo $pro_date_sql_row[csf("po_number")];?></th>
                            <th width="80"></th>
                            <th width="100"> <strong></strong></th>
                            <th width="120"> <strong></strong></th>
                            <th width="100" align="right"><strong>Grand Total:</strong></th>
                            <th width="70" align="right"><? echo number_format($grand_total_order,2); ?></th>
                            <th width="70"></th>
                            <th width="100"></th>
                           <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_cut,2); ?></th>
                            <th width="70" align="right" style="word-break:break-all;"><? //echo "%";  //echo $grand_today_cut; ?></th>
                            <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_cutting_balance,2); ?></th>
                            <th width="70" align="right" style="word-break:break-all;"><? echo number_format($grand_total_sew,2); ?></th>
                            <th width="70" align="right" style="word-break:break-all;"><? //echo "%"; //echo $input_percentage; ?></th>
                            <th width="70" align="right" style="word-break:break-all;"><?  echo number_format($grand_total_sew_bal,2); ?></th>
                            <th width="70" align="right" style="word-break:break-all;"><? echo number_format($grand_inhand,2); ?></th>
                            <th  align="center"><? //echo "remark"; //$total_iron+=$row[csf("iron_qnty")]; echo $row[csf("iron_qnty")]; ?></th>
                     </tr> 
					</tfoot>
									    
	            </table> 
	           </div> 
	           
	  	</fieldset>

	  	<br> 
	    <br> 
 		<div style="width:60%;">
		   	<fieldset style="width:530px;">
		   	<table cellspacing="0"  border="1" rules="all"  width="530" class="rpt_table">
		            <thead>
		            	<tr class="form_caption" style="border:none;">
		                       <th colspan="6" align="center" style="border:none;font-size:14px; font-weight:bold" > Summery</th>
		                 </tr>
		            	<tr >
		                    <th width="40">SL</th>
		                    <th width="80">Buyer</th>
							<th width="100">Order Qty.</th>
		                    <th width="100">Total Cutting</th>
		                    <th width="100">Cutting Bal.</th>
		                    <th width="100">Total Sewing Input</th>
		                    <th width="100">Input Inhand</th>
		                </tr>
		            </thead>
		        </table>
		       
		       <div style="max-height:325px;  width:530px;"  id="scroll_body">
		        <table  border="1" class="rpt_table"  width="530" rules="all"  id="table_body" >
		                <?
		                // echo "<pre>";print_r($summery_arr);
		                 
		                $i=1;$k=1;
			  	foreach($summery_arr as $buyer_key=>$row)	
				{
					    $sum_order_qty+=$row['total_order'];
						$sum_grand_cut+=$row['total_cut'];
						$sum_grand_cut_bal+=$row['total_order']-$row['total_cut'];
						$sum_grand_sew+=$row['sew_input'];
						$sum_grand_inhand+=$row['total_cut']-$row['sew_input'];
					
		                ?>
		            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_3rd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_3rd<? echo $i; ?>">
		                <td width="40"><? echo $i; ?></td>
		                <td width="80" style="word-break:break-all;"><p><? echo $buyer_short_library[$buyer_key]; ?></p></td>
		                <td width="100" align="right"><? echo number_format($row['total_order'],2);?></td>
						<td width="100" align="right"><? echo number_format($row['total_cut']);?></td>
		                <td width="100" align="right"><? echo number_format($row['total_order']-$row['total_cut'],2);?></td>
		                <td width="100" align="right" style="word-break:break-all;"><p><? echo number_format($row['sew_input'],2);?></p></td>
		                
		                <td width="100" align="right"><?  echo number_format($row['total_cut']-$row['sew_input'],2);  ?></td>
		             </tr>
		         
		                
				<?
				$i++;
				}
				?>
				<tfoot>
		                 <tr>
		                    <th width="40"><? // echo $i;?></th>
		                    <th width="80">Total:</th>
							<th width="100" align="right"><? echo number_format($sum_order_qty,2); ?></td>
		                    <th width="100" align="right"><? echo number_format($sum_grand_cut,2); ?></td>
		                    <th width="100" align="right"><? echo number_format($sum_grand_cut_bal,2); ?></td>
		                    <th width="100" align="right"><? echo number_format($sum_grand_sew,2); ?></th>
		                    <th width="100" align="right"><? echo number_format($sum_grand_inhand,2); ?></th>
		                </tr>
		        </tfoot>
		     </table>
		    </div>
		   </fieldset>
		</div>

	 	<?	
	}


	
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



 
?>