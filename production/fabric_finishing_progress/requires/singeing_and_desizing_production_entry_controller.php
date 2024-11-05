<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
$machine_name=return_library_array( "select machine_no,id from  lib_machine_name where is_deleted=0", "id", "machine_no"  );
$brand_library=return_library_array( "select id, brand_name from lib_brand where status_active=1", "id", "brand_name");
$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
$brand_name=return_library_array( "select id, brand_name from   lib_brand where status_active=1",'id','brand_name');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}



if($action=="roll_maintained")
{
   $roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name ='$data' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
   
  // $variable_production_roll=return_field_value("fabric_roll_level", "variable_settings_production", "variable_list=3 and item_category_id=2 and status_active=1 and is_deleted=0 and company_name=".$data."");	
   echo "document.getElementById('roll_maintained').value	= '".$roll_maintained."';\n";
   echo "document.getElementById('page_upto').value 		= '".$page_upto_id."';\n";
	
}

if($action=="populate_restenter_from_data") 
{
	$ex_data=explode("_",$data);
		//echo "select a.batch_id,a.re_stenter_no,b.batch_qty,b.production_qty,b.prod_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where  a.id=b.mst_id and a.batch_id=".$ex_data[0]." and a.is_deleted=0 and a.status_active=1 and a.entry_form=33 and a.re_stenter_no=".$ex_data[1]." and b.production_qty>0";
		 $bat_pro=sql_select("select a.id as batch_id,b.batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where  a.id=b.mst_id and a.id=".$ex_data[0]." and a.is_deleted=0 and a.status_active=1  and b.batch_qnty>0");
		$tot_batch_qty=$tot_prod_prev_qty=0;
		 foreach($bat_pro as $row)
		 {
			 //$prod_prev_qty_arr[$row[csf('batch_id')]][$row[csf('re_stenter_no')]]= $row[csf('production_qty')];
			 // $tot_prod_prev_qty+= $row[csf('production_qty')];
			  $tot_batch_qty+= $row[csf('batch_qnty')];
		 }
		  $sql_pro=sql_select("select a.batch_id,a.re_stenter_no,b.batch_qty,b.production_qty,b.prod_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where  a.id=b.mst_id and a.batch_id=".$ex_data[0]." and a.is_deleted=0 and a.status_active=1 and a.entry_form=30 and a.re_stenter_no=".$ex_data[1]." and b.production_qty>0");
		  // echo "select a.batch_id,a.re_stenter_no,b.batch_qty,b.production_qty,b.prod_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where  a.id=b.mst_id and a.batch_id=".$ex_data[0]." and a.is_deleted=0 and a.status_active=1 and a.entry_form=30 and a.re_stenter_no=".$ex_data[1]." and b.production_qty>0";
		//$tot_batch_qty=$tot_prod_prev_qty=0;
		 foreach($sql_pro as $row)
		 {
			 //$prod_prev_qty_arr[$row[csf('batch_id')]][$row[csf('re_stenter_no')]]= $row[csf('production_qty')];
			  $tot_prod_prev_qty+= $row[csf('production_qty')];
			 // $tot_batch_qty+= $row[csf('batch_qty')];
		 }
		// echo "select max(re_stenter_no) as re_stenter_no from pro_fab_subprocess where batch_id=".$ex_data[0]." and is_deleted=0 and status_active=1 and entry_form=30";
		 $re_stenter=return_field_value("max(re_stenter_no) as re_stenter_no"," pro_fab_subprocess","batch_id=".$ex_data[0]." and is_deleted=0 and status_active=1 and entry_form=30","re_stenter_no");
		 //  echo $tot_batch_qty.'='.$tot_prod_prev_qty.'='.$re_stenter.'='.$ex_data[1];
		 
	//  $sql_data=("select batch_id, $grop_con  from pro_fab_subprocess where batch_id=$data and entry_form=33 and status_active=1 and is_deleted=0 group by batch_id");
	//$data_arr=sql_select($sql_data);
	//$re_stenter_no=(explode(",",$data_arr[0][csf("re_stenter_no")]));
	//$stenter_no=end($re_stenter_no);
	//if($stenter_no>0 || $stenter_no==0) $restenter_no=$stenter_no+1; else $restenter_no=0;
	$tot_prod_prev_qty=number_format($tot_prod_prev_qty,2,'.','');
	$tot_batch_qty=number_format($tot_batch_qty,2,'.','');
	
	if($tot_prod_prev_qty>0)
	{
		if($tot_prod_prev_qty>=$tot_batch_qty)
		{
			$restenter_no=$re_stenter+1; 
		} 
		else $restenter_no=$re_stenter;
	} 
	else  $restenter_no=$ex_data[1];
		
	
	
	echo "$('#txt_reslitting_no').val('".$restenter_no."');\n";
	
}



if ($action=="load_drop_floor")
{
	$data=explode('_',$data);
	$loca=$data[0];
	$com=$data[1];
	//echo "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id='$data[0]'  order by floor_name";die;
	echo create_drop_down( "cbo_floor", 135, "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id='$data[0]'  and production_process=4 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected,"load_drop_down( 'requires/singeing_and_desizing_production_entry_controller', document.getElementById('cbo_service_company').value+'**'+this.value, 'load_drop_machine', 'machine_td' );" );     	 
	exit();
}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("**",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_service_company", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", 0, "load_drop_down('requires/singeing_and_desizing_production_entry_controller', this.value, 'load_drop_floor', 'floor_td' );","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_service_company", 135, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_service_company", 135, $blank_array,"",1, "-- Select --", 0, "" );
	}
	exit();
}
if ($action=="load_drop_machine")
{
	$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');
	$data=explode('**',$data);

	$com=$data[0];
	$floor=$data[1];
	if($db_type==2)
	{
	echo create_drop_down( "cbo_machine_name", 135, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=4 and company_id=$com and floor_id=$floor and status_active=1 and is_deleted=0 and is_locked=0 order by machine_name","id,machine_name", 1, "-- Select Machine --", $selected, "get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+this.value, 'populate_data_from_machine', 'requires/singeing_and_desizing_production_entry_controller' );","" );
	}
	else if($db_type==0)
	{
	echo create_drop_down( "cbo_machine_name", 135, "select id,concat(machine_no, '-', brand) as machine_name from lib_machine_name where category_id=4 and company_id=$com and floor_id=$floor and status_active=1 and is_deleted=0 and is_locked=0 order by machine_name","id,machine_name", 1, "-- Select Machine --", $selected, "get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+this.value, 'populate_data_from_machine', 'requires/singeing_and_desizing_production_entry_controller' );","" );
	}
	exit();
	
}

if ($action=="populate_data_from_machine")
{ 
	$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');
	$ex_data=explode('**',$data);
	 $sql_res="select id, floor_id, machine_group from lib_machine_name where id=$ex_data[2] and category_id=4 and company_id=$ex_data[0] and  floor_id=$ex_data[1] and status_active=1 and is_deleted=0";
	$nameArray=sql_select($sql_res);
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_machine_no').value 			= '".$floor_arr[$row[csf("floor_id")]]."';\n";
		echo "document.getElementById('txt_mc_group').value 			= '".$row[csf("machine_group")]."';\n";
	}
	exit();
}

