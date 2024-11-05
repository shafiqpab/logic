<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
$cut_panel_basis=array(1=>"Order No",2=>"Cut Number",3=>"Bundle Number");
$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');


if ($action=="system_no_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(id)
		{
			$('#hidden_mst_id').val(id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:830px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:820px;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                <thead>
                	<th>Company Name</th>
                    <th>Print Type</th>
                    <th>Enter Challan No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_name; ?>">
                    	<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr class="general">
                	<td>
                            <? 
                            	echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "","" );
                            ?>
                    </td>
                    <td align="center">
                    	<?
							echo create_drop_down( "cbo_embel_type", 160, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "" );  
						?>       
                    </td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 	
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_embel_type').value+'_'+document.getElementById('cbo_company_name').value, 'create_system_search_list_view', 'search_div', 'print_embro_bundle_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                     
                </tr>
           </table>
           <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_system_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	if($data[1]==0) $print_type_cond=""; else $print_type_cond=" and a.embel_type=$data[1]";
	$company_id =$data[2];
	$search_field_cond=" and a.sys_number like '$search_string'";
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql = "select a.id, $year_field, a.sys_number_prefix_num, a.sys_number, a.embel_type, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic from pro_gmts_delivery_mst a where a.production_type=3 and a.embel_name=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $print_type_cond"; 
	//echo $sql;//die;
	$result = sql_select($sql);
	$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
	$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Challan</th>
            <th width="60">Year</th>
            <th width="80">Embel. Type</th>               
            <th width="100">Source</th>
            <th width="110">Embel. Company</th>
            <th width="110">Location</th>
            <th width="100">Floor</th>
            <th>Organic</th>
        </thead>
	</table>
	<div style="width:800px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
					 
                if($row[csf('production_source')]==1)
					$serv_comp=$company_arr[$row[csf('serving_company')]]; 
				else
					$serv_comp=$supllier_arr[$row[csf('serving_company')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $emblishment_print_type[$row[csf('embel_type')]]; ?></p></td>               
                    <td width="100"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location_arr[$row[csf('production_source')]]; ?></p></td>
                    <td width="100"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                    <td><p><? echo $row[csf('organic')]; ?></p></td>
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



if ($action=="challan_no_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	if($cbo_cut_panel_basis==1)
	{
	?> 
	
		<script>
		
			function js_set_value(id)
			{
				$('#hidden_mst_id').val(id);
				parent.emailwindow.hide();
			}
		
		</script>
	
	</head>
	
	<body>
	<div align="center" style="width:830px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:820px;">
			<legend>Enter search words</legend>           
				<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Company Name</th>
						<th>Print Type</th>
						<th>Enter Challan No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_name; ?>">
							<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" value="">  
						</th> 
					</thead>
					<tr class="general">
						  <td align="center">
							   <? 
									echo create_drop_down( "cbo_company_id", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "" );
								?>      
						</td>
						<td align="center">
							<?
								echo create_drop_down( "cbo_embel_type", 160, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "" );  
							?>       
						</td>
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 	
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_embel_type').value+'_'+document.getElementById('cbo_company_id').value+'_'+<? echo $cbo_cut_panel_basis; ?>, 'create_challan_search_list_view', 'search_div', 'print_embro_bundle_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						 </td>
						 
					</tr>
			   </table>
			   <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	}
	else
	{
	?>
	<script>
	
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_bundle_nos').val( id );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			selected_id = new Array();
		}
	
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="searchwofrm"  id="searchwofrm">
            <fieldset style="width:810px;">
            <legend>Enter search words</legend>           
                <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                    <thead>
                    	<th>Company Name</th>
                        <th>Order No</th>
                        <th>Bundle No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">  
                        </th>
                    </thead>
                    <tr class="general">
                    	<td>
                            <? 
                            	echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />	
                        </td> 				
                        <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>  		
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value, 'create_bundle_search_list_view', 'search_div', 'print_embro_bundle_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                         </td>
                    </tr>
               </table>
               <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?	
	}
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_order_no = "%".trim($ex_data[0])."%";
	$company = $ex_data[1];
	$bundle_no = "%".trim($ex_data[2])."%";
	
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$scanned_bundle_arr=return_library_array( "select bundle_no, bundle_no from pro_cut_delivery_color_dtls where production_type=3 and embel_name=1 and status_active=1 and is_deleted=0",'bundle_no','bundle_no');
	
	$sql="select d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, c.production_qnty as qty, e.po_number from  pro_gmts_delivery_mst a,pro_cut_delivery_color_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e where   a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' and c.production_type=2 and c.embel_name=1 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0";
	//echo $sql;die;
	$result = sql_select($sql);

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="80">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="70">Size</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style="width:800px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('bundle_no')]; ?>"/>
						</td>
						<td width="80"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                        <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                        <td width="70"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
						<td width="80"><? echo $row[csf('bundle_no')]; ?></td>
						<td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="720">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?	
	exit();	
}


if($action=="load_mst_data")
{
	//echo $data;die;
 	$ex_data = implode("','",explode(",",$data));
	
	$txt_order_no = "%".trim($ex_data[0])."%";
	
	
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	
	$sql_mst_data=sql_select("select a.sys_number, a.company_id,a.location_id, a.embel_name, a.embel_type, a.serving_company,a.floor_id,a.organic,
	a.production_source
	from  pro_gmts_delivery_mst a,pro_cut_delivery_color_dtls c where   a.id=c.delivery_mst_id and c.bundle_no in ('$ex_data')  and c.production_type=2
	and c.embel_name=1 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
	group by a.sys_number, a.company_id,a.location_id, a.embel_name, a.embel_type, a.serving_company,a.floor_id,a.organic,a.production_source");
	//print_r($sql_mst_data);die;
	foreach($sql_mst_data as $val)
	{
		
		 	if($val[csf('production_source')]==1) {$serv_comp=$company_arr[$val[csf('serving_company')]]; }
			else { $serv_comp=$supllier_arr[$val[csf('serving_company')]];}
			$location=$location_arr[$val[csf('location_id')]];
			$floor=$floor_arr[$val[csf('floor_id')]];
			echo "$('#txt_issue_challan_no').val('".$val[csf('sys_number')]."');\n";
			echo "$('#cbo_embel_type').val(".$val[csf('embel_type')].");\n";
			echo "$('#cbo_company_name').val(".$val[csf('company_id')].");\n";
			echo "$('#cbo_source').val('".$val[csf('production_source')]."');\n";
			echo "$('#txt_embl_company').val('".$serv_comp."');\n";
			echo "$('#txt_embl_company_id').val(".$val[csf('serving_company')].");\n";
			echo "$('#txt_location_name').val('".$location."');\n";
			echo "$('#txt_floor_name').val('".$floor."');\n";
			echo "$('#txt_organic').val('".$val[csf('organic')]."');\n";
			echo "$('#txt_floor_id').val(".$val[csf('floor_id')].");\n";
			echo "$('#txt_location_id').val(".$val[csf('location_id')].");\n";
	}
}


if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	if($data[1]==0) $print_type_cond=""; else $print_type_cond=" and a.embel_type=$data[1]";
	$company_id =$data[2];
	$search_field_cond=" and a.sys_number like '$search_string'";
	$actual_delivery_basis=$data[3];
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if(str_replace("'","",$company_id)==0) { echo "Please Select Company first";die;}
	
	$delivery_basis=return_field_value("cut_panel_delevery","variable_settings_production","company_name=$company_id and variable_list=32 and 
	status_active=1 and is_deleted=0");
    if($actual_delivery_basis!=$delivery_basis) { echo "Receive Basis ".$cut_panel_basis[$actual_delivery_basis]." is not applicable in your setup.";die;}
	
	
	$sql = "select a.id, $year_field, a.sys_number_prefix_num,a.company_id, a.sys_number, a.embel_type, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic from pro_gmts_delivery_mst a where a.production_type=2 and a.embel_name=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $print_type_cond"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Challan</th>
            <th width="60">Year</th>
            <th width="80">Embel. Type</th>               
            <th width="100">Source</th>
            <th width="110">Embel. Company</th>
            <th width="110">Location</th>
            <th width="100">Floor</th>
            <th>Organic</th>
        </thead>
	</table>
	<div style="width:800px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
					 
                if($row[csf('production_source')]==1) {$serv_comp=$company_arr[$row[csf('serving_company')]]; }
					
				else {$serv_comp=$supllier_arr[$row[csf('serving_company')]];}
					
					$location=$location_arr[$row[csf('location_id')]];
					$floor=$floor_arr[$row[csf('floor_id')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('company_id')]."_".$row[csf('production_source')]."_".$row[csf('serving_company')]."_".$row[csf('location_id')]."_".$row[csf('floor_id')]."_".$row[csf('sys_number')]."_".$row[csf('organic')]."_".$row[csf('embel_type')]."_".$serv_comp."_".$location."_".$floor; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $emblishment_print_type[$row[csf('embel_type')]]; ?></p></td>               
                    <td width="100"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location; ?></p></td>
                    <td width="100"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                    <td><p><? echo $row[csf('organic')]; ?></p></td>
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


  
if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("printing_emb_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}

 	exit();
}

if($action=="load_drop_down_embro_issue_source")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	
	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "txt_embl_company_id", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "" );
		}
		else
		{
			echo create_drop_down( "txt_embl_company_id", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", 0, "" );
		}
	}
	else if($data==1)
		echo create_drop_down( "txt_embl_company_id", 180, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", $selected_company, "",0,0 );	
	else
		echo create_drop_down( "txt_embl_company_id", 180, $blank_array,"", 1, "--- Select ---", $selected, "",0 );	
			
	exit();
}


if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		
		function search_populate(str)
		{
			//alert(str); 
			if(str==0) 
			{		
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px" class="text_boxes" id="txt_search_common"	value=""  />';		 
			}
			else if(str==1) 
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px" class="text_boxes" id="txt_search_common"	value="" />';
			}
			else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<?php 
				if($db_type==0)
				{
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where find_in_set($company,tag_company) and status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');
				}
				else
				{
					$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name",'id','buyer_name');
				}			
				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				} 
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select name="txt_search_common" style="width:230px" class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}																																													
		}
	
		function js_set_value(id,item_id,po_qnty,plan_qnty,country_id)
		{
			$("#hidden_mst_id").val(id);
			$("#hidden_grmtItem_id").val(item_id); 
			$("#hidden_po_qnty").val(po_qnty);
			$("#hidden_country_id").val(country_id);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
                    <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                   		 <thead>                	 
                        	<th width="130">Search By</th>
                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                        	<th width="200">Date Range</th>
                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    	</thead>
        				<tr>
                    		<td width="130">  
							<? 
							$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
							echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
  							?>
                    		</td>
                   			<td width="180" align="center" id="search_by_td">				
								<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />			
            				</td>
                    		<td align="center">
                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
					  			<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 		</td> 
            		 		<td align="center">
                     			<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'print_embro_bundle_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" height="40" valign="middle">
					<? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_mst_id">
                    <input type="hidden" id="hidden_grmtItem_id">
                    <input type="hidden" id="hidden_po_qnty">
                    <input type="hidden" id="hidden_country_id">
          		</td>
            </tr>
    	</table> 
        <div style="margin-top:10px" id="search_div"></div>     
    </form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];
	
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";		
 	}
	if($txt_date_from!="" || $txt_date_to!="") 
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}
	
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
		
 	$sql = "select b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_embe_cost_dtls c
			where
			a.job_no = b.job_no_mst and a.job_no = c.job_no and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=$garments_nature and c.emb_name=2
			$sql_cond group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut order by b.id DESC"; 
	//echo $sql;die;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}
	
	$po_country_data_arr=array();
	$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
	
	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	}
	
	$total_issu_qty_data_arr=array();
	$total_issu_qty_arr=sql_select( "select po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=2 group by po_break_down_id, item_number_id, country_id");
	
	foreach($total_issu_qty_arr as $row)
	{
		$total_issu_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}
	
	?>
    <div style="width:1030px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Buyer</th>
                <th width="120">Style</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Total Issue Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1030px; max-height:240px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1012" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";
				
				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$numOfCountry = count($country);
				
				for($k=0;$k<$numOfItem;$k++)								
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}
					
					foreach($country as $country_id)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>');" > 
							<td width="30" align="center"><?php echo $i; ?></td>
							<td width="70" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>		
							<td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
							<td width="100"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>	
							<td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
							<td width="140"><p><?php  echo $garments_item[$grmts_item];?></p></td>	
							<td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
							<td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
                            <td width="80" align="right">
							<?php
								echo $total_cut_qty=$total_issu_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]
                             ?> &nbsp;
                           </td>
                           <td width="80" align="right">
							<?php
                             $balance=$po_qnty-$total_cut_qty;
                             echo $balance;
                             ?>&nbsp;
                           </td>
							<td><?php  echo $company_arr[$row[csf("company_name")]];?> </td> 	
						</tr>
						<? 
						$i++;
					}
				}
            }
   		?>
        </table>
    </div> 
