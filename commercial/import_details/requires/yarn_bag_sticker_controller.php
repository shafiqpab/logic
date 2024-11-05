<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id = $_SESSION['logic_erp']["user_id"];


//------------------------------------------Load Drop Down on Change---------------------------------------------//
if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);		
	exit();
}


if ($action=="load_dropdown_supplier")
{
	$data = explode('_',$data);
	 echo create_drop_down( "cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id and c.tag_company='$data[0]' and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type=2) and a.id in(select supplier_id from lib_supplier_party_type where party_type=21) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );		
	exit();
}


if ($action=="load_dropdown_buyer")
{
	$data = explode('_',$data);
	 echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0);
		
	exit();
}

if ($action=="pi_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	if($cbo_receive_basis==1)
	{
	?> 
	<script>
		/*$(document).ready(function(e) {
            load_drop_down( 'yarn_bag_sticker_controller ',<?// echo $importer_id; ?>+'_'+<?// echo $item_category; ?>, 'load_supplier_dropdown', 'supplier_td' );
			$('#cbo_supplier_id').val( <?// echo $supplier_id; ?> );
        });*/
		
		function js_set_value( pi_id )
		{
			document.getElementById('txt_selected_pi_id').value=pi_id;
			parent.emailwindow.hide();
		}
		
    </script>

</head>

<body>
<div align="center" style="width:900px;">
	<form name="searchpifrm"  id="searchpifrm">
		<fieldset style="width:100%;">
		<legend>Enter search words</legend> 
                 
            <table cellpadding="0" cellspacing="0" width="800px" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Supplier</th>
                    <th>PI Number</th>
                    <th>Date Range</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="txt_selected_pi_id" id="txt_selected_pi_id" class="text_boxes" style="width:70px" value="">   
                    </th> 
                </thead>
                <tr class="general">
                    <td>
						 <? echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',"","load_drop_down( 'yarn_bag_sticker_controller',this.value+'_1', 'load_supplier_dropdown', 'supplier_td' );",0); ?>       
                    </td>
                    <td id="supplier_td">	
                        <?
							echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
						?> 
                    </td>                 
                    <td> 
                        <input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:120px">
                    </td>						
                    <td align="center">
                      <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_supplier_id').value, 'create_pi_search_list_view', 'search_div', 'yarn_bag_sticker_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
            <table width="100%" style="margin-top:5px">
                <tr>
                    <td colspan="5">
                        <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
                    </td>
                </tr>
            </table> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	
</script>
</html>
<?php
}
else if($cbo_receive_basis==2)
{

?>          
  <script>
		
		function js_set_value( wo_number )
		{
			document.getElementById('txt_selected_pi_id').value=wo_number;
			parent.emailwindow.hide();
		}	
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <input type="hidden" name="txt_selected_pi_id" id="txt_selected_pi_id" class="text_boxes" style="width:70px" value=""> 
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="900" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                         <thead>
                         	<th width="100">Company Name</th>
                            <th width="100">Item Category</th>
                            <th width="130">Supplier</th>
                            <th width="150" align="center" id="search_by_th_up">Order Number</th>
                            <th width="200">WO Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
                        </thead>
                        <tr>
                        	 <td>
								 <? echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',"","load_drop_down( 'yarn_bag_sticker_controller',this.value+'_1', 'load_supplier_dropdown', 'supplier_td' );",0); ?>       
                            </td>
                            <td width="100"> 
                            <?
                                echo create_drop_down( "cboitem_category", 100, $item_category,"", 1, "-- Select --", 1, "",1);
                            ?> 
                            </td>
                            <td id="supplier_td">	
								<?
                                    echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
                                ?> 
                            </td>
                            <td width="150" align="center" id="search_by_td">				
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_wo_number" id="txt_wo_number" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />			
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_wo_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_importer_id').value, 'create_wo_search_list_view', 'search_div', 'yarn_bag_sticker_controller', 'setFilterGrid(\'list_view\',-1)');$('#selected_id').val('')" style="width:80px;" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="top" id="search_div"></td>
            </tr>
        </table> 
    </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
<?php
}
else if($cbo_receive_basis==3)
{
	if($db_type==0) $select_field_grp="group by a.id order by supplier_name"; 
	else if($db_type==2) $select_field_grp="group by a.id,a.supplier_name order by supplier_name";
	
?>
     
<script>
	function js_set_value( wo_number )
	{
		document.getElementById('txt_selected_pi_id').value=wo_number;
		parent.emailwindow.hide();
	}
	var permission= '<? echo $permission; ?>';
</script>
</head>
 <div align="center" style="width:830px;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
     <input type="hidden" name="txt_selected_pi_id" id="txt_selected_pi_id" class="text_boxes" style="width:70px" value=""> 
        <table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        
            <thead>
            	<tr>
                	<th colspan="3"> </th>
                    <th  >
                      <?
                       echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                      ?>
                    </th>
                    <th colspan="3" style="text-align:right"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Job</th>
                </tr>
                <tr>
                	<th width="140">Company Name</th>
                	<th width="140">Buyer Name</th>
                    <th  width="140">Supplier Name</th>
                    <th width="80">Booking No</th>
                    <th width="80">Job No</th>
                    <th  width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
                </tr>
             </thead>
            <tbody>
                <tr>
                	<td>
					 <? echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',"","load_drop_down( 'yarn_bag_sticker_controller',this.value+'_1', 'load_dropdown_supplier', 'supplier_td' );load_drop_down( 'yarn_bag_sticker_controller',this.value+'_1', 'load_dropdown_buyer', 'buyer_td' );",0); ?>       
                            </td>
                	<td align="center" id="buyer_td">
					<?
					echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0);
					?>
                    </td>
                    <td align="center" id="supplier_td">
					<?
                  
                     echo create_drop_down( "cbo_supplier_name", 140, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
					 ?>
                    </td>
                     <td align="center"><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                    <td align="center"><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" />
                     </td> 
                     <td align="center">
                       <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+ document.getElementById('chk_job_wo_po').value, 'create_sys_search_list_view', 'search_div', 'yarn_bag_sticker_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:80px;" />				
                    </td>
                </tr>
                <tr>                  
                    <td align="center" height="40" valign="middle" colspan="6">
                        <? echo load_month_buttons(1);  ?>
                        <!-- Hidden field here-------->
                       <!-- input type="hidden" id="hidden_tbl_id" value="" ---->
                       
                        <input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
                         <input type="hidden" id="hidden_id" value="hidden_id" />
                        <!-- ---------END-------------> 
                    </td>
                </tr>    
            </tbody>
        </table>    
        <div align="center" valign="top" id="search_div"></div> 
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
else if($cbo_receive_basis==4)
{

?>          
  <script>
		
		function js_set_value( wo_number )
		{
			document.getElementById('txt_selected_pi_id').value=wo_number;
			parent.emailwindow.hide();
		}	
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <input type="hidden" name="txt_selected_pi_id" id="txt_selected_pi_id" class="text_boxes" style="width:70px" value=""> 
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="900" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                         <thead>
                         	<th width="100">Company Name</th>
                            <th width="100">Item Category</th>
                            <th width="130">Supplier</th>
                            <th width="150" align="center" id="search_by_th_up">Order Number</th>
                            <th width="200">WO Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
                        </thead>
                        <tr>
                        	 <td>
								 <? echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',"","load_drop_down( 'yarn_bag_sticker_controller',this.value+'_1', 'load_supplier_dropdown', 'supplier_td' );",0); ?>       
                            </td>
                            <td width="100"> 
                            <?
                                echo create_drop_down( "cboitem_category", 100, $item_category,"", 1, "-- Select --", 1, "",1);
                            ?> 
                            </td>
                            <td id="supplier_td">	
								<?
                                    echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
                                ?> 
                            </td>
                            <td width="150" align="center" id="search_by_td">				
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_wo_number" id="txt_wo_number" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />			
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_wo_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_year_selection').value, 'create_sys_search_list_view_without_order', 'search_div', 'yarn_bag_sticker_controller', 'setFilterGrid(\'list_view\',-1)');$('#selected_id').val('')" style="width:80px;" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="top" id="search_div"></td>
            </tr>
        </table> 
    </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
<?php
}
exit();
}


if($action=="create_sys_search_list_view_without_order")
{ 
	$ex_data = explode("_",$data);
	$supplier = $ex_data[1];
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
	
	//echo $buyer_val;die;
 	//$sql_cond=""; LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
	//var_dump($order_no_arr);die;
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if($db_type==0) 
	{
		$booking_year_cond=" and year(a.insert_date)=$ex_data[6]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	if($db_type==2) 
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[6]";	
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";
	}
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";
	
	if (str_replace("'","",$ex_data[2])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[2]%'  $booking_year_cond  "; else $booking_cond="";
		
	
	 if($db_type==0)
	 {
		 $sql = "select
		 a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year,group_concat(distinct b.job_no_id) as job_no_id,group_concat(distinct b.sample_name) as sample_name,  d.buyer_name  
		 from  
				wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join   sample_development_mst d on  b.job_no_id=d.id
		 where  
				a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form in (42,114) and b.entry_form in (42,114) $company $supplier  $sql_cond   $booking_cond
		 group by a.id";
	 }
	 //LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
	 else if($db_type==2)
	 {
		 $sql = "select
		 a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, rtrim(xmlagg(xmlelement(e,b.job_no_id,',').extract('//text()') order by b.job_no_id).GetClobVal(),',') AS job_no_id , rtrim(xmlagg(xmlelement(e,b.sample_name,',').extract('//text()') order by b.sample_name).GetClobVal(),',') AS sample_name, d.buyer_name  
		 from  
				wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join   sample_development_mst d on  b.job_no_id=d.id
		 where  
				a.id=b.mst_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form in (42,114) and b.entry_form in (42,114) $company $supplier  $sql_cond  $booking_cond
		group by 
				a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,d.buyer_name";
	 }
	//echo $sql;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$sample_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	//print_r();die;	
?>	<div style="width:860px; "  align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="50">Booking no</th>
                <th width="40">Year</th>
                <th width="120">Sample Develop Id</th>
                <th width="220">Sample Name</th>
                <th width="100">Buyer Name</th>
                <th width="120">Supplier Name</th>
                <th width="70">Booking Date</th>
                <th >Delevary Date</th>
            </thead>
        </table>
        <div style="width:860px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_list_search" >
            <?
			 
				$i=1;
				$nameArray=sql_select( $sql );
                if($db_type==2) $row[csf('job_no_id')] = $row[csf('job_no_id')]->load();
                if($db_type==2) $row[csf('sample_name')] = $row[csf('sample_name')]->load();
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult)
				{
					
					$sample_name=array_unique(explode(",",$selectResult[csf("sample_name")]));
					$sample_develop_id=implode(",",array_unique(explode(",",$selectResult[csf("job_no_id")])));
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'+'_'+'<? echo $selectResult[csf('company_id')]; ?>'); "> 
                    
                     <td width="30" align="center"> <p><? echo $i; ?></p></td>
                      <td width="50" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>	
                      <td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>	
                      <td width="120"><p>
					  <?  
					  	echo $sample_develop_id; 
					  ?>
                      </p></td>	
                      <td width="220"> <p>
					  <? 
					  $sample_name_group="";
					  foreach($sample_name as $val)
					  {
						  if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val] ;
					  }
					  echo  $sample_name_group;
					  	//$po_no=implode(",",array_unique(explode(",",$order_no_arr[$job_no_id]))); echo $po_no;  
					  ?>
                      </p></td>	
                      <td width="100"><p> <? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>	
                      <td width="120"> <p><? echo $supplier_arr[$selectResult[csf('supplier_id')]]; ?></p></td>	
                      <td width="70"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>	
                      <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>	
                    </tr>
                <?
                	$i++;
				}
			?>
            </table>
        </div>
        </div>
	 

<? exit();
	
}


