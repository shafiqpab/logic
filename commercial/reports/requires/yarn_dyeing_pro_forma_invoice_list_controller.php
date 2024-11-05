<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "SELECT id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
    $data=explode("_",$data);
	// print_r($data);
	if($data[0]==""){

	}
	 else if($data[0]==1)
    {
        echo create_drop_down( "cbo_party_id", 100, "select comp.id, comp.company_name from lib_company comp where  comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Company --", "", "");
    }
    else
    {
        echo create_drop_down( "cbo_party_id", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[1]'and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",'', "" );
    }   
    exit();  
} 

if($action=="pi_no_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
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
				//selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					//alert(selected_id);
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_no.splice( i, 1 ); 
			}

			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				//num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			//num 	= num.substr( 0, num.length - 1 );
			//alert(name);
			$('#hide_pi_id').val( id );
			$('#hide_pi_no').val( name ); 
			//$('#hide_wo_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter PI No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_pi_id" id="hide_pi_id" value="" />
                    <input type="hidden" name="hide_pi_no" id="hide_pi_no" value="" />
                </thead>
                <tbody>
                	<tr>                                      
                        <td align="center">	
                    	<?
                       		//$search_by_arr=array(1=>"Job No",2=>"Style Ref");
                       		$search_by_arr=array(3=>"PI No");
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_pi_no_search_list_view', 'search_div', 'yarn_dyeing_pro_forma_invoice_list_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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