<?	
	exit();	
}

if($action=="check_bundle_data")
{
	$dataArr = explode("**",$data);
	$chanal_no = $dataArr[0];
	$cbo_cut_panel_basis = $dataArr[1];

	$sql = sql_select("select a.id, a.sys_number_prefix_num,a.company_id, a.sys_number, a.embel_type, a.production_source, a.serving_company,
	a.location_id, a.floor_id, a.organic from pro_gmts_delivery_mst a where a.production_type=2 and a.embel_name=1 and a.status_active=1 and 
	a.is_deleted=0 and a.sys_number='$chanal_no' "); 

	$delivery_basis=return_field_value("cut_panel_delevery","variable_settings_production","company_name=".$sql[0][csf('company_id')]." and variable_list=32
	and status_active=1 and is_deleted=0");

	if($cbo_cut_panel_basis!=$delivery_basis)
	{ 
	$return_data= "0**Receive Basis ".$cut_panel_basis[$cbo_cut_panel_basis]." is not applicable in your setup.";
	echo   $return_data;die;
	}
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=".$sql[0][csf('company_id')]." and variable_list=1 and status_active=1");
	if($sql[0][csf("production_source")]==1) {$serv_comp=$company_arr[$sql[0][csf("production_source")]]; }
	else {$serv_comp=$supllier_arr[$sql[0][csf("production_source")]];}
	$location=$location_arr[$sql[0][csf("location_id")]];
	$floor=$floor_arr[$sql[0][csf("floor_id")]];
	
	$return_data="1**".$sql_result[0][csf("printing_emb_production")]."**".$sql_result[0][csf("production_entry")]."**".$sql[0][csf("id")]."**".$sql[0][csf("company_id")]."**".$sql[0][csf("sys_number")]."**".$sql[0][csf("embel_type")]."**".$sql[0][csf("production_source")]."**".$sql[0][csf("serving_company")]."**".$sql[0][csf("location_id")]."**".$sql[0][csf("floor_id")]."**".$floor."**".$location."**".$serv_comp."**".$sql[0][csf("organic")];
 
	echo  $return_data;
 	exit();	
}




if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$embel_name = $dataArr[2];
	$country_id = $dataArr[3];

	$res = sql_select("select a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name
			from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c
			where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id=$po_id and c.emb_name=$embel_name group by a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name"); 
	 
 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		 		
  		$dataArray=sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=2 and embel_name='$embel_name' THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0");
 		foreach($dataArray as $row)
		{ 
 			echo "$('#txt_cutting_qty').val('".$row[csf('totalcutting')]."');\n";
			echo "$('#txt_cumul_issue_qty').attr('placeholder','".$row[csf('totalprinting')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalprinting')]."');\n";
			$yet_to_produced = $row[csf('totalcutting')]-$row[csf('totalprinting')];
			echo "$('#txt_yet_to_issue').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
		}
  	}
 	exit();	
}

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
?>
	<script>
	
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_bundle_nos').val( id );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			selected_id = new Array();
		}
	
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:810px;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Order No</th>
                    <th>Bundle No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">  
                    </th>
                </thead>
                <tr class="general">
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />	
                    </td> 				
                    <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>  		
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value, 'create_bundle_search_list_view', 'search_div', 'print_embro_bundle_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_order_no = "%".trim($ex_data[0])."%";
	$company = $ex_data[1];
	$bundle_no = "%".trim($ex_data[2])."%";
	
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$scanned_bundle_arr=return_library_array( "select bundle_no, bundle_no from pro_cut_delivery_color_dtls where production_type=2 and embel_name=2 and status_active=1 and is_deleted=0",'bundle_no','bundle_no');
	
	$sql="select d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, c.production_qnty as qty, e.po_number from pro_cut_delivery_mst a, pro_cut_delivery_order_dtls b, pro_cut_delivery_color_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e where a.id=b.delivery_mst_id and b.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' and c.production_type=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	//echo $sql;die;
	$result = sql_select($sql);

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="80">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="70">Size</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style="width:800px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('bundle_no')]; ?>"/>
						</td>
						<td width="80"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                        <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                        <td width="70"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
						<td width="80"><? echo $row[csf('bundle_no')]; ?></td>
						<td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="720">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?	
	exit();	
}
if($action=="populate_bundle_data_update")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=explode(",",$ex_data[2]);
	$bundle_nos="'".implode("','",$bundle)."'";
	
	//$scanned_bundle_arr=return_library_array( "select bundle_no, bundle_no from pro_cut_delivery_color_dtls where production_type=3 and embel_name=1 and status_active=1 and is_deleted=0",'bundle_no','bundle_no');
	
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	
	$year_field="";
	if($db_type==0) 
	{
		$year_field="YEAR(f.insert_date)"; 
	}
	else if($db_type==2) 
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}
	 
	$sql="select d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, c.production_qnty as qty, e.po_number from pro_cut_delivery_color_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.bundle_no in ($bundle_nos) and c.production_type=3 and embel_name=1 and c.status_active=1 and c.is_deleted=0";
	//echo $sql;//die;
	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{ 
		if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>"> 
            	<td width="30"><? echo $i; ?></td>
                <td width="90" id="bundle_<? echo $i; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>
                <td id="button_1" align="center">
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"/>
                </td>
			</tr>
		<?
        	$i--;
		}
	}
	exit();	
}
if($action=="populate_bundle_data")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=explode(",",$ex_data[2]);
	$bundle_nos="'".implode("','",$bundle)."'";
	
	$scanned_bundle_arr=return_library_array( "select bundle_no, bundle_no from pro_cut_delivery_color_dtls where production_type=3 and embel_name=1 and status_active=1 and is_deleted=0",'bundle_no','bundle_no');
	
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	
	$year_field="";
	if($db_type==0) 
	{
		$year_field="YEAR(f.insert_date)"; 
	}
	else if($db_type==2) 
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}
	 
	$sql="select d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, c.production_qnty as qty, e.po_number from pro_cut_delivery_color_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.bundle_no in ($bundle_nos) and c.production_type=2 and embel_name=1 and c.status_active=1 and c.is_deleted=0";
	//echo $sql;//die;
	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{ 
		if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>"> 
            	<td width="30"><? echo $i; ?></td>
                <td width="90" id="bundle_<? echo $i; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>
                <td id="button_1" align="center">
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"/>
                </td>
			</tr>
		<?
        	$i--;
		}
	}
	exit();	
}

