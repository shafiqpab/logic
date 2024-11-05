<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
  echo create_drop_down( "cbo_working_location",130,"select id, location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0","id,location_name",1,"-Select Location-",$selected,"",1,"","","","","");
  exit();
}

$new_conn=integration_params(2);

if ($action=="load_drop_down_location_hrm")
{
  echo create_drop_down( "cbo_location_name",130,"select id,location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0","id,location_name",1,"-- Select Location --",$selected,"load_drop_down('bundle_linking_operation_controller', this.value,'load_drop_down_division','division_td_hrm');","","","","","","",$new_conn );
  exit();
}

if ($action=="load_drop_down_division")
{
    echo create_drop_down("cbo_division_name",130,"select id,division_name from lib_division where location_id=$data and status_active=1 and is_deleted=0","id,division_name",1,"-- Select Division --",$selected,"load_drop_down('bundle_linking_operation_controller',this.value,'load_drop_down_department','department_td_hrm');","","","","","","",$new_conn );
	exit();
}

if ($action=="load_drop_down_department")
{
   echo create_drop_down("cbo_dept_name",130,"select id,department_name from lib_department where division_id=$data and status_active=1 and is_deleted=0","id,department_name",1,"-- Select Department --",$selected,"load_drop_down( 'bundle_linking_operation_controller',this.value, 'load_drop_down_section', 'section_td_hrm');","","","","","","",$new_conn );
   exit();
}

if ($action=="load_drop_down_section")
{
   echo create_drop_down("cbo_section_name",130,"select id,section_name from lib_section where department_id=$data and status_active=1 and is_deleted=0","id,section_name",1,"-- Select Section --",$selected,"","","","","","","",$new_conn );
   exit();
}

