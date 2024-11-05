<? 
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$mrr_date_check="";
$select_insert_year="";
$date_ref="";
$group_concat="";
if($db_type==2 || $db_type==1)
{
	$mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
	$select_insert_year="to_char";
	$date_ref=",'YYYY'";
	$group_concat="wm_concat";
	// LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY po_breakdown_id) 
}
else if ($db_type==0)
{
	$mrr_date_check="and year(insert_date)=".date('Y',time())."";
	$select_insert_year="year";
	$date_ref="";
	$group_concat="group_concat";
}

if ($action=="load_buyer_details")
{
	echo create_drop_down( "txt_search_common", 230, "select a.id, a.buyer_name from lib_buyer a,  lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data order by  a.buyer_name","id,buyer_name", 1, "-- Select --", 0, "",0 );
	exit();  	 
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(2) and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 order by a.supplier_name ","id,supplier_name", 1, "-- Select --", 0, "",0 );
	exit();  	 
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}
if ($action == "print_button_variable_setting") {
	$print_report_format = 0;
	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=19 and report_id=307 and is_deleted=0 and status_active=1");
	$printButton = explode(',', $print_report_format);
	foreach ($printButton as $id) {
		if ($id == 45) $buttonHtml .= '<input id="id_print_to_button" class="formbutton" type="button" style="width:80px" onclick="print_to_html_report(4)" name="print" value="Print4">';
		 
	}
	echo "document.getElementById('button_data_panel').innerHTML = '" . $buttonHtml . "';\n";
	exit();
}

if($action=="requisition_popup")
{
	extract($_REQUEST); 
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$cbo_wo_basis=str_replace("'","",$cbo_wo_basis);
	
	?>
	<script>
	var selected_dtls_id = new Array;
	var selected_id = new Array;
	var selected_reqsition = new Array;
	
	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		//alert(tbl_row_count);return;
		for( var i = 1; i <= tbl_row_count; i++ ) {
			js_set_value( i );
		}
	}
	
	function toggle( x, origColor ){
		var newColor = 'yellow';
		if ( x.style ) { 
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function set_all()
	{
		var old=document.getElementById('txt_req_row_id').value;
		if(old!="")
		{   
			old=old.split(",");
			for(var i=0; i<old.length; i++)
			{  
				js_set_value( old[i] ) 
			}
		}
	}
	
	function js_set_value( str ) 
	{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_dtls_id' + str).val(), selected_dtls_id ) == -1 ) {
				selected_dtls_id.push( $('#txt_dtls_id' + str).val() );
				selected_id.push( $('#txt_mst_id' + str).val() );
				selected_reqsition.push( $('#txt_req_no' + str).val() );
			}
			else {
				for( var i = 0; i < selected_dtls_id.length; i++ ) {
 					if( selected_dtls_id[i] == $('#txt_dtls_id' + str).val() ) break;
				}
				selected_dtls_id.splice( i, 1 );
				selected_id.splice( i, 1 );
				selected_reqsition.splice( i, 1 );
			}
			var mst_id =''; var dtls_id =''; var req_no ='';
			for( var i = 0; i < selected_dtls_id.length; i++ ) {
				dtls_id += selected_dtls_id[i] + ',';
				mst_id += selected_id[i] + ',';
				req_no += selected_reqsition[i] + ',';
			}
			dtls_id	= dtls_id.substr( 0, dtls_id.length - 1 );
			mst_id 	= mst_id.substr( 0, mst_id.length - 1 );
			req_no 	= req_no.substr( 0, req_no.length - 1 );
			
			$('#txt_dtls_id').val( dtls_id );
			$('#txt_mst_id').val( mst_id );
			$('#txt_req_no').val( req_no );
	}
	
	/*function reset_hidden()
	{
		if($("#txt_selected").val()=="")
		{
			$("#txt_selected").val('');
			$("#txt_selected_id").val('');
			$("#txt_selected_job").val(''); 			
 		}
		else
		{
			var selectID = $('#txt_selected_id').val().split(",");
			var selectName = $('#txt_selected').val().split(",");
			var selectJob = $('#txt_selected_job').val().split(",");
			for(var i=0;i<selectID.length;i++)
			{
				selected_id.push( selectID[i] );
				selected_name.push( selectName[i] );
				selected_job.push( selectJob[i] );
			}
		}
	}*/
	
	
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
 		<table width="750" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" align="center">
            <thead>
                <th width="250">Enter Requisition Number</th>
                <th width="300">Requisition Date Range</th>
                <th ><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </thead>
            <tr class="general">
                <td >				
                    <input type="text" style="width:180px" class="text_boxes"  name="txt_req_no" id="txt_req_no"  />			
                </td>
                <td align="center">
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px"> To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px">
                </td>
                <td align="center">
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_req_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+'<? echo $garments_nature; ?>'+'_'+'<? echo $txt_req_dtls_id; ?>', 'create_req_search_list_view', 'search_div', 'sample_yarn_work_order_controller', 'setFilterGrid(\'table_body\',-1)');set_all();" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td colspan="3" align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                </td>
            </tr>
        </table>
        <div style="margin-top:5px" id="search_div"></div>    
        <table width="750" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="hidden" id="txt_dtls_id" value="<? //echo $txt_buyer_po; ?>"  /> <!--req dtls id here -->
                            <input type="hidden" id="txt_mst_id" value="<? //echo $txt_buyer_po_no; ?>"  /> <!--req mst here -->
                            <input type="hidden" id="txt_req_no" value="<? //echo $txt_buyer_po_no; ?>"  /> <!--req number here -->
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html> 
    <?
	exit();
}

if($action=="create_req_search_list_view")
{
 	extract($_REQUEST); 
	//echo $data;die;
	$ex_data = explode("_",$data);
	$txt_req_no = $ex_data[0];
	$txt_date_from = $ex_data[1];
	$txt_date_to = $ex_data[2];
	$company = $ex_data[3];
	$garments_nature = $ex_data[4];
	$txt_req_dtls_id=$ex_data[5];
	$sql_cond="";
	
	$yarn_count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$buyer_short_name_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	
	
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_date_from=change_date_format($txt_date_from,'','',-1);
			$txt_date_to=change_date_format($txt_date_to,'','',-1);
		}
		$sql_cond=" and a.requisition_date between '$txt_date_from' and '$txt_date_to' ";
	}
	if($txt_req_no!="")
	{
		$sql_cond.=" and a.requ_prefix_num=$txt_req_no";
	}
	
	if($txt_req_dtls_id=="") $txt_req_dtls_id=0;
	$prev_req_wo=return_library_array("SELECT requisition_dtls_id, sum(supplier_order_quantity) as supplier_order_quantity from  wo_non_order_info_dtls where status_active=1 and requisition_dtls_id>0 and requisition_dtls_id not in($txt_req_dtls_id) group by requisition_dtls_id","requisition_dtls_id","supplier_order_quantity");
	
	$sql="SELECT a.id as mst_id, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, b.id as dtls_id, b.job_id, b.job_no, b.buyer_id, b.style_ref_no, b.color_id, b.quantity as req_qnty,b.count_id,b.composition_id, b.yarn_type_id, a.basis
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	where a.id=b.mst_id and a.basis=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70 and a.item_category_id=1 and a.company_id=$company $sql_cond order by a.requ_no";
	//echo $sql;
	$sql_result=sql_select($sql);
	?>
    <div style="width:1020px;">
     	<table cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="130">Req No</th>
                <th width="100">Job No</th>
                <th width="130">Style</th>
                <th width="70">Buyer</th>
                <th width="50">Color</th>
                <th width="50">Count</th>
                <th width="110">Composition</th>
                <th width="80">Yarn Type</th>
                <th width="80">Req Qnty</th>
                <th width="75">Req Date</th>
                <th >Delivery Date</th>
             </thead>
     	</table>
     </div>
     <div style="width:1020px; max-height:230px;overflow-y:scroll;" >	 
        <table cellspacing="0" width="1002" class="rpt_table" id="table_body" border="1" rules="all">
			<?
			$i=1; $req_row_id='';
			$txt_req_dtls_id_arr=explode(",",$txt_req_dtls_id);
            foreach( $sql_result as $row )
            {
				if($row[csf("req_qnty")]>$prev_req_wo[$row[csf("dtls_id")]])
				{
					if( in_array($row[csf('dtls_id')],$txt_req_dtls_id_arr)) 
					{
						if($req_row_id=="") $req_row_id=$i; else $req_row_id.=",".$i;
					}
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none;cursor:pointer;" onClick="js_set_value(<? echo $i;?>)"> 
						<td width="40" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_mst_id[]" id="txt_mst_id<? echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>
							<input type="hidden" name="txt_dtls_id[]" id="txt_dtls_id<? echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
							<input type="hidden" name="txt_req_no[]" id="txt_req_no<? echo $i ?>" value="<? echo $row[csf('requ_no')]; ?>"/>
						</td>
						<td width="130" align="center"><? echo $row[csf("requ_no")]; ?></td>
						<td width="100" align="center"><? echo $row[csf("job_no")]; ?></td>
						<td width="130" align="center"><? echo $row[csf("style_ref_no")]; ?></td>
						<td width="70"><? echo $buyer_short_name_arr[$row[csf("buyer_id")]];  ?></td>	
						<td width="50"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                        
                        
						<td width="50"><p><? echo $yarn_count_arr[$row[csf("count_id")]]; ?></p></td>
						<td width="110"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
						<td width="80"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                        
                        
						<td width="80" align="right"><? echo $row[csf("req_qnty")];?> </td>
						<td width="75" align="center"><? echo change_date_format($row[csf("requisition_date")]);?> </td>
						<td align="center"><? echo change_date_format($row[csf("delivery_date")]);?></td>
					</tr>
					<? 
					$i++;
				}
             }
   			?>
            <input type="hidden" name="txt_req_row_id" id="txt_req_row_id" value="<? echo $req_row_id; ?>"/>
		</table> 
	</div> 
    <?
	exit();
}

if ($action=="order_search_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
  extract($_REQUEST);  
  $permission=$_SESSION['page_permission'];
  //echo $cbo_basis; die;
  ?>
<script>
  function js_set_value(str)
  {
    $("#hidden_tbl_id").val(str); // wo/pi id
    parent.emailwindow.hide();
  }
  var permission='<? echo $permission; ?>';
</script>
 
<div align="center" style="width:100%;" >
<form name="searchjob"  id="searchjob" autocomplete="off">
  <table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th width="140">Company</th>
                <th width="140">Buyer</th>
                <th width="50">Year</th>
                <th width="80">Job No</th>
                <th width="80">Style No</th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:85px" class="formbutton" onClick="reset_form('searchjob','search_div','','','')"  /></th>           
            </thead>
            <tbody>
                <tr>
                    <td>
          			<? 
                    echo create_drop_down( "cbo_company_name", 135, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$company), "load_drop_down( 'yarn_requisition_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
                    ?>
                    </td>
                    <td align="center" id="buyer_td">       
         		 	<?
                    $blank_array="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
           			echo create_drop_down( "cbo_buyer_name", 135, $blank_array,"id,buyer_name", 1, "-- Select Buyer --",0,"",0);
                    ?>  
                    </td> 
                    <td>
                    <?
                        $year_current=date("Y");
                        echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "All",$year_current);
                    ?>
                    </td>   
                    <td align="center">
                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:75px" />
                     </td>
                     <td align="center">
                        <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:75px" />
                     </td>
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+<? echo $cbo_basis?>, 'create_job_search_list_view', 'search_div', 'sample_yarn_work_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:85px;" />        
                    </td>
              </tr>
            </tbody>
        </table>  
        <div id="search_div"> </div> 
        </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="create_job_search_list_view")
{
	$data=explode("_",$data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_buyer_name=str_replace("'","",$data[1]);
	$txt_job_no=str_replace("'","",$data[2]);
	$txt_style_no=str_replace("'","",$data[3]);
	$cbo_job_year=str_replace("'","",$data[4]);
	$basis=str_replace("'","",$data[5]);
	//echo $data[5];
	
	//echo $cbo_company_name."**".$cbo_buyer_name."**".$txt_job_no."**".$basis."<br>";//die;
	$sql_cond="";
	if($cbo_company_name!=0) $sql_cond.=" and a.company_name='$cbo_company_name'";
	if($cbo_buyer_name!=0) $sql_cond.=" and a.buyer_name='$cbo_buyer_name'";
	if($txt_job_no!="") $sql_cond.=" and a.job_no like '%$txt_job_no%'";
	if($txt_style_no!="") $sql_cond.=" and a.style_ref_no like '%$txt_style_no%'";
  	if($db_type==0)
	{
		if($cbo_job_year!=0) $sql_cond.=" and year(a.insert_date)='$cbo_job_year'";
		$job_year="year(a.insert_date)";
	}
	else
	{
		if($cbo_job_year!=0) $sql_cond.=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
		$job_year="to_char(a.insert_date,'YYYY')";
	}
	$sql="select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $job_year as year from  wo_po_details_master a, wo_pre_cost_fab_yarn_cost_dtls b where a.job_no=b.job_no and a.status_active=1 $sql_cond 
	group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date order by a.id DESC";
  
  //echo $sql;//die;
  ?>
  <div style="width:550px;">
    <input type="hidden" id="hidden_tbl_id">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="550" class="rpt_table" >
            <thead>
                <th width="50">SL</th>
                <th width="80">Year</th>
                <th width="120">Job No</th>
                <th width="130">Buyer</th>
                <th > Style Ref.NO</th>
               
            </thead>
        </table>
        <div style="width:550px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" >
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="532" class="rpt_table" id="tbl_list_search">
            <?
       
        $i=1;
        $nameArray=sql_select( $sql );
        foreach ($nameArray as $selectResult)
        {
          $po_number=implode(",",array_unique(explode(",",$selectResult[csf("po_number")])));
          if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('job_no')]; ?>'+'_'+'<? echo $selectResult[csf('buyer_name')]; ?>'); "> 
                    
                     <td width="50"><p> <? echo $i; ?></p></td>
                      <td width="80"  align="center"> <p><? echo $selectResult[csf('year')]; ?></p></td>  
                      <td width="120"  align="center"> <p><? echo $selectResult[csf("job_no")]; ?></p></td> 
                      <td width="130"><p><?  echo  $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>  
                      <td> <p><?  echo $selectResult[csf('style_ref_no')]; ?></p></td>  
                    </tr>
                <?
                  $i++;
        }
      ?>
            </table>
    </div>
    </div>
   <? 
  exit();
}