if($action=="bundle_nos")
{
	if($db_type==0) 
	{
		$bundle_nos=return_field_value("group_concat(b.bundle_no order by b.id desc) as bundle_no", "pro_garments_production_dtls a, pro_cut_delivery_color_dtls b","a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=3 and b.embel_name=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no');
	}
	else if($db_type==2) 
	{
	
		$bundle_nos=return_field_value("LISTAGG(b.bundle_no, ',') WITHIN GROUP (ORDER BY b.id desc) as bundle_no", "pro_garments_production_dtls a, pro_cut_delivery_color_dtls b","a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=3 and b.embel_name=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no');
	}
	echo $bundle_nos;
	exit();
}

if($action=="color_and_size_level")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$styleOrOrderWisw = $dataArr[3];
	$embelName = $dataArr[4];
	$country_id = $dataArr[5];
	
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	//#############################################################################################//
	//order wise - color level, color and size level
	
	//$variableSettings=2;
	
	if( $variableSettings==2 ) // color level
	{
		if($db_type==0)
		{
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
		}
		else
		{
			$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN c.production_type=2 and c.embel_name='$embelName' then b.production_qnty ELSE 0 END) as cur_production_qnty
					from wo_po_color_size_breakdown a 
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					left join pro_garments_production_mst c on c.id=b.mst_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";	
			
		}
		
		$colorResult = sql_select($sql);
	}
	else if( $variableSettings==3 ) //color and size level
	{
		
		/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
			
			
			
		$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");	
										
		foreach($dtlsData as $row)
		{				  
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
		}  
		//print_r($color_size_qnty_array);
			
		$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, id";
			
		$colorResult = sql_select($sql);
	}
/*	else // by default color and size level
	{
		$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where  mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
	}
*/	
	//$colorResult = sql_select($sql);		
	//print_r($sql);
	$colorHTML="";
	$colorID='';
	$chkColor = array(); 
	$i=0;$totalQnty=0;
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{ 
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';				
			$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
			$colorID .= $color[csf("color_number_id")].",";
		}
		else //color and size level
		{
			if( !in_array( $color[csf("color_number_id")], $chkColor ) )
			{
				if( $i!=0 ) $colorHTML .= "</table></div>";
				$i=0;
				$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
				$chkColor[] = $color[csf("color_number_id")];					
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
			
			$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
			$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
			
			
			
			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';				
		}
		$i++; 
	}
	//echo $colorHTML;die; 
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="show_dtls_listview")
{
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	if($db_type==2) $insert_year="to_char(a.insert_date,'YYYY') as year";
	$order_sql="select a.job_no_prefix_num, a.buyer_name, b.id, b.po_number, b.po_quantity,$insert_year from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['year']=$row[csf('year')];
	}

