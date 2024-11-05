<?
include('../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');

$trans_Type="2";

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();		 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_party_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 );
	
	exit();	 
}
if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "" );
	exit();
} 

if($action=="load_drop_down_company_supplier")
{
	$data = explode("**",$data);
	if($data[0]==3)
	{
		//echo create_drop_down( "cbo_company_supplier", 140, "select id, supplier_name from lib_supplier where find_in_set(2,party_type) and find_in_set($data[1],tag_company) and status_active=1 and is_deleted=0","id,supplier_name", 1, "--Select Supplier--", 1, "" );
		echo create_drop_down( "cbo_company_supplier", 140, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=2 and  a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );
	}
	else if($data[0]==1)
	{
		if($data[1]!="")
		{
			 echo create_drop_down( "cbo_company_supplier", 140,"select id,company_name from lib_company where is_deleted=0 and status_active=1 order by company_name","id,company_name", 1, "--Select Supplier--", $data[1], "",1 );	
		}
		else
		{
			 echo create_drop_down( "cbo_company_supplier", 140, $blank_array,"", 1, "--Select Company--", $selected, "",0 );
		}
	}
	else
	{
		echo create_drop_down( "cbo_company_supplier", 140, $blank_array,"", 1, "--Select Supplier--", $selected, "",0 );
	}
	exit();	
}

if($action=="load_drop_down_issueto")
{
	echo create_drop_down( "cbo_company_supplier", 140, "select id, company_name from lib_company where is_deleted=0 and status_active=1 order by company_name","id,company_name", 1, "-- Select Comp/Supp --", $data, "",1);
	exit();
}

if ($action=="issue_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_issue').value=id;
			parent.emailwindow.hide();
		}		
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="issueidsearch_1"  id="issueidsearch_1" autocomplete="off">
                <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="140">Company Name</th>
                        <th width="70">Source</th>
                        <th width="140">Supplier Name</th>
                        <th width="80">Issue ID</th>
                        <th width="100">Search Job</th>
                        <th width="60">Year</th>
                        <th width="170">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('issueidsearch_1','search_div','','','','');" /></th>           
                    </thead>
                    <tbody>
                        <tr>
                            <td> <input type="hidden" id="selected_issue"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
								<? 
									echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "");//load_drop_down( 'sub_contract_material_issue_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td' ); 
								?>
                            </td>
                            <td>
                            	<?
								   echo create_drop_down( "cbo_source", 70, $knitting_source,"", 1, "-- Select Source --", 0, "load_drop_down( 'sub_contract_material_issue_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_company_supplier', 'issue_to_td' );",0,'1,3' );
							   ?>
                            </td>
                            <td id="issue_to_td">
								<? 
									echo create_drop_down( "cbo_company_supplier", 140, $blank_array,"", 1, "--Select Supplier--", $data[2], "" ); 
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td >
                             <input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Job" />
                        	</td>
                            <td> 
                                <?
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year", 60, $year,"", 1, "-Year-", $selected_year, "",0 );
                                ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_company_supplier').value+'_'+document.getElementById('cbo_source').value+'_'+document.getElementById('txt_search_job').value, 'create_issue_search_list_view', 'search_div', 'sub_contract_material_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="7" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" align="center" valign="top" id=""><div id="search_div"></div> </td>
                        </tr>
                    </tbody>
                </table>  
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_issue_search_list_view")
{
	$data=explode('_',$data);
	//echo $data[2];
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!="" &&  $data[2]!="") $issue_date_cond = "and a.subcon_date between '".change_date_format($data[1],"", "",1)."' and '".change_date_format($data[2],"", "",1)."'"; else $issue_date_cond ="";
	if ($data[3]!='') $issue_id_cond=" and a.prefix_no_num= '$data[3]'"; else $issue_id_cond="";
	if ($data[5]!=0) $party_cond=" and a.party_id= '$data[5]'"; else $party_cond="";
	if ($data[6]!=0) $source_cond=" and a.prod_source= '$data[6]'"; else $source_cond="";
	if ($data[7]!='') $search_job_cond=" and c.job_no_prefix_num= '$data[7]'"; else $search_job_cond="";

	//if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '%$search_job'";  
	//$trans_Type="issue";
/*
	if($db_type==0)
	{
		$sql= "select a.id, a.sys_no, a.prefix_no_num, YEAR(a.insert_date) as year, a.location_id, a.prod_source, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active, group_concat(b.order_id) as order_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.company_id=$data[0] and a.trans_type=2 and a.entry_form=343 and a.status_active=1 $issue_id_cond $issue_date_cond $party_cond  $source_cond group by  a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.prod_source, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active order by a.id DESC";
	}
	else if($db_type==2)
	{
		$sql= "select a.id, a.sys_no, a.prefix_no_num, TO_CHAR(a.insert_date,'YYYY') as year, a.location_id, a.prod_source, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active, listagg(b.order_id,',') within group (order by b.order_id) as order_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.company_id=$data[0] and a.trans_type=2 and a.entry_form=343 and a.status_active=1 $issue_id_cond $issue_date_cond $party_cond  $source_cond group by  a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.prod_source, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active order by a.id DESC";
	}
*/

	if($db_type==0)
	{
		$sql= "select a.id, a.sys_no, a.prefix_no_num, c.job_no_prefix_num, YEAR(a.insert_date) as year, a.location_id, a.prod_source, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active, group_concat(b.order_id) as order_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b, subcon_ord_mst c, subcon_ord_dtls d where a.id=b.mst_id and c.id=d.mst_id and b.order_id=d.id and a.company_id=$data[0] and a.trans_type=2 and a.entry_form=343 and a.status_active=1 $issue_id_cond $issue_date_cond $party_cond $search_job_cond  $source_cond group by  a.id, a.sys_no, a.prefix_no_num, c.job_no_prefix_num, a.insert_date, a.location_id, a.prod_source, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active order by a.id DESC";
	}
	else if($db_type==2)
	{
		$sql= "select a.id, a.sys_no, a.prefix_no_num, c.job_no_prefix_num, TO_CHAR(a.insert_date,'YYYY') as year, a.location_id, a.prod_source, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active, listagg(b.order_id,',') within group (order by b.order_id) as order_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b, subcon_ord_mst c, subcon_ord_dtls d where a.id=b.mst_id and c.id=d.mst_id and b.order_id=d.id and a.company_id=$data[0] and a.trans_type=2 and a.entry_form=343 and a.status_active=1 $issue_id_cond $issue_date_cond $party_cond $search_job_cond $source_cond group by  a.id, a.sys_no, a.prefix_no_num, c.job_no_prefix_num, a.insert_date, a.location_id, a.prod_source, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.status_active order by a.id DESC";
	}

	//echo $sql;
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_party_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$po_array=array();
	$po_sql="select id, order_no, cust_style_ref from  subcon_ord_dtls where status_active=1 and is_deleted=0";
	$result_po = sql_select($po_sql);
	foreach($result_po as $row)
	{
		$po_array[$row[csf("id")]]['po']=$row[csf("order_no")];
		$po_array[$row[csf("id")]]['style']=$row[csf("cust_style_ref")];
	}
	?> 
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>   
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
            <thead>
                <th width="30" >SL</th>
                <th width="60" >Issue ID</th>
                <th width="60" >Year</th>
                <th width="100" >Prod Source</th>
                <th width="120" >Issue To</th>
                <th width="65" >Issue Date</th>
                <th width="70" >Issue Challan</th>
                <th width="70" >Job No</th>
                <th width="110">Style</th>
                <th width="100">Order</th>
                <th>Issue Qty</th>
            </thead>
     	</table>
     </div>
     <div style="width:900px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$order_no=''; $style_name="";
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_array[$val]['po']; else $order_no.=", ".$po_array[$val]['po'];
					if($style_name=="") $style_name=$po_array[$val]['style']; else $style_name.=", ".$po_array[$val]['style'];
				}
				
				if($row[csf("prod_source")]==1) $prod_company=$company_arr[$row[csf("party_id")]]; else $prod_company=$company_party_arr[$row[csf("party_id")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>);" > 
						<td width="30" align="center"><?php echo $i; ?></td>
						<td width="60" align="center"><?php echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="60" align="center"><?php echo $row[csf("year")]; ?></td>		
						<td width="100"><?php echo $knitting_source[$row[csf("prod_source")]];  ?></td>	
						<td width="120"><?php echo $prod_company; ?></td>
						<td width="65"><?php echo change_date_format($row[csf("subcon_date")]);?> </td>	
						<td width="70" align="center"><?php echo $row[csf("chalan_no")]; ?></td>
						<td width="70" align="center"><?php echo $row[csf("job_no_prefix_num")]; ?></td>
                        <td width="110"><p><?php echo $style_name; ?></p></td>
                        <td width="100"><p><?php echo $order_no; ?><p></td>
                        <td align="center"><p><?php echo $row[csf("quantity")]; ?><p></td>
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

if ($action=="load_php_data_to_form")
{
	//echo "select id,sys_no,company_id,location_id,prod_source,party_id,subcon_date,chalan_no,remarks,status_active from sub_material_mst where id='$data'";die;
	$nameArray=sql_select( "select id,sys_no,company_id,location_id,prod_source,party_id,subcon_date,chalan_no,remarks,status_active from sub_material_mst where id='$data'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_issue_no').value 		= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		//load_drop_down( 'requires/sub_contract_material_issue_controller', this.value, 'load_drop_down_company_supplier', 'issue_to_td' );		 
		echo "load_drop_down( 'requires/sub_contract_material_issue_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		echo "load_drop_down( 'requires/sub_contract_material_issue_controller', $('#cbo_company_name').val(), 'load_drop_down_issueto', 'issue_to_td' );";
		echo "document.getElementById('cbo_location_name').value	= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_source').value			= '".$row[csf("prod_source")]."';\n"; 
		//echo "load_drop_down( 'requires/sub_contract_material_issue_controller', document.getElementById('cbo_source').value, 'load_drop_down_company_supplier','issue_to_td');\n";
		echo "document.getElementById('cbo_company_supplier').value	= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('txt_issue_date').value 		= '".change_date_format($row[csf("subcon_date")])."';\n";   
		echo "document.getElementById('txt_issue_challan').value	= '".$row[csf("chalan_no")]."';\n"; 
		echo "document.getElementById('txt_remarks').value			= '".$row[csf("remarks")]."';\n"; 
	    echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."','fnc_material_issue',1,1);\n";
	}
	exit();	
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_order').value=id;
			parent.emailwindow.hide();
 		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        	<thead>
                <tr>
                    <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140">Company Name</th>
                    <th width="140">Party Name</th>
                    <th width="170">Date Range</th>
                    <th width="100">Search Job</th>
                    <th width="100">Search Order</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr> 
            </thead>
                 <tbody>
                    <tr>
                        <td> <input type="hidden" id="selected_order">  
                            <?   
                                $data=explode("_",$data);
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[1],"",1 );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[1]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[3], "",'' );   	 
                            ?>
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                        </td> 
                        <td >
                             <input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Job" />
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Order" />
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_order_search_list_view', 'search_div', 'sub_contract_material_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" height="40" valign="middle">
                            <? echo load_month_buttons(1);  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text" style="width:70px">
                        </td>
                    </tr>
                </tbody>            
            </table>
            <div id="search_div"></div>    
        </form>
       </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

