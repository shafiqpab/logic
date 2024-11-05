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
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.party_type not in('2') order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'daily_cutting_inhand_report_urmi_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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

if($action=="job_wise_search")
{
	/*
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
		<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
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
	*/
}//JobNumberShow


//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view_po' ).rows.length;
			// var tbl_row_count =  $('#list_view_po tr:visible').length;
			// alert(tbl_row_count);return;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($('#tr_' + i).is(":visible"))
				{					
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
				
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
	echo create_list_view("list_view_po", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view_po',-1)","0","",1) ;	
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
	$hidden_order_id=str_replace("'","",$hidden_order_id);
	//$txt_production_date=str_replace("'","",$txt_production_date);
	$job_po_id="";
	if(str_replace("'","",$txt_job_no)!="")
	{
		if($db_type==0)
		{
			$job_po_id=return_field_value("group_concat(b.id) as po_id","wo_po_break_down b","b.job_no_mst=$txt_job_no","po_id");
		}
		else
		{
			$job_po_id=return_field_value("listagg(cast(b.id as varchar(4000)),',') within group(order by id) as po_id","wo_po_break_down b","b.job_no_mst=$txt_job_no","po_id");
		}
	}
	
	//echo $job_po_id;die;
	
	$order_cond_lay="";
	$order_cond_prod="";
	
	if($hidden_order_id!="")
	{
		$production_po_id_arr=explode(",", $hidden_order_id);
        if(count($production_po_id_arr)>999 && $db_type==2)
        {
         	$po_chunk=array_chunk($production_po_id_arr, 999);
         	$order_cond= "";
         	$order_cond_lay= "";
         	foreach($po_chunk as $vals)
         	{
         		$imp_ids=implode(",", $vals);
         		if($order_cond=="") 
         		{
         			$order_cond.=" and ( a.po_break_down_id in ($imp_ids) ";
         		}
         		else
         		{
         			$order_cond.=" or   a.po_break_down_id in ($imp_ids) ";
         		}
         		//======================

         		if($order_cond_lay=="") 
         		{
         			$order_cond_lay.=" and ( c.order_id in ($imp_ids) ";
         		}
         		else
         		{
         			$order_cond_lay.=" or   c.order_id in ($imp_ids) ";
         		}

         	}
         	$order_cond.=" )";
         	$order_cond_lay.=" )";

        }
        else
        {
         	$order_cond.=" and a.po_break_down_id in($hidden_order_id)";
			$order_cond_lay.=" and c.order_id in($hidden_order_id)";
        }

		
	}
	elseif($job_po_id!="")
	{
		$order_cond.=" and a.po_break_down_id in($job_po_id)";
		$order_cond_lay.=" and c.order_id in($job_po_id)";
	}
	// echo $order_cond."<br>";
	// echo $order_cond_lay."<br>";
	// die();
	/*$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);
	if($cbo_shipping_status!=0){ $shipping_status_cond="and b.shiping_status in($cbo_shipping_status)";}else{ $shipping_status_cond="";}*/
	
	$cbo_work_company_name=str_replace("'","",$cbo_work_company_name);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
	$cbo_floor_name=str_replace("'","",$cbo_floor_name);
	$gmts_loc_floor_cond="";
	$gmts_loc_floor_cond_cut="";
	$gmts_loc_floor_cond_delv="";
	$gmts_loc_floor_cond_exfac="";
	if($cbo_location_name)
	{
		$gmts_loc_floor_cond_cut.= " and a.location_id in($cbo_location_name)  ";
		$gmts_loc_floor_cond_delv.= " and m.location_id in($cbo_location_name)  ";
		$gmts_loc_floor_cond.= " and a.location in($cbo_location_name)  ";
		$gmts_loc_floor_cond_exfac.= " and m.delivery_location_id in($cbo_location_name)  ";
	}
	
	if($cbo_floor_name)
	{
		$gmts_loc_floor_cond.= " and a.floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_cut.= " and a.floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_delv.= " and m.floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_exfac.= " and m.delivery_floor_id in ($cbo_floor_name) ";
	}
	 

	if(str_replace("'","",trim($txt_file_no))!="") $file_no=" and LOWER(b.file_no) = LOWER('".str_replace("'","",trim($txt_file_no))."')"; else $file_no="";
	if(str_replace("'","",trim($txt_int_ref_no))!="") $ref_no=" and LOWER(b.grouping) = LOWER('".str_replace("'","",trim($txt_int_ref_no))."')"; else $ref_no="";			
			 
	if($type==1)
	{
			$sql_lay=" SELECT a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, c.size_qty as production_qnty  
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
			where a.id=b.mst_id and b.id=c.dtls_id   and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_cut and entry_date=$txt_production_date $order_cond_lay  "; //			
			
			//echo $sql_lay;
			
			$sql_lay_result=sql_select($sql_lay);
			$production_data=$porduction_ord_id=$lay_order_id=array();
			$garments_order_id_arr=array();
			foreach($sql_lay_result as $row)
			{
				if($row[csf("production_qnty")]>0)
				{
					$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$lay_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["lay_qnty"]+=$row[csf("production_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("production_qnty")];
				}
			}
			// ============================== For Cut and Lay Entry Roll Wise entry form =============================================
			$sql_lay=" SELECT a.company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, c.size_qty as production_qnty  
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
			where a.id=b.mst_id and b.id=c.dtls_id   and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form=77 and a.company_id in($cbo_work_company_name) $gmts_loc_floor_cond_cut and entry_date=$txt_production_date $order_cond_lay  "; //			
			
			//echo $sql_lay;
			
			$sql_lay_result=sql_select($sql_lay);
			// $production_data=$porduction_ord_id=$lay_order_id=array();
			foreach($sql_lay_result as $row)
			{
				if($row[csf("production_qnty")]>0)
				{
					$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$lay_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["lay_qnty"]+=$row[csf("production_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("production_qnty")];
				}
			}
			
			
				$production_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id, sum(b.production_qnty) as all_today_production_qnty,
				sum(CASE WHEN b.production_type =1 and a.production_type =1 THEN b.production_qnty  ELSE 0 END) AS cutting_qnty,
				sum(CASE WHEN b.production_type =1 and a.production_type =1 THEN b.replace_qty  ELSE 0 END) AS cutting_replace_qty,
				sum(CASE WHEN b.production_type =1 and a.production_type =1 THEN b.reject_qty ELSE 0 END) AS cutting_reject_qty,
				sum(CASE WHEN b.production_type =4 and a.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_qnty,
				sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty,
				sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.reject_qty ELSE 0 END) AS sewing_reject_qty,
				sum(CASE WHEN b.production_type =7 and a.production_type =7  THEN b.production_qnty ELSE 0 END) AS iron_qnty,
				sum(CASE WHEN b.production_type =8 and a.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_qnty,
				sum(CASE WHEN b.production_type =8 and a.production_type =8  THEN b.reject_qty ELSE 0 END) AS paking_finish_reject_qty,
				sum(CASE WHEN b.production_type =11 and a.production_type=11 THEN b.production_qnty ELSE 0 END) AS poly_qnty,
				sum(CASE WHEN b.production_type =11 and a.production_type =11  THEN b.reject_qty ELSE 0 END) AS poly_reject_qty 
				from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
				where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type in(1,4,5,7,8,11) and a.production_type in(1,4,5,7,8,11) and a.serving_company in($cbo_work_company_name) $gmts_loc_floor_cond and a.production_date=".$txt_production_date." $order_cond
				group by a.production_date, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id";

			
			 //echo $production_sql;// die;
			
			$production_sql_result=sql_select($production_sql);
			foreach($production_sql_result as $row)
			{
				if($row[csf("all_today_production_qnty")]>0)
				{
					if($garments_order_id_arr[$row[csf("order_id")]]=="")
					{
						$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					}
					$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$gmt_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_qnty"]+=$row[csf("cutting_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_replace_qty"]+=$row[csf("cutting_replace_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_reject_qty"]+=$row[csf("cutting_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_in_qnty"]+=$row[csf("sewing_in_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_out_qnty"]+=$row[csf("sewing_out_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_reject_qty"]+=$row[csf("sewing_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["iron_qnty"]+=$row[csf("iron_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_qnty"]+=$row[csf("paking_finish_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_reject_qty"]+=$row[csf("paking_finish_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_qnty"]+=$row[csf("poly_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_reject_qty"]+=$row[csf("poly_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("all_today_production_qnty")];
				}
				
			}
			
				$print_embro_sql="SELECT a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, sum(b.production_qnty) as all_today_production_qnty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS printing_reject_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS embroidery_reject_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS wash_reject_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.reject_qty ELSE 0 END) AS sp_work_reject_qty
				from  pro_garments_production_dtls b, pro_garments_production_mst a, wo_po_color_size_breakdown c 
				where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and b.production_type in(2,3) and a.production_type in(2,3) and a.serving_company in($cbo_work_company_name) $gmts_loc_floor_cond and a.production_date=".$txt_production_date." $order_cond
				group by a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
			
			//echo $print_embro_sql;die;
			
			$print_embro_sql_result=sql_select($print_embro_sql);
			$print_embro_order_id=array();
			foreach($print_embro_sql_result as $row)
			{
				if($row[csf("all_today_production_qnty")]>0)
				{
					if($garments_order_id_arr[$row[csf("order_id")]]=="")
					{
						$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					}
					
					$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$print_embro_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_qnty"]+=$row[csf("printing_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_rcv_qnty"]+=$row[csf("printing_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_reject_qty"]+=$row[csf("printing_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_qnty"]+=$row[csf("embroidery_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_rcv_qnty"]+=$row[csf("embroidery_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_reject_qty"]+=$row[csf("embroidery_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_qnty"]+=$row[csf("wash_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_rcv_qnty"]+=$row[csf("wash_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_reject_qty"]+=$row[csf("wash_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_qnty"]+=$row[csf("sp_work_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_rcv_qnty"]+=$row[csf("sp_work_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_reject_qty"]+=$row[csf("sp_work_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("all_today_production_qnty")];
				}
				
			}

				$ex_factory_sql="SELECT m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, sum(b.production_qnty) as ex_fact_qnty 
				from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
				where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and m.delivery_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_exfac and m.delivery_date=".$txt_production_date." $order_cond
				group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
			
			
			//echo $ex_factory_sql;//die;
			$ex_factory_sql_result=sql_select($ex_factory_sql);
			foreach($ex_factory_sql_result as $row)
			{
				if($row[csf("ex_fact_qnty")]>0)
				{
					if($garments_order_id_arr[$row[csf("order_id")]]=="")
					{
						$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					}
					
					$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$ex_fact_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["ex_fact_qnty"]+=$row[csf("ex_fact_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("ex_fact_qnty")];
				}
			}
			
			
			$order_prev_con=" and";
			$garments_order_arr=array_chunk($garments_order_id_arr,999);
			foreach($garments_order_arr as $order_data)
			{
				if($order_prev_con==" and")
				{
					$order_prev_con .="  ( c.order_id in(".implode(',',$order_data).")";
				}
				else
				{
					$order_prev_con .=" or c.order_id in(".implode(',',$order_data).")";
				}
			}
			$order_prev_con .=")";
			
			// previous data
			//$lay_order_id=implode(',',$lay_order_id);
			if(count($garments_order_id_arr)>0)
			{
				$sql_lay_prev=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, c.size_qty as production_qnty  
				from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
				where a.id=b.mst_id and b.id=c.dtls_id   and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_cut and a.entry_date<$txt_production_date $order_prev_con";
				
				//echo $sql_lay_prev;die;
				
				$sql_lay_prev_result=sql_select($sql_lay_prev);
				foreach($sql_lay_prev_result as $row)
				{
					$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["lay_prev_qnty"]+=$row[csf("production_qnty")];
				}

				// ============================= For Cut and Lay Entry Roll Wise entry form =================================
				$sql_lay_prev=" SELECT a.company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, c.size_qty as production_qnty  
				from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
				where a.id=b.mst_id and b.id=c.dtls_id   and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.company_id in($cbo_work_company_name) $gmts_loc_floor_cond_cut and a.entry_date<$txt_production_date $order_prev_con and a.entry_form=77";
				
				//echo $sql_lay_prev;die;
				
				$sql_lay_prev_result=sql_select($sql_lay_prev);
				foreach($sql_lay_prev_result as $row)
				{
					$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["lay_prev_qnty"]+=$row[csf("production_qnty")];
				}
			}
			
			//echo "<pre>";
			//print_r($production_data);die;
			
			$order_prev_con_prod=" and";
			foreach($garments_order_arr as $order_data)
			{
				if($order_prev_con_prod==" and")
				{
					$order_prev_con_prod .="  ( a.po_break_down_id in(".implode(',',$order_data).")";
				}
				else
				{
					$order_prev_con_prod .=" or a.po_break_down_id in(".implode(',',$order_data).")";
				}
			}
			$order_prev_con_prod .=")";
			
			//$gmt_order_id=implode(',',$gmt_order_id);
			if(count($garments_order_id_arr)>0)
			{
				$production_prev_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id,
				sum(CASE WHEN b.production_type =1 THEN b.production_qnty ELSE 0 END) AS cutting_prev_qnty,
				sum(CASE WHEN b.production_type =1 THEN b.replace_qty ELSE 0 END) AS cutting_prev_replace_qnty,
				sum(CASE WHEN b.production_type =1 THEN b.reject_qty ELSE 0 END) AS cutting_reject_prev_qty,
				sum(CASE WHEN b.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_prev_qnty,
				sum(CASE WHEN b.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_prev_qnty,
				sum(CASE WHEN b.production_type =5 THEN b.reject_qty ELSE 0 END) AS sewing_reject_prev_qty,
				sum(CASE WHEN b.production_type =7 THEN b.production_qnty ELSE 0 END) AS iron_prev_qnty,
				sum(CASE WHEN b.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_prev_qnty,
				sum(CASE WHEN b.production_type =8 THEN b.reject_qty ELSE 0 END) AS paking_finish_reject_prev_qty,
				sum(CASE WHEN b.production_type =11 THEN b.production_qnty ELSE 0 END) AS poly_prev_qnty,
				sum(CASE WHEN b.production_type =11 THEN b.reject_qty ELSE 0 END) AS poly_reject_prev_qty 
				from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
				where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.production_source=1 and b.production_type in(1,4,5,7,8,11) and a.production_type in(1,4,5,7,8,11) and a.serving_company in($cbo_work_company_name) $gmts_loc_floor_cond and a.production_date<".$txt_production_date." and b.status_active=1 and b.is_deleted=0   $order_prev_con_prod
				group by a.production_date, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id";
				
				//echo $production_prev_sql;die;
				
				$production_prev_sql_result=sql_select($production_prev_sql);
				foreach($production_prev_sql_result as $row)
				{
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_prev_qnty"]+=$row[csf("cutting_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_prev_replace_qnty"]+=$row[csf("cutting_prev_replace_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_reject_prev_qty"]+=$row[csf("cutting_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_in_prev_qnty"]+=$row[csf("sewing_in_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_out_prev_qnty"]+=$row[csf("sewing_out_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_reject_prev_qty"]+=$row[csf("sewing_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["iron_prev_qnty"]+=$row[csf("iron_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_prev_qnty"]+=$row[csf("paking_finish_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_reject_prev_qty"]+=$row[csf("paking_finish_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_prev_qnty"]+=$row[csf("poly_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_reject_prev_qty"]+=$row[csf("poly_reject_prev_qty")];
				}
			}
			
			
			
			
			
			//$print_embro_order_id=implode(',',$print_embro_order_id);
			if(count($garments_order_id_arr)>0)
			{
				$print_embro_prev_sql="SELECT m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id,
				sum(CASE WHEN b.production_type =2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_rcv_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS printing_reject_prev_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_rcv_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS embroidery_reject_prev_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_rcv_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS wash_reject_prev_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_rcv_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.reject_qty ELSE 0 END) AS sp_work_reject_prev_qty
				from  pro_gmts_delivery_mst m, pro_garments_production_dtls b, pro_garments_production_mst a, wo_po_color_size_breakdown c 
				where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and b.production_type in(2,3) and a.production_type in(2,3) and m.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_delv and m.delivery_date<".$txt_production_date." $order_prev_con_prod
				group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
				
				//echo $print_embro_prev_sql;die;
				
				$print_embro_sql_result=sql_select($print_embro_prev_sql);
				foreach($print_embro_sql_result as $row)
				{
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_prev_qnty"]+=$row[csf("printing_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_rcv_prev_qnty"]+=$row[csf("printing_rcv_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_reject_prev_qty"]+=$row[csf("printing_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_prev_qnty"]+=$row[csf("embroidery_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_rcv_prev_qnty"]+=$row[csf("embroidery_rcv_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_reject_prev_qty"]+=$row[csf("embroidery_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_prev_qnty"]+=$row[csf("wash_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_rcv_prev_qnty"]+=$row[csf("wash_rcv_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_reject_prev_qty"]+=$row[csf("wash_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_prev_qnty"]+=$row[csf("sp_work_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_rcv_prev_qnty"]+=$row[csf("sp_work_rcv_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_reject_prev_qty"]+=$row[csf("sp_work_reject_prev_qty")];
				}
			}
			
			
			//$ex_fact_order_id=implode(",",$ex_fact_order_id);
			if(count($garments_order_id_arr)>0)
			{
				$ex_factory_prev_sql="SELECT m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, sum(b.production_qnty) as ex_fact_qnty 
				from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
				where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and m.delivery_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_exfac and m.delivery_date<".$txt_production_date." $order_prev_con_prod
				group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
				//echo $ex_factory_prev_sql;die;
				$ex_factory_prev_sql_result=sql_select($ex_factory_prev_sql);
				foreach($ex_factory_prev_sql_result as $row)
				{
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["ex_fact_prev_qnty"]+=$row[csf("ex_fact_qnty")];
				}
			}
			
			
			
			if($db_type==0)
			{
				$select_year=" year(a.insert_date) as job_year";
			}
			else
			{
				$select_year=" to_char(a.insert_date,'YYYY') as job_year";
			}
			$buyer_cond="";
			if(str_replace("'","",$cbo_buyer_name)>0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name";
			$porduction_ord_id=implode(",",$porduction_ord_id);
			
			$pord_ord_ids=explode(",",$porduction_ord_id);  
			$pord_ord_ids=array_chunk($pord_ord_ids,999);
			$po_qry_cond=" and";
			foreach($pord_ord_ids as $dtls_id)
			{
			if($po_qry_cond==" and")  $po_qry_cond.="(c.po_break_down_id in(".implode(',',$dtls_id).")"; else $po_qry_cond.=" or c.po_break_down_id in(".implode(',',$dtls_id).")";
			}
			$po_qry_cond.=")";
			//echo $po_qry_cond;die;
			//echo  $po_qry_cond="select work_order_id , sum(quantity) as quantity from com_pi_item_details where work_order_dtls_id>0 and status_active=1 and is_deleted=0 $po_qry_cond group by work_order_id";
			if($porduction_ord_id!="")
			{
				$sql_color_size=sql_select("SELECT a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, $select_year, b.po_number,b.file_no,b.grouping, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity,b.id as po_id 
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 $po_qry_cond $buyer_cond $file_no $ref_no");
				$order_color_data=array();
				foreach($sql_color_size as $row)
				{
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_no"]=$row[csf("job_no")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_year"]=$row[csf("job_year")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["country_ship_date"]=$row[csf("country_ship_date")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["file_no"]=$row[csf("file_no")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];

					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["po_id"]=$row[csf("po_id")];
				}
			}
			
			//echo $sql_color_size;die;
			   
			ob_start();
		 ?>
		  <fieldset style="width:5980px;">
		  <div style="width:5980px;">
		  	<table width="2040"  cellspacing="0"   >
		            <tr class="form_caption" style="border:none;">
		                   <td colspan="31" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report</td>
		             </tr>
		            <tr style="border:none;">
		                    <td colspan="31" align="center" style="border:none; font-size:16px; font-weight:bold">
		                    Working Company Name:<? 
							$cbo_work_company_name_arr=explode(",",$cbo_work_company_name);
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
		                    <td colspan="31" align="center" style="border:none;font-size:12px; font-weight:bold">
		                    <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
		                    </td>
		              </tr>
		        </table>
		     <br />
		     
		  	<fieldset style="width:6050px; float:left;">
		    <legend>Report Details Part</legend>
		     <table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="6070" class="rpt_table" align="left">
		        <thead>
		            <tr >
		                <th width="40" rowspan="2">SL</th>
		                <th width="100" rowspan="2">Buyer</th>
		                <th width="100" rowspan="2">Style Ref</th>
		                <th width="60" rowspan="2">Job No</th>
		                <th width="50" rowspan="2">Year</th>
		                <th width="100" rowspan="2">Order No</th>
		                <th width="80" rowspan="2">File No</th>
		                <th width="80" rowspan="2">Internal Ref</th>
		                <th width="100" rowspan="2">Country</th>
		                <th width="70" rowspan="2">Country Shipdate</th>
		                <th width="100" rowspan="2">Garment Item</th>
		                <th width="100" rowspan="2">Color</th>
		                <th width="70" rowspan="2">Order Qty.</th>
		                
		                <th width="210" colspan="3">Lay Quantity</th>
		                <th width="210" colspan="3">Cutting QC</th>
		                
		                <th width="70" rowspan="2">Today Cutting Reject</th>
		                <th width="70" rowspan="2">Cutting Reject Total</th>
		                <th width="70" rowspan="2">Cutting Replace Total</th>
		                <th width="70" rowspan="2">QC WIP</th>
		                
		                <th width="210" colspan="3">Delivery to Print</th>
		                <th width="210" colspan="3">Receive from Print</th>
		                
		                <th width="70" rowspan="2">Today Printing Reject</th>
		                <th width="70" rowspan="2">Printing Reject Total</th>
		                <th width="70" rowspan="2">Printing WIP</th>
		                
		                <th width="210" colspan="3">Delivery to Emb.</th>
		                <th width="210" colspan="3">Receive from Emb.</th>
		                
		                <th width="70" rowspan="2">Today Emb. Reject</th>
		                <th width="70" rowspan="2">Emb. Reject Total</th>
		                <th width="70" rowspan="2">Emb. WIP</th>
		                
		                <th width="210" colspan="3">Delivery to Wash</th>
		                <th width="210" colspan="3">Receive from Wash</th>
		                
		                <th width="70" rowspan="2">Today Wash Reject</th>
		                <th width="70" rowspan="2">Wash Reject Total</th>
		                <th width="70" rowspan="2">Wash WIP</th>
		                
		                <th width="210" colspan="3">Delivery to S.Work</th>
		                <th width="210" colspan="3">Receive from S.Work</th>
		                
		                <th width="70" rowspan="2">Today S. Work Reject</th>
		                <th width="70" rowspan="2">S. Work Reject Total</th>
		                <th width="70" rowspan="2">S.Works WIP</th>
		                
		                <th width="210" colspan="3">Sewing Input</th>
		                <th width="210" colspan="3">Sewing Output</th>
		                
		                <th width="70" rowspan="2">Today Sewing Reject</th>
		                <th width="70" rowspan="2">Sewing Reject Total</th>
		                <th width="70" rowspan="2">Sewing WIP</th>
		                
		                <th width="210" colspan="3">Poly Entry</th>
		                
		                <th width="70" rowspan="2">Today Poly Reject</th>
		                <th width="70" rowspan="2">Poly Reject Total</th>
		                <th width="70" rowspan="2">Poly WIP</th>
		                
		                <th width="210" colspan="3">Packing & Finishing</th>
		                
		                <th width="70" rowspan="2">Today Finishing Reject</th>
		                <th width="70" rowspan="2">Finishing Reject Total</th>
		                <th width="70" rowspan="2">Pac &Fin. WIP</th>
		                <th width="210" colspan="3">Ex-Factory</th>
		                <th width="70" rowspan="2">Ex-Fac. WIP</th>
		            </tr>
		            <tr>
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		            </tr>
		        </thead>
		    </table>
		    <div style="max-height:425px; overflow-y:scroll; width:6090px;" id="scroll_body">
		    <table  border="1" class="rpt_table"  width="6070" rules="all" id="table_body" >
		        <tbody>
		        <?
				//echo "<pre>";print_r($production_data);die;
				$i=1;
				foreach($order_color_data as $buyer_id=>$buyer_data)
				{
					foreach($buyer_data as $job_no=>$job_data)
					{
						foreach($job_data as $order_id=>$order_data)
						{
							foreach($order_data as $item_id=>$item_data)
							{
								foreach($item_data as $country_id=>$country_data)
								{
									foreach($country_data as $color_id=>$value)
									{
										
										if(($production_data[$order_id][$item_id][$country_id][$color_id]["today_production"]>0))
										{
											$tot_lay_qnty=$tot_cutting_qnty=$tot_printing_qnty=$tot_printing_rcv_qnty=$tot_embroidery_qnty=$tot_embroidery_rcv_qnty=$tot_wash_qnty=$tot_wash_rcv_qnty=$tot_sp_work_qnty=$tot_sp_work_rcv_qnty=$tot_sewing_in_qnty=$tot_sewing_out_qnty=$tot_poly_qnty=$tot_paking_finish_qnty=$tot_ex_fact_qnty=0;
											$cut_qc_wip=$printing_wip=$emb_wip=$wash_wip=$sp_work_wip=$sewing_wip=$poly_wip=$finishing_wip=$ex_fact_wip=$ex_fact_wip=0;
											$total_cutting_reject=$total_cutting_replace=$total_printing_reject=$total_embroidery_reject=$total_wash_reject=$total_sp_work_reject=$total_sewing_reject=$total_poly_reject=$total_finish_reject=0;
											$po_id=$value['po_id'];
											
											if ($i%2==0)
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";
											?>
		                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
		                                        <td width="40" align="center"><? echo $i; ?></td>
		                                        <td width="100"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
		                                        <td width="100"><p><? echo $value["style_ref_no"]; ?>&nbsp;</p></td>
		                                        <td width="60" align="center"><p><? echo $value["job_no_prefix_num"]; ?>&nbsp;</p></td>
		                                        <td width="50" align="center"><p><? echo $value["job_year"]; ?>&nbsp;</p></td>
		                                        <td width="100"><p><? echo $value["po_number"]; ?>&nbsp;</p></td>
		                                        <td width="80"><p><? echo $value["file_no"]; ?>&nbsp;</p></td>
		                                        <td width="80"><p><? echo $value["grouping"]; ?>&nbsp;</p></td>
		                                        <td width="100"><p><? echo $country_arr[$country_id]; ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($value["country_ship_date"]!="" && $value["country_ship_date"]!='0000-00-00') echo change_date_format($value["country_ship_date"]); ?>&nbsp;</p></td>
		                                        <td width="100"><p><? echo $garments_item[$item_id]; ?>&nbsp;</p></td>
		                                        <td width="100"><p><? echo $colorname_arr[$color_id]; ?>&nbsp;</p></td>
		                                        <td width="70" align="right"><? echo number_format($value["order_quantity"],0); $job_order_qnty+=$value["order_quantity"];$buyer_order_qnty+=$value["order_quantity"]; $gt_order_qnty+=$value["order_quantity"]; ?></td>
		                                        
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["lay_prev_qnty"],0); $job_lay_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_prev_qnty"]; $buyer_lay_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_prev_qnty"]; $gt_lay_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["lay_qnty"],0); $job_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_qnty"]; $buyer_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_qnty"]; $gt_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_lay_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["lay_qnty"]; echo number_format($tot_lay_qnty,0); $job_tot_lay_qnty+=$tot_lay_qnty; $buyer_tot_lay_qnty+=$tot_lay_qnty; $gt_tot_lay_qnty+=$tot_lay_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"],0); $job_cutting_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"]; $buyer_cutting_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"]; $gt_cutting_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"]; ?></td>
		                                        
		                                        
		                                        
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"],0); $job_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"]; $buyer_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"]; $gt_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"];?></td>
		                                        
		                                        
		                                        
		                                        <td width="70" align="right"><? $tot_cutting_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"]; echo number_format($tot_cutting_qnty,0); $job_tot_cutting_qnty+=$tot_cutting_qnty; $buyer_tot_cutting_qnty+=$tot_cutting_qnty; $gt_tot_cutting_qnty+=$tot_cutting_qnty;?></td>
		                                        
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"],0); $job_cutting_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"]; $buyer_cutting_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"]; $gt_cutting_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"];?></td>

		                                        <td width="70" align="right"><? $total_cutting_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_prev_qty"]; echo number_format($total_cutting_reject,0); $job_total_cutting_reject+=$total_cutting_reject; $buyer_total_cutting_reject+=$total_cutting_reject; $gt_total_cutting_reject+=$total_cutting_reject;?></td>

		                                         <td width="70" align="right"><? $total_cutting_replace=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_replace_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_replace_qnty"]; echo number_format($total_cutting_replace,0); $job_total_cutting_replace+=$total_cutting_replace; $buyer_total_cutting_replace+=$total_cutting_replace; $gt_total_cutting_replace+=$total_cutting_replace;?></td>

		                                        <td width="70" align="right"><? $cut_qc_wip=($tot_lay_qnty-$tot_cutting_qnty)+($total_cutting_reject-$total_cutting_replace); echo number_format($cut_qc_wip,0); $job_cut_qc_wip+=$cut_qc_wip; $buyer_cut_qc_wip+=$cut_qc_wip; $gt_cut_qc_wip+=$cut_qc_wip;  ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"],0); $job_printing_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]; $buyer_printing_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]; $gt_printing_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"],0); $job_printing_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"]; $buyer_printing_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"]; $gt_printing_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_printing_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"]; echo number_format($tot_printing_qnty,0); $job_tot_printing_qnty+=$tot_printing_qnty; $buyer_tot_printing_qnty+=$tot_printing_qnty; $gt_tot_printing_qnty+=$tot_printing_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"],0); $job_printing_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]; $buyer_printing_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]; $gt_printing_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"],0); $job_printing_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"]; $buyer_printing_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"]; $gt_printing_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_printing_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"]; echo number_format($tot_printing_rcv_qnty,0); $job_tot_printing_rcv_qnty+=$tot_printing_rcv_qnty; $buyer_tot_printing_rcv_qnty+=$tot_printing_rcv_qnty; $gt_tot_printing_rcv_qnty+=$tot_printing_rcv_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"],0); $job_printing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"]; $buyer_printing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"]; $gt_printing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_printing_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_prev_qty"]; echo number_format($total_printing_reject,0); $job_total_printing_reject+=$total_printing_reject; $buyer_total_printing_reject+=$total_printing_reject; $gt_total_printing_reject+=$total_printing_reject;?></td>
		                                        <td width="70" align="right"><? $printing_wip=(($tot_printing_rcv_qnty+$total_printing_reject)-$tot_printing_qnty); echo number_format($printing_wip,0); $job_printing_wip+=$printing_wip; $buyer_printing_wip+=$printing_wip; $gt_printing_wip+=$printing_wip;  ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"],0); $job_embroidery_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]; $buyer_embroidery_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]; $gt_embroidery_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"],0); $job_embroidery_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"]; $buyer_embroidery_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"]; $gt_embroidery_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_embroidery_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"]; echo number_format($tot_embroidery_qnty,0); $job_tot_embroidery_qnty+=$tot_embroidery_qnty; $buyer_tot_embroidery_qnty+=$tot_embroidery_qnty; $gt_tot_embroidery_qnty+=$tot_embroidery_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"],0); $job_embroidery_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]; $buyer_embroidery_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]; $gt_embroidery_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"],0); $job_embroidery_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"]; $buyer_embroidery_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"]; $gt_embroidery_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_embroidery_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"]; echo number_format($tot_embroidery_rcv_qnty,0); $job_tot_embroidery_rcv_qnty+=$tot_embroidery_rcv_qnty; $buyer_tot_embroidery_rcv_qnty+=$tot_embroidery_rcv_qnty; $gt_tot_embroidery_rcv_qnty+=$tot_embroidery_rcv_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"],0); $job_embroidery_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"]; $buyer_embroidery_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"];$gt_embroidery_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_embroidery_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_prev_qty"]; echo number_format($total_embroidery_reject,0); $job_total_embroidery_reject+=$total_embroidery_reject; $buyer_total_embroidery_reject+=$total_embroidery_reject; $gt_total_embroidery_reject+=$total_embroidery_reject;?></td>
		                                        <td width="70" align="right"><? $emb_wip=(($tot_embroidery_rcv_qnty+$total_embroidery_reject)-$tot_embroidery_qnty); echo number_format($emb_wip,0); $job_emb_wip+=$emb_wip; $buyer_emb_wip+=$emb_wip; $gt_emb_wip+=$emb_wip;  ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"],0); $job_wash_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]; $buyer_wash_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]; $gt_wash_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"],0); $job_wash_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"]; $buyer_wash_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"]; $gt_wash_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_wash_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"]; echo number_format($tot_wash_qnty,0); $job_tot_wash_qnty+=$tot_wash_qnty; $buyer_tot_wash_qnty+=$tot_wash_qnty; $gt_tot_wash_qnty+=$tot_wash_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"],0); $job_wash_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]; $buyer_wash_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]; $gt_wash_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"],0); $job_wash_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"]; $buyer_wash_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"]; $gt_wash_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_wash_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"]; echo number_format($tot_wash_rcv_qnty,0); $job_tot_wash_rcv_qnty+=$tot_wash_rcv_qnty; $buyer_tot_wash_rcv_qnty+=$tot_wash_rcv_qnty; $gt_tot_wash_rcv_qnty+=$tot_wash_rcv_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"],0); $job_wash_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"]; $buyer_wash_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"]; $gt_wash_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_wash_reject+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_prev_qty"]; echo number_format($total_wash_reject,0); $job_total_wash_reject+=$total_wash_reject; $buyer_total_wash_reject+=$total_wash_reject; $gt_total_wash_reject+=$total_wash_reject;?></td>
		                                        <td width="70" align="right"><? $wash_wip=(($tot_wash_rcv_qnty+$total_wash_reject)-$tot_wash_qnty); echo number_format($wash_wip,0); $job_wash_wip+=$wash_wip; $buyer_wash_wip+=$wash_wip; $gt_wash_wip+=$wash_wip;  ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"],0); $job_sp_work_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]; $buyer_sp_work_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]; $gt_sp_work_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"],0); $job_sp_work_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"]; $buyer_sp_work_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"]; $gt_sp_work_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_sp_work_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"]; echo number_format($tot_sp_work_qnty,0); $job_tot_sp_work_qnty+=$tot_sp_work_qnty; $buyer_tot_sp_work_qnty+=$tot_sp_work_qnty; $gt_tot_sp_work_qnty+=$tot_sp_work_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"],0); $job_sp_work_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]; $buyer_sp_work_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]; $gt_sp_work_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"],0); $job_sp_work_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"]; $buyer_sp_work_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"]; $gt_sp_work_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_sp_work_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"]; echo number_format($tot_sp_work_rcv_qnty,0); $job_tot_sp_work_rcv_qnty+=$tot_sp_work_rcv_qnty; $buyer_tot_sp_work_rcv_qnty+=$tot_sp_work_rcv_qnty; $gt_tot_sp_work_rcv_qnty+=$tot_sp_work_rcv_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"],0); $job_sp_work_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"]; $buyer_sp_work_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"]; $gt_sp_work_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_sp_work_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_prev_qty"]; echo number_format($total_sp_work_reject,0); $job_total_sp_work_reject+=$total_sp_work_reject; $buyer_total_sp_work_reject+=$total_sp_work_reject; $gt_total_sp_work_reject+=$total_sp_work_reject;?></td>
		                                        <td width="70" align="right"><? $sp_work_wip=(($tot_sp_work_rcv_qnty+$total_sp_work_reject)-$tot_sp_work_qnty); echo number_format($sp_work_wip,0); $job_sp_work_wip+=$sp_work_wip; $buyer_sp_work_wip+=$sp_work_wip; $gt_sp_work_wip+=$sp_work_wip;?></td>
		                                        
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"],0); $job_sewing_in_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"]; $buyer_sewing_in_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"]; $gt_sewing_in_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"]; ?></td>

		                                        <td width="70" align="right"><a href="##" onclick="openmypage_production_sewing_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,<? echo $txt_production_date;?>,4,'A','production_qnty_popup','Today Sewing Input','800','300');">
		                                        	<p>
		                                        	<? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"],0); $job_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"]; $buyer_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"]; $gt_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"];?></p></a></td>
		                                        <td width="70" align="right"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'B','production_qnty_popup','Total Sewing Input','730','300');"><p><? $tot_sewing_in_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"]; echo number_format($tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $buyer_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $gt_tot_sewing_in_qnty+=$tot_sewing_in_qnty;?></a></p></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"],0); $job_sewing_out_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"]; $buyer_sewing_out_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"]; $gt_sewing_out_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"],0); $job_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"]; $buyer_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"]; $gt_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_sewing_out_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"]; echo number_format($tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty+=$tot_sewing_out_qnty; $buyer_tot_sewing_out_qnty+=$tot_sewing_out_qnty; $gt_tot_sewing_out_qnty+=$tot_sewing_out_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"],0); $job_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"]; $buyer_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"]; $gt_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_sewing_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_prev_qty"]; echo number_format($total_sewing_reject,0); $job_total_sewing_reject+=$total_sewing_reject; $buyer_total_sewing_reject+=$total_sewing_reject; $gt_total_sewing_reject+=$total_sewing_reject;?></td>
		                                        <td width="70" align="right"><? $sewing_wip=(($tot_sewing_out_qnty+$total_sewing_reject)-$tot_sewing_in_qnty); echo number_format($sewing_wip,0); $job_sewing_wip+=$sewing_wip; $buyer_sewing_wip+=$sewing_wip; $gt_sewing_wip+=$sewing_wip;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"],0); $job_poly_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"]; $buyer_poly_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"]; $gt_poly_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"],0); $job_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"]; $buyer_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"]; $gt_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_poly_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"]; echo number_format($tot_poly_qnty,0); $job_tot_poly_qnty+=$tot_poly_qnty; $buyer_tot_poly_qnty+=$tot_poly_qnty; $gt_tot_poly_qnty+=$tot_poly_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"],0); $job_poly_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"]; $buyer_poly_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"]; $gt_poly_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_poly_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_prev_qty"]; echo number_format($total_poly_reject,0); $job_total_poly_reject+=$total_poly_reject; $buyer_total_poly_reject+=$total_poly_reject; $gt_total_poly_reject+=$total_poly_reject;?></td>
		                                        <td width="70" align="right"><? $poly_wip=(($tot_poly_qnty+$total_poly_reject)-$tot_sewing_out_qnty); echo number_format($poly_wip,0); $job_poly_wip+=$poly_wip; $buyer_poly_wip+=$poly_wip; $gt_poly_wip+=$poly_wip;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"],0); $job_paking_finish_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"]; $buyer_paking_finish_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"]; $gt_paking_finish_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"],0); $job_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"]; $buyer_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"]; $gt_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_paking_finish_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"]; echo number_format($tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty+=$tot_paking_finish_qnty; $buyer_tot_paking_finish_qnty+=$tot_paking_finish_qnty; $gt_tot_paking_finish_qnty+=$tot_paking_finish_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"],0); $job_paking_finish_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"]; $buyer_paking_finish_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"]; $gt_paking_finish_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_finish_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_prev_qty"]; echo number_format($total_finish_reject,0); $job_total_finish_reject+=$total_finish_reject; $buyer_total_finish_reject+=$total_finish_reject; $gt_total_finish_reject+=$total_finish_reject;?></td>
		                                        <td width="70" align="right"><? $finishing_wip=(($tot_paking_finish_qnty+$total_finish_reject)-$tot_poly_qnty); echo number_format($finishing_wip,0); $job_finishing_wip+=$finishing_wip; $buyer_finishing_wip+=$finishing_wip; $gt_finishing_wip+=$finishing_wip;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"],0); $job_ex_fact_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"]; $buyer_ex_fact_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"]; $gt_ex_fact_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"],0); $job_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"]; $buyer_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"]; $gt_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_ex_fact_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"]; echo number_format($tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $buyer_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $gt_tot_ex_fact_qnty+=$tot_ex_fact_qnty;?></td>
		                                        <td width="70" align="right"><? $ex_fact_wip=($tot_ex_fact_qnty-$tot_paking_finish_qnty); echo number_format($ex_fact_wip,0); $job_ex_fact_wip+=$ex_fact_wip; $buyer_ex_fact_wip+=$ex_fact_wip; $gt_ex_fact_wip+=$ex_fact_wip;?></td>
		                                    </tr>
		                                    <?
		                                    $i++;
										}
										
									}
								}
							}
						}
						?>
		                <tr bgcolor="#F4F3C4">
		                    <td align="right" colspan="12" style="font-weight:bold;">Job Total:</td>
		                    <td width="70" align="right"><? echo number_format($job_order_qnty,0); $job_order_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_lay_prev_qnty,0); $job_lay_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_lay_qnty,0); $job_lay_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_lay_qnty,0); $job_tot_lay_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_cutting_prev_qnty,0); $job_cutting_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_cutting_qnty,0); $job_cutting_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_cutting_qnty,0); $job_tot_cutting_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_cutting_reject_qty,0); $job_cutting_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_cutting_reject,0); $job_total_cutting_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_cutting_replace,0); $job_total_cutting_replace=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_cut_qc_wip,0); $job_cut_qc_wip=0;  ?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_prev_qnty,0); $job_printing_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_qnty,0); $job_printing_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_printing_qnty,0); $job_tot_printing_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_rcv_prev_qnty,0); $job_printing_rcv_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_rcv_qnty,0); $job_printing_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_printing_rcv_qnty,0); $job_tot_printing_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_reject_qty,0); $job_printing_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_printing_reject,0); $job_total_printing_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_wip,0); $job_printing_wip=0;  ?></td>
		                    <td width="70" align="right"><? echo number_format($job_embroidery_prev_qnty,0); $job_embroidery_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_embroidery_qnty,0); $job_embroidery_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_embroidery_qnty,0); $job_tot_embroidery_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_embroidery_rcv_prev_qnty,0); $job_embroidery_rcv_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_embroidery_rcv_qnty,0); $job_embroidery_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_embroidery_rcv_qnty,0); $job_tot_embroidery_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_embroidery_reject_qty,0); $job_embroidery_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_embroidery_reject,0); $job_total_embroidery_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_emb_wip,0); $job_emb_wip=0;  ?></td>
		                    <td width="70" align="right"><? echo number_format($job_wash_prev_qnty,0); $job_wash_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_wash_qnty,0); $job_wash_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_wash_qnty,0); $job_tot_wash_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_wash_rcv_prev_qnty,0); $job_wash_rcv_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_wash_rcv_qnty,0); $job_wash_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_wash_rcv_qnty,0); $job_tot_wash_rcv_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_wash_reject_qty,0); $job_wash_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_wash_reject,0); $job_total_wash_reject=0;?></td>
		                    <td width="70" align="right"><?  echo number_format($job_wash_wip,0); $job_wash_wip=0;  ?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_prev_qnty,0); $job_sp_work_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_qnty,0); $job_sp_work_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_sp_work_qnty,0); $job_tot_sp_work_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_rcv_prev_qnty,0); $job_sp_work_rcv_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_rcv_qnty,0); $job_sp_work_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_sp_work_rcv_qnty,0); $job_tot_sp_work_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_reject_qty,0); $job_sp_work_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_sp_work_reject,0); $job_total_sp_work_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_wip,0); $job_sp_work_wip=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sewing_in_prev_qnty,0); $job_sewing_in_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_sewing_in_qnty,0); $job_sewing_in_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sewing_out_prev_qnty,0); $job_sewing_out_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_sewing_out_qnty,0); $job_sewing_out_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sewing_reject_qty,0); $job_sewing_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_sewing_reject,0); $job_total_sewing_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sewing_wip,0); $job_sewing_wip=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_poly_prev_qnty,0); $job_poly_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_poly_qnty,0); $job_poly_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_poly_qnty,0); $job_tot_poly_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_poly_reject_qty,0); $job_poly_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_poly_reject,0); $job_total_poly_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_poly_wip,0); $job_poly_wip=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_paking_finish_prev_qnty,0); $job_paking_finish_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_paking_finish_qnty,0); $job_paking_finish_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_paking_finish_reject_qty,0); $job_paking_finish_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_finish_reject,0); $job_total_finish_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_finishing_wip,0); $job_finishing_wip=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_ex_fact_prev_qnty,0); $job_ex_fact_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_ex_fact_qnty,0); $job_ex_fact_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_ex_fact_wip,0); $job_ex_fact_wip=0;?></td>
		                </tr>
		                <?
					}
					?>
		            <tr bgcolor="#CCCCCC">
		                <td align="right" colspan="12" style="font-weight:bold;">Buyer Total:</td>
		                <td width="70" align="right"><? echo number_format($buyer_order_qnty,0); $buyer_order_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_lay_prev_qnty,0); $buyer_lay_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_lay_qnty,0);  $buyer_lay_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_lay_qnty,0); $buyer_tot_lay_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_cutting_prev_qnty,0); $buyer_cutting_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_cutting_qnty,0); $buyer_cutting_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_cutting_qnty,0); $buyer_tot_cutting_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_cutting_reject_qty,0);  $buyer_cutting_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_cutting_reject,0); $buyer_total_cutting_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_cutting_replace,0); $buyer_total_cutting_replace=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_cut_qc_wip,0); $buyer_cut_qc_wip=0;  ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_prev_qnty,0); $ $buyer_printing_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_qnty,0); $buyer_printing_qnty=0;?></td>
		                <td width="70" align="right"><?  echo number_format($buyer_tot_printing_qnty,0); $buyer_tot_printing_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_rcv_prev_qnty,0); $buyer_printing_rcv_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_rcv_qnty,0);  $buyer_printing_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_printing_rcv_qnty,0); $buyer_tot_printing_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_reject_qty,0);  $buyer_printing_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_printing_reject,0); $buyer_total_printing_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_wip,0); $buyer_printing_wip=0;  ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_embroidery_prev_qnty,0);  $buyer_embroidery_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_embroidery_qnty,0);  $buyer_embroidery_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_embroidery_qnty,0); $buyer_tot_embroidery_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_embroidery_rcv_prev_qnty,0); $buyer_embroidery_rcv_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_embroidery_rcv_qnty,0); $buyer_embroidery_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_embroidery_rcv_qnty,0);  $buyer_tot_embroidery_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_embroidery_reject_qty,0); $buyer_embroidery_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_embroidery_reject,0); $buyer_total_embroidery_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_emb_wip,0); $buyer_emb_wip=0;  ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_prev_qnty,0); $buyer_wash_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_qnty,0);  $buyer_wash_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_wash_qnty,0); $buyer_tot_wash_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_rcv_prev_qnty,0); $buyer_wash_rcv_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_rcv_qnty,0);  $buyer_wash_rcv_qnty=0;?></td>
		                <td width="70" align="right"><?  echo number_format($buyer_tot_wash_rcv_qnty,0); $buyer_tot_wash_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_reject_qty,0); $buyer_wash_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_wash_reject,0); $buyer_total_wash_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_wip,0);  $buyer_wash_wip=0;  ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sp_work_prev_qnty,0); $buyer_sp_work_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sp_work_qnty,0);  $buyer_sp_work_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_sp_work_qnty,0);  $buyer_tot_sp_work_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sp_work_rcv_prev_qnty,0);  $buyer_sp_work_rcv_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sp_work_rcv_qnty,0);  $buyer_sp_work_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_sp_work_rcv_qnty,0); $buyer_tot_sp_work_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sp_work_reject_qty,0); $buyer_sp_work_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_sp_work_reject,0); $buyer_total_sp_work_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format( $buyer_sp_work_wip,0);  $buyer_sp_work_wip=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sewing_in_prev_qnty,0); $buyer_sewing_in_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sewing_in_qnty,0);  $buyer_sewing_in_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_sewing_in_qnty,0);  $buyer_tot_sewing_in_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sewing_out_prev_qnty,0); $buyer_sewing_out_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sewing_out_qnty,0);  $buyer_sewing_out_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format( $buyer_tot_sewing_out_qnty,0); $buyer_tot_sewing_out_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sewing_reject_qty,0); $buyer_sewing_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_sewing_reject,0); $buyer_total_sewing_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sewing_wip,0); $buyer_sewing_wip=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_poly_prev_qnt,0); $buyer_poly_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_poly_qnty,0); $buyer_poly_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_poly_qnty,0); $buyer_tot_poly_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_poly_reject_qty,0);  $buyer_poly_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_poly_reject,0); $buyer_total_poly_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_poly_wip,0); $buyer_poly_wip=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_paking_finish_prev_qnty,0);  $buyer_paking_finish_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_paking_finish_qnty,0);  $buyer_paking_finish_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_paking_finish_qnty,0); $buyer_tot_paking_finish_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_paking_finish_reject_qty,0); $buyer_paking_finish_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_finish_reject,0); $buyer_total_finish_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_finishing_wip,0);  $buyer_finishing_wip=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_ex_fact_prev_qnty,0); $buyer_ex_fact_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_ex_fact_qnty,0); $buyer_ex_fact_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_ex_fact_qnty,0); $buyer_tot_ex_fact_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_ex_fact_wip,0); $buyer_ex_fact_wip=0;?></td>
		            </tr>
		            <?
				}
				
		        
		        ?>
		        </tbody>
		        
		 
		    </table> 
		    </div>  
		    <table border="1" class="rpt_table"  width="6070" rules="all" style="margin-left: 2px;" align="left" id="">
		    	<tfoot>
		        	<tr>
		        		<th style="word-break: break-all;word-wrap: break-word;" width="40" align="center">&nbsp;</th>
		        		<th style="word-break: break-all;word-wrap: break-word;" width="100"><p>&nbsp;</p></th>
		        		<th style="word-break: break-all;word-wrap: break-word;"  width="100"><p>&nbsp;</p></th>
		        		<th style="word-break: break-all;word-wrap: break-word;" width="60" align="center"><p>&nbsp;</p></th>
		        		<th style="word-break: break-all;word-wrap: break-word;" width="50" align="center"><p>&nbsp;</p></th>
		        		<th style="word-break: break-all;word-wrap: break-word;" width="100"><p>&nbsp;</p></th>
		        		<th style="word-break: break-all;word-wrap: break-word;" width="80"><p>&nbsp;</p></th>
		        		<th style="word-break: break-all;word-wrap: break-word;" width="80"><p>&nbsp;</p></th>
		        		<th style="word-break: break-all;word-wrap: break-word;" width="100"><p>&nbsp;</p></th>
		        		<th style="word-break: break-all;word-wrap: break-word;"  width="70" align="center"><p>&nbsp;</p></th>
		        		<th style="word-break: break-all;word-wrap: break-word;"  width="100"><p>&nbsp;</p></th>
		        		 
		                <th width="100" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">Grand Total</th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_order_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_lay_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_lay_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_lay_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_cutting_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_cutting_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_cutting_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_cutting_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_total_cutting_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_total_cutting_replace,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_cut_qc_wip,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_printing_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_printing_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_printing_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_printing_rcv_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_printing_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_printing_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_printing_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_total_printing_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_printing_wip,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_embroidery_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_embroidery_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_embroidery_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_embroidery_rcv_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_embroidery_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_embroidery_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_embroidery_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_total_embroidery_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_emb_wip,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_wash_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_wash_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_wash_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_wash_rcv_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_wash_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_wash_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_wash_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_total_wash_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_wash_wip,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sp_work_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sp_work_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_sp_work_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sp_work_rcv_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sp_work_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_sp_work_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sp_work_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_total_sp_work_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sp_work_wip,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sewing_in_prev_qnty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sewing_in_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_sewing_in_qnty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sewing_out_prev_qnty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sewing_out_qnty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_sewing_out_qnty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sewing_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_total_sewing_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sewing_wip,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_poly_prev_qnty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_poly_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_poly_qnty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_poly_reject_qty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_total_poly_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><?  echo number_format($gt_poly_wip,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_paking_finish_prev_qnty,0);  ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_paking_finish_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_paking_finish_qnty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_paking_finish_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_total_finish_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_finishing_wip,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_ex_fact_prev_qnty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_ex_fact_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_ex_fact_qnty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_ex_fact_wip,0);?></th>
		            </tr>    
		        </tfoot>
		    </table>
		     </fieldset>  
		  </div>     
		  </fieldset>
		 <?	
	}
	elseif($type==2)
	{
		$sql_lay=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, 
			CASE WHEN a.entry_date=".$txt_production_date." THEN c.size_qty ELSE 0 END AS production_qnty,
			c.size_qty  AS alls_production_qnty 
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id   and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_cut $order_cond_lay  "; //
		
		/*$sql_lay=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, 
			CASE WHEN a.entry_date=".$txt_production_date." THEN c.size_qty ELSE 0 END AS production_qnty 
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=99 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name)  $order_cond_lay  "; */
		
		 //sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
		//sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
			/*$sql_lay=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, 
			c.size_qty as production_qnty  
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=99 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name)  $order_cond_lay  ";*/ //
		
		//echo $sql_lay;
		
		$sql_lay_result=sql_select($sql_lay);
		$production_data=$porduction_ord_id=$lay_order_id=array();
		$garments_order_id_arr=array();
		foreach($sql_lay_result as $row)
		{
			/*if($row[csf("production_qnty")]>0)
			{*/
				$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
				$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
				$lay_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
				$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["lay_qnty"]+=$row[csf("production_qnty")];

				//$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["lay_prev_qnty"]+=$row[csf("production_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_production_qnty"]+=$row[csf("alls_production_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("production_qnty")];
			//}
		}

			$production_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id, 
			sum(CASE WHEN a.production_date=".$txt_production_date." THEN b.production_qnty  ELSE 0 END) AS all_today_production_qnty,
			sum(CASE WHEN b.production_type =1 and a.production_type =1 and a.production_date=".$txt_production_date." THEN b.production_qnty  ELSE 0 END) AS cutting_qnty,
			sum(CASE WHEN b.production_type =1 and a.production_type =1 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS cutting_reject_qty,
			sum(CASE WHEN b.production_type =4 and a.production_type =4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewing_in_qnty,
			sum(CASE WHEN b.production_type =5 and a.production_type =5 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty,
			sum(CASE WHEN b.production_type =5 and a.production_type =5 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewing_reject_qty,
			sum(CASE WHEN b.production_type =7 and a.production_type =7 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS iron_qnty,
			sum(CASE WHEN b.production_type =8 and a.production_type =8 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS paking_finish_qnty,
			sum(CASE WHEN b.production_type =8 and a.production_type =8 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS paking_finish_reject_qty,
			sum(CASE WHEN b.production_type =11 and a.production_type=11 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty,
			sum(CASE WHEN b.production_type =11 and a.production_type =11  and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS poly_reject_qty,
			
			
			sum(CASE WHEN b.production_type =1 and a.production_type =1 THEN b.production_qnty  ELSE 0 END) AS alls_cutting_qnty,
			sum(CASE WHEN b.production_type =1 and a.production_type =1 THEN b.reject_qty ELSE 0 END) AS alls_cutting_reject_qty,
			sum(CASE WHEN b.production_type =4 and a.production_type =4 THEN b.production_qnty ELSE 0 END) AS alls_sewing_in_qnty,
			sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.production_qnty ELSE 0 END) AS alls_sewing_out_qnty,
			sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.reject_qty ELSE 0 END) AS alls_sewing_reject_qty,
			sum(CASE WHEN b.production_type =7 and a.production_type =7 THEN b.production_qnty ELSE 0 END) AS alls_iron_qnty,
			sum(CASE WHEN b.production_type =8 and a.production_type =8 THEN b.production_qnty ELSE 0 END) AS alls_paking_finish_qnty,
			sum(CASE WHEN b.production_type =8 and a.production_type =8 THEN b.reject_qty ELSE 0 END) AS alls_paking_finish_reject_qty,
			sum(CASE WHEN b.production_type =11 and a.production_type=11 THEN b.production_qnty ELSE 0 END) AS alls_poly_qnty,
			sum(CASE WHEN b.production_type =11 and a.production_type =11 THEN b.reject_qty ELSE 0 END) AS alls_poly_reject_qty 
			 
			from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type in(1,4,5,7,8,11) and a.production_type in(1,4,5,7,8,11) and a.serving_company in($cbo_work_company_name) $gmts_loc_floor_cond $order_cond
			group by a.production_date, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id";
		
			/*sum(CASE WHEN b.production_type =1 and a.production_type =1 and a.production_date<".$txt_production_date." THEN b.production_qnty  ELSE 0 END) AS cutting_qnty_pre,
			sum(CASE WHEN b.production_type =1 and a.production_type =1 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS cutting_reject_qty_pre,
			sum(CASE WHEN b.production_type =4 and a.production_type =4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewing_in_qnty_pre,
			sum(CASE WHEN b.production_type =5 and a.production_type =5 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty_pre,
			sum(CASE WHEN b.production_type =5 and a.production_type =5 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewing_reject_qty_pre,
			sum(CASE WHEN b.production_type =7 and a.production_type =7 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS iron_qnty_pre,
			sum(CASE WHEN b.production_type =8 and a.production_type =8 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS paking_finish_qnty_pre,
			sum(CASE WHEN b.production_type =8 and a.production_type =8 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS paking_finish_reject_qty_pre,
			sum(CASE WHEN b.production_type =11 and a.production_type=11 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_pre,
			sum(CASE WHEN b.production_type =11 and a.production_type =11  and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS poly_reject_qty_pre*/ 
		 //echo $production_sql;// die;
		
		$production_sql_result=sql_select($production_sql);
		foreach($production_sql_result as $row)
		{
			/*if($row[csf("all_today_production_qnty")]>0)
			{*/
				if($garments_order_id_arr[$row[csf("order_id")]]=="")
				{
					$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
				}
				$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
				$gmt_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_qnty"]+=$row[csf("cutting_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_reject_qty"]+=$row[csf("cutting_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_in_qnty"]+=$row[csf("sewing_in_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_out_qnty"]+=$row[csf("sewing_out_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_reject_qty"]+=$row[csf("sewing_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["iron_qnty"]+=$row[csf("iron_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_qnty"]+=$row[csf("paking_finish_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_reject_qty"]+=$row[csf("paking_finish_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_qnty"]+=$row[csf("poly_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_reject_qty"]+=$row[csf("poly_reject_qty")];
				
				
				
				
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_cutting_qnty"]+=$row[csf("alls_cutting_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_cutting_reject_qty"]+=$row[csf("alls_cutting_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_sewing_in_qnty"]+=$row[csf("alls_sewing_in_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_sewing_out_qnty"]+=$row[csf("alls_sewing_out_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_sewing_reject_qty"]+=$row[csf("alls_sewing_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_iron_qnty"]+=$row[csf("alls_iron_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_paking_finish_qnty"]+=$row[csf("alls_paking_finish_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_paking_finish_reject_qty"]+=$row[csf("alls_paking_finish_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_poly_qnty"]+=$row[csf("alls_poly_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_poly_reject_qty"]+=$row[csf("alls_poly_reject_qty")];
				
				
				/*$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_qnty_pre"]+=$row[csf("cutting_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_reject_qty_pre"]+=$row[csf("cutting_reject_qty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_in_qnty_pre"]+=$row[csf("sewing_in_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_out_qnty_pre"]+=$row[csf("sewing_out_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_reject_qty_pre"]+=$row[csf("sewing_reject_qty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["iron_qnty_pre"]+=$row[csf("iron_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_qnty_pre"]+=$row[csf("paking_finish_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_reject_qty_pre"]+=$row[csf("paking_finish_reject_qty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_qnty_pre"]+=$row[csf("poly_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_reject_qty_pre"]+=$row[csf("poly_reject_qty_pre")];*/
				
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("all_today_production_qnty")];
			//}
			
		}
			$print_embro_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, 
			sum(CASE WHEN a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS all_today_production_qnty,
			sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS printing_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS printing_rcv_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS printing_reject_qty,
			sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS embroidery_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS embroidery_rcv_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS embroidery_reject_qty,
			sum(CASE WHEN b.production_type =2 and a.embel_name=3 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS wash_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=3 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS wash_rcv_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=3 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS wash_reject_qty,
			sum(CASE WHEN b.production_type =2 and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sp_work_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sp_work_rcv_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sp_work_reject_qty,
			
			
			sum(CASE WHEN b.production_type =2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS alls_printing_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS alls_printing_rcv_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS alls_printing_reject_qty,
			sum(CASE WHEN b.production_type =2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS alls_embroidery_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS alls_embroidery_rcv_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS alls_embroidery_reject_qty,
			sum(CASE WHEN b.production_type =2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS alls_wash_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS alls_wash_rcv_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS alls_wash_reject_qty,
			sum(CASE WHEN b.production_type =2 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS salls_p_work_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS alls_sp_work_rcv_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.reject_qty ELSE 0 END) AS alls_sp_work_reject_qty 
			 
			from  pro_gmts_delivery_mst m, pro_garments_production_dtls b, pro_garments_production_mst a, wo_po_color_size_breakdown c 
			where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and b.production_type in(2,3) and a.production_type in(2,3) and m.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_delv $order_cond
			group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
			
			/*sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS printing_qnty_pre,
			sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS printing_rcv_qnty_pre,
			sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS printing_reject_qty_pre,
			sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS embroidery_qnty_pre,
			sum(CASE WHEN b.production_type =3 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS embroidery_rcv_qnty_pre,
			sum(CASE WHEN b.production_type =3 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS embroidery_reject_qty_pre,
			sum(CASE WHEN b.production_type =2 and a.embel_name=3 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS wash_qnty_pre,
			sum(CASE WHEN b.production_type =3 and a.embel_name=3 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS wash_rcv_qnty_pre,
			sum(CASE WHEN b.production_type =3 and a.embel_name=3 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS wash_reject_qty_pre,
			sum(CASE WHEN b.production_type =2 and a.embel_name=4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sp_work_qnty_pre,
			sum(CASE WHEN b.production_type =3 and a.embel_name=4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sp_work_rcv_qnty_pre,
			sum(CASE WHEN b.production_type =3 and a.embel_name=4 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sp_work_reject_qty_pre*/
		//echo $print_embro_sql;die;
		
		$print_embro_sql_result=sql_select($print_embro_sql);
		$print_embro_order_id=array();
		foreach($print_embro_sql_result as $row)
		{
			/*if($row[csf("all_today_production_qnty")]>0)
			{*/
				if($garments_order_id_arr[$row[csf("order_id")]]=="")
				{
					$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
				}
				
				$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
				$print_embro_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_qnty"]+=$row[csf("printing_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_rcv_qnty"]+=$row[csf("printing_rcv_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_reject_qty"]+=$row[csf("printing_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_qnty"]+=$row[csf("embroidery_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_rcv_qnty"]+=$row[csf("embroidery_rcv_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_reject_qty"]+=$row[csf("embroidery_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_qnty"]+=$row[csf("wash_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_rcv_qnty"]+=$row[csf("wash_rcv_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_reject_qty"]+=$row[csf("wash_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_qnty"]+=$row[csf("sp_work_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_rcv_qnty"]+=$row[csf("sp_work_rcv_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_reject_qty"]+=$row[csf("sp_work_reject_qty")];
				
				
				
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_printing_qnty"]+=$row[csf("alls_printing_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_printing_rcv_qnty"]+=$row[csf("alls_printing_rcv_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_printing_reject_qty"]+=$row[csf("alls_printing_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_embroidery_qnty"]+=$row[csf("alls_embroidery_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_embroidery_rcv_qnty"]+=$row[csf("alls_embroidery_rcv_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_embroidery_reject_qty"]+=$row[csf("alls_embroidery_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_wash_qnty"]+=$row[csf("alls_wash_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_wash_rcv_qnty"]+=$row[csf("alls_wash_rcv_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_wash_reject_qty"]+=$row[csf("alls_wash_reject_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_sp_work_qnty"]+=$row[csf("alls_sp_work_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_sp_work_rcv_qnty"]+=$row[csf("alls_sp_work_rcv_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_sp_work_reject_qty"]+=$row[csf("alls_sp_work_reject_qty")];
				
				
				/*$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_qnty_pre"]+=$row[csf("printing_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_rcv_qnty_pre"]+=$row[csf("printing_rcv_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_reject_qty_pre"]+=$row[csf("printing_reject_qty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_qnty_pre"]+=$row[csf("embroidery_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_rcv_qnty_pre"]+=$row[csf("embroidery_rcv_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_reject_qty_pre"]+=$row[csf("embroidery_reject_qty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_qnty_pre"]+=$row[csf("wash_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_rcv_qnty_pre"]+=$row[csf("wash_rcv_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_reject_qty_pre"]+=$row[csf("wash_reject_qty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_qnty_pre"]+=$row[csf("sp_work_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_rcv_qnty_pre"]+=$row[csf("sp_work_rcv_qnty_pre")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_reject_qty_pre"]+=$row[csf("sp_work_reject_qty_pre")];*/
				
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("all_today_production_qnty")];
			//}
			
		}

			$ex_factory_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, 
			sum(CASE WHEN a.ex_factory_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS ex_fact_qnty,
			sum(b.production_qnty) AS alls_ex_fact_qnty
		from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
		where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and m.delivery_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_exfac  $order_cond
		group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
		
		
		//echo $ex_factory_sql;//die;
		$ex_factory_sql_result=sql_select($ex_factory_sql);
		foreach($ex_factory_sql_result as $row)
		{
			/*if($row[csf("ex_fact_qnty")]>0)
			{*/
				if($garments_order_id_arr[$row[csf("order_id")]]=="")
				{
					$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
				}
				
				$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
				$ex_fact_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["ex_fact_qnty"]+=$row[csf("ex_fact_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["alls_ex_fact_qnty"]+=$row[csf("alls_ex_fact_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("ex_fact_qnty")];
			//}
		}
		
		
		$order_prev_con=" and";
		$garments_order_arr=array_chunk($garments_order_id_arr,999);
		foreach($garments_order_arr as $order_data)
		{
			if($order_prev_con==" and")
			{
				$order_prev_con .="  ( c.order_id in(".implode(',',array_filter(array_unique($order_data))).")";
			}
			else
			{
				$order_prev_con .=" or c.order_id in(".implode(',',array_filter(array_unique($order_data))).")";
			}
		}
		$order_prev_con .=")";
		
		// previous data
		//$lay_order_id=implode(',',$lay_order_id);
		
		//sum(CASE WHEN production_type ='1' and txt_production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
		//sum(CASE WHEN production_type ='1' and txt_production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
		
		if(count($garments_order_id_arr)>0)
		{
			 $sql_lay_prev=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, c.size_qty as production_qnty  
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
			where a.id=b.mst_id and b.id=c.dtls_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_cut and a.entry_date<$txt_production_date $order_prev_con";
			
			//echo $sql_lay_prev;die;
			
			$sql_lay_prev_result=sql_select($sql_lay_prev);
			foreach($sql_lay_prev_result as $row)
			{
				$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["lay_prev_qnty"]+=$row[csf("production_qnty")];
			}
		}
		
		//echo "<pre>";
		//print_r($production_data);die;
		
		$order_prev_con_prod=" and";
		foreach($garments_order_arr as $order_data)
		{
			if($order_prev_con_prod==" and")
			{
				$order_prev_con_prod .="  ( a.po_break_down_id in(".implode(',',array_filter(array_unique($order_data))).")";
			}
			else
			{
				$order_prev_con_prod .=" or a.po_break_down_id in(".implode(',',array_filter(array_unique($order_data))).")";
			}
		}
		$order_prev_con_prod .=")";
		
		//$gmt_order_id=implode(',',$gmt_order_id);
		
		if(count($garments_order_id_arr)>0)
		{
			 $production_prev_sql="select a.production_date, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id,
			sum(CASE WHEN b.production_type =1 THEN b.production_qnty ELSE 0 END) AS cutting_prev_qnty,
			sum(CASE WHEN b.production_type =1 THEN b.reject_qty ELSE 0 END) AS cutting_reject_prev_qty,
			sum(CASE WHEN b.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_prev_qnty,
			sum(CASE WHEN b.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_prev_qnty,
			sum(CASE WHEN b.production_type =5 THEN b.reject_qty ELSE 0 END) AS sewing_reject_prev_qty,
			sum(CASE WHEN b.production_type =7 THEN b.production_qnty ELSE 0 END) AS iron_prev_qnty,
			sum(CASE WHEN b.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_prev_qnty,
			sum(CASE WHEN b.production_type =8 THEN b.reject_qty ELSE 0 END) AS paking_finish_reject_prev_qty,
			sum(CASE WHEN b.production_type =11 THEN b.production_qnty ELSE 0 END) AS poly_prev_qnty,
			sum(CASE WHEN b.production_type =11 THEN b.reject_qty ELSE 0 END) AS poly_reject_prev_qty 
			from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type in(1,4,5,7,8,11) and a.production_type in(1,4,5,7,8,11) and a.serving_company in($cbo_work_company_name) $gmts_loc_floor_cond and a.production_date<".$txt_production_date." and b.status_active=1 and b.is_deleted=0   $order_prev_con_prod
			group by a.production_date, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id";
			
			//echo $production_prev_sql;die;
			
			$production_prev_sql_result=sql_select($production_prev_sql);
			foreach($production_prev_sql_result as $row)
			{
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_prev_qnty"]+=$row[csf("cutting_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_reject_prev_qty"]+=$row[csf("cutting_reject_prev_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_in_prev_qnty"]+=$row[csf("sewing_in_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_out_prev_qnty"]+=$row[csf("sewing_out_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_reject_prev_qty"]+=$row[csf("sewing_reject_prev_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["iron_prev_qnty"]+=$row[csf("iron_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_prev_qnty"]+=$row[csf("paking_finish_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_reject_prev_qty"]+=$row[csf("paking_finish_reject_prev_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_prev_qnty"]+=$row[csf("poly_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_reject_prev_qty"]+=$row[csf("poly_reject_prev_qty")];
			}
		}
		
		
		
		
		
		//$print_embro_order_id=implode(',',$print_embro_order_id);
		if(count($garments_order_id_arr)>0)
		{
			$print_embro_prev_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id,
			sum(CASE WHEN b.production_type =2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_prev_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_rcv_prev_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS printing_reject_prev_qty,
			sum(CASE WHEN b.production_type =2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_prev_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_rcv_prev_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS embroidery_reject_prev_qty,
			sum(CASE WHEN b.production_type =2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_prev_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_rcv_prev_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS wash_reject_prev_qty,
			sum(CASE WHEN b.production_type =2 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_prev_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_rcv_prev_qnty,
			sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.reject_qty ELSE 0 END) AS sp_work_reject_prev_qty
			from  pro_gmts_delivery_mst m, pro_garments_production_dtls b, pro_garments_production_mst a, wo_po_color_size_breakdown c 
			where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and b.production_type in(2,3) and a.production_type in(2,3) and m.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_delv and m.delivery_date<".$txt_production_date." $order_prev_con_prod
			group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
			
			//echo $print_embro_prev_sql;die;
			
			$print_embro_sql_result=sql_select($print_embro_prev_sql);
			foreach($print_embro_sql_result as $row)
			{
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_prev_qnty"]+=$row[csf("printing_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_rcv_prev_qnty"]+=$row[csf("printing_rcv_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_reject_prev_qty"]+=$row[csf("printing_reject_prev_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_prev_qnty"]+=$row[csf("embroidery_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_rcv_prev_qnty"]+=$row[csf("embroidery_rcv_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_reject_prev_qty"]+=$row[csf("embroidery_reject_prev_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_prev_qnty"]+=$row[csf("wash_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_rcv_prev_qnty"]+=$row[csf("wash_rcv_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_reject_prev_qty"]+=$row[csf("wash_reject_prev_qty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_prev_qnty"]+=$row[csf("sp_work_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_rcv_prev_qnty"]+=$row[csf("sp_work_rcv_prev_qnty")];
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_reject_prev_qty"]+=$row[csf("sp_work_reject_prev_qty")];
			}
		}
		
		
		//$ex_fact_order_id=implode(",",$ex_fact_order_id);
		if(count($garments_order_id_arr)>0)
		{
			$ex_factory_prev_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, sum(b.production_qnty) as ex_fact_qnty 
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
			where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and m.delivery_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_exfac and m.delivery_date<".$txt_production_date." $order_prev_con_prod
			group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
			//echo $ex_factory_prev_sql;die;
			$ex_factory_prev_sql_result=sql_select($ex_factory_prev_sql);
			foreach($ex_factory_prev_sql_result as $row)

			{
				$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["ex_fact_prev_qnty"]+=$row[csf("ex_fact_qnty")];
			}
		}
		
		
		
		if($db_type==0)
		{
			$select_year=" year(a.insert_date) as job_year";
		}
		else
		{
			$select_year=" to_char(a.insert_date,'YYYY') as job_year";
		}
		$buyer_cond="";
		if(str_replace("'","",$cbo_buyer_name)>0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name";
		$porduction_ord_id=trim(implode(",",$porduction_ord_id),",");
		
		$pord_ord_ids=explode(",",$porduction_ord_id);  
		$pord_ord_ids=array_chunk($pord_ord_ids,999);
		$po_qry_cond=" and";
		foreach($pord_ord_ids as $dtls_id)
		{
		if($po_qry_cond==" and")  $po_qry_cond.="(c.po_break_down_id in(".implode(',',$dtls_id).")"; else $po_qry_cond.=" or c.po_break_down_id in(".implode(',',$dtls_id).")";
		}
		$po_qry_cond.=")";
		//echo $po_qry_cond;die;
		//echo  $po_qry_cond="select work_order_id , sum(quantity) as quantity from com_pi_item_details where work_order_dtls_id>0 and status_active=1 and is_deleted=0 $po_qry_cond group by work_order_id";
					
		if($porduction_ord_id!="")
		{
			$sql_color_size=sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, $select_year, b.po_number, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity 
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and b.shiping_status not in(3)  $po_qry_cond $buyer_cond");
		//and b.shiping_status not in(3)
			
			/*echo "select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, $select_year, b.po_number, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity 
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.shiping_status not in(3) $po_qry_cond $buyer_cond";*/
			
			$order_color_data=array();
			foreach($sql_color_size as $row)
			{
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_no"]=$row[csf("job_no")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_year"]=$row[csf("job_year")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["country_ship_date"]=$row[csf("country_ship_date")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];
				
				$order_color_data_orderQty[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["order_quantity_qry"]+=$row[csf("order_quantity")];
			}
		}
		
		//echo $sql_color_size;die;
		   
		ob_start();
	 ?>
	  <fieldset style="width:5860px;">
	  <div style="width:5860px;">
	  	<table width="1880"  cellspacing="0"   >
	            <tr class="form_caption" style="border:none;">
	                   <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report</td>
	             </tr>
	            <tr style="border:none;">
	                    <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
	                    Working Company Name:<? 
						$cbo_work_company_name_arr=explode(",",$cbo_work_company_name);
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
	                    <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
	                    <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
	                    </td>
	              </tr>
	        </table>
	     <br />
	     <fieldset style="width:3840px; float:left;">
	     <legend>Report Summary Part</legend>	
	     <table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="3840" class="rpt_table" align="left" style="margin-bottom:30px;">
	        <thead>
	            <tr >
	                <th width="40" rowspan="2">SL</th>
	                <th width="100" rowspan="2">Buyer</th>
	                <th width="210" colspan="3">Cutting QC</th>             
	                <th width="210" colspan="3">Delivery to Print</th>
	                <th width="210" colspan="3">Receive from Print</th>
	                <th width="210" colspan="3">Delivery to Emb.</th>
	                <th width="210" colspan="3">Receive from Emb.</th>
	                <th width="210" colspan="3">Sewing Input</th>
	                <th width="210" colspan="3">Sewing Output</th>
	                <th width="210" colspan="3">Poly Entry</th>
	                <th width="210" colspan="3">Packing & Finishing</th>
	                <th width="210" colspan="3">Ex-Factory</th>
	            </tr>
	            <tr>
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	            </tr>
	        </thead>
	        <tbody>
	        
	         <?
			
			foreach($order_color_data as $buyer_id=>$buyer_data)
			{
				foreach($buyer_data as $job_no=>$job_data)
				{
					foreach($job_data as $order_id=>$order_data)
					{
						foreach($order_data as $item_id=>$item_data)
						{
							foreach($item_data as $country_id=>$country_data)
							{
								foreach($country_data as $color_id=>$value)
								{
									
									/*if(($production_data[$order_id][$item_id][$country_id][$color_id]["today_production"]>0))
									{*/
										$buyer_sammary_arr[$buyer_id]['buyer']=$buyer_data;
										
										$sammary_arr[$buyer_id]['cutting_qc']['prv']+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"];
										$sammary_arr[$buyer_id]['cutting_qc']['today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"];
										$sammary_arr[$buyer_id]['cutting_qc']['alls_today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["alls_cutting_qnty"];
										$sammary_arr[$buyer_id]['printing_delv']['prv']+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"];
										$sammary_arr[$buyer_id]['printing_delv']['today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"];
										$sammary_arr[$buyer_id]['printing_delv']['alls_today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["alls_printing_qnty"];
										$sammary_arr[$buyer_id]['printing_recv']['prv']+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"];
										$sammary_arr[$buyer_id]['printing_recv']['today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"];
										$sammary_arr[$buyer_id]['printing_recv']['alls_today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["alls_printing_rcv_qnty"];
										$sammary_arr[$buyer_id]['embroidery_delv']['prv']+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"];
										$sammary_arr[$buyer_id]['embroidery_delv']['today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"];
										$sammary_arr[$buyer_id]['embroidery_delv']['alls_today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["alls_embroidery_qnty"];
										$sammary_arr[$buyer_id]['embroidery_recv']['prv']+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"];
										$sammary_arr[$buyer_id]['embroidery_recv']['today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"];
										$sammary_arr[$buyer_id]['embroidery_recv']['alls_today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["alls_embroidery_rcv_qnty"];
										$sammary_arr[$buyer_id]['sewing_in']['prv']+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"];
										$sammary_arr[$buyer_id]['sewing_in']['today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"];
										$sammary_arr[$buyer_id]['sewing_in']['alls_today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["alls_sewing_in_qnty"];
										$sammary_arr[$buyer_id]['sewing_out']['prv']+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"];
										$sammary_arr[$buyer_id]['sewing_out']['today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"];
										$sammary_arr[$buyer_id]['sewing_out']['alls_today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["alls_sewing_out_qnty"];
										$sammary_arr[$buyer_id]['poly_qnty']['prv']+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"];
										$sammary_arr[$buyer_id]['poly_qnty']['today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"];
										$sammary_arr[$buyer_id]['poly_qnty']['alls_today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["alls_poly_qnty"];
										$sammary_arr[$buyer_id]['paking_finish']['prv']+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"];
										$sammary_arr[$buyer_id]['paking_finish']['today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"];
										$sammary_arr[$buyer_id]['paking_finish']['alls_today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["alls_paking_finish_qnty"];
										$sammary_arr[$buyer_id]['ex_fact']['prv']+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"];
										$sammary_arr[$buyer_id]['ex_fact']['today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"];
										$sammary_arr[$buyer_id]['ex_fact']['alls_today']+=$production_data[$order_id][$item_id][$country_id][$color_id]["alls_ex_fact_qnty"];
										
										$sammary_arr[$buyer_id]['poqnty']['poQTY']+=$order_color_data[$buyer_id][$job_no][$order_id][$item_id][$country_id][$color_id]["order_quantity"]+=$row[csf("order_quantity")];


									//}
								}
							}
						}
					}
				}
			}
			
			
			$gTotal_cutting_qc_prv=$gTotal_cutting_qc_today=$gTotal_cutting_qc_total=$gTotal_printing_delv_prv=$gTotal_printing_delv_today=$gTotal_printing_delv_total=$gTotal_printing_recv_prv=$gTotal_printing_recv_today=$gTotal_printing_recv_total=$gTotal_embroidery_delv_prv=$gTotal_embroidery_delv_today=$gTotal_embroidery_delv_total=$gTotal_embroidery_recv_prv=
			$gTotal_embroidery_recv_today=$gTotal_embroidery_recv_total=$gTotal_sewing_in_prv=$gTotal_sewing_in_today=$gTotal_sewing_in_total=$gTotal_sewing_out_prv=$gTotal_sewing_out_today=
			$gTotal_sewing_out_total=$gTotal_poly_qnty_prv=$gTotal_poly_qnty_today=$gTotal_poly_qnty_total=$gTotal_paking_finish_prv=$gTotal_paking_finish_today=$gTotal_paking_finish_total=
			$gTotal_ex_fact_prv=$gTotal_ex_fact_today=$gTotal_ex_fact_total=0;

			$i=1;
			foreach($buyer_sammary_arr as $buyer_id=>$buyer_data)
			{
				//if(($production_data[$order_id][$item_id][$country_id][$color_id]["today_production"]>0)){
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1st<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1st<? echo $i; ?>">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="100"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
						
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['cutting_qc']['prv'],0); $gTotal_cutting_qc_prv+=$sammary_arr[$buyer_id]['cutting_qc']['prv']; ?></td>
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['cutting_qc']['today'],0);  $gTotal_cutting_qc_today+=$sammary_arr[$buyer_id]['cutting_qc']['today']; ?></td>
	                    
	                    
	                    
						<td width="70" align="right"><? //$tot_cutting_qnty_smry=$sammary_arr[$buyer_id]['cutting_qc']['prv']+ $sammary_arr[$buyer_id]['cutting_qc']['today'];
						$tot_cutting_qnty_smry=$sammary_arr[$buyer_id]['cutting_qc']['prv']+$sammary_arr[$buyer_id]['cutting_qc']['today']; 
						echo number_format($tot_cutting_qnty_smry,0); $gTotal_cutting_qc_total+=$tot_cutting_qnty_smry; ?></td>
						
						
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['printing_delv']['prv'],0);  $gTotal_printing_delv_prv+=$sammary_arr[$buyer_id]['printing_delv']['prv']; ?></td>
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['printing_delv']['today'],0);  $gTotal_printing_delv_today+=$sammary_arr[$buyer_id]['printing_delv']['today'] ;?></td>
						<td width="70" align="right"><? $tot_printing_qnty_smry=$sammary_arr[$buyer_id]['printing_delv']['prv']+ $sammary_arr[$buyer_id]['printing_delv']['today']; echo number_format($tot_printing_qnty_smry,0); $gTotal_printing_delv_total+=$tot_printing_qnty_smry ; ?></td>
						
						
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['printing_recv']['prv'],0); $gTotal_printing_recv_prv+=$sammary_arr[$buyer_id]['printing_recv']['prv']; ?></td>
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['printing_recv']['today'],0); $gTotal_printing_recv_today+=$sammary_arr[$buyer_id]['printing_recv']['today'];?></td>
						<td width="70" align="right"><? $tot_printing_rcv_qnty_smry=$sammary_arr[$buyer_id]['printing_recv']['prv']+ $sammary_arr[$buyer_id]['printing_recv']['today']; echo number_format($tot_printing_rcv_qnty_smry,0); $gTotal_printing_recv_total+=$tot_printing_rcv_qnty_smry;?></td>
						
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['embroidery_delv']['prv'],0); $gTotal_embroidery_delv_prv+=$sammary_arr[$buyer_id]['embroidery_delv']['prv']; ?></td>
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['embroidery_delv']['today'],0); $gTotal_embroidery_delv_today+=$sammary_arr[$buyer_id]['embroidery_delv']['today'];?></td>
						<td width="70" align="right"><? $tot_embroidery_qnty_smry=$sammary_arr[$buyer_id]['embroidery_delv']['prv']+ $sammary_arr[$buyer_id]['embroidery_delv']['today']; echo number_format($tot_embroidery_qnty_smry,0); $gTotal_embroidery_delv_total+=$tot_embroidery_qnty_smry;?></td>
						
						
						
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['embroidery_recv']['prv'],0); $gTotal_embroidery_recv_prv+=$sammary_arr[$buyer_id]['embroidery_recv']['prv']; ?></td>
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['embroidery_recv']['today'],0); $gTotal_embroidery_recv_today+=$sammary_arr[$buyer_id]['embroidery_recv']['today'];?></td>
						<td width="70" align="right"><? $tot_embroidery_rcv_qnty_smry=$sammary_arr[$buyer_id]['embroidery_recv']['prv']+ $sammary_arr[$buyer_id]['embroidery_recv']['today']; echo number_format($tot_embroidery_rcv_qnty_smry,0); $gTotal_embroidery_recv_total+=$tot_embroidery_rcv_qnty_smry;?></td>
						
															   
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['sewing_in']['prv'],0); $gTotal_sewing_in_prv+=$sammary_arr[$buyer_id]['sewing_in']['prv']; ?></td>
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['sewing_in']['today'],0); $gTotal_sewing_in_today+=$sammary_arr[$buyer_id]['sewing_in']['today'];?></td>
						<td width="70" align="right"><? $tot_sewing_in_qnty_smry=$sammary_arr[$buyer_id]['sewing_in']['prv']+ $sammary_arr[$buyer_id]['sewing_in']['today']; echo number_format($tot_sewing_in_qnty_smry,0); $gTotal_sewing_in_total+=$tot_sewing_in_qnty_smry;?></td>
						
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['sewing_out']['prv'],0); $gTotal_sewing_out_prv+=$sammary_arr[$buyer_id]['sewing_out']['prv']; ?></td>
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['sewing_out']['today'],0); $gTotal_sewing_out_today+=$sammary_arr[$buyer_id]['sewing_out']['today'];?></td>
						<td width="70" align="right"><? $tot_sewing_out_qnty_smry=$sammary_arr[$buyer_id]['sewing_out']['prv']+ $sammary_arr[$buyer_id]['sewing_out']['today']; echo number_format($tot_sewing_out_qnty_smry,0); $gTotal_sewing_out_total+=$tot_sewing_out_qnty_smry;?></td>
						
						
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['poly_qnty']['prv'],0); $gTotal_poly_qnty_prv+=$sammary_arr[$buyer_id]['poly_qnty']['prv']; ?></td>
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['poly_qnty']['today'],0); $gTotal_poly_qnty_today+=$sammary_arr[$buyer_id]['poly_qnty']['today'];?></td>
						<td width="70" align="right"><? $tot_poly_qnty_smry=$sammary_arr[$buyer_id]['poly_qnty']['prv']+ $sammary_arr[$buyer_id]['poly_qnty']['today']; echo number_format($tot_poly_qnty_smry,0); $gTotal_poly_qnty_total+=$tot_poly_qnty_smry;?></td>
						
						
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['paking_finish']['prv'],0); $gTotal_paking_finish_prv+=$sammary_arr[$buyer_id]['paking_finish']['prv']; ?></td>
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['paking_finish']['today'],0); $gTotal_paking_finish_today+=$sammary_arr[$buyer_id]['paking_finish']['today'];?></td>
						<td width="70" align="right"><? $tot_paking_finish_qnty_smry=$sammary_arr[$buyer_id]['paking_finish']['prv']+ $sammary_arr[$buyer_id]['paking_finish']['today']; echo number_format($tot_paking_finish_qnty_smry,0); $gTotal_paking_finish_total+=$tot_paking_finish_qnty_smry;?></td>
						
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['ex_fact']['prv'],0); $gTotal_ex_fact_prv+=$sammary_arr[$buyer_id]['ex_fact']['prv']; ?></td>
						<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['ex_fact']['today'],0); $gTotal_ex_fact_today+=$sammary_arr[$buyer_id]['ex_fact']['today'];?></td>
						<td width="70" align="right"><? $tot_ex_fact_qnty_smry=$sammary_arr[$buyer_id]['ex_fact']['prv']+ $sammary_arr[$buyer_id]['ex_fact']['today']; echo number_format($tot_ex_fact_qnty_smry,0); $gTotal_ex_fact_total+=$tot_ex_fact_qnty_smry;?></td>
					
					</tr>
					<?
					$i++;
				//}
			}
			?>
	          	
	        </tbody>
	        <tfoot>
	        	<tr>
	                <th colspan="2"  align="right" style="font-weight:bold; font-size:16px;">Grand Total</th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_cutting_qc_prv,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_cutting_qc_today,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_cutting_qc_total,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_delv_prv,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_delv_today,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_delv_total,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_recv_prv,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_recv_today,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_recv_total,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_delv_prv,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_delv_today,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_delv_total,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_recv_prv,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_recv_today,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_recv_total,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_in_prv,0); ?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_in_today,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_in_total,0); ?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_out_prv,0); ?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_out_today,0); ?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_out_total,0); ?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_poly_qnty_prv,0); ?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_poly_qnty_today,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_poly_qnty_total,0); ?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_paking_finish_prv,0);  ?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_paking_finish_today,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_paking_finish_total,0); ?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_ex_fact_prv,0); ?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_ex_fact_today,0);?></th>
	                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_ex_fact_total,0); ?></th>
	             </tr>
	          </tfoot>
	    </table>
		</fieldset>
	  	<fieldset style="width:5860px; float:left;">
	    <legend>Report Details Part</legend>
	     <table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="5840" class="rpt_table" align="left">
	        <thead>
	            <tr >
	                <th width="40" rowspan="2">SL</th>
	                <th width="100" rowspan="2">Buyer</th>
	                <th width="100" rowspan="2">Style Ref</th>
	                <th width="60" rowspan="2">Job No</th>
	                <th width="50" rowspan="2">Year</th>
	                <th width="100" rowspan="2">Order No</th>
	                <th width="100" rowspan="2">Country</th>
	                <th width="70" rowspan="2">Country Shipdate</th>
	                <th width="100" rowspan="2">Garment Item</th>
	                <th width="100" rowspan="2">Color</th>
	                <th width="70" rowspan="2">Order Qty.</th>
	                
	                <th width="210" colspan="3">Lay Quantity</th>
	                <th width="210" colspan="3">Cutting QC</th>
	                
	                <th width="70" rowspan="2">Today Cutting Reject</th>
	                <th width="70" rowspan="2">Cutting Reject Total</th>
	                <th width="70" rowspan="2">QC WIP</th>
	                
	                <th width="210" colspan="3">Delivery to Print</th>
	                <th width="210" colspan="3">Receive from Print</th>
	                
	                <th width="70" rowspan="2">Today Printing Reject</th>
	                <th width="70" rowspan="2">Printing Reject Total</th>
	                <th width="70" rowspan="2">Printing WIP</th>
	                
	                <th width="210" colspan="3">Delivery to Emb.</th>
	                <th width="210" colspan="3">Receive from Emb.</th>
	                
	                <th width="70" rowspan="2">Today Emb. Reject</th>
	                <th width="70" rowspan="2">Emb. Reject Total</th>
	                <th width="70" rowspan="2">Emb. WIP</th>
	                
	                <th width="210" colspan="3">Delivery to Wash</th>
	                <th width="210" colspan="3">Receive from Wash</th>
	                
	                <th width="70" rowspan="2">Today Wash Reject</th>
	                <th width="70" rowspan="2">Wash Reject Total</th>
	                <th width="70" rowspan="2">Wash WIP</th>
	                
	                <th width="210" colspan="3">Delivery to S.Work</th>
	                <th width="210" colspan="3">Receive from S.Work</th>
	                
	                <th width="70" rowspan="2">Today S. Work Reject</th>
	                <th width="70" rowspan="2">S. Work Reject Total</th>
	                <th width="70" rowspan="2">S.Works WIP</th>
	                
	                <th width="210" colspan="3">Sewing Input</th>
	                <th width="210" colspan="3">Sewing Output</th>
	                
	                <th width="70" rowspan="2">Today Sewing Reject</th>
	                <th width="70" rowspan="2">Sewing Reject Total</th>
	                <th width="70" rowspan="2">Sewing WIP</th>
	                
	                <th width="210" colspan="3">Poly Entry</th>
	                
	                <th width="70" rowspan="2">Today Poly Reject</th>
	                <th width="70" rowspan="2">Poly Reject Total</th>
	                <th width="70" rowspan="2">Poly WIP</th>
	                
	                <th width="210" colspan="3">Packing & Finishing</th>
	                
	                <th width="70" rowspan="2">Today Finishing Reject</th>
	                <th width="70" rowspan="2">Finishing Reject Total</th>
	                <th width="70" rowspan="2">Pac &Fin. WIP</th>
	                <th width="210" colspan="3">Ex-Factory</th>
	                <th width="70" rowspan="2">Ex-Fac. WIP</th>
	            </tr>
	            <tr>
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	                
	                <th width="70">Prev.</th>
	                <th width="70">Today </th>
	                <th width="70">Total </th>
	            </tr>
	        </thead>
	    </table>
	    <div style="max-height:425px; overflow-y:scroll; width:5860px;" id="scroll_body">
	    <table  border="1" class="rpt_table"  width="5840" rules="all" id="table_body" >
	        <tbody>
	        <?
			//echo "<pre>";print_r($production_data);die;
			$i=1;
			foreach($order_color_data as $buyer_id=>$buyer_data)
			{
				foreach($buyer_data as $job_no=>$job_data)
				{
					foreach($job_data as $order_id=>$order_data)
					{
						foreach($order_data as $item_id=>$item_data)
						{
							foreach($item_data as $country_id=>$country_data)
							{
								foreach($country_data as $color_id=>$value)
								{
									
									/*if(($production_data[$order_id][$item_id][$country_id][$color_id]["today_production"]>0))
									{*/
										$tot_lay_qnty=$tot_cutting_qnty=$tot_printing_qnty=$tot_printing_rcv_qnty=$tot_embroidery_qnty=$tot_embroidery_rcv_qnty=$tot_wash_qnty=$tot_wash_rcv_qnty=$tot_sp_work_qnty=$tot_sp_work_rcv_qnty=$tot_sewing_in_qnty=$tot_sewing_out_qnty=$tot_poly_qnty=$tot_paking_finish_qnty=$tot_ex_fact_qnty=0;
										$cut_qc_wip=$printing_wip=$emb_wip=$wash_wip=$sp_work_wip=$sewing_wip=$poly_wip=$finishing_wip=$ex_fact_wip=$ex_fact_wip=0;
										$total_cutting_reject=$total_printing_reject=$total_embroidery_reject=$total_wash_reject=$total_sp_work_reject=$total_sewing_reject=$total_poly_reject=$total_finish_reject=0;
										
										if ($i%2==0)
										$bgcolor="#E9F3FF";
										else
										$bgcolor="#FFFFFF";
										?>
	                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
	                                        <td width="40" align="center"><? echo $i; ?></td>
	                                        <td width="100"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
	                                        <td width="100"><p><? echo $value["style_ref_no"]; ?>&nbsp;</p></td>
	                                        <td width="60" align="center"><p><? echo $value["job_no_prefix_num"]; ?>&nbsp;</p></td>
	                                        <td width="50" align="center"><p><? echo $value["job_year"]; ?>&nbsp;</p></td>
	                                        <td width="100"><p><? echo $value["po_number"]; ?>&nbsp;</p></td>
	                                        <td width="100"><p><? echo $country_arr[$country_id]; ?>&nbsp;</p></td>
	                                        <td width="70" align="center"><p><? if($value["country_ship_date"]!="" && $value["country_ship_date"]!='0000-00-00') echo change_date_format($value["country_ship_date"]); ?>&nbsp;</p></td>
	                                        <td width="100"><p><? echo $garments_item[$item_id]; ?>&nbsp;</p></td>
	                                        <td width="100"><p><? echo $colorname_arr[$color_id]; ?>&nbsp;</p></td>
	                                        <td width="70" align="right"><? 
											echo number_format($order_color_data_orderQty[$buyer_id][$job_no][$order_id][$item_id][$country_id][$color_id]["order_quantity_qry"],0); 
											$job_order_qnty+=$order_color_data_orderQty[$buyer_id][$job_no][$order_id][$item_id][$country_id][$color_id]["order_quantity_qry"];
											$buyer_order_qnty+=$order_color_data_orderQty[$buyer_id][$job_no][$order_id][$item_id][$country_id][$color_id]["order_quantity_qry"]; 
											$gt_order_qnty+=$order_color_data_orderQty[$buyer_id][$job_no][$order_id][$item_id][$country_id][$color_id]["order_quantity_qry"]; ?></td>
	                                        
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["lay_prev_qnty"],0); $job_lay_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_prev_qnty"]; $buyer_lay_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_prev_qnty"]; $gt_lay_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["lay_qnty"],0); $job_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_qnty"]; $buyer_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_qnty"]; $gt_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_lay_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["lay_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["lay_qnty"]; echo number_format($tot_lay_qnty,0); $job_tot_lay_qnty+=$tot_lay_qnty; $buyer_tot_lay_qnty+=$tot_lay_qnty; $gt_tot_lay_qnty+=$tot_lay_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"],0); $job_cutting_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"]; $buyer_cutting_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"]; $gt_cutting_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"]; ?></td>
	                                        
	                                        
	                                        
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"],0); $job_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"]; $buyer_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"]; $gt_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"];?></td>
	                                        
	                                        
	                                        
	                                        <td width="70" align="right"><? $tot_cutting_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["cutting_qnty"]; echo number_format($tot_cutting_qnty,0); $job_tot_cutting_qnty+=$tot_cutting_qnty; $buyer_tot_cutting_qnty+=$tot_cutting_qnty; $gt_tot_cutting_qnty+=$tot_cutting_qnty;?></td>
	                                        
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"],0); $job_cutting_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"]; $buyer_cutting_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"]; $gt_cutting_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"];?></td>
	                                        <td width="70" align="right"><? $total_cutting_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_prev_qty"]; echo number_format($total_cutting_reject,0); $job_total_cutting_reject+=$total_cutting_reject; $buyer_total_cutting_reject+=$total_cutting_reject; $gt_total_cutting_reject+=$total_cutting_reject;?></td>
	                                        <td width="70" align="right"><? $cut_qc_wip=(($tot_cutting_qnty+$total_cutting_reject)-$tot_lay_qnty); echo number_format($cut_qc_wip,0); $job_cut_qc_wip+=$cut_qc_wip; $buyer_cut_qc_wip+=$cut_qc_wip; $gt_cut_qc_wip+=$cut_qc_wip;  ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"],0); $job_printing_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]; $buyer_printing_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]; $gt_printing_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"],0); $job_printing_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"]; $buyer_printing_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"]; $gt_printing_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_printing_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"]; echo number_format($tot_printing_qnty,0); $job_tot_printing_qnty+=$tot_printing_qnty; $buyer_tot_printing_qnty+=$tot_printing_qnty; $gt_tot_printing_qnty+=$tot_printing_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"],0); $job_printing_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]; $buyer_printing_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]; $gt_printing_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"],0); $job_printing_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"]; $buyer_printing_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"]; $gt_printing_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_printing_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"]; echo number_format($tot_printing_rcv_qnty,0); $job_tot_printing_rcv_qnty+=$tot_printing_rcv_qnty; $buyer_tot_printing_rcv_qnty+=$tot_printing_rcv_qnty; $gt_tot_printing_rcv_qnty+=$tot_printing_rcv_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"],0); $job_printing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"]; $buyer_printing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"]; $gt_printing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"];?></td>
	                                        <td width="70" align="right"><? $total_printing_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_prev_qty"]; echo number_format($total_printing_reject,0); $job_total_printing_reject+=$total_printing_reject; $buyer_total_printing_reject+=$total_printing_reject; $gt_total_printing_reject+=$total_printing_reject;?></td>
	                                        <td width="70" align="right"><? $printing_wip=(($tot_printing_rcv_qnty+$total_printing_reject)-$tot_printing_qnty); echo number_format($printing_wip,0); $job_printing_wip+=$printing_wip; $buyer_printing_wip+=$printing_wip; $gt_printing_wip+=$printing_wip;  ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"],0); $job_embroidery_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]; $buyer_embroidery_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]; $gt_embroidery_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"],0); $job_embroidery_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"]; $buyer_embroidery_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"]; $gt_embroidery_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_embroidery_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"]; echo number_format($tot_embroidery_qnty,0); $job_tot_embroidery_qnty+=$tot_embroidery_qnty; $buyer_tot_embroidery_qnty+=$tot_embroidery_qnty; $gt_tot_embroidery_qnty+=$tot_embroidery_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"],0); $job_embroidery_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]; $buyer_embroidery_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]; $gt_embroidery_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"],0); $job_embroidery_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"]; $buyer_embroidery_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"]; $gt_embroidery_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_embroidery_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"]; echo number_format($tot_embroidery_rcv_qnty,0); $job_tot_embroidery_rcv_qnty+=$tot_embroidery_rcv_qnty; $buyer_tot_embroidery_rcv_qnty+=$tot_embroidery_rcv_qnty; $gt_tot_embroidery_rcv_qnty+=$tot_embroidery_rcv_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"],0); $job_embroidery_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"]; $buyer_embroidery_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"];$gt_embroidery_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"];?></td>
	                                        <td width="70" align="right"><? $total_embroidery_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_prev_qty"]; echo number_format($total_embroidery_reject,0); $job_total_embroidery_reject+=$total_embroidery_reject; $buyer_total_embroidery_reject+=$total_embroidery_reject; $gt_total_embroidery_reject+=$total_embroidery_reject;?></td>
	                                        <td width="70" align="right"><? $emb_wip=(($tot_embroidery_rcv_qnty+$total_embroidery_reject)-$tot_embroidery_qnty); echo number_format($emb_wip,0); $job_emb_wip+=$emb_wip; $buyer_emb_wip+=$emb_wip; $gt_emb_wip+=$emb_wip;  ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"],0); $job_wash_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]; $buyer_wash_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]; $gt_wash_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"],0); $job_wash_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"]; $buyer_wash_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"]; $gt_wash_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_wash_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"]; echo number_format($tot_wash_qnty,0); $job_tot_wash_qnty+=$tot_wash_qnty; $buyer_tot_wash_qnty+=$tot_wash_qnty; $gt_tot_wash_qnty+=$tot_wash_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"],0); $job_wash_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]; $buyer_wash_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]; $gt_wash_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"],0); $job_wash_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"]; $buyer_wash_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"]; $gt_wash_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_wash_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"]; echo number_format($tot_wash_rcv_qnty,0); $job_tot_wash_rcv_qnty+=$tot_wash_rcv_qnty; $buyer_tot_wash_rcv_qnty+=$tot_wash_rcv_qnty; $gt_tot_wash_rcv_qnty+=$tot_wash_rcv_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"],0); $job_wash_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"]; $buyer_wash_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"]; $gt_wash_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"];?></td>
	                                        <td width="70" align="right"><? $total_wash_reject+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_prev_qty"]; echo number_format($total_wash_reject,0); $job_total_wash_reject+=$total_wash_reject; $buyer_total_wash_reject+=$total_wash_reject; $gt_total_wash_reject+=$total_wash_reject;?></td>
	                                        <td width="70" align="right"><? $wash_wip=(($tot_wash_rcv_qnty+$total_wash_reject)-$tot_wash_qnty); echo number_format($wash_wip,0); $job_wash_wip+=$wash_wip; $buyer_wash_wip+=$wash_wip; $gt_wash_wip+=$wash_wip;  ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"],0); $job_sp_work_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]; $buyer_sp_work_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]; $gt_sp_work_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"],0); $job_sp_work_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"]; $buyer_sp_work_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"]; $gt_sp_work_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_sp_work_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"]; echo number_format($tot_sp_work_qnty,0); $job_tot_sp_work_qnty+=$tot_sp_work_qnty; $buyer_tot_sp_work_qnty+=$tot_sp_work_qnty; $gt_tot_sp_work_qnty+=$tot_sp_work_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"],0); $job_sp_work_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]; $buyer_sp_work_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]; $gt_sp_work_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"],0); $job_sp_work_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"]; $buyer_sp_work_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"]; $gt_sp_work_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_sp_work_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"]; echo number_format($tot_sp_work_rcv_qnty,0); $job_tot_sp_work_rcv_qnty+=$tot_sp_work_rcv_qnty; $buyer_tot_sp_work_rcv_qnty+=$tot_sp_work_rcv_qnty; $gt_tot_sp_work_rcv_qnty+=$tot_sp_work_rcv_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"],0); $job_sp_work_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"]; $buyer_sp_work_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"]; $gt_sp_work_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"];?></td>
	                                        <td width="70" align="right"><? $total_sp_work_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_prev_qty"]; echo number_format($total_sp_work_reject,0); $job_total_sp_work_reject+=$total_sp_work_reject; $buyer_total_sp_work_reject+=$total_sp_work_reject; $gt_total_sp_work_reject+=$total_sp_work_reject;?></td>
	                                        <td width="70" align="right"><? $sp_work_wip=(($tot_sp_work_rcv_qnty+$total_sp_work_reject)-$tot_sp_work_qnty); echo number_format($sp_work_wip,0); $job_sp_work_wip+=$sp_work_wip; $buyer_sp_work_wip+=$sp_work_wip; $gt_sp_work_wip+=$sp_work_wip;?></td>
	                                        
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"],0); $job_sewing_in_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"]; $buyer_sewing_in_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"]; $gt_sewing_in_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"],0); $job_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"]; $buyer_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"]; $gt_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_sewing_in_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["sewing_in_qnty"]; echo number_format($tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $buyer_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $gt_tot_sewing_in_qnty+=$tot_sewing_in_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"],0); $job_sewing_out_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"]; $buyer_sewing_out_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"]; $gt_sewing_out_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"],0); $job_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"]; $buyer_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"]; $gt_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_sewing_out_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["sewing_out_qnty"]; echo number_format($tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty+=$tot_sewing_out_qnty; $buyer_tot_sewing_out_qnty+=$tot_sewing_out_qnty; $gt_tot_sewing_out_qnty+=$tot_sewing_out_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"],0); $job_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"]; $buyer_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"]; $gt_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"];?></td>
	                                        <td width="70" align="right"><? $total_sewing_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_prev_qty"]; echo number_format($total_sewing_reject,0); $job_total_sewing_reject+=$total_sewing_reject; $buyer_total_sewing_reject+=$total_sewing_reject; $gt_total_sewing_reject+=$total_sewing_reject;?></td>
	                                        <td width="70" align="right"><? $sewing_wip=(($tot_sewing_out_qnty+$total_sewing_reject)-$tot_sewing_in_qnty); echo number_format($sewing_wip,0); $job_sewing_wip+=$sewing_wip; $buyer_sewing_wip+=$sewing_wip; $gt_sewing_wip+=$sewing_wip;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"],0); $job_poly_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"]; $buyer_poly_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"]; $gt_poly_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"],0); $job_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"]; $buyer_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"]; $gt_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_poly_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["poly_qnty"]; echo number_format($tot_poly_qnty,0); $job_tot_poly_qnty+=$tot_poly_qnty; $buyer_tot_poly_qnty+=$tot_poly_qnty; $gt_tot_poly_qnty+=$tot_poly_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"],0); $job_poly_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"]; $buyer_poly_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"]; $gt_poly_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"];?></td>
	                                        <td width="70" align="right"><? $total_poly_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_prev_qty"]; echo number_format($total_poly_reject,0); $job_total_poly_reject+=$total_poly_reject; $buyer_total_poly_reject+=$total_poly_reject; $gt_total_poly_reject+=$total_poly_reject;?></td>
	                                        <td width="70" align="right"><? $poly_wip=(($tot_poly_qnty+$total_poly_reject)-$tot_sewing_out_qnty); echo number_format($poly_wip,0); $job_poly_wip+=$poly_wip; $buyer_poly_wip+=$poly_wip; $gt_poly_wip+=$poly_wip;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"],0); $job_paking_finish_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"]; $buyer_paking_finish_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"]; $gt_paking_finish_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"],0); $job_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"]; $buyer_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"]; $gt_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_paking_finish_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_qnty"]; echo number_format($tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty+=$tot_paking_finish_qnty; $buyer_tot_paking_finish_qnty+=$tot_paking_finish_qnty; $gt_tot_paking_finish_qnty+=$tot_paking_finish_qnty;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"],0); $job_paking_finish_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"]; $buyer_paking_finish_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"]; $gt_paking_finish_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"];?></td>
	                                        <td width="70" align="right"><? $total_finish_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_prev_qty"]; echo number_format($total_finish_reject,0); $job_total_finish_reject+=$total_finish_reject; $buyer_total_finish_reject+=$total_finish_reject; $gt_total_finish_reject+=$total_finish_reject;?></td>
	                                        <td width="70" align="right"><? $finishing_wip=(($tot_paking_finish_qnty+$total_finish_reject)-$tot_poly_qnty); echo number_format($finishing_wip,0); $job_finishing_wip+=$finishing_wip; $buyer_finishing_wip+=$finishing_wip; $gt_finishing_wip+=$finishing_wip;?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"],0); $job_ex_fact_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"]; $buyer_ex_fact_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"]; $gt_ex_fact_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"]; ?></td>
	                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"],0); $job_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"]; $buyer_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"]; $gt_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"];?></td>
	                                        <td width="70" align="right"><? $tot_ex_fact_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["ex_fact_qnty"]; echo number_format($tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $buyer_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $gt_tot_ex_fact_qnty+=$tot_ex_fact_qnty;?></td>
	                                        <td width="70" align="right"><? $ex_fact_wip=($tot_ex_fact_qnty-$tot_paking_finish_qnty); echo number_format($ex_fact_wip,0); $job_ex_fact_wip+=$ex_fact_wip; $buyer_ex_fact_wip+=$ex_fact_wip; $gt_ex_fact_wip+=$ex_fact_wip;?></td>
	                                    </tr>
	                                    <?
	                                    $i++;
									//}
									
								}
							}
						}
					}
					?>
	                <tr bgcolor="#F4F3C4">
	                    <td align="right" colspan="10" style="font-weight:bold;">Job Total:</td>
	                    <td width="70" align="right"><? echo number_format($job_order_qnty,0); $job_order_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_lay_prev_qnty,0); $job_lay_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_lay_qnty,0); $job_lay_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_lay_qnty,0); $job_tot_lay_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_cutting_prev_qnty,0); $job_cutting_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_cutting_qnty,0); $job_cutting_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_cutting_qnty,0); $job_tot_cutting_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_cutting_reject_qty,0); $job_cutting_reject_qty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_total_cutting_reject,0); $job_total_cutting_reject=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_cut_qc_wip,0); $job_cut_qc_wip=0;  ?></td>
	                    <td width="70" align="right"><? echo number_format($job_printing_prev_qnty,0); $job_printing_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_printing_qnty,0); $job_printing_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_printing_qnty,0); $job_tot_printing_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_printing_rcv_prev_qnty,0); $job_printing_rcv_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_printing_rcv_qnty,0); $job_printing_rcv_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_printing_rcv_qnty,0); $job_tot_printing_rcv_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_printing_reject_qty,0); $job_printing_reject_qty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_total_printing_reject,0); $job_total_printing_reject=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_printing_wip,0); $job_printing_wip=0;  ?></td>
	                    <td width="70" align="right"><? echo number_format($job_embroidery_prev_qnty,0); $job_embroidery_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_embroidery_qnty,0); $job_embroidery_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_embroidery_qnty,0); $job_tot_embroidery_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_embroidery_rcv_prev_qnty,0); $job_embroidery_rcv_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_embroidery_rcv_qnty,0); $job_embroidery_rcv_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_embroidery_rcv_qnty,0); $job_tot_embroidery_rcv_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_embroidery_reject_qty,0); $job_embroidery_reject_qty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_total_embroidery_reject,0); $job_total_embroidery_reject=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_emb_wip,0); $job_emb_wip=0;  ?></td>
	                    <td width="70" align="right"><? echo number_format($job_wash_prev_qnty,0); $job_wash_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_wash_qnty,0); $job_wash_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_wash_qnty,0); $job_tot_wash_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_wash_rcv_prev_qnty,0); $job_wash_rcv_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_wash_rcv_qnty,0); $job_wash_rcv_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_wash_rcv_qnty,0); $job_tot_wash_rcv_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_wash_reject_qty,0); $job_wash_reject_qty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_total_wash_reject,0); $job_total_wash_reject=0;?></td>
	                    <td width="70" align="right"><?  echo number_format($job_wash_wip,0); $job_wash_wip=0;  ?></td>
	                    <td width="70" align="right"><? echo number_format($job_sp_work_prev_qnty,0); $job_sp_work_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_sp_work_qnty,0); $job_sp_work_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_sp_work_qnty,0); $job_tot_sp_work_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_sp_work_rcv_prev_qnty,0); $job_sp_work_rcv_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_sp_work_rcv_qnty,0); $job_sp_work_rcv_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_sp_work_rcv_qnty,0); $job_tot_sp_work_rcv_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_sp_work_reject_qty,0); $job_sp_work_reject_qty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_total_sp_work_reject,0); $job_total_sp_work_reject=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_sp_work_wip,0); $job_sp_work_wip=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_sewing_in_prev_qnty,0); $job_sewing_in_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_sewing_in_qnty,0); $job_sewing_in_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_sewing_out_prev_qnty,0); $job_sewing_out_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_sewing_out_qnty,0); $job_sewing_out_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_sewing_reject_qty,0); $job_sewing_reject_qty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_total_sewing_reject,0); $job_total_sewing_reject=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_sewing_wip,0); $job_sewing_wip=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_poly_prev_qnty,0); $job_poly_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_poly_qnty,0); $job_poly_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_poly_qnty,0); $job_tot_poly_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_poly_reject_qty,0); $job_poly_reject_qty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_total_poly_reject,0); $job_total_poly_reject=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_poly_wip,0); $job_poly_wip=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_paking_finish_prev_qnty,0); $job_paking_finish_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_paking_finish_qnty,0); $job_paking_finish_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_paking_finish_reject_qty,0); $job_paking_finish_reject_qty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_total_finish_reject,0); $job_total_finish_reject=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_finishing_wip,0); $job_finishing_wip=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_ex_fact_prev_qnty,0); $job_ex_fact_prev_qnty=0; ?></td>
	                    <td width="70" align="right"><? echo number_format($job_ex_fact_qnty,0); $job_ex_fact_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty=0;?></td>
	                    <td width="70" align="right"><? echo number_format($job_ex_fact_wip,0); $job_ex_fact_wip=0;?></td>
	                </tr>
	                <?
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td align="right" colspan="10" style="font-weight:bold;">Buyer Total:</td>
	                <td width="70" align="right"><? echo number_format($buyer_order_qnty,0); $buyer_order_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_lay_prev_qnty,0); $buyer_lay_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_lay_qnty,0);  $buyer_lay_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_lay_qnty,0); $buyer_tot_lay_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_cutting_prev_qnty,0); $buyer_cutting_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_cutting_qnty,0); $buyer_cutting_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_cutting_qnty,0); $buyer_tot_cutting_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_cutting_reject_qty,0);  $buyer_cutting_reject_qty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_total_cutting_reject,0); $buyer_total_cutting_reject=0;?></td>
	                <td width="70" align="right"><? echo number_format( $job_cut_qc_wip,0); $job_cut_qc_wip=0;  ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_printing_prev_qnty,0); $ $buyer_printing_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_printing_qnty,0); $buyer_printing_qnty=0;?></td>
	                <td width="70" align="right"><?  echo number_format($buyer_tot_printing_qnty,0); $buyer_tot_printing_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_printing_rcv_prev_qnty,0); $buyer_printing_rcv_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_printing_rcv_qnty,0);  $buyer_printing_rcv_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_printing_rcv_qnty,0); $buyer_tot_printing_rcv_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_printing_reject_qty,0);  $buyer_printing_reject_qty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_total_printing_reject,0); $buyer_total_printing_reject=0;?></td>

	                <td width="70" align="right"><? echo number_format($buyer_printing_wip,0); $buyer_printing_wip=0;  ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_embroidery_prev_qnty,0);  $buyer_embroidery_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_embroidery_qnty,0);  $buyer_embroidery_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_embroidery_qnty,0); $buyer_tot_embroidery_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_embroidery_rcv_prev_qnty,0); $buyer_embroidery_rcv_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_embroidery_rcv_qnty,0); $buyer_embroidery_rcv_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_embroidery_rcv_qnty,0);  $buyer_tot_embroidery_rcv_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_embroidery_reject_qty,0); $buyer_embroidery_reject_qty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_total_embroidery_reject,0); $buyer_total_embroidery_reject=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_emb_wip,0); $buyer_emb_wip=0;  ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_wash_prev_qnty,0); $buyer_wash_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_wash_qnty,0);  $buyer_wash_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_wash_qnty,0); $buyer_tot_wash_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_wash_rcv_prev_qnty,0); $buyer_wash_rcv_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_wash_rcv_qnty,0);  $buyer_wash_rcv_qnty=0;?></td>
	                <td width="70" align="right"><?  echo number_format($buyer_tot_wash_rcv_qnty,0); $buyer_tot_wash_rcv_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_wash_reject_qty,0); $buyer_wash_reject_qty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_total_wash_reject,0); $buyer_total_wash_reject=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_wash_wip,0);  $buyer_wash_wip=0;  ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sp_work_prev_qnty,0); $buyer_sp_work_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sp_work_qnty,0);  $buyer_sp_work_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_sp_work_qnty,0);  $buyer_tot_sp_work_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sp_work_rcv_prev_qnty,0);  $buyer_sp_work_rcv_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sp_work_rcv_qnty,0);  $buyer_sp_work_rcv_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_sp_work_rcv_qnty,0); $buyer_tot_sp_work_rcv_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sp_work_reject_qty,0); $buyer_sp_work_reject_qty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_total_sp_work_reject,0); $buyer_total_sp_work_reject=0;?></td>
	                <td width="70" align="right"><? echo number_format( $buyer_sp_work_wip,0);  $buyer_sp_work_wip=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sewing_in_prev_qnty,0); $buyer_sewing_in_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sewing_in_qnty,0);  $buyer_sewing_in_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_sewing_in_qnty,0);  $buyer_tot_sewing_in_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sewing_out_prev_qnty,0); $buyer_sewing_out_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sewing_out_qnty,0);  $buyer_sewing_out_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format( $buyer_tot_sewing_out_qnty,0); $buyer_tot_sewing_out_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sewing_reject_qty,0); $buyer_sewing_reject_qty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_total_sewing_reject,0); $buyer_total_sewing_reject=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_sewing_wip,0); $buyer_sewing_wip=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_poly_prev_qnt,0); $buyer_poly_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_poly_qnty,0); $buyer_poly_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_poly_qnty,0); $buyer_tot_poly_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_poly_reject_qty,0);  $buyer_poly_reject_qty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_total_poly_reject,0); $buyer_total_poly_reject=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_poly_wip,0); $buyer_poly_wip=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_paking_finish_prev_qnty,0);  $buyer_paking_finish_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_paking_finish_qnty,0);  $buyer_paking_finish_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_paking_finish_qnty,0); $buyer_tot_paking_finish_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_paking_finish_reject_qty,0); $buyer_paking_finish_reject_qty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_total_finish_reject,0); $buyer_total_finish_reject=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_finishing_wip,0);  $buyer_finishing_wip=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_ex_fact_prev_qnty,0); $buyer_ex_fact_prev_qnty=0; ?></td>
	                <td width="70" align="right"><? echo number_format($buyer_ex_fact_qnty,0); $buyer_ex_fact_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_tot_ex_fact_qnty,0); $buyer_tot_ex_fact_qnty=0;?></td>
	                <td width="70" align="right"><? echo number_format($buyer_ex_fact_wip,0); $buyer_ex_fact_wip=0;?></td>
	            </tr>
	            <?
			}
			
	        
	        ?>
	        </tbody>
	        
	 
	    </table> 
	    </div> 
	    <table border="1" class="rpt_table"  width="5840" rules="all" style="margin-left: 2px;" align="left" id="">
	    	<tfoot>
	    		<tr>
	    			<th width="40" align="center"></th>
	    			<th width="100"><p>&nbsp;</p></th>
	    			<th width="100"><p>&nbsp;</p></th>
	    			<th width="60" align="center"><p>&nbsp;</p></th>
	    			<th width="50" align="center"><p>&nbsp;</p></th>
	    			<th width="100"><p>&nbsp;</p></th>
	    			<th width="100"><p>&nbsp;</p></th>
	    			<th width="70" align="center"><p>&nbsp;</p></th>
	    			<th width="100"><p>&nbsp;</p></th>
	     			<th width="100" align="right" style="font-weight:bold; font-size:16px;">Grand Total</th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_order_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_lay_prev_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_lay_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_lay_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_cutting_prev_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_cutting_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_cutting_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_cutting_reject_qty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_cutting_reject,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_cut_qc_wip,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_prev_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_printing_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_rcv_prev_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_rcv_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_printing_rcv_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_reject_qty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_printing_reject,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_wip,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_embroidery_prev_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_embroidery_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_embroidery_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_embroidery_rcv_prev_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_embroidery_rcv_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_embroidery_rcv_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_embroidery_reject_qty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_embroidery_reject,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_emb_wip,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_prev_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_wash_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_rcv_prev_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_rcv_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_wash_rcv_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_reject_qty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_wash_reject,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_wip,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_prev_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_sp_work_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_rcv_prev_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_rcv_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_sp_work_rcv_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_reject_qty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_sp_work_reject,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_wip,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_in_prev_qnty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_in_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_sewing_in_qnty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_out_prev_qnty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_out_qnty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_sewing_out_qnty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_reject_qty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_sewing_reject,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_wip,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_poly_prev_qnty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_poly_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_poly_qnty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_poly_reject_qty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_poly_reject,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><?  echo number_format($gt_poly_wip,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_paking_finish_prev_qnty,0);  ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_paking_finish_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_paking_finish_qnty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_paking_finish_reject_qty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_finish_reject,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_finishing_wip,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_ex_fact_prev_qnty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_ex_fact_qnty,0);?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_ex_fact_qnty,0); ?></th>
	    			<th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_ex_fact_wip,0);?></th>
	    		</tr>    
	    	</tfoot>

	    </table> 
	     </fieldset>  
	  </div>     
	  </fieldset>
	  <?
	} 
	elseif($type==3) 
	{
			$sql_lay=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, 
				CASE WHEN a.entry_date=".$txt_production_date." THEN c.size_qty ELSE 0 END AS production_qnty,
				c.size_qty  AS alls_production_qnty 
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
			where a.id=b.mst_id and b.id=c.dtls_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_cut  $order_cond_lay  and  c.order_id>0"; //
			
			/*$sql_lay=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, 
				CASE WHEN a.entry_date=".$txt_production_date." THEN c.size_qty ELSE 0 END AS production_qnty 
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=99 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name)  $order_cond_lay  ";*/
			
			 //sum(CASE WHEN production_type ='1' and production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
			//sum(CASE WHEN production_type ='1' and production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
				/*$sql_lay=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, 
				c.size_qty as production_qnty  
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=99 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name)  $order_cond_lay  ";*/ //
			
			//echo $sql_lay;
			
			$sql_lay_result=sql_select($sql_lay);
			$production_data=$porduction_ord_id=$lay_order_id=array();
			$garments_order_id_arr=array();
			foreach($sql_lay_result as $row)
			{
				/*if($row[csf("production_qnty")]>0)
				{*/
					$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$lay_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]]["lay_qnty"]+=$row[csf("production_qnty")];

					//$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["lay_prev_qnty"]+=$row[csf("production_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]]["alls_production_qnty"]+=$row[csf("alls_production_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("production_qnty")];
				//}
			}

				$production_sql="select a.production_date, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id,sum(a.carton_qty) as carton_qty, 
				sum(CASE WHEN a.production_date=".$txt_production_date." THEN b.production_qnty  ELSE 0 END) AS all_today_production_qnty,
				sum(CASE WHEN b.production_type =1 and a.production_type =1 and a.production_date=".$txt_production_date." THEN b.production_qnty  ELSE 0 END) AS cutting_qnty,
				sum(CASE WHEN b.production_type =1 and a.production_type =1 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS cutting_reject_qty,
				sum(CASE WHEN b.production_type =4 and a.production_type =4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewing_in_qnty,
				sum(CASE WHEN b.production_type =5 and a.production_type =5 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty,
				sum(CASE WHEN b.production_type =5 and a.production_type =5 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewing_reject_qty,
				sum(CASE WHEN b.production_type =7 and a.production_type =7 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS iron_qnty,
				sum(CASE WHEN b.production_type =8 and a.production_type =8 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS paking_finish_qnty,
				sum(CASE WHEN b.production_type =8 and a.production_type =8 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS paking_finish_reject_qty,
				sum(CASE WHEN b.production_type =11 and a.production_type=11 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty,
				sum(CASE WHEN b.production_type =11 and a.production_type =11  and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS poly_reject_qty,
				
				
				sum(CASE WHEN b.production_type =1 and a.production_type =1 THEN b.production_qnty  ELSE 0 END) AS alls_cutting_qnty,
				sum(CASE WHEN b.production_type =1 and a.production_type =1 THEN b.reject_qty ELSE 0 END) AS alls_cutting_reject_qty,
				sum(CASE WHEN b.production_type =4 and a.production_type =4 THEN b.production_qnty ELSE 0 END) AS alls_sewing_in_qnty,
				sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.production_qnty ELSE 0 END) AS alls_sewing_out_qnty,
				sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.reject_qty ELSE 0 END) AS alls_sewing_reject_qty,
				sum(CASE WHEN b.production_type =7 and a.production_type =7 THEN b.production_qnty ELSE 0 END) AS alls_iron_qnty,
				sum(CASE WHEN b.production_type =8 and a.production_type =8 THEN b.production_qnty ELSE 0 END) AS alls_paking_finish_qnty,
				sum(CASE WHEN b.production_type =8 and a.production_type =8 THEN b.reject_qty ELSE 0 END) AS alls_paking_finish_reject_qty,
				sum(CASE WHEN b.production_type =11 and a.production_type=11 THEN b.production_qnty ELSE 0 END) AS alls_poly_qnty,
				sum(CASE WHEN b.production_type =11 and a.production_type =11 THEN b.reject_qty ELSE 0 END) AS alls_poly_reject_qty 
				 
				from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
				where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.production_source=1 and b.production_type in(1,4,5,7,8,11) and a.production_type in(1,4,5,7,8,11) and a.serving_company in($cbo_work_company_name) $gmts_loc_floor_cond $order_cond
				group by a.production_date, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id,a.carton_qty";
			
				/*sum(CASE WHEN b.production_type =1 and a.production_type =1 and a.production_date<".$txt_production_date." THEN b.production_qnty  ELSE 0 END) AS cutting_qnty_pre,
				sum(CASE WHEN b.production_type =1 and a.production_type =1 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS cutting_reject_qty_pre,
				sum(CASE WHEN b.production_type =4 and a.production_type =4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewing_in_qnty_pre,
				sum(CASE WHEN b.production_type =5 and a.production_type =5 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty_pre,
				sum(CASE WHEN b.production_type =5 and a.production_type =5 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sewing_reject_qty_pre,
				sum(CASE WHEN b.production_type =7 and a.production_type =7 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS iron_qnty_pre,
				sum(CASE WHEN b.production_type =8 and a.production_type =8 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS paking_finish_qnty_pre,
				sum(CASE WHEN b.production_type =8 and a.production_type =8 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS paking_finish_reject_qty_pre,
				sum(CASE WHEN b.production_type =11 and a.production_type=11 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS poly_qnty_pre,
				sum(CASE WHEN b.production_type =11 and a.production_type =11  and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS poly_reject_qty_pre*/ 
			 //echo $production_sql;// die;
			
			$production_sql_result=sql_select($production_sql);
			foreach($production_sql_result as $row)
			{
				/*if($row[csf("all_today_production_qnty")]>0)
				{*/
					if($garments_order_id_arr[$row[csf("order_id")]]=="")
					{
						$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					}
					$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$gmt_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["cutting_qnty"]+=$row[csf("cutting_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["cutting_reject_qty"]+=$row[csf("cutting_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sewing_in_qnty"]+=$row[csf("sewing_in_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sewing_out_qnty"]+=$row[csf("sewing_out_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sewing_reject_qty"]+=$row[csf("sewing_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["iron_qnty"]+=$row[csf("iron_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["paking_finish_qnty"]+=$row[csf("paking_finish_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["paking_finish_reject_qty"]+=$row[csf("paking_finish_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["poly_qnty"]+=$row[csf("poly_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["poly_reject_qty"]+=$row[csf("poly_reject_qty")];
					
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["carton_qty"]+=$row[csf("carton_qty")];

					
					
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_cutting_qnty"]+=$row[csf("alls_cutting_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_cutting_reject_qty"]+=$row[csf("alls_cutting_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_sewing_in_qnty"]+=$row[csf("alls_sewing_in_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_sewing_out_qnty"]+=$row[csf("alls_sewing_out_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_sewing_reject_qty"]+=$row[csf("alls_sewing_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_iron_qnty"]+=$row[csf("alls_iron_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_paking_finish_qnty"]+=$row[csf("alls_paking_finish_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_paking_finish_reject_qty"]+=$row[csf("alls_paking_finish_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_poly_qnty"]+=$row[csf("alls_poly_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_poly_reject_qty"]+=$row[csf("alls_poly_reject_qty")];
					
					
					/*$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_qnty_pre"]+=$row[csf("cutting_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_reject_qty_pre"]+=$row[csf("cutting_reject_qty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_in_qnty_pre"]+=$row[csf("sewing_in_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_out_qnty_pre"]+=$row[csf("sewing_out_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_reject_qty_pre"]+=$row[csf("sewing_reject_qty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["iron_qnty_pre"]+=$row[csf("iron_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_qnty_pre"]+=$row[csf("paking_finish_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_reject_qty_pre"]+=$row[csf("paking_finish_reject_qty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_qnty_pre"]+=$row[csf("poly_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_reject_qty_pre"]+=$row[csf("poly_reject_qty_pre")];*/
					
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("all_today_production_qnty")];
				//}
				
			}
			//		sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS embroidery_qnty,

				$print_embro_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, 
				sum(CASE WHEN a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS all_today_production_qnty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS printing_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS printing_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS printing_reject_qty,
				sum(CASE WHEN b.production_type =2 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS embroidery_qnty,
				sum(CASE WHEN b.production_type =3 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS embroidery_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=2 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS embroidery_reject_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=3 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS wash_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS wash_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS wash_reject_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sp_work_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sp_work_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 and a.production_date=".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sp_work_reject_qty,
				
				
				sum(CASE WHEN b.production_type =2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS alls_printing_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS alls_printing_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS alls_printing_reject_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS alls_embroidery_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS alls_embroidery_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS alls_embroidery_reject_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS alls_wash_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS alls_wash_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS alls_wash_reject_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS salls_p_work_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS alls_sp_work_rcv_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.reject_qty ELSE 0 END) AS alls_sp_work_reject_qty 
				 
				from  pro_gmts_delivery_mst m, pro_garments_production_dtls b, pro_garments_production_mst a, wo_po_color_size_breakdown c 
				where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and b.production_type in(2,3) and a.production_type in(2,3) and m.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_delv $order_cond
				group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
				
				/*sum(CASE WHEN b.production_type =2 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS printing_qnty_pre,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS printing_rcv_qnty_pre,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS printing_reject_qty_pre,
				sum(CASE WHEN b.production_type =2 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS embroidery_qnty_pre,
				sum(CASE WHEN b.production_type =3 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS embroidery_rcv_qnty_pre,
				sum(CASE WHEN b.production_type =3 and a.embel_name=2 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS embroidery_reject_qty_pre,
				sum(CASE WHEN b.production_type =2 and a.embel_name=3 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS wash_qnty_pre,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS wash_rcv_qnty_pre,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS wash_reject_qty_pre,
				sum(CASE WHEN b.production_type =2 and a.embel_name=4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sp_work_qnty_pre,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 and a.production_date<".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS sp_work_rcv_qnty_pre,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 and a.production_date<".$txt_production_date." THEN b.reject_qty ELSE 0 END) AS sp_work_reject_qty_pre*/
			//echo $print_embro_sql;die;
			
			$print_embro_sql_result=sql_select($print_embro_sql);
			$print_embro_order_id=array();
			foreach($print_embro_sql_result as $row)
			{
				/*if($row[csf("all_today_production_qnty")]>0)
				{*/
					if($garments_order_id_arr[$row[csf("order_id")]]=="")
					{
						$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					}
					
					$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$print_embro_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["printing_qnty"]+=$row[csf("printing_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["printing_rcv_qnty"]+=$row[csf("printing_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["printing_reject_qty"]+=$row[csf("printing_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["embroidery_qnty"]+=$row[csf("embroidery_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["embroidery_rcv_qnty"]+=$row[csf("embroidery_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["embroidery_reject_qty"]+=$row[csf("embroidery_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["wash_qnty"]+=$row[csf("wash_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["wash_rcv_qnty"]+=$row[csf("wash_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["wash_reject_qty"]+=$row[csf("wash_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sp_work_qnty"]+=$row[csf("sp_work_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sp_work_rcv_qnty"]+=$row[csf("sp_work_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sp_work_reject_qty"]+=$row[csf("sp_work_reject_qty")];
					
					
					
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_printing_qnty"]+=$row[csf("alls_printing_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_printing_rcv_qnty"]+=$row[csf("alls_printing_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_printing_reject_qty"]+=$row[csf("alls_printing_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_embroidery_qnty"]+=$row[csf("alls_embroidery_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_embroidery_rcv_qnty"]+=$row[csf("alls_embroidery_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_embroidery_reject_qty"]+=$row[csf("alls_embroidery_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_wash_qnty"]+=$row[csf("alls_wash_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_wash_rcv_qnty"]+=$row[csf("alls_wash_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_wash_reject_qty"]+=$row[csf("alls_wash_reject_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_sp_work_qnty"]+=$row[csf("alls_sp_work_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_sp_work_rcv_qnty"]+=$row[csf("alls_sp_work_rcv_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_sp_work_reject_qty"]+=$row[csf("alls_sp_work_reject_qty")];
					
					
					/*$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_qnty_pre"]+=$row[csf("printing_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_rcv_qnty_pre"]+=$row[csf("printing_rcv_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_reject_qty_pre"]+=$row[csf("printing_reject_qty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_qnty_pre"]+=$row[csf("embroidery_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_rcv_qnty_pre"]+=$row[csf("embroidery_rcv_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_reject_qty_pre"]+=$row[csf("embroidery_reject_qty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_qnty_pre"]+=$row[csf("wash_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_rcv_qnty_pre"]+=$row[csf("wash_rcv_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_reject_qty_pre"]+=$row[csf("wash_reject_qty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_qnty_pre"]+=$row[csf("sp_work_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_rcv_qnty_pre"]+=$row[csf("sp_work_rcv_qnty_pre")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_reject_qty_pre"]+=$row[csf("sp_work_reject_qty_pre")];*/
					
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("all_today_production_qnty")];
				//}
				
			}


				$job_array=array(); $job_array_smry=array();
				$job_sql="select a.id, a.unit_price,b.buyer_name,b.company_name, b.job_no, b.total_set_qnty,b.set_smv,c.item_number_id,c.color_number_id from wo_po_break_down a, wo_po_details_master b, wo_po_color_size_breakdown c where a.job_no_mst=b.job_no and c.po_break_down_id=a.id and a.is_deleted=0 and a.status_active in(1,2,3) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
				
				$job_sql_result=sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$job_array[$row[csf("id")]]['unit_price']=$row[csf("unit_price")];
					$job_array[$row[csf("id")]]['job_no']=$row[csf("job_no")];
					$job_array[$row[csf("id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
					$job_array[$row[csf("id")]]['set_smv']=$row[csf("set_smv")];
					//$job_array_summary[$row[csf("company_name")]][$row[csf("buyer_name")]]['po_qty']+=$row[csf("po_quantity")];
					//$job_array[$row[csf("id")]][$row[csf("set_item_ratio")]]['smv_pcs']=$row[csf("smv_pcs")];
					//$job_array_smry[$row[csf("buyer_name")]]['unit_price_smry']=$row[csf("po_total_price")]/$row[csf("unit_price")];
					//$job_array_smry[$row[csf("buyer_name")]]['total_set_qnty_smry']=$row[csf("total_set_qnty")];
					$job_array_smry[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]['unit_price']=$row[csf("unit_price")];
					$job_array_smry[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
					
					
				}	  
				
				$ex_factory_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, 
				sum(CASE WHEN a.ex_factory_date=".$txt_production_date." THEN b.production_qnty ELSE 0 END) AS ex_fact_qnty,
				sum(b.production_qnty) AS alls_ex_fact_qnty
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
			where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and m.delivery_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_exfac  $order_cond
			group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
			
			
			//echo $ex_factory_sql;//die;
			$ex_factory_sql_result=sql_select($ex_factory_sql);
			foreach($ex_factory_sql_result as $row)
			{
				/*if($row[csf("ex_fact_qnty")]>0)
				{*/
					if($garments_order_id_arr[$row[csf("order_id")]]=="")
					{
						$garments_order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
					}
					
					$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$ex_fact_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["ex_fact_qnty"]+=$row[csf("ex_fact_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["alls_ex_fact_qnty"]+=$row[csf("alls_ex_fact_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["today_production"]+=$row[csf("ex_fact_qnty")];
					
					
					// new add for FOB ex factory
					$job_no_ex_all=$job_array[$row[csf("order_id")]]['job_no'];
					$total_ex_set_qnty_all=$job_array[$row[csf("order_id")]]['total_set_qnty'];
					
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["ex_fact_qnty_fob"]+=$row[csf("alls_ex_fact_qnty")]*($job_array[$row[csf("order_id")]]['unit_price']/$job_array[$row[csf("order_id")]]['total_set_qnty']);
					
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["ex_fact_qnty_fob_wips"]+=$job_array[$row[csf("order_id")]]['unit_price']/$job_array[$row[csf("order_id")]]['total_set_qnty'];
					
							
				//}
			}
			
			
			$order_prev_con=" and";
			$garments_order_arr=array_chunk($garments_order_id_arr,999);
			foreach($garments_order_arr as $order_data)
			{
				if($order_prev_con==" and")
				{
					$order_prev_con .="  ( c.order_id in(".implode(',',$order_data).")";
				}
				else
				{
					$order_prev_con .=" or c.order_id in(".implode(',',$order_data).")";
				}
			}
			$order_prev_con .=")";
			
			// previous data
			//$lay_order_id=implode(',',$lay_order_id);
			

			//sum(CASE WHEN production_type ='1' and txt_production_date=".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty,
			//sum(CASE WHEN production_type ='1' and txt_production_date<".$txt_date_from." THEN production_quantity ELSE 0 END) AS cutting_qnty_pre,
			
			if(count($garments_order_id_arr)>0)
			{
				$sql_lay_prev=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id, c.size_qty as production_qnty  
				from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
				where a.id=b.mst_id and b.id=c.dtls_id   and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_cut and a.entry_date<$txt_production_date $order_prev_con";
				
				//echo $sql_lay_prev;die;
				
				$sql_lay_prev_result=sql_select($sql_lay_prev);
				foreach($sql_lay_prev_result as $row)
				{
					$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]]["lay_prev_qnty"]+=$row[csf("production_qnty")];
				}
			}
			
			//echo "<pre>";
			//print_r($production_data);die;
			
			$order_prev_con_prod=" and";
			foreach($garments_order_arr as $order_data)
			{
				if($order_prev_con_prod==" and")
				{
					$order_prev_con_prod .="  ( a.po_break_down_id in(".implode(',',$order_data).")";
				}
				else
				{
					$order_prev_con_prod .=" or a.po_break_down_id in(".implode(',',$order_data).")";
				}
			}
			$order_prev_con_prod .=")";
			
			//$gmt_order_id=implode(',',$gmt_order_id);
			
			if(count($garments_order_id_arr)>0)
			{
				$production_prev_sql="select a.production_date, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id,
				sum(CASE WHEN b.production_type =1 THEN b.production_qnty ELSE 0 END) AS cutting_prev_qnty,
				sum(CASE WHEN b.production_type =1 THEN b.reject_qty ELSE 0 END) AS cutting_reject_prev_qty,
				sum(CASE WHEN b.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_prev_qnty,
				sum(CASE WHEN b.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_prev_qnty,
				sum(CASE WHEN b.production_type =5 THEN b.reject_qty ELSE 0 END) AS sewing_reject_prev_qty,
				sum(CASE WHEN b.production_type =7 THEN b.production_qnty ELSE 0 END) AS iron_prev_qnty,
				sum(CASE WHEN b.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_prev_qnty,
				sum(CASE WHEN b.production_type =8 THEN b.reject_qty ELSE 0 END) AS paking_finish_reject_prev_qty,
				sum(CASE WHEN b.production_type =11 THEN b.production_qnty ELSE 0 END) AS poly_prev_qnty,
				sum(CASE WHEN b.production_type =11 THEN b.reject_qty ELSE 0 END) AS poly_reject_prev_qty 
				from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
				where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.production_source=1 and b.production_type in(1,4,5,7,8,11) and a.production_type in(1,4,5,7,8,11) and a.serving_company in($cbo_work_company_name) $gmts_loc_floor_cond and a.production_date<".$txt_production_date." and b.status_active=1 and b.is_deleted=0   $order_prev_con_prod
				group by a.production_date, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id";
				
				//echo $production_prev_sql;die;
				
				$production_prev_sql_result=sql_select($production_prev_sql);
				foreach($production_prev_sql_result as $row)
				{
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["cutting_prev_qnty"]+=$row[csf("cutting_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["cutting_reject_prev_qty"]+=$row[csf("cutting_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sewing_in_prev_qnty"]+=$row[csf("sewing_in_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sewing_out_prev_qnty"]+=$row[csf("sewing_out_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sewing_reject_prev_qty"]+=$row[csf("sewing_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["iron_prev_qnty"]+=$row[csf("iron_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["paking_finish_prev_qnty"]+=$row[csf("paking_finish_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["paking_finish_reject_prev_qty"]+=$row[csf("paking_finish_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["poly_prev_qnty"]+=$row[csf("poly_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["poly_reject_prev_qty"]+=$row[csf("poly_reject_prev_qty")];
				}
			}
			
			
			
			
			//sum(CASE WHEN b.production_type =2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_prev_qnty,

			//$print_embro_order_id=implode(',',$print_embro_order_id);
			if(count($garments_order_id_arr)>0)
			{
				$print_embro_prev_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id,
				sum(CASE WHEN b.production_type =2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_rcv_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS printing_reject_prev_qty,
				
				sum(CASE WHEN b.production_type =2  THEN b.production_qnty ELSE 0 END) AS embroidery_prev_qnty,


				sum(CASE WHEN b.production_type in(1,2,3) THEN b.production_qnty ELSE 0 END) AS embroidery_rcv_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS embroidery_reject_prev_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_rcv_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS wash_reject_prev_qty,
				sum(CASE WHEN b.production_type =2 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_rcv_prev_qnty,
				sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.reject_qty ELSE 0 END) AS sp_work_reject_prev_qty
				from  pro_gmts_delivery_mst m, pro_garments_production_dtls b, pro_garments_production_mst a, wo_po_color_size_breakdown c 
				where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and b.production_type in(2,3) and a.production_type in(2,3) and m.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_delv and m.delivery_date<".$txt_production_date." $order_prev_con_prod
				group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
				
				//echo $print_embro_prev_sql;die;
				
				$print_embro_sql_result=sql_select($print_embro_prev_sql);
				foreach($print_embro_sql_result as $row)
				{
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["printing_prev_qnty"]+=$row[csf("printing_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["printing_rcv_prev_qnty"]+=$row[csf("printing_rcv_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["printing_reject_prev_qty"]+=$row[csf("printing_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["embroidery_prev_qnty"]+=$row[csf("embroidery_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["embroidery_rcv_prev_qnty"]+=$row[csf("embroidery_rcv_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["embroidery_reject_prev_qty"]+=$row[csf("embroidery_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["wash_prev_qnty"]+=$row[csf("wash_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["wash_rcv_prev_qnty"]+=$row[csf("wash_rcv_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["wash_reject_prev_qty"]+=$row[csf("wash_reject_prev_qty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sp_work_prev_qnty"]+=$row[csf("sp_work_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sp_work_rcv_prev_qnty"]+=$row[csf("sp_work_rcv_prev_qnty")];
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["sp_work_reject_prev_qty"]+=$row[csf("sp_work_reject_prev_qty")];
				}
			}
			
			
			//$ex_fact_order_id=implode(",",$ex_fact_order_id);
			if(count($garments_order_id_arr)>0)
			{
				$ex_factory_prev_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, sum(b.production_qnty) as ex_fact_qnty 
				from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
				where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and m.delivery_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_exfac and m.delivery_date<".$txt_production_date." $order_prev_con_prod
				group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
				//echo $ex_factory_prev_sql;die;
				$ex_factory_prev_sql_result=sql_select($ex_factory_prev_sql);
				foreach($ex_factory_prev_sql_result as $row)

				{
					$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]]["ex_fact_prev_qnty"]+=$row[csf("ex_fact_qnty")];
					
				}
			}
			
			
			
			if($db_type==0)
			{
				$select_year=" year(a.insert_date) as job_year";
			}
			else
			{
				$select_year=" to_char(a.insert_date,'YYYY') as job_year";
			}
			$buyer_cond="";
			if(str_replace("'","",$cbo_buyer_name)>0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name";
			$porduction_ord_id=implode(",",$porduction_ord_id);
			$porduction_ord_id=ltrim($porduction_ord_id,",");
			$pord_ord_ids=explode(",",$porduction_ord_id);  
			$pord_ord_ids=array_chunk($pord_ord_ids,999);
			
			$po_qry_cond=" and";
			foreach($pord_ord_ids as $dtls_id)
			{
			if($po_qry_cond==" and")  $po_qry_cond.="(c.po_break_down_id in(".implode(',',$dtls_id).")"; else $po_qry_cond.=" or c.po_break_down_id in(".implode(',',$dtls_id).")";
			}
			$po_qry_cond.=")";
			//echo $po_qry_cond;die;
			//echo  $po_qry_cond="select work_order_id , sum(quantity) as quantity from com_pi_item_details where work_order_dtls_id>0 and status_active=1 and is_deleted=0 $po_qry_cond group by work_order_id";
						
			if($porduction_ord_id!="")
			{
				$sql_color_size=sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, $select_year, b.po_number,b.shiping_status,b.pub_shipment_date, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity 
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and b.shiping_status!=3  $po_qry_cond $buyer_cond");
				
				/*echo "select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, $select_year, b.po_number,b.shiping_status,b.pub_shipment_date, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity 
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and  a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0    $po_qry_cond $buyer_cond";*/
				
				
			/*	echo "select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, $select_year, b.po_number,b.shiping_status,b.pub_shipment_date, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity 
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $shipping_status_cond  $po_qry_cond $buyer_cond";*/
				
				
			/*echo	"select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, $select_year, b.po_number, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity 
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.shiping_status not in(3)  $po_qry_cond $buyer_cond";*/
				
				
				
				
				
			//and b.shiping_status not in(3)
				
				/*echo "select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, $select_year, b.po_number, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity 
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
				where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.shiping_status not in(3) $po_qry_cond $buyer_cond";*/
				$order_color_data=array();
				foreach($sql_color_size as $row)
				{
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_no"]=$row[csf("job_no")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_year"]=$row[csf("job_year")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["country_ship_date"]=$row[csf("country_ship_date")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$row[csf("shiping_status")];
					$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
					
					$order_color_data_orderQty[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["order_quantity_qry"]+=$row[csf("order_quantity")];
					//$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
				}
			}
			//print_r($order_color_data);
			//echo $sql_color_size;die;
			   
			ob_start();
		 	?>
		  <fieldset style="width:3060px;">
		  <div style="width:3060px;">
		  	<table width="3080"  cellspacing="0"   >
		            <tr class="form_caption" style="border:none;">
		                   <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report</td>
		             </tr>
		            <tr style="border:none;">
		                    <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
		                    Working Company Name:<? 
							$cbo_work_company_name_arr=explode(",",$cbo_work_company_name);
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
		                    <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
		                    <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
		                    </td>
		              </tr>
		        </table>
			     <br />
			     <fieldset style="width:1540px; float:left;">
			     <legend>Report Summary Part</legend>	
			     <table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="1540" class="rpt_table" align="left" style="margin-bottom:30px;">
			        <thead>
			            <tr >
			                <th width="40" rowspan="2">SL</th>
			                <th width="100" rowspan="2">Buyer</th>
			                <th width="210" colspan="3">Cutting QC</th>             
			                <!--<th width="210" colspan="3">Delivery to Print</th>
			                <th width="210" colspan="3">Receive from Print</th>-->
			                <th width="210" colspan="3">Delivery to Emb.</th>
			                <th width="210" colspan="3">Receive from Emb.</th>
			                <th width="210" colspan="3">Sewing Input</th>
			                <th width="210" colspan="3">Sewing Output</th>
			                <th width="100" rowspan="2" title="total in -total out">Sewing WIP</th>
			               <!-- <th width="210" colspan="3">Poly Entry</th>-->
			                <th width="210" colspan="3">Packing & Finishing</th>
			                <th width="100" colspan="2">Finishing WIP</th>
			                <th width="210" colspan="4">Ex-Factory</th>
			                
			                 
			                <th width="70" rowspan="2">Ex-Fac. WIP</th>
			                <th width="70" rowspan="2">Ex-Fac. WIP FOB</th>
			                <th width="100" colspan="2">Waiting for Export</th>
			                
			                
			            </tr>
			            <tr>
			                <th width="70">Prev.</th>
			                <th width="70">Today </th>
			                <th width="70">Total </th>
			                
			                <th width="70">Prev.</th>
			                <th width="70">Today </th>
			                <th width="70">Total </th>
			                
			                <th width="70">Prev.</th>
			                <th width="70">Today </th>
			                <th width="70">Total </th>
			                
			                
			                 <!--<th width="70">Prev.</th>
			                <th width="70">Today </th>
			                <th width="70">Total </th>
			                
			                <th width="70">Prev.</th>
			                <th width="70">Today </th>
			                <th width="70">Total </th>-->
			                
			                <th width="70">Prev.</th>
			                <th width="70">Today </th>
			                <th width="70">Total </th>
			                
			                <th width="70">Prev.</th>
			                <th width="70">Today </th>
			                <th width="70">Total </th>
			                
			                <!--<th width="70">Prev.</th>
			                <th width="70">Today </th>
			                <th width="70">Total </th>-->
			                

			                <th width="70">Prev.</th>
			                <th width="70">Today </th>
			                <th width="70">Total </th>


			                <th width="50">Qty. </th>
			                <th width="50">FOB </th>
			                
			                <th width="70">Prev.</th>
			                <th width="70">Today </th>
			                <th width="70">Total </th>
			                <th width="70">Ex-Fac. FOB </th>

			                  <th width="50">Qty. </th>
			                <th width="50">FOB Value </th>
			            </tr>
			        </thead>
			        <tbody>
		        
		         <?
				$unitPriceSmry_arr=array();
				foreach($order_color_data as $buyer_id=>$buyer_data)
				{
					foreach($buyer_data as $job_no=>$job_data)
					{
						foreach($job_data as $order_id=>$order_data)
						{
							foreach($order_data as $item_id=>$item_data)
							{
								/*foreach($item_data as $country_id=>$country_data)
								{*/
									foreach($item_data as $color_id=>$value)
									{
										
										/*if(($production_data[$order_id][$item_id][$country_id][$color_id]["today_production"]>0))
										{*/
											$buyer_sammary_arr[$buyer_id]['buyer']=$buyer_data;
											
											$sammary_arr[$buyer_id]['cutting_qc']['prv']+=$production_data[$order_id][$item_id][$color_id]["cutting_prev_qnty"];
											$sammary_arr[$buyer_id]['cutting_qc']['today']+=$production_data[$order_id][$item_id][$color_id]["cutting_qnty"];
											$sammary_arr[$buyer_id]['cutting_qc']['alls_today']+=$production_data[$order_id][$item_id][$color_id]["alls_cutting_qnty"];
											$sammary_arr[$buyer_id]['printing_delv']['prv']+=$production_data[$order_id][$item_id][$color_id]["printing_prev_qnty"];
											$sammary_arr[$buyer_id]['printing_delv']['today']+=$production_data[$order_id][$item_id][$color_id]["printing_qnty"];
											$sammary_arr[$buyer_id]['printing_delv']['alls_today']+=$production_data[$order_id][$item_id][$color_id]["alls_printing_qnty"];
											$sammary_arr[$buyer_id]['printing_recv']['prv']+=$production_data[$order_id][$item_id][$color_id]["printing_rcv_prev_qnty"];
											$sammary_arr[$buyer_id]['printing_recv']['today']+=$production_data[$order_id][$item_id][$color_id]["printing_rcv_qnty"];
											$sammary_arr[$buyer_id]['printing_recv']['alls_today']+=$production_data[$order_id][$item_id][$color_id]["alls_printing_rcv_qnty"];
											$sammary_arr[$buyer_id]['embroidery_delv']['prv']+=$production_data[$order_id][$item_id][$color_id]["embroidery_prev_qnty"];
											$sammary_arr[$buyer_id]['embroidery_delv']['today']+=$production_data[$order_id][$item_id][$color_id]["embroidery_qnty"];
											$sammary_arr[$buyer_id]['embroidery_delv']['alls_today']+=$production_data[$order_id][$item_id][$color_id]["alls_embroidery_qnty"];
											$sammary_arr[$buyer_id]['embroidery_recv']['prv']+=$production_data[$order_id][$item_id][$color_id]["embroidery_rcv_prev_qnty"];
											$sammary_arr[$buyer_id]['embroidery_recv']['today']+=$production_data[$order_id][$item_id][$color_id]["embroidery_rcv_qnty"];
											$sammary_arr[$buyer_id]['embroidery_recv']['alls_today']+=$production_data[$order_id][$item_id][$color_id]["alls_embroidery_rcv_qnty"];
											$sammary_arr[$buyer_id]['sewing_in']['prv']+=$production_data[$order_id][$item_id][$color_id]["sewing_in_prev_qnty"];
											$sammary_arr[$buyer_id]['sewing_in']['today']+=$production_data[$order_id][$item_id][$color_id]["sewing_in_qnty"];
											$sammary_arr[$buyer_id]['sewing_in']['alls_today']+=$production_data[$order_id][$item_id][$color_id]["alls_sewing_in_qnty"];
											$sammary_arr[$buyer_id]['sewing_out']['prv']+=$production_data[$order_id][$item_id][$color_id]["sewing_out_prev_qnty"];
											$sammary_arr[$buyer_id]['sewing_out']['today']+=$production_data[$order_id][$item_id][$color_id]["sewing_out_qnty"];
											$sammary_arr[$buyer_id]['sewing_out']['alls_today']+=$production_data[$order_id][$item_id][$color_id]["alls_sewing_out_qnty"];
											$sammary_arr[$buyer_id]['poly_qnty']['prv']+=$production_data[$order_id][$item_id][$color_id]["poly_prev_qnty"];
											$sammary_arr[$buyer_id]['poly_qnty']['today']+=$production_data[$order_id][$item_id][$color_id]["poly_qnty"];
											$sammary_arr[$buyer_id]['poly_qnty']['alls_today']+=$production_data[$order_id][$item_id][$color_id]["alls_poly_qnty"];
											$sammary_arr[$buyer_id]['paking_finish']['prv']+=$production_data[$order_id][$item_id][$color_id]["paking_finish_prev_qnty"];
											$sammary_arr[$buyer_id]['paking_finish']['today']+=$production_data[$order_id][$item_id][$color_id]["paking_finish_qnty"];

											$pack_fin_all=$production_data[$order_id][$item_id][$color_id]["paking_finish_prev_qnty"] + $production_data[$order_id][$item_id][$color_id]["paking_finish_qnty"];

											$sammary_arr[$buyer_id]['paking_finish']['alls_today']+=$production_data[$order_id][$item_id][$color_id]["alls_paking_finish_qnty"];
											if($pack_fin_all)
											{
												$sammary_arr[$buyer_id]['finishing_fob']+= $pack_fin_all*($job_array[$order_id]['unit_price']/$job_array[$order_id]['total_set_qnty']);
											}

											

											$sammary_arr[$buyer_id]['ex_fact']['prv']+=$production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"];
											$sammary_arr[$buyer_id]['ex_fact']['today']+=$production_data[$order_id][$item_id][$color_id]["ex_fact_qnty"];
											$sammary_arr[$buyer_id]['ex_fact']['alls_today']+=$production_data[$order_id][$item_id][$color_id]["alls_ex_fact_qnty"];
																				
											$sammary_arr[$buyer_id]['ex_fact']['alls_ex_fact_qnty_fob']+=($production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"]+$production_data[$order_id][$item_id][$color_id]["ex_fact_qnty"])*($job_array[$order_id]['unit_price']/$job_array[$order_id]['total_set_qnty']);
											
											$sammary_arr[$buyer_id]['ex_fact']['alls_ex_fact_qnty_wip']+=$production_data[$order_id][$item_id][$color_id]["alls_paking_finish_qnty"]-($production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"]+$production_data[$order_id][$item_id][$color_id]["ex_fact_qnty"]);

											 							
											//$unitPriceSmry_arr[$buyer_id]=$job_array_smry[$order_id][$item_id][$color_id]['unit_price']/$job_array_smry[$order_id][$item_id][$color_id]['total_set_qnty'];
											if($job_array_smry[$order_id][$item_id][$color_id]['unit_price']/$job_array_smry[$order_id][$item_id][$color_id]['total_set_qnty'])
											{
												$unitPriceSmry_arr2[$buyer_id]+=($production_data[$order_id][$item_id][$color_id]["alls_paking_finish_qnty"]-($production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"]+$production_data[$order_id][$item_id][$color_id]["ex_fact_qnty"]))*($job_array_smry[$order_id][$item_id][$color_id]['unit_price']/$job_array_smry[$order_id][$item_id][$color_id]['total_set_qnty']);

												/*$waiting_for_export_fob[$buyer_id]+=($production_data[$order_id][$item_id][$color_id]["alls_sewing_out_qnty"]-($production_data[$order_id][$item_id][$color_id]["alls_paking_finish_qnty"])*($job_array_smry[$order_id][$item_id][$color_id]['unit_price']/$job_array_smry[$order_id][$item_id][$color_id]['total_set_qnty'])) + 
												( $production_data[$order_id][$item_id][$color_id]["alls_paking_finish_qnty"]-($production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"] ))*($job_array_smry[$order_id][$item_id][$color_id]['unit_price']/$job_array_smry[$order_id][$item_id][$color_id]['total_set_qnty']);*/

												$waiting_for_export_fob[$buyer_id]+= (($production_data[$order_id][$item_id][$color_id]["alls_sewing_out_qnty"]-($production_data[$order_id][$item_id][$color_id]["alls_paking_finish_qnty"]) ) * $job_array_smry[$order_id][$item_id][$color_id]['unit_price'] ) +
												(
												$production_data[$order_id][$item_id][$color_id]["alls_paking_finish_qnty"]-$production_data[$order_id][$item_id][$color_id]["alls_ex_fact_qnty"])* $job_array_smry[$order_id][$item_id][$color_id]['unit_price'];  

												 


											}
											 
											
											$sammary_arr[$buyer_id]['poqnty']['poQTY']+=$order_color_data[$buyer_id][$job_no][$order_id][$item_id][$color_id]["order_quantity"]+=$row[csf("order_quantity")];


										//}
									}
								//}
							}
						}
					}
				}
				
				$gTotal_cutting_qc_prv=$gTotal_cutting_qc_today=$gTotal_cutting_qc_total=$gTotal_printing_delv_prv=$gTotal_printing_delv_today=$gTotal_printing_delv_total=$gTotal_printing_recv_prv=$gTotal_printing_recv_today=$gTotal_printing_recv_total=$gTotal_embroidery_delv_prv=$gTotal_embroidery_delv_today=$gTotal_embroidery_delv_total=$gTotal_embroidery_recv_prv=
				$gTotal_embroidery_recv_today=$gTotal_embroidery_recv_total=$gTotal_sewing_in_prv=$gTotal_sewing_in_today=$gTotal_sewing_in_total=$gTotal_sewing_out_prv=$gTotal_sewing_out_today=
				$gTotal_sewing_out_total=$gTotal_poly_qnty_prv=$gTotal_poly_qnty_today=$gTotal_poly_qnty_total=$gTotal_paking_finish_prv=$gTotal_paking_finish_today=$gTotal_paking_finish_total=
				$gTotal_ex_fact_prv=$gTotal_ex_fact_today=$gTotal_ex_fact_total=0;
				$gr_sewing_wip=0;
				$gr_finishing_wip=0;
				$gr_finishing_fob=0;
				$gr_waiting_exp_wip=0;
				$gr_waiting_exp_fob=0;

				$i=1;
				foreach($buyer_sammary_arr as $buyer_id=>$buyer_data)
				{
					//if(($production_data[$order_id][$item_id][$country_id][$color_id]["today_production"]>0)){
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1st<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1st<? echo $i; ?>">
							<td width="40" align="center"><? echo $i; ?></td>
							<td width="100"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
							
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['cutting_qc']['prv'],0); $gTotal_cutting_qc_prv+=$sammary_arr[$buyer_id]['cutting_qc']['prv']; ?></td>
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['cutting_qc']['today'],0);  $gTotal_cutting_qc_today+=$sammary_arr[$buyer_id]['cutting_qc']['today']; ?></td>
		                    
		                    
		                    
							<td width="70" align="right"><? //$tot_cutting_qnty_smry=$sammary_arr[$buyer_id]['cutting_qc']['prv']+ $sammary_arr[$buyer_id]['cutting_qc']['today'];
							$tot_cutting_qnty_smry=$sammary_arr[$buyer_id]['cutting_qc']['prv']+$sammary_arr[$buyer_id]['cutting_qc']['today']; 
							echo number_format($tot_cutting_qnty_smry,0); $gTotal_cutting_qc_total+=$tot_cutting_qnty_smry; ?></td>
							
							<?php /*?><td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['printing_delv']['prv'],0);  $gTotal_printing_delv_prv+=$sammary_arr[$buyer_id]['printing_delv']['prv']; ?></td>
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['printing_delv']['today'],0);  $gTotal_printing_delv_today+=$sammary_arr[$buyer_id]['printing_delv']['today'] ;?></td>
							<td width="70" align="right"><? $tot_printing_qnty_smry=$sammary_arr[$buyer_id]['printing_delv']['prv']+ $sammary_arr[$buyer_id]['printing_delv']['today']; echo number_format($tot_printing_qnty_smry,0); $gTotal_printing_delv_total+=$tot_printing_qnty_smry ; ?></td>
							
							
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['printing_recv']['prv'],0); $gTotal_printing_recv_prv+=$sammary_arr[$buyer_id]['printing_recv']['prv']; ?></td>
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['printing_recv']['today'],0); $gTotal_printing_recv_today+=$sammary_arr[$buyer_id]['printing_recv']['today'];?></td>
							<td width="70" align="right"><? $tot_printing_rcv_qnty_smry=$sammary_arr[$buyer_id]['printing_recv']['prv']+ $sammary_arr[$buyer_id]['printing_recv']['today']; echo number_format($tot_printing_rcv_qnty_smry,0); $gTotal_printing_recv_total+=$tot_printing_rcv_qnty_smry;?></td><?php */?>
							
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['embroidery_delv']['prv'],0); $gTotal_embroidery_delv_prv+=$sammary_arr[$buyer_id]['embroidery_delv']['prv']; ?></td>
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['embroidery_delv']['today'],0); $gTotal_embroidery_delv_today+=$sammary_arr[$buyer_id]['embroidery_delv']['today'];?></td>
							<td width="70" align="right"><? $tot_embroidery_qnty_smry=$sammary_arr[$buyer_id]['embroidery_delv']['prv']+ $sammary_arr[$buyer_id]['embroidery_delv']['today']; echo number_format($tot_embroidery_qnty_smry,0); $gTotal_embroidery_delv_total+=$tot_embroidery_qnty_smry;?></td>
							
							
							
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['embroidery_recv']['prv'],0); $gTotal_embroidery_recv_prv+=$sammary_arr[$buyer_id]['embroidery_recv']['prv']; ?></td>
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['embroidery_recv']['today'],0); $gTotal_embroidery_recv_today+=$sammary_arr[$buyer_id]['embroidery_recv']['today'];?></td>
							<td width="70" align="right"><? $tot_embroidery_rcv_qnty_smry=$sammary_arr[$buyer_id]['embroidery_recv']['prv']+ $sammary_arr[$buyer_id]['embroidery_recv']['today']; echo number_format($tot_embroidery_rcv_qnty_smry,0); $gTotal_embroidery_recv_total+=$tot_embroidery_rcv_qnty_smry;?></td>
							

							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['sewing_in']['prv'],0); $gTotal_sewing_in_prv+=$sammary_arr[$buyer_id]['sewing_in']['prv']; ?></td>
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['sewing_in']['today'],0); $gTotal_sewing_in_today+=$sammary_arr[$buyer_id]['sewing_in']['today'];?></td>
							<td width="70" align="right"><? $tot_sewing_in_qnty_smry=$sammary_arr[$buyer_id]['sewing_in']['prv']+ $sammary_arr[$buyer_id]['sewing_in']['today']; echo number_format($tot_sewing_in_qnty_smry,0); $gTotal_sewing_in_total+=$tot_sewing_in_qnty_smry;?></td>
							
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['sewing_out']['prv'],0); $gTotal_sewing_out_prv+=$sammary_arr[$buyer_id]['sewing_out']['prv']; ?></td>
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['sewing_out']['today'],0); $gTotal_sewing_out_today+=$sammary_arr[$buyer_id]['sewing_out']['today'];?></td>
							<td width="70" align="right"><? $tot_sewing_out_qnty_smry=$sammary_arr[$buyer_id]['sewing_out']['prv']+ $sammary_arr[$buyer_id]['sewing_out']['today']; echo number_format($tot_sewing_out_qnty_smry,0); $gTotal_sewing_out_total+=$tot_sewing_out_qnty_smry;?></td>

							<td width="50" align="right"><? $sewing_wip= $tot_sewing_in_qnty_smry-$tot_sewing_out_qnty_smry; echo  number_format($tot_sewing_in_qnty_smry-$tot_sewing_out_qnty_smry,0); $gr_sewing_wip+=$sewing_wip; ?></td>
							
							
							<?php /*?><td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['poly_qnty']['prv'],0); $gTotal_poly_qnty_prv+=$sammary_arr[$buyer_id]['poly_qnty']['prv']; ?></td>
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['poly_qnty']['today'],0); $gTotal_poly_qnty_today+=$sammary_arr[$buyer_id]['poly_qnty']['today'];?></td>
							<td width="70" align="right"><? $tot_poly_qnty_smry=$sammary_arr[$buyer_id]['poly_qnty']['prv']+ $sammary_arr[$buyer_id]['poly_qnty']['today']; echo number_format($tot_poly_qnty_smry,0); $gTotal_poly_qnty_total+=$tot_poly_qnty_smry;?></td><?php */?>
							
							
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['paking_finish']['prv'],0); $gTotal_paking_finish_prv+=$sammary_arr[$buyer_id]['paking_finish']['prv']; ?></td>
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['paking_finish']['today'],0); $gTotal_paking_finish_today+=$sammary_arr[$buyer_id]['paking_finish']['today'];?></td>
							<td width="70" align="right"><? $tot_paking_finish_qnty_smry=$sammary_arr[$buyer_id]['paking_finish']['prv']+ $sammary_arr[$buyer_id]['paking_finish']['today']; echo number_format($tot_paking_finish_qnty_smry,0); $gTotal_paking_finish_total+=$tot_paking_finish_qnty_smry;?></td>

							<td width="50" align="right"><? $finishing_wip= $tot_sewing_out_qnty_smry-$tot_paking_finish_qnty_smry; echo   number_format($tot_sewing_out_qnty_smry-$tot_paking_finish_qnty_smry,0); $gr_finishing_wip+=$finishing_wip;?></td>
							<td width="50" align="right"><? $finishing_fob= $sammary_arr[$buyer_id]['finishing_fob']; echo   number_format($sammary_arr[$buyer_id]['finishing_fob'],0); $gr_finishing_fob+=$finishing_fob;?></td>
							
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['ex_fact']['prv'],0); $gTotal_ex_fact_prv+=$sammary_arr[$buyer_id]['ex_fact']['prv']; ?></td>
							<td width="70" align="right"><? echo number_format($sammary_arr[$buyer_id]['ex_fact']['today'],0); $gTotal_ex_fact_today+=$sammary_arr[$buyer_id]['ex_fact']['today'];?></td>
							<td width="70" align="right"><? $tot_ex_fact_qnty_smry=$sammary_arr[$buyer_id]['ex_fact']['prv']+ $sammary_arr[$buyer_id]['ex_fact']['today']; echo number_format($tot_ex_fact_qnty_smry,0); $gTotal_ex_fact_total+=$tot_ex_fact_qnty_smry;?></td>

						
		                    
		                    <td width="70" align="right"><? $tot_ex_fact_fob_qnty_smry=$sammary_arr[$buyer_id]['ex_fact']['alls_ex_fact_qnty_fob']; echo number_format($tot_ex_fact_fob_qnty_smry,0); $gTotal_ex_fact_fob_total+=$tot_ex_fact_fob_qnty_smry;?></td>

		                     <td width="70" align="right"><? $tot_ex_fact_wip_qnty_smry=$tot_paking_finish_qnty_smry-$tot_ex_fact_qnty_smry; echo number_format($tot_ex_fact_wip_qnty_smry,0); $gTotal_ex_fact_wip_total+=$tot_ex_fact_wip_qnty_smry;?></td>


		                     <td width="70" align="right"><? //$tot_ex_fact_wip_fob_qnty_smry=$sammary_arr[$buyer_id]['ex_fact']['alls_ex_fact_qnty_wip_fob'];
							$tot_ex_fact_wip_fob_qnty_smry=$unitPriceSmry_arr2[$buyer_id];
							echo number_format($tot_ex_fact_wip_fob_qnty_smry,0); $gTotal_ex_fact_wip_fob_total+=$tot_ex_fact_wip_fob_qnty_smry;?></td>

		                   

		                    	<td width="50" align="right"><? $waiting_exp_wip= $finishing_wip+$tot_ex_fact_wip_qnty_smry; echo   number_format($finishing_wip+$tot_ex_fact_wip_qnty_smry,0); $gr_waiting_exp_wip+=$waiting_exp_wip;?></td>
							<td width="50" align="right"><? $waiting_exp_fob=$waiting_for_export_fob[$buyer_id]; echo  number_format($waiting_for_export_fob[$buyer_id],0); $gr_waiting_exp_fob+=$waiting_exp_fob;?></td>


		                    
		                   
						
						</tr>
						<?
						$i++;
					//}
				}
				?>
		          	
		        </tbody>
		        <tfoot>
		        	<tr>
		                <th colspan="2"  align="right" style="font-weight:bold; font-size:16px;">Grand Total</th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_cutting_qc_prv,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_cutting_qc_today,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_cutting_qc_total,0);?></th>
		               <?php /*?> <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_delv_prv,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_delv_today,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_delv_total,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_recv_prv,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_recv_today,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_printing_recv_total,0);?></th><?php */?>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_delv_prv,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_delv_today,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_delv_total,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_recv_prv,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_recv_today,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_embroidery_recv_total,0);?></th>
		                
		                
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_in_prv,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_in_today,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_in_total,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_out_prv,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_out_today,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_sewing_out_total,0); ?></th>
		                <th width="50" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gr_sewing_wip,0); ?></th>
		               <?php /*?> <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_poly_qnty_prv,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_poly_qnty_today,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_poly_qnty_total,0); ?></th><?php */?>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_paking_finish_prv,0);  ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_paking_finish_today,0);?></th>

		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_paking_finish_total,0); ?></th>
		                <th width="50" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gr_finishing_wip,0); ?></th>
		                <th width="50" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gr_finishing_fob,0); ?></th>

		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_ex_fact_prv,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_ex_fact_today,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_ex_fact_total,0); ?></th>
		                
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_ex_fact_fob_total,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_ex_fact_wip_total,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gTotal_ex_fact_wip_fob_total,0); ?></th>

		                <th width="50" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gr_waiting_exp_wip,0); ?></th>
		                <th width="50" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gr_waiting_exp_fob,0); ?></th>

		                
		             </tr>
		          </tfoot>
		    </table>
			</fieldset>
		  	<fieldset style="width:3130px; float:left;">
		    <legend>Report Details Part</legend>
		     <table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="3110" class="rpt_table" align="left">
		     
		    <!-- <table width="3300" id="table_header_1" border="1" class="rpt_table" rules="all">-->

		        <thead>
		            <tr>
		                <th width="40" rowspan="2">SL</th>
		                <th width="100" rowspan="2">Buyer</th>
		                <th width="100" rowspan="2">Style Ref</th>
		                <th width="60" rowspan="2">Job No</th>
		                <th width="50" rowspan="2">Year</th>
		                <th width="100" rowspan="2">Order No</th>
		                <th width="100" rowspan="2">Ship Status</th>
		                <th width="70" rowspan="2">Publish  Ship date</th>
		                <th width="100" rowspan="2">Garment Item</th>
		                <th width="100" rowspan="2">Color</th>
		                <th width="70" rowspan="2">Order Qty.</th>
		                
		                <th width="210" colspan="3">Lay Quantity</th>
		                <th width="210" colspan="3">Cutting QC</th>
		                
		               <!-- <th width="70" rowspan="2">Today Cutting Reject</th>
		                <th width="70" rowspan="2">Cutting Reject Total</th>
		                <th width="70" rowspan="2">QC WIP</th>
		                
		                <th width="210" colspan="3">Delivery to Print</th>
		                <th width="210" colspan="3">Receive from Print</th>
		                
		                <th width="70" rowspan="2">Today Printing Reject</th>
		                <th width="70" rowspan="2">Printing Reject Total</th>
		                <th width="70" rowspan="2">Printing WIP</th>-->
		                <th width="70" rowspan="2">Total Emb. Issue Qty</th>
		                <th width="70" rowspan="2">Total Emb. Rcv. Qty</th>
		                
		                
		               <!-- <th width="210" colspan="3">Delivery to Emb.</th>
		                <th width="210" colspan="3">Receive from Emb.</th>-->
		                
		               <!-- <th width="70" rowspan="2">Today Emb. Reject</th>
		                <th width="70" rowspan="2">Emb. Reject Total</th>
		                <th width="70" rowspan="2">Emb. WIP</th>
		                
		                <th width="210" colspan="3">Delivery to Wash</th>
		                <th width="210" colspan="3">Receive from Wash</th>
		                
		                <th width="70" rowspan="2">Today Wash Reject</th>
		                <th width="70" rowspan="2">Wash Reject Total</th>
		                <th width="70" rowspan="2">Wash WIP</th>
		                
		                <th width="210" colspan="3">Delivery to S.Work</th>
		                <th width="210" colspan="3">Receive from S.Work</th>
		                
		                <th width="70" rowspan="2">Today S. Work Reject</th>
		                <th width="70" rowspan="2">S. Work Reject Total</th>
		                <th width="70" rowspan="2">S.Works WIP</th>-->
		                
		                <th width="210" colspan="3">Sewing Input</th>
		                <th width="210" colspan="3">Sewing Output</th>
		                
		                <!--<th width="70" rowspan="2">Today Sewing Reject</th>
		                <th width="70" rowspan="2">Sewing Reject Total</th>-->
		                <th width="70" rowspan="2">Sewing WIP</th>
		                
		               <!-- <th width="210" colspan="3">Poly Entry</th>-->
		                
		                <!--<th width="70" rowspan="2">Today Poly Reject</th>
		                <th width="70" rowspan="2">Poly Reject Total</th>-->
		                <!--<th width="70" rowspan="2">Poly WIP</th>-->
		                
		                <th width="210" colspan="3">Packing & Finishing</th>
		                
		                <th width="70" rowspan="2">Total Ctn Qty</th>
		                <!--<th width="70" rowspan="2">Today Finishing Reject</th>
		                <th width="70" rowspan="2">Finishing Reject Total</th>-->
		                <th width="70" rowspan="2">Pac &Fin. WIP</th>
		                <th width="210" colspan="3">Ex-Factory</th>
		                <th width="70" rowspan="2">Ex-Fac. FOB</th>
		                <th width="70" rowspan="2">Ex-Fac. WIP</th>
		                <th width="70" rowspan="2">Ex-Fac. WIP FOB</th>
		                
		                
		            </tr>
		            <tr>
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		               <!-- <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>-->
		                
		                <!--<th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>-->
		                
		                <!--<th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>-->
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		               <!-- <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>-->
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		                
		                <th width="70">Prev.</th>
		                <th width="70">Today </th>
		                <th width="70">Total </th>
		            </tr>
		        </thead>
		    </table>
		    <div style="max-height:400px; overflow-y:scroll; width:3130px;" id="scroll_body">
		    <table  border="1" class="rpt_table"  width="3110" rules="all" id="table_body" >
		        <tbody>
		        <?
				//echo "<pre>";print_r($production_data);die;
				$i=1;
					$tot_lay_qnty=$tot_cutting_qnty=$tot_printing_qnty=$tot_printing_rcv_qnty=$tot_embroidery_qnty=$tot_embroidery_rcv_qnty=$tot_wash_qnty=$tot_wash_rcv_qnty=$tot_sp_work_qnty=$tot_sp_work_rcv_qnty=$tot_sewing_in_qnty=$tot_sewing_out_qnty=$tot_poly_qnty=$tot_paking_finish_qnty=$tot_ex_fact_qnty=0;
											$cut_qc_wip=$printing_wip=$emb_wip=$wash_wip=$sp_work_wip=$sewing_wip=$poly_wip=$finishing_wip=$ex_fact_wip=$ex_fact_wip=0;
											$total_cutting_reject=$total_printing_reject=$total_embroidery_reject=$total_wash_reject=$total_sp_work_reject=$total_sewing_reject=$total_poly_reject=$total_finish_reject=0;
				foreach($order_color_data as $buyer_id=>$buyer_data)
				{
					foreach($buyer_data as $job_no=>$job_data)
					{
						foreach($job_data as $order_id=>$order_data)
						{
							foreach($order_data as $item_id=>$item_data)
							{
								/*foreach($item_data as $country_id=>$country_data)
								{*/
									foreach($item_data as $color_id=>$value)
									{
										
										/*if(($production_data[$order_id][$item_id][$country_id][$color_id]["today_production"]>0))
										{*/
										
											
											if ($i%2==0)
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";
											?>
		                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
		                                        <td width="40" align="center"><? echo $i; ?></td>
		                                        <td width="100"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
		                                        <td width="100"><p><? echo $value["style_ref_no"]; ?>&nbsp;</p></td>
		                                        <td width="60" align="center"><p><? echo $value["job_no_prefix_num"]; ?>&nbsp;</p></td>
		                                        <td width="50" align="center"><p><? echo $value["job_year"]; ?>&nbsp;</p></td>
		                                        <td width="100"><p><? echo $value["po_number"]; ?>&nbsp;</p></td>
		                                        <td width="100" align="center"><p><? echo $shipment_status[$value["shiping_status"]]; ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($value["pub_shipment_date"]!="" && $value["pub_shipment_date"]!='0000-00-00') echo change_date_format($value["pub_shipment_date"]); ?>&nbsp;</p></td>
		                                        <td width="100"><p><? echo $garments_item[$item_id]; ?>&nbsp;</p></td>
		                                        <td width="100"><p><? echo $colorname_arr[$color_id]; ?>&nbsp;</p></td>
		                                        <td width="70" align="right"><? 
												echo number_format($order_color_data_orderQty[$buyer_id][$job_no][$order_id][$item_id][$color_id]["order_quantity_qry"],0); 
												//echo number_format($value["order_quantity"],0); 
												$job_order_qnty+=$order_color_data_orderQty[$buyer_id][$job_no][$order_id][$item_id][$color_id]["order_quantity_qry"];
												$po_order_qnty+=$order_color_data_orderQty[$buyer_id][$job_no][$order_id][$item_id][$color_id]["order_quantity_qry"];
												$buyer_order_qnty+=$order_color_data_orderQty[$buyer_id][$job_no][$order_id][$item_id][$color_id]["order_quantity_qry"]; 
												$gt_order_qnty+=$order_color_data_orderQty[$buyer_id][$job_no][$order_id][$item_id][$color_id]["order_quantity_qry"]; ?></td>
		                                        
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["lay_prev_qnty"],0); $job_lay_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["lay_prev_qnty"];$po_lay_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["lay_prev_qnty"]; $buyer_lay_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["lay_prev_qnty"]; $gt_lay_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["lay_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["lay_qnty"],0); $job_lay_qnty+=$production_data[$order_id][$item_id][$color_id]["lay_qnty"];$po_lay_qnty+=$production_data[$order_id][$item_id][$color_id]["lay_qnty"]; $buyer_lay_qnty+=$production_data[$order_id][$item_id][$color_id]["lay_qnty"]; $gt_lay_qnty+=$production_data[$order_id][$item_id][$color_id]["lay_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_lay_qnty=$production_data[$order_id][$item_id][$color_id]["lay_prev_qnty"]+ $production_data[$order_id][$item_id][$color_id]["lay_qnty"]; echo number_format($tot_lay_qnty,0); $job_tot_lay_qnty+=$tot_lay_qnty;$po_tot_lay_qnty+=$tot_lay_qnty; $buyer_tot_lay_qnty+=$tot_lay_qnty; $gt_tot_lay_qnty+=$tot_lay_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["cutting_prev_qnty"],0); $job_cutting_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["cutting_prev_qnty"];$po_cutting_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["cutting_prev_qnty"]; $buyer_cutting_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["cutting_prev_qnty"]; $gt_cutting_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["cutting_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["cutting_qnty"],0); $job_cutting_qnty+=$production_data[$order_id][$item_id][$color_id]["cutting_qnty"];$po_cutting_qnty+=$production_data[$order_id][$item_id][$color_id]["cutting_qnty"]; $buyer_cutting_qnty+=$production_data[$order_id][$item_id][$color_id]["cutting_qnty"]; $gt_cutting_qnty+=$production_data[$order_id][$item_id][$color_id]["cutting_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_cutting_qnty=$production_data[$order_id][$item_id][$color_id]["cutting_prev_qnty"]+ $production_data[$order_id][$item_id][$color_id]["cutting_qnty"]; echo number_format($tot_cutting_qnty,0); $job_tot_cutting_qnty+=$tot_cutting_qnty;$po_tot_cutting_qnty+=$tot_cutting_qnty; $buyer_tot_cutting_qnty+=$tot_cutting_qnty; $gt_tot_cutting_qnty+=$tot_cutting_qnty;?></td>
		                                 
		                                 
		                                     
		                                        <?php /*?><td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"],0); $job_cutting_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"];$po_cutting_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"]; $buyer_cutting_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"]; $gt_cutting_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_cutting_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["cutting_reject_prev_qty"]; echo number_format($total_cutting_reject,0); $job_total_cutting_reject+=$total_cutting_reject;$po_total_cutting_reject+=$total_cutting_reject; $buyer_total_cutting_reject+=$total_cutting_reject; $gt_total_cutting_reject+=$total_cutting_reject;?></td>
		                                        <td width="70" align="right"><? $cut_qc_wip=(($tot_cutting_qnty+$total_cutting_reject)-$tot_lay_qnty); echo number_format($cut_qc_wip,0); $job_cut_qc_wip+=$cut_qc_wip; $po_cut_qc_wip+=$cut_qc_wip;$buyer_cut_qc_wip+=$cut_qc_wip; $gt_cut_qc_wip+=$cut_qc_wip;  ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"],0); $job_printing_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"];$po_printing_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]; $buyer_printing_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]; $gt_printing_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"],0); $job_printing_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"];$po_printing_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"]; $buyer_printing_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"]; $gt_printing_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_printing_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["printing_qnty"]; echo number_format($tot_printing_qnty,0); $job_tot_printing_qnty+=$tot_printing_qnty;$po_tot_printing_qnty+=$tot_printing_qnty; $buyer_tot_printing_qnty+=$tot_printing_qnty; $gt_tot_printing_qnty+=$tot_printing_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"],0); $job_printing_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"];$po_printing_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]; $buyer_printing_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]; $gt_printing_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"],0); $job_printing_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"];$po_printing_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"]; $buyer_printing_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"]; $gt_printing_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_printing_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["printing_rcv_qnty"]; echo number_format($tot_printing_rcv_qnty,0); $job_tot_printing_rcv_qnty+=$tot_printing_rcv_qnty;$po_tot_printing_rcv_qnty+=$tot_printing_rcv_qnty; $buyer_tot_printing_rcv_qnty+=$tot_printing_rcv_qnty; $gt_tot_printing_rcv_qnty+=$tot_printing_rcv_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"],0); $job_printing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"];$po_printing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"]; $buyer_printing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"]; $gt_printing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_printing_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["printing_reject_prev_qty"]; echo number_format($total_printing_reject,0); $job_total_printing_reject+=$total_printing_reject;$po_total_printing_reject+=$total_printing_reject; $buyer_total_printing_reject+=$total_printing_reject; $gt_total_printing_reject+=$total_printing_reject;?></td>
		                                        <td width="70" align="right"><? $printing_wip=(($tot_printing_rcv_qnty+$total_printing_reject)-$tot_printing_qnty); echo number_format($printing_wip,0); $job_printing_wip+=$printing_wip;$po_printing_wip+=$printing_wip; $buyer_printing_wip+=$printing_wip; $gt_printing_wip+=$printing_wip;  ?></td><?php */?>
		                                       
		                                          <td width="70" align="right"><? $tot_embroidery_qnty=$production_data[$order_id][$item_id][$color_id]["embroidery_prev_qnty"]+ $production_data[$order_id][$item_id][$color_id]["embroidery_qnty"]; //echo number_format($tot_embroidery_qnty,0); 
												  
												  $job_tot_embroidery_qnty+=$tot_embroidery_qnty;$po_tot_embroidery_qnty+=$tot_embroidery_qnty; $buyer_tot_embroidery_qnty+=$tot_embroidery_qnty; $gt_tot_embroidery_qnty+=$tot_embroidery_qnty;?>
		                                          
		                                        <a href="##" onClick="openmypage(<? echo $order_id.','.$item_id.','.'2'.','.$country_id; ?>)"><? echo $tot_embroidery_qnty; ?></a>
		                                          </td>
		                                          <td width="70" align="right"><? $tot_embroidery_rcv_qnty=$production_data[$order_id][$item_id][$color_id]["embroidery_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$color_id]["embroidery_rcv_qnty"]; //echo number_format($tot_embroidery_rcv_qnty,0); 
												  $job_tot_embroidery_rcv_qnty+=$tot_embroidery_rcv_qnty;$po_tot_embroidery_rcv_qnty+=$tot_embroidery_rcv_qnty; $buyer_tot_embroidery_rcv_qnty+=$tot_embroidery_rcv_qnty; $gt_tot_embroidery_rcv_qnty+=$tot_embroidery_rcv_qnty;?>
		                                           <a href="##" onClick="openmypage(<? echo $order_id.','.$item_id.','.'3'.','.$country_id; ?>)"><? echo $tot_embroidery_rcv_qnty; ?></a>
		                                          
		                                          </td>
		                                       
		                                       
		                                       <?php /*?> <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"],0); $job_embroidery_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"];$po_embroidery_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]; $buyer_embroidery_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]; $gt_embroidery_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"],0); $job_embroidery_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"];$po_embroidery_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"]; $buyer_embroidery_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"]; $gt_embroidery_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_qnty"];?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"],0); $job_embroidery_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]; $po_embroidery_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]; $buyer_embroidery_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]; $gt_embroidery_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"],0); $job_embroidery_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"];$po_embroidery_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"]; $buyer_embroidery_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"]; $gt_embroidery_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_rcv_qnty"];?></td><?php */?>
		                                       
		                                       
		                                       
		                                       
		                                       
		                                   <?php /*?>     <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"],0); $job_embroidery_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"];$po_embroidery_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"]; $buyer_embroidery_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"];$gt_embroidery_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_embroidery_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["embroidery_reject_prev_qty"]; echo number_format($total_embroidery_reject,0); $job_total_embroidery_reject+=$total_embroidery_reject; $po_total_embroidery_reject+=$total_embroidery_reject; $buyer_total_embroidery_reject+=$total_embroidery_reject; $gt_total_embroidery_reject+=$total_embroidery_reject;?></td>
		                                        <td width="70" align="right"><? $emb_wip=(($tot_embroidery_rcv_qnty+$total_embroidery_reject)-$tot_embroidery_qnty); echo number_format($emb_wip,0); $job_emb_wip+=$emb_wip; $po_emb_wip+=$emb_wip; $buyer_emb_wip+=$emb_wip; $gt_emb_wip+=$emb_wip;  ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"],0); $job_wash_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"];$po_wash_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]; $buyer_wash_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]; $gt_wash_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"],0); $job_wash_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"];$po_wash_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"]; $buyer_wash_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"]; $gt_wash_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_wash_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["wash_qnty"]; echo number_format($tot_wash_qnty,0); $job_tot_wash_qnty+=$tot_wash_qnty; $po_tot_wash_qnty+=$tot_wash_qnty; $buyer_tot_wash_qnty+=$tot_wash_qnty; $gt_tot_wash_qnty+=$tot_wash_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"],0); $job_wash_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"];$po_wash_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]; $buyer_wash_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]; $gt_wash_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"],0); $job_wash_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"];$po_wash_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"]; $buyer_wash_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"]; $gt_wash_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_wash_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["wash_rcv_qnty"]; echo number_format($tot_wash_rcv_qnty,0); $job_tot_wash_rcv_qnty+=$tot_wash_rcv_qnty;$po_tot_wash_rcv_qnty+=$tot_wash_rcv_qnty; $buyer_tot_wash_rcv_qnty+=$tot_wash_rcv_qnty; $gt_tot_wash_rcv_qnty+=$tot_wash_rcv_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"],0); $job_wash_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"];$po_wash_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"]; $buyer_wash_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"]; $gt_wash_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_wash_reject+=$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["wash_reject_prev_qty"]; echo number_format($total_wash_reject,0); $job_total_wash_reject+=$total_wash_reject;$po_total_wash_reject+=$total_wash_reject; $buyer_total_wash_reject+=$total_wash_reject; $gt_total_wash_reject+=$total_wash_reject;?></td>
		                                        <td width="70" align="right"><? $wash_wip=(($tot_wash_rcv_qnty+$total_wash_reject)-$tot_wash_qnty); echo number_format($wash_wip,0); $job_wash_wip+=$wash_wip;$po_wash_wip+=$wash_wip; $buyer_wash_wip+=$wash_wip; $gt_wash_wip+=$wash_wip;  ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"],0); $job_sp_work_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"];$po_sp_work_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]; $buyer_sp_work_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]; $gt_sp_work_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"],0); $job_sp_work_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"]; $po_sp_work_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"]; $buyer_sp_work_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"]; $gt_sp_work_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_sp_work_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_qnty"]; echo number_format($tot_sp_work_qnty,0); $job_tot_sp_work_qnty+=$tot_sp_work_qnty;$po_tot_sp_work_qnty+=$tot_sp_work_qnty; $buyer_tot_sp_work_qnty+=$tot_sp_work_qnty; $gt_tot_sp_work_qnty+=$tot_sp_work_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"],0); $job_sp_work_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"];$po_sp_work_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]; $buyer_sp_work_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]; $gt_sp_work_rcv_prev_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"],0); $job_sp_work_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"];$po_sp_work_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"]; $buyer_sp_work_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"]; $gt_sp_work_rcv_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_sp_work_rcv_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_prev_qnty"]+ $production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_rcv_qnty"]; echo number_format($tot_sp_work_rcv_qnty,0); $job_tot_sp_work_rcv_qnty+=$tot_sp_work_rcv_qnty;$po_tot_sp_work_rcv_qnty+=$tot_sp_work_rcv_qnty; $buyer_tot_sp_work_rcv_qnty+=$tot_sp_work_rcv_qnty; $gt_tot_sp_work_rcv_qnty+=$tot_sp_work_rcv_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"],0); $job_sp_work_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"]; $po_sp_work_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"];$buyer_sp_work_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"]; $gt_sp_work_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_sp_work_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["sp_work_reject_prev_qty"]; echo number_format($total_sp_work_reject,0); $job_total_sp_work_reject+=$total_sp_work_reject; $po_total_sp_work_reject+=$total_sp_work_reject; $buyer_total_sp_work_reject+=$total_sp_work_reject; $gt_total_sp_work_reject+=$total_sp_work_reject;?></td>
		                                        <td width="70" align="right"><? $sp_work_wip=(($tot_sp_work_rcv_qnty+$total_sp_work_reject)-$tot_sp_work_qnty); echo number_format($sp_work_wip,0); $job_sp_work_wip+=$sp_work_wip;$po_sp_work_wip+=$sp_work_wip; $buyer_sp_work_wip+=$sp_work_wip; $gt_sp_work_wip+=$sp_work_wip;?></td><?php */?>
		                                        
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["sewing_in_prev_qnty"],0); $job_sewing_in_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_in_prev_qnty"];$po_sewing_in_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_in_prev_qnty"]; $buyer_sewing_in_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_in_prev_qnty"]; $gt_sewing_in_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_in_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["sewing_in_qnty"],0); $job_sewing_in_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_in_qnty"];$po_sewing_in_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_in_qnty"]; $buyer_sewing_in_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_in_qnty"]; $gt_sewing_in_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_in_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_sewing_in_qnty=$production_data[$order_id][$item_id][$color_id]["sewing_in_prev_qnty"]+ $production_data[$order_id][$item_id][$color_id]["sewing_in_qnty"]; echo number_format($tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty+=$tot_sewing_in_qnty;$po_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $buyer_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $gt_tot_sewing_in_qnty+=$tot_sewing_in_qnty;?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["sewing_out_prev_qnty"],0); $job_sewing_out_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_out_prev_qnty"];$po_sewing_out_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_out_prev_qnty"]; $buyer_sewing_out_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_out_prev_qnty"]; $gt_sewing_out_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_out_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["sewing_out_qnty"],0); $job_sewing_out_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_out_qnty"];$po_sewing_out_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_out_qnty"]; $buyer_sewing_out_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_out_qnty"]; $gt_sewing_out_qnty+=$production_data[$order_id][$item_id][$color_id]["sewing_out_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_sewing_out_qnty=$production_data[$order_id][$item_id][$color_id]["sewing_out_prev_qnty"]+ $production_data[$order_id][$item_id][$color_id]["sewing_out_qnty"]; echo number_format($tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty+=$tot_sewing_out_qnty;$po_tot_sewing_out_qnty+=$tot_sewing_out_qnty; $buyer_tot_sewing_out_qnty+=$tot_sewing_out_qnty; $gt_tot_sewing_out_qnty+=$tot_sewing_out_qnty;?></td>
		                                       
		                                      <?php /*?>  <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"],0); $job_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"];$po_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"]; $buyer_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"]; $gt_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_sewing_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["sewing_reject_prev_qty"]; echo number_format($total_sewing_reject,0); $job_total_sewing_reject+=$total_sewing_reject;$po_total_sewing_reject+=$total_sewing_reject; $buyer_total_sewing_reject+=$total_sewing_reject; $gt_total_sewing_reject+=$total_sewing_reject;?></td><?php */?>
		                                        <td width="70" align="right"><? $sewing_wip=(($tot_sewing_out_qnty+$total_sewing_reject)-$tot_sewing_in_qnty); echo number_format($sewing_wip,0); $job_sewing_wip+=$sewing_wip; $po_sewing_wip+=$sewing_wip;$buyer_sewing_wip+=$sewing_wip; $gt_sewing_wip+=$sewing_wip;?></td>
		                                        <?php /*?><td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["poly_prev_qnty"],0); $job_poly_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["poly_prev_qnty"];$po_poly_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["poly_prev_qnty"]; $buyer_poly_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["poly_prev_qnty"]; $gt_poly_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["poly_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["poly_qnty"],0); $job_poly_qnty+=$production_data[$order_id][$item_id][$color_id]["poly_qnty"];$po_poly_qnty+=$production_data[$order_id][$item_id][$color_id]["poly_qnty"]; $buyer_poly_qnty+=$production_data[$order_id][$item_id][$color_id]["poly_qnty"]; $gt_poly_qnty+=$production_data[$order_id][$item_id][$color_id]["poly_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_poly_qnty=$production_data[$order_id][$item_id][$color_id]["poly_prev_qnty"]+ $production_data[$order_id][$item_id][$color_id]["poly_qnty"]; echo number_format($tot_poly_qnty,0); $job_tot_poly_qnty+=$tot_poly_qnty;$po_tot_poly_qnty+=$tot_poly_qnty; $buyer_tot_poly_qnty+=$tot_poly_qnty; $gt_tot_poly_qnty+=$tot_poly_qnty;?></td><?php */?>
		                                       <?php /*?> <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"],0); $job_poly_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"];$po_poly_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"]; $buyer_poly_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"]; $gt_poly_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_poly_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["poly_reject_prev_qty"]; echo number_format($total_poly_reject,0); $job_total_poly_reject+=$total_poly_reject;$po_total_poly_reject+=$total_poly_reject; $buyer_total_poly_reject+=$total_poly_reject; $gt_total_poly_reject+=$total_poly_reject;?></td><?php */?>
		                                        <?php /*?><td width="70" align="right"><? $poly_wip=(($tot_poly_qnty+$total_poly_reject)-$tot_sewing_out_qnty); echo number_format($poly_wip,0); $job_poly_wip+=$poly_wip;$po_poly_wip+=$poly_wip; $buyer_poly_wip+=$poly_wip; $gt_poly_wip+=$poly_wip;?></td><?php */?>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["paking_finish_prev_qnty"],0); $job_paking_finish_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["paking_finish_prev_qnty"];$po_paking_finish_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["paking_finish_prev_qnty"]; $buyer_paking_finish_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["paking_finish_prev_qnty"]; $gt_paking_finish_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["paking_finish_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["paking_finish_qnty"],0); $job_paking_finish_qnty+=$production_data[$order_id][$item_id][$color_id]["paking_finish_qnty"];$po_paking_finish_qnty+=$production_data[$order_id][$item_id][$color_id]["paking_finish_qnty"]; $buyer_paking_finish_qnty+=$production_data[$order_id][$item_id][$color_id]["paking_finish_qnty"]; $gt_paking_finish_qnty+=$production_data[$order_id][$item_id][$color_id]["paking_finish_qnty"];?></td>
		                                        <td width="70" align="right"><? $tot_paking_finish_qnty=$production_data[$order_id][$item_id][$color_id]["paking_finish_prev_qnty"]+ $production_data[$order_id][$item_id][$color_id]["paking_finish_qnty"]; echo number_format($tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty+=$tot_paking_finish_qnty;$po_tot_paking_finish_qnty+=$tot_paking_finish_qnty; $buyer_tot_paking_finish_qnty+=$tot_paking_finish_qnty; $gt_tot_paking_finish_qnty+=$tot_paking_finish_qnty;?></td>
		                                        
		                                        
		                                        
		                                         <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["carton_qty"],0); $job_carton_qnty+=$production_data[$order_id][$item_id][$color_id]["carton_qty"];$po_carton_qnty+=$production_data[$order_id][$item_id][$color_id]["carton_qty"]; $buyer_carton_qnty+=$production_data[$order_id][$item_id][$color_id]["carton_qty"]; $gt_carton_qnty+=$production_data[$order_id][$item_id][$color_id]["carton_qty"];?></td>

		                                        
		                                        
		                                       <?php /*?> 
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"],0); $job_paking_finish_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"]; $po_paking_finish_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"]; $buyer_paking_finish_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"]; $gt_paking_finish_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"];?></td>
		                                        <td width="70" align="right"><? $total_finish_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_qty"]+$production_data[$order_id][$item_id][$country_id][$color_id]["paking_finish_reject_prev_qty"]; echo number_format($total_finish_reject,0); $job_total_finish_reject+=$total_finish_reject;$po_total_finish_reject+=$total_finish_reject; $buyer_total_finish_reject+=$total_finish_reject; $gt_total_finish_reject+=$total_finish_reject;?></td><?php */?>
		                                        <td width="70" align="right"><? $finishing_wip=($tot_sewing_out_qnty-$tot_paking_finish_qnty); echo number_format($finishing_wip,0); $job_finishing_wip+=$finishing_wip;$po_finishing_wip+=$finishing_wip; $buyer_finishing_wip+=$finishing_wip; $gt_finishing_wip+=$finishing_wip;?></td>
		                                        
		                                        
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"],0); $job_ex_fact_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"];$po_ex_fact_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"];  $buyer_ex_fact_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"]; $gt_ex_fact_prev_qnty+=$production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"]; ?></td>
		                                        <td width="70" align="right"><? echo number_format($production_data[$order_id][$item_id][$color_id]["ex_fact_qnty"],0); $job_ex_fact_qnty+=$production_data[$order_id][$item_id][$color_id]["ex_fact_qnty"];$po_ex_fact_qnty+=$production_data[$order_id][$item_id][$color_id]["ex_fact_qnty"]; $buyer_ex_fact_qnty+=$production_data[$order_id][$item_id][$color_id]["ex_fact_qnty"]; $gt_ex_fact_qnty+=$production_data[$order_id][$item_id][$color_id]["ex_fact_qnty"];?></td>
		                                        
		                                        <td width="70" align="right"><? $tot_ex_fact_qnty=$production_data[$order_id][$item_id][$color_id]["ex_fact_prev_qnty"]+ $production_data[$order_id][$item_id][$color_id]["ex_fact_qnty"]; echo number_format($tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty+=$tot_ex_fact_qnty;$po_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $buyer_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $gt_tot_ex_fact_qnty+=$tot_ex_fact_qnty;?></td>
		                                        
		                                         <td width="70" align="right"><? $ex_fact_fob=$production_data[$order_id][$item_id][$color_id]["ex_fact_qnty_fob"]; echo number_format($ex_fact_fob,0); $job_ex_fact_fob+=$ex_fact_fob;$po_ex_fact_fob+=$ex_fact_fob; $buyer_ex_fact_fob+=$ex_fact_fob; $gt_ex_fact_fob+=$ex_fact_fob;?></td>
		                                        
		                                        <td width="70" align="right"><? $ex_fact_wip=($tot_paking_finish_qnty-$tot_ex_fact_qnty); echo number_format($ex_fact_wip,0); $job_ex_fact_wip+=$ex_fact_wip;$po_ex_fact_wip+=$ex_fact_wip; $buyer_ex_fact_wip+=$ex_fact_wip; $gt_ex_fact_wip+=$ex_fact_wip; ?></td>
		                                        
		                                        <td width="70" align="right"><? $ex_fact_wip_fob=$ex_fact_wip*($job_array[$order_id]['unit_price']/$job_array[$order_id]['total_set_qnty']); echo number_format($ex_fact_wip_fob,0); $job_ex_fact_wip_fob+=$ex_fact_wip_fob;$po_ex_fact_wip_fob+=$ex_fact_wip_fob; $buyer_ex_fact_wip_fob+=$ex_fact_wip_fob; $gt_ex_fact_wip_fob+=$ex_fact_wip_fob;?></td>
		                                        
		                                    </tr>
		                                    <?
		                                    $i++;
										//}
									}
								//}
							}
							//order wise subtotal
							?>
			                  <tr bgcolor="#F4F3C4">
			                    <td align="right" colspan="10" style="font-weight:bold;">PO Total:</td>
			                    <td width="70" align="right"><? echo number_format($po_order_qnty,0); $po_order_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_lay_prev_qnty,0); $po_lay_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_lay_qnty,0); $po_lay_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_lay_qnty,0); $po_tot_lay_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_cutting_prev_qnty,0); $po_cutting_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_cutting_qnty,0); $po_cutting_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_cutting_qnty,0); $po_tot_cutting_qnty=0;?></td>
		                        
		                        
			                    <?php /*?><td width="70" align="right"><? echo number_format($po_cutting_reject_qty,0); $po_cutting_reject_qty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_total_cutting_reject,0); $po_total_cutting_reject=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_cut_qc_wip,0); $po_cut_qc_wip=0;  ?></td>
			                    <td width="70" align="right"><? echo number_format($po_printing_prev_qnty,0); $po_printing_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_printing_qnty,0); $po_printing_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_printing_qnty,0); $po_tot_printing_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_printing_rcv_prev_qnty,0); $po_printing_rcv_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_printing_rcv_qnty,0); $po_printing_rcv_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_printing_rcv_qnty,0); $po_tot_printing_rcv_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_printing_reject_qty,0); $po_printing_reject_qty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_total_printing_reject,0); $po_total_printing_reject=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_printing_wip,0); $po_printing_wip=0;  ?></td><?php */?>
			                    
		                        <td width="70" align="right"><? echo number_format($po_embroidery_rcv_qnty,0); $po_embroidery_rcv_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_embroidery_rcv_qnty,0); $po_tot_embroidery_rcv_qnty=0;?></td>
		                        
		                        <?php /*?><td width="70" align="right"><? echo number_format($po_embroidery_prev_qnty,0); $po_embroidery_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_embroidery_qnty,0); $po_embroidery_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_embroidery_qnty,0); $po_tot_embroidery_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_embroidery_rcv_prev_qnty,0); $po_embroidery_rcv_prev_qnty=0; ?></td><?php */?>
			                   
			                   
		                       
		                        <?php /*?><td width="70" align="right"><? echo number_format($po_embroidery_reject_qty,0); $po_embroidery_reject_qty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_total_embroidery_reject,0); $po_total_embroidery_reject=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_emb_wip,0); $po_emb_wip=0;  ?></td>
			                    <td width="70" align="right"><? echo number_format($po_wash_prev_qnty,0); $po_wash_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_wash_qnty,0); $po_wash_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_wash_qnty,0); $po_tot_wash_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_wash_rcv_prev_qnty,0); $po_wash_rcv_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_wash_rcv_qnty,0); $po_wash_rcv_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_wash_rcv_qnty,0); $po_tot_wash_rcv_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_wash_reject_qty,0); $po_wash_reject_qty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_total_wash_reject,0); $po_total_wash_reject=0;?></td>
			                    <td width="70" align="right"><?  echo number_format($po_wash_wip,0); $po_wash_wip=0;  ?></td>
			                    <td width="70" align="right"><? echo number_format($po_sp_work_prev_qnty,0); $po_sp_work_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_sp_work_qnty,0); $po_sp_work_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_sp_work_qnty,0); $po_tot_sp_work_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_sp_work_rcv_prev_qnty,0); $po_sp_work_rcv_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_sp_work_rcv_qnty,0); $po_sp_work_rcv_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_sp_work_rcv_qnty,0); $po_tot_sp_work_rcv_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_sp_work_reject_qty,0); $po_sp_work_reject_qty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_total_sp_work_reject,0); $po_total_sp_work_reject=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_sp_work_wip,0); $po_sp_work_wip=0;?></td><?php */?>
			                    
		                        
		                        <td width="70" align="right"><? echo number_format($po_sewing_in_prev_qnty,0); $po_sewing_in_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_sewing_in_qnty,0); $po_sewing_in_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_sewing_in_qnty,0); $po_tot_sewing_in_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_sewing_out_prev_qnty,0); $po_sewing_out_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_sewing_out_qnty,0); $po_sewing_out_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_sewing_out_qnty,0); $po_tot_sewing_out_qnty=0;?></td>
			                    <?php /*?><td width="70" align="right"><? echo number_format($po_sewing_reject_qty,0); $po_sewing_reject_qty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_total_sewing_reject,0); $po_total_sewing_reject=0;?></td><?php */?>
			                    <td width="70" align="right"><? echo number_format($po_sewing_wip,0); $po_sewing_wip=0;?></td>
			                    <?php /*?><td width="70" align="right"><? echo number_format($po_poly_prev_qnty,0); $po_poly_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_poly_qnty,0); $po_poly_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_poly_qnty,0); $po_tot_poly_qnty=0;?></td><?php */?>
			                    <?php /*?><td width="70" align="right"><? echo number_format($po_poly_reject_qty,0); $po_poly_reject_qty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_total_poly_reject,0); $po_total_poly_reject=0;?></td><?php */?>
			                    <?php /*?><td width="70" align="right"><? echo number_format($po_poly_wip,0); $po_poly_wip=0;?></td><?php */?>
			                    <td width="70" align="right"><? echo number_format($po_paking_finish_prev_qnty,0); $po_paking_finish_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_paking_finish_qnty,0); $po_paking_finish_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_paking_finish_qnty,0); $po_tot_paking_finish_qnty=0;?></td>
		                        
		  						<td width="70" align="right"><? echo number_format($po_carton_qnty,0); $po_carton_qnty=0;?></td>
		                        
			                    <?php /*?><td width="70" align="right"><? echo number_format($po_paking_finish_reject_qty,0); $po_paking_finish_reject_qty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_total_finish_reject,0); $po_total_finish_reject=0;?></td><?php */?>
			                    <td width="70" align="right"><? echo number_format($po_finishing_wip,0); $po_finishing_wip=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_ex_fact_prev_qnty,0); $po_ex_fact_prev_qnty=0; ?></td>
			                    <td width="70" align="right"><? echo number_format($po_ex_fact_qnty,0); $po_ex_fact_qnty=0;?></td>
			                    <td width="70" align="right"><? echo number_format($po_tot_ex_fact_qnty,0); $po_tot_ex_fact_qnty=0;?></td>
		                        
		                        <td width="70" align="right"><? echo number_format($po_ex_fact_fob,0); $po_ex_fact_fob=0;?></td>
		                        
			                    <td width="70" align="right"><? echo number_format($po_ex_fact_wip,0); $po_ex_fact_wip=0;?></td>
		                        
		                        <td width="70" align="right"><? echo number_format($po_ex_fact_wip_fob,0); $po_ex_fact_wip_fob=0;?></td>

			                </tr>
		                    
		                    <?
						}
						?>
		                <tr bgcolor="#F4F3C4">
		                    <td align="right" colspan="10" style="font-weight:bold;">Job Total:</td>
		                    <td width="70" align="right"><? echo number_format($job_order_qnty,0); $job_order_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_lay_prev_qnty,0); $job_lay_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_lay_qnty,0); $job_lay_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_lay_qnty,0); $job_tot_lay_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_cutting_prev_qnty,0); $job_cutting_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_cutting_qnty,0); $job_cutting_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_cutting_qnty,0); $job_tot_cutting_qnty=0;?></td>
		                    
		                    
		                   <?php /*?> <td width="70" align="right"><? echo number_format($job_cutting_reject_qty,0); $job_cutting_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_cutting_reject,0); $job_total_cutting_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_cut_qc_wip,0); $job_cut_qc_wip=0;  ?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_prev_qnty,0); $job_printing_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_qnty,0); $job_printing_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_printing_qnty,0); $job_tot_printing_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_rcv_prev_qnty,0); $job_printing_rcv_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_rcv_qnty,0); $job_printing_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_printing_rcv_qnty,0); $job_tot_printing_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_reject_qty,0); $job_printing_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_printing_reject,0); $job_total_printing_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_printing_wip,0); $job_printing_wip=0;  ?></td><?php */?>
		                    
		                   <td width="70" align="right"><? echo number_format($job_embroidery_rcv_qnty,0); $job_embroidery_rcv_qnty=0;?></td>
		                   <td width="70" align="right"><? echo number_format($job_tot_embroidery_rcv_qnty,0); $job_tot_embroidery_rcv_qnty=0;?></td>
		                    
		                  <?php /*?>  <td width="70" align="right"><? echo number_format($job_embroidery_prev_qnty,0); $job_embroidery_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_embroidery_qnty,0); $job_embroidery_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_embroidery_qnty,0); $job_tot_embroidery_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_embroidery_rcv_prev_qnty,0); $job_embroidery_rcv_prev_qnty=0; ?></td><?php */?>
		                    
		                   
		                   
		                    <?php /*?><td width="70" align="right"><? echo number_format($job_embroidery_reject_qty,0); $job_embroidery_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_embroidery_reject,0); $job_total_embroidery_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_emb_wip,0); $job_emb_wip=0;  ?></td>
		                    <td width="70" align="right"><? echo number_format($job_wash_prev_qnty,0); $job_wash_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_wash_qnty,0); $job_wash_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_wash_qnty,0); $job_tot_wash_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_wash_rcv_prev_qnty,0); $job_wash_rcv_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_wash_rcv_qnty,0); $job_wash_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_wash_rcv_qnty,0); $job_tot_wash_rcv_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_wash_reject_qty,0); $job_wash_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_wash_reject,0); $job_total_wash_reject=0;?></td>
		                    <td width="70" align="right"><?  echo number_format($job_wash_wip,0); $job_wash_wip=0;  ?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_prev_qnty,0); $job_sp_work_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_qnty,0); $job_sp_work_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_sp_work_qnty,0); $job_tot_sp_work_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_rcv_prev_qnty,0); $job_sp_work_rcv_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_rcv_qnty,0); $job_sp_work_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_sp_work_rcv_qnty,0); $job_tot_sp_work_rcv_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_reject_qty,0); $job_sp_work_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_sp_work_reject,0); $job_total_sp_work_reject=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sp_work_wip,0); $job_sp_work_wip=0;?></td><?php */?>
		                   
		                    <td width="70" align="right"><? echo number_format($job_sewing_in_prev_qnty,0); $job_sewing_in_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_sewing_in_qnty,0); $job_sewing_in_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_sewing_out_prev_qnty,0); $job_sewing_out_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_sewing_out_qnty,0); $job_sewing_out_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty=0;?></td>
		                   <?php /*?> <td width="70" align="right"><? echo number_format($job_sewing_reject_qty,0); $job_sewing_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_sewing_reject,0); $job_total_sewing_reject=0;?></td><?php */?>
		                    <td width="70" align="right"><? echo number_format($job_sewing_wip,0); $job_sewing_wip=0;?></td>
		                    <?php /*?><td width="70" align="right"><? echo number_format($job_poly_prev_qnty,0); $job_poly_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_poly_qnty,0); $job_poly_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_poly_qnty,0); $job_tot_poly_qnty=0;?></td><?php */?>
		                    <?php /*?><td width="70" align="right"><? echo number_format($job_poly_reject_qty,0); $job_poly_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_poly_reject,0); $job_total_poly_reject=0;?></td><?php */?>
		                    <?php /*?><td width="70" align="right"><? echo number_format($job_poly_wip,0); $job_poly_wip=0;?></td><?php */?>
		                    <td width="70" align="right"><? echo number_format($job_paking_finish_prev_qnty,0); $job_paking_finish_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_paking_finish_qnty,0); $job_paking_finish_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty=0;?></td>
		                    
		                    <td width="70" align="right"><? echo number_format($job_carton_qnty,0); $job_carton_qnty=0;?></td>

		                    
		                    <?php /*?><td width="70" align="right"><? echo number_format($job_paking_finish_reject_qty,0); $job_paking_finish_reject_qty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_total_finish_reject,0); $job_total_finish_reject=0;?></td><?php */?>
		                    <td width="70" align="right"><? echo number_format($job_finishing_wip,0); $job_finishing_wip=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_ex_fact_prev_qnty,0); $job_ex_fact_prev_qnty=0; ?></td>
		                    <td width="70" align="right"><? echo number_format($job_ex_fact_qnty,0); $job_ex_fact_qnty=0;?></td>
		                    <td width="70" align="right"><? echo number_format($job_tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty=0;?></td>
		                    
		                    <td width="70" align="right"><? echo number_format($job_ex_fact_fob,0); $job_ex_fact_fob=0;?></td>
		                    
		                    <td width="70" align="right"><? echo number_format($job_ex_fact_wip,0); $job_ex_fact_wip=0;?></td>
		                    
		                    <td width="70" align="right"><? echo number_format($job_ex_fact_wip_fob,0); $job_ex_fact_wip_fob=0;?></td>

		                </tr>
		                <?
					}
					?>
		            <tr bgcolor="#CCCCCC">
		                <td align="right" colspan="10" style="font-weight:bold;">Buyer Total:</td>
		                <td width="70" align="right"><? echo number_format($buyer_order_qnty,0); $buyer_order_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_lay_prev_qnty,0); $buyer_lay_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_lay_qnty,0);  $buyer_lay_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_lay_qnty,0); $buyer_tot_lay_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_cutting_prev_qnty,0); $buyer_cutting_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_cutting_qnty,0); $buyer_cutting_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_cutting_qnty,0); $buyer_tot_cutting_qnty=0;?></td>
		                
		                <?php /*?><td width="70" align="right"><? echo number_format($buyer_cutting_reject_qty,0);  $buyer_cutting_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_cutting_reject,0); $buyer_total_cutting_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format( $job_cut_qc_wip,0); $job_cut_qc_wip=0;  ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_prev_qnty,0); $ $buyer_printing_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_qnty,0); $buyer_printing_qnty=0;?></td>
		                <td width="70" align="right"><?  echo number_format($buyer_tot_printing_qnty,0); $buyer_tot_printing_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_rcv_prev_qnty,0); $buyer_printing_rcv_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_rcv_qnty,0);  $buyer_printing_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_printing_rcv_qnty,0); $buyer_tot_printing_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_reject_qty,0);  $buyer_printing_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_printing_reject,0); $buyer_total_printing_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_printing_wip,0); $buyer_printing_wip=0;  ?></td><?php */?>
		               
		                
		                <td width="70" align="right"><? echo number_format($buyer_embroidery_rcv_qnty,0); $buyer_embroidery_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_embroidery_rcv_qnty,0);  $buyer_tot_embroidery_rcv_qnty=0;?></td>
		               
		                <?php /*?><td width="70" align="right"><? echo number_format($buyer_embroidery_prev_qnty,0);  $buyer_embroidery_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_embroidery_qnty,0);  $buyer_embroidery_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_embroidery_qnty,0); $buyer_tot_embroidery_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_embroidery_rcv_prev_qnty,0); $buyer_embroidery_rcv_prev_qnty=0; ?></td><?php */?>
		                
		               
		                <?php /*?><td width="70" align="right"><? echo number_format($buyer_embroidery_reject_qty,0); $buyer_embroidery_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_embroidery_reject,0); $buyer_total_embroidery_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_emb_wip,0); $buyer_emb_wip=0;  ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_prev_qnty,0); $buyer_wash_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_qnty,0);  $buyer_wash_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_wash_qnty,0); $buyer_tot_wash_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_rcv_prev_qnty,0); $buyer_wash_rcv_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_rcv_qnty,0);  $buyer_wash_rcv_qnty=0;?></td>
		                <td width="70" align="right"><?  echo number_format($buyer_tot_wash_rcv_qnty,0); $buyer_tot_wash_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_reject_qty,0); $buyer_wash_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_wash_reject,0); $buyer_total_wash_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_wash_wip,0);  $buyer_wash_wip=0;  ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sp_work_prev_qnty,0); $buyer_sp_work_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sp_work_qnty,0);  $buyer_sp_work_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_sp_work_qnty,0);  $buyer_tot_sp_work_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sp_work_rcv_prev_qnty,0);  $buyer_sp_work_rcv_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sp_work_rcv_qnty,0);  $buyer_sp_work_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_sp_work_rcv_qnty,0); $buyer_tot_sp_work_rcv_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sp_work_reject_qty,0); $buyer_sp_work_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_sp_work_reject,0); $buyer_total_sp_work_reject=0;?></td>
		                <td width="70" align="right"><? echo number_format( $buyer_sp_work_wip,0);  $buyer_sp_work_wip=0;?></td><?php */?>
		                
		                <td width="70" align="right"><? echo number_format($buyer_sewing_in_prev_qnty,0); $buyer_sewing_in_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sewing_in_qnty,0);  $buyer_sewing_in_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_sewing_in_qnty,0);  $buyer_tot_sewing_in_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sewing_out_prev_qnty,0); $buyer_sewing_out_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_sewing_out_qnty,0);  $buyer_sewing_out_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format( $buyer_tot_sewing_out_qnty,0); $buyer_tot_sewing_out_qnty=0;?></td>
		               <?php /*?> <td width="70" align="right"><? echo number_format($buyer_sewing_reject_qty,0); $buyer_sewing_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_sewing_reject,0); $buyer_total_sewing_reject=0;?></td><?php */?>
		                <td width="70" align="right"><? echo number_format($buyer_sewing_wip,0); $buyer_sewing_wip=0;?></td>
		                <?php /*?><td width="70" align="right"><? echo number_format($buyer_poly_prev_qnt,0); $buyer_poly_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_poly_qnty,0); $buyer_poly_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_poly_qnty,0); $buyer_tot_poly_qnty=0;?></td><?php */?>
		                <?php /*?><td width="70" align="right"><? echo number_format($buyer_poly_reject_qty,0);  $buyer_poly_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_poly_reject,0); $buyer_total_poly_reject=0;?></td><?php */?>
		                <?php /*?><td width="70" align="right"><? echo number_format($buyer_poly_wip,0); $buyer_poly_wip=0;?></td><?php */?>
		                <td width="70" align="right"><? echo number_format($buyer_paking_finish_prev_qnty,0);  $buyer_paking_finish_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_paking_finish_qnty,0);  $buyer_paking_finish_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_paking_finish_qnty,0); $buyer_tot_paking_finish_qnty=0;?></td>
		                
		                <td width="70" align="right"><? echo number_format($buyer_carton_qnty,0); $buyer_carton_qnty=0;?></td>

		                
		               <?php /*?> <td width="70" align="right"><? echo number_format($buyer_paking_finish_reject_qty,0); $buyer_paking_finish_reject_qty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_total_finish_reject,0); $buyer_total_finish_reject=0;?></td><?php */?>
		                <td width="70" align="right"><? echo number_format($buyer_finishing_wip,0);  $buyer_finishing_wip=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_ex_fact_prev_qnty,0); $buyer_ex_fact_prev_qnty=0; ?></td>
		                <td width="70" align="right"><? echo number_format($buyer_ex_fact_qnty,0); $buyer_ex_fact_qnty=0;?></td>
		                <td width="70" align="right"><? echo number_format($buyer_tot_ex_fact_qnty,0); $buyer_tot_ex_fact_qnty=0;?></td>
		                
		                <td width="70" align="right"><? echo number_format($buyer_ex_fact_fob,0); $buyer_ex_fact_fob=0;?></td>
		                
		                <td width="70" align="right"><? echo number_format($buyer_ex_fact_wip,0); $buyer_ex_fact_wip=0;?></td>
		                
		                <td width="70" align="right"><? echo number_format($buyer_ex_fact_wip_fob,0); $buyer_ex_fact_wip_fob=0;?></td>

		            </tr>
		            <?
				}
				
		        
		        ?>
		        </tbody>
		    </table>
		    </div> 
		    <table width="3110" id="report_table_footer" border="1" class="rpt_table" rules="all" align="left" style="margin-left:2px;">
		        <tfoot>
		        	<tr>
			            <th width="40" align="right" style="font-weight:bold; font-size:16px;"></th>
			            <th width="100" align="right" style="font-weight:bold; font-size:16px;"></th>
			            <th width="100" align="right" style="font-weight:bold; font-size:16px;"></th>
			            <th width="60" align="right" style="font-weight:bold; font-size:16px;"></th>
			            <th width="50" align="right" style="font-weight:bold; font-size:16px;"></th>
			            <th width="100" align="right" style="font-weight:bold; font-size:16px;"></th>
			            <th width="100" align="right" style="font-weight:bold; font-size:16px;"></th>
			            <th width="70" align="right" style="font-weight:bold; font-size:16px;"></th>
			            <th width="100" align="right" style="font-weight:bold; font-size:16px;"></th>
		    
		                <th width="100" align="right" style="font-weight:bold; font-size:16px;">Grand Total</th>
		                <th width="70" id="grndTotID_gt_order_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_order_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_lay_prev_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_lay_prev_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_lay_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_lay_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_tot_lay_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_lay_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_cutting_prev_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_cutting_prev_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_cutting_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_cutting_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_tot_cutting_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_cutting_qnty,0);?></th>
		              
		                <?php /*?><th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_cutting_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_cutting_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_cut_qc_wip,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_printing_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_rcv_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_printing_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_printing_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_printing_wip,0);?></th><?php */?>
		                
		                <th width="70" id="grndTotID_gt_embroidery_rcv_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_embroidery_rcv_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_tot_embroidery_rcv_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_embroidery_rcv_qnty,0);?></th>
		               
		                <?php /*?><th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_embroidery_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_embroidery_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_embroidery_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_embroidery_rcv_prev_qnty,0);?></th><?php */?>
		                
		                
		                <?php /*?><th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_embroidery_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_embroidery_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_emb_wip,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_wash_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_rcv_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_wash_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_wash_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_wash_wip,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_sp_work_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_rcv_prev_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_sp_work_rcv_qnty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_sp_work_reject,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sp_work_wip,0); ?></th><?php */?>
		                
		                <th width="70" id="grndTotID_gt_sewing_in_prev_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_in_prev_qnty,0); ?></th>
		                <th width="70" id="grndTotID_gt_sewing_in_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_in_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_tot_sewing_in_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_sewing_in_qnty,0); ?></th>
		                <th width="70" id="grndTotID_gt_sewing_out_prev_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_out_prev_qnty,0); ?></th>
		                <th width="70" id="grndTotID_gt_sewing_out_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_out_qnty,0); ?></th>
		                <th width="70" id="grndTotID_gt_tot_sewing_out_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_sewing_out_qnty,0); ?></th>
		                <?php /*?><th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_sewing_reject,0);?></th><?php */?>
		                <th width="70" id="grndTotID_gt_sewing_wip" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_sewing_wip,0);?></th>
		                <?php /*?><th width="70" id="grndTotID_gt_poly_prev_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_poly_prev_qnty,0); ?></th>
		                <th width="70" id="grndTotID_gt_poly_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_poly_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_tot_poly_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_poly_qnty,0); ?></th><?php */?>
		                <?php /*?><th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_poly_reject_qty,0); ?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_poly_reject,0);?></th><?php */?>
		                <?php /*?><th width="70" id="grndTotID_gt_poly_wip" align="right" style="font-weight:bold; font-size:16px;"><?  echo number_format($gt_poly_wip,0);?></th><?php */?>
		                <th width="70" id="grndTotID_gt_paking_finish_prev_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_paking_finish_prev_qnty,0);  ?></th>
		                <th width="70" id="grndTotID_gt_paking_finish_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_paking_finish_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_tot_paking_finish_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_paking_finish_qnty,0); ?></th>
		                
		                <th width="70" id="grndTotID_gt_carton_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_carton_qnty,0);?></th>

		                
		                <?php /*?><th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_paking_finish_reject_qty,0);?></th>
		                <th width="70" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_total_finish_reject,0);?></th><?php */?>
		                <th width="70" id="grndTotID_gt_finishing_wip" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_finishing_wip,0);?></th>
		                <th width="70" id="grndTotID_gt_ex_fact_prev_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_ex_fact_prev_qnty,0); ?></th>
		                <th width="70" id="grndTotID_gt_ex_fact_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_ex_fact_qnty,0);?></th>
		                <th width="70" id="grndTotID_gt_tot_ex_fact_qnty" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_tot_ex_fact_qnty,0); ?></th>
		                
		                
		                <th width="70" id="grndTotID_gt_ex_fact_fob" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_ex_fact_fob,0);?></th>
		                
		                <th width="70" id="grndTotID_gt_ex_fact_wip" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_ex_fact_wip,0);?></th>
		                
		                <th width="70" id="grndTotID_gt_ex_fact_wip_fob" align="right" style="font-weight:bold; font-size:16px;"><? echo number_format($gt_ex_fact_wip_fob,0);?></th>
		            </tr>    
		        </tfoot>
		 
		    </table> 
		    
		     </fieldset>  
		  </div>     
		  </fieldset>
		  <?
	}
	else if($type==4)
	{
	 	// button 4 for chaity			
			$str_po_cond="";
			//if($cbo_work_company_name!=0) $str_po_cond.=" and d.serving_company in($cbo_work_company_name)";		
			if($cbo_work_company_name!=0) $company_cond=" and d.serving_company in($cbo_work_company_name)";		

			if(str_replace("'", "",$cbo_buyer_name)!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";
			$cbo_location_name=str_replace("'", "", $cbo_location_name);
			if($cbo_location_name) $str_po_cond.=" and d.location in($cbo_location_name)";
			$cbo_floor_name=str_replace("'", "", $cbo_floor_name);
			if($cbo_floor_name) $str_po_cond.=" and d.floor_id in($cbo_floor_name)";
			// if($txt_production_date != "") $str_po_cond.=" and d.production_date=$txt_production_date";
			if(str_replace("'", "", $cbo_year))
			{
				if($db_type==0)
				{
					$str_po_cond .=" and year(a.insert_date)=$cbo_year";
				}
				else
				{
					$str_po_cond .=" and to_char(a.insert_date,'YYYY')=$cbo_year";
				}	
			}
			
			$hidden_job_id=str_replace("'","",$hidden_job_id);
			if($hidden_job_id!="")
			{
				$str_po_cond.=" and a.id in($hidden_job_id)";
			}

			$hidden_order_id=str_replace("'","",$hidden_order_id);
			if($hidden_order_id)
			{
				$str_po_cond.=" and b.id in($hidden_order_id)";
			} 
			$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
			$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);

			$sql_order_col="SELECT c.color_number_id,b.id as po_id  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond $company_cond $file_no $ref_no and d.production_date=$txt_production_date and d.production_type in(1,4,5,8) group by c.color_number_id,b.id " ;
			$po_id_array=array();
			$col_id_array=array();
			foreach(sql_select($sql_order_col) as $row) 
			{
				$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
				$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
			}
			$po_ids=implode(",", $po_id_array);
			$color_ids=implode(",", $col_id_array);
			
			
			if(!$po_ids) $po_ids=0;
			if(!$color_ids) $color_ids=0;



	 
			$order_sql_for_po="SELECT c.id as col_size_id,a.style_ref_no,a.job_no,b.id as po_id,d.item_number_id,c.color_number_id,e.cut_no as cutting_no,b.po_number,d.serving_company,d.location,a.buyer_name,c.order_quantity, sum(case when d.production_type=1 then e.production_qnty else 0 end ) as cutting_qnty,sum(case when d.production_type=4 then e.production_qnty else 0 end ) as sewing_qnty,b.file_no,b.grouping from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond $company_cond $file_no $ref_no and c.color_number_id in($color_ids) and b.id in($po_ids) and d.production_type in(1,4,5,8)   group by c.id,a.style_ref_no,a.job_no,b.id ,d.item_number_id,c.color_number_id,e.cut_no,b.po_number,d.serving_company,d.location,a.buyer_name,c.order_quantity,b.file_no,b.grouping order by c.id " ;
			//echo $order_sql_for_po;die;
			 $order_qnty_array=array();
			 $order_qnty_sqls="SELECT po_break_down_id,color_number_id,order_quantity,item_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in ($po_ids)";
			 foreach(sql_select($order_qnty_sqls) as $values)
			 {
			 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]+=$values[csf("order_quantity")];

			 }
			foreach(sql_select($order_sql_for_po)  as $row)
			{
				//if($row[csf("cutting_qnty")]>0 || $row[csf("sewing_qnty")]>0 )
				//{
					
				


					$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["po_number"]=$row[csf("po_number")];

						$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["working_company_id"]=$row[csf("serving_company")];

						$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["location_id"]=$row[csf("location_id")];						 

						$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["buyer_name"]=$row[csf("buyer_name")];	

						$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["file_no"]=$row[csf("file_no")];	

						$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["grouping"]=$row[csf("grouping")];	 

						 

						 
					//}	 

				
			}
			//echo "<pre>";print_r($production_main_array);die;
			

			 $order_sql=sql_select("SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no  ,   c.color_number_id, sum(c.order_quantity) as order_quantity, 

			sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_cutting ,
			sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

			sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_input ,
			sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input  



			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond and b.id in($po_ids) and c.color_number_id in($color_ids) and d.production_type in(1,4,5,8) 
			group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  ,  c.color_number_id,e.cut_no ");

			 //echo $order_sql;die;

			 foreach($order_sql as $vals)
			 {
			 	$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["today_cutting"]+=$vals[csf("today_cutting")];



			 	$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["total_cutting"]+=$vals[csf("total_cutting")];



			 	$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

			 	$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

			 }



			  
			 $order_sql_lay=sql_select("SELECT d.cutting_no,e.order_cut_no, e.gmt_item_id , e.color_id
			from ppl_cut_lay_mst d,ppl_cut_lay_dtls e
			where  d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0 and e.status_active=1   and e.is_deleted=0   and e.color_id in($color_ids) ");
	 		$order_cut_array=array();
			$po_col_size_qnty_array=array();
			foreach($order_sql_lay as $row )
			{				

					$order_cut_array[$row[csf("cutting_no")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]]=	$row[csf("order_cut_no")];	 				
				
			}
			//$all_po_lay_id=implode(",", $all_po_lay_id_array);
			/*if( count($all_po_lay_id)>0 )
			{
				$po_conds=" and a.po_break_down_id in($all_po_lay_id)";
			}*/

			$po_conds=" and a.po_break_down_id in($po_ids)";

			$booking_no_fin_qnty_array=array();
			$booking_sql=sql_select("SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0   $po_conds");
			foreach($booking_sql as $vals)
			{
				$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["booking_no"]=$vals[csf("booking_no")];
				$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];

			}
			$batch_sql=sql_select("SELECT id,booking_no,batch_no,color_id from pro_batch_create_mst where status_active=1 and is_deleted=0 and booking_no is not null group by id,booking_no,batch_no,color_id ");
			foreach($batch_sql as $rows)
			{
				//$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
				if(!$duplicate_batch[$rows[csf("batch_no")]])
				{
					if($batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=="")
					{
						$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
					}
					else
					{
						$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]].=','.$rows[csf("batch_no")];

					}
					
					$duplicate_batch[$rows[csf("batch_no")]]=trim($rows[csf("batch_no")]);
				}
				$batch_mst_id_arr[$rows[csf("booking_no")]]=$rows[csf("id")];
			}
			/*echo "<pre>";
			 print_r($batch_mst_id_arr);die;*/
			 
			 $issue_sql=sql_select("SELECT batch_id,issue_qnty,order_id from inv_finish_fabric_issue_dtls where status_active=1 and is_deleted=0 ");
			 foreach($issue_sql as $values)
			 {
			 	//$issue_qnty_arr[$values[csf("batch_id")]]+=$values[csf("issue_qnty")];
			 	$issue_qnty_arr[$values[csf("order_id")]]+=$values[csf("issue_qnty")];
			 }



			foreach($production_main_array as $style_id=>$job_data)
			{
				foreach($job_data as $job_id=>$po_data)
				{
					foreach($po_data as $po_id=>$item_data)
					{
						foreach($item_data as $item_id=>$color_data)
						{
							foreach($color_data as $color_id=>$cutting_data)
							{
								$color_span=0;
								$cut=0;
								$sew=0;
								foreach($cutting_data as $cutting_id=>$row)
								{
									$cut+=$cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["total_cutting"];
	 								$sew+=$cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["total_sewing_input"];
									$color_span++;
								}
								$style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]=$color_span;
								$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["cut"]=$cut;
								$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["sew"]=$sew;
	 
							}

						}
					}

				}

			}

			$result_consumtion=array();
			$sql_consumtiont_qty=sql_select(" SELECT a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id in($po_ids)  GROUP BY a.job_no, a.body_part_id");

			foreach($sql_consumtiont_qty as $row_consum)
			{

				$result_consumtion[$row_consum[csf('job_no')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
			}
			unset($sql_consumtiont_qty); 


			 
			
			if(count($production_main_array)==0)
			{
				echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px">No Data Found.</div>'; die;
			} 
			ob_start();	
			
			?>
			 
	        <div>
	        	<table width="2170" cellspacing="0" >
	        		<tr class="form_caption" style="border:none;">
	        			<td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
	        				Daily RMG Production Report
	        			</strong></td>
	        		</tr>
	        		<tr style="border:none;">
	        			<td colspan="24" align="center" style="border:none; font-size:14px;">
	        				<strong>
	        					Working Company Name : <? 
	        					$comp_names=""; 
	        					foreach(explode(",",$cbo_work_company_name) as $vals) 
	        					{
	        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
	        					}
	        					echo $comp_names;
	        					 ?>
	        				</strong>                                
	        			</td>
	        		</tr>
	        		<tr style="border:none;">
	        			<td colspan="24" align="center" style="border:none;font-size:12px; font-weight:bold" >
	        				<?
	        				$dates=str_replace("'","",trim($txt_production_date));
	        				if($dates)
	        				{
	        					echo "Date ".change_date_format($dates)  ;
	        				}
	        				?>
	        			</td>
	        		</tr>
	        	</table>
				<div>
					<table width="2170" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
						<thead>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Working Company</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Location</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer Name</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Job No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Style Reff</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>File No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Internal Ref</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>F.Booking No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Batch No</p></th>	

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="5" width="400"><p>Fabric Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="5" width="400"><p>Cutting Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="320"><p>Input Status</p></th>
						</tr>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Color Name</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>F.Fab. Req.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Fin. Fab. Issued</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>F.Issued Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Possible Cut Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>System Cut No.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order Cut No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Cut.Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order-Input Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Inhand Qty</p></th>

						</tr>
							
							  
						   
						</thead>
					</table>
					<div style="max-height:400px; overflow-y:scroll; width:2190px" id="scroll_body">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2170" rules="all" id="table_body" >
						<?
						$k=1;
						$jj=1;	
						$gr_order_qnty=0;
						$gr_req=0;
						$gr_iss=0;
						$gr_iss_bal=0;
						$gr_pos_cut=0;
						$gr_today_cut=0;
						$gr_cut_bal=0;
						$gr_total_cut=0;
						$gr_today_sewing=0;
						$gr_total_sewing=0;
						$gr_inh_bal=0;
						$gr_inh_qty=0;	
						
						foreach($production_main_array as $style_id=>$job_data)
						{				
							$style_wise_order_qty = 0;		
							$style_wise_fab_req = 0;		
							$style_wise_fab_issued = 0;		
							$style_wise_fab_issued_balance = 0;	

							$style_wise_fab_posible_cut_qty = 0;		
							$style_wise_cut_today = 0;		
							$style_wise_cut_total = 0;		
							$style_wise_cut_balance = 0;

							$style_wise_input_today = 0;		
							$style_wise_input_total = 0;		
							$style_wise_input_balance = 0;		
							$style_wise_inhand_qty = 0;	

							foreach($job_data as $job_id=>$po_data)
							{
								
								foreach($po_data as $po_id=>$item_data)
								{
									$order_wise_subtotal = 0;
									$order_wise_today_cutting=0;
									$order_wise_total_cutting=0;
									$order_wise_cut_balance=0;

									$order_wise_today_input=0;
									$order_wise_total_input=0;
									$order_wise_input_balance=0;
									$order_wise_inhand_qty=0;
									// fabric status sum
									$order_wise_fab_req = 0;
									$order_wise_fin_fab_req = 0;
									$order_wise_fab_issued_balance = 0;
									$order_wise_fab_possible_qty = 0;
									//

									foreach($item_data as $item_id=>$color_data)
									{
										foreach($color_data as $color_id=>$cutting_data)
										{
											$color_wise_today_cutting=0;
											$color_wise_total_cutting=0;
											$color_wise_today_sewing_input =0;
											$color_wise_total_sewing_input=0;
											$color_wise_inh=0;
											$pp=0;
											$fin_req = 0;
											foreach($cutting_data as $cutting_id=>$row)
											{
												
												 

												$fin_req=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
												//$issue_qty=$issue_qnty_arr[$batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]]];
												$issue_qty=$issue_qnty_arr[$po_id];
												$req_issue_bal=$fin_req-$issue_qty;
												$possible_cut_pcs=$issue_qty/$result_consumtion[$job_id];

												$today_cutting_qnty= $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_cutting"];
												$total_cutting_qnty= $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["total_cutting"];
												$color_wise_today_cutting+=$today_cutting_qnty;
												$color_wise_total_cutting+=$total_cutting_qnty;

												$today_sewing_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_sewing_input"];
												$total_sewing_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["total_sewing_input"];
												$color_wise_today_sewing_input += $today_sewing_input;
												$color_wise_total_sewing_input += $total_sewing_input;

												// order wise

												$order_wise_today_cutting+=$today_cutting_qnty;
												$order_wise_total_cutting+=$total_cutting_qnty;
												

												$order_wise_today_input += $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_sewing_input"];
												$order_wise_total_input += $total_sewing_input;

												

												$order_wise_inhand_qty += $total_cutting_qnty-$total_sewing_input;

												// style wise
												

												$style_wise_cut_today += $today_cutting_qnty;		
												$style_wise_cut_total += $total_cutting_qnty;		


												$style_wise_input_today += $today_input;		
												$style_wise_input_total += $total_sewing_input;		
														
												$style_wise_inhand_qty += $order_wise_inhand_qty;
												
											 
												if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
														<?
														$jj++;
														if($pp==0)
														{
															$order_quantitys=$order_qnty_array[$po_id][$item_id][$color_id];
															$order_wise_subtotal += $order_quantitys;
															$style_wise_order_qty += $order_quantitys;
															$gr_order_qnty+=$order_quantitys;
															$gr_req+=$fin_req;
															$gr_iss+=$issue_qty;
															$gr_iss_bal+=$req_issue_bal;
															$gr_pos_cut+=$possible_cut_pcs;

															?>
														 
															<td valign="middle" rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>

															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $company_library[$row["working_company_id"]]; ?></p></td>

															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $location_library[$row["location_id"]]; ?></p></td>

															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $buyer_library[$row["buyer_name"]]; ?></p></td>

															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $job_id; ?></p></td>

															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $style_id;?></p></td>
															 
															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["po_number"];?></p></td>

															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["file_no"];?></p></td>

															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["grouping"];?></p></td>

															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"  align="center"    width="80"><p><? echo $order_quantitys; ?></p></td> 
															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"] ;?></p></td>
															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $batch_mst_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]][$color_id];?></p></td>
															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $color_Arr_library[$color_id];?></p></td>


															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $fin_req;?></p></td>

															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><a href="##" onclick="openmypage_fab_issue(<? echo $po_id;?>,<? echo $color_id?>,<? echo 1 ?>, 'fab_issue_popup');" > <p><? echo $issue_qty;?></p></a></td>
															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $req_issue_bal;?></p></td>
															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo number_format($possible_cut_pcs,4);?></p></td>
														<?
														$order_wise_fab_req += $fin_req;
														$order_wise_fin_fab_req += $issue_qty;
														$order_wise_fab_issued_balance += $req_issue_bal;
														$order_wise_fab_possible_qty += $possible_cut_pcs;
														}
														//$pp++;
														$gr_today_cut+=$today_cutting_qnty;
														$gr_total_cut+=$total_cutting_qnty;
														$gr_today_sewing+=$cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_sewing_input"];
														$gr_total_sewing+=$total_sewing_input;
														?>
															<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $cutting_id;?></p></td>

															<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $order_cut_array[$cutting_id][$item_id][$color_id];?></p></td>

															<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $today_cutting_qnty;?></p></td>
	 
															<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'<? echo  $cutting_id;?>',<? echo $color_id;?>,'1', 'cutting_sewing_action');" > <p><? echo $total_cutting_qnty;?></p></a></td>
															<?
															if($pp==0)
															{
																?>
																<td valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]+1; ?>"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $cut_balance = $order_quantitys-$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["cut"];?></p></td>
																<?
																$gr_cut_bal+=$cut_balance;
																$order_wise_cut_balance += $cut_balance;

																$style_wise_cut_balance += $cut_balance;
											 

															}
															?>
															<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $today_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_sewing_input"];?></p></td>

															<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'<? echo  $cutting_id;?>',<? echo $color_id;?>,'4', 'cutting_sewing_action');" ><p><? echo $total_sewing_input;?></a></p></td>
															<?
															if($pp==0)
															{


																?>
																<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]+1; ?>"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo 	$input_balance = $order_quantitys-$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["sew"];?></p></td>
															<?
															$gr_inh_bal+=$input_balance;
															$order_wise_input_balance += $input_balance;											 
															$style_wise_input_balance += $input_balance;
						 
															}
															$pp++;
															?>
															<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $inh_qty= $total_cutting_qnty-$total_sewing_input;$gr_inh_qty+=$inh_qty;$color_wise_inh+=$inh_qty;?></p></td>								 
														</tr>
												<?											
												
												
											}
											
											//$style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]=$color_span;
											?>
											<tr bgcolor="#E4E4E4">
												<td colspan="18" align="right"><b>Color Wise Sub Total</b></td>
												<td></td>
												<td align="right"><b><?php echo $color_wise_today_cutting;?></b></td>
												<td align="right"><b><?php echo $color_wise_total_cutting;?></b></td>
												
												<td align="right"><b><?php echo $color_wise_today_sewing_input;?></b></td>
												<td align="right"><b><?php echo $color_wise_total_sewing_input;?></b></td>
												<td align="right"><?php echo $color_wise_inh;?></td>
												 
											
											</tr>
											<?
											$style_wise_fab_req += $fin_req;		
											$style_wise_fab_issued += $issue_qty;		
											$style_wise_fab_issued_balance += $req_issue_bal;		
											$style_wise_fab_posible_cut_qty += $possible_cut_pcs;
										}

									}
									?>
									<tr bgcolor="#E4E4E4">
										<td colspan="9" align="right"><b>Order Wise Sub Total</b></td>
										<td align="right"><b><? echo $order_wise_subtotal;?></b></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="right"><b><? echo $order_wise_fab_req;?></b></td>
										<td align="right"><b><? echo $order_wise_fin_fab_req;?></b></td>
										<td align="right"><b><? echo $order_wise_fab_issued_balance;?></b></td>
										<td align="right"><b><? echo $order_wise_fab_possible_qty;?></b></td>
										<td></td>
										<td></td>
										<td align="right"><b><? echo $order_wise_today_cutting;?></b></td>
										<td align="right"><b><? echo $order_wise_total_cutting;?></b></td>
										<td align="right"><b><? echo $order_wise_cut_balance; ?></b></td>
										<td align="right"><b><? echo $order_wise_today_input;?></b></td>
										<td align="right"><b><? echo $order_wise_total_input;?></b></td>
										<td align="right"><b><? echo $order_wise_input_balance;?></b></td>
										<td align="right"><b><? echo $order_wise_inhand_qty;?></b></td>
									</tr>
									<?
								}
							
							}
							$k++;
							?>
							<tr bgcolor="#E4E4E4">
							<td colspan="9" align="right"><b>Style Wise Sub Total</b></td>
							<td align="right"><b><? echo $style_wise_order_qty;?></b></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><b><? echo $style_wise_fab_req;?></b></td>
							<td align="right"><b><? echo $style_wise_fab_issued;?></b></td>
							<td align="right"><b><? echo $style_wise_fab_issued_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_fab_posible_cut_qty;?></b></td>
							<td></td>
							<td></td>
							<td align="right"><b><? echo $style_wise_cut_today;?></b></td>
							<td align="right"><b><? echo $style_wise_cut_total;?></b></td>
							<td align="right"><b><? echo $style_wise_cut_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_input_today;?></b></td>
							<td align="right"><b><? echo $style_wise_input_total;?></b></td>
							<td align="right"><b><? echo $style_wise_input_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_inhand_qty;?></b></td>
						</tr>	
							<?
						}

						?>
						<tr bgcolor="#E4E4E4">  
							 
							<td style="word-wrap: break-word;word-break: break-all;" colspan="9" align="right"><strong>Grand Total</strong></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"    align="right"><b><? echo $gr_order_qnty;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo $gr_req;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo $gr_iss;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo $gr_iss_bal;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo $gr_pos_cut;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"  align="right"> </td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"  align="right"> </td>

							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_cut;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_cut;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_cut_bal;?></b></td>

							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_sewing;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_sewing;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_inh_bal;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_inh_qty;?></b></td>
												 
											
						</tr>	
											
						</table>					  
					</div>

					 

				</div>
			 </div> 
	        <?
		
	}
	else if($type==44)
	{

		$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
		$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	 	// button 44 for AKH			
		$str_po_cond="";
		//if($cbo_work_company_name!=0) $str_po_cond.=" and d.serving_company in($cbo_work_company_name)";		
		if($cbo_work_company_name!=0) $company_cond=" and d.serving_company in($cbo_work_company_name)";		

		if(str_replace("'", "",$cbo_buyer_name)!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";
		$cbo_location_name=str_replace("'", "", $cbo_location_name);
		if($cbo_location_name) $str_po_cond.=" and d.location in($cbo_location_name)";
		$cbo_floor_name=str_replace("'", "", $cbo_floor_name);
		if($cbo_floor_name) $str_po_cond.=" and d.floor_id in($cbo_floor_name)";
		// if($txt_production_date != "") $str_po_cond.=" and d.production_date=$txt_production_date";
		if(str_replace("'", "", $cbo_year))
		{
			if($db_type==0)
			{
				$str_po_cond .=" and year(a.insert_date)=$cbo_year";
			}
			else
			{
				$str_po_cond .=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			}	
		}
		
		$hidden_job_id=str_replace("'","",$hidden_job_id);
		if($hidden_job_id!="")
		{
			$str_po_cond.=" and a.id in($hidden_job_id)";
		}

		$hidden_order_id=str_replace("'","",$hidden_order_id);
		if($hidden_order_id)
		{
			$str_po_cond.=" and b.id in($hidden_order_id)";
		} 
		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);

		$sql_order_col="SELECT c.color_number_id,b.id as po_id  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond $company_cond $file_no $ref_no and d.production_date=$txt_production_date and d.production_type in(1,4,5,8) group by c.color_number_id,b.id " ;
		$po_id_array=array();
		$col_id_array=array();
		foreach(sql_select($sql_order_col) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		}
		$po_ids=implode(",", $po_id_array);
		$color_ids=implode(",", $col_id_array);
		
		
		if(!$po_ids) $po_ids=0;
		if(!$color_ids) $color_ids=0;



 
		$order_sql_for_po="SELECT c.id as col_size_id,a.style_ref_no,a.job_no,b.id as po_id,d.item_number_id,c.color_number_id,e.cut_no as cutting_no,b.po_number,d.serving_company,d.location,a.buyer_name,c.order_quantity, sum(case when d.production_type=1 then e.production_qnty else 0 end ) as cutting_qnty,sum(case when d.production_type=4 then e.production_qnty else 0 end ) as sewing_qnty,b.file_no,b.grouping,d.sewing_line,d.prod_reso_allo from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond $company_cond $file_no $ref_no and c.color_number_id in($color_ids) and b.id in($po_ids) and d.production_type in(1,4,5,8)   
		group by c.id,a.style_ref_no,a.job_no,b.id ,d.item_number_id,c.color_number_id,e.cut_no,b.po_number,d.serving_company,d.location,a.buyer_name,c.order_quantity,b.file_no,b.grouping,d.sewing_line,d.prod_reso_allo order by c.id " ;
		// echo $order_sql_for_po;die;
		 $order_qnty_array=array();
		 $order_qnty_sqls="SELECT po_break_down_id,color_number_id,order_quantity,item_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in ($po_ids)";
		 foreach(sql_select($order_qnty_sqls) as $values)
		 {
		 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]+=$values[csf("order_quantity")];

		 }

		$line_check_array = array();
		foreach(sql_select($order_sql_for_po)  as $row)
		{
			if($row[csf("prod_reso_allo")]==1)
        	{
        		$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}
        	}
        	else
        	{
        		$line_name=$lineArr[$row[csf('sewing_line')]];
        	}

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("serving_company")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];						 

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["file_no"]=$row[csf("file_no")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["grouping"]=$row[csf("grouping")];	
			if($line_check_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("sewing_line")]] != $row[csf("sewing_line")])
			{
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["sewing_line"] .= $line_name.",";	
			}
			$line_check_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("sewing_line")]] = $row[csf("sewing_line")];
			
		}
		unset($line_check_array);
		//echo "<pre>";print_r($production_main_array);die;
		

		 $order_sql=sql_select("SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id  ,   c.color_number_id, sum(c.order_quantity) as order_quantity, 

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input 

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond and b.id in($po_ids) and c.color_number_id in($color_ids) and d.production_type in(1,4,5,8) 
		group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  ,  c.color_number_id ");

		 //echo $order_sql;die;

		foreach($order_sql as $vals)
		{
		 	$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];

		 	$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];

		 	$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

		 	$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

		}
		// print_r($cutting_sewing_data)  ;die();
		$order_sql_lay=sql_select("SELECT d.cutting_no,e.order_cut_no, e.gmt_item_id , e.color_id
		from ppl_cut_lay_mst d,ppl_cut_lay_dtls e
		where  d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0 and e.status_active=1   and e.is_deleted=0   and e.color_id in($color_ids) ");
 		$order_cut_array=array();
		$po_col_size_qnty_array=array();
		foreach($order_sql_lay as $row )
		{				

			$order_cut_array[$row[csf("cutting_no")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]]=	$row[csf("order_cut_no")];	 				
		}
		//$all_po_lay_id=implode(",", $all_po_lay_id_array);
		/*if( count($all_po_lay_id)>0 )
		{
			$po_conds=" and a.po_break_down_id in($all_po_lay_id)";
		}*/

		$po_conds=" and a.po_break_down_id in($po_ids)";

		$booking_no_fin_qnty_array=array();
		$booking_sql=sql_select("SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0   $po_conds");
		foreach($booking_sql as $vals)
		{
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["booking_no"]=$vals[csf("booking_no")];
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];

		}
		$batch_sql=sql_select("SELECT id,booking_no,batch_no,color_id from pro_batch_create_mst where status_active=1 and is_deleted=0 and booking_no is not null group by id,booking_no,batch_no,color_id ");
		foreach($batch_sql as $rows)
		{
			//$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
			if(!$duplicate_batch[$rows[csf("batch_no")]])
			{
				if($batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=="")
				{
					$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
				}
				else
				{
					$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]].=','.$rows[csf("batch_no")];

				}
				
				$duplicate_batch[$rows[csf("batch_no")]]=trim($rows[csf("batch_no")]);
			}
			$batch_mst_id_arr[$rows[csf("booking_no")]]=$rows[csf("id")];
		}
		/*echo "<pre>";
		 print_r($batch_mst_id_arr);die;*/
		 
		 $issue_sql=sql_select("SELECT po_breakdown_id as order_id, color_id, sum( case when entry_form in(18,71) and trans_type=2 then quantity else 0 end)-sum( case when entry_form in(52) and trans_type=4 then quantity else 0 end) as quantity from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in (18,71,52) and po_breakdown_id in($po_ids)and color_id in($color_ids) and trans_type in(2,4) group by po_breakdown_id, color_id"); 
		 foreach($issue_sql as $values)
		 {
		 	//$issue_qnty_arr[$values[csf("batch_id")]]+=$values[csf("issue_qnty")];
		 	$issue_qnty_arr[$values[csf("order_id")]][$values[csf("color_id")]] += $values[csf("quantity")];
		 }



		foreach($production_main_array as $style_id=>$job_data)
		{
			foreach($job_data as $job_id=>$po_data)
			{
				foreach($po_data as $po_id=>$item_data)
				{
					foreach($item_data as $item_id=>$color_data)
					{
						foreach($color_data as $color_id=>$cutting_data)
						{
							$color_span=0;
							$cut=0;
							$sew=0;
							// foreach($cutting_data as $cutting_id=>$row)
							// {
								$cut=$cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["total_cutting"];
 								$sew=$cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_input"];
								$color_span++;
							// }
							$style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]=$color_span;
							$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["cut"]=$cut;
							$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["sew"]=$sew;
 
						}

					}
				}

			}

		}

		$result_consumtion=array();
		$sql_consumtiont_qty=sql_select(" SELECT a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id in($po_ids)  GROUP BY a.job_no, a.body_part_id");

		foreach($sql_consumtiont_qty as $row_consum)
		{

			$result_consumtion[$row_consum[csf('job_no')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
		}
		unset($sql_consumtiont_qty); 

		 
		
		if(count($production_main_array)==0)
		{
			echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px">No Data Found.</div>'; die;
		} 
		ob_start();	
		
		?>
			 <style type="text/css">
			 	table tr td,table tr th{ word-break: break-all;word-wrap: break-word; vertical-align: middle; }
			 </style>
	        <div>
	        	<table width="1900" cellspacing="0" >
	        		<tr class="form_caption" style="border:none;">
	        			<td colspan="23" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
	        				Daily RMG Production Report
	        			</strong></td>
	        		</tr>
	        		<tr style="border:none;">
	        			<td colspan="23" align="center" style="border:none; font-size:14px;">
	        				<strong>
	        					Working Company Name : <? 
	        					$comp_names=""; 
	        					foreach(explode(",",$cbo_work_company_name) as $vals) 
	        					{
	        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
	        					}
	        					echo $comp_names;
	        					 ?>
	        				</strong>                                
	        			</td>
	        		</tr>
	        		<tr style="border:none;">
	        			<td colspan="23" align="center" style="border:none;font-size:12px; font-weight:bold" >
	        				<?
	        				$dates=str_replace("'","",trim($txt_production_date));
	        				if($dates)
	        				{
	        					echo "Date ".change_date_format($dates)  ;
	        				}
	        				?>
	        			</td>
	        		</tr>
	        	</table>
				<div>
					<table width="1880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="left">
						<thead>
						<tr>
							<th rowspan="2" width="30" ><p>SL</p></th>
							<th rowspan="2" width="115"><p>Buyer Name</p></th>
							<th rowspan="2" width="115"><p>Job No</p></th>
							<th rowspan="2" width="80"><p>Style Reff</p></th>
							<th rowspan="2" width="80"><p>Order No</p></th>
							<th rowspan="2" width="80"><p>File No</p></th>
							<th rowspan="2" width="80"><p>Internal Ref</p></th>
							<th rowspan="2" width="80"><p>Order Qty</p></th>

							<th colspan="5" width="400"><p>Fabric Status</p></th>
							<th colspan="4" width="320"><p>Cutting Status</p></th>
							<th colspan="5" width="400"><p>Input Status</p></th>
							<th rowspan="2" width="100"><p>Line</p></th>
						</tr>
						<tr>
							<th width="80"><p>Color Name</p></th>
							<th width="80"><p>F.Fab. Req.</p></th>
							<th width="80"><p>Fin. Fab. Issued</p></th>
							<th width="80"><p>F.Issued Balance</p></th>
							<th width="80"><p>Possible Cut Qty</p></th>

							<th width="80"><p>Today</p></th>
							<th width="80"><p>Total</p></th>
							<th width="80"><p>Cut.Balance</p></th>
							<th width="80"><p>Cutting%</p></th>

							<th width="80"><p>Today</p></th>
							<th width="80"><p>Total</p></th>
							<th width="80"><p>Order-Input Balance</p></th>
							<th width="80"><p>Inhand Qty</p></th>
							<th width="80"><p>Input%</p></th>

						</tr>
						</thead>
					</table>
					<div style="max-height:400px; overflow-y:scroll; width:1900px" id="scroll_body">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1880" rules="all" id="table_body" align="left">
						<?
						$k=1;
						$jj=1;	
						$gr_order_qnty=0;
						$gr_req=0;
						$gr_iss=0;
						$gr_iss_bal=0;
						$gr_pos_cut=0;
						$gr_today_cut=0;
						$gr_cut_bal=0;
						$gr_total_cut=0;
						$gr_today_sewing=0;
						$gr_total_sewing=0;
						$gr_inh_bal=0;
						$gr_inh_qty=0;	
						
						foreach($production_main_array as $style_id=>$job_data)
						{				
							$style_wise_order_qty = 0;		
							$style_wise_fab_req = 0;		
							$style_wise_fab_issued = 0;		
							$style_wise_fab_issued_balance = 0;	

							$style_wise_fab_posible_cut_qty = 0;		
							$style_wise_cut_today = 0;		
							$style_wise_cut_total = 0;		
							$style_wise_cut_balance = 0;

							$style_wise_input_today = 0;		
							$style_wise_input_total = 0;		
							$style_wise_input_balance = 0;		
							$style_wise_inhand_qty = 0;	

							foreach($job_data as $job_id=>$po_data)
							{
								
								foreach($po_data as $po_id=>$item_data)
								{
									$order_wise_subtotal = 0;
									$order_wise_today_cutting=0;
									$order_wise_total_cutting=0;
									$order_wise_cut_balance=0;

									$order_wise_today_input=0;
									$order_wise_total_input=0;
									$order_wise_input_balance=0;
									$order_wise_inhand_qty=0;
									// fabric status sum
									$order_wise_fab_req = 0;
									$order_wise_fin_fab_req = 0;
									$order_wise_fab_issued_balance = 0;
									$order_wise_fab_possible_qty = 0;
									//

									foreach($item_data as $item_id=>$color_data)
									{
										foreach($color_data as $color_id=>$row)
										{
											$color_wise_today_cutting=0;
											$color_wise_total_cutting=0;
											$color_wise_today_sewing_input =0;
											$color_wise_total_sewing_input=0;
											$color_wise_inh=0;
											$pp=0;
											$fin_req = 0;
											// foreach($cutting_data as $cutting_id=>$row)
											// {	
												$fin_req=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
												//$issue_qty=$issue_qnty_arr[$batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]]];
												$issue_qty=$issue_qnty_arr[$po_id][$color_id];
												$req_issue_bal=$fin_req-$issue_qty;
												$possible_cut_pcs=$issue_qty/$result_consumtion[$job_id];

												$today_cutting_qnty= $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_cutting"];
												$total_cutting_qnty= $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["total_cutting"];
												$color_wise_today_cutting+=$today_cutting_qnty;
												$color_wise_total_cutting+=$total_cutting_qnty;

												$today_sewing_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
												$total_sewing_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_input"];
												$color_wise_today_sewing_input += $today_sewing_input;
												$color_wise_total_sewing_input += $total_sewing_input;

												// order wise

												$order_wise_today_cutting+=$today_cutting_qnty;
												$order_wise_total_cutting+=$total_cutting_qnty;
												

												$order_wise_today_input += $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
												$order_wise_total_input += $total_sewing_input;											

												$order_wise_inhand_qty += $total_cutting_qnty-$total_sewing_input;
												// style wise											

												$style_wise_cut_today += $today_cutting_qnty;		
												$style_wise_cut_total += $total_cutting_qnty;		


												$style_wise_input_today += $today_input;		
												$style_wise_input_total += $total_sewing_input;		
														
												// $style_wise_inhand_qty += $order_wise_inhand_qty;										
											 
												if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
														<?
														$jj++;
														// if($pp==0)
														// {
															$order_quantitys=$order_qnty_array[$po_id][$item_id][$color_id];
															$order_wise_subtotal += $order_quantitys;
															$style_wise_order_qty += $order_quantitys;
															$gr_order_qnty+=$order_quantitys;
															$gr_req+=$fin_req;
															$gr_iss+=$issue_qty;
															$gr_iss_bal+=$req_issue_bal;
															$gr_pos_cut+=$possible_cut_pcs;

															?>
														 
															<td valign="middle" rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>

															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $buyer_library[$row["buyer_name"]]; ?></p></td>

															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $job_id; ?></p></td>

															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $style_id;?></p></td>
															 
															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["po_number"];?></p></td>

															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["file_no"];?></p></td>

															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["grouping"];?></p></td>

															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"  align="right"    width="80"><p><? echo $order_quantitys; ?></p></td>
															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $color_Arr_library[$color_id];?></p></td>


															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($fin_req,2);?></p></td>

															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><a href="##" onclick="openmypage_fab_issue(<? echo $po_id;?>,<? echo $color_id?>,<? echo 1; ?>, 'fab_issue_popup');" > <p><? echo $issue_qty;?></p></a></td>
															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($req_issue_bal,2);?></p></td>
															<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($possible_cut_pcs);?></p></td>
														<?
														$order_wise_fab_req += $fin_req;
														$order_wise_fin_fab_req += $issue_qty;
														$order_wise_fab_issued_balance += $req_issue_bal;
														$order_wise_fab_possible_qty += $possible_cut_pcs;
														//}
														//$pp++;
														$gr_today_cut+=$today_cutting_qnty;
														$gr_total_cut+=$total_cutting_qnty;
														$gr_today_sewing+=$cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
														$gr_total_sewing+=$total_sewing_input;
														?>
															<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $today_cutting_qnty;?></p></td>
	 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'0',<? echo $color_id;?>,'1', 'cutting_sewing_action');" > <p><? echo $total_cutting_qnty;?></p></a></td>
															<?
															// if($pp==0)
															// {
																?>
																<td valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]+1; ?>"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $cut_balance = $order_quantitys-$total_cutting_qnty;?></p></td>
																<?
																$gr_cut_bal+=$cut_balance;
																$order_wise_cut_balance += $cut_balance;

																$style_wise_cut_balance += $cut_balance;
											 

															// }
															?>
															<td width="80" align="right"><? $cutting_prsnt = ($total_cutting_qnty*100)/$order_quantitys; echo number_format( $cutting_prsnt); ?></td>

															<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $today_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];?></p></td>

															<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'0',<? echo $color_id;?>,'4', 'cutting_sewing_action');" ><p><? echo $total_sewing_input;?></a></p></td>
															<?
															// if($pp==0)
															// {


																?>
																<td  valign="middle"  rowspan="<? //echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]+1; ?>"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo 	$input_balance = $order_quantitys-$total_sewing_input;?></p></td>
															<?
															$gr_inh_bal+=$input_balance;
															$order_wise_input_balance += $input_balance;											 
															$style_wise_input_balance += $input_balance;
						 
															// }
															$pp++;
															?>
															<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $inh_qty= $total_cutting_qnty-$total_sewing_input;$gr_inh_qty+=$inh_qty;$color_wise_inh+=$inh_qty;$style_wise_inhand_qty += $inh_qty;?></p></td>			
															<td width="80" align="right"><? $sewing_prsnt = ($total_sewing_input*100)/$order_quantitys; echo number_format($sewing_prsnt); ?></td>					 
															<td width="100"><p><? echo chop($row['sewing_line'],',');?></p></td>					 
														</tr>
												<?											
												
												
											//}
											
											//$style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]=$color_span;
											?>
											<!-- <tr bgcolor="#E4E4E4">
												<td colspan="13" align="right"><b>Color Wise Sub Total</b></td>
												
												<td align="right"><b><?php //echo $color_wise_today_cutting;?></b></td>
												<td align="right"><b><?php //echo $color_wise_total_cutting;?></b></td>
												<td></td>

												<td align="right"><b><?php //echo $color_wise_today_sewing_input;?></b></td>
												<td align="right"><b><?php //echo $color_wise_total_sewing_input;?></b></td>
												<td align="right"><?php //echo $color_wise_inh;?></td>
												<td></td>
												 
											
											</tr> -->
											<?
											$style_wise_fab_req += $fin_req;		
											$style_wise_fab_issued += $issue_qty;		
											$style_wise_fab_issued_balance += $req_issue_bal;		
											$style_wise_fab_posible_cut_qty += $possible_cut_pcs;
										}

									}
									?>
									<tr bgcolor="#E4E4E4">
										<td colspan="7" align="right"><b>Order Wise Sub Total</b></td>
										<td align="right"><b><? echo $order_wise_subtotal;?></b></td>
										<td></td>
										<td align="right"><b><? echo $order_wise_fab_req;?></b></td>
										<td align="right"><b><? echo $order_wise_fin_fab_req;?></b></td>
										<td align="right"><b><? echo number_format($order_wise_fab_issued_balance,2);?></b></td>
										<td align="right"><b><? echo number_format($order_wise_fab_possible_qty);?></b></td>
										<td align="right"><b><? echo $order_wise_today_cutting;?></b></td>
										<td align="right"><b><? echo $order_wise_total_cutting;?></b></td>
										<td align="right"><b><? echo $order_wise_cut_balance; ?></b></td>
										<td></td>
										<td align="right"><b><? echo $order_wise_today_input;?></b></td>
										<td align="right"><b><? echo $order_wise_total_input;?></b></td>
										<td align="right"><b><? echo $order_wise_input_balance;?></b></td>
										<td align="right"><b><? echo $order_wise_inhand_qty;?></b></td>
										<td></td>
										<td></td>
									</tr>
									<?
								}
							
							}
							$k++;
							?>
							<tr bgcolor="#E4E4E4">
							<td colspan="7" align="right"><b>Style Wise Sub Total</b></td>
							<td align="right"><b><? echo $style_wise_order_qty;?></b></td>
							<td></td>
							<td align="right"><b><? echo $style_wise_fab_req;?></b></td>
							<td align="right"><b><? echo $style_wise_fab_issued;?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_issued_balance,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_posible_cut_qty);?></b></td>
							<td align="right"><b><? echo $style_wise_cut_today;?></b></td>
							<td align="right"><b><? echo $style_wise_cut_total;?></b></td>
							<td align="right"><b><? echo $style_wise_cut_balance;?></b></td>
							<td></td>
							<td align="right"><b><? echo $style_wise_input_today;?></b></td>
							<td align="right"><b><? echo $style_wise_input_total;?></b></td>
							<td align="right"><b><? echo $style_wise_input_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_inhand_qty;?></b></td>
							<td></td>
							<td></td>
							</tr>	
							<?
						}

						?>
						<tr bgcolor="#E4E4E4">  
							 
							<td style="word-wrap: break-word;word-break: break-all;" colspan="7" align="right"><strong>Grand Total</strong></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"    align="right"><b><? echo $gr_order_qnty;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo $gr_req;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo $gr_iss;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_iss_bal,2);?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_pos_cut);?></b></td>

							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_cut;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_cut;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_cut_bal;?></b></td>
							<td></td>

							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_sewing;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_sewing;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_inh_bal;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_inh_qty;?></b></td>	
							<td></td>
							<td></td>
						</tr>												
						</table>					  
					</div>	
				</div>
			 </div> 
	        <?
		
	}
	else if($type == 5)
	{

	 	// button 5 for chaity 	 		
			$str_po_cond="";
			$_SESSION["work_comp"]="";
			$_SESSION["work_comp"]=$cbo_work_company_name;

			//if($cbo_work_company_name!=0) $str_po_cond.=" and d.serving_company in($cbo_work_company_name)";		
			if($cbo_work_company_name!=0) $company_cond=" and d.serving_company in($cbo_work_company_name)";		

			if(str_replace("'", "",$cbo_buyer_name)!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";
			$cbo_location_name=str_replace("'", "", $cbo_location_name);
			if($cbo_location_name) $str_po_cond.=" and d.location in($cbo_location_name)";
			$cbo_floor_name=str_replace("'", "", $cbo_floor_name);
			if($cbo_floor_name) $str_po_cond.=" and d.floor_id in($cbo_floor_name)";
			// if($txt_production_date != "") $str_po_cond.=" and d.production_date=$txt_production_date";
			if(str_replace("'", "", $cbo_year))
			{
				if($db_type==0)
				{
					$str_po_cond .=" and year(a.insert_date)=$cbo_year";
				}
				else
				{
					$str_po_cond .=" and to_char(a.insert_date,'YYYY')=$cbo_year";
				}	
			}
			
			$hidden_job_id=str_replace("'","",$hidden_job_id);
			if($hidden_job_id!="")
			{
				$str_po_cond.=" and a.id in($hidden_job_id)";
			}

			$hidden_order_id=str_replace("'","",$hidden_order_id);
			if($hidden_order_id)
			{
				$str_po_cond.=" and b.id in($hidden_order_id)";
			} 
			$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
			$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);

			$sql_order_col="SELECT c.color_number_id,b.id as po_id  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond $company_cond and d.production_date=$txt_production_date and d.production_type in(1,4,5,8) group by c.color_number_id,b.id " ;
			$po_id_array=array();
			$col_id_array=array();
			foreach(sql_select($sql_order_col) as $row) 
			{
				$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
				$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
			}
			$po_ids=implode(",", $po_id_array);
			$color_ids=implode(",", $col_id_array);
			
			
			if(!$po_ids) $po_ids=0;
			if(!$color_ids) $color_ids=0;
			$order_qnty_array=array();
			 $order_qnty_sqls="SELECT po_break_down_id,color_number_id,order_quantity,item_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in ($po_ids)";
			 foreach(sql_select($order_qnty_sqls) as $values)
			 {
			 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]+=$values[csf("order_quantity")];

			 }



	 
			$order_sql_for_po="SELECT c.id as col_size_id,a.style_ref_no,a.job_no,b.id as po_id,d.item_number_id,c.color_number_id,e.cut_no as cutting_no,b.po_number,d.serving_company,d.location,a.buyer_name,c.order_quantity, 
			sum(case when d.production_type=1 then e.production_qnty else 0 end ) as cutting_qnty,
			sum(case when d.production_type=4 then e.production_qnty else 0 end ) as sewing_qnty, 
			sum(case when d.production_type=5 then e.production_qnty else 0 end ) as sewing_qnty_out ,
			sum(case when d.production_type=8 then e.production_qnty else 0 end ) as finishing_qnty 
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond  $company_cond   and c.color_number_id in($color_ids) and b.id in($po_ids) and d.production_type in(1,4,5,8)    group by c.id,a.style_ref_no,a.job_no,b.id ,d.item_number_id,c.color_number_id,e.cut_no,b.po_number,d.serving_company,d.location,a.buyer_name,c.order_quantity order by c.id " ;
			// echo $order_sql_for_po;die;
			 
			foreach(sql_select($order_sql_for_po)  as $row)
			{
				//if($row[csf("cutting_qnty")]>0 || $row[csf("sewing_qnty")]>0 )
				//{
					
				
					if($po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=="")
					{
						$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

							$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("serving_company")];

							$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

							 

							$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	
							$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];


					}

						
						 

						 
					//}	 

				
			}
			 
			

			 $order_sql=sql_select("SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no  ,   c.color_number_id, sum(c.order_quantity) as order_quantity, 

			sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_cutting ,
			sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

			sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_input ,
			sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input ,

			sum(case when d.production_type=5 and e.production_type=5 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_output ,
			sum(case when d.production_type=5 and e.production_type=5   then e.production_qnty else 0 end ) as total_sewing_output ,

			sum(case when d.production_type=8 and e.production_type=8 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_finishing ,
			sum(case when d.production_type=8 and e.production_type=8   then e.production_qnty else 0 end ) as total_finishing 


			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond and b.id in($po_ids) and c.color_number_id in($color_ids) and d.production_type in(1,4,5,8) 
			group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  ,  c.color_number_id,e.cut_no ");



			 //echo $order_sql;die;

			foreach($order_sql as $vals)
			{
				$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];			 

				$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];
				 

				$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

				$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

				// =============================================================================
				$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_output"]+=$vals[csf("today_sewing_output")];

				$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_output"]+=$vals[csf("total_sewing_output")];


				$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["today_finishing"]+=$vals[csf("today_finishing")];

				$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["total_finishing"]+=$vals[csf("total_finishing")];

			}
			//echo "<pre>";
			//print_r($cutting_sewing_data);die;

		  $lay_sqls="SELECT  a.job_no,c.order_id, b.gmt_item_id,b.color_id,sum( case when a.entry_date=$txt_production_date then  c.size_qty else 0 end) as today_lay,sum(c.size_qty) as total_lay from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.order_id in($po_ids) and b.color_id in($color_ids) group by a.job_no,c.order_id, b.gmt_item_id,b.color_id ";

			$lay_qnty_array=array();
			foreach(sql_select($lay_sqls) as $vals)
			{
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["today_lay"]+=$vals[csf("today_lay")];
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("total_lay")];
			}



			$ex_factory_arr=array();
			$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id,c.color_number_id, sum(CASE WHEN a.entry_form!=85 and ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS today_ex_fac , sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id  and  a.id=b.mst_id and b.color_size_break_down_id=c.id and  a.po_break_down_id in($po_ids) and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.delivery_company_id in($cbo_work_company_name) group by a.po_break_down_id, a.item_number_id,c.color_number_id");
			foreach($ex_factory_data as $exRow)
			{
				$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
				$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['total_ex_fac']=+$exRow[csf('total_ex_fac')];
			}
			//print_r($ex_factory_arr);

			  
			 
			//$all_po_lay_id=implode(",", $all_po_lay_id_array);
			/*if( count($all_po_lay_id)>0 )
			{
				$po_conds=" and a.po_break_down_id in($all_po_lay_id)";
			}*/

			$po_conds=" and a.po_break_down_id in($po_ids)";

			$booking_no_fin_qnty_array=array();
			$booking_sql=sql_select("SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0   $po_conds");
			foreach($booking_sql as $vals)
			{
				$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["booking_no"]=$vals[csf("booking_no")];
				$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];

			}
			$batch_sql=sql_select("SELECT id,booking_no,batch_no,color_id from pro_batch_create_mst where status_active=1 and is_deleted=0 and booking_no is not null group by id,booking_no,batch_no,color_id ");
			foreach($batch_sql as $rows)
			{
				//$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
				if(!$duplicate_batch[$rows[csf("batch_no")]])
				{
					if($batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=="")
					{
						$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
					}
					else
					{
						$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]].=','.$rows[csf("batch_no")];

					}
					
					$duplicate_batch[$rows[csf("batch_no")]]=trim($rows[csf("batch_no")]);
				}
				$batch_mst_id_arr[$rows[csf("booking_no")]]=$rows[csf("id")];
			}
			/*echo "<pre>";
			 print_r($batch_mst_id_arr);die;*/
			 
			 $issue_sql=sql_select("SELECT po_breakdown_id,quantity,color_id from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=18 ");
			 foreach($issue_sql as $values)
			 {
			 	//$issue_qnty_arr[$values[csf("batch_id")]]+=$values[csf("issue_qnty")];
			 	$issue_qnty_arr[$values[csf("po_breakdown_id")]][$values[csf("color_id")]]+=$values[csf("quantity")];
			 }

			$result_consumtion=array();
			$sql_consumtiont_qty=sql_select(" SELECT a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id in($po_ids)  GROUP BY a.job_no, a.body_part_id");

			foreach($sql_consumtiont_qty as $row_consum)
			{

				$result_consumtion[$row_consum[csf('job_no')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
			}
			unset($sql_consumtiont_qty); 


			 
			
			if(count($production_main_array)==0)
			{
				echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px">No Data Found.</div>'; die;
			} 
			ob_start();	
			
			?>
			 
	        <div>
	        	<table width="2650" cellspacing="0" >
	        		<tr class="form_caption" style="border:none;">
	        			<td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
	        				Daily RMG Production Report
	        			</strong></td>
	        		</tr>
	        		<tr style="border:none;">
	        			<td colspan="24" align="center" style="border:none; font-size:14px;">
	        				<strong>
	        					Working Company Name : <? 
	        					$comp_names=""; 
	        					foreach(explode(",",$cbo_work_company_name) as $vals) 
	        					{
	        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
	        					}
	        					echo $comp_names;
	        					 ?>
	        				</strong>                                
	        			</td>
	        		</tr>
	        		<tr style="border:none;">
	        			<td colspan="24" align="center" style="border:none;font-size:12px; font-weight:bold" >
	        				<?
	        				$dates=str_replace("'","",trim($txt_production_date));
	        				if($dates)
	        				{
	        					echo "Date ".change_date_format($dates)  ;
	        				}
	        				?>
	        			</td>
	        		</tr>
	        	</table>
				<div>
					<table width="2890" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
						<thead>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Working Company</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Location</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer Name</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Job No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Style Reff</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Color Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>F.Booking No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Batch No</p></th>	

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="5" width="400"><p>Fabric Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Lay Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Cutting Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="320"><p>Input Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Sewing Output</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Finishing</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="320"><p>Export</p></th>
						</tr>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Color Name</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>F.Fab. Req.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Fin. Fab. Issued</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>F.Issued Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Possible Cut Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Lay.Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Cut.Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order-Input Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Inhand Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Input -Sewing Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Sewing -Fini. Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order - Exfactory Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Sewing - Exfactory Balance</p></th>

						</tr>
							
							  
						   
						</thead>
					</table>
					<div style="max-height:400px; overflow-y:scroll; width:2910px" id="scroll_body">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2890" rules="all" id="table_body" >
						<?
						$k=1;
						$jj=1;	
						$gr_order_qnty=0;
						$gr_req=0;
						$gr_iss=0;
						$gr_iss_bal=0;
						$gr_pos_cut=0;
						$gr_today_cut=0;
						$gr_cut_bal=0;
						$gr_total_lay=0;
						$gr_today_lay=0;
						$gr_lay_bal=0;
						$gr_total_cut=0;

						$gr_today_sewing=0;
						$gr_total_sewing=0;
						$gr_inh_bal=0;
						$gr_inh_qty=0;

						$gr_today_output = 0;
						$gr_total_output = 0;
						$gr_input_sewing_balance = 0;

						$gr_today_finishing = 0;
						$gr_total_finishing = 0;
						$gr_sewing_fin_balance = 0;

						$gr_today_export = 0;
						$gr_total_export = 0;
						$gr_order_xfact_balance = 0;
						$gr_sewing_xfact_balance = 0;
						
						foreach($production_main_array as $style_id=>$job_data)
						{				
							$style_wise_order_qty = 0;		
							$style_wise_fab_req = 0;		
							$style_wise_fab_issued = 0;		
							$style_wise_fab_issued_balance = 0;	

							$style_wise_fab_posible_cut_qty = 0;		
							$style_wise_cut_today = 0;		
							$style_wise_cut_total = 0;		
							$style_wise_cut_balance = 0;

							$style_wise_lay_today = 0;		
							$style_wise_lay_total = 0;		
							$style_wise_lay_balance = 0;


							$style_wise_input_today = 0;		
							$style_wise_input_total = 0;		
							$style_wise_input_balance = 0;		
							$style_wise_inhand_qty = 0;	

							$style_wise_today_output = 0;
							$style_wise_total_output = 0;
							$style_wise_input_sewing_balance = 0;

							$style_wise_today_finishing = 0;
							$style_wise_total_finishing = 0;
							$style_wise_sewing_fin_balance = 0;

							$style_wise_today_export = 0;
							$style_wise_total_export = 0;
							$style_wise_order_xfact_balance = 0;
							$style_wise_sewing_xfact_balance = 0;

							foreach($job_data as $job_id=>$po_data)
							{
								
								foreach($po_data as $po_id=>$item_data)
								{
									$order_wise_subtotal = 0;
									$order_wise_today_cutting=0;
									$order_wise_total_cutting=0;
									$order_wise_cut_balance=0;

									$order_wise_today_lay=0;
									$order_wise_total_lay=0;
									$order_wise_lay_balance=0;

									$order_wise_today_input=0;
									$order_wise_total_input=0;
									$order_wise_input_balance=0;
									$order_wise_inhand_qty=0;
									// fabric status sum
									$order_wise_fab_req = 0;
									$order_wise_fin_fab_req = 0;
									$order_wise_fab_issued_balance = 0;
									$order_wise_fab_possible_qty = 0;

									// sewing output
									$order_wise_today_output = 0;
									$order_wise_total_output = 0;
									$order_wise_input_sewing_balance = 0;

									$order_wise_today_finishing = 0;
									$order_wise_total_finishing = 0;
									$order_wise_sewing_fin_balance = 0;

									$order_wise_today_export = 0;
									$order_wise_total_export = 0;
									$order_wise_order_exfact_balance = 0;
									$order_wise_sewing_exfact_balance = 0;

									foreach($item_data as $item_id=>$color_data)
									{
										foreach($color_data as $color_id=>$row)
										{
											$color_wise_today_cutting=0;
											$color_wise_total_cutting=0;
											$color_wise_today_sewing_input =0;
											$color_wise_total_sewing_input=0;
											$color_wise_inh=0;
											$pp=0;
											$fin_req = 0;
											//foreach($cutting_data as $cutting_id=>$row)
											//{
												 

												$today_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['today_ex_fac'];
												$total_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['total_ex_fac'];									
												$fin_req=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
												//$issue_qty=$issue_qnty_arr[$batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]]];
												$issue_qty=$issue_qnty_arr[$po_id][$color_id];

												$req_issue_bal=$fin_req-$issue_qty;
												$possible_cut_pcs=$issue_qty/$result_consumtion[$job_id];

											 	$today_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["today_lay"];
											 	$total_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_lay"]; 



												$today_cutting_qnty= $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_cutting"];
												$total_cutting_qnty= $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["total_cutting"];
												$color_wise_today_cutting+=$today_cutting_qnty;
												$color_wise_total_cutting+=$total_cutting_qnty;

												$today_sewing_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
												$total_sewing_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_input"];
												$today_sewing_output = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_output"];

												$total_sewing_output = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_output"];

												$today_finishing = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][0]["today_finishing"];

												$total_finishing = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][0]["total_finishing"];

												$color_wise_today_sewing_input += $today_sewing_input;
												$color_wise_total_sewing_input += $total_sewing_input;

												// order wise

												$order_wise_today_cutting+=$today_cutting_qnty;
												$order_wise_total_cutting+=$total_cutting_qnty;

												$order_wise_today_lay+=$today_lay_qnty;
												$order_wise_total_lay+=$total_lay_qnty;

												$order_wise_today_input += $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
												$order_wise_total_input += $total_sewing_input;										

												$order_wise_inhand_qty += $total_cutting_qnty-$total_sewing_input;

												$order_wise_today_output += $today_sewing_output;
												$order_wise_total_output += $total_sewing_output;

												$order_wise_today_finishing += $today_finishing;
												$order_wise_total_finishing += $total_finishing;

												$order_wise_today_export += $today_ex_fac;
												$order_wise_total_export += $total_ex_fac;

												// style wise
												
												$style_wise_fab_req += $fin_req;		
												$style_wise_fab_issued += $issue_qty;		
												$style_wise_fab_issued_balance += $req_issue_bal;		
												$style_wise_fab_posible_cut_qty += $possible_cut_pcs;

												$style_wise_cut_today += $today_cutting_qnty;		
												$style_wise_cut_total += $total_cutting_qnty;

												$style_wise_lay_today += $today_lay_qnty;		
												$style_wise_lay_total += $total_lay_qnty;

												$style_wise_input_today += $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];;		
												$style_wise_input_total += $total_sewing_input;													
												$style_wise_inhand_qty += $order_wise_inhand_qty;

												$style_wise_today_output += $today_sewing_output;
												$style_wise_total_output += $total_sewing_output;

												$style_wise_today_finishing += $today_finishing;
												$style_wise_total_finishing += $total_finishing;

												$style_wise_today_export += $today_ex_fac;
												$style_wise_total_export += $total_ex_fac;

												// grand total
												$gr_today_output += $today_sewing_output;
												$gr_total_output += $total_sewing_output;

												$gr_today_finishing += $today_finishing;
												$gr_total_finishing += $total_finishing;

												$gr_today_export += $today_ex_fac;
												$gr_total_export += $total_ex_fac;
												
											 
												if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
														<?
														$jj++;
														
															$order_quantitys=$order_qnty_array[$po_id][$item_id][$color_id];
															$order_wise_subtotal += $order_quantitys;
															$style_wise_order_qty += $order_quantitys;
															$gr_order_qnty+=$order_quantitys;
															$gr_req+=$fin_req;
															$gr_iss+=$issue_qty;
															$gr_iss_bal+=$req_issue_bal;
															$gr_pos_cut+=$possible_cut_pcs;

															?>
														 
															<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>

															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $company_library[$row["working_company_id"]]; ?></p></td>

															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $location_library[$row["location_id"]]; ?></p></td>

															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $buyer_library[$row["buyer_name"]]; ?></p></td>

															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $job_id; ?></p></td>

															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $style_id;?></p></td>
															 
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["po_number"];?></p></td>

															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="right"    width="80"><p><? echo $order_quantitys; ?></p></td> 
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"] ;?></p></td>
															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $batch_mst_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]][$color_id];?></p></td>
															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $color_Arr_library[$color_id];?></p></td>


															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($fin_req,2);?></p></td>

															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><a href="##" onclick="openmypage_fab_issue(<? echo $po_id;?>,<? echo $color_id?>,<? echo 2 ?>, 'fab_issue_popup');" > <p><? echo number_format($issue_qty,2);?></p></a></td>
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($req_issue_bal,2);?></p></td>
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($possible_cut_pcs,2);?></p></td>
														<?
														$order_wise_fab_req += $fin_req;
														$order_wise_fin_fab_req += $issue_qty;
														$order_wise_fab_issued_balance += $req_issue_bal;
														$order_wise_fab_possible_qty += $possible_cut_pcs;
														
														$gr_today_cut+=$today_cutting_qnty;
														$gr_total_cut+=$total_cutting_qnty;

														$gr_today_lay+=$today_lay_qnty;
														$gr_total_lay+=$total_lay_qnty;

														$gr_today_sewing+=$cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
														$gr_total_sewing+=$total_sewing_input;
														?>
														<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $today_lay_qnty;?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="80"><p><? echo $total_lay_qnty;?></p></td>

														<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $lay_balance = $order_quantitys-$total_lay_qnty;?></p></td>
															

															<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $today_cutting_qnty;?></p></td>
	 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'<? echo  0;?>',<? echo $color_id;?>,'1', 'cutting_sewing_action');" > <p><? echo $total_cutting_qnty;?></p></a></td>
															
																<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $cut_balance = $order_quantitys-$total_cutting_qnty;?></p></td>
																<?
																$gr_lay_bal+=$lay_balance;
																$order_wise_lay_balance += $lay_balance;
																$style_wise_lay_balance += $lay_balance;


																$gr_cut_bal+=$cut_balance;
																$order_wise_cut_balance += $cut_balance;

																$style_wise_cut_balance += $cut_balance;										 

															
															?>
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $today_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];?></p></td>

															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'<? echo  0;?>',<? echo $color_id;?>,'4', 'cutting_sewing_action');" ><p><? echo $total_sewing_input;?></a></p></td>
															
																<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo 	$input_balance = $order_quantitys-$total_sewing_input;?></p></td>
															<?
															$gr_inh_bal+=$input_balance;
															$order_wise_input_balance += $input_balance;											 
															$style_wise_input_balance += $input_balance;
						 
															
															?>
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $inh_qty= $total_cutting_qnty-$total_sewing_input;$gr_inh_qty+=$inh_qty;$color_wise_inh+=$inh_qty;?></p></td>		
															<!-- =================********=================-->						 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $today_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_output"];?></p></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'<? echo  0;?>',<? echo $color_id;?>,'5', 'cutting_sewing_action');" ><p><? echo $total_sewing_output;?></p></a></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="80"><p><? echo $input_sewing_balance = $total_sewing_input-$total_sewing_output;?></p></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $today_finishing;?></p></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'<? echo  0;?>',<? echo $color_id;?>,'8', 'cutting_sewing_action');" ><p><? echo $total_finishing;?></p></a></td>							 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $sewing_fin_balance = $total_sewing_output-$total_finishing;?></p></td>




															<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $today_ex_fac;?></p></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_ex_fac_total(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>, 'total_exfac_action');" ><p><? echo $total_ex_fac;?></p></a></td>							 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $order_xfact= $order_quantitys-$total_ex_fac;?></p></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $sewing_xfact= $total_sewing_output-$total_ex_fac;?></p></td>								 
														</tr>	
												<?											
												
												$order_wise_input_sewing_balance += $input_sewing_balance;
												$order_wise_sewing_fin_balance += $sewing_fin_balance;

												$style_wise_input_sewing_balance += $input_sewing_balance;
												$style_wise_sewing_fin_balance += $sewing_fin_balance;

												$gr_input_sewing_balance += $input_sewing_balance;	
												$gr_sewing_fin_balance += $sewing_fin_balance;

												$order_wise_order_exfact_balance += $order_xfact;
												$order_wise_sewing_exfact_balance += $sewing_xfact;

												$style_wise_order_xfact_balance += $order_xfact;
												$style_wise_sewing_xfact_balance += $sewing_xfact;
												
												$gr_order_xfact_balance += $order_xfact;
												$gr_sewing_xfact_balance += $sewing_xfact;
												$k++;										
											//}									
											
											
										}

									}
									?>
									<tr bgcolor="#E4E4E4">
										<td colspan="7" align="right"><b>Order Wise Sub Total</b></td>
										<td align="right"><b><? echo $order_wise_subtotal;?></b></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="right"><b><? echo number_format($order_wise_fab_req,2);?></b></td>
										<td align="right"><b><? echo number_format($order_wise_fin_fab_req,2);?></b></td>
										<td align="right"><b><? echo number_format($order_wise_fab_issued_balance,2);?></b></td>
										<td align="right"><b><? echo number_format($order_wise_fab_possible_qty,2);?></b></td>
										<td align="right"><b><? echo $order_wise_today_lay;?></b></td>
										<td align="right"><b><? echo $order_wise_total_lay;?></b></td>
										<td align="right"><b><? echo $order_wise_lay_balance; ?></b></td>

										<td align="right"><b><? echo $order_wise_today_cutting;?></b></td>
										<td align="right"><b><? echo $order_wise_total_cutting;?></b></td>
										<td align="right"><b><? echo $order_wise_cut_balance; ?></b></td>
										<td align="right"><b><? echo $order_wise_today_input;?></b></td>
										<td align="right"><b><? echo $order_wise_total_input;?></b></td>
										<td align="right"><b><? echo $order_wise_input_balance;?></b></td>
										<td align="right"><b><? echo $order_wise_inhand_qty;?></b></td>
										<!-- =================********=================-->
										<td align="right"><b><? echo $order_wise_today_output;?></b></td>
										<td align="right"><b><? echo $order_wise_total_output;?></b></td>
										<td align="right"><b><? echo $order_wise_input_sewing_balance;?></b></td>

										<td align="right"><b><? echo $order_wise_today_finishing;?></b></td>
										<td align="right"><b><? echo $order_wise_total_finishing;?></b></td>
										<td align="right"><b><? echo $order_wise_sewing_fin_balance;?></b></td>

										<td align="right"><b><? echo $order_wise_today_export;?></b></td>
										<td align="right"><b><? echo $order_wise_total_export;?></b></td>
										<td align="right"><b><? echo $order_wise_order_exfact_balance;?></b></td>
										<td align="right"><b><? echo $order_wise_sewing_exfact_balance;?></b></td>
									</tr>
									<?
								}
							
							}
							
							?>
							<tr bgcolor="#E4E4E4">
							<td colspan="7" align="right"><b>Style Wise Sub Total</b></td>
							<td align="right"><b><? echo $style_wise_order_qty;?></b></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><b><? echo number_format($style_wise_fab_req,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_issued,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_issued_balance,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_posible_cut_qty,2);?></b></td>
							<td align="right"><b><? echo $style_wise_lay_today;?></b></td>
							<td align="right"><b><? echo $style_wise_lay_total;?></b></td>
							<td align="right"><b><? echo $style_wise_lay_balance;?></b></td>

							<td align="right"><b><? echo $style_wise_cut_today;?></b></td>
							<td align="right"><b><? echo $style_wise_cut_total;?></b></td>
							<td align="right"><b><? echo $style_wise_cut_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_input_today;?></b></td>
							<td align="right"><b><? echo $style_wise_input_total;?></b></td>
							<td align="right"><b><? echo $style_wise_input_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_inhand_qty;?></b></td>
							<!-- =================********=================-->
							<td align="right"><b><? echo $style_wise_today_output;?></b></td>
							<td align="right"><b><? echo $style_wise_total_output;?></b></td>
							<td align="right"><b><? echo $style_wise_input_sewing_balance;?></b></td>

							<td align="right"><b><? echo $style_wise_today_finishing;?></b></td>
							<td align="right"><b><? echo $style_wise_total_finishing;?></b></td>
							<td align="right"><b><? echo $style_wise_sewing_fin_balance;?></b></td>

							<td align="right"><b><? echo $style_wise_today_export;?></b></td>
							<td align="right"><b><? echo $style_wise_total_export;?></b></td>
							<td align="right"><b><? echo $style_wise_order_xfact_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_sewing_xfact_balance;?></b></td>
							</tr>	
							<?
							$gr_inh_qty;
						}

						?>
						<tr bgcolor="#E4E4E4">  
							 
							<td style="word-wrap: break-word;word-break: break-all;" colspan="7" align="right"><strong>Grand Total</strong></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"    align="right"><b><? echo $gr_order_qnty;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_req,2);?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_iss,2);?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_iss_bal,2);?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_pos_cut,2);?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_lay;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_lay;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_lay_bal;?></b></td>

							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_cut;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_cut;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_cut_bal;?></b></td>

							

							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_sewing;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_sewing;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_inh_bal;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_inh_qty;?></b></td>		
							<!-- =================********=================-->									 
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_output;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_output;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_input_sewing_balance;?></b></td>

							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_finishing;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_finishing;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_sewing_fin_balance;?></b></td>

							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_export;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_export;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_order_xfact_balance;?></b></td>
							<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_sewing_xfact_balance;?></b></td>											 
											
						</tr>	
											
						</table>					  
					</div>				 

				</div>
			 </div> 
	        <?
	 
	}
	else if($type ==6)
	{

 	 	// button 6 for chaity    
 		$str_po_cond="";
 		$str_po_cond2="";
 		$str_loc_cond_lay="";
 		$_SESSION["txt_production_date"]="";
  		$_SESSION["txt_production_date"]=$txt_production_date;
		$_SESSION["work_comp"]="";
		$_SESSION["work_comp"]=$cbo_work_company_name;
		if($cbo_work_company_name!=0) $company_cond=" and d.serving_company in($cbo_work_company_name)";		
		if($cbo_work_company_name!=0) $company_cond_lay=" and d.working_company_id in($cbo_work_company_name)";		
		if($cbo_work_company_name!=0) $company_cond_lay_a=" and a.working_company_id in($cbo_work_company_name)";		
		if($cbo_work_company_name!=0) $company_cond_delv=" and d.delivery_company_id in($cbo_work_company_name)";		
		if(str_replace("'", "",$cbo_buyer_name)!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";
		if(str_replace("'", "",$cbo_buyer_name)!=0) $str_po_cond2.=" and a.buyer_name=$cbo_buyer_name";
		$cbo_location_name=str_replace("'", "", $cbo_location_name);
		if($cbo_location_name) $str_po_cond.=" and d.location_id in($cbo_location_name)";
		if($cbo_location_name) $str_po_cond2.=" and d.location in($cbo_location_name)";
		if($cbo_location_name) $str_loc_cond_lay.=" and a.location_id in($cbo_location_name)";
		$cbo_floor_name=str_replace("'", "", $cbo_floor_name);
		if($cbo_floor_name) $str_po_cond.=" and d.floor_id in($cbo_floor_name)";
		if($cbo_floor_name) $str_po_cond2.=" and d.floor_id in($cbo_floor_name)";
		if(str_replace("'", "", $cbo_year))
		{
			if($db_type==0)
			{
				$str_po_cond .=" and year(a.insert_date)=$cbo_year";
				$str_po_cond2 .=" and year(a.insert_date)=$cbo_year";
			}
			else
			{
				$str_po_cond .=" and to_char(a.insert_date,'YYYY')=$cbo_year";
				$str_po_cond2 .=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			}	
		}
		
		$hidden_job_id=str_replace("'","",$hidden_job_id);
		if($hidden_job_id!="")
		{
			$str_po_cond.=" and a.id in($hidden_job_id)";
			$str_po_cond2.=" and a.id in($hidden_job_id)";
		}

		$hidden_order_id=str_replace("'","",$hidden_order_id);
		if($hidden_order_id)
		{
			$str_po_cond.=" and b.id in($hidden_order_id)";
			$str_po_cond2.=" and b.id in($hidden_order_id)";
		} 
		$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);
		if($cbo_shipping_status)
		{
			$str_po_cond.=" and b.shiping_status in($cbo_shipping_status)";
			$str_po_cond2.=" and b.shiping_status in($cbo_shipping_status)";
		} 
		 


		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);
		$prod_sqls="SELECT  po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date group by po_break_down_id ";
		$prod_po_id_array=array();
		foreach(sql_select($prod_sqls) as $val)
		{
			$prod_po_id_array[$val[csf("po_break_down_id")]]=$val[csf("po_break_down_id")];
		}

		$prod_sqls_ex="SELECT  po_break_down_id from pro_ex_factory_mst where status_active=1 and is_deleted=0 and ex_factory_date=$txt_production_date group by po_break_down_id ";

		foreach(sql_select($prod_sqls_ex) as $val)
		{
			$prod_po_id_array[$val[csf("po_break_down_id")]]=$val[csf("po_break_down_id")];
		}

		// =========================================== MAIN QUERY ======================================
		$today_lay_sql="SELECT c.color_number_id,b.id as po_id,(b.unit_price/a.total_set_qnty) as unit_price, a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
		where b.is_confirmed=1 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.job_no=a.job_no   and d.id=e.mst_id  and a.job_no=c.job_no_mst and e.mst_id=f.mst_id and f.order_id=b.id and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond $company_cond_lay group by c.color_number_id,b.id ,b.unit_price ,a.total_set_qnty,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status " ;  

		// echo $today_lay_sql;
		$po_id_array=array();
		$col_id_array=array();
		$production_main_array=array();
		foreach(sql_select($today_lay_sql) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["unit_price"]=$row[csf("unit_price")];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
			unset($prod_po_id_array[$row[csf("po_id")]]);

		}
		$prod_po_ids=implode(",", $prod_po_id_array);
		if($prod_po_ids)
		{
			$po_conds2=" and b.id in($prod_po_ids)";
		}



		/*
		 $today_lay_sql2="SELECT c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,c.color_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,a.buyer_name,b.pub_shipment_date,b.shiping_status  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.job_no=a.job_no      and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0    $po_conds2   group by c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,c.color_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,a.buyer_name,b.pub_shipment_date,b.shiping_status " ; 
		 
		//$po_id_array=array();
		//$col_id_array=array();
		//$production_main_array=array();
		foreach(sql_select($today_lay_sql2) as $row) 
		{
			$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
			$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

			$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];	

			$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];

		}*/




		$po_ids=implode(",", $po_id_array);
		$color_ids=implode(",", $col_id_array);
		if(!$po_ids) $po_ids=0;
		if(!$color_ids) $color_ids=0;

		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( po_id_string in ($ids) ";
				else
					$po_cond.=" or   po_id_string in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and po_id_string in ($po_ids) ";
		}
		//echo $po_cond;die;
		

		
		
		
		$order_qnty_array=array();
		$po_cond1=str_replace("po_id_string", "po_break_down_id", $po_cond);
		 $order_qnty_sqls="SELECT po_break_down_id,color_number_id,order_quantity,item_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $po_cond1 ";
		 foreach(sql_select($order_qnty_sqls) as $values)
		 {
		 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]+=$values[csf("order_quantity")];

		 }



  
		 
		
		 $po_cond2=str_replace("po_id_string", "b.id", $po_cond);
		 $order_sql=sql_select("SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no  ,   c.color_number_id, sum(c.order_quantity) as order_quantity, 

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input ,

		sum(case when d.production_type=5 and e.production_type=5 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_output ,
		sum(case when d.production_type=5 and e.production_type=5   then e.production_qnty else 0 end ) as total_sewing_output ,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_finishing ,
		sum(case when d.production_type=8 and e.production_type=8   then e.production_qnty else 0 end ) as total_finishing 


		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond2 $po_cond2  $company_cond and d.production_type in(1,4,5,8) 
		group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  ,  c.color_number_id,e.cut_no ");



		 //echo $order_sql;die;

		foreach($order_sql as $vals)
		{
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];			 

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];
			 

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

			 
			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_output"]+=$vals[csf("today_sewing_output")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_output"]+=$vals[csf("total_sewing_output")];


			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["today_finishing"]+=$vals[csf("today_finishing")];

			$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["total_finishing"]+=$vals[csf("total_finishing")];

		}
		//echo "<pre>";
		//print_r($cutting_sewing_data);die;
	  $po_cond3=str_replace("po_id_string", "c.order_id", $po_cond); 	
	  $lay_sqls="SELECT  a.job_no,c.order_id, b.gmt_item_id,b.color_id,sum( case when a.entry_date=$txt_production_date then  c.size_qty else 0 end) as today_lay,sum(c.size_qty) as total_lay from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond3  $company_cond_lay_a  $str_loc_cond_lay  group by a.job_no,c.order_id, b.gmt_item_id,b.color_id ";

		$lay_qnty_array=array();
		foreach(sql_select($lay_sqls) as $vals)
		{
			$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["today_lay"]+=$vals[csf("today_lay")];
			$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("total_lay")];
		}


		$po_cond4=str_replace("po_id_string", "a.po_break_down_id", $po_cond); 	
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id,c.color_number_id, sum(CASE WHEN a.entry_form!=85 and ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS today_ex_fac , sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id  and  a.id=b.mst_id and b.color_size_break_down_id=c.id   and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond_delv  $po_cond4  group by a.po_break_down_id, a.item_number_id,c.color_number_id");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['total_ex_fac']=+$exRow[csf('total_ex_fac')];
		}
		//print_r($ex_factory_arr);

		  
		 
		//$all_po_lay_id=implode(",", $all_po_lay_id_array);
		/*if( count($all_po_lay_id)>0 )
		{
			$po_conds=" and a.po_break_down_id in($all_po_lay_id)";
		}*/


		$booking_no_fin_qnty_array=array();
		$booking_sql=sql_select("SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0   $po_cond4");
		foreach($booking_sql as $vals)
		{
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["booking_no"]=$vals[csf("booking_no")];
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];

		}
		$batch_sql=sql_select("SELECT id,booking_no,batch_no,color_id from pro_batch_create_mst where status_active=1 and is_deleted=0 and booking_no is not null group by id,booking_no,batch_no,color_id ");
		foreach($batch_sql as $rows)
		{
			//$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
			if(!$duplicate_batch[$rows[csf("batch_no")]])
			{
				if($batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=="")
				{
					$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
				}
				else
				{
					$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]].=', '.$rows[csf("batch_no")];

				}
				
				$duplicate_batch[$rows[csf("batch_no")]]=trim($rows[csf("batch_no")]);
			}
			$batch_mst_id_arr[$rows[csf("booking_no")]]=$rows[csf("id")];
		}
		/*echo "<pre>";
		 print_r($batch_mst_id_arr);die;*/
		 
		 $issue_sql=sql_select("SELECT po_breakdown_id,quantity,color_id from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=18 ");
		 foreach($issue_sql as $values)
		 {
		 	//$issue_qnty_arr[$values[csf("batch_id")]]+=$values[csf("issue_qnty")];
		 	$issue_qnty_arr[$values[csf("po_breakdown_id")]][$values[csf("color_id")]]+=$values[csf("quantity")];
		 }
		$po_cond5=str_replace("po_id_string", "b.po_break_down_id", $po_cond); 
		$result_consumtion=array();
		$sql_consumtiont_qty=sql_select(" SELECT a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id  $po_cond5  GROUP BY a.job_no, a.body_part_id");

		foreach($sql_consumtiont_qty as $row_consum)
		{

			$result_consumtion[$row_consum[csf('job_no')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
		}
		unset($sql_consumtiont_qty); 


		 
		
		if(count($production_main_array)==0)
		{
			echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px">No Data Found.</div>'; die;
		} 
		ob_start();	
		
		?>
		 <style type="text/css">
		 	table thead tr th, table tr td {word-wrap: break-word;word-break: break-all;}
		 </style>
        <div>
        	<table width="2650" cellspacing="0" >
        		
        		<tr style="border:none;">
        			<td colspan="24" align="center" style="border:none; font-size:14px;">
        				<strong style="font-size: 24px;">
        					<? 
        					$comp_names=""; 
        					foreach(explode(",",$cbo_work_company_name) as $vals) 
        					{
        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
        					}
        					echo $comp_names;
        					 ?>
        				</strong>                                
        			</td>
        		</tr>
        		<tr class="form_caption" style="border:none;">
        			<td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
        				Daily RMG Production Report v2
        			</strong></td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold" >
        				<?
        				$dates=str_replace("'","",trim($txt_production_date));
        				if($dates)
        				{
        					echo "Date ".change_date_format($dates)  ;
        				}
        				?>
        			</td>
        		</tr>
        	</table>
        </div>
			<div>
				<table width="3245" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
					<thead>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Working Company</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Location</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Floor</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer Name</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Job No</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Style Reff</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Ship Status</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Ship Date</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order No</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Color Qty</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>F.Booking No</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="5" width="400"><p>Fabric Status</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Lay Status</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Cutting Status</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="320"><p>Input Status</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Sewing Output</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Finishing</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="5" ><p>Export</p></th>
					</tr>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Color Name</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>F.Fab. Req.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Fin. Fab. Issued</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>F.Issued Balance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Possible Cut Qty</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Lay.Balance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Cut.Balance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order-Input Balance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Inhand Qty</p></th>

						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Input -Sewing Balance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Sewing -Fini. Balance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order - Exfactory Balance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  ><p>Sewing - Exfactory Balance</p></th>

						
					</tr>
					</thead>
				</table>
					<div style="max-height:400px; overflow-y:scroll; width:3265px" id="scroll_body">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3245" rules="all" id="table_body" >
					<?
						$k=1;
						$jj=1;	
						$gr_order_qnty=0;
						$gr_req=0;
						$gr_iss=0;
						$gr_iss_bal=0;
						$gr_pos_cut=0;
						$gr_today_cut=0;
						$gr_cut_bal=0;
						$gr_total_lay=0;
						$gr_today_lay=0;
						$gr_lay_bal=0;
						$gr_total_cut=0;

						$gr_today_sewing=0;
						$gr_total_sewing=0;
						$gr_inh_bal=0;
						$gr_inh_qty=0;

						$gr_today_output = 0;
						$gr_total_output = 0;
						$gr_input_sewing_balance = 0;

						$gr_today_finishing = 0;
						$gr_total_finishing = 0;
						$gr_sewing_fin_balance = 0;

						$gr_today_export = 0;
						$gr_total_export = 0;
						$gr_order_xfact_balance = 0;
						$gr_sewing_xfact_balance = 0;
						$gr_ex_fact_fob_val = 0;
					
						foreach($production_main_array as $style_id=>$job_data)
						{				
						$style_wise_order_qty = 0;		
						$style_wise_fab_req = 0;		
						$style_wise_fab_issued = 0;		
						$style_wise_fab_issued_balance = 0;	

						$style_wise_fab_posible_cut_qty = 0;		
						$style_wise_cut_today = 0;		
						$style_wise_cut_total = 0;		
						$style_wise_cut_balance = 0;

						$style_wise_lay_today = 0;		
						$style_wise_lay_total = 0;		
						$style_wise_lay_balance = 0;


						$style_wise_input_today = 0;		
						$style_wise_input_total = 0;		
						$style_wise_input_balance = 0;		
						$style_wise_inhand_qty = 0;	

						$style_wise_today_output = 0;
						$style_wise_total_output = 0;
						$style_wise_input_sewing_balance = 0;

						$style_wise_today_finishing = 0;
						$style_wise_total_finishing = 0;
						$style_wise_sewing_fin_balance = 0;

						$style_wise_today_export = 0;
						$style_wise_total_export = 0;
						$style_wise_order_xfact_balance = 0;
						$style_wise_sewing_xfact_balance = 0;
						$style_wise_ex_fact_fob_val = 0;

						foreach($job_data as $job_id=>$po_data)
						{
							
							foreach($po_data as $po_id=>$item_data)
							{
								$order_wise_subtotal = 0;
								$order_wise_today_cutting=0;
								$order_wise_total_cutting=0;
								$order_wise_cut_balance=0;

								$order_wise_today_lay=0;
								$order_wise_total_lay=0;
								$order_wise_lay_balance=0;

								$order_wise_today_input=0;
								$order_wise_total_input=0;
								$order_wise_input_balance=0;
								$order_wise_inhand_qty=0;
								// fabric status sum
								$order_wise_fab_req = 0;
								$order_wise_fin_fab_req = 0;
								$order_wise_fab_issued_balance = 0;
								$order_wise_fab_possible_qty = 0;

								// sewing output
								$order_wise_today_output = 0;
								$order_wise_total_output = 0;
								$order_wise_input_sewing_balance = 0;

								$order_wise_today_finishing = 0;
								$order_wise_total_finishing = 0;
								$order_wise_sewing_fin_balance = 0;

								$order_wise_today_export = 0;
								$order_wise_total_export = 0;
								$order_wise_order_exfact_balance = 0;
								$order_wise_sewing_exfact_balance = 0;
								$order_wise_ex_fact_fob_val = 0;

								foreach($item_data as $item_id=>$color_data)
								{
									foreach($color_data as $color_id=>$row)
									{
										$color_wise_today_cutting=0;
										$color_wise_total_cutting=0;
										$color_wise_today_sewing_input =0;
										$color_wise_total_sewing_input=0;
										$color_wise_inh=0;
										$pp=0;
										$fin_req = 0;

											$today_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['today_ex_fac'];
											$total_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['total_ex_fac'];									
											$fin_req=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
											$issue_qty=$issue_qnty_arr[$po_id][$color_id];

											$req_issue_bal=$fin_req-$issue_qty;
											$possible_cut_pcs=$issue_qty/$result_consumtion[$job_id];

										 	$today_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["today_lay"];
										 	$total_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_lay"]; 



											$today_cutting_qnty= $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_cutting"];
											$total_cutting_qnty= $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_cutting"];
											$color_wise_today_cutting+=$today_cutting_qnty;
											$color_wise_total_cutting+=$total_cutting_qnty;

											$today_sewing_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
											$total_sewing_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_input"];
											$today_sewing_output = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_output"];

											$total_sewing_output = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_output"];

											$today_finishing = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id][0]["today_finishing"];

											$total_finishing = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id][0]["total_finishing"];

											$color_wise_today_sewing_input += $today_sewing_input;
											$color_wise_total_sewing_input += $total_sewing_input;

											// order wise

											$order_wise_today_cutting+=$today_cutting_qnty;
											$order_wise_total_cutting+=$total_cutting_qnty;

											$order_wise_today_lay+=$today_lay_qnty;
											$order_wise_total_lay+=$total_lay_qnty;

											$order_wise_today_input += $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
											$order_wise_total_input += $total_sewing_input;										

											$order_wise_inhand_qty += $total_cutting_qnty-$total_sewing_input;

											$order_wise_today_output += $today_sewing_output;
											$order_wise_total_output += $total_sewing_output;

											$order_wise_today_finishing += $today_finishing;
											$order_wise_total_finishing += $total_finishing;

											$order_wise_today_export += $today_ex_fac;
											$order_wise_total_export += $total_ex_fac;

											// style wise
											
											$style_wise_fab_req += $fin_req;		
											$style_wise_fab_issued += $issue_qty;		
											$style_wise_fab_issued_balance += $req_issue_bal;		
											$style_wise_fab_posible_cut_qty += $possible_cut_pcs;

											$style_wise_cut_today += $today_cutting_qnty;		
											$style_wise_cut_total += $total_cutting_qnty;

											$style_wise_lay_today += $today_lay_qnty;		
											$style_wise_lay_total += $total_lay_qnty;

											$style_wise_input_today += $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];;		
											$style_wise_input_total += $total_sewing_input;													
											$style_wise_inhand_qty += $order_wise_inhand_qty;

											$style_wise_today_output += $today_sewing_output;
											$style_wise_total_output += $total_sewing_output;

											$style_wise_today_finishing += $today_finishing;
											$style_wise_total_finishing += $total_finishing;

											$style_wise_today_export += $today_ex_fac;
											$style_wise_total_export += $total_ex_fac;

											// grand total
											$gr_today_output += $today_sewing_output;
											$gr_total_output += $total_sewing_output;

											$gr_today_finishing += $today_finishing;
											$gr_total_finishing += $total_finishing;

											$gr_today_export += $today_ex_fac;
											$gr_total_export += $total_ex_fac;
											
										 
											if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
												<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
														<?
														$jj++;
													
														$order_quantitys=$order_qnty_array[$po_id][$item_id][$color_id];
														$order_wise_subtotal += $order_quantitys;
														$style_wise_order_qty += $order_quantitys;
														$gr_order_qnty+=$order_quantitys;
														$gr_req+=$fin_req;
														$gr_iss+=$issue_qty;
														$gr_iss_bal+=$req_issue_bal;
														$gr_pos_cut+=$possible_cut_pcs;

														?>
													 
														<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>

														<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $company_library[$row["working_company_id"]]; ?></p></td>

														<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $location_library[$row["location_id"]]; ?></p></td>
														<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $floor_arr[$row["floor_id"]]; ?></p></td>

														<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $buyer_library[$row["buyer_name"]]; ?></p></td>

														<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $job_id; ?></p></td>

														<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo implode(PHP_EOL, str_split($style_id,10));?></p></td>
														 
														<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["shiping_status"];?></p></td>
														<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["pub_shipment_date"];?></p></td>
														<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo implode(PHP_EOL, str_split($row["po_number"],10));?></p></td>

														<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="right"    width="80"><p><? echo $order_quantitys; ?></p></td> 
														<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"] ;?></p></td>

														<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $color_Arr_library[$color_id];?></p></td>
														<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($fin_req,2);?></p></td>
														<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><a href="##" onclick="openmypage_fab_issue(<? echo $po_id;?>,<? echo $color_id?>,<? echo 2 ?>, 'fab_issue_popup');" > <p><? echo number_format($issue_qty,2);?></p></a></td>
														<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($req_issue_bal,2);?></p></td>
														<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($possible_cut_pcs,2);?></p></td>
													<?
													$order_wise_fab_req += $fin_req;
													$order_wise_fin_fab_req += $issue_qty;
													$order_wise_fab_issued_balance += $req_issue_bal;
													$order_wise_fab_possible_qty += $possible_cut_pcs;
													
													$gr_today_cut+=$today_cutting_qnty;
													$gr_total_cut+=$total_cutting_qnty;

													$gr_today_lay+=$today_lay_qnty;
													$gr_total_lay+=$total_lay_qnty;

													$gr_today_sewing+=$cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
													$gr_total_sewing+=$total_sewing_input;
													?>
													<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,0,'A','production_qnty_popup','Today Lay','600','300');"><p><? echo $today_lay_qnty;?></p></a></td>

													<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,0,'B','production_qnty_popup','Total Lay','730','300');"><p><? echo $total_lay_qnty;?></p></a></td>

													<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $lay_balance = $order_quantitys-$total_lay_qnty;?></p></td>
														

														<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"> 
														<a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'A','production_qnty_popup','Today Cutting','600','300');">
														<p><? echo $today_cutting_qnty;?></p> </a></td>
 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'B','production_qnty_popup','Total Cutting','730','300');"> <p><? echo $total_cutting_qnty;?></p></a></a></td>
														
															<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $cut_balance = $order_quantitys-$total_cutting_qnty;?></p></td>
															<?
															$gr_lay_bal+=$lay_balance;
															$order_wise_lay_balance += $lay_balance;
															$style_wise_lay_balance += $lay_balance;


															$gr_cut_bal+=$cut_balance;
															$order_wise_cut_balance += $cut_balance;

															$style_wise_cut_balance += $cut_balance;										 

														
														?>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'A','production_qnty_popup','Today Sewing Input','800','300');"><p><? echo $today_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];?></p></a></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'B','production_qnty_popup','Total Sewing Input','730','300');"><p><? echo $total_sewing_input;?></a></p></td>
														
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo 	$input_balance = $order_quantitys-$total_sewing_input;?></p></td>
														<?
														$gr_inh_bal+=$input_balance;
														$order_wise_input_balance += $input_balance;											 
														$style_wise_input_balance += $input_balance;
					 
														
														?>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $inh_qty= $total_cutting_qnty-$total_sewing_input;$gr_inh_qty+=$inh_qty;$color_wise_inh+=$inh_qty;?></p></td>		
														 					 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,5,'A','production_qnty_popup','Today Sewing Output','800','300');"><p><? echo $today_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_output"];?></p></a></td>								 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,5,'B','production_qnty_popup','Total Sewing Output','730','300');"><p><? echo $total_sewing_output;?></p></a></td>								 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="80"><p><? echo $input_sewing_balance = $total_sewing_input-$total_sewing_output;?></p></td>								 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,8,'A','production_qnty_popup','Today Finish','800','300');"><p><? echo $today_finishing;?></p></a></td>								 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,8,'B','production_qnty_popup','Total Finish','730','300');"><p><? echo $total_finishing;?></p></a></td>							 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $sewing_fin_balance = $total_sewing_output-$total_finishing;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $today_ex_fac;?></p></td>								 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_ex_fac_total(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>, 'total_exfac_action');" ><p><? echo $total_ex_fac;?></p></a></td>							 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $order_xfact= $order_quantitys-$total_ex_fac;?></p></td>								 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" ><p><? echo $sewing_xfact= $total_sewing_output-$total_ex_fac;?></p></td>								 
																					 
													</tr>	
											<?											
											
											$order_wise_input_sewing_balance += $input_sewing_balance;
											$order_wise_sewing_fin_balance += $sewing_fin_balance;

											$style_wise_input_sewing_balance += $input_sewing_balance;
											$style_wise_sewing_fin_balance += $sewing_fin_balance;

											$gr_input_sewing_balance += $input_sewing_balance;	
											$gr_sewing_fin_balance += $sewing_fin_balance;

											$order_wise_order_exfact_balance += $order_xfact;
											$order_wise_sewing_exfact_balance += $sewing_xfact;
											$order_wise_ex_fact_fob_val += $ex_fact_fob_val;

											$style_wise_order_xfact_balance += $order_xfact;
											$style_wise_sewing_xfact_balance += $sewing_xfact;
											$style_wise_ex_fact_fob_val += $ex_fact_fob_val;
											
											$gr_order_xfact_balance += $order_xfact;
											$gr_sewing_xfact_balance += $sewing_xfact;
											$gr_ex_fact_fob_val += $ex_fact_fob_val;
											$k++;										
										//}									
										
										
									}

								}
								?>
								<tr bgcolor="#E4E4E4">
									<td colspan="9" align="right"><b>Order Wise Sub Total</b></td>
									<td align="right"><b><? echo $order_wise_subtotal;?></b></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="right"><b><? echo number_format($order_wise_fab_req,2);?></b></td>
									<td align="right"><b><? echo number_format($order_wise_fin_fab_req,2);?></b></td>
									<td align="right"><b><? echo number_format($order_wise_fab_issued_balance,2);?></b></td>
									<td align="right"><b><? echo number_format($order_wise_fab_possible_qty,2);?></b></td>

									<td align="right"><b><? echo $order_wise_today_lay;?></b></td>
									<td align="right"><b><? echo $order_wise_total_lay;?></b></td>
									<td align="right"><b><? echo $order_wise_lay_balance; ?></b></td>

									<td align="right"><b><? echo $order_wise_today_cutting;?></b></td>
									<td align="right"><b><? echo $order_wise_total_cutting;?></b></td>
									<td align="right"><b><? echo $order_wise_cut_balance; ?></b></td>
									<td align="right"><b><? echo $order_wise_today_input;?></b></td>
									<td align="right"><b><? echo $order_wise_total_input;?></b></td>
									<td align="right"><b><? echo $order_wise_input_balance;?></b></td>
									<td align="right"><b><? echo $order_wise_inhand_qty;?></b></td>
									<!-- =================********=================-->
									<td align="right"><b><? echo $order_wise_today_output;?></b></td>
									<td align="right"><b><? echo $order_wise_total_output;?></b></td>
									<td align="right"><b><? echo $order_wise_input_sewing_balance;?></b></td>

									<td align="right"><b><? echo $order_wise_today_finishing;?></b></td>
									<td align="right"><b><? echo $order_wise_total_finishing;?></b></td>
									<td align="right"><b><? echo $order_wise_sewing_fin_balance;?></b></td>

									<td align="right"><b><? echo $order_wise_today_export;?></b></td>
									<td align="right"><b><? echo $order_wise_total_export;?></b></td>
									<td align="right"><b><? echo $order_wise_order_exfact_balance;?></b></td>
									<td align="right"><b><? echo $order_wise_sewing_exfact_balance;?></b></td>
									
								</tr>
								<?
							}
						
						}
						
						?>
						<tr bgcolor="#E4E4E4">
							<td colspan="9" align="right"><b>Style Wise Sub Total</b></td>
							<td align="right"><b><? echo $style_wise_order_qty;?></b></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><b><? echo number_format($style_wise_fab_req,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_issued,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_issued_balance,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_posible_cut_qty,2);?></b></td>
							<td align="right"><b><? echo $style_wise_lay_today;?></b></td>
							<td align="right"><b><? echo $style_wise_lay_total;?></b></td>
							<td align="right"><b><? echo $style_wise_lay_balance;?></b></td>

							<td align="right"><b><? echo $style_wise_cut_today;?></b></td>
							<td align="right"><b><? echo $style_wise_cut_total;?></b></td>
							<td align="right"><b><? echo $style_wise_cut_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_input_today;?></b></td>
							<td align="right"><b><? echo $style_wise_input_total;?></b></td>
							<td align="right"><b><? echo $style_wise_input_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_inhand_qty;?></b></td>
						
							<td align="right"><b><? echo $style_wise_today_output;?></b></td>
							<td align="right"><b><? echo $style_wise_total_output;?></b></td>
							<td align="right"><b><? echo $style_wise_input_sewing_balance;?></b></td>

							<td align="right"><b><? echo $style_wise_today_finishing;?></b></td>
							<td align="right"><b><? echo $style_wise_total_finishing;?></b></td>
							<td align="right"><b><? echo $style_wise_sewing_fin_balance;?></b></td>

							<td align="right"><b><? echo $style_wise_today_export;?></b></td>
							<td align="right"><b><? echo $style_wise_total_export;?></b></td>
							<td align="right"><b><? echo $style_wise_order_xfact_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_sewing_xfact_balance;?></b></td>
							
						</tr>	
						<?
						$gr_inh_qty;
					}

					?>
					
										
					</table>

									  
				</div>				 

			</div>
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3245" rules="all"  >
				<tr bgcolor="#E4E4E4"  >  
					<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p> &nbsp;</p></td>

					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p>&nbsp;</p></td>
					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p>&nbsp;</p></td>

					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p>&nbsp;</p></td>

					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p>&nbsp;</p></td>

					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p>&nbsp;</p></td>

					<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p>&nbsp;</p></td>
					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p>&nbsp;</p></td>
					<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><strong>Grand Total</strong></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="80"    align="right"><b><? echo $gr_order_qnty;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_req,2);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_iss,2);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_iss_bal,2);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_pos_cut,2);?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_lay;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_lay;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_lay_bal;?></b></td>

					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_cut;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_cut;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_cut_bal;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_sewing;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_sewing;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_inh_bal;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_inh_qty;?></b></td>	
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_output;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_output;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_input_sewing_balance;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_finishing;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_finishing;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_sewing_fin_balance;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_export;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_export;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_order_xfact_balance;?></b></td>
					<td style="word-wrap: break-word;word-break: break-all;" align="right"><b><?php echo $gr_sewing_xfact_balance;?></b></td>	
				</tr>	
				
			</table>	
		 </div> 
        <?
	 
	}
	else if($type ==66) // back up 30-05-2019
	{

	 	 	// button 6 for chaity    
	 		$str_po_cond="";
	 		$str_po_cond2="";
	 		$str_loc_cond_lay="";
	 		$_SESSION["txt_production_date"]="";
	  		$_SESSION["txt_production_date"]=$txt_production_date;
			$_SESSION["work_comp"]="";
			$_SESSION["work_comp"]=$cbo_work_company_name;
			if($cbo_work_company_name!=0) $company_cond=" and d.serving_company in($cbo_work_company_name)";		
			if($cbo_work_company_name!=0) $company_cond_lay=" and d.working_company_id in($cbo_work_company_name)";		
			if($cbo_work_company_name!=0) $company_cond_lay_a=" and a.working_company_id in($cbo_work_company_name)";		
			if($cbo_work_company_name!=0) $company_cond_delv=" and d.delivery_company_id in($cbo_work_company_name)";		
			if(str_replace("'", "",$cbo_buyer_name)!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";
			if(str_replace("'", "",$cbo_buyer_name)!=0) $str_po_cond2.=" and a.buyer_name=$cbo_buyer_name";
			$cbo_location_name=str_replace("'", "", $cbo_location_name);
			if($cbo_location_name) $str_po_cond.=" and d.location_id in($cbo_location_name)";
			if($cbo_location_name) $str_po_cond2.=" and d.location in($cbo_location_name)";
			if($cbo_location_name) $str_loc_cond_lay.=" and a.location_id in($cbo_location_name)";
			$cbo_floor_name=str_replace("'", "", $cbo_floor_name);
			if($cbo_floor_name) $str_po_cond.=" and d.floor_id in($cbo_floor_name)";
			if($cbo_floor_name) $str_po_cond2.=" and d.floor_id in($cbo_floor_name)";
			if(str_replace("'", "", $cbo_year))
			{
				if($db_type==0)
				{
					$str_po_cond .=" and year(a.insert_date)=$cbo_year";
					$str_po_cond2 .=" and year(a.insert_date)=$cbo_year";
				}
				else
				{
					$str_po_cond .=" and to_char(a.insert_date,'YYYY')=$cbo_year";
					$str_po_cond2 .=" and to_char(a.insert_date,'YYYY')=$cbo_year";
				}	
			}
			
			$hidden_job_id=str_replace("'","",$hidden_job_id);
			if($hidden_job_id!="")
			{
				$str_po_cond.=" and a.id in($hidden_job_id)";
				$str_po_cond2.=" and a.id in($hidden_job_id)";
			}

			$hidden_order_id=str_replace("'","",$hidden_order_id);
			if($hidden_order_id)
			{
				$str_po_cond.=" and b.id in($hidden_order_id)";
				$str_po_cond2.=" and b.id in($hidden_order_id)";
			} 
			$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);
			if($cbo_shipping_status)
			{
				$str_po_cond.=" and b.shiping_status in($cbo_shipping_status)";
				$str_po_cond2.=" and b.shiping_status in($cbo_shipping_status)";
			} 
			 


			$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
			$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);
			$prod_sqls="SELECT  po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date group by po_break_down_id ";
			$prod_po_id_array=array();
			foreach(sql_select($prod_sqls) as $val)
			{
				$prod_po_id_array[$val[csf("po_break_down_id")]]=$val[csf("po_break_down_id")];
			}

			$prod_sqls_ex="SELECT  po_break_down_id from pro_ex_factory_mst where status_active=1 and is_deleted=0 and ex_factory_date=$txt_production_date group by po_break_down_id ";

			foreach(sql_select($prod_sqls_ex) as $val)
			{
				$prod_po_id_array[$val[csf("po_break_down_id")]]=$val[csf("po_break_down_id")];
			}
			
			$today_lay_sql="SELECT c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.job_no=a.job_no   and d.id=e.mst_id  and a.job_no=c.job_no_mst and e.mst_id=f.mst_id and f.order_id=b.id and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond $company_cond_lay group by c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,d.floor_id,a.buyer_name,b.pub_shipment_date,b.shiping_status " ; 

			 
			$po_id_array=array();
			$col_id_array=array();
			$production_main_array=array();
			foreach(sql_select($today_lay_sql) as $row) 
			{
				$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
				$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["floor_id"]=$row[csf("floor_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];	

				$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];
				unset($prod_po_id_array[$row[csf("po_id")]]);

			}
			$prod_po_ids=implode(",", $prod_po_id_array);
			if($prod_po_ids)
			{
				$po_conds2=" and b.id in($prod_po_ids)";
			}



			/*
			 $today_lay_sql2="SELECT c.color_number_id,b.id as po_id,a.style_ref_no,a.job_no, c.item_number_id,c.color_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,a.buyer_name,b.pub_shipment_date,b.shiping_status  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.job_no=a.job_no      and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0    $po_conds2   group by c.color_number_id,b.id  ,a.style_ref_no,a.job_no, c.item_number_id,c.color_number_id,d.cutting_no,b.po_number,d.working_company_id,d.location_id,a.buyer_name,b.pub_shipment_date,b.shiping_status " ; 
			 
			//$po_id_array=array();
			//$col_id_array=array();
			//$production_main_array=array();
			foreach(sql_select($today_lay_sql2) as $row) 
			{
				$po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];
				$col_id_array[$row[csf("color_number_id")]]=$row[csf("color_number_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["working_company_id"]=$row[csf("working_company_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["location_id"]=$row[csf("location_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];	

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["pub_shipment_date"]=change_date_format($row[csf("pub_shipment_date")]);	

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$shipment_status[$row[csf("shiping_status")]];	

				$po_item_col_check[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]=$row[csf("po_id")];

			}*/




			$po_ids=implode(",", $po_id_array);
			$color_ids=implode(",", $col_id_array);
			if(!$po_ids) $po_ids=0;
			if(!$color_ids) $color_ids=0;

			$po_cond="";
			if(count($po_id_array)>999)
			{
				$chunk_arr=array_chunk($po_id_array,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",", $val);
					if($po_cond=="") $po_cond.=" and ( po_id_string in ($ids) ";
					else
						$po_cond.=" or   po_id_string in ($ids) "; 
				}
				$po_cond.=") ";

			}
			else
			{
				$po_cond.=" and po_id_string in ($po_ids) ";
			}
			//echo $po_cond;die;
			

			
			
			
			$order_qnty_array=array();
			$po_cond1=str_replace("po_id_string", "po_break_down_id", $po_cond);
			 $order_qnty_sqls="SELECT po_break_down_id,color_number_id,order_quantity,item_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $po_cond1 ";
			 foreach(sql_select($order_qnty_sqls) as $values)
			 {
			 	$order_qnty_array[$values[csf("po_break_down_id")]][$values[csf("item_number_id")]][$values[csf("color_number_id")]]+=$values[csf("order_quantity")];

			 }



	  
			 
			
			 $po_cond2=str_replace("po_id_string", "b.id", $po_cond);
			 $order_sql=sql_select("SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no  ,   c.color_number_id, sum(c.order_quantity) as order_quantity, 

			sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_cutting ,
			sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

			sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_input ,
			sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input ,

			sum(case when d.production_type=5 and e.production_type=5 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_sewing_output ,
			sum(case when d.production_type=5 and e.production_type=5   then e.production_qnty else 0 end ) as total_sewing_output ,

			sum(case when d.production_type=8 and e.production_type=8 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as today_finishing ,
			sum(case when d.production_type=8 and e.production_type=8   then e.production_qnty else 0 end ) as total_finishing 


			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
			where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $str_po_cond2 $po_cond2  $company_cond and d.production_type in(1,4,5,8) 
			group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  ,  c.color_number_id,e.cut_no ");



			 //echo $order_sql;die;

			foreach($order_sql as $vals)
			{
				$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_cutting"]+=$vals[csf("today_cutting")];			 

				$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_cutting"]+=$vals[csf("total_cutting")];
				 

				$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

				$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

				 
				$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["today_sewing_output"]+=$vals[csf("today_sewing_output")];

				$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]]["total_sewing_output"]+=$vals[csf("total_sewing_output")];


				$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["today_finishing"]+=$vals[csf("today_finishing")];

				$cutting_sewing_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][0]["total_finishing"]+=$vals[csf("total_finishing")];

			}
			//echo "<pre>";
			//print_r($cutting_sewing_data);die;
		  $po_cond3=str_replace("po_id_string", "c.order_id", $po_cond); 	
		  $lay_sqls="SELECT  a.job_no,c.order_id, b.gmt_item_id,b.color_id,sum( case when a.entry_date=$txt_production_date then  c.size_qty else 0 end) as today_lay,sum(c.size_qty) as total_lay from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond3  $company_cond_lay_a  $str_loc_cond_lay  group by a.job_no,c.order_id, b.gmt_item_id,b.color_id ";

			$lay_qnty_array=array();
			foreach(sql_select($lay_sqls) as $vals)
			{
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["today_lay"]+=$vals[csf("today_lay")];
				$lay_qnty_array[$vals[csf("job_no")]][$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("total_lay")];
			}


			$po_cond4=str_replace("po_id_string", "a.po_break_down_id", $po_cond); 	
			$ex_factory_arr=array();
			$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id,c.color_number_id, sum(CASE WHEN a.entry_form!=85 and ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS today_ex_fac , sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id  and  a.id=b.mst_id and b.color_size_break_down_id=c.id   and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond_delv  $po_cond4  group by a.po_break_down_id, a.item_number_id,c.color_number_id");
			foreach($ex_factory_data as $exRow)
			{
				$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
				$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['total_ex_fac']=+$exRow[csf('total_ex_fac')];
			}
			//print_r($ex_factory_arr);

			  
			 
			//$all_po_lay_id=implode(",", $all_po_lay_id_array);
			/*if( count($all_po_lay_id)>0 )
			{
				$po_conds=" and a.po_break_down_id in($all_po_lay_id)";
			}*/


			$booking_no_fin_qnty_array=array();
			$booking_sql=sql_select("SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0   $po_cond4");
			foreach($booking_sql as $vals)
			{
				$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["booking_no"]=$vals[csf("booking_no")];
				$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];

			}
			$batch_sql=sql_select("SELECT id,booking_no,batch_no,color_id from pro_batch_create_mst where status_active=1 and is_deleted=0 and booking_no is not null group by id,booking_no,batch_no,color_id ");
			foreach($batch_sql as $rows)
			{
				//$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
				if(!$duplicate_batch[$rows[csf("batch_no")]])
				{
					if($batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=="")
					{
						$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
					}
					else
					{
						$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]].=','.$rows[csf("batch_no")];

					}
					
					$duplicate_batch[$rows[csf("batch_no")]]=trim($rows[csf("batch_no")]);
				}
				$batch_mst_id_arr[$rows[csf("booking_no")]]=$rows[csf("id")];
			}
			/*echo "<pre>";
			 print_r($batch_mst_id_arr);die;*/
			 
			 $issue_sql=sql_select("SELECT po_breakdown_id,quantity,color_id from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=18 ");
			 foreach($issue_sql as $values)
			 {
			 	//$issue_qnty_arr[$values[csf("batch_id")]]+=$values[csf("issue_qnty")];
			 	$issue_qnty_arr[$values[csf("po_breakdown_id")]][$values[csf("color_id")]]+=$values[csf("quantity")];
			 }
			$po_cond5=str_replace("po_id_string", "b.po_break_down_id", $po_cond); 
			$result_consumtion=array();
			$sql_consumtiont_qty=sql_select(" SELECT a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id  $po_cond5  GROUP BY a.job_no, a.body_part_id");

			foreach($sql_consumtiont_qty as $row_consum)
			{

				$result_consumtion[$row_consum[csf('job_no')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
			}
			unset($sql_consumtiont_qty); 


			 
			
			if(count($production_main_array)==0)
			{
				echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px">No Data Found.</div>'; die;
			} 
			ob_start();	
			
			?>
			 
	        <div>
	        	<table width="2650" cellspacing="0" >
	        		<tr class="form_caption" style="border:none;">
	        			<td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
	        				Daily RMG Production Report v2
	        			</strong></td>
	        		</tr>
	        		<tr style="border:none;">
	        			<td colspan="24" align="center" style="border:none; font-size:14px;">
	        				<strong>
	        					Working Company Name : <? 
	        					$comp_names=""; 
	        					foreach(explode(",",$cbo_work_company_name) as $vals) 
	        					{
	        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
	        					}
	        					echo $comp_names;
	        					 ?>
	        				</strong>                                
	        			</td>
	        		</tr>
	        		<tr style="border:none;">
	        			<td colspan="24" align="center" style="border:none;font-size:12px; font-weight:bold" >
	        				<?
	        				$dates=str_replace("'","",trim($txt_production_date));
	        				if($dates)
	        				{
	        					echo "Date ".change_date_format($dates)  ;
	        				}
	        				?>
	        			</td>
	        		</tr>
	        	</table>
				<div>
					<table width="3165" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
						<thead>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Working Company</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Location</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Floor</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer Name</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Job No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Style Reff</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Ship Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Ship Date</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Color Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>F.Booking No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Batch No</p></th>	

							<th style="word-wrap: break-word;word-break: break-all;"  colspan="5" width="400"><p>Fabric Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Lay Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Cutting Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="320"><p>Input Status</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Sewing Output</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="240"><p>Finishing</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="320"><p>Export</p></th>
						</tr>
						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Color Name</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>F.Fab. Req.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Fin. Fab. Issued</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>F.Issued Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Possible Cut Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Lay.Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Cut.Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order-Input Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Inhand Qty</p></th>

							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Input -Sewing Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Sewing -Fini. Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order - Exfactory Balance</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Sewing - Exfactory Balance</p></th>

						</tr>
							
							  
						   
						</thead>
					</table>
					<div style="max-height:400px; overflow-y:scroll; width:3185px" id="scroll_body">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3165" rules="all" id="table_body" >
						<?
						$k=1;
						$jj=1;	
						$gr_order_qnty=0;
						$gr_req=0;
						$gr_iss=0;
						$gr_iss_bal=0;
						$gr_pos_cut=0;
						$gr_today_cut=0;
						$gr_cut_bal=0;
						$gr_total_lay=0;
						$gr_today_lay=0;
						$gr_lay_bal=0;
						$gr_total_cut=0;

						$gr_today_sewing=0;
						$gr_total_sewing=0;
						$gr_inh_bal=0;
						$gr_inh_qty=0;

						$gr_today_output = 0;
						$gr_total_output = 0;
						$gr_input_sewing_balance = 0;

						$gr_today_finishing = 0;
						$gr_total_finishing = 0;
						$gr_sewing_fin_balance = 0;

						$gr_today_export = 0;
						$gr_total_export = 0;
						$gr_order_xfact_balance = 0;
						$gr_sewing_xfact_balance = 0;
						
						foreach($production_main_array as $style_id=>$job_data)
						{				
							$style_wise_order_qty = 0;		
							$style_wise_fab_req = 0;		
							$style_wise_fab_issued = 0;		
							$style_wise_fab_issued_balance = 0;	

							$style_wise_fab_posible_cut_qty = 0;		
							$style_wise_cut_today = 0;		
							$style_wise_cut_total = 0;		
							$style_wise_cut_balance = 0;

							$style_wise_lay_today = 0;		
							$style_wise_lay_total = 0;		
							$style_wise_lay_balance = 0;


							$style_wise_input_today = 0;		
							$style_wise_input_total = 0;		
							$style_wise_input_balance = 0;		
							$style_wise_inhand_qty = 0;	

							$style_wise_today_output = 0;
							$style_wise_total_output = 0;
							$style_wise_input_sewing_balance = 0;

							$style_wise_today_finishing = 0;
							$style_wise_total_finishing = 0;
							$style_wise_sewing_fin_balance = 0;

							$style_wise_today_export = 0;
							$style_wise_total_export = 0;
							$style_wise_order_xfact_balance = 0;
							$style_wise_sewing_xfact_balance = 0;

							foreach($job_data as $job_id=>$po_data)
							{
								
								foreach($po_data as $po_id=>$item_data)
								{
									$order_wise_subtotal = 0;
									$order_wise_today_cutting=0;
									$order_wise_total_cutting=0;
									$order_wise_cut_balance=0;

									$order_wise_today_lay=0;
									$order_wise_total_lay=0;
									$order_wise_lay_balance=0;

									$order_wise_today_input=0;
									$order_wise_total_input=0;
									$order_wise_input_balance=0;
									$order_wise_inhand_qty=0;
									// fabric status sum
									$order_wise_fab_req = 0;
									$order_wise_fin_fab_req = 0;
									$order_wise_fab_issued_balance = 0;
									$order_wise_fab_possible_qty = 0;

									// sewing output
									$order_wise_today_output = 0;
									$order_wise_total_output = 0;
									$order_wise_input_sewing_balance = 0;

									$order_wise_today_finishing = 0;
									$order_wise_total_finishing = 0;
									$order_wise_sewing_fin_balance = 0;

									$order_wise_today_export = 0;
									$order_wise_total_export = 0;
									$order_wise_order_exfact_balance = 0;
									$order_wise_sewing_exfact_balance = 0;

									foreach($item_data as $item_id=>$color_data)
									{
										foreach($color_data as $color_id=>$row)
										{
											$color_wise_today_cutting=0;
											$color_wise_total_cutting=0;
											$color_wise_today_sewing_input =0;
											$color_wise_total_sewing_input=0;
											$color_wise_inh=0;
											$pp=0;
											$fin_req = 0;
											//foreach($cutting_data as $cutting_id=>$row)
											//{
												 

												$today_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['today_ex_fac'];
												$total_ex_fac=$ex_factory_arr[$po_id][$item_id][$color_id]['total_ex_fac'];									
												$fin_req=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
												//$issue_qty=$issue_qnty_arr[$batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]]];
												$issue_qty=$issue_qnty_arr[$po_id][$color_id];

												$req_issue_bal=$fin_req-$issue_qty;
												$possible_cut_pcs=$issue_qty/$result_consumtion[$job_id];

											 	$today_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["today_lay"];
											 	$total_lay_qnty=$lay_qnty_array[$job_id][$po_id][$item_id][$color_id]["total_lay"]; 



												$today_cutting_qnty= $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_cutting"];
												$total_cutting_qnty= $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_cutting"];
												$color_wise_today_cutting+=$today_cutting_qnty;
												$color_wise_total_cutting+=$total_cutting_qnty;

												$today_sewing_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
												$total_sewing_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_input"];
												$today_sewing_output = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_output"];

												$total_sewing_output = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["total_sewing_output"];

												$today_finishing = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id][0]["today_finishing"];

												$total_finishing = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id][0]["total_finishing"];

												$color_wise_today_sewing_input += $today_sewing_input;
												$color_wise_total_sewing_input += $total_sewing_input;

												// order wise

												$order_wise_today_cutting+=$today_cutting_qnty;
												$order_wise_total_cutting+=$total_cutting_qnty;

												$order_wise_today_lay+=$today_lay_qnty;
												$order_wise_total_lay+=$total_lay_qnty;

												$order_wise_today_input += $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
												$order_wise_total_input += $total_sewing_input;										

												$order_wise_inhand_qty += $total_cutting_qnty-$total_sewing_input;

												$order_wise_today_output += $today_sewing_output;
												$order_wise_total_output += $total_sewing_output;

												$order_wise_today_finishing += $today_finishing;
												$order_wise_total_finishing += $total_finishing;

												$order_wise_today_export += $today_ex_fac;
												$order_wise_total_export += $total_ex_fac;

												// style wise
												
												$style_wise_fab_req += $fin_req;		
												$style_wise_fab_issued += $issue_qty;		
												$style_wise_fab_issued_balance += $req_issue_bal;		
												$style_wise_fab_posible_cut_qty += $possible_cut_pcs;

												$style_wise_cut_today += $today_cutting_qnty;		
												$style_wise_cut_total += $total_cutting_qnty;

												$style_wise_lay_today += $today_lay_qnty;		
												$style_wise_lay_total += $total_lay_qnty;

												$style_wise_input_today += $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];;		
												$style_wise_input_total += $total_sewing_input;													
												$style_wise_inhand_qty += $order_wise_inhand_qty;

												$style_wise_today_output += $today_sewing_output;
												$style_wise_total_output += $total_sewing_output;

												$style_wise_today_finishing += $today_finishing;
												$style_wise_total_finishing += $total_finishing;

												$style_wise_today_export += $today_ex_fac;
												$style_wise_total_export += $total_ex_fac;

												// grand total
												$gr_today_output += $today_sewing_output;
												$gr_total_output += $total_sewing_output;

												$gr_today_finishing += $today_finishing;
												$gr_total_finishing += $total_finishing;

												$gr_today_export += $today_ex_fac;
												$gr_total_export += $total_ex_fac;
												
											 
												if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
														<?
														$jj++;
														
															$order_quantitys=$order_qnty_array[$po_id][$item_id][$color_id];
															$order_wise_subtotal += $order_quantitys;
															$style_wise_order_qty += $order_quantitys;
															$gr_order_qnty+=$order_quantitys;
															$gr_req+=$fin_req;
															$gr_iss+=$issue_qty;
															$gr_iss_bal+=$req_issue_bal;
															$gr_pos_cut+=$possible_cut_pcs;

															?>
														 
															<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>

															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $company_library[$row["working_company_id"]]; ?></p></td>

															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $location_library[$row["location_id"]]; ?></p></td>
															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $floor_arr[$row["floor_id"]]; ?></p></td>

															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $buyer_library[$row["buyer_name"]]; ?></p></td>

															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $job_id; ?></p></td>

															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $style_id;?></p></td>
															 
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["shiping_status"];?></p></td>
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["pub_shipment_date"];?></p></td>
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["po_number"];?></p></td>

															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="right"    width="80"><p><? echo $order_quantitys; ?></p></td> 
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"] ;?></p></td>
															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $batch_mst_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]][$color_id];?></p></td>
															<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $color_Arr_library[$color_id];?></p></td>


															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($fin_req,2);?></p></td>

															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><a href="##" onclick="openmypage_fab_issue(<? echo $po_id;?>,<? echo $color_id?>,<? echo 2 ?>, 'fab_issue_popup');" > <p><? echo number_format($issue_qty,2);?></p></a></td>
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($req_issue_bal,2);?></p></td>
															<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo number_format($possible_cut_pcs,2);?></p></td>
														<?
														$order_wise_fab_req += $fin_req;
														$order_wise_fin_fab_req += $issue_qty;
														$order_wise_fab_issued_balance += $req_issue_bal;
														$order_wise_fab_possible_qty += $possible_cut_pcs;
														
														$gr_today_cut+=$today_cutting_qnty;
														$gr_total_cut+=$total_cutting_qnty;

														$gr_today_lay+=$today_lay_qnty;
														$gr_total_lay+=$total_lay_qnty;

														$gr_today_sewing+=$cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];
														$gr_total_sewing+=$total_sewing_input;
														?>
														<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,0,'A','production_qnty_popup','Today Lay','600','300');"><p><? echo $today_lay_qnty;?></p></a></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,0,'B','production_qnty_popup','Total Lay','730','300');"><p><? echo $total_lay_qnty;?></p></a></td>

														<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $lay_balance = $order_quantitys-$total_lay_qnty;?></p></td>
															

															<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"> 
															<a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'A','production_qnty_popup','Today Cutting','600','300');">
															<p><? echo $today_cutting_qnty;?></p> </a></td>
	 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,1,'B','production_qnty_popup','Total Cutting','730','300');"> <p><? echo $total_cutting_qnty;?></p></a></a></td>
															
																<td valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $cut_balance = $order_quantitys-$total_cutting_qnty;?></p></td>
																<?
																$gr_lay_bal+=$lay_balance;
																$order_wise_lay_balance += $lay_balance;
																$style_wise_lay_balance += $lay_balance;


																$gr_cut_bal+=$cut_balance;
																$order_wise_cut_balance += $cut_balance;

																$style_wise_cut_balance += $cut_balance;										 

															
															?>
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'A','production_qnty_popup','Today Sewing Input','800','300');"><p><? echo $today_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_input"];?></p></a></td>

															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,4,'B','production_qnty_popup','Total Sewing Input','730','300');"><p><? echo $total_sewing_input;?></a></p></td>
															
																<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo 	$input_balance = $order_quantitys-$total_sewing_input;?></p></td>
															<?
															$gr_inh_bal+=$input_balance;
															$order_wise_input_balance += $input_balance;											 
															$style_wise_input_balance += $input_balance;
						 
															
															?>
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $inh_qty= $total_cutting_qnty-$total_sewing_input;$gr_inh_qty+=$inh_qty;$color_wise_inh+=$inh_qty;?></p></td>		
															 					 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,5,'A','production_qnty_popup','Today Sewing Output','800','300');"><p><? echo $today_input = $cutting_sewing_data[$job_id][$style_id][$po_id][$item_id][$color_id]["today_sewing_output"];?></p></a></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,5,'B','production_qnty_popup','Total Sewing Output','730','300');"><p><? echo $total_sewing_output;?></p></a></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle"  width="80"><p><? echo $input_sewing_balance = $total_sewing_input-$total_sewing_output;?></p></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,8,'A','production_qnty_popup','Today Finish','800','300');"><p><? echo $today_finishing;?></p></a></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_production_popup(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>,8,'B','production_qnty_popup','Total Finish','730','300');"><p><? echo $total_finishing;?></p></a></td>							 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $sewing_fin_balance = $total_sewing_output-$total_finishing;?></p></td>




															<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $today_ex_fac;?></p></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><a href="##" onclick="openmypage_ex_fac_total(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $color_id;?>, 'total_exfac_action');" ><p><? echo $total_ex_fac;?></p></a></td>							 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $order_xfact= $order_quantitys-$total_ex_fac;?></p></td>								 
															<td style="word-wrap: break-word;word-break: break-all;"   align="right" valign="middle" width="80"><p><? echo $sewing_xfact= $total_sewing_output-$total_ex_fac;?></p></td>								 
														</tr>	
												<?											
												
												$order_wise_input_sewing_balance += $input_sewing_balance;
												$order_wise_sewing_fin_balance += $sewing_fin_balance;

												$style_wise_input_sewing_balance += $input_sewing_balance;
												$style_wise_sewing_fin_balance += $sewing_fin_balance;

												$gr_input_sewing_balance += $input_sewing_balance;	
												$gr_sewing_fin_balance += $sewing_fin_balance;

												$order_wise_order_exfact_balance += $order_xfact;
												$order_wise_sewing_exfact_balance += $sewing_xfact;

												$style_wise_order_xfact_balance += $order_xfact;
												$style_wise_sewing_xfact_balance += $sewing_xfact;
												
												$gr_order_xfact_balance += $order_xfact;
												$gr_sewing_xfact_balance += $sewing_xfact;
												$k++;										
											//}									
											
											
										}

									}
									?>
									<tr bgcolor="#E4E4E4">
										<td colspan="10" align="right"><b>Order Wise Sub Total</b></td>
										<td align="right"><b><? echo $order_wise_subtotal;?></b></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="right"><b><? echo number_format($order_wise_fab_req,2);?></b></td>
										<td align="right"><b><? echo number_format($order_wise_fin_fab_req,2);?></b></td>
										<td align="right"><b><? echo number_format($order_wise_fab_issued_balance,2);?></b></td>
										<td align="right"><b><? echo number_format($order_wise_fab_possible_qty,2);?></b></td>
										<td align="right"><b><? echo $order_wise_today_lay;?></b></td>
										<td align="right"><b><? echo $order_wise_total_lay;?></b></td>
										<td align="right"><b><? echo $order_wise_lay_balance; ?></b></td>

										<td align="right"><b><? echo $order_wise_today_cutting;?></b></td>
										<td align="right"><b><? echo $order_wise_total_cutting;?></b></td>
										<td align="right"><b><? echo $order_wise_cut_balance; ?></b></td>
										<td align="right"><b><? echo $order_wise_today_input;?></b></td>
										<td align="right"><b><? echo $order_wise_total_input;?></b></td>
										<td align="right"><b><? echo $order_wise_input_balance;?></b></td>
										<td align="right"><b><? echo $order_wise_inhand_qty;?></b></td>
										<!-- =================********=================-->
										<td align="right"><b><? echo $order_wise_today_output;?></b></td>
										<td align="right"><b><? echo $order_wise_total_output;?></b></td>
										<td align="right"><b><? echo $order_wise_input_sewing_balance;?></b></td>

										<td align="right"><b><? echo $order_wise_today_finishing;?></b></td>
										<td align="right"><b><? echo $order_wise_total_finishing;?></b></td>
										<td align="right"><b><? echo $order_wise_sewing_fin_balance;?></b></td>

										<td align="right"><b><? echo $order_wise_today_export;?></b></td>
										<td align="right"><b><? echo $order_wise_total_export;?></b></td>
										<td align="right"><b><? echo $order_wise_order_exfact_balance;?></b></td>
										<td align="right"><b><? echo $order_wise_sewing_exfact_balance;?></b></td>
									</tr>
									<?
								}
							
							}
							
							?>
							<tr bgcolor="#E4E4E4">
							<td colspan="10" align="right"><b>Style Wise Sub Total</b></td>
							<td align="right"><b><? echo $style_wise_order_qty;?></b></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><b><? echo number_format($style_wise_fab_req,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_issued,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_issued_balance,2);?></b></td>
							<td align="right"><b><? echo number_format($style_wise_fab_posible_cut_qty,2);?></b></td>
							<td align="right"><b><? echo $style_wise_lay_today;?></b></td>
							<td align="right"><b><? echo $style_wise_lay_total;?></b></td>
							<td align="right"><b><? echo $style_wise_lay_balance;?></b></td>

							<td align="right"><b><? echo $style_wise_cut_today;?></b></td>
							<td align="right"><b><? echo $style_wise_cut_total;?></b></td>
							<td align="right"><b><? echo $style_wise_cut_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_input_today;?></b></td>
							<td align="right"><b><? echo $style_wise_input_total;?></b></td>
							<td align="right"><b><? echo $style_wise_input_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_inhand_qty;?></b></td>
						 
							<td align="right"><b><? echo $style_wise_today_output;?></b></td>
							<td align="right"><b><? echo $style_wise_total_output;?></b></td>
							<td align="right"><b><? echo $style_wise_input_sewing_balance;?></b></td>

							<td align="right"><b><? echo $style_wise_today_finishing;?></b></td>
							<td align="right"><b><? echo $style_wise_total_finishing;?></b></td>
							<td align="right"><b><? echo $style_wise_sewing_fin_balance;?></b></td>

							<td align="right"><b><? echo $style_wise_today_export;?></b></td>
							<td align="right"><b><? echo $style_wise_total_export;?></b></td>
							<td align="right"><b><? echo $style_wise_order_xfact_balance;?></b></td>
							<td align="right"><b><? echo $style_wise_sewing_xfact_balance;?></b></td>
							</tr>	
							<?
							$gr_inh_qty;
						}

						?>
						
											
						</table>

										  
					</div>				 

				</div>
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3165" rules="all"  >
							<tr bgcolor="#E4E4E4"  >  
								<td valign="middle" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p> &nbsp;</p></td>

								<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p>&nbsp;</p></td>
								<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p>&nbsp;</p></td>

								<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p>&nbsp;</p></td>

								<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p>&nbsp;</p></td>

								<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p>&nbsp;</p></td>

								<td  valign="middle" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p>&nbsp;</p></td>

								<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p>&nbsp;</p></td>
								<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p>&nbsp;</p></td>
								<td  valign="middle"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><strong>Grand Total</strong></td>



								<td style="word-wrap: break-word;word-break: break-all;"  width="80"    align="right"><b><? echo $gr_order_qnty;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80"   align="right"> </td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_req,2);?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_iss,2);?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_iss_bal,2);?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><? echo number_format($gr_pos_cut,2);?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_lay;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_lay;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_lay_bal;?></b></td>

								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_cut;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_cut;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_cut_bal;?></b></td>



								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_sewing;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_sewing;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_inh_bal;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_inh_qty;?></b></td>		

								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_output;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_output;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_input_sewing_balance;?></b></td>

								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_finishing;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_finishing;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_sewing_fin_balance;?></b></td>

								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_today_export;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_total_export;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_order_xfact_balance;?></b></td>
								<td style="word-wrap: break-word;word-break: break-all;"  width="80" align="right"><b><?php echo $gr_sewing_xfact_balance;?></b></td>											 

							</tr>	
							
						</table>	
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
	$create_new_doc = fopen($filename, 'w') or die('can not open');
	$is_created = fwrite($create_new_doc,ob_get_contents()) or die('can not write');
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}



if($action=="finish_fabric")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	 
	  $insert_cond="   and  d.production_date='$insert_date'";
    // if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active in(1,2,3) and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active in(1,2,3) and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
          </tfoot> 
       </table>
      </fieldset>
       <br />
    <? 
	
	$sql_fabric="SELECT a.po_breakdown_id,a.color_id,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
	    ELSE 0 END ) AS grey_fabric_issue,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
	    ELSE 0 END ) AS grey_fabric_issue_return,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS finish_fabric_rece,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS finish_fabric_rece_return,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS fabric_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity 
		ELSE 0 END ) AS fabric_qty_pre,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
		FROM order_wise_pro_details a,inv_transaction b
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0 AND a.po_breakdown_id 
		in (".str_replace("'","",$po_number_id).") group by a.po_breakdown_id,a.color_id";

			
	
		//echo $sql_cutting_delevery;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql_cutting_delevery);
		$production_details_arr=array();
		$production_size_details_arr=array();
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		
		$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
		$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('cut_delivery_date')];
		$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
		$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];
		}
		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">ID</th>
                        <th width="70">Date</th>
                        <th width="70">Fabric Qty.</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//if($value_c != "")
				//{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
                 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
				 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							
						?>
						<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
						<?
							
						}
				 ?>
				 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				//}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>
				
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
	    <br />
     </fieldset>
 </div>
 <?
}


if($action=="cutting_delivery_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active in(1,2,3)  and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active in(1,2,3) and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
          </tfoot>
       </table>
      </fieldset>
       <br />
    <? 
		$sql_cutting_delevery="select a.id,a.cut_delivery_date,a.challan_no ,b.production_qnty,c.size_number_id,c.color_number_id,c.country_id
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c 
	    where a.id=b.mst_id 
	    and b.color_size_break_down_id=c.id 
		and a.po_break_down_id=c.po_break_down_id  
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id 
		and a.status_active=1 and a.is_deleted=0
		and  b.status_active=1  and b.is_deleted=0
		and c.status_active=1 ";
		//echo $sql_cutting_delevery;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql_cutting_delevery);
		$production_details_arr=array();
		$production_size_details_arr=array();
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];

		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		
		$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
		$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('cut_delivery_date')];
		$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
		$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];
		}
		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//if($value_c != "")
				//{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
                 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
				 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							
						?>
						<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
						<?
							
						}
				 ?>
				 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				//}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>
				
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
				  <br />
     </fieldset>
 </div>
 <?
}

if($action=="cutting_and_sewing_remarks")
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	?>
    <div align="center">
        <fieldset style="width:480px">
        <legend>Cutting</legend>
		<? 
        $sql="SELECT  d.id,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=1  and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1   $insert_cond group by d.id,f.color_number_id,d.remarks,d.production_date order by d.id";
        //echo $sql;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql, "", "","", 1, '0,0,0,0', $arr, "id,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_urmi_controller", '','0,3,1,0','0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Cutting Delivery to Input</legend>
		<? 
	    $sql_cutting_delevery="select a.id,a.cut_delivery_date ,a.remarks,
		sum(b.production_qnty) AS cut_delivery_qnty
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c 
	    where a.id=b.mst_id 
	    and b.color_size_break_down_id=c.id 
		and a.po_break_down_id=c.po_break_down_id  
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id 
	    group by a.id,a.cut_delivery_date ,a.remarks";
       // echo $sql_cutting_delevery;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql_cutting_delevery, "", "","", 1, '0,0,0,0', $arr, "id,cut_delivery_date,cut_delivery_qnty,remarks", "../requires/daily_cutting_inhand_report_urmi_controller", '','0,3,1,0','0,0,0,cut_delivery_qnty,0');
                
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
		<? 
        $sql="SELECT  d.id,d.embel_name,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=2 and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1   $insert_cond group by d.id,d.embel_name,f.color_number_id,d.remarks,d.production_date order by d.embel_name";
        $arr=array(1=>$emblishment_name_array);
        echo  create_list_view ( "list_view_1", "ID,Embel. Name,Date,Production Qnty,Remarks", "80,100,70,70,180","600","220",1, $sql, "", "","", 1, '0,embel_name,0,0,0', $arr, "id,embel_name,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_urmi_controller", '','0,0,3,1,0','0,0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
		<? 
        $sql="SELECT  d.id,d.embel_name,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=3 and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1  and
		f.is_deleted =0 and
		f.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 
		
		 $insert_cond group by d.id,d.embel_name,f.color_number_id,d.remarks,d.production_date order by d.embel_name";
        $arr=array(1=>$emblishment_name_array);
        echo  create_list_view ( "list_view_1", "ID,Embel. Name,Date,Production Qnty,Remarks", "80,100,70,70,180","600","220",1, $sql, "", "","", 1, '0,embel_name,0,0,0', $arr, "id,embel_name,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_urmi_controller", '','0,0,3,1,0','0,0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
        <?
        $sql="SELECT  d.id,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=4  and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1  and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1 
		 $insert_cond group by d.id,f.color_number_id,d.remarks,d.production_date order by d.id";
        //echo $sql;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql, "", "","", 1, '0,0,0,0', $arr, "id,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_urmi_controller", '','0,3,1,0','0,0,0,product_qty,0');
		?>
        </fieldset>
	</div>  
<?
exit();
}



if($action=="emblishment_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  ); 
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active in(1,2,3) and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active in(1,2,3) and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:810px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="150">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
          </tfoot>
       </table>
      </fieldset>
       <br />
    <? 
		$sql="SELECT  d.id,d.floor_id,d.production_source,d.serving_company,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,
		    d.challan_no,d.production_date,f.country_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=$type  and
		    f.color_number_id=$color_id and
			d.embel_name=$embl_type  and
		    e.is_deleted =0 and
			e.status_active =1 and
		    d.is_deleted =0 and
			d.status_active =1 and
		    f.is_deleted =0 and
			f.status_active =1   $insert_cond order by d.production_date,f.id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $sql_data as $row)
		{
			if($row[csf('production_source')]==1)
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
			}
			else
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];	
			}
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$grand_color_qty+=$row[csf('product_qty')];
		}
		//print_r($job_size_array);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="" style="width:100%">
			<label> <strong>In House </strong><label/>
            
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="150">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$i=1;
				$inhouse_floor=array();
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{
							
							if(!in_array($value_c['floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td colspan="5" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>		
								<?	
								}
							}
							
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							
							$i++;
							$inhouse_floor[]=$value_c['floor_id'];
							$floor_id=$value_c['floor_id'];;
					}
					?>
                    
                    
                    		 <tr bgcolor="#FFFFE8">
									 <td colspan="5" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
								 </tr>	
                                 
                                 
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                          
                             <th></th>
                             <th>Total</th>
                            
                             <?
                                    foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                    <?
                                        }
                                    }
                            ?>
                             <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>
                   
                <label > <strong>Out Bound:</strong><label/>    
                <table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="120">Color</th>
                        <th width="130">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[3][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[3] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							$j++;
							
					}
					?>
                    
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                              <th></th>
                             <th>Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                            
                             <th colspan="5"> Grand Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $grand_size_qty[$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>
                             
                          </tfoot>
					
					</table>    
	 </div>
 <?
}


if($action=="cutting_and_sewing_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active in(1,2,3) and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active in(1,2,3) and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
      
    <? 
	 
	

		$sql="SELECT  d.production_source,d.serving_company,d.floor_id,d.sewing_line,d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,
		    d.production_date,f.country_id,d.challan_no
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=4  and
			f.color_number_id=$color_id and
		    e.is_deleted =0 and
			e.status_active =1 and
		    f.is_deleted =0 and
			f.status_active =1 and
		    d.is_deleted =0 and
			d.status_active =1 
			 $insert_cond order by d.production_date,f.id";
		//echo $sql;die;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $sql_data as $row)
		{
			if($row[csf('production_source')]==1)
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
			}
			else
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];	
			}
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$grand_color_qty+=$row[csf('product_qty')];
		}
		//print_r($job_size_array);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="" style="width:100%">
			<label> <strong>In House </strong><label/>
            
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Line No</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$i=1;
				$inhouse_floor=array();
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{
						
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{
							
							if(!in_array($value_c['floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>		
								<?	
								}
							}
							
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
							 <td align="center"><? echo  $line_name; ?></td>
							 
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							
							$i++;
							$inhouse_floor[]=$value_c['floor_id'];
							$floor_id=$value_c['floor_id'];;
					}
					?>
                    
                    
                    		 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{

												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
								 </tr>	
                                 
                                 
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th>Total</th>
                            
                             <?
                                    foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                    <?
                                        }
                                    }
                            ?>
                             <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>
                   
                <label > <strong>Out Bound:</strong><label/>    
                <table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[3][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[3] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
									<?

										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							$j++;
							
					}
					?>
                    
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                              <th></th>
                             <th>Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                            
                             <th colspan="5"> Grand Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $grand_size_qty[$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>
                             
                          </tfoot>
					
					</table>    
	 </div>
	 <?
}



if($action=="cutting_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active in(1,2,3) and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active in(1,2,3) and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
             <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
            </tfoot>
       </table>
      </fieldset>
       <br />
    <? 
	 
	

		$sql="SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,d.production_date,f.country_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=$type  and
			f.color_number_id=$color_id and
		    e.is_deleted =0 and
			e.status_active =1 and
			f.is_deleted =0 and
			f.status_active =1 and
			d.is_deleted =0 and
			d.status_active=1  
			  $insert_cond
			order by d.production_date";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('product_qty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		
		$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
		$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
		$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
		$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
		}
		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?></strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//if($value_c != "")
				//{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
                 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
				 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							
						?>
						<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
						<?
							
						}
				 ?>
				 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				//}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>
				
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
				  <br />
     </fieldset>
 </div>
 <?
}

if($action=="total_fabric_recv_qty")//total_fabric_recv_qty
{
	echo load_html_head_contents("Today Fabric Recv Qty","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	/*	echo $prod_date.'_';
	echo $order_id.'_';
	echo $color_id.'_';*/
	
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$batch_noArr = return_library_array("select id,batch_no from pro_batch_create_mst","id","batch_no"); 
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	//$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
	//echo $prod_date;
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	
	  $sql_fabric_qty=("SELECT a.po_breakdown_id,a.color_id,c.issue_number,c.issue_date,b.pi_wo_batch_no,
	
		CASE WHEN b.transaction_date <= '".$prod_date."' AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END  AS fabric_qty
		
		FROM order_wise_pro_details a,inv_transaction b,inv_issue_master c
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and c.id=b.mst_id and a.quantity!=0 and  b.is_deleted=0  and a.color_id=$color_id AND a.po_breakdown_id in (".str_replace("'","",$order_id).") order by c.issue_number ");
		//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
		//echo $sql_fabric_qty;
		$result=sql_select($sql_fabric_qty);
		$fabric_pre_qty=array();
		$fabric_today_qty=array();  
		$total_fabric=array();
		$fabric_balance=array();
		$fabric_wip=array();
		/*foreach($result as $value)
		{
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];
			
		//	$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
		//	-$value[csf("trans_out_pre")];
		
			$fabric_pre_qty[$value[csf("color_id")]]['fab_qty']+=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]-$value[csf("trans_out_pre")];
			//-$value[csf("trans_out_pre")];
			$fabric_pre_qty[$value[csf("color_id")]]['fabric_qty']+=$value[csf("fabric_qty")];
			$fabric_pre_qty[$value[csf("color_id")]]['issue_id']=$value[csf("issue_number")];
			$fabric_pre_qty[$value[csf("color_id")]]['issue_date']=$value[csf("issue_date")];
			$fabric_pre_qty[$value[csf("color_id")]]['batch_no']=$value[csf("pi_wo_batch_no")];
				
		}*/
	//	print_r($fabric_pre_qty);
	?>
	
    
     <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:540px">  
		<table width="540" align="center" border="1" rules="all" class="rpt_table"   >
		<thead>
			<tr>
                <th width="30">SL</th>
                <th width="130">ISSUE ID</th>
                <th width="80">Issue Date</th>
                <th width="100">BATCH NO</th>
                <th width="100">COLOR NAME</th>
                <th width="80">Recv. Qty</th>
            </tr>
         </thead>
         <?
		 $total_fab_qty=0;
		 $k=1;
        // foreach($fabric_today_qty as $order_id=>$order_data)
		 //{
			 //foreach($fabric_pre_qty as $color_key=>$color_val)
			 
			 foreach($result as $value)
			 {
				  if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
         <tr style="font:'Arial Narrow'" align="center" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
         	<td width="30"><? echo  $k;?> </td>
            <td width="130"><? echo  $value[csf("issue_number")];//$color_val['issue_id'];?>  </td>
            <td width="80"><? echo   change_date_format($value[csf("issue_date")]);//change_date_format($color_val['issue_date']);?> </td>
            <td width="100"> <? echo  $batch_noArr[$value[csf("pi_wo_batch_no")]];//$batch_noArr[$color_val['batch_no']];?></td>
            <td width="100"> <? echo  $color_name_arr[$value[csf("color_id")]];//$color_name_arr[$color_key];?></td>
            <td width="80" align="right"><? echo  number_format($value[csf("fabric_qty")],2);//number_format($color_val['fab_qty']+$color_val['fabric_qty'],2);?> </td>
            
         </tr>
         <?
		 $total_fab_qty+=$value[csf("fabric_qty")];//$color_val['fab_qty']+$color_val['fabric_qty'];
		 $k++;
			 }
		 //}
		 ?>
         <tr>
         <tfoot>
         <th align="right" colspan="5">Total</th><th align="right"> <? echo number_format($total_fab_qty,2);?></th> 
         </tr>
         </table>
                        
  </fieldset>
  </div>
	<?
	//exit();
	
}
if($action=="today_fabric_recv_qty")//
{
	echo load_html_head_contents("Today Fabric Recv Qty","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$batch_noArr = return_library_array("select id,batch_no from pro_batch_create_mst","id","batch_no"); 
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	//$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
	//echo $prod_date;
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	
	   $sql_fabric_qty=("SELECT a.po_breakdown_id,a.color_id,c.issue_number,c.issue_date,b.pi_wo_batch_no,
	
		CASE WHEN b.transaction_date = '".$prod_date."' AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END  AS fabric_qty
		
		FROM order_wise_pro_details a,inv_transaction b,inv_issue_master c
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and c.id=b.mst_id and a.quantity>0 and  b.is_deleted=0 and a.color_id=$color_id  AND a.po_breakdown_id in (".str_replace("'","",$order_id).") ");
		//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
		//echo  $sql_fabric_qty;
		$result=sql_select($sql_fabric_qty);
		$fabric_pre_qty=array();
		$fabric_today_qty=array();  
		$total_fabric=array();
		$fabric_balance=array();
		$fabric_wip=array();
		/*foreach($result as $value)
		{
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];
			
			//$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
			//-$value[csf("trans_out_pre")];
			$fabric_today_qty[$value[csf("color_id")]]['fabric_qty']+=$value[csf("fabric_qty")]+$value[csf("trans_in_qty")]
			-$value[csf("trans_out_qty")];
			//-$value[csf("trans_out_pre")];
			$fabric_today_qty[$value[csf("color_id")]]['issue_id']=$value[csf("issue_number")];
			$fabric_today_qty[$value[csf("color_id")]]['issue_date']=$value[csf("issue_date")];
			$fabric_today_qty[$value[csf("color_id")]]['batch_no']=$value[csf("pi_wo_batch_no")];
				
		}*/
		
	?>
	
    
     <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:550px">  
		<table width="520" align="center" border="1" rules="all" class="rpt_table"   >
		<thead>
			<tr>
                <th width="30">SL</th>
                <th width="130">ISSUE ID</th>
                <th width="80">Issue Date</th>
                <th width="100">BATCH NO</th>
                <th width="100">COLOR NAME</th>
                <th width="80">Recv. Qty</th>
            </tr>
         </thead>
         <?
		 $total_fab_qty=0;
		 $k=1;
        
			 //foreach($fabric_today_qty as $color_key=>$color_val)
			 foreach($result as $value)
			 {
				 if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";  
				
				 if($value[csf("fabric_qty")]>0)
				 {
				
		 ?>
        <tr style="font:'Arial Narrow'" align="center" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
         	<td width="30"><?   echo  	$k;?> </td>
            <td width="130"><? 	echo    $value[csf("issue_number")];//$color_val['issue_id'];?>  </td>
            <td width="80"><? 	echo   change_date_format($value[csf("issue_date")]);//change_date_format($color_val['issue_date']);?> </td>
            <td width="100"><? 	echo   $batch_noArr[$value[csf("pi_wo_batch_no")]];//$batch_noArr[$color_val['batch_no']];?></td>
            <td width="100"><? 	echo   $color_name_arr[$value[csf("color_id")]];//$color_name_arr[$color_key];?></td>
            <td width="80" align="right"><? echo  number_format($value[csf("fabric_qty")],2);// number_format($color_val['fabric_qty'],2);?> </td>
            
         </tr>
         <?
		  $total_fab_qty+=$value[csf("fabric_qty")]; //new
		  //$total_fab_qty+=$color_val['fabric_qty'];//old
		 $k++;
			 	}
			  }
		 ?>
         <tr>
         <tfoot>
         <th align="right" colspan="5">Total</th><th align="right"> <? echo number_format($total_fab_qty,2);?></th> 
         </tr>
         </table>
                        
  </fieldset>
  </div>
	<?
	//	exit();
	
}

//--print/emb issue-2,print/emb receive-3,
if ($action==2 || $action==3)
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $action;
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	
 	?>
    <div id="data_panel" align="center" style="width:100%">
         <script>
		 	
			function new_window()
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				//$('#table_body tr:first').hide();
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
				//$('#table_body tr:first').show();
				document.getElementById('scroll_body').style.overflowY="scroll";
				document.getElementById('scroll_body').style.maxHeight="none";
			}
          </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
 	</div>
    <div id="details_reports">
    
  
        <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
            <thead>
                <tr>
                    <th width="100">Buyer</th>
                    <th width="100">Job Number</th>
                    <th width="100">Style Name</th>
                    <th width="300">Order Number</th>
                    <th width="100">Ship Date</th>
                    <th width="100">Item Name</th>
                    <th width="100">Order Qty.</th>
                </tr>
            </thead>
            <?
                $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
                if($db_type==0)
                {
                    $sql = "SELECT a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
                        from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
                        where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                }
                else
                {
                    $sql = "SELECT a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
                        from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
                        where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
                }
                //echo $sql;die;
                $resultRow=sql_select($sql);
                    
                $cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[0][csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
                
            ?> 
            <tr style=" background-color:#FFFFFF">
                <td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
                <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
                <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
                <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
                <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
                <td><? echo $garments_item[$item_id]; ?></td>
                <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
            </tr>
             <?
             /*$prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and is_deleted=0 and status_active=1");
             foreach($prod_sewing_sql as $sewingRow);*/
			 
			if ($action==2) 
			{
				$th_head="Sys ID";
			}
			else if($action==3) 
			{
				$th_head="Challan";
			}
            ?> 	
           <!-- <tr>
                <td colspan="2">Total Alter Sewing Qty : <b><?// echo $sewingRow[csf("alter_qnty")]; ?></b></td>
                <td colspan="2">Total Reject Sewing Qty : <b><?// echo $sewingRow[csf("reject_qnty")]; ?></b></td>
                <td colspan="2">Pack Assortment: <b><?// echo $packing[$resultRow[csf("packing")]]; ?></b></td>
            </tr>-->
        </table>
    
    <br/>
    
        <table width="1710" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                   
                 <? if ($action==2) { ?>
                  <tr style="font-size:12px">
                        <th width="25" rowspan="2">Sl.</th>    
                        <th width="70" rowspan="2">Date</th>
                        <th colspan="4">Printing Issue</th>
                        <th colspan="4">Embroidery Issue</th>
                        <th colspan="4">Wash Issue</th>
                        <th colspan="4">Special Work Issue</th>
                        <th colspan="4">Gmts Dyeing Issue</th>
                    </tr> 
                 <? } else {?>
                 	<tr style="font-size:12px">
                        <th width="25" rowspan="2">Sl.</th>    
                        <th width="70" rowspan="2">Date</th>
                        <th colspan="4">Printing Receive</th>
                        <th colspan="4">Embroidery Receive</th>
                        <th colspan="4">Wash Receive</th>
                        <th colspan="4">Special Work Receive</th>
                        <th colspan="4">Gmts Dyeing Receive</th>
                    </tr> 
                 <? } ?>   
                    
                    <tr style="font-size:12px">
                     <?php /*?> <th width="70"><? echo $th_head ?></th><?php */?>
                      <th width="70">InHouse</th>
                      <th width="70">Outside</th>
                      <th width="80">Print Com.</th>
                      <th width="80">Locat.</th>
                      
                      <?php /*?><th width="70"><? echo $th_head ?></th><?php */?>
                      <th width="70">InHouse</th>
                      <th width="70">Outside</th>
                      <th width="80">Embl. Com.</th>
                      <th width="80">Locat.</th>
                      
                      <?php /*?><th width="70"><? echo $th_head ?></th><?php */?>
                      <th width="70">InHouse</th>
                      <th width="70">Outside</th>
                      <th width="80">Wash Com.</th>
                      <th width="80">Locat.</th>
                      
                     <?php /*?> <th width="70"><? echo $th_head ?></th><?php */?>
                      <th width="70">InHouse</th>
                      <th width="70">Outside</th>
                      <th width="80">Dyeing Com.</th>
                      <th width="80">Locat.</th>
                      
                      <?php /*?><th width="70"><? echo $th_head ?></th><?php */?>
                      <th width="70">InHouse</th>
                      <th width="70">Outside</th>
                      <th width="80">Comp.</th>
                      <th>Locat.</th>
                    </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:1710px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="1690" rules="all" id="table_body" >
            <?
			$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name" );
 			$supplier_library=return_library_array( "select id,short_name from  lib_supplier", "id", "short_name" );
			$location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );	
 			 
			  $sql_arr= sql_select("SELECT 
						 production_date,production_source,serving_company,location,id as sys_id,challan_no,
						(CASE WHEN production_source in(1,3) AND embel_name=1 THEN id ELSE 0 END) AS sys_ids1,  
						(CASE WHEN production_source in(1,3) AND embel_name=2 THEN id ELSE 0 END) AS sys_ids2,
						(CASE WHEN production_source in(1,3) AND embel_name=3 THEN id ELSE 0 END) AS sys_ids3,
						(CASE WHEN production_source in(1,3) AND embel_name=4 THEN id ELSE 0 END) AS sys_ids4,
						(CASE WHEN production_source in(1,3) AND embel_name=5 THEN id ELSE 0 END) AS sys_ids5,
						
						(CASE WHEN production_source in(1,3) AND embel_name=1 THEN challan_no ELSE null END) AS challan_no5,  
						(CASE WHEN production_source in(1,3) AND embel_name=2 THEN challan_no ELSE null END) AS challan_no6,
						(CASE WHEN production_source in(1,3) AND embel_name=3 THEN challan_no ELSE null END) AS challan_no7,
						(CASE WHEN production_source in(1,3) AND embel_name=4 THEN challan_no ELSE null END) AS challan_no8,
						(CASE WHEN production_source in(1,3) AND embel_name=5 THEN challan_no ELSE null END) AS challan_no9
												
 					FROM
						pro_garments_production_mst 
					WHERE 
						status_active=1 and is_deleted=0 and production_type=$action and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id order by production_date");
						
						$prod_arr=array();
						foreach($sql_arr as $row)
						{
							if($action==2)
							{
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['1'].=$row[csf('sys_ids1')].",";	
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['2'].=$row[csf('sys_ids2')].",";
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['3'].=$row[csf('sys_ids3')].",";
							
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['4'].=$row[csf('sys_ids4')].",";
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['5'].=$row[csf('sys_ids5')].",";
							}
							else if($action==3)
							{
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['1'].=$row[csf('challan_no5')].",";	
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['2'].=$row[csf('challan_no6')].",";
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['3'].=$row[csf('challan_no7')].",";
							
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['4'].=$row[csf('challan_no8')].",";
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['5'].=$row[csf('challan_no9')].",";	
							}
						
						}
					//print_r($prod_arr);
			 
			$sql = sql_select("SELECT production_date,production_source,serving_company,
						max(CASE WHEN  embel_name=1 THEN location ELSE 0 END) AS print_location,
						max(CASE WHEN  embel_name=2 THEN location ELSE 0 END) AS emb_location,
						max(CASE WHEN  embel_name=3 THEN location ELSE 0 END) AS wash_location,
						max(CASE WHEN  embel_name=4 THEN location ELSE 0 END) AS sp_location,
						max(CASE WHEN  embel_name=5 THEN location ELSE 0 END) AS gd_location,
						SUM(CASE WHEN production_source =1 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod11,  
						SUM(CASE WHEN production_source =1 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod12,
						SUM(CASE WHEN production_source =1 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod13,
						SUM(CASE WHEN production_source =1 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod14,
						SUM(CASE WHEN production_source =1 AND embel_name=5 THEN production_quantity ELSE 0 END) AS prod15,
						

						SUM(CASE WHEN production_source =3 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod31,  
						SUM(CASE WHEN production_source =3 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod32,
						SUM(CASE WHEN production_source =3 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod33,
						SUM(CASE WHEN production_source =3 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod34,
						SUM(CASE WHEN production_source =3 AND embel_name=5 THEN production_quantity ELSE 0 END) AS prod35
						
						
						
 					FROM
						pro_garments_production_mst 
					WHERE 
						status_active=1 and is_deleted=0 and production_type=$action and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id 
					GROUP BY production_date,production_source,serving_company order by production_date");
			 /*echo "SELECT production_date,production_source,serving_company,
			 			max(CASE WHEN  embel_name=1 THEN location ELSE 0 END) AS print_location,
						max(CASE WHEN  embel_name=2 THEN location ELSE 0 END) AS emb_location,
						max(CASE WHEN  embel_name=3 THEN location ELSE 0 END) AS wash_location,
						max(CASE WHEN  embel_name=4 THEN location ELSE 0 END) AS sp_location,
						SUM(CASE WHEN production_source =1 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod11,  
						SUM(CASE WHEN production_source =1 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod12,
						SUM(CASE WHEN production_source =1 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod13,
						SUM(CASE WHEN production_source =1 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod14,
						
						SUM(CASE WHEN production_source =3 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod31,  
						SUM(CASE WHEN production_source =3 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod32,
						SUM(CASE WHEN production_source =3 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod33,
						SUM(CASE WHEN production_source =3 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod34
 					FROM
						pro_garments_production_mst 
					WHERE 
						status_active=1 and is_deleted=0 and production_type=$action and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id $country_cond
					GROUP BY production_date,production_source,serving_company";*/
			
		   	$printing_in_qnty=0;$emb_in_qnty=0;$wash_in_qnty=0;$special_in_qnty=0;
			$printing_out_qnty=0;$emb_out_qnty=0;$wash_out_qnty=0;$special_out_qnty=0;$gd_out_qnty=0;$gd_in_qnty=0;
			$dataArray=array();$companyArray=array();
            $i=1;
			foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 
				 if($resultRow[csf('production_source')]==3)
					$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
				else
					$serving_company= $company_library[$resultRow[csf('serving_company')]];
				$td_count = 2;	
				 $print_sys_is=implode(",",array_unique(explode(",",$prod_arr[$resultRow[csf('production_date')]][$resultRow[csf('production_source')]][$resultRow[csf('serving_company')]]['1'])));
				  $embo_sys_is=$prod_arr[$resultRow[csf('production_date')]][$resultRow[csf('production_source')]][$resultRow[csf('serving_company')]]['2'];
				   $wash_sys_is=$prod_arr[$resultRow[csf('production_date')]][$resultRow[csf('production_source')]][$resultRow[csf('serving_company')]]['3'];
				    $sp_sys_is=$prod_arr[$resultRow[csf('production_date')]][$resultRow[csf('production_source')]][$resultRow[csf('serving_company')]]['4'];
					  $gd_sys_is=$prod_arr[$resultRow[csf('production_date')]][$resultRow[csf('production_source')]][$resultRow[csf('serving_company')]]['5'];
					 
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" style="font-size:12px">
                    <td width="25"><? echo $i;?></td>
                    <td width="70"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($resultRow[csf("production_date")]); ?></div></td>
                    
                     <?php /*?><td width="70" align="right"><p><?  echo rtrim($print_sys_is,","); ?></p></td><?php */?>
                     
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod11")];$printing_in_qnty+=$resultRow[csf("prod11")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod31")];$printing_out_qnty+=$resultRow[csf("prod31")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod11')]>0 || $resultRow[csf('prod31')]>0) echo $serving_company; ?></p></td>
                    <td width="80"><p>&nbsp;<?  echo $location_library[$resultRow[csf('print_location')]]; 
					$companyArray[$serving_company]=$serving_company;
					$dataArray[1][$serving_company]+=$resultRow[csf("prod11")]+$resultRow[csf("prod31")]; ?></p></td>
                    
                     <?php /*?><td width="70" align="right"><p><? echo rtrim($embo_sys_is,","); ?></p></td><?php */?>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod12")];$emb_in_qnty+=$resultRow[csf("prod12")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod32")];$emb_out_qnty+=$resultRow[csf("prod32")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod12')]>0 || $resultRow[csf('prod32')]>0) echo $serving_company; ?></p></td>
                    <td width="80"><p>&nbsp;<?  echo $location_library[$resultRow[csf('emb_location')]];
                    
 					$dataArray[2][$serving_company]+=$resultRow[csf("prod12")]+$resultRow[csf("prod32")]; ?></p></td>
                    
                     <?php /*?><td width="70" align="right"><? echo  rtrim($wash_sys_is,",");//if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod13")];$wash_in_qnty+=$resultRow[csf("prod13")];}else echo "0"; ?></td><?php */?>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod13")];$wash_in_qnty+=$resultRow[csf("prod13")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod33")];$wash_out_qnty+=$resultRow[csf("prod33")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod13')]>0 || $resultRow[csf('prod33')]>0) echo $serving_company; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo  $location_library[$resultRow[csf('wash_location')]]; 
                     
 					$dataArray[3][$serving_company]+=$resultRow[csf("prod13")]+$resultRow[csf("prod33")]; ?></p></td>
                    
                     <?php /*?><td width="70" align="right"><? echo  rtrim($sp_sys_is,",");//if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod13")];$wash_in_qnty+=$resultRow[csf("prod13")];}else echo "0"; ?></td><?php */?>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod14")];$wash_in_qnty+=$resultRow[csf("prod14")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod34")];$wash_out_qnty+=$resultRow[csf("prod34")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod14')]>0 || $resultRow[csf('prod34')]>0) echo $serving_company; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo  $location_library[$resultRow[csf('wash_location')]]; 
                     
 					$dataArray[3][$serving_company]+=$resultRow[csf("prod14")]+$resultRow[csf("prod34")]; ?></p></td>
                    
                    
                    <?php /*?> <td width="70" align="right"><? echo rtrim($sp_sys_is,",");//if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod14")];$special_in_qnty+=$resultRow[csf("prod14")];}else echo "0"; ?></td><?php */?>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod15")];$gd_in_qnty+=$resultRow[csf("prod15")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod35")];$gd_out_qnty+=$resultRow[csf("prod35")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod15')]>0 || $resultRow[csf('prod35')]>0) echo $serving_company; ?></p></td>
                    <td><p>&nbsp;<? echo  $location_library[$resultRow[csf('gd_location')]]; $dataArray[5][$serving_company]+=$resultRow[csf("prod15")]+$resultRow[csf("prod35")]; ?> </p></td>
                  </tr> 
 				 <?		
             	$i++;
            
        }//end foreach 1st
        ?>
        		<tfoot>
                    <tr>
                       <th align="right" colspan="2">Total</th>
                       <!-- <th align="right"><? //echo $printing_in_qnty; ?></th>-->
                       <th align="right"><? echo $printing_in_qnty; ?></th>
                       <th align="right"><? echo $printing_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right">&nbsp;</th>
                       <!-- <th align="right"><? //echo $emb_in_qnty; ?></th>-->
                       <th align="right"><? echo $emb_in_qnty; ?></th>
                       <th align="right"><? echo $emb_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right">&nbsp;</th>
                        <!--<th align="right"><? //echo $wash_in_qnty; ?></th>-->
                       <th align="right"><? echo $wash_in_qnty; ?></th>
                       <th align="right"><? echo $wash_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right">&nbsp;</th>
                       <!-- <th align="right"><? //echo $special_in_qnty; ?></th>-->
                       <th align="right"><? echo $special_in_qnty; ?></th>
                       <th align="right"><? echo $special_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right">&nbsp;</th>
                       
                      <!--  <th align="right"><? //echo $special_in_qnty; ?></th>-->
                       <th align="right"><? echo $gd_in_qnty; ?></th>
                       <th align="right"><? echo $gd_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right">&nbsp;</th>
                     </tr>
               </tfoot>      
        </table>
       </div>
       
       <div style="clear:both">&nbsp;</div>
       
       <div style="width:490px; float:left"> 
       <table width="470" cellspacing="0" border="1" class="rpt_table" rules="all" > 
       		<? if($action==2){?> <label><h3>Issue Summary</h3></label><? } else {?> <label><h3>Receive Summary</h3></label> <? } ?>               	
             <thead> 
                <tr>
                    <th>SL</th>
                    <th>Emb.Company</th>
                    <th>Print</th>
                    <th>Embroidery</th>
                    <th>Emb	Wash</th>
                    <th>Special Work</th>
                     <th>Gmt Dyeing</th>
                 </tr>
              </thead>  
			 <?
			 $printing_total=0;$emb_total=0;$wash_total=0;$special_total=0;$gd_total=0;
			 $i=1;	 
			 foreach($companyArray as $com){
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			 ?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                 		<td><? echo $i; ?></td>
                        <td><? echo $com; ?></td>
                        <td align="right"><? echo number_format($dataArray[1][$com]);$printing_total+=$dataArray[1][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[2][$com]);$emb_total+=$dataArray[2][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[3][$com]);$wash_total+=$dataArray[3][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[4][$com]);$special_total+=$dataArray[4][$com]; ?></td>
                         <td align="right"><? echo number_format($dataArray[5][$com]);$gd_total+=$dataArray[5][$com]; ?></td>
                 </tr>   
              <? $i++; } ?>
              <tfoot>
                    <tr>
                       <th align="right" colspan="2">Grand Total</th>
                       <th align="right"><? echo number_format($printing_total); ?></th>
                       <th align="right"><? echo number_format($emb_total); ?></th>
                       <th align="right"><? echo number_format($wash_total); ?></th>
                       <th align="right"><? echo number_format($special_total); ?></th>
                        <th align="right"><? echo number_format($gd_total); ?></th>
                    </tr>
              </tfoot>          
    	 </table>
     </div>
     
     <div style="width:450px; float:left; "> 
     	<? if($action!=2) //only for receive
		 { 
			?> 	
			<table width="450" cellspacing="0" border="1" class="rpt_table" rules="all" > 
            <label><h3>Balance </h3></label>
              <thead> 
                <tr>
                    <th>SL</th>
                    <th>Particulers</th>
                    <th>Print</th>
                    <th>Embroidery</th>
                    <th> Wash</th>
                    <th>Special Work</th>
                    <th>Gmt Dyeing</th>
                  
                 </tr>
              </thead>  
 			<?
 				$sql_order = sql_select("SELECT 
						SUM(CASE WHEN b.emb_name=1 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS print,  
						SUM(CASE WHEN b.emb_name=2 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS emb,
						SUM(CASE WHEN b.emb_name=3 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS wash,
						SUM(CASE WHEN b.emb_name=4 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS special,
						SUM(CASE WHEN b.emb_name=5 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS gmt_dyeing
   					FROM
						wo_po_break_down a, wo_pre_cost_embe_cost_dtls b 
					WHERE
						a.id in ($po_break_down_id) and a.job_no_mst=b.job_no and a.status_active in(1,2,3)");
				foreach($sql_order as $resultRow);	
						
				$sql_mst = sql_select("SELECT 
						SUM(CASE WHEN embel_name=1 THEN production_quantity ELSE 0 END) AS print_issue,  
						SUM(CASE WHEN embel_name=2 THEN production_quantity ELSE 0 END) AS emb_issue,
						SUM(CASE WHEN embel_name=3 THEN production_quantity ELSE 0 END) AS wash_issue,
						SUM(CASE WHEN embel_name=4 THEN production_quantity ELSE 0 END) AS special_issue,
						SUM(CASE WHEN embel_name=5 THEN production_quantity ELSE 0 END) AS gmt_issue
 					FROM
						pro_garments_production_mst
					WHERE
						po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=2 
					");		
				//echo $sql_mst;die;
				foreach($sql_mst as $resultMst);
				//echo $sql;die;
				$i=1;		
				 
					 ?>
						 <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
								<td>Req Qnty</td>
								<td align="right"><? echo number_format($resultRow[csf('print')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('emb')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('wash')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('special')]); ?></td>
                                <td align="right"><? echo number_format($resultRow[csf('gmt_dyeing')]); ?></td>
						 </tr> 
                         <tr bgcolor="#FFFFFF">
								<td><? echo $i++; ?></td>
                                <td>Total Sent for</td>
 								<td align="right"><? echo number_format($resultMst[csf('print_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('emb_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('wash_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('special_issue')]); ?></td>
                                <td align="right"><? echo number_format($resultMst[csf('gmt_issue')]); ?></td>
						 </tr>
                         <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
                                <td>Total Receive</td>
 								<td align="right"><? echo number_format($printing_total); ?></td>
								<td align="right"><? echo number_format($emb_total); ?></td>
								<td align="right"><? echo number_format($wash_total); ?></td>
								<td align="right"><? echo number_format($special_total); ?></td>
                                <td align="right"><? echo number_format($gd_total); ?></td>
						 </tr>
                         <tr bgcolor="#FFFFFF">
								<td><? echo $i++; ?></td>
                                <td>Receive Balance</td>
                                <? $rcv_print_balance = $resultMst[csf('print_issue')]-$printing_total; ?>
 								<td align="right"><? echo number_format($rcv_print_balance); ?></td>
								<? $rcv_emb_balance = $resultMst[csf('emb_issue')]-$emb_total; ?>
 								<td align="right"><? echo number_format($rcv_emb_balance); ?></td>
								<? $rcv_wash_balance = $resultMst[csf('wash_issue')]-$wash_total; ?>
 								<td align="right"><? echo number_format($rcv_wash_balance); ?></td>
								<? $rcv_special_balance = $resultMst[csf('special_issue')]-$special_total;
								$rcv_gd_balance = $resultMst[csf('gmt_issue')]-$gd_total;
								 ?>
 								<td align="right"><? echo number_format($rcv_special_balance); ?></td>
                                <td align="right"><? echo number_format($rcv_gd_balance); ?></td>
						 </tr> 
                         <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
 								<td>Issue Balance</td>
 								<td align="right"><? echo  number_format($resultRow[csf('print')]-$resultMst[csf('print_issue')]); ?></td>
								<td align="right"><? echo  number_format($resultRow[csf('emb')]-$resultMst[csf('emb_issue')]); ?></td>
                                <td align="right"><? echo  number_format($resultRow[csf('wash')]-$resultMst[csf('wash_issue')]); ?></td>
                                <td align="right"><? echo  number_format($resultRow[csf('special')]-$resultMst[csf('special_issue')]); ?></td>
                                <td align="right"><? echo  number_format($resultRow[csf('gmt_dyeing')]-$resultMst[csf('gmt_issue')]); ?></td>
 						 </tr>  
					 <? 
 				} 
			?>
            </table> 
        
     </div>
 </div>    
    
	<?
  	exit();
 
}

if($action=="cutting_sewing_action")
{
	extract($_REQUEST);
	list($po,$item,$cutting,$type,$color)=explode('**', $data);
	$work_comp=$_SESSION["work_comp"];
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	if($cutting) $cutting_cond=" and b.cut_no='$cutting'";


	$production_sql="SELECT a.serving_company, c.color_number_id,c.size_number_id,sum(b.production_qnty) as qntys,c.order_quantity  from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id='$po' and a.item_number_id='$item' $cutting_cond  and c.color_number_id='$color' and a.production_type='$type'  group by  a.serving_company, c.color_number_id,c.size_number_id,c.order_quantity";
	 $color_size_wise_qnty=array();
	 $size_all_arr=array();
	 foreach(sql_select($production_sql) as $keys=>$vals)
	 {
	 	if($po_col_size_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]=="")
	 	{
	 		$color_size_wise_qnty[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["order_quantity"]+=$vals[csf("order_quantity")];
	 		$po_col_size_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]=1;
	 	}
	 	
	 	$working_comp_color_size_wise_qnty[$vals[csf("serving_company")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["qntys"]+=$vals[csf("qntys")];
	 	$details_part_array[$vals[csf("serving_company")]][$vals[csf("color_number_id")]]=$vals[csf("serving_company")];
	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	 	$color_all_arr[$vals[csf("color_number_id")]]=$vals[csf("color_number_id")];
	 }
	  
	 $size_count=count($size_all_arr)*45;
	 $tbl_width=200+$size_count;
	?>
     

    </head>
    <body>
        <div align="center" style="width:100%;" >
            
            
             	<table width="<? echo $tbl_width ;?>" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Size</strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Color Name</th>             				 
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>
             					<?
             				}
             				?>
             				<th width="80">Total</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table  width="<? echo $tbl_width ;?>" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1;
              	$gr_size_total=array();
              	$size_total=0;
             	foreach($color_all_arr as  $keys=> $rows)            		 
             	{
             		$total_sizeqnty=0;
             		
             		?>
             			<tr>                	 
         						<td align="center" width="30" ><? echo $p++;?></td>
          						<td align="center"  width="90"><? echo $colorarr[$keys] ;?></td>
         						<?
         						
         						foreach($size_all_arr as $size_key=>$val)
         						{
         							?>
         							<td align="right"  width="45"><? echo  $value= $color_size_wise_qnty[$keys][$size_key]["order_quantity"] ;?></td>

         							<?
         							$gr_size_total[$size_key]+=$value;
         							$total_sizeqnty+=$value;
         							 
         							
         						}
         					 
         						?>

         						<td align="right"  width="80"><b><? echo $total_sizeqnty;?></b></td>
         						 
             						 
             			</tr>  
				<?
				}
				?>   
						<tr>
							<td colspan="2" align="right"><b>Total</b></td>
							<?
							$gr_all_size=0;
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<td width="45" align="right"><b><? echo $vals=$gr_size_total[$key];?></b></td>

             					<?
             					$gr_all_size+=$vals;
             				}

             				?>
             				<td align="right"><b><? echo $gr_all_size?></b></td>
						</tr>            		 
             		</table>
             		</div>

             		<table width="<? echo $tbl_width+120 ;?>" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Details</strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="120">Working Company</th>             				 
             				<th width="90">Color Name</th>             				 
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45" align="right"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Total</th>
             				 
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table  width="<? echo $tbl_width+120 ;?>" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1;
             	$detail_grand_total = 0;
             	$dtls_gr_size=array();
             	foreach($details_part_array as  $company_id=> $color_data)            		 
             	{             		
             		foreach($color_data as  $color_id=> $rows)
             		{
             			?>
             			<tr>                	 
         						<td align="center" width="30" ><? echo $p++;?></td>
          						<td align="center"  width="120"><? echo $company_library[$company_id] ;?></td>
          						<td align="center"  width="90"><? echo $colorarr[$color_id] ;?></td>
         						<?
         						$size_total=0;
         						foreach($size_all_arr as $size_key=>$val)
         						{
         							?>
         							<td align="right"  width="45"><? echo $size_qn=  $working_comp_color_size_wise_qnty[$company_id][$color_id][$size_key]["qntys"] ;?></td>

         							<?
         							$size_total+=$size_qn;
         							$dtls_gr_size[$size_key]+=$size_qn;
         							
         						}
         						$detail_grand_total += $size_total;
         						?>
         						<td align="right"  width="80"><b><? echo $size_total;?></b></td>         						 
             						 
             			</tr>  
					<?
					}
				}
				?>
					<tr>
						<td colspan="3" align="right"><b>Total</b></td>
						<?
         				foreach($size_all_arr as $key=>$val)
         				{
         					?>
         					<td width="45" align="right"><b><? echo $dtls_gr_size[$key];?></b></td>

         					<?
         				}

         				?>
         				<td align="right"><b><?php echo $detail_grand_total;?></b></td>
					</tr>               		 
             	</table>
             </div>
             
        </div>
      
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}


if($action=="total_exfac_action")
{
	extract($_REQUEST);
	list($po,$item,$color)=explode('**', $data);
	$work_comp=$_SESSION["work_comp"];
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$po_arr=return_library_array("select id,po_number from  wo_po_break_down where id=$po and status_active in(1,2,3) ","id","po_number");
	$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id,c.color_number_id, a.ex_factory_date,  sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id and  a.id=b.mst_id and b.color_size_break_down_id=c.id and  a.po_break_down_id=$po and c.color_number_id=$color and a.item_number_id=$item and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0   group by a.po_break_down_id, a.item_number_id,c.color_number_id , a.ex_factory_date");

	 
	?>
     

    </head>
    <body>
        <div align="center" style="width:100%;" >
            
            
             	<table width="310" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Ex-Factory</strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="110">Order No</th>            				 
             				<th width="90">Date</th>            				 
              				<th width="80">Qnty</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table  width="<? echo $tbl_width ;?>" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1;
              	 
              	$gr_total=0;
             	foreach($ex_factory_data as  $keys=> $rows)            		 
             	{
             		 
             		
             		?>
             			<tr>                	 
         						<td align="center" width="30" ><? echo $p++;?></td>
          						<td align="center"  width="110"><? echo $po_arr[$rows[csf("po_break_down_id")]];?></td>
          						<td align="right"  width="90"><b><? echo change_date_format($rows[csf("ex_factory_date")]);?></b></td>
          						<td align="right"  width="80"><b><? echo $qntys=$rows[csf("total_ex_fac")];?></b></td>
         						 
             						 
             			</tr>  
				<?
				$gr_total+=$qntys;
				}
				?>  
				<tr bgcolor="#E4E4E4">
				<td align="right" colspan="3">Total</td>
				<td align="right"><strong><? echo $gr_total;?></strong></td>
					
				</tr> 
						      		 
             		</table>
             		</div>

             		 
             </div>
             
        </div>
      
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}

if($action=="fab_issue_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode("_", $data);
	$order_id = $data[0];
	$color = $data[1];
	// if($data[2]==2) $color_cond=" and color_id =$data[1] ";
	$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	?>
	<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title><style>table tr td{font-size:12px;}</style></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";

		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="230px";
	}	
	
	</script>	
	<div style="width:740px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:740px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Issue Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">Issue No</th>
                    <th width="100">Challan No</th>
                    <th width="80">Issue Date</th>
                    <th width="120">Batch No</th>
                    <th width="110">Issue Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
					
                    $i=1; $total_issue_to_cut_qnty=0;
                    $sql="SELECT a.issue_number, a.issue_date, d.pi_wo_batch_no as batch_id, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, inv_transaction d where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=d.mst_id and b.prod_id=d.prod_id and d.id=b.trans_id and d.id=c.trans_id group by b.id, a.issue_number, a.issue_date, a.challan_no, d.pi_wo_batch_no, b.prod_id";
                    // echo $sql;
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_issue_to_cut_qnty += $row[csf('quantity')] - $issue_return_qty[$val[csf('order_id')]][$val[csf('color_id')]];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="120" align="center"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="5" align="right">Total Issue</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
               </div>
                 <table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Issue Return Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">Issue Rtn No</th>
                    <th width="100">Challan No</th>
                    <th width="80">Return Date</th>
                    <!-- <th width="120">Batch No</th> -->
                    <th width="110">Return Qty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body2">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?					
                    $j=1; $total_ret_qnty=0;
                    $sql_ret="SELECT a.recv_number, a.receive_date, b.pi_wo_batch_no, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52,126) and c.entry_form in (52,126) and b.transaction_type in (4) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.challan_no, b.pi_wo_batch_no, b.prod_id";
                    // echo $sql_ret;
                    $result_ret=sql_select($sql_ret);
        			foreach($result_ret as $row)
                    {
                        if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    
                        $total_ret_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trr_<? echo $j; ?>','<? echo $bgcolor;?>')" id="trr_<? echo $j;?>">
                            <td width="50"><? echo $j; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <!-- <td width="120"><p><? //echo $batch_details[$row[csf('pi_wo_batch_no')]]; ?></p></td> -->
                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <tr>

                            <th colspan="4" align="right">Total Return</th>
                            <th align="right"><? echo number_format($total_ret_qnty,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="4" align="right">Total Issue to Cut</th>
                            <th align="right"><? $tot_iss_to_cut=$total_issue_to_cut_qnty-$total_ret_qnty; echo number_format($tot_iss_to_cut,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
	exit();
}

if($action=="production_qnty_popup")
{
 	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$dates=$_SESSION['txt_production_date'];
	$date_cond="";
	?>
	 
	

	<div id="data_panel" align="center" style="width:100%">
		<script>
			function new_window()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
	</div>

	<?
	if($day=='A')
	{
		$date_cond=" and a.production_date=$dates";
		$date_cond_lay=" and a.entry_date=$dates";
	}
	$companyarr=return_library_array("SELECT id,company_name from lib_company ","id","company_name");
	$floorarr=return_library_array("SELECT id,floor_name from lib_prod_floor ","id","floor_name");
	$resourcearr=return_library_array("SELECT id,line_number from prod_resource_mst ","id","line_number");
	$linearr=return_library_array("SELECT id,line_name from lib_sewing_line ","id","line_name");
	$locationarr=return_library_array("SELECT id,location_name from lib_location ","id","location_name");
	$countryarr=return_library_array("SELECT id,country_name from lib_country ","id","country_name");
	$buyerarr=return_library_array("SELECT id,buyer_name from lib_buyer ","id","buyer_name");
	$sizearr=return_library_array("SELECT id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("SELECT id,color_name from  lib_color ","id","color_name");
	$po_arr=return_library_array("SELECT id,po_number from  wo_po_break_down where id=$po and status_active in(1,2,3) ","id","po_number");
	$po_qnty_arr=return_library_array("SELECT id,po_quantity from  wo_po_break_down where id=$po and status_active in(1,2,3) ","id","po_quantity");
	if( ($type==1 && $day=='A') ||($type==0 && $day=='A'))
	{

		
		$size_id_array=array();
		$size_wise_qnty_array=array();
		$color_wise_qnty_array=array();
		
		$data_array=array();
		if($type==1)
		{
			$production_data="SELECT a.po_break_down_id,a.item_number_id,sum(b.production_qnty) as production_qnty,d.size_number_id,d.color_number_id,d.country_id from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_break_down c,wo_po_color_size_breakdown d where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and d.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  and a.item_number_id=$item and d.color_number_id=$color $date_cond group by  a.po_break_down_id,a.item_number_id,d.size_number_id,d.color_number_id,d.country_id ";
		}
		else if($type==0)
		{
			 $production_data="SELECT c.order_id as po_break_down_id,b.gmt_item_id as item_number_id,sum(c.size_qty) as production_qnty,c.size_id as size_number_id,b.color_id as color_number_id,c.country_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c  where  a.id=b.mst_id and b.id=c.dtls_id     and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active =1 and c.is_deleted=0  and c.order_id=$po    and b.gmt_item_id=$item and b.color_id=$color $date_cond_lay group by c.order_id ,b.gmt_item_id,c.size_id,b.color_id,c.country_id ";
		}
		
		$production_data=sql_select($production_data);

		foreach($production_data as $vals)
		{

			$data_array[$vals[csf("color_number_id")]][$vals[csf("country_id")]]=$vals[csf("country_id")];
			$size_id_array[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
			$size_wise_qnty_array[$vals[csf("color_number_id")]][$vals[csf("country_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];
			$color_wise_qnty_array[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];

		}
		$counts=count($size_id_array);  



		?>


		 

			


			<div id="details_reports">
				<table width="<? echo 280+($counts*50); ?>" cellspacing="0" cellpadding="0" border="0"   rules="">

					<thead>
						<?
						if(($day=="A" && $type==1) || ($day=="A" && $type==0)) 
						{
							?>
							<tr>
								<td height="5">&nbsp;</td>
							</tr>
							<tr>
								<td><strong>Date : <? echo change_date_format(str_replace("'", "", $dates)); ?></strong></td> 

							</tr>
							<tr>
								<td><strong>Order No: <? echo $po_arr[$po]; ?></strong></td>

							</tr>

							<?
						}
	             			//die;
						?>

					</thead>
				</table>




				<table width="<? echo 280+($counts*50); ?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
					<thead>
						<tr> 
							<th width="30" rowspan="2">SI</th>
							<th width="100" rowspan="2">Color</th>
							<th width="100" rowspan="2">Country Name</th>
							<th colspan="<? echo $counts;?>">Size</th>
							<th width="50" rowspan="2">Total</th>
						</tr>
						<tr>
							<?
							foreach ($size_id_array as $value)
							{
								?>
								<th width="50"><? echo $sizearr[$value]; ?></th>

								<?

							}

							?>
						</tr>
					</thead>
				</table>
					<div style="max-height:300px;  ">
						<table  id="table_body"  width="<? echo 280+($counts*50); ?>" border="1" rules="all" class="rpt_table" align="left" >
							<?
							$p=1;              	 
							$gr_total=0;
							foreach($data_array as  $color_id=> $country_val)            		 
							{
								foreach($country_val as  $country_id=> $rows)  
								{




									?>
									<tr>                	 
										<td align="center" width="30" ><? echo $p++;?></td>
										<td align="center"  width="100"><? echo $colorarr[$color_id];?></td>
										<td align="center"  width="100"><b><? echo $countryarr[$country_id];?></b></td>
										<?
										$total_qnty=0;
										foreach ($size_id_array as $value)
										{
											?>
											<td align="center" width="50"><?  echo $qntys= $size_wise_qnty_array[$color_id][$country_id][$value];  ?></td>

											<?
											$total_qnty+=$qntys;

										}

										?>

										<td align="center"  width="50"><b><? echo $total_qnty;?></b></td>


									</tr>  
									<?
								}
								$gr_total+=$qntys;
								?>

								<tr>                	 

									<td align="right" colspan="3"><b>Color Total: </b></td>
									<?
									$total_qnty=0;
									foreach ($size_id_array as $value)
									{
										?>
										<td align="center" width="50"><?  echo $qntys= $color_wise_qnty_array[$color_id][$value];  ?></td>

										<?
										$total_qnty+=$qntys;

									}

									?>

									<td align="center"  width="50"><b><? echo $total_qnty;?></b></td>


								</tr> 
								<?


							}
							?>  
							<tr>                	 

								<td align="right" colspan="3"><b>Day Total: </b></td>
								<?
								$total_qnty=0;
								foreach ($size_id_array as $value)
								{
									?>
									<td align="center" width="50"><?  echo $qntys= $color_wise_qnty_array[$color_id][$value];  ?></td>

									<?
									$total_qnty+=$qntys;

								}

								?>

								<td align="center"  width="50"><b><? echo $total_qnty;?></b></td>


							</tr> 

						</table>
					</div>


				</div>

			</div>
			<script>   setFilterGrid("table_body",-1);  </script> 

		 
		<?

	}
	else if( ($type==1 && $day=='B') || ($type==4 && $day=='B') || ($type==5 && $day=='B') || ($type==8 && $day=='B') || ($type==0 && $day=='B'))
	{
		$order_data="SELECT e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po and d.item_number_id=$item and d.color_number_id=$color  group by  e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date ";
		if($type)
		{

			$production_data="SELECT a.floor_id,a.sewing_line, a.production_date,a.serving_company,a.location,sum(case when production_source=1 then b.production_qnty else 0 end ) as inhouse ,sum(case when production_source=3 then b.production_qnty else 0 end ) as outbound   from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and d.po_break_down_id=c.id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  and a.item_number_id=$item and d.color_number_id=$color  group by   a.floor_id,a.sewing_line,a.production_date,a.serving_company,a.location ";

		}

		else if($type==0)
		{
			$production_data="SELECT a.entry_date as production_date,a.location_id as location,a.working_company_id as serving_company,  sum(c.size_qty) as inhouse from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c  where  a.id=b.mst_id and b.id=c.dtls_id     and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active =1 and c.is_deleted=0  and c.order_id=$po    and b.gmt_item_id=$item and b.color_id=$color  group by a.entry_date ,a.location_id ,a.working_company_id ";
		}		 
		$production_data=sql_select($production_data);

		 
		?>
			<div id="details_reports"> 
				<table width="700" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
					<thead>
						<tr>
							<th width="100">Buyer</th>
							<th width="100">Job Number</th>
							<th width="100">Style Name</th>
							<th width="100">Order Number</th>
							<th width="100">Ship Date</th>
							<th width="100">Item Name</th>
							<th width="80">Order Qty.</th>
						</tr>
					</thead>
					<tbody>
						<?

						foreach(sql_select($order_data) as $vals)
						{
							?>
							<tr>
								<td align="center" width="100"><?echo $buyerarr[$vals[csf("buyer_name")]]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("job_no")]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("style_ref_no")]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("po_number")]; ?></td>
								<td align="center" width="100"><?echo $vals[csf("pub_shipment_date")]; ?></td>
								<td align="center" width="100"><?echo $garments_item[$vals[csf("item_number_id")]]; ?></td>
								<td align="center" width="80"><?echo $vals[csf("po_qnty")]; ?></td>
							</tr>
							<?


					    }
 

							 
						//}
						if($type==5 || $type==4)$tbl_wid=700;else $tbl_wid=500;



						?>
					</tbody>
				</table>
				<br>
				<br>

				
				    <table style="margin-top: 10px;" width="<? echo $tbl_wid;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
							<thead>
								<tr> 
									<th  width="30" rowspan="2">SI</th>
									<th width="100" rowspan="2"><? if($type==1 || $type==0)echo "Cutting Date";else if($type==4 || $type==5 ) echo "Sewing Date"; else if($type==8) echo "Finish Date";?></th>
									<?
										if($type==5 || $type==4)
										{
											?>
											<th rowspan="2" width="100">Floor</th>
											<th rowspan="2" width="100">Sewing Line</th>

											<?
										}
									?>
									<th width="100" colspan="2"><? if($type==1)echo "Cutting Qty";else  if($type==4 || $type==5) echo "Sewing Qty";else if($type==8) echo "Finish Qty";else if($type==0) echo "Lay Qty";?> </th>
									<th width="100" rowspan="2"><? if($type==1)echo "Cutting Company";else  if($type==4 || $type==5) echo "Sewing Comany";else if($type==8) echo "Finish Company";else if($type==0) echo "Lay Company";?></th>

									<th width="100" rowspan="2">Location</th>
								</tr>
								<tr>
									<th width="50">In-house</th>
									<th width="50">Out-bound</th>
									 
								</tr>
							</thead>
					</table>
						<div style="max-height:300px;  ">
							<table id="table_body"  width="<? echo $tbl_wid;?>"  border="1" rules="all" class="rpt_table"  >
								 
								<tbody>
									<?
									$p=1;
									$total_inhouse=0;
									$total_out=0;
									foreach($production_data as $vals)
									{
										?>
										<tr>
											<td align="center" width="30"><?echo $p++; ?></td>
											<td align="center" width="100"><?echo change_date_format($vals[csf("production_date")]); ?></td>

											<?
											if($type==5 || $type==4)
											{
												?>
												<td width="100" align="center"><? echo $floorarr[$vals[csf("floor_id")]]; ?></td>
												<td width="100" align="center"><?  $line= explode(",",  $resourcearr[$vals[csf("sewing_line")]]); 
													$lines="";
													foreach($line as $val)
													{

														if($lines=="") $lines=$linearr[$val];
														else  $lines.=','.$linearr[$val];
													}
													if($lines=="") $lines = $linearr[$vals[csf("sewing_line")]];
													echo $lines;
													?></td>

													<?
												}
												$total_inhouse+=$vals[csf("inhouse")];
												$total_out+=$vals[csf("outbound")];
												?>


												<td align="center" width="50"><?echo $vals[csf("inhouse")]; ?></td>
												<td align="center" width="50"><?echo $vals[csf("outbound")]; ?></td>
												<td align="center" width="100"><?echo $companyarr[$vals[csf("serving_company")]]; ?></td>
												<td align="center" width="100"><?echo $locationarr[$vals[csf("location")]]; ?></td>

											</tr>
											<?
									}
										?>
										
											


								</tbody>
								
							</table> 
							<div>

					<table   width="<? echo $tbl_wid;?>"  border="1" rules="all" class="rpt_table"  >
						<tfoot>
							<tr>


								<?
								if($type==5 || $type==4)
								{
									?>
									<td width="30">&nbsp;</td>
									<td width="100" >&nbsp;</td>
									<td width="100" >&nbsp;</td>
									<td width="100"   align="right"><strong>Grand Total</strong></td>
									


									<?
								}
								else
								{
									?>
									<td width="30">&nbsp;</td>
									<td width="100"   align="right"><strong>Grand Total</strong></td>

									<?
								}
								?>


								<td id="ttl_inhouse" align="center" width="50"><?//echo $total_inhouse; ?></td>
								<td id="ttl_outbound" align="center" width="50"><?//echo $total_out; ?></td>
								<td width="100" >&nbsp;</td>
								<td width="100" >&nbsp;</td>

							</tr>

						</tfoot>
								 
								 
								
					</table>
			</div>

			</div>
			


					<script type="text/javascript">
 								var tableFilters1 = 
								{
									 
									col_operation: {
										id: ["ttl_inhouse","ttl_outbound"],
										col: [2,3],
										operation: ["sum","sum"],
										write_method: ["innerHTML","innerHTML"]
									}
								}

								var tableFilters2 = 
								{
									 
									col_operation: {
										id: ["ttl_inhouse","ttl_outbound"],
										col: [4,5],
										operation: ["sum","sum"],
										write_method: ["innerHTML","innerHTML"]
									}
								}
								var type='<? echo $type;?>';
								 if(type==4 || type==5)
								 {
								 	setFilterGrid("table_body",-1,tableFilters2);
								 }
								 else
								 {
								 	setFilterGrid("table_body",-1,tableFilters1);
								 }
								

					</script>


		 
		<?

	}


	else if( ($type==4 && $day=='A') || ($type==5 && $day=='A') ||   ($type==8 && $day=='A') )
	{
		$order_data="SELECT c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id, sum(d.order_quantity) as po_qnty from wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where c.id=d.po_break_down_id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and c.id=$po and d.item_number_id=$item and d.color_number_id=$color  group by c.id, e.buyer_name,e.job_no,e.style_ref_no, c.po_number,d.item_number_id,c.pub_shipment_date,d.color_number_id,d.size_number_id ";
		$job_array=array();
		foreach(sql_select($order_data) as $vals)
		{
			$job_array[$vals[csf("id")]]["buyer_name"]=$buyerarr[$vals[csf("buyer_name")]];
			$job_array[$vals[csf("id")]]["job_no"]=$vals[csf("job_no")];
			$job_array[$vals[csf("id")]]["style_ref_no"]=$vals[csf("style_ref_no")];
			$job_array[$vals[csf("id")]]["po_number"]=$vals[csf("po_number")];
			$job_array[$vals[csf("id")]]["pub_shipment_date"]=$vals[csf("pub_shipment_date")];
			$job_array[$vals[csf("id")]]["po_qnty"]+=$vals[csf("po_qnty")];
			$job_array[$vals[csf("id")]]["item_number_id"]=$garments_item[$vals[csf("item_number_id")]];
			$col_size_id_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("po_qnty")];
			$col_id_arr[$vals[csf("color_number_id")]]+=$vals[csf("po_qnty")];
			$size_id_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
		}
		$counts=count($size_id_arr);


		$production_data="SELECT a.serving_company, a.country_id,a.production_source,a.challan_no,a.floor_id,a.sewing_line,a.production_date,d.color_number_id,d.size_number_id,sum(b.production_qnty) as qnty   from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e  where a.id=b.mst_id and b.color_size_break_down_id=d.id and a.po_break_down_id=c.id and d.po_break_down_id=c.id and e.job_no=d.job_no_mst and e.job_no=c.job_no_mst and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id=$po and a.production_type=$type  and a.item_number_id=$item and d.color_number_id=$color and a.production_date='$txt_production_date' group by  a.serving_company, a.country_id,a.production_source,a.challan_no,a.floor_id,a.sewing_line,a.production_date,d.color_number_id,d.size_number_id  ";		  
        // echo $production_data;
		$production_data=sql_select($production_data);
		$main_data_arr=array();
		$size_wise_main_data_arr=array();
		foreach($production_data as $vals)
		{
			$main_data_arr[$vals[csf("country_id")]][$vals[csf("production_source")]][$vals[csf("challan_no")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]] +=$vals[csf("qnty")];
			$size_wise_main_data_arr[$vals[csf("country_id")]][$vals[csf("production_source")]][$vals[csf("challan_no")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("qnty")];

			$main_data_arr_fin[$vals[csf("serving_company")]][$vals[csf("color_number_id")]] +=$vals[csf("qnty")];
			$size_wise_main_data_arr_fin[$vals[csf("serving_company")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]] +=$vals[csf("qnty")];

		}

		?>



		<div id="details_reports">
			<div>
				<strong>Buyer Name : <? echo $job_array[$po]["buyer_name"]; ?>&nbsp;&nbsp;Job No: <? echo $job_array[$po]["job_no"]; ?>&nbsp;&nbsp;Style No : <? echo $job_array[$po]["style_ref_no"]; ?>&nbsp;&nbsp;Garments Item : <? echo $job_array[$po]["item_number_id"]; ?>&nbsp;&nbsp;<br>Order No : <? echo $job_array[$po]["po_number"]; ?>&nbsp;&nbsp;Date: <? echo change_date_format(str_replace("'", "", $dates)); ?>&nbsp;&nbsp;</strong>
			</div>
			<br>
			<table width="<? echo 230+($counts*45);?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
				<caption><strong>Summary</strong></caption>
				<thead>
					<tr>
						<th rowspan="2" width="30">SI</th>
						<th rowspan="2" width="100">Color</th>
						<th colspan="<? echo $counts;?>">Size</th>
						<th rowspan="2" width="100">Total</th>						 
					</tr>
					<tr>
						<?
						foreach($size_id_arr as $vals)
						{
							?>
							<th width="45"><? echo $sizearr[$vals]; ?></th>


							<?
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?
					$p=1;
					$size_wise_vertical_arr =array();

					foreach($col_id_arr as $col_id=>$size_val)
					{
								//foreach($size_val as $vals)
								//{
									//?>
									<tr>
										<td align="center" width="30"><?echo $p++; ?></td>
										<td align="center" width="100"><?echo $colorarr[$col_id]; ?></td>
										<?
										$total=0;
										foreach($size_id_arr as $vals)
										{
											?>
											<th width="45"><? echo $tot= $col_size_id_arr[$col_id][$vals]; ?></th>


											<?
											$total+=$tot;
											$size_wise_vertical_arr[$vals]+=$tot;
										}
										?>

										<td align="center" width="100"><?echo $total; ?></td>
									</tr>


									<?

								//}

					}
								?>
								<tr>
									<td colspan="2" align="right">&nbsp;</td>
									<?
									$total=0;
									foreach($size_id_arr as $vals)
									{
										?>
										<th width="45"><? echo $tot=$size_wise_vertical_arr[$vals]; ?></th>


										<?
										$total+=$tot;
										$size_wise_vertical_arr[$vals]+=$tot;
									}
									?>

									<td align="center" width="100"><?echo $total; ?></td>
								</tr>




				</tbody>
		    </table>
					<?
					if($type!=8)
					{
						?>
						<div> 
							<table width="<? echo 630+($counts*45);?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"   rules="all">
								<caption><strong>Details</strong></caption>
								<thead>
									<tr> 
										<th width="30" rowspan="2">SI</th>
										<th width="80" rowspan="2">Country Name</th>
										<th width="50" rowspan="2">Source</th>
										<th width="70" rowspan="2">Challan</th>
										<th width="100" rowspan="2">Sewing Unit</th>
										<th width="100" rowspan="2">Sewing Line</th>
										<th width="100" rowspan="2">Color</th>
										<th colspan="<? echo $counts;?>">Size</th>
										<th width="100" rowspan="2">Total</th>
									</tr>
									<tr>
										<?
										foreach($size_id_arr as $vals)
										{
											?>
											<th width="45"><? echo $sizearr[$vals]; ?></th>


											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="max-height:300px;  ">
								<table id="table_body"   width="<? echo 630+($counts*45);?>" border="1" rules="all" class="rpt_table"   >

									<tbody>
										<?
										$p=1;
										$size_wise_vertical_arr=array();

										foreach($main_data_arr as $c_id=>$source_data)
										{
											foreach($source_data as $s_id=>$challan_data)
											{
												foreach($challan_data as $ch_id=>$floor_data)
												{
													foreach($floor_data as $f_id=>$line_data)
													{
														foreach($line_data as $l_id=>$col_data)
														{
															foreach($col_data as $color_id=>$vals)
															{
																?>
																<tr>
																	<td align="center" width="30"><?echo $p++; ?></td>
																	<td align="center" width="80"><?echo $countryarr[$c_id]; ?></td>
																	<td align="center" width="50"><?echo $knitting_source[$s_id]; ?></td>
																	<td align="center" width="70"><?echo $ch_id; ?></td>
																	<td align="center" width="100"><?echo $floorarr[$f_id]; ?></td>
																	<td align="center" width="100">
																		<?
																		$lines=explode(",", $resourcearr[$l_id]); 
																		$line_names="";
																		foreach($lines as $v)
																		{
																			$line_names.=($line_names)? " , $linearr[$v]" : $linearr[$v];
																		}
																		echo $line_names;
																		?>

																	</td>
																	<td align="center" width="100"><?echo $colorarr[$color_id]; ?></td>
																	<?
																	$total=0;
																	foreach($size_id_arr as $vals)
																	{
																		?>
																		<td width="45" align="center"><? echo $tot=$size_wise_main_data_arr[$c_id][$s_id][$ch_id][$f_id][$l_id][$color_id][$vals]; ?></th>


																			<?
																			$total+=$tot;
																			$size_wise_vertical_arr[$vals]+=$tot;
																		}
																		?>


																		<td align="center" width="100"><?echo $total; ?></td>

																	</tr>


																	<?

																}

															}

														}
													}
												}

											}
											?>
											 



										</tbody>


									</table>
									<table  width="<? echo 630+($counts*45);?>" border="1" rules="all" class="rpt_table">
										<tr>
											<td align="center" width="30"> </td>
											<td align="center" width="80"> </td>
											<td align="center" width="50"> </td>
											<td align="center" width="70"> </td>
											<td align="center" width="100"> </td>
											<td align="center" width="100">		</td>									 
											<td align="center" width="100"><strong>Grand Total</strong></td>
											<?
											$total=0;
											$index=7;
											$id_arr=array();
											$index_array=array();
											$operation=array();
											$write_method=array();
											$kk=0;
											foreach($size_id_arr as $vals)
											{
												$id_arr[$kk]="size".$vals;
												$index_array[$kk]=$index;
												$operation[$kk]="sum";
												$write_method[$kk]="innerHTML";

												?>
												<td align="center" id="<? echo 'size'.$vals;?>" width="45"></td>


												<?
												$total+=$tot;
												$size_wise_vertical_arr[$vals]+=$tot;
												$kk++;
												$index++;
											}
											$id_arr[$kk]="all_total";
											$index_array[$kk]=$index;
											$operation[$kk]="sum";
											$write_method[$kk]="innerHTML";
											$id_arr=json_encode($id_arr);
											$index_array=json_encode($index_array);
											$operation=json_encode($operation);
											$write_method=json_encode($write_method);
											?>

											<td  id="all_total" align="center" width="100"></td>

										</tr>

									</table>
								</div>

								
								 <script type="text/javascript">
								 	var id_arr='<? echo $id_arr;?>';
								 	var id_arr=JSON.parse(id_arr); 

								 	var index_array='<? echo $index_array;?>';
								 	var index_array=JSON.parse(index_array); 

								 	var operation='<? echo $operation;?>';
								 	var operation=JSON.parse(operation); 

								 	var write_method='<? echo $write_method;?>';
								 	var write_method=JSON.parse(write_method); 
								 	//alert(id_arr+index_array+operation);
								 	var tableFilters1 = 
								 	{

								 		col_operation: {
								 			id: id_arr ,
								 			col: index_array,
								 			operation: operation,
								 			write_method: write_method
								 		}
								 	}
								 	setFilterGrid("table_body",-1,tableFilters1);
								 </script>


							</div>

						</div>


						<?

					}
					else if($type==8)
					{
						?>

						<div> 
							<table    width="<? echo 430+($counts*45);?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table"  rules="all">
								<caption><strong>Details</strong></caption>
								<thead>
									<tr> 
										<th width="30" rowspan="2">SI</th>
										<th width="100" rowspan="2">Working Company</th>
										<th width="100" rowspan="2">Color</th>
										<th colspan="<? echo $counts;?>">Size</th>
										<th width="100" rowspan="2">Total</th>
									</tr>
									<tr>
										<?
										foreach($size_id_arr as $vals)
										{
											?>
											<th width="45"><? echo $sizearr[$vals]; ?></th>


											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="max-height:300px;  ">
								<table id="table_body"   width="<? echo 430+($counts*45);?>" border="1" rules="all" class="rpt_table" >

									<tbody>
										<?
										$p=1;
										$size_wise_vertical_arr=array();

										foreach($main_data_arr_fin as $c_id=>$color_data)
										{
											foreach($color_data as $col_id=>$vals)
											{
												?>
												<tr>
													<td align="center" width="30"><?echo $p++; ?></td>
													<td align="center" width="100"><?echo $companyarr[$c_id]; ?></td>
													<td align="center" width="100"><?echo $colorarr[$col_id]; ?></td>



													<?
													$total=0;
													foreach($size_id_arr as $vals)
													{
														?>
														<td width="45" align="center"><? echo $tot=$size_wise_main_data_arr_fin[$c_id][$col_id][$vals]; ?></th>


															<?
															$total+=$tot;
															$size_wise_vertical_arr[$vals]+=$tot;
														}
														?>


														<td align="center" width="100"><?echo $total; ?></td>

													</tr>


													<?

												}

											}
											?>
											



										</tbody>


									</table>
								</div>
								<table   width="<? echo 430+($counts*45);?>" border="1" rules="all" class="rpt_table" >
								 <tfoot>
									<tr>
										<td align="center" width="30"> </td>
										<td align="center" width="100"> </td>
										<td align="center" width="100"><strong>Grand Totals</strong></td>

										<?
										$total=0;
										$index=3;
										$id_arr=array();
										$index_array=array();
										$operation=array();
										$write_method=array();
										$kk=0;
										foreach($size_id_arr as $vals)
										{
											$id_arr[$kk]="size".$vals;
											$index_array[$kk]=$index;
											$operation[$kk]="sum";
											$write_method[$kk]="innerHTML";
											?>
											<td align="center"  id="<? echo 'size'.$vals;?>" width="45"></td>


											<?
											$total+=$tot;
											$size_wise_vertical_arr[$vals]+=$tot;
											$kk++;
											$index++;
										}
										$id_arr[$kk]="all_total";
										$index_array[$kk]=$index;
										$operation[$kk]="sum";
										$write_method[$kk]="innerHTML";
										$id_arr=json_encode($id_arr);
										$index_array=json_encode($index_array);
										$operation=json_encode($operation);
										$write_method=json_encode($write_method);
										?>

										<td  id="all_total" align="center" width="100"> </td>
									</tr>
								 </tfoot>
								</table>
								 <script type="text/javascript">
								 	var id_arr='<? echo $id_arr;?>';
								 	var id_arr=JSON.parse(id_arr); 

								 	var index_array='<? echo $index_array;?>';
								 	var index_array=JSON.parse(index_array); 

								 	var operation='<? echo $operation;?>';
								 	var operation=JSON.parse(operation); 

								 	var write_method='<? echo $write_method;?>';
								 	var write_method=JSON.parse(write_method); 
								 	//alert(id_arr+index_array+operation+write_method);
								 	var tableFilters1 = 
								 	{

								 		col_operation: {
								 			id: id_arr,
								 			col: index_array,
								 			operation: operation,
								 			write_method: write_method
								 		}
								 	}
								 	setFilterGrid("table_body",-1,tableFilters1);
								 </script>


							</div>

						</div>


						<?

					}
					?>


		</div>
		<script>   //setFilterGrid("table_body",-1);  </script> 



				
				<?

	}

 	?>
 	          
  	  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  	  </body>
   	 </html>
    
    <?	
	exit();
}
?>