if($action=="create_sys_search_list_view")
{ 
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val=$ex_data[4];
	$chk_job_wo_po=trim($ex_data[9]);
	//echo $buyer_val;die;
 	//$sql_cond=""; LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
	/*if($db_type==0)
	{
		$order_no_arr = return_library_array( "select a.id, group_concat(distinct b.po_number) as po_number  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst group by a.id",'id','po_number');

	}
	else
	{
		$order_no_arr = return_library_array( "select a.id, LISTAGG(CAST(b.po_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) as po_number  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst group by a.id",'id','po_number');
	}*/
	
	
	//var_dump($order_no_arr);die;
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";
	if($db_type==0) 
	{
	 $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$ex_data[8]";
	 $year_cond=" and SUBSTRING_INDEX(d.insert_date, '-', 1)=$ex_data[8]"; 
	 if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	if($db_type==2) 
	{
	  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[8]";
	  $year_cond=" and to_char(d.insert_date,'YYYY')=$ex_data[8]";
	  if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";
	}
	
	//TO_CHAR(insert_date,'YYYY')
	/*$sql = "select id, yarn_dyeing_prefix_num, ydw_no, company_id, supplier_id, booking_date, delivery_date, currency, ecchange_rate, pay_mode,source, attention from  wo_yarn_dyeing_mst where  status_active=1 and is_deleted=0 $supplier $company $sql_cond";*/
	
	if($ex_data[5]==4 || $ex_data[5]==0)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num like '%$ex_data[7]%' $year_cond "; else  $job_cond=""; 
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
	}
    if($ex_data[5]==1)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num ='$ex_data[7]' "; else  $job_cond=""; 
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num ='$ex_data[6]'   "; else $booking_cond="";
	}
   if($ex_data[5]==2)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num like '$ex_data[7]%'  $year_cond"; else  $job_cond=""; 
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
	}
	if($ex_data[5]==3)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num like '%$ex_data[7]'  $year_cond"; else  $job_cond=""; 
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]'  $booking_year_cond  "; else $booking_cond="";
	}
	
	if($db_type==0) $select_year="year(a.insert_date) as year"; else $select_year="to_char(a.insert_date,'YYYY') as year";
	if($chk_job_wo_po==1)
	{
		$sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number  
		from wo_yarn_dyeing_mst a
		where a.status_active=1 and a.is_deleted=0 and a.entry_form=41 and a.id not in(select mst_id from wo_yarn_dyeing_dtls where job_no_id>0 and entry_form in (41,125)  and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond";
	}
	else
	{
		if($db_type==0)
		{
			$sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year,group_concat(distinct b.job_no_id) as job_no_id, group_concat(distinct b.job_no) as job_no,d.buyer_name, group_concat(distinct e.po_number) as po_number  
			from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, wo_po_details_master d, wo_po_break_down e
			where a.id=b.mst_id and b.job_no_id=d.id and d.job_no=e.job_no_mst and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form in (41,125) and b.entry_form in (41,125) $company $supplier  $sql_cond  $buyer_cond $job_cond $booking_cond
			group by a.id";
		}
		else if($db_type==2)
		{
			$sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, rtrim(xmlagg(xmlelement(e,b.job_no,',').extract('//text()') order by b.job_no).GetClobVal(),',') AS job_no, rtrim(xmlagg(xmlelement(e,b.job_no_id,',').extract('//text()') order by b.job_no_id).GetClobVal(),',') AS job_no_id ,d.buyer_name,  rtrim(xmlagg(xmlelement(e,e.po_number,',').extract('//text()') order by e.po_number).GetClobVal(),',') AS po_number
			from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, wo_po_details_master d, wo_po_break_down e
			where a.id=b.mst_id and b.job_no_id=d.id and d.job_no=e.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.entry_form in (41,125) and b.entry_form  in(41,125) $company $supplier  $sql_cond  $buyer_cond  $job_cond $booking_cond
			group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,d.buyer_name";
		}
	}


?>	<div style="width:860px; "  align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="50">Booking no</th>
                <th width="40">Year</th>
                <th width="120">Job No</th>
                <th width="220">Order No</th>
                <th width="100">Buyer Name</th>
                <th width="120">Supplier Name</th>
                <th width="70">Booking Date</th>
                <th >Delevary Date</th>
            </thead>
        </table>
        <div style="width:860px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_list_search" >
            <?
			 
				$i=1;
				$nameArray=sql_select( $sql );
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult)
				{
					if($db_type==2){
                        $row[csf('job_no')] = $row[csf('job_no')]->load();
                        $row[csf('job_no_id')] = $row[csf('job_no_id')]->load();
                        $row[csf('po_number')] = $row[csf('po_number')]->load();
                    } 
					$job_no=implode(",",array_unique(explode(",",$selectResult[csf("job_no")])));
					$job_no_id=implode(",",array_unique(explode(",",$selectResult[csf("job_no_id")])));
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'+'_'+'<? echo $selectResult[csf('company_id')]; ?>'); "> 
                    
                     <td width="30" align="center"> <p><? echo $i; ?></p></td>
                      <td width="50" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>	
                      <td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>	
                      <td width="120"><p><?  echo $job_no; ?></p></td>	
                      <td width="220"> <p><?  $po_no=implode(",",array_unique(explode(",",$selectResult[csf('po_number')]))); echo $po_no;  ?></p></td>	
                      <td width="100"><p> <? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>	
                      <td width="120"> <p><? echo $supplier_arr[$selectResult[csf('supplier_id')]]; ?></p></td>	
                      <td width="70"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>	
                      <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>	
                    </tr>
                <?
                	$i++;
				}
			?>
            </table>
        </div>
        </div>
	 

<? exit();
	
}


if($action=="create_pi_search_list_view")
{
	$data=explode('_',$data);
	 
	if ($data[0]!="") $pi_number=" and pi_number like '%".trim($data[0])."%'"; else { $pi_number = ''; }
	if ($data[1]!="" &&  $data[2]!="")
	{
		if($db_type==0)
		{
			$pi_date = "and pi_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$pi_date = "and pi_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'";
		}
	}
	else
	{
		$pi_date ="";
	}
	$importer_id =$data[3];
	if($data[5]==0) $supplier_id="%%"; else $supplier_id =$data[4];
	  
	if($importer_id==0) { echo "Please Select Company First."; die; }
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$arr=array (2=>$item_category,3=>$comp,4=>$supplier,7=>$pi_basis);
	 
	
	$sql= "select id,pi_number,pi_date,item_category_id,importer_id,supplier_id,last_shipment_date,hs_code,pi_basis_id 
	from com_pi_master_details where supplier_id like '$supplier_id' and importer_id = $importer_id and item_category_id = 1 $pi_number $pi_date and status_active=1 and is_deleted=0 and goods_rcv_status<>1 order by pi_number"; 
	
	//echo $sql; 
	
	echo create_list_view("list_view", "PI No,PI Date,Item Category,Importer,Supplier,Last Shipment Date,HS Code,PI Basis", "100,80,80,130,100,90,100","880","270",0, $sql , "js_set_value", "id,pi_number,importer_id", "", 1, "0,0,item_category_id,importer_id,supplier_id,0,0,pi_basis_id", $arr , "pi_number,pi_date,item_category_id,importer_id,supplier_id,last_shipment_date,hs_code,pi_basis_id", "",'','0,3,0,0,0,3,0,0');
	 
exit();	
} 

