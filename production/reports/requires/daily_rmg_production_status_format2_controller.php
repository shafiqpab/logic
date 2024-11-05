<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );

if (!function_exists("pre")) 
{
	function pre($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	} 	 
}
if($action=="print_button_variable_setting")
{
	 
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=7 and report_id=80 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit(); 
}

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(40) and is_deleted=0 and status_active=1");
 	echo trim($print_report_format);	
	exit();

}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/daily_rmg_production_status_format2_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();  	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();   	 
}

if ($action=="load_drop_down_buyer")
{
	//echo $data; exit();
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name ","id,buyer_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/daily_rmg_production_status_format2_controller',this.value, 'load_drop_down_season_buyer', 'season_td' );load_drop_down( 'requires/daily_rmg_production_status_format2_controller',this.value, 'load_drop_down_brand', 'brand_td' )" );     	 
	exit();
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_name", 100, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "--Select Brand--", $selected, "" );
	exit();
}
if ($action=="load_drop_down_season_buyer")
{
    echo create_drop_down( "cbo_season_name", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
    exit();
}


if ($action=="load_drop_down_buyer_popup")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}
 
if($action=="job_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
    <script>
		
		var selected_id = new Array;	
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{ 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( job_id )
		{
			var arrs=job_id.split("_");
 			document.getElementById('selected_id').value=arrs[0];
 			document.getElementById('selected_name').value=arrs[1]; 
			parent.emailwindow.hide();
		}

		function dynamic_ttl_change(data)
		{
			var titles="";
			if(data==1)
			{
				titles="Job No";
			}
			else if(data==2)
			{
				titles="Style Ref."
			}
			else if(data==3)
			{
				titles="Po No.";
			}
			else
			{
				titles="Job No";
			}
			$("#dynamic_ttl").html(titles);
			$("#dynamic_ttl").css('color','blue');
		}
		
		
		
    </script>

    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>
                        
                        <tr>                	 
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="130" class="">Buyer Name</th>
                            <th width="100" class="must_entry_caption">Search By</th>
                            <th width="100" id="dynamic_ttl"class="must_entry_caption">Job No</th>
                             <th>&nbsp;</th>
                        </tr>           
                    </thead>
                    <tr class="general">
                        <td>
                        <input type="hidden" id="selected_id">
                        <input type="hidden" id="selected_name"> 
                            <?
                            $search_by_arr=[1=>"Job No",2=>"Style Ref.",3=>"Po No"];
                             echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'daily_rmg_production_status_format2_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td_popup' );" );

                             ?>
                        </td>
                        <td id="buyer_td_popup"><? asort($buyer_arrs);echo create_drop_down( "cbo_buyer_name", 130, $buyer_arrs,'', 1, "-- Select Buyer --" ); ?></td>
                        <td>
	                        <? echo create_drop_down( "cbo_search_by", 100, $search_by_arr,'',0, "-- Select--", '',"dynamic_ttl_change(this.value);" );
	                        ?>
                        	
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_job_po_style_no" id="txt_job_po_style_no" /></td>
                        <input type="hidden" name="hidden_job_year" id="hidden_job_year" value="<? echo $job_year;?>">
                        
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_job_po_style_no').value+'_'+document.getElementById('hidden_job_year').value, 'create_job_list_view', 'search_div', 'daily_rmg_production_status_format2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                    </tr>
                    
                </table>
            </form>
        </div>
        <div id="search_div"></div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}

if($action=="create_job_list_view")
{
	$data=explode('_',$data);
	if(!$data[0])
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 14px;">Select Company Name</div>';die;
	}
	elseif($data[3]=="")
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 14px;">Please enter search string</div>';die;
	}
	$str_cond="";
	$str_cond.=($data[0])? " and a.company_name='$data[0]' " : "";
	$str_cond.=($data[1])? " and a.buyer_name='$data[1]' " : "";
	if($data[3])
	{
		if($data[2]==1)
		{
			$str_cond.= " and a.job_no_prefix_num='$data[3]'";

		}
		else if($data[2]==2)
		{
			$str_cond.= " and a.style_ref_no like '%$data[3]%'";

		}
		else if($data[2]==3)
		{
			$str_cond.= " and b.po_number like '%$data[3]%'";

		}
	}
	if($data[4])
	{
	   if($db_type==2)
	   {
	   	 $str_cond.=" and to_char(a.insert_date,'YYYY')='$data[4]'";
	   }
	   else
	   {
	   		$str_cond.=" and year(a.insert_date)='$data[4]'";
	   }
	}
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	 $sql= "SELECT a.id,b.po_number,a.job_no_prefix_num as job_no,a.style_ref_no,a.company_name,a.buyer_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $str_cond  group by a.id,b.po_number,a.job_no_prefix_num,a.style_ref_no,a.company_name,a.buyer_name";
	 // echo $sql;die;
	echo  create_list_view("list_view", "Company,Buyer Name,Job No,Style,Po No", "120,100,100,100,140","600","290",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,style_ref_no,po_number", "",'','0,0,0,0,0') ;
	exit();
} 

if($action=="sewingQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo"<pre>";print_r($_REQUEST);die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"); 
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$sizearr_order=return_library_array("select size_number_id, size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	//var_dump();die;
	
	$po_details_sql=sql_select("SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id,b.grouping");
	// echo"<pre>";
	// print_r($po_details_sql);die;
	$serving_company_sql=sql_select("select a.company_id from pro_garments_production_mst a, wo_po_break_down b where a.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.company_id");
	$serving_company_id=$serving_company_sql[0][csf("company_id")];
	// echo"<pre>";
	// print_r($serving_company_id);die;
	// echo $serving_company_id;die;
	//For Show Date Location and Floor 
	$ex_item_id=explode("__",$gmts_item_id);
	$gmt_item_id=$ex_item_id[0];
	$serving_comp_id=$ex_item_id[1];
	
	if($location_id!=0) $location_cond=" and a.location in($location_id)"; else  $location_cond=""; 
	if($floor_id!=0) $floor_cond=" and a.floor_id in($floor_id)"; else  $floor_cond="";
	if($sewing_line!=0) $sewing_line_cond=" and a.sewing_line in($sewing_line)"; else  $sewing_line_cond="";
	if($serving_comp_id!=0) $serving_comp_cond=" and a.serving_company in($serving_comp_id)"; else $serving_comp_cond="";
	
	$sql_cond= '';
	$sql_cond .= $page 		 	 	? " and a.production_type=$page " 		 			: "";
	$sql_cond .= $gmt_item_id 	 	? " and a.item_number_id=$gmt_item_id " 			: "";
	$sql_cond .= $color		 	 	? " and c.color_number_id=$color " 	 				: "";
	$sql_cond .= $production_date 	? " and a.production_date='$production_date' " 	 	: "";
	$sql_cond .= $po_break_down_id 	? " and a.po_break_down_id='$po_break_down_id' " 	: "";
	$sql_cond .= $prod_source 		? " and a.production_source='$prod_source' " 		: "";

	$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, a.serving_company, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id, a.prod_reso_allo  
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where  a.id=b.mst_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0 $location_cond  $floor_cond $sewing_line_cond $serving_comp_cond $sql_cond and a.po_break_down_id = c.po_break_down_id
	group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.prod_reso_allo,c.color_number_id
	order by a.country_id,a.floor_id, a.sewing_line"; // a.challan_no, 
	
	// echo $sql;die;

	$nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$serving_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
    // $prod_reso_allocation = 1;
	
	// echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	
	$sql_cond2= '';
	$sql_cond2.= $page 		 	 	? " and a.production_type=$page " 		 			: "";
	$sql_cond2.= $gmt_item_id 	 	? " and a.item_number_id=$gmt_item_id " 			: "";
	$sql_cond2.= $color		 	 	? " and c.color_number_id=$color " 	 				: "";
	$sql_cond2.= $production_date 	? " and a.production_date='$production_date' " 	 	: "";
	$sql_cond2.= $po_break_down_id 	? " and a.po_break_down_id='$po_break_down_id' " 	: "";
	$sql_cond2.= $prod_source 		? " and a.production_source='$prod_source' " 		: "";

	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a, pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where  a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond  $floor_cond $sewing_line_cond $serving_comp_cond $sql_cond2 and a.po_break_down_id = c.po_break_down_id and c.status_active in(1,2,3)  and c.is_deleted=0 
	group by a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id");
 
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	//$table_width=630+$col_width;
	if($prod_source==3) $table_width=750+$col_width; else $table_width=630+$col_width;
	$summer_table_width=230+$col_width;
	?>	
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}
		
		function window_close()
		{
			parent.emailwindow.hide();
		}
		
	</script>	
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
			<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
	    <div width="100%" id="report_container">
	    
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">
	                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Internal Ref : <? echo $po_details_sql[0][csf("grouping")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item : 
					<?
	                    $item_data=""; 
	                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")])); 
	                    foreach($garments_item_arr as $item_id)
	                    {
	                        if($item_data!="") $item_data .=", ";
	                        $item_data .=$garments_item[$item_id];
	                    }
	                    echo $item_data;
	                ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?> 
	                <br />
	                Summary
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				//var_dump($result);die;
				foreach($summery_data as $color_id=>$row)
				{
					?>
	                <tr>
	                    <td valign="middle" align="center"><? echo $i;  ?></td>
	                    <td valign="middle" ><? echo $colorarr[$color_id];  ?></td>
	                    <?
						$summry_color_total_in =0;
	                    foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                        <td valign="middle" align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
	                        <?
	                    }
	                    ?>
	                    <td valign="middle" align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            </tbody>
	            <tfoot>
	                <th colspan="2">Total :</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">Details</td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                    <th width="80" rowspan="2">Source</th>
	                    <?
						if($prod_source==3)
						{
							?>
	                    	<th width="120" rowspan="2">Serving Company</th>
	                        <?
						}
						?>
	                    <th width="70" rowspan="2">Challan</th>
	                    
							<? 
								if($page==11){
									?>
					    <th width="90" rowspan="2" colspan="2">Floor/Unit</th>
					        <?
								}else{
									?>
						<th width="90" rowspan="2">Sewing Unit</th>
						<th width="70" rowspan="2">Sewing Line</th>
						    <?
								}
							?>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	                
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				$line_color_size_in = array();
				foreach($result as $row)
				{
					if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]][$row[csf("floor_id")]]))
					{
						$temp_arr[$row[csf("country_id")]][$row[csf("floor_id")]][]=$row[csf("sewing_line")];
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
                                <td  colspan="<?=$prod_source==3 ? 8: 7;?>" align="right"><strong>(<?=$sewing_line?>) Total :</strong></td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in[$sewing_line][$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
						}
                        $line_color_total_in = 0;
						$k++;
					}
                    $sewing_line='';
                    if($prod_reso_allocation==1)
                    {
                        $line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
                        foreach($line_number as $val)
                        {
                            if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
                        }
                    }
                    else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];
					?>
	                <tr>
	                    <td valign="middle" align="center"><? echo $i;  ?></td>
	                    <td valign="middle" ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
	                    <td valign="middle" ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
	                    <?
						if($prod_source==3)
						{
							?>
	                    	<td valign="middle" ><p><? echo $supplier_arr[$row[csf("serving_company")]];  ?></p></td>
	                        <?
						}
						?>
	                    <td valign="middle" ><p><? echo $row[csf("challan_no")];  ?></p></td>
							<? if($page==11){
								?>
						<td valign="middle" colspan="2"><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
								<?
							}else{
								?>
                        <td valign="middle" ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
						<td valign="middle" align="center"><p><? echo $sewing_line;  ?></p></td>
								<?
							}?>
	                    <td valign="middle" ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
	                    <?
						$color_total_in=0;
						
	                    foreach($sizearr_order as $size_id)
	                    {
							$Production_qty=0;
	                        ?>
	                        <td valign="middle" align="right"><p>
							<?
								$Production_qty=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 	echo number_format($Production_qty,0);
								 $color_total_in+=$Production_qty;
                                 $color_size_in[$size_id]+=$Production_qty;
                                 $line_color_total_in+=$Production_qty;
                                 $line_color_size_in[$sewing_line][$size_id]+=$Production_qty;
							 ?>
	                        </p></td>
	                        <?
	                    }
	                    ?>
	                    <td valign="middle" align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            <tr bgcolor="#CCCCCC">
                    <td  colspan="<?=$prod_source==3 ? 8: 7;?>" align="right"><strong>(<?=$sewing_line?>) Total :</strong></td>

                    <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in[$sewing_line][$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
                <th  colspan="<?=$prod_source==3 ? 8: 7;?>" align="right"><strong>Grand Total :</strong></th>

                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
	exit();	
}

if($action=="orderQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	
	//$sql= "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*(select from wo_po_details_mas_set_details set where set.job_no=a.job_no and set.gmts_item_id=$gmts_item_id) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.garments_nature='$garments_nature' and a.is_deleted=0 and a.status_active=1";
	//echo $sql;
	echo "<br />". create_list_view ( "list_view", "Order No,Order Qnty,Pub Shipment Date", "200,120,220","540","220",1, "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*a.total_set_qnty as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.is_deleted=0 and a.status_active=1", "", "","", 1, '0,0,0', $arr, "po_number,po_quantity,pub_shipment_date","../requires/date_wise_prod_without_cm_report_controller", '','0,1,3');
	exit();
}
if ($action=='gmt_finishing_receive')  // Finishing Rcv Popup
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
       		$item_arr=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
				$company_short_arr=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
				$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
				$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
				$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
				$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
				$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
        	   $order_cond= where_con_using_array(array_unique(explode(",",$po_break_down_id)),0,"c.id") ;
        	  
        	    $txt_date=" and a.receive_date='$rcv_date'";
        	    $item_id_cond=" and d.item_id='$item_id'";
        	  
        		$sql="SELECT d.id,
				       a.sys_no,
				       b.job_no,
				       b.style_ref_no,
				       c.po_number,
				       c.grouping,
				       a.company_id,
				       d.location_id,
				       d.floor_id,
				       d.qc_pass_qnty,
				       d.fin_receive_qnty,
				       d.po_break_down_id,
				       d.lc_company_id,
				       b.buyer_name,
				       a.fini_location_id,
				       a.floor_id AS fin_floor_id,
				       c.shipment_date,
				       c.po_quantity,
				       b.gmts_item_id,
				       a.receive_date,
				       d.color_id,
   					   d.size_id,
   					   a.sys_number_prefix_num,
   					   d.item_id
				  FROM gmt_finishing_receive_mst a,
				       wo_po_details_master b,
				       wo_po_break_down c,
				       gmt_finishing_receive_dtls d
				 WHERE     a.id = d.mst_id
				       AND d.po_break_down_id = c.id
				       AND b.job_no = c.job_no_mst
				       AND a.status_active = 1
				       AND d.status_active = 1
				       AND c.status_active = 1
				       AND d.status_active = 1
				       AND a.company_id = $company_id
				       $order_cond
				        $txt_date
				       $item_id_cond
				
				";
			//echo $sql;
			$result=sql_select($sql);
			$po_wise_data=array();
			$color_wise_data=array();
			$size_id_arr=array();
			$buyer='';
			$job_no='';
			$buyer='';
			$po_number='';
			$gmts_item_id='';
			$receive_date='';
			foreach ($result as $row) 
			{
				$buyer.=$buyer_arr[$row[csf('buyer_name')]]."***";
				$gmts_item_id.=$item_arr[$row[csf('item_id')]]."***";
				$job_no.=$row[csf('buyer_name')]."***";
				$style_ref_no.=$row[csf('style_ref_no')]."***";
				$po_number.=$row[csf('po_number')]."***";
				$receive_date.=change_date_format($row[csf('receive_date')])."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['buyer_name'].=$buyer_arr[$row[csf('buyer_name')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['job_no'].=$row[csf('job_no')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['style_ref_no'].=$row[csf('style_ref_no')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['po_number'].=$row[csf('po_number')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['grouping'].=$row[csf('grouping')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['gmts_item_id'].=$item_arr[$row[csf('item_id')]]."***";
				//$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['gmts_item_id'].=$item_arr[$row[csf('gmts_item_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['fin_floor_id'].=$floor_arr[$row[csf('fin_floor_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['sew_floor_id'].=$floor_arr[$row[csf('floor_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['fini_location_id'].=$location_arr[$row[csf('fini_location_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['fini_company'].=$company_short_arr[$row[csf('company_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['sew_comapny'].=$company_short_arr[$row[csf('lc_company_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['sew_location'].=$location_arr[$row[csf('location_id')]]."***";

				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['po_quantity']+=$row[csf('po_quantity')];
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['rcv_no'].=$row[csf('sys_number_prefix_num')]."***";

				$po_size_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('fin_receive_qnty')];
				$color_wise_data[$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('fin_receive_qnty')];
				array_push($size_id_arr, $row[csf('size_id')]);
			}
			$size_id_arr=array_unique($size_id_arr);
	
 	?>

    <fieldset>
		 <div id="data_panel" align="center" style="width:100%">
	        <script>
			      function new_window()
			      {
				       var w = window.open("Surprise", "#");
				       var d = w.document.open();
				       d.write(document.getElementById('details_reports').innerHTML);
				       d.close();
			      }
	        </script>
	 		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
    <div style="margin:2px" id="details_reports">
    	<h4>Buyer Name : <?=implode(", ", array_unique(explode("***", chop($buyer,"***"))));?>     Job No : <?=implode(", ", array_unique(explode("***", chop($job_no,"***"))));?>     Style No : <?=implode(", ", array_unique(explode("***", chop($style_ref_no,"***"))));?>     Garments Item : <?=implode(", ", array_unique(explode("***", chop($gmts_item_id,"***"))));?>  </h4>
    	<h4>Order No : <?=implode(", ", array_unique(explode("***", chop($po_number,"***"))));?>    Date : <?=implode(", ", array_unique(explode("***", chop($receive_date,"***"))));?></h4>
    	<br>
        <table width="95%" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
        	<caption style="justify-content: left;text-align: left;font-weight: bold;">Summary</caption>
            <thead>
                    <tr>
                        <th width="50" rowspan="2">Sl.</th>  
                        <th width="100" rowspan="2">Color</th>  
                        <th colspan="<?=count($size_id_arr);?>" width="<?=count($size_id_arr)*60;?>">Size</th>  
                       
                        <th rowspan="2">Total</th>
               		</tr>
               		<tr>
               			<?
               				foreach ($size_id_arr as $size_id) 
               				{
               					?>
               					<th width="60"><?=$size_arr[$size_id];?></th>
               					<?
               				}
               			?>
               		</tr>
            </thead>
            <tbody>
            	<?
            		
            	   
				$i=1;
				$sum_total=0;
				$size_total_arr=array();
				foreach ($color_wise_data as $color_id => $color_data) 
				{
					 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
            		?>
	            	 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    	<td ><? echo $i;?></td>
                    	<td ><? echo $color_arr[$color_id];?></td>
                    	<?
                    		$color_total=0;
               				foreach ($size_id_arr as $size_id) 
               				{
               					?>
               					<td><?=fn_number_format($color_data[$size_id],0,".",",");?></td>
               					<?
               					$sum_total+=$color_data[$size_id];
               					// $size_total_arr[$color_id]+=$color_data[$size_id];
               					$size_total_arr[$size_id]+=$color_data[$size_id];
               					$color_total+=$color_data[$size_id];
               				}
               			?>
                    	<td ><? echo fn_number_format($color_total,0,".",",");?></td>
	            		
	            	</tr>
	            	<?
	            	$i++;
	            }
	            ?>

            </tbody>
            <tfoot>
            	<tr>
            		<td></td>
            		<td></td>
        		     <?
        		     		
						foreach ($size_id_arr as $size_id) 
						{
							?>
							<td width="60"><?=fn_number_format($size_total_arr[$size_id],0,".",",");?></td>
							<?
							
						}
					?>
					<td ><? echo fn_number_format($sum_total,0,".",",");?></td>
            	</tr>
           </tfoot>
        </table>
        <br>
       <table width="95%" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
       		<caption style="justify-content: left;text-align: left;font-weight: bold;">Details</caption>
            <thead>
                    <tr>
                        <th width="50"  rowspan="2">Sl.</th>    
                        <th width="70"  rowspan="2">Rcv ID</th>    
                        <th width="110" rowspan="2">Finishing Company</th>
                        <th width="110" rowspan="2">Finishing Floor</th>
                        <th width="110" rowspan="2">Sewing Company</th>
                        <th width="110" rowspan="2">Sewing Floor</th>
                       
                        <th width="110" rowspan="2">Color</th>
                       	<th colspan="<?=count($size_id_arr);?>" width="<?=count($size_id_arr)*60;?>">Size</th>
                         <th width="80" rowspan="2">Total</th>
               		</tr>
               		<tr>
               			<?
               				foreach ($size_id_arr as $size_id) 
               				{
               					?>
               					<th width="60"><?=$size_arr[$size_id];?></th>
               					<?
               				}
               			?>
               		</tr>
            </thead>
            <tbody>
            	<?
            		
				$i=1;
				$rcv_qnt=0;
				$sum_total=0;
				$size_total_arr=array();
				foreach ($po_wise_data as $po_id => $po_data) 
				{
					foreach ($po_data as $color_id => $color_data) 
					{
						# code...
						
						 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
	            		?>
		            	 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
	                    	<td ><? echo $i;?></td>
	                    	
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['rcv_no'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['fini_company'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['fin_floor_id'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['sew_comapny'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['sew_floor_id'],"***"))));;?></td>
	                    	<td ><? echo $color_arr[$color_id];?></td>
                	     	<?
                	     		$color_total=0;
                					foreach ($size_id_arr as $size_id) 
                					{
                						?>
                						<td><?=fn_number_format($po_size_wise_data[$po_id][$color_id][$size_id],0,".",",");?></td>
                						<?
                						$sum_total+=$po_size_wise_data[$po_id][$color_id][$size_id];
                						// $size_total_arr[$color_id]+=$po_size_wise_data[$po_id][$color_id][$size_id];
                						$size_total_arr[$size_id]+=$po_size_wise_data[$po_id][$color_id][$size_id];
                						$color_total+=$po_size_wise_data[$po_id][$color_id][$size_id];
                					}
                				?>
                			<td ><? echo fn_number_format($color_total,0,".",",");?></td>
		            		
		            	<?
		            	$i++;
		            	$rcv_qnt+=$row[csf('fin_receive_qnty')];
		            }
	            }
	            ?>
            </tbody>
            <tfoot>
            	
        		<tr>
            		
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
        		     <?
        		     		
						foreach ($size_id_arr as $size_id) 
						{
							?>
							<td width="60"><?=fn_number_format($size_total_arr[$size_id],0,".",",");?></td>
							<?
							
						}
					?>
					<td ><? echo fn_number_format($sum_total,0,".",",");?></td>
            	</tr>
            	
            </tfoot>
        </table>
       </div>
     </div>
     </fieldset>
    <?
 	
 exit();
 
}

if($action=="finishQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;
	
	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;
	
	if($db_type==2)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id 
		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
		where a.production_type=8 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0 
		group by  a.challan_no, a.floor_id, a.country_id, c.color_number_id
		order by a.country_id, a.challan_no, a.floor_id";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, group_concat(c.size_number_id) as size_number_id
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=8 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0
		group by a.country_id,a.challan_no, a.floor_id, c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	
	
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	
	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, c.size_number_id,
	 sum(case when production_source=1 then b.production_qnty else 0 end) as in_quantity,
	 sum(case when production_source=3 then b.production_qnty else 0 end) as out_quantity,
	 sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=8 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0 
	group by a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id");
	
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['in'] +=$row[csf('in_quantity')];
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['out'] +=$row[csf('out_quantity')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;
	?>	
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}
		
		function window_close()
		{
			parent.emailwindow.hide();
		}
		
	</script>	
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item : 
				<?
                    $item_data=""; 
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")])); 
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?> 
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
					<?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th><? echo $grand_tot_in; ?></th>
            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="3">SI</th>
                    <th width="100" rowspan="3">Country Name</th>
                    <th width="80" rowspan="3">Source</th>
                    <th width="70" rowspan="3">Challan</th>
                    <th width="70" rowspan="3">Floor</th>
                    <th width="100" rowspan="3">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">In-House</th>
                    <th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Out-Bound</th>
                    <th width="80" rowspan="3" >Total</th>
                </tr>
                <tr>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
							$production_break_qty_in=0;
							$production_break_qty_in=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$size_id]['in'];
						 	echo number_format($production_break_qty_in,0) ;
							
							 $color_total_in+= $production_break_qty_in; 
							 $color_size_in [$size_id]+=$production_break_qty_in;
						 ?>
                        </p></td>
                        <?
                    }
					$color_total_out=0;
					foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
							$production_break_qty_out=0;
							$production_break_qty_out=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$size_id]['out'];
						 	echo number_format($production_break_qty_out,0) ;
							
							 $color_total_out+= $production_break_qty_out; 
							 $color_size_out[$size_id]+=$production_break_qty_out;
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? $color_total=$color_total_in+$color_total_out; echo  number_format( $color_total,0); $grand_tot_in+=$color_total; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
                {
                    ?>
                    <th align="right"><? echo number_format($color_size_in[$size_id],0); ?></th>
                    <?
                }
				foreach($sizearr_order as $size_id)
                {
                    ?>
                    <th align="right"><? echo number_format($color_size_out[$size_id],0); ?></th>
                    <?
                }
				?>
                <th><? echo $grand_tot_in; ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
	<?	
	exit();
}

