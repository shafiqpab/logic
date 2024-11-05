<?
/*-------------------------------------------- Comments
Version (MySql)          :  
Version (Oracle)         :  
Converted by             :  
Purpose			         :  This form will create 
Functionality	         :
JS Functions	         :
Created by		         :  
Creation date 	         :  
Requirment Client        :  
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.trims.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="load_drop_down_buyer_order")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_id", 130, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}exit();
}

function signature_table1($report_id, $company, $width, $template_id="", $padding_top = 70,$prepared_by='') {
	if ($template_id != '') {
		$template_id = " and template_id=$template_id ";
	}
	$sql = sql_select("select designation,name,activities,prepared_by from variable_settings_signature where report_id=$report_id and company_id=$company   and status_active=1  $template_id order by sequence_no");


	if($sql[0][csf("prepared_by")]==1){
		list($prepared_by,$activities)=explode('**',$prepared_by);
		$sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME => $prepared_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
		$sql=$sql_2+$sql;
	}

	$count = count($sql);
	$td_width = floor($width / $count);
	$standard_width = $count * 150;
	if ($standard_width > $width) {
		$td_width = 150;
	}
	$no_coloumn_per_tr = floor($width / $td_width);
	$i = 1;
	if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
	$signature_data = '<table id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr><tr>';
	foreach ($sql as $row) {
		$signature_data .= '<td width="' . $td_width . '" align="center" valign="top">
		<strong>' . $row[csf("activities")] . '</strong><br>
		<strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
		if ($i % $no_coloumn_per_tr == 0) {
			$signature_data .= '</tr><tr><td width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
		}
		$i++;
	}
	$signature_data .= '</tr></table>';
	return $signature_data; 
	exit();
}

//---------------------------------------------------- Start---------------------------------------------------------------------------
function load_drop_down_supplier($data){
	$data=explode("_",$data);
	$pay_mode_id=$data[0];
	$tag_buyer_id=$data[1];
	$tag_comp_id=$data[2];
	if($pay_mode_id==5 || $pay_mode_id==3){
	   echo create_drop_down( "cbo_supplier_name", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/revised_booking_report_controller');",0,"" );
	}
	else
	{
		$tag_buyer=return_field_value("tag_buyer as tag_buyer", "lib_supplier_tag_buyer", "tag_buyer=$tag_buyer_id","tag_buyer");
		if($tag_buyer!='')
		{
			$tag_by_buyer=sql_select("SELECT supplier_id from lib_supplier_tag_buyer where tag_buyer = $tag_buyer_id group by supplier_id");
			foreach ($tag_by_buyer as $row) {
				$supplier_arr2[$row[csf('supplier_id')]] = $row[csf('supplier_id')];
			}
			$supplier_string2=implode(',', $supplier_arr2);
			$tag_another_buyer=sql_select("SELECT supplier_id from lib_supplier_tag_buyer where tag_buyer != $tag_buyer_id and supplier_id not in ($supplier_string2) group by supplier_id");
			foreach ($tag_another_buyer as $row) {
				$supplier_arr[$row[csf('supplier_id')]] = $row[csf('supplier_id')];
			}
			//$supplier_string=implode(',', $supplier_arr);
			function where_con_not_in_using_array($arrayData,$dataType=0,$table_coloum){
				$chunk_list_arr=array_chunk($arrayData,999);
				$p=1;
				foreach($chunk_list_arr as $process_arr)
				{
					if($dataType==0){
						if($p==1){$sql .=" and (".$table_coloum." not in(".implode(',',$process_arr).")"; }
						else {$sql .=" or ".$table_coloum." not in(".implode(',',$process_arr).")";}
					}
					else{
						if($p==1){$sql .=" and (".$table_coloum." not in('".implode("','",$process_arr)."')"; }
						else {$sql .=" or ".$table_coloum." not in('".implode("','",$process_arr)."')";}
					}
					$p++;
				}
				
				$sql.=") ";
				return $sql;
			}
			$supplier_string='';
			if(count($supplier_arr))
			{
				$supplier_string=where_con_not_in_using_array($supplier_arr,0,"c.id");
			}
			$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type in (4,5) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0 $supplier_string group by c.id, c.supplier_name order by c.supplier_name";
			//echo $tag_buy_supp; die;
			//$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c,lib_supplier_tag_buyer d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and d.supplier_id=c.id and d.supplier_id=a.supplier_id  and d.supplier_id=b.supplier_id and b.party_type  in (4,5) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0  and d.tag_buyer=$tag_buyer group by c.id, c.supplier_name order by c.supplier_name";
		}
		else
		{
			$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type in (4,5) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name";
		}
		//echo $tag_buy_supp;
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 150, $tag_buy_supp,"id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/revised_booking_report_controller');","");
	}
	return $cbo_supplier_name;
	exit();
}

if ($action=="send_mail_report_setting_first_select"){
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=26 and is_deleted=0 and status_active=1");
	echo $print_report_format;
	exit();
}

if ($action=="load_drop_down_buyer"){

	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_buyer_pop"){
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_supplier"){
	echo $action($data);
	exit();
}

if($action=="load_drop_down_attention"){
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();
}
if ($action=="system_number_search_popup")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	$data=extract($_REQUEST);
   // echo "<pre>";	print_r($_REQUEST);//action and company_id value

	?>
	<script>
		function js_set_value(id){
			document.getElementById('id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
    <body>
        <div align="center" >
        <input type="hidden" id="id" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="700" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="11" align="center"><?=create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                    </tr>
                    <tr>
                        <th width="160" class="must_entry_caption">Company Name</th>
                        <th width="160" class="must_entry_caption">Buyer Name</th>
                        <th width="100">System No</th>
                        <th width="100">Part No.</th>
                        <th width="140" colspan="2"> Date Range</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
						<?=create_drop_down( "cbo_company_mst", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$cbo_company_name), "load_drop_down( 'revised_booking_report_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td' );",1); ?>
					</td>
                    <td id="buyer_td">
						<?=create_drop_down( "cbo_buyer_name", 160, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_company_name), "",0,"" ); 
						?>
					</td>
                    <td><input name="txt_system_no_prifix" id="txt_system_no_prifix" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_part_no" id="txt_part_no" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date"></td>
                    <td align="center">
                    	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_system_no_prifix').value+'_'+document.getElementById('txt_part_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_year_selection').value, 'create_systemNo_search_list_view', 'search_div', 'revised_booking_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:70px;" />   
                    </td>
                </tr>
                <tr class="general">
                    <td align="center" valign="middle" colspan="11" >
						<?=load_month_buttons(1); ?>
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">
		$("#cbo_company_mst").val(<? echo $company_id?>);
		load_drop_down( 'revised_booking_report_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer_pop', 'buyer_td' );
	</script>
    </html>
    <?
    exit();
}

if ($action=="create_systemNo_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);
	$company_name=$data[0];
	$buyer_name=$data[1];
	$system_no_prifix=$data[2];
	$part_no=$data[3];
	$date_from=$data[4];
	$date_to=$data[5];
	$cbo_search_category=$data[6];
	$year_id=$data[7];

	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; disconnect($con); die; }
	if ($data[1]!=0) $buyercon=" and a.buyer_name='$data[1]'";else echo $buyercon="";
	if ($data[2]!="") $system_cond=" and a.system_no_prefix_num='$data[2]'"; else $system_no_prifix_con ="";
	if ($year_id>0)  $year_id_cond="and to_char(a.insert_date,'YYYY')=$year_id";else $year_id_cond="";
	
	if($db_type==2){
		$system_year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
		if ($data[4]!="" &&  $data[5]!="") $revised_date_con  = "and a.insert_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $revised_date_con ="";
	}

	if($data[6]==1){
		if (str_replace("'","",$data[6])!="") $system_cond=" and a.system_no_prefix_num ='$data[2]'   "; else $system_cond="";
		if (str_replace("'","",$data[3])!="") $part_no_cond=" and a.part_no = '$data[3]'  "; //else  $part_no="";
	}
	if($data[6]==2){
		if (str_replace("'","",$data[6])!="") $system_cond=" and a.system_no_prefix_num like '$data[2]%'  $year_id_cond  "; else $system_cond="";
		if (str_replace("'","",$data[3])!="") $part_no_cond=" and a.part_no like '$data[3]%'  "; //else  $part_no_cond="";
	}
	if($data[6]==3){
		if (str_replace("'","",$data[6])!="") $system_cond=" and a.system_no_prefix_num like '%$data[2]'  $year_id_cond  "; else $system_cond="";
		if (str_replace("'","",$data[3])!="") $part_no_cond=" and a.part_no like '$data[3]%'  "; //else  $part_no_cond="";
	}
	if($data[6]==4 || $data[6]==0){
		if (str_replace("'","",$data[6])!="") $system_cond=" and a.system_no_prefix_num like '%$data[2]%'  $year_id_cond  "; else $system_cond="";
		if (str_replace("'","",$data[3])!="") $part_no_cond=" and a.part_no like '$data[3]%'  "; //else  $part_no_cond="";
	}
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$sql="SELECT a.id,a.buyer_name, a.company_name, a.fab_delivery_date, a.order_no, a.part_no, a.remarks, a.revised_date, a.revised_no, a.revised_reason,a.system_no,a.system_no_prefix_num from wo_booking_revised_mst a where  a.status_active =1 and a.is_deleted=0 $company $revised_date_con $system_cond $buyercon  $year_id_cond $part_no_cond order by a.system_no DESC";
	

	$arr=array (1=>$comp);
	echo  create_list_view("list_view", "System Number,Company,Revised Date, Part No", "120,150,150,120","600","300",0, $sql , "js_set_value", "id", "", 1, "0,company_name,0,0", $arr , "system_no,company_name,revised_date,part_no", '','','0,0,0,0','','');
	exit();
}

if ($action=="populate_data_from_search_popup_booking")
{
	 $sql="SELECT a.id,a.buyer_name, a.company_name, a.fab_delivery_date, a.order_no,a.po_break_down_id, a.part_no, a.remarks, a.revised_date, a.revised_no, a.revised_reason,a.system_no,a.system_no_prefix_num from wo_booking_revised_mst a where a.id='$data'and a.status_active =1 and a.is_deleted=0"; 


	

	$data_array=sql_select($sql);
	foreach ($data_array as $row){
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_system_no').value = '".$row[csf("system_no")]."';\n";
		echo "document.getElementById('txt_select_item').value = '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_selected_po').value = '".$row[csf("po_break_down_id")]."';\n";
		echo "document.getElementById('text_revised_no').value = '".$row[csf("revised_no")]."';\n";
		echo "document.getElementById('txt_part_no').value = '".$row[csf("part_no")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("fab_delivery_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_revised_date').value = '".change_date_format($row[csf("revised_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_revision_reason').value = '".$row[csf("revised_reason")]."';\n";
		
		echo " $('#text_revised_no').attr('disabled',true);\n";
		//echo "fnc_generate_booking();\n";
	}
	exit();
}
if ($action=="file_popup")
{

  	echo load_html_head_contents("Popup Info File","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $lien_bank;die;
	?>
	<script>
		function js_set_value(str)
		{
			$("#hide_file_no").val(str);
			parent.emailwindow.hide();
		}
		function set_caption(id)
		{
		if(id==1)  document.getElementById('search_by_td_up').innerHTML='Enter File No';
		if(id==2)  document.getElementById('search_by_td_up').innerHTML='Enter Buyer Name';
		if(id==3)  document.getElementById('search_by_td_up').innerHTML='Enter Lein Bank';
	    if(id==4)  document.getElementById('search_by_td_up').innerHTML='Enter SC/LC';
		}
	</script>
	</head>
	<body>
	    <div style="width:530px">
		    <form name="search_order_frm"  id="search_order_frm">
			    <fieldset style="width:530px">
			        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
			            <thead>
			            	<th>Year</th>
			                <th>Search By</th>
			                <th id="search_by_td_up">Enter File No</th>
			                <th>
				                <input type="hidden" name="txt_company_id" id="txt_company_id" value="<?  echo $company_id; ?>"/>
				               
				                <input type="hidden" name="txt_sclc_id" id="txt_sclc_id" value="<? //echo ?>"/>
				                <input type="hidden" name="txt_selected_file" id="txt_selected_file" value=""/>
			                </th>
			            </thead>
			            <tbody>
			                <tr class="general">
			                	<td>
			                    <?
								$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$company_id' and status_active=1 and is_deleted=0");
								foreach($sql as $row)
								{
									$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
								}
								echo create_drop_down( "cbo_year", 100,$lc_sc_year,"", 1, "-- Select --",$cbo_year);
								?>
			                    </td>
			                    <td>
			                    <?
								$sarch_by_arr=array(1=>"File No",2=>"Buyer",3=>"Lien Bank",4=>"SC/LC");
								echo create_drop_down( "cbo_search_by", 130,$sarch_by_arr,"", 0, "-- Select Search --", 1,"load_drop_down( 'pi_controller_urmi',document.getElementById('txt_company_id').value+'_'+this.value, 'load_drop_down_search', 'search_by_td' );set_caption(this.value)");
								?>
			                    </td>
			                    <td align="center" id="search_by_td">
			                    	<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />
			                    </td>
			                    <td>
			                    	<input type="button" name="show" id="show" onClick="show_list_view(document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_year').value+'_'+'<? echo $is_lc_sc; ?>'+'_'+'<? echo $lc_sc_id; ?>','search_file_info','search_div_file','revised_booking_report_controller','setFilterGrid(\'list_view\',-1)')" class="formbutton" style="width:100px;" value="Show" />
			                    </td>
			                </tr>
			            </tbody>
			        </table>
			        <table width="100%">
			            <tr>
			                <td>
			                	<div style="width:560px; margin-top:5px" id="search_div_file" align="left"></div>
			                </td>
			            </tr>
			        </table>
			    </fieldset>
		    </form>
	    </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="search_file_info")
{
	$ex_data = explode("_",$data);
	// print_r($ex_data);die;
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company_id = $ex_data[2];
	//$buyer_id = $ex_data[3];
	//$lien_bank_id = $ex_data[4];
	$cbo_year = $ex_data[3];
	$is_lc_sc = str_replace("'","",$ex_data[4]);
	$lc_sc_id = str_replace("'","",$ex_data[5]);
	//echo $cbo_year; die;
	//if($buyer_id!=0) $buy_query="and buyer_name='$buyer_id'"; else  $buy_query="";
	//if($lien_bank_id!=0) $lien_bank_id="and lien_bank='$lien_bank_id'"; else  $lien_bank_id="";
	if($cbo_year!=0)
	{
		$year_cond_sc="and sc_year='$cbo_year'";
		$year_cond_lc="and lc_year='$cbo_year'";
	}
	else
	{
		$year_cond_sc="";
		$year_cond_lc="";
	}
	//$year_cond_sc="and sc_year='".date("Y")."'";
	//$year_cond_lc="and lc_year='".date("Y")."'";
	//echo $lien_bank_id;die;

	//if($txt_search_common==0)$txt_search_common="";

    $txt_search_common = trim($txt_search_common);
    $search_cond ="";$search_cond_lc="";$search_cond_sc="";
    if($txt_search_common!="")
    {
        if($cbo_search_by==1)
        {
            $search_cond .= " and internal_file_no like '%$txt_search_common%'";
        }
        else if($cbo_search_by==2)
        {
            $search_cond .= " and buyer_name='$txt_search_common'";
        }
        else if($cbo_search_by==3)
        {
            $search_cond .= " and lien_bank='$txt_search_common'";
        }
        else if($cbo_search_by==4)
        {
            $search_cond_lc .= " and export_lc_no='$txt_search_common'";
            $search_cond_sc .= " and contract_no='$txt_search_common'";
        }
    }
    //echo $cbo_search_by."**".$txt_search_common; die;
    //echo $cbo_search_by."**".$search_cond_lc."**".$search_cond_sc; die;
    
		
		$sql = "SELECT a.id, a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , listagg(cast(a.export_lc_no as varchar2(4000)),',') within group (order by a.export_lc_no) as export_lc_no ,a.is_lc_sc from (
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year,  listagg(cast(export_lc_no as varchar2(4000)),',') within group (order by export_lc_no) as export_lc_no, 'export' as type, 1 as is_lc_sc
		from com_export_lc
		where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
		group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year, listagg(cast(contract_no as varchar2(4000)),',') within group (order by contract_no) as export_lc_no, 'import' as type, 2 as is_lc_sc
		from com_sales_contract		
		where beneficiary_name='$company_id'
		and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
		group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
		) a
		group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year, a.is_lc_sc
		order by a.id desc";
		// echo $sql;
    
    $lein_bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name",'id','bank_name');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name",'id','buyer_name');
	//echo $sql;
	?>
   <div style="width:560px">
    <form name="display_file"  id="display_file">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO.</td>
                <th width="80">File NO</td>
                <th width="80">Year</td>
                <th width="130"> Buyer</td>
                <th width="100"> Lien Bank</td>
                <th >SC/LC No.</td>
            </thead>
            </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" id="list_view">
            <tbody>
            <?
			$sql_results=sql_select($sql);
			$i=1;
			//echo count($sql_results);
			foreach($sql_results as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; 
				else $bgcolor="#FFFFFF";
				//echo $row[csf("internal_file_no")];die;
				//if($db_type==2) $row[csf('export_lc_no')] = $row[csf('export_lc_no')]->load();
				if($is_lc_sc==$row[csf('is_lc_sc')] && $lc_sc_id==$row[csf('id')]){$bgcolor="#FFFF00";}else{$bgcolor=$bgcolor;};
				?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf('internal_file_no')];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
                    <td align="center" width="60"> <? echo $i;?></td>
                    <td align="center" width="80"><p><? echo $row[csf("internal_file_no")];  ?></p></td>
                    <td align="center" width="80"><p><? echo $row[csf("lc_sc_year")];  ?></p></td>
                    <td width="130"><p><? echo $buyer_name_arr[$row[csf("buyer_name")]];  ?></p></td>
                    <td width="100"><p><? echo $lein_bank_arr[$row[csf("lien_bank")]];  ?></p></td>
                    <td><p><? echo $row[csf("export_lc_no")];  ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <input type="hidden" id="hide_file_no" name="hide_file_no"  />
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>

        <?
		exit();
}

if ($action=="fnc_process_data"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_revised_date=str_replace("'","",$txt_revised_date);
	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');

	?>
	<script>
	
	var txt_revised_date='<?=$txt_revised_date; ?>';
	//var po_job_level=cbo_level; 
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			//if(document.getElementById('check_all').checked==true)
			//{
				//po_job_level=1;
		//	}
			//else if(document.getElementById('check_all').checked==false)
			//{
				//po_job_level=cbo_level;
			//}
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

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		var selected_id = new Array();
		var selected_po=new Array();
		var selected_name=new Array();

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				var select_row=0;// var sp=1;
				//if(document.getElementById('chksinglerow').checked==true) po_job_level=1; else po_job_level=po_job_level; // ISD-22-16175
				//var shipment_date=$('#hidd_shipment_date' + str).val();
				//var max_date_control=$('#max_date_control').val();
				//if(max_date_control==1){
					//var date_com = date_diff('d',shipment_date, txt_revised_date);
					// if(date_com>60){
					// 	alert("Date Between Booking Date and PO ship date must be 60 days or less then 60 days");
					// 	return;
					// }					
					
				//}
				//if(po_job_level==1)
				//{
				//	var select_row= str;
				//	sp=1;
				//}
				//else if(po_job_level==2)
				//{
					var tbl_length =$('#tbl_list_search tr').length-1;
					var select_str=$('#txt_job_no' + str).val();
					for(var i=1; i<=tbl_length; i++)
					{
						var string=$('#txt_job_no' + i).val();
						if(select_str==string)
						{
							if(select_row==0)
							{
								select_row=i; sp=1;
								//alert(sp);
							}
							else
							{
								select_row+=','+i; sp=2;
								//alert(sp);
							}
						}
					}
				//}
				var exrow = new Array();
				if(sp==2) { exrow=select_row.split(','); var countrow=exrow.length; }
				else countrow=1;
				for(var m=0; m<countrow; m++)
				{
					if(sp==2) exrow[m]=exrow[m];
					else exrow[m]=select_row;
					toggle( document.getElementById( 'search' + exrow[m] ), '#FFFFCC' );
					if( jQuery.inArray( $('#txt_individual_id' + exrow[m]).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + exrow[m]).val() );
						selected_name.push($('#txt_job_no' + exrow[m]).val());
						selected_po.push($('#txt_po_id' + exrow[m]).val());
					}
					else{
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == $('#txt_individual_id' + exrow[m]).val() ) break;
						}
						selected_id.splice( i, 1 );
						selected_name.splice( i,1 );
						selected_po.splice( i,1 );
					}
				}
				var id = ''; var job = ''; var txt_po_id='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					job += selected_name[i] + ',';
					txt_po_id+=selected_po[i]+ ',';
				}
				id = id.substr( 0, id.length - 1 );
				job = job.substr( 0, job.length - 1 );
				txt_po_id = txt_po_id.substr( 0, txt_po_id.length - 1 );
				$('#txt_selected_id').val( id );
				$('#txt_job_id').val( job );
				$('#txt_selected_po').val( txt_po_id );
			}
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <input type="hidden" id="txt_booking" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
							<th width="100">Company Name</th>
							<th width="100">Buyer Name</th>
                            <th width="100">Style Ref</th>
                            <th width="80">Job No</th>
                            <th width="60">Job Year</th>
                            <th width="80">Int. Ref. No</th>
                            <th width="100">Order No</th>
                            <th width="100">File No</th>
                            <th width="130" colspan="2">Ship Date Range</th>
                        </tr>
                    </thead>
                    <tr class="general">
						<td><?=create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --",  str_replace("'","",$cbo_company_name), "load_drop_down('requires/revised_booking_report_controller', this.value, 'load_drop_down_buyer_order', 'buyer_td' );" ); ?>
						</td>
						<td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 120,"SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",  str_replace("'","",$cbo_buyer_name), "" ); ?>
						</td>
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                        <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:70px"></td>
                        <td><?=create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td><input name="txt_intref_no" id="txt_intref_no" class="text_boxes" style="width:70px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                        <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" value=""></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" value="" ></td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_intref_no').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_fnc_process_data', 'search_div', 'revised_booking_report_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:60px;" />
                        </td>
                    </tr>
					<tr>
                        <td  align="center"  valign="top" colspan="11">
                                    <input type="hidden" id="po_number_id">
                                    <input type="hidden" id="job_no">
									
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center"><strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes"  readonly style="width:450px" id="po_number"></td>
                    </tr>
                </table>
            </form>
        </div>
        <div id="search_div"></div>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_fnc_process_data")
{
	$data=explode('_',$data);
	//echo "<pre>";print_r($data);die;
	$cbo_company_name=$data[0];
	$cbo_buyer_name=$data[1];
	$txt_style=$data[2];
	$txt_job=$data[3];
	$cbo_year=$data[4];
	$txt_intref_no=$data[5];
	$txt_order_search=$data[6];
	$txt_file_no=$data[7];
	$txt_date_from=$data[8];
	$txt_date_to=$data[9];
	
	if($txt_style == '' && $txt_job == '' && $txt_intref_no == '' && $txt_order_search == '' && $cbo_buyer_name ==0 && $txt_file_no ==0 && $cbo_company_name==0 && $txt_date_from == '' && $txt_date_to =='')
	{ 	
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select any search data.";
		die;
	}

	if ($cbo_company_name!=0) $cbo_comp_cond=" and a.company_name=$cbo_company_name";else $cbo_comp_cond="";
	if ($cbo_buyer_name!=0) $cbo_buyer_cond=" and a.buyer_name=$cbo_buyer_name";else $cbo_buyer_cond="";
	if ($txt_style!="") $style_cond=" and a.style_ref_no='$txt_style'"; else $style_cond="";
	if ($txt_job!="") $job_cond=" and a.job_no_prefix_num='$txt_job'"; else $job_cond ="";
    if ($cbo_year!=0)$cbo_year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";else $cbo_year_cond="";
	if ($txt_intref_no!="") $txt_intref_cond=" and b.grouping='$txt_intref_no'"; else $txt_intref_cond="";
	if ($txt_order_search!="") $order_cond=" and b.po_number='$txt_order_search'"; else $order_cond="";
	if ($txt_file_no!="") $txt_file_cond=" and b.file_no='$txt_file_no'"; else $txt_file_cond="";

	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	extract(check_magic_quote_gpc($_REQUEST));
    if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	$shipment_date ="";
	if ($txt_date_from!="" &&  $txt_date_to!="")
	{
			if ($txt_date_from!="" &&  $txt_date_to!="") $shipment_date = "and b.shipment_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	?>
	<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
	<input type="hidden" name="txt_job_id" id="txt_job_id" value="" />
	<input type="hidden" name="txt_selected_po" id="txt_selected_po" value="" />
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1305" class="rpt_table"  >
        <thead>
            <th width="25">SL</th>
			<th width="70">Company</th>
            <th width="70">Buyer</th>
            <th width="80">Style Ref</th>
            <th width="70">Job No</th>
            <th width="70">Fab Booking No</th>
            <th width="80">Internal Ref.</th>
            <th width="70">File No</th>
            <th width="100">PO Number</th>
			<th width="100">Color Name</th>
            <th width="100">Color Qty</th>
            <th width="130">Shipment Date</th>
        </thead>
	</table>
	<div style="width:1325px; overflow-y:scroll; max-height:340px;" id="buyer_list_view" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1305" class="rpt_table" id="tbl_list_search" >
        <?
		$sql_po="select a.job_no_prefix_num, a.job_no, $year_field  as year, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping, b.po_quantity as plan_cut,b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.company_name=$cbo_company_name and b.shiping_status not in(3) $job_cond  $cbo_buyer_cond $order_cond $txt_intref_cond $style_cond $cbo_year_cond $shipment_date";
		//echo $sql_po; die;
		$sql_poRes=sql_select($sql_po); $jobData_arr=array(); $tot_rows=0; $poIds=''; $jobNo='';
		foreach($sql_poRes as $jrow)
		{
			$tot_rows++;
			$poIds.=$jrow[csf('id')].",";
			$jobNo.="'".$jrow[csf('job_no')]."',";
			$jobData_arr[$jrow[csf('id')]]['job_no_prefix_num']=$jrow[csf('job_no_prefix_num')];
			$jobData_arr[$jrow[csf('id')]]['job_no']=$jrow[csf('job_no')];
			$jobData_arr[$jrow[csf('id')]]['year']=$jrow[csf('year')];
			$jobData_arr[$jrow[csf('id')]]['company_name']=$jrow[csf('company_name')];
			$jobData_arr[$jrow[csf('id')]]['buyer_name']=$jrow[csf('buyer_name')];
			$jobData_arr[$jrow[csf('id')]]['style_ref_no']=$jrow[csf('style_ref_no')];
			$jobData_arr[$jrow[csf('id')]]['po_number']=$jrow[csf('po_number')];
			$jobData_arr[$jrow[csf('id')]]['file_no']=$jrow[csf('file_no')];
			$jobData_arr[$jrow[csf('id')]]['grouping']=$jrow[csf('grouping')];
			$jobData_arr[$jrow[csf('id')]]['shipment_date']=$jrow[csf('shipment_date')];
			$poIdArr[$jrow[csf('id')]]=$jrow[csf('id')];
			
		}
		//echo "<pre>";print_r($jobData_arr);
		unset($sql_poRes);
		$poId_cond=where_con_using_array($poIdArr,0,'d.po_break_down_id');
		$sql_color_booking_query="SELECT d.booking_no, d.po_break_down_id as po_id, c.color_number_id,sum(c.order_quantity) as order_quantity,e.company_id,e.buyer_id from wo_po_color_size_breakdown c, wo_booking_dtls d ,wo_booking_mst e where d.booking_mst_id=e.id and d.job_no = c.job_no_mst and d.po_break_down_id = c.po_break_down_id  and d.gmts_color_id = c.color_number_id and d.gmts_color_id = c.color_number_id and e.status_active = 1 and d.status_active = 1 and c.status_active = 1 $poId_cond group by d.booking_no, c.color_number_id,d.po_break_down_id,e.company_id,e.buyer_id order by d.booking_no desc";

		
     	$i=1; $total_req=0; $total_amount=0;
		$sql_color_booking=sql_select($sql_color_booking_query);
        foreach ($sql_color_booking as $row)
        {
        			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_id=$row[csf('po_id')];
					?>
					<tr style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i;?>)">
						<td width="25"><?=$i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<?=$row[csf('id')];?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<?=$jobData_arr['job_no'];?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<?=$po_id;?>"/>
							<input type="hidden" name="hidd_shipment_date" id="hidd_shipment_date<?php echo $i ?>" value="<?= change_date_format($row[csf('shipment_date')], "yyyy-mm-dd", "-");?>"/>
						</td>	
						<td width="70" style="word-break:break-all"><?=$comp[$row[csf('company_id')]];?></td>
						<td width="70" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_id')]];?></td>
						<td width="80" style="word-break:break-all"><?=$jobData_arr[$po_id]['style_ref_no'];?></td>
						<td width="70" style="word-break:break-all"><?=$jobData_arr[$po_id]['job_no'];?></td>
						<td width="70" style="word-break:break-all"><?=$row[csf('booking_no')];?></td>
						<td width="80" style="word-break:break-all"><?=$jobData_arr[$po_id]['grouping'];?></td>
						<td width="70" style="word-break:break-all"><?=$jobData_arr[$po_id]['file_no'];?></td>
						<td width="100"  style="word-break:break-all"><?=$jobData_arr[$po_id]['po_number'];?></td>
						<td width="100" style="word-break:break-all"><?=$color_library[$row[csf('color_number_id')]];?></td>
						<td width="100" style="word-break:break-all"><?=$row[csf('order_quantity')];?></td>
						<td width="130" style="word-break:break-all"><? echo change_date_format($jobData_arr[$po_id]['shipment_date']);?></td>
					</tr>
					<?
					$i++;
					$total_amount+=$amount;

        }
        ?>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1305" class="rpt_table">
        	<tfoot>
                <th width="25">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
				<th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
                <th width="130">&nbsp;</th>
            </tfoot>
        </table>
	</div>
	<table width="1305" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_total_req","value_total_amount"],
				col: [11,17],
				operation: ["sum","sum"],
				write_method: ["innerHTML","innerHTML"]
			}
		}
		setFilterGrid('tbl_list_search',-1,tableFilters)
	</script>
	</div>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="order_search_popup")
{
  	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<!-- <script>
	/*  var selected_id = new Array, selected_name = new Array();	
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		} */
		var selected_id = new Array, selected_name = new Array();
		function check_all_data(){
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length-1;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ){
				if($("#tr_"+i).css("display") !='none'){
				document.getElementById("tr_"+i).click();
				}
			}
		}
		
		function toggle( x, origColor ) 
		{
			//alert(x)
			var newColor = 'yellow';
			//if ( x.style ) 
			//{
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
			//}
		}
		
		function js_set_value( str_data,tr_id ) 
		{
			//alert(str_data);
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			//alert(str_all[2]);
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] )
			{
				alert('No Job Mix Allowed');return;	
			}
			toggle( tr_id, '#FFFFCC');
			document.getElementById('job_no').value=str_all[2];
			
			if( jQuery.inArray( str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( str_po );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#po_number_id').val( id );
			$('#po_number').val( name );
		}

		
    </script> -->

	<script>
		/* var selected_id = new Array, selected_name = new Array();
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				js_set_value( i );
			}
		} */
		var selected_id = new Array, selected_name = new Array();
		function check_all_data(){
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length-1;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ){
				if($("#tr_"+i).css("display") !='none'){
				document.getElementById("tr_"+i).click();
				}
			}
		}

		function toggle( x, origColor )
		{
			
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}

		function js_set_value( str_data,tr_id )
		{
			
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] )
			{
				alert('No Job Mix Allowed')
				return;
			}
			toggle( tr_id, '#FFFFCC');
			document.getElementById('job_no').value=str_all[2];

			if( jQuery.inArray( str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( str_po );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str ) break;
				}

				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
					//alert(selected_id.length)
				if(selected_id.length==0)
				{
					document.getElementById('job_no').value="";
				}
			}
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#po_number_id').val( id );
			$('#po_number').val( name );
		}
	</script>

