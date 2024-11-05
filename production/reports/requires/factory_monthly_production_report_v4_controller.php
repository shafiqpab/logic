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
$floor_group_library=return_library_array( "select id,group_name from lib_prod_floor", "id", "group_name"  );

if ($action=="load_drop_down_buyer")
{
	// echo create_drop_down( "cbo_buyer_name", 100, "SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.party_type='1' order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );     	 
	exit();
  exit();	 
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if($action=="load_drop_down_location")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($ex_data[0]) order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/factory_monthly_production_report_v4_controller','$ex_data[0]'+'_'+this.value+'_'+'$ex_data[1]', 'load_drop_down_floor', 'floor_td')" );
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 
	and company_id in($ex_data[0]) and location_id in($ex_data[1]) order by floor_name","id,floor_name",1, "-- Select Floor --", $selected, "" );   
	  	 	
	exit();    	 
}
if ($action=="load_drop_down_group")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_group_name", 100, "select id,group_name from lib_prod_floor where status_active =1 and is_deleted=0 
	and company_id in($ex_data[0]) and location_id in($ex_data[1]) and id in($ex_data[2]) order by group_name","id,group_name",1, "-- Select Group --", $selected, "" );   

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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'factory_monthly_production_report_v4_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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
	
	//echo $sql;
	//echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	//echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}