if($action=="create_pi_no_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$search_type,$search_value,$cbo_year,$item_category_id)=explode('**',$data);

	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0){
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}else{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	$sql_cond =" and a.exporter_id=$company";	
	if($search_type==3 && $search_value!=''){
		$sql_cond .=" and a.pi_number like '%$search_value%'";	
	}


	if($db_type==0) $year_field="YEAR(a.insert_date) as job_year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as job_year";
	else $year_field="";

    $sql_pi="SELECT a.id, a.pi_number from com_export_pi_mst a where a.status_active=1 and a.is_deleted=0 $sql_cond order by  a.id desc";

	echo create_list_view("list_view", "PI No, System ID","190,160","400","200",0, $sql_pi , "js_set_value", "id,pi_number", "", 1, "0,0", $arr, "pi_number,id", "","setFilterGrid('list_view',-1)","0","",1) ;

	echo "<input type='hidden' id='hide_pi_id' />";
	echo "<input type='hidden' id='hide_pi_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	//var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}

if($action=="report_generate")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_pro_type=str_replace("'","", $cbo_pro_type);
	$cbo_order_type=str_replace("'","", $cbo_order_type);
	$cbo_party_id=str_replace("'","", $cbo_party_id);
	$cbo_within_group=str_replace("'","", $cbo_within_group);
	$pi_no_id=str_replace("'","", $pi_no_id);
	$cbo_marcendiser=str_replace("'","", $cbo_marcendiser);
	
	$buyer_library=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  ); 
	$team_member_name=return_library_array( "SELECT id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0", "id", "team_member_name"  ); 
	$company_library=return_library_array( "SELECT id,company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name"  );
	$count_name=return_library_array( "SELECT id,yarn_count from lib_yarn_count where status_active =1 and is_deleted=0", "id", "yarn_count"  );
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');


	// $cond="";
	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_id";	

	if($cbo_order_type==0)$order_typ=""; else $order_typ=" and a.order_type=$cbo_order_type";
	if($cbo_party_id==0)$party_typ=""; else $party_typ=" and a.party_id=$cbo_party_id";
	if($cbo_within_group==0)$within_group=""; else $within_group=" and a.within_group=$cbo_within_group";
	if($pi_no_id==0)$pi_id=""; else $pi_id=" and d.id in($pi_no_id)";
	if($cbo_marcendiser==0)$marcadiser=""; else $marcadiser=" and a.team_member =$cbo_marcendiser";

	if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; 
    else $date_con=" and a.receive_date between $txt_date_from and $txt_date_to";

	//============================= MAIN QUERY ================================
    // $sql=" SELECT a.company_id, a.yd_job, a.within_group, a.party_id, c.pi_date, a.order_type, a.team_member, b.count_id, b.yarn_type_id, sum(b.order_quantity) as order_quantity, sum(d.quantity) as quantity, c.pi_number, c.id
  	// from yd_ord_mst a, yd_ord_dtls b, com_export_pi_mst c, com_export_pi_dtls d where  a.id = b.mst_id  AND b.mst_id = d.work_order_id AND d.pi_id = c.id $company_name $date_con $job_cond $party_typ $prod_type $order_typ $within_group $pi_id $marcadiser
	// group by a.company_id, a.yd_job, a.within_group, a.party_id, c.pi_date, a.order_type, a.team_member, b.count_id, b.yarn_type_id, c.pi_number, c.id";


	$sql_adance_data=sql_select("SELECT b.order_quantity, a.yd_job from yd_ord_mst a, yd_ord_dtls b where a.id = b.mst_id $company_name");
	$advance_data=array();
	foreach($sql_adance_data as $row){
		$advance_data[$row[csf('yd_job')]]['order_quantity']=$row[csf('order_quantity')];
	}

	$sql=" SELECT a.company_id,a.advance_job,a.yd_job, a.tag_pi_no, a.within_group, a.party_id,
	a.order_type, a.team_member, b.count_id, b.yarn_type_id, SUM (b.order_quantity) AS order_quantity, c.net_pi_amount, d.pi_number, sum(c.quantity) as quantity,d.pi_date FROM yd_ord_mst a, yd_ord_dtls b, com_export_pi_dtls c, com_export_pi_mst d
   WHERE a.id = b.mst_id
   and b.PI_DTLS_ID=c.id and c.PI_ID=d.id $company_name $date_con $party_typ $order_typ $within_group $pi_id $marcadiser
   GROUP BY a.company_id, a.advance_job, a.tag_pi_no,a.within_group, a.party_id,a.order_type, a.team_member, b.count_id, b.yarn_type_id,c.net_pi_amount,a.yd_job, d.pi_number,d.pi_date";


	// echo $sql;
	$sql_result=sql_select($sql);

	ob_start();
	?>
	<style type="text/css">
		table tr td{word-break: break-all;word-wrap: break-word;}
	</style>
    <div align="left" style="width:1135px;padding-left: 30px;"> 
    
		<table width="1115" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="10" align="center" style="font-size:20px;"><?php echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="10" align="center" style="font-size:14px; font-weight:bold" ><?php echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="10" align="center" style="font-size:14px; font-weight:bold">
							<?php echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
						</td>
					</tr>
				</thead>
			</table>
			
            <table width="1115" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="70">Date</th>
                        <th width="100">PI Number</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Total Quantity</th>
                        <th width="100">Confirm Order Quantity</th>                                
                        <th width="80">Blance Quantity</th>                                
                        <th width="150">Confirm Job Number</th>                                
                        <th width="100">Order Type</th>                                
                        <th width="100">Merchandiser</th>                                                              
                    </tr>
                </thead>
            </table>
           
            <div style="max-height:350px; width:1135px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1115" rules="all" id="table_body" cellpadding="0">
					<tbody>
						<?
						$i=1;
						foreach ($sql_result as $val) 
						{
							$within_group=$val[csf('within_group')];
							if($within_group==1)
							{
								$com_buyer=$company_library[$val[csf('party_id')]];
							}
							else
							{
								$com_buyer=$buyer_arr[$val[csf('party_id')]];
							}
							$qty = $val[csf('quantity')]-$val[csf('order_quantity')];
							
								$bgcolor 	= ($i%2==0)?"#E9F3FF":"#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                        <td align="center" width="35"><? echo $i;?></td>		                       
			                        <td align="center" width="70" align="center"><? echo change_date_format($val[csf('pi_date')]); ?></td>			                        
			                        <td align="center" width="100" align="center"><? echo $val[csf('pi_number')]; ?></td>			                        
			                        <td align="center" width="100" align="center"><? echo $com_buyer; ?></td>			                        
			                        <td align="center" width="100" align="center"><? echo number_format($advance_data[$val[csf('advance_job')]]['order_quantity'],2) ?></td>			                        
			                        <td align="center" width="100" align="center"><? echo number_format($val[csf('order_quantity')],2);  ?></td>			                        
			                        <td align="center" width="80" align="center"><? echo number_format($qty,2); ?></td>			                        
			                        <td align="center" width="150" align="center"><? echo $val[csf("tag_pi_no")]; ?></td>			                        
			                        <td align="center" width="100" align="center"><? echo $w_order_type_arr[$val[csf('order_type')]]; ?></td>			                        
			                        <td align="center" width="100" align="center"><? echo $team_member_name[$val[csf('team_member')]]; ?></td>			                        
			                    </tr>
			                    <?
			                    $i++;			                  
	                	}
	                    ?>
					</tbody>
                </table>
                </div>                              	
	        </div><!-- end main div -->
			<?
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

?>