</head>

<body>
<div align="center" style="width:100%;" >
<?
?>
	<form name="searchpofrm_1" id="searchpofrm_1">
    
         
				<table width="1100"  align="center" rules="all">
                    <tr>
                        <td align="center" width="100%">
                            <table  width="1090" class="rpt_table" align="center" rules="all">
                                <thead>                	 
                                    <th width="150">Company Name</th>
                                    <th width="140">Buyer Name</th>
									<th width="60">Job Year</th>
                                    <th width="100">Job No</th>
                                    <th width="60">Internal Ref</th>
                                    <th width="60">Order No</th>
                                    <th width="60">Style Ref</th>
                                    <th width="60">File No</th>
									
                                    <th width="150">Date Range</th><th><input  style="display:none" type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po"> </th>           
                                </thead>
                                <tr>
                                    <td> 
                                        <? 
                                            echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$cbo_company_name), "load_drop_down( 'service_booking_dyeing_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
                                        ?>
                                    </td>
                                <td id="buyer_td">
									
                                 <?
								 if(str_replace("'","",$cbo_company_name)!=0)
								 {
								 	echo create_drop_down( "cbo_buyer_name", 150,"select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".str_replace("'","",$cbo_company_name)."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" ); 
								 }
								 else
								 {
								   echo create_drop_down( "cbo_buyer_name", 150, $blank_array, 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
								 }
                                ?>	
                                </td>
								 <td><? echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                                 <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                                 <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px"></td>
                                 <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:130px"></td>
                                 <td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:60px"></td>
                                 <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
								
                                <td>
                                  <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value="<? //echo $start_date; ?>"/>
                                  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value="<? //echo $end_date; ?>"/>
                                 </td> 
                                 <td align="center">
                                 <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_year_selection').value, 'create_po_search_list_view', 'search_div', 'revised_booking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100%;" /></td>
                            </tr>
                            <tr>
                                <td  align="center"  valign="top" colspan="11">
									<? 
										echo create_drop_down("cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
									?>
                                    <? echo load_month_buttons();  ?>
                                    <input type="hidden" id="po_number_id">
                                    <input type="hidden" id="job_no">
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="6" align="center"><strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes"  readonly style="width:550px" id="po_number"></td>
                            </tr>
                         </table>
                        
    				</td>
           		</tr>
            <tr>
                <td align="center" >
                <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" /> 
                </td>
            </tr>
			 <tr>
				<td colspan="11" align="center">
					<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All 
				</td>
             </tr>
            <tr>
                <td id="search_div" align="center">
                            
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

if($action=="create_po_search_list_view")
{
	
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	$booking_date=$data[10];
	$job_no=$data[5];	
	//echo $job_no.'DDD';
	if ($job_no!="") $job_no_cond=" and a.job_no='$job_no' "; else  $job_no_cond=""; 
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==2) $insert_year="to_char(a.insert_date,'YYYY') as year";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
	if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]' "; else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; else  $order_cond=""; 


	//new development 
	if (str_replace("'","",$data[7])!="") $ref_cond=" and b.grouping='$data[7]' "; else  $ref_cond="";
	if (str_replace("'","",$data[8])!="") $style_ref_cond=" and a.style_ref_no='$data[8]' "; else  $style_ref_cond="";
	if (str_replace("'","",$data[9])!="") $file_no_cond=" and b.file_no='$data[9]' "; else  $file_no_cond="";
	if($db_type==2)
	{
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$jobYear = str_replace("'","", $data[10]);
	$jobYear_cond_month_field = str_replace("'","", $data[11]);
	$cbo_year_cond="";
	if($jobYear!=0)
	{
		if($db_type==2) $cbo_year_cond=" and to_char(a.insert_date,'YYYY')=$jobYear";
	}
	$month_field_year_cond="";
	if($jobYear_cond_month_field!=0)
	{
		if($db_type==2) $month_field_year_cond=" and to_char(a.insert_date,'YYYY')=$jobYear_cond_month_field";
	}
	 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	


		if ($data[2]==0)
		{
			// $sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.po_break_down_id=b.id and c.booking_type=1 and a.status_active=1 and b.status_active=1   $shipment_date $company $buyer $job_cond $order_cond $ref_cond $style_ref_cond $file_no_cond $job_no_cond order by a.job_no";  
		}
		else
		{
			$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 $company $buyer $job_no_cond order by a.job_no";
		
		}?>


<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1305" class="rpt_table"  >
        <thead>
            <th width="25">SL</th>
			<th width="70">Company</th>
            <th width="70">Buyer</th>
            <th width="80">Style Ref</th>
            <th width="70">Job No</th>
            <th width="70">Fab Booking No</th>
            <th width="80">Internal Ref.</th>
            <th width="70">File No</th>
            <th width="100">PO Number</th>
			<th width="100">Color Name</th>
            <th width="100">Color Qty</th>
            <th width="130">Shipment Date</th>

        </thead>
	</table>
	<div style="width:1325px; overflow-y:scroll; max-height:340px;" id="buyer_list_view" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1305" class="rpt_table" id="tbl_list_search" >
        <?
		$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
		$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

		 $year_field="to_char(a.insert_date,'YYYY')";
		  $sql_po="select a.job_no_prefix_num, a.job_no, $year_field  as year, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping, b.po_quantity as plan_cut,b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id  $style_ref_cond  $company $job_cond  $buyer $order_cond $ref_cond $cbo_year_cond $shipment_date $file_no_cond $month_field_year_cond";
		$sql_poRes=sql_select($sql_po); $jobData_arr=array(); $tot_rows=0; $poIds=''; $jobNo='';
		foreach($sql_poRes as $jrow)
		{
			$tot_rows++;
			$poIds.=$jrow[csf('id')].",";
			$jobNo.="'".$jrow[csf('job_no')]."',";
			$jobData_arr[$jrow[csf('id')]]['job_no_prefix_num']=$jrow[csf('job_no_prefix_num')];
			$jobData_arr[$jrow[csf('id')]]['job_no']=$jrow[csf('job_no')];
			$jobData_arr[$jrow[csf('id')]]['year']=$jrow[csf('year')];
			$jobData_arr[$jrow[csf('id')]]['company_name']=$jrow[csf('company_name')];
			$jobData_arr[$jrow[csf('id')]]['buyer_name']=$jrow[csf('buyer_name')];
			$jobData_arr[$jrow[csf('id')]]['style_ref_no']=$jrow[csf('style_ref_no')];
			$jobData_arr[$jrow[csf('id')]]['po_number']=$jrow[csf('po_number')];
			$jobData_arr[$jrow[csf('id')]]['file_no']=$jrow[csf('file_no')];
			$jobData_arr[$jrow[csf('id')]]['grouping']=$jrow[csf('grouping')];
			$jobData_arr[$jrow[csf('id')]]['shipment_date']=$jrow[csf('shipment_date')];
			$poIdArr[$jrow[csf('id')]]=$jrow[csf('id')];
			
		}
		//echo "<pre>";print_r($jobData_arr);and d.gmts_color_id = c.color_number_id
		unset($sql_poRes);
		$poId_cond=where_con_using_array($poIdArr,0,'d.po_break_down_id');
		    $sql_color_booking_query="SELECT d.booking_no, d.po_break_down_id as po_id, c.color_number_id,c.order_quantity as order_quantity,e.company_id,e.buyer_id from wo_po_color_size_breakdown c, wo_booking_dtls d ,wo_booking_mst e where d.booking_mst_id=e.id and d.job_no = c.job_no_mst and d.po_break_down_id = c.po_break_down_id and e.is_short=2  and e.status_active = 1 and d.status_active = 1 and c.status_active = 1 $poId_cond group by d.booking_no, c.color_number_id,c.order_quantity,d.po_break_down_id,e.company_id,e.buyer_id order by d.po_break_down_id ASC";
		
     	$i=1; $total_req=0; $total_amount=0;
		$sql_color_booking=sql_select($sql_color_booking_query);
        foreach ($sql_color_booking as $row)
        {
        			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_id=$row[csf('po_id')];
					$po_number=$jobData_arr[$po_id]['po_number'];
					$job_no=$jobData_arr[$po_id]['job_no'];
					$job_no_str=$po_id.'_'.$po_number.'_'.$job_no;
					//id,po_number,job_no
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<?=$i;?>" style="text-decoration:none; cursor:pointer"   onClick="js_set_value('<?=$job_no_str;?>',this.id)">
						<td width="25">
							<?=$i;?>
							 
						</td>	
						<td width="70" style="word-break:break-all"><?=$comp[$row[csf('company_id')]];?></td>
						<td width="70" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_id')]];?></td>
						<td width="80" style="word-break:break-all"><?=$jobData_arr[$po_id]['style_ref_no'];?></td>
						<td width="70" style="word-break:break-all"><?=$jobData_arr[$po_id]['job_no'];?></td>
						<td width="70" style="word-break:break-all"><?=$row[csf('booking_no')];?></td>
						<td width="80" style="word-break:break-all"><?=$jobData_arr[$po_id]['grouping'];?></td>
						<td width="70" style="word-break:break-all"><?=$jobData_arr[$po_id]['file_no'];?></td>
						<td width="100"  style="word-break:break-all"><?=$jobData_arr[$po_id]['po_number'];?></td>
						<td width="100" style="word-break:break-all"><?=$color_library[$row[csf('color_number_id')]];?></td>
						<td width="100" style="word-break:break-all"><?=$row[csf('order_quantity')];?></td>
						<td width="130" style="word-break:break-all"><? echo change_date_format($jobData_arr[$po_id]['shipment_date']);?></td>
					</tr>
					<?
					$i++;
					$total_amount+=$amount;

        }
        ?>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1305" class="rpt_table">
        	<tfoot>
                <th width="25">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
				<th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
                <th width="130">&nbsp;</th>
            </tfoot>
        </table>
	</div>
	 
<?
		 

exit();
	
} 
if ($action=="show_fabric_booking")
{
	//extract($_REQUEST);
	extract(check_magic_quote_gpc( $_REQUEST ));
	$data=str_replace("'","",$data);
	$param=str_replace("'","",$param);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_system_no=str_replace("'","",$txt_system_no);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	?>
    <table width="2020" class="rpt_table" border="0" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
				<th width="120">Booking No</th>
                <th width="120"  style="word-wrap: break-word;word-break: break-all;">PO Number</th>
                <th width="140">Gmts Item</th>
                <th width="120">Body Part</th>
                <th width="100">Color Type</th>
                <th width="100">Construction</th>
                <th width="100">Composition</th>
                <th width="50">GSM</th>
                <th width="80">Dia/Width</th>
                <th width="50">Item Size</th>
                <th width="150">Col. Sensivity</th>
                <th width="100">Gmts.Color</th>
                <th width="100">Fab.Color</th>
                <th width="100">Gmts. Quantity (Plan Cut)</th>
                <th width="100">Fin Fab Qnty</th>
                <th width="100">Booking ROW ID</th>
            </tr>
        </thead>
    </table>
    <div style=" max-height:200px; overflow-y:scroll; width:2025px"  align="left">
    <table width="2020" class="rpt_table" id="tbl_list_search" border="0" rules="all">
    <tbody>
	<?
	$sql_po="select c.id,c.po_number,a.company_id,a.is_approved from wo_po_break_down c, wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and c.job_no_mst=b.job_no and c.id=b.po_break_down_id  and b.id in ($data) and a.booking_no=b.booking_no and a.booking_type=1 and a.entry_form= 118 and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1";

	$result_po=sql_select($sql_po);
	foreach($result_po as $row)
	{
		$company_id=$row[csf('company_id')];
	}
	$job_sql=sql_select("select a.company_name, a.job_no, b.id, b.po_number, min(c.id) as cid, sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where b.id in(".$data.") and b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.plan_cut_qnty>0 group by a.company_name, a.job_no, b.id, b.po_number, c.color_number_id, c.size_number_id, c.item_number_id");

	$company2=0; $po_number_arr=array(); $paln_cut_qnty_array=array(); $popaln_cut_qnty_array=array();
	foreach($job_sql as $jrow)
	{
		$company2=$jrow[csf("company_name")];
		$job_no=$jrow[csf("job_no")];
		$po_number_arr[$jrow[csf("id")]]=$jrow[csf("po_number")];
		$paln_cut_qnty_array[$jrow[csf("cid")]]+=$jrow[csf("plan_cut_qnty")];
		$popaln_cut_qnty_array[$jrow[csf("id")]]+=$jrow[csf("plan_cut_qnty")];
	}
	//print_r($paln_cut_qnty_array);die;
	unset($job_sql);

		$tot_finish_fab_qnty=0; $tot_grey_fab_qnty=0;
		
		$booking_dtls_sql="SELECT a.id as booking_dtls_id,a.booking_no, b.id, a.fabric_color_id, a.fin_fab_qnty, a.grey_fab_qnty  from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id in(".$data.") and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

		$booking_dtls_res=sql_select($booking_dtls_sql);
		$booking_dtls_id_array=array(); $fabric_color_array=array(); $finish_fabric_qnty_array=array();   
		foreach($booking_dtls_res as $row)
		{
			$booking_dtls_id_array[$row[csf("id")]]=$row[csf("booking_dtls_id")];
			//$job_no=$row[csf("job_no")];
			//$fabric_color_array[$row[csf("id")]]=$row[csf("fabric_color_id")];
			$finish_fabric_qnty_array[$row[csf("id")]]+=$row[csf("fin_fab_qnty")];
			//$booking_no_array[$row[csf("booking_dtls_id")]]=$row[csf("booking_no")];
		}
		//print_r($booking_no_array);die;
		unset($booking_dtls_res);

		$contrastcolor_id="select job_no, pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls ";
		$contrastcolor_id_res=sql_select($contrastcolor_id); $contrastcolor_arr=array();
		foreach($contrastcolor_id_res as $row)
		{
			$contrastcolor_arr[$row[csf("job_no")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]=$row[csf("contrast_color_id")];
		}
		unset($contrastcolor_id_res);

		$body_part_type=return_library_array( "select id,body_part_type from lib_body_part", "id", "body_part_type"  );
		//$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down",'id','po_number');
		//echo "select gmts_item_id, set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'";
		$item_ratio_array=return_library_array( "select gmts_item_id, set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");
		$booking_no_query="select a.id as booking_id,a.booking_no,a.booking_mst_id,a.po_break_down_id from wo_booking_dtls a where a.po_break_down_id IN (".$data.") and a.booking_type=1 and a.is_short=2";
		$nameArray_booking=sql_select($booking_no_query);
		foreach ($nameArray_booking as $row)
		{ 
			$booking.=$row[csf("booking_no")].',';
			$booking_arr[$row[csf("po_break_down_id")]]['booking_no']=$row[csf("booking_no")];
			$booking_id_no.=$row[csf("booking_id")].',';

		}
	 	$name_sql="SELECT a.id as pre_cost_fabric_cost_dtls_id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id, a.fabric_source, a.color_type_id, a.gsm_weight, a.construction, a.composition, a.color_size_sensitive, a.costing_per, a.color, a.color_break_down, a.rate as rate_mst, b.id, b.po_break_down_id, b.color_size_table_id, c.fabric_color_id,c.gmts_color_id as color_number_id, b.gmts_sizes as size_number_id, b.dia_width, b.item_size, b.cons, b.process_loss_percent, b.requirment, b.rate, b.pcs, b.remarks,c.booking_no,c.booking_mst_id,sum(c.fin_fab_qnty) as fin_fab_qnty,sum(d.plan_cut_qnty) as plan_cut_qnty
		FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls c,wo_po_color_size_breakdown d 
		WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id= c.po_break_down_id and c.booking_type=1 and a.id=c.pre_cost_fabric_cost_dtls_id AND b.color_size_table_id = c.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id and d.id = c.color_size_table_id and b.po_break_down_id in (".$data.") and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id, a.fabric_source, a.color_type_id, a.gsm_weight, a.construction, a.composition, a.color_size_sensitive, a.costing_per, a.color, a.color_break_down, a.rate, b.id, b.po_break_down_id, b.color_size_table_id, c.fabric_color_id,c.gmts_color_id, b.gmts_sizes, b.dia_width, b.item_size, b.cons, b.process_loss_percent, b.requirment, b.rate, b.pcs, b.remarks,c.booking_no,c.booking_mst_id,c.fin_fab_qnty,d.plan_cut_qnty  order by b.po_break_down_id, a.id,b.color_size_table_id ASC"; 
		$nameArray=sql_select($name_sql);
		$powiseCostingPerReqQtyArr=array();
		foreach ($nameArray as $nrow)
		{
			
			$req_grey_fab_qty=0; $reqGreyFabQty=0; $costing_per=0;
			if($nrow[csf("costing_per")]==1) $costing_per=12;
			else if($nrow[csf("costing_per")]==2) $costing_per=1;
			else if($nrow[csf("costing_per")]==3) $costing_per=24;
			else if($nrow[csf("costing_per")]==4) $costing_per=36;
			else if($nrow[csf("costing_per")]==5) $costing_per=48;
			else $costing_per=0;
	
			$req_grey_fab_qty =($paln_cut_qnty_array[$nrow[csf("color_size_table_id")]]/$item_ratio_array[$nrow[csf("item_number_id")]])*($nrow[csf("requirment")]/$costing_per);
			$powiseCostingPerReqQtyArr[$nrow[csf("pre_cost_fabric_cost_dtls_id")]][$nrow[csf("po_break_down_id")]]['greyreqqty']+=$req_grey_fab_qty;

			$fab_dtl_id=$nrow[csf("pre_cost_fabric_cost_dtls_id")];

    		$booking_str=$nrow[csf("booking_no")].'_'.$nrow[csf("po_break_down_id")].'_'.$fab_dtl_id.'_'.$nrow[csf("dia_width")].'_'.$nrow[csf("item_size")].'_'.$nrow[csf("color_number_id")].'_'.$nrow[csf("fabric_color_id")];
			$booking_dataArr[$booking_str]['costing_per']=$costing_per;
			$booking_dataArr[$booking_str]['job_no']=$nrow[csf("job_no")];
			$booking_dataArr[$booking_str]['item_number_id']=$nrow[csf("item_number_id")];
			$booking_dataArr[$booking_str]['body_part_id']=$nrow[csf("body_part_id")];
			$booking_dataArr[$booking_str]['fab_nature_id']=$nrow[csf("fab_nature_id")];
			$booking_dataArr[$booking_str]['fabric_source']=$nrow[csf("fabric_source")];
			$booking_dataArr[$booking_str]['color_type_id']=$nrow[csf("color_type_id")];
			$booking_dataArr[$booking_str]['gsm_weight']=$nrow[csf("gsm_weight")];
			$booking_dataArr[$booking_str]['construction']=$nrow[csf("construction")];
			$booking_dataArr[$booking_str]['color_size_sensitive']=$nrow[csf("color_size_sensitive")];
			$booking_dataArr[$booking_str]['cons']+=$nrow[csf("cons")];
			$booking_dataArr[$booking_str]['requirment']=$nrow[csf("requirment")];
			$booking_dataArr[$booking_str]['composition']=$nrow[csf("composition")];
			$booking_dataArr[$booking_str]['dia_width']=$nrow[csf("dia_width")];
			$booking_dataArr[$booking_str]['color_size_table_id']=$nrow[csf("color_size_table_id")];
			$booking_dataArr[$booking_str]['booking_mst_id']=$nrow[csf("booking_mst_id")];
			$booking_dataArr[$booking_str]['id']=$nrow[csf("id")];
			$booking_dataArr[$booking_str]['color_break_down']=$nrow[csf("color_break_down")];
			$booking_dataArr[$booking_str]['item_size']+=$nrow[csf("item_size")];
			$booking_dataArr[$booking_str]['fin_fab_qnty']+=$nrow[csf("fin_fab_qnty")];
			$booking_dataArr[$booking_str]['plan_cut_qnty']+=$nrow[csf("plan_cut_qnty")];



			

		}
		//print_r($powiseCostingPerReqQtyArr);
		
        $count=0;
        foreach ($booking_dataArr as $fab_key=>$result)
        {
			
			$fab_arr=explode("_",$fab_key);
			$booking_no=$fab_arr[0];
			$po_id=$fab_arr[1];
			$fab_dtl_id=$fab_arr[2];
			$dia_width=$fab_arr[3];
			$item_size=$fab_arr[4];
			$color_number_id=$fab_arr[5];
			$fabric_color_id=$fab_arr[6];
			if (count($booking_dataArr)>0 )
            {
                $constrast_color_arr=array();
                if($result["color_size_sensitive"]==3)
                {
                    $constrast_color=explode('__',$result["color_break_down"]);
                    for($i=0;$i<count($constrast_color);$i++)
                    {
                        $constrast_color2=explode('_',$constrast_color[$i]);
                        $constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
                    }
                }

			    if ($count%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$costing_per=0;
				if($result["costing_per"]==1) $costing_per=12;
				else if($result["costing_per"]==2) $costing_per=1;
				else if($result["costing_per"]==3) $costing_per=24;
				else if($result["costing_per"]==4) $costing_per=36;
				else if($result["costing_per"]==5) $costing_per=48;
				else $costing_per=0;
	
				$bala_fin_fab_qnty =def_number_format(((($paln_cut_qnty_array[$result["color_size_table_id"]]/$item_ratio_array[$result["item_number_id"]])*($result["cons"]/$costing_per))*($txt_booking_percent/100)),5,"");
				
				$bala_grey_fab_qnty =def_number_format(((($paln_cut_qnty_array[$result["color_size_table_id"]]/$item_ratio_array[$result["item_number_id"]])*($result["requirment"]/$costing_per))*($txt_booking_percent/100)),5,"");
				//echo $bala_fin_fab_qnty.'='.$bala_grey_fab_qnty.', ';

				//preconskg_ value
				$itempoWiseReqQty=0;
				$itempoWiseReqQty=def_number_format(($powiseCostingPerReqQtyArr[$fab_dtl_id][$po_id]['greyreqqty']/($popaln_cut_qnty_array[$po_id]/$item_ratio_array[$result["item_number_id"]]))*$costing_per,5,"");

					$count++;
					?>
                	<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$count; ?>','<?=$bgcolor; ?>');" id="tr_<?=$count; ?>">
                        <td width="30" onClick="select_id_for_delete_item(<?=$count; ?>);" style="word-break:break-all"> <?=$count;   ?></td>
						<td width="120"  style="word-wrap: break-word;word-break: break-all;"><?=$booking_no;?> 
                        	<input type="hidden" id="fab_booking_id_<?=$count; ?>" style="width:20px;" value="<?=$booking_no; ?>"/>
						</td>
                        <td width="120"  style="word-wrap: break-word;word-break: break-all;"><?=$po_number_arr[$po_id];?> 
                        	<input type="hidden" id="pobreakdownid_<?=$count; ?>" style="width:20px;" value="<?=$po_id; ?>"/></td>
                        <td width="140" style="word-break:break-all"><?=$garments_item[$result["item_number_id"]];?></td>
                        <td width="120"  style="word-wrap: break-word;word-break: break-all;"><? echo $body_part[$result["body_part_id"]];?>
                            <input  type="hidden" id="bodypartid_<? echo $count; ?>" style="width:20px;" value="<? echo $result["body_part_id"]; ?>"/>
                            <input type="hidden" id="bodyparttype_<? echo $count; ?>" style="width:20px;" value="<? echo $body_part_type[$result["body_part_id"]]; ?>"/>
                        </td>
                        <td width="100" style="word-break:break-all"><? echo $color_type[$result["color_type_id"]];?> 
							<input type="hidden" id="colortype_<? echo $count; ?>" style="width:20px;" value="<? echo $result["color_type_id"]; ?>"/></td>
                        <td width="100" style="word-break:break-all"><? echo $result["construction"]; ?>
                            <input type="hidden" id="construction_<? echo $count; ?>" style="width:20px;" value="<? echo $result["construction"]; ?>"/>
                            <input type="hidden" id="precostfabriccostdtlsid_<? echo $count; ?>" style="width:20px;" value="<? echo $fab_dtl_id; ?>"/></td>
                        <td width="100" style="word-break:break-all"><? echo $result["composition"]; ?>
                            <input type="hidden" id="composition_<? echo $count; ?>" style="width:20px;" value="<? echo $result["composition"]; ?>"/>
                            <input type="hidden" id="cotaid_<? echo $count; ?>" style="width:20px;" value="<? echo $result["color_size_table_id"]; ?>"/>
                            <input type="hidden" id="preconskg_<? echo $count; ?>" style="width:20px;" value="<?=$itempoWiseReqQty; ?>"/>
                        </td>
                        <td width="50" style="word-break:break-all"><? echo $result["gsm_weight"]; ?> 
							<input type="hidden" id="gsmweight_<? echo $count; ?>" style="width:20px;" value="<? echo $result["gsm_weight"]; ?>"/></td>
                        <td width="80" style="word-break:break-all"><?  echo $result["dia_width"]; ?>
                            <input type="hidden" id="diawidth_<? echo $count; ?>" style="width:20px;" value="<? echo $result["dia_width"]; ?>"/>
                        </td>
                        <td width="50" style="word-break:break-all"><p><?  echo $item_size;?></p>
							<input  type="hidden" id="sizeid_<? echo $count; ?>" style="width:20px;" value="<? echo $item_size; ?>"/>
						</td>
                        <td width="150"><? echo $size_color_sensitive[$result["color_size_sensitive"]];  ?>
							<input  type="hidden" id="color_size_id_<? echo $count; ?>" style="width:20px;" value="<? echo $result["color_size_sensitive"]; ?>"/>
						</td>
                        <td width="100" style="word-break:break-all"> <? echo $color_library[$color_number_id]; ?>
                            <input  type="hidden" id="gmtscolorid_<? echo $count; ?>" style="width:20px;" value="<? echo $color_number_id; ?>"/>
                        </td>
                        <td width="100" style="word-break:break-all"><?$color_id=$fabric_color_id; echo $color_library[$color_id];?>
                            <input  type="hidden" id="colorid_<? echo $count; ?>" style="width:20px;" value="<? echo $color_id; ?>"/>
                        </td>
                        <td align="right" width="100" style="word-break:break-all"> <? echo $result["plan_cut_qnty"]; ?>
							<input  type="hidden" id="colorSizeTable_id_<? echo $count; ?>" style="width:20px;" value="<? echo $result["color_size_table_id"]; ?>"/></td>
                        <td align="right" width="100" style="word-break:break-all"><?=$result["fin_fab_qnty"];  //echo $finish_fabric_qnty_array[$result["id"]]; //$tot_finish_fab_qnty+=$finish_fabric_qnty_array[$result["id"]];?>
                        	<input type="hidden" title="<? echo $result["cons"]; ?>"   id="finscons_<? echo $count; ?>" name="finscons_<? echo $count; ?>" value="<?  echo $finish_fabric_qnty_array[$result["id"]]; //$tot_finish_fab_qnty+=$finish_fabric_qnty_array[$result["id"]];  ?>" />
                        </td>
                        <td align="right" width="100" style="word-break:break-all"><? echo $result["booking_mst_id"]; ?>
							<input type="hidden" id="booking_row_id_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px" value="<?echo $result["booking_mst_id"]; ?>" readonly/></td>
                    </tr>
                	<?
				 
			} 
		} 
	

	?>
	</tbody>
    </table>
    </div>
	<?
	exit();
}

if ($action=="generate_fabric_booking")
{
	//extract($_REQUEST);
	extract(check_magic_quote_gpc($_REQUEST));
	//print_r($_REQUEST);die;
	//$booking_month=0;
	if($cbo_booking_month<10) $booking_month.=$cbo_booking_month; else $booking_month=$cbo_booking_month;
	if($garments_nature==0) $garment_nature_cond=""; else $garment_nature_cond=" and a.garments_nature=$garments_nature";
	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	$param=implode(",",array_unique(explode(",",str_replace("'","",$param))));
	$data=implode(",",array_unique(explode(",",str_replace("'","",$data))));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name, conversion_factor, order_uom as cons_uom, hs_code, trim_type from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][hs_code]=$row_sql_lib_item_group[csf('hs_code')];
		$trim_type_arr[$row_sql_lib_item_group[csf('id')]]=$row_sql_lib_item_group[csf('trim_type')];
		$trim_group_library[$row_sql_lib_item_group[csf('id')]]=$row_sql_lib_item_group[csf('item_name')];
	}
	$exchange_rate_conversion = set_conversion_rate($cbo_currency, $txt_booking_date);//Conversion Exchance From Lib

	$condition= new condition();
	if(str_replace("'","",$data) !=''){
		$condition->po_id("in($data)");
	}

	$condition->init();
	$trims= new trims($condition);
	$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
	$reqAmountJobLevelArr=$trims->getAmountArray_by_job();
	$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c, wo_booking_mst e where a.id=d.job_id and e.booking_no=c.booking_no and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name $shipment_date and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.booking_type=2 and c.pre_cost_fabric_cost_dtls_id in($pre_cost_id)  and e.entry_form!=555  group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id");
	foreach($sql_cu_booking as $row_cu_booking)
	{
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
	}
	unset ($sql_cu_booking);
	
	$powiseCostingPerReqQtyArr=array();
	foreach ($req_amount_arr as $poid=>$podata)
	{
		foreach ($podata as $bomid=>$bomdata)
		{
			$powiseCostingPerReqQtyArr[$bomid][$poid]['amt']+=$bomdata;
		}
	}
	//print_r($powiseCostingPerReqQtyArr[34805][60978]);
	
	

		$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, c.amount, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e

	where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$cbo_company_name $garment_nature_cond and e.id in($param) and e.po_break_down_id in($data) and d.is_deleted=0 and d.status_active=1 

	group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, c.amount, d.id, d.po_number, d.po_quantity, e.po_break_down_id order by d.id,c.id";

	$i=1; $job_and_trimgroup_level=array();
	$nameArray=sql_select( $sql ); $job_noArr=array();
	/*foreach ($nameArray as $row)
	{
		$job_noArr[$row[csf('job_no')]]="'".$row[csf('job_no')]."'";
	}
	$txt_job_no=implode(",",$job_noArr); $item_ratio_array=array();
	if($txt_job_no!="")
	{
		$item_ratiodata=sql_select("select job_no, gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_no in ($txt_job_no)");
		foreach ($item_ratiodata as $irow)
		{
			$item_ratio_array[$irow[csf('job_no')]][$irow[csf('gmts_item_id')]]=$irow[csf('set_item_ratio')];
		}
		unset($item_ratiodata);
	}*/
	
	if($cbo_level==2)
	{
		foreach ($nameArray as $row)
		{
			$cbo_currency_job=$row[csf('currency_id')];
			$exchange_rate=$row[csf('exchange_rate')];//$exchange_rate_conversion;//
			if($cbo_currency==$cbo_currency_job) $exchange_rate=1;
			$job_no=$row[csf('job_no')];
			$trim_cost_dtls_id=$row[csf('wo_pre_cost_trim_cost_dtls')];
			$po_id=$row[csf('po_id')];

			$req_qnty_cons_uom=$req_qty_arr[$po_id][$trim_cost_dtls_id];
			$req_amount_cons_uom=$req_amount_arr[$po_id][$trim_cost_dtls_id];
			$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

			$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$row[csf('trim_group')]][conversion_factor],3,"");
			$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$row[csf('trim_group')]][conversion_factor])*$exchange_rate,3,"");
			$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,3,"");

			$cu_woq=$cu_booking_arr[$job_no][$trim_cost_dtls_id]['cu_woq'][$po_id];
			$cu_amount=$cu_booking_arr[$job_no][$trim_cost_dtls_id]['cu_amount'][$po_id];
			$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,3,"");
			$amount=def_number_format($rate_ord_uom*$bal_woq,3,"");

			//$reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$job_no];
			
			$costing_per=0;
			if($row[csf('costing_per')]==1) $costing_per=12;
			else if($row[csf('costing_per')]==2) $costing_per=1;
			else if($row[csf('costing_per')]==3) $costing_per=24;
			else if($row[csf('costing_per')]==4) $costing_per=36;
			else if($row[csf('costing_per')]==5) $costing_per=48;
			else $costing_per=0;
			
			$itempoWiseReqQty=0;
			
			$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$trim_cost_dtls_id][$po_id]['amt']/$row[csf('plan_cut')])*$costing_per;
			if($row[csf('country')]=="") $row[csf('country')]="";
			if($row[csf('description')]=="") $row[csf('description')]="";
			if($row[csf('brand_sup_ref')]=="") $row[csf('brand_sup_ref')]="";
			if($txt_delivery_date=="") $txt_delivery_date="";
			if($cu_woq=="") $cu_woq=0;
			if($cu_amount=="") $cu_amount=0;

			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['job_no'][$po_id]=$job_no;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['style_ref_no'][$po_id]=$row[csf('style_ref_no')];
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['po_id'][$po_id]=$po_id;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['po_number'][$po_id]=$row[csf('po_number')];
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['country'][$po_id]=$row[csf('country')];
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['description'][$po_id]=$row[csf('description')];
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['brand_sup_ref'][$po_id]=$row[csf('brand_sup_ref')];
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['trim_group'][$po_id]=$row[csf('trim_group')];
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['trim_group_name'][$po_id]=$trim_group_library[$row[csf('trim_group')]];
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['wo_pre_cost_trim_cost_dtls'][$po_id]=$trim_cost_dtls_id;

			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['req_qnty'][$po_id]=$req_qnty_ord_uom;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['uom'][$po_id]=$sql_lib_item_group_array[$row[csf('trim_group')]][cons_uom];
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['uom_name'][$po_id]=$unit_of_measurement[$sql_lib_item_group_array[$row[csf('trim_group')]][cons_uom]];
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['req_amount'][$po_id]=$req_amount_ord_uom;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['req_amount_cons_uom'][$po_id]=$req_amount_cons_uom;
			//$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['req_amount_job_lebel_cons_uom'][$po_id]=$reqAmtJobLevelConsUom;

			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['cu_woq'][$po_id]=$cu_woq;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['cu_amount'][$po_id]=$cu_amount;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['bal_woq'][$po_id]=$bal_woq;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['exchange_rate'][$po_id]=$exchange_rate;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['rate'][$po_id]=$rate_ord_uom;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['amount'][$po_id]=$amount;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['pre_req_amt'][$po_id]=$row[csf('amount')];
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['txt_delivery_date'][$po_id]=$txt_delivery_date;
			$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['reqqtycostingper'][$po_id]=$itempoWiseReqQty;
		}
	}
	?>
	<input type="hidden" id="strdata" value='<?=json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1740" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="80">Job No</th>
            <th width="80">Style  Ref</th>
            <th width="100">Ord. No</th>
            <th width="100">Trims Group</th>
            <th width="100">HS Code</th>
            <th width="150">Description</th>
            <th width="150">Brand Sup.</th>
            <th width="70">Req. Qnty</th>
            <th width="50">UOM</th>
            <th width="80">CU WOQ</th>
            <th width="80">Bal WOQ</th>
            <th width="100">Sensitivity</th>
            <th width="80">WOQ</th>
            <th width="55">Exch.Rate</th>
            <th width="80">Rate</th>
            <th width="80">Amount</th>
            <th width="80">Delv. Date</th>
            <th width="80">Remark</th>
			<th>Image</th>
        </thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1740" class="rpt_table" id="tbl_list_search" >
        <tbody>
        <?
        if($cbo_level==1)
        {
            foreach ($nameArray as $selectResult)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                $cbo_currency_job=$selectResult[csf('currency_id')];
                $exchange_rate=$selectResult[csf('exchange_rate')];//$exchange_rate_conversion;//
                if($cbo_currency == $cbo_currency_job) $exchange_rate=1;

                $req_qnty_cons_uom = $req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
                $req_amount_cons_uom = $req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
                $rate_cons_uom = $req_amount_cons_uom/$req_qnty_cons_uom;

                $req_qnty_ord_uom = def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
                $rate_ord_uom = def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
                $req_amount_ord_uom = def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

                $cu_woq = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]];
                $cu_amount = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]];
                $bal_woq = def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
                $amount = def_number_format($bal_woq*$rate_ord_uom,5,"");
                $reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$selectResult[csf('job_no')]];
				$req_amt_bal=$req_amount_ord_uom-$cu_amount;

				if($cbo_company_name<0) $cbo_company_name=0;
					//$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$cbo_company_name' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");

				if($tna_integrated==1)
				{
					//echo " $('#txt_delevary_date').attr('disabled',true);\n";
					$txt_delivery_date="";
					$deli_date_con="disabled=disabled";
				}
				else
				{
					//echo "$('#txt_delevary_date').attr('disabled',false);\n";
					$deli_date_con="";
				}
				$trim_type=$trim_type_arr[$selectResult[csf('trim_group')]];
				if($trim_type==1) $task_num='70'; else $task_num='71';
				$task_finish_date='';
				$po_id_txt=$selectResult[csf('po_id')];
				/*$tnasql=sql_select("select po_number_id,task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($po_id_txt) and is_deleted= 0 and status_active=1");

				foreach($tnasql as $tnarow){
				$task_finish_date_arr[$tnarow[csf('po_number_id')]]=$tnarow[csf('task_finish_date')];
				}*/
				$task_finish_date=$tnataskfinishArr[$task_num][$po_id_txt];
				
				//$task_finish_date_arr[$selectResult[csf('po_id')]];

				$sql_tna_lib=sql_select("select b.date_calc, b.day_status from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc='$task_finish_date' and a.status_active=1 and a.is_deleted=0");

				$date_calc=$sql_tna_lib[0][csf("date_calc")];
				$day_status=$sql_tna_lib[0][csf("day_status")];

				if($day_status==2)
				{
					$task_finish_date=return_field_value("max(b.date_calc) as date_calc ", "lib_capacity_calc_mst a, lib_capacity_calc_dtls b","a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc<'$task_finish_date' and a.status_active=1 and a.is_deleted=0 and b.day_status=1","date_calc");
				}
				else
				{
					$task_finish_date=$task_finish_date;
				}

				if($task_finish_date !='')
				{
					$txt_delivery_date=change_date_format($task_finish_date,'dd-mm-yyyy','-');
					$txt_tna_date=change_date_format($task_finish_date,'dd-mm-yyyy','-');
				}
				else
				{
					$txt_delivery_date=""; $txt_tna_date="";
				}
				
				$costing_per=0;
				if($selectResult[csf('costing_per')]==1) $costing_per=12;
				else if($selectResult[csf('costing_per')]==2) $costing_per=1;
				else if($selectResult[csf('costing_per')]==3) $costing_per=24;
				else if($selectResult[csf('costing_per')]==4) $costing_per=36;
				else if($selectResult[csf('costing_per')]==5) $costing_per=48;
				else $costing_per=0;
				
				$itempoWiseReqQty=0;
			
				$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('po_id')]]['amt']/$selectResult[csf('plan_cut')])*$costing_per;

                ?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="change_color('search<?=$i; ?>','<?=$bgcolor; ?>');">
                    <td width="40"><?=$i; ?></td>
                    <td width="80" style="word-wrap:break-word; word-break:break-all; width:78px"><?=$selectResult[csf('job_no')]; ?>
                        <input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$selectResult[csf('job_no')]; ?>" style="width:30px" class="text_boxes" readonly/>
                    </td>
                     <td width="80" style="word-wrap:break-word; word-break:break-all; width:78px">
                          <p>  <? echo $selectResult[csf('style_ref_no')];?> </p>
                        </td>
                    <td width="100" style="word-wrap:break-word; word-break:break-all; width:98px"><?=$selectResult[csf('po_number')]; ?>
                        <input type="hidden" id="txtbookingid_<?=$i;?>" value="" readonly/>
                        <input type="hidden" id="txtpoid_<?=$i;?>" value="<?=$selectResult[csf('po_id')];?>" readonly/>
                        <input type="hidden" id="txtcountry_<?=$i;?>" value="<?=$selectResult[csf('country')]; ?>" readonly />
                    </td>
                    <td width="100" title="<?=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]; ?>">
					<?=$trim_group_library[$selectResult[csf('trim_group')]];?>
                        <input type="hidden" id="txttrimcostid_<?=$i;?>" value="<?=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/>
                        <input type="hidden" id="txttrimgroup_<?=$i;?>" value="<?=$selectResult[csf('trim_group')];?>" readonly/>
                        <input type="hidden" id="txtReqAmt_<?=$i;?>" value="<?=$itempoWiseReqQty; ?>" style="width:30px"/>
						<input id="hiddlabeldtlsdata_<?=$i;?>" type="hidden" value=""/>
                    </td>
					<td width="100"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txthscode_<?=$i;?>" value="<?=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][hs_code] ?>" /></td>
                    <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdesc_<? echo $i;?>"  value="<? echo $selectResult[csf('description')];?>" /></td>
                    <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtbrandsup_<? echo $i;?>"  value="<? echo $selectResult[csf('brand_sup_ref')];?>" /></td>
                    <td width="70" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                    </td>
                    <td width="50"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?>
                        <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($selectResult[csf('cu_woq')],4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($selectResult[csf('cu_amount')],4,'.','');?>"  readonly  />
                    </td>
                    <td width="80" align="right"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.',''); ?>" readonly  /></td>
                    <td width="100" align="right"><? echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?></td>
                    <td width="80" align="right"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>" onClick="open_consumption_popup('requires/revised_booking_report_controller.php?action=consumption_popup', '<?=$trim_group_library[$selectResult[csf('trim_group')]]?>','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly /></td>
                    <td width="55" align="right"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly /></td>
                    <td width="80" align="right">
                        <?
                        $ratetexcolor="#000000";
                        $decimal=explode(".",$rate_ord_uom);
                        if(strlen($decimal[1]>6)) $ratetexcolor="#F00";
                        ?>
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   text-align:right; color:<? echo $ratetexcolor; ?>; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate_ord_uom ;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />
                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />
                    </td>
                    <td width="80" align="right"><input type="text"  title="Available balance=<? echo number_format($req_amt_bal,4);?>"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>" readonly /></td>
                    <td width="80" align="right">
                        <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" onChange="compare_date(1)" value="<? echo $txt_delivery_date; ?>"   <? echo $deli_date_con; ?> readonly  />
                        <input name="txttnadate_<? echo $i;?>" id="txttnadate_<? echo $i;?>" class="datepicker" type="hidden" value="<? echo $txt_tna_date;?>" style="width:70px;"  readonly/>
                        <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                        <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                    </td>
                     <td width="80" align="right">
                        <input type="text"   style="width:70px; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:center; background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>"  class="text_boxes"  value="<? //echo $txt_delivery_date; ?>" />
                        
                         
                    </td>
					<td width="" align="right">
					<input type="button" class="image_uploader" id="uploader" style="width:60px" value="ADD Image" onClick="fnc_file_upload(<? echo $i;?>);"> 
                         
                    </td>
                </tr>
                <?
                $i++;
            }
        }
        else if($cbo_level==2)
        {
            $i=1;
            foreach ($job_and_trimgroup_level as $job_no)
            {
                foreach ($job_no as $wo_pre_cost_trim_cost_dtls)
                {
                    $job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
                    $po_number=implode(", ",$wo_pre_cost_trim_cost_dtls['po_number']);
					$style_ref_no=implode(", ",$wo_pre_cost_trim_cost_dtls['style_ref_no']);
                    $po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
                    $country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
                    $description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
                    $brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
                    $wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
                    $wo_pre_req_amt=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['pre_req_amt']));

                    $trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
                    $uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));

                    $req_qnty_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
                    $rate_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
                    $req_amount_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);
                    $req_amount_cons_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount_cons_uom']);

                    $bal_woq=array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
                    $amount=array_sum($wo_pre_cost_trim_cost_dtls['amount']);

                    $cu_woq=array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
                    $cu_amount=array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);

                    $reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$job_no];
					$req_amt_bal=$req_amount_ord_uom-$cu_amount;
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					if($cbo_company_name<0) $cbo_company_name=0;
					$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$cbo_company_name' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");

					//echo $tna_integrated."TTTTTTTTTTM";
					if($tna_integrated==1)
					{
						//echo " $('#txt_delevary_date').attr('disabled',true);\n";
						$deli_date_con="disabled=disabled";
					}
					else
					{
						//echo "$('#txt_delevary_date').attr('disabled',false);\n";
						$deli_date_con="";
					}
					$trim_type=$trim_type_arr[$trim_group];
					if($trim_type==1) $task_num='70'; else $task_num='71';
					$task_finish_date='';
					$tnasql=sql_select("select min(po_number_id) as po_number_id,min(task_finish_date) as  task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($data) and is_deleted= 0 and status_active=1 order by task_finish_date ");
					//echo "select min(po_number_id) as po_number_id,min(task_finish_date) as  task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($data) and is_deleted= 0 and status_active=1 order by task_finish_date ";

					foreach($tnasql as $tnarow){
					//$task_finish_date_arr[$tnarow[csf('po_number_id')]]=$tnarow[csf('task_finish_date')];
					$task_finish_date=$tnarow[csf('task_finish_date')];
					}
					//echo $po_id.'='.$data;
					//$po_ids=array_unique(explode(",",$po_id));
					//$task_finish_date="";
					
					//$task_finish_date=$task_finish_date_arr[$pId];
					//echo $task_finish_date.'G';
					$sql_tna_lib=sql_select("select b.date_calc, b.day_status from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc='$task_finish_date' and a.status_active=1 and a.is_deleted=0");
					//echo "select b.date_calc, b.day_status from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc='$task_finish_date' and a.status_active=1 and a.is_deleted=0";

					$date_calc=$sql_tna_lib[0][csf("date_calc")];
					$day_status=$sql_tna_lib[0][csf("day_status")];

					if($day_status==2)
					{
					$task_finish_date=return_field_value("max(b.date_calc) as  date_calc ", " lib_capacity_calc_mst a, lib_capacity_calc_dtls b "," a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc<'$task_finish_date' and a.status_active=1 and a.is_deleted=0 and b.day_status=1","date_calc");
					//$task_finish_date.=$task_finish_date.",";
					}
					else
					{
					$task_finish_date=$task_finish_date;
					}
					//echo $task_finish_date.', ';
					$txt_delivery_date="";
					if($task_finish_date !='')
					{
						$txt_delivery_date=change_date_format($task_finish_date,'dd-mm-yyyy','-');
						$txt_tna_date=change_date_format($task_finish_date,'dd-mm-yyyy','-');
					}
					else
					{
						$txt_delivery_date="";
						$txt_tna_date="";
					}
                    ?>
                    <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="change_color('search<?=$i; ?>','<?=$bgcolor; ?>');">
                        <td width="40"><?=$i;?></td>
                        <td width="80"><?=$job_no; ?>
                            <input type="hidden" id="txtjob_<?=$i;?>" value="<?=$job_no;?>" style="width:30px" class="text_boxes" readonly/>
                        </td>
                        <td width="80" style="word-wrap:break-word; word-break:break-all; width:80px">
                          <p>  <?=$style_ref_no;?></p>
                        </td>
                        <td width="100" style="word-wrap:break-word; word-break:break-all; width:100px"><?=$po_number; ?>
                            <input type="hidden" id="txtbookingid_<?=$i;?>" value="" readonly/>
                            <input type="hidden" id="txtpoid_<?=$i;?>" value="<?=$po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<?=$i;?>" value="<?=$country; ?>" readonly />
                        </td>
                        <td width="100" title="<?=$sql_lib_item_group_array[$trim_group][conversion_factor]; ?>">
						<A href="javascript:void(0)" onClick="openlabeldtls_popup('0_<?=$trim_group."_".$i; ?>',<?=$i; ?>);"><?=$trim_group_library[$trim_group];?></A>
                            <input type="hidden" id="txttrimcostid_<?=$i;?>" value="<?=$wo_pre_cost_trim_id;?>" readonly/>
                            <input type="hidden" id="txttrimgroup_<?=$i;?>" value="<?=$trim_group;?>" readonly/>
                            <input class="text_boxes" name="txtReqAmt_<?=$i;?>" id="txtReqAmt_<?=$i;?>" type="hidden" value="<? //=$wo_pre_req_amt; ?>" style="width:30px"/>
							<input id="hiddlabeldtlsdata_<?=$i;?>" type="hidden" value=""/>
                        </td>
						<td width="100"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txthscode_<?=$i;?>" value="<?=$sql_lib_item_group_array[$trim_group][hs_code] ?>" /></td>
                        <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txtdesc_<?=$i;?>" value="<?=$description; ?>" /></td>
                        <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txtbrandsup_<?=$i;?>" value="<?=$brand_sup_ref;?>" /></td>
                        <td width="70" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                        </td>
                        <td width="50"><? echo $unit_of_measurement[$uom]; ?><input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $uom;?>" readonly /></td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.',''); ?>"  readonly  />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($cu_amount,4,'.','');?>"  readonly  />
                        </td>
                        <td width="80" align="right"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>" readonly /></td>
                        <td width="100" align="right"><? echo create_drop_down( "cbocolorsizesensitive_".$i, 95, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?></td>
                        <td width="80" align="right"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>" onClick="open_consumption_popup('requires/revised_booking_report_controller.php?action=consumption_popup', '<?=$trim_group_library[$trim_group]?>','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly/></td>
                        <td width="55" align="right"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly /></td>
                        <td width="80" align="right">
                            <?
                            $ratetexcolor="#000000";
                            $decimal=explode(".",$rate_ord_uom);
                            if(strlen($decimal[1])>6) $ratetexcolor="#F00";
                            ?>
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; color:<? echo $ratetexcolor;  ?>;  background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate_ord_uom ;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />
                        </td>
                        <td width="80" align="right"> <input type="text"  title="Available balance=<? echo number_format($req_amt_bal,4);?>" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>" readonly /></td>
                        <td width="80" align="center">
                            <input type="text"   style="width:70px; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo $txt_delivery_date; ?>"  onChange="compare_date(1)"  <? echo $deli_date_con;?> readonly />
                            <input name="txttnadate_<? echo $i;?>" id="txttnadate_<? echo $i;?>" class="datepicker" type="hidden" value="<? echo $txt_tna_date;?>" style="width:70px;"  readonly/>
                            <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                            <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                        </td>
                        <td width="80" align="center">
                        <input type="text"   style="width:70px; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:center; background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>"  class="text_boxes"  value="<? //echo $txt_delivery_date; ?>" />
                        </td>
                        <td width="" align="right">
						<input type="button" class="image_uploader" id="uploader" style="width:60px" value="ADD Image" onClick="fnc_file_upload(<? echo $i;?>);"> 
                    	</td>
                    </tr>
                    <?
                    $i++;
                }
            }
        }
        ?>
        </tbody>
	</table>
	<table width="1740" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="40">&nbsp;</th>
                <th width="80"></th>
                 <th width="80"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="150"></th>
                <th width="150"></th>
                <th width="70"><? echo $tot_req_qty; ?></th>
                <th width="50"></th>
                <th width="80"><? echo $tot_cu_woq; ?></th>
                <th width="80"><? echo $tot_bal_woq; ?></th>
                <th width="100"></th>
                <th width="80"></th>
                <th width="55"></th>
                <th width="80"></th>
                <th width="80"><input type="hidden" id="tot_amount" value="<? echo  $total_amount; ?>" style="width:80px" readonly /></th>
                <th width="80"><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
                 <th width="80"></th>
                 <th width=""></th>
            </tr>
        </tfoot>
	</table>
    <table width="1740" colspan="18" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container">
            	<? echo load_submit_buttons( $permission, "fnc_revised_booking_dtls", 0,0,"reset_form('','booking_list_view','','','')",2); ?>
            </td>
        </tr>
    </table>
	<?
	exit();
}