if($action=="create_wo_search_list_view")
{
 	extract($_REQUEST); 
	$ex_data = explode("_",$data);
	$itemCategory = $ex_data[0];
	$cbo_supplier = $ex_data[1];
	$txt_wo_number = $ex_data[2];
	$txt_date_from = $ex_data[3];
	$txt_date_to = $ex_data[4];
	$company = $ex_data[5];
 	$garments_nature = $ex_data[6];
				
	$sql_cond="";
	if(trim($itemCategory)!="") $sql_cond .= " and item_category='$itemCategory'";
	if(trim($cbo_supplier)!=0)
	{
		$sql_cond .= " and supplier_id=trim('$cbo_supplier')";		
 	}
	if(trim($txt_wo_number)!="")
	{
		$sql_cond .= " and wo_number like '%".trim($txt_wo_number)."'";
	}
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	if($txt_date_from!="" && $txt_date_to!="") 
	{
		if($db_type==2) 
		{
			$sql_cond .= " and wo_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
		}
		else
		{
			$sql_cond.= " and wo_date between '".change_date_format(trim($txt_date_from),"yyyy-mm-dd","-")."' and '".change_date_format(trim($txt_date_to),"yyyy-mm-dd","-")."'";	
		}
	}
	if(trim($company)!="") $sql_cond .= " and company_name='$company'";
		
 	$sql = "select id, wo_number_prefix_num, wo_number, company_name, buyer_po, company_name,wo_date,supplier_id,attention,wo_basis_id, item_category, currency_id,delivery_date,source,pay_mode 
	from wo_non_order_info_mst where status_active=1 and is_deleted=0 and item_category=1 and pay_mode!=2 $sql_cond order by id"; //and garments_nature=$garments_nature
	//echo $sql;die;
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
 	
	$arr=array(0=>$company_arr,3=>$pay_mode,4=>$supplier_arr,5=>$wo_basis,6=>$source);
	echo  create_list_view("list_view", "Company, WO Number, WO Date, Pay Mode, Supplier, WO Basis, Source", "150,100,100,100,150,100,100","900","250",0, $sql, "js_set_value", "id,wo_number,company_name", "", 1, "company_name,0,0,pay_mode,supplier_id,wo_basis_id,source", $arr , "company_name,wo_number_prefix_num,wo_date,pay_mode,supplier_id,wo_basis_id,source", "","",'0,0,3,0,0,0,0,0');
 	exit();	
}


if( $action =='pi_details' ) 
{	

	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
	$data = explode( '_', $data );
	
	$item_category_id=1;
	$pi_id=$data[0];
	$receive_basis=$data[1];
	$update_id=$data[2];
	$color_array=return_library_array( "select id, color_name from lib_color",'id','color_name');

	?>
    <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
		
        	<thead>
            	<th style="color:blue;"  >Lot</th>
                <th style="color:blue;"  >Count</th>
                <th style="color:blue;"  >Composition</th>
                <th style="color:blue;"  >%</th>
                <th style="color:blue;"  >Yarn Type</th>
                <th style="color:blue;"  >Color</th>
                <th style="color:blue;"  >Brand</th>
                <th style="color:blue;"  >No of Bag</th>
                <th style="color:blue;"  >Wgt/Cone</th>
                <th style="color:blue;"  >Cone/Bag</th>
                <th style="color:blue;"  >Bag/Wgt</th>
                <th style="color:blue;"  >Rate/PerUnit</th>
                <th width="30"></th>
            </thead>
            <tbody>
       		<?
	

		if($receive_basis==1)
		{
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type, uom, quantity, rate,net_pi_rate, amount from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0" );

				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
				$i=1;
				foreach($nameArray as $row)
				{
					
					
            	?>
                <tr class="general" id="row_<?php echo $i; ?>">
                	<td>
                    	<input type="checkbox" id="check_<?php echo $i; ?>" name="check[]"  checked />
                        <input type="text" name="lotName[]" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:80px"/>
                    </td>
                    
                    <td>
                    <?
                        echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$row[csf('count_name')],1,"","","","","","","","countName[]"); 
                    ?>                         
                    </td>
                    <td>
                        <?
                            echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$row[csf('yarn_composition_item1')],"control_composition(1,'comp_one')",1,"","","","","","","yarnCompositionItem[]"); 
                        ?>    
                    </td>
                    <td>
                        <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $row[csf("yarn_composition_percentage1")];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                    </td>
                    <td>
                        <?
                            echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],1,"","","","","","","","yarnType[]"); 						?>    
                    </td>
                    <td>
                        <input type="text" name="colorName[]" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$row[csf('color_id')]];?>" style="width:60px;" readonly/>
                    </td>
                     <td>
                        <input type="text" name="brand[]" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="" style="width:60px;" />
                    </td>
                    <td>
                         <input type="text" name="noOfBag[]" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:80px"  class="text_boxes_numeric" />
                    </td>
                    <td>
                        <input type="text" name="conWgt[]" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  />
                    </td>
                    <td>
                        <input type="text" name="bagCon[]" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" />
                    </td>
                    <td>
                        <input type="text" name="bagWgt[]" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:60px;"   readonly/>
                        <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<?php echo $i; ?>" value="" readonly/>
                        <input type="hidden" name="colorId[]" id="colorId_<?php echo $i; ?>" value="<?php echo $row[csf('color_id')]; ?>" readonly/>
                        <input type="hidden" name="piDtlsId[]" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
                    </td>
                    <td>
                        <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $row[csf('net_pi_rate')]; ?>" style="width:60px;"/>
                    </td>
                     <td>
                        <input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
                     
                     </td>
                </tr>
                
                
            <?
				$i++;
			}
		}
		else if($receive_basis==2)
		{
		
			$nameArray=sql_select( "select b.id, a.wo_basis_id, b.requisition_dtls_id, b.requisition_no, b.job_id, b.job_no, b.buyer_id, b.style_no, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name, b.req_quantity, b.supplier_order_quantity, b.uom, b.rate, b.amount, b.yarn_inhouse_date, b.remarks from wo_non_order_info_mst a, wo_non_order_info_dtls b where	a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$pi_id' and a.id=b.mst_id" );

				$i=1;
				foreach($nameArray as $row)
				{
					$check_cond="";
				
					
            	?>
                <tr class="general" id="row_<?php echo $i; ?>">
                	<td>
                    	<input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" checked />
                        <input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:70px"  value=""/>
                    </td>
                    
                    <td>
                    <?
                        echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$row[csf('yarn_count')],1,""); 
                    ?>                         
                    </td>
                    <td>
                        <?
                            echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$row[csf('yarn_comp_type1st')],"control_composition(1,'comp_one')",1); 
                        ?>    
                    </td>
                    <td>
                        <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $row[csf("yarn_comp_percent1st")];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                    </td>
                    <td>
                        <?
                            echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],1,""); 
                        ?>    
                    </td>
                    <td>
                        <input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$row[csf('color_name')]];?>" readonly/>
                    </td>
                    <td>
                        <input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="" />
                    </td>
                    <td>
                         <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:60px"  class="text_boxes_numeric" value=""/>
                    </td>
                    <td>
                        <input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value=""/>
                    </td>
                    <td>
                        <input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value=""/>
                    </td>
                    <td>
                        <input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:60px;"  value="" readonly/>
                        <input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="" readonly/>
                        <input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>" value="<?php echo $row[csf('color_name')]; ?>" readonly/>
                        <input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
                    </td>
                     <td>
                        <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $row[csf('rate')]; ?>" style="width:60px;"/>
                    </td>
                     <td>
                        <input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
                     
                     </td>
                </tr>
            <?
				$i++;
			}
		}
		else if($receive_basis==3)
		{
		
			if($db_type==0)
			{
				$sql = "select a.id, a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no,a.yarn_comp_type1st ,a.yarn_type , a.yarn_comp_percent1st
				from wo_yarn_dyeing_dtls a
				where  a.mst_id='$pi_id' 
				group by a.id, a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no,a.yarn_comp_type1st ,a.yarn_type, a.yarn_comp_percent1st ";
			}
			else
			{
				$sql = "select a.id, a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no,a.yarn_comp_type1st ,a.yarn_type, a.yarn_comp_percent1st 
				from wo_yarn_dyeing_dtls a
				where  a.mst_id='$pi_id' 
				group by a.id, a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no,a.yarn_comp_type1st ,a.yarn_type, a.yarn_comp_percent1st ";
			}
//echo $sql;
				$i=1;
				$nameArray=sql_select($sql);
				foreach($nameArray as $row)
				{
					$check_cond="";
					if($check_update_id!="")
					{
						if(in_array($row[csf('id')],$updata_details_id))
						{
							$check_cond="checked";
						}
					}
					else
					{
						$check_cond="checked";
					}
					
            	?>
                <tr class="general" id="row_<?php echo $i; ?>">
                	<td>
                    	<input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" <?php echo  $check_cond; ?> checked/>
                        <input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:70px"  value="<?php echo $update_data_arr[$row[csf('id')]]['lot_no']; ?>"/>
                    </td>
                    
                    <td>
                    <?
                        echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$row[csf('count')],"",1); 
                    ?>                         
                    </td>
                    <td>
                        <?
                            echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$row[csf('yarn_comp_type1st')],"control_composition(1,'comp_one')",1); 
                        ?>    
                    </td>
                    <td>
                        <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $row[csf("yarn_comp_percent1st")];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                    </td>
                    <td>
                        <?
              
                            echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",1); 
                        ?>    
                    </td>
                    <td>
                        <input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$row[csf('yarn_color')]];?>" readonly/>
                    </td>
                    <td>
                        <input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="" />
                    </td>
                    <td>
                         <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:60px"  class="text_boxes_numeric" value="<?php echo  $update_data_arr[$row[csf('id')]]['no_of_bag']; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value="<?php echo $update_data_arr[$row[csf('id')]]['weight_per_con']; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value="<?php echo $update_data_arr[$row[csf('id')]]['con_per_bag']; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:60px;"  value="<?php echo $update_data_arr[$row[csf('id')]]['bag_weight']; ?>" readonly/>
                        <input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['id']; ?>" readonly/>
                        <input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>" value="<?php echo $row[csf('yarn_color')]; ?>" readonly/>
                        <input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
                    </td>
                     <td>
                        <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $row[csf('dyeing_charge')]; ?>" style="width:60px;"/>
                    </td>
                    <td>
                        <input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
                     
                     </td>
                </tr>
            <?
				$i++;
			}
		}
		else if($receive_basis==4)
		{
		
			if($db_type==0)
			{
				$sql = "select a.id, a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no,a.yarn_comp_type1st ,a.yarn_type , a.yarn_comp_percent1st
				from wo_yarn_dyeing_dtls a
				where  a.mst_id='$pi_id' 
				group by a.id, a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no,a.yarn_comp_type1st ,a.yarn_type, a.yarn_comp_percent1st ";
			}
			else
			{
				$sql = "select a.id, a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no,a.yarn_comp_type1st ,a.yarn_type , a.yarn_comp_percent1st
				from wo_yarn_dyeing_dtls a
				where  a.mst_id='$pi_id' 
				group by a.id, a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no,a.yarn_comp_type1st ,a.yarn_type, a.yarn_comp_percent1st ";
			}
//echo $sql;
				$i=1;
				$nameArray=sql_select($sql);
				foreach($nameArray as $row)
				{
					$check_cond="";
					if($check_update_id!="")
					{
						if(in_array($row[csf('id')],$updata_details_id))
						{
							$check_cond="checked";
						}
					}
					else
					{
						$check_cond="checked";
					}
					
            	?>
                <tr class="general" id="row_<?php echo $i; ?>">
                	<td>
                    	<input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" <?php echo  $check_cond; ?> checked/>
                        <input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:70px"  value="<?php echo $update_data_arr[$row[csf('id')]]['lot_no']; ?>"/>
                    </td>
                    
                    <td>
                    <?
                        echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$row[csf('count')],"",1); 
                    ?>                         
                    </td>
                    <td>
                        <?
                            echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$row[csf('yarn_comp_type1st')],"control_composition(1,'comp_one')",1); 
                        ?>    
                    </td>
                    <td>
                        <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $row[csf("yarn_comp_percent1st")];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                    </td>
                    <td>
                        <?
              
                            echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",1); 
                        ?>    
                    </td>
                    <td>
                        <input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$row[csf('yarn_color')]];?>" readonly/>
                    </td>
                    <td>
                        <input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="" />
                    </td>
                    <td>
                         <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:60px"  class="text_boxes_numeric" value="<?php echo  $update_data_arr[$row[csf('id')]]['no_of_bag']; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value="<?php echo $update_data_arr[$row[csf('id')]]['weight_per_con']; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value="<?php echo $update_data_arr[$row[csf('id')]]['con_per_bag']; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:60px;"  value="<?php echo $update_data_arr[$row[csf('id')]]['bag_weight']; ?>" readonly/>
                        <input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['id']; ?>" readonly/>
                        <input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>" value="<?php echo $row[csf('yarn_color')]; ?>" readonly/>
                        <input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
                    </td>
                     <td>
                        <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $row[csf('dyeing_charge')]; ?>" style="width:60px;"/>
                    </td>
                    <td>
                        <input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
                     </td>
                </tr>
            <?
				$i++;
			}
		}
			?>
            </tbody>

    </table>
   
    <?
}