/* ===========   Order PopUP End Here ====================== */
if($action=="create_order_search_list_view")
{	
$data=explode('_',$data);
	$search_job=str_replace("'","",$data[4]);
	$search_order=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else  $company="";
	if ($data[1]!=0) $buyer=" and party_id='$data[1]'"; else $buyer="";
	
	if($search_type==1)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num='$search_job'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no='$search_order'";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '%$search_job%'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '%$search_order%'";
	}
	else if($search_type==2)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '$search_job%'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '$search_order%'";
	}
	else if($search_type==3)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '%$search_job'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '%$search_order'";
	}	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($db_type==0)
	{
		//$sql= "select a.id, b.id as ord_id, a.subcon_job, a.job_no_prefix_num, year(a.insert_date)as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.order_no, b.order_rcv_date, b.delivery_date, b.status_active from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond order by a.id DESC";

		$color_id_str="group_concat(c.color_id)";
		$sql= "select a.id, b.id as ord_id, a.subcon_job, a.job_no_prefix_num, year(a.insert_date)as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.order_no, b.order_rcv_date, b.delivery_date, b.status_active, $color_id_str as color_id,b.process_id from subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c where  a.entry_form=238 and a.subcon_job=b.job_no_mst and  b.id=c.order_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond group by a.id, b.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.order_no, b.order_rcv_date, b.delivery_date, b.status_active,b.process_id  order by a.id DESC";
	}
	else if($db_type==2)
	{
		//$sql= "select a.id, b.id as ord_id, a.subcon_job, a.job_no_prefix_num, TO_CHAR(a.insert_date,'YYYY') as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.order_no, b.order_rcv_date, b.delivery_date, b.status_active from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond order by a.id DESC";

		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		$sql= "select a.id, b.id as ord_id, a.subcon_job, a.job_no_prefix_num, TO_CHAR(a.insert_date,'YYYY') as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.order_no, b.order_rcv_date, b.delivery_date, b.status_active, $color_id_str as color_id,b.main_process_id   from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.entry_form=238 and a.subcon_job=b.job_no_mst and b.id=c.order_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond group by a.id, b.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.order_no, b.order_rcv_date, b.delivery_date, b.status_active,b.main_process_id order by a.id DESC";
	}
	//echo $sql;//die;
	$color_name_arr=array();
	$data_array=sql_select($sql);
	foreach($data_array as $row)
    {  
		$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
		$color_name="";	
		foreach ($excolor_id as $color_id)
		{
			if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
		}
		$color_name_arr[$row[csf('ord_id')]]=$color_name;
	}

	$arr=array (3=>$production_process,4=>$comp,7=>$color_name_arr);
	echo  create_list_view("list_view", "Job No,Year,Order No,process,Company,Ord Receive Date,Delivery Date, Color","70,40,100,100,150,100,100","850","250",0,$sql, "js_set_value","ord_id","",1,"0,0,0,main_process_id,company_id,0,0,ord_id",$arr,"job_no_prefix_num,year,order_no,main_process_id,company_id,order_rcv_date,delivery_date,ord_id", "",'','0,0,0,0,0,3,3,0') ;
	exit();	
}