if($action=="check_is_booking_used")
{
	$txt_booking_no="'".$data."'";
	if($data!="")
	{
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		

		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}
	}
	exit();
}

if ($action == "consumption_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	?>
	<script>
		var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from lib_size where status_active=1 and is_deleted=0", "size_name" ), 0, -1); ?>];
		var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0", "color_name" ), 0, -1); ?>];

		function poportionate_qty(qty)
		{
			var round_check=0;
			var total_txtwoq_cal=0;
			if ($('#round_down').is(":checked"))
			{
			   round_check=1;
			}
			var txtwoq=document.getElementById('txtwoq').value*1;
			var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for(var i=1; i<=rowCount; i++){
				var poreqqty=$('#poreqqty_'+i).val()*1;
				var txtwoq_cal =number_format_common((txtwoq_qty/txtwoq) * (poreqqty),5,0);
				if(round_check==1){
					txtwoq_cal=Math.floor(txtwoq_cal);
					total_txtwoq_cal+=Math.floor(txtwoq_cal);
				}
				$('#qty_'+i).val(txtwoq_cal);
				calculate_requirement(i);
			}
			set_sum_value( 'qty_sum', 'qty_');
			if(round_check!=1){
				var j=i-1;
				var qty_sum=document.getElementById('qty_sum').value*1;
				if(qty_sum >txtwoq_qty ){
					$('#qty_'+j).val(number_format_common(txtwoq_cal*1-(qty_sum-txtwoq_qty),5,0));				
				}
				else if(qty_sum < txtwoq_qty ){
					$('#qty_'+j).val(number_format_common((txtwoq_cal*1) +(txtwoq_qty - qty_sum),5,0));				
				}
				else{
					$('#qty_'+j).val(number_format_common(txtwoq_cal,5,0));
				}
			}
			//set_sum_value( 'qty_sum', 'qty_');
			calculate_requirement(j);
		}

		function calculate_requirement(i)
		{
			var process_loss_method_id=document.getElementById('process_loss_method_id').value;
			if(process_loss_method_id == ''){
				console.log('if process_loss_method_id setting value 0 calculate requirment set woqny 0 ::'+process_loss_method_id);
			}
			var cons=(document.getElementById('qty_'+i).value)*1;
			var processloss=(document.getElementById('excess_'+i).value)*1;
			var WastageQty='';
			if(process_loss_method_id==1){
				WastageQty=cons+cons*(processloss/100);
			}
			else if(process_loss_method_id==2){
				var devided_val = 1-(processloss/100);
				var WastageQty=parseFloat(cons/devided_val);
			}
			else{
				WastageQty=0;
			}
			WastageQty= number_format_common( WastageQty, 5, 0) ;
			document.getElementById('woqny_'+i).value= WastageQty;
			set_sum_value( 'woqty_sum', 'woqny_' );
			calculate_amount(i);
		}

		function set_sum_value(des_fil_id,field_id)
		{
			if(des_fil_id=='qty_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='excess_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='woqty_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='amount_sum') var ddd={dec_type:6,comma:0,currency:0};
			if(des_fil_id=='pcs_sum') var ddd={dec_type:6,comma:0};
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}

		function copy_value(value,field_id,i)
		{
			var itemsizes=document.getElementById('itemsizes_'+i).value;
			var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
			var pocolorid=document.getElementById('pocolorid_'+i).value;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			var copy_basis=$('input[name="copy_basis"]:checked').val()

			for(var j=i; j<=rowCount; j++)
			{
				if(field_id=='des_' || field_id=='brndsup_' || field_id=='itemcolor_' || field_id=='itemsizes_' || field_id=='qty_' || field_id=='excess_' || field_id=='rate_' || field_id=='itemref_' || field_id=='hidRateCalStr_'){
					if(copy_basis==0) document.getElementById(field_id+j).value=value;
					if(copy_basis==1)
					{
						if(field_id=='itemcolor_')
						{
							if( pocolorid==document.getElementById('pocolorid_'+j).value) document.getElementById(field_id+j).value=value;
						}
						else
						{
							if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value) document.getElementById(field_id+j).value=value;
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('pocolorid_'+j).value) document.getElementById(field_id+j).value=value;
					}
					if(copy_basis==3){
						if( itemsizes==document.getElementById('itemsizes_'+j).value) document.getElementById(field_id+j).value=value;
					}
					if(field_id=='qty_' || field_id=='excess_')
					{
						calculate_requirement(j);
						if(field_id=='qty_') set_sum_value( 'qty_sum', 'qty_' );
					}
					if(field_id=='rate_') calculate_amount(j);
				}
			}
		}

		function calculate_amount(i)
		{
			var rate=(document.getElementById('rate_'+i).value)*1;
			var woqny=(document.getElementById('woqny_'+i).value)*1;
			var amount=number_format_common((rate*woqny),6,0);
			document.getElementById('amount_'+i).value=amount;
			set_sum_value( 'amount_sum', 'amount_' );
			calculate_avg_rate();
		}

		function calculate_avg_rate()
		{
			var woqty_sum=document.getElementById('woqty_sum').value;
			var amount_sum=document.getElementById('amount_sum').value;
			//var avg_rate=number_format_common((amount_sum/woqty_sum),5,0);
			var avg_rate=(amount_sum/woqty_sum);
			document.getElementById('rate_sum').value=number_format_common((avg_rate),6,0);
		}

		function js_set_value()
		{
			var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
			var row_num=$('#tbl_consmption_cost tbody tr').length;
			var cons_breck_down="";
			for(var i=1; i<=row_num; i++){
				var txtdescription=$('#des_'+i).val();
				var txtsupref=$('#brndsup_'+i).val();
				//alert(txtdescription.match(reg))
				if(txtdescription.match(reg)){
					alert("Your Description Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
					//release_freezing();
					$('#des_'+i).css('background-color', 'red');
					return;
				}
				if(txtsupref.match(reg)){
					alert("Your Brand Sup. Ref Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
					$('#brndsup_'+i).css('background-color', 'red');
					//release_freezing();
					return;
				}

				var pocolorid=$('#pocolorid_'+i).val();					if($('#pocolorid_'+i).val()=='') pocolorid=0;
				var gmtssizesid=$('#gmtssizesid_'+i).val(); 			if(gmtssizesid=='') gmtssizesid=0;
				var des=trim($('#des_'+i).val()); 						if(des=='') des=0;
				var brndsup=trim($('#brndsup_'+i).val()); 				if(brndsup=='') brndsup=0;
				var itemcolor=$('#itemcolor_'+i).val(); 				if(itemcolor=='') itemcolor=0;
				
				var preitemcolor=$('#preitemcolor_'+i).val(); 				if(preitemcolor=='') preitemcolor=0;
				var preitemsizes=$('#preitemsizes_'+i).val(); 				if(preitemsizes=='') preitemsizes=0;
				
				var itemref=$('#itemref_'+i).val(); 					if(itemref=='') itemref=0;
				var itemsizes=$('#itemsizes_'+i).val(); 				if(itemsizes=='') itemsizes=0;
				var qty=$('#qty_'+i).val(); 							if(qty=='') qty=0;
				var excess=$('#excess_'+i).val(); 						if(excess=='') excess=0;
				var woqny=$('#woqny_'+i).val(); 						if(woqny=='') woqny=0;
				var rate=$('#rate_'+i).val(); 							if(rate=='') rate=0;
				var amount=$('#amount_'+i).val(); 						if(amount=='') amount=0;
				var pcs=$('#pcs_'+i).val(); 							if(pcs=='') pcs=0;
				var colorsizetableid=$('#colorsizetableid_'+i).val(); 	if(colorsizetableid=='')colorsizetableid=0;
				var updateid=$('#updateid_'+i).val(); 					if(updateid=='') updateid=0;
				var reqqty=$('#reqqty_'+i).val(); 						if(reqqty=='') reqqty=0;
				var remarks=$('#remarks_'+i).val(); 					if(remarks=='') remarks=0;
				var poarticle=$('#poarticle_'+i).val(); 				if(poarticle=='') poarticle='no article';
				var hidRateCalStr=$('#hidRateCalStr_'+i).val(); 		if(hidRateCalStr=='') hidRateCalStr='0';

				if(cons_breck_down==""){
					cons_breck_down+=pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle+'_'+itemref+'_'+remarks+'_'+preitemcolor+'_'+preitemsizes+'_'+hidRateCalStr;
				}
				else{
					cons_breck_down+="__"+pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle+'_'+itemref+'_'+remarks+'_'+preitemcolor+'_'+preitemsizes+'_'+hidRateCalStr;
				}
			}
			document.getElementById('cons_breck_down').value=cons_breck_down;
			parent.emailwindow.hide();
		}

		function fnc_qty_blank()
		{
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			if(document.getElementById('chk_qty').checked==true)
			{
				document.getElementById('chk_qty').value=1;
				for(var i=1; i<=rowCount; i++){
					$('#qty_'+i).val('');
					calculate_requirement(i);
					calculate_amount(i);
				}
				set_sum_value( 'qty_sum', 'qty_' );
				set_sum_value( 'woqty_sum', 'woqny_' );
			}
			else if(document.getElementById('chk_qty').checked==false)
			{
				document.getElementById('chk_qty').value=0;
				for(var i=1; i<=rowCount; i++){
					$('#qty_'+i).val('');
					calculate_requirement(i);
					calculate_amount(i);
				}
				set_sum_value( 'qty_sum', 'qty_' );
				set_sum_value( 'woqty_sum', 'woqny_' );
			}
		}
	function fnc_ratecal_parameter(inc,item_group,rate_calculator_parameter)
	{
		var str_data=$('#hidRateCalStr_'+inc).val();
		var cbogroup=$('#txttrimgroup').val();
		var txthidden_job=$('#txtjobno').val();
		var cbonominasupplier=$('#supplier_name').val();
		var txtdescription=$('#des_'+inc).val();
		var cboconsuom=$('#txtuom').val();
		var page_link='revised_booking_report_controller.php?action=rate_calculator_parameter_popup';
		var title='Rate Cal Parameter';
		page_link=page_link+'&inc='+inc+'&item_group='+item_group+'&rate_calculator_parameter='+rate_calculator_parameter+'&rate_cal_data='+str_data+'&cbogroup='+cbogroup+'&cbonominasupplier='+cbonominasupplier+'&txtdescription='+txtdescription+'&cboconsuom='+cboconsuom+'&txthidden_job='+txthidden_job;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=350px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var rateValue=this.contentDoc.getElementById("txtfinalrate").value;
			var strData=this.contentDoc.getElementById("hidden_all_data").value;
			
			document.getElementById('rate_'+inc).value=trim(rateValue);
			document.getElementById('hidRateCalStr_'+inc).value=trim(strData);

			copy_value(strData,'hidRateCalStr_',inc);
			copy_value(rateValue,'rate_',inc);
		}
	}
	</script>
	</head>
	<body>
		<?
       extract($_REQUEST);
        if($txt_job_no==""){
			$txt_job_no_cond=""; $txt_job_no_cond1="";
        }
        else{
			$txt_job_no_cond ="and a.job_no='$txt_job_no'"; $txt_job_no_cond1 ="and job_no='$txt_job_no'";
        }
        if($txt_country=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";

        $process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
        $tot_po_qty=0;
        $sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); 
        foreach($sql_po_qty as $sql_po_qty_row){
			$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
			$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
        }
		
		 $sql_pre=sql_select("select d.wo_pre_cost_trim_cost_dtls_id as trims_dtls_id,d.color_size_table_id,d.item_ref,d.size_number_id,d.color_number_id,d.item_color_number_id as item_color  from  wo_pre_cost_trim_co_cons_dtls d where   d.po_break_down_id in($txt_po_id)   and d.status_active=1  "); 
        foreach($sql_pre as $row){
			if($row[csf('item_ref')]!='')
			{
			$item_ref_color_size_arr[$row[csf('trims_dtls_id')]][$row[csf('color_size_table_id')]]['item_ref']=$row[csf('item_ref')];
			$item_ref_color_arr[$row[csf('color_number_id')]]['item_ref'].=$row[csf('item_ref')].',';
			$item_ref_size_arr[$row[csf('size_number_id')]]['item_ref'].=$row[csf('item_ref')].',';
			}
        }
		
        ?>
        <div align="center" style="width:1250px;" >
            <fieldset>
                <form id="consumptionform_1" autocomplete="off">
                    <table width="1250" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                        <thead>
                        	<tr>
                                <th colspan="16" id="td_sync_msg" style="color:#FF0000"></th>
                            </tr>
                            <tr>
                                <th colspan="16">
                                    <input type="hidden" id="cons_breck_down" name="cons_breck_down" value="" />
                                    <input type="hidden" id="txtwoq" value="<?=$txt_req_quantity;?>"/>
                                    <input type="hidden" id="txtjobno" value="<?=$txt_job_no;?>"/>
                                    <input type="hidden" id="txttrimgroup" value="<?=$txt_trim_group_id;?>"/>
                                    <input type="hidden" id="supplier_name" value="<?=$cbo_supplier_name;?>"/>
                                    <input type="hidden" id="txtuom" value="<?=$txtuom;?>"/>
                                    <input type="hidden" id="cbo_company_name" value="<?=$cbo_company_name;?>"/>

                                    Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value);" value="<?=$txtwoq; ?>"/>
                                    <input type="radio" name="copy_basis" value="0" <? if(!$txt_update_dtls_id) { echo "checked";} ?>>Copy to All
                                    <input type="radio" name="copy_basis" value="1">Gmts Size Wise
                                    <input type="radio" name="copy_basis" value="2">Gmts Color Wise
                                    <input type="radio" name="copy_basis" value="3">Item Size Wise
                                    <input type="radio" name="copy_basis" value="10" <? if($txt_update_dtls_id) { echo "checked";} ?>>No Copy
									<input type="checkbox" name="round_down" id="round_down" value="" onClick="poportionate_qty();" >Round Down

                                    <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<?=$process_loss_method; ?>"/>
                                    <input type="hidden" id="po_qty" name="po_qty" value="<?=$tot_po_qty; ?>"/>
                                </th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th width="80">Article No</th>
                                <th width="90">Gmts. Color</th>
                                <th width="70">Gmts. Size</th>
                                <th width="100">Description</th>
                                <th width="100">Brand/Sup Ref</th>
                                <th width="100">Item Color</th>
                                <th width="100">Item Ref</th>
                                <th width="70">Item Size</th>
                                <th width="70">Wo Qty <input type="checkbox" id="chk_qty" name="chk_qty" value="0" onClick="fnc_qty_blank();"></th>
                                <th width="40">Excess %</th>
                                <th width="70">WO Qty.</th>
                                <th width="100">Rate</th>
                                <th width="100">Amount</th>
                                <th>RMG Qty</th>
								<th width="200">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
						//echo $txt_trim_group_id.'GGGGGGGGG'; //For Contrast
						  $item_color_sql=sql_select("select d.id,e.color_size_table_id,e.color_number_id,e.item_color_number_id   from wo_pre_cost_trim_cost_dtls d,wo_pre_cost_trim_co_cons_dtls e where  d.id=$cbo_trim_precost_id  and   d.id=e.wo_pre_cost_trim_cost_dtls_id  and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and e.item_color_number_id>0");
						     foreach($item_color_sql as $row)
							 {
							 	if($row[csf('item_color_number_id')])
								{
								$itemColorArr[$row[csf('color_number_id')]]=$row[csf('item_color_number_id')];
								}
							 }
							 unset($item_color_sql);
							 
                        $sql_lib_item_group_array=array();$conversion_factor=1;
                        $sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom, rate_cal_parameter from lib_item_group where id=$txt_trim_group_id");
                        foreach($sql_lib_item_group as $lrow){
							$sql_lib_item_group_array[$lrow[csf('id')]][item_name]=$lrow[csf('item_name')];
							$sql_lib_item_group_array[$lrow[csf('id')]][conversion_factor]=$lrow[csf('conversion_factor')];
							$sql_lib_item_group_array[$lrow[csf('id')]][cons_uom]=$lrow[csf('cons_uom')];
							$conversion_factor=$lrow[csf('conversion_factor')];
							$rate_calculator_parameter=$lrow[csf('rate_cal_parameter')];
                        }
						//echo $conversion_factor.'=';
						unset($sql_lib_item_group);

                        $booking_data_arr=array();
						if($txt_update_dtls_id=="") $txt_update_dtls_id=0;
                        $booking_data=sql_select("select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color,item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id,item_ref,remarks,bom_item_color,bom_item_size, rate_cal_data from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
						 

                        foreach($booking_data as $row){
							$booking_data_arr[$row[csf('color_size_table_id')]][id]=$row[csf('id')];
							$booking_data_arr[$row[csf('color_size_table_id')]][description]=$row[csf('description')];
							$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data]=$row[csf('rate_cal_data')];
							$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier]=$row[csf('brand_supplier')];
							$booking_data_arr[$row[csf('color_size_table_id')]][item_color]=$row[csf('item_color')];
							$booking_data_arr[$row[csf('color_size_table_id')]][item_ref]=$row[csf('item_ref')];
							$booking_data_arr[$row[csf('color_size_table_id')]][item_size]=$row[csf('item_size')];
							$booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color]=$row[csf('bom_item_color')];
							$booking_data_arr[$row[csf('color_size_table_id')]][bom_item_size]=$row[csf('bom_item_size')];
							$booking_data_arr[$row[csf('color_size_table_id')]][remarks]=$row[csf('remarks')];
							$booking_data_arr[$row[csf('color_size_table_id')]][cons]+=$row[csf('cons')];
							$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]=$row[csf('process_loss_percent')];
							$booking_data_arr[$row[csf('color_size_table_id')]][requirment]+=$row[csf('requirment')];
							$booking_data_arr[$row[csf('color_size_table_id')]][rate]=$row[csf('rate')];
							$booking_data_arr[$row[csf('color_size_table_id')]][amount]+=$row[csf('amount')];
                        }
						unset($booking_data);

                        $condition= new condition();
                        if(str_replace("'","",$txt_po_id) !=''){
							$condition->po_id("in($txt_po_id)");
                        }

                        $condition->init();
                        $trims= new trims($condition);
						//echo $trims->getQuery(); die;
						$piNumber=0;
						$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no='$txt_booking_no' and b.item_group='".$txt_trim_group_id."' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($pi_number){
							$piNumber=1;
						}
						$recvNumber=0;
						//$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and b.item_group_id='".$txt_trim_group_id."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b,order_wise_pro_details c"," a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=b.prod_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and  c.po_breakdown_id in($txt_po_id) and b.item_group_id='".$txt_trim_group_id."' and c.entry_form=24 and c.trans_type=1 and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0");
						if($recv_number){
							$recvNumber=1;
						}
						
                        $gmt_color_edb=""; $item_color_edb=""; $gmt_size_edb=""; $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="SELECT b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							//$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle();
							//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle();
							
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeItemsizeAndArticle();
							//print_r($req_qty_arr);die;
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeItemsizeAndArticle();
							 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,(e.item_size) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set, e.rate_cal_data from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.article_number, e.rate_cal_data,e.item_size order by b.id,size_order";
							
							$gmt_color_edb=1; $item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,min(e.item_color_number_id) as item_color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
							$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
							$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
							$sql="SELECT b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, e.item_size as item_size, sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,e.item_color_number_id, e.rate_cal_data from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number,e.item_color_number_id,e.item_size, e.rate_cal_data  order by b.id, color_order,size_order";
							//echo $sql; die;
                        }
                        else{
							$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
                        }

                        $po_color_level_data_arr=array(); $po_size_level_data_arr=array(); $po_no_sen_level_data_arr=array(); $po_color_size_level_data_arr=array();
						
						
                        $data_array=sql_select($sql);
						//echo $sql;
                        if ( count($data_array)>0)
						{
							$i=0;
							foreach( $data_array as $row ){
								$data=explode('_',$data_array_cons[$i]);
								$i++;
								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size=='0' || $item_size == "") $item_size=$row[csf('item_size')];

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="")$rate=$txt_avg_price;

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);
								if($row[csf('item_color_number_id')]=="" )  $row[csf('item_color_number_id')]="";
								if($booking_data_arr[$row[csf('color_size_table_id')]][cons]=="" )  $booking_data_arr[$row[csf('color_size_table_id')]][cons]="";
								if($booking_data_arr[$row[csf('color_size_table_id')]][requirment]=="" )  $booking_data_arr[$row[csf('color_size_table_id')]][requirment]="";
								if($booking_data_arr[$row[csf('color_size_table_id')]][amount]=="" )  $booking_data_arr[$row[csf('color_size_table_id')]][amount]="";
								$po_qty=$row[csf('order_quantity')];
								
								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 )
								{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									$txtreq_amount=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];

									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_amt'][$row[csf('id')]]=$txtreq_amount;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['item_color_number_id'][$row[csf('id')]]=$row[csf('item_color_number_id')];
									

									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];
								}
								else if($cbo_colorsizesensitive==2)
								{
									
									
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]];
									$txtreq_amount=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]];  
									 if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; //For zero value Json not work
									
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									
									//echo $txtwoq_cal.'='.$row[csf('color_size_table_id')].'='.$row[csf('item_size')].'=A<br> ';
									//echo "<pre>";
									if($txtwoq_cal>0)
									{
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									}
									if($txtreq_amount>0)
									{
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['req_amt'][$row[csf('id')]]=$txtreq_amount;
									}
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];

									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['article_number'][$row[csf('id')]]=$row[csf('article_number')];

									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
									if($rate_cal_data==''){
										$rate_cal_data=$row[csf('rate_cal_data')];
									}

								}
								else if($cbo_colorsizesensitive==4)
								{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
									$txtreq_amount=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									
									

									if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0') $row[csf('item_color_number_id')]=$row[csf('color_number_id')];

									if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; //For zero value Json not work

									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty'][$row[csf('id')]]+=$txtwoq_cal;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_amt'][$row[csf('id')]]+=$txtreq_amount;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['article_number'][$row[csf('id')]]=$row[csf('article_number')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['item_size'][$row[csf('id')]]=$row[csf('item_size')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['item_color_number_id'][$row[csf('id')]]=$row[csf('item_color_number_id')];

									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
									if($rate_cal_data==''){
										$rate_cal_data=$row[csf('rate_cal_data')];
									}

								}
								else if($cbo_colorsizesensitive==0)
								{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$txtreq_amount=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_amt'][$row[csf('id')]]=$txtreq_amount;
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];

								}
							}
                        }

                        if ( count($data_array)>0 && $cbo_level==1)
						{
							$i=0;
							foreach( $data_array as $row )
							{
								$data=explode('_',$data_array_cons[$i]);

								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
									$txtwoq_amt_cal=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
									$item_size="";
									//$pre_item_size="";
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if(empty($item_color)) $item_color = $row[csf('item_color_number_id')];
										
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									$item_color_id=$row[csf('item_color_number_id')];
									
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
								}
								else if($cbo_colorsizesensitive==2){
									$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]];
									$txtwoq_amt_cal = $req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]];
									if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; 
									$item_color ="";$pre_item_color ="";
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									
									 if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$row[csf('item_size')];
									
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
									if($rate_cal_data==''){
										$rate_cal_data=$row[csf('rate_cal_data')];
									}
								}
								else if($cbo_colorsizesensitive==4){
																		
									$item_color_id=$row[csf('item_color_number_id')];
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
									$txtwoq_amt_cal=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if(empty($item_color))
									{
										$item_color = $row[csf('item_color_number_id')];
										 
									}
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									
									if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; 
									
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									 if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$row[csf('item_size')];
									
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
									if($rate_cal_data==''){
										$rate_cal_data=$row[csf('rate_cal_data')];
									}
								}
								else if($cbo_colorsizesensitive==0){
									$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$txtwoq_amt_cal = $req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id];
									
									 $item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									 if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = '';
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									// echo $row[csf('color_size_table_id')].'='.$item_size.'d';
									if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size='';
									
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
								}
								$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
								$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="") $rate=($txtwoq_amt_cal/$txt_req_quantity)*$conversion_factor;//$rate=$txt_avg_price; 20-2047 by Aziz
								
								//$conversion_factor;
								//echo $txtwoq_amt_cal.'='.$txt_req_quantity.'f=';

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="")$brand_supplier=trim($txt_pre_brand_sup);
								
								$pre_item_ref=$item_ref_color_size_arr[$cbo_trim_precost_id][$row[csf('color_size_table_id')]]['item_ref'];
								//echo $pre_item_ref.'='.$row[csf('color_size_table_id')].',';
								$item_ref = $booking_data_arr[$row[csf('color_size_table_id')]][item_ref];
								if($item_ref) $item_ref=$item_ref;else $item_ref=$pre_item_ref;
								$remarks = $booking_data_arr[$row[csf('color_size_table_id')]][remarks];

								if($txtwoq_cal>0)
								{
									$i++;
									if($rate_calculator_parameter==2 || $rate_calculator_parameter==14) $rate_calculator_cond=" readonly onClick='fnc_ratecal_parameter($i,$txt_trim_group_id,$rate_calculator_parameter);'"; else $rate_calculator_cond="";
									if($cbo_colorsizesensitive==2 || $cbo_colorsizesensitive==4)
									{
										if($booking_item_size=='o') $booking_item_size='0';
										if($row[csf('item_size')]=='o') $row[csf('item_size')]='0'; //For Zero value not work in Json
									}
									?>
                                    <tr id="break_<? echo $i;?>" align="center">
                                        <td><? echo $i;?></td>
                                        <td><input type="text" id="poarticle_<? echo $i;?>" name="poarticle_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('article_number')]; ?>"  readonly /></td>
                                        <td>
                                            <input type="text" id="pocolor_<? echo $i;?>" name="pocolor_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="pocolorid_<? echo $i;?>" name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<? echo $i;?>" name="poid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('id')]; ?>" />
                                            <input type="hidden" id="poqty_<? echo $i;?>" name="poqty_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $po_qty_arr[$row[csf('id')]]; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<? echo $i;?>" name="poreqqty_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:60px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/></td>
                                        <td><input type="hidden" id="preitemcolor_<? echo $i;?>" value="<? echo $item_color_id; ?>" name="preitemcolor_<? echo $i;?>" class="text_boxes" style="width:90px"  />
                                        <input type="text" id="itemcolor_<? echo $i;?>" value="<? echo $color_library[$booking_item_color]; ?>" name="itemcolor_<? echo $i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)" <? if($item_color_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="itemref_<? echo $i;?>" value="<? echo $item_ref; ?>" name="itemref_<? echo $i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemref_',<? echo $i;?>)" <? if($piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        
                                        <td><input type="hidden" id="preitemsizes_<? echo $i;?>"  name="preitemsizes_<? echo $i;?>"    class="text_boxes" style="width:60px"   value="<? echo $row[csf('item_size')]; ?>" />
                                        <input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:60px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $booking_item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="hidden" id="reqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:60px"    value="<? echo $txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="qty_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:60px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/>
                                        </td>
                                        <td><input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:30px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/></td>
                                        <td><input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value('woqty_sum', 'woqny_')" onChange="set_sum_value('woqty_sum', 'woqny_')" name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:60px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly /></td>
                                        <td><input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:90px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} echo $rate_calculator_cond ?> />
										<input type="hidden" id="hidRateCalStr_<?= $i ?>" value="<?= $rate_cal_data ?>"></td>
                                        <td><input type="text" id="amount_<? echo $i;?>" name="amount_<? echo $i;?>" onBlur="set_sum_value( 'amount_sum', 'amount_' )" class="text_boxes_numeric" style="width:90px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly></td>
                                        <td>
                                            <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity_set')]; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:45px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:45px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
										<td><input type="text" id="remarks_<? echo $i;?>"  name="remarks_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $remarks;?>" onChange="copy_value(this.value,'remarks_',<? echo $i;?>)" />
                                            
                                        </td>
                                    </tr>
								<?
								}
							}
                        }

                        $level_arr=array(); $gmt_color_edb="";  $item_color_edb="";  $gmt_size_edb="";  $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,e.item_size as item_size,sum(c.order_quantity) as order_quantity, e.rate_cal_data from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id and e.cons>0 group by  c.size_number_id,c.article_number, e.rate_cal_data,e.item_size order by size_order";
							$level_arr=$po_size_level_data_arr;
							$gmt_color_edb=1; $item_color_edb=1;
							//echo $sql; die;
                        }
                        else if($cbo_colorsizesensitive==3){
							$sql="SELECT min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity, min(e.item_color_number_id) as item_color_number_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
						  $sql="select min(b.id) as id ,min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, e.item_size as item_size, e.item_color_number_id, sum(c.order_quantity) as order_quantity, e.rate_cal_data  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id, c.article_number,e.item_color_number_id,e.item_size, e.rate_cal_data order by  color_order,size_order,c.article_number";
						  //echo $sql; die;
							$level_arr=$po_color_size_level_data_arr;
                        }
                        else{
							$sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
							$level_arr=$po_no_sen_level_data_arr;
                        }
						
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0 && $cbo_level==2){
							$i=0;
							foreach( $data_array as $row ){
								if($cbo_colorsizesensitive==1){
									if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0')  $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_amt']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_amt']),8,"");
									
									$item_size="";$item_color_id="";
									//booking_item_size
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color=$booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if($item_color==0 || $item_color=="" ) $booking_item_color = $row[csf('color_number_id')];
									
									if($booking_item_color==0 || $booking_item_color=="" ) $booking_item_color = $row[csf('color_number_id')];
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0" || $booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									else $booking_item_color = $row[csf('item_color_number_id')];
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
									//$pre_item_color = "";
								}
								if($cbo_colorsizesensitive==2){
										
									 if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; //For Zero value not work in Json
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['req_qty']),5,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['req_amt']),5,"");
									//echo $txtwoq_amt_cal.'='.$txtwoq_cal.'='.$row[csf('item_size')].'=T <br> ';
								
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['booking_amt']),8,"");
									
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									
									//$booking_item_size=$booking_data_arr[$row[csf('color_size_table_id')]][bom_item_size];
									
									 if($item_size!="") $booking_item_size=$item_size;
									 
									if($item_size=="")  $booking_item_size=$row[csf('item_size')];
									 
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$size_library[$row[csf('size_number_id')]];
									
									$item_color = "";//$pre_item_color = "";$pre_item_size=$row[csf('item_size')];
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
									if($rate_cal_data==''){
										$rate_cal_data=$row[csf('rate_cal_data')];
									}
								}
								if($cbo_colorsizesensitive==3){
									$row[csf('item_color_number_id')]=$itemColorArr[$row[csf('color_number_id')]];
									if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0')  $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_amt']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_amt']),8,"");
									
									$item_size="";//$pre_item_size = "";
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
									
									//$pre_item_color = $row[csf('item_color_number_id')];
									
								}
								if($cbo_colorsizesensitive==4){
									$item_color_id=$row[csf('item_color_number_id')];
									if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0')  $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									 
									//if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="";
									if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; //For Zero value not work in Json
									
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty']),5,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_amt']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_amt']),8,"");
									//$item_color =0;
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									 
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									// echo $row[csf('color_size_table_id')].'='.$item_size.'d';
									if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$row[csf('item_size')];
									//if($item_size==0) $item_size='0';
									
									 
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$size_library[$row[csf('size_number_id')]];
									
									//if($row[csf('item_size')]==0) { echo $row[csf('item_size')]='0'; }
									
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
									if($rate_cal_data==''){
										$rate_cal_data=$row[csf('rate_cal_data')];
									}
								}
								if($cbo_colorsizesensitive==0){
									
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_qty']),5,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_amt']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_amt']),8,"");
									
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									 if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = '';
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									//echo $row[csf('color_size_table_id')].'='.$item_size.'d';
									if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size='';
									
									$item_ref = $booking_data_arr[$row[csf('color_size_table_id')]][item_ref];
									if($item_ref) $item_ref=$item_ref;else $item_ref='';
									$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]][rate_cal_data];
								} 

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								
								if(($rate*1)==0)
								{									
									if($booking_amt>0) $rate=$booking_amt/$booking_qty; else $rate=($txtwoq_amt_cal/$txtwoq_cal);
									$rate=$rate*$txtexchrate;
								}
								
								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);
								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);
								
								$pre_item_ref=$item_ref_color_size_arr[$cbo_trim_precost_id][$row[csf('color_size_table_id')]]['item_ref'];
								$item_ref = $booking_data_arr[$row[csf('color_size_table_id')]][item_ref];
								if($item_ref) $item_ref=$item_ref;else $item_ref=$pre_item_ref;
								$remarks = $booking_data_arr[$row[csf('color_size_table_id')]][remarks];
								
								
								 // echo $txtwoq_cal.'='.$row[csf('item_size')].'<br>';

								if($txtwoq_cal>0)
								{
									$i++;
									if($rate_calculator_parameter==2 || $rate_calculator_parameter==14) $rate_calculator_cond=" readonly onClick='fnc_ratecal_parameter($i,$txt_trim_group_id,$rate_calculator_parameter);'"; else $rate_calculator_cond="";
									if($cbo_colorsizesensitive==2 || $cbo_colorsizesensitive==4)
									{
									if($booking_item_size=='o') $booking_item_size='0'; //For Zero value not work in Json
									if($row[csf('item_size')]=='o') $row[csf('item_size')]='0';
									}
									?>
									<tr id="break_<?=$i; ?>" align="center">
                                        <td><?=$i; ?></td>
                                        <td><input type="text" id="poarticle_<?=$i; ?>" name="poarticle_<?=$i; ?>" class="text_boxes" style="width:70px" value="<?=$row[csf('article_number')]; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="pocolor_<?=$i; ?>" name="pocolor_<?=$i; ?>" class="text_boxes" style="width:80px" value="<?=$color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly />
                                            <input type="hidden" id="pocolorid_<?=$i; ?>" name="pocolorid_<?=$i; ?>" class="text_boxes" style="width:75px" value="<?=$row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<?=$i;?>" name="poid_<?=$i;?>" class="text_boxes" style="width:75px" value="<?=$row[csf('id')]; ?>" readonly />
                                            <input type="hidden" id="poqty_<?=$i;?>" name="poqty_<?=$i;?>" class="text_boxes" style="width:75px" value="<?=$po_qty; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<?=$i;?>" name="poreqqty_<?=$i;?>" class="text_boxes" style="width:75px" value="<?=$txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<?=$i;?>" name="gmtssizes_<?=$i;?>" class="text_boxes" style="width:60px" value="<?=$size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<?=$i;?>" name="gmtssizesid_<?=$i;?>" class="text_boxes" style="width:60px" value="<?=$row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<?=$i;?>" name="des_<?=$i;?>" class="text_boxes" style="width:90px" value="<?=$description;?>" onChange="copy_value(this.value,'des_',<?=$i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="brndsup_<?=$i;?>" name="brndsup_<?=$i;?>" class="text_boxes" style="width:90px" value="<?=$brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<?=$i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="hidden" id="preitemcolor_<?=$i;?>" value="<?=$row[csf('item_color_number_id')]; ?>" name="preitemcolor_<?=$i;?>" class="text_boxes" style="width:90px"  />
                                        <input type="text" id="itemcolor_<?=$i;?>" value="<?=$color_library[$booking_item_color]; ?>" name="itemcolor_<?=$i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemcolor_',<?=$i;?>)" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                         <td><input type="text" id="itemref_<?=$i;?>" value="<?=$item_ref; ?>" name="itemref_<?=$i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemref_',<?=$i;?>);" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                         
                                      <td><input type="hidden" id="preitemsizes_<?=$i;?>" name="preitemsizes_<?=$i;?>" class="text_boxes" style="width:60px"   value="<?=$row[csf('item_size')]; ?>" />
                                        
                                        <input type="text" id="itemsizes_<?=$i;?>" name="itemsizes_<?=$i;?>" class="text_boxes" style="width:60px" onChange="copy_value(this.value,'itemsizes_',<?=$i;?>);" value="<?=$booking_item_size; ?>"  <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/>
                                        </td>
                                        <td><input type="hidden" id="reqqty_<?=$i;?>" name="reqqty_<?=$i;?>" class="text_boxes_numeric" style="width:60px" value="<?=$txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="qty_<?=$i;?>" onBlur="validate_sum(<?=$i; ?>);" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<?=$i;?>);copy_value(this.value,'qty_',<?=$i;?>);" name="qty_<?=$i;?>" class="text_boxes_numeric" style="width:60px" placeholder="<?=$txtwoq_cal; ?>" value="<? if($booking_cons>0){echo $booking_cons;} ?>"/>
                                        </td>
                                        <td>
                                        	<input type="text" id="excess_<?=$i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_');" name="excess_<?=$i;?>" class="text_boxes_numeric" style="width:30px" onChange="calculate_requirement(<?=$i;?>);set_sum_value( 'excess_sum', 'excess_');set_sum_value( 'woqty_sum', 'woqny_'); copy_value(this.value,'excess_',<?=$i;?>);" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/>
                                        </td>
                                        <td><input type="text" id="woqny_<?=$i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_');" onChange="set_sum_value('woqty_sum', 'woqny_');"  name="woqny_<?=$i;?>" class="text_boxes_numeric" style="width:60px" value="<? if($booking_qty){echo $booking_qty;} ?>" readonly />
                                        </td>
                                        <td><input type="text" id="rate_<?=$i;?>" name="rate_<?=$i;?>" class="text_boxes_numeric" style="width:90px" onChange="calculate_amount(<?=$i;?>); set_sum_value('amount_sum', 'amount_'); copy_value(this.value,'rate_',<?=$i;?>);" value="<?=fn_number_format($rate,6); ?>" <? if( $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} echo $rate_calculator_cond?> />
										<input type="hidden" id="hidRateCalStr_<?=$i?>" value="<?= $rate_cal_data ?>" />
                                        </td>
                                        <td><input type="text" id="amount_<?=$i;?>" name="amount_<?=$i;?>" onBlur="set_sum_value('amount_sum', 'amount_');" class="text_boxes_numeric" style="width:90px" value="<?=$booking_amt; //$booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly>
                                        </td>

                                        <td><input type="text" id="pcs_<?=$i;?>" name="pcs_<?=$i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' );" class="text_boxes_numeric" style="width:50px"  value="<?=$row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<?=$i;?>" name="pcsset_<?=$i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_') " class="text_boxes_numeric" style="width:50px" value="<?=$order_quantity_set; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<?=$i;?>" name="colorsizetableid_<?=$i;?>" class="text_boxes" style="width:45px" value="<?=$row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<?=$i;?>" name="updateid_<?=$i;?>" class="text_boxes" style="width:45px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
										<td><input type="text" id="remarks_<?=$i;?>" name="remarks_<?=$i;?>" class="text_boxes" style="width:90px" value="<?=$remarks;?>" onChange="copy_value(this.value,'remarks_',<?=$i;?>);" /></td>
                                        <?
										if($cbo_colorsizesensitive==0)
										{
										?>
                                        
                                        <?
										}
										?>
									</tr>
								<?
								}
							}
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                               <th width="30">&nbsp;</th>
                               <th width="80">&nbsp;</th>
                               <th width="90">&nbsp;</th>
                               <th width="70">&nbsp;</th>
                               <th width="100">&nbsp;</th>
                               <th width="100">&nbsp;</th>
                               <th width="100">&nbsp;</th>
                               <th width="100">&nbsp;</th>
                               <th width="70">&nbsp;</th>
                               <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:60px"  readonly></th>
                               <th width="40"><input type="text" id="excess_sum"  name="excess_sum" class="text_boxes_numeric" style="width:30px" readonly></th>
                               <th width="70"><input type="text" id="woqty_sum"  name="woqty_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                               <th width="100"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:90px" readonly></th>
                               <th width="100"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:90px" readonly></th>
                               <th><input type="hidden" id="json_data" name="
                               " class="text_boxes_numeric" style="width:50px" value='<?=json_encode($level_arr); ?>' readonly>
                                	<input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly>
                               </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table width="1250" cellspacing="0" class="" border="0" rules="all">
                        <tr>
                            <td align="center" width="100%"> <input type="button" class="formbutton" value="Close" onClick="js_set_value();"/> </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
        </div>
	</body>
	<script>
		$("input[type=text]").focus(function() {
		   $(this).select();
		});
		<?
		if($txt_update_dtls_id==""){
			?>
			poportionate_qty(<?=$txtwoq; ?>);
			<?
		}
		?>
		set_sum_value( 'qty_sum', 'qty_' );
		set_sum_value( 'woqty_sum', 'woqny_' );
		set_sum_value( 'amount_sum', 'amount_' );
		set_sum_value( 'pcs_sum', 'pcs_' );
		calculate_avg_rate();
		var wo_qty=$('#txtwoq_qty').val()*1;

		var wo_qty_sum=$('#qty_sum').val()*1;
		//console.log(wo_qty+'--'+wo_qty_sum);
		//if(wo_qty!=wo_qty_sum)
		if((wo_qty-wo_qty_sum)>1)
		{
			$('#td_sync_msg').html("Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.");
		}
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
    exit();
}
if($action=="rate_calculator_parameter_popup")
{
	echo load_html_head_contents("Country","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var rate_calculator_parameter = '<? echo $rate_calculator_parameter; ?>';

		function fnc_totRateCal()
		{
			var all_data="";

			if(rate_calculator_parameter==2)
			{
				all_data=$('#txtlength_1').val()+'~~'+$('#txtwidth_1').val()+'~~'+$('#txtheight_1').val()+'~~'+$('#txtrate_1').val()+'~~'+$('#txtcartonrate_1').val()+'~~'+$('#txtlwall_1').val()+'~~'+$('#txthwall_1').val();
				$('#hidden_all_data').val(all_data);
				var finalRate=0;
				var ply7_1_actual_ctn=($('#txtlength_1').val()*1)+($('#txtwidth_1').val()*1)+$('#txtlwall_1').val()*1;
				var ply7_2_actual_ctn=($('#txtwidth_1').val()*1)+($('#txtheight_1').val()*1)+$('#txthwall_1').val()*1;
				var ctn7Rate=((((ply7_1_actual_ctn*1)*(ply7_2_actual_ctn*1))/5000)*($('#txtrate_1').val()*1));
				$('#txtcartonrate_1').val( number_format(ctn7Rate,4) );
				$('#txtfinalrate').val( number_format(ctn7Rate,4) );
			}
			else if(rate_calculator_parameter==14)
			{
				all_data=$('#txtlength_1').val()+'~~'+$('#txtwidth_1').val()+'~~'+$('#txtheight_1').val()+'~~'+$('#txtrate_1').val()+'~~'+$('#txtcartonrate_1').val();
				$('#hidden_all_data').val(all_data);
				var finalRate=0;
				var ply7_2_actual_ctn=($('#txtlength_1').val()*1)*($('#txtwidth_1').val()*1);
				var ctn7Rate=(((ply7_2_actual_ctn*1)/10000)*($('#txtrate_1').val()*1));
				$('#txtcartonrate_1').val( number_format(ctn7Rate,4) );
				$('#txtfinalrate').val( number_format(ctn7Rate,4) );
			}
		}

		function js_set_value()
		{
			fnc_totRateCal();
			document.getElementById('txtfinalrate').value;
			document.getElementById('hidden_all_data').value;
			var description = $('#txtlength_1').val()+'x'+$('#txtwidth_1').val()+'x'+$('#txtheight_1').val();
			var rate_description = $('#hidden_rate_des').val();
			$('#hidden_description').val(rate_description +'@'+description);
			parent.emailwindow.hide();
		}

		function fnc_assign_all_data()
		{
			var all_data=$('#hidden_all_data').val();
			var exData=all_data.split("~~");
			if(rate_calculator_parameter==2)
			{
				$('#txtlength_1').val(exData[0]);
				$('#txtwidth_1').val(exData[1]);
				$('#txtheight_1').val(exData[2]);
				$('#txtrate_1').val(exData[3]);
				$('#txtcartonrate_1').val(exData[4]);
				$('#txtlwall_1').val(exData[5]);
				$('#txthwall_1').val(exData[6]);			
				//$('#txtfinalrate').val(exData[10]);
			}
			else if(rate_calculator_parameter==14)
			{
				$('#txtlength_1').val(exData[0]);
				$('#txtwidth_1').val(exData[1]);
				$('#txtheight_1').val(exData[2]);
				$('#txtrate_1').val(exData[3]);
				$('#txtcartonrate_1').val(exData[4]);
			}
		}
		function supp_trim_rate_popup(i)
		{
			var cbogroup=document.getElementById('cbogroup').value;
			var txtdescription=document.getElementById('txtdescription').value;
			var cbonominasupplier=document.getElementById('cbonominasupplier').value;
			var page_link="pre_cost_entry_controller_v2.php?cbogroup="+trim(cbogroup)+"&txtdescription="+trim(txtdescription)+"&cbonominasupplier="+trim(cbonominasupplier)+"&action=supp_trim_rate_popup_page";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Rate', 'width=1000px,top=0px,height=400px,center=1,resize=1,scrolling=0;','../../')
			emailwindow.onclose=function(){
				var txt_selected_supllier=this.contentDoc.getElementById("txt_selected_supllier");
				var txt_selected_rate=this.contentDoc.getElementById("txt_selected_rate");
				var txt_selected_description=this.contentDoc.getElementById("txt_selected_description");
				var txt_uom=this.contentDoc.getElementById("txt_uom");
				document.getElementById('txtrate_'+i).value=txt_selected_rate.value;
				document.getElementById('hidden_rate_des').value=txt_selected_description.value;
				document.getElementById('hidden_carton_supplier').value=txt_selected_supllier.value;
				fnc_totRateCal()
			}
		}

	</script>
	</head>
    <body>
    <div align="center">
    <input type="hidden" name="hidden_all_data" id="hidden_all_data" value=""/>
    <input type="hidden" name="hidden_description" id="hidden_description" value=""/>
    <input type="hidden" name="hidden_rate_des" id="hidden_rate_des" value=""/>
    <input type="hidden" name="hidden_carton_supplier" id="hidden_carton_supplier" value=""/>
    <input type="hidden" name="txtfinalrate" id="txtfinalrate" value=""/>
    <form>
		<?
		$company_name = return_field_value("company_name", "wo_po_details_master", "job_no='$txthidden_job'");
		$country_library=return_library_array("select id,country_name from lib_country","id","country_name");
		$trim_variable=1;
		$trim_variable_sql=sql_select("select trim_rate from  variable_order_tracking where company_name='$company_name' and variable_list=35 order by id");
		foreach($trim_variable_sql as $trim_variable_row)
		{
			$trim_variable=	$trim_variable_row[csf('trim_rate')];
		}
		if($trim_variable==1 || $trim_variable==0)
		{
			$trim_rate_popup="";
		}
		else
		{
			$trim_rate_popup="  onClick='supp_trim_rate_popup(1)' placeholder='Click' readonly";//onClick="supp_trim_rate_popup(1)" readonly
		}

		$item_groupArr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name");

		if($rate_calculator_parameter==2)
		{
			$rate_cal=explode('~~',$rate_cal_data);
			if($rate_cal[5]==''){
				$rate_cal[5]=6;
			}
			if($rate_cal[6]==''){
				$rate_cal[6]=3;
			}
			?>
			<table width="500" cellspacing="0" class="rpt_table" border="1" id="tbl_list_search" rules="all">
				<thead>
					<tr>
						<th colspan="6"><? echo $item_groupArr[$item_group]; ?> (Square Mtr)</th>
						<th colspan="2">Measurement in CM</th>
					</tr>
					<tr>
						<th width="130">Details</th>
						<th width="80">L</th>
						<th width="80">W</th>
						<th width="80">H</th>
						<th width="80">LW All.</th>
						<th width="80">HW All.</th>
						<th width="80">Rate ($)</th>
						<th title="((((L+W+LW All.)+(W+H+HW All.))/5000)*Rate ($))">Carton Rate</th>
						<input type="hidden" id="cbogroup" name="cbogroup" value="<? echo $cbogroup ?>">
						<input type="hidden" id="cbonominasupplier" name="cbonominasupplier" value="<? echo $supplier ?>">
						<input type="hidden" id="txtdescription" name="txtdescription" value="<? echo $txtdescription ?>">
						<input type="hidden" id="cboconsuom" name="cboconsuom" value="<? echo $cboconsuom ?>">
					</tr>
				</thead>
				<tr>
					<td><?= $txtdescription ?></td>
					<td><input type="text" name="txtlength_1" id="txtlength_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[0] ?>" onBlur="fnc_totRateCal();"/></td>
					<td><input type="text" name="txtwidth_1" id="txtwidth_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[1] ?>" onBlur="fnc_totRateCal();"/></td>
					<td><input type="text" name="txtheight_1" id="txtheight_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[2] ?>" onBlur="fnc_totRateCal();"/></td>
					<td><input type="text" name="txtlwall_1" id="txtlwall_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[5] ?>" onBlur="fnc_totRateCal();"/></td>
					<td><input type="text" name="txthwall_1" id="txthwall_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[6] ?>" onBlur="fnc_totRateCal();"/></td>
					<td><input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[3] ?>"  onBlur="fnc_totRateCal();" <? //echo $trim_rate_popup;?> /></td>
					<td><input type="text" name="txtcartonrate_1" id="txtcartonrate_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[4] ?>" onBlur="fnc_totRateCal();"/></td>
				</tr>
			</table>
			<br>
			<table width="500" cellspacing="0" class="" border="0" align="center">
				<tr>
					<td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value()"/></td>
				</tr>
			</table>
			<?
		}
		else if ($rate_calculator_parameter==14)
		{
			$rate_cal=explode('~~',$rate_cal_data);
			?>
			<table width="500" cellspacing="0" class="rpt_table" border="1" id="tbl_list_search" rules="all">
				<thead>
					<tr>
						<th colspan="4"><? echo $item_groupArr[$item_group]; ?> (Square Mtr)</th>
						<th colspan="2">Measurement in CM</th>
					</tr>
					<tr>
						<th width="130">Details</th>
						<th width="80">L</th>
						<th width="80">W</th>
						<th width="80">Rate ($)</th>
						<th title="(((L*W)/10000)*Rate ($))">Carton Rate</th>
						<input type="hidden" id="cbogroup" name="cbogroup" value="<? echo $cbogroup ?>">
						<input type="hidden" id="cbonominasupplier" name="cbonominasupplier" value="<? echo $supplier ?>">
						<input type="hidden" id="txtdescription" name="txtdescription" value="<? echo $txtdescription ?>">
						<input type="hidden" id="cboconsuom" name="cboconsuom" value="<? echo $cboconsuom ?>">
					</tr>
				</thead>
				<tr>
					<td><?= $txtdescription ?></td>
					<td><input type="text" name="txtlength_1" id="txtlength_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[0] ?>" onBlur="fnc_totRateCal();"/></td>
					<td><input type="text" name="txtwidth_1" id="txtwidth_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[1] ?>" onBlur="fnc_totRateCal();"/><input type="hidden" name="txtheight_1" id="txtheight_1" value="<? echo $rate_cal[2] ?>"/></td>
					<td><input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[3] ?>"  onBlur="fnc_totRateCal();" /></td>
					<td><input type="text" name="txtcartonrate_1" id="txtcartonrate_1" class="text_boxes_numeric" style="width:70px;" value="<? echo $rate_cal[4] ?>" onBlur="fnc_totRateCal();"/></td>
				</tr>
			</table>
			<br>
			<table width="500" cellspacing="0" class="" border="0" align="center">
				<tr>
					<td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value()"/></td>
				</tr>
			</table>
			<?
		}
		?>
        </table>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="set_cons_break_down"){
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size",'id','size_name');
	$data=explode("_",$data);
	$garments_nature=$data[0];
	$cbo_company_name=$data[1];
	$txt_job_no=$data[2];
	$txt_po_id=$data[3];
	$cbo_trim_precost_id=$data[4];
	$txt_trim_group_id=$data[5];
	$txt_update_dtls_id=trim($data[6]);
	$cbo_colorsizesensitive=$data[7];
	$txt_req_quantity=$data[8];
	$txt_avg_price=$data[9];
	$txt_country=$data[10];
	$txt_pre_des=$data[11];
	$txt_pre_brand_sup=$data[12];
	$cbo_level=$data[13];

	if($txt_job_no==""){
		$txt_job_no_cond=""; $txt_job_no_cond1="";
	}
	else{
		$txt_job_no_cond ="and a.job_no='$txt_job_no'"; $txt_job_no_cond1 ="and job_no='$txt_job_no'";
	}

	if($txt_country=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
	$sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
	$tot_po_qty=0;
	foreach($sql_po_qty as $sql_po_qty_row){
		$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
		$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
	}
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}
	if($txt_update_dtls_id=="" || $txt_update_dtls_id==0) $txt_update_dtls_id=0;else $txt_update_dtls_id=$txt_update_dtls_id;
	$booking_data_arr=array();
	$booking_data=sql_select("select id,wo_trim_booking_dtls_id,description,brand_supplier,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id,item_ref,bom_item_color,bom_item_size,rate_cal_data  from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
	foreach($booking_data as $booking_data_row){
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][id]=$booking_data_row[csf('id')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][description]=$booking_data_row[csf('description')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][brand_supplier]=$booking_data_row[csf('brand_supplier')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_color]=$booking_data_row[csf('item_color')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_ref]=$booking_data_row[csf('item_ref')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_size]=$booking_data_row[csf('item_size')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][book_item_color]=$booking_data_row[csf('bom_item_color')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][book_item_size]=$booking_data_row[csf('bom_item_size')];
		
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][cons]+=$booking_data_row[csf('cons')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][process_loss_percent]=$booking_data_row[csf('process_loss_percent')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][requirment]+=$booking_data_row[csf('requirment')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][rate]=$booking_data_row[csf('rate')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][amount]+=$booking_data_row[csf('amount')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][rate_cal_data]=$booking_data_row[csf('rate_cal_data')];
	}
	 $sql_pre=sql_select("select d.wo_pre_cost_trim_cost_dtls_id as trims_dtls_id,d.color_size_table_id,d.item_ref,d.size_number_id,d.color_number_id,d.item_color_number_id as item_color  from  wo_pre_cost_trim_co_cons_dtls d where   d.po_break_down_id in($txt_po_id)   and d.status_active=1  "); //,c.item_number_id
		// echo "select d.color_size_table_id,d.item_ref  from  wo_pre_cost_trim_co_cons_dtls d where   d.po_break_down_id in($txt_po_id)   and d.status_active=1  ";
        foreach($sql_pre as $row){
			if($row[csf('item_ref')]!='')
			{
			$item_ref_color_size_arr[$row[csf('trims_dtls_id')]][$row[csf('color_size_table_id')]]['item_ref']=$row[csf('item_ref')];
			$item_ref_color_arr[$row[csf('trims_dtls_id')]][$row[csf('color_number_id')]]['item_ref'].=$row[csf('item_ref')].',';
			$item_ref_size_arr[$row[csf('trims_dtls_id')]][$row[csf('size_number_id')]]['item_ref'].=$row[csf('item_ref')].',';
			}
			 
        }
		unset($sql_pre);
		
		$cu_booking_data_arr=array();
	$cu_booking_data=sql_select("select a.pre_cost_fabric_cost_dtls_id,b.id,b.wo_trim_booking_dtls_id,b.po_break_down_id,b.color_number_id,b.gmts_sizes,b.item_size,b.requirment,b.article_number  from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and b.po_break_down_id in($txt_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id not in($txt_update_dtls_id)");
	foreach($cu_booking_data as $cu_booking_data_row){
		if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==2 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('item_size')]][$cu_booking_data_row[csf('article_number')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==4 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('article_number')]][$cu_booking_data_row[csf('item_color')]][$cu_booking_data_row[csf('item_size')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==0 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
	}

	$condition= new condition();
	if(str_replace("'","",$txt_po_id) !=''){
			$condition->po_id("in($txt_po_id)");
	}

	$condition->init();
	$trims= new trims($condition);
	$gmt_color_edb="";
	$item_color_edb="";
	$gmt_size_edb="";
	$item_size_edb="";
	if($cbo_colorsizesensitive==1){
		$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
	    //$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtssize();
		//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtssize();
		//$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle();
		//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle();
		$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeItemsizeAndArticle();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeItemsizeAndArticle();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,e.item_size as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,e.item_size,c.article_number order by b.id,size_order";
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3){
		$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,min(e.item_color_number_id) as item_color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){

		//$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
		//$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();

		// $req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
		// $req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();


		 $req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
		 $req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();



		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,e.item_size as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,e.item_color_number_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number,e.item_color_number_id,e.item_size  order by b.id, color_order,size_order";
	}
	else{
		$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	    $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
	}

	$data_array=sql_select($sql);
	if ( count($data_array)>0)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			$po_qty=$row[csf('order_quantity')];
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="") $color_number_id=0;

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="") $size_number_id=0;

			$description=$txt_pre_des;
			if($description=="") $description=0;

			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="") $brand_supplier=0;

			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="") $item_color=0;

			$item_size=$row[csf('item_size')];
			if($item_size=="") $item_size=0;
			$excess=0;
			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';

			if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
				$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]],5,"");
				//$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				//$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount = def_number_format($txtwoq_cal*$txt_avg_price,5,"");

				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

				if($cbo_colorsizesensitive==3)
				{
					$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['item_color_number_id'][$row[csf('id')]]=$row[csf('item_color_number_id')];
				}

				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==2){
				
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]],5,"");
				 if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; //For Zero value not work in Json
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				
				//$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==4){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]],5,"");
				//$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				if($txtwoq_cal) 
				{
				//if($row[csf('item_size')]=='' || $row[csf('item_size')]=='0') $row[csf('item_size')]=$size_library[$row[csf('size_number_id')]];
				//if($row[csf('item_size')]==0 )  $row[csf('item_size')]="";
				if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; //For Zero value not work in Json
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
				if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0') $row[csf('item_color_number_id')]=$row[csf('color_number_id')];

				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];

				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['item_color_number_id'][$row[csf('id')]]=$row[csf('item_color_number_id')];

				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['amount'][$row[csf('id')]]=$amount;
				}
			}
			else if($cbo_colorsizesensitive==0){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id],5,"");
				//$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['amount'][$row[csf('id')]]=$amount;
			}
		}
	}

	$cons_breck_down="";
	if ( count($data_array)>0 && $cbo_level==1)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="") $color_number_id=0;

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="") $size_number_id=0;

			$description=$txt_pre_des;
			if($description=="") $description=0;

			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="") $brand_supplier=0;
			if($cbo_colorsizesensitive==3 || $cbo_colorsizesensitive==4)
			{
				$item_color=$color_library[$row[csf('item_color_number_id')]];
			}
			else
			{
				$item_color=$color_library[$row[csf('color_number_id')]];
			}
			if($item_color=="") $item_color=0;
			
			$pre_item_color=$row[csf('item_color_number_id')];
			$pre_item_size=$row[csf('item_size')];
			
			if($pre_item_color=="") $pre_item_color=0;
			if($pre_item_size=="") $pre_item_size=0;

			$item_size=$row[csf('item_size')];
			
			$item_ref=$item_ref_color_size_arr[$cbo_trim_precost_id][$row[csf('color_size_table_id')]]['item_ref'];
			if($item_ref=="") $item_ref=0;
			$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]]['rate_cal_data'];
			if($rate_cal_data=="") $rate_cal_data=0;

			if($item_size=="") $item_size=0;
			$excess=0;

			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';

			$remarks=$row[csf('remarks')];
			if($remarks=="") $remarks=0;

			if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}

			else if($cbo_colorsizesensitive==2){
				 if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; //For Zero value not work in Json
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==4){
				if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; //For Zero value not work in Json
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==0){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			$remark=0;
			if($txtwoq_cal>0){
				if($cons_breck_down=="")
				{
					$cons_breck_down.=$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber."_".$item_ref."_".$remark."_".$pre_item_color."_".$pre_item_size."_".$rate_cal_data;
				}
				else
				{
					$cons_breck_down.="__".$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber."_".$item_ref."_".$remark."_".$pre_item_color."_".$pre_item_size."_".$rate_cal_data;
				}
			}
		}
		echo $cons_breck_down;
	}

	$level_arr=array();
	$gmt_color_edb="";
	$item_color_edb="";
	$gmt_size_edb="";
	$item_size_edb="";
	if($cbo_colorsizesensitive==1){
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,e.item_size as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id,e.item_size,c.article_number order by size_order";
		$level_arr=$po_size_level_data_arr;
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3){
		$sql="select min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity,min(e.item_color_number_id) as item_color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){
		$sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,e.item_size as item_size,sum(c.order_quantity) as order_quantity,e.item_color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id,c.article_number,e.item_color_number_id,e.item_size  order by  color_order,size_order";
		$level_arr=$po_color_size_level_data_arr;
	}
	else{
		  $sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
		$level_arr=$po_no_sen_level_data_arr;
	}
	$data_array=sql_select($sql);

	$cons_breck_down="";
	if ( count($data_array)>0 && $cbo_level==2)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			if($row[csf('item_size')]=='' || $row[csf('item_size')]=='0') $row[csf('item_size')]=$size_library[$row[csf('size_number_id')]];
			if($cbo_colorsizesensitive==1){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
				$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==2){
				 if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; //For Zero value not work in Json
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('item_size')]][$row[csf('article_number')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==3){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==4){
				
				if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0') $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
				//if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="";
				if($row[csf('item_size')]=='0' )  $row[csf('item_size')]="o"; //For Zero value not work in Json
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==0){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="") $color_number_id=0;

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="") $size_number_id=0;

			$description=$txt_pre_des;
			if($description=="") $description=0;

			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="") $brand_supplier=0;


			if($cbo_colorsizesensitive==3 || $cbo_colorsizesensitive==4)
			{
				$item_color=$color_library[$row[csf('item_color_number_id')]];
			}
			else
			{
				$item_color=$color_library[$row[csf('color_number_id')]];
			}

			if($item_color=="") $item_color=0;
			
			//if($item_color>0) $booking_item_color=$item_color;
			//else $booking_item_color = $row[csf('item_color_number_id')];
									
									//if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									 
									 
			$pre_item_color=$row[csf('item_color_number_id')];
			$pre_item_size=$row[csf('item_size')];
			 
			if($pre_item_color=="") $pre_item_color=0;
			if($pre_item_size=="") $pre_item_size=0;

			 
			
			$item_ref=$item_ref_color_size_arr[$cbo_trim_precost_id][$row[csf('color_size_table_id')]]['item_ref'];
			if($item_ref=="") $item_ref=0;
			$rate_cal_data=$booking_data_arr[$row[csf('color_size_table_id')]]['rate_cal_data'];
			if($rate_cal_data=="") $rate_cal_data=0;
			

			$item_size=$row[csf('item_size')];
			if($item_size=="") $item_size=0;
			$excess=0;

			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';