if( $action =='pi_details_listview' ) 
{	
	$data = explode( '_', $data );
	$pi_id=$data[0];
	$receive_basis=$data[1];
	$pi_array=return_library_array( "select id, pi_number from com_pi_master_details",'id','pi_number');
	?>
    <table class="rpt_table" width="500" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
        	<thead>
            	<th  width="40">SL</th>
            	<th width="120">PI Number</th>
                <th width="80">Date</th>
                <th width="90">No of Bag</th>
                <th width="">Total Bag Weight</th>
            </thead>
            <tbody>
       		<? //echo "select a.id, sum(b.no_of_bag) as total_bag, sum(b.bag_weight) as weight,a.delivery_date from com_yarn_bag_sticker a,com_yarn_bag_sticker_dtls b where a.id=b.mst_id and a.wo_pi_id=$pi_id  and a.receive_basis=$receive_basis and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.delivery_date";
				$sql_data=sql_select("select a.id, sum(b.no_of_bag) as total_bag,sum(b.bag_weight*b.no_of_bag) as weight,a.delivery_date from com_yarn_bag_sticker a,com_yarn_bag_sticker_dtls b where a.id=b.mst_id and a.wo_pi_id=$pi_id  and a.receive_basis=$receive_basis and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.delivery_date");
				//print_r($sql_data);die;
				$i=1;
				foreach($sql_data as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					 <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_data_dtls_part('<? echo $row[csf('id')]; ?>');">
                        <td width="40"><? echo $i; ?></td> 
                        <td width="120"><p><? echo $pi_array[$pi_id]; ?></p></td>
                        <td width="80"><p><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</p></td>
                        <td width="90"  align="right"><p><? echo $row[csf('total_bag')]; ?></p></td>
                        <td width=""  align="right"><p><? echo $row[csf('weight')]; ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $i++;
				}
			?>
            </tbody>

    </table>
   
    <?
}