if($action=="load_php_data_to_form_order")
{
	$nameArray=sql_select( "select id,order_no from subcon_ord_dtls where id='$data'" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txtorderno_1').value	= '".$row[csf("order_no")]."';\n"; 
		echo "document.getElementById('order_no_id').value	= '".$row[csf("id")]."';\n"; 
	}
	exit();
}
// check recv quantity
if($action=="check_recvQnty")
{
	$nameArray=sql_select( "select id, quantity from sub_material_dtls where status_active=2 and order_id='$data'" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_hidden_checkRecvQty').value	= '".$row[csf("quantity")]."';\n"; 
	}
	exit();
}
/*if($action=="load_php_data_ord_rev_qty")
{
	$nameArray=sql_select( "select order_id,sum(quantity) as rev_qty from sub_material_dtls where order_id='$data' group by  order_id" );
	
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_hidden_ord_rev_qty').value	= '".$row[csf("rev_qty")]."';\n"; 
	}
	exit();
}*/
if ($action=="material_description_popup")
{
	echo load_html_head_contents("Material Description Form", "../../", 1, 1,$unicode,1,1);
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$order_array=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$dia_width_type=return_library_array( "select id, width_dia_id from lib_subcon_charge",'id','width_dia_id');		

	?>
    <script>
	  function js_set_value(id,val,dia,rec_challan,fin_dia,color,gsm,color_name,item_category_id,recvQty,size,size_name,issue_balnce,usedYarnDtls,uom,lot_no,brand)
	  {
		  //alert (val)
	  	  $("#description_id").val(id);
		  $("#material_description").val(val);
		  $("#dia").val(dia);
		  $("#rec_challan").val(rec_challan);
		  $("#fin_dia").val(fin_dia);
		  $("#color").val(color);
		  $("#color_name").val(color_name);
		  $("#size").val(size);
		  $("#size_name").val(size_name);
		  $("#gsm").val(gsm);
		  $("#item_category_id").val(item_category_id);
		  $("#recvQty").val(recvQty);
		  $("#issue_balnce").val(issue_balnce);
		  $("#usedYarnDtls").val(usedYarnDtls);
		  $("#uom").val(uom);
		  $("#lot_no").val(lot_no);
		  $("#brand").val(brand);
		  parent.emailwindow.hide();
	  }
	</script>
    </head>
    <body>
            <input type="hidden" name="description_id" id="description_id">
        	<input type="hidden" name="material_description" id="material_description">
            <input type="hidden" name="dia" id="dia">
            <input type="hidden" name="rec_challan" id="rec_challan">
            <input type="hidden" name="fin_dia" id="fin_dia">
            <input type="hidden" name="color" id="color">
            <input type="hidden" name="color_name" id="color_name">
            <input type="hidden" name="size" id="size">
            <input type="hidden" name="size_name" id="size_name">
            <input type="hidden" name="gsm" id="gsm">
            <input type="hidden" name="item_category_id" id="item_category_id">
            <input type="hidden" name="recvQty" id="recvQty">
            <input type="hidden" name="issue_balnce" id="issue_balnce">
            <input type="hidden" name="usedYarnDtls" id="usedYarnDtls">
            <input type="hidden" name="uom" id="uom">
			<input type="hidden" name="lot_no" id="lot_no">
			<input type="hidden" name="brand" id="brand">

    
     <div style="width:1110px; min-height:200px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" style="width: 1090px;" class="rpt_table" >
        	<thead>
            	<tr>
                	<th colspan="16" width="1080" align="center">RECEIVED ITEM, Order No :<? echo $order_array[$ex_data[2]]; ?></th>
                </tr>
                <tr>
                    <th width="30">SL</th>
					<th width="80">Lot</th>
					<th width="80">Brand</th>
                    <th width="120">Material Description</th>
                    <th width="100">Used Yarn</th>
                    <th width="60">Color</th>
                    <th width="40">UOM</th>
                    <th width="60">Gray Dia</th>
                    <th width="60">Finish Dia</th>
                    <th width="60">(Rec.+Issue Ret) Qty</th>
                    <th width="60">Roll/Bag</th>
                    <th width="80">Width/Dia Type</th>
                    <th width="60">Cone</th>
                    <th width="60">Iss. Qty</th>
                    <th width="60">Issue Balance</th>
                    <th width="60">Batch Balance</th>
                </tr>
            </thead>
            <tbody id="tbl_po_list">
            	
            
			<?
			if($data!=0){ $category_id=" and a.item_category_id=$ex_data[0]";}else{echo "Please Select item category First."; die; }
			if($db_type==0)
			{
				$id="group_concat(a.id) as id";
				$challn="group_concat(b.chalan_no) as chalan_no";
				//$used_yarn_details="group_concat(a.used_yarn_details) as used_yarn_details";
			}
			else if($db_type==2)
			{
				$id="listagg(a.id,',') within group (order by a.id) as id";
				$challn="listagg(cast(b.chalan_no as varchar2(4000)),',') within group (order by b.chalan_no) as chalan_no";
				//$used_yarn_details="listagg(cast(a.used_yarn_details as varchar2(4000)),',') within group (order by a.used_yarn_details) as used_yarn_details";
			}
			
			 $sql_order_item_arr=sql_select(" select order_id,item_id from subcon_ord_breakdown");
			 //$sql_recv_arr=sql_select(" select b.order_id,b.item_id,b.stitch_length ,b.used_yarn_details from sub_material_dtls b,sub_material_mst a where a.id=b.mst_id and b.status_active=2 and  a.is_deleted=0 and a.trans_type=1");
			  foreach($sql_order_item_arr as $row)
              {
				  $order_data_arr[$row[csf('order_id')]]['item_id']=$row[csf('item_id')];
			  }
			
			$issue_balance_array=array();
			$sql_issue="select $id,b.entry_form, a.material_description, a.used_yarn_details, a.fin_dia, a.color_id, a.size_id, a.gsm, a.subcon_uom, sum(a.subcon_roll) as subcon_roll, sum(a.quantity) as quantity, sum(a.rec_cone) as rec_cone, a.grey_dia,a.item_category_id,a.order_id,a.lot_no from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and a.material_description<>' ' and b.trans_type  in (2,3) and b.entry_form in (343,344) and b.status_active=1 and b.is_deleted=0 and a.order_id='$ex_data[2]' $category_id group by b.entry_form,a.material_description, a.used_yarn_details, a.fin_dia, a.color_id, a.gsm, a.subcon_uom, a.grey_dia,a.item_category_id,a.order_id,a.size_id, a.lot_no";
			$sql_issue_result=sql_select($sql_issue);
			foreach( $sql_issue_result as $row )
			{
				$entry_formId=$row[csf("entry_form")];
				if($entry_formId==343)
				{
				$issue_balance_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]][$row[csf("lot_no")]]=$row[csf("quantity")];
				}
				else
				{
				$issue_return_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]][$row[csf("lot_no")]]=$row[csf("quantity")];
				}
			}

			$rcv_return_array=array();
			$sql_rcv_return="select a.id as return_dtls_id, b.id, b.mst_id, b.item_category_id, b.material_description, b.color_id, b.size_id, b.gsm, b.stitch_length, b.grey_dia, b.mc_dia, b.mc_gauge, b.fin_dia, b.dia_uom, b.rate, b.uom, b.subcon_roll, b.rec_cone, b.order_id, b.buyer_po_id, b.job_id, b.job_dtls_id, b.job_break_id, b.fabric_details_id, a.quantity, b.subcon_uom from sub_material_return_dtls a, sub_material_dtls b	where  a.is_deleted=0 and b.is_deleted=0 and a.receive_dtls_id = b.id";
			$sql_rcv_return_result=sql_select($sql_rcv_return);
			foreach( $sql_rcv_return_result as $row )
			{
				$rcv_return_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]]=$row[csf("quantity")];
			}
			// echo "<pre>";
			// print_r($rcv_return_array);

			$batch_balance_array=array();
			 $sql_batch="select $id, sum(a.batch_qnty) as quantity ,a.po_id, a.item_description from pro_batch_create_dtls a, pro_batch_create_mst b where b.id=a.mst_id and a.item_description<>' ' and b.status_active=1 and b.is_deleted=0 and a.po_id='$ex_data[2]' group by a.item_description, a.po_id";
			$sql_batch_result=sql_select($sql_batch);
			foreach( $sql_batch_result as $row )
			{
				$batch_balance_array[$row[csf("po_id")]][$row[csf("item_description")]]=$row[csf("quantity")];
			}

			//var_dump($issue_balance_array);
			$sql="select $id, $challn, a.material_description, a.fin_dia, a.color_id, a.used_yarn_details , a.size_id, a.gsm, a.subcon_uom, sum(a.subcon_roll) as subcon_roll, sum(a.quantity) as quantity, sum(a.rec_cone) as rec_cone, a.grey_dia,a.item_category_id,a.order_id,a.lot_no,a.brand from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and a.material_description<>' ' and b.trans_type=1 and b.entry_form=288 and b.status_active=1 and b.is_deleted=0 and a.order_id='$ex_data[2]' $category_id group by a.material_description, a.fin_dia, a.color_id, a.used_yarn_details, a.gsm, a.subcon_uom, a.grey_dia,a.item_category_id,a.order_id,a.size_id,a.lot_no,a.brand";
			
			$i=1;
			$nameArray=sql_select($sql);
            foreach( $nameArray as $row )
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                		//echo "**".$row[csf("quantity")]."==".$issue_balance_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]];
						$issueRet=$issue_return_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]][$row[csf("lot_no")]];
						
						$rcv_return_qnty=$rcv_return_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]];
                		$issue_balnce=($row[csf("quantity")]+$issueRet)-($issue_balance_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]][$row[csf("lot_no")]]+$rcv_return_qnty);
						$issueBal=$issue_balance_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]][$row[csf("lot_no")]];
						//echo $rcv_return_qnty.'D';
						$issue_return_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]][$row[csf("lot_no")]];
						

				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")]; ?>','<? echo $row[csf("material_description")]; ?>','<? echo $row[csf("grey_dia")]; ?>','<? echo $row[csf("chalan_no")]; ?>','<? echo $row[csf("fin_dia")]; ?>','<? echo $row[csf("color_id")]; ?>','<? echo $row[csf("gsm")]; ?>','<? echo $color_arr[$row[csf("color_id")]]; ?>','<? echo $row[csf("item_category_id")]; ?>','<? echo $row[csf("quantity")]; ?>','<? echo $row[csf("size_id")]; ?>','<? echo $size_arr[$row[csf("size_id")]]; ?>','<? echo $issue_balnce; ?>','<? echo $row[csf("used_yarn_details")]; ?>','<? echo $row[csf("subcon_uom")]; ?>','<? echo $row[csf("lot_no")]; ?>','<? echo $row[csf("brand")]; ?>');" > 
						<td  align="center"><? echo $i; ?></td>

						<td  align="center"><? echo $row[csf("lot_no")]; ?></td>
						<td  align="center"><? echo $row[csf("brand")]; ?></td>
						<td ><? echo $row[csf("material_description")].", ".$row[csf("gsm")]; ?></td>
						<td  align="center"><? echo $row[csf("used_yarn_details")]; ?></td>
                        <td ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
						<td  align="center"><? echo $unit_of_measurement[$row[csf("subcon_uom")]]; ?></td>
                        <td  align="center"><? echo $row[csf("grey_dia")]; ?></td>
                        <td  align="center"><? echo $row[csf("fin_dia")]; ?></td>
                        <td  align="center"><? echo $row[csf("quantity")]+$issueRet; ?></td>	
                        <td  align="center"><? echo $row[csf("subcon_roll")]; ?></td>	
                        <td  align="center"><? echo $fabric_typee[$dia_width_type[$order_data_arr[$row[csf('order_id')]]['item_id']]]; ?></td>	
                        <td  align="center"><? echo $row[csf("rec_cone")]; ?></td>
                        <td  align="center"><? echo $issue_balance_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]][$row[csf("lot_no")]]; ?></td>	
                        <td  align="center" title="RecvWithIssueRet: <?=$row[csf("quantity")]+$issueRet.'-Issue: '.$issueBal.'-RecRet: '.$rcv_return_qnty;?>"><? echo $issue_balnce; ?></td>

                        <td align="center"  title="RecvWithIssueRet:<?=$row[csf("quantity")]+$issueRet.'-Batch Qty :'.$batch_balance_array[$row[csf("order_id")]][$row[csf("material_description")]];?>">
                        	 <? echo ($row[csf("quantity")]+$issueRet)-$batch_balance_array[$row[csf("order_id")]][$row[csf("material_description")]]; ?>
                        </td>	
					</tr>
				<? 
				$i++;
            }
   		?>
   			</tbody>
			</table>
		</div>
        <br>
    <div style="width:950px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
            	<tr>
                	<th colspan="11" align="center">ALL ITEM</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="100">Rec. Challan</th>
                    <th width="200">Material Description</th>
                    <th width="100">Used Yarn</th>
                    <th width="60">Color</th>
                    <th width="60">UOM</th>
                    <th width="60">Dia</th>
                    <th width="60">Rec. Qty</th>
                    <th width="60">Roll/Bag</th>
                    <th width="100">Width/Dia Type</th>
                    <th>Cone</th>
                </tr>
            </thead>
     	</table>
     </div>
        <div style="width:950px; max-height:200px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" >
			<?
			if($ex_data[0]!=0){ $category_id=" and a.item_category_id=$ex_data[0]";}else{echo "Please Select item category First."; die; }
			if($db_type==0)
			{
				$sql="select $id, b.chalan_no, a.material_description, a.fin_dia, a.color_id, a.used_yarn_details, a.size_id, a.gsm, a.subcon_uom, sum(a.subcon_roll) as subcon_roll, sum(a.quantity) as quantity, sum(a.rec_cone) as rec_cone, a.grey_dia,a.item_category_id,a.order_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and a.id not in (select a.id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.entry_form=288 and b.trans_type=1 and a.material_description<>' ' and b.status_active=1 and b.is_deleted=0 and a.order_id='$ex_data[2]' $category_id ) $category_id group by b.chalan_no, a.material_description, a.fin_dia, a.color_id, a.gsm, a.subcon_uom, a.grey_dia,a.item_category_id,a.order_id,a.size_id,a.used_yarn_details ";			
			}
			else if($db_type==2)
			{
				$sql="select $id, b.chalan_no, a.material_description, a.fin_dia, a.color_id, a.used_yarn_details, a.size_id, a.gsm, a.subcon_uom, sum(a.subcon_roll) as subcon_roll, sum(a.quantity) as quantity, sum(a.rec_cone) as rec_cone, a.grey_dia,a.item_category_id,a.order_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and a.id not in (select a.id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.entry_form=288 and b.trans_type=1 and a.material_description<>' ' and b.status_active=1 and b.is_deleted=0 and a.order_id='$ex_data[2]' $category_id ) $category_id group by b.chalan_no, a.material_description, a.fin_dia, a.color_id, a.gsm, a.subcon_uom, a.grey_dia,a.item_category_id,a.order_id,a.size_id,a.used_yarn_details";//
			}
			$i=1;
			$nameArray=sql_select($sql);
            foreach( $nameArray as $row )
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")]; ?>','<? echo $row[csf("material_description")]; ?>','<? echo $row[csf("grey_dia")]; ?>','<? echo $row[csf("chalan_no")]; ?>','<? echo $row[csf("fin_dia")]; ?>','<? echo $row[csf("color_id")]; ?>','<? echo $row[csf("gsm")]; ?>','<? echo $color_arr[$row[csf("color_id")]]; ?>','<? echo $row[csf("item_category_id")]; ?>','<? echo $row[csf("quantity")]; ?>','<? echo $row[csf("size_id")]; ?>','<? echo $size_arr[$row[csf("size_id")]]; ?>','','<? echo $row[csf("used_yarn_details")]; ?>','<? echo $row[csf("subcon_uom")]; ?>');" > 
						<td width="30" align="center"><? echo $i; ?></td>
                        <td width="100"><? echo $row[csf("chalan_no")]; ?></td>
						<td width="200"><? echo $row[csf("material_description")]; ?></td>
						<td width="100" align="center"><? echo $row[csf("used_yarn_details")]; ?></td>
                        <td width="60"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>		
						<td width="60" align="center"><? echo $unit_of_measurement[$row[csf("subcon_uom")]]; ?></td>
                        <td width="60" align="center"><? echo $row[csf("grey_dia")]; ?></td>
                        <td width="60" align="right"><? echo $row[csf("quantity")]; ?></td>	
                        <td width="60" align="center"><? echo $row[csf("subcon_roll")]; ?></td>	
                        <td width="100" align="center"><? echo $fabric_typee[$dia_width_type[$order_data_arr[$row[csf('order_id')]]['item_id']]]; ?></td>	
                        <td align="center"><? echo $row[csf("rec_cone")]; ?></td>	
					</tr>
				<? 
				$i++;
            }
   		?>
			</table>
		</div>         
    </div>
    </body>           
    
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>   
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
  exit();
}