?>	
    <div style="width:100%;">
        <table cellpadding="0" width="920" cellspacing="0" border="1" class="rpt_table" rules="all">
           <thead>
                <th width="40">SL</th>
                <th width="120">Order No</th>
                <th width="60">Year</th>
                <th width="70">Job No</th>
                <th width="85">Buyer</th>
                <th width="140">Gmts. Item</th>
                <th width="140">Country</th>
                <th width="100">Production Qty.</th>
                <th align="center">Challan No</th>
                <th></th>
            </thead 
        ></table>
    </div>
	<div style="width:100%;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
		<?php  
			$i=1;	
			$total_production_qnty=0;
			$sqlResult =sql_select("select id,po_break_down_id,item_number_id,country_id,production_date,production_quantity,production_source,serving_company,location,challan_no from pro_garments_production_mst where delivery_mst_id='$data' and status_active=1 and is_deleted=0 order by id");
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
               	$total_production_qnty+=$selectResult[csf('production_quantity')]; 	
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>"  > 
                    <td width="40" align="center"><? echo $i; ?><input type="checkbox" id="check_row_<? echo $i; ?>" name="check_row[]" checked /></td>
                    
                    <td width="120" align="center" style=" word-break:break-all"><p><? echo $order_array[$selectResult[csf('po_break_down_id')]]['po_number']; ?></p></td>
                    <td width="60" align="center" style=" word-break:break-all"><p><? echo $order_array[$selectResult[csf('po_break_down_id')]]['year']; ?></p></td>
                    <td width="70" align="center" style=" word-break:break-all"><?php echo $order_array[$selectResult[csf('po_break_down_id')]]['job_no']; ?></td>
                    <td width="85" align="center" style=" word-break:break-all"><?php echo $buyer_arr[$order_array[$selectResult[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                    <td width="140" align="center" style=" word-break:break-all"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="140" align="center" style=" word-break:break-all"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                   
                    <td width="100" align="center" style=" word-break:break-all"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
                    <td align="center"><p><?php echo $selectResult[csf('challan_no')]; ?></p></td>
                </tr>
            <?php
                $i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=="show_country_listview")
{
?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="110">Item Name</th>
            <th width="80">Country</th>
            <th width="75">Shipment Date</th>
            <th>Order Qty.</th>                    
        </thead>
		<?  
		$i=1;
		
		$sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);"> 
				<td width="30"><? echo $i; ?></td>
				<td width="110"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="75" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right"><?php  echo $row[csf('order_qnty')]; ?></td>
			</tr>
		<?	
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="populate_issue_form_data")
{
	//production type=2 come from array
	$sqlResult =sql_select("select id,garments_nature,challan_no,po_break_down_id,item_number_id,country_id,production_source,serving_company,location,embel_name,embel_type,production_date,production_quantity,production_source,production_type,entry_break_down_type,production_hour,sewing_line,supervisor,carton_qty,remarks,floor_id,alter_qnty,reject_qnty,total_produced,yet_to_produced from pro_garments_production_mst where id='$data' and production_type='2' and status_active=1 and is_deleted=0 order by id");
  	//echo "sdfds".$sqlResult;die;
	foreach($sqlResult as $result)
	{ 
		//echo "$('#txt_issue_date').val('".change_date_format($result[csf('production_date')])."');\n";
  		echo "$('#txt_issue_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
		echo "$('#txt_iss_id').val('".$result[csf('id')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		
			$dataArray=sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=2 and embel_name=".$result[csf('embel_name')]." THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and is_deleted=0");
 		foreach($dataArray as $row)
		{ 
 			echo "$('#txt_cutting_qty').val('".$row[csf('totalCutting')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalPrinting')]."');\n";
			$yet_to_produced = $row[csf('totalCutting')]-$row[csf('totalPrinting')];
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
		}
		
		echo "get_php_form_data(".$result[csf('po_break_down_id')]."+'**'+".$result[csf("item_number_id")]."+'**'+".$result[csf("embel_name")]."+'**'+".$result[csf("country_id")].", 'populate_data_from_search_popup', 'requires/print_embro_bundle_receive_controller' );\n";

		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		
		echo "show_list_view('".$result[csf('po_break_down_id')]."','show_country_listview','list_view_country','requires/print_embro_bundle_receive_controller','');\n";
		
		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);\n";
		
		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		if( $variableSettings!=1 ) // gross level
		{ 
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];
			
			$sql_dtls = sql_select("select color_size_break_down_id,production_qnty,size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");	
			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
			}  
			
			//$variableSettings=2;
			
			
			 
			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
					
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
				}
				else
				{
					$sql="select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as cur_production_qnty
					from wo_po_color_size_breakdown a 
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					left join pro_garments_production_mst c on c.id=b.mst_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";
					
				}
			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
					
					
				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");	
										
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				}  
				//print_r($color_size_qnty_array);
					
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id";
					
			}
			else // by default color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
					
					$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");	
										
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				}  
				//print_r($color_size_qnty_array);
					
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id";
			}
 
 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array(); 
			$i=0;$totalQnty=0;$colorWiseTotal=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{  
					$amount = $amountArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';				
					$totalQnty += $amount;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$amount = $amountArr[$index];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					
					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					
					
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ></td></tr>';				
					$colorWiseTotal += $amount;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}//end if condtion
		//#############################################################################################//
	}
 	exit();		
}