if ($action=="save_update_delete_dtls")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$brand_library = return_library_array("select id,brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		
		if($db_type==2) $txt_date=change_date_format($txt_date,"","",1);
		else $txt_date=change_date_format($txt_date,"yyyy-mm-dd", "-");
		
		$id=return_next_id( "id", "com_yarn_bag_sticker", 1 ); 
		$field_array="id, wo_pi_id,company_id,receive_basis,wo_pi_no,delivery_date, inserted_by, insert_date,status_active,is_deleted";
		
	
		$field_array_dtls=" id, mst_id, wo_pi_id,pi_dtls_id,receive_basis_id, color_id,brand, count_name,composition,composition_percentase, yarn_type, no_of_bag,lot_no, weight_per_con, con_per_bag,bag_weight,rate_perunit, inserted_by, insert_date,status_active,is_deleted";
		
		$field_array_barcode="id,dtls_id, mst_id,pi_dtls_id,barcode_year, barcode_suffix_no, barcode_no, wo_pi_id, receive_basis_id,color_id,brand, count_name, composition,composition_percentase,yarn_type, no_of_bag, lot_no,weight_per_con, con_per_bag, bag_weight,rate_perunit,inserted_by, insert_date, status_active, is_deleted";
		$idDtls=return_next_id( "id","com_yarn_bag_sticker_dtls", 1 ) ;
		
		$idBarcode=return_next_id( "id","com_yarn_bag_sticker_barcode", 1 ) ;
		$barcode_year=date("y"); 
		
		$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","com_yarn_bag_sticker_barcode","barcode_year=$barcode_year","suffix_no");
		for($i=1;$i<=$total_row;$i++)
		{
			$lotName="lotName_".$i;
			$countName="countName_".$i;
			$yarnComposition="yarnCompositionItem_".$i;
            $yarnCompositionPercentage="yarnCompositionPercentage_".$i;
			$yarnType="yarnType_".$i; 
			$colorId="colorId_".$i;
            $brand="brand_".$i;
			//$gsm="gsm_".$i;
			$noOfBag="noOfBag_".$i;
			$conWgt="conWgt_".$i;
			$bagCon="bagCon_".$i;
			$bagWgt="bagWgt_".$i;
			$rateUnit="rateUnit_".$i;
			$piDtlsId="piDtlsId_".$i;
			
			/*if (str_replace("'", "", $$brand) != "") {
				$$brand = return_id(str_replace("'", "", $$brand), $brand_library, "lib_brand", "id,brand_name");
			}*/
			
			$brand_id = 0;
			if (str_replace("'", "", trim($$brand)) != "") {
				if (!in_array(str_replace("'", "", trim($$brand)),$new_array_brand)){
					$brand_id = return_id( str_replace("'", "", trim($$brand)), $brand_library, "lib_brand", "id,brand_name","406");
					$new_array_brand[$brand_id]=str_replace("'", "", trim($$brand));
				}
				else $brand_id =  array_search(str_replace("'", "", trim($$brand)), $new_array_brand);
			} else $brand_id = 0;
                        
			//if($i!=1) $data_array_barcode.=",";
			if(str_replace("'","",$$noOfBag)!="")
			{
				for($sl=1;$sl<=str_replace("'","",$$noOfBag);$sl++)
				{
					if($data_array_barcode!="") $data_array_barcode.=",";
					$barcode_suffix_no=$barcode_suffix_no+1;
					$barcode_no=$barcode_year."".str_pad($barcode_suffix_no,7,"0",STR_PAD_LEFT);
					$data_array_barcode.="(".$idBarcode.",".$idDtls.",".$id.",'".str_replace("'","",$$piDtlsId)."',".$barcode_year.",".$barcode_suffix_no.",".$barcode_no.",".$hidden_pi_id.",".$cbo_receive_basis.",'".str_replace("'","",$$colorId)."','".$brand_id."','".str_replace("'","",$$countName)."','".str_replace("'","",$$yarnComposition)."','".str_replace("'","",$$yarnCompositionPercentage)."','".str_replace("'","",$$yarnType)."','".str_replace("'","",$$noOfBag)."','".str_replace("'","",$$lotName)."','".str_replace("'","",$$conWgt)."','".str_replace("'","",$$bagCon)."','".str_replace("'","",$$bagWgt)."','".str_replace("'","",$$rateUnit)."',".$user_id.",'".$pc_date_time."',1,0)";
					$idBarcode=$idBarcode+1;
				}
			}
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$idDtls.",".$id.",".$hidden_pi_id.",'".str_replace("'","",$$piDtlsId)."',".$cbo_receive_basis.",'".str_replace("'","",$$colorId)."','".$brand_id."','".str_replace("'","",$$countName)."','".str_replace("'","",$$yarnComposition)."','".str_replace("'","",$$yarnCompositionPercentage)."','".str_replace("'","",$$yarnType)."','".str_replace("'","",$$noOfBag)."','".str_replace("'","",$$lotName)."','".str_replace("'","",$$conWgt)."','".str_replace("'","",$$bagCon)."','".str_replace("'","",$$bagWgt)."','".str_replace("'","",$$rateUnit)."',".$user_id.",'".$pc_date_time."',1,0)"; 
			$idDtls=$idDtls+1;
			
		}
		
		$data_array="(".$id.",".$hidden_pi_id.",".$hidden_company_id.",".$cbo_receive_basis.",'".$pi_number."','".$txt_date."',".$user_id.",'".$pc_date_time."',1,0)";
		
		//echo "5**insert into com_yarn_bag_sticker_dtls (".$field_array_barcode.") values ".$data_array_barcode;die;	
		
		//$hidden_pi_id $cbo_receive_basis
		$receive_basis_cond="";
		if(str_replace("'","",$cbo_receive_basis)==1)
		{
			$receive_basis_cond=" and receive_basis=1";
		}
		else if(str_replace("'","",$cbo_receive_basis)==2)
		{
			$receive_basis_cond=" and receive_basis=2 and receive_purpose<>2";
		}
		else
		{
			$receive_basis_cond=" and receive_basis=2 and receive_purpose=2";
		}
		$wo_pi_receive=return_field_value("booking_id","inv_receive_master"," status_active=1 and entry_form=1 and booking_id=$hidden_pi_id $receive_basis_cond","booking_id");
		
		if($wo_pi_receive!="")
		{
			echo "11**This PI/ WO Already Been Used In Normal Yarn Receive Page. Ones MRR Done In One Page, Not Allowed Other Receive Page.";
			disconnect($con);
			die;
		}
		
		$rID=sql_insert("com_yarn_bag_sticker",$field_array,$data_array,1);
		$rID2=true;
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("com_yarn_bag_sticker_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		$rID3=true;
		if($data_array_barcode!="")
		{
			$rID3=sql_insert("com_yarn_bag_sticker_barcode",$field_array_barcode,$data_array_barcode,0);
		}
		//oci_rollback($con); 
		//echo "10**".$rID.$rID2.$rID3;
		//echo "5**insert into com_pi_item_details (".$field_array.") values ".$data_array;die;	
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "0**".$id;
			}
			else
			{
				oci_rollback($con); 
				echo "5**0";
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
		if($db_type==2) $txt_date=change_date_format($txt_date,"","",1);
		else $txt_date=change_date_format($txt_date,"yyyy-mm-dd", "-");
		
		//$hidden_pi_id $cbo_receive_basis
		$receive_basis_cond="";
		if(str_replace("'","",$cbo_receive_basis)==1)
		{
			$receive_basis_cond=" and receive_basis=1";
		}
		else if(str_replace("'","",$cbo_receive_basis)==2)
		{
			$receive_basis_cond=" and receive_basis=2 and receive_purpose<>2";
		}
		else
		{
			$receive_basis_cond=" and receive_basis=2 and receive_purpose=2";
		}
		$wo_pi_receive=return_field_value("booking_id","inv_receive_master"," status_active=1 and entry_form=1 and booking_id=$hidden_pi_id $receive_basis_cond","booking_id");
		
		if($wo_pi_receive!="")
		{
			//echo "11**Yarn Receive Found, Sticker Not Allow.";
			echo "11**Update should be allow before Yarn Bag Rcv.";
			disconnect($con);
			die;
		}
		
		$rID=execute_query("delete from com_yarn_bag_sticker_barcode where mst_id=$update_id");
		$field_array_dtls=" id, mst_id, wo_pi_id,pi_dtls_id,receive_basis_id, color_id,brand, count_name,composition,composition_percentase, yarn_type, no_of_bag,lot_no, weight_per_con, con_per_bag,bag_weight,rate_perunit, inserted_by, insert_date,status_active,is_deleted";
		
		$field_array_barcode="id,dtls_id,mst_id,pi_dtls_id,barcode_year, barcode_suffix_no, barcode_no, wo_pi_id, receive_basis_id,color_id,brand, count_name, composition,composition_percentase,yarn_type, no_of_bag, lot_no,weight_per_con, con_per_bag, bag_weight,rate_perunit,inserted_by, insert_date, status_active, is_deleted";
		
		$field_array_dtls_update="lot_no*brand*no_of_bag*weight_per_con*con_per_bag*bag_weight*rate_perunit*updated_by*update_date";
		$field_array_details_remove="updated_by*update_date*status_active*is_deleted";
		$idDtls=return_next_id( "id","com_yarn_bag_sticker_dtls", 1 ) ;
		
		$idBarcode=return_next_id( "id","com_yarn_bag_sticker_barcode", 1 ) ;
		$barcode_year=date("y"); 
		$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","com_yarn_bag_sticker_barcode","barcode_year=$barcode_year","suffix_no");

		for($i=1;$i<=$total_row;$i++)
		{
			$lotName="lotName_".$i;
			$countName="countName_".$i;
			$yarnComposition="yarnCompositionItem_".$i;
            $yarnCompositionPercentage="yarnCompositionPercentage_".$i;
			$yarnType="yarnType_".$i; 
			$colorId="colorId_".$i;
            $brand="brand_".$i;
			$gsm="gsm_".$i;
			$noOfBag="noOfBag_".$i;
			$conWgt="conWgt_".$i;
			$bagCon="bagCon_".$i;
			$bagWgt="bagWgt_".$i;
			$rateUnit="rateUnit_".$i;
			$piDtlsId="piDtlsId_".$i;
			$piDtlsId="piDtlsId_".$i;
			$checkedValue="is_checked_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			
			//---------------Check Brand---------------------------//
			/*if (str_replace("'", "", $$brand) != "") {
				$$brand = return_id(str_replace("'", "", $$brand), $brand_library, "lib_brand", "id,brand_name");
			}*/
			$brand_id = 0;
			if (str_replace("'", "", trim($$brand)) != "") {
				if (!in_array(str_replace("'", "", trim($$brand)),$new_array_brand)){
					$brand_id = return_id( str_replace("'", "", trim($$brand)), $brand_library, "lib_brand", "id,brand_name","406");
					$new_array_brand[$brand_id]=str_replace("'", "", trim($$brand));
				}
				else $brand_id =  array_search(str_replace("'", "", trim($$brand)), $new_array_brand);
			} else $brand_id = 0;
			//---------------Check Brand End---------------------------//
                        
			if(str_replace("'","",$$checkedValue)==1)
			{
				if(str_replace("'","",$$updateIdDtls)!="")
				{
					$update_details_id_arr[]=str_replace("'","",$$updateIdDtls);
					$update_details_arr[str_replace("'","",$$updateIdDtls)]=explode("*",("'".str_replace("'","",$$lotName)."'*'".$brand_id."'*'".str_replace("'","",$$noOfBag)."'*'".str_replace("'","",$$conWgt)."'*'".str_replace("'","",$$bagCon)."'*'".str_replace("'","",$$bagWgt)."'*'".str_replace("'","",$$rateUnit)."'*".$user_id."*'".$pc_date_time."'"));
					$dtls_id_for_update=str_replace("'","",$$updateIdDtls);
				}
				else
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$idDtls.",".$update_id.",".$hidden_pi_id.",'".str_replace("'","",$$piDtlsId)."',".$cbo_receive_basis.",'".str_replace("'","",$$colorId)."','".$brand_id."','".str_replace("'","",$$countName)."','".str_replace("'","",$$yarnComposition)."','".str_replace("'","",$$yarnCompositionPercentage)."','".str_replace("'","",$$yarnType)."','".str_replace("'","",$$noOfBag)."','".str_replace("'","",$$lotName)."','".str_replace("'","",$$conWgt)."','".str_replace("'","",$$bagCon)."','".str_replace("'","",$$bagWgt)."','".str_replace("'","",$$rateUnit)."',".$user_id.",'".$pc_date_time."',1,0)"; 
					$dtls_id_for_update=$idDtls;
					$idDtls=$idDtls+1;
					
				}
					
				for($sl=1;$sl<=str_replace("'","",$$noOfBag);$sl++)
				{
					if($data_array_barcode!="") $data_array_barcode.=",";
					$barcode_suffix_no=$barcode_suffix_no+1;
					$barcode_no=$barcode_year."".str_pad($barcode_suffix_no,7,"0",STR_PAD_LEFT);
					$data_array_barcode.="(".$idBarcode.",".$dtls_id_for_update.",".$update_id.",'".str_replace("'","",$$piDtlsId)."',".$barcode_year.",".$barcode_suffix_no.",".$barcode_no.",".$hidden_pi_id.",".$cbo_receive_basis.",'".str_replace("'","",$$colorId)."','".$brand_id."','".str_replace("'","",$$countName)."','".str_replace("'","",$$yarnComposition)."','".str_replace("'","",$$yarnCompositionPercentage)."','".str_replace("'","",$$yarnType)."','".str_replace("'","",$$noOfBag)."','".str_replace("'","",$$lotName)."','".str_replace("'","",$$conWgt)."','".str_replace("'","",$$bagCon)."','".str_replace("'","",$$bagWgt)."','".str_replace("'","",$$rateUnit)."',".$user_id.",'".$pc_date_time."',1,0)";
					$idBarcode=$idBarcode+1;
				}
			}
			else
			{
				if(str_replace("'","",$$updateIdDtls)!="")
				{
					$remove_detils_id[]=str_replace("'","",$$updateIdDtls);
					$remove_detils_arr[str_replace("'","",$$updateIdDtls)]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
				}
			}
		}
		//echo $data_array_barcode;die;
		//print_r($remove_detils_arr);die;
		$field_array="delivery_date";
		$data_array="'".$txt_date."'";
		//echo "5**insert into com_yarn_bag_sticker_barcode (".$field_array_barcode.") values ".$data_array_barcode;die;	
		$rID1=sql_update("com_yarn_bag_sticker",$field_array,$data_array,"id",$update_id,0);
		
		$rID2=true;
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("com_yarn_bag_sticker_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		$rID3=true;
		if($data_array_barcode!="")
		{
			$rID3=sql_insert("com_yarn_bag_sticker_barcode",$field_array_barcode,$data_array_barcode,0);
		}
			
		$rID4=true;
		if(count($update_details_arr)>0)
		{
			$rID4=execute_query(bulk_update_sql_statement( "com_yarn_bag_sticker_dtls", "id", $field_array_dtls_update, $update_details_arr, $update_details_id_arr ));
		}
		//echo "10**";
		$rID5=true;
		if(count($remove_detils_arr)>0)
	 	{
			//echo bulk_update_sql_statement("com_yarn_bag_sticker_dtls","id",$field_array_details_remove,$remove_detils_arr,$remove_detils_id);die;
			// print_r($remove_detils_arr);die;
		    $rID5=execute_query(bulk_update_sql_statement("com_yarn_bag_sticker_dtls","id",$field_array_details_remove,$remove_detils_arr,$remove_detils_id),1);
	 	}
			
			//echo "10**".$rID1 ."&&".$rID4 ."&&". $rID2 ."&&". $rID3."&&". $rID5."&&". $rID;die;
				
			if($db_type==0)
			{
				if($rID1 && $rID4  && $rID2 && $rID3 && $rID5)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'", '', $update_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "6**".str_replace("'", '', $update_id);
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID1 && $rID4  && $rID2 && $rID3 && $rID5)
				{
					oci_commit($con);  
					echo "1**".str_replace("'", '', $update_id);
				}
				else
				{
					oci_rollback($con); 
					echo "6**".str_replace("'", '', $update_id);
				}
			}
			
			disconnect($con);
			die;
		
	}
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$sql_attach=sql_select("select a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and b.pi_id=$update_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.lc_number");
		if(count($sql_attach)>0)
		{
			$lc_number=$sql_attach[0][csf('lc_number')];
			echo "14**".$lc_number."**1"; 
			die;	
		}
		
		$sql_app=sql_select("select approved from com_pi_master_details where id=$update_id and approved=1");
		if(count($sql_app)>0)
		{
			echo "16**1**1"; 
			die;	
		}
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("com_pi_master_details",$field_array,$data_array,"id",$update_id,0);
		
		$field_array_dtls="status_active*is_deleted*updated_by*update_date";
		$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID2=sql_update("com_pi_item_details",$field_array_dtls,$data_array_dtls,"pi_id",$update_id,1);
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".str_replace("'", '', $update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);   
				echo "2**0";
			}
			else
			{
				oci_rollback($con);  
				echo "7**".str_replace("'", '', $update_id);
			}
		}
		
		disconnect($con);
		die;
	}
}