if($action=="category_description_list_view")
{	
	$sql = "select a.id, a.mst_id, a.item_category_id, a.material_description, a.quantity, a.subcon_uom, a.subcon_roll, a.grey_dia, a.status_active, b.order_no,a.lot_no,a.brand from sub_material_dtls a,subcon_ord_dtls b where a.order_id=b.id and a.status_active=1 and a.mst_id='$data'"; 
		
	$arr=array(1=>$item_category,6=>$unit_of_measurement,7=>$row_status);
	echo  create_list_view("list_view", "Order No,Item Catg,Lot,Brand,Material Des.,Issue Qty,UOM,Roll,Dia,Status", "80,100,80,80,200,80,60,60,60,80","960","250",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form_dtls'", 1, "0,item_category_id,0,0,0,0,subcon_uom,0,0,status_active",$arr,"order_no,item_category_id,lot_no,brand,material_description,quantity,subcon_uom,subcon_roll,grey_dia,status_active", "requires/sub_contract_material_issue_controller","","0,00,0,,0,0,0,0,0");
	exit();
}

if ($action=="load_php_data_to_form_dtls")
{
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');

	$nameArray=sql_select( "select a.id, a.mst_id, a.item_category_id, a.rec_challan, a.used_yarn_details, a.material_description, a.quantity, a.subcon_uom, a.subcon_roll, a.rec_cone, a.grey_dia, a.gsm, a.fin_dia, a.color_id, a.size_id,a.lot_no,a.brand, a.status_active, b.id as order_tbl_id, b.order_no from sub_material_dtls a,subcon_ord_dtls b where a.order_id=b.id and a.id='$data'" );		  
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txtorderno_1').value 				= '".$row[csf("order_no")]."';\n";  
		echo "document.getElementById('cboitemcategory_1').value 			= '".$row[csf("item_category_id")]."';\n";
		echo "document.getElementById('txt_rec_challan_no').value 			= '".$row[csf("rec_challan")]."';\n";   		  
		echo "document.getElementById('materialdescription_1').value 		= '".$row[csf("material_description")]."';\n";
		echo "document.getElementById('hidden_materialdescription').value 		= '".$row[csf("material_description")]."';\n";
		echo "document.getElementById('txtissuequantity_1').value			= '".$row[csf("quantity")]."';\n";
		echo "document.getElementById('cbouom_1').value						= '".$row[csf("subcon_uom")]."';\n";
		echo "document.getElementById('txt_roll').value						= '".$row[csf("subcon_roll")]."';\n";
		echo "document.getElementById('txt_cone').value						= '".$row[csf("rec_cone")]."';\n";  
		echo "document.getElementById('txt_dia').value						= '".$row[csf("grey_dia")]."';\n"; 
		echo "document.getElementById('txt_gsm').value						= '".$row[csf("gsm")]."';\n"; 
		echo "document.getElementById('txt_fin_dia').value					= '".$row[csf("fin_dia")]."';\n";
		echo "document.getElementById('txt_fin_dia_show').value				= '".$row[csf("fin_dia")]."';\n";
		echo "document.getElementById('txt_color_id').value					= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_color_show').value				= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_size_id').value					= '".$row[csf("size_id")]."';\n";
		echo "document.getElementById('txt_size_show').value				= '".$size_arr[$row[csf("size_id")]]."';\n";
		echo "document.getElementById('order_no_id').value					= '".$row[csf("order_tbl_id")]."';\n"; 
		echo "document.getElementById('update_id_dtl').value				= '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('txt_brand').value					= '".$row[csf("brand")]."';\n"; 
		echo "document.getElementById('txt_lot_no').value					= '".$row[csf("lot_no")]."';\n"; 

		echo "document.getElementById('txt_used_yarn_details').value		= '".$row[csf("used_yarn_details")]."';\n"; 

		//echo "change_uom('".$row[csf("item_category_id")]."');\n";		
		echo "document.getElementById('txt_hidden_ord_rev_qty').value				= '".$row[csf("quantity")]."';\n";		
		echo "set_button_status( 1,'".$_SESSION['page_permission']."', 'fnc_material_issue',1);\n";	
	}	
	exit();
}