//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
		
		if(str_replace("'","",$txt_system_id)=="")
		{
			$mst_id=return_next_id("id", "pro_gmts_delivery_mst", 1);
			
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="extract(year from insert_date)";
			else $year_cond="";//defined Later
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'EDE', date("Y",time()), 5, "select sys_number_prefix,
			sys_number_prefix_num from pro_gmts_delivery_mst where company_id=$cbo_company_name and production_type='3' and embel_name=$cbo_embel_name and 
			$year_cond=".date('Y',time())." order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));

			$field_array_delivery="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type, production_source, serving_company, floor_id, organic, delivery_date, inserted_by, insert_date";
			$data_array_delivery="(".$mst_id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."', ".$cbo_company_name.",3,".$txt_location_id.",".$cbo_cut_panel_basis.",".$cbo_embel_name.",".$cbo_embel_type.",".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$txt_organic.",".$txt_issue_date.",".$user_id.",'".$pc_date_time."')";
			$challan_no=$new_sys_number[2];
			$txt_challan_no=$new_sys_number[0];
		}
		else
		{
			$mst_id=str_replace("'","",$txt_system_id);
			$txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
			$challan_no=(int) $txt_chal_no[3];
			
			$field_array_delivery="company_id*location_id*delivery_basis*embel_name*embel_type*production_source*serving_company*floor_id*organic*delivery_date*updated_by*update_date";
			$data_array_delivery="".$cbo_company_name."*".$cbo_location."*".$cbo_cut_panel_basis."*".$cbo_embel_name."*".$cbo_embel_type."*".$cbo_source."*".$txt_embl_company_id."*".$txt_floor_id."*".$txt_organic."*".$txt_issue_date."*".$user_id."*'".$pc_date_time."'";
			
		}
 		
		if(str_replace("'","",$cbo_cut_panel_basis)==3)
		{
			$id=return_next_id("id", "pro_garments_production_mst", 1);
			$field_array_mst="id, delivery_mst_id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, floor_id, inserted_by, insert_date";

			$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); $colorSizeIdArr=array();
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$bundleNo="bundleNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorId="colorId_".$j;
				$sizeId="sizeId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$qty="qty_".$j;
				
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$colorSizeArr[$$colorSizeId]=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$colorSizeId]+=$$qty;
			}
			
			foreach($mstArr as $orderId=>$orderData)
			{
				foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
				{
					foreach($gmtsItemIdData as $countryId=>$qty)
					{
						if($data_array_mst!="") $data_array_mst.=",";
						$data_array_mst.="(".$id.",".$mst_id.",".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.", ".$gmtsItemId.",".$countryId.", ".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$qty.",3,".$sewing_production_variable.",".$txt_floor_id.",".$user_id.",'".$pc_date_time."')";
						$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
						$id = $id+1;
					}
				}
			}
			
			$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$field_array_dtls="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty";
			
			foreach($dtlsArr as $colorSizeId=>$qty)
			{
				$colorSizedData=explode("**",$colorSizeArr[$colorSizeId]);
				$gmtsMstId=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",3,'".$colorSizeId."','".$qty."')";
				$colorSizeIdArr[$colorSizeId]=$dtls_id;
				$dtls_id = $dtls_id+1;
			}
			
			$bundle_id=return_next_id("id", "pro_cut_delivery_color_dtls", 1);
			$field_array_bundle="id,production_type,embel_name,delivery_mst_id,mst_id,color_size_break_down_id,production_qnty,bundle_no";
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$bundleNo="bundleNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorId="colorId_".$j;
				$sizeId="sizeId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$qty="qty_".$j;
				
				$dtlsId=$colorSizeIdArr[$$colorSizeId];
				
				if($data_array_bundle!="") $data_array_bundle.=",";
				$data_array_bundle.= "(".$bundle_id.",3,".$cbo_embel_name.",".$mst_id.",".$dtlsId.",'".$$colorSizeId."','".$$qty."','".$$bundleNo."')";
				$bundle_id = $bundle_id+1;
			}
			
			if(str_replace("'","",$txt_system_id)=="")
			{
				$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			}
			else
			{
				$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			}
			
			$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
			$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
		
			//echo "10**insert into pro_garments_production_mst (".$field_array_mst.") values ".$data_array_mst;die;
			//echo $challanrID ."&&". $rID ."&&". $dtlsrID ."&&". $bundlerID;die;
			//release lock table
			check_table_status( $_SESSION['menu_id'],0);
		
			if($db_type==0)
			{  
				if($challanrID && $rID && $dtlsrID && $bundlerID)
				{
					mysql_query("COMMIT");  
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($challanrID && $rID && $dtlsrID && $bundlerID)
				{
					oci_commit($con); 
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		else
		{
			$id=return_next_id("id", "pro_garments_production_mst", 1);
			$field_array1="id, delivery_mst_id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";
			
			$data_array1="(".$id.",".$mst_id.",".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$hidden_po_break_down_id.", ".$cbo_item_name.",".$cbo_country_name.", ".$cbo_source.",".$txt_embl_company_id.",".$cbo_location.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$txt_issue_qty.",2,".$sewing_production_variable.",".$txt_remark.",".$txt_floor_id.",".$user_id.",'".$pc_date_time."')";
			
			//echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;
			// pro_garments_production_dtls table entry here ----------------------------------///
			$field_array="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty";
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue); 
				$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					//2 for Issue to Print / Emb Entry
					if($j==0)$data_array = "(".$dtls_id.",".$mst_id.",".$id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					else $data_array .= ",(".$dtls_id.",".$mst_id.",".$id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					$dtls_id=$dtls_id+1;							
					$j++;								
				}
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf('id')];
				}
					
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				$rowEx = explode("***",$colorIDvalue); 
				$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];				
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$colorID;
					
					//2 for Issue to Print / Emb Entry
					if($j==0)$data_array = "(".$dtls_id.",".$mst_id.",".$id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					else $data_array .= ",(".$dtls_id.",".$mst_id.",".$id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					$dtls_id=$dtls_id+1;
					$j++;
				}
			}	
		//echo "10** insert into pro_garments_production_mst($field_array1)values".$data_array1;die;
			$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
			if(str_replace("'","",$txt_system_id)=="")
			{
				$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			}
			else
			{
				$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			}
			
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
			{	 
				$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			} 
		
			check_table_status( $_SESSION['menu_id'],0);
		//echo "10**".$rID."**".$challanrID."**".$dtlsrID."**".$challanrID;die;
			if($db_type==0)
			{  
				if($rID && $challanrID && $dtlsrID)
				{
					mysql_query("COMMIT");  
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($rID && $challanrID && $dtlsrID)
				{
					oci_commit($con); 
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
		$mst_id=str_replace("'","",$txt_system_id);
		$txt_chal_no=str_replace("'","",$txt_challan_no);
		$challan_no=(int) $txt_chal_no[3];

		$field_array_delivery="company_id*location_id*embel_name*embel_type*production_source*serving_company*floor_id*organic*delivery_date*updated_by*update_date";
		$data_array_delivery="".$cbo_company_name."*".$txt_location_id."*".$cbo_embel_name."*".$cbo_embel_type."*".$cbo_source."*".$txt_embl_company_id."*".$txt_floor_id."*".$txt_organic."*".$txt_issue_date."*".$user_id."*'".$pc_date_time."'";
		
		if(str_replace("'","",$cbo_cut_panel_basis)==3)
		{
			$delete = execute_query("DELETE FROM pro_garments_production_mst WHERE delivery_mst_id=$mst_id and production_type=3 and embel_name=1");
			$delete_dtls = execute_query("DELETE FROM pro_garments_production_dtls WHERE delivery_mst_id=$mst_id and production_type=3");
			$delete_bundle = execute_query("DELETE FROM pro_cut_delivery_color_dtls WHERE delivery_mst_id=$mst_id and production_type=3 and embel_name=1");
			
			$id=return_next_id("id", "pro_garments_production_mst", 1);
			$field_array_mst="id, delivery_mst_id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";

			$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); $colorSizeIdArr=array();
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$bundleNo="bundleNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorId="colorId_".$j;
				$sizeId="sizeId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$qty="qty_".$j;
				
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$colorSizeArr[$$colorSizeId]=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$colorSizeId]+=$$qty;
			}
			
			foreach($mstArr as $orderId=>$orderData)
			{
				foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
				{
					foreach($gmtsItemIdData as $countryId=>$qty)
					{
						if($data_array_mst!="") $data_array_mst.=",";
						$data_array_mst.="(".$id.",".$mst_id.",".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.", ".$gmtsItemId.",".$countryId.", ".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$qty.",3,".$sewing_production_variable.",'".$txt_remark."',".$txt_floor_id.",".$user_id.",'".$pc_date_time."')";
						$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
						$id = $id+1;
					}
				}
			}
			
			$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$field_array_dtls="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty";
			
			foreach($dtlsArr as $colorSizeId=>$qty)
			{
				$colorSizedData=explode("**",$colorSizeArr[$colorSizeId]);
				$gmtsMstId=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",2,'".$colorSizeId."','".$qty."')";
				$colorSizeIdArr[$colorSizeId]=$dtls_id;
				$dtls_id = $dtls_id+1;
			}
			
			$bundle_id=return_next_id("id", "pro_cut_delivery_color_dtls", 1);
			$field_array_bundle="id,production_type,embel_name,delivery_mst_id,mst_id,color_size_break_down_id,production_qnty,bundle_no";
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$bundleNo="bundleNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorId="colorId_".$j;
				$sizeId="sizeId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$qty="qty_".$j;
				
				$dtlsId=$colorSizeIdArr[$$colorSizeId];
				
				if($data_array_bundle!="") $data_array_bundle.=",";
				$data_array_bundle.= "(".$bundle_id.",2,".$cbo_embel_name.",".$mst_id.",".$dtlsId.",'".$$colorSizeId."','".$$qty."','".$$bundleNo."')";
				$bundle_id = $bundle_id+1;
			}
			
			$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
			$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
		
			//echo "10**insert into pro_cut_delivery_color_dtls (".$field_array_bundle.") values ".$data_array_bundle;die;
			//echo $challanrID ."&&". $rID ."&&". $dtlsrID ."&&". $bundlerID ."&&". $delete ."&&". $delete_dtls ."&&". $delete_bundle;die;
			//release lock table
			check_table_status( $_SESSION['menu_id'],0);
		
			if($db_type==0)
			{  
				if($challanrID && $rID && $dtlsrID && $bundlerID && $delete && $delete_dtls && $delete_bundle)
				{
					mysql_query("COMMIT");  
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($challanrID && $rID && $dtlsrID && $bundlerID && $delete && $delete_dtls && $delete_bundle)
				{
					oci_commit($con); 
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		else
		{
			// pro_garments_production_mst table data entry here 
			$field_array1="production_source*serving_company*location*embel_name*embel_type*production_date*production_quantity*production_type*entry_break_down_type*challan_no*remarks*floor_id*total_produced*yet_to_produced*updated_by*update_date";
			
			$data_array1="".$cbo_source."*".$txt_embl_company_id."*".$cbo_location."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_issue_date."*".$txt_issue_qty."*2*".$sewing_production_variable."*'".$challan_no."'*".$txt_remark."*".$txt_floor_id."*".$txt_cumul_issue_qty."*".$txt_yet_to_issue."*".$user_id."*'".$pc_date_time."'";
			// pro_garments_production_dtls table data entry here 
			if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' )// check is not gross level
			{
				$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
				$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty";
				
				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{		
					$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id" );
					$colSizeID_arr=array(); 
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}	
					
					// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
					$rowEx = explode("**",$colorIDvalue); 
					$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array="";$j=0;
					foreach($rowEx as $rowE=>$val)
					{
						$colorSizeNumberIDArr = explode("*",$val);
						//2 for Issue to Print / Emb Entry
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
						$dtls_id=$dtls_id+1;							
						$j++;								
					}
				}
				
				if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{		
					$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id" );
					$colSizeID_arr=array(); 
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}	
					
					//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
					$rowEx = explode("***",$colorIDvalue); 
					$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array="";$j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];				
						$colorSizeValue = $colorAndSizeAndValue_arr[2];
						$index = $sizeID.$colorID;
						//2 for Issue to Print / Emb Entry
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
						$dtls_id=$dtls_id+1;
						$j++;
					}
				}
				 
				//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			}//end cond
			
			$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);//echo $rID;die;
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
			{
				$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			}
			
			//release lock table
			check_table_status( $_SESSION['menu_id'],0);
			
			if($db_type==0)
			{
				if($rID && $challanrID && $dtlsrID && $dtlsrDelete)
				{
					mysql_query("COMMIT");  
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $challanrID && $dtlsrID && $dtlsrDelete)
				{
					oci_commit($con); 
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		disconnect($con);
		die;
	}
 
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		$mst_id=str_replace("'","",$txt_system_id);
		
 		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		disconnect($con);
		die;
	}
}