if($action=="print_barcode_one")
{	
	extract($_REQUEST);
	?>
    <style type="text/css" media="print">
       	 p{ page-break-after: always;}
    	</style>
    <?
	$brand_library = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$supplier_library=return_library_array( "select id, short_name from lib_supplier", "id", "short_name");
	$count_name_library=return_library_array( "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC", "id", "yarn_count");
	
	$barcode_data_arr=sql_select("select barcode_no,wo_pi_id, receive_basis_id, color_id,count_name,composition, yarn_type, no_of_bag, lot_no, weight_per_con,con_per_bag, bag_weight,rate_perunit, pi_dtls_id,brand from com_yarn_bag_sticker_barcode where status_active=1 and  is_deleted=0 and mst_id=$update_id");
	if($cbo_receive_basis==1)
	{
		$pi_data=sql_select("select a.supplier_id ,a.pi_number from com_pi_master_details a,com_yarn_bag_sticker  b where a.id=b.wo_pi_id and b.id=$update_id");
		$pi_number=$pi_data[0][csf('pi_number')];
		$supplier_id=$pi_data[0][csf('supplier_id')];
	}
	else if($cbo_receive_basis==2)
	{
		$pi_data=sql_select("select a.supplier_id ,a.wo_number from wo_non_order_info_mst a,com_yarn_bag_sticker  b where a.id=b.wo_pi_id and b.id=$update_id");
		$pi_number=$pi_data[0][csf('wo_number')];
		$supplier_id=$pi_data[0][csf('supplier_id')];
	}
	else
	{
		
		$pi_data=sql_select("select a.supplier_id ,a.ydw_no from wo_yarn_dyeing_mst a,com_yarn_bag_sticker  b where a.id=b.wo_pi_id and b.id=$update_id");
		$pi_number=$pi_data[0][csf('ydw_no')];
		$supplier_id=$pi_data[0][csf('supplier_id')];
	}
        if($cbo_receive_basis!=1)
        {
            $pi= "WO:";
        }else{
            $pi= "PI:";
        }
	
	$i=1;
	
	foreach($barcode_data_arr as $val)
	{
		$bundle_array[$i]=$val[csf("barcode_no")];
		echo '<table style="width:3.0in;font-size:13px" border="0" cellpadding="0" cellspacing="0">';
		echo '<tr ><td style="padding-left:0px;;font-size:16px " colspan="2"><strong>&nbsp;&nbsp;ID:&nbsp;&nbsp; '.$val[csf("barcode_no")].'</strong></td></tr>';
		echo '<tr ><td style="padding-left:0px;" colspan="2">&nbsp;&nbsp; '.$pc_date_time.'</td></tr>';
		echo '<tr ><td style="padding-left:0px;padding-bottom:5px"><div id="div_'.$i.'"></div></td></tr>';
		echo '<tr ><td style="padding-left:6px;">'.$pi.$pi_number.';Brand: '.$brand_library[$val[csf('brand')]].'</td></tr>'; //$supplier_library[$supplier_id]
		echo '<tr ><td style="padding-left:6px;">Count: '.$count_name_library[$val[csf('count_name')]].';Lot:'.$val[csf('lot_no')].';Wgt:'.$val[csf('bag_weight')].'</td></tr>';
		echo '</table><p></p>';
		$i++;
	}   
	
	
	
	?>
    
    <script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		var barcode_array =<? echo json_encode($bundle_array); ?>;
		function generateBarcode( td_no, valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
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
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#div_"+td_no).show().barcode(value, btype, settings);
		}

		for (var i in barcode_array) 
		{
			generateBarcode(i,barcode_array[i]);
		}
	</script>
    <?
	exit();
}