if ($action=="reject_qty")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $po_id;
	//echo $company_name;die;
	if($db_type==0)
		{
			$prod_date="and a.production_date ='".change_date_format($production_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$prod_date="and a.production_date = '".change_date_format($production_date,'','',1)."'";	
		}
		
	$sql_variable=sql_select("select cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update from variable_settings_production where company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0");
	$cutting_variable=$sql_variable[0][csf('cutting_update')];
	$printing_variable=$sql_variable[0][csf('printing_emb_production')];
	$sewing_variable=$sql_variable[0][csf('sewing_production')];
	$iron_variable=$sql_variable[0][csf('iron_update')];
	$finishing_variable=$sql_variable[0][csf('finishing_update')];
	//echo $service_company;
	//$cutting_variable_setting=return_field_value("cutting_update","variable_settings_production","company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0","cutting_update");
	$po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
	$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id in($po_id)","size_number_id","size_number_id");
	
	if($cutting_variable==1)
	{
		$sql_cutting=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS cutting_rej_qnty
					from pro_garments_production_mst  a
					where a.production_type =1 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 
					and a.is_deleted=0 and a.serving_company=$service_company and $prod_date group by po_break_down_id");
	}
	else
	{
		
		 $sql_cutting=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS cutting_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=1 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0  and a.serving_company=$service_company $prod_date group by a.po_break_down_id,c.color_number_id, c.size_number_id");
		
		foreach($sql_cutting as $row)
		{
			if($row[csf('cutting_rej_qnty')]>0)
			{
				$cutting_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('cutting_rej_qnty')];
			}
		}
	}
	
	//var_dump($cutting_data);die;
	
	if($printing_variable==1)
	{
		$sql_printing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS printing_rej_qnty
					from pro_garments_production_mst a 
					where a.production_type =3 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company and  $prod_date  group by po_break_down_id");
	}
	else
	{
		$sql_printing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS printing_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=3 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by a.po_break_down_id,c.color_number_id, c.size_number_id");
		
		foreach($sql_printing as $row)
		{
			if($row[csf('printing_rej_qnty')]>0)
			{
				$printing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('printing_rej_qnty')];
			}
		}
	}
	
	if($sewing_variable==1)
	{
		$sql_sewing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS sewingout_rej_qnty
					from pro_garments_production_mst a 
					where a.production_type =5 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date group by po_break_down_id");
	}
	else
	{
		$sql_sewing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS sewingout_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=5 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date group by a.po_break_down_id,c.color_number_id, c.size_number_id");
					
		foreach($sql_sewing as $row)
		{
			if($row[csf('sewingout_rej_qnty')]>0)
			{
				$sewing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('sewingout_rej_qnty')];
			}
		}
	}
	
	if($iron_variable==1)
	{
		$sql_iron=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS iron_rej_qnty
					from pro_garments_production_mst a 
					where a.production_type =7 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by po_break_down_id");
	}
	else
	{
		$sql_iron=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS iron_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=7 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by a.po_break_down_id,c.color_number_id, c.size_number_id");
		
		foreach($sql_iron as $row)
		{
			if($row[csf('iron_rej_qnty')]>0)
			{
				$iron_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('iron_rej_qnty')];
			}
		}
	}
	
	if($finishing_variable==1)
	{
		$sql_finishing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS finish_rej_qnty
					from pro_garments_production_mst a
					where a.production_type =8 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by po_break_down_id");
	}
	else
	{
		$sql_finishing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS finish_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=8 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by a.po_break_down_id,c.color_number_id, c.size_number_id");
		
		foreach($sql_finishing as $row)
		{
			if($row[csf('finish_rej_qnty')]>0)
			{
				$finishing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('finish_rej_qnty')];
			}
		}
	}
	
	
	?>
    <div id="data_panel" align="center" style="width:100%">
		<script>
        function new_window()
        {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write(document.getElementById('details_reports').innerHTML);
        d.close();
        }
        </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </div>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
    <div style="width:635px" align="center" id="details_reports"> 
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="60">Buyer</th>
                <th width="90">Job Number</th>
                <th width="90">Style Name</th>
                <th width="150">Order Number</th>
                <th width="70">Ship Date</th>
                <th width="100">Item Name</th>
                <th >Order Qty.</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			if($db_type==0)
			{
 				$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				$sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
			}
			//echo $sql;
			$resultRow=sql_select($sql);
				
 		?> 
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
            <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
            <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
            <td><? echo $garments_item[$item_id]; ?></td>
            <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
        </tr>
    </table>
    <br />
    <?
	
	
	//Cutting Data Display Here
	if($cutting_variable==1)
	{
		if(!empty($sql_cutting))
		{
			 $tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Cutting Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_cutting as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td align="right"><? echo number_format($row[csf("cutting_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	else
	{
			$collspan=count($sizearr_order);
			$table_width=(230+($collspan*60));
			$colspan=2;
		
		if(!empty($sql_cutting))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Cutting Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_cutting=0;
					foreach($cutting_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_cutting=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<? 
										echo number_format($cutting_data[$order_id][$color_id][$size_id],0);
										$color_total_cutting+= $cutting_data[$order_id][$color_id][$size_id];
										$color_size_cutting [$size_id]+=$cutting_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_cutting,0); $grand_total_cutting+=$color_total_cutting;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_cutting [$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_cutting,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	//emblish Data Display Here
	if($printing_variable==1)
	{
		if(!empty($sql_printing))
		{
			$tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Embellishment Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_printing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td align="right"><? echo number_format($row[csf("printing_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
		}
	}
	else
	{
		$collspan=count($sizearr_order);
			$table_width=(230+($collspan*60));
			$colspan=2;
		if(!empty($sql_printing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Embellishment Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th>Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1; $grand_total_printing=0;
					foreach($printing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_printing=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<? 
										echo number_format($printing_data[$order_id][$color_id][$size_id],0);
										$color_total_printing+= $printing_data[$order_id][$color_id][$size_id];
										$color_size_printing[$size_id]+=$printing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_printing,0); $grand_total_printing+=$color_total_printing;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_printing[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_printing,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	
	//Sewing Data Display Here
	if($sewing_variable==1)
	{
		if(!empty($sql_sewing))
		{
			 $tbl_width=250; 
			?>
            <span style="font-size:18px; font-weight:bold;">Sewing Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_sewing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                           
                            <td align="right"><? echo number_format($row[csf("sewingout_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		$table_width=(230+($collspan*60));
		$colspan=2;
		if(!empty($sql_sewing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Sewing Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_sewing=0;
					foreach($sewing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_sewing=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<? 
										echo number_format($sewing_data[$order_id][$color_id][$size_id],0);
										$color_total_sewing+= $sewing_data[$order_id][$color_id][$size_id];
										$color_size_sewing[$size_id]+=$sewing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_sewing,0); $grand_total_sewing+=$color_total_sewing;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_sewing[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_sewing,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	
	//Iron Data Display Here
	if($iron_variable==1)
	{
		if(!empty($sql_iron))
		{
			 $tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Iron  Reject Quantity</span>
            <table width="<? echo $tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_iron as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td align="right"><? echo number_format($row[csf("iron_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		$table_width=(230+($collspan*60));
			$colspan=2;
		if(!empty($sql_iron))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Iron Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_iron=0;
					foreach($iron_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                               
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_iron=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<? 
										echo number_format($iron_data[$order_id][$color_id][$size_id],0);
										$color_total_iron+= $iron_data[$order_id][$color_id][$size_id];
										$color_size_iron[$size_id]+=$iron_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_iron,0); $grand_total_iron+=$color_total_iron;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_iron[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_iron,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	
	//Finish Data Display Here
	if($finishing_variable==1)
	{
		if(!empty($sql_finishing))
		{
			 $tbl_width=250; 
			?>
            <span style="font-size:18px; font-weight:bold;">Finishing  Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_finishing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td align="right"><? echo number_format($row[csf("finish_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		$table_width=(230+($collspan*60));
		$colspan=2;
		if(!empty($sql_finishing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Finishing Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th>Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_finish=0;
					foreach($finishing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_finish=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<? 
										echo number_format($finishing_data[$order_id][$color_id][$size_id],0);
										$color_total_finish+= $finishing_data[$order_id][$color_id][$size_id];
										$color_size_finish[$size_id]+=$finishing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_finish,0); $grand_total_finish+=$color_total_finish;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_finish[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_finish,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <?
		}
	}
	?>
    </div>
    <?
	exit();
	
}

if($action=='date_wise_production_report') 
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 	?>
		<div id="view_part" class="view_part">
			<fieldset style="width:505px">
				<legend>Cutting</legend>
				<? 
					$i=1;
					$sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1  and  a.po_break_down_id='$po_break_down_id' and a.production_type='1' and b.production_type='1' and a.is_deleted=0 and a.status_active=1 group by a.id,a.production_date,a.remarks ";
					$result=sql_select($sql);
					$avg_prod_qty="";

					?>
				<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th>SL No</th>
						<th>Date</th>
						<th>Production Qnty</th>
						<th>Remarks</th>
					</thead>
					<?
					foreach($result as $row)
					{
						?>
						<tr>
							<td width="50"><? echo $i;?></td>
							<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
							<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
							<td>
							<? echo $row[csf('remarks')];
								$avg_prod_qty+=$row[csf('production_quantity')];
							?>
							</td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<th align="right" colspan="2">Total</th>
						<th align=""><? echo $avg_prod_qty; ?></th>
					</tfoot>
				</table>
			</fieldset>
		
			<fieldset style="width:505px">
				<legend>Print/Embr Issue</legend>
				<? 
					$i=1;
					$sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1  and a.po_break_down_id='$po_break_down_id' and a.production_type='2' and b.production_type='2' and a.is_deleted=0 and a.status_active=1 group by  a.id,a.production_date,a.remarks";
					$result=sql_select($sql);
					$avg_prod_qty="";

					?>
				<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th>SL No</th>
						<th>Date</th>
						<th>Production Qnty</th>
						<th>Remarks</th>
					</thead>
					<?
					foreach($result as $row)
					{
						?>
						<tr>
							<td width="50"><? echo $i;?></td>
							<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
							<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
							<td>
							<? echo $row[csf('remarks')];
								$avg_prod_qty+=$row[csf('production_quantity')];
							?>
							</td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<th align="right" colspan="2">Total</th>
						<th align=""><? echo $avg_prod_qty; ?></th>
					</tfoot>
				</table>
			</fieldset>

			<fieldset style="width:505px">
				<legend>Print/Embr Receive</legend>
				<?  
					$i=1;
					$sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and b.production_type='3' and a.po_break_down_id='$po_break_down_id' and a.production_type='3' and a.is_deleted=0 and a.status_active=1 group by a.id,a.production_date,a.remarks";
					$result=sql_select($sql);
					$avg_prod_qty="";

					?>
					<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<th>SL No</th>
							<th>Date</th>
							<th>Production Qnty</th>
							<th>Remarks</th>
						</thead>
						<?
						foreach($result as $row)
						{
							?>
							<tr>
								<td width="50"><? echo $i;?></td>
								<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
								<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
								<td>
								<? echo $row[csf('remarks')];
									$avg_prod_qty+=$row[csf('production_quantity')];
								?>
								</td>
							</tr>
							<?
							$i++;
						}
						?>
						<tfoot>
							<th align="right" colspan="2">Total</th>
							<th align=""><? echo $avg_prod_qty; ?></th>
						</tfoot>
					</table>
				
			</fieldset>

			<fieldset style="width:505px">
				<legend>Sewing Input</legend>
				<? 
					$i=1;
					$sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and b.production_type='4' and  a.po_break_down_id='$po_break_down_id' and a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.id,a.production_date,a.remarks";
					$result=sql_select($sql);
					$avg_prod_qty="";

					?>
				<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th>SL No</th>
						<th>Date</th>
						<th>Production Qnty</th>
						<th>Remarks</th>
					</thead>
					<?
					foreach($result as $row)
					{
						?>
						<tr>
							<td width="50"><? echo $i;?></td>
							<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
							<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
							<td>
							<? echo $row[csf('remarks')];
								$avg_prod_qty+=$row[csf('production_quantity')];
							?>
							</td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<th align="right" colspan="2">Total</th>
						<th align=""><? echo $avg_prod_qty; ?></th>
					</tfoot>
				</table>
			</fieldset>

			<fieldset style="width:505px">
				<legend>Sewing Output</legend> 
				<? 
					$i=1;
					$sql= "SELECT a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and b.production_type='5' and po_break_down_id='$po_break_down_id'  and a.production_type='5' and a.is_deleted=0 and a.status_active=1 group by a.production_date,a.remarks";

					$result=sql_select($sql);
					$avg_prod_qty="";

					?>
				<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th>SL No</th>
						<th>Date</th>
						<th>Production Qnty</th>
						<th>Remarks</th>
					</thead>
					<?
					foreach($result as $row)
					{
						?>
						<tr>
							<td width="50"><? echo $i;?></td>
							<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
							<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
							<td>
							<? echo $row[csf('remarks')];
								$avg_prod_qty+=$row[csf('production_quantity')];
							?>
							</td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<th align="right" colspan="2">Total</th>
						<th align=""><? echo $avg_prod_qty; ?></th>
					</tfoot>
				</table>
			</fieldset>

			<fieldset style="width:505px">
				<legend>Iron Qty.</legend>
				<? 
					$i=1;
					$sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and b.production_type='7' and   a.po_break_down_id='$po_break_down_id' and a.production_type='7'  and a.is_deleted=0 and a.status_active=1 group by a.id,a.production_date,a.remarks";
					$result=sql_select($sql);
					$avg_prod_qty="";

				?>
				<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th>SL No</th>
						<th>Date</th>
						<th>Production Qnty</th>
						<th>Remarks</th>
					</thead>
					<?
					foreach($result as $row)
					{
						?>
						<tr>
							<td width="50"><? echo $i;?></td>
							<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
							<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
							<td>
							<? echo $row[csf('remarks')];
								$avg_prod_qty+=$row[csf('production_quantity')];
							?>
							</td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<th align="right" colspan="2">Total</th>
						<th align=""><? echo $avg_prod_qty; ?></th>
					</tfoot>
				</table>
			</fieldset>

			<fieldset style="width:505px">
				<legend>Finish Output</legend>
				<? 
					$i=1;
					$sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and b.production_type='8' and   a.po_break_down_id='$po_break_down_id' and a.production_type='8'  and a.is_deleted=0 and a.status_active=1 group by a.id,a.production_date,a.remarks";
					$result=sql_select($sql);
					$avg_prod_qty="";

					?>
				<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th>SL No</th>
						<th>Date</th>
						<th>Production Qnty</th>
						<th>Remarks</th>
					</thead>
					<?
					foreach($result as $row)
					{
						?>
						<tr>
							<td width="50"><? echo $i;?></td>
							<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
							<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
							<td>
							<? echo $row[csf('remarks')];
								$avg_prod_qty+=$row[csf('production_quantity')];
							?>
							</td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<th align="right" colspan="2">Total</th>
						<th align=""><? echo $avg_prod_qty; ?></th>
					</tfoot>
				</table>
			</fieldset>
		</div> 
		<div id="view_part2"></div> 
		<script type="text/javascript">
			//var contents=contents.trim();
			document.getElementById('view_part2').innerHTML='<input type="button" onclick="new_window()" value="Print" name="Print" class="formbutton" style="width:100px;margin-left:200px;"/>';


			function new_window()
			{
				
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body style="font-size:12px; font-family:Arial Narrow">'+document.getElementById('view_part').innerHTML+'</body</html>');
				d.close();
			}

		</script>
	<?
}//end if 

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_brand_name=str_replace("'","",$cbo_brand_name);
	$cbo_season_name=str_replace("'","",$cbo_season_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$cbo_job_year=str_replace("'","",$cbo_job_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$hidden_job_id=str_replace("'","",$hidden_job_id);  
	$txt_production_date=str_replace("'","",$txt_production_date);
	  
	
	if($txt_production_date!="")
	{
		if($db_type==0)
		{
			$txt_production_date=change_date_format($txt_production_date,'yyyy-mm-dd');
 		}
		else if($db_type==2)
		{
			$txt_production_date=change_date_format($txt_production_date,'','',-1);
 		}

		$prod_po_arr = array();
		$sql=sql_select( "SELECT po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date='$txt_production_date' and SERVING_COMPANY in($cbo_company_name)");
		foreach ($sql as $val) 
		{
		 	$prod_po_arr[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
		}
		// print_r($prod_po_arr);
		$sql=sql_select( "SELECT a.po_break_down_id from PRO_EX_FACTORY_MST a, PRO_EX_FACTORY_DELIVERY_MST b where b.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and a.ex_factory_date='$txt_production_date' and b.company_id in($cbo_company_name)");
		foreach ($sql as $val) 
		{
			$prod_po_arr[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
		}

		$sql=sql_select( "SELECT b.order_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id = b.mst_id 
		and b.status_active=1 and  b.is_deleted=0 and a.entry_date='$txt_production_date' and a.working_company_id in($cbo_company_name)");
		foreach ($sql as $val) 
		{
			$prod_po_arr[$val['ORDER_ID']] = $val['ORDER_ID'];
		}
		// print_r($prod_po_arr);
		if(count(array_filter($prod_po_arr))>0)
		{
			$prod_po_cond = where_con_using_array(array_filter($prod_po_arr),0,"b.id");
		}
		else
		{
			echo "<div style='color:red;text-align:center;font-size:18px;'>Data Not Found!</div>";die;
		}
	}
	 
	if($type==0)
	{
			
		$str_po_cond="";
		if($cbo_company_name!=0) $str_po_cond.=" and d.serving_company in($cbo_company_name)";

		if($cbo_buyer_name!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";		
		if($cbo_brand_name!=0) $str_po_cond.=" and a.brand_id=$cbo_brand_name";
		if($cbo_season_name!=0) $str_po_cond.=" and a.season_buyer_wise=$cbo_season_name";
		if($cbo_location>0) $str_po_cond.=" and d.location=$cbo_location";
		if($cbo_floor>0) $str_po_cond.=" and d.floor_id=$cbo_floor";
		if($cbo_job_year>0)
		{
			if($db_type==0)
			{
				$str_po_cond .=" and year(a.insert_date)='$cbo_job_year'";
			}
			else
			{
				$str_po_cond .=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
			}	
		}
		if($hidden_job_id!="")
		{
			$str_po_cond.=" and a.id in($hidden_job_id)";
		}
		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);

		//if($txt_production_date!="")  $str_po_cond .=" and d.production_date= '$txt_production_date'";  

	    $order_sql="SELECT a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.client_id, b.id as po_id, b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id, sum(c.order_quantity) as order_quantity,max(b.shiping_status) as shiping_status,

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input ,


		sum(case when d.production_type=5 and e.production_type=5 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_output ,
		sum(case when d.production_type=5 and e.production_type=5   then e.production_qnty else 0 end ) as total_sewing_output ,


		sum(case when d.production_type=11 and e.production_type=11 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_poly ,
		sum(case when d.production_type=11 and e.production_type=11   then e.production_qnty else 0 end ) as total_poly ,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_packing ,
		sum(case when d.production_type=8 and e.production_type=8   then e.production_qnty else 0 end ) as total_packing ,


		sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=0 and d.production_date='$txt_production_date' then e.reject_qty else 0 end )- sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_reject_qty ,
		sum(case when d.production_type in( 5) and e.production_type in( 5) and e.is_rescan=0  then e.reject_qty else 0 end )  -sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=1   then e.production_qnty else 0 end ) as total_sewing_reject_qty 




		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and b.shiping_status <> 3 and e.is_deleted=0  $str_po_cond $prod_po_cond
		group by a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.client_id, b.id , b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id";
		
		// echo $order_sql;die;
		
		
		$sql_po_result=sql_select($order_sql);
		$production_main_array=array();
		$po_wise_color_production=array();
		foreach($sql_po_result as $row)
		{
			//if($row[csf("today_cutting")] || $row[csf("today_sewing_input")] || $row[csf("today_sewing_output")] ||  $row[csf("today_poly")] ||    $row[csf("today_packing")])
			//{
				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$row[csf("shiping_status")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["client_id"]=$row[csf("client_id")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_cutting"]+=$row[csf("today_cutting")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_cutting"]+=$row[csf("total_cutting")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_input"]+=$row[csf("today_sewing_input")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_input"]+=$row[csf("total_sewing_input")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_output"]+=$row[csf("today_sewing_output")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_output"]+=$row[csf("total_sewing_output")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_poly"]+=$row[csf("today_poly")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_poly"]+=$row[csf("total_poly")];




				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_packing"]+=$row[csf("today_packing")];




				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_packing"]+=$row[csf("total_packing")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_reject_qty"]+=$row[csf("today_sewing_reject_qty")];




				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_reject_qty"]+=$row[csf("total_sewing_reject_qty")];


				$all_po_id.=$row[csf("po_id")].",";

			//}

				if($row[csf("total_cutting")] || $row[csf("total_sewing_input")] || $row[csf("total_sewing_output")] ||  $row[csf("total_poly")] ||    $row[csf("total_packing")])
				{
					$po_wise_color_production[$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("color_number_id")];

				}

			  

			
		}
		$all_po_id=implode(',',array_unique(explode(",",chop($all_po_id,","))));
		 
		
		$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst where po_number_id in ('".$all_po_id."') group by po_number_id, template_id","po_number_id","template_id");
		
		$client_array = array();
		$sql_client=sql_select("SELECT a.id, a.buyer_name
		FROM lib_buyer a, lib_buyer_tag_company b
   		WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.buyer_id = a.id AND a.id IN 
   		(SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (7)) 
   		group by a.id, a.buyer_name
		ORDER BY buyer_name");

		foreach ($sql_client as $key => $value) {
			$client_array[$value[csf('id')]] = $value[csf('buyer_name')];
		}
		// echo "<pre>";
		// print_r($client_array);

		$po_product_cond="";
		if($db_type==0)
		{
			if($all_po_id!="") $po_product_cond =" and a.po_break_down_id in($all_po_id)";
		}
		else
		{
			if($all_po_id!="")
			{
				$all_po_id_arr=array_chunk(explode(",",$all_po_id),999);
				$p=1;
				if(!empty($all_po_id_arr))
				{
					foreach($all_po_id_arr as $po_id)
					{
						if($p==1) $po_product_cond =" and (a.po_break_down_id in(".implode(',',$po_id).")"; else $po_product_cond .=" or a.po_break_down_id in(".implode(',',$po_id).")";
						$p++;
					}
					$po_product_cond .=" )";
				}
				
			}
		}

		$po_product_cond2=str_replace("a.po_break_down_id in", "b.id not in", $po_product_cond);

		$order_sql_lay="SELECT a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id,c.size_number_id, c.order_quantity ,max(b.shiping_status) as shiping_status ,
		sum(case when d.entry_date='$txt_production_date' then f.size_qty else 0 end ) as today_lay,sum(f.size_qty) as total_lay
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
		where a.id=b.job_id and b.id=c.po_break_down_id and c.job_no_mst=d.job_no and d.id=e.mst_id and d.id=f.mst_id and b.id=f.order_id and c.color_number_id=e.color_id and c.size_number_id=f.size_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and b.shiping_status <> 3 and e.is_deleted=0 and f.status_active=1  and f.is_deleted=0 $str_po_cond_lay $prod_po_cond
		group by a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id ,c.size_number_id,c.order_quantity ";
		$lay_new_po=array();
		$po_col_size_qnty_array=array();
		foreach(sql_select($order_sql_lay) as $row )
		{
			if($po_wise_color_production[$row[csf("po_id")]][$row[csf("color_number_id")]]=="")
			{
				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$row[csf("shiping_status")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				
				if($po_col_size_qnty_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_quantity"]=="")
				{
					$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

					$po_col_size_qnty_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

				}
				

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_lay"]+=$row[csf("today_lay")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_lay"]+=$row[csf("total_lay")];
				//$lay_new_po[$row[csf("po_id")]]=$row[csf("po_id")];
			}
		}
		/*$lay_new_po_ids=implode(",", $lay_new_po);
		if($lay_new_po_ids)
		{
			$new_cond= " or c.order_id in($lay_new_po_ids) ";
		}
		*/


		$po_product_cond_cut_lay=str_replace("a.po_break_down_id", "c.order_id", $po_product_cond);
		$cut_lay_sql="SELECT a.entry_date, c.order_id, b.gmt_item_id, b.color_id ,c.country_id  ,sum(c.size_qty) as qntys from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b ,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0   $po_product_cond_cut_lay   group by a.entry_date, c.order_id, b.gmt_item_id, b.color_id ,c.country_id"; 
		$cut_lay_array=array();
		foreach(sql_select($cut_lay_sql) as $keys=>$vals)
		{
			$cut_lay_array[$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("country_id")]][$vals[csf("color_id")]][change_date_format($vals[csf("entry_date")])]["today_lay"]+=$vals[csf("qntys")];
			$cut_lay_array[$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("country_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("qntys")];

			 
		}



		$order_qnty_pcs_arr=array();
		$order_qnty_pcs_sql="SELECT a.po_break_down_id,a.job_no_mst,a.color_number_id,a.item_number_id  , a.country_id, a.country_ship_date,   sum(a.order_quantity) as qntys from wo_po_color_size_breakdown a where a.status_active in(1,2,3) and a.is_deleted=0 $po_product_cond  group by  a.po_break_down_id,a.job_no_mst,a.color_number_id,a.item_number_id  , a.country_id, a.country_ship_date";
		foreach(sql_select($order_qnty_pcs_sql) as $pcs_key=>$pcs_val)
		{
			$order_qnty_pcs_arr[$pcs_val[csf("job_no_mst")]][$pcs_val[csf("po_break_down_id")]][$pcs_val[csf("item_number_id")]][$pcs_val[csf("country_id")]][change_date_format($pcs_val[csf("country_ship_date")])][$pcs_val[csf("color_number_id")]]+=$pcs_val[csf("qntys")];
		}
		
		
		if($po_product_cond=="")
		{
			echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px">No Data Found.</div>'; die;
		}
		
		
		$color_id_arr=return_library_array( "select a.id,a.color_number_id from wo_po_color_size_breakdown a where a.status_active in(1,2,3) and a.color_number_id>0 $po_product_cond", "id", "color_number_id");
		
		
		 
		 
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id ,sum(CASE WHEN a.entry_form <> 85 and a.ex_factory_date='$txt_production_date' THEN b.production_qnty ELSE 0 END)-
		sum(CASE WHEN a.entry_form=85 and a.ex_factory_date='$txt_production_date' THEN b.production_qnty ELSE 0 END) as today_ex_factory_qnty,
		sum(CASE WHEN a.entry_form <> 85   THEN b.production_qnty ELSE 0 END) 
		-
		sum(CASE WHEN a.entry_form=85   THEN b.production_qnty ELSE 0 END) as total_ex_factory_qnty ,a.total_carton_qnty
		 from pro_ex_factory_mst a, pro_ex_factory_dtls b 
		 where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $po_product_cond 
		 group by a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id,a.total_carton_qnty");
		
		foreach($ex_factory_data as $exRow)
		{
			 
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$color_id_arr[$exRow[csf('color_size_break_down_id')]]]['today_ex_factory_qnty']+=$exRow[csf('today_ex_factory_qnty')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$color_id_arr[$exRow[csf('color_size_break_down_id')]]]['total_ex_factory_qnty']+=$exRow[csf('total_ex_factory_qnty')];		 

		}
		
		ob_start();	
		
		?>
		 
        <div>
        	<table width="2810" cellspacing="0" >
        		<tr class="form_caption" style="border:none;">
        			<td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
        				Daily RMG Production Report
        			</strong></td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="30" align="center" style="border:none; font-size:14px;">
        				<strong>
        					Working Company Name : <? 
        					$comp_names=""; 
        					foreach(explode(",",$cbo_company_name) as $vals) 
        					{
        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
        					}
        					echo $comp_names;
        					 ?>
        				</strong>                                
        			</td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold" >
        				<?
        				if(str_replace("'","",trim($txt_production_date))!="")
        				{
        					echo "Date ".change_date_format($txt_production_date)  ;
        				}
        				?>
        			</td>
        		</tr>
        	</table>
            <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:2810px">Details Part</div>
            
			<div style="float:left; width:1930px">
				<table width="2810" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
					<thead>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="30" ><p>SL</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer Client</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Style Ref.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Job No.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Order No.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Country</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Country Shipdate</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="120"><p>Garment Item</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Color</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order Qty.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2"   width="160"><p>Cut & Lay</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Cutting QC Qty</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Sewing Input</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Sewing Output</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Today Sewing Reject</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Total Sewing Reject</p></th>
						 
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Sewing WIP</p></th> 
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Poly Entry</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Poly WIP</p></th> 
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Packing &Fin.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Pac &Fin. WIP</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p> Ex-Factory</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Ex-Fac. WIP</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Status</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Remarks</p></th>
					</tr>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>

					</tr>
						
						  
					   
					</thead>
				</table>
				<div style="max-height:425px; overflow-y:scroll; width:2830px" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"  width="2810" rules="all" id="table_body" >
					<?
					$po_wise_ship_span_arr=array();
					// echo "<pre>";
					// print_r($production_main_array);die();
					foreach($production_main_array as $buyer_id=>$job_data)
					{
						foreach($job_data as $job_id=>$po_data)
						{
							foreach($po_data as $po_id=>$country_data)
							{
								$po_wise_ship_span=0;
								foreach($country_data as $country_id=>$shipdate_data)
								{
									foreach($shipdate_data as $shipdate_id=>$item_data)
									{
										foreach($item_data as $item_id=>$color_data)
										{
											foreach($color_data as $color_id=>$rows)
											{
												$po_wise_ship_span++;
											}
										}
									}
								}
								$po_wise_ship_span_arr[$po_id]=$po_wise_ship_span;

							}

							
						}

					}
					 
						$k=1;
						$gr_wise_order_qnty=0;
						$gr_wise_lay_qnty_today=0;
						$gr_wise_lay_qnty_total=0;
						$gr_wise_today_cutting=0;
						$gr_wise_total_cutting=0;
						$gr_wise_today_sewing_input=0;
						$gr_wise_total_sewing_input=0;
						$gr_wise_today_sewing_output=0;
						$gr_wise_total_sewing_output=0;
						$gr_wise_today_sewing_rej=0;
						$gr_wise_total_sewing_rej=0;
						$gr_wise_today_poly=0;
						$gr_wise_total_poly=0;
						$gr_wise_today_pac=0;
						$gr_wise_total_pac=0;
						$gr_wise_today_ex_fac=0;
						$gr_wise_total_ex_fac=0;
						$gr_wise_sewing_wip=0;
						$gr_wise_poly_wip=0;
						$gr_wise_pack_wip=0;
						$gr_wise_exfac_wip=0;
						 
						foreach($production_main_array as $buyer_id=>$job_data)
						{
							$buyer_wise_order_qnty=0;
							$buyer_wise_lay_qnty_today=0;
							$buyer_wise_lay_qnty_total=0;
							$buyer_wise_today_cutting=0;
							$buyer_wise_total_cutting=0;
							$buyer_wise_today_sewing_input=0;
							$buyer_wise_total_sewing_input=0;
							$buyer_wise_today_sewing_output=0;
							$buyer_wise_total_sewing_output=0;
							$buyer_wise_today_sewing_rej=0;
							$buyer_wise_total_sewing_rej=0;
							$buyer_wise_today_poly=0;
							$buyer_wise_total_poly=0;
							$buyer_wise_today_pac=0;
							$buyer_wise_total_pac=0;
							$buyer_wise_today_ex_fac=0;
							$buyer_wise_total_ex_fac=0;
							$buyer_wise_sewing_wip=0;
							$buyer_wise_poly_wip=0;
							$buyer_wise_pack_wip=0;
							$buyer_wise_exfac_wip=0;

							foreach($job_data as $job_id=>$po_data)
							{
								$job_wise_order_qnty=0;
								$job_wise_lay_qnty_today=0;
								$job_wise_lay_qnty_total=0;
								$job_wise_today_cutting=0;
								$job_wise_total_cutting=0;
								$job_wise_today_sewing_input=0;
								$job_wise_total_sewing_input=0;
								$job_wise_today_sewing_output=0;
								$job_wise_total_sewing_output=0;
								$job_wise_today_sewing_rej=0;
								$job_wise_total_sewing_rej=0;
								$job_wise_today_poly=0;
								$job_wise_total_poly=0;
								$job_wise_today_pac=0;
								$job_wise_total_pac=0;
								$job_wise_today_ex_fac=0;
								$job_wise_total_ex_fac=0;
								$job_wise_sewing_wip=0;
								$job_wise_poly_wip=0;
								$job_wise_pack_wip=0;
								$job_wise_exfac_wip=0;

								foreach($po_data as $po_id=>$country_data)
								{
									$kk=0;
									$po_wise_order_qnty=0;
									$po_wise_lay_qnty_today=0;
									$po_wise_lay_qnty_total=0;
									$po_wise_today_cutting=0;
									$po_wise_total_cutting=0;
									$po_wise_today_sewing_input=0;
									$po_wise_total_sewing_input=0;
									$po_wise_today_sewing_output=0;
									$po_wise_total_sewing_output=0;
									$po_wise_today_sewing_rej=0;
									$po_wise_total_sewing_rej=0;
 									$po_wise_today_poly=0;
									$po_wise_total_poly=0;
									$po_wise_today_pac=0;
									$po_wise_total_pac=0;
									$po_wise_today_ex_fac=0;
									$po_wise_total_ex_fac=0;
									$po_wise_sewing_wip=0;
									$po_wise_poly_wip=0;
									$po_wise_pack_wip=0;
									$po_wise_exfac_wip=0;
									 
									foreach($country_data as $country_id=>$shipdate_data)
									{
										foreach($shipdate_data as $shipdate_id=>$item_data)
										{
											foreach($item_data as $item_id=>$color_data)
											{
												foreach($color_data as $color_id=>$rows)
												{
													// echo "<pre>";
													// print_r($rows);
													$today_ex_fac=$ex_factory_arr[$po_id][$item_id][$country_id][$color_id]['today_ex_factory_qnty'];
													$total_ex_fac=$ex_factory_arr[$po_id][$item_id][$country_id][$color_id]['total_ex_factory_qnty'];
													//$order_qnty_pcs_arr[$pcs_val[csf("job_no_mst")]][$pcs_val[csf("po_break_down_id")]][$pcs_val[csf("item_number_id")]][$pcs_val[csf("country_id")]][change_date_format($pcs_val[csf("country_ship_date"))]][$pcs_val[csf("color_number_id")]]
													$order_qntys=$order_qnty_pcs_arr[$job_id][$po_id][$item_id][$country_id][change_date_format($shipdate_id)][$color_id];

													$cut_lays_qnty_today=$cut_lay_array[$po_id][$item_id][$country_id][$color_id][change_date_format(str_replace("'", "", $txt_production_date))]["today_lay"];
													

													$cut_lays_qnty_total=$cut_lay_array[$po_id][$item_id][$country_id][$color_id]["total_lay"];

													if(!$cut_lays_qnty_today)
													{
														$cut_lays_qnty_today=$rows["today_lay"];
 													}
													if(!$cut_lays_qnty_total)
													{
														$cut_lays_qnty_total=$rows["total_lay"];
													}
													if(!$order_qntys)
													{
														$order_qntys=$rows["order_quantity"];
													}
													$sewing_wip= ($rows["total_sewing_output"]+$rows["total_sewing_reject_qty"])-$rows["total_sewing_input"];

													$po_wise_order_qnty+=$order_qntys;
													$po_wise_lay_qnty_today+=$cut_lays_qnty_today;
													$po_wise_lay_qnty_total+=$cut_lays_qnty_total;
													$po_wise_today_cutting+=$rows["today_cutting"];
													$po_wise_total_cutting+=$rows["total_cutting"];
													$po_wise_today_sewing_input+=$rows["today_sewing_input"];
													$po_wise_total_sewing_input+=$rows["total_sewing_input"];
													$po_wise_today_sewing_output+=$rows["today_sewing_output"];
													$po_wise_total_sewing_output+=$rows["total_sewing_output"];
													$po_wise_today_poly+=$rows["today_poly"];
													$po_wise_total_poly+=$rows["total_poly"];
													$po_wise_today_pac+=$rows["today_packing"];
													$po_wise_total_pac+=$rows["total_packing"];
													$po_wise_today_ex_fac+=$today_ex_fac;
													$po_wise_total_ex_fac+=$total_ex_fac;
													$po_wise_sewing_wip+=$sewing_wip;
													$po_wise_poly_wip+=$rows["total_poly"]-$rows["total_sewing_output"];
													$po_wise_pack_wip+=$rows["total_packing"]-$rows["total_poly"];
													$po_wise_exfac_wip+=$order_qntys-$total_ex_fac;
													$po_wise_today_sewing_rej+=$rows["today_sewing_reject_qty"];
													$po_wise_total_sewing_rej+=$rows["total_sewing_reject_qty"];


													$job_wise_order_qnty+=$order_qntys;
													$job_wise_lay_qnty_today+=$cut_lays_qnty_today;
													$job_wise_lay_qnty_total+=$cut_lays_qnty_total;
													$job_wise_today_cutting+=$rows["today_cutting"];
													$job_wise_total_cutting+=$rows["total_cutting"];
													$job_wise_today_sewing_input+=$rows["today_sewing_input"];
													$job_wise_total_sewing_input+=$rows["total_sewing_input"];
													$job_wise_today_sewing_output+=$rows["today_sewing_output"];
													$job_wise_total_sewing_output+=$rows["total_sewing_output"];
													$job_wise_today_poly+=$rows["today_poly"];
													$job_wise_total_poly+=$rows["total_poly"];
													$job_wise_today_pac+=$rows["today_packing"];
													$job_wise_total_pac+=$rows["total_packing"];
													$job_wise_today_ex_fac+=$today_ex_fac;
													$job_wise_total_ex_fac+=$total_ex_fac;
													$job_wise_sewing_wip+=$sewing_wip;
													$job_wise_poly_wip+=$rows["total_poly"]-$rows["total_sewing_output"];
													$job_wise_pack_wip+=$rows["total_packing"]-$rows["total_poly"];
													$job_wise_exfac_wip+=$order_qntys-$total_ex_fac;
													$job_wise_today_sewing_rej+=$rows["today_sewing_reject_qty"];
													$job_wise_total_sewing_rej+=$rows["total_sewing_reject_qty"];



													$buyer_wise_order_qnty+=$order_qntys; 
													$buyer_wise_lay_qnty_today+=$cut_lays_qnty_today;
													$buyer_wise_lay_qnty_total+=$cut_lays_qnty_total;
													$buyer_wise_today_cutting+=$rows["today_cutting"];
													$buyer_wise_total_cutting+=$rows["total_cutting"];
													$buyer_wise_today_sewing_input+=$rows["today_sewing_input"];
													$buyer_wise_total_sewing_input+=$rows["total_sewing_input"];
													$buyer_wise_today_sewing_output+=$rows["today_sewing_output"];
													$buyer_wise_total_sewing_output+=$rows["total_sewing_output"];
													$buyer_wise_today_poly+=$rows["today_poly"];
													$buyer_wise_total_poly+=$rows["total_poly"];
													$buyer_wise_today_pac+=$rows["today_packing"];
													$buyer_wise_total_pac+=$rows["total_packing"];
													$buyer_wise_today_ex_fac+=$today_ex_fac;
													$buyer_wise_total_ex_fac+=$total_ex_fac;
													$buyer_wise_sewing_wip+=$sewing_wip;
													$buyer_wise_poly_wip+=$rows["total_poly"]-$rows["total_sewing_output"];
													$buyer_wise_pack_wip+=$rows["total_packing"]-$rows["total_poly"];
													$buyer_wise_exfac_wip+=$order_qntys-$total_ex_fac;
													$buyer_wise_today_sewing_rej+=$rows["today_sewing_reject_qty"];
													$buyer_wise_total_sewing_rej+=$rows["total_sewing_reject_qty"];



													$gr_wise_order_qnty+=$order_qntys; 
													$gr_wise_lay_qnty_today+=$cut_lays_qnty_today;
													$gr_wise_lay_qnty_total+=$cut_lays_qnty_total;
													$gr_wise_today_cutting+=$rows["today_cutting"];
													$gr_wise_total_cutting+=$rows["total_cutting"];
													$gr_wise_today_sewing_input+=$rows["today_sewing_input"];
													$gr_wise_total_sewing_input+=$rows["total_sewing_input"];
													$gr_wise_today_sewing_output+=$rows["today_sewing_output"];
													$gr_wise_total_sewing_output+=$rows["total_sewing_output"];
													$gr_wise_today_poly+=$rows["today_poly"];
													$gr_wise_total_poly+=$rows["total_poly"];
													$gr_wise_today_pac+=$rows["today_packing"];
													$gr_wise_total_pac+=$rows["total_packing"];
													$gr_wise_today_ex_fac+=$today_ex_fac;
													$gr_wise_total_ex_fac+=$total_ex_fac;
													$gr_wise_sewing_wip+=$sewing_wip;
													$gr_wise_poly_wip+=$rows["total_poly"]-$rows["total_sewing_output"];
													$gr_wise_pack_wip+=$rows["total_packing"]-$rows["total_poly"];
													$gr_wise_exfac_wip+=$total_ex_fac-$order_qntys;
													$gr_wise_today_sewing_rej+=$rows["today_sewing_reject_qty"];
													$gr_wise_total_sewing_rej+=$rows["total_sewing_reject_qty"];





													if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $k; ?>">
													 
														<td style="word-wrap: break-word;word-break: break-all;"  align="left"  width="30"><p><? echo $k;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="115"><p><? echo $buyer_library[$buyer_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="115"><p><? echo $client_array[$rows["client_id"]]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="115"><p><? echo $rows["style_ref_no"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="80"><p><? echo $rows["job_no_prefix_num"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="115"><p><? echo $rows["po_number"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"  align="left"    width="80"><p><? echo $country_library[$country_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="80"><p><? echo change_date_format($shipdate_id); ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="120"><p><? echo $garments_item[$item_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="80"><p><? echo $color_Arr_library[$color_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $order_qntys;?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $cut_lays_qnty_today;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $cut_lays_qnty_total;?></p></td>


														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="80"><p><? echo $rows["today_cutting"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="80"><p><? echo $rows["total_cutting"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="80"><p><? echo $rows["today_sewing_input"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="80"><p><? echo $rows["total_sewing_input"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="80"><p><? echo $rows["today_sewing_output"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="80"><p><? echo $rows["total_sewing_output"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="100"><p><? echo $rows["today_sewing_reject_qty"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="100"><p><? echo $rows["total_sewing_reject_qty"];?></p></td>
 														<td style="word-wrap: break-word;word-break: break-all;"    align="right"  width="80"><p><? echo $sewing_wip ;?></p></td> 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="80"><p><? echo $rows["today_poly"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="80"><p><? echo $rows["total_poly"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"    align="right"  width="80"><p><? echo $poly_wip=$rows["total_poly"]- $rows["total_sewing_output"];?></p></td> 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="80"><p><? echo $rows["today_packing"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"  align="right"   width="80"><p><? echo $rows["total_packing"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo  $packing_wip=  $rows["total_packing"]- $rows["total_poly"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"  align="right"   width="80"><p><? echo $today_ex_fac;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="80"><p><? echo $total_ex_fac ;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $ex_fac_wip= $total_ex_fac-$order_qntys;?></p></td>
														 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo  $shipment_status[$rows['shiping_status']];?></p></td>


															 
														
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p>
														 
															
															<a href="##" onClick="openmypage_remarks(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $country_id;?>,<? echo $color_id;?>, 'remarks_popup');" >Remarks</a>

														</p></td>

								 
													</tr>	


													<?
													$kk++;

													$k++;

												}
											}
										}
									}

									

									?>
									<!-- <tr bgcolor="#E4E4E4">
										<td style="word-wrap: break-word;word-break: break-all;"  colspan="9" align="center"><p><strong>Po Total</strong></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $po_wise_order_qnty;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $po_wise_lay_qnty_today;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $po_wise_lay_qnty_total;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_sewing_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_poly_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_pack_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_exfac_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p></p></td>
									</tr> -->


									<?
								}

								?>
								<!-- <tr bgcolor="#E4E4E4">
										<td style="word-wrap: break-word;word-break: break-all;"  colspan="9" align="center"><p><strong>Job Total</strong> </p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $job_wise_order_qnty;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $job_wise_lay_qnty_today;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $job_wise_lay_qnty_total;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_sewing_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_poly_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_pack_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_exfac_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p></p></td>
								</tr> -->


								<?

							}


							?>
									<!-- <tr bgcolor="#E4E4E4">
										<td style="word-wrap: break-word;word-break: break-all;"  colspan="9" align="center"><p><strong>Buyer Total</strong></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $buyer_wise_order_qnty;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $buyer_wise_lay_qnty_today;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $buyer_wise_lay_qnty_total;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_sewing_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_poly_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_pack_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_exfac_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p></p></td>
									</tr> -->


									<?

							

							
							
							 		
								 
							 
						} 
						?>
						 

						
						
						 
					 
					</table>
					<table border="1" class="tbl_bottom" width="2810" rules="all" id="report_table_footer_1" >
						<tr >
							 
							<td style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"  align="center"    width="80">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="120"><strong>Grand Total</strong></td>
							<td align="center"   align="center"   width="80">&nbsp;</p></td>

							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;"  align="center" id="grand_total_order"><p> </p> <? //echo $gr_wise_order_qnty;?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_lay"  align="center"><p> </p> <? //echo $gr_wise_lay_qnty_today;?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_lay"  align="center"><p> </p> <? //echo $gr_wise_lay_qnty_total;?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;"  id="grand_today_cut" align="center"><p><? //echo $gr_wise_today_cutting; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;"  id="grand_total_cut" align="center"><p><? //echo $gr_wise_total_cutting; ?></p></td>
							<td  width="80" style="word-wrap: break-word;word-break: break-all;text-align: center;"  id="grand_today_sewin" align="center"><p><? //echo $gr_wise_today_sewing_input; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;"  id="grand_total_sewin" align="center"><p><? //echo $gr_wise_total_sewing_input; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_sewout" align="center"><p><? //echo $gr_wise_today_sewing_output; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_sewout" align="center"><p><? //echo $gr_wise_total_sewing_output; ?></p></td>
							<td  width="100" style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_sew_rej" align="center"><p><? //echo $gr_wise_today_sewing_rej; ?></p></td>
							<td width="100"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_sewrej" align="center"><p><? echo $gr_wise_total_sewing_rej; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_sewingwip" align="center"><p><? //echo $gr_wise_sewing_wip; ?></p></td>


							<td  width="80" style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_poly" align="center"><p><? //echo $gr_wise_today_poly; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_poly" align="center"><p><? //echo $gr_wise_total_poly; ?></p></td>
							<td  width="80" style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_poly_wip" align="center"><p><? //echo $gr_wise_poly_wip; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_pack" align="center"><p><? //echo $gr_wise_today_pac; ?></p></td>
							<td  width="80" style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_pack" align="center"><p><? //echo $gr_wise_total_pac; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_pack_wip" align="center"><p><?// echo $gr_wise_pack_wip; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_exfac" align="center"><p><?// echo $gr_wise_today_ex_fac; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_exfac" align="center"><p><?// echo $gr_wise_total_ex_fac; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_exfac_wip" align="center"><p><? //echo $gr_wise_exfac_wip; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;"  align="center"><p></p></td>
						</tr>
					</table>
					
					  
				</div>
			</div>
			 
			 
			 
			 
		 </div> 
        <?
	}
	else if($type==1) // Show2 button 
	{			
		$str_po_cond="";
		if($cbo_company_name!=0) $str_po_cond.=" and d.serving_company in($cbo_company_name)";
		if($cbo_buyer_name!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";		
		if($cbo_brand_name!=0) $str_po_cond.=" and a.brand_id=$cbo_brand_name";
		if($cbo_season_name!=0) $str_po_cond.=" and a.season_buyer_wise=$cbo_season_name";
		if($cbo_location>0) $str_po_cond.=" and d.location=$cbo_location";
		if($cbo_floor>0) $str_po_cond.=" and d.floor_id=$cbo_floor";
		if($cbo_job_year>0)
		{
			if($db_type==0)
			{
				$str_po_cond .=" and year(a.insert_date)='$cbo_job_year'";
			}
			else
			{
				$str_po_cond .=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
			}	
		}

		if($hidden_job_id!="")
		{
			$str_po_cond.=" and a.id in($hidden_job_id)";
		}
		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay); 
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  ); 
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

		/*================================================================================================
		/										production data											 /	
		=================================================================================================*/

	    $order_sql="SELECT a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.client_id, b.id as po_id, b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id, sum(c.order_quantity) as order_quantity,max(b.shiping_status) as shiping_status,d.sewing_line,d.prod_reso_allo,

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=3 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_wash_snd ,
		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=3  then e.production_qnty else 0 end ) as total_wash_snd ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=3 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_wash_rcv ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=3  then e.production_qnty else 0 end ) as total_wash_rcv ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input ,

		sum(case when d.production_type=5 and e.production_type=5 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_output ,
		sum(case when d.production_type=5 and e.production_type=5   then e.production_qnty else 0 end ) as total_sewing_output ,

		sum(case when d.production_type=7 and e.production_type=7 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_iron ,
		sum(case when d.production_type=7 and e.production_type=7   then e.production_qnty else 0 end ) as total_iron ,

		sum(case when d.production_type=11 and e.production_type=11 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_poly ,
		sum(case when d.production_type=11 and e.production_type=11   then e.production_qnty else 0 end ) as total_poly ,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_packing ,
		sum(case when d.production_type=8 and e.production_type=8   then e.production_qnty else 0 end ) as total_packing ,

		sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=0 and d.production_date='$txt_production_date' then e.reject_qty else 0 end )- sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_reject_qty ,
		sum(case when d.production_type in( 5) and e.production_type in( 5) and e.is_rescan=0  then e.reject_qty else 0 end )  -sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=1   then e.production_qnty else 0 end ) as total_sewing_reject_qty 

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and b.shiping_status <> 3 and e.is_deleted=0  $str_po_cond $prod_po_cond
		group by a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.client_id, b.id , b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id,d.sewing_line,d.prod_reso_allo";
		
		//echo $order_sql."<br>";
				
		$sql_po_result=sql_select($order_sql);
		$production_main_array=array();
		$po_wise_color_production=array();
		foreach($sql_po_result as $row)
		{
			//if($row[csf("today_cutting")] || $row[csf("today_sewing_input")] || $row[csf("today_sewing_output")] ||  $row[csf("today_poly")] ||    $row[csf("today_packing")])
			//{
				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$row[csf("shiping_status")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["client_id"]=$row[csf("client_id")];

				$sewing_line='';
				if($row[csf("prod_reso_allo")]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$row[csf("sewing_line")]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
					}
				}
				else
				{ 
					$sewing_line=$line_library[$row[csf("sewing_line")]];	
				}

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["sewing_line"] .= $sewing_line.",";

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_cutting"]+=$row[csf("today_cutting")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_cutting"]+=$row[csf("total_cutting")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_wash_snd"]+=$row[csf("today")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_wash_snd"]+=$row[csf("total_wash_snd")];
				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_wash_rcv"]+=$row[csf("today_wash_rcv")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_wash_rcv"]+=$row[csf("total_wash_rcv")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_input"]+=$row[csf("today_sewing_input")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_input"]+=$row[csf("total_sewing_input")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_output"]+=$row[csf("today_sewing_output")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_output"]+=$row[csf("total_sewing_output")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_iron"]+=$row[csf("today_iron")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_iron"]+=$row[csf("total_iron")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_poly"]+=$row[csf("today_poly")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_poly"]+=$row[csf("total_poly")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_packing"]+=$row[csf("today_packing")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_packing"]+=$row[csf("total_packing")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_reject_qty"]+=$row[csf("today_sewing_reject_qty")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_reject_qty"]+=$row[csf("total_sewing_reject_qty")];

				//$all_po_id.=$row[csf("po_id")].",";
				$all_po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];

			//}

				if($row[csf("total_cutting")] || $row[csf("total_sewing_input")] || $row[csf("total_sewing_output")] ||  $row[csf("total_poly")] ||    $row[csf("total_packing")])
				{
					$po_wise_color_production[$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("color_number_id")];

				}

		}
		//$all_po_id=implode(',',array_unique(explode(",",chop($all_po_id,","))));

		$con = connect();
		$r_id2=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (43)");
		if($r_id2)
		{
			oci_commit($con);
		}

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 43, 1,$all_po_id_array, $empty_arr);
		 
		
		$template_id_arr=return_library_array("SELECT a.po_number_id, a.template_id from tna_process_mst a, GBL_TEMP_ENGINE b where a.po_number_id=b.ref_val and b.entry_form=43 and b.ref_from=1 and b.user_id=$user_id group by a.po_number_id, a.template_id","po_number_id","template_id");
		//a.po_number_id in ('".$all_po_id."')
		/*================================================================================================
		/										get client data											 /	
		=================================================================================================*/
		$client_array = array();
		$sql_client=sql_select("SELECT a.id, a.buyer_name FROM lib_buyer a, lib_buyer_tag_company b WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.buyer_id = a.id AND a.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (7)) group by a.id, a.buyer_name ORDER BY buyer_name");
		foreach ($sql_client as $key => $value) {
			$client_array[$value[csf('id')]] = $value[csf('buyer_name')];
		}
		// echo "<pre>";
		// print_r($client_array);

		/* $po_product_cond="";
		if($db_type==0)
		{
			if($all_po_id!="") $po_product_cond =" and a.po_break_down_id in($all_po_id)";
		}
		else
		{
			if($all_po_id!="")
			{
				$all_po_id_arr=array_chunk(explode(",",$all_po_id),999);
				$p=1;
				if(!empty($all_po_id_arr))
				{
					foreach($all_po_id_arr as $po_id)
					{
						if($p==1) $po_product_cond =" and (a.po_break_down_id in(".implode(',',$po_id).")"; else $po_product_cond .=" or a.po_break_down_id in(".implode(',',$po_id).")";
						$p++;
					}
					$po_product_cond .=" )";
				}
				
			}
		} */
		/*================================================================================================
		/										wash production data									 /	
		=================================================================================================*/
		$po_product_cond2 = str_replace("a.po_break_down_id", "b.buyer_po_id", $po_product_cond);
		$sql_sub = "SELECT b.ID,b.BUYER_PO_ID,c.PROCESS from subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.id=c.mst_id and a.subcon_job=c.job_no_mst and b.buyer_po_id=g.ref_val and g.entry_form=43 and g.ref_from=1 and g.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.prod_sequence_no=1";
		//$po_product_cond2
		// echo $sql_sub;die();
		$sub_res = sql_select($sql_sub);
		$wet_prod_po_array = array();
		$dry_prod_po_array = array();
		foreach ($sub_res as $val) 
		{
			if($val['PROCESS']==1)
			{
				$wet_prod_po_array[$val['ID']] = $val['ID'];
			}
			else if($val['PROCESS']==2)
			{
				$dry_prod_po_array[$val['ID']] = $val['ID'];
			}
		}
		$po_ids = array_merge($wet_prod_po_array,$dry_prod_po_array);
		//$po_ids_cond = (count($po_ids)>0) ? " and b.po_id in(".implode(",", $po_ids).")" : "";
		//$wet_po_ids = (count($wet_prod_po_array)>0) ? " and b.po_id in(".implode(",", $wet_prod_po_array).")" : "";
		//$dry_po_ids = (count($dry_prod_po_array)>0) ? " and b.po_id in(".implode(",", $dry_prod_po_array).")" : "";
		// echo $wet_po_ids;die();

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 43, 2,$wet_prod_po_array, $empty_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 43, 3,$dry_prod_po_array, $empty_arr);

		//$po_product_cond2 = str_replace("a.po_break_down_id", "b.buyer_po_id", $po_product_cond);
	    $wash_prod="SELECT b.buyer_po_id as po_id,
		sum(case when a.entry_form=301 and b.po_id=h.ref_val and h.entry_form=43 and h.ref_from in (2) and b.process_id=1 and  b.production_date='$txt_production_date' then b.qcpass_qty else 0 end ) as today_wet_wash_prod ,
		sum(case when a.entry_form=301 and b.po_id=h.ref_val and h.entry_form=43 and h.ref_from in (2) and b.process_id=1 then b.qcpass_qty else 0 end ) as total_wet_wash_prod,
		sum(case when a.entry_form=342 and b.po_id=h.ref_val and h.entry_form=43 and h.ref_from in (3) and b.process_id=2 and b.production_date='$txt_production_date' then b.qcpass_qty else 0 end ) as today_dry_wash_prod ,
		sum(case when a.entry_form=342 and b.po_id=h.ref_val and h.entry_form=43 and h.ref_from in (3) and b.process_id=2 then b.qcpass_qty else 0 end ) as total_dry_wash_prod
		from subcon_embel_production_mst a,subcon_embel_production_dtls b, GBL_TEMP_ENGINE g, GBL_TEMP_ENGINE h
		where a.id=b.mst_id and b.buyer_po_id=g.ref_val and g.entry_form=43 and g.ref_from=1 and g.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1)  and b.is_deleted=0 and b.po_id=h.ref_val and h.entry_form=43 and h.ref_from in (2,3) and h.userid=$user_id 
		group by b.buyer_po_id";
		//$po_ids_cond  $po_product_cond2
		
		// echo $wash_prod;die();
				
		$wash_prod_result=sql_select($wash_prod);
		$wash_prod_array=array();
		foreach($wash_prod_result as $row)
		{
			$wash_prod_array[$row[csf("po_id")]]["today_wash_prod"]+=$row[csf("today_wet_wash_prod")] + $row[csf("today_dry_wash_prod")];

			$wash_prod_array[$row[csf("po_id")]]["total_wash_prod"]+=$row[csf("total_wet_wash_prod")] + $row[csf("total_dry_wash_prod")];
		}
		// print_r($wash_prod_array);
		/*================================================================================================
		/										cut and lay data										 /	
		=================================================================================================*/
		$po_product_cond2=str_replace("a.po_break_down_id in", "b.id not in", $po_product_cond);

		$order_sql_lay="SELECT a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id, c.country_id, c.country_ship_date, c.color_number_id,c.size_number_id, c.order_quantity ,max(b.shiping_status) as shiping_status,
		sum(case when d.entry_date='$txt_production_date' then f.size_qty else 0 end ) as today_lay,sum(f.size_qty) as total_lay
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
		where a.id=b.job_id and b.id=c.po_break_down_id and c.job_no_mst=d.job_no and d.id=e.mst_id and d.id=f.mst_id and b.id=f.order_id and c.color_number_id=e.color_id and c.size_number_id=f.size_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and b.shiping_status <> 3 and e.is_deleted=0 and f.status_active=1  and f.is_deleted=0 $str_po_cond_lay $prod_po_cond  
		group by a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id ,c.size_number_id,c.order_quantity ";
		$lay_new_po=array();
		$po_col_size_qnty_array=array();
		foreach(sql_select($order_sql_lay) as $row )
		{
			if($po_wise_color_production[$row[csf("po_id")]][$row[csf("color_number_id")]]=="")
			{
				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$row[csf("shiping_status")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				
				if($po_col_size_qnty_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_quantity"]=="")
				{
					$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

					$po_col_size_qnty_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

				}				

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_lay"]+=$row[csf("today_lay")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_lay"]+=$row[csf("total_lay")];
				//$lay_new_po[$row[csf("po_id")]]=$row[csf("po_id")];
			}
		}

		$po_product_cond_cut_lay=str_replace("a.po_break_down_id", "c.order_id", $po_product_cond);
		$cut_lay_sql="SELECT a.entry_date, c.order_id, b.gmt_item_id, b.color_id ,c.country_id, (c.size_qty) as qntys from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b ,ppl_cut_lay_bundle c, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 and c.order_id=g.ref_val and g.entry_form=43 and g.ref_from=1 and g.user_id=$user_id"; 
		//$po_product_cond_cut_lay group by a.entry_date, c.order_id, b.gmt_item_id, b.color_id ,c.country_id
		$cut_lay_array=array();
		foreach(sql_select($cut_lay_sql) as $keys=>$vals)
		{
			$cut_lay_array[$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("country_id")]][$vals[csf("color_id")]][change_date_format($vals[csf("entry_date")])]["today_lay"]+=$vals[csf("qntys")];
			$cut_lay_array[$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("country_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("qntys")];
		}

		$order_qnty_pcs_arr=array();
		$order_qnty_pcs_sql="SELECT a.po_break_down_id,a.job_no_mst,a.color_number_id,a.item_number_id, a.country_id, a.country_ship_date,   a.order_quantity as qntys from wo_po_color_size_breakdown a, GBL_TEMP_ENGINE g where a.status_active in(1,2,3) and a.is_deleted=0 and a.po_break_down_id=g.ref_val and g.entry_form=43 and g.ref_from=1 and g.user_id=$user_id";
		//$po_product_cond  group by a.po_break_down_id,a.job_no_mst,a.color_number_id,a.item_number_id, a.country_id, a.country_ship_date
		foreach(sql_select($order_qnty_pcs_sql) as $pcs_key=>$pcs_val)
		{
			$order_qnty_pcs_arr[$pcs_val[csf("job_no_mst")]][$pcs_val[csf("po_break_down_id")]][$pcs_val[csf("item_number_id")]][$pcs_val[csf("country_id")]][change_date_format($pcs_val[csf("country_ship_date")])][$pcs_val[csf("color_number_id")]]+=$pcs_val[csf("qntys")];
		}
		
		
		if(empty($all_po_id_array))
		{
			echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px">No Data Found.</div>'; die;
		}
		
		
		$color_id_arr=return_library_array( "SELECT a.id,a.color_number_id from wo_po_color_size_breakdown a, GBL_TEMP_ENGINE g where a.status_active in(1,2,3) and a.color_number_id>0 and a.po_break_down_id=g.ref_val and g.entry_form=43 and g.ref_from=1 and g.user_id=$user_id", "id", "color_number_id");
		
		
		 
		/*================================================================================================
		/										shipment data											 /	
		=================================================================================================*/ 
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id ,sum(CASE WHEN a.entry_form <> 85 and a.ex_factory_date='$txt_production_date' THEN b.production_qnty ELSE 0 END)-
		sum(CASE WHEN a.entry_form=85 and a.ex_factory_date='$txt_production_date' THEN b.production_qnty ELSE 0 END) as today_ex_factory_qnty,
		sum(CASE WHEN a.entry_form <> 85   THEN b.production_qnty ELSE 0 END) 
		-
		sum(CASE WHEN a.entry_form=85   THEN b.production_qnty ELSE 0 END) as total_ex_factory_qnty ,a.total_carton_qnty
		 from pro_ex_factory_mst a, pro_ex_factory_dtls b, GBL_TEMP_ENGINE g 
		 where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id=g.ref_val and g.entry_form=43 and g.ref_from=1 and g.user_id=$user_id
		 group by a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id,a.total_carton_qnty");
		 //$po_product_cond 
		
		foreach($ex_factory_data as $exRow)
		{			 
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$color_id_arr[$exRow[csf('color_size_break_down_id')]]]['today_ex_factory_qnty']+=$exRow[csf('today_ex_factory_qnty')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$color_id_arr[$exRow[csf('color_size_break_down_id')]]]['total_ex_factory_qnty']+=$exRow[csf('total_ex_factory_qnty')];
		}
		
		$r_id2=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (43)");
		oci_commit($con);
		disconnect($con);
		ob_start();	
		
		?>
        <div>
        	<table width="3260" cellspacing="0" >
        		<tr class="form_caption" style="border:none;">
        			<td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
        				Daily RMG Production Report
        			</strong></td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="30" align="center" style="border:none; font-size:14px;">
        				<strong>
        					Working Company Name : <? 
        					$comp_names=""; 
        					foreach(explode(",",$cbo_company_name) as $vals) 
        					{
        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
        					}
        					echo $comp_names;
        					 ?>
        				</strong>                                
        			</td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold" >
        				<?
        				if(str_replace("'","",trim($txt_production_date))!="")
        				{
        					echo "Date ".change_date_format($txt_production_date)  ;
        				}
        				?>
        			</td>
        		</tr>
        	</table>
            <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:3260px">Details Part</div>
            
			<div style="float:left; width:3260px">
				<table width="3260" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
					<thead>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="30" ><p>SL</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="115"><p>Buyer</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="115"><p>Buyer Client</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="115"><p>Style Ref.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="80"><p>Job No.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="115"><p>Order No.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="80"><p>Country</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="80"><p>Country Shipdate</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="120"><p>Garment Item</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="80"><p>Color</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="80"><p>Order Qty.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" colspan="2" width="120"><p>Cut & Lay</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" colspan="2" width="120"><p>Cutting QC Qty</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" colspan="2" width="120"><p>Sewing Input</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" colspan="2" width="120"><p>Sewing Output</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="100"><p>Today Sewing <br>Reject</p></th>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="100"><p>Total Sewing<br> Reject</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="60"><p>Sewing <br>WIP</p></th> 

						<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Wash Send</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Wash Prod</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Wash Rcv</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="3" width="180"><p>Finishing</p></th>

						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Poly Entry</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="60"><p>Poly WIP</p></th> 
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p>Packing &Fin.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="60"><p>Pac &Fin. WIP</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="120"><p> Ex-Factory</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="60"><p>Ex-Fac. WIP</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Status</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="60"><p>Line</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="60"><p>Remarks</p></th>
					</tr>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Balance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>WIP</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Balance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Balance</p></th>

						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="60"><p>Total</p></th>

					</tr>
						
						  
					   
					</thead>
				</table>
				<div style="max-height:425px; overflow-y:scroll; width:3280px" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"  width="3260" rules="all" id="table_body" >
						<?
						$po_wise_ship_span_arr=array();
						// echo "<pre>";
						// print_r($production_main_array);die();
						foreach($production_main_array as $buyer_id=>$job_data)
						{
							foreach($job_data as $job_id=>$po_data)
							{
								foreach($po_data as $po_id=>$country_data)
								{
									$po_wise_ship_span=0;
									foreach($country_data as $country_id=>$shipdate_data)
									{
										foreach($shipdate_data as $shipdate_id=>$item_data)
										{
											foreach($item_data as $item_id=>$color_data)
											{
												foreach($color_data as $color_id=>$rows)
												{
													$po_wise_ship_span++;
												}
											}
										}
									}
									$po_wise_ship_span_arr[$po_id]=$po_wise_ship_span;
								}
							}
						}
					 
						$k=1;		
			
						
						$gr_wise_order_qnty=0;
						$gr_wise_lay_qnty_today=0;
						$gr_wise_lay_qnty_total=0;
						$gr_wise_today_cutting=0;
						$gr_wise_total_cutting=0;
						$gr_wise_today_sewing_input=0;
						$gr_wise_total_sewing_input=0;
						$gr_wise_today_sewing_output=0;
						$gr_wise_total_sewing_output=0;
						$gr_wise_today_sewing_rej=0;
						$gr_wise_total_sewing_rej=0;
						$gr_wise_today_poly=0;
						$gr_wise_total_poly=0;
						$gr_wise_today_finish=0;
						$gr_wise_total_finish=0;
						$gr_wise_blance_finish=0;
						$gr_wise_today_pac=0;
						$gr_wise_total_pac=0;
						$gr_wise_today_ex_fac=0;
						$gr_wise_total_ex_fac=0;
						$gr_wise_sewing_wip=0;
						$gr_wise_poly_wip=0;
						$gr_wise_pack_wip=0;
						$gr_wise_exfac_wip=0;
						
						foreach($production_main_array as $buyer_id=>$job_data)
						{

							foreach($job_data as $job_id=>$po_data)
							{
								

								foreach($po_data as $po_id=>$country_data)
								{
									$kk=0;	
									
									
									 
									foreach($country_data as $country_id=>$shipdate_data)
									{
										foreach($shipdate_data as $shipdate_id=>$item_data)
										{
											foreach($item_data as $item_id=>$color_data)
											{
												foreach($color_data as $color_id=>$rows)
												{
													// echo "<pre>";
													// print_r($rows);
													$today_ex_fac=$ex_factory_arr[$po_id][$item_id][$country_id][$color_id]['today_ex_factory_qnty'];
													$total_ex_fac=$ex_factory_arr[$po_id][$item_id][$country_id][$color_id]['total_ex_factory_qnty'];
													$order_qntys=$order_qnty_pcs_arr[$job_id][$po_id][$item_id][$country_id][change_date_format($shipdate_id)][$color_id];

													$cut_lays_qnty_today=$cut_lay_array[$po_id][$item_id][$country_id][$color_id][change_date_format(str_replace("'", "", $txt_production_date))]["today_lay"];
													

													$cut_lays_qnty_total=$cut_lay_array[$po_id][$item_id][$country_id][$color_id]["total_lay"];

													$today_wash_prod=$wash_prod_array[$po_id]["today_wash_prod"];
													$total_wash_prod=$wash_prod_array[$po_id]["total_wash_prod"];

													if(!$cut_lays_qnty_today)
													{
														$cut_lays_qnty_today=$rows["today_lay"];
 													}
													if(!$cut_lays_qnty_total)
													{
														$cut_lays_qnty_total=$rows["total_lay"];
													}
													if(!$order_qntys)
													{
														$order_qntys=$rows["order_quantity"];
													}
													$sewing_wip= ($rows["total_sewing_output"]+$rows["total_sewing_reject_qty"])-$rows["total_sewing_input"];

													



												


										
													

													if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $k; ?>">
													 
														<td style="word-wrap: break-word;word-break: break-all;"  align="left"  width="30"><p><? echo $k;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="115"><p><? echo $buyer_library[$buyer_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="115"><p><? echo $client_array[$rows["client_id"]]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="115"><p><? echo $rows["style_ref_no"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="80"><p><? echo $rows["job_no_prefix_num"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="115"><p><? echo $rows["po_number"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"  align="left"    width="80"><p><? echo $country_library[$country_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="80"><p><? echo change_date_format($shipdate_id); ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="120"><p><? echo $garments_item[$item_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="80"><p><? echo $color_Arr_library[$color_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="80"><p><? echo $order_qntys;?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60"><p><? echo $cut_lays_qnty_today;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60"><p><? echo $cut_lays_qnty_total;?></p></td>


														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="60"><p><? echo $rows["today_cutting"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="60"><p><? echo $rows["total_cutting"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="60"><p><? echo $rows["today_sewing_input"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="60"><p><? echo $rows["total_sewing_input"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="60"><p><? echo $rows["today_sewing_output"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="60"><p><? echo $rows["total_sewing_output"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="100"><p><? echo $rows["today_sewing_reject_qty"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="100"><p><? echo $rows["total_sewing_reject_qty"];?></p></td>
 														<td style="word-wrap: break-word;word-break: break-all;"    align="right"  width="60"><p><? echo $sewing_wip ;?></p></td> 

 														<td width="60" align="right"><p><? echo $rows["today_wash_snd"];?></p></td>
														<td width="60" align="right"><p><? echo $rows["total_wash_snd"];?></p></td>
														<td width="60" align="right"><p><? echo $rows["total_wash_snd"] - $rows["today_wash_snd"];?></p></td>
														<? if($kk==0){?>
														<td width="60" rowspan="<? echo $po_wise_ship_span_arr[$po_id];?>" align="right"><p><? echo number_format($today_wash_prod,0);?></p></td>
														<td width="60" rowspan="<? echo $po_wise_ship_span_arr[$po_id];?>" align="right"><p><? echo number_format($total_wash_prod,0);?></p></td>
														<td width="60" rowspan="<? echo $po_wise_ship_span_arr[$po_id];?>" align="right"><p><? echo number_format(($total_wash_prod-$today_wash_prod),0);?></p></td>
														<?}?>
														<td width="60" align="right"><p><? echo $rows["today_wash_rcv"];?></p></td>
														<td width="60" align="right"><p><? echo $rows["total_wash_rcv"];?></p></td>
														<td width="60" align="right"><p><? echo $rows["total_wash_rcv"] - $rows["today_wash_rcv"];?></p></td>
														<td width="60" align="right"><p><? echo $rows["today_iron"];?></p></td>
														<td width="60" align="right"><p><? echo $rows["total_iron"];?></p></td>
														<td width="60" align="right"><p><? echo $rows["total_iron"] - $rows["today_iron"];?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="60"><p><? echo $rows["today_poly"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="60"><p><? echo $rows["total_poly"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"    align="right"  width="60"><p><? echo $poly_wip=$rows["total_poly"]- $rows["total_sewing_output"];?></p></td> 
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="60"><p><? echo $rows["today_packing"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"  align="right"   width="60"><p><? echo $rows["total_packing"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60"><p><? echo  $packing_wip=  $rows["total_packing"]- $rows["total_poly"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"  align="right"   width="60"><p><? echo $today_ex_fac;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"  width="60"><p><? echo $total_ex_fac ;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="right"   width="60"><p><? echo $ex_fac_wip= $total_ex_fac-$order_qntys;?></p></td>
														 
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="80"><p><? echo  $shipment_status[$rows['shiping_status']];?></p></td>


														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="60"><p><? echo chop($rows['sewing_line'],',');?></p></td>	 
														
														<td style="word-wrap: break-word;word-break: break-all;"   align="left"   width="60"><p>	
															<a href="##" onClick="openmypage_remarks(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $country_id;?>,<? echo $color_id;?>, 'remarks_popup');" >Remarks</a>

														</p></td>
								 
													</tr>	
													<?
													$kk++;

													$k++;
													
													$gr_wise_order_qnty+=$order_qntys; 
													$gr_wise_lay_qnty_today+=$cut_lays_qnty_today;
													$gr_wise_lay_qnty_total+=$cut_lays_qnty_total;
													$gr_wise_today_cutting+=$rows["today_cutting"];
													$gr_wise_total_cutting+=$rows["total_cutting"];
													$gr_wise_today_sewing_input+=$rows["today_sewing_input"];
													$gr_wise_total_sewing_input+=$rows["total_sewing_input"];
													$gr_wise_today_sewing_output+=$rows["today_sewing_output"];
													$gr_wise_total_sewing_output+=$rows["total_sewing_output"];
													$gr_wise_today_poly+=$rows["today_poly"];
													$gr_wise_total_poly+=$rows["total_poly"];
													$gr_wise_today_pac+=$rows["today_packing"];
													$gr_wise_total_pac+=$rows["total_packing"];
													$gr_wise_today_ex_fac+=$today_ex_fac;
													$gr_wise_total_ex_fac+=$total_ex_fac;
													$gr_wise_sewing_wip+=$sewing_wip;
													$gr_wise_poly_wip+=$rows["total_poly"]-$rows["total_sewing_output"];
													$gr_wise_pack_wip+=$rows["total_packing"]-$rows["total_poly"];
													$gr_wise_exfac_wip+=$total_ex_fac-$order_qntys;
													$gr_wise_today_sewing_rej+=$rows["today_sewing_reject_qty"];
													$gr_wise_total_sewing_rej+=$rows["total_sewing_reject_qty"];
													$gr_wise_today_finish+=$rows["today_iron"];
													$gr_wise_total_finish+=$rows["total_iron"];
													$gr_wise_blance_finish+=$rows["total_iron"] - $rows["today_iron"];0;

												}
											}
										}
									} 
								}
							}
							 
						} 
						?>
					</table>
				</div>
				<table border="1" class="tbl_bottom" width="3260" rules="all" id="report_table_footer_1" >
					<tr >
						 
						<td style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30">&nbsp;</td>
						<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
						<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
						<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
						<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80">&nbsp;</td>
						<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
						<td style="word-wrap: break-word;word-break: break-all;"  align="center"    width="80">&nbsp;</td>
						<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80">&nbsp;</td>
						<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="120"><strong>Grand Total</strong></td>
						<td align="center"   align="center"   width="80">&nbsp;</p></td>

						<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: right;"  align="right" id="grand_total_order"><p> </p> <? echo $gr_wise_order_qnty;?></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_today_lay"  align="right"><p> </p> <? echo $gr_wise_lay_qnty_today;?></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_total_lay"  align="right"><p> </p> <? echo $gr_wise_lay_qnty_total;?></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;"  id="grand_today_cut" align="right"><p><? echo $gr_wise_today_cutting; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;"  id="grand_total_cut" align="right"><p><? echo $gr_wise_total_cutting; ?></p></td>
						<td  width="60" style="word-wrap: break-word;word-break: break-all;text-align: right;"  id="grand_today_sewin" align="right"><p><? echo $gr_wise_today_sewing_input; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;"  id="grand_total_sewin" align="right"><p><? echo $gr_wise_total_sewing_input; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_today_sewout" align="right"><p><? echo $gr_wise_today_sewing_output; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_total_sewout" align="right"><p><? echo $gr_wise_total_sewing_output; ?></p></td>
						<td  width="100" style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_today_sew_rej" align="right"><p><? echo $gr_wise_today_sewing_rej; ?></p></td>
						<td width="100"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_total_sewrej" align="right"><p><? echo $gr_wise_total_sewing_rej; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_sewingwip" align="right"><p><? echo $gr_wise_sewing_wip; ?></p></td>

						<td width="60" id="grand_today_wash_snd"><p></p></td>
						<td width="60" id="grand_total_wash_snd"><p></p></td>
						<td width="60" id="grand_wash_balance"><p></p></td>
						<td width="60" id="grand_today_wash_prod"><p></p></td>
						<td width="60" id="grand_total_wash_prod"><p></p></td>
						<td width="60" id="grand_wash_prod_bal"><p></p></td>
						<td width="60" id="grand_today_wash_rcv"><p></p></td>
						<td width="60" id="grand_total_wash_rcv"><p></p></td>
						<td width="60" id="grand_wash_rcv_bal"><p></p></td>
						<td width="60" id="grand_today_iron"><p><? echo $gr_wise_today_finish?></p></td>
						<td width="60" id="grand_total_iron"><p></p><?echo $gr_wise_total_finish?></td>
						<td width="60" id="grand_iron_balance"><p><?echo $gr_wise_blance_finish?></p></td>


						<td  width="60" style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_today_poly" align="right"><p><? echo $gr_wise_today_poly; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_total_poly" align="right"><p><? echo $gr_wise_total_poly; ?></p></td>
						<td  width="60" style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_poly_wip" align="right"><p><? echo $gr_wise_poly_wip; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_today_pack" align="right"><p><? echo $gr_wise_today_pac; ?></p></td>
						<td  width="60" style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_total_pack" align="right"><p><? echo $gr_wise_total_pac; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_pack_wip" align="right"><p><?echo $gr_wise_pack_wip; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_today_exfac" align="right"><p><? echo $gr_wise_today_ex_fac; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_total_exfac" align="right"><p><? echo $gr_wise_total_ex_fac; ?></p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;text-align: right;" id="grand_exfac_wip" align="right"><p><? echo $gr_wise_exfac_wip; ?></p></td>
						<td width="80"  style="word-wrap: break-word;word-break: break-all;"  align="right"><p> </p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;"  align="right"><p> </p></td>
						<td width="60"  style="word-wrap: break-word;word-break: break-all;"  align="right"><p></p></td>
					</tr>
				</table>
			</div>
		 </div> 
        <?
	}
	else if($type==2)
	{
			
		$str_po_cond="";
		if($cbo_company_name!=0) $str_po_cond.=" and d.serving_company in($cbo_company_name)";

		if($cbo_buyer_name!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";		
		if($cbo_brand_name!=0) $str_po_cond.=" and a.brand_id=$cbo_brand_name";
		if($cbo_season_name!=0) $str_po_cond.=" and a.season_buyer_wise=$cbo_season_name";
		if($cbo_location>0) $str_po_cond.=" and d.location=$cbo_location";
		if($cbo_floor>0) $str_po_cond.=" and d.floor_id=$cbo_floor";
		// if($txt_production_date != "") $str_po_cond.=" and d.production_date=$txt_production_date";
		if($cbo_job_year>0)
		{
			if($db_type==0)
			{
				$str_po_cond .=" and year(a.insert_date)='$cbo_job_year'";
			}
			else
			{
				$str_po_cond .=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
			}	
		}
		if($hidden_job_id!="")
		{
			$str_po_cond.=" and a.id in($hidden_job_id)";
		}
		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);

		


		echo $order_sql="SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no  ,   c.color_number_id, sum(c.order_quantity) as order_quantity, 

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input  



		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and b.shiping_status <> 3 and e.is_deleted=0  $str_po_cond 
		group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  ,  c.color_number_id,e.cut_no ";

		 

		foreach(sql_select($order_sql) as $vals)
		{
			$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["today_cutting"]+=$vals[csf("today_cutting")];

			$today_total += $cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["today_cutting"];

			$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["total_cutting"]+=$vals[csf("total_cutting")];

			// $total_cutting += $cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["total_cutting"];

			$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

			$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

		}


		 
		$order_sql_lay="SELECT d.working_company_id,d.location_id,d.cutting_no,e.order_cut_no,a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id , c.color_number_id, c.order_quantity, e.batch_id
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.job_no_mst=d.job_no and d.id=e.mst_id and d.id=f.mst_id and b.id=f.order_id and c.color_number_id=e.color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1   and e.is_deleted=0 and f.status_active=1  and f.is_deleted=0 $str_po_cond_lay  ";
		$production_main_array=array();
		$all_po_lay_id_array=array();
		$po_col_size_qnty_array=array();
		foreach(sql_select($order_sql_lay) as $row )
		{
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["working_company_id"]=$row[csf("working_company_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["location_id"]=$row[csf("location_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["order_cut_no"]=$row[csf("order_cut_no")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["buyer_name"]=$row[csf("buyer_name")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["batch_id"]=$row[csf("batch_id")];

				 

				if($production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["order_quantity"]=="")
				{
					$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["order_quantity"]+=$row[csf("order_quantity")];

				}	

				$all_po_lay_id_array[$row[csf("po_id")]]=	$row[csf("po_id")];					
			
		}
		$all_po_lay_id=implode(",", $all_po_lay_id_array);
		if( count($all_po_lay_id)>0 )
		{
			$po_conds=" and a.po_break_down_id in($all_po_lay_id)";
		}

		$booking_no_fin_qnty_array=array();
		$booking_sql="SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0   $po_conds";
		foreach(sql_select($booking_sql) as $vals)
		{
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["booking_no"]=$vals[csf("booking_no")];
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];

		}
		$batch_sql=sql_select("SELECT id,booking_no,batch_no,color_id from pro_batch_create_mst where status_active=1 and is_deleted=0 ");
		foreach($batch_sql as $rows)
		{
			$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
			$batch_mst_id_arr[$rows[csf("booking_no")]]=$rows[csf("id")];
		}
		/*echo "<pre>";
		 print_r($batch_mst_id_arr);die;*/
		 
		 $issue_sql=sql_select("SELECT batch_id,issue_qnty from inv_finish_fabric_issue_dtls where status_active=1 and is_deleted=0 ");
		 foreach($issue_sql as $values)
		 {
		 	$issue_qnty_arr[$values[csf("batch_id")]]+=$values[csf("issue_qnty")];
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
								$cut+=$cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["total_cutting"];;
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
		$sql_consumtiont_qty=sql_select(" SELECT a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY a.job_no, a.body_part_id");

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
        	<table width="2010" cellspacing="0" >
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
        					foreach(explode(",",$cbo_company_name) as $vals) 
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
        				if(str_replace("'","",trim($txt_production_date))!="")
        				{
        					echo "Date ".change_date_format($txt_production_date)  ;
        				}
        				?>
        			</td>
        		</tr>
        	</table>
			<div style="float:left;">
				<table width="2010" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
					<thead>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Working Company</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Location</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer Name</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Job No</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Style Reff</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order No</p></th>
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
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Cut. Blance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order - Input Blance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Inhand Qty</p></th>

					</tr>
						
						  
					   
					</thead>
				</table>
				<div style="max-height:500px; overflow-y:scroll; width:2030px" id="scroll_body">
					<table cellspacing="0" cellpadding="2" border="1" class="rpt_table"  width="2010" rules="all" id="table_body" >
					<?
					$k=1;
					$jj=1;		
					
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
										$pp=0;
										$fin_req = 0;
										foreach($cutting_data as $cutting_id=>$row)
										{
											$fin_req=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
											$issue_qty=$issue_qnty_arr[$batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]]];
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
											$order_wise_cut_balance += $cut_balance;

											$order_wise_today_input += $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_sewing_input"];
											$order_wise_total_input += $total_sewing_input;
											$order_wise_input_balance += $input_balance;
											$order_wise_inhand_qty += $total_cutting_qnty-$total_sewing_input;

											// style wise
											

											$style_wise_cut_today += $today_cutting_qnty;		
											$style_wise_cut_total += $total_cutting_qnty;		
											$style_wise_cut_balance += $cut_balance;

											$style_wise_input_today += $today_input;		
											$style_wise_input_total += $total_sewing_input;		
											$style_wise_input_balance += $input_balance;		
											$style_wise_inhand_qty += $order_wise_inhand_qty;
											
										 
											if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
													<?
													$jj++;
													if($pp==0)
													{
														$order_wise_subtotal += $row["order_quantity"];
														$style_wise_order_qty += $row["order_quantity"];

														?>
													 
														<td valign="middle" rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $company_library[$row["working_company_id"]]; ?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $location_library[$row["location_id"]]; ?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $buyer_library[$row["buyer_name"]]; ?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $job_id; ?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $style_id;?></p></td>
														 
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $po_id;?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"  align="center"    width="80"><p><? echo $row["order_quantity"]; ?></p></td> 
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"] ;?></p></td>
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $batch_mst_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]][$color_id];?></p></td>
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $color_Arr_library[$color_id];?></p></td>


														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $fin_req;?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><a href="##" onClick="openmypage_fab_issue(<? echo $batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]];?>, 'fab_issue_popup');" > <p><? echo $issue_qty;?></p></a></td>
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $req_issue_bal;?></p></td>
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo number_format($possible_cut_pcs,4);?></p></td>
													<?
													$order_wise_fab_req += $fin_req;
													$order_wise_fin_fab_req += $issue_qty;
													$order_wise_fab_issued_balance += $req_issue_bal;
													$order_wise_fab_possible_qty += $possible_cut_pcs;
													}
													//$pp++;
													?>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $cutting_id;?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["order_cut_no"];?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $today_cutting_qnty;?></p></td>
 
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><a href="##" onClick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'<? echo  $cutting_id;?>',<? echo $color_id;?>,'1', 'cutting_sewing_action');" > <p><? echo $total_cutting_qnty;?></p></a></td>
														<?
														if($pp==0)
														{
															?>
															<td valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]+1; ?>"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $cut_balance = $row["order_quantity"]-$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["cut"];?></p></td>
															<?
														}
														?>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $today_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_sewing_input"];?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'<? echo  $cutting_id;?>',<? echo $color_id;?>,'4', 'cutting_sewing_action');" ><p><? echo $total_sewing_input;?></a></p></td>
														<?
														if($pp==0)
														{


															?>
															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]+1; ?>"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo 	$input_balance = $row["order_quantity"]-$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["sew"];?></p></td>
														<?
														}
														$pp++;
														?>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $total_cutting_qnty-$total_sewing_input;?></p></td>								 
													</tr>
											<?											
											
											
										}
										
										//$style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]=$color_span;
										?>
										<tr bgcolor="#E4E4E4">
											<td colspan="16" align="right"><b>Color Wise Sub Total</b></td>
											<td></td>
											<td align="right"><b><?php echo $color_wise_today_cutting;?></b></td>
											<td align="right"><b><?php echo $color_wise_total_cutting;?></b></td>
											
											<td align="right"><b><?php echo $color_wise_today_sewing_input;?></b></td>
											<td align="right"><b><?php echo $color_wise_total_sewing_input;?></b></td>
											<td></td>
										
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
									<td colspan="7" align="center"><b>Order Wise Sub Total</b></td>
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
						<td colspan="7" align="right"><b>Style Wise Sub Total</b></td>
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
										
					</table>					  
				</div>
			</div>
		 </div> 
        <?
	}
	else if($type==3)//show4 kamrul //
	{
		
			
		$str_po_cond="";
		if($cbo_company_name!=0) $str_po_cond.=" and d.serving_company in($cbo_company_name)";

		if($cbo_buyer_name!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";		
		if($cbo_brand_name!=0) $str_po_cond.=" and a.brand_id=$cbo_brand_name";
		if($cbo_season_name!=0) $str_po_cond.=" and a.season_buyer_wise=$cbo_season_name";
		if($cbo_location>0) $str_po_cond.=" and d.location=$cbo_location";
		if($cbo_floor>0) $str_po_cond.=" and d.floor_id=$cbo_floor";
		if($cbo_job_year>0)
		{
			if($db_type==0)
			{
				$str_po_cond .=" and year(a.insert_date)='$cbo_job_year'";
			}
			else
			{
				$str_po_cond .=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
			}	
		}
		if($hidden_job_id!="")
		{
			$str_po_cond.=" and a.id in($hidden_job_id)";
		}
		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);

		//if($txt_production_date!="")  $str_po_cond .=" and d.production_date= '$txt_production_date'";  

	    $order_sql="SELECT a.id as job_id, a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.client_id, b.id as po_id, b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id, sum(c.order_quantity) as order_quantity,max(b.shiping_status) as shiping_status,

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_print_issue ,

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=1  then e.production_qnty else 0 end ) as total_print_issue,

		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_print_rcv ,

		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=1  then e.production_qnty else 0 end ) as total_print_rcv,

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=2 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_embl_issue ,

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=2  then e.production_qnty else 0 end ) as total_embl_issue,

		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=2 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_embl_rcv ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=2  then e.production_qnty else 0 end ) as total_embl_rcv,
		sum(case when d.production_type=4 and e.production_type=4 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input ,

		sum(case when d.production_type=5 and e.production_type=5 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_output ,
		sum(case when d.production_type=5 and e.production_type=5   then e.production_qnty else 0 end ) as total_sewing_output ,

		sum(case when d.production_type=11 and e.production_type=11 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_poly ,
		sum(case when d.production_type=11 and e.production_type=11   then e.production_qnty else 0 end ) as total_poly ,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_packing ,
		sum(case when d.production_type=8 and e.production_type=8   then e.production_qnty else 0 end ) as total_packing ,

		sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=0 and d.production_date='$txt_production_date' then e.reject_qty else 0 end )- sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_reject_qty ,
		sum(case when d.production_type in( 5) and e.production_type in( 5) and e.is_rescan=0  then e.reject_qty else 0 end )  -sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=1   then e.production_qnty else 0 end ) as total_sewing_reject_qty 
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and b.shiping_status <> 3 and e.is_deleted=0  $str_po_cond $prod_po_cond
		group by a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.client_id, b.id , b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id,a.id";
		
		// echo $order_sql;die;
		
		
		$sql_po_result=sql_select($order_sql);
		$production_main_array=array();
		$po_wise_color_production=array();
		$jobIdArr=array();
		foreach($sql_po_result as $row)
		{
			//if($row[csf("today_cutting")] || $row[csf("today_sewing_input")] || $row[csf("today_sewing_output")] ||  $row[csf("today_poly")] ||    $row[csf("today_packing")])
			//{
				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$row[csf("shiping_status")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["client_id"]=$row[csf("client_id")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_cutting"]+=$row[csf("today_cutting")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_cutting"]+=$row[csf("total_cutting")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_print_issue"]+=$row[csf("today_print_issue")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_print_issue"]+=$row[csf("total_print_issue")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_print_rcv"]+=$row[csf("today_print_rcv")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_print_rcv"]+=$row[csf("total_print_rcv")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_embl_issue"]+=$row[csf("today_embl_issue")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_embl_issue"]+=$row[csf("total_embl_issue")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_embl_rcv"]+=$row[csf("today_embl_rcv")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_embl_rcv"]+=$row[csf("total_embl_rcv")];



				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_input"]+=$row[csf("today_sewing_input")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_input"]+=$row[csf("total_sewing_input")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_output"]+=$row[csf("today_sewing_output")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_output"]+=$row[csf("total_sewing_output")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_packing"]+=$row[csf("today_packing")];




				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_packing"]+=$row[csf("total_packing")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_reject_qty"]+=$row[csf("today_sewing_reject_qty")];




				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_reject_qty"]+=$row[csf("total_sewing_reject_qty")];


				$all_po_id.=$row[csf("po_id")].",";

			//}

				if($row[csf("total_cutting")] || $row[csf("total_sewing_input")] || $row[csf("total_sewing_output")] ||  $row[csf("total_poly")] ||    $row[csf("total_packing")])
				{
					$po_wise_color_production[$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("color_number_id")];

				}

				$jobIdArr[$row[csf("job_id")]]=$row[csf("job_id")];

			
		}
		// echo "<pre>";print_r($jobIdArr);die;
		$all_po_id=implode(',',array_unique(explode(",",chop($all_po_id,","))));
		 
		
		$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst where po_number_id in ('".$all_po_id."') group by po_number_id, template_id","po_number_id","template_id");

		$job_id_cond = where_con_using_array($jobIdArr,0,"job_id");

		$cm_arr = return_library_array("SELECT job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost");
		$costing_per_arr = return_library_array("SELECT job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per");
		$exchange_rate_arr = return_library_array("SELECT job_no, exchange_rate from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","exchange_rate");

		
		$client_array = array();
		$sql_client=sql_select("SELECT a.id, a.buyer_name
		FROM lib_buyer a, lib_buyer_tag_company b
   		WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.buyer_id = a.id AND a.id IN 
   		(SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (7)) 
   		group by a.id, a.buyer_name
		ORDER BY buyer_name");

		foreach ($sql_client as $key => $value) {
			$client_array[$value[csf('id')]] = $value[csf('buyer_name')];
		}
		// echo "<pre>";
		// print_r($client_array);

		$po_product_cond="";
		if($db_type==0)
		{
			if($all_po_id!="") $po_product_cond =" and a.po_break_down_id in($all_po_id)";
		}
		else
		{
			if($all_po_id!="")
			{
				$all_po_id_arr=array_chunk(explode(",",$all_po_id),999);
				$p=1;
				if(!empty($all_po_id_arr))
				{
					foreach($all_po_id_arr as $po_id)
					{
						if($p==1) $po_product_cond =" and (a.po_break_down_id in(".implode(',',$po_id).")"; else $po_product_cond .=" or a.po_break_down_id in(".implode(',',$po_id).")";
						$p++;
					}
					$po_product_cond .=" )";
				}
				
			}
		}

		$po_product_cond2=str_replace("a.po_break_down_id in", "b.id not in", $po_product_cond);

		$order_sql_lay="SELECT a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id,c.size_number_id, c.order_quantity ,max(b.shiping_status) as shiping_status ,
		sum(case when d.entry_date='$txt_production_date' then f.size_qty else 0 end ) as today_lay,sum(f.size_qty) as total_lay
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
		where a.id=b.job_id and b.id=c.po_break_down_id and c.job_no_mst=d.job_no and d.id=e.mst_id and d.id=f.mst_id and b.id=f.order_id and c.color_number_id=e.color_id and c.size_number_id=f.size_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and b.shiping_status <> 3 and e.is_deleted=0 and f.status_active=1  and f.is_deleted=0 $str_po_cond_lay $prod_po_cond
		group by a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id ,c.size_number_id,c.order_quantity ";
		$lay_new_po=array();
		$po_col_size_qnty_array=array();
		foreach(sql_select($order_sql_lay) as $row )
		{
			if($po_wise_color_production[$row[csf("po_id")]][$row[csf("color_number_id")]]=="")
			{
				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$row[csf("shiping_status")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				
				if($po_col_size_qnty_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_quantity"]=="")
				{
					$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

					$po_col_size_qnty_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

				}
				

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_lay"]+=$row[csf("today_lay")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_lay"]+=$row[csf("total_lay")];
				//$lay_new_po[$row[csf("po_id")]]=$row[csf("po_id")];
			}
		}
	
		$order_qnty_pcs_arr=array();
		$order_qnty_pcs_sql="SELECT a.po_break_down_id,a.job_no_mst,a.color_number_id,a.item_number_id  , a.country_id, a.country_ship_date,   sum(a.order_quantity) as qntys from wo_po_color_size_breakdown a where a.status_active in(1,2,3) and a.is_deleted=0 $po_product_cond  group by  a.po_break_down_id,a.job_no_mst,a.color_number_id,a.item_number_id  , a.country_id, a.country_ship_date";
		foreach(sql_select($order_qnty_pcs_sql) as $pcs_key=>$pcs_val)
		{
			$order_qnty_pcs_arr[$pcs_val[csf("job_no_mst")]][$pcs_val[csf("po_break_down_id")]][$pcs_val[csf("item_number_id")]][$pcs_val[csf("country_id")]][change_date_format($pcs_val[csf("country_ship_date")])][$pcs_val[csf("color_number_id")]]+=$pcs_val[csf("qntys")];
		}
		
		
		if($po_product_cond=="")
		{
			echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px">No Data Found.</div>'; die;
		}
		
		
		$color_id_arr=return_library_array( "select a.id,a.color_number_id from wo_po_color_size_breakdown a where a.status_active in(1,2,3) and a.color_number_id>0 $po_product_cond", "id", "color_number_id");
		
		
		 
		 
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id ,sum(CASE WHEN a.entry_form <> 85 and a.ex_factory_date='$txt_production_date' THEN b.production_qnty ELSE 0 END)-
		sum(CASE WHEN a.entry_form=85 and a.ex_factory_date='$txt_production_date' THEN b.production_qnty ELSE 0 END) as today_ex_factory_qnty,
		sum(CASE WHEN a.entry_form <> 85   THEN b.production_qnty ELSE 0 END) 
		-
		sum(CASE WHEN a.entry_form=85   THEN b.production_qnty ELSE 0 END) as total_ex_factory_qnty ,a.total_carton_qnty
		 from pro_ex_factory_mst a, pro_ex_factory_dtls b 
		 where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $po_product_cond 
		 group by a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id,a.total_carton_qnty");
		
		foreach($ex_factory_data as $exRow)
		{
			 
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$color_id_arr[$exRow[csf('color_size_break_down_id')]]]['today_ex_factory_qnty']+=$exRow[csf('today_ex_factory_qnty')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$color_id_arr[$exRow[csf('color_size_break_down_id')]]]['total_ex_factory_qnty']+=$exRow[csf('total_ex_factory_qnty')];		 

		}
		
		ob_start();	
		
		?>
		 
        <div>
        	<table width="2810" cellspacing="0" >
        		<tr class="form_caption" style="border:none;">
        			<td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
        				Daily RMG Production Report
        			</strong></td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="30" align="center" style="border:none; font-size:14px;">
        				<strong>
        					Working Company Name : <? 
        					$comp_names=""; 
        					foreach(explode(",",$cbo_company_name) as $vals) 
        					{
        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
        					}
        					echo $comp_names;
        					 ?>
        				</strong>                                
        			</td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold" >
        				<?
        				if(str_replace("'","",trim($txt_production_date))!="")
        				{
        					echo "Date ".change_date_format($txt_production_date)  ;
        				}
        				?>
        			</td>
        		</tr>
        	</table>
            <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:3210px">Details Part</div>
				<div style="float:left; width:1930px">
					<table width="3210" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
						<thead>
							<tr>
								<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="30" ><p>SL</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer Client</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Style Ref.</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Job No.</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Order No.</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Country</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Country Ship Date</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="120"><p>Garment Item</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Color</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order Qty.</p></th>	
								<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Cutting QC Qty</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Sewing Input</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Sewing Output</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Today Sewing Reject</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Total Sewing Reject</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Sewing WIP</p></th> 
								<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Print Issue</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Print Rcv</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Embl. Issue</p></th> 
								<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Embl. Rcv</p></th> 
								<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Packing &Fin.</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Pac &Fin. WIP</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p> Ex-Factory</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Ex-Fac. WIP</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Status</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>CM Cost (Tk)</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="120"><p>Today total CM Value</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Remarks</p></th>
							</tr>
							<tr>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
								<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
							</tr>  
						</thead>
				  </table>
					<div style="max-height:425px; overflow-y:scroll; width:3230px" id="scroll_body">
						<table cellspacing="0" border="1" class="rpt_table"  width="3210" rules="all" id="table_body" >
							<tbody>
								<?
								// $job_wise_ship_span_arr=array();
								$job_wise_ship_span_arr=array();
								foreach($production_main_array as $buyer_id=>$job_data)
								{
								
									foreach($job_data as $job_id=>$po_data)
									{
										foreach($po_data as $po_id=>$country_data)
										{
											
											foreach($country_data as $country_id=>$shipdate_data)
											{
												foreach($shipdate_data as $shipdate_id=>$item_data)
												{
													foreach($item_data as $item_id=>$color_data)
													{
														foreach($color_data as $color_id=>$rows)
														{
															$job_wise_ship_span[$job_id]++;
														}
													}
												}
											}
										}	
									}

								}
								// echo "<pre>";print_r($job_wise_ship_span);
								
								$k=1;
								$gr_wise_order_qnty=0;$gr_wise_lay_qnty_today=0;$gr_wise_lay_qnty_total=0;$gr_wise_today_cutting=0;$gr_wise_total_cutting=0;$gr_wise_today_sewing_input=0;$gr_wise_total_sewing_input=0;$gr_wise_today_sewing_output=0;$gr_wise_total_sewing_output=0;$gr_wise_today_sewing_rej=0;$gr_wise_total_sewing_rej=0;$gr_wise_today_pac=0;$gr_wise_total_pac=0;$gr_wise_today_ex_fac=0;$gr_wise_total_ex_fac=0;$gr_wise_sewing_wip=0;$gr_wise_poly_wip=0;$gr_wise_pack_wip=0;$gr_wise_exfac_wip=0;
									
								foreach($production_main_array as $buyer_id=>$job_data)
								{
									
									foreach($job_data as $job_id=>$po_data)
									{
										$a=0;	// $costing_per=$costing_per_arr[$job_id];
												// if($costing_per==1) $dzn_qnty=12;
												// else if($costing_per==3) $dzn_qnty=12*2;
												// else if($costing_per==4) $dzn_qnty=12*3;
												// else if($costing_per==5) $dzn_qnty=12*4;
												// else $dzn_qnty=1;
												// $cm_cost=(((($cm_arr[$job_id]*60)/$dzn_qnty)/100)*$exchange_rate_arr[$job_id]);
										foreach($po_data as $po_id=>$country_data)
										{
											foreach($country_data as $country_id=>$shipdate_data)
											{
												foreach($shipdate_data as $shipdate_id=>$item_data)
												{
													foreach($item_data as $item_id=>$color_data)
													{
														foreach($color_data as $color_id=>$rows)
														{
															$today_ex_fac=$ex_factory_arr[$po_id][$item_id][$country_id][$color_id]['today_ex_factory_qnty'];
															$total_ex_fac=$ex_factory_arr[$po_id][$item_id][$country_id][$color_id]['total_ex_factory_qnty'];
														
															$order_qntys=$order_qnty_pcs_arr[$job_id][$po_id][$item_id][$country_id][change_date_format($shipdate_id)][$color_id];

															if(!$order_qntys)
															{
																$order_qntys=$rows["order_quantity"];
															}
															$sewing_wip= ($rows["total_sewing_output"]+$rows["total_sewing_reject_qty"])-$rows["total_sewing_input"];

															$costing_per=$costing_per_arr[$job_id];
															if($costing_per==1) $dzn_qnty=12;
															else if($costing_per==3) $dzn_qnty=12*2;
															else if($costing_per==4) $dzn_qnty=12*3;
															else if($costing_per==5) $dzn_qnty=12*4;
															else $dzn_qnty=1;
															$cm_cost=(((($cm_arr[$job_id]*60)/$dzn_qnty)/100)*$exchange_rate_arr[$job_id]);

															$gr_wise_order_qnty+=$order_qntys; 
															$gr_wise_today_cutting+=$rows["today_cutting"];
															$gr_wise_total_cutting+=$rows["total_cutting"];
															$gr_wise_today_print_issue+=$rows["today_print_issue"];
															$gr_wise_total_print_issue+=$rows["total_print_issue"];
															$gr_wise_today_print_rcv+=$rows["today_print_rcv"];
															$gr_wise_total_print_rcv+=$rows["total_print_rcv"];
															$gr_wise_today_embl_issue+=$rows["today_embl_issue"];
															$gr_wise_total_embl_issue+=$rows["total_embl_issue"];
															$gr_wise_today_embl_rcv+=$rows["today_embl_rcv"];
															$gr_wise_total_embl_rcv+=$rows["total_embl_rcv"];
															$gr_wise_today_sewing_input+=$rows["today_sewing_input"];
															$gr_wise_total_sewing_input+=$rows["total_sewing_input"];
															$gr_wise_today_sewing_output+=$rows["today_sewing_output"];
															$gr_wise_total_sewing_output+=$rows["total_sewing_output"];
															$gr_wise_today_pac+=$rows["today_packing"];
															$gr_wise_total_pac+=$rows["total_packing"];
															$gr_wise_today_ex_fac+=$today_ex_fac;
															$gr_wise_total_ex_fac+=$total_ex_fac;
															$gr_wise_sewing_wip+=$sewing_wip;
															$gr_wise_pack_wip+=$rows["total_packing"]-$rows["total_sewing_output"];
															$gr_wise_exfac_wip+=$total_ex_fac-$order_qntys;
															$gr_wise_today_sewing_rej+=$rows["today_sewing_reject_qty"];
															$gr_wise_total_sewing_rej+=$rows["total_sewing_reject_qty"];
															$cm_cost_val=$cm_cost*$rows["today_sewing_output"];
															$gr_wise_cm_cost =$cm_cost;
															$gr_wise_cm_cost_val +=$cm_cost_val;

															if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
															?>
															<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $k; ?>">
															
																<td lign="left"  width="30"><p><? echo $k;?></p></td>
																<td align="left"   width="115"><p><? echo $buyer_library[$buyer_id]; ?></p></td>
																<td align="left"   width="115"><p><? echo $client_array[$rows["client_id"]]; ?></p></td>
																<td align="left"   width="115"><p><? echo $rows["style_ref_no"];?></p></td>
																<td align="left"   width="80"><p><? echo $rows["job_no_prefix_num"];?></p></td>
																<td align="left"   width="115"><p><? echo $rows["po_number"];?></p></td>
																<td lign="left"    width="80"><p><? echo $country_library[$country_id]; ?></p></td>
																<td align="left"   width="100"><p><? echo change_date_format($shipdate_id); ?></p></td>
																<td align="left"   width="120"><p><? echo $garments_item[$item_id]; ?></p></td>
																<td align="left" width="80"><p><? echo $color_Arr_library[$color_id]; ?></p></td>
																<td align="right"  width="80"><p><? echo $order_qntys;?></p></td>
																<td align="right" width="80"><p><? echo $rows["today_cutting"];?></p></td>
																<td align="right" width="80"><p><? echo $rows["total_cutting"];?></p></td>
																<td align="right" width="80"><p><? echo $rows["today_sewing_input"];?></p></td>
																<td align="right" width="80"><p><? echo $rows["total_sewing_input"];?></p></td>
																<td align="right" width="80"><p><? echo $rows["today_sewing_output"];?></p></td>
																<td align="right" width="80"><p><? echo $rows["total_sewing_output"];?></p></td>
																<td align="right" width="100"><p><? echo $rows["today_sewing_reject_qty"];?></p></td>
																<td align="right" width="100"><p><? echo $rows["total_sewing_reject_qty"];?></p></td>
																<td  align="right"  width="80"><p><? echo $sewing_wip ;?></p></td> 
																<td align="right"  width="80"><p><?=$rows['today_print_issue'];?></p></td>
																<td align="right"  width="80"><p><?=$rows['total_print_issue'];?></p></td>
																<td align="right"  width="80"><p><?=$rows['today_print_rcv'];?></p></td>
																<td align="right"  width="80"><p><?=$rows['total_print_rcv'];?></p></td>
																<td align="right"  width="80"><p><?=$rows['today_embl_issue'];?></p></td>
																<td align="right"  width="80"><p><?=$rows['total_embl_issue'];?></p></td>
																<td align="right"  width="80"><p><?=$rows['today_embl_rcv'];?></p></td>
																<td align="right"  width="80"><p><?=$rows['total_embl_rcv'];?></p></td>
																<td  align="right"  width="80"><p><? echo $rows["today_packing"];?></p></td>
																<td align="right"   width="80"><p><? echo $rows["total_packing"];?></p></td>
																<td  align="right"   width="80"><p><? echo  $packing_wip=  $rows["total_packing"]- $rows['total_sewing_output'];?></p></td>
																<td align="right"   width="80"><p><? echo $today_ex_fac;?></p></td>
																<td  align="right"  width="80"><p><? echo $total_ex_fac ;?></p></td>
																<td  align="right"   width="80"><p><? echo $ex_fac_wip= $total_ex_fac-$order_qntys;?></p></td>
																<td  align="right"   width="80"><p><? echo  $shipment_status[$rows['shiping_status']];?></p></td>
																<?
																	if($a==0) 
																	{   
																		$costing_per=$costing_per_arr[$job_id];
																		if($costing_per==1) $dzn_qnty=12;
																		else if($costing_per==3) $dzn_qnty=12*2;
																		else if($costing_per==4) $dzn_qnty=12*3;
																		else if($costing_per==5) $dzn_qnty=12*4;
																		else $dzn_qnty=1;
																		
																		?>
																		<td rowspan="<? echo $job_wise_ship_span[$job_id] ; ?>" align="right" width="80"><p><?= number_format($cm_cost,2) ?></p></td>
																	<? $a++ ;
																	} 
																	
																?>	
																<td  align="right"   width="120"><p><?=number_format($cm_cost_val,2);?></p></td>		
																<td  align="center"   width="80">
																<p>
																	<a href="##" onClick="openmypage_remarks(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $country_id;?>,<? echo $color_id;?>, 'remarks_popup');" >Remarks</a>
																</p></td>
															</tr>	
															<?
															
															$k++;
														}
													}
												}
											}
										}										
									}							 		
								} 
								?>
							</tbody>
							<tfoot>
								<tr >
									<th align="center" width="30">&nbsp;</th>
									<th align="center" width="115">&nbsp;</th>
									<th align="center" width="115">&nbsp;</th>
									<th align="center" width="115">&nbsp;</th>
									<th align="center" width="80">&nbsp;</th>
									<th align="center" width="115">&nbsp;</th>
									<th align="center"  width="80">&nbsp;</th>
									<th align="center"  width="100">&nbsp;</th>
									<th align="center"  width="120"><strong>Grand Total</strong></th>
									<th align="center"  width="80">&nbsp;</p></td>
									<th width="80"   align="center" ><p><? echo $gr_wise_order_qnty;?></p></th>
									<th width="80"  align="center"><p><? echo $gr_wise_today_cutting; ?></p></th>
									<th width="80"   align="center"><p><? echo $gr_wise_total_cutting; ?></p></th>
									<th  width="80"  align="center"><p><? echo $gr_wise_today_sewing_input; ?></p></th>
									<th width="80"   align="center"><p><? echo $gr_wise_total_sewing_input; ?></p></th>
									<th width="80"   align="center"><p><? echo $gr_wise_today_sewing_output; ?></p></th>
									<th width="80"  align="center"><p><? echo $gr_wise_total_sewing_output; ?></p></th>
									<th  width="100" align="center"><p><? echo $gr_wise_today_sewing_rej; ?></p></th>
									<th width="100"  align="center"><p><? echo $gr_wise_total_sewing_rej; ?></p></th>
									<th width="80"   align="center"><p><? echo $gr_wise_sewing_wip; ?></p></th>
									<th  width="80" " align="center"><p><? echo$gr_wise_today_print_issue; ?></p></th>
									<th width="80"  align="center"><p><? echo $gr_wise_total_print_issue; ?></p></th>
									<th  width="80"  align="center"><p><? echo $gr_wise_today_print_rcv; ?></p></th>
									<th width="80"  align="center"><p><? echo $gr_wise_total_print_rcv; ?></p></th>
									<th  width="80" align="center"><p><? echo $gr_wise_today_embl_issue; ?></p></th>
									<th width="80"  align="center"><p><? echo $gr_wise_total_embl_issue; ?></p></th>
									<th  width="80" " align="center"><p><? echo $gr_wise_today_embl_rcv; ?></p></th>
									<th width="80"  align="center"><p><? echo $gr_wise_total_embl_rcv; ?></p></th>
									<th width="80"   align="center"><p><? echo $gr_wise_today_pac; ?></p></th>
									<th  width="80"  align="center"><p><? echo $gr_wise_total_pac; ?></p></th>
									<th width="80"   align="center"><p><? echo $gr_wise_pack_wip; ?></p></th>
									<th width="80"   align="center"><p><? echo $gr_wise_today_ex_fac; ?></p></th>
									<th width="80"   align="center"><p><? echo $gr_wise_total_ex_fac; ?></p></th>
									<th width="80"  align="center"><p><? echo $gr_wise_exfac_wip; ?></p></th>
									<th width="80"  align="center"></th>
									<th width="80"  align="center"><?=number_format($gr_wise_cm_cost,2);?></th>
									<th width="120" align="center"><?=number_format($gr_wise_cm_cost_val,2);?></th>
									<th width="80"  align="center"></th>
								</tr>
							</tfoot>
						</table>			
					</div>
			    </div>	 
	     </div> 
        <?
	}


	else if($type==4)//show5   GBL_TEMP_ENGINE //REF_FROM 4   
	{
		 //THIS BUTTON COPIED FROM "production\reports\requires\date_wise_prod_without_cm_report_controller.php" 
		//  $ReportType==1 show button   
		
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
		$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
		$supplier_arr=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name");
		$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
		$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
		$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 

		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  ); 


		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number'); 	
		$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$buyer_brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
		$buyer_season_arr=return_library_array( "select id, season_name from  lib_buyer_season",'id','season_name');

		$cbo_floor=str_replace("'","",$cbo_floor);
		//echo $cbo_floor;  
		$cbo_com_fac_name = $cbo_company_name;
		$cbo_buyer_name   = "'$cbo_buyer_name'";
		$location	 	  = $cbo_location;
		$txt_date_from 	  = "'$txt_production_date'"; 
		$txt_date_to	  = "'$txt_production_date'";  

		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
		if($cbo_floor==0) $floor_name="";else $floor_name=" and floor_id in($cbo_floor)";
		//echo $floor_name;die;
		if($cbo_floor==0) $floor_id="";else $floor_id=" and floor_id in($cbo_floor)";
		if ($location==0) $location_cond=""; else $location_cond=" and location in($location) "; 
		
		if($txt_production_date=="")$txt_date="";else $txt_date=" and production_date ='$txt_production_date'";
		
		$job_id_cond =  ($hidden_job_id) ? " and a.id =$hidden_job_id" : '';
		// echo $job_id_cond; die;
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		//cbo_garments_nature
		$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);  
		if ($location)  $location_cond=" and location in($location) "; else $location_cond=""; 
		if($cbo_com_fac_name=="") $working_factory_cond=""; else $working_factory_cond=" and serving_company in($cbo_com_fac_name)";
		// echo $working_factory_cond;

		$variable_setting = sql_select("select EX_FACTORY from variable_settings_production where company_name=$cbo_com_fac_name and variable_list=1 and status_active=1");
 	
		$exfactory_level = $variable_setting[0]["EX_FACTORY"];

        $job_arr=array();
        $job_arr2=array(); 
        /*==========================================================================================/
        /										main query											/
        /========================================================================================= */
		$job_sql="SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $buyer_name $job_id_cond $txt_date";

		// echo $job_sql; die;
		 
		
        $job_sql_res=sql_select($job_sql); 
        
        $tot_rows=0; 
        $poIds='';
        $poIds_array = array();
        foreach($job_sql_res as $row)
        {
            $tot_rows++;
            $poIds.=$row[csf("id")].",";
            $poIds_array[$row[csf("id")]]=$row[csf("id")];
            $job_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
            $job_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $job_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
            $job_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
            $job_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
            $job_arr[$row[csf("id")]]['ref']=$row[csf("grouping")];
            $job_arr2[$row[csf("id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
        }
        unset($job_sql_res); 
		
        $txt_date_ex = str_replace("production_date", "ex_factory_date", $txt_date);
        $job_sql_ex="SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_id_cond $buyer_name $txt_date_ex";
		
		// echo $job_sql_ex; die;
		
        $job_sql_res_ex=sql_select($job_sql_ex); 
        
        foreach($job_sql_res_ex as $row)
        {
        	$poIds_array[$row[csf("id")]]=$row[csf("id")];
            $job_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
            $job_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $job_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
            $job_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
            $job_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
            $job_arr[$row[csf("id")]]['ref']=$row[csf("grouping")];
            $job_arr2[$row[csf("id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
        }
        unset($job_sql_res_ex); 
		// pre($poIds_array); die;
        $poIds_cond="";
        
       	$poIds=implode(",", $poIds_array);
         if($db_type==2 && count($poIds_array)>=1000)
        {
            $poIds_cond=" and (";
            $poIdsArr=array_chunk($poIds_array,999);
            foreach($poIdsArr as $ids)
            {
                $ids=implode(",",$ids);
                $poIds_cond.=" po_break_down_id in ($ids) or ";
            }
            $poIds_cond=chop($poIds_cond,'or ');
            $poIds_cond.=")";
        }
        else
        {
            $poIds_cond=" and po_break_down_id in ($poIds)";
        }
		
		// ============================== data store to gbl table ==================================
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=4 and ENTRY_FORM=43");
		oci_commit($con);
				
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 43, 4, $poIds_array, $empty_arr);//PO ID
		// ============================== end data store to gbl table ==================================
      
        
        $buyer_fullQty_arr=array();
        $prod_date_qty_arr=array();
        $prod_dlfl_qty_arr=array();
		$all_data_arr=array();
		$all_data_arrr=array();
        /*==========================================================================================/
        /										production data										/
        /========================================================================================= */ 
        $sql_dtls="SELECT a.location, a.company_id, a.serving_company, a.floor_id, a.sewing_line, a.po_break_down_id,a.re_production_qty, a.production_date, a.item_number_id, a.production_type, a.production_source, a.embel_name, (a.prod_reso_allo) as prod_reso_allo, (b.production_qnty) as production_quantity,  (b.reject_qty) as reject_qnty, a.carton_qty as carton_qty 
        from pro_garments_production_mst a ,pro_garments_production_dtls b ,wo_po_color_size_breakdown c,GBL_TEMP_ENGINE tmp
        where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=a.po_break_down_id and c.po_break_down_id=tmp.ref_val and tmp.entry_form=43 and tmp.ref_from=4 and tmp.user_id = $user_id  and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.is_deleted=0 and a.status_active=1  $working_factory_cond $txt_date $floor_name $location_cond order by a.production_date ASC";
		
		// echo $sql_dtls; die;
		
		$sql_dtls_res=sql_select($sql_dtls);

		$w_company_cond='';
		if($cbo_com_fac_name)
		{
			$w_company_cond=" and a.company_id=$cbo_com_fac_name";
		}
		
		$buyer_cond='';
		if($cbo_buyer_name)
		{
			$buyer_cond=" and d.buyer_name=$cbo_buyer_name";
		}
		
		$location_cond='';
		if($location)
		{
			$location_cond=" and a.fini_location_id=$location";
		}
		$floor_cond='';
		if($cbo_floor)
		{
			$floor_cond=" and a.floor_id=$cbo_floor";
		}
		

		$txt_rcv_date = str_replace("production_date", "a.receive_date", $txt_date);
        $fin_rcv_sql=" SELECT c.po_break_down_id, (c.fin_receive_qnty) as qnty,
					         a.receive_date,d.job_no_prefix_num as job_no,d.buyer_name,b.shipment_date,b.file_no,
					         b.grouping,d.style_ref_no,d.gmts_item_id,b.po_number,a.company_id,c.item_id
					    FROM gmt_finishing_receive_mst a, gmt_finishing_receive_dtls c,wo_po_break_down b,wo_po_details_master d
					   WHERE     a.id = c.mst_id
					         and b.id=c.po_break_down_id
					         and d.id=b.job_id
					         and B.STATUS_ACTIVE=1 
					         and b.is_deleted=0
					         and d.is_deleted=0
					         AND a.status_active = 1
					         AND a.is_deleted = 0
					         AND c.status_active = 1
					         AND c.is_deleted = 0
					        $w_company_cond 
					        $buyer_cond
					        $txt_rcv_date
					        $location_cond
					        $floor_cond
					         ";
		// echo $fin_rcv_sql; die;
		$fin_rcv_res= sql_select( $fin_rcv_sql);
        $order_wise_fin_rec_qnty=array();
        foreach ($fin_rcv_res as $row) 
        {
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['qnty']+=$row[csf('qnty')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['job_no']=$row[csf('job_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['buyer_name']=$row[csf('buyer_name')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['shipment_date']=$row[csf('shipment_date')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['file_no']=$row[csf('file_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['grouping']=$row[csf('grouping')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['style_ref_no']=$row[csf('style_ref_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['po_number']=$row[csf('po_number')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['company_id']=$row[csf('company_id')];
        	



        }



		
		// var_dump($sql_dtls_res);
        // echo $sql_dtls;  die;//$poIds_cond
		if( count($sql_dtls_res) > 0)
		{		
         
			$po_wise_unit_price_sql="SELECT a.buyer_name,b.id,b.unit_price from wo_po_details_master a, wo_po_break_down b,GBL_TEMP_ENGINE tmp where a.id=b.job_id and b.id=tmp.ref_val and tmp.entry_form=43 and tmp.ref_from=4 and tmp.user_id = $user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			// echo $po_wise_unit_price_sql; die;
			foreach( sql_select($po_wise_unit_price_sql) as $key=>$val)
			{
				$po_wise_unit_price[$val[csf("id")]]=$val[csf("unit_price")];
				$buyer_wise_unit_price[$val[csf("buyer_name")]]+=$val[csf("unit_price")];
			}

		}
		/*==========================================================================================/
        /										shipment data										/
        /========================================================================================= */
        $txt_date2=str_replace("production_date", "e.ex_factory_date", $txt_date);
		$delivery_company_cond = $cbo_com_fac_name ? " and d.delivery_company_id in($cbo_com_fac_name) " : "";

        if($exfactory_level==1) // gross level
		{
			$sql_ex="SELECT a.buyer_name,b.unit_price,d.delivery_location_id as location, d.company_id, d.delivery_company_id, d.delivery_floor_id as floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id, (e.ex_factory_qnty) as ex_factory_qnty
        	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, pro_ex_factory_delivery_mst d ,pro_ex_factory_mst e
        	where d.id=e.delivery_mst_id and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and e.status_active=1 and e.is_deleted=0 and  d.is_deleted=0 and d.status_active=1 $job_id_cond $buyer_name $txt_date2 $delivery_company_cond
        	order by e.ex_factory_date ASC";
		}
		else // color or color and size level
		{
        	$sql_ex="SELECT a.buyer_name,b.unit_price, d.delivery_location_id as location, d.company_id, d.delivery_company_id, d.delivery_floor_id as floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id, (f.production_qnty) as ex_factory_qnty
        	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, pro_ex_factory_delivery_mst d ,pro_ex_factory_mst e ,pro_ex_factory_dtls f
        	where d.id=e.delivery_mst_id and e.id=f.mst_id and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and f.color_size_break_down_id=c.id and f.status_active in(1) and f.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and  d.is_deleted=0 and d.status_active=1 $job_id_cond $buyer_name $txt_date2  $delivery_company_cond	order by e.ex_factory_date ASC";
        }
        // echo $sql_ex;die;
        $ex_res=sql_select($sql_ex);

		/*==========================================================================================/
        /										carton qty											/
        /========================================================================================= */
		// GETTING CARTON QNTY
		$sql_carton="SELECT a.location,a.floor_id,a.sewing_line, a.po_break_down_id, a.production_date, a.item_number_id,a.serving_company,sum(a.carton_qty) as carton_qty 
    	from pro_garments_production_mst a,GBL_TEMP_ENGINE tmp
    	where a.po_break_down_id=tmp.ref_val and tmp.entry_form=43 and tmp.ref_from=4 and tmp.user_id = $user_id and a.is_deleted=0 and a.status_active=1  $working_factory_cond $txt_date $floor_name $location_cond 
    	group by a.location,a.floor_id,a.sewing_line,a.po_break_down_id, a.production_date, a.item_number_id,a.serving_company order by a.production_date ASC";
    	// echo $sql_carton;die;
    	$sql_carton_res = sql_select($sql_carton);
    	
		foreach ($sql_carton_res as $row) 
		{           							
			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
		}
		
		// ================== for production data ======================
		$buyer_po_date_wise_gmts_data_arr = array();
		$check_fin_rcv = array();
        		
		foreach($sql_dtls_res as $row)
		{
			// var_dump($row);
			$buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
			$buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
			$job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
			$po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
			$style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
			$ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
			$file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];
			
			//Buyer Wise Summary array start
			$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
			$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
			$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
			$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
			$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];
 

			$buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][$row[csf("production_type")]] += $row[csf("production_quantity")];

			//Details array start
			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
			
			//for serving company
			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
			// $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];


			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
			$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")]; 
			if($row[csf("production_source")]==0) $row[csf("production_source")]=1;
			$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]].=$row[csf("item_number_id")].'**'.$buyer_name_dat.'**'.$job_no.'**'.$po_no.'**'.$style_ref.'**'.$ref_no.'**'.$file_no.'**'.$row[csf("company_id")].'**'.$row[csf("production_source")].'**'.$row[csf("serving_company")].'__'; 
		}
        
        
        // echo "<br>"; print_r($prod_date_qty_arr); die; 
        unset($sql_dtls_res);
        // unset($job_arr);
        
        // ============================ for shipment data ========================
        $ex_fac_arr_buyerwise = array();
        $prod_date_qty_arr_ex = array();
        $prod_date_qty_arr_ex2 = array();
        $po_id_arr = array();
       	
		foreach($ex_res as $row)
		{
			
			$buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
			$buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
			$job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
			$po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
			$style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
			$ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
			$file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

			$ex_fac_arr_po_datewise[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]]+=$row[csf("ex_factory_qnty")];
			$ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];

			$buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][0] += $row[csf("ex_factory_qnty")];
			
			//Buyer Wise Summary array start 
			$buyer_fullQty_arr[$buyer_name_dat][0]['0']['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

			 
			
			//for serving company
			$prod_date_qty_arr_ex[$row[csf("po_break_down_id")]][$row[csf("delivery_company_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

			$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]].=$row[csf("item_number_id")].'**'.$buyer_name_dat.'**'.$job_no.'**'.$po_no.'**'.$style_ref.'**'.$ref_no.'**'.$file_no.'**'.$row[csf("company_id")].'**1**'.$row[csf("delivery_company_id")].'__';//.'**'.$row[csf("floor_id")]
			array_push($po_id_arr, $row[csf("po_break_down_id")]);
		}
        

        // echo "<pre>";print_r($all_data_arr);die();        
        unset($ex_res);
        unset($job_arr);
        $b=1; //date_wise Summary
        	
        //$rcv_data_need_to_show=array();
        foreach ($order_wise_fin_rec_qnty as $po_id=> $po_wise_data) 
        {

        	foreach ($po_wise_data as $receive_date => $rcv_date_wise_data) 
        	{
        		foreach ($rcv_date_wise_data as $item_id => $item_data) 
        		{
        			$buyer_fullQty_arr[$item_data['buyer_name']][0]['0']['fin_rcv_qnty']+=$item_data['qnty'];
        		}
        	}
        }



		

        foreach ($buyer_po_date_wise_gmts_data_arr as $b_key => $b_value) 
        {
        	foreach ($b_value as $po_key => $po_value) 
        	{
        		$sewing_output_total = $po_value[5];
        		$ex_fact = $po_value[0];

                $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_key];

                $ex_fact_bal= $sewing_output_total-$ex_fact;
                $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_key];
                $ex_fac_arr_buyerwise[$b_key] += $ex_fact;
                $ex_fac_fob_arr_buyerwise[$b_key]+=$ex_fact_fob;
                $ex_fac_bal_arr_buyerwise[$b_key]+=$ex_fact_bal;
                $ex_fac_bal_fob_arr_buyerwise[$b_key]+=$ex_fact_bal_fob;
        	}
        }
         
		// print_r($ex_fac_arr_buyerwise);
		// ============================ delete GBL_TEMP_ENGINE data ====================================
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=4 and ENTRY_FORM=43");
		oci_commit($con);
		disconnect($con);
		$s_width = 1950;
		$div_width="3140";
		ob_start();	
		?>
		<style>
			.break_all {
				word-wrap: break-word;
				word-break: break-all;
			}
		</style>
       <div>
			<div style="width:<? echo $div_width; ?>px">
				<table width="<? echo $div_width; ?>" cellspacing="0">
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? //echo $report_name; ?></td>
					 </tr>
					<tr style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">
							Company Name:
							<? 
								echo $company_library[str_replace("'","",$cbo_com_fac_name)];  
							?>                                
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? echo "From $fromDate To $toDate" ;?>
						</td>
					</tr>
				</table>
				<!-- ==========================================================================================/
		        /										summary part										   /
		        /=========================================================================================== -->
				<table width="<?= $s_width; ?>" cellspacing="0" border="1" rules="all" align="left">
					<tr>
						<td width="1920" align="left" valign="top">
	                    <div style="width:300px; float:left; background-color:#FCF"><strong>Production-Regular Order Summary</strong></div>
	                    <br/>
						<div style="clear:both;">
							<table width="<?= $s_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
								<thead>
									<tr>
										<th class="break_all" width="30">Sl.</th>    
										<th class="break_all" width="80">Buyer Name</th>
										<th class="break_all" width="80">Cut Quantity</th>
										<th class="break_all" width="80">Sent to Print</th>
										<th class="break_all" width="80">Received from Print</th>
										<th class="break_all" width="80">Sent to Embroidery </th>
										<th class="break_all" width="80">Received from Embroidery</th>
										<th class="break_all" width="80">Sent to Wash</th>
										<th class="break_all" width="80">Rev Wash</th>
										<th class="break_all" width="80">Sent to Sp. Works</th>
										<th class="break_all" width="80">Rev Sp. Works</th>
										
										<th class="break_all" width="80">Sewing Input</th>
										<th class="break_all" width="80">Sew Input (Outbound)</th>
										<th class="break_all" width="80">Sewing Output</th>
										<th class="break_all" width="80">Sew Output (Outbound)</th>
										<th class="break_all" width="80">Finishing Received</th>
										<th class="break_all" width="80">Total Iron Production</th>
										<th class="break_all" width="80">Total Re-Iron</th>
										<th class="break_all" width="80">Total Poly (Inhouse)</th>
										<th class="break_all" width="80">Total Poly (Outbond)</th>
										<th class="break_all" width="80">Total Packing/ Finishing</th>
										<th class="break_all" width="80">EXF Quantity</th>
										<th class="break_all" width="80">EXF Value</th>
										<th class="break_all" width="80">Ex-Fac. Bal. Qty</th>
										<th class="break_all" width="80">Ex- Fac. Bal. FOB Value</th>
									</tr>
								</thead>
							</table>
                            <div style="overflow-y:scroll; max-height:225px; width:<?= $s_width+20; ?>px" >
                                <table cellspacing="0" border="1" class="rpt_table"  width="<?= $s_width; ?>" rules="all" id="" >
                                <?
								// echo "<pre>";
								// print_r($buyer_fullQty_arr);
								$tot_fin_rcv_qnty=0;
								$tot_buyer_fin_rcv_qnty=0;
                                foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
                                {
                                    if($buyer_id!="")
                                    {
                                        if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                        $cutting_qty=$printing_qty=$printreceived_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewOut_inQty=$sewOut_outQty=$iron_qty=$reIron_qty=$finish_qty=$polyIn_inQty=$polyOut_outQty=0;
                                        $cutting_qty=$buyer_data['1']['0']['pQty'];
                                        $printing_qty=$buyer_data['2']['1']['embQty'];
                                        $printreceived_qty=$buyer_data['3']['1']['embQty'];
                                        $emb_qty=$buyer_data['2']['2']['embQty'];
                                        $embRec_qty=$buyer_data['3']['2']['embQty'];
                                        $wash_qty=$buyer_data['2']['3']['embQty'];
                                        $washRec_qty=$buyer_data['3']['3']['embQty'];
                                        $special_qty=$buyer_data['2']['4']['embQty'];
                                        $specialRec_qty=$buyer_data['3']['4']['embQty'];
                                        $sewIn_inQty=$buyer_data['4']['0']['pQty'];
                                        $sewIn_outQty=$buyer_data['4']['3']['sQty'];
                                        $sewOut_inQty=$buyer_data['5']['0']['pQty'];
                                        $sewOut_outQty=$buyer_data['5']['3']['sQty'];
                                        $polyIn_inQty=$buyer_data['11']['1']['sQty'];
                                        $polyOut_outQty=$buyer_data['11']['3']['sQty'];
                                        $iron_qty=$buyer_data['7']['0']['pQty'];
                                        $reIron_qty=$buyer_data['7']['0']['reQty'];
                                        $finish_qty=$buyer_data['8']['0']['pQty'];
                                        // $ex_fact_buy=$ex_fac_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy=$buyer_data[0]['0']['ex_factory_qnty'];
                                        $fin_rcv_qnty=$buyer_data[0]['0']['fin_rcv_qnty'];
                                        $ex_fact_buy_fob=$ex_fac_fob_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy_bal=$ex_fac_bal_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy_bal_fob=$ex_fac_bal_fob_arr_buyerwise[$buyer_id];
            
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
                                            <td class="break_all" width="30"><? echo $b;?></td>
                                            <td class="break_all" width="80"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($cutting_qty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($printing_qty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($printreceived_qty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($emb_qty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($embRec_qty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($wash_qty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($washRec_qty);  ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($special_qty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($specialRec_qty);  ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($sewIn_inQty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($sewIn_outQty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($sewOut_inQty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($sewOut_outQty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($fin_rcv_qnty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($iron_qty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($reIron_qty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($polyIn_inQty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($polyOut_outQty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($finish_qty); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($ex_fact_buy); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($ex_fact_buy_fob); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($ex_fact_buy_bal); ?></td>
                                            <td class="break_all" width="80" align="right"><? echo number_format($ex_fact_buy_bal_fob); ?></td>
                                        </tr>	
                                        <?
                                        $sumCutting_qty+=$cutting_qty;
                                        $sumPrinting_qty+=$printing_qty;
                                        $sumPrintreceived_qty+=$printreceived_qty;
                                        $sumEmb_qty+=$emb_qty;
                                        $sumEmbRec_qty+=$embRec_qty;
                                        $sumWash_qty+=$wash_qty;
                                        $sumWashRec_qty+=$washRec_qty;
                                        $sumSpecial_qty+=$special_qty;
                                        $sumSpecialRec_qty+=$specialRec_qty;
                                        $sumSewIn_inQty+=$sewIn_inQty;
                                        $sumSewIn_outQty+=$sewIn_outQty;
                                        $sumSewOut_inQty+=$sewOut_inQty;
                                        $sumSewOut_outQty+=$sewOut_outQty;
                                        $sumIron_qty+=$iron_qty;
                                        $sumReIron_qty+=$reIron_qty;
                                        $sumFinish_qty+=$finish_qty;
                                        $sumPolyIn_inQty+=$polyIn_inQty;
                                        $sumPolyOut_outQty+=$polyOut_outQty;
             
                                         
                                        $sum_exfact+=$ex_fact_buy;
                                        $sum_exfact_fob+=$ex_fact_buy_fob;
                                        $sum_exfact_bal+=$ex_fact_buy_bal;
                                        $sum_exfact_bal_fob+=$ex_fact_buy_bal_fob ;
                                        $tot_fin_rcv_qnty+=$fin_rcv_qnty;
                                        $tot_buyer_fin_rcv_qnty+=$fin_rcv_qnty;
                                        $b++;
                                    }
                                }
                                ?>
                                </table>
                            </div>
                            	<table border="1" class="tbl_bottom"  width="<?= $s_width; ?>" rules="all" id="" >
                                    <tr> 
                                    	<td class="break_all" width="30">&nbsp;</td> 
                                        <td class="break_all" width="80" lign="right">Total</td> 
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumCutting_qty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumPrinting_qty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumPrintreceived_qty); ?></td> 
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumEmb_qty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumEmbRec_qty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumWash_qty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumWashRec_qty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumSpecial_qty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumSpecialRec_qty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumSewIn_inQty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumSewIn_outQty); ?></td>  
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumSewOut_inQty); ?></td>  

                                        <td class="break_all" width="80" align="right"><? echo number_format($sumSewOut_outQty); ?></td> 
                                        <td class="break_all" width="80" align="right"><? echo number_format($tot_buyer_fin_rcv_qnty); ?></td> 

                                        <td class="break_all" width="80" align="right"><? echo number_format($sumIron_qty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumReIron_qty); ?></td> 
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumPolyIn_inQty); ?></td> 
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumPolyOut_outQty); ?></td> 
                                        <td class="break_all" width="80" align="right"><? echo number_format($sumFinish_qty); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sum_exfact); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sum_exfact_fob); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sum_exfact_bal); ?></td>
                                        <td class="break_all" width="80" align="right"><? echo number_format($sum_exfact_bal_fob); ?></td>
                                    </tr>
                                </table>
	                 	</div>
						</td>
					
						<td width="550" align="left" valign="top"><div align="left" style="width:350px; background-color:#FCF"><strong>Production-Subcontract Order(Inbound)Summary </strong></div>
						<div style="float:left; width:550px">
							<table width="550" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
								<thead>
									<tr>
										<th width="30">Sl.</th>    
										<th width="120">Buyer</th>
										<th width="80">Total Cut Qty</th>
										<th width="80">Total Sew Input</th>
										<th width="80">Total Sew Qty</th>
										<th width="80">Total Iron Qty</th>
										<th>Total Gmt. Fin. Qty</th>
									</tr>
								</thead>
							</table>
							<div style="max-height:425px; width:550px" >
							<table cellspacing="0" border="1" class="rpt_table"  width="550" rules="all" id="" >
							<?  
							if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
							if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in($location) "; 
			
							$total_po_quantity=0;$total_po_value=0;$total_cut_subcon=0;$total_sew_out_subcon=0;$total_ex_factory=0;
							$i=1;
							
							$ex_factory_sql="SELECT a.party_id, sum(c.order_quantity) as order_quantity 
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where a.id=c.MST_ID and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to  $subcon_work_comp $sub_floor_name $sub_location_cond group by a.party_id";
							
							//echo  $exfactory_sql;
							$ex_factory_sql_result=sql_select($ex_factory_sql);
							$ex_factory_arr=array(); 
							foreach($ex_factory_sql_result as $resRow)
							{
								$ex_factory_arr[$resRow[csf("party_id")]] = $resRow[csf("order_quantity")];
							}
							//var_dump($exfactory_arr);die;
							//print_r($ex_factory_arr);die;
							
							//@@@@@@@@@@@@@@@@@@@@@
							$sub_cut_sew_array=array();
							
							if($db_type==0)
							{
								$production_mst_sql= sql_select("SELECT  a.party_id,
								sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
								sum(CASE WHEN production_type ='7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,
								sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
								sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
								sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty 
								from subcon_ord_mst a, subcon_ord_dtls c, subcon_gmts_prod_dtls b
			
								where a.id=c.MST_ID and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub  $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id");
							}
							else
							{
								$production_mst_sql=sql_select("SELECT  a.party_id,
								sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
								sum(CASE WHEN production_type ='7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,

								sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
								sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
								sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty 
								from subcon_ord_mst a, subcon_ord_dtls c, subcon_gmts_prod_dtls b
								where a.id=c.MST_ID and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id");

							}
							foreach($production_mst_sql as $sql_result)
							{
								$sub_cut_sew_array[$sql_result[csf("party_id")]]['1']=$sql_result[csf("cutting_qnty")];
								$sub_cut_sew_array[$sql_result[csf("party_id")]]['7']=$sql_result[csf("sewing_input_qnty")];
								$sub_cut_sew_array[$sql_result[csf("party_id")]]['2']=$sql_result[csf("sewingout_qnty")];
								$sub_cut_sew_array[$sql_result[csf("party_id")]]['3']=$sql_result[csf("ironout_qnty")];
								$sub_cut_sew_array[$sql_result[csf("party_id")]]['4']=$sql_result[csf("gmts_fin_qnty")];
							}
							//var_dump($cutting_array);
							//@@@@@@@@@@@@@@@@@@@@@
							if($db_type==0)
							{
								$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price       
								from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c 
								where c.job_no_mst=a.subcon_job and c.id=b.order_id and b.production_type in (1,2,7) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name  group by a.party_id order by a.party_id ASC";
							}
							else
							{
								$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price       
								from subcon_ord_mst a, subcon_ord_dtls c , subcon_gmts_prod_dtls b
								where a.id=c.mst_id and c.id=b.order_id and b.production_type in (1,2,7) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id order by a.party_id ASC";
							}
							//echo $production_date_sql;//die;
							$pro_sql_result=sql_select($production_date_sql);	
							foreach($pro_sql_result as $pro_date_sql_row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
									<td width="30"><? echo $i;?></td>
									<td width="120"><? echo $buyer_short_library[$pro_date_sql_row[csf("party_id")]]; ?></td>
									<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1']); ?></td>
									<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['7']); ?></td>
									<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2']); ?></td>
									<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3']); ?></td>
									<td align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4']); ?></td>
								</tr>	
								<?		
								$total_cut_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1'];
								$total_input_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['7'];
								$total_sew_out_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2'];
								$total_iron_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3'];
								$total_gmts_fin_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4'];
								$i++;
							}//end foreach 1st
							//$chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew Out ;".$total_sew_out."\n"."Ex-Fact;".$total_ex_factory."\n";
							?>
							</table>
							<table border="1" class="tbl_bottom"  width="550" rules="all" id="" >
								<tr> 
									<td width="30">&nbsp;</td> 
									<td width="120" align="right">Total</td> 
									<td width="80" id="tot_cutting"><? echo number_format($total_cut_subcon); ?></td>
									<td width="80" id="tot_input"><? echo number_format($total_input_subcon); ?></td>
									<td width="80" id="tot_sew_out"><? echo number_format($total_sew_out_subcon); ?></td>
									<td width="80" id="tot_iron_out"><? echo number_format($total_iron_subcon); ?></td> 
									<td id="tot_gmt_fin_out"><? echo number_format($total_gmts_fin_subcon); ?></td>   
								</tr>
							</table>
							<br />
								<div style="background-color:#FF9; width:400px; font-size:16px"><strong>Total Cutting: <? echo number_format($all_production_cutt=$sumCutting_qty+$total_cut_subcon,0); ?> (Pcs)&nbsp;&nbsp; & Total Sewing: <? echo number_format($all_production_sewing=$sumSewOut_inQty+$total_sew_out_subcon,0); ?> (Pcs)</strong></div><br />
								<div style="background-color:#FF9; width:400px; font-size:16px"><strong>Total Iron: <? echo number_format($all_production_iron=$sumIron_qty+$total_iron_subcon,0); ?> (Pcs)&nbsp;&nbsp; & Total Gmts. Fin.: <? echo number_format($all_production_gmts_fin=$sumFinish_qty+$total_gmts_fin_subcon,0); ?> (Pcs)</strong></div>
								</div>
							</div>
						</td>
					
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
				</table> 
	         
			</div>
			<div>&nbsp;</div>
			<br />
			<!-- ==========================================================================================/
	        /										Details part										   /
	        /=========================================================================================== -->
			
			<h5 style="width:600px; background-color:#FCF; float:left;"><strong>Production-Regular Order</strong></h5>
			<table width="3915" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
				<thead>
				<tr>
					<th width="30" style="word-wrap: break-word;word-break: break-all;">Sl.</th>    
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Working Factory</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Job No</th>
					<th width="130" style="word-wrap: break-word;word-break: break-all;">Order Number</th>
					<th width="130" style="word-wrap: break-word;word-break: break-all;">Ship Date</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Buyer Name</th>
					<th width="130" style="word-wrap: break-word;word-break: break-all;">Style Name</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">File No</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Internal Ref</th>
					<th width="130" style="word-wrap: break-word;word-break: break-all;">Item Name</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Production Date</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Cutting</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to prnt</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev prn/Emb</th>
					
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Emb</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev Emb</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Wash</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev Wash</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Sp. Works</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev Sp. Works</th>
					
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing In (Inhouse)</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing In (Out-bound)</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Sewing Input</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Out (Inhouse)</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Out (Out-bound)</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Sewing Out</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Finishing<br>Rcv</th>
					
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Qty (Inhouse)</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Qty (Out-bound)</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Iron Qty</th>
					
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Re-Iron Qty </th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Poly Qty (Inhouse)</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Poly Qty (Out-bound)</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Poly Qty</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Finish Qty (Inhouse)</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Finish Qty (Out-bound)</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Finish Qty</th>
					
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Today Carton</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Prod/Dzn</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Reject Qty</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac. Qty</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac. FOB Value</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac Bal. Qty</th>
					<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex- Fac. Bal. FOB Value</th>
					<th style="word-wrap: break-word;word-break: break-all;">Remarks</th>
					</tr>
					</thead>
			</table>
			<div style="width:3933px; overflow-y: scroll; max-height:400px;" id="scroll_body" >
				<table cellspacing="0" border="1" class="rpt_table"  width="3915" rules="all" id="table_body" >  
					<?
					// var_dump($all_data_arr);
					$i=1;
					$fin_rcv_total=0;
					$check_fin_rcv=array();
					foreach($all_data_arr as $po_id=>$po_data)
					// var_dump($all_data_arr);
					// var_dump($po_data);
					{
						foreach($po_data as $prod_date=>$prod_date_data)
						{
							$ex_itemdata='';
							$ex_itemdata=array_filter(array_unique(explode('__',$prod_date_data)));
							foreach($ex_itemdata as $data_all)
							{
								$item_id=''; $buyer_name=''; $job_no=''; $po_no=''; $style_ref=''; $ref_no=''; $file_no=''; $company_id='';
									$ex_data=array_filter(explode('**',$data_all));
									// print_r($ex_data);

								if($ex_data[1] !="")
								{	
									$item_id=$ex_data[0];
									$buyer_name=$ex_data[1];
									$job_no=$ex_data[2];
									$po_no=$ex_data[3];
									$style_ref=$ex_data[4];
									$ref_no=$ex_data[5];
									$file_no=$ex_data[6];
									$company_id=$ex_data[7];
									//$floor_id=$ex_data[10];
									$serving_comp_id=$ex_data[9];
									$serving_company='';
									//  echo $serving_comp_id."</br>";
									if($ex_data[8]==1)
									{
										$serving_company=$company_short_library[$serving_comp_id];
									}
									else if($ex_data[8]==3)
									{
										$serving_company=$supplier_arr[$serving_comp_id];
									}
									
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=$polyIn_Qty=$polyOut_Qty=0;
									//$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
	
									$cutting_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['1']['0']['pQty'];
									$print_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['2']['1']['embQty'];
									$printRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['3']['1']['embQty'];
									$emb_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['2']['2']['embQty'];
									$embRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['3']['2']['embQty'];
									$wash_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['2']['3']['embQty'];
									$washRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['3']['3']['embQty'];
									$special_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['2']['4']['embQty'];
									$specialRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['3']['4']['embQty'];
									$sewIn_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['4']['1']['sQty'];
									$sewIn_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['4']['3']['sQty'];
									$sewOut_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['1']['sQty'];
									$sewOut_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['3']['sQty'];
									$ironIn_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['7']['1']['sQty'];
									$ironOut_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['7']['3']['sQty'];
									$reIron_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['7']['0']['reQty'];
									$finishIn_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['8']['1']['sQty'];
									$finishOut_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['8']['3']['sQty'];
									$carton_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['0']['0']['crtQty'];
									$rejFinish_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['8']['0']['rejectQty'];
									$rejSewing_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['0']['rejectQty'];

									$polyIn_Qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['11']['1']['sQty'];
									$polyOut_Qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['11']['3']['sQty'];
									
									$rejIron_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['7']['0']['rejectQty'];
									$rejPrint_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['3']['0']['rejectQty'];
									$rejCutting_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['1']['0']['rejectQty'];
									//echo $rejFinish_qty.'='.$rejSewing_qty.'='.$rejIron_qty.'='.$rejPrint_qty.'='.$rejCutting_qty;
									$ex_fact = $prod_date_qty_arr_ex[$po_id][$ex_data[9]][$prod_date][$item_id]['ex_factory_qnty'];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
										<td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i;?></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $serving_company; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_no;?></p></td>
										<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
										<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><?php echo change_date_format($job_arr2[$po_id]['pub_shipment_date']); ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $buyer_short_library[$buyer_name]; ?></p></td>
										<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $style_ref; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $file_no; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $ref_no; ?></p></td>
										<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $garments_item[$item_id]; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($prod_date); ?></p></td>
										
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? //echo $floor_id; ?>','Cutting Info','cutting_popup');" ><? echo $cutting_qty; ?></a></td>
										
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="Here"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? //echo $floor_id; ?>','Printing Issue Info','printing_issue_popup');" ><? echo $print_qty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? //echo $floor_id; ?>','Priniting Receive Info','printing_receive_popup');" ><? echo $printRec_qty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Embroidery Issue Info','embroi_issue_popup');" ><? echo $emb_qty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Embroidery Receive Info','embroi_receive_popup');" ><? echo $embRec_qty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Wash Issue Info','wash_issue_popup');" ><? echo $wash_qty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Wash Receive Info','wash_receive_popup');" ><? echo $washRec_qty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Special Works Issue Info','sp_issue_popup');" ><? echo $special_qty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Special Works Receive Info','sp_receive_popup');" ><? echo $specialRec_qty; ?></a> </td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','','1','4','','sewingQnty_popup');" ><? echo $sewIn_inQty; ?></a></td>
										
										<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','3','4','sewingQnty_popup');" ><? echo $sewIn_outQty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $sewing_input_total=$sewIn_inQty+$sewIn_outQty; echo $sewing_input_total; ?></td>
										
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id; ?>','','1','5','sewingQnty_popup');" ><? echo $sewOut_inQty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','','3','5','sewingQnty_popup');" ><? echo $sewOut_outQty; ?></a></td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $sewing_output_total=$sewOut_inQty+$sewOut_outQty; echo $sewing_output_total; ?></td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right">
											<? 
										$fin_rcv_qnty=$order_wise_fin_rec_qnty[$po_id][$prod_date][$item_id]['qnty'];
										$company_id=$order_wise_fin_rec_qnty[$po_id][$prod_date][$item_id]['company_id'];
										$fin_rcv_total+=$fin_rcv_qnty;
										$arr_bind=$po_id."***".$prod_date."***".$item_id;
										array_push($check_fin_rcv, $arr_bind); 

											?>

										<a href="##" onclick="openmyfinrcv('<?=$po_id;?>','<?=$company_id;?>','<?=$prod_date;?>','<?=$item_id;?>')"><? echo fn_number_format($fin_rcv_qnty,0,".",","); ?></a>
											
										</td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'','1','0','ironQnty_popup');" ><? echo $ironIn_qty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'','3','0','ironQnty_popup');" ><? echo $ironOut_qty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $iron_qty_total=$ironIn_qty+$ironOut_qty; echo $iron_qty_total; ?></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $reIron_qty; ?></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','','1','11','sewingQnty_popup');" ><? echo $polyIn_Qty; ?></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','','3','11','sewingQnty_popup');" ><? echo $polyOut_Qty; ?></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $poly_qty_total=$polyIn_Qty+$polyOut_Qty; echo $poly_qty_total; ?></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $finishIn_qty; ?></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $finishOut_qty; ?></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'','0','0','finishQnty_popup');" ><? $finishing_qty=$finishIn_qty+$finishOut_qty; echo $finishing_qty; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $carton_qty; ?></td>
										<? $prod_dzn=0;
										if($sewing_output_total!=0) 
										{
											$prod_dzn=($sewing_output_total)/12;
										}
										?>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
										<? //$cm_per=0; $cm_per=$cm_per_dzn[$rows[csf("job_no_mst")]] ;
										$rej_title='Fin '.$rejFinish_qty.', Sew '.$rejSewing_qty.', Iron '.$rejIron_qty.', Print '.$rejPrint_qty.', Cut '.$rejCutting_qty;
											?>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
										<a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','reject_qty');" ><? $reject_Qty=$rejFinish_qty+$rejSewing_qty+$rejIron_qty+$rejPrint_qty+$rejCutting_qty; echo $reject_Qty;  ?></a>
										</td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact; //$ex_fact=$ex_fac_arr_po_datewise[$po_id][$prod_date]; ?></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_id]; ?></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact_bal= $sewing_output_total-$ex_fact; ?></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_id]; ?></td>
										
										<td style="word-wrap: break-word;word-break: break-all;" width="">
											<a href="##"  onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > Veiw </a>
										</td>
									</tr>
									<?
									$tot_cutting_qty+=$cutting_qty;
									$tot_print_qty+=$print_qty;
									$tot_printRec_qty+=$printRec_qty;
									$tot_emb_qty+=$emb_qty;
									$tot_embRec_qty+=$embRec_qty;
									$tot_wash_qty+=$wash_qty;
									$tot_washRec_qty+=$washRec_qty;
									$tot_special_qty+=$special_qty;
									$tot_specialRec_qty+=$specialRec_qty;
									$tot_sewIn_inQty+=$sewIn_inQty;
									$tot_sewIn_outQty+=$sewIn_outQty;
									$tot_sewing_input+=$sewing_input_total;
									$tot_sewOut_inQty+=$sewOut_inQty;
									$tot_sewOut_outQty+=$sewOut_outQty;
									$tot_sewing_output+=$sewing_output_total;
									$tot_ironIn_qty+=$ironIn_qty;
									$tot_ironOut_qty+=$ironOut_qty;
									$tot_iron_qty+=$iron_qty_total;
									$tot_reIron_qty+=$reIron_qty;
									$tot_polyIn_Qty+=$polyIn_Qty;
									$tot_polyOut_Qty+=$polyOut_Qty;
									$tot_poly_qty_sum+=$poly_qty_total;
									$tot_finishIn_qty+=$finishIn_qty;
									$tot_finishOut_qty+=$finishOut_qty;
									$tot_finishing_qty+=$finishing_qty; 
									$tot_carton_qty+=$carton_qty;
									$total_prod_dzn+=$prod_dzn;
									$tot_rejFinish_qty+=$rejFinish_qty;
									$tot_rejSewing_qty+=$rejSewing_qty;
									$tot_reject_Qty+=$reject_Qty;
									$tot_ex_fac+=$ex_fact;
									$tot_ex_fac_fob+=$ex_fact_fob;
									$tot_fac_bal+=$ex_fact_bal;
									$tot_fac_bal_fob+=$ex_fact_bal_fob;
									$i++;
								}
							}
						}
					}
					//unset($date_sql_result);

					// echo "<pre>";
					// print_r($rcv_data_need_to_show);
					// echo "</pre>";

					$rcv_data_need_to_show=array();
					foreach ($order_wise_fin_rec_qnty as $po_id=> $po_wise_data) 
					{

						foreach ($po_wise_data as $receive_date => $rcv_date_wise_data) 
						{
							foreach ($rcv_date_wise_data as $item_id => $item_data) 
							{
								
								$arr_bind=$po_id."***".$receive_date."***".$item_id;
								if(!in_array($arr_bind, $check_fin_rcv))
								{
									$buyer_fullQty_arr[$item_data['buyer_name']][0]['0']['fin_rcv_qnty']+=$item_data['qnty'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['qnty']=$item_data['qnty'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['job_no']=$item_data['job_no'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['po_number']=$item_data['po_number'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['buyer_name']=$item_data['buyer_name'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['shipment_date']=$item_data['shipment_date'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['file_no']=$item_data['file_no'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['grouping']=$item_data['grouping'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['style_ref_no']=$item_data['style_ref_no'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['gmts_item_id']=$item_data['gmts_item_id'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['item_id']=$item_data['item_id'];
									$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['company_id']=$item_data['company_id'];
									array_push($check_fin_rcv, $arr_bind);
								}
							}
						}
					}


					foreach ($rcv_data_need_to_show as $po_id => $po_d) 
					{
						foreach ($po_d as $receive_date => $date_data) 
						{
							foreach ($date_data as $item_id => $rcv_data) 
							{
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
										<td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i;?></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $company_short_library[$rcv_data['company_id']]; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['job_no'];?></p></td>
										<td width="130" style="word-wrap: break-word;word-break: break-all;">
											<p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $rcv_data['po_number'];?></a></p>
										</td>
										<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><?php echo change_date_format($rcv_data['shipment_date']); ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $buyer_short_library[$rcv_data['buyer_name']]; ?></p></td>
										<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['style_ref_no']; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['file_no']; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['grouping']; ?></p></td>
										<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $garments_item[$item_id]; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($receive_date); ?></p></td>
										
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="Here"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"> </td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right">
											<? 
										$fin_rcv_qnty=$rcv_data['qnty'];
										$company_id=$rcv_data['company_id'];
										$fin_rcv_total+=$fin_rcv_qnty;  ?>
										<a href="##" onclick="openmyfinrcv('<?=$po_id;?>','<?=$company_id;?>','<?=$receive_date;?>','<?=$item_id;?>')"><? echo fn_number_format($fin_rcv_qnty,0,".",","); ?></a>	
										</td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" >
										
										</td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
										
										<td style="word-wrap: break-word;word-break: break-all;" width="">
											
										</td>
									</tr>
								<?
								$i++;
							}
						}
					}
				?>
				
				</table> 
				<table width="3915" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
					<tr>
							
						<td width="30" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>    
						<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
						<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
						<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
						<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
						<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;" align="right">Total</td>
						<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right" id="total_cut_td" ><? echo $tot_cutting_qty;?></td> 
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?></td> 
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_printrcv_td"><?  echo $tot_printRec_qty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?></td> 
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_emb_re"><? echo $tot_embRec_qty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?></td> 
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_wash_re"><? echo $tot_washRec_qty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?></td> 
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sp_re"><? echo $tot_specialRec_qty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewin_td"><? echo $tot_sewing_input; ?></td> 
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_fin_rcv_td"><? echo $fin_rcv_total; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_iron_in_td"><?  echo $tot_ironIn_qty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_iron_out_td"><?  echo $tot_ironOut_qty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_iron_td"><?  echo $tot_iron_qty; ?></td> 
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_re_iron_td"><?  echo $tot_reIron_qty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_polyIn_Qty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_polyOut_Qty; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_poly_qty_sum; ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_finishin_td"><? echo $tot_finishIn_qty; ?></td>
						<td width="80"  style="word-wrap: break-word;word-break: break-all;"align="right" id="total_finishout_td"><? echo $tot_finishOut_qty; ?></td> 
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_finish_td"><? echo $tot_finishing_qty; ?></td>   
						<td width="80"  style="word-wrap: break-word;word-break: break-all;"align="right" id="total_carton_td"><? echo $tot_carton_qty; ?></td> 
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_reject_Qty,2); ?></td >
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td1"><? echo number_format($tot_ex_fac,2); ?></td >
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td2"><? echo number_format($tot_ex_fac_fob,2); ?></td >
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td3"><? echo number_format($tot_fac_bal,2); ?></td >
						<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td4"><? echo number_format($tot_fac_bal_fob,2); ?></td >
						<td>&nbsp;</td>
				</tr>
				</table>
					
			</div>
				
				
			<?  
			

			/*=============================================================================================/
	        /											Subcon part										   /
	        /============================================================================================ */

			
			$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
			$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
			
			if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
			if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in($location) "; 
			?>
					
				<h5 style="width:800px;float: left; background-color:#FCF"><strong>Production-Subcontract Order (Inbound) Details</strong></h5>
					
					<table width="1590" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_2">
						<thead>
							<tr>
								<th width="30">Sl.</th>    
								<th width="100">Working Factory</th>
								<th width="100">Job No</th>
								<th width="130">Order No</th>
								<th width="100">Buyer </th>
								<th width="130">Style </th>
								<th width="130">Item Name</th>
								<th width="75">Production Date</th>
									
								<th width="100">Floor</th>
								<th width="100">Sewing Line</th>

								<th width="90">Cutting</th>
								<th width="90">Sewing Input</th>
								<th width="90">Sewing Output</th>
								<th width="90">Iron Output</th>
								<th width="90">Gmts. Finishing</th>
								<th width="">Remarks</th>
							</tr>
						</thead>
					</table>
				<div style="max-height:300px; overflow-y:scroll; width:1610px" id="scroll_body2">
					<table border="1"  class="rpt_table"  width="1590" rules="all" id="sub_list_view">
							<? 
							$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
							$subcon_lc_comp2=str_replace("a.", "c.", $subcon_lc_comp);
							$subcon_work_comp2=str_replace("a.", "c.", $subcon_work_comp);
							
							$production_array=array();
							if($db_type==0)
							{
								$prod_sql= "SELECT c.order_id, c.production_date,c.floor_id,c.line_id,
									sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END) AS cutting_qnty,
									sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END) AS sewing_input_qnty,
									sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END) AS sewingout_qnty,
									sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END) AS ironout_qnty,
									sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END) AS gmts_fin_qnty
								from 
									subcon_gmts_prod_dtls c
								where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp2 $subcon_work_comp2  group by c.order_id, c.production_date,c.floor_id";
							}
							else
							{
									$prod_sql= "SELECT c.order_id,c.gmts_item_id, c.production_date,c.floor_id,c.line_id,
									NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
									sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END) AS sewing_input_qnty,

									NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
									NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS ironout_qnty,
									NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS gmts_fin_qnty
								from 
									subcon_gmts_prod_dtls c
								where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp2 $subcon_work_comp2 group by c.order_id,c.gmts_item_id, c.production_date,c.floor_id,c.line_id";
							}
							$prod_sql_result= sql_select($prod_sql);
							// echo $prod_sql;//die;
							foreach($prod_sql_result as $proRes)
							{
								$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['cutting_qnty']=$proRes[csf("cutting_qnty")];
								$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['sewing_input_qnty']=$proRes[csf("sewing_input_qnty")];
								$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['sewingout_qnty']=$proRes[csf("sewingout_qnty")];
								$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['ironout_qnty']=$proRes[csf("ironout_qnty")];
								$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['gmts_fin_qnty']=$proRes[csf("gmts_fin_qnty")];
							}
							// echo "<pre>";
							// print_r($production_array);
							if($db_type==0)
							{	
								$order_sql= "SELECT c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty, b.production_date, b.line_id,b.floor_id,b.prod_reso_allo 
								from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
								where b.order_id=c.id and b.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp $subcon_work_comp $sub_floor_name $sub_location_cond and a.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
								group by b.order_id, b.production_date 
								order by b.production_date";
							}
							else
							{
									$order_sql= "SELECT c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty,  b.production_date, b.line_id,b.floor_id,b.prod_reso_allo 
									from subcon_ord_mst a, subcon_ord_dtls c , subcon_gmts_prod_dtls b
									where a.id=c.mst_id and b.order_id=c.id and  b.production_date between $txt_date_from and $txt_date_to  $subcon_lc_comp $subcon_work_comp	 $sub_floor_name $sub_location_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
									group by b.order_id, c.id, c.order_no, c.cust_style_ref, a.company_id, a.job_no_prefix_num, a.party_id, a.company_id, a.party_id, a.location_id, b.gmts_item_id, b.production_date, b.line_id,b.floor_id,b.prod_reso_allo  
									order by c.id";
							}
	
							// echo $order_sql; die;
							
							$order_sql_result=sql_select($order_sql);
								$j=0;$k=0;
								$total_cutt = 0;
								//$po_item_line_array=array();
								foreach($order_sql_result as $orderRes)
								{

									//if( $po_item_line_array[$orderRes[csf("id")]][$orderRes[csf("gmt_item_id")]][$orderRes[csf("line_id")]]=="" )
									//{
										$j++;
										if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
										
										$sewing_line='';
										if($orderRes[csf('prod_reso_allo')]==1)
										{
											$line_number=explode(",",$prod_reso_arr[$orderRes[csf("line_id")]]);
											foreach($line_number as $val)
											{
												if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
											}
										}
										else {$sewing_line=$line_library[$orderRes[csf("line_id")]];}
										$po_id=$orderRes[csf("id")];
										$item_id=$orderRes[csf("gmts_item_id")];
										$prod_date=$orderRes[csf("production_date")];

										
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>" style="height:20px">
											<td width="30" ><? echo $j; ?></td>    
											<td width="100"><p><? echo $company_short_library[$orderRes[csf("company_id")]]; ?></p></td>
											<td width="100" align="center"><p><? echo $orderRes[csf("job_no_prefix_num")]; ?></p></td>
											<td width="130"><p><? echo $orderRes[csf("order_no")]; ?></p></td>
											<td width="100"><? echo $buyer_short_library[$orderRes[csf("party_id")]]; ?></td>
											<td width="130"><p><? echo $orderRes[csf("cust_style_ref")]; ?></p></td>
											<td width="130"><p><? echo $garments_item[$orderRes[csf("gmts_item_id")]];?></p></td>
											<td width="75" bgcolor="<? echo $color; ?>"><? echo change_date_format($orderRes[csf("production_date")]);  ?></td>

											<td width="100" align="left"><p><? echo $floor_library[$orderRes[csf('floor_id')]]; ?></p></td>

											<td width="100" align="left"><p><? echo $sewing_line; ?></p></td>
												
											<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','','1','1','production_popup_subcon');" ><? echo $cutting= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['cutting_qnty']; $total_cutt+=$cutting; ?></a></td>

											<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','','1','7','production_popup_subcon');" ><? echo $input= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['sewing_input_qnty']; $total_sewinput+=$input; ?></a></td>

											<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','','1','2','production_popup_subcon');" ><? echo $output=$production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['sewingout_qnty']; $total_sew+=$output; ?></a></td>
											<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','','1','3','production_popup_subcon');" ><? echo $iron= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['ironout_qnty']; $total_iron_sub+=$iron; ?></a></td>
											<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','','1','4','production_popup_subcon');" ><? echo $fin= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['gmts_fin_qnty']; $total_gmtfin+=$fin; ?></a></td>
											
											<td width="">&nbsp;</td>
										</tr>
										<?
										//$po_item_line_array[$orderRes[csf("id")]][$orderRes[csf("gmt_item_id")]]=$orderRes[csf("line_id")];

									//}

									
								}
								?>  
							</table>
							
							<table border="1" class="tbl_bottom"  width="1590" rules="all" id="report_table_footer2" >
								<tr>
									<td width="30">&nbsp;</td>    
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="130">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="130">&nbsp;</td>
									<td width="130">&nbsp;</td>
										
									<td width="75"></td>
									<td width="100">&nbsp;</td>
									<td width="100">Total:</td>
										
									<td width="90" id="total_cutt"><? echo $total_cutt; ?></td>
									<td width="90" id="total_sew_input"><? echo $total_sew_input; ?></td>
									<td width="90" id="total_sew"><? echo $total_sew; ?></td>
									<td width="90" id="total_iron_sub"><? echo $total_iron_sub; ?></td>
									<td width="90" id="total_gmtfin"><? echo $total_gmtfin; ?></td>
									<td width=""></td>
									</tr>
							</table>
							
				</div>	


			<?
			
			//-------------------------------------------END Show Date Location Floor & Line Wise------------------------
			?>
		</div><?
	}

	foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename,'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $html = ob_get_contents();
    ob_clean();
    echo "$html**$filename**$type";
    exit();  
	 
}

if($action == "report_generate_show_2"){ //Show3 button, type=2
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_brand_name=str_replace("'","",$cbo_brand_name);
	$cbo_season_name=str_replace("'","",$cbo_season_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$cbo_job_year=str_replace("'","",$cbo_job_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$hidden_job_id=str_replace("'","",$hidden_job_id);  
	$txt_production_date = str_replace("'","",$txt_production_date);
	$txt_production_date = change_date_format($txt_production_date, "", "",1);


	//Library
	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name"); 
    $companyArr[0] = "All Company";
    $com_dtls = fnc_company_location_address($cbo_company_name, "", 1);

	//Search condition
	$searchCond="";
	if($cbo_company_name!=0) $searchCond.=" and d.serving_company in($cbo_company_name)";
	if($cbo_buyer_name!=0) $searchCond.=" and a.buyer_name=$cbo_buyer_name";		
	if($cbo_brand_name!=0) $searchCond.=" and a.brand_id=$cbo_brand_name";
	if($cbo_season_name!=0) $searchCond.=" and a.season_buyer_wise=$cbo_season_name";
	if($cbo_location>0) $searchCond.=" and d.location=$cbo_location";
	if($cbo_floor>0) $searchCond.=" and d.floor_id=$cbo_floor";
	if($cbo_job_year>0) $searchCond .=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
	if($hidden_job_id!="") $searchCond.=" and a.id=$hidden_job_id";
	if($txt_production_date!="") $searchCond.=" and d.production_date <= '$txt_production_date'";
	$production_type="1,4,5,11"; //2,3,7,8,
	$searchCond .=" and d.production_type in ($production_type)";
	$dataArray = array();
	//--WHEN e.production_type =1 THEN e.production_qnty ELSE 0 END AS total_cutting
	$sql = "SELECT b.id as order_id, a.style_ref_no, a.avg_unit_price, b.po_number, b.excess_cut, b.po_quantity, c.color_number_id, c.item_number_id,c.order_rate,
			c.order_quantity, c.excess_cut_perc, a.buyer_name,e.production_type,c.order_total,c.article_number,
			CASE WHEN e.production_type =1 and d.production_date = '$txt_production_date' THEN e.production_qnty ELSE 0 END AS today_cutting,
		    CASE WHEN e.production_type =1 THEN e.production_qnty ELSE 0 END AS total_cutting,
			CASE WHEN e.production_type =4 and d.production_date = '$txt_production_date' THEN e.production_qnty ELSE 0 END AS today_sewing_input,
		    CASE WHEN e.production_type =4 THEN e.production_qnty ELSE 0 END AS total_sewing_input,
			CASE WHEN e.production_type =5 and d.production_date = '$txt_production_date' THEN e.production_qnty ELSE 0 END AS today_sewing_output,
		    CASE WHEN e.production_type =5 THEN e.production_qnty ELSE 0 END AS total_sewing_output,
			CASE WHEN e.production_type =11 and d.production_date = '$txt_production_date' THEN e.production_qnty ELSE 0 END AS today_poly,
		    CASE WHEN e.production_type =11 THEN e.production_qnty ELSE 0 END AS total_poly
		    FROM wo_po_details_master          a,
				wo_po_break_down              b,
				wo_po_color_size_breakdown    c,
				pro_garments_production_mst   d,
				pro_garments_production_dtls  e
	 		WHERE a.id=b.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id 
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
			and b.shiping_status != 3 $searchCond";
	//echo $sql; exit();
	$orderIdArray = array();
	foreach(sql_select($sql) as $val){

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]]['order_quantity'] = $val[csf('order_total')];

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]]['excess_cut_percent'] = $val[csf('order_total')] + (($val[csf('order_total')] * $val[csf('excess_cut_perc')])/100);

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]]['fob'] = $val[csf('order_rate')];
		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]]['article_number'][$val[csf('article_number')]] = $val[csf('article_number')];

		//Calculation with $production_type array from array_function.php
		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]][$val[csf('production_type')]]['today_cutting'] += $val[csf('today_cutting')];

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]][$val[csf('production_type')]]['total_cutting'] += $val[csf('total_cutting')];

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]][$val[csf('production_type')]]['today_sewing_input'] += $val[csf('today_sewing_input')];

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]][$val[csf('production_type')]]['total_sewing_input'] += $val[csf('total_sewing_input')];

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]][$val[csf('production_type')]]['today_sewing_output'] += $val[csf('today_sewing_output')];

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]][$val[csf('production_type')]]['total_sewing_output'] += $val[csf('total_sewing_output')];

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]][$val[csf('production_type')]]['today_poly'] += $val[csf('today_poly')];

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]][$val[csf('production_type')]]['total_poly'] += $val[csf('total_poly')];

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]]['buyer_name'] = $val[csf('buyer_name')];

		$dataArray[$val[csf('style_ref_no')]][$val[csf('order_id')]][$val[csf('item_number_id')]] [$val[csf('color_number_id')]]['po_number'] = $val[csf('po_number')];
 
		$orderIdArray[$val[csf('order_id')]] = $val[csf('order_id')];

	}
	//echo "<pre>"; print_r($dataArray); exit();

	$con = connect();
	$r_id2=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (43)");
	if($r_id2){
		oci_commit($con);
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 43, 1,$orderIdArray, $empty_arr);
	
	$order_result=sql_select("SELECT a.order_quantity, a.po_break_down_id, a.item_number_id, a.color_number_id, a.plan_cut_qnty, a.excess_cut_perc from wo_po_color_size_breakdown a, GBL_TEMP_ENGINE b where a.po_break_down_id=b.ref_val and b.entry_form=43 and b.ref_from=1 and b.user_id=$user_id and a.status_active=1 and a.is_deleted=0");
	//echo $order_result; exit();
	$r_id2=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (43)");
	if($r_id2){
		oci_commit($con);
	}

	$order_qty_array = array();
	foreach($order_result as $res){
		$order_qty_array[$res[csf('po_break_down_id')]] [$res[csf('item_number_id')]] [$res[csf('color_number_id')]] ['order_quantity'] += $res[csf('order_quantity')];
		$order_qty_array[$res[csf('po_break_down_id')]] [$res[csf('item_number_id')]] [$res[csf('color_number_id')]] ['excess_cut_percent'] += $res[csf('plan_cut_qnty')];

		
	}

	//echo "<pre>"; print_r($order_qty_array); exit();
	ob_start();
	$width =  2500;
	?>
		<style>
			#fixHeader{
				overflow-y: auto;
        		height: 400px;
			}
			#fixHeader #tbl_head {
				position: sticky;
				top: 0px;
			}
			#fixHeader #tbl_foot {
				position: sticky;
				bottom: 1px;
			}
			table {
				border-collapse: collapse; 
			}
			.word_break{
				word-break: break-all;
			}
		</style>

		
		<div id="fixHeader">
			<table width="<?= $width ?>" style="background-image: -webkit-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);" >
				<tbody>
					<tr><td align="center" width="100%" colspan="29" class="form_caption" style="font-size:18px;">Daily RMG Production status Report</td></tr>
					<tr><td align="center" width="100%" colspan="29" class="form_caption"><?=$companyArr[$cbo_company_name];?></td></tr>
					<tr><td align="center" width="100%" colspan="29" class="form_caption" style="font-size:12px;"><?=$com_dtls[1];?></td></tr>
					<tr><td align="center" width="100%" colspan="29" class="form_caption" style="font-size:14px;">Date: <?=$txt_production_date; ?></td></tr>
				</tbody>
			</table>
			<div id="tbl_head">
				<table class="rpt_table" border="1" rules="all" width="<?= $width ?>" style="height: 100px;">
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th>
							<th width="100" rowspan="2">Style No.</th>
							<th width="100" rowspan="2">PO</th>
							<th width="100" rowspan="2">Article No</th>
							<th width="100" rowspan="2">Buyer</th>
							<th width="100" rowspan="2">Color</th>
							<th width="100" rowspan="2">Item</th>
							<th width="100" rowspan="2">Order Qty</th>
							<th width="100" rowspan="2">Access Cut(%)</th>
							<th width="300" colspan="3">Cutting Status</th>
							<th width="300" colspan="3">Input Status</th>
							<th width="300" colspan="3">Sewing Status</th>
							<th width="300" colspan="3">Poly Status</th>
							<th width="50" rowspan="2">$FOB</th>
							<th width="100" rowspan="2">Finishing Balance</th>
							<th width="100" rowspan="2">Finishing Rcvd Qty Value</th>
							<th width="100" rowspan="2">Poly Completed Qty Value</th>
							<th width="100" rowspan="2">Poly Balance Qty Value</th>
						</tr>
						<tr>
							<th width="100">Today Cutting</th>
							<th width="100">Total Cutting</th>
							<th width="100">Cutting Balance</th>

							<th width="100">Today Input</th>
							<th width="100">Total Input</th>
							<th width="100">Input Balance</th>

							<th width="100">Today Sewing</th>
							<th width="100">Total Sewing</th>
							<th width="100">Sewing Balance</th>

							<th width="100">Today Poly</th>
							<th width="100">Total Poly</th>
							<th width="100">Poly Balance</th>
						</tr>
					</thead>
				</table>
			</div>
			<div>
				<table class="rpt_table" border="1" rules="all" width="<?= $width ?>" id="table_body">
					<tbody>
						<?php 
							$serial = 1;
							foreach($dataArray as $style => $styleWise)
							{
								foreach($styleWise as $order_id => $poWise)
								{
									foreach($poWise as $item => $colorWise)
									{
										foreach($colorWise as $color => $itemData)
										{
											$order_quantity = $order_qty_array[$order_id][$item][$color]['order_quantity'];
											$excess_cut_percent = $order_qty_array[$order_id][$item][$color]['excess_cut_percent'];
											
											$cutting_balance 				= floor($itemData[1]['total_cutting'] - $excess_cut_percent);
											$sewing_input_balance 			= floor($itemData[4]['total_sewing_input'] - $excess_cut_percent);
											$sewing_output_balance 			= floor($itemData[5]['total_sewing_output'] - $excess_cut_percent);
											$finishing_balance 				= floor($itemData[11]['total_poly'] - $itemData[5]['total_sewing_output']);
											$poly_balance 					= floor($itemData[11]['total_poly'] - $order_quantity);
											$finising_rcv_qty_val			= floor($itemData[5]['total_sewing_output'] * $itemData['fob']);
											$poly_completed_qty_val			= floor($itemData[11]['total_poly'] * $itemData['fob']);
											$poly_balance_qty_val			= floor($finishing_balance * $itemData['fob']);

											//Summary varialbles.
											$total_order_quantity 			+= floor($itemData['order_quantity']);
											$total_excess_cut 				+= floor($excess_cut_percent);
											$total_today_cutting			+= floor($itemData[1]['today_cutting']);
											$total_total_cutting			+= floor($itemData[1]['total_cutting']);
											$total_cutting_balance			+= floor($cutting_balance);

											$total_today_sewing_input			+= floor($itemData[4]['today_sewing_input']);
											$total_total_sewing_input			+= floor($itemData[4]['total_sewing_input']);
											$total_sewing_input_balance			+= floor($sewing_input_balance);

											$total_today_sewing_output			+= floor($itemData[5]['today_sewing_output']);
											$total_total_sewing_output			+= floor($itemData[5]['total_sewing_output']);
											$total_sewing_output_balance		+= floor($sewing_output_balance);

											$total_today_poly					+= floor($itemData[11]['today_poly']);
											$total_total_poly					+= floor($itemData[11]['total_poly']);
											$total_poly_balance					+= floor($poly_balance);

											$total_fob							+= round($itemData['fob'], 2);
											$total_finishing_balance			+= floor($finishing_balance);
											$total_finising_rcv_qty_val			+= floor($finising_rcv_qty_val);
											$total_poly_completed_qty_val		+= floor($poly_completed_qty_val);
											$total_poly_balance_qty_val			+= floor($poly_balance_qty_val);

											if ($serial%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
												<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_1nd<? echo $serial; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $serial; ?>">
													<td width="30" class="word_break"><?=$serial;?></td>
													<td width="100" class="word_break"><?=$style;?></td>
													<td width="100" class="word_break"><?=$itemData['po_number'];?></td>
													<td width="100" class="word_break"><?= implode(',',$itemData['article_number']);?></td>
													<td width="100" class="word_break"><?=$buyerArr[$itemData['buyer_name']];?></td>
													<td width="100" class="word_break"><?=$color_Arr_library[$color];?></td>
													<td width="100" class="word_break"><?=$garments_item[$item];?></td>
													<td width="100" class="word_break" align="right"><?=$order_quantity;?></td>
													<td width="100" class="word_break" align="right"><?=floor($excess_cut_percent);?></td>

													<td width="100" class="word_break" align="right"><?=floor($itemData[1]['today_cutting']);?></td>
													<td width="100" class="word_break" align="right"><?=floor($itemData[1]['total_cutting']);?></td>
													<td width="100" class="word_break" align="right"><?=$cutting_balance;?></td>

													<td width="100" class="word_break" align="right"><?=floor($itemData[4]['today_sewing_input']);?></td>
													<td width="100" class="word_break" align="right"><?=floor($itemData[4]['total_sewing_input']);?></td>
													<td width="100" class="word_break" align="right"><?=$sewing_input_balance;?></td>

													<td width="100" class="word_break" align="right"><?=floor($itemData[5]['today_sewing_output']);?></td>
													<td width="100" class="word_break" align="right"><?=floor($itemData[5]['total_sewing_output']);?></td>
													<td width="100" class="word_break" align="right"><?=$sewing_output_balance;?></td>

													<td width="100" class="word_break" align="right"><?=floor($itemData[11]['today_poly']);?></td>
													<td width="100" class="word_break" align="right"><?=floor($itemData[11]['total_poly']);?></td>
													<td width="100" class="word_break" align="right"><?=$poly_balance;?></td>

													<td width="50" align="right" class="word_break"><?=round($itemData['fob'], 2);?></td>
													<td width="100" class="word_break" align="right"><?=$finishing_balance;?></td>
													<td width="100" class="word_break" align="right"><?=$finising_rcv_qty_val;?></td>
													<td width="100" class="word_break" align="right"><?=$poly_completed_qty_val;?></td>
													<td width="100" class="word_break" align="right"><?=$poly_balance_qty_val;?></td>
												</tr>
											<?php
											$serial++;
										}
									}
								}
							}
						?>
					</tbody>
				</table>
			</div>
			<div id="tbl_foot">
				<table class="rpt_table" border="1" rules="all" width="<?= $width ?>" >
					<tfoot>
						<tr >
							<th colspan="7" align="center" valign="middle">Total Quantity</th>
							<th width="100" id="total_order_quantity"><?=$total_order_quantity;?></th>
							<th width="100" id="total_excess_cut"><?=$total_excess_cut;?></th>

							<th width="100" id="total_today_cutting"><?=$total_today_cutting;?></th>
							<th width="100" id="total_total_cutting"><?=$total_total_cutting;?></th>
							<th width="100" id="total_cutting_balance"><?=$total_cutting_balance;?></th>

							<th width="100" id="total_today_sewing_input"><?=$total_today_sewing_input;?></th>
							<th width="100" id="total_total_sewing_input"><?=$total_total_sewing_input;?></th>
							<th width="100" id="total_sewing_input_balance"><?=$total_sewing_input_balance;?></th>

							<th width="100" id="total_today_sewing_output"><?=$total_today_sewing_output;?></th>
							<th width="100" id="total_total_sewing_output"><?=$total_total_sewing_output;?></th>
							<th width="100" id="total_sewing_output_balance"><?=$total_sewing_output_balance;?></th>

							<th width="100" id="total_today_poly"><?=$total_today_poly;?></th>
							<th width="100" id="total_total_poly"><?=$total_total_poly;?></th>
							<th width="100" id="total_poly_balance"><?=$total_poly_balance;?></th>

							<th width="50" id="total_fob" align="right"><?=$total_fob;?></th>
							<th width="100" id="total_finishing_balance"><?=$total_finishing_balance;?></th>
							<th width="100" id="total_finising_rcv_qty_val"><?=$total_finising_rcv_qty_val;?></th>
							<th width="100" id="total_poly_completed_qty_val"><?=$total_poly_completed_qty_val;?></th>
							<th width="100" id="total_poly_balance_qty_val"><?=$total_poly_balance_qty_val;?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

		<script>
			function new_window(){
				var w = window.open("Surprise", "#");
	                var d = w.document.open();
	                d.write(document.getElementById('fixHeader').innerHTML);
	                d.close();
			}
		</script>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename,'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $html = ob_get_contents();
    ob_clean();
    echo "$html**$filename**$type";
    exit(); 
}
if($action == "report_generate_show_5"){ //Summary button, type=5  GBL_TEMP_ENGINE //REF_FROM 5  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_brand_name=str_replace("'","",$cbo_brand_name);
	$cbo_season_name=str_replace("'","",$cbo_season_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$cbo_job_year=str_replace("'","",$cbo_job_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$hidden_job_id=str_replace("'","",$hidden_job_id);  
	$txt_production_date = str_replace("'","",$txt_production_date);
	$txt_production_date = change_date_format($txt_production_date, "", "",1);


	//Library
	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name"); 
    $companyArr[0] = "All Company";
    $com_dtls = fnc_company_location_address($cbo_company_name, "", 1);

	//Search condition
	$searchCond="";
	if($cbo_company_name!=0) $searchCond.=" and d.serving_company in($cbo_company_name) ";
	if($cbo_buyer_name!=0) $searchCond.=" and a.buyer_name=$cbo_buyer_name ";		
	if($cbo_brand_name!=0) $searchCond.=" and a.brand_id=$cbo_brand_name ";
	if($cbo_season_name!=0) $searchCond.=" and a.season_buyer_wise=$cbo_season_name ";
	if($cbo_location>0) $searchCond.=" and d.location=$cbo_location ";
	if($cbo_floor>0) $searchCond.=" and d.floor_id=$cbo_floor ";
	if($cbo_job_year>0) $searchCond .=" and to_char(a.insert_date,'YYYY')='$cbo_job_year' ";
	if($hidden_job_id!="") $searchCond.=" and a.id=$hidden_job_id";
	if($txt_production_date!="") $searchCond.=" and d.production_date <= '$txt_production_date' "; 

	//=========================================================================================================
	//												PRODUCTION DATA
	// ========================================================================================================== 
	$sql = "SELECT b.id as po_id,c.order_rate,c.order_quantity as po_qty,a.buyer_name,e.production_type as prod_type,c.order_total,e.production_qnty as prod_qty,c.color_number_id as color,c.item_number_id as item,c.id as color_size_id FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls  e WHERE a.id=b.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type in (1,4,5,11) $searchCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.shiping_status != 3";
	// echo $sql; die();
	$po_id_array 		= array();
	$prod_data_array  	= array();
	$po_wise_data_array = array(); 
	$production_po_array= array(); 
	foreach(sql_select($sql) as $v)
	{
		if ($v['PROD_TYPE']==5) {
			$prod_data_array[$v['BUYER_NAME']][$v['PO_ID']]['SEWING_OUT'] += $v['PROD_QTY'];  
			$prod_data_array[$v['BUYER_NAME']][$v['PO_ID']]['SEWING_VAL'] += $v['PROD_QTY'] * $v['ORDER_RATE'];  
		}
		if ($v['PROD_TYPE']==11) {
			$prod_data_array[$v['BUYER_NAME']][$v['PO_ID']]['POLY'] += $v['PROD_QTY'];  
			$prod_data_array[$v['BUYER_NAME']][$v['PO_ID']]['POLY_VAL'] += $v['PROD_QTY'] * $v['ORDER_RATE'];  
		}
		$prod_data_array[$v['BUYER_NAME']][$v['PO_ID']]['ALL_PROD'] += $v['PROD_QTY']; //ONLY FOR TAKING ALL PO IN ARRAY

		$po_wise_data_array[$v['PO_ID']]['ORDER_TOTAL']  += $v['ORDER_TOTAL'];
		$po_wise_data_array[$v['PO_ID']]['PO_QTY']  	 += $v['PO_QTY']; 
		$po_wise_data_array[$v['PO_ID']]['ORDER_RATE']    = $v['ORDER_RATE']; 

		$po_id_array[$v['PO_ID']] 	= $v['PO_ID']; 
		$production_po_array [$v['PO_ID']] [$v['COLOR']][$v['ITEM']] = true;

	} 
	//=========================================================================================================
	//												CLEAR TEMP ENGINE
	// ==========================================================================================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=43 and ref_from=5 ");
	oci_commit($con);   
	// =========================================================================================================
	//												INSERT DATA INTO TEMP ENGINE
	// =========================================================================================================
	fnc_tempengine("gbl_temp_engine", $user_id, 43, 5,$po_id_array, $empty_arr); 
	 
	//=========================================================================================================
	//												ORDER DATA
	// ========================================================================================================== 
	$order_sql="SELECT a.order_quantity as po_qty, a.po_break_down_id as po_id,a.color_number_id as color,a.item_number_id as item  from wo_po_color_size_breakdown a, gbl_temp_engine tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=43 and tmp.ref_from=5 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0";
	//echo $order_result; die();
	$order_result = sql_select($order_sql);
	// echo $order_sql; die;
	$order_qty_array = array();
	$order_qty = 0;
	foreach($order_result as $v)
	{
		if($production_po_array[$v['PO_ID']] [$v['COLOR']][$v['ITEM']])   // CHEAKING SAME PO COLOR AND ITEM PRODUCTION OR NOT 
		{
			$order_qty_array[$v['PO_ID']] ['PO_QTY'] += $v['PO_QTY']; 
			// $order_qty  += $v['PO_QTY'] ;
		}
			
	}

	//=========================================================================================================
	//												 DATA MAKING
	// ==========================================================================================================
	$buyer_summary = array();
	foreach ($prod_data_array as $buyer_id => $po_data) 
	{
		foreach ($po_data as $po_id => $v) 
		{
			$order_qty	 = $order_qty_array[$po_id] ['PO_QTY'] ;
			$order_total = $po_wise_data_array[$po_id]['ORDER_TOTAL'];
			/* $po_qty 	 = $po_wise_data_array[$po_id]['PO_QTY']; 
			$fob2		 = $po_wise_data_array[$po_id]['ORDER_RATE'];
			$fob		 = $order_total / $po_qty;  */
			
			$sewing_out = $v['SEWING_OUT'];
			$poly_qty 	= $v['POLY'];

			// $sewing_out_val = $sewing_out * $fob;
			// $poly_val 		= $poly_qty * $fob;
			
			$sewing_out_val = $v['SEWING_VAL'];
			$poly_val 		= $v['POLY_VAL'];


			$buyer_summary[$buyer_id]['PO_QTY'] 		+= $order_qty;
			$buyer_summary[$buyer_id]['PO_QTY_PROD'] 	+= $po_qty; 
			$buyer_summary[$buyer_id]['SEWING_OUT'] 	+= $sewing_out;
			$buyer_summary[$buyer_id]['POLY_QTY'] 		+= $poly_qty; 
			$buyer_summary[$buyer_id]['SEWING_OUT_VAL'] += $sewing_out_val; 
			$buyer_summary[$buyer_id]['POLY_VAL'] 		+= $poly_val;
			 
		}
		 
	}
	// pre($buyer_summary); die;
	//=========================================================================================================
	//												CLEAR TEMP ENGINE
	// ========================================================================================================== 
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=43 and ref_from=5 ");
	oci_commit($con);    
	//echo "<pre>"; print_r($order_qty_array); exit();
	ob_start();
	$width =  890;
	?>
		<style>
			#fixHeader{
				overflow-y: auto;
        		height: 400px;
			}
			#fixHeader #tbl_head {
				position: sticky;
				top: 0px;
			}
			#fixHeader #tbl_foot {
				position: sticky;
				bottom: 1px;
			}
			table {
				border-collapse: collapse; 
			}
			table tr,th,td{
				word-break: break-word;
			}
		</style>

		
		<div  style="width: <?= $width ?>px; margin-top:30px;">
			<table width="<?= $width ?>" style="background-image: -webkit-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);" >
				<tbody>
					<tr><td align="center" width="100%" colspan="29" class="form_caption" style="font-size:18px;">Daily RMG Production status Report</td></tr>
					<tr><td align="center" width="100%" colspan="29" class="form_caption"><?=$companyArr[$cbo_company_name];?></td></tr>
					<tr><td align="center" width="100%" colspan="29" class="form_caption" style="font-size:12px;"><?=$com_dtls[1];?></td></tr> 
				</tbody>
			</table> 
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead class="form_caption" >
					<tr>
						<th width="30" >SL</th>
						<th width="100" >Buyer Name.</th>
						<th width="100" >Order Qty</th>
						<th width="110" >Finishing Received Quantity</th>
						<th width="110" >Finishing Complete Quantity</th>
						<th width="110" >Finishing Balance Quantity</th>
						<th width="110" >Finishing Received Value ($)</th>
						<th width="110" >Finishing Complete Value ($)</th>
						<th width="110" >Finishing Balance Value ($)</th> 
					</tr> 
				</thead>
			</table> 
			<div style="width:<?= $width+20;?>px; max-height:350px; float:left; overflow-y:scroll;" id="scroll_body">
				<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
					<tbody >
						<?php 
							$serial = 1;
							$total_order_qty = $total_sew_out = $total_poly_qty = $total_sew_out_val = $total_poly_val = $total_prod_bal = $total_val_bal = 0;
							foreach($buyer_summary as $buyer_id => $v)
							{ 
								$po_qty 		= $v['PO_QTY'];
								$sewing_out 	= $v['SEWING_OUT'];
								$poly_qty 		= $v['POLY_QTY'];
								$sewing_out_val	= $v['SEWING_OUT_VAL'];
								$poly_val 		= $v['POLY_VAL'];
								$prod_balance 	= $sewing_out - $poly_qty; 
								$val_balance 	= $sewing_out_val - $poly_val;

								$total_order_qty 	+= $po_qty;
								$total_sew_out 		+= $sewing_out;
								$total_poly_qty 	+= $poly_qty;
								$total_sew_out_val 	+= $sewing_out_val;
								$total_poly_val 	+= $poly_val;
								$total_prod_bal 	+= $prod_balance;
								$total_val_bal 		+= $val_balance;
								  
								if ($serial%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_1nd<? echo $serial; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $serial; ?>">
										<td width="30"><?= $serial; ?></td>
										<td width="100"><?= $buyerArr[$buyer_id]; ?></td>
										<td width="100" align="right"><?= $po_qty; ?></td>
										<td width="110" align="right"><?= $sewing_out; ?></td>
										<td width="110" align="right"><?= $poly_qty; ?></td>
										<td width="110" align="right"><?= $prod_balance ?></td>
										<td width="110" align="right"><?= number_format($sewing_out_val,2); ?></td>
										<td width="110" align="right"><?= number_format($poly_val,2); ?></td>
										<td width="110" align="right"><?= number_format($val_balance,2); ?></td>
									</tr>
								<?php
								$serial++; 		 
							}
						?>
					</tbody>
				</table>
			</div>
			<div id="tbl_foot">
				<table class="rpt_table" border="1" rules="all" width="<?= $width ?>" >
					<tfoot width="<?= $width ?>">
						<tr >
							<th width="30" align="center"></th>
							<th width="100" align="center">Total Quantity</th>
							<th width="100" id="total_order_qty"> 	<?= $total_order_qty ?> </th>
							<th width="110" id="total_sewing_qty"> 	<?= $total_sew_out ?> </th> 
							<th width="110" id="total_ploy_qty"> 	<?= $total_poly_qty ?> </th>
							<th width="110" id="total_prod_balance"><?= $total_prod_bal  ?> </th>
							<th width="110" id="total_sewing_val"> 	<?= number_format($total_sew_out_val,2) ?> </th> 
							<th width="110" id="total_poly_val"> 	<?= number_format($total_poly_val,2) ?> </th>
							<th width="110" id="total_val_balance"> <?= number_format($total_val_bal,2) ?> </th> 
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

		<script>
			function new_window(){
				var w = window.open("Surprise", "#");
	                var d = w.document.open();
	                d.write(document.getElementById('fixHeader').innerHTML);
	                d.close();
			}
		</script>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename,'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $html = ob_get_contents();
    ob_clean();
    echo "$html**$filename**$type";
    exit(); 
}

if($action=="remarks_popup__")
{
	extract($_REQUEST);
	list($po,$item,$country,$color)=explode('**', $data);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


	 $production_sql="SELECT a.production_date,c.id,c.color_number_id,c.size_number_id,a.sewing_line,a.floor_id,a.production_type,a.remarks,a.prod_reso_allo,sum(b.production_qnty) as qntys from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id='$po' and a.item_number_id='$item' and a.country_id='$country' and c.color_number_id='$color' group by a.production_date,c.id,c.color_number_id,c.size_number_id,a.sewing_line,a.floor_id,a.production_type,a.remarks,a.prod_reso_allo order by c.id";
	 //echo $production_sql;
	 $type_line_wise_arr=array();
	 $size_all_arr=array();
	foreach(sql_select($production_sql) as $keys=>$vals)
	{
	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["qntys"]+=$vals[csf("qntys")];

	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["prod_reso_allo"]=$vals[csf("prod_reso_allo")];

	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["remarks"]=$vals[csf("remarks")];

	 	$type_line_wise_arr_sizewise[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["size_qty"]+=$vals[csf("qntys")];

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	}
	 $cut_lay_sql="SELECT a.entry_date as production_date,b.color_id as color_number_id ,c.size_id as size_number_id ,sum(c.size_qty) as qntys,0 as floor_id,0 as  sewing_line  from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b ,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 and c.order_id='$po' and b.color_id='$color' and b.gmt_item_id ='$item' and c.country_id='$country'  group by  a.entry_date,b.color_id,c.size_id order by c.size_id";
	 //echo $cut_lay_sql;
	foreach(sql_select($cut_lay_sql) as $keys=>$vals)
	{
	 	$type_line_wise_arr[0][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["qntys"]+=$vals[csf("qntys")];

	 	$type_line_wise_arr_sizewise[0][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["size_qty"]+=$vals[csf("qntys")];

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	}
	 
	 $size_all_ids=implode(',', $size_all_arr);
	 //$type_name=[1=>"Cutting",4=>"Sewing Input",5=>"Sewing Output",8=>"Finishing & Packing",11=>"Poly",0=>"Cut and Lay"];
	 

	?>
     

    </head>
    <body>
	    <div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
			<script>
	            function new_window()
	            {
	            	$('.fltrow').hide();
	                var w = window.open("Surprise", "#");
	                var d = w.document.open();
	                d.write(document.getElementById('details_reports').innerHTML);
	                d.close();
	                $('.fltrow').show();
	            }
	        </script>
	    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
	    </div>
	    <?
	    ob_start();
		?>
        <div id="details_reports" align="center" style="width:100%;" >
            
            <?
            ksort($type_line_wise_arr);
            /*echo "<pre>";
            print_r($type_line_wise_arr);die;*/
            $total_type=0;
            $production_type[0]="Cut and Lay";
            foreach($type_line_wise_arr as $type_id=>$date_data)
            {

             	$total_type++;
             	$p=0;
             	?>
             	<table width="620" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" style="padding-top: 15px;">
             		<caption> <strong><? echo $production_type[$type_id];?></strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Date</th>
             				<th width="90">Color</th>
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Prod. Qty.</th>
             				<th width="80">Remarks</th>
             				<? if($type_id!=1 && $type_id !=0)
             				{
             					?>
             					<th width="100">Floor</th>
             					<th width="80">Line</th>

             					<?

             				}
             				?>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table id="table_body<? echo $total_type;?>" width="620" border="1" rules="all" class="rpt_table" align="center">
             	<tbody>

             	<?
             	$size_wise_qty = array();
             	$total_prod_qty = 0;
             	foreach($date_data as $date_id=>$floor_data)
             	{
             		foreach($floor_data as $floor_id=>$line_data)
             		{
             			foreach($line_data as $line_id=>$color_data)
             			{
             				foreach($color_data as $color_id=>$rows)
             				{
             					$p++;
             					?>


             					<tr>                	 
             						<td align="center" width="30" ><? echo $p;?></td>
             						<td align="center"  width="90"><? echo change_date_format($date_id);?></td>
             						<td align="center"  width="90"><? echo $colorarr[$color_id] ;?></td>
             						<?
             						foreach($size_all_arr as $key=>$val)
             						{
             							?>
             							<td align="right"  width="45"><? echo $col_size_qty = $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"] ;?></td>

             							<?
             							$total_prod_qty += $col_size_qty;
             							$size_wise_qty[$key] += $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"];
             						}

             						?>

             						<td align="right"  width="80"><? echo $rows["qntys"];?></td>
             						<td align="center"  width="80"><? echo $rows["remarks"];?></td>
             						<? if($type_id!=1 && $type_id !=0)
             						{
             							?>
             							<td align="center"  width="100"><? echo $floor_library[$floor_id]; ?></td>
             							<td align="center"  width="80">
             								<?
             								$sewing_line='';

             								if($rows['prod_reso_allo']==1)
             								{
             									$line_number=explode(",",$prod_reso_arr[$line_id]);
             									foreach($line_number as $line_val)
             									{
             										if($sewing_line=='') $sewing_line=$sewing_line_library[$line_val]; else $sewing_line.=",".$sewing_line_library[$line_val];
             									}
             								}
             								else 
             								{
             									$sewing_line=$sewing_line_library[$line_id];
             								}
             								echo $sewing_line;

             								?>
             							 	
             							 </td>

             							<?

             						}
             						?>
             					</tr>           



             					<?

             				}
             				

             			}

             		}


             	}
             	?>
             	</tbody>
             	<!-- ================================ For Total ============================== -->
             	<tfoot>
             		<tr class="tbl_bottom">                	 
         				<th colspan="3" width="30" align="right" >Total </th>
         				<?
         				foreach($size_all_arr as $key=>$val)
         				{
         					?>
         					<th align="right" width="45"><? echo $size_wise_qty[$key];?></th>

         					<?
         				}

         				?>

         				<th width="80" align="right"><? echo number_format($total_prod_qty,0); ?></th>
         				<th width="80"></th>
         				<? if($type_id!=1 && $type_id !=0)
         				{
         					?>
         					<th width="100"></th>
         					<th width="80"></th>

         					<?

         				}
         				?>
         			</tr> 
             	</tfoot>
             		              		 
             	</table>
             	</div>
             		
             	<?
             
         	}

            ?>

                 <script> 
                 var total_type='<? echo $total_type;?>';
                 for(i=1;i<=total_type;i++)
                 {
                 	setFilterGrid("table_body"+i,-1);
                 }
                 
                  </script>
          
        </div>
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
			$(document).ready(function(e) 
			{
				document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
			});	
		</script>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}

if($action=="remarks_popup")
{
	extract($_REQUEST);
	list($po,$item,$country,$color)=explode('**', $data);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


	 $production_sql="SELECT a.production_date,c.id,c.color_number_id,c.size_number_id,a.sewing_line,a.floor_id,a.production_type,a.remarks,a.prod_reso_allo,sum(b.production_qnty) as qntys from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id='$po' and a.item_number_id='$item' and a.country_id='$country' and c.color_number_id='$color' group by a.production_date,c.id,c.color_number_id,c.size_number_id,a.sewing_line,a.floor_id,a.production_type,a.remarks,a.prod_reso_allo order by a.production_date";
	 //echo $production_sql;
	 $type_line_wise_arr=array();
	 $size_all_arr=array();
	foreach(sql_select($production_sql) as $keys=>$vals)
	{
	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["qntys"]+=$vals[csf("qntys")];

	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["prod_reso_allo"]=$vals[csf("prod_reso_allo")];

	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["remarks"]=$vals[csf("remarks")];

	 	$type_line_wise_arr_sizewise[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["size_qty"]+=$vals[csf("qntys")];

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	}
	 $cut_lay_sql="SELECT a.entry_date as production_date,b.color_id as color_number_id ,c.size_id as size_number_id ,sum(c.size_qty) as qntys,0 as floor_id,0 as  sewing_line  from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b ,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 and c.order_id='$po' and b.color_id='$color' and b.gmt_item_id ='$item' and c.country_id='$country'  group by  a.entry_date,b.color_id,c.size_id order by c.size_id";
	 //echo $cut_lay_sql;
	foreach(sql_select($cut_lay_sql) as $keys=>$vals)
	{
	 	$type_line_wise_arr[0][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["qntys"]+=$vals[csf("qntys")];

	 	$type_line_wise_arr_sizewise[0][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["size_qty"]+=$vals[csf("qntys")];

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	}
	 
	 $size_all_ids=implode(',', $size_all_arr);


	 //$type_name=[1=>"Cutting",4=>"Sewing Input",5=>"Sewing Output",8=>"Finishing & Packing",11=>"Poly",0=>"Cut and Lay"];
	 

	?>
     

    </head>
    <body>
	    <div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
			<script>
	            function new_window()
	            {
	            	$('.fltrow').hide();
	                var w = window.open("Surprise", "#");
	                var d = w.document.open();
	                d.write(document.getElementById('details_reports').innerHTML);
	                d.close();
	                $('.fltrow').show();
	            }
	        </script>
	    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
	    </div>
	    <?
	    ob_start();
		?>
        <div id="details_reports" align="center" style="width:100%;" >
            
            <?
            ksort($type_line_wise_arr);
            /*echo "<pre>";
            print_r($type_line_wise_arr);die;*/
            $total_type=0;
            $production_type[0]="Cut and Lay";
			
            foreach($type_line_wise_arr as $type_id=>$date_data)
            {
				$tble_width = 0;
				if($type_id!=1 && $type_id !=0)
				{
					$tble_width = 550+(count($size_all_arr)*45);
				}
				else
				{
					$tble_width = 370+(count($size_all_arr)*45);
				}

             	$total_type++;
             	$p=0;
             	?>
                <div style="width:<? echo $tble_width+20;?>px;float: left;">
             	<table width="<? echo $tble_width;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all" style="padding-top: 15px;">
             		<caption> <strong><? echo $production_type[$type_id];?></strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Date</th>
             				<th width="90">Color</th>
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Prod. Qty.</th>
             				<th width="80">Remarks</th>
             				<? if($type_id!=1 && $type_id !=0)
             				{
             					?>
             					<th width="100">Floor</th>
             					<th width="80">Line</th>

             					<?

             				}
             				?>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:<? echo $tble_width*50;?>px; width:<? echo $tble_width+20;?>px;float: left;">
             	<table id="table_body<? echo $total_type;?>" width="<? echo $tble_width;?>" border="1" rules="all" class="rpt_table" align="left">
             	<tbody>

             	<?
             	$size_wise_qty = array();
             	$total_prod_qty = 0;
             	foreach($date_data as $date_id=>$floor_data)
             	{
             		foreach($floor_data as $floor_id=>$line_data)
             		{
             			foreach($line_data as $line_id=>$color_data)
             			{
             				foreach($color_data as $color_id=>$rows)
             				{
             					$p++;
             					?>


             					<tr>                	 
             						<td align="center" width="30" ><? echo $p;?></td>
             						<td align="center"  width="90"><? echo change_date_format($date_id);?></td>
             						<td align="center"  width="90"><? echo $colorarr[$color_id] ;?></td>
             						<?
             						foreach($size_all_arr as $key=>$val)
             						{
             							?>
             							<td align="right"  width="45"><? echo $col_size_qty = $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"] ;?></td>

             							<?
             							$total_prod_qty += $col_size_qty;
             							$size_wise_qty[$key] += $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"];
             						}

             						?>

             						<td align="right"  width="80"><? echo $rows["qntys"];?></td>
             						<td align="center"  width="80"><? echo $rows["remarks"];?></td>
             						<? if($type_id!=1 && $type_id !=0)
             						{
             							?>
             							<td align="center"  width="100"><? echo $floor_library[$floor_id]; ?></td>
             							<td align="center"  width="80">
             								<?
             								$sewing_line='';

             								if($rows['prod_reso_allo']==1)
             								{
             									$line_number=explode(",",$prod_reso_arr[$line_id]);
             									foreach($line_number as $line_val)
             									{
             										if($sewing_line=='') $sewing_line=$sewing_line_library[$line_val]; else $sewing_line.=",".$sewing_line_library[$line_val];
             									}
             								}
             								else 
             								{
             									$sewing_line=$sewing_line_library[$line_id];
             								}
             								echo $sewing_line;

             								?>
             							 	
             							 </td>

             							<?

             						}
             						?>
             					</tr>           



             					<?

             				}
             				

             			}

             		}


             	}
             	?>
             	</tbody>
             	<!-- ================================ For Total ============================== -->
             	<tfoot>
             		<tr class="tbl_bottom">                	 
         				<th colspan="3" width="30" align="right" >Total </th>
         				<?
         				foreach($size_all_arr as $key=>$val)
         				{
         					?>
         					<th align="right" width="45"><? echo $size_wise_qty[$key];?></th>

         					<?
         				}

         				?>

         				<th width="80" align="right"><? echo number_format($total_prod_qty,0); ?></th>
         				<th width="80"></th>
         				<? if($type_id!=1 && $type_id !=0)
         				{
         					?>
         					<th width="100"></th>
         					<th width="80"></th>

         					<?

         				}
         				?>
         			</tr> 
             	</tfoot>
             		              		 
             	</table>
             	</div>
             		
             	<?
             
         	}

         	$ex_factory_sql="SELECT a.EX_FACTORY_DATE as PRODUCTION_DATE, a.REMARKS, b.PRODUCTION_QNTY as QNTYS,c.color_number_id as COLOR_ID,c.size_number_id as SIZE_ID from PRO_EX_FACTORY_MST a ,PRO_EX_FACTORY_DTLS b, WO_PO_COLOR_SIZE_BREAKDOWN c where a.id=b.mst_id and b.COLOR_SIZE_BREAK_DOWN_ID=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and  c.is_deleted=0 and c.po_break_down_id=$po and c.item_number_id=$item and c.color_number_id=$color and c.country_id=$country  order by c.SIZE_ORDER";
	 			//echo $ex_factory_sql; die;
	 			$ex_factory_array = array();
	 			$size_qty_array = array();
				foreach(sql_select($ex_factory_sql) as $keys=>$vals)
				{
				 	$ex_factory_array[$vals["PRODUCTION_DATE"]][$vals["COLOR_ID"]]['qty']+=$vals["QNTYS"];
				 	$ex_factory_array[$vals["PRODUCTION_DATE"]][$vals["COLOR_ID"]]['remarks'] = $vals["REMARKS"];
				 	$size_qty_array[$vals["SIZE_ID"]]+=$vals["QNTYS"];
				 	$size_all_arr[$vals["SIZE_ID"]]=$vals["SIZE_ID"];
				}
				 
				 $size_all_ids=implode(',', $size_all_arr);
				 //echo "<pre>";
				 //print_r($ex_factory_array);
				 //echo "</pre>";
				 $total_type=0;
				 if(count(sql_select($ex_factory_sql)))
				 {
				 	$tble_width = 0;
					if($type_id!=1 && $type_id !=0)
					{
						$tble_width = 550+(count($size_all_arr)*45);
					}
					else
					{
						$tble_width = 370+(count($size_all_arr)*45);
					}

					$total_type++;
             		$i=0;
				 	?>
				 	<table width="<? echo $tble_width;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all" style="padding-top: 15px;">
             		<caption> <strong>Ex-Factory</strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Date</th>
             				<th width="90">Color</th>
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Prod. Qty.</th>
             				<th width="80">Remarks</th>
             				
             			</tr>           
             		</thead>
             		<tbody>
             			<?php
             			$size_total_qty_arr = array();
             			foreach($ex_factory_array as $date=>$date_data)
             			{
             				foreach($date_data as $color_id=>$row)
             				{
             					// foreach($color_data as $size_id=>$vals)
             					// {
		             				$i++;
		             				?>
		             				<tr>
		             					<td><?php echo $i; ?></td>
		             					<td align="center"><?php echo $date; ?></td>
		             					<td align="center"><?php echo $colorarr[$color_id]; ?></td>
		             					<?
		             						$total_size_qty =0;
		             						foreach($size_all_arr as $key=>$val)
		             						{
		             							?>
		             							<td align="right"  width="45"><? echo $size_qty_array[$key];?></td>

		             							<?
		             							$total_size_qty += $size_qty_array[$key];
		             							$size_total_qty_arr[$key] += $size_qty_array[$key];
		             							//$size_wise_qty[$key] += $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"];
		             						}

		             						?>
		             					<td align="right"><?php echo $total_size_qty; ?></td>
		             					<td align="center"><?php echo $row["remarks"]; ?></td>
		             				</tr>
		             				<?php
		             			// }
		             		}
		             	}
             			?>
             		</tbody>
             		<tfoot>
             			<tr>
             				<th></th>
             				<th></th>
             				<th>Total</th>
             				<?
             				$gr_total = 0;
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45">

             						<? echo $size_total_qty_arr[ $key] ; $gr_total+=$size_total_qty_arr[ $key];?>
             							
             						</th>

             					<?
             				}

             				?>
             				<th><? echo $gr_total;?></th>
             				<th></th>
             			</tr>
             		</tfoot>
             	</table>
				 	<?
				 }

            ?>

                 <script> 
                 var total_type='<? echo $total_type;?>';
                 for(i=1;i<=total_type;i++)
                 {
                 	setFilterGrid("table_body"+i,-1);
                 }
                 
                  </script>
          </div>
        </div>
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
			$(document).ready(function(e) 
			{
				document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
			});	
		</script>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}
 
 if($action=="cutting_sewing_action")
{
	extract($_REQUEST);
	list($po,$item,$cutting,$type,$color)=explode('**', $data);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


	$production_sql="SELECT a.serving_company, c.color_number_id,c.size_number_id,sum(b.production_qnty) as qntys,c.order_quantity  from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id='$po' and a.item_number_id='$item' and b.cut_no='$cutting' and c.color_number_id='$color' and a.production_type='$type' group by  a.serving_company, c.color_number_id,c.size_number_id,c.order_quantity";
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

 if($action=="fab_issue_popup")
{
	extract($_REQUEST);	 
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	 ?>

    </head>
    <body>
        <div align="center" style="width:100%;" >
            
            
             	<table width="660" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Issue To Cutting Info</strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="110">Issue No</th>             				 
             				<th width="90">Challan No</th>             				 
             				<th width="90">Issue Date</th>             				 
             				<th width="90">Batch No</th>           				 
             				<th width="90">Issue Qnty</th>
             				<th width="160">Fabric Description</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="">
             	<table  width="660" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1; 
             	$sqls=sql_select("SELECT a.issue_number,a.issue_date,a.challan_no,b.batch_id,sum(b.issue_qnty) as qnty from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.batch_id='$batch_id' group by a.issue_number,a.issue_date,a.challan_no,b.batch_id");

             	$batch_sql="SELECT a.id, a.batch_no,b.item_description from pro_batch_create_mst a,PRO_BATCH_CREATE_DTLS b  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$batch_id'";
             	foreach(sql_select($batch_sql) as $vals)
             	{
             		$batch_array[$vals[csf("id")]]["batch_no"]=$vals[csf("batch_no")];
             		$batch_array[$vals[csf("id")]]["item_description"]=$vals[csf("item_description")];
             	}
             	$total=0;
             	foreach($sqls as  $keys=> $rows)            		 
             	{
             		
             		?>

             		<tr>                	 
         				<td align="center" width="30" ><? echo $p++;?></td>
         				<td align="center"  width="110"><? echo $rows[csf("issue_number")];?></td>             				 
         				<td align="center"  width="90"><? echo $rows[csf("challan_no")];?></td>             				 
         				<td align="center"  width="90"><? echo $rows[csf("issue_date")];?></td>             				 
         				<td align="center"  width="90"><? echo $batch_array[$rows[csf("batch_id")]]["batch_no"];?></td>           				 
         				<td align="center"  width="90"><? echo $rows[csf("qnty")];?></td>
         				<td align="center"  width="160"><? echo $batch_array[$rows[csf("batch_id")]]["item_description"];?></td>
             			</tr>   
             			 
					<?
					$total+=$rows[csf("qnty")];
				}
				?>   
						<tr bgcolor="#E4E4E4">                	 
             				<td colspan="5" align="right">Total</td>       				 
             				<td  align="center"  width="90"><? echo $total;?></td>
             				<td  align="center"  width="160">&nbsp;</td>
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
?>