if($action=='populate_data_from_challan_popup')
{
	$data_array=sql_select("select id, company_id, sys_number, embel_type, embel_name, production_source, serving_company, location_id, floor_id, organic, delivery_date from pro_gmts_delivery_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		//echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#cbo_source').val('".$row[csf('production_source')]."');\n";
		
		if($row[csf('production_source')]==1) {$serv_comp=$company_arr[$row[csf('serving_company')]]; }
		else { $serv_comp=$supllier_arr[$val[csf('serving_company')]];}
		$location=$location_arr[$row[csf('location_id')]];
		$floor=$floor_arr[$row[csf('floor_id')]];
		echo "$('#txt_embl_company').val('".$serv_comp."');\n";
		echo "$('#txt_embl_company_id').val('".$row[csf('serving_company')]."');\n";
		echo "$('#txt_location_name').val('".$location."');\n";
		echo "$('#txt_floor_name').val('".$floor."');\n";
		echo "$('#txt_location_id').val('".$row[csf('location_id')]."');\n";
		echo "$('#txt_floor_id').val('".$row[csf('floor_id')]."');\n";
		echo "$('#cbo_embel_name').val('".$row[csf('embel_name')]."');\n";
		echo "$('#cbo_embel_type').val('".$row[csf('embel_type')]."');\n";
		echo "$('#txt_organic').val('".$row[csf('organic')]."');\n";
		echo "$('#txt_system_id').val('".$row[csf('id')]."');\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf('delivery_date')])."');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_issue_print_embroidery_entry',1,1);\n"; 
		//txt_embl_company_id*txt_location_id*txt_floor_id 
		exit();
	}
}