if( $action =='pi_update_details' ) 
{	
	
	$update_id=$data;
	$color_array=return_library_array( "select id, color_name from lib_color",'id','color_name');
        $brand_array=return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');

	?>
    <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
		
        	<thead>
            	<th style="color:blue;"  >Lot</th>
                <th style="color:blue;"  >Count</th>
                <th style="color:blue;"  >Composition</th>
                <th style="color:blue;"  >%</th>
                <th style="color:blue;"  >Yarn Type</th>
                <th style="color:blue;"  >Color</th>
                <th style="color:blue;"  >Brand</th>
                <th style="color:blue;"  >No of Bag</th>
                <th style="color:blue;"  >Wgt/Cone</th>
                <th style="color:blue;"  >Cone/Bag</th>
                <th style="color:blue;"  >Bag/Wgt</th>
                <th style="color:blue;"  >Rate/PerUnit</th>
                <th></th>
            </thead>
            <tbody>
       		<?
			if($update_id!="")
			{
				
				//echo "select id, mst_id,pi_dtls_id, wo_pi_id,receive_basis_id, color_id, count_name,composition, yarn_type, no_of_bag,lot_no, weight_per_con, con_per_bag,bag_weight,rate_perunit from com_yarn_bag_sticker_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
				
				$sql_update_data=sql_select("select id, mst_id,pi_dtls_id, wo_pi_id,receive_basis_id, color_id,brand, count_name,composition,composition_percentase, yarn_type, no_of_bag,lot_no, weight_per_con, con_per_bag,bag_weight,rate_perunit from com_yarn_bag_sticker_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
				$update_data_arr=array();
				foreach($sql_update_data as $val)
				{
					$update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['no_of_bag']=$val[csf('no_of_bag')];
					$update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['lot_no']=$val[csf('lot_no')];
					$update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['weight_per_con']=$val[csf('weight_per_con')];
					$update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['con_per_bag']=$val[csf('con_per_bag')];
					$update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['bag_weight']=$val[csf('bag_weight')];
					
					$update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['color_id']=$val[csf('color_id')];
                                        $update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['brand']=$val[csf('brand')];
					$update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['count_name']=$val[csf('count_name')];
					$update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['composition']=$val[csf('composition')];
                                        $update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['composition_percentase']=$val[csf('composition_percentase')];
					$update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['yarn_type']=$val[csf('yarn_type')];
					$update_data_arr[$val[csf('pi_dtls_id')]][$val[csf('id')]]['rate_perunit']=$val[csf('rate_perunit')];
					
					//$update_data_arr[$val[csf('pi_dtls_id')]]['id']=$val[csf('id')];
					$check_pi_id_arr[]=$val[csf('pi_dtls_id')];
					$pi_id=$val[csf('wo_pi_id')];
					$receive_basis=$val[csf('receive_basis_id')];
				}
			}
		//print_r($update_data_arr);
		if($receive_basis==1)
		{
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type, uom, quantity, rate,net_pi_rate, amount 
			from com_pi_item_details 
			where pi_id='$pi_id' and status_active=1 and is_deleted=0" );

				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
				$i=1;
				foreach($nameArray as $row)
				{
					if(in_array($row[csf('id')],$check_pi_id_arr))
					{
						foreach($update_data_arr[$row[csf('id')]] as $dtls_id=>$d_val)
						{
							//print_r($d_val);
						?>
						<tr class="general" id="row_<?php echo $i; ?>">
							<td>
								<input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" <?php echo  $check_cond; ?> checked/>
								<input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:70px"  value="<?php echo $d_val['lot_no']; ?>"/>
							</td>
							<td>
							<?
								echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$d_val['count_name'],"",1); 
							?>                         
							</td>
							<td>
								<?
									echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$d_val['composition'],"control_composition(1,'comp_one')",1); 
								?>    
							</td>
                                                        <td>
                                                            <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $d_val["composition_percentase"];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                                                        </td>
							<td>
								<?
					  
									echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$d_val['yarn_type'],"",1); 
								?>    
							</td>
							<td>
								<input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$d_val['color_id']];?>" readonly/>
							</td>
                                                        <td>
								<input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="<? echo $brand_array[$d_val['brand']];?>" />
							</td>
							<td>
								 <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:80px"  class="text_boxes_numeric" value="<?php echo  $d_val['no_of_bag']; ?>"/>
							</td>
							<td>
								<input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value="<?php echo $d_val['weight_per_con']; ?>"/>
							</td>
							<td>
								<input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value="<?php echo $d_val['con_per_bag']; ?>"/>
							</td>
							<td>
								<input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:75px;"  value="<?php echo $d_val['bag_weight']; ?>" readonly/>
								<input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="<?php echo $dtls_id; ?>" readonly/>
								<input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>" value="<?php echo $d_val['color_id']; ?>" readonly/>
								<input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
							</td>
                             <td>
                                        <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $d_val['rate_perunit']; ?>" style="width:60px;" />
                                       
                            </td>
							<td>
							<input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
						 </td>
						</tr>
					<?
					$i++;
					}
				}
				else
				{
					?>
                    <tr class="general" id="row_<?php echo $i; ?>">
                        <td>
                            <input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" />
                            <input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:80px"  value=""/>
                        </td>
                        
                        <td>
                        <?
                            echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$row[csf('count_name')],"",1); 
                        ?>                         
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$row[csf('yarn_composition_item1')],"control_composition(1,'comp_one')",1); 
                            ?>    
                        </td>
                        <td>
                            <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $row[csf('yarn_composition_percentage1')];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                        </td>
                        <td>
                            <?
                  
                                echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",1); 
                            ?>    
                        </td>
                        <td>
                            <input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$row[csf('color_id')]];?>" readonly/>
                        </td>
                        <td>
                            <input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="" />
                        </td>
                        <td>
                             <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:80px"  class="text_boxes_numeric" value=""/>
                        </td>
                        <td>
                            <input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value=""/>
                        </td>
                        <td>
                            <input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value=""/>
                        </td>
                        <td>
                            <input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:75px;"  value="" readonly/>
                            <input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="" readonly/>
                            <input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>"  value="<?php echo $row[csf('color_id')]; ?>" readonly/>
                            <input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
                        </td>
                        <td>
                           <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $row[csf('rate_perunit')]; ?>" style="width:60px;" />
                          </td>
                        <td>
                        <input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
                     </td>
                    </tr>
                <?
				$i++;	
				}
				
			}
		}
		else if($receive_basis==2)
		{
		
			$nameArray=sql_select( "select b.id, a.wo_basis_id, b.requisition_dtls_id, b.requisition_no, b.job_id, b.job_no, b.buyer_id, b.style_no, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name, b.req_quantity, b.supplier_order_quantity, b.uom, b.rate, b.amount, b.yarn_inhouse_date, b.remarks 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b 
			where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$pi_id' and a.id=b.mst_id" );

				$i=1;
				foreach($nameArray as $row)
				{
					if(in_array($row[csf('id')],$check_pi_id_arr))
					{
						foreach($update_data_arr[$row[csf('id')]] as $dtls_id=>$d_val)
						{
							?>
                            <tr class="general" id="row_<?php echo $i; ?>">
                                <td>
                                    <input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" <?php echo  $check_cond; ?> checked/>
                                    <input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:70px"  value="<?php echo $d_val['lot_no']; ?>"/>
                                </td>
                                <td>
                                <?
                                    echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$d_val['count_name'],"",1); 
                                ?>                         
                                </td>
                                <td>
                                    <?
                                        echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$d_val['composition'],"control_composition(1,'comp_one')",1); 
                                    ?>    
                                </td>
                                <td>
                                    <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $d_val["composition_percentase"];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                                </td>
                                <td>
                                    <?
                          
                                        echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$d_val['yarn_type'],"",1); 
                                    ?>    
                                </td>
                                <td>
                                    <input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$d_val['color_id']];?>" readonly/>
                                </td>
                                <td>
                                    <input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="<? echo $brand_array[$d_val['brand']];?>" />
                                </td>
                                <td>
                                     <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:60px"  class="text_boxes_numeric" value="<?php echo  $d_val['no_of_bag']; ?>"/>
                                </td>
                                <td>
                                    <input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value="<?php echo $d_val['weight_per_con']; ?>"/>
                                </td>
                                <td>
                                    <input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value="<?php echo $d_val['con_per_bag']; ?>"/>
                                </td>
                                <td>
                                    <input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:75px;"  value="<?php echo $d_val['bag_weight']; ?>" readonly/>
                                    <input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="<?php echo $dtls_id; ?>" readonly/>
                                    <input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>" value="<?php echo $d_val['color_id']; ?>" readonly/>
                                    <input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
                                </td>
                                <td>
                               <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $d_val['rate_perunit']; ?>" style="width:60px;" />
                                           
                              </td>
                                <td>
                                <input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
                                
                             </td>
                            </tr>
                        <?
                        $i++;
							
						}
					}
					else
					{
					
					?>
					<tr class="general" id="row_<?php echo $i; ?>">
						<td>
							<input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" <?php echo  $check_cond; ?> />
							<input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:70px"  value="<?php echo $update_data_arr[$row[csf('id')]]['lot_no']; ?>"/>
						</td>
						
						<td>
						<?
							echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$row[csf('yarn_count')],"",1); 
						?>                         
						</td>
						<td>
							<?
								echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$row[csf('yarn_comp_type1st')],"control_composition(1,'comp_one')",1); 
							?>    
						</td>
                                                <td>
                                                    <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $row[csf('yarn_comp_percent1st')]?>" style="width:40px"  class="text_boxes" readonly disabled/>
                                                </td>
						<td>
							<?
				  
								echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",1); 
							?>    
						</td>
						<td>
							<input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$row[csf('color_name')]];?>" readonly/>
						</td>
                                                <td>
							<input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="" />
						</td>
						<td>
							 <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:60px"  class="text_boxes_numeric" value="<?php echo  $update_data_arr[$row[csf('id')]]['no_of_bag']; ?>"/>
						</td>
						<td>
							<input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value="<?php echo $update_data_arr[$row[csf('id')]]['weight_per_con']; ?>"/>
						</td>
						<td>
							<input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value="<?php echo $update_data_arr[$row[csf('id')]]['con_per_bag']; ?>"/>
						</td>
						<td>
							<input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:75px;"  value="<?php echo $update_data_arr[$row[csf('id')]]['bag_weight']; ?>" readonly/>
							<input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['id']; ?>" readonly/>
							<input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>" value="<?php echo $row[csf('color_name')]; ?>" readonly/>
							<input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
						</td>
                        <td>
                        <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $row[csf('rate')]; ?>" style="width:60px;" />
                        </td>
                        <td>
                        <input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
                        
                        </td>
					</tr>
					<?
					$i++;
				}
			}
		}
		else if($receive_basis==3)
		{
		
			
				$sql = "select a.id, a.mst_id,  a.count, a.yarn_description, a.yarn_color, a.color_range,  a.yarn_wo_qty, a.dyeing_charge, a.amount, a.yarn_comp_type1st ,yarn_comp_percent1st,a.yarn_type
				from wo_yarn_dyeing_dtls a
				where  a.mst_id='$pi_id' and a.status_active=1 and a.is_deleted=0";
		//echo $sql;
				$i=1;
				$nameArray=sql_select($sql);
				foreach($nameArray as $row)
				{
					if(in_array($row[csf('id')],$check_pi_id_arr))
					{
						foreach($update_data_arr[$row[csf('id')]] as $dtls_id=>$d_val)
						{
							?>
						<tr class="general" id="row_<?php echo $i; ?>">
							<td>
								<input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" <?php echo  $check_cond; ?> checked/>
								<input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:70px"  value="<?php echo $d_val['lot_no']; ?>"/>
							</td>
							<td>
							<?
								echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$d_val['count_name'],"",1); 
							?>                         
							</td>
							<td>
								<?
									echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$d_val['composition'],"control_composition(1,'comp_one')",1); 
								?>    
							</td>
                                                        <td>
                                                            <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $d_val["composition_percentase"];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                                                        </td>
							<td>
								<?
					  
									echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$d_val['yarn_type'],"",1); 
								?>    
							</td>
							<td>
								<input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$d_val['color_id']];?>" readonly/>
							</td>
                                                        <td>
								<input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="<? echo $brand_array[$d_val['brand']];?>" />
							</td>
							<td>
								 <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:60px"  class="text_boxes_numeric" value="<?php echo  $d_val['no_of_bag']; ?>"/>
							</td>
							<td>
								<input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value="<?php echo $d_val['weight_per_con']; ?>"/>
							</td>
							<td>
								<input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value="<?php echo $d_val['con_per_bag']; ?>"/>
							</td>
							<td>
								<input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:75px;"  value="<?php echo $d_val['bag_weight']; ?>" readonly/>
								<input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="<?php echo $dtls_id; ?>" readonly/>
								<input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>" value="<?php echo $d_val['color_id']; ?>" readonly/>
								<input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
							</td>
                            <td>
                           <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $d_val['rate_perunit']; ?>" style="width:60px;" />
                                       
                          </td>
							<td>
							<input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
							
						 </td>
						</tr>
					<?
					$i++;
							
						}
					}
					else
					{
					
					?>
					<tr class="general" id="row_<?php echo $i; ?>">
						<td>
							<input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" <?php echo  $check_cond; ?> />
							<input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:70px"  value="<?php echo $update_data_arr[$row[csf('id')]]['lot_no']; ?>"/>
						</td>
						
						<td>
						<?
							echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$row[csf('count')],"",1); 
						?>                         
						</td>
						<td>
							<?
								echo create_drop_down( "yarnCompositionItem_".$i,180, $composition,'', 1, '-Select-',$row[csf('yarn_comp_type1st')],"control_composition(1,'comp_one')",1); 
							?>    
						</td>
                                                <td>
                                                    <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $row[csf("yarn_comp_percent1st")];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                                                </td>
						<td>
							<?
				  
								echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",1); 
							?>    
						</td>
						<td>
							<input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$row[csf('yarn_color')]];?>" readonly/>
						</td>
                                                <td>
							<input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="" />
						</td>
						<td>
							 <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:60px"  class="text_boxes_numeric" value="<?php //echo  $update_data_arr[$row[csf('id')]]['no_of_bag']; ?>"/>
						</td>
						<td>
							<input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value="<?php //echo $update_data_arr[$row[csf('id')]]['weight_per_con']; ?>"/>
						</td>
						<td>
							<input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value="<?php //echo $update_data_arr[$row[csf('id')]]['con_per_bag']; ?>"/>
						</td>
						<td>
							<input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:75px;"  value="<?php // echo $update_data_arr[$row[csf('id')]]['bag_weight']; ?>" readonly/>
							<input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['id']; ?>" readonly/>
							<input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>" value="<?php echo $row[csf('yarn_color')]; ?>" readonly/>
							<input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
						</td>
                        <td>
                           <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $row[csf('dyeing_charge')]; ?>" style="width:60px;" />
                                       
                        </td>
                        <td>
							<input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
						</td>
					</tr>
					<?
					$i++;
				}
			}
		}
		else if($receive_basis==4)
		{
		
			
			$sql = "select a.id, a.mst_id,  a.count, a.yarn_description, a.yarn_color, a.color_range,  a.yarn_wo_qty, a.dyeing_charge, a.amount, a.yarn_comp_type1st ,a.yarn_type
			from wo_yarn_dyeing_dtls a
			where  a.mst_id='$pi_id' and a.status_active=1 and a.is_deleted=0";
	//echo $sql;
			$i=1;
			$nameArray=sql_select($sql);
			foreach($nameArray as $row)
			{
				if(in_array($row[csf('id')],$check_pi_id_arr))
				{
					foreach($update_data_arr[$row[csf('id')]] as $dtls_id=>$d_val)
					{
						?>
					<tr class="general" id="row_<?php echo $i; ?>">
						<td>
							<input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" <?php echo  $check_cond; ?> checked/>
							<input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:70px"  value="<?php echo $d_val['lot_no']; ?>"/>
						</td>
						<td>
						<?
							echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$d_val['count_name'],"",1); 
						?>                         
						</td>
						<td>
							<?
								echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$d_val['composition'],"control_composition(1,'comp_one')",1); 
							?>    
						</td>
                                                <td>
                                                    <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="<? echo $d_val["composition_percentase"];?>" style="width:40px"  class="text_boxes" readonly disabled/>
                                                </td>
						<td>
							<?
				  
								echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$d_val['yarn_type'],"",1); 
							?>    
						</td>
						<td>
							<input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$d_val['color_id']];?>" readonly/>
						</td>
                                                <td>
							<input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="<? echo $brand_array[$d_val['brand']];?>" />
						</td>
						<td>
							 <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:60px"  class="text_boxes_numeric" value="<?php echo  $d_val['no_of_bag']; ?>"/>
						</td>
						<td>
							<input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value="<?php echo $d_val['weight_per_con']; ?>"/>
						</td>
						<td>
							<input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value="<?php echo $d_val['con_per_bag']; ?>"/>
						</td>
						<td>
							<input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:75px;"  value="<?php echo $d_val['bag_weight']; ?>" readonly/>
							<input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="<?php echo $dtls_id; ?>" readonly/>
							<input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>" value="<?php echo $d_val['color_id']; ?>" readonly/>
							<input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
						</td>
						<td>
					   <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $d_val['rate_perunit']; ?>" style="width:60px;" />
								   
					  </td>
						<td>
						<input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
					 </td>
					</tr>
					<?
					$i++;
						
					}
				}
				else
				{
				
					?>
					<tr class="general" id="row_<?php echo $i; ?>">
						<td>
							<input type="checkbox" id="check_<?php echo $i; ?>" name="check_<?php echo $i; ?>" <?php echo  $check_cond; ?> />
							<input type="text" name="lotName_<?php echo $i; ?>" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:70px"  value="<?php echo $update_data_arr[$row[csf('id')]]['lot_no']; ?>"/>
						</td>
						
						<td>
						<?
							echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$row[csf('count')],"",1); 
						?>                         
						</td>
						<td>
							<?
								echo create_drop_down( "yarnCompositionItem_".$i,180, $composition,'', 1, '-Select-',$row[csf('yarn_comp_type1st')],"control_composition(1,'comp_one')",1); 
							?>    
						</td>
					 
						<td>
							<?
				  
								echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",1); 
							?>    
						</td>
						<td>
							<input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$row[csf('yarn_color')]];?>" readonly/>
						</td>
                                                <td>
							<input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value="" />
						</td>
						<td>
							 <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:60px"  class="text_boxes_numeric" value="<?php //echo  $update_data_arr[$row[csf('id')]]['no_of_bag']; ?>"/>
						</td>
						<td>
							<input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(<?php echo $i; ?>)"  value="<?php //echo $update_data_arr[$row[csf('id')]]['weight_per_con']; ?>"/>
						</td>
						<td>
							<input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric"  style="width:45px;" onKeyUp="calculate_amount(<?php echo $i; ?>)" value="<?php //echo $update_data_arr[$row[csf('id')]]['con_per_bag']; ?>"/>
						</td>
						<td>
							<input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric"   style="width:75px;"  value="<?php // echo $update_data_arr[$row[csf('id')]]['bag_weight']; ?>" readonly/>
							<input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['id']; ?>" readonly/>
							<input type="hidden" name="colorId_<?php echo $i; ?>" id="colorId_<?php echo $i; ?>" value="<?php echo $row[csf('yarn_color')]; ?>" readonly/>
							<input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
						</td>
						<td>
						   <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="<?php echo $row[csf('dyeing_charge')]; ?>" style="width:60px;" />
									   
						 </td>
						 <td>
							<input type="button" id="increase_<?php echo $i; ?>" name="increase[]" class="formbutton" value="+"  style=" width:30px" onClick="add_break_down_tr(<?php echo $i; ?>)"/>
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
   
    <?
}

if($action=='populate_data_from_master')
{
	$sql_update_data=sql_select("select id, delivery_date from com_yarn_bag_sticker where id=$data");
	foreach ($sql_update_data as $row)
	{ 
		echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_date').value 				= '".change_date_format($row[csf("delivery_date")])."';\n";
	}
}