if($action=="intref_search_popup")
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
			
			$('#hide_int_ref_id').val( id );
			$('#hide_int_ref_no').val( name );
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
	                    <th id="search_by_td_up" width="170">Please Enter Int Ref No</th>
	                   
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
	                    <input type="hidden" name="hide_int_ref_no" id="hide_int_ref_no" value="" />
	                    <input type="hidden" name="hide_int_ref_id" id="hide_int_ref_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($company) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Internal Ref",2=>"Style Ref",3=>"Job No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $job_no; ?>', 'intref_search_list_view', 'search_div', 'factory_monthly_production_report_v4_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="intref_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$job_no=$data[6];
	if($job_no!='') $job_no_cond="and a.job_no_prefix_num=$job_no";else $job_no_cond="";
	
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
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}

	if($search_by==1) 
		$search_field="b.grouping"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no_prefix_num";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
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
	$arr=array (0=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT a.id, a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $date_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,b.grouping order by a.id, b.grouping"; 
    // echo $sql;die;
		
	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Style Ref. No,PO Number, Int Ref", "80,70,170,130","620","220",0, $sql , "js_set_value", "id,grouping","",1,"buyer_name,0,0,0,0,0",$arr,"buyer_name,job_no,style_ref_no,po_number,grouping","",'','0,0,0,0,0','',1) ;
   exit(); 
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name 	= str_replace("'","",$cbo_company_id);
	$cbo_floor 		= str_replace("'","",$cbo_floor_id);
	$cbo_location 	= str_replace("'","",$cbo_location_id);
	$cbo_group_name = str_replace("'","",$cbo_group_name);
	$buyer_name 	= str_replace("'","",$cbo_buyer_name);
	$int_ref 		= str_replace("'","",$txt_int_ref);
	$job_no 		= str_replace("'","",$txt_job_no);
	$style_ref 		= str_replace("'","",$txt_style_ref);
	$date_from 		=  str_replace("'","",$txt_date_from);
	$date_to 		=  str_replace("'","",$txt_date_to);
	$search_prod_date=change_date_format(str_replace("'","",$txt_date_from));
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=number_format($datediff/60,2);
	$dif_hour_min=date("H", strtotime($dif_time));	
	
	//echo $company_working_cond;
	ob_start();	
	
	if($type==1)
	{
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
		$company_group_library=return_library_array( "select id,group_id from lib_company", "id", "group_id");
		$group_short_library=return_library_array( "select id,group_name from lib_group", "id", "group_name");
		// $costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		// $tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 
		if(str_replace("'","",$cbo_location)==""){$location_con="";}else{$location_con=" and a.location=$cbo_location";}
		if(str_replace("'","",$cbo_floor)==""){$floor_con="";}else{$floor_con=" and a.floor_id=$cbo_floor";}
		if(str_replace("'","",$buyer_name)==0){$buyer_con="";}else{$buyer_con=" and c.buyer_name=$buyer_name";}
		
		if($company_name==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id in($company_name)";
		if($company_name==0) $cbo_company_cond_ex=""; else $cbo_company_cond_ex=" and d.company_id in($company_name)";		

	    //  ======================= geting shift name ============================
	    $sql = "SELECT shift_name,start_time,end_time from shift_duration_entry where status_active=1 and is_deleted=0 and production_type=3 order by shift_name asc";
	    $res = sql_select($sql);
	    $shift_arr = array();
	    foreach ($res as $val) 
	    {
	    	$shift_arr[$val['SHIFT_NAME']]['start_time'] = $val['START_TIME'];
	    	$shift_arr[$val['SHIFT_NAME']]['end_time'] = $val['END_TIME'];
	    }
		unset($res);

		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($company_name) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
		
		
		$group_prod_start_time=sql_select("select min(TO_CHAR(prod_start_time,'HH24:MI')) as prod_start_time  from variable_settings_production where company_name in($company_name) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
		
		
		foreach($start_time_data_arr as $row)
		{
			$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
			$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		}
		unset($start_time_data_arr);

		$prod_start_hour=$group_prod_start_time[0][csf('prod_start_time')];
		if($prod_start_hour=="") $prod_start_hour="08:00";
		$start_time=explode(":",$prod_start_hour);
		$hour=$start_time[0]*1; 
		$minutes=$start_time[1]; 
		$last_hour=23;
		$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
		$start_hour=$prod_start_hour;
		$start_hour_arr[$hour]=$start_hour;
		for($j=$hour;$j<$last_hour;$j++)
		{
			$start_hour=add_time($start_hour,60);
			$start_hour_arr[$j+1]=substr($start_hour,0,5);
			// echo $j."<br>";
		}
		//echo $pc_date_time;die;
		$start_hour_arr[$j+1]='23:59';


		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($company_name) and shift_id=1 and pr_date='$date_from' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");

		
		$first_hour_time=explode(":",$min_shif_start);
		$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
		$line_start_hour_arr[$hour_line]=$min_shif_start;
		
		for($l=$hour_line;$l<$last_hour;$l++)
		{
			$min_shif_start=add_time($min_shif_start,60);
			$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		}
		
		$line_start_hour_arr[$j+1]='23:59';

		/* $job_array=array(); 
		$job_sql="SELECT a.id, a.unit_price,b.buyer_name,b.company_name,a.po_quantity, b.job_no, b.total_set_qnty,b.set_smv from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
		
		
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			$job_array[$row[csf("id")]]['unit_price']=$row[csf("unit_price")];
			$job_array[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			$job_array[$row[csf("id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$job_array[$row[csf("id")]]['set_smv']=$row[csf("set_smv")];
			
		} */	  
		
	
	
		
		 $dtls_sql="SELECT a.production_date,a.po_break_down_id as po_id,c.buyer_name,a.company_id,a.item_number_id,a.sewing_line,b.unit_price,c.total_set_qnty,c.job_no,c.id as job_id,
					sum(CASE WHEN a.production_type =1 THEN e.production_qnty END) AS cutting_qnty,
					sum(CASE WHEN a.production_type =1 and a.production_source=1 THEN e.production_qnty END) AS cutting_qnty_inhouse,
					sum(CASE WHEN a.production_type =1 and a.production_source=3 THEN e.production_qnty END) AS cutting_qnty_outbound, 	
					sum(CASE WHEN a.production_type =5 THEN e.production_qnty END) AS sewing_qnty,
					sum(CASE WHEN a.production_type =5 and a.production_source=1 THEN e.production_qnty END) AS sewingout_qnty_inhouse,
					sum(CASE WHEN a.production_type =5 and a.production_source=3 THEN e.production_qnty END) AS sewingout_qnty_outbound, 
					sum(CASE WHEN a.production_type =4 THEN e.production_qnty END) AS sewing_input_qnty,
					sum(CASE WHEN a.production_type =4 and a.production_source=1 THEN e.production_qnty END) AS sewing_input_qnty_inhouse,
					sum(CASE WHEN a.production_type =4 and a.production_source=3 THEN e.production_qnty END) AS sewing_input_qnty_outbound, 
					sum(CASE WHEN a.production_type =8 THEN e.production_qnty END) AS finish_qnty,
					sum(CASE WHEN a.production_type =8 and a.production_source=1 THEN e.production_qnty END) AS finish_qnty_inhouse, 
					sum(CASE WHEN a.production_type =8 and a.production_source=3 THEN e.production_qnty END) AS finish_qnty_outbound,
					sum(CASE WHEN a.production_type =8  THEN a.carton_qty END) AS carton_qty					
					
					from pro_garments_production_mst a, wo_po_break_down b,wo_po_details_master c , WO_PO_COLOR_SIZE_BREAKDOWN d, pro_garments_production_dtls e
					where a.po_break_down_id=b.id and c.id=b.job_id and d.id=e.COLOR_SIZE_BREAK_DOWN_ID and a.id=e.mst_id and b.id=d.po_break_down_id   $location_con $floor_con $cbo_company_cond $buyer_con and a.production_date between '$date_from' and '$date_to'  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.production_date,a.po_break_down_id,c.buyer_name,a.company_id,a.item_number_id,a.sewing_line,b.unit_price,c.total_set_qnty,c.job_no,c.id order by a.sewing_line asc"; //and b.id=11106 
			 	// echo $dtls_sql;die; 
		
			    $dtls_sql_result=sql_select($dtls_sql);
				$prod_date_buyer_wise_summary=array();
				$sewing_total_buyer_wise_array=array();
				$sewing_line_buyer_wise_array=array();
				$production_data_arr=array();
				$po_id_array=array();
				$job_id_array=array();
				$check_array=array();
				foreach($dtls_sql_result as $row)
				{
					$job_id_array[$row['JOB_ID']] = $row['JOB_ID'];
				}
				$job_id_cond = where_con_using_array($job_id_array,0,"job_id");
				$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per"); 
				// echo "select job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond";die;
				$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost"); 
				foreach($dtls_sql_result as $row)
				{
					if($check_array[$row[csf('sewing_line')]][$row[csf('production_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]]=="")
					{
						if($production_data_arr[$row[csf('sewing_line')]][$row[csf('production_date')]]['item_number_id']!="")
						{
							$production_data_arr[$row[csf('sewing_line')]][$row[csf('production_date')]]['item_number_id'].="****".$row[csf('po_id')]."**".$row[csf('item_number_id')]."**".$row[csf('job_no')]."**".$row[csf('buyer_name')]."**".$row[csf('sewing_line')]; 
						}
						else
						{
							$production_data_arr[$row[csf('sewing_line')]][$row[csf('production_date')]]['item_number_id']=$row[csf('po_id')]."**".$row[csf('item_number_id')]."**".$row[csf('job_no')]."**".$row[csf('buyer_name')]."**".$row[csf('sewing_line')]; 
						}
						$check_array[$row[csf('sewing_line')]][$row[csf('production_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]] = "aa";
					}
					//array for summary part
					$prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
					$prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
					$prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['cutting_qnty']+=$row[csf("cutting_qnty")];
					$prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
					$prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
					$prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewing_input_qnty']+=$row[csf("sewing_input_qnty")];
					$prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				    $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
				    $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")]; 
					$prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['finish_qnty']+=$row[csf("finish_qnty")];

					$sewing_total_buyer_wise_array[$row[csf('sewing_line')]][$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("item_number_id")]]+=$row[csf("sewing_qnty")];
					// echo $row[csf('sewing_line')]."==".$row[csf("buyer_name")]."=".$row[csf("po_id")]."=".$row[csf("item_number_id")]."=".$row[csf("sewing_qnty")]."<br>";

					$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];

					$sewing_line_buyer_wise_array[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("sewing_line")]][$row[csf("production_date")]]+=$row[csf("sewing_qnty")];
					
					
				    $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewingout_value_inhouse']+=$row[csf("sewingout_qnty_inhouse")]*($row[csf("unit_price")]/$row[csf("total_set_qnty")]);

					$prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewingout_value_outbound']+=$row[csf("sewingout_qnty_outbound")]*($row[csf("unit_price")]/$row[csf("total_set_qnty")]);
			
						$cm_value=0; $cm_value_in=0; $cm_value_out=0; $sewing_qty_in=0; $sewing_qty_out=0;
							//$sewing_qnty=$row[csf("sewing_qnty")];
						$sewing_qty_in=$row[csf("sewingout_qnty_inhouse")];
						$sewing_qty_out=$row[csf("sewingout_qnty_outbound")];
						
						$job_no=$row[csf("job_no")];
						$total_set_qnty=$row[csf("total_set_qnty")];
						$costing_per=$costing_per_arr[$job_no];
						
						if($costing_per==1) $dzn_qnty=12;
						else if($costing_per==3) $dzn_qnty=12*2;
						else if($costing_per==4) $dzn_qnty=12*3;
						else if($costing_per==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
								   
						$dzn_qnty=$dzn_qnty*$total_set_qnty;
						
						$cm_value_in=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_in;
						// echo "(".$tot_cost_arr[$job_no]."/".$dzn_qnty.")*".$sewing_qty_in."<br>";
						$cm_value_out=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_out;
						$prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['cm_value_in']+=$cm_value_in;   
			   }
			//    echo"<pre>";  print_r($sewing_total_buyer_wise_array);die;

				if($cbo_company==0) $cbo_delivery_com_cond=""; else $cbo_delivery_com_cond=" and d.company_id in($cbo_company)";

				$exfactory_res =("SELECT a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,d.company_id as company,b.unit_price,c.total_set_qnty,c.job_no,c.id as job_id,  
				
				sum(case when a.entry_form!=85 then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 then a.ex_factory_qnty else 0 end) as ex_factory_qnty, 
				sum(case when a.entry_form!=85 and (d.company_id=0 OR d.company_id=c.company_name)  then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and (d.company_id=0 OR d.company_id=c.company_name) then a.ex_factory_qnty else 0 end) as ex_factory_qnty_inhouse,
				
				
				sum(case when a.entry_form!=85 and d.company_id!=0 and d.company_id!=c.company_name then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and d.company_id!=0 and d.company_id!=c.company_name then a.ex_factory_qnty else 0 end) as ex_factory_qnty_outbound
				
				from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
				
				where a.delivery_mst_id=d.id and  a.po_break_down_id=b.id and b.job_id=c.id $cbo_company_cond_ex $cbo_delivery_com_cond  and a.is_deleted=0 and a.status_active=1 and a.ex_factory_date between '$date_from' and '$date_to' group by a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,d.company_id,b.unit_price,c.total_set_qnty,c.job_no,c.id");
				
				//echo $exfactory_res; die;
						
				$exfactory_res_val=sql_select($exfactory_res);
			   	
				$job_id_array=array();
				foreach($exfactory_res_val as $row)
				{
					$job_id_array[$row['JOB_ID']] = $row['JOB_ID'];
				}
				$job_id_cond = where_con_using_array($job_id_array,0,"job_id");
				$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per"); 
				$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost"); 

				foreach($exfactory_res_val as $ex_row)
				{
					//for summery part
					$ex_cm_value_in=0; $ex_cm_value_inhouse=0; $ex_cm_value_outbound=0; $ex_sewing_qty_in=0; $ex_sewing_qty_inhouse=0; $ex_sewing_qty_outbound=0;
					
					$ex_sewing_qty_in=$ex_row[csf("ex_factory_qnty")];
					$ex_sewing_qty_inhouse=$ex_row[csf("ex_factory_qnty_inhouse")];
					$ex_sewing_qty_outbound=$ex_row[csf("ex_factory_qnty_outbound")];
					
					$job_no_ex=$ex_row[csf("job_no")];
					$total_ex_set_qnty=$ex_row[csf("total_set_qnty")];
					$costing_per_ex=$costing_per_arr[$job_no_ex];
					
					if($costing_per_ex==1) $dzn_qnty_ex=12;
					else if($costing_per_ex==3) $dzn_qnty_ex=12*2;
					else if($costing_per_ex==4) $dzn_qnty_ex=12*3;
					else if($costing_per_ex==5) $dzn_qdzn_qnty_exnty=12*4;
					else $dzn_qnty_ex=1;
								
					$dzn_qnty_ex=$dzn_qnty_ex*$total_ex_set_qnty;
					$ex_cm_value_in=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_in;
					// echo "(".$tot_cost_arr[$job_no_ex]."/".$dzn_qnty_ex.")*".$ex_sewing_qty_in."<br>";
					$ex_cm_value_inhouse=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_inhouse;
					$ex_cm_value_outbound=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_outbound;
					
					
					//for summary part
					$prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_cm_value_in']+=$ex_cm_value_in;
					$prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_cm_value_inhouse']+=$ex_cm_value_inhouse;
					$prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_cm_value_outbound']+=$ex_cm_value_outbound;
					$prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_smry']+=$ex_row[csf("ex_factory_qnty")];
					$prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_qnty_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")];
					$prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_qnty_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")];	
					$prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice']=$ex_row[csf("unit_price")]/$ex_row[csf("total_set_qnty")];
					$prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")]*($ex_row[csf("unit_price")]/$ex_row[csf("total_set_qnty")]);
					$prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")]*($ex_row[csf("unit_price")]/$ex_row[csf("total_set_qnty")]);
					$prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal']+=$ex_row[csf("ex_factory_qnty")]*($ex_row[csf("unit_price")]/$ex_row[csf("total_set_qnty")]);
					//end
						
					
					// end for summary part
				}

				/* $knited_query="SELECT a.buyer_id,a.knitting_source,c.quantity
				 from inv_receive_master a, pro_grey_prod_entry_dtls b , order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id $cbo_company_cond   and a.receive_date between '$date_from' and '$date_to'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.entry_form =2  and c.entry_form =2 "; */

				$knited_query="SELECT a.buyer_id,a.knitting_source,b.grey_receive_qnty as quantity
				from inv_receive_master a, pro_grey_prod_entry_dtls b  where a.id=b.mst_id $cbo_company_cond   and a.receive_date between '$date_from' and '$date_to'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.entry_form =2";
			   //echo $knited_query; die;
			
			    $knited_query_result=sql_select($knited_query);
				foreach( $knited_query_result as $knit_row)
				{
					$buyer_wise_kint_summary[$knit_row[csf("buyer_id")]][$knit_row[csf("knitting_source")]]+=$knit_row[csf("quantity")];			

				}
				// echo"<pre>"; print_r($buyer_wise_kint_summary);die;
				$finish_query="SELECT b.buyer_id,a.knitting_source,c.quantity from  inv_receive_master a, pro_finish_fabric_rcv_dtls b,order_wise_pro_details c  where a.id=b.mst_id and b.id = c.dtls_id $cbo_company_cond  and a.receive_date between '$date_from' and '$date_to' and a.entry_form=37  and c.entry_form =37 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
			//echo $finish_query; die;

			$finish_query_result=sql_select($finish_query);
			$count_finish=count($finish_query_result);

			foreach( $finish_query_result as $finish_row)
			{
				$buyer_wise_fin_summary[$finish_row[csf("buyer_id")]][$finish_row[csf("knitting_source")]]+=$finish_row[csf("quantity")];
			}
		   
			if($db_type==0)
			{
				$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
			}
			else
			{
				$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
			}
			//echo $manufacturing_company;
			$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
			// echo $smv_source;die;
			$po_id_con=where_con_using_array($po_id_array,0,"b.id");

			if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
			
			if($smv_source==3)
			{
				$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 
				and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_id_con";
				$resultItem=sql_select($sql_item);
				foreach($resultItem as $itemData)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
				}
			}
			else
			{
				$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_id_con";
				//echo $sql_item;
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
			//print_r($item_smv_array);

			$location_con = str_replace("a.location","a.location_id",$location_con);
			$smv_adjustment_sql="SELECT a.id,a.company_id, b.pr_date,b.ADJUSTMENT_SOURCE, sum(b.TOTAL_SMV) as TOTAL_SMV
			from prod_resource_mst a, prod_resource_smv_adj b
			where a.id=b.mst_id and a.is_deleted=0 and b.status_active=1 and b.adjustment_source in (9,10) $cbo_company_cond $location_con $floor_con and b.pr_date between '$date_from' and '$date_to' group by a.id,a.company_id, b.pr_date,b.adjustment_source ";
			// echo $smv_adjustment_sql;die;
			$smv_adjustment_data=sql_select($smv_adjustment_sql);
			// print_r($smv_adjustment_data);die;
			$smv_adjust_array = array();
			foreach($smv_adjustment_data as $row)
			{
				if($row["ADJUSTMENT_SOURCE"]==9)
				{
					$smv_adjust_array[$row[csf('id')]][$row[csf('pr_date')]]['smv_adjustment_plus']+=$row["TOTAL_SMV"];
				}
				if($row["ADJUSTMENT_SOURCE"]==10)
				{
					$smv_adjust_array[$row[csf('id')]][$row[csf('pr_date')]]['smv_adjustment_minus']+=$row["TOTAL_SMV"];
				}		
				// echo $row["TOTAL_SMV"]."sdfdsfds<br>";	
			}
			// echo"<pre>"; print_r($smv_adjust_array);die;
			$buyer_wise_min_array=array();
			$buyer_wise_prod_min_array=array();
			ksort($sewing_total_buyer_wise_array);
			foreach ($sewing_total_buyer_wise_array as $li_key => $l_data) 
			{
				foreach($l_data as $buyer_key=> $buyer_val)
				{
					foreach($buyer_val as $po_key=> $po_val)
					{
						foreach($po_val as $item_key=> $val)
						{
							$buyer_wise_min_array[$buyer_key]['earn_min']+=$val*$item_smv_array[$po_key][$item_key];
							// echo $li_key."=".$po_key."=".$item_key."==".$val."*".$item_smv_array[$po_key][$item_key]."<br>";
							$buyer_wise_prod_min_array[$buyer_key][$po_key][$item_key][$li_key]+=$val*$item_smv_array[$po_key][$item_key];
						}
					
					}

				}
			}
		// echo"<pre>"; print_r($buyer_wise_prod_min_array);die;
		// =========================== acctual resource data ========================
		$prod_resource_array=array();
		
		
		$dataArray_sql=("SELECT a.id, a.line_number,b.pr_date, b.man_power,b.smv_adjust,b.smv_adjust_type,b.working_hour from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_name $location_con $floor_con and b.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0");
		// echo $dataArray_sql;die;
		
		$data_arry=sql_select($dataArray_sql);
		
		foreach($data_arry as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
			if($val[csf('smv_adjust_type')]==1)
			{							
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			}
			if($val[csf('smv_adjust_type')]==2)
			{							
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')]*-1;
			}
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		 }
      	// echo"<pre>";print_r($prod_resource_array);die;		

		// ======================== shift wise line =========================
		
		$sql = "SELECT a.id,min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time,min(TO_CHAR(d.lunch_start_time,'HH24:MI')) as LUNCH_START_TIME,b.pr_date from prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d where a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($company_name) $location_con $floor_con and shift_id=1 and b.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 group by a.id,b.pr_date";
		// echo $sql;
		$res = sql_select($sql);
		$line_wise_shift_arr = array();
		$line_wise_shift_lunch_arr = array();

		foreach ($res as $val) 
		{
			$line_wise_shift_arr[$val['ID']][$val[csf('pr_date')]] = $val['LINE_START_TIME'];
			$line_wise_shift_lunch_arr[$val['ID']][$val[csf('pr_date')]] = $val['LUNCH_START_TIME'];
		}
		unset($res);
	 	// echo"<pre>";print_r($line_wise_shift_lunch_arr);die;		
	   	/* $production_data_arr=array();

		
		$dataArray_sum=("SELECT a.id,a.line_number,b.man_power,b.pr_date,b.smv_adjust,b.smv_adjust_type from  prod_resource_dtls b,prod_resource_mst a  where a.id=b.mst_id  and a.company_id=$company_name and b.pr_date between '$date_from' and '$date_to'  and a.is_deleted=0 and b.is_deleted=0  ");
		 //echo $dataArray_sum;die;
		
		$date_arr=sql_select($dataArray_sum);
		foreach($date_arr as $row)
		{			
			$production_data_arr[$row[csf('id')]][$row[csf('pr_date')]]['man_power']=$row[csf('man_power')];
			$production_data_arr[$row[csf('id')]][$row[csf('pr_date')]]['smv_adjust']=$row[csf('smv_adjust')]; 
			$production_data_ar[$row[csf('id')]][$row[csf('pr_date')]]['smv_adjust_type']=$row[csf('smv_adjust_type')]; 
			
		} */
		//echo"<pre>";print_r($production_data_arr);
		$buyer_wise_avai_min_array=array();
		$l_chk_array=array();
		$search_prod_date=change_date_format(str_replace("'","",$date_from));
		$current_date_time=date('d-m-Y H:i');
		$ex_date_time=explode(" ",$current_date_time);
		$current_date=$ex_date_time[0];
		$current_time=$ex_date_time[1];

		
		$actual_date=date("Y-m-d");
		
		$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
		$line_start_hour_arr[$hour_line]=$min_shif_start;
		// $actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));

		foreach ($prod_resource_array as $l_key => $l_value) 
		{
			foreach ($l_value as $dt_key => $r) 
			{
				// echo $dt_key;die;
				$lunch_start="";
				$lunch_start=$line_number_arr[$l_key][$pr_date]['lunch_start_time']; 
				$lunch_hour=$start_time_arr[$company_id][1]['lst']; 
				if($lunch_start!="") 
				{ 
					$lunch_start_hour=$lunch_start; 
				}
				else
				{
					$lunch_start_hour=$lunch_hour; 
				}

				$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$dt_key)));				
				// echo $production_data_arr[$l_key][$dt_key]['item_number_id']."<br>";
				$production_data = explode("****",$production_data_arr[$l_key][$dt_key]['item_number_id']);
				foreach ($production_data as $key => $val) 
				{
					if($l_chk_array[$l_key][$dt_key]=="")
					{
						// ============================================
						if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
						{
							
							$line_start=$line_number_arr[$resource_id][$pr_date]['prod_start_time'];
							
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
									$total_eff_hour=$total_eff_hour+1;
								}
							}
							//echo $total_eff_hour.'aaaa';
							if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
							
							if($total_eff_hour>$production_data_arr[$f_id][$resource_id]['working_hour'])
							{
								 $total_eff_hour=$production_data_arr[$f_id][$resource_id]['working_hour'];
							}
						}
						
						if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
						{
							for($ah=$hour;$ah<=$last_hour;$ah++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
								$line_production_hour+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								//echo $production_data_arr[$f_id][$ldata][$prod_hour];
								$line_floor_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								$line_total_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								$actual_time_hour=$start_hour_arr[$ah+1];
							}
							
							$total_eff_hour=$resource_data['working_hour'];	
						}
						// =============================================
						if($current_date==$search_prod_date)
						{

							$current_hour_min=date('H:i');
							$line_shift_hour_min=$line_wise_shift_arr[$l_key][$dt_key];
							$timeDiff=datediff("n",$line_shift_hour_min,$current_hour_min);
							$time_dif=number_format($timeDiff/60,2);
							if(strtotime(date('H:i'))>strtotime($line_wise_shift_lunch_arr[$l_key][$dt_key]) && $line_wise_shift_lunch_arr[$l_key][$dt_key]!="")
							{
								$line_wise_shift_lunch_h_m = $line_wise_shift_lunch_arr[$l_key][$dt_key]; 
								$lunchTimeDiff=datediff("n",$line_wise_shift_lunch_h_m,$current_hour_min);
								if($lunchTimeDiff>60)
								{
									$cla_cur_time=$time_dif-1;
								}
								else
								{
									$lunchMin=number_format($lunchTimeDiff/60,2);
									$cla_cur_time=$time_dif-$lunchMin;
								}
							}
							else
							{
								$cla_cur_time=$time_dif;
							}
						}
						else
						{
							$cla_cur_time=$r['working_hour'];
						}


						$ex_data = explode("**",$val);
						$efficiency_min=$r['smv_adjust']+($r['man_power']*$cla_cur_time*60);
						// echo $l_key."==".$efficiency_min."<br>";
						// echo $efficiency_min."==".$l_key."==".$r['smv_adjust']."+(".$r['man_power']."*".$cla_cur_time."*60)<br>";
						$produce_minit = $buyer_wise_prod_min_array[$ex_data[3]][$ex_data[0]][$ex_data[1]][$l_key];
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
						// echo $l_key."((".$produce_minit.")*100)/".$efficiency_min."<br>";
						$buyer_wise_avai_min_array[$ex_data[3]]['available_min']+=($r['smv_adjust']+($r['man_power']*$cla_cur_time*60));
						$l_chk_array[$l_key][$dt_key] = $dt_key;
						
						// echo $efficiency_min."<br>";
					}
				}
				// echo $l_key."==(".$r['smv_adjust']."+".$r['man_power'].")*8*60<br>";
			}
			
		}
		// echo "<pre>";print_r($buyer_wise_avai_min_array);die;
		$check_array = array();
		foreach($sewing_line_buyer_wise_array as $buyer_key=> $buyer_val )
		{
			foreach($buyer_val as $po_key=> $po_val)
			{
				foreach ($po_val as $item_key => $item_value) 
				{
					
					foreach($item_value as $line_key=> $lineval)
					{
						foreach($lineval as $pro_date=> $row)
						{
							$efficiency_min=($production_data_arr[$line_key][$pro_date]['smv_adjust'])+($production_data_arr[$line_key][$pro_date]['man_power'])*8*60;
							// echo $line_key."==(".$production_data_arr[$line_key][$pro_date]['smv_adjust'].")+(".$production_data_arr[$line_key][$pro_date]['man_power'].")*8*60<br>";
							// echo $line_key."==".$efficiency_min."<br>";
							$produce_minit = $buyer_wise_prod_min_array[$buyer_key][$po_key][$item_key][$line_key];
							$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							// echo "((".$produce_minit.")*100)/".$efficiency_min."<br>";
							// $buyer_wise_avai_min_array[$buyer_key]['available_min']+=$efficiency_min;

							if($check_array[$line_key][$pro_date]=="")
							{

								$buyer_wise_min_array[$buyer_key]['earn_min']+=$smv_adjust_array[$line_key][$pro_date]['smv_adjustment_plus'] - $smv_adjust_array[$line_key][$pro_date]['smv_adjustment_minus'];
								// echo $smv_adjust_array[$line_key][$pro_date]['smv_adjustment_plus'] ."-". $smv_adjust_array[$line_key][$pro_date]['smv_adjustment_minus']."<br>";
								// echo $line_key."==".$pro_date."<br>";
								$check_array[$line_key][$pro_date] = $line_key;
							}
						}

					}
				}

			}
		}
		// echo"<pre>";print_r($buyer_wise_min_array);

		?>
        <table width="2310px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
              <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:18px;">Group Name:<? $comp=explode(",",$company_name); echo $group_short_library[$company_group_library[$comp[0]]];?></td>
            </tr> 
             <tr>  
               <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:14px;"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:14px;"><strong> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
      <div>
        <table width="2650px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                     
				<thead>
					<tr>
						<th  width="30" rowspan="2">SL</th>
						<th  width="150" rowspan="2">Buyer Name</th>
						<th  colspan="3">Knitting Production</th>
						<th  colspan="3">Finish Fabrics Receive</th>
						<th  colspan="3">Cutting</th>
						<th  colspan="3">Sewing</th>
						<th  colspan="3">Finishing</th>
						<th  rowspan="2" width="80">Earn Min</th>
						<th  rowspan="2" width="80" title="smv adjust +(manpower*current hour*60)">Available Min</th>
						<th  width="80">Sewing CM Value</th>
						<th  colspan="3">FOB Value(On Sewing Qty)</th>
						<th  colspan="3">Ex-Factory Qty</th>
						<th  colspan="3">Ex-Factory CM Value</th>
						<th  colspan="3">FOB Value(On Ex-Factory Qty)</th>
					</tr>
					<tr>
						<th width="80">In House</th>
						<th width="80">Sub Contact</th>
						<th width="80">Total</th>

						<th width="80">In House</th>
						<th width="80">Sub Contact</th>
						<th width="80">Total</th>

						<th width="80">In House</th>
						<th width="80">Sub Contact</th>
						<th width="80">Total</th>

						<th width="80">In House</th>
						<th width="80">Sub Contact</th>
						<th width="80">Total</th>

						<th width="80">In House</th>
						<th width="80">Sub Contact</th>
						<th width="80">Total</th>

						<th width="80">In House</th>

						<th width="80">In House</th>
						<th width="80">Sub Contact</th>
						<th width="80">Total</th>

						<th width="80">In House</th>
						<th width="80">Sub Contact</th>
						<th width="80">Total</th>

						<th width="80">In House</th>
						<th width="80">Sub Contact</th>
						<th width="80">Total</th>

						<th width="80">In House</th>
						<th width="80">Sub Contact</th>
						<th >Total</th>
				</tr>
            </thead>
        </table>
		</div>	
		<div style="max-height:420px;  width:2650px" >
			<table cellspacing="0" border="1" class="rpt_table"  width="2650px" rules="all"  >
					<tbody>
						<?
						$i=1;
						$tot_kint_qnty_inhouse = 0;
						$tot_kint_qnty_outbond = 0;
						$tot_kint_qnty = 0;

						$tot_fin_qnty_inhouse = 0;
						$tot_fin_qnty_outbond = 0;
						$tot_fin_qnty = 0;

						$tot_cutting_qnty_inhouse = 0;
						$tot_cutting_qnty_outbound = 0;
						$tot_cutting_qnty = 0;

						$tot_sewingout_qnty_inhouse = 0;
						$tot_sewingout_qnty_outbound = 0;
						$tot_sewing_qnty = 0;

						$tot_finish_qnty_inhouse = 0;
						$tot_finish_qnty_outbound = 0;
						$tot_finish_qnty = 0;

						$tot_earn_min = 0;

						$tot_aval_min = 0;

						$tot_cm_value_in = 0;

						$tot_sewingout_value_inhouse = 0;
						$tot_sewingout_value_outbound = 0;
						$tot_fob_sew = 0;

						$tot_ex_qnty_inhouse = 0;
						$tot_ex_qnty_outbound = 0;
						$tot_ex_factory_smry = 0;

						$tot_ex_cm_value_inhouse = 0;
						$tot_ex_cm_value_outbound = 0;
						$tot_ex_cm_value_in = 0;

						$tot_ex_fobVal_inhouse = 0;
						$tot_ex_factory_unitPrice = 0;
						$tot_ex_fobVal = 0;
						
						
						foreach($prod_date_buyer_wise_summary as $buyerKey => $buyer_value)
						{
							
							?>
							<tr  bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
									<td width="30"><p><?=$i?></p></td>
									<td width="150"><p><?= $buyer_short_library[$buyerKey];?></p></td>
									<td width="80"  align="right"><p><?= number_format($buyer_wise_kint_summary[$buyerKey][1],0);?></p></td>
									<td width="80"  align="right"><p><?= number_format($buyer_wise_kint_summary[$buyerKey][3],0);?></p></td>
									<td width="80"  align="right"><p><?=number_format(($buyer_wise_kint_summary[$buyerKey][1])+($buyer_wise_kint_summary[$buyerKey][3]),0)?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_wise_fin_summary[$buyerKey][1],0);?></p></td>
									<td width="80"  align="right"><p><?= number_format($buyer_wise_fin_summary[$buyerKey][3],0);?></p></td>
									<td width="80"  align="right"><p><?=number_format(($buyer_wise_fin_summary[$buyerKey][1])+($buyer_wise_fin_summary[$buyerKey][3]),0)?></p></td>

									<td width="80" align="right"><p><?= number_format($buyer_value['cutting_qnty_inhouse'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['cutting_qnty_outbound'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['cutting_qnty'],0);?></p></td>

									<td width="80" align="right"><p><?= number_format($buyer_value['sewingout_qnty_inhouse'],0) ;?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['sewingout_qnty_outbound'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format ($buyer_value['sewing_qnty'],0);?></p></td>

									<td width="80" align="right"><p><?= number_format ($buyer_value['finish_qnty_inhouse'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['finish_qnty_outbound'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['finish_qnty'],0);?></p></td>

									<td width="80" align="right"><p><?= number_format($buyer_wise_min_array[$buyerKey]['earn_min'],2);?></p></td>
									<td width="80" align="right"  title="smv adjust +(manpower*current hour*60)"><p><?= number_format($buyer_wise_avai_min_array[$buyerKey]['available_min'],2);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['cm_value_in'],0);?></p></td>

									<td width="80" align="right"><p><?= number_format($buyer_value['sewingout_value_inhouse'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['sewingout_value_outbound'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['sewingout_value_inhouse'] + $buyer_value['sewingout_value_outbound'],0)?></p></td>

									<td width="80" align="right"><p><?= number_format($buyer_value['ex_factory_qnty_inhouse'],0) ;?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['ex_factory_qnty_outbound'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['ex_factory_smry'],0);?></p></td>

									<td width="80" align="right"><p><?= number_format($buyer_value['ex_cm_value_inhouse'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['ex_cm_value_outbound'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['ex_cm_value_in'],0);?></p></td>

									<td width="80" align="right"><p><?= number_format($buyer_value['ex_factory_smry_fobVal_inhouse'],0);?></p></td>
									<td width="80" align="right"><p><?= number_format($buyer_value['exfactory_unitPrice'],0);?></p></td>
									<td align="center"><p><?= number_format($buyer_value['ex_factory_smry_fobVal'],0);?></p></td>
									
						</tr>
						<?                      
													$i++;
													
													$tot_kint_qnty_inhouse += $buyer_wise_kint_summary[$buyerKey][1] ;
													$tot_kint_qnty_outbond += $buyer_wise_kint_summary[$buyerKey][3];
													$tot_kint_qnty += ($buyer_wise_kint_summary[$buyerKey][1])+($buyer_wise_kint_summary[$buyerKey][3]);
								
													$tot_fin_qnty_inhouse += $buyer_wise_fin_summary[$buyerKey][1] ;
													$tot_fin_qnty_outbond += $buyer_wise_fin_summary[$buyerKey][3];
													$tot_fin_qnty += (($buyer_wise_fin_summary[$buyerKey][1])+($buyer_wise_fin_summary[$buyerKey][3]));
								
													$tot_cutting_qnty_inhouse += $buyer_value['cutting_qnty_inhouse'];
													$tot_cutting_qnty_outbound += $buyer_value['cutting_qnty_outbound'];
													$tot_cutting_qnty += $buyer_value['cutting_qnty'];
								
													$tot_sewingout_qnty_inhouse += $buyer_value['sewingout_qnty_inhouse'];
													$tot_sewingout_qnty_outbound += $buyer_value['sewingout_qnty_outbound'];
													$tot_sewing_qnty += $buyer_value['sewing_qnty'];
								
													$tot_finish_qnty_inhouse += $buyer_value['finish_qnty_inhouse'];
													$tot_finish_qnty_outbound += $buyer_value['finish_qnty_outbound'];
													$tot_finish_qnty += $buyer_value['finish_qnty'];
								
													$tot_earn_min += $buyer_wise_min_array[$buyerKey]['earn_min'];
								
													$tot_aval_min += $buyer_wise_avai_min_array[$buyerKey]['available_min'];
								
													$tot_cm_value_in +=$buyer_value['cm_value_in'] ;
								
													$tot_sewingout_value_inhouse += $buyer_value['sewingout_value_inhouse'];
													$tot_sewingout_value_outbound += $buyer_value['ex_factory_qnty_outbound'];
													$tot_fob_sew += ($buyer_value['sewingout_value_inhouse'] + $buyer_value['sewingout_value_outbound']);
								
													$tot_ex_qnty_inhouse += $buyer_value['ex_factory_qnty_inhouse'];
													$tot_ex_qnty_outbound += $buyer_value['ex_factory_qnty_outbound'];
													$tot_ex_factory_smry += $buyer_value['ex_factory_smry'];
								
													$tot_ex_cm_value_inhouse += $buyer_value['ex_cm_value_inhouse'];
													$tot_ex_cm_value_outbound += $buyer_value['ex_cm_value_outbound'];;
													$tot_ex_cm_value_in += $buyer_value['ex_cm_value_in'];;
								
													$tot_ex_fobVal_inhouse += $buyer_value['ex_factory_smry_fobVal_inhouse'];;
													$tot_ex_factory_unitPrice += $buyer_value['exfactory_unitPrice'];;
													$tot_ex_fobVal += $buyer_value['ex_factory_smry_fobVal'];;
													
								}
								
						?>
						
					</tbody>

					<tfoot>
						<tr>
									<th width="30"><p></p></th>
									<th width="150">Total</th>

									<th width="80"><?= number_format($tot_kint_qnty_inhouse,0);?></th>
									<th width="80"><?= number_format($tot_kint_qnty_outbond,0);?></th>
									<th width="80"><?= number_format($tot_kint_qnty,0);?></th>

									<th width="80"><?= number_format($tot_fin_qnty_inhouse,0);?></th>
									<th width="80"><?= number_format($tot_fin_qnty_outbond,0);?></th>
									<th width="80"><?= number_format($tot_fin_qnty,0);?></th>

									<th width="80"><p><?= number_format($tot_cutting_qnty_inhouse,0);?></p></th>
									<th width="80"><p><?= number_format($tot_cutting_qnty_outbound,0);?></p></th>
									<th width="80"><p><?= number_format($tot_cutting_qnty,0);?></p></th>

									<th width="80"><p><?= number_format($tot_sewingout_qnty_inhouse,0);?></p></th>
									<th width="80"><p><?= number_format($tot_sewingout_qnty_outbound,0);?></p></th>
									<th width="80"><p><?= number_format($tot_sewing_qnty,0);?></p></th>

									<th width="80"><p><?= number_format($tot_finish_qnty_inhouse,0);?></p></th>
									<th width="80"><p><?= number_format($tot_finish_qnty_outbound,0);?></p></th>
									<th width="80"><p><?= number_format($tot_finish_qnty,0);?></p></th>

									<th width="80"><p><?= number_format($tot_earn_min,2); ?></p></th>
									<th width="80"><p><?=number_format($tot_aval_min,2);?></p></th>

									<th width="80"><p><?= number_format($tot_cm_value_in,0);?></p></th>

									<th width="80"><p><?= number_format($tot_sewingout_value_inhouse,0);?></p></th>
									<th width="80"><p><?= number_format($tot_sewingout_value_outbound,0);?></p></th>
									<th width="80"><p><?= number_format($tot_fob_sew,0);?></p></th>

									<th width="80"><p><?= number_format($tot_ex_qnty_inhouse,0);?></p></th>
									<th width="80"><p><?= number_format($tot_ex_qnty_outbound,0);?></p></th>
									<th width="80"><p><?= number_format($tot_ex_factory_smry,0);?></p></th>

									<th width="80"><p><?= number_format($tot_ex_cm_value_inhouse,0);?></p></th>
									<th width="80"><p><?= number_format($tot_ex_cm_value_outbound,0);?></p></th>
									<th width="80"><p><?= number_format($tot_ex_cm_value_in,0);?></p></th>

									<th width="80"><p><?= number_format($tot_ex_fobVal_inhouse,0);?></p></th>
									<th width="80"><p><?= number_format($tot_ex_factory_unitPrice,0);?></p></th>
									<th style="text-align: center;"><p><?= number_format($tot_ex_fobVal,0);?></p></th>
							</tr>		
					</tfoot>
					
			</table>
		</div>
         <br/>
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