if($action=="populate_data_from_data")
{
	$sql = "select id, company_id, recv_number_prefix_num, dyeing_source, dyeing_company, receive_date, batch_id, process_id from inv_receive_mas_batchroll where id=$data and entry_form=63";
	//echo $sql;
	if($db_type==2) $group_concat="listagg(roll_id ,',') within group (order by roll_id) as roll_id ";
	else if($db_type==0) $group_concat="group_concat(roll_id)  as roll_id ";
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#txt_issue_chalan').val('".$row[csf("recv_number_prefix_num")]."');\n";
		echo "$('#cbo_service_source').val(".$row[csf("dyeing_source")].");\n";
		echo "load_drop_down( 'requires/heat_setting_controller', ".$row[csf("dyeing_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_service_company').val(".$row[csf("dyeing_company")].");\n";
		$roll_id_concat = return_field_value("$group_concat","pro_grey_batch_dtls","mst_id='".$data."'","roll_id");
		$all_roll_concat=implode(",",array_unique(explode(",",$roll_id_concat))); 
		echo "$('#txt_roll_id').val('".$all_roll_concat."');\n";
		echo "$('#hidden_batch_id').val(".$row[csf("batch_id")].");\n";
		echo "$('#txt_issue_mst_id').val(".$row[csf("id")].");\n";
  	}
	exit();	
}
///Issue Challan POPUP Start
if ($action=="issue_challan_popup")
{

	echo load_html_head_contents("Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_company_id;die;
	?> 

	<script>
	
		function js_set_value(id)
		{
			$('#hidden_system_id').val(id);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
	<div align="center" style="width:760px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:760px; margin-left:2px">
	            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
	                <thead>
	                    <th>Issue Date Range</th>
	                    
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="180">Please Enter Issue No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                    	<input type="hidden" name="hidden_system_id" id="hidden_system_id">  
	                    </th> 
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
						  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
	                    
	                        
	                    <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Issue No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
	                    </td>     
	                    <td align="center" id="search_by_td">				
	                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
	                    </td> 						
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_challan_search_list_view', 'search_div', 'heat_setting_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                     </td>
	                </tr>
	                <tr>
	                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}
if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and recv_number like '$search_string'";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(insert_date) as year,";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later
	
	$sql = "select id, $year_field recv_number_prefix_num, recv_number, dyeing_source, dyeing_company, receive_date, process_id, batch_id from inv_receive_mas_batchroll where entry_form=63 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Issue No</th>
            <th width="60">Year</th>
            <th width="120">Service Source</th>
            <th width="140">Service Company</th>
            <th width="110">Process</th>
            <th width="100">Batch</th>
            <th>Issue date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				 
				$dye_comp="&nbsp;";
                if($row[csf('dyeing_source')]==1)
					$dye_comp=$company_arr[$row[csf('dyeing_company')]]; 
				else
					$dye_comp=$supllier_arr[$row[csf('dyeing_company')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $dye_comp; ?>&nbsp;</p></td>
                    <td width="110"><p><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
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





if ($action=="batch_number_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id,batch_no,is_sales,result_data,re_slitting)
		{ 
			 
			var result_arr=result_data.split("_");
			result_id=result_arr[0];
			result_name=result_arr[1];
			
			if(result_id==2 || result_id==3 || result_id==4 || result_id==5 || result_id==6)
			{
				alert("Result="+result_name+" Found");
				return;
			}
			var data_id=id+'_'+batch_no+'_'+is_sales+'_'+re_slitting;
			$('#hidden_batch_id').val(data_id);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:800px;">
	    <form name="searchbatchnofrm"  id="searchbatchnofrm">
	        <fieldset style="width:790px;">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="770" border="1" rules="all" class="rpt_table">
	                <thead>
	                    <tr>
	                        <th colspan="4">
	                          <?
								  echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
	                          ?>
	                        </th>
	                    </tr>                	
	                    <tr>
	                        <th width="150px">Batch Type</th>
	                        <th width="150px">Batch No</th>
	                        <th width="220px">Batch Date Range</th>
	                        <th>
	                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
	                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
	                        </th>
	                    </tr>
	                </thead>
	                <tr>
	                    <td align="center">	
	                        <?
	                            echo create_drop_down( "cbo_batch_type", 150, $order_source,"",0, "--Select--", 0,0,0 );
	                        ?>
	                    </td>
	                     <td align="center">				
	                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_batch" id="txt_search_batch" />	
	                    </td> 
	                    <td align="center">
	                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
	                    </td>
	                   
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_batch_type').value+'_'+document.getElementById('txt_search_batch').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_batch_search_list_view', 'search_div', 'singeing_and_desizing_production_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	            </table>
	            <table width="100%" style="margin-top:5px;">
	                <tr>
	                    <td colspan="4">
	                        <div style="width:100%; margin-left:3px;" id="search_div" align="left"></div>
	                    </td>
	                </tr>
	            </table>
	        </fieldset>
	    </form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript">
	$('#txt_search_batch').focus();
	</script>
	</html>
	<?
}
if($action=="create_batch_search_list_view")
{
	$data = explode("_",$data);
	$start_date =$data[0];
	$end_date =$data[1];
	$company_id =$data[2];
	$batch_type =$data[3];
	$batch_no =$data[4];
	$search_type =$data[5];
 	
	if($search_type==1)
	{
		if ($batch_no!='') { $batch_cond=" and batch_no='$batch_no'";$batch_cond2=" and a.batch_no='$batch_no'";} else { $batch_cond="";$batch_cond2="";}
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($batch_no!='') $batch_cond=" and batch_no like '%$batch_no%'"; else $batch_cond="";
		if ($batch_no!='') $batch_cond2=" and a.batch_no like '%$batch_no%'"; else $batch_cond2="";
	}
	else if($search_type==2)
	{
		if ($batch_no!='') $batch_cond=" and batch_no like '$batch_no%'"; else $batch_cond="";
		if ($batch_no!='') $batch_cond2=" and a.batch_no like '$batch_no%'"; else $batch_cond2="";
	}
	else if($search_type==3)
	{
		if ($batch_no!='') $batch_cond=" and batch_no like '%$batch_no'"; else $batch_cond="";
		if ($batch_no!='') $batch_cond2=" and a.atch_no like '%$batch_no'"; else $batch_cond2="";
	}	
	if($batch_type==0)
		$search_field_cond_batch="and entry_form in (0,36)";
	else if($batch_type==1)
		$search_field_cond_batch="and entry_form in (0,563)";
	else if($batch_type==2)
		$search_field_cond_batch="and entry_form=36";
	//echo $search_field_cond_batch;die;
	if($db_type==2)
		{	
		if ($start_date!="" &&  $end_date!="") 
		{ 
		$batch_date_con = " and batch_date between '".change_date_format($start_date, "mm-dd-yyyy", "-",1)."' and '".change_date_format($end_date, "mm-dd-yyyy", "-",1)."'";
		$batch_date_con2 = " and a.batch_date between '".change_date_format($start_date, "mm-dd-yyyy", "-",1)."' and '".change_date_format($end_date, "mm-dd-yyyy", "-",1)."'";
		}
		else { $batch_date_con ="";$batch_date_con2 ="";}
			if($batch_type==0 || $batch_type==2)
			{
		
			$sql_po=sql_select("select b.mst_id, a.job_no_mst,listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			}
			else
			{
				
			$sql_po=sql_select("select b.mst_id, a.job_no_mst,listagg(cast(a.po_number as varchar2(4000)),',') within group (order by a.po_number) as po_no from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			
				
			}
		}
		if($db_type==0)
		{	
		if ($start_date!="" &&  $end_date!="") $batch_date_con = " and batch_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $batch_date_con ="";
		
		if($batch_type==0 || $batch_type==2)
			{
		
			$sql_po=sql_select("select b.mst_id, a.job_no_mst,group_concat(distinct a.order_no )  as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			}
			else
			{
				
			$sql_po=sql_select("select b.mst_id, a.job_no_mst,group_concat( distinct a.po_number)  as po_no from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			
				
			}
		}
	$po_num=array();
	
	foreach($sql_po as $row_po_no)
	{
	$po_num[$row_po_no[csf('mst_id')]]['po_no']=$row_po_no[csf('po_no')];
	$po_num[$row_po_no[csf('mst_id')]]['job_no_mst']=$row_po_no[csf('job_no_mst')];		
	} 	//and company_id=$company_id

 $sql_prod = "select a.id, a.batch_no, b.result from pro_batch_create_mst a,pro_fab_subprocess b  where a.id=b.batch_id and b.entry_form=35 and b.status_active=1 and b.is_deleted=0  and b.load_unload_id=2 and b.result in(2,3,4,5,6) $batch_cond2 $batch_date_con2 order by a.id desc"; 
$nameArray_prod=sql_select( $sql_prod );
	foreach ($nameArray_prod as $row)
	{
		 // 2,3,4,5,6
		$batch_dyeing_chk_arr[$row[csf('id')]]=$row[csf('result')].'_'.$dyeing_result[$row[csf('result')]];
	}
	

	$sql = "select id, batch_no, batch_date, batch_weight, booking_no, extention_no, color_id, batch_against, re_dyeing_from, is_sales from pro_batch_create_mst where batch_for in(0,1) and batch_against<>4 and status_active=1 and is_deleted=0 $search_field_cond_batch $batch_date_con $batch_cond order by id desc"; 

	
	$is_sales_flag=0;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $selectResult)
	{
		if($selectResult[csf('is_sales')]==1)
		{
			$is_sales_flag=1;
		}
	}
	
	if($is_sales_flag==1)
	{
	
		$sql_sales_job=array();
		 
		$sql_sales_job= sql_select("SELECT b.job_no as job_no_mst,b.booking_no,f.job_no as sales_order_no,f.within_group from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, fabric_sales_order_mst f,pro_batch_create_mst g  where a.booking_no=b.booking_no and b.booking_no=f.sales_booking_no and b.po_break_down_id=c.id and g.booking_no=f.sales_booking_no $batch_date_con $batch_cond and g.status_active=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.job_no,b.booking_no,f.job_no,f.within_group");

	
		foreach ($sql_sales_job as $sales_job_row) {
			$sales_job_arr[$sales_job_row[csf('booking_no')]]["job_no_mst"] = $sales_job_row[csf('job_no_mst')];
			$sales_job_arr[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
			$sales_job_arr[$sales_job_row[csf('booking_no')]]["within_group"] = $sales_job_row[csf('within_group')];
		}
	}
	$sql_sales_job2= sql_select("SELECT  a.sales_booking_no as  booking_no, a.job_no as sales_order_no  from   fabric_sales_order_mst a ,pro_batch_create_mst b    where a.sales_booking_no=b.booking_no and a.status_active=1 and a.within_group=2  $batch_date_con $batch_cond group by a.sales_booking_no , a.job_no ");


	foreach ($sql_sales_job2 as $sales_job_row) {
		 
		$sales_job_arr2[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		 
	}

	//echo $sql;//die;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Batch No</th>
                <th width="80">Extention No</th>
                <th width="80">Batch Date</th>
                <th width="90">Batch Qnty</th>
                <th width="115">Job No</th>
                <th width="80">Color</th>
                <th>Po/FSO No</th>
            </thead>
        </table>
        <div style="width:770px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$is_sales = $selectResult[csf('is_sales')];
					$within_group=$sales_job_arr[$selectResult[csf('booking_no')]]["within_group"];
					$po_no='';
					$dyeing_resultid=$batch_dyeing_chk_arr[$selectResult[csf('id')]];
					if($selectResult[csf('re_dyeing_from')]==0 || 1==1)
					{	
						if($is_sales == 1){
							if($within_group == 1){
								$po_no = $sales_job_arr[$selectResult[csf('booking_no')]]["sales_order_no"];
								$job_no= $sales_job_arr[$selectResult[csf('booking_no')]]["job_no_mst"];
							}else{
								$po_no = $sales_job_arr2[$selectResult[csf('booking_no')]]["sales_order_no"];
								$job_no= "";								}
						}else{
							$po_no = implode(",", array_unique(explode(",", $po_num[$selectResult[csf('id')]]['po_no'])));
							$job_no= $po_num[$selectResult[csf('id')]]['job_no_mst'];
						}
						
						//$po_no=implode(",",array_unique(explode(",",$po_num[$selectResult[csf('id')]]['po_no'])));
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $selectResult[csf('id')]; ?>','<? echo $selectResult[csf('batch_no')];?>','<? echo $is_sales;?>','<? echo $dyeing_resultid;?>','0')"> 
							<td width="40" align="center"><? echo $i; ?></td>	
							<td width="100"  title="<? echo $dyeing_resultid;?>"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                            <td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
							<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
							<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
                            <td width="115"><p><? echo $job_no; ?></p></td>
							<td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
							<td style="word-break:break-all"><? echo $po_no; ?></td>	
						</tr>
						<?
						$i++;
					}
					else
					{
						$sql_re= "select id, batch_no, batch_date, batch_weight, booking_no, MAX(extention_no) as extention_no, color_id, batch_against, re_dyeing_from from pro_batch_create_mst where  batch_for in(0,1) and entry_form in(0,36,563) and batch_against<>4 and status_active=1 and is_deleted=0 and id=".$selectResult[csf('re_dyeing_from')]." group by id, batch_no, batch_date, batch_weight, booking_no,color_id, batch_against,re_dyeing_from ";
						//$dataArray=sql_select( $sql_re );
						$dataArray=array();
						foreach($dataArray as $row)
						{
							if($row[csf('re_dyeing_from')]==0)
							{
								$po_no=implode(",",array_unique(explode(",",$po_num[$selectResult[csf('id')]]['po_no'])));
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $selectResult[csf('id')]; ?>','<? echo $selectResult[csf('batch_no')];?>','<? echo $is_sales;?>','<? echo $dyeing_resultid;?>')">  
									<td width="40" align="center"><? echo $i; ?></td>	
									<td width="100" title="<? echo $dyeing_resultid;?>"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
									<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
									<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
									<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
								
                                    <td width="115"><p><? echo $po_num[$selectResult[csf('id')]]['job_no_mst']; ?></p></td>
									<td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
									<td><? echo $po_no; ?></td>	
								</tr>
								<?
								$i++;
							}
						}
					}
				}
			?>
            </table>
        </div>
	</div>           
	<?
	exit();
}

if($action=='populate_data_from_batch')
{
	$ex_data=explode('_',$data);
	$batch_id=$ex_data[0]; 
	$is_sales=$ex_data[1];
	$batch_no=$ex_data[2];
	$re_slitting_no=$ex_data[3]; 

	$sql_sales_job=array();
	$sql_sales_job=sql_select("SELECT a.buyer_id, b.job_no as job_no_mst,b.booking_no,f.job_no as sales_order_no,f.within_group from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, fabric_sales_order_mst f where a.booking_no=b.booking_no and b.booking_no=f.sales_booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.job_no,b.booking_no,f.job_no,f.within_group,a.buyer_id");

	foreach ($sql_sales_job as $sales_job_row) {
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["job_no_mst"] = $sales_job_row[csf('job_no_mst')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["buyer_id"] = $sales_job_row[csf('buyer_id')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["within_group"] = $sales_job_row[csf('within_group')];
	}
	$sql_sales_job2=sql_select("SELECT  a.sales_booking_no as  booking_no, a.job_no as sales_order_no ,a.buyer_id from   fabric_sales_order_mst a,pro_batch_create_mst b   where a.sales_booking_no=b.booking_no and b.id ='$batch_id' and a.status_active=1 and a.within_group=2 group by a.sales_booking_no , a.job_no,a.buyer_id ");
	foreach ($sql_sales_job2 as $sales_job_row) {
		 
		$sales_job_arr2[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		$sales_job_arr2[$sales_job_row[csf('booking_no')]]["buyer_id"] = $sales_job_row[csf('buyer_id')];
		 
	}


	if($db_type==0) $select_group_row="  order by a.id desc limit 1"; 
	else if($db_type==2) $select_group_row="group by a.entry_form,a.batch_no,a.batch_weight,a.color_id, 
	a.booking_without_order,a.batch_date,a.color_range_id,a.company_id,a.process_id,a.booking_no,a.total_trims_weight,a.sales_order_no ";	
	if($db_type==0) $pop_batch="";
	else if($db_type==2) $pop_batch=" group by a.batch_no, a.batch_weight,batch_date,a.color_id,a.color_range_id, a.booking_without_order,a.company_id,a.process_id,a.entry_form,a.booking_no,a.total_trims_weight,a.sales_order_no";
	if($db_type==0) $select_list=" group_concat(distinct(b.po_id)) as po_id"; 
	else if($db_type==2) $select_list=" listagg(b.po_id,',') within group (order by b.po_id) as po_id";

	if($batch_no!='')
	{ 
		$sql_re="SELECT MAX(a.id) as id,a.batch_no,a.entry_form,a.batch_date,a.company_id,a.total_trims_weight, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch,a.color_id,a.color_range_id,max(a.insert_date) as insert_date, a.booking_without_order,a.booking_no, sum(b.batch_qnty) as batch_qnty,a.sales_order_no, $select_list from pro_batch_create_mst a,pro_batch_create_dtls b where a.batch_no='$batch_no' and a.entry_form in(0,36,563)  and a.id=b.mst_id  and b.status_active=1  $select_group_row ";
	}
	else
	{	 
		$sql_re="SELECT a.company_id,a.total_trims_weight,MAX(a.id)as id,a.batch_no,a.entry_form, a.batch_weight,Max(a.extention_no) as extention_no,a.batch_date,a.process_id as process_id_batch, a.booking_no,a.color_id,a.color_range_id,Max(a.insert_date) as insert_date, a.booking_without_order, sum(b.batch_qnty) as batch_qnty,a.sales_order_no, $select_list from pro_batch_create_mst a,pro_batch_create_dtls b where a.id='$batch_id'  and a.id=b.mst_id and a.entry_form in(0,36,563)  and b.status_active=1 $pop_batch";	
	}

	
	$data_array=sql_select($sql_re);

	if($db_type==0) $select_f_group=""; 
	else if($db_type==2) $select_f_group="group by a.job_no_mst, b.buyer_name";

	if($db_type==0) $select_listagg="group_concat(distinct(a.po_number)) as po_no"; 
	else if($db_type==2) $select_listagg="listagg(cast(a.po_number as varchar2(4000)),',') within group (order by a.po_number) as po_no";
	if($db_type==0) $select_listagg_subcon="group_concat(distinct(a.order_no)) as po_no"; 
	else if($db_type==2) $select_listagg_subcon="listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no";

	foreach ($data_array as $row)
	{ 
	    $variable_production_roll=return_field_value("fabric_roll_level", "variable_settings_production", "variable_list=3 and item_category_id=50 and status_active=1 and is_deleted=0 and company_name=".$row[csf("company_id")]."");
		$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =".$row[csf("company_id")]." and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
		$re_slitting_no=return_field_value("re_stenter_no","pro_fab_subprocess","batch_id ='$batch_id' and entry_form=613  and is_deleted=0 and status_active=1");
		if($re_slitting_no==0 || $re_slitting_no=="") $re_slitting_no=0;else $re_slitting_no=$re_slitting_no;
		
		if($row[csf("total_trims_weight")]) $total_trims_weight=$row[csf("total_trims_weight")];else $total_trims_weight=0;

		 
		
	  	echo "document.getElementById('roll_maintained').value= '".$variable_production_roll."';\n";
		echo "document.getElementById('page_upto').value 		= '".$page_upto_id."';\n";
		
	  	 // if(str_replace("'","",$variable_production_roll)==1)
		if(($page_upto_id==3 || $page_upto_id>3) && str_replace("'","",$variable_production_roll)==1)
		{
		echo "$('#txt_issue_chalan').removeAttr('disabled','disabled');\n";
		echo "$('#roll_status_td').text('Roll No');\n";
		}
		else
		{
		echo "$('#roll_status_td').text('No of Roll');\n";
		echo "$('#txt_issue_chalan').attr('disabled','disabled');\n";
		}
		$pro_id=implode(",",array_unique(explode(",",$row[csf('po_id')])));
		//	echo "load_drop_down( 'requires/singeing_and_desizing_production_entry_controller', '".$row[csf("company_id")]."', 'load_drop_floor', 'floor_td' );\n";
		echo "document.getElementById('txt_reslitting_no').value 				= '".$re_slitting_no."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('txt_sales_number').value 				= '".$row[csf("sales_order_no")]."';\n";
		echo "document.getElementById('hidden_batch_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_batch_ID').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
 
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		
		if($row[csf("entry_form")]==36)
		{
			
			$batch_type="<b> SUBCONTRACT ORDER BATCH</b>";
			$result_job=sql_select("select $select_listagg_subcon, b.subcon_job as job_no_mst, b.party_id as buyer_name from  subcon_ord_dtls a, subcon_ord_mst b where a.job_no_mst=b.subcon_job and a.id in(".$pro_id.") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by b.subcon_job, b.party_id");
		}
		else
		{
			$batch_type="<b> SELF ORDER BATCH </b>";
			$result_job=sql_select("select $select_listagg, a.job_no_mst, b.buyer_name from wo_po_break_down a, 
			wo_po_details_master b where a.job_no_mst=b.job_no and a.id in(".$pro_id.") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 	and a.is_deleted=0 $select_f_group");
		}
		echo "document.getElementById('batch_type').innerHTML 			= '".$batch_type."';\n";
		
		$pro_id2=implode(",",array_unique(explode(",",$result_job[0][csf("po_no")])));
		$within_group=$sales_job_arr[$row[csf('booking_no')]]["within_group"];
		if ($is_sales == 1) {
			if($within_group == 1){
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$sales_job_arr[$row[csf('booking_no')]]["buyer_id"]] . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '" . $sales_job_arr[$row[csf('booking_no')]]["job_no_mst"] . "';\n";
		 
			}else{
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$sales_job_arr2[$row[csf('booking_no')]]["buyer_id"]] . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '';\n";
			 
			}
		}else{
			echo "document.getElementById('txt_buyer').value 			= '".$buyer_arr[$result_job[0][csf("buyer_name")]]."';\n";
			echo "document.getElementById('txt_job_no').value 			= '".$result_job[0][csf("job_no_mst")]."';\n";
			 
		}
		$sql_batch_d=sql_select("select id,load_unload_id,batch_no,process_end_date,production_date,end_hours,end_minutes,machine_id,floor_id,process_id,remarks from pro_fab_subprocess where
		batch_id='$batch_id' and entry_form=35 and load_unload_id in(1,2) and status_active=1");
		foreach($sql_batch_d as $dyeing_d)
		{
			if($dyeing_d[csf("load_unload_id")]==1)
			{
			
			echo "document.getElementById('txt_dying_end').value = '".$dyeing_d[csf("end_hours")].':'.$dyeing_d[csf("end_minutes")]."';\n";
			}
			 
		}
	    
		exit();
	}
}
if($action=='show_fabric_desc_listview')
{
	$ex_data=explode('_',$data);
    $batch_id=$ex_data[0];
	 $re_slitting_no=$ex_data[2];
	 
	 
	  // $re_slitting_no=return_field_value("max(re_stenter_no) as re_stenter_no"," pro_fab_subprocess","batch_id=".$batch_id." and is_deleted=0 and status_active=1 and entry_form=30","re_stenter_no");
	   // echo $re_slitting_no.'=d';
	  if($re_slitting_no=='') $re_slitting_no=0;else $re_slitting_no=$re_slitting_no;
	 
	$fabric_desc_arr=array();
	$prodData=sql_select("select id,detarmination_id, product_name_details,lot,gsm,yarn_count_id,brand, dia_width from product_details_master where item_category_id=13");
	foreach($prodData as $row)
	{
		$fabric_desc_arr[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
		$fabric_desc_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$fabric_desc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$fabric_desc_arr[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
	}
	$company_id=return_field_value("company_id","pro_batch_create_mst","id ='$batch_id' and is_deleted=0 and status_active=1");

    $yarn_lot_arr=array();
	if($db_type==0)
	{
		$yarn_lot_data=sql_select("SELECT   b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot, group_concat(distinct(a.brand_id)) as brand_id,group_concat( distinct a.yarn_count,'**') AS yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where  a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
	}
	else if($db_type==2)
	{
		$yarn_lot_data=sql_select("SELECT  b.po_breakdown_id, a.prod_id, LISTAGG(CAST(a.yarn_lot AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.yarn_lot) as yarn_lot, listagg(cast(a.yarn_count as varchar2(4000)),'**') within group (order by a.yarn_count) AS yarn_count, LISTAGG(a.brand_id,',') WITHIN GROUP ( ORDER BY a.brand_id) as brand_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
	
	}
	
	foreach($yarn_lot_data as $rows)
	{
		$yarn_lot=explode(",",$rows[csf('yarn_lot')]);
		$brand_id=explode(",",$rows[csf('brand_id')]);
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot']=implode(", ",array_unique($yarn_lot));
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count']=$rows[csf('yarn_count')];
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id']=implode(", ",array_unique($brand_id));

		$yarn_lot_arr_prodwise[$rows[csf('prod_id')]]['lot']=implode(", ",array_unique($yarn_lot));
		$yarn_lot_arr_prodwise[$rows[csf('prod_id')]]['yarn_count']=$rows[csf('yarn_count')];
		$yarn_lot_arr_prodwise[$rows[csf('prod_id')]]['brand_id']=implode(", ",array_unique($brand_id));


	}
	$fabric_roll_arr=array();
	//$prollData=sql_select("select id,roll_no,barcode_no from pro_roll_details where  status_active=1 and is_deleted=0");
	/*foreach($prollData as $row)
	{
		$fabric_roll_arr[$row[csf('id')]]['roll']=$row[csf('roll_no')];
		$fabric_roll_arr[$row[csf('id')]]['barcode']=$row[csf('barcode_no')];
	}*/
	
	$variable_production_roll=return_field_value("fabric_roll_level", "variable_settings_production", "variable_list=3 and item_category_id=50 and status_active=1 and is_deleted=0 and company_name=$company_id");
	$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	 
	//if($variable_production_roll==1)
	if(($page_upto_id==3 || $page_upto_id>3) && $variable_production_roll==1)
	{
		$i=1;	
		
		$sql_insert_roll=sql_select("SELECT b.roll_id from  pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batch_id and a.entry_form=30 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0");
		$inserted_roll=array();
		foreach($sql_insert_roll as $in_row)
		{
			$inserted_roll[]=$in_row[csf('roll_id')];
		}
		$roll_id_cond="";
		if(count($inserted_roll)>0) $roll_id_cond=" and b.roll_id not in (".implode(",",$inserted_roll).")";
		$sql_result=sql_select("SELECT a.entry_form,b.width_dia_type,b.gsm,b.fin_dia,a.company_id,b.item_description,b.prod_id,b.barcode_no,b.roll_no,b.roll_id,b.po_id,  b.batch_qnty as batch_qnty from pro_batch_create_dtls b,pro_batch_create_mst a where b.mst_id='$batch_id'  and a.id=b.mst_id and  a.entry_form in(0,36,563) and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0");//$roll_id_cond
		
			
		 foreach($sql_result as $row)
		{ 
			if($row[csf('entry_form')]==36)
			{
				$desc=explode(",",$row[csf('item_description')]);
				$cons_comps=$desc[0].','.$desc[1];
				$gsm=$row[csf('gsm')];
				$dia_width=$row[csf('fin_dia')];
			}
			else if($row[csf('entry_form')]==563)
			{
				$desc=explode(",",$row[csf('item_description')]);
				$cons_comps=$desc[0].','.$desc[1];
				$gsm=$row[csf('gsm')];
				$dia_width=$row[csf('fin_dia')];
			}
			else
			{
				//$cons_comps='';
				//$cons_comps_data=$fabric_desc_arr[$row[csf('prod_id')]]['desc'];
			/*	$z=0;
				foreach($cons_comps_data as $val)
				{
					if($z!=0)
					{
						$cons_comps.=$val." ";
					}
					$z++;
				}*/
				$cons_comps_data=explode(",",$fabric_desc_arr[$row[csf('prod_id')]]['desc']);
				$cons_comps=$cons_comps_data[0].' '.$cons_comps_data[1];
				$gsm=$fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
				$dia_width=$fabric_desc_arr[$row[csf('prod_id')]]['dia'];
			}
				$brand=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'];
				$lot=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'];
				$brand_id=explode(',',$brand);
				$brand_value="";
				foreach($brand_id as $val)
				{
					if($val>0)
					{
					if($brand_value=='') $brand_value=$brand_name[$val]; else $brand_value.=", ".$brand_name[$val];
					}
				}
				$y_count_id=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'];
				$count_id=array_unique(explode("**",$y_count_id));
				//print_r( $count_id).'aziz';
				//array_unique(explode(',',$y_count));
				$yarn_count_value='';
				foreach($count_id as $val)
				{
					if($val>0)
					{
						if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
					}
				}
				$barcode=$row[csf('barcode_no')];
				//$barcode=$fabric_roll_arr[$row[csf('roll_id')]]['barcode'];
			//print $gsm;
		    ?>
			<tr class="general" id="row_<? echo $i; ?>">
				<td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  checked> <? echo $i; ?></td>
				<td title="<? echo $cons_comps; ?>"><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:145px;" value="<? echo $cons_comps; ?>" disabled/></td>
				<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $gsm; ?>" /></td>
				<td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td>
				<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:50px;"  value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
				<input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
				</td>
				 <td>
                  <input type="text" name="txtknitting_density_<? echo $i; ?>" id="txtknitting_density_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px;" value=""/>
                 </td>
				 <td>
					<input type="text" name="txtheat_set_density_<? echo $i; ?>" id="txtheat_set_density_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px;"  value="" disabled />
				</td>
				 <td>
				 <input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" style="width:35px;" value="<? echo $row[csf('roll_no')];?>"/>
				 <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')];?>" class="text_boxes_numeric" />
				 
				 </td>
                
				<td><input type="text" name="txtsalesqnty_<? echo $i; ?>" id="txtsalesqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="" disabled/>
				<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
                <input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
				<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly />
				</td>
				<td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo number_format($row[csf('batch_qnty')],2,'.',''); ?>" onKeyUp="calculate_production_qnty();"/></td>
			   
				
			 
			 
                <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" readonly/></td>
                <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;"  readonly/> <input type="hidden" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;"  value="<? echo $yarn_count_value;?>"  disabled />
				<input type="hidden" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $brand_value;?>"  disabled/></td>
			</tr>
		    <?
		    $b_qty+= $row[csf('batch_qnty')];
			$i++;
	    }
	
		?>
		 <tr>
	        <td colspan="7" align="right"><b style=" float:left">Check/Uncheck<input type="checkbox" style="width:30px" id="allcheckbox" name="allcheckbox[]"  onClick="checkbox_all(this.id);" checked  /></b><b>Sum:</b> <? //echo $b_qty; ?> </td>
	        <td align="right"><? echo number_format($b_qty,2); ?> </td>
	        <td align="right" id="total_production_qnty"><? echo number_format($b_qty,2); ?> </td>
	        <td align="right" colspan="5" id="total_amount"> </td>
	     </tr>
	        <?
		exit();
	}
	else
	{	
		$fab_dtls_prod_arr=array();
		$sql_result_bal=sql_select("SELECT b.gsm,b.width_dia_type,a.batch_id,b.dia_width,sum(b.production_qty) as prod_qty,b.no_of_roll, b.prod_id from pro_fab_subprocess_dtls b,pro_fab_subprocess a where a.id=b.mst_id and a.batch_id='$batch_id' and b.entry_page=30 and a.entry_form=30 and a.re_stenter_no=$re_slitting_no  and a.status_active=1 and a.is_deleted=0 group by  b.gsm,b.width_dia_type,a.batch_id,b.dia_width,b.no_of_roll, b.prod_id");
		 
		foreach($sql_result_bal as $row)
		{
			$fab_dtls_prod_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('no_of_roll')]][$row[csf('width_dia_type')]][$row[csf('dia_width')]][$row[csf('gsm')]]['prod_qty']=$row[csf('prod_qty')];
		}
	//	$batch_id_search=sql_select("select a.batch_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$ex_data[0] and company_id=$ex_data[1] and entry_form=30");
		
		//$batch_insert_cond="";
		//if(count($batch_id_search)>0) $batch_insert_cond=" and a.id!=".$batch_id_search[0][csf('batch_id')]."";
		
		

		//$sql_result=sql_select("SELECT a.id as mst_id, b.id, b.gsm,b.width_dia_type,b.dia_width,b.const_composition,b.batch_qty,b.production_qty, b.roll_no,b.no_of_roll,b.prod_id,b.rate, b.amount from pro_fab_subprocess_dtls b,pro_fab_subprocess a where a.id=b.mst_id and a.batch_id='$batch_id' and b.entry_page=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.re_stenter_no=$re_slitting_no  order by a.id desc,b.id asc ");
		 //echo "SELECT a.id as mst_id, b.id, b.gsm,b.width_dia_type,b.dia_width,b.const_composition,b.batch_qty,b.production_qty, b.roll_no,b.no_of_roll,b.prod_id,b.rate, b.amount from pro_fab_subprocess_dtls b,pro_fab_subprocess a where a.id=b.mst_id and a.batch_id='$batch_id' and b.entry_page=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.re_stenter_no=$re_slitting_no  order by a.id desc,b.id asc ";
		 
		 $sql_result=sql_select("SELECT a.id as batch_id,a.entry_form,b.width_dia_type,b.gsm,b.fin_dia,b.item_description, sum(b.batch_qnty) as batch_qnty,b.prod_id,count(b.roll_no) as  roll_no from pro_batch_create_dtls b,pro_batch_create_mst a where b.mst_id=$batch_id and a.id=b.mst_id and  a.entry_form in(0,36,563) and b.status_active=1 and b.is_deleted=0 group by a.id,a.entry_form,b.width_dia_type,b.gsm,b.fin_dia,b.item_description,b.prod_id  order by b.item_description ");
		
	 
		 
		if(count($sql_result)>0)
		{
			 

			$i=1;
			//$mst_id=$sql_result[0][csf("mst_id")];

			foreach($sql_result as $row)
			{
				//if($mst_id!=$row[csf("mst_id")])break; Off this for Fakir  issue
				//echo $row[csf('production_qty')].'d';
				if($row[csf('entry_form')]==36)
				{
					$desc=explode(",",$row[csf('item_description')]);
					//print_r($desc);
					$cons_comps=$desc[0].','.$desc[1];
					$gsm=$row[csf('gsm')];
					$dia_width=$row[csf('fin_dia')];
				}else if($row[csf('entry_form')]==563)
				{
					$desc=explode(",",$row[csf('item_description')]);
					//print_r($desc);
					$cons_comps=$desc[0].','.$desc[1];
					$gsm=$row[csf('gsm')];
					$dia_width=$row[csf('fin_dia')];
				}
				else
				{
					//$cons_comps='';
					$cons_comps_data=explode(",",$fabric_desc_arr[$row[csf('prod_id')]]['desc']);
					$cons_comps=$cons_comps_data[0].' '.$cons_comps_data[1];
					//print_r($cons_comps_data);
					/*$z=0;
					foreach($cons_comps_data as $val)
					{
						if($z!=0)
						{
							$cons_comps.=$val." ";
						}
						$z++;
					}*/
					$gsm=$fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
					$dia_width=$fabric_desc_arr[$row[csf('prod_id')]]['dia'];
					
					
				}
			
				$brand=$lot=$yarn_lot_arr_prodwise[$row[csf('prod_id')]]['brand_id'];
				$lot=$yarn_lot_arr_prodwise[$row[csf('prod_id')]]['lot'];
				$brand_id=explode(',',$brand);
				$brand_value="";
				foreach($brand_id as $val)
				{
					if($val>0)
					{
						if($brand_value=='') $brand_value=$brand_name[$val]; else $brand_value.=", ".$brand_name[$val];
					}
				}
				$y_count_id=$yarn_lot_arr_prodwise[$row[csf('prod_id')]]['yarn_count'];
				$count_id=array_unique(explode("**",$y_count_id));

				$yarn_count_value='';
				foreach($count_id as $val)
				{
					if($val>0)
					{
						if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
					}
				}

				//$barcode=$row[csf('barcode_no')];
				
				$prod_prev_qty=$fab_dtls_prod_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('roll_no')]][$row[csf('width_dia_type')]][$dia_width][$gsm]['prod_qty'];
			// echo $row[csf('batch_id')].'='.$row[csf('prod_id')].'='.$row[csf('roll_no')].'='.$row[csf('width_dia_type')].'='.$dia_width.'='.$gsm;
			//$roll_no=$fabric_roll_arr[$row[csf('roll_id')]]['barcode'];
			if(!$prod_prev_qty) $prod_prev_qty=$row[csf('batch_qnty')];

				?>
				<tr class="general" id="row_<? echo $i; ?>">
					<!-- <td width="40" id="sl_<? //echo $i; ?>">&nbsp; &nbsp;<? //echo $i; ?></td> -->
					<td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  checked><? echo $i; ?></td>
					<td title="<? echo  $cons_comps; ?>"><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:145px;" value="<? echo  $cons_comps; ?>" disabled/></td>
					<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo  $gsm; ?>" readonly /></td>
					<td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo  $dia_width;; //$row[csf('width_dia_type')]; ?>" disabled/></td>
					<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:50px;"  value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
						<input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
					</td>
					<td>
						<input type="text" name="txtknitting_density_<? echo $i; ?>" id="txtknitting_density_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px;" value=""   />
					</td>
					<td><input type="text" name="txtheat_set_density_<? echo $i; ?>" id="txtheat_set_density_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px;"  value="" /></td>
					<td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" style="width:35px;" value="<? echo $row[csf('roll_no')];?>"/></td>
					
					<td><input type="text" name="txtsalesqnty_<? echo $i; ?>" id="txtsalesqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="" />
						<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
						<input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
						<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly />
					</td>
					<td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($prod_prev_qty,2,'.',''); ?>" style="width:50px;" onKeyUp="calculate_production_qnty();" /></td>

					<td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" readonly/></td>
					<td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;"  readonly/><input type="hidden" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;"  value="<? echo $yarn_count_value;?>"  disabled /><input type="hidden" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $brand_value;?>"  disabled/>
				    </td>
				</tr>
				<?
				$b_qty+= $row[csf('batch_qnty')];
				$prod_qty+= $prod_prev_qty;
				$i++;

			}
		}

		else
		{
			$result=sql_select("SELECT a.entry_form,b.width_dia_type,b.gsm,b.fin_dia,b.item_description, sum(b.batch_qnty) as batch_qty,count(b.roll_no) as no_of_roll,b.prod_id from pro_batch_create_dtls b,pro_batch_create_mst a  where b.mst_id='$batch_id'  and b.width_dia_type in(1,2,3) and a.id=b.mst_id and  a.entry_form in(0,36,563) and b.status_active=1 and b.is_deleted=0 group by a.entry_form,b.width_dia_type,b.gsm,b.fin_dia,b.item_description,b.prod_id order by b.item_description asc");
			$i=1;
			foreach($result as $row)
			{

				if($row[csf('entry_form')]==36)
				{
					$desc=explode(",",$row[csf('item_description')]);
					$cons_comps=$desc[0].','.$desc[1];
					$gsm=$row[csf('gsm')];
					$dia_width=$row[csf('fin_dia')];
				}
				else
				{

					$cons_comps_data=explode(",",$fabric_desc_arr[$row[csf('prod_id')]]['desc']);
					$cons_comps=$cons_comps_data[0].' '.$cons_comps_data[1];
					$gsm=$fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
					$dia_width=$fabric_desc_arr[$row[csf('prod_id')]]['dia'];
				}
				$brand=$lot=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'];
				$lot=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'];
				$brand_id=explode(',',$brand);
				$brand_value="";
				foreach($brand_id as $val)
				{
					if($val>0)
					{
						if($brand_value=='') $brand_value=$brand_name[$val]; else $brand_value.=", ".$brand_name[$val];
					}
				}
				$y_count_id=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'];
				$count_id=array_unique(explode("**",$y_count_id));

				$yarn_count_value='';
				foreach($count_id as $val)
				{
					if($val>0)
					{
						if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
					}
				}
				$barcode=$row[csf('barcode_no')];
				?>
				<tr class="general" id="row_<? echo $i; ?>">
					<!-- <td width="40" id="sl_<? //echo $i; ?>">&nbsp; &nbsp;<? //echo $i; ?></td> -->
					<td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  checked> <? echo $i; ?></td>
					<td title="<? echo $cons_comps; ?>"><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:145px;" value="<? echo $cons_comps; ?>" disabled/></td>
					<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $gsm; ?>" /></td>
					<td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td>
					<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:50px;"  value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
						<input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
					</td>
				
					<td>
						<input type="text" name="txtknitting_density_<? echo $i; ?>" id="txtknitting_density_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px;" value=" "/>
					</td>
					<td><input type="text" name="txtheat_set_density_<? echo $i; ?>" id="txtheat_set_density_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px;"  value="<? echo $lot;?>"   /></td>
					<td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" style="width:35px;" value="<? echo $row[csf('no_of_roll')];?>"/></td>
					<td><input type="text" name="txtsalesqnty_<? echo $i; ?>" id="txtsalesqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value=""  />
						<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
						<input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
						<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly />
					</td>
					<td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo number_format($row[csf('batch_qty')],2,'.',''); ?>"  onKeyUp="calculate_production_qnty();" /></td>

					
					 
					<td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" readonly/></td>
					<td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;"  readonly/>
						<input type="hidden" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;"  value="<? echo $yarn_count_value;?>"  disabled /><input type="hidden" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $brand_value;?>"  disabled/>
					</td>
				</tr>
				<?
				$b_qty+= $row[csf('batch_qty')];
				$prod_qty+= $row[csf('batch_qty')];
				$i++;

			}

		}
		
		?>
		<tr>
			<td colspan="8" align="right"><b style=" float:left">Check/Uncheck<input type="checkbox" style="width:30px" id="allcheckbox" name="allcheckbox[]"  onClick="checkbox_all(this.id);" checked  /></b><b>Sum:</b> <? //echo $b_qty; ?> </td>
			<td align="right">  </td> 
			<td align="right" id="total_production_qnty"><? echo number_format($prod_qty,2); ?> </td>
			<td align="right" colspan="5" id="total_amount"> </td>
		</tr>
		<?
	}

	exit();
}
if($action=='show_fabric_issue_listview')
{
	
	$ex_data=explode('_',$data);
    $batch_id=$ex_data[0];
	$hidden_roll_id=$ex_data[1];
	$fabric_desc_arr=array();
	$prodData=sql_select("select id,detarmination_id, product_name_details,lot,gsm,yarn_count_id,brand, dia_width from product_details_master where item_category_id=13");
	foreach($prodData as $row)
	{
		$fabric_desc_arr[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
		$fabric_desc_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$fabric_desc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$fabric_desc_arr[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
	}
	$fabric_roll_arr=array();
	$prollData=sql_select("select id,roll_no,barcode_no from pro_roll_details where  status_active=1 and is_deleted=0");
	foreach($prollData as $row)
	{
		$fabric_roll_arr[$row[csf('id')]]['barcode']=$row[csf('barcode_no')];
		$fabric_roll_arr[$row[csf('id')]]['roll']=$row[csf('roll_no')];
	}
	
        $yarn_lot_arr=array();
		if($db_type==0)
		{
			$yarn_lot_data=sql_select("select   b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot, group_concat(distinct(a.brand_id)) as brand_id,group_concat( distinct a.yarn_count,'**') AS yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where  a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
		}
		else if($db_type==2)
		{
			$yarn_lot_data=sql_select("select  b.po_breakdown_id, a.prod_id, LISTAGG(CAST(a.yarn_lot AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.yarn_lot) as yarn_lot, listagg(cast(a.yarn_count as varchar2(4000)),'**') within group (order by a.yarn_count) AS yarn_count, LISTAGG(a.brand_id,',') WITHIN GROUP ( ORDER BY a.brand_id) as brand_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
		}
		foreach($yarn_lot_data as $rows)
		{
			$yarn_lot=explode(",",$rows[csf('yarn_lot')]);
			$brand_id=explode(",",$rows[csf('brand_id')]);
			$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot']=implode(", ",array_unique($yarn_lot));
			$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count']=$rows[csf('yarn_count')];
			$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id']=implode(", ",array_unique($brand_id));
		}

	      $sql_insert_roll=sql_select("select b.roll_id from  pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batch_id and a.entry_form=30 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0");
		$inserted_roll=array();
		foreach($sql_insert_roll as $in_row)
		{
		$inserted_roll[]=$in_row[csf('roll_id')];
		}
		$roll_id_cond="";
		if(count($inserted_roll)>0) $roll_id_cond=" and b.roll_id not in (".implode(",",$inserted_roll).")";
		$sql_result=sql_select("select a.entry_form,b.width_dia_type,a.company_id,b.item_description,b.prod_id,b.barcode_no,b.roll_no,b.roll_id,b.po_id,  b.batch_qnty as batch_qnty from pro_batch_create_dtls b,pro_batch_create_mst a where b.mst_id='$batch_id' $roll_id_cond and b.roll_id  in($hidden_roll_id) and a.id=b.mst_id and  a.entry_form in(0,36,563) and b.status_active=1 and b.is_deleted=0  ");
		$batch_roll_id=array();
		$i=1;
	
		if(count($sql_result)<=0)
		{
			
		$sql_result=sql_select("select a.entry_form,b.width_dia_type,a.company_id,b.item_description,b.prod_id,b.barcode_no,b.roll_no,b.roll_id,b.po_id,  b.batch_qnty as batch_qnty from pro_batch_create_dtls b,pro_batch_create_mst a where b.mst_id='$batch_id' $roll_id_cond and a.id=b.mst_id and  a.entry_form in(0,36,563) and b.status_active=1 and b.is_deleted=0  ");
		}
		foreach($sql_result as $row)
		{ 
			if($row[csf('entry_form')]==36)
			{
				$desc=explode(",",$row[csf('item_description')]);
				$cons_comps=$desc[0];
				$gsm=$desc[1];
				$dia_width=$desc[2];
			}
			else
			{
				//$cons_comps='';
				//$cons_comps=$fabric_desc_arr[$row[csf('prod_id')]]['desc'];
				$cons_comps_data=explode(",",$fabric_desc_arr[$row[csf('prod_id')]]['desc']);
				$cons_comps=$cons_comps_data[0].' '.$cons_comps_data[1];
				/*$z=0;
				foreach($cons_comps_data as $val)
				{
					if($z!=0)
					{
						$cons_comps.=$val." ";
					}
					$z++;
				}*/
				$gsm=$fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
				$dia_width=$fabric_desc_arr[$row[csf('prod_id')]]['dia'];
			}
			$compamy=$row[csf('company_id')];
			
			    $brand=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'];
				$lot=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'];
				$brand_id=explode(',',$brand);
				$brand_value="";
				foreach($brand_id as $val)
				{
					if($val>0)
					{
						if($brand_value=='') $brand_value=$brand_name[$val]; else $brand_value.=", ".$brand_name[$val];
					}
				}
				$y_count_id=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'];
				$count_id=array_unique(explode("**",$y_count_id));
				//print_r( $count_id).'aziz';
				//array_unique(explode(',',$y_count));
				$yarn_count_value='';
				foreach($count_id as $val)
				{
					if($val>0)
					{
						if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
					}
				}
				$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$compamy' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
				$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$compamy and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
				
				if(($page_upto_id==3 || $page_upto_id>3) && $roll_maintained==1)//if($roll_maintained==1)
				{
					$barcode=$fabric_roll_arr[$row[csf('roll_id')]]['barcode'];
					$roll_no=$fabric_roll_arr[$row[csf('roll_id')]]['roll'];
					$readonly="readonly";
				}
				else
				{
					$roll_no=$row[csf('roll_no')];
					$readonly="";	
				}
				
			//print $gsm;
	?>
    	<tr class="general" id="row_<? echo $i; ?>">
            <td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]" checked="checked" > &nbsp; &nbsp;<? echo $i; ?></td>
            <td title="<? echo $cons_comps; ?>"><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:145px;" value="<? echo $cons_comps; ?>" disabled/></td>
            <td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $gsm; ?>" /></td>
            <td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td>
            <td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:50px;"  value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
            <input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
            </td>
             <td>
             <input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" style="width:35px;" value="<? echo $roll_no;?>"/>
             <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')];?>" class="text_boxes_numeric" />
             
             </td>
             <td>
              	<input type="text" name="txtknitting_density_<? echo $i; ?>" id="txtknitting_density_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px;" value="<? echo $barcode;?>"/>
             </td>
            <td><input type="text" name="txtsalesqnty_<? echo $i; ?>" id="txtsalesqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo number_format($row[csf('batch_qnty')],2,'.',''); ?>" disabled/>
            <input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
            <input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
			<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly />
            </td>
            <td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;"onKeyUp="calculate_production_qnty();" /></td>
           
            <td><input type="text" name="txtheat_set_density_<? echo $i; ?>" id="txtheat_set_density_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px;"  value="<? echo $lot;?>" disabled /></td>
            <td><input type="text" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;"  value="<? echo $yarn_count_value;?>"  disabled /></td>
            <td><input type="text" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $brand_value;?>"  disabled/></td>
            <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" readonly/></td>
            <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;"  readonly/> </td>
        </tr>
    <?
	$b_qty+= $row[csf('batch_qnty')];
		$i++;
	 }
	
	?>
	 <tr>
         <td colspan="7" align="right"><b>Sum:</b> <? //echo $b_qty; ?> </td>
         <td align="right"><? echo number_format($b_qty,2); ?> </td>
         <td align="right" id="total_production_qnty"><? echo number_format($prod_qty,2); ?> </td>
         <td align="right" colspan="5" id="total_amount"><? //echo number_format($b_qty,2); ?> </td>
     </tr>
        <?
	exit();


	

}
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
		
		$updateId=str_replace("'","",$txt_update_id);
		if($updateId) $up_id_cond="and a.id!=$updateId";else $up_id_cond="";
		$batch_prod=sql_select("select a.batch_id,max(a.id) as mst_id from pro_fab_subprocess a where  a.batch_id=$txt_batch_ID and a.company_id=$cbo_company_id and a.status_active=1 $up_id_cond group by a.batch_id ");
		//echo "10**select a.batch_id,max(a.id) as mst_id from pro_fab_subprocess a where  a.batch_id=$txt_batch_ID and a.company_id=$cbo_company_id and a.status_active=1 $up_id_cond group by a.batch_id ";die;
		$last_mst_id=0;
		foreach($batch_prod as $row)
		{
			$last_mst_id= $row[csf('mst_id')];
		}
		$process_enddate=str_replace("'","",$txt_process_date);
		$end_hours=str_replace("'","",$txt_end_hours);
		$end_minutes=str_replace("'","",$txt_end_minutes);
		
		$end_hours=(int)$end_hours;
		$end_minutes=(int)$end_minutes;
		
		if ($operation==0)
		{
		$batch_last_process=sql_select("select a.batch_id,a.end_hours,a.end_minutes,a.process_end_date,a.production_date from pro_fab_subprocess a where  a.batch_id=$txt_batch_ID and a.company_id=$cbo_company_id and a.id in($last_mst_id) and a.status_active=1  ");
		foreach($batch_last_process as $row)
		{
			$previ_process_end_date= $row[csf('production_date')];
			$previ_end_hours= $row[csf('end_hours')];
			$previ_end_minutes= $row[csf('end_minutes')];
		}
		$previ_end_hours=(int)$previ_end_hours;
		$previ_end_minutes=(int)$previ_end_minutes;
		}
		else if ($operation==1)
		{
		$batch_last_process=sql_select("select a.batch_id,a.end_hours,a.end_minutes,a.process_end_date,a.production_date from pro_fab_subprocess a where  a.batch_id=$txt_batch_ID and a.company_id=$cbo_company_id and a.id in($last_mst_id) and a.id!=$updateId and a.status_active=1  ");
		//echo "10**select a.batch_id,a.end_hours,a.end_minutes,a.process_end_date,a.production_date from pro_fab_subprocess a where  a.batch_id=$txt_batch_ID and a.company_id=$cbo_company_id and a.id in($last_mst_id) and a.status_active=1  ";die;
		foreach($batch_last_process as $row)
		{
			$previ_process_end_date= $row[csf('production_date')];
			$previ_end_hours= $row[csf('end_hours')];
			$previ_end_minutes= $row[csf('end_minutes')];
		}
		$previ_end_hours=(int)$previ_end_hours;
		$previ_end_minutes=(int)$previ_end_minutes;
		}
		$end_date_time=$process_enddate.'.'.$end_hours.'.'.$end_minutes; 
		$curr_prod_end_date_time=strtotime($end_date_time);
		$previ_end_date_time=$previ_process_end_date.'.'.$previ_end_hours.'.'.$previ_end_minutes; 
		$previ_prod_end_date_time=strtotime($previ_end_date_time);
		//echo "10**".$previ_prod_end_date_time.'='.$curr_prod_end_date_time;die;
		$msg_txt="Current process date and time cannot be less than previous date and time";
		if($curr_prod_end_date_time<=$previ_prod_end_date_time && $previ_process_end_date!="")
		{
			echo "13**".$msg_txt."**".$end_date_time."**".$previ_end_date_time;
			disconnect($con);die;
		}
		
		
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	//	$batch_id_search=sql_select("select a.batch_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$txt_batch_ID and company_id=$cbo_company_id and entry_form=30");
		
		//$duplicate = is_duplicate_field("id","pro_fab_subprocess","batch_id=$txt_batch_ID and company_id=$cbo_company_id and entry_form=30  and status_active=1 and is_deleted=0"); 
		/*if($duplicate==1) 
		{
			echo "11**Duplicate Batch is Not Allow.";
			die;
		}*/
			$unload_batch=return_field_value("batch_id","pro_fab_subprocess","batch_id=$txt_batch_ID and company_id=$cbo_company_id and entry_form in(38,35) and load_unload_id=2  and status_active=1 and is_deleted=0","batch_id");
			if($unload_batch=="")
			{
				//echo "100**Please Unload First";disconnect($con);die;
			}
		
		$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$cbo_company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
		
		if(str_replace("'","",$update_id)=="")
		$field_array="id,company_id,service_source,service_company,received_chalan,issue_chalan,issue_challan_mst_id ,batch_no,re_stenter_no,batch_id,
        process_id,process_start_date,process_end_date,production_date,start_hours,start_minutes,end_hours,end_minutes,machine_id,result,floor_id,entry_form,
        shift_name,next_process_id,advanced_prod_qty,is_re_dyeing,booking_no,temparature,steam,over_feed,speed_min,inserted_by,insert_date";
		$id=return_next_id( "id", " pro_fab_subprocess", 1 ) ;
		$data_array="(".$id.",".$cbo_company_id.",".$cbo_service_source.",".$cbo_service_company.",".$txt_recevied_chalan.",".$txt_issue_chalan.",".$txt_issue_mst_id.",".$txt_batch_no.",".$txt_reslitting_no.",".$txt_batch_ID.",463,".$txt_process_start_date.",".$txt_process_end_date.",".$txt_process_date.",".$txt_start_hours.",".$txt_start_minutes.",".$txt_end_hours.",".$txt_end_minutes.",".$cbo_machine_name.",".$cbo_result_name.",".$cbo_floor.",613,".$cbo_shift_name.",".$cbo_next_process.",".$txt_advance_prod.",".$re_checkbox.",".$txt_booking_no.",".$txt_temparature.",".$txt_steam.",".$txt_feed.",".$txt_speed.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//print_r($data_array);die;
		$mst_update_id=str_replace("'","",$id);
		
		$id_dtls=return_next_id( "id", "pro_fab_subprocess_dtls", 1 ) ;
		//echo $total_row;die;
		//if(str_replace("'","",$roll_maintained)==1)
		if(($page_upto_id==3 || $page_upto_id>3) && str_replace("'","",$roll_maintained)==1)
		{
			$field_array_dtls="id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,sales_qnty,roll_no,knitting_density,heat_set_density,roll_id,production_qty, rate, amount, currency_id, exchange_rate, inserted_by,insert_date";
			for($i=1;$i<=$total_row;$i++)
			{
				$checked_tr="checkRow_".$i;
				if(str_replace("'","",$$checked_tr)==1)
				{
				$prod_id="txtprodid_".$i;
				$txtconscomp="txtconscomp_".$i;
				$txtgsm="txtgsm_".$i;
				$txtbodypart="txtbodypart_".$i;
				$txtdiawidth="txtdiawidth_".$i;
				$txtbatchqnty="txtsalesqnty_".$i;
				$txtroll="txtroll_".$i;
				$txtknitting_density="txtknitting_density_".$i;
				$txtheat_set_density="txtheat_set_density_".$i;
				$rollid="rollid_".$i;
				$txtproductionqty="txtproductionqty_".$i;
				$txtdiawidthID="txtdiawidthID_".$i;
				
				$txtrate="txtrate_".$i;
				$txtamount="txtamount_".$i;
				
				$Itemprod_id=str_replace("'","",$$prod_id);
				if($data_array_dtls!="") $data_array_dtls.=","; 
				$data_array_dtls.="(".$id_dtls.",".$mst_update_id.",30,'".$Itemprod_id."',".$$txtconscomp.",".$$txtgsm.",".$$txtbodypart.",".$$txtdiawidthID.",".$$txtbatchqnty.",".$$txtroll.",".$$txtknitting_density.",".$$txtheat_set_density.",".$$rollid.",".$$txtproductionqty.",".$$txtrate.",".$$txtamount.",".$hidden_currency.",".$hidden_exchange_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_dtls=$id_dtls+1;
				}
				//print_r($data_array_dtls);die;
			}	
			
		}
		else
		{
			$field_array_dtls="id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,sales_qnty,no_of_roll,production_qty, rate, amount, currency_id, exchange_rate, inserted_by,insert_date";
			for($i=1;$i<=$total_row;$i++)
			{
				$checked_tr="checkRow_".$i;
				if(str_replace("'","",$$checked_tr)==1)
				{
					$prod_id="txtprodid_".$i;
					$txtconscomp="txtconscomp_".$i;
					$txtgsm="txtgsm_".$i;
					$txtbodypart="txtbodypart_".$i;
					$txtdiawidth="txtdiawidth_".$i;
					$txtbatchqnty="txtsalesqnty_".$i;
					$txtroll="txtroll_".$i;
					$txtproductionqty="txtproductionqty_".$i;
					$txtdiawidthID="txtdiawidthID_".$i;
					$txtrate="txtrate_".$i;
					$txtamount="txtamount_".$i;
					
					$Itemprod_id=str_replace("'","",$$prod_id);
					if($data_array_dtls!="") $data_array_dtls.=","; 
					$data_array_dtls.="(".$id_dtls.",".$mst_update_id.",30,'".$Itemprod_id."','".str_replace("'","",$$txtconscomp)."',".$$txtgsm.",".$$txtbodypart.",".$$txtdiawidthID.",".$$txtbatchqnty.",".$$txtroll.",".$$txtproductionqty.",".$$txtrate.",".$$txtamount.",".$hidden_currency.",".$hidden_exchange_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id_dtls=$id_dtls+1;
					//print_r($data_array_dtls);die;
			   }
			}
		
		}




		$rID=$rID2=true;
		$rID=sql_insert("pro_fab_subprocess",$field_array,$data_array,0);
		if($rID) $flag=1; else $flag=0;
		//if($data_array_dtls!="")
		//{
			$rID2=sql_insert("pro_fab_subprocess_dtls",$field_array_dtls,$data_array_dtls,0);
			
			if ($flag == 1)
			{
				if ($rID2)
					$flag = 1;
				else
					$flag = 0;
			}
		//}
		//echo "10**insert into pro_fab_subprocess_dtls ($field_array_dtls) values $data_array_dtls";die;
		//echo "10**$rID**$rID2";die;
		//check_table_status( $_SESSION['menu_id'],0);

		/*$batch_field_array_update = "total_trims_weight*updated_by*update_date";
		$batch_data_array_update = "".$txt_trims_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID4=sql_update("pro_batch_create_mst",$batch_field_array_update,$batch_data_array_update,"id",$txt_batch_ID,1);	*/
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".str_replace("'","",$roll_maintained)."**".str_replace("'","",$txt_reslitting_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$id."**".str_replace("'","",$roll_maintained)."**".str_replace("'","",$txt_reslitting_no);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id."**".str_replace("'","",$roll_maintained)."**".str_replace("'","",$txt_reslitting_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id."**".str_replace("'","",$roll_maintained)."**".str_replace("'","",$txt_reslitting_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here 
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$cbo_company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
		
		$update_id=str_replace("'","",$txt_update_id);
		$field_array_update="company_id*service_source*service_company*received_chalan*issue_chalan*issue_challan_mst_id *batch_no*re_stenter_no*batch_id*process_id*process_end_date*production_date*process_start_date*start_hours*start_minutes*end_hours*end_minutes*machine_id*result*floor_id*entry_form*shift_name*next_process_id*advanced_prod_qty*is_re_dyeing*booking_no*temparature*steam*over_feed*speed_min*updated_by*update_date";
		$data_array_update="".$cbo_company_id."*".$cbo_service_source."*".$cbo_service_company."*".$txt_recevied_chalan."*".$txt_issue_chalan."*".$txt_issue_mst_id."*".$txt_batch_no."*".$txt_reslitting_no."*".$txt_batch_ID."*463*".$txt_process_end_date."*".$txt_process_date."*".$txt_process_start_date."*".$txt_start_hours."*".$txt_start_minutes."*".$txt_end_hours."*".$txt_end_minutes."*".$cbo_machine_name."*".$cbo_result_name."*".$cbo_floor."*613*".$cbo_shift_name."*".$cbo_next_process."*".$txt_advance_prod."*".$re_checkbox."*".$txt_booking_no."*".$txt_temparature."*".$txt_steam."*".$txt_feed."*".$txt_speed."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//print_r($data_array_update);die;
		
		$add_comma=0;
		//if(str_replace("'",'',$roll_maintained)==1)
		if(($page_upto_id==3 || $page_upto_id>3) && str_replace("'","",$roll_maintained)==1)
		{
			$field_array_up="gsm*production_qty*rate*amount*currency_id*exchange_rate*updated_by*update_date";
			$field_array_delete="updated_by*update_date*status_active*is_deleted";
		    for($i=1; $i<=$total_row; $i++)
			{
				$checked_tr="checkRow_".$i;
				$prod_id="txtprodid_".$i;
				$rollid="rollid_".$i;
				$txtgsm="txtgsm_".$i;
				$txtroll="txtroll_".$i;
				$txtknitting_density="txtknitting_density_1".$i;
				$txtheat_set_density="txtheat_set_density_".$i;
				$txtproductionqty="txtproductionqty_".$i;
				$txtdiawidth="txtdiawidth_".$i;
				$txtrate="txtrate_".$i;
				$txtamount="txtamount_".$i;
				//$txtbatchqnty="txtsalesqnty_".$i;
				$updateiddtls="updateiddtls_".$i;
				
				if(str_replace("'","",$$checked_tr)==1)
				{
					$id_arr[]=str_replace("'",'',$$updateiddtls);
					$data_array_up[str_replace("'",'',$$updateiddtls)] =explode("*",("".$$txtgsm."*".$$txtproductionqty."*".$$txtrate."*".$$txtamount."*".$hidden_currency."*".$hidden_exchange_rate."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
				else
				{
					$id_arr_delete[]=str_replace("'",'',$$updateiddtls);
					$data_array_delete[str_replace("'",'',$$updateiddtls)] =explode("*",("'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1"));	
				}
			}	
			
		}
		else
		{
			$field_array_up="mst_id*gsm*production_qty*no_of_roll*rate*amount*currency_id*exchange_rate*knitting_density*heat_set_density*updated_by*update_date";
			$field_array_delete="updated_by*update_date*status_active*is_deleted";
			for($i=1; $i<=$total_row; $i++)
			{
				$checked_tr="checkRow_".$i;
				$prod_id="txtprodid_".$i;
				$txtgsm="txtgsm_".$i;
				$txtroll="txtroll_".$i;
				$txtproductionqty="txtproductionqty_".$i;
				$txtdiawidth="txtdiawidth_".$i;
				$txtknitting_density="txtknitting_density_1".$i;
				$txtheat_set_density="txtheat_set_density_".$i;
				$txtrate="txtrate_".$i;
				$txtamount="txtamount_".$i;
				//$txtbatchqnty="txtsalesqnty_".$i;
				$updateiddtls="updateiddtls_".$i;
				if(str_replace("'","",$$checked_tr)==1)
				{
					$id_arr[]=str_replace("'",'',$$updateiddtls);
					$data_array_up[str_replace("'",'',$$updateiddtls)] =explode("*",("".$update_id."*".$$txtgsm."*".$$txtproductionqty."*".$$txtroll."*".$$txtrate."*".$$txtamount."*".$hidden_currency."*".$hidden_exchange_rate."*".$txtknitting_density."*".$txtheat_set_density."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
				else
				{
					$id_arr_delete[]=str_replace("'",'',$$updateiddtls);
					$data_array_delete[str_replace("'",'',$$updateiddtls)] =explode("*",("'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1"));	
				}
			}
		}
		
		$flag=0;
		$rID=sql_update("pro_fab_subprocess",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		
		if(count($field_array_up)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement("pro_fab_subprocess_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
			if($rID2) $flag=1; else $flag=0;
		}
		if(count($data_array_delete)>0)
		{
			$rID3=execute_query(bulk_update_sql_statement("pro_fab_subprocess_dtls", "id",$field_array_delete,$data_array_delete,$id_arr_delete ));
			if($rID3) $flag=1; else $flag=0;
		}

		/*$batch_field_array_update = "total_trims_weight*updated_by*update_date";
		$batch_data_array_update = "".$txt_trims_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID4=sql_update("pro_batch_create_mst",$batch_field_array_update,$batch_data_array_update,"id",$txt_batch_ID,1);*/
		//check_table_status( $_SESSION['menu_id'],0);	
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$update_id."**".str_replace("'","",$roll_maintained)."**".str_replace("'","",$txt_reslitting_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_id."**".str_replace("'","",$roll_maintained)."**".str_replace("'","",$txt_reslitting_no);
			}
		}
		if($db_type==2 || $db_type==1 )
		{	
		if($flag==1)
			  {
				 oci_commit($con); 
					echo "1**".$update_id."**".str_replace("'","",$roll_maintained)."**".str_replace("'","",$txt_reslitting_no);
			  }
			  else
			  {
				 oci_rollback($con);
				  echo "10**".$update_id."**".str_replace("'","",$roll_maintained)."**".str_replace("'","",$txt_reslitting_no);
			  }
		}
		disconnect($con);
		die;
	}
}
if($action=="check_issue_challan_no_scan")
{
	$data=explode("**",$data);
	//print_r($data);die;
	//$sql_result=sql_select("select  b.prod_id,b.width_dai_type,a.recv_number_prefix_num,b.roll_id,a.batch_id from pro_grey_batch_dtls  b, inv_receive_mas_batchroll a  where a.id=b.mst_id and b.mst_id=$data and a.entry_form=63 and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0");
	$sql="select  a.id,a.recv_number from  inv_receive_mas_batchroll a  where   a.recv_number='$data[1]'  and a.company_id=$data[0] and a.entry_form=63 and a.status_active=1 and a.is_deleted=0 ";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('recv_number_prefix_num')];
	}
	else
	{
		echo "0_";
	}
	exit();	
}


if($action=="check_issue_challan_no")
{
	$data=explode("**",$data);
	//print_r($data);die;
	//$sql_result=sql_select("select  b.prod_id,b.width_dai_type,a.recv_number_prefix_num,b.roll_id,a.batch_id from pro_grey_batch_dtls  b, inv_receive_mas_batchroll a  where a.id=b.mst_id and b.mst_id=$data and a.entry_form=63 and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0");
	$sql="select  a.id,a.recv_number_prefix_num from  inv_receive_mas_batchroll a  where   a.recv_number_prefix_num=$data[1]  and a.company_id=$data[0] and a.entry_form=63 and a.status_active=1 and a.is_deleted=0 ";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('recv_number_prefix_num')];;
	}
	else
	{
		echo "0_";
	}
	exit();	
}

if($action=="check_batch_no")
{	//and company_id='".trim($data[0])."'
	$data=explode("**",$data);
	$sql="select id, batch_no,is_sales from pro_batch_create_mst where batch_no='".trim($data[1])."' and  entry_form in(0,36,563) and is_deleted=0 and status_active=1 order by id desc";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('is_sales')];
	}
	else
	{
		echo "0_";
	}
	
	exit();	
}

if($action=="check_batch_deying")
{	//and company_id='".trim($data[0])."'
	//$data=explode("**",$data);
	$sql="select id, batch_id,batch_no from pro_fab_subprocess where batch_no='".trim($data)."' and  entry_form in(35,38) and load_unload_id in(1) and is_deleted=0 and status_active=1";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('batch_id')];
	}
	else
	{
		echo "0_";
	}
	
	exit();	
}
if($action=="check_batch_deying_result")
{	//and company_id='".trim($data[0])."'
	//$data=explode("**",$data);
	$sql="select id, batch_id,batch_no,result from pro_fab_subprocess where batch_no='".trim($data)."' and  entry_form in(35,38) and load_unload_id in(2) and result in(2,3,4,5,6)  and is_deleted=0 and status_active=1";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('batch_id')]."_".$dyeing_result[$data_array[0][csf('result')]];
	}
	else
	{
		echo "0_";
	}
	
	exit();	
}


if($action=="check_batch_no_scan")
{
	$data=explode("**",$data);
	$batch_id=(int) $data[1];
	$sql="select id, batch_no,company_id from pro_batch_create_mst where id='".$batch_id."' and entry_form in(0,36,563) and is_deleted=0 and status_active=1 order by id desc";
	$data_array=sql_select($sql,1);
	echo $data_array[0][csf('batch_no')];
	/*if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('company_id')];
	}
	else
	{
		echo "0_";
	}*/
	exit();	
}
if($action=="process_name_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var style_ref_array= new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function set_all()
		{
			var old=document.getElementById('txt_process_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
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
			
			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
    </script>
	</head>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
	    	<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
	        <input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
	        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
	                <thead>
	                    <th width="50">SL</th>
	                    <th>Process Name</th>
	                </thead>
	            </table>
	            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
	                <?
	                    $i=1; $process_row_id=''; $not_process_id_print_array=array(1,2,3,4,101,120,121,122,123,124);
						$hidden_process_id=explode(",",$txt_process_id);
	                    foreach($conversion_cost_head_array as $id=>$name)
	                    {
							if(!in_array($id,$not_process_id_print_array))
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								 
								if(in_array($id,$hidden_process_id)) 
								{ 
									if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
									<td width="50" align="center"><?php echo "$i"; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
									</td>	
									<td><p><? echo $name; ?></p></td>
								</tr>
								<?
								$i++;
							}
	                    }
	                ?>
	                    <input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
	                </table>
	            </div>
	             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
	                <tr>
	                    <td align="center" height="30" valign="bottom">
	                        <div style="width:100%"> 
	                            <div style="width:50%; float:left" align="left">
	                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
	                            </div>
	                            <div style="width:50%; float:left" align="left">
	                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
	                            </div>
	                        </div>
	                    </td>
	                </tr>
	            </table>
	        </form>
	    </fieldset>
	</div>    
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}


if($action=="show_dtls_list_view")
{
	$ex_data = explode("_",$data);
	//$issue_number = $ex_data[0];
 	

	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	if($db_type==0)
	{
		
		// group_concat(roll_id)  as roll_id 
	 	$sql = "SELECT a.id,group_concat(b.roll_no) AS roll_no,group_concat(b.const_composition) AS const_composition,group_concat(b.gsm) as gsm ,group_concat(b.dia_width) as dia_width,group_concat(b.width_dia_type) as width_dia_type,sum(b.batch_qty) as batch_qty,sum(b.production_qty) as production_qty,sum(b.no_of_roll) as no_of_roll from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$ex_data[0] and entry_form=613 group by a.id";
	}
	else
	{
		$sql = "SELECT a.id,a.batch_id,b.roll_no AS roll_no,b.const_composition AS const_composition, b.gsm as gsm ,b.dia_width as dia_width,b.width_dia_type as width_dia_type, b.roll_id as roll_id,sum(b.batch_qty) as batch_qty,sum(b.production_qty) as production_qty,sum(b.no_of_roll) as no_of_roll from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$ex_data[0] and entry_form=613 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id,a.batch_id,b.roll_no,b.const_composition, b.gsm,b.dia_width,b.width_dia_type, b.roll_id order by b.roll_no";	
		
	}
	// echo $sql;die;
	$i=1;
	$dtls_arr = array();
	$check_arr = array();
	$result = sql_select($sql);
	foreach($result as $key=>$val)
	{
		$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['id'] = $val[csf('id')];
		// $dtls_arr[$val[csf('batch_id')]]['roll'] = $val[csf('roll_no')];
		if($check_arr[$val[csf('batch_id')]]['roll_no']==$val[csf('roll_no')])
		{
			$check_arr[$val[csf('batch_id')]]['roll_no'] = $val[csf('roll_no')];
			$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['roll_no'] = $val[csf('roll_no')];
		}
		else
		{
			$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['roll_no'] .= $val[csf('roll_no')].",";
			$check_arr[$val[csf('batch_id')]]['roll_no'] = $val[csf('roll_no')];
		}

		$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['const_composition'] .= ($dtls_arr[$val[csf('batch_id')]]['const_composition']==$val[csf('const_composition')].",") ? '' : $val[csf('const_composition')].",";
		// $dtls_arr[$val[csf('batch_id')]]['gsm'] .= ($dtls_arr[$val[csf('batch_id')]]['gsm']==$val[csf('gsm')].",") ? '' : $val[csf('gsm')].",";
		if($check_arr[$val[csf('batch_id')]]['gsm']==$val[csf('gsm')])
		{
			$check_arr[$val[csf('batch_id')]]['gsm'] = $val[csf('gsm')];
			$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['gsm'] = $val[csf('gsm')];
		}
		else
		{
			$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['gsm'] .= $val[csf('gsm')].",";
			$check_arr[$val[csf('batch_id')]]['gsm'] = $val[csf('gsm')];
		}
		$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['dia_width'] .= ($dtls_arr[$val[csf('batch_id')]]['dia_width']==$val[csf('dia_width')].",") ?  '' : $val[csf('dia_width')].",";
		$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['width_dia_type'] .= ($dtls_arr[$val[csf('batch_id')]]['width_dia_type']==$val[csf('width_dia_type')].",") ? '' : $val[csf('width_dia_type')].",";
		$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['roll_id'] .= ($dtls_arr[$val[csf('batch_id')]]['roll_id']==$val[csf('roll_id')].",") ? '' : $val[csf('roll_id')].",";
		$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['batch_qty'] += $val[csf('batch_qty')];
		$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['production_qty'] += $val[csf('production_qty')];
		$dtls_arr[$val[csf('batch_id')]][$val[csf('id')]]['no_of_roll'] += $val[csf('no_of_roll')];
		// echo $i++;
	}
	// echo "<pre>";
	// print_r($dtls_arr);die();

	$i=1;
	$total_batch_qty=0;
	$total_prod_qty=0;
	?> 
    	
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="800" rules="all">
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Cons Composition</th>
                    <th>GSM</th>                    
                    <th>Dia/Width</th>
                    <th>Dia Width Type</th>
                    <th> <?  if(str_replace(",","",$ex_data[1])==1){ echo "Roll No";} else {echo "Number Of Roll";} ?></th>
                    <th>Bacth Qty</th>
                    <th>Prod. Qty</th>
                   
                </tr>
            </thead>
            <tbody>
            	<? 
            	$checkBatchArr = array();
            	foreach($dtls_arr as $batch_id=>$id_row){  
					foreach($id_row as $id=>$row){  
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF"; 
					if(!in_array($batch_id,$checkBatchArr))
					{
						$total_batch_qty += $row["batch_qty"];	
						$checkBatchArr[] = $batch_id;
					}
					
					$total_prod_qty += $row["production_qty"];
					$dia_type='';
					$dia_type_id=array_unique(explode(",",chop($row['width_dia_type'],',')));
					foreach($dia_type_id as $dia_id)
					{	
						
						if($dia_type=="") $dia_type=$fabric_typee[$dia_id]; else $dia_type.=",".$fabric_typee[$dia_id];
					}
					?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='show_list_view("<? echo $row["id"]."_".$ex_data[1];?>","child_form_input_data","list_fabric_desc_container","requires/singeing_and_desizing_production_entry_controller");get_php_form_data("<? echo $ex_data[0]."_".$ex_data[2];?>", "populate_data_from_batch", "requires/singeing_and_desizing_production_entry_controller" );get_php_form_data("<? echo $row["id"];?>","mst_id_child_form_input_data","requires/singeing_and_desizing_production_entry_controller")'  style="cursor:pointer" >
                        <td width="30"><? echo $i; ?></td>
                        <td width="80"><p><? echo $row['const_composition']; ?></p></td>
                        <td width="80"><p><? echo chop($row['gsm'],','); ?></p></td>
                        <td width="70"><p><? echo chop($row['dia_width'],','); ?></p></td>
                        <td width="130"><p><? echo $dia_type;//$row[csf("width_dia_type")] ; ?></p></td>
                        <td width="80"><p>
						<? 
						  if(str_replace(",","",$ex_data[1])==1) { echo chop($row["roll_no"],','); } else { echo chop($row["no_of_roll"],','); }
						?>
                        </p></td>
                         <td align="right" width="80"><p><? echo number_format($row["batch_qty"],2); ?></p></td>
                        <td align="right" width="80"><p><? echo number_format($row["production_qty"],2); ?></p></td>
                   </tr>
                <? $i++; } }?>
                	<tfoot>
                            <th colspan="6" align="right">Sum</th>
                            <th><? echo number_format($total_batch_qty,2); ?></th>
                            <th id="total_production_qnty"><? echo number_format($total_prod_qty,2); ?></th>
                     </tfoot>
            </tbody>
        </table>
    <?
	exit();
}



if($action=="child_form_input_data")
{
	//print($data);
	$ex_data=explode('_',$data);
	$batch_id=$ex_data[0];
	$fabric_desc_arr=array();
	$prodData=sql_select("select id,detarmination_id from product_details_master where item_category_id=13");
	foreach($prodData as $row)
	{
		$fabric_desc_arr[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
	}
	$i=1;	
	    if($db_type==0)
		{
			$yarn_lot_data=sql_select("select   b.po_breakdown_id as po_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot, group_concat(distinct(a.brand_id)) as brand_id,group_concat(a.yarn_count,'**') AS yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where  a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
		}
		else if($db_type==2)
		{
			$yarn_lot_data=sql_select("select  b.po_breakdown_id as po_id, a.prod_id, LISTAGG(CAST(a.yarn_lot AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.yarn_lot) as yarn_lot, listagg(cast(a.yarn_count as varchar2(4000)),'**') within group (order by a.yarn_count) AS yarn_count, LISTAGG(a.brand_id,',') WITHIN GROUP ( ORDER BY a.brand_id) as brand_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
		}
		foreach($yarn_lot_data as $rows)
		{
			$yarn_lot=explode(",",$rows[csf('yarn_lot')]);
			$brand_id=explode(",",$rows[csf('brand_id')]);
			$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot']=implode(", ",array_unique($yarn_lot));
			$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['yarn_count']=$rows[csf('yarn_count')];
			$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['brand_id']=implode(", ",array_unique($brand_id));
		}
		
 
	$bat_pro=sql_select("select a.id as batch_id,b.batch_qnty,b.po_id,b.prod_id from pro_batch_create_mst a,pro_batch_create_dtls b,pro_fab_subprocess c where  a.id=b.mst_id  and c.batch_id=a.id and c.id=$ex_data[0] and a.is_deleted=0 and a.status_active=1  and c.entry_form=613");
 //echo "select a.id as batch_id,b.batch_qnty,b.po_id,b.prod_id from pro_batch_create_mst a,pro_batch_create_dtls b where  a.id=b.mst_id and a.id=".$batch_id." and a.is_deleted=0 and a.status_active=1  and b.batch_qnty>0";
		
		 foreach($bat_pro as $row)
		 {
			 //$prod_prev_qty_arr[$row[csf('batch_id')]][$row[csf('re_stenter_no')]]= $row[csf('production_qty')];
			 // $tot_prod_prev_qty+= $row[csf('production_qty')];
			  $chk_batch_arr[$row[csf('batch_id')]][$row[csf('prod_id')]]= $row[csf('po_id')];
		 }
		// print_r($chk_batch_arr);
	 $sql_result=sql_select("select a.id,b.id as dtls_id,a.batch_id,b.prod_id,b.const_composition,b.gsm,b.dia_width,b.width_dia_type,b.batch_qty,b.production_qty,b.roll_no,b.barcode_no,b.roll_id,b.no_of_roll,b.rate,b.amount from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.id=$ex_data[0] and entry_form=613 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
	// echo "select a.id,b.id as dtls_id,a.batch_id,b.prod_id,b.const_composition,b.gsm,b.dia_width,b.width_dia_type,b.batch_qty,b.production_qty,b.roll_no,b.barcode_no,b.roll_id,b.no_of_roll,b.rate,b.amount from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.id=$ex_data[0] and entry_form=30 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
	//if(count($sql_result)>0)
	//if(count($sql_result)>0)
	//{
	
		//$subprocess_dtls=return_field_value("id ", "pro_fab_subprocess_dtls", "id=$hidden_buyer_id  and status_active=1 and is_deleted=0","job_no");
	
	
		foreach($sql_result as $row)
		{
			//$desc=explode(",",$row[csf('item_description')]);
			
		 
				$cons_comps=$row[csf('const_composition')];
				$gsm=$row[csf('gsm')];
				$dia_width=$row[csf('dia_width')];
				$width_dia_type=$row[csf('width_dia_type')];
				$batch_qty=$row[csf('batch_qty')];
				$production_qty=$row[csf('production_qty')];
				//$roll_no=$row[csf('roll_no')];
				$roll_id=$row[csf('roll_id')];
				$prod_id=$row[csf('prod_id')];
				$update_id=$row[csf('dtls_id')];
				$po_id=$chk_batch_arr[$row[csf('batch_id')]][$row[csf('prod_id')]];
				$brand=$yarn_lot_arr[$prod_id][$po_id]['brand_id'];
				$lot=$yarn_lot_arr[$prod_id][$po_id]['lot'];
			
				//echo $po_id.'XXXXXXXXX'.$prod_id.', ';
				$brand_id=explode(',',$brand);
				$brand_value="";
				foreach($brand_id as $val)
				{
					if($val>0)
					{
					if($brand_value=='') $brand_value=$brand_name[$val]; else $brand_value.=", ".$brand_name[$val];
					}
				}
				$y_count_id=$yarn_lot_arr[$prod_id][$po_id]['yarn_count'];
				$count_id=array_unique(explode("**",$y_count_id));
				//print_r( $count_id).'aziz';
				//array_unique(explode(',',$y_count));
				$yarn_count_value='';
				foreach($count_id as $val)
				{
				if($val>0)
				{
				if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
				}
				}
				
				 $variable_production_roll=return_field_value("fabric_roll_level", "variable_settings_production", "variable_list=3 and item_category_id=50 and status_active=1 and is_deleted=0 and company_name=$ex_data[1]");
				   $page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =".$ex_data[1]." and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
				 
				
				 if(($page_upto_id==3 || $page_upto_id>3) && $ex_data[1]==1)//if($roll_maintained==1)
					{
						 $barcode=return_field_value("barcode_no","pro_roll_details","id=$roll_id and is_deleted=0 and status_active=1");
						 
						 $roll_no=$row[csf('roll_no')];
					}
					else
					{
						$roll_no=$row[csf('no_of_roll')];
					}
				
				?>
			<tr class="general" id="row_<? echo $i; ?>">
            <td width="40" id="sl_<? echo $i; ?>">
            <? //if(($page_upto_id==3 || $page_upto_id>3) && $ex_data[1]==1) { ?>
            <input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]" checked >
            <?  //}  ?>
             &nbsp; &nbsp;<? echo $i; ?></td>
            <td title="<? echo $cons_comps; ?>"><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:145px;" value="<? echo $cons_comps; ?>" disabled/></td>
            <td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $gsm; ?>" /></td>
            <td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td>
            <td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:50px;"  value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
            <input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
            </td>
			<td>
              <input type="text" name="txtknitting_density_<? echo $i; ?>" id="txtknitting_density_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px;"  value=""/>
             </td>
			 <td><input type="text" name="txtheat_set_density_<? echo $i; ?>" id="txtheat_set_density_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px;"  value=""  /></td>
             <td>
             <input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" style="width:35px;" 
             value="<? if(str_replace(",","",$ex_data[1])==1) echo $row[csf('roll_no')]; else echo $row[csf('no_of_roll')];?>"/>
             <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')];?>" class="text_boxes_numeric" />
             
             </td>
            
            <td><input type="text" name="txtsalesqnty_<? echo $i; ?>" id="txtsalesqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="" />
            <input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
            <input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
			<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('dtls_id')];?>" readonly />
            </td>
            <td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $row[csf('production_qty')];?>" onKeyUp="calculate_production_qnty();"/></td>
           
            
            
            <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $row[csf('rate')];?>" readonly/></td>
            <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $row[csf('amount')];?>"  readonly/><input type="hidden" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;"  value="<? echo $yarn_count_value;?>"  disabled /> <input type="hidden" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $brand_value;?>"  disabled/></td>
            
        </tr>
    <?
	$b_qty+= $row[csf('batch_qty')];
	$prod_qty+= $row[csf('production_qty')];
	$total_amount+= $row[csf('amount')];
	$i++;
   }
	
	?>
	 <tr>
        <td colspan="8" align="right"><b>Sum:</b> <? //echo $b_qty; ?> </td>
         <td align="right">  </td> 
         <td align="right" id="total_production_qnty"><? echo number_format($prod_qty,2); ?> </td>
         <td align="right" colspan="5" id="total_amount"><? echo number_format($total_amount,2); ?> </td>
     </tr>
   
	<?
	exit();
	
}




if($action=="mst_id_child_form_input_data")
{
	//temparature*steam*over_feed*speed_min
   $sql_result=sql_select("SELECT a.id, a.company_id,a.service_source, a.service_company, a.received_chalan,a.is_re_dyeing, a.issue_chalan, a.issue_challan_mst_id, a.process_end_date, a.production_date, a.process_start_date, a.batch_id,a.batch_no,a.process_id,a.next_process_id,a.advanced_prod_qty, a.end_hours, a.end_minutes, a.start_hours, a.start_minutes, a.temparature, a.steam,a.stretch, a.speed_min,a.over_feed, a.feed_in, a.pinning, a.speed_min, a.floor_id, a.machine_id,a.result, a.shift_name,a.chemical_name, a.remarks,a.booking_no 
   from pro_fab_subprocess a where a.id=$data and a.entry_form=613 and a.status_active=1 and a.is_deleted=0");
   
   $variable_production_roll=return_field_value("fabric_roll_level", "variable_settings_production", "variable_list=3 and item_category_id=50 and status_active=1 and is_deleted=0 and company_name=".$sql_result[0][csf('company_id')]."");
    $page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =".$sql_result[0][csf('company_id')]." and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
  $trims_weight=return_field_value("total_trims_weight","pro_batch_create_mst","company_id =".$sql_result[0][csf('company_id')]." and id=".$sql_result[0][csf('batch_id')]." and is_deleted=0 and status_active=1");
 $is_re_dyeing=$sql_result[0][csf("is_re_dyeing")];
 
   $sql_re_stent=sql_select("select max(re_stenter_no) as re_stenter_no_max,min(re_stenter_no) as re_stenter_no_min from pro_fab_subprocess where entry_form=613 and batch_no='".$sql_result[0][csf('batch_no')]."' and status_active = 1 and is_deleted = 0");
    $re_slitting_no_max=$sql_re_stent[0][csf('re_stenter_no_max')];
   // $re_slitting_no_min=$sql_re_stent[0][csf('re_stenter_no_min')];

	if($re_slitting_no_max)
	{
		$re_slitting_from = $re_slitting_no_max;
	}else{
		$re_slitting_from = "0";
	}
	
	echo "document.getElementById('roll_maintained').value	= '".$variable_production_roll."';\n";
	echo "document.getElementById('page_upto').value	= '".$page_upto_id."';\n";
	echo "document.getElementById('txt_issue_chalan').value	= '".$sql_result[0][csf('issue_chalan')]."';\n";
	echo "document.getElementById('txt_advance_prod').value	= '".$sql_result[0][csf('advanced_prod_qty')]."';\n";
	echo "document.getElementById('cbo_next_process').value	= '".$sql_result[0][csf('next_process_id')]."';\n";
	echo "document.getElementById('txt_issue_mst_id').value	= '".$sql_result[0][csf('issue_challan_mst_id')]."';\n";
	echo "document.getElementById('cbo_service_source').value	= '".$sql_result[0][csf('service_source')]."';\n";
	echo "load_drop_down( 'requires/singeing_and_desizing_production_entry_controller',".$sql_result[0][csf("service_source")]."+'**'+ document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
	echo "document.getElementById('cbo_service_company').value	= '".$sql_result[0][csf('service_company')]."';\n";
	echo "document.getElementById('txt_recevied_chalan').value	= '".$sql_result[0][csf('received_chalan')]."';\n";
	echo "document.getElementById('txt_process_end_date').value 	= '".change_date_format($sql_result[0][csf("process_end_date")])."';\n";
	echo "document.getElementById('txt_process_date').value 	= '".change_date_format($sql_result[0][csf("production_date")])."';\n";
	echo "document.getElementById('txt_process_start_date').value 	= '".change_date_format($sql_result[0][csf("process_start_date")])."';\n";
	if($is_re_dyeing==1)
		{
		echo "document.getElementById('re_checkbox').checked=true;\n";
		}
		else
		{
			echo "document.getElementById('re_checkbox').checked=false;\n";
		}
		 
	echo "document.getElementById('cbo_sub_process').value 			= '".$sql_result[csf("process_id")]."';\n";
	echo "document.getElementById('txt_batch_ID').value 			= '".$sql_result[0][csf("batch_id")]."';\n";
	 
	$minute=''; $hour='';
	if ($sql_result[0][csf("end_hours")] != '' && $sql_result[0][csf("end_minutes")] != '')
	{
		$hour=str_pad($sql_result[0][csf("end_hours")],2,'0',STR_PAD_LEFT);
	    $minute=str_pad($sql_result[0][csf("end_minutes")],2,'0',STR_PAD_LEFT);
	}
	
	echo "document.getElementById('txt_end_hours').value	= '".$hour."';\n";
	echo "document.getElementById('txt_end_minutes').value	= '".$minute."';\n";
	$start_hour=str_pad($sql_result[0][csf("start_hours")],2,'0',STR_PAD_LEFT);
	$start_minute=str_pad($sql_result[0][csf("start_minutes")],2,'0',STR_PAD_LEFT);
	echo "document.getElementById('txt_start_hours').value	= '".$start_hour."';\n";
	echo "document.getElementById('txt_start_minutes').value = '".$start_minute."';\n";
	echo "document.getElementById('txt_steam').value	= '".$sql_result[0][csf('steam')]."';\n";
	echo "document.getElementById('txt_temparature').value	= '".$sql_result[0][csf('temparature')]."';\n";
	echo "document.getElementById('txt_speed').value	= '".$sql_result[0][csf('speed_min')]."';\n";
	echo "document.getElementById('txt_feed').value	= '".$sql_result[0][csf('over_feed')]."';\n";
	echo "document.getElementById('txt_reslitting_no').value	= '".$re_slitting_from."';\n";
	echo "document.getElementById('re_reslitting_from').value	= '".$re_slitting_from."';\n";
	
	echo "$('#txt_reslitting_no').attr('readonly','readonly');\n";
	
	//echo "document.getElementById('txt_temparature').value	= '".$r_batch[csf("temparature")]."';\n";
	//echo "document.getElementById('txt_stretch').value	= '".$r_batch[csf("stretch")]."';\n";
	//echo "document.getElementById('txt_feed').value	= '".$r_batch[csf("over_feed")]."';\n";
	//echo "document.getElementById('txt_feed_in').value	= '".$r_batch[csf("feed_in")]."';\n";
	//echo "document.getElementById('txt_pinning').value	= '".$r_batch[csf("pinning")]."';\n";
	//echo "document.getElementById('txt_speed').value	= '".$r_batch[csf("speed_min")]."';\n";
	if($sql_result[0][csf('service_source')]==1)
	{
	echo "load_drop_down('requires/singeing_and_desizing_production_entry_controller', '".$sql_result[0][csf('service_company')]."', 'load_drop_floor', 'floor_td' );\n";
	}
	echo "load_drop_down( 'requires/singeing_and_desizing_production_entry_controller', document.getElementById('cbo_service_company').value+'**'+".$sql_result[0][csf("floor_id")].", 'load_drop_machine', 'machine_td' );\n";
	echo "document.getElementById('cbo_floor').value = '".$sql_result[0][csf("floor_id")]."';\n";
	echo "document.getElementById('cbo_machine_name').value = '".$sql_result[0][csf("machine_id")]."';\n";
	echo "document.getElementById('cbo_result_name').value = '".$sql_result[0][csf("result")]."';\n";
	echo "document.getElementById('cbo_shift_name').value	= '".$sql_result[0][csf("shift_name")]."';\n";
 
	 
	echo "document.getElementById('txt_booking_no').value	= '".$sql_result[0][csf("booking_no")]."';\n";

	echo "document.getElementById('txt_update_id').value	= ".$data.";\n";
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pro_fab_subprocess',1,1);\n";
	//echo "set_button_status(1, permission, 'fnc_pro_fab_subprocess',1,1);\n";
	$sql_result_dtls=sql_select("select b.currency_id,b.exchange_rate from pro_fab_subprocess_dtls b where b.mst_id=$data   and b.status_active=1 and b.is_deleted=0");
	echo "document.getElementById('hidden_exchange_rate').value	= '".$sql_result_dtls[0][csf("currency_id")]."';\n";
	echo "document.getElementById('hidden_currency').value	= '".$sql_result_dtls[0][csf("exchange_rate")]."';\n";
	exit();
}

if ($action=="service_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
     
	<script>
	var permission="<? echo $_SESSION['page_permission']; ?>";
	function js_set_value(booking_no)
	{
		data=booking_no.split("_");
		//alert(data[4])
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	
    </script>

	</head>

	<body>
	    <div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="900">
	            <thead>
	            	<tr>
	                    <th colspan="3"> </th>
	                    <th>
	                      <?
	                       echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
	                      ?>
	                    </th>
	                    <th colspan="3"></th>
	                </tr>
	                <tr>
	                    <th width="160">Company Name</th>
	                    <th width="160">Buyer Name</th>
	                    <th width="120">Booking No</th>
	                    <th width="200">Date Range</th>
	                    <th><input type="reset" id="rst" class="formbutton" style="width:100px" onClick="reset_form('searchorderfrm_1','search_div','','','')" ></th>   
	                </tr>                	 
	            </thead>
	            <tbody>
	                <tr>
	                    <td align="center"> <input type="hidden" id="selected_booking">
	                    <? 
	                   		echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and id=".$cbo_company_id." order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'singeing_and_desizing_production_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
	                    ?>
	                    </td>
	                    <td id="buyer_td"  align="center">
	                    <? 
	                    	echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" );
	                    ?>	
	                    </td>
	                    <td align="center">
	                   		<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:100px"  placeholder="Write Booking No">	
	                    </td>
	                  
	                    <td  align="center">
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
	                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
	                    </td> 
	                    <td align="center">
	                    	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_no').value+'_'+'<?php echo $supplier_id."_".$process_id; ?>', 'create_booking_search_list_view', 'search_div', 'singeing_and_desizing_production_entry_controller', 'setFilterGrid(\'table_body\',-1)')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                	<td  align="center" height="40" valign="middle" colspan="6"><? echo load_month_buttons(1);  ?></td>
	                </tr>
	            </tbody>
	        </table>   
	    	
	    </form>
	    </div>
	    <div id="search_div" style="margin-top:10px;"> </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}


if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$date_form=$data[2];
	$date_to=$data[3];
	$search_catgory=$data[4];
	$booking_no=$data[5];
	$supplier_id=$data[6];
	$process_id=$data[7];
	$sql_cond="";
	if ($company_id!=0) $sql_cond =" and a.company_id='$company_id'"; else { echo "Please Select Company First."; die; }
	if ($buyer_id!=0) $sql_cond .=" and a.buyer_id='$buyer_id'";
	
	if($db_type==0)
	{
		if ($date_form!="" &&  $date_to!="")  $sql_cond .= "and a.booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
	}
	if($db_type==2)
	{
		if ($date_form!="" &&  $date_to!="") $sql_cond .= "and a.booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'";
	}
	
	
	if($booking_no!="") $sql_cond .=" and a.booking_no_prefix_num=$booking_no";
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_no_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');

	$sql_booking= sql_select("select f.lib_yarn_count_deter_id,d.pre_cost_fabric_cost_dtls_id,sum(d.amount) as amount, sum(d.wo_qnty) as wo_qnty,d.booking_no  from wo_pre_cost_fab_conv_cost_dtls e,wo_pre_cost_fabric_cost_dtls f, wo_booking_dtls d,wo_booking_mst a, wo_po_details_master b where e.job_no=f.job_no and f.id=e.fabric_description and e.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=a.booking_no and a.job_no=b.job_no and a.booking_type=3 and a.supplier_id=$supplier_id and  a.status_active=1 and a.is_deleted=0 and d.process=$process_id $sql_cond group by d.booking_no,d.pre_cost_fabric_cost_dtls_id,f.lib_yarn_count_deter_id ");
	$booking_determination_rate=array();
	foreach($sql_booking as $val)
	{
		$booking_determination_rate[$val[csf('booking_no')]][$val[csf('lib_yarn_count_deter_id')]]['wo_qnty']+=$val[csf('wo_qnty')];
		$booking_determination_rate[$val[csf('booking_no')]][$val[csf('lib_yarn_count_deter_id')]]['amount']+=$val[csf('amount')];
	}
	
	$sql= "select   sum(d.amount)/ sum(d.wo_qnty) as rate,a.booking_no_prefix_num, a.booking_no ,a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, b.job_no_prefix_num,a.currency_id, a.exchange_rate  from wo_booking_dtls d,wo_booking_mst a, wo_po_details_master b where d.booking_no=a.booking_no and a.job_no=b.job_no and a.booking_type=3 and a.supplier_id=$supplier_id and  a.status_active=1 and a.is_deleted=0 and d.process=$process_id $sql_cond group by a.booking_no_prefix_num, a.booking_no ,a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, b.job_no_prefix_num ,a.currency_id, a.exchange_rate order by a.booking_no"; 
	//echo $sql;
	?>
    <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="970">
    	<thead>
        	<tr>
            	<th width="40">SL</th>
            	<th width="50">Booking No</th>
                <th width="70">Booking Date</th>
                <th width="60">Company</th>
                <th width="60">Buyer</th>
                <th width="60">Job No</th>
                <th width="200">PO number</th>
                <th width="120">Item Category</th>
                <th width="110">Fabric Source</th>
                <th>Supplier</th>  
            </tr>
        </thead>
    </table>
    <div id="scroll_body" style="width:990px; max-height:350; overflow-y:scroll" align="center">
    <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="970" id="table_body">
        <tbody>
        <?
		$sql_result=sql_select($sql);
		$i=1;
		foreach($sql_result as $row)
		{
			//$deter_amount_arr=array();
			//$deter_amount_arr[$deter_id]
			$determination_data='';
			foreach($booking_determination_rate[$row[csf("booking_no")]] as $deter_id=>$deter_val)
			{
				$determination_data.=$deter_id."*".$deter_val['amount']/$deter_val['wo_qnty']."**";
			}
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("booking_no")]."_".$row[csf("currency_id")]."_".$row[csf("exchange_rate")]."_".$determination_data; ?>')" style="cursor:pointer;">
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="50" align="center"><p><? echo $row[csf("booking_no_prefix_num")]; ?>&nbsp;</p></td>
                <td width="70" align="center"><p><? if($row[csf("booking_date")]!="" && $row[csf("booking_date")]!="0000-00-00") echo change_date_format($row[csf("booking_date")]); ?>&nbsp;</p></td>
                <td width="60"><p><? echo $comp_arr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                <td width="60" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?>&nbsp;</p></td>
                <td width="200"><p>
				<?
				$po_id_arr=array_unique(explode(",",$row[csf("po_break_down_id")]));
				$all_po="";
				foreach($po_id_arr as $po_id)
				{
					$all_po.=$po_no_arr[$po_id].",";
				}
				$all_po=chop($all_po," , ");
				echo $all_po; 
				?>&nbsp;</p></td>
                <td width="120"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                <td width="110"><p><? echo $fabric_source[$row[csf("fabric_source")]]; ?>&nbsp;</p></td>
                <td><p><? echo $suplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>  
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>
    <?
}


?>