$remark=0;
			if($txtwoq_cal>0){
				if($cons_breck_down==""){
					$cons_breck_down.=trim($color_number_id).'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber."_".$item_ref."_".$remark."_".$pre_item_color."_".$pre_item_size."_".$rate_cal_data;

				}
				else{
					$cons_breck_down.="__".trim($color_number_id).'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber."_".$item_ref."_".$remark."_".$pre_item_color."_".$pre_item_size."_".$rate_cal_data;
				}
			}
		}
		//echo $cons_breck_down;die;
		echo $cons_breck_down."**".json_encode($level_arr); 
	}
	exit();
}
if($action=="show_revised_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$path=str_replace("'","",$path);
	if($path==1) $path="../../";

	$txt_system_no=str_replace("'","",$txt_system_no);
	$txt_order_no_id=str_replace("'","",$txt_selected_po);
	$txt_select_item=str_replace("'","",$txt_select_item);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

	$season_name_arr=return_library_array( "select id,season_name from lib_buyer_season",'id','season_name');
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$size_library=return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	$user_name_arr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1330px" align="center">
    <?php

		$dtlsTable_qry=sql_select( "SELECT b.id,c.booking_no,c.job_no, b.system_no,b.po_break_down_id,b.color_type,b.construction,b.pre_cost_fabric_cost_dtls_id,b.copmposition,b.color_size_table_id,b.precons,b.gsm_weight,b.dia_width,b.gmts_color_id,b.fabric_color_id,b.fin_fab_qnty,b.booking_mst_id from wo_booking_revised_dtls b,wo_booking_dtls c where  b.booking_mst_id = c.booking_mst_id AND b.system_no = '$txt_system_no' ");
		$txt_job_no=$dtlsTable_qry[0][csf('job_no')];

		if($txt_job_no!="") $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'"); else $location="";
		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}		
		$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_no='$txt_job_no'  and status_active=1 and is_deleted=0  order by id");
		
		$peach=''; $brush=''; $fab_wash='';

		$emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_no='$txt_job_no' and status_active=1 and is_deleted=0 and cons_dzn_gmts>0 and emb_name in (1,2,3) order by id");
		
		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}


		$nameArray=sql_select( "SELECT c.system_no,a.supplier_id,  a.fabric_source, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id,  b.style_description,b.season_buyer_wise as season, b.product_dept,c.revised_no,c.fab_delivery_date,c.revised_date,c.revised_reason,c.part_no  from wo_booking_mst a, wo_po_details_master b,wo_booking_revised_mst c where c.system_no='$txt_system_no' and a.job_no=b.job_no and a.job_no='$txt_job_no' AND a.is_short=2 and a.booking_type=1 AND c.po_break_down_id in ($txt_selected_po) group by c.system_no,a.supplier_id,  a.fabric_source, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id,  b.style_description,b.season_buyer_wise, b.product_dept,c.revised_no,c.fab_delivery_date,c.revised_date,c.revised_reason,c.part_no");

		$job_no_str=$nameArray[0][csf('job_no')];
		
		$job_yes_no=sql_select("select id, job_id,job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq,bush,bushseq,peach,peachseq,yd,ydseq from wo_po_details_mas_set_details where job_no='$txt_job_no'");

		$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$txt_order_no_id.") order by shipment_date asc ");
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }

		$po_sql=sql_select("select a.id, a.po_number, sum(b.order_quantity) as poqtypcs from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.id in(".$txt_order_no_id.") and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number");
		foreach($po_sql as $row)
        {
            $po_qnty_tot1+=$row[csf('poqtypcs')];
		}

		$sum_fin_fabqnty=sql_select("SELECT  sum(d.fin_fab_qnty) as qty	FROM wo_pre_cost_fabric_cost_dtls a join wo_pre_cos_fab_co_avg_con_dtls b on  a.id=b.pre_cost_fabric_cost_dtls_id join wo_po_color_size_breakdown c on c.id=b.color_size_table_id join wo_booking_dtls d on b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id WHERE  d.po_break_down_id in (".$txt_order_no_id.") and a.uom=12 and d.status_active=1 and  d.is_deleted=0");

		foreach($sum_fin_fabqnty as $row)
        {
            $fin_fabqnty+=$row[csf('qty')];
		}
		
        $po_running_cancel= sql_select("select case when status_active=1 then  PO_NUMBER end as running_po, case when status_active>1 then po_number end as cancel_po,po_quantity from wo_po_break_down  where id in(".$txt_order_no_id.") order by shipment_date asc ");
        $running_po='';
        $cancel_po='';
        $running_po_qnty=0;
        foreach ($po_running_cancel as $row) {
        	if(!empty($row[csf('running_po')]))
        	{
        		if(!empty($running_po))
        		{
        			$running_po.=",".$row[csf('running_po')];
        		}
        		else{
        			$running_po.=$row[csf('running_po')];
        		}
        		$running_po_qnty+=$row[csf('po_quantity')];
        	}
        	if(!empty($row[csf('cancel_po')]))
        	{
        		if(!empty($cancel_po))
        		{
        			$cancel_po.=",".$row[csf('cancel_po')];
        		}
        		else{
        			$cancel_po.=$row[csf('cancel_po')];
        		}
        	}
        }
        $stype_color_res=sql_select("select  stripe_type from wo_pre_stripe_color where job_no='$txt_job_no' and status_active=1 and is_deleted=0 group by stripe_type");
        $stype_color='';
        foreach ($stype_color_res as $val) {
        	if(!empty($stype_color))
        	{
        		$stype_color.=", ".$stripe_type_arr[$val[csf('stripe_type')]];
        	}
        	else
        	{
        		$stype_color=$stripe_type_arr[$val[csf('stripe_type')]];
        	}
        	
        }
        $yd_aop_sql=sql_select("select id, job_no,  color_type_id from wo_pre_cost_fabric_cost_dtls where job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by id asc");

        $yd=''; $aop='';
		foreach ($yes_no_sql as $row) {
			
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'peach') !== false)
        	{
			    if(!empty($peach))
			    {
			    	$peach.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$peach.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'brush') !== false || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('Brushing at Main Fabric Booking') || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('Brushing at Main Fabric Booking') || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('Brush [With Finish]') || strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'brushing') !== false)
        	{
			    if(!empty($brush))
			    {
			    	$brush.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$brush.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'wash') !== false || strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'washing') !== false)
        	{
			    if(!empty($fab_wash))
			    {
			    	$fab_wash.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$fab_wash.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'y/d') !== false || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('YD at Main Fabric Booking') || strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'yarn dyeing') !== false)
        	{
			    if(!empty($yd))
			    {
			    	$yd.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$yd.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'aop') !== false || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('All Over Printing'))
        	{
			    if(!empty($aop))
			    {
			    	$aop.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$aop.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
		}
  		ob_start();     
		?>	
											<!--    Header Company Information         -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
			<td width="200" style="font-size:28px"><img  src='<? echo base_url($imge_arr[$cbo_company_name]); ?>' height='100%' width='100%' /></td>
                <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><?=$location_address_arr[$location]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            	<span style="float:center;"><b><strong> <font style="color:black">Main Fabric Booking (Revised)</font></strong></b></span> 
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
							<?
							//if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <?// echo "[Approved]"; ?> </font></strong></span> 
                               <?// }else{ ?>
								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><?// echo "[Not Approved]"; ?> </font></strong></span> 
							   <? //} ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
		<?
        $job_no=trim($txt_job_no,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all from wo_po_break_down where id in($txt_order_no_id) group by id, po_number, pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date");
        $to_ship=0; $fp_ship=0; $f_ship=0;

        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 
        }

        if($to_ship==$f_ship) $shiping_status= "Full shipped";
        else if($to_ship==$fp_ship) $shiping_status= "Full Pending";
        else $shiping_status= "Partial Delivery";
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
        foreach ($nameArray as $result)
        {
           // $total_set_qnty=$result[csf('total_set_qnty')];
            $colar_excess_percent=$result[csf('colar_excess_percent')];
            $cuff_excess_percent=$result[csf('cuff_excess_percent')];
           // $rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
            
            //$booking_percent=$result[csf('booking_percent')];
			$booking_po_id=$result[csf('po_break_down_id')];
			$a_process_loss=$extra[14]+$extra[8]+$extra[6]+$extra[12]+$extra[0]+$extra[10]+$extra[2]+$extra[1];//+$extra[13]
			$b_process_loss=$extra[4]+$extra[3]+$extra[15];
			$tot_pro_loss=$a_process_loss+$b_process_loss;
			?>
			<table width="100%" class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td colspan="1" rowspan="7" width="210">
						<? $nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no' and file_type=1"); ?>
                        <div id="div_size_color_matrix" style="float:left;">
                            <fieldset id="" width="210">
                                <legend>Image </legend>
                                <table width="208">
                                    <tr>
										<?
                                        $img_counter = 0;
                                        foreach($nameArray_imge as $result_imge)
                                        {
											if($path=="") $path='../../';
											?>
											<td><img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="200" height="200" border="2" /></td>
											<?
											$img_counter++;
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
					</td>
					<td width="200"><b>Job No</b></td>
					<?php 
					 ?>
					<td width="250" colspan="3"> <span style="font-size:18px"><b style="float:left;font-size:18px">&nbsp;<? echo trim($txt_job_no,"'");?></span></b> </span> </td>
					<td width="200"><span style="font-size:18px"><b>Product Dept.</b></span></td>
					<td  width="250" colspan="3"><b>&nbsp;<? echo $product_dept[$result[csf('product_dept')]]; ?></b></td>
				</tr>
				<tr>
					<td width="200" style="font-size:16px;"><b>Style Ref</b></td>
					<td  width="250" colspan="3" style="font-size:16px;" >&nbsp;<? echo $result[csf('style_ref_no')]; ?></td>
					<td width="200"><b>Season</b></td>
					<td  width="250" colspan="3">&nbsp; <?echo $season_name_arr[$result[csf('season')]];
                       
                        ?>
                    </td>
				</tr>
				<tr>
					<td width="200"><span style="font-size:18px"><b>Buyer Name</b></span></td>
					<td  width="250" colspan="3">&nbsp;<span style="font-size:18px"><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></span></td>
					<td width="200"><span style="font-size:18px"><b>Revised Booking Date</b></span></td>
					<td  width="250" colspan="3">&nbsp;<span style="font-size:18px"><? $revised_date=$result[csf('revised_date')]; echo change_date_format($revised_date,'dd-mm-yyyy','-',''); ?></span></td>
				</tr>
				<?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$result[csf('gmts_item_id')]);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
						$order_uom_res=sql_select( "select a.order_uom from wo_po_details_master a where a.status_active=1 and a.is_deleted=0  and a.job_no='$txt_job_no' ");
						$order_uom='';
						if(count($order_uom_res))
						{
							$order_uom=$unit_of_measurement[$order_uom_res[0][csf('order_uom')]];
						}
					 ?>
				<tr>
					<td width="200"><span style="font-size:18px"><b>Style Description</b></span></td>
					<td width="250" colspan="3"><span style="font-size:18px"><? echo $result[csf('style_description')]; ?></span></td>
					<td width="200"><b>Fabric Delivery Date</b></td>
					<td width="250" colspan="3">&nbsp;<? echo change_date_format($result[csf('fab_delivery_date')]); ?></td>
				</tr>
				<tr>
					<td width="200"><b>Fabric Source</b></td>
					<td width="250" colspan="3">&nbsp;<? echo $fabric_source[$result[csf('fabric_source')]]; ?></td>
					<td width="200"><b>Revised No</b></td>
                	<td width="250" colspan="3">&nbsp;<?php echo $result[csf('revised_no')];?></td>				
				</tr>
				<tr>
					<td width="200"><b>Avg Con's</b></td>
					<td width="250" colspan="3">&nbsp;<?$avg_cons_div=($fin_fabqnty/$po_qnty_tot); echo number_format($avg_cons_div*12,4); ?></td>
					<td width="200"><b>Shipment Date</b></td>
                	<td width="250" colspan="3">&nbsp;<?php  echo change_date_format($max_shipment_date,'dd-mm-yyyy','-','');?></td>				
				</tr>
				<tr>
					<td width="200"><span style="font-size:18px"><b>Revised Reason</b></span></td>
					<td width="250" colspan="3">&nbsp;<? echo $result[csf('revised_reason')];  ?></td>
					<td width="200"><b>Part No</b></td>
					<td width="250" colspan="3">&nbsp;<?php echo $result[csf('part_no')]; ?></td>
				</tr>
                <tr>
					<td width="200"><span style="font-size:18px"><b>PO No</b></span></td>
					<td width="250" colspan="7" style="word-break: break-all;">&nbsp;<b style="font-size:18px;width: 450px;word-break: break-all;" ><? echo $running_po; ?></b></td>
				</tr>		
								
			</table>
			<br>
			<?
		}	
			
		//if($cbo_fabric_source==1)
		//{
			$nameArray_size=sql_select( "select size_number_id,min(id) as id, min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by size_number_id order by size_order");

			?>
			<table width="100%" style="font-family:Arial Narrow;font-size:18px" >
                <tr>
                    <td width="100%">
                                <table class="rpt_table"  border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
                                    <tr>
                                        <td align="center" style="border:1px solid black"><strong>Color/Size</strong></td>
                                        <?
                                        foreach($nameArray_size  as $result_size)
                                        {
											?>
                                        	<td align="center" style="border:1px solid black"><strong><?=$size_library[$result_size[csf('size_number_id')]];?></strong></td>
                                        <? } ?>
                                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                                    </tr>
                                    <?
                                    $color_size_order_qnty_array=array(); $color_size_qnty_array=array(); $size_tatal=array(); $size_tatal_order=array();
                                    for($c=0;$c<count($gmts_item); $c++)
                                    {
										$item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
										$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id  order by color_order");
										?>
										<tr>
											<td style="border:1px solid black; text-align:center;" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
										</tr>
										<?
										foreach($nameArray_color as $result_color)
										{
											?>
											<tr>
                                                <td align="center" style="border:1px solid black"><?=$color_library[$result_color[csf('color_number_id')]]; ?></td>
                                                <?
                                                $color_total=0; $color_total_order=0;
                                                foreach($nameArray_size  as $result_size)
                                                {
													$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0"); 
													foreach($nameArray_color_size_qnty as $result_color_size_qnty)
													{
														?>
														<td style="border:1px solid black; text-align:center; font-size:18px;">
														<?
														if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
														{
															echo number_format($result_color_size_qnty[csf('order_quantity')],0);
															$color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
															$color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
															$item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
															$item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
															$grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
															$grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
															
															$color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
															$color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
															{
																$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
																$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
															}
															else
															{
																$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
																$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															}
															if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
															{
																$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
																$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
															}
															else
															{
																$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
																$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															}
														}
														else echo "0";
														?>
														</td>
														<?
													}
                                                }
                                                ?>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total_order),0); ?></td>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?></td>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total),0); ?></td>
											</tr>
											<?
										}
										?>
										
										<td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
										<?
										foreach($nameArray_size  as $result_size)
										{
											?>
											<td style="border:1px solid black;  text-align:center; font-size:18px;"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
											<?
										}
										?>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><?  echo number_format(round($item_grand_total_order),0); ?></td>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><?  echo number_format(round($item_grand_total),0); ?></td>
										</tr>
										<?
                                    }
                                    ?>
                                    <tr>
                                    	<td style="border:1px solid black; font-size:18px;" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                                        <?
                                        foreach($nameArray_size  as $result_size)
                                        {
											?>
											<td style="border:1px solid black; text-align:center; font-size:18px;"><? echo $size_tatal_order[$result_size[csf('size_number_id')]]; ?></td>
											<?
                                        }
                                        ?>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($grand_total_order),0); ?></td>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><? $excess_gra_tot=($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><?  echo number_format(round($grand_total),0); ?></td>
                                    </tr>
                                </table>
                    </td>
                    </td>
                </tr>
			</table>
		<br/>
		<br>
      	<!--  Here will be the fabric details  -->
		  <style>
	 .main_table tr th{
		 border:1px solid black;
		 font-size:13px;
		 outline: 0;
	 }
	  .main_table tr td{
		 border:1px solid black;
		 font-size:13px;
		 outline: 0;
	 }
	 </style>
     <?
	 $costing_per=""; $costing_per_qnty=0;
	 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$txt_job_no'");
	{
		$costing_per="Kg";
		$costing_per_qnty=12;
	}
	if($costing_per_id==2)
	{
		$costing_per="Pcs";
		$costing_per_qnty=1;
	}
	if($costing_per_id==3)
	{
		$costing_per="2 Dzn";
		$costing_per_qnty=24;
	}
	if($costing_per_id==4)
	{
		$costing_per="3 Dzn";
		$costing_per_qnty=36;
	}
	if($costing_per_id==5)
	{
		$costing_per="4 Dzn";
		$costing_per_qnty=48;
	}
	$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");
	$nameArray_fabric_description= sql_select("SELECT a.body_part_id, a.lib_yarn_count_deter_id as determin_id, a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type,a.uom, b.dia_width, b.remarks, avg(b.cons) as cons, b.process_loss_percent, avg(b.requirment) as requirment,b.po_break_down_id,  d.fabric_color_id, d.gmts_color_id, d.id as dtls_id, sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id  and d.po_break_down_id in (".str_replace("'","",$txt_order_no_id).") and a.uom=12 and d.status_active=1 and d.is_deleted=0  AND a.status_active = 1 AND a.is_deleted = 0   AND c.status_active = 1  AND c.is_deleted = 0  AND b.status_active = 1  AND b.is_deleted = 0 group by a.body_part_id, a.lib_yarn_count_deter_id, a.color_type_id, a.construction, a.composition, a.gsm_weight,a.uom, b.dia_width, b.remarks,d.fabric_color_id, d.gmts_color_id, d.id,b.po_break_down_id, b.process_loss_percent order by d.id");
	
	foreach ($nameArray_fabric_description as $row) {	
		$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."'  and approval_status=3 and color_name_id=".$row[csf('fabric_color_id')]."");

		$grouping_item=$row[csf('fabric_color_id')].'*'.$row[csf('gmts_color_id')].'*'.$row[csf('construction')].'*'.$row[csf('composition')].'*'.$row[csf('gsm_weight')].'*'.$row[csf('dia_width')].'*'.$row[csf('color_type_id')];	
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['body_part_id'] = $row[csf('body_part_id')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['gmts_color_id'] = $row[csf('gmts_color_id')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['fabric_color_id'] = $row[csf('fabric_color_id')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['lapdip_no'] = $lapdip_no;
		//$fabric_data_arr[$row[csf('body_part_id')]][$grouping_body_part_idart_id'] = $row[csf('body_part_id')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['construction'] = $row[csf('construction')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['composition'] = $row[csf('composition')];

		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['gsm'] = $row[csf('gsm_weight')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['remarks'] = $row[csf('remarks')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['fabric_dia'] = $row[csf('dia_width')].",".$fabric_typee[$row[csf('width_dia_type')]];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['color_type_id'] = $row[csf('color_type_id')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['finsh_cons'] = $row[csf('cons')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['gray_cons'] = $row[csf('requirment')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
		$fabric_data_arr[$row[csf('body_part_id')]][$grouping_item]['process_loss_percent'] = $row[csf('process_loss_percent')];

	}

	/*echo '<pre>';
	print_r($fabric_data_arr); die;*/

	?>
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-size: 18px;">
	 	<tr>
        	<td align="center" colspan="12">  <b>Fabric Details [Kg]</b></td>
        		            
    	</tr>
     	<tr>
		 	<th>Body Part</th>  
			<th>Gmts Color</th>
			<th>Fabric Color</th> 		
     		<th>Composition</th>
			<th>Construction</th>
     		<th>GSM</th>
			<th>Fin. Required GSM/Remarks</th>
			<th>Lab Dip No</th> 
     		<th>Dia Type with </br> Fabric Dia</th>		
     		<th>Color Type</th>
     		<th>Finish Cons</th>
     		<th>Total Finish</br> Fabric Kg</th>
     	</tr>
     	<? 
     	foreach ($fabric_data_arr as $body_part_id=>$fabric_data_arr) {  
     	$i=1;     		  		
     		foreach ($fabric_data_arr as $fabric_id => $value) {
				$fin_fab_qnty+=$value['fin_fab_qnty'];   		 	  		 	
				if($i==1){
				?>
				<tr>
					<td rowspan="<? echo count($fabric_data_arr) ?>"><? echo $body_part[$body_part_id] ?></td>
					<td><? echo $color_library[$value['gmts_color_id']] ?></td>
					<td><? echo $color_library[$value['fabric_color_id']] ?></td>
					<td><? echo $value['composition'] ?></td>
					<td><? echo $value['construction'] ?></td>
					<td><? echo $value['gsm'] ?></td>
					<td><? echo $value['remarks'] ?></td>
					<td><? echo $value['lapdip_no']; ?></td>
					<td><? echo $value['fabric_dia'] ?></td>
					<td><? echo $color_type[$value['color_type_id']] ?></td>
					<td align="right";><? echo number_format($value['finsh_cons'],4) ?></td>
					<td align="right";><? echo number_format($value['fin_fab_qnty'],2) ?></td>
				</tr>
				<? } 
				else { ?>
					<tr>
						<td><? echo $color_library[$value['gmts_color_id']] ?></td>
						<td><? echo $color_library[$value['fabric_color_id']] ?></td>
						<td><? echo $value['composition'] ?></td>
						<td><? echo $value['construction'] ?></td>
						<td><? echo $value['gsm'] ?></td>
						<td><? echo $value['remarks'] ?></td>
						<td><? echo $value['lapdip_no']; ?></td>
						<td><? echo $value['fabric_dia'] ?></td>
						<td><? echo $color_type[$value['color_type_id']] ?></td>
						<td align="right";><? echo number_format($value['finsh_cons'],4) ?></td>
						<td align="right";><? echo number_format($value['fin_fab_qnty'],2) ?></td>
					</tr>
				<? }
				$i++;
     		}
     	} 
     	?>
     	<tr>
     		<th align="right" colspan="11">Total</th>
     		<th align="right";><?echo number_format($fin_fab_qnty,2);  ?></th>
     	</tr>
     </table>
	 <?
	$nameArray_fabric_desc= sql_select("SELECT a.body_part_id, a.lib_yarn_count_deter_id as determin_id, a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type,a.uom, b.dia_width, b.remarks, avg(b.cons) as cons, b.process_loss_percent, avg(b.requirment) as requirment,b.po_break_down_id,  d.fabric_color_id, d.gmts_color_id, d.id as dtls_id, sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.po_break_down_id in (".str_replace("'","",$txt_order_no_id).") and a.uom=1 and d.status_active=1 and d.is_deleted=0  AND a.status_active = 1 AND a.is_deleted = 0   AND c.status_active = 1  AND c.is_deleted = 0  AND b.status_active = 1  AND b.is_deleted = 0 group by a.body_part_id, a.lib_yarn_count_deter_id, a.color_type_id, a.construction, a.composition, a.gsm_weight,a.uom, b.dia_width, b.remarks,d.fabric_color_id, d.gmts_color_id, d.id,b.po_break_down_id, b.process_loss_percent order by d.id");
	foreach ($nameArray_fabric_desc as $row) {	
		$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."'  and approval_status=3 and color_name_id=".$row[csf('fabric_color_id')]."");

		$grouping_item=$row[csf('fabric_color_id')].'*'.$row[csf('gmts_color_id')].'*'.$row[csf('construction')].'*'.$row[csf('composition')].'*'.$row[csf('gsm_weight')].'*'.$row[csf('dia_width')].'*'.$row[csf('color_type_id')];	
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['body_part_id'] = $row[csf('body_part_id')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['gmts_color_id'] = $row[csf('gmts_color_id')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['fabric_color_id'] = $row[csf('fabric_color_id')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['lapdip_no'] = $lapdip_no;
		//$fabric_data_arr[$row[csf('body_part_id')]][$grouping_body_part_idart_id'] = $row[csf('body_part_id')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['construction'] = $row[csf('construction')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['composition'] = $row[csf('composition')];

		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['gsm'] = $row[csf('gsm_weight')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['remarks'] = $row[csf('remarks')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['fabric_dia'] = $row[csf('dia_width')].",".$fabric_typee[$row[csf('width_dia_type')]];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['color_type_id'] = $row[csf('color_type_id')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['finsh_cons'] = $row[csf('cons')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['gray_cons'] = $row[csf('requirment')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
		$fabric_data_ar[$row[csf('body_part_id')]][$grouping_item]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];

	}

	/*echo '<pre>';
	print_r($fabric_data_arr); die;*/

	?>
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-size: 18px;">
	 	<tr>
        	<td align="center" colspan="12">  <b>Fabric Details [Pcs]</b></td>
        		            
    	</tr>
     	<tr>
		 	<th>Body Part</th> 
			<th>Gmts Color</th> 
			<th>Fabric Color</th> 		
     		<th>Composition</th>
			<th>Construction</th>
     		<th>GSM</th>
			<th>Fin. Required GSM</th>
			<th>Lab Dip No</th> 
     		<th>Dia Type with </br> Fabric Dia</th>		
     		<th>Color Type</th>
     		<th>Finish Cons</th>
     		<th>Total Finish</br>Quantity</th>
     	</tr>
     	<? 
     	foreach ($fabric_data_ar as $body_part_id=>$fabric_data_ar) {  
     	$i=1;     		  		
     		foreach ($fabric_data_ar as $fabric_id => $value) {
     				$fin_fab_qty+=$value['fin_fab_qnty'];   		 	  		 	
     		 		if($i==1){
     		 	 	?>
	     		 	<tr>
		     			<td rowspan="<? echo count($fabric_data_ar) ?>"><? echo $body_part[$body_part_id] ?></td>
						<td><? echo $color_library[$value['gmts_color_id']] ?></td>
						<td><? echo $color_library[$value['fabric_color_id']] ?></td>
						<td><? echo $value['composition'] ?></td>
		     			<td><? echo $value['construction'] ?></td>
		     			<td><? echo $value['gsm'] ?></td>
						<td><? echo $value['remarks'] ?></td>
						<td><? echo $value['lapdip_no']; ?></td>
		     			<td><? echo $value['fabric_dia'] ?></td>
		     			<td><? echo $color_type[$value['color_type_id']] ?></td>
		     			<td align="right";><? echo number_format($value['finsh_cons']) ?></td>
		     			<td align="right";><? echo number_format($value['fin_fab_qnty']) ?></td>
	     			</tr>
     		 		<? } 
     		 		else { ?>
     		 			<tr>
						  	<td><? echo $color_library[$value['gmts_color_id']] ?></td>
						  	<td><? echo $color_library[$value['fabric_color_id']] ?></td>
							<td><? echo $value['composition'] ?></td>
		     				<td><? echo $value['construction'] ?></td>
			     			<td><? echo $value['gsm'] ?></td>
							<td><? echo $value['remarks'] ?></td>
							<td><? echo $value['lapdip_no']; ?></td>
			     			<td><? echo $value['fabric_dia'] ?></td>
			     			<td><? echo $color_type[$value['color_type_id']] ?></td>
			     			<td align="right";><? echo number_format($value['finsh_cons']) ?></td>
			     			<td align="right";><? echo number_format($value['fin_fab_qnty']) ?></td>
	     				</tr>
     		 		<? }
     		 		$i++;
     		 	//}
     		}
     	} 
     	?>
     	<tr>
     		<th align="right" colspan="11">Total</th>
     		<th align="right";><?echo number_format($fin_fab_qty);  ?></th>
     	</tr>
     </table>
      <br/>
     <?
	$lab_dip_color_arr=array();
	$lab_dip_color_sql=sql_select("select pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no='$txt_job_no'");
	foreach($lab_dip_color_sql as $row)
	{
		$lab_dip_color_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('gmts_color_id')]]=$row[csf('contrast_color_id')];
	}
	
	

	$collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

	$collar_cuff_sql="select a.id, a.item_number_id, a.color_size_sensitive, a.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size, c.size_number_id, d.colar_cuff_per, e.body_part_full_name, e.body_part_type
	FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d, lib_body_part e

	WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.po_break_down_id in (".str_replace("'","",$txt_order_no_id).") and a.body_part_id=e.id and e.body_part_type in (40,50) and c.id=d.color_size_table_id and d.color_size_table_id=b.color_size_table_id  and d.po_break_down_id=c.po_break_down_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 order by  c.size_order";
	//echo $collar_cuff_sql;
	$collar_cuff_sql_res=sql_select($collar_cuff_sql);
	
	$itemIdArr=array();

	foreach($collar_cuff_sql_res as $collar_cuff_row)
	{
		$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
		$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
		$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
		if(!empty($collar_cuff_row[csf('item_size')]))
		{
			$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
		}
		
		$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('color_size_sensitive')]]=$collar_cuff_row[csf('color_break_down')];
		
		$itemIdArr[$collar_cuff_row[csf('body_part_type')]].=$collar_cuff_row[csf('item_number_id')].',';
	}
	unset($collar_cuff_sql_res);
	
	$order_plan_qty_arr=array();
	$color_wise_wo_sql_qnty=sql_select( "select item_number_id, color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in (".str_replace("'","",$txt_order_no_id).") and status_active=1 and is_deleted =0  group by item_number_id, color_number_id, size_number_id"); 

	foreach($color_wise_wo_sql_qnty as $row)
	{
		$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
		$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
	}
	unset($color_wise_wo_sql_qnty);

	
	foreach($collar_cuff_body_arr as $body_type=>$body_name)
	{
		$gmtsItemId=array_filter(array_unique(explode(",",$itemIdArr[$body_type])));
		foreach($body_name as $body_val)
		{
			$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
			$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

			?>
			<div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;font-size:18px;">
			<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
				<tr>
					<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
				</tr>
				<tr>
					<td width="100">Size</td>
						<?
						foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
						{
							?>
							<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
							<?
						}
						?>
					<td width="60" rowspan="2" align="center"><strong>Total</strong></td>
					<td rowspan="2" align="center"><strong>Extra %</strong></td>
				</tr>
				<tr>
					<td style="font-size:12px"><? echo $body_val; ?> Size</td>
					<?
					foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
					{
						if(count($size_number)>0)
						{
							 foreach($size_number  as $item_size=>$val)
							 {
								?>
								<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
								<?
							 }
						}
						else
						{
							?>
							<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
							<?
						}
					}
					?>
				</tr>
					<?

					$pre_size_total_arr=array();
					foreach($color_size_sensitive_arr[$body_val] as $pre_cost_id=>$pre_cost_data)
					{
						foreach($pre_cost_data as $color_number_id=>$color_number_data)
						{
							foreach($color_number_data as $color_size_sensitive=>$color_break_down)
							{
								$pre_color_total_collar=0;
								$pre_color_total_collar_order_qnty=0;
								$process_loss_method=$process_loss_method;
								$constrast_color_arr=array();
								if($color_size_sensitive==3)
								{
									$constrast_color=explode('__',$color_break_down);
									for($i=0;$i<count($constrast_color);$i++)
									{
										$constrast_color2=explode('_',$constrast_color[$i]);
										$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
									}
								}
								?>
								<tr>
									<td>
										<?
										if( $color_size_sensitive==3)
										{
											echo strtoupper ($constrast_color_arr[$color_number_id]) ;
											$lab_dip_color_id=$lab_dip_color_arr[$pre_cost_id][$color_number_id];
										}
										else
										{
											echo $color_library[$color_number_id];
											$lab_dip_color_id=$color_number_id;
										}
										?>
									</td>
									<?
									foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
									{
										?>
										<td align="center" style="border:1px solid black">
											<? $plan_cut=0; $collerqty=0; $collar_ex_per=0;
											$plan_cut=0;
											foreach($gmtsItemId as $giid)
											{
												if($body_type==50) $plan_cut+=($order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan']); // ISsue id- 7185
												else $plan_cut+=$order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'];
											}


											$collar_ex_per=$collar_cuff_percent_arr[$body_type][$body_val][$color_number_id][$size_number_id];


											if($body_type==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
											else if($body_type==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
											$colar_excess_per=number_format(($plan_cut*$collar_ex_per)/100,6,".",",");
											$collerqty=($plan_cut+$colar_excess_per);
											echo number_format($collerqty);
											$pre_size_total_arr[$size_number_id]+=$collerqty;
											$pre_color_total_collar+=$collerqty;
											$pre_color_total_collar_order_qnty+=$plan_cut;
											?>
										</td>
										<?
									}
									?>

									<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
									<td align="center"><? echo number_format((($pre_color_total_collar-$pre_color_total_collar_order_qnty)/$pre_color_total_collar_order_qnty)*100,2); ?></td>
								</tr>
								<?
								$pre_grand_collar_ex_per+=$collar_ex_per;
								$pre_grand_tot_collar+=$pre_color_total_collar;
								$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
							}
						}
					}
					?>
				
				<tr>
					<td>Size Total</td>
						<?
							foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
							{
								$size_qty=$pre_size_total_arr[$size_number_id];
								?>
								<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
								<?
							}
						?>
					<td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
					<td align="center" style="border:1px solid black"><? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td>
				</tr>
			</table>
		</div>
		<br/>
		<?
	}
 }
   ?>

        <br/>
						<?
						$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
						$sql=sql_select("select id from wo_booking_mst where job_no='$txt_job_no'");
						$bookingId=0;
						foreach($sql as $row){
							$bookingId= $row[csf('id')];
						}
						$co=0;
						// $sql_data="select a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom,sum(qty) as qty, b.description   from wo_dye_to_match a, wo_pre_cost_trim_cost_dtls b where a.precost_trim_cost_id=b.id and a.booking_id=$bookingId and a.qty>0 and a.status_active=1 and b.status_active=1  group by a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom, b.description order by a.fabric_color";
						
						/*$sql_data="select a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom,qty as qty, b.description ,a.pre_cost_fabric_cost_id	from wo_dye_to_match a, wo_pre_cost_trim_cost_dtls b, wo_booking_dtls c 
						where a.precost_trim_cost_id=b.id and a.booking_id=$bookingId and a.qty>0  and a.fabric_color=c.fabric_color_id
						 and a.pre_cost_fabric_cost_id=c.pre_cost_fabric_cost_dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom, b.description ,qty,a.pre_cost_fabric_cost_id order by a.fabric_color ";*/
						 
						  $sql_data="SELECT a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom,qty as qty, b.description ,a.pre_cost_fabric_cost_id	from wo_dye_to_match a, wo_pre_cost_trim_cost_dtls b
						where a.precost_trim_cost_id=b.id and b.job_no='$txt_job_no' and a.qty>0  
						  and a.status_active=1 and b.status_active=1  group by a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom, b.description ,qty,a.pre_cost_fabric_cost_id order by a.fabric_color ";

						$sql_data_tdm=sql_select($sql_data);
						if(count($sql_data_tdm)>0){
						?> 
						
 			       		 <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

 			                   <tr align="center">
 			                    	<td colspan="10"><b>Trims Dyes To Match</b></td>
 			                    </tr>
 			                    <tr align="center">
 				                    <td>Sl</td>
 				                    <td>Item</td>
 				                    <td>Item Description</td>
 				                    <td>Body Color</td>
 				                    <td>Item Color</td>
 				                    <td>Finish Qty.</td>
 				                    <td>UOM</td>
 			                    </tr>
 			                    <?
 								
					

 								foreach($sql_data_tdm  as $row)
 			                    {
 									$co++;
 									?>
 				                    <tr>
 				                    <td><? echo $co; ?></td>
 				                    <td> <? echo $lib_item_group_arr[$row[csf('trim_group')]];?></td>
 				                    <td><p> <? echo $row[csf('description')];?></p></td>
 				                    <td><? echo $color_library[$row[csf('fabric_color')]];?></td>
 				                    <td><? echo $color_library[$row[csf('item_color')]];?></td>
 				                    <td align="right"><? echo $row[csf('qty')];?></td>
 				                    <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]];?></td>
 				                    </tr>
 				                    <?
 								}
 								?>


 			            </table>
       		 		
						 <?
						}
			 ?>
					 
       		 
			
            <br>
			<br>


     
			<table  width="100%" style="margin: 0px;padding: 0px;">
	        <?php $stripe_color_wise=array(); ?>
	       
	        <tr>
	        	<td width="70%" >
    		        <table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;" >
    			        		       
    				        
    				        <?
    						$color_name_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
    						$sql_stripe=("SELECT c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d , wo_po_color_size_breakdown e where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and b.job_no='$txt_job_no'  and d.job_no='$txt_job_no' and b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  and c.color_type_id in (2,6,33,34) and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and 	b.is_deleted=0 and e.id=b.color_size_table_id and e.is_deleted=0 and e.status_active=1 and e.color_number_id=d.color_number_id  group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,d.uom,c.composition,c.construction,b.dia_width order by d.id "); 	
							
							
    						$result_data=sql_select($sql_stripe);
							if(count($result_data)>0){
    						foreach($result_data as $row)
    						{
    							$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
    							$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
    							$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
    							$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
    							$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];

    							$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
    							$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
    							$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
    							$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
    							$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
    						}
							//if(count($stripe_arr)>0){
    						?>
    						<thead>
							<tr align="center">
 			                    	<td colspan="9"><b>Stripe Details</b></td>
 			                    </tr>
    							<tr>
    		    		            <th align="center" width="30"> SL</th>
    		    		            <th align="center" width="100"> Body Part</th>
    		    		            <th align="center" width="80"> Fabric Color</th>
    		    		            <th align="center" width="70"> Fabric Qty(KG)</th>
    		    		            <th align="center" width="70"> Stripe Color</th>
    		    		            <th align="center" width="70"> Stripe Measurement</th>
    		    		            <th align="center" width="70"> Stripe Uom</th>
    		    		            <th align="center" width="70"> Qty.(KG)</th>
    		    		            <th align="center" width="70"> Y/D Req.</th>
    				            </tr>
    						</thead>
    				        <tbody>  
    				            <?
    							//if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
    							//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
    								//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
    				            
    							$i=1;$total_fab_qty=0;$total_fabreqtotkg=0;$fab_data_array=array();
    				            foreach($stripe_arr as $body_id=>$body_data)
    				            {
    								foreach($body_data as $color_id=>$color_val)
    								{
    									$rowspan=count($color_val['stripe_color']);
    									$composition=$stripe_arr2[$body_id][$color_id]['composition'];
    									$construction=$stripe_arr2[$body_id][$color_id]['construction'];
    									$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
    									$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
    									$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];

    									if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
    									else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";

    									$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
    										WHERE a.job_no=b.job_no and
    										a.id=b.pre_cost_fabric_cost_dtls_id and
    										c.job_no_mst=a.job_no and
    										c.id=b.color_size_table_id and
    										b.po_break_down_id=d.po_break_down_id and
    										b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
											d.po_break_down_id in (".str_replace("'","",$txt_order_no_id).") and
    										a.body_part_id='".$body_id."' and
    										a.color_type_id='".$color_type_id."' and
    										a.construction='".$construction."' and
    										a.composition='".$composition."' and
    										a.gsm_weight='".$gsm_weight."' and
    										$color_cond and
    										d.status_active=1 and
    										d.is_deleted=0
    										");
    								
    										list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;
    									$sk=0;
    									foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
    		    						{
    		    							
    			    							?>
    										<tr title="<?=$span?>">
    			    							<?
    			    							$color_qty=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
    			    							if($sk==0)
    			    							{
    				    							?>
    				    							<td rowspan="<?=$rowspan;?>"> <? echo $i; ?></td>
    				    							<td rowspan="<?=$rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
    				    							<td rowspan="<?=$rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
    				    							<td rowspan="<?=$rowspan;?>" align="right"> <? echo number_format($color_qty,2); ?>&nbsp; </td>
    				    							<?
    				    							$total_fab_qty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
    				    							$i++;
    				    						}
    			    							

    			    								$measurement=$color_val['measurement'][$strip_color_id];
    			    								$uom=$color_val['uom'][$strip_color_id];
    			    								$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
    			    								$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
    			    								
    			    								?>
    			    							
    			        								<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
    			        								<td align="right"> <? echo  number_format($measurement,2); ?> &nbsp; </td>
    			        		                        <td align="center"> <? echo  $unit_of_measurement[$uom]; ?></td>
    			        								<td align="right"> <? echo  number_format($fabreqtotkg,2); ?> &nbsp;</td>
    			        								<td align="center"> <? echo  $yes_no[$yarn_dyed]; ?></td>
    			    								
    			    								<?
    			    								
    			    								$sk++;
    			    								$total_fabreqtotkg+=$fabreqtotkg;
    			    								$stripe_color_wise[$color_name_arr[$s_color_val]]+=$fabreqtotkg;
    			    							
    			    							
    			    							?>
    										</tr>
    										<?
    									}
    								}
    							}
    							?>
    						</tbody>
    			            <tfoot>
    				            <tr>
    		    		            <td colspan="3">Total </td>
    		    		            <td align="right">  <? echo  number_format($total_fab_qty,2); ?> &nbsp;</td>
    		    		            <td></td>
    		    		            <td></td>
    		    		            <td>   </td>
    		    		            <td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> &nbsp;</td>
    		    		        </tr>
    			            </tfoot>
							<?}else echo ""; ?>
    			    </table>
	        	</td>
	        	
	        	<!-- <td width="20%" >
			        <table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;"  >
			       	        <caption style="justify-content: center;text-align: center;">Stripe  Color wise Summary</caption>
			               	<thead>
			       	            <tr>
			       		            <th width="30"> SL</th>
			       		            
			       		            <th width="70"> Stripe Color</th>
			       		           
			       		            <th  width="70"> Qty.(KG)</th>
			       		           
			       	            </tr>
			       	        </thead>
			       	        <tbody>
			       	            <?

			       					$i=1;$total_stripe_qnt=0;        		            
			       						foreach($stripe_color_wise as $color=>$val)
			       						{
			       							
			       							
			       							?>
			       							<tr>
			           							<td> <? echo $i; ?></td>
			           							
			           							<td > <? echo $color; ?></td>
			           							<td align="right"> <?php echo number_format($val,2); ?></td>
			           						</tr>
			       							
			       							<?
			       							$total_stripe_qnt+=$val;
			       							
			       							$i++;
			       						}
			       					
			       					?>
			       			</tbody>
		                   <tfoot>
		       	            <tr>
		       		            <td></td>
		       		            <td></td>
		       		            <td align="right"><? echo  number_format($total_stripe_qnt,2); ?> </td>
		       	            </tr>
		                   </tfoot>
			        </table>
	        	</td> -->
	        </tr>
        </table >
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
					<tr>
						<td width="49%" valign="top">
							<table  width="70%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
								<tr align="center">
								<td colspan="7"><b>Summary  Part</b></td>
								</tr>
								<tr align="center">
								<td>System Generate Number</td>
								<td>Booking No</td>
								<td>Existing PO</td>
								<td>Remarks</td>
								</tr>
								<?
						$order_arr=return_library_array( "select id,po_number from wo_po_break_down where is_deleted=0  and status_active=1 and id in ($txt_order_no_id)", "id", "po_number");
						$masterTable_qry=sql_select("SELECT c.system_no,b.booking_no, b.po_break_down_id,c.remarks from wo_booking_revised_mst c,wo_booking_revised_dtls b where c.system_no = '$txt_system_no' and c.system_no=b.system_no and c.po_break_down_id in ($txt_selected_po) group by c.system_no,b.booking_no, b.po_break_down_id,c.remarks");

						 foreach($masterTable_qry  as $key=> $val)
						 { 
						 	$system_arr[$val[csf('system_no')]][$val[csf('booking_no')]]['po_break_down_id'].=$order_arr[$val[csf('po_break_down_id')]].",";
							$system_arr[$val[csf('system_no')]][$val[csf('booking_no')]]['remark']=$val[csf('remarks')];
						 }
						 			
									 $k=1;$p=1;
									foreach($system_arr  as $sys_num=>$sys_data)
									{
										foreach($sys_data  as $booking_num=>$row)
										{ 	
											$po_num_arr=rtrim($row['po_break_down_id'],',');
											$po_num=implode(",",array_unique(explode(",",str_replace("'","",$po_num_arr))));
											?>
											<tr align="center">
												<td align="center" ><?=$sys_num;?></td>
												<td align="center"><?=$booking_num;?></td>
												<td align="center"><?=$po_num;?></td>
												<td align="center"><?=$row['remark'];?></td>
											</tr>
											<? $k++;
										}
									}?>
								
							</table>
						</td>
					</tr>
		</table>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="font-family:Arial Narrow;font-size:18px;">
            <tr>
                <td width="49%" valign="top">
				<?  echo get_spacial_instruction($txt_booking_no,"97%",118); ?>
                </td>
                <td width="2%">
                </td>
            </tr>
        </table>
        <br/>
        
        <?
	   
		  echo signature_table(1, $cbo_company_name, "1000px",$template_id, 40, $user_lib_name_arr[$inserted_by]);
       ?>
		<br>
       <?
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){	
	$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=87 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$mail_sql_res=sql_select($sql);
		
		$mailArr=array();
		foreach($mail_sql_res as $row)
		{
			$mailArr[$row[EMAIL]]=$row[EMAIL]; 
		}
		
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");

		

		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}
		
		$to=implode(',',$mailArr);
		$subject="Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			echo sendMailMailer( $to, $subject, $emailBody,$from_mail,'' );
		}
	}
	exit();
}
if($action="save_update_delete"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//---------------------- Insert Here-------------------------------

	$txt_system_no   = str_replace("'","",$txt_system_no);
	$cbo_company_name     = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name     = str_replace("'","",$cbo_buyer_name);
	$txt_selected_po     = str_replace("'","",$txt_selected_po);
	if ($operation==0) 
	{
		
			$con = connect();

			if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";
			check_table_status( $_SESSION['menu_id'],1);
			$id=return_next_id( "id", "wo_booking_revised_mst", 0 ) ;
			$field_array="id,system_no_prefix, system_no_prefix_num, system_no,company_name,buyer_name,revised_no,order_no,po_break_down_id,part_no,fab_delivery_date,remarks,revised_date,revised_reason,inserted_by,insert_date,is_deleted";
			$new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'RB', date("Y",time()), 5, "select system_no_prefix,system_no_prefix_num from wo_booking_revised_mst where company_name=$cbo_company_name and $year_cond=".date('Y',time())." order by id DESC", "system_no_prefix", "system_no_prefix_num" ));

			$data_array="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$text_revised_no.",".$txt_select_item.",'".$txt_selected_po."',".$txt_part_no.",".$txt_delivery_date.",".$txt_remarks.",".$txt_revised_date.",".$txt_revision_reason.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)"; 

			//if  (check_table_status( $_SESSION['menu_id'],1)==0 ) { echo "15**0"; disconnect($con); die;}
			$id_dtls=return_next_id( "id", "wo_booking_revised_dtls", 1 ) ;
			$field_array1="id, system_no,booking_no,po_break_down_id,color_type,construction,pre_cost_fabric_cost_dtls_id,copmposition,color_size_table_id,precons,gsm_weight,dia_width,gmts_color_id,fabric_color_id,fin_fab_qnty,booking_mst_id, inserted_by, insert_date";
			for ($i=1;$i<=$total_row;$i++)
			{
				$fab_booking_id="fab_booking_id_".$i;
				$pobreakdownid="pobreakdownid_".$i;
				$colortype="colortype_".$i;
				$construction="construction_".$i;
				$precostfabriccostdtlsid="precostfabriccostdtlsid_".$i;
				$composition="composition_".$i;
				$cotaid="cotaid_".$i;
				$preconskg="preconskg_".$i;
				$gsmweight="gsmweight_".$i;
				$diawidth="diawidth_".$i;
				$gmtscolorid="gmtscolorid_".$i;
				$colorid="colorid_".$i;
				$finscons="finscons_".$i;
				$booking_row_id="booking_row_id_".$i;
				if ($i!=1) $data_array1 .=",";

				$data_array1 .="(".$id_dtls.",'".$new_return_number[0]."',".$$fab_booking_id.",".$$pobreakdownid.",".$$colortype.",".$$construction.",".$$precostfabriccostdtlsid.",".$$composition.",".$$cotaid.",".$$preconskg.",".$$gsmweight.",".$$diawidth.",".$$gmtscolorid.",".$$colorid.",".$$finscons.",".$$booking_row_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
   
			  $id_dtls=$id_dtls+1;
   
			}
		//	echo "10**";
			$flag=0;
			$rID=sql_insert("wo_booking_revised_mst",$field_array,$data_array,0);
			if($rID==1) $flag=1;else $flag=0;
			if($flag==1)
			{
				$rID1=sql_insert("wo_booking_revised_dtls",$field_array1,$data_array1,1);
				if($rID1==1) $flag=1;else $flag=0;
			}
			
			  //echo "10**insert into wo_booking_revised_mst (".$field_array.") Values ".$data_array."";die;
			//echo  "10**=".$rID."**".$rID1."**".$flag;
			check_table_status( $_SESSION['menu_id'],0);//die;
			$system_id=$new_return_number[0];
			if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);  
					echo "0**".$id."**".$system_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**"."rollback";
					
				}
			}
			disconnect($con);
			die;
	   
	}else if ($operation==1)
	{
		
			$con = connect();
			$update_id=str_replace("'","",$update_id);
			$sql_chk_revised_qry=sql_select("select max(a.revised_no) as revised_no,a.po_break_down_id from wo_booking_revised_mst a where a.system_no='$txt_system_no'  and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id");
			 
			$i=1;
			$txt_select_po=str_replace("'","",$txt_selected_po);
			//$revised_no=str_replace("'","",$text_revised_no);
			$revised_no=1;
			foreach($sql_chk_revised_qry as $rows){
				
				if($txt_select_po!= $rows[csf('po_break_down_id')])
				{
					
					$revised_no_val=$rows[csf('revised_no')]+1;
				}
				else{

					if($rows[csf('revised_no')])
					{
						$revised_no_val=$rows[csf('revised_no')];
					}
					else{
						$revised_no_val=$revised_no;
					}
					
				}
				
				
				$i++;
			}
			//echo "10**=A=".$revised_no_val;die;
			//id,system_no_prefix, system_no_prefix_num, system_no,company_name,buyer_name,revised_no,order_no,po_break_down_id,part_no,fab_delivery_date,remarks,revised_date,revised_reason
			$field_array="buyer_name*revised_no*order_no*po_break_down_id*part_no*fab_delivery_date*remarks*revised_date*revised_reason*updated_by*update_date";
			$data_array="".$cbo_buyer_name."*".$revised_no_val."*".$txt_select_item."*'".$txt_selected_po."'*".$txt_part_no."*".$txt_delivery_date."*".$txt_remarks."*".$txt_revised_date."*".$txt_revision_reason."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$id_dtls=return_next_id( "id", "wo_booking_revised_dtls", 1 );
			$field_array1="id, system_no,booking_no,po_break_down_id,color_type,construction,pre_cost_fabric_cost_dtls_id,copmposition,color_size_table_id,precons,gsm_weight,dia_width,gmts_color_id,fabric_color_id,fin_fab_qnty,booking_mst_id, inserted_by, insert_date";
			// $field_array_up1="po_break_down_id*color_type*construction*pre_cost_fabric_cost_dtls_id*copmposition*color_size_table_id*precons*gsm_weight*dia_width*gmts_color_id*fabric_color_id*fin_fab_qnty*updated_by*update_date";

		   for ($i=1;$i<=$total_row;$i++)
		   {
			$fab_booking_id="fab_booking_id_".$i;
			$pobreakdownid="pobreakdownid_".$i;
			$colortype="colortype_".$i;
			$construction="construction_".$i;
			$precostfabriccostdtlsid="precostfabriccostdtlsid_".$i;
			$composition="composition_".$i;
			$cotaid="cotaid_".$i;
			$preconskg="preconskg_".$i;
			$gsmweight="gsmweight_".$i;
			$diawidth="diawidth_".$i;
			$gmtscolorid="gmtscolorid_".$i;
			$colorid="colorid_".$i;
			$finscons="finscons_".$i;
			$booking_row_id="booking_row_id_".$i;

			//   if(str_replace("'",'',$$booking_row_id)!="")
			//   {
			// 	  $id_arr[]=str_replace("'",'',$$booking_row_id);
			// 	  $data_array_up1[str_replace("'",'',$$booking_row_id)] =explode("*",("".$$pobreakdownid."*".$$colortype."*".$$construction."*".$$precostfabriccostdtlsid."*".$$composition."*".$$cotaid."*".$$preconskg."*".$$gsmweight."*".$$diawidth."*".$$gmtscolorid."*".$$colorid."*".$$finscons."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			// 	  $rID_de1=execute_query( "delete from wo_booking_revised_dtls where  booking_mst_id =".$$booking_row_id."",0);
  
			//   }
			if ($i!=1) $data_array1 .=",";

				$data_array1 .="(".$id_dtls.",'".$txt_system_no."',".$$fab_booking_id.",".$$pobreakdownid.",".$$colortype.",".$$construction.",".$$precostfabriccostdtlsid.",".$$composition.",".$$cotaid.",".$$preconskg.",".$$gsmweight.",".$$diawidth.",".$$gmtscolorid.",".$$colorid.",".$$finscons.",".$$booking_row_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_dtls=$id_dtls+1;

		   }
		    
			// $rID1=execute_query(bulk_update_sql_statement( "wo_booking_revised_dtls", "booking_mst_id", $field_array_up1, $data_array_up1, $id_arr ));
			//echo "10**".bulk_update_sql_statement("wo_booking_revised_dtls", "booking_mst_id",$field_array_up1,$data_array_up1,$id_arr );die;
		
			$flag=0;
			$rID=sql_update("wo_booking_revised_mst",$field_array,$data_array,"id","".$update_id."",0);
			if($rID==1) $flag=1;else $flag=0;
			if($flag==1)
			{
				$system_no=str_replace("'","",$txt_system_no);
				$rID_de1=execute_query( "delete from wo_booking_revised_dtls where  system_no ='".$system_no."'",0);
				if($rID_de1==1) $flag=1;else $flag=0;
				if($flag==1)
				{
					$rID1=sql_insert("wo_booking_revised_dtls",$field_array1,$data_array1,1);
					if($rID1==1) $flag=1;else $flag=0;

				}
				
			// echo  "10**=".$rID."**".$rID1."**".$flag;die;
			}
			check_table_status( $_SESSION['menu_id'],0);
			if($db_type==2 || $db_type==1 )
			{
			if($flag==1)
				{
					oci_commit($con);   
					echo "1**".str_replace("'","",$update_id)."**".$txt_system_no."**".$revised_no_val;;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID.'=rollback&update_id= '.str_replace("'","",$update_id);
				}
			}
			disconnect($con);
			die;		
	}else if ($operation==2)   // Delete Here
	{
		$con = connect();

		// $field_array="updated_by*update_date*status_active*is_deleted";
		// $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		// $rID=sql_delete("wo_booking_revised_mst",$field_array,$data_array,"id","".$txt_system_no."",1);
		// $rID1=sql_delete("wo_booking_revised_dtls",$field_array,$data_array,"id","".$txt_system_no."",1);
		$rID=execute_query("update wo_booking_revised_mst set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where id=$update_id");
		$rID1=execute_query("update wo_booking_revised_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where system_no='$txt_system_no'");
		 if($db_type==2 || $db_type==1 )
		 {
			if($rID1 && $rID1 ){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no);
			}
		 }
		disconnect($con);
		die;
	}
 exit();
}

?>