if($action=="dtls_part_html_row")
{
	$data_ex=explode("_",$data);
	$job_no=$data_ex[1];
	$basis=$data_ext[4];//die;


	$fabric_uom_arr=array(); $fabric_gitem_arr=array();
	$yarnsql="select id, item_number_id, uom from wo_pre_cost_fabric_cost_dtls where JOB_ID IN($data_ex[0])";
	$yarnsqlRes=sql_select($yarnsql);
	foreach($yarnsqlRes as $yrow)
	{
		$fabric_uom_arr[$yrow[csf('id')]]=$yrow[csf('uom')];
		$fabric_gitem_arr[$yrow[csf('id')]]=$yrow[csf('item_number_id')];
	}
	unset($yarnsqlRes);

	$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst where JOB_ID IN($data_ex[0])", "job_no", "costing_per");

	$stripe_color_arr=array(); $strip_cons_arr=array();
	$sql_stripe=sql_select("select job_no, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement from wo_pre_stripe_color where status_active=1 and is_deleted=0 and JOB_ID IN($data_ex[0]) group by job_no, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement" );

	foreach($sql_stripe as $row)
	{
		$stripe_color_arr[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('stripe_color')]].=$row[csf('color_number_id')].',';
		$strip_cons_arr[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('stripe_color')]].=$row[csf('color_number_id')].'_'.$row[csf('measurement')].',';
	}
	unset($sql_stripe);

	  // wo data
	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	$sql_order_dtls_arr = sql_select("SELECT a.JOB_ID, a.YARN_COUNT, a.YARN_COMP_TYPE1ST, a.YARN_TYPE, a.COLOR_NAME, a.SUPPLIER_ORDER_QUANTITY from wo_non_order_info_dtls a, WO_NON_ORDER_INFO_MST b   where a.JOB_ID=$data_ex[0] and b.COMPANY_NAME=$data_ex[3] and b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.ENTRY_FORM=234");
	if(!empty($sql_order_dtls_arr)){
		$WoQtyCheckArr=array();
		foreach($sql_order_dtls_arr as $row){
			$WoQtyCheckArr[$row["JOB_ID"]][$row["YARN_COUNT"]][$row["YARN_COMP_TYPE1ST"]][$row["YARN_TYPE"]][$row["COLOR_NAME"]]["REQ_QUANTITY"]+=$row["SUPPLIER_ORDER_QUANTITY"];
		}
	}

	$process_loss_method=return_field_value("editable", "variable_order_tracking", "company_name=$data_ex[3] and variable_list=104 and  status_active=1 and is_deleted=0");

	$plan_qty_arr=array();
	//$po_sql=sql_select("select job_no_mst, sum(plan_cut) as plan_cut from wo_po_break_down where status_active=1 and is_deleted=0 group by job_no_mst");
	$po_sql=sql_select("select job_no_mst, item_number_id, color_number_id, plan_cut_qnty as plan_cut from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and JOB_ID IN($data_ex[0])");
	foreach($po_sql as $row)
	{
		$plan_qty_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut')];
	}

	
	$sql="select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio,a.job_quantity, c.fabric_cost_dtls_id, c.count_id, c.copm_one_id, c.percent_one, c.type_id, c.color, sum(c.cons_qnty) as cons_qnty, (sum(rate)/count(rate)) as rate_ratio 
	from wo_po_details_master a, wo_pre_cost_fab_yarn_cost_dtls c 
	where a.job_no=c.job_no and a.id=$data_ex[0] and a.status_active=1
	group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.total_set_qnty,a.job_quantity, c.fabric_cost_dtls_id, c.count_id, c.copm_one_id, c.percent_one, c.type_id,c.color";
	// echo $sql;//die;
	$sql_result=sql_select($sql);
	$i=$data_ex[2];
	$k=1;
	$dzn_qnty=0; 
	foreach($sql_result as $row)
	{
    	$company_name=$row[csf("company_name")];

		$stripe_color=''; $contrast_color=''; $color_id=0; $is_stripe=0;
		$stripe_color=implode(",",array_filter(array_unique(explode(",",$stripe_color_arr[$row["JOB_NO"]][$row['FABRIC_COST_DTLS_ID']][$row['COLOR']]))));
		$contrast_color=$contrast_color_arr[$row["JOB_NO"]][$row['COLOR']];
		if($stripe_color!="") { $color_id=$stripe_color; $is_stripe=1; } else if($contrast_color!="") $color_id=$contrast_color; else $color_id=$row['COLOR'];
				
		$dzn_qnty=0; $cons_qnty=0; $cons_balance_qnty=0;  $fabuom=0; $fgitem=0;
		if($costing_per_id_library[$row["JOB_NO"]]==1) $dzn_qnty=12;
		else if($costing_per_id_library[$row["JOB_NO"]]==3) $dzn_qnty=12*2;
		else if($costing_per_id_library[$row["JOB_NO"]]==4) $dzn_qnty=12*3;
		else if($costing_per_id_library[$row["JOB_NO"]]==5) $dzn_qnty=12*4;
		else $dzn_qnty=1;
		$dzn_qnty=$dzn_qnty;
		
		$fabuom=$fabric_uom_arr[$row['FABRIC_COST_DTLS_ID']];
		$fgitem=$fabric_gitem_arr[$row['FABRIC_COST_DTLS_ID']];
		if($fabuom==12)
		{
			$plan_cut_qnty=0; $cons_qnty=0;
			$excolor_id=explode(",",$color_id);
			if($is_stripe==1)
			{
				$stripe_data=array_filter(array_unique(explode(",",$strip_cons_arr[$row["JOB_NO"]][$row['FABRIC_COST_DTLS_ID']][$row['COLOR']])));
				//print_r($stripe_data);
				foreach($stripe_data as $stcolorcons)
				{
					$gmts_color=""; $strip_cons=0; $plan_cut_qnty=0;
					$ex_stcolorcons=explode("_",$stcolorcons);
					$gmts_color=$ex_stcolorcons[0]; 
					$strip_cons=$ex_stcolorcons[1];
					//$strip_cons=$row[csf('cons_qnty')];
					$plan_cut_qnty=$plan_qty_arr[$row["JOB_NO"]][$fgitem][$gmts_color];
					
					$cons_qty=$plan_cut_qnty*($strip_cons/$dzn_qnty);
					//echo $stcolorcons.'='.$plan_cut_qnty.'='.$strip_cons.'='.$dzn_qnty.'='.$cons_qty.'<br>';
					$cons_qnty+=$cons_qty;
				}
			}
			else
			{
				foreach($excolor_id as $colorid)
				{
					$plan_cut_qnty+=$plan_qty_arr[$row["JOB_NO"]][$fgitem][$colorid]*$row['RATIO'];
				}
				$cons_qnty=$plan_cut_qnty*($row['CONS_QNTY']/$dzn_qnty);
			}
			
			$cons_qnty=$cons_qnty*2.20462;
		}
		else
		{
			$plan_cut_qnty=$plan_qty_arr[$row["JOB_NO"]][$fgitem][$color_id]*$row['RATIO'];
			$cons_qnty=$plan_cut_qnty*($row['CONS_QNTY']/$dzn_qnty);
		}
		
		?>
		<tr class="general" id="tr_<? echo $i; ?>" >
            <td align="center">
            <input type="text" id="txtjobno_<? echo $i; ?>" name="txtjobno[]" class="text_boxes" style="width:90px;"  value="<? echo $row[csf("job_no")]; ?>" readonly />
            <input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid_<? echo $i; ?>" style="width:100px;" value="<? echo $row[csf("id")]; ?>">
            <input type="hidden" id="txtreq_<? echo $i; ?>" name="txtreq[]" value="0" />
            <input type="hidden" id="txtreqdtlsid_<? echo $i; ?>" name="txtreqdtlsid[]" value="0" />
            <input type="hidden" name="txtrowid_<? echo $i; ?>" id="txtrowid_<? echo $i; ?>" />
            <input type="hidden" name="upDtlsId[]" id="upDtlsId_<? echo $i; ?>" />
            <input type="hidden" name="process_loss_method_id[]" id="process_loss_method_id_<? echo $i; ?>" value="<?=$process_loss_method?>"/>
            </td>
            <td>
            <?
            echo create_drop_down( "cbobuyername_".$i, 90, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$row[csf("company_name")]."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", $row[csf("buyer_name")], "",1, "", "", "", "", "", "", "cbobuyername[]" );
            ?>
            </td>
            <td align="center"><input type="text" id="txtstyleno_<? echo $i; ?>" name="txtstyleno[]" class="text_boxes" value="<? echo $row[csf("style_ref_no")]; ?>" style="width:75px;" readonly disabled /></td>
            <td align="center">
              <input type="text"  id="txtyarncolor_<? echo $i; ?>" name="txtyarncolor[]" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $color_arr[$row[csf("color")]];//$color_arr[$color_val]; ?>" style="width:75px;" readonly/>
            </td>
            <td align="center">
            <?
            echo create_drop_down( "cbocount_".$i, 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", $row[csf("count_id")], "",0, "", "", "", "", "", "", "cbocount[]" ); 
            ?>
            </td> 
            <td align="center">
            <?  
            echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("copm_one_id")], "",0, "", "", "", "", "", "", "cbocompone[]" ); 
            ?>
            </td> 
            <td>
            <? $percent_one = ($row[csf("percent_one")])? $row[csf("percent_one")]: "100";?>
            <input type="text" name="txtpacent[]" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="<? echo $percent_one; ?>" style="width:40px;"/>
            </td>
            <td>
            <?  
            echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",0, "", "", "", "", "", "", "cbotype[]" ); 
            ?>
            </td>
            <td>
            <? 
            echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 15, "",1, "", "", "", "", "", "", "cbouom[]" ); 
            ?>
            </td>
            <td>
            <input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty[]" class="text_boxes_numeric" style="width:50px" value="0" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty[]" value="0"  />
            <input type="hidden" id="HiddenWoQnty_<? echo $i; ?>" name="HiddenWoQnty[]" value="<? echo number_format($WoQtyCheckArr[$row["ID"]][$row["COUNT_ID"]][$row["COPM_ONE_ID"]][$row["TYPE_ID"]][$row["COLOR"]]["REQ_QUANTITY"],2,'.',''); ?>"/>
            <input type="hidden" id="HiddenPreCostQty_<? echo $i; ?>" name="HiddenPreCostQty[]" value="<?=number_format($cons_qnty,2)?>"/>
            </td>
            <td><input type="text" name="txtrate[]" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($row[csf("rate_ratio")],4,'.',''); ?>"  style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" name="hiddentxtrate[]" id="hiddentxtrate_<? echo $i; ?>" value="<? echo number_format($row[csf("rate_ratio")],4,'.',''); ?>" /></td>
            <td><input type="text" name="txtamount[]" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="" style="width:55px;" readonly /></td>
            <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryStart[]" id="txtdeliveryStart_<? echo $i; ?>" placeholder="Select Date" /></td> 
            <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryEnd[]" id="txtdeliveryEnd_<? echo $i; ?>" placeholder="Select Date" /></td>
            <td>
            <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>);" disabled />
            <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" /></td>
		</tr>
		<?
		$i++;
	}
	?>
    <tr class="general" id="tr_<? echo $i; ?>">
        <td align="center">
        <input type="text" id="txtjobno_<? echo $i; ?>" name="txtjobno[]" class="text_boxes" value="" style="width:90px;" onDblClick="openmypage_job(<? echo $i; ?>)" placeholder="Doble Click For Job" readonly />
       
        <input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid[]" style="width:100px;">
        <input type="hidden" id="txtreq_<? echo $i; ?>" name="txtreq[]" value="0" />
        <input type="hidden" id="txtreqdtlsid_<? echo $i; ?>" name="txtreqdtlsid[]" value="0" />
        <input type="hidden" name="txtrowid[]" id="txtrowid_<? echo $i; ?>" />
        <input type="hidden" name="upDtlsId[]" id="upDtlsId_<? echo $i; ?>" />
        </td>
        <td id="buy_td">
        <?
            echo create_drop_down( "cbobuyername_".$i, 90, "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select --", 0, "",1,"","","","","","","cbobuyername[]" );
        ?>
        </td>
        <td align="center"><input type="text" name="txtstyleno[]" id="txtstyleno_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" readonly disabled /></td>
        <td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" onFocus="add_auto_complete( 1 )" />
            <input type="hidden" id="hidden_txtyarncolor_id" readonly/>
        </td>
        <td align="center">
        <? 
            echo create_drop_down( "cbocount_".$i, 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", 0, "",0,"","","","","","","cbocount[]" ); 
        ?>
        </td> 
        <td align="center">
        <?  
            echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", 0, "",0,"","","","","","","cbocompone[]"); 
        ?>
        </td> 
        <td><input type="text" id="txtpacent_<? echo $i; ?>" name="txtpacent[]"  class="text_boxes" value="100" style="width:40px;" /></td>
        <td>
        <?  
            echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"","","","","","","cbotype[]" ); 
        ?>
        </td>
        <td>
        <? 
            echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 15, "",1,"","","","","","","cbouom[]"); 
        ?>
        </td>
        <td>
            <input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty[]" class="text_boxes_numeric" style="width:50px" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty[]" value=""/>
        </td>
        <td>
            <input type="text" name="txtrate[]" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="" style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddentxtrate_<? echo $i; ?>" name="hiddentxtrate[]" value=""/>
        </td>
        <td><input type="text" name="txtamount[]" id="txtamount_1" class="text_boxes_numeric" value="" style="width:55px;" readonly /></td>
        <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryStart[]" id="txtdeliveryStart_<? echo $i; ?>" placeholder="Select Date" /></td>	
        <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryEnd[]" id="txtdeliveryEnd_<? echo $i; ?>" placeholder="Select Date" /></td>
    
        <td>
        <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>);" disabled />
        <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" /></td>
    </tr>
	<? 
}

