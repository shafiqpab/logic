<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_sent")
{
	$data = explode("_",$data);
	if($data[0]==1)
	{
	 echo create_drop_down( "cbo_out_company", 170, "select id,buyer_name from  lib_buyer  where status_active=1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","0" );
    }
	else if($data[0]==2)
	{
	 echo create_drop_down( "cbo_out_company", 170, "select id,supplier_name from  lib_supplier  where status_active=1 and is_deleted=0  order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected,"","0" );
	}
     else if($data[0]==3)
	{
	 echo create_drop_down( "cbo_out_company", 170, "select id,other_party_name from  lib_other_party where status_active=1 and is_deleted=0  order by other_party_name","id,other_party_name", 1, "-- Select Other Party --", $selected,"","0" );
	}
	else
	{
		echo create_drop_down( "cbo_out_company", 172, $blank_array,"", 1, "-- Select  --", 0, "",0 );
	}
	
	exit();
}

if ($action=="load_drop_down_out_company")
{
	echo create_drop_down( "cbo_out_company", 170, "select id,company_name from lib_company  where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $company_id,"",1 );
	exit();
}

if ($action=="load_drop_down_out_supplier")
{
	$datas=explode("_", $data);
	echo create_drop_down( "cbo_out_company", 170, "select sent_to,sent_to from inv_gate_pass_mst where status_active=1 and id=$datas[0] group by sent_to","sent_to,sent_to", 1, "-- Select --", $datas[2], "",0 );
	//echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", $datas[2], "",0 );
	exit();
}

if ($action=="load_drop_down_out_location")
{
	echo create_drop_down( "cbo_out_location_id", 170, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if ($action=="load_drop_down_com_location")
{
	echo create_drop_down( "cbo_com_location_id", 170, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if($action=="load_drop_down_dying_source")
{
	$data = explode("_",$data);
	//print_r($data);die;
	$basis_id=$data[1];
	$company_id=$data[2];
	$sql_issue_dtls="select knit_dye_source,knit_dye_company,issue_purpose from inv_issue_master where  issue_number='$data[0]' and status_active=1 and is_deleted=0";
	$res = sql_select($sql_issue_dtls);
	foreach($res as $row)
	{
		$dying_source=$row[csf("knit_dye_source")];
		$dying_company=$row[csf("knit_dye_company")];
		$issue_purpose=$row[csf("issue_purpose")];
	}
	if( $basis_id==3)
	{
		if($dying_source==1)
		{
			echo create_drop_down( "cbo_out_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select --", $company, "" );
		}	
		else if($dying_source==3 && $issue_purpose==1)
		{
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		}	
		else if($dying_source==3 && $issue_purpose==2)
		{
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,21,24) and a.status_active=1 group by a.id order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		}	
		else if($dying_source==3)
		{	
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		}	
		else
		{
			echo create_drop_down( "cbo_out_company", 170, $blank_array,"", 1, "-- Select --", 0, "",0 );
		}		
	}
	else if( $basis_id==4)
	{
		if($dying_source==1 || $dying_source==3)
		{
			echo create_drop_down( "cbo_out_company", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "$company_id", "","" );
		}
		/*else if($dying_source==3)
		{
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=21 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Sewing Company--", 1, "" );
		}*/
		else
		{
			echo create_drop_down( "cbo_out_company", 170, $blank_array,"",1, "--Select Sewing Company--", 1, "" );
		}
	
	}
	else if($basis_id==6)
	{
		echo create_drop_down( "cbo_out_company", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "$company_id", "","" );
	}
	
	else if( $basis_id==2)
	{
		//echo $dying_source.'==='.$issue_purpose;die;
		if($dying_source==1)
		{		
			echo create_drop_down( "cbo_out_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select --", $company, "" );
		}
		else if($dying_source==3 && $issue_purpose==1)
		{
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		}
		else if($dying_source==3 && $issue_purpose==2)
		{
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,21,24) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		}
		else if($dying_source==3)
		{
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		}
		else if($dying_source==0)
		{	
			echo create_drop_down( "cbo_out_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );	
		}
	}
	else if($basis_id==5 || $basis_id==7 )
	{
		echo create_drop_down( "cbo_out_company", 170, "select id,company_name from lib_company  where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $selected,"","0" );
	}
	else
	{
		echo create_drop_down( "cbo_out_company", 170, "select id,company_name from lib_company  where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $company_id,"",1 );
	}
	
	//$sql = "select department_id,section,within_group,sent_by,sent_to,challan_no,basis from inv_gate_pass_mst where sys_number='$data'";
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 170, "select a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type=20 and c.tag_company=$data and a.status_active=1 and
	a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );  	 
	exit();
}
 
//wo/pi popup here----------------------// 
if ($action=="piworeq_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $cbo_group;die;
	$selected = $company;
	?>     
	<script>
		function js_set_value(str)
		{
			//master part call here
			$("#hidden_tbl_id").val(str);
			parent.emailwindow.hide(); 
		}

		function display_show()
		{
			$("#search_tbl").show();
		}
	</script>

	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="940" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th width="150">Company Name</th>
						<th width="180" align="center" id="search_by_td_up">Basis</th>
						<th width="150" align="center" id="search_by_td_up">Challan No</th>
						<th width="100" align="center">Gate Out ID</th>
						<th width="200">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>         
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<?  
								echo create_drop_down( "cbo_company_id", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
							?>
						</td>
						<td align="center" id="search_by_td">
							<?
								echo create_drop_down( "cbo_basis", 150, $get_pass_basis,"",1, "-- Select --", 0, "" ); 
							?>
						</td>  
						<td align="center">
						<input type="text"  class="text_boxes" id="challan_no" name="challan_no" style="width:140" placeholder="Challan No."/>
						</td>  
						<td align="center">
						<input type="text"  class="text_boxes" id="gate_out_id" name="gate_out_id" style="width:60"/>
						</td>  
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_basis').value+'_'+document.getElementById('challan_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('gate_out_id').value+'_'+<? echo $cbo_group;?>+'_'+document.getElementById('cbo_year_selection').value, 'create_wopireq_search_list_view', 'search_div', 'get_in_entry_controller', 'setFilterGrid(\'list_view\',-1)');display_show();" style="width:100px;" />				
						</td>
					</tr>
					<tr>                  
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							<input type="hidden" id="hidden_tbl_id" value="" />
							<!--END--> 
						</td>
					</tr>    
				</tbody>       
			</table>
			<table style="display:none;" id="search_tbl" width="810" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th colspan="6" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
					</tr>
				</thead>
			</table>    
			<div align="center" valign="top" id="search_div"> </div> 
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_wopireq_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_basis = str_replace("'","",$ex_data[1]);
	$txt_challan_no = str_replace("'","",$ex_data[2]);
	
	$txt_date_from =str_replace("'","",$ex_data[3]);
	$txt_date_to = str_replace("'","",$ex_data[4]);
	$company = str_replace("'","",$ex_data[0]);
	$gate_out_id = str_replace("'","",$ex_data[5]);
	$cbo_group= str_replace("'","",$ex_data[6]);
  
	// if($company!=0) $com_cond= " and b.company_id=$company "; else $com_cond="";
	if($cbo_group==1) {
		if($company!=0) $com_cond= " and b.sent_to='$company' "; else $com_cond="";
	}else{
		if($company!=0) $com_cond= " and b.company_id=$company "; else $com_cond="";
	}

	$basis_cond=$challan_no_cond="";
	$gate_out_id_cond=$gate_in_id_cond="";
	if ($txt_basis!=0) $basis_cond= " and b.basis=$txt_basis";
	if ($txt_challan_no!='') $challan_no_cond= " and b.challan_no like '%$txt_challan_no%'";
	if ($gate_out_id!='') $gate_out_id_cond= " and b.sys_number like '%$gate_out_id'";
	if ($gate_out_id!='') $gate_in_id_cond= " and b.gate_pass_no like '%$gate_out_id%'";

	$sql_cond="";
	if($db_type==0)
	{
		if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.out_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	}
	else if($db_type==2)
	{
		if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.out_date  between '".change_date_format($txt_date_from,'mm-dd-yyyy','-',1)."' and '".change_date_format($txt_date_to,'mm-dd-yyyy','-',1)."'";
	}

	$sql_get_pass_qnty="select b.sys_number, sum(c.quantity) as quantity from inv_gate_pass_mst b, inv_gate_pass_dtls c where b.id=c.mst_id $com_cond $basis_cond $challan_no_cond $sql_cond $gate_out_id_cond group by b.sys_number";
	$sql_get_in_qnty="select b.gate_pass_no ,sum(c.quantity) as quantity  from inv_gate_in_mst b, inv_gate_in_dtl c where b.id=c.mst_id $com_cond $basis_cond $challan_no_cond $sql_cond $gate_in_id_cond group by b.gate_pass_no";

	$sql_get_in_qnty_res=sql_select($sql_get_in_qnty);
	$get_in_qnty_arr=array();
	foreach($sql_get_in_qnty_res as $key=>$value)
	{
		$get_in_qnty_arr[$value[csf("gate_pass_no")]] +=$value[csf("quantity")];
	}

	$sql_get_pass_qnty_res=sql_select($sql_get_pass_qnty);
	$get_pass_qnty_arr=array();
	foreach ($sql_get_pass_qnty_res as $key => $value)
	{
		//if($value[csf("quantity")] != $get_in_qnty_arr[$value[csf("sys_number")]])

		$get_pass_qnty_arr[$value[csf("sys_number")]] +=$value[csf("quantity")];

		// if($get_in_qnty_arr[$value[csf("sys_number")]]==$value[csf("quantity")])
		// {
		// 	$totally_complete_challan_arr[$value[csf("sys_number")]]=$value[csf("sys_number")];
		// }	
	}

	$totally_complete_challan= "";
	if(count($totally_complete_challan_arr)>0)
	{
		$totally_complete="'".implode("','", $totally_complete_challan_arr)."'";
		$totally_complete_challan=" and b.sys_number not in ($totally_complete) ";
	}		

	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$ex_data[7] ";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[7] ";
	}
	
 
	$sql = "SELECT a.id as id, b.sys_number_prefix_num, b.sys_number, b.company_id, b.basis, a.out_date, b.challan_no, LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) AS gate_pass_dtls_id 
	from inv_gate_out_scan a, inv_gate_pass_mst b, inv_gate_pass_dtls c 
	where b.sys_number=a.gate_pass_id and b.id=c.mst_id and b.status_active=1  and b.is_deleted=0 
	and b.within_group <> 2 and b.returnable <> 2  $com_cond $basis_cond $challan_no_cond $sql_cond $gate_out_id_cond $year_cond
	group by a.id, b.sys_number_prefix_num, b.sys_number, b.company_id, b.basis, a.out_date, b.challan_no 
	order by b.sys_number_prefix_num";	

	//echo $sql;

	//$get_pass_basis=array(1=>"Independent",2=>"Challan(Yarn)",3=>"Challan(Gray Fabric)",4=>"Challan(Finish Fabric)",5=>"Challan(General Item)",6=>"Challan(Trims)",6=>"Challan(Dyes & Chemical)",7=>"Challan(Trims)");
	$result = sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	// $arr=array(0=>$company_library,2=>$get_pass_basis);
	// echo create_list_view("list_view", "Company Name,Gate Out ID,Basis,Out Date,Challan No","150,150,170,100,180","800","230",0, $sql , "js_set_value", "sys_number,basis,gate_pass_dtls_id", "", 1, "company_id,0,basis,0,0", $arr, "company_id,sys_number,basis,out_date,challan_no", "",'','0,0,0,0,0,0') ;	

	?>
	<div style="margin-top:5px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="150">Company Name</th>
				<th width="150">Gate Out ID</th>
				<th width="170">Basis</th>
				<th width="100">Out Date</th>
				<th width="180">Challan No</th>
			</thead>
		</table>
		<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="list_view">
				<?
				$i = 1;
				foreach ($result as $row) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					if($get_pass_qnty_arr[$row[csf("sys_number")]] != $get_in_qnty_arr[$row[csf("sys_number")]])
					{
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
							onClick="js_set_value('<? echo $row[csf('sys_number')]; ?>');">
							<td width="40"><? echo $i; ?></td>
							<td width="150"><p>&nbsp;<? echo $company_library[$row[csf('company_id')]]; ?></p></td>
							<td width="150" align="center"><p><? echo $row[csf('sys_number')]; ?></p></td>
							<td width="170" align="center"><p><? echo $row[csf('basis')]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo change_date_format($row[csf('out_date')]); ?>&nbsp;</p></td>
							
							<td width="180" align="center"><? echo $row[csf('challan_no')]; ?></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
			</table>
		</div>
	</div>

	<?
	exit();	
}

if($action=="create_wopireq_search_list_view_back_up")
{
 	$ex_data = explode("_",$data);
	$txt_basis = str_replace("'","",$ex_data[1]);
	$txt_challan_no = str_replace("'","",$ex_data[2]);
	
	$txt_date_from =str_replace("'","",$ex_data[3]);
	$txt_date_to = str_replace("'","",$ex_data[4]);
	$company = str_replace("'","",$ex_data[0]);
	$gate_out_id = str_replace("'","",$ex_data[5]);
	$cbo_group= str_replace("'","",$ex_data[6]);
  
	// if($company!=0) $com_cond= " and b.company_id=$company "; else $com_cond="";
	if($cbo_group==1) {
		if($company!=0) $com_cond= " and b.sent_to='$company' "; else $com_cond="";
	}else{
		if($company!=0) $com_cond= " and b.company_id=$company "; else $com_cond="";
	}

	$basis_cond=$challan_no_cond="";
	$gate_out_id_cond=$gate_in_id_cond="";
	if ($txt_basis!=0) $basis_cond= " and b.basis=$txt_basis";
	if ($txt_challan_no!='') $challan_no_cond= " and b.challan_no like '%$txt_challan_no%'";
	if ($gate_out_id!='') $gate_out_id_cond= " and b.sys_number like '%$gate_out_id'";
	if ($gate_out_id!='') $gate_in_id_cond= " and b.gate_pass_no like '%$gate_out_id%'";

	$sql_cond="";
	if($db_type==0)
	{
		if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.out_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	}
	else if($db_type==2)
	{
		if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.out_date  between '".change_date_format($txt_date_from,'mm-dd-yyyy','-',1)."' and '".change_date_format($txt_date_to,'mm-dd-yyyy','-',1)."'";
	}

	$sql_get_pass_qnty="select b.sys_number, sum(c.quantity) as quantity from inv_gate_pass_mst b, inv_gate_pass_dtls c where b.id=c.mst_id $com_cond $basis_cond $challan_no_cond $sql_cond $gate_out_id_cond group by b.sys_number";
	$sql_get_in_qnty="select b.gate_pass_no ,sum(c.quantity) as quantity  from inv_gate_in_mst b, inv_gate_in_dtl c where b.id=c.mst_id $com_cond $basis_cond $challan_no_cond $sql_cond $gate_in_id_cond group by b.gate_pass_no";

	$sql_get_in_qnty_res=sql_select($sql_get_in_qnty);
	$get_in_qnty_arr=array();
	foreach($sql_get_in_qnty_res as $key=>$value)
	{
		$get_in_qnty_arr[$value[csf("gate_pass_no")]] +=$value[csf("quantity")];
	}

	$sql_get_pass_qnty_res=sql_select($sql_get_pass_qnty);
	$totally_complete_challan_arr=array();
	foreach ($sql_get_pass_qnty_res as $key => $value)
	{
		if($get_in_qnty_arr[$value[csf("sys_number")]]==$value[csf("quantity")])
		{
			$totally_complete_challan_arr[$value[csf("sys_number")]]=$value[csf("sys_number")];
		}	
	}

	$totally_complete_challan= "";
	if(count($totally_complete_challan_arr)>0)
	{
		$totally_complete="'".implode("','", $totally_complete_challan_arr)."'";
		$totally_complete_challan=" and b.sys_number not in ($totally_complete) ";
	}		

	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$ex_data[7] ";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[7] ";
	}
	
 	/*$sql = "select a.id as id,b.sys_number_prefix_num ,b.sys_number, b.company_id,b.basis ,a.out_date,b.challan_no 
		from inv_gate_out_scan a,inv_gate_pass_mst b where b.sys_number=a.gate_pass_id and	b.status_active=1 and b.within_group=$cbo_group and b.is_deleted=0  and b.sys_number not in(select gate_pass_no as sys_number from inv_gate_in_mst where  gate_pass_no is not null and status_active=1 and is_deleted=0) $com_cond $basis_cond $sql_cond $gate_out_id_cond  order by b.sys_number_prefix_num";*/	
	// $sql = "SELECT a.id as id, b.sys_number_prefix_num, b.sys_number, b.company_id, b.basis, a.out_date, b.challan_no, LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) AS gate_pass_dtls_id 
	// from inv_gate_out_scan a, inv_gate_pass_mst b, inv_gate_pass_dtls c 
	// where b.sys_number=a.gate_pass_id and b.id=c.mst_id and b.status_active=1 and b.within_group=$cbo_group and b.is_deleted=0 $totally_complete_challan $com_cond $basis_cond $challan_no_cond $sql_cond $gate_out_id_cond $year_cond
	// group by a.id, b.sys_number_prefix_num, b.sys_number, b.company_id, b.basis, a.out_date, b.challan_no 
	// order by b.sys_number_prefix_num";	

	$sql = "SELECT a.id as id, b.sys_number_prefix_num, b.sys_number, b.company_id, b.basis, a.out_date, b.challan_no, LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) AS gate_pass_dtls_id 
	from inv_gate_out_scan a, inv_gate_pass_mst b, inv_gate_pass_dtls c 
	where b.sys_number=a.gate_pass_id and b.id=c.mst_id and b.status_active=1  and b.is_deleted=0 
	and b.within_group <> 2 and b.returnable <> 2 $totally_complete_challan $com_cond $basis_cond $challan_no_cond $sql_cond $gate_out_id_cond $year_cond
	group by a.id, b.sys_number_prefix_num, b.sys_number, b.company_id, b.basis, a.out_date, b.challan_no 
	order by b.sys_number_prefix_num";	

	//echo $sql;

	//$get_pass_basis=array(1=>"Independent",2=>"Challan(Yarn)",3=>"Challan(Gray Fabric)",4=>"Challan(Finish Fabric)",5=>"Challan(General Item)",6=>"Challan(Trims)",6=>"Challan(Dyes & Chemical)",7=>"Challan(Trims)");
	$result = sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$arr=array(0=>$company_library,2=>$get_pass_basis);
	echo create_list_view("list_view", "Company Name,Gate Out ID,Basis,Out Date,Challan No","150,150,170,100,180","800","230",0, $sql , "js_set_value", "sys_number,basis,gate_pass_dtls_id", "", 1, "company_id,0,basis,0,0", $arr, "company_id,sys_number,basis,out_date,challan_no", "",'','0,0,0,0,0,0') ;	
	exit();	
}

if($action=="populate_main_from_data")
{
	$data=explode("**", $data);
	$companyID=$data[1];
 	// $sql = "SELECT a.id, a.department_id, a.section, a.attention, a.company_id, a.com_location_id, a.location_id, a.returnable, a.out_date, a.carried_by, a.est_return_date, a.within_group, a.sent_by, a.sent_to, a.challan_no, a.basis from inv_gate_pass_mst a, inv_gate_out_scan c where a.sys_number=c.gate_pass_id and a.sys_number='$data[0]'";
	$sql = "SELECT a.id, a.department_id, a.section, a.attention, a.company_id, a.com_location_id, a.location_id, a.returnable, a.out_date, a.carried_by, a.est_return_date, a.within_group, a.sent_by, a.sent_to, a.challan_no, a.basis from inv_gate_pass_mst a, inv_gate_out_scan c 
	where a.sys_number=c.gate_pass_id and a.sys_number='$data[0]' and a.WITHIN_GROUP <> 2 and a.RETURNABLE <> 2 ";
	// echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{	
        $gate_pass_id=$row[csf('id')];
        $sent_to=$row[csf('sent_to')];
        $chalan_no=$row[csf('challan_no')];
        $company_id=$row[csf('company_id')];
		
		$basis=$row[csf('basis')];
		$returnable=$row[csf('returnable')];
		if ($returnable==1) 
		{
			echo "$('#returnable_item_dtls').removeClass('formbutton_disabled');\n";
			echo "$('#returnable_item_dtls').addClass('formbutton');\n";
		}
		$within_group=$row[csf('within_group')];
		echo "$('#cbo_group').val(".$row[csf("within_group")].");\n";
		echo "$('#cbo_group').attr('disabled',true);\n";
  		echo "$('#cbo_department_name').val(".$row[csf("department_id")].");\n";
		echo "$('#cbo_section').val(".$row[csf("section")].");\n";
		echo "$('#txt_receive_from').val('".$row[csf("sent_by")]."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		echo "$('#txt_carried_by').val('".$row[csf("carried_by")]."');\n";
		echo "$('#cbo_returnable').val('".$row[csf("returnable")]."');\n";
		echo "$('#txt_out_date').val('".change_date_format($row[csf("out_date")])."');\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("est_return_date")])."');\n";		
		// echo "load_drop_down( 'requires/get_in_entry_controller','".$company_id."', 'load_drop_down_com_location', 'com_location_td' );";
		// echo "$('#cbo_com_location_id').val(".$row[csf("com_location_id")].");\n";
		if($row[csf("within_group")]==1)
		{
			echo "set_field_level_access(".$row[csf("sent_to")].");\n";
			echo "$('#cbo_company_name').val(".$row[csf("sent_to")].");\n";
			echo "$('#cbo_company_name').attr('disabled',true);\n";
			echo "load_drop_down( 'requires/get_in_entry_controller','".$sent_to."', 'load_drop_down_com_location', 'com_location_td' );";
			echo "$('#cbo_com_location_id').val(".$row[csf("location_id")].");\n";

			echo "load_drop_down( 'requires/get_in_entry_controller','".$row[csf("company_id")]."', 'load_drop_down_out_location', 'out_location_td' );";
			echo "$('#cbo_out_location_id').val('".$row[csf("com_location_id")]."');\n";
		}
		else
		{

			echo "set_field_level_access(".$row[csf("company_id")].");\n";
			echo "$('#cbo_company_name').val(".$row[csf("company_id")].");\n";
			echo "$('#cbo_company_name').attr('disabled',true);\n";
			echo "load_drop_down( 'requires/get_in_entry_controller','".$company_id."', 'load_drop_down_com_location', 'com_location_td' );";
			echo "$('#cbo_com_location_id').val(".$row[csf("com_location_id")].");\n";
		}	
		
		if($row[csf("within_group")]==1)
		{
			echo "load_drop_down( 'requires/get_in_entry_controller',$company_id, 'load_drop_down_out_company', 'sent_td');\n";
			echo "$('#cbo_out_company').val('".$row[csf("company_id")]."');\n";
			echo "$('#cbo_party_type').attr('disabled',true);\n";
		}
		if($row[csf("within_group")]==2 && $row[csf("returnable")]==1)
		{
			echo "$('#cbo_party_type').val(2);\n";
			echo "load_drop_down( 'requires/get_in_entry_controller','$gate_pass_id'+'_'+$basis+'_'+'$sent_to', 'load_drop_down_out_supplier', 'sent_td');\n";
			echo "$('#cbo_party_type').attr('disabled',true);\n";
			echo "$('#cbo_out_company').attr('disabled',true);\n";
		}			
  	}	
	exit();
}

//right side product list create here-----------//
if($action=="show_product_listview")
{
	$data=explode("**", $data);
	$sql_get_in_qnty="SELECT c.get_pass_dtlsid, b.sys_number, b.gate_pass_no, c.item_description,sum(c.quantity) as quantity 
	from inv_gate_in_mst b, inv_gate_in_dtl c 
	where b.id=c.mst_id and b.gate_pass_no='$data[0]' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by c.get_pass_dtlsid, b.sys_number, b.gate_pass_no, c.item_description";
	$gateInSysNumber="";
	$get_in_qnty_arr_dtls=array();
	foreach(sql_select($sql_get_in_qnty) as $key=>$value)
	{
		$get_in_qnty_arr[$value[csf("gate_pass_no")]] +=$value[csf("quantity")];		
		$get_in_qnty_arr_with_item[$value[csf("gate_pass_no")]][$value[csf("item_description")]] +=$value[csf("quantity")];		
		$get_in_qnty_arr_dtls[$value[csf("gate_pass_no")]][$value[csf("item_description")]][$value[csf("get_pass_dtlsid")]]+=$value[csf("quantity")];
		$gateInSysNumber=$value[csf("sys_number")];
	}	
	//echo "<pre>";
	//print_r($get_in_qnty_arr_dtls);
	
	$sql_get_pass_qnty="select b.sys_number,sum(c.quantity) as quantity from inv_gate_pass_mst b, inv_gate_pass_dtls c where b.id=c.mst_id  and  b.sys_number='$data[0]' group by b.sys_number"; 
	
	foreach(sql_select($sql_get_pass_qnty) as $key => $value)
	{
		//$prev_qnty=$get_in_qnty_arr_dtls[$value[csf("sys_number")]][$value[csf("item_description")]][$value[csf("id")]];
		$prev_qnty=$get_in_qnty_arr[$value[csf("sys_number")]];	
		if($prev_qnty==$value[csf("quantity")])
		{
			$totally_complete_challan_arr[$value[csf("sys_number")]]=$value[csf("sys_number")];
		}
	}
	
	$totally_complete_challan= ""; 
	if(count($totally_complete_challan_arr)>0)
	{
		$totally_complete="'".implode("','", $totally_complete_challan_arr)."'";
		$totally_complete_challan=" and b.sys_number not in ($totally_complete) ";
	}
	
	$tbl_row=0;

	// $sql = "SELECT a.id as getpassdtlsid, sample_id, a.item_category_id, a.item_description, a.quantity, a.uom, a.rate, a.amount, a.remarks, a.buyer_order,a.BUYER_ORDER_ID as PO_ID, b.sys_number,a.reject_qty 
	// from inv_gate_pass_dtls a, inv_gate_pass_mst b, inv_gate_out_scan c 
	// where b.id=a.mst_id and b.sys_number=c.gate_pass_id and b.sys_number='$data[0]' $totally_complete_challan and a.status_active=1 and a.is_deleted=0"; 
	$sql = "SELECT a.id as getpassdtlsid, sample_id, a.item_category_id, a.item_description, a.quantity, a.uom, a.rate, a.amount, a.remarks, a.buyer_order,a.BUYER_ORDER_ID as PO_ID, b.sys_number,a.reject_qty , b.within_group ,b.returnable
	from inv_gate_pass_dtls a, inv_gate_pass_mst b, inv_gate_out_scan c 
	where b.id=a.mst_id and b.sys_number=c.gate_pass_id and b.sys_number='$data[0]' $totally_complete_challan and a.status_active=1 and a.is_deleted=0"; 
  	//echo $sql;
    $result=sql_select($sql);

	foreach($result as $row)
	{
		$po_id .= $row['PO_ID'].",";
	}
	$all_btb_id = ltrim(implode(",", array_unique(explode(",", chop($po_id, ",")))), ',');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$style_sql = "SELECT B.ID,A.BUYER_NAME,A.STYLE_REF_NO FROM WO_PO_DETAILS_MASTER A, WO_PO_BREAK_DOWN B WHERE A.JOB_NO = B.JOB_NO_MST AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.ID in ($all_btb_id)";
	//echo $style_sql;
	$style_result=sql_select($style_sql);
	foreach($style_result as $row)
	{
		$buyerArr[$row['ID']]['BUYER_NAME'] = $row['BUYER_NAME'];
		$buyerArr[$row['ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
	}

	if(count($result)>0)
	{
		foreach($result as $row)
		{
			if($row[csf("within_group")]==2 && $row[csf("returnable")]==2)
			{
				?>
					<tr>
						<td colspan="13" align="center">
							<h3 style="color: red;" >** IN this gate pass Within group and Returnable is NO</h3>
						</td>
					</tr>
				<?
			}
			else
			{
				if($get_in_qnty_arr_dtls[$row[csf("sys_number")]][$row[csf("item_description")]][$row[csf("getpassdtlsid")]]!=$row[csf("quantity")])
				{			
					$tbl_row++;				 
					//$balance_qnty=$row[csf('quantity')]-$get_in_qnty_arr_with_item[$row[csf("sys_number")]][$row[csf("item_description")]];
					$balance_qnty=$row[csf('quantity')]-$get_in_qnty_arr_dtls[$row[csf("sys_number")]][$row[csf("item_description")]][$row[csf("getpassdtlsid")]];
					// echo $balance_qnty;
					?>
					<tr class="" id="tr_<? echo $tbl_row; ?>" style="height:10px;">
						<td>
							<? 
								echo create_drop_down( "cboitemcategory_".$tbl_row, 120,$item_category,"",1, "-- Select --",$row[csf('item_category_id')] , "",1 ); 
							?>
						</td>
						<td>
							<? 
							echo create_drop_down( "cbosample_".$tbl_row, 100, "select id,sample_name from lib_sample where status_active=1 order by sample_name","id,sample_name",1, "-- Select --", $row[csf('sample_id')],0,1 ); 
							?> 
						</td>
						<td><input type="text" name="txtitemdescription_<? echo $tbl_row; ?>" id="txtitemdescription_<? echo $tbl_row; ?>" class="text_boxes" style="width:200px;" value="<? echo $row[csf('item_description')];?>" disabled></td>
						
						<td><input type="text" name="txtcalanquantity_<? echo $tbl_row; ?>" id="txtcalanquantity_<? echo $tbl_row; ?>" class="text_boxes_numeric" onKeyUp="fn_calculate_amount()"   value="<? echo $row[csf('quantity')];?>" style="width:60px;" disabled></td>
						
						<td><input type="text" name="txtquantity_<? echo $tbl_row; ?>" id="txtquantity_<? echo $tbl_row; ?>" class="text_boxes_numeric required" placeholder="<? echo $balance_qnty; ?>"  value="" style="width:60px;" onBlur="fn_check_quantity(<? echo $tbl_row; ?>);" ></td>
						<td><input type="text" name="txtRejQuantity_<? echo $tbl_row; ?>" id="txtRejQuantity_<? echo $tbl_row; ?>" class="text_boxes_numeric rejrequired" placeholder="<? echo $row[csf('reject_qty')];  ?>"  value="<? echo $row[csf('reject_qty')];  ?>" style="width:60px;" ></td>
						<td><? echo create_drop_down( "cbouom_".$tbl_row, 60, $unit_of_measurement,"", 1, "-- Select--", $row[csf('uom')], "",1 ); ?></td>
						<!--<td><input type="text" name="txtuomqty_<? //echo $tbl_row; ?>" id="txtuomqty_<? //echo $tbl_row; ?>" class="text_boxes"   value="" style="width:60px;"></td>-->
						<td><input type="text" name="txtrate_<? echo $tbl_row; ?>" id="txtrate_<? echo $tbl_row; ?>" class="text_boxes_numeric" onKeyUp="fn_calculate_amount()"  value="<? echo $row[csf('rate')];?>" style="width:60px" disabled></td>
						<td><input type="text" name="txtamount_<? echo $tbl_row; ?>" id="txtamount_<? echo $tbl_row; ?>" class="text_boxes_numeric" style="width:80px"  value="<? echo $row[csf('amount')];?>" randomly disabled></td>

						<td><input type="text" name="txtorder_<? echo $tbl_row; ?>" id="txtorder_<? echo $tbl_row; ?>" class="text_boxes" style="width:80px"      value="<? echo $row[csf('buyer_order')];?>" readonly disabled></td>
						<?
						//if($row['PO_ID']>1){
						?>
						<td><input type="text" name="cbobuyer_<? echo $tbl_row; ?>" id="cbobuyer_<? echo $tbl_row; ?>" class="text_boxes" style="width:80px"  value="<? echo $buyer_arr[$buyerArr[$row['PO_ID']]['BUYER_NAME']];?>" readonly disabled></td>

						<td><input type="text" name="txstyle_<? echo $tbl_row; ?>" id="txstyle_<? echo $tbl_row; ?>" class="text_boxes" style="width:80px"      value="<? echo $buyerArr[$row['PO_ID']]['STYLE_REF_NO'];?>" readonly disabled></td>
						<?//}?>

						<td><input type="text" name="txtremarks_<? echo $tbl_row; ?>" id="txtremarks_<? echo $tbl_row; ?>" class="text_boxes" style="width:150px"    value="<? echo $row[csf('remarks')];?>">
						<input type="hidden" id="updatedtlsid_<? echo $tbl_row; ?>" name="updatedtlsid_<? echo $tbl_row; ?>" value="" /> 
						<input type="hidden" id="getpassdtlsid_<? echo $tbl_row; ?>" name="getpassdtlsid_<? echo $tbl_row; ?>" value="<? echo $row[csf('getpassdtlsid')];?>" />
						<input type="hidden" id="fabriccolorid_<? echo $tbl_row; ?>" name="fabriccolorid_<? echo $tbl_row; ?>" value="" />
						</td>
					</tr>
					<?
					$i++;
				}
			}
			
		}
	}
	else
	{
		?>
			<h3 style="color:#FF0000; width:600px;"> Gate In already have been done. Your gate in system ID:  <? echo $gateInSysNumber; die;	?> </h3>
	    <?
	}
}  
  
if($action=="wo_pi_req_product_form_input")
{
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$product_name_details = $ex_data[1];
	$wo_pi_req_ID = $ex_data[2]; //pi,wo,req dtls table ID
 	$category = $ex_data[3];
	
	if($receive_basis==1) // pi basis
	{	
		$sql = "select uom,quantity,net_pi_rate as rate,amount from com_pi_item_details where id=$wo_pi_req_ID";
 	}  
	else if($receive_basis==2) // wo basis
	{
		$sql = "select uom,supplier_order_quantity as quantity,rate,amount from wo_non_order_info_dtls where id=$wo_pi_req_ID";
 	}
	else if($receive_basis==3) // requisition basis
	{
		$sql = "select cons_uom as uom,quantity,rate,amount from inv_purchase_requisition_dtls  where id=$wo_pi_req_ID";	
 	}	
 	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{ 
		echo "$('#txt_item_description').val('".$product_name_details."');\n";
		echo "$('#cbo_uom').val(".$row[csf("uom")].");\n";
		echo "$('#cbo_uom').attr('disabled',true);\n";
		echo "$('#txt_quantity').val(".$row[csf("quantity")].");\n";
		echo "$('#txt_rate').val('".number_format($row[csf("rate")],$dec_place[3],".","")."');\n";
		echo "$('#txt_amount').val('".number_format($row[csf("amount")],$dec_place[4],".","")."');\n";
  	}	
	exit();	
}  
  
if($action=="returnable_item_dtls_pupup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var permission='<? echo $permission; ?>';

		function fnc_returnable_item_dtls( operation )
		{
			if (operation==2) {
				alert("Delete restricted");return;
			}
			var row_num=$('#tbl_returnable_details tr').length-1;
			var data_all=""; var count=1;
			for (var i=1; i<=row_num; i++)
			{
				/*alert(count(row_num));
				if (form_validation('qty_'+i,'Quantity')==false)
				{
					return;
				}*/
				var qty=$('#qty_'+i).val()*1;
				if(qty != "") count+=1;

				data_all=data_all+get_submitted_data_string('txt_system_id*gate_in_system_id*itemDescription_'+i+'*cboItemCat_'+i+'*qty_'+i+'*cbouom_'+i+'*txtRemarks_'+i+'*getPassId_'+i,"../../");
			}
			if(count<=1)
			{
				alert("Please Input Quantity.");
				return;
			}
			// alert(row_num);return;
			var data="action=save_update_delete_returnable&operation="+operation+'&total_row='+row_num+data_all;
			// alert(data);return;
			//freeze_window(operation);
			http.open("POST","get_in_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_returnable_item_reponse;
		}

		function fnc_returnable_item_reponse()
		{
			if(http.readyState == 4)
			{
				//alert(http.responseText);
				var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0)
				{
					alert('Data Saved Successfully');
					set_button_status(1, permission, 'fnc_returnable_item_dtls',1);
					//parent.emailwindow.hide();
				}
				if (reponse[0]==1) 
				{
					alert('Data Update Successfully');
					set_button_status(1, permission, 'fnc_returnable_item_dtls',1);
				}
				if (reponse[0]==10) 
				{
					alert('Invalid Operation');return;
				}
				if (reponse[0]==11) 
				{
					alert('This Gate Pass No. Already Gate Out. Changed Not Allowed.');return;
				}
				if (reponse[0]==30) 
				{
					alert('Gate in Quantity is more than pass Quantity.');return;
				}
				parent.emailwindow.hide();
			}
		}

		function fn_check_return_qty(id)
		{
			var placeholder_value=$("#qty_"+id).attr("placeholder");
			var field_value=$("#qty_"+id).val();
			if(field_value*1 > placeholder_value*1)
			{
				alert("Qnty Excceded by"+(placeholder_value-field_value));
				$("#qty_"+id).val('');
			}
		}
	</script>
	</head>

	<body>
	<div align="center" style="width:100%;" >
		<? echo load_freeze_divs ("../../",$permission,1); ?>
		<fieldset>
			<form id="returnable_1" autocomplete="off">
				<input type="hidden" id="txt_system_id" name="txt_system_id" value="<? echo str_replace("'","",$txt_pass_id) ?>"/>
				<input type="hidden" id="gate_in_system_id" name="gate_in_system_id" value="<? echo $gate_in_system_id ?>"/>
				<table width="780" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
					<thead>
						<tr>
							<th width="50">Sl</th>
							<th width="130">Item Catagory</th>
							<th width="130">Item Description</th>
							<th width="130">Qty.</th>
							<th width="130">UOM</th>
							<th width="130">Remarks</th>
							<!-- <th ></th> -->
						</tr>
					</thead>
					<tbody>
						<?
						//id = $update_id
						// ============================Start================
						$sql_get_in_qnty="SELECT id,entry_form,item_catagory_id,item_description,quantity,uom,remarks,gate_pass_id,gate_pass_sys_id from returnable_item_dtls where gate_pass_sys_id='$txt_pass_id' and entry_form=363 and status_active=1 and is_deleted=0 order by id";
						$get_in_data=sql_select($sql_get_in_qnty);
						$get_in_qnty_arr_dtls=array();
						foreach($get_in_data as $key=>$value)
						{
							$get_in_qnty_arr_dtls[$value[csf("item_catagory_id")]][$value[csf("gate_pass_sys_id")]][$value[csf("item_description")]][$value[csf("uom")]] +=$value[csf("quantity")];
						}
						// echo "<pre>";print_r($get_in_qnty_arr_dtls);die;
						$get_pass_data=sql_select("SELECT id,entry_form,item_catagory_id,item_description,quantity,uom,remarks,gate_pass_id,gate_pass_sys_id from returnable_item_dtls where gate_pass_sys_id='$txt_pass_id' and status_active=1 and is_deleted=0 and entry_form=251 order by id");
						$get_pass_qnty_arr_dtls=array();
						foreach($get_pass_data as $key=>$value)
						{
							$get_pass_qnty_arr_dtls[$value[csf("item_catagory_id")]][$value[csf("gate_pass_sys_id")]][$value[csf("item_description")]][$value[csf("uom")]] +=$value[csf("quantity")];
						}
						// echo "<pre>";print_r($get_pass_qnty_arr_dtls);die;
						// ============================End===========================
						$data_array=sql_select("SELECT id,entry_form,item_catagory_id,item_description,quantity,uom,remarks,gate_pass_id,gate_pass_sys_id from returnable_item_dtls where gate_pass_sys_id='$txt_pass_id' and status_active=1 and is_deleted=0 and entry_form=363 and gate_in_sys_no='$gate_in_system_id' order by id");
						if( count($data_array)>0 )
						{
							$i=0;
							foreach( $data_array as $row )
							{
								$i++;
								$get_pass_qnty=$get_pass_qnty_arr_dtls[$row[csf("item_catagory_id")]][$row[csf("gate_pass_sys_id")]][$row[csf("item_description")]][$row[csf("uom")]];
								$get_in_qnty=$get_in_qnty_arr_dtls[$row[csf("item_catagory_id")]][$row[csf("gate_pass_sys_id")]][$row[csf("item_description")]][$row[csf("uom")]];
								$balance_qnty=$get_pass_qnty-($get_in_qnty-$row[csf('quantity')]);
								if ($row[csf('quantity')]==0) 
								{
									$row[csf('quantity')]="";
								}

								?>
									<tr id="settr_1" align="center">
										<td>
										<? echo $i;?>
										</td>
										<td>
										<? echo create_drop_down( "cboItemCat_".$i, 130, $item_category,"", 1, "-- Select Item --", $row[csf('item_catagory_id')], "",1,""); ?>
										<input type="hidden" id="getPassId_<? echo $i;?>"   name="getPassId_<? echo $i;?>" style="width:90%"  class="text_boxes"  value="<? echo $row[csf('gate_pass_id')]; ?>" />
										</td>
										<td>
										<input type="text" id="itemDescription_<? echo $i;?>"   name="itemDescription_<? echo $i;?>" style="width:90%"  class="text_boxes"  value="<? echo $row[csf('item_description')]; ?>"  disabled=""/>
										</td>
										<td>
										<input type="text" id="qty_<? echo $i;?>"   name="qty_<? echo $i;?>" style="width:90%"  class="text_boxes_numeric required"  value="<? echo $row[csf('quantity')]; ?>" placeholder="<? echo $balance_qnty; ?>" onBlur="fn_check_return_qty(<? echo $i; ?>);"/>
										</td>
										<td>
										<? echo create_drop_down( "cbouom_".$i, 130, $unit_of_measurement,"", 1, "-UOM-", $row[csf('uom')], "",1,"1,12,23,27"); ?>
										</td>
										<td>
										<input type="text" id="txtRemarks_<? echo $i;?>"   name="txtRemarks_<? echo $i;?>" style="width:90%"  class="text_boxes"  value="<? echo $row[csf('remarks')]; ?>" />
										</td>
										<!-- <td>
										<input type="button" id="increase_<? //echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? //echo $i; ?> )" />
										<input type="button" id="decrease_<? //echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? //echo $i; ?>);" />
										</td> -->
									</tr>
								<?
							}
						}
						else
						{
							$get_pass_data_array=sql_select("SELECT id,entry_form,item_catagory_id,item_description,quantity,uom,remarks,gate_pass_id,gate_pass_sys_id from returnable_item_dtls where gate_pass_sys_id='$txt_pass_id' and status_active=1 and is_deleted=0 and entry_form=251 order by id");
							if( count($get_pass_data_array)>0 )
							{
								$i=0;
								foreach( $get_pass_data_array as $row )
								{
									if($get_in_qnty_arr_dtls[$row[csf("item_catagory_id")]][$row[csf("gate_pass_sys_id")]][$row[csf("item_description")]][$row[csf("uom")]]!=$row[csf("quantity")])
									{
										$i++;
										$balance_qnty=$row[csf('quantity')]-$get_in_qnty_arr_dtls[$row[csf("item_catagory_id")]][$row[csf("gate_pass_sys_id")]][$row[csf("item_description")]][$row[csf("uom")]];
										?>
										<tr id="settr_1" align="center">
											<td>
											<? echo $i;?>
											</td>
											<td>
											<? echo create_drop_down( "cboItemCat_".$i, 130, $item_category,"", 1, "-- Select Item --", $row[csf('item_catagory_id')], "",1,""); ?>
											<input type="hidden" id="getPassId_<? echo $i;?>"   name="getPassId_<? echo $i;?>" style="width:90%"  class="text_boxes"  value="<? echo $row[csf('gate_pass_id')]; ?>" />
											</td>
											<td>
											<input type="text" id="itemDescription_<? echo $i;?>"   name="itemDescription_<? echo $i;?>" style="width:90%"  class="text_boxes"  value="<? echo $row[csf('item_description')]; ?>" disabled="" />
											</td>
											<td>
											<input type="text" id="qty_<? echo $i;?>" name="qty_<? echo $i;?>" style="width:90%"  class="text_boxes_numeric required"  placeholder="<? echo $balance_qnty;//$row[csf('quantity')]; ?>" value=""  onBlur="fn_check_return_qty(<? echo $i; ?>);"/>
											</td>
											<td>
											<? echo create_drop_down( "cbouom_".$i, 130, $unit_of_measurement,"", 1, "-UOM-", $row[csf('uom')], "",1,"1,12,23,27"); ?>
											</td>
											<td>
											<input type="text" id="txtRemarks_<? echo $i;?>"   name="txtRemarks_<? echo $i;?>" style="width:90%"  class="text_boxes"  value="<? echo $row[csf('remarks')]; ?>" />
											</td>
											<!-- <td>
											<input type="button" id="increase_<? //echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? //echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? //echo $i; ?>);" />
											</td> -->
										</tr>
										<?
									}								
								}
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
							if( count($data_array)>0 )
							{
								echo load_submit_buttons( $permission, "fnc_returnable_item_dtls", 1,0 ,"reset_form('returnable_1','','','','')",1) ;
							}
							else
							{								
								echo load_submit_buttons( $permission, "fnc_returnable_item_dtls", 0,0 ,"reset_form('returnable_1','','','','')",1) ;
							}
							?>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="save_update_delete_returnable")
{
	$process = array( &$_POST );
	//echo "10**string";die;
	extract(check_magic_quote_gpc( $process ));

	$validate_gate_pass=sql_select("select item_catagory_id,gate_pass_sys_id,item_description,uom, sum(quantity) as gate_in_qty from returnable_item_dtls 
	where gate_pass_sys_id=".$txt_system_id." and entry_form=251 
	group by gate_pass_sys_id,item_catagory_id,item_description,uom");

	$gate_pass_qty=0;
	$gate_pass_qty_arr=array();
	foreach($validate_gate_pass as $value)
	{
		//$gate_pass_qty+=$value[csf("gate_in_qty")];
		$gate_pass_qty_arr[$value[csf("gate_pass_sys_id")]][$value[csf("item_catagory_id")]][$value[csf("item_description")]][$value[csf("uom")]] +=$value[csf("gate_in_qty")];
	}
	//echo "<pre>";print_r($gate_pass_qty_arr);//die;
	$sql_get_in_qnty="SELECT id,entry_form,item_catagory_id,item_description,quantity,uom,remarks,gate_pass_id,gate_pass_sys_id from returnable_item_dtls where gate_pass_sys_id=$txt_system_id and entry_form=363 and status_active=1 and is_deleted=0 and gate_in_sys_no not in ($gate_in_system_id) order by id";
	// echo $sql_get_in_qnty;die;
	$get_in_data=sql_select($sql_get_in_qnty);
	$get_in_qnty_arr_dtls=array();
	foreach($get_in_data as $key=>$value)
	{
		$get_in_qnty_arr_dtls[$value[csf("gate_pass_sys_id")]][$value[csf("item_catagory_id")]][$value[csf("item_description")]][$value[csf("uom")]] +=$value[csf("quantity")];
	}
	//echo "<pre>";print_r($get_in_qnty_arr_dtls);die;
	for($i=1; $i<=$total_row; $i++)
	{
		$cboItemCat		= "cboItemCat_".$i;
		$itemDescription = "itemDescription_".$i;
		$cbouom 		= "cbouom_".$i;
		$qty			= "qty_".$i;

		//echo $txt_system_id.'='.$$cboItemCat.'='.$$itemDescription.'='.$$cbouom.'<br>';
		$gate_pass_qty=$gate_pass_qty_arr[str_replace("'","",$txt_system_id)][str_replace("'","",$$cboItemCat)][str_replace("'","",$$itemDescription)][str_replace("'","",$$cbouom)];
		$get_in_qnty=$get_in_qnty_arr_dtls[str_replace("'","",$txt_system_id)][str_replace("'","",$$cboItemCat)][str_replace("'","",$$itemDescription)][str_replace("'","",$$cbouom)];
		$gate_in_qty_total=$get_in_qnty+str_replace("'","",$$qty);
		//echo $gate_pass_qty.'<'.$gate_in_qty_total.'<br>';
		if($gate_pass_qty<$gate_in_qty_total)
		{
			echo "30** Gate in Quantity is more than pass Quantity";
			exit();
		}
	}

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");

		$id=return_next_id( "id", "returnable_item_dtls", 1 );
		$field_array = "id,gate_pass_id,gate_pass_sys_id,gate_in_sys_no,entry_form,item_catagory_id,item_description,quantity,uom,remarks,inserted_by,insert_date"; $data_array = "";
		$idsArr = "";$j=0;
		for ($i=1;$i<=$total_row;$i++) //get_pass_id
		{
			$cboItemCat		= "cboItemCat_".$i;
			$itemDescription = "itemDescription_".$i;
			$qty			= "qty_".$i;
			$cbouom 		= "cbouom_".$i;
			$txtRemarks 	= "txtRemarks_".$i;
			$getPassId 	= "getPassId_".$i;
			if ( str_replace("'", "", $$qty) =="") { $qty=0; }
			else { $qty=$$qty; }
			$j++;
			if ($j!=1){ $data_array .=",";}
			$data_array .="(".$id.",".$$getPassId.",".$txt_system_id.",".$gate_in_system_id.",363,".$$cboItemCat.",".$$itemDescription.",".$qty.",".$$cbouom.",".$$txtRemarks.",".$user_id.",'".$pc_date_time."')";
			$id=$id+1;
		}

 		if($data_array!="")
		{
			//echo "10**insert into returnable_item_dtls(".$field_array.") values ".$data_array."";die;
			$rID=sql_insert("returnable_item_dtls",$field_array,$data_array,0);
		}
		//echo "10**$rID.'##'.$txt_system_id";die;
		//oci_commit($con); oci_rollback($con);
		if($db_type==0)
		{
			if( $rID && $data_array!=""){
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

		if( $rID && $data_array!="")
		{
			oci_commit($con);
			echo "0**";
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
		disconnect($con);
		die;
	}
	if ($operation==1)  // Update Here
	{
		//echo "10**string";die;
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");


		$id=return_next_id( "id", "returnable_item_dtls", 1 );
		$field_array = "id,gate_pass_id,gate_pass_sys_id,gate_in_sys_no,entry_form,item_catagory_id,item_description,quantity,uom,remarks,inserted_by,insert_date"; $data_array = "";
		$idsArr = "";$j=0;
		for ($i=1;$i<=$total_row;$i++) 
		{
			$cboItemCat			= "cboItemCat_".$i;
			$itemDescription 	= "itemDescription_".$i;
			$qty				= "qty_".$i;
			$cbouom 			= "cbouom_".$i;
			$txtRemarks 		= "txtRemarks_".$i;
			$getPassId 			= "getPassId_".$i;
			if ( str_replace("'", "", $$qty) =="") { $qty=0; }
			else { $qty=$$qty; }
			$j++;
			if ($j!=1){ $data_array .=",";}
			$data_array .="(".$id.",".$$getPassId.",".$txt_system_id.",".$gate_in_system_id.",363,".$$cboItemCat.",".$$itemDescription.",".$qty.",".$$cbouom.",".$$txtRemarks.",".$user_id.",'".$pc_date_time."')";
			$id=$id+1;
		}
		//$get_pass_id = str_replace("'", '',$get_pass_id);
		//echo "10**delete from returnable_item_dtls where gate_pass_id=".$get_pass_id." and entry_form=251";die;

		$query = execute_query("delete from returnable_item_dtls where gate_in_sys_no=".$gate_in_system_id." and entry_form=363", 0);

 		if($data_array!="")
		{
			//echo "10**insert into returnable_item_dtls(".$field_array.") values ".$data_array."";die;
			$rID=sql_insert("returnable_item_dtls",$field_array,$data_array,0);
		}
		//echo "10**$rID##$query##$get_pass_id##$flag##$txt_system_id";die;
		//oci_commit($con); oci_rollback($con);
		if($db_type==0)
		{
			if( $rID && $data_array!="" && $query){
				mysql_query("COMMIT");
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}

		if( $rID && $data_array!="" && $query)
		{
			oci_commit($con);
			echo "1**";
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
		disconnect($con);
		die;
	}
}

//data save update delete here--------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if( $operation==0 ) // Insert Here--------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// validate condition for (gate pass>=get in)
		
		$cbo_group=str_replace("'","",$cbo_group);
		$validate_gate_pass=sql_select("SELECT a.id,a.sys_number as pass_sys_number,sum(b.quantity) as pass_qty from  inv_gate_pass_mst a,inv_gate_pass_dtls b
		where a.sys_number=".$txt_pass_id." and a.id=b.mst_id and a.status_active=1 and b.status_active=1 group by a.id,a.sys_number");
		$validate_gate_in=sql_select("SELECT c.gate_pass_no as gate_in_sys_number,sum(d.quantity) as gate_in_qty from  inv_gate_in_mst c,inv_gate_in_dtl d 
		where c.gate_pass_no=".$txt_pass_id." and c.id=d.mst_id and c.status_active=1 and d.status_active=1 group by c.gate_pass_no");

		$gate_pass_qty=0;
		$gate_in_qty=0;$gate_pass_mst_id=0;
		foreach($validate_gate_pass as $row)
		{
			$gate_pass_qty+=$row[csf("pass_qty")]*1;
			$gate_pass_mst_id=$row[csf("id")];
		}
			//echo $gate_pass_qty;		
		foreach($validate_gate_in as $row)
		{
			$gate_in_qty+=$row[csf("gate_in_qty")];
		}
		//echo $gate_in_qty;
		$total_quantity=0;
		for($i=1; $i<=$row_num; $i++)
		{
			$txtquantity="txtquantity_".$i;
			$txtQuantity=(int)str_replace("'","",$$txtquantity);
			$total_quantity+=$txtQuantity;
		}
		//echo "10**".str_replace("'","",$txt_pass_id); die;
		$gate_in_qty_total=$gate_in_qty+$total_quantity;
		
		$txt_pass=str_replace("'","",$txt_pass_id);
		if($txt_pass)
		{
			if(($cbo_group==1 || $cbo_group==2) && count($validate_gate_pass) > 0 )
			{
				if($gate_pass_qty*1<$gate_in_qty_total*1)
				{
					echo "30**Gate in Quantity is more than pass Quantity";oci_rollback($con);disconnect($con);die;
					
				}
			}
		}
		else
		{
			if($cbo_group==1 )
			{
				if($gate_pass_qty<$gate_in_qty_total)
				{
					echo "30**Gate in Quantity is more than pass Quantity";oci_rollback($con);disconnect($con);die;
				}
			}
		}
		
		//echo "select a.sys_number as pass_sys_number,sum(b.quantity) as pass_qty,c.gate_pass_no as gate_in_sys_number,sum(d.quantity) as gate_in_qty from  inv_gate_pass_mst a,inv_gate_pass_dtls b,inv_gate_in_mst c,inv_gate_in_dtl d where a.sys_number=".$txt_pass_id." and a.id=b.mst_id and c.id=d.mst_id group by a.sys_number,c.gate_pass_no";

		$txt_loaded_weight 		= str_replace("'","",$txt_loaded_weight) ;
		$txt_unloaded_weight 	= str_replace("'","",$txt_unloaded_weight) ;
		$txt_net_weight 		= str_replace("'","",$txt_net_weight) ;
		if(str_replace("'","",$update_id)=="")
		{
			$id=return_next_id("id", "inv_gate_in_mst", 1);			
			if($db_type==2)
			{
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GIE', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from inv_gate_in_mst where company_id=$cbo_company_name and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id DESC ", "sys_number_prefix", "sys_number_prefix_num" ));
			} 
			if($db_type==0)
			{	
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GIE', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from inv_gate_in_mst where company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by id DESC ", "sys_number_prefix", "sys_number_prefix_num" ));
			}
			//update_id
			$field_array="id,sys_number_prefix,sys_number_prefix_num,sys_number,company_id,inv_gate_pass_mst_id,gate_pass_no,within_group,party_type,sending_company,
			receive_from,department_id,section,in_date,attention,returnable,est_return_date,com_location_id,out_location_id,out_date,challan_no,time_hour,time_minute,carried_by,pi_reference,basis_id,party_challan,inserted_by,insert_date,status_active,is_deleted,vehicle_no,loaded_weight,unloaded_weight,net_weight";
			$data_array="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$gate_pass_mst_id.",".strtoupper($txt_pass_id).",".$cbo_group.",".$cbo_party_type.",".$cbo_out_company.",".$txt_receive_from.",".$cbo_department_name.",".$cbo_section.",".$txt_in_date.",".$txt_attention.",".$cbo_returnable.",".$txt_return_date.",".$cbo_com_location_id.",".$cbo_out_location_id.",".$txt_out_date.",".$txt_challan_no.",".$txt_start_hours.",".$txt_start_minuties.",".$txt_carried_by.",".$txt_reference_id.",".$txt_basis_id.",".$txt_party_challan.",'".$user_id."','".$pc_date_time."',1,0,".$txt_vehicle_no.",'".$txt_loaded_weight."','".$txt_unloaded_weight."','".$txt_net_weight."')";
			 $txt_system_no=$new_sys_number[0];
		 }
		 else
		 {
			$id=str_replace("'",'',$update_id);
			$field_array="department_id*section*in_date*attention*returnable*gate_pass_no*est_return_date*com_location_id*out_location_id*out_date*time_hour*time_minute*basis_id*party_challan*updated_by*update_date*status_active*is_deleted*vehicle_no*loaded_weight*unloaded_weight*net_weight";
			$data_array="".$cbo_department_name."*".$cbo_section."*".$txt_in_date."*".$txt_attention."*".$cbo_returnable."*".strtoupper($txt_pass_id)."*".$txt_return_date."*".$cbo_com_location_id."*".$cbo_out_location_id."*".$txt_out_date."*".$txt_start_hours."*".$txt_start_minuties."*".$txt_basis_id."*".$txt_party_challan."*'".$user_id."'*'".$pc_date_time."'*1*0*".$txt_vehicle_no."*'".$txt_loaded_weight."'*'".$txt_unloaded_weight."'*'".$txt_net_weight."'";
			$txt_system_no=$txt_system_id;
	 		//$field_array1="quantity*remarks*updated_by*update_date*status_active*is_deleted";
	 		//$data_array1= "".$txtquantity_1."*".$txtremarks_1."*'".$user_id."'*'".$pc_date_time."'*1*0";
			//$rID=sql_update("inv_gate_in_mst",$field_array,$data_array,"id",$update_id,1);
			//print_r($data_array);	
		}
		//if($id == "" ){ echo "15"; exit(); }
		$dtlsid=return_next_id("id", "inv_gate_in_dtl", 1);		
  		$field_array1="id,mst_id,sample_id,item_category_id,buyer_order,item_description,chalan_qty,quantity,reject_qty,uom,rate,amount,remarks,get_pass_dtlsid,fabric_color_id,inserted_by,
		insert_date,status_active,is_deleted";
	   	$add_comma=0;
		// $req_txt_chalan_qty=0;
		// $present_txt_chalan_qty=0;
		for($i=1; $i<=$row_num; $i++)
		{
			$item_category_id="cboitemcategory_".$i;
			$txt_sample="cbosample_".$i;
			$txt_descrption="txtitemdescription_".$i;
			$txt_qty="txtquantity_".$i;
			$txt_reject_qty="txtRejQuantity_".$i;
			$txt_chalan_qty="txtcalanquantity_".$i;
			$cbo_uom="cbouom_".$i;
			$txtuomqty="txtuomqty_".$i;
			$txt_rate="txtrate_".$i;
			$txt_amount="txtamount_".$i;
			$txt_order="txtorder_".$i;
			$txt_ramarks="txtremarks_".$i;
			$update_details_id="updatedtlsid_".$i;
			$getpassdtlsid="getpassdtlsid_".$i;
			$fabriccolorid="fabriccolorid_".$i;
			// $req_txt_chalan_qty+=str_replace("'","",$$txt_chalan_qty);
			// $present_txt_chalan_qty+=str_replace("'","",$$txt_qty);
			if(str_replace("'","",$$txt_qty)!='' || str_replace("'","",$$txt_qty)!=0 || str_replace("'","",$$txt_reject_qty)!='' || str_replace("'","",$$txt_reject_qty)!=0 )
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1.="(".$dtlsid.",".$id.",".$$txt_sample.",".$$item_category_id.",".$$txt_order.",".$$txt_descrption.",".$$txt_chalan_qty.",".$$txt_qty.",".$$txt_reject_qty.",".$$cbo_uom.",".$$txt_rate.",".$$txt_amount.",".$$txt_ramarks.",".$$getpassdtlsid.",".$$fabriccolorid.",'".$user_id."','".$pc_date_time."',1,0)"; 
				$dtlsid=$dtlsid+1;
				$add_comma++;
			}
		}
		//echo "10**".$data_array1; die;
	//	echo "10**insert into inv_gate_in_mst (".$field_array.") values ".$data_array;die;
		if(str_replace("'","",$update_id)=="")
		{
		  $rID=sql_insert("inv_gate_in_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_gate_in_mst",$field_array,$data_array,"id",$update_id,1);	
		}
	 	 //echo "10**insert into inv_gate_in_dtl (".$field_array1.") values ".$data_array1;die;
	
		$dtlsrID=sql_insert("inv_gate_in_dtl",$field_array1,$data_array1,1);
		//echo "10**".$rID.'=='.$dtlsrID.'==';die;
		//print($data_array1);die;
		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "0**".$txt_system_no."**".str_replace("'","",$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2)
		{		
			if($rID && $dtlsrID)
			{
			oci_commit($con);
			echo "0**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$id);
			}
		
			else
			{	oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$id)."**$rID";
			}
		}	
		disconnect($con);
		die;		
	}	
	else if ($operation==1) // Update Here----------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$cbo_group=str_replace("'","",$cbo_group);	
		$validate_gate_pass=sql_select("select a.sys_number as pass_sys_number,sum(b.quantity) as pass_qty from  inv_gate_pass_mst a,inv_gate_pass_dtls b
		where a.sys_number=".$txt_pass_id." and a.id=b.mst_id  group by a.sys_number");
		$validate_gate_in=sql_select("select c.gate_pass_no as gate_in_sys_number,sum(d.quantity) as gate_in_qty from  inv_gate_in_mst c,inv_gate_in_dtl d 
		where c.gate_pass_no=".$txt_pass_id." and c.id=d.mst_id group by c.gate_pass_no");
		$gate_pass_qty=0;
		$gate_in_qty=0;
		
		foreach($validate_gate_pass as $row)
		{
			$gate_pass_qty+=$row[csf("pass_qty")];
		}

		$total_quantity=0;
		for($i=1; $i<=$row_num; $i++)
		{
			$txtquantity="txtquantity_".$i;
			$txtquantity=str_replace("'","",$$txtquantity);
			$total_quantity+=$txtquantity;
		}
		//echo "10**".$txtquantity; die;
		$gate_in_qty_total=$total_quantity;
		 
		$txt_pass=str_replace("'","",$txt_pass_id);
		if($txt_pass)
		{
			if(($cbo_group==1 || $cbo_group==2) && count($validate_gate_pass) > 0 )
			{
				if($gate_pass_qty<$gate_in_qty_total)
				{
					echo "30** Gate in Quantity is more than pass Quantity";disconnect($con);
					exit();
				}
			}
		}
		else
		{
			if($cbo_group==1 )
			{
				if($gate_pass_qty<$gate_in_qty_total)
				{
					echo "30** Gate in Quantity is more than pass Quantity";disconnect($con);
					exit();
				}
			}
		}
		$txt_loaded_weight 		= str_replace("'","",$txt_loaded_weight) ;
		$txt_unloaded_weight 	= str_replace("'","",$txt_unloaded_weight) ;
		$txt_net_weight 		= str_replace("'","",$txt_net_weight) ;
		
	    $field_array="department_id*section*in_date*attention*returnable*gate_pass_no*est_return_date*out_date*time_hour*time_minute*carried_by*pi_reference*challan_no*receive_from*party_challan*updated_by*update_date*status_active*is_deleted*vehicle_no*loaded_weight*unloaded_weight*net_weight";

		$data_array="".$cbo_department_name."*".$cbo_section."*".$txt_in_date."*".$txt_attention."*".$cbo_returnable."*".strtoupper($txt_pass_id)."*".$txt_return_date."*".$txt_out_date."*".$txt_start_hours."*".$txt_start_minuties."*".$txt_carried_by."*".$txt_reference_id."*".$txt_challan_no."*".$txt_receive_from."*".$txt_party_challan."*'".$user_id."'*'".$pc_date_time."'*1*0*".$txt_vehicle_no."*'".$txt_loaded_weight."'*'".$txt_unloaded_weight."'*'".$txt_net_weight."'";
 		$field_array1="item_category_id*sample_id*item_description*chalan_qty*uom*rate*amount*quantity*reject_qty*buyer_order*remarks*updated_by*update_date*status_active*is_deleted"; 
 		$data_array1= "".$cboitemcategory_1."*".$cbosample_1."*".$txtitemdescription_1."*".$txtcalanquantity_1."*".$cbouom_1."*".$txtrate_1."*".$txtamount_1."*".$txtquantity_1."*".$txtRejQuantity_1."*".$txtorder_1."*".$txtremarks_1."*'".$user_id."'*'".$pc_date_time."'*1*0";
		//print($data_array);die;
		$rID=sql_update("inv_gate_in_mst",$field_array,$data_array,"id",$update_id,1);	
 		$dtlsrID=sql_update("inv_gate_in_dtl",$field_array1,$data_array1,"id",str_replace("'","",$updatedtlsid_1),1); 
		//echo $dtlsrID."_".$rID;die;
		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if( $rID && $dtlsrID  )
			{
				oci_commit($con);
			    echo "1**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
		    	echo "10**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$update_id)."**".$rID;
			}
		}
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = return_field_value("id","inv_gate_in_mst","sys_number=$txt_system_id");	
		if($mst_id=="" || $mst_id==0){ echo "15**0"; disconnect($con);die;}
		//$rID=1;
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";
		//$rID=sql_update("inv_gate_in_mst",$field_array,$data_array,"id",$mst_id,1); //old
		//$dtlsrID=sql_update("inv_gate_in_dtl",$field_array,$data_array,"mst_id",$mst_id,1); //old	
	
		$checkDtlsData=sql_select("select count(mst_id) as total_mst_id from  inv_gate_in_dtl where mst_id=$mst_id and status_active=1 and is_deleted=0");
		$total_mst_id=$checkDtlsData[0][csf('total_mst_id')];
		//echo "10**".$total_mst_id;
		//die;
		if($total_mst_id==1) {
			$rID=sql_update("inv_gate_in_mst",$field_array,$data_array,"id",$mst_id,1); //old 
		}
		
 		$dtlsrID=sql_update("inv_gate_in_dtl",$field_array,$data_array,"id",$updatedtlsid_1,1); //new	
		if($total_mst_id==1) $dlt_mst_dtls="$rID && $dtlsrID"; else $dlt_mst_dtls="$dtlsrID";
	
		if($db_type==0)
		{	
			 if($dlt_mst_dtls) //if($rID && $dtlsrID) old
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{	if($dlt_mst_dtls) //if($rID && $dtlsrID) old
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$update_id);
			}
		}
		else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id);
			}
		disconnect($con);
		die;
	}		
}

if($action=="sys_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>     
	<script>
		function js_set_value(sys_number)
		{
			$("#hidden_sys_number").val(sys_number); // mrr number
			parent.emailwindow.hide();
		}

		function display_show()
		{
			$("#search_tbl").show();
		}
	</script>

	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="980" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>                	 
						<th>Company</th>
						<th>System ID</th>
						<th>Challan No</th>
						<th>Gate Pass No</th>
						<input type="hidden" id="within_group" name="within_group" value="<? echo $cbo_group;?>" />
						<th>Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>          
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							echo create_drop_down( "cbo_company_id", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1 );
							?>
						</td>						
						<td width="" align="center" >				
							<input type="text" style="width:140px" class="text_boxes"  name="txt_gate_pass_id" id="txt_gate_pass_id" />	
						</td>  
						<td width="" align="center" >				
							<input type="text" style="width:100px" class="text_boxes"  name="txt_challan_no" id="txt_challan_no" />	
						</td>  
						<td width="" align="center" >				
							<input type="text" style="width:100px" class="text_boxes"  name="txt_gate_in_id" id="txt_gate_in_id" />	
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_gate_pass_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('within_group').value+'_'+document.getElementById('txt_challan_no').value+'_'+document.getElementById('txt_gate_in_id').value+'_'+document.getElementById('cbo_year_selection').value, 'create_sys_search_list_view', 'search_div', 'get_in_entry_controller', 'setFilterGrid(\'list_view\',-1)');display_show();" style="width:100px;" />				
						</td>
					</tr>
					<tr>                  
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
							<input type="hidden" id="hidden_update_id" value="hidden_update_id" />
							<!---END-->
						</td>
					</tr>    
				</tbody>
			</table> 
			<br>   
			<table style="display:none;" id="search_tbl" width="910" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th colspan="8" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
					</tr>
				</thead>
			</table>
			<div align="center" valign="top" id="search_div"> </div> 
		</form>
    </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_sys_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$gate_pass_id = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$within_group = $ex_data[4];
	$challan_no = $ex_data[5];
	$gate_pass_no = $ex_data[6];
 	//echo $fromDate;die;
 	$sql_cond="";
 
	if($db_type==2) 
	{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and in_date  between '".change_date_format($fromDate,'mm-dd-yyyy','-',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','-',1)."'";
	}
	else if($db_type==0) 
	{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and in_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}

	if(str_replace("'","",$company)!=0) $sql_cond .= " and company_id=".str_replace("'","",$company)." ";	
	if(str_replace("'","",$gate_pass_id)!="") $get_cond .= "and sys_number_prefix_num  like '%".str_replace("'","",$gate_pass_id)."%'  "; else  $get_cond=""; 
	if(str_replace("'","",$challan_no)!="") $challan_cond .= "and challan_no  like '%".str_replace("'","",$challan_no)."%'  "; else  $challan_cond="";
	if(str_replace("'","",$gate_pass_no)!="") $gate_pass_no_cond .= "and gate_pass_no  like '%".str_replace("'","",$gate_pass_no)."%'  "; else  $gate_pass_no_cond=""; 
	
	if(str_replace("'","",$within_group)!=0) $within_group_cond .= "and within_group in($within_group)  "; else  $within_group_cond=""; 

	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(insert_date, '-', 1)=$ex_data[7] ";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(insert_date,'YYYY')=$ex_data[7] ";
	}
	$sql = "select id, sys_number_prefix_num, sys_number, within_group, gate_pass_no, department_id, challan_no, in_date, pi_reference  
			from inv_gate_in_mst where status_active=1 and is_deleted=0 $sql_cond $get_cond $within_group_cond $challan_cond $gate_pass_no_cond $year_cond order by id asc";
	// echo $sql;
	$department_arr = return_library_array( "select id, department_name from  lib_department",'id','department_name');
	$arr=array(1=>$department_arr,6=>$yes_no);

	echo create_list_view("list_view", "System No, Department,Gate Pass NO, Challan No,WO/PI/REQ, IN Date,Within Group","120,150,120,120,120,100","900","260",0, $sql , "js_set_value", "id,sys_number", "", 1, "0,department_id,0,0,0,0,within_group", $arr, "sys_number,department_id,gate_pass_no,challan_no,pi_reference,in_date,within_group", "",'','0,0,0,0,0,3,0') ;	
	exit();
}

if($action=="refpopup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>   
	<script>
		function js_set_value(sys_number)
		{
			//alert(sys_number);
			$("#hidden_sys_number").val(sys_number); // mrr number
			//return;
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>                	 
						<th class="must_entry_caption">Company</th>
						<th >Item Category</th>
						<th >PI/WO/REQ</th>
						<th id="search_by_td_up" >Please Enter PI</th>
						<th >Year</th>
						<th>
						<input type="reset" name="re_button" id="re_button" value="Reset" style="width:90px" class="formbutton"  />
						<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
						</th>           
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							echo create_drop_down( "cbo_company_id", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1 );
							?>
						</td>						
						<td align="center">
							<? 
								echo create_drop_down( "cbo_itemcategory", 120,$item_category,"",1, "-- Select --",$row[csf('item_category_id')] , "" ); 
							?>
						</td>
						<td align="center">	
							<?
								$search_by_arr=array(1=>"PI",2=>"WO",3=>"REQ",4=>"Trims WO");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:120px" class="text_boxes"  name="txt_reference_id" id="txt_reference_id" />
						</td>
						<td align="center">
							<?
								$selected_year=date("Y"); 
								echo create_drop_down( "cbo_job_year_id", 120, $year,"", 1, "--Year--", $selected_year, "",0,"","");
							?>
						</td>	
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_itemcategory').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_reference_id').value+'_'+document.getElementById('cbo_job_year_id').value+'_'+'<? echo $cbo_party_type; ?>'+'_'+'<? echo $cbo_out_company; ?>', 'create_ref_search_list_view', 'search_div', 'get_in_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:90px;" />				
						</td>
					</tr>
					<tr style="display:none">                  
						<td align="center" height="40" valign="middle" colspan="5">
							<? //echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							
							<!---END-->
						</td>
					</tr>    
				</tbody>   
			</table> 
			<br>   
			<div align="center" valign="top" id="search_div"> </div> 
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_ref_search_list_view")
{
	$ex_data = explode("_",$data);
	//print_r($ex_data); die;
	$company 		= $ex_data[0];
	$item_cat_ref 	= $ex_data[1];
	$cbo_search 	= $ex_data[2];
	$txt_refe 		= $ex_data[3];
	$cbo_year 		= $ex_data[4];
	$cbo_party_type	= $ex_data[5];
	$cbo_out_company	= $ex_data[6];	
	//echo $cbo_search.'system'.$item_cat_ref.'system'.$cbo_party_type.'system'.$cbo_out_company .'system'.$cbo_year;die;

	if($cbo_year!=0)
	{		
		if ($db_type == 0)
		{
			$str_yy_pi=" and a.pi_date like '".$cbo_year."-%'";
			$str_yy_rq=" and a.requisition_date like '".$cbo_year."-%'";
			if($item_cat_ref == 2 || $item_cat_ref == 3)
			{
				$str_yy_wo=" and a.booking_date like '".$cbo_year."-%'";
			}elseif($item_cat_ref == 4){
				$str_yy_wo=" and a.booking_date like '".$cbo_year."-%'";
				$str_yy_wo_order=" and a.wo_date like '".$cbo_year."-%'";
			}else{					
				$str_yy_wo_all=" and wo_date like '".$cbo_year."-%'";
				//echo $str_yy_wo_all;
			}
		}
		else if($db_type==1 || $db_type==2)
		{
			$year_replace = substr($cbo_year, -2);		
			$str_yy_pi=" and a.pi_date like '%-".$year_replace."'";
			$str_yy_rq=" and a.requisition_date like '%-".$year_replace."'";
			if($item_cat_ref == 2 || $item_cat_ref == 3)
			{
				$str_yy_wo=" and a.booking_date like '%-".$year_replace."'";
			}elseif($item_cat_ref == 4){
				$str_yy_wo=" and a.booking_date like '%-".$year_replace."'";
				$str_yy_wo_order=" and a.wo_date like '%-".$year_replace."'";
			}else{					
				$str_yy_wo_all=" and wo_date like '%-".$year_replace."'";
				$str_yy_wo=" and a.booking_date like '%-".$year_replace."'";
			}
		}		
	}	
	
	if($cbo_search == 1)
	{ 
		// PI
		$category_cond 	= ($item_cat_ref != 0)?"a.item_category_id = $item_cat_ref":"a.item_category_id not in (1,2,5,6,7,12,13,14)";
		$txt_refe=trim($txt_refe);
		$reference_cond = ($txt_refe != '')?" and a.pi_number like '%$txt_refe%'":" ";
		//$reference_cond3 = ($txt_refe != '')?"a.wo_number_prefix_num like '%$txt_refe%'":" ";
		//echo $reference_cond3;die;
		if($cbo_party_type==2 && $cbo_out_company > 0) $reference_cond.=" and a.supplier_id=$cbo_out_company";
		if($db_type==0)
		{			
	 		$sql = "select a.id as id,a.pi_number as wopi_number,b.lc_number as lc_number,a.pi_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source ,a.item_category_id as item_category,concat(a.id,'_1_".$item_cat_ref."_',a.pi_number) as id_type_pi
			from com_pi_master_details a left join com_btb_lc_master_details b on FIND_IN_SET(a.id,b.pi_id)
			where $category_cond  $reference_cond and a.status_active=1 and a.is_deleted=0 and a.goods_rcv_status<>1 $str_yy_pi order by a.id desc";
		}
				
		if($db_type==1 || $db_type==2)
		{
			$sql = "select a.id as id, a.pi_number as wopi_number,b.lc_number as lc_number,a.pi_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source,a.item_category_id as item_category,a.id || '_1_".$item_cat_ref."_' || a.pi_number as id_type_pi 
			from com_pi_master_details a left join com_btb_lc_pi c on a.id=c.pi_id left join com_btb_lc_master_details b on c.com_btb_lc_master_details_id=b.id
			where $category_cond  $reference_cond  and a.status_active=1 and a.is_deleted=0 and a.goods_rcv_status<>1 $str_yy_pi order by a.id desc";
		}
		//echo $sql;
		//$result = sql_select($sql);
	 	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$arr=array(2=>$supplier_arr,3=>$currency,4=>$source,5=>$item_category);
		echo create_list_view("list_view", "PI No ,Date, Supplier, Currency, Source,Item Category","120,100,130,100,100,100","900","260",0, $sql , "js_set_value", "id_type_pi", "", 1, "0,0,supplier_id,currency_id,source,item_category", $arr, "wopi_number,wopi_date,supplier_id,currency_id,source,item_category", "",'','0,0,0,0,0,0') ;

		exit();	
	}
	else if($cbo_search == 2)
	{ 
		// WO
		//echo $item_cat_ref.test;die;
		if($cbo_year!=0)
		{
			if ($db_type == 0) $str_yy_wo_all=" and wo_date like '".$cbo_year."-%'";
			else if($db_type==1 || $db_type==2) $str_yy_wo_all=" and a.wo_date like '%-".$year_replace."'";		
		}			
		
		if($item_cat_ref == 2 || $item_cat_ref == 3)
		{
			if($item_cat_ref== 2) {  $category_cond= " and a.item_category=2"; } 
			else {$category_cond= " and a.item_category=3";}
			$txt_refe=trim($txt_refe);
			$reference_cond = ($txt_refe != '')?" and a.booking_no_prefix_num like '%$txt_refe%'":"";

			$str_cond="";
			if($item_cat_ref) $str_cond=" and a.item_category=$item_cat_ref";
			if($txt_refe != '') $str_cond.=" and a.booking_no_prefix_num like '%$txt_refe%'";
			if($cbo_party_type==1 && $cbo_out_company > 0) $str_cond.=" and a.buyer_id=$cbo_out_company";
			else if($cbo_party_type==2 && $cbo_out_company > 0) $str_cond.=" and a.supplier_id=$cbo_out_company";
			if($db_type==0)
			{
				$sql= "select a.* from ( 
				select a.id, a.booking_no_prefix_num, a.booking_no,  a.booking_date, a.supplier_id, a.item_category, a.currency_id, a.source, 'With Order' as serial,concat(a.id,'_2_".$item_cat_ref."_',a.booking_no_prefix_num) as id_booking_no_prefix_num 
				from wo_booking_mst a where a.status_active=1 and a.is_deleted=0 $str_yy_wo $reference_cond $category_cond
				union
				select a.id, a.booking_no_prefix_num, a.booking_no,  a.booking_date, a.supplier_id, a.item_category, a.currency_id, a.source, 'Without Order' as serial,concat(a.id,'_2_".$item_cat_ref."_',a.booking_no_prefix_num) as id_booking_no_prefix_num 
				from wo_non_ord_samp_booking_mst a where a.status_active=1 and a.is_deleted=0 $str_yy_wo $reference_cond $category_cond 
				) a 
				order by a.id desc";
			}
			if($db_type==1 || $db_type==2)
			{
				// $sql= "select a.* from (
				// select a.id, a.booking_no_prefix_num, a.booking_no,  a.booking_date, a.supplier_id, a.item_category, a.currency_id, a.source, 'With Order' as serial,a.id || '_2_".$item_cat_ref."_' || a.booking_no_prefix_num as id_booking_no_prefix_num 
				// from wo_booking_mst a 
				// where a.status_active=1 and a.is_deleted=0 $str_yy_wo $reference_cond $category_cond 
				// union
				// select a.id, a.booking_no_prefix_num, a.booking_no,  a.booking_date, a.supplier_id, a.item_category, a.currency_id, a.source, 'Without Order' as serial,a.id || '_2_".$item_cat_ref."_' || a.booking_no_prefix_num as id_booking_no_prefix_num 
				// from wo_non_ord_samp_booking_mst a 
				// where a.status_active=1 and a.is_deleted=0 $str_yy_wo $reference_cond $category_cond
				// ) a 
				// order by a.id desc";

				$sql = "select a.id as wo_id, a.company_id as company_name, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.supplier_id, a.item_category, a.currency_id, a.source, a.id || '_2_' || a.item_category || '_' || a.booking_no_prefix_num || '_With Order' as id_booking_no_prefix_num, a.item_category as item_cat, 2 as type 
				from wo_booking_mst a 
				where a.status_active=1 and a.is_deleted=0 $str_cond $str_yy_wo
				union all
				select a.id as wo_id, a.company_id as company_name, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.supplier_id, a.item_category, a.currency_id, a.source, a.id || '_2_' || a.item_category || '_' || a.booking_no_prefix_num || '_With Order' as id_booking_no_prefix_num, a.item_category as item_cat, 3 as type
				from wo_non_ord_samp_booking_mst a 
				where a.status_active=1 and a.is_deleted=0 $str_cond $str_yy_wo
				order by type, wo_id desc";
			}
			
			//echo $sql;die;
			// $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
			// $arr=array(2=>$supplier_arr,3=>$currency,4=>$source,5=>$item_category);
			// echo  create_list_view("list_view", "WO No, Date, Supplier, Currency, Source,Item Category,Order","100,120,140,120,120,120","900","260",0, $sql , "js_set_value", "id_booking_no_prefix_num", "", 1, "0,0,supplier_id,currency_id,source,item_category", $arr, "booking_no_prefix_num,booking_date,supplier_id,currency_id,source,item_category,serial", "",'','0,3,0,0,0,0,0') ;
			// exit();

			$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');	
			$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');			

			$result_cat = sql_select($sql);
			$itemCatIdArr=array();
			foreach ($result_cat as  $value) 
			{
				$itemCatIdArr = array_unique(array_filter(explode(",",$value[csf("item_cat")])));
				foreach ($itemCatIdArr as  $itemCat) 
				{
					$itemCatString = $item_category[$itemCat].",";
					$catFromWoNo[$value[csf("wo_id")]] = chop($itemCatString,",");
				}
			}
			//echo $sql;//die;
			$arr=array(0=>$company_arr,3=>$supplier_arr,4=>$currency,5=>$source,6=>$catFromWoNo);
			echo  create_list_view("list_view", "Company, WO No, Date, Supplier, Currency, Source, Item Category","180,70,70,220,70,80","900","260",0, $sql , "js_set_value", "id_booking_no_prefix_num,0,type", "", 1, "company_name,0,0,supplier_id,currency_id,source,wo_id", $arr, "company_name,wo_number_prefix_num,wo_date,supplier_id,currency_id,source,wo_id", "",'','0,0,3,0,0,0,0,0') ;
			exit();	

		}
		else if($item_cat_ref == 4)
		{
			$txt_refe=trim($txt_refe);
			$reference_cond = ($txt_refe != '')?" and a.booking_no_prefix_num like '%$txt_refe%'":"";
			$reference_cond2 = ($txt_refe != '')?" and a.wo_number_prefix_num like '%$txt_refe%'":"";
			
			if($cbo_party_type==1 && $cbo_out_company > 0) $reference_cond.=" and a.buyer_id=$cbo_out_company";
			else if($cbo_party_type==2 && $cbo_out_company > 0) $reference_cond.=" and a.supplier_id=$cbo_out_company";
			
			if($db_type==0)
			{
				$sql="select a.* from (select a.id, a.booking_no_prefix_num as wo_number,  a.booking_date, a.currency_id, a.source, 'With Order' as serial,concat(a.id,'_2_".$item_cat_ref."_',a.booking_no_prefix_num) as id_wo_number, a.item_category  
				from wo_booking_mst a where a.item_category = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond $str_yy_wo and a.company_id=$company
				union
				select a.id, a.wo_number_prefix_num as wo_number, a.wo_date, a.currency_id, a.source, 'Without Order' as serial,concat(a.id,'_2_".$item_cat_ref."_',a.wo_number_prefix_num) as id_wo_number , group_concat(b.item_category_id) as item_category 
				from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id = b.mst_id and b.item_category_id = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond2 $str_yy_wo_order and a.company_name=$company
					) a order by serial desc";
			}
			if($db_type==1 || $db_type==2)
			{
				/*$sql = "select a.* from (select a.id, a.booking_no_prefix_num as wo_number, a.booking_date, a.currency_id, a.source, 'With Order' as serial,a.id || '_2_".$item_cat_ref."_' || a.booking_no_prefix_num as id_wo_number,  cast(a.item_category as  varchar2(4000)) as item_category
				 from wo_booking_mst a 
				 where a.item_category = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond $str_yy_wo and a.company_id=$company
				 union 
				 select a.id, a.wo_number_prefix_num as wo_number, a.wo_date, a.currency_id, a.source, 'Without Order' as serial,a.id || '_2_".$item_cat_ref."_' || a.wo_number_prefix_num as id_wo_number, listagg(b.item_category_id,',') within group (order by b.item_category_id) item_category
				 from wo_non_order_info_mst a , wo_non_order_info_dtls b
				 where a.id =b.mst_id and b.item_category_id = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond2 $str_yy_wo_order and a.company_name=$company
				 group by a.id, a.wo_number_prefix_num , a.wo_date, a.currency_id, a.source 
				 ) a order by serial desc";*/
				 
				 $sql = "SELECT a.id,a.booking_no_prefix_num as wo_number, a.booking_date as wo_date, a.currency_id, a.source, 'With Order' as serial, a.id || '_2_4_' || a.booking_no_prefix_num || '_With Order__2' as id_wo_number, listagg(a.item_category,',') within group (order by a.item_category) item_category
				 from wo_booking_mst a 
				 where a.status_active=1 and a.is_deleted=0 and a.item_category=4 and a.company_id=$company $reference_cond $str_yy_wo
				 group by a.id,a.booking_no_prefix_num, a.booking_date, a.currency_id, a.source
				 union all
				 SELECT a.id, a.wo_number_prefix_num as wo_number, a.wo_date, a.currency_id, a.source, 'Without Order' as serial, a.id || '_2_4_' || a.wo_number_prefix_num || '_Without Order__3' as id_wo_number, listagg(b.item_category_id,',') within group (order by b.item_category_id) item_category
				 from wo_non_order_info_mst a, wo_non_order_info_dtls b
				 where a.id =b.mst_id and b.item_category_id = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond2 $str_yy_wo_order and a.company_name=$company
				 group by a.id, a.wo_number_prefix_num , a.wo_date, a.currency_id, a.source ";

			}
	        $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
			//$arr=array(2=>$currency,3=>$source,4=>$item_category);
			//echo "**".$sql;
			$result = sql_select($sql);
			?>
			<div style="margin-top:5px">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="100">WO No</th>
						<th width="120">Date</th>
						<th width="140">Currency</th>
						<th width="120">Source</th>
						<th width="120">Item Category</th>
						<th width="120">Order</th>
					</thead>
				</table>
				<div style="width:900px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="list_view">
						<?
						$i = 1;
						foreach ($result as $row) 
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
								onClick="js_set_value('<? echo $row[csf('id_wo_number')]; ?>');">
								<td width="40"><? echo $i; ?></td>
								<td width="100"><p>&nbsp;<? echo $row[csf('wo_number')]; ?></p></td>
								<td width="120" align="center"><p><? echo change_date_format($row[csf('wo_date')]); ?></p></td>
								<td width="140" align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
								<td width="120" align="center"><p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p></td>
								<? 
								if($row[csf("serial")] == 'With Order')
								{
									?>
									<td width="120" align="center"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
									<?
								}
								else
								{
									$item_cat = implode(",",array_filter(array_unique(explode(",", $row[csf('item_category')]))));
									?>
									<td width="120" align="center"><p><? echo $item_category[$item_cat]; ?></p></td>
									<?
								} ?>
								
								<td width="120" align="center"><? echo $row[csf('serial')]; ?></td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
            </div>
            <?
			//echo  create_list_view("list_view", "WO No, Date, Currency, Source,Item Category,Order","100,120,140,120,120,120","900","260",0, $sql , "js_set_value", "id_wo_number", "", 1, "0,0,currency_id,source,item_category", $arr, "wo_number,booking_date,currency_id,source,item_category,serial", "",'','0,0,0,0,0,0,0') ;
			exit();
		}
		else
		{
			$txt_refe=trim($txt_refe);
			$category_cond 	= ($item_cat_ref != 0)?"and b.item_category_id = $item_cat_ref":"";
			if($cbo_party_type==1 && $cbo_out_company > 0) $reference_cond.=" and a.buyer_name=$cbo_out_company";
			else if($cbo_party_type==2 && $cbo_out_company > 0) $reference_cond.=" and a.supplier_id=$cbo_out_company";
			$str_cond="";
			if($item_cat_ref) $str_cond=" and a.item_category=$item_cat_ref";
			if($txt_refe != '') $str_cond.=" and a.booking_no_prefix_num like '%$txt_refe%'";
			if($txt_refe != '') $str_cond1=" and a.wo_number_prefix_num like '%$txt_refe%'";
			if($cbo_party_type==1 && $cbo_out_company > 0) $str_cond.=" and a.buyer_id=$cbo_out_company";
			else if($cbo_party_type==2 && $cbo_out_company > 0) $str_cond.=" and a.supplier_id=$cbo_out_company";
			
			if($db_type==0)
			{
				$sql = "select a.id, a.company_name, a.wo_number_prefix_num, a.wo_date, a.supplier_id, a.item_category, a.currency_id, a.source,a.id || '_2_0_' || wo_number_prefix_num as id_wo_number_prefix_num, group_concat(b.item_category_id) item_cat
				from wo_non_order_info_mst a, wo_non_order_info_dtls b
				where  a.id = b.mst_id and b.status_active=1 and b.is_deleted=0 and a.company_name=$company $str_yy_wo_all $reference_cond $category_cond 
				group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_date, a.supplier_id, item_category, a.currency_id, a.source,a.id order by a.id desc";
			}
			if($db_type==1 || $db_type==2)
			{
				$sql = "select a.id as wo_id, a.company_name, a.wo_number_prefix_num, a.wo_date, cast(a.supplier_id as number) as supplier_id, a.item_category, a.currency_id, a.source, a.id || '_2_' || b.item_category_id || '_' || wo_number_prefix_num || '_Without Order' as id_wo_number_prefix_num, b.item_category_id as item_cat, 1 as type 
				from wo_non_order_info_mst a, wo_non_order_info_dtls b
				where  a.id = b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.company_name=$company $str_yy_wo_all $reference_cond $category_cond $str_cond1
				group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_date, a.supplier_id, item_category, a.currency_id, a.source, a.id, b.item_category_id
				union all
				select a.id as wo_id, a.company_id as company_name, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.supplier_id, a.item_category, a.currency_id, a.source, a.id || '_2_' || a.item_category || '_' || a.booking_no_prefix_num || '_With Order' as id_booking_no_prefix_num, a.item_category as item_cat, 2 as type 
				from wo_booking_mst a 
				where a.status_active=1 and a.is_deleted=0 and a.company_id=$company $str_cond $str_yy_wo
				union all
				select a.id as wo_id, a.company_id as company_name, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.supplier_id, a.item_category, a.currency_id, a.source, a.id || '_2_' || a.item_category || '_' || a.booking_no_prefix_num || '_With Order' as id_booking_no_prefix_num, a.item_category as item_cat, 3 as type
				from wo_non_ord_samp_booking_mst a 
				where a.status_active=1 and a.is_deleted=0 and a.company_id=$company $str_cond $str_yy_wo
				order by type, wo_id desc";
			}
			//echo $sql;//die;
			$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');	
			$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');			

			$result_cat = sql_select($sql);
			$itemCatIdArr=array();
			foreach ($result_cat as  $value) 
			{
				$itemCatIdArr = array_unique(array_filter(explode(",",$value[csf("item_cat")])));
				foreach ($itemCatIdArr as  $itemCat) 
				{
					$itemCatString = $item_category[$itemCat].",";
					$catFromWoNo[$value[csf("wo_id")]] = chop($itemCatString,",");
				}
			}
			//echo $sql;//die;
			$arr=array(0=>$company_arr,3=>$supplier_arr,4=>$currency,5=>$source,6=>$catFromWoNo);
			echo  create_list_view("list_view", "Company, WO No, Date, Supplier, Currency, Source, Item Category","180,70,70,220,70,80","900","260",0, $sql , "js_set_value", "id_wo_number_prefix_num,0,type", "", 1, "company_name,0,0,supplier_id,currency_id,source,wo_id", $arr, "company_name,wo_number_prefix_num,wo_date,supplier_id,currency_id,source,wo_id", "",'','0,0,3,0,0,0,0,0') ;
			exit();	
		}
	}
	else if($cbo_search == 3)
	{ // req
		$txt_refe=trim($txt_refe);
		$category_cond 	= ($item_cat_ref != 0)?"x.item_category = $item_cat_ref":"x.item_category not in (1,2,5,6,7,12,13,14)";
		$reference_cond = ($txt_refe != '')?" and a.requ_no like '%$txt_refe%'":"";
		
		if($cbo_party_type==2 && $cbo_out_company > 0) $reference_cond.=" and a.supplier_id=$cbo_out_company";
		//echo $reference_cond;die;

		if($db_type==0)
		{		
			$sql = "select a.id, a.requ_no, b.lc_number as lc_number, a.requisition_date, a.supplier_id, x.item_category as item_category_id, a.source, b.currency_id,concat(a.id,'_3_".$item_cat_ref."_',a.requ_no) as id_requ_no 
			from inv_purchase_requisition_dtls x, inv_purchase_requisition_mst a 
			left join com_btb_lc_pi c on a.id=c.pi_id 
			left join com_btb_lc_master_details b on c.com_btb_lc_master_details_id=b.id 
			where a.id=x.mst_id and $category_cond  $reference_cond $str_yy_rq and a.status_active=1 and a.is_deleted=0 order by a.id desc";
		}
				
		if($db_type==1 || $db_type==2)
		{
			$sql = "select a.id, a.requ_no, b.lc_number as lc_number, a.requisition_date, a.supplier_id, x.item_category as item_category_id, a.source, b.currency_id,a.id || '_3_".$item_cat_ref."_' || a.requ_no as id_requ_no 
			from inv_purchase_requisition_dtls x, inv_purchase_requisition_mst a 
			left join com_btb_lc_pi c on a.id=c.pi_id 
			left join com_btb_lc_master_details b on c.com_btb_lc_master_details_id=b.id 
			where a.id=x.mst_id and $category_cond  $reference_cond $str_yy_rq and a.status_active=1 and a.is_deleted=0 order by a.id desc";
		}
		
		$supplier_arr	=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$arr=array(2=>$supplier_arr,3=>$currency,4=>$source,5=>$item_category);
		echo  create_list_view("list_view", "REQ No ,Date, Supplier, Currency, Source,Item Category","120,100,130,100,100,100","900","260",0, $sql , "js_set_value", "id_requ_no", "", 1, "0,0,supplier_id,currency_id,source,item_category_id", $arr, "requ_no,requisition_date,supplier_id,currency_id,source,item_category_id", "",'','0,0,0,0,0,0') ;
		exit();	
	}
	else if($cbo_search == 4)
	{
		 // WO trims
		if($item_cat_ref == 4)
		{
			$txt_refe=trim($txt_refe);
			$reference_cond = ($txt_refe != '')?" and a.booking_no_prefix_num like '%$txt_refe%'":"";
			$reference_cond2 = ($txt_refe != '')?" and a.wo_number_prefix_num like '%$txt_refe%'":"";
			
			if($cbo_party_type==1 && $cbo_out_company > 0) $reference_cond.=" and a.buyer_id=$cbo_out_company";
			else if($cbo_party_type==2 && $cbo_out_company > 0) $reference_cond.=" and a.supplier_id=$cbo_out_company";
			
			if($cbo_party_type==1 && $cbo_out_company > 0) $reference_cond2.=" and a.buyer_name=$cbo_out_company";
			else if($cbo_party_type==2 && $cbo_out_company > 0) $reference_cond2.=" and a.supplier_id=$cbo_out_company";
			
			if($db_type==0)
			{
				$sql="select a.* from (select a.id, a.booking_no_prefix_num as wo_number, a.booking_date, a.item_category, a.currency_id, a.source, 'With Order' as serial, a.booking_type, a.is_short, concat(a.id,'_4_".$item_cat_ref."_',a.booking_no_prefix_num,'_',a.booking_type,'_',a.is_short) as id_wo_number 
				from wo_booking_mst a 
				where a.item_category = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond $str_yy_wo and a.company_id=$company 
				union 
				select a.id, a.booking_no_prefix_num as wo_number, a.booking_date, a.item_category, a.currency_id, a.source, 'Without Order' as serial,a.booking_type,a.is_short,concat(a.id,'_4_".$item_cat_ref."_',a.booking_no_prefix_num,'_',a.booking_type,'_',a.is_short) as id_wo_number 
				from wo_non_ord_samp_booking_mst a 
				where a.item_category = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond $str_yy_wo  and a.company_id=$company 
				union 
				select a.id, a.wo_number_prefix_num as wo_number, a.wo_date as booking_date, a.item_category,a.currency_id, a.source, 'Without Order' as serial,null as booking_type,null as is_short,concat(a.id,'_4_".$item_cat_ref."_',a.wo_number_prefix_num,,'_0_0') as id_wo_number 
				from wo_non_order_info_mst a 
				where a.item_category = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond2 $str_yy_wo_order  and a.company_name=$company) a order by serial desc"; 	
			}
			if($db_type==1 || $db_type==2)
			{				
				// $sql="select a.* from (select a.id, a.booking_no_prefix_num as wo_number, a.booking_date, a.item_category, a.currency_id, a.source, 'With Order' as serial,a.booking_type,a.is_short ,a.id || '_4_".$item_cat_ref."_' || a.booking_no_prefix_num ||'_'||a.booking_type||'_'||a.is_short as id_wo_number 
				// from wo_booking_mst a 
				// where a.item_category = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond $str_yy_wo and a.company_id=$company  
				// union 
				// select a.id, a.booking_no_prefix_num as wo_number, a.booking_date, a.item_category,a.currency_id, a.source, 'Without Order' as serial,a.booking_type,a.is_short,a.id || '_4_".$item_cat_ref."_' || a.booking_no_prefix_num ||'_'||a.booking_type||'_'||a.is_short as id_wo_number  
				// from wo_non_ord_samp_booking_mst a 
				// where a.item_category = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond $str_yy_wo and a.company_id=$company 
				// union 
				// select a.id, a.wo_number_prefix_num as wo_number, a.wo_date as booking_date, a.item_category,a.currency_id, a.source, 'Without Order' as serial,null as booking_type,null as is_short,a.id || '_4_".$item_cat_ref."_' || a.wo_number_prefix_num ||'_0_0' as id_wo_number
				// from wo_non_order_info_mst a 
				// where a.item_category = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond2 $str_yy_wo_order and a.company_name=$company ) a order by serial desc"; 	
				
				$sql="select a.id, a.company_id, a.booking_no_prefix_num as wo_number, a.booking_date, a.item_category, a.currency_id, a.source, 'With Order' as serial, a.booking_type, a.supplier_id, a.is_short ,a.id || '_4_".$item_cat_ref."_' || a.booking_no_prefix_num ||'_'||a.booking_type||'_'||a.is_short as id_wo_number 
				from wo_booking_mst a 
				where a.item_category = 4 and a.status_active=1 and a.is_deleted=0 $reference_cond $str_yy_wo and a.company_id=$company";
				//echo $sql;
		
			}
			
			$suppler_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
			$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
			$result = sql_select($sql);
			?>
			<div style="margin-top:5px">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="160">Company</th>
						<th width="50">WO No</th>
						<th width="150">Supplier</th>
						<th width="80">Date</th>
						<th width="80">Currency</th>
						<th width="80">Source</th>
						<th width="100">Item Category</th>
						<th width="80">Order</th>
						<th>Booking Type</th>
					</thead>
				</table>
				<div style="width:920px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table"
						id="list_view">
						<?
						$i = 1;
						foreach ($result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id_wo_number')]; ?>');">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="160" title="<? echo $row[csf('company_id')];?>" style="word-break:break-all"><p>&nbsp;<? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
								<td width="50" align="center" style="word-break:break-all"><p>&nbsp;<? echo $row[csf('wo_number')]; ?></p></td>
								<td width="150" title="<? echo $row[csf('supplier_id')];?>" style="word-break:break-all"><p>&nbsp;<? echo $suppler_arr[$row[csf('supplier_id')]]; ?></p></td>
								<td width="80" align="center"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
								<td width="80" align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
								<td width="80" align="center"><? echo $row[csf('serial')]; ?></td>
								<td align="center"><p><? if($row[csf('booking_type')]==2 && $row[csf('is_short')]==1){echo 'Short Booking';}else if($row[csf('booking_type')]=="" && $row[csf('is_short')]==""){echo 'General Accessories';}else{ echo $booking_type[$row[csf('booking_type')]];} ?></p></td>
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
		}else{}
	}
}

if($action=="populate_master_from_data")
{
	$sql="select company_id, gate_pass_no, carried_by, within_group, party_type, pi_reference, sending_company, attention,returnable, est_return_date, com_location_id, out_location_id, out_date, receive_from, department_id, section,in_date, challan_no, time_hour, time_minute, basis_id, party_challan,loaded_weight,unloaded_weight,net_weight,vehicle_no from inv_gate_in_mst where id=$data ";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{	
        $sql_is = "select id, basis, company_id, sent_to from inv_gate_pass_mst where sys_number='".$row[csf("gate_pass_no")]."'";
		$result = sql_select($sql_is);
		foreach($result as $val)
		{	
			$basis=$val[csf('basis')];
			$company_id=$val[csf('company_id')];
			$gate_pass_id=$val[csf('id')];
			$sent_to=$val[csf('sent_to')];
		}
		$chalan_no=$row[csf("challan_no")];
		$party_type=$row[csf("party_type")];
		$send_com=$row[csf("sending_company")];
		$companyID=$row[csf("company_id")];
 		echo "$('#cbo_company_name').val(".$row[csf("company_id")].");\n";
		echo "$('#txt_pass_id').val('".strtoupper($row[csf("gate_pass_no")])."');\n";
		echo "$('#txt_vehicle_no').val('".strtoupper($row[csf("vehicle_no")])."');\n";
		echo "$('#txt_receive_from').val('".$row[csf("receive_from")]."');\n";
		echo "$('#cbo_department_name').val('".$row[csf("department_id")]."');\n";
		echo "$('#cbo_section').val(".$row[csf("section")].");\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_in_date').val('".change_date_format($row[csf("in_date")])."');\n";	
		echo "$('#txt_start_hours').val(".$row[csf("time_hour")].");\n";
		echo "$('#txt_carried_by').val('".$row[csf("carried_by")]."');\n";
		echo "$('#txt_basis_id').val('".$row[csf("basis_id")]."');\n";
		echo "$('#txt_party_challan').val('".$row[csf("party_challan")]."');\n";
		
		echo "$('#txt_reference_id').val('".$row[csf("pi_reference")]."');\n";
		echo "$('#txt_start_minuties').val(".$row[csf("time_minute")].");\n";
		echo "$('#cbo_group').val(".$row[csf("within_group")].");\n";
		//cbo_com_location_id*cbo_out_location_id*txt_out_date*cbo_returnable*txt_return_date*txt_attention*
		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		echo "$('#cbo_returnable').val(".$row[csf("returnable")].");\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("est_return_date")])."');\n";
		echo "$('#txt_out_date').val('".change_date_format($row[csf("out_date")])."');\n";
		
		//echo "load_drop_down( 'requires/get_in_entry_controller','".$row[csf("company_id")]."', 'load_drop_down_com_location', 'com_location_td' );";
		echo "load_drop_down( 'requires/get_in_entry_controller','".$companyID."', 'load_drop_down_com_location', 'com_location_td' );";
		echo "$('#cbo_com_location_id').val('".$row[csf("com_location_id")]."');\n";

		echo "$('#txt_loaded_weight').val('".$row[csf("loaded_weight")]."');\n";
		echo "$('#txt_unloaded_weight').val('".$row[csf("unloaded_weight")]."');\n";
		echo "$('#txt_net_weight').val('".$row[csf("net_weight")]."');\n";
		
		if($row[csf("within_group")]==1)
		{
			echo "load_drop_down( 'requires/get_in_entry_controller','".$row[csf("sending_company")]."', 'load_drop_down_out_location', 'out_location_td' );";
			echo "$('#cbo_out_location_id').val('".$row[csf("out_location_id")]."');\n";
		
			echo "load_drop_down( 'requires/get_in_entry_controller','$send_com', 'load_drop_down_out_company', 'sent_td');\n";
			echo "$('#cbo_out_company').val('".$row[csf("sending_company")]."');\n";
			echo "$('#cbo_party_type').attr('disabled',true);\n";
		}
		if($row[csf("within_group")]==2 && $row[csf("returnable")]==1)
		{
			echo "$('#cbo_party_type').val(2);\n";
			echo "load_drop_down( 'requires/get_in_entry_controller','$gate_pass_id'+'_'+$basis+'_'+'$sent_to', 'load_drop_down_out_supplier', 'sent_td');\n";
			echo "$('#cbo_party_type').attr('disabled',true);\n";
			echo "$('#cbo_out_company').attr('disabled',true);\n";
			//echo "load_drop_down( 'requires/get_in_entry_controller','$chalan_no'+'_'+$basis+'_'+$company_id, 'load_drop_down_dying_source', 'sent_td');\n";
		}
		if($row[csf("within_group")]==2 && $row[csf("returnable")]==2)
		{
			echo "$('#cbo_party_type').val('".$row[csf("party_type")]."');\n";
			echo  "load_drop_down( 'requires/get_in_entry_controller', $party_type, 'load_drop_down_sent', 'sent_td');\n";
			echo "$('#cbo_out_company').val('".$row[csf("sending_company")]."');\n";
			echo "$('#cbo_party_type').attr('disabled',true);\n";
			echo "$('#cbo_out_company').attr('disabled',true);\n";
		}

		$returnable=$row[csf('returnable')];
		if ($returnable==1) 
		{
			echo "$('#returnable_item_dtls').removeClass('formbutton_disabled');\n";
			echo "$('#returnable_item_dtls').addClass('formbutton');\n";
		}
  	}	
	exit();	
}

if($action=="show_dtls_list_view")
{
	extract($data);
	$sample_library=return_library_array( "select id, sample_name from  lib_sample", "id", "sample_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
 	
	$sql = "SELECT ID, SAMPLE_ID, ITEM_CATEGORY_ID, BUYER_ORDER, ITEM_DESCRIPTION, CHALAN_QTY, QUANTITY, UOM, UOM_QTY, RATE, AMOUNT, BUYER_ORDER, REMARKS, REJECT_QTY,GET_PASS_DTLSID 
	FROM INV_GATE_IN_DTL WHERE MST_ID=$data and status_active=1 and is_deleted=0 order by id asc"; 

	//echo $sql;
  	$result=sql_select($sql);
	foreach($result as $row)
	{
		$get_pass_dtls_id .= $row['GET_PASS_DTLSID'].",";
	}
    $all_get_pass_dtls_id = ltrim(implode(",", array_unique(explode(",", chop($get_pass_dtls_id, ",")))), ',');

	$style_sql = "SELECT A.BUYER_NAME,A.STYLE_REF_NO,C.ID 
	FROM WO_PO_DETAILS_MASTER A, WO_PO_BREAK_DOWN B, inv_gate_pass_dtls c
	WHERE A.JOB_NO = B.JOB_NO_MST  and B.ID = C.BUYER_ORDER_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.ID in ($all_get_pass_dtls_id)";
	//echo $style_sql;
	$style_result=sql_select($style_sql);
	foreach($style_result as $row)
	{
		$buyerArr[$row['ID']]['BUYER_NAME'] = $row['BUYER_NAME'];
		$buyerArr[$row['ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
	}

	
	//$arr=array(0=>$item_category,1=>$sample_library,6=>$unit_of_measurement,11=>$buyer_arr);
	
 	//  echo create_list_view("list_view", "Item Category,Sample,Item Description,Challan Qty,Quantity,Reject Qty,UOM,UOM Qty,Rate,Amount,Buyer Order,Remarks","120,100,150,80,80,80,80,80,80,150,100","1290","260",0, $sql, "get_php_form_data", "id", "'child_form_input_data','requires/get_in_entry_controller'", 1, "item_category_id,sample_id,0,0,0,0,uom,0,0,0,0,0", $arr, "item_category_id,sample_id,item_description,chalan_qty,quantity,reject_qty,uom,uom_qty,rate,amount,buyer_order,remarks", "","",'0,0,0,0,0,0,0,0',"4,chalan_qty,quantity,reject_qty,'',uom_qty,'',amount,2,''");	
	// exit();		
?>
<div style="width:1440px;">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1440" class="rpt_table" style="margin-right: 17px;">
		<thead>
			<th width="30">SL</th>
			<th width="120">Item Category</th>
			<th width="100" align="center">Sample</th>
			<th width="150" align="center">Item Description</th>
			<th width="80" align="center">Challan Qty</th>
			<th width="80" align="center">Quantity </th>
			<th width="80" align="center">Reject Qty </th>
			<th width="80" align="center">UOM </th>
			<th width="80" align="center">UOM Qty </th>
			<th width="80" align="center">Rate </th>
			<th width="80" align="center">Amount </th>
			<th width="120" align="center">Buyer Order </th>
			<th width="80" align="center">Buyer</th>
			<th width="100" align="center">Style</th>
			<th width="80" align="center">Remarks </th>     
		</thead> 
	</table>
</div>
<div style="width:1440px ;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1440px" class="rpt_table" id="list_view">
        	<?
        	$i=1;
			foreach($result as $row)
			{				
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$basis=$row['BASIS'];
	
				?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="set_child_id('<? echo $row["ID"]; ?>')" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="120" style="word-break:break-all"><?php echo $item_category[$row['ITEM_CATEGORY_ID']];  ?></td>
                    <td width="100" style="word-break:break-all" align="center"><? echo $sample_library[$row['SAMPLE_ID']]; ?></td>
                    <td width="150" style="word-break:break-all" align="center"><?php echo $row['ITEM_DESCRIPTION']; ?></td>
                    <td width="80" align="center"><? echo $row['CHALAN_QTY']; ?></td>

                    <td width="80" align="center"><? echo $row['QUANTITY']; ?></td>
                    <td width="80" align="center"><? echo $row['REJECT_QTY']; ?></td>
                    <td width="80" align="center"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
                    <td width="80" align="center"><? echo $row['UOM_QTY']; ?></td>
                    <td width="80" align="center"><? echo $row['RATE']; ?></td>
                    <td width="80" align="center"><? echo $row['AMOUNT']; ?></td>
                    <td width="120" align="center"><? echo $row['BUYER_ORDER']; ?></td>
                    <td width="80" align="center"><? echo $buyer_arr[$buyerArr[$row['GET_PASS_DTLSID']]['BUYER_NAME']]; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><? echo $buyerArr[$row['GET_PASS_DTLSID']]['STYLE_REF_NO']; ?></td>
                    <td width="80" align="center"><? echo $row['REMARKS']; ?></td>
    
                </tr>
                <?
			    $i++;	
				$total_challan_qty+=$row['CHALAN_QTY'];
				$total_qty+=$row['QUANTITY'];
				$total_rej_qty+=$row['REJECT_QTY'];		   
			}
			exit();	
			?>
			
        </table>
		<!-- <table width="1295" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
			<tfoot>
				<tr align="right">
					<td width="400" align="center">Total</td>
                    <td width="80" align="center"><? echo $total_challan_qty; ?></td>
                    <td width="80" align="center"><? echo $total_qty; ?></td>
                    <td width="80" align="center"><? echo $total_rej_qty; ?></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="center"></td>
                    <td width="150" align="center"></td>
                    <td width="80" align="center"></td>
				</tr>
			</tfoot>
        </table> -->
    </div>

<?

} 

if($action=="child_form_input_data")
{
	//$data = details table ID 	
	//echo $data;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$sql = "SELECT a.id, a.sample_id, a.item_category_id, a.uom_qty, a.buyer_order, a.item_description, a.chalan_qty, a.quantity, a.uom, a.rate, a.amount, a.buyer_order, b.pi_reference, a.remarks, b.gate_pass_no, a.get_pass_dtlsid, a.reject_qty 
	from inv_gate_in_dtl a , inv_gate_in_mst b  
	where  a.mst_id=b.id  and a.id=$data and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ";
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$get_pass_dtls_id .= $row[csf('get_pass_dtlsid')].",";
	}

	$sys_numbers=$result[0][csf("gate_pass_no")];
	$dtls_id=$result[0][csf("id")];

	$sql_get_in_qnty="SELECT c.get_pass_dtlsid,b.gate_pass_no,c.item_description ,sum(c.quantity) as quantity  
	from inv_gate_in_mst b ,inv_gate_in_dtl c 
	where b.id=c.mst_id and b.gate_pass_no='$sys_numbers' and c.id not in ($dtls_id) and c.status_active=1 and b.status_active=1 group by c.get_pass_dtlsid,b.gate_pass_no,c.item_description ";
	foreach(sql_select($sql_get_in_qnty) as $key=>$value)
	{
		$get_in_qnty_arr_with_item[$value[csf("gate_pass_no")]][$value[csf("item_description")]] +=$value[csf("quantity")];
		$get_in_qnty_arr_dtls[$value[csf("gate_pass_no")]][$value[csf("item_description")]][$value[csf("get_pass_dtlsid")]] +=$value[csf("quantity")];
	}

    $all_get_pass_dtls_id = ltrim(implode(",", array_unique(explode(",", chop($get_pass_dtls_id, ",")))), ',');

	$style_sql = "SELECT B.ID,A.BUYER_NAME,A.STYLE_REF_NO,C.ID 
	FROM WO_PO_DETAILS_MASTER A, WO_PO_BREAK_DOWN B, inv_gate_pass_dtls c
	WHERE A.JOB_NO = B.JOB_NO_MST  and B.ID = C.BUYER_ORDER_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.ID in ($all_get_pass_dtls_id)";
	//echo $style_sql;
	$style_result=sql_select($style_sql);
	foreach($style_result as $row)
	{
		$buyerArr[$row['ID']]['BUYER_NAME'] = $row['BUYER_NAME'];
		$buyerArr[$row['ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
	}



	foreach($result as $row)
	{
		//$balance_qnty=$row[csf("chalan_qty")] - $get_in_qnty_arr_with_item[$row[csf("gate_pass_no")]][$row[csf("item_description")]];
		$balance_qnty=$row[csf("chalan_qty")] - $get_in_qnty_arr_dtls[$row[csf("gate_pass_no")]][$row[csf("item_description")]][$row[csf("get_pass_dtlsid")]];
		echo "$('#txtitemdescription_1').val('".$row[csf("item_description")]."');\n";
		if ($row[csf("pi_reference")] != "") echo "$('#txtitemdescription_1').attr('disabled',true);\n";
		else echo "$('#txtitemdescription_1').attr('disabled',false);\n";
		echo "$('#cbouom_1').val(".$row[csf("uom")].");\n";
		echo "$('#txtquantity_1').val(".$row[csf("quantity")].");\n";
		echo "$('#txtRejQuantity_1').val(".$row[csf("reject_qty")].");\n";
		//echo "$('#txtuomqty_1').val(".$row[csf("uom_qty")].");\n";
		echo "$('#txtrate_1').val(".$row[csf("rate")].");\n";
		echo "$('#txtamount_1').val(".$row[csf("amount")].");\n";		
 		echo "$('#txtremarks_1').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbosample_1').val(".$row[csf("sample_id")].");\n";
		echo "$('#cboitemcategory_1').val(".$row[csf("item_category_id")].");\n";	
		echo "$('#txtquantity_1').attr('placeholder',".$balance_qnty.");\n";	
  		echo "$('#txtcalanquantity_1').val('".$row[csf("chalan_qty")]."');\n";
		echo "$('#txtorder_1').val('".$row[csf("buyer_order")]."');\n";

		echo "$('#cbobuyer_1').val('".$buyer_arr[$buyerArr[$row[csf('get_pass_dtlsid')]]['BUYER_NAME']]."');\n";	
		echo "$('#txstyle_1').val('".$buyerArr[$row[csf('get_pass_dtlsid')]]['STYLE_REF_NO']."');\n";	

		echo "$('#updatedtlsid_1').val(".$row[csf("id")].");\n";
		if($row[csf("item_category_id")]!=0)
		{
			echo " gate_enable_disable(".$row[csf("item_category_id")].");\n";		
		}
		else if($row[csf("sample_id")]!=0)
		{
			echo " gate_enable_disable(".$row[csf("sample_id")].");\n";	
		}
		//echo "show_list_view(".$row[csf("wo_po_type")]."+'**'+".$row[csf("wo_pi_no")].",'show_product_listview','list_product_container','requires/yarn_receive_controller','');\n";
		echo "set_button_status(1, permission, 'fnc_getin_entry',1,1);\n";
	}
	exit();
}

if ($action=="get_in_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data[0]);
	$company=$data[0];
	$location=$data[4];
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$section_library=return_library_array( "select id,section_name from   lib_section", "id","section_name"  );
	$deparntment_library=return_library_array( "select id,department_name from   lib_department", "id", "department_name"  );
	$sample_library=return_library_array( "select id,sample_name from   lib_sample", "id", "sample_name"  );
	
	$sql="SELECT id,sys_number,company_id,within_group,party_type,returnable,carried_by,gate_pass_no,sending_company,receive_from,department_id,section,in_date,challan_no,time_hour,time_minute,vehicle_no ,loaded_weight,unloaded_weight,net_weight
	from inv_gate_in_mst 
	where sys_number='$data[1]' and status_active=1 and is_deleted=0";
	//echo $sql;
	$dataArray=sql_select($sql);
	$party_type=$dataArray[0][csf('party_type')];

	$sql_gatepass="SELECT id, basis, company_id, sent_to from inv_gate_pass_mst where sys_number='".$dataArray[0][csf('gate_pass_no')]."'";
	$sql_gatepass_res = sql_select($sql_gatepass);	
	
	if($dataArray[0][csf('party_type')]==1)
	{
	    $out_company_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
    }
	else if($dataArray[0][csf('party_type')]==2)
	{
	    $out_company_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	}
    else if($dataArray[0][csf('party_type')]==3)
	{
	    $out_company_arr=return_library_array( "select id, other_party_name from lib_other_party", "id", "other_party_name"  );
	}

	if ($dataArray[0][csf('within_group')] == 1)
	{
		$out_company=$company_library[$dataArray[0][csf('sending_company')]];
	}
	else if ($dataArray[0][csf('within_group')] == 2 && $dataArray[0][csf('returnable')] == 1)
	{
		$out_company=$sql_gatepass_res[0][csf('sent_to')];
	}
	else if ($dataArray[0][csf('within_group')] == 2 && $dataArray[0][csf('returnable')] == 2)
	{
		$out_company=$out_company_arr[$dataArray[0][csf('sending_company')]];
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);

	?>
	<div style="width:930px;" align="center">
		<table width="900" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="7" align="center">
					<?
						echo $com_dtls[1];
						//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";die;
						/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
						foreach ($nameArray as $result)
						{ 
						?>
							<? echo $result[csf('plot_no')]; ?> 
							<? echo $result[csf('level_no')]?>
							<? echo $result[csf('road_no')]; ?> 
							<? echo $result[csf('block_no')];?> 
							<? echo $result[csf('city')];?> 
							<? echo $result[csf('zip_code')]; ?> 
							<? echo $result[csf('province')];?> 
							<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
							<? echo $result[csf('email')];?> 
							<? echo $result[csf('website')];
						}*/
					?> 
				</td>
			</tr>
			<tr>
				<td colspan="7" align="center" style="font-size:x-large"><strong><u>Inword Gate Pass</u></strong></td>
			</tr>
			<tr>
				<td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
				<td width="120"><strong>Gate Pass ID:</strong></td><td width="175px" colspan="2"><? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
				<td width="125"><strong>Out Company:</strong></td><td width="175px"><? echo $out_company; ?></td>
			</tr>
			<tr>
				<td><strong>Receive From:</strong></td> <td width="175px"><? echo $dataArray[0][csf('receive_from')]; ?></td>
				<td><strong>Department:</strong></td><td width="175px" colspan="2"><? echo $deparntment_library[$dataArray[0][csf('department_id')]]; ?></td>
				<td><strong>Section:</strong></td><td width="175px"><? echo $section_library[$dataArray[0][csf('section')]]; ?></td>
			</tr>
			<tr>
				<td><strong>In Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('in_date')]); ?></td>
				<td><strong>Challan NO:</strong></td><td width="175px" colspan="2"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>IN Time:</strong></td><td width="175px"><? echo $dataArray[0][csf('time_hour')]." HH ".$dataArray[0][csf('time_minute')]." Min"; ?></td>
			</tr>
			<tr>
				<td><strong>Carried By:</strong></td> <td width="175px"><? echo $dataArray[0][csf('carried_by')]; ?></td>
				<td><strong>Vehicle No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('vehicle_no')]; ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td width="160"><strong>Loaded Weight:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('loaded_weight')]; ?></td>
				<td width="120"><strong>Unloaded Weight:</strong></td>
				<td width="175px" colspan="2"><? echo $dataArray[0][csf('unloaded_weight')]; ?></td>
				<td width="125"><strong>Net Weight:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('net_weight')]; ?></td>
			</tr>			
		</table>
		<br>
		<table align="center" cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" >
			<thead bgcolor="#dddddd" align="center">
				<th width="30">SL</th>
				<th width="100" align="center">Item Category</th>
				<th width="100" align="center">Sample</th>
				<th width="150" align="center">Item Description</th>
				<th width="50" align="center">UOM</th>
				<th width="80" align="center">Challan Qty</th>
				<th width="80" align="center">Quantity</th>
				<th width="80" align="center">Reject Qty</th>
				<th width="80" align="center">UOM Qty.</th>
				<th width="80" align="center">Rate</th> 
				<th width="80" align="center">Amount </th>
				<th width="80" align="center">Buyer Order </th>
				<th width="100" align="center">Remarks</th>
			</thead>
			<?
			$i=1;
			$gate_id=$dataArray[0][csf('id')];
			$sql_dtls= "SELECT id,sample_id,item_category_id,uom_qty,buyer_order,chalan_qty,item_description, uom, quantity,reject_qty, rate, amount, remarks from inv_gate_in_dtl where mst_id=$gate_id and status_active=1 and is_deleted=0 order by id asc";
			//echo $sql_dtls;
			$sql_result=sql_select($sql_dtls);
			
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF";
				else $bgcolor="#FFFFFF";
				$chalan_qty+=$row[csf('chalan_qty')];
				$quantity+=$row[csf('quantity')];
				$reject_qty+=$row[csf('reject_qty')];
				$tot_uom_qty+=$row[csf('uom_qty')];
				$amount+=$row[csf('amount')];					
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td><? echo $i; ?></td>
					<td><?  echo $item_category[$row[csf('item_category_id')]]; ?></td>
					<td><?  echo $sample_library[$row[csf('sample_id')]]; ?></td>
					<td><?  echo $row[csf('item_description')]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
					<td align="right"><? echo $row[csf('chalan_qty')]; ?></td>
					<td align="right"><? echo $row[csf('quantity')]; ?></td>
					<td align="right"><? echo $row[csf('reject_qty')]; ?></td>
					<td align="right"><? echo $row[csf('uom_qty')]; ?></td>
					<td align="right"><? echo number_format($row[csf('rate')],2,'.',''); ?></td>
					<td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
					<td align="right"><? echo $row[csf('buyer_order')]; ?></td>
					<td><? echo $row[csf('remarks')]; ?></td>
				</tr>				
				<?
				$uom_unit="Kg";
				$uom_gm="Grams";
				$i++;
			}
			?>
			<tfoot>
				<tr>						
					<th colspan="5" align="right">Total</th>
					<th width="" align="right"><? echo $chalan_qty ; ?> </th>
					<th width="" align="right"><? echo number_format($quantity,2,'.','') ; ?></th>
					<th width="" align="right"><? echo $reject_qty ; ?></th>
					<th width="" align="right"><? echo $tot_uom_qty ; ?></th>
					<th width="" align="center"></th> 
					<th width="" align="right"><? echo number_format($amount,2,'.',''); ; ?> </th>
					<th width="" align="center">  </th>
					<th width="" align="center"></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<div>
		<? echo signature_table(33, $data[0], "900px"); ?>
	</div>
    <?
	exit();
}