if ($action=="load_php_data_for_dtls")
{
	$ex_data=explode('_',$data);
	$sql_rec="Select a.id, a.quantity, a.subcon_roll, a.rec_cone, a.grey_dia from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and a.order_id=$ex_data[0] and a.id=$ex_data[1] and a.status_active=1 and and b.entry_form=288 b.trans_type=1 and a.is_deleted=0";
	$sql_result_rec = sql_select($sql_rec);
	$sql_iss="Select sum(a.quantity) as quantity, sum (a.subcon_roll) as subcon_roll, sum (a.rec_cone) as rec_cone from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and a.order_id=$ex_data[0] and b.trans_type=2 and b.entry_form=343 and a.status_active=1 and a.is_deleted=0";
	$sql_result_iss = sql_select($sql_iss);
	$tot_roll=0;
	$tot_dia=$sql_result_rec[0][csf('grey_dia')]*1;//-($sql_result_iss[0][csf('grey_dia')]*1)
	$tot_qty=$sql_result_rec[0][csf('quantity')]*1-($sql_result_iss[0][csf('quantity')]*1);
	$tot_roll=$sql_result_rec[0][csf('subcon_roll')]*1-($sql_result_iss[0][csf('subcon_roll')]*1);
	$tot_cone=$sql_result_rec[0][csf('rec_cone')]*1-($sql_result_iss[0][csf('rec_cone')]*1);
	echo "$('#txt_dia').val('".$tot_dia."');\n";
	echo "$('#txtissuequantity_1').attr('placeholder','".$tot_qty."');\n";
	echo "$('#txt_roll').attr('placeholder','".$tot_roll."');\n";
	echo "$('#txt_cone').attr('placeholder','".$tot_cone."');\n";
	exit();
}