if ($action=="operator_popup")
{
	echo load_html_head_contents("Operator Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(str)
		{
			//alert(str);
			$("#hidden_emp_number").val(str);
			parent.emailwindow.hide(); 
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:950px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:950px;">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" width="870" border="1" rules="all" class="rpt_table" align="center">
	                <thead>
		                <th width="150">Company</th>
		                <th width="130">Location</th>
		                <th width="130">Division</th>
		                <th width="130">Department</th>
		                <th width="130">Section</th>
		            	<th width="100">Employee Code</th>
		                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /><input type="hidden" id="hidden_emp_number" /></th>           
		            </thead>
	                <tr class="general">
	                    <td><? echo create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name", "id,company_name", 1, "--- Select Company ---", $selected,"load_drop_down('bundle_linking_operation_controller', this.value,'load_drop_down_location_hrm','location_td_hrm');","","","","","","",$new_conn ); ?></td>
	                    <td id="location_td_hrm"><? echo create_drop_down( "cbo_location_name", 130, $blank_array,"", 1, "-- Select Location --", $selected ); ?></td>
		                <td id="division_td_hrm"><? echo create_drop_down( "cbo_division_name", 130,$blank_array ,"", 1, "-- Select Division --", $selected ); ?></td> 
		                <td id="department_td_hrm"><? echo create_drop_down( "cbo_dept_name", 130,$blank_array ,"", 1, "-- Select Department --", $selected ); ?></td>   
		                <td id="section_td_hrm"><? echo create_drop_down( "cbo_section_name", 130,$blank_array ,"", 1, "-- Select Section --", $selected ); ?></td>
		           
		                <td><input type="text" id="src_emp_code" name="src_emp_code" class="text_boxes" style="width:90px;" ></td> 
		                <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_division_name').value+'_'+document.getElementById('cbo_dept_name').value+'_'+document.getElementById('cbo_section_name').value+'_'+document.getElementById('src_emp_code').value, 'create_emp_search_list_view','search_div','bundle_linking_operation_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
		                </td>
		            </tr> 
	           </table>
	           <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_emp_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$location = $ex_data[1];
	$division = $ex_data[2];
	$department = $ex_data[3];
	$section = $ex_data[4];
	$emp_code = $ex_data[6];

 	//$sql_cond="";
	if( $company!=0 ) $company=" and company_id=$company"; else $company="";
	if( $location!=0 ) $location=" and location_id=$location"; else $location="";
	if( $division!=0 ) $division=" and division_id=$division"; else $division="";
	if( $department!=0 ) $department=" and department_id=$department"; else $department="";
	if( $section!=0 ) $section=" and section_id=$section"; else $section="";
	if( $emp_code!='' ) $emp_code=" and emp_code=$emp_code"; else $emp_code="";
	
	if($db_type==2 || $db_type==1 )
	{

    	$sql = "select emp_code,id_card_no,(first_name||' '||middle_name|| ' ' || last_name) as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
    }
	else if($db_type==0)
	{
		$sql = "select emp_code,id_card_no, concat(first_name,' ',middle_name,' ',last_name) as emp_name, designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name',$new_conn);
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name',$new_conn);
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name',$new_conn);
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name',$new_conn);
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name',$new_conn);
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation',$new_conn);

	$arr=array(3=>$designation_arr,4=>$company_arr,5=>$location_arr,6=>$division_arr,7=>$department_arr,8=>$section_arr);
		
	echo create_list_view("list_view", "Emp Code,ID Card,Employee Name,Designation,Company,Location,Division,Department,Section", "65,65,100,90,110,140,80,100,80","930","250",0, $sql, "js_set_value", "emp_code,id_card_no,emp_name", "", 1, "0,0,0,designation_id,company_id,location_id,division_id,department_id,section_id", $arr , "emp_code,id_card_no,emp_name,designation_id,company_id,location_id,division_id,department_id,section_id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0',"","",$new_conn) ;
	exit();
}

if($action=="populate_operator_data")
{
	if($db_type==2 || $db_type==1 )
	{
    	$sql = "select emp_code, id_card_no,(first_name||' '||middle_name|| ' ' || last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0 and id_card_no='$data'";
    }
	else if($db_type==0)
	{
		$sql = "select id_card_no, concat(first_name,' ',middle_name,' ',last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0 and id_card_no='$data'";
	}
	//echo $sql;
	$result = sql_select($sql,'',$new_conn);	
	
	foreach ($result as $row)
	{ 
		echo "document.getElementById('txt_operator_id').value 			= '".$row[csf("id_card_no")]."';\n";
		echo "document.getElementById('txt_operator_name').value 		= '".$row[csf("emp_name")]."';\n";
	}
	exit();
}

if($action=='populate_mst_data')
{
	$exData=explode("_",$data);
	$barcode_no="'".implode("','",explode(",",$exData[0]))."'";
	//echo "select mst_barcode_no from ppl_cut_lay_bundle_operation where status_active=1 and is_deleted=0 and barcode_no in ($barcode_no)";
	$result=sql_select("select mst_barcode_no from ppl_cut_lay_bundle_operation where status_active=1 and is_deleted=0 and barcode_no in ($barcode_no)");

	$datastr="";
	foreach ($result as $row)
	{ 
		$datastr.=$row[csf('mst_barcode_no')].',';
	}
	$barcode_noCond="";
	$barcode_noCond="'".implode("','",array_unique(array_filter(explode(",",$datastr))))."'";
	
	$data_array=sql_select("select a.company_id, a.location_id, a.production_source, a.working_company_id, a.working_location_id, a.floor_id, a.operator_id, a.sewing_line
	from pro_gmts_delivery_mst a, pro_garments_production_dtls b, ppl_cut_lay_mst c
	where b.barcode_no in ($barcode_noCond) and a.id=b.delivery_mst_id and  b.cut_no=c.cutting_no and a.production_type=55 and  b.production_type=55 and a.status_active=1  and  b.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0
	group by a.company_id, a.location_id, a.production_source, a.working_company_id, a.working_location_id, a.floor_id, a.operator_id,  a.sewing_line");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		$workingloaction=create_drop_down( "cbo_working_location",130,"select id, location_name from lib_location where company_id='".$row[csf('working_company_id')]."' and status_active=1 and is_deleted=0","id,location_name",1,"-Select Location-",$selected,"",1,"","","","","");
		echo "document.getElementById('working_location_td').innerHTML = '".$workingloaction."';\n";
		
		//echo "load_drop_down( 'requires/bundle_linking_operation_controller', '".$row[csf('working_company_id')]."', 'load_drop_down_location', 'working_location_td' );\n";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("working_location_id")]."';\n";
		exit();
	}
}

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($shortName,$ryear,$lot_prifix)=explode('-',$lot_ratio);
	if($ryear=="") $ryear=date("Y",time()); else $ryear=("20$ryear")*1;
	//echo $company_id;die;
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
			var strs=str.split("__");
			//var strs=strs[0];
			/*if( $("#hidden_lot_ratio").val()!="" &&   $("#hidden_lot_ratio").val()!=strs[1] ) {
				alert("Lot Ratio Mixed Not Allow.Previous Selected Lot Ratio "+strs[1]);
				return;
			}*/

			toggle( document.getElementById( 'search' + strs[0] ), '#FFFFCC' );
			
			if( jQuery.inArray( strs[1], selected_id ) == -1 ) {
				selected_id.push( strs[1] );
				$('#hidden_lot_ratio').val( strs[1] );	
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == strs[1] ) break;
				}
				selected_id.splice( i, 1 );
				
				/*if(selected_id.length==0 && $('#hidden_lot_ratio_pre').val()=="")
					$('#hidden_lot_ratio').val('');*/

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
			//return;
			parent.emailwindow.hide();
			//alert($('#hidden_bundle_nos').val())
		}
		
		function reset_hide_field()
		{
			selected_id = new Array();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:810px;">
			<legend></legend>           
	            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th width="140">Company</th>
	                    <th width="60">Lot Ratio Year</th>
	                    <th width="90">Job No</th>                  
	                    <th width="90" class="must_entry_caption">Ratio No</th>
	                    <th width="90">Bundle No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                            <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos"> 
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td><? echo create_drop_down( "cbo_company_name",140, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1,"-- Select --", $company_id, "",0 ); ?></td>
	                    <td><? echo create_drop_down( "cbo_lot_year", 60, $year,'', "", '-- Select --',$ryear, "" ); ?></td>  				
	                    <td><input type="text" style="width:80px" class="text_boxes" name="txt_job_no" id="txt_job_no" />	</td> 				
	                    <td><input type="text" name="txt_lot_no" id="txt_lot_no" style="width:80px" value="<?php if($lot_prifix) echo $lot_prifix*1; ?>" class="text_boxes" /></td>
	                    <td>
                        	<input type="hidden" name="hidden_lot_ratio" value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio"  />
                        	<input type="hidden" name="hidden_lot_ratio_pre"  value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio_pre"  />
				            <input type="text" name="bundle_no" id="bundle_no" style="width:80px" class="text_boxes" />
	                    </td>  		
	            		<td>
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($ticketNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_bundle_search_list_view','search_div','bundle_linking_operation_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:70px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		if($("#hidden_lot_ratio").val()!="")
		{
			show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_bundle_search_list_view','search_div','bundle_linking_operation_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')
		}
	</script>
	</html>
	<?
	exit();
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$company 				= $ex_data[0];
	$selectedBuldle			="'".implode("','",explode(",",$ex_data[2]))."'";
	$job_no					=$ex_data[3];
	$lot_no					=$ex_data[4];
	$syear 					= substr($ex_data[5],2);
	$full_lot_no			=$ex_data[7];
	
	if(trim($ex_data[1])) $bundle_no_cond=" and c.bundle_no='".trim($ex_data[1])."'";

	/*if( trim($ex_data[0])=='' || trim($ex_data[0])==0)
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select  Company First. </h2>";
		exit();
	}

	if( trim($ex_data[1])=='' && trim($ex_data[3])==''  && trim($ex_data[4])=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Job No Or  Lot No Or Bundle No. </h2>";
		exit();
	} */
	
	$cutCon=''; $receiveCon=''; $cutCon='';
	if ($lot_no != '') $cutCon = " and c.cut_no like'%".$lot_no."%'";
    //if ($full_lot_no != '') $cutpCon = " and b.cut_no='".$full_lot_no."'";
	
	if($job_no!='') $jobCon=" and f.job_no like '%$job_no%'"; else $jobCon="";
	if(str_replace("'","",$selectedBuldle)!="") $selected_bundle_cond=" and b.barcode_no not in (".$selectedBuldle.")";
	//echo "SELECT a.barcode_no, a.bundle_no from pro_garments_production_mst b, pro_garments_production_dtls a where b.id=a.mst_id and a.production_type=55 and b.production_type=55 and b.status_active=1 and b.is_deleted=0 $bundle_no_cond $cutpCon "; 
	$output_bundle_arr=return_library_array("SELECT a.barcode_no, a.bundle_no from pro_garments_production_mst b, pro_garments_production_dtls a where b.id=a.mst_id and a.production_type=55 and b.production_type=55 and b.status_active=1 and b.is_deleted=0 $bundle_no_cond $cutpCon ", 'barcode_no', 'bundle_no');
	$scanned_bundle_arr = return_library_array("select ticket_no, ticket_no from pro_linking_operation_dtls where status_active=1 and is_deleted=0 $scnbundle_nos_cond", 'ticket_no', 'ticket_no');
	//print_r($output_bundle_arr);
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		//$scanned_bundle_arr[$bn]=$bn;	
		//echo $bn.'<br>';
	}
	//unset($scanned_bundle_arr);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1110" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="120">Gmts Item</th>
            <th width="120">Country</th>
            <th width="120">Color</th>
            <th width="50">Size</th>
            <th width="90">Lot Ratio No</th>
            <th width="85">Bundle No</th>
            <th width="80">Bundle Qty.</th>
            <th width="120">Operation</th>
            <th>Ticket No</th>
        </thead>
	</table>
	<div style=" width:1130px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1110" class="rpt_table" id="tbl_list_search">  
        	<?
			$i=1;
			/*$sql="select d.cutting_no, a.bundle_no, a.order_id, b.mst_barcode_no, b.barcode_no, b.operation_id, c.id, c.job_no_mst, c.po_break_down_id, c.country_id, c.color_number_id, c.item_number_id, c.size_number_id, a.size_qty as bundle_qty
			from ppl_cut_lay_bundle a, ppl_cut_lay_bundle_operation b, wo_po_color_size_breakdown c, ppl_cut_lay_mst d, ppl_cut_lay_dtls e
 			where d.id=a.mst_id and d.entry_form=322 and d.id=e.mst_id and e.id=a.dtls_id and e.id=b.dtls_id and a.barcode_no=b.mst_barcode_no and a.order_id=c.po_break_down_id 
			and a.size_id=c.size_number_id and a.country_id=c.country_id and e.color_id=c.color_number_id and c.item_number_id=e.gmt_item_id
			$selected_bundle_cond $jobCon $cutCon $bundle_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
            group by d.cutting_no, a.bundle_no, a.order_id, b.mst_barcode_no, b.barcode_no, b.operation_id, c.id, c.job_no_mst, c.po_break_down_id, c.country_id, c.color_number_id, c.item_number_id, c.size_number_id, a.size_qty  
                order by c.job_no_mst, length(a.bundle_no) asc, a.bundle_no asc";*/
				
			$sql="SELECT a.floor_id, a.sewing_line, b.mst_barcode_no, b.operation_id, b.barcode_no, max(c.id) as prdid, c.cut_no as cutting_no, c.bundle_no, sum(c.production_qnty) as production_qnty, d.id, d.job_no_mst, d.country_id, d.color_number_id, d.item_number_id, d.size_number_id, e.id as order_id, e.po_number, f.buyer_name, f.style_ref_no 
			
			from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f, ppl_cut_lay_bundle_operation b where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.barcode_no=b.mst_barcode_no and a.production_type=55 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $selected_bundle_cond $jobCon $cutCon $bundle_no_cond 
	 group by a.floor_id, a.sewing_line, b.mst_barcode_no, b.operation_id, b.barcode_no, c.cut_no, c.bundle_no, d.id, d.job_no_mst, d.country_id, d.color_number_id, d.item_number_id, d.size_number_id, e.id, e.po_number, f.buyer_name, f.style_ref_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
			//echo $sql;
			$result = sql_select($sql);	
			foreach ($result as $val)
			{
				//$po_id_arr[$val[csf('po_break_down_id')]] 		=$val[csf('po_break_down_id')];
				$color_id_arr[$val[csf('color_number_id')]] 	=$val[csf('color_number_id')];
				$size_id_arr[$val[csf('size_number_id')]] 		=$val[csf('size_number_id')];
				$country_id_arr[$val[csf('country_id')]] 		=$val[csf('country_id')];
				$cutting_id_arr[$val[csf('mst_id')]] 			=$val[csf('mst_id')];
				$operation_id_arr[$val[csf('operation_id')]] 	=$val[csf('operation_id')];
				$bundle_no_arr[$val[csf('bundle_no')]] 			="'".$val[csf('bundle_no')]."'";
			}

			$size_arr=return_library_array( "select id, size_name from lib_size where id in (".implode(',', $size_id_arr).")",'id','size_name');
			$color_arr=return_library_array( "select id, color_name from lib_color where id in (".implode(',', $color_id_arr).")", "id", "color_name");
			$country_arr=return_library_array( "select id, country_name from lib_country where id in (".implode(',', $country_id_arr).")",'id','country_name');
			//$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where id in (".implode(',', $po_id_arr).")",'id','po_number');
			
			$operation_name_arr=return_library_array( "select id, operation_name from lib_sewing_operation_entry where id in (".implode(',', $operation_id_arr).")",'id','operation_name');
			$bundleQtyArr=array();
			$bundleQtySql=sql_select("select barcode_no, size_qty from ppl_cut_lay_bundle where bundle_no in (".implode(',', $bundle_no_arr).") and status_active=1 and is_deleted=0");
			//echo "select barcode_no, size_qty from ppl_cut_lay_bundle where bundle_no in (".implode(',', $bundle_no_arr).") and status_active=1 and is_deleted=0";
			foreach($bundleQtySql as $brow)
			{
				$bundleQtyArr[$brow[csf('barcode_no')]]=$brow[csf('size_qty')];
			}
			unset($bundleQtySql);
			
			if(count($result)==0) { echo "<h2 style='color:#D00; text-align:center;'>Linking Output Not Found. </h2>"; }

			foreach ($result as $row)
			{  
				if($scanned_bundle_arr[$row[csf('barcode_no')]]=="" && $output_bundle_arr[$row[csf('mst_barcode_no')]]!="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
					?>
                    <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value('<?=$i.'__'.$row[csf('barcode_no')].'__'.$row[csf('cutting_no')]; ?>')"> 
                        <td width="30" align="center"><?=$i; ?>
                        	<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" alue="<?php echo $row[csf('barcode_no')]; ?>"/>
                            <input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $i; ?>" value="<?php echo $row[csf('cutting_no')]; ?>"/>
                        </td>
                        <td width="50" align="center"><?=$year; ?></td>
                        <td width="50" align="center" title="<?=$row[csf('job_no_mst')]; ?>"><?=$job*1; ?></td>
                        <td width="90" style="word-break:break-all"><?=$row[csf('po_number')]; ?></td>
                        <td width="120" style="word-break:break-all"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
                        <td width="120" style="word-break:break-all"><?=$country_arr[$row[csf('country_id')]]; ?></td>
                        <td width="120" style="word-break:break-all"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
                        <td width="50" style="word-break:break-all"><?=$size_arr[$row[csf('size_number_id')]]; ?></td>
                        <td width="90" style="word-break:break-all"><?=$row[csf('cutting_no')]; ?></td>
                        <td width="85" style="word-break:break-all"><?=$row[csf('bundle_no')]; ?></td>
                        <td width="80" align="right"><?=$bundleQtyArr[$brow[csf('mst_barcode_no')]]; ?></td>
                        <td width="120" style="word-break:break-all"><?=$operation_name_arr[$row[csf('operation_id')]]; ?></td>
                        <td style="word-break:break-all"><?=$row[csf('barcode_no')]; ?></td>
                    </tr>
                    <?
                    $i++;
				}
				else
				{
					//echo "<h2 style='color:#D00; text-align:center;'>Linking Output Not Found. </h2>";
				}
			}
        	?>
        </table>
    </div>
    <table width="1000">
        <tr>
            <td align="center" >
               <span style="float:left;"><input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" />Check / Uncheck All </span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
	exit();	
}

if($action=="populate_bundle_data")
{
	$ex_data = explode("**", $data);
    $bundle = explode(",", $ex_data[0]);
    $mst_id = $ex_data[2];
    $bundle_nos = "'" . implode("','", $bundle) . "'";
    $vscan=$ex_data[4];
    $source_cond=$ex_data[5];
	if($mst_id==0) $mst_id="";
	
	if($mst_id!="" && str_replace("'",'',$bundle_nos)=='')
	{
		$bundle_nos='';
		$sql_bundle="select id, rate, amount, ticket_no from pro_linking_operation_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by id";
		
		$sql_bundle_res=sql_select($sql_bundle); $bundle_count=0; $ticketDataArr=array();
		foreach($sql_bundle_res as $row)
		{
			$bundle_count++;
			$bundle_nos.="'".$row[csf("ticket_no")]."',";
			$ticketDataArr[$row[csf("ticket_no")]]['id']=$row[csf("id")];
			$ticketDataArr[$row[csf("ticket_no")]]['rate']=$row[csf("rate")];
			$ticketDataArr[$row[csf("ticket_no")]]['amount']=$row[csf("amount")];
		}
		unset($sql_bundle_res);
	}
	
	$bundle_nos=implode(",",array_filter(explode(',',$bundle_nos)));
	//echo $bundle_nos;
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nosCond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nosCond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nosCond.=" b.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.ticket_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nosCond=chop($bundle_nosCond,'or ');
		$bundle_nosCond.=")";
		
		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";
		
		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nosCond=" and b.barcode_no in ($bundle_nos)";
		$scnbundle_nos_cond=" and b.ticket_no in ($bundle_nos)";
		$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";
	}
	//echo $bundle_nos.'='.$scnbundle_nos_cond;
	//echo "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=55 and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond";
	
	//echo "select ticket_no, ticket_no from pro_linking_operation_dtls where status_active=1 and is_deleted=0 $scnbundle_nos_cond";
	$scanned_bundle_arr=array();
	// if($mst_id=="") $scanned_bundle_arr = return_library_array("select ticket_no, ticket_no as ticket_no2 from pro_linking_operation_dtls where status_active=1 and is_deleted=0 $scnbundle_nos_cond", 'ticket_no', 'ticket_no2');

	if($mst_id=="") { 
		$scanned_bundle_data = sql_select("SELECT SYS_NO, ticket_no from pro_linking_operation_mst a, pro_linking_operation_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $scnbundle_nos_cond");
		if(count($scanned_bundle_data)>0)
		{
			echo "10####This ticket has already been scanned. System ID no: ".$scanned_bundle_data[0]["SYS_NO"];die();
		}
	}

   //$scanned_bundle_arr = return_library_array("select b.bundle_no, b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=55 and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond", 'bundle_no', 'production_qnty');
   //die;
    $year_field = "";
    if ($db_type == 0) {
        $year_field = "YEAR(f.insert_date)";
    } else if ($db_type == 2) {
        $year_field = "to_char(f.insert_date,'YYYY')";
    }
     
    $last_operation=array();
    //$last_operation=gmt_production_validation_script( 4, 1);

    /*$sql="select d.cutting_no, a.bundle_no, a.order_id, b.mst_barcode_no, b.barcode_no, b.operation_id, c.id, c.job_no_mst, c.po_break_down_id, c.country_id, c.color_number_id, c.item_number_id, c.size_number_id, a.size_qty as bundle_qty
			from ppl_cut_lay_bundle a, ppl_cut_lay_bundle_operation b, wo_po_color_size_breakdown c, ppl_cut_lay_mst d, ppl_cut_lay_dtls e
 			where d.id=a.mst_id and d.entry_form=322 and d.id=e.mst_id and e.id=a.dtls_id and e.id=b.dtls_id and a.barcode_no=b.mst_barcode_no and a.order_id=c.po_break_down_id 
			and a.size_id=c.size_number_id and a.country_id=c.country_id and e.color_id=c.color_number_id and c.item_number_id=e.gmt_item_id
			and b.barcode_no in($bundle_nos) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
            group by d.cutting_no, a.bundle_no, a.order_id, b.mst_barcode_no, b.barcode_no, b.operation_id, c.id, c.job_no_mst, c.po_break_down_id, c.country_id, c.color_number_id, c.item_number_id, c.size_number_id, a.size_qty  
            order by c.job_no_mst, length(a.bundle_no) asc, b.barcode_no DESC";*/
			
	$sql="SELECT a.floor_id, a.sewing_line, b.mst_barcode_no, b.operation_id, b.barcode_no, max(c.id) as prdid, c.cut_no as cutting_no, c.bundle_no, sum(c.production_qnty) as production_qnty, d.id, d.job_no_mst, d.country_id, d.color_number_id, d.item_number_id, d.size_number_id, e.id as order_id, e.po_number, f.buyer_name, f.style_ref_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f, ppl_cut_lay_bundle_operation b 
	
	where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and c.barcode_no=b.mst_barcode_no and a.production_type=55 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nosCond 
	 group by a.floor_id, a.sewing_line, b.mst_barcode_no, b.operation_id, b.barcode_no, c.cut_no, c.bundle_no, d.id, d.job_no_mst, d.country_id, d.color_number_id, d.item_number_id, d.size_number_id, e.id, e.po_number, f.buyer_name, f.style_ref_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
	//echo $sql;		
	$result = sql_select($sql);	
	foreach ($result as $val)
	{
		$color_id_arr[$val[csf('color_number_id')]] 	=$val[csf('color_number_id')];
		$size_id_arr[$val[csf('size_number_id')]] 		=$val[csf('size_number_id')];
		$country_id_arr[$val[csf('country_id')]] 		=$val[csf('country_id')];
		$operation_id_arr[$val[csf('operation_id')]] 	=$val[csf('operation_id')];
		$bundle_no_arr[$val[csf('bundle_no')]] 			="'".$val[csf('bundle_no')]."'";
	}
	
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$size_arr=return_library_array( "select id, size_name from lib_size where id in (".implode(',', $size_id_arr).")",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where id in (".implode(',', $color_id_arr).")", "id", "color_name");
	$operation_name_arr=return_library_array( "select id, operation_name from lib_sewing_operation_entry where id in (".implode(',', $operation_id_arr).")",'id','operation_name');	
	$country_arr=return_library_array( "select id, country_name from lib_country where id in (".implode(',', $country_id_arr).")",'id','country_name');
	
	$bundleQtyArr=array();
	$bundleQtySql=sql_select("select barcode_no, size_qty from ppl_cut_lay_bundle where bundle_no in (".implode(',', $bundle_no_arr).") and status_active=1 and is_deleted=0");
	//echo "select barcode_no, size_qty from ppl_cut_lay_bundle where bundle_no in (".implode(',', $bundle_no_arr).") and status_active=1 and is_deleted=0";
	foreach($bundleQtySql as $brow)
	{
		$bundleQtyArr[$brow[csf('barcode_no')]]=$brow[csf('size_qty')];
	}
	$bundleQtySql=array();
	
	$result = sql_select($sql);
	$count=count($result);
	if($count<1){ echo "10####No Data Found. Please Check Pre-Costing Or Order Entry For Bundle Previous Process.";die(); }
	else{ echo "11####"; }
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{
		$dtls_id=$rate=$amount='';
		$dtls_id=$ticketDataArr[$row[csf("barcode_no")]]['id'];
		$rate=$ticketDataArr[$row[csf("barcode_no")]]['rate'];
		$amount=$ticketDataArr[$row[csf("barcode_no")]]['amount'];
		//echo $scanned_bundle_arr[$row[csf('bundle_no')]];
		// if(trim($scanned_bundle_arr[$row[csf('barcode_no')]])=="")
		// {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
			?>
			<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>"> 
                <td width="30"><?=$i; ?></td>
                <td width="90" id="ticketno_<?=$i; ?>"><?=$row[csf('barcode_no')]; ?></td>
                <td width="120" style="word-break:break-all" title="<?=$row[csf('operation_id')]; ?>"><?=$operation_name_arr[$row[csf('operation_id')]]; ?></td>
                <td width="70" id="bundle_<?=$i; ?>" align="center"><?=$row[csf('bundle_no')]; ?></td>
                <td width="60" align="center" style="word-break:break-all"><?=$floor_arr[$row[csf('floor_id')]]; ?></td>
                <td width="60" align="center" style="word-break:break-all"><?=$row[csf('sewing_line')]; ?></td>
                
                <td width="40" align="center"><?=$year; ?></td>
                <td width="40" align="center" title="<?=$row[csf('job_no_mst')]; ?>"><?=$job*1; ?></td>
                <td width="65" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
                
                <td width="100" style="word-break:break-all;"><?=$row[csf('style_ref_no')]; ?></td>
                <td width="70" style="word-break:break-all;"><?=$row[csf('po_number')]; ?></td>
                <td width="100" style="word-break:break-all;"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
                
                <td width="100" style="word-break:break-all;"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="50" align="center" style="word-break:break-all;"><?=$size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="50" align="center"><?=$bundleQtyArr[$row[csf('mst_barcode_no')]]; ?></td>
                <td width="50" align="center"><?=$row[csf('production_qnty')]; ?></td>
				<input type="hidden" class="text_boxes_numeric" name="txtRate[]" id="txtRate_<?=$i; ?>" style="width:28px" value="<?=$rate; ?>"/>
				<input type="hidden" class="text_boxes_numeric" name="txtAmount[]" id="txtAmount_<?=$i; ?>" style="width:28px" value="<?=$amount; ?>"/>
                <!-- <td width="40" id="rate_<?=$i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="txtRate[]" id="txtRate_<?=$i; ?>" style="width:28px" value="<?=$rate; ?>" onBlur="fnc_calculate_amount(<?=$i; ?>);" /></td><!--onKeyPress="return numOnly(this,event,this.id);"-->
                <!-- <td width="50" id="amount_<?=$i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="txtAmount[]" id="txtAmount_<?=$i; ?>" style="width:35px" value="<?=$amount; ?>" readonly/></td>  -->
                <td id="button_<?=$i; ?>" align="center">
				
                    <input type="button" id="decrease_<?=$i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i; ?>);" />
                    <input type="hidden" name="txtOperationId[]" id="txtOperationId_<?=$i; ?>" value="<?=$row[csf('operation_id')]; ?>"/>
                    <input type="hidden" name="txtcutNo[]" id="txtcutNo_<?=$i; ?>" value="<?=$row[csf('cutting_no')]; ?>"/>
                    <input type="hidden" name="txtbarcode[]" id="txtbarcode_<?=$i; ?>" value="<?=$row[csf('mst_barcode_no')]; ?>"/>
                    <input type="hidden" name="txtcolorSizeId[]" id="txtcolorSizeId_<?=$i; ?>" value="<?=$row[csf('id')]; ?>"/>
                    <input type="hidden" name="txtorderId[]" id="txtorderId_<?=$i; ?>" value="<?=$row[csf('order_id')]; ?>"/>
                    <input type="hidden" name="txtstyle[]" id="txtstyle_<?=$i; ?>" value="<?=$row[csf('style_ref_no')]; ?>"/>
                    <input type="hidden" name="txtgmtsitemId[]" id="txtgmtsitemId_<?=$i; ?>" value="<?=$row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="txtcountryId[]" id="txtcountryId_<?=$i; ?>" value="<?=$row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="txtcolorId[]" id="txtcolorId_<?=$i; ?>" value="<?=$row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="txtsizeId[]" id="txtsizeId_<?=$i; ?>" value="<?=$row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="txtqty[]" id="txtqty_<?=$i; ?>" value="<?=$row[csf('production_qnty')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<?=$i; ?>" value="<?=$dtls_id; ?>"/> 
                    <input type="hidden" name="isRescan[]" id="isRescan_<?=$i; ?>" value="0"/>
                </td>
            </tr>
			<?
			$i--;
		// }
	}
	$result='';
    exit(); 
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con= connect();
		$delivery_basis =3;
		if($db_type==0)	{ mysql_query("BEGIN"); }
		if($db_type==0) $year_cond="YEAR(insert_date)"; else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')"; else $year_cond="";	

		for($j=1;$j<=$tot_row;$j++)
        {   
            $ticketNo="ticketNo_".$j;       
            $ticketCheckArr[$$ticketNo]=$$ticketNo;       
        }
            
        $ticket_no ="'".implode("','",$ticketCheckArr)."'";

        $operation_sql="select c.barcode_no, c.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=55 and c.bundle_no in ($bundle) and c.production_type=55 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 "; //and (c.is_rescan=0 or c.is_rescan is null)

        $operation_result = sql_select($operation_sql);
        foreach ($operation_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }
		unset($operation_result);

 		$field_array_mst = "id, garments_nature, sys_prefix, sys_prefix_num, sys_no, production_type, working_company, working_location, operator_id, operation_date, operation_time, challan_no, remarks, loss_minute, entry_form, inserted_by, insert_date, status_active, is_deleted";
		
		if($db_type==2) 
		{
			$txt_reporting_hour=str_replace("'","",$txt_operation_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
		
		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_working_company), '', 'BLOT', date("Y",time()), 5, "select sys_prefix, sys_prefix_num from  pro_linking_operation_mst where working_company=$cbo_working_company and production_type=75 and $year_cond=".date('Y',time())." order by id desc ", "sys_prefix", "sys_prefix_num" ));
		/*echo "10**";
		print_r($new_system_id); die;*/
		$qc_id = return_next_id( "id", "pro_linking_operation_mst", 1);
		if($db_type==2)
		{
			$data_array_mst="INTO pro_linking_operation_mst (".$field_array_mst.") VALUES (".$qc_id.",".$garments_nature.",'".$new_system_id[1]."',".(int)$new_system_id[2].",'".$new_system_id[0]."',75,".$cbo_working_company.",".$cbo_working_location.",".$txt_operator_id.",".$txt_operation_date.",".$txt_reporting_hour.",".$txt_challan_no.",".$txt_remarks.",".$txt_loss_min.",346,".$user_id.",'".$pc_date_time."',1,0)";
		}
		else
		{
			$data_array_mst="(".$qc_id.",".$garments_nature.",'".$new_system_id[1]."',".(int)$new_system_id[2].",'".$new_system_id[0]."',75,".$cbo_working_company.",".$cbo_working_location.",".$txt_operator_id.",".$txt_operation_date.",".$txt_reporting_hour.",".$txt_challan_no.",".$txt_remarks.",".$txt_loss_min.",346,".$user_id.",'".$pc_date_time."',1,0)";
		}
		$challan_no=(int)$new_system_id[2];	
		
		$qc_dtls_id = return_next_id( "id", "pro_linking_operation_dtls", 1);
		$field_array_dtls ="id, mst_id, order_id,style_ref_no, color_size_id, gmts_item_id, color_id, size_id, bundle_qty, qc_pass_qty, bundle_no, barcode_no, ticket_no,  operation_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array_dtls="";
		for($j=1;$j<=$tot_row;$j++)
		{
			$txtOperationId ="txtOperationId_".$j;
			$ticketNo 		="ticketNo_".$j;			
			$bundleNo 		="bundleNo_".$j;
			$txtcutNo 		="txtcutNo_".$j;
			$txtbarcode 	="txtbarcode_".$j;
			$txtcolorSizeId ="txtcolorSizeId_".$j;
			$txtorderId 	="txtorderId_".$j;
			$txtgmtsitemId 	="txtgmtsitemId_".$j;
			$txtcolorId 	="txtcolorId_".$j;
			$txtsizeId 		="txtsizeId_".$j;
			$txtBundleQty 	="txtBundleQty_".$j;
			$txtqty 		="txtqty_".$j;
			// $txtRate 		="txtRate_".$j;
			// $txtAmount 		="txtAmount_".$j;
			$dtlsId 		="dtlsId_".$j;
			$isRescan 		="isRescan_".$j;
			$style_ref 		="txtstyle_".$j;
	
			if($duplicate_bundle[$$bundleNo]=='')
            {
				if($data_array_dtls!='') $data_array_dtls.=",";
				$data_array_dtls.="(".$qc_dtls_id.",".$qc_id.",'".$$txtorderId."','".$$style_ref."','".$$txtcolorSizeId."','".$$txtgmtsitemId."','".$$txtcolorId."','".$$txtsizeId."','".$$txtBundleQty."','".$$txtqty."','".$$bundleNo."','".$$txtbarcode."','".$$ticketNo."','".$$txtOperationId."',".$user_id.",'".$pc_date_time."',1,0)";

				$qc_dtls_id++;
			}
		}
				
		$flag=1;
		
		if($db_type==2)
		{
			$query="INSERT ALL ".$data_array_mst." SELECT * FROM dual";
			$rID_mst=execute_query($query);
		}
		else
		{
			$rID_mst=sql_insert("pro_linking_operation_mst",$field_array_mst,$data_array_mst,1);
		} 
		
		if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		$rID_dtls=sql_insert("pro_linking_operation_dtls",$field_array_dtls,$data_array_dtls,0);
		if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**insert into pro_linking_operation_dtls($field_array_dtls) values ".$data_array_dtls; die;
		
		//$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
		//echo "10**".$query;die;
		//echo "10**".$rID_mst."**".$rID_dtls."**".$flag;die;
	
		if($db_type==0)
		{  
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$qc_id."**".str_replace("'","",$new_system_id[0]);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "0**".$qc_id."**".str_replace("'","",$new_system_id[0]);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
	
		$mst_id=str_replace("'","",$txt_update_id);
		$txt_chal_no=explode("-",str_replace("'","",$txt_system_no));
		$challan_no=(int) $txt_chal_no[3];
		
		$sql_dtls="Select id from pro_linking_operation_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls ); $dtls_update_id_array=array();
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}
		
		for($j=1;$j<=$tot_row;$j++)
        {   
            $ticketNo="ticketNo_".$j;       
            $ticketCheckArr[$$ticketNo]=$$ticketNo;       
        }
            
        $ticket_no ="'".implode("','",$ticketCheckArr)."'";
		
		 $receive_sql="select c.barcode_no,c.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=55 and c.bundle_no  in ($bundle)  and c.production_type=55 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.delivery_mst_id!=$mst_id and c.delivery_mst_id!=$mst_id and (c.is_rescan=0 or c.is_rescan is null)"; 
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }
		
		if($db_type==2) 
		{
			$txt_reporting_hour=str_replace("'","",$txt_operation_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
		
		$field_array_mst = "working_location*operation_date*operation_time*challan_no*remarks*loss_minute*updated_by*update_date";
		$data_array_mst="".$cbo_working_location."*".$txt_operation_date."*".$txt_reporting_hour."*".$txt_challan_no."*".$txt_remarks."*".$txt_loss_min."*".$user_id."*'".$pc_date_time."'";
		
		$field_arrup_dtls ="order_id*style_ref_no*color_size_id*gmts_item_id*color_id*size_id*bundle_qty*qc_pass_qty*bundle_no*barcode_no*ticket_no*operation_id*updated_by*update_date";
		$qc_dtls_id = return_next_id( "id", "pro_linking_operation_dtls", 1);
		$field_array_dtls ="id, mst_id, order_id,style_ref_no, color_size_id, gmts_item_id, color_id, size_id, bundle_qty, qc_pass_qty, bundle_no, barcode_no, ticket_no,  operation_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array_dtls="";
		$data_arrup_dtls = array();
		for($j=1;$j<=$tot_row;$j++)
		{
			$txtOperationId ="txtOperationId_".$j;
			$ticketNo 		="ticketNo_".$j;			
			$bundleNo 		="bundleNo_".$j;
			$txtcutNo 		="txtcutNo_".$j;
			$txtbarcode 	="txtbarcode_".$j;
			$txtcolorSizeId ="txtcolorSizeId_".$j;
			$txtorderId 	="txtorderId_".$j;
			$txtgmtsitemId 	="txtgmtsitemId_".$j;
			$txtcolorId 	="txtcolorId_".$j;
			$txtsizeId 		="txtsizeId_".$j;
			$txtBundleQty 	="txtBundleQty_".$j;
			$txtqty 		="txtqty_".$j;
			// $txtRate 		="txtRate_".$j;
			// $txtAmount 		="txtAmount_".$j;
			$dtlsId 		="dtlsId_".$j;
			$isRescan 		="isRescan_".$j;
			$style_ref 		="txtstyle_".$j;

			if(str_replace("'",'',$$dtlsId)=='')
			{
				if($duplicate_bundle[$$bundleNo]=='')
				{
					if($data_array_dtls!='') $data_array_dtls.=",";
					$data_array_dtls.="(".$qc_dtls_id.",".$mst_id.",'".$$txtorderId."','".$$style_ref."','".$$txtcolorSizeId."','".$$txtgmtsitemId."','".$$txtcolorId."','".$$txtsizeId."','".$$txtBundleQty."','".$$txtqty."','".$$bundleNo."','".$$txtbarcode."','".$$ticketNo."','".$$txtOperationId."',".$user_id.",'".$pc_date_time."',1,0)";
	
					$qc_dtls_id++;
				}
			}
			else if(str_replace("'",'',$$dtlsId)!='')
			{
				$data_arrup_dtls[str_replace("'",'',$$dtlsId)] =explode("*",("'".$$txtorderId."'*'".$$style_ref."'*'".$$txtcolorSizeId."'*'".$$txtgmtsitemId."'*'".$$txtcolorId."'*'".$$txtsizeId."'*'".$$txtBundleQty."'*'".$$txtqty."'*'".$$bundleNo."'*'".$$txtbarcode."'*'".$$ticketNo."'*'".$$txtOperationId."'*".$user_id."*'".$pc_date_time."'"));
				$id_arr[]=str_replace("'",'',$$dtlsId);
			}
		}

		$flag=1;
		$rID=sql_update("pro_linking_operation_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_arrup_dtls!="" && $flag==1)
		{
			$rID1=execute_query(bulk_update_sql_statement("pro_linking_operation_dtls", "id",$field_arrup_dtls,$data_arrup_dtls,$id_arr ));
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		if($data_array_dtls!="" && $flag==1)
		{
			$rID_dtls=sql_insert("pro_linking_operation_dtls",$field_array_dtls,$data_array_dtls,0);
			//echo "10**insert into pro_linking_operation_dtls($field_array_dtls) values ".$data_array_dtls; die;
			if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if(implode(',',$id_arr)!="")
		{
			$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
		}
		else
		{
			$distance_delete_id=implode(',',$dtls_update_id_array);
		}
		if(str_replace("'",'',$distance_delete_id)!="")
		{
			$rID3=execute_query( "update pro_linking_operation_dtls set updated_by='$user_id', update_date='".$pc_date_time."', status_active=0, is_deleted=1 where id in ($distance_delete_id) and status_active=1 and is_deleted=0 ",0);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".bulk_update_sql_statement( "pro_gmts_knitting_issue_dtls", "id", $field_array_color_dtls, $data_array_color_dtls, $color_dtls_id_arr );die;	
		 // echo "10**".$rID_mst_qc .'&&'. $rID_dtls .'&&'. $rID .'&&'. $dtlsrID .'&&'. $defectQ."**".$delete.'&&'. $delete_dtls .'&&'. $delete_qc."**".$delete_defect;oci_rollback($con);die;
		//echo "10**".$rID.'='.$rID1.'='.$rID_dtls.'='.$flag;die;
		
		
		if($db_type==0)
		{  
			if($flag==1)
			{ 
				mysql_query("COMMIT");  
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( 160,0);
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

if($action=="system_number_popup")
{
  	echo load_html_head_contents("Linking Operation Track Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_system_value(strCon ) 
		{
			document.getElementById('update_mst_id').value=strCon;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%; overflow-y:hidden;" >
    	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <th width="130" class="must_entry_caption">Working Company</th>
                    <th width="50">System ID</th>
                    <th width="70">Lot Ratio No</th>
                    <th width="100">Job No</th>
                    <th width="100">Order No</th>
                    <th width="80">QR Code</th>
                    <th width="130" colspan="2">Op. Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" /></th>           
                </thead>
                <tbody>
                    <tr class="general">                    
                        <td><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --","", ""); ?></td>
                        <td><input name="txt_cut_qc" id="txt_cut_qc" class="text_boxes" style="width:50px"  placeholder="Write"/></td>
                        <td>
                            <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:70px"  class="text_boxes" placeholder="Write"/>
                            <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td><input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:90px" placeholder="Write"/></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px" placeholder="Write" /></td>
						<td><input name="txt_qr_code" id="txt_qr_code" class="text_boxes" style="width:80px" placeholder="Write" /></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" /></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date" /></td>
                        <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_cut_qc').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_qr_code').value, 'create_system_search_list_view', 'search_div', 'bundle_linking_operation_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
                        </td>
                    </tr>
                    <tr>                  
                        <td align="center" valign="middle" colspan="9"><? echo load_month_buttons(1); ?></td>
                    </tr>   
                </tbody>
            </table> 
     	<div align="center" valign="top" id="search_div"> </div>  
    </form>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_system_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cut_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$system_no= $ex_data[6];
	$order_no= $ex_data[7];
	$qr_code= trim($ex_data[8]);
	if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
	
    if($db_type==2){ $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from f.insert_date) as year";}
    if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(f.insert_date, '-', 1) as year";}
	
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and f.working_company=".str_replace("'","",$company)."";
	if(str_replace("'","",$cut_no)=="") $cut_nocond=""; else $cut_nocond="and a.cut_qc_prefix_no='".str_replace("'","",$cut_no)."'  ";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and f.sys_prefix_num=".trim($system_no)." $year_cond";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	if(str_replace("'","",$qr_code)=="") $qr_code_cond=""; else $qr_code_cond=" and g.ticket_no='$qr_code'";
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and f.operation_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$sql_cond= " and f.operation_date between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	$emp_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| ' ' || last_name) as emp_name from hrm_employee",'id_card_no','emp_name',$new_conn);
	$arr=array(6=>$emp_arr);
	
	$sql_order="SELECT f.id, f.sys_prefix_num, f.operator_id, a.job_no, f.operation_date, c.job_no_prefix_num, b.cut_num_prefix_no, $year, d.po_number
    FROM pro_gmts_cutting_qc_mst a, ppl_cut_lay_mst b, wo_po_details_master c, wo_po_break_down d, ppl_cut_lay_dtls e, pro_linking_operation_mst f, pro_linking_operation_dtls g
    where a.garments_nature=100 and a.cutting_no=b.cutting_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and b.id=e.mst_id and f.id=g.mst_id and d.id=g.order_id $conpany_cond $cut_nocond $job_cond $sql_cond $order_cond $system_cond $qr_code_cond and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0
	group by f.id, f.sys_prefix_num, f.operator_id, a.job_no, f.operation_date, c.job_no_prefix_num, b.cut_num_prefix_no, f.insert_date, d.po_number
	 order by f.id DESC";
	// echo $sql_order;die;
	echo create_list_view("list_view", "Sys. No,Year,Lot Ratio No,Job No,Order No,Operation Date,Operator Name","60,60,60,80,100,80","750","270",0, $sql_order , "js_set_system_value", "id", "", 1, "0,0,0,0,0,0,operator_id", $arr, "sys_prefix_num,year,cut_num_prefix_no,job_no,po_number,operation_date,operator_id", "","setFilterGrid('list_view',-1)","0,0,0,0,0,3,0") ;
	exit();	
}

if($action=='populate_data_from_track')
{
	$employee_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| '  ' || last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0",'id_card_no','emp_name',$new_conn);
	if($db_type==0) $production_hour="operation_time"; else if($db_type==2) $production_hour="TO_CHAR(operation_time,'HH24:MI')";
	//echo "select id, garments_nature, sys_no, working_company, working_location, operator_id, operation_date, $production_hour as operation_time, challan_no, remarks from pro_linking_operation_mst where id=$data and status_active=1 and is_deleted=0";
	$data_array=sql_select("select id, garments_nature, sys_no, working_company, working_location, operator_id, operation_date, $production_hour as operation_time, challan_no, remarks, loss_minute from pro_linking_operation_mst where id=$data and status_active=1 and  is_deleted=0");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_no').value 				= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('txt_update_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company")]."';\n";
		echo "load_drop_down( 'requires/bundle_linking_operation_controller', ".$row[csf("working_company")].", 'load_drop_down_location','working_location_td');";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("working_location")]."';\n";
		echo "document.getElementById('txt_operation_date').value 			= '".change_date_format($row[csf("operation_date")])."';\n";
		echo "document.getElementById('txt_operator_id').value 				= '".$row[csf("operator_id")]."';\n";
		echo "document.getElementById('txt_operator_name').value 			= '".$employee_arr[$row[csf("operator_id")]]."';\n";
		
		echo "document.getElementById('txt_reporting_hour').value 			= '".$row[csf("operation_time")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_remarks').value  				= '".($row[csf("remarks")])."';\n";
		echo "document.getElementById('garments_nature').value 				= '".$row[csf("garments_nature")]."';\n";
		echo "document.getElementById('txt_loss_min').value 				= '".$row[csf("loss_minute")]."';\n";
		exit();
	}
}

if($action=="challan_duplicate_check")
{
	$bundle_no="'".implode("','",explode(",",$data))."'";
	$msg=1;
	
	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond="";
	$bundle_nos_cond=" and b.barcode_no in ($bundle_no)";
	//$result=sql_select("select a.cutting_qc_no,b.bundle_no from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond");

	$datastr="";
	if(count($result)>0)
	{
		foreach ($result as $row)
		{ 
			$msg=2;
			$datastr=$row[csf('bundle_no')]."*".$row[csf('cutting_qc_no')];
		}
	}
	
	echo rtrim($msg)."_".rtrim($datastr)."_".$search_lot_no;
	exit();
}

if ($action=="load_drop_down_floor")
{
 	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (2) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
	exit();     	 
} 

if ($action == "load_drop_down_wc_com")
{
	$data = explode("_", $data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) {
		echo create_drop_down("cbo_working_company", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $company_id, "load_location();", 1);
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_working_company", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
	} else {
		echo create_drop_down("cbo_working_company", 130, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
	}
	exit();
}

if ($action=="load_drop_down_wc_location")
{
	echo create_drop_down( "cbo_working_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down('requires/bundle_linking_operation_controller', this.value, 'load_drop_down_floor', 'floor_td' );",1 );
	exit();   
}

if ($action=="load_drop_down_lc_location")
{
	echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
	exit();   
}

if ($action == "load_drop_down_line")
{
    list($wcompany_id, $wlocation, $floor,$input_date) = explode("_", $data);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$wcompany_id' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
    $cond = "";
    
    if ($prod_reso_allocation == 1)
	{
        $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
        $line_array = array();
        if ($floor == 0 && $location != 0) $cond = " and a.location_id= $location";
        if ($floor != 0) $cond = " and a.floor_id= $floor";
		
        if($db_type==0) $input_date = date("Y-m-d",strtotime($input_date)); else $input_date = change_date_format(date("Y-m-d",strtotime($input_date)),'','',1);
        
        $cond.=" and b.pr_date='".$input_date."'";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.prod_resource_num asc, a.id asc");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num order by a.prod_resource_num asc, a.id asc");
        }
        
        $line_merge=9999; 
        foreach($line_data as $row)
        {
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if(count($line_number)>1)
                {
                    $line_merge++;
                    $new_arr[$line_merge]=$row[csf('id')];
                }
                else
                    $new_arr[$line_library[$val]]=$row[csf('id')];
                    
                if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
            }
            $line_array[$row[csf('id')]]=$line;
        }
        ksort($new_arr);
        foreach($new_arr as $key=>$v)
        {
            $line_array_new[$v]=$line_array[$v];
        }
        echo create_drop_down( "cbo_line_no", 130,$line_array_new,"", 1, "--Select Line--", $selected, "",0,0 );
        
    } else {
        if ($floor == 0 && $location != 0) $cond = " and location_name= $location";
        if ($floor != 0) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

        echo create_drop_down("cbo_line_no", 130, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name", "id,line_name", 1, "--Select Line--", $selected, "", 0, 0);
    }
    exit();
}


//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(50) and is_deleted=0 and status_active=1");		 
	echo trim($print_report_format);	
	exit();

}
if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();   
}
?>