if($action=="show_req_dtls_listview")
{
	extract($_REQUEST); 
	$data_exp = explode("***",$data);
	$req_dtls_id_all = $data_exp[0];
	$update_id=str_replace("'","",$data_exp[2]);
	$req_mst_id_all =implode(",",array_unique(explode(",",$data_exp[1])));
	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	
	$sql="SELECT a.id as mst_id, a.requ_prefix_num, a.requ_no, a.company_id, b.id as dtls_id, b.job_id, b.job_no, b.buyer_id, b.style_ref_no, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom, b.quantity as req_qnty, b.rate, b.yarn_inhouse_date, b.remarks, a.basis 
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	where a.id=b.mst_id and a.id in($req_mst_id_all) and b.id in ($req_dtls_id_all) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70";
	 //echo $sql."<br>";
	$result=sql_select($sql);
	 
	if( count($result)==0 ){ echo "No Data Found";die;}
	$i=1;$dtls_found_arr=array();
	foreach($result as $row)
	{
		$key=$row[csf('job_no')].$row[csf('count_id')].$row[csf('yarn_type_id')].$row[csf('composition_id')];
		?>		
		<tr class="general" id="tr_<? echo $i; ?>">
			<td align="center">
			<input type="text" id="txtjobno_<? echo $i; ?>" name="txtjobno[]" class="text_boxes" value="<? echo $row[csf("job_no")]; ?>" style="width:90px;" onDblClick="openmypage_job(<? echo $i; ?>)" placeholder="Doble Click For Job" readonly disabled />
		   
			<input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid[]" value="<? echo $row[csf("job_id")]; ?>">
			<input type="hidden" id="txtreq_<? echo $i; ?>" name="txtreq[]" value="<? echo $row[csf("requ_no")]; ?>" />
			<input type="hidden" id="txtreqdtlsid_<? echo $i; ?>" name="txtreqdtlsid[]" value="<? echo $row[csf("dtls_id")]; ?>" />
			<input type="hidden" name="txtrowid[]" id="txtrowid_<? echo $i; ?>" />
			<input type="hidden" name="upDtlsId[]" id="upDtlsId_<? echo $i; ?>" />
			</td>
			<td>
            <?
            echo create_drop_down( "cbobuyername_".$i, 90, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$row[csf("company_id")]."' and buy.id in(".$row[csf("buyer_id")].") $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", $row[csf("buyer_id")], "",1, "", "", "", "", "", "", "cbobuyername[]" );
            ?>
            </td>
            <td align="center"><input type="text" id="txtstyleno_<? echo $i; ?>" name="txtstyleno[]" class="text_boxes" value="<? echo $row[csf("style_ref_no")]; ?>" style="width:75px;" readonly disabled /></td>
            <td align="center">
              <input type="text"  id="txtyarncolor_<? echo $i; ?>" name="txtyarncolor[]" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $color_arr[$row[csf("color_id")]]; ?>" style="width:75px;" readonly/>
            </td>
            <td align="center">
            <?
            echo create_drop_down( "cbocount_".$i, 70, "select id, yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 and id in(".$row[csf("count_id")].") order by yarn_count", "id,yarn_count",1,"-- Select --", $row[csf("count_id")], "",0, "", "", "", "", "", "", "cbocount[]" ); 
            ?>
            </td> 
            <td align="center">
            <?  
            echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("composition_id")], "",0, "", "", "", "", "", "", "cbocompone[]" ); 
            ?>
            </td> 
            <td>
            <? $percent_one = ($row[csf("com_percent")])? $row[csf("com_percent")]: "100";?>
            <input type="text" name="txtpacent[]" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="<? echo $percent_one; ?>" style="width:40px;"/>
            </td>
            <td>
            <?  
            echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("yarn_type_id")], "",0, "", "", "", "", "", "", "cbotype[]" ); 
            ?>
            </td>
            <td>
            <? 
            echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 15, "",1, "", "", "", "", "", "", "cbouom[]" ); 
            ?>
            </td>
            <td>
            <input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty[]" class="text_boxes_numeric" style="width:50px" value="<? echo number_format($row[csf("req_qnty")],4,'.',''); ?>" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty[]" value="<? echo number_format($row[csf("req_qnty")],4,'.',''); ?>"  />
            </td>
            <td><input type="text" name="txtrate[]" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($row[csf("rate")],4,'.',''); ?>"  style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" name="hiddentxtrate[]" id="hiddentxtrate_<? echo $i; ?>" value="<? echo number_format($row[csf("rate")],4,'.',''); ?>" /></td>
            <td><input type="text" name="txtamount[]" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format(($row[csf("req_qnty")]*$row[csf("rate")]),4,'.',''); ?>" style="width:55px;" readonly /></td>
            <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryStart[]" id="txtdeliveryStart_<? echo $i; ?>" placeholder="Select Date" /></td> 
            <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryEnd[]" id="txtdeliveryEnd_<? echo $i; ?>" placeholder="Select Date" /></td>
            <td>
            <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>);" disabled />
            <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" />
            </td>
		</tr>
		<? 
		$i++;
	}
	exit();
}

if($action=="ini_dtls_listview")
{
	extract($_REQUEST); 
	$data_exp = explode("***",$data);
	?>		
    <tr class="general" id="tr_1">
                        <td align="center">
                        <input type="text" id="txtjobno_1" name="txtjobno[]" class="text_boxes" value="" style="width:90px;" onDblClick="openmypage_job(1)" placeholder="Doble Click For Job" readonly />
                       
                        <input type="hidden" id="txtjobid_1" name="txtjobid[]" />
                        <input type="hidden" id="txtreq_1" name="txtreq[]" />
                        <input type="hidden" id="txtreqdtlsid_1" name="txtreqdtlsid[]" />
                        <input type="hidden" name="txtrowid[]" id="txtrowid_1" />
                        <input type="hidden" name="upDtlsId[]" id="upDtlsId_1" />
                        </td>
                        <td id="buy_td">
                        <?
                            echo create_drop_down( "cbobuyername_1", 90, "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select --", 0, "",1,"","","","","","","cbobuyername[]" );
                        ?>
                        </td>
                        <td align="center"><input type="text" name="txtstyleno[]" id="txtstyleno_1" class="text_boxes" value="" style="width:75px;" readonly disabled /></td>
                        <td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_1" class="text_boxes" value="" style="width:75px;" onFocus="add_auto_complete( 1 )" />
                            <input type="hidden" id="hidden_txtyarncolor_id" readonly/>
                        </td>
                        <td align="center">
                        <? 
                            echo create_drop_down( "cbocount_1", 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", 0, "",0,"","","","","","","cbocount[]" ); 
                        ?>
                        </td> 
                        <td align="center">
                        <?  
                            echo create_drop_down( "cbocompone_1", 100, $composition,"", 1, "-- Select --", 0, "",0,"","","","","","","cbocompone[]"); 
                        ?>
                        </td> 
                        <td><input type="text" id="txtpacent_1" name="txtpacent[]"  class="text_boxes" value="100" style="width:40px;" /></td>
                        <td>
                        <?  
                            echo create_drop_down( "cbotype_1", 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"","","","","","","cbotype[]" ); 
                        ?>
                        </td>
                        <td>
                        <? 
                            echo create_drop_down( "cbouom_1", 60, $unit_of_measurement,"", 1, "-- Select--", 15, "",1,"","","","","","","cbouom[]"); 
                        ?>
                        </td>
                        <td>
                            <input type="text" id="reqqnty_1" name="reqqnty[]" class="text_boxes_numeric" style="width:50px" onKeyUp="calculate_amount(1)" />
                            <input type="hidden" id="hiddenreqqnty_1" name="hiddenreqqnty[]" value=""/>
                        </td>
                        <td>
                            <input type="text" name="txtrate[]" id="txtrate_1" class="text_boxes_numeric" value="" style="width:40px;" onKeyUp="calculate_amount(1)" />
                            <input type="hidden" id="hiddentxtrate_1" name="hiddentxtrate[]" value=""/>
                        </td>
                        <td><input type="text" name="txtamount[]" id="txtamount_1" class="text_boxes_numeric" value="" style="width:55px;" readonly /></td>
                        <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryStart[]" id="txtdeliveryStart_1" placeholder="Select Date" /></td>	
                        <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryEnd[]" id="txtdeliveryEnd_1" placeholder="Select Date" /></td>
                        <td>
                        <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1);" />
                        <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(1);" />
                        </td>
                    </tr>
    <?
	exit();
}