/* =============== Save Update Delete Start Here  ===================*/
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$trans_Type="2";  
	// --------------------------------- Insert Start Here -------------------------
	if ($operation==0)   
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}

		//$order_quantity=return_field_value("order_quantity","subcon_ord_dtls","id=$order_no_id and status_active=1 and is_deleted=0","order_quantity");
		//echo "10**select sum(b.quantity) as recv_qty from sub_material_dtls b, sub_material_mst a where a.id=b.mst_id and b.order_id=$order_no_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.entry_form=288 and a.trans_type=1"; die;
		$item_categoryId=str_replace("'","",$cboitemcategory_1);
		
		 $gsm_cond = ( str_replace("'","", $txt_gsm)!="")?" and b.gsm=$txt_gsm" : "";
		 $lot_cond = ( str_replace("'","", $txt_lot_no)!="")?" and b.lot_no=$txt_lot_no" : "";
		 $material_description_cond = ( str_replace("'","", $hidden_materialdescription)!="")?" and b.material_description=$hidden_materialdescription" : "";
		 $color_cond = ( str_replace("'","", $txt_color_id)!="")?" and b.color_id=$txt_color_id" : "";
		 $size_id_cond = ( str_replace("'","", $txt_size_id)!="")?" and b.size_id=$txt_size_id" : "";
		 $fin_dia_cond = ( str_replace("'","", $txt_fin_dia)!="")?" and b.fin_dia=$txt_fin_dia" : "";
		 if($item_categoryId) $item_cat_cond="and b.item_category_id=$item_categoryId";else  $item_cat_cond="";
		 
		 $material_description_cond_rec_ret = ( str_replace("'","", $hidden_materialdescription)!="")?" and a.material_description=$hidden_materialdescription" : "";
		
		 //  $issue_balance_array=array();
			//echo "10**".$sql_issue="select $id, a.material_description, a.used_yarn_details, a.fin_dia, a.color_id, a.size_id, a.gsm, a.subcon_uom, sum(a.subcon_roll) as subcon_roll, sum(a.quantity) as quantity, sum(a.rec_cone) as rec_cone, a.grey_dia,a.item_category_id,a.order_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and a.material_description<>' ' and b.trans_type  in (2,3) and b.entry_form in (343,344) and b.status_active=1 and b.is_deleted=0 and a.order_id=$order_no_id  $color_cond $fin_dia_cond group by a.material_description, a.used_yarn_details, a.fin_dia, a.color_id, a.gsm, a.subcon_uom, a.grey_dia,a.item_category_id,a.order_id,a.size_id"; die; 
			
			
			//$sql_issue_result=sql_select($sql_issue);
			/*foreach( $sql_issue_result as $row )
			{
				$issue_balance_array[$row[csf("material_description")]][$row[csf("subcon_uom")]][$row[csf("grey_dia")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("fin_dia")]][$row[csf("used_yarn_details")]][$row[csf("gsm")]]=$row[csf("quantity")];
			}
*/
         
 
		
		//$recv_qnty=return_field_value("sum(b.quantity) as recv_qty","sub_material_dtls b, sub_material_mst a"," a.id=b.mst_id and b.order_id=$order_no_id  $gsm_cond  $material_description_cond  $color_cond  $size_id_cond  $item_cat_cond $fin_dia_cond $lot_cond and a.entry_form=288 and a.trans_type=1 and b.status_active=2","recv_qty");
		
		$recv_qty_sql = "select b.id as rec_dtl_id,b.quantity  from sub_material_dtls b, sub_material_mst a	where  a.id=b.mst_id and b.order_id=$order_no_id  $gsm_cond  $material_description_cond  $color_cond  $size_id_cond  $item_cat_cond $fin_dia_cond $lot_cond and a.entry_form=288 and a.trans_type=1 and b.status_active=2";
		//echo "10**=".$recv_qty_sql;die;
		 $recv_qty_result = sql_select($recv_qty_sql); 
		 $recv_qnty=0; $recv_ret_qnty=0;
		foreach ($recv_qty_result as $row) 
		{
			$recv_qnty+=$row[csf('quantity')];
			$recv_Idarr[$row[csf('rec_dtl_id')]]=$row[csf('rec_dtl_id')];
		}
		$recv_returned_sql = "select a.item_category_id, a.receive_dtls_id,a.lot_no, a.material_description, a.quantity,a.order_id from sub_material_return_dtls a	where a.order_id in ($order_no_id) and a.is_deleted = 0 and a.is_deleted = 0 and a.receive_dtls_id in(".implode(',',$recv_Idarr).") $material_description_cond_rec_ret";
		 //echo "10**=".$recv_returned_sql;die;
   		$recv_returned_result = sql_select($recv_returned_sql); 
		foreach($recv_returned_result as $row) 
		{
			$recv_ret_qnty+=$row[csf('quantity')];
		}
	 $recv_qnty_bal=$recv_qnty-$recv_ret_qnty;
		
		$prevIssue_qnty=return_field_value("sum(b.quantity) as prevIssue_qnty","sub_material_dtls b, sub_material_mst a","a.id=b.mst_id and b.order_id=$order_no_id   $gsm_cond  $material_description_cond  $color_cond  $size_id_cond  $fin_dia_cond $item_cat_cond $lot_cond  and a.entry_form=343 and a.trans_type=2 and a.status_active=1  and b.status_active=1","prevIssue_qnty");
		//echo "10**".$recv_qnty."**".$prevIssue_qnty; die;
		//echo "17**Issue Quantity Exceeds Order Quantity=".$txtissuequantity_1.'=Recv='.$recv_qnty.',Recv Ret='.$recv_ret_qnty.',Previ Issue='.$prevIssue_qnty;die;

		if((str_replace("'","",$txtissuequantity_1)+$prevIssue_qnty)>$recv_qnty_bal)//.',Previ Issue='.$prevIssue_qnty
		{
			echo "17**Issue Quantity Exceeds Recv Quantity=".$txtissuequantity_1.'+ Previ Issue='.$prevIssue_qnty.', Recv='.$recv_qnty.' - Recv Ret='.$recv_ret_qnty;
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
		//echo "17**A-Issue Quantity Exceeds Order Quantity=".$txtissuequantity_1.'='.$recv_qnty.'='.$prevIssue_qnty;
			//check_table_status( $_SESSION['menu_id'],0);
			//disconnect($con);
		//	die;
		//echo $update_id;die;
		$new_issue_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'ISU', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_type=2 and entry_form=343 $year_cond=".date('Y',time())." order by id desc", "prefix_no", "prefix_no_num" ));
		if(str_replace("'",'',$update_id)=="")
		{	
			$id=return_next_id( "id", "sub_material_mst",1); 		
			$field_array="id, sys_no, prefix_no, prefix_no_num, trans_type, company_id, location_id, prod_source, party_id, subcon_date, chalan_no, remarks, entry_form , inserted_by, insert_date";
			$data_array="(".$id.",'".$new_issue_no[0]."','".$new_issue_no[1]."','".$new_issue_no[2]."','".$trans_Type."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_source.",".$cbo_company_supplier.",".$txt_issue_date.",".$txt_issue_challan.",".$txt_remarks.",343,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$txt_issue_no=$new_issue_no[0];
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="company_id*location_id*prod_source*party_id*subcon_date*chalan_no*remarks*updated_by*update_date";				
			$data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_source."*".$cbo_company_supplier."*".$txt_issue_date."*".$txt_issue_challan."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 	
			$txt_issue_no=$txt_issue_no; 
		}	
			
		$id1=return_next_id( "id", "sub_material_dtls",1); 
		$field_array2="id, mst_id, order_id, item_category_id, rec_challan, material_description, quantity, subcon_uom, subcon_roll, rec_cone, grey_dia, gsm, fin_dia, color_id, size_id,lot_no,brand, inserted_by, insert_date, used_yarn_details";		
		$data_array2="(".$id1.",".$id.",".$order_no_id.",".$cboitemcategory_1.",".$txt_rec_challan_no.",".$hidden_materialdescription.",".$txtissuequantity_1.",".$cbouom_1.",".$txt_roll.",".$txt_cone.",".$txt_dia.",".$txt_gsm.",".$txt_fin_dia.",".$txt_color_id.",".$txt_size_id.",".$txt_lot_no.",".$txt_brand.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_used_yarn_details.")"; 
		
		if(str_replace("'",'',$update_id)=="")
		{
			//echo "INSERT INTO sub_material_mst (".$field_array.") VALUES ".$data_array; //die;  			
			$rID=sql_insert("sub_material_mst",$field_array,$data_array,0);//die;
		}
		else
		{
			$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0); 
		}
		//echo "10**TT=";die;
		//echo "INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; die;		
		$rID2=sql_insert("sub_material_dtls",$field_array2,$data_array2,1);//	die;	
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_issue_no)."**".str_replace("'", '',$id)."**".str_replace("'",'',$id1);	
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_issue_no)."**".str_replace("'", '',$id)."**".str_replace("'",'',$id1);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'", '',$id)."**".str_replace("'",'',$id1);
			}
		}	
		disconnect($con);
		die;
	}	
	// ================================ Update Here ============================
	else if ($operation==1)   
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}	
		$item_categoryId=str_replace("'","",$cboitemcategory_1);
         $gsm_cond = ( str_replace("'","", $txt_gsm)!="")?" and b.gsm=$txt_gsm" : "";
		 $lot_cond = ( str_replace("'","", $txt_lot_no)!="")?" and b.lot_no=$txt_lot_no" : "";
		 $material_description_cond = ( str_replace("'","", $hidden_materialdescription)!="")?" and b.material_description=$hidden_materialdescription" : "";
		  $material_description_cond_rec_ret = ( str_replace("'","", $hidden_materialdescription)!="")?" and a.material_description=$hidden_materialdescription" : "";
		 $color_cond = ( str_replace("'","", $txt_color_id)!="")?" and b.color_id=$txt_color_id" : "";
		 $size_id_cond = ( str_replace("'","", $txt_size_id)!="")?" and b.size_id=$txt_size_id" : "";
		 $fin_dia_cond = ( str_replace("'","", $txt_fin_dia)!="")?" and b.fin_dia=$txt_fin_dia" : "";
		  if($item_categoryId) $item_cat_cond="and b.item_category_id=$item_categoryId";else  $item_cat_cond="";
	//	$recv_qnty=return_field_value("sum(b.quantity) as recv_qty","sub_material_dtls b, sub_material_mst a"," a.id=b.mst_id and b.order_id=$order_no_id $gsm_cond  $material_description_cond  $color_cond  $size_id_cond  $fin_dia_cond $item_cat_cond  $lot_cond  and a.entry_form=288 and a.trans_type=1","recv_qty");
		
		$recv_qty_sql = "select b.id as rec_dtl_id,b.quantity  from sub_material_dtls b, sub_material_mst a	where  a.id=b.mst_id and b.order_id=$order_no_id $gsm_cond  $material_description_cond  $color_cond  $size_id_cond  $fin_dia_cond $item_cat_cond  $lot_cond  and a.entry_form=288 and a.trans_type=1";
		//echo "10**=".$recv_qty_sql;die;
		 $recv_qty_result = sql_select($recv_qty_sql); 
		 $recv_qnty=0; $recv_ret_qnty=0;
		foreach ($recv_qty_result as $row) 
		{
			$recv_qnty+=$row[csf('quantity')];
			$recv_Idarr[$row[csf('rec_dtl_id')]]=$row[csf('rec_dtl_id')];
		}
		
		
		$recv_returned_sql = "select a.item_category_id, a.receive_dtls_id,a.lot_no, a.material_description, a.quantity,a.order_id from sub_material_return_dtls a	where a.order_id in ($order_no_id) and a.is_deleted = 0 and a.is_deleted = 0 and a.receive_dtls_id in(".implode(',',$recv_Idarr).") $material_description_cond_rec_ret";
		 //echo "10**=".$recv_returned_sql;die;
   		$recv_returned_result = sql_select($recv_returned_sql); 
		foreach($recv_returned_result as $row) 
		{
			$recv_ret_qnty+=$row[csf('quantity')];
		}
	 $recv_qnty_bal=$recv_qnty-$recv_ret_qnty;
	 

		$prevIssue_qnty=return_field_value("sum(b.quantity) as prevIssue_qnty","sub_material_dtls b, sub_material_mst a","a.id=b.mst_id and b.order_id=$order_no_id  $gsm_cond  $material_description_cond  $color_cond  $size_id_cond  $fin_dia_cond $item_cat_cond $lot_cond and id !=$update_id_dtl and a.entry_form=343 and b.status_active=1 and a.trans_type=2","prevIssue_qnty");
		//echo "10**".$recv_qnty."**".$prevIssue_qnty; die;

		 if((str_replace("'","",$txtissuequantity_1)+$prevIssue_qnty)>$recv_qnty_bal)
		{
			echo "17**Cumulative Issue Quantity Exceeds Receive Quantity-".',Priv Issue='.$prevIssue_qnty.',Recv='.$recv_qnty.',Recv Ret='.$recv_ret_qnty;
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		} 
		//------------------issue Id 17012 Start====================================//	
		$prevProdQty=return_field_value("sum(b.product_qnty) as prevProdQty","subcon_production_mst a, subcon_production_dtls b","a.id=b.mst_id and b.order_id=$order_no_id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ","prevProdQty");
		if(str_replace("'","",$txtissuequantity_1)<$prevProdQty)
		{
			echo "17**Cumulative Issue Quantity Exceeds Production Quantity ".',Priv Production='.$prevProdQty;
			disconnect($con);
			die;
		}
		//------------------END====================================//	
		$field_array="company_id*location_id*prod_source*party_id*subcon_date*chalan_no*remarks*updated_by*update_date";				
		$data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_source."*".$cbo_company_supplier."*".$txt_issue_date."*".$txt_issue_challan."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 	
		$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0);  //die;	
				
		$field_array2="order_id*item_category_id*rec_challan*material_description*quantity*subcon_uom*subcon_roll*rec_cone*grey_dia*gsm*fin_dia*color_id*size_id*lot_no*brand*updated_by*update_date*used_yarn_details";		
		$data_array2="".$order_no_id."*".$cboitemcategory_1."*".$txt_rec_challan_no."*".$hidden_materialdescription."*".$txtissuequantity_1."*".$cbouom_1."*".$txt_roll."*".$txt_cone."*".$txt_dia."*".$txt_gsm."*".$txt_fin_dia."*".$txt_color_id."*".$txt_size_id."*".$txt_lot_no."*".$txt_brand."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_used_yarn_details.""; 					
		$rID2=sql_update("sub_material_dtls",$field_array2,$data_array2,"id",$update_id_dtl,1);  		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_issue_no)."**".str_replace("'", '',$update_id)."**".str_replace("'",'',$update_id_dtl);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'", '',$update_id)."**".str_replace("'",'',$update_id_dtl);	
			}
		}	
		disconnect($con);
		die;
	}	
	// =================================== Delete Here=========================
	else if ($operation==2)   
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$order_no_id=str_replace("'","",$order_no_id);
		$sub_knitting_sql = sql_select("select a.product_no,b.order_id, b.product_type  from subcon_production_dtls b, subcon_production_mst a	where   a.id=b.mst_id and a.entry_form=159 and a.company_id=$cbo_company_name and a.product_type='2' and b.status_active=1 and b.order_id ='$order_no_id' ");
		foreach($sub_knitting_sql as $row)
		{
			$product_no=$row[csf('product_no')];
		}
		 
		$sub_cutting_sql = sql_select("select b.order_id  from subcon_gmts_prod_dtls b where   b.status_active=1 and b.order_id in($order_no_id)");

		$sub_dyeing_sql = sql_select("select b.po_id  from pro_batch_create_mst a,pro_batch_create_dtls b,pro_fab_subprocess f  where  a.id=b.mst_id  and a.id=f.batch_id  and b.status_active=1 and b.po_id in($order_no_id)");

	// echo "13**.select b.order_id  from subcon_gmts_prod_dtls b where   b.status_active=1 and b.order_id in($order_no_id)";die;
	 

		if($product_no!="" || count($sub_cutting_sql)>0 | count($sub_dyeing_sql)>0)
		{

			echo "13**Next Process Found. Delete not allowed.**".$product_no;
			die;
		}

		$recv_qty_sql = "select b.id as rec_dtl_id,b.quantity  from sub_material_dtls b, sub_material_mst a	where  a.id=b.mst_id and a.id=$update_id  and trans_type=2 and entry_form=343 and b.status_active=1";
		//echo "10**=".$recv_qty_sql;die;
		$recv_result =sql_select($recv_qty_sql);
		if(count($recv_result)==1)
		{
			$field_array_mst="updated_by*update_date*status_active*is_deleted";
			$data_array_mst="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$rID_mst=sql_update("sub_material_mst",$field_array_mst,$data_array_mst,"id",$update_id,1);
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		//$rID=sql_delete("sub_material_dtls",$field_array,$data_array,"id","".$update_id_dtl."",1);			
		$rID=sql_update("sub_material_dtls",$field_array,$data_array,"id",$update_id_dtl,1);			
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				//echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl);
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl);
			}
		}
		if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_issue_no)."**".str_replace("'", '',$update_id)."**".str_replace("'",'',$update_id_dtl);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'", '',$update_id)."**".str_replace("'",'',$update_id_dtl);	
			}
		}
		disconnect($con);
		die;
	}
}	
/* =============== Save Update Delete End Here  ===================*/