if($action=="items_list_view_action")
{
	extract($data); 
	$data=explode("*",$data);	
	$item_cat_refr_no=$data[2];
	$list_type=$data[6];
	//echo $item_cat_refr_no."=".$data[1]."=".$list_type;die;
	if($data[1]==1) // pi
	{
	    if($db_type==0)
	    {
		    $sql = "SELECT a.id,a.item_category_id,b.pi_id,a.pi_number,b.determination_id,b.fabric_composition,b.fabric_construction,b.item_description,b.body_part_id,b.fab_type,b.fab_design,b.quantity,b.uom,b.item_size,b.rate,b.amount,concat(a.id,'_1_".$item_cat_refr_no."_',a.pi_number,b.id) as id_type_pi 
			from com_pi_master_details a,com_pi_item_details b 
			where a.id=b.pi_id and a.is_deleted=0 and a.status_active=1 and a.pi_number='$data[3]'";  
	    }
	    if($db_type==1 || $db_type==2)
	    {
		    $sql = "SELECT a.id,a.item_category_id,b.pi_id,a.pi_number,b.determination_id,b.fabric_composition,b.fabric_construction, b.item_description,b.body_part_id,b.fab_type,b.fab_design, b.quantity,b.uom,b.item_size,b.rate,b.amount,a.id || '_1_".$item_cat_refr_no."_' || a.pi_number || '_' || b.id as id_type_pi 
			from com_pi_master_details a,com_pi_item_details b 
			where a.id=b.pi_id and a.is_deleted=0 and a.status_active=1 and a.pi_number='$data[3]'"; 
			//echo $sql;
	    }
	  	$sql_res = sql_select($sql);
	    ?>
   		<div>
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table">
	            <thead>		            	
		            <tr>
		            	<th width="20">SL</th>
		            	<th width="80">Item Category</th>
		            	<th width="120">Item Description</th>
		            	<th width="40">UOM</th>
		            	<th width="60">Quantity</th>
		            	<th>Item Size</th>
		            </tr>				
	            </thead>
	        </table>
	        <div style="width:400px; overflow-y: auto; max-height: 150px;">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table" id="list_view">
	            	<?
                    $lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
	            	$i=1;
	            	foreach ($sql_res as $row)
	            	{
						if($row[csf("item_category_id")]==3)
						{
							$fab_description=$lib_body_part_arr[$row[csf('body_part_id')]]." ".$row[csf('fab_type')]." ".$row[csf('fabric_construction')]." ".$row[csf('fab_design')]." ".$row[csf('fabric_composition')];
						}
						else
						{							
							$fab_description=$row[csf("item_description")];													
						}					
												
		            	if ($i % 2 == 0) $bgcolor="#E9F3FF"; 
						else $bgcolor="#FFFFFF";
		            	?>
		            	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="set_pi_id('<? echo $row[csf('id_type_pi')]; ?>')">
		                    <td width="20" align="center"><? echo $i; ?></td>
		                    <td width="80"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
		                    <td width="120" title="<?=$row[csf("item_description")];?>"><p><? echo $fab_description; ?></p></td>
		                    <td width="40" align="center"><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?></p></td>
		                    <td width="60" align="right"><p><? echo $row[csf("quantity")]; ?></p></td>
		                    <td><p><? echo $row[csf("item_size")]; ?></p></td>		                    
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
	if($data[1]==2)// WO
	{
		//echo "test".$item_cat_refr_no;die;
		if($item_cat_refr_no == 2 || $item_cat_refr_no == 3)
		{
			if($db_type==0)
	  		{
				$sql= "SELECT a.id, a.booking_no_prefix_num, a.item_category,b.construction,b.composition,b.fabric_description,b.finish_fabric,sum(b.amount) as amount,sum(b.rate) as rate,b.uom,b.item_size,concat(b.id,'_2_".$item_cat_refr_no."_',a.booking_no_prefix_num) as id_booking_no_prefix_num 
				from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category = 2 and a.status_active=1 and a.is_deleted=0 and a.booking_no_prefix_num='$data[3]' 
				group by a.id, a.booking_no_prefix_num, a.item_category,b.construction,b.composition,b.fabric_description,b.uom,b.item_size,b.finish_fabric,concat(a.id,'_2_".$item_cat_refr_no."_',a.booking_no_prefix_num)";
			}
			if($db_type==1 || $db_type==2)
	  		{
				if($list_type==2)
				{
					$sql= "SELECT a.id, a.booking_no_prefix_num, a.item_category, b.construction, b.copmposition as composition, b.uom, b.item_size, b.fabric_color_id, c.fabric_description, sum(b.fin_fab_qnty) as finish_fabric, sum(b.amount) as amount, avg(b.rate) as rate, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id)  || '_2_' || a.item_category || '_' || a.booking_no_prefix_num || '_With Order' as id_booking_no_prefix_num, 2 as type
					from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
					where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.item_category = $item_cat_refr_no and a.status_active=1 and a.is_deleted=0 and a.booking_no_prefix_num='$data[3]'
					group by a.id, a.booking_no_prefix_num, a.item_category, b.construction, b.copmposition, b.construction,b.copmposition, b.uom, b.item_size, b.fabric_color_id, c.fabric_description";
				}
				else
				{
					$sql= "SELECT a.id, a.booking_no_prefix_num, a.item_category, b.construction, b.composition, b.fabric_description, b.uom, b.item_size, b.fin_fab_qnty as finish_fabric, b.amount as amount, b.rate as rate, b.id || '_2_' || a.item_category || '_' || a.booking_no_prefix_num || '_Without Order' as id_booking_no_prefix_num, 3 as type
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
					where a.booking_no=b.booking_no and a.item_category = $item_cat_refr_no and a.status_active=1 and a.is_deleted=0 and a.booking_no_prefix_num='$data[3]'";
				}
				//echo $sql;
				
			}
			$lib_color_arr=return_library_array("select id, color_name from lib_color", "id", "color_name");
			?>
            <div>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table">
                    <thead>		            	
                        <tr>
						<th width="20">SL No</th>
                            <th width="60">Item Category</th>
                            <th width="90">Item Description</th>
                            <th width="60">Fab. Color</th>
                            <th width="50">Item Size</th>
                            <th width="40">UOM</th>
                            <th>Quantity</th>
                        </tr>				
                    </thead>
                </table>
                <div style="width:400px; max-height:150px; overflow-y:scroll" id="list_container_batch">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table" id="list_view">
                        <?
                        $i=1;
						//echo $sql;
						$sql_res=sql_select($sql);
						//echo "<pre>";print_r($sql_res);die;
                        foreach ($sql_res as $row)
                        {
                                                    
                            if ($i % 2 == 0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="set_pi_id('<? echo $row[csf('id_booking_no_prefix_num')]; ?>')">
							<td width="20" align="center"><? echo $i; ?></td>
                                <td width="60"><p><? echo $item_category[$row[csf("item_category")]]; ?></p></td>
                                <td width="90" style="word-break: break-all;"><p><? echo $row[csf("fabric_description")]; ?></p></td>
                                <td width="60" align="center"><p><? echo $lib_color_arr[$row[csf("fabric_color_id")]]; ?></p></td>
                                <td width="50" align="center"><p><? echo $row[csf("item_size")]; ?></p></td>
                                <td width="40" align="center" title="<? echo $row[csf("uom")]; ?>"><p><? echo $unit_of_measurement[$row[csf("uom")]];?></p></td>
                                <td align="right"><p><? echo number_format($row[csf("finish_fabric")],2); ?></p></td>
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
		else if($item_cat_refr_no == 4)
		{
			//echo "test";die;
			if($db_type==0)
	  		{
	  			if ($data[4]=="With Order") 
				{
				 	$sql = "SELECT a.id, a.booking_no_prefix_num as wo_number,
					a.item_category as item_category,b.uom,b.item_size,sum(b.rate) as rate,sum(b.amount) as amount,b.wo_qnty as supplier_order_quantity,b.description as item_description,concat(b.id,'_2_".$item_cat_refr_no."_',a.booking_no_prefix_num,'_With Order') as id_wo_number
					from wo_booking_mst a,wo_booking_dtls b 
					where a.booking_no=b.booking_no and a.id=$data[0] and a.item_category = $item_cat_refr_no and a.status_active=1 and a.is_deleted=0  
					group by a.id, a.booking_no_prefix_num , a.item_category,b.uom,b.item_size,b.wo_qnty,b.description,b.id || '_2_".$item_cat_refr_no."_' || a.booking_no_prefix_num,'_With Order'";				 
				}
				else
				{
					$sql= "SELECT a.id, a.wo_number_prefix_num as wo_number, a.item_category,b.uom,c.item_size,sum(b.rate) as rate,sum(b.amount) as amount, b.supplier_order_quantity,c.item_description,concat(b.id,'_2_".$item_cat_refr_no."_',a.wo_number_prefix_num,'_Without Order') as id_wo_number 
					from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master c 
					where a.item_category = 4 and a.id=b.mst_id and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and a.wo_number_prefix_num='$data[3]' 
					group by a.id, a.wo_number_prefix_num, a.item_category,b.uom,c.item_size,c.item_description,b.supplier_order_quantity,concat(b.id,'_2_".$item_cat_refr_no."_',a.wo_number_prefix_num,'_Without Order')";
					//echo $sql;
				}
			}
			if($db_type==1 || $db_type==2)
	  		{
				if($list_type==2)
				{
					$sql= "SELECT a.id, a.booking_no_prefix_num, a.item_category, b.construction, b.copmposition as composition, b.description , b.uom, b.item_size, b.wo_qnty as wo_qnty, b.amount as amount, b.rate as rate, b.id || '_2_' || a.item_category || '_' || a.booking_no_prefix_num || '_With Order' as id_booking_no_prefix_num, 2 as type
					from wo_booking_mst a, wo_booking_dtls b 
					where a.booking_no=b.booking_no and a.item_category = $item_cat_refr_no and a.status_active=1 and a.is_deleted=0 and a.booking_no_prefix_num='$data[3]' and a.id='$data[0]'";
				}
				else
				{
					// $sql= "SELECT a.id, a.booking_no_prefix_num, a.item_category, b.construction, b.composition, b.description, b.uom, b.item_size, b.wo_qnty as wo_qnty, b.amount as amount, b.rate as rate, b.id || '_2_' || a.item_category || '_' || a.booking_no_prefix_num || '_Without Order' as id_booking_no_prefix_num, 3 as type
					// from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
					// where a.booking_no=b.booking_no and a.item_category = $item_cat_refr_no and a.status_active=1 and a.is_deleted=0 and a.booking_no_prefix_num='$data[3]' and a.id='$data[0]'";


					$sql= "SELECT a.id, a.item_category, TO_NCHAR(b.FABRIC_DESCRIPTION) as description, b.uom, b.item_size, b.wo_qty as wo_qnty, b.id || '_2_' || a.item_category || '_' || a.booking_no_prefix_num || '_Without Order' as id_booking_no_prefix_num, 3 as type
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
					where a.booking_no=b.booking_no and a.item_category = $item_cat_refr_no and a.status_active=1 and a.is_deleted=0 and a.booking_no_prefix_num='$data[3]' and a.id='$data[0]'
					union all 
					SELECT a.id,c.item_category_id as item_category,c.item_description as description,b.uom,c.item_size,b.supplier_order_quantity as wo_qnty, b.id || '_2_".$item_cat_refr_no."_' || a.wo_number_prefix_num as id_booking_no_prefix_num ,3 as type
					from wo_non_order_info_mst a, wo_non_order_info_dtls b,product_details_master c 
					where b.item_id=c.id and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.mst_id='$data[0]' 
					";

				}
				//  echo $sql;
 
			}			

			$arr=array(0=>$item_category,4=>$unit_of_measurement);
			echo create_list_view("list_view", "Item Category,Item Description,Quantity,Item Size,UOM","70,100,60,50,40","380","250",0, $sql, "get_php_form_data", "id_booking_no_prefix_num,0,type", "'child_form_item_list','requires/get_in_entry_controller'", 1, "item_category,0,0,0,uom", $arr, "item_category,description,wo_qnty,item_size,uom", "","",'0,0,1,0,0');	
		 	exit();
		}
		else if ($item_cat_refr_no == 14)
		{
			if($db_type==0)
	  		{				
				$sql = "SELECT a.id, 14 AS item_category_id, a.booking_no_prefix_num AS wo_number_prefix_num, b.uom, b.item_size, SUM (b.rate) AS rate, SUM (b.amount) AS amount, SUM (b.grey_fab_qnty) AS qnty, c.fabric_desc as item_description, concat(b.id || '_2_14_' || a.booking_no_prefix_num) AS id_booking_no_prefix_num
				FROM wo_booking_mst a, wo_booking_dtls b,fabric_sales_order_dtls c
				WHERE a.id = b.booking_mst_id AND b.pre_cost_fabric_cost_dtls_id = c.mst_id AND b.po_break_down_id = c.id AND a.is_deleted = 0 AND a.status_active = 1 AND b.booking_mst_id = '$data[0]' AND a.booking_type = 7 AND a.entry_form = 549
				GROUP BY a.id, a.booking_no_prefix_num, b.uom, c.fabric_desc, b.item_size, concat(b.id || '_2_14_' || a.booking_no_prefix_num)";
			}
			if($db_type==1 || $db_type==2)
	  		{
				$sql = "SELECT a.id, 14 AS item_category_id, a.booking_no_prefix_num AS wo_number_prefix_num, b.uom, b.item_size, SUM (b.rate) AS rate, SUM (b.amount) AS amount, SUM (b.grey_fab_qnty) AS supplier_order_quantity, c.fabric_desc as item_description, b.id || '_2_14_' || a.booking_no_prefix_num AS id_booking_no_prefix_num
				FROM wo_booking_mst a, wo_booking_dtls b,fabric_sales_order_dtls c
				WHERE a.id = b.booking_mst_id AND b.pre_cost_fabric_cost_dtls_id = c.mst_id AND b.po_break_down_id = c.id AND a.is_deleted = 0 AND a.status_active = 1 AND b.booking_mst_id = '$data[0]' AND a.booking_type = 7 AND a.entry_form = 549
				GROUP BY a.id, a.booking_no_prefix_num, b.uom, c.fabric_desc, b.item_size, b.id || '_2_14_' || a.booking_no_prefix_num";
			}
			//echo $sql;

			$arr=array(0=>$item_category,4=>$unit_of_measurement);
	   		echo create_list_view("list_view", "Item Category,Item Description,Quantity,Item Size,UOM","70,100,60,50,40","380","250",0, $sql, "get_php_form_data", "id_booking_no_prefix_num", "'child_form_item_list','requires/get_in_entry_controller'", 1, "item_category_id,0,0,0,uom", $arr, "item_category_id,item_description,supplier_order_quantity,item_size,uom", "","");
			exit();
		}
		else
		{
			if($db_type==0)
	  		{				
				$sql = "SELECT a.id, c.item_category_id, a.wo_number_prefix_num, b.uom, c.item_size,sum(b.rate) as rate, sum(b.amount) as amount, b.supplier_order_quantity, c.item_description, concat(b.id,'_2_".$item_cat_refr_no."_',a.wo_number_prefix_num) as id_booking_no_prefix_num 
				from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master c 
				where b.item_id=c.id and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.mst_id='$data[0]' 
				group by a.id,c.item_category_id,a.wo_number_prefix_num,b.uom,c.item_size,b.supplier_order_quantity,c.item_description,concat(b.id,'_2_".$item_cat_refr_no."_',a.wo_number_prefix_num)";
			}
			if($db_type==1 || $db_type==2)
	  		{
				$sql = "SELECT a.id,c.item_category_id,a.wo_number_prefix_num,b.uom,c.item_size,sum(b.rate) as rate,sum(b.amount) as amount, b.supplier_order_quantity,c.item_description,b.id || '_2_".$item_cat_refr_no."_' || a.wo_number_prefix_num as id_booking_no_prefix_num 
				from wo_non_order_info_mst a, wo_non_order_info_dtls b,product_details_master c 
				where b.item_id=c.id and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.mst_id='$data[0]' 
				group by a.id,c.item_category_id,a.wo_number_prefix_num,b.uom,c.item_size,b.supplier_order_quantity,c.item_description,b.id || '_2_".$item_cat_refr_no."_' || a.wo_number_prefix_num";
				
			}
			//echo $sql;		
			$arr=array(0=>$item_category,4=>$unit_of_measurement);

	   		echo create_list_view("list_view", "Item Category,Item Description,Quantity,Item Size,UOM","70,100,60,50,40","380","250",0, $sql, "get_php_form_data", "id_booking_no_prefix_num", "'child_form_item_list','requires/get_in_entry_controller'", 1, "item_category_id,0,0,0,uom", $arr, "item_category_id,item_description,supplier_order_quantity,item_size,uom", "","");
			exit();
		}	
	}
	if($data[1]==3) //req
	{
		$category_cond 	= ($item_cat_refr_no != 0)?"b.item_category = $item_cat_ref":"b.item_category not in (1,2,3,5,6,7,12,13,14)";
		$reference_cond = ($txt_refe != '')?" and a.requ_no like '%$txt_refe%'":"";
		if($db_type==0)
	  	{			
			$sql = "SELECT a.item_account,a.item_description,a.item_size,a.item_group_id,b.required_for,a.unit_of_measure,a.re_order_label,b.id,b.quantity,b.rate,b.amount,b.stock,b.status_active,b.remarks,c.item_name,b.item_category as item_category_id,concat(b.id,'_3_".$item_cat_refr_no."_',d.requ_no) as id_requ_no 
			from product_details_master a,inv_purchase_requisition_dtls b, lib_item_group c,inv_purchase_requisition_mst d 
			where b.is_deleted=0 and b.mst_id='$data[0]' and a.id=b.product_id and a.item_group_id=c.id and b.mst_id=d.id";
		}
		if($db_type==1 || $db_type==2)
	  	{
		   $sql = "SELECT a.item_account,a.item_description,a.item_size,a.item_group_id,b.required_for,a.unit_of_measure,a.re_order_label,b.id,b.quantity,b.rate,b.amount,b.stock,b.status_active,b.remarks,c.item_name,b.item_category as item_category_id,b.id || '_3_".$item_cat_refr_no."_' || d.requ_no as id_requ_no 
		   from product_details_master a,inv_purchase_requisition_dtls b, lib_item_group c,inv_purchase_requisition_mst d 
		   where b.is_deleted=0 and b.mst_id='$data[0]' and a.id=b.product_id and a.item_group_id=c.id and b.mst_id=d.id";
			
		}
	  	//echo $sql;
		
	    $arr=array(0=>$item_category,4=>$unit_of_measurement);
	    echo create_list_view("list_view", "Item Category,Item Description,Quantity,Item Size,UOM","70,100,60,50,40","380","250",0, $sql, "get_php_form_data", "id_requ_no", "'child_form_item_list','requires/get_in_entry_controller'", 1, "item_category_id,0,0,0,unit_of_measure", $arr, "item_category_id,item_description,quantity,item_size,unit_of_measure", "","");	
	    exit();
	}
	if($data[1]==4)//Trims WO
	{
		if($item_cat_refr_no == 4)
		{
			//echo $item_cat_refr_no."=".$data[4]."=".$data[5]."=".$list_type;die;
			if($data[4]==5 && $data[5]=="") // without order
			{
				if($db_type==0)
				{
					$sql= "SELECT a.item_category, b.uom, b.item_size, b.trim_group as item_group_id, b.composition as item_description, b.trim_qty as order_quantity, b.fabric_description as item_description, concat(b.id,'_4_".$item_cat_refr_no."_',a.wo_number_prefix_num,'_',a.booking_type,'_',a.is_short) as id_wo_number 
					FROM  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
					WHERE a.booking_no=b.booking_no and a.booking_no_prefix_num ='$data[3]' and a.id='$data[0]'  and b.status_active=1 and	b.is_deleted=0"; 
				//wo_non_ord_samp_booking_mst
				}
				else
				{
					$sql= "SELECT a.item_category, b.uom, b.item_size, b.trim_group as item_group_id, b.composition as item_description, b.trim_qty as order_quantity, b.fabric_description as item_description, b.id || '_4_".$item_cat_refr_no."_' || a.booking_no_prefix_num ||'_'||a.booking_type||'_'||a.is_short as id_wo_number  
					FROM  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
					WHERE a.booking_no=b.booking_no and a.booking_no_prefix_num ='$data[3]' and a.id='$data[0]'  and b.status_active=1 and b.is_deleted=0"; 
				//wo_non_ord_samp_booking_mst
				}
			}
			else if($data[4]==5 && $data[5]==2) // with ortder
			{
				if($db_type==0)
				{
					$sql="SELECT a.item_category, b.trim_group as item_group_id, c.description as item_description, b.uom, b.item_size, b.wo_qnty as order_quantity, concat(b.id,'_4_".$item_cat_refr_no."_',a.booking_no_prefix_num,'_',a.booking_type,'_',a.is_short) as id_wo_number 
					from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c  
					where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id  and a.booking_no_prefix_num='$data[3]' and a.id='$data[0]' and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0";
				//wo_trim_book_con_dtls
				}
				else
				{
					$sql="SELECT a.item_category, b.trim_group as item_group_id, c.description as item_description, b.uom, b.item_size, b.wo_qnty as order_quantity, b.id || '_4_".$item_cat_refr_no."_' || a.booking_no_prefix_num ||'_'||a.booking_type||'_'||a.is_short as id_wo_number 
					from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c  
					where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id  and a.booking_no_prefix_num='$data[3]' and a.id='$data[0]' and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0";
				//wo_trim_book_con_dtls
				}
			}
			else if($data[4]==0 && $data[5]==0) // general accessories
			{
				if($db_type==0)
				{
					$sql = " SELECT a.id, a.wo_number_prefix_num as wo_number, a.item_category, b.uom, c.item_size, sum(b.rate) as rate, sum(b.amount) as amount, b.supplier_order_quantity as order_quantity, c.item_description, c.item_group_id, concat(b.id,'_4_".$item_cat_refr_no."_',a.wo_number_prefix_num,'_0_0') as id_wo_number  
					from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c 
					where a.item_category = 4 and a.id=b.mst_id and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and a.id='$data[0]' and a.wo_number_prefix_num='$data[3]' 
					group by a.id, a.wo_number_prefix_num, a.item_category,b.uom,c.item_size,b.supplier_order_quantity, c.item_description, c.item_group_id, b.id || '_4_".$item_cat_refr_no."_' || a.wo_number_prefix_num ||'_0_0' 
					order by a.id";
				}
				else
				{
					$sql = "SELECT a.id, a.wo_number_prefix_num as wo_number, a.item_category, b.uom, c.item_size, sum(b.rate) as rate, sum(b.amount) as amount, b.supplier_order_quantity as order_quantity, c.item_description, c.item_group_id, b.id || '_4_".$item_cat_refr_no."_' || a.wo_number_prefix_num ||'_0_0' as id_wo_number 
					from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master c 
					where a.item_category = 4 and a.id=b.mst_id and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and a.id='$data[0]' and a.wo_number_prefix_num='$data[3]' 
					group by a.id, a.wo_number_prefix_num, a.item_category,b.uom,c.item_size,b.supplier_order_quantity, c.item_description, c.item_group_id,b.id || '_4_".$item_cat_refr_no."_' || a.wo_number_prefix_num ||'_0_0' 
					order by a.id";
				//wo_non_order_info_mst,wo_non_order_info_dtls
				}
			}
			else // short,main trims booking
			{
				if($db_type==0)
				{
					$sql="SELECT a.item_category, b.trim_group as item_group_id, b.description as item_description, b.uom, b.item_size, b.wo_qnty as order_quantity, concat(b.id,'_4_".$item_cat_refr_no."_',a.booking_no_prefix_num,'_',a.booking_type,'_',a.is_short) as id_wo_number 
					from wo_booking_mst a, wo_booking_dtls b  
					where a.booking_no=b.booking_no and a.booking_no_prefix_num='$data[3]' and a.id='$data[0]' and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0";
				}
				else
				{
				//item_category,item_description,supplier_order_quantity,uom			
					$sql = "SELECT a.item_category, b.trim_group as item_group_id, b.description as item_description, b.uom, b.item_size, b.wo_qnty as order_quantity, b.id || '_4_".$item_cat_refr_no."_' || a.booking_no_prefix_num ||'_'||a.booking_type||'_'||a.is_short as id_wo_number 
					from wo_booking_mst a, wo_booking_dtls b  
					where a.booking_no=b.booking_no and a.booking_no_prefix_num='$data[3]' and a.id='$data[0]' and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0";
				}
				//echo $sql;
			}
			//echo $sql;
			$lib_group_arr=return_library_array("SELECT id, item_name from lib_item_group where status_active=1 and item_category=4", "id", "item_name");		
			$arr=array(0=>$item_category,1=>$lib_group_arr,5=>$unit_of_measurement);
			echo create_list_view("list_view", "Item Category,Item Group,Item Description,Quantity,Item Size,UOM","70,60,100,60,60,40","380","250",0, $sql, "get_php_form_data", "id_wo_number", "'child_form_item_list','requires/get_in_entry_controller'", 1, "item_category,item_group_id,0,0,0,uom", $arr, "item_category,item_group_id,item_description,order_quantity,item_size,uom", "","",'0,0,0,2,0,0');	
			exit();
		}
		else
		{}			
	}
}

if($action=="child_form_item_list")
{
	$data=explode("_",$data);
	//print_r($data);
	//$data[0]= 'mst id'; //$data[1]='type'; //$data[2]='item cat';	//$data[3]='pi number';//$data[4]='dtls_id';
	$item_cat_refr_no=$data[2];
	$list_type=$data[1];
	//echo $list_type;die;
	if($data[1]==1) // pi
	{	
	    $sql = "SELECT a.id,a.item_category_id,b.pi_id,a.pi_number,b.determination_id,b.body_part_id,b.fab_type,b.fab_design,b.fabric_construction,b.fabric_composition as fabric_composition2,b.item_description as fabric_composition,b.quantity,b.uom,b.rate,b.amount,b.color_id as fabric_color_id
	    from com_pi_master_details a,com_pi_item_details b 
	    where a.id=b.pi_id and a.is_deleted=0 and a.status_active=1 and a.id='$data[0]' and b.id='$data[4]'"; 
	    // echo $sql;
	}
	if($data[1]==2) // wo
	{
		//echo $item_cat_refr_no;die;
		if($item_cat_refr_no == 2 || $item_cat_refr_no == 3)
		{
			if($list_type==2)
			{
				$sql= "SELECT a.id, a.booking_no_prefix_num, a.item_category as item_category_id, b.construction, b.copmposition as composition, b.construction || b.copmposition as fabric_composition, b.fabric_color_id, b.uom, sum(b.fin_fab_qnty) as quantity, sum(b.amount) as amount, avg(b.rate) as rate 
				from wo_booking_mst a, wo_booking_dtls b  
				where a.booking_no=b.booking_no and a.item_category = $item_cat_refr_no and a.status_active=1 and a.is_deleted=0 and b.id in($data[0]) 
				group by a.id, a.booking_no_prefix_num, a.item_category, b.construction, b.copmposition, b.fabric_color_id, b.uom";
				//echo $sql;
			}
			else
			{
				$sql= "SELECT a.id, a.booking_no_prefix_num, a.item_category as item_category_id, b.construction, b.composition, b.fabric_description as fabric_composition, b.finish_fabric as quantity, b.amount, b.rate, b.uom
				from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
				where a.booking_no=b.booking_no and a.item_category = $item_cat_refr_no and a.status_active=1 and a.is_deleted=0 and b.id='$data[0]'";
			}
		}
		else if($item_cat_refr_no == 4)
		{
			if ($data[4]=="With Order") 
			{
				$sql="SELECT a.id, a.booking_no_prefix_num as wo_number, a.item_category as item_category_id,b.uom,sum(b.rate) as rate,sum(b.amount) as amount,b.wo_qnty as quantity,b.description as fabric_composition 
				from wo_booking_mst a,wo_booking_dtls b 
				where a.booking_no=b.booking_no and b.id=$data[0] and a.item_category = $item_cat_refr_no and a.status_active=1 and a.is_deleted=0 group by a.id, a.booking_no_prefix_num , a.item_category,b.uom,b.wo_qnty,b.description";
				
			}
			else
			{
				$sql="SELECT a.id, a.wo_number_prefix_num as wo_number, b.item_category_id as item_category_id,b.uom,b.rate,b.amount,b.supplier_order_quantity as quantity,c.item_description as fabric_composition 
				from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master c  
				where b.item_category_id = 4 and a.id=b.mst_id and b.item_id=c.id and c.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.id='$data[0]' 
				group by  a.id, a.wo_number_prefix_num, b.item_category_id,b.uom,b.rate,b.amount,b.supplier_order_quantity,c.item_description";
			}		
		}
		else if($item_cat_refr_no == 14)
		{
			$sql = "SELECT a.id, 14 AS item_category_id, a.booking_no_prefix_num AS wo_number_prefix_num, b.uom, b.item_size, b.rate AS rate, (b.amount) AS amount, (b.grey_fab_qnty) AS quantity, c.fabric_desc as fabric_composition, b.fabric_color_id, c.order_uom as uom FROM wo_booking_mst a, wo_booking_dtls b,fabric_sales_order_dtls c WHERE a.id = b.booking_mst_id AND B.PRE_COST_FABRIC_COST_DTLS_ID = c.mst_id AND B.PO_BREAK_DOWN_ID = c.id AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active = 1 AND c.is_deleted = 0 AND c.status_active = 1 AND b.id='$data[0]' AND a.booking_type = 7 AND a.entry_form = 549 GROUP BY a.id, a.booking_no_prefix_num, b.uom, b.rate, b.amount, b.grey_fab_qnty, b.item_size, b.fabric_color_id, c.fabric_desc, c.order_uom";
		}
		else
		{
			$sql = "SELECT a.id,b.item_category_id as item_category_id,a.wo_number_prefix_num,b.uom,b.rate,b.amount,b.supplier_order_quantity as quantity,c.item_description as fabric_composition,c.color as fabric_color_id 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b,product_details_master c 
			where b.item_id=c.id and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.id='$data[0]' 
			group by a.id,b.item_category_id,a.wo_number_prefix_num,b.uom,b.rate,b.amount,b.supplier_order_quantity,c.item_description,c.color";			
			//echo $sql; 
		}	
	}
	if($data[1]==3) // req
	{
		$sql = "SELECT a.item_account,a.item_description as fabric_composition,a.color as fabric_color_id,a.item_size,a.item_group_id,b.required_for,a.unit_of_measure as uom,a.re_order_label,b.id,b.quantity,b.rate,b.amount,b.stock,b.status_active,b.remarks,c.item_name,b.item_category as item_category_id 
		from product_details_master a,inv_purchase_requisition_dtls b, lib_item_group c,inv_purchase_requisition_mst d 
		where b.is_deleted=0 and b.id='$data[0]' and a.id=b.product_id and a.item_group_id=c.id and b.mst_id=d.id";
		//echo $sql;		
	}
	
	if($data[1]==4) // wo Trims
	{		
		if($item_cat_refr_no == 4 && $data[4] == 0 && $data[5] == 0) //general accessories
		{
			$sql="SELECT a.id, a.wo_number_prefix_num as wo_number, a.item_category as item_category_id,b.uom,b.rate,b.amount,b.supplier_order_quantity as quantity,c.item_description as fabric_composition, c.item_group_id 
			from wo_non_order_info_mst a,wo_non_order_info_dtls b,product_details_master c  
			where a.item_category = 4 and a.id=b.mst_id and b.item_id=c.id and c.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.id='$data[0]' 
			group by  a.id, a.wo_number_prefix_num, a.item_category,b.uom,b.rate,b.amount,b.supplier_order_quantity,c.item_description, c.item_group_id";			
		}
		else if($item_cat_refr_no == 4 && $data[4]==5 && $data[5]=="") // without order 
		{			
		 	$sql= "SELECT a.id, a.booking_no_prefix_num as wo_number, a.item_category as item_category_id,b.construction,b.composition,b.fabric_description as fabric_composition,b.finish_fabric as quantity,b.amount,b.rate,b.uom, b.trim_group as item_group_id 
			from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b 
			where a.booking_no=b.booking_no and  a.status_active=1 and a.is_deleted=0 and b.id='$data[0]' 
			group by a.id, a.booking_no_prefix_num, a.item_category ,b.construction,b.composition,b.fabric_description,b.finish_fabric,b.amount,b.rate,b.uom, b.trim_group";
			//wo_non_ord_samp_booking_mst			
		}
		else if($item_cat_refr_no == 4 && $data[4]==5 && $data[5]==2) // with ortder
		{
			$sql="SELECT a.id,a.booking_no_prefix_num as wo_number,a.item_category as item_category_id,c.description as fabric_composition,b.uom, b.wo_qnty as quantity, c.amount,c.rate, b.trim_group as item_group_id 
			from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c  
			where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and b.id='$data[0]' 
			group by a.id,a.booking_no_prefix_num,a.item_category,c.description,b.uom, b.wo_qnty, c.amount,c.rate, b.trim_group";
			//wo_trim_book_con_dtls
			
		}
		else // short trims/main trims
		{
			$sql="SELECT a.id,a.item_category as item_category_id,b.description as fabric_composition,b.uom, b.wo_qnty as quantity,b.amount,b.rate, b.trim_group as item_group_id,b.fabric_color_id 
			from wo_booking_mst a, wo_booking_dtls b  
			where a.booking_no=b.booking_no and a.booking_no_prefix_num='$data[3]' and b.id='$data[0]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0";
			//echo $sql;
		}
	}
	
	$result = sql_select($sql);
	echo "reset_form('','','cboitemcategory_1*cbosample_1*txtitemdescription_1*txtcalanquantity_1*txtquantity_1*cbouom_1*txtrate_1*txtamount_1*txtorder_1*txtremarks_1','','','');\n";
	$lib_body_part_arr=return_library_array("SELECT id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
	$lib_group_arr=return_library_array("SELECT id, item_name from lib_item_group where status_active=1 and item_category=4", "id", "item_name");
	foreach($result as $row)
	{
		if($data[1]==4)
		{
			echo "$('#txtitemdescription_1').val('".$lib_group_arr[$row[csf("item_group_id")]].", ".$row[csf("fabric_composition")]."');\n";
		}
		else
		{
			$fab_descrip=$lib_body_part_arr[chop($row[csf('body_part_id')],",")]." ".$row[csf('fab_type')]." ".$row[csf('fabric_construction')]." ".$row[csf('fab_design')]." ".$row[csf('fabric_composition')];
			$fab_description=chop($fab_descrip,", ");
			echo "$('#txtitemdescription_1').val('$fab_description');\n";			
		}		
		
		echo "$('#cbouom_1').val(".$row[csf("uom")].");\n";
		echo "$('#txtquantity_1').val(".$row[csf("quantity")].");\n";
		echo "$('#txtrate_1').val(".$row[csf("rate")].");\n";
		echo "$('#txtamount_1').val(".$row[csf("amount")].");\n";
		echo "$('#cboitemcategory_1').val(".$row[csf("item_category_id")].");\n";
		echo "$('#txtitemdescription_1').attr('disabled',true);\n";	
		//echo "$('#fabriccolorid_1').val(".$row[csf("fabric_color_id")].");\n";

		if($row[csf("fabric_color_id")] !="")
		{
			echo "$('#fabriccolorid_1').val(".$row[csf("fabric_color_id")].");\n";	
		}
		else
		{
			echo "$('#fabriccolorid_1').val('');\n";	
		}		
			
		if($row[csf("item_category_id")]!=0)
		{
			echo " gate_enable_disable(".$row[csf("item_category_id")].");\n";		
		}
	}
	exit();
}

?>