if($action=="emblishment_issue_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$order_array=array();
	$order_sql="select a.job_no, a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
	}
	
	$sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
	production_source, serving_company, floor_id, organic, delivery_date from pro_gmts_delivery_mst where production_type=2 and id='$data[1]' and 
	status_active=1 and is_deleted=0 ";
	$dataArray=sql_select($sql);

?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px"> 
				<?
				
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
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
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="125"><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="125"><strong>Embel. Name :</strong></td><td width="175px"><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="125"><strong>Emb. Type:</strong></td><td width="175px">
			<? 
				if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; 
				elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
			 ?>
            </td>
        </tr>
        <tr>
            <td><strong>Emb. Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Emb. Company:</strong></td><td>
				<? 
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else										   echo $supplier_library[$dataArray[0][csf('serving_company')]];
				 
                ?>
            </td>
            <td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Floor :</strong></td><td><? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Organic :</strong></td><td><? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date :</strong></td><td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
        	<td  colspan="6" id="barcode_img_id"></td>
        	
        </tr>
       
    </table>
         <br>
        <?
		
			$delivery_mst_id =$dataArray[0][csf('id')];
			if($data[2]==3)
			{
				/*$sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id,
				count(d.id) as 	num_of_bundle 
				from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c,pro_cut_delivery_color_dtls d 
				where c.delivery_mst_id ='$data[1]' 
				and c.id=a.mst_id and a.id=d.mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
				and b.is_deleted=0 
				group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ";*/
				
				$sql="SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id, d.color_number_id,
				count(c.id) as 	num_of_bundle 
				from pro_garments_production_mst a, pro_garments_production_dtls b,pro_cut_delivery_color_dtls c,wo_po_color_size_breakdown d 
				where a.delivery_mst_id ='$data[1]' 
				and a.id=b.mst_id and b.id=c.mst_id  and c.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 
				and d.is_deleted=0 
				group by a.po_break_down_id,a.item_number_id,a.country_id,d.color_number_id
				order by a.po_break_down_id,d.color_number_id ";
			}
			else
			{
				$sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id 
				from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' 
				and c.id=a.mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ";
			}
			//echo $sql;die;
			$result=sql_select($sql);
		?> 
         
	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Buyer</th>
            <th width="80" align="center">Job</th>
            <th width="80" align="center">Order No.</th>
            <th width="80" align="center">Gmt. Item</th>
            <th width="80" align="center">Country</th>
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Gmt. Qty</th>
            <? if($data[2]==3)  {  ?>
            <th width="80" align="center">No of Bundle</th>
            <? }   ?>
        </thead>
        <tbody>
			<?
            
            $i=1;
            $tot_qnty=array();
                foreach($result as $val)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                        <td><? echo $i;  ?></td>
                        <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                        <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                        <td align="center"><? echo $country_library[$val[csf('country_id')]]; ?></td>
                        <td align="center"><? echo $color_library[$val[csf('color_number_id')]];?></td>
                        <td align="right"><?  echo $val[csf('production_qnty')]; ?></td>
                        <? if($data[2]==3) 
						 {  ?>
                        <td  align="center"> <?  echo $val[csf('num_of_bundle')]; ?></td>
                        <? 
						$total_bundle+=$val[csf('num_of_bundle')];
						}   
						?>
                        
                    </tr>
                    <?
					$production_quantity+=$val[csf('production_qnty')];
					$i++;
                }
            ?>
        </tbody>
        <tr>
        <? if($data[3]==3) $colspan=8 ; else $colspan=7; ?>
            <td colspan="7" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><?  echo $production_quantity; ?></td>
             <? if($data[2]==3)  {  ?>
            <td  align="center"> <?  echo $total_bundle; ?></td>
            <? }   ?>
        </tr>                           
    </table>
        <br>
		 <?
           // echo signature_table(26, $data[0], "900px");
         ?>
	</div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
		
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
<?
exit();	
}

?>