if($action=="show_dtls_listview_update")
{
	extract($_REQUEST); 
	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	$sql = "select b.id, a.wo_basis_id, b.requisition_no, b.requisition_dtls_id, b.job_id, b.job_no, b.buyer_id, b.style_no, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name, b.req_quantity, b.supplier_order_quantity, b.uom, b.rate, b.amount, b.yarn_inhouse_date, b.delivery_end_date
	from wo_non_order_info_mst a, wo_non_order_info_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data";
	//echo $sql;die;	
	$result = sql_select($sql);
	
	$i=1;$dtls_found_arr=array();
	foreach($result as $row)
	{
		?>		
		<tr class="general" id="tr_<? echo $i; ?>">
			<td align="center">
			<input type="text" id="txtjobno_<? echo $i; ?>" name="txtjobno[]" class="text_boxes" value="<? echo $row[csf("job_no")]; ?>" style="width:90px;" onDblClick="openmypage_job(<? echo $i; ?>)" placeholder="Doble Click For Job" readonly disabled />
		   
			<input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid[]" value="<? echo $row[csf("job_id")]; ?>">
			<input type="hidden" id="txtreq_<? echo $i; ?>" name="txtreq[]" value="<? echo $row[csf("requisition_no")]; ?>" />
			<input type="hidden" id="txtreqdtlsid_<? echo $i; ?>" name="txtreqdtlsid[]" value="<? echo $row[csf("requisition_dtls_id")]; ?>" />
			<input type="hidden" name="txtrowid[]" id="txtrowid_<? echo $i; ?>" />
			<input type="hidden" name="upDtlsId[]" id="upDtlsId_<? echo $i; ?>" value="<? echo $row[csf("id")]; ?>" />
			</td>
			<td>
            <?
            echo create_drop_down( "cbobuyername_".$i, 90, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 and buy.id in(".$row[csf("buyer_id")].") $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $row[csf("buyer_id")], "",1, "", "", "", "", "", "", "cbobuyername[]" );
            ?>
            </td>
            <td align="center"><input type="text" id="txtstyleno_<? echo $i; ?>" name="txtstyleno[]" class="text_boxes" value="<? echo $row[csf("style_no")]; ?>" style="width:75px;" readonly disabled /></td>
            <td align="center">
              <input type="text"  id="txtyarncolor_<? echo $i; ?>" name="txtyarncolor[]" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $color_arr[$row[csf("color_name")]]; ?>" style="width:75px;" readonly/>
            </td>
            <td align="center">
            <?
            echo create_drop_down( "cbocount_".$i, 70, "select id, yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", $row[csf("yarn_count")], "",0, "", "", "", "", "", "", "cbocount[]" ); 
            ?>
            </td> 
            <td align="center">
            <?  
            echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("yarn_comp_type1st")], "",0, "", "", "", "", "", "", "cbocompone[]" ); 
            ?>
            </td> 
            <td>
            <? $percent_one = ($row[csf("yarn_comp_percent1st")])? $row[csf("yarn_comp_percent1st")]: "100";?>
            <input type="text" name="txtpacent[]" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="<? echo $percent_one; ?>" style="width:40px;"/>
            </td>
            <td>
            <?  
            echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("yarn_type")], "",0, "", "", "", "", "", "", "cbotype[]" ); 
            ?>
            </td>
            <td>
            <? 
            echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 15, "",1, "", "", "", "", "", "", "cbouom[]" ); 
            ?>
            </td>
            <td>
            <input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty[]" class="text_boxes_numeric" style="width:50px" value="<? echo number_format($row[csf("supplier_order_quantity")],4,'.',''); ?>" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty[]" value="<? echo number_format($row[csf("supplier_order_quantity")],4,'.',''); ?>"  />
            </td>
            <td><input type="text" name="txtrate[]" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($row[csf("rate")],4,'.',''); ?>"  style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" name="hiddentxtrate[]" id="hiddentxtrate_<? echo $i; ?>" value="<? echo number_format($row[csf("rate")],4,'.',''); ?>" /></td>
            <td><input type="text" name="txtamount[]" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format(($row[csf("supplier_order_quantity")]*$row[csf("rate")]),4,'.',''); ?>" style="width:55px;" readonly /></td>
            <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryStart[]" id="txtdeliveryStart_<? echo $i; ?>" value="<? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format( $row[csf("yarn_inhouse_date")]); else echo ""; ?>" placeholder="Select Date" /></td> 
            <td><input class="datepicker" type="text" style="width:60px;" name="txtdeliveryEnd[]" id="txtdeliveryEnd_<? echo $i; ?>" value="<? if($row[csf("delivery_end_date")]!="" && $row[csf("delivery_end_date")]!="0000-00-00") echo change_date_format( $row[csf("delivery_end_date")]); else echo ""; ?>" placeholder="Select Date" /></td>
            <td>
            <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>);" disabled />
            <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" />
            </td>
		</tr>
		<? 
		$total_wo_qnty+=number_format($row[csf("supplier_order_quantity")],4,'.','');
		$total_amount+=number_format(($row[csf("supplier_order_quantity")]*$row[csf("rate")]),4,'.','');
		$i++;
		
	}
	?>
	     <tr  >
			<td >&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><b> Total </b></td>
			<td align="right"> <b><? echo $total_wo_qnty?></b></td>
			<td>&nbsp;</td>
			<td align="right" ><b><?echo $total_amount?></b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	<?
	exit();
	
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode,1);
	extract($_REQUEST);
	$terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
	$terms_name = "";
	foreach( $terms_sql as $result )
	{ 
		//$terms_name.= '{value:"'.$result[csf('terms')].'",id:'.$result[csf('id')]."},";
		$terms_name.= '{value:"'.str_replace('"',"'",$result[csf('terms')]).'",id:'.$result[csf('id')]."},";
	}
	?>
	<script>
	
		function termsName(rowID)
		{
			$("#termsconditionID_"+rowID).val('');
			 
			$(function() {
				var terms_name = [<? echo substr($terms_name, 0, -1); ?>]; 
				$("#termscondition_"+rowID).autocomplete({
					source: terms_name,			
					select: function (event, ui) { 
						$("#termscondition_"+rowID).val(ui.item.value); // display the selected text
						$("#termsconditionID_"+rowID).val(ui.item.id); // save selected id to hidden input
					} 
				});
			});
		}
	 
		function add_break_down_tr(i) 
		{
			alert(i);return
			var row_num=$('#tbl_termcondi_details tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				
				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name + i },
					  'value': function(_, value) { return value }              
					});  
				  }).end().appendTo("#tbl_termcondi_details"); 
				$("#tbl_termcondi_details tr:last td:first").html(i);    
				$('#termscondition_'+i).removeAttr("onKeyPress").attr("onKeyPress","termsName("+i+");"); 
				$('#termscondition_'+i).removeAttr("onKeyUp").attr("onKeyUp","termsName("+i+");");   
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#termscondition_'+i).val("");
				$('#termsconditionID_'+i).val("");
			}
		}
	
		function fn_deletebreak_down_tr(rowNo) 
		{   
			var numRow = $('table#tbl_termcondi_details tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}
			
		}
	
		function fnc_work_order_terms_condition( operation )
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{			
				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('txt_wo_number*termscondition_'+i+'*termsconditionID_'+i,"../../../");
			}
			var data="action=save_update_delete_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
			//alert(data);
			//freeze_window(operation);
			http.open("POST","sample_yarn_work_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_order_terms_condition_reponse;
		}
	
		function fnc_yarn_order_terms_condition_reponse()
		{
			if(http.readyState == 4) 
			{
				//alert(http.responseText);
				var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					parent.emailwindow.hide();
				}
			}
		}
	
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
    <? echo load_freeze_divs ("../../../",$permission,1); ?>
	<fieldset>
		   <form id="termscondi_1" autocomplete="off">
				<input type="hidden" id="txt_wo_number" name="txt_wo_number" value="<? echo str_replace("'","",$update_id) ?>"/>
				<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                    <thead>
                        <tr>
                            <th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $terms_and_conditionID = return_field_value("terms_and_condition","wo_non_order_info_mst","id = $update_id");  
                    $flag=0;
                    if($terms_and_conditionID=="") 
                        $condd = " and is_default=1"; 
                    else
                    { 
                        $condd = " and id in ($terms_and_conditionID)";
                        $flag=1;
                    }
                    $data_array=sql_select("select id, terms from lib_terms_condition where page_id=100 $condd order by id");
                    if( count($data_array)>0 )
                    {
                        $i=0;
                        foreach( $data_array as $row )
                        {
                            $i++;
                            ?>
                            <tr id="settr_1" align="center">
                                <td>
									<? echo $i;?>
                                </td>
                                <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" onKeyPress="termsName(<? echo $i;?>)" onKeyUp="termsName(<? echo $i;?>)" /> 
                                    <input type="hidden" id="termsconditionID_<? echo $i;?>"  name="termsconditionID_<? echo $i;?>" value="<? echo $row[csf('id')]; ?>"  readonly />
                                    </td>
                                    <td> 
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                </td>
                            </tr>
                            <?
                        }
                    }
                    ?>
                </tbody>
            </table>
            <table width="650" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" height="15" width="100%"> </td>
                </tr>
                <tr>
                    <td align="center" width="100%" class="button_container">
						<?
                            echo load_submit_buttons( $permission, "fnc_work_order_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
                        ?>
                    </td> 
                </tr>
            </table>
        </form>
    </fieldset>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="save_update_delete_terms_condition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	 //echo "10**";echo $total_row.'reza';die;
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		 
		$terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
		$terms_name = array();
		foreach( $terms_sql as $result )
		{ 
			$terms_name[$result[csf('terms')]] = $result[csf('id')];
		}
		
		$id=return_next_id( "id", "lib_terms_condition", 1 );
		$field_array = "id,terms,page_id"; $data_array = "";
		$idsArr = "";$j=0;
		for ($i=1;$i<=$total_row;$i++)
		{
			 $termscondition = "termscondition_".$i;
			 $termscondition = $$termscondition;
			 $termsconditionID = "termsconditionID_".$i;
			 $termsconditionID = $$termsconditionID;
			 if(str_replace("'","",$termsconditionID) == "")
			 {
				 $j++;
				 if ($j!=1){ $data_array .=",";}
				 $data_array .="(".$id.",".$termscondition.",100)";
				 $idsArr[]=$id;
				 $id=$id+1;				 
			 }
			 else
			 {
				 $idsArr[]=str_replace("'","",$termsconditionID);
			 }
		 }
		
	 //echo "insert into lib_terms_condition (".$field_array.") values ".$data_array."";die;
 		if($data_array!="")
		{
			$CondrID=sql_insert("lib_terms_condition",$field_array,$data_array,0);
		}
		
		
		foreach($idsArr as $value)
		{
		   $value = str_replace("'","",$value);
		}
		
		$idsArr = implode(",", $idsArr);
		$rID=true;
		$rID = sql_update("wo_non_order_info_mst","terms_and_condition","'$idsArr'","id",str_replace("'","",$txt_wo_number),1);
		
		
		
		if($db_type==0)
		{
			if( $rID && $data_array!="" && $CondrID){
				mysql_query("COMMIT");  
				echo "0**";
			}
			else if($rID && $data_array==""){
				mysql_query("COMMIT");  
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		//oci_commit($con); oci_rollback($con); 
		if($db_type==2 || $db_type==1 )
		{
			if( $rID && $data_array!="" && $CondrID){
				oci_commit($con);  
				echo "0**";
			}
			else if($rID && $data_array==""){
				oci_commit($con); 
				echo "0**";
			}
			else{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
	//	if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 
		$terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
		$terms_name = array();
		foreach( $terms_sql as $result )
		{ 
			$terms_name[$result[csf('terms')]] = $result[csf('id')];
		}
		 
		$id=return_next_id( "id", "lib_terms_condition", 0 );
		$field_array = "id,terms"; $data_array = "";
		$idsArr = "";$j=0;
		for ($i=1;$i<=$total_row;$i++)
		{
			 $termscondition = "termscondition_".$i;
			 $termscondition = $$termscondition;
			 $termsconditionID = "termsconditionID_".$i;
			 $termsconditionID = $$termsconditionID;
			 if(str_replace("'","",$termsconditionID) == "")
			 {
				 $j++;
				 if ($j!=1){ $data_array .=",";}
				 $data_array .="(".$id.",".$termscondition.",100)";
				 $idsArr[]=$id;
				 $id=$id+1;				 
			 }
			 else
			 {
				 $idsArr[]=$termsconditionID;
			 }
		 }
		
 		if($data_array!="")
		{
			$CondrID=sql_insert("lib_terms_condition",$field_array,$data_array,1);
		}
		
		foreach($idsArr as &$value)
		{
		   $value = str_replace("'","",$value);
		}
		$idsArr = implode(",", $idsArr);
		$rID = sql_update("wo_non_order_info_mst","terms_and_condition","'$idsArr'","wo_number",$txt_wo_number,1);
		//echo $rID;die;
		//oci_commit($con); oci_rollback($con); 		
		//check_table_status( $_SESSION['menu_id'],0);		
		if($db_type==0)
		{
			if( $rID && $data_array!="" && $CondrID){
				oci_commit($con);  
				echo "0**";
			}
			else if($rID && $data_array==""){
				oci_commit($con);  
				echo "0**";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if( $rID && $data_array!="" && $CondrID){
				mysql_query("COMMIT");  
				echo "0**";
			}
			else if($rID && $data_array==""){
				mysql_query("COMMIT");  
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}	
}

if($action=="save_update_delete")
{	
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  ); 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$ready_to_approved = str_replace("'", "", $cbo_ready_to_approved);
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		if( check_table_status( 175, 1 )==0 ) { echo "15**0"; die;}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
			
		// master table netry here---------------------------------------
		$id=return_next_id("id", "wo_non_order_info_mst", 1);
		//$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name and item_category=1  and $year_cond=".date('Y',time())." order by id desc ", "wo_number_prefix", "wo_number_prefix_num" ));
		$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name and entry_form = 284  and $year_cond=".date('Y',time())." order by id desc ", "wo_number_prefix", "wo_number_prefix_num" ));
		//echo "10**".$new_wo_number[0]."_".$new_wo_number[1]."_".$new_wo_number[2];die;
		$field_array_mst="id, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, currency_id, supplier_id, wo_date, pay_mode, wo_basis_id, delivery_date, booking_type, pi_issue_to, payterm_id, source, delivery_mode, do_no, tenor, inco_term, delivery_place, attention, remarks, team_leader, dealing_marchant,  ready_to_approved, entry_form, inserted_by, insert_date";
		$data_array_mst="(".$id.",".$garments_nature.",'".$new_wo_number[1]."','".$new_wo_number[2]."','".$new_wo_number[0]."',".$cbo_company_name.",".$cbo_currency.",".$cbo_supplier.",".$txt_wo_date.",".$cbo_pay_mode.",".$cbo_wo_basis.",".$txt_delivery_date.",".$cbo_booking_type.",".$cbo_pi_issue_to.",".$cbo_payterm_id.",".$cbo_source.",".$cbo_delivery_mode.",".$txt_do_no.",".$txt_tenor.",".$cbo_inco_term.",".$txt_inco_term_place.",".$txt_attention.",".$txt_remarks.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_ready_to_approved.",284,'".$user_id."','".$pc_date_time."')";
		
 		//$rID=sql_insert("wo_non_order_info_mst",$field_array,$data_array,1);
		
		// details table entry here --------------------------------------
		
		$field_array_dtls="id, mst_id, requisition_no, requisition_dtls_id, job_id, job_no, buyer_id, style_no, item_id, item_category_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, color_name, uom, req_quantity, supplier_order_quantity, rate, amount, yarn_inhouse_date, delivery_end_date, inserted_by, insert_date";
		
		$total_row = str_replace("'","",$total_row);
		$wo_basis=str_replace("'","",$cbo_wo_basis);
		
		$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
		$data_array_dtls="";
		for($i=1;$i<=$total_row;$i++)
		{
			$txt_req = "txtreq_".$i;
			$txt_req_dtls_id = "txtreqdtlsid_".$i;
			$txt_job = "txtjobno_".$i;
			$txt_job_id = "txtjobid_".$i;
			$txt_buyer_id = "cbobuyername_".$i;
			$txt_style = "txtstyleno_".$i;
			
			$txt_color	 = "txtyarncolor_".$i;
			$cbocount	 = "cbocount_".$i;
			$cbocompone	 = "cbocompone_".$i;
			$percentone	 = "txtpacent_".$i;
			$cbotype	 = "cbotype_".$i;
			
			
			$cbo_uom	 = "cbouom_".$i;
			$txt_req_qnty  = "reqqnty_".$i;
			$txt_quantity  = "reqqnty_".$i;
			$txt_rate    = "txtrate_".$i;
			$txt_amount  = "txtamount_".$i;
			$txt_inhouse_date  = "txtdeliveryStart_".$i;
			$txt_delivery_end_date  = "txtdeliveryEnd_".$i;
			$upDtlsId  = "upDtlsId_".$i;
			
			if(str_replace("'","",$$txt_color)!="")
			{
				if (!in_array(str_replace("'","",$$txt_color),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txt_color), $color_library, "lib_color", "id,color_name","284");
					$new_array_color[$color_id]=str_replace("'","",$$txt_color);
				}
				else $color_id =  array_search(str_replace("'","",$$txt_color), $new_array_color);
			}
			else
			{
				$color_id=0;
			}
			
			if($$txt_quantity!="" || $$txt_rate!="")
			{
				if($data_array_dtls!="") $data_array_dtls .=",";
				$data_array_dtls .="(".$dtlsid.",".$id.",".$$txt_req.",".$$txt_req_dtls_id.",".$$txt_job_id.",".$$txt_job.",".$$txt_buyer_id.",".$$txt_style.",0,1,".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbotype.",'".$color_id."',".$$cbo_uom.",".$$txt_req_qnty.",".$$txt_quantity.",".$$txt_rate.",".$$txt_amount.",".$$txt_inhouse_date.",".$$txt_delivery_end_date.",'".$user_id."','".$pc_date_time."')";
				$dtlsid=$dtlsid+1;
			}
		}
		
		
		
		//echo "5**insert into wo_non_order_info_dtls (".$field_array_dtls.") values".$data_array_dtls.""; //die;
		//echo "insert into wo_non_order_info_mst(".$field_array_mst.") values ".$data_array_mst." ";die;
		$rID=sql_insert("wo_non_order_info_mst",$field_array_mst,$data_array_mst,1);		
		$dtlsrID=sql_insert("wo_non_order_info_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "5**insert into wo_non_order_info_dtls ($field_array_dtls) values".$data_array_dtls;check_table_status( 175,0);oci_rollback($con);die;
		//echo "5**$rID ** $dtlsrID";check_table_status( 175,0);oci_rollback($con); die;
		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_wo_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);  
				echo "0**".$new_wo_number[0]."**".$id;
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		//release lock table
		check_table_status( 175,0);
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		if( check_table_status( 175, 1 )==0 ) { echo "15**0"; die;}
		
		
		// master table netry here---------------------------------------
		//$mst_id = return_field_value("id","wo_non_order_info_mst","wo_number=$txt_wo_number");
		$mst_id=str_replace("'","",$update_id);
		if($mst_id>0) $pay_mode=return_field_value("pay_mode","wo_non_order_info_mst","id=$mst_id and status_active=1","pay_mode");
		if($mst_id>0 && $pay_mode==2)
		{
			$pi_sql=sql_select("select a.id as pi_id, a.pi_number, b.work_order_id, b.item_prod_id, b.quantity as quantity, b.rate
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.item_category_id=1 and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$mst_id");
			if(count($pi_sql)>0)
			{
				echo "11**PI Number ".$pi_sql[0][csf("pi_number")]." Found . \n So Update/Delete Not Possible.**$mst_id";check_table_status( 175,0);disconnect($con);die;
			}
		}
		
		if($mst_id>0 && $pay_mode!=2)
		{
			$mrr_sql=sql_select("select a.id as mrr_id, a.recv_number, a.booking_id, b.prod_id, b.order_qnty, b.order_rate  
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=1 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$mst_id");
			if(count($mrr_sql)>0)
			{
				echo "11**Receive Number ".$mrr_sql[0][csf("recv_number")]." Found .  \n So Update/Delete Not Possible.**$mst_id";check_table_status( 175,0);disconnect($con);die;
			}
		}
		
		$field_array_mst="id, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, currency_id, supplier_id, wo_date, pay_mode, wo_basis_id, delivery_date, booking_type, pi_issue_to, payterm_id, source, delivery_mode, do_no, tenor, inco_term, delivery_place, attention, remarks, team_leader, dealing_marchant, ready_to_approved, entry_form, inserted_by, insert_date";
		$data_array_mst="(".$id.",".$garments_nature.",'".$new_wo_number[1]."','".$new_wo_number[2]."','".$new_wo_number[0]."',".$cbo_company_name.",".$cbo_currency.",".$cbo_supplier.",".$txt_wo_date.",".$cbo_pay_mode.",".$cbo_wo_basis.",".$txt_delivery_date.",".$cbo_booking_type.",".$cbo_pi_issue_to.",".$cbo_payterm_id.",".$cbo_source.",".$cbo_delivery_mode.",".$txt_do_no.",".$txt_tenor.",".$cbo_inco_term.",".$txt_inco_term_place.",".$txt_attention.",".$txt_remarks.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_ready_to_approved.",284,'".$user_id."','".$pc_date_time."')";
		
		if($mst_id!="")
		{
			$field_array_mst="currency_id*supplier_id*wo_date*pay_mode*delivery_date*booking_type*pi_issue_to*payterm_id*source*delivery_mode*do_no*tenor*inco_term*delivery_place*attention*remarks*team_leader*dealing_marchant*ready_to_approved*updated_by*update_date";
			$data_array_mst="".$cbo_currency."*".$cbo_supplier."*".$txt_wo_date."*".$cbo_pay_mode."*".$txt_delivery_date."*".$cbo_booking_type."*".$cbo_pi_issue_to."*".$cbo_payterm_id."*".$cbo_source."*".$cbo_delivery_mode."*".$txt_do_no."*".$txt_tenor."*".$cbo_inco_term."*".$txt_inco_term_place."*".$txt_attention."*".$txt_remarks."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$cbo_ready_to_approved."*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array_mst."<br>".$data_array_mst;die;
			//$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
		}
 		 
		
		$field_array_insert="id, mst_id, requisition_no, requisition_dtls_id, job_id, job_no, buyer_id, style_no, item_id, item_category_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, color_name, uom, req_quantity, supplier_order_quantity, rate, amount, yarn_inhouse_date, delivery_end_date, inserted_by, insert_date";
		//$field_array="supplier_order_quantity*rate*amount*updated_by*update_date*yarn_inhouse_date*delivery_end_date*number_of_lot*remarks";
		$field_array="requisition_no*requisition_dtls_id*job_id*job_no*buyer_id*style_no*yarn_count*yarn_comp_type1st*yarn_comp_percent1st*yarn_type*color_name*uom*req_quantity*supplier_order_quantity*rate*amount*yarn_inhouse_date*delivery_end_date*updated_by*update_date";
		
		$data_array=array();
		$update_ID=array();
		$data_array_insert="";
		$dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
		
		for($i=1;$i<=$total_row;$i++)
		{
			$txt_req = "txtreq_".$i;
			$txt_req_dtls_id = "txtreqdtlsid_".$i;
			$txt_job = "txtjobno_".$i;
			$txt_job_id = "txtjobid_".$i;
			$txt_buyer_id = "cbobuyername_".$i;
			$txt_style = "txtstyleno_".$i;
			
			$txt_color	 = "txtyarncolor_".$i;
			$cbocount	 = "cbocount_".$i;
			$cbocompone	 = "cbocompone_".$i;
			$percentone	 = "txtpacent_".$i;
			$cbotype	 = "cbotype_".$i;
			
			
			$cbo_uom	 = "cbouom_".$i;
			$txt_req_qnty  = "reqqnty_".$i;
			$txt_quantity  = "reqqnty_".$i;
			$txt_rate    = "txtrate_".$i;
			$txt_amount  = "txtamount_".$i;
			$txt_inhouse_date  = "txtdeliveryStart_".$i;
			$txt_delivery_end_date  = "txtdeliveryEnd_".$i;
			$upDtlsId  = "upDtlsId_".$i;
			
			
			if($$txt_quantity!="" || $$txt_rate!="") //check blank row  
			{
				if(str_replace("'","",$$txt_color)!="")
				{
					if (!in_array(str_replace("'","",$$txt_color),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txt_color), $color_library, "lib_color", "id,color_name","284");
						$new_array_color[$color_id]=str_replace("'","",$$txt_color);
					}
					else $color_id =  array_search(str_replace("'","",$$txt_color), $new_array_color);
				}
				else
				{
					$color_id=0;
				}
				
				$updtls_id=str_replace("'","",$$upDtlsId);
				if($updtls_id>0) //update
				{
					$update_ID[]=$updtls_id;
					$data_array[$updtls_id]=explode("*",("".$$txt_req."*".$$txt_req_dtls_id."*".$$txt_job_id."*".$$txt_job."*".$$txt_buyer_id."*".$$txt_style."*".$$cbocount."*".$$cbocompone."*".$$percentone."*".$$cbotype."*'".$color_id."'*".$$cbo_uom."*".$$txt_req_qnty."*".$$txt_quantity."*".$$txt_rate."*".$$txt_amount."*".$$txt_inhouse_date."*".$$txt_delivery_end_date."*'".$user_id."'*'".$pc_date_time."'"));
					$all_up_dtls_id_arr[$updtls_id]=$updtls_id;
				}
				else // new insert
				{
					if($$txt_quantity!="" || $$txt_rate!="")
					{
						if($data_array_insert!="") $data_array_insert.=",";
						$data_array_insert .="(".$dtlsid.",".$id.",".$$txt_req.",".$$txt_req_dtls_id.",".$$txt_job_id.",".$$txt_job.",".$$txt_buyer_id.",".$$txt_style.",0,1,".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbotype.",'".$color_id."',".$$cbo_uom.",".$$txt_req_qnty.",".$$txt_quantity.",".$$txt_rate.",".$$txt_amount.",".$$txt_inhouse_date.",".$$txt_delivery_end_date.",'".$user_id."','".$pc_date_time."')";
						$dtlsid=$dtlsid+1;
					}
				}			
			}//end if cond
		}
		$prev_data=sql_select("select id from wo_non_order_info_dtls where status_active=1 and is_deleted=0 and mst_id=$mst_id");
		$deleted_id="";
		foreach($prev_data as $row)
		{
			if($all_up_dtls_id_arr[$row[csf("id")]]=="")
			{
				$deleted_id .=$row[csf("id")].",";
			}
		}
		$deleted_id=chop($deleted_id,",");
		//echo "10** $deleted_id";print_r($all_up_dtls_id_arr);check_table_status( 175,0);die;
		//print_r($data_array);die;
		//echo "insert into wo_non_order_info_dtls( ".$field_array_insert.") values ".$data_array_insert."";die;
		$rID=$delete_details=$dtlsrIDI=$dtlsrID=true;
		if($mst_id!="")
		{
			$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
			//echo "10***".$field_array_mst.'<br>'.$data_array_mst;die;
		}	
		if($deleted_id!="")
		{
			$field_array_dtls_del="updated_by*update_date*status_active*is_deleted";
			$data_array_dtls_del="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$delete_details=sql_multirow_update("wo_non_order_info_dtls",$field_array_dtls_del,$data_array_dtls_del,"id",$deleted_id,1);
			//$delete_details = execute_query("UPDATE wo_non_order_info_dtls SET status_active=0,is_deleted=1 WHERE id in ($txt_delete_row)",1);
		}
		if($data_array_insert!="")
		{		
			$dtlsrIDI=sql_insert("wo_non_order_info_dtls",$field_array_insert,$data_array_insert,1);
		}
		if(count($update_ID)>0)
		{
			// bulk_update_sql_statement( $table, $id_column, $update_column, $data_values, $id_count )
			$dtlsrID=execute_query(bulk_update_sql_statement("wo_non_order_info_dtls","id",$field_array,$data_array,$update_ID));
		}
		//echo $dtlsrID;die;
		//echo "10**".$rID."=".$delete_details."=".$dtlsrIDI."=".$dtlsrID;check_table_status( 175,0);oci_rollback($con);die;
		if($db_type==0)
		{
			if($rID && $dtlsrIDI && $dtlsrID && $delete_details)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_wo_number)."**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrIDI && $dtlsrID && $delete_details)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_wo_number)."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		//release lock table
		check_table_status( 175,0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
        $mst_id=str_replace("'","",$update_id);
		if($mst_id>0) $pay_mode=return_field_value("pay_mode","wo_non_order_info_mst","id=$mst_id and status_active=1","pay_mode");
		if($mst_id>0 && $pay_mode==2)
		{
			$pi_sql=sql_select("select a.id as pi_id, a.pi_number, b.work_order_id, b.item_prod_id, b.quantity as quantity, b.rate
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.item_category_id=1 and a.pi_basis_id=1 and a.goods_rcv_status<>1 and b.work_order_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_id=$mst_id");
			if(count($pi_sql)>0)
			{
				
				echo "11**Already add in PI Number (".$pi_sql[0][csf("pi_number")]."). \n Update/Delete Not Possible.**$mst_id";check_table_status( 175,0);disconnect($con);die;
			}
		}
		
		if($mst_id>0 && $pay_mode!=2)
		{
			$mrr_sql=sql_select("select a.id as mrr_id, a.recv_number, a.booking_id, b.prod_id, b.order_qnty, b.order_rate  
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=1 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$mst_id");
			if(count($mrr_sql)>0)
			{
				echo "11**Already add in Receive Number (".$mrr_sql[0][csf("recv_number")].").  \n Update/Delete Not Possible.**$mst_id";check_table_status( 175,0);disconnect($con);die;
			}
		}
		
		
		if($mst_id!="")
		{
			$field_array_mst="buyer_po*wo_date*supplier_id*attention*buyer_name*style*wo_basis_id*currency_id*delivery_date*source*pay_mode*do_no*remarks*payterm_id*tenor*inco_term*pi_issue_to*updated_by*update_date*ready_to_approved*delivery_place*delivery_mode";
			$data_array_mst="".$txt_buyer_po."*".$txt_wo_date."*".$cbo_supplier."*".$txt_attention."*".$txt_buyer_name."*".$txt_style."*".$cbo_wo_basis."*".$cbo_currency."*".$txt_delivery_date."*".$cbo_source."*".$cbo_pay_mode."*".$txt_do_no."*".$txt_remarks."*".$cbo_payterm_id."*".$txt_tenor."*".$cbo_inco_term."*".$cbo_pi_issue_to."*'".$user_id."'*'".$pc_date_time."'*".$ready_to_approved."*".$txt_inco_term_place."*".$cbo_delivery_mode."";
			//echo $field_array_mst."<br>".$data_array_mst;die;
			$rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
		}
		/*$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = return_field_value("id","wo_non_order_info_mst","wo_number like $txt_wo_number");	
		if($mst_id=="" || $mst_id==0){ echo "15**0"; die;}
 		$rID = sql_update("wo_non_order_info_mst",'status_active*is_deleted','0*1',"id",$mst_id,1);
		$dtlsrID = sql_update("wo_non_order_info_dtls",'status_active*is_deleted','0*1',"mst_id",$mst_id,1);
		if($db_type==0 )
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "2**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);   
				echo "2**";
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;*/
	}
}

if($action=="wo_popup")
{
	extract($_REQUEST); 
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str); 
			if(str==1) // wo number
			{		
				document.getElementById('search_by_th_up').innerHTML="Enter WO Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:140px " class="text_boxes" id="txt_search_common"	value=""  />';		 
			}
			else if(str==2) // supplier
			{
				var supplier_name = '<option value="0">--- Select ---</option>';
				<? 
				$supplier_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=2 order by supplier_name",'id','supplier_name');
				foreach($supplier_arr as $key=>$val)
				{
					echo "supplier_name += '<option value=\"$key\">".($val)."</option>';";
				} 
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Supplier Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:150px " class="combo_boxes" id="txt_search_common">'+ supplier_name +'</select>';
			}	
		}
			
		function js_set_value(wo_number)
		{
			$("#hidden_wo_number").val(wo_number);	
			//$("#hidden_wo_number").val(wo_number);	
			parent.emailwindow.hide();
		}
			
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
             <thead>
                <th width="100">Item Category</th>
                <th width="130">Search By</th>
                <th width="150" align="center" id="search_by_th_up">Enter Order Number</th>
                <th width="130">Style Ref</th>
                <th width="130">Job No</th>
                <th width="130" colspan="2">WO Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </thead>
            <tr class="general">
                <td><? echo create_drop_down( "cboitem_category", 100, $item_category,"", 1, "-- Select --", $itemCategory, "",1); ?></td>
                <td>  
					<? 
						$searchby_arr=array(1=>"WO Number",2=>"Supplier");
						echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 0, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
                    ?>
                </td>
                <td id="search_by_td"><input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" /></td>
                <td id="search_by_style_td">
					<input type="text" style="width:130px" class="text_boxes"  name="txt_style_search" id="txt_style_search" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" placeholder="Write Full Style Ref No" />
				</td>
                <td id="search_by_job_td">
					<input type="text" style="width:130px" class="text_boxes"  name="txt_job_search" id="txt_job_search" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()"  placeholder="Write Full Job No"/>
				</td>
                <td>
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From">
                </td>
                <td>
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To">
                </td> 
                <td align="center">
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('txt_style_search').value+'_'+document.getElementById('txt_job_search').value, 'create_wo_search_list_view', 'search_div', 'sample_yarn_work_order_controller', 'setFilterGrid(\'list_view\',-1)');$('#selected_id').val('')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td  align="center" valign="middle" colspan="8">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
                </td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_wo_search_list_view")
{
 	extract($_REQUEST); 
	$ex_data = explode("_",$data);
	$itemCategory = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = $ex_data[2];
	$txt_date_from = $ex_data[3];
	$txt_date_to = $ex_data[4];
	$company = $ex_data[5];
 	$garments_nature = $ex_data[6];
 	$txt_style_search = $ex_data[7];
 	$txt_job_search = $ex_data[8];
				
	$sql_cond="";
	//if(trim($itemCategory)!="") $sql_cond .= " and item_category='$itemCategory'";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1)
			$sql_cond .= " and a.wo_number_prefix_num like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond .= " and a.supplier_id=trim('$txt_search_common')";		
 	}
	 //search by style
	if(trim($txt_style_search)!="")
	{
		$sql_cond .= " and b.style_no like '%".trim($txt_style_search)."%'";	
 	}
	 //search by job
	if(trim($txt_job_search)!="")
	{
		$sql_cond .= " and b.job_no like '%".trim($txt_job_search)."%'";	
 	}
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	if($txt_date_from!="" && $txt_date_to!="") 
	{
		if($db_type==2) 
		{
			$sql_cond .= " and a.wo_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
		}
		else
		{
			$sql_cond.= " and a.wo_date between '".change_date_format(trim($txt_date_from),"yyyy-mm-dd","-")."' and '".change_date_format(trim($txt_date_to),"yyyy-mm-dd","-")."'";	
		}
	}
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
		
 	$sql = "select a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.buyer_po, a.wo_date, a.supplier_id, a.attention, a.wo_basis_id, a.item_category, a.currency_id, a.delivery_date, a.source, a.pay_mode, b.job_no, b.style_no, b.requisition_no
	from wo_non_order_info_mst a, wo_non_order_info_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=284 $sql_cond order by a.id DESC";
	//echo $sql;//die;
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
 	
	$arr=array(0=>$company_arr,3=>$pay_mode,4=>$supplier_arr,5=>$wo_basis,6=>$source);
	echo  create_list_view("list_view", "Company, WO Number, WO Date, Pay Mode, Supplier, WO Basis, Source, Job No, Style Ref, Req No", "130,60,70,60,140,100,65,100,80,90","1000","250",0, $sql, "js_set_value", "wo_number,id", "", 1, "company_name,0,0,pay_mode,supplier_id,wo_basis_id,source,0,0,0", $arr , "company_name,wo_number_prefix_num,wo_date,pay_mode,supplier_id,wo_basis_id,source,job_no,style_no,requisition_no", "","",'0,0,3,0,0,0,0,0,0,0,0');
 	exit();	
}

if($action=="populate_data_from_search_popup")
{
	 $sql = "select id, wo_number, company_name, buyer_po, wo_date, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, payterm_id, is_approved, do_no, remarks, team_leader, dealing_marchant, tenor, pi_issue_to, ref_closing_status, ready_to_approved, delivery_place, delivery_mode, booking_type, inco_term from wo_non_order_info_mst where id='$data'";  
	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $resultRow)
	{
		echo "load_drop_down( 'requires/sample_yarn_work_order_controller', '".$resultRow[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";

		echo "$('#cbo_company_name').val('".$resultRow[csf("company_name")]."');\n";
		echo "$('#update_id').val('".$resultRow[csf("id")]."');\n";
		//echo "$('#cbo_item_category').val('".$resultRow[csf("item_category")]."');\n";
		echo "$('#cbo_currency').val('".$resultRow[csf("currency_id")]."');\n";
		echo "$('#cbo_supplier').val('".$resultRow[csf("supplier_id")]."');\n";
		echo "$('#txt_wo_date').val('".change_date_format($resultRow[csf("wo_date")])."');\n";
		echo "$('#cbo_pay_mode').val('".$resultRow[csf("pay_mode")]."');\n";
		echo "$('#cbo_wo_basis').val('".$resultRow[csf("wo_basis_id")]."');\n";
		echo "$('#cbo_wo_basis').attr('disabled',true);\n";
		echo "$('#txt_delivery_date').val('".change_date_format($resultRow[csf("delivery_date")])."');\n";
		echo "$('#cbo_booking_type').val(".$resultRow[csf("booking_type")].");\n";
		echo "$('#cbo_pi_issue_to').val('".$resultRow[csf("pi_issue_to")]."');\n";
		echo "$('#cbo_payterm_id').val('".$resultRow[csf("payterm_id")]."');\n";
		echo "$('#cbo_source').val('".$resultRow[csf("source")]."');\n";
		echo "$('#cbo_delivery_mode').val('".$resultRow[csf("delivery_mode")]."');\n";
		echo "$('#txt_do_no').val('".$resultRow[csf("do_no")]."');\n";
		echo "$('#txt_tenor').val('".$resultRow[csf("tenor")]."');\n";
		echo "$('#cbo_inco_term').val(".$resultRow[csf("inco_term")].");\n";
		echo "$('#txt_inco_term_place').val('".$resultRow[csf("delivery_place")]."');\n";
		echo "$('#txt_attention').val('".$resultRow[csf("attention")]."');\n";
		echo "$('#txt_remarks').val('".$resultRow[csf("remarks")]."');\n";
		echo "$('#cbo_team_leader').val('".$resultRow[csf("team_leader")]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$resultRow[csf("dealing_marchant")]."');\n";
		echo "$('#cbo_ready_to_approved').val('".$resultRow[csf("ready_to_approved")]."');\n";
		
		
		if($resultRow[csf("wo_basis_id")]==1)
		{
			if($db_type==0)
			{
				$sql_req=sql_select("select a.mst_id as wo_mst_id, group_concat(a.requisition_no) as req_all, group_concat(a.requisition_dtls_id) as requisition_dtls_id, group_concat(b.mst_id) as req_mst_id from  wo_non_order_info_dtls a, inv_purchase_requisition_dtls b where a.requisition_dtls_id=b.id and a.mst_id='".$resultRow[csf('id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by  a.mst_id");
				
				
			}
			else if($db_type==2)
			{
				$sql_req=sql_select("select a.mst_id as wo_mst_id, LISTAGG(CAST(a.requisition_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.requisition_no) as req_all, LISTAGG(CAST(a.requisition_dtls_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.requisition_dtls_id) as requisition_dtls_id, LISTAGG(CAST(b.mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.mst_id)  as req_mst_id from  wo_non_order_info_dtls a, inv_purchase_requisition_dtls b where a.requisition_dtls_id=b.id and a.mst_id='".$resultRow[csf('id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  a.mst_id");
				
			}
			
			echo "$('#txt_requisition').val('".$sql_req[0][csf("req_all")]."');\n";
			echo "$('#txt_req_id').val('".$sql_req[0][csf("req_mst_id")]."');\n";
			echo "$('#txt_req_dtls_id').val('".$sql_req[0][csf("requisition_dtls_id")]."');\n";
			echo "$('#txt_requisition').attr('disabled',false);\n";
			
		}
		
		
		if($resultRow[csf("is_approved")]==3){
			$is_approved=1;
		}else{
			$is_approved=$resultRow[csf("is_approved")];
		}
		
		echo "document.getElementById('is_approved').value = '".$is_approved."';\n";
		
		if($is_approved==1)
		{
			echo "$('#approved').text('Approved');\n"; 
		}
		else
		{
			echo "$('#approved').text('');\n";
		}
	}
	exit();
}

if ($action=="yarn_work_order_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);

	echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'',''); 
	//print_r ($data);
	/*if($db_type==0)
	{
		$sql=" select a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, $group_concat(b.po_breakdown_id) as po_breakdown, a.buyer_name, a.style, a.do_no, a.remarks  from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id= '$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
	}
	// LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY po_breakdown_id)
	else
	{
		$sql=" select a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, LISTAGG(CAST (b.po_breakdown_id as varchar(4000) ), ',')  WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_breakdown, a.buyer_name, a.style, a.do_no, a.remarks from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id= $data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.wo_number, a.supplier_id, a.wo_date, a.wo_basis_id, a.delivery_date, a.source, a.attention, a.terms_and_condition, a.buyer_name, a.style, a.do_no, a.remarks";
	}*/
	
	$user_level_library=return_library_array( "select id, user_level from user_passwd where id=$user_id", "id", "user_level"  );
	//if(($data[3]==1 || $data[3]==0) && $user_level_library[$user_id]==2)
	if(($data[3]==1 && $user_level_library[$user_id]==2) || ($data[3]==0))
	{
			
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$location=return_field_value("city","lib_company","id=$data[0]" );
		$address=return_field_value("address","lib_location","id=$data[0]");
		$lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
		$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
		$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
		$lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		
		
		$sql_data = sql_select("SELECT id, wo_number_prefix_num, wo_number, buyer_po, requisition_no, delivery_place, wo_date, currency_id, supplier_id, attention, buyer_name, style, wo_basis_id, item_category, delivery_date, source, pay_mode, remarks,do_no, insert_date  FROM  wo_non_order_info_mst WHERE id = $data[1]");
		foreach($sql_data as $row)
		{
			$work_order_no=$row[csf("wo_number")];
			$item_category_id=$row[csf("item_category")];
			$supplier_id=$row[csf("supplier_id")];
			$work_order_date=$row[csf("wo_date")];
			$currency_id=$row[csf("currency_id")];
			$buyer_name=$row[csf("buyer_name")];
			$style=$row[csf("style")];
			$wo_basis_id=$row[csf("wo_basis_id")];
			$pay_mode_id=$row[csf("pay_mode")];
			$source=$row[csf("source")];
			$delivery_date=$row[csf("delivery_date")];
			$attention=$row[csf("attention")];
			$requisition_no=$row[csf("requisition_no")];
			$delivery_place=$row[csf("delivery_place")];
			$do_no=$row[csf("do_no")];
			$remarks=$row[csf("remarks")];
			$insert_date=$row[csf("insert_date")];
		}
	
		$sql_job=sql_select("select a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sql_job as $row)
		{
			$buyer_job_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$buyer_job_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$buyer_job_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$buyer_job_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$buyer_job_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		}
	
	
		$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");
	
	   foreach($sql_supplier as $supplier_data) 
		{//contact_no 	
			$row_mst[csf('supplier_id')];
			
			if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
			if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
			if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
			if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
			if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
			if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
			if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
			//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
			$country = $supplier_data['country_id'];
			
			$supplier_address = $address_1;
			$supplier_country =$country;
			$supplier_phone =$contact_no;
			$supplier_email = $email;
		}
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		$varcode_booking_no=$work_order_no;
		?>
		<div style="width:930px;">
	    <table width="900" cellspacing="0" align="center">
	        <tr>
	        	<td rowspan="3" width="70"><img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
	            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	            <td rowspan="3" id="barcode_img_id"> </td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="2" align="center" style="font-size:14px"><? echo $location; ?></td>  
	        </tr>
	        <tr>
	            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2] ; ?></strong></td>
	        </tr>
	    </table>
	    <table width="900" cellspacing="0" align="center">
	         <tr>
	            <td width="300" align="left" style="font-size:16px;"><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
	            <td width="150" style="font-size:16px;"><strong>WO Number:</strong></td>
	            <td width="150" align="left" style="font-size:16px;"><b><? echo $work_order_no; ?></b></td>
	            <td><strong>Currency:</strong></td>
	            <td align="left"><? echo $currency[$currency_id]; ?></td>
	        </tr>
	        <tr>
	            <td rowspan="4" style="font-size:16px;"><? echo "<strong>".$supplier_name_library[$supplier_id]."</strong>"; echo "<br>"; echo $supplier_address;  echo  $lib_country_arr[$country]; echo "<br>"; echo "Cell :".$supplier_phone; echo "<br>"; echo "Email :".$supplier_email; echo "<br>"; echo "Remarks :".$remarks; ?></td>
	            <td width="150" align="left" ><strong>WO Date :</strong></td>
	            <td width="150" align="left"><? echo change_date_format($work_order_date); ?></td>
	            <td align="left" ><strong>WO Basis:</strong></td>
	            <td align="left" ><? echo $wo_basis[$wo_basis_id]; ?></td>
	        </tr>
	        <tr>
	            <td ><strong>Delivery Date :</strong></td>
	            <td><? echo change_date_format($delivery_date); ?></td>
	            <td align="left"><strong>D/O No.</strong></td>
	            <td align="left" ><? echo $do_no; ?></td>
	        </tr>
	        <tr>
	            <td align="right" colspan="5" >Print Date: <? $pc_day_time=explode(" ",$pc_date_time); echo change_date_format($pc_day_time[0]); echo " ".$pc_day_time[1]." ".$pc_day_time[2]; ?></td>
	        </tr>
	        <tr>
	            <td align="right" colspan="5" >Insert Date: <? $insert_day_time=explode(" ",$insert_date); echo change_date_format($insert_day_time[0]); echo " ".$insert_day_time[1]." ".$insert_day_time[2]; ?></td>
	        </tr>
	    </table>
	         <br>
	         <? 
			 	if($wo_basis_id==3)
				{
					$buy_job_sty="Buyer Job Style";
				}
				else if($wo_basis_id==2)
				{
					$buy_job_sty="Buyer Style";
				}
				else if($wo_basis_id==1)
				{
					$buy_job_sty="Buyer Job Style";
				}
			 ?>
	    <table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <? if($wo_basis_id!=1){ ?><th width="90">PO No</th><? } ?>
	            <th width="140"><? echo $buy_job_sty; ?></th>
	            <th width="70">Color</th>
	            <th width="60">Count</th>
	            <th width="250">Item Description</th>
	            <th width="50" >UOM</th>
	            <th width="70">Quantity </th>
	            <th width="60">Rate</th> 
	            <th >Amount</th>
	        </thead>
	        <tbody>
	<?
		$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
		
		$i=1; $buy_job_sty_val="";
		$mst_id=$dataArray[0][csf('id')];
	
		$sql_dtls="Select a.id, a.job_no,a.buyer_id,a.style_no, a.po_breakdown_id, a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.uom, a.supplier_order_quantity, a.rate, a.amount, b.currency_id from wo_non_order_info_dtls a, wo_non_order_info_mst b  where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
		 //echo $sql_dtls;
		$sql_result = sql_select($sql_dtls); 	
		foreach($sql_result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$order_quantity+=$row[csf('supplier_order_quantity')];
				$amount += $row[csf('amount')];
				if($wo_basis_id==2)
				{
					$buyer_name_val="";
					$buyer_id=explode(',',$buyer_name);
					foreach($buyer_id as $val)
					{
						if($buyer_name_val=="") $buyer_name_val=$buyer_arr[$val]; else $buyer_name_val.=', '.$buyer_arr[$val];
					}
					
					$buy_job_sty_val=$buyer_name_val."<br>".$style;
				}
				else if($wo_basis_id==3)
				{
					if($row[csf("po_breakdown_id")]!="" && $row[csf("po_breakdown_id")]!=0)
					{
						$buyer_name_val=$buyer_arr[$buyer_job_arr[$row[csf("po_breakdown_id")]]["buyer_name"]]."<br>".$buyer_job_arr[$row[csf("po_breakdown_id")]]["job_no"]."<br>".$buyer_job_arr[$row[csf("po_breakdown_id")]]["style_ref_no"]."<br>";
					}
					$buy_job_sty_val=$buyer_name_val;
				}
				else if($wo_basis_id==1)
				{
					if($row[csf("job_no")]!="")
					{ 
						$buyer_name_val=$buyer_arr[$row[csf("buyer_id")]]."<br>".$row[csf("job_no")]."<br>".$row[csf("style_no")]."<br>";
					}
					$buy_job_sty_val=$buyer_name_val;
				}
				
				
				
			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="center"><? echo $i; ?></td>
	                <?
					$feb_des='';
					if($row[csf("yarn_comp_type2nd")]==0)
					{
						$feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %, '.$yarn_type[$row[csf("yarn_type")]];
					}
					else if( $row[csf("yarn_comp_type2nd")]!=0)
					{
						$feb_des=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].' %,'.$composition[$row[csf("yarn_comp_type2nd")]].' '.$row[csf("yarn_comp_percent2nd")].' %, '.$yarn_type[$row[csf("yarn_type")]];
					}
					
					?>
	                <? if($wo_basis_id!=1){ ?><td><p><? echo $buyer_job_arr[$row[csf("po_breakdown_id")]]["po_number"]; ?>&nbsp;</p></td><? }?>
	                <td><p><? echo $buy_job_sty_val; ?>&nbsp;</p></td>
	                <td align="center"><p><? echo $color_arr[$row[csf("color_name")]]; ?></p></td>
	                <td align="center"><p><? echo $count_arr[$row[csf("yarn_count")]]; ?></p></td>
	                <td align="center"><p><? echo $feb_des; ?></p></td>
	                <td align="center"><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?></p></td>
	                <td align="right"><p><? echo number_format($row[csf("supplier_order_quantity")],2); ?></p></td>
	                <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
	                <td align="right"><p><? echo number_format($row[csf("amount")],2,".",""); $carrency_id=$row[csf('currency_id')]; if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}  ?></p></td>
				</tr>
				<? $i++; } ?>
	            
	        </tbody>
	        <tfoot>
	                <th colspan="<? echo ($wo_basis_id==1)?6:7;?>" align="right">Total </th>
	                <th align="right"><? echo number_format($order_quantity,0); ?></th>
	                <th>&nbsp;</th>
	                <th align="right"><? echo $word_amount=number_format($amount,2,".",""); ?></th>
	        </tfoot>
	    </table>
	    <table width="900" align="center">
	        <tr>
	        <td colspan="11">&nbsp;  </td>
	        </tr>
	        <tr>
	        <td colspan="11"> Amount in words:<? echo number_to_words($word_amount,$currency[$carrency_id],$paysa_sent); ?> </td>
	        </tr>
	        <tr>
	        <td colspan="11">&nbsp;   </td>
	        </tr>
	        <tr>
	        <td colspan="11">&nbsp;   </td>
	        </tr>
	    </table>
	    <br>
	    <table  width="900" class="rpt_table" border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
	                <thead>
	                <th width="3%">Sl</th><th width="97%">Terms & Condition/Note</th>
	                </thead>
	                <tbody>
					<?
	                //echo "select terms_and_condition from wo_non_order_info_mst where id='$data[1]'"; 
	                $data_array=sql_select("select terms_and_condition from wo_non_order_info_mst where id='$data[1]'");
	                //echo count($data_array);
	                if ( count($data_array)>0)
	                {
						$i=0;$k=0;
						foreach( $data_array as $row )
						{
							$term_id=explode(",",$row[csf('terms_and_condition')]);
							
							//print_r($term_id);
							$i++;
							foreach($term_id as $row_term)
							{
								$k++;
								echo "<tr> <td>
								$k</td><td> $lib_terms_condition[$row_term]</td></tr>";
							
							}
						}
	                }
	                else
	                {
						$i=0;
						$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
						//echo count($data_array)."jahid";
						foreach( $data_array as $row )
						{
							$i++;
							?>
							<tr>
	                            <td>
	                            <? echo $i;?>
	                            </td>
	                            <td>
	                            <? echo $row[csf('terms')]; ?>
	                            </td>
							</tr>
							<? 
						}
	                } 
	                ?>
	                </tbody>
	            </table>
	     <?
	        echo signature_table(42, $data[0], "900px");
	     ?>
		</div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	    <script>
	    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	    </script>
		<?
	    exit();	
	}
	else{}		
}

if ($action == "print_to_html_report4") 
{
    extract($_REQUEST);
    $data = explode('*', $data);
	$rate_amt=$data[5];
	//echo "test".$rate_amt;die;
    echo load_html_head_contents($data[2], "../../", 1, 1, $unicode, '', '');
    //print_r ($data);
	//$user_level_library=return_library_array( "select id, user_level from user_passwd where id=$user_id", "id", "user_level"  );
	//if(($data[4]==1 || $data[4]==0) && $user_level_library[$user_id]==2)
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
	$location = return_field_value("city", "lib_company", "id=$data[0]");
	$address = return_field_value("address", "lib_location", "id=$data[0]");

	$item_name_arr = return_library_array("select id,item_name from lib_item_group", "id", "item_name");
	$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier', 'id', 'supplier_name');
	$lib_terms_condition = return_library_array("select id, terms from lib_terms_condition", 'id', 'terms');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	
	if($db_type == 0)
	{
		$sql = "SELECT  a.id, a.wo_number_prefix_num, a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id, a.tenor,
		group_concat(b.requisition_no) as req_nos, group_concat(b.requisition_dtls_id) as req_dtls_ids, group_concat(b.job_no) as job_no,
		a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode, a.payterm_id, a.remarks, a.do_no, a.insert_date, a.pi_issue_to, a.inco_term, a.buyer_name, a.inserted_by 
		FROM wo_non_order_info_mst a, wo_non_order_info_dtls b 
		WHERE a.id=b.mst_id and a.id = $data[1] and b.mst_id=$data[1] and b.status_active = 1 and b.is_deleted = 0
		group by a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id, 
		a.remarks,a.do_no, a.insert_date,a.pi_issue_to,a.inco_term,
		a.id, a.wo_number_prefix_num,a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id,a.tenor,a.buyer_name, a.inserted_by ";
	}
	else if($db_type == 2)
	{
		$sql = "SELECT  a.id, a.wo_number_prefix_num, a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id, a.tenor, 
		LISTAGG (cast(b.requisition_no as varchar(4000)),',') within group ( order by b.requisition_no ) as req_nos, LISTAGG (cast(b.requisition_dtls_id as varchar(4000)),',') within group ( order by b.requisition_dtls_id ) as req_dtls_ids, LISTAGG (cast(b.job_no as varchar(4000)),',') within group ( order by b.job_no ) as job_no,
		a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode, a.payterm_id, a.remarks, a.do_no, a.insert_date, a.pi_issue_to, a.inco_term, a.buyer_name, a.inserted_by, a.delivery_place, a.delivery_mode
		FROM wo_non_order_info_mst a,wo_non_order_info_dtls b 
		WHERE a.id=b.mst_id and a.id = $data[1] and b.mst_id=$data[1] and b.status_active = 1 and b.is_deleted = 0
		group by a.supplier_id, a.attention, a.buyer_name, a.style, a.wo_basis_id, a.item_category, a.delivery_date, a.source, a.pay_mode,a.payterm_id, a.remarks,a.do_no, a.insert_date,a.pi_issue_to,a.inco_term, a.id, a.wo_number_prefix_num,a.wo_number, a.buyer_po, a.delivery_place, a.wo_date, a.currency_id,a.tenor,a.buyer_name, a.inserted_by, a.delivery_place, a.delivery_mode  ";
	}
	//echo $sql;
	$sql_data = sql_select($sql);
	
	$req_nos = ''; $booking_no = '';
	foreach ($sql_data as $row) 
	{
		$work_order_no = $row[csf("wo_number")];
		$item_category_id = $row[csf("item_category")];
		$supplier_id = $row[csf("supplier_id")];
		$work_order_date = $row[csf("wo_date")];
		$currency_id = $row[csf("currency_id")];
		$buyer_name = $row[csf("buyer_name")];
		$style = $row[csf("style")];
		$job_no = $row[csf("job_no")];
		$wo_basis_id = $row[csf("wo_basis_id")];
		$pay_mode_id = $row[csf("pay_mode")];
		$pay_term_id = $row[csf("payterm_id")];
		$source_str = $row[csf("source")];
		$delivery_date = $row[csf("delivery_date")];
		$attention = $row[csf("attention")];
		$inserted_by = $row[csf("inserted_by")];

		$delivery_place = $row[csf("delivery_place")];
		$do_no = $row[csf("do_no")];
		$remarks = $row[csf("remarks")];
		$insert_date = $row[csf("insert_date")];
		$inco_term = $row[csf("inco_term")];
		$pi_issue_to = $row[csf("pi_issue_to")];
		$req_nos = $row[csf("req_nos")];
		$req_dtls_ids = $row[csf("req_dtls_ids")];
		$tenor = $row[csf("tenor")];
		$buyer_name = $row[csf("buyer_name")];
		$delivery_place=$row[csf("delivery_place")];
		$delivery_mode=$row[csf("delivery_mode")];
		//
	}
	
	$req_nos= implode(",",array_unique(explode(",",$req_nos)));
   //echo $booking_no;
	$job_no=implode(",",array_unique(explode(",",chop($job_no,","))));
	if( $booking_no == "") $booking_no = "";
	$source_str =$source[$source_str];
	$paysa_sent ="";
	if ($currency_id == 1)  $paysa_sent = "Paisa"; else if ($currency_id == 2)  $paysa_sent = "CENTS";
	
	//$pay_mode = return_field_value("pa", "lib_company", "id=$data[0]");
	$job_style_arr=array();
	$sql_job = sql_select("select a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($sql_job as $row) {
		$buyer_job_arr[$row[csf("po_id")]]["po_id"] = $row[csf("po_id")];
		$buyer_job_arr[$row[csf("po_id")]]["po_number"] = $row[csf("po_number")];
		$buyer_job_arr[$row[csf("po_id")]]["buyer_name"] = $row[csf("buyer_name")];
		$buyer_job_arr[$row[csf("po_id")]]["job_no"] = $row[csf("job_no")];
		$buyer_job_arr[$row[csf("po_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
		$job_style_arr[$row[csf("job_no")]]['style']=$row[csf("style_ref_no")];
	}

	$sql_supplier = sql_select("SELECT id, supplier_name, contact_person, contact_no, country_id, web_site, email, address_1, address_2, address_3, address_4 FROM  lib_supplier WHERE id = $supplier_id");
	
	/* print_r("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id"); */
	foreach ($sql_supplier as $supplier_data) {//contact_no 	
		$row_mst[csf('supplier_id')];

		if ($supplier_data[csf('address_1')] != '') $address_1 = $supplier_data[csf('address_1')] . ',' . ' '; else $address_1 = '';
		if ($supplier_data[csf('address_2')] != '') $address_2 = $supplier_data[csf('address_2')] . ',' . ' '; else $address_2 = '';
		if ($supplier_data[csf('address_3')] != '') $address_3 = $supplier_data[csf('address_3')] . ',' . ' '; else $address_3 = '';
		if ($supplier_data[csf('address_4')] != '') $address_4 = $supplier_data[csf('address_4')] . ',' . ' '; else $address_4 = '';
		if ($supplier_data[csf('contact_no')] != '') $contact_no = $supplier_data[csf('contact_no')] . ',' . ' '; else $contact_no = '';
		if ($supplier_data[csf('contact_person')] != '') $contact_person = $supplier_data[csf('contact_person')] . ',' . ' '; else $contact_person = '';
		
		if ($supplier_data[csf('web_site')] != '') $web_site = $supplier_data[csf('web_site')] . ',' . ' '; else $web_site = '';
		if ($supplier_data[csf('supplier_name')] != '') $supplier_name = $supplier_data[csf('supplier_name')] . ',' . ' '; else $supplier_name = '';
		if ($supplier_data[csf('email')] != '') $email = $supplier_data[csf('email')] . ',' . ' '; else $email = '';
		$country = $supplier_data['country_id'];
		$supplier_name = $supplier_name;
		$supplier_address = $address_1;
		$supplier_country = $country;
		$supplier_phone = $contact_no;
		$supplier_email = $email;
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	$varcode_booking_no = $work_order_no;
	    ?>
        <div style="width:960px;">
            <table width="950" cellspacing="0" align="center">
                <tr>
                    <td rowspan="3" width="70"><img src="../../../<? echo $image_location; ?>" height="70" width="200"></td>
                    <td colspan="2" style="font-size:18px;" align="center"><strong><? echo $company_library[$data[0]]; ?></strong></td>
                    <td rowspan="3" id="barcode_img_id"> </td>
                </tr>
                <tr class="form_caption">
                    <td colspan="2" align="center" style="font-size:14px"><? echo show_company($data[0],'',''); ?></td>  
                </tr>
                <tr>
                    <td colspan="2" align="center" style="font-size:16px"><strong><? echo $data[2]; ?></strong></td>
                </tr>
            </table>
            <table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table" >
                <tr>
                    <td rowspan="2" colspan="2" width="230" valign="top"><strong>SUPPLIER</strong>:&nbsp;<? echo $supplier_name; ?></td>
                    <td width="150">WO DATE:</td><td width="65" style="font-size: 13px;"><? echo change_date_format($work_order_date); ?></td>
                    <td width="85" style="font-size: 14px;"><strong>WO Number:</strong></td><td width="90" style="font-size: 13px;"><? echo $work_order_no; ?></td>
                </tr>
                <tr>
                	<td><strong>STYLE NO:</strong></td><td colspan="3"><? echo $job_style_arr[$job_no]['style']; ?></td>
                </tr>
                <tr>
                	<td rowspan="5" colspan="2" valign="top"><strong>CONTACT PERSON</strong>:&nbsp;<? echo $contact_person.', '.$contact_no; ?></td>
                	<td><strong>JOB NO:</strong></td><td colspan="3"><? echo $job_no; ?></td>
                </tr> 
                <tr>
                	<td>REQUISITION. NO</td><td colspan="3"><? echo $req_nos; ?></td>
                </tr>
                <tr>
                	<td>TERMS OF PAYMENT:</td><td colspan="3"><? echo $pay_term[$pay_term_id]; ?></td>
                </tr>
                <tr>
                	<td>INCO TERM:</td><td colspan="3"><? echo $incoterm[$inco_term]; ?></td>
                </tr>   
                <tr>
                	<td>INCO TERM PLACE:</td><td colspan="3"><? echo $delivery_place; ?>&nbsp;</td>
                </tr>
                <tr>
                	<td colspan="2">&nbsp;</td><td>PI ISSUE TO:</td><td colspan="3"><? echo $company_library[$pi_issue_to]; ?></td>
                </tr>
                <tr>
                	<td rowspan="3" colspan="2" valign="top">CONSIGNEE:&nbsp;</td><td>DELIVERY MODE:</td><td colspan="3"><? echo $shipment_mode[$delivery_mode]; ?>&nbsp;</td>
                </tr>
                <tr>
                	<td>DELIVERY DATE:</td><td colspan="3"><? echo change_date_format($delivery_date); ?></td>
                </tr>
                <tr>
                	<td>CURRENCY:</td><td colspan="3"><? echo $currency[$currency_id]; ?></td>
                </tr>
                <tr>
                	<td colspan="2">ATTENTION:&nbsp;<? echo $attention; ?></td><td>CREATED BY:</td><td colspan="3"><? echo $user_arr[$inserted_by]; ?></td>
                </tr>
            </table>
            <table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table" >
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="100">COLOR NAME</th>
                    <th width="100">SUPPLIER COLOR REF</th>
                    <th width="80">APPR. OPTION</th>
                    <th width="200">YARN COMPOSITION</th>
                    <th width="90">COUNT</th>
                    <th width="<? if($rate_amt) echo "100";else echo ""; ?>">TOTAL QTY(LBS) </th>
                    <?
					if($rate_amt)
					{
						?>
                        <th width="70">UNIT PRICE /LBS</th> 
                    	<th>TOTAL AMOUNT</th>
                        <?
					}
					?>
                    
                </thead>
                <tbody>
                <?
				 $sql_dtls = "Select a.color_name, a.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.supplier_order_quantity as qty, a.rate, a.amount from wo_non_order_info_dtls a, wo_non_order_info_mst b  where a.mst_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
				$sql_result = sql_select($sql_dtls); $i=1;
				foreach ($sql_result as $row) 
				{
					$yarn_des = '';
					if ($row[csf("yarn_comp_type2nd")] == 0) {
						$yarn_des = $composition[$row[csf("yarn_comp_type1st")]] . ' ' . $row[csf("yarn_comp_percent1st")] . ' %, ' . $yarn_type[$row[csf("yarn_type")]];
					} else if ($row[csf("yarn_comp_type2nd")] != 0) {
						$yarn_des = $composition[$row[csf("yarn_comp_type1st")]] . ' ' . $row[csf("yarn_comp_percent1st")] . ' %,' . $composition[$row[csf("yarn_comp_type2nd")]] . ' ' . $row[csf("yarn_comp_percent2nd")] . ' %, ' . $yarn_type[$row[csf("yarn_type")]];
					}
					
					$color_str=explode('[',$color_arr[$row[csf("color_name")]]);
					$color_name=$color_str[0];
					$color_ref_option=explode(']',$color_str[1]);
					$color_ref=$color_ref_option[0];
					$color_app_option=$color_ref_option[1];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td style='word-break:break-all'><? echo $color_name; ?>&nbsp;</td>
                        <td style='word-break:break-all'><? echo $color_ref; ?>&nbsp;</td>
                        <td><? echo $color_app_option; ?>&nbsp;</td>
                        <td style='word-break:break-all'><? echo $yarn_des; ?>&nbsp;</td>
                        <td style='word-break:break-all'><? echo $count_arr[$row[csf("yarn_count")]]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf("qty")]); ?></td>
                        <?
						if($rate_amt)
						{
							?>
                            <td align="right"><? echo number_format($row[csf("amount")] / $row[csf("qty")],2); ?></td>
                            <td align="right"><? echo number_format($row[csf("amount")], 2); ?></td>
                            <?
						}
						?>
					</tr>
					<?
					$i++;
					$gqty+=$row[csf("qty")];
					$gamount+=$row[csf("amount")];
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                        <th colspan="6" align="right">TOTAL :</th>
                        <th align="right"><? echo number_format($gqty); ?></th>
                        <?
						if($rate_amt)
						{
							?>
                            <th>&nbsp;</th>
                            <th align="right"><? echo $word_amount = number_format($gamount, 2); ?></th>
                            <?
						}
						?>
                    </tr>
                    <tr>
                        <th colspan="9" align="left">
                        In words: <span style="font-weight:normal !important;"><? echo number_to_words($word_amount,$currency[$currency_id],$paysa_sent); ?></span></th>
                    </tr>
                </tfoot>
            </table>
            <br>
            <? echo get_spacial_instruction($data[1],"950px", 284); ?>
            <!--<table width="950" class="rpt_table" cellpadding="0" cellspacing="0" align="center">
                <thead>
                	<th width="100%" align="left"><u>Terms & Condition/Note</u></th>
                </thead>
                <tbody>
				<?
                /*$data_array = sql_select("select terms_and_condition from wo_non_order_info_mst where id='$data[1]'");
                //echo count($data_array);
                if (count($data_array) > 0) {
                    $i = 0; $k = 0;
                    foreach ($data_array as $row) {
                        $term_id = explode(",", $row[csf('terms_and_condition')]);
                        //print_r($term_id);
                        $i++;
                        foreach ($term_id as $row_term) {
                            $k++;
                            echo "<tr><td>".$k . $lib_terms_condition[$row_term]."</td></tr>";
                        }
                    }
                } else {
                    $i = 0;
                    $data_array = sql_select("select id, terms from  lib_terms_condition"); // quotation_id='$data'
                    //echo count($data_array)."jahid";
                    foreach ($data_array as $row) {
                        $i++;
                        ?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td><? echo $row[csf('terms')]; ?></td>
                        </tr>
                        <?
                    }
                }*/
			?>
            </tbody>
        </table>-->
	                            
	 <!----------------------------------------------------------------->  
		<?
		$user_sql = "select a.id,a.user_full_name,b.custom_designation from user_passwd a,lib_designation b where a.valid=1 and b.id=a.designation";
		$user_data_array=sql_select($user_sql);
		foreach($user_data_array as $row){
			$user_arr[$row[csf(id)]]=$row[csf(user_full_name)].'<br><span style="font-size:12px;">('.$row[csf(custom_designation)].')</span>';
		}
		
		
		
		$sql="select updated_by,inserted_by,company_name,is_approved  FROM wo_non_order_info_mst where id=$data[1]";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('updated_by')]!=0){$PreparedBy = $row[csf('updated_by')];}
			else{$PreparedBy = $row[csf('inserted_by')];}
			$company_name=$row[csf('company_name')];//approved_by
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
			//$is_approved=$is_approved;//approved_by
		}
		
	   //$last_authority = return_field_value("user_id", "electronic_approval_setup", " page_id=412 and entry_form=2 and company_id=$company_name order by sequence_no desc");
		
		$sql="select approved_by  FROM approval_history where mst_id=$data[1] and entry_form=2 and un_approved_by=0";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$last_approved_by = $row[csf(approved_by)];
		}
		
		if($is_approved==1){ echo '<style > body{ background-image: url("../../../img/approved.gif"); } </style>';} 
		else{ echo '<style > body{ background-image: url("../../../img/draft.gif"); } </style>';}
	?>
	    
	<!----------------------------------------------------------------->  
    <br><br>
    <table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="text-align:center;" rules="all" width="880">	
        <tr>
            <td style="border:none; line-height:13px;" align="left"><? echo $user_arr[$PreparedBy]; ?></td>
            <td style="border:none; line-height:13px;" align="right"><? echo $user_arr[$last_approved_by]; ?></td>
        </tr>
        <tr style="alignment-baseline:baseline;">
            <td width="" style="text-decoration:overline; border:none" align="left">Prepared By</td>
            <td width="" style="text-decoration:overline; border:none" align="right">Authorized By</td>
        </tr>
    </table>
    <style>
		p.breakAfter { page-break-after:always; }
		
		.main_div { border: 1px solid #000; width: 950px; margin-left: 0px; margin-top: 0px; }
		.div1 { border-bottom: 1px solid #000; min-height: 100px; } 
		.div1_1{float: left; margin-top: 5px;} 
		.div1_2{text-align: center;} 
		.head_p1{ font-size: 18px;font-weight: bold; }
		.head_p2{font-weight: bold;font-size: 14px;}
		.head_p3{ font-size: 16px;font-weight: bold;}
		.div2{ margin-top: 20px; margin-left: 5px;} 
		.div2_p1 {font-size: 18px; text-decoration: underline; font-weight: bold; margin-bottom: 10px;}
		.parag{font-size: 15px; margin-bottom: 5px;}
		.div2_p2 { margin-top: 20px; font-size: 15px; } 
		.div2_p3 {font-size: 15px; margin-top: 15px; margin-bottom: 20px; }
		.div3{margin-left:5px;} 
		.div3_p1{font-size: 18px; text-decoration: underline; font-weight: bold; margin-bottom: 10px;} 
		.div3_p2{} 
		.div3_p3 {font-size: 15px; margin-top: 20px; margin-bottom: 25px; }
		/*.div4{} 
		table, td{border: 1px dotted black; border-left: none; border-collapse: collapse; text-align: left; font-size: 15px; padding: 5px; }*/
		.div5{border-top: 1px solid #000; min-height: 50px;} 
		.div5_1 {min-height: 50px; width: 50%; border-right: 1px solid #000; float: left; }
		.div5_p1{font-size: 15px; margin-left: 5px; padding-top: 13px;}
		.div5_2{min-height: 50px; width: 49%;float: right;padding-top: 13px;  margin-right: 5px; } 
		.div5_p2{font-size: 15px;text-align: right;} 
    </style>
    <p class="breakAfter">
    
    <div class="main_div">
        <div class="div1">
            <div class="div1_1">
                <img src="../../../<? echo $image_location; ?>" height="70" width="200">
            </div>
            <div class="div1_2">
                <p class="head_p1">SONIA & SWEATERS LTD</p>
                <p class="head_p2"><? echo show_company($data[0],'',''); ?></p>
                <p class="head_p3">MINIMUM YARN SPECIFICATIONS</p>
            </div>
        </div>
        <div class="div2">
            <p class="div2_p1">General Specifications:</p>
            <p class="parag">Free from contaminations/picot/knots/neps/snarls/kemps.Yarns to be regular in thickness ( Uster U value < 8% )</p>
            <p class="parag">Good quality of splices/regular splicing point.</p>
            <p class="parag">All Yarn Should be double waxed and Suitable for Jacquard Knitting</p>
            <p class="parag">Woollen Yarns should be capable of withstanding machine wash process ( DCCA / Simpl-X processes )</p>
            <p class="parag">When specified as suitable for machine wash treated finish.</p>
            <p class="parag">Yarns ordered for stripes /multicoloured styles, fastness to be 5 (min 4 - 5 accepted).</p>

            <p class="div2_p2">Yarn Count: +/- 5% ( CV% :< 2 )</p>
            <p class="parag">Co-efficient of Friction : < 0.20</p>
            <p class="parag">Yarn Tenacity (Woollen ) : > 3 g/Tex</p>
            <p class="parag">Count Strength Product: (CSP >2000)</p>
            <p class="div2_p3">Twist : +/- 5% ( CV% : < 2 )</p>
        </div>
        <div class="div3">
            <p class="div3_p1">Colour Control (Light Sources: D65/TL 84/CWF)</p>

            <p class="parag">Instrumental colour measurement (spectrophotometer) Delta E: <1.0.</p>
            <p class="parag">Final decision will be visual assessment ( colours should be free from metamerism )</p>
            <p class="parag">Free from shading / undyed yarn places / streakiness ( both cone to cone & within same cone ).</p>

            <p class="div3_p3">Free from prohibited amines ( Azo - free ) & free from Allergenic Disperse Dyes. All chemicals used should conform to EU REACH Regulations.</p>
        </div>

        <div class="div4">
            <table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table" >
                <tr>
                    <td width="30%" rowspan="3">Colour Fastness to Washing</td>
                    <td width="35%">ISO 105 CO6</td>
                    <td width="10%">STAINING</td>
                    <td width="25%">4 (On multifibre stripe type DW)</td>
                </tr>
                <tr>
                    <td>A2S (For Normal Wash)</td>
                    <td>C CHANGE</td>
                    <td>4 - 5</td>
                </tr>
                <tr>
                    <td>B2S (For Machine Wash)</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td rowspan="2">Colour Fastness to Water</td>
                    <td rowspan="2">ISO 105 E01</td>
                    <td>STAINING</td>
                    <td>4 (On multifibre stripe type DW)</td>
                </tr>
                <tr>
                    <td>C CHANGE</td>
                    <td>4 - 5</td>
                </tr>

                <tr>
                    <td rowspan="2">Colour Fastness to Perspiration</td>
                    <td rowspan="2">ISO 105 E04</td>
                    <td>STAINING</td>
                    <td>4 (On multifibre stripe type DW)</td>
                </tr>
                <tr>
                    <td>C CHANGE</td>
                    <td>4 - 5</td>
                </tr>

                <tr>
                    <td rowspan="2">Colour Fastness to Rubbing</td>
                    <td rowspan="2">ISO 105 X12</td>
                    <td>DRY RUB</td>
                    <td>4</td>
                </tr>
                <tr>
                    <td>WET RUB</td>
                    <td>3 - 4</td>
                </tr>
                <tr>
                    <td>Colour Fastness to Light</td>
                    <td>ISO 105 B02</td>
                    <td>BWS</td>
                    <td>Better Than 4</td>
                </tr>
                <tr>
                    <td>Pilling Resistance</td>
                    <td>ISO 12945 - 1 ( Pilling box )</td>
                    <td colspan="2">Cotton : Grade 4 after 4 hrs<br>Woollen : Grade 3 - 4 after 2 hrs</td>
                </tr>

                <tr>
                    <td rowspan="2">Wash Stability</td>
                    <td rowspan="2">ISO 6330</td>
                    <td colspan="2">Cotton: ±5% (After 3 cotton wash/drying cycles )</td>
                </tr>
                <tr>
                    <td colspan="2">Woollen : ± 5% (after 1 X 7A + 1 X 7A wash cycles) for normal wash / (after 1 X 7A + 2 X 5A wash cycles) for m/c wash / 1x7A T/D + 5( 5A +T/D ) for TEC</td>
                </tr>
            </table>
        </div>
        <p class="parag" style="margin-left: 5px; margin-top: 5px;">pH : 6.0 - 7.5</p>
        <div class="div5">
            <div class="div5_1">
                <p class="div5_p1">Compiled By: Husain Khales Rahman</p>
            </div>
            <div class="div5_2">
                <p class="div5_p2">Approved By: Mahabubur Rahman</p>
            </div>                       
        </div>     
    </div>
    
    </p>

    
    </div>
        <script type="text/javascript" src="../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
        <script>fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');</script>
    <?
	exit();
}


?>