if($action=="material_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[4];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name"  );
	$party_library_arr=return_library_array("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]'  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id","buyer_name");
	//$order_array=return_library_array( "select id, order_no from subcon_ord_dtls", "id","order_no"  );
	$sql_po=sql_select("select id, order_no,job_no_mst from subcon_ord_dtls");
	foreach($sql_po as $row)
	{
		$order_array[$row[csf('id')]]=$row[csf('order_no')];
		$job_array[$row[csf('id')]]=$row[csf('job_no_mst')];
	}
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$sql_mst="Select sys_no, prod_source, party_id, subcon_date, chalan_no, remarks from sub_material_mst where company_id=$data[0] and id='$data[1]' and trans_type=2 and entry_form=343 and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
    <div style="width:1180px;">
         <table width="1180" cellspacing="0" align="right" border="0">
         <tr>
         	<td colspan="6">
         		
         	</td>
         </tr>
         <tr>
            <td colspan="2" rowspan="2">
				<img src="../<? echo $com_dtls[2]; ?>" height="60" width="200" style="float:left;">
            </td>
            <td colspan="4" align="center" style="font-size:x-large">
            	<strong><? echo $com_dtls[0]; ?></strong>
        	</td>
          </tr>

	        <tr>
	            <td colspan="4" align="center">
	                <?
	                	echo $com_dtls[1];
						//$party_id="";
	                  /*  $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
	                    foreach ($nameArray as $result)
	                    { 
	                    ?>
	                        <? echo $result[csf('plot_no')]; ?>,
	                        Level No: <? echo $result[csf('level_no')]?>,
	                        <? echo $result[csf('road_no')]; ?>, 
	                        <? echo $result[csf('block_no')];?>, 
	                       	<? echo $result[csf('city')];?>, 
	                       	<? echo $result[csf('zip_code')]; ?>, 
	                        <?php echo $result[csf('province')];?>, 
	                        <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
	                        Email Address: <? echo $result[csf('email')];?> 
	                        Website No: <? echo $result[csf('website')];?> <br>
	                        <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
							//$party_id= $result[csf('party_id')];
	                    }*/
	                ?> 
	            </td>
	        </tr> 

        	<tr>
                <td colspan="6" align="center" style="font-size:20px"><u>
                	<strong style="margin-left:265px;"><? echo $data[3]; ?></strong></u>
                </td>
            </tr>
             <tr>
             <?
			 	if($dataArray[0][csf('prod_source')]==3)
				{
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, email, country_id from lib_supplier where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
                    	$address="";
						if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					$party_name=$party_library[$dataArray[0][csf('party_id')]].' : Address :- '.$address;;
				}
				else if($dataArray[0][csf('prod_source')]==1)
				{
					$party_name=$company_library[$dataArray[0][csf('party_id')]];
				}
			 ?>
                <td><strong>Issue To: </strong></td><td colspan="5"> <? echo $party_name; ?></td>
                <td width="130"><strong>Issue No :</strong></td> <td width="175"><? echo $dataArray[0][csf('sys_no')]; ?></td>
            </tr>
            <tr>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('prod_source')]]; ?></td>
                <td width="130"></td> <td width="175"></td>
                <td width="130"></td> <td width="175"></td>
                <td width="130"><strong>Issue Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('subcon_date')]); ?></td>
            </tr>
           
            <tr>
            	<td width="130"><strong>Issue Challan:</strong></td><td><? echo $dataArray[0][csf('chalan_no')]; ?></td>
                <td><strong>Remarks:</strong></td><td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
         <br>
        <div style="width:100%;">
		<table align="right" cellspacing="0" width="1180"  border="1" rules="all" class="rpt_table"  >
            <thead bgcolor="#dddddd">
                <th width="20">SL</th>
                <th width="110">Job No</th>
                <th width="80">Order No</th>
                <th width="100">Customer Name</th>
                <th width="100">Item Category</th>
                <!-- <th width="70">Rec. Challan</th> -->
                <th width="130">Material Description</th> 
                <th width="130">Color</th> 
				<th width="60">Lot</th>
				<th width="60">Brand</th>
                <th width="30">Dia</th>
                <th width="30">Finish Dia</th> 
                <th width="80">Issue Qty</th>
                <th width="30">UOM</th>                   
                <th width="30">Roll/Bag</th>
                <th width="30">Cone</th>
                <th width="70">Stitch Length</th>
                <th width="100">Used Yarn Details</th>
            </thead>         
         <?	
         	/*if($db_type==0)
			{
				$used_yarn_details="group_concat(b.used_yarn_details) as used_yarn_details";
			}
			else if($db_type==2)
			{
				$used_yarn_details="listagg(cast(b.used_yarn_details as varchar2(4000)),',') within group (order by b.used_yarn_details) as used_yarn_details";
			}*/

			$sql_recv_arr=sql_select(" select a.chalan_no, b.order_id, b.material_description, b.stitch_length, b.color_id from sub_material_dtls b,sub_material_mst a where a.id=b.mst_id and b.status_active=2 and a.entry_form=288 and  a.is_deleted=0 and a.trans_type=1 and entry_form=288 group by a.chalan_no, b.order_id,b.material_description,b.stitch_length, b.color_id");
			foreach($sql_recv_arr as $row)
			{
			$recv_data_arr[$row[csf('order_id')]][$row[csf('chalan_no')]][$row[csf('material_description')]][$row[csf('color_id')]]['stitch_length'].=$row[csf('stitch_length')].',';
			//$recv_data_arr[$row[csf('order_id')]]['used_yarn_details']=$row[csf('used_yarn_details')];
			}
		 
			$i=1;
			$mst_id=$data[1];
			$dataArray=sql_select($sql_mst);
			//$sql_result =sql_select("select order_id, item_category_id, rec_challan, material_description, quantity, subcon_uom, subcon_roll, rec_cone, grey_dia,gsm,color_id,fin_dia from sub_material_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0"); 
			$sql_result =sql_select("select  c.party_id as sub_order_party_id, b.order_id, b.used_yarn_details, b.item_category_id, b.rec_challan, b.material_description, b.quantity,b.lot_no,b.brand, b.subcon_uom, b.subcon_roll, b.rec_cone, b.grey_dia,b.gsm,b.color_id,b.fin_dia from sub_material_mst a, sub_material_dtls b,subcon_ord_mst c,subcon_ord_dtls d where a.id=b.mst_id and a.entry_form=343 and  b.order_id=d.id and c.subcon_job=d.job_no_mst and b.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0");

			
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$ex_recchallan=array_unique(explode(",",$row[csf('rec_challan')]));
				
				$stitch_length='';
				foreach($ex_recchallan as $challan)
				{
					$rec_stitch_length="";
					$rec_stitch_length=implode(",",array_filter(array_unique(explode(",",$recv_data_arr[$row[csf('order_id')]][$challan][$row[csf('material_description')]][$row[csf('color_id')]]['stitch_length']))));
					if($stitch_length=="") $stitch_length=$rec_stitch_length; else $stitch_length.=', '.$rec_stitch_length;
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
                    <td><? echo $i; ?></td>
                    <td><p><? echo $job_array[$row[csf('order_id')]]; ?></p></td>
                     <td><p><? echo $order_array[$row[csf('order_id')]]; ?></p></td>
                    <td><p><? echo $party_library_arr[$row[csf('sub_order_party_id')]]; ?></p></td>
                    <td><p><? echo $item_category[$row[csf('item_category_id')]]; ?></p></td>

                    <!-- <td><p><? //echo $row[csf('rec_challan')]; ?></p></td> -->

                    <td><p><? echo $row[csf('material_description')]; if($row[csf('gsm')]!=""){ echo ", ".$row[csf('gsm')];} ?></p></td>
                    <td><p>
                    	<? 	
                    		//if($row[csf('item_category_id')]==13){ 
                    		echo $color_arr[$row[csf('color_id')]];
                    		//} 
                    	?>
                   	</p></td>
					   <td><p><? echo $row[csf('lot_no')]; ?></p></td>
					   <td><p><? echo $row[csf('brand')]; ?></p></td>
                    <td><p><? echo $row[csf('grey_dia')]; ?></p></td>
                    <td><p><? 	if($row[csf('item_category_id')]==13){echo $row[csf('fin_dia')];} ?></p></td>
                    <td align="right"><? echo number_format($row[csf('quantity')],2,'.',''); $tot_issue_qty+=$row[csf('quantity')]; ?>&nbsp;</td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf('subcon_uom')]]; ?></p></td>
                    <td align="right"><? echo $row[csf('subcon_roll')]; $tot_roll_qty+=$row[csf('subcon_roll')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('rec_cone')]; $tot_cone_qty+=$row[csf('rec_cone')]; ?>&nbsp;</td>
                    <td><p><? echo $stitch_length; ?></p></td>
                    <td><p><? echo $row[csf('used_yarn_details')]; ?></p></td>
                </tr>
                <?
                $i++;
			}
			?>
        	<tr> 
                <td align="right" colspan="11"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $tot_roll_qty; ?>&nbsp;</td>
                <td align="right"><? echo $tot_cone_qty; ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
        </table>
        <br>
		 <?
            echo signature_table(61, $data[0], "930px");
         ?>
   </div>
   </div>
	<?
}
?>
